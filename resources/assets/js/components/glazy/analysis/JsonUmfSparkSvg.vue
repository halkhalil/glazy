<template>
    <div class="umf-svg-container"
         v-bind:height="oxides.height">
        <svg xmlns:svg="http://www.w3.org/2000/svg"
             xmlns="http://www.w3.org/2000/svg"
             version="1.1"
             v-bind:viewbox="'0 0 100 100'"
             v-bind:width="oxides.width"
             v-bind:height="oxides.height"
             class="umf-svg">

            <rect x="0"
                  y="0"
                  v-bind:height="squareSize"
                  v-bind:width="squareSize"
                  class="oxide-colors-fill-R2O"
                  v-bind:rx="radius"
                  v-bind:ry="radius">
                <title v-if="showOxideTitle">R₂O:RO</title>
            </rect>

            <path class="oxide-colors-fill-RO"
                  v-bind:d="rightRoundedRect(squareSize - (umf.ROTotal * squareSize), 0, umf.ROTotal * squareSize, squareSize, radius)">
                <title v-if="showOxideTitle">R₂O:RO</title>
            </path>

            <text v-if="showOxideList"
                  class="legend"
                  v-bind:style="{ fontSize: fontSize + 'px' }"
                  v-bind:transform="'translate(' + Math.round(squareSize/2) + ', ' + Math.round(squareSize/2) + ') rotate(90)'"
                  v-bind:x="0"
                  v-bind:y="0">
                R₂O:RO
            </text>

            <g v-for="(item, index) in oxides.units">
                <rect y="0"
                      v-bind:x="item.x"
                      v-bind:height="squareSize"
                      v-bind:width="item.width"
                      v-bind:class="'oxide-colors-fill-' + item.name"
                      v-bind:rx="radius"
                      v-bind:ry="radius">
                    <title v-if="showOxideTitle">{{ item.name }}</title>
                </rect>
                <text v-if="showOxideList && item.width >= fontSize"
                      class="legend"
                      v-bind:style="{ fontSize: fontSize + 'px' }"
                      v-bind:transform="'translate(' + (item.x + Math.round(item.width/2) - Math.round(fontSize/2)) + ', ' + Math.round(squareSize/2) + ') rotate(90)'"
                      v-bind:x="0"
                      v-bind:y="0">
                    {{ OXIDE_NAME_UNICODE[item.name] }}
                </text>
            </g>

            <circle v-if="oxides.isOversized" v-bind:cx="oxides.width - 22"
                    v-bind:cy="Math.round(squareSize/2)"
                    r="2"
                    fill="rgba(0,0,0,0.5)" />
            <circle v-if="oxides.isOversized" v-bind:cx="oxides.width - 16"
                    v-bind:cy="Math.round(squareSize/2)"
                    r="2"
                    fill="rgba(0,0,0,0.5)" />
            <circle v-if="oxides.isOversized" v-bind:cx="oxides.width - 10"
                    v-bind:cy="Math.round(squareSize/2)"
                    r="2"
                    fill="rgba(0,0,0,0.5)" />
        </svg>
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
      showOxideTitle: {
        type: Boolean,
        default: true
      },
      squareSize: {
        type: Number,
        default: 24
      },
      radius: {
        type: Number,
        default: 5
      },
      margin: {
        type: Number,
        default: 2
      },
      maxWidth: {
        type: Number,
        default: 0
      },
      fontSize: {
        type: Number,
        default: 10
      }
    },

    data() {
      return {
        sparkOxides: [
          'B2O3',
          'Al2O3',
          'SiO2',
          'Fe2O3',
          'MnO2',
          'P2O5',
          'F',
          'CoO',
          'Cr2O3',
          'Cu2O',
          'CuO',
          'NiO',
          'V2O5',
          'ZrO'
        ],
        OXIDE_NAME_UNICODE: Analysis.OXIDE_NAME_UNICODE
      }
    },

    computed: {

      umf: function () {
        return this.material.analysis.umfAnalysis;
      },

      oxides: function () {
        var units = []
        var j = 0
        var isOversized = false
        // add in unity square initial x value
        var width = this.squareSize + this.margin

        for (var i = 0; i < this.sparkOxides.length; i++) {
          if (this.maxWidth > 0 && width + this.squareSize > this.maxWidth) {
            isOversized = true
            break
          }
          var oxideValue = this.umf[this.sparkOxides[i]]
          if (oxideValue && Number(oxideValue).toFixed(2) > 0) {
            while (oxideValue >= 1) {
              if (this.maxWidth > 0 && width + this.squareSize > this.maxWidth) {
                isOversized = true
                break
              }
              units[j++] = {
                name: this.sparkOxides[i],
                width: this.squareSize,
                x: width
              }
              width += this.squareSize + this.margin
              oxideValue -= 1
            }
            var partial = Math.round(oxideValue * this.squareSize)
            if (!isOversized && partial > 0) {
              units[j++] = {
                name: this.sparkOxides[i],
                width: partial,
                x: width
              }
              width += partial + this.margin
            }
          }
        }

        var totalWidth = width
        if (this.maxWidth > 0 && isOversized) {
          totalWidth = this.maxWidth
        }

        var height = this.squareSize
        /*
        if (this.showOxideList) {
          height += this.fontSize + (this.margin * 2)
        }
        */
        var oxides = {
          height: height,
          width: totalWidth,
          isOversized: isOversized,
          units: units
        }

        return oxides
      },

      isLoaded: function () {
        if (this.material) {
          return true;
        }
        return false;
      }
    },
    methods: {
      /*
       https://bl.ocks.org/mbostock/3468167
       // Returns path data for a rectangle with rounded right corners.
       // Note: it’s probably easier to use a <rect> element with rx and ry attributes!
       // The top-left corner is ⟨x,y⟩.
       */
      rightRoundedRect: function (x, y, width, height, radius) {
        return "M" + x + "," + y
          + "h" + (width - radius)
          + "a" + radius + "," + radius + " 0 0 1 " + radius + "," + radius
          + "v" + (height - 2 * radius)
          + "a" + radius + "," + radius + " 0 0 1 " + -radius + "," + radius
          + "h" + (radius - width)
          + "z";
      }
    }

  }
</script>

<style>
    .umf-svg-container {
        position: relative;
        width: 100%;
        overflow-x: scroll;
        overflow-y: hidden;
    }
    .umf-svg text.legend {
        stroke: none;
        fill: rgba(255,255,255,0.6);
        text-anchor: middle;
        overflow-x: visible;
    }
    /*
    .umf-svg rect.color-R2O    { fill: #ff6666; }
    .umf-svg path.color-RO     { fill: #cccc66; }
    .umf-svg rect.color-SiO2   { fill: #9999ff; }
    .umf-svg rect.color-Al2O3  { fill: #66ff66; }
    .umf-svg rect.color-B2O3   { fill: #339933; }
    .umf-svg rect.color-Li2O   { fill: #663333; }
    .umf-svg rect.color-Na2O   { fill: #ff6666; }
    .umf-svg rect.color-K2O    { fill: #99ffff; }
    .umf-svg rect.color-BeO    { fill: #ff3300; }
    .umf-svg rect.color-MgO    { fill: #ff9966; }
    .umf-svg rect.color-CaO    { fill: #ffff66; }
    .umf-svg rect.color-SrO    { fill: #ff6600; }
    .umf-svg rect.color-BaO    { fill: #ff6633; }
    .umf-svg rect.color-P2O5   { fill: #666699; }
    .umf-svg rect.color-TiO2   { fill: #999999; }
    .umf-svg rect.color-ZrO    { fill: #666666; }
    .umf-svg rect.color-ZrO2   { fill: #444444; }
    .umf-svg rect.color-V2O5   { fill: #cccccc; }
    .umf-svg rect.color-Cr2O3  { fill: #cccccc; }
    .umf-svg rect.color-MnO    { fill: #333333; }
    .umf-svg rect.color-MnO2   { fill: #111111; }
    .umf-svg rect.color-FeO    { fill: #993333; }
    .umf-svg rect.color-Fe2O3  { fill: #996666; }
    .umf-svg rect.color-CoO    { fill: #333399; }
    .umf-svg rect.color-NiO    { fill: #339999; }
    .umf-svg rect.color-CuO    { fill: #336666; }
    .umf-svg rect.color-Cu2O   { fill: #224444; }
    .umf-svg rect.color-CdO    { fill: #999933; }
    .umf-svg rect.color-ZnO    { fill: #cccccc; }
    .umf-svg rect.color-F      { fill: #cccccc; }
    .umf-svg rect.color-PbO    { fill: #ff0000; }
    .umf-svg rect.color-SnO2   { fill: #cccccc; }
    */
</style>