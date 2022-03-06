<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-col cols="12" v-if="desserts.length > 0">
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
        <template v-slot:item.codigo="{ item }">
          <br />
          {{ `Código del stock: ${item.codigo}` }}
          <br />
          {{ `Código del producto de fábrica: ${item.codigo_real}` }}
          <br />
          {{ `Código del producto interno: ${item.codigo_sistema}` }}
          <br />
        </template>
        <template v-slot:item.stock="{ item }">
          <v-chip
            class="ma-2"
            color="blue darken-4"
            text-color="white"
            disabled
          >
            {{ item.stock_historico.toLocaleString() }} Stock histórico
          </v-chip>
          <br />
          <v-chip class="ma-2" color="green" text-color="white" disabled>
            {{ item.stock_actual.toLocaleString() }} Stock actual
          </v-chip>
          <br />
          <v-chip
            class="ma-2"
            color="orange darken-4"
            text-color="white"
            :disabled="item.stock_historico === 0 ? false : true"
            @click="mapear(item)"
          >
            {{ item.stock_inicial.toLocaleString() }} Stock inicial
          </v-chip>
          <br />
          <v-chip
            class="ma-2"
            color="teal darken-4"
            text-color="white"
            disabled
          >
            {{ item.stock_compra.toLocaleString() }} Stock por compra
          </v-chip>
          <br />
          <v-chip
            class="ma-2"
            color="cyan darken-1"
            text-color="white"
            disabled
          >
            {{ item.stock_venta.toLocaleString() }} Stock por venta
          </v-chip>
          <br />
          <v-chip
            class="ma-2"
            color="brown darken-4"
            text-color="white"
            disabled
          >
            {{ item.stock_traslado.toLocaleString() }} Stock por traslado
          </v-chip>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-btn
            class="ma-2"
            text
            icon
            :color="'green red-2'"
            @click="view(item)"
          >
            <v-icon v-text="'mdi-eye'"></v-icon>
          </v-btn>
          <v-btn
            class="ma-2"
            text
            icon
            :color="item.activo ? 'blue lighten-2' : 'red lighten-2'"
            @click="destroy(item)"
          >
            <v-icon
              v-text="item.activo ? 'mdi-thumb-up' : 'mdi-thumb-down'"
            ></v-icon>
          </v-btn>
        </template>
        <template v-slot:no-data>
          <br />
          <v-alert type="error">No hay información para mostrar.</v-alert>
        </template>
      </v-data-table>

      <v-dialog v-model="dialog" persistent color="primary" width="50%">
        <v-card>
          <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
          </v-overlay>
          <v-card-title>
            <span class="headline">Apertura de Stock</span>
          </v-card-title>

          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12" md="12">
                  <v-dialog
                    ref="dialogo_vencimiento"
                    v-model="dialogo_vencimiento"
                    :return-value.sync="form.vencimiento"
                    persistent
                    width="290px"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="form.vencimiento"
                        label="fecha de vencimiento"
                        prepend-icon="mdi-calendar"
                        readonly
                        v-bind="attrs"
                        v-on="on"
                        data-vv-scope="apertura_stock"
                        data-vv-name="fecha de vencimiento"
                      ></v-text-field>
                      <FormError
                        :attribute_name="'apertura_stock.fecha de vencimiento'"
                        :errors_form="errors"
                      ></FormError>
                    </template>
                    <v-date-picker
                      v-model="form.vencimiento"
                      scrollable
                      :min="new Date().toISOString().substr(0, 10)"
                      no-title
                    >
                      <v-spacer></v-spacer>
                      <v-btn color="error" @click="dialogo_vencimiento = false">
                        Cerrar
                      </v-btn>
                      <v-btn
                        color="primary"
                        @click="
                          $refs.dialogo_vencimiento.save(form.vencimiento)
                        "
                      >
                        Seleccionar
                      </v-btn>
                    </v-date-picker>
                  </v-dialog>
                </v-col>
                <v-col cols="12" md="12">
                  <label>cantidad de stock para aperturar</label>
                  <vue-number-input
                    v-model="form.stock"
                    :min="1"
                    :max="100000"
                    controls
                    placeholder="stock de apertura"
                    data-vv-scope="apertura_stock"
                    data-vv-name="stock de apertura"
                    v-validate="'required'"
                  ></vue-number-input>
                  <FormError
                    :attribute_name="'apertura_stock.stock de apertura'"
                    :errors_form="errors"
                  ></FormError>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="red darken-1" @click="dialog = false">
              Cancelar
            </v-btn>
            <v-btn
              color="blue darken-1"
              @click="validar_formulario('apertura_stock')"
            >
              Guardar
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="dialog_view" persistent color="primary" width="90%">
        <v-card>
          <v-card-text>
            <br />
            <div class="text-center">
              <v-avatar size="100" tile>
                <img :src="info.path" :alt="info.nombre_completo" />
              </v-avatar>
              <br />
              <div class="text-h5">{{ `Stock Cod: ${info.codigo}` }}</div>

              <br />
              {{ info.nombre_completo }}
            </div>
            <hr />
            <v-container>
              <InformacionStock
                :stock_id="info.stock_id"
                ref="recargarComponent"
                v-if="dialog_view"
              ></InformacionStock>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="red darken-1" @click="dialog_view = false">
              Cerrar
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-col>
  </v-row>
</template>

<script>
import FormError from '../../shared/FormError'
import InformacionStock from '../extras/InformacionStockComponent'
export default {
  name: 'Stock',
  components: {
    FormError,
    InformacionStock,
  },
  data() {
    return {
      loading: false,
      dialog: false,
      dialogo_vencimiento: false,
      dialog_view: false,
      search: '',
      headers: [
        {
          text: 'Código',
          align: 'start',
          value: 'codigo',
          sortable: false,
        },
        {
          text: 'Producto',
          align: 'start',
          value: 'nombre_producto',
        },
        {
          text: 'Stock',
          align: 'start',
          value: 'stock',
          sortable: false,
        },
        {
          text: 'Categorias',
          align: 'start',
          value: 'nombre_categoria',
        },
        {
          text: 'Marca',
          align: 'start',
          value: 'nombre_marca',
        },
        {
          text: 'Bodega',
          align: 'start',
          value: 'nombre_bodega',
        },
        { text: 'Opciones', value: 'actions', sortable: false },
      ],
      footer: {
        showFirstLastPage: true,
        firstIcon: 'mdi-arrow-collapse-left',
        lastIcon: 'mdi-arrow-collapse-right',
        prevIcon: 'mdi-minus',
        nextIcon: 'mdi-plus',
      },
      desserts: [],
      form: {
        id: 0,
        stock: 0,
        vencimiento: null,
      },
      info: {
        stock_id: 0,
        codigo: null,
        nombre_completo: null,
        path: null,
      },
    }
  },

  created() {
    this.initialize()
  },

  methods: {
    initialize() {
      this.loading = true

      this.$store.state.services.stockController
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
          this.dialog = false
        })
        .catch((r) => {
          this.loading = false
        })
    },

    mapear(item) {
      this.form.id = item.id
      this.form.stock = 0
      this.form.vencimiento = null

      this.$validator.reset()
      this.$validator.reset()

      this.dialog = true
    },

    validar_formulario(scope) {
      this.$validator.validateAll(scope).then((result) => {
        if (result) this.update(this.form)
      })
    },

    destroy(data) {
      this.$swal({
        title: 'Eliminar',
        text: '¿Está seguro de realizar esta acción?',
        type: 'error',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.stockController
            .destroy(data)
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
              vm.$forceUpdate()
            })
            .catch((r) => {
              this.loading = false
            })
        }
      })
    },

    update(data) {
      this.$swal({
        title: 'Apertura de Stock',
        text: '¿Está seguro de realizar esta acción?',
        type: 'warning',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.stockController
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

    view(item) {
      this.info.stock_id = item.id
      this.info.codigo = item.codigo
      this.info.nombre_completo = item.nombre_completo
      this.info.path = item.productos_join.logo_path
      typeof this.$refs.recargarComponent === 'undefined'
        ? null
        : this.$refs.recargarComponent.initialize(item.id)
      this.dialog_view = true
    },
  },
}
</script>
