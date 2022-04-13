import Vue from 'vue'
import Router from 'vue-router'
import store from '../store/index'
import multiguard from 'vue-router-multiguard' //Middelware
import goTo from 'vuetify/es5/services/goto'

import Login from '@/components/login/Index'

import Default from '@/components/Default'

//Seguridad
import Rol from '@/components/seguridad/RolComponent'
import Usuario from '@/components/seguridad/UsuarioComponent'

//Catalogo General
import Presentacion from '@/components/catalogo/PresentacionComponent'

//Principal
import Ayuda from '@/components/principal/AyudaComponent'
import Bitacora from '@/components/principal/BitacoraComponent'
import Cliente from '@/components/principal/ClienteComponent'
import Proveedor from '@/components/principal/ProveedorComponent'
import Empleado from '@/components/principal/EmpleadoComponent'
/*
|--------------------------------------------------------------------------
| Restaurant Components
|--------------------------------------------------------------------------
*/
import Mesero from '@/components/restaurante/mesero/IndexComponent'
import Cocina from '@/components/restaurante/cocina/CocinaComponent'
import EstadoOrden from '@/components/restaurante/estado-orden/IndexComponent'
import TipoOrden from '@/components/restaurante/tipo-orden/IndexComponent'
import CategoriaComida from '@/components/restaurante/categoria-comida/IndexComponent'
import ProductoRestaurante from '@/components/restaurante/producto/IndexComponent'
import Ordenes from '@/components/restaurante/ordenes/IndexComponent'
import Mesa from '@/components/restaurante/mesa/IndexComponent'
import ReporteCaja from '@/components/restaurante/reportes/ReporteCajaComponent'
import InventarioRestaurante from '@/components/restaurante/reportes/InventarioComponent'
import VentaRestaurante from '@/components/restaurante/reportes/VentaComponent'
import CajaRestaurante from '@/components/restaurante/caja/CajaComponent'
import GastosRestaurante from '@/components/restaurante/reportes/GastosComponent'
/*
|--------------------------------------------------------------------------
| Hotel Components
|--------------------------------------------------------------------------
*/
import HTipoCama from '@/components/hotel/habitacion/tipo-cama/IndexComponent'
import HHabitacion from '@/components/hotel/habitacion/IndexComponent'
import HInsumo from '@/components/hotel/insumo/IndexComponent'
import HKardex from '@/components/hotel/insumo/kardex/IndexComponent'
import HHistorial from '@/components/hotel/reservacion/HistorialComponent'
import HRegistrar from '@/components/hotel/reservacion/RegistrarComponent'
import HCheckIn from '@/components/hotel/reservacion/proceso/CheckInComponent'
import HCheckOut from '@/components/hotel/reservacion/proceso/CheckOutComponent'
import HPagos from '@/components/hotel/reservacion/proceso/PagosComponent'
import HReservaciones from '@/components/hotel/reservacion/proceso/ReservacionesComponen'
import HCajaChica from '@/components/hotel/CajaChicaComponent'
import HDashboard from '@/components/hotel/DashboardComponent'

Vue.use(Router)

//validar authenticacion
const isLoggedIn = async (to, from, next) => {
  try {
    let login = 'login';
    if (store.state.is_login) {
      if (to.path === '/') {
        return next()
      } else {
        var permissions = store.state.permissions
        var ruta = to.path.split('/').join('')
        var perm = _.includes(permissions, ruta)
        perm ? null : alert(`No tiene permisos para navegar a la ruta ${ruta}`, 'Privilegios');

        return perm ? next() : next(false)
      }
    } else {
      if (to.path !== `/${login}`) {
        alert(`Es necesario que inicie sesión para ingresar al sistema.`, 'Inicio de Sesión')
      }
      return store.state.is_login ? next(false) : next("/login")
    }
  } catch (error) {
    //console.log(`Ocurrio un problema al intentar ingresar a ${to.path}`, 'Problema')
  }
  /*const options = { from: from.fullPath, to: to.fullPath, route: to.fullPath };
  try {
    const result = await this.authProvider.getClient().ensureLoggedIn(options);

    if (!result || !result.success) {
      next(false);
      return;
    }

    // Change the global location
    if (result.path) {
      next(false);
      window.location.replace(result.path);
      return;
    }

    this.store.commit('setinvalidTokenCalls', 0);

    // Otherwise only change the local location
    if (result.route) {
      next({ path: result.route });
      return;
    }

    // If there is no redirect set, just continue
    next();
  } catch (error) {
    this.logger.error({ title: 'User login error', exception: error });
    this.alertHandler.createAlert(
      'Login issue',
      `We are having trouble logging you in.
         If this continues please contact support@teaminator.io`,
      null, 'danger', 3000);
    next(false);
  }*/
};

const isLoggedOut = (to, from, next) => {
  if (store.state.is_login) {
    alert(`Actualmente usted cuenta con un inicio de sesión activo.`, 'Inicio de Sesión')
    return next(false)
  } else {
    return next()
  }
}

const permissionsValidations = (to, from, next) => {
  var permissions = store.state.permissions
  var ruta = to.path.split('/').join('')
  var perm = _.includes(permissions, ruta)
  perm ? null : alert(`No tiene permisos para navegar a la ruta ${ruta}`, 'Privilegios');
  return perm ? next() : next('/')
}

const routes = [
  {
    path: '/',
    name: 'Default',
    component: Default,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    beforeEnter: multiguard([isLoggedOut])
  },

  {
    path: '/presentaciones',
    name: 'Presentacion',
    component: Presentacion,
    beforeEnter: multiguard([isLoggedIn])
  },

  //Principal
  {
    path: '/ayuda',
    name: 'Ayuda',
    component: Ayuda,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/bitacora',
    name: 'Bitacora',
    component: Bitacora,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/empleados',
    name: 'Empleado',
    component: Empleado,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/clientes',
    name: 'Cliente',
    component: Cliente,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/proveedores',
    name: 'Proveedor',
    component: Proveedor,
    beforeEnter: multiguard([isLoggedIn])
  },

  //Seguridad
  {
    path: '/rol',
    name: 'Rol',
    component: Rol,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/usuarios',
    name: 'Usuario',
    component: Usuario,
    beforeEnter: multiguard([isLoggedIn])
  },
  /*
|--------------------------------------------------------------------------
| Restaurant Components
|--------------------------------------------------------------------------
  */
  {
    path: '/mesero',
    name: 'Mesero',
    component: Mesero,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/cocina',
    name: 'Cocina',
    component: Cocina,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/estado_orden',
    name: 'EstadoOrden',
    component: EstadoOrden,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/tipo_orden',
    name: 'TipoOrden',
    component: TipoOrden,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/categoria_comida',
    name: 'CategoriaComida',
    component: CategoriaComida,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/productos_restaurante',
    name: 'ProductoRestaurante',
    component: ProductoRestaurante,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/ordenes',
    name: 'Ordenes',
    component: Ordenes,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/mesas',
    name: 'Mesa',
    component: Mesa,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/restaurante_caja',
    name: 'CajaRestaurante',
    component: CajaRestaurante,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/reporte_restaurante_gastos',
    name: 'GastosRestaurante',
    component: GastosRestaurante,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/reporte_restaurante_caja',
    name: 'Caja',
    component: ReporteCaja,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/reporte_restaurante_inventario',
    name: 'Inventario',
    component: InventarioRestaurante,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/reporte_restaurante_venta',
    name: 'VentaRestaurante',
    component: VentaRestaurante,
    beforeEnter: multiguard([isLoggedIn])
  },
  /*
|--------------------------------------------------------------------------
| Hotel Components
|--------------------------------------------------------------------------
  */
  {
    path: '/tipo_cama',
    name: 'HTipoCama',
    component: HTipoCama,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/habitacion',
    name: 'HHabitacion',
    component: HHabitacion,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/insumo',
    name: 'HInsumo',
    component: HInsumo,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/kardex',
    name: 'HKardex',
    component: HKardex,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/registrar',
    name: 'HRegistrar',
    component: HRegistrar,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/reservacion',
    name: 'HReservaciones',
    component: HReservaciones,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/check_in',
    name: 'HCheckIn',
    component: HCheckIn,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/check_out',
    name: 'HCheckOut',
    component: HCheckOut,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/pago',
    name: 'HPagos',
    component: HPagos,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/reservaciones',
    name: 'HHistorial',
    component: HHistorial,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/caja_chica_hotel',
    name: 'HCajaChica',
    component: HCajaChica,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/dashboard_hotel',
    name: 'HDashboard',
    component: HDashboard,
    beforeEnter: multiguard([isLoggedIn])
  }
]

export default new Router({
  scrollBehavior: (to, from, savedPosition) => {
    return new Promise((resolve, reject) => {
      setTimeout(() => {
        resolve({ x: 0, y: 0 })
      }, 500)
    })
  },
  routes
})
