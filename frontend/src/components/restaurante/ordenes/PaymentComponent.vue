<template>
  <v-row  class="justify-center">
    <v-col md='8' sm='8'>
      <v-card>
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
        <div style="padding: 10px;">
          <v-simple-table>
            <thead>
              <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <template v-for="item in recordDetail.detalle">
                <tr v-bind:key="item.id">
                  <td>{{ item.producto }}</td>
                  <td>Q. {{ item.precio }}</td>
                  <td>{{ item.cantidad }}</td>
                  <td>{{ getSubtotalOrder(item.precio,item.cantidad)}}</td>
                </tr>
              </template>
            </tbody>
          </v-simple-table>
          <!-- Totales -->
          <div class="d-flex flex-column">
            <v-card-title class="text-h4 justify-center">Q. {{ recordDetail.monto }}</v-card-title>
            <br/>
            <v-btn rounded block color="primary" x-large>
              Generar pago
            </v-btn>
          </div>
        </div>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
//import component from '@/components'

export default{
  components:{
  },
  props:{
    item:{}
  },
  data(){
    return{
      loading:false,

      recordDetail:{}
    }
  },
  mounted(){
    this.getOrderDetailToPayment()
  },
  created(){
  },
  methods:{
    getSubtotalOrder(price, quantity)
    {
      let total = price * quantity

      return 'Q. '+parseFloat(total).toFixed(2)
    },
    closeForm()
    {
      events.$emit("close_payment_form",true)
    },
    getOrderDetailToPayment()
    {
      this.loading = true
      let id = this.item

      this.$parent.$store.state.services.orderService
        .getOneOrders(id)
        .then((r)=>{
          this.recordDetail = r.data.data
        })
        .catch((e)=>{
          this.$toastr.error(e,'Error')
        })
        .finally(()=>{
          this.loading = false
        })
    },
  },
}
</script>
