$(function() {
    
    $(".bt-image").on("click", function() {
        var action = $(this).data("type");
        var key = $(this).data("key");        

        if(action === "visualizar") {
            $("#banco").val(key);
            $("#form1").attr('action','detalhes_banco.php');
            $("#form1").submit();
            
        }else if(action === "editar"){
            $("#pagamento").val(key);
            $("#form1").attr('action','form_pagamento.php');
            $("#form1").submit();
            
        }else if(action === "excluir"){
//            thickBoxConfirm("Fechar", "Deseja realmente excluir essa pagamento?", "500", "300", function(data){
//                if(data == true){
//                    $.ajax({
//                        url:"del_pagamento.php",
//                        type:"POST",
//                        dataType:"json",
//                        data:{
//                            id:key,
//                            method:"excluir_pagamento"
//                        },
//                        success:function(data){                            
//                            if(data.status){
//                                $("#"+key).remove();                                
//                            }
//                        }
//                    });
//                }
//            });
            bootConfirm("Deseja realmente excluir essa pagamento?", "Excluir", function(data){
                if(data == true){
                    $.ajax({
                        url:"del_pagamento.php",
                        type:"POST",
                        dataType:"json",
                        data:{
                            id:key,
                            method:"excluir_pagamento"
                        },
                        success:function(data){                            
                            if(data.status){
                                $("#"+key).remove();                                
                            }
                        }
                    });
                }
            }, 'warning');
        }
    });
    
    $("#novoPgt").click(function(){
        $("#form1").attr('action','form_pagamento.php');
        $("#form1").submit();
    });
    
});