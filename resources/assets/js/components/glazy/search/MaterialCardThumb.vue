<template>
  <div v-if="materialHelper"
       class="card material-card"
       @mouseover="highlightMaterial(material.id)"
       @mouseleave="unhighlightMaterial(material.id)">
    <router-link :to="{ name: 'recipes', params: { id: material.id }}"
      class="material-card-img-link">
      <img class="card-img-top img-fluid w-100"
           :src="materialHelper.getImageUrl()"
           :alt="material.name">
    </router-link>
    <span v-bind:id="'material-card-' + material.id"
          class="material-anchor"></span>
    <div class="swatches" v-if="material.thumbnail">
      <a v-if="material.thumbnail.dominantHexColor"
         role="button" class="btn"
         :href="'/search?hex_color=' + material.thumbnail.dominantHexColor"
         :style="'background-color: #' + material.thumbnail.dominantHexColor">
      </a>
      <a v-if="material.thumbnail.secondaryHexColor"
         role="button" class="btn"
         :href="'/search?hex_color=' + material.thumbnail.secondaryHexColor"
         :style="'background-color: #' + material.thumbnail.secondaryHexColor">
      </a>
    </div>

    <ul v-if="!material.isPrimitive" class="list-group list-group-flush list-group-cone">
      <li class="list-group-item"
          v-html="'&#9651;' + materialHelper.getConeString() + ' ' + materialHelper.getAtmospheresString()">
      </li>
    </ul>
    <div class="card-body">
      <h6 class="category text-primary" v-html="materialHelper.getMaterialTypeString()"></h6>
      <h5 class="card-title">
        <router-link :to="{ name: 'recipes', params: { id: material.id }}">
          <i v-if="material.isPrivate" class="fa fa-eye-slash"></i>
          <i v-if="material.isArchived" class="fa fa-lock"></i>
          {{ material.name }}
        </router-link>
      </h5>
      <p class="card-text">{{ material.description }}</p>
      <router-link :to="{ name: 'user', params: { id: material.createdByUser.id}}">
        <div class="author">
          <img src="/img/profile.jpg" alt="..." class="avatar">
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

  .material-card .card-body {
    padding: 10px;
  }

  .material-card .swatches {
    position: absolute;
    top: 5px;
    right: 5px;
    padding: 0;
    margin: 0;
  }

  .material-card .material-card-img-link {
    border-bottom: none !important;
  }

  .material-card .swatches .btn {
    min-width: 24px;
    width: 24px;
    height: 24px;
    line-height: 24px;
    overflow: hidden;
    border-radius: 24px !important;
    padding: 0;
    margin: 0;
  }

  .material-card .card-footer  {
    text-align: center;
    margin-top: 0;
  }

  .material-card .card-footer .btn {
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