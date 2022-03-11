<template>
  <v-row>
      <v-overlay :value="loading">
        <v-progress-circular indeterminate size="64"></v-progress-circular>
      </v-overlay>
      <v-col md='8'>
        <v-card>
          <Categoria :categories="categories"/>
          <v-container style="background-color:#e3f2fd; height:85vh; overflow-y:scroll">
            <v-row>
              <template v-for="menu in menus">
                <Platillo v-bind:key="menu.id" :platillo="menu"/>
              </template>
            </v-row>
          </v-container>
        </v-card>
      </v-col>

      <v-col md='4'>
        <v-card>
          <v-toolbar>
            <v-toolbar-title style="padding:2px;" v-text="'Detalle de la orden'"></v-toolbar-title>
          </v-toolbar>
          <DetalleOrden />
        </v-card>
      </v-col>
    </v-row>
</template>

<script>
import Categoria from './CategoriaComponent.vue'
import Platillo from './PlatilloComponent.vue'
import DetalleOrden from './DetalleOrden.vue'

export default{
  components:{
    Categoria,
    Platillo,
    DetalleOrden
  },
  data(){
    return{
      loading:false,

       categories:[],
       menus:[],
       auxiliaryMenu:[],
       ordenes:[
        {'id':11,'cantidad':1,'nombre':'Hamburguesa','precio':'15.00','img':'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/20191011-apple-cider-brined-turkey-delish-ehg-2689-1571251679.jpg?crop=1.00xw:0.716xh;0,0.0644xh&resize=768:*'},
        {'id':12,'cantidad':1,'nombre':'platillo 1','precio':'15.00','img':'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/20191011-apple-cider-brined-turkey-delish-ehg-2689-1571251679.jpg?crop=1.00xw:0.716xh;0,0.0644xh&resize=768:*'},
        {'id':13,'cantidad':1,'nombre':'Hamburguesa','precio':'15.00','img':'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/20191011-apple-cider-brined-turkey-delish-ehg-2689-1571251679.jpg?crop=1.00xw:0.716xh;0,0.0644xh&resize=768:*'},
        {'id':14,'cantidad':1,'nombre':'platillo 1','precio':'15.00','img':'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/20191011-apple-cider-brined-turkey-delish-ehg-2689-1571251679.jpg?crop=1.00xw:0.716xh;0,0.0644xh&resize=768:*'}
       ],
    }
  },
  mounted(){
    this.getAllFoodCategories()
    this.getAllMenu()
  },
  created(){
    events.$on('filter_menus',this.eventFilterMenusList)
  },
  beforeDestroy(){
    events.$off('filter_menus')
  },
  methods:{
    eventFilterMenusList(categoryFilter)
    {
      this.menus = []
      this.auxiliaryMenu.forEach((item)=>{
        if(this.isMenuCategory(item.producto_categoria_comida, categoryFilter)){
          this.menus.push(item)
        }
      })
    },

    isMenuCategory(item, filter){
      let isEqualCategory = false

      for(let category of item){
        if(category.categoria_comida_id === filter){
          isEqualCategory = true
          break;
        }
      }

      return isEqualCategory
    },
    getAllFoodCategories(){
      this.loading = true

      this.$store.state.services.foodCategoryService
        .getListFoodCategory()
        .then((r)=>{
          this.categories = r.data.data
        })
        .catch((e)=>{
          this.$toastr.error(e,'Error')
        })
        .finally(()=>{
          this.loading = false
        })
    },
    getAllMenu(){
      this.loading = true

      this.$store.state.services.productService
        .getProductsList()
        .then((r)=>{
          this.menus = r.data.data
          this.auxiliaryMenu = r.data.data
        })
        .catch((e) => {
          this.$toastr.error(e,'Error')
        })
        .finally(()=>{
          this.loading = false
        })
    },
  },
}
</script>
