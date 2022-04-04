//Audio notificador
exports.install = function (Vue, options) {
    //Audio notificador
    Vue.prototype.notificador_audible = function (audio) {
        return new Promise((resolve, reject) => {
            try {
                let sound = new Audio(audio)
                sound.volume = 100 / 100
                sound.onended = () => {
                    resolve("audio")
                }
                sound.play()
            } catch (error) {
                reject(error)
            }
        })
    };

    //Formato para monedas GTQ
    Vue.prototype.formato_moneda = function (cantidad = 1, precio, descuento = 0) {
        let cantidad_no_null = cantidad ? cantidad : 0
        let precio_no_null = precio ? precio : 0
        let descuento_no_null = descuento ? descuento : 0
        let monto =
            parseInt(cantidad_no_null) * parseFloat(precio_no_null) -
            parseFloat(descuento_no_null)
        return monto.toLocaleString('es-GT', {
            style: 'currency',
            currency: 'GTQ',
            minimumFractionDigits: 2,
        })
    };

    //Captura error catch
    Vue.prototype.catalogo_tipo_pago = function () {
        return ['EFECTIVO', 'TARJETA', 'CHEQUE']
    };

    //Captura error catch
    Vue.prototype.errorResponse = function (e) {
        if (e.response) {
            if (e.response.data.code === 404) {
                this.$toastr.warning(e.response.data.error, 'Advertencia')
                return
            } else if (e.response.data.code === 423) {
                this.$toastr.warning(e.response.data.error, 'Advertencia')
                return
            } else {
                for (let value of Object.values(e.response.data)) {
                    this.$toastr.error(value, 'Mensaje')
                }
            }
            return
        } else {
            console.info(e)
        }
    };
};