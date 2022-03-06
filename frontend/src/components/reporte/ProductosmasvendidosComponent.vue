<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-col cols="12" md="12">
      <v-card>
        <v-overlay :value="loading">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-card-title>
          <span class="headline">
            Reporte de productos más vendidos y menos vendidos
          </span>
        </v-card-title>

        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12" md="4">
                <label>cantidad para verificar</label>
                <vue-number-input
                  v-model="form.cantidad"
                  :min="1"
                  :max="1000"
                  controls
                  placeholder="cantidad"
                  data-vv-name="cantidad"
                  v-validate="'required'"
                ></vue-number-input>
                <FormError
                  :attribute_name="'cantidad'"
                  :errors_form="errors"
                ></FormError>
              </v-col>
              <v-col cols="12" md="4">
                <v-btn
                  block
                  x-large
                  class="ma-2 mb-2"
                  color="success darken-1"
                  @click="consulta()"
                  :loading="loading"
                >
                  Consulta
                </v-btn>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" md="12" v-if="respuesta.pdf">
                <embed
                  :src="respuesta.pdf"
                  width="100%"
                  height="930"
                  style="
                    overflow: hidden;
                    overflow-x: hidden;
                    overflow-y: hidden;
                    border: 1px solid #666ccc;
                  "
                  title="Comprobante"
                  frameborder="1"
                />
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import FormError from '../shared/FormError'

export default {
  name: 'Productosmasvendidos',
  components: { FormError },
  data() {
    return {
      loading: false,
      form: {
        cantidad: 10,
      },
      respuesta: {
        pdf: null,
      },
    }
  },

  created() {
    this.consulta()
  },

  methods: {
    consulta() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          this.loading = true
          this.respuesta.pdf = null
          this.$store.state.services.reporteController
            .mas_menos_vendidos(this.form)
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

              this.respuesta.pdf = r.data.pdf
            })
            .catch((r) => {
              this.loading = false
            })
        }
      })
    },
  },
}
</script>
