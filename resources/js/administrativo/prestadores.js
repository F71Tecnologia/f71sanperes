$(function () {

    $("body").on("click", ".novo-documento", function () {
        $(".novo-documento, .div-novo-documento").toggle();
    });

    $("body").on('click', ".editar_prestador", function () {
        var id = $(this).data("key");
        $("#form_prestador").prop('action', 'form_prestador.php').append($("<input>", {type: 'hidden', name: 'id_prestador', value: id}));
        $("#form_prestador").submit();
    });
    
    $("body").on('click', ".abrir_processo", function () {
        $('.modal.in').modal('hide') ;
    });

    $("body").on('click', ".ver_documentos", function () {
        var id = $(this).data('key');

        cria_carregando_modal();
        $.post("/intranet/admin/actions/action_prestadores.php", {bugger: Math.random(), id_prestador: id, action: 'ver_documentos'}, function (resultado) {
            //console.log(resultado);
            bootDialog(
                resultado,
                "<strong>Documentos Prestador: " + id + "</strong>",
                [{
                    label: 'Fechar',
                    action: function (dialog) {
                        dialog.close();
                        //window.location.reload();
                    }
                }],
            'primary'
            );
            remove_carregando_modal();
        });
    });

    $("body").on('click', ".form_duplicar_prestador", function () {
        var id = $(this).data('key');

        cria_carregando_modal();
        $.post("/intranet/admin/actions/action_prestadores.php", {bugger: Math.random(), id_prestador: id, action: 'form_duplicar_prestador'}, function (resultado) {
            //console.log(resultado);
            new BootstrapDialog({
                nl2br: false,
                size: BootstrapDialog.SIZE_WIDE,
                type: 'type-primary',
                title: 'Duplicar Prestador',
                message: resultado,
                closable: false,
                buttons:
                [{
                    label: 'Fechar',
                    action: function (dialog) {
                        dialog.close();
                        //window.location.reload();
                    }
                }]
            }).open();
            remove_carregando_modal();
        });
    });

    $("body").on('click', ".ger_anexos", function () {
        var id_documento = $(this).data('doc');
        var id_prestador = $(this).data('key');

        cria_carregando_modal();
        $.post("/intranet/admin/actions/action_prestadores.php", {bugger: Math.random(), id_prestador: id_prestador, id_documento: id_documento, action: 'ger_anexos'}, function (resultado) {
            bootDialog(
                    resultado,
                    "<strong>Gerenciar Anexos</strong>",
                    [{
                            label: 'Fechar',
                            action: function (dialog) {
                                dialog.close();
                                //window.location.reload();
                            }
                        }],
                    'primary'
                    );
            remove_carregando_modal();
        });
    });

    $("body").on('click', ".editar_documento", function () {
        //$(this).parent().prev().children().addClass("col-sm-5");
        $(this).parent().prev().children().children().removeProp("disabled");
        $(this).parent().prev().children().next().removeClass("hide");
    });

    $("body").on('click', ".cancelar_edicao", function () {
        //$(this).parent().prev().removeClass("col-sm-5");
        $(this).parent().prev().children().val($(this).parent().prev().children().data('data'));
        $(this).parent().prev().children().prop("disabled", true);
        $(this).parent().addClass("hide");
    });

    $("body").on('click', ".salvar_edicao", function () {

        var id_documento = $(this).data('documento');
        var data = $("#data_" + id_documento).val();

        $.post("/intranet/admin/actions/action_prestadores.php", {bugger: Math.random(), data: data, id_documento: id_documento, action: 'salvar_edicao'}, function (resultado) {
            //console.log(resultado);
            bootAlert(
                    "Documento Atualizado!",
                    "<strong>Edição de Documento</strong>",
                    [{
                            label: 'Fechar',
                            action: function (dialog) {
                                dialog.close();
                                //window.location.reload();
                            }
                        }],
                    'success'
                    );
            remove_carregando_modal();
        });

        $(this).parent().prev().children().prop("disabled", true);
        $(this).parent().addClass("hide");
    });

    $("body").on('click', ".excluir_documento", function () {
        var tr = $(this).parent().parent();
        var id_documento = $(this).data('documento');

        bootConfirm(
                "Confirmar Exclusão do Documento " + id_documento + "?",
                "<strong>Exclusão de Documento</strong>",
                function (data) {
                    if (data == true) {
                        $.post("/intranet/admin/actions/action_prestadores.php", {bugger: Math.random(), id_documento: id_documento, action: 'excluir_documento'}, function (resultado) {
                            //console.log(resultado);
                            bootAlert(
                                    "Documento Excluido!",
                                    "<strong>Exclusão de Documento</strong>",
                                    [{
                                            label: 'Fechar',
                                            action: function (dialog) {
                                                dialog.close();
                                                //window.location.reload();
                                            }
                                        }],
                                    'success'
                                    );
                        });
                        tr.remove();
                    }
                },
                'warning'
                );
    });

    $("body").on('click', ".gerenciar_prestador", function () {
        var id = $(this).data('key');

        cria_carregando_modal();
        $.post("/intranet/admin/actions/action_prestadores.php", {bugger: Math.random(), id_prestador: id, action: 'gerenciar_prestador'}, function (resultado) {
            //console.log(resultado);
            new BootstrapDialog({
                nl2br: false,
                size: BootstrapDialog.SIZE_WIDE,
                type: 'type-primary',
                title: 'Gerenciar Prestador',
                message: resultado,
                closable: false,
                buttons:
                        [{
                                label: 'Fechar',
                                action: function (dialog) {
                                    dialog.close();
                                    //window.location.reload();
                                }
                            }]
            }).open();
            remove_carregando_modal();
        });
    });

    $('body').on('click', '.menu', function () {
        //console.log($(this).data('target'));
        $('.collapse').slideUp();
        $($(this).data('target')).slideDown();
    });


//    $("body").on('click', ".excluir_parceiro", function(){
//        var id = $(this).data("key");
//        bootConfirm("Deseja Excluir este Parceiro","Excluir Parceiro "+id, function(data){
//            if(data == true){
//                $.post("../actions/action_parceiros.php", {bugger:Math.random(), id_parceiro:id, action:'excluir_parceiro'}, function(resultado){
//                    bootDialog(
//                        "Parceiro Excluído com Sucesso", 
//                        //resultado,
//                        'Excluir Parceiro '+id, 
//                        [{
//                            label: 'Fechar',
//                            action: function (dialog) {
//                                window.location.reload();
//                            }
//                        }],
//                        'success'
//                    );
//                });
//            }
//        },"warning");
//    });

    if ($("#id_projeto").val() != '-1') {
        $.post('contratos_vencendo.php', {id_projeto: $("#id_projeto").val()}, function (data) {
            //console.log(data);
            var html = '';
            $.each(data.contratos, function (i, value) {
                html += "<tr>\n";
                html += "<td class=\"text-center\" style=\"min-width: 55px;\">" + value.id_prestador + "</td>\n";
                html += "<td class=\"text-center\" style=\"min-width: 80px;\">\n";
                html += "<button type=\"button\" class=\"btn btn-xs btn-info gerenciar_prestador\" data-key=\"" + value.id_prestador + "\" data-toggle=\"tooltip\" data-original-title=\"Gerenciar Prestador\"><i class=\"fa fa-book\"></i></button>\n";
                html += "<button type=\"button\" class=\"btn btn-xs btn-warning editar_prestador\" data-key=\"" + value.id_prestador + "\" data-toggle=\"tooltip\" data-original-title=\"Editar\"><i class=\"fa fa-edit\"></i></button>\n";
                html += "</td>\n";
                html += "<td class=\"\">" + value.razao + "</td>\n";
                html += "<td class=\"text-center\" style=\"width: 160px;\">" + value.cnpj + "</td>\n";
                html += "<td class=\"text-center\" style=\"width: 80px;\">" + value.encerrado_em + "</td>\n";
                html += "</tr>\n";
            });
            $("#table-contratos-vencendo table tbody").html(html);
            if (data.status) {
                BootstrapDialog.show({
                    nl2br: false,
                    title: 'Contratos Vencendo',
                    message: $("#table-contratos-vencendo").html(), 
                    type: 'type-warning',
                    size: 'size-wide',
                    buttons: [{
                            cssClass: 'btn-warning',
                            label: 'Fechar',
                            action: function (dialogItself) {
                                dialogItself.close();
                            }
                        }]
                });
            }
        }, 'json');
    }

});