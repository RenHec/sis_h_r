<template>
  <v-col md='12' sm='12'>
    <v-card style="background-color:#e3f2fd; padding:5px;">
      <v-row style="text-align:center;">
        <v-col md='3'>
          <v-img
            :src="item.img"
            style="border-radius:75%; height:50px; width:50px; margin:auto"
            gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
          ></v-img>
        </v-col>
        <v-col md='7' class="d-flex flex-column">
          <h3>{{ item.nombre }}</h3>
          <h3>Q. {{ item.precio }}</h3>
        </v-col>
        <v-col md='2' class="d-flex flex-column">
          <v-icon style="cursor:pointer" right large color="red">fas fa-trash</v-icon>
        </v-col>
      </v-row>
      <v-row style="text-align:center; background-color:#fff;margin:2px;border-radius:15px;">
        <v-col md='4'><v-btn fab small color="amber"><v-icon small>fas fa-minus</v-icon></v-btn></v-col>
        <v-col md='4'><h2>2</h2></v-col>
        <v-col md='4'><v-btn fab small color="amber"><v-icon small>fas fa-plus</v-icon></v-btn></v-col>
      </v-row>
    </v-card>
  </v-col>
</template>

<script>

export default{
  components:{
  },
  props:{
     item:{},
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
