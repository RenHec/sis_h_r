// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import Vuetify from 'vuetify'
import '@mdi/font/css/materialdesignicons.css'
import '@fortawesome/fontawesome-free/css/all.css'
import 'material-design-icons-iconfont/dist/material-design-icons.css'
Vue.use(Vuetify, { iconfont: 'mdi' || 'fa' || 'md' || 'fa4' })
import 'vuetify/dist/vuetify.min.css'

import VueCookies from 'vue-cookies'
Vue.use(VueCookies)

// App
import App from './App'
// Vue Router
import router from './router'
// Vuex: for services, shared components, etc
import store from './store/index'
//import lodash
import _ from 'lodash'

//validators
import VeeValidate from 'vee-validate'
const VueValidationEs = require('vee-validate/dist/locale/es');
const config = {
  locale: 'es',
  validity: true,
  dictionary: {
    es: VueValidationEs
  },
  fieldsBagName: 'campos',
  errorBagName: 'errors', // change if property conflicts
};
Vue.use(VeeValidate, config);

//import toastr
import VueToastr2 from 'vue-toastr-2'
import 'vue-toastr-2/dist/vue-toastr-2.min.css'
window.toastr = require('toastr')
Vue.use(VueToastr2)

import { v4 as uuidv4 } from 'uuid'
Vue.use(uuidv4)

Vue.config.productionTip = false
// set default config
window.events = new Vue();

// MomentJs for Vue
const moment = require('moment')
Vue.use(require('vue-moment'), {
  moment
})

import VueSweetalert2 from 'vue-sweetalert2'
const options = {
  confirmButtonColor: '#41b882',
  cancelButtonColor: '#ff7674',
  confirmButtonText: "Confirmar",
  cancelButtonText: "Cancelar"
}
Vue.use(VueSweetalert2, options)

//volver a validar
store.dispatch('autoLogin')

/* eslint-disable no-new */
new Vue({
  el: '#app',
  vuetify: new Vuetify(),
  router,
  store,
  template: '<App/>',
  components: {
    App
  }
})

//Nuevos componentes
import {
  Cropper
} from 'vue-advanced-cropper'
Vue.use(Cropper, {
  handlersClassnames: {
    default: "handler",
    hover: "handler--hover",
    eastNorth: "handler--east-north",
    north: "handler--north",
    westNorth: "handler--west-north",
    west: "handler--west",
    westSouth: "handler--west-south",
    south: "handler--south",
    eastSouth: "handler--east-south",
    east: "handler--ease",
    previewClassname: 'preview'
  },
  movable: true,
  scalable: true,
});

import VuePhoneNumberInput from 'vue-phone-number-input';
import '../static/vue-phone-number-input.css';
Vue.component('vue-phone-number-input', VuePhoneNumberInput);

import VueNumberInput from '@chenfengyuan/vue-number-input';
Vue.component('vue-number-input', VueNumberInput);

import VueQuillEditor from 'vue-quill-editor'
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'
Vue.use(VueQuillEditor, {
  modules: {
    toolbar: [
      ['bold', 'italic', 'underline', 'strike'],
      [{
        'list': 'ordered'
      }, {
        'list': 'bullet'
      }],
      [{
        'indent': '-1'
      }, {
        'indent': '+1'
      }],
      [{
        'align': []
      }],
    ],
    syntax: {
      highlight: text => hljs.highlightAuto(text).value
    }
  }
})

import '../static/style.css'
import '../static/cleave.min.js'
//import '../static/main-bac.js'

//Gráficas
import Highcharts from 'highcharts'
import HighchartsVue from 'highcharts-vue'
Vue.use(HighchartsVue)
import exportingInit from 'highcharts/modules/exporting'
exportingInit(Highcharts)
