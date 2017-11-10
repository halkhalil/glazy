<template>
  <div
          class="card recipe-card"
          @mouseover="highlightRecipe(recipe.id)"
          @mouseleave="unhighlightRecipe(recipe.id)">
    <router-link :to="{ name: 'recipes', params: { id: recipe.id }}">
      <img class="card-img-top img-fluid w-100"
           :src="imageUrl"
           :alt="recipe.name">
    </router-link>
    <span v-bind:id="'recipe-card-' + recipe.id"
          class="recipe-anchor"></span>
    <div class="swatches" v-if="recipe.thumbnail">
      <a v-if="recipe.thumbnail.dominantHexColor"
         role="button" class="btn"
         :href="'/search?hex_color=' + recipe.thumbnail.dominantHexColor"
         :style="'background-color: #' + recipe.thumbnail.dominantHexColor">
      </a>
      <a v-if="recipe.thumbnail.secondaryHexColor"
         role="button" class="btn"
         :href="'/search?hex_color=' + recipe.thumbnail.secondaryHexColor"
         :style="'background-color: #' + recipe.thumbnail.secondaryHexColor">
      </a>
    </div>

    <ul class="list-group list-group-flush list-group-cone">
      <li class="list-group-item" v-html="'&#9651;' + coneString + ' ' + atmospheresString"></li>
    </ul>
    <div class="card-body">
      <h6 class="category text-primary">{{ materialTypes.LOOKUP[recipe.materialTypeId] }}</h6>
      <h5 class="card-title">
        <router-link :to="{ name: 'recipes', params: { id: recipe.id }}">
          {{ recipe.name }}
        </router-link>
      </h5>
      <p class="card-text">{{ recipe.description }}</p>
      <div class="card-footer">
        <div class="author">
          <img src="/static/img/profile.jpg" alt="..." class="avatar img-raised">
          <span>{{ recipe.createdByUser.name }}</span>
        </div>
      </div>
    </div>

    <div class="card-footer" v-if="$auth.check()">

      <a @click="collectRecipeRequest(recipe.id)" class="btn btn-icon btn-neutral"><i class="fa fa-bookmark"></i></a>
      <a class="btn btn-icon btn-neutral"
         @click="copyRecipeRequest(recipe.id)"><i class="fa fa-copy"></i></a>
      <a v-if="canEdit()"
         @click="deleteRecipeRequest(recipe.id)"
         class="btn btn-icon btn-neutral"><i class="fa fa-trash"></i></a>
    </div>

  </div>

</template>

<script>

  import Analysis from 'ceramicscalc-js/src/analysis/Analysis'
  import PercentageAnalysis from 'ceramicscalc-js/src/analysis/PercentageAnalysis'
  import Material from 'ceramicscalc-js/src/material/Material'
  import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'
  import MaterialTypes from 'ceramicscalc-js/src/material/MaterialTypes'

  export default {
    name: 'RecipeCardThumb',
    components: {
    },
    props: {
      recipe: {
        type: Object,
        default: null
      }
    },
    data() {
      return {
        STORAGE_BASE_URL: 'http://homestead.app',
        constants: new GlazyConstants(),
        oxides: new GlazyConstants().OXIDE_NAME_UNICODE_SELECT,
        materialTypes: new MaterialTypes()
      };
    },
    computed : {
      isLoaded: function () {
        if (this.recipe) {
          return true;
        }
        return false;
      },

      imageUrl: function() {
        if (this.recipe.thumbnail && this.recipe.thumbnail.filename) {
          var bin = this.getImageBin(this.recipe.id);
          return this.STORAGE_BASE_URL +
            '/storage/uploads/recipes/' +
            bin + '/s_' + this.recipe.thumbnail.filename;
        }
        return '/static/img/recipes/black.png';
      },

      coneString: function() {
        // TODO: Move to util class
        var coneString = '?'
        if (this.recipe.fromOrtonConeId
          && this.recipe.toOrtonConeId
          && this.recipe.fromOrtonConeId != this.recipe.toOrtonConeId) {
          return this.constants.ORTON_CONES_LOOKUP[this.recipe.fromOrtonConeId] +
            "-" + this.constants.ORTON_CONES_LOOKUP[this.recipe.toOrtonConeId]
        }

        if (this.recipe.fromOrtonConeId) {
          return coneString = this.constants.ORTON_CONES_LOOKUP[this.recipe.fromOrtonConeId]
        }

        if (this.recipe.toOrtonConeId) {
          coneString = this.constants.ORTON_CONES_LOOKUP[this.recipe.toOrtonConeId]
        }
        return coneString
      },

      atmospheresString: function() {
        var str = '';
        if (this.recipe.atmospheres) {
          this.recipe.atmospheres.forEach((atmosphere) => {
            if (str.length) {
              str += ', '
            }
            str += this.constants.ATMOSPHERE_SHORT_LOOKUP[atmosphere.id]
          })
        }
        return str
      }


    },
    methods: {

      hasThumbnail: function(recipe) {
        if (recipe.hasOwnProperty('thumbnail')
          && recipe.thumbnail.hasOwnProperty('url')
          && recipe.thumbnail.url) {
          return true;
        }
        return false;
      },

      getImageBin: function(id) {
        id = '' + id;
        return id.substr(id.length - 2);
      },

      highlightRecipe: function (id) {
        this.$emit('highlightRecipe', id);
      },

      unhighlightRecipe: function (id) {
        this.$emit('unhighlightRecipe', id);
      },

      collectRecipeRequest: function (id) {
        this.$emit('collectRecipeRequest', id);
      },

      copyRecipeRequest: function (id) {
        this.$emit('copyRecipeRequest', id);
      },

      deleteRecipeRequest: function (id) {
        this.$emit('deleteRecipeRequest', id);
      },

      canEdit: function () {
        // Only the creator of a recipe can edit it
        if (this.$auth.check() &&
          this.$auth.user().id === this.recipe.createdByUserId) {
          return true
        }
        return false
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

  .recipe-card .swatches {
    position: absolute;
    top: 5px;
    right: 5px;
    padding: 0;
    margin: 0;
  }

  .recipe-card .swatches .btn {
    min-width: 24px;
    width: 24px;
    height: 24px;
    line-height: 24px;
    overflow: hidden;
    border-radius: 24px !important;
    padding: 0;
    margin: 0;
  }

  .recipe-card .card-footer  {
    text-align: center;
    margin-top: 0;
  }

  .recipe-card .card-footer .btn {
    color: #999999;
    margin: 5px 0;
  }

  .recipe-anchor {
    display: block;
    position: relative;
    top: -240px;
    visibility: hidden;
  }


</style>