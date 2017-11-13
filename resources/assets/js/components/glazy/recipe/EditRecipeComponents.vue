<template>
  <div class="calc-container" v-if="isLoaded">
    <div class="row edit-recipe-title-row" v-if="originalMaterial">
      <div class="col-md-12">
          <b-button class="cancel-edit-btn float-right"
                    size="sm"
                    variant="secondary"
                    @click.prevent="editComponentsCancel"><i class="fa fa-times"></i> Cancel</b-button>
        <h4 class="edit-recipe-title">
          <i v-if="originalMaterial.isPrivate" class="fa fa-lock"></i>
          Edit {{ originalMaterial.name }}
        </h4>
      </div>
    </div>

    <div class="row calc-row">
      <b-alert v-if="apiError" show variant="danger">
        API Error: {{ apiError.message }}
      </b-alert>
      <b-alert v-if="serverError" show variant="danger">
        Server Error: {{ serverError }}
      </b-alert>
      <div class="load-container load7 fullscreen" v-if="isProcessing">
        <div class="loader">Loading...</div>
      </div>

      <div class="col-md-4 chart-col">
        <div v-if="chartMaterials && isLoaded" id="umf-d3-chart-container" class="w-100">
          <umf-d3-chart
                  :recipeData="chartMaterials"
                  :width="chartWidth"
                  :height="chartHeight"
                  :chartDivId="'umf-d3-chart-container'"
                  :baseTypeId="baseTypeId"
                  :colortype="'r2o'"
                  :showRecipes="true"
                  :showCones="false"
                  :showStullChart="true"
                  :showStullLabels="true"
                  :axisLabelFontSize="'1rem'"
                  :stullLabelsFontSize="'0.75rem'"
                  :showZoomButtons="false"
                  :showAxesLabels="true"
                  :xoxide="'SiO2'"
                  :yoxide="'Al2O3'"
          >
          </umf-d3-chart>
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
                <JsonUmfSparkSvg
                        :material="newMaterial"
                        :showOxideList="false"
                        :squareSize="30"
                >
                </JsonUmfSparkSvg>
                <JsonUmfSparkSvg
                        v-if="originalMaterial"
                        :material="originalMaterial"
                        :showOxideList="false"
                        :squareSize="30"
                >
                </JsonUmfSparkSvg>
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

    <b-card>
      <div class="row">
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

      <div class="row"
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
      <div class="row">
        <div class="col-sm-7">
          <b-button size="sm"
                    variant="secondary"
                    @click.prevent="resetRecipe"><i class="fa fa-refresh"></i> Reset</b-button>
        </div>
        <div class="col-sm-3">
          <b-form-input v-model="subtotal"
                        placeholder="Total"
                        type="number"
                        disabled></b-form-input>
          <b-button size="sm"
                    variant="secondary"
                    @click.prevent="setTo100">Set 100%</b-button>
        </div>
        <div class="col-sm-2">
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <b-button size="sm"
                    variant="info"
                    @click.prevent="update"><i class="fa fa-save"></i> Save Changes</b-button>
        </div>
      </div>
    </b-card>
  </div>
</template>

<script>
  import Multiselect from 'vue-multiselect';

  import MaterialTypes from 'ceramicscalc-js/src/material/MaterialTypes'
  import Analysis from 'ceramicscalc-js/src/analysis/Analysis'
  import PercentageAnalysis from 'ceramicscalc-js/src/analysis/PercentageAnalysis'
  import Material from 'ceramicscalc-js/src/material/Material'
  import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'

  import UmfD3Chart from 'vue-d3-stull-charts/src/components/UmfD3Chart.vue'

  import MaterialAnalysisTableCompare from '../analysis/MaterialAnalysisTableCompare.vue';
  import MaterialAnalysisUmfSpark2Single from '../analysis/MaterialAnalysisUmfSpark2Single.vue';
  import MaterialAnalysisPercentTableCompare from '../analysis/MaterialAnalysisPercentTableCompare.vue';
  import JsonUmfSparkSvg from '../analysis/JsonUmfSparkSvg.vue'

  import qs from 'qs'

  export default {
    name: 'EditRecipeComponents',
    components: {
      Multiselect,
      UmfD3Chart,
      MaterialAnalysisTableCompare,
      MaterialAnalysisPercentTableCompare,
      JsonUmfSparkSvg
    },
    props: {
      originalMaterial: {
        type: Object,
        default: null
      }
    },
    data() {
      return {
        oxides: new GlazyConstants().OXIDE_NAME_UNICODE_SELECT,
        glazetypes: new GlazyConstants().GLAZE_TYPES_SELECT,
        colortype: {value:'r2o'},
        materialLibrary: null,
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
        chartWidth: 0,
        apiError: null,
        serverError: null
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

      chartMaterials: function() {
        var chartMaterials = [];
        if (this.originalMaterial && this.newMaterial) {
          chartMaterials.push(this.originalMaterial)
          chartMaterials.push(this.newMaterial)
        }
        else {
          chartMaterials.push(this.newMaterial)
        }
        return chartMaterials;
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
      this.fetchMaterials();

      if (this.originalMaterial) {
        this.newMaterial = this.originalMaterial.clone();
      }
      else {
        this.newMaterial = new Material();
        this.newMaterial.setName('New Recipe');
      }

      this.resetMaterialFields();
      this.setSubtotal();

      setTimeout(() => {
        this.handleResize()
      }, 300);
      window.addEventListener('resize', this.handleResize)

    },


    methods: {

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

      fetchMaterials : function() {
        this.isProcessing = true
        var materialsListUrl = '/usermaterials/editList/';
        if (this.originalMaterial) {
          materialsListUrl += this.originalMaterial.id;
        }
        console.log('FETCHING: ' + Vue.axios.defaults.baseURL + materialsListUrl)
        Vue.axios.get(Vue.axios.defaults.baseURL + materialsListUrl)
          .then((response) => {
          if (response.data.error) {
            this.apiError = response.data.error
            console.log(this.apiError)
            this.isProcessing = false
          } else {
            this.isProcessing = false
            this.materialLibrary = response.data.data;
            console.log('GOT MATERIALS LIST LEN: ' + this.materialLibrary.length);
            this.lookupMaterialLibrary = {};
            this.selectMaterials = [];
            for (var i = 0; i < this.materialLibrary.length; i++) {
              this.lookupMaterialLibrary[this.materialLibrary[i].id] = this.materialLibrary[i];
              this.selectMaterials.push({value: this.materialLibrary[i].id, label: this.materialLibrary[i].name});
            }
          }
        })
        .catch(response => {
          if (response.response && response.response.status) {
            if (response.response.status === 401) {
              this.$router.push({ path: 'login', query: { error: 401 }})
            } else {
              this.serverError = response.response.message;
            }
          }
          this.isProcessing = false
        })
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
              && Number(this.materialFieldsAmount[i]) > 0
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
          this.$emit('isProcessing')
          this.isProcessing = true

          var form = {
            materialComponents: []
          }

          for (var i = 0; i < this.materialFieldsId.length; i++) {
            if (this.materialFieldsId[i] && this.materialFieldsId[i].value
              && !isNaN(this.materialFieldsAmount[i]
                && this.materialFieldsAmount[i] > 0)
            ) {
              form.materialComponents.push({
                componentMaterialId: Number(this.materialFieldsId[i].value),
                percentageAmount: Number(this.materialFieldsAmount[i]),
                isAdditional: Boolean(this.materialFieldsIsAdditional[i])
              })
            }
          }

          if (this.newMaterial) {
            form._method = 'PATCH';
            // form.materialComponents = JSON.stringify(form.materialComponents)
            console.log('updating: form:')
            console.log(form)
            console.log('url: ' + Vue.axios.defaults.baseURL + '/materialmaterials/' + this.originalMaterial.id)
            Vue.axios.post(Vue.axios.defaults.baseURL + '/materialmaterials/' + this.originalMaterial.id, form)
              .then((response) => {
              console.log('got response:')
              console.log(response)
              if (response.data.error) {
                // error
                this.apiError = response.data.error
                console.log(this.apiError)
              } else {
                console.log('emit updatedRecipeComponents')
                this.$emit('updatedRecipeComponents');
              }
            })
            .catch(response => {
              this.serverError = response;
              console.log('UPDATE ERROR')
              console.log(response.data)
            })
          } else {
            Vue.axios.post(Vue.axios.defaults.baseURL + '/materialmaterials/', form)
              .then((response) => {
              console.log('got response:')
              console.log(response)
              if (response.data.error) {
                // error
                this.apiError = response.data.error
                console.log(this.apiError)
              } else {
                console.log('emit updatedRecipeComponents')
                this.$emit('updatedRecipeComponents')
                // TODO:
              }
            })
            .catch(response => {
              this.serverError = response;
              console.log('UPDATE ERROR')
              console.log(response.data)
            })
          }
        }
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
        if (this.isLoaded) {
          // this.chartHeight = document.getElementById('umf-d3-chart-container').clientHeight
          this.chartWidth = document.getElementById('umf-d3-chart-container').clientWidth
        }
      },

      editComponentsCancel: function() {
        this.$emit('editComponentsCancel');
      }

    }
  }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style>

  .edit-recipe-title-row {
    margin-top: 1rem;
  }

  .edit-recipe-title {
    margin-top: 0;
    margin-bottom: 0;
  }

  .cancel-edit-btn {
    margin: 0;
  }

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