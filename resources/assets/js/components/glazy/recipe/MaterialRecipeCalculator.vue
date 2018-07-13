<template>
    <div class="table-responsive">
        <table v-if="isLoaded"
               class="table table-hover material-recipe-calculator-table">
            <thead>
            <tr>
                <th>Material</th>
                <th class="text-right">Amount</th>
                <th v-if="batchValues" class="text-right">Batch</th>
                <th v-if="batchValues" class="text-right">Subtotal</th>
            </tr>
            </thead>
            <tbody>
            <tr v-if="batchValues && tareSize"
                class="table-warning">
                <td>Tare Weight</td>
                <td></td>
                <td v-if="batchValues"
                    class="text-right batch"
                    id="batch_tare">
                    {{ parseFloat(tareSize) }}
                </td>
                <td v-if="batchValues"
                    class="text-right subtotal"
                    id="subtotal_tare">
                    {{ parseFloat(tareSize) }}
                </td>
            </tr>
            <tr v-for="(materialComponent, index) in this.materialComponents"
                v-if="!materialComponent.isAdditional">
                <td class="align-middle">
                    <img class="rounded-circle d-none d-sm-block"
                         width="40" height="40"
                         v-if="materialComponent.material.thumbnail"
                         :src="glazyHelper.getSmallImageUrl(materialComponent.material, materialComponent.material.thumbnail)"/>
                    <router-link v-if="materialComponent.material.isPrimitive" :to="{ name: 'material', params: { id: materialComponent.material.id }}">{{ materialComponent.material.name }}</router-link>
                    <router-link v-else :to="{ name: 'recipes', params: { id: materialComponent.material.id }}">{{ materialComponent.material.name }}</router-link>
                </td>
                <td class="text-right amount">
                    {{ parseFloat(materialComponent.percentageAmount) }}
                </td>
                <td v-if="batchValues"
                    class="text-right batch" :id="'batch_' + materialComponent.material.id">
                    {{ parseFloat(batchValues.batchRows[index]) }}
                </td>
                <td v-if="batchValues"
                    class="text-right subtotal" :id="'subtotal_' + materialComponent.material.id">
                    {{ parseFloat(batchValues.subtotalRows[index]) }}
                </td>
            </tr>
            <tr v-if="hasAdditional" class="align-middle total">
                <td>Total Base Recipe</td>
                <td class="text-right">{{ parseFloat(baseRecipeAmount) }}</td>
                <td v-if="batchValues" colspan="2"></td>
            </tr>

            <tr v-for="(materialComponent, index) in this.materialComponents"
                v-if="materialComponent.isAdditional"
                class="table-info">
                <td class="align-middle">
                    <img class="rounded-circle d-none d-sm-block"
                         width="40" height="40"
                         v-if="materialComponent.material.thumbnail"
                         :src="glazyHelper.getSmallImageUrl(materialComponent.material, materialComponent.material.thumbnail)"/>
                    <i class="fa fa-plus"></i>
                    <router-link v-if="materialComponent.material.isPrimitive" :to="{ name: 'material', params: { id: materialComponent.material.id }}">{{ materialComponent.material.name }}</router-link>
                    <router-link v-else :to="{ name: 'recipes', params: { id: materialComponent.material.id }}">{{ materialComponent.material.name }}</router-link>
                </td>
                <td class="text-right amount">
                    {{ parseFloat(materialComponent.percentageAmount) }}
                </td>
                <td v-if="batchValues"
                    class="text-right batch" :id="'batch_' + materialComponent.material.id">
                    {{ parseFloat(batchValues.batchRows[index]) }}
                </td>
                <td v-if="batchValues"
                    class="text-right subtotal" :id="'subtotal_' + materialComponent.material.id">
                    {{ parseFloat(batchValues.subtotalRows[index]) }}
                </td>
            </tr>

            <tr class="align-middle total">
                <td>Total</td>
                <td class="text-right">{{ parseFloat(totalRecipeAmount) }}</td>
                <td v-if="batchValues" colspan="2"></td>
            </tr>
            <tr class="batch_form">
                <td v-bind:colspan="numColumns" class="text-right">
                    Tare:
                    <input type="number"
                           inputmode="numeric"
                           size="4"
                           maxlength="10"
                           placeholder="0.0"
                           id="tareSize"
                           class="material-recipe-calculator-tare-input"
                           v-model.number="tareSize">

                    Batch:
                    <input type="number"
                           inputmode="numeric"
                           size="8"
                           maxlength="10"
                           placeholder="0.0"
                           id="batchSize"
                           class="material-recipe-calculator-batch-input"
                           v-model.number="batchSize">
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import GlazyHelper from '../helpers/glazy-helper'

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
      glazyHelper: new GlazyHelper(),
      batchSize: null,
      tareSize: null
    }
  },

  computed: {
    isLoaded: function () {
      if (this.materialComponents) {
        return true;
      }
      return false;
    },
    baseRecipeAmount: function () {
      var baseRecipeAmount = 0.0
      if (this.materialComponents && this.materialComponents.length > 0) {
        this.materialComponents.forEach(function (materialComponent, index) {
          if (!materialComponent.isAdditional) {
            baseRecipeAmount += parseFloat(materialComponent.percentageAmount);
          }
        })
        return baseRecipeAmount.toFixed(2)
      }
      return 0
    },
    totalRecipeAmount: function () {
      var totalRecipeAmount = 0.0
      if (this.materialComponents && this.materialComponents.length > 0) {
        this.materialComponents.forEach(function (materialComponent, index) {
          totalRecipeAmount += parseFloat(materialComponent.percentageAmount);
        })
        return totalRecipeAmount.toFixed(2)
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
        if (this.tareSize && parseFloat(this.tareSize) > 0) {
          subtotal += parseFloat(this.tareSize)
        }
        this.materialComponents.forEach(function (materialComponent, index) {
          var value = parseFloat(materialComponent.percentageAmount)
            * parseFloat(this.batchSize)
            / parseFloat(this.baseRecipeAmount)
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
    },
    hasAdditional: function () {
      var hasAdditional = false
      this.materialComponents.forEach(function (materialComponent, index) {
        if (materialComponent.isAdditional) {
          hasAdditional = true
        }
      })
      return hasAdditional
    }
  }
}

</script>

<style>

    .material-recipe-calculator-table {
        font-size: 16px;
    }

    .material-recipe-calculator-table tr th {
        font-size: 0.75em;
        padding: 5px;
    }

    .material-recipe-calculator-table tr td {
        padding: 5px;
    }

    .material-recipe-calculator-table tr td.batch {
        color: #009900;
    }

    .material-recipe-calculator-table tr td.subtotal {
        color: #000099;
    }

    .material-recipe-calculator-table tr.total {
        background-color: #efefef;
        font-size: 0.75em;
        font-style: italic;
    }

    .material-recipe-calculator-table tr.batch_form {
    }

    .material-recipe-calculator-tare-input {
        width: 80px;
        margin-right: 10px;
    }

    .material-recipe-calculator-batch-input {
        width: 80px;
    }

</style>