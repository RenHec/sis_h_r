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
          <v-toolbar flat :color="colorTolbar">
            <v-toolbar-title>Categorías</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-text-field
              v-model="search"
              append-icon="search"
              label="Buscar"
              single-line
              hide-details
            ></v-text-field>
            <v-spacer></v-spacer>

            <v-dialog v-model="dialog" color="primary" width="50%">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  :loading="dialog"
                  :disabled="dialog"
                  @click="dialog = true"
                  color="primary"
                  dark
                  class="mb-2"
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
                    <v-row>
                      <v-col cols="12" md="6" v-if="!editedIndex">
                        <v-select
                          prepend-icon="developer_board"
                          v-model="form.sub_categorias_id"
                          :items="sub_categorias"
                          chips
                          label="seleccione una categoría principal por favor"
                          outlined
                          :clearable="true"
                          :deletable-chips="true"
                          item-text="nombre_categoria"
                          item-value="id"
                          return-object
                          data-vv-scope="crear_categoria"
                          data-vv-name="categoría principal"
                        ></v-select>
                        <FormError
                          :attribute_name="'crear_categoria.categoría principal'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col cols="12" md="4">
                        <v-text-field
                          prepend-icon="closed_caption_off"
                          counter
                          outlined
                          v-model="form.slug"
                          type="text"
                          label="código de la categoría"
                          data-vv-scope="crear_categoria"
                          data-vv-name="código de la categoría"
                          v-validate="'max:5'"
                          @input="form.slug = $event.toUpperCase()"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_categoria.código de la categoría'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="8">
                        <v-text-field
                          prepend-icon="fiber_new"
                          counter
                          outlined
                          v-model="form.nombre"
                          type="text"
                          label="nombre de la categoría"
                          data-vv-scope="crear_categoria"
                          data-vv-name="nombre de la categoría"
                          v-validate="'required|max:50'"
                          @input="form.nombre = $event.toUpperCase()"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_categoria.nombre de la categoría'"
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
                          data-vv-scope="crear_categoria"
                          data-vv-name="descripción"
                          v-validate="'required|max:150'"
                          @input="form.descripcion = $event.toUpperCase()"
                        ></v-textarea>
                        <FormError
                          :attribute_name="'crear_categoria.descripción'"
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
                    @click="validar_formulario('crear_categoria')"
                  >
                    Guardar
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </v-toolbar>
        </template>
        <template v-slot:item.sub_categorias="{ item }">
          <br />
          <v-card class="mx-auto" width="100%" tile>
            <v-list-item
              three-line
              v-for="sub in item.sub_categorias"
              v-bind:key="sub.id"
            >
              <v-list-item-content>
                <v-list-item-title>
                  {{ sub.nombre_sub_categoria }}
                  <v-btn
                    class="ma-2"
                    text
                    icon
                    :color="
                      item.deleted_at ? 'blue lighten-2' : 'red lighten-2'
                    "
                    @click="destroy(sub)"
                  >
                    <v-icon
                      v-text="
                        item.deleted_at ? 'mdi-thumb-up' : 'mdi-thumb-down'
                      "
                    ></v-icon>
                  </v-btn>
                </v-list-item-title>
                <v-list-item-subtitle v-text="sub.slug"></v-list-item-subtitle>
                <v-list-item-subtitle
                  v-text="sub.descripcion"
                ></v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-card>
          <br />
        </template>
        <template v-slot:item.actions="{ item }">
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
import FormError from '../shared/FormError'
export default {
  name: 'Categoria',
  components: {
    FormError,
  },
  data() {
    return {
      loading: false,
      dialog: false,
      search: '',
      headers: [
        {
          text: 'Código',
          align: 'start',
          value: 'slug',
        },
        {
          text: 'Categoría principal',
          align: 'start',
          value: 'nombre_categoria',
        },
        {
          text: 'Descripción',
          align: 'start',
          value: 'descripcion',
        },
        {
          text: '',
          align: 'start',
          value: 'sub_categorias',
          sortable: false,
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
      editedIndex: false,

      sub_categorias: [],
      form: {
        id: 0,
        slug: null,
        sub_categorias_id: null,
        nombre: null,
        descripcion: null,
        tipos_categorias_id: 1,
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
    limpiar() {
      this.form.id = 0
      this.form.slug = null
      this.form.sub_categorias_id = null
      this.form.nombre = null
      this.form.descripcion = null
      this.form.tipos_categorias_id = 1
      this.editedIndex = false

      this.$validator.reset()
      this.$validator.reset()
    },

    initialize() {
      this.loading = true

      this.$store.state.services.categoriaController
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
          this.sub_categorias = r.data.data.filter(
            (element) => element.deleted_at == null,
          )
          this.close()
        })
        .catch((r) => {
          this.loading = false
        })
    },

    mapear(item) {
      this.form.id = item.id
      this.form.slug = item.slug
      this.form.nombre =
        item.sub_categorias_id > 0
          ? item.nombre_sub_categoria
          : item.nombre_categoria
      this.form.descripcion = item.descripcion
      this.form.tipos_categorias_id = 1
      this.editedIndex = true
      this.dialog = true
    },

    close() {
      this.limpiar()
      this.dialog = false
    },

    validar_formulario() {
      this.$validator.validateAll().then((result) => {
        if (result)
          this.editedIndex ? this.update(this.form) : this.store(this.form)
      })
    },

    destroy(data) {
      this.$swal({
        title: 'Desactivar',
        text: '¿Está seguro de realizar esta acción?',
        type: 'error',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.categoriaController
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
          this.$store.state.services.categoriaController
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
          this.$store.state.services.categoriaController
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
  },
}
</script>
