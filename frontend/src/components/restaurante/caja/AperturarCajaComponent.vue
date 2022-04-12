<template>
  <v-row class="justify-center mt-5">
    <v-col md='5' sm='12'>
      <v-card>
        <v-overlay :value="loading">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-toolbar class="d-flex justify-center">
          <v-toolbar-title><v-icon color="green" large>local_atm</v-icon> Aperturar Caja</v-toolbar-title>
        </v-toolbar>
        <v-container fluid>
          <v-card-text>
            <v-form :autocomplete="'off'" ref="form" lazy-validation>
              <v-text-field outlined dense autofocus name="saldo" v-model="saldo" v-validate="'required|decimal'" label="Saldo inicial"></v-text-field>
              <form-error :attribute_name="'saldo'" :errors_form="errors"> </form-error>
          </v-form>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn rounded block color="primary" class="float: bottom" @click="validateForm()">
              Aperturar caja
            </v-btn>
          </v-card-actions>
        </v-container>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import moment from 'moment'
import FormError from '../../shared/FormError.vue'
import { createNamespacedHelpers } from 'vuex'

const {
  mapGetters: restaurantMapGetter,
  mapActions: restaurantMapActions
} = createNamespacedHelpers('restaurant')

export default{
  components:{
    FormError
  },
  data(){
    return{
      loading: false,

      saldo:'',
    }
  },
  mounted(){

  },
  created(){
  },
  methods:{
    ...restaurantMapActions([
      'UPDATE_CASH_OPENING',
      'UPDATE_CAJA_ID'
    ]),
    validateForm(){
      this.$validator.validateAll().then((result) => {
        if (result) {
          this.openCashRestaurant()
        }
      })
    },
    getFormaHour(){
      return moment().format('h:mm:ss a')
    },
    getFormaDate(){
      return moment().format('yyyy-MM-DD')
    },
    openCashRestaurant(){
      this.loading = true

      let data = {
        'fecha':this.getFormaDate(),
        'hora':this.getFormaHour(),
        'saldo':this.saldo
      }

      this.$store.state.services.checkoutRestaurantService
        .openCash(data)
        .then((r)=>{
          this.$toastr.success('La caja se aperturó correctamente','Mensaje')
          this.UPDATE_CAJA_ID(r.data.id)
          this.UPDATE_CASH_OPENING(true)
        })
        .catch((e)=>{
          this.$toastr.error(e,'Error')
        })
        .finally(()=>{
          this.loading = false
        })
    },
  },
}
</script>
