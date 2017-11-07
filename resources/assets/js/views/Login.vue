<template>

    <div>
        <h1>Oauth2</h1>

        My type: {{ type }}

        <div v-show="!code || !type">
            <a @click="loginSocial('facebook')" href="#" class="btn btn-facebook btn-block">
                <i class="fa fa-facebook-square"></i> Login with Facebook
            </a>
            <a @click="loginSocial('google')" class="btn btn-google btn-block">
                <i class="fa fa-google-plus"></i> Login with Google
            </a>
        </div>
        <div v-show="code && type">
            Verifying {{ type }} code...
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
      /*
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


      loginSocial(type) {
        this.$auth.oauth2({
          provider: type || this.type
        })
      }

    }
  }

</script>

<style>
</style>