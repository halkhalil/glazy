<template>
    <form class="search-form">
        <div class="form-row">
            <div v-bind:class="sizeLarge" class="form-group">
                <b-form-input
                        size="sm"
                        v-model="query.keywords"
                        type="text"
                        placeholder="Search Term"
                        @input="search"></b-form-input>
            </div>
            <div v-bind:class="sizeMedium" class="form-group">
                <b-form-select
                        size="sm"
                        v-model="query.base_type_id"
                        :options="base_type_options"
                        @input="search">
                    <template slot="first">
                        <option :value="null">Type</option>
                    </template>
                </b-form-select>
            </div>
            <div v-bind:class="sizeMedium" v-if="subtype_options" class="form-group">
                <b-form-select
                        size="sm"
                        v-if="subtype_options"
                        v-model="query.type_id"
                        :options="subtype_options"
                        @input="search">
                    <template slot="first">
                        <option :value="null">Subtype</option>
                    </template>
                </b-form-select>
            </div>
        </div>
        <div class="form-row">
            <div v-bind:class="sizeMedium" class="form-group">
                <b-form-select
                        size="sm"
                        v-model="query.cone_id"
                        :options="constants.ORTON_CONES_SELECT"
                        @input="search">
                    <template slot="first">
                        <option :value="null">Temp</option>
                    </template>
                </b-form-select>
            </div>
            <div v-bind:class="sizeMedium" class="form-group">
                <b-form-select
                        size="sm"
                        v-model="query.atmosphere_id"
                        :options="constants.ATMOSPHERE_SELECT"
                        @input="search">
                    <template slot="first">
                        <option :value="null">Atmosphere</option>
                    </template>
                </b-form-select>
            </div>
        </div>
        <div v-if="isAdvanced" class="form-row">
            <div v-bind:class="sizeMedium" class="form-group">
                <b-form-select
                        size="sm"
                        v-model="query.surface_type_id"
                        :options="constants.SURFACE_SELECT"
                        @input="search">
                    <template slot="first">
                        <option :value="null">Surface</option>
                    </template>
                </b-form-select>
            </div>
            <div v-bind:class="sizeMedium" class="form-group">
                <b-form-select
                        size="sm"
                        v-model="query.transparency_type_id"
                        :options="constants.TRANSPARENCY_SELECT"
                        @input="search">
                    <template slot="first">
                        <option :value="null">Transparency</option>
                    </template>
                </b-form-select>
            </div>
        </div>
        <div v-if="isAdvanced" class="form-row">
            <div class="form-group col">
                <b-checkbox v-model="query.isThreeAxes">3 Axis</b-checkbox>
            </div>
            <div class="form-group col">
                <b-form-select
                        size="sm"
                        v-model="query.oxide1"
                        :options="oxides"
                        @input="search"
                        class="col">
                    <template slot="first">
                        <option :value="null">Oxide 1</option>
                    </template>
                </b-form-select>
            </div>
            <div class="form-group col">
                <b-form-select
                        size="sm"
                        v-model="query.oxide2"
                        :options="oxides"
                        @input="search"
                        class="col">
                    <template slot="first">
                        <option :value="null">Oxide 2</option>
                    </template>
                </b-form-select>
            </div>
            <div class="form-group col">
                <b-form-select
                        v-if="query.isThreeAxes"
                        size="sm"
                        v-model="query.oxide3"
                        :options="oxides"
                        @input="search"
                        class="col">
                    <template slot="first">
                        <option :value="null">Oxide 3</option>
                    </template>
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
//import MaterialTypes from '../../../ceramicscalc/material/material-types';

import { Chrome } from 'vue-color'

export default {
  name: 'SearchForm',

  props: {
      isLarge: {
        type: Boolean,
        default: false
      },
    query: {
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

  components: {
    'chrome-picker': Chrome
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

    calc_base_type_id: function () {
      return this.query.base_type_id;
    },

    subtype_options: function () {
      if (this.calc_base_type_id) {
        if (this.previousBaseTypeId != 0) {
          // we're switching base types.. set type to null
          this.query.type_id = null;
        }
        this.previousBaseTypeId = this.calc_base_type_id;
        switch (this.calc_base_type_id) {
          case this.materialTypes.GLAZE_TYPE_ID:
            return this.materialTypes.getGlazeTypes();
          case this.materialTypes.CLAYS_TYPE_ID:
            return this.materialTypes.getClayTypes();
          case this.materialTypes.SLIPS_TYPE_ID:
            return this.materialTypes.getSlipTypes();
        }
      }
      return null;
    }
  },

  mounted() {
  },

  methods: {
    search: function () {
      this.$emit('searchrequest', this.query);
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