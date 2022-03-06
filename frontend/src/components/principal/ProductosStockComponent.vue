<template>
  <v-row justify="center" align="center">
    <v-col cols="12" md="12">
      <v-expansion-panels multiple>
        <v-expansion-panel>
          <v-expansion-panel-header>Productos</v-expansion-panel-header>
          <v-expansion-panel-content>
            <Productos></Productos>
          </v-expansion-panel-content>
        </v-expansion-panel>

        <v-expansion-panel>
          <v-expansion-panel-header>Stock</v-expansion-panel-header>
          <v-expansion-panel-content>
            <Stock></Stock>
          </v-expansion-panel-content>
        </v-expansion-panel>
      </v-expansion-panels>
    </v-col>
  </v-row>
</template>

<script>
import Productos from '../principal/extras/ProductosComponent'
import Stock from '../principal/extras/StockComponent'
export default {
  name: 'ProductosStock',
  components: {
    Productos,
    Stock,
  },
  data() {
    return {}
  },

  created() {},

  methods: {},
}
</script>
