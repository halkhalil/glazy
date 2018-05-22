<template>
    <div id="reviews-panel">
        <div class="reviews-table" v-if="reviewList.length">
            <div class="row" v-for="review in reviewList">
                <div class="col-sm-12">
                    <div class="media">
                        <a class="pull-left" :href="glazyHelper.getUserProfileUrl(review.user)">
                            <div class="avatar">
                                <img :src="glazyHelper.getUserAvatar(review.user)" class="media-object img-raised"  />
                            </div>
                        </a>
                        <div class="media-body">
                            <h5 class="media-heading">
                                <router-link :to="{ name: 'user', params: { id: review.user.id }}">
                                    {{ glazyHelper.getUserDisplayName(review.user) }}
                                </router-link>
                                <small class="text-muted"><timeago :since="review.updatedAt"></timeago></small>
                            </h5>
                            <star-rating :rating="Number(review.rating)"
                                         :read-only="true"
                                         :star-size="24"
                                         :show-rating="false"
                                         :increment="0.01"></star-rating>
                            <p v-if="'description' in review && review.description"
                                style="white-space: pre-wrap;" 
                                v-html="glazyHelper.getLinkifiedText(review.description.trim())">
                            </p>
                            <div v-if="currentUserReview && currentUserReview.id == review.id && !editOwnReview">
                                <button @click="clickEditOwnReview"
                                        class="btn btn-info"><i class="fa fa-edit"></i> Edit</button>
                                <button @click="clickDeleteOwnReview"
                                        class="btn btn-danger"><i class="fa fa-trash"></i> Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="reviews-table" v-else>
            <h5>No reviews found</h5>
        </div>

        <form v-if="currentUser && (!currentUserReview || editOwnReview)">
        <div class="row">
            <div class="col-sm-12">
                <h4 v-if="currentUserReview">Edit your review</h4>
                <h4 v-else="currentUserReview">Add a review</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="starRating">Rate the glaze from 1 to 5 stars:</label>
                    <star-rating id="starRating"
                                 @rating-selected="syncRating"
                                 :rating="form.rating"
                                 :increment="1"
                                 :max-rating="5"
                                 :show-rating="false"
                                 :star-size="24">
                    </star-rating>

                    <a v-if="form.rating" @click.prevent="resetRating">Reset Rating</a>
                </div>
            </div>
            <div class="col-sm-9">
                <div class="form-group">
                    <label for="reviewTextarea">Enter your review here:</label>
                    <textarea class="form-control"
                              v-model="form.description"
                              id="reviewTextarea"
                              placeholder="Please let us know about material substitutions, firing conditions, etc."
                              rows="3"></textarea>
                </div>
                <button v-if="form.rating > 0 && form.description"
                        @click.prevent="submitReview" class="btn btn-info">
                    <i class="fa fa-save"></i>
                    <span v-if="currentUserReview">Update</span>
                    <span v-else>Save</span>
                </button>
            </div>
        </div>
        </form>
    </div>
</template>
<script>
  import StarRating from 'vue-star-rating'
  import VueTimeago from 'vue-timeago'
  import GlazyHelper from '../helpers/glazy-helper'

  export default {
    name: 'ReviewsPanel',
    components: {
      StarRating,
      VueTimeago
    },
    props: {
      material: {
        type: Object,
        default: null
      },
    },
    data() {
      return {
        form: {
          description: null,
          rating: 0
        },
        editOwnReview: false,
        isProcessing: false,
        glazyHelper: new GlazyHelper()
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

      reviewList: function () {
        if (this.isLoaded) {
          if (this.material.hasOwnProperty('reviews')) {
            return this.material.reviews;
          }
        }
        return [];
      },

      currentUserReview: function () {

        if (this.currentUser
          && this.isLoaded
          && this.reviewList
          && this.reviewList.length) {

          for (var i = 0, len = this.reviewList.length; i < len; i++) {
            if (this.currentUser.id == this.reviewList[i].user.id) {
              return this.reviewList[i];
            }
          }
        }

        return null;
      }

    },

    methods: {

      syncRating(rating) {
        this.form.rating = rating;
      },

      resetRating() {
        this.form.rating = 0;
      },

      clickEditOwnReview() {
        this.form.rating = this.currentUserReview.rating;
        this.form.description = this.currentUserReview.description;
        this.editOwnReview = true;
      },

      clickDeleteOwnReview() {
        this.isProcessing = true;
        Vue.axios.delete(Vue.axios.defaults.baseURL + '/materialreviews/' + this.currentUserReview.id)
          .then((response) => {
          if (response.data.error) {
            this.apiError = response.data.error
            this.isProcessing = false
            console.log(this.apiError)
          } else {
            this.resetForm();
            this.isProcessing = false;
            this.editOwnReview = false;
            this.$emit('reviewsmodified');
          }
        }).catch(response => {
          this.serverError = response;
          this.isProcessing = false
        })
      },

      submitReview() {
        this.isProcessing = true;

        if (this.currentUserReview) {
          this.form._method = 'PATCH'
          var updateUrl = '/materialreviews/' + this.currentUserReview.id

          Vue.axios.post(Vue.axios.defaults.baseURL + '/materialreviews/' + this.currentUserReview.id, this.form)
            .then((response) => {
            if (response.data.error) {
              this.apiError = response.data.error
              this.isProcessing = false
              console.log(this.apiError)
            }
            else {
              this.resetForm();
              this.isProcessing = false;
              this.editOwnReview = false;
              this.$emit('reviewsmodified');
            }
          }).catch(response => {
            this.serverError = response;
            this.isProcessing = false
          })
        }
        else {
          this.form.material_id = this.material.id;

          Vue.axios.post(Vue.axios.defaults.baseURL + '/materialreviews/', this.form)
            .then((response) => {
            if (response.data.error) {
              this.apiError = response.data.error
              this.isProcessing = false
              console.log(this.apiError)
            } else {
              this.resetForm();
              this.isProcessing = false;
              this.$emit('reviewsmodified');
            }
          }).catch(response => {
            this.serverError = response;
            this.isProcessing = false
          })
        }
      },

      resetForm: function () {
        this.form.rating = 0;
        this.form.description = null;
      }
    }
  }

</script>

<style>

    .reviews-table tr {
        vertical-align: top;
    }


    .reviews-table th, td {
        padding: 1rem;
    }

</style>