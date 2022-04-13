class ReporteCajaService {
  axios;
  baseUrl;

  constructor (axios, baseUrl, urlRestaurant) {
    this.axios = axios
    this.baseUrl = baseUrl + urlRestaurant
  }

  getListPurchases (data) {
    return this.axios.get(`${this.baseUrl}reporte-restaurante-gastos?sortBy=${data.sortBy}&sortDesc=${data.sortDesc}&perPage=${data.perPage}&page=${data.page}&search=${data.search}`)
  }
  closeCash (data) {
    return this.axios.post(`${this.baseUrl}cierre-restaurante-caja`, data)
  }
  storePurcharses (data) {
    return this.axios.post(`${this.baseUrl}egresos-restaurante-caja`, data)
  }
  getSalesAmountReportByDate (data) {
    return this.axios.post(`${this.baseUrl}reporte-restaurante-caja`, data)
  }

  verifyIfExistsCashOpened () {
    return this.axios.get(`${this.baseUrl}verificar-caja-restaurante`)
  }

  openCash (data) {
    return this.axios.post(`${this.baseUrl}aperturar-restaurante-caja`, data)
  }
}

export default ReporteCajaService
