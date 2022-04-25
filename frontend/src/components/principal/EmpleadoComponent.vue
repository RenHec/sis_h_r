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
            <v-toolbar-title>Empleados</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-text-field
              v-model="search"
              append-icon="search"
              label="Buscar"
              single-line
              hide-details
            ></v-text-field>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-btn color="white" small @click="initialize">
              <v-icon :color="'success'">sync</v-icon>
            </v-btn>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-dialog
              v-model="dialog"
              color="primary"
              width="85%"
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
                  small
                  class="ma-2"
                  v-bind="attrs"
                  v-on="on"
                >
                  Agregar
                </v-btn>
              </template>
              <v-card dark>
                <v-overlay :value="loading">
                  <v-progress-circular
                    indeterminate
                    size="64"
                  ></v-progress-circular>
                </v-overlay>

                <v-card-title>
                  <v-btn icon @click="close" large color="red">
                    <v-icon>mdi-close</v-icon>
                  </v-btn>
                  <v-spacer></v-spacer>
                  {{ formTitle }}
                  <v-spacer></v-spacer>
                  <v-btn
                    large
                    color="success"
                    @click="validar_formulario('crear_empleado')"
                  >
                    Guardar
                  </v-btn>
                </v-card-title>

                <v-card-text>
                  <v-container>
                    <v-row>
                      <v-col cols="12" md="6">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.cui"
                          type="text"
                          label="número de CUI"
                          data-vv-scope="crear_empleado"
                          data-vv-name="número de CUI"
                          v-validate="'required|digits:13'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_empleado.número de CUI'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="6"></v-col>
                      <v-col cols="12" md="6">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.primer_nombre"
                          type="text"
                          label="primer nombre"
                          data-vv-scope="crear_empleado"
                          data-vv-name="primer nombre"
                          v-validate="'required|max:50'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_empleado.primer nombre'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.segundo_nombre"
                          type="text"
                          label="segundo nombre"
                          data-vv-scope="crear_empleado"
                          data-vv-name="segundo nombre"
                          v-validate="'max:50'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_empleado.segundo nombre'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.primer_apellido"
                          type="text"
                          label="primer apellido"
                          data-vv-scope="crear_empleado"
                          data-vv-name="primer apellido"
                          v-validate="'required|max:50'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_empleado.primer apellido'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.segundo_apellido"
                          type="text"
                          label="segundo apellido"
                          data-vv-scope="crear_empleado"
                          data-vv-name="segundo apellido"
                          v-validate="'max:50'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_empleado.segundo apellido'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="8">
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
                          data-vv-scope="crear_empleado"
                          data-vv-name="departamento y municipio"
                        ></v-autocomplete>
                        <FormError
                          :attribute_name="'crear_empleado.departamento y municipio'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="12">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.ubicacion"
                          type="text"
                          label="dirección exacta"
                          data-vv-scope="crear_empleado"
                          data-vv-name="dirección exacta"
                          v-validate="'max:100'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_empleado.dirección exacta'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="6">
                        <vue-phone-number-input
                          v-model="number"
                          default-country-code="GT"
                          size="lg"
                          :dark="true"
                          :translations="translations"
                          show-code-on-list
                          @update="validar_numero($event)"
                          v-validate="'required'"
                          data-vv-scope="crear_empleado"
                          data-vv-name="número de teléfono"
                          :no-flags="true"
                          required
                        />
                        <FormError
                          :attribute_name="'crear_empleado.número de teléfono'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field
                          counter
                          outlined
                          v-model="form.email"
                          type="text"
                          label="Correo electrónico"
                          data-vv-scope="crear_empleado"
                          data-vv-name="correo"
                          v-validate="'required|email|max:75'"
                          @input="form.email = $event.toLowerCase()"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_empleado.correo'"
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
                          data-vv-scope="crear_empleado"
                          data-vv-name="contraseña"
                          v-validate="'required|min:6'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear_empleado.contraseña'"
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
                        <v-list-item-avatar size="300">
                          <img :src="form.foto" />
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

        <template v-slot:item.actions="{ item }">
          <v-btn
            class="ma-2"
            text
            icon
            large
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
            large
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
            large
            :color="'green'"
            @click="crear_usuario(item)"
            v-if="!item.usuario"
          >
            <v-icon>account_circle</v-icon>
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
  name: 'Empleado',
  components: {
    FormError,
  },
  data() {
    return {
      loading: false,
      dialog: false,

      esconder_boton: false,
      editedIndex: false,
      search: '',
      imagen_upload: null,
      number: null,
      temp: null,
      headers: [
        {
          text: 'CUI',
          align: 'center',
          value: 'cui',
        },
        {
          text: 'Nombre',
          align: 'center',
          value: 'name',
        },
        {
          text: 'E-mail',
          align: 'center',
          value: 'email',
        },
        {
          text: 'Teléfono',
          align: 'center',
          value: 'telefono',
        },
        {
          text: 'Opciones',
          align: 'center',
          value: 'actions',
          sortable: false,
        },
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
        ubicacion: null,
        telefono: null,
        municipio_id: null,
        roles: [],
        password: null,
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
      return !this.editedIndex ? 'Agregar empleado' : 'Administrar empleado'
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

      this.$validator.reset()
      this.$validator.reset()
    },

    initialize() {
      this.loading = true

      this.$store.state.services.EmpleadoCatalogoService.getAll()
        .then((r) => {
          this.desserts = r.data
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
      this.form.cui = item.cui
      this.form.primer_nombre = item.primer_nombre
      this.form.segundo_nombre = item.segundo_nombre
      this.form.primer_apellido = item.primer_apellido
      this.form.segundo_apellido = item.segundo_apellido
      this.form.email = item.email
      this.form.ubicacion = item.ubicacion
      this.form.telefono = item.telefono
      this.form.municipio_id = item.municipio
      this.imagen_upload = item.picture
      this.form.foto = item.picture

      this.number = item.telefono

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
          this.$store.state.services.EmpleadoCatalogoService.delete(data)
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
          this.$store.state.services.EmpleadoCatalogoService.store(data)
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
        title: 'Modificar',
        text: '¿Está seguro de realizar esta acción?',
        type: 'warning',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.EmpleadoCatalogoService.update(data)
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

    crear_usuario(data) {
      this.$swal({
        title: 'Crear Usuario',
        text: '¿Está seguro de realizar esta acción?',
        type: 'question',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.EmpleadoCatalogoService.show(data)
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
