<template>
<div class="row user-materials">
    <div class="col-md-12">
        <b-alert v-if="apiError" show variant="danger">
            API Error: {{ apiError.message }}
        </b-alert>
        <b-alert v-if="serverError" show variant="danger">
            Server Error: {{ serverError }}
        </b-alert>
        <div class="load-container load7 fullscreen" v-if="isProcessing">
            <div class="loader">Loading...</div>
        </div>
        <b-alert :show="actionMessageSeconds"
                 @dismiss-count-down="actionMessageCountdown"
                 variant="info">
            {{ actionMessage }}
        </b-alert>
    </div>

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="card-title">
                            Materials Inventory
                        </h3>
                        <p>
                            The following materials are available to you for making recipes.
                            You can add more materials by <a href="/materials">searching the Materials section</a>.
                            Removing a material from your inventory does <em>NOT</em> delete it.
                        </p>
                        <p>
                            <em>Soon, this page will allow you to track your materials inventory.</em>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <b-card>
                            <b-media>
                                <a slot="aside" href="/calculator"><i class="fa fa-calculator fa-fw calc-icon-lg"></i>
                                </a>
                                <h5 class="mt-0">Your Calculator Materials</h5>
                                <p>
                                    Note: For simplicity, only materials listed here in your inventory can be used in the
                                    <a href="/calculator">Calculator</a>.
                                </p>
                            </b-media>
                        </b-card>
                    </div>
                </div>
                <table v-if="!isProcessing && inventoryMaterials && inventoryMaterials.length > 0"
                       class="table table-hover table-sm">
                    <thead>
                        <th colspan="2">Material</th>
                        <th>Author</th>
                        <!--
                        <th>Amount in Stock</th>
                        <th>Price</th>
                        <th>Vendor</th>
                        <th>Vendor Code</th>
                        -->
                        <th>Remove from Inventory</th>
                    </thead>
                    <tbody>
                    <tr class="col-md-12" v-for="(inventory, index) in inventoryMaterials" v-if="'material' in inventory">
                        <td>
                            <router-link :to="{ name: 'material', params: { id: inventory.material.id }}">
                                <img class="img-fluid"
                                     :alt="inventory.material.name"
                                     :src="getImageUrl(inventory.material, 's')"
                                     width="40" height="40">
                            </router-link>
                        </td>
                        <td>
                            <router-link :to="{ name: 'material', params: { id: inventory.material.id }}">
                                <i v-if="inventory.material.isPrivate"
                                   v-b-tooltip.hover title="Private"
                                   class="fa fa-eye-slash"></i>
                                <i v-if="inventory.material.isArchived"
                                   v-b-tooltip.hover title="Archived"
                                   class="fa fa-lock"></i>
                                {{ inventory.material.name }}<em v-if="inventory.material.otherNames">, {{ inventory.material.otherNames }}</em>
                            </router-link>
                        </td>
                        <td>
                            <div class="author">
                                <router-link :to="{ name: 'user', params: { id: getUserSearchParam(inventory.material.createdByUser) }}">
                                    <img v-if="'profile' in inventory.material.createdByUser && 'avatar' in inventory.material.createdByUser.profile"
                                         v-bind:src="inventory.material.createdByUser.profile.avatar"
                                         class="avatar"/>
                                    <span>{{ inventory.material.createdByUser.name }}</span>
                                </router-link>
                            </div>
                        </td>
                        <!--
                        <td>
                            {{ inventory.stockAmount }}
                        </td>
                        <td>
                            {{ inventory.price }}
                        </td>
                        <td>
                            {{ inventory.vendor }}
                        </td>
                        <td>
                            {{ inventory.vendorCode }}
                        </td>
                        -->
                        <td>
                            <b-button v-b-tooltip.hover title="Remove from Inventory"
                                   @click="removeMaterialRequest(inventory.id)"
                                   class="btn btn-icon btn-default"><i class="fa fa-times"></i></b-button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
</template>


<script>
  export default {
    props: {
    },
    components: {
    },
    data() {
      return {
        user: null,
        inventoryMaterials: null,
        errors: [],
        apiError: null,
        serverError: null,
        isProcessing: false,
        actionMessage: null,
        actionMessageSeconds: 0
      }
    },
    created() {
      if (this.$auth.check()) {
        this.user = this.$auth.user()
      }
      this.getInventoryMaterials()
    },
    computed: {
      isLoaded: function () {
        if (this.$auth.check()) {
          return true;
        }
        return false;
      }
    },
    methods: {

      getInventoryMaterials: function () {
        if (this.isLoaded) {
          this.isProcessing = true
          this.serverError = null
          Vue.axios.get(Vue.axios.defaults.baseURL + '/usermaterials')
            .then((response) => {
            console.log('got response:')
            console.log(response)
            if (response.data.error) {
              // error
              this.apiError = response.data.error
              console.log(this.apiError)
            } else {
              // got results
              if (response.data.data) {
                this.inventoryMaterials = response.data.data
              }
            }
            this.isProcessing = false
          })
          .catch(response => {
            this.serverError = response
            this.isProcessing = false
            console.log('Get user materials ERROR')
            console.log(response.data)
          })
        }
      },

      removeMaterialRequest(inventoryId) {
        if (!inventoryId) {
          return
        }
        this.isProcessing = true
        Vue.axios.delete(Vue.axios.defaults.baseURL + '/usermaterials/' + inventoryId)
          .then((response) => {
          if (response.data.error) {
            this.apiError = response.data.error
            console.log(this.apiError)
            this.isProcessing = false
          } else {
            this.actionMessage = 'Removed material from inventory.'
            this.actionMessageSeconds = 5
            this.getInventoryMaterials()
            console.log('refresh user materials')
            // Refresh user inventory materials
            this.$auth.fetch({
              success(res) {
                console.log('success fetching user');
                console.log(this.$auth.user())
                console.log('user id: ' + this.$auth.user().id)
                // Refresh the recipe
                this.fetchRecipe()
              },
              error() {
                console.log('error fetching user');
              }
            })
          }
        })
        .catch(response => {
          this.serverError = response
          console.log(response)
          this.isProcessing = false
        })
      },

      getUserSearchParam: function (user) {
        if (!user) { return }
        if ('profile' in user && 'username' in user.profile && user.profile.username) {
          return user.profile.username
        }
        return user.id
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
      },

      actionMessageCountdown(seconds) {
        this.actionMessageSeconds = seconds
      }

    }
  }
</script>

<style>
    .user-materials {
        padding-top: 15px;
    }

    .calc-icon-lg {
        font-size: 42px;
    }
</style>