<template>
    <form class="search-form">
        <div class="form-row">
            <div v-if="collectionsSelect && collectionsSelect.length > 0"
                 v-bind:class="sizeLarge" class="form-group">
                <b-form-select
                        size="sm"
                        v-model="query.collection"
                        :options="collections"
                        value-field="id"
                        text-field="name"
                        @change.native="search">
                </b-form-select>
            </div>
            <div v-bind:class="sizeLarge" class="form-group">
                <b-form-input
                        size="sm"
                        v-model="query.keywords"
                        type="text"
                        placeholder="Search Term"
                        @change.native="search"></b-form-input>
            </div>
            <div v-bind:class="sizeMedium" class="form-group">
                <multiselect
                        :options="base_type_options"
                        v-model="formParams.base_type"
                        @input="searchBaseType"
                        placeholder="Type"
                        :allow-empty="false"
                        key="value"
                        label="text"
                        track-by="value"
                ></multiselect>
            </div>
            <div v-bind:class="sizeMedium" v-if="subtype_options" class="form-group">
                <multiselect
                        v-if="subtype_options"
                        :options="subtype_options"
                        v-model="formParams.type"
                        @input="search"
                        placeholder="Subtype"
                        key="value"
                        label="text"
                        track-by="value"
                ></multiselect>
            </div>
        </div>
        <div class="form-row">
            <div v-bind:class="sizeMedium" class="form-group">
                <multiselect
                        :options="constants.ORTON_CONES_SELECT_TEXT"
                        v-model="formParams.cone"
                        @input="search"
                        placeholder="Temp"
                        key="value"
                        label="text"
                        track-by="value"
                ></multiselect>
            </div>
            <div v-bind:class="sizeMedium" class="form-group">
                <multiselect
                        :options="constants.ATMOSPHERE_SELECT"
                        v-model="formParams.atmosphere"
                        @input="search"
                        placeholder="Atmosphere"
                        key="value"
                        label="text"
                        track-by="value"
                ></multiselect>
            </div>
        </div>
        <div v-if="isAdvanced" class="form-row">
            <div v-bind:class="sizeMedium" class="form-group">
                <multiselect
                        :options="constants.SURFACE_SELECT"
                        v-model="formParams.surface"
                        @input="search"
                        placeholder="Surface"
                        key="value"
                        label="text"
                        track-by="value"
                ></multiselect>
            </div>
            <div v-bind:class="sizeMedium" class="form-group">
                <multiselect
                        :options="constants.TRANSPARENCY_SELECT"
                        v-model="formParams.transparency"
                        @input="search"
                        placeholder="Transparency"
                        key="value"
                        label="text"
                        track-by="value"
                ></multiselect>
            </div>
        </div>
        <div v-if="isAdvanced" class="form-row">
            <div class="form-group col">
                <b-checkbox v-model="query.isThreeAxes">3 Axis</b-checkbox>
            </div>
            <div class="form-group col">
                <multiselect
                        :options="oxides"
                        v-model="formParams.oxide1"
                        @input="search"
                        placeholder="Oxide"
                        key="value"
                        label="text"
                        track-by="value"
                ></multiselect>
            </div>
            <div class="form-group col">
                <multiselect
                        :options="oxides"
                        v-model="formParams.oxide2"
                        @input="search"
                        placeholder="Oxide"
                        key="value"
                        label="text"
                        track-by="value"
                ></multiselect>
            </div>
            <div class="form-group col">
                <multiselect
                        :options="oxides"
                        v-model="formParams.oxide3"
                        @input="search"
                        placeholder="Oxide"
                        key="value"
                        label="text"
                        track-by="value"
                ></multiselect>
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
  import Multiselect from 'vue-multiselect'

import SearchQuery from './search-query'
import Analysis from 'ceramicscalc-js/src/analysis/Analysis'
import MaterialTypes from 'ceramicscalc-js/src/material/MaterialTypes'
import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'
//import MaterialTypes from '../../../ceramicscalc/material/material-types';

import { Chrome } from 'vue-color'

export default {
  name: 'SearchForm',

  components: {
    'chrome-picker': Chrome,
    Multiselect
  },

  props: {
      isLarge: {
        type: Boolean,
        default: false
      },
    query: {
      type: Object,
      default: null
    },
    collections: {
      type: Object,
      default: null
    }
  },

  data() {
    return {
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

    base_type_options: function () {
      return this.materialTypes.getParentTypes();
    },

    calc_base_type: function () {
      return this.query.params.base_type;
    },

    subtype_options: function () {
      if (this.calc_base_type) {
        if (this.previousBaseTypeId != 0) {
          // we're switching base types.. set type to null
          this.query.type = null;
        }
        this.previousBaseTypeId = this.calc_base_type;
        switch (this.calc_base_type) {
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
          { name: 'All Recipes', id: 0 },
          { name: 'Your Recipes', id: -1 }
        ]
        if (this.collections) {
          collectionsSelect = collectionsSelect.concat(this.collections)
        }
        return collectionsSelect
      }
      return null
    },

    formParams() {
      var params = {}
      params.keywords = this.query.params.keywords

      params.collection = {}
      if (this.collections && this.query.params.collection in this.collections) {
        params.collection = {
          id: this.query.params.collection,
          name: this.collections[this.query.params.collection]
        }
      }
      params.base_type = {}
      var x = this.base_type_options.find(item => item.value === this.query.params.base_type);
      if (x && x.text) {
        params.base_type = { value: x.value, text: x.text }
      }
      params.type = {}
      if (this.subtype_options) {
        var x = this.subtype_options.find(item => item.value === this.query.params.type);
        if (x && x.text) {
          params.type = { value: x.value, text: x.text }
        }
      }
      params.cone = {}
      var x = this.constants.ORTON_CONES_SELECT_TEXT.find(item => item.value === this.query.params.cone);
      if (x && x.text) {
        params.cone = { value: x.value, text: x.text }
      }
      params.atmosphere = {}
      var x = this.constants.ATMOSPHERE_SELECT.find(item => item.value === this.query.params.atmosphere);
      if (x && x.text) {
        params.atmosphere = { value: x.value, text: x.text }
      }
      params.surface = {}
      var x = this.constants.SURFACE_SELECT.find(item => item.value === this.query.params.surface);
      if (x && x.text) {
        params.surface = { value: x.value, text: x.text }
      }
      params.transparency = {}
      var x = this.constants.TRANSPARENCY_SELECT.find(item => item.value === this.query.params.transparency);
      if (x && x.text) {
        params.transparency = { value: x.value, text: x.text }
      }
      params.oxide1 = {}
      var x = this.oxides.find(item => item.value === this.query.params.oxide1);
      if (x && x.text) {
        params.oxide1 = { value: x.value, text: x.text }
      }
      params.oxide2 = {}
      var x = this.oxides.find(item => item.value === this.query.params.oxide2);
      if (x && x.text) {
        params.oxide2 = { value: x.value, text: x.text }
      }
      params.oxide3 = {}
      var x = this.oxides.find(item => item.value === this.query.params.oxide3);
      if (x && x.text) {
        params.oxide3 = { value: x.value, text: x.text }
      }
      params.isThreeAxes = this.query.params.isThreeAxes

      return params
    }

  },

  mounted() {
  },

  methods: {
    search: function () {
      console.log('FORM SEARCH')
      console.log(this.query.base_type)
      if (this.query.base_type) {
        this.query.base_type = this.query.base_type.value
      }
      if (this.query.type) {
        this.query.type = this.query.type.value
      }
      var x = Object.assign({}, this.query)
      this.$emit('searchrequest', this.getFormParams());
    },

    searchBaseType: function () {
      this.query.type = null
      this.search()
    },

//        resetFields: function()
    resetSearch: function () {
//            var emptyQuery = new SearchQuery();

      this.query = new SearchQuery();

//            this.query = emptyQuery.search_params;
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
    },

    getFormParams() {
      var params = {}
      params.keywords = this.formParams.keywords
      if (this.formParams.collection && this.formParams.collection.value) {
        params.collection = this.formParams.collection.value
      }
      if (this.formParams.base_type && this.formParams.base_type.value) {
        params.base_type = this.formParams.base_type.value
      }
      if (this.formParams.type && this.formParams.type.value) {
        params.type = this.formParams.type.value
      }
      if (this.formParams.cone && this.formParams.cone.value) {
        params.cone = this.formParams.cone.value
      }
      if (this.formParams.atmosphere && this.formParams.atmosphere.value) {
        params.atmosphere = this.formParams.atmosphere.value
      }
      if (this.formParams.surface && this.formParams.surface.value) {
        params.surface = this.formParams.surface.value
      }
      if (this.formParams.transparency && this.formParams.transparency.value) {
        params.transparency = this.formParams.transparency.value
      }
      if (this.formParams.oxide1 && this.formParams.oxide1.value) {
        params.oxide1 = this.formParams.oxide1.value
      }
      if (this.formParams.oxide2 && this.formParams.oxide2.value) {
        params.oxide2 = this.formParams.oxide2.value
      }
      if (this.formParams.oxide3 && this.formParams.oxide3.value) {
        params.oxide3 = this.formParams.oxide3.value
      }
      params.isThreeAxes = this.formParams.isThreeAxes
      console.log('search form sending params:')
      console.log(params)
      return params
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