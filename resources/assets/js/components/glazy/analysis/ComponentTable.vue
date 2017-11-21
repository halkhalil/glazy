<template>
    <div class="table-responsive">
        <table v-if="isLoaded" class="table table-sm table-hover material-analysis-table">
            <thead>
            <tr>
                <th>Material</th>
                <th>% Amount</th>
                <th v-for="oxideName in presentOxides">
                    <a :href="'/oxides/' + oxideName" v-html="OXIDE_NAME_DISPLAY[oxideName]"></a>
                </th>
                <th v-if="!isFormula">LOI</th>
            </tr>

            </thead>
            <tbody>
            <tr v-for="materialComponent in componentContributions" v-bind:class="{ info : materialComponent.isAdditional }">
                <td>
                    <a v-if="materialComponent.isPrimitive" :href="'/materials/' + materialComponent.id">{{ materialComponent.name }}</a>
                    <a v-else :href="'/recipes/' + materialComponent.id">{{ materialComponent.name }}</a>
                </td>
                <td class="amount">
                    {{ materialComponent.amount }}
                </td>
                <td v-for="oxideName in presentOxides">
                    <span v-if="materialComponent.analysis[oxideName]">
                        {{ materialComponent.analysis[oxideName] }}
                    </span>
                </td>

                <td v-if="!isFormula">
                    <span v-if="materialComponent.analysis['loi']">
                        {{ materialComponent.analysis['loi'] }}
                    </span>
                </td>

            </tr>
            <tr class="table-secondary">
                <td>
                    Total
                </td>
                <td class="amount">
                    <span v-if="material.componentsTotalPercentage">
                        {{ parseFloat(material.componentsTotalPercentage).toFixed(2) }}
                    </span>
                </td>
                <td v-for="oxideName in presentOxides">
                    <span v-if="totalAnalysis.getOxide(oxideName) > 0">
                        {{ parseFloat(totalAnalysis.getOxide(oxideName)).toFixed(2) }}
                    </span>
                </td>
                <td v-if="!isFormula">
                    <span v-if="totalAnalysis.loi && totalAnalysis.loi > 0">
                        {{ parseFloat(totalAnalysis.loi).toFixed(2) }}
                    </span>
                </td>
            </tr>
            <tr class="table-info">
                <td v-if="isFormula">
                    Mol % Formula
                </td>
                <td v-else>
                    Adjusted Total (100%)
                </td>
                <td>
                </td>
                <td v-for="oxideName in presentOxides">
                    <span v-if="adjustedPercentageAnalysis.getOxide(oxideName) > 0">
                        {{ parseFloat(adjustedPercentageAnalysis.getOxide(oxideName)).toFixed(2) }}
                    </span>
                </td>
                <td v-if="!isFormula"></td>
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
      isFormula: {
        type: Boolean,
        default: true
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
          if (this.isFormula) {
            return this.material.analysis.formulaAnalysis.getPresentOxideNamesArray()
          }
          return this.material.analysis.percentageAnalysis.getPresentOxideNamesArray()
        }
        return [];
      },

      componentContributions: function () {
        var myarray = []
        if (this.presentOxides && this.presentOxides.length &&
          this.material.materialComponents && this.material.materialComponents.length > 0) {
          this.material.materialComponents.forEach(function (materialComponent, index) {
            var materialAnalysis = null
            if (this.isFormula) {
              materialAnalysis = materialComponent.material.analysis.formulaAnalysis
            } else {
              materialAnalysis = materialComponent.material.analysis.percentageAnalysis
            }
            var materialOxides = {}
            this.presentOxides.forEach(function (oxideName) {
              if (materialAnalysis.getOxide(oxideName)) {
                materialOxides[oxideName] = parseFloat(materialAnalysis.getOxide(oxideName) *
                  materialComponent.amount / this.material.componentsTotalPercentage).toFixed(2)
              } else {
                materialOxides[oxideName] = ''
              }
            }.bind(this))
            if (!this.isFormula) {
              if (materialAnalysis.loi) {
                materialOxides['loi'] = parseFloat(materialAnalysis.loi *
                  materialComponent.amount / this.material.componentsTotalPercentage).toFixed(2)
              } else {
                materialOxides['loi'] = ''
              }
            }
            myarray.push({
              id: materialComponent.material.id,
              name: materialComponent.material.name,
              amount: parseFloat(materialComponent.amount).toFixed(2),
              isAdditional: materialComponent.isAdditional,
              isPrimitive: materialComponent.material.isPrimitive,
              analysis: materialOxides
            })
          }.bind(this))
        }
        return myarray
      },
      analysisName: function () {
        if (this.isFormula) {
          return 'formulaAnalysis'
        }
        return 'percentageAnalysis'
      },

      totalAnalysis: function () {
        if (this.isFormula) {
          return this.material.analysis.formulaAnalysis
        }
        return this.material.analysis.percentageAnalysis
      },

      adjustedPercentageAnalysis: function () {
        if (this.isLoaded) {
          if (this.isFormula) {
            return this.material.getMolePercentageFormula()
          }
          return this.material.get100PercentPercentageAnalysis()
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