class ReporteCajaService {
  axios;
  baseUrl;

  constructor (axios, baseUrl, urlRestaurant) {
    this.axios = axios
    this.baseUrl = baseUrl + urlRestaurant + 'reporte-restaurante-caja'
  }

  getSalesAmountReportByDate (data) {
    return this.axios.post(`${this.baseUrl}`, data)
  }
}

export default ReporteCajaService
