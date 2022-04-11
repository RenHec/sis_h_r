<template>
  <v-toolbar>
    <v-sheet
      class="mx-auto"
      max-width="100%"
    >
      <v-slide-group show-arrows>
        <v-slide-item
          v-for="category in categories"
          :key="category.id"
          v-slot="{ active, toggle }"
        >
          <v-btn
            class="mx-2"
            :input-value="active"
            :color="active ? 'red darken-1' : undefined"
            depressed
            rounded
            outlined
            @click="categoryFilter(category.id)"
          >
            {{ category.nombre }}
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
    categories:{}
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
    categoryFilter(item)
    {
      events.$emit("filter_menus",item)
    }
  },
}
</script>
