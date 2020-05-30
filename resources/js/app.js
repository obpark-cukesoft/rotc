import Vue from 'vue'
window.Vue = Vue;
import Vuetify from 'vuetify'
window.Vuetify = Vuetify;
import 'vuetify/dist/vuetify.min.css' // Ensure you are using css-loader
import '@mdi/font/css/materialdesignicons.css' // mdi https://materialdesignicons.com
import '@fortawesome/fontawesome-free/css/all.css' // fa https://fontawesome.com
import 'material-design-icons-iconfont/dist/material-design-icons.css' // md https://material.io

/*import Vuex from 'vuex'
window.Vuex = Vuex;
Vue.use(Vuetify,Vuex);*/
Vue.use(Vuetify,{
    iconfont: 'mdi' // 'md' || 'mdi' || 'fa' || 'fa4'
});

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

Vue.component('loading-spinner', require('./components/LoadingSpinner.vue').default);
axios.interceptors.request.use((config) => {
    app.loadingSpinner = true;
    return config;
});
axios.interceptors.response.use((response) => {
    app.loadingSpinner = false;
    return Promise.resolve(response)
}, (error) => {
    app.loadingSpinner = false;
    return Promise.reject(error)
});
