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
  },
  metaInfo: {
    // if no subcomponents specify a metaInfo.title, this title will be used
    title: 'Glazy',
    // all titles will be injected into this template
    titleTemplate: '%s | Glazy',
    meta: [
      {
        'property': 'og:type',
        'content': 'website'
      },
      {
        'property': 'og:site_name',
        'content': 'Glazy Ceramic Glaze and Pottery Recipes'
      }
    ]
  }
}
</script>

<style lang="scss">
  // Import Main styles for this application
  @import '../sass/style';
</style>
