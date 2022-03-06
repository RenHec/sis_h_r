<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-col cols="12" md="12" v-if="renderizar">
      <v-data-table
        :headers="headers_historiales"
        :items="items_historiales"
        :search="search_historiales"
        sort-by="calories"
        class="elevation-1"
        dark
        :footer-props="footer_historiales"
      >
        <template v-slot:top>
          <v-toolbar flat>
            <v-toolbar-title>Movimientos</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-text-field
              v-model="search_historiales"
              append-icon="search"
              label="Buscar"
              single-line
              hide-details
            ></v-text-field>
          </v-toolbar>
        </template>
        <template v-slot:no-data>
          <br />
          <v-alert type="error">No hay información para mostrar.</v-alert>
        </template>
      </v-data-table>
    </v-col>

    <v-col cols="12" md="6" v-if="renderizar">
      <v-data-table
        :headers="headers_presentaciones"
        :items="items_presentaciones"
        :search="search_presentaciones"
        sort-by="calories"
        class="elevation-1"
        dark
        :footer-props="footer_presentaciones"
      >
        <template v-slot:top>
          <v-toolbar flat>
            <v-toolbar-title>Presentaciones</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-text-field
              v-model="search_presentaciones"
              append-icon="search"
              label="Buscar"
              single-line
              hide-details
            ></v-text-field>

            <v-dialog
              v-model="dialog_asignar_presentacion"
              persistent
              color="primary"
              width="50%"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  :loading="dialog_asignar_presentacion"
                  :disabled="dialog_asignar_presentacion"
                  @click="asignar_presentacion()"
                  color="primary"
                  dark
                  class="ma-2 mb-2"
                  v-bind="attrs"
                  v-on="on"
                >
                  Agregar
                </v-btn>
              </template>
              <v-card>
                <v-overlay :value="loading">
                  <v-progress-circular
                    indeterminate
                    size="64"
                  ></v-progress-circular>
                </v-overlay>
                <v-card-title>
                  <span class="headline">Agregar presentación</span>
                </v-card-title>

                <v-card-text>
                  <v-container>
                    <v-row>
                      <v-col cols="12" md="12">
                        <v-select
                          prepend-icon="developer_board"
                          v-model="form.presentaciones_id"
                          :items="presentaciones"
                          chips
                          label="seleccione una presentación por favor"
                          outlined
                          :clearable="true"
                          :deletable-chips="true"
                          item-text="nombre"
                          item-value="id"
                          return-object
                          v-validate="'required'"
                          data-vv-scope="asignar_presentacion"
                          data-vv-name="presentación"
                        ></v-select>
                        <FormError
                          :attribute_name="'asignar_presentacion.presentación'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field
                          prepend-icon="local_mall"
                          counter
                          outlined
                          v-model="form.precio"
                          type="text"
                          label="precio de venta"
                          data-vv-scope="asignar_presentacion"
                          data-vv-name="precio de venta"
                          v-validate="'required|decimal:2'"
                          prefix="Q"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'asignar_presentacion.precio de venta'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="6">
                        <label>cantidad de stock a debitar</label>
                        <vue-number-input
                          v-model="form.debitar_stock"
                          :min="1"
                          :max="100000"
                          controls
                          placeholder="cantidad de stock a debitar"
                          data-vv-scope="asignar_presentacion"
                          data-vv-name="cantidad de stock a debitar"
                          v-validate="'required'"
                        ></vue-number-input>
                        <FormError
                          :attribute_name="'asignar_presentacion.cantidad de stock a debitar'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>

                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn
                    color="red darken-1"
                    @click="dialog_asignar_presentacion = false"
                  >
                    Cancelar
                  </v-btn>
                  <v-btn
                    color="blue darken-1"
                    @click="asignar('asignar_presentacion')"
                  >
                    Guardar
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </v-toolbar>
        </template>
        <template v-slot:item.principal="{ item }">
          <br />
          <v-chip
            class="ma-2"
            :color="item.principal ? 'primary' : 'default'"
            text-color="white"
            @click="volver_principal(item)"
          >
            {{ item.principal ? 'SI' : 'NO' }}
          </v-chip>
          <br />
        </template>
        <template v-slot:no-data>
          <br />
          <v-alert type="error">No hay información para mostrar.</v-alert>
        </template>
      </v-data-table>
    </v-col>

    <v-col cols="12" md="6" v-if="renderizar">
      <v-data-table
        :headers="headers_vencidos"
        :items="items_vencidos"
        :search="search_vencidos"
        sort-by="calories"
        class="elevation-1"
        dark
        :footer-props="footer_vencidos"
      >
        <template v-slot:top>
          <v-toolbar flat>
            <v-toolbar-title>Vencidos</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-text-field
              v-model="search_vencidos"
              append-icon="search"
              label="Buscar"
              single-line
              hide-details
            ></v-text-field>
          </v-toolbar>
        </template>
        <template v-slot:item.vencido="{ item }">
          <br />
          <v-chip
            class="ma-2"
            color="red"
            text-color="white"
            v-if="item.vencido"
          >
            Vencido
          </v-chip>

          <v-chip class="ma-2" color="green" text-color="white" v-else>
            En fecha
          </v-chip>
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
import FormError from '../../shared/FormError'
export default {
  name: 'InformacionStock',
  components: {
    FormError,
  },
  props: ['stock_id'],
  data() {
    return {
      loading: false,
      renderizar: false,
      dialog_asignar_presentacion: false,

      items_vencidos: [],
      search_vencidos: '',
      headers_vencidos: [
        {
          text: 'Cantidad',
          align: 'start',
          value: 'cantidad',
        },
        {
          text: 'Comprobante de compra',
          align: 'start',
          value: 'documento',
        },
        {
          text: 'Fecha de Vencimiento',
          align: 'start',
          value: 'vencimiento',
        },
        {
          text: 'Indicador',
          align: 'start',
          value: 'vencido',
        },
      ],
      footer_vencidos: {
        showFirstLastPage: true,
        firstIcon: 'mdi-arrow-collapse-left',
        lastIcon: 'mdi-arrow-collapse-right',
        prevIcon: 'mdi-minus',
        nextIcon: 'mdi-plus',
      },

      items_historiales: [],
      search_historiales: '',
      headers_historiales: [
        {
          text: 'Stock',
          align: 'start',
          value: 'codigo_stocks',
        },
        {
          text: 'Movimiento',
          align: 'start',
          value: 'movimientos',
        },
        {
          text: 'Cantidad Anterior',
          align: 'start',
          value: 'stock_anterior',
        },
        {
          text: 'Cantidad Actual',
          align: 'start',
          value: 'stock_actual',
        },
        {
          text: 'Información',
          align: 'start',
          value: 'descripcion',
        },
      ],
      footer_historiales: {
        showFirstLastPage: true,
        firstIcon: 'mdi-arrow-collapse-left',
        lastIcon: 'mdi-arrow-collapse-right',
        prevIcon: 'mdi-minus',
        nextIcon: 'mdi-plus',
      },

      items_presentaciones: [],
      search_presentaciones: '',
      headers_presentaciones: [
        {
          text: 'Presentación',
          align: 'start',
          value: 'presentaciones',
        },
        {
          text: 'Precio',
          align: 'start',
          value: 'precio',
        },
        {
          text: 'Debita',
          align: 'start',
          value: 'debitar_stock',
        },
        {
          text: 'Principal',
          align: 'start',
          value: 'principal',
        },
      ],
      footer_presentaciones: {
        showFirstLastPage: true,
        firstIcon: 'mdi-arrow-collapse-left',
        lastIcon: 'mdi-arrow-collapse-right',
        prevIcon: 'mdi-minus',
        nextIcon: 'mdi-plus',
      },

      presentaciones: [],
      form: {
        id: 0,
        presentaciones_id: null,
        precio: null,
        debitar_stock: null,
        stocks_id: null,
      },
    }
  },

  created() {
    this.initialize(this.stock_id)
  },

  methods: {
    initialize(item) {
      this.loading = true
      this.renderizar = false
      Promise.all([
        this.getVencidos(item),
        this.getPresentacionAsignada(item),
        this.getHistorialStocks(item),
        this.getSelects(),
      ])
        .then(() => {
          this.renderizar = true
          this.loading = false
        })
        .catch((error) => {
          this.$toastr.error('Ocurrió un error: ' + error, 'Error')
        })
    },

    getVencidos(item) {
      this.$store.state.services.selectController
        .vencidos(item)
        .then((r) => {
          this.items_vencidos = r.data.data
        })
        .catch((r) => {})
    },

    getPresentacionAsignada(item) {
      this.$store.state.services.selectController
        .presentacion_asignada(item)
        .then((r) => {
          this.items_presentaciones = r.data.data
        })
        .catch((r) => {})
    },

    getHistorialStocks(item) {
      this.$store.state.services.selectController
        .historial_stocks(item)
        .then((r) => {
          this.items_historiales = r.data.data
        })
        .catch((r) => {})
    },

    getSelects() {
      this.$store.state.services.selectController
        .presentacion()
        .then((r) => {
          this.presentaciones = r.data.data
        })
        .catch((r) => {})
    },

    volver_principal(item) {
      this.$swal({
        title: 'Presentación principal',
        text: '¿Está seguro de realizar esta acción?',
        type: 'warning',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.asignarPresentacionController
            .show(item.id)
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
              this.initialize(this.stock_id)
            })
            .catch((r) => {
              this.loading = false
            })
        }
      })
    },

    asignar_presentacion() {
      this.form.id = 0
      this.form.presentaciones_id = null
      this.form.precio = null
      this.form.debitar_stock = null
      this.form.stocks_id = this.stock_id

      this.$validator.reset()
      this.$validator.reset()
      this.dialog_asignar_presentacion = true
    },

    asignar(scope) {
      this.$validator.validateAll(scope).then((result) => {
        if (result) {
          this.$swal({
            title: 'Agregar',
            text: '¿Está seguro de realizar esta acción?',
            type: 'success',
            showCancelButton: true,
          }).then((result) => {
            if (result.value) {
              this.loading = true
              this.$store.state.services.asignarPresentacionController
                .store(this.form)
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
                  this.initialize(this.stock_id)
                  this.asignar_presentacion()
                })
                .catch((r) => {
                  this.loading = false
                })
            }
          })
        }
      })
    },
  },
}
</script>
