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
          <v-toolbar flat :color="colorTolbar">
            <v-toolbar-title>Cajas</v-toolbar-title>
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
        <template v-slot:item.detalle="{ item }">
          <br />
          <v-card
            dark
            class="mx-auto text-left text-white"
            :color="item.abierta ? 'green' : 'red'"
            max-width="400"
          >
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-subtitle class="text-h6">
                  {{ `Apertura: ${item.inicio}` }}
                </v-list-item-subtitle>
                <v-list-item-subtitle class="text-h6">
                  {{ `Cierre: ${item.finalizo ? item.finalizo : ''}` }}
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title class="text-h5 text-right">
                  {{ formato_moneda(1, item.inicia_caja, 0) }}
                </v-list-item-title>
                <v-list-item-subtitle>
                  Monto de Apertura (+)
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title class="text-h5 text-right">
                  {{ formato_moneda(1, item.pagos, 0) }}
                </v-list-item-title>
                <v-list-item-subtitle>
                  Monto de Reservaciones (+)
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title class="text-h5 text-right">
                  {{ formato_moneda(1, item.restaurante, 0) }}
                </v-list-item-title>
                <v-list-item-subtitle>
                  Monto de Restaurante (+)
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title class="text-h5 text-right">
                  {{ formato_moneda(1, item.insumos, 0) }}
                </v-list-item-title>
                <v-list-item-subtitle>
                  Monto de Insumos (-)
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title class="text-h5 text-right">
                  {{ formato_moneda(1, item.compras, 0) }}
                </v-list-item-title>
                <v-list-item-subtitle>
                  Monto de Compras (-)
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-list-item two-line>
              <v-list-item-content>
                <v-list-item-title class="text-h5 text-right">
                  {{ formato_moneda(1, item.cierre_caja, 0) }}
                </v-list-item-title>
                <v-list-item-subtitle>Monto de Cierre (=)</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-card-actions>
              <v-btn
                block
                color="info"
                light
                small
                @click="mostrar_movimientos(item)"
              >
                Ver movimientos
              </v-btn>
            </v-card-actions>
          </v-card>
          <br />
        </template>
        <template v-slot:no-data>
          <br />
          <v-alert type="error">No hay información para mostrar.</v-alert>
        </template>
      </v-data-table>
    </v-col>

    <v-col cols="12" md="12">
      <v-dialog v-model="dialog" width="70%">
        <v-data-table
          :headers="headers_caja"
          :items="caja.movimientos"
          :search="search_caja"
          class="elevation-1"
          light
          :footer-props="footer"
          fixed-header
          height="80%"
        >
          <template v-slot:top>
            <v-toolbar flat color="success">
              <v-toolbar-title>
                {{ `Movimientos de la caja #${caja.id}` }}
              </v-toolbar-title>
              <v-divider class="mx-4" inset vertical></v-divider>
              <v-text-field
                v-model="search_caja"
                append-icon="search"
                label="Buscar"
                single-line
                hide-details
              ></v-text-field>
              <v-divider class="mx-4" inset vertical></v-divider>
              <v-toolbar-title>
                {{ `Aperturo caja: ${caja.usuario}` }}
              </v-toolbar-title>
            </v-toolbar>
          </template>
          <template v-slot:item.caja="{ item }">
            {{ caja.id }}
          </template>
          <template v-slot:item.monto_total="{ item }">
            {{ formato_moneda(1, item.monto_total, 0) }}
          </template>
          <template v-slot:no-data>
            <br />
            <v-alert type="error">No hay información para mostrar.</v-alert>
          </template>
        </v-data-table>
      </v-dialog>
    </v-col>
  </v-row>
</template>

<script>
export default {
  name: 'HCajaChica',
  data() {
    return {
      loading: false,
      dialog: false,

      search: '',
      headers: [
        {
          text: 'Caja #',
          align: 'center',
          value: 'id',
        },
        {
          text: 'Detalle',
          align: 'center',
          value: 'detalle',
        },
        {
          text: 'Día',
          align: 'center',
          value: 'dia',
        },
        {
          text: 'Mes',
          align: 'center',
          value: 'mes.nombre',
        },
        {
          text: 'Año',
          align: 'center',
          value: 'anio',
        },
        {
          text: 'Apertura',
          align: 'center',
          value: 'usuario.cui',
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

      caja: {
        id: 0,
        usuario: null,
        movimientos: [],
      },

      search_caja: '',
      headers_caja: [
        {
          text: 'Caja #',
          align: 'center',
          value: 'caja',
        },
        {
          text: 'Movimiento #',
          align: 'center',
          value: 'id',
        },
        {
          text: 'Descripción',
          align: 'left',
          value: 'descripcion',
        },
        {
          text: 'Tipo de Pago',
          align: 'center',
          value: 'tipo_pago',
        },
        {
          text: 'Comprobante',
          align: 'center',
          value: 'comprobante',
        },
        {
          text: 'Monto',
          align: 'right',
          value: 'monto_total',
        },
        {
          text: 'Usuario',
          align: 'center',
          value: 'usuario.cui',
        },
        {
          text: 'Fecha',
          align: 'center',
          value: 'created_at',
        },
      ],
    }
  },
  computed: {
    formTitle() {
      return !this.editedIndex ? 'Agregar' : 'Editar'
    },

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
      this.dialog = false
      this.caja.movimientos = []
      this.$store.state.services.CajaService.getAll()
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

    mostrar_movimientos(item) {
      this.caja.id = item.id
      this.caja.usuario = item.usuario.cui
      this.caja.movimientos = item.movimientos
      this.dialog = true
    },
  },
}
</script>
