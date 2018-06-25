<template>
    <div id="orton-cones-select">
        <table>
            <tr>
                <td>
                    From &nbsp;&nbsp;
                </td>
                <td>
                    <select class="form-control" v-bind:value="value.from"
                            v-on:change="updateFromValue($event.target.value)">
                        <option v-for="option in from_cone_options"
                                v-bind:value="option.value">
                            {{ option.text }}
                        </option>
                    </select>
                </td>
                <td>
                    &nbsp;&nbsp; to &nbsp;&nbsp;
                </td>
                <td>
                    <select class="form-control" v-model="value.to"
                            v-on:change="updateToValue($event.target.value)">
                        <option v-for="option in to_cone_options"
                                v-bind:value="option.value">
                            {{ option.text }}
                        </option>
                    </select>
                </td>
            </tr>
        </table>

    </div>
</template>

<script>
  import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'

  export default {

    props: {
      value: {
        type: Object,
        default: null
      }
    },

    data() {
      return {
        to_cone_options: null
      }
    },

    computed: {

      from_cone_options: function () {
        return GlazyConstants.ORTON_CONES_SELECT_TEXT;
      }

    },

    mounted() {
      this.to_cone_options = GlazyConstants.ORTON_CONES_SELECT_TEXT;
    },

    methods: {

      updateFromValue (value) {
        this.value.from = Number(value);

        if (this.value.to < this.value.from) {
          console.log('to ' + this.value.to + ' is less than from ' + this.value.from);
          this.value.to = this.value.from;
        }

        var cones = GlazyConstants.ORTON_CONES_SELECT_TEXT;
        for (var i = 0; i < cones.length; i++) {
          if (this.value.from === Number(cones[i].value)) {
            console.log('from ' + this.value.from + ' equal to ' + cones[i].value + ' to val: ' + this.value.to);
            this.to_cone_options = cones.slice(i);
          }
        }

        this.updateValue();
      },

      updateToValue (value) {
        this.value.to = Number(value);
        this.updateValue();
      },

      updateValue () {
        var orton_cone_range = {from: this.value.from, to: this.value.to};
        this.$emit('input', orton_cone_range);
      }

    }

  }
</script>

