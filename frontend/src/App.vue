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
      width="15%"
      permanent
      flat
      fixed
      height="100%"
    >
      <v-img :aspect-ratio="16 / 10" :src="logo"></v-img>
      <v-list dense>
        <v-list-item @click="redirect('/')" link>
          <v-list-item-action>
            <v-icon>home</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>Inicio</v-list-item-title>
          </v-list-item-content>
        </v-list-item>

        <template v-for="item in getMenu">
          <v-list-item
            @click="redirect(item.path)"
            link
            v-if="item.path !== '#'"
            v-bind:key="item.text"
          >
            <v-list-item-action>
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ item.text }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>

          <v-list-group
            v-else
            :key="item.text"
            :prepend-icon="item.icon"
            no-action
          >
            <template v-slot:activator>
              <v-list-item-content>
                <v-list-item-title v-text="item.text"></v-list-item-title>
              </v-list-item-content>
            </template>

            <v-list-item
              v-for="subItem in item.childrens"
              :key="subItem.path"
              link
              @click="redirect(subItem.path)"
            >
              <v-list-item-content>
                <v-list-item-title v-text="subItem.text"></v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list-group>
        </template>
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
      <v-app-bar-title>{{ empresa }}</v-app-bar-title>
      <v-spacer></v-spacer>

      <v-menu bottom width="30%" rounded offset-y v-if="notIsVendedor">
        <template v-slot:activator="{ on }">
          <v-chip class="ma-2" text-color="white" v-on="on">
            <v-avatar left class="green darken-4">
              {{ registros.length }}
            </v-avatar>
            Movimientos recientes
          </v-chip>
        </template>
        <v-card>
          <v-list two-line disabled>
            <v-list-item-group>
              <template v-for="(item, index) in registros">
                <v-list-item :key="item.id">
                  <v-list-item-content>
                    <v-list-item-title
                      v-text="item.codigo_stocks"
                    ></v-list-item-title>

                    <v-list-item-subtitle
                      class="text--primary"
                      v-text="item.movimientos"
                    ></v-list-item-subtitle>

                    <v-list-item-subtitle
                      v-text="item.descripcion"
                    ></v-list-item-subtitle>
                  </v-list-item-content>

                  <v-list-item-action>
                    <v-list-item-action-text
                      v-text="`${item.usuarios.full_name}-${item.created_at}`"
                    ></v-list-item-action-text>
                  </v-list-item-action>
                </v-list-item>

                <v-divider
                  v-if="index < registros.length - 1"
                  :key="index"
                ></v-divider>
              </template>
            </v-list-item-group>
          </v-list>
        </v-card>
      </v-menu>

      <v-menu bottom min-width="300px" rounded offset-y>
        <template v-slot:activator="{ on }">
          <v-btn icon x-large v-on="on">
            <v-avatar size="40">
              <img :src="iniciales" :alt="cui" />
            </v-avatar>
          </v-btn>
        </template>
        <v-card>
          <v-list-item-content class="justify-center">
            <div class="mx-auto text-center">
              <v-avatar size="100">
                <img :src="iniciales" :alt="cui" />
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
                <div class="my-2">
                  <v-btn
                    small
                    @click="dialog_caja = true"
                    outlined
                    color="primary"
                    v-if="notIsVendedor"
                  >
                    CAJA
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

      <v-dialog v-model="dialog_caja" width="80%" persistent>
        <v-card>
          <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
          </v-overlay>
          <v-card-title>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title class="text-h5">
                  Caja
                  <v-btn color="red darken-1" @click="dialog_caja = false">
                    Cerrar
                  </v-btn>
                </v-list-item-title>
                <v-list-item-subtitle
                  v-html="
                    `Hola ${userName}, actualmente estas operando con la empresa: <b>${empresa}</b>`
                  "
                ></v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-card-title>

          <v-card-text>
            <v-container>
              <Caja v-if="dialog_caja"></Caja>
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-app-bar>

    <v-main>
      <v-container class="fill-height" fluid :style="fondo">
        <router-view></router-view>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import FormError from './components/shared/FormError'
import Caja from './components/principal/extras/CajaComponent'

export default {
  components: {
    FormError,
    Caja,
  },
  data() {
    return {
      loading: false,
      dialog_password: false,
      dialog_configuration: false,
      dialog_caja: false,
      drawer: false,
      menu: false,
      form: {
        id: 0,
        password: null,
      },
      registros: [],
    }
  },
  created() {
    this.initialize()
  },

  methods: {
    initialize() {
      this.loading = true

      this.$store.state.services.selectController
        .movimientos_registrados(10)
        .then((r) => {
          this.loading = false
          if (r.response) {
            if (r.response.data.code === 404) {
              this.$toastr.warning(r.response.data.error, 'Advertencia')
              return
            } else if (r.response.data.code === 423) {
              this.$toastr.warning(r.response.data.error, 'Advertencia')
              return
            } else {
              for (let value of Object.values(r.response.data)) {
                this.$toastr.error(value, 'Mensaje')
              }
            }
            return
          }

          this.registros = r.data.data
        })
        .catch((r) => {
          this.loading = false
        })
    },

    logout() {
      let self = this
      self.loading = true
      self.$store.state.services.loginService
        .logout()
        .then((r) => {
          self.$store.dispatch('logout')
          self.$router.push('/login')
          self.drawer = null
          self.$store.state.is_login = false
          self.loading = false
        })
        .catch((e) => {})
    },

    redirect(item) {
      this.$router.push({ path: item })
    },

    mostar() {
      let self = this
      this.initialize()
      self.drawer = self.drawer ? false : true
    },

    cambiar_password() {
      this.loading = true
      this.form.id = this.$store.state.usuario.id
      this.dialog_password = true
      this.loading = false
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

    notIsVendedor() {
      let self = this
      return self.$store.state.roles.length > 0
        ? _.includes(self.$store.state.roles, 'GERENTE') ||
          _.includes(self.$store.state.roles, 'ADMINISTRADOR')
          ? true
          : false
        : false
    },

    isAdmin() {
      let self = this
      return self.$store.state.usuario
        ? self.$store.state.usuario.admin == 'ADMINISTRADOR'
          ? true
          : false
        : false
    },

    iniciales() {
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

    empresa() {
      return this.$store.state.empresa ? this.$store.state.empresa.nombre : null
    },

    logo() {
      return this.$store.state.empresa
        ? this.$store.state.empresa.logo_path
        : `${this.$store.state.base_url}img/logo.png`
    },

    fondo() {
      return `
          background-image: url('${this.$store.state.base_url}img/fondo.jpg');
          background-size: cover;
          background-position: top center;
          align-items: center;
        `
    },
  },
}
</script>
