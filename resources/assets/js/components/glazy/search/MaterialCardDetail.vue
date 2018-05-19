<template>

  <div v-if="material"
       class="card material-detail-card"
       v-bind:class="materialCardClass"
       @mouseover="highlightMaterial(material.id)"
       @mouseleave="unhighlightMaterial(material.id)">
    <div class="card-body">
      <table class="card-header-table w-100">
        <tr>
          <td class="align-top">
            <span v-bind:id="'material-card-' + material.id"
                  class="material-anchor"></span>
            <h5 v-if="!material.isPrimitive"
                v-html="'&#9651;' + materialHelper.getConeString(material)">
            </h5>
            {{  materialHelper.getAtmospheresString(material) }}
          </td>
          <td class="align-top text-right">
            <router-link :to="{ name: linkName, params: { id: material.id }}"
                         class="material-card-img-link">
              <img class="rounded"
                   :src="materialHelper.getSmallImageUrl(material, material.selectedImage)"
                   :alt="material.name"
                   height="72" width="72">
            </router-link>
          </td>
        </tr>
      </table>
      <div>
        <h6 class="category text-muted" v-html="materialHelper.getMaterialTypeString(material)"></h6>
        <h5 class="card-title">
          <router-link :to="{ name: linkName, params: { id: material.id }}">
            <i v-if="material.isPrivate" class="fa fa-eye-slash"></i>
            <i v-if="material.isArchived" class="fa fa-lock"></i>
            {{ material.name }}
          </router-link>
        </h5>
      </div>

      <b-btn v-b-toggle="'detail-collapse-' + material.id"
             variant="primary"
             class="btn btn-link btn-info btn-more-info">
        <span class="when-opened"><i class="fa fa-chevron-up"></i> Less Info</span>
        <span class="when-closed"><i class="fa fa-chevron-down"></i> More Info</span>
      </b-btn>

      <b-collapse v-model="showCollapse" v-bind:id="'detail-collapse-' + material.id">

        <umf-traditional-notation
                :material="material"
                :showSimpleLegend="true"
                :isSmall="true">
        </umf-traditional-notation>

        <div class="ratios">
          R<sub>2</sub>O:RO

          <span class="badge">
            <span class="oxide-colors-Na2O">
            {{ Number(material.analysis.umfAnalysis.R2OTotal).toFixed(2) }}
            </span>
              :
              <span class="oxide-colors-CaO">
            {{ Number(material.analysis.umfAnalysis.ROTotal).toFixed(2) }}
            </span>
          </span>

          SiO<sub>2</sub>:Al<sub>2</sub>O<sub>3</sub>
          <span class="badge">
            {{ Number(material.analysis.umfAnalysis.SiO2Al2O3Ratio).toFixed(2) }}
          </span>
        </div>

        <table v-if="!material.isPrimitive && 'materialComponents' in material && material.materialComponents.length > 0"
               class="table table-sm material-component-table">
          <tr v-for="(materialComponent, index) in material.materialComponents"
              v-bind:class="{ 'table-info' : materialComponent.isAdditional }">
            <td>
              <i v-if="materialComponent.isAdditional" class="fa fa-plus"></i>
              {{ materialComponent.material.name }}
            </td>
            <td class="text-right">
              {{ Number(materialComponent.percentageAmount).toFixed(2) }}
            </td>
          </tr>
          <tr class="subtotal">
            <td></td>
            <td class="text-right">{{ Number(material.materialComponentTotalAmount).toFixed(2) }}</td>
          </tr>
        </table>

      </b-collapse>


      <router-link :to="{ name: 'user', params: { id: material.createdByUser.id}}">
        <div class="author">
          <img v-if="'profile' in material.createdByUser && 'avatar' in material.createdByUser.profile"
               v-bind:src="material.createdByUser.profile.avatar"
               class="avatar"/>
          <span>{{ material.createdByUser.name }}</span>
        </div>
      </router-link>
    </div>
    <div class="card-footer" v-if="$auth.check() && !isEmbedded">
      <b-btn v-if="isViewingSelfCollection"
             @click="uncollectMaterialRequest(material.id)"
             v-b-tooltip.hover title="Unbookmark"
             class="btn btn-icon btn-neutral"><i class="fa fa-times"></i></b-btn>
      <b-btn @click="collectMaterialRequest(material.id)"
             v-b-tooltip.hover title="Bookmark"
             class="btn btn-icon btn-neutral"><i class="fa fa-bookmark"></i></b-btn>
      <b-btn class="btn btn-icon btn-neutral"
             v-b-tooltip.hover title="Copy"
             @click="copyMaterialRequest(material.id)"><i class="fa fa-copy"></i></b-btn>
      <b-btn v-if="isCanEdit && !material.isArchived"
             v-b-tooltip.hover title="Delete"
             @click="deleteMaterialRequest(material.id)"
             class="btn btn-icon btn-neutral"><i class="fa fa-trash"></i></b-btn>
    </div>
  </div>

</template>

<script>
  import Analysis from 'ceramicscalc-js/src/analysis/Analysis'
  import GlazyHelper from '../helpers/glazy-helper'

  // import MaterialAnalysisVerticalTable from '../analysis/MaterialAnalysisVerticalTable.vue'
  import UmfTraditionalNotation from '../analysis/UmfTraditionalNotation.vue';

  export default {
    name: 'MaterialCardDetail',
    components: {
      UmfTraditionalNotation
    },
    props: {
      material: {
        type: Object,
        default: null
      },
      currentMaterialId: {
        type: Number,
        default: 0
      },
      isViewingSelf: {
        type: Boolean,
        default: false
      },
      isViewingSelfCollection: {
        type: Boolean,
        default: false
      },
      isEmbedded: {
        type: Boolean,
        default: false
      },
      showCollapse: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        materialHelper: new GlazyHelper()
      }
    },
    computed : {
      isCanEdit: function () {
        // Only the creator of a material can edit it
        if (this.$auth.check() &&
          this.$auth.user().id === this.material.createdByUserId) {
          return true
        }
        return false
      },

      linkName: function () {
        if (this.material) {
          if (this.material.isPrimitive) {
            return 'material'
          }
          if (this.material.isAnalysis) {
            return 'analysis'
          }
          return 'recipes'
        }
      },

      materialCardClass: function () {
        if (this.currentMaterialId === this.material.id) {
          return 'material-detail-card-current'
        }
        return null
      }
    },
    methods: {

      highlightMaterial: function (id) {
        this.$emit('highlightMaterial', id);
      },

      unhighlightMaterial: function (id) {
        this.$emit('unhighlightMaterial', id);
      },

      collectMaterialRequest: function (id) {
        this.$emit('collectMaterialRequest', id);
      },

      uncollectMaterialRequest: function (id) {
        this.$emit('uncollectMaterialRequest', id);
      },

      copyMaterialRequest: function (id) {
        this.$emit('copyMaterialRequest', id);
      },

      deleteMaterialRequest: function (id) {
        this.$emit('deleteMaterialRequest', id);
      }
    }
  }
</script>

<style>

  .list-group-cone li {
    background-color: #efefef;
    color: #666666;
    padding: 5px 10px;
    border-top: none;
    border-bottom: none;
  }

  .material-detail-card-current {
    /* border: 2px solid #2CA8FF; */
    border: 1px solid #2488cf;
  }

  .material-detail-card .card-body {
    padding: 10px;
  }

  .material-detail-card .card-body h5 {
    margin-bottom: 0;
  }

  .material-detail-card .card-body .category {
    margin-bottom: 0;
  }

  .material-detail-card .card-body .card-title {
    font-size: 1.15em;
    margin-top: 5px;
    margin-bottom: 5px;
  }

  .material-detail-card .card-body .ratios {
    margin-top: 4px;
    font-size: 12px;
  }

  .material-detail-card .card-body .ratios .badge {
    background-color: #efefef;
    font-size: 12px;
    padding: 4px;
    margin: 2px;
  }

  .material-detail-card .btn-more-info {
    padding: 10px 4px;
    margin: 0;
  }

  .collapsed > .when-opened,
  :not(.collapsed) > .when-closed {
    display: none;
  }

  .material-detail-card .umf-traditional {
    margin: 0;
    font-size: 12px;
    line-height: 13px;
  }
  .material-detail-card .umf-traditional thead tr th {
    font-size: 10px;
  }
  .material-detail-card .umf-traditional tbody tr td.bracket {
    padding: 0 4px;
  }

  .material-detail-card .card-body .card-header-table {
    margin: 0;
  }

  .material-detail-card .card-body .card-header-table tr td {
    padding: 0;
  }

  .material-detail-card .card-body .material-component-table {
    font-size: 12px;
    margin-top: 10px;
    margin-bottom: 10px;
  }

  .material-detail-card .card-body .material-component-table tr.subtotal {
    background-color: #efefef;
  }

  .material-detail-card .card-footer  {
    text-align: center;
    margin-top: 0;
  }

  .material-detail-card .card-footer .btn {
    color: #999999;
    margin: 0;
  }

  .material-anchor {
    display: block;
    position: relative;
    top: -240px;
    visibility: hidden;
  }

  .material-card-img-link {
    border-bottom: none !important;
  }



</style>