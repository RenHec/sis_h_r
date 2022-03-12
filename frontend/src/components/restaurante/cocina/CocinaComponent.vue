<template>
  <v-row>
    <v-col md='12'>
      <v-card>
        <v-overlay :value="loading">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
          <v-toolbar>
            <v-toolbar-title style="padding:2px;" v-text="'Listado de órdenes para preparar'"></v-toolbar-title>
          </v-toolbar>
          <div style="background-color:#e3f2fd; height:85vh; overflow-y:scroll">
              <v-row>
                <template v-for="order in orders">
                  <OrdenComponent v-bind:key="order.id" :item="order"/>
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
    }
  },
  mounted(){
    this.getListOrders()
  },
  created(){
  },
  methods:{
    getListOrders()
    {
      this.loading = true

      this.$store.state.services.waiterService
        .getAllWaitersOrder()
        .then((r)=>{
          this.orders = r.data.data
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
