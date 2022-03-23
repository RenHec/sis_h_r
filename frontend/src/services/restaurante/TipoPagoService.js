class TipoPagoService {
  axios;
  baseUrl;

  constructor (axios, baseUrl, urlRestaurant) {
    this.axios = axios
    this.baseUrl = baseUrl + urlRestaurant + 'tipos-pagos'
  }

  getListPaymentMethod () {
    return this.axios.get(`${this.baseUrl}`)
  }
}

export default TipoPagoService
