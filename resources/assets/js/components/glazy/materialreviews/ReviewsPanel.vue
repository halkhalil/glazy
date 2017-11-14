<template>
    <div id="reviews-panel">
        <div class="reviews-table" v-if="reviewList.length">
            <div class="row" v-for="review in reviewList">
                <div class="col-sm-12">
                    <div class="media">
                        <a class="pull-left" :href="getUserProfileUrl(review.user)">
                            <div class="avatar">
                                <img :src="getUserAvatar(review.user)" class="media-object img-raised"  />
                            </div>
                        </a>
                        <div class="media-body">
                            <h5 class="media-heading">
                                {{ getDisplayName(review.user) }}
                                <small class="text-muted"><timeago :since="review.updatedAt"></timeago></small>
                            </h5>
                            <star-rating :rating="Number(review.rating)"
                                         :read-only="true"
                                         :star-size="24"
                                         :show-rating="false"
                                         :increment="0.01"></star-rating>
                            <button v-if="currentUserReview && currentUserReview.id == review.id && !editOwnReview"
                                    @click="clickEditOwnReview"
                                    class="btn btn-primary">Edit Your Review</button>
                            <p>
                                {{ review.description }}
                            </p>
                            <div class="media-footer">
                                <a href="#pablo" class="btn btn-primary btn-neutral pull-right" rel="tooltip" title="" data-original-title="Reply to Comment">
                                    <i class="now-ui-icons ui-1_send"></i> Reply
                                </a>

                                <a href="#pablo" class="btn btn-danger btn-neutral pull-right">
                                    <i class="now-ui-icons ui-2_favourite-28"></i> 243
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="reviews-table" v-else>
            <em>No reviews found</em>
        </div>

        <form v-if="current_user && (!currentUserReview || editOwnReview)">
        <div class="row">
            <div class="col-sm-12">
                <h4 v-if="currentUserReview">Modify your review</h4>
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

                    <a href="#" @click.prevent="resetRating">Reset Rating</a>
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
                        @click.prevent="submitReview" class="btn btn-primary">
                    <span v-if="currentUserReview">Update Your Review</span>
                    <span v-else>Add Review</span>
                </button>
            </div>
        </div>
        </form>
    </div>
</template>
<script>
  import StarRating from 'vue-star-rating'
  import VueTimeago from 'vue-timeago'

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
        isProcessing: false
      }
    },
    computed: {
      isLoaded: function () {
        if (this.material) {
          return true;
        }
        return false;
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

        if (this.current_user
          && this.isLoaded
          && this.reviewList
          && this.reviewList.length) {

          for (var i = 0, len = this.reviewList.length; i < len; i++) {
            if (this.current_user.id == this.reviewList[i].user.id) {
              return this.reviewList[i];
            }
          }
        }

        return null;
      }

    },

    methods: {

      getUserProfileUrl: function (user) {
        if (user.hasOwnProperty('username')
          && user.username) {
          return '/u/' + user.username;
        }
        return '/u/' + user.id;
      },

      getUserAvatar: function (user) {
        if (user.hasOwnProperty('avatar') && user.avatar) {
          return user.avatar;
        }
        else if (user.hasOwnProperty('gravatar') && user.gravatar) {
          return user.gravatar;
        }
        else {
          return 'http://www.gravatar.com/avatar/?d=mm';
        }
      },

      getDisplayName: function (user) {
        if (user.hasOwnProperty('username') && user.username) {
          return user.username;
        }
        return user.name;
      },

      syncRating(rating) {
        this.form.rating = rating;
      },

      resetRating() {
        this.form.rating = 0;
//                e.preventDefault();
      },

      clickEditOwnReview() {
        this.form.rating = this.currentUserReview.rating;
        this.form.description = this.currentUserReview.description;
        this.editOwnReview = true;
      },

      submitReview(e) {
        e.preventDefault();

        this.isProcessing = true;

        if (this.currentUserReview) {
          this.form._method = 'PATCH';
          var updateUrl = '/api/v1/materialreviews/' + this.currentUserReview.id;
          axios.post(updateUrl, this.form)
            .then(function (response) {
              this.resetForm();
              this.isProcessing = false;
              this.editOwnReview = false;
              this.$emit('reviewsmodified');
            }.bind(this), function (response) {
              this.errors = response.data;
              this.hasErrors = true;
              this.isProcessing = false;
            }.bind(this));
        }
        else {
          this.form.material_id = this.material.id;
          axios.post('/api/v1/materialreviews', this.form)
            .then(function (response) {
              this.resetForm();
              this.isProcessing = false;
              this.$emit('reviewsmodified');
            }.bind(this), function (response) {
              this.errors = response.data;
              this.hasErrors = true;
              this.isProcessing = false;
            }.bind(this));
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