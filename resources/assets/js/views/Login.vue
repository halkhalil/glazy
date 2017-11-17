<template>

    <div class="row content-row">
        <div class="col-md-4 offset-md-4 col-sm-12">
            <b-card title="Login to Glazy">
                <b-alert v-if="serverError" show variant="danger">
                    {{ serverError }}
                </b-alert>

                <div v-show="!code || !type">
                    <a @click="loginSocial('facebook')" href="#" class="btn btn-facebook btn-block btn-sm">
                        <i class="fa fa-facebook-square"></i> Login with Facebook
                    </a>
                    <a @click="loginSocial('google')" class="btn btn-google btn-block btn-sm">
                        <i class="fa fa-google-plus"></i> Login with Google
                    </a>
                    <b-form-group
                            id="email"
                            label="Email Address">
                        <b-form-input v-model.trim="data.body.email"
                                      type="email"
                                      :state="emailState"
                                      aria-describedby="input-help input-feeback"
                                      placeholder="jane@doe.com"></b-form-input>
                        <b-form-feedback id="email-feedback">
                            Invalid email address
                        </b-form-feedback>
                    </b-form-group>
                    <b-form-group
                            id="password"
                            label="Password">
                        <b-form-input
                                id="login-form-password"
                                v-model.trim="data.body.password"
                                :state="passwordState"
                                type="password"></b-form-input>
                        <b-form-feedback id="password-feedback">
                            Invalid password
                        </b-form-feedback>
                    </b-form-group>
                    <div>
                        <b-btn size="sm" class="float-left" variant="secondary" @click="cancelLogin()">
                            Cancel
                        </b-btn>
                        <b-btn size="sm" class="float-right" variant="info" @click="login()">
                            Login
                        </b-btn>
                    </div>
                </div>
                <div v-show="code && type">
                    Logging you in via {{ type }}...
                </div>
            </b-card>
        </div>
    </div>

</template>

<script>
  export default {
    name: 'Login',

    props: {
    },
    data() {
      return {
        context: 'login context',

        data: {
          body: {
            email: '',
            password: ''
          },
          rememberMe: false,
          fetchUser: true
        },
        code: this.$route.query.code,
        type: this.$route.params.type,
        serverError: null,
        errors: null
      }
    },
    computed : {
      emailState() {
        if (this.errors && 'email' in this.errors) {
          return 'invalid'
        }
        return 'valid'
      },
      passwordState() {
        if (this.errors && 'password' in this.errors) {
          return 'invalid'
        }
        return 'valid'
      }
    },
    mounted () {
      if (this.code) {
        this.$auth.oauth2 ({
          code: true,
          provider: this.type,
          data: {
            code: this.$route.query.code
          },
          success: function(res) {
            console.log('social success ' + this.context);
          },
          error: function (res) {
            console.log('social error ' + this.context);
          }
        })
      }

      if (this.$route.query && this.$route.query.error) {
        if (Number(this.$route.query.error) === 401) {
          this.error = 'Unauthorized, please login to perform this function.'
        }
      }

    },
    methods: {
      login () {
        var redirect = this.$auth.redirect()
        this.$auth.login({
          data: this.data.body,
          rememberMe: this.data.rememberMe,
          redirect: {
            name: redirect ? redirect.from.name : 'search'
          },
          fetchUser: this.data.fetchUser,
          success (res) {
            console.log('success ' + this.context)
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
          }
        })
      },

      loginSocial(type) {
        this.$auth.oauth2({
          provider: type || this.type
        })
      },

      cancelLogin() {
        this.$router.push('search')
      }

    }
  }

</script>

<style>

    .content-row {
        padding-top: 15px;
    }
</style>