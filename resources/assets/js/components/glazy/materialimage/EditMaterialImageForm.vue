<template>

    <form role="form" v-on:submit.prevent v-if="isLoaded">

        <div class="load-container load7" v-if="isProcessing">
            <div class="loader">Uploading...</div>
        </div>

        <div class="alert alert-danger" v-if="hasErrors">
            Errors were found in the form below.
        </div>

        <div class="row" v-if="!isProcessing">
            <div v-if="image" class="col-md-6 offset-md-3 col-sm-8 offset-sm-2">
                <img class="img-fluid" :src="imageUrl" />
            </div>
            <b-form-group
                    class="col-sm-12"
                    :class="{'has-danger': errors.title}"
                    id="photoTitle">
                <b-form-input
                        id="photoTitleInput"
                        v-model.trim="myImage.title"
                        placeholder="Photo Caption"
                        type="text"
                ></b-form-input>
            </b-form-group>
            <b-form-group
                    class="col-sm-12"
                    :class="{'has-danger': errors.description}"
                    id="photoDescription">
                <b-textarea id="photoDescriptionTextarea"
                                 v-model="myImage.description"
                                 placeholder="Optional description. Firing conditions, recipe changes, etc."
                                 :rows="3"
                                 :max-rows="6">
                </b-textarea>
            </b-form-group>
            <b-form-group class="col-md-6 col-sm-4" id="imageCone">
                <b-form-select
                        id="ortonConeId"
                        v-model="myImage.ortonConeId"
                        :options="constants.ORTON_CONES_SELECT_TEXT_SIMPLE">
                    <template slot="first">
                        <option :value="0">Temperature</option>
                    </template>
                </b-form-select>
            </b-form-group>
            <b-form-group class="col-md-6 col-sm-8" id="imageAtmosphere">
                <b-form-select
                        id="atmosphereId"
                        v-model="myImage.atmosphereId"
                        :options="constants.ATMOSPHERE_SELECT">
                    <template slot="first">
                        <option :value="0">Atmosphere</option>
                    </template>
                </b-form-select>
            </b-form-group>
            <div class="form-group col-sm-12 text-right">
                <button class="btn btn-cancel btn-sm"
                        @click.prevent="cancelEdit">
                    Cancel
                </button>
                <button class="btn btn-info btn-sm"
                        @click.prevent="update">
                    <i class="fa fa-save"></i> Update Image
                </button>
            </div>
        </div>
    </form>

</template>

<script>

  import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'

  export default {
    name: 'UploadMaterialImageForm',
    props: {
      image: {
        type: Object,
        default: null
      },
      imageUrl: {
        type: String,
        default: null
      }
    },
    data() {
      return {
        myImage: Object.assign({}, this.image),
        errors: {},
        hasErrors: false,
        isProcessing: false,
        constants: new GlazyConstants(),
        files: null,
        maxUploadSizeMB: 6,
        apiError: null,
        serverError: null
      }
    },
    watch:{
      image(newImage){
        this.myImage = Object.assign({}, newImage)
      }
    },
    computed: {
      isLoaded: function () {
        if (this.image && this.imageUrl) {
          return true;
        }
        return false;
      }
    },
    methods: {

      update: function () {
        this.isProcessing = true;
        var form = {
          _method: 'PATCH',
          id: this.myImage.id,
          title: this.myImage.title,
          description: this.myImage.description,
          ortonConeId: this.myImage.ortonConeId,
          atmosphereId: this.myImage.atmosphereId
        }
        Vue.axios.post(Vue.axios.defaults.baseURL + '/materialimages/' + this.image.id, form)
          .then((response) => {
          if (response.data.error) {
            this.apiError = response.data.error
            this.isProcessing = false
            console.log(this.apiError)
          }
          else {
            this.isProcessing = false;
            this.$emit('imageupdated');
          }
        }).catch(response => {
          this.serverError = response;
          this.isProcessing = false
        })
      },

      cancelEdit: function () {
        this.$emit('imageupdatecancel');
      }
    }
  }

</script>
