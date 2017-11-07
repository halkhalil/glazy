<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialAnalysesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_analyses', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('material_id')->unsigned()->unique();

            /**************************************
             * PERCENTAGE ANALYSIS
             **************************************/
            $table->decimal('SiO2_percent', 7, 4)->nullable();
            $table->decimal('Al2O3_percent', 7, 4)->nullable();
            $table->decimal('B2O3_percent', 7, 4)->nullable();

            // Alkalis
            $table->decimal('Li2O_percent', 7, 4)->nullable();
            $table->decimal('K2O_percent', 7, 4)->nullable();
            $table->decimal('Na2O_percent', 7, 4)->nullable();
            $table->decimal('KNaO_percent', 7, 4)->nullable();

            // Alkaline Earths
            $table->decimal('BeO_percent', 7, 4)->nullable();
            $table->decimal('MgO_percent', 7, 4)->nullable();
            $table->decimal('CaO_percent', 7, 4)->nullable();
            $table->decimal('SrO_percent', 7, 4)->nullable();
            $table->decimal('BaO_percent', 7, 4)->nullable();

            $table->decimal('ZnO_percent', 7, 4)->nullable();
            $table->decimal('PbO_percent', 7, 4)->nullable();

            $table->decimal('P2O5_percent', 7, 4)->nullable();
            $table->decimal('F_percent', 7, 4)->nullable();

            // Colors
            $table->decimal('V2O5_percent', 7, 4)->nullable();
            $table->decimal('Cr2O3_percent', 7, 4)->nullable();
            $table->decimal('MnO_percent', 7, 4)->nullable();
            $table->decimal('MnO2_percent', 7, 4)->nullable();
            $table->decimal('FeO_percent', 7, 4)->nullable();
            $table->decimal('Fe2O3_percent', 7, 4)->nullable();
            $table->decimal('CoO_percent', 7, 4)->nullable();
            $table->decimal('NiO_percent', 7, 4)->nullable();
            $table->decimal('CuO_percent', 7, 4)->nullable();
            $table->decimal('Cu2O_percent', 7, 4)->nullable();
            $table->decimal('CdO_percent', 7, 4)->nullable();

            // Opacifiers
            $table->decimal('TiO2_percent', 7, 4)->nullable();
            $table->decimal('ZrO_percent', 7, 4)->nullable();
            $table->decimal('ZrO2_percent', 7, 4)->nullable();
            $table->decimal('SnO2_percent', 7, 4)->nullable();

            /**************************************
             * UMF
             **************************************/
            $table->decimal('SiO2_umf', 9, 4)->nullable()->index();
            $table->decimal('Al2O3_umf', 9, 4)->nullable()->index();
            $table->decimal('B2O3_umf', 9, 4)->nullable();

            // Alkalis
            $table->decimal('Li2O_umf', 9, 4)->nullable();
            $table->decimal('K2O_umf', 9, 4)->nullable();
            $table->decimal('Na2O_umf', 9, 4)->nullable();
            $table->decimal('KNaO_umf', 9, 4)->nullable();

            // Alkaline Earths
            $table->decimal('BeO_umf', 9, 4)->nullable();
            $table->decimal('MgO_umf', 9, 4)->nullable();
            $table->decimal('CaO_umf', 9, 4)->nullable();
            $table->decimal('SrO_umf', 9, 4)->nullable();
            $table->decimal('BaO_umf', 9, 4)->nullable();

            $table->decimal('ZnO_umf', 9, 4)->nullable();
            $table->decimal('PbO_umf', 9, 4)->nullable();

            $table->decimal('P2O5_umf', 9, 4)->nullable();
            $table->decimal('F_umf', 9, 4)->nullable();

            // Colors
            $table->decimal('V2O5_umf', 9, 4)->nullable();
            $table->decimal('Cr2O3_umf', 9, 4)->nullable();
            $table->decimal('MnO_umf', 9, 4)->nullable();
            $table->decimal('MnO2_umf', 9, 4)->nullable();
            $table->decimal('FeO_umf', 9, 4)->nullable();
            $table->decimal('Fe2O3_umf', 9, 4)->nullable();
            $table->decimal('CoO_umf', 9, 4)->nullable();
            $table->decimal('NiO_umf', 9, 4)->nullable();
            $table->decimal('CuO_umf', 9, 4)->nullable();
            $table->decimal('Cu2O_umf', 9, 4)->nullable();
            $table->decimal('CdO_umf', 9, 4)->nullable();

            // Opacifiers
            $table->decimal('TiO2_umf', 9, 4)->nullable();
            $table->decimal('ZrO_umf', 9, 4)->nullable();
            $table->decimal('ZrO2_umf', 9, 4)->nullable();
            $table->decimal('SnO2_umf', 9, 4)->nullable();

            /**************************************
             * FORMULA
             **************************************/
            $table->decimal('SiO2_mol', 9, 4)->nullable();
            $table->decimal('Al2O3_mol', 9, 4)->nullable();
            $table->decimal('B2O3_mol', 9, 4)->nullable();

            // Alkalis
            $table->decimal('Li2O_mol', 9, 4)->nullable();
            $table->decimal('K2O_mol', 9, 4)->nullable();
            $table->decimal('Na2O_mol', 9, 4)->nullable();
            $table->decimal('KNaO_mol', 9, 4)->nullable();

            // Alkaline Earths
            $table->decimal('BeO_mol', 9, 4)->nullable();
            $table->decimal('MgO_mol', 9, 4)->nullable();
            $table->decimal('CaO_mol', 9, 4)->nullable();
            $table->decimal('SrO_mol', 9, 4)->nullable();
            $table->decimal('BaO_mol', 9, 4)->nullable();

            $table->decimal('ZnO_mol', 9, 4)->nullable();
            $table->decimal('PbO_mol', 9, 4)->nullable();

            $table->decimal('P2O5_mol', 9, 4)->nullable();
            $table->decimal('F_mol', 9, 4)->nullable();

            // Colors
            $table->decimal('V2O5_mol', 9, 4)->nullable();
            $table->decimal('Cr2O3_mol', 9, 4)->nullable();
            $table->decimal('MnO_mol', 9, 4)->nullable();
            $table->decimal('MnO2_mol', 9, 4)->nullable();
            $table->decimal('FeO_mol', 9, 4)->nullable();
            $table->decimal('Fe2O3_mol', 9, 4)->nullable();
            $table->decimal('CoO_mol', 9, 4)->nullable();
            $table->decimal('NiO_mol', 9, 4)->nullable();
            $table->decimal('CuO_mol', 9, 4)->nullable();
            $table->decimal('Cu2O_mol', 9, 4)->nullable();
            $table->decimal('CdO_mol', 9, 4)->nullable();

            // Opacifiers
            $table->decimal('TiO2_mol', 9, 4)->nullable();
            $table->decimal('ZrO_mol', 9, 4)->nullable();
            $table->decimal('ZrO2_mol', 9, 4)->nullable();
            $table->decimal('SnO2_mol', 9, 4)->nullable();


            /**************************************
             * PERCENT FORMULA
             **************************************/
            $table->decimal('SiO2_percent_mol', 9, 4)->nullable();
            $table->decimal('Al2O3_percent_mol', 9, 4)->nullable();
            $table->decimal('B2O3_percent_mol', 9, 4)->nullable();

            // Alkalis
            $table->decimal('Li2O_percent_mol', 9, 4)->nullable();
            $table->decimal('K2O_percent_mol', 9, 4)->nullable();
            $table->decimal('Na2O_percent_mol', 9, 4)->nullable();
            $table->decimal('KNaO_percent_mol', 9, 4)->nullable();

            // Alkaline Earths
            $table->decimal('BeO_percent_mol', 9, 4)->nullable();
            $table->decimal('MgO_percent_mol', 9, 4)->nullable();
            $table->decimal('CaO_percent_mol', 9, 4)->nullable();
            $table->decimal('SrO_percent_mol', 9, 4)->nullable();
            $table->decimal('BaO_percent_mol', 9, 4)->nullable();

            $table->decimal('ZnO_percent_mol', 9, 4)->nullable();
            $table->decimal('PbO_percent_mol', 9, 4)->nullable();

            $table->decimal('P2O5_percent_mol', 9, 4)->nullable();
            $table->decimal('F_percent_mol', 9, 4)->nullable();

            // Colors
            $table->decimal('V2O5_percent_mol', 9, 4)->nullable();
            $table->decimal('Cr2O3_percent_mol', 9, 4)->nullable();
            $table->decimal('MnO_percent_mol', 9, 4)->nullable();
            $table->decimal('MnO2_percent_mol', 9, 4)->nullable();
            $table->decimal('FeO_percent_mol', 9, 4)->nullable();
            $table->decimal('Fe2O3_percent_mol', 9, 4)->nullable();
            $table->decimal('CoO_percent_mol', 9, 4)->nullable();
            $table->decimal('NiO_percent_mol', 9, 4)->nullable();
            $table->decimal('CuO_percent_mol', 9, 4)->nullable();
            $table->decimal('Cu2O_percent_mol', 9, 4)->nullable();
            $table->decimal('CdO_percent_mol', 9, 4)->nullable();

            // Opacifiers
            $table->decimal('TiO2_percent_mol', 9, 4)->nullable();
            $table->decimal('ZrO_percent_mol', 9, 4)->nullable();
            $table->decimal('ZrO2_percent_mol', 9, 4)->nullable();
            $table->decimal('SnO2_percent_mol', 9, 4)->nullable();

            /**************************************
             * Ratios, weights, calculated fields
             **************************************/

            $table->decimal('SiO2_Al2O3_ratio_umf', 9, 4)->nullable()->index();
            $table->decimal('R2O_umf', 9, 4)->nullable()->index();
            $table->decimal('RO_umf', 9, 4)->nullable();

            $table->decimal('weight', 9, 4)->nullable();
            $table->decimal('loi', 9, 4)->nullable();
            $table->decimal('formula_weight', 9, 4)->nullable();
            $table->decimal('calculated_loi', 9, 4)->nullable();
            $table->decimal('thermal_expansion', 9, 4)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // FOREIGN KEYS
            $table->foreign('material_id')->references('id')->on('materials');

        });

        DB::statement('ALTER TABLE material_analyses ADD SiO2_Al2O3 POINT AFTER SiO2_Al2O3_ratio_umf' );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('material_analyses');
    }
}
