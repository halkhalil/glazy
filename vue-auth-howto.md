

## Installation

This document assumes you already have a working API server with JWT and Socialite installed, as well as a working Vue application with vue-auth installed.

The examples are for Laravel 5.5 with JWT & Socialite, however any API server can be used.

This how-to contains examples from the vue-auth demo _laravel-api-demo_ application at:
https://github.com/websanova/laravel-api-demo

### Server-Side: API Server with JWT

Verify you have a working Laravel installation with API routes protected with JWT.
A starting point boiler application can be found at:  https://github.com/francescomalatesta/laravel-api-boilerplate-jwt

### Server-Side: Socialite

Install and configure Socialite for Laravel:

https://github.com/laravel/socialite

### Postman

For testing the API you can try Postman: https://www.getpostman.com/

### Client-Side:  vue-auth

Installation instructions at:  https://github.com/websanova/vue-auth/blob/master/docs/Installation.md

## Basic Token Authentication

### Configure API Routes

_vue-router_ can be modified to handle various API endpoints and JSON formats, 
but in this article we will only be concerned with the default behaviour.

### Basic Token Authentication Login Route

This route handles the basic token authentication.  
_vue-auth_ will look for a specific URL for this action (can be overridden).

vue-auth default route: `BASE API URL + 'auth/login'`

Example: `http://homestead.app/api/auth/login`

Example Laravel Route: `$api->post('login', 'App\\Api\\V1\\Controllers\\LoginController@login');`

````php
    public function login(LoginRequest $request, JWTAuth $JWTAuth)
    {
        $credentials = $request->only(['email', 'password']);

        try {
            $token = Auth::guard()->attempt($credentials);

            if(!$token) {
                throw new AccessDeniedHttpException();
            }

        } catch (JWTException $e) {
            throw new HttpException(500);
        }

        return response([
                'status' => 'success',
                'token' => $token,
                'expires_in' => Auth::guard()->factory()->getTTL() * 60,
                'data' => Auth::guard()->user()
        ])->header('Authorization', $token);
    }
````

#### JSON Output

In Postman, visit `http://homestead.app/api/auth/login` using POST
with Body form-data `email` and `password` key-value pairs.

With `email` = "janedoe@test.app" and the correct password, the output is:

```json
{
    "status": "success",
    "token": "eyJ0eXAiOiJKV1...2Pp-ZBIVujxPPaXW",
    "expires_in": 3600,
    "data": {
        "id": 7,
        "name": "Jane Doe",
        "first_name": "Jane",
        "last_name": "Doe",
        "email": "janedoe@test.app",
        "api_token": null,
        "last_login": "2017-09-12 16:58:01",
        "created_at": "2015-08-08 19:37:19",
        "updated_at": "2017-09-12 16:58:01"
    }
}
```

### User Route

The _user route_ returns information about a user.
It is typically found within a _UserController_ and protected with the `auth:api` middleware.

vue-auth default route: `BASE API URL + 'auth/user'`

Example: `http://homestead.app/api/auth/user`

Example Laravel Route: `$api->get('user', 'App\\Api\\V1\\Controllers\\UserController@user');
`

By default, _vue-auth_ requires that the _user route_ returns user data within a `data` element.

laravel-api-demo example:

```php
    public function user(Request $request)
    {
        $user = User::find(Auth::user()->id);

        return response([
            'status' => 'success',
            'data' => $user
        ]);
    }
```

Other example:

```php
class UserController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', []);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user()
    {
        return response()->json(['data' => Auth::guard()->user()]);
    }
}
```

#### JSON Output

In Postman, just append the token as a querysting to test the user function using GET:

`http://homestead.app/api/auth/user?token=eyJ0eXAiOiJKV1...2Pp-ZBIVujxPPaXW`

The _user route_ should return user data encapsulated within a `data` element:

```json
{
    "data": {
        "id": 7,
        "name": "Jane Doe",
        "first_name": "Jane",
        "last_name": "Doe",
        "email": "janedoe@test.app",
        "api_token": null,
        "last_login": "2017-09-12 16:58:01",
        "created_at": "2015-08-08 19:37:19",
        "updated_at": "2017-09-12 16:58:01"
    }
}
```

### vue-auth Setup for Basic Authentication

Assuming you've already installed _vue-auth_, you Vue application
should have something similar to the following:

```vue
import axios from 'axios'
import VueAxios from 'vue-axios'
import VueAuth from '@websanova/vue-auth'

Vue.use(VueAxios, axios)
//Vue.axios = axios
Vue.axios.defaults.baseURL = 'http://homestead.app/api';

Vue.router=router

Vue.use(VueAuth, {
  auth: require('@websanova/vue-auth/drivers/auth/bearer.js'),
  http: require('@websanova/vue-auth/drivers/http/axios.1.x.js'),
  router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),
  rolesVar: 'role'
});
```

In the _vue-router_ file, create a login route:

```javascript
        {
          path: '/login/:type?',
          name: 'login',
          component: Login
        },

```

_(The optional `type` will be used later for oAuth authentication.)_

Create a Login.vue component:

_The following example uses bootstrap and bootstrap-vue (https://bootstrap-vue.js.org/),
but any type of form would work._

````vue
<template>

    <div class="row">
        <div class="col">

            <h3>Login</h3>

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
        }
        error: null
      }
    },
    mounted () {
    },
    methods: {

      login () {
        var redirect = this.$auth.redirect()
        this.$auth.login({
          data: this.data.body, // Axios uses 'data'.  Use 'params' for vue resource
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

      cancelLogin() {
        this.$router.push('home')
      }

    }
  }

</script>

<style>
</style>
````