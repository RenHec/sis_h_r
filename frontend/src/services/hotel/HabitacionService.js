class HabitacionService {
    axios;
    baseUrl;

    constructor(axios, baseUrl, prefix) {
        this.axios = axios
        this.baseUrl = `${baseUrl}${prefix}habitacion`
    }

    getAll() {
        return this.axios.get(`${this.baseUrl}`)
    }

    store(data) {
        return this.axios.post(`${this.baseUrl}`, data)
    }

    update(data) {
        return this.axios.put(`${this.baseUrl}/${data.id}`, data)
    }

    delete(data) {
        return this.axios.delete(`${this.baseUrl}/${data.id}`)
    }
}

export default HabitacionService
