
<template>

    <div id="similar-base-components">
        <div class="table-responsive" v-if="isLoaded && materialList.length > 1">

            <table class="table table-hover table-sm similar-base-components-table">

                <thead>
                <tr>
                    <th colspan="2">Recipe</th>
                    <th>△ Temp</th>
                    <th>Additional Ingredients</th>
                </tr>
                </thead>
                <tbody>

                    <tr class="" v-for="similar in materialList">
                        <td class="description">
                            <a :href="'/recipes/' + similar.id">
                                <img class="img-fluid"
                                     :alt="similar.name"
                                     :src="getImageUrl(similar, 's')"
                                     width="72" height="72">
                            </a>
                        </td>
                        <td class="description">
                            <a :href="'/recipes/' + similar.id">
                                {{ similar.name }}
                            </a>
                        </td>
                        <td>
                            {{ coneString(similar.fromOrtonConeName, similar.toOrtonConeName) }}
                        </td>
                        <td v-html="getAdditionalComponentsString(similar)">
                        </td>

                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <h5>No similar base recipes found.</h5>
        </div>
    </div>



</template>

<script>

  import Vue from 'vue'

  export default {

      name: 'SimilarBaseComponents',
        props: ['material'],

        data() {
            return {
                materialList : null,
                isLoaded : false
            };
        },

        computed: {
        },

        mounted() {
            console.log('XXX similar-base-components');
            console.log(this.material);
            this.fetchSimilarBaseComponents();
        },

        methods: {

            fetchSimilarBaseComponents : function(){
                console.log('Fetching similar unity formulas...');

              Vue.axios.get(Vue.axios.defaults.baseURL + '/search/similarBaseComponents/'  + this.material.id)
                    .then((response) => {
                        this.materialList = response.data.data;
                        console.log('YYYY')
                        console.log(this.materialList)
                        this.isLoaded = true;
                    })
                    .catch(response => {
                        // Error Handling
                    });
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
                return '/static/img/recipes/black.png';
            },

          getAdditionalComponentsString: function (similar) {
              var tmp = ''

              if (similar.materialComponents) {
                similar.materialComponents.forEach((component) => {
                  if (component.isAdditional) {
                    if (tmp.length) {
                      tmp += ', '
                    }
                    tmp += component.material.name
                  }
                });

              }
              return tmp
          }


      }

    }

</script>
