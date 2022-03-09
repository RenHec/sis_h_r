<template>
  <v-row align="center" justify="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
    <v-col cols="12" md="4">
      <v-card class="elevation-12 text-center">
        <v-img
          :aspect-ratio="14 / 10"
          class="img img-fluid img-thumbnail rounded"
          :src="empresa.logo"
        ></v-img>
        <v-card-subtitle class="display-2 text-bold">
          Inicio de Sesión
        </v-card-subtitle>
        <v-card-subtitle class="display-1">
          {{ empresa.nombre }}
        </v-card-subtitle>
        <v-card-text>
          <v-form>
            <v-text-field
              type="text"
              v-model="credentials.cui"
              name="número de dpi"
              prepend-icon="mdi-account"
              placeholder="número de dpi"
              v-validate="'required|numeric'"
              :class="{
                input: true,
                'has-errors': errors.has('número de dpi'),
              }"
            ></v-text-field>
            <FormError
              :attribute_name="'número de dpi'"
              :errors_form="errors"
            ></FormError>

            <v-text-field
              @keypress.enter="beforeLogin"
              type="password"
              name="contraseña"
              prepend-icon="mdi-lock"
              v-model="credentials.password"
              placeholder="Password"
              v-validate="'required'"
              :class="{ input: true, 'has-errors': errors.has('contraseña') }"
            ></v-text-field>
            <FormError
              :attribute_name="'contraseña'"
              :errors_form="errors"
            ></FormError>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="beforeLogin" x-large color="primary">INGRESAR</v-btn>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import FormError from '../shared/FormError'
import auth from '../../auth'
export default {
  name: 'Login',
  components: {
    FormError,
  },

  data() {
    return {
      loading: false,
      credentials: {
        cui: '',
        password: '',
      },
      empresa: {
        nombre: null,
        logo: null,
      },
    }
  },

  created() {
    this.configracion()
  },

  methods: {
    login() {
      let self = this
      self.loading = true
      self.$store.state.services.loginService
        .login(self.credentials)
        .then((r) => {
          self.loading = false
          if (self.$store.state.global.captureError(r)) {
            self.loading = false
            return
          }
          self.$store.dispatch('guardarToken', r.data)
          auth.getUser()
          self.$router.push('/')
        })
        .catch((e) => {
            this.$toastr.error(e, 'Error')
        })
        .finally(()=>{self.loading = false})
    },

    beforeLogin() {
      let self = this
      self.$validator.validateAll().then((result) => {
        if (result) {
          self.login()
        }
      })
    },

    configracion() {
      this.empresa.nombre = 'Sistema POS'
      this.empresa.logo = `${this.$store.state.base_url}img/logo.png`
    },
  },
}
</script>
