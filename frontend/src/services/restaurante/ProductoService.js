class ProductoService {
  axios;
  baseUrl;
  domainUrl;

  constructor (axios, baseUrl, urlRestaurant) {
    this.axios = axios
    this.baseUrl = baseUrl + urlRestaurant + 'productos'
    this.domainUrl = baseUrl
  }

  getAllProducts (data) {
    return this.axios.get(`${this.baseUrl}?sortBy=${data.sortBy}&sortDesc=${data.sortDesc}&perPage=${data.perPage}&page=${data.page}&search=${data.search}`)
  }

  getOneProducts (id) {
    return this.axios.get(`${this.baseUrl}/${id}`)
  }

  getProductsList () {
    return this.axios.get(`${this.baseUrl}-list`)
  }

  storeProducts (datos) {
    return this.axios({
      url: this.baseUrl,
      data: datos,
      method: 'POST',
      'content-type': `multipart/form-data, charset=utf-8; boundary=${Math.random().toString().substr(2)}`
    })
  }

  updateProducts (datos) {
    return this.axios({
      url: this.baseUrl,
      data: datos,
      method: 'POST',
      'content-type': `multipart/form-data, charset=utf-8;`
    })
  }

  deleteProducts (data) {
    return this.axios.delete(`${this.baseUrl}/${data.id}`)
  }

  deleteOneProductInventory (data) {
    return this.axios.post(`${this.baseUrl}-delete-inventory`, data)
  }
}

export default ProductoService
