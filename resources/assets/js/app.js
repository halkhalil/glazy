/**
 * INITIALIZE ALL GLAZY-RELATED CONSTANTS
 */
// import GlazyConstants from 'ceramicscalc-js/src/helpers/GlazyConstants'
// import MaterialTypes from 'ceramicscalc-js/src/material/MaterialTypes'

window.GLAZY_APP_URL = 'http://homestead.test'

/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

//Vue.component('example', require('./components/Example.vue'));


// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import GlazyApp from './GlazyApp'
import router from './router'

import Meta from 'vue-meta'
Vue.use(Meta)
import SocialSharing from 'vue-social-sharing'
Vue.use(SocialSharing);
import BootstrapVue from 'bootstrap-vue'
Vue.use(BootstrapVue)

//window.axios = require('axios');

import axios from 'axios'
import VueAxios from 'vue-axios'
import VueAuth from '@websanova/vue-auth'

import { store } from './store/store'

//require('vue-multiselect/dist/vue-multiselect.min.css')

Vue.use(VueAxios, axios)

Vue.axios.defaults.baseURL = GLAZY_APP_URL + '/api'

Vue.router=router

Vue.use(VueAuth, {
  auth: require('@websanova/vue-auth/drivers/auth/bearer.js'),
  http: require('@websanova/vue-auth/drivers/http/axios.1.x.js'),
  router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),
  rolesVar: 'role',
  registerData: {url: 'auth/register', method: 'POST', redirect: '/login?firstLogin=true'},
  facebookData: {url: GLAZY_APP_URL + '/api/auth/login/facebook/callback', method: 'POST', redirect: '/'},
  facebookOauth2Data: {
    clientId: '186121775282852',
    redirect: function () { return this.options.getUrl() + '/login/facebook'; },
    scope: 'public_profile email'
  },
  googleData: {url: GLAZY_APP_URL + '/api/auth/login/google/callback', method: 'POST', redirect: '/'},
  googleOauth2Data: {
    clientId: '927001342736-522sp5s40ecildcdmeq08njcb250o7t0.apps.googleusercontent.com',
    redirect: function () { return this.options.getUrl() + '/login/google'; },
    scope: 'profile email'
  }
})

Vue.config.productionTip = false

import VueTimeago from 'vue-timeago'
Vue.use(VueTimeago, {
  name: 'timeago', // component name, `timeago` by default
  locale: 'en-US',
  locales: {
    // you will need json-loader in webpack 1
    'en-US': require('vue-timeago/locales/en-US.json')
  }
})

import VueProgressiveImage from 'vue-progressive-image'
Vue.use(VueProgressiveImage, {
  blur: 10
})

/* eslint-disable no-new */
const app = new Vue({
  el: '#app',
  router,
  store,
  template: '<GlazyApp/>',
  components: { GlazyApp }
})

/* Google Analytics */
ga('set', 'page', router.currentRoute.path)
ga('send', 'pageview')
router.afterEach(( to, from ) => {
  ga('set', 'page', to.path)
  ga('send', 'pageview')
})

//const app = new Vue({
//  el: '#app'
//});