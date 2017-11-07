<?php namespace App\Models\Glazy\Material;

use DerekPhilipAu\Ceramicscalc\Models\Analysis\FormulaAnalysis;
use Illuminate\Database\Eloquent\Model;

use DerekPhilipAu\Ceramicscalc\Models\Analysis\PercentageAnalysis;
use DerekPhilipAu\Ceramicscalc\Models\Analysis\Analysis;
use DerekPhilipAu\Ceramicscalc\Models\Material\AbstractMaterial;
use Illuminate\Support\Facades\DB;

/**
 * Class HistoryType
 * package App
 */
class MaterialAnalysis extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'material_analyses';

	public function material()
	{
		return $this->belongsTo('App\Models\Glazy\Material\Material');
	}

	public function setAnalysis(AbstractMaterial $material)
	{
		$percentageAnalysis = $material->getPercentageAnalysis();
		$formulaAnalysis = $material->getFormulaAnalysis();
		$umfAnalysis = $material->getUmfAnalysis();
		$molePercentageAnalysis = FormulaAnalysis::createMolePercentageFormula($formulaAnalysis);

		foreach(Analysis::OXIDE_NAMES as $oxide_name)
		{
			$value = $percentageAnalysis->getOxide($oxide_name);
			$property_name = $oxide_name.'_percent';
			$this->$property_name = $value;

			$value = $formulaAnalysis->getOxide($oxide_name);
			$property_name = $oxide_name.'_mol';
			$this->$property_name = $value;

			$value = $molePercentageAnalysis->getOxide($oxide_name);
			$property_name = $oxide_name.'_percent_mol';
			$this->$property_name = $value;

            $value = $umfAnalysis->getOxide($oxide_name);
            $property_name = $oxide_name.'_umf';
            $this->$property_name = $value;
		}

		$this->KNaO_percent = $percentageAnalysis->getKNaO();

        $this->loi = $percentageAnalysis->getLOI();

        $this->KNaO_mol = $formulaAnalysis->getKNaO();

		$this->KNaO_percent_mol = $molePercentageAnalysis->getKNaO();

        $this->KNaO_umf = $umfAnalysis->getKNaO();

        $this->SiO2_Al2O3_ratio_umf = $umfAnalysis->getSiO2Al2O3Ratio();

        $this->SiO2_Al2O3 = DB::raw(
            'GeometryFromText( \'POINT('
            .$umfAnalysis->getOxide(FormulaAnalysis::SiO2)
            .' '
            .$umfAnalysis->getOxide(FormulaAnalysis::Al2O3)
            .')\' )');

        $this->R2O_umf = $umfAnalysis->getR2OTotal();
        $this->RO_umf = $umfAnalysis->getROTotal();

	}

}