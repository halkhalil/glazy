<?php

namespace App\Models\Glazy\Material;

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
        return $this->belongsTo('App\Models\Glazy\Material\Material');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}