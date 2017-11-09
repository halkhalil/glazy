<template>
  <tr class="recipe-tr">
    <td class="recipe-td">
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
    <td class="recipe-td">
        <p>adfasdf</p>
    </td>
    <td>
      <table class="umf-spark-table">
        <JsonUmfSparkSvg
                :material="recipe"
                :showOxideList="false"
                :squareSize="24"
        >
        </JsonUmfSparkSvg>




      </table>
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

      setLeach () {
        return true
      }

    }
  }
</script>

<style>

  .recipe-tr {
  }

  .recipe-td {
    border-bottom-style: hidden;
  }

  .card-recipe-detail {
    margin-bottom: 20px;
    text-align: left;
  }

  .card-recipe-detail .card-body .card-title {
    margin-top: 0;
  }

</style>