<template>
    <header>
        <b-navbar class="navbar navbar-expand-md fixed-top navbar-light bg-white"
                  toggleable>
            <b-navbar-brand v-if="$auth.check()"
                            :to="{ name: 'user', params: { id: $auth.user().id}}">
                <img src="/img/logo.png" height="26" alt="Glazy">
            </b-navbar-brand>
            <b-navbar-brand v-if="!$auth.check()"
                            :to="{ name: 'search' }">
                <img src="/img/logo.png" height="26" alt="Glazy">
            </b-navbar-brand>
            <b-navbar-toggle target="nav_dropdown_collapse"></b-navbar-toggle>
            <b-collapse is-nav id="nav_dropdown_collapse">
                <b-navbar-nav>

                    <b-nav-item-dropdown v-if="$auth.check()"
                                         :text="$auth.user().name" left>
                        <b-dropdown-item :to="{ name: 'user', params: { id: $auth.user().id}}">
                            My Recipes
                        </b-dropdown-item>
                        <b-dropdown-item :to="{ name: 'user-materials', params: { id: $auth.user().id}}">
                            My Materials
                        </b-dropdown-item>
                        <b-dropdown-item href="#">
                            Settings
                        </b-dropdown-item>
                        <b-dropdown-item @click="logout">
                            Logout
                        </b-dropdown-item>
                    </b-nav-item-dropdown>

                    <b-nav-item :to="{ name: 'search' }" >
                        <i class="fa fa-list fa-fw"></i> Recipes
                    </b-nav-item>
                    <b-nav-item :to="{ name: 'materials' }" >
                        <i class="fa fa-flask fa-fw"></i> Materials
                    </b-nav-item>
                    <b-nav-item v-if="$auth.check()"
                                :to="{ name: 'calculator' }" >
                        <i class="fa fa-calculator fa-fw"></i> Calc
                    </b-nav-item>
                    <b-nav-item v-if="!$auth.check()"
                                :to="{ name: 'login' }" >
                        <i class="fa fa-user-circle-o fa-fw"></i> Login</a>
                    </b-nav-item>
                    <b-nav-item v-if="!$auth.check()"
                                :to="{ name: 'register' }" >
                        <i class="fa fa-user-plus fa-fw"></i> Join!</a>
                    </b-nav-item>
                    <b-nav-item href="#" >
                        <i class="fa fa-question fa-fw"></i> Help</a>
                    </b-nav-item>
                    <b-nav-item-dropdown text="Lang" right>
                        <b-dropdown-item href="#">BROKEN</b-dropdown-item>
                        <b-dropdown-item href="#">EN</b-dropdown-item>
                        <b-dropdown-item href="#">ES</b-dropdown-item>
                        <b-dropdown-item href="#">RU</b-dropdown-item>
                        <b-dropdown-item href="#">FA</b-dropdown-item>
                    </b-nav-item-dropdown>

                    <b-nav-item href="#" >
                        MANY THINGS BROKEN! BUT NOTHING WILL BE LOST!
                    </b-nav-item>
                </b-navbar-nav>
            </b-collapse>
        </b-navbar>
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
        // TODO: no need to fetch user, already gotten from login
        // this.fetchUser()
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
            this.$router.push('/login')
          },
          redirect: '/search?logout=true'
        })
      }
    }
  }

</script>

<style>
</style>