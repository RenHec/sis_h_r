class ReporteControllerService {
    axios
    baseUrl

    constructor(axios, baseUrl) {
        this.axios = axios
        this.baseUrl = baseUrl + 'service/rest/v1/principal/reporte'
    }

    ventas_generales(data) {
        let self = this;
        return self.axios.post(`${self.baseUrl}/ventas_generales`, data);
    }

    mas_menos_vendidos(data) {
        let self = this;
        return self.axios.post(`${self.baseUrl}/mas_menos_vendidos`, data);
    }

    proximo_vencer(data) {
        let self = this;
        return self.axios.post(`${self.baseUrl}/proximo_vencer`, data);
    }

    stock_categoria_producto(data) {
        let self = this;
        return self.axios.post(`${self.baseUrl}/stock_categoria_producto`, data);
    }

    ventas_diarias(data) {
        let self = this;
        return self.axios.post(`${self.baseUrl}/ventas_diarias`, data);
    }

    ganancia_producto(data) {
        let self = this;
        return self.axios.post(`${self.baseUrl}/ganancia_producto`, data);
    }

    producto_bodega(data) {
        let self = this;
        return self.axios.post(`${self.baseUrl}/producto_bodega`, data);
    }

    logs_inicio_session(data) {
        let self = this;
        return self.axios.post(`${self.baseUrl}/logs_inicio_session`, data);
    }

    ticket(compra_venta) {
        let self = this;
        return self.axios.get(`${self.baseUrl}/ticket/${compra_venta}`);
    }
}

export default ReporteControllerService