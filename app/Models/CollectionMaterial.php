<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CollectionMaterial extends Model {

	protected $table = 'collection_materials';

    protected $fillable = [
        'collection_id',
        'material_id'
    ];

	public function collection()
	{
		return $this->belongsTo('App\Models\Collection');
	}

	public function material()
	{
		return $this->belongsTo('App\Models\Material');
	}

}