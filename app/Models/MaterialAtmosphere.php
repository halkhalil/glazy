<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialAtmosphere extends Model
{

    public function material()
    {
        return $this->belongsTo('App\Models\Material');
    }

    public function atmosphere()
    {
        return $this->belongsTo('App\Models\Atmosphere');
    }

}
