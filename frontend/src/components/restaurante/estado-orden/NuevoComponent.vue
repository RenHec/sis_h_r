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

              <v-select outlined dense
                v-validate="'required'"
                v-model="inicia"
                item-value="id"
                name="inicia"
                item-text="nombre"
                :items="procesos" label="Inicia proceso"></v-select>
              <form-error :attribute_name="'inicia'" :errors_form="errors"> </form-error>

              <v-select outlined dense
                v-validate="'required'"
                v-model="finaliza"
                item-value="id"
                name="finaliza"
                item-text="nombre"
                :items="procesos" label="Finaliza proceso"></v-select>
              <form-error :attribute_name="'finaliza'" :errors_form="errors"> </form-error>

              <v-text-field outlined dense name="orden" v-model="orden" v-validate="'required|numeric'" label="Orden despliegue"></v-text-field>
              <form-error :attribute_name="'orden'" :errors_form="errors"> </form-error>

              <v-text-field outlined dense name="color" v-model="color" v-validate="'required'" label="Color"></v-text-field>
              <form-error :attribute_name="'color'" :errors_form="errors"> </form-error>
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
      inicia:'',
      finaliza:'',
      orden:'',
      color:'',

      loading:false,

      procesos:[
        {'id':0,'nombre':'No'},
        {'id':1,'nombre':'Si'}
      ],
    }
  },
  mounted(){
  },
  created(){

  },
  methods:{
    closeForm(){
      events.$emit("close_form_new_order_status",true)
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
      this.inicia = '',
      this.finaliza = '',
      this.orden = '',
      this.color = ''
      this.$validator.reset()
    },

    saveRecord(){
      this.loading = true

      let data = {
          'nombre':this.nombre,
          'icono':this.icono,
          'inicia':this.inicia,
          'finaliza':this.finaliza,
          'orden':this.orden,
          'color':this.color
      }

      this.$parent.$store.state.services.statusOrderService
        .storeOrderStatus(data)
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
