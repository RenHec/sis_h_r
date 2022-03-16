'use strict'

export default {
  namespaced: true,

  state: {
    ordersRestaurant: [],
    totalAmountOrdersRestaurant: 0.00,

    selectedTable: 0,
    isTableSelected: false,
    tableName: ''
  },

  actions: {
    UPDATE_AMOUNT_ORDER({commit}, amount) {
      commit('SET_AMOUNT_ORDER', amount)
    },
    UPDATE_SELECTED_TABLE({commit}, item) {
      commit('SET_SELECTED_TABLE', item.id)
      commit('SET_IS_TABLE_SELECTED', item.selected)
      commit('SET_TABLE_NAME', item.name)
    }
  },

  mutations: {
    SET_ORDER_ITEM (state, item) {
      state.ordersRestaurant.push(item)
    },
    SET_AMOUNT_ORDER (state, amount) {
      state.totalAmountOrdersRestaurant = parseFloat(amount)
    },
    SET_LIST_ITEMS (state, list) {
      state.ordersRestaurant = list
    },
    SET_SELECTED_TABLE (state, table) {
      state.selectedTable = table
    },
    SET_IS_TABLE_SELECTED (state, selected) {
      state.isTableSelected = selected
    },
    SET_TABLE_NAME (state, tableName) {
      state.tableName = tableName
    }
  },

  getters: {
    ordersRestaurant: state => state.ordersRestaurant,
    totalAmountOrdersRestaurant: state => state.totalAmountOrdersRestaurant,
    selectedTable: state => state.selectedTable,
    isTableSelected: state => state.isTableSelected,
    tableName: state => state.tableName
  }
}
