<template>
  <div class="calc-container" v-if="isLoaded">
    <div class="row edit-recipe-title-row" v-if="originalMaterial">
      <div class="col-md-12">
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
      <div class="col-md-8">
        <b-card no-body class="analysis-card">
          <b-tabs ref="tabs" card>
            <b-tab title="UMF" active>
              <div v-if="!hasMaterials">
                No analysis.  Please add materials.
              </div>
              <div v-else>
                <table class="table-analysis-layout">
                  <tr v-if="originalMaterial && originalUMFAnalysis">
                    <td>
                      <umf-traditional-notation
                              :material="originalMaterial"
                              :isSmall="false">
                      </umf-traditional-notation>
                    </td>
                    <td class="text-right">
                      <div class="card card-umf-info card-plain">
                        <div class="card-body">
                          <h6 class="card-title">R<sub>2</sub>O : RO</h6>
                          <p class="card-text">
                          <span class="oxide-colors-Na2O">
                            {{ Number(originalUMFAnalysis.getR2OTotal()).toFixed(2) }}
                          </span>
                            :
                            <span class="oxide-colors-CaO">
                            {{ Number(originalUMFAnalysis.getROTotal()).toFixed(2) }}
                          </span>
                          </p>
                        </div>
                      </div>

                      <div class="card card-umf-info card-plain">
                        <div class="card-body">
                          <h6 class="card-title">SiO<sub>2</sub> : Al<sub>2</sub>O<sub>3</sub></h6>
                          <p class="card-text">
                            {{ Number(originalUMFAnalysis.getSiO2Al2O3Ratio()).toFixed(2) }}
                          </p>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="newMaterial && newUMFAnalysis">
                    <td>
                      <umf-traditional-notation
                              class="mt-1"
                              :material="newMaterial"
                              :showLegend="false"
                              :isSmall="false">
                      </umf-traditional-notation>
                    </td>
                    <td class="text-right">
                      <div class="card card-umf-info card-plain">
                        <div class="card-body">
                          <h6 class="card-title">R<sub>2</sub>O : RO</h6>
                          <p class="card-text">
                          <span class="oxide-colors-Na2O">
                            {{ Number(newUMFAnalysis.getR2OTotal()).toFixed(2) }}
                          </span>
                            :
                            <span class="oxide-colors-CaO">
                            {{ Number(newUMFAnalysis.getROTotal()).toFixed(2) }}
                          </span>
                          </p>
                        </div>
                      </div>

                      <div class="card card-umf-info card-plain">
                        <div class="card-body">
                          <h6 class="card-title">SiO<sub>2</sub> : Al<sub>2</sub>O<sub>3</sub></h6>
                          <p class="card-text">
                            {{ Number(newUMFAnalysis.getSiO2Al2O3Ratio()).toFixed(2) }}
                          </p>
                        </div>
                      </div>
                    </td>
                  </tr>
                </table>
              </div>
            </b-tab>
            <b-tab title="% Mol">
              <div v-if="!hasMaterials">
                No analysis.  Please add materials.
              </div>
              <div v-else class="table-responsive">
                <MaterialAnalysisPercentTableCompare
                        :originalMaterial="originalMaterial"
                        :newMaterial="newMaterial"
                        :showHeadings="false"
                        :isPercentMol="true">
                </MaterialAnalysisPercentTableCompare>
              </div>
            </b-tab>
            <b-tab title="%">
              <div v-if="!hasMaterials">
                No analysis.  Please add materials.
              </div>
              <div v-else class="table-responsive">
                <MaterialAnalysisPercentTableCompare
                        :originalMaterial="originalMaterial"
                        :newMaterial="newMaterial"
                        :showHeadings="false"
                        :isPercentMol="false">
                </MaterialAnalysisPercentTableCompare>
              </div>
            </b-tab>
          </b-tabs>
        </b-card>
      </div>

      <div class="col-md-4 chart-col">
        <b-card no-body class="chart-card">
          <div v-if="this.originalMaterial && chartMaterials && isLoaded" id="umf-d3-chart-container">
            <umf-d3-chart
                    :recipeData="[this.originalMaterial, this.newMaterial]"
                    :currentRecipeId="this.originalMaterial.id"
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
          <div v-if="!this.originalMaterial && chartMaterials && isLoaded" id="umf-d3-chart-container">
            <umf-d3-chart
                    :recipeData="[this.newMaterial]"
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
                  @input="focusAmountInput(index)"
                  key="value"
                  label="label"
                  selectLabel=""
          ></multiselect>
        </div>

        <div class="col-sm-3">
          <b-form-input :id="index + '_amount'"
                        v-model="materialFieldsAmount[index]"
                        type="number"
                        min="0"
                        placeholder="%"
                        v-focus="index === focused"
                        @focus="focused = index"
                        @blur="focused = null"
                        @change="focused = index"
                        @input="updateMaterial"></b-form-input>
        </div>
        <div class="col-sm-2">
          <b-form-checkbox id="index + '_add'"
                           v-model="materialFieldsIsAdditional[index]"
                           @input="updateMaterial"
                           plain />
        </div>
      </div>
      <div class="row">
        <div class="col-sm-7">
          <b-button v-if="$auth.check()"
                    class="cancel-edit-btn"
                    size="sm"
                    variant="secondary"
                    @click.prevent="editComponentsCancel"><i class="fa fa-times"></i> Cancel</b-button>
          <b-button size="sm"
                    variant="secondary"
                    @click.prevent="resetRecipe"><i class="fa fa-refresh"></i> Reset</b-button>
          <b-button size="sm"
                    variant="info"
                    @click.prevent="checkForDuplicates"><i class="fa fa-search"></i> Search</b-button>
          <b-button v-if="$auth.check()"
                    size="sm"
                    variant="info"
                    @click.prevent="store"><i class="fa fa-save"></i> Save</b-button>
          <br/>Can't find a material?  <a href="http://help.glazy.org/guide/calculator/#why-are-materials-missing-from-the-calculator" target="_blank">How to add a material to the calculator.</a>
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
    </b-card>

    <div class="row" v-if="isProcessingDuplicates">
      <div class="col-sm-12">
        <h5>Similar Recipes</h5>
        <p class="description">Searching for Similar Recipes...</p>
      </div>
    </div>

    <div class="row" v-if="similarMaterials && !isProcessingDuplicates">
      <div class="col-sm-12">
        <h5>Similar Recipes <em>(Limited to first 40 recipes)</em></h5>
        <p v-if="!similarMaterials || !similarMaterials.length" class="description">No similar recipes found.</p>
      </div>
      <div class="col-md-4 col-sm-6" v-for="material in similarMaterials">
        <material-card-detail
                :material="material"
                :isEmbedded="true"
                :showCollapse="true"
        ></material-card-detail>
      </div>
    </div>
  </div>
</template>

<script>
  import MaterialTypes from 'ceramicscalc-js/src/material/MaterialTypes'
  import Analysis from 'ceramicscalc-js/src/analysis/Analysis'
  import PercentageAnalysis from 'ceramicscalc-js/src/analysis/PercentageAnalysis'
  import Material from 'ceramicscalc-js/src/material/Material'
  import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'

  import UmfD3Chart from 'vue-d3-stull-charts/src/components/UmfD3Chart.vue'

  import MaterialAnalysisTableCompare from '../analysis/MaterialAnalysisTableCompare.vue'
  import MaterialAnalysisUmfSpark2Single from '../analysis/MaterialAnalysisUmfSpark2Single.vue'
  import MaterialAnalysisPercentTableCompare from '../analysis/MaterialAnalysisPercentTableCompare.vue'
  import JsonUmfSparkSvg from '../analysis/JsonUmfSparkSvg.vue'
  import UmfTraditionalNotation from '../analysis/UmfTraditionalNotation.vue'

  import MaterialCardDetail from '../search/MaterialCardDetail.vue'

  import Multiselect from 'vue-multiselect'

  import { focus } from 'vue-focus'

  export default {
    name: 'EditRecipeComponents',
    components: {
      Multiselect,
      UmfD3Chart,
      MaterialAnalysisTableCompare,
      MaterialAnalysisPercentTableCompare,
      JsonUmfSparkSvg,
      UmfTraditionalNotation,
      MaterialCardDetail
    },
    props: {
      originalMaterial: {
        type: Object,
        default: null
      }
    },
    directives: { focus: focus },
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
        isProcessingDuplicates: false,
        similarMaterials: null,
        chartHeight: 240,
        chartWidth: 0,
        apiError: null,
        serverError: null,
        focused: null
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

      hasMaterials: function() {
        if (this.newMaterial.materialComponents.length > 0) {
          return true
        }
        return false
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
      this.fetchPrimitiveMaterials();

      if (this.originalMaterial) {
        this.newMaterial = this.originalMaterial.clone()
        this.newMaterial.id = 0
      }
      else {
        this.newMaterial = new Material()
        this.newMaterial.setName('New Recipe')
      }

      this.resetMaterialFields()
      this.setSubtotal()

      setTimeout(() => {
        this.handleResize()
      }, 300);
      window.addEventListener('resize', this.handleResize)

      setTimeout(() => {
        this.checkForDuplicates()
      }, 2000);
    },


    methods: {

      focusAmountInput (index) {
        this.focused = index
        this.updateMaterial()
      },
      /*
      moveDown: function() {
        this.focused = Math.min(this.focused + 1, this.materialFieldsId.length - 1);
      },
      moveUp: function() {
        this.focused = Math.max(this.focused - 1, 0);
      },
      */
      updateMaterial () {
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
        this.setSubtotal()
      },

      fetchPrimitiveMaterials : function() {
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
            this.lookupMaterialLibrary = {}
            this.selectMaterials = []
            for (var i = 0; i < this.materialLibrary.length; i++) {
              this.lookupMaterialLibrary[this.materialLibrary[i].id] = this.materialLibrary[i]
              var fullName = this.materialLibrary[i].name
              if (this.materialLibrary[i].otherNames) {
                fullName += ', ' + this.materialLibrary[i].otherNames
              }
              this.selectMaterials.push({value: this.materialLibrary[i].id, label: fullName})
            }
          }
        })
        .catch(response => {
          if (response.response && response.response.status) {
            if (response.response.status === 401) {
              this.$router.push({ path: '/login', query: { error: 401 }})
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
        this.updateMaterial();
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

      store: function () {
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

          if (this.originalMaterial) {
            // We are updating the original material
            form._method = 'PATCH';
            // form.materialComponents = JSON.stringify(form.materialComponents)
            Vue.axios.post(Vue.axios.defaults.baseURL + '/materialmaterials/' + this.originalMaterial.id, form)
              .then((response) => {
              if (response.data.error) {
                // error
                this.apiError = response.data.error
                console.log(this.apiError)
              } else {
                this.$emit('updatedRecipeComponents');
              }
            })
            .catch(response => {
              this.serverError = response;
              console.log('UPDATE ERROR')
              console.log(response.data)
            })
          } else {
            // Create a new recipe
            if (form.materialComponents.length === 0) {
              // Don't create a new material that has no components
              // TODO: Add warning message
              this.isProcessing = false
              return
            }
            Vue.axios.post(Vue.axios.defaults.baseURL + '/materialmaterials/', form)
              .then((response) => {
              if (response.data.error) {
                // error
                this.apiError = response.data.error
                this.isProcessing = false
              } else {
                // Success creating recipe, now go to the recipe page:
                var material = response.data.data
                this.$router.push({ name: 'recipes', params: { id: material.id }, query: { isEdit: true }})
              }
            })
            .catch(response => {
              this.serverError = response;
              this.isProcessing = false
              console.log('UPDATE ERROR')
              console.log(response.data)
            })
          }
        }
      },

      checkForDuplicates: function() {
        if (!this.isLoaded) {
          return
        }
        this.isProcessingDuplicates = true
        this.similarMaterials = null;

        var form = {
          excludeMaterialId: null,
          materialComponents: []
        };

        if (this.originalMaterial) {
          form.excludeMaterialId = this.originalMaterial.id;
        }

        // Check each material component for id and amount
        for (var i = 0; i < this.materialFieldsId.length; i++) {
          if (this.materialFieldsId[i] && this.materialFieldsId[i].value) {
            if (!isNaN(this.materialFieldsAmount[i] && Number(this.materialFieldsAmount[i]) > 0)) {
              form.materialComponents.push({
                componentMaterialId: this.materialFieldsId[i].value,
                percentageAmount: Number(this.materialFieldsAmount[i]),
                isAdditional: this.materialFieldsIsAdditional[i]
              });
            } else {
              form.materialComponents.push({
                componentMaterialId: this.materialFieldsId[i].value,
                percentageAmount: 0,
                isAdditional: false
              });
            }
          }
        }
        if (form.materialComponents.length > 0) {
          // Only search if we have at least one material component
          Vue.axios.post(Vue.axios.defaults.baseURL + '/search/similarMaterials', form)
            .then((response) => {
            if (response.data.error) {
              console.log('dups error')
              this.apiError = response.data.error
              console.log(this.apiError)
              this.isProcessingDuplicates = false
            } else {
              this.isProcessingDuplicates = false
              this.similarMaterials = response.data.data;
            }
          })
          .catch(response => {
            if (response.response && response.response.status) {
              if (response.response.status === 401) {
                this.$router.push({ path: '/login', query: { error: 401 }})
              } else {
                this.serverError = response.response.message;
              }
            }
            this.isProcessingDuplicates = false
          })
        }
        else {
          this.isProcessingDuplicates = false
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


<style>

  .edit-recipe-title-row {
    margin-bottom: 10px;
  }

  .edit-recipe-title {
    margin-top: 0;
    margin-bottom: 0;
  }

  .cancel-edit-btn {
    margin: 0;
  }

  .calc-container {
  }

  .calc-row {
  }

  .chart-col {
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

  .table-analysis-layout tr td {
    padding: 4px;
  }

  .card-umf-info {
    background-color: #efefef;
    max-width: 7em;
    min-width: 6em;
    margin-bottom: 10px;
  }

  .card-umf-info .card-body {
    padding: 5px;
    text-align: center;
  }

  .card-umf-info .card-body .card-title {
    font-size: .8em;
    color: #999999;
    margin: 0;
    text-transform: none;
  }

  .card-umf-info .card-body .card-text {
    font-size: 1.2em;
  }

.dropdown .dropdown-menu {
  z-index: 999;
}
.v-select .dropdown-menu {
  z-index: 999;
}

</style>