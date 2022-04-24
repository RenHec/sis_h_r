<template>
  <v-toolbar>
    <v-sheet class="mx-auto" max-width="100%">
      <v-slide-group show-arrows>
        <v-slide-item
          v-for="category in categories"
          :key="category.id"
          v-slot="{ active }"
        >
          <v-btn
            class="mx-2"
            :input-value="active"
            active-class="purple white--text"
            depressed
            rounded
            outlined
            small
            @click="categoryFilter(category)"
          >
            {{ category.nombre }}
          </v-btn>
        </v-slide-item>
      </v-slide-group>
    </v-sheet>
  </v-toolbar>
</template>

<script>
export default {
  components: {},
  props: {
    categories: {},
  },
  data() {
    return {}
  },
  mounted() {},
  created() {},
  methods: {
    categoryFilter(item) {
      events.$emit('filter_menus', item)
    },
  },
}
</script>
