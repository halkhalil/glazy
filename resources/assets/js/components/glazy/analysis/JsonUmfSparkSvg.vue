<template>
    <svg v-bind:width="oxides.width" v-bind:height="squareSize">
        <rect x="0"
              y="0"
              v-bind:height="squareSize"
              v-bind:width="squareSize"
              class="color-R2O"
              v-bind:rx="radius"
              v-bind:ry="radius"></rect>

        <path class="color-RO"
              v-bind:d="rightRoundedRect(squareSize - (umf.ROTotal * squareSize), 0, umf.ROTotal * squareSize, squareSize, radius)"></path>
        <rect v-for="(item, index) in oxides.units"
              y="0"
              v-bind:x="item.x"
              v-bind:height="squareSize"
              v-bind:width="item.width"
              v-bind:class="'color-' + item.name"
              v-bind:rx="radius"
              v-bind:ry="radius">
        </rect>
        <circle v-if="oxides.isOversized" v-bind:cx="oxides.width - 22"
                v-bind:cy="Math.round(squareSize/2)"
                r="2"
                fill="black" />
        <circle v-if="oxides.isOversized" v-bind:cx="oxides.width - 16"
                v-bind:cy="Math.round(squareSize/2)"
                r="2"
                fill="black" />
        <circle v-if="oxides.isOversized" v-bind:cx="oxides.width - 10"
                v-bind:cy="Math.round(squareSize/2)"
                r="2"
                fill="black" />
    </svg>
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
      maxLength: {
        type: Number,
        default: 300
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
        ]
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
          if (width > this.maxLength) {
            isOversized = true
            break
          }
          var oxideValue = this.umf[this.sparkOxides[i]]
          if (oxideValue && Number(oxideValue).toFixed(2) > 0) {
            while (oxideValue >= 1) {
              if (width > this.maxLength) {
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
        if (isOversized) {
          totalWidth = this.maxLength
        }

        var oxides = {
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
    text.spark-legend { font-size: 10px; }
    rect.color-R2O    { fill: #ff6666; }
    path.color-RO     { fill: #cccc66; }
    rect.color-SiO2   { fill: #9999ff; }
    rect.color-Al2O3  { fill: #66ff66; }
    rect.color-B2O3   { fill: #339933; }
    rect.color-Li2O   { fill: #663333; }
    rect.color-Na2O   { fill: #ff6666; }
    rect.color-K2O    { fill: #99ffff; }
    rect.color-BeO    { fill: #ff3300; }
    rect.color-MgO    { fill: #ff9966; }
    rect.color-CaO    { fill: #ffff66; }
    rect.color-SrO    { fill: #ff6600; }
    rect.color-BaO    { fill: #ff6633; }
    rect.color-P2O5   { fill: #666699; }
    rect.color-TiO2   { fill: #999999; }
    rect.color-ZrO    { fill: #666666; }
    rect.color-ZrO2   { fill: #444444; }
    rect.color-V2O5   { fill: #cccccc; }
    rect.color-Cr2O3  { fill: #cccccc; }
    rect.color-MnO    { fill: #333333; }
    rect.color-MnO2   { fill: #111111; }
    rect.color-FeO    { fill: #993333; }
    rect.color-Fe2O3  { fill: #996666; }
    rect.color-CoO    { fill: #333399; }
    rect.color-NiO    { fill: #339999; }
    rect.color-CuO    { fill: #336666; }
    rect.color-Cu2O   { fill: #224444; }
    rect.color-CdO    { fill: #999933; }
    rect.color-ZnO    { fill: #cccccc; }
    rect.color-F      { fill: #cccccc; }
    rect.color-PbO    { fill: #ff0000; }
    rect.color-SnO2   { fill: #cccccc; }

</style>