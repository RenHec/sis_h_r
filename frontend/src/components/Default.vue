<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-col cols="12" md="6">
      <v-card class="mt-4 mx-auto" max-width="100%">
        <v-sheet
          class="v-sheet--offset mx-auto"
          color="cyan"
          elevation="12"
          max-width="calc(100% - 32px)"
        >
          <highcharts
            :options="graficaProductosMasVendidos"
            v-if="productos_mas_vendidos.series.length > 0"
          ></highcharts>
        </v-sheet>

        <v-card-text class="pt-0">
          <div class="text-h6 font-weight-light mb-2">
            Productos más vendidos
          </div>
          <div class="subheading font-weight-light grey--text">
            {{
              `Estos productos pertenecen a la empresa ${empresa}, y la gráfica muestra los ${productos_mas_vendidos.cantidad} productos más vendidos`
            }}
          </div>
          <v-divider class="my-2"></v-divider>
          <v-icon class="mr-2" small>
            mdi-clock
          </v-icon>
          <span class="text-caption grey--text font-weight-light">
            {{ productos_mas_vendidos.mensaje }}
          </span>
        </v-card-text>
      </v-card>
    </v-col>

    <v-col cols="12" md="6">
      <v-card class="mt-4 mx-auto" max-width="100%">
        <v-sheet
          class="v-sheet--offset mx-auto"
          color="cyan"
          elevation="12"
          max-width="calc(100% - 32px)"
        >
          <highcharts
            :options="graficaProximosATerminar"
            v-if="productos_por_terminar.categorias.length > 0"
          ></highcharts>
        </v-sheet>

        <v-card-text class="pt-0">
          <div class="text-h6 font-weight-light mb-2">
            Productos próximos a terminar
          </div>
          <div class="subheading font-weight-light grey--text">
            {{
              `Estos productos pertenecen a la empresa ${empresa}, y la gráfica muestra todos los productos que tienen menos de ${productos_por_terminar.menos} en el stock`
            }}
          </div>
          <v-divider class="my-2"></v-divider>
          <v-icon class="mr-2" small>
            mdi-clock
          </v-icon>
          <span class="text-caption grey--text font-weight-light">
            {{ productos_por_terminar.mensaje }}
          </span>
        </v-card-text>
      </v-card>
    </v-col>

    <v-col cols="12" md="12">
      <v-card class="mt-4 mx-auto" max-width="100%">
        <v-sheet
          class="v-sheet--offset mx-auto"
          color="cyan"
          elevation="12"
          max-width="calc(100% - 32px)"
        >
          <highcharts
            :options="graficaProximosAVencer"
            v-if="productos_vencidos.categorias.length > 0"
          ></highcharts>
        </v-sheet>

        <v-card-text class="pt-0">
          <div class="text-h6 font-weight-light mb-2">
            Productos próximos a vencer
          </div>
          <div class="subheading font-weight-light grey--text">
            {{
              `Estos productos pertenecen a la empresa ${empresa}, y la gráfica muestra todos los productos que vencerán en ${productos_vencidos.dias} días, el rango de fecha inicia en ${productos_vencidos.fecha_inicio} y termina ${productos_vencidos.fecha_fin}`
            }}
          </div>
          <v-divider class="my-2"></v-divider>
          <v-icon class="mr-2" small>
            mdi-clock
          </v-icon>
          <span class="text-caption grey--text font-weight-light">
            {{ productos_vencidos.mensaje }}
          </span>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import FormError from './shared/FormError'

export default {
  name: 'Default',
  components: { FormError },
  data() {
    return {
      loading: false,
      productos_mas_vendidos: {
        series: [],
        mensaje: null,
        cantidad: null,
      },
      productos_vencidos: {
        categorias: [],
        series: [],
        dias: null,
        fecha_inicio: null,
        fecha_fin: null,
        mensaje: null,
      },
      productos_por_terminar: {
        series: [],
        categorias: [],
        menos: null,
        mensaje: null,
      },
    }
  },

  created() {
    this.initialize()
  },

  methods: {
    initialize() {
      this.loading = true

      Promise.all([
        this.getProductosMasVendidos(),
        this.getVencidos(),
        this.getProductosPorTerminar(),
      ])
        .then(() => {
          this.loading = false
        })
        .catch((error) => {
          this.$toastr.error('Ocurrió un error: ' + error, 'Error')
        })
    },

    getProductosMasVendidos() {
      this.$store.state.services.selectController
        .productos_mas_vendidos()
        .then((r) => {
          this.productos_mas_vendidos.series = r.data.series
          this.productos_mas_vendidos.cantidad = r.data.cantidad
          this.productos_mas_vendidos.mensaje = r.data.mensaje
        })
        .catch((r) => {})
    },

    getVencidos() {
      this.$store.state.services.selectController
        .productos_vencidos()
        .then((r) => {
          this.productos_vencidos.categorias = r.data.fechas
          this.productos_vencidos.series = r.data.series
          this.productos_vencidos.dias = r.data.dias
          this.productos_vencidos.fecha_inicio = r.data.fecha_inicio
          this.productos_vencidos.fecha_fin = r.data.fecha_fin
          this.productos_vencidos.mensaje = r.data.mensaje
        })
        .catch((r) => {})
    },

    getProductosPorTerminar() {
      this.$store.state.services.selectController
        .productos_por_terminar()
        .then((r) => {
          this.productos_por_terminar.categorias = r.data.labels
          this.productos_por_terminar.series = r.data.values
          this.productos_por_terminar.menos = r.data.valor
          this.productos_por_terminar.mensaje = r.data.mensaje
        })
        .catch((r) => {})
    },
  },

  computed: {
    empresa() {
      return this.$store.state.empresa ? this.$store.state.empresa.nombre : null
    },

    graficaProductosMasVendidos() {
      let chart = {
        chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: 'pie',
        },
        title: {
          text: `Productos más vendidos`.toUpperCase(),
        },
        subtitle: {
          text: this.$store.state.empresa
            ? this.$store.state.empresa.nombre.toUpperCase()
            : null,
        },
        tooltip: {
          pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>',
        },
        accessibility: {
          point: {
            valueSuffix: '%',
          },
        },
        plotOptions: {
          pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
              enabled: true,
              format: '<b>{point.name}</b>: {point.percentage:.1f} %',
            },
          },
        },
        series: [
          {
            name: 'ventas',
            colorByPoint: true,
            data: this.productos_mas_vendidos.series,
          },
        ],
      }

      return chart
    },

    graficaProximosATerminar() {
      let chart = {
        chart: {
          type: 'column',
        },
        title: {
          text: `Productos próximos a terminar stock`.toUpperCase(),
        },
        subtitle: {
          text: this.$store.state.empresa
            ? this.$store.state.empresa.nombre.toUpperCase()
            : null,
        },

        legend: {
          align: 'right',
          verticalAlign: 'middle',
          layout: 'vertical',
        },
        xAxis: {
          categories: this.productos_por_terminar.categorias,
        },
        yAxis: {
          allowDecimals: false,
          title: {
            text: 'stock',
          },
        },
        series: [
          {
            type: 'column',
            colorByPoint: true,
            data: this.productos_por_terminar.series,
            showInLegend: false,
            name: 'stock',
          },
        ],
        responsive: {
          rules: [
            {
              condition: {
                maxWidth: 500,
              },
              chartOptions: {
                legend: {
                  align: 'center',
                  verticalAlign: 'bottom',
                  layout: 'horizontal',
                },
                yAxis: {
                  labels: {
                    align: 'left',
                    x: 0,
                    y: -5,
                  },
                  title: {
                    text: null,
                  },
                },
                subtitle: {
                  text: null,
                },
                credits: {
                  enabled: false,
                },
              },
            },
          ],
        },
      }

      return chart
    },

    graficaProximosAVencer() {
      let chart = {
        chart: {
          type: 'column',
        },
        title: {
          text: `Productos próximos a vencer`.toUpperCase(),
        },
        subtitle: {
          text: this.$store.state.empresa
            ? this.$store.state.empresa.nombre.toUpperCase()
            : null,
        },
        legend: {
          align: 'right',
          verticalAlign: 'middle',
          layout: 'vertical',
        },
        xAxis: {
          categories: this.productos_vencidos.categorias,
          labels: {
            x: -10,
          },
        },
        yAxis: {
          allowDecimals: false,
          title: {
            text: 'cantidad',
          },
        },
        series: this.productos_vencidos.series,
        responsive: {
          rules: [
            {
              condition: {
                maxWidth: 500,
              },
              chartOptions: {
                legend: {
                  align: 'center',
                  verticalAlign: 'bottom',
                  layout: 'horizontal',
                },
                yAxis: {
                  labels: {
                    align: 'left',
                    x: 0,
                    y: -5,
                  },
                  title: {
                    text: null,
                  },
                },
                subtitle: {
                  text: null,
                },
                credits: {
                  enabled: false,
                },
              },
            },
          ],
        },
      }

      return chart
    },
  },
}
</script>
