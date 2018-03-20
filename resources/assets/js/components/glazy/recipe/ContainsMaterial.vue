<template>
    <div id="contains-material">
        <div class="load-container load7" v-if="isProcessing">
            <div class="loader">Searching...</div>
        </div>

        <paginator
                v-if="!isProcessing"
                :pagination="pagination"
                :num_page_links="5"
                :item_type_name="item_type_name"
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
                                     :src="getImageUrl(containsMaterial, 's')"
                                     width="72" height="72">
                            </a>
                        </td>
                        <td class="description">
                            <a :href="'/recipes/' + containsMaterial.id">
                                {{ containsMaterial.name }}
                            </a>
                        </td>
                        <td v-html="coneString(containsMaterial.fromOrtonConeName, containsMaterial.toOrtonConeName)">
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
                :num_page_links="5"
                :item_type_name="item_type_name"
                v-on:pagerequest="containsMaterials"
        ></paginator>

    </div>

</template>

<script>

  import Vue from 'vue'

  import Paginator from '../search/Paginator.vue'

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
        isLoaded : false,
        isProcessing: false
      }
    },
    mounted() {
      console.log('XXX contains-material');
      console.log(this.material);
      this.containsMaterials();
    },
    methods: {

            containsMaterials : function(page_num){
              this.isProcessing = true
              console.log('Searching for recipes containing material...');

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
                console.log('YYYY')
                console.log(this.materialList)
                this.isLoaded = true
                this.isProcessing = false
            })
              .catch(response => {
                // Error Handling
                this.isProcessing = false
            })

            },

            getMaterialAmount : function(materialComponents) {
              console.log('getmaterialamount1')
              if (materialComponents) {
                var amount = 0
                materialComponents.forEach(function (component) {
                  console.log('getmaterialamount2')
                  if (component.material.id === this.material.id) {
                    console.log ('FOUND: ' + component.percentageAmount)
                    amount = component.percentageAmount
                  }
                }.bind(this))
                return amount
              }
            },

            coneString: function(fromOrtonConeName, toOrtonConeName) {
                var coneString = '?';
                if (fromOrtonConeName
                    && toOrtonConeName
                    && fromOrtonConeName != toOrtonConeName) {
                    return fromOrtonConeName + "-" + toOrtonConeName;
                }

                if (fromOrtonConeName) {
                    return coneString = fromOrtonConeName;
                }

                if (toOrtonConeName) {
                    coneString = toOrtonConeName;
                }
                return coneString;
            },

            getImageBin: function(id) {
                id = '' + id;
                return id.substr(id.length - 2);
            },

            hasThumbnail: function(recipe) {
                if (recipe.hasOwnProperty('thumbnail')
                    && recipe.thumbnail.hasOwnProperty('filename')
                    && recipe.thumbnail.filename) {
                    return true;
                }
                return false;
            },

            getImageUrl: function(recipe, size) {
                if (this.hasThumbnail(recipe)) {
                    var bin = this.getImageBin(recipe.id);
                    return '/storage/uploads/recipes/' + bin + '/' + size + '_' + recipe.thumbnail.filename;
                }
                return '/img/recipes/black.png';
            }


    }

  }

</script>
