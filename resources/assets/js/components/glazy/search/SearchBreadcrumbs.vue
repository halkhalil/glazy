<template>
    <nav aria-label="breadcrumb" role="navigation" id="material-type-breadcrumbs">
        <ol class="breadcrumb">
            <li class="breadcrumb-item" v-for="breadcrumb in breadcrumbs">
                <router-link :to="{ name: breadcrumb.routeName, query: breadcrumb.query }">
                    {{ breadcrumb.name }}
                </router-link>
            </li>
        </ol>
    </nav>
</template>
<script>
  import MaterialTypes from 'ceramicscalc-js/src/material/MaterialTypes'
  import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'

  export default {

    name: 'SearchBreadcrumbs',

    props: {
      searchQuery: {
        type: Object,
        default: null
      },
      searchUser: {
        type: Object,
        default: null
      }
    },

    data() {
      return {
        materialTypes: new MaterialTypes(),
        constants: new GlazyConstants(),
        minSearchTextLength: 3
      }
    },

    computed: {

      breadcrumbs () {
        console.log("BREADCRUMBS TYPE " + this.searchQuery.params.type)
        var breadcrumbs = []

        if (this.searchUser) {
          breadcrumbs.push({
            name: this.searchUser.name,
            query: { u: this.searchUser.u },
            routeName: 'user'
          })
        } else {
          breadcrumbs.push({
            name: 'All Users',
            query: { },
            routeName: 'search'
          })
        }

        if (this.searchQuery) {

          // Cone
          if (this.searchQuery.params.cone
            && this.constants.ORTON_CONES_LOOKUP[this.searchQuery.params.cone]) {
            var name = this.constants.ORTON_CONES_LOOKUP[this.searchQuery.params.cone]
            if (name === 'High' || name === 'Mid' || name === 'Low') {
              name += '-fire'
            } else {
              name = '△' + name
            }
            breadcrumbs.push({
              name: name,
              query: { cone: this.searchQuery.params.cone },
              routeName: 'search'
            })
          }

          // Type & subtype hierarchy
          if (this.searchQuery.params.type
            && this.materialTypes.CHILD_LINEAGES[this.searchQuery.params.type]) {
            var lineage  = this.materialTypes.CHILD_LINEAGES[this.searchQuery.params.type]
            lineage.forEach((type) => {
              if (type !== 1 && type !== 100) {
                breadcrumbs.push({
                  name: this.materialTypes.LOOKUP[type],
                  query: { type: type },
                  routeName: 'search'
                })
              }
            })
          } else if (this.searchQuery.params.base_type
            && this.materialTypes.LOOKUP[this.searchQuery.params.base_type]) {
            breadcrumbs.push({
              name: this.materialTypes.LOOKUP[this.searchQuery.params.base_type],
              query: { base_type: this.searchQuery.params.base_type },
              routeName: 'search'
            })
          }

          // Atmosphere
          if (this.searchQuery.params.atmosphere
            && this.constants.ATMOSPHERE_LOOKUP[this.searchQuery.params.atmosphere]) {
            breadcrumbs.push({
              name: this.constants.ATMOSPHERE_LOOKUP[this.searchQuery.params.atmosphere],
              query: { atmosphere: this.searchQuery.params.atmosphere },
              routeName: 'search'
            })
          }

          // Surface
          if (this.searchQuery.params.surface
            && this.constants.SURFACE_LOOKUP[this.searchQuery.params.surface]) {
            breadcrumbs.push({
              name: this.constants.SURFACE_LOOKUP[this.searchQuery.params.surface],
              query: { surface: this.searchQuery.params.surface },
              routeName: 'search'
            })
          }

          // Transparency
          if (this.searchQuery.params.transparency
            && this.constants.TRANSPARENCY_LOOKUP[this.searchQuery.params.transparency]) {
            breadcrumbs.push({
              name: this.constants.TRANSPARENCY_LOOKUP[this.searchQuery.params.transparency ],
              query: { transparency: this.searchQuery.params.transparency },
              routeName: 'search'
            })
          }

          // Keywords
          if (this.searchQuery.params.keywords &&
            this.searchQuery.params.keywords.length >= this.minSearchTextLength) {
            breadcrumbs.push({
              name: '"' + this.searchQuery.params.keywords + '"',
              query: { keywords: this.searchQuery.params.keywords },
              routeName: 'search'
            })
          }

        }

        return breadcrumbs
      }
    },

    methods: {

    }

  }
</script>

