$(function() {
    $('body').on('click', '.tipo_obrig', function(){
        //var key = elem.data('original-title');
        var key = $(this).data('key');
        //console.log(elem.next());
        if($('.l'+key).html() == ''){
            cria_carregando_modal();
            $.post("../actions/action_obrigacoes.php", {bugger:Math.random(), id:key, action:'show_obrigacoes_inst'}, function(resultado){
                $('.l'+key).html(resultado);
                remove_carregando_modal();
                $("[data-toggle='tooltip']").tooltip(); 
            });
        }
        $(".lista_obrig").slideUp();
        if($('.l'+key).css('display') == 'none'){
            $('.l'+key).slideDown();
            $('.l'+key).find( ".seta" ).removeClass( "fa-arrow-circle-down" ).addClass( "fa-arrow-circle-up" );
        } else {
            $('.l'+key).find( ".seta" ).removeClass( "fa-arrow-circle-up" ).addClass( "fa-arrow-circle-down" );
        }
    });
    
    $("body").on('click', ".ver_anexos", function(){
        var id = $(this).data("key");
        cria_carregando_modal();
        $.post("../actions/action_obrigacoes.php", {bugger:Math.random(), id:id, action:'ver_anexos'}, function(resultado){
            bootDialog(
                resultado, 
                'Ver Anexos '+id, 
                [{
                    label: 'Fechar',
                    action: function (dialog) {
                        dialog.close();
                    }
                }],
                'primary'
            );
            remove_carregando_modal();
        });
    });
    
    $("body").on('click', ".editar_oscip", function(){
        var id = $(this).data("key");
        $("#editar_obrigacao").prop('action','form_obrigacoes.php').append($("<input>",{type:'hidden', name:'id_obrigacao', value:id}));
        $("#editar_obrigacao").submit();
    });
    
    $("body").on('click', ".renovar_oscip", function(){
        var id = $(this).data("key");
        $("#editar_obrigacao").prop('action','form_obrigacoes.php').append($("<input>",{type:'hidden', name:'id_obrigacao', value:id}),$("<input>",{type:'hidden', name:'renovar', value:'renovar'}));
        $("#editar_obrigacao").submit();
    });
    
    $("body").on('click', ".excluir_oscip", function(){
        var id = $(this).data("key");
        bootConfirm("Deseja Excluir está Obrigação","Excluir Obrigação "+id, function(data){
            if(data == true){
                $.post("../actions/action_obrigacoes.php", {bugger:Math.random(), id:id, action:'excluir_oscip'}, function(resultado){
                    bootDialog(
                        "Obrigação Excluída com Sucesso", 
                        //resultado,
                        'Excluir Obrigação '+id, 
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