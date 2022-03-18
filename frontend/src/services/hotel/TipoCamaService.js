class TipoCamaService {
    axios;
    baseUrl;

    constructor(axios, baseUrl, prefix) {
        this.axios = axios
        this.baseUrl = `${baseUrl}${prefix}tipo_cama`
    }

    getAll(data = null) {
        return data == null ? this.axios.get(`${this.baseUrl}`) : this.axios.get(`${this.baseUrl}?sortBy=${data.sortBy}&sortDesc=${data.sortDesc}&perPage=${data.perPage}&page=${data.page}&search=${data.search}`)
    }

    get(id) {
        return this.axios.get(`${this.baseUrl}/${id}`)
    }

    store(data) {
        return this.axios.post(`${this.baseUrl}`, data)
    }

    delete(data) {
        return this.axios.delete(`${this.baseUrl}/${data.id}`)
    }
}

export default TipoCamaService
