<?php namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\LookupModel;

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
        return $this->belongsToMany('App\Models\Material', 'material_atmospheres');
    }

}