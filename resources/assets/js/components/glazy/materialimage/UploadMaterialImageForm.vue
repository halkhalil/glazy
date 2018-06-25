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
            <img class="img-fluid" :src="image" />
            <button class="btn btn-block btn-default btn-sm" @click="removeImage"><i class="fa fa-minus"></i> Remove</button>
        </div>
        <div v-else class="col-md-12">
            <div class="form-group" :class="{'has-danger': errors.imageFile}">
                <label for="imageFile">Image File (.jpg, .gif, or .png)</label>
                <input @change="onFileChange" type="file" id="imageFile"
                       name="imageFile" class="form-control-file"
                       aria-describedby="fileHelp">
                <div v-if="errors.imageFile" class="form-control-feedback">
                    <span v-for="error in errors.imageFile">{{ error }}</span>
                </div>
            </div>
        </div>
        <b-form-group
                class="col-sm-12"
                :class="{'has-danger': errors.title}"
                id="photoTitle">
            <b-form-input
                    id="photoTitleInput"
                    v-model.trim="form.title"
                    placeholder="Photo Caption"
                    type="text"
            ></b-form-input>
        </b-form-group>
        <b-form-group
                class="col-sm-12"
                :class="{'has-danger': errors.description}"
                id="photoDescription">
            <b-form-textarea id="photoDescriptionTextarea"
                             v-model="form.description"
                             placeholder="Optional description. Firing conditions, recipe changes, etc."
                             :rows="3"
                             :max-rows="6">
            </b-form-textarea>
        </b-form-group>
        <b-form-group class="col-md-6 col-sm-4" id="imageCone">
            <b-form-select
                    id="ortonConeId"
                    v-model="form.ortonConeId"
                    :options="constants.ORTON_CONES_SELECT_TEXT_SIMPLE">
                <template slot="first">
                    <option :value="0">Temperature</option>
                </template>
            </b-form-select>
        </b-form-group>

        <b-form-group class="col-md-6 col-sm-8" id="imageAtmosphere">
            <b-form-select
                    id="atmosphereId"
                    v-model="form.atmosphereId"
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
            <button v-if="files && !hasErrors"
                    class="btn btn-info btn-sm"
                    @click.prevent="upload">
                <i class="fa fa-cloud-upload"></i> Upload Image
            </button>
        </div>
    </div>
</form>

</template>

<script>

import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'
import GlazyHelper from '../helpers/glazy-helper'

export default {
  name: 'UploadMaterialImageForm',
  props: {
    material: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      form: {
        materialId: this.material.id,
        title: '',
        description: '',
        ortonConeId: 0,
        atmosphereId: 0
      },
      errors: {},
      hasErrors: false,
      isProcessing: false,
      constants: GlazyConstants,
      files: null,
      image: '',
      glazyHelper: new GlazyHelper(),
      apiError: null,
      serverError: null
    }
  },
  computed: {
    isLoaded: function () {
      if (this.material) {
        return true;
      }
      return false;
    }
  },

  methods: {

    upload: function (e) {
      e.preventDefault();

      var self = this;

      this.isProcessing = true;
      var formData = new FormData();
      formData.append('materialId', this.form.materialId);
      formData.append('title', this.form.title);
      formData.append('description', this.form.description);
      formData.append('ortonConeId', this.form.ortonConeId);
      formData.append('atmosphereId', this.form.atmosphereId);
      formData.append('imageFile', this.files[0]);

      Vue.axios.post(Vue.axios.defaults.baseURL + '/materialimages', formData)
        .then((response) => {
        if (response.data.error) {
          this.apiError = response.data.error
          this.isProcessing = false
          console.log(this.apiError)
        }
        else {
          this.isProcessing = false;
          this.resetForm();
          this.$emit('imageuploaded');
        }
      }).catch(response => {
        this.serverError = response;
        this.isProcessing = false
      })
    },

    onFileChange(e) {
      this.files = e.target.files || e.dataTransfer.files;
      if (!this.files.length)
        return;
      if (!this.glazyHelper.hasImageFileExtension(this.files[0])) {
        this.image = ''
        this.files = null;
        this.errors.imageFile = ['Image file type not supported.'];
        this.hasErrors = true;
      }
      else if (!this.glazyHelper.isUnderMaxFileSize(this.files[0])) {
        this.image = ''
        this.files = null;
        this.errors.imageFile = ['File size must be less than ' + GlazyHelper.MAX_UPLOAD_SIZE_MB + 'MB.'];
        this.hasErrors = true;
      }
      else {
        // TODO: Refactor errors handling
        this.errors.imageFile = {};
        this.hasErrors = false;
        this.createImage(this.files[0]);
      }
    },

    resetForm: function () {
      this.image = ''
      this.files = null
      this.form.title = ''
      this.form.description = ''
      this.form.ortonConeId = 0
      this.form.atmosphereId = 0
    },

    createImage: function (file) {
      var reader = new FileReader();
      var vm = this;

      reader.onload = (e) => {
        vm.image = e.target.result;
      }
      reader.readAsDataURL(file);
    },

    removeImage: function (e) {
      this.image = '';
      this.files = null;
    },

    cancelEdit: function () {
      this.resetForm();
      this.$emit('imageuploadcancel');
    }
  }
}

</script>
