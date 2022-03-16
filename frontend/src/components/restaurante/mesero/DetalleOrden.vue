<template>
  <div>
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
    <v-container style="height:85vh; overflow-y:scroll;" class="d-flex flex-column">
      <template v-for="item in ordersRestaurant">
        <v-row v-bind:key="item.key">
          <v-col md='12' sm='12'>
            <v-card style="background-color:#e3f2fd; padding:5px;">
              <v-row style="text-align:center;">
                <v-col md='3'>
                  <v-img
                    :src="getAbsoluteImagePath(item.img)"
                    style="border-radius:75%; height:50px; width:50px; margin:auto"
                    gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                  ></v-img>
                </v-col>
                <v-col md='7' class="d-flex flex-column">
                  <h3>{{ item.nombre }}</h3>
                  <h3>Q. {{ item.precio }}</h3>
                </v-col>
                <v-col md='2' class="d-flex flex-column">
                  <v-icon style="cursor:pointer" right large color="red" @click="deleteMenu(item)">fas fa-trash</v-icon>
                </v-col>
              </v-row>
              <v-row style="text-align:center; background-color:#fff;margin:2px;border-radius:15px;">
                <v-col md='4'><v-btn fab small color="amber" @click="minusQuantity(item)"><v-icon small>fas fa-minus</v-icon></v-btn></v-col>
                <v-col md='4'><h2>{{ item.cantidad }}</h2></v-col>
                <v-col md='4'><v-btn fab small color="amber" @click="plusQuantity(item)"><v-icon small>fas fa-plus</v-icon></v-btn></v-col>
              </v-row>
            </v-card>
          </v-col>
        </v-row>
      </template>
      <v-card-title class="mt-5 justify-center" v-show="ordersRestaurant.length > 0">
        <div class="d-flex flex-column">
          <v-radio-group name="tipo-orden" v-model="orderType" row v-validate="'required'">
            <v-radio
              v-for="item in orderTypeList"
              :key="item.id"
              :label="item.nombre"
              :value="item.id"
            >
            </v-radio>
          </v-radio-group>
          <form-error :attribute_name="'tipo-orden'" :errors_form="errors"> </form-error>

          <h2>Total: Q. {{ formatPrice(totalAmountOrdersRestaurant) }}</h2>
        </div>
      </v-card-title>
      <v-card-actions v-if="ordersRestaurant.length > 0">
        <v-btn rounded block color='error' dark large @click="validateForm()">
          Generar orden
        </v-btn>
      </v-card-actions>
    </v-container>
  </div>
</template>

<script>

import { mapGetters, createNamespacedHelpers } from 'vuex'
import FormError from '../../shared/FormError.vue'
import { v4 as uuidv4 } from 'uuid'
import moment from 'moment'

const {
  mapGetters: restaurantMapGetter,
  mapMutations: restaurantMapMutations,
  mapActions: restaurantMapActions
} = createNamespacedHelpers('restaurant')

export default{
  components:{
    FormError,
  },
  props:{
     item:{},
  },
  data(){
    return{
      orderTypeList: [],
      orderType:'',
      orderId:'',
      loading:false,
    }
  },
  mounted(){
    this.getOrderTypes()
    this.orderId = uuidv4()
  },
  created(){
  },
  methods:{
    ...restaurantMapMutations([
      'SET_LIST_ITEMS'
    ]),
    ...restaurantMapActions([
      'UPDATE_AMOUNT_ORDER',
      'UPDATE_SELECTED_TABLE'
    ]),

    validateForm(){
      this.$validator.validateAll().then((result) => {
        if (result) {
          this.generateOrder()
        }
      })
    },

    generateOrder(){
       this.loading = true

       let data = {
        'id': this.orderId,
        'monto': this.totalAmountOrdersRestaurant,
        'tipo_orden_id': this.orderType,
        'fecha': moment().format('YYYY-MM-D'),
        'hora': moment().format('h:mm a'),
        'detalle':this.ordersRestaurant,
        'mesa_id':this.selectedTable
       }

       this.$store.state.services.waiterService
          .storeWaitersOrder(data)
          .then((r)=>{
            this.$toastr.success('Orden guardada con éxito','Mensaje')
            this.orderId = uuidv4()
            this.SET_LIST_ITEMS([])
            this.UPDATE_SELECTED_TABLE({'id':0,'name':'','selected':false})
            this.$validator.reset()
          })
          .catch((e)=>{
            this.$toastr.error(e,'Error')
          })
          .finally(()=>{
            this.loading = false
          })
    },

    getOrderTypes(){
      this.loading = true

      this.$parent.$store.state.services.orderTypeService
        .getListOrderType()
        .then((r)=>{
          this.orderTypeList = r.data.data
        })
        .catch((e)=>{
          this.$toastr.error(e,'Error')
        })
        .finally(()=>{
          this.loading = false
        })
    },

    deleteMenu(item){
      let auxiliarOrders = this.reduceMenus(this.ordersRestaurant, item.id)
      this.SET_LIST_ITEMS(auxiliarOrders)
      this.updateAmountOrder()
    },

    getAbsoluteImagePath(item){
      return this.$store.state.services.productService.domainUrl+item
    },

    formatPrice(value) {
      return  parseFloat(value).toFixed(2);
    },

    updateAmountOrder(){
      let amount = 0

      this.ordersRestaurant.forEach((item) => {
        amount+= parseFloat(item.precio) * item.cantidad
      })

      this.UPDATE_AMOUNT_ORDER(amount)
    },

    minusQuantity(item){
      if(item.cantidad === 1){
        return
      }

      this.modifyQuantityMenus('minus', item)
    },

    plusQuantity(item){
      this.modifyQuantityMenus('plus', item)
    },

    modifyQuantityMenus(opc, item){
      var auxiliar = []
      this.ordersRestaurant.forEach(menu =>{
        if(item.id == menu.id){
          opc == 'plus' ? menu.cantidad++ : menu.cantidad--
          auxiliar.push(menu)
          return
        }
        auxiliar.push(menu)
      });

      this.SET_LIST_ITEMS(auxiliar)
      this.updateAmountOrder()
    },

    reduceMenus(array, id) {
      var auxiliar = []
      array.forEach(item =>{
        if(item.id !== id) {
          auxiliar.push(item)
        }
      });
      return auxiliar
    },

  },
  computed:{
    ...restaurantMapGetter([
      'ordersRestaurant',
      'totalAmountOrdersRestaurant',
      'selectedTable'
    ]),
  },
}
</script>
