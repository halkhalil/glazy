<template>
    <div class="container-fluid app-container">
        <AppHeader/>
        <router-view></router-view>
    </div>
</template>

<script>
    import AppHeader from './AppHeader.vue'
    import AppSidebar from './AppSidebar.vue'

  export default {
    name: 'AppLayoutFull',
    components: {
      AppHeader,
      AppSidebar
    },
    mounted() {
    }
  }
</script>

<style>
    .app-container {
    }
</style>