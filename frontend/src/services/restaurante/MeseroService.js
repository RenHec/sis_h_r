class MeseroService {
  axios;
  baseUrl;

  constructor (axios, baseUrl, urlRestaurant) {
    this.axios = axios
    this.baseUrl = baseUrl + urlRestaurant + 'ordenes'
  }

  getAllWaitersOrder () {
    return this.axios.get(`${this.baseUrl}`)
  }

  getOneWaitersOrder (id) {
    return this.axios.get(`${this.baseUrl}/${id}`)
  }

  storeWaitersOrder (data) {
    return this.axios.post(`${this.baseUrl}`, data)
  }

  updateWaitersOrder (data) {
    return this.axios.put(`${this.baseUrl}/${data.id}`, data)
  }

  deleteWaitersOrder (data) {
    return this.axios.delete(`${this.baseUrl}/${data.id}`)
  }
}

export default MeseroService
