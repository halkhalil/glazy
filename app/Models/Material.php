<?php namespace App\Models;

use DerekPhilipAu\Ceramicscalc\Models\Material\CompositeMaterial;
use DerekPhilipAu\Ceramicscalc\Models\Material\PrimitiveMaterial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

use DerekPhilipAu\Ceramicscalc\Models\Analysis\PercentageAnalysis;
use DerekPhilipAu\Ceramicscalc\Models\Analysis\FormulaAnalysis;
use DerekPhilipAu\Ceramicscalc\Models\Analysis\Analysis;
use DerekPhilipAu\Ceramicscalc\Models\Material\AbstractMaterial;

use Illuminate\Support\Facades\Log;
/**
 * Class HistoryType
 * package App
 */
class Material extends Model
{
    use SoftDeletes;

    const DB_ID = 'id';
    const DB_PARENT_ID = 'parent_id';
    const DB_NAME = 'name';
    const DB_OTHER_NAMES = 'other_names';
    const DB_DESCRIPTION = 'description';
    const DB_IS_ANALYSIS = 'is_analysis';
    const DB_IS_PRIMITIVE = 'is_primitive';
    const DB_IS_ARCHIVED = 'is_archived';
    const DB_IS_PRIVATE = 'is_private';
    const DB_MATERIAL_TYPE_ID = 'material_type_id';
    const DB_MATERIAL_TYPE = 'material_type';
    const DB_FROM_ORTON_CONE = 'from_orton_cone';
    const DB_FROM_ORTON_CONE_ID = 'from_orton_cone_id';
    const DB_TO_ORTON_CONE = 'to_orton_cone';
    const DB_TO_ORTON_CONE_ID = 'to_orton_cone_id';
    const DB_SURFACE_TYPE_ID = 'surface_type_id';
    const DB_TRANSPARENCY_TYPE_ID = 'transparency_type_id';
    const DB_COUNTRY_ID = 'country_id';
    const DB_COLOR_NAME = 'color_name';
    const DB_RGB_R = 'rgb_r';
    const DB_RGB_G = 'rgb_g';
    const DB_RGB_B = 'rgb_b';
    const DB_THUMBNAIL_ID = 'thumbnail_id';
    const DB_RATING_TOTAL = 'rating_total';
    const DB_RATING_NUMBER = 'rating_number';
    const DB_RATING_AVERAGE = 'rating_average';
    const DB_CREATED_BY_USER_ID = 'created_by_user_id';
    const DB_CREATED_AT = 'created_at';
    const DB_UPDATED_AT = 'updated_at';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'materials';


    protected $fillable = [
        'name',
        'other_names',
        'description',
        'material_type_id',
        'is_analysis',
        'from_orton_cone_id',
        'to_orton_cone_id',
        'surface_type_id',
        'transparency_type_id',
        'country_id',
        'color_name',
        'rgb_r',
        'rgb_g',
        'rgb_b',
    ];
/*
    public static function boot()
    {
        // TODO: doesn't work for migrations
        // Update field update_by with current user id each time record is updated.
        static::creating(function ($material) {
            $material->created_by_user_id = Auth::user()->id;
        });
        // Update field update_by with current user id each time record is updated.
        static::updating(function ($material) {
            $material->updated_by_user_id = Auth::user()->id;
        });



                parent::boot();

                static::deleting(function($check) {
                    $check->checks()->delete();
                    $check->results()->delete();
                });
    }
*/
    public function parent()
    {
        return $this->belongsTo('App\Models\Material', 'parent_id');
    }

    public function analysis()
    {
        return $this->hasOne('App\Models\MaterialAnalysis');
    }

    public function atmospheres()
    {
        return $this->belongsToMany('App\Models\Atmosphere', 'material_atmospheres');
    }

    public function shallowComponents()
    {
        return $this->hasMany('App\Models\MaterialMaterial', 'parent_material_id')
            ->with('component_material')
            ->orderBy('is_additional', 'asc')
            ->orderBy('percentage_amount', 'desc')
            ->orderBy('component_material_id', 'asc');
    }

    public function components()
    {
        return $this->hasMany('App\Models\MaterialMaterial', 'parent_material_id')
            ->with('component_material')
            ->with('component_material.analysis')
            ->with('component_material.thumbnail')
            // ->with('component_material.thumbnail.created_by_user')
            // ->with('component_material.thumbnail.created_by_user.profile')
            ->orderBy('is_additional', 'asc')
            ->orderBy('percentage_amount', 'desc')
            ->orderBy('component_material_id', 'asc');
    }


    public function parent_materials()
    {
        return $this->belongsToMany('App\Models\Material', 'material_materials');
    }

    public function material_type()
    {
        return $this->belongsTo('App\Models\MaterialType');
    }

    public function from_orton_cone()
    {
        return $this->belongsTo('App\Models\OrtonCone');
    }

    public function to_orton_cone()
    {
        return $this->belongsTo('App\Models\OrtonCone');
    }

    public function surface_type()
    {
        return $this->belongsTo('App\Models\SurfaceType');
    }

    public function transparency_type()
    {
        return $this->belongsTo('App\Models\TransparencyType');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }

    public function thumbnail()
    {
        return $this->belongsTo('App\Models\MaterialImage');
    }

    public function images()
    {
        return $this->hasMany('App\Models\MaterialImage', 'material_id');
    }

    public function collections()
    {
        return $this->belongsToMany('App\Models\Collection', 'collection_materials');
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\MaterialReview', 'material_id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\MaterialComment', 'material_id');
    }

    public function created_by_user()
    {
        return $this->belongsTo('App\User');
    }

    public function updated_by_user()
    {
        return $this->belongsTo('App\User');
    }


    /**
     * @param $hex
     *
     * Return array of R, G, B values
     */
    public static function getRGBFromHex($hex)
    {
        // Remove leading # if necessary
        $hex = ltrim($hex, '#');

        list($r, $g, $b) = sscanf($hex, "%02x%02x%02x");
        $var = array();
        $var["Red"] = $r;
        $var["Green"] = $g;
        $var["Blue"] = $b;
        return $var;
    }

    public function setRGBFromHex($hex)
    {
        // Remove leading # if necessary
        $hex = ltrim($hex, '#');

        list($r, $g, $b) = sscanf($hex, "%02x%02x%02x");
        $this->rgb_r = $r;
        $this->rgb_g = $g;
        $this->rgb_b = $b;
    }

    public static function getHexFromRGB($r, $g, $b)
    {
        $r = dechex($r);
        if (strlen($r) == 1) {
            $r = '0' . $r;
        }
        $g = dechex($g);
        if (strlen($g) == 1) {
            $g = '0' . $g;
        }
        $b = dechex($b);
        if (strlen($b) == 1) {
            $b = '0' . $b;
        }

        return $r . $g . $b;
    }

    public function getHexColor()
    {
        return static::getHexFromRGB($this->rgb_r, $this->rgb_g, $this->rgb_b);
    }

    /*
     * TODO: Assumes a material is already primitive and has no components.
    */
    public function getPrimitiveMaterial()
    {
        $primitiveMaterial = new PrimitiveMaterial($this->id);
        $percentageAnalysis = new PercentageAnalysis();

        $existingAnalysis = $this->analysis;

        if ($existingAnalysis) {
            foreach (Analysis::OXIDE_NAMES as $oxide_name) {
                $property_name = $oxide_name . '_percent';
                $oxide_amount = $existingAnalysis->$property_name;
                if ($oxide_amount === null) {
                    $oxide_amount = 0;
                }
                $percentageAnalysis->setOxide($oxide_name, $oxide_amount);
            }
            $percentageAnalysis->setLOI($existingAnalysis->loi);
        }

        $primitiveMaterial->setPercentageAnalysis($percentageAnalysis);
        return $primitiveMaterial;
    }

    public function getPrimitiveMaterialUsingFormula()
    {
        $primitiveMaterial = new PrimitiveMaterial($this->id);
        $formulaAnalysis = new FormulaAnalysis();

        $existingAnalysis = $this->analysis;

        if ($existingAnalysis) {
            foreach (Analysis::OXIDE_NAMES as $oxide_name) {
                $property_name = $oxide_name . '_umf';
                $formulaAnalysis->setOxide($oxide_name, $existingAnalysis->$property_name);
            }
        }
        $primitiveMaterial->setFormulaAnalysis($formulaAnalysis);
        return $primitiveMaterial;
    }



    /**
    public function scopeOfCreatedByUserId($query, $search_user_id, $current_user_id = null)
    {
        $query->where('created_by_user_id', $search_user_id);

        if ($current_user_id !== $search_user_id) {
            // Searching for someone else's materials
            $query->where('is_private', false);
        }

        return $query;
    }


    public function scopeOfNotPrivate($query, $current_user_id)
    {
        // Can view all my own recipes (including private) and
        // everyone else's public recipes.
        $query->where(function ($query) use ($current_user_id) {
            $query->where('is_private', false)
                ->orWhere('materials.created_by_user_id', $current_user_id);
        });

        return $query;
    }

    public function scopeOfPublic($query)
    {
        // Not logged in, can only view public recipes.
        $query->where('materials.is_private', false);

        return $query;
    }
*/

    public function scopeOfUserViewable($query, $current_user_id, $search_user_id)
    {
        if ($search_user_id) {
            // Search by a user
            $query->where('materials.created_by_user_id', $search_user_id);

            if (!$current_user_id || $current_user_id !== $search_user_id) {
                // If user not logged in or user viewing someone else's recipes,
                // don't show private.
                $query->where('materials.is_private', false);
            }
        }
        elseif ($current_user_id) {
            // Can view all my own recipes (including private) and
            // everyone else's public recipes.
            $query->where(function ($query) use ($current_user_id) {
                $query->where('materials.is_private', false)
                    ->orWhere('materials.created_by_user_id', $current_user_id);
            });
        }
        else {
            // Not logged in, can only view public recipes.
            $query->where('materials.is_private', false);
        }

        return $query;
    }

    public function scopeOfCollection($query, $collection_id)
    {
        if (!empty($collection_id)) {
            $query->with('collections')->whereHas('collections', function($query) use ($collection_id) {
                $query->where('collections.id', $collection_id);
            });
        }

        return $query;
    }


    public function scopeOfKeywords($query, $keywords)
    {
        $keywords = trim($keywords);

        if (!empty($keywords))
        {
            $whereID = '';
            $whereIDend = '';
            if (is_numeric($keywords) && is_int($keywords + 0)) {
                $whereID = '(id = '.$keywords.' OR ';
                $whereIDend = ')';
            }

            $search_str = substr($keywords, 0, 50);
            $search_str = preg_replace('/\s+/',',',$search_str);
            $search_str = addslashes($search_str);

            $selectWords = 'MATCH (materials.description, materials.name, materials.other_names) AGAINST (\''.$search_str.'\') AS relevance';
            $query->selectRaw($selectWords);
            $query->whereRaw($whereID.'MATCH (materials.description, materials.name, materials.other_names) AGAINST (\''.$search_str.'\') > 0'.$whereIDend);
            $query->orderByRaw('relevance DESC');
        }

        return $query;
    }

    public function scopeOfMaterialType($query, $base_type_id, $type_id)
    {
        $search_type_id = null;
        $materialType = new MaterialType();

        if (!empty($type_id)
            && !empty($materialType->getValue($type_id))) {
            // type selected, search by it..
            $search_type_id = $type_id;
        }
        elseif (!empty($base_type_id)
            && !empty($materialType->getValue($base_type_id))) {
            $search_type_id = $base_type_id;
        }
        else {
            // Neither base nor type id given
            return $query;
        }

        $catIds = $materialType->getChildrenCategoriesArrayIncludingSelf($search_type_id);

        if (count($catIds) > 0)
        {
            if (count($catIds) == 1)
            {
                $query->where('materials.material_type_id', $catIds[0]);
            }
            else
            {
                $query->whereIn('materials.material_type_id', $catIds);
            }
        }

        return $query;
    }

    public function scopeOfOrtonCone($query, $orton_cone_id)
    {
        $ortonCone = new OrtonCone();

        if ($orton_cone_id == 'high') {
            $from_orton_cone_id = $ortonCone->getHighFireBottomConeId();
            $query->where(function ($query) use ($from_orton_cone_id) {
                $query->where('materials.to_orton_cone_id', '>=', $from_orton_cone_id);
            });
        }
        elseif ($orton_cone_id == 'mid') {
            $from_orton_cone_id = $ortonCone->getMidFireBottomConeId();
            $to_orton_cone_id = $ortonCone->getMidFireTopConeId();

            // TODO:  This query still misses cases in which a recipe cone range extends farther
            // on both sides than the query range.
            $query->where(function ($query) use ($from_orton_cone_id, $to_orton_cone_id) {
                $query->whereBetween('materials.from_orton_cone_id', [$from_orton_cone_id, $to_orton_cone_id])
                    ->orWhere(function ($query) use ($from_orton_cone_id, $to_orton_cone_id) {
                        $query->whereBetween('materials.to_orton_cone_id', [$from_orton_cone_id, $to_orton_cone_id]);
                    });
            });

        }
        elseif ($orton_cone_id == 'low') {
            $to_orton_cone_id = $ortonCone->getLowFireTopConeId();
            $query->where(function ($query) use ($to_orton_cone_id) {
                $query->where('materials.from_orton_cone_id', '<=', $to_orton_cone_id);
            });
        }
        elseif (!empty($orton_cone_id)
            && !empty($ortonCone->getValue((int)$orton_cone_id))) {

            // Search for orton cone temperature within range
            $query->where(function ($query) use ($orton_cone_id) {
                $query->where('materials.from_orton_cone_id', '<=', (int)$orton_cone_id)
                    ->where('materials.to_orton_cone_id', '>=', (int)$orton_cone_id);
            });

        }

        return $query;

    }


    public function scopeOfAtmosphere($query, $atmosphere_id)
    {
        $atmosphere = new Atmosphere();

        if (!empty($atmosphere_id)
            && !empty($atmosphere->getValue($atmosphere_id))) {
            $query->with('atmospheres')->whereHas('atmospheres', function($query) use ($atmosphere_id) {
                $query->where('atmospheres.id', $atmosphere_id);
            });
        }

        return $query;
    }

    public function scopeOfSurfaceType($query, $surface_type_id)
    {
        $surfaceType = new SurfaceType();

        if (!empty($surface_type_id)
            && !empty($surfaceType->getValue($surface_type_id))) {
            $query->where('materials.surface_type_id', $surface_type_id);
        }

        return $query;
    }

    public function scopeOfTransparencyType($query, $transparency_type_id)
    {
        $transparencyType = new TransparencyType();

        if (!empty($transparency_type_id)
            && !empty($transparencyType->getValue($transparency_type_id))) {
            $query->where('materials.transparency_type_id', $transparency_type_id);
        }

        return $query;
    }

    public function scopeOfCountry($query, $country_id)
    {
        if (!empty($country_id)) {
            $query->where('materials.country_id', $country_id);
            /*
             * TODO: Include materials without country?  Nah..
            $query->where(function ($query) use ($country_id) {
                $query->where('materials.country_id', $country_id)
                    ->orWhere('materials.country_id', null);
            });
            */

        }

        return $query;
    }

    public function scopeOfUser($query, $username)
    {
        if (!empty($username)) {
            if (is_numeric($username) && is_int($username + 0)) {
                $query->where('materials.created_by_user_id', $username);
            }
            else {
                $query->with('created_by_user')->whereHas('created_by_user', function($query) use ($username) {
                    $query->where('users.name', 'like', '%'.$username.'%');
                });
            }
        }

        return $query;
    }

}