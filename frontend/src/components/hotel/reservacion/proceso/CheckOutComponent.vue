<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-col cols="12" md="12">
      <v-toolbar dark flat :color="colorTolbar">
        <v-toolbar-title>Check Out</v-toolbar-title>
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
          <v-icon :color="colorTolbar">sync</v-icon>
        </v-btn>
      </v-toolbar>
      <v-row>
        <v-col
          cols="12"
          md="6"
          v-for="(reservacion, index) in filteredItems"
          v-bind:key="index"
        >
          <v-card tile :loading="loading">
            <v-card-text>
              <br />
              <div class="invoice-box">
                <table cellpadding="0" cellspacing="0">
                  <tr class="top">
                    <td colspan="2" class="text-center">
                      <img
                        :src="logotipo"
                        style="width: 100%; max-width: 300px;"
                      />
                    </td>
                  </tr>
                  <tr class="top">
                    <td colspan="2" class="text-center">
                      {{ `Reservación ${reservacion.codigo}` }}
                      <br />
                      NIT
                      <br />
                      Dirección de la Empresa
                    </td>
                  </tr>
                  <tr class="top">
                    <td colspan="2" class="text-center"><br /></td>
                  </tr>

                  <tr class="information">
                    <td colspan="2">
                      <table>
                        <tr>
                          <td>
                            <v-text-field
                              filled-inverted
                              suffix
                              dense
                              prepend-inner-icon="fiber_new"
                              counter
                              v-model="reservacion.nit"
                              type="text"
                              label="NIT"
                              :data-vv-scope="reservacion.codigo"
                              :data-vv-name="`nit del cliente para la reservación ${reservacion.codigo}`"
                              v-validate="'required|max:15'"
                              hint="El número de nit de la persona"
                              persistent-hint
                            ></v-text-field>
                            <FormError
                              :attribute_name="`${reservacion.codigo}.nit del cliente para la reservación ${reservacion.codigo}`"
                              :errors_form="errors"
                            ></FormError>
                          </td>

                          <td>
                            <v-text-field
                              filled-inverted
                              suffix
                              dense
                              prepend-inner-icon="fiber_new"
                              counter
                              v-model="reservacion.nombre"
                              type="text"
                              label="nombre"
                              :data-vv-scope="reservacion.codigo"
                              :data-vv-name="`nombre del cliente para la reservación ${reservacion.codigo}`"
                              v-validate="'required|max:100'"
                              hint="El nombre de la persona"
                              persistent-hint
                            ></v-text-field>
                            <FormError
                              :attribute_name="`${reservacion.codigo}.nombre del cliente para la reservación ${reservacion.codigo}`"
                              :errors_form="errors"
                            ></FormError>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2">
                            <v-text-field
                              filled-inverted
                              suffix
                              dense
                              prepend-inner-icon="fiber_new"
                              counter
                              v-model="reservacion.direccion"
                              type="text"
                              label="dirección"
                              :data-vv-scope="reservacion.codigo"
                              :data-vv-name="`dirección del cliente para la reservación ${reservacion.codigo}`"
                              v-validate="'required|max:150'"
                              hint="La dirección de la persona"
                              persistent-hint
                            ></v-text-field>
                            <FormError
                              :attribute_name="`${reservacion.codigo}.dirección del cliente para la reservación ${reservacion.codigo}`"
                              :errors_form="errors"
                            ></FormError>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>

                  <tr class="heading">
                    <td>Método de Pago</td>
                    <td>Comprobante</td>
                  </tr>

                  <tr class="details">
                    <td>
                      <v-radio-group
                        name="metodo-pago"
                        v-model="reservacion.tipos_pagos_id"
                        row
                        :data-vv-scope="reservacion.codigo"
                        :data-vv-name="`método de pago para la reservación ${reservacion.codigo}`"
                        v-validate="'required'"
                        hint="Método de pago que utilizará"
                        persistent-hint
                      >
                        <v-radio
                          v-for="item in tipos_pago"
                          :key="item.id"
                          :label="item.nombre"
                          :value="item.id"
                        ></v-radio>
                      </v-radio-group>
                      <FormError
                        :attribute_name="`${reservacion.codigo}.método de pago para la reservación ${reservacion.codigo}`"
                        :errors_form="errors"
                      ></FormError>
                    </td>
                    <td v-if="reservacion.tipos_pagos_id === 2">
                      <v-text-field
                        filled-inverted
                        suffix
                        dense
                        prepend-inner-icon="fiber_new"
                        counter
                        v-model="reservacion.vaucher_pago"
                        type="text"
                        label="vaucher de pago"
                        :data-vv-scope="reservacion.codigo"
                        :data-vv-name="`vaucher de pago para la reservación ${reservacion.codigo}`"
                        v-validate="'required|integer|max:12'"
                        hint="Número del vaucher de pago generado"
                        persistent-hint
                      ></v-text-field>
                      <FormError
                        :attribute_name="`${reservacion.codigo}.vaucher de pago para la reservación ${reservacion.codigo}`"
                        :errors_form="errors"
                      ></FormError>
                    </td>
                  </tr>

                  <tr class="heading">
                    <td>Descripción</td>
                    <td>Precio</td>
                  </tr>

                  <tr
                    class="item"
                    v-for="(habitacion, index) in reservacion.detalle"
                    v-bind:key="index"
                  >
                    <td>{{ habitacion.descripcion }}</td>
                    <td>
                      {{ formato_moneda(1, habitacion.sub_total, 0) }}
                    </td>
                  </tr>

                  <tr class="total text-right">
                    <td colspan="2">
                      {{
                        `Sub Total: ${formato_moneda(
                          1,
                          reservacion.sub_total,
                          0,
                        )}`
                      }}
                    </td>
                  </tr>

                  <tr class="total text-right">
                    <td></td>
                    <td>
                      <br />
                      <v-text-field
                        filled-inverted
                        prefix="Q"
                        dense
                        counter
                        v-model="reservacion.descuento"
                        type="text"
                        label="descuento"
                        :data-vv-scope="reservacion.codigo"
                        :data-vv-name="`descuento para la reservación ${reservacion.codigo}`"
                        v-validate="'integer'"
                        hint="Descuento aplicado a la reservación"
                        persistent-hint
                      ></v-text-field>
                      <FormError
                        :attribute_name="`${reservacion.codigo}.descuento para la reservación ${reservacion.codigo}`"
                        :errors_form="errors"
                      ></FormError>
                    </td>
                  </tr>

                  <tr class="total text-right">
                    <td colspan="2">
                      {{
                        `Total: ${formato_moneda(
                          1,
                          reservacion.sub_total,
                          reservacion.descuento,
                        )}`
                      }}
                    </td>
                  </tr>
                </table>
              </div>
            </v-card-text>
            <v-card-actions>
              <v-row justify="center" align="center">
                <v-btn-toggle dark rounded>
                  <v-tooltip bottom color="blue lighten-2">
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        text
                        icon
                        color="blue lighten-2"
                        @click="store(reservacion.codigo, reservacion)"
                        dark
                        v-bind="attrs"
                        v-on="on"
                        :loading="loading"
                        :disabled="loading"
                      >
                        <v-icon>local_atm</v-icon>
                      </v-btn>
                    </template>
                    <span
                      v-text="`Pagar la reservación ${reservacion.codigo}`"
                    ></span>
                  </v-tooltip>
                  <v-tooltip bottom color="orange lighten-2">
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        text
                        icon
                        color="orange lighten-2"
                        dark
                        v-bind="attrs"
                        v-on="on"
                        :loading="loading"
                        :disabled="loading"
                        @click="ver_informacion(reservacion)"
                      >
                        <v-icon>info</v-icon>
                      </v-btn>
                    </template>
                    <span
                      v-text="
                        `Ver el información de la reservación ${reservacion.codigo}`
                      "
                    ></span>
                  </v-tooltip>
                  <v-tooltip bottom color="red lighten-2">
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        text
                        icon
                        color="red lighten-2"
                        @click="destroy(reservacion)"
                        dark
                        v-bind="attrs"
                        v-on="on"
                        :loading="loading"
                        :disabled="loading"
                      >
                        <v-icon>report_problem</v-icon>
                      </v-btn>
                    </template>
                    <span
                      v-text="
                        `Eliminar check out para la reservación ${reservacion.codigo}`
                      "
                    ></span>
                  </v-tooltip>
                </v-btn-toggle>
              </v-row>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
    </v-col>

    <v-col cols="12" md="12">
      <v-dialog v-model="dialog" color="primary" width="80%">
        <v-card v-if="item_reservacion">
          <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
          </v-overlay>
          <v-card-title>
            <span class="headline">
              {{ `Reservación ${item_reservacion.codigo}` }}
            </span>
            <v-spacer></v-spacer>
            <v-btn @click="dialog = false" color="red" small>
              <v-icon>close</v-icon>
            </v-btn>
          </v-card-title>

          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12" md="6">
                  <div class="title text-center">Check In</div>
                  <template
                    v-for="(item, index) in item_reservacion.check_in_list"
                  >
                    <div class="subtitle-1" v-bind:key="`div${index}`">
                      {{ item.habitacion }}
                    </div>
                    <ul v-bind:key="`ul${index}`">
                      <li
                        v-for="(iitem, index) in item.lista"
                        v-bind:key="`ul${index}li${index}`"
                      >
                        {{ `${iitem.cantidad} | ${iitem.producto}` }}
                      </li>
                    </ul>
                    <hr v-bind:key="`hr${index}`" />
                  </template>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="title text-center">Check Out</div>
                  <template
                    v-for="(item, index) in item_reservacion.check_out_list"
                  >
                    <div class="subtitle-1" v-bind:key="`div${index}`">
                      {{ item.habitacion }}
                    </div>
                    <ul v-bind:key="`ul${index}`">
                      <li
                        v-for="(iitem, index) in item.lista"
                        v-bind:key="`ul${index}li${index}`"
                      >
                        {{ `${iitem.cantidad} | ${iitem.producto}` }}
                      </li>
                    </ul>
                    <hr v-bind:key="`hr${index}`" />
                  </template>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-col>
  </v-row>
</template>

<script>
import FormError from '../../../shared/FormError.vue'
export default {
  name: 'HCheckOut',
  components: {
    FormError,
  },
  data() {
    return {
      loading: false,
      dialog: true,
      search: '',
      desserts: [],

      tipos_pago: [],

      dialog: false,
      item_reservacion: null,
    }
  },

  computed: {
    colorTolbar() {
      return 'success'
    },

    logotipo() {
      return this.$store.state.logo_ticket
    },

    filteredItems() {
      if (this.search) {
        return this.desserts.filter((element) => {
          return element.codigo
            .toUpperCase()
            .includes(this.search.toUpperCase())
        })
      } else {
        return this.desserts
      }
    },
  },
  created() {
    this.initialize()
    this.getSelects()
  },

  methods: {
    initialize() {
      this.loading = true
      this.desserts = []
      this.dialog = false
      this.item_reservacion = null
      this.$store.state.services.ReservacionService.getAll('o')
        .then((r) => {
          r.data.forEach((ele) => {
            let objeto = new Object()
            objeto.id = ele.id
            objeto.codigo = ele.codigo
            objeto.nit = ele.cliente.nit
            objeto.nombre = ele.cliente.nombre
            objeto.direccion = `${ele.cliente.municipio.full_name}, ${ele.cliente.direcciones}`
              .toLowerCase()
              .replace(', null', '')
            objeto.tipos_pagos_id = null
            objeto.vaucher_pago = null
            objeto.factura = false
            objeto.detalle = []

            ele.detalle.forEach((det) => {
              let objeto_detalle = new Object()
              objeto_detalle.descripcion = `Reservación ${ele.codigo} | ${det.descripcion}`
              objeto_detalle.sub_total = det.sub_total
              objeto.detalle.push(objeto_detalle)
            })

            objeto.sub_total = ele.sub_total
            objeto.descuento = 0
            objeto.check_in_list = ele.check_in_list
            objeto.check_out_list = ele.check_out_list

            this.desserts.push(objeto)
          })

          this.$validator.reset()
          this.$validator.reset()
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
        .tipo_pago()
        .then((r) => {
          this.tipos_pago = r.data.data
        })
        .catch((e) => {
          this.errorResponse(e)
        })
    },

    store(scope, item) {
      this.$validator.validateAll(scope).then((result) => {
        if (result) {
          this.$swal({
            title: 'Pago',
            text: '¿Está seguro de realizar esta acción?',
            type: 'success',
            showCancelButton: true,
          }).then((result) => {
            if (result.value) {
              this.loading = true
              this.$store.state.services.PagoService.store(item)
                .then((r) => {
                  this.documento_imprimir = null
                  this.dialog_print = false

                  this.$toastr.success(r.data.mensaje, 'Mensaje')
                  this.notificador_audible(this.$store.state.audio.agregar)
                  this.$swal({
                    title: 'Imprimir',
                    text: '¿Está seguro de realizar esta acción?',
                    type: 'info',
                    showCancelButton: true,
                  })
                    .then((result) => {
                      if (result.value) {
                        window.open(
                          r.data.pago,
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

                        this.initialize()
                      } else {
                        this.initialize()
                      }
                    })
                    .catch((e) => {
                      this.errorResponse(e)
                    })
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

    destroy(data) {
      this.$swal({
        title: 'Anular',
        text: '¿Está seguro de realizar esta acción?',
        type: 'error',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.CheckOutService.delete(data)
            .then((r) => {
              this.$toastr.success(r.data, 'Mensaje')
              this.notificador_audible(this.$store.state.audio.anular)
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

    ver_informacion(item) {
      this.loading = true
      this.item_reservacion = item
      this.dialog = true
      this.loading = false
    },
  },
}
</script>
