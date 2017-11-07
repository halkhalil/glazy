<template>
    <tbody v-if="isLoaded">
        <tr class="umf-spark-oxide-list" v-if="showOxideList">
            <td v-if="showLegend">
            </td>
            <td>
                R<sub>2</sub>O:RO
            </td>
            <td v-if="umf.getOxide('B2O3') > 0">
                B
            </td>
            <td v-else="umf.getOxide('B2O3') > 0">
            </td>
            <td v-if="showLegend">
            </td>
            <td>
                Al
            </td>
            <td>
                Si
            </td>
        </tr>
        <tr class="oxide-colors">
            <td class="umf-spark-legend" v-if="showLegend">
                <span class="umf-spark-label">R<sub>2</sub>O:RO</span>
                <br/>
                {{ Math.round(umf.getR2OTotal() * 100) / 100 }}:{{ Math.round(umf.getROTotal() * 100) / 100 }}
            </td>
            <td class="umf-spark-row" v-bind:style="{ width: 5 + squareSize + 'px' }">
                <div v-if="umf.getR2OTotal() > 0"
                     v-bind:tooltip="'R2O:RO ' + Number(umf.getR2OTotal()).toFixed(2) + ':' + Number(umf.getROTotal()).toFixed(2)"
                     class="umf-spark umf-spark-r2o color-R2O"
                     v-bind:style="{ height: squareSize + 'px', lineHeight: squareSize + 'px', width: (umf.getR2OTotal() * squareSize) + 'px' }">
                </div>
                <div v-if="umf.getROTotal() > 0"
                     v-bind:tooltip="'R2O:RO ' + Number(umf.getR2OTotal()).toFixed(2) + ':' + Number(umf.getROTotal()).toFixed(2)"
                     class="umf-spark umf-spark-ro color-RO"
                     v-bind:style="{ height: squareSize + 'px', lineHeight: squareSize + 'px', width: (umf.getROTotal() * squareSize) + 'px' }">
                </div>
            </td>
            <td class="umf-spark-row"
                v-if="umf.getOxide('B2O3') > 0">
                <div v-for="item in myB2O3"
                     v-bind:tooltip="'B2O3 ' + Number(umf.getOxide('B2O3')).toFixed(2)"
                     class="umf-spark color-B2O3"
                     v-bind:style="{ height: squareSize + 'px', lineHeight: squareSize + 'px', width : item.width }">
                </div>
            </td>
            <td v-else></td>
            <td class="umf-spark-legend" v-if="showLegend">
                <span class="umf-spark-label">Si:Al</span>
                {{ Number(umf.getSiO2Al2O3Ratio()).toFixed(2) }}
            </td>
            <td class="umf-spark-row">
                <div v-for="item in myAl2O3"
                     v-bind:tooltip="'Al2O3 ' + Number(umf.getOxide('Al2O3')).toFixed(2)"
                     class="umf-spark color-Al2O3"
                     v-bind:style="{ height: squareSize + 'px', lineHeight: squareSize + 'px', width : item.width }">
                </div>
            </td>
            <td class="umf-spark-row">
                <div v-for="item in mySiO2"
                     v-bind:tooltip="'SiO2 ' + Number(umf.getOxide('SiO2')).toFixed(2)"
                     class="umf-spark color-SiO2"
                     v-bind:style="{ height: squareSize + 'px', lineHeight: squareSize + 'px', width : item.width }">
                </div>
            </td>
            <td class="umf-spark-row">
                <div v-for="item in otherOxides"
                     v-bind:tooltip="item.name + ' ' + Number(umf.getOxide(item.name)).toFixed(2)"
                     v-bind:class="'umf-spark color-' + item.name"
                     v-bind:style="{ height: squareSize + 'px', lineHeight: squareSize + 'px', width : item.width }">
                </div>
            </td>
        </tr>
    </tbody>

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
      }
    },

    computed: {

      umf: function () {
        return this.material.getROR2OUnityFormulaAnalysis();
      },

      myB2O3: function () {
        var units = []
        for (var i = 0, len = Math.floor(this.umf.getOxide('B2O3')); i < len; i++) {
          units[i] = { width: this.squareSize + 'px' }
        }

        if (this.umf.getOxide('B2O3') % 1 > 0) {
          units.push({ width: ((this.umf.getOxide('B2O3') % 1) * this.squareSize).toFixed(1) + 'px' })
        }

        return units
      },

      myAl2O3: function () {
        var units = []
        for (var i = 0, len = Math.floor(this.umf.getOxide('Al2O3')); i < len; i++) {
          units[i] = { width: this.squareSize + 'px' }
        }

        if (this.umf.getOxide('Al2O3') % 1 > 0) {
          units.push({ width: ((this.umf.getOxide('Al2O3') % 1) * this.squareSize).toFixed(1) + 'px' })
        }

        return units
      },

      mySiO2: function () {
        var units = []
        for (var i = 0, len = Math.floor(this.umf.getOxide('SiO2')); i < len; i++) {
          units[i] = { width: this.squareSize + 'px' }
        }

        if (this.umf.getOxide('SiO2') % 1 > 0) {
          units.push({ width: ((this.umf.getOxide('SiO2') % 1) * this.squareSize).toFixed(1) + 'px' })
        }

        return units
      },

      otherOxides: function () {
        var units = []

        for (var i = 0; i < Analysis.OTHER_OXIDES.length; i++) {
          var oxideValue = this.umf.getOxide(Analysis.OTHER_OXIDES[i]);
          if (oxideValue.toFixed(2) > 0) {
            while (oxideValue >= 1) {
              units.push({
                name: Analysis.OTHER_OXIDES[i],
                width: this.squareSize + 'px'
              })
              oxideValue -= 1;
            }
            if (oxideValue > 0) {
              units.push({
                name: Analysis.OTHER_OXIDES[i],
                width: (oxideValue * this.squareSize).toFixed(1) + 'px'
              })
            }
          }
        }

        return units
      },

      isLoaded: function () {
        if (this.material) {
          return true;
        }
        return false;
      }
    }

  }

</script>

<style>
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

    .umf-spark {
        margin: 0px 3px 5px 0px;
        float: left;
        display: inline-block;
        overflow:hidden;
        background: #000000;
        border-radius: 5px;
        text-align: center;
        box-shadow: 0 0 0 1px #ddd;
    }

    .umf-spark-label {
        opacity: 0.5;
    }

    .umf-spark-oxide {
        vertical-align:-50%;
    }

    .umf-spark-r2o {
        margin: 0px 0px 5px 0px;
        border-top-right-radius: 0px;
        border-bottom-right-radius: 0px;
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
        text-align: center;
    }

    .umf-spark-ro {
        margin: 0px 3px 5px 0px;
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
        border-top-left-radius: 0px;
        border-bottom-left-radius: 0px;
    }

    .umf-spark-b2o3 {
        margin: 0px 3px 5px 0px;
    }

    .umf-spark .oxide-title {
        display:inline-block;
        font-weight: normal;
        font-size: 12px;
        line-height: 12px;
        opacity: 0.5;
    }

    .umf-spark-legend {
        margin: 0px 8px 0px 0px;
        float: left;
        height: 30px;
        width: 4em;
        display: inline-block;
        text-align: center;
        font-size: 1em;
        line-height: 1em;
    }

    .umf-spark-oxide-list {
        font-size: .8em;
        line-height: .8em;
        vertical-align: text-top;
    }

    [tooltip]:before {
        /* needed - do not touch */
        content: attr(tooltip);
        position: absolute;
        opacity: 0;

        /* customizable */
        transition: all 0.15s ease;
        height: 30px;
        padding: 0 5px 0 5px;
        color: #eee;
        border-radius: 5px;
        box-shadow: 2px 2px 1px silver;
    }

    [tooltip]:hover:before {
        /* needed - do not touch */
        opacity: 1;

        /* customizable */
        background: black;
        margin-top: -35px;
        margin-left: -20px;
    }

    [tooltip]:not([tooltip-persistent]):before {
        pointer-events: none;
    }


</style>