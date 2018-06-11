<template>
  <div class="row recipe-component">
    <nav v-if="isLoaded && searchItems && searchItems.length > 0"
         class="col-md-3 sidebar d-none d-md-block">

      <h5 class="search-title">Search Results</h5>

      <router-link v-if="searchRoute && (searchRoute.name === 'search' || searchRoute.name === 'materials')"
                   :to="{ name: searchRoute.name, query: searchRoute.query }">
        <i class="fa fa-chevron-left"></i> Back to search
      </router-link>

      <router-link v-if="searchRoute && (searchRoute.name === 'user' || searchRoute.name === 'user-materials')"
                   :to="{ name: searchRoute.name, params: searchRoute.params, query: searchRoute.query }">
        <i class="fa fa-chevron-left"></i> Back to search
      </router-link>

      <section class="row search-results">
        <div class="col-md-12"
             v-for="searchMaterial in searchItems">
          <material-card-detail
                  :material="searchMaterial"
                  :currentMaterialId="recipe.id"
                  :isEmbedded="true"
          ></material-card-detail>
        </div>
      </section>
    </nav>
    <main v-bind:class="mainClass" role="main" class="ml-sm-auto recipe-result">
      <b-alert v-if="apiError" show variant="danger">
        API Error: {{ apiError.message }}
      </b-alert>
      <b-alert v-if="serverError" show variant="danger">
        Server Error: {{ serverError }}
      </b-alert>
      <div class="load-container load7 fullscreen" v-if="isProcessing">
        <div class="loader">Loading...</div>
      </div>
      <b-alert :show="actionMessageSeconds"
               @dismiss-count-down="actionMessageCountdown"
               variant="info">
        {{ actionMessage }}
      </b-alert>

      <div v-if="isEditComponents && isLoaded && !isDeleted">
        <edit-recipe-components :originalMaterial="material"
                                v-on:isProcessing="isProcessingRecipe"
                                v-on:updatedRecipeComponents="updatedRecipeComponents"
                                v-on:editComponentsCancel="editComponentsCancel">
        </edit-recipe-components>
      </div>
      <div v-if="!isEditComponents && isLoaded && !isDeleted">

        <div id="addCollectionAlert" class="alert alert-success fade show" style="display: none;">
          <i class="fa fa-check"></i> Recipe added to collection!
        </div>

        <div class="row recipe-info-row">
          <div class="col-md-8">
            <div class="card recipe-info-card">
              <div class="card-body">

                <b-alert :show="infoMessageSeconds" variant="info">
                  Recipe successfully updated.
                </b-alert>

                <div v-if="isEditMeta">
                  <edit-material-metadata :material="recipe"
                                        v-on:updatedRecipeMeta="updatedRecipeMeta"
                                        v-on:editMetaCancel="editMetaCancel"
                                        v-on:isProcessing="isProcessingRecipe"></edit-material-metadata>
                </div>
                <div v-show="!(isEditMeta)">
                  <div class="row">
                    <div class="col">
                      <material-type-breadcrumbs :recipe="recipe"></material-type-breadcrumbs>
                      <h2 class="card-title">
                        <i v-if="recipe.isPrivate"
                           v-b-tooltip.hover title="Private"
                           class="fa fa-eye-slash"></i>
                        <i v-if="recipe.isArchived"
                           v-b-tooltip.hover title="Locked"
                           class="fa fa-lock"></i>
                        {{ recipe.name }}
                      </h2>
                      <span v-if="recipe.otherNames">
                        Other Names: {{ recipe.otherNames }}
                      </span>
                      <span>
                        <star-rating v-if="recipe.ratingTotal"
                                     class="recipe-vue-star-rating"
                                     :rating="Number(recipe.ratingAverage)"
                                     :read-only="true"
                                     :star-size="24"
                                     :show-rating="false"
                                     :increment="0.01"></star-rating>
                      </span>
                    </div>
                    <div class="col-md-4 col-sm-4" v-if="!recipe.isPrimitive">
                      <firing-card :recipe="recipe"></firing-card>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12 col-sm-6">
                      <div class="author">
                        <router-link :to="{ name: 'user', params: { id: glazyHelper.getUserProfileUrlId(recipe.createdByUser) }}">
                          <img v-bind:src="glazyHelper.getUserAvatar(recipe.createdByUser)"
                               class="avatar"/> {{ recipe.createdByUser.name }}
                        </router-link>,
                        <timeago :since="recipe.updatedAt"></timeago>
                      </div>
                    </div>
                    <div v-if="!recipe.isPrivate"
                         class="col-12 col-sm-6 text-right">
                      Share:
                      <social-sharing :url="this.meta.url"
                                      :title="this.meta.title"
                                      :description="this.meta.description"
                                      :media="this.meta.image"
                                      hashtags="glaze,ceramics,recipe"
                                      inline-template>
                        <div class="btn-group" role="group" aria-label="Social sharing buttons">
                          <button class="btn btn-icon btn-neutral btn-default">
                            <network network="email">
                              <i class="fa fa-envelope"></i>
                            </network>
                          </button>
                          <button class="btn btn-icon btn-neutral btn-facebook">
                            <network network="facebook">
                              <i class="fa fa-facebook"></i>
                            </network>
                          </button>
                          <button class="btn btn-icon btn-neutral btn-google">
                            <network network="googleplus">
                              <i class="fa fa-google-plus"></i>
                            </network>
                          </button>
                          <button class="btn btn-icon btn-neutral btn-pinterest">
                            <network network="pinterest">
                              <i class="fa fa-pinterest"></i>
                            </network>
                          </button>
                        </div>
                      </social-sharing>
                    </div>
                  </div>

                  <div class="row material-metadata">
                    <div class="col-6 col-sm-4" v-if="recipe.surfaceTypeName">
                      Surface
                      <br/>
                      <span class="badge badge-info">
                    {{ recipe.surfaceTypeName }}
                    </span>
                    </div>
                    <div class="col-6 col-sm-4"  v-if="recipe.transparencyTypeName">
                      Transparency
                      <br/>
                      <span class="badge badge-info">
                     {{ recipe.transparencyTypeName }}
                    </span>
                    </div>
                    <div class="col-6 col-sm-4" v-if="recipe.countryName">
                      Country
                      <br/>
                      <span class="badge badge-info">
                          {{ recipe.countryName }}
                        </span>
                    </div>
                  </div>

                  <p class="card-description" v-if="recipe.description">
                    <span style="white-space: pre-wrap;" v-html="glazyHelper.getLinkifiedText(recipe.description.trim())">
                    </span>
                  </p>

                  <div class="row" v-if="$auth.check()">

                    <div class="col-md-12">

                      <b-button-group class="recipe-action-group">

                        <b-button @click="collectMaterialSelect(material.id)"
                               v-b-tooltip.hover title="Bookmark">
                          <i class="fa fa-bookmark" aria-hidden="true"></i> Bookmark
                        </b-button>
                        <b-dropdown left>
                          <span slot=text><i class="fa fa-cloud-download" aria-hidden="true"></i> Export</span>
                          <b-dropdown-item v-on:click="exportRecipe('Insight')">Insight</b-dropdown-item>
                          <b-dropdown-item v-on:click="exportRecipe('GlazeChem')">GlazeChem</b-dropdown-item>
                        </b-dropdown>
                        <b-button v-on:click="copyRecipe()"><i class="fa fa-copy"></i> Copy</b-button>
                      </b-button-group>
                      <b-button v-if="recipe.isPrimitive && isUserMaterial" class="btn-info" :disabled="true">
                        <i class="fa fa-cubes"></i> In Inventory
                      </b-button>
                      <b-button v-if="recipe.isPrimitive && !isUserMaterial" class="btn-info" v-on:click="addUserMaterial()">
                        <i class="fa fa-cubes"></i> Add to Inventory
                      </b-button>
                      <b-button-group class="recipe-action-group" v-if="canEdit && !recipe.isArchived">
                        <b-button class="btn-info" v-if="recipe.isPrivate" v-on:click="publishRecipe()"><i class="fa fa-eye"></i> Publish</b-button>
                        <b-button class="btn-info" v-if="!recipe.isPrivate" v-on:click="unpublishRecipe()"><i class="fa fa-eye-slash"></i> Unpublish</b-button>
                        <b-button class="btn-info" v-if="!recipe.isPrivate" v-b-modal.archiveConfirmModal><i class="fa fa-lock"></i> Lock</b-button>
                        <b-button class="btn-info" v-on:click="editMeta()"><i class="fa fa-edit"></i> Edit Info</b-button>
                        <b-button class="btn-info" v-if="!recipe.isPrimitive && !recipe.isAnalysis" v-on:click="editComponents()"><i class="fa fa-list"></i> Edit Recipe</b-button>
                        <b-button class="btn-danger" v-b-modal.deleteConfirmModal><i class="fa fa-trash"></i></b-button>
                      </b-button-group>

                      <b-modal v-if="canEdit"
                               id="deleteConfirmModal"
                               title="Delete Recipe?"
                               v-on:ok="deleteRecipe"
                               ok-title="Delete Forever"
                      >
                        <p>Once deleted, you will not be able to retrieve this recipe!</p>
                      </b-modal>

                      <b-modal v-if="canEdit"
                               id="archiveConfirmModal"
                               :title="'Lock ' + materialTypeName"
                               v-on:ok="archiveRecipe"
                               ok-title="Lock"
                      >
                        <p>Locking a {{ materialTypeName }} ensures it will not change in the future.
                          You will not be able to edit, unpublish, or delete this {{ materialTypeName }}
                          once you lock it. Users will still be able to add photos and reviews.
                          Are you sure you want to proceed?</p>
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
                    :material="recipe"
                    v-on:imageupdated="imageUpdated"></material-image-gallery>

          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="card"> <!-- BEGIN Analysis Card -->
              <div class="card-body">
                <h3>
                  Analysis
                  <a href="http://help.glazy.org/concepts/analysis/" target="_blank" class="help-link"><i class="fa fa-question-circle fa-fw"></i></a>
                </h3>
                <!--
                <view-recipe-materials-analysis
                        :recipe="recipe"></view-recipe-materials-analysis>
                -->
                <div class="row mb-2" v-if="!recipe.isPrimitive && recipe.baseTypeId == glazeTypeId">
                  <div class="col-12 col-sm-12 col-md-auto mb-2">
                    <umf-traditional-notation
                            :material="material"
                            :showOxideList="false"
                            :squareSize="100">
                    </umf-traditional-notation>
                  </div>
                  <div v-if="'analysis' in recipe && 'umfAnalysis' in recipe.analysis"  
                      class="col-12 col-sm-12 col-md-auto">
                    <div class="row">
                      <div class="col-6 col-md-12 col-umf-r2oro">
                        <div class="card card-umf-info card-plain">
                          <div class="card-body">
                            <h6 class="card-title">R<sub>2</sub>O : RO</h6>
                            <p class="card-text">
                          <span class="oxide-colors-Na2O">
                            {{ Number(recipe.analysis.umfAnalysis.R2OTotal).toFixed(2) }}
                          </span>
                              :
                              <span class="oxide-colors-CaO">
                            {{ Number(recipe.analysis.umfAnalysis.ROTotal).toFixed(2) }}
                          </span>
                            </p>
                          </div>
                        </div>
                      </div>
                      <div class="col-6 col-md-12">
                        <div class="card card-umf-info card-plain">
                          <div class="card-body">
                            <h6 class="card-title">SiO<sub>2</sub> : Al<sub>2</sub>O<sub>3</sub></h6>
                            <p class="card-text">
                              {{ Number(recipe.analysis.umfAnalysis.SiO2Al2O3Ratio).toFixed(2) }}
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row" v-if="recipe.isPrimitive">
                  <div class="col-12 col-sm-12 col-md-auto mb-2">
                    Weight: 
                    <span class="badge badge-info">
                      {{ Number(recipe.analysis.weight).toFixed(3) }}
                    </span>&nbsp;&nbsp;
                    Calculated Oxide Weight: 
                    <span class="badge badge-info">
                      {{ Number(recipe.analysis.oxideWeight).toFixed(3) }}
                    </span>&nbsp;&nbsp;
                    <span v-if="'percentageAnalysis' in recipe.analysis">
                      LOI: 
                      <span class="badge badge-info">
                        {{ Number(recipe.analysis.percentageAnalysis.loi).toFixed(3) }}
                      </span>
                    </span>
                  </div>
                </div>
                <div v-if="recipe.isPrimitive || recipe.isAnalysis"
                     class="row">
                  <div class="col-md-12">
                      <simple-analysis-table :material="material"></simple-analysis-table>
                  </div>
                </div>
                <div v-else class="row">
                  <div class="col-md-12">
                    <b-tabs class="analysis-tabs" active>
                      <b-tab title="% Analysis">
                        <component-table
                                :material="material"
                                :isFormula="false"></component-table>
                      </b-tab>
                      <b-tab title="Formula" >
                        <component-table
                                :material="material"
                                :isFormula="true"></component-table>
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
                <h3>Similar Base Recipes</h3>
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
                <h3>Similar Unity Formula</h3>
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
                <h3 id="reviews">Reviews</h3>
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

        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <h3 id="comments">Comments & Questions</h3>
                <div class="row">
                  <div class="col-sm-12">
                    <comments-panel
                            v-on:commentsmodified="fetchRecipe"
                            :currentUser="current_user"
                            :material="recipe"
                    ></comments-panel>
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
                <h3 id="collections">Collections</h3>
                <div class="row">
                  <div class="col-sm-12">
                    <material-collections-panel
                            :material="recipe"
                    ></material-collections-panel>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="recipe.isPrimitive" class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <h3 id="recipesContaining">Recipes Containing this Material</h3>
                <div class="row">
                  <div class="col-sm-12">
                    <contains-material :material="recipe"></contains-material>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <b-modal id="collectModal"
                 ref="collectModal"
                 title="Collect Recipe"
                 v-on:ok="collectMaterial"
                 ok-title="Add"
        >
          <p>Collect in:</p>
          <div v-if="collections && collections.length > 0">
            <b-form-select v-model="selectedCollectionId"
                           :options="collections"
                           text-field="name"
                           value-field="id">
              <template slot="first">
                <option :value="0">-- Select a collection --</option>
              </template>
            </b-form-select>
          </div>

          <b-form-group
                  id="groupName"
                  label="Create a New Collection:"
                  :feedback="feedbackCollectionName"
                  :state="stateCollectionName"
          >
            <b-form-input id="name"
                          :state="stateCollectionName"
                          v-model.trim="newCollectionName"
                          placeholder="Collection Name"></b-form-input>
          </b-form-group>

        </b-modal>

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

      <AppFooter/>

    </main>

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

  // import JsonUmfSparkSvg from '../components/glazy/analysis/JsonUmfSparkSvg.vue'
  // 20180610 import MaterialAnalysisUmfSpark2Single from '../components/glazy/analysis/MaterialAnalysisUmfSpark2Single.vue';
  import UmfTraditionalNotation from '../components/glazy/analysis/UmfTraditionalNotation.vue';
  import ComponentTable from '../components/glazy/analysis/ComponentTable.vue'
  import SimpleAnalysisTable from '../components/glazy/analysis/SimpleAnalysisTable.vue'

  import UmfChart from '../components/glazy/recipe/UmfChart.vue'
  import SimilarBaseComponents from '../components/glazy/recipe/SimilarBaseComponents.vue'
  import SimilarUnityFormula from '../components/glazy/recipe/SimilarUnityFormula.vue'
  import ContainsMaterial from '../components/glazy/recipe/ContainsMaterial.vue'

  import GlazyHelper from '../components/glazy/helpers/glazy-helper'

  import EditMaterialMetadata from '../components/glazy/recipe/EditMaterialMetadata.vue'
  import EditRecipeComponents from '../components/glazy/recipe/EditRecipeComponents.vue'

  import ReviewsPanel from '../components/glazy/materialreviews/ReviewsPanel.vue'
  import CommentsPanel from '../components/glazy/materialcomments/CommentsPanel.vue'
  import MaterialCollectionsPanel from '../components/glazy/recipe/MaterialCollectionsPanel.vue'

  import MaterialCardDetail from '../components/glazy/search/MaterialCardDetail.vue'

  import AppFooter from './layout/AppFooter.vue'

  import VueTimeago from 'vue-timeago'

  import Vue from 'vue'

  export default {
    name: 'Recipe',
    metaInfo () {
      return {
        title: this.meta.title,
        meta: [
          {
            'vmid': "description",
            'property': 'description',
            'content': this.meta.description
          },
          {
            'property': 'og:description',
            'content': this.meta.description
          },
          {
            'property': 'og:title',
            'content': this.meta.title
          },
          {
            'property': 'og:url',
            'content': this.meta.url
          },
          {
            'property': 'og:image',
            'content': this.meta.image
          },
          {
            'property': 'og:image:width',
            'content': 800
          },
          {
            'property': 'og:image:height',
            'content': 800
          },
          {
            'property': 'twitter:description',
            'content': this.meta.description
          }
        ]
      }
    },
    /**
     <meta property="og:type" content="website">
     <meta property="og:site_name" content="Glazy">
     <meta property="og:url" content="{!! URL::full('recipes.show', array('recipes' => $recipe->id)) !!}">
     <meta property="og:title" content="{{ $metatitle }}">
     <meta property="og:image" content="{!! 'http://glazy.org'.$imgurl !!}">
     <meta property="og:description" content="{{ $meta_description }}">

     <meta name="description" content="{{ $meta_description }}">
     @if (!empty($recipe->created_by_user))
     <meta name="author" content="{!! $recipe->created_by_user->first_name !!} {!! $recipe->created_by_user->last_name !!}">
     @endif

     */
    components: {
      MaterialTypeBreadcrumbs,
      FiringCard,
      StarRating,
      MaterialRecipeCalculator,
      MaterialImageGallery,
      UmfChart,
      // MaterialAnalysisUmfSpark2Single,
      UmfTraditionalNotation,
      ComponentTable,
      SimpleAnalysisTable,
      SimilarBaseComponents,
      SimilarUnityFormula,
      ContainsMaterial,
      EditMaterialMetadata,
      EditRecipeComponents,
      MaterialCollectionsPanel,
      ReviewsPanel,
      CommentsPanel,
      VueTimeago,
      MaterialCardDetail,
      AppFooter
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
        infoMessageSeconds: 0,
        isProcessing: false,
        apiError: null,
        serverError: null,
        glazeTypeId: new MaterialTypes().GLAZE_TYPE_ID,
        glazyHelper: new GlazyHelper(),
        isEditRequest: false,
        searchRoute: null,
        selectedCollectionId: 0,
        newCollectionName: '',
        materialToCollect: 0,
        actionMessage: null,
        actionMessageSeconds: 0
      }
    },

    mounted() {
      if ('isEdit' in this.$route.query && this.$route.query.isEdit) {
        this.isEditRequest = true
      }
      this.fetchRecipe()
    },

    beforeRouteEnter (to, from, next) {
      if (from.name === 'search' || from.name === 'user' ||
        from.name === 'materials' || from.name === 'user-materials') {
        // cache the search route for "back" button
        // access this component via vm, not this
        next(vm => {
          vm.searchRoute = from
        })
      } else {
        next()
      }
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
      materialTypeName: function () {
        if (!this.isLoaded) return ''
        if (this.recipe.isPrimitive) return 'Material'
        return 'Recipe'
      },
      mainClass: function() {
        if (this.searchItems && this.searchItems.length > 0) {
          return 'col-md-9'
        }
        return 'col-md-12'
      },
      canEdit: function () {
        // Only the creator of a recipe can edit it
        if (this.$auth.check() &&
          this.$auth.user().id === this.recipe.createdByUserId) {
          return true
        }
        return false
      },
      meta: function () {
        var meta = {
          title: 'Recipe',
          description: 'Glazy Ceramics Recipe',
          url: GLAZY_APP_URL + this.$route.fullPath,
          image: ''
        }
        if (this.recipe &&
          'thumbnail' in this.recipe &&
          this.recipe.thumbnail) {
          meta.image = this.glazyHelper.getMediumImageUrl(this.recipe, this.recipe.thumbnail)
        }
        return meta
      },
      searchItems: function () {
        return this.$store.getters['search/searchItems']
      },
      collections () {
        var user = this.$auth.user()
        if (user && user.collections && user.collections.length > 0) {
          return user.collections
        }
        return null
      },
      feedbackCollectionName() {
        return this.newCollectionName.length > 0 ? 'Enter at least 3 characters' : 'Please enter a name';
      },
      stateCollectionName() {
        return this.newCollectionName.length > 2 ? 'valid' : 'invalid';
      },
      isUserMaterial() {
        if (this.isLoaded && this.recipe.isPrimitive) {
          var user = this.$auth.user()
          if (user && user.user_materials && user.user_materials.length > 0) {
            for (var i = 0, len = user.user_materials.length; i < len; i++) {
              if (user.user_materials[i].material_id === this.recipe.id) {
                // This material id is already in the user's inventory
                return true
              }
            }
          }
        }
        // This material is not in the users inventory
        return false
      }
    },
    beforeRouteUpdate (to, from, next) {
      // Ensure that the back/forward buttons work within this component/route
      this.isEditComponents = false
      this.isEditMeta = false
      // this.recipe = null
      this.sendRecipeGetRequest('/recipes/' + to.params.id)
      next()
    },
    methods : {

      fetchRecipe: function (){
        this.sendRecipeGetRequest('/recipes/' + this.$route.params.id, null, 0)
      },

      publishRecipe: function () {
        if (this.recipe) {
          this.sendRecipeGetRequest('/recipes/' + this.recipe.id + '/publish', this.materialTypeName + ' Published!', 5)
        }
      },

      unpublishRecipe: function () {
        if (this.recipe) {
          this.sendRecipeGetRequest('/recipes/' + this.recipe.id + '/unpublish', this.materialTypeName + ' Unpublished!', 5)
        }
      },

      archiveRecipe: function () {
        if (this.recipe) {
          this.sendRecipeGetRequest('/recipes/' + this.recipe.id + '/archive', this.materialTypeName + ' Locked!', 5)
        }
      },

      exportRecipe: function (exportType) {
        if (!this.recipe) {
          return
        }
        this.isProcessing = true
        var fileType = 'txt'
        if (exportType === 'Insight') {
          fileType = 'xml'
        }
        Vue.axios.get(Vue.axios.defaults.baseURL + '/recipes/' + this.recipe.id + '/export/' + exportType)
          .then((response) => {
          if (response.data.error) {
            this.apiError = response.data.error
            console.log(this.apiError)
            this.isProcessing = false
          } else {
            this.isProcessing = false
            // https://stackoverflow.com/questions/3665115/create-a-file-in-memory-for-user-to-download-not-through-server
            var a = window.document.createElement('a');
            a.href = window.URL.createObjectURL(new Blob([response.data], {type: 'text/text'}));
            a.download = 'Glazy_ID_' + this.recipe.id + '_' + exportType + '.' + fileType;
            // Append anchor to body.
            document.body.appendChild(a);
            a.click();
            // Remove anchor from body
            document.body.removeChild(a);
          }
        })
        .catch(response => {
          this.serverError = response;
          this.isProcessing = false
        })
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
        var materialObj = new Material();
        this.material = materialObj.createFromJson(this.recipe);
      },

      updatedRecipeMeta: function () {
        this.fetchRecipe()
        this.infoMessageSeconds = 5
        this.isRecipeUpdated = true
        this.isEditMeta = false
      },

      updatedRecipeComponents: function () {
        this.fetchRecipe()
        this.infoMessageSeconds = 5
        this.isRecipeUpdated = true
        this.isEditComponents = false
      },

      imageUpdated: function () {
        this.fetchRecipe()
      },

      editMeta: function () {
        this.isEditMeta = true
        window.scrollTo(0, 0)
      },

      editMetaCancel: function () {
        this.isEditMeta = false
      },

      editComponents: function () {
        this.isEditComponents = true
        window.scrollTo(0, 0)
      },

      editComponentsCancel: function () {
        this.isEditComponents = false
      },

      isProcessingRecipe: function () {
        this.isProcessing = true
      },

      collectMaterialSelect(id) {
        if (id) {
          this.materialToCollect = id
          this.$refs.collectModal.show()
        }
      },

      collectMaterial() {
        if (!this.materialToCollect) {
          return
        }
        if (!this.selectedCollectionId && !this.newCollectionName) {
          return
        }
        this.isProcessingLocal = true
        var form = {
          collectionName: this.newCollectionName,
          collectionId: this.selectedCollectionId,
          materialId: this.materialToCollect
        }
        Vue.axios.post(Vue.axios.defaults.baseURL + '/collectionmaterials', form)
          .then((response) => {
          if (response.data.error) {
            this.apiError = response.data.error
            console.log(this.apiError)
            this.isProcessingLocal = false
          } else {
            this.isProcessingLocal = false
            this.actionMessage = 'Collected.'
            this.actionMessageSeconds = 5
            this.fetchRecipe();  // Update Recipe Info
            if (this.newCollectionName) {
              // Refresh user collections
              this.$auth.fetch({
                success(res) {
                },
                error() {
                  console.log('error fetching user');
                }
              })
            }
          }
          this.newCollectionName = ''
          this.materialToCollect = 0
        })
        .catch(response => {
          this.serverError = response
          this.isProcessingLocal = false
          this.newCollectionName = ''
          this.materialToCollect = 0
        })
      },

      actionMessageCountdown(seconds) {
        this.actionMessageSeconds = seconds
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

      addUserMaterial() {
        this.isProcessingLocal = true
        Vue.axios.get(Vue.axios.defaults.baseURL + '/usermaterials/addMaterial/' + this.recipe.id)
          .then((response) => {
          if (response.data.error) {
            this.apiError = response.data.error
            console.log(this.apiError)
            this.isProcessingLocal = false
          } else {
            this.isProcessingLocal = false
            this.actionMessage = 'Material added to your inventory.'
            this.actionMessageSeconds = 5
            // Refresh user inventory materials
            this.$auth.fetch({
              success(res) {
                // Refresh the recipe
                this.fetchRecipe()
              },
              error() {
                console.log('error fetching user');
              }
            })
          }
        })
        .catch(response => {
          this.serverError = response
          console.log(response)
          this.isProcessingLocal = false
        })
      },

      sendRecipeGetRequest: function (url, successMessage, successMessageSeconds) {
        this.apiError = null
        this.isProcessing = true
        Vue.axios.get(Vue.axios.defaults.baseURL + url)
          .then((response) => {
          this.isProcessing = false
          if (response.data.error) {
            this.apiError = response.data.error
            console.log(this.apiError)
          } else {
            this.recipe = null
            this.recipe = response.data.data
            this.meta.title = this.recipe.name
            this.meta.description = this.recipe.name
            var materialObj = new Material()
            this.material = Material.createFromJson(this.recipe)

            if (this.searchItems && this.searchItems.length > 0) {
              // doesn't work
              //this.$router.push({ path: this.$route.path + '#material-card-' + this.recipe.id })
              //this.$nextTick(() => document.getElementById('#material-card-' + this.recipe.id).scrollIntoView())
              //Vue.nextTick(function () {
              //  document.getElementById('#material-card-' + this.recipe.id).scrollIntoView()
              //}.bind(this))
              //window.setTimeout(function () {
              //  document.getElementById('#material-card-' + this.recipe.id).scrollIntoView()
              //}.bind(this), 2000)
            }

            if (this.isEditRequest && this.canEdit) {
              // User just created this recipe in calculator
              this.isEditRequest = false
              this.editMeta()
            }

            if (successMessage && successMessageSeconds) {
              this.actionMessage = successMessage
              this.actionMessageSeconds = successMessageSeconds
            }
          }
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
      }

    }
  }
</script>


<style>

  .sidebar {
    background-color: #efefef;
    position: fixed;
    top: 50px;
    bottom: 0;
    left: 0;
    z-index: 1000;
    padding: 15px 15px;
    overflow-x: hidden;
    overflow-y: auto; /* Scrollable contents if viewport is shorter than content. */
  }

  .recipe-result {
    background-color: #dedede;
    padding-top: 15px;
    padding-bottom: 64px;
  }

  .sidebar .search-title {
    margin-bottom: 0;
  }

  .sidebar .search-results {
    margin-top: 5px;
    background-color: #efefef;
  }

  .recipe-info-card .card-description {
    margin-top: 20px;
  }

  .recipe-info-card .card-body .card-title {
    font-size: 2.25em;
    margin-bottom: 0;
  }

  .recipe-info-card .card-body .author {
    margin-top: 10px;
  }

  .recipe-action-group .btn {
    padding: 11px 16px;
  }

  .material-metadata {
    font-size: 1em;
  }

  .material-metadata .badge {
    font-size: 1em;
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

  .analysis-layout-table {
    padding: 0 20px;
  }

  .analysis-layout-table tr td {
    padding: 0;
  }

  .col-umf-r2oro {
    padding-right: 0;
  }

  .card-umf-info {
    background-color: #efefef;
    max-width: 11em;
    margin-bottom: 10px;
  }

  .card-umf-info .card-body {
    padding: 5px;
    text-align: center;
  }

  .card-umf-info .card-body .card-title {
    color: #999999;
    margin: 0;
    text-transform: none;
  }

  .card-umf-info .card-body .card-text {
    font-size: 1.825em;
  }

</style>