<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-col cols="12">
      <v-card>
        <v-overlay :value="loading">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-card-title>
          <span class="headline">Trasladar producto</span>
        </v-card-title>

        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12" md="5">
                <v-select
                  prepend-icon="view_carousel"
                  v-model="form.bodegas_solicita"
                  :items="bodegas_solicita"
                  @input="buscar_productos"
                  chips
                  label="seleccione una bodega por favor"
                  outlined
                  :clearable="true"
                  :deletable-chips="true"
                  item-text="full_name"
                  item-value="id"
                  return-object
                  v-validate="'required'"
                  data-vv-scope="traslado_producto"
                  data-vv-name="bodega"
                ></v-select>
                <FormError
                  :attribute_name="'traslado_producto.bodega'"
                  :errors_form="errors"
                ></FormError>
              </v-col>

              <v-col cols="12" md="1" class="ma-2 mb-2 text-center">
                <h3>TRASLADAR A</h3>
              </v-col>

              <v-col cols="12" md="5">
                <v-select
                  prepend-icon="view_carousel"
                  v-model="form.bodegas_autoriza"
                  :items="bodegas_autoriza"
                  chips
                  label="seleccione una bodega por favor"
                  outlined
                  :clearable="true"
                  :deletable-chips="true"
                  item-text="full_name"
                  item-value="id"
                  return-object
                  v-validate="'required'"
                  data-vv-scope="traslado_producto"
                  data-vv-name="bodega"
                ></v-select>
                <FormError
                  :attribute_name="'traslado_producto.bodega'"
                  :errors_form="errors"
                ></FormError>
              </v-col>

              <v-col cols="12" md="9">
                <v-select
                  prepend-icon="loyalty"
                  v-model="form.productos_id"
                  :items="productos"
                  chips
                  label="seleccione un producto por favor"
                  outlined
                  :clearable="true"
                  :deletable-chips="true"
                  item-text="producto"
                  item-value="id"
                  return-object
                  v-validate="'required'"
                  data-vv-scope="traslado_producto"
                  data-vv-name="producto"
                ></v-select>
                <FormError
                  :attribute_name="'traslado_producto.producto'"
                  :errors_form="errors"
                ></FormError>
              </v-col>

              <v-col cols="12" md="3" v-if="form.productos_id">
                <label>cantidad de stock a solicitar</label>
                <vue-number-input
                  v-model="form.cantidad_solicitada"
                  :min="1"
                  :max="form.productos_id ? form.productos_id.stock : 0"
                  controls
                  placeholder="cantidad solicitada"
                  data-vv-scope="traslado_producto"
                  data-vv-name="cantidad solicitada"
                  v-validate="'required'"
                ></vue-number-input>
                <FormError
                  :attribute_name="'traslado_producto.cantidad solicitada'"
                  :errors_form="errors"
                ></FormError>
              </v-col>

              <v-col cols="12" md="12">
                <v-textarea
                  class="mx-2"
                  rows="2"
                  prepend-icon="comment"
                  counter
                  outlined
                  v-model="form.descripcion"
                  type="text"
                  label="descripción"
                  data-vv-scope="traslado_producto"
                  data-vv-name="descripción"
                  v-validate="'max:150'"
                  @input="form.descripcion = $event.toUpperCase()"
                ></v-textarea>
                <FormError
                  :attribute_name="'traslado_producto.descripción'"
                  :errors_form="errors"
                ></FormError>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="red darken-1" @click="limpiar">
            Cancelar
          </v-btn>
          <v-btn
            color="blue darken-1"
            @click="validar_formulario('traslado_producto')"
          >
            Guardar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-col>

    <v-col cols="12">
      <v-data-table
        :headers="headers"
        :items="desserts"
        :search="search"
        sort-by="calories"
        class="elevation-1"
        dark
        :footer-props="footer"
      >
        <template v-slot:top>
          <v-toolbar flat>
            <v-text-field
              v-model="search"
              append-icon="search"
              label="Buscar"
              single-line
              hide-details
            ></v-text-field>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-spacer></v-spacer>
            <v-btn class="ma-2 mb-2" color="success" @click="initialize">
              Actualizar
            </v-btn>
          </v-toolbar>
        </template>
        <template v-slot:item.producto="{ item }">
          <br />
          <v-list-item two-line>
            <v-list-item-content>
              <v-list-item-title>
                {{ `Cantidad: ${item.cantidad_autorizada}` }}
              </v-list-item-title>
              <v-list-item-subtitle>
                {{ item.nombre_producto }}
              </v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>
          <br />
        </template>
        <template v-slot:item.solicita="{ item }">
          <br />
          <v-card class="mx-auto" max-width="200" tile color="primary">
            <v-list-item>
              <v-list-item-content>
                <v-list-item-title>
                  {{ item.usuarios_solicita }}
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>

            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Fecha</v-list-item-title>
                <v-list-item-subtitle>
                  {{ item.fecha_solicita }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>

            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Bodega</v-list-item-title>
                <v-list-item-subtitle>
                  {{ item.bodegas_solicita }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>

            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Empresa</v-list-item-title>
                <v-list-item-subtitle>
                  {{ item.empresas_solicita }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-card>
          <br />
        </template>
        <template v-slot:item.autoriza="{ item }">
          <br />
          <v-card
            class="mx-auto"
            max-width="200"
            tile
            color="success"
            v-if="item.usuarios_autoriza"
          >
            <v-list-item>
              <v-list-item-content>
                <v-list-item-title>
                  {{ item.usuarios_autoriza }}
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>

            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Fecha</v-list-item-title>
                <v-list-item-subtitle>
                  {{ item.fecha_autoriza }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>

            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Bodega</v-list-item-title>
                <v-list-item-subtitle>
                  {{ item.bodegas_autoriza }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>

            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Empresa</v-list-item-title>
                <v-list-item-subtitle>
                  {{ item.empresas_autoriza }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-card>
          <div class="text-center" v-else>
            <v-btn
              class="mx-2"
              icon
              fab
              dark
              large
              color="success"
              outlined
              @click="update(item)"
              v-if="
                !item.fecha_autoriza &&
                $store.state.empresa.id === item.empresas_autoriza_id
              "
            >
              <v-icon dark>mdi-check</v-icon>
            </v-btn>
          </div>
          <br />
        </template>
        <template v-slot:no-data>
          <br />
          <v-alert type="error">No hay información para mostrar.</v-alert>
        </template>
      </v-data-table>
    </v-col>
  </v-row>
</template>

<script>
import FormError from '../shared/FormError'
export default {
  name: 'TrasladosBodega',
  components: {
    FormError,
  },
  data() {
    return {
      loading: false,
      search: '',
      headers: [
        {
          text: 'Código',
          align: 'start',
          value: 'codigo',
        },
        {
          text: 'Producto',
          align: 'start',
          value: 'producto',
        },
        {
          text: 'Traslada',
          align: 'start',
          value: 'solicita',
        },
        {
          text: 'Autoriza',
          align: 'start',
          value: 'autoriza',
        },
        {
          text: 'Descripción',
          align: 'start',
          value: 'descripcion',
        },
      ],
      footer: {
        showFirstLastPage: true,
        firstIcon: 'mdi-arrow-collapse-left',
        lastIcon: 'mdi-arrow-collapse-right',
        prevIcon: 'mdi-minus',
        nextIcon: 'mdi-plus',
      },

      desserts: [],
      bodegas_solicita: [],
      bodegas_autoriza: [],
      productos: [],

      form: {
        id: 0,
        bodegas_solicita: null,
        bodegas_autoriza: null,
        productos_id: null,
        descripcion: null,
        cantidad_solicitada: null,
      },
    }
  },

  created() {
    this.initialize()
    this.getSelects()
  },

  methods: {
    limpiar() {
      this.form.id = 0
      this.form.bodegas_solicita = null
      this.form.bodegas_autoriza = null
      this.form.productos_id = null
      this.form.descripcion = null
      this.form.cantidad_solicitada = null
      this.productos = []

      this.$validator.reset()
      this.$validator.reset()
    },

    initialize() {
      this.loading = true

      this.$store.state.services.trasladoStockController
        .index()
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

          this.desserts = r.data.data
          this.limpiar()
        })
        .catch((r) => {
          this.loading = false
        })
    },

    validar_formulario(scope) {
      this.$validator.validateAll(scope).then((result) => {
        if (result) this.store(this.form)
      })
    },

    store(data) {
      this.$swal({
        title: 'Agregar',
        text: '¿Está seguro de realizar esta acción?',
        type: 'success',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.trasladoStockController
            .store(data)
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

              this.$toastr.success(r.data, 'Mensaje')
              this.initialize()
            })
            .catch((r) => {
              this.loading = false
            })
        }
      })
    },

    update(data) {
      this.$swal({
        title: 'Autorizar traslado',
        text: '¿Está seguro de realizar esta acción?',
        type: 'warning',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.trasladoStockController
            .update(data)
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

              this.$toastr.success(r.data, 'Mensaje')
              this.initialize()
            })
            .catch((r) => {
              this.loading = false
            })
        }
      })
    },

    getSelects() {
      this.$store.state.services.selectController
        .bodega()
        .then((r) => {
          this.bodegas_solicita = r.data.bodegas_asignadas
          this.bodegas_autoriza = r.data.bodegas_no_asignadas
        })
        .catch((r) => {})
    },

    buscar_productos(item) {
      this.productos = []
      if (item) {
        this.loading = true

        this.$store.state.services.selectController
          .producto_sin_id(null, this.$store.state.empresa.id, item.id)
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

            this.productos = r.data.data
          })
          .catch((r) => {
            this.loading = false
          })
      }
    },
  },
}
</script>
