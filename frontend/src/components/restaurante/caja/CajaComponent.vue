<template>
    <v-row>
      <v-overlay :value="loading">
        <v-progress-circular indeterminate size="64"></v-progress-circular>
      </v-overlay>
      <v-col md='12' sm='12' v-if="!paymentScreen">
        <v-card>
          <v-toolbar>
            <v-toolbar-title>Registrar pago</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click="recharge()">
              <v-icon>replay</v-icon>
            </v-btn>
          </v-toolbar>
          <div style="background-color:#e3f2fd; height:85vh; overflow-y:scroll">
              <v-row>
                <template v-for="item in recordList">
                    <v-col v-bind:key="item.id" md="4" lg="3" sm='12' xs='12'>
                      <v-card class="mt-3 mx-2" :color="item.color" dark>
                        <div @click="checkTable(item)">
                          <v-card-title class="justify-center text-h4" bold>{{ item.mesa }}</v-card-title>
                        <div>
                          <p class="text-center" dark><v-icon>{{ item.icono }}</v-icon>&nbsp;{{ item.estado_orden }}</p>
                          <p class="text-center" dark>{{ setFormatDate(item.fecha) }} {{ item.hora }}</p>
                          <p class="text-center text-h6 mb-0" >{{ item.tipo_orden }}</p>
                        </div>
                        <v-card-title class="justify-center text-h4" >{{ getAmountTitle(item.monto) }}</v-card-title>
                        </div>
                      </v-card>
                    </v-col>
                </template>
              </v-row>
            </div>
        </v-card>
      </v-col>
      <PaymentComponent v-if="showPaymentScreen" :item="orderId"></PaymentComponent>
    </v-row>
</template>

<script>
import moment from 'moment'
import PaymentComponent from './PaymentComponent.vue'

export default{
  components:{
    PaymentComponent,
  },
  data(){
    return{
      loading: false,

      recordList:[],
      itemUpdate:{},

      mainTable:true,
      formNewRecord:false,
      formUpdateRecord:false,

      paymentScreen:false,
      orderId:0,
      tableName:'',
    }
  },
  mounted(){
    this.getAllFoodCategory()
  },
  created(){
    events.$on('close_payment_form',this.eventClosePaymentForm)
  },
  beforeDestroy(){
    events.$off('close_payment_form')
  },
  methods:{
    eventClosePaymentForm(){
      this.orderId = 0
      this.paymentScreen = false
      this.getAllFoodCategory()
    },
    checkTable(item){
      if(item.finaliza === 1){
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
        .finally(()=>{
          this.loading = false
        })
    },
    setFormatDate(data){
      return moment(data).format('D-MM-YYYY')
    },
    getAmountTitle (item){
      return 'Q. ' + item
    },
  },
  computed:{
    showPaymentScreen(){
      return this.paymentScreen
    }
  }
}
</script>
