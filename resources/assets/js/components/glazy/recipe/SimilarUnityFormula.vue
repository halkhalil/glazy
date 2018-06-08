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
                    <th>&Delta;Temp</th>
                    <th>SiO<sub>2</sub>:Al<sub>2</sub>O<sub>3</sub></th>
                    <th class="oxide-colors-SiO2">SiO<sub>2</sub></th>
                    <th class="oxide-colors-Al2O3">Al<sub>2</sub>O<sub>3</sub></th>
                    <th class="oxide-colors-B2O3">B<sub>2</sub>O<sub>3</sub></th>

                    <th class="oxide-colors-Li2O">Li<sub>2</sub>O</th>
                    <th class="oxide-colors-Na2O">Na<sub>2</sub>O</th>
                    <th class="oxide-colors-K2O">K<sub>2</sub>O</th>
                    <th class="oxide-colors-KNaO">KNaO</th>

                    <th class="oxide-colors-MgO">MgO</th>
                    <th class="oxide-colors-CaO">CaO</th>
                    <th class="oxide-colors-SrO">SrO</th>
                    <th class="oxide-colors-BaO">BaO</th>
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
                        <td v-html="glazyHelper.getConeString(material)">
                        </td>

                        <td>{{ (material.analysis.umfAnalysis.SiO2Al2O3Ratio) ? Number(material.analysis.umfAnalysis.SiO2Al2O3Ratio).toFixed(2) : ''  }}</td>

                        <td>{{ (material.analysis.umfAnalysis.SiO2) ? Number(material.analysis.umfAnalysis.SiO2).toFixed(2) : ''  }}</td>
                        <td>{{ (material.analysis.umfAnalysis.Al2O3) ? Number(material.analysis.umfAnalysis.Al2O3).toFixed(2) : '' }}</td>
                        <td>{{ (material.analysis.umfAnalysis.B2O3) ? Number(material.analysis.umfAnalysis.B2O3).toFixed(2) : ''  }}</td>

                        <td>{{ (material.analysis.umfAnalysis.Li2O) ? Number(material.analysis.umfAnalysis.Li2O).toFixed(2) : ''  }}</td>
                        <td>{{ (material.analysis.umfAnalysis.Na2O) ? Number(material.analysis.umfAnalysis.Na2O).toFixed(2) : ''  }}</td>
                        <td>{{ (material.analysis.umfAnalysis.K2O) ? Number(material.analysis.umfAnalysis.K2O).toFixed(2) : ''  }}</td>
                        <td>{{ (material.analysis.umfAnalysis.KNaO) ? Number(material.analysis.umfAnalysis.KNaO).toFixed(2) : ''  }}</td>

                        <td>{{ (material.analysis.umfAnalysis.MgO) ? Number(material.analysis.umfAnalysis.MgO).toFixed(2) : ''  }}</td>
                        <td>{{ (material.analysis.umfAnalysis.CaO) ? Number(material.analysis.umfAnalysis.CaO).toFixed(2) : ''  }}</td>
                        <td>{{ (material.analysis.umfAnalysis.SrO) ? Number(material.analysis.umfAnalysis.SrO).toFixed(2) : ''  }}</td>
                        <td>{{ (material.analysis.umfAnalysis.BaO) ? Number(material.analysis.umfAnalysis.BaO).toFixed(2) : ''  }}</td>
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
                        <td v-html="glazyHelper.getConeString(similar)">
                        </td>

                        <td>{{ (similar.analysis.umfAnalysis.SiO2Al2O3Ratio) ? Number(similar.analysis.umfAnalysis.SiO2Al2O3Ratio).toFixed(2) : ''  }}</td>

                        <td>{{ (similar.analysis.umfAnalysis.SiO2) ? Number(similar.analysis.umfAnalysis.SiO2).toFixed(2) : ''  }}</td>
                        <td>{{ (similar.analysis.umfAnalysis.Al2O3) ? Number(similar.analysis.umfAnalysis.Al2O3).toFixed(2) : '' }}</td>
                        <td>{{ (similar.analysis.umfAnalysis.B2O3) ? Number(similar.analysis.umfAnalysis.B2O3).toFixed(2) : ''  }}</td>

                        <td>{{ (similar.analysis.umfAnalysis.Li2O) ? Number(similar.analysis.umfAnalysis.Li2O).toFixed(2) : ''  }}</td>
                        <td>{{ (similar.analysis.umfAnalysis.Na2O) ? Number(similar.analysis.umfAnalysis.Na2O).toFixed(2) : ''  }}</td>
                        <td>{{ (similar.analysis.umfAnalysis.K2O) ? Number(similar.analysis.umfAnalysis.K2O).toFixed(2) : ''  }}</td>
                        <td>{{ (similar.analysis.umfAnalysis.KNaO) ? Number(similar.analysis.umfAnalysis.KNaO).toFixed(2) : ''  }}</td>

                        <td>{{ (similar.analysis.umfAnalysis.MgO) ? Number(similar.analysis.umfAnalysis.MgO).toFixed(2) : ''  }}</td>
                        <td>{{ (similar.analysis.umfAnalysis.CaO) ? Number(similar.analysis.umfAnalysis.CaO).toFixed(2) : ''  }}</td>
                        <td>{{ (similar.analysis.umfAnalysis.SrO) ? Number(similar.analysis.umfAnalysis.SrO).toFixed(2) : ''  }}</td>
                        <td>{{ (similar.analysis.umfAnalysis.BaO) ? Number(similar.analysis.umfAnalysis.BaO).toFixed(2) : ''  }}</td>

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
        glazyHelper: new GlazyHelper(),
        isProcessing: false
      }
    },

    computed: {
      isLoaded: function () {
        if (this.material) {
          return true;
        }
        return false;
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
        var recipeUrl = Vue.axios.defaults.baseURL + '/search/similarUnityFormula/' + this.material.id;

        Vue.axios.get(recipeUrl)
          .then((response) => {
          this.materialList = response.data.data;
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