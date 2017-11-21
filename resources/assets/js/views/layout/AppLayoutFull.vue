<template>
    <div class="container-fluid app-container">
        <AppHeader/>
        <router-view></router-view>
    </div>
</template>

<script>
    import AppHeader from './AppHeader.vue'

  export default {
    name: 'AppLayoutFull',
    components: {
      AppHeader
    },
    mounted() {
    }
  }
</script>

<style>
    .app-container {
    }
</style>