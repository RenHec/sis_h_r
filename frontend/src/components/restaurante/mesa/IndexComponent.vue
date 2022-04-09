<template>
  <v-container fluid>
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
    <NuevoComponent v-if="showFormNewRecord"/>
    <EditarComponent :item="itemUpdate" v-if="showFormUpdateRecord"/>
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
      show-select
      class="elevation-1"
      fixed-header
      v-if="showMainTable"
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
    <template v-slot:item.icono="{ item }">
      <v-icon>{{ item.icono }}</v-icon>
    </template>
    <!--  -->
    <template v-slot:top>
        <v-toolbar flat color="white">
        <v-toolbar-title>Mesas</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-text-field class="text-xs-center" outlined dense v-model="search" :label="'Buscar'" single-line hide-details v-on:keyup.enter="searching">
            <v-icon slot="append">search</v-icon>
        </v-text-field>
        <v-spacer></v-spacer>
        <v-btn
            color="primary"
            dark
            class="mb-2"
            @click="newRecord()"
        >Nuevo registro</v-btn>
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
                  <v-tooltip top v-if="unitary">
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn icon v-bind="attrs" v-on="on" @click="updateRecord(selected[0])">
                        <v-icon color="grey darken-2">edit</v-icon>
                      </v-btn>
                    </template>
                    <span>Editar</span>
                  </v-tooltip>
                  <v-icon v-if="unitary">more_vert</v-icon>
                  <v-tooltip top v-if="option">
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn icon v-bind="attrs" v-on="on"  @click="deleteRecord(selected[0])">
                        <v-icon color="grey darken-2">delete</v-icon>
                      </v-btn>
                    </template>
                    <span>Borrar</span>
                  </v-tooltip>
              </v-toolbar>
            </v-card>
        <!--  -->
    </template>
    <!--  -->
    </v-data-table>
  </v-container>
</template>

<script>
import NuevoComponent from './NuevoComponent.vue'
import EditarComponent from './EditarComponent.vue'

export default{
  components:{
    NuevoComponent,
    EditarComponent,
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
      itemUpdate:{},

      mainTable:true,
      formNewRecord:false,
      formUpdateRecord:false,

      headers:[
        { text: 'Nombre', value: 'nombre' },
        { text: 'Icono', value: 'icono', sortable:false },
        { text: 'Orden de despliegue', value: 'orden', sortable:false }
      ],
      singleSelect: true,
      selected:[],
    }
  },
  mounted(){
    this.getAllTable()
  },
  created(){
    events.$on('close_form_new_table',this.eventCloseFormNewTable)
    events.$on('close_form_update_table',this.eventCloseFormUpdateTable)
  },
  beforeDestroy(){
    events.$off('close_form_new_table')
    events.$off('close_form_update_table')
  },
  watch: {
    options: {
      handler() {
        if(this.recordList.length > 0 && this.syncronize)
        {
          this.getAllTable();
        }
      },
    },
    deep: true,
  },
  methods:{
    initializeView(){
      this.formNewRecord = false
      this.formUpdateRecord = false
      this.mainTable = true
    },
    eventCloseFormNewTable(){
      this.initializeView()
      this.recharge()
    },
    eventCloseFormUpdateTable(){
      this.initializeView()
      this.recharge()
    },
    updateRecord(item){
      this.itemUpdate = item
      this.formUpdateRecord = true
      this.mainTable = false
    },
    newRecord(){
      this.formNewRecord = true
      this.mainTable = false
    },

    searching (){
      this.syncronize = false
      this.getAllTable()
    },

    recharge() {
      this.syncronize = false
      this.getAllTable()
      this.selected = []
    },

    getAllTable() {
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

      this.$store.state.services.tableService
        .getAllTables(data)
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
    deleteRecord(item) {
      this.$swal({
        title: 'Eliminar',
        text: '¿Está seguro de realizar esta acción?',
        type: 'question',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.tableService
            .deleteTables(item)
            .then((r) => {
              this.loading = false
              if (r.response) {
                if (r.response.data.code === 404) {
                  this.$toastr.warning(r.response.data.error, 'Advertencia')
                  return
                } else if (r.response.data.code === 423) {
                  this.$toastr.warning(r.response.data.error, 'Advertencia')
                  return
                } else {
                  for (let value of Object.values(r.response.data)) {
                    this.$toastr.error(value, 'Mensaje')
                  }
                }
                return
              }

              this.$toastr.success('Registro eliminado con éxito', 'Mensaje')
              this.recharge()
            })
            .catch((e) => {
              this.$toastr.error(e,'Error')
            })
            .finally(()=>{
              this.loading = false
            })
        } else {
          this.close()
        }
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
    showMainTable(){
      return !this.formNewRecord && !this.formUpdateRecord
    },
    showFormNewRecord(){
      return !this.mainTable && !this.formUpdateRecord
    },
    showFormUpdateRecord(){
      return !this.mainTable && !this.formNewRecord
    },
  }
}
</script>
