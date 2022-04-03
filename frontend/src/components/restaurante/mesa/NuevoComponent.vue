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

              <v-text-field outlined dense name="icono" v-model="icono" v-validate="'required'" label="Icono"></v-text-field>
              <form-error :attribute_name="'icono'" :errors_form="errors"> </form-error>

              <v-text-field outlined dense name="orden" v-model="orden" v-validate="'required'" label="Orden de despliegue"></v-text-field>
              <form-error :attribute_name="'orden'" :errors_form="errors"> </form-error>
          </v-form>
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
      icono:'',
      orden:'',

      loading:false,
    }
  },
  mounted(){
  },
  created(){

  },
  methods:{
    closeForm(){
      events.$emit("close_form_new_table",true)
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
      this.icono = '',
      this.orden = '',
      this.$validator.reset()
    },

    saveRecord(){
      this.loading = true

      let data = {
          'nombre':this.nombre,
          'icono':this.icono,
          'orden':this.orden
      }

      this.$parent.$store.state.services.tableService
        .storeTables(data)
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
    }
  },
}
</script>
