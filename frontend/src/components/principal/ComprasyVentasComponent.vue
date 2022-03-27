<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-col cols="12" md="12">
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
            <v-toolbar-title>Compras y Ventas</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-text-field
              counter
              outlined
              v-model="search"
              append-icon="search"
              label="Buscar"
              single-line
              hide-details
            ></v-text-field>
            <v-spacer></v-spacer>
            <v-btn color="success darken-1" @click="initialize">
              Actualizar
            </v-btn>
          </v-toolbar>
        </template>
        <template v-slot:item.codigo="{ item }">
          <v-tooltip
            bottom
            v-if="item.movimientos_id != 8 && item.puede_anular"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                color="success"
                dark
                v-bind="attrs"
                v-on="on"
                @click="anular(item)"
              >
                {{ item.codigo }}
              </v-btn>
            </template>
            <span>Anular</span>
          </v-tooltip>
          <v-btn color="success" dark v-bind="attrs" v-on="on" disabled v-else>
            {{ item.codigo }}
          </v-btn>
        </template>
        <template v-slot:item.subtotal="{ item }">
          {{ formato_moneda(item.subtotal) }}
        </template>
        <template v-slot:item.total="{ item }">
          {{ formato_moneda(item.total) }}
        </template>
        <template v-slot:item.articulos="{ item }">
          <div class="text-center">
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  color="primary"
                  small
                  dark
                  fab
                  v-bind="attrs"
                  v-on="on"
                  @click="show(item)"
                >
                  {{ item.articulos }}
                </v-btn>
              </template>
              <span>Ver más</span>
            </v-tooltip>
            <v-tooltip bottom v-if="item.movimientos_id != 5">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  color="default"
                  large
                  dark
                  icon
                  fab
                  v-bind="attrs"
                  v-on="on"
                  @click="imprimir_documento(item.id)"
                >
                  <v-icon>print</v-icon>
                </v-btn>
              </template>
              <span>Imprimir comprobante</span>
            </v-tooltip>
          </div>
        </template>
        <template v-slot:no-data>
          <br />
          <v-alert type="error">No hay información para mostrar.</v-alert>
        </template>
      </v-data-table>

      <v-dialog
        v-model="dialog"
        color="primary"
        width="75%"
        v-if="dialog"
        persistent
      >
        <v-card>
          <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
          </v-overlay>
          <v-card-title>
            <span class="headline">
              {{ `Detalle del código ${informacion.codigo}` }}
            </span>
            <v-spacer></v-spacer>
            <v-tooltip bottom v-if="informacion.movimientos_id != 5">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  color="default"
                  small
                  dark
                  icon
                  fab
                  v-bind="attrs"
                  v-on="on"
                  @click="imprimir_documento(informacion.id)"
                >
                  <v-icon>print</v-icon>
                </v-btn>
              </template>
              <span>Imprimir comprobante</span>
            </v-tooltip>
            <v-spacer></v-spacer>
            <v-btn color="red darken-1" small @click="close">
              Cerrar
            </v-btn>
          </v-card-title>

          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12" md="12">
                  <v-simple-table dark>
                    <template v-slot:default>
                      <thead>
                        <tr>
                          <th class="text-center">
                            ARTICULO
                          </th>
                          <th class="text-center">
                            CANTIDAD
                          </th>
                          <th class="text-center">
                            PRODUCTO
                          </th>
                          <th class="text-center">
                            PRESENTACION
                          </th>
                          <th class="text-center">
                            PRECIO
                          </th>
                          <th class="text-center">
                            SUB TOTAL Q
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr
                          v-for="(item, key) in informacion.detalle"
                          :key="key"
                        >
                          <td class="ma-2 mb-2">
                            <div class="my-2">
                              {{ key + 1 }}
                            </div>
                          </td>
                          <td width="14%">
                            <br />
                            {{ item.cantidad_real }}
                          </td>
                          <td width="16%">
                            <br />
                            <v-tooltip
                              bottom
                              v-if="
                                item.movimientos_id == 4 &&
                                informacion.puede_anular
                              "
                            >
                              <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                  class="font-italic green--text"
                                  small
                                  dark
                                  text
                                  v-bind="attrs"
                                  v-on="on"
                                  @click="devolucion(item)"
                                >
                                  {{
                                    item.asignar_presentaciones.presentaciones
                                  }}
                                </v-btn>
                              </template>
                              <span>Aplicar devolución</span>
                            </v-tooltip>
                            <p class="font-italic green--text" v-else>
                              {{ item.nombre_producto }}
                            </p>
                          </td>
                          <td width="30%">
                            <br />
                            {{ item.asignar_presentaciones.presentaciones }}
                          </td>
                          <td>
                            <br />
                            {{ formato_moneda(item.precio) }}
                          </td>
                          <td width="20%">
                            <br />
                            <p class="title text-md-right">
                              {{ formato_moneda(item.total) }}
                            </p>
                          </td>
                        </tr>
                      </tbody>
                      <tfoot>
                        <tr>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td>
                            <hr />
                            <br />
                            <p
                              class="display-1 font-weight-black text-md-right"
                            >
                              Total
                            </p>
                          </td>
                          <td>
                            <hr />
                            <br />
                            <p
                              class="display-1 font-weight-black text-md-right"
                            >
                              {{ formato_moneda(informacion.total) }}
                            </p>
                          </td>
                        </tr>
                      </tfoot>
                    </template>
                  </v-simple-table>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-dialog v-model="dialog_print" max-width="50%" persistent>
        <v-card color="success">
          <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
          </v-overlay>
          <v-card-title>
            <span class="headline">
              Imprimir documento
            </span>
            <v-spacer></v-spacer>
            <v-btn color="red darken-1" @click="cerrar_print()">
              Cerrar
            </v-btn>
          </v-card-title>

          <v-card-text v-if="documento_imprimir">
            <v-container>
              <v-row>
                <v-col cols="12" md="12">
                  <embed
                    :src="documento_imprimir"
                    type="application/pdf"
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
      </v-dialog>

      <v-dialog
        v-model="dialog_devolucion"
        persistent
        color="primary"
        width="50%"
        v-if="informacion_dev"
      >
        <v-card>
          <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
          </v-overlay>
          <v-card-title>
            <span class="headline">
              {{ `Devolución del producto ${informacion_dev.nombre_completo}` }}
            </span>
          </v-card-title>

          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12" md="12">
                  <label>cantidad a devolver</label>
                  <vue-number-input
                    v-model="form.cantidad"
                    :min="1"
                    :max="informacion_dev.cantidad"
                    controls
                    placeholder="cantidad"
                    data-vv-scope="devolucion"
                    data-vv-name="cantidad"
                    v-validate="'required'"
                  ></vue-number-input>
                  <FormError
                    :attribute_name="'devolucion.cantidad'"
                    :errors_form="errors"
                  ></FormError>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="red darken-1" @click="close_devolucion">
              Cancelar
            </v-btn>
            <v-btn
              color="blue darken-1"
              @click="aplicar_devolucion('devolucion')"
            >
              Guardar
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-col>
  </v-row>
</template>

<script>
export default {
  name: 'ComprasyVentas',
  data() {
    return {
      loading: false,
      dialog: false,

      desserts: [],
      search: null,
      headers: [
        {
          text: 'Código',
          align: 'start',
          value: 'codigo',
        },
        {
          text: 'Número de documentos',
          align: 'start',
          value: 'numero_documento',
        },
        {
          text: 'NIT',
          align: 'start',
          value: 'nit',
        },
        {
          text: 'Persona',
          align: 'start',
          value: 'persona',
        },
        {
          text: 'Fecha',
          align: 'start',
          value: 'fecha',
        },
        {
          text: 'Sub Total Q',
          align: 'start',
          value: 'subtotal',
        },
        {
          text: 'Total Q',
          align: 'start',
          value: 'total',
        },
        {
          text: 'Artículos',
          align: 'start',
          value: 'articulos',
        },
      ],
      footer: {
        showFirstLastPage: true,
        firstIcon: 'mdi-arrow-collapse-left',
        lastIcon: 'mdi-arrow-collapse-right',
        prevIcon: 'mdi-minus',
        nextIcon: 'mdi-plus',
      },

      informacion: null,

      dialog_print: false,
      documento_imprimir: null,

      informacion_dev: null,

      dialog_devolucion: false,
      form: {
        id: 0,
        cantidad: 0,
      },
    }
  },

  created() {
    this.initialize()
  },

  methods: {
    initialize() {
      this.loading = true

      this.$store.state.services.compraVentaController
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
        })
        .catch((r) => {
          this.loading = false
        })
    },

    anular(data) {
      this.$swal({
        title: 'Anular',
        text: '¿Está seguro de realizar esta acción?',
        type: 'error',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.compraVentaController
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
            })
            .catch((r) => {
              this.loading = false
            })
        }
      })
    },

    imprimir_documento(id) {
      this.$swal({
        title: 'Imprimir comprobante',
        text: '¿Está seguro de realizar esta acción?',
        type: 'info',
        showCancelButton: true,
        confirmButtonText: 'Imprimir',
        allowOutsideClick: () => {
          const popup = this.$swal.getPopup()
          popup.classList.remove('swal2-show')
          setTimeout(() => {
            popup.classList.add('animate__animated', 'animate__headShake')
          })
          setTimeout(() => {
            popup.classList.remove('animate__animated', 'animate__headShake')
          }, 500)
          return false
        },
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.reporteController
            .ticket(id)
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

              this.$toastr.success(r.data.data, 'Mensaje')
              this.dialog_print = true
              this.documento_imprimir = r.data.path
            })
            .catch((r) => {
              this.loading = false
            })
        }
      })
    },

    show(item) {
      this.informacion = item
      this.dialog = true
    },

    close() {
      this.informacion = null
      this.dialog = false
    },

    cerrar_print() {
      this.dialog_print = false
      this.documento_imprimir = false
    },

    devolucion(item) {
      this.loading = true
      this.form.id = item.id
      this.form.cantidad = 0
      this.informacion_dev = item
      this.dialog_devolucion = true

      this.$validator.reset()
      this.$validator.reset()
      this.loading = false
    },

    close_devolucion() {
      this.form.id = 0
      this.form.cantidad = 0
      this.dialog_devolucion = false
      this.informacion_dev = null

      this.$validator.reset()
      this.$validator.reset()
    },

    aplicar_devolucion() {
      this.$swal({
        title: 'Aplicar devolución',
        text: '¿Está seguro de realizar esta acción?',
        type: 'info',
        showCancelButton: true,
        confirmButtonText: 'Imprimir',
        allowOutsideClick: () => {
          const popup = this.$swal.getPopup()
          popup.classList.remove('swal2-show')
          setTimeout(() => {
            popup.classList.add('animate__animated', 'animate__headShake')
          })
          setTimeout(() => {
            popup.classList.remove('animate__animated', 'animate__headShake')
          }, 500)
          return false
        },
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.devolucionController
            .update(this.form)
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
              this.close_devolucion()
              this.close()
              this.initialize()
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
