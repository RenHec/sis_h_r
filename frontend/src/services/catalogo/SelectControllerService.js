class SelectControllerService {
    axios
    baseUrl

    constructor(axios, baseUrl) {
        this.axios = axios
        this.baseUrl = `${baseUrl}servicio/compartido/version_uno/select`
    }

    departamento() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/departamento`);
    }

    municipio() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/municipio`);
    }

    presentacion() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/presentacion`);
    }

    tipo_categoria() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/tipo_categoria`);
    }

    cliente() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/cliente`);
    }

    proveedor() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/proveedor`);
    }

    mes() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/mes`);
    }

    tipo_pago() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/tipo_pago`);
    }

    tipo_cama() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/tipo_cama`);
    }

    estado_habitacion() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/estado_habitacion`);
    }

    producto_insumo() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/producto_insumo`);
    }

    producto_check_in() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/producto_check_in`);
    }

    precios(habitacion) {
        let self = this;
        return self.axios.get(`${self.baseUrl}/precios/${habitacion}`);
    }
}

export default SelectControllerService