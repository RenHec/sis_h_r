<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
  </v-row>
</template>

<script>
import FormError from './shared/FormError'
import { createNamespacedHelpers } from 'vuex'

const {
  mapActions: restaurantMapActions
} = createNamespacedHelpers('restaurant')

export default {
  name: 'Default',
  components: { FormError },
  data() {
    return {
      loading: false,
    }
  },

  created() {
    //this.initialize()
    this.verifyCashOpened()
  },

  methods: {
    ...restaurantMapActions([
      'UPDATE_CASH_OPENING',
      'UPDATE_CAJA_ID'
    ]),

    verifyCashOpened(){
      this.$store.state.services.checkoutRestaurantService
      .verifyIfExistsCashOpened()
      .then((r)=>{
        if(!r.data.data){
          this.UPDATE_CASH_OPENING(false)
        }else{
          let caja = r.data.data
  console.log(caja)
          this.UPDATE_CASH_OPENING(true)
          this.UPDATE_CAJA_ID(caja.id)
        }
      })
      .catch((e)=>{
        this.$toastr.error(e.response.data.error,'Error');
      })
    },

    initialize() {
      this.loading = true
    },
  },
}
</script>
