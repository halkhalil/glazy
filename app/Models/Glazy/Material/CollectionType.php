<?php namespace App\Models\Glazy\Material;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Glazy\LookupModel;


class CollectionType extends LookupModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'collection_types';

	use SoftDeletes;

	protected $fillable = [
		'name',
		'description'
	];

	function __construct()
	{
        $this->static_values = [
            1 => 'User',
            2 => 'Group',
            3 => 'World',
            4 => 'Bookmarks'
        ];
	}

}

