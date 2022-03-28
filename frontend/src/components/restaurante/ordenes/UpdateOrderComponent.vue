<template>
  <v-row class="justify-center mt-5 mx-2">
    <v-col md='7' sm='12'>
      <v-card>
        <v-toolbar>
          <v-toolbar-title>Editar orden de <strong>{{ tableName }}</strong></v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn icon @click="closeUpdateOrder()">
            <v-icon dark>close</v-icon>
          </v-btn>
        </v-toolbar>
        <v-overlay :value="loading">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-container style="height:80vh; overflow-y:scroll;">
          <template v-for="item in listDetail">
            <v-row v-bind:key="item.key">
              <v-col md='12' sm='12'>
                <v-card style="background-color:#e3f2fd; padding:5px;">
                  <div class="d-flex flex-row">
                    <v-img
                        class="col-1 text-center"
                        :src="getAbsoluteImagePath(item.img)"
                        style="border-radius:75%; height:50px; width:50px; margin:auto;"
                        gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                      ></v-img>
                      <div class="d-flex flex-column col-10">
                        <h3 class="text-center">{{ item.producto }}</h3>
                        <h3 class="text-center">Q. {{ item.precio }}</h3>
                      </div>
                      <div class="col-1 text-center">
                        <v-icon style="cursor:pointer" large color="red" @click="deleteMenu(item)">fas fa-trash</v-icon>
                      </div>
                  </div>
                  <v-row style="text-align:center; background-color:#fff;margin:2px;border-radius:15px;">
                    <v-col md='4'><v-btn fab small color="amber" @click="minusQuantity(item)"><v-icon small>fas fa-minus</v-icon></v-btn></v-col>
                    <v-col md='4'><h2>{{ item.cantidad }}</h2></v-col>
                    <v-col md='4'><v-btn fab small color="amber" @click="plusQuantity(item)"><v-icon small>fas fa-plus</v-icon></v-btn></v-col>
                  </v-row>
                </v-card>
              </v-col>
            </v-row>
          </template>
        </v-container>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>

export default{
  components:{
  },
  data(){
    return{
      loading: false,

      listDetail:[]
    }
  },
  props:{
    orderId:{},
    tableName:{}
  },
  mounted(){
    this.getDetailOrder(this.orderId)
  },
  created(){
  },
  methods:{
    deleteMenu(item){
      this.loading = true

      let data = {
        'detalle': item.id,
        'orden':item.orden_id
      }

      this.$parent.$store.state.services.orderDetailService
        .deleteOneDetailOrder(data)
        .then((r)=>{
          this.$toastr.success('Registro actualizado con éxito','Mensaje')
          this.getDetailOrder(this.orderId)
        })
        .catch((e)=>{
          this.$toastr.error(e,'Error')
        })
        .finally(()=>{
          this.loading = false
        })
    },
    minusQuantity(item){
      if(item.cantidad === 1) return

      this.loading = true

      let data = {
        'detalle': item.id,
        'orden':item.orden_id
      }

      this.$parent.$store.state.services.orderDetailService
        .setMinusQuantity(data)
        .then((r)=>{
          this.$toastr.success('Registro actualizado con éxito','Mensaje')
          this.getDetailOrder(this.orderId)
        })
        .catch((e)=>{
          this.$toastr.error(e,'Error')
        })
        .finally(()=>{
          this.loading = false
        })

    },
    plusQuantity(item){
      this.loading = true

      let data = {
        'detalle': item.id,
        'orden':item.orden_id
      }

      this.$parent.$store.state.services.orderDetailService
        .setPlusQuantity(data)
        .then((r)=>{
          this.$toastr.success('Registro actualizado con éxito','Mensaje')
          this.getDetailOrder(this.orderId)
        })
        .catch((e)=>{
          this.$toastr.error(e,'Error')
        })
        .finally(()=>{
          this.loading = false
        })
    },
    closeUpdateOrder(){
      events.$emit("close_update_order",true)
    },
    getDetailOrder(orderId){

      this.loading = true

      this.$parent.$store.state.services.orderDetailService
        .getOrderDetail(orderId)
        .then(({data}) =>{
          this.listDetail = data.data
        })
        .catch((e)=>{
          this.$toastr.error(e, 'Error')
        })
        .finally(()=>{
          this.loading = false
        })
    },
    getAbsoluteImagePath(item){
      return this.$store.state.services.productService.domainUrl+item
    },
  },
}
</script>
