//Audio notificador
exports.install = function (Vue, options) {
    //Audio notificador
    Vue.prototype.notificador_audible = function (audio) {
        return new Promise((resolve, reject) => {
            try {
                let sound = new Audio(audio)
                sound.volume = 100 / 100
                sound.onended = () => {
                    resolve()
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
};