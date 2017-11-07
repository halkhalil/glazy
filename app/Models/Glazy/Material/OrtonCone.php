<?php namespace App\Models\Glazy\Material;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Glazy\LookupModel;

class OrtonCone extends LookupModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'orton_cones';

	use SoftDeletes;

	protected $fillable = [
		'name',
		'description',
	];


	function __construct()
	{
		$this->static_values = [
			38 => '14',
			37 => '13',
			36 => '12',
			35 => '11',
			34 => '10',
			33 => '9',
			32 => '8',
			31 => '7',
			30 => '6',
			29 => '5&#189;',
			28 => '5',
			27 => '4',
			26 => '3',
			25 => '2',
			24 => '1',
			23 => '01',
			22 => '02',
			21 => '03',
			20 => '04',
			19 => '05',
			18 => '05&#189;',
			17 => '06',
			16 => '07',
			15 => '08',
			14 => '09',
			13 => '010',
			12 => '011',
			11 => '012',
			10 => '013',
			9 => '014',
			8 => '015',
			7 => '016',
			6 => '017',
			5 => '018',
			4 => '019',
			3 => '020',
			2 => '021',
			1 => '022',
		];
	}

    public function getHighFireBottomConeId() {
        return 33; // cone 9
    }

    public function getHighFireTopConeId() {
        return 38; // cone 14
    }

    public function getMidFireBottomConeId() {
        return 27; // cone 4
    }

    public function getMidFireTopConeId() {
        return 32; // cone 8
    }

    public function getLowFireBottomConeId() {
        return 1; // cone 022
    }

    public function getLowFireTopConeId() {
        return 26; // cone 3
    }
	
}

