<template>
<div class="row edit-user">
    <div class="col-md-12">
        <b-alert v-if="apiError" show variant="danger">
            API Error: {{ apiError.message }}
        </b-alert>
        <b-alert v-if="serverError" show variant="danger">
            Server Error: {{ serverError }}
        </b-alert>
        <div class="load-container load7 fullscreen" v-if="isProcessing">
            <div class="loader">Loading...</div>
        </div>
        <b-alert :show="actionMessageSeconds"
                 @dismiss-count-down="actionMessageCountdown"
                 variant="info">
            {{ actionMessage }}
        </b-alert>
    </div>

    <div class="col-md-6 col-sm-12">
        <div class="card">
            <div class="card-body">
                <form  role="form" method="POST" v-if="isLoaded">
                    <div>
                        <h3 class="card-title">
                            Edit Your Glazy Account
                        </h3>
                    </div>

                    <b-form-group
                            id="groupName"
                            description="Your full name or handle"
                            label="Your Name"
                            label-for="name"
                            :feedback="feedbackName"
                            :state="stateName"
                    >
                        <b-form-input id="name" :state="stateName" v-model.trim="form.name"></b-form-input>
                    </b-form-group>

                    <b-form-group
                            id="groupUsername"
                            description="Cannot be a number.  Please no spaces or special characters"
                            label="Your Glazy Username"
                            label-for="username"
                            :feedback="feedbackUsername"
                            :state="stateUsername"
                    >
                        <b-form-input id="username"
                                      :state="stateUsername"
                                      :formatter="formatUsername"
                                      v-model.trim="form.username"></b-form-input>
                    </b-form-group>

                    <b-form-group
                            id="groupTitle"
                            description="Short description or quote about yourself"
                            label="Quote"
                            label-for="title"
                    >
                        <b-form-input id="title" v-model.trim="form.title"></b-form-input>
                    </b-form-group>

                    <!--
                    <b-form-group
                            id="groupDescription"
                            description="Long introduction of yourself and your work"
                            label="Bio"
                    >
                        <b-form-textarea id="description"
                                         v-model="form.description"
                                         :rows="3"
                                         :max-rows="6">
                        </b-form-textarea>
                    </b-form-group>


                    -->

                    <b-form-group
                            id="groupUrl"
                            description="Your website address, without the http://"
                            label="Website"
                            label-for="url"
                    >
                        <b-form-input id="url" v-model.trim="form.url"></b-form-input>
                    </b-form-group>

                    <b-form-group
                            id="groupPinterest"
                            description="Your Pinterest username can be found in the URL, e.g. https://www.pinterest.com/your_username/"
                            label="Pinterest Username"
                            label-for="pinterest"
                    >
                        <b-form-input id="pinterest" v-model.trim="form.pinterest"></b-form-input>
                    </b-form-group>

                    <b-form-group
                            id="groupFacebook"
                            label="Facebook Username"
                            label-for="facebook"
                    >
                        <b-form-input id="facebook" v-model.trim="form.facebook"></b-form-input>
                    </b-form-group>

                    <b-form-group
                            id="groupInstagram"
                            label="Instagram Username"
                            label-for="instagram"
                    >
                        <b-form-input id="instagram" v-model.trim="form.instagram"></b-form-input>
                    </b-form-group>

                    <b-form-group id="groupButtons1">
                        <b-button class="float-right"
                                  size="sm"
                                  variant="primary"
                                  @click.prevent="updateInfo"><i class="fa fa-save"></i> Update Your Account</b-button>
                        <b-button class="float-right"
                                  size="sm"
                                  variant="secondary"
                                  @click.prevent="cancelEdit()"><i class="fa fa-times"></i> Cancel</b-button>
                    </b-form-group>
                </form>
            </div>
        </div>

    </div>
    <div class="col-md-6 col-sm-12">
        <div class="card">
            <div class="card-body">
                <form  role="form" method="POST" v-if="isLoaded">
                    <div>
                        <h3 class="card-title">
                            Reset Glazy Password
                        </h3>
                    </div>

                    <b-form-group
                            id="email"
                            label="Email Address"
                            label-for="email"
                    >
                        <b-form-input id="email"
                                      v-model.trim="passwordForm.email"
                                      type="email"
                                      autocomplete="username"
                                      aria-describedby="input-help input-feeback"
                                      placeholder="jane@doe.com"></b-form-input>
                    </b-form-group>

                    <b-form-group
                            id="groupCurrentPassword"
                            label="Current Password"
                            label-for="currentPassword"
                    >
                        <b-form-input id="currentPassword"
                                      type="password"
                                      autocomplete="current-password"
                                      v-model.trim="passwordForm.old_password"></b-form-input>
                    </b-form-group>

                    <b-form-group
                            id="groupNewPassword"
                            label="New Password"
                            label-for="newPassword"
                    >
                        <b-form-input id="newPassword"
                                      type="password"
                                      autocomplete="new-password"
                                      v-model.trim="passwordForm.password"></b-form-input>
                    </b-form-group>

                    <b-form-group
                            id="groupNewPasswordAgain"
                            label="New Password (Again)"
                            label-for="newPasswordAgain"
                    >
                        <b-form-input id="newPasswordAgain"
                                      type="password"
                                      autocomplete="new-password"
                                      v-model.trim="passwordForm.password_confirmation"></b-form-input>
                    </b-form-group>

                    <b-form-group v-if="passwordForm.email && passwordForm.old_password && passwordForm.password && passwordForm.password_confirmation"
                                  id="groupButtons2">
                        <b-button class="float-right"
                                  size="sm"
                                  variant="primary"
                                  @click.prevent="updatePassword"><i class="fa fa-save"></i> Reset Password</b-button>
                        <b-button class="float-right"
                                  size="sm"
                                  variant="secondary"
                                  @click.prevent="cancelEdit()"><i class="fa fa-times"></i> Cancel</b-button>
                    </b-form-group>
                </form>
            </div>
        </div>
    </div>
</div>
</template>


<script>
  export default {
    props: {
      recipe: {
        type: Object,
        default: null
      }
    },
    components: {
    },
    data() {
      return {
        user: null,
        form: {},
        passwordForm: {
          email: '',
          old_password: '',
          password: '',
          password_confirmation: ''
        },
        errors: [],
        apiError: null,
        serverError: null,
        isProcessing: false,
        actionMessage: null,
        actionMessageSeconds: 0
      }
    },
    created() {
      if (this.$auth.check()) {
        this.user = this.$auth.user()
        this.form = {
          _method: 'PATCH',
          id: this.user.id,
          name: this.user.name,
        }

        if ('profile' in this.user) {
          this.form.username = this.user.profile.username
          this.form.title = this.user.profile.title
          this.form.description = this.user.profile.description
          this.form.url = this.user.profile.url
          this.form.pinterest = this.user.profile.pinterest
          this.form.facebook = this.user.profile.facebook
          this.form.instagram = this.user.profile.instagram
        }
      }
    },
    computed: {
      isLoaded: function () {
        if (this.$auth.check()) {
          return true;
        }
        return false;
      },
      feedbackName() {
        return this.form.name.length > 0 ? 'Enter at least 3 characters' : 'Please enter your name';
      },
      stateName() {
        return this.form.name.length > 2 ? 'valid' : 'invalid';
      },
      feedbackUsername() {
        return '';
      },
      stateUsername() {
        return 'valid';
      }
    },
    methods: {
      updateInfo: function () {
        if (this.isLoaded) {
          window.scrollTo(0, 0)
          this.isProcessing = true
          this.serverError = null
          this.apiError = null

          Vue.axios.post(Vue.axios.defaults.baseURL + '/auth/profile', this.form)
            .then((response) => {
            console.log('got response:')
            console.log(response)
            if (response.data.error) {
              // error
              this.apiError = response.data.error
              console.log(this.apiError)
            } else {
              console.log('emit updatedUserProfile')
              this.$emit('updatedUserProfile')
              this.actionMessage = 'Success: Your user profile was updated.'
              this.actionMessageSeconds = 5
            }
            this.isProcessing = false
          })
          .catch(response => {
            this.serverError = response
            this.isProcessing = false
            console.log('UPDATE USER ERROR')
            console.log(response.data)
          })
        }
      },
      updatePassword: function () {
        if (this.isLoaded) {
          window.scrollTo(0, 0)
          this.isProcessing = true
          this.serverError = null

          Vue.axios.post(Vue.axios.defaults.baseURL + '/auth/changePassword', this.passwordForm)
            .then((response) => {
            console.log('got response:')
            console.log(response)
            if (response.data.error) {
              // error
              this.apiError = response.data.error
              console.log(this.apiError)
            } else {
              this.actionMessage = 'Your password was successfullly changed.'
              this.actionMessageSeconds = 5
              this.passwordForm.email = ''
              this.passwordForm.old_password = ''
              this.passwordForm.password = ''
              this.passwordForm.password_confirmation = ''
            }
            this.isProcessing = false
          })
          .catch(response => {
            if (response.response && response.response.status) {
              if (response.response.status === 403) {
                this.serverError = 'Incorrect Email & Password combination'
              } else if (response.response.status === 422) {
                this.serverError = 'Password form not filled out correctly'
              } else {
                this.serverError = response
              }
            }
            console.log('UPDATE USER PASSWORD ERROR')
            this.isProcessing = false
          })
        }
      },
        /*
        reset () {
          this.isProcessing = true
          var redirect = this.$auth.redirect()
          this.$auth.reset({
          data: this.data.body,
          rememberMe: this.data.rememberMe,
          autoLogin: true,
          redirect: {
            name: redirect ? redirect.from.name : 'search'
          },
          fetchUser: this.data.fetchUser,
          success (res) {
            console.log('success ' + this.context)
            this.isProcessing = false
            //this.$router.push({ name: 'user', params: { id: this.$auth.user().id }})
          },
          error (res) {
            console.log('error ' + this.context)
            if (res.response.data && res.response.data.error) {
              this.errors = res.response.data.error.errors
            }
            if (res.response.status &&
              Number(res.response.status) === 403 ) {
              this.serverError = 'Incorrect email & password.'
            }
            this.isProcessing = false
          }
        })
      },
      */

      cancelEdit: function () {
        //this.$router.push('search')
        this.$router.go(-1)
      },

      actionMessageCountdown(seconds) {
        this.actionMessageSeconds = seconds
      },

      formatUsername (value, event) {
        value = value.toLowerCase()
        return value.replace(/[^\w]/gi, '')
      }

    }
  }
</script>

<style>
    .edit-user {
        padding-top: 15px;
    }
</style>