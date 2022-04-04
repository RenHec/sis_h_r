<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-col cols="12" md="12">
      <v-tabs dark color="blue accent-4" right>
        <v-tab>Reservaciones activas</v-tab>
        <v-tab>Reservaciones anuladas</v-tab>

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
                <v-toolbar-title>
                  Reservaciones registradas
                </v-toolbar-title>
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
            <template v-slot:item.ticket="{ item }">
              <v-tooltip bottom color="success lighten-2">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn
                    class="ma-2"
                    text
                    icon
                    color="success lighten-2"
                    dark
                    v-bind="attrs"
                    v-on="on"
                    @click="imprimir_ticket(item)"
                    small
                    :disabled="!item.ticket"
                  >
                    <v-icon>print</v-icon>
                  </v-btn>
                </template>
                <span
                  v-text="`Imprimir ticket de reservación ${item.codigo}`"
                ></span>
              </v-tooltip>
            </template>
            <template v-slot:item.informacion="{ item }">
              <v-timeline :reverse="false" dense>
                <v-timeline-item v-bind:key="`detalle${item.codigo}`">
                  <v-card light class="elevation-2 text-left">
                    <v-card-title class="text-h5">
                      Detalle de la reservación {{ item.codigo }}
                    </v-card-title>
                    <v-card-text>
                      <ul v-bind:key="item.correlativo">
                        <li v-for="(item, index) in item.detalle" :key="index">
                          {{ item.descripcion }}
                        </li>
                      </ul>
                    </v-card-text>
                  </v-card>
                </v-timeline-item>
                <v-timeline-item
                  v-bind:key="`check_in${item.codigo}`"
                  v-if="item.check_in"
                >
                  <v-card light class="elevation-2 text-left">
                    <v-card-title class="text-h5">
                      Check In de la reservación {{ item.codigo }}
                    </v-card-title>
                    <v-card-text>
                      <template v-for="(item, index) in item.check_in_list">
                        <div class="subtitle-1" v-bind:key="`div${index}`">
                          {{ item.habitacion }}
                        </div>
                        <ul v-bind:key="`ul${index}`">
                          <li
                            v-for="(iitem, index) in item.lista"
                            v-bind:key="`ul${index}li${index}`"
                          >
                            {{ `${iitem.cantidad} | ${iitem.producto}` }}
                          </li>
                        </ul>
                        <hr v-bind:key="`hr${index}`" />
                      </template>
                    </v-card-text>
                  </v-card>
                </v-timeline-item>
                <v-timeline-item
                  v-bind:key="`check_out${item.codigo}`"
                  v-if="item.check_out"
                >
                  <v-card light class="elevation-2 text-left">
                    <v-card-title class="text-h5">
                      Check Out de la reservación {{ item.codigo }}
                    </v-card-title>
                    <v-card-text>
                      <template v-for="(item, index) in item.check_out_list">
                        <div class="subtitle-1" v-bind:key="`div${index}`">
                          {{ item.habitacion }}
                        </div>
                        <ul v-bind:key="`ul${index}`">
                          <li
                            v-for="(iitem, index) in item.lista"
                            v-bind:key="`ul${index}li${index}`"
                          >
                            {{ `${iitem.cantidad} | ${iitem.producto}` }}
                          </li>
                        </ul>
                        <hr v-bind:key="`hr${index}`" />
                      </template>
                    </v-card-text>
                  </v-card>
                </v-timeline-item>
                <v-timeline-item
                  v-bind:key="`pagado${item.codigo}`"
                  v-if="item.pagado"
                >
                  <v-card light class="elevation-2 text-left">
                    <v-card-title class="text-h5">
                      Comprobante de pago de la reservación
                      {{ item.codigo }}
                    </v-card-title>
                    <v-card-text>
                      <v-tooltip bottom color="black lighten-2">
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            class="ma-2"
                            text
                            icon
                            color="black dark-2"
                            dark
                            v-bind="attrs"
                            v-on="on"
                            @click="imprimir_comprobante(item.pago)"
                            x-large
                            :disabled="!item.pago.ticket"
                          >
                            <v-icon>print</v-icon>
                          </v-btn>
                        </template>
                        <span
                          v-text="
                            `Imprimir comprobante de pago de la reservación ${item.codigo}`
                          "
                        ></span>
                      </v-tooltip>
                    </v-card-text>
                  </v-card>
                </v-timeline-item>
              </v-timeline>
            </template>
            <template v-slot:no-data>
              <br />
              <v-alert type="error">
                No hay información para mostrar.
              </v-alert>
            </template>
          </v-data-table>
        </v-tab-item>

        <v-tab-item :key="'1'">
          <v-data-table
            :headers="headers_anulado"
            :items="desserts_anulado"
            :search="search_anulado"
            sort-by="calories"
            class="elevation-1"
            dark
            dense
            :footer-props="footer_anulado"
          >
            <template v-slot:top>
              <v-toolbar flat :color="'red'">
                <v-toolbar-title>Rservaciones anuladas</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-text-field
                  v-model="search_anulado"
                  append-icon="search"
                  label="Buscar"
                  single-line
                  hide-details
                ></v-text-field>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-btn color="white" small @click="initialize_anulado">
                  <v-icon :color="'red'">sync</v-icon>
                </v-btn>
              </v-toolbar>
            </template>
            <template v-slot:no-data>
              <br />
              <v-alert type="error">
                No hay información para mostrar.
              </v-alert>
            </template>
          </v-data-table>
        </v-tab-item>
      </v-tabs>
    </v-col>
  </v-row>
</template>

<script>
export default {
  name: 'HHistorial',
  data() {
    return {
      loading: false,
      search: '',
      headers: [
        {
          text: 'Fecha Registro',
          align: 'center',
          value: 'created_at',
        },
        {
          text: 'Ticket',
          align: 'center',
          value: 'ticket',
          sortable: false,
          search: false,
        },
        {
          text: 'Reservación',
          align: 'center',
          value: 'codigo',
        },
        {
          text: 'Fecha Inicio',
          align: 'center',
          value: 'detalle[0].inicio',
        },
        {
          text: 'Fecha Fin',
          align: 'center',
          value: 'detalle[0].fin',
        },
        {
          text: 'Información',
          align: 'center',
          value: 'informacion',
          sortable: false,
          search: false,
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

      search_anulado: '',
      headers_anulado: [
        {
          text: 'Fecha Registro',
          align: 'center',
          value: 'created_at',
        },
        {
          text: 'Reservación',
          align: 'center',
          value: 'codigo',
        },
        {
          text: 'Fecha de Anulación',
          align: 'center',
          value: 'updated_at',
        },
      ],
      footer_anulado: {
        showFirstLastPage: true,
        firstIcon: 'mdi-arrow-collapse-left',
        lastIcon: 'mdi-arrow-collapse-right',
        prevIcon: 'mdi-minus',
        nextIcon: 'mdi-plus',
      },
      desserts_anulado: [],
    }
  },

  computed: {
    colorTolbar() {
      return !this.editedIndex ? 'success' : 'warning'
    },
  },
  created() {
    this.initialize()
    this.initialize_anulado()
  },

  methods: {
    initialize() {
      this.loading = true
      this.$store.state.services.ReservacionService.getAll()
        .then((r) => {
          this.desserts = r.data
        })
        .catch((e) => {
          this.errorResponse(e)
        })
        .finally(() => {
          this.loading = false
        })
    },

    initialize_anulado() {
      this.loading = true
      this.$store.state.services.ReservacionService.getAll('a')
        .then((r) => {
          this.desserts_anulado = r.data
        })
        .catch((e) => {
          this.errorResponse(e)
        })
        .finally(() => {
          this.loading = false
        })
    },

    imprimir_ticket(item) {
      if (!item.ticket) {
        this.$toastr.warning(
          'NO se encuentra el ticket para la reservación seleccionada.',
          'Ticket',
        )
        return
      }

      this.$swal({
        title: 'Imprimir ticket',
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

    imprimir_comprobante(item) {
      if (!item.ticket) {
        this.$toastr.warning(
          'NO se encuentra el comprobante de pago para la reservación seleccionada.',
          'Comporbante',
        )
        return
      }

      this.$swal({
        title: 'Imprimir comprobante',
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
