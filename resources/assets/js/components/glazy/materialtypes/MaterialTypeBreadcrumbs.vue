<template>
    <nav aria-label="breadcrumb" role="navigation" id="material-type-breadcrumbs">
        <ol class="breadcrumb">
            <li class="breadcrumb-item" v-for="type in types" :class="{ active: type.isActive }">
                <router-link v-if="type.isBaseType"
                             :to="{ path: '/search', query: { base_type: type.id }}">
                    {{ materialTypes.LOOKUP[type.id] }}
                </router-link>
                <router-link v-else
                             :to="{ path: '/search', query: { type: type.id }}">
                    {{ materialTypes.LOOKUP[type.id] }}
                </router-link>
            </li>
        </ol>
    </nav>
</template>
<script>

  import MaterialTypes from 'ceramicscalc-js/src/material/MaterialTypes'


  export default {

      name: 'MaterialTypeBreadcrumbs',

      props: {
        recipe: {
          type: Object,
          default: null
        }
      },

      data() {
        return {
          materialTypes: new MaterialTypes()
        }
      },

          computed: {

            isLoaded: function () {
                if (this.recipe) {
                    return true;
                }
                return false;
            },

            types: function() {
                var types = [];
                if (this.isLoaded) {
                    if (this.recipe.materialType) {
                        this.addValue(types, this.recipe.materialType, this.recipe.baseTypeId, true);
                    }
                }
                return types;
            }
        },

        methods: {

            addValue: function(myTypes, jsonType, baseTypeId, isActive) {
                var type = {
                    id: jsonType.id,
                    name: jsonType.name,
                    concat: jsonType.concatenatedName,
                    isBaseType: false,
                    isActive: isActive
                };
                if (jsonType.id == baseTypeId) {
                    type.isBaseType = true;
                }
                myTypes.splice(0, 0, type);

                if (jsonType.id == baseTypeId) {
                    // don't go past the base type
                    return;
                }
                else if (jsonType.parentMaterialType) {
                    this.addValue(myTypes, jsonType.parentMaterialType, baseTypeId, false);
                }
            }

        }

    }
</script>

