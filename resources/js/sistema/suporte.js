
$(function() {
    
    acoesBotoes();
    
});

var acoesBotoes = function(){
    $(".bt-image").click(function(){
        var tipo = $(this).data('type');
        var suporte = $(this).data('key');
        if(tipo == "fechar"){
            thickBoxConfirm("Fechar", "Deseja realmente fechar esse chamado", "500", "300", function(data){
                if(data == true){
                    $.ajax({
                        url:"fim_suporte.php",
                        type:"POST",
                        dataType:"json",
                        data:{
                            id:suporte,
                            method:"fechar_chamado"
                        },
                        success:function(data){
                            if(data.status){
                                $("#"+suporte).remove();
                            }
                        }
                    });
                }
            });
        }else if(tipo == "ver"){
            $("#id_suporte").val(suporte);
            $("form:first").attr('action','detalhe.php').submit();
        };
    });
    
    $(".fim_chamado").click(function(){
        var chave = $(this).data('key');
        thickBoxConfirm("Fechar", "Deseja realmente fechar esse chamado", "500", "300", function(data){                                                
            if(data == true){
                $.ajax({
                    url:"fim_suporte.php",
                    type:"POST",
                    dataType:"json",
                    data:{
                        id:chave,
                        method:"fechar_chamado"
                    },
                    success:function(data){
                        if(data.status){
                            $(window.location).attr('href','index.php');
                        }
                    }
                });
            }            
        });
    });
};