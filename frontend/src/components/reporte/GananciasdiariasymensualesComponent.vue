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
            Reporte de ventas generales
          </span>
        </v-card-title>

        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12" md="5">
                <v-select
                  v-model="form.meses_id"
                  :items="meses"
                  chips
                  label="seleccione un mes por favor"
                  outlined
                  :clearable="true"
                  :deletable-chips="true"
                  item-text="nombre"
                  item-value="id"
                  return-object
                  :loading="loading"
                ></v-select>
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
  name: 'VentasGenerales',
  components: { FormError },
  data() {
    return {
      loading: false,
      form: {
        meses_id: null,
      },
      respuesta: {
        pdf: null,
      },
      meses: [],
    }
  },

  created() {
    this.consulta()
    this.getSelects()
  },

  methods: {
    consulta() {
      this.loading = true
      this.respuesta.pdf = null
      this.$store.state.services.reporteController
        .ganancia_producto(this.form)
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
    },

    getSelects() {
      this.$store.state.services.selectController
        .mes()
        .then((r) => {
          this.meses = r.data.data
        })
        .catch((r) => {})
    },
  },
}
</script>
