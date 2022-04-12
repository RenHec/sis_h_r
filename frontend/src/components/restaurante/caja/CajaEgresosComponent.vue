<template>
  <v-container fluid>
    <v-card-text>
      <v-overlay :value="loading">
        <v-progress-circular indeterminate size="64"></v-progress-circular>
      </v-overlay>
      <v-form :autocomplete="'off'" ref="form" lazy-validation>

        <v-text-field outlined dense autofocus name="comprobante" v-model="comprobante" v-validate="'required'" label="Comprobante"></v-text-field>
        <form-error :attribute_name="'comprobante'" :errors_form="errors"> </form-error>

        <v-text-field outlined dense name="descripcion" v-model="descripcion" v-validate="'required'" label="Descripción"></v-text-field>
        <form-error :attribute_name="'descripcion'" :errors_form="errors"> </form-error>

        <v-text-field outlined dense name="monto" v-model="monto" v-validate="'required|decimal'" label="Monto"></v-text-field>
        <form-error :attribute_name="'monto'" :errors_form="errors"> </form-error>
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
      loading: false,

      comprobante:'',
      descripcion:'',
      monto:'',
    }
  },
  mounted(){
  },
  created(){
  },
  methods:{
    validateForm()
    {
      this.$validator.validateAll().then((result) => {
        if (result) {
          this.saveRecord()
        }
      })
    },
    saveRecord(){

      let data = {
        'comprobante':this.comprobante,
        'descripcion':this.descripcion,
        'monto':this.monto
      }

      this.loading = true

      this.$parent.$store.state.services.checkoutRestaurantService
        .storePurcharses(data)
        .then((r)=>{
          this.$toastr.success('Registro guardado con éxito','Mensaje')
          this.closeForm()
        })
        .catch((e)=>{
          this.$toastr.error(e.response.data.error, 'Error');
        })
        .finally(()=>{
          this.loading = false
        })
    },
    closeForm(){
      events.$emit("close_egresos_form",true)
    },
  },
}
</script>
