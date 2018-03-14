$(function() {
    
    $("#novoReembolso").click(function(){
        $("#form1").attr('action','form_reembolso.php');
        $("#form1").submit();
    });      
    
    $("#fun2").click(function(){
        if($(this).is(":checked")){
            $("#nome_razao").show();
            $("#user").hide();
            $("#user").val('-1');
            $("#banco").val('');
            $("#agencia").val('');
            $("#conta").val('');
            $("#nomefavo").val('');
            $("#cpf").val('');
        }
    });
    
    $("#fun1").click(function(){
        if($(this).is(":checked")){
            $("#nome_razao").hide();
            $("#user").show();
            $("#nome_razao").val('');
        }
    });
    
    $('#user').change(function(){
        var user = $("#user").val();                  
        
        $.ajax({
            url:"funcionario.php",
            type:"POST",
            dataType:"json",
            data:{
                id:user,
                method:"trazer_funcionario"
            },
            success:function(data){
                console.log(data);
                if(data.status){
                    
                    $("#banco").val(data.banco);
                    $("#agencia").val(data.agencia);
                    $("#conta").val(data.conta);
                    $("#nomefavo").val(data.nome);
                    $("#cpf").val(data.cpf);
                }
            }
        });                        
    });
    
    $(".bt-image").on("click", function() {
        var action = $(this).data("type");
        var key = $(this).data("key");
        
        if(action === "gerar_doc") {
            $("#id").val(key);
            $("#form1").attr('action','doc_reembolso.php');            
            $("#form1").submit();
        }
    });
});