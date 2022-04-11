<template>
  <v-app id="inspire">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-navigation-drawer
      v-show="isLogin"
      v-model="drawer"
      v-if="drawer"
      app
      absolute
      dark
      clipped
      permanent
      fixed
      height="100%"
    >
      <v-img :aspect-ratio="16 / 12" sizes="100" :src="logo"></v-img>
      <v-list dense>
        <v-list-item @click="redirect('/')" link>
          <v-list-item-icon>
            <v-icon>home</v-icon>
          </v-list-item-icon>
          <v-list-item-title>Inicio</v-list-item-title>
        </v-list-item>

        <v-list-group
          v-for="item in getMenu"
          v-bind:key="`Primer${item.text}`"
          @click="redirect(item.path)"
          link
          no-action
          :prepend-icon="item.icon"
          :append-icon="item.childrens.length > 0 ? '$expand' : null"
        >
          <template v-slot:activator>
            <v-list-item-title v-text="item.text"></v-list-item-title>
          </template>
          <v-list dense>
            <v-list-group
              v-for="subItem in item.childrens"
              v-bind:key="`Segundo${subItem.text}`"
              @click="redirect(subItem.path)"
              link
              no-action
              :prepend-icon="subItem.icon"
              :append-icon="subItem.childrens.length > 0 ? '$expand' : null"
            >
              <template v-slot:activator>
                <v-list-item-title v-text="subItem.text"></v-list-item-title>
              </template>
              <v-list dense>
                <v-list-group
                  v-for="subbItem in subItem.childrens"
                  v-bind:key="`Tercero${subbItem.text}`"
                  @click="redirect(subbItem.path)"
                  link
                  no-action
                  :prepend-icon="subbItem.icon"
                  :append-icon="null"
                >
                  <template v-slot:activator>
                    <v-list-item-title
                      v-text="subbItem.text"
                    ></v-list-item-title>
                  </template>
                </v-list-group>
              </v-list>
            </v-list-group>
          </v-list>
        </v-list-group>
      </v-list>
    </v-navigation-drawer>

    <v-app-bar
      v-show="isLogin"
      app
      v-if="isLogin"
      :clipped-left="$vuetify.breakpoint.lgAndUp"
      dense
      absolute
      src="https://picsum.photos/1920/1080?random"
    >
      <template v-slot:img="{ props }">
        <v-img
          v-bind="props"
          gradient="to top right, rgba(55,236,186,.7), rgba(25,32,72,.7)"
        ></v-img>
      </template>
      <v-app-bar-nav-icon @click="mostar"></v-app-bar-nav-icon>
      <v-spacer></v-spacer>

      <v-menu bottom min-width="300px" rounded offset-y>
        <template v-slot:activator="{ on }">
          <v-btn icon x-large v-on="on">
            <v-avatar size="40">
              <img :src="fotografia" :alt="cui" />
            </v-avatar>
          </v-btn>
        </template>
        <v-card>
          <v-list-item-content class="justify-center">
            <div class="mx-auto text-center">
              <v-avatar size="100">
                <img :src="fotografia" :alt="cui" />
              </v-avatar>
              <h3>{{ userName }}</h3>
              <p class="text-caption mt-1">
                {{ cui }}
              </p>
              <v-divider class="my-3"></v-divider>
              <div class="text-center">
                <div class="my-2">
                  <v-btn small @click="cambiar_password" outlined color="green">
                    CAMBIAR PASSWORD
                  </v-btn>
                </div>
                <div class="my-2" v-show="apertura_mostrar">
                  <v-btn small @click="aperturar_caja" outlined color="primary">
                    CAJA DEL HOTEL
                  </v-btn>
                </div>
                <div class="my-2">
                  <v-btn
                    small
                    fab
                    outlined
                    color="primary"
                    @click="redirect('/ayuda')"
                    link
                  >
                    <v-icon>info</v-icon>
                  </v-btn>
                </div>
              </div>
              <v-divider class="my-3"></v-divider>
              <v-btn small fab outlined color="red" @click="logout">
                <v-icon>exit_to_app</v-icon>
              </v-btn>
            </div>
          </v-list-item-content>
        </v-card>
      </v-menu>

      <v-dialog v-model="dialog_password" width="30%" persistent>
        <v-card color="success">
          <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
          </v-overlay>
          <v-card-title>
            <span class="headline">Cambiar contraseña</span>
          </v-card-title>

          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12" md="12">
                  <v-text-field
                    clearable
                    counter
                    outlined
                    v-model="form.password"
                    type="password"
                    label="Ingresar la contraseña"
                    data-vv-scope="crear_password"
                    data-vv-name="contraseña"
                    v-validate="'required|min:6'"
                  ></v-text-field>
                  <FormError
                    :attribute_name="'crear_password.contraseña'"
                    :errors_form="errors"
                  ></FormError>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="red darken-1" @click="dialog_password = false">
              Cancelar
            </v-btn>
            <v-btn
              color="blue darken-1"
              @click="validar_formulario_password('crear_password')"
            >
              Guardar
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="dialog_caja" width="70%" persistent>
        <v-toolbar flat :color="'primary'">
          <v-toolbar-title>Caja del Hotel</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          {{ `Hola ${userName}` }}
          <v-spacer></v-spacer>
          <v-btn color="red darken-1" x-small @click="dialog_caja = false">
            Cerrar
          </v-btn>
        </v-toolbar>
        <v-card color="white" flat>
          <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
          </v-overlay>

          <v-card-text v-if="!existe_caja">
            <v-container>
              <v-row>
                <v-col cols="12" md="4">
                  <v-text-field
                    v-model="form_caja.inicia_caja"
                    label="dinero de apertura"
                    prefix="Q"
                    filled
                    shaped
                    dense
                    light
                    data-vv-scope="apertura"
                    data-vv-name="dinero de apertura"
                    v-validate="'required|integer|min_value:100'"
                  ></v-text-field>
                  <FormError
                    :attribute_name="'apertura.dinero de apertura'"
                    :errors_form="errors"
                  ></FormError>
                </v-col>
                <v-col cols="12" md="8">
                  <v-btn
                    color="green darken-1"
                    block
                    dark
                    x-large
                    @click="registro_apertura('apertura')"
                  >
                    Aperturar Caja
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>

          <v-card-text v-if="existe_caja">
            <v-container>
              <v-row>
                <v-col cols="12" md="4">
                  <v-row>
                    <v-col cols="12" md="12">
                      <v-text-field
                        v-model="form_caja.monto_total"
                        label="dinero de movimiento"
                        prefix="Q"
                        filled
                        shaped
                        dense
                        ligth
                        data-vv-scope="movimiento"
                        data-vv-name="dinero de movimiento"
                        v-validate="'required|integer|min_value:0'"
                      ></v-text-field>
                      <FormError
                        :attribute_name="'movimiento.dinero de movimiento'"
                        :errors_form="errors"
                      ></FormError>
                    </v-col>
                    <v-col cols="12" md="12">
                      <v-textarea
                        rows="2"
                        prepend-icon="fiber_new"
                        counter
                        outlined
                        ligth
                        v-model="form_caja.descripcion"
                        type="text"
                        label="descripción"
                        data-vv-scope="movimiento"
                        data-vv-name="descripción"
                        v-validate="'required|min:10|max:100'"
                      ></v-textarea>
                      <FormError
                        :attribute_name="'movimiento.descripción'"
                        :errors_form="errors"
                      ></FormError>
                    </v-col>
                    <v-col cols="12" md="12">
                      <v-select
                        filled-inverted
                        suffix
                        dense
                        ligth
                        prepend-inner-icon="view_carousel"
                        v-model="form_caja.tipo_pago"
                        :items="catalogo_tipo_pago"
                        label="seleccione uno por favor"
                        :clearable="true"
                        :deletable-chips="true"
                        item-text="valor"
                        item-value="valor"
                        return-object
                        v-validate="'required'"
                        data-vv-scope="movimiento"
                        data-vv-name="tipo de pago"
                        @input="verificar_comprobante"
                      ></v-select>
                      <FormError
                        :attribute_name="'movimiento.tipo de pago'"
                        :errors_form="errors"
                      ></FormError>
                    </v-col>
                    <v-col cols="12" md="12" v-if="mostrar_comprobante">
                      <v-text-field
                        filled-inverted
                        suffix
                        dense
                        prepend-inner-icon="fiber_new"
                        counter
                        ligth
                        v-model="form_caja.comprobante"
                        type="text"
                        label="comprobante"
                        data-vv-scope="movimiento"
                        data-vv-name="`comprobante`"
                        v-validate="'required|max:15'"
                        hint="Número del comprobante"
                        persistent-hint
                      ></v-text-field>
                      <FormError
                        :attribute_name="`movimiento.comprobante`"
                        :errors_form="errors"
                      ></FormError>
                    </v-col>
                    <v-col cols="12" md="12" class="text-right">
                      <v-btn
                        color="primary darken-1"
                        dark
                        small
                        @click="registrar_movimiento('movimiento')"
                      >
                        Registrar movimiento
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-col>
                <v-col cols="12" md="8" v-if="registros.movimientos.length > 0">
                  <v-row>
                    <v-col cols="12 text-center">
                      <span class="text-h5">
                        Movimientos de Caja
                      </span>
                    </v-col>
                    <v-col cols="12">
                      <v-simple-table light dense fixed-header height="80%">
                        <thead>
                          <tr>
                            <th class="text-center">Descripción</th>
                            <th class="text-center">Monto</th>
                            <th class="text-center">Anular</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-for="(mov,
                            index_detalle) in registros.movimientos"
                            v-bind:key="index_detalle"
                          >
                            <td class="text-center">{{ mov.descripcion }}</td>
                            <td class="text-center">
                              {{ formato_moneda(1, mov.monto_total, 0) }}
                            </td>
                            <td class="text-center">
                              <v-btn
                                color="red darken-1"
                                dark
                                text
                                fab
                                small
                                @click="anular_movimiento(mov)"
                                v-if="mov.registro_manual"
                              >
                                <v-icon>close</v-icon>
                              </v-btn>
                            </td>
                          </tr>
                        </tbody>
                      </v-simple-table>
                    </v-col>
                  </v-row>
                </v-col>
                <v-col cols="12">
                  <v-btn
                    color="warning darken-1"
                    dark
                    block
                    x-large
                    @click="cerrar_caja()"
                    v-show="cierre_mostrar"
                  >
                    CERRAR CAJA
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-app-bar>

    <v-main :style="fondo">
      <v-container fluid>
        <router-view></router-view>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import FormError from './components/shared/FormError'

export default {
  components: {
    FormError,
  },
  data() {
    return {
      loading: false,
      dialog_password: false,
      dialog_caja: false,
      drawer: false,
      menu: false,
      form: {
        id: 0,
        password: null,
      },

      form_caja: {
        id: 0,
        inicia_caja: 0,

        descripcion: null,
        monto_total: 0,
        tipo_pago: null,
        comprobante: null,
      },

      registros: [],
      mostrar_comprobante: false,
      existe_caja: false,

      catalogo_tipo_pago: [
        { valor: 'EFECTIVO' },
        { valor: 'TARJETA' },
        { valor: 'CHEQUE' },
      ],
    }
  },
  created() {},

  methods: {
    logout() {
      let self = this
      self.loading = true
      self.$store.state.services.loginService
        .logout()
        .then((r) => {})
        .catch((e) => {
          this.errorResponse(e)
        })
        .finally(() => {
          self.$store.dispatch('logout')
          self.$router.push('/login')
          self.drawer = null
          self.$store.state.is_login = false
          self.loading = false
        })
    },

    redirect(item) {
      if (item !== '#') {
        this.$router.push({ path: item })
      }
    },

    mostar() {
      let self = this
      self.drawer = self.drawer ? false : true
    },

    cambiar_password() {
      this.loading = true
      this.form.id = this.$store.state.usuario.id
      this.dialog_password = true
      this.loading = false
    },

    aperturar_caja() {
      this.loading = true
      this.registros = []
      this.form_caja.id = 0
      this.form_caja.inicia_caja = 0

      this.form_caja.descripcion = null
      this.form_caja.monto_total = 0
      this.form_caja.tipo_pago = null
      this.form_caja.comprobante = null

      this.mostrar_comprobante = false
      this.existe_caja = false
      this.$store.state.services.CajaService.getCreate()
        .then((r) => {
          this.registros = r.data
          this.existe_caja = true
          this.form_caja.id = r.data.id
        })
        .catch((e) => {
          this.errorResponse(e)
        })
        .finally(() => {
          this.dialog_caja = true
          this.loading = false
        })
    },

    registro_apertura(scope) {
      this.$validator.validateAll(scope).then((result) => {
        if (result) {
          this.$swal({
            title: 'Aperturar Caja',
            text: '¿Está seguro de realizar esta acción?',
            type: 'question',
            showCancelButton: true,
          }).then((result) => {
            if (result.value) {
              this.loading = true
              this.$store.state.services.CajaService.store(this.form_caja)
                .then((r) => {
                  this.$toastr.success(r.data, 'Mensaje')
                })
                .catch((e) => {
                  this.errorResponse(e)
                })
                .finally(() => {
                  this.aperturar_caja()
                })
            }
          })
        }
      })
    },

    cerrar_caja() {
      this.$swal({
        title: 'Cerrar Caja',
        text: '¿Está seguro de realizar esta acción?',
        type: 'warning',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.CajaService.get(this.form_caja.id)
            .then((r) => {
              this.$toastr.success(r.data, 'Mensaje')
            })
            .catch((e) => {
              this.errorResponse(e)
            })
            .finally(() => {
              this.aperturar_caja()
            })
        }
      })
    },

    registrar_movimiento(scope) {
      this.$validator.validateAll(scope).then((result) => {
        if (result) {
          this.$swal({
            title: 'Registrar Movimiento',
            text: '¿Está seguro de realizar esta acción?',
            type: 'info',
            showCancelButton: true,
          }).then((result) => {
            if (result.value) {
              this.loading = true
              this.$store.state.services.CajaMovimientoService.update(
                this.form_caja,
              )
                .then((r) => {
                  this.$toastr.success(r.data, 'Mensaje')
                })
                .catch((e) => {
                  this.errorResponse(e)
                })
                .finally(() => {
                  this.aperturar_caja()
                })
            }
          })
        }
      })
    },

    anular_movimiento(data) {
      this.$swal({
        title: 'Anular Movimiento',
        text: '¿Está seguro de realizar esta acción?',
        type: 'error',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.CajaMovimientoService.delete(data)
            .then((r) => {
              this.$toastr.success(r.data, 'Mensaje')
            })
            .catch((e) => {
              this.errorResponse(e)
            })
            .finally(() => {
              this.aperturar_caja()
            })
        }
      })
    },

    verificar_comprobante(item) {
      if (item) {
        this.mostrar_comprobante = item.valor !== 'EFECTIVO'
      }
    },
  },

  computed: {
    isLogin() {
      let self = this

      if (self.$store.state.is_login) {
        this.drawer = true
        this.$vuetify.theme.dark = false
      } else {
        this.drawer = null
        this.$vuetify.theme.dark = false
      }

      return self.$store.state.is_login
    },

    isAdmin() {
      let self = this
      return self.$store.state.usuario
        ? self.$store.state.usuario.admin == 'ADMINISTRADOR'
          ? true
          : false
        : false
    },

    fotografia() {
      let self = this
      return self.$store.state.usuario
        ? self.$store.state.usuario.picture
        : null
    },

    userName() {
      let self = this
      return self.$store.state.usuario
        ? self.$store.state.usuario.full_name
        : null
    },

    cui() {
      let self = this
      return self.$store.state.usuario ? self.$store.state.usuario.cui : null
    },

    getMenu() {
      let self = this
      return self.$store.state.menu
    },

    logo() {
      return `${this.$store.state.base_url}img/logo.png`
    },

    fondo() {
      return `
          background-image: url('${this.$store.state.base_url}img/fondo.jpg');
          background-size: cover;
        `
    },

    apertura_mostrar() {
      var permissions = this.$store.state.permissions
      return _.includes(permissions, 'apertura_caja_hotel')
    },

    cierre_mostrar() {
      var permissions = this.$store.state.permissions
      return _.includes(permissions, 'cierre_caja_hotel')
    },
  },
}
</script>
