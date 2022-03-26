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
            <v-toolbar-title>Reservaciones</v-toolbar-title>
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
                  @click="proceso_check_in(item)"
                >
                  <v-icon x-large>playlist_add_check</v-icon>
                </v-btn>
              </template>
              <span
                v-text="
                  `Registrar Check In a la reservación con código ${item.codigo}`
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
              <v-icon x-large>playlist_add_check</v-icon>
              Check In
            </span>
            <v-spacer></v-spacer>
            <v-btn color="red" text icon @click="dialog = false">
              <v-icon>close</v-icon>
            </v-btn>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-overlay :value="loading">
                <v-progress-circular
                  indeterminate
                  size="64"
                ></v-progress-circular>
              </v-overlay>
              <v-row class="pa-4" justify="space-between">
                <v-col cols="12" md="2" sm="2">
                  <div
                    class="text-h6 white--text text--lighten-1 font-weight-light"
                    style="align-self: center;"
                  >
                    {{
                      `Habitaciones de la reservación con código ${form.codigo}`
                    }}
                  </div>
                  <v-divider></v-divider>
                  <v-list flat>
                    <v-list-item-group :no-action="true" color="indigo">
                      <v-list-item
                        v-for="(item, i) in form.check_in"
                        :key="i"
                        @click="seleecionar_habitacion(i)"
                      >
                        <v-list-item-content>
                          <v-list-item-title
                            v-text="item.habitacion"
                          ></v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list-item-group>
                  </v-list>
                </v-col>
                <v-divider vertical></v-divider>
                <v-col
                  cols="12"
                  md="6"
                  sm="6"
                  class="d-flex text-center"
                  v-if="selected > -1"
                >
                  <v-scroll-y-transition mode="out-in">
                    <div
                      v-if="selected < 0"
                      class="text-h6 white--text text--lighten-1 font-weight-light"
                      style="align-self: center;"
                    >
                      Seleccionar una habitación por favor...
                    </div>
                    <v-card
                      v-else
                      class="pt-6 mx-auto animate__fadeIn"
                      flat
                      max-width="80%"
                      v-bind:key="selected"
                    >
                      <v-card-text>
                        <h3 class="text-h4 mb-2">
                          {{ form.check_in[selected]['habitacion'] }}
                        </h3>
                        <div class="blue--text mb-2">
                          Check In
                        </div>
                      </v-card-text>
                      <v-divider></v-divider>
                      <v-card-text>
                        <v-row
                          align="center"
                          v-for="(producto, index_pro) in form.check_in[
                            selected
                          ]['lista']"
                          v-bind:key="`${selected}-${index_pro}`"
                        >
                          <v-col cols="12" md="2">
                            <v-checkbox
                              v-model="producto.incluir"
                              hide-details
                              class="shrink mr-2 mt-0"
                            ></v-checkbox>
                          </v-col>
                          <v-col cols="12" md="7">
                            <v-chip class="ma-2" color="primary" label small>
                              {{ producto.producto }}
                            </v-chip>
                          </v-col>
                          <v-col cols="12" md="3">
                            <vue-number-input
                              v-model="producto.cantidad"
                              size="small"
                              :min="0"
                              :max="productos[index_pro]['stock_actual']"
                              inline
                              center
                              controls
                              placeholder="stock"
                              rounded
                              @change="actualizar_stock"
                            ></vue-number-input>
                            <br />
                            <small>
                              {{
                                `Stock actual ${productos[index_pro]['stock_actual']}`
                              }}
                            </small>
                          </v-col>
                        </v-row>
                      </v-card-text>
                    </v-card>
                  </v-scroll-y-transition>
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
                    <v-col class="text-center" cols="12">
                      <v-btn small color="red" text @click="limpiar_firma">
                        Volver a firmar
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
  name: 'HReservaciones',
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
          text: '¿Quién reservo?',
          align: 'center',
          value: 'nombre',
        },
        {
          text: 'Check In',
          align: 'center',
          value: 'detalle[0].inicio',
        },
        {
          text: 'Check Out',
          align: 'center',
          value: 'detalle[0].fin',
        },
        {
          text: 'Registrado',
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

      productos: [],
      stock_general: [],
      form: {
        foto: null,
        codigo: null,
        nombre: null,
        descripcion: null,
        check_in: [],
      },

      selected: -1,

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
      this.form.check_in = []
      this.editedIndex = false

      this.selected = -1
      this.stock_general = []

      this.lastPos = this.mousePos

      this.$validator.reset()
      this.$validator.reset()
    },

    getSelects() {
      this.$store.state.services.selectController
        .producto_check_in()
        .then((r) => {
          this.productos = r.data.data
        })
        .catch((r) => {})
    },

    initialize() {
      this.loading = true

      this.$store.state.services.ReservacionService.getAll('r')
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
          this.dialog = false
          this.limpiar()
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

    /*:::::::::::::::::::::: PROCESO CHECK IN ::::::::::::::::::::::::::*/
    proceso_check_in(item) {
      this.loading = true
      this.selected = -1

      this.$store.state.services.selectController
        .producto_check_in()
        .then((r) => {
          if (r.response) {
            this.loading = false
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

          this.productos = r.data
          this.form.foto = null
          this.form.codigo = item.codigo
          this.form.nombre = item.nombre
          this.form.check_in = []

          let detalle = item.detalle

          const unicos = [
            ...new Map(
              detalle.map((item) => [item['h_habitaciones_id'], item]),
            ).values(),
          ]

          //Agregando la lista a la habitacion correspondiente
          unicos.forEach((element) => {
            let objeto = new Object()
            objeto.lista = []
            objeto.h_reservaciones_id = element.h_reservaciones_id
            objeto.h_reservaciones_detalles_id = element.id
            objeto.h_habitaciones_id = element.habitacion.id
            objeto.habitacion = `Habitación #${element.habitacion.numero}`
            this.form.check_in.push(objeto)
          })

          this.dialog = true
          this.loading = false
        })
        .catch((r) => {
          this.loading = false
        })
    },

    actualizar_stock(event) {
      console.log(event)
    },

    seleecionar_habitacion(index) {
      this.loading = true

      let asignar_productos = function asignar(productos, item) {
        return new Promise((resolve, reject) => {
          setTimeout(() => {
            if (item.lista.length === 0) {
              //Objeto para la lista de productos nueva
              productos.forEach((producto) => {
                let nuevo = new Object()
                nuevo.id = producto.id
                nuevo.producto = producto.producto
                nuevo.stock_actual = producto.stock_actual
                nuevo.cantidad = null
                nuevo.incluir = false
                nuevo.consumible = producto.consumible

                item.lista.push(nuevo)
              })

              if (item.lista.length) resolve('Nueva lista de productos')
              else
                reject(
                  `No fue posible asignar productos a la lista del check in de la ${item.habitacion}`,
                )
            } else {
              resolve('Ya tiene lista de productos asignada')
            }
          }, 2000)
        })
      }

      asignar_productos(this.productos, this.form.check_in[index])
        .then((res) => {
          this.$toastr.success(res, 'Lista de productos')
          this.selected = index
          this.loading = false
        })
        .catch((error) => {
          this.loading = false
          this.$swal({
            title: 'Lista de productos',
            text: error,
            type: 'info',
            showCancelButton: false,
          })
        })
    },

    store(scope) {
      //Validacion de check in seleccionados
      this.loading = true
      var resultantes = []
      let verificador_lista_check = function verficador(
        formulario,
        check_list,
        firma_en_blanco,
        canvas,
      ) {
        return new Promise((resolve, reject) => {
          setTimeout(() => {
            let error = new Object()
            check_list.forEach((element) => {
              let quitar_no_seleecionados = element.lista.filter((filtro) => {
                return Boolean(filtro.incluir) === true
              })

              if (quitar_no_seleecionados.length > 0) {
                resultantes.push(quitar_no_seleecionados)
              } else {
                error.titulo = `Check In ${formulario.codigo}`
                error.text = `Es necesario que seleccione elementos de la lista de productos para el Check In de la ${element.habitacion}.`
                reject(error)
              }
            })

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
        this.form,
        this.form.check_in,
        this.$store.state.firma_en_blanco,
        this.canvas.toDataURL(),
      )
        .then((res) => {
          this.loading = false

          this.$validator.validateAll(scope).then((result) => {
            if (result) {
              this.$swal({
                title: 'Registrar Check In',
                text: '¿Está seguro de realizar esta acción?',
                type: 'success',
                showCancelButton: true,
              }).then((result) => {
                if (result.value) {
                  this.loading = true

                  this.form.check_in.forEach((element, index) => {
                    element.lista = resultantes[index]
                  })

                  this.$store.state.services.CheckInService.store(this.form)
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
