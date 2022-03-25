class ClienteService {
  axios;
  baseUrl;

  constructor (axios, baseUrl, urlRestaurant) {
    this.axios = axios
    this.baseUrl = baseUrl + urlRestaurant + 'clientes'
  }

  searchCustomer (name) {
    return this.axios.get(`${this.baseUrl}/${name}`)
  }

  storeCustomer (data) {
    return this.axios.post(`${this.baseUrl}`, data)
  }
}

export default ClienteService
