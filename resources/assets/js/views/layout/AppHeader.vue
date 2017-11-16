<template>
    <header>
        <nav class="navbar navbar-expand-md fixed-top navbar-light bg-white">
            <a class="navbar-brand" href="#"><img src="/img/logo.png" height="26" alt="Glazy"></a>
            <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav mr-auto">
                    <router-link :to="{ name: 'search' }" tag="li" active-class="active" class="nav-link">
                        <i class="fa fa-list fa-fw"></i> Recipes
                    </router-link>
                    <router-link :to="{ name: 'materials' }" tag="li" active-class="active" class="nav-link">
                        <i class="fa fa-flask fa-fw"></i> Materials
                    </router-link>
                    <router-link :to="{ name: 'calculator' }"
                                 tag="li"
                                 active-class="active"
                                 class="nav-item">
                        <a class="nav-link">
                            <i class="fa fa-calculator fa-fw"></i> Calc
                        </a>
                    </router-link>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-question fa-fw"></i> Help</a>
                    </li>
                    <li class="nav-item" v-if="!$auth.check()">
                        <a class="nav-link" href="/login"><i class="fa fa-user-circle-o fa-fw"></i> Login</a>
                    </li>
                    <li class="nav-item" v-if="$auth.check()">
                        <a class="nav-link" @click="logout">Logout</a>
                    </li>
                    <li class="nav-item" v-if="$auth.check()">
                        <router-link :to="{ name: 'user', params: { id: $auth.user().id}}"
                                     tag="li"
                                     active-class="active"
                                     class="nav-link">
                            {{ $auth.user().name }}
                        </router-link>
                    </li>
                </ul>
            </div>
        </nav>
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
      if (this.$auth.check() && !this.$auth.user.id) {
        this.fetchUser()
      }
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

      logout () {
        this.$auth.logout({
          makeRequest: true,
          data: {}, // data: {} in axios
          success: function () {
            this.$router.push('home')
          },
          error: function () {
            console.log('ERROR IN LOGOUT')
            this.$router.push('login')
          },
          redirect: '/search?logout=true'
        })
      }
    }
  }

</script>

<style>
</style>