<template>
  <v-row class="justify-center">
    <v-col md="12" sm="12">
      <v-dialog persistent v-model="dialog" max-width="85%">
        <v-card>
          <v-toolbar>
            <v-toolbar-title>Registrar nuevo cliente</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click="closeCustomerForm()">
              <v-icon dark>close</v-icon>
            </v-btn>
          </v-toolbar>
          <CustomerForm v-if="dialog" />
        </v-card>
      </v-dialog>
    </v-col>
    <v-col md="12" sm="12">
      <v-card class="mx-2 my-2">
        <v-overlay :value="loading">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-toolbar>
          <v-toolbar-title>Detalle de la orden</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn icon @click="closeForm()">
            <v-icon dark>close</v-icon>
          </v-btn>
        </v-toolbar>
        <!-- Detalle -->
        <div style="background-color: #e3f2fd;">
          <v-row>
            <v-col md="7" sm="12">
              <div style="height: 80vh; overflow-y: scroll;">
                <template v-for="item in recordDetail.detalle">
                  <v-card
                    v-bind:key="item.id"
                    class="mx-2 my-2"
                    style="padding: 15px;"
                  >
                    <!--  -->
                    <div
                      class="d-flex justify-md-space-between justify-sm-center justify-xs-center"
                    >
                      <v-img
                        :src="getAbsoluteImagePath(item.img)"
                        style="border-radius: 75%; height: 64px; width: 64px;"
                        class="mx-2 py-2 mr-4"
                      ></v-img>
                      <v-card-text class="text-h6">
                        {{ item.cantidad }}&nbsp;{{ item.producto }}
                      </v-card-text>
                      <v-card-text class="text-h6 justify-end">
                        {{ setSubtotalItem(item) }}
                      </v-card-text>
                      <v-btn icon>
                        <v-icon
                          style="cursor: pointer;"
                          large
                          color="red"
                          @click="deleteMenu(item)"
                        >
                          fas fa-trash
                        </v-icon>
                      </v-btn>
                    </div>
                    <!--  -->
                  </v-card>
                </template>
              </div>
            </v-col>
            <v-col md="5" sm="12">
              <v-card class="mx-2 my-2">
                <v-container fluid>
                  <v-chip class="ma-2" :color="recordDetail.color" dark>
                    <v-icon left>
                      {{ recordDetail.icono }}
                    </v-icon>
                    {{ recordDetail.estado_orden }}
                  </v-chip>
                  <v-card-text>
                    <div
                      style="
                        font-size: 12px;
                        font-weight: bold;
                        background-color: #dbdada;
                        padding: 5px;
                        margin-bottom: 5px;
                      "
                    >
                      NIT del cliente
                    </div>
                    <v-form autocomplete="off">
                      <div class="d-flex flex-row">
                        <v-autocomplete
                          outlined
                          clearable
                          dense
                          v-validate="'required'"
                          name="cliente"
                          v-model="cliente"
                          :items="items"
                          :search-input.sync="search"
                          item-text="nit"
                          placeholder="Buscar NIT del cliente"
                          @change="setPaymentCustomer"
                          return-object
                        ></v-autocomplete>
                        <v-btn class="info" @click="newCustomer">
                          <v-icon>add</v-icon>
                        </v-btn>
                      </div>
                      <form-error
                        :attribute_name="'cliente'"
                        :errors_form="errors"
                      ></form-error>
                      <div
                        style="
                          font-size: 12px;
                          font-weight: bold;
                          background-color: #dbdada;
                          padding: 5px;
                          margin-bottom: 5px;
                        "
                      >
                        Datos del cliente
                      </div>
                      <v-card-text>
                        <strong>Cliente:</strong>
                        {{ nombreCliente }}
                      </v-card-text>
                      <v-card-text>
                        <strong>NIT:</strong>
                        {{ nit }}
                      </v-card-text>
                      <v-card-text>
                        <strong>Dirección:</strong>
                        {{ direccion }}
                      </v-card-text>

                      <div
                        style="
                          font-size: 12px;
                          font-weight: bold;
                          background-color: #dbdada;
                          padding: 5px;
                        "
                      >
                        Método de pago
                      </div>

                      <div class="d-flex flex-column">
                        <v-radio-group
                          name="metodoPago"
                          v-model="paymentMethodToPay"
                          row
                          v-validate="'required'"
                        >
                          <v-radio
                            v-for="item in paymentMethodList"
                            :key="item.id"
                            :label="item.nombre"
                            :value="item.id"
                            @click="verifyNeedVoucher(item.ticket)"
                          ></v-radio>
                        </v-radio-group>
                        <form-error
                          :attribute_name="'metodoPago'"
                          :errors_form="errors"
                        ></form-error>
                      </div>
                      <v-text-field
                        outlined
                        dense
                        name="Número de voucher"
                        v-model="voucher"
                        label="Número de voucher"
                        v-show="showInputVoucher"
                      ></v-text-field>
                      <form-error
                        :attribute_name="'voucher'"
                        :errors_form="errors"
                      ></form-error>
                    </v-form>
                    <v-card-title class="text-h4 justify-center">
                      Q. {{ totalOrder }}
                    </v-card-title>
                    <br />
                    <v-btn
                      rounded
                      block
                      color="primary"
                      x-large
                      class="float: bottom"
                      @click="validateForm()"
                      v-if="showPaymentButton"
                    >
                      Generar pago
                    </v-btn>
                  </v-card-text>
                </v-container>
              </v-card>
            </v-col>
          </v-row>
        </div>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import FormError from '../../shared/FormError.vue'
import CustomerForm from '../cliente/IndexComponent.vue'
import moment from 'moment'

import { createNamespacedHelpers } from 'vuex'

const { mapGetters: restaurantMapGetter } = createNamespacedHelpers(
  'restaurant',
)

import { v4 as uuidv4 } from 'uuid'

export default {
  components: {
    FormError,
    CustomerForm,
  },
  props: {
    item: {},
  },
  data() {
    return {
      loading: false,
      dialog: false,
      search: '',
      no_voucher: false,

      cliente: '',
      items: [],
      nit: '',
      nombreCliente: '',
      direccion: '',
      paymentMethodToPay: '',
      paymentMethodList: [],
      paymentId: '',
      voucher: '',

      recordDetail: {},
      totalOrder: '',
      tmp: [],
    }
  },
  mounted() {
    this.configError()
    this.getRecordDetail()
    this.paymentId = uuidv4()
  },
  created() {
    events.$on('close_customer_form', this.eventCloseCustomerForm)
  },
  beforeDestroy() {
    events.$off('close_customer_form')
  },
  watch: {
    search(query) {
      if (!query || query.length < 3) return

      this.$parent.$store.state.services.customerService
        .searchCustomer(query)
        .then(({ data }) => {
          this.items = data.data
        })
        .catch((e) => {
          this.$toastr.error(e, 'Error')
        })
    },
  },
  computed: {
    showInputVoucher() {
      return this.no_voucher
    },
    showPaymentButton() {
      return this.tmp.length > 0
    },
    ...restaurantMapGetter(['caja']),
  },
  methods: {
    getNewAmountOrderTotal() {
      let temp = 0

      this.recordDetail.detalle.forEach((item) => {
        temp += parseFloat(item.precio) * parseInt(item.cantidad)
      })
      this.totalOrder = temp.toFixed(2)
    },
    deleteMenu(item) {
      let index = this.recordDetail.detalle.indexOf(item)
      this.recordDetail.detalle.splice(index, 1)
      this.getNewAmountOrderTotal()
      return
    },
    verifyNeedVoucher(item) {
      this.no_voucher = item === 1 ? true : false
    },
    validateForm() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          this.savePayment()
        }
      })
    },
    savePayment() {
      let detail = []
      this.recordDetail.detalle.forEach((item) => {
        detail.push(item.id)
      })

      let data = {
        id: this.paymentId,
        orden_id: this.item,
        tipo_pago_id: this.paymentMethodToPay,
        cliente_id: this.cliente.id,
        monto: this.totalOrder,
        voucher: this.voucher,
        ticket: this.showInputVoucher ? 1 : 0,
        detalle: detail,
        caja: this.caja,
      }

      this.loading = true

      this.$parent.$store.state.services.orderService
        .paymentOrders(data)
        .then((r) => {
          this.getInvoice(this.paymentId)
          this.$toastr.success('Registro generado con éxito', 'Mensaje')
          this.closeForm()
        })
        .catch((e) => {
          this.$toastr.error(e.response.data.error, 'Error')
        })
        .finally(() => {
          this.loading = false
        })
    },
    setPaymentCustomer(data) {
      if (!data) {
        this.items = []
        this.nit = ''
        this.nombreCliente = ''
        this.direccion = ''
        return
      }

      this.nit = data.nit
      this.nombreCliente = data.nombre
      this.direccion = data.direcciones
    },
    eventCloseCustomerForm() {
      this.dialog = false
    },
    closePDFModal() {
      this.dialogPdf = false
    },
    newCustomer() {
      this.dialog = true
    },
    setSubtotalItem(item) {
      return 'Q. ' + parseFloat(item.precio * item.cantidad).toFixed(2)
    },
    getAbsoluteImagePath(item) {
      if (!item) {
        return '../../../../static/img/no-photo.png'
      }
      return this.$parent.$store.state.services.productService.domainUrl + item
    },
    getSubtotalOrder(price, quantity) {
      let total = price * quantity

      return 'Q. ' + parseFloat(total).toFixed(2)
    },
    closeCustomerForm() {
      this.dialog = false
    },
    closeForm() {
      events.$emit('close_payment_form', true)
    },
    getRecordDetail() {
      this.loading = true

      Promise.all([this.getPaymentMethods(), this.getOrderDetailToPayment()])
        .then((r) => {})
        .catch((e) => {
          this.$toastr.error(e, 'Error')
        })
        .finally(() => {
          this.loading = false
        })
    },
    getInvoice(saleId) {
      this.loading = true

      this.$parent.$store.state.services.invoiceRestaurantService
        .getInvoice(saleId)
        .then((r) => {
          const blob = new Blob([r.data], { type: r.data.type })
          const url = window.URL.createObjectURL(blob)
          const link = document.createElement('a')
          link.href = url
          let fileName = moment().format('MMDDYYYY_h:mm:ss') + '.pdf'
          link.setAttribute('download', fileName)
          document.body.appendChild(link)
          link.click()
          link.remove()
          window.URL.revokeObjectURL(url)
        })
        .catch((e) => {
          this.$toastr.error(e, 'Error')
        })
        .finally(() => {
          this.loading = false
        })
    },
    getPaymentMethods() {
      return new Promise((resolve, reject) => {
        this.$parent.$store.state.services.paymentMethodService
          .getListPaymentMethod()
          .then((r) => {
            this.paymentMethodList = r.data.data
            resolve()
          })
          .catch((e) => {
            reject(e)
          })
      })
    },
    getOrderDetailToPayment() {
      return new Promise((resolve, reject) => {
        let id = this.item

        this.$parent.$store.state.services.orderService
          .getOneOrders(id)
          .then((r) => {
            this.recordDetail = r.data.data
            this.totalOrder = this.recordDetail.monto
            this.tmp = this.recordDetail.detalle
            resolve()
          })
          .catch((e) => {
            reject(e)
          })
      })
    },
    configError() {
      let dict = {
        custom: {
          metodoPago: {
            required: 'Debe seleccionar un método de pago',
          },
          cliente: {
            required: 'Debe seleccionar el NIT del cliente',
          },
        },
      }

      this.$validator.localize('es', dict)
    },
  },
}
</script>
