class DepartamentoService {
  axios;
  baseUrl;

  constructor (axios, baseUrl, urlRestaurant) {
    this.axios = axios
    this.baseUrl = baseUrl + urlRestaurant + 'departamentos'
  }

  getListDepartamentos () {
    return this.axios.get(`${this.baseUrl}`)
  }
}

export default DepartamentoService
