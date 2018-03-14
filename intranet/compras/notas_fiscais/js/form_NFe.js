$(document).ready(function () {
    //datepicker
    $('.data').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '2005:c+1'
    });

    // carregando projetos e regioes -------------------------------------------
    $('#regiao').change(function () {
        var destino = $(this).data('for');
        $.post("../../methods.php", {method: "carregaProjetos", regiao: $(this).val()}, function (data) {
            $("#" + destino).html(data);
        });
    });

    $('#projeto').change(function () {
        var destino = $(this).data('for');
        $.post("../../methods.php", {method: "carregaPrestadores", projeto: $(this).val()}, function (data) {
            $("#" + destino).html(data);
        });
    });

    // -------------------------------------------------------------------------
    // mascaras e formatações --------------------------------------------------
    $('body').on('focus', '.money', function () {
        $('.money').maskMoney({allowNegative: true, thousands: '.', decimal: ',', affixesStay: false});
    });
    $("#dt_emissao_nf, #data_vencimento").mask("99/99/9999");
    $("#cnpj, #cnpj1").mask("99.999.999/9999-99");
    // -------------------------------------------------------------------------
    // 
    // frm-arquivo-xml ---------------------------------------------------------

    $('body').on('click', "#limpa", function () {
        $("#tabela").html('');
        $("#habilitar").addClass('hidden');
        closeAllDialog();
    });

    $(".btn-impotacao-nfe").click(function () {
        var $this = $(this);
        var arr_post = {
            id_pedido: $this.data('id'),
            id_fornecedor: $this.data('fornecedor'),
            id_projeto: $this.data('projeto'),
            method: 'open_form_importacao_nfe'
        };
        $.post('nfe_controle.php', arr_post, function (data) {
            var funcao = function () {
                $("#nfe").filestyle({buttonText: " Escolher Arquivo"});
                $("#frm-arquivo-xml").ajaxForm({
                    dataType: 'json',
                    beforeSubmit: showRequest,
                    success: function (data) {
                        $("#salvar").prop('disabled', false);

                        // verifica se tem mensagem de retorno
                        if (typeof data.status !== 'undefined') {
                            if (data.status === false) {
                                bootAlert(data.msg, 'Atenção', null, 'danger');
                                data.settings = {erase_form: true};
                            }
                        }

                        // verifica se tem dados para exibicao
                        if (typeof data.dados !== 'undefined') {
                            // se tem, para todos, coloca o conteudo na div correta
                            $.each(data.dados, function (key, value) {
                                $('#' + key).html(value);
                            });
                        }

                        // determina inputs que devem ser limpados, e class/ids que devem sumir
                        if (typeof data.settings !== 'undefined') {
                            if (data.settings.erase_form === true) {
                                $(".habilitar").addClass('hidden');
                                $("#frm-arquivo-xml").find("input[type=text]:not([readonly]),input[type=file], textarea").val("");
                            } else {
                                $(".habilitar").removeClass('hidden');
                            }
                            if (data.settings.pedido_status == 4) {
                                $this.closest('tr').find('.pedido_status').html('<span class="label label-warning">Em Aberto</span>');
                            } else if (data.settings.pedido_status == 5) {
                                $this.closest('tr').remove();
                            }
                        }
                    }
                });
            };
            bootShow(data, 'Importação de NFe', 'info', funcao);
        });
    });


    // fim do frm-arquivo-xml --------------------------------------------------

    // form3 -------------------------------------------------------------------
    $("#form3").ajaxForm({
        beforeSubmit: function () {
            $("#visualizar-NFe").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
        },
        success: function (data) {
            $("#visualizar-NFe").html(data);
        }
    });

    $("#visualizar-NFe").on('click', '.nfe-detalhes', function () {
        console.log($(this).data('id'));
        $.post("visualizar_xml.php", {id_nfe: $(this).data('id'), projeto: $("#projeto").val(), regiao: $("#regiao").val(), method: 'Detalhes'}, function (dados) {
            BootstrapDialog.show({
                type: 'type-success',
                size: 'size-wide',
                nl2br: false,
                title: 'Detalhes da Nota Fiscal',
                message: dados,
            });
        });
    });

    $("#visualizar-NFe").on('click', '.nfe-cancelar', function () {
        console.log($(this).data('id'));
        var titulo = 'Cancelar Nota Fiscal!';
        var id = $(this).data('id');
        bootConfirm('Deseja prosseguir com o cancelamento ?', titulo, function (status) {
            if (status) {
                $.post('visualizar_xml.php', {method: 'cancelar', id: id}, function (data) {
                    if (data.status) {
                        bootAlert('Cancelado com sucesso!', titulo, null, 'success');
                        $('#tr-' + id).remove();
                    } else {
                        bootAlert('Erro ao Cancelar a nota.', titulo, null, 'danger');
                    }
                }, 'json');
            }
        }, 'danger');
    });
    //fim do form3 -------------------------------------------------------------



    // conferencia
    $('body').on('click', ".conferencia", function () {
        var id = $(this).data('id');
        $.post('../pedidos/pedidos_methods.php', {method: 'conferencia', id: id}, function (data) {
            var botoes=[{
                    label: 'Fechar',
                    action: function (dialog) {
                        typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(false);
                        dialog.close();
                    }
//                }, {
//                    icon: 'fa fa-arrow-circle-down',
//                    label: 'Finalizar',
//                    cssClass: 'btn-success finalizar',
//                    action: function (dialog) {
//                        dialog.close();
//                    }
                }];
            bootDialog(data, 'Conferência',botoes);
        });
    });
    // conferencia

    $('body').on('change', '.vinculo_prod', function () {
        var $this = $(this);
        var id_prod = $this.val();
        var cProd = $this.closest('td').find('.cProd').val();
        $.post('nfe_controle.php', {method: 'vincular_produto', id_prod: id_prod, cProd: cProd}, function (data) {
            if (data.status) {
                $("#import").trigger('click');
            } else {
                $this.colsest('tr').addClass('danger');
            }
        }, 'json');
    });
    
    $('body').on('click','.finalizar', function(){
        var $this = $(this);
        var id = $this.data('id');
        // depois implementarei o lance do desconto. talvez nem fique nessa tela.
//        bootConfirm('Tem certeza que deseja finalizar? Pode haver itens faltando.<br><br><form><label>Desconto*:</label><input type="text" name="desconto" class="form-control money"><p class="help-block">* Quando houver.</p><label>Detalhamento do Desconto:</label><textarea name="descricao_desconto" class="form-control" row=3></textarea></form>', 'Atenção', function(resp){
        bootConfirm('Tem certeza que deseja finalizar? Pode haver <strong>ITENS PENDENTES</strong>.', 'Atenção', function(resp){
            if(resp){
                $.post('nfe_controle.php',{id_pedido: id, method:'finalizar_pedido'},function(data){
                    if(data.status){
                        bootAlert('Finalizado.','Atenção',null,'success');
                        $this.closest('tr').remove();
                    }else{
                        bootAlert('Erro ao finalizar.','Atenção',null,'danger');
                    }
                },'json');
            }
        },'warning');
    });
});

function showRequest(formData, jqForm, options) {
    $("#salvar").prop('disabled', true);
//    var valid = $("#form2").validationEngine('validate');
    var valid = jqForm.validationEngine('validate');
    if (valid == true) {
//        $(".loading").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
        return true;
    } else {
        return false;
    }
}

function bootShow(msg, title, type, onshown) {
    if (typeof type === 'undefined' || type === '' || type === null) {
        type = 'primary';
    }
    BootstrapDialog.show({
        type: 'type-' + type,
        title: title,
        message: msg,
        nl2br: false,
        size: 'size-wide',
        onshown: onshown
    });
}

function closeAllDialog() {
    $.each(BootstrapDialog.dialogs, function (id, dialog) {
        dialog.close();
    });
    return true;
}

