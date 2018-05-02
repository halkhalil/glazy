<?php

namespace App\Api\V1\Transformers\Material;

use App\Models\Material;
use DateTime;
use App\Models\MaterialType;
use App\Models\OrtonCone;
use DerekPhilipAu\Ceramicscalc\Models\Analysis\Analysis;

/**
 * Created by PhpStorm.
 * User: dau
 * Date: 5/2/18
 * Time: 09:42
 */

class InsightMaterialTransformer
{
    public static function transform(Material $material)
    {
        if ($material->is_primitive || $material->is_analysis) {
            return self::transformSimpleMaterial($material);
        }
        else {
            return self::transformCompositeMaterial($material);
        }
    }

    public static function transformSimpleMaterial(Material $material)
    {
        $url_type = "materials";
        if ($material->is_analysis) {
            $url_type = "analyses";
        }

        $out = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $out .= "<material name=\"".strip_tags($material->name).' (Glazy ID '.$material->id.')" ';
        if ($material->description) {
            $out .= 'descrip="'.substr(preg_replace('/[^a-zA-Z0-9\s]/', '', strip_tags($material->description)),0,250).'" ';
        }
        if ($material->analysis && $material->analysis['loi']) {
            $out .= 'loi="'.(float)$material->analysis['loi'].'" ';
        }
        $out .= '>'.PHP_EOL;

        if ($material->analysis) {
            $oxides = '';
            foreach (Analysis::OXIDE_NAMES as $oxide_name) {
                $percent_oxide_name = $oxide_name . '_percent';
                if ($material->analysis[$percent_oxide_name] > 0) {
                    $oxides .= "\t\t<oxide symbol=\"".$oxide_name."\" percent=\"".$material->analysis[$percent_oxide_name]."\" tolerance=\"\"/>".PHP_EOL;
                }
            }

            if (strlen($oxides)) {
                $out .= "\t<oxides>".PHP_EOL;
                $out .= $oxides;
                $out .= "\t</oxides>".PHP_EOL;
            }

            if ($material->analysis['loi']) {
                $out .= "\t<volatiles>".PHP_EOL;
                $out .= "\t\t<volatile symbol=\"LOI\" name=\"Loss on Ignition\" percent=\"".$material->analysis['loi']."\" tolerance=\"\" />".PHP_EOL;
                $out .= "\t</volatiles>".PHP_EOL;
            }
        }
        $out .= "</material>".PHP_EOL;

        return $out;
    }

    public static function transformCompositeMaterial(Material $material)
    {
        $oDate = new DateTime($material->created_at);
        $date = $oDate->format("Y-m-d");

        $total_percentage_amount = 0;

        $url_type = "recipes";
        if ($material->is_primitive) {
            $url_type = "materials";
        }
        elseif ($material->is_analysis) {
            $url_type = "analyses";
        }

        $recipelines = '';
        if (isset($material->components)) {
            foreach ($material->components as $materialComponent)
            {
                $is_additional = "false";
                if ($materialComponent->is_additional) {
                    $is_additional = "true";
                }

                $insight_name = $materialComponent->component_material->name;
                if (!empty($materialComponent->component_material->insight_name)) {
                    $insight_name = $materialComponent->component_material->insight_name;
                }
                else if (!empty($materialComponent->component_material->short_name)) {
                    $insight_name = $materialComponent->component_material->short_name;
                }

                $recipelines .= "\t\t\t".'<recipeline material="'.strip_tags($insight_name).'" ';
                $recipelines .= 'unitabbr="g" conversion="1.0000" ';
                $recipelines .= 'amount="'.(float)$materialComponent->percentage_amount.'" ';
                $recipelines .= 'added="'.$is_additional.'"/>'.PHP_EOL;

                $total_percentage_amount += $materialComponent->percentage_amount;
            }
        }

        $out = '<?xml version="1.0"?>'.PHP_EOL;
        $out .= '<recipes version="1.0" encoding="UTF-8">'.PHP_EOL;
        $out .= "\t<recipe name=\"".strip_tags($material->name).' (Glazy ID '.$material->id.')" date="'.$date.'" ';
        $out .= 'id="'.$material->id.'" ';
        if ($material->code) {
            $out .= 'codenum="'.$material->code.'" ';
        }
        if ($material->description) {
            $out .= 'keywords="'.strip_tags($material->description).'" ';
        }
        $out .= 'batchsize="'.(float)$total_percentage_amount.'" ';
        $out .= 'url="https://glazy.org/'.$url_type.'/'.$material->id.'">'.PHP_EOL;
        $out .= "\t\t<recipelines>".PHP_EOL;
        $out .= $recipelines;
        // Not sure why Digitalfire is putting URL information inside recipelines tag?
        $out .= "\t\t\t<url url=\"https://glazy.org/".$url_type.'/'.$material->id.'" descrip="Recipe page on glazy.org"/>'.PHP_EOL;
        $out .= "\t\t</recipelines>".PHP_EOL;
        if ($material->description) {
            $out .= '<notes>' . strip_tags($material->description) . "</notes>" . PHP_EOL;
        }
        $out .= "\t\t<urls>".PHP_EOL;
        $out .= "\t\t\t<url url=\"https://glazy.org/".$url_type.'/'.$material->id.'" descrip="Recipe page on glazy.org"/>'.PHP_EOL;
        $out .= "\t\t</urls>".PHP_EOL;
        $out .= "\t</recipe>".PHP_EOL;
        $out .= "</recipes>".PHP_EOL;

        return $out;
    }
}


