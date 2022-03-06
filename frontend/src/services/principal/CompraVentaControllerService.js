class CompraVentaControllerService {
    axios
    baseUrl

    constructor(axios, baseUrl) {
        this.axios = axios
        this.baseUrl = baseUrl + 'service/rest/v1/principal/compra_venta'
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

    update(data) {
        let self = this;
        return self.axios.put(`${self.baseUrl}/${data.id}`, data);
    }

    destroy(data) {
        let self = this;
        return self.axios.delete(`${self.baseUrl}/${data.id}`);
    }
}

export default CompraVentaControllerService