<template>
    <v-row>
      <v-overlay :value="loading">
        <v-progress-circular indeterminate size="64"></v-progress-circular>
      </v-overlay>
      <v-col md='12' sm='12' v-if="!showUpdateOrderScreen && !showDetailOrderScreen">
        <v-card>
          <v-toolbar>
            <v-toolbar-title>Listado de órdenes</v-toolbar-title>
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
                        <div class="justify-center" v-if="checkStatusOrder(item)">
                          <v-btn
                            class="ma-1 float-right"
                            fab
                            small
                            dark
                            color="white"
                            @click="deleteOrder(item)"
                          >
                            <v-icon dark color="red">
                              delete
                            </v-icon>
                          </v-btn>
                        </div>
                        <div class="justify-center" v-if="checkStatusOrder(item)">
                          <v-btn
                            class="ma-1 float-left"
                            small
                            fab
                            dark
                            color="white"
                            @click="finishOrder(item)"
                          >
                            <v-icon dark color="green">
                              check
                            </v-icon>
                          </v-btn>
                        </div>
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
      <UpdateOrderComponent v-if="showUpdateOrderScreen" :tableName="tableName" :orderId="orderId"></UpdateOrderComponent>
      <DetailOrderComponent v-if="showDetailOrderScreen" :tableName="tableName" :orderId="orderId"></DetailOrderComponent>
    </v-row>
</template>

<script>
import moment from 'moment'
import UpdateOrderComponent from './UpdateOrderComponent.vue'
import DetailOrderComponent from './DetailOrdenComponent.vue'

export default{
  components:{
    UpdateOrderComponent,
    DetailOrderComponent
  },
  data(){
    return{
      loading: false,

      recordList:[],
      itemUpdate:{},

      mainTable:true,
      formNewRecord:false,
      formUpdateRecord:false,

      updateOrderScreen:false,
      detailOrderScreen:false,
      orderId:0,
      tableName:'',
    }
  },
  mounted(){
    this.getAllFoodCategory()
  },
  created(){
    events.$on('close_update_order',this.eventCloseUpdateOrder)
    events.$on('close_detail_order',this.eventCloseDetailOrder)
  },
  beforeDestroy(){
    events.$off('close_update_order')
    events.$off('close_detail_order')
  },
  methods:{
    eventCloseUpdateOrder(){
      this.orderId = 0
      this.updateOrderScreen = false
      this.tableName = ''
      this.getAllFoodCategory()
    },
    eventCloseDetailOrder(){
      this.orderId = 0
      this.detailOrderScreen = false
      this.tableName = ''
      this.getAllFoodCategory()
    },
    finishOrder(item){
      let data = {
        'orden':item.id
      }
      this.$swal({
        title: 'Modificar estado',
        text: '¿Está seguro de modificar estado de la orden?',
        type: 'question',
        showCancelButton: true,
      }).then((r) => {
        if(!r.value){
          this.close
          return
        }
        this.loading = true
        this.$store.state.services.orderDetailService
        .modifyStateWaiterOrderDetail(data)
        .then((r) =>{
          this.getAllFoodCategory()
          this.$toastr.success('Los productos asociados fueron actualizados con éxito','Mensaje')
        })
        .catch((e) =>{
          this.$toastr.error(e,'Error')
        })
        .finally(()=>{
          this.loading = false
        })
      })
    },
    deleteOrder(item){

      let data = {
        'orden':item.id
      }
      this.$swal({
        title: 'Eliminar',
        text: '¿Está seguro de eliminar la orden?',
        type: 'question',
        showCancelButton: true,
      }).then((r) => {
        if(!r.value){
          this.close
          return
        }
        this.loading = true
        this.$store.state.services.orderDetailService
        .deleteAllOrderDetail(data)
        .then((r) =>{
          this.getAllFoodCategory()
        })
        .catch((e) =>{
          this.$toastr.error(e,'Error')
        })
        .finally(()=>{
          this.loading = false
        })
      })

    },
    checkStatusOrder(item){
      return item.inicia === 1
    },
    checkTable(item){
      if(item.inicia === 1 ){
        this.updateOrderScreen = true
        this.orderId = item.id
        this.tableName = item.mesa
      }else if(item.finaliza === 0 ){
        this.detailOrderScreen = true
        this.tableName = item.mesa
        this.orderId = item.id
      }
    },
    initializeView(){
      this.formNewRecord = false
      this.formUpdateRecord = false
      this.mainTable = true
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
    showUpdateOrderScreen(){
      return this.updateOrderScreen
    },
    showDetailOrderScreen(){
      return this.detailOrderScreen
    }
  }
}
</script>
