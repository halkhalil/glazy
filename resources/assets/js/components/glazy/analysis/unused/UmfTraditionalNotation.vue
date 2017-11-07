<template>

    <table class="umf-traditional" v-if="isLoaded">
        <thead>
        <tr>
            <th colspan="2">
                <span class="subtitle">FLUXES</span>
                <br/>
                RO/R<sub>2</sub>O
            </th>
            <th colspan="2">
                <span class="subtitle">STABILIZERS</span>
                <br/>
                R<sub>2</sub>O<sub>3</sub>
            </th>
            <th colspan="2">
                <span class="subtitle">GLASS-FORMERS</span>
                <br/>
                RO<sub>2</sub>
            </th>
            <th colspan="2">
                <span class="subtitle">OTHER</span>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <div v-for="oxide in fluxes" v-html="oxide"></div>
            </td>
            <td>
                <span v-bind:class="fluxesBracketClass">}</span>
            </td>
            <td>
                <div v-for="oxide in r2o3" v-html="oxide"></div>
            </td>
            <td>
                <span v-bind:class="sio2BracketClass">{</span>
            </td>
            <td>
                <div v-for="oxide in sio2" v-html="oxide"></div>
            </td>
            <td>
                <span v-bind:class="otherBracketClass">{</span>
            </td>
            <td>
                <div v-for="oxide in other" v-html="oxide"></div>
            </td>
        </tr>
        </tbody>
    </table>

</template>


<script>
  import Analysis from 'ceramicscalc-js/src/analysis/Analysis'

  export default {

    props: {
      material: {
        type: Object,
        default: null
      },
      tableClass: {
        type: String,
        default: null
      },
      showLegend: {
        type: Boolean,
        default: false
      },
      showOxideList: {
        type: Boolean,
        default: false
      },
      squareSize: {
        type: Number,
        default: 30
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
        return this.material.getROR2OUnityFormulaAnalysis();
      },

      fluxes: function () {
        return this.getOxideArray(Analysis.RO_R2O_OXIDES)
      },

      fluxesBracketClass: function () {
        return 'umf-bracket-' + this.fluxes.length
      },

      r2o3: function () {
        return this.getOxideArray(Analysis.R2O3_OXIDES)
      },

      r2o3BracketClass: function () {
        return 'umf-bracket-' + this.r2o3.length
      },

      sio2: function () {
        return this.getOxideArray(Analysis.RO2_OXIDES)
      },

      sio2BracketClass: function () {
        return 'umf-bracket-' + this.sio2.length
      },

      other: function () {
        return this.getOxideArray(Analysis.OTHER_OXIDES)
      },

      otherBracketClass: function () {
        return 'umf-bracket-' + this.other.length
      },

      isLoaded: function () {
        if (this.material) {
          return true;
        }
        return false;
      }
    },

    methods : {

      getOxideArray: function (searchOxides) {
        var oxides = []
        searchOxides.forEach((oxideName) => {
          if (this.umf[oxideName] && Number(this.umf[oxideName]).toFixed(2) > 0) {
            oxides.push(Number(this.umf[oxideName]).toFixed(2) +
              ' ' + Analysis.OXIDE_NAME_DISPLAY[oxideName])
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
    .umf-traditional tbody tr td {
        padding-right: 16px;
    }
    .umf-traditional thead tr th {
        font-size: 14px;
        text-transform: none;
        line-height: 20px;
        padding-bottom: 10px;
    }
    .umf-traditional thead tr th .subtitle {
        font-size: 10px;
        text-transform: none;
    }
    .umf-traditional td {
        white-space: nowrap!important;
    }
    .umf-bracket-1 {
        font-weight: 400;
        font-size: 26px;
        line-height: 26px;
    }
    .umf-bracket-2 {
        font-weight: 200;
        font-size: 52px;
        line-height: 52px;
    }
    .umf-bracket-3 {
        font-weight: 100;
        font-size: 78px;
        line-height: 78px;
    }
    .umf-bracket-4 {
        font-weight: 100;
        font-size: 104px;
        line-height: 104px;
    }
    .umf-bracket-5 {
        font-weight: 100;
        font-size: 130px;
        line-height: 130px;
    }
    .umf-bracket-6 {
        font-weight: 100;
        font-size: 156px;
        line-height: 156px;
    }
    .umf-bracket-7 {
        font-weight: 100;
        font-size: 182px;
        line-height: 182px;
    }
    .umf-bracket-8 {
        font-weight: 100;
        font-size: 208px;
        line-height: 208px;
    }
    .umf-bracket-9 {
        font-weight: 100;
        font-size: 234px;
        line-height: 234px;
    }


    .oxide-colors .color-R2O    { background: #ff6666; color: #000000; }
    .oxide-colors .color-RO     { background: #cccc66; color: #000000; }
    .oxide-colors .color-SiO2   { background: #9999ff; color: #ffffff; }
    .oxide-colors .color-Al2O3  { background: #66ff66; color: #000000; }
    .oxide-colors .color-B2O3   { background: #339933; color: #ffffff; }
    .oxide-colors .color-Li2O   { background: #663333; color: #000000; }
    .oxide-colors .color-Na2O   { background: #ff6666; color: #000000; }
    .oxide-colors .color-K2O    { background: #99ffff; color: #000000; }
    .oxide-colors .color-BeO    { background: #ff3300; color: #000000; }
    .oxide-colors .color-MgO    { background: #ff9966; color: #000000; }
    .oxide-colors .color-CaO    { background: #ffff66; color: #000000; }
    .oxide-colors .color-SrO    { background: #ff6600; color: #000000; }
    .oxide-colors .color-BaO    { background: #ff6633; color: #000000; }
    .oxide-colors .color-P2O5   { background: #666699; color: #000000; }
    .oxide-colors .color-TiO2   { background: #999999; color: #000000; }
    .oxide-colors .color-ZrO    { background: #666666; color: #ffffff; }
    .oxide-colors .color-ZrO2   { background: #444444; color: #ffffff; }
    .oxide-colors .color-V2O5   { background: #cccccc; color: #000000; }
    .oxide-colors .color-Cr2O3  { background: #cccccc; color: #000000; }
    .oxide-colors .color-MnO    { background: #333333; color: #ffffff; }
    .oxide-colors .color-MnO2   { background: #111111; color: #ffffff; }
    .oxide-colors .color-FeO    { background: #993333; color: #000000; }
    .oxide-colors .color-Fe2O3  { background: #996666; color: #000000; }
    .oxide-colors .color-CoO    { background: #333399; color: #ffffff; }
    .oxide-colors .color-NiO    { background: #339999; color: #000000; }
    .oxide-colors .color-CuO    { background: #336666; color: #ffffff; }
    .oxide-colors .color-Cu2O   { background: #224444; color: #ffffff; }
    .oxide-colors .color-CdO    { background: #999933; color: #000000; }
    .oxide-colors .color-ZnO    { background: #cccccc; color: #000000; }
    .oxide-colors .color-F      { background: #cccccc; color: #000000; }
    .oxide-colors .color-PbO    { background: #ff0000; color: #ffffff; }
    .oxide-colors .color-SnO2   { background: #cccccc; color: #000000; }

</style>