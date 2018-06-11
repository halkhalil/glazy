<template>
    <div class="table-responsive">
        <table v-if="isLoaded" class="table table-sm table-striped table-hover material-analysis-table">
            <thead>
            <tr>
                <th>Type</th>
                <th v-for="oxideName in presentOxides">
                    <span v-html="OXIDE_NAME_DISPLAY[oxideName]"
                        v-bind:class="'oxide-colors-' + oxideName"></span>
                </th>
                <th>LOI</th>
            </tr>
            </thead>
            <tbody>

            <tr v-if="'percentageAnalysis' in material.analysis">
                <td>
                    Percentage Analysis
                </td>
                <td v-for="oxideName in presentOxides">
                    <span v-if="material.analysis.percentageAnalysis.getOxide(oxideName) > 0">
                        {{ parseFloat(material.analysis.percentageAnalysis.getOxide(oxideName)).toFixed(3) }}
                    </span>
                </td>
                <td>
                    <span v-if="material.analysis.percentageAnalysis.loi && material.analysis.percentageAnalysis.loi > 0">
                        {{ parseFloat(material.analysis.percentageAnalysis.loi).toFixed(3) }}
                    </span>
                </td>
            </tr>

           <tr v-if="hundredPercent">
                <td>
                    100% Percentage Analysis
                </td>
                <td v-for="oxideName in presentOxides">
                    <span v-if="hundredPercent.getOxide(oxideName) > 0">
                        {{ parseFloat(hundredPercent.getOxide(oxideName)).toFixed(3) }}
                    </span>
                </td>
                <td>
                </td>
            </tr>

            <tr v-if="'formulaAnalysis' in material.analysis">
                <td>
                    Formula
                </td>
                <td v-for="oxideName in presentOxides">
                    <span v-if="material.analysis.formulaAnalysis.getOxide(oxideName) > 0">
                        {{ parseFloat(material.analysis.formulaAnalysis.getOxide(oxideName)).toFixed(3) }}
                    </span>
                </td>
                <td>
                </td>
            </tr>

           <tr v-if="molePercent">
                <td>
                    Mole Percent
                </td>
                <td v-for="oxideName in presentOxides">
                    <span v-if="molePercent.getOxide(oxideName) > 0">
                        {{ parseFloat(molePercent.getOxide(oxideName)).toFixed(3) }}
                    </span>
                </td>
                <td>
                </td>
            </tr>

            </tbody>
        </table>
    </div>
</template>


<script>

  import Analysis from 'ceramicscalc-js/src/analysis/Analysis'
  import PercentageAnalysis from 'ceramicscalc-js/src/analysis/PercentageAnalysis'
  import Material from 'ceramicscalc-js/src/material/Material'

  export default {

    name: 'SimpleAnalysisTable',
    props: {
      material: {
        type: Object,
        default: null
      }
    },
    data() {
      return {
        OXIDE_NAME_DISPLAY: Analysis.OXIDE_NAME_DISPLAY
      }
    },
    computed: {
      presentOxides: function () {
        if (this.isLoaded) {
          if ('percentageAnalysis' in this.material.analysis) {
            var presentOxides = this.material.analysis.percentageAnalysis.getPresentOxideNamesArray()
            if (presentOxides && presentOxides.length) {
              return presentOxides
            }
          }
          if ('formulaAnalysis' in this.material.analysis) {
            var presentOxides = this.material.analysis.formulaAnalysis.getPresentOxideNamesArray()
            if (presentOxides && presentOxides.length) {
              return presentOxides
            }
          }
        }
        return [];
      },
      molePercent: function () {
          if (this.isLoaded) {
            return this.material.getMolePercentageFormula()
          }
          return null
      },
      hundredPercent: function () {
          if (this.isLoaded) {
            return this.material.get100PercentPercentageAnalysis()
          }
          return null
      },
      isLoaded: function () {
        if (this.material
          && this.material.hasOwnProperty('analysis')
        ) {
          return true;
        }
        return false;
      }
    }
  }

</script>

<style>
    .material-analysis-table tr th {
        font-size: 12px;
    }
    .material-analysis-table tr td, .material-analysis-table tr th {
        text-align: right;
    }
    .material-analysis-table tr td.amount {
        font-weight: bold;
    }
</style>