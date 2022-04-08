<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
  </v-row>
</template>

<script>
import FormError from './shared/FormError'

export default {
  name: 'Default',
  components: { FormError },
  data() {
    return {
      loading: false,
    }
  },

  created() {
    //this.initialize()
  },

  methods: {
    initialize() {
      this.loading = true
    },
  },
}
</script>
