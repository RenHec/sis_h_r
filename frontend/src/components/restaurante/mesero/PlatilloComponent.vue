<template>
  <v-col md='4' sm='6'>
    <v-card style="padding:15px; ">
      <v-tooltip top open-delay="5">
        <template v-slot:activator="{ on, attrs }">
          <v-img
            :src="getAbsoluteImagePath(platillo.img)"
            style="border-radius:50%; height:150px; width:150px; margin:auto"
            gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
            v-bind="attrs"
            v-on="on"
          >
          </v-img>
        </template>
        <span>{{ platillo.descripcion }}</span>
      </v-tooltip>
      <v-card-title style="padding:2px;justify-content:center!important;" v-text="platillo.nombre"></v-card-title>
      <v-card-title style="padding:2px;justify-content:center!important;">Q. {{ platillo.precio }}</v-card-title>
      <v-card-actions>
        <v-btn rounded block color='error' dark large @click="addOrder(platillo)">
          Agregar
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-col>
</template>

<script>
import { mapActions, mapMutations, createNamespacedHelpers } from 'vuex'

const {
  mapActions: restaurantMapActions,
  mapMutations: restaurantMapMutations,
  mapGetters: restaurantMapGetter
} = createNamespacedHelpers('restaurant')

export default{
  components:{
  },
  props:{
    platillo:{}
  },
  data(){
    return{
    }
  },
  mounted(){
  },
  created(){
  },
  methods:{
    ...restaurantMapMutations([
      'SET_ORDER_ITEM'
    ]),
    ...restaurantMapActions([
      'UPDATE_AMOUNT_ORDER'
    ]),

    addOrder(item){
      item.cantidad = 1
      let menuAlreadyExists = false

      for(let menu of this.ordersRestaurant){
        if(menu.id === item.id){
          this.$toastr.info('El item ya está incluido en el detalle','Mensaje')
          menuAlreadyExists = true
          break;
        }
      }

      if(!menuAlreadyExists){
        this.SET_ORDER_ITEM(item)
        this.updateAmountOrder()
      }
    },

    updateAmountOrder(){
      let amount = 0

      this.ordersRestaurant.forEach((item) => {
        amount+= item.precio * item.cantidad
      })

      this.UPDATE_AMOUNT_ORDER(amount)
    },

    getAbsoluteImagePath(item){
      if(!item){
        return '../../../../static/img/no-photo.png'
      }
      return this.$store.state.services.productService.domainUrl+item
    },
  },
  computed:{
    ...restaurantMapGetter([
      'ordersRestaurant'
    ]),
  },
}
</script>
