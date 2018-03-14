var sorteados = [];
var valorMaximo = 10000;

$(document).ready(function () {

    window.location.href = '#foo';

    $('body').on('focus', '.money', function () {
        $('.money').maskMoney({allowNegative: true, thousands: '.', decimal: ',', affixesStay: false, allowZero: true});
    });

    $('.selecionardata').datepicker({
        dateFormat: 'dd/md/yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '2005:c+1'
    });

    $('body').on('keyup', ".conta", function () {
        var $this = $(this);
        var codigo = $(this).val();
        var projeto = $('#projeto').val();
        $.post('classificacao_controle.php', {method: 'retornaConta', codigo: codigo, projeto: projeto}, function (data) {
            $this.autocomplete({
                source: data.contas,
                change: function (event, ui) {
                    if (event.type == 'autocompletechange') {
                        var array_contas = $this.val().split(' | ');
                        $("#" + $this.data('conta')).val(array_contas[2]);
                        $this.val(array_contas[1]);
                        $("#" + $this.data('conta_id')).val(array_contas[3]);
                        $(".loading").html('');
                        $("#" + $this.data('historico')).val(array_contas[4]);
                    }
                }
            });
        }, 'json');
    });

    // ABA Concilicao ----------------------------------------------------------

    // realiza consulta dos lancamentos
    $("#form_consulta_conciliacao").ajaxForm({
        beforeSubmit: form_consulta_beforeSubmit,
        success: form_consulta_success
    });

    /*
     * abre modal para cadastro de novos lancamentos
     */
    $('body').on('click', '.add_lancamento', function () {
        var id_projeto = parseInt($('#projeto').val());
        if (id_projeto <= 0) {

            bootAlert('Selecione um Projeto', null, null, 'danger');

        } else {

            var formattedDate = new Date();
            var d = formattedDate.getDate();
            var m = formattedDate.getMonth();
            m += 1;  // JavaScript months are 0-11
            if (m < 10) {
                m = '0' + m;
            }
            var y = formattedDate.getFullYear();

            var id_novo = criarUnico();

            var html =
                    $('<form>', {class: 'form-horizontal', id: 'form_lancamento', method: 'post', action: 'classificacao_controle.php'}).append(
                    $("<div>", {class: "panel panel-default panel-lancamento-data"}).append(
                    $("<div>", {class: "panel-heading panel-lancamento-head", 'data-data': id_novo}).append(
                    $('<div>', {class: 'row'}).append(
                    $("<div>", {class: "col-sm-3"}).append(
                    $("<input>", {value: d + "/" + m + "/" + y, name: "data_lancamento", readonly: true, class: "form-control input-sm data_lancamento text-center", style: " background-color: #FFF; cursor: pointer;"})//,
                    ),
                    $("<div>", {class: "col-sm-9"}).append(
                    $("<input>", {name: "historico_lancamento", class: "form-control input-sm", placeholder: "Hist�rico do Lancamento"})
                    )
                    )
                    ),
                    $("<div>", {class: "panel-body " + id_novo}).append(
                    $("<table>", {class: "table table-stripe table-condensed text-sm valign-middle", id: "table_" + id_novo}).append(
                    $("<thead>").append(
                    $("<tr>").append(
                    $("<th>", {style: "width: 20%;", html: "Conta"}),
                    $("<th>", {html: "Descri��o"}),
                    $("<th>", {style: "width: 30%;", html: "Tipo"}),
                    $("<th>", {style: "width: 5%;", html: "&emsp;"})
                    )
                    ),
                    $("<tbody>").append(),
                    $("<tfoot>").append(
                    $("<tr>").append(
                    $("<th>", {class: "text-right"}).append(
                    $("<button>", {type: "button", class: "btn btn-block btn-sm btn-success add_lancamento_item", 'data-id': id_novo}).append(
                    $("<i>", {class: "fa fa-plus"}), " Incluir"
                    )
                    ),
                    $("<th>", {class: "text-right"}).append(
                    $("<span>", {style: "display: inline-block; padding-top:5px;", html: "Saldo:"})
                    ),
                    $("<th>").append(
                    $("<div>", {class: "input-group"}).append(
                    $("<div>", {class: "input-group-addon text-sm", html: "R$"}),
                    $("<input>", {class: "form-control input-sm money saldo", id: "saldo_lan", value: "0,00", readonly: true})
                    )
                    )
                    )
                    )
                    )
                    )
                    )
                    );

            function daysInMonth(month, year) {
                var m = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
                if (month != 2)
                    return m[month];
                if (year % 4 != 0)
                    return m[1];
                if (year % 100 == 0 && year % 400 != 0)
                    return m[1];

                return m[1] + 1;
            }

            var mes = parseInt($('#mes_lote').val()) - 1;
            var ano = $('#ano_lote').val();

            html.find('.data_lancamento').datepicker({dateFormat: 'dd/mm/yy'});
            $.post('form_conciliacao.php', {method: 'getTipos'}, function (data) {
                html.find('select').html(data);
            });

            var button = [{
                    label: 'Cancelar',
                    action: function (dialog) {
                        dialog.close();
                    }
                }, {
                    label: '<i class="fa fa-floppy-o"></i> Salvar',
                    cssClass: 'btn-success',
                    action: function (dialog) {
                        $('#form_lancamento').ajaxSubmit({
                            dataType: 'json',
                            data: {method: 'salvar_conciliacao', id_projeto: id_projeto},
                            success: function (data) {
                                bootAlert(data.msg, 'Salvando Lan�amento', null, data.status);
                            }
                        });
                        dialog.close();
                    }
                }];
            bootForm(html, 'Lancamentos', button, 'success');
        }
    });

    /*
     * add novas linhas de lancamento
     */

    $("body").on('click', '.add_lancamento_item', function () {
        var $this = $(this);
        var id = $this.data('id');
        var idLinha = criarUnico();

        $("#table_" + id + " tbody").append(
                $('<tr>', {class: 'danger tr' + idLinha}).append(
                $('<td>').append(
                $('<input>', {type: "hidden", class: "form-control input-sm", name: "id_conta[]", id: "id_conta_a" + idLinha}),
                $('<input>', {type: "text", class: "form-control input-sm conta validate[required]", name: "conta[]", 'data-conta': "desc_a" + idLinha, 'data-conta_id': "id_conta_a" + idLinha, 'data-historico': "historico_item_" + idLinha})
                ),
                $('<td>').append(
                $('<input>', {type: "text", class: "form-control input-sm validate[required]", name: "descricao[]", id: "desc_a" + idLinha, readonly: ''})
                ),
                $('<td>').append(
                $('<select>', {name: "tipo[]", class: "validate[required,custom[select]] lancamento_item_tipo form-control input-sm tipo_" + idLinha, 'data-id': idLinha}).append(
                $('<option>', {value: '2'}).text('D�bito'),
                $('<option>', {value: '1'}).text('Cr�dito')
                )
                ),
                $('<td>', {rowspan: 2, class: "text-center"}).append(
                $('<button>', {type: "button", class: "btn btn-xs btn-danger excluir_lancamento_item", 'data-id': idLinha, 'data-data': id, title: "Excluir Item do Lan�amento"}).append(
                $('<i>', {class: "fa fa-trash"})
                )
                )
                ),
                $('<tr>', {class: 'no-border danger tr' + idLinha}).append(
                $('<td>', {class: 'no-border', colspan: '2'}).append(
                $('<input>', {type: "text", class: "form-control input-sm validate[required]", id: "historico_item_" + idLinha, name: "historico_item[]", placeholder: 'Hist�rico do Item'})
                ),
                $('<td>').append(
                $('<div>', {class: "input-group"}).append(
                $('<div>', {class: "input-group-addon text-sm"}).text('R$'),
                $('<input>', {type: "text", class: "form-control text-right input-sm money lancamento_item_valor validate[required]", name: "valor[]", value: "0,00", 'data-id': idLinha})
                )
                )
                ),
                $('<tr>', {class: "tr" + idLinha}).append($('<td>', {colspan: '5', class: 'no-border'}))
                );
        $(".btnSalvar").removeClass('hidden');
    });

    /*
     * calcula os totais
     */
    $('body').on('change', '.lancamento_item_valor, .lancamento_item_tipo, #saldo_lan', function () {

        var vlrCredor = 0, vlrDevedor = 0;

        $(".lancamento_item_valor").each(function () {
            var $this = $(this);
            var id = $(this).data('id');
            var tipo = $('.lancamento_item_tipo.tipo_' + id).val();
            if (tipo == 1) {
                vlrCredor = parseFloat(vlrCredor) + parseFloat($this.maskMoney('unmasked')[0]);
            } else if (tipo == 2) {
                vlrDevedor = parseFloat(vlrDevedor) + parseFloat($this.maskMoney('unmasked')[0]);
            }
        });

        var diferenca = vlrCredor.toFixed(2) - vlrDevedor.toFixed(2);
        var valor = number_format(diferenca, 2, ',', '.');
        $('#saldo_lan').val(valor);
    });

    /*
     * muda a cor do item dependendo do tipo (debito/credito)
     */
    $('body').on('change', '.lancamento_item_tipo', function () {
        if ($(this).val() == 1)
            $(this).parent().parent().removeClass('danger').addClass('success').next().removeClass('danger').addClass('success');
        else
            $(this).parent().parent().removeClass('success').addClass('danger').next().removeClass('success').addClass('danger');
    });

    $("body").on('click', '.btn_conferir', function () {
        var $this = $(this);
        var id = $this.data('id');
        $.post('', {method: 'confere', id: id}, function (data) {
            bootAlert(data.msg);
        }, 'json');
//        $("#form_post").append($('<input>', {type: 'hidden', name: 'id_lancamento', value: id})).prop('action', 'form_conciliacao.php').submit();
    });

//    $("body").on('click', '.btnSalvar', function () {
//        var id_lote = $("#id_lote").val();
//        $("#form_conciliacao").ajaxSubmit({
//            dataType: 'json',
//            data: {method: 'salvar_conciliacao'},
//            beforeSubmit: form_conciliacao_beforeSubmit,
//            success: form_conciliacao_success
//        });
//    });

    $("body").on('click', '.btnFinalizar', function () {
        bootConfirm('<p>O Lote do Lan�amento ser� Finalizado !<strong>Comfirma ...?</strong></p><p>Depois de finalizar somente o responsav�l autorizado pode reverter.', 'ATEN��O', function (resp) {
            if (resp) {
                $("#form_conciliacao").ajaxSubmit({
                    dataType: 'json',
                    data: {method: 'salvar_conciliacao', finalizar: true},
                    beforeSubmit: form_conciliacao_beforeSubmit,
                    success: form_conciliacao_success
                });
            }
        }, 'warning');
    });

    $("body").on('click', '.exclui_lancamento', function () {
        var $this = $(this);
        var id = parseInt($this.data('id'));
        if (id > 0) {
            bootConfirm('Deseja realmente Excluir?', 'Excluindo...', function (confirmacao) {
                if (confirmacao) {
                    $.post('classificacao_controle.php', {method: 'exclui_lancamento', id: id}, function (data) {
                        bootAlert(data.msg, 'Excluindo...', function () {
                            if (data.status === 'success') {
//                                $this.closest('.panel-default').remove();
                                $('.tr' + id).remove();
                                location.reload();
                            }
                        }, data.status);
                    }, 'json');
                }
            }, 'danger');
        } else {
//            $this.closest('.panel-default').remove();
            $this.closest('tr').remove();
            location.reload();
        }

    });

    $("body").on('click', '.excluir_lancamento_item', function () {
        var $this = $(this);
        var id = $this.data('id');
        if (id > 0) {
            bootConfirm('Deseja realmente Excluir?', 'Excluindo...', function (confirmacao) {
                if (confirmacao) {
                    $.post('classificacao_controle.php', {method: 'excluir_lancamento_item', id: id}, function (data) {
                        bootAlert(data.msg, 'Excluindo...', function () {
//                            $this.closest('tr').remove();
                            $('.tr' + id).remove();
                            $("#saldo_lan").trigger('change');
                        }, data.status);
                    }, 'json');
                }
            }, 'danger');
        } else {
            $this.closest('tr').remove();
            $("#saldo_lan").trigger('change');
        }
    });

    // ABA Relatorio
    $("#form_consulta_finalizados").ajaxForm({
        beforeSubmit: form_consulta_beforeSubmit,
        success: form_finalizados_success
    });

    $("body").on('click', ".btn_visualizar", function () {
        var $this = $(this);
        var id = $this.data('id');
        var nome_projeto = $this.closest('tr').find('.projeto').text();
        var lote = $this.closest('tr').find('.lote').text();
        $.post('classificacao_controle.php', {method: 'ver_finalizado', id: id}, function (dados) {
            bootShow(dados, '<strong>Concilia&ccedil;&atilde;o</strong> <i class="fa fa-angle-double-right"></i> Lote ' + lote + ' - ' + nome_projeto);
        });
    });

    $("body").on('click', ".btn_reabrir", function () {
        var $this = $(this);
        var id = $this.data('id');
        $.post('classificacao_controle.php', {method: 'reabrir', id: id}, function (data) {
            bootAlert(data.msg, 'Reabrindo...', null, data.status);
            if (data.status === 'success') {
                $("#consultar_conciliacao, #consultar_finalizados").trigger('click');
            }
        }, 'json');
    });

    // -------------------------------------------------------------------------

    $('body').on('click', '.editar_lancamento_item', function () {
        var $this = $(this);
        var id_novo = $this.data('id');

        $.post('classificacao_controle.php', {method:'consolta_lancamento',id:id_novo}, function (data) {
            var html =
                    $('<form>', {class: 'form-horizontal', id: 'form_lancamento', method: 'post', action: 'classificacao_controle.php'}).append(
                        $("<div>", {class: "panel panel-default panel-lancamento-data"}).append(
                            $("<div>", {class: "panel-heading panel-lancamento-head", 'data-data': id_novo}).append(
                                $('<div>', {class: 'row'}).append(
                                    $("<div>", {class: "col-sm-3"}).append(
                                        $("<input>", {value: data.data_lancamento, name: "data_lancamento", readonly: true, class: "form-control input-sm data_lancamento text-center", style: " background-color: #FFF; cursor: pointer;"}),
                                        $("<input>", {value: data.id_lancamento, name: "id_lancamento", type:'hidden'})
                                    ),
                                    $("<div>", {class: "col-sm-9"}).append(
                                        $("<input>", {name: "historico_lancamento", class: "form-control input-sm", placeholder: "Hist�rico do Lancamento", value: data.historico})
                                    )
                                )
                            ),
                            $("<div>", {class: "panel-body " + id_novo}).append(
                                $("<table>", {class: "table table-stripe table-condensed text-sm valign-middle", id: "table_" + id_novo}).append(
                                    $("<thead>").append(
                                        $("<tr>").append(
                                            $("<th>", {style: "width: 20%;", html: "Conta"}),
                                            $("<th>", {html: "Descri��o"}),
                                            $("<th>", {style: "width: 30%;", html: "Tipo"}),
                                            $("<th>", {style: "width: 5%;", html: "&emsp;"})
                                        )
                                    ),
                                    $("<tbody>").append(
                                            
                                    ),
                                    $("<tfoot>").append(
                                        $("<tr>").append(
                                            $("<th>", {class: "text-right"}).append(),
                                            $("<th>", {class: "text-right"}).append(
                                                $("<span>", {style: "display: inline-block; padding-top:5px;", html: "Saldo:"})
                                            ),
                                            $("<th>").append(
                                                $("<div>", {class: "input-group"}).append(
                                                $("<div>", {class: "input-group-addon text-sm", html: "R$"}),
                                                    $("<input>", {class: "form-control input-sm money saldo", id: "saldo_lan", value:'0,00', readonly: true})
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    );

            $.each(data.itens, function (i, v) {
                var css_class = (v.tipo == 2)?'danger':'success';
                
                html.find('tbody').append(
                    $('<tr>', {class: css_class+' tr' }).append(
                        $('<td>').append(
                            $('<input>', {type: "hidden", class: "form-control input-sm", name: "id_conta[]", id: "id_conta_a"+i, value: v.id_conta }),
                            $('<input>', {type: "hidden", class: "form-control input-sm", name: "id_lancamento_itens[]", id: "id_lancamento_itens_a", value: v.id_lancamento_itens }),
                            $('<input>', {type: "text", class: "form-control input-sm conta validate[required]", name: "conta[]", 'data-conta': "desc_a"+i , 'data-conta_id': "id_conta_a"+i , 'data-historico': "historico_item_"+i, value: v.acesso })
                        ),
                        $('<td>').append(
                            $('<input>', {type: "text", class: "form-control input-sm validate[required]", name: "descricao[]", id: "desc_a"+i , readonly: '', value: v.descricao})
                        ),
                        $('<td>').append(
                            $('<select>', {name: "tipo[]", class: "validate[required,custom[select]] lancamento_item_tipo form-control input-sm tipo_"+i , 'data-id': i}).append(
                                $('<option>', {value: '2'}).text('D�bito'),
                                $('<option>', {value: '1'}).text('Cr�dito')
                            ).val(v.tipo)
                        )
                    ),
                    $('<tr>', {class: 'no-border '+ css_class+' tr' }).append(
                        $('<td>', {class: 'no-border', colspan: '2'}).append(
                            $('<input>', {type: "text", class: "form-control input-sm validate[required]", id: "historico_item_"+i , name: "historico_item[]", placeholder: 'Hist�rico do Item', value: v.historico_item})
                        ),
                        $('<td>').append(
                            $('<div>', {class: "input-group"}).append(
                                $('<div>', {class: "input-group-addon text-sm"}).text('R$'),
                                $('<input>', {type: "text", class: "form-control text-right input-sm money lancamento_item_valor validate[required]", name: "valor[]", value: v.valor, 'data-id': i})
                            )
                        )
                    ),
                    $('<tr>', {class: "tr" }).append($('<td>', {colspan: '5', class: 'no-border'}))
                );
            });

            html.find('.money').trigger('change');
            
            var id_projeto = parseInt($('#projeto').val());
            var button = [{
                    label: 'Cancelar',
                    action: function (dialog) {
                        dialog.close();
                    }
                }, {
                    label: '<i class="fa fa-floppy-o"></i> Salvar',
                    cssClass: 'btn-success',
                    action: function (dialog) {
                        $('#form_lancamento').ajaxSubmit({
                            dataType: 'json',
                            data: {method: 'salvar_conciliacao', id_projeto: id_projeto},
                            success: function (data) {
                                bootAlert(data.msg, 'Salvando Lan�amento', null, data.status);
                            }
                        });
                        dialog.close();
                    }
                }];
            bootForm(html, 'Lancamentos', button, 'info');
            
        }, 'json');

    });

    $('#btn_editar').click(function () {
        if ($('#projeto').val() < 0) {
            bootAlert('Selecione um projeto!', null, null, 'danger');
        } else {
            var xxx = $('#form_consulta_conciliacao').serialize();
            window.open('edicao_lancamento.php?' + xxx, '_blank');
        }
    });

    /*
     * para funcionar o hidden / show
     */

    $('body').on('click', ".inf", function () {
        var modo = $(this).attr("href");
        if ($(modo).is(":visible")) {
            $(modo).hide();
            return false;
        } else {
            $(".oculto").hide("slow");
            $(modo).fadeToggle("fast");
            return false;
        }
    });



});

function showRequest(formData, jqForm, options) {
    var valid = jqForm.validationEngine('validate');
    if (valid == true) {
        return true;
    } else {
        return false;
    }
}

function subtrairLancamento() {
    var vlrCredor = 0, vlrDevedor = 0;
    $(".subtrair").each(function () {
        if ($(this).data('id') == 2) {
            vlrCredor = parseFloat(vlrCredor) + parseFloat($(this).maskMoney('unmasked')[0]);
        } else if ($(this).data('id') == 1) {
            vlrDevedor = parseFloat(vlrDevedor) + parseFloat($(this).maskMoney('unmasked')[0]);
        }
    });
    $("#somacredora").maskMoney('mask', vlrCredor).trigger('focus');
    $("#somadevedora").maskMoney('mask', vlrDevedor).trigger('focus');
    $("#diferenca").maskMoney('mask', (vlrCredor - vlrDevedor)).trigger('focus');
}

function salvarLancamento(idForm) {
    $(idForm).ajaxSubmit({
        success: function (data) {
            $("#resp").html(data);
        },
        resetForm: true
    });
}

// -----------------------------------------------------------------------------

function form_consulta_beforeSubmit(formData, jqForm, options) {
    $('.btnSalvar').prop('disabled', true);
    $("#tabela_conciliacao").html('<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>');
    return jqForm.validationEngine('validate');
}

function form_consulta_success(data, status, xhr, $form) {
//    $('.btnSalvar').prop('disabled',false);
    $("#dados_conciliacao").html(data);
    if (data.length > 0) {
        $("#tabela_conciliacao").parent().removeClass('hidden');
        $("#tabela_conciliacao").html(data);
        $(".oculto").hide();
    } else {
        $("#tabela_conciliacao").parent().addClass('hidden');
    }
}

// -----------------------------------------------------------------------------

function form_conciliacao_beforeSubmit(formData, jqForm, options) {
    var retorno = true;

    retorno = jqForm.validationEngine('validate');

    // verifica se o saldo est� zerado
    $('.saldo').each(function () {
        var saldo = $(this).maskMoney('unmasked')[0];
        if (saldo !== 0.0) {
            bootAlert('O saldo tem que ser igual a <strong>R$ 0,00</strong>!', '<i class="fa fa-exclamation-triangle"></i> Aten&ccedil;&atilde;o!', null, 'danger');
            retorno = false;
            return false;
        }

    });

    // verifica se as tabelas dos lancamentos est�o vaizas...
    $('.modal tbody').each(function () {
        if ($(this).find('tr').length === 0) {
            bootAlert('O Lan�amento n�o pode ser vazio!', '<i class="fa fa-exclamation-triangle"></i> Aten&ccedil;&atilde;o!', null, 'danger');
            retorno = false;
            return false;
        }
    });
    return retorno;
}

function form_conciliacao_success(data, status, xhr, $form) {
    bootAlert(data.msg, 'Salvar...', function () {
        if (data.status == 'success') {
            $.each(BootstrapDialog.dialogs, function (id, dialog) {
                //$('body').append($("<form>").append($('<input>',{ type:'hidden', name:'id_lote', value:id_lote })).prop('action', 'form_conciliacao.php'));
                location.reload();
            });
            if (typeof data.id !== 'undefined') {
                $("#tr" + data.id).remove();
            }
//            $.post('classificacao_controle.php', {method: 'consultar_finalizados'}, function (data) {
//                $("#tabela_finalizados tbody").html(data);
//            });
        }

    }, data.status);

}

// -----------------------------------------------------------------------------

function form_finalizados_success(data, status, xhr, $form) {
//    $("#dados_conciliacao").html(data);
    if (data.length > 0) {
        $("#tabela_finalizados").parent().removeClass('hidden');
        $("#tabela_finalizados").html(data);
    } else {
        $("#tabela_finalizados").parent().addClass('hidden');
    }
    //window.location.href = 'classificacao.php';
}

// -----------------------------------------------------------------------------

function importar(lote, nrlote, nome_projeto, botao) {
    $.post('importar_lancamento.php', {nome_projeto: nome_projeto, lote: lote, nrlote: nrlote}, function (data) {
        bootAlert(data.msg, 'LAN�AMENTOS - IMPORTA��O', null, data.status);
        if (data.status == 'success') {
            botao.removeClass('btn-info').addClass('btn-default').prop('disabled', true);
        }
    }, 'json');
}

function criarUnico() {
    if (sorteados.length == valorMaximo) {
        if (confirm('J� n�o h� mais! Quer recome�ar?'))
            sorteados = [];
        else
            return;
    }
    var sugestao = Math.ceil(Math.random() * valorMaximo); // Escolher um numero ao acaso
    while (sorteados.indexOf(sugestao) >= 0) {  // Enquanto o numero j� existir, escolher outro
        sugestao = Math.ceil(Math.random() * valorMaximo);
    }
    sorteados.push(sugestao); // adicionar este numero � array de numeros sorteados para futura refer�ncia
    return sugestao; // devolver o numero �nico
}

function empty(mixed_var) {
    //  discuss at: http://phpjs.org/functions/empty/
    // original by: Philippe Baumann
    //    input by: Onno Marsman
    //    input by: LH
    //    input by: Stoyan Kyosev (http://www.svest.org/)
    // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: Onno Marsman
    // improved by: Francesco
    // improved by: Marc Jansen
    // improved by: Rafal Kukawski
    //   example 1: empty(null);
    //   returns 1: true
    //   example 2: empty(undefined);
    //   returns 2: true
    //   example 3: empty([]);
    //   returns 3: true
    //   example 4: empty({});
    //   returns 4: true
    //   example 5: empty({'aFunc' : function () { alert('humpty'); } });
    //   returns 5: false

    var undef, key, i, len;
    var emptyValues = [undef, null, false, 0, '', '0'];

    for (i = 0, len = emptyValues.length; i < len; i++) {
        if (mixed_var === emptyValues[i]) {
            return true;
        }
    }

    if (typeof mixed_var === 'object') {
        for (key in mixed_var) {
            // TODO: should we check for own properties only?
            //if (mixed_var.hasOwnProperty(key)) {
            return false;
            //}
        }
        return true;
    }

    return false;
}

function ocultarDiv(el) {
    var display = document.getElementById(el).style.display;
    if (display == "none")
        document.getElementById(el).style.display = 'block';
    else
        document.getElementById(el).style.display = 'none';
}
    