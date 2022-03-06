class DevolucionControllerService {
    axios
    baseUrl

    constructor(axios, baseUrl) {
        this.axios = axios
        this.baseUrl = baseUrl + 'service/rest/v1/principal/devolucion'
    }

    index() {
        let self = this;
        return self.axios.get(`${self.baseUrl}`);
    }

    show(id) {
        let self = this;
        return self.axios.get(`${self.baseUrl}/${id}`);
    }

    update(data) {
        let self = this;
        return self.axios.put(`${self.baseUrl}/${data.id}`, data);
    }
}

export default DevolucionControllerService