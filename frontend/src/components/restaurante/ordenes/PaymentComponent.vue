<template>
  <v-row  class="justify-center">
    <v-col md='12' sm='12'>
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
        <div
            style="background-color: #e3f2fd; height: 85vh;"
          >
          <v-row>
            <v-col md='7' sm='12'>
              <div  style="height: 80vh; overflow-y:scroll;">
                <template v-for="(item) in recordDetail.detalle">
                  <v-card v-bind:key="item.id" class="mx-2 my-2" style="padding:15px">
                    <v-row class="justify-center">
                      <v-col md='3' sm='3'>
                        <v-img
                          :src="getAbsoluteImagePath(item.img)"
                          style="border-radius:75%; height:64px; width:64px;"
                          class="mx-2 py-2 mr-4">
                        </v-img>
                      </v-col>
                      <v-col md='9' sm='9' class="d-flex flex-row">
                        <v-card-text class="text-h6 col-9">
                          {{ item.cantidad }}&nbsp;{{ item.producto }}
                        </v-card-text>
                        <v-card-text class="text-h6 col-3 justify-end">
                          {{ setSubtotalItem(item) }}
                        </v-card-text>
                      </v-col>
                    </v-row>
                  </v-card>
                </template>
              </div>
            </v-col>
            <v-col md='5' sm='12'>
              <v-card class="mx-2 my-2">
                <v-container fluid>
                  <v-chip class="ma-2" :color="recordDetail.color" dark>
                      <v-icon left>
                        {{ recordDetail.icono }}
                      </v-icon>
                      {{ recordDetail.estado_orden }}
                    </v-chip>
                  <v-card-text>
                    <v-text-field
                      outlined
                      dense
                      name="cliente"
                      v-model="cliente"
                      v-validate="'required'"
                      label="Cliente"
                      append-outer-icon="search"
                      @click:append-outer="searchClient">
                    </v-text-field>
                    <v-text-field
                      outlined
                      disabled
                      dense
                      name="nit"
                      v-model="nit"
                      label="NIT">
                    </v-text-field>
                    <div class="d-flex flex-column">
                      <v-radio-group name="tipo-orden" v-model="paymentMethodToPay" row v-validate="'required'">
                        <v-radio
                          v-for="item in paymentMethodList"
                          :key="item.id"
                          :label="item.nombre"
                          :value="item.id"
                        >
                        </v-radio>
                      </v-radio-group>
                      <form-error :attribute_name="'tipo-orden'" :errors_form="errors"> </form-error>
                    </div>
                    <v-card-title class="text-h4 justify-center">Q. {{ recordDetail.monto }}</v-card-title>
                    <br/>
                    <v-btn rounded block color="primary" x-large class="float: bottom">
                      Generar pago
                    </v-btn>
                  </v-card-text>
                </v-container>
              </v-card>
            </v-col>
          </v-row>
          <!-- <v-simple-table>
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
          </v-simple-table> -->
          <!-- Totales -->
          <!-- <div class="d-flex flex-column">
          </div> -->
        </div>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import FormError from '../../shared/FormError.vue'

export default{
  components:{
    FormError,
  },
  props:{
    item:{}
  },
  data(){
    return{
      loading:false,

      cliente:'',
      nit:'',
      paymentMethodToPay:'',
      paymentMethodList:[],

      recordDetail:{}
    }
  },
  mounted(){
    this.getRecordDetail()
  },
  created(){
  },
  methods:{
    searchClient(){

    },
    setSubtotalItem(item){
      return 'Q. '+parseFloat(item.precio * item.cantidad).toFixed(2)
    },
    getAbsoluteImagePath(item){
      return this.$parent.$store.state.services.productService.domainUrl+item
    },
    getSubtotalOrder(price, quantity)
    {
      let total = price * quantity

      return 'Q. '+parseFloat(total).toFixed(2)
    },
    closeForm()
    {
      events.$emit("close_payment_form",true)
    },
    getRecordDetail(){
      this.loading = true

      Promise.all([this.getPaymentMethods(),this.getOrderDetailToPayment()])
        .then((r)=>{})
        .catch((e) =>{
          this.$toastr.error(e,'Error')
        })
        .finally(()=>{
          this.loading = false
        })
    },
    getPaymentMethods()
    {
      return new Promise((resolve,reject)=>{
        this.$parent.$store.state.services.paymentMethodService
          .getListPaymentMethod()
          .then((r)=>{
            this.paymentMethodList = r.data.data
            resolve()
          })
          .catch((e)=>{
            reject(e)
          })
      })
    },
    getOrderDetailToPayment()
    {
      return new Promise((resolve, reject)=>{
        let id = this.item

        this.$parent.$store.state.services.orderService
          .getOneOrders(id)
          .then((r)=>{
            this.recordDetail = r.data.data
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
