<template>
  <div class="row search-row" v-cloak>

    <div class="load-container load7 fullscreen" v-if="isProcessing">
      <div class="loader">Searching...</div>
    </div>

    <nav v-bind:class="sidebarClass" class="sidebar d-none d-md-block">

      <h4 class="search-title" v-html="searchTitle"></h4>
      <h6 class="search-subtitle" v-if="searchSubtitle" v-html="searchSubtitle"></h6>

      <b-button
              size="sm"
              type="reset"
              variant="secondary"
              @click.prevent="toggleExpandMap"
              class="btn-sm expand-button"
              v-html="expandButtonText">
      </b-button>

      <div v-if="hasResults" id="umf-d3-chart-container" class="w-100">
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
                :highlightedRecipeId="{highlightedRecipeId}"
                :unHighlightedRecipeId="{unHighlightedRecipeId}"
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
                  v-on:searchrequest="search"
                  :isLarge="isMapExpanded">
          </search-form>
        </div>
      </div>
    </nav>

    <main v-bind:class="mainClass" role="main" class="ml-sm-auto pt-3 search-results">


      <b-alert v-if="apiError" show variant="danger">
        API Error: {{ apiError.message }}
      </b-alert>
      <b-alert v-if="serverError" show variant="danger">
        Server Error: {{ serverError }}
      </b-alert>

      <div class="row">

        <div class="col-sm-12 d-xl-none d-lg-none d-md-none">

          <h5 class="search-title" v-html="searchTitle"></h5>
          <h6 class="search-subtitle" v-if="searchSubtitle" v-html="searchSubtitle"></h6>

          <search-form
                  v-if="searchQuery"
                  :searchQuery="searchQuery"
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

  import StaticMaterialList from '../../static/data/material-list.json'

  import UmfD3Chart from 'vue-d3-stull-charts/src/components/UmfD3Chart.vue'

  import MaterialAnalysisTableCompare from '../components/glazy/analysis/MaterialAnalysisTableCompare.vue';
  import MaterialAnalysisUmfSpark2Single from '../components/glazy/analysis/MaterialAnalysisUmfSpark2Single.vue';
  import MaterialAnalysisPercentTableCompare from '../components/glazy/analysis/MaterialAnalysisPercentTableCompare.vue';

  import RecipeCardThumb from '../components/glazy/search/RecipeCardThumb.vue'
  import RecipeCardRow from '../components/glazy/search/RecipeCardRow.vue'

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
      SearchForm
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
        itemlist: [],
        pagination: null,
        isProcessing: false,
        materialTypes: new MaterialTypes(),
        constants: new GlazyConstants(),
        chartHeight: 200,
        chartWidth: 300,
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
        highlightedRecipeId: 0,
        unHighlightedRecipeId: 0,
        selectedCollectionId: 0,
        toDeleteRecipeId: 0,
        newCollectionName: '',
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

      searchTitle () {
        var title = ''
        var hasTitle = false

        if (this.searchQuery.params.keywords) {
          title += '"' + this.searchQuery.params.keywords + '"'
          hasTitle = true
        }

        if (this.searchQuery.params.base_type
          && this.materialTypes.LOOKUP[this.searchQuery.params.base_type]) {
          if (hasTitle) {
            title += ', '
          }
          var type = this.materialTypes.LOOKUP[this.searchQuery.params.base_type]
          if (this.searchQuery.params.type
            && this.materialTypes.LOOKUP[this.searchQuery.params.type]) {
            type = this.materialTypes.LOOKUP[this.searchQuery.params.type]
          }
          title += type
        }

        if (!title) {
          title = 'Search'
        }

        return title
      },

      searchSubtitle () {
        var subtitle = ''
        var hasSubtitle = false

        if (this.searchQuery.params.cone_id
          && this.constants.ORTON_CONES_LOOKUP[this.searchQuery.params.cone_id]) {
          subtitle += '△' + this.constants.ORTON_CONES_LOOKUP[this.searchQuery.params.cone_id]
          hasSubtitle = true
        }

        if (this.searchQuery.params.atmosphere_id
          && this.constants.ATMOSPHERE_LOOKUP[this.searchQuery.params.atmosphere_id]) {
          if (hasSubtitle) {
            subtitle += ', '
          }
          subtitle += this.constants.ATMOSPHERE_LOOKUP[this.searchQuery.params.atmosphere_id]
          hasSubtitle = true
        }

        if (this.searchQuery.params.surface_type
          && this.constants.SURFACE_LOOKUP[this.searchQuery.params.surface_type]) {
          if (hasSubtitle) {
            subtitle += ', '
          }
          subtitle += this.constants.SURFACE_LOOKUP[this.searchQuery.params.surface_type]
          hasSubtitle = true
        }

        if (this.searchQuery.params.transparency_type
          && this.constants.TRANSPARENCY_LOOKUP[this.searchQuery.params.transparency_type]) {
          if (hasSubtitle) {
            subtitle += ', '
          }
          subtitle += this.constants.TRANSPARENCY_LOOKUP[this.searchQuery.params.transparency_type]
          hasSubtitle = true
        }

        return subtitle
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
      this.searchQuery = new SearchQuery(this.$route.query)
      this.fetchitemlist()

      setTimeout(() => {
        this.handleResize()
      }, 300)
      window.addEventListener('resize', this.handleResize)

    },
    methods: {

      initialSearch () {
        // direct link into search, get search params from query string & search

        //this.fetchitemlist()
      },

      fetchitemlist () {
        // New Search, reset all errors
        this.serverError = null
        this.apiError = null

        console.log('############ FETCHITEMLIST')
        if (this.searchQuery.params.collection === 'user' && this.$auth.check()) {
          // DAU special case, collection === 'user' signifies search for own recipes..
          this.searchQuery.params.u = this.$auth.user().id
          this.searchQuery.params.collection = 0
        }

        var myQuery = this.searchQuery.getMinimalQuery()
        console.log('searchQuery:')
        console.log(this.searchQuery)
        console.log('minimal query:')
        console.log(myQuery)

        var querystring = this.searchQuery.toQuerystring(myQuery)
        this.isProcessing = true
        console.log('SEARCH: ' + querystring)

        Vue.axios.get(Vue.axios.defaults.baseURL + '/search?' + querystring)
          .then((response) => {
            console.log('############ GOT RESPONSE')
            if (response.data.error) {
              this.apiError = response.data.error
              console.log(this.apiError)
              this.itemlist = null
              this.isProcessing = false
            } else {
              this.itemlist = response.data.data

              if (!this.itemlist) {
                // Make sure itemlist is always defined, and an array
                this.itemlist = []
              }
              this.pagination = response.data.meta.pagination
              this.$router.push({path: 'search', query: myQuery})
              console.log(this.searchQuery)
              this.isProcessing = false
            }
          })
          .catch(response => {
            this.itemlist = null
            this.serverError = response
            this.isProcessing = false
          })
      },
      search (query) {
        console.log('about to call routerquery')
        console.log('searchquery')
        console.log(this.searchQuery)
        console.log('form query')
        console.log(query)

        // this.searchQuery = new SearchQuery(query.params)
        // this.searchQuery.setParams(query)
        // this.searchQuery.setFromRouterQuery(query.params)
        this.searchQuery.setFromSearchForm(query.params)
        // New search, so reset the page number
        this.searchQuery.params.p = null

        console.log('############ SEARCH')
        console.log(this.searchQuery)

        this.fetchitemlist()
      },
      pageRequest (p) {
        console.log('############ PAGE')
        this.searchQuery.params.p = p
        this.fetchitemlist()
      },
      orderRequest (order) {
        console.log('############ ORDER')
        this.searchQuery.params.order = order
        this.fetchitemlist()
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
          this.chartHeight = document.getElementById('umf-d3-chart-container').clientHeight
          this.chartWidth = document.getElementById('umf-d3-chart-container').clientWidth
        }
      },
      clickedChart (data) {
        document.getElementById('d3-tooltip-div').setAttribute('style', 'opacity: 0')
        Vue.router.push('#recipe-card-' + data.customdata)
      },
      clickedD3Chart (data) {
        this.$router.push({ path: '/search#recipe-card-' + data.id, query: this.$route.query })
      },
      highlightRecipe: function (id) {
        this.highlightedRecipeId = id
      },
      unhighlightRecipe: function (id) {
        this.unHighlightedRecipeId = id
      },
      collectRecipeSelect(id) {
        if (id) {
          this.$refs.collectModal.show();
        }
      },

      collectRecipe(id) {

      },

      copyRecipe: function (id) {
        console.log('search copy recipe : ' + id)
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
              this.fetchitemlist()
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
    top: 51px;
    bottom: 0;
    left: 0;
    z-index: 1000;
    padding: 20px 10px;
    overflow-x: hidden;
    overflow-y: auto; /* Scrollable contents if viewport is shorter than content. */
    // border-right: 1px solid #888888;
  }

  .expand-button {
    position: absolute;
    top: 84px;
    right: -4px;
    z-index: 1001;
    font-size: 1.25rem;
    line-height: 1;
//    background-color: #dedede;
//    padding-right: 0.4rem;
  }

  #umf-d3-chart-container {
    /* need fix tip bug position: relative; */
  }

  .chart-form {
    padding: 0 15px;
  }

  .search-results {
    background-color: #dedede;
    padding-bottom: 64px;
  }

  .search-pagination {
    margin-bottom: 10px;
    margin-top: 5px;
  }

  .search-buttons {
    margin-bottom: 10px;
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

  .search-subtitle {
    margin-bottom: 5px;
  }


  .d3-tip {
    font-family: 'Avenir', Helvetica, Arial, sans-serif;
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
</style>