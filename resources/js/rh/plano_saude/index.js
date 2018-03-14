$(function() {
    
    $("body").on('click', ".action_plano", function(){
        var acao = $(this).data('acao');
        var id_plano_saude = $(this).data('key');                
        
        $.post("action/action_plano.php", {bugger:Math.random(), action:'form_plano', id_plano_saude:id_plano_saude, tipo:acao}, function(resultado){
            bootDialog(
                resultado, 
                "<strong>"+acao+"</strong> Plano de Saúde",
                [{
                    label: 'Fechar',
                    action: function (dialog) {
                        dialog.close();
                    }
                }, {
                    label: 'Enviar',
                    cssClass: 'btn-primary' ,
                    action: function (dialog) {
                        var verificacao = true;
                        $('#form1').validationEngine();
                        if (!$("#form1").validationEngine('validate')){
                            verificacao = false;
                        }
                        if(acao == 'Cadastrar'){
                            $.post("action/action_plano.php", {bugger:Math.random(), action:'verificar_cnpj', cnpj:$('#cnpj').val()}, function(r1){
                                if(parseInt(r1) > 0){
                                    verificacao = false;
                                    bootAlert('Cnpj já Cadastrado','Cnpj já Cadastrado',null,'danger');
                                }
                                if (verificacao == true) {
                                    $("#form1").submit();
                                }
                            });
                        } else if(acao == 'Editar'){
                            if (verificacao == true) {
                                $("#form1").submit();
                            }
                        }
                    }
                }],
                'info'
            );
            remove_carregando_modal();
        });                
    });
    
    $("body").on('click', ".deletar", function(){
        var id_plano_saude = $(this).data("key");
        var nome = $(this).data("nome");
        bootConfirm(
            "Deseja <strong>DELETAR</strong> a Plano de Saúde "+nome+"?",
            'Deletar Saída', 
            function(dialog){
                if(dialog == true){
                    $.post("action/action_plano.php", {bugger:Math.random(), id_plano_saude:id_plano_saude, action:'Deletar'}, function(resultado){
                        bootAlert('Plano de Saúde Deletado!', 'Plano de Saúde Deletado!', function(){window.location.reload();}, 'success');
                    });
                }
            },
            'warning'
        );
    });
    
    $(".detalhe_saude").on("click", function() {
        var id_plano = $(this).data("key");
        var url = 'participantes_plano.php';
        
        $.post(url,{id_plano:id_plano},function(data){
            bootDialog(data,'Participantes');
        });
    });
});