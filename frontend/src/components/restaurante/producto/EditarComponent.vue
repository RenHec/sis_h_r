<template>
  <v-row class="justify-center">
    <v-col md="6" sm="12">
      <v-card>
        <v-overlay :value="loading">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-toolbar>
          <v-toolbar-title>Editar registro</v-toolbar-title>
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

              <v-file-input
                @change="loadImage"
                accept="image/*"
                type="file"
                outlined
                dense
                name="imagen"
                v-model="imagen"
                label="Imagen"
              ></v-file-input>
              <form-error
                :attribute_name="'imagen'"
                :errors_form="errors"
              ></form-error>

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
                v-for="(item, i) in foodCategoryList"
                :key="i"
                style="padding: 1 16px;"
                dense
              >
                <v-list-item-content style="padding: 1px;">
                  <v-checkbox
                    style="margin-top: 1px;"
                    v-model="opc"
                    :checked="item.checked"
                    :value="item.id"
                    :label="item.nombre"
                  ></v-checkbox>
                </v-list-item-content>
              </v-list-item>
            </v-list-item-group>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="green" dark @click="validateForm()">
              Actualizar
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
      costo: '',
      imagen: null,
      preparacion: '',
      consumo_reservacion: false,
      categories: [],
      opc: [],

      loading: false,
      foodCategoryList: [],
      imageAccept: ['image/png', 'image/jpeg', 'image/jpg'],
      prepara: [
        { id: 1, nombre: 'Si' },
        { id: 2, nombre: 'No' },
      ],
    }
  },
  props: {
    item: {},
  },
  mounted() {
    this.getDetailRecord(this.item.id)
  },
  created() {},
  methods: {
    getListFoodCategories() {
      this.loading = true

      this.$store.state.services.foodCategoryService
        .getListFoodCategory()
        .then((r) => {
          let items = this.addCheck(r.data.data)
          this.foodCategoryList = this.verifyCheck(items)
        })
        .catch((e) => {
          this.$toastr.error(e, 'Error')
        })
        .finally(() => {
          this.loading = false
        })
    },
    addCheck(items) {
      items.forEach((item) => {
        item.checked = false
      })

      return items
    },
    verifyCheck(items) {
      let ability = this.categories
      for (let index = 0; index < items.length; index++) {
        for (
          let indexProduct = 0;
          indexProduct < ability.length;
          indexProduct++
        ) {
          if (ability[indexProduct].categoria_comida_id === items[index].id) {
            items[index].checked = true
            this.opc.push(items[index].id)
          }
        }
      }

      return items
    },
    getDetailRecord(item) {
      this.loading = true
      this.$store.state.services.productService
        .getOneProducts(item)
        .then((r) => {
          this.initializeModel(r.data.data)
          this.getListFoodCategories()
        })
        .catch((e) => {
          this.$toastr.error(e, 'Error')
        })
        .finally(() => {
          this.loading = false
        })
    },
    initializeModel(item) {
      this.id = item.id
      this.nombre = item.nombre
      this.precio = item.precio
      this.costo = item.costo
      this.preparacion = item.quien_prepara
      this.consumo_reservacion = item.consumo_reservacion
      this.categories = item.producto_categoria_comida
    },
    closeForm() {
      events.$emit('close_form_update_product', true)
    },
    validateForm() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          this.updateRecord()
        }
      })
    },

    updateRecord() {
      this.loading = true

      let data = new FormData()
      data.append('id', this.id)
      data.append('nombre', this.nombre)
      data.append('precio', this.precio)
      data.append('costo', this.costo)
      data.append('preparacion', this.preparacion)
      data.append('consumo_reservacion', this.consumo_reservacion)
      this.imagen ? data.append('imagen', this.imagen, this.imagen.name) : ''
      this.opc.forEach((item) => {
        data.append('categorias[]', item)
      })

      this.$parent.$store.state.services.productService
        .updateProducts(data)
        .then((r) => {
          this.$toastr.success('Registro editado con éxito', 'Mensaje')
        })
        .catch((e) => {
          this.$toastr.error(e, 'Error')
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
