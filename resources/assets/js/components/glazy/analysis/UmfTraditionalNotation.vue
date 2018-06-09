<template>
    <div class="table-responsive umf-traditional-container">
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
            <tr class="legend-row-2">
                <th colspan="2">
                    R<sub>2</sub>O/RO
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
                    <i v-bind:class="'fa fa-r' + fluxes.length + ' fa-' + cssClassString + fluxes.length"></i>
                </td>
                <td>
                    <div v-for="oxide in r2o3" v-html="oxide"></div>
                </td>
                <td class="bracket">
                    <i v-bind:class="'fa fa-l' + sio2.length + ' fa-' + cssClassString + sio2.length"></i>
                </td>
                <td>
                    <div v-for="oxide in sio2" v-html="oxide"></div>
                </td>
                <td class="bracket" v-if="other.length">
                    <i v-bind:class="'fa fa-l' + other.length + ' fa-' + cssClassString + other.length"></i>
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

      cssClassString: function () {
        if (this.isSmall) {
          return 'bs'
        }
        return 'b'
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
    .umf-traditional-container {
        overflow-y: hidden;
    }

    .umf-traditional {
        font-size: 16px;
        line-height: 22px;
    }
    .umf-traditional img {
        max-width: none;
    }
    .umf-traditional thead tr th {
        font-size: 14px;
        text-transform: none;
        line-height: 14px;
    }
    .umf-traditional thead tr.legend-row-2 th {
        padding-bottom: 8px;
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

    [class*=" fa-b"]:before {
        margin: 0; width: 10px;
    }
    [class*=" fa-bs"]:before {
        margin: 0; width: 6px;
    }
    /* Regular size: */
    .fa-b1 { font-size: 22px; line-height: 22px; }
    .fa-b2 { font-size: 44px; line-height: 44px; }
    .fa-b3 { font-size: 66px; line-height: 66px; }
    .fa-b4 { font-size: 88px; line-height: 88px; }
    .fa-b5 { font-size: 110px; line-height: 110px; }
    .fa-b6 { font-size: 132px; line-height: 132px; }
    .fa-b7 { font-size: 154px; line-height: 154px; }
    .fa-b8 { font-size: 176px; line-height: 176px; }
    .fa-b9 { font-size: 198px; line-height: 198px; }
    .fa-b10 { font-size: 220px; line-height: 220px; }
    .fa-b11 { font-size: 242px; line-height: 242px; }
    /* Small size: */
    .fa-bs1 { font-size: 13px; line-height: 13px; }
    .fa-bs2 { font-size: 26px; line-height: 26px; }
    .fa-bs3 { font-size: 39px; line-height: 39px; }
    .fa-bs4 { font-size: 52px; line-height: 52px; }
    .fa-bs5 { font-size: 65px; line-height: 65px; }
    .fa-bs6 { font-size: 78px; line-height: 78px; }
    .fa-bs7 { font-size: 91px; line-height: 91px; }
    .fa-bs8 { font-size: 104px; line-height: 104px; }
    .fa-bs9 { font-size: 117px; line-height: 117px; }
    .fa-bs10 { font-size: 130px; line-height: 130px; }
    .fa-bs11 { font-size: 142px; line-height: 142px; }
</style>