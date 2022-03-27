<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-col cols="12" md="12">
      <v-row>
        <v-col cols="12" md="1"></v-col>
        <v-col cols="12" md="6">
          <v-select
            prepend-icon="account_circle"
            v-model="form.usuarios_id"
            :items="vendedores"
            chips
            label="seleccione un vendedor por favor"
            outlined
            :clearable="true"
            :deletable-chips="true"
            item-text="full_name"
            item-value="id"
            return-object
            v-validate="'required'"
            data-vv-scope="crear_caja"
            data-vv-name="vendedor"
          ></v-select>
          <FormError
            :attribute_name="'crear_caja.vendedor'"
            :errors_form="errors"
          ></FormError>
        </v-col>
        <v-col cols="12" md="3">
          <v-text-field
            prepend-icon="local_mall"
            counter
            outlined
            v-model="form.inicia_caja"
            type="text"
            label="monto de apertura"
            data-vv-scope="crear_caja"
            data-vv-name="monto de apertura"
            v-validate="'required|decimal:2'"
            prefix="Q"
          ></v-text-field>
          <FormError
            :attribute_name="'crear_caja.monto de apertura'"
            :errors_form="errors"
          ></FormError>
        </v-col>
        <v-col cols="12" md="2">
          <v-btn
            color="blue darken-1 mb-2"
            @click="crear_caja('crear_caja')"
            x-large
          >
            Guardar
          </v-btn>
        </v-col>
      </v-row>
    </v-col>

    <v-col cols="12" md="12" v-if="desserts.length > 0">
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
            <v-btn class="ma-2 mb-2" color="success" @click="initialize">
              Actualizar
            </v-btn>
          </v-toolbar>
        </template>
        <template v-slot:item.montos="{ item }">
          <br />
          <v-card class="mx-auto" max-width="200" tile>
            <v-list-item>
              <v-list-item-content>
                <v-list-item-title>Montos Q</v-list-item-title>
              </v-list-item-content>
            </v-list-item>

            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Apertura +</v-list-item-title>
                <v-list-item-subtitle>
                  {{ formato_moneda(item.inicia_caja) }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>

            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Ventas +</v-list-item-title>
                <v-list-item-subtitle>
                  {{ formato_moneda(item.venta_total) }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>

            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Compras -</v-list-item-title>
                <v-list-item-subtitle>
                  {{ formato_moneda(item.compra_total) }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>

            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Devoluciones +</v-list-item-title>
                <v-list-item-subtitle>
                  {{ formato_moneda(item.devolucion) }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>

            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title>Total</v-list-item-title>
                <v-list-item-subtitle>
                  {{
                    formato_moneda(
                      item.inicia_caja +
                        item.venta_total -
                        (item.devolucion + item.compra_total),
                    )
                  }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-list-item>
              <v-list-item-content>
                <v-btn
                  :color="'success'"
                  @click="show(item)"
                  small
                  v-text="'REGISTROS'"
                ></v-btn>
              </v-list-item-content>
            </v-list-item>
            <v-list-item>
              <v-list-item-content>
                <v-btn
                  :color="item.abierta ? 'blue' : 'red lighten-2'"
                  :disabled="item.abierta ? false : true"
                  @click="destroy(item)"
                  small
                  v-text="item.abierta ? 'CERRAR CAJA' : 'CAJA CERRADA'"
                ></v-btn>
              </v-list-item-content>
            </v-list-item>
          </v-card>
          <br />
        </template>
        <template v-slot:item.usuario="{ item }">
          <br />
          <div class="text-center">
            <v-avatar size="75">
              <img
                :src="item.usuarios.picture"
                :alt="item.usuarios.full_name"
              />
            </v-avatar>
            <br />
            {{ item.usuarios.cui }}
            <br />
            {{ item.usuarios.full_name }}
          </div>
          <br />
        </template>
        <template v-slot:no-data>
          <br />
          <v-alert type="error">No hay información para mostrar.</v-alert>
        </template>
      </v-data-table>
    </v-col>

    <v-dialog
      v-model="dialog_movimientos"
      persistent
      color="primary"
      width="50%"
    >
      <v-card>
        <v-card-text>
          <br />
          <div class="text-center">
            <v-btn
              color="red darken-1"
              small
              @click="dialog_movimientos = false"
            >
              Cerrar
            </v-btn>
            <br />
            <br />
            <v-avatar size="100" tile>
              <img :src="info.path" :alt="info.nombre_completo" />
            </v-avatar>
            <br />
            {{ info.nombre_completo }}
            <br />
            <div class="text-h3">{{ `Caja Cod: ${info.codigo}` }}</div>
            <br />
            <div class="text-h5">Registros</div>
          </div>
          <hr />
          <v-container>
            <Registro
              :movimientos="registros_data"
              v-if="dialog_movimientos"
            ></Registro>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>
import FormError from '../../shared/FormError'
import Registro from '../extras/RegistroComponent'

export default {
  name: 'Caja',
  components: { FormError, Registro },
  data() {
    return {
      loading: false,
      dialog_movimientos: false,

      search: '',
      desserts: [],
      headers: [
        {
          text: 'Inicia',
          align: 'start',
          value: 'inicio',
        },
        {
          text: 'Finaliza',
          align: 'start',
          value: 'finalizo',
        },
        {
          text: '',
          align: 'start',
          value: 'montos',
          sortable: false,
        },
        {
          text: 'Mes',
          align: 'start',
          value: 'meses.nombre',
        },
        {
          text: 'Empresa',
          align: 'start',
          value: 'empresas.nombre',
        },
        {
          text: 'Usuario',
          align: 'start',
          value: 'usuario',
        },
      ],
      footer: {
        showFirstLastPage: true,
        firstIcon: 'mdi-arrow-collapse-left',
        lastIcon: 'mdi-arrow-collapse-right',
        prevIcon: 'mdi-minus',
        nextIcon: 'mdi-plus',
      },

      vendedores: [],
      form: {
        inicia_caja: null,
        empresas_id: null,
        usuarios_id: null,
      },

      registros_data: [],
      info: {
        nombre_completo: null,
        path: null,
        codigo: null,
      },
    }
  },

  created() {
    this.initialize()
    //this.getSelects()
  },

  methods: {
    initialize() {
      this.loading = true

      this.$store.state.services.cajaController
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

    limpiar() {
      this.form.inicia_caja = 0
      this.form.empresas_id = null
      this.form.usuarios_id = null

      this.$validator.reset()
      this.$validator.reset()
    },

    getSelects() {
      this.$store.state.services.selectController
        .vendedores()
        .then((r) => {
          this.vendedores = r.data.data
        })
        .catch((r) => {})
    },

    crear_caja(scope) {
      this.$validator.validateAll(scope).then((result) => {
        if (result) {
          this.form.empresas_id = this.$store.state.empresa
            ? this.$store.state.empresa
            : null
          this.$swal({
            title: 'Aperturar caja',
            text: '¿Está seguro de realizar esta acción?',
            type: 'success',
            showCancelButton: true,
          }).then((result) => {
            if (result.value) {
              this.loading = true
              this.$store.state.services.cajaController
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
                  this.initialize()
                })
                .catch((r) => {
                  this.loading = false
                })
            }
          })
        }
      })
    },

    destroy(data) {
      this.$swal({
        title: 'Cerrar caja',
        text: '¿Está seguro de realizar esta acción?',
        type: 'error',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.cajaController
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
        } else {
          this.close()
        }
      })
    },

    show(item) {
      this.loading = true

      this.$store.state.services.cajaController
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

          this.registros_data = r.data.data
          this.info.codigo = item.id
          this.info.path = item.usuarios.picture
          this.info.nombre_completo = item.usuarios.full_name
          this.dialog_movimientos = true
        })
        .catch((r) => {
          this.loading = false
        })
    },
  },
}
</script>
