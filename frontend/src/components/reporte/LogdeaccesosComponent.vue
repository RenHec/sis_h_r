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
            Reporte de stock por categoría y producto
          </span>
        </v-card-title>

        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12" md="5">
                <v-select
                  v-model="form.usuarios_id"
                  :items="respuesta.usuarios"
                  chips
                  label="seleccione un usuario por favor"
                  outlined
                  :clearable="true"
                  :deletable-chips="true"
                  item-text="full_name"
                  item-value="id"
                  return-object
                  :loading="loading"
                ></v-select>
              </v-col>
              <v-col cols="12" md="5">
                <v-select
                  v-model="form.acciones"
                  :items="respuesta.acciones"
                  chips
                  label="seleccione uno o más acciones por favor"
                  outlined
                  multiple
                  :clearable="true"
                  :deletable-chips="true"
                  item-text="nombre"
                  item-value="id"
                  :loading="loading"
                ></v-select>
              </v-col>
              <v-col cols="12" md="2">
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
  name: 'Movimientoenbodegas',
  components: { FormError },
  data() {
    return {
      loading: false,
      form: {
        usuarios_id: null,
        acciones: [],
      },
      respuesta: {
        pdf: null,
        usuarios: [],
        acciones: [],
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
        .logs_inicio_session(this.form)
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
          this.respuesta.usuarios = r.data.usuarios
          this.respuesta.acciones = r.data.acciones
        })
        .catch((r) => {
          this.loading = false
        })
    },
  },
}
</script>
