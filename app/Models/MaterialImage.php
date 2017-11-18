<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialImage extends Model
{
    const DB_ID = 'id';
    const DB_MATERIAL_ID = 'material_id';
    const DB_TITLE = 'title';
    const DB_DESCRIPTION = 'description';
    const DB_ORTON_CONE_ID = 'orton_cone_id';
    const DB_ATMOSPHERE_ID = 'atmosphere_id';
    const DB_DOMINANT_RGB_R = 'dominant_rgb_r';
    const DB_DOMINANT_RGB_G = 'dominant_rgb_g';
    const DB_DOMINANT_RGB_B = 'dominant_rgb_b';
    const DB_SECONDARY_RGB_R = 'secondary_rgb_r';
    const DB_SECONDARY_RGB_G = 'secondary_rgb_g';
    const DB_SECONDARY_RGB_B = 'secondary_rgb_b';
    const DB_FILENAME = 'filename';
    const DB_IS_PRIVATE = 'is_private';
    const DB_CREATED_BY_USER_ID = 'created_by_user_id';
    const DB_CREATED_AT = 'created_at';
    const DB_UPDATED_AT = 'updated_at';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'material_images';

    protected $fillable = [
        'title',
        'description',
        'orton_cone_id',
        'atmosphere_id',
    ];

    public function material()
    {
        return $this->belongsTo('App\Models\Material');
    }

    public function orton_cone()
    {
        return $this->belongsTo('App\Models\OrtonCone');
    }

    public function atmosphere()
    {
        return $this->belongsTo('App\Models\Atmosphere');
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
     * Scope a query to only include materials created by $user_id
     */
    public function scopeOfCreatedByUserId($query, $search_user_id, $current_user_id = null)
    {
        $query->where('created_by_user_id', $search_user_id);

        if ($current_user_id !== $search_user_id) {
            // This is unique as we are making sure the recipe
            // associated with the image is not private
            $query->whereHas('material', function ($q) {
                $q->where('is_private', false);
            });
        }

        return $query;
    }

    public function scopeOfNotPrivate($query, $current_user_id)
    {
        // This is unique as we are making sure the recipe
        // associated with the image is not private
        $query->whereHas('material', function ($q) use ($current_user_id) {
            $q->where('is_private', false);
            $q->orWhere('created_by_user_id', $current_user_id);
        });

        return $query;
    }

    public function scopeOfPublic($query)
    {
        // This is unique as we are making sure the recipe
        // associated with the image is not private
        $query->whereHas('material', function ($q) {
            $q->where('is_private', false);
        });

        return $query;
    }

    public function scopeOfKeywords($query, $keywords)
    {
        $keywords = trim($keywords);

        if (!empty($keywords))
        {
            $search_str = substr($keywords, 0, 50);
            $search_str = preg_replace('/\s+/',',',$search_str);
            $search_str = addslashes($search_str);

            $selectWords = 'material_images.*, ';
            $selectWords .= 'MATCH (material_images.description, material_images.title) AGAINST (\''.$search_str.'\') AS relevance';
            $query->selectRaw($selectWords);
            $query->whereRaw('MATCH (material_images.description, material_images.title) AGAINST (\''.$search_str.'\') > 0');
            $query->orderByRaw('relevance DESC');

        }

        return $query;
    }
}