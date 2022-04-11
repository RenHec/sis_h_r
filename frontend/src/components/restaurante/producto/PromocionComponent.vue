<template>
  <v-row class="justify-center">
    <v-col md="6" sm="12">
      <v-card>
        <v-overlay :value="loading">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-toolbar>
          <v-toolbar-title>Crear promocion para producto <strong>{{ item.nombre }}</strong></v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn icon @click="closeForm()">
            <v-icon dark>close</v-icon>
          </v-btn>
        </v-toolbar>
        <v-container fluid>
          <v-card-text>
            <v-form :autocomplete="'off'" ref="form" lazy-validation>
              <v-text-field
                outlined
                dense
                autofocus
                name="nombre"
                v-model="nombre"
                v-validate="'required'"
                label="Nombre"
              ></v-text-field>
              <form-error
                :attribute_name="'nombre'"
                :errors_form="errors"
              ></form-error>

              <v-text-field
                outlined
                dense
                name="precio"
                v-model="precio"
                v-validate="'required|decimal'"
                label="Precio venta"
              ></v-text-field>
              <form-error
                :attribute_name="'precio'"
                :errors_form="errors"
              ></form-error>

              <v-text-field
                outlined
                dense
                name="costo"
                v-model="costo"
                v-validate="'required|decimal'"
                label="Precio costo"
              ></v-text-field>
              <form-error
                :attribute_name="'costo'"
                :errors_form="errors"
              ></form-error>

              <v-text-field
                outlined
                dense
                name="cantidad"
                v-model="cantidad"
                v-validate="'required|decimal'"
                label="Cantidad"
              ></v-text-field>
              <form-error
                :attribute_name="'cantidad'"
                :errors_form="errors"
              ></form-error>

              <v-file-input
                @change="loadImage"
                accept="image/*"
                type="file"
                outlined
                dense
                name="imagen"
                v-model="imagen"
                v-validate="'required'"
                label="Imagen"
              ></v-file-input>
              <form-error
                :attribute_name="'imagen'"
                :errors_form="errors"
              ></form-error>

              <v-text-field v-model="descripcion" outlined dense label="Descripción"></v-text-field>

              <v-select
                outlined
                dense
                v-validate="'required'"
                v-model="preparacion"
                item-value="id"
                name="preparacion"
                item-text="nombre"
                :items="prepara"
                label="Cocina prepara este producto?"
              ></v-select>
              <form-error
                :attribute_name="'preparacion'"
                :errors_form="errors"
              ></form-error>

              <v-checkbox
                dense
                v-model="consumo_reservacion"
                name="consumo en reservación"
                :label="`${
                  consumo_reservacion
                    ? 'SI, aplica para consumir en reservación'
                    : 'NO, aplica para consumir en reservación'
                }`"
                v-validate="'required'"
              ></v-checkbox>
              <form-error
                :attribute_name="'consumo en reservación'"
                :errors_form="errors"
              ></form-error>
            </v-form>
            <v-card-title>Categorías</v-card-title>
            <v-list-item-group dense class="py-0">
              <v-list-item
                v-for="(item, i) in foodCategoriesList"
                :key="i"
                style="padding: 1 16px;"
                dense
              >
                <v-list-item-content style="padding: 1px;">
                  <v-checkbox
                    style="margin-top: 1px;"
                    v-model="opc"
                    :value="item.id"
                    :label="item.nombre"
                  ></v-checkbox>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" @click="validateForm()">
              Guardar
            </v-btn>
          </v-card-actions>
        </v-container>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import FormError from '../../shared/FormError.vue'

export default {
  components: {
    FormError,
  },
  data() {
    return {
      nombre: '',
      precio: '',
      imagen: null,
      costo: '',
      preparacion: '',
      inventario: '',
      cantidad: '',
      descripcion: '',
      consumo_reservacion: false,
      opc: [],

      loading: false,
      foodCategoriesList: [],
      imageAccept: ['image/png', 'image/jpeg', 'image/jpg'],
      prepara: [
        { id: 1, nombre: 'Si' },
        { id: 2, nombre: 'No' },
      ],
      useInventory: [
        { id: 1, nombre: 'Si' },
        { id: 0, nombre: 'No' },
      ],
    }
  },
  props:{
    item:{}
  },
  mounted() {
    this.getListFoodCategories()
  },
  created() {},
  methods: {
    getListFoodCategories() {
      this.loading = true

      this.$store.state.services.foodCategoryService
        .getListFoodCategory()
        .then((r) => {
          this.foodCategoriesList = r.data.data
        })
        .catch((e) => {
          this.$toastr.error(e, 'Error')
        })
        .finally(() => {
          this.loading = false
        })
    },
    closeForm() {
      events.$emit('close_form_new_product', true)
    },
    validateForm() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          this.saveRecord()
        }
      })
    },
    intializeFields() {
      ;(this.nombre = ''),
        (this.precio = ''),
        (this.imagen = null),
        (this.costo = ''),
        (this.opc = []),
        (this.preparacion = ''),
        (this.inventario = ''),
        (this.consumo_reservacion = false),
        this.cantidad = '',
        this.descripcion = '',
      this.$validator.reset()
    },

    saveRecord() {
      this.loading = true

      let data = new FormData()
      data.append('nombre', this.nombre)
      data.append('precio', this.precio)
      data.append('cantidad', this.cantidad)
      data.append('costo', this.costo)
      data.append('preparacion', this.preparacion)
      data.append('productoId', this.item.id)
      data.append('descripcion', this.descripcion)
      data.append('consumo_reservacion', this.consumo_reservacion ? 1 : 0)
      data.append('imagen', this.imagen, this.imagen.name)
      this.opc.forEach((item) => {
        data.append('categorias[]', item)
      })

      this.$parent.$store.state.services.productService
        .storePromotionProducts(data)
        .then((r) => {
          this.$toastr.success('Registro guardado con éxito', 'Mensaje')
          this.intializeFields()
          this.closeForm()
        })
        .catch((e) => {
          if (e.response) {
            if (e.response.data.code === 404) {
              this.$toastr.warning(e.response.data.error, 'Advertencia')
              return
            } else if (e.response.data.code === 423) {
              this.$toastr.warning(e.response.data.error, 'Advertencia')
              return
            } else {
              for (let value of Object.values(e.response.data)) {
                this.$toastr.error(value, 'Mensaje')
              }
            }
            return
          }
        })
        .finally(() => {
          this.loading = false
        })
    },
    loadImage(e) {
      if (typeof e === 'undefined') {
        this.imagen = null
        return
      }

      if (!this.imageAccept.includes(e.type.toLowerCase())) {
        this.imagen = null
        this.$toastr.info('El archivo debe ser una imagen', 'Mensaje')
        return
      }
    },
  },
}
</script>
