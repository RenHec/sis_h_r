'use strict'

export default {
  namespaced: true,

  state: {
    ordersRestaurant: [],
    totalAmountOrdersRestaurant: 0.00
  },

  actions: {
    UPDATE_AMOUNT_ORDER({commit}, amount) {
      commit('SET_AMOUNT_ORDER', amount)
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
    }
  },

  getters: {
    ordersRestaurant: state => state.ordersRestaurant,
    totalAmountOrdersRestaurant: state => state.totalAmountOrdersRestaurant
  }
}
