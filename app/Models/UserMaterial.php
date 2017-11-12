<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMaterial extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_materials';

    protected $fillable = [
        'stock_amount',
        'price',
        'vendor',
        'vendor_code'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function material()
    {
        return $this->belongsTo('App\Models\Material');
    }

}