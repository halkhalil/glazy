<template>
    <form class="search-form">
        <div class="form-row">
            <div v-if="collectionsSelect && collectionsSelect.length > 0"
                 v-bind:class="sizeLarge" class="form-group">
                <b-form-select
                        size="sm"
                        v-model="query.params.collection"
                        :options="collectionsSelect"
                        value-field="id"
                        text-field="name"
                        @input="search">
                    <template slot="first">
                        <option :value="0">All Collections</option>
                    </template>
                </b-form-select>
            </div>
            <div v-bind:class="sizeLarge" class="form-group">
                <input type="text"
                       class="form-control form-control-sm"
                       v-model="query.params.keywords"
                       placeholder="Search Term"
                       @input="updateKeywords">
            </div>
            <div v-bind:class="sizeMedium" class="form-group">
                <b-form-select
                        size="sm"
                        id="baseTypeId"
                        placeholder="Type"
                        v-model="query.params.base_type"
                        :options="baseTypeOptions"
                        @input="searchBaseType">
                    <template slot="first">
                        <option :value="0">All Types</option>
                    </template>
                </b-form-select>
            </div>
            <div v-bind:class="sizeMedium" v-if="subTypeOptions" class="form-group">
                <b-form-select
                        size="sm"
                        v-if="subTypeOptions"
                        id="typeId"
                        placeholder="Subtype"
                        v-model="query.params.type"
                        :options="subTypeOptions"
                        @input="search">
                    <template slot="first">
                        <option :value="0">All Subtypes</option>
                    </template>
                </b-form-select>
            </div>
        </div>
        <div v-if="$route.name !== 'materials'" class="form-row">
            <div v-bind:class="sizeMedium" class="form-group">
                <b-form-select
                        size="sm"
                        id="coneId"
                        placeholder="Temp"
                        v-model="query.params.cone"
                        :options="constants.ORTON_CONES_SELECT_TEXT"
                        @input="search">
                    <template slot="first">
                        <option :value="0">All Temps</option>
                    </template>
                </b-form-select>
            </div>
            <div v-bind:class="sizeMedium" class="form-group">
                <b-form-select
                        size="sm"
                        id="atmosphereId"
                        placeholder="Atmosphere"
                        v-model="query.params.atmosphere"
                        :options="constants.ATMOSPHERE_SELECT"
                        @input="search">
                    <template slot="first">
                        <option :value="0">All Atmospheres</option>
                    </template>
                </b-form-select>
            </div>
        </div>
        <div v-if="$route.name !== 'materials' && isAdvanced" class="form-row">
            <div v-bind:class="sizeMedium" class="form-group">
                <b-form-select
                        size="sm"
                        id="surfaceId"
                        placeholder="Surface"
                        v-model="query.params.surface"
                        :options="constants.SURFACE_SELECT"
                        @input="search">
                    <template slot="first">
                        <option :value="0">All Surfaces</option>
                    </template>
                </b-form-select>
            </div>
            <div v-bind:class="sizeMedium" class="form-group">
                <b-form-select
                        size="sm"
                        id="transparencyId"
                        placeholder="Transparency"
                        v-model="query.params.transparency"
                        :options="constants.TRANSPARENCY_SELECT"
                        @input="search">
                    <template slot="first">
                        <option :value="0">All Transparencies</option>
                    </template>
                </b-form-select>
            </div>
        </div>
        <div v-if="isAdvanced" class="form-row">
            <div class="form-group col">
                <b-form-select
                        size="sm"
                        id="yId"
                        placeholder="Y Oxide"
                        v-model="query.params.y"
                        :options="oxides"
                        @input="search">
                </b-form-select>
            </div>
            <div class="form-group col">
                <b-form-select
                        size="sm"
                        id="xId"
                        placeholder="X Oxide"
                        v-model="query.params.x"
                        :options="oxides"
                        @input="search">
                </b-form-select>
            </div>
        </div>
        <div class="form-row">
            <div v-if="$route.name !== 'materials'"
                 v-bind:class="sizeMedium"
                 class="form-group">
                <button v-if="!this.query.params.hex_color"
                        @click.prevent="openColor"
                        class="btn btn-default search-form-button"
                        type="button">
                    <i class="fa fa-eyedropper"></i> Color
                </button>
                <chrome-picker v-if="this.query.params.hex_color"
                               :value="this.query.params.color"
                               @input="updateColorValue"></chrome-picker>
            </div>
            <div v-bind:class="sizeMedium" class="form-group">
                <b-button
                        class="search-form-button"
                        variant="secondary"
                        @click.prevent="toggleAdvanced"
                        v-html="advancedButtonText">
                </b-button>
                <b-button
                        class="search-form-button"
                        type="reset"
                        variant="secondary"
                        @click.prevent="resetSearch">
                    <i class="fa fa-refresh"></i> Reset
                </b-button>
            </div>
        </div>
    </form>
</template>

<script>
import SearchQuery from './search-query'
import Analysis from 'ceramicscalc-js/src/analysis/Analysis'
import MaterialTypes from 'ceramicscalc-js/src/material/MaterialTypes'
import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'
import { Chrome } from 'vue-color'

import _ from 'lodash'

// import { Chrome } from 'vue-color'

export default {
  name: 'SearchForm',
  components: {
    'chrome-picker': Chrome
  },
  props: {
      isLarge: {
        type: Boolean,
        default: false
      },
    searchQuery: {
      type: Object,
      default: null
    },
    searchUser: {
        type: Object,
      default: null
    }
  },
  data() {
    return {
      // query: new SearchQuery(),
      materialTypes: new MaterialTypes(),
      previousBaseTypeId: null,
      constants: new GlazyConstants(),
      oxides: Analysis.OXIDE_NAME_UNICODE_SELECT,
      minSearchTextLength: 3,
      showAdvancedText: '<i class="fa fa-plus"></i> Advanced',
      hideAdvancedText: '<i class="fa fa-minus"></i> Hide Advanced',
      advancedButtonText: '<i class="fa fa-plus"></i> Advanced',
      isAdvanced: false,
      largeSmall: 'col-md-4',
      largeMedium: 'col-md-6',
      largeLarge: 'col-md-12',
      smallSmall: 'col-md-6',
      smallMedium: 'col-md-12',
      smallLarge: 'col-md-12',
      flex: 'col'
    }
  },
  computed: {

    query: function () {
      var query = new SearchQuery()
      if (this.searchQuery) {
        // var myParams = this.searchQuery.params
        // query.setParams(myParams)
        console.log('SEARCH FORM UPDAATE SEARCH QUERY:')
        console.log(this.searchQuery)
        query.setParams(this.searchQuery.params)
        /*
        if (query.params.collection && this.$auth.check()) {
          if (this.$auth.user().id === query.params.u) {
            query.params.collection = query.userSelfSearchString
          }
        }
        */
      }

      return query
    },
    sizeSmall: function () {
      if (this.isLarge) {
        return this.largeSmall
      }
      return this.smallSmall
    },

    sizeMedium: function () {
      if (this.isLarge) {
        return this.largeMedium
      }
      return this.smallMedium
    },

    sizeLarge: function () {
      if (this.isLarge) {
        return this.largeLarge
      }
      return this.smallLarge
    },

    baseTypeOptions: function () {
      if (this.$route.name === 'materials') {
        return this.materialTypes.PRIMITIVE_SELECT;
      }
      return this.materialTypes.COMPOSITE_PARENT_SELECT;
    },

    subTypeOptions: function () {
      if (this.query.params.base_type) {
        if (this.previousBaseTypeId != 0) {
          // we're switching base types.. set type to null
          // TODO: BUG
          // this.query.params.type = 0;
        }
        this.previousBaseTypeId = this.query.params.base_type;
        switch (this.query.params.base_type) {
          case this.materialTypes.GLAZE_TYPE_ID:
            return this.materialTypes.getGlazeTypes();
          case this.materialTypes.CLAYS_TYPE_ID:
            return this.materialTypes.getClayTypes();
          case this.materialTypes.SLIPS_TYPE_ID:
            return this.materialTypes.getSlipTypes();
        }
      }
      return null;
    },

    collectionsSelect () {
      // TODO: ensure only user-viewable collections are returned
      if (this.searchUser && this.searchUser.collections &&
        this.searchUser.collections.length > 0) {
        return this.searchUser.collections
      }
      return null
    }

  },
  created() {

  },
  methods: {
    search: function () {
      console.log('FORM SEARCH')
      console.log(this.query.params.base_type)
      this.$emit('searchrequest', this.query.params);
    },

    updateKeywords: _.debounce(function (e) {
      if (this.query.params.keywords.length >= this.minSearchTextLength) {
        this.query.params.keywords = e.target.value
        this.search()
      } else if (!e.target.value && this.$route.query && 'keywords' in this.$route.query) {
        // There was a keyword search, but now there is not
        // So we still need to search
        this.query.params.keywords = ''
        this.search()
      }
    }, 1000),
    searchBaseType: function () {
      this.query.params.type = 0
      this.search()
    },
    resetSearch: function () {
      // reset search, but keep old base type
      var oldBaseTypeId = this.query.params.base_type
      var newQuery = new SearchQuery()
      newQuery.params.base_type = oldBaseTypeId
      this.$emit('searchrequest', newQuery);
    },
    toggleAdvanced: function () {
      if (this.isAdvanced) {
        this.isAdvanced = false
        this.advancedButtonText = this.showAdvancedText
      }
      else {
        this.isAdvanced = true
        this.advancedButtonText = this.hideAdvancedText
      }
    },

    openColor: function () {
      if (!this.query.params.hex_color) {
        this.query.params.hex_color = '1199CC'
        this.query.params.color = { hex: '#1199CC' }
      }
    },

    updateColorValue: _.debounce(function (value) {
      if (value.hex) {
        this.query.params.hex_color = value.hex.substring(1)
        this.search()
      }
    }, 1500)
  }

}

</script>

<style>

    .search-form {
        margin-bottom: 10px;
    }

    .search-form .form-group {
        margin-top: 0.25rem;
        margin-bottom: 0;
    }

    .search-form-button {
        margin-top: 0px;
        margin-bottom: 0px;
        padding: 11px 16px;
    }


</style>