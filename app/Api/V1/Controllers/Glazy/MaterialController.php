<?php

namespace App\Api\V1\Controllers\Glazy;

//use App\Api\V1\Controllers\ApiController;

use App\Api\V1\Transformers\Material\MaterialTransformer;

use App\Api\V1\Repositories\MaterialRepository;

use App\Models\Material;

use App\Api\V1\Requests\Recipe\CreateRecipeRequest;
use App\Api\V1\Requests\Recipe\UpdateRecipeRequest;

use App\Models\MaterialImage;

use App\Models\MaterialType;
use App\Models\OrtonCone;
use DateTime;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Manager as FractalManager;

use App\Api\V1\Serializers\GlazySerializer;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Auth;

/**
 * Class FrontendController
 * @package App\Http\Controllers
 */
class MaterialController extends ApiBaseController
{

    /**
     * @var MaterialRepository
     */
	protected $materialRepository;

    public function __construct(
        MaterialRepository $materialRepository,
        FractalManager $manager,
        GlazySerializer $serializer)
    {
        parent::__construct($manager, $serializer);
        $this->materialRepository = $materialRepository;

//        $this->middleware('auth.basic', ['only' => 'store']);
    }

    public function show($id)
    {
        $material = $this->materialRepository->getWithDetails($id);

        if (!$material)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        if ($material->is_private) {
            if (!Auth::guard()->user()) {
                return $this->respondUnauthorized('Recipe is private. Please login.');
            } else if (!Auth::guard()->user()->can('view', $material)) {
                return $this->respondUnauthorized('Recipe is private.');
            }
        }

        $resource = new FractalItem($material, new MaterialTransformer());

        return $this->manager->createData($resource)->toArray();
    }

    public function thumbnail($materialId, $imageId)
    {
        // TODO: just get material from image?
        $material = Material::find($materialId);
        $image = MaterialImage::find($imageId);

        if (!$material || !$image)
        {
            return $this->respondNotFound('Material or Image does not exist');
        }

        if (!Auth::guard()->user()->can('update', $material)) {
            return $this->respondUnauthorized('This recipe cannot be edited by you.');
        }

        $material->thumbnail_id = $image->id;
        $material->save();

        return $this->respondUpdated('Thumbnail set');
    }

    public function store(CreateRecipeRequest $request)
    {
        if (!Auth::guard()->user()) {
            return $this->respondUnauthorized('You must login to create materials.');
        }

        $data = $request->all();
        $material = new Material();
        $material->created_by_user_id = Auth::guard()->user()->id;
        // TODO: Properly use store & update methods
        $material = $this->materialRepository->createOrUpdate($material, $data);

        if (!$material) {
            // Error updating the material
            return $this->respondInternalError('Internal Error creating the material');
        }

        $resource = new FractalItem($material, new MaterialTransformer());

        return $this->manager->createData($resource)->toArray();
        //        return $this->respondCreated('Recipe successfully created');
    }

    public function update($id, UpdateRecipeRequest $request)
    {
        if (!Auth::guard()->user()) {
            return $this->respondUnauthorized('You must login to edit recipes.');
        }

        //$data = $request->get('form', []);
        $data = $request->all();

        $material = $this->materialRepository->get($id);

        if (! $material)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        if (!Auth::guard()->user()->can('update', $material)) {
            return $this->respondUnauthorized('This recipe does not belong to you.');
        }

        $material = $this->materialRepository->update($material, $data);

        if (!$material) {
            // Error updating the material
            return $this->respondInternalError('Internal Error updating the recipe');
        }

        $resource = new FractalItem($material, new MaterialTransformer());

        return $this->manager->createData($resource)->toArray();
    }

    public function destroy($id)
    {
        $material = $this->materialRepository->get($id);

        if (! $material)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        if (!Auth::guard()->user()->can('delete', $material)) {
            return $this->respondUnauthorized('This recipe cannot be deleted by you.');
        }

        $result = $this->materialRepository->destroy($id);

        return $this->respondDeleted('Recipe deleted');
    }


    public function copy($id)
    {
        $material = $this->materialRepository->get($id);

        if (! $material)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        if ($material->is_private) {
            if (!Auth::guard()->user()) {
                return $this->respondUnauthorized('Recipe is private. Please login.');
            } else if (!Auth::guard()->user()->can('view', $material)) {
                return $this->respondUnauthorized('Recipe is private.');
            }
        }

        $copiedMaterial = $this->materialRepository->copy($material);

        $resource = new FractalItem($copiedMaterial, new MaterialTransformer());

        return $this->manager->createData($resource)->toArray();
    }


    public function publish($id)
    {
        $material = $this->materialRepository->getWithDetails($id);

        if (! $material)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        if (!Auth::guard()->user()->can('update', $material)) {
            return $this->respondUnauthorized('This recipe cannot be published by you.');
        }

        $material->is_private = false;
        $material->save();

        $resource = new FractalItem($material, new MaterialTransformer());

        return $this->manager->createData($resource)->toArray();
    }

    public function unpublish($id)
    {
        $material = $this->materialRepository->getWithDetails($id);

        if (! $material)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        if (!Auth::guard()->user()->can('update', $material)) {
            return $this->respondUnauthorized('This recipe cannot be updated by you.');
        }

        $material->is_private = true;
        $material->save();

        $resource = new FractalItem($material, new MaterialTransformer());

        return $this->manager->createData($resource)->toArray();
    }

    public function archive($id)
    {
        $material = $this->materialRepository->getWithDetails($id);

        if (! $material)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        if (!Auth::guard()->user()->can('update', $material)) {
            return $this->respondUnauthorized('This recipe cannot be locked by you.');
        }

        $material->is_archived = true;
        $material->timestamps = false; // Don't update timestamp for an archive
        $material->save();

        $resource = new FractalItem($material, new MaterialTransformer());

        return $this->manager->createData($resource)->toArray();
    }

    public function export($id, $exportType)
    {
        $material = $this->materialRepository->getWithDetails($id);

        if (!$material)
        {
            return $this->respondNotFound('Recipe does not exist');
        }

        if ($material->is_private) {
            if (!Auth::guard()->user()) {
                return $this->respondUnauthorized('Recipe is private. Please login.');
            } else if (!Auth::guard()->user()->can('view', $material)) {
                return $this->respondUnauthorized('Recipe is private.');
            }
        }

        if ($exportType === 'GlazeChem')
        {
            $content = $this->glazeChemExport($material);
            $response = new Response($content, '200');
            $response->header('Content-Type', 'plain/txt');
            return $response;
        }
        elseif ($exportType === 'Insight')
        {
            /*
            $content = View::make('recipes.export.insight', [
                'recipe' => $material,
                'recipeMaterials' => $recipeMaterials,
                'recipeImages' => $recipeImages,
                'collections' => $collections,
                'recipe_subtype_lineage' => $recipe_subtype_lineage,
                'from_orton_cone' => OrtonCone::getStaticName($recipe->from_orton_cone_id),
                'to_orton_cone' => OrtonCone::getStaticName($recipe->to_orton_cone_id),
            ]);
            $response = new Response($content, '200');
            $response->header('Cache-Control', 'public');
            $response->header('Content-Description', 'File Transfer');
            $response->header('Content-Disposition', 'attachment; filename='.$recipe->id.'.xml');
            $response->header('Content-Transfer-Encoding', 'binary');
            $response->header('Content-Type', 'text/xml');
            return $response;
            */
        }
        elseif ($exportType === 'Card')
        {
            /*
            // Create a recipe card image and return to user
            return $this->recipeCard($recipe);
            */
        }

    }


    public function glazeChemExport($material) {
        $out = "name   = ".strip_tags($material->name)."\n";
        $out .= "index  = \n";
        $oDate = new DateTime($material->created_at);
        $out .= "date   = ".$oDate->format("m/d/Y")."\n";
        $out .= "source = https://www.glazy.org\n";
        $type = '';

        $materialType = new MaterialType();

        if ($material->material_type_id)
        {
            $type = $materialType->getValue($material->material_type_id);
            if ($type && isset($type['concatenated_name'])) {
                $out .= "type   = ".$type['concatenated_name']."\n";
            }
        }

        $range = '';

        $cone = new OrtonCone();

        $from_orton_cone = $cone->getValue($material->from_orton_cone_id);
        $to_orton_cone = $cone->getValue($material->to_orton_cone_id);

        if (!empty($from_orton_cone))
        {
            if (!empty($to_orton_cone))
            {
                if ($from_orton_cone === $to_orton_cone)
                {
                    $range = $from_orton_cone;
                }
                else
                {
                    $range = $from_orton_cone.'-'.$to_orton_cone;
                }
            }
            else
            {
                $range = $from_orton_cone;
            }
        }
        elseif (!empty($to_orton_cone))
        {
            $range = $to_orton_cone;
        }
        //$range = str_replace('05&#189;', '05 1/2', $range);
        $out .= "range  = ".$range."\n";

        $firetype = '';
        if ($material->atmospheres && count($material->atmospheres))
        {
            $hasFiretype = false;
            foreach ($material->atmospheres as $atmosphere)
            {
                if ($hasFiretype)
                {
                    $firetype .= ', ';
                }
                $firetype .= $atmosphere->name;

                $hasFiretype = true;
            }
        }
        $out .= "firetype     = ".$firetype."\n";

        $out .= "color        = ".strip_tags($material->color_name)."\n";

        $out .= "vistexture   = \n";

        if (isset($material->surface_type) && isset($material->surface_type->name))
        {
            $out .= "quality      = ".$material->surface_type->name."\n";
        }
        else
        {
            $out .= "quality      = \n";
        }

        if (isset($material->transparency_type) && isset($material->transparency_type->name))
        {
            $out .= "transparency = ".$material->transparency_type->name."\n";
        }
        else
        {
            $out .= "transparency = \n";
        }
        $out .= "xtals        = \n";
        $out .= "bubbles      = \n";
        $out .= "flow         = \n";
        $out .= "durability   = \n";
        $out .= "flaws     = \n";
        $out .= "tested    = \n";
        $out .= "imagefile = \n";
        $out .= "notefile  = \n";
        $out .= "limform   = \n";
        $out .= "by_vol    = n\n";

        $materials = '';
        $total_percentage_amount = 0;

        if (isset($material->components)) {
            foreach ($material->components as $materialComponent)
            {
                if ($materialComponent->is_additional) {
                    $materials .= "addition  = ";
                }
                else {
                    $materials .= "component = ";
                }

                $materials .= $materialComponent->component_material->name."\n";

                if ($materialComponent->is_additional) {
                    $materials .= "addamount = ";
                }
                else
                {
                    $materials .= "amount    = ";
                }
                $materials .= (float)$materialComponent->percentage_amount."\n";
                $total_percentage_amount += $materialComponent->percentage_amount;
            }
            $out .= "batchsize = ".(float)$total_percentage_amount."\n";
            $out .= $materials;
        }

        $notes = '';
        $note = '';
        if (!empty($material->description))
        {
            $desc = strip_tags($material->description);
            $keywords = preg_split("/\s+/", $desc);

            foreach ($keywords as $keyword)
            {
                if (strlen($keyword) + strlen($note) < 72)
                {
                    $note .= " ".$keyword;
                }
                else
                {
                    $notes .= "note = |".$note."\n";
                    $note = " ".$keyword;
                }
            }
        }
        if (strlen($note) > 0)
        {
            $notes .= "note = |".$note."\n";
        }
        $out .= $notes;
        return $out;
    }

}
