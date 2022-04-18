<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-col cols="12" md="12">
      <v-card dark dense>
        <v-toolbar dark :color="colorTolbar" class="mb-1">
          <v-toolbar-title>Kardex</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-text-field
            v-model="search"
            clearable
            flat
            solo-inverted
            hide-details
            prepend-inner-icon="mdi-magnify"
            label="Busqueda"
          ></v-text-field>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-btn color="white" small @click="initialize">
            <v-icon :color="colorTolbar">sync</v-icon>
          </v-btn>
        </v-toolbar>
        <v-card-text>
          <br />
          <v-data-iterator
            :items="items"
            :items-per-page.sync="itemsPerPage"
            :page.sync="page"
            :search="search"
            hide-default-footer
            :custom-filter="filteredItems"
          >
            <template v-slot:default="props">
              <v-row>
                <v-col
                  v-for="item in props.items"
                  :key="item.name"
                  cols="12"
                  sm="6"
                  md="4"
                  lg="3"
                >
                  <v-card color="blue-grey">
                    <v-list
                      dense
                      dark
                      :color="
                        item.producto.activo
                          ? 'info lighten-1'
                          : 'error lighten-1'
                      "
                    >
                      <v-list-item class="title font-weight-bold">
                        <v-list-item-content>
                          {{ item.producto.nombre }}
                        </v-list-item-content>
                      </v-list-item>
                      <v-list-item>
                        <v-list-item-content>
                          <small>{{ item.producto.descripcion }}</small>
                        </v-list-item-content>
                      </v-list-item>
                      <v-divider></v-divider>
                      <v-list-item>
                        <v-list-item-content>
                          Stock inicial:
                        </v-list-item-content>
                        <v-list-item-content>
                          <v-chip color="blue" text-color="white">
                            {{ item.stock_inicial }}
                          </v-chip>
                        </v-list-item-content>
                      </v-list-item>
                      <v-list-item>
                        <v-list-item-content>
                          Stock actual:
                        </v-list-item-content>
                        <v-list-item-content>
                          <template v-if="item.activo">
                            <v-tooltip bottom color="green lighten-2">
                              <template v-slot:activator="{ on, attrs }">
                                <v-chip
                                  color="green"
                                  dark
                                  text-color="white"
                                  v-bind="attrs"
                                  v-on="on"
                                >
                                  {{ item.stock_actual }}
                                </v-chip>
                              </template>
                              <span
                                v-text="
                                  `El producto ${item.producto.nombre} cuenta con stock`
                                "
                              ></span>
                            </v-tooltip>
                          </template>
                          <template v-else>
                            <v-tooltip bottom color="red lighten-2">
                              <template v-slot:activator="{ on, attrs }">
                                <v-chip
                                  color="red"
                                  dark
                                  text-color="white"
                                  v-bind="attrs"
                                  v-on="on"
                                >
                                  {{ item.stock_actual }}
                                </v-chip>
                              </template>
                              <span
                                v-text="
                                  `El producto ${item.producto.nombre} no cuenta con stock`
                                "
                              ></span>
                            </v-tooltip>
                          </template>
                        </v-list-item-content>
                      </v-list-item>
                      <v-list-item>
                        <v-list-item-content>
                          Stock consumido:
                        </v-list-item-content>
                        <v-list-item-content>
                          <v-chip color="greey" text-color="white">
                            {{ item.stock_consumido }}
                          </v-chip>
                        </v-list-item-content>
                      </v-list-item>
                      <v-list-item>
                        <v-list-item-content>
                          Movimientos stock:
                        </v-list-item-content>
                        <v-list-item-content>
                          <v-tooltip bottom color="brown lighten-2">
                            <template v-slot:activator="{ on, attrs }">
                              <v-chip
                                color="brown"
                                text-color="white"
                                v-bind="attrs"
                                v-on="on"
                                @click="ver_historial(item)"
                              >
                                {{ item.historial.length }}
                              </v-chip>
                            </template>
                            <span
                              v-text="
                                `Ver los movimientos de stock para el producto ${item.producto.nombre}`
                              "
                            ></span>
                          </v-tooltip>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>

                    <v-card-actions>
                      <v-row>
                        <v-col cols="12" class="text-center">
                          <v-tooltip bottom color="orange lighten-2">
                            <template v-slot:activator="{ on, attrs }">
                              <v-btn
                                text
                                icon
                                color="orange lighten-2"
                                @click="mapear(item)"
                                dark
                                v-bind="attrs"
                                v-on="on"
                              >
                                <v-icon>mdi-pencil</v-icon>
                              </v-btn>
                            </template>
                            <span
                              v-text="`Editar producto ${item.producto.nombre}`"
                            ></span>
                          </v-tooltip>
                          <v-tooltip
                            bottom
                            :color="
                              item.producto.activo
                                ? 'red lighten-2'
                                : 'blue lighten-2'
                            "
                          >
                            <template v-slot:activator="{ on, attrs }">
                              <v-btn
                                class="ma-2"
                                text
                                icon
                                :color="
                                  item.producto.activo
                                    ? 'red lighten-2'
                                    : 'blue lighten-2'
                                "
                                @click="destroy(item)"
                                dark
                                v-bind="attrs"
                                v-on="on"
                              >
                                <v-icon>
                                  {{
                                    item.producto.activo
                                      ? 'thumb_down'
                                      : 'thumb_up'
                                  }}
                                </v-icon>
                              </v-btn>
                            </template>
                            <span
                              v-text="
                                item.producto.activo
                                  ? `Dar de baja al producto ${item.producto.nombre}`
                                  : `Dar de alta al producto ${item.producto.nombre}`
                              "
                            ></span>
                          </v-tooltip>
                        </v-col>
                      </v-row>
                    </v-card-actions>
                  </v-card>
                </v-col>
              </v-row>
            </template>

            <template v-slot:no-data>
              <br />
              <v-alert type="error">No hay información para mostrar.</v-alert>
            </template>
          </v-data-iterator>
        </v-card-text>

        <v-card-actions>
          <span class="grey--text">Registros por página</span>
          <v-menu offset-y>
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                dark
                text
                color="primary"
                class="ml-2"
                v-bind="attrs"
                v-on="on"
              >
                {{ itemsPerPage }}
                <v-icon>mdi-chevron-down</v-icon>
              </v-btn>
            </template>
            <v-list>
              <v-list-item
                v-for="(number, index) in itemsPerPageArray"
                :key="index"
                @click="updateItemsPerPage(number)"
              >
                <v-list-item-title>{{ number }}</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>

          <v-spacer></v-spacer>

          <span class="mr-4 grey--text">
            Página {{ page }} de {{ numberOfPages }}
          </span>
          <v-btn
            fab
            dark
            color="blue darken-3"
            class="mr-1"
            small
            @click="formerPage"
          >
            <v-icon>mdi-chevron-left</v-icon>
          </v-btn>
          <v-btn
            fab
            dark
            color="blue darken-3"
            class="ml-1"
            small
            @click="nextPage"
          >
            <v-icon>mdi-chevron-right</v-icon>
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-col>

    <v-col cols="12" md="12">
      <v-dialog v-model="dialog" color="primary" width="80%">
        <v-card>
          <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
          </v-overlay>
          <v-card-title>
            <span class="headline">{{ formTitle }}</span>
          </v-card-title>

          <v-card-subtitle>
            * Indica todos los campos obligatorios
          </v-card-subtitle>
          <v-divider></v-divider>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12" md="12">
                  <v-text-field
                    class="mx-2"
                    prepend-icon="fiber_new"
                    counter
                    outlined
                    v-model="form_producto.nombre"
                    type="text"
                    label="* nombre del produto"
                    data-vv-scope="crear"
                    data-vv-name="nombre del produto"
                    v-validate="'required|max:100'"
                  ></v-text-field>
                  <FormError
                    :attribute_name="'crear.nombre del produto'"
                    :errors_form="errors"
                  ></FormError>
                </v-col>
                <v-col cols="12" md="12">
                  <v-textarea
                    class="mx-2"
                    rows="2"
                    prepend-icon="fiber_new"
                    counter
                    outlined
                    v-model="form_producto.descripcion"
                    type="text"
                    label="* descripción"
                    data-vv-scope="crear"
                    data-vv-name="descripción"
                    v-validate="'required|max:500'"
                  ></v-textarea>
                  <FormError
                    :attribute_name="'crear.descripción'"
                    :errors_form="errors"
                  ></FormError>
                </v-col>
                <v-col
                  cols="12"
                  md="4"
                  v-if="form_producto.stock_inicial === 0"
                >
                  <v-text-field
                    class="mx-2"
                    prepend-icon="fiber_new"
                    counter
                    outlined
                    v-model="form_producto.stock_inicial"
                    type="number"
                    label="stock inicial"
                    data-vv-scope="crear"
                    data-vv-name="stock inicial"
                    v-validate="'integer|min_value:0'"
                    hint="Debe indicar la cantidad con la que inicia el stock, si no la sabe coloque el valor 0"
                    persistent-hint
                  ></v-text-field>
                  <FormError
                    :attribute_name="'crear.stock inicial'"
                    :errors_form="errors"
                  ></FormError>
                </v-col>
                <v-col cols="12" md="8">
                  <v-row class="text-h5 text-center">
                    <v-col cols="6">
                      <v-checkbox
                        v-model="form_producto.consumible"
                        :label="`${
                          form_producto.consumible
                            ? 'SI, es consumible'
                            : 'NO, es consumible'
                        }`"
                      ></v-checkbox>
                    </v-col>
                    <v-col cols="6">
                      <v-checkbox
                        v-model="form_producto.check_in"
                        :label="`${
                          form_producto.check_in
                            ? 'SI, aplica en la lista Check In'
                            : 'NO, aplica en la lista Check In'
                        }`"
                      ></v-checkbox>
                    </v-col>
                  </v-row>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="red darken-1" @click="close">
              Cancelar
            </v-btn>
            <v-btn color="blue darken-1" @click="validar_formulario('crear')">
              Guardar
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog
        v-model="dialog_historial"
        transition="dialog-top-transition"
        max-width="80%"
        persistent
        dark
      >
        <v-card v-if="historial" elevation="4" dark shaped>
          <v-toolbar :color="historial.producto.activo ? 'info' : 'error'" dark>
            {{ `Información del producto ${historial.producto.nombre}` }}
            <v-spacer></v-spacer>
            <v-btn color="red" text icon @click="dialog_historial = false">
              <v-icon>close</v-icon>
            </v-btn>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12" md="12">
                  <p class="text-h5">
                    Descripción
                  </p>
                </v-col>
                <v-col cols="12" md="12">
                  <v-textarea
                    v-model="historial.producto.descripcion"
                    label="Descripción"
                    rows="2"
                    filled
                    shaped
                    dense
                    disabled
                  ></v-textarea>
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field
                    v-model="historial.producto.consumible_tag"
                    label="Consumible"
                    filled
                    shaped
                    dense
                    disabled
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field
                    v-model="historial.producto.created_at"
                    label="Fecha de creación del producto"
                    filled
                    shaped
                    dense
                    disabled
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field
                    v-model="historial.producto.updated_at"
                    label="Fecha de actualización del producto"
                    filled
                    shaped
                    dense
                    disabled
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field
                    v-model="historial.usuario.cui"
                    label="Usuario"
                    filled
                    shaped
                    dense
                    disabled
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="12">
                  <v-divider></v-divider>
                </v-col>
                <v-col cols="12" md="12">
                  <p class="text-h5">
                    Inventario
                  </p>
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field
                    v-model="historial.check_in_tag"
                    label="Check In"
                    filled
                    shaped
                    dense
                    disabled
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field
                    v-model="historial.stock_inicial"
                    label="Stock inicial"
                    filled
                    shaped
                    dense
                    disabled
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field
                    v-model="historial.stock_actual"
                    label="Stock actual"
                    filled
                    shaped
                    dense
                    disabled
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field
                    v-model="historial.stock_consumido"
                    label="Stock consumido"
                    filled
                    shaped
                    dense
                    disabled
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="12">
                  <v-divider></v-divider>
                </v-col>
                <v-col cols="12" md="12">
                  <p class="text-h5">
                    {{ `Movimientos en el inventario #${historial.id}` }}
                  </p>
                </v-col>
                <v-col cols="12" md="12">
                  <v-simple-table dark dense>
                    <thead>
                      <tr>
                        <td>Movimiento</td>
                        <td>Descripción</td>
                        <td>Stock Anterior</td>
                        <td>Stock Nuevo</td>
                        <td>Fecha</td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="inventario in historial.historial"
                        :key="`I${inventario.id}`"
                      >
                        <td>
                          {{ inventario.documento }}
                        </td>
                        <td>
                          {{ inventario.descripcion }}
                        </td>
                        <td>
                          {{ inventario.stock_anterior }}
                        </td>
                        <td>
                          {{
                            `${inventario.stock_nuevo} (${inventario.signo})`
                          }}
                        </td>
                        <td>
                          {{ inventario.created_at }}
                        </td>
                      </tr>
                    </tbody>
                  </v-simple-table>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-col>
  </v-row>
</template>

<script>
export default {
  name: 'HKardex',
  data() {
    return {
      loading: false,
      dialog: false,

      itemsPerPageArray: [4, 8, 12, 16, 20],
      search: '',
      page: 1,
      itemsPerPage: 12,
      items: [],

      editedIndex: false,

      historial: null,
      dialog_historial: false,
      form_producto: {
        id: null,
        nombre: null,
        descripcion: null,
        consumible: false,
        stock_inicial: 0,
        check_in: false,
      },
    }
  },
  computed: {
    formTitle() {
      return !this.editedIndex ? 'Agregar' : 'Editar'
    },

    colorTolbar() {
      return !this.editedIndex ? 'success' : 'warning'
    },

    numberOfPages() {
      return Math.ceil(this.items.length / this.itemsPerPage)
    },
  },

  watch: {
    dialog(val) {
      val || this.close()
    },
  },

  created() {
    this.initialize()
  },

  methods: {
    nextPage() {
      if (this.page + 1 <= this.numberOfPages) this.page += 1
    },
    formerPage() {
      if (this.page - 1 >= 1) this.page -= 1
    },
    updateItemsPerPage(number) {
      this.itemsPerPage = number
    },

    limpiar() {
      this.form_producto.id = null
      this.form_producto.nombre = null
      this.form_producto.descripcion = null
      this.form_producto.consumible = false
      this.form_producto.stock_inicial = 0
      this.form_producto.check_in = false

      this.dialog_historial = false
      this.editedIndex = false

      this.$validator.reset()
      this.$validator.reset()
    },

    initialize() {
      this.loading = true

      this.$store.state.services.KardexService.getAll()
        .then((r) => {
          this.desserts = r.data
          this.items = r.data
          this.close()
        })
        .catch((e) => {
          this.errorResponse(e)
        })
        .finally(() => {
          this.loading = false
        })
    },

    close() {
      this.limpiar()
      this.dialog = false
    },

    validar_formulario(scope) {
      this.$validator.validateAll(scope).then((result) => {
        if (result) this.update(this.form_producto)
      })
    },

    mapear(item) {
      this.form_producto.id = item.id
      this.form_producto.nombre = item.producto.nombre
      this.form_producto.descripcion = item.producto.descripcion
      this.form_producto.consumible = item.producto.consumible
      this.form_producto.stock_inicial = item.stock_inicial > 0 ? -1 : 0
      this.form_producto.check_in = item.check_in

      this.editedIndex = true
      this.dialog = true
    },

    destroy(data) {
      this.$swal({
        title: data.producto.activo ? 'Baja' : 'Alta',
        text: '¿Está seguro de realizar esta acción?',
        type: 'info',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.KardexService.delete(data)
            .then((r) => {
              this.$toastr.success(r.data, 'Mensaje')
              this.initialize()
            })
            .catch((e) => {
              this.errorResponse(e)
            })
            .finally(() => {
              this.loading = false
            })
        } else {
          this.close()
        }
      })
    },

    update(data) {
      this.$swal({
        title: 'Actualizar',
        text: '¿Está seguro de realizar esta acción?',
        type: 'success',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.KardexService.update(data)
            .then((r) => {
              this.$toastr.success(r.data, 'Mensaje')
              this.initialize()
            })
            .catch((e) => {
              this.errorResponse(e)
            })
            .finally(() => {
              this.loading = false
            })
        } else {
          this.close()
        }
      })
    },

    ver_historial(item) {
      this.historial = item
      this.dialog_historial = true
    },

    filteredItems() {
      if (this.search) {
        return this.items.filter((element) => {
          return element.producto.nombre
            .toUpperCase()
            .includes(this.search.toUpperCase())
        })
      } else {
        return this.items
      }
    },
  },
}
</script>
