class EstadoOrdenService {
  axios;
  baseUrl;

  constructor (axios, baseUrl, urlRestaurant) {
    this.axios = axios
    this.baseUrl = baseUrl + urlRestaurant + 'estado-orden'
  }

  getAllOrderStatus (data) {
    return this.axios.get(`${this.baseUrl}?sortBy=${data.sortBy}&sortDesc=${data.sortDesc}&perPage=${data.perPage}&page=${data.page}&search=${data.search}`)
  }

  getListOrderStatus () {
    return this.axios.get(`${this.baseUrl}-list`)
  }
  getOneOrderStatus (id) {
    return this.axios.get(`${this.baseUrl}/${id}`)
  }

  storeOrderStatus (data) {
    return this.axios.post(`${this.baseUrl}`, data)
  }

  updateOrderStatus (data) {
    return this.axios.put(`${this.baseUrl}/${data.id}`, data)
  }

  deleteOrderStatus (data) {
    return this.axios.delete(`${this.baseUrl}/${data.id}`)
  }
}

export default EstadoOrdenService
