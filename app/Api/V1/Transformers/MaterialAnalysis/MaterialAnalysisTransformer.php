<?php

namespace App\Api\V1\Transformers\MaterialAnalysis;

use DerekPhilipAu\Ceramicscalc\Models\Analysis\Analysis;

use League\Fractal;

class MaterialAnalysisTransformer extends Fractal\TransformerAbstract
{
    public function transform($analysis)
    {
        $analysis_data = array();

        if ($analysis)
        {
            $percent_analysis = [];
            foreach(Analysis::OXIDE_NAMES as $oxide_name)
            {
                $percent_oxide_name = $oxide_name.'_percent';

                if ($analysis[$percent_oxide_name] > 0)
                {
                    $percent_analysis[$oxide_name] = $analysis[$percent_oxide_name];
                }
            }
            $percent_analysis['loi'] = $analysis['loi'];
            $analysis_data['percentageAnalysis'] = $percent_analysis;

            /*
            $mol_percent_analysis = [];
            foreach(Analysis::OXIDE_NAMES as $oxide_name)
            {
                $mol_percent_oxide_name = $oxide_name.'_percent_mol';

                if ($analysis[$mol_percent_oxide_name] > 0)
                {
                    $mol_percent_analysis[$oxide_name] = $analysis[$mol_percent_oxide_name];
                }
            }
            $analysis_data['molPercentageAnalysis'] = $mol_percent_analysis;
            */

            $umf_analysis = [];
            foreach(Analysis::OXIDE_NAMES as $oxide_name)
            {
                $umf_oxide_name = $oxide_name.'_umf';

                if ($analysis[$umf_oxide_name] > 0)
                {
                    $umf_analysis[$oxide_name] = $analysis[$umf_oxide_name];
                }
            }
            $umf_analysis['SiO2Al2O3Ratio'] = $analysis['SiO2_Al2O3_ratio_umf'];
            $umf_analysis['R2OTotal'] = $analysis['R2O_umf'];
            $umf_analysis['ROTotal'] = $analysis['RO_umf'];
            $analysis_data['umfAnalysis'] = $umf_analysis;

            $analysis_data['weight'] = $analysis['weight'];

        }

        return $analysis_data;
    }
}