$(document).ready(function () {
    $("a[data-toggle=tooltip]").tooltip({container: 'body'});

    // VOLTAR EVENTO PARA ATIVIDADE NORMAL
    $(".voltar").click(function () {
        var eventos = $(this).attr("data-key");
        BootstrapDialog.confirm('Deseja realmente voltar esse funcionário para atividade normal?', null, function (result) {
            if (result) {
                $("#id_evento").val(eventos);
                $.ajax({
                    url: "../methods.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        method: "cadEvento",
                        id_evento: eventos
                    },
                    success: function (data) {
                        if (data.status) {
                            history.go(0);
                        }
                    }
                });
            }
        });
    });

    $("body").on("focusin", ".data", function () {
        $(".data").mask("99/99/9999");
        $('.data').datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            yearRange: '2005:c+1',
            beforeShow: function () {
                setTimeout(function () {
                    $('.ui-datepicker').css('z-index', 5010);
                }, 0);
            }
        });
    });
    
    $('.infobox').on('click', function(){
        var id_licenca = $(this).data('key');
        var title = $(this).children().next().children().next().children().html().replace('<br>', '');
        if($(this).data('qtd') > 0){
            cria_carregando_modal();
            $.post("", {bugger:Math.random(), id:id_licenca, action:'ver_clt_licenca'}, function(resultado){
                bootAlert(
                    '<table class="table table-bordered table-condensed table-hover table-striped text-sm">'+resultado+'</table>', 
                    title, 
                    null,
                    'info'
                );
                remove_carregando_modal();
            });
        }
    });
    
//    $.post("", {bugger:Math.random(), action:'aviso'}, function(resultado){
//        var aviso = $(resultado);
//        aviso.find('#qtd').val();
//        if(aviso.find('#open_dialog').val()){
//            new BootstrapDialog({
//                nl2br: false,
//                size: BootstrapDialog.SIZE_WIDE,
//                title: 'Aviso Férias',
//                message: aviso,
//                closable: false,
//                type: 'type-primary',
//                buttons: [
//                    {
//                        label: 'Fechar',
//                        cssClass: 'btn-default',
//                        action: function (dialog) {
//                            typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(true);
//                            $.post("", {bugger:Math.random(), action:'aviso_visto'}, function(resultado){
//                                if(resultado) dialog.close();
//                            });
//                        }
//                    }, 
//                    {
//                        label: 'Ver Férias',
//                        cssClass: 'btn-success',
//                        action: function (dialog) {
//                            typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(true);
//                            $.post("", {bugger:Math.random(), action:'aviso_visto'}, function(resultado){
//                                if(resultado) {
//                                    dialog.close();
//                                    cria_carregando_modal();
//                                    window.location.href = '../rh_novaintra/ferias';
//                                }
//                            });
//                        }
//                    }]
//            }).open();
//        }
//    });
});