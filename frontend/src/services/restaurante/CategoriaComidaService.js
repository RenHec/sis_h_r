class CategoriaComidaService {
  axios;
  baseUrl;

  constructor (axios, baseUrl, urlRestaurant) {
    this.axios = axios
    this.baseUrl = baseUrl + urlRestaurant + 'categoria-comida'
  }

  getAllFoodCategory (data) {
    return this.axios.get(`${this.baseUrl}?sortBy=${data.sortBy}&sortDesc=${data.sortDesc}&perPage=${data.perPage}&page=${data.page}&search=${data.search}`)
  }

  getOneFoodCategory (id) {
    return this.axios.get(`${this.baseUrl}/${id}`)
  }

  getListFoodCategory () {
    return this.axios.get(`${this.baseUrl}-list`)
  }

  storeFoodCategory (data) {
    return this.axios.post(`${this.baseUrl}`, data)
  }

  updateFoodCategory (data) {
    return this.axios.put(`${this.baseUrl}/${data.id}`, data)
  }

  deleteFoodCategory (data) {
    return this.axios.delete(`${this.baseUrl}/${data.id}`)
  }
}

export default CategoriaComidaService
