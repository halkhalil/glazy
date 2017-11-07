<template>
  <tr class="recipe-tr">
    <td class="recipe-td">
      <img class="rounded img-raised" width="120" height="120" src="/static/img/glazes/g4.jpg">
    </td>
    <td class="recipe-td">
      <div class="card card-recipe-detail">
        <div class="card-body">
          <h4 class="card-title">{{ recipe.name }}</h4>
          <h6 class="category text-primary">Blue Celadon</h6>
          <p class="card-description">
            {{ recipe.description }}
          </p>
        </div>
      </div>
    </td>
    <td class="recipe-td">
        <p>adfasdf</p>
    </td>
  </tr>
</template>

<script>
  import Analysis from 'ceramicscalc-js/src/analysis/Analysis'
  import PercentageAnalysis from 'ceramicscalc-js/src/analysis/PercentageAnalysis'
  import Material from 'ceramicscalc-js/src/material/Material'
  import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'

  export default {
    name: 'RecipeCardThumb',
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