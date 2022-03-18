class HabitacionPrecioService {
    axios;
    baseUrl;

    constructor(axios, baseUrl, prefix) {
        this.axios = axios
        this.baseUrl = `${baseUrl}${prefix}habitacion_precio`
    }

    update(data) {
        return this.axios.put(`${this.baseUrl}/${data.id}`, data)
    }

    delete(data) {
        return this.axios.delete(`${this.baseUrl}/${data.id}`)
    }
}

export default HabitacionPrecioService
