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
    var menu = []
    var permisions = []
    items.forEach(function (item) {
      permisions.push(item.menu.nombre_ruta)
      if (item.menu.padre == 0 && item.menu.mostrar) {
        var object = new Object
        object.icon = item.menu.icono
        object.text = item.menu.nombre
        object.path = item.menu.nombre_ruta
        object.childrens = []
        items.forEach(function (child, i) {
          if (item.menu.id == child.menu.padre && child.menu.mostrar) {
            var object2 = new Object()
            object2.icon = child.menu.icono
            object2.text = child.menu.nombre
            object2.path = child.menu.nombre_ruta

            object.childrens.push(object2)
          }
        });

        menu.push(object)
      }
    })

    store.dispatch('setMenu', {
      items: menu,
      permissions: permisions
    })
  }
}
