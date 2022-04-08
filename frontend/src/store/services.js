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
import PresentacionController from '../services/catalogo/PresentacionControllerService'
import SelectController from '../services/catalogo/SelectControllerService'
/* :::::::::::::::::::::::::::::::::::: FIN DE LOS IMPORT DE CATALOGO ::::::::::::::::::::::::::::::::::::: */

/* ::::::::::::::::::::::::::::::::::::::::::::: PRINCIPAL :::::::::::::::::::::::::::::::::::::::::::::::: */
import ClienteCatalogoService from '../services/principal/ClienteService'
import ProveedorCatalogoService from '../services/principal/ProveedorService'
import EmpleadoCatalogoService from '../services/principal/EmpleadoService'
/* :::::::::::::::::::::::::::::::::::: FIN DE LOS IMPORT DE PRINCIPAL ::::::::::::::::::::::::::::::::::::: */

/*
|--------------------------------------------------------------------------
| Restaurant Services
|--------------------------------------------------------------------------
*/
import ClienteService from '../services/restaurante/ClienteService'
import TipoPagoService from '../services/restaurante/TipoPagoService'
import EstadoOrdenService from '../services/restaurante/EstadoOrdenService'
import TipoOrdenService from '../services/restaurante/TipoOrdenService'
import CategoriaComidaService from '../services/restaurante/CategoriaComidaService'
import ProductoService from '../services/restaurante/ProductoService'
import MeseroService from '../services/restaurante/MeseroService'
import OrdenService from '../services/restaurante/OrdenService'
import OrdenDetailService from '../services/restaurante/OrdenDetailService'
import MesaService from '../services/restaurante/MesaService'
import DepartamentoService from '../services/restaurante/DepartamentoService'
import MunicipioService from '../services/restaurante/MunicipioService'
import ReporteCajaService from '../services/restaurante/ReporteCajaService'
import InventarioService from '../services/restaurante/InventarioService'
import FacturaService from '../services/restaurante/FacturaService'

/*
|--------------------------------------------------------------------------
| Hotel Services
|--------------------------------------------------------------------------
*/
import TipoCamaService from '../services/hotel/TipoCamaService'
import HabitacionService from '../services/hotel/HabitacionService'
import HabitacionFotoService from '../services/hotel/HabitacionFotoService'
import HabitacionPrecioService from '../services/hotel/HabitacionPrecioService'
import InsumoService from '../services/hotel/InsumoService'
import KardexService from '../services/hotel/KardexService'
import ReservacionService from '../services/hotel/ReservacionService'
import CheckInService from '../services/hotel/CheckInService'
import CheckOutService from '../services/hotel/CheckOutService'
import PagoService from '../services/hotel/PagoService'
import CajaService from '../services/hotel/CajaService'
import CajaMovimientoService from '../services/hotel/CajaMovimientoService'
/* :::::::::::::::::::::::::::::::::::: FIN DE LOS IMPORT DE HOTEL ::::::::::::::::::::::::::::::::::::: */

let baseUrl = process.env.BASE_URL
let token_data = $cookies.get('token_data')
let restaurantUrl = process.env.PREFIX_RESTAURANT
let hotelUrl = process.env.PREFIX_HOTEL

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

  throw error
});

function refreshToken() {
  var data = auth.getRefreshToken()
  return new Promise(function (resolve, reject) {
    instance.post(baseUrl + 'service/passport/generar/token', data)
      .then(r => {
        resolve(r)
      }).catch(e => {
        reject(e)
      })
  })
}

instance.interceptors.response.use(response => {
  return response
}, error => {

  if (error.response.status === 401 || error.response.data.message == "The refresh token is invalid.") {
    auth.noAcceso()
  }

  throw error
});

export default {
  //Seguridad
  loginService: new loginService(Axios, baseUrl),
  menuService: new menuService(Axios, baseUrl),
  rolMenuService: new rolMenuService(Axios, baseUrl),
  rolService: new rolService(Axios, baseUrl),
  userRolService: new userRolService(Axios, baseUrl),
  userService: new userService(Axios, baseUrl),

  /*
  |--------------------------------------------------------------------------
  | Catalogo Services
  |--------------------------------------------------------------------------
  */
  selectController: new SelectController(Axios, baseUrl),
  presentacionController: new PresentacionController(Axios, baseUrl),

  /*
  |--------------------------------------------------------------------------
  | Principal Services
  |--------------------------------------------------------------------------
  */
  ClienteCatalogoService: new ClienteCatalogoService(Axios, baseUrl),
  ProveedorCatalogoService: new ProveedorCatalogoService(Axios, baseUrl),
  EmpleadoCatalogoService: new EmpleadoCatalogoService(Axios, baseUrl),

  /*
  |--------------------------------------------------------------------------
  | Restaurant Services
  |--------------------------------------------------------------------------
  */
  customerService: new ClienteService(Axios, baseUrl, restaurantUrl),
  departamentoService: new DepartamentoService(Axios, baseUrl, restaurantUrl),
  municipioService: new MunicipioService(Axios, baseUrl, restaurantUrl),
  paymentMethodService: new TipoPagoService(Axios, baseUrl, restaurantUrl),
  statusOrderService: new EstadoOrdenService(Axios, baseUrl, restaurantUrl),
  orderTypeService: new TipoOrdenService(Axios, baseUrl, restaurantUrl),
  foodCategoryService: new CategoriaComidaService(Axios, baseUrl, restaurantUrl),
  productService: new ProductoService(Axios, baseUrl, restaurantUrl),
  waiterService: new MeseroService(Axios, baseUrl, restaurantUrl),
  orderService: new OrdenService(Axios, baseUrl, restaurantUrl),
  orderDetailService: new OrdenDetailService(Axios, baseUrl, restaurantUrl),
  tableService: new MesaService(Axios, baseUrl, restaurantUrl),
  checkoutRestaurantService: new ReporteCajaService(Axios, baseUrl, restaurantUrl),
  inventoryRestaurantService: new InventarioService(Axios, baseUrl, restaurantUrl),
  invoiceRestaurantService: new FacturaService(Axios, baseUrl, restaurantUrl),

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
  CheckInService: new CheckInService(Axios, baseUrl, hotelUrl),
  CheckOutService: new CheckOutService(Axios, baseUrl, hotelUrl),
  PagoService: new PagoService(Axios, baseUrl, hotelUrl),
  CajaService: new CajaService(Axios, baseUrl, hotelUrl),
  CajaMovimientoService: new CajaMovimientoService(Axios, baseUrl, hotelUrl),
}
