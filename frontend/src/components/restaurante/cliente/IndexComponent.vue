<template>
  <v-container fluid>
    <v-card-text>
      <v-form :autocomplete="'off'" ref="form" lazy-validation>

        <v-text-field outlined dense autofocus name="nit" v-model="nit" v-validate="'required'" label="NIT"></v-text-field>
        <form-error :attribute_name="'nit'" :errors_form="errors"> </form-error>

        <v-text-field outlined dense autofocus name="nombre" v-model="nombre" v-validate="'required'" label="Nombre"></v-text-field>
        <form-error :attribute_name="'nombre'" :errors_form="errors"> </form-error>

        <v-text-field outlined dense name="telefono" v-model="telefono" v-validate="'numeric'" label="Teléfono"></v-text-field>
        <form-error :attribute_name="'telefono'" :errors_form="errors"> </form-error>

        <v-text-field outlined dense name="correo" v-model="correo" v-validate="'email'" label="Correo electrónico"></v-text-field>
        <form-error :attribute_name="'correo'" :errors_form="errors"> </form-error>

        <v-select outlined dense
          v-model="departamento"
          item-value="id"
          name="departamento"
          item-text="nombre"
          @change="getMunicipios"
          :items="departamentos" label="Departamento" v-validate="'required'"></v-select>
        <form-error :attribute_name="'departamento'" :errors_form="errors"> </form-error>

        <v-select outlined dense
          v-model="municipio"
          item-value="id"
          name="municipio"
          item-text="nombre"
          :items="municipios" label="Municipio" v-validate="'required'"></v-select>
        <form-error :attribute_name="'municipio'" :errors_form="errors"> </form-error>

        <v-text-field outlined name="direccion" v-model="direccion" v-validate="'required'" label="Dirección"></v-text-field>
        <form-error :attribute_name="'direccion'" :errors_form="errors"> </form-error>
    </v-form>
    </v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="primary" @click="validateForm()">
        Guardar
      </v-btn>
    </v-card-actions>
  </v-container>
</template>

<script>
import FormError from '../../shared/FormError.vue'

export default{
  components:{
    FormError,
  },
  data(){
    return{
      loading:false,

      nit:'',
      nombre:'',
      telefono:'',
      correo:'',
      direccion:'',

      departamentos:[],
      departamento:'',

      municipios:[],
      municipio:''
    }
  },
  mounted(){
    this.getDepartamentos()
  },
  created(){

  },
  methods:{
    getMunicipios(){
      let depto = this.departamento

      this.$parent.$store.state.services.municipioService
          .getListMunicipios(depto)
          .then((r)=>{
            this.municipios = r.data.data
          })
          .catch((e)=>{
            this.$toastr.error(e,'Error')
          })
    },
    getDepartamentos(){
      this.$parent.$store.state.services.departamentoService
          .getListDepartamentos()
          .then((r)=>{
            this.departamentos = r.data.data
          })
          .catch((e)=>{
            this.$toastr.error(e,'Error')
          })
    },
    closeForm(){
      events.$emit("close_customer_form",true)
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
      this.nit ='',
      this.nombre ='',
      this.telefono ='',
      this.correo ='',
      this.direccion ='',
      this.departamento ='',
      this.municipios = [],
      this.municipio =''
    },

    saveRecord(){
      this.loading = true

      let data = {
          'nit':this.nit,
          'nombre':this.nombre,
          'telefono':this.telefono,
          'correo':this.correo,
          'direccion':this.direccion,
          'departamento':this.departamento,
          'municipio':this.municipio
      }

      this.$parent.$store.state.services.customerService
        .storeCustomer(data)
        .then((r) => {
          this.$toastr.success('Registro guardado con éxito', 'Mensaje')
          this.intializeFields()
          this.closeForm()
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
