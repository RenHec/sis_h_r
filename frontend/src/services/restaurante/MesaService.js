class MesaService {
  axios;
  baseUrl;

  constructor (axios, baseUrl, urlRestaurant) {
    this.axios = axios
    this.baseUrl = baseUrl + urlRestaurant + 'mesas'
  }

  getAllTables (data) {
    return this.axios.get(`${this.baseUrl}?sortBy=${data.sortBy}&sortDesc=${data.sortDesc}&perPage=${data.perPage}&page=${data.page}&search=${data.search}`)
  }

  getOneTables (id) {
    return this.axios.get(`${this.baseUrl}/${id}`)
  }

  getListTables () {
    return this.axios.get(`${this.baseUrl}-list`)
  }

  storeTables (data) {
    return this.axios.post(`${this.baseUrl}`, data)
  }

  updateTables (data) {
    return this.axios.put(`${this.baseUrl}/${data.id}`, data)
  }

  deleteTables (data) {
    return this.axios.delete(`${this.baseUrl}/${data.id}`)
  }

  getListTablesIsBusy () {
    return this.axios.get(`${this.baseUrl}-busy`)
  }
}

export default MesaService
