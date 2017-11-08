<template>
<div class="row" id="edit-recipe-form">
    <div class="col-md-12">
        <b-alert v-if="error" show variant="danger">
            {{ error.message }}
        </b-alert>
        <div class="load-container load7" v-if="isProcessing">
            <div class="loader">Loading...</div>
        </div>
        <form class="form-horizontal" role="form" method="POST" v-if="isLoaded && !isProcessing">
            <div>
                <h3 class="card-title">
                    Edit {{ recipe.name }}
                </h3>
            </div>

            <div class="col-md-12 alert alert-danger" v-if="hasErrors">
                <i class="fa fa-warning mr-2"></i> Errors were found in the form below.
            </div>

            <input type="hidden" name="_method" value="PATCH">

            <div class="form-group" :class="{'has-danger': errors.name}">

                <label for="name" class="control-label">Name</label>
                <b-form-input id="name"
                              v-model="recipe.name"
                              type="text"
                              placeholder="Recipe Title"></b-form-input>

                <span class="help-block" v-if="errors.name">
                    <div v-for="error in errors.name">
                        {{ error }}
                    </div>
                </span>
            </div>

            <div class="form-group" :class="{'has-danger': errors.description}">
                <label for="description" class="control-label">Description</label>

                <b-form-textarea id="description"
                                 v-model="recipe.description"
                                 placeholder="Recipe description (optional)"
                                 :rows="6"
                                 :max-rows="10">
                </b-form-textarea>
                <span class="help-block" v-if="errors.description">
                    <div v-for="error in errors.description">
                        {{ error }}
                    </div>
                </span>
            </div>

            <div class="form-group" :class="{'has-danger': errors.material_type}">
                <label for="material_type" class="control-label">Recipe Type</label>

                <div class="row" id="recipe-type-select">
                    <div class="col-md-4">
                        <b-form-select
                                v-model="recipe.baseTypeId"
                                :options="baseTypeOptions"
                                @input="updateBaseType">
                        </b-form-select>
                    </div>
                    <div class="col-md-8">
                        <b-form-select
                                v-if="subTypeOptions && subTypeOptions.length > 0"
                                v-model="recipe.materialTypeId"
                                :options="subTypeOptions">
                        </b-form-select>
                    </div>
                </div>
            </div>

            <div class="form-group" :class="{'has-danger': errors.from_orton_cone_id}">
                <label for="from_orton_cone_id" class="control-label">Temperature Range</label>

                <div class="row" id="orton-cone-select">
                    <div class="col-md-4">
                        <b-form-select
                                v-model="recipe.fromOrtonConeId"
                                :options="constants.ORTON_CONES_SELECT_TEXT"
                                @input="updateFromCone">
                        </b-form-select>
                    </div>
                    <div class="col-md-4">
                        <b-form-select
                                v-model="recipe.toOrtonConeId"
                                :options="constants.ORTON_CONES_SELECT_TEXT"
                                @input="updateToCone">
                        </b-form-select>
                    </div>
                </div>
            </div>

            <div class="form-group" :class="{'has-danger': errors.atmospheres}">

                <label for="atmospheres" class="control-label">Firing Atmospheres</label>

                <label class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input"
                           id="atmosphere_1" value="1" v-model.number="atmospheres">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">
                        Oxidation
                    </span>
                </label>

                <label class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input"
                           id="atmosphere_2" value="2" v-model.number="atmospheres">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">
                        Neutral
                    </span>
                </label>

                <label class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input"
                           id="atmosphere_3" value="3" v-model.number="atmospheres">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">
                        Reduction
                    </span>
                </label>

                <label class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input"
                           id="atmosphere_4" value="4" v-model.number="atmospheres">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">
                        Salt & Soda
                    </span>
                </label>

                <label class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input"
                           id="atmosphere_5" value="5" v-model.number="atmospheres">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">
                        Wood
                    </span>
                </label>

                <label class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input"
                           id="atmosphere_6" value="6" v-model.number="atmospheres">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">
                        Raku
                    </span>
                </label>

                <label class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input"
                           id="atmosphere_7" value="7" v-model.number="atmospheres">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description">
                        Luster
                    </span>
                </label>

            </div>

            <div class="form-group">
                <b-button class="float-left"
                          size="sm"
                          variant="secondary"
                          @click.prevent="cancelEdit()"><i class="fa fa-times"></i> Cancel</b-button>
                <b-button class="float-right"
                          size="sm"
                          variant="primary"
                          @click.prevent="update"><i class="fa fa-save"></i> Update</b-button>
            </div>
        </form>

    </div>
</div>

</template>


<script>

  import MaterialTypes from 'ceramicscalc-js/src/material/MaterialTypes'
  import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'

  // import EditRecipeTypeSelect from './EditRecipeTypeSelect.vue'
  // import EditRecipeOrtonConesSelect from './EditRecipeOrtonConesSelect.vue'
  import Multiselect from 'vue-multiselect'

  export default {

    props: ['recipe'],
    components: {
      // EditRecipeTypeSelect,
      // EditRecipeOrtonConesSelect,
      Multiselect
    },

    data() {
      return {
        errors: [],
        error: null,
        constants: new GlazyConstants(),
        materialTypes: new MaterialTypes(),
        isProcessing: false,
        hasErrors: false
//                submitted: false
      };
    },

    computed: {

      isLoaded: function () {
        if (this.recipe) {
          return true;
        }
        return false;
      },

      form: function () {

        var form = {};

        if (this.isLoaded) {
          form = {
            _method: 'PATCH',
            id: this.recipe.id,
            name: this.recipe.name,
            description: this.recipe.description,
            typeId: {
              value: this.recipe.materialTypeId,
              text: this.materialTypes.LOOKUP[this.recipe.materialTypeId]
            },
            baseTypeId: {
              value: this.recipe.baseTypeId,
              text: this.materialTypes.LOOKUP[this.recipe.baseTypeId]
            },
            orton_cone_range: {
              from: this.recipe.from_orton_cone_id,
              to: this.recipe.to_orton_cone_id
            },
            atmosphere_ids: []
//                        hex_color: this.recipe.hex_color,
          };

          if (this.recipe.atmospheres) {
            for (var i = 0; i < this.recipe.atmospheres.length; i++) {
              form.atmosphere_ids.push(this.recipe.atmospheres[i].id);
            }
          }
        }
        return form;
      },

      atmospheres: function() {
        var atmospheres = []
        if (this.recipe.atmospheres) {
          this.recipe.atmospheres.forEach((atmosphere) => {
            atmospheres.push(atmosphere.id)
          })
        }
        return atmospheres
      },

      baseTypeOptions: function () {
        return this.materialTypes.getParentTypes();
      },

      subTypeOptions: function () {
          switch (this.recipe.baseTypeId) {
            case this.materialTypes.GLAZE_TYPE_ID:
              return this.materialTypes.getGlazeTypes()
            case this.materialTypes.CLAYS_TYPE_ID:
              return this.materialTypes.getClayTypes()
            case this.materialTypes.SLIPS_TYPE_ID:
              return this.materialTypes.getSlipTypes()
          }
        return null
      }
    },

    methods: {

      update: function () {
        if (this.isLoaded) {
          this.isProcessing = true

          var form = {
            _method: 'PATCH',
            id: this.recipe.id,
            name: this.recipe.name,
            description: this.recipe.description,
            material_type_id: this.recipe.materialTypeId,
            from_orton_cone_id: this.recipe.from_orton_cone_id,
            to_orton_cone_id: this.recipe.to_orton_cone_id
          }

          if (!form.material_type_id && this.recipe.baseTypeId) {
            form.material_type_id = this.recipe.baseTypeId
          }

          console.log('UPDATE: ')
          console.log('UPDATE: RECIPE: ')
          console.log(this.recipe)
          console.log('UPDATE: ')
          console.log('UPDATE FORM: ')
          console.log(form)
          console.log('UPDATE: ATMOSPHERES')
          console.log(this.atmospheres)

          Vue.axios.post(Vue.axios.defaults.baseURL + '/recipes/' + this.recipe.id, form)
            .then((response) => {
              console.log('got response:')
              console.log(response)
              if (response.data.error) {
                // error
                this.error = response.data.error
                console.log(this.error)
              } else {
                console.log('emit recipeUpdated')
                this.$emit('recipeupdated')
              }
              this.isProcessing = false
            })
            .catch(response => {
              this.errors = response.data;
              console.log('UPDATE ERROR')
              console.log(response.data)
              this.hasErrors = true
              this.isProcessing = false
            })
        }
      },

      cancelEdit: function () {
        this.$emit('editMetaCancel');
      },

      updateBaseType () {
        this.recipe.typeId = 0
      },

      updateFromCone () {
        if (this.recipe.toOrtonConeId && this.recipe.toOrtonConeId < this.recipe.fromOrtonConeId) {
          this.recipe.toOrtonConeId = this.recipe.fromOrtonConeId
        }
      },

      updateToCone () {
        if (this.recipe.fromOrtonConeId && this.recipe.fromOrtonConeId > this.recipe.toOrtonConeId) {
          this.recipe.fromOrtonConeId = this.recipe.toOrtonConeId
        }
      }

    }
  }
</script>
