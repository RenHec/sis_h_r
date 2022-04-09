<template>
  <v-col md='4' sm='6'>
    <v-card class="mt-3 mx-2">
      <v-toolbar>
        <v-toolbar-title class="p-2">
          <div class="d-flex flex-column">
            <strong>Pedido de {{ item.mesa }}</strong>
            <div>{{ setFormatDate(item.fecha) }} {{ item.hora }}</div>
          </div>
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn fab small dark color="blue-grey">
          <v-icon dark>fas fa-user</v-icon>
        </v-btn>
      </v-toolbar>
      <v-virtual-scroll
        :bench="'0'"
        :items="item.detalle"
        item-height="85"
        style="height:30vh"
      >
      <template v-slot:default="{ item }">
        <v-list-item :key="item.id">
          <v-list-item-action>
            <v-img
              :src="getAbsoluteImagePath(item.img)"
              style="border-radius:75%; height:50px; width:50px; margin:auto"
              gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
            ></v-img>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title class="d-flex flex-column">
              <strong>{{ item.producto }}</strong>
              <span>Precio: <strong>Q.{{ item.precio }}</strong></span>
            </v-list-item-title>
          </v-list-item-content>
          <v-list-item-action>
            <v-btn fab small depressed color="primary">
              <h2>{{ item.cantidad }}</h2>
            </v-btn>
          </v-list-item-action>
        </v-list-item>
        <v-divider></v-divider>
      </template>
      </v-virtual-scroll>

      <v-card-actions class="d-flex flex-row">
        <v-card-title>{{ item.tipo_orden }}</v-card-title>
        <v-spacer></v-spacer>
        <v-btn :class="item.color" dark @click="setNextState(item)"><v-icon>{{ item.icono }}</v-icon>&nbsp;{{ item.estado_orden }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-col>
</template>

<script>
import moment from 'moment'
//import component from '@/components'

export default{
  components:{
  },
  props:{
    item:{},
    listStatus:{}
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
    setNextState(item){
      if(item.finaliza !== 1)
      {
        for (let index = 0; index < this.listStatus.length; index++) {
          if(item.estado_orden_id === this.listStatus[index].id)
          {
            let data = { 'orden': item.id, 'estado':this.listStatus[index+1].id}
            events.$emit('update_order_state',data)
            break
          }
        }
      }
    },
    setFormatDate(data){
      return moment(data).format('D-MM-YYYY')
    },
    getAbsoluteImagePath(item){
      if(!item){
        return '../../../../static/img/no-photo.png'
      }
      return this.$store.state.services.productService.domainUrl+item
    },
  },
}
</script>
