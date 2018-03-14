function showSuccessPedido(data) {
    if (typeof data.itens != 'undefined') {

        $('.gera-pedido').show();
        var html = "";
        if (data.itens != null) {
            $.each(data.itens, function (i, item) {
                var x = parseFloat(data.itens[i].vUnCom).toFixed(3).toString().split('.');
                if (x[1].length === 3) {
                    if (x[1].substring(2, 3) === '0') {
                        var doisDigi = number_format(data.itens[i].vUnCom, 2, ',', '.');
                    } else {
                        var doisDigi = number_format(data.itens[i].vUnCom, 3, ',', '.');
                    }
                } else {
                    var doisDigi = number_format(data.itens[i].vUnCom, 2, ',', '.');
                }
                html += "<tr class=\"valign-middle\" id=\"tr-item-" + data.itens[i].id_prod + "\">";
                html += "<td>" + data.itens[i].xProd + "<input type=\"hidden\" name=\"idProd[]\" value=\"" + data.itens[i].id_prod + "\"></td>";
                html += "<td class=\"text text-center\">" + data.itens[i].uCom + "</td>";
                html += "<td class=\"text text-center\">" + doisDigi + "<input type=\"hidden\" name=\"vUnCom[]\" id=\"vUnCom-" + data.itens[i].id_prod + "\" class=\"form-control money\" value=" + number_format(data.itens[i].vUnCom, 3, ',', '.') + " ></td>";
                html += "<td><input type=\"text\" class=\"form-control money item_qtde\" name=\"qtde[]\"  data-id=" + data.itens[i].id_prod + " size=\"12\" maxlength=\"12\" value=\"" + number_format(data.itens[i].qCom, 3, ',', '.') + "\"></td>";
                html += "<td><input type=\"text\" name=\"vProd[]\" id=\"vProd-" + data.itens[i].id_prod + "\" size=\"12\" class=\"vlrTl money form-control\" readonly value=\"" + number_format(data.itens[i].total, 3, ',', '.') + "\"> </td>";
                html += "</tr>";
            });
        } else {
            html = "<tr><td colspan='5' class='info text-center text-info'><i class='fa fa-info-circle'></i> Não há produtos para esse fornecedor.</td></tr>";
        }

        $("#tab-produtos tbody").html(html);
        $(".item_qtde").trigger('blur').trigger('mask.maskMoney');
    } else if (typeof data.msg != 'undefined') {
        if (data.status) {
            var titulo = 'Salvo';
            var type = 'success';
            $("#tab-produtos").addClass("hide");
            $('#form-pedido')[0].reset();
        } else {
            var titulo = 'Erro';
            var type = 'danger';
        }
        bootAlert(data.msg, titulo, function () {
            if (titulo == 'Salvo') {
                location.href = 'pedidos.php';
            }
        }, type);
    }
}

$(document).ready(function () {
    $('#regiao1').change(function () {
        var destino = $(this).data('for');
        $.post("../../methods.php", {method: "carregaProjetos", regiao: $(this).val()}, function (data) {
            $("#" + destino).html(data);
        });
    });

    // solicitacao -------------------------------------------------------------

    $('#projeto1,#filtra_tipo').change(function () {
        var tipo = $("#filtra_tipo").val();
        var projeto = $("#projeto1").val()
        var destino = $(this).data('for');
        $.post("confirmapedido.php", {method: "carregaFornecedor", projeto: projeto, tipo: tipo}, function (data) {
            $("#" + destino).html(data);
        });
    });

    $(".importacao").click(function () {
        var $this = $(this);
        if ($this.val() === 's') {
            $('#div_importar').fadeIn();
        } else if ($this.val() === 'n') {
            $('#div_importar').fadeOut();
        }
    });

    // calcula total
    $("body").on('blur', ".item_qtde", function () {
        var id_prod = $(this).data('id');

        var qtde = parseFloat($(this).val().replace('.', '').replace(',', '.'));
        var vUni = parseFloat($("#vUnCom-" + id_prod).val().replace('.', '').replace(',', '.')); //$("#vUnCom-" + id_prod).maskMoney('unmasked')[0];
        var valor = (qtde * vUni).toFixed(3);
        valor = number_format(valor, 2, ',', '.');
        $("#vProd-" + id_prod).val(valor);
        var tot = 0;
        $(".vlrTl").each(function () {
            if ($(this).val().replace('.', '').replace(',', '.') != "") {
                tot = parseFloat(tot) + parseFloat($(this).val().replace('.', '').replace(',', '.'));
            }
        });
        $("#somapedido").val("R$ " + number_format(tot.toFixed(2), 2, ',', '.'));
        $("#total").html("R$ " + number_format(tot.toFixed(2), 2, ',', '.'));
    });

    var optionsCadastroPed = {
        beforeSubmit: function (arr, $form, options) {
            $("#tab-produtos tbody").html("<tr><td class='text-center' colspan='5'><i class='fa fa-spinner fa-spin fa-5x'></i></td></tr>");
            $("#tab-produtos").removeClass("hide");
            return $($form).validationEngine('validate');// add validation engine
        },
        success: showSuccessPedido,
        dataType: 'json'
    };

    $("#form-confirmaPedido").ajaxForm(optionsCadastroPed);// add javaxForm
    $("#form-confirmaPedido").validationEngine();// add validation engine

    $("#form-pedido").ajaxForm(optionsCadastroPed);// add javaxForm
    $("#form-pedido").validationEngine();// add validation engine

    $("#vUnCom").maskMoney({thousands: '.', decimal: ',', affixesStay: false});

    $('body').on('focusin','.money',function () {
        $(".money").maskMoney({thousands: '.', decimal: ',', affixesStay: false, precision:3});
    });


    $('.gera-pedido').click(function () {
        $(this).hide();
    });

    $("#gera_excel").click(function () {
        var id_prestador = $("#id_prestador").val();
        var filtra_tipo = $("#filtra_tipo").val();
        window.location.href = "excel_pedido.php?id_prestador=" + id_prestador + "&filtra_tipo=" + filtra_tipo;
    });

    // fim solicitacao ---------------------------------------------------------

    // confirmacao -------------------------------------------------------------

    $('body').on('click', "#item_incluir", function () {
        $("#incluirItem").toggle('slow');
    });

    $("#form-confirmaPedido").ajaxForm({
        beforeSubmit: function () {
            $("#confirmarPedido").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
        },
        success: function (data) {
            $("#confirmarPedido").html(data);
        }
    });

    $("body").on('click', '.pedido-detalhes', function () {
        var pedido = $(this).data('id');
        $.post("confirmapedido.php", {id_pedido: $(this).data('id'), method: 'Detalhes'}, function (dados) {
            BootstrapDialog.show({
                size: 'size-wide',
                nl2br: false,
                title: 'DETALHES DO PEDIDO Nº ' + pedido,
                message: dados
            });

        });
    });

    $("body").on('click', '.pedido-cancelar', function () {
        var titulo = 'CANCELAMENTO DO PEDIDO !';
        var id = $(this).data('id');
        var buttons = [{
                label: 'Confirmar',
                cssClass: 'btn-danger',
                action: function (dialog) {
                    $.post('confirmapedido.php', {method: 'cancelapedido', id: id, motivo_cancelamento: $("#motivo_cancelamento").val()}, function (data) {

                        if (data.status) {
                            bootAlert('Pedido Cancelado.', titulo, function () {
                                $.each(BootstrapDialog.dialogs, function (id, dialog) {
                                    dialog.close();
                                });
                            }, 'success');
                            $('#tr-' + id).remove();
                        } else {
                            bootAlert('Erro ao cancelar pedido.', titulo, null, 'danger');
                        }
                    }, 'json');
                }
            }, {
                label: 'Cancelar',
                action: function (dialog) {
                    dialog.close();
                }
            }];

        var motivo = '<p><label>Deseja cancelar o pedido?</label></p>\n\
                    <textarea name="motivo_cancelamendo" id="motivo_cancelamento" class="form-control" placeholder="Informe o motivo...!" autofocus></textarea>';
        bootDialog(motivo, titulo, buttons, 'danger');
    });

    // fim confirmacao ---------------------------------------------------------

    // enviar pedido -----------------------------------------------------------
    var optionsEnviarPed = {
        //      success: showSuccessPedidoE,
        dataType: 'json'
    };

    $("#form-enviarPedido").ajaxForm(optionsEnviarPed);// add javaxForm
    $("#form-enviarPedido").validationEngine();// add validation engine

//    $("#form-confirmacao").ajaxForm({
//        beforeSubmit: showRequest,
//        success: function (data) {
//            $("#resp_form_inserir_item").html(data);
//            $("#descricao_item").val('');
//            $("#tb-itens").html('');
//            $("#endeforn").html('');
//            $("#fornecedor").html('');
//        },
//        resetForm: true 
//    });
//    


    // fim enviar pedido -------------------------------------------------------

    $("body").on('click', '.enviar-pedido', function () {
        var id = $(this).data('id');
        var email = $(this).data('email');
        var upa = $(this).data('upa')
        $.post("confirmapedido.php", {id_pedido: $(this).data('id'), email: $(this).data('email'), upa: $(this).data('upa'), method: 'Enviar'}, function (dados) {
//            BootstrapDialog.show({
//                size: 'size-wide',
//                nl2br: false,
//                title: 'Pedido enviado ao fornecedor.',
//                message: dados
//            });
            bootAlert(dados, 'Pedido enviado ao fornecedor.', function () {
                location.href = 'pedidos.php?aba=pedidosfinalizado';
            }, 'primary');
            $('#tr-' + id).remove();
            //location.href = 'pedidos.php?aba=pedidosfinalizado';

        });            //location.href = 'pedidos.php?aba=pedidosfinalizado';

    });

    $('body').on('click', '.sem_enviar', function () {
        var $this = $(this);
        bootConfirm('Deseja Confirmar sem enviar?', 'Atenção', function (status) {
            if (status) {
                $.post('confirmapedido.php', {method: 'sem_enviar', id_pedido: $this.data('id'), email: $this.data('email'), upa: $this.data('upa')}, function (data) {
                    if (data.status) {
                        bootAlert('Confirmado com Sucesso!','Confirmação',function(){location.href = 'pedidos.php?aba=pedidosfinalizado';},'success');
                        $this.closest('tr').remove();
                        
                    } else {
                        bootAlert('Erro ao confirmar!');
                        
                    }
                }, 'json');
            }
        }, 'warning');
    });

    $("body").on('click', '.pedido_reabrir', function () {
        var titulo = 'PEDIDO CANCELADO !';
        var titulo1 = 'PEDIDO REABERTO !';
        var id = $(this).data('id');
        var buttons = [{
                label: 'Confirmar',
                cssClass: 'btn-success',
                action: function (dialog) {
                    $.post("confirmapedido.php", {method: 'reabrirpedido', id: id, motivo_reabertura: $("#motivo_reabertura").val()}, function (data) {
                        if (data.status) {
                            bootAlert('Reabertura de Pedido.', titulo1, function () {
                                $.each(BootstrapDialog.dialogs, function (id, dialog) {
                                    dialog.close();
                                });
                            }, 'success');
                            $('#tr-' + id).remove();
                        } else {
                            bootAlert('Erro ao reabrir o pedido.', titulo, null, 'danger');
                        }
                    }, 'json');
                }
            }, {
                label: 'Cancelar', cssClass: 'btn-danger', action: function (dialog) {
                    dialog.close();
                }
            }];
        var motivo = '<p><label>Reabertura do Pedido!</label></p>\n\
                    <textarea name="motivo_reabertura" id="motivo_reabertura" class="form-control" placeholder="Informe motivo da Reabertura do Pedido!" autofocus></textarea>';
        bootDialog(motivo, titulo, buttons, 'danger');
    });

    /** impedir que o botão ENTER realize o SUBMIT **/
    $(document).keypress(function (e) {
        var keycode = (e.keyCode ? e.keyCode : e.which);
        if (keycode == '13') {
            return false;
        }
    });

    $("body").on("keypress", "#descricao_item", function () {
        var fornecedor = $('#xfornecedor').val();
        $.post('../produtos/methods_prod.php', {method: 'carregaItem', fornecedor: fornecedor}, function (data) {
            $("#descricao_item").autocomplete({
                source: data.prods,
                minLength: 2,
                change: function (event, ui) {
                    if (event.type == 'autocompletechange') {
                        var array_item = $("#descricao_item").val().split(' - ');
                        $("#item").val(array_item[0]);
                        $("#descricao_item").val();
                        $(".loading_item").html('');
                    }
                }
            });
        }, 'json');
    });

    $("body").on('click', "#incluirnopedido", function () {
        var id_prod = $("#item").val();
        var qtd_item = $("#qtd_item").val();
        var id_prestador = $('#xfornecedor').val();
        if (id_prod !== '') {
            $.post("confirmapedido.php", {id_prod: id_prod, qtd_item: qtd_item, method: 'itemIncluir', id_prestador: id_prestador}, function (dados) {
                $("#tab_inclui_produtos tbody").append(dados);
                $("#item").val('');
                $("#descricao_item").val('');
                $("#qtd_item").val('');
                $(".item_qtde").trigger('blur');
            });
        }
    });

    $('#form_filtro_finalizados').ajaxForm({
        success: function (data) {
            $('#ped_finalizados').html(data);
        }
    });

// conferencia
    $('body').on('click', ".conferencia", function () {
        var id = $(this).data('id');
        $.post('pedidos_methods.php', {method: 'conferencia', id: id}, function (data) {
            bootShow(data, 'Conferência');
        });
    });
// conferencia





});

function confirmaOK(id) {
    var titulo = 'CONFIRMAÇÃO DO PEDIDO';

//    var id = $('.btn-confirmaOk').data('id');

    bootConfirm('Deseja Confirmar Pedido?', titulo, function (status) {
        if (status) {
            if ($('#form-confirmacao').length > 0) { // se existe o formulário (do modal)
                $('#form-confirmacao').ajaxSubmit({
                    dataType: 'json',
                    success: function (data) {
                        resposta_confirmaOK(data, id);
                    }
                });
            } else { // se não existe o formulario
                $.post('confirmapedido.php', {method: 'confirmarpedidoOk', id: id}, function (data) {
                    resposta_confirmaOK(data, id);
                }, 'json');
            }
        }
    }, 'info');
}

function resposta_confirmaOK(data, id) {
    var titulo = 'CONFIRMAÇÃO DO PEDIDO';
    if (data.status) {
        bootAlert('Pedido Confirmado.', titulo, function () {
            $.each(BootstrapDialog.dialogs, function (ids, dialog) {
                dialog.close();
                location.href = 'pedidos.php?aba=enviarpedidos';
            });
        }, 'success');
        $('#tr-' + id).remove();

    } else {
        bootAlert('Erro ao confirmar o pedido.', titulo, null, 'danger');
    }
}