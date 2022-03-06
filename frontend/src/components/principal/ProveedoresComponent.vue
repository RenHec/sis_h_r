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
            <v-toolbar-title>Proveedores</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-text-field
              v-model="search"
              append-icon="search"
              label="Buscar"
              single-line
              hide-details
            ></v-text-field>
            <v-spacer></v-spacer>
            <v-btn class="ma-2 mb-2" color="success" @click="initialize">
              Actualizar
            </v-btn>

            <v-dialog v-model="dialog" color="primary" width="75%">
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
              <v-card :color="!editedIndex ? 'primary' : 'warning'">
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
                      <v-col cols="12" md="4">
                        <v-radio-group v-model="form.representacion">
                          <v-radio
                            v-for="representacion in representaciones"
                            :key="representacion"
                            :label="`Representación: ${representacion}`"
                            :value="representacion"
                            data-vv-scope="crear_persona"
                            data-vv-name="representacion"
                            v-validate="'required'"
                          ></v-radio>
                        </v-radio-group>
                        <FormError
                          :attribute_name="'crear_persona.representacion'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                    </v-row>
                    <v-row v-if="form.representacion === 'INDIVIDUAL'">
                      <v-col cols="12" md="4">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.nit"
                          type="text"
                          label="nit"
                          data-vv-scope="crear_persona"
                          data-vv-name="nit"
                          v-validate="'max:10'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_persona.nit'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="4">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.nombres"
                          type="text"
                          label="nombres"
                          data-vv-scope="crear_persona"
                          data-vv-name="nombres"
                          v-validate="'required|max:50'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_persona.nombres'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="4">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.apellidos"
                          type="text"
                          label="apellidos"
                          data-vv-scope="crear_persona"
                          data-vv-name="apellidos"
                          v-validate="'required|max:50'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_persona.apellidos'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                    </v-row>
                    <v-row v-if="form.representacion === 'JURIDICO'">
                      <v-col cols="12" md="4">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.nit"
                          type="text"
                          label="nit"
                          data-vv-scope="crear_persona"
                          data-vv-name="nit"
                          v-validate="'max:10'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_persona.nit'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="4">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.completo"
                          type="text"
                          label="nombre empresa"
                          data-vv-scope="crear_persona"
                          data-vv-name="nombre empresa"
                          v-validate="'required|max:100'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_persona.nombre empresa'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col cols="12" md="6">
                        <v-autocomplete
                          v-model="form.municipios_id"
                          :items="municipios"
                          chips
                          label="Seleccionar departamento y municipio"
                          outlined
                          :clearable="true"
                          :deletable-chips="true"
                          item-text="full_name"
                          item-value="id"
                          return-object
                          v-validate="'required'"
                          data-vv-scope="crear_persona"
                          data-vv-name="departamento y municipio"
                        ></v-autocomplete>
                        <FormError
                          :attribute_name="'crear_persona.departamento y municipio'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.direccion"
                          type="text"
                          label="dirección exacta"
                          data-vv-scope="crear_persona"
                          data-vv-name="dirección exacta"
                          v-validate="'max:100'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_persona.dirección exacta'"
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
                    @click="validar_formulario('crear_persona')"
                  >
                    Guardar
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </v-toolbar>
        </template>
        <template v-slot:item.persona="{ item }">
          <br />
          <div class="text-center">
            <br />
            {{ item.representacion }}
            <br />
            {{ item.completo }}
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
import FormError from '../shared/FormError'
export default {
  name: 'Proveedores',
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
          text: 'Dirección',
          align: 'start',
          value: 'direccion',
        },
        {
          text: 'Municipio, Departamento',
          align: 'start',
          value: 'municipios.full_name',
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
      representaciones: ['INDIVIDUAL', 'JURIDICO'],
      municipios: [],
      editedIndex: false,
      form: {
        id: 0,
        representacion: null,
        nit: null,
        nombres: null,
        apellidos: null,
        completo: null,
        direccion: null,
        es_proveedor: true,
        municipios_id: null,
      },
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
    this.getMunicipios()
  },

  methods: {
    limpiar() {
      this.form.id = 0
      this.form.representacion = null
      this.form.nit = null
      this.form.nombres = null
      this.form.apellidos = null
      this.form.completo = null
      this.form.email = null
      this.form.direccion = null
      this.form.es_proveedor = true
      this.form.municipios_id = null
      this.editedIndex = false

      this.$validator.reset()
      this.$validator.reset()
    },

    initialize() {
      this.loading = true

      this.$store.state.services.personaController
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
      this.form.representacion = item.representacion
      this.form.nit = item.nit
      this.form.nombres = item.nombres
      this.form.apellidos = item.apellidos
      this.form.completo = item.completo
      this.form.direccion = item.direccion
      this.form.municipios_id = item.municipios
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
        title: 'Desactivar',
        text: '¿Está seguro de realizar esta acción?',
        type: 'error',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.personaController
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
          this.$store.state.services.personaController
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
          this.$store.state.services.personaController
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

    getMunicipios() {
      this.$store.state.services.selectController
        .municipio()
        .then((r) => {
          this.municipios = r.data.data
        })
        .catch((r) => {})
    },
  },
}
</script>
