<template>
<div class="row edit-recipe-metadata">
    <div class="col-md-12">
        <b-alert v-if="apiError" show variant="danger">
            API Error: {{ apiError.message }}
        </b-alert>
        <b-alert v-if="serverError" show variant="danger">
            Server Error: {{ serverError }}
        </b-alert>
        <form  role="form" method="POST" v-if="isLoaded">
            <div>
                <h3 class="card-title">
                    Edit {{ form.name }}
                </h3>
            </div>

            <b-form-group
                    id="groupName"
                    description="No need to add the cone temp here!"
                    label="Recipe Name"
                    :feedback="feedbackName"
                    :state="stateName"
            >
                <b-form-input id="name" :state="stateName" v-model.trim="form.name"></b-form-input>
            </b-form-group>

            <b-form-group
                    id="groupOtherNames"
                    description="You can also add a Code here."
                    label="Other Names or Code"
            >
                <b-form-input id="otherNames" v-model.trim="form.otherNames"></b-form-input>
            </b-form-group>

            <b-form-group
                    id="groupDescription"
                    description="Here you can add glaze preparation, firing notes, etc."
                    label="Description (Optional)"
            >
                <b-form-textarea id="description"
                                 v-model="form.description"
                                 placeholder="Type description here.."
                                 :rows="6"
                                 :max-rows="10">
                </b-form-textarea>
            </b-form-group>

            <b-row class="mt-2">
                <b-col md="6" v-if="!recipe.isPrimitive">
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
                    <label for="materialTypeId">Subtype</label>
                    <b-form-select
                            class="col"
                            id="materialTypeId"
                            v-model="form.materialTypeId"
                            :options="subTypeOptions">
                    </b-form-select>
                </b-col>
            </b-row>

            <b-row class="mt-2" v-if="!recipe.isPrimitive">
                <b-col md="6">
                    <label for="transparencyTypeId">Transparency</label>
                    <b-form-select
                            size="sm"
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
                            size="sm"
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

            <b-row class="mt-2" v-if="!recipe.isPrimitive">
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

            <b-row class="mt-2" v-if="!recipe.isPrimitive">
                <b-col md="12">
                    <b-form-group
                            id="groupAtmospheres"
                            label="Atmospheres (Optional)">
                        <b-form-checkbox-group id="atmospheres"
                                               v-model="form.atmospheres"
                                               :options="atmospheres">
                        </b-form-checkbox-group>
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

            <b-row class="mt-2 percent-analysis-oxide"  v-if="recipe.isPrimitive">
                <b-col lg="3" md="3" sm="6"
                       v-for="(oxideName, index) in OXIDE_NAMES" :key="index">
                    <label v-bind:for="oxideName" v-html="OXIDE_NAME_DISPLAY[oxideName]"></label>
                    <b-form-input id="oxideName"
                                  type="number"
                                  v-model.trim="form.analysis[oxideName]"></b-form-input>
                </b-col>
                <b-col lg="3" md="3" sm="6">
                    <label for="loi">LOI</label>
                    <b-form-input id="loi"
                                  type="number"
                                  v-model.trim="form.loi"></b-form-input>
                </b-col>
                <b-col lg="3" md="3" sm="6">
                    <label for="weight">Weight</label>
                    <b-form-input id="weight"
                                  type="number"
                                  v-model.trim="form.weight"></b-form-input>
                </b-col>
            </b-row>

            <b-form-group id="groupButtons">
                <b-button class="float-right"
                          size="sm"
                          variant="primary"
                          @click.prevent="update"><i class="fa fa-save"></i> Update</b-button>
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

  import MaterialTypes from 'ceramicscalc-js/src/material/MaterialTypes'
  import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'

  export default {
    props: {
      recipe: {
        type: Object,
        default: null
      }
    },
    components: {
    },
    data() {
      return {
        form: {
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
          analysis: new Analysis(),
          loi: null,
          weight: null
        },
        // isPrimitive: false,
        errors: [],
        apiError: null,
        serverError: null,
        constants: new GlazyConstants(),
        materialTypes: new MaterialTypes(),
        atmospheres: new GlazyConstants().ATMOSPHERE_SELECT,
        COUNTRY_SELECT: GlazyConstants.COUNTRY_SELECT,
        OXIDE_NAMES: Analysis.OXIDE_NAMES,
        OXIDE_NAME_DISPLAY: Analysis.OXIDE_NAME_DISPLAY
      }
    },
    created() {
      if (this.recipe) {
        this.form = {
          _method: 'PATCH',
          id: this.recipe.id,
          name: this.recipe.name,
          otherNames: this.recipe.otherNames,
          description: this.recipe.description,
          baseTypeId: this.recipe.baseTypeId,
          materialTypeId: this.recipe.materialTypeId,
          transparencyTypeId: this.recipe.transparencyTypeId,
          surfaceTypeId: this.recipe.surfaceTypeId,
          fromOrtonConeId: this.recipe.fromOrtonConeId,
          toOrtonConeId: this.recipe.toOrtonConeId,
          atmospheres: [],
          countryId: this.recipe.countryId,
          analysis: new Analysis(),
          loi: this.recipe.analysis.percentageAnalysis.loi
        }
        this.form.analysis.setOxides(this.recipe.analysis.percentageAnalysis)

        // this.form.analysis.initOxidesNull()
        if (this.recipe.atmospheres) {
          for (var i = 0; i < this.recipe.atmospheres.length; i++) {
            this.form.atmospheres.push(this.recipe.atmospheres[i].id);
          }
        }
      }
    },
    computed: {
      isLoaded: function () {
        if (this.recipe) {
          return true;
        }
        return false;
      },
      feedbackName() {
        return this.form.name.length > 0 ? 'Enter at least 3 characters' : 'Please enter a name';
      },
      stateName() {
        return this.form.name.length > 2 ? 'valid' : 'invalid';
      },
      baseTypeOptions: function () {
        return this.materialTypes.getParentTypes();
      },
      subTypeOptions: function () {
        if (this.isLoaded) {
          if (this.recipe.isPrimitive) {
            return this.materialTypes.PRIMITIVE_SELECT
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
      formulaWeight: function () {
        if (this.isLoaded && this.recipe.isPrimitive) {
          var percentageAnalysis = new PercentageAnalysis()
          percentageAnalysis.setOxides(this.form.analysis)
          percentageAnalysis.setLOI(this.form.loi)
          console.log("PERCENT")
          console.log(percentageAnalysis)
          var formulaAnalysis = FormulaAnalysis.createNoUnityFormula(percentageAnalysis)
          console.log("FORMULA")
          console.log(formulaAnalysis)

          return formulaAnalysis.getFormulaWeight()
        }
        return null
      }
      */
    },
    methods: {
      update: function () {
        if (this.isLoaded) {
          window.scrollTo(0, 0)
          this.$emit('isProcessing');

          if (!this.form.materialTypeId && this.form.baseTypeId) {
            this.form.materialTypeId = this.form.baseTypeId
          }

          Vue.axios.post(Vue.axios.defaults.baseURL + '/recipes/' + this.recipe.id, this.form)
            .then((response) => {
              console.log('got response:')
              console.log(response)
              if (response.data.error) {
                // error
                this.apiError = response.data.error
                console.log(this.apiError)
              } else {
                console.log('emit updatedRecipeMeta')
                this.$emit('updatedRecipeMeta')
              }
            })
            .catch(response => {
              this.serverError = response;
              console.log('UPDATE ERROR')
              console.log(response.data)
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
      }

    }
  }
</script>

<style>

    .edit-recipe-metadata label {
        margin-bottom: 0;
    }

    .percent-analysis-oxide .col {
        padding: 0 5px;
    }
</style>