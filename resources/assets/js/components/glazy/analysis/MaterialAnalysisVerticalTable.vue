<template>
    <table v-if="umfAnalysis" class="table table-sm analysis-table">
        <thead>
        <tr class="oxide-type-headings" v-if="showHeadings">
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <table>
                    <tr v-for="(oxideName, index) in RO_R2O_OXIDES">
                        <td v-if="oxideName in umfAnalysis">
                            {{ oxideName }}
                        </td>
                        <td v-if="oxideName in umfAnalysis"
                            v-html="Number(umfAnalysis[oxideName]).toFixed(2)">
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr v-for="(oxideName, index) in RO_R2O_OXIDES">
                        <td v-if="oxideName in umfAnalysis">
                            {{ oxideName }}
                        </td>
                        <td v-if="oxideName in umfAnalysis"
                            v-html="Number(umfAnalysis[oxideName]).toFixed(2)">
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <table>
                    <tr v-for="(oxideName, index) in RO_R2O_OXIDES">
                        <td v-if="oxideName in umfAnalysis">
                            {{ oxideName }}
                        </td>
                        <td v-if="oxideName in umfAnalysis"
                            v-html="Number(umfAnalysis[oxideName]).toFixed(2)">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</template>


<script>
  import Analysis from 'ceramicscalc-js/src/analysis/Analysis'

  export default {
    name: 'MaterialAnalysisVerticalTable',
    props: {
      material: {
        type: Object,
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
    computed: {
      umfAnalysis: function () {
        if (this.material &&
          !this.material.isPrimitive &&
          'analysis' in this.material &&
          'umfAnalysis' in this.material.analysis) {
          return this.material.analysis.umfAnalysis
        }
        return null
      }
    },
    data() {
      return {
        OXIDE_NAME_DISPLAY: Analysis.OXIDE_NAME_DISPLAY,
        RO_R2O_OXIDES: Analysis.RO_R2O_OXIDES
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