<template>
  <div class="row search-row" v-cloak>

    <div class="load-container load7 fullscreen" v-if="isProcessing">
      <div class="loader">Searching...</div>
    </div>

    <nav v-bind:class="sidebarClass" class="sidebar d-none d-md-block">

      <div v-if="searchUser" class="card card-glazy-profile">
        <div class="card-avatar">
          <a href="#pablo">
            <img src="/img/profile.jpg" alt="..."  class="rounded-circle img-raised">
          </a>
        </div>
        <div class="card-body">
          <h4 class="card-title">{{ searchUser.name }}</h4>
          <h6 v-if="'profile' in searchUser && searchUser.profile.countryName" class="category text-gray">
            {{ searchUser.profile.countryName }}
          </h6>
          <div v-if="'profile' in searchUser">
            <a v-if="'url' in searchUser.profile && searchUser.profile.url"
               v-bind:href="'http://' + searchUser.profile.url"
               target="_blank"
               class="btn btn-icon btn-sm btn-round btn-default">
              <i class="fa fa-link"></i>
            </a>
            <a v-if="'facebook' in searchUser.profile && searchUser.profile.facebook"
               v-bind:href="'https://www.facebook.com/' + searchUser.profile.facebook"
               target="_blank"
               class="btn btn-icon btn-sm btn-round btn-facebook">
              <i class="fa fa-facebook"></i>
            </a>
            <a v-if="'instagram' in searchUser.profile && searchUser.profile.instagram"
               v-bind:href="'https://www.instagram.com/' + searchUser.profile.instagram"
               target="_blank"
               class="btn btn-icon btn-sm btn-round btn-instagram">
              <i class="fa fa-instagram"></i>
            </a>
            <a v-if="'pinterest' in searchUser.profile && searchUser.profile.pinterest"
               v-bind:href="'https://www.pinterest.com/' + searchUser.profile.pinterest"
               target="_blank"
               class="btn btn-icon btn-sm btn-round btn-pinterest">
              <i class="fa fa-pinterest"></i>
            </a>
          </div>
        </div>
      </div>

      <b-btn
              v-b-tooltip.hover
              :title="expandbuttonTooltip"
              size="sm"
              type="reset"
              variant="secondary"
              @click.prevent="toggleExpandMap"
              class="btn-sm expand-button"
              v-html="expandButtonText">
      </b-btn>

      <div v-if="hasResults" id="umf-d3-chart-container">
        <umf-d3-chart
                :recipeData="itemlist"
                :width="chartWidth"
                :height="chartHeight"
                :margin="chartMargin"
                :axisLabelFontSize="'0.75rem'"
                :stullLabelsFontSize="'0.5rem'"
                :chartDivId="'umf-d3-chart-container'"
                :baseTypeId="searchQuery.params.base_type"
                :colortype="'r2o'"
                :showRecipes="true"
                :showCones="false"
                :showStullChart="true"
                :showStullLabels="false"
                :showZoomButtons="false"
                :showAxesLabels="true"
                :highlightedRecipeId="highlightedRecipeId"
                :unHighlightedRecipeId="unHighlightedRecipeId"
                :xoxide="searchQuery.params.x"
                :yoxide="searchQuery.params.y"
                v-on:clickedUmfD3Recipe="clickedD3Chart"
        >
        </umf-d3-chart>
      </div>

      <div class="row">
        <div class="col-md-12">
          <search-form
                  v-if="searchQuery"
                  :searchQuery="searchQuery"
                  :searchUser="searchUser"
                  v-on:searchrequest="search"
                  :isLarge="isMapExpanded">
          </search-form>
        </div>
      </div>
    </nav>

    <main v-bind:class="mainClass" role="main" class="ml-sm-auto search-results">


      <b-alert v-if="apiError" show variant="danger">
        API Error: {{ apiError.message }}
      </b-alert>
      <b-alert v-if="serverError" show variant="danger">
        Server Error: {{ serverError }}
      </b-alert>


      <div class="row">

        <div class="col-sm-12 d-xl-none d-lg-none d-md-none">
          <div class="card card-glazy-profile">
            <div class="card-avatar">
              <a href="#pablo">
                <img src="/img/profile.jpg" alt="..."  class="rounded-circle img-raised">
              </a>
            </div>
            <div class="card-body">
              <h4 class="card-title">Derek Au</h4>
              <h6 class="category text-gray">
                United States
              </h6>
              <a href="#pablo" class="btn btn-icon btn-sm btn-round btn-default"><i class="fa fa-info"></i></a>
              <a href="#pablo" class="btn btn-icon btn-sm btn-round btn-twitter"><i class="fa fa-twitter"></i></a>
              <a href="#pablo" class="btn btn-icon btn-sm btn-round btn-facebook"><i class="fa fa-facebook-square"></i></a>
              <a href="#pablo" class="btn btn-icon btn-sm btn-round btn-google"><i class="fa fa-google"></i></a>
            </div>
          </div>
        </div>

        <div class="col-sm-12">
          <search-breadcrumbs :searchQuery="searchQuery"
                              :searchUser="searchUser"></search-breadcrumbs>
        </div>
        <div class="col-sm-12 d-xl-none d-lg-none d-md-none">
          <search-form
                  v-if="searchQuery"
                  :searchQuery="searchQuery"
                  :searchUser="searchUser"
                  v-on:searchrequest="search"
                  :isLarge="false">
          </search-form>
        </div>

      </div>

      <filter-paginator
              v-if="hasResults && hasPagination"
              :pagination="pagination"
              :view="searchQuery.params.view"
              :order="order"
              :item_type_name="'recipes'"
              v-on:pagerequest="pageRequest"
              v-on:orderrequest="orderRequest"
              v-on:viewrequest="viewRequest">
      </filter-paginator>


      <div class="row">
        <div
                class="alert alert-warning col-sm-12"
                role="alert"
                v-if="(!itemlist || itemlist.length <= 0) && !isProcessing">
          <div class="container">
            <div class="alert-icon">
              <i class="fa fa-bell"></i>
            </div>
            <strong>No recipes found.</strong>
            Please try a broader search, or reset the search form.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">
                                <i class="fa fa-remove"></i>
                            </span>
            </button>
          </div>
        </div>

      </div>


      <section class="row" v-if="(searchQuery.params.view === 'cards') && !isProcessing">
        <div v-bind:class="recipeCardClass" class=""
             v-for="(recipe, index) in itemlist">
          <RecipeCardThumb
                  :recipe="recipe"
                  v-on:highlightRecipe="highlightRecipe"
                  v-on:unhighlightRecipe="unhighlightRecipe"
                  v-on:copyRecipeRequest="copyRecipe"
                  v-on:deleteRecipeRequest="confirmDeleteRecipe"
                  v-on:collectRecipeRequest="collectRecipeSelect"
          ></RecipeCardThumb>
        </div>
      </section>
      <section class="row" v-else-if="!isProcessing">
        <table class="table table-hover recipe-detail-table">
          <tbody>
            <tr is="RecipeCardRow"
                v-for="(recipe, index) in itemlist"
                :recipe="recipe"
                v-on:highlightRecipe="highlightRecipe"
                v-on:unhighlightRecipe="unhighlightRecipe">
            </tr>
          </tbody>
        </table>
      </section>

      <filter-paginator
              v-if="hasResults && hasPagination && !isProcessing"
              :pagination="pagination"
              :view="searchQuery.params.view"
              :order="order"
              :item_type_name="'recipes'"
              v-on:pagerequest="pageRequest"
              v-on:orderrequest="orderRequest"
              v-on:viewrequest="viewRequest">
      </filter-paginator>

      <AppFooter/>

    </main>

    <b-modal id="deleteConfirmModal"
             ref="deleteConfirmModal"
             title="Delete Recipe?"
             v-on:ok="deleteRecipe"
             ok-title="Delete Forever"
    >
      <p>Once deleted, you will not be able to retrieve this recipe!</p>
    </b-modal>

    <b-modal id="collectModal"
             ref="collectModal"
             title="Collect Recipe"
             v-on:ok="collectRecipe"
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

  </div>
</template>

<script>
  import Analysis from 'ceramicscalc-js/src/analysis/Analysis'
  import PercentageAnalysis from 'ceramicscalc-js/src/analysis/PercentageAnalysis'
  import Material from 'ceramicscalc-js/src/material/Material'
  import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'
  import MaterialTypes from 'ceramicscalc-js/src/material/MaterialTypes'

  import UmfD3Chart from 'vue-d3-stull-charts/src/components/UmfD3Chart.vue'

  import MaterialAnalysisTableCompare from '../components/glazy/analysis/MaterialAnalysisTableCompare.vue';
  import MaterialAnalysisUmfSpark2Single from '../components/glazy/analysis/MaterialAnalysisUmfSpark2Single.vue';
  import MaterialAnalysisPercentTableCompare from '../components/glazy/analysis/MaterialAnalysisPercentTableCompare.vue';

  import RecipeCardThumb from '../components/glazy/search/RecipeCardThumb.vue'
  import RecipeCardRow from '../components/glazy/search/RecipeCardRow.vue'

  import SearchBreadcrumbs from '../components/glazy/search/SearchBreadcrumbs.vue'

  import SearchForm from '../components/glazy/search/SearchForm.vue'
  import SearchQuery from '../components/glazy/search/search-query'
  import FilterPaginator from '../components/glazy/search/FilterPaginator.vue'

  import AppFooter from './layout/AppFooter.vue'

  import Vue from 'vue'

  export default {
    name: 'Search',
    components: {
      AppFooter,
      FilterPaginator,
      RecipeCardThumb,
      RecipeCardRow,
      UmfD3Chart,
      SearchForm,
      SearchBreadcrumbs
    },
    props: {
      isembedded: {
        type: Boolean,
        default: false
      },

      userId: {
        type: Number,
        default: null
      },

      selectedCollection: {
        type: Object,
        default: null
      },

      isPrimitive: {
        type: Number,
        default: 0
      }

    },
    data() {
      return {
        oxides: new GlazyConstants().OXIDE_NAME_UNICODE_SELECT,
        recipes: null,
        // searchQuery: new SearchQuery(),
        searchQuery: null,
        searchUser: null,
        itemlist: [],
        pagination: null,
        isProcessing: false,
        materialTypes: new MaterialTypes(),
        constants: new GlazyConstants(),
        chartHeight: 200,
        chartWidth: 0,
        chartMargin: {
          left: 24,
          right: 10,
          top: 0,
          bottom: 12
        },
        isMapExpanded: false,
        expandButtonText: '<i class="fa fa-angle-double-right"></i>',
        expandbuttonTooltip: 'Show More Map',
        sidebarClass: 'col-md-3',
        mainClass: 'col-md-9',
        recipeCardClass: 'col-lg-3 col-md-4 col-sm-6',
        currentPage: null,
        isThumbnailView: true,
        highlightedRecipeId: {},
        unHighlightedRecipeId: {},
        selectedCollectionId: 0,
        toDeleteRecipeId: 0,
        newCollectionName: '',
        minSearchTextLength: 3,
        apiError: null,
        serverError: null
      }
    },
    computed: {
      isLoaded () {
        return true;
      },

      hasResults () {
        if (this.itemlist
          && this.itemlist.length > 0) {
          return true;
        }
        return false;
      },

      hasPagination () {
        if (this.pagination) {
          return true;
        }
        return false;
      },

      order () {
        return this.searchQuery.params.order;
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
      }
    },

    created() {

      this.searchUser = null
      this.searchQuery = new SearchQuery(this.$route.query)

      if (this.$route.name === 'materials' ||
        this.$route.name === 'user-materials') {
        // Primitive search
        if (!this.searchQuery.params.base_type) {
          this.searchQuery.params.base_type = 1
        }
      } else if (!this.searchQuery.params.base_type) {
        this.searchQuery.params.base_type = this.materialTypes.GLAZE_TYPE_ID
      }
      if (this.$route.params && this.$route.params.id) {
        this.searchQuery.params.u = this.$route.params.id
      }
      this.fetchitemlist()
    },
    /*
    beforeRouteUpdate (to, from, next) {
      console.log('$$$$$$$$$$$$$$$$$ before route update')

      this.searchUser = null
      this.searchQuery = new SearchQuery(to.query)
      if (!this.searchQuery.params.base_type) {
        this.searchQuery.params.base_type = this.materialTypes.GLAZE_TYPE_ID
      }
      if ('params' in to && to.params.id) {
        this.searchQuery.params.u = to.params.id
      }
      this.fetchitemlist()
      next()
    },
    */
    watch: {

      $route (route) {
        if (route.hash) {
          // This is only an internal link, no need to requery
          return
        }
        this.searchUser = null
        this.searchQuery = new SearchQuery(route.query)

        if (route.name === 'materials' ||
          route.name === 'user-materials') {
          // Primitive search
          if (!this.searchQuery.params.base_type) {
            this.searchQuery.params.base_type = 1
          }
        } else if (!this.searchQuery.params.base_type) {
          // Composite search
          this.searchQuery.params.base_type = this.materialTypes.GLAZE_TYPE_ID
        }
        if ('params' in route && route.params.id) {
          this.searchQuery.params.u = route.params.id
        }
        this.fetchitemlist()
      }
    },
    mounted() {
      setTimeout(() => {
          this.handleResize()
        }, 2000)
      window.addEventListener('resize', this.handleResize)
    },
    methods: {

      newSearch () {
        var myQuery = this.searchQuery.getMinimalQuery()
        if ('u' in myQuery) {
          delete myQuery.u // 'u' param is in the route
        }
        // Update the route.  Actual search triggered in beforeRouteUpdate
        this.$router.push({path: this.$route.path, query: myQuery})
      },

      fetchitemlist () {
        // New Search, reset all errors
        this.serverError = null
        this.apiError = null

        var myQuery = this.searchQuery.getMinimalQuery()

        var querystring = this.searchQuery.toQuerystring(myQuery)
        this.isProcessing = true
        console.log('SEARCH: ' + querystring)

        Vue.axios.get(Vue.axios.defaults.baseURL + '/search?' + querystring)
          .then((response) => {
            if (response.data.error) {
              this.apiError = response.data.error
              console.log(this.apiError)
              this.itemlist = null
              this.isProcessing = false
            } else {
              this.itemlist = response.data.data
              document.title = 'Search';
              if (!this.itemlist) {
                // Make sure itemlist is always defined, and an array
                this.itemlist = []
              }
              this.pagination = response.data.meta.pagination

              if ('user' in response.data.meta && 'data' in response.data.meta.user) {
                this.searchUser = response.data.meta.user.data
              }

              this.isProcessing = false
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
      },

      search (query) {
        this.searchQuery.setFromSearchForm(query.params)

        // New search, so reset the page number
        this.searchQuery.params.p = null

        this.newSearch()
      },
      pageRequest (p) {
        this.searchQuery.params.p = p
        this.newSearch()
      },
      orderRequest (order) {
        this.searchQuery.params.order = order
        this.newSearch()
      },
      viewRequest (view) {
        this.searchQuery.params.view = view
      },
      toggleExpandMap () {
        if (this.isMapExpanded) {
          this.expandButtonText = '<i class="fa fa-angle-double-right"></i>'
          this.expandbuttonTooltip = 'Show More Map'
          this.sidebarClass = 'col-md-3'
          this.mainClass = 'col-md-9'
          this.recipeCardClass = 'col-lg-3 col-md-4 col-sm-6'
          this.chartHeight = 200
        } else {
          this.expandButtonText = '<i class="fa fa-angle-double-left"></i>'
          this.expandbuttonTooltip = 'Show Less Map'
          this.sidebarClass = 'col-md-6'
          this.mainClass = 'col-md-6'
          this.recipeCardClass = 'col-lg-4 col-md-6 col-sm-6'
          this.chartHeight = 300
        }
        this.isMapExpanded = !this.isMapExpanded
        this.$root.$emit('bv::hide::tooltip')
        setTimeout(() => {
          this.handleResize()
        }, 300)

      },

      thumbnailView () {
        this.isThumbnailView = true
      },
      listView () {
        this.isThumbnailView = false
      },
      handleResize: function () {
        if (document.getElementById('umf-d3-chart-container')) {
          // this.chartHeight = document.getElementById('umf-d3-chart-container').clientHeight
          this.chartWidth = document.getElementById('umf-d3-chart-container').clientWidth
        }
      },
      clickedD3Chart (data) {
        this.$router.push({ path: this.$route.path + '#recipe-card-' + data.id, query: this.$route.query })
      },
      highlightRecipe: function (id) {
        // this.highlightedRecipeId = id
        this.highlightedRecipeId = { id: id }
      },
      unhighlightRecipe: function (id) {
        // this.highlightedRecipeId = 0
        this.unHighlightedRecipeId = { id: id }
      },
      collectRecipeSelect(id) {
        if (id) {
          this.$refs.collectModal.show();
        }
      },

      collectRecipe(id) {

      },

      copyRecipe: function (id) {
        if (!id) {
          return
        }
        this.isProcessing = true
        Vue.axios.get(Vue.axios.defaults.baseURL + '/recipes/' + id + '/copy')
          .then((response) => {
          if (response.data.error) {
            this.apiError = response.data.error
            console.log(this.apiError)
            this.isProcessing = false
          } else {
            this.isProcessing = false
            var recipeCopy = response.data.data;
            this.$router.push({ name: 'recipes', params: { id: recipeCopy.id }})
          }
        })
        .catch(response => {
          this.serverError = response;
          this.isProcessing = false
        })
      },

      confirmDeleteRecipe: function(id) {
        if (id) {
          this.toDeleteRecipeId = id
          this.$refs.deleteConfirmModal.show();
        }
      },

      deleteRecipe: function() {
        if (this.toDeleteRecipeId) {
          Vue.axios.delete(Vue.axios.defaults.baseURL + '/recipes/' + this.toDeleteRecipeId)
            .then((response) => {
            if (response.data.error) {
              this.apiError = response.data.error
              console.log(this.apiError)
              this.isProcessing = false
            } else {
              this.toDeleteRecipeId = 0
              this.isProcessing = false
              this.newSearch()
            }
          })
          .catch(response => {
            this.toDeleteRecipeId = 0
            this.serverError = response;
            this.isProcessing = false
          })
        }
      }

    }
  }
</script>

<style>

  .search-row {
    background-color: #dedede;
  }

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

  .expand-button {
    position: absolute;
    top: 144px;
    right: -4px;
    z-index: 1001;
    font-size: 1.25rem;
    line-height: 1;
  }

  #umf-d3-chart-container {
    /* need fix tip bug position: relative; */
  }

  .card-glazy-profile {
    color: #FFFFFF;
    background-color: #2c2c2c;
    margin-top: 0;
    margin-bottom: 10px;
  }

  .card-glazy-profile .card-avatar {
    max-width: 64px;
    max-height: 64px;
  }

  .card-glazy-profile .card-avatar img {
    float: left;
    margin: 10px;
  }

  .card-glazy-profile .card-body {
    padding: 0 10px;
  }

  .card-glazy-profile .card-body .card-title {
    margin-top: 10px;
    font-size: 1.25em;
  }

  .card-glazy-profile .card-body .btn {
    margin: 0 1px 10px 1px;
  }

  .chart-form {
    padding: 0 15px;
  }

  .search-results {
    background-color: #dedede;
    padding-top: 15px;
    padding-bottom: 64px;
  }

  .search-pagination {
    margin-bottom: 10px;
    margin-top: 5px;
  }

  .search-buttons {
    margin-bottom: 16px;
    margin-top: 5px;
  }

  .search-buttons .btn {
    margin-bottom: 0px;
    margin-top: 0px;
  }

  .recipe-detail-table {
    border-top-style: hidden;
    border-bottom-style: hidden;
  }

  .search-title {
    margin-top: 0;
    margin-bottom: 5px;
  }

  .d3-tip {
    font-size: .875rem;
    line-height: 1.125rem;
    font-weight: normal;
    padding: .75rem;
    background: rgba(0, 0, 0, 0.7);
    color: #fff;
    border-radius: 2px;
    z-index: 999999;
    position: relative;
  }

  .vc-chrome {
    width: 100% !important;
    border: 1px solid #ced4da !important;
    border-radius: 0.2rem !important;
    box-shadow: none !important;
  }

</style>