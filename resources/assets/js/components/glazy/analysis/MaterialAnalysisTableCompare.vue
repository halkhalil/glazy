<template>
    <table v-if="isLoaded" class="table table-sm analysis-table" v-bind:class="tableClass">
        <thead>
        <tr class="oxide-type-headings" v-if="showHeadings">
            <th v-if="this.originalAnalysis && this.newAnalysis"></th>
            <th :colspan="presentROR2OOXIDES.length">Fluxes</th>
            <th :colspan="presentR2O3OXIDES.length">Stabilizers</th>
            <th :colspan="presentRO2OXIDES.length">Glass-formers</th>
            <th :colspan="presentOTHEROXIDES.length">Other</th>
            <th colspan="2">Ratios</th>
        </tr>
        <tr>
            <th v-if="this.originalAnalysis && this.newAnalysis"></th>
            <th v-for="i in presentROR2OOXIDES.length"
                v-html="OXIDE_NAME_DISPLAY[presentROR2OOXIDES[i-1]]">
            </th>
            <th v-for="i in presentR2O3OXIDES.length"
                v-html="OXIDE_NAME_DISPLAY[presentR2O3OXIDES[i-1]]">
            </th>
            <th v-for="i in presentRO2OXIDES.length"
                v-html="OXIDE_NAME_DISPLAY[presentRO2OXIDES[i-1]]">
            </th>
            <th v-for="i in presentOTHEROXIDES.length"
                v-html="OXIDE_NAME_DISPLAY[presentOTHEROXIDES[i-1]]">
            </th>
            <th>
                R<sub>2</sub>O:RO
            </th>
            <th>
                Si:Al
            </th>
        </tr>
        </thead>
        <tbody>
        <tr class="old-analysis" v-if="this.originalAnalysis">
            <th v-if="this.originalAnalysis && this.newAnalysis">Old</th>
            <td v-for="i in presentROR2OOXIDES.length">
                <span v-if="originalAnalysis.getOxide(presentROR2OOXIDES[i-1]) > 0">
                    {{ Number(originalAnalysis.getOxide(presentROR2OOXIDES[i-1])).toFixed(precision) }}
                </span>
            </td>
            <td v-for="i in presentR2O3OXIDES.length">
                <span v-if="originalAnalysis.getOxide(presentR2O3OXIDES[i-1]) > 0">
                    {{ Number(originalAnalysis.getOxide(presentR2O3OXIDES[i-1])).toFixed(precision) }}
                </span>
            </td>
            <td v-for="i in presentRO2OXIDES.length">
                <span v-if="originalAnalysis.getOxide(presentRO2OXIDES[i-1]) > 0">
                    {{ Number(originalAnalysis.getOxide(presentRO2OXIDES[i-1])).toFixed(precision) }}
                </span>
            </td>
            <td v-for="i in presentOTHEROXIDES.length">
                <span v-if="round(originalAnalysis.getOxide(presentOTHEROXIDES[i-1]), precision) > 0">
                    {{ Number(originalAnalysis.getOxide(presentOTHEROXIDES[i-1])).toFixed(precision) }}
                </span>
            </td>
            <td>
                {{ Number(originalAnalysis.getR2OTotal()).toFixed(precision) }}
                :
                {{ Number(originalAnalysis.getROTotal()).toFixed(precision) }}
            </td>
            <td>
                {{ Number(originalAnalysis.getSiO2Al2O3Ratio()).toFixed(precision) }}
            </td>
        </tr>
        <tr class="new-analysis" v-if="this.newAnalysis">
            <th v-if="this.originalAnalysis && this.newAnalysis">New</th>
            <td v-for="i in presentROR2OOXIDES.length">
                <span v-if="newAnalysis.getOxide(presentROR2OOXIDES[i-1]) > 0">
                    <strong>{{ Number(newAnalysis.getOxide(presentROR2OOXIDES[i-1])).toFixed(precision) }}</strong>
                </span>
            </td>
            <td v-for="i in presentR2O3OXIDES.length">
                <span v-if="newAnalysis.getOxide(presentR2O3OXIDES[i-1]) > 0">
                    <strong>{{ Number(newAnalysis.getOxide(presentR2O3OXIDES[i-1])).toFixed(precision) }}</strong>
                </span>
            </td>
            <td v-for="i in presentRO2OXIDES.length">
                <span v-if="newAnalysis.getOxide(presentRO2OXIDES[i-1]) > 0">
                    <strong>{{ Number(newAnalysis.getOxide(presentRO2OXIDES[i-1])).toFixed(precision) }}</strong>
                </span>
            </td>
            <td v-for="i in presentOTHEROXIDES.length">
                <span v-if="round(newAnalysis.getOxide(presentOTHEROXIDES[i-1]), 2) > 0">
                    <strong>{{ Number(newAnalysis.getOxide(presentOTHEROXIDES[i-1])).toFixed(precision) }}</strong>
                </span>
            </td>
            <td>
                {{ Number(newAnalysis.getR2OTotal()).toFixed(precision) }}
                :
                {{ Number(newAnalysis.getROTotal()).toFixed(precision) }}
            </td>
            <td>
                {{ Number(newAnalysis.getSiO2Al2O3Ratio()).toFixed(precision) }}
            </td>
        </tr>
        <tr class="diff-analysis" v-if="this.originalAnalysis && this.newAnalysis">
            <th>Diff</th>
            <td v-for="i in presentROR2OOXIDES.length">
                <span v-if="Math.abs(newAnalysis.getOxide(presentROR2OOXIDES[i-1]) - originalAnalysis.getOxide(presentROR2OOXIDES[i-1])) > 0.004">
                    {{ Number(newAnalysis.getOxide(presentROR2OOXIDES[i-1]) - originalAnalysis.getOxide(presentROR2OOXIDES[i-1])).toFixed(precision) }}
                </span>
            </td>
            <td v-for="i in presentR2O3OXIDES.length">
                <span v-if="Math.abs(newAnalysis.getOxide(presentR2O3OXIDES[i-1]) - originalAnalysis.getOxide(presentR2O3OXIDES[i-1])) > 0.004">
                    {{ Number(newAnalysis.getOxide(presentR2O3OXIDES[i-1]) - originalAnalysis.getOxide(presentR2O3OXIDES[i-1])).toFixed(precision) }}
                </span>
            </td>
            <td v-for="i in presentRO2OXIDES.length">
                <span v-if="Math.abs(newAnalysis.getOxide(presentRO2OXIDES[i-1]) - originalAnalysis.getOxide(presentRO2OXIDES[i-1])) > 0.004">
                    {{ Number(newAnalysis.getOxide(presentRO2OXIDES[i-1]) - originalAnalysis.getOxide(presentRO2OXIDES[i-1])).toFixed(precision) }}
                </span>
            </td>
            <td v-for="i in presentOTHEROXIDES.length">
                <span v-if="Math.abs(newAnalysis.getOxide(presentOTHEROXIDES[i-1]) - originalAnalysis.getOxide(presentOTHEROXIDES[i-1])) > 0.004">
                    {{ Number(newAnalysis.getOxide(presentOTHEROXIDES[i-1]) - originalAnalysis.getOxide(presentOTHEROXIDES[i-1])).toFixed(precision) }}
                </span>
            </td>
            <td>
                <span v-if="Math.abs(newAnalysis.getR2OTotal() - originalAnalysis.getR2OTotal()) > 0.004">
                {{ Number(newAnalysis.getR2OTotal() - originalAnalysis.getR2OTotal()).toFixed(precision) }}
                :
                {{ Number(newAnalysis.getROTotal() - originalAnalysis.getROTotal()).toFixed(precision) }}
                </span>
            </td>
            <td>
                <span v-if="Math.abs(newAnalysis.getSiO2Al2O3Ratio() - originalAnalysis.getSiO2Al2O3Ratio()) > 0.004">
                {{ Number(newAnalysis.getSiO2Al2O3Ratio() - originalAnalysis.getSiO2Al2O3Ratio()).toFixed(precision) }}
                </span>
            </td>
        </tr>

        </tbody>
    </table>
</template>


<script>
  import Analysis from 'ceramicscalc-js/src/analysis/Analysis'
  import PercentageAnalysis from 'ceramicscalc-js/src/analysis/PercentageAnalysis'
  import Material from 'ceramicscalc-js/src/material/Material'

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
      tableClass: {
        type: String,
        default: null
      },
      showHeadings: {
        type: Boolean,
        default: true
      },
      precision: {
        type: Number,
        default: 2
      }

    },

    data() {
      return {
        OXIDE_NAME_DISPLAY: Analysis.OXIDE_NAME_DISPLAY
      }
    },

    computed: {

      originalAnalysis: function () {
        if (this.originalMaterial) {
          return this.originalMaterial.getROR2OUnityFormulaAnalysis();
        }
        return null
      },

      newAnalysis: function () {
        if (this.newMaterial) {
          return this.newMaterial.getROR2OUnityFormulaAnalysis();
        }
        return null
      },
      presentROR2OOXIDES: function () {
        var present = [];
        if (this.isLoaded) {
          for (var i = 0; i < Analysis.RO_R2O_OXIDES.length; i++) {
            if ((this.originalAnalysis &&
              this.round(this.originalAnalysis.getOxide(Analysis.RO_R2O_OXIDES[i]), this.precision) > 0) ||
              (this.newAnalysis &&
              this.round(this.newAnalysis.getOxide(Analysis.RO_R2O_OXIDES[i]), this.precision) > 0)) {
              present.push(Analysis.RO_R2O_OXIDES[i]);
            }
          }
        }
        return present;
      },
      presentR2O3OXIDES: function () {
        var present = [];
        if (this.isLoaded) {
          for (var i = 0; i < Analysis.R2O3_OXIDES.length; i++) {
            if ((this.originalAnalysis &&
              this.round(this.originalAnalysis.getOxide(Analysis.R2O3_OXIDES[i]), this.precision) > 0) ||
              (this.newAnalysis &&
              this.newAnalysis.getOxide(Analysis.R2O3_OXIDES[i]) > 0)) {
              present.push(Analysis.R2O3_OXIDES[i]);
            }
          }
        }
        return present;
      },
      presentRO2OXIDES: function () {
        var present = [];
        if (this.isLoaded) {
          for (var i = 0; i < Analysis.RO2_OXIDES.length; i++) {
            if ((this.originalAnalysis &&
              this.round(this.originalAnalysis.getOxide(Analysis.RO2_OXIDES[i]), this.precision) > 0) ||
              (this.newAnalysis &&
              this.round(this.newAnalysis.getOxide(Analysis.RO2_OXIDES[i]), this.precision) > 0)) {
              present.push(Analysis.RO2_OXIDES[i]);
            }
          }
        }
        return present;
      },

      presentOTHEROXIDES: function () {
        var present = [];
        if (this.isLoaded) {
          for (var i = 0; i < Analysis.OTHER_OXIDES.length; i++) {
            if ((this.originalAnalysis &&
              this.round(this.originalAnalysis.getOxide(Analysis.OTHER_OXIDES[i]), this.precision) > 0) ||
              (this.newAnalysis &&
              this.round(this.newAnalysis.getOxide(Analysis.OTHER_OXIDES[i]), this.precision) > 0)) {
              present.push(Analysis.OTHER_OXIDES[i]);
            }
          }
        }
        return present;
      },
      isLoaded: function () {
        if (this.originalAnalysis || this.newAnalysis) {
          return true;
        }
        return false;
      }
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
    .analysis-table {
        margin-top: 0;
    }

    .analysis-table tr th {
        opacity: 0.5;
    }

    .analysis-table .oxide-type-headings {
        font-size: 10px;
    }

    .diff-analysis {
        opacity: 0.5;
    }

</style>