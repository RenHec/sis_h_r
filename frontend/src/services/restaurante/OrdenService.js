class OrdenServices {
  axios;
  baseUrl;

  constructor (axios, baseUrl, urlRestaurant) {
    this.axios = axios
    this.baseUrl = baseUrl + urlRestaurant + 'ordenes'
  }

  getAllOrders (data) {
    return this.axios.get(`${this.baseUrl}-list?sortBy=${data.sortBy}&sortDesc=${data.sortDesc}&perPage=${data.perPage}&page=${data.page}&search=${data.search}`)
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
