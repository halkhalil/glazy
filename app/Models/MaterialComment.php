<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialComment extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'material_comments';

    protected $fillable = [
        'content'
    ];

    public function parent()
    {
        return $this->belongsTo('App\Models\MaterialComment');
    }

    public function material()
    {
        return $this->belongsTo('App\Models\Material');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}