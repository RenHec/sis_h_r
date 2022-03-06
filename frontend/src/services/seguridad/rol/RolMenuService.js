class RolMenuService {
  axios
  baseUrl

  constructor(axios, baseUrl) {
    this.axios = axios
    this.baseUrl = baseUrl + 'servicio/compartido/version_uno/rol_menu'
  }

  store(data) {
    let self = this
    return self.axios.post(`${self.baseUrl}`, data)
  }

  eliminar_masivo(data) {
    let self = this
    return self.axios.post(`${self.baseUrl}/eliminar_masivo`, data)
  }

  destroy(data) {
    let self = this;
    return self.axios.delete(`${self.baseUrl}/${data.id}`);
  }
}

export default RolMenuService