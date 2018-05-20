<template>
    <div id="comments-panel">
        <div class="load-container load7" v-if="isProcessing">
            <div class="loader">Searching...</div>
        </div>
        <b-alert v-if="apiError" show variant="danger">
            API Error: {{ apiError.message }}
        </b-alert>
        <b-alert v-if="serverError" show variant="danger">
            Server Error: {{ serverError }}
        </b-alert>
        <div class="comments-table" v-if="commentList.length && !isProcessing">
            <div class="row">
                <div class="col-12">
                    <div class="media" v-for="comment in commentList">
                        <a class="pull-left" :href="glazyHelper.getUserProfileUrl(comment.user)">
                            <div class="avatar">
                                <img class="media-object img-raised" :src="glazyHelper.getUserAvatar(comment.user)" alt="...">
                            </div>
                        </a>
                        <div class="media-body">
                            <h5 class="media-heading">
                                <a :href="glazyHelper.getUserProfileUrl(comment.user)">
                                    {{ glazyHelper.getUserDisplayName(comment.user) }}
                                </a>
                                <small class="text-muted"> <timeago :since="comment.updatedAt"></timeago></small>
                            </h5>
                            <form v-show="showEditComment(comment.id)">
                                <div class="form-group">
                                        <textarea class="form-control"
                                                  v-model="comment.content"
                                                  id="commentTextarea"
                                                  placeholder="" rows="3">
                                        </textarea>
                                </div>
                                <button v-if="comment.content" @click.prevent="saveComment(comment.id, comment.content)" class="btn btn-info">
                                    <i class="fa fa-save"></i>
                                    Save
                                </button>
                                <button @click.prevent="cancelEditComment(comment.id)" class="btn btn-cancel">
                                    <i class="fa fa-times"></i>
                                    Cancel
                                </button>
                            </form>
                            <p v-show="!showEditComment(comment.id)">
                                {{ comment.content }}
                            </p>
                            <div v-if="!showEditComment(comment.id) && $auth.check() && $auth.user().id === comment.userId"
                                 class="media-footer  pull-right">
                                <button @click.prevent="editComment(comment.id)"
                                        class="btn btn-info">
                                    <i class="fa fa-edit"></i>
                                    Edit
                                </button>
                                <button @click.prevent="deleteComment(comment.id)"
                                        class="btn btn-danger">
                                    <i class="fa fa-trash"></i>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="comments-table" v-else>
            <h5>No comments found</h5>
        </div>

        <form v-if="$auth.check()">
            <div class="row">
                <div class="col-sm-12">
                    <h4>Your comment or question:</h4>
                    <div class="form-group">
                        <textarea class="form-control"
                                  v-model="form.content"
                                  id="commentTextarea"
                                  placeholder=""
                                  rows="3"></textarea>
                    </div>
                    <button v-if="form.content"
                            @click.prevent="saveComment()" class="btn btn-info">
                        <i class="fa fa-save"></i> Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>
<script>
  import VueTimeago from 'vue-timeago'
  import GlazyHelper from '../helpers/glazy-helper'

  export default {
    name: 'CommentsPanel',
    components: {
      VueTimeago
    },
    props: {
      material: {
        type: Object,
        default: null
      }
    },
    data() {
      return {
        form: {
          content: null
        },
        replies: {},
        editingCommentId: null,
        isProcessing: false,
        serverError: null,
        apiError: null,
        glazyHelper: new GlazyHelper()
      }
    },
    computed: {
      commentList: function () {
        if (this.material && this.material.hasOwnProperty('comments')) {
          return this.material.comments;
        }
        return [];
      }
    },

    methods: {

      editComment(id) {
        this.editingCommentId = id
      },

      showEditComment(id) {
        if (this.editingCommentId && this.editingCommentId === id) {
          return true
        }
        return false
      },

      cancelEditComment(id) {
        this.editingCommentId = null
      },

      deleteComment(id) {
        console.log('derelete: ' + id)
        this.isProcessing = true;
        Vue.axios.delete(Vue.axios.defaults.baseURL + '/materialcomments/' + id)
          .then((response) => {
          console.log(2)
          if (response.data.error) {
            console.log(3)
            this.apiError = response.data.error
            this.isProcessing = false
            console.log(this.apiError)
          } else {
            console.log(4)
            this.resetForm();
            this.isProcessing = false;
            this.$emit('commentsmodified');
          }
        }).catch(response => {
          console.log(5)
          this.serverError = response;
          this.isProcessing = false
        })
      },

      saveComment(id, content) {
        this.isProcessing = true;

        if (id && content) {
          // Editing existing comment
          var updateForm = {}
          updateForm.content = content
          updateForm.id = id
          updateForm._method = 'PATCH'

          Vue.axios.post(Vue.axios.defaults.baseURL + '/materialcomments/' + id, updateForm)
            .then((response) => {
            if (response.data.error) {
              this.apiError = response.data.error
              this.isProcessing = false
              console.log(this.apiError)
            }
            else {
              this.resetForm()
              this.isProcessing = false
              this.editingCommentId = null
              this.$emit('commentsmodified')
            }
          }).catch(response => {
            this.serverError = response;
            this.isProcessing = false
          })
        }
        else {
          this.form.material_id = this.material.id;

          Vue.axios.post(Vue.axios.defaults.baseURL + '/materialcomments/', this.form)
            .then((response) => {
            if (response.data.error) {
              this.apiError = response.data.error
              this.isProcessing = false
              console.log(this.apiError)
            } else {
              this.resetForm();
              this.isProcessing = false;
              this.$emit('commentsmodified')
            }
          }).catch(response => {
            this.serverError = response;
            this.isProcessing = false
          })
        }
      },

      resetForm: function () {
        this.form.content = null;
      }

    }
  }

</script>

<style>

    .comments-table tr {
        vertical-align: top;
    }


    .comments-table th, td {
        padding: 1rem;
    }

</style>