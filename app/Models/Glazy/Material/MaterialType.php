<?php namespace App\Models\Glazy\Material;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Glazy\LookupModel;


class MaterialType extends LookupModel {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'material_types';

	use SoftDeletes;

	protected $fillable = [
		'parent_id',
		'name',
		'concatenated_name',
		'description',
		'url',
	];

	function __construct()
	{
		$this->static_values = [
            1 => array( 'id' => 1, 'parent_id' => NULL, 'name' => 'Primitive', 'concatenated_name' => 'Primitive'),
            5 => array( 'id' => 5, 'parent_id' => 1, 'name' => 'Alumina', 'concatenated_name' => 'Alumina'),
            9 => array( 'id' => 9, 'parent_id' => 1, 'name' => 'Ash', 'concatenated_name' => 'Ash'),
            13 => array( 'id' => 13, 'parent_id' => 1, 'name' => 'Clay', 'concatenated_name' => 'Clay'),
            17 => array( 'id' => 17, 'parent_id' => 1, 'name' => 'Colorant', 'concatenated_name' => 'Colorant'),
            20 => array( 'id' => 20, 'parent_id' => 1, 'name' => 'Feldspar', 'concatenated_name' => 'Feldspar'),
            23 => array( 'id' => 23, 'parent_id' => 1, 'name' => 'Flux', 'concatenated_name' => 'Flux'),
            26 => array( 'id' => 26, 'parent_id' => 1, 'name' => 'Frit', 'concatenated_name' => 'Frit'),
            29 => array( 'id' => 29, 'parent_id' => 1, 'name' => 'Opacifier', 'concatenated_name' => 'Opacifier'),
            32 => array( 'id' => 32, 'parent_id' => 1, 'name' => 'Silica', 'concatenated_name' => 'Silica'),
            35 => array( 'id' => 35, 'parent_id' => 1, 'name' => 'Miscellaneous', 'concatenated_name' => 'Miscellaneous'),

            100 => array( 'id' => 100, 'parent_id' => NULL, 'name' => 'Composite', 'concatenated_name' => 'Composite'),

            110 => array( 'id' => 110, 'parent_id' => 100, 'name' => 'Clay Body', 'concatenated_name' => 'Clay Body'),
            120 => array( 'id' => 120, 'parent_id' => 110, 'name' => 'Earthenware', 'concatenated_name' => 'Earthenware'),
            130 => array( 'id' => 130, 'parent_id' => 120, 'name' => 'Throwing', 'concatenated_name' => 'Earthenware - Throwing'),
            140 => array( 'id' => 140, 'parent_id' => 120, 'name' => 'Slipcasting', 'concatenated_name' => 'Earthenware - Slipcasting'),
            150 => array( 'id' => 150, 'parent_id' => 120, 'name' => 'Sculpture', 'concatenated_name' => 'Earthenware - Sculpture'),
            160 => array( 'id' => 160, 'parent_id' => 120, 'name' => 'Hand-building', 'concatenated_name' => 'Earthenware - Hand-building'),
            170 => array( 'id' => 170, 'parent_id' => 120, 'name' => 'Salt', 'concatenated_name' => 'Earthenware - Salt'),
            180 => array( 'id' => 180, 'parent_id' => 120, 'name' => 'Raku', 'concatenated_name' => 'Earthenware - Raku'),
            190 => array( 'id' => 190, 'parent_id' => 110, 'name' => 'Stoneware', 'concatenated_name' => 'Stoneware'),
            200 => array( 'id' => 200, 'parent_id' => 190, 'name' => 'Throwing', 'concatenated_name' => 'Stoneware - Throwing'),
            210 => array( 'id' => 210, 'parent_id' => 190, 'name' => 'Slipcasting', 'concatenated_name' => 'Stoneware - Slipcasting'),
            220 => array( 'id' => 220, 'parent_id' => 190, 'name' => 'Sculpture', 'concatenated_name' => 'Stoneware - Sculpture'),
            230 => array( 'id' => 230, 'parent_id' => 190, 'name' => 'Hand-building', 'concatenated_name' => 'Stoneware - Hand-building'),
            240 => array( 'id' => 240, 'parent_id' => 190, 'name' => 'Salt', 'concatenated_name' => 'Stoneware - Salt'),
            250 => array( 'id' => 250, 'parent_id' => 190, 'name' => 'Raku', 'concatenated_name' => 'Stoneware - Raku'),
            260 => array( 'id' => 260, 'parent_id' => 110, 'name' => 'Porcelain', 'concatenated_name' => 'Porcelain'),
            270 => array( 'id' => 270, 'parent_id' => 260, 'name' => 'Throwing', 'concatenated_name' => 'Porcelain - Throwing'),
            280 => array( 'id' => 280, 'parent_id' => 260, 'name' => 'Slipcasting', 'concatenated_name' => 'Porcelain - Slipcasting'),
            290 => array( 'id' => 290, 'parent_id' => 260, 'name' => 'Sculpture', 'concatenated_name' => 'Porcelain - Sculpture'),
            300 => array( 'id' => 300, 'parent_id' => 260, 'name' => 'Hand-building', 'concatenated_name' => 'Porcelain - Hand-building'),
            310 => array( 'id' => 310, 'parent_id' => 260, 'name' => 'Salt', 'concatenated_name' => 'Porcelain - Salt'),
            320 => array( 'id' => 320, 'parent_id' => 260, 'name' => 'Raku', 'concatenated_name' => 'Porcelain - Raku'),
            330 => array( 'id' => 330, 'parent_id' => 110, 'name' => 'Flameware', 'concatenated_name' => 'Flameware'),
            340 => array( 'id' => 340, 'parent_id' => 330, 'name' => 'Throwing', 'concatenated_name' => 'Flameware - Throwing'),
            350 => array( 'id' => 350, 'parent_id' => 330, 'name' => 'Slipcasting', 'concatenated_name' => 'Flameware - Slipcasting'),
            360 => array( 'id' => 360, 'parent_id' => 330, 'name' => 'Sculpture', 'concatenated_name' => 'Flameware - Sculpture'),
            370 => array( 'id' => 370, 'parent_id' => 330, 'name' => 'Hand-building', 'concatenated_name' => 'Flameware - Hand-building'),
            380 => array( 'id' => 380, 'parent_id' => 330, 'name' => 'Salt', 'concatenated_name' => 'Flameware - Salt'),
            390 => array( 'id' => 390, 'parent_id' => 330, 'name' => 'Raku', 'concatenated_name' => 'Flameware - Raku'),

            400 => array( 'id' => 400, 'parent_id' => 100, 'name' => 'Slip & Engobe', 'concatenated_name' => 'Slip & Engobe'),
            410 => array( 'id' => 410, 'parent_id' => 400, 'name' => 'Slip', 'concatenated_name' => 'Slip'),
            420 => array( 'id' => 420, 'parent_id' => 400, 'name' => 'Engobe', 'concatenated_name' => 'Engobe'),
            430 => array( 'id' => 430, 'parent_id' => 400, 'name' => 'Terra Sigillata', 'concatenated_name' => 'Terra Sigillata'),

            440 => array( 'id' => 440, 'parent_id' => 100, 'name' => 'Overglaze', 'concatenated_name' => 'Overglaze'),

            450 => array( 'id' => 450, 'parent_id' => 100, 'name' => 'Underglaze', 'concatenated_name' => 'Underglaze'),

            460 => array( 'id' => 460, 'parent_id' => 100, 'name' => 'Glaze', 'concatenated_name' => 'Glaze'),

            470 => array( 'id' => 470, 'parent_id' => 460, 'name' => 'Clear', 'concatenated_name' => 'Clear'),
            480 => array( 'id' => 480, 'parent_id' => 460, 'name' => 'White, Off-White', 'concatenated_name' => 'White, Off-White'),
            490 => array( 'id' => 490, 'parent_id' => 460, 'name' => 'Iron', 'concatenated_name' => 'Iron'),
            500 => array( 'id' => 500, 'parent_id' => 490, 'name' => 'Celadon', 'concatenated_name' => 'Iron - Celadon'),
            510 => array( 'id' => 510, 'parent_id' => 500, 'name' => 'Blue', 'concatenated_name' => 'Iron - Celadon - Blue'),
            520 => array( 'id' => 520, 'parent_id' => 500, 'name' => 'Green', 'concatenated_name' => 'Iron - Celadon - Green'),
            530 => array( 'id' => 530, 'parent_id' => 500, 'name' => 'Yellow', 'concatenated_name' => 'Iron - Celadon - Yellow'),
            533 => array( 'id' => 533, 'parent_id' => 500, 'name' => 'Chun, Jun', 'concatenated_name' => 'Iron - Celadon - Chun'),
            535 => array( 'id' => 535, 'parent_id' => 490, 'name' => 'Amber', 'concatenated_name' => 'Iron - Amber'),
            540 => array( 'id' => 540, 'parent_id' => 490, 'name' => 'Tenmoku, Temmoku, Tianmu', 'concatenated_name' => 'Iron - Tenmoku'),
            550 => array( 'id' => 550, 'parent_id' => 490, 'name' => 'Tea Dust', 'concatenated_name' => 'Iron - Tea Dust'),
            560 => array( 'id' => 560, 'parent_id' => 490, 'name' => 'Tessha, Hare\'s Fur', 'concatenated_name' => 'Iron - Tessha, Hare\'s Fur'),
            570 => array( 'id' => 570, 'parent_id' => 490, 'name' => 'Kaki, Tomato Red', 'concatenated_name' => 'Iron - Kaki, Tomato Red'),
            580 => array( 'id' => 580, 'parent_id' => 490, 'name' => 'Oil Spot', 'concatenated_name' => 'Iron - Oil Spot'),
            585 => array( 'id' => 585, 'parent_id' => 490, 'name' => 'Slip-Based', 'concatenated_name' => 'Iron - Slip-Based'),
            590 => array( 'id' => 590, 'parent_id' => 460, 'name' => 'Shino', 'concatenated_name' => 'Shino'),
            600 => array( 'id' => 600, 'parent_id' => 590, 'name' => 'Traditional', 'concatenated_name' => 'Shino - Traditional'),
            610 => array( 'id' => 610, 'parent_id' => 590, 'name' => 'Carbon Trap', 'concatenated_name' => 'Shino - Carbon Trap'),
            620 => array( 'id' => 620, 'parent_id' => 590, 'name' => 'High-Alumina', 'concatenated_name' => 'Shino - High-Alumina'),
            630 => array( 'id' => 630, 'parent_id' => 590, 'name' => 'White', 'concatenated_name' => 'Shino - White'),
            635 => array( 'id' => 635, 'parent_id' => 460, 'name' => 'Red', 'concatenated_name' => 'Red'),
            640 => array( 'id' => 640, 'parent_id' => 635, 'name' => 'Copper', 'concatenated_name' => 'Red - Copper'),
            650 => array( 'id' => 650, 'parent_id' => 640, 'name' => 'Oxblood', 'concatenated_name' => 'Red - Copper - Oxblood'),
            660 => array( 'id' => 660, 'parent_id' => 640, 'name' => 'Flambe', 'concatenated_name' => 'Red - Copper - Flambe'),
            670 => array( 'id' => 670, 'parent_id' => 640, 'name' => 'Peach Bloom', 'concatenated_name' => 'Red - Copper - Peach Bloom'),
            673 => array( 'id' => 673, 'parent_id' => 635, 'name' => 'Pink', 'concatenated_name' => 'Red - Pink'),
            675 => array( 'id' => 675, 'parent_id' => 635, 'name' => 'Stain', 'concatenated_name' => 'Red - Stain'),
            680 => array( 'id' => 680, 'parent_id' => 460, 'name' => 'Green', 'concatenated_name' => 'Green'),
            690 => array( 'id' => 690, 'parent_id' => 680, 'name' => 'Copper', 'concatenated_name' => 'Green - Copper'),
            700 => array( 'id' => 700, 'parent_id' => 680, 'name' => 'Oribe', 'concatenated_name' => 'Green - Oribe'),
            710 => array( 'id' => 710, 'parent_id' => 680, 'name' => 'Chrome', 'concatenated_name' => 'Green - Chrome'),
            720 => array( 'id' => 720, 'parent_id' => 680, 'name' => 'Titanium', 'concatenated_name' => 'Green - Titanium'),
            730 => array( 'id' => 730, 'parent_id' => 680, 'name' => 'Nickel', 'concatenated_name' => 'Green - Nickel'),
            740 => array( 'id' => 740, 'parent_id' => 680, 'name' => 'Stain', 'concatenated_name' => 'Green - Stain'),
            745 => array( 'id' => 745, 'parent_id' => 460, 'name' => 'Turquoise', 'concatenated_name' => 'Turquoise'),
            750 => array( 'id' => 750, 'parent_id' => 460, 'name' => 'Blue', 'concatenated_name' => 'Blue'),
            760 => array( 'id' => 760, 'parent_id' => 750, 'name' => 'Cobalt', 'concatenated_name' => 'Blue - Cobalt'),
            770 => array( 'id' => 770, 'parent_id' => 750, 'name' => 'Rutile', 'concatenated_name' => 'Blue - Rutile'),
            780 => array( 'id' => 780, 'parent_id' => 750, 'name' => 'Barium', 'concatenated_name' => 'Blue - Barium'),
            790 => array( 'id' => 790, 'parent_id' => 750, 'name' => 'Strontium', 'concatenated_name' => 'Blue - Strontium'),
            800 => array( 'id' => 800, 'parent_id' => 750, 'name' => 'Nickel', 'concatenated_name' => 'Blue - Nickel'),
            810 => array( 'id' => 810, 'parent_id' => 750, 'name' => 'Stain', 'concatenated_name' => 'Blue - Stain'),
            820 => array( 'id' => 820, 'parent_id' => 460, 'name' => 'Purple', 'concatenated_name' => 'Purple'),
            830 => array( 'id' => 830, 'parent_id' => 820, 'name' => 'Magnesium Matte', 'concatenated_name' => 'Purple - Magnesium Matte'),
            840 => array( 'id' => 840, 'parent_id' => 820, 'name' => 'Nickel', 'concatenated_name' => 'Purple - Nickel'),
            850 => array( 'id' => 850, 'parent_id' => 820, 'name' => 'Manganese', 'concatenated_name' => 'Purple - Manganese'),
            860 => array( 'id' => 860, 'parent_id' => 460, 'name' => 'Matte', 'concatenated_name' => 'Matte'),
            870 => array( 'id' => 870, 'parent_id' => 860, 'name' => 'Magnesium Matte', 'concatenated_name' => 'Matte - Magnesium Matte'),
            880 => array( 'id' => 880, 'parent_id' => 460, 'name' => 'Black', 'concatenated_name' => 'Black'),
            890 => array( 'id' => 890, 'parent_id' => 880, 'name' => 'Slip-Based', 'concatenated_name' => 'Black - Slip-Based'),
            900 => array( 'id' => 900, 'parent_id' => 880, 'name' => 'Glossy', 'concatenated_name' => 'Black - Glossy'),
            910 => array( 'id' => 910, 'parent_id' => 880, 'name' => 'Satin', 'concatenated_name' => 'Black - Satin'),
            920 => array( 'id' => 920, 'parent_id' => 460, 'name' => 'Yellow', 'concatenated_name' => 'Yellow'),
            930 => array( 'id' => 930, 'parent_id' => 920, 'name' => 'Iron', 'concatenated_name' => 'Yellow - Iron'),
            940 => array( 'id' => 940, 'parent_id' => 920, 'name' => 'Barium', 'concatenated_name' => 'Yellow - Barium'),
            950 => array( 'id' => 950, 'parent_id' => 920, 'name' => 'Manganese', 'concatenated_name' => 'Yellow - Manganese'),
            960 => array( 'id' => 960, 'parent_id' => 920, 'name' => 'Stain', 'concatenated_name' => 'Yellow - Stain'),
            970 => array( 'id' => 970, 'parent_id' => 920, 'name' => 'Nickel', 'concatenated_name' => 'Yellow - Nickel'),
            980 => array( 'id' => 980, 'parent_id' => 460, 'name' => 'Crystalline', 'concatenated_name' => 'Crystalline'),
            990 => array( 'id' => 990, 'parent_id' => 980, 'name' => 'Micro', 'concatenated_name' => 'Crystalline - Micro'),
            1000 => array( 'id' => 1000, 'parent_id' => 980, 'name' => 'Aventurine', 'concatenated_name' => 'Crystalline - Aventurine'),
            1010 => array( 'id' => 1010, 'parent_id' => 980, 'name' => 'Manganese', 'concatenated_name' => 'Crystalline - Manganese'),
            1020 => array( 'id' => 1020, 'parent_id' => 980, 'name' => 'Macro', 'concatenated_name' => 'Crystalline - Macro'),
            1030 => array( 'id' => 1030, 'parent_id' => 460, 'name' => 'Single-Fire', 'concatenated_name' => 'Single-Fire'),
            1040 => array( 'id' => 1040, 'parent_id' => 460, 'name' => 'Wood', 'concatenated_name' => 'Wood'),
            1050 => array( 'id' => 1050, 'parent_id' => 460, 'name' => 'Salt & Soda', 'concatenated_name' => 'Salt & Soda'),
            1055 => array( 'id' => 1055, 'parent_id' => 460, 'name' => 'Raku', 'concatenated_name' => 'Raku'),
            1060 => array( 'id' => 1060, 'parent_id' => 460, 'name' => 'Ash', 'concatenated_name' => 'Ash'),
            1070 => array( 'id' => 1070, 'parent_id' => 1060, 'name' => 'Nuka', 'concatenated_name' => 'Ash - Nuka'),
            1080 => array( 'id' => 1080, 'parent_id' => 1060, 'name' => 'Synthetic/Fake', 'concatenated_name' => 'Ash - Synthetic/Fake'),
            1090 => array( 'id' => 1090, 'parent_id' => 1060, 'name' => 'Slip-Based', 'concatenated_name' => 'Ash - Slip-Based'),
            1100 => array( 'id' => 1100, 'parent_id' => 460, 'name' => 'Majolica', 'concatenated_name' => 'Majolica'),
            1130 => array( 'id' => 1130, 'parent_id' => 460, 'name' => 'Specialty', 'concatenated_name' => 'Specialty'),
            1140 => array( 'id' => 1140, 'parent_id' => 1130, 'name' => 'Crackle', 'concatenated_name' => 'Specialty - Crackle'),
            1150 => array( 'id' => 1150, 'parent_id' => 1130, 'name' => 'Crawling', 'concatenated_name' => 'Specialty - Crawling'),
            1160 => array( 'id' => 1160, 'parent_id' => 1130, 'name' => 'Crater', 'concatenated_name' => 'Specialty - Crater'),
            1170 => array( 'id' => 1170, 'parent_id' => 1130, 'name' => 'Metallic', 'concatenated_name' => 'Specialty - Metallic'),

            1180 => array( 'id' => 1180, 'parent_id' => 100, 'name' => 'Refractory', 'concatenated_name' => 'Refractory'),
		];
	}

    public function getLineageIdNameArrayOld($id)
    {
        // Normally would use recursion but this is a small fixed-level tree.
        if (!isset($this->static_values[$id]))
        {
            return null;
        }
        $lineage = array();
        $lineage[] = array(
            $id,
            $this->static_values[$id]['name'],
            $this->static_values[$id]['concatenated_name']
        );
        $parent_id = $this->static_values[$id]['parent_id'];
        if ($parent_id != null)
        {
            $lineage[] = array(
                $parent_id,
                $this->static_values[$parent_id]['name'],
                $this->static_values[$parent_id]['concatenated_name']
            );
            $parent_id = $this->static_values[$parent_id]['parent_id'];
            if ($parent_id != null)
            {
                $lineage[] = array(
                    $parent_id,
                    $this->static_values[$parent_id]['name'],
                    $this->static_values[$parent_id]['concatenated_name']
                );
                $parent_id = $this->static_values[$parent_id]['parent_id'];
                if ($parent_id != null)
                {
                    $lineage[] = array(
                        $parent_id,
                        $this->static_values[$parent_id]['name'],
                        $this->static_values[$parent_id]['concatenated_name']
                    );
                }
            }
        }
        return $lineage;
    }

    public function getLineageIdNameArray($id)
    {
        if (!isset($this->static_values[$id]))
        {
            return null;
        }
        $lineage = array();
        $lineage['id'] = $id;
        $lineage['name'] = $this->static_values[$id]['name'];
        $lineage['concatenatedName'] = $this->static_values[$id]['concatenated_name'];
        if ($this->static_values[$id]['parent_id'])
        {
            $lineage['parentMaterialType'] = $this->getLineageIdNameArray($this->static_values[$id]['parent_id']);
        }
        return $lineage;
    }

    public function getSimpleLineageIdNameArray($id)
    {
        if (!isset($this->static_values[$id]))
        {
            return null;
        }
        $lineage = array();
        $lineage['id'] = $id;
        if ($this->static_values[$id]['parent_id'])
        {
            $lineage['parentMaterialType'] = $this->getSimpleLineageIdNameArray($this->static_values[$id]['parent_id']);
        }
        return $lineage;
    }

    public function getLineageIdNameArrayFlat($id)
    {
        if (!isset($this->static_values[$id]))
        {
            return;
        }

        $lineage = array();
        $material_type = array();
        $material_type['id'] = $id;
        $material_type['name'] = $this->static_values[$id]['name'];
        $material_type['concatenated_name'] = $this->static_values[$id]['concatenated_name'];
        $lineage[] = $material_type;

        if ($this->static_values[$id]['parent_id'])
        {
            $lineage = array_merge($this->getLineageIdNameArrayFlat($this->static_values[$id]['parent_id']), $lineage);
        }
        return $lineage;
    }

    public function getBaseType($id)
    {
        if (isset($this->static_values[$id]))
        {
            // TODO:  get rid of these hard-coded values, too dangerous
            if ($id > 0 && $id < 36) {
                // This is a primitive type.  Primitive types only have one level
                return $id;
            }

            $composite_types = [ 110, 400, 440, 450, 460, 1180];

            if (in_array($id, $composite_types))
            {
                return $id;
            }
            elseif (!empty($this->static_values[$id]['parent_id']))
            {
                return $this->getBaseType($this->static_values[$id]['parent_id']);
            }
        }

        return null;
    }

    public function getChildrenCategoriesArray($id)
    {
        $childrenCatIds = [];
        foreach ( $this->static_values as $child_id => $child) {
            if ($child['parent_id'] == $id) {
                // This is a child of the argument id
                $childrenCatIds[] = $child_id;
                // Check if child also has children
                $grandchildrenIds = $this->getChildrenCategoriesArray($child_id);
                if (count($grandchildrenIds)) {
                    $childrenCatIds = array_merge($childrenCatIds, $grandchildrenIds);
                }
            }
        }
        return $childrenCatIds;
    }

    public function getChildrenCategoriesArrayIncludingSelf($id)
    {
        $childrenCatIds = $this->getChildrenCategoriesArray($id);
        $childrenCatIds[] = $id;
        return $childrenCatIds;
    }


    /**
     * @return array
     *
     * Recursively get all categories in parent/child tree.  Assumes no loops.
     */
    /*
    protected function getSubcategoriesArray($parent_id, &$children)
    {
        foreach ( $this->$flat as $child_id => $child)
        {
            if ($child['parent_id'] == $parent_id)
            {
                if ($children == null)
                {
                    $children = array();
                }
                $children[$child_id] = $child['concatenated_name'];
                $this->getSubcategoriesArray($child_id, $children);
            }
        }
    }
    */

}

