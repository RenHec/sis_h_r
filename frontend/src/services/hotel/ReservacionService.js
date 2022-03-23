class ReservacionService {
    axios;
    baseUrl;

    constructor(axios, baseUrl, prefix) {
        this.axios = axios
        this.baseUrl = `${baseUrl}${prefix}reservacion`
    }

    getAll(consulta) {
        /*
            r - devuelve todas las reservaciones que se encuentran en reserva
            i - devuelve todas las reservaciones que se encuentran en check in
            o - devuelve todas las reservaciones que se encuentran en check out
            p - devuelve todas las reservaciones que fueron pagadas
            a - devuelve todas las reservaciones que se encuentran anuladas
            t - devuelve todas las reservaciones registradas
        */
        return this.axios.get(`${this.baseUrl}?consulta=${consulta}`)
    }

    store(data) {
        return this.axios.post(`${this.baseUrl}`, data)
    }

    delete(data) {
        return this.axios.delete(`${this.baseUrl}/${data.id}`)
    }
}

export default ReservacionService
