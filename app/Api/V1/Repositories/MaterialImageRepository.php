<?php

namespace App\Api\V1\Repositories;

use App\Api\V1\Repositories\Filesystem\GlazyImageFile;
use App\Models\Material;
use Exception;
use Intervention\Image\ImageManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Api\V1\Repositories\Repository;

use App\Models\MaterialImage;

class MaterialImageRepository extends Repository
{

    /**
     * The image manager instance.
     *
     * @var ImageManager
     */
    protected $images;

    /**
     * Create a new interaction instance.
     *
     * @param  ImageManager  $images
     * @return void
     */
    public function __construct(ImageManager $images)
    {
        $this->images = $images;
    }


    public function getModel()
    {
        return new MaterialImage();
    }

    public function get($id)
    {
        return MaterialImage::find($id);
    }

    public function getWithDetails($id)
    {
        return MaterialImage::with('orton_cone')->with('atmosphere')->find($id);
    }

    public function getByUserId($user_id)
    {
        return MaterialImage::where('created_by_user_id', $user_id)->get();
    }

    /**
     * Get a list of user's images.
     *
     * If a $user_id is specified and is different from the current
     * user's id, make sure that only public images are returned.
     *
     * If no $user_id is specified and the user is logged in, return
     * images for the current user.
     *
     * If no $user_id is specified and the user is NOT logged in,
     * just get the list of public images from the Glazy admin.
     *
     * @param $user_id
     * @return mixed
     */
    public function userlist($user_id)
    {
        if (!$user_id) {
            // TODO: should probably also check that user ID exists
            return;
        }

        if (Auth::check()) {
            $current_user_id = Auth::user()->id;

            if (!$user_id || $current_user_id == $user_id) {
                // user is getting own images
                return MaterialImage::where('created_by_user_id', $user_id)
                    ->with('material')
                    ->with('material.from_orton_cone')
                    ->with('material.to_orton_cone')
                    ->with('material.atmospheres')
                    ->with('material.thumbnail')
                    ->with('material.created_by_user')
                    ->get();
            }
        }

        // User not allowed to see private recipes
        return MaterialImage::where('created_by_user_id', $user_id)
            ->whereHas('material', function ($q) {
                $q->where('is_private', false);
            })
            ->with('material.from_orton_cone')
            ->with('material.to_orton_cone')
            ->with('material.atmospheres')
            ->with('material.thumbnail')
            ->with('material.created_by_user')
            ->get();

    }

    public function all()
    {
        return MaterialImage::all();
    }

    /**
     * Create a new material image on disk and in the DB.
     *
     * Create is only used for uploading the image file and storing
     * a new record in the DB.  All other image attributes are set
     * through the update action.
     *
     * @param Model $material
     * @param array $data
     * @return MaterialImage
     */
    public function create(Model $material, array $data)
    {

        if (!Auth::guard('api')->check()) {
            // Should not be able to reach this point
            return;
        }

        $current_user_id = Auth::guard('api')->user()->id;

            // Increase time limit in case of large uploads
        set_time_limit(300);

        $materialImage = $this->getModel();

        $materialImage->fill($data);
        $materialImage->material_id = $material->id;
        $materialImage->created_by_user_id = $current_user_id;
        $materialImage->updated_by_user_id = $current_user_id;

        if (!$data['imageFile']) {
            throw new Exception('Upload file not found');
        }

        // Store resized images on filesystem
        $glazyImageFile = new GlazyImageFile(
            GlazyImageFile::MATERIALS_STORAGE_NAME,
            $material->id,
            $data['imageFile']
        );

        $glazyImageFile->store();

        $materialImage->filename = $glazyImageFile->getFilename();

        $materialImage->dominant_rgb_r = $glazyImageFile->dominant_rgb_r;
        $materialImage->dominant_rgb_g = $glazyImageFile->dominant_rgb_g;
        $materialImage->dominant_rgb_b = $glazyImageFile->dominant_rgb_b;
        $materialImage->secondary_rgb_r = $glazyImageFile->secondary_rgb_r;
        $materialImage->secondary_rgb_g = $glazyImageFile->secondary_rgb_g;
        $materialImage->secondary_rgb_b = $glazyImageFile->secondary_rgb_b;

        // Finally we can save the image
        $materialImage->save();

        // If recipe doesn't already have a thumbnail,
        // make this image the thumbnail
        if (!$material->thumbnail_id)
        {
            $material->thumbnail_id = $materialImage->id;
            $material->save();
        }

        return $materialImage;
    }

    public function update(Model $materialImage, array $data)
    {
        if (!Auth::guard('api')->check()) {
            // Should not be able to reach this point
            return;
        }

        $current_user_id = Auth::guard('api')->user()->id;

        $materialImage->fill($data);
        $materialImage->updated_by_user_id = $current_user_id;

        $materialImage->save();

        return $materialImage;
    }


    public function destroy(Model $materialImage)
    {
        $material = Material::find($materialImage->material_id);

        if (!$material) {
            throw new Exception('Material for image does not exist');
        }

        // If this image is the material's thumbnail image, unset it
        if ($material->thumbnail_id === $materialImage->id)
        {
            $material->thumbnail_id = null;

            // If this material has another image, just make it the thumbnail instead
            $materialImages = MaterialImage::where('material_id', $material->id)->where('id', '<>', $materialImage->id)->get();
            if (!$materialImages->isEmpty()) {
                $newThumbnail = $materialImages->first();
                $material->thumbnail_id = $newThumbnail->id;
            }
            $material->save();
        }

//TODO
//        $deletedRows = MaterialCollections::where('thumbnail_id', $materialImage->id)->delete();

        GlazyImageFile::deleteFiles(
            GlazyImageFile::MATERIALS_STORAGE_NAME,
            $materialImage->material_id,
            $materialImage->filename
        );

        $materialImage->delete();
    }

    /**
     * Resize an image instance for the given file.
     *
     * @param  \SplFileInfo  $file
     * @return \Intervention\Image\Image
     */
    protected function formatImage($file)
    {
        return (string) $this->images->make($file->path())
            ->fit(300)->encode();
    }

}
