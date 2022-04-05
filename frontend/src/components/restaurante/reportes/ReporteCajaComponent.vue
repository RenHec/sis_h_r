<template>
  <v-row class="justify-center mt-5">
    <v-col md='5' sm='12'>
      <v-card>
        <v-overlay :value="loading">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-toolbar class="d-flex justify-center">
          <v-toolbar-title><v-icon color="green" large>local_atm</v-icon> Total ventas del {{ getFormaDate() }}</v-toolbar-title>
        </v-toolbar>
        <template v-for="(fila, key) in reporte">
            <div v-bind:key="key">
              <div class="d-flex justify-space-between">
                <v-card-title>{{ fila.forma }}</v-card-title>
                <v-card-title>Q. {{ fila.monto}}</v-card-title>
              </div>
            </div>
        </template>
        <div class="d-flex justify-space-between">
          <v-card-title><h3 class="text-danger">TOTAL</h3></v-card-title>
          <v-card-title><h3 class="text-danger">Q. {{ getTotalAmount(reporte) }}</h3></v-card-title>
        </div>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import moment from 'moment'

export default{
  components:{
  },
  data(){
    return{
      loading: false,

      valorFechaInicio:null,

      reporte:[],
    }
  },
  mounted(){
    this.setDate()
    this.getCheckoutReport()
  },
  created(){
  },
  methods:{
    getFormaDate(){
      return moment().format('DD-MM-yyyy')
    },
    getTotalAmount(data){
      let total = 0

      data.forEach(item => {
        total += parseFloat(item.monto)
      });

      return parseFloat(total).toFixed(2)
    },
    setDate(){
      this.valorFechaInicio = moment().format('YYYY-MM-DD')
      this.valorFechaFin = moment().format('YYYY-MM-DD')
    },
    getCheckoutReport(){
      this.loading = true

      let data = {
        'desde':this.valorFechaInicio,
        'hasta':this.valorFechaFin
      }

      this.$store.state.services.checkoutRestaurantService
        .getSalesAmountReportByDate(data)
        .then((r)=>{
          this.reporte = r.data.data
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
