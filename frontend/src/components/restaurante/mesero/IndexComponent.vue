<template>
  <div>
    <Mesero v-if="isTableSelected" />
    <v-row>
      <v-overlay :value="loading">
        <v-progress-circular indeterminate size="64"></v-progress-circular>
      </v-overlay>
      <v-col cols="12" md="12" sm="12" v-if="!isTableSelected">
        <v-card>
          <v-toolbar>
            <v-toolbar-title>Seleccionar mesa</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click="recharge()">
              <v-icon>replay</v-icon>
            </v-btn>
          </v-toolbar>
          <div
            style="background-color: #e3f2fd; height: 85vh; overflow-y: scroll;"
          >
            <v-row>
              <template v-for="item in items">
                <v-col v-bind:key="item.id" lg="3" md="4" sm="6" xs="12">
                  <v-card :color="verifyIfBusy(item.id)" class="mt-3 mx-2 eicon-table eicon-2x d-flex justify-center card-table-restaurant" @click="checkTable(item)">
                    <span class="text-table-restaurant">{{ item.nombre }}</span>
                  </v-card>
                </v-col>
              </template>
            </v-row>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import Mesero from './MeseroComponent.vue'
import { createNamespacedHelpers } from 'vuex'

const {
  mapGetters: restaurantMapGetter,
  mapMutations: restaurantMapMutations,
  mapActions: restaurantMapActions,
} = createNamespacedHelpers('restaurant')

export default {
  components: {
    Mesero,
  },
  data() {
    return {
      loading: false,
      items: [],

      tableBusy:[],
    }
  },
  mounted() {
    this.getData()
    this.SET_LIST_ITEMS([])
  },
  created() {},
  beforeDestroy() {
    this.UPDATE_SELECTED_TABLE({ id: 0, name: '', selected: false })
  },
  methods: {
    ...restaurantMapActions(['UPDATE_SELECTED_TABLE']),
    ...restaurantMapMutations(['SET_LIST_ITEMS']),
    checkTable(item) {
      this.UPDATE_SELECTED_TABLE({
        id: item.id,
        name: item.nombre,
        selected: true,
      })
    },
    recharge(){
      this.getData()
    },
    getData(){
      this.loading = true

      Promise.all([this.getListTables(), this.getTableIsBusy()])
        .then((r)=>{})
        .catch((e)=>{
          this.$toastr.error(e.response.data.error,'Error');
        })
        .finally(()=>{
          this.loading = false
        })
    },
    verifyIfBusy(mesaId){
      let busy = ''

      for (let index = 0; index < this.tableBusy.length; index++) {
        if(this.tableBusy[index].id === mesaId)
        {
          busy = 'pink lighten-4'
          break
        }
      }

      return busy
    },
    getTableIsBusy(){
      return new Promise((resolve,reject)=>{
        this.$store.state.services.tableService
        .getListTablesIsBusy()
        .then((r) => {
          this.tableBusy = r.data.data
          resolve()
        })
        .catch((e) => {
          reject(e)
        })
      })
    },
    getListTables() {

      return new Promise((resolve, reject)=>{
        this.$store.state.services.tableService
        .getListTables()
        .then((r) => {
          this.items = r.data.data
          resolve()
        })
        .catch((e) => {
          reject(e)
        })
      })
    },
  },
  computed: {
    ...restaurantMapGetter(['selectedTable', 'isTableSelected']),
  },
}
</script>
