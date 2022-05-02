<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-col cols="12" v-show="mostrarCalendario">
      <v-sheet tile dark class="d-flex">
        <v-row>
          <v-col cols="12" class="text-center" v-if="$refs.calendar">
            <br />
            <h3>{{ $refs.calendar.title }}</h3>
          </v-col>
          <v-col cols="12" md="1" class="text-center">
            <v-btn icon class="ma-2" @click="$refs.calendar.prev()">
              <v-icon>mdi-chevron-left</v-icon>
            </v-btn>
          </v-col>
          <v-col cols="12" md="3">
            <v-select
              v-model="type"
              :items="types"
              item-text="name"
              item-value="type"
              return-object
              dense
              outlined
              hide-details
              class="ma-2"
              label="Consulta por"
            ></v-select>
          </v-col>
          <v-col cols="12" md="3">
            <v-select
              v-model="weekday"
              :items="weekdays"
              dense
              outlined
              hide-details
              label="Consulta por"
              class="ma-2"
            ></v-select>
          </v-col>
          <v-col cols="12" md="4">
            <v-select
              v-model="room"
              :items="rooms"
              item-text="nombre"
              item-value="numero"
              return-object
              dense
              outlined
              clearable
              hide-details
              label="Habitación"
              class="ma-2"
            ></v-select>
          </v-col>
          <v-col cols="12" md="1" class="text-center">
            <v-btn icon class="ma-2" @click="$refs.calendar.next()">
              <v-icon>mdi-chevron-right</v-icon>
            </v-btn>
          </v-col>
        </v-row>
      </v-sheet>
      <v-sheet>
        <v-calendar
          dark
          ref="calendar"
          locale="es"
          :short-weekdays="false"
          v-model="value"
          :weekdays="weekday"
          :type="type.type"
          :events="events"
          :event-overlap-mode="mode"
          :event-overlap-threshold="30"
          :event-color="getEventColor"
        ></v-calendar>
      </v-sheet>
    </v-col>
  </v-row>
</template>

<script>
import FormError from './shared/FormError'
import { createNamespacedHelpers } from 'vuex'

const { mapActions: restaurantMapActions } = createNamespacedHelpers(
  'restaurant',
)

export default {
  name: 'Default',
  components: { FormError },
  data() {
    return {
      loading: false,
      type: { type: 'month', name: 'Mes' },
      types: [
        { type: 'month', name: 'Mes' },
        { type: 'week', name: 'Semana' },
        { type: 'day', name: 'Día' },
        { type: '4day', name: 'Últimos 4 días' },
      ],
      mode: 'stack',
      weekday: [0, 1, 2, 3, 4, 5, 6],
      weekdays: [
        { text: 'Domingo - Sábado', value: [0, 1, 2, 3, 4, 5, 6] },
        { text: 'Domingo', value: [0] },
        { text: 'Lunes', value: [1] },
        { text: 'Martes', value: [2] },
        { text: 'Miércoles', value: [3] },
        { text: 'Jueves', value: [4] },
        { text: 'Viernes', value: [5] },
        { text: 'Sábado', value: [6] },
      ],
      value: '',
      events: [],
      rooms: [],
      room: null,
    }
  },

  created() {
    this.getEvents()
    this.verifyCashOpened()
  },

  methods: {
    ...restaurantMapActions(['UPDATE_CASH_OPENING', 'UPDATE_CAJA_ID']),

    verifyCashOpened() {
      this.$store.state.services.checkoutRestaurantService
        .verifyIfExistsCashOpened()
        .then((r) => {
          if (!r.data.data) {
            this.UPDATE_CASH_OPENING(false)
          } else {
            let caja = r.data.data
            this.UPDATE_CASH_OPENING(true)
            this.UPDATE_CAJA_ID(caja.id)
          }
        })
        .catch((e) => {
          this.$toastr.error(e.response.data.error, 'Error')
        })
    },

    getEvents() {
      this.loading = true
      const events = []
      this.$store.state.services.selectController
        .habitaciones_agendadas()
        .then((r) => {
          r.data.agendadas.forEach((element) => {
            events.push({
              name: `Habitación #${element.numero} | ${element.codigo} - ${element.nombre}`,
              start: element.inicio,
              end: element.fin,
              color: 'blue',
              number: element.numero,
            })
          })

          this.rooms = r.data.habitaciones
        })
        .catch((e) => {
          this.errorResponse(e)
        })
        .finally(() => {
          this.loading = false
          this.events = events
        })
    },

    getEventColor(event) {
      return event.color
    },

    rnd(a, b) {
      return Math.floor((b - a + 1) * Math.random()) + a
    },
  },
  mounted() {
    this.$refs.calendar.checkChange()
  },
  computed: {
    mostrarCalendario() {
      var permissions = this.$store.state.permissions
      return _.includes(permissions, 'reservaciones')
    },

    filtrarAgenda() {
      let filtro = this.events

      if (this.room) {
        filtro = this.events.filter((element) => {
          return element.number.includes(this.room.numero)
        })
      }

      return filtro
    },
  },
}
</script>
