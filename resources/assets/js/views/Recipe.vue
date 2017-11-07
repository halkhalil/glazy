<template>

  <div v-if="isLoaded && !isDeleted" v-cloak>

    <div id="recipeUpdatedAlert" class="alert alert-success fade show" style="display: none;">
      <i class="fa fa-check"></i> Recipe updated!
    </div>

    <div id="addCollectionAlert" class="alert alert-success fade show" style="display: none;">
      <i class="fa fa-check"></i> Recipe added to collection!
    </div>

    <div class="row recipe-info-row">
      <div class="col-md-8">

        <div class="card recipe-info-card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-9 col-sm-8">
                <material-type-breadcrumbs :recipe="recipe"></material-type-breadcrumbs>
                <h1 class="card-title">
                  <i v-if="recipe.isPrivate" class="fa fa-lock"></i>
                  {{ recipe.name }}
                </h1>
              </div>
              <div class="col-md-3 col-sm-4">
                <firing-card :recipe="recipe"></firing-card>
              </div>
            </div>
            <div class="row">
              <div class="col">
                <div class="author">
                  <img :src="getUserAvatar(recipe.createdByUser)" alt="..." class="avatar img-raised">
                  <span>{{ recipe.createdByUser.name }}</span>
                </div>

              </div>
              <div class="col text-right">
                <star-rating :rating="Number(recipe.ratingAverage)"
                             :read-only="true"
                             :star-size="24"
                             :show-rating="false"
                             :increment="0.01"></star-rating>
              </div>
            </div>
            <p class="card-description">
              {{ recipe.description }}
            </p>

            <div class="row" v-if="current_user">

              <div class="col-md-12">

                <button type="button" class="btn btn-primary" v-on:click="collectionAdd()">
                  <i class="fa fa-bookmark"></i>
                  Collect
                </button>

                <div class="btn-group btn-group-recipe-action">
                  <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="fa fa-cloud-download"></i> Export
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                      <a class="dropdown-item" href="https://glazy.org/recipes/export/3308?type=Card" target="_blank">Recipe Card</a>
                      <a class="dropdown-item" href="https://glazy.org/recipes/export/3308?type=Insight">Insight</a>
                      <a class="dropdown-item" href="https://glazy.org/recipes/export/3308?type=GlazeChem">GlazeChem</a>
                    </div>
                  </div>
                </div>

                <a class="btn btn-primary" :href="'/recipes/' + recipe.id + '/copy'" role="button">
                  <i class="fa fa-copy"></i>
                  Duplicate
                </a>

                <button v-if="recipe.isPrivate" type="button" class="btn btn-info" v-on:click="publishRecipe()">
                  <i class="fa fa-unlock"></i>
                  Publish
                </button>

                <button v-if="!recipe.isPrivate" type="button" class="btn btn-info" v-on:click="unpublishRecipe()">
                  <i class="fa fa-lock"></i>
                  Unpublish
                </button>

                <button type="button" class="btn btn-info" v-on:click="recipeEdit()">
                  <i class="fa fa-edit"></i>
                  Edit Info
                </button>

                <a v-if="!recipe.is_analysis" class="btn btn-info" :href="'/recipematerials/' + recipe.id + '/edit'" role="button">
                  <i class="fa fa-list"></i>
                  Edit Recipe
                </a>

                <button class="btn btn-danger" v-on:click="confirmDeleteRecipe()">
                  <i class="fa fa-times"></i>
                  Delete
                </button>

              </div>

            </div>

            <div v-if="!recipe.is_analysis" class="row">

              <div class="col-md-12">
                <material-recipe-calculator
                        :materialComponents="recipe.materialComponents"></material-recipe-calculator>
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
            <div class="row">
              <div class="col-md-6">
                <umf-traditional-notation
                        :material="material"
                        :showOxideList="false"
                        :squareSize="100">
                </umf-traditional-notation>
              </div>
              <div class="col-md-6">
                <div class="table-responsive">
                  <table class="umf-spark-table">
                    <MaterialAnalysisUmfSpark2Single
                            :material="material"
                            :showOxideList="false"
                            :squareSize="60"
                    >
                    </MaterialAnalysisUmfSpark2Single>
                  </table>
                </div>
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


    <div class="row">
      <div class="col-md-12">
        <umf-chart
                :current_user="null"
                :material="recipe"
        ></umf-chart>
      </div>
    </div>



    <div v-if="!recipe.is_analysis" class="row">
      <div class="col-md-12">
        <div class="card"> <!-- BEGIN Similar Base Components -->
          <div class="card-body">
            <h2 class="card-title">Similar Base Recipes</h2>
            <div class="row">
              <div class="col-sm-12">
                <similar-base-components :material="recipe"></similar-base-components>
              </div>
            </div>
          </div>
        </div> <!-- END Similar Base Components -->
      </div>
    </div>

    <div v-if="!recipe.is_analysis" class="row">
      <div class="col-md-12">
        <div class="card"> <!-- BEGIN Similar Unity Formula -->
          <div class="card-block">
            <h2>Similar Unity Formula</h2>
            <div class="row">
              <div class="col-sm-12">
                <similar-unity-formula :material="recipe"></similar-unity-formula>
              </div>
            </div>
          </div>
        </div> <!-- END Similar Unity Formula -->
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card"> <!-- BEGIN Reviews Card -->
          <div class="card-block">
            <h2>Reviews</h2>
            <reviews-panel
                    v-on:reviewsmodified="reviewsmodified"
                    :current_user="current_user"
                    :material="recipe"
            ></reviews-panel>
          </div>
        </div> <!-- END Reviews Card -->
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


    <!-- Edit Recipe Modal -->
    <div class="modal fade" id="editMetaModal" tabindex="-1" role="dialog" aria-labelledby="edit recipe info" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Edit Info</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <edit-recipe :recipe="recipe"
                         v-on:recipeupdated="recipeUpdated"
                         v-on:recipeeditcancel="recipeEditCancel"></edit-recipe>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="deleteMaterialModal" tabindex="-1" role="dialog" aria-labelledby="delete recipe" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Are you sure?</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Once deleted, you will not be able to retrieve this recipe!</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger"
                    @click.stop.prevent="deleteRecipe()">Confirm Delete</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
</template>

<script>

  import StarRating from 'vue-star-rating'

  import Material from 'ceramicscalc-js/src/material/Material'

  import MaterialTypeBreadcrumbs from '../components/glazy/materialtypes/MaterialTypeBreadcrumbs.vue'
  import FiringCard from '../components/glazy/recipe/FiringCard.vue'
  import MaterialRecipeCalculator from '../components/glazy/recipe/MaterialRecipeCalculator.vue'
  import MaterialImageGallery from '../components/glazy/materialimage/MaterialImageGallery.vue'

  import MaterialAnalysisUmfSpark2Single from '../components/glazy/analysis/MaterialAnalysisUmfSpark2Single.vue';
  import UmfTraditionalNotation from '../components/glazy/analysis/UmfTraditionalNotation.vue';
  import ComponentTable from '../components/glazy/analysis/ComponentTable.vue'

  import UmfChart from '../components/glazy/recipe/UmfChart.vue'
  import SimilarBaseComponents from '../components/glazy/recipe/SimilarBaseComponents.vue'

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
      UmfTraditionalNotation,
      ComponentTable,
      SimilarBaseComponents
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
        recipe : null,
        material : null,
        isDeleted : false,
        isRecipeUpdated : false
      }
    },

    mounted() {
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
      }
    },

    methods : {

      fetchRecipe : function(){
        console.log('Fetching recipe ID: ' + this.$route.params.id)
        Vue.axios.get(Vue.axios.defaults.baseURL + '/recipes/' + this.$route.params.id)
          .then((response) => {
          console.log('HERE');
          this.recipe = response.data.data;
          document.title = document.title + " - " + this.recipe.name;
//TODO                    this.setMaterial();
          console.log(this.recipe);
            var materialObj = new Material();
            this.material = Material.createFromJson(this.recipe);
          console.log(this.material);
            console.log('YYYY MATERIAL');

          })
        .catch(response => {
          // Error Handling
        });
      },

      fetchRecipeStatic () {
      },

      setMaterial: function() {
        console.log('YYYY');
        var materialObj = new Material();
        console.log('YYYY MATERIAL');
        this.material = materialObj.createFromJson(this.recipe);
        console.log('XXXX MATERIAL');
      },

      recipeUpdated: function() {
        console.log("calling recipeUpdated");
        this.fetchRecipe();
        $("#recipeUpdatedAlert").show();
//            $("#recipeUpdatedAlert").alert('open');
        $('#editMetaModal').modal('hide');
        this.isRecipeUpdated = true;
        window.setTimeout(function () {
          //$("#recipeUpdatedAlert").hide();
          $("#recipeUpdatedAlert").slideUp(500, function() {
            //$(this).remove();
            $("#recipeUpdatedAlert").hide();
          });
        }, 5000);
      },

      imageUpdated: function() {
        this.fetchRecipe();
      },

      recipeEdit: function() {
        $('#editMetaModal').modal('show');
      },

      recipeEditCancel: function() {
        $('#editMetaModal').modal('hide');
      },

      collectionAdd: function(material) {
        $('#addToCollectionModal').modal('show');
      },

      collectionaddrecipe: function() {
        $("#addCollectionAlert").show();
        $('#addToCollectionModal').modal('hide');
        window.setTimeout(function () {
          //$("#recipeUpdatedAlert").hide();
          $("#addCollectionAlert").slideUp(500, function() {
            //$(this).remove();
            $("#addCollectionAlert").hide();
          });
        }, 5000);
        this.fetchRecipe();
      },

      reviewsmodified: function() {
        this.fetchRecipe();
      },

      /*
       confirmDeleteRecipe: function() {
       swal({
       title: "Are you sure?",
       text: "You will not be able to recover this recipe!",
       type: "warning",
       showCancelButton: true,
       confirmButtonColor: "#DD6B55",
       confirmButtonText: "Yes, delete it!",
       closeOnConfirm: false,
       html: false
       }, function(){
       this.deleteRecipe();
       }.bind(this));
       },
       */
      confirmDeleteRecipe: function() {
        $('#deleteMaterialModal').modal('show');
      },
      /*
       deleteRecipe: function() {
       this.$http.delete('/api/v1/recipes/' + this.recipe_id)
       .then((response) => {
       this.isDeleted = true;
       swal({
       title: "Deleted!",
       text: "Your recipe has been deleted.",
       type: "success"
       }, function(){
       window.location = '/recipes';
       }.bind(this));
       })
       .catch(response => {
       // Error Handling
       });
       }
       */
      deleteRecipe: function() {
        $('#deleteMaterialModal').modal('hide');
        axios.delete('http://homestead.app/api/recipes/' + this.recipe_id)
          .then((response) => {
          $('#materialDeletedModal').modal('show');
        window.setTimeout(function () {
          this.isDeleted = true;
          window.location = '/search';
        }, 2000);
      })
        .catch(response => {
          // Error Handling
        });
      },

      publishRecipe: function() {
        var publishUrl = '/api/v1/recipes/' + this.recipe_id + '/publish';
        axios.get(publishUrl)
          .then((response) => {
          this.recipe = response.data.data.material;
        var materialObj = new Material();
        this.material = materialObj.createFromJson(this.recipe);
      })
        .catch(response => {
          // Error Handling
        });
      },

      unpublishRecipe: function() {
        var unpublishUrl = '/api/v1/recipes/' + this.recipe_id + '/unpublish';
        axios.get(unpublishUrl)
          .then((response) => {
          this.recipe = response.data.data.material;
        var materialObj = new Material();
        this.material = materialObj.createFromJson(this.recipe);
      })
        .catch(response => {
          // Error Handling
        });
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
  }

  .recipe-info-card .card-description {
    margin-top: 20px;
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

</style>