class FacturaService {
  axios;
  baseUrl;

  constructor (axios, baseUrl, urlRestaurant) {
    this.axios = axios
    this.baseUrl = baseUrl + urlRestaurant + 'ticket-restaurante'
  }

  getInvoice (saleId) {
    return this.axios({
      method: 'GET',
      url: `${this.baseUrl}-pago/${saleId}`,
      responseType: 'blob'
    })
  }
}

export default FacturaService
