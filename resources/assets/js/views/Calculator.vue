<template>
  <div class="mt-3">
    <edit-recipe-components></edit-recipe-components>
  </div>
</template>

<script>
  import EditRecipeComponents from '../components/glazy/recipe/EditRecipeComponents.vue'

  export default {
    name: 'Calculator',
    components: {
      EditRecipeComponents
    },
    data() {
      return {}
    }
  }
</script>
