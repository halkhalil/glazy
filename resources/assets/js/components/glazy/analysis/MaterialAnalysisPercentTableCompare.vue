<template>
    <div v-if="isLoaded">
        <table class="table table-sm analysis-percent-table">
            <thead>
            <tr>
                <th v-if="hasNewAnalysis"></th>
                <th v-for="i in presentOxides.length"
                    v-html="OXIDE_NAME_DISPLAY[presentOxides[i-1]]">
                </th>
                <th v-if="!isPercentMol">LOI</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th v-if="hasNewAnalysis">Old</th>
                <td v-for="i in presentOxides.length">
                        <span v-if="originalAnalysis.getOxide(presentOxides[i-1]) > 0">
                            {{ Number(originalAnalysis.getOxide(presentOxides[i-1])).toFixed(2) }}
                        </span>
                </td>
                <td v-if="!isPercentMol">
                    {{ Number(originalAnalysis.getLOI()).toFixed(2) }}
                </td>
            </tr>
            <tr v-if="hasNewAnalysis">
                <th>New</th>
                <td v-for="i in presentOxides.length">
                        <span v-if="newAnalysis.getOxide(presentOxides[i-1]) > 0">
                            <strong>
                               {{ Number(newAnalysis.getOxide(presentOxides[i-1])).toFixed(2) }}
                            </strong>
                        </span>
                </td>
                <td v-if="!isPercentMol">
                    {{ Number(newAnalysis.getLOI()).toFixed(2) }}
                </td>
            </tr>
            <tr class="diff-analysis" v-if="hasNewAnalysis">
                <th>Diff</th>
                <td v-for="i in presentOxides.length">
                        <span v-if="Math.abs(newAnalysis.getOxide(presentOxides[i-1]) - originalAnalysis.getOxide(presentOxides[i-1])) >= 0.005">
                            <em>
                                {{ Number(newAnalysis.getOxide(presentOxides[i-1]) - originalAnalysis.getOxide(presentOxides[i-1])).toFixed(2) }}
                            </em>
                        </span>
                </td>
                <td v-if="!isPercentMol">
                    <em>{{ (Number(newAnalysis.getLOI()) - Number(originalAnalysis.getLOI())).toFixed(2) }}</em>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>


<script>

  import Analysis from 'ceramicscalc-js/src/analysis/Analysis'

  export default {
    props: {
      originalMaterial: {
        type: Object,
        default: null
      },
      newMaterial: {
        type: Object,
        default: null
      },
      isPercentMol: {
        type: Boolean,
        default: false
      },
      precision: {
        type: Number,
        default: 2
      }
    },
    data() {
      return {
        isMounted: false,
        OXIDE_NAME_DISPLAY: Analysis.OXIDE_NAME_DISPLAY
      }
    },

    computed: {
      originalAnalysis: function () {
        if (this.originalMaterial) {
          if (this.isPercentMol) {
            return this.originalMaterial.getMolePercentageFormula();
          }
          return this.originalMaterial.getPercentageAnalysis();
        }
      },

      newAnalysis: function () {
        if (this.newMaterial) {
          if (this.isPercentMol) {
            return this.newMaterial.getMolePercentageFormula();
          }
          return this.newMaterial.getPercentageAnalysis();
        }
      },

      presentOxides: function () {
        var present = [];
        if (this.isLoaded) {
          if (this.hasNewAnalysis) {
            for (var i = 0; i < Analysis.OXIDE_NAMES.length; i++) {
              if (this.round(this.originalAnalysis.getOxide(Analysis.OXIDE_NAMES[i]), this.precision) > 0 ||
                  this.round(this.newAnalysis.getOxide(Analysis.OXIDE_NAMES[i]), this.precision) > 0) {
                present.push(Analysis.OXIDE_NAMES[i]);
              }
            }
          }
          else {
            for (var i = 0; i < Analysis.OXIDE_NAMES.length; i++) {
              if (this.round(this.originalAnalysis.getOxide(Analysis.OXIDE_NAMES[i]), this.precision) > 0) {
                present.push(Analysis.OXIDE_NAMES[i]);
              }
            }
          }
        }
        return present;
      },

      isLoaded: function () {
        if (this.isMounted
          && this.originalAnalysis
        ) {
          return true;
        }
        return false;
      },

      hasNewAnalysis: function () {
        if (this.newAnalysis) {
          return true;
        }
        return false;
      }
    },

    mounted() {
      this.isMounted = true;
    },

    methods: {

      round: function (number, precision) {
        var factor = Math.pow(10, precision);
        var tempNumber = number * factor;
        var roundedTempNumber = Math.round(tempNumber);
        return roundedTempNumber / factor;
      }

    }
  }

</script>

<style>
    .analysis-percent-table {
        margin-top: 0;
        width: 100%;
    }

    .analysis-percent-table tr th {
        opacity: 0.5;
    }

    .diff-analysis {
        opacity: 0.5;
    }

</style>
