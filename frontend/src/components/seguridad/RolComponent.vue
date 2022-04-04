<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-col>
      <v-card>
        <v-overlay :value="loading">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-card-title>
          <span class="headline">{{ formTitle }}</span>
        </v-card-title>

        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12" md="12">
                <v-text-field
                  counter
                  outlined
                  v-model="form.nombre"
                  type="text"
                  label="Nombre"
                  data-vv-scope="create_rol"
                  data-vv-name="nombre"
                  v-validate="'required|max:20'"
                  @input="form.nombre = $event.toUpperCase()"
                ></v-text-field>
                <FormError
                  :attribute_name="'create_rol.nombre'"
                  :errors_form="errors"
                ></FormError>
              </v-col>
              <v-col cols="12" md="12">
                <v-select
                  v-model="form.menus"
                  :items="menus"
                  chips
                  label="Seleccione uno o varios por favor"
                  multiple
                  outlined
                  :clearable="true"
                  :deletable-chips="true"
                  item-text="nombre"
                  item-value="id"
                  return-object
                  v-validate="'required'"
                  data-vv-scope="create_rol"
                  data-vv-name="menus"
                ></v-select>
                <FormError
                  :attribute_name="'create_rol.menus'"
                  :errors_form="errors"
                ></FormError>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="close">Cancelar</v-btn>
          <v-btn
            color="blue darken-1"
            text
            @click="validar_formulario('create_rol')"
          >
            Guardar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-col>

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
          <v-toolbar flat color="success">
            <v-toolbar-title>Roles</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-text-field
              v-model="search"
              append-icon="search"
              label="Buscar"
              single-line
              hide-details
            ></v-text-field>
            <v-btn class="ma-2" color="primary" @click="initialize">
              Actualizar
            </v-btn>
            <v-dialog
              v-model="dialog"
              color="primary"
              fullscreen
              hide-overlay
              transition="dialog-bottom-transition"
            >
              <v-card>
                <v-overlay :value="loading">
                  <v-progress-circular
                    indeterminate
                    size="64"
                  ></v-progress-circular>
                </v-overlay>
                <v-card-title>
                  <v-toolbar dark color="primary">
                    <v-btn icon @click="close">
                      <v-icon>mdi-close</v-icon>
                    </v-btn>
                    <v-toolbar-title>{{ formTitle }}</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                      <v-btn
                        text
                        @click="validar_formulario('create_menu_rol')"
                      >
                        Guardar
                      </v-btn>
                    </v-toolbar-items>
                  </v-toolbar>
                </v-card-title>

                <v-card-text>
                  <v-container>
                    <v-row>
                      <v-col cols="12" md="12" class="text-center">
                        <h2 v-text="form.nombre"></h2>
                      </v-col>
                      <v-col cols="12" md="12">
                        <v-select
                          v-model="form.menus_id"
                          :items="menus_rol"
                          chips
                          label="Seleccione uno o varios por favor"
                          multiple
                          outlined
                          :clearable="true"
                          :deletable-chips="true"
                          item-text="nombre"
                          item-value="id"
                          return-object
                          v-validate="'required'"
                          data-vv-scope="create_menu_rol"
                          data-vv-name="menus"
                        ></v-select>
                        <FormError
                          :attribute_name="'create_menu_rol.menus'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col cols="12" md="12">
                        <v-card>
                          <v-toolbar color="primary" dark flat>
                            <v-toolbar-title>Menu</v-toolbar-title>
                          </v-toolbar>

                          <v-row>
                            <v-col cols="12" md="12" class="text-center">
                              <v-btn
                                class="ma-2"
                                fab
                                x-large
                                dark
                                color="error"
                                @click="destroy_menu_rol(delete_masivo)"
                              >
                                <v-icon v-text="'mdi-thumb-down'"></v-icon>
                              </v-btn>
                            </v-col>
                            <v-col cols="12" md="12">
                              <v-card-text>
                                <v-treeview
                                  :items="form.menus"
                                  selected-color="indigo"
                                  open-on-click
                                  selectable
                                  return-object
                                  selection-type="leaf"
                                  v-model="delete_masivo"
                                ></v-treeview>
                              </v-card-text>
                            </v-col>
                          </v-row>
                        </v-card>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>
              </v-card>
            </v-dialog>
          </v-toolbar>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-btn
            class="ma-2"
            text
            icon
            color="green lighten-2"
            v-if="!item.deleted_at"
            @click="mapear(item)"
          >
            <v-icon>mdi-eye</v-icon>
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
import FormError from '../shared/FormError'

export default {
  name: 'Rol',
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
          text: 'Nombre',
          align: 'start',
          value: 'nombre',
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
      form: {
        id: 0,
        nombre: '',
        menus: [],
        menus_id: [],
      },
      delete_masivo: [],
      menus: [],
      menus_rol: [],
    }
  },
  computed: {
    formTitle() {
      return !this.editedIndex ? 'Agregar rol' : 'Administrar menu del rol'
    },
  },

  watch: {
    dialog(val) {
      val || this.close()
    },
  },

  created() {
    this.initialize()
    this.getMenu()
  },

  methods: {
    limpiar() {
      this.form.id = 0
      this.form.nombre = null
      this.form.menus = []
      this.form.menus_id = []
      this.editedIndex = false
      this.delete_masivo = []

      this.$validator.reset()
      this.$validator.reset()
    },

    initialize() {
      this.loading = true

      this.$store.state.services.rolService
        .index()
        .then((r) => {
          this.desserts = r.data.data
        })
        .catch((e) => {
          this.errorResponse(e)
        })
        .finally(() => {
          this.loading = false
          this.close()
        })
    },

    mapear(item) {
      this.form.id = item.id
      this.form.nombre = item.nombre
      this.form.menus = item.roles_menus

      this.getMenusRol(this.form.menus)

      this.editedIndex = true
      this.dialog = true
    },

    close() {
      this.limpiar()
      this.getMenu()
      this.dialog = false
    },

    validar_formulario(scope) {
      this.$validator.validateAll(scope).then((result) => {
        if (result)
          this.editedIndex
            ? this.store_menu_rol(this.form)
            : this.store(this.form)
      })
    },

    destroy(data) {
      let title = !data.deleted_at ? 'Desactivar' : 'Activar'
      let type = !data.deleted_at ? 'error' : 'success'
      this.$swal({
        title: title,
        text: '¿Está seguro de realizar esta acción?',
        type: type,
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.rolService
            .destroy(data)
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

    destroy_menu_rol(data) {
      let set = {}
      let title = 'Desactivar'
      let type = 'error'
      set.eliminar = data

      this.$swal({
        title: title,
        text: '¿Está seguro de realizar esta acción?',
        type: type,
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.rolMenuService
            .eliminar_masivo(set)
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

    store(data) {
      this.$swal({
        title: 'Agregar',
        text: '¿Está seguro de realizar esta acción?',
        type: 'success',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.rolService
            .store(data)
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

    store_menu_rol(data) {
      this.$swal({
        title: 'Agregar',
        text: '¿Está seguro de realizar esta acción?',
        type: 'success',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.rolMenuService
            .store(data)
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

    //necesarios
    getMenu() {
      this.loading = true

      this.$store.state.services.menuService
        .index()
        .then((r) => {
          this.menus = r.data.data
        })
        .catch((e) => {
          this.errorResponse(e)
        })
        .finally(() => {
          this.loading = false
        })
    },

    getMenusRol(items) {
      let temporal = []
      this.loading = true
      this.menus_rol = []

      items.forEach((element_x) => {
        this.menus.forEach((element_y) => {
          element_x.menu_id == element_y.id
            ? this.menus.splice(this.menus.indexOf(element_y), 1)
            : null
        })
      })

      items.forEach(function (item) {
        if (item.menu.padre == 0 && item.menu.mostrar) {
          var primer_nivel = new Object()
          primer_nivel.id = item.id
          primer_nivel.name = item.menu.nombre
          primer_nivel.icon = item.menu.icono
          primer_nivel.children = []
          items.forEach(function (item2) {
            if (item.menu.id == item2.menu.padre && item2.menu.mostrar) {
              var segundo_nivel = new Object()
              segundo_nivel.id = item2.id
              segundo_nivel.name = item2.menu.nombre
              segundo_nivel.icon = item2.menu.icono
              segundo_nivel.children = []

              items.forEach(function (item3) {
                if (item2.menu.id == item3.menu.padre && item3.menu.mostrar) {
                  var tercer_nivel = new Object()
                  tercer_nivel.id = item3.id
                  tercer_nivel.name = item3.menu.nombre
                  tercer_nivel.icon = item3.menu.icono

                  segundo_nivel.children.push(tercer_nivel)
                }
              })

              primer_nivel.children.push(segundo_nivel)
            }
          })
          temporal.push(primer_nivel)
        }
      })

      this.menus_rol = this.menus
      this.form.menus = temporal
      this.loading = false
    },
  },
}
</script>
