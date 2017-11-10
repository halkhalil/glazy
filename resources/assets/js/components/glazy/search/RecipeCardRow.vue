<template>
  <tr class="recipe-tr"
      @mouseover="highlightRecipe(recipe.id)"
      @mouseleave="unhighlightRecipe(recipe.id)">
    <td class="recipe-td">
      <span v-bind:id="'recipe-card-' + recipe.id"
        class="recipe-anchor"></span>
      <router-link :to="{ name: 'recipes', params: { id: recipe.id }}">
        <img :src="imageUrl"
             :alt="recipe.name"
             width="120" height="120" >
      </router-link>
    </td>
    <td class="recipe-td">
      <router-link :to="{ name: 'recipes', params: { id: recipe.id }}">
        {{ recipe.name }}
      </router-link>
    </td>
    <td nowrap class="recipe-td">
      <span class="badge badge-default"
            v-html="getR2ORORatioString(recipe)">
      </span>
    </td>
    <td class="recipe-td">
      <span class="badge badge-info"
            v-html="Number(recipe.analysis.umfAnalysis.SiO2Al2O3Ratio).toFixed(2)">
      </span>
    </td>
    <td>
      <JsonUmfSparkSvg
              :material="recipe"
              :showOxideList="false"
              :squareSize="24"
              :showOxideTitle="true"
      >
      </JsonUmfSparkSvg>
    </td>
  </tr>
</template>

<script>
  import Analysis from 'ceramicscalc-js/src/analysis/Analysis'
  import PercentageAnalysis from 'ceramicscalc-js/src/analysis/PercentageAnalysis'
  import Material from 'ceramicscalc-js/src/material/Material'
  import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'

  import JsonUmfSparkSvg from '../analysis/JsonUmfSparkSvg.vue'

  export default {
    name: 'RecipeCardThumb',
    components: {
      JsonUmfSparkSvg
    },
    props: {
      recipe: {
        type: Object,
        default: null
      }
    },
    data() {
      return {
        oxides: new GlazyConstants().OXIDE_NAME_UNICODE_SELECT
      };
    },
    computed : {
      isLoaded: function () {
        return true;
      }
    },
    methods: {
      getR2ORORatioString (recipe) {
        if (recipe.analysis &&
          recipe.analysis.umfAnalysis &&
          recipe.analysis.umfAnalysis.R2OTotal &&
          recipe.analysis.umfAnalysis.ROTotal) {
          return (Number(recipe.analysis.umfAnalysis.R2OTotal).toFixed(1) + '').substr(1)
            + ' : ' +
            (Number(recipe.analysis.umfAnalysis.ROTotal).toFixed(1) + '').substr(1)
        }
        return ''
      },

      highlightRecipe: function (id) {
        this.$emit('highlightRecipe', id);
      },

      unhighlightRecipe: function (id) {
        this.$emit('unhighlightRecipe', id);
      }


    }
  }
</script>

<style>

  .recipe-tr {
  }

  .recipe-td {
  }

  .card-recipe-detail {
    margin-bottom: 20px;
    text-align: left;
  }

  .card-recipe-detail .card-body .card-title {
    margin-top: 0;
  }

  .recipe-anchor {
    display: block;
    position: relative;
    top: -78px;
    visibility: hidden;
  }

</style>