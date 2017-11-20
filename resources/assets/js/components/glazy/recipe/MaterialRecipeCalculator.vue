<template>
    <table v-if="isLoaded"
           class="table table-hover material-recipe-calculator-table">
        <thead>
        <tr>
            <th>Material</th>
            <th>Amount</th>
            <th>Batch</th>
            <th>Subtotal</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(materialComponent, index) in this.materialComponents" v-bind:class="{ 'table-info' : materialComponent.isAdditional }">
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
            <td class="align-middle batch" :id="'batch_' + materialComponent.material.id">{{ batch_rows[index] }}</td>
            <td class="align-middle subtotal" :id="'subtotal_' + materialComponent.material.id">{{ subtotal_rows[index] }}</td>
        </tr>
        <tr class="align-middle subtotal">
            <td>Total</td>
            <td>{{ totalAmount }}</td>
            <td></td>
            <td></td>
        </tr>
        <tr class="batch_form">
            <td colspan="4">
                <form class="form-inline pull-right" id="batch_form" v-on:submit.prevent @keydown.enter="calculateBatch">
                    <div class="form-group">
                        <label for="batch_size">Batch:&nbsp;</label>
                        <input type="number" inputmode="numeric" size="10" class="form-control" placeholder="0.00" id="batch_size" v-model.number="batch_size">
                        <button type="button" v-on:click="calculateBatch" id="batch_calculate" class="btn btn-primary btn-icon">
                            <i class="fa fa-calculator"></i>
                        </button>
                    </div>
                </form>
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
      batch_size: '',
      batch_rows: [],
      subtotal_rows: []
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
      console.log('Get total amount.');
      var total_amount = 0.0;
      for (var index in this.materialComponents) {
        var materialComponent = this.materialComponents[index];
        if (!materialComponent.isAdditional) {
          total_amount += parseFloat(materialComponent.percentageAmount);
        }
      }
      return total_amount.toFixed(2);
    }
  },

  mounted() {
    console.log('Materials Calculator Component mounted.');
  },

  methods: {
    calculateBatch: function (event) {
      if (!isNaN(parseFloat(this.batch_size))) {
        var batch_rows = [];
        var subtotal_rows = [];
        var subtotal = 0;

        for (var index in this.materialComponents) {
          var materialComponent = this.materialComponents[index];
          var value = parseFloat(materialComponent.percentageAmount)
            * parseFloat(this.batch_size)
            / parseFloat(this.totalAmount);
          subtotal += value;
          batch_rows[index] = value.toFixed(2);
          subtotal_rows[index] = subtotal.toFixed(2);
        }
        this.$set(this, 'batch_rows', batch_rows);
        this.$set(this, 'subtotal_rows', subtotal_rows);
      }
    },


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