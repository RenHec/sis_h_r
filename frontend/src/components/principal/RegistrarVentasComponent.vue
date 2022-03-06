<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-col cols="12" md="12">
      <v-card>
        <v-overlay :value="loading">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-card-title>
          <span class="headline">Registrar venta</span>
          <v-spacer></v-spacer>
          <v-btn class="ma-2 mb-2" small color="success" @click="initialize">
            Actualizar
          </v-btn>
        </v-card-title>

        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12" md="4">
                <v-radio-group v-model="persona.representacion">
                  <v-radio
                    v-for="representacion in representaciones"
                    :key="representacion"
                    :label="`Representación: ${representacion}`"
                    :value="representacion"
                    data-vv-scope="crear_venta"
                    data-vv-name="representacion"
                    v-validate="'required'"
                    :disabled="loading"
                  ></v-radio>
                </v-radio-group>
                <FormError
                  :attribute_name="'crear_venta.representacion'"
                  :errors_form="errors"
                ></FormError>
              </v-col>
            </v-row>
            <v-row v-if="persona.representacion === 'INDIVIDUAL'">
              <v-col cols="12" md="4">
                <v-text-field
                  counter
                  outlined
                  v-model="persona.nit"
                  type="text"
                  label="nit"
                  data-vv-scope="crear_venta"
                  data-vv-name="nit"
                  v-validate="'max:10'"
                  :loading="loading"
                ></v-text-field>
                <FormError
                  :attribute_name="'crear_venta.nit'"
                  :errors_form="errors"
                ></FormError>
                <ul v-for="(item, index) in filteredList" v-bind:key="index">
                  <li>
                    <a @click="seleccionar_persona(item)">
                      {{ item.nit }}
                    </a>
                  </li>
                </ul>
              </v-col>
              <v-col cols="12" md="4">
                <v-text-field
                  counter
                  outlined
                  v-model="persona.nombres"
                  type="text"
                  label="nombres"
                  data-vv-scope="crear_venta"
                  data-vv-name="nombres"
                  v-validate="
                    'required_if:persona.representacion,INDIVIDUAL|max:50'
                  "
                  :loading="loading"
                ></v-text-field>
                <FormError
                  :attribute_name="'crear_venta.nombres'"
                  :errors_form="errors"
                ></FormError>
              </v-col>
              <v-col cols="12" md="4">
                <v-text-field
                  counter
                  outlined
                  v-model="persona.apellidos"
                  type="text"
                  label="apellidos"
                  data-vv-scope="crear_venta"
                  data-vv-name="apellidos"
                  v-validate="'required|max:50'"
                  :loading="loading"
                ></v-text-field>
                <FormError
                  :attribute_name="'crear_venta.apellidos'"
                  :errors_form="errors"
                ></FormError>
              </v-col>
            </v-row>
            <v-row v-if="persona.representacion === 'JURIDICO'">
              <v-col cols="12" md="4">
                <v-text-field
                  counter
                  outlined
                  v-model="persona.nit"
                  type="text"
                  label="nit"
                  data-vv-scope="crear_venta"
                  data-vv-name="nit"
                  v-validate="'max:10'"
                  :loading="loading"
                ></v-text-field>
                <FormError
                  :attribute_name="'crear_venta.nit'"
                  :errors_form="errors"
                ></FormError>
                <ul v-for="(item, index) in filteredList" v-bind:key="index">
                  <li>
                    <a @click="seleccionar_persona(item)">
                      {{ item.nit }}
                    </a>
                  </li>
                </ul>
              </v-col>
              <v-col cols="12" md="8">
                <v-text-field
                  counter
                  outlined
                  v-model="persona.completo"
                  type="text"
                  label="nombre empresa"
                  data-vv-scope="crear_venta"
                  data-vv-name="nombre empresa"
                  v-validate="
                    'required_if:persona.representacion,JURIDICO|max:100'
                  "
                  :loading="loading"
                ></v-text-field>
                <FormError
                  :attribute_name="'crear_venta.nombre empresa'"
                  :errors_form="errors"
                ></FormError>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" md="6">
                <v-autocomplete
                  v-model="persona.municipios_id"
                  :items="municipios"
                  chips
                  label="Seleccionar departamento y municipio"
                  outlined
                  :clearable="true"
                  :deletable-chips="true"
                  item-text="full_name"
                  item-value="id"
                  return-object
                  v-validate="'required'"
                  data-vv-scope="crear_venta"
                  data-vv-name="departamento y municipio"
                  :loading="loading"
                ></v-autocomplete>
                <FormError
                  :attribute_name="'crear_venta.departamento y municipio'"
                  :errors_form="errors"
                ></FormError>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  counter
                  outlined
                  v-model="persona.direccion"
                  type="text"
                  label="dirección exacta"
                  data-vv-scope="crear_venta"
                  data-vv-name="dirección exacta"
                  v-validate="'max:100'"
                  :loading="loading"
                ></v-text-field>
                <FormError
                  :attribute_name="'crear_venta.dirección exacta'"
                  :errors_form="errors"
                ></FormError>
              </v-col>
              <v-col>
                <hr />
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" md="6">
                <v-select
                  v-model="form.estados_id"
                  :items="estados"
                  chips
                  label="seleccione un tipo de documento por favor"
                  outlined
                  :clearable="true"
                  :deletable-chips="true"
                  item-text="nombre"
                  item-value="id"
                  return-object
                  v-validate="'required'"
                  data-vv-scope="crear_venta"
                  data-vv-name="tipo de documento"
                  :loading="loading"
                ></v-select>
                <FormError
                  :attribute_name="'crear_venta.tipo de documento'"
                  :errors_form="errors"
                ></FormError>
              </v-col>
              <v-col
                cols="12"
                md="6"
                v-if="
                  form.estados_id
                    ? form.estados_id.nombre === 'FACTURA'
                      ? true
                      : false
                    : false
                "
              >
                <v-select
                  v-model="form.tipos_pagos_id"
                  :items="tipos_pagos"
                  chips
                  label="seleccione un tipo de pago por favor"
                  outlined
                  :clearable="true"
                  :deletable-chips="true"
                  item-text="nombre"
                  item-value="id"
                  return-object
                  v-validate="'required'"
                  data-vv-scope="crear_venta"
                  data-vv-name="tipo de pago"
                  :loading="loading"
                ></v-select>
                <FormError
                  :attribute_name="'crear_venta.tipo de pago'"
                  :errors_form="errors"
                ></FormError>
              </v-col>
              <v-col cols="12">
                <hr />
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" md="10">
                <v-row>
                  <v-col cols="12" md="8">
                    <v-select
                      v-model="detalle.productos_id"
                      :items="productos"
                      chips
                      label="seleccione un producto por favor"
                      outlined
                      :clearable="true"
                      :deletable-chips="true"
                      item-text="producto"
                      item-value="stocks_id"
                      return-object
                      v-validate="'required'"
                      data-vv-scope="agregar_detalle"
                      data-vv-name="producto"
                      :loading="loading"
                    ></v-select>
                    <FormError
                      :attribute_name="'agregar_detalle.producto'"
                      :errors_form="errors"
                    ></FormError>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-select
                      v-model="detalle.asignar_presentaciones_id"
                      :items="
                        detalle.productos_id
                          ? detalle.productos_id.presentacion
                          : []
                      "
                      chips
                      label="seleccione una presentación por favor"
                      outlined
                      :clearable="true"
                      :deletable-chips="true"
                      item-text="nombre_completo"
                      item-value="id"
                      return-object
                      v-validate="'required'"
                      data-vv-scope="agregar_detalle"
                      data-vv-name="presentación"
                      :loading="loading"
                      :disabled="detalle.productos_id ? false : true"
                    ></v-select>
                    <FormError
                      :attribute_name="'agregar_detalle.presentación'"
                      :errors_form="errors"
                    ></FormError>
                  </v-col>
                  <v-col cols="12" md="10">
                    <v-row>
                      <v-col
                        cols="12"
                        md="4"
                        v-if="
                          detalle.productos_id &&
                          detalle.asignar_presentaciones_id
                        "
                      >
                        <label>
                          stock disponible
                          {{
                            parseInt(
                              detalle.productos_id.stock /
                                detalle.asignar_presentaciones_id.debitar_stock,
                            )
                          }}
                          {{ detalle.asignar_presentaciones_id.nombre }}
                        </label>
                        <vue-number-input
                          v-model="detalle.cantidad"
                          :min="1"
                          :max="
                            stock_disponible(
                              detalle.productos_id.stock,
                              detalle.asignar_presentaciones_id.debitar_stock,
                            )
                          "
                          controls
                          placeholder="cantidad"
                          data-vv-scope="agregar_detalle"
                          data-vv-name="cantidad"
                          :disabled="loading"
                        ></vue-number-input>
                      </v-col>
                      <v-col
                        cols="12"
                        md="4"
                        v-if="detalle.asignar_presentaciones_id"
                      >
                        <v-text-field
                          counter
                          outlined
                          v-model="detalle.asignar_presentaciones_id.precio"
                          type="text"
                          label="precio de venta"
                          data-vv-scope="agregar_detalle"
                          data-vv-name="precio de venta"
                          v-validate="'required|decimal:2'"
                          prefix="Q"
                          :loading="loading"
                        ></v-text-field>
                        <FormError
                          :attribute_name="'agregar_detalle.precio de venta'"
                          :errors_form="errors"
                        ></FormError>
                      </v-col>
                      <v-col
                        cols="12"
                        md="3"
                        class="px-4 pt-4 pb-3 text-center"
                        style="cursor: pointer;"
                      >
                        <label>Sub Total Q</label>
                        <p class="display-1 font-weight-black text-md-right">
                          {{ sub_precio }}
                        </p>
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
              </v-col>
              <v-col cols="12" md="2">
                <v-row>
                  <v-col cols="12" md="12" v-if="detalle.productos_id">
                    <div class="text-center">
                      <v-avatar size="200" tile>
                        <img
                          :src="detalle.productos_id.logo"
                          :alt="detalle.productos_id.producto"
                        />
                      </v-avatar>
                    </div>
                  </v-col>
                  <v-col cols="12" md="12">
                    <v-btn
                      block
                      x-large
                      class="ma-2 mb-2"
                      color="success darken-1"
                      @click="agregar_detalle('agregar_detalle')"
                      :loading="loading"
                    >
                      Agregar
                    </v-btn>
                  </v-col>
                </v-row>
              </v-col>

              <v-col cols="12"><hr /></v-col>
            </v-row>
            <v-row>
              <v-col cols="12" md="12">
                <v-simple-table dark>
                  <template v-slot:default>
                    <thead>
                      <tr>
                        <th class="text-center">
                          ARTICULO
                        </th>
                        <th class="text-center">
                          CANTIDAD
                        </th>
                        <th class="text-center">
                          PRODUCTO
                        </th>
                        <th class="text-center">
                          PRESENTACION
                        </th>
                        <th class="text-center">
                          PRECIO
                        </th>
                        <th class="text-center">
                          SUB TOTAL Q
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(item, key) in form.detalle" :key="key">
                        <td class="ma-2 mb-2">
                          <div class="my-2">
                            <v-btn
                              color="error"
                              fab
                              large
                              dark
                              @click="quitar_detalle(item)"
                            >
                              {{ key + 1 }}
                            </v-btn>
                          </div>
                        </td>
                        <td width="14%">
                          <br />
                          <label>
                            stock disponible
                            {{
                              parseInt(
                                item.productos_id.stock /
                                  item.asignar_presentaciones_id.debitar_stock,
                              )
                            }}
                            {{ item.asignar_presentaciones_id.nombre }}
                          </label>
                          <vue-number-input
                            v-model="item.cantidad"
                            :min="1"
                            :max="
                              stock_disponible(
                                item.productos_id.stock,
                                item.asignar_presentaciones_id.debitar_stock,
                              )
                            "
                            controls
                            placeholder="cantidad"
                          ></vue-number-input>
                        </td>
                        <td width="16%">
                          <br />
                          <p class="font-italic green--text">
                            {{ item.productos_id.producto }}
                          </p>
                        </td>
                        <td width="30%">
                          <br />
                          <v-select
                            v-model="item.asignar_presentaciones_id"
                            :items="item.productos_id.presentacion"
                            chips
                            label="seleccione una presentación por favor"
                            outlined
                            :clearable="true"
                            :deletable-chips="true"
                            item-text="nombre_completo"
                            item-value="id"
                            return-object
                            v-validate="'required'"
                            data-vv-scope="crear_venta"
                            :data-vv-name="`presentación${key}`"
                          ></v-select>
                          <FormError
                            :attribute_name="`crear_venta.presentación${key}`"
                            :errors_form="errors"
                          ></FormError>
                        </td>
                        <td>
                          <br />
                          <v-text-field
                            counter
                            outlined
                            v-model="item.asignar_presentaciones_id.precio"
                            type="text"
                            label="precio de venta"
                            data-vv-scope="crear_venta"
                            :data-vv-name="`precio de venta${key}`"
                            v-validate="'required|decimal:2'"
                            prefix="Q"
                          ></v-text-field>
                          <FormError
                            :attribute_name="`crear_venta.precio de venta${key}`"
                            :errors_form="errors"
                          ></FormError>
                        </td>
                        <td width="20%">
                          <br />
                          <p class="title text-md-right">
                            {{
                              calcular_sub_total(
                                item.cantidad,
                                item.asignar_presentaciones_id.precio,
                              )
                            }}
                          </p>
                        </td>
                      </tr>
                    </tbody>
                    <tfoot>
                      <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                          <hr />
                          <br />
                          <p class="display-1 font-weight-black text-md-right">
                            Total
                          </p>
                        </td>
                        <td>
                          <hr />
                          <br />
                          <p class="display-1 font-weight-black text-md-right">
                            {{ total_detalle }}
                          </p>
                        </td>
                      </tr>
                      <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                          <hr />
                          <br />
                          <p class="display-1 font-weight-black text-md-right">
                            Paga
                          </p>
                        </td>
                        <td>
                          <hr />
                          <br />
                          <v-text-field
                            counter
                            outlined
                            v-model="cambio"
                            type="text"
                            label="cobrar"
                            data-vv-scope="crear_venta"
                            data-vv-name="cobrar"
                            v-validate="'required|decimal:2'"
                            prefix="Q"
                            :loading="loading"
                          ></v-text-field>
                          <FormError
                            :attribute_name="'crear_venta.cobrar'"
                            :errors_form="errors"
                          ></FormError>
                        </td>
                      </tr>
                      <tr v-if="cambio">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                          <hr />
                          <br />
                          <p class="display-1 font-weight-black text-md-right">
                            Vuelto
                          </p>
                        </td>
                        <td>
                          <hr />
                          <br />
                          <p class="display-1 font-weight-black text-md-right">
                            {{ cambio_venta }}
                          </p>
                        </td>
                      </tr>
                    </tfoot>
                  </template>
                </v-simple-table>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="red darken-1" @click="limpiar">
            Cancelar
          </v-btn>
          <v-btn color="blue darken-1" @click="crear_venta('crear_venta')">
            {{
              form.estados_id
                ? form.estados_id.nombre === 'FACTURA'
                  ? 'Vender'
                  : 'Cotizar'
                : null
            }}
          </v-btn>
        </v-card-actions>
      </v-card>

      <v-dialog v-model="dialog_print" max-width="50%" persistent>
        <v-card color="success">
          <v-overlay :value="loading">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
          </v-overlay>
          <v-card-title>
            <span class="headline">
              Imprimir documento
            </span>
            <v-spacer></v-spacer>
            <v-btn color="red darken-1" @click="cerrar_print()">
              Cerrar
            </v-btn>
          </v-card-title>

          <v-card-text v-if="documento_imprimir">
            <v-container>
              <v-row>
                <v-col cols="12" md="12">
                  <embed
                    :src="documento_imprimir"
                    type="application/pdf"
                    width="100%"
                    height="930"
                    style="
                      overflow: hidden;
                      overflow-x: hidden;
                      overflow-y: hidden;
                      border: 1px solid #666ccc;
                    "
                    title="Comprobante"
                    frameborder="1"
                  />
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-col>
  </v-row>
</template>

<script>
import FormError from '../shared/FormError'

export default {
  name: 'RegistrarVentas',
  components: { FormError },
  data() {
    return {
      loading: false,
      dialogo_fecha: false,
      dialogo_vencimiento: false,

      representaciones: ['INDIVIDUAL', 'JURIDICO'],
      form: {
        id: 0,
        movimientos_id: {
          id: 4,
        },
        empresas_id: null,
        persona: null,
        fecha: null,
        numero_documento: null,
        estados_id: null,
        tipos_pagos_id: null,
        detalle: [],
        cambio: 0,
      },

      cambio: null,

      movimientos: [],
      estados: [],
      tipos_pagos: [],
      personas: [],
      municipios: [],
      productos: [],

      persona: {
        representacion: 'INDIVIDUAL',
        nombres: null,
        apellidos: null,
        completo: null,
        nit: null,
        direccion: null,
        es_proveedor: false,
        municipios_id: null,
      },

      detalle: {
        productos_id: null,
        cantidad: null,
        precio: null,
        vencimiento: null,
        asignar_presentaciones_id: null,
      },

      dialog_print: false,
      documento_imprimir: null,
    }
  },

  created() {
    this.initialize()
  },

  methods: {
    limpiar() {
      this.form.id = 0
      this.form.movimientos_id = {
        id: 4,
      }
      this.form.empresas_id = this.$store.state.empresa
      this.form.persona = null
      this.form.fecha = null
      this.form.numero_documento = null
      this.form.estados_id = null
      this.form.tipos_pagos_id = null
      this.form.detalle = []
      this.form.cambio = 0

      this.cambio = null

      this.persona.representacion = 'INDIVIDUAL'
      this.persona.nombres = null
      this.persona.apellidos = null
      this.persona.completo = null
      this.persona.nit = null
      this.persona.direccion = null
      this.persona.es_proveedor = false
      this.persona.municipios_id = null

      this.detalle.productos_id = null
      this.detalle.cantidad = null
      this.detalle.precio = null
      this.detalle.vencimiento = null
      this.detalle.asignar_presentaciones_id = null

      this.$validator.reset()
      this.$validator.reset()
    },

    initialize() {
      this.loading = true

      Promise.all([
        this.getMovimiento(),
        this.getEstado(),
        this.getTipos(),
        this.getPersonas(),
        this.getProuctos(),
        this.getMunicipios(),
      ])
        .then(() => {
          this.limpiar()
          this.loading = false
        })
        .catch((error) => {
          this.$toastr.error('Ocurrió un error: ' + error, 'Error')
        })
    },

    getMovimiento() {
      this.$store.state.services.selectController
        .movimiento()
        .then((r) => {
          this.movimientos = r.data.data
        })
        .catch((r) => {})
    },

    getEstado() {
      this.$store.state.services.selectController
        .estado(4)
        .then((r) => {
          this.estados = r.data.data
        })
        .catch((r) => {})
    },

    getTipos() {
      this.$store.state.services.selectController
        .tipo_pago(4)
        .then((r) => {
          this.tipos_pagos = r.data.data
        })
        .catch((r) => {})
    },

    getPersonas() {
      this.$store.state.services.selectController
        .cliente()
        .then((r) => {
          this.personas = r.data.data
        })
        .catch((r) => {})
    },

    getProuctos() {
      this.$store.state.services.selectController
        .producto_sin_id(null, this.$store.state.empresa.id, null)
        .then((r) => {
          this.productos = r.data.data
        })
        .catch((r) => {})
    },

    getMunicipios() {
      this.$store.state.services.selectController
        .municipio()
        .then((r) => {
          this.municipios = r.data.data
        })
        .catch((r) => {})
    },

    seleccionar_persona(item) {
      this.persona.representacion = item.representacion
      this.persona.nombres = item.nombres
      this.persona.apellidos = item.apellidos
      this.persona.completo = item.completo
      this.persona.nit = item.nit
      this.persona.direccion = item.direccion
      this.persona.es_proveedor = false
      this.persona.municipios_id = item.municipios
    },

    agregar_detalle(scope) {
      this.$validator.validateAll(scope).then((result) => {
        if (result) {
          this.loading = true
          let objeto = new Object()
          objeto.productos_id = this.detalle.productos_id
          objeto.cantidad = this.detalle.cantidad
          objeto.precio = this.detalle.asignar_presentaciones_id.precio
          objeto.vencimiento = this.detalle.vencimiento
          objeto.asignar_presentaciones_id = this.detalle.asignar_presentaciones_id

          this.productos.find((element) => {
            if (element.stocks_id == objeto.productos_id.stocks_id) {
              let stock = element.stock
              let debitar = objeto.asignar_presentaciones_id.debitar_stock

              element.stock = stock - objeto.cantidad * debitar
            }
          })

          this.form.detalle.push(objeto)

          this.detalle.productos_id = null
          this.detalle.cantidad = null
          this.detalle.precio = null
          this.detalle.vencimiento = null
          this.detalle.asignar_presentaciones_id = null

          this.$validator.reset()
          this.$validator.reset()

          this.loading = false
        }
      })
    },

    calcular_sub_total(cantidad, precio) {
      let sub = cantidad * precio

      return this.formato_moneda(sub)
    },

    quitar_detalle(item) {
      this.$swal({
        title: 'Eliminar',
        text: '¿Está seguro de realizar esta acción?',
        type: 'error',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.productos.filter((element) => {
            if (element.stocks_id == item.productos_id.stocks_id) {
              element.stock =
                element.stock +
                element.stock / item.asignar_presentaciones_id.debitar_stock
            }
          })
          this.form.detalle.splice(item)
          this.loading = false
        }
      })
    },

    crear_venta(scope) {
      if (this.persona.representacion === 'INDIVIDUAL') {
        if (!this.persona.nombres || !this.persona.apellidos) {
          this.$toastr.info(
            'Es obligatorio el nombre y apellido de la persona',
            'Validación',
          )
          return
        }
      } else {
        if (!this.persona.completo) {
          this.$toastr.info(
            'Es obligatorio el nombre de la empresa',
            'Validación',
          )
          return
        }
      }

      if (this.form.detalle.length === 0) {
        this.$toastr.info(
          'Es necesario que agregue al menos 1 producto a la lista de la venta',
          'Detalle de la venta',
        )
        return
      }

      this.$validator.validateAll(scope).then((result) => {
        if (result) {
          let mensaje = this.form.estados_id
            ? this.form.estados_id.nombre === 'FACTURA'
              ? 'venta'
              : 'cotización'
            : null
          this.form.movimientos_id.id =
            this.form.estados_id.nombre === 'FACTURA' ? 4 : 8
          this.$swal({
            title: `Registrar ${mensaje}`,
            text: '¿Está seguro de realizar esta acción?',
            type: 'success',
            showCancelButton: true,
          }).then((result) => {
            if (result.value) {
              this.loading = true
              this.form.persona = this.persona
              this.$store.state.services.compraVentaController
                .store(this.form)
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

                  this.$toastr.success(r.data.mensaje, 'Mensaje')
                  this.initialize()

                  this.$swal({
                    title: 'Imprimir comprobante',
                    text: '¿Está seguro de realizar esta acción?',
                    type: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Imprimir',
                    allowOutsideClick: () => {
                      const popup = this.$swal.getPopup()
                      popup.classList.remove('swal2-show')
                      setTimeout(() => {
                        popup.classList.add(
                          'animate__animated',
                          'animate__headShake',
                        )
                      })
                      setTimeout(() => {
                        popup.classList.remove(
                          'animate__animated',
                          'animate__headShake',
                        )
                      }, 500)
                      return false
                    },
                  }).then((result) => {
                    if (result.value) {
                      this.loading = true
                      this.$store.state.services.reporteController
                        .ticket(r.data.comprobante)
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
                              for (let value of Object.values(
                                r.response.data,
                              )) {
                                this.$toastr.error(value, 'Mensaje')
                              }
                            }
                            return
                          }

                          this.$toastr.success(r.data.data, 'Mensaje')
                          this.dialog_print = true
                          this.documento_imprimir = r.data.path
                        })
                        .catch((r) => {
                          this.loading = false
                        })
                    }
                  })
                })
                .catch((r) => {
                  this.loading = false
                })
            }
          })
        }
      })
    },

    cerrar_print() {
      this.dialog_print = false
      this.documento_imprimir = false
    },

    formato_moneda(monto) {
      return monto.toLocaleString('es-GT', {
        style: 'currency',
        currency: 'GTQ',
        minimumFractionDigits: 2,
      })
    },

    stock_disponible(stock_actual, quitar_stock) {
      return parseInt(stock_actual / quitar_stock)
    },
  },

  computed: {
    total_detalle() {
      let suma = 0
      this.form.detalle.length > 0
        ? this.form.detalle.forEach((a) => {
            suma += a.cantidad * a.asignar_presentaciones_id.precio
          })
        : 0

      return this.formato_moneda(suma)
    },

    sub_precio() {
      let sub = 0

      if (this.detalle.asignar_presentaciones_id) {
        sub =
          this.detalle.cantidad * this.detalle.asignar_presentaciones_id.precio
      }

      return this.formato_moneda(sub)
    },

    cambio_venta() {
      let suma = 0
      this.form.detalle.length > 0
        ? this.form.detalle.forEach((a) => {
            suma += a.cantidad * a.asignar_presentaciones_id.precio
          })
        : 0

      let resta = this.cambio ? suma - this.cambio : 0
      this.form.cambio = resta

      return this.formato_moneda(resta)
    },

    filteredList() {
      if (this.persona.nit) {
        if (this.persona.nit.replace(' ', '').length > 3) {
          return this.personas.filter((element) => {
            return element.nit
              .replace(' ', '')
              .includes(this.persona.nit.replace(' ', ''))
          })
        }
      } else {
        return []
      }
    },
  },
}
</script>
