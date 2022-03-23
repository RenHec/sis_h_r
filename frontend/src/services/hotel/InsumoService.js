class InsumoService {
    axios;
    baseUrl;

    constructor(axios, baseUrl, prefix) {
        this.axios = axios
        this.baseUrl = `${baseUrl}${prefix}insumo`
    }

    getAll() {
        return this.axios.get(`${this.baseUrl}`)
    }

    store(data) {
        return this.axios.post(`${this.baseUrl}`, data)
    }

    delete(data) {
        return this.axios.delete(`${this.baseUrl}/${data.id}`)
    }
}

export default InsumoService
