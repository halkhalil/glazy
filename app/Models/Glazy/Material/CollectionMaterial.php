<?php namespace App\Models\Glazy\Material;

use Illuminate\Database\Eloquent\Model;


class CollectionMaterial extends Model {

	protected $table = 'collection_materials';

    protected $fillable = [
        'collection_id',
        'material_id'
    ];

	public function collection()
	{
		return $this->belongsTo('App\Models\Glazy\Material\Collection');
	}

	public function material()
	{
		return $this->belongsTo('App\Models\Glazy\Material\Material');
	}

}