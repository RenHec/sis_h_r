class SelectControllerService {
    axios
    baseUrl

    constructor(axios, baseUrl) {
        this.axios = axios
        this.baseUrl = `${baseUrl}servicio/compartido/version_uno/select`
    }

    empresa() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/empresa`);
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

    marca() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/marca`);
    }

    tipo_categoria() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/tipo_categoria`);
    }

    categoria(tipo_categoria = 1) {
        let self = this;
        return self.axios.get(`${self.baseUrl}/categoria/${tipo_categoria}`);
    }

    bodega() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/bodega`);
    }

    producto_sin_id(busqueda = null, empresa, bodega = null, compra = null) {
        let self = this;
        return self.axios.get(`${self.baseUrl}/producto/sin_id/${busqueda}/${empresa}/${bodega}/${compra}`);
    }

    producto_con_id(marca, categoria, empresa) {
        let self = this;
        return self.axios.get(`${self.baseUrl}/producto/con_id/${marca}/${categoria}/${empresa}`);
    }

    movimiento() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/movimiento`);
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

    tipo_pago(movimiento) {
        let self = this;
        return self.axios.get(`${self.baseUrl}/tipo_pago/${movimiento}`);
    }

    pago_credito() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/pago_credito`);
    }

    estado(movimiento) {
        let self = this;
        return self.axios.get(`${self.baseUrl}/estado/${movimiento}`);
    }

    productos_vencidos() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/productos_vencidos`);
    }

    productos_por_terminar() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/productos_por_terminar`);
    }

    productos_mas_vendidos() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/productos_mas_vendidos`);
    }

    vendedores() {
        let self = this;
        return self.axios.get(`${self.baseUrl}/vendedores`);
    }

    vencidos(stock) {
        let self = this;
        return self.axios.get(`${self.baseUrl}/vencidos/${stock}`);
    }

    presentacion_asignada(stock) {
        let self = this;
        return self.axios.get(`${self.baseUrl}/presentacion_asignada/${stock}`);
    }

    historial_stocks(stock) {
        let self = this;
        return self.axios.get(`${self.baseUrl}/historial_stocks/${stock}`);
    }

    movimientos_registrados(limite = null) {
        let self = this;
        return self.axios.get(`${self.baseUrl}/movimientos_registrados/${limite}`);
    }
}

export default SelectControllerService