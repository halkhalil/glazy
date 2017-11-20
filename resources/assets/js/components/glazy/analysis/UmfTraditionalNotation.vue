<template>
    <div class="table-responsive">
        <table class="umf-traditional" v-if="isLoaded">
            <thead v-if="showLegend || showSimpleLegend">
            <tr class="legend-row">
                <th colspan="2">
                    <span v-if="!showSimpleLegend" class="subtitle">FLUXES</span>
                </th>
                <th colspan="2">
                    <span v-if="!showSimpleLegend" class="subtitle">STABILIZERS</span>
                </th>
                <th colspan="2">
                    <span v-if="!showSimpleLegend" class="subtitle">GLASS-FORMERS</span>
                </th>
                <th v-if="other.length" colspan="2">
                    <span v-if="!showSimpleLegend" class="subtitle">OTHER</span>
                </th>
            </tr>
            <tr>
                <th colspan="2">
                    RO/R<sub>2</sub>O
                </th>
                <th colspan="2">
                    R<sub>2</sub>O<sub>3</sub>
                </th>
                <th colspan="2">
                    RO<sub>2</sub>
                </th>
                <th v-if="other.length" colspan="2">
                    <span v-if="showSimpleLegend">OTHER</span>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <div v-for="oxide in fluxes" v-html="oxide"></div>
                </td>
                <td class="bracket">
                    <img v-bind:src="'/img/brackets/r' + fluxes.length + '.png'"
                         v-bind:height="fluxes.length * heightMultiplier"
                         v-bind:width="bracketWidth"/>
                </td>
                <td>
                    <div v-for="oxide in r2o3" v-html="oxide"></div>
                </td>
                <td class="bracket">
                    <img v-bind:src="'/img/brackets/l' + sio2.length + '.png'"
                         v-bind:height="sio2.length * heightMultiplier"
                         v-bind:width="bracketWidth"/>
                </td>
                <td>
                    <div v-for="oxide in sio2" v-html="oxide"></div>
                </td>
                <td class="bracket" v-if="other.length">
                    <img v-bind:src="'/img/brackets/l' + other.length + '.png'"
                         v-bind:height="other.length * heightMultiplier"
                         v-bind:width="bracketWidth"/>
                </td>
                <td v-if="other.length">
                    <div v-for="oxide in other" v-html="oxide"></div>
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
      material: {
        type: Object,
        default: null
      },
      showLegend: {
        type: Boolean,
        default: true
      },
      showSimpleLegend: {
        type: Boolean,
        default: false
      },
      isSmall: {
        type: Boolean,
        default: false
      }
    },

    data() {
      return {
        RO_R2O_OXIDES: Analysis.RO_R2O_OXIDES,
        OXIDE_NAME_DISPLAY: Analysis.OXIDE_NAME_DISPLAY
      }
    },

    computed: {

      umf: function () {
        return this.material.analysis.umfAnalysis
        // return this.material.getROR2OUnityFormulaAnalysis();
      },

      fluxes: function () {
        return this.getOxideArray(Analysis.RO_R2O_OXIDES)
      },

      r2o3: function () {
        return this.getOxideArray(Analysis.R2O3_OXIDES)
      },

      sio2: function () {
        return this.getOxideArray(Analysis.RO2_OXIDES)
      },

      other: function () {
        return this.getOxideArray(Analysis.OTHER_OXIDES)
      },

      isLoaded: function () {
        if (this.material) {
          return true;
        }
        return false;
      },

      heightMultiplier: function () {
        if (this.isSmall) {
          return 13
        }
        return 26
      },

      bracketWidth: function () {
        if (this.isSmall) {
          return 6
        }
        return 13
      }
    },

    methods : {

      getOxideArray: function (searchOxides) {
        var oxides = []
        searchOxides.forEach((oxideName) => {
          if (this.umf[oxideName] && Number(this.umf[oxideName]).toFixed(2) > 0) {
            var str = '<span class="oxide-colors-' + oxideName + '">'
            str += Number(this.umf[oxideName]).toFixed(2) + ' '
            str += Analysis.OXIDE_NAME_DISPLAY[oxideName]
            str += '</span>'
            oxides.push(str)
          }
        })
        return oxides
      }
    }

}

</script>

<style>
    /* Traditional UMF Notation Styles */
    .umf-traditional {
        font-size: 18px;
        line-height: 26px;
    }
    .umf-traditional img {
        max-width: none;
    }
    .umf-traditional thead tr th {
        font-size: 14px;
        text-transform: none;
        line-height: 20px;
    }
    .umf-traditional thead tr th .subtitle {
        font-size: 10px;
        text-transform: none;
    }
    .umf-traditional tbody tr td {
        padding: 0;
    }
    .umf-traditional tbody tr td.bracket {
        padding: 0 10px;
    }
    .umf-traditional td {
        white-space: nowrap!important;
    }
</style>