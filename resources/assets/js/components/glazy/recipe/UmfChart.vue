<template>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <h2 class="card-title">UMF Chart</h2>
            </div>
            <div class="col-md-8 text-right">
                <span v-if="isLoaded && !isProcessing && recipeList && recipeList.length > 0">
                    <em>Search for 100 closest recipes,
                    {{ recipeList.length - 1 }} recipes found.</em>
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-12 mb-2">
                <img src="/img/charts/recipe.png" height="12"/> Recipe &nbsp;
                <img src="/img/charts/analysis.png" height="14"/> Analysis &nbsp;
                <img src="/img/charts/current.png" height="15"/> Current Recipe
            </div>
            <div class="col-md-6 col-sm-12 mb-2 text-right">
                R<sub>2</sub>O:RO Scale <img src="/img/charts/ror2oscale.png" height="35" width="366"/>
            </div>
            <div class="col-md-12">
                <div class="row">
                    <div v-show="!isLoaded || isProcessing" class="col-md-12">
                        <div class="load-container load7">
                            <div class="loader">Loading...</div>
                        </div>
                    </div>
                    <div v-show="isLoaded && !isProcessing" class="col-sm-12">
                        <div id="stull-chart-d3">
                            <umf-plotly
                                    :recipeData="recipeList"
                                    :oxide1="oxide1"
                                    :oxide2="oxide2"
                                    :oxide3="oxide3"
                                    :baseTypeId="baseTypeId"
                                    :noZeros="false"
                                    :isThreeAxes="isThreeAxes"
                                    :showStullChart="true"
                                    :chartHeight="chartHeight"
                                    :chartWidth="chartWidth"
                                    :axesColor="'#aaaaaa'"
                                    :gridColor="'#aaaaaa'"
                                    :highlightedRecipeId="highlightedRecipeId"
                                    :showModeBar="true"
                                    :currentRecipeId="material.id"
                                    v-on:clickedUmfPlotly="clickedChart"
                            >
                            </umf-plotly>
                        </div>
                    </div>
                    <div class="col-md-8 col-sm-12">
                        <form class="search-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="typeId">Type:</label>
                                    <b-form-select
                                            id="typeId"
                                            size="sm"
                                            v-if="materialTypeOptions"
                                            v-model="materialTypeId"
                                            :options="materialTypeOptions"
                                            @input="fetchRecipeList">
                                        <template slot="first">
                                            <option :value="null">All Recipe Types</option>
                                        </template>
                                    </b-form-select>
                                </div>
                                <div class="form-group">
                                    <label for="coneId">Temperature:</label>
                                    <b-form-select
                                            id="coneId"
                                            size="sm"
                                            v-model="cone_id"
                                            :options="constants.ORTON_CONES_SELECT"
                                            @input="fetchRecipeList">
                                        <template slot="first">
                                            <option :value="null">All Temps</option>
                                        </template>
                                    </b-form-select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="axisToggle"></label>
                                    <b-checkbox
                                            id="axisToggle"
                                            v-model="isThreeAxes">3 Axis</b-checkbox>
                                </div>
                                <div class="form-group">
                                    <label for="oxide1">Oxide 1:</label>
                                    <b-form-select
                                            id="oxide1"
                                            size="sm"
                                            v-model="oxide1"
                                            :options="oxides"
                                            @input="fetchRecipeList"
                                            class="col">
                                    </b-form-select>
                                </div>
                                <div class="form-group">
                                    <label for="oxide2">Oxide 2:</label>
                                    <b-form-select
                                            id="oxide2"
                                            size="sm"
                                            v-model="oxide2"
                                            :options="oxides"
                                            @input="fetchRecipeList"
                                            class="col">
                                        <template slot="first">
                                            <option :value="null">Oxide 2</option>
                                        </template>
                                    </b-form-select>
                                </div>
                                <div v-show="isThreeAxes" class="form-group">
                                    <label for="oxide3">Oxide 3:</label>
                                    <b-form-select
                                            id="oxide3"
                                            size="sm"
                                            v-model="oxide3"
                                            :options="oxides"
                                            @input="fetchRecipeList"
                                            class="col">
                                        <template slot="first">
                                            <option :value="null">Oxide 3</option>
                                        </template>
                                    </b-form-select>
                                </div>

                            </div>
                        </form>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div v-if="clickedRecipe"
                             class="card chart-recipe-card">
                            <div class="card-body">
                                <p v-html="clickedRecipe.text"></p>
                                <a v-bind:href="'https://glazy.org/recipes/' + clickedRecipe.customdata"
                                   target="_blank" class="btn">View on Glazy</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</template>


<script>

  import Analysis from 'ceramicscalc-js/src/analysis/Analysis'
  import MaterialTypes from 'ceramicscalc-js/src/material/MaterialTypes'
  import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'
  import UmfPlotly from 'vue-plotly-umf-charts/src/components/UmfPlotly.vue'
  import Vue from 'vue'

  export default {

    name: 'UmfChart',
    components: {
      UmfPlotly
    },

    props: ['current_user', 'material'],

    data() {
      return {
        recipeList: null,
        chart: null,
        materialTypes: new MaterialTypes(),
        constants: new GlazyConstants(),
        oxides: Analysis.OXIDE_NAME_UNICODE_SELECT,
        materialTypeId: null,
        cone_id: null,
        showConesString: 'Show Cones',
        isProcessing: true,
        oxide1: 'Al2O3',
        oxide2: 'SiO2',
        oxide3: 'Fe2O3',
        baseTypeId: new MaterialTypes().GLAZE_TYPE_ID,
        noZeros: false,
        isThreeAxes: false,
        showStullChart: true,
        chartHeight: 300,
        chartWidth: 400,
        axesColor: '#aaaaaa',
        gridColor: '#aaaaaa',
        highlightedRecipeId: 0,
        showModeBar: 'false',
        clickedRecipe: null
      }
    },

    computed: {
      isLoaded: function () {
        if (this.recipeList && this.recipeList.length > 0) {
          return true;
        }
        return false;
      },

      hasRecipeList: function () {

      },

      materialTypeOptions: function () {
        if (this.material.baseTypeId) {
          switch (this.material.baseTypeId) {
            case this.materialTypes.GLAZE_TYPE_ID:
              return this.materialTypes.getGlazeTypes();
            case this.materialTypes.CLAYS_TYPE_ID:
              return this.materialTypes.getClayTypes();
            case this.materialTypes.SLIPS_TYPE_ID:
              return this.materialTypes.getSlipTypes();
          }
        }
        return null;
      },

      umf_analysis: function () {
        if (this.isLoaded) {
          if (this.material.hasOwnProperty('analysis')
            && this.material.analysis
            && this.material.analysis.hasOwnProperty('umf_analysis')
            && this.material.analysis.umf_analysis) {
            return this.material.analysis.umf_analysis;
          }
        }
        return null;
      },

      type_options: function () {
        return this.materialTypes.getGlazeTypes();
      }
    },

    mounted() {
      // TODO: assumes material already loaded
      // TODO: for some reason the mounted function being called twice!
      if (this.material) {
        if (this.material.materialTypeId) {
          this.materialTypeId = this.material.materialTypeId;
        }
        this.fetchRecipeList();
      }

      setTimeout(() => {
        this.handleResize()
      }, 300);
      window.addEventListener('resize', this.handleResize)
    },

    methods: {


      fetchRecipeList: function () {

        this.isProcessing = true;

        var recipeUrl = Vue.axios.defaults.baseURL + '/search/nearestSiAl?material_id=' + this.material.id;
        recipeUrl += '&oxide1=' + this.oxide1
        recipeUrl += '&oxide2=' + this.oxide2
        if (this.materialTypeId) {
          recipeUrl += '&material_type_id=' + this.materialTypeId;
        }
        if (this.cone_id) {
          recipeUrl += '&cone=' + this.cone_id;
        }

        Vue.axios.get(recipeUrl)
          .then(function (response) {
            this.recipeList = response.data.data;
            if (!this.recipeList) {
              this.recipeList = [];
            }
            this.recipeList.push(this.material)

            var currentUserId = null;
            if (this.current_user && this.current_user.hasOwnProperty('id')) {
              currentUserId = this.current_user.id;
            }
            if (this.recipeList) {

            }
            this.isProcessing = false;
          }.bind(this), function (response) {
//                .catch(response => {
            // Error Handling
//                });
            this.isProcessing = false;
          }.bind(this));
      },

      clickedChart: function(data) {
        this.clickedRecipe = data
      },

      handleResize: function () {
        if (document.getElementById('stull-chart-d3')) {
          console.log('old width: ' + this.chartWidth)
          this.chartHeight = document.getElementById('stull-chart-d3').clientHeight
          this.chartWidth = document.getElementById('stull-chart-d3').clientWidth
          console.log('new width: ' + this.chartWidth)
        }
      },

    }

}

</script>

<style>
    .chart-recipe-card {
        background-color: #cccccc;
    }

    .r2o-colors tr td {
        font-size: 12px;
        min-width: 26px;
        margin: 1px;
        text-align: center;
    }
    .r2o-colors tr td.label {
        width: 100px;
    }

    .r2ocolor-b-100 { background-color: #FF0000; }
    .r2ocolor-b-95  { background-color: #FF1212; }
    .r2ocolor-b-90  { background-color: #FF2424; }
    .r2ocolor-b-85  { background-color: #FF3636; }
    .r2ocolor-b-80  { background-color: #FF4848; }
    .r2ocolor-b-75  { background-color: #FF5B5B; }
    .r2ocolor-b-70  { background-color: #FF6D6D; }
    .r2ocolor-b-65  { background-color: #FF7F7F; }
    .r2ocolor-b-60  { background-color: #FF9191; }
    .r2ocolor-b-55  { background-color: #FFA3A3; }
    .r2ocolor-b-50  { background-color: #FFB6B6; }
    .r2ocolor-b-45  { background-color: #FFC8C8; }
    .r2ocolor-b-40  { background-color: #FFDADA; }
    .r2ocolor-b-35  { background-color: #FFECEC; }
    .r2ocolor-b-30  { background-color: #FFFFFF; }
    .r2ocolor-b-25  { background-color: #D4D4FF; }
    .r2ocolor-b-20  { color: #FFFFFF; background-color: #AAAAFF; }
    .r2ocolor-b-15  { color: #FFFFFF; background-color: #7F7FFF; }
    .r2ocolor-b-10  { color: #FFFFFF; background-color: #5555FF; }
    .r2ocolor-b-5   { color: #FFFFFF; background-color: #2A2AFF; }
    .r2ocolor-b-0   { color: #FFFFFF; background-color: #0000FF; }
</style>