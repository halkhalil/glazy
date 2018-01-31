<template>
    <main role="main" class="ml-sm-auto">
      <div class="row">
        <div class="col-lg-12">
          <div class="card mt-4">
            <div class="card-body">
              <edit-material-metadata :material="material"
                                    :isNewMaterial="true"
                                    v-on:updatedRecipeMeta="updatedRecipeMeta"
                                    v-on:editMetaCancel="editMetaCancel"
                                    v-on:isProcessing="isProcessingRecipe"></edit-material-metadata>
            </div>
          </div>
        </div>
      </div>
    </main>
</template>

<script>
  import Material from 'ceramicscalc-js/src/material/Material'

  import EditMaterialMetadata from '../components/glazy/recipe/EditMaterialMetadata.vue'

  import Vue from 'vue'

  export default {
    name: 'MaterialCreate',
    metaInfo () {
      return {
        title: 'Glazy: Add Material',
        meta: [
          {
            'vmid': "description",
            'property': 'description',
            'content': 'Create a new material for use in making ceramic glazes and clay bodies.'
          },
          {
            'property': 'og:description',
            'content': 'Create a new material for use in making ceramic glazes and clay bodies.'
          },
          {
            'property': 'og:title',
            'content': 'Add Material'
          },
          {
            'property': 'og:url',
            'content': GLAZY_APP_URL + this.$route.fullPath
          }
        ]
      }
    },
    components: {
      EditMaterialMetadata
    },
    props: {
      current_user: {
        type: Object,
        default: null
      }
    },
    data() {
      return {
        material: new Material(),
        isProcessing: false,
        apiError: null,
        serverError: null,
        actionMessage: null,
        actionMessageSeconds: 0
      }
    },

    mounted() {
    },

    computed : {
      isLoaded: function() {
        return true;
      }
    },
    methods : {
      actionMessageCountdown(seconds) {
        this.actionMessageSeconds = seconds
      },
      updatedRecipeMeta: function () {
        this.$router.push({ name: 'user-materials' })
        // TODO
        // this.$router.push({ name: 'material', params: { id: recipeCopy.id }})
      },
      editMetaCancel: function () {
        this.$router.push({ name: 'user-materials' })
      },
      isProcessingRecipe: function () {
        this.isProcessing = true
      }
    }
  }
</script>


<style>

</style>