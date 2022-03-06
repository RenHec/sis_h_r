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
          <v-toolbar flat color="success">
            <v-toolbar-title>Usuarios</v-toolbar-title>
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
              persistent
              transition="dialog-bottom-transition"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  :loading="dialog"
                  :disabled="dialog"
                  @click="dialog = true"
                  color="primary"
                  dark
                  class="ma-2"
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
                  <v-toolbar dark color="primary">
                    <v-btn icon @click="close">
                      <v-icon>mdi-close</v-icon>
                    </v-btn>
                    <v-toolbar-title>{{ formTitle }}</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                      <v-btn
                        color="success"
                        @click="validar_formulario('crear_usuario')"
                      >
                        Guardar
                      </v-btn>
                    </v-toolbar-items>
                  </v-toolbar>
                </v-card-title>

                <v-card-text>
                  <v-container>
                    <v-row>
                      <v-col cols="12" md="4">
                        <v-autocomplete
                          v-model="form.empresas_id"
                          :items="empresas"
                          chips
                          label="Seleccionar empresa"
                          outlined
                          :clearable="true"
                          :deletable-chips="true"
                          item-text="nombre"
                          item-value="id"
                          return-object
                          v-validate="'required'"
                          data-vv-scope="crear_usuario"
                          data-vv-name="empresa"
                        ></v-autocomplete>
                        <FormError
                          :attribute_name="'crear_usuario.empresa'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col cols="12" md="4">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.cui"
                          type="text"
                          label="número de CUI"
                          data-vv-scope="crear_usuario"
                          data-vv-name="número de CUI"
                          v-validate="'required|digits:13'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_usuario.número de CUI'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="4">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.primer_nombre"
                          type="text"
                          label="primer nombre"
                          data-vv-scope="crear_usuario"
                          data-vv-name="primer nombre"
                          v-validate="'required|max:50'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_usuario.primer nombre'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="4">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.segundo_nombre"
                          type="text"
                          label="segundo nombre"
                          data-vv-scope="crear_usuario"
                          data-vv-name="segundo nombre"
                          v-validate="'max:50'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_usuario.segundo nombre'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="4">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.primer_apellido"
                          type="text"
                          label="primer apellido"
                          data-vv-scope="crear_usuario"
                          data-vv-name="primer apellido"
                          v-validate="'required|max:50'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_usuario.primer apellido'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="4">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.segundo_apellido"
                          type="text"
                          label="segundo apellido"
                          data-vv-scope="crear_usuario"
                          data-vv-name="segundo apellido"
                          v-validate="'max:50'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_usuario.segundo apellido'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="4">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.apellido_casada"
                          type="text"
                          label="apellido de casad@"
                          data-vv-scope="crear_usuario"
                          data-vv-name="apellido de casad@"
                          v-validate="'max:50'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_usuario.apellido de casad@'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-autocomplete
                          v-model="form.municipio_id"
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
                          data-vv-scope="crear_usuario"
                          data-vv-name="departamento y municipio"
                        ></v-autocomplete>
                        <FormError
                          :attribute_name="'crear_usuario.departamento y municipio'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.ubicacion"
                          type="text"
                          label="dirección exacta"
                          data-vv-scope="crear_usuario"
                          data-vv-name="dirección exacta"
                          v-validate="'max:100'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_usuario.dirección exacta'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="4">
                        <vue-phone-number-input
                          v-model="number"
                          default-country-code="GT"
                          size="lg"
                          :dark="true"
                          :translations="translations"
                          show-code-on-list
                          @update="validar_numero($event)"
                          v-validate="'required'"
                          data-vv-scope="crear_usuario"
                          data-vv-name="número de teléfono"
                          :no-flags="true"
                          required
                        />
                        <FormError
                          :attribute_name="'crear_usuario.número de teléfono'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="4">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.email"
                          type="text"
                          label="Correo electrónico"
                          data-vv-scope="crear_usuario"
                          data-vv-name="correo"
                          v-validate="'required|email|max:75'"
                          @input="form.email = $event.toLowerCase()"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_usuario.correo'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="4" v-if="form.id == 0">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.password"
                          type="password"
                          label="Ingresar la contraseña"
                          data-vv-scope="crear_usuario"
                          data-vv-name="contraseña"
                          v-validate="'required|min:6'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_usuario.contraseña'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="12" v-if="!editedIndex">
                        <v-select
                          v-model="form.roles"
                          :items="roles"
                          chips
                          label="Seleccione uno o más roles"
                          multiple
                          outlined
                          :clearable="true"
                          :deletable-chips="true"
                          item-text="nombre"
                          item-value="id"
                          return-object
                          v-validate="'required'"
                          data-vv-scope="crear_usuario"
                          data-vv-name="roles"
                        ></v-select>
                        <FormError
                          :attribute_name="'crear_usuario.roles'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="12">
                        <v-textarea
                          counter
                          outlined
                          auto-grow
                          rows="3"
                          row-height="15"
                          v-model="form.observation"
                          type="text"
                          label="Observaciones"
                          data-vv-name="observaciones"
                          v-validate="'max:250'"
                          @input="form.observation = $event.toUpperCase()"
                        ></v-textarea>
                        <FormError
                          :attribute_name="'observaciones'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="12">
                        <v-file-input
                          outlined
                          v-model="temp"
                          @change="cargarImagen"
                          accept="image/*"
                          placeholder="Imagen"
                          prepend-icon="mdi-camera"
                        ></v-file-input>
                      </v-col>
                      <v-col cols="12" md="6">
                        <cropper
                          class="cropper"
                          image-restriction="area"
                          :src="imagen_upload"
                          ref="cropper"
                          stencil-component="circle-stencil"
                        ></cropper>
                        <div class="text-center" v-if="imagen_upload">
                          <v-btn
                            class="mx-2"
                            fab
                            dark
                            color="indigo"
                            @click="crop"
                          >
                            <v-icon class="material-icons">
                              format_shapes
                            </v-icon>
                          </v-btn>
                        </div>
                      </v-col>
                      <v-col cols="12" md="6" class="text-center">
                        <v-list-item-avatar
                          style="height: 80%; min-width: 80%; width: 80%;"
                        >
                          <img :src="form.foto" alt width="80%" height="80%" />
                        </v-list-item-avatar>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>
              </v-card>
            </v-dialog>
          </v-toolbar>
        </template>
        <template v-slot:item.name="{ item }">
          <br />
          <div class="text-center">
            <v-avatar size="75">
              <img :src="item.picture" :alt="item.full_name" />
            </v-avatar>
            <br />
            {{ item.cui }}
            <br />
            {{ item.full_name }}
          </div>
          <br />
        </template>

        <template v-slot:item.users_rols="{ item }">
          <template v-for="x in item.roles">
            <span v-bind:key="x.rol.nombre + x.id" v-text="x.rol.nombre"></span>
            <br v-bind:key="x.id" />
          </template>
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
            color="orange lighten-2"
            v-if="!item.deleted_at"
            @click="cambiar_password(item)"
          >
            <v-icon>mdi-key</v-icon>
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
          <v-btn
            class="ma-2"
            text
            icon
            color="info lighten-2"
            @click="mapear_roles(item)"
          >
            <v-icon class="material-icons">library_books</v-icon>
          </v-btn>
        </template>
        <template v-slot:no-data>
          <br />
          <v-alert type="error">No hay información para mostrar.</v-alert>
        </template>
      </v-data-table>

      <v-dialog v-model="dialog_rol" color="primary">
        <v-card height="400px">
          <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
          </v-overlay>
          <v-card-title>
            <span class="headline">
              Agregar rol para el usuario {{ full_name }}
            </span>
          </v-card-title>

          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12" md="12" v-if="!editedIndex">
                  <v-select
                    v-model="form_rol.roles"
                    :items="roles"
                    chips
                    label="Seleccione uno o más roles"
                    multiple
                    outlined
                    :clearable="true"
                    :deletable-chips="true"
                    item-text="nombre"
                    item-value="id"
                    return-object
                    v-validate="'required'"
                    data-vv-scope="crear_rol"
                    data-vv-name="roles"
                  ></v-select>
                  <FormError
                    :attribute_name="'crear_rol.roles'"
                    :errors_form="errors"
                  ></FormError>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" text @click="dialog_rol = false">
              Cancelar
            </v-btn>
            <v-btn
              color="blue darken-1"
              text
              @click="validar_formulario_rol('crear_rol')"
            >
              Guardar
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog
        v-model="dialog_password"
        width="35%"
        persistent
        color="primary"
      >
        <v-card>
          <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
          </v-overlay>
          <v-card-title>
            <span class="headline">Cambiar contraseña</span>
          </v-card-title>

          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12" md="12">
                  <v-text-field
                    counter
                    outlined
                    v-model="form.password"
                    type="password"
                    label="Ingresar la contraseña"
                    data-vv-scope="crear_password"
                    data-vv-name="contraseña"
                    v-validate="'required|min:6'"
                  ></v-text-field>
                  <FormError
                    :attribute_name="'crear_password.contraseña'"
                    :errors_form="errors"
                  ></FormError>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" @click="dialog_password = false">
              Cancelar
            </v-btn>
            <v-btn
              color="blue darken-1"
              @click="validar_formulario_password('crear_password')"
            >
              Guardar
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-col>
  </v-row>
</template>

<style scoped>
.preview {
  border: dashed 1px rgba(170, 29, 29, 0.25);
}
.table {
  color: white;
}
</style>

<script>
import FormError from '../shared/FormError'

export default {
  name: 'Usuario',
  components: {
    FormError,
  },
  data() {
    return {
      loading: false,
      dialog: false,
      dialog_password: false,
      dialog_rol: false,
      esconder_boton: false,
      editedIndex: false,
      search: '',
      imagen_upload: null,
      number: null,
      temp: null,
      headers: [
        {
          text: 'Empresa',
          align: 'start',
          value: 'empresas.empresa.nombre',
        },
        {
          text: 'Nombre',
          align: 'start',
          value: 'name',
        },
        {
          text: 'E-mail',
          align: 'start',
          value: 'email',
        },
        {
          text: 'Teléfono',
          align: 'start',
          value: 'telefono',
        },
        {
          text: 'Rol',
          align: 'start',
          value: 'users_rols',
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
      municipios: [],
      roles: [],
      empresas: [],
      form: {
        id: 0,
        cui: null,
        primer_nombre: null,
        segundo_nombre: null,
        primer_apellido: null,
        segundo_apellido: null,
        apellido_casada: null,
        foto: null,
        email: null,
        observation: null,
        ubicacion: null,
        phone: null,
        municipio_id: null,
        roles: [],
        password: null,
        empresas_id: null,
      },
      form_rol: {
        id: null,
        roles: [],
      },
      translations: {
        countrySelectorLabel: 'Código de país',
        countrySelectorError: 'Elige un país',
        phoneNumberLabel: 'Número de teléfono',
        example: 'Ejemplo :',
      },
      full_name: '',
    }
  },
  computed: {
    formTitle() {
      return !this.editedIndex ? 'Agregar usuario' : 'Administrar usuario'
    },
  },

  watch: {
    dialog(val) {
      val || this.close()
    },
  },

  created() {
    this.initialize()
    this.getRoles()
    this.getMunicipios()
    this.getEmpresa()
  },

  methods: {
    validar_numero(e) {
      this.form.telefono = e.isValid ? e.phoneNumber : null
      this.esconder_boton = this.form.telefono ? true : false
    },

    limpiar() {
      this.editedIndex = false
      this.imagen_upload = null
      this.temp = null
      this.number = null
      this.esconder_boton = false

      this.form.id = 0
      this.form.cui = null
      this.form.primer_nombre = null
      this.form.segundo_nombre = null
      this.form.primer_apellido = null
      this.form.segundo_apellido = null
      this.form.apellido_casada = null
      this.form.foto = null
      this.form.email = null
      this.form.observation = null
      this.form.ubicacion = null
      this.form.telefono = null
      this.form.municipio_id = null
      this.form.roles = []
      this.form.password = null
      this.form.empresas_id = null

      this.$validator.reset()
      this.$validator.reset()
    },

    initialize() {
      this.loading = true

      this.$store.state.services.userService
        .index()
        .then((r) => {
          if (r.response) {
            if (r.response.data.code === 423) {
              this.$toastr.error(r.response.data.error, 'Mensaje')
            } else {
              for (let value of Object.values(r.response.data.error)) {
                this.$toastr.error(value, 'Mensaje')
              }
            }
            this.loading = false
            return
          }

          this.desserts = r.data.data
          this.close()
          this.loading = false
        })
        .catch((r) => {
          this.loading = false
        })
    },

    mapear(item) {
      this.form.id = item.id
      this.form.cui = item.cui
      this.form.primer_nombre = item.primer_nombre
      this.form.segundo_nombre = item.segundo_nombre
      this.form.primer_apellido = item.primer_apellido
      this.form.segundo_apellido = item.segundo_apellido
      this.form.apellido_casada = item.apellido_casada
      this.form.email = item.email
      this.form.observation = item.observation
      this.form.ubicacion = item.ubicacion
      this.form.telefono = item.telefono
      this.form.municipio_id = item.municipio
      this.imagen_upload = item.picture
      this.form.empresas_id = item.empresas.empresa

      this.number = item.telefono

      this.editedIndex = true
      this.dialog = true
    },

    cambiar_password(item) {
      this.loading = true
      this.form.id = item.id
      this.dialog_password = true
      this.loading = false
    },

    validar_formulario_password(scope) {
      this.$validator.validateAll(scope).then((result) => {
        if (result) {
          this.$swal({
            title: 'Cambiar contraseña',
            text: '¿Está seguro de realizar esta acción?',
            type: 'warning',
            showCancelButton: true,
          }).then((result) => {
            if (result.value) {
              this.form.password = window.btoa(this.form.password)
              this.loading = true
              this.$store.state.services.userService
                .reset(this.form)
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
                  this.limpiar()
                  this.dialog_password = false
                })
                .catch((r) => {
                  this.loading = false
                })
            } else {
              this.close()
            }
          })
        }
      })
    },

    close() {
      this.limpiar()
      this.dialog = false
      this.dialog_rol = false
    },

    validar_formulario(scope) {
      this.$validator.validateAll(scope).then((result) => {
        if (result)
          this.editedIndex ? this.update(this.form) : this.store(this.form)
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
          this.$store.state.services.userService
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
          this.form.password = window.btoa(this.form.password)
          this.loading = true
          this.$store.state.services.userService
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
          this.$store.state.services.userService
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

    //necesarios
    cargarImagen(e) {
      this.imagen_upload = null
      typeof e !== 'undefined'
        ? (this.imagen_upload = URL.createObjectURL(e))
        : null
      this.form.foto = null
    },

    crop() {
      this.form.foto = null
      const { coordinates, canvas } = this.$refs.cropper.getResult()
      this.form.foto = canvas.toDataURL()
    },

    getRoles() {
      this.$store.state.services.rolService
        .index()
        .then((r) => {
          this.roles = r.data.data
        })
        .catch((r) => {})
    },

    getMunicipios() {
      this.$store.state.services.selectController
        .municipio()
        .then((r) => {
          this.municipios = r.data.data
        })
        .catch((r) => {})
    },

    getEmpresa() {
      this.$store.state.services.selectController
        .empresa()
        .then((r) => {
          this.empresas = r.data.data
        })
        .catch((r) => {})
    },

    limipiar_form_rol() {
      this.form_rol.id = 0
      this.form_rol.roles = []

      this.$validator.reset()
      this.$validator.reset()
    },

    mapear_roles(item) {
      this.limipiar_form_rol()
      this.form_rol.id = item.id
      this.full_name = item.full_name

      item.roles.forEach((element) => {
        this.form_rol.roles.push(element.rol)
      })

      this.dialog_rol = true
    },

    validar_formulario_rol(scope) {
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
              this.$store.state.services.userRolService
                .store(this.form_rol)
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
        }
      })
    },
  },
}
</script>
