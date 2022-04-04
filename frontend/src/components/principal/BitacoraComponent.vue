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
        :footer-props="footer"
      >
        <template v-slot:top>
          <v-toolbar flat :color="colorTolbar">
            <v-toolbar-title>Bitácora</v-toolbar-title>
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
        <template v-slot:descripcion="{ item }">
          <pre>{{ itme.descripcion }}</pre>
        </template>
        <template v-slot:no-data>
          <br />
          <v-alert type="error">No hay información para mostrar.</v-alert>
        </template>
      </v-data-table>
    </v-col>
  </v-row>
</template>

<script>
export default {
  name: 'Bitacora',
  data() {
    return {
      loading: false,
      dialog: false,
      search: '',
      headers: [
        {
          text: 'Fecha',
          align: 'start',
          value: 'created_at',
        },
        {
          text: 'Tabla',
          align: 'start',
          value: 'tabla',
        },
        {
          text: 'Acción',
          align: 'start',
          value: 'accion',
        },
        {
          text: 'Descripción',
          align: 'start',
          value: 'descripcion',
          sortable: false,
          search: false,
        },
        {
          text: 'Usuario',
          align: 'start',
          value: 'usuario',
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

      this.$store.state.services.selectController
        .bitacora()
        .then((r) => {
          console.log(r.data.data)
          this.desserts = r.data.data
        })
        .catch((e) => {
          this.errorResponse(e)
        })
        .finally(() => {
          this.loading = false
        })
    },
  },
}
</script>
