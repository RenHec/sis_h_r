import Vue from 'vue'
import Vuex from 'vuex'
import services from './services'
import auth from '../auth'
import Axios from 'axios'
import router from '../router'
import global from '../components/sharedjs/global'
import restaurantStore from './restaurant'

Vue.use(Vuex)

const state = {
  services,
  usuario: null,
  configuracion: null,
  empresa: null,
  roles: [],
  token: null,
  global,
  is_login: false,
  token_expired: null,
  menu: [],
  permissions: [],
  firma_en_blanco: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAACWCAYAAABkW7XSAAAEYklEQVR4Xu3UAQkAAAwCwdm/9HI83BLIOdw5AgQIRAQWySkmAQIEzmB5AgIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlAABg+UHCBDICBisTFWCEiBgsPwAAQIZAYOVqUpQAgQMlh8gQCAjYLAyVQlKgIDB8gMECGQEDFamKkEJEDBYfoAAgYyAwcpUJSgBAgbLDxAgkBEwWJmqBCVAwGD5AQIEMgIGK1OVoAQIGCw/QIBARsBgZaoSlACBB1YxAJfjJb2jAAAAAElFTkSuQmCC',
  audio: {
    agregar: `${process.env.BASE_URL}audio/agregar.mp3`,
    anular: `${process.env.BASE_URL}audio/anular.mp3`,
  },

  base_url: process.env.BASE_URL,
  client_id: process.env.CLIENT_API,
  client_secret: process.env.CLIENT_SECRET
}

const mutations = {
  setUser(state, data) {
    state.usuario = data.user.empleado
    state.empresa = null
    state.roles = data.roles
    //state.configuracion = data.configuracion
  },

  setMenu(state, menu) {
    state.menu = menu.items
    state.permissions = menu.permissions
  },

  setToken(state, token) {
    state.token = token
    state.is_login = true
  },

  logout(state) {
    state.is_login = false
    state.token = null
  },

  setState(state) {
    state.is_login = false
    state.token = null
  },

  setTokenExpired(state, time) {
    state.token_expired = time
  }
}

const actions = {
  guardarToken({
    commit
  }, data_user) {
    Axios.defaults.headers.common.Authorization = `Bearer ${data_user.access_token}`
    commit("setToken", data_user.access_token)
    $cookies.set('token_data', data_user)
  },

  logout({
    commit
  }) {
    state.is_login = false
    state.token = null
    $cookies.remove('token_data')
    commit("logout")
  },

  autoLogin({
    commit
  }) {
    let token = $cookies.get('token_data')
    if (token) {
      commit('setToken', token)
      auth.getUser()
      router.push('/')
    } else {
      commit('setState')
    }
  },

  setUser({
    commit
  }, user) {
    commit('setUser', user)
  },

  setMenu({
    commit
  }, menu) {
    commit('setMenu', menu)
  },
}

export default new Vuex.Store({
  strict: true,
  state,
  mutations,
  actions,
  modules: {
    restaurant: restaurantStore
  }
})
