<template>
  <v-row>
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
    <v-col cols="12" md="8">
      <v-card>
        <Categoria :categories="categories" />
        <v-container style="background-color: #e3f2fd; overflow-y: scroll;">
          <v-row>
            <v-col v-if="categorySelect" class="text-center" cols="12">
              <h1 class="font-italic">{{ categorySelect.nombre }}</h1>
            </v-col>
            <v-col cols="12">
              <v-text-field
                v-model="search"
                append-icon="search"
                label="Buscar"
                single-line
                hide-details
              ></v-text-field>
            </v-col>
            <template v-for="menu in filtrarMenu">
              <Platillo v-bind:key="menu.id" :platillo="menu" />
            </template>
          </v-row>
        </v-container>
      </v-card>
    </v-col>

    <v-col cols="12" md="4">
      <v-card>
        <v-toolbar>
          <v-toolbar-title style="padding: 2px;">
            Detalle de la orden: {{ tableName }}
          </v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn icon @click="returnToTables()">
            <v-icon dark>close</v-icon>
          </v-btn>
        </v-toolbar>
        <DetalleOrden />
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import Categoria from './CategoriaComponent.vue'
import Platillo from './PlatilloComponent.vue'
import DetalleOrden from './DetalleOrden.vue'

import { createNamespacedHelpers } from 'vuex'

const {
  mapGetters: restaurantMapGetter,
  mapMutations: restaurantMapMutations,
  mapActions: restaurantMapActions,
} = createNamespacedHelpers('restaurant')

export default {
  components: {
    Categoria,
    Platillo,
    DetalleOrden,
  },
  data() {
    return {
      loading: false,

      categories: [],
      menus: [],
      auxiliaryMenu: [],
      categorySelect: null,
      search: null,
    }
  },
  mounted() {
    this.getAllFoodCategories()
    this.getAllMenu()
  },
  created() {
    events.$on('filter_menus', this.eventFilterMenusList)
  },
  beforeDestroy() {
    events.$off('filter_menus')
  },
  methods: {
    ...restaurantMapActions(['UPDATE_SELECTED_TABLE']),
    ...restaurantMapMutations(['SET_AMOUNT_ORDER', 'SET_LIST_ITEMS']),
    returnToTables() {
      this.SET_AMOUNT_ORDER(0)
      this.SET_LIST_ITEMS([])
      this.UPDATE_SELECTED_TABLE({ id: 0, name: '', selected: false })
    },
    eventFilterMenusList(categoryFilter) {
      this.menus = []
      this.categorySelect = categoryFilter
      this.search = null
      this.auxiliaryMenu.forEach((item) => {
        if (
          this.isMenuCategory(item.producto_categoria_comida, categoryFilter.id)
        ) {
          this.menus.push(item)
        }
      })
    },

    isMenuCategory(item, filter) {
      let isEqualCategory = false

      for (let category of item) {
        if (category.categoria_comida_id === filter) {
          isEqualCategory = true
          break
        }
      }

      return isEqualCategory
    },
    getAllFoodCategories() {
      this.loading = true

      this.$store.state.services.foodCategoryService
        .getListFoodCategory()
        .then((r) => {
          this.categories = r.data.data
        })
        .catch((e) => {
          this.$toastr.error(e, 'Error')
        })
        .finally(() => {
          this.loading = false
        })
    },
    getAllMenu() {
      this.loading = true

      this.$store.state.services.productService
        .getProductsList()
        .then((r) => {
          this.menus = r.data.data
          this.auxiliaryMenu = r.data.data
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
    ...restaurantMapGetter(['selectedTable', 'isTableSelected', 'tableName']),
    filtrarMenu() {
      let filtro = this.menus
      if (this.search) {
        filtro = this.menus.filter((element) => {
          return element.nombre
            .toUpperCase()
            .includes(this.search.toUpperCase())
        })
      }

      return filtro
    },
  },
}
</script>
