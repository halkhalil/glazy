<?php

namespace App\Api\V1\Repositories\Filesystem;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
//use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use ColorThief\ColorThief;

use Intervention\Image\ImageManagerStatic as Image;

class GlazyImageFile
{

    const LARGE_IMAGE_PREFIX = 'l_';
    const MEDIUM_IMAGE_PREFIX = 'm_';
    const SMALL_IMAGE_PREFIX = 's_';

/*
 * Legacy sizes
    const IMAGE_SIZE_LARGE = 1200;
    const IMAGE_SIZE_MEDIUM = 600;
    const IMAGE_SIZE_SMALL = 300;
*/

    const IMAGE_SIZE_LARGE = 2500;
    const IMAGE_SIZE_MEDIUM = 800;
    const IMAGE_SIZE_SMALL = 300;

    const IMAGE_URL_PATH = '/storage/uploads';

    const TMP_STORAGE_NAME = 'tmp';
    const MATERIALS_STORAGE_NAME = 'materials';
    const COLLECTIONS_STORAGE_NAME = 'collections';
    const PROFILES_STORAGE_NAME = 'profiles';

    protected $storageDiskName; // e.g. 'materials', 'collections'
    protected $primaryId;
    protected $bin_directory;   // calculated based on primaryId
    protected $file;            // UploadedFile
    protected $file_extension;  // Either jpg, png, gif
    protected $filename;
    protected $image;           // Intervention Image
    protected $diskStoragePath;
    protected $actual_medium_height;
    protected $actual_medium_width;

    public $dominant_rgb_r = null;
    public $dominant_rgb_g = null;
    public $dominant_rgb_b = null;

    public $secondary_rgb_r = null;
    public $secondary_rgb_g = null;
    public $secondary_rgb_b = null;

    public function __construct($storageDiskName, $primaryId, UploadedFile $file)
    {
        $this->storageDiskName = $storageDiskName;
        $this->primaryId = $primaryId;
        $this->file = $file;
        $this->file_extension = $this->getFileExtension();

        if (!$this->file_extension) {
            throw new Exception('Incorrect File Type');
        }

        $this->setBin();

        $this->filename = $this->getUniqueGlazyImageFilename();

        $this->diskStoragePath = Storage::disk($this->storageDiskName)->getDriver()->getAdapter()->getPathPrefix();
    }

    public function store()
    {
        // store uploaded image file to tmp
        Storage::disk(self::TMP_STORAGE_NAME)->put($this->filename,  File::get($this->file));

        $tmpStoragePath = Storage::disk(self::TMP_STORAGE_NAME)->getDriver()->getAdapter()->getPathPrefix();

        // Force use of imagick
        $manager = new ImageManager(array('driver' => 'imagick'));

        // Load Intervention image
        $this->image = $manager->make($tmpStoragePath.$this->filename);

        // From large to small, create resized image files
        $this->createLargeImage();
        $this->createMediumImage();
        $this->createSmallImage();

        $this->image->destroy();    // Free up memory

        $this->setDominantColors();

        // Delete tmp image
        Storage::disk(self::TMP_STORAGE_NAME)->delete($this->filename);
    }

    protected function getFileExtension()
    {
        $file_extension = null;
        switch ($this->file->getClientMimeType()) {
            case 'image/jpeg':
                $file_extension = 'jpg';
                break;
            case 'image/png':
                $file_extension = 'png';
                break;
            case 'image/gif':
                $file_extension = 'gif';
                break;

        }
        return $file_extension;
    }

    protected function getUniqueGlazyImageFilename()
    {
        return $this->primaryId . '.' . uniqid() . '.' . $this->file_extension;
    }

    protected function setBin()
    {
        //  Save image files to bins.
        $this->bin_directory = self::getBin($this->primaryId, $this->storageDiskName);

        // Create bin directory
        if (!Storage::disk($this->storageDiskName)->exists($this->bin_directory))
        {
            Storage::disk($this->storageDiskName)->makeDirectory($this->bin_directory, 755);
        }

    }

    public static function getBin($primaryId) {
        //  A bin is a directory with the same digits as the first one or two digits of the recipe id.
        return substr($primaryId, -2, 2);
    }

    public function getFullPathImageFilename($image_size_prefix) {
        return $this->diskStoragePath.$this->bin_directory.'/'.$image_size_prefix.$this->filename;
    }

    /**
     * widen: Resizes the current image to new width, constraining aspect ratio.
     * Pass an optional Closure callback as third parameter, to apply
     * additional constraints like preventing possible upsizing.
     */
    protected function createLargeImage()
    {
        $filename = $this->getFullPathImageFilename(self::LARGE_IMAGE_PREFIX);

        $this->image->widen(self::IMAGE_SIZE_LARGE, function ($constraint) {
            $constraint->upsize();
        });

        $this->image->orientate();  // Correct for orientation
        $this->image->save($filename);
    }

    /**
     * fit: Combine cropping and resizing to format image in a smart way.
     * The method will find the best fitting aspect ratio of your
     * given width and height on the current image automatically,
     * cut it out and resize it to the given dimension.
     * You may pass an optional Closure callback as third parameter,
     * to prevent possible upsizing and a custom position of the
     * cutout as fourth parameter.
     */
    protected function createMediumImage()
    {
        // Medium: Square
        $filename = $this->getFullPathImageFilename(self::MEDIUM_IMAGE_PREFIX);
        $this->image->fit(self::IMAGE_SIZE_MEDIUM, self::IMAGE_SIZE_MEDIUM);
        $this->image->save($filename);
        // Store dimensions for later use by color theif
        $this->actual_medium_height = $this->image->height();
        $this->actual_medium_width = $this->image->width();
    }

    protected function createSmallImage()
    {
        // Small:  Square
        $filename = $this->getFullPathImageFilename(self::SMALL_IMAGE_PREFIX);
        $this->image->fit(self::IMAGE_SIZE_SMALL, self::IMAGE_SIZE_SMALL);
        $this->image->save($filename);
    }

    protected function setDominantColors()
    {
        $filename = $this->getFullPathImageFilename(self::MEDIUM_IMAGE_PREFIX);

        $area = null;
        if ($this->actual_medium_width >= 550 && $this->actual_medium_height >= 550) {
            // Only search area in center of image
            $area = array('x' => 150, 'y' => 150, 'w' => 300, 'h' => 300);
        }
        $palette = ColorThief::getPalette($filename, 2, 1, $area);

        if (!empty($palette)) {
            if (isset($palette[0])) {
                $this->dominant_rgb_r = $palette[0][0];
                $this->dominant_rgb_g = $palette[0][1];
                $this->dominant_rgb_b = $palette[0][2];
                if (isset($palette[1])) {
                    // two colors
                    $this->secondary_rgb_r = $palette[1][0];
                    $this->secondary_rgb_g = $palette[1][1];
                    $this->secondary_rgb_b = $palette[1][2];
                }
            }
        }
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public static function deleteFiles($storageDiskName, $primaryId, $filename)
    {
        $diskStoragePath = Storage::disk($storageDiskName)->getDriver()->getAdapter()->getPathPrefix();
        $bin_directory = self::getBin($primaryId);
//        $fullPath = $diskStoragePath.$bin_directory;
//        $fullPath = $bin_directory;

        Storage::disk($storageDiskName)->delete($bin_directory.'/'.self::LARGE_IMAGE_PREFIX.$filename);
        Storage::disk($storageDiskName)->delete($bin_directory.'/'.self::MEDIUM_IMAGE_PREFIX.$filename);
        Storage::disk($storageDiskName)->delete($bin_directory.'/'.self::SMALL_IMAGE_PREFIX.$filename);
    }


}
