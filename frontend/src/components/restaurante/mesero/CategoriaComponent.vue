<template>
  <v-toolbar>
    <v-sheet
      class="mx-auto"
      max-width="100%"
    >
      <v-slide-group show-arrows>
        <v-slide-item
          v-for="categoria in mycategorias"
          :key="categoria.id"
          v-slot="{ active, toggle }"
        >
          <v-btn
            class="mx-2"
            :input-value="active"
            :color="active ? 'red darken-1' : undefined"
            depressed
            rounded
            outlined
            @click="toggle"
          >
            <v-icon>{{ categoria.icono }}</v-icon>&nbsp;{{ categoria.nombre }}
          </v-btn>
        </v-slide-item>
      </v-slide-group>
    </v-sheet>
  </v-toolbar>
</template>

<script>

export default{
  components:{
  },
  props:{
    categorias:[]
  },
  data(){
    return{
      mycategoria:[]
    }
  },
  mounted(){

  },
  created(){
    this.mycategorias = this.categorias
  },
  methods:{
  },
}
</script>
