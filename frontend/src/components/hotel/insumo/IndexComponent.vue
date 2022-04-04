<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-col cols="12" md="8">
      <v-data-table
        :headers="no_anulados_headers"
        :items="no_anulados"
        :search="no_anulados_search"
        sort-by="calories"
        class="elevation-1"
        dark
        :footer-props="no_anulados_footer"
        calculate-widths
      >
        <template v-slot:top>
          <v-toolbar flat :color="colorTolbar">
            <v-toolbar-title>Insumos</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-text-field
              v-model="no_anulados_search"
              append-icon="search"
              label="Buscar"
              single-line
              hide-details
            ></v-text-field>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-dialog
              v-model="dialog"
              color="primary"
              fullscreen
              persistent
              transition="dialog-bottom-transition"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  :loading="dialog"
                  :disabled="dialog"
                  @click="dialog = true"
                  color="primary"
                  dark
                  class="mb-2"
                  v-bind="attrs"
                  v-on="on"
                >
                  Agregar
                </v-btn>
              </template>

              <v-overlay :value="loading">
                <v-progress-circular
                  indeterminate
                  size="64"
                ></v-progress-circular>
              </v-overlay>

              <v-card color="green darken-4" dark>
                <v-card-text>
                  <v-container>
                    <v-row>
                      <!-- DATOS PERSONALES -->
                      <v-col cols="12" md="3">
                        <v-row>
                          <v-col cols="12" md="12"></v-col>
                          <v-col cols="12" md="4">
                            <p class="text-h4">
                              {{ formTitle }}
                            </p>
                          </v-col>
                          <v-col cols="12" md="8">
                            <v-text-field
                              filled-inverted
                              suffix
                              dense
                              dark
                              prepend-inner-icon="fiber_new"
                              counter
                              v-model="form.documento"
                              type="text"
                              label="documento"
                              data-vv-scope="crear"
                              data-vv-name="documento"
                              v-validate="'required'"
                              hint="El número de documento de compra, en su defecto escribir 0"
                              persistent-hint
                            ></v-text-field>
                            <FormError
                              :attribute_name="'crear.documento'"
                              :errors_form="errors"
                            ></FormError>
                          </v-col>
                          <v-col cols="12" md="12">
                            Datos personales del proveedor
                          </v-col>
                          <v-col cols="12" md="6">
                            <v-text-field
                              filled-inverted
                              suffix
                              dense
                              dark
                              prepend-inner-icon="fiber_new"
                              counter
                              v-model="form.nit"
                              type="text"
                              label="nit"
                              data-vv-scope="crear"
                              data-vv-name="nit"
                              v-validate="'required'"
                              hint="El número de nit del proveedor, caso contrario CF"
                              persistent-hint
                            ></v-text-field>
                            <FormError
                              :attribute_name="'crear.nit'"
                              :errors_form="errors"
                            ></FormError>
                          </v-col>
                          <v-col cols="12" md="6" v-if="mostrar_nit">
                            <strong>Resultados de la busqueda</strong>
                            <ul
                              v-for="(item, index) in filteredList"
                              v-bind:key="index"
                            >
                              <li>
                                <v-chip
                                  class="ma-2"
                                  color="primary"
                                  text-color="white"
                                  small
                                  @click="seleccionar_cliente(item)"
                                >
                                  <v-avatar left>
                                    <v-icon dense color="cyan lighten-2">
                                      contacts
                                    </v-icon>
                                  </v-avatar>
                                  {{ item.nit }}
                                </v-chip>
                              </li>
                            </ul>
                          </v-col>
                          <v-col cols="12" md="12">
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
                              v-validate="'required'"
                              hint="El nombre del proveedor, caso contrario CF"
                              persistent-hint
                            ></v-text-field>
                            <FormError
                              :attribute_name="'crear.nombre'"
                              :errors_form="errors"
                            ></FormError>
                          </v-col>
                          <v-col cols="12" md="12">
                            <v-select
                              filled-inverted
                              suffix
                              dense
                              dark
                              prepend-inner-icon="view_carousel"
                              v-model="form.municipios_id"
                              :items="municipios"
                              label="seleccione un municipio por favor"
                              :clearable="true"
                              :deletable-chips="true"
                              item-text="nombre"
                              item-value="id"
                              return-object
                              v-validate="'required'"
                              data-vv-scope="crear"
                              data-vv-name="municipio"
                            ></v-select>
                            <FormError
                              :attribute_name="'crear.municipio'"
                              :errors_form="errors"
                            ></FormError>
                          </v-col>
                          <v-col cols="12" md="12">
                            <v-text-field
                              filled-inverted
                              suffix
                              dense
                              dark
                              prepend-inner-icon="fiber_new"
                              counter
                              v-model="form.direcciones"
                              type="text"
                              label="dirección"
                              hint="dirección, caso contrario dejar en blanco"
                              persistent-hint
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" md="12">
                            <v-row>
                              <v-col cols="12" md="5">
                                <v-text-field
                                  filled-inverted
                                  suffix
                                  dense
                                  dark
                                  prepend-inner-icon="fiber_new"
                                  counter
                                  v-model="form.telefonos"
                                  type="text"
                                  label="teléfono"
                                  hint="número de teléfono, caso contrario dejar en blanco"
                                  persistent-hint
                                ></v-text-field>
                              </v-col>
                            </v-row>
                            <v-row>
                              <v-col cols="12" md="8">
                                <v-text-field
                                  filled-inverted
                                  suffix
                                  dense
                                  dark
                                  prepend-inner-icon="fiber_new"
                                  counter
                                  v-model="form.emails"
                                  type="text"
                                  label="correo electrónico"
                                  hint="correo electrónico, caso contrario dejar en blanco"
                                  persistent-hint
                                ></v-text-field>
                              </v-col>
                            </v-row>
                          </v-col>
                        </v-row>
                      </v-col>
                      <!-- DATOS DEL INSUMO DETALLE -->
                      <v-col cols="12" md="9">
                        <v-card
                          elevation="24"
                          :loading="loading"
                          class="mx-auto"
                          color="light-blue darken-2"
                        >
                          <v-card-text>
                            <p class="text-h5 text--white">
                              <v-btn
                                class="ma-2"
                                small
                                color="success"
                                @click="agregar_detalle"
                                dark
                              >
                                Agregar
                              </v-btn>
                              {{
                                `al detalle de la compra número ${form.documento}`
                              }}
                            </p>
                          </v-card-text>
                          <v-divider dark></v-divider>
                          <v-card-text>
                            <v-simple-table dark dense>
                              <template v-slot:default>
                                <thead>
                                  <tr>
                                    <th class="text-center">
                                      Cantidad
                                    </th>
                                    <th class="text-center">
                                      Producto
                                    </th>
                                    <th class="text-center">
                                      Precio
                                    </th>
                                    <th class="text-center">
                                      Descuento
                                    </th>
                                    <th class="text-center">
                                      Sub Total
                                    </th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr
                                    v-for="(item,
                                    index) in form.h_insumos_detalles"
                                    :key="index"
                                  >
                                    <td class="text-center">
                                      <br />
                                      <vue-number-input
                                        v-model="item.cantidad"
                                        size="small"
                                        :min="1"
                                        :max="1500"
                                        inline
                                        center
                                        controls
                                        placeholder="cantidad"
                                        rounded
                                      ></vue-number-input>
                                      <br />
                                    </td>
                                    <td class="text-center">
                                      <br />
                                      <v-autocomplete
                                        filled-inverted
                                        suffix
                                        dense
                                        dark
                                        prepend-inner-icon="view_carousel"
                                        v-model="item.h_productos_id"
                                        :items="producto_insumo"
                                        label="Seleccionar producto"
                                        :clearable="true"
                                        :deletable-chips="true"
                                        item-text="producto"
                                        item-value="id"
                                        return-object
                                        v-validate="'required'"
                                        data-vv-scope="crear"
                                        :data-vv-name="`producto de la fila ${index}`"
                                      >
                                        <template v-slot:no-data>
                                          <v-row
                                            align="center"
                                            justify="space-around"
                                          >
                                            <v-btn
                                              text
                                              block
                                              shaped
                                              @click="dialog_producto"
                                            >
                                              Agregar producto nuevo
                                            </v-btn>
                                          </v-row>
                                        </template>
                                      </v-autocomplete>
                                      <FormError
                                        :attribute_name="`crear.producto de la fila ${index}`"
                                        :errors_form="errors"
                                      ></FormError>
                                      <br />
                                    </td>
                                    <td class="text-center">
                                      <br />
                                      <v-text-field
                                        filled-inverted
                                        suffix
                                        dense
                                        dark
                                        prefix="Q"
                                        counter
                                        v-model="item.precio"
                                        type="text"
                                        label="precio"
                                        data-vv-scope="crear"
                                        :data-vv-name="`precio de la fila ${index}`"
                                        v-validate="
                                          'required|decimal:2|min_value:0'
                                        "
                                        hint="Precio costo"
                                        persistent-hint
                                      ></v-text-field>
                                      <FormError
                                        :attribute_name="`crear.precio de la fila ${index}`"
                                        :errors_form="errors"
                                      ></FormError>
                                      <br />
                                    </td>
                                    <td class="text-center">
                                      <br />
                                      <v-text-field
                                        filled-inverted
                                        suffix
                                        dense
                                        dark
                                        prefix="Q"
                                        counter
                                        v-model="item.descuento"
                                        type="text"
                                        label="descuento"
                                        data-vv-scope="crear"
                                        :data-vv-name="`descuento de la fila ${index}`"
                                        v-validate="
                                          'required|decimal:2|min_value:0'
                                        "
                                        hint="Descuento del producto"
                                        persistent-hint
                                      ></v-text-field>
                                      <FormError
                                        :attribute_name="`crear.descuento de la fila ${index}`"
                                        :errors_form="errors"
                                      ></FormError>
                                      <br />
                                    </td>
                                    <td
                                      class="text-right"
                                      @click="quitar_detalle"
                                    >
                                      <p class="text-h5 text--white">
                                        {{
                                          formato_moneda(
                                            item.cantidad,
                                            item.precio,
                                            item.descuento,
                                          )
                                        }}
                                      </p>
                                    </td>
                                  </tr>
                                </tbody>
                                <tfoot>
                                  <tr>
                                    <td class="text-right" colspan="3">
                                      <p class="text-h4 text--white">Total</p>
                                    </td>
                                    <td class="text-right" colspan="2">
                                      <p class="text-h3 text--white">
                                        {{ total }}
                                      </p>
                                    </td>
                                  </tr>
                                </tfoot>
                              </template>
                            </v-simple-table>
                          </v-card-text>

                          <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="red darken-1" @click="close">
                              Cancelar
                            </v-btn>
                            <v-btn
                              color="green darken-1"
                              @click="validar_formulario('crear')"
                            >
                              Guardar
                            </v-btn>
                          </v-card-actions>
                        </v-card>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>

                <v-row justify="center">
                  <v-dialog
                    v-model="form_producto.dialog"
                    persistent
                    max-width="50%"
                  >
                    <v-card>
                      <v-card-title>
                        <span class="text-h5">Crear producto</span>
                      </v-card-title>
                      <v-card-subtitle>
                        * Indica todos los campos obligatorios
                      </v-card-subtitle>
                      <v-divider></v-divider>
                      <v-card-text>
                        <v-container>
                          <v-row>
                            <v-col cols="12" md="12">
                              <v-text-field
                                class="mx-2"
                                prepend-icon="fiber_new"
                                counter
                                outlined
                                v-model="form_producto.nombre"
                                type="text"
                                label="* nombre del produto"
                                data-vv-scope="crear_producto"
                                data-vv-name="nombre del produto"
                                v-validate="'required|max:100'"
                              ></v-text-field>
                              <FormError
                                :attribute_name="'crear_producto.nombre del produto'"
                                :errors_form="errors"
                              ></FormError>
                            </v-col>
                            <v-col cols="12" md="12">
                              <v-textarea
                                class="mx-2"
                                rows="2"
                                prepend-icon="fiber_new"
                                counter
                                outlined
                                v-model="form_producto.descripcion"
                                type="text"
                                label="* descripción"
                                data-vv-scope="crear_producto"
                                data-vv-name="descripción"
                                v-validate="'required|max:500'"
                              ></v-textarea>
                              <FormError
                                :attribute_name="'crear_producto.descripción'"
                                :errors_form="errors"
                              ></FormError>
                            </v-col>
                            <v-col cols="12" md="4">
                              <v-text-field
                                class="mx-2"
                                prepend-icon="fiber_new"
                                counter
                                outlined
                                v-model="form_producto.stock_inicial"
                                type="number"
                                label="stock inicial"
                                data-vv-scope="crear_producto"
                                data-vv-name="stock inicial"
                                v-validate="'integer|min_value:0'"
                                hint="Debe indicar la cantidad con la que inicia el stock, si no la sabe coloque el valor 0"
                                persistent-hint
                              ></v-text-field>
                              <FormError
                                :attribute_name="'crear_producto.stock inicial'"
                                :errors_form="errors"
                              ></FormError>
                            </v-col>
                            <v-col cols="12" md="8">
                              <v-row class="text-h5 text-center">
                                <v-col cols="6">
                                  <v-checkbox
                                    v-model="form_producto.consumible"
                                    :label="`${
                                      form_producto.consumible
                                        ? 'SI, es consumible'
                                        : 'NO, es consumible'
                                    }`"
                                  ></v-checkbox>
                                </v-col>
                                <v-col cols="6">
                                  <v-checkbox
                                    v-model="form_producto.check_in"
                                    :label="`${
                                      form_producto.check_in
                                        ? 'SI, aplica en la lista Check In'
                                        : 'NO, aplica en la lista Check In'
                                    }`"
                                  ></v-checkbox>
                                </v-col>
                              </v-row>
                            </v-col>
                          </v-row>
                        </v-container>
                      </v-card-text>

                      <v-card-actions>
                        <v-btn
                          color="red darken-1"
                          @click="form_producto.dialog = false"
                        >
                          Cancelar
                        </v-btn>
                        <v-spacer></v-spacer>
                        <v-btn
                          color="blue darken-1"
                          @click="agregar_producto('crear_producto')"
                        >
                          Guardar
                        </v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-dialog>
                </v-row>
              </v-card>
            </v-dialog>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-btn color="white" small @click="initialize">
              <v-icon :color="colorTolbar">sync</v-icon>
            </v-btn>
          </v-toolbar>
        </template>
        <template v-slot:item.sub_total="{ item }">
          <p class="text-h5">
            {{ formato_moneda(1, item.sub_total, 0) }}
          </p>
        </template>
        <template v-slot:item.descuento="{ item }">
          <p class="text-h5">
            {{ formato_moneda(1, item.descuento, 0) }}
          </p>
        </template>
        <template v-slot:item.total="{ item }">
          <p class="text-h5">
            {{ formato_moneda(1, item.total, 0) }}
          </p>
        </template>
        <template v-slot:item.anular="{ item }">
          <v-tooltip bottom color="red lighten-2">
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                class="ma-2"
                text
                icon
                color="red lighten-2"
                @click="destroy(item)"
                dark
                v-bind="attrs"
                v-on="on"
              >
                <v-icon>block</v-icon>
              </v-btn>
            </template>
            <span v-text="`Anular documento número ${item.documento}`"></span>
          </v-tooltip>
        </template>
        <template v-slot:item.detalle="{ item }">
          <v-tooltip bottom color="teal lighten-2">
            <template v-slot:activator="{ on, attrs }">
              <v-chip
                class="ma-2"
                color="teal"
                dark
                v-bind="attrs"
                v-on="on"
                @click="ver_detalle(item)"
              >
                Productos
                <v-avatar right class="teal darken-4">
                  {{ item.detalle.length }}
                </v-avatar>
              </v-chip>
            </template>
            <span
              v-text="
                `Click para ver más información del número de documento ${item.documento}`
              "
            ></span>
          </v-tooltip>
        </template>
        <template v-slot:no-data>
          <br />
          <v-alert type="error">No hay información para mostrar.</v-alert>
        </template>
      </v-data-table>
    </v-col>

    <v-col cols="12" md="4">
      <v-data-table
        :headers="anulados_headers"
        :items="anulados"
        :search="anulados_search"
        sort-by="calories"
        class="elevation-1"
        dark
        :footer-props="anulados_footer"
        calculate-widths
      >
        <template v-slot:top>
          <v-toolbar flat :color="colorTolbar">
            <v-toolbar-title>Insumos anulados</v-toolbar-title>
            <v-divider class="mx-4" inset vertical></v-divider>
            <v-text-field
              v-model="no_anulados_search"
              append-icon="search"
              label="Buscar"
              single-line
              hide-details
            ></v-text-field>
          </v-toolbar>
        </template>
        <template v-slot:item.total="{ item }">
          <p class="text-h5">
            {{ formato_moneda(1, item.total, 0) }}
          </p>
        </template>
        <template v-slot:no-data>
          <br />
          <v-alert dense type="error">No hay información para mostrar.</v-alert>
        </template>
      </v-data-table>
    </v-col>

    <v-col cols="12" md="12">
      <v-dialog
        v-model="dialog_detalle"
        transition="dialog-top-transition"
        max-width="80%"
        persistent
        dark
      >
        <v-card v-if="detalle" elevation="4" dark shaped>
          <v-toolbar color="primary" dark>
            {{ `Información del documento ${detalle.documento}` }}
            <v-spacer></v-spacer>
            <v-btn color="red" text icon @click="dialog_detalle = false">
              <v-icon>close</v-icon>
            </v-btn>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12" md="12">
                  <p class="text-h5">
                    Proveedor
                  </p>
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field
                    v-model="detalle.proveedor.nit"
                    label="NIT"
                    filled
                    shaped
                    dense
                    disabled
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="9">
                  <v-text-field
                    v-model="detalle.nombre_proveedor"
                    label="Nombre"
                    filled
                    shaped
                    dense
                    disabled
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="5">
                  <v-text-field
                    v-model="detalle.proveedor.municipio.full_name"
                    label="Departamento / Municipio"
                    filled
                    shaped
                    dense
                    disabled
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="7">
                  <v-text-field
                    v-model="detalle.proveedor.direccions"
                    label="Dirección"
                    filled
                    shaped
                    dense
                    disabled
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                  <v-text-field
                    v-model="detalle.proveedor.telefonos"
                    label="Teléfono"
                    filled
                    shaped
                    dense
                    disabled
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                  <v-text-field
                    v-model="detalle.proveedor.emails"
                    label="Correo electrónico"
                    filled
                    shaped
                    dense
                    disabled
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="12">
                  <v-divider></v-divider>
                </v-col>
                <v-col cols="12" md="12">
                  <p class="text-h5">
                    Detalle
                  </p>
                </v-col>
                <v-col cols="12" md="12">
                  <v-simple-table dark dense>
                    <template v-slot:default>
                      <tbody>
                        <tr
                          v-for="producto in detalle.detalle"
                          :key="`P${producto.id}`"
                        >
                          <td>
                            <br />
                            <v-text-field
                              v-model="producto.cantidad"
                              label="Cantidad"
                              filled
                              shaped
                              dense
                              disabled
                            ></v-text-field>
                          </td>
                          <td>
                            <br />
                            <v-text-field
                              v-model="producto.producto"
                              label="Producto"
                              filled
                              shaped
                              dense
                              disabled
                            ></v-text-field>
                          </td>
                          <td>
                            <br />
                            <v-text-field
                              v-model="producto.precio"
                              label="Precio"
                              prefix="Q"
                              filled
                              shaped
                              dense
                              disabled
                            ></v-text-field>
                          </td>
                          <td>
                            <br />
                            <v-text-field
                              v-model="producto.descuento"
                              label="Descuento"
                              prefix="Q"
                              filled
                              shaped
                              dense
                              disabled
                            ></v-text-field>
                          </td>
                          <td>
                            <br />
                            <v-text-field
                              v-model="producto.sub_total"
                              label="Sub Total"
                              prefix="Q"
                              filled
                              shaped
                              dense
                              disabled
                            ></v-text-field>
                          </td>
                        </tr>
                      </tbody>
                      <tfoot>
                        <tr>
                          <td colspan="3"></td>
                          <td>
                            <br />
                            <v-text-field
                              v-model="detalle.sub_total"
                              label="Sub Total"
                              prefix="Q"
                              filled
                              shaped
                              dense
                              disabled
                            ></v-text-field>
                          </td>
                          <td>
                            <br />
                            <v-text-field
                              v-model="detalle.descuento"
                              label="Descuento"
                              prefix="Q"
                              filled
                              shaped
                              dense
                              disabled
                            ></v-text-field>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="3"></td>
                          <td colspan="2">
                            <br />
                            <v-text-field
                              v-model="detalle.total"
                              label="Total"
                              prefix="Q"
                              filled
                              shaped
                              dense
                              disabled
                            ></v-text-field>
                          </td>
                        </tr>
                      </tfoot>
                    </template>
                  </v-simple-table>
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
import FormError from '../../shared/FormError.vue'
export default {
  name: 'HInsumo',
  components: {
    FormError,
  },
  data() {
    return {
      loading: false,
      dialog: false,

      no_anulados_search: '',
      no_anulados_headers: [
        {
          text: 'Documento',
          align: 'start',
          value: 'documento',
        },
        {
          text: 'Proveedor',
          align: 'start',
          value: 'nombre_proveedor',
        },
        {
          text: 'Sub Total',
          align: 'start',
          value: 'sub_total',
        },
        {
          text: 'Descuento',
          align: 'start',
          value: 'descuento',
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
        {
          text: 'Anular',
          align: 'start',
          value: 'anular',
          sortable: false,
        },
        {
          text: 'Detalle',
          align: 'start',
          value: 'detalle',
          sortable: false,
        },
      ],
      no_anulados_footer: {
        showFirstLastPage: true,
        firstIcon: 'mdi-arrow-collapse-left',
        lastIcon: 'mdi-arrow-collapse-right',
        prevIcon: 'mdi-minus',
        nextIcon: 'mdi-plus',
      },
      no_anulados: [],

      anulados_search: '',
      anulados_headers: [
        {
          text: 'Documento',
          align: 'start',
          value: 'documento',
        },
        {
          text: 'Proveedor',
          align: 'start',
          value: 'nombre_proveedor',
        },
        {
          text: 'Total',
          align: 'start',
          value: 'total',
        },
        {
          text: 'Anulación',
          align: 'start',
          value: 'updated_at',
        },
      ],
      anulados_footer: {
        showFirstLastPage: true,
        firstIcon: 'mdi-arrow-collapse-left',
        lastIcon: 'mdi-arrow-collapse-right',
        prevIcon: 'mdi-minus',
        nextIcon: 'mdi-plus',
      },
      anulados: [],

      editedIndex: false,

      proveedores: [],
      producto_insumo: [],
      municipios: [],
      form: {
        id: 0,
        documento: null,
        nit: null,
        nombre: null,
        telefonos: null,
        emails: null,
        direcciones: null,
        municipios_id: null,
        h_insumos_detalles: [],
      },

      form_producto: {
        dialog: false,
        nombre: null,
        descripcion: null,
        consumible: false,
        stock_inicial: null,
        check_in: false,
      },

      detalle: null,
      dialog_detalle: false,

      mostrar_nit: false,
    }
  },
  computed: {
    formTitle() {
      return !this.editedIndex ? 'Agregar' : 'Editar'
    },

    colorTolbar() {
      return !this.editedIndex ? 'success' : 'warning'
    },

    filteredList() {
      if (this.form.nit) {
        let encontrados = this.proveedores.filter((element) => {
          return element.nit.toUpperCase().includes(this.form.nit.toUpperCase())
        })
        this.mostrar_nit = true
        return encontrados.slice(encontrados.length - 5)
      } else {
        this.mostrar_nit = false
        return []
      }
    },

    total() {
      let total = 0
      this.form.h_insumos_detalles.forEach((element) => {
        let cantidad_no_null = element.cantidad ? element.cantidad : 0
        let precio_no_null = element.precio ? element.precio : 0
        let descuento_no_null = element.descuento ? element.descuento : 0
        let monto =
          parseInt(cantidad_no_null) *
          (parseFloat(precio_no_null) - parseFloat(descuento_no_null))

        total += monto
      })
      return this.formato_moneda(1, total, 0)
    },
  },

  watch: {
    dialog(val) {
      val || this.close()
    },
  },

  created() {
    this.initialize()
    this.getSelects()
  },

  methods: {
    limpiar() {
      this.form.id = 0
      this.form.documento = null
      this.form.nit = null
      this.form.nombre = null
      this.form.telefonos = null
      this.form.emails = null
      this.form.direcciones = null
      this.form.municipios_id = null
      this.form.h_insumos_detalles = []
      this.editedIndex = false

      this.dialog_detalle = false

      this.mostrar_nit = false

      this.$validator.reset()
      this.$validator.reset()
    },

    getSelects() {
      this.$store.state.services.selectController
        .proveedor()
        .then((r) => {
          this.proveedores = r.data.data
        })
        .catch((e) => {
          this.errorResponse(e)
        })

      this.$store.state.services.selectController
        .municipio()
        .then((r) => {
          this.municipios = r.data.data
        })
        .catch((e) => {
          this.errorResponse(e)
        })

      this.$store.state.services.selectController
        .producto_insumo()
        .then((r) => {
          this.producto_insumo = r.data.data
        })
        .catch((e) => {
          this.errorResponse(e)
        })
    },

    initialize() {
      this.loading = true

      this.$store.state.services.InsumoService.getAll()
        .then((r) => {
          this.no_anulados = r.data.no_anulados
          this.anulados = r.data.anulados
          this.close()
        })
        .catch((e) => {
          this.errorResponse(e)
        })
        .finally(() => {
          this.loading = false
        })
    },

    close() {
      this.limpiar()
      this.dialog = false
    },

    validar_formulario(scope) {
      if (this.form.h_insumos_detalles.length === 0) {
        this.$toastr.info(
          'Es necesario que agregue información en el detalle de la compra.',
          'Información incorrecta',
        )
      }

      this.$validator.validateAll(scope).then((result) => {
        if (result) this.store(this.form)
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
          this.$store.state.services.InsumoService.delete(data)
            .then((r) => {
              this.$toastr.success(r.data, 'Mensaje')
              this.initialize()
            })
            .catch((e) => {
              this.errorResponse(e)
            })
            .finally(() => {
              this.loading = false
            })
        } else {
          this.close()
        }
      })
    },

    store(data) {
      this.$swal({
        title: 'Agregar',
        text: '¿Está seguro de realizar esta acción?',
        type: 'success',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.InsumoService.store(data)
            .then((r) => {
              this.$toastr.success(r.data, 'Mensaje')
              this.initialize()
            })
            .catch((e) => {
              this.errorResponse(e)
            })
            .finally(() => {
              this.loading = false
            })
        } else {
          this.close()
        }
      })
    },

    seleccionar_cliente(item) {
      this.form.nit = item.nit
      this.form.nombre = item.nombre
      this.form.telefonos = item.telefonos
      this.form.emails = item.emails
      this.form.direcciones = item.direcciones
      this.form.municipios_id = item.municipio
    },

    agregar_detalle() {
      let objecto = new Object()
      objecto.h_productos_id = null
      objecto.producto = null
      objecto.cantidad = null
      objecto.precio = null
      objecto.descuento = null
      this.form.h_insumos_detalles.push(objecto)
    },

    quitar_detalle(index) {
      this.$swal({
        title: 'Eliminar',
        text: '¿Está seguro de realizar esta acción?',
        type: 'error',
        showCancelButton: true,
      })
        .then((result) => {
          if (result.value) {
            this.loading = true
            this.form.h_insumos_detalles.splice(
              this.form.h_insumos_detalles.indexOf(index),
              1,
            )
          }
        })
        .catch((e) => {
          this.errorResponse(e)
        })
        .finally(() => {
          this.loading = false
        })
    },

    dialog_producto() {
      this.loading = true
      this.form_producto.dialog = true
      this.form_producto.nombre = null
      this.form_producto.descripcion = null
      this.form_producto.consumible = false
      this.form_producto.stock_inicial = null
      this.form_producto.check_in = false
      this.loading = false
    },

    agregar_producto() {
      this.$swal({
        title: 'Agregar',
        text: '¿Está seguro de realizar esta acción?',
        type: 'success',
        showCancelButton: true,
      }).then((result) => {
        if (result.value) {
          this.loading = true
          this.$store.state.services.KardexService.store(this.form_producto)
            .then((r) => {
              this.$toastr.success(r.data, 'Mensaje')
              this.form_producto.dialog = false
              this.getSelects()
            })
            .catch((e) => {
              this.errorResponse(e)
            })
            .finally(() => {
              this.loading = false
            })
        }
      })
    },

    ver_detalle(item) {
      this.dialog_detalle = true
      this.detalle = item
    },
  },
}
</script>
