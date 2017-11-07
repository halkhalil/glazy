
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

import BootstrapVue from 'bootstrap-vue'
Vue.use(BootstrapVue)

//window.axios = require('axios');

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
  router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js')
});

Vue.config.productionTip = false

/* eslint-disable no-new */
const app = new Vue({
  el: '#app',
  router,
  template: '<GlazyApp/>',
  components: { GlazyApp }
})


//const app = new Vue({
//  el: '#app'
//});