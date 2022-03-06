<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

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

            <v-dialog v-model="dialog" persistent color="primary" width="75%">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  :loading="dialog"
                  :disabled="dialog"
                  @click="dialog = true"
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
                  <span class="headline">{{ formTitle }}</span>
                </v-card-title>

                <v-card-text>
                  <v-container>
                    <v-row v-if="!editedIndex">
                      <v-col cols="12" md="6">
                        <v-select
                          prepend-icon="view_carousel"
                          v-model="form.bodegas_id"
                          :items="bodegas"
                          chips
                          label="seleccione una bodega por favor"
                          outlined
                          :clearable="true"
                          :deletable-chips="true"
                          item-text="full_name"
                          item-value="id"
                          return-object
                          v-validate="'required'"
                          data-vv-scope="crear_producto"
                          data-vv-name="bodega"
                        ></v-select>
                        <FormError
                          :attribute_name="'crear_producto.bodega'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col cols="12" md="3" v-if="!editedIndex">
                        <v-text-field
                          prepend-icon="closed_caption_off"
                          counter
                          outlined
                          v-model="form.codigo_real"
                          type="text"
                          label="código del producto"
                          data-vv-scope="crear_producto"
                          data-vv-name="código del producto"
                          v-validate="'max:25'"
                          @input="form.codigo_real = $event.toUpperCase()"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_producto.código del producto'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" :md="!editedIndex ? '9' : '12'">
                        <v-text-field
                          prepend-icon="fiber_new"
                          counter
                          outlined
                          v-model="form.nombre"
                          type="text"
                          label="nombre"
                          data-vv-scope="crear_producto"
                          data-vv-name="nombre"
                          v-validate="'required|max:65'"
                          @input="form.nombre = $event.toUpperCase()"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_producto.nombre'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="4">
                        <v-select
                          prepend-icon="art_track"
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
                          v-validate="'required'"
                          data-vv-scope="crear_producto"
                          data-vv-name="categoría"
                        ></v-select>
                        <FormError
                          :attribute_name="'crear_producto.categoría'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="4">
                        <v-select
                          prepend-icon="star"
                          v-model="form.marcas_id"
                          :items="marcas"
                          chips
                          label="seleccione una marca por favor"
                          outlined
                          :clearable="true"
                          :deletable-chips="true"
                          item-text="nombre"
                          item-value="id"
                          return-object
                          v-validate="'required'"
                          data-vv-scope="crear_producto"
                          data-vv-name="marca"
                        ></v-select>
                        <FormError
                          :attribute_name="'crear_producto.marca'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="4" v-if="!editedIndex">
                        <v-dialog
                          ref="fecha_ingreso"
                          v-model="dialogo_fecha_ingreso"
                          :return-value.sync="form.fecha_ingreso"
                          persistent
                          width="290px"
                        >
                          <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                              v-model="form.fecha_ingreso"
                              label="fecha de ingreso"
                              prepend-icon="mdi-calendar"
                              readonly
                              v-bind="attrs"
                              v-on="on"
                              v-validate="'required'"
                              data-vv-scope="crear_producto"
                              data-vv-name="fecha de ingreso"
                            ></v-text-field>
                            <FormError
                              :attribute_name="'crear_producto.fecha de ingreso'"
                              :errors_form="errors"
                            ></FormError>
                          </template>
                          <v-date-picker
                            v-model="form.fecha_ingreso"
                            scrollable
                            :max="new Date().toISOString().substr(0, 10)"
                            no-title
                          >
                            <v-spacer></v-spacer>
                            <v-btn
                              color="error"
                              @click="dialogo_fecha_ingreso = false"
                            >
                              Cerrar
                            </v-btn>
                            <v-btn
                              color="primary"
                              @click="
                                $refs.fecha_ingreso.save(form.fecha_ingreso)
                              "
                            >
                              Seleccionar
                            </v-btn>
                          </v-date-picker>
                        </v-dialog>
                      </v-col>
                      <v-col cols="12" md="4">
                        <v-file-input
                          outlined
                          v-model="form.imagen"
                          @change="cargarImagen"
                          accept="image/*"
                          placeholder="Imagén del producto"
                          prepend-icon="mdi-camera"
                        ></v-file-input>
                      </v-col>
                      <v-col cols="12" md="4">
                        <v-text-field
                          prepend-icon="local_mall"
                          counter
                          outlined
                          v-model="form.precio_compra"
                          type="text"
                          label="precio de compra"
                          data-vv-scope="crear_producto"
                          data-vv-name="precio de compra"
                          v-validate="'required|decimal:2'"
                          prefix="Q"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_producto.precio de compra'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <!--<v-col cols="12" md="4" v-if="!editedIndex">
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
                              v-validate="'required'"
                              data-vv-scope="crear_producto"
                              data-vv-name="fecha de vencimiento"
                            ></v-text-field>
                            <FormError
                              :attribute_name="'crear_producto.fecha de vencimiento'"
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
                            <v-btn
                              color="error"
                              @click="dialogo_vencimiento = false"
                            >
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
                      </v-col>-->
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
                          data-vv-scope="crear_producto"
                          data-vv-name="descripción"
                          v-validate="'max:150'"
                          @input="form.descripcion = $event.toUpperCase()"
                        ></v-textarea>
                        <FormError
                          :attribute_name="'crear_producto.descripción'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>

                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="red darken-1" @click="close">
                    Cancelar
                  </v-btn>
                  <v-btn
                    color="blue darken-1"
                    @click="validar_formulario('crear_producto')"
                  >
                    Guardar
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </v-toolbar>
        </template>
        <template v-slot:item.codigo="{ item }">
          <br />
          {{ `Código de fábrica: ${item.codigo_real}` }}
          <br />
          {{ `Código interno: ${item.codigo_sistema}` }}
          <br />
        </template>
        <template v-slot:item.producto="{ item }">
          <br />
          <div class="text-center">
            <v-avatar size="100" tile>
              <img :src="item.logo_path" :alt="item.nombre" />
            </v-avatar>
            <br />
            {{ item.nombre }}
            <br />
            {{ item.fecha_ingreso }}
          </div>
          <br />
        </template>
        <template v-slot:item.actions="{ item }">
          <v-btn
            class="ma-2"
            text
            icon
            color="orange lighten-2"
            v-if="!item.deleted_at"
            @click="mapear(item)"
          >
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn
            class="ma-2"
            text
            icon
            :color="item.deleted_at ? 'blue lighten-2' : 'red lighten-2'"
            @click="destroy(item)"
          >
            <v-icon
              v-text="item.deleted_at ? 'mdi-thumb-up' : 'mdi-thumb-down'"
            ></v-icon>
          </v-btn>
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
  name: 'Productos',
  components: {
    FormError,
  },
  data() {
    return {
      loading: false,
      dialog: false,
      dialogo_fecha_ingreso: false,
      dialogo_vencimiento: false,
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
          text: 'Descripción',
          align: 'start',
          value: 'descripcion',
        },
        {
          text: 'Categorias',
          align: 'start',
          value: 'categorias',
        },
        {
          text: 'Marca',
          align: 'start',
          value: 'marcas',
        },
        {
          text: 'Precio Compra',
          align: 'start',
          value: 'monto',
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
      categorias: [],
      marcas: [],
      bodegas: [],
      editedIndex: false,
      form: {
        id: 0,
        logo: null,
        codigo_real: null,
        nombre: null,
        descripcion: null,
        fecha_ingreso: null,
        categorias_id: null,
        marcas_id: null,
        precio_compra: 0,
        bodegas_id: null,
        vencimiento: null,
        imagen: null,
      },
      accept: ['image/png', 'image/jpeg', 'image/jpg'],
    }
  },
  computed: {
    formTitle() {
      return !this.editedIndex ? 'Agregar' : 'Editar'
    },
  },

  watch: {
    dialog(val) {
      val || this.close()
    },
  },

  created() {
    this.initialize()
    this.getSelects()
  },

  methods: {
    limpiar() {
      this.form.id = 0
      this.form.logo = null
      this.form.codigo_real = null
      this.form.nombre = null
      this.form.descripcion = null
      this.form.fecha_ingreso = null
      this.form.categorias_id = null
      this.form.marcas_id = null
      this.form.precio_compra = 0
      this.form.bodegas_id = null
      this.form.vencimiento = null
      this.form.imagen = null
      this.editedIndex = false

      this.$validator.reset()
      this.$validator.reset()
    },

    initialize() {
      this.loading = true

      this.$store.state.services.productoController
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
          this.close()
        })
        .catch((r) => {
          this.loading = false
        })
    },

    mapear(item) {
      this.form.id = item.id
      this.form.nombre = item.nombre
      this.form.descripcion = item.descripcion
      this.form.categorias_id = this.categorias.find(
        (element) => element.id == item.categorias_id,
      )
      this.form.marcas_id = item.marcas_join
      this.form.precio_compra = item.precio_compra

      this.editedIndex = true
      this.dialog = true
    },

    close() {
      this.limpiar()
      this.dialog = false
    },

    validar_formulario(scope) {
      this.$validator.validateAll(scope).then((result) => {
        if (result)
          this.editedIndex ? this.update(this.form) : this.store(this.form)
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
          this.$store.state.services.productoController
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

    store(data) {
      this.$swal({
        title: 'Agregar',
        text: '¿Está seguro de realizar esta acción?',
        type: 'success',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.productoController
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

              this.$toastr.success(r.data.mensaje, 'Mensaje')
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

    update(data) {
      this.$swal({
        title: 'Modificar',
        text: '¿Está seguro de realizar esta acción?',
        type: 'warning',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.productoController
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
        } else {
          this.close()
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

      this.$store.state.services.selectController
        .marca()
        .then((r) => {
          this.marcas = r.data.data
        })
        .catch((r) => {})

      this.$store.state.services.selectController
        .bodega()
        .then((r) => {
          this.bodegas = r.data.bodegas_asignadas
        })
        .catch((r) => {})
    },

    cargarImagen(e) {
      if (typeof e !== 'undefined') {
        if (this.accept.includes(e.type.toLowerCase())) {
          const reader = new FileReader()
          reader.readAsDataURL(e)
          reader.onload = () => (this.form.logo = reader.result)
        } else {
          this.form.imagen = null
          this.$swal({
            title: 'Logotipo',
            text: 'La imagén del producto debe tener formato PNG, JPG o JPEG',
            type: 'warning',
            showCancelButton: false,
          })
        }
      } else {
        this.form.logo = null
      }
    },
  },
}
</script>
