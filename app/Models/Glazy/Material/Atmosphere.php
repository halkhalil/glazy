<?php namespace App\Models\Glazy\Material;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Glazy\LookupModel;

class Atmosphere extends LookupModel {

    use SoftDeletes;

    protected $table = 'atmospheres';

    protected $fillable = [
        'name',
        'description',
    ];

    function __construct() {
        $this->static_values = [
            1 => 'Oxidation',
            2 => 'Neutral',
            3 => 'Reduction',
            4 => 'Salt & Soda',
            5 => 'Wood',
            6 => 'Raku',
            7 => 'Luster',
        ];
    }

    public function recipes()
    {
        return $this->belongsToMany('App\Models\Glazy\Material\Material', 'material_atmospheres');
    }

}