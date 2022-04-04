class EmpleadoService {
    axios;
    baseUrl;
    prefix = 'servicio/compartido/version_uno/';

    constructor(axios, baseUrl) {
        this.axios = axios
        this.baseUrl = `${baseUrl}${this.prefix}empleado`
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
        return this.axios.delete(`${this.baseUrl}/${data.id}`)
    }
}

export default EmpleadoService
