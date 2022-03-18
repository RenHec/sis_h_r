class OrdenServices {
  axios;
  baseUrl;

  constructor (axios, baseUrl, urlRestaurant) {
    this.axios = axios
    this.baseUrl = baseUrl + urlRestaurant + 'ordenes'
  }

  getAllOrders () {
    return this.axios.get(`${this.baseUrl}-list`)
  }

  getOneOrders (id) {
    return this.axios.get(`${this.baseUrl}/${id}`)
  }

  storeOrders (data) {
    return this.axios.post(`${this.baseUrl}`, data)
  }

  updateOrders (data) {
    return this.axios.put(`${this.baseUrl}/${data.id}`, data)
  }

  deleteOrders (data) {
    return this.axios.delete(`${this.baseUrl}/${data.id}`)
  }
}

export default OrdenServices
