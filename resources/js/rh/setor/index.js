$(function() {
    
    
    $("body").on('click', ".action_setor", function(){
        var acao = $(this).data('acao');
        var id_setor = $(this).data('key');
        $.post("action/action_setor.php", {bugger:Math.random(), action:'form_setor', id_setor:id_setor, tipo:acao}, function(resultado){
            bootDialog(
                resultado, 
                "<strong>"+acao+"</strong> Setor",
                [{
                    label: 'Fechar',
                    action: function (dialog) {
                        dialog.close();
                    }
                }, {
                    label: 'Enviar',
                    cssClass: 'btn-primary' ,
                    action: function (dialog) {
                        $('#form1').validationEngine();
                        if ($("#form1").validationEngine('validate')) {
                            $("#form1").submit();
                        }
                    }
                }],
                'warning'
            );
            remove_carregando_modal();
        });
    });
    
    $("body").on('click', ".deletar", function(){
        var id_setor = $(this).data("key");
        var nome = $(this).data("nome");
        bootConfirm(
            "Deseja <strong>DELETAR</strong> a Setor "+nome+"?",
            'Deletar Saída', 
            function(dialog){
                if(dialog == true){
                    $.post("action/action_setor.php", {bugger:Math.random(), id_setor:id_setor, action:'Deletar'}, function(resultado){
                        bootAlert('Setor Deletado!', 'Setor Deletado!', function(){window.location.reload();}, 'success');
                    });
                }
            },
            'warning'
        );
    });
});