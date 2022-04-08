<template>
  <v-row>
    <v-col md='12'>
      <v-card>
        <v-overlay :value="loading">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
          <v-toolbar>
            <v-toolbar-title style="padding:2px;" v-text="'Listado de órdenes para preparar'"></v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click="recharge()">
              <v-icon>replay</v-icon>
            </v-btn>
          </v-toolbar>
          <div style="background-color:#e3f2fd; height:85vh; overflow-y:scroll">
              <v-row>
                <template v-for="order in orders">
                  <OrdenComponent v-bind:key="order.id" :item="order" :listStatus="listStatus" v-if="order.finaliza !== 1 && order.detalle.length > 0"/>
                </template>
              </v-row>
          </div>
        </v-card>
    </v-col>
  </v-row>
</template>

<script>
import OrdenComponent from './OrdenComponent.vue'

export default{
  components:{
    OrdenComponent,
  },
  data(){
    return{
      loading: false,
      orders:[],
      listStatus:[],
    }
  },
  mounted(){
    this.getRecords()
  },
  created(){
    events.$on('update_order_state',this.eventUpdateOrderState)
  },
  beforeDestroy(){
    events.$off('update_order_state')
  },
  methods:{
    eventUpdateOrderState(data){
      this.loading = true

      this.$store.state.services.waiterService
        .updateWaiterOrderStatus(data)
        .then((r)=>{
          this.getRecords()
        })
        .catch((e)=>{
          this.$toastr.error(e,'Error')
        })
        .finally(()=>{
          this.loading = false
        })
    },
    recharge(){
      this.getRecords()
    },
    getRecords(){

      this.loading = true

      Promise.all([this.getOrderStatus(), this.getListOrders()])
        .then((r)=>{

        })
        .catch((e)=>{
          this.$toastr.error(e,'Error')
        })
        .finally(()=>{
          this.loading = false
        })
    },
    getOrderStatus()
    {
      return new Promise((resolve, reject) => {

        this.$store.state.services.statusOrderService
        .getListOrderStatus()
        .then((r)=>{
          this.listStatus = r.data.data
          resolve()
        })
        .catch((e) => {
          reject(e)
        })
       })
    },
    getListOrders()
    {
      return new Promise((resolve, reject) => {

        this.$store.state.services.waiterService
        .getAllWaitersOrder()
        .then((r)=>{
          this.orders = r.data.data
          resolve()
        })
        .catch((e)=>{
          reject(e)
        })
       })
    },
  },
}
</script>
