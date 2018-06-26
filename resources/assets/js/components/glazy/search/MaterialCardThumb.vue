<template>
  <div v-if="material"
       class="card material-card"
       @mouseover="highlightMaterial(material.id)"
       @mouseleave="unhighlightMaterial(material.id)">
    <router-link :to="{ name: linkName, params: { id: material.id }}"
      class="material-card-img-link">
      <progressive-img
              class="card-img-top img-fluid w-100"
              :src="glazyHelper.getSmallImageUrl(material, material.selectedImage)"
              :placeholder="glazyHelper.getPreImageUrl(material, material.selectedImage)"
              :alt="material.name"
              :aspect-ratio="1"
      />
    </router-link>
    <span v-bind:id="'material-card-' + material.id"
          class="material-anchor"></span>
    <div class="swatches" v-if="material.selectedImage">
      <a v-if="material.selectedImage.dominantHexColor"
         role="button" class="btn"
         :href="'/search?hex_color=' + material.selectedImage.dominantHexColor"
         :style="'background-color: #' + material.selectedImage.dominantHexColor">
      </a>
      <a v-if="material.selectedImage.secondaryHexColor"
         role="button" class="btn"
         :href="'/search?hex_color=' + material.selectedImage.secondaryHexColor"
         :style="'background-color: #' + material.selectedImage.secondaryHexColor">
      </a>
    </div>
    <div class="checkimage" v-if="$auth.check()">
      <input type="checkbox" 
          v-bind:id="'material-checkbox-' + material.id"
          @change="selectMaterialRequest(material.id)"
          v-model="checkboxIsSelected">
    </div>
    <ul v-if="!material.isPrimitive" class="list-group list-group-flush list-group-cone">
      <li class="list-group-item"
          v-html="'&#9651;' + glazyHelper.getConeString(material) + ' ' + glazyHelper.getAtmospheresString(material)">
      </li>
    </ul>
    <div class="card-body">
      <h6 class="category text-muted" v-html="glazyHelper.getMaterialTypeString(material)"></h6>
      <h5 class="card-title">
        <router-link :to="{ name: linkName, params: { id: material.id }}">
          <i v-if="material.isPrivate" class="fa fa-eye-slash"></i>
          <i v-if="material.isArchived" class="fa fa-lock"></i>
          {{ material.name }}
        </router-link>
      </h5>
      <star-rating v-if="material.ratingTotal"
                   class="recipe-vue-star-rating"
                   :rating="Number(material.ratingAverage)"
                   :read-only="true"
                   :star-size="18"
                   :show-rating="false"
                   :increment="0.01"></star-rating>
      <p v-if="material.description" class="card-text">{{ material.description }}</p>
      <router-link :to="{ name: 'user', params: { id: glazyHelper.getUserProfileUrlId(material.createdByUser) }}">
        <div class="author">
          <img v-bind:src="glazyHelper.getUserAvatar(material.createdByUser)"
               class="avatar"/>
          <span>{{ glazyHelper.getUserDisplayName(material.createdByUser) }}</span>
        </div>
      </router-link>
    </div>
    <div class="card-footer" v-if="$auth.check()">
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

  import GlazyHelper from '../helpers/glazy-helper'
  import StarRating from 'vue-star-rating'

  export default {
    name: 'MaterialCardThumb',
    components: {
      StarRating
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
      },
      isSelected: {
        type: Boolean,
        default: false
      }
  },
    data() {
      return {
        glazyHelper: new GlazyHelper(),
        checkboxIsSelected: false
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
      }

    },
    watch: {
      isSelected: function(val) {
        this.checkboxIsSelected = this.isSelected
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
      },

      selectMaterialRequest: function (id) {
        this.$emit('selectMaterialRequest', id);
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
    padding: 10px 10px 0 10px;
  }

  .material-card .card-body .card-title {
    margin-top: 5px;
    margin-bottom: 5px;
  }

  .material-card .card-body .author {
    margin-bottom: 10px;
  }

  .material-card .swatches {
    position: absolute;
    top: 5px;
    right: 5px;
    padding: 0;
    margin: 0;
    z-index: 2;
  }

  .material-card .checkimage {
    position: absolute;
    top: 0;
    left: 5px;
    padding: 0;
    margin: 0;
    z-index: 2;
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