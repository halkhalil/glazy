<?php

namespace App\Api\V1\Transformers\Material;

use App\Models\Material;
use DateTime;
use App\Models\MaterialType;
use App\Models\OrtonCone;

/**
 * Created by PhpStorm.
 * User: dau
 * Date: 5/2/18
 * Time: 09:42
 */

class GlazeChemMaterialTransformer
{
    public static function transform(Material $material)
    {
        $out = "name   = ".strip_tags($material->name)."\n";
        $out .= "index  = \n";
        $oDate = new DateTime($material->created_at);
        $out .= "date   = ".$oDate->format("m/d/Y")."\n";
        $out .= "source = https://www.glazy.org\n";
        $type = '';

        $materialType = new MaterialType();

        if ($material->material_type_id)
        {
            $type = $materialType->getValue($material->material_type_id);
            if ($type && isset($type['concatenated_name'])) {
                $out .= "type   = ".$type['concatenated_name']."\n";
            }
        }

        $range = '';

        $cone = new OrtonCone();

        $from_orton_cone = $cone->getValue($material->from_orton_cone_id);
        $to_orton_cone = $cone->getValue($material->to_orton_cone_id);

        if (!empty($from_orton_cone))
        {
            if (!empty($to_orton_cone))
            {
                if ($from_orton_cone === $to_orton_cone)
                {
                    $range = $from_orton_cone;
                }
                else
                {
                    $range = $from_orton_cone.'-'.$to_orton_cone;
                }
            }
            else
            {
                $range = $from_orton_cone;
            }
        }
        elseif (!empty($to_orton_cone))
        {
            $range = $to_orton_cone;
        }
        //$range = str_replace('05&#189;', '05 1/2', $range);
        $out .= "range  = ".$range."\n";

        $firetype = '';
        if ($material->atmospheres && count($material->atmospheres))
        {
            $hasFiretype = false;
            foreach ($material->atmospheres as $atmosphere)
            {
                if ($hasFiretype)
                {
                    $firetype .= ', ';
                }
                $firetype .= $atmosphere->name;

                $hasFiretype = true;
            }
        }
        $out .= "firetype     = ".$firetype."\n";

        $out .= "color        = ".strip_tags($material->color_name)."\n";

        $out .= "vistexture   = \n";

        if (isset($material->surface_type) && isset($material->surface_type->name))
        {
            $out .= "quality      = ".$material->surface_type->name."\n";
        }
        else
        {
            $out .= "quality      = \n";
        }

        if (isset($material->transparency_type) && isset($material->transparency_type->name))
        {
            $out .= "transparency = ".$material->transparency_type->name."\n";
        }
        else
        {
            $out .= "transparency = \n";
        }
        $out .= "xtals        = \n";
        $out .= "bubbles      = \n";
        $out .= "flow         = \n";
        $out .= "durability   = \n";
        $out .= "flaws     = \n";
        $out .= "tested    = \n";
        $out .= "imagefile = \n";
        $out .= "notefile  = \n";
        $out .= "limform   = \n";
        $out .= "by_vol    = n\n";

        $materials = '';
        $total_percentage_amount = 0;

        if (isset($material->components)) {
            foreach ($material->components as $materialComponent)
            {
                if ($materialComponent->is_additional) {
                    $materials .= "addition  = ";
                }
                else {
                    $materials .= "component = ";
                }

                $materials .= $materialComponent->component_material->name."\n";

                if ($materialComponent->is_additional) {
                    $materials .= "addamount = ";
                }
                else
                {
                    $materials .= "amount    = ";
                }
                $materials .= (float)$materialComponent->percentage_amount."\n";
                $total_percentage_amount += $materialComponent->percentage_amount;
            }
            $out .= "batchsize = ".(float)$total_percentage_amount."\n";
            $out .= $materials;
        }

        $notes = '';
        $note = '';
        if (!empty($material->description))
        {
            $desc = strip_tags($material->description);
            $keywords = preg_split("/\s+/", $desc);

            foreach ($keywords as $keyword)
            {
                if (strlen($keyword) + strlen($note) < 72)
                {
                    $note .= " ".$keyword;
                }
                else
                {
                    $notes .= "note = |".$note."\n";
                    $note = " ".$keyword;
                }
            }
        }
        if (strlen($note) > 0)
        {
            $notes .= "note = |".$note."\n";
        }
        $out .= $notes;
        return $out;
    }
}
