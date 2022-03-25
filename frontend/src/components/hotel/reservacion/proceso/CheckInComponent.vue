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
        sort-by="calories"
        class="elevation-1"
        dark
        dense
        :footer-props="footer"
      >
        <template v-slot:top>
          <v-toolbar flat :color="colorTolbar">
            <v-toolbar-title>Check In</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-text-field
              v-model="search"
              append-icon="search"
              label="Buscar"
              single-line
              hide-details
            ></v-text-field>
          </v-toolbar>
        </template>
        <template v-slot:item.habitacion="{ item }">
          <br />
          <v-chip
            class="ma-2"
            color="secondary"
            v-for="(check, index) in item.check_in"
            v-bind:key="index"
          >
            {{ `Habitación #${check.detalle.habitacion.numero}` }}
          </v-chip>
        </template>
        <template v-slot:item.firma="{ item }">
          <br />
          <v-avatar color="white" size="100" tile>
            <img :src="item.check_in[0].firma" alt="firma" />
          </v-avatar>
          <br />
          <br />
        </template>
        <template v-slot:item.total="{ item }">
          <br />
          <p class="text-h4 text--white">
            {{ formato_moneda(1, item.total, 0) }}
            <v-tooltip bottom color="primary lighten-2">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  class="ma-2"
                  text
                  icon
                  color="primary lighten-2"
                  dark
                  v-bind="attrs"
                  v-on="on"
                >
                  <v-icon x-large>ballot</v-icon>
                </v-btn>
              </template>
              <span
                v-text="
                  `Registrar Check Out a la reservación con código ${item.codigo}`
                "
              ></span>
            </v-tooltip>
          </p>
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
import FormError from '../../../shared/FormError.vue'
export default {
  name: 'HCheckIn',
  components: {
    FormError,
  },
  data() {
    return {
      loading: false,
      dialog: true,
      search: '',
      headers: [
        {
          text: 'Reservación',
          align: 'center',
          value: 'codigo',
        },
        {
          text: 'Responsable',
          align: 'center',
          value: 'check_in[0].nombre',
        },
        {
          text: 'Registro',
          align: 'center',
          value: 'check_in[0].created_at',
        },
        {
          text: 'Habitaciones',
          align: 'center',
          value: 'habitacion',
          sortable: false,
        },
        {
          text: 'Firma',
          align: 'center',
          value: 'firma',
          sortable: false,
        },
        {
          text: 'Total',
          align: 'right',
          value: 'total',
          sortable: false,
        },
      ],
      footer: {
        showFirstLastPage: true,
        firstIcon: 'mdi-arrow-collapse-left',
        lastIcon: 'mdi-arrow-collapse-right',
        prevIcon: 'mdi-minus',
        nextIcon: 'mdi-plus',
      },
      desserts: [],
      editedIndex: false,
    }
  },

  computed: {
    colorTolbar() {
      return !this.editedIndex ? 'success' : 'warning'
    },
  },

  created() {
    this.initialize()
  },

  methods: {
    initialize() {
      this.loading = true

      this.$store.state.services.ReservacionService.getAll('i')
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

          this.desserts = r.data
        })
        .catch((r) => {
          this.loading = false
        })
    },

    formato_moneda(cantidad, precio, descuento) {
      let cantidad_no_null = cantidad ? cantidad : 0
      let precio_no_null = precio ? precio : 0
      let descuento_no_null = descuento ? descuento : 0
      let monto =
        parseInt(cantidad_no_null) * parseFloat(precio_no_null) -
        parseFloat(descuento_no_null)
      return monto.toLocaleString('es-GT', {
        style: 'currency',
        currency: 'GTQ',
        minimumFractionDigits: 2,
      })
    },
  },
}
</script>
