<template>
    <header>
        <nav class="navbar navbar-expand-md fixed-top navbar-light bg-white">
            <a class="navbar-brand" href="#"><img src="/static/img/logo.png" height="26" alt="Glazy"></a>
            <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav mr-auto">
                    <router-link to="/search" tag="li" active-class="active" class="nav-item">
                        <a class="nav-link">
                            <i class="fa fa-search fa-fw"></i> Search
                        </a>
                    </router-link>
                    <router-link to="/calculator" tag="li" active-class="active" class="nav-item">
                        <a class="nav-link">
                            <i class="fa fa-calculator fa-fw"></i> Calc
                        </a>
                    </router-link>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-question fa-fw"></i> Help</a>
                    </li>
                    <li class="nav-item" v-if="!$auth.check()">
                        <a class="nav-link" @click="showLoginModal"><i class="fa fa-user-circle-o fa-fw"></i> Login</a>
                    </li>
                    <li class="nav-item" v-if="$auth.check()">
                        <a class="nav-link" @click="logout">Logout</a>
                    </li>
                    <li class="nav-item" v-if="$auth.check()">
                        <a class="nav-link" >{{ $auth.user().name }}</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div>
            <!-- Modal Component -->
            <b-modal ref="loginModal" id="loginModal" title="Login">
                <a href="#" class="btn btn-facebook btn-block">
                    <i class="fa fa-facebook-square"></i> Login with Facebook
                </a>
                <a @click="loginSocial('google')" class="btn btn-google btn-block">
                    <i class="fa fa-google-plus"></i> Login with Google
                </a>
                <b-form-group
                        id="email"
                        label="Email Address"
                >
                    <b-form-input
                            id="login-form-email"
                            v-model.trim="data.body.email"
                            type="email"
                    ></b-form-input>
                </b-form-group>
                <b-form-group
                        id="password"
                        label="Password"
                >
                    <b-form-input
                            id="login-form-password"
                            v-model.trim="data.body.password"
                            type="password"
                    ></b-form-input>
                </b-form-group>
                <div slot="modal-footer" class="w-100">
                    <b-btn size="sm" class="float-left" variant="secondary" @click="hideLoginModal()">
                        Cancel
                    </b-btn>
                    <b-btn size="sm" class="float-right" variant="info" @click="login()">
                        Login
                    </b-btn>
                </div>
            </b-modal>
        </div>
    </header>
</template>

<script>

  export default {
    name: 'AppHeader',

    props: {
    },
    data() {
      return {
        context: 'login context',

        data: {
          body: {
            email: 'derek@derekau.net',
            password: ''
          },
          rememberMe: false,
          fetchUser: false
        },
        code: this.$route.query.code,
        type: this.$route.params.type,
        error: null
      }
    },
    mounted () {
      /*
      if (this.code) {
        this.$auth.oauth2 ({
          code: true,
          provider: this.type,
          data: {
            code: this.code,
          },
          success: function(res) {
            console.log('social success ' + this.context);
          },
          error: function (res) {
            console.log('social error ' + this.context);
          }
        })
      }

      if (this.$auth.check()) {
        console.log('333333 TRYING TO FETCH USER...')
        this.fetchUser()
      }
      else {
        console.log('333333 NOT AUTH')
      }
      */
    },
    methods: {

      fetchUser () {
        console.log('AM I LOGGED IN? ' + this.$auth.check())
        this.$auth.fetch({
          success(res) {
            console.log('success ' + this.context);
            console.log('user')
            console.log(this.$auth.user())
            console.log('user id: ' + this.$auth.user().id)
          },
          error() {
            console.log('error ' + this.context);
          }
        });
      },

      showLoginModal: function () {
        this.$refs.loginModal.show()
      },
      hideLoginModal: function () {
        this.$refs.loginModal.hide()
      },
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
            this.$auth.token(null, res.data.token)
            this.$auth.user(res.data.data)
            this.hideLoginModal()
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
      logout () {
        this.$auth.logout({
          makeRequest: true,
          data: {}, // data: {} in axios
          success: function () {},
          error: function () {},
          redirect: '/search',
        })
      }
    }
  }

</script>

<style>
</style>