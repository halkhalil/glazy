<?php namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\LookupModel;

class SurfaceType extends LookupModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'surface_types';

	use SoftDeletes;

	protected $fillable = [
		'name',
		'description',
	];


	function __construct()
	{
		$this->static_values = [
			'1' => 'Matte',
			'2' => 'Stony Matte',
			'3' => 'Dry Matte',
			'4' => 'Semi-matte',
			'5' => 'Smooth Matte',
			'7' => 'Satin-matte',
			'6' => 'Satin',
			'9' => 'Semi-glossy',
			'8' => 'Glossy',
		];
	}
}