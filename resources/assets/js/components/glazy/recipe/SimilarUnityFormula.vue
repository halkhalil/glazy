<template>
    <div id="similar-unity-formula">
        <div class="load-container load7" v-if="isProcessing">
            <div class="loader">Searching...</div>
        </div>
        <div class="table-responsive" v-if="isLoaded && !isProcessing && materialList.length > 0">

            <table class="table table-bordered table-hover table-sm similar-unity-formula-table">

                <thead>
                <tr>
                    <th class="description" colspan="2">Recipe</th>
                    <th><small>&Delta;Temp</small></th>
                    <th><small>SiO<sub>2</sub></small>:<small>Al<sub>2</sub>O<sub>3</sub></small></th>
                    <th><small>SiO<sub>2</sub></small></th>
                    <th><small>Al<sub>2</sub>O<sub>3</sub></small></th>
                    <th><small>B<sub>2</sub>O<sub>3</sub></small></th>

                    <th><small>Li<sub>2</sub>O</small></th>
                    <th><small>Na<sub>2</sub>O</small></th>
                    <th><small>K<sub>2</sub>O</small></th>
                    <th><small>KNaO</small></th>

                    <th><small>MgO</small></th>
                    <th><small>CaO</small></th>
                    <th><small>SrO</small></th>
                    <th><small>BaO</small></th>
                </tr>
                </thead>
                <tbody>

                    <tr class="table-info">
                        <td class="description">
                            <a :href="'/recipes/' + material.id">
                                <img class="img-fluid"
                                     :alt="material.name"
                                     :src="getImageUrl(material, 's')"
                                     width="40" height="40">
                            </a>
                        </td>
                        <td class="description">
                                {{ material.name }}
                        </td>
                        <td>
                            {{ coneString(material.fromOrtonConeName, material.toOrtonConeName) }}
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
                                     :src="getImageUrl(similar, 's')"
                                     width="40" height="40">
                            </a>
                        </td>
                        <td class="description">
                            <a :href="'/recipes/' + similar.id">
                                {{ similar.name }}
                            </a>
                        </td>
                        <td v-html="coneString(similar.fromOrtonConeName, similar.toOrtonConeName)">
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
    methods: {
      fetchSimilarUnityFormula: function () {
        this.isProcessing = true
        var recipeUrl = Vue.axios.defaults.baseURL + '/search/similarUnityFormula/' + this.material.id;

        Vue.axios.get(recipeUrl)
          .then((response) => {
          this.materialList = response.data.data;
          console.log('YYYY')
          console.log(this.materialList)
          this.isProcessing = false
        })
        .catch(response => {
          // Error Handling
          this.isProcessing = false
        })

      },

      coneString: function(fromOrtonConeName, toOrtonConeName) {
        var coneString = '?';
        if (fromOrtonConeName
          && toOrtonConeName
          && fromOrtonConeName != toOrtonConeName) {
          return fromOrtonConeName + "-" + toOrtonConeName;
        }

        if (fromOrtonConeName) {
          return coneString = fromOrtonConeName;
        }

        if (toOrtonConeName) {
          coneString = toOrtonConeName;
        }
        return coneString;
      },

      getImageBin: function(id) {
        id = '' + id;
        return id.substr(id.length - 2);
      },

      hasThumbnail: function(recipe) {
        if (recipe.hasOwnProperty('thumbnail')
          && recipe.thumbnail.hasOwnProperty('filename')
          && recipe.thumbnail.filename) {
          return true;
        }
        return false;
      },

      getImageUrl: function(recipe, size) {
        if (this.hasThumbnail(recipe)) {
          var bin = this.getImageBin(recipe.id);
          return '/storage/uploads/recipes/' + bin + '/' + size + '_' + recipe.thumbnail.filename;
        }
        return '/img/recipes/black.png';
      }

    }

  }

</script>
