<template>
  <v-col md='4' sm='6'>
    <v-card class="mt-3 mx-2">
      <v-toolbar>
        <v-toolbar-title>
          <strong>Pedido #{{ item.id }}</strong> -- {{ item.fecha }} {{ item.hora }}
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn fab small dark color="blue-grey">
          <v-icon dark>fas fa-user</v-icon>
        </v-btn>
      </v-toolbar>
      <v-virtual-scroll
        :bench="'0'"
        :items="item.items"
        item-height="85"
        style="height:30vh"
      >
      <template v-slot:default="{ item }">
        <v-list-item :key="item.id">
          <v-list-item-action>
            <v-img
              :src="item.img"
              style="border-radius:75%; height:50px; width:50px; margin:auto"
              gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
            ></v-img>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title class="d-flex flex-column">
              <strong>{{ item.nombre }}</strong>
              <div>Precio: <strong>Q.{{ item.precio }}</strong></div>
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
        <v-card-title><strong>Total: Q.{{ item.total }}</strong></v-card-title>
        <v-spacer></v-spacer>
        <v-btn class="red darken-1" dark><v-icon>fas fa-clock-o</v-icon>&nbsp;Pendiente</v-btn>
      </v-card-actions>
    </v-card>
  </v-col>
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
      estados: [
                {'nombre':'Pendiente'},
                {'nombre':'En preparación'},
                {'nombre':'Preparado'},
                {'nombre':'Servido'}
                ],
    }
  },
  mounted(){
  },
  created(){
  },
  methods:{
  },
}
</script>
