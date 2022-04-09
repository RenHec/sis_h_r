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
    <template v-slot:item.stock="{ item }">
      <v-chip color="cyan" dark>{{ item.stock }}</v-chip>
    </template>
    <!--  -->
    <template v-slot:top>
        <v-toolbar flat color="white">
        <v-toolbar-title>Inventario de productos</v-toolbar-title>
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
        { text: 'Nombre', value: 'nombre' },
        { text: 'Stock', value: 'stock', sortable:false },
        { text: 'Suministrado', value: 'suministrado', sortable:false },
        { text: 'Consumido', value: 'consumido', sortable:false },
      ],
      singleSelect: true,
      selected:[],
    }
  },
  mounted(){
    this.getAllInventory()
  },
  created(){
  },
  watch: {
    options: {
      handler() {
        if(this.recordList.length > 0 && this.syncronize)
        {
          this.getAllInventory();
        }
      },
    },
    deep: true,
  },
  methods:{
    searching (){
      this.syncronize = false
      this.getAllInventory()
    },

    recharge() {
      this.syncronize = false
      this.getAllInventory()
      this.selected = []
    },

    getAllInventory() {
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

      this.$store.state.services.inventoryRestaurantService
        .getAllInventory(data)
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
  },
  computed:{
    option(){
      return this.selected.length > 0 ? true : false
    },
    unitary(){
       return (this.selected.length > 0  && this.selected.length < 2) ? true : false
    },
  }
}
</script>
