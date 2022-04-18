<template>
  <v-container fluid>
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
    <v-col md="12" sm="12">
      <v-dialog persistent v-model="dialog" max-width="80%">
        <v-card>
          <v-toolbar>
            <v-toolbar-title>Detalle de gastos</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon @click="closeCustomerForm()">
              <v-icon dark>close</v-icon>
            </v-btn>
          </v-toolbar>
          <GastoComponent v-if="dialog" :items="detailItem" />
        </v-card>
      </v-dialog>
    </v-col>
    <v-data-table
      :page="page"
      :options.sync="options"
      :server-items-length="totalRecords"
      :footer-props="{
        'items-per-page-options': [5, 10, 15, 20],
        'items-per-page-text': 'Registros por página',
        'page-text': `{0} - {1} de {2}`,
      }"
      :headers="headers"
      :items="recordList"
      :single-select="singleSelect"
      v-model="selected"
      class="elevation-1"
      fixed-header
    >
      <template v-slot:no-data>
        No se encontraron registros.
      </template>
      <template v-slot:no-results>
        No se encontraron registros.
      </template>
      <template v-slot:item.id="{ item }">
        <v-btn @click="detailRecord(item.detalle)" class="success" small>
          Detalle
        </v-btn>
      </template>
      <template v-slot:item.saldo_inicial="{ item }">
        <span>Q. {{ item.saldo_inicial }}</span>
      </template>
      <template v-slot:item.ingresos="{ item }">
        <span>Q. {{ item.ingresos }}</span>
      </template>
      <template v-slot:item.egresos="{ item }">
        <span>Q. {{ item.egresos }}</span>
      </template>
      <!--  -->
      <template v-slot:top>
        <v-toolbar flat color="white">
          <v-toolbar-title>Reportes de gastos por caja</v-toolbar-title>
        </v-toolbar>
        <!--  -->
        <v-card
          color="grey lighten-4"
          flat
          height="auto"
          tile
          style="margin-left: 10px; margin-right: 10px;"
        >
          <v-toolbar dense>
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-btn icon v-bind="attrs" v-on="on" @click="recharge">
                  <v-icon color="grey darken-2">replay</v-icon>
                </v-btn>
              </template>
              <span>Recargar</span>
            </v-tooltip>
            <v-icon>more_vert</v-icon>
          </v-toolbar>
        </v-card>
        <!--  -->
      </template>
      <!--  -->
    </v-data-table>
  </v-container>
</template>

<script>
import GastoComponent from './GastosDetalleComponent.vue'
export default {
  components: {
    GastoComponent,
  },
  data() {
    return {
      loading: false,
      totalRecords: 0,
      options: {},
      syncronize: true,
      selected: [],
      page: 1,

      search: '',
      recordList: [],
      detailItem: [],

      headers: [
        { text: 'Saldo inicial', value: 'saldo_inicial' },
        { text: 'Ingresos', value: 'ingresos', sortable: false },
        { text: 'Egresos', value: 'egresos', sortable: false },
        { text: 'Fecha', value: 'fecha_apertura', sortable: false },
        { text: 'Gastos', value: 'id', sortable: false },
      ],
      singleSelect: true,
      selected: [],

      dialog: false,
    }
  },
  mounted() {
    this.getAllPurchases()
  },
  created() {},
  watch: {
    options: {
      handler() {
        if (this.recordList.length > 0 && this.syncronize) {
          this.getAllPurchases()
        }
      },
    },
    deep: true,
  },
  methods: {
    searching() {
      this.syncronize = false
      this.getAllPurchases()
    },

    recharge() {
      this.syncronize = false
      this.getAllPurchases()
      this.selected = []
    },
    closeCustomerForm() {
      this.dialog = false
      this.detailItem = []
    },
    detailRecord(item) {
      this.detailItem = item
      this.dialog = true
    },
    getAllPurchases() {
      this.loading = true

      const { sortBy, sortDesc, page, itemsPerPage } = this.options

      let pageNumber = page - 1

      let data = {
        perPage: itemsPerPage,
        page: pageNumber * itemsPerPage,
        sortBy: sortBy,
        sortDesc: sortDesc,
        search: this.search,
      }

      this.$store.state.services.checkoutRestaurantService
        .getListPurchases(data)
        .then((r) => {
          this.recordList = r.data.data
          this.syncronize = true
          this.totalRecords = r.data.total
        })
        .catch((e) => {
          this.$toastr.error(e, 'Error')
        })
        .finally(() => {
          this.loading = false
        })
    },
  },
  computed: {
    option() {
      return this.selected.length > 0 ? true : false
    },
    unitary() {
      return this.selected.length > 0 && this.selected.length < 2 ? true : false
    },
  },
}
</script>
