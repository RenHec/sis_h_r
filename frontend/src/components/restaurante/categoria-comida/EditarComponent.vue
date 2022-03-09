<template>
  <v-row class="justify-center">
    <v-col md='6' sm='12'>
      <v-card>
        <v-overlay :value="loading">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-toolbar>
          <v-toolbar-title>Editar registro</v-toolbar-title>
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

              <v-text-field outlined dense name="icono" v-model="icono" v-validate="'required'" label="Icono"></v-text-field>
              <form-error :attribute_name="'icono'" :errors_form="errors"> </form-error>

              <v-text-field outlined dense name="orden" v-model="orden" v-validate="'required|numeric'" label="Orden despliegue"></v-text-field>
              <form-error :attribute_name="'orden'" :errors_form="errors"> </form-error>
          </v-form>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="green" dark @click="validateForm()">
              Actualizar
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
      id:'',
      nombre:'',
      icono:'',
      orden:'',

      loading:false,
    }
  },
  mounted(){
    this.initializeModel()
  },
  created(){

  },
  beforeDestroy(){
    this.item = {}
  },
  props:{
    item:{}
  },
  methods:{
    initializeModel(){
      this.id = this.item.id
      this.nombre = this.item.nombre
      this.icono = this.item.icono
      this.orden = this.item.orden
    },
    closeForm(){
      events.$emit("close_form_update_food_category",true)
    },
    validateForm()
    {
      this.$validator.validateAll().then((result) => {
        if (result) {
          this.updateRecord()
        }
      })
    },

    updateRecord(){
      this.loading = true

      let data = {
          'id':this.id,
          'nombre':this.nombre,
          'icono':this.icono,
          'orden':this.orden,
      }

      this.$parent.$store.state.services.foodCategoryService
        .updateFoodCategory(data)
        .then((r) => {
          this.$toastr.success('Registro actualizado con éxito', 'Mensaje')
        })
        .catch((e) => {
          this.$toastr.error(e, 'Error')
        })
        .finally(()=>{
          this.loading = false
        })
    }
  },
}
</script>
