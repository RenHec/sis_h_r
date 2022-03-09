<template>
  <v-row>
      <v-col md='8'>
        <v-card>
          <Categoria :categorias="categorias"/>
          <v-container style="background-color:#e3f2fd; height:85vh; overflow-y:scroll">
            <v-row>
              <template v-for="platillo in platillos">
                <Platillo v-bind:key="platillo.id" :platillo="platillo"/>
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
          <v-container style="height:85vh; overflow-y:scroll;" class="d-flex flex-column">
              <template v-for="orden in ordenes">
                <v-row v-bind:key="orden.id" >
                  <DetalleOrden :item="orden" />
                </v-row>
              </template>
            <v-card-title class="mt-5 justify-center">
              <h2>Total: Q. 30.00</h2>
            </v-card-title>
            <v-card-actions>
              <v-btn rounded block color='error' dark large>
                Generar orden
              </v-btn>
            </v-card-actions>
          </v-container>
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
       categorias:[
         {'id':1,'nombre':'Desayuno','icono':'mdi-food-variant'},
         {'id':2,'nombre':'Almuerzo','icono':'mdi mdi-pasta'},
         {'id':3,'nombre':'Cena','icono':'mdi-noodles'},
         {'id':4,'nombre':'Refacciones','icono':'mdi-food'},
         {'id':5,'nombre':'Bebidas','icono':'mdi-bottle-soda'}
       ],
       platillos:[
         {'id':11,'nombre':'Hamburguesa','precio':'15.00','img':'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/20191011-apple-cider-brined-turkey-delish-ehg-2689-1571251679.jpg?crop=1.00xw:0.716xh;0,0.0644xh&resize=768:*'},
         {'id':12,'nombre':'platillo 1','precio':'15.00','img':'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/20191011-apple-cider-brined-turkey-delish-ehg-2689-1571251679.jpg?crop=1.00xw:0.716xh;0,0.0644xh&resize=768:*'},
         {'id':13,'nombre':'platillo 1','precio':'15.00','img':'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/20191011-apple-cider-brined-turkey-delish-ehg-2689-1571251679.jpg?crop=1.00xw:0.716xh;0,0.0644xh&resize=768:*'},
         {'id':14,'nombre':'platillo 1','precio':'15.00','img':'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/20191011-apple-cider-brined-turkey-delish-ehg-2689-1571251679.jpg?crop=1.00xw:0.716xh;0,0.0644xh&resize=768:*'},
         {'id':15,'nombre':'platillo 1','precio':'15.00','img':'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/20191011-apple-cider-brined-turkey-delish-ehg-2689-1571251679.jpg?crop=1.00xw:0.716xh;0,0.0644xh&resize=768:*'},
         {'id':16,'nombre':'platillo 1','precio':'15.00','img':'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/20191011-apple-cider-brined-turkey-delish-ehg-2689-1571251679.jpg?crop=1.00xw:0.716xh;0,0.0644xh&resize=768:*'},
       ],
       ordenes:[
        {'id':11,'cantidad':1,'nombre':'Hamburguesa','precio':'15.00','img':'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/20191011-apple-cider-brined-turkey-delish-ehg-2689-1571251679.jpg?crop=1.00xw:0.716xh;0,0.0644xh&resize=768:*'},
        {'id':12,'cantidad':1,'nombre':'platillo 1','precio':'15.00','img':'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/20191011-apple-cider-brined-turkey-delish-ehg-2689-1571251679.jpg?crop=1.00xw:0.716xh;0,0.0644xh&resize=768:*'},
        {'id':13,'cantidad':1,'nombre':'Hamburguesa','precio':'15.00','img':'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/20191011-apple-cider-brined-turkey-delish-ehg-2689-1571251679.jpg?crop=1.00xw:0.716xh;0,0.0644xh&resize=768:*'},
        {'id':14,'cantidad':1,'nombre':'platillo 1','precio':'15.00','img':'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/20191011-apple-cider-brined-turkey-delish-ehg-2689-1571251679.jpg?crop=1.00xw:0.716xh;0,0.0644xh&resize=768:*'}
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
