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
                    <th v-if="isGlaze">
                        &Delta;Temp
                    </th>
                    <th v-for="(oxideValue, oxideName) in presentOxides"
                        v-html="formattedOxideNames[oxideName]"
                        v-bind:class="'oxide-colors-' + oxideName">
                    </th>
                </tr>
                </thead>
                <tbody>
                    <tr class="table-info">
                        <td>
                            <img class="img-fluid"
                                    :alt="material.name"
                                    :src="glazyHelper.getSmallThumbnailUrl(material)"
                                    width="40" height="40">
                        </td>
                        <td>
                            {{ material.name }}
                        </td>
                        <td v-if="isGlaze"
                            v-html="glazyHelper.getConeString(material)">
                        </td>
                        <td v-for="(oxideValue, oxideName) in presentOxides">
                            {{ oxideName in material.analysis.molPercentageAnalysis && +Number(material.analysis.molPercentageAnalysis[oxideName]).toFixed(2) > 0 ? Number(material.analysis.molPercentageAnalysis[oxideName]).toFixed(2) : '' }}
                        </td>
                    </tr>
                    <tr class="clickable-row" v-for="similar in materialList"  @click="materialLink(similar)">
                        <td>      
                            <img class="img-fluid"
                                    :alt="similar.name"
                                    :src="glazyHelper.getSmallImageUrl(similar, similar.selectedImage)"
                                    width="40" height="40">
                        </td>
                        <td>
                            <span  class="clickable-item">
                                {{ similar.name }}
                            </span>
                        </td>
                        <td v-if="isGlaze"
                            v-html="glazyHelper.getConeString(similar)">
                        </td>
                        <td v-for="(oxideValue, oxideName) in presentOxides">
                            {{ oxideName in similar.analysis.molPercentageAnalysis && +Number(similar.analysis.molPercentageAnalysis[oxideName]).toFixed(2) > 0 ? Number(similar.analysis.molPercentageAnalysis[oxideName]).toFixed(2) : '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <h5 v-if="!isProcessing">No similar analyses found.</h5>
        </div>
    </div>
</template>

<script>
import GlazyHelper from "../helpers/glazy-helper";
import Analysis from "ceramicscalc-js/src/analysis/Analysis";

export default {
  name: "SimilarUnityFormula",
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
    };
  },

  computed: {
    isLoaded: function() {
      if (this.material) {
        return true;
      }
      return false;
    },
    isGlaze: function() {
      if (this.material) {
        if (this.material.baseTypeId === 460) {
          return true;
        }
      }
      return false;
    }
  },

  mounted() {
    this.fetchSimilarUnityFormula();
  },
  watch: {
    material: function(val) {
      this.fetchSimilarUnityFormula();
    }
  },

  methods: {
    fetchSimilarUnityFormula: function() {
      this.isProcessing = true;
      var recipeUrl =
        Vue.axios.defaults.baseURL +
        "/search/similarAnalysis/" +
        this.material.id;
      Vue.axios
        .get(recipeUrl)
        .then(response => {
          this.materialList = response.data.data;

          // create a list of all oxides present in the material list:
          this.oxideNames.forEach(
            function(oxideName) {
              this.presentOxides[oxideName] = false;
            }.bind(this)
          );
          this.materialList.forEach(
            function(similarMaterial) {
              for (const oxideName in similarMaterial.analysis
                .molPercentageAnalysis) {
                if (
                  similarMaterial.analysis.molPercentageAnalysis.hasOwnProperty(
                    oxideName
                  ) &&
                  +Number(
                    similarMaterial.analysis.molPercentageAnalysis[oxideName]
                  ).toFixed(2) > 0
                ) {
                  this.presentOxides[oxideName] = true;
                }
              }
            }.bind(this)
          );

          delete this.presentOxides["loi"];
          delete this.presentOxides["SiO2Al2O3Ratio"];
          delete this.presentOxides["R2OTotal"];
          delete this.presentOxides["ROTotal"];
          this.oxideNames.forEach(
            function(oxideName) {
              if (!this.presentOxides[oxideName]) {
                delete this.presentOxides[oxideName];
              }
            }.bind(this)
          );

          this.isProcessing = false;
        })
        .catch(response => {
          // Error Handling
          this.isProcessing = false;
        });
    },

    materialLink: function(material) {
      if (material) {
        if (material.isPrimitive) {
          this.$router.push({ name: "material", params: { id: material.id } });
        } else if (material.isAnalysis) {
          this.$router.push({ name: "analysis", params: { id: material.id } });
        } else {
          this.$router.push({ name: "recipes", params: { id: material.id } });
        }
      }
    }
  }
};
</script>

<style>
.similar-unity-formula-table tr th {
  font-size: 12px;
}
.similar-unity-formula-table tr td,
.similar-unity-formula-table tr th {
  text-align: right;
}
.similar-unity-formula-table tr td.amount {
  font-weight: bold;
}
.clickable-row {
  cursor: pointer;
}
.clickable-item {
  text-decoration: none;
  border-bottom: 1px solid transparent;
  color: #2488cf;
}
.clickable-item:hover {
  border-color: #2488cf;
}
</style>