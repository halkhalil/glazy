<template>
  <tr v-if="materialHelper"
      class="material-tr"
      @mouseover="highlightMaterial(material.id)"
      @mouseleave="unhighlightMaterial(material.id)">
    <td class="material-td">
      <span v-bind:id="'material-card-' + material.id"
        class="material-anchor"></span>
      <router-link :to="{ name: 'recipes', params: { id: material.id }}">
        <img :src="materialHelper.getImageUrl()"
             :alt="material.name"
             width="72" height="72" >
      </router-link>
    </td>
    <td class="material-td">
      <router-link :to="{ name: 'recipes', params: { id: material.id }}">
        {{ material.name }}
      </router-link>
      <h6 class="category text-primary" v-html="materialHelper.getMaterialTypeString()"></h6>
      <JsonUmfSparkSvg
              :material="material"
              :showOxideList="false"
              :squareSize="24"
              :showOxideTitle="true"
      >
      </JsonUmfSparkSvg>
    </td>

    <td nowrap class="material-td">
      <span v-if="'SiO2' in material.analysis.umfAnalysis"
            v-html="Number(material.analysis.umfAnalysis.SiO2).toFixed(2)"></span>
    </td>
    <td nowrap class="material-td">
      <span v-if="'Al2O3' in material.analysis.umfAnalysis"
            v-html="Number(material.analysis.umfAnalysis.Al2O3).toFixed(2)"></span>
    </td>
    <td nowrap class="material-td">
      <span v-if="'B2O3' in material.analysis.umfAnalysis"
            v-html="Number(material.analysis.umfAnalysis.B2O3).toFixed(2)"></span>
    </td>
    <td nowrap class="material-td">
      <span v-if="'R2OTotal' in material.analysis.umfAnalysis"
            v-html="Number(material.analysis.umfAnalysis.R2OTotal).toFixed(2)"></span>
    </td>
    <td nowrap class="material-td">
      <span v-if="'ROTotal' in material.analysis.umfAnalysis"
            v-html="Number(material.analysis.umfAnalysis.ROTotal).toFixed(2)"></span>
    </td>
    <td nowrap class="material-td">
      <span class="badge badge-default"
            v-html="materialHelper.getR2ORORatioString()">
      </span>
    </td>
    <td class="material-td">
      <span class="badge badge-info"
            v-html="Number(material.analysis.umfAnalysis.SiO2Al2O3Ratio).toFixed(2)">
      </span>
    </td>
  </tr>
</template>

<script>
  import Analysis from 'ceramicscalc-js/src/analysis/Analysis'
  import PercentageAnalysis from 'ceramicscalc-js/src/analysis/PercentageAnalysis'
  import Material from 'ceramicscalc-js/src/material/Material'
  import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'

  import JsonUmfSparkSvg from '../analysis/JsonUmfSparkSvg.vue'
  import MaterialHelper from './material-helper'

  export default {
    name: 'MaterialCardRow',
    components: {
      JsonUmfSparkSvg
    },
    props: {
      material: {
        type: Object,
        default: null
      },
      isViewingSelf: {
        type: Boolean,
        default: false
      },
      isViewingSelfCollection: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        oxides: new GlazyConstants().OXIDE_NAME_UNICODE_SELECT
      }
    },
    computed : {
      materialHelper: function () {
        var materialHelper = null
        if (this.material) {
          materialHelper = new MaterialHelper(this.material)
        }
        return materialHelper
      }
    },
    methods: {

      highlightMaterial: function (id) {
        this.$emit('highlightMaterial', id);
      },

      unhighlightMaterial: function (id) {
        this.$emit('unhighlightMaterial', id);
      }

    }
  }
</script>

<style>

  .material-tr {
  }

  .material-td {
    background-color: #ffffff;
  }

  .card-material-detail {
    margin-bottom: 20px;
    text-align: left;
  }

  .card-material-detail .card-body .card-title {
    margin-top: 0;
  }

  .material-anchor {
    display: block;
    position: relative;
    top: -78px;
    visibility: hidden;
  }

</style>