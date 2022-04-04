class ProveedorService {
    axios;
    baseUrl;
    prefix = 'servicio/compartido/version_uno/';

    constructor(axios, baseUrl) {
        this.axios = axios
        this.baseUrl = `${baseUrl}${this.prefix}proveedor_management`
    }

    getAll() {
        return this.axios.get(`${this.baseUrl}`)
    }

    update(data) {
        return this.axios.put(`${this.baseUrl}/${data.id}`, data)
    }

    delete(data) {
        return this.axios.delete(`${this.baseUrl}/${data.id}`)
    }
}

export default ProveedorService
