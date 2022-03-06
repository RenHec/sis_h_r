<template>
  <v-row justify="center" align="center">
    <v-overlay :value="loading">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-col cols="12" md="12">
      <v-card>
        <v-overlay :value="loading">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-card-title>
          <span class="headline">
            Pantalla de ayuda
          </span>
        </v-card-title>

        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12" md="12">
                <v-stepper v-model="pasos" vertical>
                  <v-stepper-step
                    :complete="pasos > 1"
                    step="1"
                    @click="pasos = 1"
                  >
                    Requerimientos
                    <small>
                      El sistema esta desarrollado basado en los siguientes
                      requerimientos
                    </small>
                  </v-stepper-step>

                  <v-stepper-content step="1">
                    <embed
                      :src="requerimiento"
                      type="application/pdf"
                      width="100%"
                      height="930"
                      style="
                        overflow: hidden;
                        overflow-x: hidden;
                        overflow-y: hidden;
                        border: 1px solid #666ccc;
                      "
                      title="Comprobante"
                      frameborder="1"
                    />
                  </v-stepper-content>

                  <v-stepper-step
                    :complete="pasos > 2"
                    step="2"
                    @click="pasos = 2"
                  >
                    Punto de Venta (POS)
                  </v-stepper-step>

                  <v-stepper-content step="2">
                    <br />
                    <p>
                      El sistema esta desarrollado con las siguientes
                      tecnologías
                    </p>
                    <ul>
                      <li>
                        Front End: para el front-end se utiliza el lenguaje de
                        programación asincrono
                        <b>
                          <a
                            href="https://www.javascript.com/"
                            target="_blank"
                            rel="noopener noreferrer"
                          >
                            JavaScript
                          </a>
                        </b>
                        con el framework de
                        <b>
                          <a
                            href="https://vuejs.org/"
                            target="_blank"
                            rel="noopener noreferrer"
                          >
                            VUE JS
                          </a>
                        </b>
                        y el entorno de ejecución multiplataforma
                        <b>Node JS</b>
                      </li>
                      <li>
                        Back End: para el back-end se utiliza el lenguaje de
                        programación
                        <b>
                          <a
                            href="https://www.php.net/"
                            target="_blank"
                            rel="noopener noreferrer"
                          >
                            PHP
                          </a>
                        </b>
                        con el framework de
                        <b>
                          <a
                            href="https://laravel.com/"
                            target="_blank"
                            rel="noopener noreferrer"
                          >
                            Laravel
                          </a>
                        </b>
                        y el gestionador de paquetes
                        <b>
                          <a
                            href="https://getcomposer.org/"
                            target="_blank"
                            rel="noopener noreferrer"
                          >
                            Composer
                          </a>
                        </b>
                      </li>
                      <li>
                        Base de Datos: para la base de datos relacional se
                        utiliza
                        <b>
                          <a
                            href="https://www.mysql.com/"
                            target="_blank"
                            rel="noopener noreferrer"
                          >
                            MySQL
                          </a>
                        </b>
                      </li>
                      <li>
                        CSS: para la maquetación del diseño de colores, botones,
                        etc.. y el tema del
                        <b>responsive</b>
                        , por ser un desarrollo en ambiente WEB se utiliza la
                        libreria de
                        <b>
                          <a
                            href="https://vuetifyjs.com/en/"
                            target="_blank"
                            rel="noopener noreferrer"
                          >
                            Vuetify
                          </a>
                        </b>
                        que esta basada en componentes de
                        <b>
                          <a
                            href="https://material.io/"
                            target="_blank"
                            rel="noopener noreferrer"
                          >
                            Material
                          </a>
                        </b>
                      </li>
                      <li>
                        Servidor HTTP: para el despliegue de la aplicación utilizamos
                        <b><a href="https://httpd.apache.org/" target="_blank" rel="noopener noreferrer">Apache</a></b>
                      </li>
                      <li>
                        Sistema Operativo: al ser un sistema WEB es soportado por todos los sistemas operativos, solo es necesario accesar por medio de una navegador WEB.
                      </li>
                    </ul>
                    <small>Existe una herramienta que cuenta con todo lo necesario para poder desplegar el sistema, la herramienta lleva por nombre <b><a href="https://laragon.org/" target="_blank" rel="noopener noreferrer">Laragon</a></b></small>
                    <br />
                  </v-stepper-content>

                  <v-stepper-step
                    :complete="pasos > 3"
                    step="3"
                    @click="pasos = 3"
                  >
                    Multi-tienda
                  </v-stepper-step>

                  <v-stepper-content step="3">
                    <br />
                    <p>
                      El sistema esta diseñado para soportar multiples
                      sucursales que pertenezcan a la misma entidad.
                    </p>
                    <br />
                  </v-stepper-content>

                  <v-stepper-step
                    :complete="pasos > 4"
                    step="4"
                    @click="pasos = 4"
                  >
                    Bodegas
                  </v-stepper-step>

                  <v-stepper-content step="4">
                    <br />
                    <p>
                      El sistema permite adminsitrar el stock dependiendo de la asignación de bodega que tenga configurada el producto en la sucursal.
                    </p>
                    <br />
                  </v-stepper-content>

                  <v-stepper-step
                    :complete="pasos > 5"
                    step="5"
                    @click="pasos = 5"
                  >
                    Traslados de producto
                  </v-stepper-step>

                  <v-stepper-content step="5">
                    <br />
                    <p>
                      El sistema permite realizar los movimientos entre las bodegas para abastecer a otras tiendas, sin perder de vista la trazabilidad de la transacción y mantener el cuadre de los inventarios principalmente.
                    </p>
                    <br />
                  </v-stepper-content>

                  <v-stepper-step
                    :complete="pasos > 6"
                    step="6"
                    @click="pasos = 6"
                  >
                    Presentaciones
                  </v-stepper-step>

                  <v-stepper-content step="6">
                    <br />
                    <p>
                      El sistema permite asignar más de una presentación al producto.
                    </p>
                    <br />
                  </v-stepper-content>

                  <v-stepper-step
                    :complete="pasos > 7"
                    step="7"
                    @click="pasos = 7"
                  >
                    Código de barra
                  </v-stepper-step>

                  <v-stepper-content step="7">
                    <br />
                    <p>
                      Para mayor referencia del producto el sistema permite buscar los productos por código de barra.
                    </p>
                    <br />
                  </v-stepper-content>

                  <v-stepper-step
                    :complete="pasos > 8"
                    step="8"
                    @click="pasos = 8"
                  >
                    Tipo de pago
                  </v-stepper-step>

                  <v-stepper-content step="8">
                    <br />
                    <p>
                      Al realizar una venta el sistema permite registrar el tipo de pago.
                    </p>
                    <br />
                  </v-stepper-content>

                  <v-stepper-step
                    :complete="pasos > 9"
                    step="9"
                    @click="pasos = 9"
                  >
                    Tipo de pago
                  </v-stepper-step>

                  <v-stepper-content step="9">
                    <br />
                    <p>
                      Se cuentan principalmente con tres roles siendo estos.
                      <ul>
                        <li>Gerente: Usuario que posee todos los permisos de inserción, actualización, eliminación y consulta a reportes.</li>
                        <li>Administrador: Usuario que posee permisos de inserción, actualización y consulta a algunos reportes.</li>
                        <li>Vendedor: Este usuario únicamente puede efectuar el proceso de venta.</li>
                      </ul>
                    </p>
                    <br />
                  </v-stepper-content>

                  <v-stepper-step
                    :complete="pasos > 10"
                    step="10"
                    @click="pasos = 10"
                  >
                    Caja chica
                  </v-stepper-step>

                  <v-stepper-content step="10">
                    <br />
                    <p>
                      El sistema permite llevar el control de caja chica.
                    </p>
                    <br />
                  </v-stepper-content>

                  <v-stepper-step
                    :complete="pasos > 11"
                    step="11"
                    @click="pasos = 11"
                  >
                    Alerta stock
                  </v-stepper-step>

                  <v-stepper-content step="11">
                    <br />
                    <p>
                      El sistema tiene la alerta cuando un producto en stock está próximo a escasear.
                    </p>
                    <br />
                  </v-stepper-content>

                  <v-stepper-step
                    :complete="pasos > 12"
                    step="12"
                    @click="pasos = 12"
                  >
                    Ingreso de producto al stock
                  </v-stepper-step>

                  <v-stepper-content step="12">
                    <br />
                    <p>
                      El sistema tiene la capacidad de registrar las compras no importando la presentación en la cual se adquieran los productos, es decir, libra, arroba, etc...
                    </p>
                    <br />
                  </v-stepper-content>

                  <v-stepper-step
                    :complete="pasos > 13"
                    step="13"
                    @click="pasos = 13"
                  >
                    Facturación
                  </v-stepper-step>

                  <v-stepper-content step="13">
                    <br />
                    <v-card elevation="2">
                      <v-card-text>Al realizar una venta el sistema emite un comprobante que se le entrega al cliente, para la generación del comprobante se utiliza la libreria <b><a href="http://www.fpdf.org/" target="_blank" rel="noopener noreferrer">FPDF</a></b></v-card-text>
                      <object :data="ticket" type="text/plain"
                      width="100%" style="height: 900px; background-color: white;">
                      <a :href="ticket">Descargar ejemplo</a>
                      </object>
                    </v-card>
                    <br />
                  </v-stepper-content>

                  <v-stepper-step
                    :complete="pasos > 14"
                    step="14"
                    @click="pasos = 14"
                  >
                    Módulos del sistema
                  </v-stepper-step>

                  <v-stepper-content step="14">
                    <v-card height="100%">
                    <br />
                    <p>
                        El sistema cuenta con los siguientes módulos y funcionalidad:
                    </p>
                    <v-list subheader>
                      <v-list-item
                        v-for="(modulo, index) in modulos"
                        :key="modulo.titulo"
                      >
                        <v-list-item-avatar size="100" tile>
                          <v-img
                            :alt="`modulo${index}`"
                            :src="modulo.imagen"
                          ></v-img>
                        </v-list-item-avatar>
                
                        <v-list-item-content>
                          <v-list-item-title v-text="modulo.titulo"></v-list-item-title>
                          <v-list-item-subtitle v-html="modulo.funcionalidad"></v-list-item-subtitle>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                    <br />
                    </v-card>
                  </v-stepper-content>

                  <v-stepper-step
                    :complete="pasos > 15"
                    step="15"
                    @click="pasos = 15"
                  >
                    Modelo Relacional
                  </v-stepper-step>

                  <v-stepper-content step="15">
                    <embed
                      :src="entidad"
                      type="application/pdf"
                      width="100%"
                      height="830"
                      style="
                        overflow: hidden;
                        overflow-x: hidden;
                        overflow-y: hidden;
                        border: 1px solid #666ccc;
                      "
                      title="Comprobante"
                      frameborder="1"
                    />
                  </v-stepper-content>

                  <v-stepper-step
                    :complete="pasos > 16"
                    step="16"
                    @click="pasos = 16"
                  >
                    Reportes
                  </v-stepper-step>

                  <v-stepper-content step="16">
                    <v-card height="100%">
                    <br />
                    <p>
                        Reportes exportables a PDF (Quienes pueden acceder a estos reportes)
                    </p>
                    <v-list subheader>
                      <v-list-item
                        v-for="(modulo, index) in reportes"
                        :key="modulo.titulo"
                      >
                        <v-list-item-avatar size="100" tile>
                          <v-img
                            :alt="`modulo${index}`"
                            :src="modulo.imagen"
                          ></v-img>
                        </v-list-item-avatar>
                
                        <v-list-item-content>
                          <v-list-item-title v-text="modulo.titulo"></v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list>
                    <br />
                    </v-card>
                  </v-stepper-content>
                </v-stepper>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
export default {
  name: 'Ayuda',
  data() {
    return {
      loading: false,
      pasos: 0,
      modulos: [],
      reportes: []
    }
  },

  created() {
    this.modulos.push({titulo: 'Web Form Login', imagen: `${this.$store.state.base_url}/ayuda/login.PNG`, funcionalidad: 'Inicio de sesión para usuarios registrados.'})
    this.modulos.push({titulo: 'Dashboard de control', imagen: `${this.$store.state.base_url}/ayuda/dashboard.PNG`, funcionalidad: '<lu><li>10 productos con mayor movimiento de venta por tienda</li><li>Productos próximos a vencer</li><li>Productos próximos a terminarse</li></lu>'})
    this.modulos.push({titulo: 'Web Form Principal', imagen: `${this.$store.state.base_url}/ayuda/menu.PNG`, funcionalidad: 'Control de todos los otros Web Forms'})
    this.modulos.push({titulo: 'Accesos para los Web Forms', imagen: `${this.$store.state.base_url}/ayuda/usuarios.PNG`, funcionalidad: '<lu><li>Usuarios</li><li>Cambios de Clave</li><li>Roles</li></lu>'})
    this.modulos.push({titulo: 'Compras y Ventas', imagen: `${this.$store.state.base_url}/ayuda/compras_ventas.PNG`, funcionalidad: '<lu><li>Registro de ventas</li><li>Registro de ventas</li><li>Compras y ventas</li></lu>'})
    this.modulos.push({titulo: 'Devoluciones', imagen: `${this.$store.state.base_url}/ayuda/devoluciones.PNG`, funcionalidad: 'Devoluciones realizadas'})
    this.modulos.push({titulo: 'Proveedores', imagen: `${this.$store.state.base_url}/ayuda/proveedores.PNG`, funcionalidad: 'Proveedores registrados'})
    this.modulos.push({titulo: 'Productos', imagen: `${this.$store.state.base_url}/ayuda/productos.PNG`, funcionalidad: '<lu><li>Productos</li><li>Stock</li></lu>'})
    this.modulos.push({titulo: 'Reportes', imagen: `${this.$store.state.base_url}/ayuda/reportes.PNG`, funcionalidad: 'Reportes realizados en base a los requerimientos'})
    this.modulos.push({titulo: 'Traslados de Bodega', imagen: `${this.$store.state.base_url}/ayuda/traslados.PNG`, funcionalidad: '<lu><li>Creación de traslado nuevo</li><li>Registro de traslados</li><li>Autorización de traslados</li></lu>'})
    this.modulos.push({titulo: 'Movimientos', imagen: `${this.$store.state.base_url}/ayuda/movimientos.PNG`, funcionalidad: 'Movimientos realizados en el stock'})
    this.modulos.push({titulo: 'Cerrar Sesión', imagen: `${this.$store.state.base_url}/ayuda/cerrar.PNG`, funcionalidad: '<lu><li>Cambiar contraseña</li><li>Caja</li><li>Cerrar sesión</li></lu>'})

    this.reportes.push({titulo: 'Reporte de ventas generales por medio de rango de fechas. (Gerente)', imagen: `${this.$store.state.base_url}/ayuda/reporte1.PNG`})
    this.reportes.push({titulo: 'Productos más vendidos y menos vendidos. (Gerente, Administrador)', imagen: `${this.$store.state.base_url}/ayuda/reporte2.PNG`})
    this.reportes.push({titulo: 'Productos próximos a vencer (establezca el tiempo según la naturaleza del mismo). (Gerente, administrador, Vendedor)', imagen: `${this.$store.state.base_url}/ayuda/reporte3.PNG`})
    this.reportes.push({titulo: 'Reporte de stock por categoría y por producto. (Gerente, administrador)', imagen: `${this.$store.state.base_url}/ayuda/reporte4.PNG`})
    this.reportes.push({titulo: 'Reporte de ventas diarias. (Gerente, administrador)', imagen: `${this.$store.state.base_url}/ayuda/reporte5.PNG`})
    this.reportes.push({titulo: 'Reporte de ganancias diarias y mensuales, las ganancias se determinan de la diferencia del precio del producto comprado al proveedor y el precio de venta al público. (Gerente)', imagen: `${this.$store.state.base_url}/ayuda/reporte6.PNG`})
    this.reportes.push({titulo: 'Reporte de movimientos de producto entre bodegas. (Gerente). (Gerente)', imagen: `${this.$store.state.base_url}/ayuda/reporte7.PNG`})
    this.reportes.push({titulo: 'Log de accesos al sistema. (Gerente).', imagen: `${this.$store.state.base_url}/ayuda/reporte8.PNG`})
  },

  methods: {},

  computed: {
    requerimiento() {
      return `${this.$store.state.base_url}/ayuda/requerimiento.pdf`
    },

    entidad() {
      return `${this.$store.state.base_url}/ayuda/entidad_relacion.pdf`
    },

    ticket() {
      return `${this.$store.state.base_url}/ayuda/ticket.txt`
    },
  },
}
</script>
