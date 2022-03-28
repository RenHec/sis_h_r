class OrdenDetailService {
  axios;
  baseUrl;

  constructor (axios, baseUrl, urlRestaurant) {
    this.axios = axios
    this.baseUrl = baseUrl + urlRestaurant
  }

  getOrderDetail (orderId) {
    return this.axios.get(`${this.baseUrl}ordenes-edit-list/${orderId}`)
  }

  setMinusQuantity (data) {
    return this.axios.post(`${this.baseUrl}ordenes-minus`, data)
  }

  setPlusQuantity (data) {
    return this.axios.post(`${this.baseUrl}ordenes-plus`, data)
  }

  deleteOneDetailOrder (data) {
    return this.axios.post(`${this.baseUrl}ordenes-delete-one`, data)
  }

  deleteAllOrderDetail (data) {
    return this.axios.post(`${this.baseUrl}ordenes-delete-all`, data)
  }
}

export default OrdenDetailService
