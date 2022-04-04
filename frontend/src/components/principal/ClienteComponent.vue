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
            <v-toolbar-title>Clientes</v-toolbar-title>
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
            <v-dialog v-model="dialog" color="primary" width="30%">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  :loading="dialog"
                  :disabled="dialog"
                  @click="dialog = true"
                  color="primary"
                  dark
                  small
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
                  <span class="headline">{{ formTitle }}</span>
                </v-card-title>

                <v-card-text>
                  <v-container>
                    <v-row>
                      <v-col cols="12" md="6">
                        <v-text-field
                          filled-inverted
                          suffix
                          dense
                          dark
                          prepend-inner-icon="fiber_new"
                          counter
                          v-model="form.nit"
                          type="text"
                          label="nit"
                          data-vv-scope="crear"
                          data-vv-name="nit"
                          v-validate="'required'"
                          hint="El número de nit del cliente, caso contrario CF"
                          persistent-hint
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear.nit'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="12">
                        <v-text-field
                          filled-inverted
                          suffix
                          dense
                          dark
                          prepend-inner-icon="fiber_new"
                          counter
                          v-model="form.nombre"
                          type="text"
                          label="nombre"
                          data-vv-scope="crear"
                          data-vv-name="nombre"
                          v-validate="'required'"
                          hint="El nombre del cliente, caso contrario CF"
                          persistent-hint
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear.nombre'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="12">
                        <v-select
                          filled-inverted
                          suffix
                          dense
                          dark
                          prepend-inner-icon="view_carousel"
                          v-model="form.municipios_id"
                          :items="municipios"
                          label="seleccione un municipio por favor"
                          :clearable="true"
                          :deletable-chips="true"
                          item-text="nombre"
                          item-value="id"
                          return-object
                          v-validate="'required'"
                          data-vv-scope="crear"
                          data-vv-name="municipio"
                        ></v-select>
                        <FormError
                          :attribute_name="'crear.municipio'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="12">
                        <v-text-field
                          filled-inverted
                          suffix
                          dense
                          dark
                          prepend-inner-icon="fiber_new"
                          counter
                          v-model="form.direcciones"
                          type="text"
                          label="dirección"
                          hint="dirección, caso contrario dejar en blanco"
                          persistent-hint
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="5">
                        <v-text-field
                          filled-inverted
                          suffix
                          dense
                          dark
                          prepend-inner-icon="fiber_new"
                          counter
                          v-model="form.telefonos"
                          type="text"
                          label="teléfono"
                          hint="número de teléfono, caso contrario dejar en blanco"
                          persistent-hint
                        ></v-text-field>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col cols="12" md="8">
                        <v-text-field
                          filled-inverted
                          suffix
                          dense
                          dark
                          prepend-inner-icon="fiber_new"
                          counter
                          v-model="form.emails"
                          type="text"
                          label="correo electrónico"
                          hint="correo electrónico, caso contrario dejar en blanco"
                          persistent-hint
                        ></v-text-field>
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
                    @click="validar_formulario('crear_presentacion')"
                  >
                    Guardar
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </v-toolbar>
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
  name: 'Cliente',
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
          align: 'center',
          value: 'nit',
        },
        {
          text: 'Nombre',
          align: 'center',
          value: 'nombre',
        },
        {
          text: 'Teléfono',
          align: 'center',
          value: 'telefonos',
        },
        {
          text: 'E-mail',
          align: 'center',
          value: 'emails',
        },
        {
          text: 'Departamento',
          align: 'center',
          value: 'departamento.nombre',
        },
        {
          text: 'Municipio',
          align: 'center',
          value: 'municipio.nombre',
        },
        {
          text: 'Dirección',
          align: 'center',
          value: 'direcciones',
        },
        { text: 'Opciones', value: 'actions', sortable: false, search: false },
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

      municipios: [],
      form: {
        id: 0,
        nit: null,
        nombre: null,
        telefonos: null,
        emails: null,
        direcciones: null,
        municipios_id: null,
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
    this.getSelects()
  },

  methods: {
    limpiar() {
      this.form.id = 0
      this.form.nit = null
      this.form.nombre = null
      this.form.telefonos = null
      this.form.emails = null
      this.form.direcciones = null
      this.form.municipios_id = null
      this.editedIndex = false

      this.$validator.reset()
      this.$validator.reset()
    },

    getSelects() {
      this.$store.state.services.selectController
        .municipio()
        .then((r) => {
          this.municipios = r.data.data
        })
        .catch((e) => {
          this.errorResponse(e)
        })
    },

    initialize() {
      this.loading = true
      this.$store.state.services.ClienteCatalogoService.getAll()
        .then((r) => {
          this.desserts = r.data
        })
        .catch((e) => {
          this.errorResponse(e)
        })
        .finally(() => {
          this.close()
          this.loading = false
        })
    },

    mapear(item) {
      this.form.id = item.id
      this.form.nit = item.nit
      this.form.nombre = item.nombre
      this.form.telefonos = item.telefonos
      this.form.emails = item.emails
      this.form.direcciones = item.direcciones
      this.form.municipios_id = item.municipio

      this.editedIndex = true
      this.dialog = true
    },

    close() {
      this.limpiar()
      this.dialog = false
    },

    validar_formulario() {
      this.$validator.validateAll().then((result) => {
        if (result) this.update(this.form)
      })
    },

    destroy(data) {
      this.$swal({
        title: !data.deleted_at ? 'Desactivar' : 'Activar',
        text: '¿Está seguro de realizar esta acción?',
        type: !data.deleted_at ? 'error' : 'success',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.ClienteCatalogoService.delete(data)
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
          this.$store.state.services.ClienteCatalogoService.update(data)
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
  },
}
</script>
