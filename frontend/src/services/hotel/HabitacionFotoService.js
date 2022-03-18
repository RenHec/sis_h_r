class HabitacionFotoService {
    axios;
    baseUrl;

    constructor(axios, baseUrl, prefix) {
        this.axios = axios
        this.baseUrl = `${baseUrl}${prefix}habitacion_foto`
    }

    get(id) {
        return this.axios.get(`${this.baseUrl}/${id}`)
    }

    update(data) {
        return this.axios.put(`${this.baseUrl}/${data.id}`, data)
    }

    delete(data) {
        return this.axios.delete(`${this.baseUrl}/${data.id}`)
    }
}

export default HabitacionFotoService
