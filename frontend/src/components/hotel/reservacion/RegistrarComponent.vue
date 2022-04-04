<template>
  <v-row>
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-col cols="12" md="12">
      <v-card color="primary lighten-2">
        <v-overlay :value="loading">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-card-title>
          <span class="headline">
            <v-icon color="black" x-large>rate_review</v-icon>
            Registrar reservación por rango de fecha
          </span>
          <v-spacer></v-spacer>
          <v-btn color="white" small @click="limpiar">
            <v-icon color="primary lighten-2">sync</v-icon>
          </v-btn>
        </v-card-title>

        <v-card-text>
          <v-container class="text-center">
            <HotelDatePicker
              :i18n="i18n"
              format="DD/MM/YYYY"
              @period-selected="formato_fecha"
              :disableCheckoutOnCheckin="bloquear"
            ></HotelDatePicker>
          </v-container>
        </v-card-text>
      </v-card>
    </v-col>

    <v-col cols="12" md="12">
      <v-card color="success lighten-2">
        <v-overlay :value="loading">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-card-title>
          <span class="headline">
            <v-icon color="black" x-large>rate_review</v-icon>
            Registrar reservación por hora
          </span>
          <v-spacer></v-spacer>
          <v-btn color="white" small @click="limpiar">
            <v-icon color="success lighten-2">sync</v-icon>
          </v-btn>
        </v-card-title>

        <v-card-text>
          <v-container class="text-center">
            <label for="horas">Horas</label>
            <br />
            <vue-number-input
              v-model="form.horas"
              size="mid"
              :min="1"
              :max="4"
              inline
              center
              controls
              placeholder="horas"
              rounded
              :disable="bloquear"
            ></vue-number-input>
          </v-container>
          <v-container class="text-center">
            <v-time-picker
              format="24hr"
              :allowed-hours="[
                6,
                7,
                8,
                9,
                10,
                11,
                12,
                13,
                14,
                15,
                16,
                17,
                18,
                19,
                20,
                21,
                22,
                23,
              ]"
              :allowed-minutes="[0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55]"
              dark
              flat
              elevation="20"
              full-width
              @input="formato_tiempo"
              :disabled="bloquear"
            ></v-time-picker>
          </v-container>
        </v-card-text>
      </v-card>
    </v-col>

    <v-dialog
      v-model="dialog"
      fullscreen
      persistent
      transition="dialog-bottom-transition"
      hide-overlay
    >
      <v-overlay :value="loading">
        <v-progress-circular indeterminate size="64"></v-progress-circular>
      </v-overlay>

      <v-card color="blue darken-4" dark>
        <v-card-text>
          <v-container>
            <v-row>
              <!-- DATOS PERSONALES -->
              <v-col cols="12" md="3">
                <v-row>
                  <v-col cols="12" md="12"></v-col>
                  <v-col cols="12" md="12">
                    <span class="headline">
                      <v-icon color="white" x-large>rate_review</v-icon>
                      {{
                        `Registrar por ${
                          form.consulta_por_hora ? 'hora' : 'noche'
                        }`
                      }}
                    </span>
                  </v-col>
                  <v-col cols="12" md="12">
                    Datos personales del cliente
                  </v-col>
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
                  <v-col cols="12" md="6" v-show="mostrar_nit === true">
                    <strong>Resultados de la busqueda</strong>
                    <ul
                      v-for="(item, index) in filteredList"
                      v-bind:key="index"
                    >
                      <li>
                        <v-chip
                          class="ma-2"
                          color="primary"
                          text-color="white"
                          small
                          @click="seleccionar_cliente(item)"
                        >
                          <v-avatar left>
                            <v-icon dense color="cyan lighten-2">
                              contacts
                            </v-icon>
                          </v-avatar>
                          {{ item.nit }}
                        </v-chip>
                      </li>
                    </ul>
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
                  <v-col cols="12" md="12">
                    <v-row>
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
                  </v-col>
                </v-row>
              </v-col>
              <!-- DATOS DE LA RESERVACION DETALLE -->
              <v-col cols="12" md="9">
                <v-card
                  elevation="24"
                  :loading="loading"
                  class="mx-auto"
                  color="light-green darken-2"
                >
                  <v-card-title>
                    <p class="text-h5 text--white">
                      {{ `Reservación del cliente ${form.nombre}` }}
                    </p>
                    <v-spacer></v-spacer>
                    <v-autocomplete
                      filled-inverted
                      suffix
                      dense
                      dark
                      rounded
                      prepend-inner-icon="view_carousel"
                      v-model="search_h"
                      :items="habitaciones_disponibles"
                      label="Seleccionar número de la habitación para bsucar"
                      :clearable="true"
                      :deletable-chips="true"
                      item-text="numero"
                      item-value="id"
                      return-object
                      @input="filtrar_habitaciones"
                      multiple
                    >
                      <template v-slot:no-data>
                        <v-row align="center" justify="space-around">
                          No hay habitaciones disponibles
                        </v-row>
                      </template>
                    </v-autocomplete>
                  </v-card-title>
                  <v-card-text>
                    <v-container>
                      <v-expansion-panels tile dark flat>
                        <v-expansion-panel>
                          <v-expansion-panel-header
                            disable-icon-rotate
                            color="primary"
                          >
                            Habitaciones
                            <template v-slot:actions>
                              <v-icon color="white">
                                $expand
                              </v-icon>
                            </template>
                          </v-expansion-panel-header>
                          <v-expansion-panel-content>
                            <br />
                            <br />
                            <v-row>
                              <v-col
                                cols="12"
                                md="4"
                                sm="6"
                                v-for="(item, index) in filtro_aplicado"
                                v-bind:key="`Habitacion#${index}`"
                              >
                                <v-card class="mx-auto elevation-22" dark>
                                  <v-img
                                    contain
                                    height="25%"
                                    :src="item.foto"
                                    :alt="`Habitación #${item.numero}`"
                                  ></v-img>
                                  <v-card-title>
                                    {{ `Habitación #${item.numero}` }}
                                  </v-card-title>
                                  <v-card-subtitle>
                                    {{
                                      `Huespedes recomendados ${item.huespedes}`
                                    }}
                                  </v-card-subtitle>
                                  <v-card-actions>
                                    <v-tooltip
                                      bottom
                                      color="light-green darken-2"
                                    >
                                      <template
                                        v-slot:activator="{ on, attrs }"
                                      >
                                        <v-btn
                                          class="ma-2"
                                          text
                                          icon
                                          color="light-green darken-2"
                                          dark
                                          v-bind="attrs"
                                          v-on="on"
                                          small
                                          @click="ver_fotos(item)"
                                        >
                                          <v-icon>perm_media</v-icon>
                                        </v-btn>
                                      </template>
                                      <span
                                        v-text="
                                          `Fotografías de la habitación # ${item.numero}`
                                        "
                                      ></span>
                                    </v-tooltip>
                                    <v-spacer></v-spacer>
                                    <v-tooltip
                                      bottom
                                      color="light-blue darken-2"
                                    >
                                      <template
                                        v-slot:activator="{ on, attrs }"
                                      >
                                        <v-btn
                                          class="ma-2"
                                          text
                                          icon
                                          color="light-blue darken-2"
                                          dark
                                          v-bind="attrs"
                                          v-on="on"
                                          small
                                          @click="
                                            item.ver_precio = !item.ver_precio
                                          "
                                        >
                                          <v-icon>
                                            {{
                                              item.ver_precio
                                                ? 'visibility_off'
                                                : 'visibility'
                                            }}
                                          </v-icon>
                                        </v-btn>
                                      </template>
                                      <span
                                        v-text="
                                          `Ver precios de la habitación # ${item.numero}`
                                        "
                                      ></span>
                                    </v-tooltip>
                                    <v-spacer></v-spacer>
                                    <v-btn icon @click="item.show = !item.show">
                                      <v-icon>
                                        {{
                                          item.show
                                            ? 'mdi-chevron-up'
                                            : 'mdi-chevron-down'
                                        }}
                                      </v-icon>
                                    </v-btn>
                                  </v-card-actions>

                                  <v-expand-transition>
                                    <div v-show="item.show">
                                      <v-divider></v-divider>
                                      <v-card-text>
                                        <small>{{ item.descripcion }}</small>
                                      </v-card-text>
                                    </div>
                                  </v-expand-transition>

                                  <v-expand-transition>
                                    <div v-show="item.ver_precio">
                                      <v-divider></v-divider>
                                      <v-card-text>
                                        <template
                                          v-for="(precio,
                                          p_index) in item.precios"
                                        >
                                          <p
                                            class="text-caption light-green--text"
                                            v-bind:key="`Precio${item.numero} #${p_index}`"
                                          >
                                            <v-btn
                                              :disabled="
                                                precio.seleccionado === 0
                                                  ? false
                                                  : true
                                              "
                                              color="light-green accent-3"
                                              icon
                                              x-small
                                              @click="
                                                agregar_detalle(precio, item)
                                              "
                                              :loading="loading"
                                            >
                                              <v-icon>check</v-icon>
                                            </v-btn>
                                            {{
                                              `${precio.nombre_completo} - Q${
                                                precio.precio
                                              } ${
                                                precio.incluye_desayuno == 1
                                                  ? ' | Incluye desayuno'
                                                  : ''
                                              }`
                                            }}
                                          </p>
                                        </template>
                                      </v-card-text>
                                    </div>
                                  </v-expand-transition>
                                </v-card>
                              </v-col>
                            </v-row>
                          </v-expansion-panel-content>
                        </v-expansion-panel>
                      </v-expansion-panels>
                    </v-container>
                  </v-card-text>

                  <v-divider dark></v-divider>
                  <v-card-text>
                    <v-simple-table dark dense>
                      <template v-slot:default>
                        <thead>
                          <tr>
                            <th class="text-center">
                              {{ form.consulta_por_hora ? 'Horas' : 'Noches' }}
                            </th>
                            <th class="text-center">
                              Huespedes
                            </th>
                            <th class="text-center">
                              Descripción
                            </th>
                            <th class="text-center">
                              Precio
                            </th>
                            <th class="text-center">
                              Sub Total
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr
                            v-for="(detalle,
                            index_detalle) in form.h_reservaciones_detalles"
                            v-bind:key="index_detalle"
                          >
                            <td class="text-center">
                              <br />
                              {{
                                form.consulta_por_hora ? form.horas : form.dias
                              }}
                            </td>
                            <td class="text-center">
                              <br />
                              <vue-number-input
                                v-model="detalle.huespedes"
                                size="small"
                                :min="1"
                                :max="detalle.h_reservaciones_detalles.cantidad"
                                inline
                                center
                                controls
                                placeholder="huespedes"
                                rounded
                              ></vue-number-input>
                            </td>
                            <td class="text-left">
                              <br />
                              {{
                                detalle.h_reservaciones_detalles.nombre_completo
                              }}
                            </td>
                            <td class="text-center">
                              <br />
                              <v-text-field
                                filled-inverted
                                suffix
                                dense
                                dark
                                prefix="Q"
                                counter
                                v-model="detalle.precio"
                                type="text"
                                label="precio"
                                data-vv-scope="crear"
                                :data-vv-name="`precio de la fila ${index_detalle}`"
                                v-validate="
                                  `required|integer|min_value:${parseInt(
                                    detalle.h_reservaciones_detalles.precio,
                                  )}`
                                "
                                :hint="`Precio mínimo ${formato_moneda(
                                  1,
                                  detalle.h_reservaciones_detalles.precio,
                                  0,
                                )}`"
                                persistent-hint
                              ></v-text-field>
                              <FormError
                                :attribute_name="`crear.precio de la fila ${index_detalle}`"
                                :errors_form="errors"
                              ></FormError>
                              <br />
                            </td>
                            <td class="text-right" @click="quitar_detalle">
                              <p class="text-h5 text--white">
                                {{
                                  form.consulta_por_hora
                                    ? formato_moneda(1, detalle.precio, 0)
                                    : formato_moneda(
                                        form.dias,
                                        detalle.precio,
                                        0,
                                      )
                                }}
                              </p>
                            </td>
                          </tr>
                        </tbody>
                        <tfoot>
                          <tr>
                            <td class="text-right" colspan="3">
                              <p class="text-h4 text--white">Total</p>
                            </td>
                            <td class="text-right" colspan="2">
                              <p class="text-h3 text--white">
                                {{ total }}
                              </p>
                            </td>
                          </tr>
                        </tfoot>
                      </template>
                    </v-simple-table>
                  </v-card-text>

                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red darken-1" @click="limpiar">
                      Cancelar
                    </v-btn>
                    <v-btn
                      color="green darken-1"
                      @click="validar_formulario('crear')"
                    >
                      Guardar
                    </v-btn>
                  </v-card-actions>
                </v-card>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="dialog_pickture"
      persistent
      max-width="100%"
      hide-overlay
      fullscreen
    >
      <v-card v-if="foto">
        <v-card-title class="text-h5">
          {{ `Fotografías de la habitación #${foto.numero}` }}
          <v-spacer></v-spacer>
          <v-btn color="red" text icon @click="cerrar_foto">
            <v-icon>close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-carousel
              cycle
              show-arrows-on-hover
              dark
              touchless
              v-if="foto"
              vertical
              hide-delimiters
              height="50%"
            >
              <v-carousel-item
                v-for="(ver, i) in foto.fotografias"
                :key="`Foto#${i}`"
                :src="ver"
              ></v-carousel-item>
            </v-carousel>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>
import FormError from '../../shared/FormError.vue'
import moment from 'moment'
import HotelDatePicker from 'vue-hotel-datepicker'
import 'vue-hotel-datepicker/dist/vueHotelDatepicker.css'

export default {
  name: 'HRegistrar',
  components: {
    FormError,
    moment,
    HotelDatePicker,
  },
  data() {
    return {
      loading: false,
      dialog: false,

      menu_hora: false,
      bloquear: false,

      clientes: [],
      municipios: [],
      habitaciones_disponibles: [],
      filtro_aplicado: [],
      form: {
        id: 0,
        nit: null,
        nombre: null,
        telefonos: null,
        emails: null,
        direcciones: null,
        municipios_id: null,

        horas: null,
        inicio: null,
        fin: null,
        dias: 0,

        h_reservaciones_detalles: [],

        consulta_por_hora: false,
      },

      //calendario
      i18n: {
        night: 'Noche',
        nights: 'Noches',
        week: 'Semana',
        weeks: 'Semanas',
        'day-names': ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
        'check-in': 'Check-in',
        'check-out': 'Check-out',
        'month-names': [
          'Enero',
          'Febrero',
          'Marzo',
          'Abril',
          'Mayo',
          'Junio',
          'Julio',
          'Agosto',
          'Septiembre',
          'Octubre',
          'Noviembre',
          'Diciembre',
        ],
        tooltip: {
          halfDayCheckIn: 'Entrada disponible',
          halfDayCheckOut: 'Pago disponible',
          saturdayToSaturday: 'Solo sábado a sábado',
          sundayToSunday: 'Solo de domingo a domingo',
          minimumRequiredPeriod: '%{minNightInPeriod} %{night} mínimo.',
          maximumRequiredPeriod: '%{maxNightInPeriod} %{night} máximo.',
        },

        fecha_inicio_original: null,
        fecha_fin_original: null,

        dialog_pickture: false,
        foto: null,

        mostrar_nit: false,

        search_h: null,
      },
    }
  },

  computed: {
    filteredList() {
      if (this.form.nit) {
        let encontrados = this.clientes.filter((element) => {
          return element.nit.toUpperCase().includes(this.form.nit.toUpperCase())
        })
        this.mostrar_nit = true
        return encontrados.slice(encontrados.length - 5)
      } else {
        this.mostrar_nit = false
        return []
      }
    },

    total() {
      let total = 0
      this.form.h_reservaciones_detalles.forEach((element) => {
        let cantidad_no_null = this.form.consulta_por_hora ? 1 : this.form.dias
        let precio_no_null = element.precio
        let monto = parseInt(cantidad_no_null) * parseFloat(precio_no_null)

        total += monto
      })
      return this.formato_moneda(1, total, 0)
    },
  },

  created() {
    this.limpiar()
  },

  methods: {
    filtrar_habitaciones(item) {
      if (item.length > 0) {
        this.filtro_aplicado = this.habitaciones_disponibles.filter(
          (element) => {
            return item.includes(element)
          },
        )
      } else {
        return (this.filtro_aplicado = this.habitaciones_disponibles)
      }
    },

    //formato de fecha
    formato_fecha(event, inicio, fin) {
      this.fecha_inicio_original = inicio
      this.fecha_fin_original = fin

      this.form.inicio = null
      this.form.fin = null
      this.form.dias = 0
      this.form.horas = 1
      this.form.consulta_por_hora = false

      if (!inicio && !fin) return null

      this.form.inicio = moment(inicio).format('YYYY-MM-DD')
      this.form.fin = moment(fin).format('YYYY-MM-DD')
      this.form.dias = Math.floor((fin - inicio) / 86400000)

      this.$swal({
        title: 'Consultar',
        text: '¿Está seguro de realizar esta acción?',
        type: 'info',
        showCancelButton: true,
      })
        .then((result) => {
          if (result.value) {
            this.loading = true
            this.form.horas = null
            this.serviceHabitaciones(this.form)
          }
        })
        .catch((e) => {
          this.errorResponse(e)
        })
    },

    //formato de la hora
    formato_tiempo(hora) {
      this.fecha_inicio_original = null
      this.fecha_fin_original = null

      this.form.inicio = null
      this.form.fin = null
      this.form.dias = 0
      this.form.consulta_por_hora = true

      if (!hora) return null

      this.form.inicio = `${hora}:00`

      this.$swal({
        title: 'Consultar',
        text: '¿Está seguro de realizar esta acción?',
        type: 'info',
        showCancelButton: true,
      })
        .then((result) => {
          if (result.value) {
            this.loading = true
            this.serviceHabitaciones(this.form)
          }
        })
        .catch((e) => {
          this.errorResponse(e)
        })
    },

    serviceHabitaciones(data) {
      this.$store.state.services.selectController
        .buscar_habitaciones(data)
        .then((r) => {
          this.habitaciones_disponibles = r.data[0]
          this.filtro_aplicado = this.habitaciones_disponibles
          this.dialog = true
          this.bloquear = true

          this.dialog_pickture = false
          this.foto = null
        })
        .catch((e) => {
          this.errorResponse(e)
        })
        .finally(() => {
          this.loading = false
        })
    },

    getSelects() {
      this.$store.state.services.selectController
        .cliente()
        .then((r) => {
          this.clientes = r.data.data
        })
        .catch((e) => {
          this.errorResponse(e)
        })

      this.$store.state.services.selectController
        .municipio()
        .then((r) => {
          this.municipios = r.data.data
        })
        .catch((e) => {
          this.errorResponse(e)
        })
    },

    limpiar() {
      this.loading = true
      this.getSelects()

      this.dialog = false
      this.menu_hora = false
      this.bloquear = false
      this.habitaciones_disponibles = []
      this.form.id = 0
      this.form.nit = null
      this.form.nombre = null
      this.form.telefonos = null
      this.form.emails = null
      this.form.direcciones = null
      this.form.municipios_id = null

      this.form.horas = 1
      this.form.inicio = null
      this.form.fin = null
      this.form.dias = null

      this.form.h_reservaciones_detalles = []

      this.form.consulta_por_hora = false

      this.form.fecha_inicio_original = null
      this.form.fecha_fin_original = null

      this.dialog_pickture = false
      this.foto = null

      this.mostrar_nit = false

      this.search_h = null

      this.$validator.reset()
      this.$validator.reset()
      this.loading = false
    },

    seleccionar_cliente(item) {
      this.form.nit = item.nit
      this.form.nombre = item.nombre
      this.form.telefonos = item.telefonos
      this.form.emails = item.emails
      this.form.direcciones = item.direcciones
      this.form.municipios_id = item.municipio
    },

    ver_fotos(item) {
      this.foto = item
      this.dialog_pickture = true
      this.dialog = false
    },

    cerrar_foto() {
      this.dialog_pickture = false
      this.foto = null
      this.dialog = true
    },

    agregar_detalle(precio, item) {
      this.loading = true
      let object = new Object()
      object.h_reservaciones_detalles = precio
      object.huespedes = 1
      object.precio = 0
      this.form.h_reservaciones_detalles.push(object)
      precio.seleccionado = precio.mostrar === 1 ? 0 : 1
      this.$toastr.info(
        `Se agrego la habitación #${item.numero} a la reservación, con la descripción ${precio.cantidad} | ${precio.nombre_completo} - Q${precio.precio}`,
        'Reservado',
      )
      this.loading = false
    },

    quitar_detalle(index) {
      this.$swal({
        title: 'Eliminar',
        text: '¿Está seguro de realizar esta acción?',
        type: 'error',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.form.h_reservaciones_detalles.splice(
            this.form.h_reservaciones_detalles.indexOf(index),
            1,
          )
          this.loading = false
        }
      })
    },

    validar_formulario(scope) {
      if (this.form.h_reservaciones_detalles.length === 0) {
        this.$toastr.info(
          'Es necesario que agregue habitaciones en el detalle de la reservación.',
          'Información incorrecta',
        )
        return
      }

      this.$validator.validateAll(scope).then((result) => {
        if (result) {
          this.store(this.form)
        } else {
          this.$toastr.warning(
            'Por favor verifique la información.',
            'Formulario',
          )
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
          this.$store.state.services.ReservacionService.store(data)
            .then((r) => {
              this.$toastr.success(r.data.mensaje, 'Mensaje')
              this.notificador_audible(this.$store.state.audio.agregar)
              this.limpiar()

              window.open(
                r.data.comprobante,
                'popup',
                'width=' +
                  1000 +
                  ', height=' +
                  800 +
                  ', left=' +
                  500 / 2 +
                  ', top=' +
                  500 / 2 +
                  '',
              )
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
