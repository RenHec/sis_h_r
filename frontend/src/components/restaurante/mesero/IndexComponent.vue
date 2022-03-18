<template>
  <div>
    <Mesero v-if="isTableSelected"/>
    <v-row>
      <v-overlay :value="loading">
        <v-progress-circular indeterminate size="64"></v-progress-circular>
      </v-overlay>
      <v-col md='12' sm='12'  v-if="!isTableSelected">
        <v-card>
          <v-toolbar>
            <v-toolbar-title>Seleccionar mesa</v-toolbar-title>
          </v-toolbar>
          <div style="background-color:#e3f2fd; height:85vh; overflow-y:scroll">
              <v-row>
                <template v-for="item in items">
                    <v-col v-bind:key="item.id" md="4" lg="3" sm='6'>
                      <v-card class="mt-3 mx-2" @click="checkTable(item)">
                          <div class="d-flex flex-no-wrap justify-space-between">
                            <div>
                              <v-card-subtitle :v-text="item.nombre"></v-card-subtitle>

                              <v-card-actions>
                                <v-card-title
                                  class="text-h6"
                                  v-text="item.nombre"
                                ></v-card-title>
                              </v-card-actions>
                            </div>

                            <v-avatar
                              class="ma-3"
                              size="125"
                              tile
                            >
                              <v-btn
                                class="ma-2"
                                outlined
                                large
                                fab
                                color="red darken-1"
                              >
                                <v-icon>{{ item.icono }}</v-icon>
                              </v-btn>
                            </v-avatar>
                          </div>
                        </v-card>
                    </v-col>
                </template>
              </v-row>
            </div>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import Mesero from './MeseroComponent.vue'
import { createNamespacedHelpers } from 'vuex'

const {
  mapGetters: restaurantMapGetter,
  mapMutations: restaurantMapMutations,
  mapActions: restaurantMapActions
} = createNamespacedHelpers('restaurant')

export default{
  components:{
    Mesero,
  },
  data(){
    return{
      loading:false,
      items:[],
    }
  },
  mounted(){
    this.getListTables()
    this.SET_LIST_ITEMS([])
  },
  created(){

  },
  beforeDestroy(){
    this.UPDATE_SELECTED_TABLE({'id':0,'name':'','selected':false})

  },
  methods:{
    ...restaurantMapActions([
      'UPDATE_SELECTED_TABLE'
    ]),
    ...restaurantMapMutations([
      'SET_LIST_ITEMS'
    ]),
    checkTable(item)
    {
      this.UPDATE_SELECTED_TABLE({'id':item.id,'name':item.nombre,'selected': true})
    },
    getListTables()
    {
      this.loading = true

      this.$store.state.services.tableService
        .getListTables()
        .then((r)=>{
          this.items = r.data.data
        })
        .catch((e)=>{
          this.$toastr.error(e,'Error')
        })
        .finally(()=>{
          this.loading = false
        })
    },
  },
  computed:{
    ...restaurantMapGetter([
      'selectedTable',
      'isTableSelected'
    ]),
  },
}
</script>
