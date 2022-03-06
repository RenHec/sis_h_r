class AsignarPresentacionControllerService {
    axios
    baseUrl

    constructor(axios, baseUrl) {
        this.axios = axios
        this.baseUrl = baseUrl + 'service/rest/v1/principal/asignar_presentacion'
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

export default AsignarPresentacionControllerService