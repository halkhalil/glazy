
<template>

    <div id="image-gallery">

        <div class="row">
            <div class="col-md-12" v-if="currentImage">
                <div class="card image-gallery-card text-center">
                    <a @click.stop.prevent="lightbox()" href="#">
                        <img class="card-img-top img-fluid"
                             :src="getImageUrl(currentImage.filename, 'm')"
                             :alt="currentImage.title">
                    </a>

                    <div class="btn-previous-image" v-if="imageList.length > 1">
                        <b-button
                                @click.stop.prevent="previousImage()"
                                class="gallery-nav-button">
                            <i class="fa fa-chevron-left"></i>
                        </b-button>
                    </div>
                    <div class="btn-next-image" v-if="imageList.length > 1">
                        <b-button
                                @click.stop.prevent="nextImage()"
                                class="gallery-nav-button">
                            <i class="fa fa-chevron-right"></i>
                        </b-button>
                    </div>

                    <div v-if="current_user" class="card-block gallery-actions">
                        <a v-if="(current_user.id == material.createdByUserId) && (currentImage.id != material.thumbnail_id)"
                           @click.stop.prevent="setThumbnail()"
                            href="#" role="button" class="btn btn-primary btn-float btn-sm">
                            <i class="fa fa-thumb-tack"></i>
                        </a>
                        <button v-else-if="(current_user.id == material.createdByUserId) && (currentImage.id == material.thumbnail_id)"
                           class="btn btn-outline-default btn-float btn-sm" disabled>
                            <i class="fa fa-thumb-tack"></i>
                        </button>
                        <a v-if="(current_user.id == currentImage.createdByUserId)"
                                @click.stop.prevent="imageUpdate()"
                                href="#" role="button" class="btn btn-primary btn-float btn-sm">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a v-if="(current_user.id == currentImage.createdByUserId)"
                           @click.stop.prevent="confirmDelete()"
                            href="#" role="button" class="btn btn-danger btn-float btn-sm">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>

                    <div class="gallery-swatches text-right">
                        <a v-if="currentImage.dominant_hex_color"
                           role="button" class="btn btn-primary btn-float btn-sm"
                           :href="'/search?hex_color=' + currentImage.dominant_hex_color"
                           :style="'background-color: #' + currentImage.dominant_hex_color">
                            <i class="fa fa-eyedropper"></i>
                        </a>
                        <a v-if="currentImage.secondary_hex_color"
                           role="button" class="btn btn-primary btn-float btn-sm"
                           :href="'/search?hex_color=' + currentImage.secondary_hex_color"
                           :style="'background-color: #' + currentImage.secondary_hex_color">
                            <i class="fa fa-eyedropper"></i>
                        </a>
                    </div>
                    <div v-if="currentImage.title || currentImage.description"
                            class="card-body">
                        <p class="card-title" v-if="currentImage.title">
                            {{ currentImage.title }}
                        </h5>
                        <p class="card-text" v-if="currentImage.description">
                            {{ currentImage.description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" v-if="imageList.length > 1">
            <div class="col-md-4 col-sm-6 image-gallery-thumb" v-for="image in imageList">
                <a @click.stop.prevent="selectImage(image)"
                   :class="{ galleryselected: (image.id == currentImage.id) }"
                   href="#">
                    <img class="rounded img-raised"
                         :src="getImageUrl(image.filename, 's')"
                         :alt="image.title">
                </a>
            </div>
        </div>

        <div v-if="$auth.check()" class="row">
            <div class="col-sm-12">
                <button type="button" class="btn btn-info btn-block btn-sm"  v-on:click="imageUpload()">
                    <i class="fa fa-camera-retro"></i> Upload Photo
                </button>
            </div>
        </div>

        <!-- Add Image Modal -->
        <b-modal ref="addImageModal" id="addImageModal" title="Add Photo">
            <upload-material-image-form
                    :material="material"
                    v-on:imageuploadcancel="imageUploadCancel"
                    v-on:imageuploaded="imageUploaded">
            </upload-material-image-form>
            <div slot="modal-footer" class="w-100">
            </div>
        </b-modal>

        <!-- Edit Image Modal -->
        <div class="modal fade" id="updateImageModal" tabindex="-1" role="dialog" aria-labelledby="update image" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Image</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <edit-material-image-form
                                :image="currentImage"
                                v-on:imageupdatecancel="imageUpdateCancel"
                                v-on:imageupdated="imageUpdated">
                        </edit-material-image-form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirm Delete Modal -->
        <div class="modal fade" id="deleteImageModal" tabindex="-1" role="dialog" aria-labelledby="delete image" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Are you sure?</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Once deleted, you will not be able to retrieve this image!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger"
                                @click.stop.prevent="deleteImage()">Confirm Delete</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lightbox Modal -->
        <div v-if="currentImage" class="modal fade" id="lightboxModal" tabindex="-1" role="dialog" aria-labelledby="image lightbox" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" v-if="currentImage.title">
                            {{ currentImage.title }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p v-if="currentImage.description">
                            {{ currentImage.description }}
                        </p>
                        <img class="img-fluid"
                             :src="getImageUrl(currentImage.filename, 'l')"
                             :alt="currentImage.title">
                    </div>
                </div>
            </div>
        </div>
    </div>



</template>
<script>

  import Vue from 'vue'

  import UploadMaterialImageForm from './UploadMaterialImageForm.vue'

  export default {

    name: 'MaterialImageGallery',

    components: {
      UploadMaterialImageForm
    },

    props: ['current_user', 'material'],

    data() {
      return {
        STORAGE_BASE_URL: 'http://homestead.app',
        selectedImage: null,
        dropzoneOptions: {
          url: 'https://httpbin.org/post',
          thumbnailWidth: 150,
          maxFilesize: 0.5,
          headers: { "My-Awesome-Header": "header value" },
          dictDefaultMessage: "<i class='fa fa-cloud-upload'></i> Upload Photo"
        },
        photoForm: {
          title: null
        }
      };
    },

    computed: {

      isLoaded: function () {
        if (this.material) {
          return true;
        }
        return false;
      },

      imageList: function () {

        if (this.isLoaded) {
          if (this.material.hasOwnProperty('images')) {
            return this.material.images;
          }
        }

        return [];
      },

      thumbnail: function () {
        if (this.isLoaded) {
          if (this.material.hasOwnProperty('thumbnail')) {
            return this.material.thumbnail;
          }
          if (this.imageList) {
            if (this.imageList.length > 0) {
              return this.imageList[0];
            }
          }
        }
        return null;
      },

      currentImage: function () {
        if (this.selectedImage) {
          return this.selectedImage;
        }
        else if (this.thumbnail) {
          return this.thumbnail;
        }
        return null;
      }

    },

    methods: {

      imageUpload: function () {
        //$('#addImageModal').modal('show');
        this.$refs.addImageModal.show();
      },

      imageUploadCancel: function () {
        $('#addImageModal').modal('hide');
      },

      imageUploaded: function () {
        this.$refs.addImageModal.hide()
        this.$emit('imageupdated');
      },

      imageUpdate: function () {
        $('#updateImageModal').modal('show');
      },

      imageUpdateCancel: function () {
        $('#updateImageModal').modal('hide');
      },

      imageUpdated: function () {
        $('#updateImageModal').modal('hide');
        this.$emit('imageupdated');
      },

      getImageBin: function (id) {
        id = '' + id;
        console.log("IMAGE BIN: " + id.substr(id.length - 2))
        return id.substr(id.length - 2);
      },

      getImageUrl: function (filename, size) {
        var bin = this.getImageBin(this.material.id);

        return this.STORAGE_BASE_URL + '/storage/uploads/recipes/' + bin + '/' + size + '_' + filename;
      },

      selectImage: function (image) {
        this.selectedImage = image;
      },

      lightbox: function () {
        $('#lightboxModal').modal('show');
      },

      confirmDelete: function () {
        $('#deleteImageModal').modal('show');
      },
        /*
         cancelDelete: function() {
         $('#deleteImageModal').modal('hide');
         },
         */

      deleteImage: function() {
        if (!this.currentImage) {
          return;
        }
        $('#deleteImageModal').modal('hide');
//                $('#deleteMaterialModal').modal('hide');
        axios.delete('/api/v1/materialimages/' + this.currentImage.id)
          .then((response) => {
          this.material = null;
        this.selectedImage = null;
        this.$emit('imageupdated');
//                        $('#materialDeletedModal').modal('show');
//                        window.setTimeout(function () {
//                            this.isDeleted = true;
//                            window.location = '/search';
//                        }, 2000);
      })
        .catch(response => {
          // Error Handling
        });
      },

      setThumbnail: function() {
        if (!this.currentImage) {
          return;
        }
        axios.get('/api/v1/recipes/' + this.material.id + '/image/' + this.currentImage.id)
          .then((response) => {
          this.$emit('imageupdated');
      })
        .catch(response => {
          // Error Handling
        });
      },

      previousImage: function () {
        for (var i = 0; i < this.imageList.length; i++) {
          if (this.imageList[i].id == this.currentImage.id) {
            if (i == 0) {
              this.selectImage(this.imageList[this.imageList.length - 1]);
              return;
            }
            else {
              this.selectImage(this.imageList[i - 1]);
              return;
            }
          }
        }
      },

      nextImage: function () {
        for (var i = 0; i < this.imageList.length; i++) {
          if (this.imageList[i].id == this.currentImage.id) {
            if (i == this.imageList.length - 1) {
              this.selectImage(this.imageList[0]);
              return;
            }
            else {
              this.selectImage(this.imageList[i + 1]);
              return;
            }
          }
        }
      }

    }

  }

    /*

     "images": [
     {
     "id": 1,
     "materialId": 102,
     "title": "",
     "description": "",
     "dominant_rgb_r": 68,
     "dominant_rgb_g": 69,
     "dominant_rgb_b": 71,
     "dominant_hex_color": "444547",
     "secondary_rgb_r": 182,
     "secondary_rgb_g": 178,
     "secondary_rgb_b": 171,
     "secondary_hex_color": "b6b2ab",
     "url": "102.588f065774ad3.jpg",
     "is_private": false,
     "createdByUserId": null,
     "created_at": {
     "date": "2017-01-30 09:24:40.000000",
     "timezone_type": 3,
     "timezone": "UTC"
     },
     "updated_at": {
     "date": "2017-01-30 09:24:40.000000",
     "timezone_type": 3,
     "timezone": "UTC"
     }
     },
     {
     "id": 2,
     "materialId": 102,
     "title": "",
     "description": "",
     "dominant_rgb_r": 68,
     "dominant_rgb_g": 69,
     "dominant_rgb_b": 71,
     "dominant_hex_color": "444547",
     "secondary_rgb_r": 182,
     "secondary_rgb_g": 178,
     "secondary_rgb_b": 171,
     "secondary_hex_color": "b6b2ab",
     "url": "102.588f0681eccc6.jpg",
     "is_private": false,
     "createdByUserId": null,
     "created_at": {
     "date": "2017-01-30 09:25:23.000000",
     "timezone_type": 3,
     "timezone": "UTC"
     },
     "updated_at": {
     "date": "2017-01-30 09:25:23.000000",
     "timezone_type": 3,
     "timezone": "UTC"
     }
     }
     */

</script>

<style>


    .gallery-nav-button {
        color: #ffffff !important;
        font-size: 1.25rem;
        line-height: 1.25rem;
        padding:0.5rem;
        background-color: rgba(0,0,0,0.4) !important;
    }

    .btn-previous-image {
        position: absolute;
        display: block;
        top: 50%;
        left: 1rem;
    }

    .btn-next-image {
        position: absolute;
        display: block;
        top: 50%;
        right: 1rem;
    }

    .image-gallery-card {
        margin-bottom: 10px;
    }
    .image-gallery-card .card-body {
        padding: 10px;
    }

    .image-gallery-thumb {
        padding: 0 10px;
    }

    /**/

    .glazy-gallery-thumbs {
    }

    .gallery-actions {
        margin-top: -4.5rem;
    }

    .gallery-swatches {
        position: absolute;
        top: 0;
        width: 100%;
    }

    .galleryselected {
        opacity: 0.5;
        filter: alpha(opacity=50);
    }

    .btn-upload-image {
    //  background-color: blue;
        position:absolute;
        width:100%;
        height:100%;
        left:0;
        top:0;
        margin-right: .25rem;
        margin-left: .25rem;
        margin-top: .5rem;
        font-size: 3rem;
    }

    .upload-thumbnail {
    //  display: inline;
        width:100%;
        height:100%;
    }

</style>