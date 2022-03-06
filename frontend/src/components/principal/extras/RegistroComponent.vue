<template>
  <v-row justify="center" align="center">
    <v-col cols="12">
      <v-data-table
        :headers="headers"
        :items="movimientos"
        :search="search"
        sort-by="calories"
        class="elevation-1"
        dark
        :footer-props="footer"
      >
        <template v-slot:top>
          <v-toolbar flat>
            <v-text-field
              v-model="search"
              append-icon="search"
              label="Buscar"
              single-line
              hide-details
            ></v-text-field>
          </v-toolbar>
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
  name: 'Registro',
  props: ['movimientos'],
  data() {
    return {
      search: '',
      headers: [
        {
          text: 'Pago',
          align: 'start',
          value: 'tipos_pagos.nombre',
        },
        {
          text: 'Monto',
          align: 'start',
          value: 'monto',
          sortable: false,
        },
        {
          text: 'Fecha',
          align: 'start',
          value: 'created_at',
        },
      ],
      footer: {
        showFirstLastPage: true,
        firstIcon: 'mdi-arrow-collapse-left',
        lastIcon: 'mdi-arrow-collapse-right',
        prevIcon: 'mdi-minus',
        nextIcon: 'mdi-plus',
      },
    }
  },

  created() {},

  methods: {},
}
</script>
