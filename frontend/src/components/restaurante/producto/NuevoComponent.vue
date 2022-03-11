<template>
  <v-row class="justify-center">
    <v-col md='6' sm='12'>
      <v-card>
        <v-overlay :value="loading">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-toolbar>
          <v-toolbar-title>Nuevo registro</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn icon @click="closeForm()">
            <v-icon dark>close</v-icon>
          </v-btn>
        </v-toolbar>
        <v-container fluid>
          <v-card-text>
            <v-form :autocomplete="'off'" ref="form" lazy-validation>
              <v-text-field outlined dense autofocus name="nombre" v-model="nombre" v-validate="'required'" label="Nombre"></v-text-field>
              <form-error :attribute_name="'nombre'" :errors_form="errors"> </form-error>

              <v-text-field outlined dense name="precio" v-model="precio" v-validate="'required|decimal'" label="Precio"></v-text-field>
              <form-error :attribute_name="'precio'" :errors_form="errors"> </form-error>

              <v-file-input @change="loadImage" accept="image/*" type="file" outlined dense name="imagen" v-model="imagen" v-validate="'required'" label="Imagen"></v-file-input>
              <form-error :attribute_name="'imagen'" :errors_form="errors"> </form-error>
          </v-form>
          <v-card-title>Categorías</v-card-title>
          <v-list-item-group dense class="py-0">
            <v-list-item v-for="(item,i) in foodCategoriesList" :key="i" style="padding: 1 16px" dense>
                <v-list-item-content style="padding: 1px">
                    <v-checkbox style="margin-top:1px;"
                            v-model="opc"
                            :value="item.id"
                            :label="item.nombre">
                        </v-checkbox>
                </v-list-item-content>
            </v-list-item>
        </v-list-item-group>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" @click="validateForm()">
              Guardar
            </v-btn>
          </v-card-actions>
        </v-container>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import FormError from '../../shared/FormError.vue'

export default{
  components:{
    FormError,
  },
  data(){
    return{
      nombre:'',
      precio:'',
      imagen:null,
      opc:[],

      loading:false,
      foodCategoriesList:[],
      imageAccept: ['image/png', 'image/jpeg', 'image/jpg'],
    }
  },
  mounted(){
    this.getListFoodCategories()
  },
  created(){

  },
  methods:{
    getListFoodCategories(){
      this.loading = true

      this.$store.state.services.foodCategoryService
        .getListFoodCategory()
        .then((r)=>{
          this.foodCategoriesList = r.data.data
        })
        .catch((e) =>{
          this.$toastr.error(e,'Error')
        })
        .finally(()=>{
          this.loading = false
        })
    },
    closeForm(){
      events.$emit("close_form_new_product",true)
    },
    validateForm()
    {
      this.$validator.validateAll().then((result) => {
        if (result) {
          this.saveRecord()
        }
      })
    },
    intializeFields(){
      this.nombre = '',
      this.precio = '',
      this.imagen = null,
      this.opc = [],
      this.$validator.reset()
    },

    saveRecord(){
      this.loading = true

      let data = new FormData()
      data.append('nombre',this.nombre)
      data.append('precio',this.precio)
      data.append('imagen',this.imagen,this.imagen.name)
      this.opc.forEach((item)=>{
        data.append('categorias[]',item)
      })

      this.$parent.$store.state.services.productService
        .storeProducts(data)
        .then((r) => {
          this.$toastr.success('Registro guardado con éxito', 'Mensaje')
          this.intializeFields()
        })
        .catch((e) => {
          this.$toastr.error(e, 'Error')
        })
        .finally(()=>{
          this.loading = false
        })
    },
    loadImage(e) {
      if(typeof e === 'undefined'){
        this.imagen = null
        return
      }

      if (!this.imageAccept.includes(e.type.toLowerCase())) {
        this.imagen = null
        this.$toastr.info('El archivo debe ser una imagen','Mensaje')
        return
      }
    },
  },
}
</script>
