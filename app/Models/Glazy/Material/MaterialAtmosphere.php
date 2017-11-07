<?php namespace App\Models\Glazy\Material;

use Illuminate\Database\Eloquent\Model;

class MaterialAtmosphere extends Model
{

    public function material()
    {
        return $this->belongsTo('App\Models\Glazy\Material\Material');
    }

    public function atmosphere()
    {
        return $this->belongsTo('App\Models\Glazy\Material\Atmosphere');
    }

}
