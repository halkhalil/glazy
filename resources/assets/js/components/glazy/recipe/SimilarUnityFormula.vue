<template>
    <div id="similar-unity-formula">
        <div class="load-container load7" v-if="isProcessing">
            <div class="loader">Searching...</div>
        </div>
        <div class="table-responsive" v-if="isLoaded && !isProcessing && materialList && materialList.length > 0">
            <table class="table table-bordered table-hover table-sm similar-unity-formula-table">
                <thead>
                <tr>
                    <th colspan="2">Recipe</th>
                    <th v-if="analysisType === 'umfAnalysis'">
                        &Delta;Temp
                    </th>
                    <th v-if="analysisType === 'umfAnalysis'">
                        SiO<sub>2</sub>:Al<sub>2</sub>O<sub>3</sub>
                    </th>
                    <th v-for="(oxideValue, oxideName) in presentOxides"
                        v-html="formattedOxideNames[oxideName]"
                        v-bind:class="'oxide-colors-' + oxideName">
                    </th>
                </tr>
                </thead>
                <tbody>
                    <tr class="table-info">
                        <td class="description">
                            <a :href="'/recipes/' + material.id">
                                <img class="img-fluid"
                                     :alt="material.name"
                                     :src="glazyHelper.getSmallThumbnailUrl(material)"
                                     width="40" height="40">
                            </a>
                        </td>
                        <td class="description">
                                {{ material.name }}
                        </td>
                        <td v-if="analysisType === 'umfAnalysis'"
                            v-html="glazyHelper.getConeString(material)">
                        </td>
                        <td v-if="analysisType === 'umfAnalysis'">
                            {{ Number(material.analysis.umfAnalysis.SiO2Al2O3Ratio).toFixed(2) }}
                        </td>
                        <td v-for="(oxideValue, oxideName) in presentOxides">
                            {{ oxideName in material.analysis[analysisType] && +Number(material.analysis[analysisType][oxideName]).toFixed(2) > 0 ? Number(material.analysis[analysisType][oxideName]).toFixed(2) : '' }}
                        </td>
                    </tr>
                    <tr class="" v-for="similar in materialList">
                        <td class="description">
                            <a :href="'/recipes/' + similar.id">
                                <img class="img-fluid"
                                     :alt="similar.name"
                                     :src="glazyHelper.getSmallThumbnailUrl(similar)"
                                     width="40" height="40">
                            </a>
                        </td>
                        <td class="description">
                            <a :href="'/recipes/' + similar.id">
                                {{ similar.name }}
                            </a>
                        </td>
                        <td v-if="analysisType === 'umfAnalysis'"
                            v-html="glazyHelper.getConeString(similar)">
                        </td>
                        <td v-if="analysisType === 'umfAnalysis'">
                            {{ Number(similar.analysis.umfAnalysis.SiO2Al2O3Ratio).toFixed(2) }}
                        </td>
                        <td v-for="(oxideValue, oxideName) in presentOxides">
                            {{ oxideName in similar.analysis[analysisType] && +Number(similar.analysis[analysisType][oxideName]).toFixed(2) > 0 ? Number(similar.analysis[analysisType][oxideName]).toFixed(2) : '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <h5 v-if="!isProcessing">No recipes with similar UMF formulas found.</h5>
        </div>
    </div>
</template>

<script>
  import GlazyHelper from '../helpers/glazy-helper'
  import Analysis from 'ceramicscalc-js/src/analysis/Analysis'

  export default {
    name: 'SimilarUnityFormula',
    props: {
      material: {
        type: Object,
        default: null
      }
    },

    data() {
      return {
        materialList: [],
        presentOxides: {},
        glazyHelper: new GlazyHelper(),
        oxideNames: Analysis.OXIDE_NAMES,
        formattedOxideNames: Analysis.OXIDE_NAME_DISPLAY,
        isProcessing: false
      }
    },

    computed: {
      isLoaded: function () {
        if (this.material) {
          return true;
        }
        return false;
      },
      analysisType: function () {
        if (this.material.isPrimitive || this.material.isAnalysis) {
            return 'percentageAnalysis'
        }
        return 'umfAnalysis'
      }
    },

    mounted() {
      this.fetchSimilarUnityFormula();
    },
    watch: {
      material: function (val) {
        this.fetchSimilarUnityFormula()
      }
    },

    methods: {
      fetchSimilarUnityFormula: function () {
        this.isProcessing = true
        var recipeUrl = Vue.axios.defaults.baseURL + '/search/similarAnalysis/' + this.material.id;
        Vue.axios.get(recipeUrl)
          .then((response) => {
          this.materialList = response.data.data;

          // create a list of all oxides present in the material list:
          this.oxideNames.forEach(function (oxideName) {
            this.presentOxides[oxideName] = false
          }.bind(this))
          this.materialList.forEach(function (similarMaterial) {
              for (const oxideName in similarMaterial.analysis[this.analysisType]) {
                if (similarMaterial.analysis[this.analysisType].hasOwnProperty(oxideName) && 
                    +Number(similarMaterial.analysis[this.analysisType][oxideName]).toFixed(2) > 0) {
                    this.presentOxides[oxideName] = true
                }
              }
          }.bind(this))

          delete this.presentOxides['loi']
          delete this.presentOxides['SiO2Al2O3Ratio']
          delete this.presentOxides['R2OTotal'] 
          delete this.presentOxides['ROTotal']
          this.oxideNames.forEach(function (oxideName) {
            if (!this.presentOxides[oxideName]) {
                delete this.presentOxides[oxideName]
            }
          }.bind(this))

          this.isProcessing = false
        })
        .catch(response => {
          // Error Handling
          this.isProcessing = false
        })

      }
    }

  }

</script>

<style>
    .similar-unity-formula-table tr th {
        font-size: 12px;
    }
    .similar-unity-formula-table tr td, .similar-unity-formula-table tr th {
        text-align: right;
    }
    .similar-unity-formula-table tr td.amount {
        font-weight: bold;
    }
</style>