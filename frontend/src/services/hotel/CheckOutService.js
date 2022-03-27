class CheckOutService {
    axios;
    baseUrl;

    constructor(axios, baseUrl, prefix) {
        this.axios = axios
        this.baseUrl = `${baseUrl}${prefix}check_out`
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

export default CheckOutService
