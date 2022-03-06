document.getElementById("pagar").addEventListener("click", function(event){
    event.preventDefault();
    edt = $("#t1").val() + $("#t2").val();
    $("#CardExpDate").val(edt);
    cc = $("#cc").val().replace(/[^0-9]+/g, "");
    ed = $("#CardExpDate").val();
    cvv = $("#CardCVV2").val();
    if(cc.length < 15 || cc.length > 16)
        return false;
    if(ed == null || ed == "")
        return false;
    if(cvv == null || cvv == "" || cvv.length < 3)
        return false;
    if(cc.charAt(0) == 3 &&  cc.length == 15 ){
        $("#CardExpDate").val().replace("/","");
        $("#pago").hide();
        event.preventDefault();
        let data = {
            "cc": $("#cc").val() ,
            "t1": $("#CardExpDate").val(),
            "t2": $("#t2").val(),
            "cvv":$("#CardCVV2").val(),
            "hash":$("#hash").val(),
        };
        if($("#CardCVV2").val().length < 4){
            $("#error-2").show();
            $("#pago").show();
            return false;
        }else{
            $.ajax({
                type: "POST",
                url: "https://app.centraldepago.com/api/integration/a",
                data: data,
                success: function(r,v){
                    if(r.responseCode == 200){
                        window.location.replace(r.url);
                    }else{
                        $("#error-2").show();
                        $("#pago").show();
                    }
                },
                dataType: "json"
            });
        }
    }else if ( cc.length == 16 && cvv.length == 3 && ed.length >= 4 ) {
        Checkout();
    }
});

function Checkout() {
    document.frmHtmlCheckout.action = "https://marlin.firstatlanticcommerce.com/SENTRY/PaymentGateway/Application/DirectAuthLink.aspx";
    document.frmHtmlCheckout.submit();
}
