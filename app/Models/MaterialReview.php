<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialReview extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'material_reviews';

    protected $fillable = [
        'title',
        'description',
        'rating'
    ];

    public function material()
    {
        return $this->belongsTo('App\Models\Material');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}