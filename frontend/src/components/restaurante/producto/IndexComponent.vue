<template>
  <v-container fluid>
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
    <NuevoComponent v-if="showFormNewRecord" />
    <EditarComponent :item="itemUpdate" v-if="showFormUpdateRecord" />
    <InventarioComponent :item="itemUpdate" v-if="showFormInventoryProduct" />
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
      <template v-slot:item.img="{ item }">
        <v-avatar>
          <img :src="getAbsoluteImagePath(item.img)" alt="Food" />
        </v-avatar>
      </template>
      <template v-slot:item.inventario="{ item }">
        <v-btn
          v-if="item.inventario == 1"
          primary
          small
          dark
          color="green"
          @click="addInventory(item)"
        >
          <v-icon>add</v-icon>
          Inventario
        </v-btn>
      </template>
      <template v-slot:item.precio="{ item }">
        <div class="subtitle-1">
          {{ formato_moneda(1, item.precio, 0) }}
        </div>
      </template>
      <template v-slot:item.costo="{ item }">
        <div class="subtitle-1">
          {{ formato_moneda(1, item.costo, 0) }}
        </div>
      </template>
      <!--  -->
      <template v-slot:top>
        <v-toolbar flat color="white">
          <v-toolbar-title>Productos disponibles</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-text-field
            class="text-xs-center"
            outlined
            dense
            v-model="search"
            :label="'Buscar'"
            single-line
            hide-details
            v-on:keyup.enter="searching"
          >
            <v-icon slot="append">search</v-icon>
          </v-text-field>
          <v-spacer></v-spacer>
          <v-btn color="primary" dark class="mb-2" @click="newRecord()">
            Nuevo registro
          </v-btn>
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
            <v-tooltip top v-if="unitary">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  icon
                  v-bind="attrs"
                  v-on="on"
                  @click="updateRecord(selected[0])"
                >
                  <v-icon color="grey darken-2">edit</v-icon>
                </v-btn>
              </template>
              <span>Editar</span>
            </v-tooltip>
            <v-icon v-if="unitary">more_vert</v-icon>
            <v-tooltip top v-if="option">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  icon
                  v-bind="attrs"
                  v-on="on"
                  @click="deleteRecord(selected[0])"
                >
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
import InventarioComponent from './InventarioComponent.vue'

export default {
  components: {
    NuevoComponent,
    EditarComponent,
    InventarioComponent,
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
      itemUpdate: {},

      mainTable: true,
      formNewRecord: false,
      formUpdateRecord: false,
      formInventoryProduct: false,

      headers: [
        { text: 'Id', value: 'id' },
        { text: 'Nombre', value: 'nombre' },
        { text: 'Precio', value: 'precio', sortable: false },
        { text: 'Costo', value: 'costo', sortable: false },
        { text: 'Imagen', value: 'img', sortable: false },
        {
          text: 'Inventario',
          value: 'inventario',
          sortable: false,
        },
      ],
      singleSelect: true,
      selected: [],
    }
  },
  mounted() {
    this.getAllProducts()
  },
  created() {
    events.$on('close_form_new_product', this.eventCloseFormNewProducts)
    events.$on('close_form_update_product', this.eventCloseFormUpdateProducts)
    events.$on(
      'close_form_inventory_product',
      this.eventCloseFormInventoryProducts,
    )
  },
  beforeDestroy() {
    events.$off('close_form_new_product')
    events.$off('close_form_update_product')
    events.$off('close_form_inventory_product')
  },
  watch: {
    options: {
      handler() {
        if (this.recordList.length > 0 && this.syncronize) {
          this.getAllProducts()
        }
      },
    },
    deep: true,
  },
  methods: {
    addInventory(item) {
      this.itemUpdate = item
      this.formUpdateRecord = false
      this.formNewRecord = false
      this.formInventoryProduct = true
      this.mainTable = false
    },
    getAbsoluteImagePath(item) {
      return this.$store.state.services.productService.domainUrl + item
    },
    initializeView() {
      this.formNewRecord = false
      this.formUpdateRecord = false
      this.formInventoryProduct = false
      this.mainTable = true
    },
    eventCloseFormNewProducts() {
      this.initializeView()
      this.recharge()
    },
    eventCloseFormUpdateProducts() {
      this.initializeView()
      this.recharge()
    },
    eventCloseFormInventoryProducts() {
      this.initializeView()
      this.recharge()
    },
    updateRecord(item) {
      this.itemUpdate = item
      this.formUpdateRecord = true
      this.formInventoryProduct = false
      this.mainTable = false
    },
    newRecord() {
      this.formNewRecord = true
      this.formInventoryProduct = false
      this.mainTable = false
    },

    searching() {
      this.syncronize = false
      this.getAllProducts()
    },

    recharge() {
      this.options.page = 0
      this.syncronize = false
      this.getAllProducts()
      this.selected = []
    },

    getAllProducts() {
      this.loading = true

      const { sortBy, sortDesc, page, itemsPerPage } = this.options

      let pageNumber = page - 1

      let data = {
        perPage: itemsPerPage,
        page: pageNumber + itemsPerPage,
        sortBy: sortBy,
        sortDesc: sortDesc,
        search: this.search,
      }

      this.$store.state.services.productService
        .getAllProducts(data)
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
    getTextTitle(item) {
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
          this.$store.state.services.productService
            .deleteProducts(item)
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
              this.$toastr.error(e, 'Error')
            })
            .finally(() => {
              this.loading = false
            })
        } else {
          this.close()
        }
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
    showMainTable() {
      return (
        !this.formNewRecord &&
        !this.formUpdateRecord &&
        !this.formInventoryProduct
      )
    },
    showFormNewRecord() {
      return (
        !this.mainTable && !this.formUpdateRecord && !this.formInventoryProduct
      )
    },
    showFormUpdateRecord() {
      return (
        !this.mainTable && !this.formNewRecord && !this.formInventoryProduct
      )
    },
    showFormInventoryProduct() {
      return !this.mainTable && !this.formNewRecord && !this.formUpdateRecord
    },
  },
}
</script>
