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
                  v-model="form.productos_id"
                  :items="productos"
                  chips
                  label="seleccione un producto por favor"
                  outlined
                  :clearable="true"
                  :deletable-chips="true"
                  item-text="nombre"
                  item-value="id"
                  return-object
                  :loading="loading"
                ></v-select>
              </v-col>
              <v-col cols="12" md="5">
                <v-select
                  v-model="form.categorias_id"
                  :items="categorias"
                  chips
                  label="seleccione una categoría por favor"
                  outlined
                  :clearable="true"
                  :deletable-chips="true"
                  item-text="nombre"
                  item-value="id"
                  return-object
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
  name: 'Stockporcategoriayproducto',
  components: { FormError },
  data() {
    return {
      loading: false,
      form: {
        categorias_id: null,
        productos_id: null,
      },
      respuesta: {
        pdf: null,
      },
      categorias: [],
      productos: [],
    }
  },

  created() {
    this.consulta()
    this.getSelects()
  },

  methods: {
    consulta() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          this.loading = true
          this.respuesta.pdf = null
          this.$store.state.services.reporteController
            .stock_categoria_producto(this.form)
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

    getSelects() {
      this.$store.state.services.selectController
        .categoria()
        .then((r) => {
          this.categorias = r.data.data
        })
        .catch((r) => {})

      this.$store.state.services.productoController
        .index()
        .then((r) => {
          this.productos = r.data.data
        })
        .catch((r) => {})
    },
  },
}
</script>
