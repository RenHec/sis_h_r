class KardexService {
    axios;
    baseUrl;

    constructor(axios, baseUrl, prefix) {
        this.axios = axios
        this.baseUrl = `${baseUrl}${prefix}kardex`
    }

    getAll() {
        return this.axios.get(`${this.baseUrl}`)
    }

    store(data) {
        return this.axios.post(`${this.baseUrl}`, data)
    }

    get(id) {
        return this.axios.get(`${this.baseUrl}/${id}`)
    }

    update(data) {
        return this.axios.put(`${this.baseUrl}/${data.id}`, data)
    }

    delete(data) {
        return this.axios.delete(`${this.baseUrl}/${data.producto.id}`)
    }
}

export default KardexService
