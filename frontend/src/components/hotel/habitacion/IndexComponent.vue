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
            <v-toolbar-title>Habitaciones</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-text-field
              v-model="search"
              append-icon="search"
              label="Buscar"
              single-line
              hide-details
            ></v-text-field>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-dialog v-model="dialog" color="primary" width="50%" persistent>
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
                    <v-row v-if="!editedIndex">
                      <v-col cols="12" md="12">
                        <v-text-field
                          class="mx-2"
                          prepend-icon="fiber_new"
                          counter
                          outlined
                          v-model="form.nombre"
                          type="text"
                          label="nombre del cobro"
                          data-vv-scope="crear"
                          data-vv-name="nombre del cobro"
                          v-validate="'required|max:75'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear.nombre del cobro'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="3">
                        <v-checkbox
                          class="mx-2"
                          prepend-icon="fiber_new"
                          v-model="form.incluye_desayuno"
                          hide-details
                          :label="`¿Incluye desayuno? ${
                            form.incluye_desayuno ? 'SI' : 'NO'
                          }`"
                          data-vv-scope="crear"
                          data-vv-name="incluye desayuno"
                          v-validate="'required'"
                        ></v-checkbox>
                        <FormError
                          :attribute_name="'crear.incluye desayuno'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="3" v-if="form.incluye_desayuno">
                        <v-text-field
                          class="mx-2"
                          prepend-icon="fiber_new"
                          counter
                          outlined
                          v-model="form.precio_desayuno"
                          type="text"
                          label="precio del desayuno"
                          data-vv-scope="crear"
                          data-vv-name="precio del desayuno"
                          v-validate="'required|integer'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear.precio del desayuno'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" md="3">
                        <v-text-field
                          class="mx-2"
                          prepend-icon="fiber_new"
                          counter
                          outlined
                          v-model="form.precio_habitacion"
                          type="text"
                          label="precio de la habitación"
                          data-vv-scope="crear"
                          data-vv-name="precio de la habitación"
                          v-validate="'required|integer'"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'crear.precio de la habitación'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col cols="12" :md="form.incluye_desayuno ? '3' : '6'">
                        <v-select
                          prepend-icon="view_carousel"
                          v-model="form.h_tipos_camas_id"
                          :items="h_tipos_camas"
                          chips
                          label="seleccione un tipo de cama por favor"
                          outlined
                          :clearable="true"
                          :deletable-chips="true"
                          item-text="nombre"
                          item-value="id"
                          return-object
                          v-validate="'required'"
                          data-vv-scope="crear"
                          data-vv-name="tipo de cama"
                        ></v-select>
                        <FormError
                          :attribute_name="'crear.tipo de cama'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col cols="12" md="12">
                        <v-textarea
                          class="mx-2"
                          rows="2"
                          prepend-icon="fiber_new"
                          counter
                          outlined
                          v-model="form.descripcion"
                          type="text"
                          label="descripción"
                          data-vv-scope="crear"
                          data-vv-name="descripción"
                          v-validate="'required|max:500'"
                        ></v-textarea>
                        <FormError
                          :attribute_name="'crear.descripción'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                    </v-row>
                    <v-row v-if="!editedIndex">
                      <v-col cols="12" md="4">
                        <v-file-input
                          outlined
                          v-model="form.imagen"
                          @change="cargarImagen"
                          accept="image/*"
                          placeholder="Imagén de la habitación"
                          prepend-icon="mdi-camera"
                          data-vv-scope="crear"
                          data-vv-name="imagén de la habitación"
                          v-validate="'required'"
                        ></v-file-input>
                        <FormError
                          :attribute_name="'crear.imagén de la habitación'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                    </v-row>
                    <v-row v-if="editedIndex">
                      <v-col cols="12" md="4">
                        <v-select
                          prepend-icon="view_carousel"
                          v-model="form.h_estados_id"
                          :items="h_estados"
                          chips
                          label="seleccione un estado por favor"
                          outlined
                          :clearable="true"
                          :deletable-chips="true"
                          item-text="nombre"
                          item-value="id"
                          return-object
                          v-validate="'required'"
                          data-vv-scope="crear"
                          data-vv-name="estado de la habitación"
                        ></v-select>
                        <FormError
                          :attribute_name="'crear.estado de la habitación'"
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
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-btn color="white" small @click="initialize">
              <v-icon :color="colorTolbar">sync</v-icon>
            </v-btn>
          </v-toolbar>
        </template>
        <template v-slot:item.fotografia="{ item }">
          <br />
          <v-card
            class="mx-auto"
            max-width="400"
            :color="colorCardH(item.h_estados_id)"
          >
            <v-img
              class="white--text align-end"
              height="200px"
              :src="item.picture"
              :alt="`Habitacion#${item.numero}`"
            >
              <v-card-title
                v-text="`Habitación #${item.numero}`"
              ></v-card-title>
            </v-img>

            <div class="text-center">
              <v-chip class="ma-2" color="greey lighten-5" text-color="white">
                <v-avatar
                  left
                  class="blue darken-4"
                  v-text="`${item.huespedes}`"
                ></v-avatar>
                {{ item.huespedes > 1 ? 'Huespedes' : 'Huesped' }}
              </v-chip>
            </div>

            <v-divider></v-divider>

            <v-list subheader two-line :color="colorCardH(item.h_estados_id)">
              <v-list-item
                v-for="(habitacion_precio, key) in item.precios"
                :key="`Precio#${key}`"
              >
                <v-list-item-avatar>
                  <v-icon class="lighten-3" dark>local_atm</v-icon>
                </v-list-item-avatar>

                <v-list-item three-line>
                  <v-list-item-content>
                    <v-list-item-title
                      v-text="
                        `Precio habitación: ${formato_moneda(
                          1,
                          habitacion_precio.precio_habitacion,
                          0,
                        )}`
                      "
                      v-if="habitacion_precio.incluye_desayuno"
                    ></v-list-item-title>
                    <v-list-item-title
                      v-text="
                        `Precio desayuno: ${formato_moneda(
                          1,
                          habitacion_precio.precio_desayuno,
                          0,
                        )}`
                      "
                      v-if="habitacion_precio.incluye_desayuno"
                    ></v-list-item-title>
                    <v-list-item-title
                      v-text="
                        `Precio total: ${formato_moneda(
                          1,
                          habitacion_precio.precio,
                          0,
                        )}`
                      "
                    ></v-list-item-title>
                    <v-list-item-subtitle
                      v-text="`${habitacion_precio.nombre}`"
                    ></v-list-item-subtitle>
                    <v-list-item-subtitle
                      v-text="
                        `${habitacion_precio.tipo_cama.nombre} | Persona: ${habitacion_precio.tipo_cama.cantidad}`
                      "
                    ></v-list-item-subtitle>
                    <v-list-item-subtitle
                      v-text="
                        `Incluye desayuno: ${
                          habitacion_precio.incluye_desayuno ? 'SI' : 'NO'
                        }`
                      "
                    ></v-list-item-subtitle>
                    <v-list-item-subtitle
                      v-text="habitacion_precio.activo ? 'ACTIVO' : 'INACTIVO'"
                    ></v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>

                <v-list-item-action>
                  <v-tooltip
                    bottom
                    :color="
                      habitacion_precio.activo
                        ? 'red lighten-1'
                        : 'green lighten-1'
                    "
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        icon
                        dark
                        v-bind="attrs"
                        v-on="on"
                        @click="quitar_precio(habitacion_precio)"
                      >
                        <v-icon
                          :color="
                            habitacion_precio.activo
                              ? 'red lighten-1'
                              : 'green lighten-1'
                          "
                          v-text="
                            habitacion_precio.activo
                              ? 'dangerous'
                              : 'check_circle'
                          "
                        ></v-icon>
                      </v-btn>
                    </template>
                    <span
                      v-text="
                        habitacion_precio.activo
                          ? `Desactivar el precio ${formato_moneda(
                              habitacion_precio.precio,
                            )}`
                          : `Activar el precio ${formato_moneda(
                              habitacion_precio.precio,
                            )}`
                      "
                    ></span>
                  </v-tooltip>
                </v-list-item-action>
              </v-list-item>

              <v-divider></v-divider>

              <v-slide-group center-active show-arrows>
                <v-slide-item
                  v-for="(imagen, key) in item.imagenes"
                  :key="`Fotografia#${key}`"
                >
                  <v-avatar
                    color="grey"
                    class="ma-3"
                    @click="opciones_imagen(imagen, key)"
                    size="81"
                    tile
                  >
                    <v-img :src="imagen.picture" :alt="imagen.picture"></v-img>
                  </v-avatar>
                </v-slide-item>
              </v-slide-group>
            </v-list>

            <v-card-actions>
              <v-row justify="center" align="center">
                <v-btn-toggle dark rounded>
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
                    <span v-text="`Editar habitacion #${item.numero}`"></span>
                  </v-tooltip>
                  <v-tooltip bottom color="blue lighten-2">
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        text
                        icon
                        color="blue lighten-2"
                        @click="modal_foto(item)"
                        dark
                        v-bind="attrs"
                        v-on="on"
                        :loading="form_foto.dialog"
                        :disabled="form_foto.dialog"
                      >
                        <v-icon>perm_media</v-icon>
                      </v-btn>
                    </template>
                    <span
                      v-text="
                        `Agregar fotografía a la habitacion #${item.numero}`
                      "
                    ></span>
                  </v-tooltip>
                  <v-tooltip color="black lighten-2" bottom>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        text
                        icon
                        color="white lighten-2"
                        @click="modal_precio(item)"
                        dark
                        v-bind="attrs"
                        v-on="on"
                        :loading="form_precio.dialog"
                        :disabled="form_precio.dialog"
                      >
                        <v-icon>local_atm</v-icon>
                      </v-btn>
                    </template>
                    <span
                      v-text="`Agregar precio a la habitacion #${item.numero}`"
                    ></span>
                  </v-tooltip>
                  <v-tooltip bottom color="red lighten-2">
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        text
                        icon
                        color="red lighten-2"
                        @click="destroy(item)"
                        dark
                        v-bind="attrs"
                        v-on="on"
                      >
                        <v-icon>mdi-thumb-down</v-icon>
                      </v-btn>
                    </template>
                    <span v-text="`Eliminar habitacion #${item.numero}`"></span>
                  </v-tooltip>
                </v-btn-toggle>
              </v-row>
            </v-card-actions>
          </v-card>
          <br />
        </template>
        <template v-slot:no-data>
          <br />
          <v-alert type="error">No hay información para mostrar.</v-alert>
        </template>
      </v-data-table>

      <!-- ::::::::  PRECIOS ::::::::::: -->
      <v-dialog
        v-model="form_precio.dialog"
        color="primary"
        width="30%"
        persistent
      >
        <v-card>
          <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
          </v-overlay>
          <v-card-title>
            <span class="headline" v-if="form_precio.h_habitaciones_id">
              {{
                `Agregar precio a la habitación #${form_precio.h_habitaciones_id.numero}`
              }}
            </span>
          </v-card-title>

          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12" md="12">
                  <v-text-field
                    class="mx-2"
                    prepend-icon="fiber_new"
                    counter
                    outlined
                    v-model="form_precio.nombre"
                    type="text"
                    label="nombre del cobro"
                    data-vv-scope="crear_precio"
                    data-vv-name="nombre del cobro"
                    v-validate="'required|max:75'"
                  ></v-text-field>
                  <FormError
                    :attribute_name="'crear_precio.nombre del cobro'"
                    :errors_form="errors"
                  ></FormError>
                </v-col>
                <v-col cols="12" md="12">
                  <v-checkbox
                    class="mx-2"
                    prepend-icon="fiber_new"
                    v-model="form_precio.incluye_desayuno"
                    hide-details
                    :label="`¿Incluye desayuno? ${
                      form_precio.incluye_desayuno ? 'SI' : 'NO'
                    }`"
                    data-vv-scope="crear_precio"
                    data-vv-name="incluye desayuno"
                    v-validate="'required'"
                  ></v-checkbox>
                  <FormError
                    :attribute_name="'crear_precio.incluye desayuno'"
                    :errors_form="errors"
                  ></FormError>
                </v-col>
                <v-col cols="12" md="12" v-if="form.incluye_desayuno">
                  <v-text-field
                    class="mx-2"
                    prepend-icon="fiber_new"
                    counter
                    outlined
                    v-model="form_precio.precio_desayuno"
                    type="text"
                    label="precio del desayuno"
                    data-vv-scope="crear_precio"
                    data-vv-name="precio del desayuno"
                    v-validate="'required|integer'"
                  ></v-text-field>
                  <FormError
                    :attribute_name="'crear_precio.precio del desayuno'"
                    :errors_form="errors"
                  ></FormError>
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field
                    class="mx-2"
                    prepend-icon="fiber_new"
                    counter
                    outlined
                    v-model="form_precio.precio_habitacion"
                    type="text"
                    label="precio de la habitación"
                    data-vv-scope="crear_precio"
                    data-vv-name="precio de la habitación"
                    v-validate="'required|integer'"
                  ></v-text-field>
                  <FormError
                    :attribute_name="'crear_precio.precio de la habitación'"
                    :errors_form="errors"
                  ></FormError>
                </v-col>
                <v-col cols="12" md="12">
                  <v-select
                    prepend-icon="view_carousel"
                    v-model="form_precio.h_tipos_camas_id"
                    :items="h_tipos_camas"
                    chips
                    label="seleccione un tipo de cama por favor"
                    outlined
                    :clearable="true"
                    :deletable-chips="true"
                    item-text="nombre"
                    item-value="id"
                    return-object
                    v-validate="'required'"
                    data-vv-scope="crear_precio"
                    data-vv-name="tipo de cama"
                  ></v-select>
                  <FormError
                    :attribute_name="'crear_precio.tipo de cama'"
                    :errors_form="errors"
                  ></FormError>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="red darken-1" @click="form_precio.dialog = false">
              Cancelar
            </v-btn>
            <v-btn
              color="blue darken-1"
              @click="agregar_precio('crear_precio')"
            >
              Guardar
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- ::::::::  FOTOGRAFIAS ::::::::::: -->
      <v-dialog
        v-model="form_foto.dialog"
        color="primary"
        width="30%"
        persistent
      >
        <v-card>
          <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
          </v-overlay>
          <v-card-title>
            <span class="headline" v-if="form_foto.h_habitaciones_id">
              {{
                `Agregar fotos a la habitación #${form_foto.h_habitaciones_id.numero}`
              }}
            </span>
          </v-card-title>

          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12" md="12">
                  <v-file-input
                    v-model="masiva_image"
                    color="deep-purple accent-4"
                    counter
                    accept="image/*"
                    multiple
                    placeholder="Seleccionar fotografías para la habitación"
                    outlined
                    :show-size="1000"
                    @change="cargaMasiva($event)"
                    ref="fileupload"
                    data-vv-scope="crear_foto"
                    data-vv-name="fotografías"
                    v-validate="'required'"
                  >
                    <template v-slot:selection="{ index, text }">
                      <v-chip
                        v-if="index < 2"
                        color="deep-white accent-4"
                        dark
                        label
                        small
                      >
                        {{ text }}
                      </v-chip>

                      <span
                        v-else-if="index === 2"
                        class="overline grey--text text--darken-3 mx-2"
                      >
                        +{{ masiva_image.length - 2 }} Foto(s)
                      </span>
                    </template>
                  </v-file-input>
                  <FormError
                    :attribute_name="'crear_foto.fotografías'"
                    :errors_form="errors"
                  ></FormError>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="red darken-1" @click="form_foto.dialog = false">
              Cancelar
            </v-btn>
            <v-btn color="blue darken-1" @click="agregar_foto('crear_foto')">
              Guardar
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-col>
  </v-row>
</template>

<script>
import FormError from '../../shared/FormError.vue'
export default {
  name: 'HHabitacion',
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
          text: '',
          align: 'start',
          value: 'fotografia',
          sortable: false,
        },
        {
          text: 'Descripción',
          align: 'start',
          value: 'descripcion',
        },
        {
          text: 'Estado',
          align: 'start',
          value: 'estado.nombre',
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
      editedIndex: false,

      h_tipos_camas: [],
      h_estados: [],
      form: {
        id: 0,
        foto: null,
        descripcion: null,

        nombre: null,
        precio_desayuno: 0,
        precio_habitacion: 0,
        incluye_desayuno: false,

        h_tipos_camas_id: null,
        h_estados_id: null,
        imagen: null,
      },

      form_precio: {
        id: null,
        nombre: null,
        precio_desayuno: 0,
        precio_habitacion: 0,
        incluye_desayuno: false,
        h_tipos_camas_id: null,
        dialog: false,
        h_habitaciones_id: null,
      },

      masiva_image: [],
      form_foto: {
        id: null,
        fotografias: [],
        dialog: false,
        h_habitaciones_id: null,
      },

      accept: ['image/png', 'image/jpeg', 'image/jpg'],
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
    colorCardH(estado) {
      let color = null
      switch (estado) {
        case 1:
          color = 'light-blue darken-4'
          break
        case 2:
          color = 'orange accent-4'
          break
        case 3:
          color = 'blue-grey darken-4'
          break
      }

      return color
    },

    cargarImagen(e) {
      if (typeof e !== 'undefined') {
        if (this.accept.includes(e.type.toLowerCase())) {
          const reader = new FileReader()
          reader.readAsDataURL(e)
          reader.onload = () => (this.form.foto = reader.result)
        } else {
          this.form.imagen = null
          this.$swal({
            title: 'Fotografía',
            text:
              'La imagén de la habitación debe tener formato PNG, JPG o JPEG',
            type: 'warning',
            showCancelButton: false,
          })
        }
      } else {
        this.form.foto = null
      }
    },

    //Carga masiva de fotografias
    cargaMasiva(e) {
      this.form_foto.fotografias = []
      if (typeof e !== 'undefined') {
        e.forEach((file) => {
          if (this.accept.includes(file.type.toLowerCase())) {
            const reader = new FileReader()
            reader.readAsDataURL(file)
            reader.onload = () =>
              this.form_foto.fotografias.push({ foto: reader.result })
          } else {
            this.form_foto.fotografias = []
            this.$swal({
              title: 'Fotografía',
              text:
                'La imagén de la habitación debe tener formato PNG, JPG o JPEG',
              type: 'warning',
              showCancelButton: false,
            })
          }
        })
      }
    },

    limpiar() {
      this.form.id = 0
      this.form.descripcion = null
      this.form.foto = null
      this.form.h_tipos_camas_id = null
      this.h_estados_id = null
      this.form.precio = null
      this.editedIndex = false

      //PRECIO
      this.form_precio.id = null
      this.form_precio.nombre = null
      this.form_precio.precio_desayuno = 0
      this.form_precio.precio_habitacion = 0
      this.form_precio.incluye_desayuno = false
      this.form_precio.h_tipos_camas_id = null
      this.form_precio.h_habitaciones_id = null
      this.form_precio.dialog = false

      //FOTOGRAFIAS
      this.masiva_image = []
      this.form_foto.id = null
      this.form_foto.fotografias = []
      this.form_foto.h_habitaciones_id = null
      this.form_foto.dialog = false

      this.$validator.reset()
      this.$validator.reset()
    },

    getSelects() {
      this.$store.state.services.selectController
        .tipo_cama()
        .then((r) => {
          this.h_tipos_camas = r.data.data
        })
        .catch((r) => {})

      this.$store.state.services.selectController
        .estado_habitacion()
        .then((r) => {
          this.h_estados = r.data.data
        })
        .catch((r) => {})
    },

    initialize() {
      this.loading = true

      this.$store.state.services.HabitacionService.getAll()
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

    mapear(item) {
      this.form.id = item.id
      this.form.descripcion = item.descripcion
      this.form.h_estados_id = item.estado

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
          this.$store.state.services.HabitacionService.delete(data)
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
          this.$store.state.services.HabitacionService.store(data)
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
          this.$store.state.services.HabitacionService.update(data)
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

    //PRECIOS
    modal_precio(item) {
      this.loading = true
      this.form_precio.id = item.id
      this.form_precio.nombre = null
      this.form_precio.precio_desayuno = 0
      this.form_precio.precio_habitacion = 0
      this.form_precio.incluye_desayuno = false
      this.form_precio.h_tipos_camas_id = null
      this.form_precio.h_habitaciones_id = item
      this.form_precio.dialog = true
      this.loading = false
    },

    agregar_precio(scope) {
      this.$validator.validateAll(scope).then((result) => {
        if (result) {
          this.$swal({
            title: `Agregar precio a la habitación #${this.form_precio.h_habitaciones_id.numero}`,
            text: '¿Está seguro de realizar esta acción?',
            type: 'warning',
            showCancelButton: true,
          }).then((result) => {
            if (result.value) {
              this.loading = true
              this.$store.state.services.HabitacionPrecioService.update(
                this.form_precio,
              )
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
            }
          })
        }
      })
    },

    quitar_precio(data) {
      this.$swal({
        title: data.activo ? 'Desactivar' : 'Activar',
        text: '¿Está seguro de realizar esta acción?',
        type: data.activo ? 'error' : 'success',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.HabitacionPrecioService.delete(data)
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
        }
      })
    },

    //FOTOGRAFIAS
    modal_foto(item) {
      this.loading = true
      this.masiva_image = []
      this.form_foto.id = item.id
      this.form_foto.fotografias = []
      this.form_foto.h_habitaciones_id = item
      this.form_foto.dialog = true
      this.loading = false
    },

    agregar_foto(scope) {
      this.$validator.validateAll(scope).then((result) => {
        if (result) {
          this.$swal({
            title: `Agregar fotografías a la habitación #${this.form_foto.h_habitaciones_id.numero}`,
            text: '¿Está seguro de realizar esta acción?',
            type: 'warning',
            showCancelButton: true,
          }).then((result) => {
            if (result.value) {
              this.loading = true
              this.$store.state.services.HabitacionFotoService.update(
                this.form_foto,
              )
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
            }
          })
        }
      })
    },

    opciones_imagen(imagen, index) {
      this.$swal({
        title: 'Opciones',
        text: '¿Está seguro de realizar esta acción?',
        type: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Seleccionar como principal',
        cancelButtonText: index === 0 ? 'Cancelar' : 'Eliminar',
      })
        .then((result) => {
          if (result.value) {
            this.imagen_principal(imagen)
          } else if (result.dismiss === this.$swal.DismissReason.cancel) {
            index === 0 ? null : this.quitar_foto(imagen)
          }
        })
        .catch((e) => {
          this.errorResponse(e)
        })
        .finally(() => {
          this.loading = false
        })
    },

    imagen_principal(data) {
      this.$swal({
        title: 'Imagen Principal',
        text: '¿Está seguro de realizar esta acción?',
        type: 'warning',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.HabitacionFotoService.get(data.id)
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
        }
      })
    },

    quitar_foto(data) {
      this.$swal({
        title: 'Eliminar',
        text: '¿Está seguro de realizar esta acción?',
        type: 'error',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.HabitacionFotoService.delete(data)
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
        }
      })
    },
  },
}
</script>
