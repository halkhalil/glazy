<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Log;

class Collection extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'collections';

    protected $fillable = [
        'parent_id',
        'name',
        'description'
    ];

    public function parent()
    {
        return $this->belongsTo('App\Models\Collection');
    }

    public function collection_type()
    {
        return $this->belongsTo('App\Models\CollectionType');
    }

    public function thumbnail()
    {
        return $this->belongsTo('App\Models\MaterialImage');
    }

    public function materials()
    {
        return $this->hasMany('App\Models\CollectionMaterial', 'collection_id')
            ->with('material')
            ->orderBy('created_at', 'desc');
    }

    public function created_by_user()
    {
        return $this->belongsTo('App\User');
    }

    public function updated_by_user()
    {
        return $this->belongsTo('App\User');
    }

    public function incrementCount() {
        $this->material_count += 1;
    }

    public function decrementCount() {
        if ($this->material_count > 0) {
            $this->material_count -= 1;
        }
    }


    /**
     * Scope a query to only include materials created by $user_id
     */
    public function scopeOfCreatedByUserId($query, $search_user_id, $current_user_id = null)
    {
        $query->where('created_by_user_id', $search_user_id);

        if ($current_user_id !== $search_user_id) {
            $query->where('is_private', false);
        }

        return $query;
    }


    public function scopeOfNotPrivate($query, $current_user_id)
    {
        $query->where(function ($query) use ($current_user_id) {
            $query->where('is_private', false)
                ->orWhere('created_by_user_id', $current_user_id);
        });

        return $query;
    }

    public function scopeOfPublic($query)
    {
        $query->where('is_private', false);

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

            $selectWords = 'collections.*, ';
            $selectWords .= 'MATCH (description, name) AGAINST (\''.$search_str.'\') AS relevance';
            $query->selectRaw($selectWords);
            $query->whereRaw($whereID.'MATCH (description, name) AGAINST (\''.$search_str.'\') > 0'.$whereIDend);
            $query->orderByRaw('relevance DESC');
        }

        return $query;
    }

}