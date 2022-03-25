class MunicipioService {
  axios;
  baseUrl;

  constructor (axios, baseUrl, urlRestaurant) {
    this.axios = axios
    this.baseUrl = baseUrl + urlRestaurant + 'municipios'
  }

  getListMunicipios (id) {
    return this.axios.get(`${this.baseUrl}/${id}`)
  }
}

export default MunicipioService
