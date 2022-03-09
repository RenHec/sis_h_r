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
import Categoria from '@/components/catalogo/CategoriaComponent'
import Marca from '@/components/catalogo/MarcaComponent'
import Presentacion from '@/components/catalogo/PresentacionComponent'
import Bodegas from '@/components/catalogo/BodegasComponent'

//Principal
import ComprasyVentas from '@/components/principal/ComprasyVentasComponent'
import RegistrarCompras from '@/components/principal/RegistrarComprasComponent'
import RegistrarVentas from '@/components/principal/RegistrarVentasComponent'
import Devoluciones from '@/components/principal/DevolucionesComponent'
import Proveedores from '@/components/principal/ProveedoresComponent'
import TrasladosBodega from '@/components/principal/TrasladosBodegaComponent'
import ProductosStock from '@/components/principal/ProductosStockComponent'
import Movimientos from '@/components/principal/MovimientosComponent'
import Ayuda from '@/components/principal/AyudaComponent'

import Ventasgenerales from '@/components/reporte/VentasGeneralesComponent'
import Productosmasvendidos from '@/components/reporte/ProductosmasvendidosComponent'
import Productosproximoavencer from '@/components/reporte/ProductosproximoavencerComponent'
import Stockporcategoriayproducto from '@/components/reporte/StockporcategoriayproductoComponent'
import Ventasdiarias from '@/components/reporte/VentasdiariasComponent'
import Gananciasdiariasymensuales from '@/components/reporte/GananciasdiariasymensualesComponent'
import Movimientoenbodegas from '@/components/reporte/MovimientoenbodegasComponent'
import Logdeaccesos from '@/components/reporte/LogdeaccesosComponent'
/*
|--------------------------------------------------------------------------
| Restaurant Components
|--------------------------------------------------------------------------
|
|
*/
import Mesero from '@/components/restaurante/mesero/MeseroComponent'
import Cocina from '@/components/restaurante/cocina/CocinaComponent'
import EstadoOrden from '@/components/restaurante/estado-orden/IndexComponent'
import TipoOrden from '@/components/restaurante/tipo-orden/IndexComponent'
import CategoriaComida from '@/components/restaurante/categoria-comida/IndexComponent'

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
    alert(`Ocurrio un problema al intentar ingresar a ${to.path}`, 'Problema')
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
    path: '/categorias',
    name: 'Categoria',
    component: Categoria,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/marcas',
    name: 'Marca',
    component: Marca,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/presentaciones',
    name: 'Presentacion',
    component: Presentacion,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/bodegas',
    name: 'Bodegas',
    component: Bodegas,
    beforeEnter: multiguard([isLoggedIn])
  },

  {
    path: '/compras_ventas',
    name: 'ComprasyVentas',
    component: ComprasyVentas,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/registrar_venta',
    name: 'RegistrarVentas',
    component: RegistrarVentas,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/registrar_compra',
    name: 'RegistrarCompras',
    component: RegistrarCompras,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/devoluciones',
    name: 'Devoluciones',
    component: Devoluciones,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/proveedores',
    name: 'Proveedores',
    component: Proveedores,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/productos',
    name: 'ProductosStock',
    component: ProductosStock,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/traslado_bodega',
    name: 'TrasladosBodega',
    component: TrasladosBodega,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/movimientos',
    name: 'Movimientos',
    component: Movimientos,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/ayuda',
    name: 'Ayuda',
    component: Ayuda,
    beforeEnter: multiguard([isLoggedIn])
  },

  {
    path: '/ventas_generales',
    name: 'Ventasgenerales',
    component: Ventasgenerales,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/productos_mas_vendidos',
    name: 'Productosmasvendidos',
    component: Productosmasvendidos,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/productos_proximos_vencer',
    name: 'Productosproximoavencer',
    component: Productosproximoavencer,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/stock_categoria_productos',
    name: 'Stockporcategoriayproducto',
    component: Stockporcategoriayproducto,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/ventas_diarias',
    name: 'Ventasdiarias',
    component: Ventasdiarias,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/ganancias_diarias_mensuales',
    name: 'Gananciasdiariasymensuales',
    component: Gananciasdiariasymensuales,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/movimiento_bodegas',
    name: 'Movimientoenbodegas',
    component: Movimientoenbodegas,
    beforeEnter: multiguard([isLoggedIn])
  },
  {
    path: '/log_accessos',
    name: 'Logdeaccesos',
    component: Logdeaccesos,
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
