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
      <v-img :aspect-ratio="16 / 10" :src="logo"></v-img>
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
                <div class="my-2">
                  <v-btn small @click="aperturar_caja" outlined color="primary">
                    CAJA
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

      <v-dialog v-model="dialog_caja" width="30%" persistent>
        <v-card>
          <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
          </v-overlay>
          <v-card-title>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title class="text-h5">
                  Caja
                  <v-spacer></v-spacer>
                  <v-btn color="red darken-1" @click="dialog_caja = false">
                    <v-icon>close</v-icon>
                  </v-btn>
                </v-list-item-title>
                <v-list-item-subtitle
                  v-html="`Hola ${userName}</b>`"
                ></v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-card-title>

          <v-card-text v-if="registros.length === 0">
            <v-container>
              <v-row>
                <v-col cols="12"></v-col>
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
        inicia_caja: 0,
      },
      registros: [],
    }
  },
  created() {
    this.initialize()
  },

  methods: {
    initialize() {
      this.loading = false
    },

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
      let self = this
      self.loading = true
      self.registros = []

      self.$store.state.services.CajaService.getCreate()
        .then((r) => {
          self.registros = r.data.data
        })
        .catch((e) => {
          this.errorResponse(e)
        })
        .finally(() => {
          self.dialog_caja = true
          self.loading = false
        })
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
  },
}
</script>
