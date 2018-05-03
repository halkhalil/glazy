<template>
    <main role="main" class="ml-sm-auto">
      <div class="row">
        <div class="col-lg-12">
          <div class="card mt-4">
            <div class="card-body">
              <edit-material-metadata
                      :material="material"
                      :isNewMaterial="isNewMaterial"
                      :isNewAnalysis="isNewAnalysis"
                      v-on:updatedRecipeMeta="updatedRecipeMeta"
                      v-on:editMetaCancel="editMetaCancel"
                      v-on:isProcessing="isProcessingRecipe"
              ></edit-material-metadata>
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
        actionMessageSeconds: 0,
        isNewMaterial: false,
        isNewAnalysis: false
      }
    },

    created() {

      if (this.$route.name === 'material-create') {
        this.isNewMaterial = true
        this.isNewAnalysis = false
        this.material.isPrimitive = true
        this.material.isAnalysis = false
      }
      else {
        this.isNewMaterial = false
        this.isNewAnalysis = true
        this.material.isAnalysis = true
        this.material.isPrimitive = false
      }

    },

    watch: {
      $route (route) {
        this.material = new Material()
        if (route.name === 'material-create') {
          this.isNewMaterial = true
          this.isNewAnalysis = false
          this.material.isPrimitive = true
          this.material.isAnalysis = false
        }
        else {
          this.isNewMaterial = false
          this.isNewAnalysis = true
          this.material.isAnalysis = true
          this.material.isPrimitive = false
        }
      }
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
      updatedRecipeMeta: function (id, recipeType) {
        this.$router.push({ name: recipeType, params: { id: id }})
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