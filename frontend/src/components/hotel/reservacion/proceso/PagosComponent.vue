<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-col cols="12" md="12">
      <v-tabs dark color="blue accent-4" right>
        <v-tab>Pagos activos</v-tab>
        <v-tab>Pagos anulados</v-tab>

        <v-tab-item :key="'0'">
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
                <v-toolbar-title>Pagos realizados</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-text-field
                  v-model="search"
                  append-icon="search"
                  label="Buscar"
                  single-line
                  hide-details
                ></v-text-field>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-btn color="white" small @click="initialize">
                  <v-icon :color="colorTolbar">sync</v-icon>
                </v-btn>
              </v-toolbar>
            </template>
            <template v-slot:item.anular="{ item }">
              <v-tooltip bottom color="red lighten-2">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn
                    class="ma-2"
                    text
                    icon
                    color="red lighten-2"
                    dark
                    v-bind="attrs"
                    v-on="on"
                    @click="destroy(item)"
                    small
                    :disabled="!item.anula"
                  >
                    <v-icon>report_problem</v-icon>
                  </v-btn>
                </template>
                <span
                  v-text="
                    `Anular pago para la reservación con código ${item.reservacion.codigo}`
                  "
                ></span>
              </v-tooltip>
            </template>
            <template v-slot:item.detalle="{ item }">
              <ul v-bind:key="item.correlativo">
                <li v-for="(item, index) in item.detalle" :key="index">
                  {{ item.descripcion }}
                </li>
              </ul>
            </template>
            <template v-slot:item.total="{ item }">
              <br />
              <p class="text-h4 text--white">
                {{ formato_moneda(1, item.total, 0) }}
                <v-tooltip bottom color="default orange-4">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      class="ma-2"
                      text
                      icon
                      color="orange lighten-4"
                      dark
                      v-bind="attrs"
                      v-on="on"
                      @click="imprimir_comprobante(item)"
                      :disabled="!item.ticket"
                    >
                      <v-icon x-large>print</v-icon>
                    </v-btn>
                  </template>
                  <span
                    v-text="
                      `Imprimir comprobante de pago de la reservación con código ${item.reservacion.codigo}`
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
        </v-tab-item>

        <v-tab-item :key="'1'">
          <v-data-table
            :headers="headers_anulados"
            :items="desserts_anulados"
            :search="search_anulados"
            sort-by="calories"
            class="elevation-1"
            dark
            dense
            :footer-props="footer"
          >
            <template v-slot:top>
              <v-toolbar flat :color="'red'">
                <v-toolbar-title>Pagos anulados</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-text-field
                  v-model="search_anulados"
                  append-icon="search"
                  label="Buscar"
                  single-line
                  hide-details
                ></v-text-field>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-btn color="white" small @click="initialize">
                  <v-icon :color="'red'">sync</v-icon>
                </v-btn>
              </v-toolbar>
            </template>
            <template v-slot:item.anular="{ item }">
              <v-tooltip bottom color="red lighten-2">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn
                    class="ma-2"
                    text
                    icon
                    color="red lighten-2"
                    dark
                    v-bind="attrs"
                    v-on="on"
                    @click="destroy(item)"
                    small
                    :disabled="!item.anula"
                  >
                    <v-icon>report_problem</v-icon>
                  </v-btn>
                </template>
                <span
                  v-text="
                    `Anular pago para la reservación con código ${item.reservacion.codigo}`
                  "
                ></span>
              </v-tooltip>
            </template>
            <template v-slot:item.detalle="{ item }">
              <ul v-bind:key="item.correlativo">
                <li v-for="(item, index) in item.detalle" :key="index">
                  {{ item.descripcion }}
                </li>
              </ul>
            </template>
            <template v-slot:item.total="{ item }">
              <br />
              <p class="text-h4 text--white">
                {{ formato_moneda(1, item.total, 0) }}
                <v-tooltip bottom color="default orange-4">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      class="ma-2"
                      text
                      icon
                      color="orange lighten-4"
                      dark
                      v-bind="attrs"
                      v-on="on"
                      @click="imprimir_comprobante(item)"
                      :disabled="!item.ticket"
                    >
                      <v-icon x-large>print</v-icon>
                    </v-btn>
                  </template>
                  <span
                    v-text="
                      `Imprimir comprobante de pago de la reservación con código ${item.reservacion.codigo}`
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
        </v-tab-item>
      </v-tabs>
    </v-col>

    <v-col cols="12" md="12"></v-col>
  </v-row>
</template>

<script>
export default {
  name: 'HPagos',
  data() {
    return {
      loading: false,

      search: '',
      headers: [
        {
          text: '',
          align: 'center',
          value: 'anular',
        },
        {
          text: 'Tipo de Pago',
          align: 'center',
          value: 'tipo_pago.nombre',
        },
        {
          text: 'Comprobante',
          align: 'center',
          value: 'correlativo',
        },
        {
          text: 'NIT',
          align: 'center',
          value: 'nit',
        },
        {
          text: 'Cliente',
          align: 'center',
          value: 'nombre',
        },
        {
          text: 'Reservación',
          align: 'center',
          value: 'reservacion.codigo',
        },
        {
          text: 'Detalle',
          align: 'center',
          value: 'detalle',
          sortable: false,
        },
        {
          text: 'Fecha',
          align: 'center',
          value: 'created_at',
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

      desserts_anulados: [],
      search_anulados: '',
      headers_anulados: [
        {
          text: '',
          align: 'center',
          value: 'anular',
        },
        {
          text: 'Tipo de Pago',
          align: 'center',
          value: 'tipo_pago.nombre',
        },
        {
          text: 'Comprobante',
          align: 'center',
          value: 'correlativo',
        },
        {
          text: 'NIT',
          align: 'center',
          value: 'nit',
        },
        {
          text: 'Cliente',
          align: 'center',
          value: 'nombre',
        },
        {
          text: 'Reservación',
          align: 'center',
          value: 'reservacion.codigo',
        },
        {
          text: 'Detalle',
          align: 'center',
          value: 'detalle',
          sortable: false,
        },
        {
          text: 'Fecha de Anulación',
          align: 'center',
          value: 'updated_at',
        },
        {
          text: 'Total',
          align: 'right',
          value: 'total',
          sortable: false,
        },
      ],
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
      this.$store.state.services.PagoService.getAll()
        .then((r) => {
          this.desserts = r.data.pagos
          this.desserts_anulados = r.data.anulados
        })
        .catch((e) => {
          this.errorResponse(e)
        })
        .finally(() => {
          this.loading = false
        })
    },

    destroy(data) {
      if (data.anula) {
        this.$swal({
          title: 'Anular',
          text: '¿Está seguro de realizar esta acción?',
          type: 'error',
          showCancelButton: true,
        }).then((result) => {
          if (result.value) {
            this.loading = true
            this.$store.state.services.PagoService.delete(data)
              .then((r) => {
                this.$toastr.success(r.data, 'Mensaje')
                this.notificador_audible(this.$store.state.audio.anular)
                this.initialize()
              })
              .catch((e) => {
                this.errorResponse(e)
              })
              .finally(() => {
                this.loading = false
              })
          }
        })
      } else {
        this.$toastr.success(
          'No puede eliminar comprobante de fechas pasadas.',
          'Mensaje',
        )
      }
    },

    imprimir_comprobante(item) {
      if (!item.ticket) {
        this.$toastr.warning(
          'NO se encuentra el comprobante de pago para la reservación seleccionada.',
          'Comporbante',
        )
        return
      }

      this.$swal({
        title: 'Imprimir',
        text: '¿Está seguro de realizar esta acción?',
        type: 'question',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          window.open(
            item.ticket,
            'popup',
            'width=' +
              1000 +
              ', height=' +
              800 +
              ', left=' +
              500 / 2 +
              ', top=' +
              500 / 2 +
              '',
          )
        }
      })
    },
  },
}
</script>
