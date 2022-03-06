<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-col cols="12" md="12">
      <v-data-table
        :headers="headers"
        :items="desserts"
        :search="search"
        class="elevation-1"
        dark
        :footer-props="footer"
      >
        <template v-slot:top>
          <v-toolbar flat>
            <v-toolbar-title>Devoluciones</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-text-field
              v-model="search"
              append-icon="search"
              label="Buscar"
              single-line
              hide-details
            ></v-text-field>
            <v-spacer></v-spacer>
            <v-btn class="ma-2 mb-2" color="success" @click="initialize">
              Actualizar
            </v-btn>
          </v-toolbar>
        </template>
        <template v-slot:item.usuario="{ item }">
          <br />
          <div class="text-center">
            <v-avatar size="75">
              <img
                :src="item.usuarios.picture"
                :alt="item.usuarios.full_name"
              />
            </v-avatar>
            <br />
            {{ item.usuarios.cui }}
            <br />
            {{ item.usuarios.full_name }}
          </div>
          <br />
        </template>
        <template v-slot:item.precio="{ item }">
          {{ formato_moneda(item.precio) }}
        </template>
        <template v-slot:item.total="{ item }">
          {{ formato_moneda(item.total) }}
        </template>
        <template v-slot:no-data>
          <br />
          <v-alert type="error">No hay información para mostrar.</v-alert>
        </template>
      </v-data-table>
    </v-col>
  </v-row>
</template>

<script>
export default {
  name: 'Devoluciones',
  data() {
    return {
      loading: false,

      desserts: [],
      search: '',
      headers: [
        { text: 'Usuario', align: 'start', value: 'usuario' },
        {
          text: 'Código',
          align: 'start',
          value: 'codigo',
        },
        {
          text: 'Cantidad',
          align: 'start',
          value: 'cantidad',
        },
        {
          text: 'Producto',
          align: 'start',
          value: 'nombre_completo',
        },
        {
          text: 'Precio',
          align: 'start',
          value: 'precio',
        },
        {
          text: 'Total',
          align: 'start',
          value: 'total',
        },
        {
          text: 'Fecha',
          align: 'start',
          value: 'created_at',
        },
      ],
      footer: {
        showFirstLastPage: true,
        firstIcon: 'mdi-arrow-collapse-left',
        lastIcon: 'mdi-arrow-collapse-right',
        prevIcon: 'mdi-minus',
        nextIcon: 'mdi-plus',
      },
    }
  },

  created() {
    this.initialize()
  },

  methods: {
    initialize() {
      this.loading = true

      this.$store.state.services.devolucionController
        .index()
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

          this.desserts = r.data.data
        })
        .catch((r) => {
          this.loading = false
        })
    },

    formato_moneda(monto) {
      return monto.toLocaleString('es-GT', {
        style: 'currency',
        currency: 'GTQ',
        minimumFractionDigits: 2,
      })
    },
  },
}
</script>
