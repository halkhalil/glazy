<template>

<table v-if="'materialComponents' in material && material.materialComponents.length > 0"
        class="table table-sm simple-material-component-table">
    <thead v-if="title">
    <tr>
        <th colspan="2">
            {{ title }}
        </th>
    </tr>
    </thead>
    <tbody>
    <tr v-for="(materialComponent, index) in material.materialComponents"
        v-bind:class="{ 'table-info' : materialComponent.isAdditional }">
        <td>
            <i v-if="materialComponent.isAdditional" class="fa fa-plus"></i>
            {{ materialComponent.material.name }}
        </td>
        <td class="text-right">
            {{ Number(materialComponent.amount).toFixed(2) }}
        </td>
    </tr>
    <tr class="table-dark subtotal">
        <td>Total</td>
        <td class="text-right">{{ Number(material.componentsTotalPercentage).toFixed(2) }}</td>
    </tr>
    </tbody>
</table>

</template>
<script>
  export default {

    name: 'SimpleMaterialComponentTable',

    props: {
      material: {
        type: Object,
        default: null
      },
      title: {
        type: String,
        default: null
      }
    }
  }
</script>

<style>

  .simple-material-component-table tr th {
      padding: 0.2rem;
      font-size: 10px;
  }
  .simple-material-component-table tr td {
      padding: 0.2rem;
      font-size: 12px;
  }

  .simple-material-component-table tr.subtotal {
      font-style: italic;
  }


</style>
