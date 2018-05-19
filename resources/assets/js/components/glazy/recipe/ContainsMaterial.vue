<template>
    <div id="contains-material">
        <div class="load-container load7" v-if="isProcessing">
            <div class="loader">Searching...</div>
        </div>

        <paginator
                v-if="!isProcessing"
                :pagination="pagination"
                :numPageLinks="5"
                :itemTypeName="'Recipes'"
                v-on:pagerequest="containsMaterials"
        ></paginator>

        <div class="table-responsive" v-if="isLoaded && !isProcessing && materialList.length > 0">

            <table class="table table-hover table-sm contains-material-table">
                <thead>
                <tr>
                    <th colspan="2">Recipe</th>
                    <th>△ Temp</th>
                    <th>Percent Amount</th>
                </tr>
                </thead>
                <tbody>
                    <tr class="" v-for="containsMaterial in materialList">
                        <td class="description">
                            <a :href="'/recipes/' + containsMaterial.id">
                                <img class="img-fluid"
                                     :alt="containsMaterial.name"
                                     :src="glazyHelper.getSmallThumbnailUrl(containsMaterial)"
                                     width="72" height="72">
                            </a>
                        </td>
                        <td class="description">
                            <a :href="'/recipes/' + containsMaterial.id">
                                {{ containsMaterial.name }}
                            </a>
                        </td>
                        <td v-html="glazyHelper.getConeString(containsMaterial)">
                        </td>
                        <td v-html="parseFloat(getMaterialAmount(containsMaterial.materialComponents))">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else>
            <h5 v-if="!isProcessing">No recipes containing this material found.</h5>
        </div>

        <paginator
                v-if="!isProcessing"
                :pagination="pagination"
                :numPageLinks="5"
                :itemTypeName="'Recipes'"
                v-on:pagerequest="containsMaterials"
        ></paginator>

    </div>

</template>

<script>

  import Vue from 'vue'

  import Paginator from '../search/Paginator.vue'

  import GlazyHelper from '../helpers/glazy-helper'

  export default {

    name: 'ContainsMaterial',
    components: {
      Paginator
    },
    props: ['material'],
    data() {
      return {
        materialList : null,
        pagination : null,
        glazyHelper: new GlazyHelper(),
        isLoaded : false,
        isProcessing: false
      }
    },
    mounted() {
      this.containsMaterials();
    },
    methods: {
        containsMaterials : function(page_num){
            this.isProcessing = true
            // console.log('Searching for recipes containing material...');

            var form = {
            materials: [this.material.id]
            }

            if (page_num) {
            form.p = page_num
            }

            Vue.axios.post(Vue.axios.defaults.baseURL + '/search/containsMaterials', form)
                .then((response) => {
                this.materialList = response.data.data
                this.pagination = response.data.meta.pagination
                this.isLoaded = true
                this.isProcessing = false
            })
            .catch(response => {
                // Error Handling
                this.isProcessing = false
            })

        },

        getMaterialAmount : function(materialComponents) {
            if (materialComponents) {
            var amount = 0
            materialComponents.forEach(function (component) {
                if (component.material.id === this.material.id) {
                amount = component.percentageAmount
                }
            }.bind(this))
            return amount
            }
        }
    }
  }

</script>
