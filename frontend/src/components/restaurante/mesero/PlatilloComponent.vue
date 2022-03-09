<template>
  <v-col md='4' sm='6'>
    <v-card style="padding:15px; ">
      <v-img
        :src="platillo.img"
        style="border-radius:50%; height:150px; width:150px; margin:auto"
        gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
      >
      </v-img>
      <v-card-title style="padding:2px;justify-content:center!important;" v-text="platillo.nombre"></v-card-title>
      <v-card-title style="padding:2px;justify-content:center!important;">Q. {{ platillo.precio }}</v-card-title>
      <v-card-actions>
        <v-btn rounded block color='error' dark large>
          Agregar
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-col>
</template>

<script>

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
  },
}
</script>
