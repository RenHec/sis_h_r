export default {
    captureError(r){
        if(r.response){
            if (r.response.data.error == undefined && typeof r.response.data === 'object') {
                for (let value of Object.values(r.response.data)) {
                    toastr.error(value, 'Mensaje')
                }
            } else {
                toastr.error(r.response.data.error, 'error')
            }
            return true
        }
        return false
    }
}