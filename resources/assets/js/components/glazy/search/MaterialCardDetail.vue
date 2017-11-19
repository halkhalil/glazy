<template>

  <div v-if="materialHelper"
       class="card material-detail-card"
       @mouseover="highlightMaterial(material.id)"
       @mouseleave="unhighlightMaterial(material.id)">
    <div class="card-body">
      <table class="w-100">
        <tr>
          <td class="align-top">
            <span v-bind:id="'material-card-' + material.id"
                  class="material-anchor"></span>
            <h5 v-html="'&#9651;' + materialHelper.getConeString()">
            </h5>
            {{  materialHelper.getAtmospheresString() }}
          </td>
          <td class="align-top text-right">
            <router-link :to="{ name: 'recipes', params: { id: material.id }}"
                         class="material-card-img-link">
              <img class="rounded"
                   :src="materialHelper.getImageUrl()"
                   :alt="material.name"
                   height="72" width="72">
            </router-link>
          </td>
        </tr>
      </table>
      <div>
        <h6 class="category text-primary" v-html="materialHelper.getMaterialTypeString()"></h6>
        <h5 class="card-title">
          <router-link :to="{ name: 'recipes', params: { id: material.id }}">
            <i v-if="material.isPrivate" class="fa fa-eye-slash"></i>
            <i v-if="material.isArchived" class="fa fa-lock"></i>
            {{ material.name }}
          </router-link>
        </h5>
      </div>
      <table v-if="!material.isPrimitive && 'materialComponents' in material && material.materialComponents.length > 0"
             class="table table-sm">
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
      <router-link :to="{ name: 'user', params: { id: material.createdByUser.id}}">
        <div class="author">
          <img src="/img/profile.jpg" alt="..." class="avatar img-raised">
          <span>{{ material.createdByUser.name }}</span>
        </div>
      </router-link>
    </div>
    <div class="card-footer" v-if="$auth.check()">
      <a @click="collectMaterialRequest(material.id)" class="btn btn-icon btn-neutral"><i class="fa fa-bookmark"></i></a>
      <a class="btn btn-icon btn-neutral"
         @click="copyMaterialRequest(material.id)"><i class="fa fa-copy"></i></a>
      <a v-if="isCanEdit && !material.isArchived"
         @click="deleteMaterialRequest(material.id)"
         class="btn btn-icon btn-neutral"><i class="fa fa-trash"></i></a>
    </div>
  </div>

</template>

<script>

  import MaterialHelper from './material-helper'

  export default {
    name: 'MaterialCardThumb',
    components: {
    },
    props: {
      material: {
        type: Object,
        default: null
      }
    },
    data() {
      return {
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
      },

      collectMaterialRequest: function (id) {
        this.$emit('collectMaterialRequest', id);
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

  .material-detail-card .card-body table {
    font-size: 12px;
    margin-bottom: 5px;
  }

  .material-detail-card .card-body table tr.subtotal {
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


</style>