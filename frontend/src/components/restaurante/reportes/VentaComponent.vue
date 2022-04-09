<template>
  <v-container fluid>
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
    <v-data-table
      :page="page"
      :options.sync="options"
      :server-items-length="totalRecords"
      :footer-props="{
        'items-per-page-options': [5, 10, 15, 20],
        'items-per-page-text':'Registros por página',
        'page-text':`{0} - {1} de {2}`
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
      <span>{{ item.id }}</span>
    </template>
    <template v-slot:item.monto="{ item }">
      <span>Q. {{ item.monto }}</span>
    </template>
    <template v-slot:item.id="{ item }">
        <v-btn
          primary
          small
          dark
          color="green"
          @click="printerInvoice(item.id)"
        >
          <v-icon>print</v-icon>
          Imprimir
        </v-btn>
      </template>
    <!--  -->
    <template v-slot:top>
        <v-toolbar flat color="white">
        <v-toolbar-title>Listado de ventas realizadas</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-text-field class="text-xs-center" outlined dense v-model="search" :label="'Buscar'" single-line hide-details v-on:keyup.enter="searching">
            <v-icon slot="append">search</v-icon>
        </v-text-field>
        <v-spacer></v-spacer>
        </v-toolbar>
        <!--  -->
            <v-card
                color="grey lighten-4"
                flat
                height="auto"
                tile
                style="margin-left: 10px;margin-right: 10px;"
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
import moment from 'moment'

export default{
  components:{

  },
  data(){
    return{
      loading: false,
      totalRecords : 0,
      options: {},
      syncronize: true,
      selected: [],
      page: 1,

      search:'',
      recordList:[],

      headers:[
        { text: 'Cliente', value: 'nombre' },
        { text: 'Monto', value: 'monto', sortable:false },
        { text: 'Tipo orden', value: 'tipo_orden', sortable:false },
        { text: 'Fecha', value: 'fecha', sortable:false },
        { text: 'Comprobante', value: 'id', sortable: false },
      ],
      singleSelect: true,
      selected:[],
    }
  },
  mounted(){
    this.getAllFoodCategory()
  },
  created(){

  },
  watch: {
    options: {
      handler() {
        if(this.recordList.length > 0 && this.syncronize)
        {
          this.getAllFoodCategory();
        }
      },
    },
    deep: true,
  },
  methods:{
    initializeView(){

    },
    searching (){
      this.syncronize = false
      this.getAllFoodCategory()
    },

    recharge() {
      this.syncronize = false
      this.getAllFoodCategory()
      this.selected = []
    },
    printerInvoice(saleId){
      this.loading = true

      this.$parent.$store.state.services.invoiceRestaurantService
        .getInvoice(saleId)
        .then((r) =>{
          const blob = new Blob([r.data], {type: r.data.type});
          const url = window.URL.createObjectURL(blob);
          const link = document.createElement('a');
          link.href = url;
          let fileName = moment().format('MMDDYYYY_h:mm:ss')+'.pdf';
          link.setAttribute('download', fileName);
          document.body.appendChild(link);
          link.click();
          link.remove();
          window.URL.revokeObjectURL(url);
        })
        .catch((e)=>{
          this.$toastr.error(e,'Error')
        })
        .finally(()=>{
          this.loading = false
        })
    },
    getAllFoodCategory() {
      this.loading = true

      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let pageNumber = page - 1;

      let data = {
          'perPage':itemsPerPage,
          'page':pageNumber * itemsPerPage,
          'sortBy':sortBy,
          'sortDesc':sortDesc,
          'search':this.search
      }

      this.$store.state.services.salesRestaurantService
        .getListOfSales(data)
        .then((r) => {
          this.recordList = r.data.data
          this.syncronize = true
          this.totalRecords = r.data.total
        })
        .catch((e) => {
          this.$toastr.error(e, 'Error')
        })
        .finally(()=>{
          this.loading = false
        })
    },
    getTextTitle (item){
      if (item === 1) return 'Si'
        else return 'No'
    },

  },
  computed:{
    option(){
      return this.selected.length > 0 ? true : false
    },
    unitary(){
       return (this.selected.length > 0  && this.selected.length < 2) ? true : false
    }
  }
}
</script>
