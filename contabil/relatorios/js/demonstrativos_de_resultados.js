$(document).ready(function () {

    $("#form_dre").validationEngine();
    
//    $("#exercicio_periodo").hide();
//    $("#exercicio_ano").hide();
    $("input[name=exercicio]").change(function() {
        
        var val = $("input[name=exercicio]:checked").val();
        
        console.log(val);
        
        if(val == '1'){
            // mosta ano e esconde periodo
            $("#exercicio_periodo").hide();
            $("#exercicio_ano").show("slow");
        } else {
            // esconda ano e mostra periodo
            $("#exercicio_periodo").show("slow");
            $("#exercicio_ano").hide();
        }
    }); 
    
});
