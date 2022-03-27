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
              >
                <v-icon>report_problem</v-icon>
              </v-btn>
            </template>
            <span
              v-text="
                `Anular check in para la reservación con código ${item.codigo}`
              "
            ></span>
          </v-tooltip>
        </template>
        <template v-slot:item.habitacion="{ item }">
          <br />
          <v-chip
            class="ma-2"
            color="secondary"
            v-for="(check, index) in item.check_in_list"
            v-bind:key="index"
          >
            {{ check.habitacion }}
          </v-chip>
          <v-divider></v-divider>
          <v-card class="pt-6 mx-auto" light color="white" flat>
            <template
              v-for="(distribucion, index_distribucion) in item.check_in_list[0]
                .distribucion"
            >
              <v-card-text v-bind:key="`CardText${index_distribucion}`">
                <h3 class="text-h5 mb-2">
                  {{ distribucion.producto }}
                </h3>
                <div class="blue--text mb-2">
                  {{ `Total de productos ${distribucion.total}` }}
                </div>
              </v-card-text>
              <v-row
                class="text-left"
                tag="v-card-text"
                v-for="(producto_item,
                index_producto) in distribucion.distribucion"
                v-bind:key="`Producto${index_producto}-Lista${index_distribucion}`"
              >
                <v-col class="text-left text-sm-h6" cols="8">
                  {{ `Habitación #${producto_item.habitacion}:` }}
                </v-col>
                <v-col class="text-right">
                  {{ producto_item.cantidad }}
                </v-col>
              </v-row>
              <v-divider
                v-bind:key="`Divider${index_distribucion}`"
              ></v-divider>
            </template>
          </v-card>
          <br />
        </template>
        <template v-slot:item.firma="{ item }">
          <br />
          <v-avatar color="white" size="100" tile>
            <img :src="item.check_in_list[0].firma" alt="firma" />
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
                  @click="procesor_check_out(item)"
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

    <v-col cols="12">
      <v-dialog
        v-model="dialog"
        fullscreen
        persistent
        transition="dialog-top-transition"
      >
        <v-card dark :loading="loading">
          <v-card-title class="indigo white--text text-h5">
            <span class="headline">
              <v-icon x-large>ballot</v-icon>
              {{ `Check Out ${form.codigo}` }}
            </span>
            <v-spacer></v-spacer>
            <v-btn color="red" text icon @click="dialog = false">
              <v-icon>close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            <v-container fluid>
              <v-overlay :value="loading">
                <v-progress-circular
                  indeterminate
                  size="64"
                ></v-progress-circular>
              </v-overlay>
              <v-row class="pa-4" justify="space-between">
                <v-col cols="8">
                  <v-expansion-panels light flat>
                    <v-expansion-panel>
                      <v-expansion-panel-header disable-icon-rotate>
                        Habitaciones
                        <template v-slot:actions>
                          <v-icon color="black">
                            $expand
                          </v-icon>
                        </template>
                      </v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <template v-for="(item, index) in form.check_out">
                          <br v-bind:key="`Salto1${item.habitacion}`" />
                          <div
                            class="text-h6 white--black text--lighten-1 font-weight-light"
                            style="align-self: center;"
                            v-bind:key="item.habitacion"
                          >
                            {{ item.habitacion }}
                          </div>
                          <v-row v-bind:key="`Listas${index}`">
                            <v-col
                              cols="12"
                              sm="3"
                              md="3"
                              v-for="(lista, il) in item.lista"
                              v-bind:key="`Producto${il}`"
                            >
                              <v-checkbox
                                v-model="lista.checkout"
                                :label="lista.producto"
                                color="indigo"
                                hide-details
                              ></v-checkbox>
                            </v-col>
                          </v-row>
                          <br v-bind:key="`Salto2${item.habitacion}`" />
                          <v-divider
                            v-bind:key="`Division${index}`"
                          ></v-divider>
                        </template>
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                  </v-expansion-panels>
                </v-col>
                <v-divider vertical></v-divider>
                <v-col cols="12" md="4" sm="4">
                  <v-row>
                    <v-col cols="12">
                      <div
                        class="text-h6 white--text text--lighten-1 font-weight-light"
                        style="align-self: center;"
                      >
                        {{
                          `Encargado del check in en la reservación con código ${form.codigo}`
                        }}
                      </div>
                      <v-divider></v-divider>
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        filled-inverted
                        suffix
                        dense
                        dark
                        prepend-inner-icon="fiber_new"
                        counter
                        v-model="form.nombre"
                        type="text"
                        label="nombre"
                        data-vv-scope="crear"
                        data-vv-name="nombre"
                        v-validate="'required|max:100'"
                        hint="El nombre del responsable"
                        persistent-hint
                      ></v-text-field>
                      <FormError
                        :attribute_name="'crear.nombre'"
                        :errors_form="errors"
                      ></FormError>
                    </v-col>
                    <v-col cols="12">
                      <v-textarea
                        filled-inverted
                        suffix
                        dense
                        dark
                        prepend-inner-icon="fiber_new"
                        counter
                        v-model="form.descripcion"
                        type="text"
                        label="descripción"
                        data-vv-scope="crear"
                        data-vv-name="descripción"
                        v-validate="'max:1500'"
                        hint="Notas"
                        persistent-hint
                      ></v-textarea>
                      <FormError
                        :attribute_name="'crear.descripción'"
                        :errors_form="errors"
                      ></FormError>
                    </v-col>

                    <v-col cols="12">
                      <div
                        class="text-h6 white--text text--lighten-1 font-weight-light text-center"
                        style="align-self: center;"
                      >
                        Firma del encargado
                      </div>
                    </v-col>
                    <v-col class="text-center" cols="12">
                      <v-btn small color="red" text @click="limpiar_firma">
                        Volver a firmar
                      </v-btn>
                    </v-col>
                    <v-col cols="12" class="text-center">
                      <canvas
                        id="draw-canvas"
                        ref="canvas_img"
                        @mousedown="startPainting"
                        @mouseup="finishedPainting"
                        @mousemove="draw"
                        @touchstart="startTouch"
                        @touchend="finishedTouch"
                        @touchleave="leaveTouch"
                        @touchmove="moveTouch"
                      >
                        No tienes un buen navegador.
                      </canvas>
                    </v-col>

                    <v-col cols="12">
                      <v-btn
                        color="success"
                        x-large
                        :loading="loading"
                        :disabled="loading"
                        block
                        @click="store('crear')"
                      >
                        REGISTRAR
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
          <v-divider></v-divider>
          <v-card-actions>
            <v-btn
              color="success"
              x-large
              :loading="loading"
              :disabled="loading"
              block
              @click="store('crear')"
            >
              REGISTRAR
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-col>
  </v-row>
</template>

<style scoped>
#draw-canvas {
  border: 2px solid black;
  border-radius: 5px;
  cursor: crosshair;
  background: white;
  left: 10;
  top: 20;
  right: 10;
  bottom: 20;
}
</style>

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
          text: '',
          align: 'center',
          value: 'anular',
        },
        {
          text: 'Reservación',
          align: 'center',
          value: 'codigo',
        },
        {
          text: 'Responsable',
          align: 'center',
          value: 'check_in_list[0].nombre',
        },
        {
          text: 'Registro',
          align: 'center',
          value: 'check_in_list[0].created_at',
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

      form: {
        foto: null,
        codigo: null,
        nombre: null,
        descripcion: null,
        check_out: [],
      },

      //Firma
      painting: false,
      canvas: null,
      ctx: null,
      mousePos: { x: 0, y: 0 },
      lastPos: null,
      tint: null,
    }
  },

  computed: {
    colorTolbar() {
      return !this.editedIndex ? 'success' : 'warning'
    },
  },

  mounted() {
    this.canvas = this.$refs.canvas_img
    this.ctx = this.canvas.getContext('2d')
  },

  created() {
    this.initialize()
  },

  methods: {
    limpiar_firma() {
      this.canvas.width = this.canvas.width
    },

    limpiar() {
      this.form.foto = null
      this.form.codigo = null
      this.form.nombre = null
      this.form.descripcion = null
      this.form.check_out = []
      this.editedIndex = false

      this.selected = -1
      this.stock_general = []

      this.lastPos = this.mousePos
      this.dialog = false

      this.$validator.reset()
      this.$validator.reset()
    },

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
          this.limpiar()
        })
        .catch((r) => {
          this.loading = false
        })
    },

    destroy(data) {
      this.$swal({
        title: 'Anular',
        text: '¿Está seguro de realizar esta acción?',
        type: 'error',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.CheckInService.delete(data)
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

              this.$toastr.success(r.data, 'Mensaje')
              this.notificador_audible(this.$store.state.audio.anular)
              this.initialize()
            })
            .catch((r) => {
              this.loading = false
            })
        }
      })
    },

    /*:::::::::::::::::::::: PROCESO CHECK OUT ::::::::::::::::::::::::::*/
    procesor_check_out(item) {
      this.loading = true

      let nuevo = new Object()
      let crear_objeto = function check_out(reservacion) {
        return new Promise((resolve, reject) => {
          setTimeout(() => {
            nuevo.foto = null
            nuevo.codigo = reservacion.codigo
            nuevo.nombre = reservacion.nombre
            nuevo.descripcion = null
            nuevo.check_out = []

            reservacion.check_in_list.forEach((ele) => {
              let check = new Object()
              check.h_check_in_id = ele.id
              check.h_reservaciones_id = ele.h_reservaciones_id
              check.h_reservaciones_detalles_id =
                ele.h_reservaciones_detalles_id
              check.h_habitaciones_id = ele.h_habitaciones_id
              check.habitacion = ele.habitacion
              check.lista = ele.lista.filter((element) => {
                return element.consumible === 0
              })

              nuevo.descripcion = ele.descripcion
              nuevo.check_out.push(check)
            })

            if (nuevo.check_out.length === 0) {
              reject(
                `No fue posible obtener la información de la lista del check in de la reservación ${reservacion.codigo}`,
              )
            } else {
              resolve(
                `La lista del check in de la reservación ${reservacion.codigo} fue cargada al proceso del check out`,
              )
            }
          }, 2000)
        })
      }

      crear_objeto(item)
        .then((res) => {
          this.$toastr.success(res, 'Check Out')
          this.form.foto = nuevo.foto
          this.form.codigo = nuevo.codigo
          this.form.nombre = nuevo.nombre
          this.form.descripcion = nuevo.descripcion
          this.form.check_out = nuevo.check_out

          this.dialog = true
          this.loading = false
        })
        .catch((error) => {
          this.loading = false
          this.$swal({
            title: 'Check Out',
            text: error,
            type: 'info',
            showCancelButton: false,
          })
        })
    },

    store(scope) {
      //Validacion de check in seleccionados
      this.loading = true
      let verificador_lista_check = function verficador(
        firma_en_blanco,
        canvas,
      ) {
        return new Promise((resolve, reject) => {
          setTimeout(() => {
            let error = new Object()
            if (firma_en_blanco === canvas) {
              error.titulo = `Firma`
              error.text = `Es obligatorio que el responsable firme de enterado.`
              reject(error)
            }
            resolve('Resolved')
          }, 2000)
        })
      }

      this.form.foto = this.canvas.toDataURL()

      verificador_lista_check(
        this.$store.state.firma_en_blanco,
        this.canvas.toDataURL(),
      )
        .then((res) => {
          this.loading = false

          this.$validator.validateAll(scope).then((result) => {
            if (result) {
              this.$swal({
                title: 'Registrar Check Out',
                text: '¿Está seguro de realizar esta acción?',
                type: 'success',
                showCancelButton: true,
              }).then((result) => {
                if (result.value) {
                  this.loading = true

                  this.$store.state.services.CheckOutService.store(this.form)
                    .then((r) => {
                      this.loading = false
                      if (r.response) {
                        if (r.response.data.code === 404) {
                          this.$toastr.warning(
                            r.response.data.error,
                            'Advertencia',
                          )
                          return
                        } else if (r.response.data.code === 423) {
                          this.$toastr.warning(
                            r.response.data.error,
                            'Advertencia',
                          )
                          return
                        } else {
                          for (let value of Object.values(r.response.data)) {
                            this.$toastr.error(value, 'Mensaje')
                          }
                        }
                        return
                      }

                      this.$toastr.success(r.data.mensaje, 'Mensaje')
                      this.notificador_audible(this.$store.state.audio.agregar)
                      this.initialize()
                    })
                    .catch((r) => {
                      this.loading = false
                    })
                }
              })
            }
          })
        })
        .catch((error) => {
          this.loading = false
          this.$swal({
            title: error.titulo,
            text: error.text,
            type: 'info',
            showCancelButton: false,
          })
        })
    },

    //Funciones para la firma
    startPainting(event) {
      this.painting = true

      this.lastPos = this.getMousePos(this.canvas, event)
      this.draw(event)
    },

    finishedPainting() {
      this.painting = false
      this.ctx.beginPath()
    },

    draw(event) {
      this.mousePos = this.getMousePos(this.canvas, event)
      if (this.painting) {
        this.ctx.strokeStyle = '#000000'
        this.ctx.beginPath()
        this.ctx.moveTo(this.lastPos.x, this.lastPos.y)
        this.ctx.lineTo(this.mousePos.x, this.mousePos.y)
        this.ctx.lineWidth = 5
        this.ctx.lineCap = 'round'
        this.ctx.stroke()
        this.ctx.closePath()
        this.lastPos = this.mousePos
      }
    },

    getMousePos(canvasDom, mouseEvent) {
      var rect = canvasDom.getBoundingClientRect()

      return {
        x: mouseEvent.clientX - rect.left,
        y: mouseEvent.clientY - rect.top,
      }
    },

    getTouchPos(canvasDom, touchEvent) {
      var rect = canvasDom.getBoundingClientRect()

      return {
        x: touchEvent.touches[0].clientX - rect.left,
        y: touchEvent.touches[0].clientY - rect.top,
      }
    },

    startTouch(event) {
      this.mousePos = this.getTouchPos(this.canvas, event)
      event.preventDefault()
      var touch = event.touches[0]
      var mouseEvent = new MouseEvent('mousedown', {
        clientX: touch.clientX,
        clientY: touch.clientY,
      })

      this.canvas.dispatchEvent(mouseEvent)
    },

    finishedTouch(event) {
      event.preventDefault()
      var mouseEvent = new MouseEvent('mouseup', {})
      this.canvas.dispatchEvent(mouseEvent)
    },

    leaveTouch(event) {
      event.preventDefault() // Prevent scrolling when touching the canvas
      var mouseEvent = new MouseEvent('mouseup', {})
      this.canvas.dispatchEvent(mouseEvent)
    },

    moveTouch(event) {
      event.preventDefault() // Prevent scrolling when touching the canvas
      var touch = event.touches[0]
      var mouseEvent = new MouseEvent('mousemove', {
        clientX: touch.clientX,
        clientY: touch.clientY,
      })
      this.canvas.dispatchEvent(mouseEvent)
    },
  },
}
</script>
