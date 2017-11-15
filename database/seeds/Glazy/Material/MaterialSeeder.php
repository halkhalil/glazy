<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use DerekAu\Ceramicscalc\Models\Material\PrimitiveMaterial;
use DerekAu\Ceramicscalc\Models\Analysis\Analysis;
use DerekAu\Ceramicscalc\Models\Analysis\PercentageAnalysis;
use DerekAu\Ceramicscalc\Models\Material\CompositeMaterial;

use App\Models\Glazy\Material\Material;
use App\Models\Glazy\Material\MaterialAnalysis;
use App\Models\Glazy\Material\MaterialMaterial;
use App\Models\Glazy\Material\Atmosphere;
use App\Models\Glazy\Material\MaterialAtmosphere;
use App\Models\Glazy\Material\OrtonCone;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DB::connection()->getDriverName() == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        DB::table('materials')->truncate();

        /***************************************
         * Silica
         ***************************************/
        $material = new Material();
        $material->id = 1;
        $material->old_id = 400;
        $material->name = 'Silica';
        $material->other_names = 'Quartz, Flint';
        $material->material_type_id = 32;
        $material->is_primitive = true;
        $material->is_private = false;
        $material->created_by_user_id = 1;
        $material->save();

        $silica = new PrimitiveMaterial($material->id);
        $percent = new PercentageAnalysis();
        $percent->setOxide(Analysis::SiO2, 100);
        $silica->setPercentageAnalysis($percent);

        $materialAnalysis = new MaterialAnalysis();
        $materialAnalysis->id = $material->id;
        $materialAnalysis->material_id = $material->id;
        $materialAnalysis->setAnalysis($silica);
        $materialAnalysis->save();

        /***************************************
         * Potash Feldspar
         ***************************************/
        $material = new Material();
        $material->id = 2;
        $material->old_id = 371;
        $material->name = 'Potash Feldspar';
        $material->other_names = 'Potassium Feldspar';
        $material->material_type_id = 20;
        $material->is_primitive = true;
        $material->is_private = false;
        $material->created_by_user_id = 1;
        $material->save();

        $potash = new PrimitiveMaterial($material->id);
        $percent = new PercentageAnalysis();
        $percent->setOxide(Analysis::SiO2, 64.76);
        $percent->setOxide(Analysis::Al2O3, 18.32);
        $percent->setOxide(Analysis::K2O, 16.92);
        $potash->setPercentageAnalysis($percent);

        $materialAnalysis = new MaterialAnalysis();
        $materialAnalysis->id = $material->id;
        $materialAnalysis->material_id = $material->id;
        $materialAnalysis->setAnalysis($potash);
        $materialAnalysis->save();

        /***************************************
         * G200 HP
         ***************************************/
        $material = new Material();
        $material->id = 10;
        $material->old_id = 371;
        $material->name = 'G200 HP';
        $material->other_names = 'G200 HP Feldspar';
        $material->material_type_id = 20;
        $material->is_primitive = true;
        $material->is_private = false;
        $material->created_by_user_id = 1;
        $material->save();

        $g200 = new PrimitiveMaterial($material->id);
        $percent = new PercentageAnalysis();
        $percent->setOxide(Analysis::SiO2, 66.00);
        $percent->setOxide(Analysis::Al2O3, 18.30);
        $percent->setOxide(Analysis::CaO, 0.3);
        $percent->setOxide(Analysis::K2O, 14.2);
        $percent->setOxide(Analysis::Na2O, 0.9);
        $percent->setOxide(Analysis::Fe2O3, 0.09);
        $g200->setPercentageAnalysis($percent);

        $materialAnalysis = new MaterialAnalysis();
        $materialAnalysis->id = $material->id;
        $materialAnalysis->material_id = $material->id;
        $materialAnalysis->setAnalysis($g200);
        $materialAnalysis->save();


        /***************************************
         * EPK
         ***************************************/
        $material = new Material();
        $material->id = 3;
        $material->old_id = 137;
        $material->name = 'EPK';
        $material->other_names = 'EP Kaolin, Edgar Plastic';
        $material->material_type_id = 13;
        $material->is_primitive = true;
        $material->is_private = false;
        $material->created_by_user_id = 1;
        $material->save();

        $epk = new PrimitiveMaterial($material->id);
        $percent = new PercentageAnalysis();
        $percent->setOxide(Analysis::SiO2, 45.73);
        $percent->setOxide(Analysis::Al2O3, 37.36);
        $percent->setOxide(Analysis::MgO, 0.10);
        $percent->setOxide(Analysis::CaO, 0.18);
        $percent->setOxide(Analysis::K2O, 0.35);
        $percent->setOxide(Analysis::Na2O, 0.07);
        $percent->setOxide(Analysis::P2O5, 0.26);
        $percent->setOxide(Analysis::Fe2O3, 0.76);
        $percent->setOxide(Analysis::TiO2, 0.38);
        $epk->setPercentageAnalysis($percent);

        $materialAnalysis = new MaterialAnalysis();
        $materialAnalysis->id = $material->id;
        $materialAnalysis->material_id = $material->id;
        $materialAnalysis->setAnalysis($epk);
        $materialAnalysis->save();

        /***************************************
         * Grolleg Kaolin
         ***************************************/
        $material = new Material();
        $material->id = 5;
        $material->old_id = 268;
        $material->name = 'Grolleg Kaolin';
        $material->material_type_id = 13;
        $material->is_primitive = true;
        $material->is_private = false;
        $material->created_by_user_id = 1;
        $material->save();

        $grolleg = new PrimitiveMaterial($material->id);
        $percent = new PercentageAnalysis();
        $percent->setOxide(Analysis::SiO2, 47.92);
        $percent->setOxide(Analysis::Al2O3, 36.94);
        $percent->setOxide(Analysis::MgO, 0.30);
        $percent->setOxide(Analysis::CaO, 0.10);
        $percent->setOxide(Analysis::K2O, 1.90);
        $percent->setOxide(Analysis::Na2O, 0.10);
        $percent->setOxide(Analysis::Fe2O3, 0.70);
        $percent->setOxide(Analysis::TiO2, 0.3);
        $grolleg->setPercentageAnalysis($percent);

        $materialAnalysis = new MaterialAnalysis();
        $materialAnalysis->id = $material->id;
        $materialAnalysis->material_id = $material->id;
        $materialAnalysis->setAnalysis($grolleg);
        $materialAnalysis->save();


        /***************************************
         * Wollastonite
         ***************************************/
        $material = new Material();
        $material->id = 7;
        $material->old_id = 458;
        $material->name = 'Wollastonite';
        $material->material_type_id = 32;
        $material->is_primitive = true;
        $material->is_private = false;
        $material->created_by_user_id = 1;
        $material->save();

        $wollastonite = new PrimitiveMaterial($material->id);
        $percent = new PercentageAnalysis();
        $percent->setOxide(Analysis::SiO2, 51.72);
        $percent->setOxide(Analysis::CaO, 48.28);
        $wollastonite->setPercentageAnalysis($percent);

        $materialAnalysis = new MaterialAnalysis();
        $materialAnalysis->id = $material->id;
        $materialAnalysis->material_id = $material->id;
        $materialAnalysis->setAnalysis($wollastonite);
        $materialAnalysis->save();

        /***************************************
         * WHITING
         ***************************************/
        $material = new Material();
        $material->id = 8;
        $material->old_id = 457;
        $material->name = 'Whiting';
        $material->other_names = 'Calcium Carbonate';
        $material->material_type_id = 23;
        $material->is_primitive = true;
        $material->is_private = false;
        $material->created_by_user_id = 1;
        $material->save();

        $whiting = new PrimitiveMaterial($material->id);
        $percent = new PercentageAnalysis();
        $percent->setOxide(Analysis::CaO, 56.10);
        $whiting->setPercentageAnalysis($percent);

        $materialAnalysis = new MaterialAnalysis();
        $materialAnalysis->id = $material->id;
        $materialAnalysis->material_id = $material->id;
        $materialAnalysis->setAnalysis($whiting);
        $materialAnalysis->save();


        /***************************************
         * Ferro Frit 3124
         ***************************************/
        $material = new Material();
        $material->id = 9;
        $material->old_id = 457;
        $material->name = 'Ferro Frit 3124';
        $material->material_type_id = 23;
        $material->is_primitive = true;
        $material->is_private = false;
        $material->created_by_user_id = 1;
        $material->save();

        $f3124 = new PrimitiveMaterial($material->id);
        $percent = new PercentageAnalysis();
        $percent->setOxide(Analysis::SiO2, 55.3);
        $percent->setOxide(Analysis::Al2O3, 9.9);
        $percent->setOxide(Analysis::B2O3, 13.77);
        $percent->setOxide(Analysis::CaO, 14.07);
        $percent->setOxide(Analysis::K2O, 0.68);
        $percent->setOxide(Analysis::Na2O, 6.28);
        $f3124->setPercentageAnalysis($percent);

        $materialAnalysis = new MaterialAnalysis();
        $materialAnalysis->id = $material->id;
        $materialAnalysis->material_id = $material->id;
        $materialAnalysis->setAnalysis($f3124);
        $materialAnalysis->save();





        /***************************************
         * Cobalt Carbonate
         ***************************************/
        $material = new Material();
        $material->id = 6;
        $material->old_id = 114;
        $material->name = 'Cobalt Carbonate';
        $material->material_type_id = 17;
        $material->is_primitive = true;
        $material->is_private = false;
        $material->created_by_user_id = 1;
        $material->save();

        $cobaltcarb = new PrimitiveMaterial($material->id);
        $percent = new PercentageAnalysis();
        $percent->setOxide(Analysis::CoO, 63.00);
        $cobaltcarb->setPercentageAnalysis($percent);

        $materialAnalysis = new MaterialAnalysis();
        $materialAnalysis->id = $material->id;
        $materialAnalysis->material_id = $material->id;
        $materialAnalysis->setAnalysis($cobaltcarb);
        $materialAnalysis->save();

        /***************************************
         * Red Iron Oxide
         ***************************************/
        $material = new Material();
        $material->id = 4;
        $material->old_id = 387;
        $material->name = 'Red iron oxide';
        $material->other_names = 'RIO';
        $material->material_type_id = 17;
        $material->is_primitive = true;
        $material->is_private = false;
        $material->created_by_user_id = 1;
        $material->save();

        $rio = new PrimitiveMaterial($material->id);
        $percent = new PercentageAnalysis();
        $percent->setOxide(Analysis::Fe2O3, 95);
        $rio->setPercentageAnalysis($percent);

        $materialAnalysis = new MaterialAnalysis();
        $materialAnalysis->id = $material->id;
        $materialAnalysis->material_id = $material->id;
        $materialAnalysis->setAnalysis($rio);
        $materialAnalysis->save();



        /***************************************
         * Insert Recipes
         ***************************************/
        $atmosphere = new Atmosphere();
        $ortonCone = new OrtonCone();


        /***************************************
         * RECIPE: Leach 4321
         ***************************************/
        $material = new Material();
        $material->id = 100;
        $material->old_id = null;
        $material->name = 'Leach 4321';
        $material->material_type_id = 470;
        $material->from_orton_cone_id = $ortonCone->getId('9');
        $material->to_orton_cone_id = $ortonCone->getId('10');

        $material->is_primitive = false;
        $material->is_private = false;
        $material->created_by_user_id = 1;
        $material->save();

        $glaze = new CompositeMaterial();
        $glaze->setName($material->name);
        $glaze->addMaterial($potash, 4);
        $glaze->addMaterial($silica, 3);
        $glaze->addMaterial($whiting, 2);
        $glaze->addMaterial($epk, 1);

        $materialAnalysis = new MaterialAnalysis();
        $materialAnalysis->id = $material->id;
        $materialAnalysis->material_id = $material->id;
        $materialAnalysis->setAnalysis($glaze);
        $materialAnalysis->save();

        foreach ($glaze->getMaterialComponents() as $materialComponent)
        {
            $recipeMaterial = new MaterialMaterial();
            $recipeMaterial->setMaterialComponent($material->id, $materialComponent);
            $recipeMaterial->save();
        }

        $materialAtmosphere = new MaterialAtmosphere();
        $materialAtmosphere->material_id = $material->id;
        $materialAtmosphere->atmosphere_id = $atmosphere->getId('Reduction');
        $materialAtmosphere->save();

        $materialAtmosphere = new MaterialAtmosphere();
        $materialAtmosphere->material_id = $material->id;
        $materialAtmosphere->atmosphere_id = $atmosphere->getId('Oxidation');
        $materialAtmosphere->save();


        /***************************************
         * RECIPE: Pinnell Clear
         ***************************************/
        $material = new Material();
        $material->id = 101;
        $material->old_id = null;
        $material->name = 'Pinnell Clear';
        $material->material_type_id = 470;
        $material->from_orton_cone_id = $ortonCone->getId('10');
        $material->to_orton_cone_id = $ortonCone->getId('11');
        $material->is_primitive = false;
        $material->is_private = false;
        $material->created_by_user_id = 1;
        $material->save();

        $glaze = new CompositeMaterial();
        $glaze->setName($material->name);
        $glaze->addMaterial($potash, 25);
        $glaze->addMaterial($silica, 35);
        $glaze->addMaterial($whiting, 20);
        $glaze->addMaterial($epk, 20);

        $materialAnalysis = new MaterialAnalysis();
        $materialAnalysis->id = $material->id;
        $materialAnalysis->material_id = $material->id;
        $materialAnalysis->setAnalysis($glaze);
        $materialAnalysis->save();

        foreach ($glaze->getMaterialComponents() as $materialComponent)
        {
            $recipeMaterial = new MaterialMaterial();
            $recipeMaterial->setMaterialComponent($material->id, $materialComponent);
            $recipeMaterial->save();
        }

        $materialAtmosphere = new MaterialAtmosphere();
        $materialAtmosphere->material_id = $material->id;
        $materialAtmosphere->atmosphere_id = $atmosphere->getId('Reduction');
        $materialAtmosphere->save();


        /***************************************
         * RECIPE: Leach 4321 Celadon
         ***************************************/
        $material = new Material();
        $material->id = 102;
        $material->old_id = null;
        $material->name = 'Leach 4321 Celadon';
        $material->material_type_id = 500;
        $material->from_orton_cone_id = $ortonCone->getId('9');
        $material->to_orton_cone_id = $ortonCone->getId('10');

        $material->is_primitive = false;
        $material->is_private = false;
        $material->created_by_user_id = 1;
        $material->save();

        $glaze = new CompositeMaterial();
        $glaze->setName($material->name);
        $glaze->addMaterial($silica, 30);
        $glaze->addAdditionalMaterial($rio, 2);
        $glaze->addMaterial($potash, 40);
        $glaze->addMaterial($epk, 10);
        $glaze->addMaterial($whiting, 20);

        $materialAnalysis = new MaterialAnalysis();
        $materialAnalysis->id = $material->id;
        $materialAnalysis->material_id = $material->id;
        $materialAnalysis->setAnalysis($glaze);
        $materialAnalysis->save();

        foreach ($glaze->getMaterialComponents() as $materialComponent)
        {
            $recipeMaterial = new MaterialMaterial();
            $recipeMaterial->setMaterialComponent($material->id, $materialComponent);
            $recipeMaterial->save();
        }

        $materialAtmosphere = new MaterialAtmosphere();
        $materialAtmosphere->material_id = $material->id;
        $materialAtmosphere->atmosphere_id = $atmosphere->getId('Reduction');
        $materialAtmosphere->save();



        /***************************************
         * RECIPE: Matt and Dave's Craze-Free Gloss
         ***************************************/
        $material = new Material();
        $material->id = 103;
        $material->old_id = null;
        $material->name = 'Matt and Dave\'s Craze-Free Gloss';
        $material->material_type_id = 470;
        $material->from_orton_cone_id = $ortonCone->getId('9');
        $material->to_orton_cone_id = $ortonCone->getId('10');

        $material->is_primitive = false;
        $material->is_private = false;
        $material->created_by_user_id = 1;
        $material->save();

        $glaze = new CompositeMaterial();
        $glaze->setName($material->name);
        $glaze->addMaterial($whiting, 5.42);
        $glaze->addMaterial($f3124, 40.46);
        $glaze->addMaterial($g200, 14.37);
        $glaze->addMaterial($epk, 27.2);
        $glaze->addMaterial($silica, 12.55);

        $materialAnalysis = new MaterialAnalysis();
        $materialAnalysis->id = $material->id;
        $materialAnalysis->material_id = $material->id;
        $materialAnalysis->setAnalysis($glaze);
        $materialAnalysis->save();

        foreach ($glaze->getMaterialComponents() as $materialComponent)
        {
            $recipeMaterial = new MaterialMaterial();
            $recipeMaterial->setMaterialComponent($material->id, $materialComponent);
            $recipeMaterial->save();
        }

        $materialAtmosphere = new MaterialAtmosphere();
        $materialAtmosphere->material_id = $material->id;
        $materialAtmosphere->atmosphere_id = $atmosphere->getId('Reduction');
        $materialAtmosphere->save();



        if (DB::connection()->getDriverName() == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

    }
}
