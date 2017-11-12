<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DerekPhilipAu\Ceramicscalc\Models\Material\CompositeMaterialComponent;


/**
 * Class HistoryType
 * package App
 */
class MaterialMaterial extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'material_materials';

    protected $fillable = [
        'parent_material_id',
        'component_material_id',
        'percentage_amount',
        'is_additional',
    ];

	public function parent_material()
	{
		return $this->belongsTo('App\Models\Material');
	}

	public function component_material()
	{
		return $this->belongsTo('App\Models\Material');
	}

	public function setMaterialComponent($parent_id, CompositeMaterialComponent $materialComponent)
	{
		$this->parent_material_id = $parent_id;
		$this->component_material_id = $materialComponent->getMaterial()->getUniqueId();
		$this->percentage_amount = $materialComponent->getAmount();
		$this->is_additional = $materialComponent->getIsAdditional();
	}

}