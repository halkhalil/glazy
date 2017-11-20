<template>
    <table v-if="isLoaded"
           class="table table-hover material-recipe-calculator-table">
        <thead>
        <tr>
            <th>Material</th>
            <th>Amount</th>
            <th v-if="batchValues">Batch</th>
            <th v-if="batchValues">Subtotal</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(materialComponent, index) in this.materialComponents"
            v-bind:class="{ 'table-info' : materialComponent.isAdditional }">
            <td class="align-middle">
                <img class="rounded-circle"
                     width="40" height="40"
                     v-if="materialComponent.material.thumbnail"
                     :src="getImageUrl(materialComponent.material.thumbnail, 's')"/>
                <i v-if="materialComponent.isAdditional" class="fa fa-plus"></i>
                <a v-if="materialComponent.material.isPrimitive" :href="'/materials/' + materialComponent.material.id">{{ materialComponent.material.name }}</a>
                <a v-else :href="'/recipes/' + materialComponent.material.id">{{ materialComponent.material.name }}</a>
            </td>
            <td class="align-middle amount">
                {{ parseFloat(materialComponent.percentageAmount) }}
            </td>
            <td v-if="batchValues"
                class="align-middle batch" :id="'batch_' + materialComponent.material.id">
                {{ batchValues.batchRows[index] }}
            </td>
            <td v-if="batchValues"
                class="align-middle subtotal" :id="'subtotal_' + materialComponent.material.id">
                {{ batchValues.subtotalRows[index] }}
            </td>
        </tr>
        <tr class="align-middle subtotal">
            <td>Total</td>
            <td>{{ totalAmount }}</td>
            <td v-if="batchValues" colspan="2"></td>
        </tr>
        <tr class="batch_form">
            <td v-bind:colspan="numColumns" class="text-right">
                        Calculate Batch:&nbsp;
                        <input type="number"
                               inputmode="numeric"
                               size="10"
                               placeholder="0.00"
                               id="batchSize"
                               v-model.number="batchSize">
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script>
export default {
  name: 'MaterialRecipeCalculator',

  props: {
    materialComponents: {
      type: Array,
      default: null
    }
  },

  data() {
    return {
      batchSize: null
    }
  },

  computed: {
    isLoaded: function () {
      if (this.materialComponents) {
        return true;
      }
      return false;
    },
    totalAmount: function () {
      var totalAmount = 0.0
      if (this.materialComponents && this.materialComponents.length > 0) {
        this.materialComponents.forEach(function (materialComponent, index) {
          if (!materialComponent.isAdditional) {
            totalAmount += parseFloat(materialComponent.percentageAmount);
          }
        })
        return totalAmount.toFixed(2)
      }
      return 0
    },
    batchValues: function () {
      if (this.batchSize &&
        !isNaN(parseFloat(this.batchSize)) &&
        this.materialComponents) {
        var batchValues = {
          batchRows: [],
          subtotalRows: []
        }
        var subtotal = 0;
        this.materialComponents.forEach(function (materialComponent, index) {
          var value = parseFloat(materialComponent.percentageAmount)
            * parseFloat(this.batchSize)
            / parseFloat(this.totalAmount)
          subtotal += value
          batchValues.batchRows[index] = value.toFixed(2)
          batchValues.subtotalRows[index] = subtotal.toFixed(2)
        }.bind(this))
        return batchValues
      }
      return null
    },
    numColumns: function () {
      if (this.batchValues) {
        return 4
      }
      return 2
    }
  },

  mounted() {
    console.log('Materials Calculator Component mounted.');
  },

  methods: {

    getImageBin: function (id) {
      id = '' + id;
      console.log("IMAGE BIN: " + id.substr(id.length - 2))
      return id.substr(id.length - 2);
    },

    getImageUrl: function (materialImage, size) {
      var bin = this.getImageBin(materialImage.materialId);
      return GLAZY_APP_URL + '/storage/uploads/recipes/' + bin + '/' + size + '_' + materialImage.filename;
    }
  }

}

</script>

<style>

    .material-recipe-calculator-table tr th {
        font-size: 10px;
        padding: 5px;
    }

    .material-recipe-calculator-table tr td {
        padding: 5px;
    }

    .material-recipe-calculator-table tr td.batch {
        color: #666666;
    }

    .material-recipe-calculator-table tr.subtotal {
        background-color: #efefef;
    }

    .material-recipe-calculator-table tr.batch_form {
    }

</style>