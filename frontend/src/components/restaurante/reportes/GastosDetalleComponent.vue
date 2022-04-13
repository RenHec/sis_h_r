<template>
  <v-container fluid>
    <v-simple-table>
      <template v-slot:default>
        <thead>
          <tr>
            <th>Comprobante</th>
            <th>Descripción</th>
            <th>Monto</th>
          </tr>
        </thead>
        <tbody>
          <template v-for="item in items">
            <tr v-bind:key="item.id">
              <td>{{ item.comprobante }}</td>
              <td>{{ item.descripcion }}</td>
              <td>Q. {{ item.monto }}</td>
            </tr>
          </template>
        </tbody>
      </template>
    </v-simple-table>
  </v-container>
</template>

<script>

export default{
  components:{
  },
  data(){
    return{
    }
  },
  props:{
    items:{}
  },
  mounted(){
  },
  created(){
  },
  methods:{
  },
}
</script>
