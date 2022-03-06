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
              <v-col cols="12" md="4">
                <v-dialog
                  ref="fecha_inicio"
                  v-model="dialogo_fecha_inicio"
                  :return-value.sync="form.fecha_inicio"
                  persistent
                  width="290px"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="form.fecha_inicio"
                      label="fecha de inicio"
                      prepend-icon="mdi-calendar"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      class="ma-2 mb-2"
                      :loading="loading"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="form.fecha_inicio"
                    scrollable
                    no-title
                  >
                    <v-spacer></v-spacer>
                    <v-btn color="error" @click="dialogo_fecha_inicio = false">
                      Cerrar
                    </v-btn>
                    <v-btn
                      color="primary"
                      @click="$refs.fecha_inicio.save(form.fecha_inicio)"
                    >
                      Seleccionar
                    </v-btn>
                  </v-date-picker>
                </v-dialog>
              </v-col>
              <v-col cols="12" md="4">
                <v-dialog
                  ref="fecha_finaliza"
                  v-model="dialogo_fecha_finaliza"
                  :return-value.sync="form.fecha_finaliza"
                  persistent
                  width="290px"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="form.fecha_finaliza"
                      label="fecha de finaliza"
                      prepend-icon="mdi-calendar"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      class="ma-2 mb-2"
                      :loading="loading"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="form.fecha_finaliza"
                    scrollable
                    no-title
                  >
                    <v-spacer></v-spacer>
                    <v-btn
                      color="error"
                      @click="dialogo_fecha_finaliza = false"
                    >
                      Cerrar
                    </v-btn>
                    <v-btn
                      color="primary"
                      @click="$refs.fecha_finaliza.save(form.fecha_finaliza)"
                    >
                      Seleccionar
                    </v-btn>
                  </v-date-picker>
                </v-dialog>
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
      dialogo_fecha_inicio: false,
      dialogo_fecha_finaliza: false,
      form: {
        fecha_inicio: null,
        fecha_finaliza: null,
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
      this.loading = true
      this.respuesta.pdf = null
      this.$store.state.services.reporteController
        .ventas_generales(this.form)
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
  },
}
</script>
