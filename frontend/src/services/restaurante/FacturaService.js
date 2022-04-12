class FacturaService {
  axios;
  baseUrl;

  constructor (axios, baseUrl, urlRestaurant) {
    this.axios = axios
    this.baseUrl = baseUrl + urlRestaurant + 'ticket-restaurante'
  }

  getVoucherCash (cashId) {
    return this.axios({
      method: 'GET',
      url: `${this.baseUrl}-caja/${cashId}`,
      responseType: 'blob'
    })
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
