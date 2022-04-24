'use strict'

export default {
  namespaced: true,

  state: {
    ordersRestaurant: [],
    totalAmountOrdersRestaurant: 0.00,

    selectedTable: 0,
    isTableSelected: false,
    tableName: '',

    cashOpened: false,
    caja: 0
  },

  actions: {
    UPDATE_CAJA_ID({ commit }, id) {
      commit('SET_CAJA_ID', id)
    },
    UPDATE_CASH_OPENING({ commit }, flag) {
      commit('SET_CASH_OPENING', flag)
    },
    UPDATE_AMOUNT_ORDER({ commit }, amount) {
      commit('SET_AMOUNT_ORDER', amount)
    },
    UPDATE_SELECTED_TABLE({ commit }, item) {
      commit('SET_SELECTED_TABLE', item.id)
      commit('SET_IS_TABLE_SELECTED', item.selected)
      commit('SET_TABLE_NAME', item.name)
    }
  },

  mutations: {
    SET_ORDER_ITEM(state, item) {
      state.ordersRestaurant.push(item)
    },
    SET_AMOUNT_ORDER(state, amount) {
      state.totalAmountOrdersRestaurant = parseFloat(amount)
    },
    SET_LIST_ITEMS(state, list) {
      state.ordersRestaurant = list
    },
    SET_SELECTED_TABLE(state, table) {
      state.selectedTable = table
    },
    SET_IS_TABLE_SELECTED(state, selected) {
      state.isTableSelected = selected
    },
    SET_TABLE_NAME(state, tableName) {
      state.tableName = tableName
    },
    SET_CASH_OPENING(state, flag) {
      state.cashOpened = flag
    },
    SET_CAJA_ID(state, id) {
      state.caja = id
    }
  },

  getters: {
    ordersRestaurant: state => state.ordersRestaurant,
    totalAmountOrdersRestaurant: state => state.totalAmountOrdersRestaurant,
    selectedTable: state => state.selectedTable,
    isTableSelected: state => state.isTableSelected,
    tableName: state => state.tableName,

    cashOpened: state => state.cashOpened,
    caja: state => state.caja
  }
}
