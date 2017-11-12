<?php namespace App\Models;

use App\Models\LookupModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransparencyType extends LookupModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'transparency_types';

	use SoftDeletes;

	protected $fillable = [
		'name',
		'description',
	];


	function __construct()
	{
		$this->static_values = [
			'1' => 'Opaque',
			'2' => 'Semi-opaque',
			'3' => 'Translucent',
			'4' => 'Transparent',
		];
	}
}