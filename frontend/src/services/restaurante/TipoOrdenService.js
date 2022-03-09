class TipoOrdenService {
  axios;
  baseUrl;

  constructor (axios, baseUrl, urlRestaurant) {
    this.axios = axios
    this.baseUrl = baseUrl + urlRestaurant + 'tipo-orden'
  }

  getAllOrderType (data) {
    return this.axios.get(`${this.baseUrl}?sortBy=${data.sortBy}&sortDesc=${data.sortDesc}&perPage=${data.perPage}&page=${data.page}&search=${data.search}`)
  }

  getOneOrderType (id) {
    return this.axios.get(`${this.baseUrl}/${id}`)
  }

  storeOrderType (data) {
    return this.axios.post(`${this.baseUrl}`, data)
  }

  updateOrderType (data) {
    return this.axios.put(`${this.baseUrl}/${data.id}`, data)
  }

  deleteOrderType (data) {
    return this.axios.delete(`${this.baseUrl}/${data.id}`)
  }
}

export default TipoOrdenService
