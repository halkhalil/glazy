<template>
  <div class="calc-container" v-if="isLoaded">
    <div class="row calc-row">
      <div class="col-md-4 chart-col">
        <div id="stull-chart-d3">
          <umf-plotly
                  :recipeData="[originalMaterial, newMaterial]"
                  :oxide1="'Al2O3'"
                  :oxide2="'SiO2'"
                  :noZeros="false"
                  :isThreeAxes="false"
                  :showStullChart="true"
                  :chartHeight="chartHeight"
                  :chartWidth="chartWidth"
                  :axesColor="'#aaaaaa'"
                  :gridColor="'#efefef'"
                  :baseTypeId="baseTypeId"
          >
          </umf-plotly>
        </div>
      </div>
      <div class="col-md-8">
        <b-card no-body class="analysis-card">
          <b-tabs ref="tabs" card>
            <b-tab title="UMF" active>
              <div class="table-responsive">
                <MaterialAnalysisTableCompare
                        :originalMaterial="originalMaterial"
                        :newMaterial="newMaterial"
                        :showHeadings="false"
                >
                </MaterialAnalysisTableCompare>
                <table class="umf-spark-table">
                  <MaterialAnalysisUmfSpark2Single
                          :material="originalMaterial"
                          :showOxideList="true"
                  >
                  </MaterialAnalysisUmfSpark2Single>
                  <MaterialAnalysisUmfSpark2Single
                          :material="newMaterial"
                  >
                  </MaterialAnalysisUmfSpark2Single>
                </table>
              </div>
            </b-tab>
            <b-tab title="% Mol">
              <div class="table-responsive">
                <MaterialAnalysisPercentTableCompare
                        :originalMaterial="originalMaterial"
                        :newMaterial="newMaterial"
                        :showHeadings="false"
                        :isPercentMol="true"
                >
                </MaterialAnalysisPercentTableCompare>
              </div>
            </b-tab>
            <b-tab title="%">
              <div class="table-responsive">
                <MaterialAnalysisPercentTableCompare
                        :originalMaterial="originalMaterial"
                        :newMaterial="newMaterial"
                        :showHeadings="false"
                        :isPercentMol="false"
                >
                </MaterialAnalysisPercentTableCompare>
              </div>
            </b-tab>
          </b-tabs>
        </b-card>
      </div>
    </div>


    <div class="row is-mobile">
      <div class="col-sm-7">
        Material
      </div>
      <div class="col-sm-3">
        Amount
      </div>
      <div class="col-sm-2">
        Additive
      </div>
    </div>

    <div class="row is-mobile"
         v-for="(fieldArray, index) in materialFieldsId"
         v-if="index < numVisibleRows">

      <div class="col-sm-7">
          <multiselect
                  :id="index + '_name'"
                  :options="selectMaterials"
                  v-model="materialFieldsId[index]"
                  @input="updateSelected"
                  key="value"
                  label="label"
          ></multiselect>
      </div>

      <div class="col-sm-3">
        <b-form-input :id="index + '_amount'"
                      v-model="materialFieldsAmount[index]"
                      type="number"
                      min="0"
                      max="100"
                      placeholder="%"
                      @input="updateSelected"></b-form-input>
      </div>

      <div class="col-sm-2">
        <b-form-checkbox id="index + '_add'"
                         v-model="materialFieldsIsAdditional[index]"
                         @input="updateSelected">
        </b-form-checkbox>
      </div>

    </div>

    <div class="row is-mobile">
      <div class="col-sm-7">
        <button class="btn" @click="resetRecipe">Reset</button>
      </div>
      <div class="col-sm-3">
        <b-form-input v-model="subtotal"
                      placeholder="Total"
                      type="number"
                      disabled></b-form-input>
        <button class="btn" @click="setTo100">Set 100%</button>
      </div>
      <div class="col-sm-2">
      </div>
    </div>

  </div>
</template>

<script>
  import Multiselect from 'vue-multiselect';

  import MaterialTypes from 'ceramicscalc-js/src/material/MaterialTypes'
  import Analysis from 'ceramicscalc-js/src/analysis/Analysis'
  import PercentageAnalysis from 'ceramicscalc-js/src/analysis/PercentageAnalysis'
  import Material from 'ceramicscalc-js/src/material/Material'
  import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'

  import StaticMaterialList from '../../static/data/material-list.json'

  import UmfPlotly from 'vue-plotly-umf-charts/src/components/UmfPlotly.vue'

  import MaterialAnalysisTableCompare from '../components/glazy/analysis/MaterialAnalysisTableCompare.vue';
  import MaterialAnalysisUmfSpark2Single from '../components/glazy/analysis/MaterialAnalysisUmfSpark2Single.vue';
  import MaterialAnalysisPercentTableCompare from '../components/glazy/analysis/MaterialAnalysisPercentTableCompare.vue';

  export default {
    name: 'Calculator',
    components: {
      Multiselect,
      UmfPlotly,
      MaterialAnalysisTableCompare,
      MaterialAnalysisUmfSpark2Single,
      MaterialAnalysisPercentTableCompare
    },
    props: {
//      originalMaterial: {
//        type: Object,
//        default: null
//      }
    },
    data() {
      return {
        oxides: new GlazyConstants().OXIDE_NAME_UNICODE_SELECT,
        glazetypes: new GlazyConstants().GLAZE_TYPES_SELECT,
        colortype: {value:'r2o'},
        originalMaterial: null,
        materialLibrary: StaticMaterialList.data.materials,
        lookupMaterialLibrary: {},
        selectMaterials: [],
        materialFieldsId: [],
        materialFieldsAmount: [],
        materialFieldsIsAdditional: [],
        newMaterial: new Material(),
        baseTypeId: new MaterialTypes().GLAZE_TYPE_ID,
        maximumRowNumber: 30,
        minimumVisibleRows: 1,
        subtotal: 0,
        isProcessing: false,
        similarMaterials: null,
        loadingSimilarMaterials: false,
        chartHeight: 300,
        chartWidth: 300
      };
    },
    computed : {

      isLoaded: function() {
        if (this.selectMaterials.length > 0 && this.newMaterial) {
          return true;
        }
        return false;
      },

      numVisibleRows: function() {
        var numVisibleRows = 0;
        if (this.isLoaded) {
          for (var i = 0; i < this.materialFieldsId.length; i++) {
            if ((!this.materialFieldsId[i] || !this.materialFieldsId[i].value)
              && !this.materialFieldsAmount[i]
              && !this.materialFieldsIsAdditional[i]
            ) {
              numVisibleRows = i + 1;
              break;
            }
          }
          if (numVisibleRows > this.materialFieldsId.length) {
            return this.materialFieldsId.length;
          }
          if (numVisibleRows < this.minimumVisibleRows) {
            if (this.minimumVisibleRows <= this.materialFieldsId.length) {
              return this.minimumVisibleRows;
            }
            return this.materialFieldsId.length;
          }
        }
        return numVisibleRows;
      },

      chartmaterials: function() {
        var chartmaterials = [];
        if (this.originalMaterial && this.newMaterial) {
          chartmaterials = [ this.originalMaterial, this.newMaterial ];
        }
        else {
          chartmaterials = [this.newMaterial];
        }
        return chartmaterials;
      },

      originalUMFAnalysis: function() {
        if (this.originalMaterial) {
          return this.originalMaterial.getROR2OUnityFormulaAnalysis();
        }
        return null;
      },

      newUMFAnalysis: function() {
        return this.newMaterial.getROR2OUnityFormulaAnalysis();
      },

      originalMolAnalysis: function() {
        if (this.originalMaterial) {
          return this.originalMaterial.getMolePercentageFormula();
        }
        return null;
      },

      newMolAnalysis: function() {
        return this.newMaterial.getMolePercentageFormula();
      }

    },

    mounted() {

      this.staticFetchMaterials(); // Load materials from json file

      this.setLeach();

      if (this.originalMaterial) {
        this.newMaterial = this.originalMaterial.clone();
      }
      else {
        this.newMaterial = new Material();
        this.newMaterial.setName('New Recipe');
      }

      //this.fetchMaterials();  // Not live

      this.resetMaterialFields();

      this.setSubtotal();

      setTimeout(() => {
        this.handleResize()
      }, 300);
      window.addEventListener('resize', this.handleResize)

    },


    methods: {

      setLeach () {

        this.originalMaterial = new Material();

        this.originalMaterial.setName('Leach 4321');

        var materialObj = Material.createFromJson(this.lookupMaterialLibrary['12131']);
        this.originalMaterial.addMaterialComponent(materialObj, 40, false);

        console.log(materialObj);

        materialObj = Material.createFromJson(this.lookupMaterialLibrary['12400']);
        this.originalMaterial.addMaterialComponent(materialObj, 30, false);
        materialObj = Material.createFromJson(this.lookupMaterialLibrary['12457']);
        this.originalMaterial.addMaterialComponent(materialObj, 20, false);
        materialObj = Material.createFromJson(this.lookupMaterialLibrary['12288']);
        this.originalMaterial.addMaterialComponent(materialObj, 10, false);

        console.log(this.originalMaterial);

      },
      updateSelected (newSelected) {
        this.newMaterial.removeAllMaterialComponents();

        for (var i = 0; i < this.materialFieldsId.length; i++) {
          if (this.materialFieldsId[i] && this.materialFieldsId[i].value &&
            this.materialFieldsAmount[i] > 0) {
            var materialObj = Material.createFromJson(
              this.lookupMaterialLibrary[this.materialFieldsId[i].value]
            );
            this.newMaterial.addMaterialComponent(
              materialObj,
              this.materialFieldsAmount[i],
              this.materialFieldsIsAdditional[i]
            );
          }
        }

        this.setSubtotal();

        //this.checkForDuplicates();
      },

      staticFetchMaterials : function() {
        this.lookupMaterialLibrary = {};
        this.selectMaterials = [];
        for (var i = 0; i < this.materialLibrary.length; i++) {
          this.lookupMaterialLibrary[this.materialLibrary[i].id] = this.materialLibrary[i];
          this.selectMaterials.push({value: this.materialLibrary[i].id, label: this.materialLibrary[i].name});
        }
      },

      fetchMaterials : function(){
        var materialsListUrl = '/api/v1/materials/editRecipeMaterialList/';
        if (this.originalMaterial) {
          materialsListUrl += this.newMaterial.id;
        }
        axios.get(materialsListUrl)
          .then((response) => {
            this.materialLibrary = response.data.data.materials;
            console.log('GOT MATERIALS LIST LEN: ' + this.materialLibrary.length);
            this.lookupMaterialLibrary = {};
            this.selectMaterials = [];
            for (var i = 0; i < this.materialLibrary.length; i++) {
              this.lookupMaterialLibrary[this.materialLibrary[i].id] = this.materialLibrary[i];
              this.selectMaterials.push({value: this.materialLibrary[i].id, label: this.materialLibrary[i].name});
            }
          })
          .catch(response => {
            // Error Handling
          });
      },

      resetMaterialFields: function() {
        this.materialFieldsId = [];
        this.materialFieldsAmount = [];
        this.materialFieldsIsAdditional = [];

        if (this.originalMaterial) {
          for (var i = 0; i < this.originalMaterial.materialComponents.length; i++) {
            this.materialFieldsId.push({value: this.originalMaterial.materialComponents[i].material.id, label: this.originalMaterial.materialComponents[i].material.name});
            this.materialFieldsAmount.push(this.originalMaterial.materialComponents[i].amount);
            this.materialFieldsIsAdditional.push(this.originalMaterial.materialComponents[i].isAdditional);
          }
        }
        for (var i = 0; i < this.maximumRowNumber; i++) {
          this.materialFieldsId.push({value: null, label: ''});
          this.materialFieldsAmount.push(null);
          this.materialFieldsIsAdditional.push(false);
        }
      },

      setTo100: function() {
        if (this.isLoaded) {
          var subtotal = 0.0;

          for (var i = 0; i < this.materialFieldsId.length; i++) {
            if (!isNaN(this.materialFieldsAmount[i])
              && this.materialFieldsAmount[i] > 0
              && !this.materialFieldsIsAdditional[i]) {
              subtotal += parseFloat(this.materialFieldsAmount[i]);
            }
          }
          if (subtotal > 0) {
            for (var i = 0; i < this.materialFieldsId.length; i++) {
              var newVal = Math.round(this.materialFieldsAmount[i] / subtotal * 10000) / 100;
              if (newVal > 0) {
                this.materialFieldsAmount[i] = newVal;
              }
              else {
                this.materialFieldsAmount[i] = null;
              }
            }
          }
          this.setSubtotal();
        }
      },

      resetRecipe: function() {
        this.resetMaterialFields();
        this.updateSelected(null);
      },

      setSubtotal: function() {
        var subtotal = 0.0;

        for (var i = 0; i < this.materialFieldsId.length; i++) {
          if (this.materialFieldsId[i] && this.materialFieldsId[i].value
            && this.materialFieldsAmount[i] > 0
            && !isNaN(this.materialFieldsAmount[i])
          ) {
            subtotal += parseFloat(this.materialFieldsAmount[i]);
          }
        }

        this.subtotal = Number(subtotal).toFixed(2);
      },

      update: function () {
        if (this.isLoaded) {
          this.isProcessing = true;

          var form = {
            materialComponents: []
          };

          for (var i = 0; i < this.materialFieldsId.length; i++) {
            if (this.materialFieldsId[i] && this.materialFieldsId[i].value
              && !isNaN(this.materialFieldsAmount[i]
                && this.materialFieldsAmount[i] > 0)
            ) {
              form.materialComponents.push({
                componentMaterialId: this.materialFieldsId[i].value,
                percentageAmount: this.materialFieldsAmount[i],
                isAdditional: this.materialFieldsIsAdditional[i],
              });
            }
          }

          if (this.newMaterial) {
            form._method = 'PATCH';
            form.id = this.newMaterial.id;
            axios.post('/api/v1/recipematerials/' + this.newMaterial.id, form)
              .then(function (response) {
                this.$emit('recipematerialsupdated');
                this.recipeUpdated();
              }.bind(this), function (response) {
                this.errors = response.data;
                this.hasErrors = true;
                this.isProcessing = false;
              }.bind(this));
          }
          else
          {
            // This is a new material
            axios.post('/api/v1/recipematerials', form)
              .then(function (response) {
                this.material = response.data.data.material;
                console.log('CREATED NEW MATERIAL');
                console.log(this.material);
                this.$emit('recipematerialsupdated');
                this.recipeUpdated();
              }.bind(this), function (response) {
                this.errors = response.data;
                this.hasErrors = true;
                this.isProcessing = false;
              }.bind(this));
          }
        }
      },

      recipeUpdated: function() {
        window.location = '/recipes/' + this.newMaterial.id;
      },

      checkForDuplicates: function() {

        this.loadingSimilarMaterials = true;
        this.similarMaterials = null;

        var form = {
          excludeMaterialId: null,
          materialComponents: []
        };

        if (this.material) {
          form.excludeMaterialId = this.newMaterial.id;
        }

        // Check each material component for id and amount
        for (var i = 0; i < this.materialFieldsId.length; i++) {
          if (this.materialFieldsId[i] && this.materialFieldsId[i].value
            && !isNaN(this.materialFieldsAmount[i]
              && Number(this.materialFieldsAmount[i]) > 0)
          ) {
            form.materialComponents.push({
              componentMaterialId: this.materialFieldsId[i].value,
              percentageAmount: this.materialFieldsAmount[i],
              isAdditional: this.materialFieldsIsAdditional[i],
            });
          }
        }

        if (form.materialComponents.length > 0) {
          // Only search if we have at least one material component
          axios.post('/api/v1/search/similarRecipes', form)
            .then((response) => {
              this.similarMaterials = response.data.data;
              this.loadingSimilarMaterials = false;
            })
            .catch(response => {
              this.loadingSimilarMaterials = false;
            });
        }
        else {
          this.loadingSimilarMaterials = false;
        }
      },

      getImageBin: function(id) {
        id = '' + id;
        return id.substr(id.length - 2);
      },

      hasThumbnail: function(recipe) {
        if (recipe.hasOwnProperty('thumbnail')
          && recipe.thumbnail.hasOwnProperty('url')
          && recipe.thumbnail.url) {
          return true;
        }
        return false;
      },

      getImageUrl: function(recipe, size) {
        if (this.hasThumbnail(recipe)) {
          var bin = this.getImageBin(recipe.id);
          return '/storage/uploads/recipes/' + bin + '/' + size + '_' + recipe.thumbnail.url;
        }
        return '/img/glazy/recipes/black.png';
      },

      handleResize: function () {
        console.log('old width: ' + this.chartWidth)
        this.chartHeight = document.getElementById('stull-chart-d3').clientHeight
        this.chartWidth = document.getElementById('stull-chart-d3').clientWidth
        console.log('new width: ' + this.chartWidth)
      }




    }
  }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style>

  .calc-container {
//    background-color: #efefef;
  }

  .calc-row {
    margin-top: 1rem;
  }

  .chart-col {
    padding-left: 10px;
    padding-right: 0;
  }

  .analysis-card .tabs .nav-tabs {
    justify-content: center;
    padding: 10px 10px;
  }

  .analysis-card .card-body {
    padding: 15px 10px 10px 10px;
  }

  .analysis-card .tabs .nav-tabs .nav-item .nav-link {
    padding: 5px 10px;
  }

  .analysis-card .tabs .nav-tabs .nav-item .nav-link.active {
    background-color: #999;
  }


.dropdown .dropdown-menu {
  z-index: 999;
}
.v-select .dropdown-menu {
  z-index: 999;
}

</style>