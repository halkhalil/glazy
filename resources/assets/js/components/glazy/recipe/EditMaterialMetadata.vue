<template>
<div class="row edit-material-metadata">
    <div class="col-md-12">
        <b-alert v-if="apiError" show variant="danger">
            API Error: {{ apiError.message }}
        </b-alert>
        <b-alert v-if="serverError" show variant="danger">
            Server Error: {{ serverError }}
        </b-alert>
        <form  role="form" method="POST" v-if="isLoaded">
            <div>
                <h3 v-if="isNewMaterial" class="card-title">
                    Add New Material
                </h3>
                <h3 v-else-if="isNewAnalysis" class="card-title">
                    Add New Analysis
                </h3>
                <h3 v-else class="card-title">
                    Edit {{ form.name }}
                </h3>
            </div>

            <b-form-group
                    id="groupName"
                    description="No need to add the cone temp here!"
                    label="Name"
                    label-for="name"
                    :feedback="feedbackName"
                    :state="stateName"
            >
                <b-form-input id="name" :state="stateName" v-model.trim="form.name"></b-form-input>
            </b-form-group>

            <b-form-group
                    id="groupOtherNames"
                    description="You can also add a Code here."
                    label="Other Names or Code (Optional)"
                    label-for="otherNames"
            >
                <b-form-input id="otherNames" v-model.trim="form.otherNames"></b-form-input>
            </b-form-group>

            <b-form-group
                    id="groupDescription"
                    description="Here you can add glaze preparation, firing notes, etc."
                    label="Description (Optional)"
                    label-for="description"
            >
                <b-form-textarea id="description"
                                 v-model="form.description"
                                 placeholder="Type description here.."
                                 :rows="6"
                                 :max-rows="10">
                </b-form-textarea>
            </b-form-group>

            <b-row class="mt-2">
                <b-col md="6" v-if="!material.isPrimitive">
                    <label for="baseTypeId">Type</label>
                    <b-form-select
                            class="col"
                            id="baseTypeId"
                            v-model="form.baseTypeId"
                            :options="baseTypeOptions"
                            @input="updateBaseType"
                    >
                    </b-form-select>
                </b-col>
                <b-col md="6" v-if="subTypeOptions && subTypeOptions.length > 0">
                    <label for="materialTypeIdSelect">Subtype</label>
                    <b-form-select
                            class="col"
                            id="materialTypeIdSelect"
                            v-model="form.materialTypeId"
                            :options="subTypeOptions">
                    </b-form-select>
                </b-col>
            </b-row>

            <b-row class="mt-2" v-if="!material.isPrimitive">
                <b-col md="6">
                    <label for="transparencyTypeId">Transparency</label>
                    <b-form-select
                            id="transparencyTypeId"
                            placeholder="Transparency"
                            v-model="form.transparencyTypeId"
                            :options="constants.TRANSPARENCY_SELECT"
                            >
                        <template slot="first">
                            <option :value="0">All Transparencies</option>
                        </template>
                    </b-form-select>
                </b-col>
                <b-col md="6">
                    <label for="surfaceTypeId">Surface</label>
                    <b-form-select
                            id="surfaceTypeId"
                            placeholder="Surface"
                            v-model="form.surfaceTypeId"
                            :options="constants.SURFACE_SELECT"
                            >
                        <template slot="first">
                            <option :value="0">All Surfaces</option>
                        </template>
                    </b-form-select>
                </b-col>
            </b-row>

            <b-row class="mt-2" v-if="!material.isPrimitive">
                <b-col md="4" sm="6">
                    <label for="fromOrtonConeId">Lowest Cone</label>
                    <b-form-select
                            v-model="form.fromOrtonConeId"
                            id="fromOrtonConeId"
                            :options="constants.ORTON_CONES_SELECT_TEXT_SIMPLE"
                            @input="updateFromCone">
                    </b-form-select>
                </b-col>
                <b-col md="4" sm="6">
                    <label for="toOrtonConeId">Highest Cone</label>
                    <b-form-select
                            v-model="form.toOrtonConeId"
                            id="toOrtonConeId"
                            :options="constants.ORTON_CONES_SELECT_TEXT_SIMPLE"
                            @input="updateToCone">
                    </b-form-select>
                </b-col>
            </b-row>

            <b-row class="mt-2" v-if="!material.isPrimitive">
                <b-col md="12">
                    <b-form-group
                            id="groupAtmospheres"
                            label="Atmospheres (Optional)"
                            label-for="atmospheres"
                    >
                        <b-form-checkbox-group id="atmospheres"
                                               v-model="form.atmospheres"
                                               :options="atmospheres"
                                               plain />
                    </b-form-group>
                </b-col>
            </b-row>

            <b-row class="mt-2">
                <b-col md="6">
                    <label for="countryId">Country (Optional)</label>
                    <b-form-select
                            class="col"
                            id="countryId"
                            v-model="form.countryId"

                            :options="COUNTRY_SELECT">
                        <template slot="first">
                            <option :value="0">No Country</option>
                        </template>
                    </b-form-select>
                </b-col>
            </b-row>

            <b-row class="mt-2 percent-analysis-oxide"  v-if="material.isPrimitive || material.isAnalysis">
                <b-col cols="12" class="mt-4">
                  <h3>Analysis</h3>
                </b-col>

                <b-col cols="12">
                  <b-form-group label="Analysis Type:" label-for="analysisTypeRadios">
                    <b-form-radio-group 
                      id="analysisTypeRadios" 
                      v-model="enteringFormulaType" 
                      name="analysisTypeRadioGroup"
                      plain>
                      <b-form-radio value="percentage">Percentage Analysis</b-form-radio>
                      <b-form-radio value="formula">Formula</b-form-radio>
                    </b-form-radio-group>
                  </b-form-group>
                </b-col>

                <b-col cols="12" v-if="enteringFormulaType === 'percentage'">
                  <b-form-group label="Unity Type for Formula:" label-for="unityTypeSelect">
                    <b-form-select
                        id="unityTypeSelect" 
                        @input="updateAnalysis"
                        v-model="unityType">
                      <option value="auto" selected>Automatically determine unity</option>
                      <option value="ror2o">RO/R2O Unity (Flux, UMF)</option>
                      <option value="r2o3">R2O3 Unity</option>
                      <option value="none">No Unity</option>
                    </b-form-select>
                  </b-form-group>
                </b-col>

                <b-col cols="12">
                  <b-form-group label="Enter either LOI or Weight:" label-for="loiWeightRadios">
                      <b-form-radio-group 
                        id="loiWeightRadios" 
                        v-model="loiWeightSelector" 
                        name="loiWeightRadioGroup"
                        plain>
                        <b-form-radio value="loi">
                          LOI
                          <b-form-input id="loi"
                          type="number"
                          size="sm"
                          v-model.trim="form.loi"
                          @change="updateAnalysis"
                          :disabled="!(loiWeightSelector === 'loi')"
                          ></b-form-input>                        
                        </b-form-radio>
                        <b-form-radio value="weight">
                          Weight
                          <b-form-input id="weight"
                            type="number"
                            size="sm"
                            v-model.trim="form.weight"
                            @change="updateAnalysis"
                            :disabled="!(loiWeightSelector === 'weight')"
                          ></b-form-input>
                        </b-form-radio>
                      </b-form-radio-group>
                  </b-form-group>
                </b-col>
                <b-col cols="12">
                  Calculated Oxide Weight: {{ Number(oxideWeight).toFixed(3) }}
                </b-col>
                <b-col cols="12" class="mt-2">
                  <table class="">
                    <thead>
                      <tr>
                        <th></th>
                        <th>% Analysis</th>
                        <th>
                          Formula
                          <span v-if="enteringFormulaType === 'percentage'">
                            <span v-if="unityType === 'auto'">(Auto)</span>
                            <span v-else-if="unityType === 'ror2o'">(UMF)</span>
                            <span v-else-if="unityType === 'r2o3'">(R2O3)</span>
                            <span v-else>(No Unity)</span>
                          </span>
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(oxideName, index) in OXIDE_NAMES" :key="index">
                      <td>
                        <span v-bind:class="'oxide-colors-' + oxideName"
                            v-html="OXIDE_NAME_DISPLAY[oxideName]"></span>
                      </td>
                      <td>
                        <b-form-input id="oxideName"
                                      type="number"
                                      size="sm"
                                      v-model="percentageAnalysis[oxideName]"
                                      :disabled="(enteringFormulaType === 'formula')"
                                      @change="updateAnalysis"
                                      ></b-form-input>
                      </td>
                      <td>
                        <b-form-input id="oxideName"
                                      type="number"
                                      size="sm"
                                      v-model="formulaAnalysis[oxideName]"
                                      :disabled="(enteringFormulaType === 'percentage')"
                                      @change="updateAnalysis"
                                      ></b-form-input>
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </b-col>
            </b-row>

            <b-form-group id="groupButtons">
                <b-button class="float-right"
                          size="sm"
                          variant="primary"
                          @click.prevent="save">
                    <i class="fa fa-save"></i> Save
                </b-button>
                <b-button class="float-right"
                          size="sm"
                          variant="secondary"
                          @click.prevent="cancelEdit()"><i class="fa fa-times"></i> Cancel</b-button>
            </b-form-group>
        </form>

    </div>
</div>

</template>


<script>
  import Analysis from 'ceramicscalc-js/src/analysis/Analysis'
  import FormulaAnalysis from 'ceramicscalc-js/src/analysis/FormulaAnalysis'
  import PercentageAnalysis from 'ceramicscalc-js/src/analysis/PercentageAnalysis'
  import Material from 'ceramicscalc-js/src/material/Material'

  import MaterialTypes from 'ceramicscalc-js/src/material/MaterialTypes'
  import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'

  export default {
    props: {
      material: {
        type: Object,
        default: null
      },
      isNewMaterial: {
        type: Boolean,
        default: false
      },
      isNewAnalysis: {
        type: Boolean,
        default: false
      }
    },
    components: {
    },
    data() {
      return {
        form: {
          isPrimitive: false,
          isAnalysis: false,
          name: '',
          otherNames: '',
          description: '',
          baseTypeId: null,
          materialTypeId: null,
          transparencyTypeId: null,
          surfaceTypeId: null,
          fromOrtonConeId: null,
          toOrtonConeId: null,
          atmospheres: [],
          countryId: null,
          analysis: null,
          formula: null,
          loi: null,
          weight: null
        },
        ceramicsCalcMaterial: null,        
        percentageAnalysis: null,
        formulaAnalysis: null,
        enteringFormulaType: 'percentage',
        loiWeightSelector: 'loi',
        oxideWeight: 0.0,
        unityType: 'auto',
        // isPrimitive: false,
        errors: [],
        apiError: null,
        serverError: null,
        constants: GlazyConstants,
        materialTypes: MaterialTypes,
        atmospheres: GlazyConstants.ATMOSPHERE_SELECT,
        COUNTRY_SELECT: GlazyConstants.COUNTRY_SELECT,
        OXIDE_NAMES: Analysis.OXIDE_NAMES,
        OXIDE_NAME_DISPLAY: Analysis.OXIDE_NAME_DISPLAY
      }
    },
    created() {
      if (this.material) {
        this.form = {
          isPrimitive: this.material.isPrimitive,
          isAnalysis: this.material.isAnalysis,
          id: this.material.id,
          name: this.material.name,
          otherNames: this.material.otherNames,
          description: this.material.description,
          baseTypeId: this.material.baseTypeId,
          materialTypeId: this.material.materialTypeId,
          transparencyTypeId: this.material.transparencyTypeId,
          surfaceTypeId: this.material.surfaceTypeId,
          fromOrtonConeId: this.material.fromOrtonConeId,
          toOrtonConeId: this.material.toOrtonConeId,
          atmospheres: [],
          countryId: this.material.countryId,
          loi: this.material.analysis.percentageAnalysis.loi,
          weight: this.material.analysis.weight
        }
        this.ceramicsCalcMaterial = Material.createFromJson(this.material)
        this.percentageAnalysis = this.ceramicsCalcMaterial.getPercentageAnalysis()
        this.formulaAnalysis = this.ceramicsCalcMaterial.getFormulaAnalysis()
        this.formatOxideArrays()

        if (this.material.atmospheres) {
          for (var i = 0; i < this.material.atmospheres.length; i++) {
            this.form.atmospheres.push(this.material.atmospheres[i].id);
          }
        }
      }
    },
    watch: {
    },
    computed: {
      isLoaded: function () {
        //if (this.material || this.isNewMaterial || this.isNewAnalysis) {
        if (this.material) {
          return true;
        }
        return false;
      },
      feedbackName() {
        return (this.form.name && this.form.name.length > 0) ?
          'Enter at least 3 characters' : 'Please enter a name';
      },
      stateName() {
        return (this.form.name && this.form.name.length > 2) ? 'valid' : 'invalid';
      },
      baseTypeOptions: function () {
        return this.materialTypes.getParentTypes();
      },
      subTypeOptions: function () {
        if (this.isLoaded) {
          if (this.material.isPrimitive) {
            // return this.materialTypes.PRIMITIVE_SELECT
            return [{ value: 1, text: 'Simple Material' }].concat(this.materialTypes.PRIMITIVE_SELECT)
            // return this.materialTypes.PRIMITIVE_SELECT.unshift({ value: 1, text: 'Simple Material' })
          } else {
            switch (this.form.baseTypeId) {
              case this.materialTypes.GLAZE_TYPE_ID:
                return this.materialTypes.getGlazeTypes()
              case this.materialTypes.CLAYS_TYPE_ID:
                return this.materialTypes.getClayTypes()
              case this.materialTypes.SLIPS_TYPE_ID:
                return this.materialTypes.getSlipTypes()
            }
          }
        }
        return null
      }
      /*
      calculated formula weight depends upon unity type..
      oxideWeight: function () {
        if (this.isLoaded && this.material.isPrimitive) {
          var percentageAnalysis = new PercentageAnalysis()
          percentageAnalysis.setOxides(this.form.analysis)
          percentageAnalysis.setLOI(this.form.loi)
          var formulaAnalysis = FormulaAnalysis.createNoUnityFormula(percentageAnalysis)

          return formulaAnalysis.getFormulaWeight()
        }
        return null
      }
      */
    },
    methods: {
      save: function () {
        if (this.isLoaded) {
          window.scrollTo(0, 0)
          this.$emit('isProcessing');

          if (!this.form.materialTypeId && this.form.baseTypeId) {
            this.form.materialTypeId = this.form.baseTypeId
          }

          var url = Vue.axios.defaults.baseURL + '/recipes'
          if (!this.isNewMaterial && !this.isNewAnalysis) {
            this.form._method = 'PATCH'
            url = Vue.axios.defaults.baseURL + '/recipes/' + this.material.id
          }

          if ((this.material.isPrimitive || this.material.isAnalysis) &&
              (this.percentageAnalysis || this.formulaAnalysis)) {
            if (this.enteringFormulaType === 'percentage') {
              this.form.analysis = this.percentageAnalysis
              // Remove loi from analysis (already set in form.loi):
              delete this.form.analysis['loi'];
              // How should the formula be made?
              this.form.unityType = this.unityType;
            }
            else {
              this.form.formula = this.formulaAnalysis
            }
            if (this.loiWeightSelector === 'weight') {
              // Don't include calculated LOI, use weight directly inputted by user
              this.form.loi = null
            }
            else {
              // Don't include calculated weight, use LOI directly inputted by user
              this.form.weight = null
            }
          }

          Vue.axios.post(url, this.form)
            .then((response) => {
              if (response.data.error) {
                // error
                this.apiError = response.data.error
              } else {
                this.recipe = null
                this.recipe = response.data.data
                if ('data' in response.data && 'id' in response.data.data) {
                  // Emit with the ID of the updated/saved recipe/material/analysis
                  var recipeType = 'recipes'
                  if (this.isNewMaterial) {
                    recipeType = 'material'
                  }
                  else if (this.isNewAnalysis) {
                    recipeType = 'analysis'
                  }
                  this.$emit('updatedRecipeMeta', response.data.data.id, recipeType)
                }
                else {
                  this.$emit('updatedRecipeMeta')
                }
              }
            })
            .catch(response => {
              if (response.response && response.response.status) {
                if (response.response.status === 401) {
                  this.$router.push({ path: '/login', query: { error: 401 }})
                } else {
                  this.serverError = response.response.data.error.message;
                }
              }
              this.isProcessing = false
            })
        }
      },

      cancelEdit: function () {
        window.scrollTo(0, 0)
        this.$emit('editMetaCancel');
      },

      updateBaseType () {
        this.form.materialTypeId = 0
      },

      updateFromCone () {
        if (this.form.toOrtonConeId && this.form.toOrtonConeId < this.form.fromOrtonConeId) {
          this.form.toOrtonConeId = this.form.fromOrtonConeId
        }
      },

      updateToCone () {
        if (this.form.fromOrtonConeId && this.form.fromOrtonConeId > this.form.toOrtonConeId) {
          this.form.fromOrtonConeId = this.form.toOrtonConeId
        }
      },

      updateAnalysis () {
        if (this.enteringFormulaType === 'percentage') {
          this.ceramicsCalcMaterial.setPercentageAnalysis(this.percentageAnalysis)
          if (this.unityType === 'auto') {
            this.formulaAnalysis = this.ceramicsCalcMaterial.getAutomaticUnityFormula()
          }
          else if (this.unityType === 'ror2o') {
            this.formulaAnalysis = this.ceramicsCalcMaterial.getUmfAnalysis()
          }
          else if (this.unityType === 'r2o3') {
            this.formulaAnalysis = this.ceramicsCalcMaterial.getR2O3UnityFormulaAnalysis()
          }
          else {
            // No unity
            this.formulaAnalysis = this.ceramicsCalcMaterial.getFormulaAnalysis()
          }
          this.formatOxideArray(this.formulaAnalysis)
        }
        else {
          this.oxideWeight = this.formulaAnalysis.getFormulaWeight()
          if (this.loiWeightSelector === 'weight') {
            // Need LOI before we can get the percentage analysis:
            this.form.loi = this.formulaAnalysis.getCalculatedLoiFromWeight(Number(this.form.weight))
            // this.form.loi = (Number(this.form.weight) - this.oxideWeight) /  this.form.weight * 100
          }
          this.ceramicsCalcMaterial.setFormulaAnalysis(this.formulaAnalysis, this.form.loi)
          this.percentageAnalysis = this.ceramicsCalcMaterial.getPercentageAnalysis()
          this.formatOxideArray(this.percentageAnalysis)
        }
        this.oxideWeight = this.formulaAnalysis.getFormulaWeight()
        if (this.loiWeightSelector === 'weight') {
          // Calculate the LOI using weight & formula weight
          this.form.loi = this.formulaAnalysis.getCalculatedLoiFromWeight(Number(this.form.weight))
          // this.form.loi = (Number(this.form.weight) - this.oxideWeight) /  this.form.weight * 100
        }
        else {
          // Calculate weight using formula weight & LOI
          this.form.weight = this.formulaAnalysis.getCalculatedWeightFromLoi(Number(this.form.loi))
          // this.form.weight = this.oxideWeight / (100 - Number(this.form.loi)) * 100
        }
      },

      formatOxideArrays() {
        this.formatOxideArray(this.percentageAnalysis)
        this.formatOxideArray(this.formulaAnalysis)
      },

      formatOxideArray (myOxides) {
        Analysis.OXIDE_NAMES.forEach((oxideName) => {
          if (myOxides[oxideName] <= 0) {
            // don't keep zeros for form
            myOxides[oxideName] = ''
          } else {
            myOxides[oxideName] = Number(myOxides[oxideName]).toFixed(3)
          }
        })
      }
    }
  }
</script>

<style>

    .edit-material-metadata label {
        margin-bottom: 0;
    }

    .percent-analysis-oxide .col {
        padding: 0 5px;
    }
</style>