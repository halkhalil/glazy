
<template>
    <div v-if="isLoaded" id="image-gallery">
        <div class="row">
            <div class="col-md-12" v-if="currentImage">
                <div class="card image-gallery-card text-center">
                    <div class="card-image">
                        <a @click.stop.prevent="lightbox()" href="#">
                            <img class="img rounded"
                                 :src="getImageUrl(currentImage.filename, 'm')"
                                 :alt="currentImage.title">
                        </a>

                        <div v-if="currentUser" class="gallery-actions">
                            <b-button v-if="(currentUser.id == material.createdByUserId) && (currentImage.id !== material.thumbnailId)"
                                      @click.stop.prevent="setThumbnail()"
                                      v-b-tooltip.hover
                                      title="Set as recipe thumbnail"
                                      class="btn btn-info btn-icon btn-round"
                                      type="button">
                                <i class="fa fa-thumb-tack"></i>
                            </b-button>
                            <b-button v-else-if="(currentUser.id == material.createdByUserId) && (currentImage.id === material.thumbnailId)"
                                      class="btn btn-default btn-icon btn-round"
                                      disabled
                                      type="button">
                                <i class="fa fa-thumb-tack"></i>
                            </b-button>

                            <b-button v-if="(currentUser.id == currentImage.createdByUserId)"
                                      @click.stop.prevent="imageUpdate()"
                                      v-b-tooltip.hover
                                      title="Edit Image"
                                      class="btn btn-info btn-icon btn-round"
                                      type="button">
                                <i class="fa fa-edit"></i>
                            </b-button>
                            <b-button v-if="(currentUser.id == currentImage.createdByUserId)"
                                      @click.stop.prevent="confirmDelete()"
                                      v-b-tooltip.hover
                                      title="Delete Image"
                                      class="btn btn-danger btn-icon btn-round"
                                      type="button">
                                <i class="fa fa-times"></i>
                            </b-button>
                        </div>
                    </div>
                    <!--
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
                    -->

                    <div class="gallery-swatches">
                        <a v-if="currentImage.dominantHexColor"
                           role="button" class="btn"
                           :href="'/search?hex_color=' + currentImage.dominantHexColor"
                           :style="'background-color: #' + currentImage.dominantHexColor">
                        </a>
                        <a v-if="currentImage.secondaryHexColor"
                           role="button" class="btn"
                           :href="'/search?hex_color=' + currentImage.secondaryHexColor"
                           :style="'background-color: #' + currentImage.secondaryHexColor">
                        </a>
                    </div>

                    <ul v-if="currentImage.ortonConeId || currentImage.atmosphereId"
                        class="list-group list-group-flush list-group-cone">
                        <li class="list-group-item">
                            <span v-if="currentImage.ortonConeId"
                                  v-html="'&#9651;' + constants.ORTON_CONES_LOOKUP[currentImage.ortonConeId]">
                            </span>
                            <span v-if="currentImage.atmosphereId">
                                {{ constants.ATMOSPHERE_LOOKUP[currentImage.atmosphereId] }}
                            </span>
                        </li>
                    </ul>

                    <div v-if="currentImage.title || currentImage.description"
                            class="card-body">
                        <h6 class="card-title" v-if="currentImage.title">
                            {{ currentImage.title }}
                        </h6>
                        <p class="card-text" v-if="currentImage.description">
                            {{ currentImage.description }}
                        </p>
                        <p>
                            <router-link :to="{ name: 'user', params: { id: currentImage.createdByUser.id}}">
                                <div class="author">
                                    <img v-if="'profile' in currentImage.createdByUser && 'avatar' in currentImage.createdByUser.profile"
                                         v-bind:src="currentImage.createdByUser.profile.avatar"
                                         class="avatar"/>
                                    <span>
                                        {{ currentImage.createdByUser.name }},
                                        <timeago :since="currentImage.updatedAt"></timeago>
                                    </span>
                                </div>
                            </router-link>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="currentImage && imageList.length > 1" class="row">
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
        <b-modal ref="addImageModal" hide-footer id="addImageModal" title="Add Photo">
            <upload-material-image-form
                    :material="material"
                    v-on:imageuploadcancel="imageUploadCancel"
                    v-on:imageuploaded="imageUploaded">
            </upload-material-image-form>
        </b-modal>


        <!-- Edit Image Modal -->
        <b-modal v-if="currentImage" ref="updateImageModal" hide-footer id="updateImageModal" title="Update Photo">
            <edit-material-image-form
                    :image="currentImage"
                    :imageUrl="getImageUrl(currentImage.filename, 'm')"
                    v-on:imageupdatecancel="imageUpdateCancel"
                    v-on:imageupdated="imageUpdated">
            </edit-material-image-form>
        </b-modal>

        <!-- Lightbox Modal -->
        <b-modal v-if="currentImage" ref="lightboxModal"
                 hide-footer
                 hide-header
                 size="lg">
            <div class="d-block text-center">
                <img class="img-fluid"
                     :src="getImageUrl(currentImage.filename, 'l')"
                     :alt="currentImage.title">
                <h6 class="mt-2" v-if="currentImage.title">
                    {{ currentImage.title }}
                </h6>
                <p class="description"
                   v-if="currentImage.description">
                    {{ currentImage.description }}
                </p>
            </div>
        </b-modal>

        <!-- Confirm Delete Modal -->
        <b-modal id="deleteConfirmModal"
                 ref="deleteConfirmModal"
                 title="Delete Image?"
                 v-on:ok="deleteImage"
                 ok-title="Delete Forever">
            <p>Once deleted, you will not be able to retrieve this image!</p>
        </b-modal>
    </div>

</template>
<script>

  import Vue from 'vue'
  import UploadMaterialImageForm from './UploadMaterialImageForm.vue'
  import EditMaterialImageForm from './EditMaterialImageForm.vue'
  import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'

  export default {

    name: 'MaterialImageGallery',
    components: {
      UploadMaterialImageForm,
      EditMaterialImageForm
    },
    props: {
      material: {
        type: Object,
        default: null
      }
    },
    data() {
      return {
        selectedImage: null,
        constants: new GlazyConstants(),
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

      currentUser: function () {
        // Only the creator of a recipe can edit it
        if (this.$auth.check()) {
          return this.$auth.user()
        }
        return false
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
        this.$refs.addImageModal.hide();
      },

      imageUploaded: function () {
        this.$refs.addImageModal.hide()
        this.$emit('imageupdated');
      },

      imageUpdate: function () {
        this.$refs.updateImageModal.show();
      },

      imageUpdateCancel: function () {
        this.$refs.updateImageModal.hide();
      },

      imageUpdated: function () {
        this.$refs.updateImageModal.hide();
        this.$emit('imageupdated');
      },

      getImageBin: function (id) {
        id = '' + id;
        return id.substr(id.length - 2);
      },

      getImageUrl: function (filename, size) {
        var bin = this.getImageBin(this.material.id);

        return GLAZY_APP_URL + '/storage/uploads/recipes/' + bin + '/' + size + '_' + filename;
      },

      selectImage: function (image) {
        this.selectedImage = image;
      },

      lightbox: function () {
        this.$refs.lightboxModal.show()
      },

      confirmDelete: function () {
        this.$refs.deleteConfirmModal.show()
      },

      deleteImage: function() {
        if (!this.currentImage) {
          return;
        }
        this.$refs.deleteConfirmModal.hide();

        Vue.axios.delete(Vue.axios.defaults.baseURL + '/materialimages/' + this.currentImage.id)
          .then((response) => {
          if (response.data.error) {
            this.apiError = response.data.error
            console.log(this.apiError)
          } else {
            this.$emit('imageupdated');
            this.selectedImage = null;
          }
        })
        .catch(response => {
          this.serverError = response;
        })
      },

      setThumbnail: function() {
        if (!this.currentImage) {
          return;
        }
        Vue.axios.get(Vue.axios.defaults.baseURL + '/recipes/' + this.material.id + '/image/' + this.currentImage.id)
          .then((response) => {
          if (response.data.error) {
            this.apiError = response.data.error
            console.log(this.apiError)
          } else {
            this.$emit('imageupdated');
          }
        })
        .catch(response => {
          this.serverError = response;
        })
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

    .image-gallery-card .card-image {
    }

    .image-gallery-card .card-image .gallery-actions {
        position: absolute;
        bottom: 0px;
        right: 10px;
        padding: 0;
        margin: 0;
    }
    .image-gallery-card .card-body {
        padding: 10px;
    }
    .image-gallery-card .card-body .card-title {
        margin-top: 0;
        margin-bottom: 10px;
    }
    .image-gallery-thumb {
        padding: 10px 10px;
    }

    /**/

    .glazy-gallery-thumbs {
    }

    /*
    .gallery-swatches {
        position: absolute;
        top: 0;
        width: 100%;
    }
    */

    .gallery-swatches {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 0;
        margin: 0;
    }

    .gallery-swatches .btn {
        min-width: 24px;
        width: 24px;
        height: 24px;
        line-height: 24px;
        overflow: hidden;
        border-radius: 24px !important;
        padding: 0;
        margin: 0;
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



    .list-group-cone li {
        background-color: #efefef;
        color: #666666;
        padding: 5px 10px;
        border-top: none;
        border-bottom: none;
    }

</style>