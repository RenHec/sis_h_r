<template>
  <v-row>
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
    <v-col md="12" sm="12">
      <v-dialog persistent v-model="dialog" max-width="85%">
        <v-card>
          <CajaEgresosComponent v-if="dialog" />
        </v-card>
      </v-dialog>
    </v-col>
    <v-col cols="12" v-if="!paymentScreen && cashOpened">
      <v-toolbar color="primary" dark extended flat>
        <v-btn class="success" text @click="showEgresosForm()">
          Registrar gastos
        </v-btn>
        <v-spacer></v-spacer>
        <v-btn v-show="cerrarCaja" dark color="red" @click="closeCash()">
          Cerrar caja
        </v-btn>
      </v-toolbar>

      <v-card class="mx-auto" max-width="90%" style="margin-top: -30px;">
        <v-toolbar flat>
          <v-toolbar-title>Registrar pago</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-btn icon @click="recharge()">
            <v-icon>replay</v-icon>
          </v-btn>
        </v-toolbar>

        <v-divider></v-divider>

        <div style="background-color: #e3f2fd;">
          <v-row>
            <template v-for="item in recordList">
              <v-col v-bind:key="item.id" md="4" cols="12">
                <v-card class="mt-3 mx-2" :color="item.color" dark>
                  <div @click="checkTable(item)">
                    <v-card-title class="justify-center text-h4" bold>
                      {{ item.mesa }}
                    </v-card-title>
                    <div>
                      <p class="text-center" dark>
                        <v-icon>{{ item.icono }}</v-icon>
                        &nbsp;{{ item.estado_orden }}
                      </p>
                      <p class="text-center" dark>
                        {{ setFormatDate(item.fecha) }} {{ item.hora }}
                      </p>
                      <p class="text-center text-h6 mb-0">
                        {{ item.tipo_orden }}
                      </p>
                    </div>
                    <v-card-title class="justify-center text-h4">
                      {{ getAmountTitle(item.monto) }}
                    </v-card-title>
                  </div>
                </v-card>
              </v-col>
            </template>
          </v-row>
        </div>
      </v-card>
    </v-col>
    <PaymentComponent
      v-if="showPaymentScreen && cashOpened"
      :item="orderId"
    ></PaymentComponent>
    <CashOpenedComponent v-if="!cashOpened"></CashOpenedComponent>
  </v-row>
</template>

<script>
import moment from 'moment'
import CajaEgresosComponent from './CajaEgresosComponent.vue'
import PaymentComponent from './PaymentComponent.vue'
import CashOpenedComponent from './AperturarCajaComponent.vue'

import { createNamespacedHelpers } from 'vuex'

const {
  mapGetters: restaurantMapGetter,
  mapActions: restaurantMapActions,
} = createNamespacedHelpers('restaurant')

export default {
  components: {
    PaymentComponent,
    CashOpenedComponent,
    CajaEgresosComponent,
  },
  data() {
    return {
      loading: false,

      recordList: [],
      itemUpdate: {},
      dialog: false,

      mainTable: true,
      formNewRecord: false,
      formUpdateRecord: false,

      paymentScreen: false,
      orderId: 0,
      tableName: '',
    }
  },
  mounted() {
    this.getAllFoodCategory()
  },
  created() {
    events.$on('close_payment_form', this.eventClosePaymentForm)
    events.$on('close_egresos_form', this.eventCloseEgresosForm)
  },
  beforeDestroy() {
    events.$off('close_payment_form')
    events.$off('close_egresos_form')
  },
  methods: {
    ...restaurantMapActions(['UPDATE_CASH_OPENING']),
    getFormaHour() {
      return moment().format('h:mm:ss a')
    },
    getFormaDate() {
      return moment().format('yyyy-MM-DD')
    },
    closeCash() {
      this.$swal({
        title: 'Corte de caja',
        text: '¿Está seguro de realizar el corte de caja?',
        type: 'question',
        showCancelButton: true,
      }).then((r) => {
        if (!r.value) {
          this.close
          return
        }
        this.loading = true

        let data = {
          fecha: this.getFormaDate(),
          hora: this.getFormaHour(),
        }

        this.$store.state.services.checkoutRestaurantService
          .closeCash(data)
          .then((r) => {
            let data = r.data
            this.getVoucher(data.id)
            this.$toastr.success(
              'El cierre de caja se realizó correctamente',
              'Mensaje',
            )
            this.UPDATE_CASH_OPENING(false)
          })
          .catch((e) => {
            this.$toastr.error(e, 'Error')
          })
          .finally(() => {
            this.loading = false
          })
      })
    },

    getVoucher(caja) {
      this.loading = true

      this.$store.state.services.invoiceRestaurantService
        .getVoucherCash(caja)
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
    showEgresosForm() {
      this.dialog = true
    },
    eventCloseEgresosForm() {
      this.closeEgresosForm()
    },
    closeEgresosForm() {
      this.dialog = false
    },

    eventClosePaymentForm() {
      this.orderId = 0
      this.paymentScreen = false
      this.getAllFoodCategory()
    },
    checkTable(item) {
      if (item.finaliza === 1) {
        this.paymentScreen = true
        this.orderId = item.id
      }
    },
    recharge() {
      this.getAllFoodCategory()
    },

    getAllFoodCategory() {
      this.loading = true

      this.$store.state.services.orderService
        .getAllOrders()
        .then((r) => {
          this.recordList = []
          this.recordList = r.data.data
        })
        .catch((e) => {
          this.$toastr.error(e, 'Error')
        })
        .finally(() => {
          this.loading = false
        })
    },
    setFormatDate(data) {
      return moment(data).format('D-MM-YYYY')
    },
    getAmountTitle(item) {
      return 'Q. ' + item
    },

    aperturarCaja() {
      var permissions = this.$store.state.permissions
      return _.includes(permissions, 'apertura_caja_restaurante')
    },
  },
  computed: {
    showPaymentScreen() {
      return this.paymentScreen
    },
    ...restaurantMapGetter(['cashOpened']),
    cerrarCaja() {
      var permissions = this.$store.state.permissions
      return _.includes(permissions, 'cierre_caja_restaurante')
    },
  },
}
</script>
