import Axios from 'axios'
import auth from '../auth'
import {
  isNullOrUndefined
} from 'util';

/* ::::::::::::::::::::::::::::::::::::::::::::: SEGURIDAD :::::::::::::::::::::::::::::::::::::::::::::::: */
import loginService from '../services/seguridad/login/LoginService'
import menuService from '../services/seguridad/menu/MenuService'
import rolMenuService from '../services/seguridad/rol/RolMenuService'
import rolService from '../services/seguridad/rol/RolService'
import userRolService from '../services/seguridad/usuario/UserRolService'
import userService from '../services/seguridad/usuario/UserService'
/* ::::::::::::::::::::::::::::::::::: FIN DE LOS IMPORT DE SEGURIDAD ::::::::::::::::::::::::::::::::::::: */

/* ::::::::::::::::::::::::::::::::::::::::::::: CATALOGO :::::::::::::::::::::::::::::::::::::::::::::::: */
import CategoriaController from '../services/catalogo/CategoriaControllerService'
import MarcaController from '../services/catalogo/MarcaControllerService'
import PresentacionController from '../services/catalogo/PresentacionControllerService'
import SelectController from '../services/catalogo/SelectControllerService'
/* :::::::::::::::::::::::::::::::::::: FIN DE LOS IMPORT DE CATALOGO ::::::::::::::::::::::::::::::::::::: */

/* ::::::::::::::::::::::::::::::::::::::::::::: PRINCIPAL :::::::::::::::::::::::::::::::::::::::::::::::: */
import AsignarPresentacionController from '../services/principal/AsignarPresentacionControllerService'
import BodegaController from '../services/principal/BodegaControllerService'
import CajaController from '../services/principal/CajaControllerService'
import CompraVentaController from '../services/principal/CompraVentaControllerService'
import DevolucionController from '../services/principal/DevolucionControllerService'
import EmpresaController from '../services/principal/EmpresaControllerService'
import PersonaController from '../services/principal/PersonaControllerService'
import ProductoController from '../services/principal/ProductoControllerService'
import StockController from '../services/principal/StockControllerService'
import TrasladoStockController from '../services/principal/TrasladoStockControllerService'
import ReporteController from '../services/principal/ReporteControllerService'

/*
|--------------------------------------------------------------------------
| Restaurant Services
|--------------------------------------------------------------------------
*/
import EstadoOrdenService from '../services/restaurante/EstadoOrdenService'
import TipoOrdenService from '../services/restaurante/TipoOrdenService'
import CategoriaComidaService from '../services/restaurante/CategoriaComidaService'
import ProductoService from '../services/restaurante/ProductoService'
import MeseroService from '../services/restaurante/MeseroService'
import OrdenService from '../services/restaurante/OrdenService'
import MesaService from '../services/restaurante/MesaService'

/*
|--------------------------------------------------------------------------
| Restaurant Services
|--------------------------------------------------------------------------
*/
import TipoCamaService from '../services/hotel/TipoCamaService'
import HabitacionService from '../services/hotel/HabitacionService'
import HabitacionFotoService from '../services/hotel/HabitacionFotoService'
import HabitacionPrecioService from '../services/hotel/HabitacionPrecioService'
import InsumoService from '../services/hotel/InsumoService'
import KardexService from '../services/hotel/KardexService'
import ReservacionService from '../services/hotel/ReservacionService'
/* :::::::::::::::::::::::::::::::::::: FIN DE LOS IMPORT DE PRINCIPAL ::::::::::::::::::::::::::::::::::::: */

let baseUrl = 'http://localhost:8001/hotel_restuarante/backend/public/'
//let baseUrl = 'http://64.225.43.65/multi_api/'
let token_data = $cookies.get('token_data')
let restaurantUrl = 'api/v1/restaurante/'
let hotelUrl = 'servicio/hotel/version_uno/'

// Axios Configuration
Axios.defaults.headers.common.Accept = 'application/json'
if (token_data !== null) {
  Axios.defaults.headers.common.Authorization = `Bearer ${token_data.access_token}`
}

const instance = Axios.create();
Axios.interceptors.response.use(response => {
  return response
}, error => {
  if (error.response.status === 401) {
    var token_data = $cookies.get('token_data')
    if (isNullOrUndefined(token_data)) {
      return error
    }

    var original_request = error.config
    return refreshToken().then(res => {
      auth.saveToken(res.data)
      original_request.headers['Authorization'] = 'Bearer ' + res.data.access_token
      return Axios.request(original_request)
    })
  }

  return error
});

function refreshToken() {
  var data = auth.getRefreshToken()
  return new Promise(function (resolve, reject) {
    instance.post(baseUrl + 'service/passport/generar/token', data)
      .then(r => {
        resolve(r)
      }).catch(e => {
        reject(r)
      })
  })
}

instance.interceptors.response.use(response => {
  return response
}, error => {

  if (error.response.status === 401 || error.response.data.message == "The refresh token is invalid.") {
    auth.noAcceso()
  }

  return error
});

export default {
  //Seguridad
  loginService: new loginService(Axios, baseUrl),
  menuService: new menuService(Axios, baseUrl),
  rolMenuService: new rolMenuService(Axios, baseUrl),
  rolService: new rolService(Axios, baseUrl),
  userRolService: new userRolService(Axios, baseUrl),
  userService: new userService(Axios, baseUrl),

  selectController: new SelectController(Axios, baseUrl),
  categoriaController: new CategoriaController(Axios, baseUrl),
  marcaController: new MarcaController(Axios, baseUrl),
  presentacionController: new PresentacionController(Axios, baseUrl),

  asignarPresentacionController: new AsignarPresentacionController(Axios, baseUrl),
  bodegaController: new BodegaController(Axios, baseUrl),
  cajaController: new CajaController(Axios, baseUrl),
  compraVentaController: new CompraVentaController(Axios, baseUrl),
  devolucionController: new DevolucionController(Axios, baseUrl),
  empresaController: new EmpresaController(Axios, baseUrl),
  personaController: new PersonaController(Axios, baseUrl),
  productoController: new ProductoController(Axios, baseUrl),
  stockController: new StockController(Axios, baseUrl),
  trasladoStockController: new TrasladoStockController(Axios, baseUrl),
  reporteController: new ReporteController(Axios, baseUrl),

  /*
  |--------------------------------------------------------------------------
  | Restaurant Services
  |--------------------------------------------------------------------------
  */
  statusOrderService: new EstadoOrdenService(Axios, baseUrl, restaurantUrl),
  orderTypeService: new TipoOrdenService(Axios, baseUrl, restaurantUrl),
  foodCategoryService: new CategoriaComidaService(Axios, baseUrl, restaurantUrl),
  productService: new ProductoService(Axios, baseUrl, restaurantUrl),
  waiterService: new MeseroService(Axios, baseUrl, restaurantUrl),
  orderService: new OrdenService(Axios, baseUrl, restaurantUrl),
  tableService: new MesaService(Axios, baseUrl, restaurantUrl),

  /*
  |--------------------------------------------------------------------------
  | Hotel Services
  |--------------------------------------------------------------------------
  */
  TipoCamaService: new TipoCamaService(Axios, baseUrl, hotelUrl),
  HabitacionService: new HabitacionService(Axios, baseUrl, hotelUrl),
  HabitacionFotoService: new HabitacionFotoService(Axios, baseUrl, hotelUrl),
  HabitacionPrecioService: new HabitacionPrecioService(Axios, baseUrl, hotelUrl),
  InsumoService: new InsumoService(Axios, baseUrl, hotelUrl),
  KardexService: new KardexService(Axios, baseUrl, hotelUrl),
  ReservacionService: new ReservacionService(Axios, baseUrl, hotelUrl),
}
