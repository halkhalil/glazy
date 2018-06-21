<template>
    <div id="similar-base-components">
        <div class="load-container load7" v-if="isProcessing">
            <div class="loader">Searching...</div>
        </div>
        <div class="table-responsive" v-if="isLoaded && !isProcessing && materialList && materialList.length > 0">
            <table class="table table-hover table-sm similar-base-components-table">
                <thead>
                <tr>
                    <th colspan="2">Recipe</th>
                    <th>△ Temp</th>
                    <th>Additional Ingredients</th>
                </tr>
                </thead>
                <tbody>
                <tr class="clickable-row" v-for="similar in materialList" @click="materialLink(similar)">
                    <td>
                        <img class="img-fluid"
                                :alt="similar.name"
                                :src="glazyHelper.getSmallThumbnailUrl(similar)"
                                width="72" height="72">
                    </td>
                    <td>
                        <span  class="clickable-item">
                            {{ similar.name }}
                        </span>
                    </td>
                    <td v-html="coneString(similar.fromOrtonConeName, similar.toOrtonConeName)">
                    </td>
                    <td v-html="getAdditionalComponentsString(similar)">
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <h5 v-if="!isProcessing">No similar base recipes found.</h5>
        </div>
    </div>
</template>

<script>
import Vue from "vue";
import GlazyHelper from "../helpers/glazy-helper";

export default {
  name: "SimilarBaseComponents",
  props: ["material"],

  data() {
    return {
      materialList: null,
      glazyHelper: new GlazyHelper(),
      isLoaded: false,
      isProcessing: false
    };
  },

  computed: {},

  watch: {
    material: function(val) {
      this.fetchSimilarBaseComponents();
    }
  },

  mounted() {
    this.fetchSimilarBaseComponents();
  },

  methods: {
    fetchSimilarBaseComponents: function() {
      this.isProcessing = true;
      Vue.axios
        .get(
          Vue.axios.defaults.baseURL +
            "/search/similarBaseComponents/" +
            this.material.id
        )
        .then(response => {
          this.materialList = response.data.data;
          this.isLoaded = true;
          this.isProcessing = false;
        })
        .catch(response => {
          // Error Handling
          this.isProcessing = false;
        });
    },

    coneString: function(fromOrtonConeName, toOrtonConeName) {
      var coneString = "?";
      if (
        fromOrtonConeName &&
        toOrtonConeName &&
        fromOrtonConeName != toOrtonConeName
      ) {
        return fromOrtonConeName + "-" + toOrtonConeName;
      }

      if (fromOrtonConeName) {
        return (coneString = fromOrtonConeName);
      }

      if (toOrtonConeName) {
        coneString = toOrtonConeName;
      }
      return coneString;
    },

    getAdditionalComponentsString: function(similar) {
      var tmp = "";

      if (similar.materialComponents) {
        similar.materialComponents.forEach(component => {
          if (component.isAdditional) {
            if (tmp.length) {
              tmp += ", ";
            }
            tmp += component.material.name;
          }
        });
      }
      return tmp;
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
.similar-base-components-table tr th {
  font-size: 12px;
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