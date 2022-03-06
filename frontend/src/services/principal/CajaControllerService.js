class CajaControllerService {
    axios
    baseUrl

    constructor(axios, baseUrl) {
        this.axios = axios
        this.baseUrl = baseUrl + 'servicio/compartido/version_uno/caja'
    }

    index() {
        let self = this;
        return self.axios.get(`${self.baseUrl}`);
    }

    store(data) {
        let self = this
        return self.axios.post(`${self.baseUrl}`, data)
    }

    show(id) {
        let self = this;
        return self.axios.get(`${self.baseUrl}/${id}`);
    }

    destroy(data) {
        let self = this;
        return self.axios.delete(`${self.baseUrl}/${data.id}`);
    }
}

export default CajaControllerService