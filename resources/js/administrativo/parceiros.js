$(function() {
    
    $("body").on('click', ".editar_parceiro", function(){
        var id = $(this).data("key");
        $("#editar_parceiro").prop('action','form_parceiros.php').append($("<input>",{type:'hidden', name:'id_parceiro', value:id}));
        $("#editar_parceiro").submit();
    });
    
    $("body").on('click', ".excluir_parceiro", function(){
        var id = $(this).data("key");
        bootConfirm("Deseja Excluir este Parceiro","Excluir Parceiro "+id, function(data){
            if(data == true){
                $.post("../actions/action_parceiros.php", {bugger:Math.random(), id_parceiro:id, action:'excluir_parceiro'}, function(resultado){
                    bootDialog(
                        "Parceiro Excluído com Sucesso", 
                        //resultado,
                        'Excluir Parceiro '+id, 
                        [{
                            label: 'Fechar',
                            action: function (dialog) {
                                window.location.reload();
                            }
                        }],
                        'success'
                    );
                });
            }
        },"warning");
    });
    
});