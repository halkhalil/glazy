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
                <b-form-input
                        size="sm"
                        v-model="query.params.keywords"
                        type="text"
                        placeholder="Search Term"
                        @input="search"></b-form-input>
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
            <div v-bind:class="sizeMedium" v-if="subtype_options" class="form-group">
                <b-form-select
                        size="sm"
                        v-if="subtype_options"
                        id="typeId"
                        placeholder="Subtype"
                        v-model="query.params.type"
                        :options="subtype_options"
                        @input="search">
                    <template slot="first">
                        <option :value="0">All Subtypes</option>
                    </template>
                </b-form-select>
            </div>
        </div>
        <div class="form-row">
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
        <div v-if="isAdvanced" class="form-row">
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
            <div class="form-group col">
                <b-button
                        class="search-form-button btn-sm"
                        variant="secondary"
                        @click.prevent="toggleAdvanced"
                        v-html="advancedButtonText">
                </b-button>
            </div>
            <div class="form-group col">
                <b-button
                        class="search-form-button btn-sm"
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
    }
  },
  data() {
    return {
      query: new SearchQuery(),
      materialTypes: new MaterialTypes(),
      previousBaseTypeId: null,
      constants: new GlazyConstants(),
      oxides: Analysis.OXIDE_NAME_UNICODE_SELECT,
      showAdvancedText: '<i class="fa fa-plus"></i> Show Advanced',
      hideAdvancedText: '<i class="fa fa-minus"></i> Hide Advanced',
      advancedButtonText: '<i class="fa fa-plus"></i> Show Advanced',
      isAdvanced: false,
      largeSmall: 'col-md-4',
      largeMedium: 'col-md-6',
      largeLarge: 'col-md-12',
      smallSmall: 'col-md-6',
      smallMedium: 'col-md-12',
      smallLarge: 'col-md-12',
      flex: 'col',
      colors: {
        hex: '#194d33',
        hsl: {
          h: 150,
          s: 0.5,
          l: 0.2,
          a: 1
        },
        hsv: {
          h: 150,
          s: 0.66,
          v: 0.30,
          a: 1
        },
        rgba: {
          r: 25,
          g: 77,
          b: 51,
          a: 1
        },
        a: 1
      }
    }
  },

  computed: {

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
      return this.materialTypes.getParentTypes();
    },

    subtype_options: function () {
      if (this.query.params.base_type) {
        if (this.previousBaseTypeId != 0) {
          // we're switching base types.. set type to null
          this.query.params.type = 0;
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
      if (this.$auth.check()) {
        var collectionsSelect = [
          { name: 'Your Recipes', id: 'user' }
        ]
        var user = this.$auth.user()
        if (user && user.collections && user.collections.length > 0) {
          collectionsSelect = collectionsSelect.concat(user.collections)
        }
        return collectionsSelect
      }
      return null
    }
  },
  watch: {
    /*
    searchQuery: function (val) {
      this.query.setParams(this.searchQuery.params)
      if (this.query.collection && this.$auth.check()) {
        if (this.$auth.user().id === this.query.params.u) {
          this.query.params.collection = this.query.userSelfSearchString
        }
      }
    }
    */
  },
  created() {
    //this.query = new SearchQuery()
    console.log('CREATED: QUERY')
    console.log(this.query.params)
    this.query.setParams(this.searchQuery.params)
    if (this.query.collection && this.$auth.check()) {
      if (this.$auth.user().id === this.query.params.u) {
        this.query.params.collection = this.query.userSelfSearchString
      }
    }
    console.log('AFTER')
    console.log(this.query.params)
  },
  methods: {
    search: function () {
      console.log('FORM SEARCH')
      console.log(this.query.params.base_type)
      this.$emit('searchrequest', this.query.params);
    },

    searchBaseType: function () {
      this.query.params.type = 0
      this.search()
    },

    resetSearch: function () {
      this.query = new SearchQuery();
      this.$emit('searchrequest', this.query);
    },

    toggleAdvanced () {
      if (this.isAdvanced) {
        this.isAdvanced = false
        this.advancedButtonText = this.showAdvancedText
      }
      else {
        this.isAdvanced = true
        this.advancedButtonText = this.hideAdvancedText
      }
    }
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
    }

</style>