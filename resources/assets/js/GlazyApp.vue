<template>
  <div v-if="$auth.ready()">
    <router-view></router-view>
  </div>
</template>

<script>
//require('material-design-lite');

import { AppLayoutFull, AppHeader, AppSidebar } from './views/layout/'

export default {
  name: 'GlazyApp',
  components: {
    AppLayoutFull,
    AppHeader,
    AppSidebar
  }
}
</script>

<style lang="scss">
  // Import Main styles for this application
  @import '../sass/style';
</style>
