<template>

    <div class="row mt-4">
        <div class="col-md-6 offset-md-3 col-sm-12">
            <h3>Login to Glazy</h3>

            <b-alert v-if="error" show variant="danger">
                {{ error }}
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
                    <b-form-input
                            id="login-form-email"
                            v-model.trim="data.body.email"
                            type="email"
                    ></b-form-input>
                </b-form-group>
                <b-form-group
                        id="password"
                        label="Password">
                    <b-form-input
                            id="login-form-password"
                            v-model.trim="data.body.password"
                            type="password"
                    ></b-form-input>
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
        error: null
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
            this.$router.push('home')
          },
          error (res) {
            console.log('error ' + this.context)
            this.error = res.data;
          }
        })
      },

      loginSocial(type) {
        this.$auth.oauth2({
          provider: type || this.type
        })
      },

      cancelLogin() {
        this.$router.push('home')
      }

    }
  }

</script>

<style>
</style>