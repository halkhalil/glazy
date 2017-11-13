<template>

<form class="form-horizontal" role="form" v-on:submit.prevent v-if="isLoaded">

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
                <small v-if="errors.imageFile" class="form-control-feedback">
                    <span v-for="error in errors.imageFile">{{ error }}</span>
                </small>
                </span>
            </div>
        </div>
        <div class="col-sm-12">
            <b-form-group
                    :class="{'has-danger': errors.title}"
                    id="photoTitle"
            >
                <b-form-input
                        id="photoTitleInput"
                        v-model.trim="form.title"
                        placeholder="Photo Caption"
                        type="text"
                ></b-form-input>
            </b-form-group>

            <b-form-group
                    :class="{'has-danger': errors.description}"
                    id="photoDescription"
            >
                <b-form-textarea id="photoDescriptionTextarea"
                                 v-model="form.description"
                                 placeholder="Optional description. Firing conditions, recipe changes, etc."
                                 :rows="3"
                                 :max-rows="6">
                </b-form-textarea>
            </b-form-group>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 text-right pt-5">
            <div class="form-group">
                <button v-if="files"
                        class="btn btn-info btn-sm"
                        @click.prevent="upload">
                    <i class="fa fa-cloud-upload"></i> Upload Image
                </button>

                <button class="btn btn-cancel btn-sm"
                        @click.prevent="cancelEdit">
                    Cancel
                </button>

            </div>
        </div>
    </div>

</form>

</template>

<script>

export default {

  name: 'UploadMaterialImageForm',
  props: {
    material: {
      type: Object,
      default: null
    },
  },
  data() {
    return {
      errors: {},
      hasErrors: false,
      isProcessing: false,
      files: null,
      image: '',
      maxUploadSizeMB: 6,
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
    },
    form: function () {
      var form = {};
      if (this.isLoaded) {
        form = {
          materialId: this.material.id,
          title: '',
          description: ''
        }
      }
      return form;
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
      formData.append('imageFile', this.files[0]);

      Vue.axios.post(Vue.axios.defaults.baseURL + '/materialimages', formData)
        .then((response) => {
        if (response.data.error) {
          this.apiError = response.data.error
          this.isProcessing = true
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
      if (!this.hasExtension(['.jpg', '.gif', '.png'])) {
        this.files = null;
        this.errors.imageFile = ['Image file type not supported.'];
        this.hasErrors = true;
      }
      else if (!this.checkFileSize()) {
        this.errors.imageFile = ['File size must be less than ' + this.maxUploadSizeMB + 'MB.'];
        this.hasErrors = true;
      }
      else {
        // TODO: Refactor errors handling
        this.errors.imageFile = {};
        this.hasErrors = false;
        this.createImage(this.files[0]);
      }
    },

    createImage: function (file) {
      var image = new Image();
      var reader = new FileReader();
      var vm = this;

      reader.onload = (e) => {
        vm.image = e.target.result;
      }
      reader.readAsDataURL(file);
    },

    resetForm: function () {
      this.image = '';
      this.files = null;
      this.form.title = null;
      this.form.description = null;
    },

    removeImage: function (e) {
      this.image = '';
      this.files = null;
    },

    cancelEdit: function () {
      this.resetForm();
      this.$emit('imageuploadcancel');
    },

    hasExtension: function (exts) {
      // http://stackoverflow.com/questions/4234589/validation-of-file-extension-before-uploading-file
      if (!this.files.length) {
        return false;
      }
      return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$', "i")).test(this.files[0].name);
    },

    checkFileSize: function () {
      console.log('FILE SIZE: ' + (this.files[0].size / 1048576));
      if (!this.files.length) {
        return false;
      }
      if ((this.files[0].size / 1048576) > this.maxUploadSizeMB) {
        return false;
      }
      return true;
    }
  }
}

</script>
