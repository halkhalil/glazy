<?php

namespace App\Api\V1\Controllers\Glazy;

use App\Api\V1\Requests\Search\SimilarMaterialsMaterialRequest;
use App\Api\V1\Requests\Search\SimilarUnityFormulaRequest;

use App\Api\V1\Transformers\ChartMaterialTransformer;
use App\Api\V1\Transformers\Material\ChartPointMaterialTransformer;
use App\Api\V1\Transformers\NoComponentsMaterialTransformer;
use App\Api\V1\Transformers\Material\ShallowMaterialFromMaterialImageTransformer;
use App\Api\V1\Transformers\Material\ShallowMaterialTransformer;

use App\Api\V1\Repositories\MaterialRepository;

use App\Models\Material;

use App\Models\MaterialAnalysis;
use App\Models\MaterialImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use DerekPhilipAu\Ceramicscalc\Models\Analysis\Analysis;

use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Manager as FractalManager;

use App\Api\V1\Serializers\GlazySerializer;


/**
 * Class FrontendController
 * @package App\Http\Controllers
 */
class SearchController extends ApiBaseController
{
    const MAX_ITEMS_PER_PAGE = 50;
    const DEFAULT_ITEMS_PER_PAGE = 48;
//    const MAX_ITEMS_PER_PAGE = 1000;
//    const DEFAULT_ITEMS_PER_PAGE = 1000;

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
    }

    public function index(Request $request)
    {
        $isColorQuery = false;

        $page = (int)$request->input('p');
        if (!$page || $page <= 0) {
            // if page not specified, start w/ page 1
            $page = 1;
        }

        $search_user_id = (int)$request->input('u');
        $collection_id = (int)$request->input('collection');
        $is_primitive = (int)$request->input('is_primitive'); // 0 == false
        $keywords = $request->input('keywords');
        $base_type_id = (int)$request->input('base_type');
        $type_id = (int)$request->input('type');
        $orton_cone_id = $request->input('cone');
        $atmosphere_id = (int)$request->input('atmosphere');
        $surface_type_id = (int)$request->input('surface');
        $transparency_type_id = (int)$request->input('transparency');
        $hex_color = $request->input('hex_color');
        $r = null;
        $g = null;
        $b = null;

        $sort_order_id = $request->input('sort_order');
        $view_option = $request->input('view_option');
        $view_option_paginate = $request->input('pag');

        $count = $request->input('count');

        $query = Material::query();

        $query->select(
            'materials.id', 'materials.name',
            'materials.is_primitive', 'materials.material_type_id',
            'materials.is_analysis', 'materials.is_theoretical',
            'materials.from_orton_cone_id', 'materials.to_orton_cone_id',
            'materials.surface_type_id', 'materials.transparency_type_id',
            'materials.rating_total', 'materials.rating_number',
            'materials.rgb_r', 'materials.rgb_g', 'materials.rgb_b', 'materials.thumbnail_id',
            'materials.is_private', 'materials.created_by_user_id', 'materials.updated_by_user_id', 'materials.created_at', 'materials.updated_at'
        );

//        $user = null;
        $current_user_id = null;
        if (Auth::check())
        {
            $user = Auth::guard('api')->user();
            $current_user_id = $user->id;
        }

        // TODO: Why is query not handling this automatically?
//todo fixed in model        $query->whereNull('deleted_at');

        $query->ofUserViewable($current_user_id, $search_user_id);

        if ($collection_id) {
            $query->ofCollection($collection_id);
        }

        $query->ofKeywords($keywords);

        $query->ofMaterialType($base_type_id, $type_id);

        $query->ofOrtonCone($orton_cone_id);

        $query->ofAtmosphere($atmosphere_id);

        $query->ofSurfaceType($surface_type_id);

        $query->ofTransparencyType($transparency_type_id);

//        $query->where('materials.is_analysis', false);

        $query->with('analysis');
        $query->with('atmospheres');
        $query->with('material_type');
        $query->with('shallowComponents');
        $query->with('thumbnail');
        $query->with('created_by_user');

        // TODO
        if ($is_primitive) {
            $query->where('is_primitive', true);
        }
        else {
            $query->where('is_primitive', false);
        }

        if (!empty($hex_color))
        {
            list($r, $g, $b) = sscanf($hex_color, "%02x%02x%02x");

            if (is_int($r) && is_int($g) && is_int($b)) {
                $query->join('material_images', 'material_images.material_id', '=', 'materials.id');

                $selectColor = '((CAST(material_images.dominant_rgb_r AS SIGNED) - '.$r.')*(CAST(material_images.dominant_rgb_r AS SIGNED) - '.$r.'))';
                $selectColor .= ' + ((CAST(material_images.dominant_rgb_g AS SIGNED) - '.$g.')*(CAST(material_images.dominant_rgb_g AS SIGNED) - '.$g.'))';
                $selectColor .= ' + ((CAST(material_images.dominant_rgb_b AS SIGNED) - '.$b.')*(CAST(material_images.dominant_rgb_b AS SIGNED) - '.$b.'))';
                $selectColor .= ' AS colordiff ';
                $query->selectRaw($selectColor);

                $query->whereNotNull('material_images.dominant_rgb_r');

                $query->with('analysis');
                $query->with('atmospheres');
                $query->with('material_type');
                $query->with('shallowComponents');
                $query->with('thumbnail');
                $query->with('created_by_user');

                $query->orderByRaw('colordiff ASC');
            }
        }

        $query->orderBy('updated_at', 'DESC');

        if ($count && $count < self::MAX_ITEMS_PER_PAGE) {
            $recipes = $query->paginate($count, ['*'], 'page', $page);
        }
        else {
            $recipes = $query->paginate(self::DEFAULT_ITEMS_PER_PAGE, ['*'], 'page', $page);
        }

        if (!$recipes || $recipes->total() == 0)
        {
            return $this->respondNotFound('No recipes found.');
        }

        $this->manager->parseIncludes(['atmospheres', 'thumbnail', 'createdByUser']);

        $resource = new FractalCollection($recipes, new ShallowMaterialTransformer());
        $resource->setPaginator(new IlluminatePaginatorAdapter($recipes));
        return $this->manager->createData($resource)->toArray();
    }

    /*
     * TODO: check query needs optimizing
    protected function getImageQuery($materialQuery, $r, $g, $b)
    {
        $imageQuery = MaterialImage::query();

        $imageQuery->join('materials', 'material_images.material_id', '=', 'materials.id');

        $selectColor = 'material_images.*, ';
        $selectColor .= '((CAST(material_images.dominant_rgb_r AS SIGNED) - '.$r.')*(CAST(material_images.dominant_rgb_r AS SIGNED) - '.$r.'))';
        $selectColor .= ' + ((CAST(material_images.dominant_rgb_g AS SIGNED) - '.$g.')*(CAST(material_images.dominant_rgb_g AS SIGNED) - '.$g.'))';
        $selectColor .= ' + ((CAST(material_images.dominant_rgb_b AS SIGNED) - '.$b.')*(CAST(material_images.dominant_rgb_b AS SIGNED) - '.$b.'))';
        $selectColor .= ' AS colordiff ';
        $selectColor .= ', materials.*';
        $imageQuery->selectRaw($selectColor);

        $q = $materialQuery->getQuery();
        $wheres = $q->wheres;
        $bindings = $q->getBindings();
        $imageQuery->whereHas('material', function ($q) use ($wheres, $bindings) {
            $q->mergeWheres($wheres, $bindings);
        });

        $imageQuery->whereNotNull('material_images.dominant_rgb_r');

        $imageQuery->with('material.analysis');
        $imageQuery->with('material.atmospheres');
        $imageQuery->with('material.material_type');
        $imageQuery->with('material.shallowComponents');
        $imageQuery->with('material.thumbnail');
        $imageQuery->with('material.created_by_user');

        $imageQuery->orderByRaw('colordiff ASC');

        return $imageQuery;

    }
    */

    /**
     * @param $id The Recipe ID
     * @return \Illuminate\Http\JsonResponse  Returns a list of nearest neighbors

        select id, `SiO2_umf`,
        `Al2O3_umf`,
        `SiO2_Al2O3`,
        SiO2_Al2O3_ratio_umf
        from material_analyses
        where SiO2_Al2O3_ratio_umf IS NOT NULL
        order by
        st_distance(SiO2_Al2O3, ST_GeomFromText('POINT(2.90345711144 0.371202367614)')) ASC;
    */
    public function nearestXY(Request $request)
    {

        $material_id = (int)$request->input('material_id');
        $material_type_id = (int)$request->input('material_type_id');
        $orton_cone_id = $request->input('cone');

        $xOxide = $request->input('x');
        $yOxide = $request->input('y');

        $yOxide = $this->getDBFieldFromJSONField($yOxide, Analysis::SiO2.'_umf');
        $xOxide = $this->getDBFieldFromJSONField($xOxide, Analysis::Al2O3.'_umf');

        $material = MaterialAnalysis::where('material_id', '=', $material_id)->first();

        if (!$material)
        {
            return $this->respondNotFound('Search material analysis not found.');
        }

        $yOxide_umf = $material[$yOxide];
        $xOxide_umf = $material[$xOxide];

        if ($yOxide_umf === null) {
            $yOxide_umf = 0;
        }
        if ($xOxide_umf === null) {
            $xOxide_umf = 0;
        }

        $query = Material::query();

        $distanceField =
            '('.$yOxide_umf.' - analyses.'.$yOxide.') * ('.$yOxide_umf.' - analyses.'.$yOxide.') + '
            . '('.$xOxide_umf.' - analyses.'.$xOxide.') * ('.$xOxide_umf.' - analyses.'.$xOxide.')';

        /*
         * 3-axis query:
        if ($oxide3) {
            $distanceField =
                '('.$yOxide_umf.' - analyses.'.$yOxide.') * ('.$yOxide_umf.' - analyses.'.$yOxide.') + '
                . '('.$xOxide_umf.' - analyses.'.$xOxide.') * ('.$xOxide_umf.' - analyses.'.$xOxide.') + '
                . '('.$oxide3_umf.' - analyses.'.$oxide3.') * ('.$oxide3_umf.' - analyses.'.$oxide3.')';
        }
        */

        $selectFields = 'materials.id, materials.name, materials.is_primitive, materials.material_type_id, '
            .'materials.is_analysis, materials.is_theoretical, materials.from_orton_cone_id, '
            .'materials.to_orton_cone_id, materials.surface_type_id, materials.transparency_type_id, '
            .'materials.thumbnail_id, materials.is_private, materials.created_by_user_id, '
            .'materials.created_at, materials.updated_at, '
            . $distanceField
            .' as distance';

        $query->join('material_analyses as analyses', 'analyses.material_id', '=', 'materials.id');

        $query->select(DB::raw($selectFields));

        $query->with('material_type');
        $query->with('analysis');
        $query->with('thumbnail');
        $query->with('created_by_user');

        // Only search for recipes
        // TODO:  Why not also search for primitives?
        // $query->where('materials.is_primitive', false);

        // Needs to have both oxides in order to be charted
        // Hmm, Does it need both oxides?
        // $query->where('analyses.'.$yOxide.'_umf', '>', 0);
        // $query->where('analyses.'.$xOxide.'_umf', '>', 0);
        // Unneeded $query->where('analyses.SiO2_Al2O3_ratio_umf', '>', 0);

        //$query->where('analyses.'.$yOxide, '<>', null);
        //$query->where('analyses.'.$xOxide, '<>', null);

        if ($material_type_id > 0) {
            // Search only within a type
            $query->ofMaterialType(null, $material_type_id);
        }

        $query->ofOrtonCone($orton_cone_id);

        $current_user_id = null;
        if (Auth::check())
        {
            $user = Auth::guard('api')->user();
            $current_user_id = $user->id;
        }
        $query->ofUserViewable($current_user_id, null);

        // Exclude the original material id
        $query->where('materials.id', '<>', $material_id);

        // $query->orderByRaw('st_distance(analyses.SiO2_Al2O3, ST_GeomFromText(\'POINT('.$yOxide_umf.' '.$xOxide_umf.')\')) ASC');

        $query->orderBy('distance', 'asc');
        $query->orderBy('analyses.'.$yOxide, 'asc');
        $query->orderBy('analyses.'.$xOxide, 'asc');

        $query->limit(100);

        $materials = $query->get();

        if (!$materials)
        {
            return $this->respondNotFound('No materials found.');
        }

        $resource = new FractalCollection($materials, new ChartPointMaterialTransformer());
        return $this->manager->createData($resource)->toArray();
        /*
         * TODO

        $chartMaterialTransformer = new ChartMaterialTransformer();

        return $this->respond([
            'data' => $chartMaterialTransformer->transformCollection($materials->all())
        ]);
        */
    }

    protected function getDBFieldFromJSONField($oxide, $defaultValue = null) {
        if ($oxide) {
            if (in_array($oxide, Analysis::OXIDE_NAMES)) {
                return $oxide.'_umf';
            }
            else if ($oxide === 'R2OTotal') {
                return 'R2O_umf';
            }
            else if ($oxide === 'ROTotal') {
                return 'RO_umf';
            }
            else if ($oxide === 'SiO2Al2O3Ratio') {
                return 'SiO2_Al2O3_ratio_umf';
            }
        }
        if ($defaultValue) {
            return $defaultValue;
        }
        return null;
    }

    /**
     * @param $id The Recipe ID
     * @return \Illuminate\Http\JsonResponse  Returns a list of nearest neighbors

    select id, `SiO2_umf`,
    `Al2O3_umf`,
    `SiO2_Al2O3`,
    SiO2_Al2O3_ratio_umf
    from material_analyses
    where SiO2_Al2O3_ratio_umf IS NOT NULL
    order by
    st_distance(SiO2_Al2O3, ST_GeomFromText('POINT(2.90345711144 0.371202367614)')) ASC;
     */
    /*
    public function nearestSiAlOriginal(Request $request)
    {
        $material_id = (int)$request->input('material_id');
        $material_type_id = (int)$request->input('material_type_id');
        $orton_cone_id = $request->input('cone');

        $oxide1 = $request->input('oxide1');
        $oxide2 = $request->input('oxide2');

        $material = MaterialAnalysis::where('material_id', '=', $material_id)->first();

        if (!$material)
        {
            return $this->respondNotFound('Search material analysis not found.');
        }

        $oxide1_umf = $material->SiO2_umf;
        $oxide2_umf = $material->Al2O3_umf;

        if (!($oxide1_umf > 0) || !($oxide2_umf > 0))
        {
            return $this->respondNotFound('Search material analysis does not have SiO2 or Al2O3.');
        }

        $query = Material::query();

        $query->join('material_analyses as analyses', 'analyses.material_id', '=', 'materials.id');

        $query->select(
            'materials.id', 'materials.name',
            'materials.is_primitive', 'materials.material_type_id',
            'materials.is_analysis', 'materials.is_theoretical',
            'materials.from_orton_cone_id', 'materials.to_orton_cone_id',
            'materials.surface_type_id', 'materials.transparency_type_id',
            'materials.thumbnail_id',
            'materials.is_private', 'materials.created_by_user_id',
            'materials.created_at', 'materials.updated_at'
        );

        $query->with('material_type');
        $query->with('analysis');
        $query->with('thumbnail');
        $query->with('created_by_user');

        $query->where('materials.is_primitive', false);       // Only search for recipes
        $query->where('analyses.SiO2_umf', '>', 0); // Needs to have both SiO2 and Al2O3 to be charted
        $query->where('analyses.Al2O3_umf', '>', 0);
        $query->where('analyses.SiO2_Al2O3_ratio_umf', '>', 0);

        if ($material_type_id > 0) {
            // Search only within a type
            //$query->where('materials.material_type_id', $material_type_id);
            $query->ofMaterialType(null, $material_type_id);
        }

        $query->ofOrtonCone($orton_cone_id);

        $current_user_id = null;
        if (Auth::check())
        {
            $user = Auth::guard('api')->user();
            $current_user_id = $user->id;
        }
        $query->ofUserViewable($current_user_id, null);

        // Exclude the original material id
        $query->where('materials.id', '<>', $material_id);

        $query->orderByRaw('st_distance(analyses.SiO2_Al2O3, ST_GeomFromText(\'POINT('.$oxide1_umf.' '.$oxide2_umf.')\')) ASC');

        $query->limit(100);

        $materials = $query->get();

        if (!$materials)
        {
            return $this->respondNotFound('No materials found.');
        }

        $resource = new FractalCollection($materials, new ShallowMaterialTransformer());
        return $this->manager->createData($resource)->toArray();
    }
    */

    public function similarMaterials(SimilarMaterialsMaterialRequest $request)
    {
        $data = $request->all();

        $materials = $this->materialRepository->similarMaterials($data);

        if (!$materials)
        {
            return $this->respondNotFound('No materials found.');
        }

        $this->manager->parseIncludes(['materialComponents', 'atmospheres', 'thumbnail', 'createdByUser']);

        $resource = new FractalCollection($materials, new ShallowMaterialTransformer());

        return $this->manager->createData($resource)->toArray();
    }

    public function similarUnityFormula($material_id)
    {
        $materials = $this->materialRepository->similarUnityFormula($material_id);

        if (!$materials)
        {
            return $this->respondNotFound('No materials found.');
        }

        $resource = new FractalCollection($materials, new ChartPointMaterialTransformer());
        return $this->manager->createData($resource)->toArray();
    }

    public function similarBaseComponents($material_id)
    {
        $materials = $this->materialRepository->similarBaseComponents($material_id);

        if (!$materials)
        {
            return $this->respondNotFound('No materials found.');
        }

        $this->manager->parseIncludes(['materialComponents', 'atmospheres', 'thumbnail', 'createdByUser']);

        $resource = new FractalCollection($materials, new ShallowMaterialTransformer());
        return $this->manager->createData($resource)->toArray();
        /*
        $transformer = new NoComponentsMaterialTransformer();
        return $this->respond([
            'data' => $transformer->transformCollection($materials->all())
        ]);
        */
    }


}
