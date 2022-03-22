import router from '../router'
import store from '../store/index'

export default {

  data_refresh_token: {
    grant_type: 'refresh_token',
    refresh_token: '',
    client_id: '',
    client_secret: ''
  },

  getRefreshToken() {
    var token_data = $cookies.get('token_data')
    this.data_refresh_token.refresh_token = token_data.refresh_token
    this.data_refresh_token.client_id = store.state.client_id
    this.data_refresh_token.client_secret = store.state.client_secret

    return this.data_refresh_token
  },

  saveToken(token) {
    store.dispatch('guardarToken', token)
  },

  noAcceso() {
    store.dispatch('logout')
    router.push('/login')
  },

  getUser() {
    store.state.services.loginService.me()
      .then(r => {
        store.dispatch('setUser', r.data)
        this.getMenus(r.data.user)
      }).catch(e => {

      })
  },

  getMenus(data) {
    let self = this
    self.loading = true
    store.state.services.userRolService
      .show(data)
      .then(r => {
        self.loading = false
        if (r.response !== undefined) {
          self.$toastr.error(r.response.data.error, 'error')
          return
        }
        this.mapMenu(r.data)
      }).catch(e => {

      })
  },

  mapMenu(items) {
    try {
      var menu = []
      var permisions = []
      items.forEach(function (item) {
        permisions.push(item.menu.nombre_ruta)
        if (item.menu.padre == 0 && item.menu.mostrar) {
          var primer_nivel = new Object
          primer_nivel.icon = item.menu.icono
          primer_nivel.text = item.menu.nombre
          primer_nivel.path = item.menu.nombre_ruta
          primer_nivel.childrens = []
          items.forEach(function (item2) {
            if (item.menu.id == item2.menu.padre && item2.menu.mostrar) {
              var segundo_nivel = new Object()
              segundo_nivel.icon = item2.menu.icono
              segundo_nivel.text = item2.menu.nombre
              segundo_nivel.path = item2.menu.nombre_ruta
              segundo_nivel.childrens = []

              items.forEach(function (item3) {
                if (item2.menu.id == item3.menu.padre && item3.menu.mostrar) {
                  var tercer_nivel = new Object()
                  tercer_nivel.icon = item3.menu.icono
                  tercer_nivel.text = item3.menu.nombre
                  tercer_nivel.path = item3.menu.nombre_ruta

                  segundo_nivel.childrens.push(tercer_nivel)
                }
              });
              primer_nivel.childrens.push(segundo_nivel)
            }
          });
          menu.push(primer_nivel)
        }
      })

      store.dispatch('setMenu', {
        items: menu,
        permissions: permisions
      })
    } catch (error) {
      alert("Problema al cargar el menú")
    }
  }
}
