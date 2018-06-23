<template>
    <nav aria-label="breadcrumb" role="navigation" id="material-type-breadcrumbs">
        <ol class="breadcrumb">
            <li class="breadcrumb-item" v-for="breadcrumb in breadcrumbs">
                <router-link :to="{ name: breadcrumb.routeName, query: breadcrumb.query }"
                    class="align-middle">
                    {{ breadcrumb.name }}
                </router-link>
                <b-btn v-if="isViewingSelfCollection && breadcrumb.type === 'collection'"
                       v-b-tooltip.hover title="Delete Collection"
                       @click="deleteCollectionRequest(breadcrumb.query.collection)"
                       class="btn-icon btn-danger btn-sm breadcrumb-btn"><i class="fa fa-trash"></i></b-btn>
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
      },
      isViewingSelf: {
        type: Boolean,
        default: false
      },
      isViewingSelfCollection: {
        type: Boolean,
        default: false
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
        var breadcrumbs = []

        if (this.searchUser) {
          breadcrumbs.push({
            name: this.searchUser.name,
            query: { u: this.searchUser.u },
            routeName: 'user',
            type: 'user'
          })
        } else {
          breadcrumbs.push({
            name: 'All Users',
            query: { },
            routeName: 'search',
            type: 'search'
          })
        }

        if (this.searchQuery) {

          // Collection
          if (this.searchUser &&
            this.searchQuery.params.collection &&
            this.searchUser.collections &&
            this.searchUser.collections.length > 0) {
            var collection = this.searchUser.collections.find(x => x.id === this.searchQuery.params.collection)
            if (collection) {
              breadcrumbs.push({
                name: collection.name,
                query: { u: this.searchUser.u, collection: collection.id },
                routeName: 'user',
                type: 'collection'
              })
            }
          }

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
              routeName: 'search',
              type: 'search'
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
                  routeName: 'search',
                  type: 'search'
                })
              }
            })
          } else if (this.searchQuery.params.base_type
            && this.materialTypes.LOOKUP[this.searchQuery.params.base_type]) {
            breadcrumbs.push({
              name: this.materialTypes.LOOKUP[this.searchQuery.params.base_type],
              query: { base_type: this.searchQuery.params.base_type },
              routeName: 'search',
              type: 'search'
            })
          }

          // Atmosphere
          if (this.searchQuery.params.atmosphere
            && this.constants.ATMOSPHERE_LOOKUP[this.searchQuery.params.atmosphere]) {
            breadcrumbs.push({
              name: this.constants.ATMOSPHERE_LOOKUP[this.searchQuery.params.atmosphere],
              query: { atmosphere: this.searchQuery.params.atmosphere },
              routeName: 'search',
              type: 'search'
            })
          }

          // Surface
          if (this.searchQuery.params.surface
            && this.constants.SURFACE_LOOKUP[this.searchQuery.params.surface]) {
            breadcrumbs.push({
              name: this.constants.SURFACE_LOOKUP[this.searchQuery.params.surface],
              query: { surface: this.searchQuery.params.surface },
              routeName: 'search',
              type: 'search'
            })
          }

          // Transparency
          if (this.searchQuery.params.transparency
            && this.constants.TRANSPARENCY_LOOKUP[this.searchQuery.params.transparency]) {
            breadcrumbs.push({
              name: this.constants.TRANSPARENCY_LOOKUP[this.searchQuery.params.transparency ],
              query: { transparency: this.searchQuery.params.transparency },
              routeName: 'search',
              type: 'search'
            })
          }

          // Keywords
          if (this.searchQuery.params.keywords &&
            this.searchQuery.params.keywords.length >= this.minSearchTextLength) {
            breadcrumbs.push({
              name: '"' + this.searchQuery.params.keywords + '"',
              query: { keywords: this.searchQuery.params.keywords },
              routeName: 'search',
              type: 'search'
            })
          }

        }

        return breadcrumbs
      }
    },

    methods: {
      deleteCollectionRequest: function (id) {
        this.$emit('deleteCollectionRequest', id);
      }
    }

  }
</script>

<style>
    .breadcrumb {
        padding: 5px 10px;
        margin-bottom: 10px;
    }

    .breadcrumb-item {
        height: 30px;
        line-height:30px;
        text-align:center;
    }

    .breadcrumb-btn {
        margin: 0;
    }

</style>