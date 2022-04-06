<template>
  <v-row class="justify-center">
    <v-col md="6" sm="12">
      <v-card>
        <v-overlay :value="loading">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-toolbar>
          <v-toolbar-title>Agrega stock de producto <strong>{{ item.nombre }}</strong></v-toolbar-title>
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
                name="cantidad"
                v-model="cantidad"
                v-validate="'required'"
                label="Cantidad"
              ></v-text-field>
              <form-error
                :attribute_name="'cantidad'"
                :errors_form="errors"
              ></form-error>
            </v-form>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="green" @click="validateForm()" dark>
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
  props:{
    item:{}
  },
  data() {
    return {
      cantidad: '',
      productoId: '',

      loading: false,
    }
  },
  mounted() {

  },
  created() {},
  methods: {
    closeForm() {
      events.$emit('close_form_inventory_product', true)
    },
    validateForm() {
      this.$validator.validateAll().then((result) => {
        if (result) {
          this.saveRecord()
        }
      })
    },
    intializeFields() {
      this.cantidad = '',
      this.productoId = '',
      this.$validator.reset()
    },

    saveRecord() {
      this.loading = true

      let data = {
        'productoId': this.item.id,
        'cantidad':this.cantidad
      }

      this.$parent.$store.state.services.inventoryRestaurantService
        .storeInventory(data)
        .then((r) => {
          this.$toastr.success('Registro actualizado con éxito', 'Mensaje')
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
    }
  },
}
</script>
