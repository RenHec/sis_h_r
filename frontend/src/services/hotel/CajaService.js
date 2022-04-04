class CajaService {
    axios;
    baseUrl;

    constructor(axios, baseUrl, prefix) {
        this.axios = axios
        this.baseUrl = `${baseUrl}${prefix}hotel_caja`
    }

    getAll() {
        return this.axios.get(`${this.baseUrl}`)
    }

    get(id) {
        return this.axios.get(`${this.baseUrl}/${id}`)
    }

    store(data) {
        return this.axios.post(`${this.baseUrl}`, data)
    }
}

export default CajaService
