<template>
  <div>
    <b-alert v-if="apiError" show variant="danger">
      API Error: {{ apiError.message }}
    </b-alert>
    <b-alert v-if="serverError" show variant="danger">
      Server Error: {{ serverError }}
    </b-alert>
    <div class="load-container load7 fullscreen" v-if="isProcessing">
      <div class="loader">Loading...</div>
    </div>

    <div v-if="isEditComponents && isLoaded && !isDeleted">
      <edit-recipe-components :originalMaterial="material"
                              v-on:isProcessing="isProcessingRecipe"
                              v-on:updatedRecipeComponents="updatedRecipeComponents"
                              v-on:editComponentsCancel="editComponentsCancel">
      </edit-recipe-components>
    </div>
    <div v-if="!isEditComponents && isLoaded && !isDeleted" v-cloak>

      <div id="addCollectionAlert" class="alert alert-success fade show" style="display: none;">
        <i class="fa fa-check"></i> Recipe added to collection!
      </div>

      <div class="row recipe-info-row">
        <div class="col-md-8">
          <div class="card recipe-info-card">
            <div class="card-body">

              <b-alert :show="showRecipeUpdatedSeconds" variant="info">
                Recipe successfully updated.
              </b-alert>

              <div v-if="isEditMeta">
                <edit-recipe-metadata :recipe="recipe"
                                      v-on:updatedRecipeMeta="updatedRecipeMeta"
                                      v-on:editMetaCancel="editMetaCancel"
                                      v-on:isProcessing="isProcessingRecipe"></edit-recipe-metadata>
              </div>
              <div v-show="!(isEditMeta)">
                <div class="row">
                  <div class="col">
                    <material-type-breadcrumbs v-if="!recipe.isPrimitive"
                                               :recipe="recipe"></material-type-breadcrumbs>
                    <h2 class="card-title">
                      <i v-if="recipe.isPrivate"
                         v-b-tooltip.hover title="Archived"
                         class="fa fa-lock"></i>
                      <i v-if="recipe.isArchived"
                         v-b-tooltip.hover title="Archived"
                         class="fa fa-archive"></i>
                      {{ recipe.name }}
                    </h2>
                  </div>
                  <div class="col-md-3 col-sm-4" v-if="!recipe.isPrimitive">
                    <firing-card :recipe="recipe"></firing-card>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-8">
                    <div class="author">
                      <img :src="getUserAvatar(recipe.createdByUser)" alt="..." class="avatar img-raised">
                      <span>
                        {{ recipe.createdByUser.name }},
                        <timeago :since="recipe.updatedAt"></timeago>
                      </span>
                    </div>

                  </div>
                  <div class="col-sm-4 float-right">
                    <star-rating v-if="recipe.ratingTotal"
                                 class="recipe-vue-star-rating"
                                 :rating="Number(recipe.ratingAverage)"
                                 :read-only="true"
                                 :star-size="24"
                                 :show-rating="false"
                                 :increment="0.01"></star-rating>
                  </div>
                </div>
                <p class="card-description">
                  {{ recipe.description }}
                </p>

                <div class="row" v-if="$auth.check()">

                  <div class="col-md-12">

                    <b-button-group class="recipe-action-group">
                      <b-dropdown left>
                        <span slot=text><i class="fa fa-bookmark" aria-hidden="true"></i> Collect</span>
                        <b-dropdown-item>Item 1</b-dropdown-item>
                        <b-dropdown-item>Item 2</b-dropdown-item>
                        <b-dropdown-divider></b-dropdown-divider>
                        <b-dropdown-item>Item 3</b-dropdown-item>
                      </b-dropdown>
                      <b-dropdown left>
                        <span slot=text><i class="fa fa-cloud-download" aria-hidden="true"></i> Export</span>
                        <b-dropdown-item>Item 1</b-dropdown-item>
                        <b-dropdown-item>Item 2</b-dropdown-item>
                        <b-dropdown-divider></b-dropdown-divider>
                        <b-dropdown-item>Item 3</b-dropdown-item>
                      </b-dropdown>
                      <b-button v-on:click="copyRecipe()"><i class="fa fa-copy"></i> Copy</b-button>
                    </b-button-group>
                    <b-button-group class="recipe-action-group" v-if="canEdit">
                      <b-button class="btn-info" v-if="recipe.isPrivate" v-on:click="publishRecipe()"><i class="fa fa-unlock"></i> Publish</b-button>
                      <b-button class="btn-info" v-if="!(recipe.isPrivate)" v-on:click="unpublishRecipe()"><i class="fa fa-lock"></i> Unpublish</b-button>
                      <b-button class="btn-info" v-on:click="editMeta()"><i class="fa fa-edit"></i> Edit Info</b-button>
                      <b-button class="btn-info" v-on:click="editComponents()"><i class="fa fa-list"></i> Edit Recipe</b-button>
                      <b-button class="btn-danger" v-if="!recipe.isArchived" v-b-modal.deleteConfirmModal><i class="fa fa-trash"></i></b-button>
                    </b-button-group>

                    <b-modal v-if="canEdit"
                             id="deleteConfirmModal"
                             title="Delete Recipe?"
                             v-on:ok="deleteRecipe"
                             ok-title="Delete Forever"
                    >
                      <p>Once deleted, you will not be able to retrieve this recipe!</p>
                    </b-modal>

                  </div>

                </div>

                <div v-if="!recipe.isPrimitive" class="row">

                  <div class="col-md-12 mt-4">
                    <material-recipe-calculator
                            :materialComponents="recipe.materialComponents"></material-recipe-calculator>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">

          <material-image-gallery
                  :current_user="current_user"
                  :material="recipe"
                  v-on:imageupdated="imageUpdated"></material-image-gallery>

        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="card"> <!-- BEGIN Analysis Card -->
            <div class="card-body">
              <h2 class="card-title">Analysis</h2>
              <!--
              <view-recipe-materials-analysis
                      :recipe="recipe"></view-recipe-materials-analysis>
              -->
              <div class="row" v-if="!recipe.isPrimitive && recipe.baseTypeId == glazeTypeId">
                <div class="col-md-6">
                  <umf-traditional-notation
                          :material="material"
                          :showOxideList="false"
                          :squareSize="100">
                  </umf-traditional-notation>
                </div>
                <div class="col-md-6">
                      <JsonUmfSparkSvg
                              :material="recipe"
                              :squareSize="56"
                              :fontSize="12"
                              :showOxideTitle="true"
                              :showOxideList="true"
                      >
                      </JsonUmfSparkSvg>
                </div>
              </div>
              <div class="row mt-4">
                <div class="col-md-12">
                  <b-tabs class="analysis-tabs" active>
                    <b-tab title="Mol % Analysis" >
                      <component-table
                              :material="material"
                              :isFormulaAnalysis="true"></component-table>
                    </b-tab>
                    <b-tab title="% Analysis">
                      <component-table
                              :material="material"
                              :isFormulaAnalysis="false"></component-table>
                    </b-tab>
                  </b-tabs>
                </div>
              </div>

              <!--
              <material-analysis-percent-table-compare
                      :originalAnalysis="material.getMolePercentageFormula().analysis">
              </material-analysis-percent-table-compare>
              -->
            </div>
          </div> <!-- END Analysis Card -->
        </div>
      </div>


      <div class="row" v-if="!recipe.isPrimitive">
        <div class="col-md-12">
          <umf-chart
                  :current_user="null"
                  :material="recipe"
          ></umf-chart>
        </div>
      </div>



      <div v-if="!recipe.isPrimitive" class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <h2 class="card-title">Similar Base Recipes</h2>
              <div class="row">
                <div class="col-sm-12">
                  <similar-base-components :material="recipe"></similar-base-components>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="!recipe.isPrimitive" class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <h2 class="card-title">Similar Unity Formula</h2>
              <div class="row">
                <div class="col-sm-12">
                  <similar-unity-formula :material="recipe"></similar-unity-formula>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <h2 class="card-title">Reviews</h2>
              <div class="row">
                <div class="col-sm-12">
                <reviews-panel
                        v-on:reviewsmodified="reviewsmodified"
                        :current_user="current_user"
                        :material="recipe"
                ></reviews-panel>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade collection-add-recipe-modal" id="addToCollectionModal" tabindex="-1" role="dialog" aria-labelledby="add to collection" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Add to Collection</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <collection-add-recipe-form
                      :recipe="recipe"
                      :current_user="current_user"
                      v-on:collectionaddrecipe="collectionaddrecipe"
              ></collection-add-recipe-form>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="materialDeletedModal" tabindex="-1" role="dialog" aria-labelledby="delete recipe" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Material deleted!</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <p>Redirecting to your recipes..</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>

<script>

  import StarRating from 'vue-star-rating'
  import Material from 'ceramicscalc-js/src/material/Material'

  import MaterialTypes from 'ceramicscalc-js/src/material/MaterialTypes'

  import MaterialTypeBreadcrumbs from '../components/glazy/materialtypes/MaterialTypeBreadcrumbs.vue'
  import FiringCard from '../components/glazy/recipe/FiringCard.vue'
  import MaterialRecipeCalculator from '../components/glazy/recipe/MaterialRecipeCalculator.vue'
  import MaterialImageGallery from '../components/glazy/materialimage/MaterialImageGallery.vue'

  import JsonUmfSparkSvg from '../components/glazy/analysis/JsonUmfSparkSvg.vue'
  import MaterialAnalysisUmfSpark2Single from '../components/glazy/analysis/MaterialAnalysisUmfSpark2Single.vue';
  import UmfTraditionalNotation from '../components/glazy/analysis/UmfTraditionalNotation.vue';
  import ComponentTable from '../components/glazy/analysis/ComponentTable.vue'

  import UmfChart from '../components/glazy/recipe/UmfChart.vue'
  import SimilarBaseComponents from '../components/glazy/recipe/SimilarBaseComponents.vue'
  import SimilarUnityFormula from '../components/glazy/recipe/SimilarUnityFormula.vue'

  import EditRecipeMetadata from '../components/glazy/recipe/EditRecipeMetadata.vue'
  import EditRecipeComponents from '../components/glazy/recipe/EditRecipeComponents.vue'

  import ReviewsPanel from '../components/glazy/materialreviews/ReviewsPanel.vue'

  import VueTimeago from 'vue-timeago'

  import Vue from 'vue'

  export default {
    name: 'Recipe',

    components: {
      MaterialTypeBreadcrumbs,
      FiringCard,
      StarRating,
      MaterialRecipeCalculator,
      MaterialImageGallery,
      UmfChart,
      MaterialAnalysisUmfSpark2Single,
      JsonUmfSparkSvg,
      UmfTraditionalNotation,
      ComponentTable,
      SimilarBaseComponents,
      SimilarUnityFormula,
      EditRecipeMetadata,
      EditRecipeComponents,
      ReviewsPanel,
      VueTimeago
    },
    props: {
      recipe_id: {
        type: Number,
        default: null
      },

      current_user: {
        type: Object,
        default: null
      }
    },

    data() {
      return {
        recipe: null,
        material: null,
        isDeleted: false,
        isRecipeUpdated: false,
        isEditMeta: false,
        isEditComponents: false,
        showRecipeUpdatedSeconds: 0,
        isProcessing: false,
        apiError: null,
        serverError: null,
        glazeTypeId: new MaterialTypes().GLAZE_TYPE_ID,
        isEditRequest: false
      }
    },

    mounted() {
      if ('isEdit' in this.$route.query && this.$route.query.isEdit) {
        this.isEditRequest = true
      }
      this.fetchRecipe()
    },

    computed : {
      isLoaded: function() {
        if (this.recipe &&
            this.material &&
            this.recipe.hasOwnProperty('name')
//                && this.material
        ) {
          return true;
        }
        return false;
      },
      canEdit: function () {
        // Only the creator of a recipe can edit it
        if (this.$auth.check() &&
          this.$auth.user().id === this.recipe.createdByUserId) {
          return true
        }
        return false
      }
    },
    beforeRouteUpdate (to, from, next) {
      // Ensure that the back/forward buttons work within this component/route
      this.sendRecipeGetRequest('/recipes/' + to.params.id)
      next()
    },
    methods : {

      fetchRecipe: function (){
        console.log('--- FETCH: /recipes/' + this.$route.params.id)
        this.sendRecipeGetRequest('/recipes/' + this.$route.params.id)
      },

      publishRecipe: function () {
        if (this.recipe) {
          this.sendRecipeGetRequest('/recipes/' + this.recipe.id + '/publish')
        }
      },

      unpublishRecipe: function () {
        if (this.recipe) {
          this.sendRecipeGetRequest('/recipes/' + this.recipe.id + '/unpublish')
        }
      },

      copyRecipe: function () {
        if (!this.recipe) {
          return
        }
        this.isProcessing = true
        Vue.axios.get(Vue.axios.defaults.baseURL + '/recipes/' + this.recipe.id + '/copy')
          .then((response) => {
          if (response.data.error) {
            this.apiError = response.data.error
            console.log(this.apiError)
            this.isProcessing = false
          } else {
            this.isProcessing = false
            var recipeCopy = response.data.data;
            this.$router.push({ name: this.$route.name, params: { id: recipeCopy.id }})
          }
        })
        .catch(response => {
          this.serverError = response;
          this.isProcessing = false
        })
      },

      setMaterial: function () {
        console.log('YYYY');
        var materialObj = new Material();
        console.log('YYYY MATERIAL');
        this.material = materialObj.createFromJson(this.recipe);
        console.log('XXXX MATERIAL');
      },

      updatedRecipeMeta: function () {
        console.log("calling updatedRecipeMeta")
        this.fetchRecipe()
        this.showRecipeUpdatedSeconds = 5
        this.isRecipeUpdated = true
        this.isEditMeta = false
      },

      updatedRecipeComponents: function () {
        console.log("calling updatedRecipeComponents")
        this.fetchRecipe()
        this.showRecipeUpdatedSeconds = 5
        this.isRecipeUpdated = true
        this.isEditComponents = false
      },

      imageUpdated: function () {
        this.fetchRecipe()
      },

      editMeta: function () {
        this.isEditMeta = true
      },

      editMetaCancel: function () {
        this.isEditMeta = false
      },

      editComponents: function () {
        this.isEditComponents = true
      },

      editComponentsCancel: function () {
        this.isEditComponents = false
      },

      isProcessingRecipe: function () {
        this.isProcessing = true
      },

      collectionAdd: function (material) {
        $('#addToCollectionModal').modal('show');
      },

      collectionaddrecipe: function () {
        $("#addCollectionAlert").show();
        $('#addToCollectionModal').modal('hide');
        window.setTimeout(function () {
          $("#addCollectionAlert").slideUp(500, function() {
            $("#addCollectionAlert").hide();
          });
        }, 5000);
        this.fetchRecipe();
      },

      reviewsmodified: function () {
        this.fetchRecipe();
      },

      deleteRecipe: function () {
        Vue.axios.delete(Vue.axios.defaults.baseURL + '/recipes/' + this.recipe.id)
          .then((response) => {
          if (response.data.error) {
          this.apiError = response.data.error
          console.log(this.apiError)
          this.isProcessing = false
        } else {
          this.isProcessing = false
          this.$router.push({ name: 'search' })
        }
        })
        .catch(response => {
          this.serverError = response;
          this.isProcessing = false
        })
      },


      sendRecipeGetRequest: function (url) {
        this.apiError = null
        this.isProcessing = true
        Vue.axios.get(Vue.axios.defaults.baseURL + url)
          .then((response) => {
          if (response.data.error) {
            this.apiError = response.data.error
            this.isProcessing = false
            console.log(this.apiError)
          } else {
            this.recipe = response.data.data
            document.title = this.recipe.name
            var materialObj = new Material()
            this.material = Material.createFromJson(this.recipe)

            if (this.isEditRequest && this.canEdit) {
              // User just created this recipe in calculator
              this.isEditRequest = false
              this.editMeta()
            }
        }
          this.isProcessing = false
        })
        .catch(response => {
          this.itemlist = null
          if (response.response && response.response.status) {
            if (response.response.status === 401) {
              this.$auth.refresh() // attempt refresh
            } else {
              this.serverError = response.response.message;
            }
          }
          this.isProcessing = false
        })
      },

      getUserProfileUrl: function(recipe) {
        if (recipe.createdByUser.hasOwnProperty('username')
          && recipe.createdByUser.username) {
          return '/u/' + recipe.createdByUser.username;
        }
        return '/u/' + recipe.createdByUser.id;
      },

      getUserAvatar: function(user) {
        if (user.hasOwnProperty('avatar') && user.avatar) {
          return user.avatar;
        }
        else if (user.hasOwnProperty('gravatar') && user.gravatar) {
          return user.gravatar;
        }
        else {
          return 'http://www.gravatar.com/avatar/?d=mm';
        }
      },

      getDisplayName: function(user) {
        if (user.hasOwnProperty('username') && user.username) {
          return user.username;
        }
        return user.name;
      }



    }
  }
</script>

<style>

  .recipe-info-row {
    margin-top: 1rem;
    padding-top: 15px;
  }

  .recipe-info-card .card-description {
    margin-top: 20px;
  }

  .recipe-action-group .btn {
    padding: 11px 16px;
  }

  .analysis-tabs .nav-tabs {
    padding: 10px 10px;
  }

  .analysis-tabs .nav-tabs .nav-item .nav-link {
    padding: 5px 10px;
  }

  .analysis-tabs .nav-tabs .nav-item .nav-link.active {
    background-color: #999;
  }

  .recipe-vue-star-rating {
    float: right;
  }
</style>