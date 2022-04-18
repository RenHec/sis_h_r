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
            <v-toolbar-title>Tipo de cama</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-text-field
              v-model="search"
              append-icon="search"
              label="Buscar"
              single-line
              hide-details
            ></v-text-field>
            <v-spacer></v-spacer>

            <v-dialog v-model="dialog" color="primary" width="80%">
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
                      <v-col cols="12" md="12">
                        <v-text-field
                          prepend-icon="fiber_new"
                          counter
                          outlined
                          v-model="form.nombre"
                          type="text"
                          label="nombre del tipo de cama"
                          data-vv-scope="crear"
                          data-vv-name="nombre del tipo de cama"
                          v-validate="'required|max:50'"
                          @input="form.nombre = $event.toUpperCase()"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear.nombre del tipo de cama'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="4" md="4">
                        <v-text-field
                          class="mx-2"
                          rows="2"
                          prepend-icon="fiber_new"
                          counter
                          outlined
                          v-model="form.cantidad"
                          type="text"
                          label="cantidad"
                          data-vv-scope="crear"
                          data-vv-name="cantidad"
                          v-validate="'required|integer'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear.cantidad'"
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
                    @click="validar_formulario('crear')"
                  >
                    Guardar
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </v-toolbar>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-tooltip bottom color="red lighten-2">
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                class="ma-2"
                text
                icon
                color="red lighten-2"
                @click="destroy(item)"
                dark
                v-bind="attrs"
                v-on="on"
                :disabled="item.id !== 1 ? false : true"
              >
                <v-icon>mdi-thumb-down</v-icon>
              </v-btn>
            </template>
            <span v-text="`Eliminar ${item.nombre}`"></span>
          </v-tooltip>
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
import FormError from '../../../shared/FormError.vue'
export default {
  name: 'HTipoCama',
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
        {
          text: 'Cantidad',
          align: 'start',
          value: 'cantidad',
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
        nombre: null,
        cantidad: null,
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
      this.form.nombre = null
      this.form.cantidad = null
      this.editedIndex = false

      this.$validator.reset()
      this.$validator.reset()
    },

    initialize() {
      this.loading = true

      this.$store.state.services.TipoCamaService.getAll()
        .then((r) => {
          this.desserts = r.data
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
        if (result) this.store(this.form)
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
          this.$store.state.services.TipoCamaService.delete(data)
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
          this.$store.state.services.TipoCamaService.store(data)
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
