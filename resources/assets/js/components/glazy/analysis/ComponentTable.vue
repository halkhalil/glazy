<template>
    <div class="table-responsive">
        <table v-if="isLoaded" class="table table-sm table-hover material-analysis-table">
            <thead>
            <tr>
                <th>Material</th>
                <th>% Amount</th>
                <th v-for="oxideName in presentOxides">
                    <a :href="'/oxides/' + oxideName">{{ oxideName }}</a>
                </th>
                <th v-if="!isFormulaAnalysis">LOI</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(materialComponent, index) in material.materialComponents" v-bind:class="{ info : materialComponent.is_additional }">
                <td>
                    <a v-if="materialComponent.material.is_primitive" :href="'/materials/' + materialComponent.material.id">{{ materialComponent.material.name }}</a>
                    <a v-else :href="'/recipes/' + materialComponent.material.id">{{ materialComponent.material.name }}</a>
                </td>
                <td>
                    {{ parseFloat(materialComponent.getAmount()).toFixed(2) }}
                </td>
                <td v-for="oxideName in presentOxides">
                    <span v-if="materialComponent.material.analysis[analysisName].getOxide(oxideName) > 0">
                        {{ parseFloat(materialComponent.material.analysis[analysisName].getOxide(oxideName) *  materialComponent.amount / material.componentsTotalPercentage).toFixed(2) }}
                    </span>
                </td>

                <td v-if="!isFormulaAnalysis">
                    <span v-if="materialComponent.material.analysis[analysisName].loi && materialComponent.material.analysis[analysisName].loi > 0">
                        {{ parseFloat(materialComponent.material.analysis[analysisName].loi *  materialComponent.amount / material.componentsTotalPercentage).toFixed(2) }}
                    </span>
                </td>

            </tr>
            <tr class="table-secondary">
                <td>
                    Total
                </td>
                <td>
                    <span v-if="material.componentsTotalPercentage">
                        {{ parseFloat(material.componentsTotalPercentage).toFixed(2) }}
                    </span>
                </td>
                <td v-for="oxideName in presentOxides">
                    <span v-if="material.analysis[analysisName].getOxide(oxideName) > 0">
                        {{ parseFloat(material.analysis[analysisName].getOxide(oxideName)).toFixed(2) }}
                    </span>
                </td>
                <td v-if="!isFormulaAnalysis">
                    <span v-if="material.analysis[analysisName].loi && material.analysis[analysisName].loi > 0">
                        {{ parseFloat(material.analysis[analysisName].loi).toFixed(2) }}
                    </span>
                </td>
            </tr>
            <tr class="table-info" v-if="!isFormulaAnalysis">
                <td>
                    Adjusted Total (100%)
                </td>
                <td>
                </td>
                <td v-for="oxideName in presentOxides">
                    <span v-if="adjustedPercentageAnalysis.getOxide(oxideName) > 0">
                        {{ parseFloat(adjustedPercentageAnalysis.getOxide(oxideName)).toFixed(2) }}
                    </span>
                </td>
                <td></td>
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

    name: 'ComponentTable',
    props: {
      material: {
        type: Object,
        default: null
      },
      isFormulaAnalysis: {
        type: Boolean,
        default: true
      }
    },

    computed: {
      presentOxides: function () {
        if (this.isLoaded) {
          console.log('GGGGGGGGGGGGGG')
          console.log(this.material)
          return this.material.analysis[this.analysisName].getPresentOxideNamesArray()
        }
        return [];
      },

      analysisName: function () {
        if (this.isFormulaAnalysis) {
          return 'formulaAnalysis'
        }
        return 'percentageAnalysis'
      },

      adjustedPercentageAnalysis: function () {
        if (this.isLoaded) {
          return this.material.get100PercentPercentageAnalysis();
        }
        return null;
      },

      isLoaded: function () {
        if (this.material
          && this.material.hasOwnProperty('name')
        ) {
          return true;
        }
        return false;
      }

    }
  }

</script>

<style>
    .material-analysis-table tr td, .material-analysis-table tr th {
        text-align: right;
    }
</style>