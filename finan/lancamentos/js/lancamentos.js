var sorteados = [];
var valorMaximo = 10000;

$(document).ready(function () {

    $("#regiao1, #regiao2, #regiao3, #regiao4").change(function () {
        var destino = $(this).data('for');
        $.post("../methods.php", {method: "carregaProjetos", regiao: $(this).val()}, function (data) {
            $("#" + destino).html(data);
        });
    });

    $('#projeto1,#projeto2,#projeto3,#projeto3').change(function () {
        var destino = $(this).data('for');
        $.post("../methods.php", {method: "carregaPrestadores", projeto: $(this).val()}, function (data) {
            $("#" + destino).html(data);
        });
    });

    $('body').on('focus', '.money', function () {
        $('.money').maskMoney({allowNegative: true, thousands: '.', decimal: ',', affixesStay: false, allowZero: true});
    });

    // ABA Lote ----------------------------------------------------------------
    $('#form_lote').ajaxForm({
        dataType: 'json',
        beforeSubmit: form_lote_beforeSubmit,
        success: form_lote_success
    });

    // ABA Lancamento ----------------------------------------------------------
    $('#form_lancamento').ajaxForm({
        success: form_lancamento_success
    });

    $('.selecionardata').datepicker({
        dateFormat: 'dd/md/yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '2005:c+1'
    });

    $('body').on('click', ".tipo_lancamento", function () {
        $(".tipo_lancamento").toggle;
    });

    $('body').on('click', "#multi", function () {
        $("#div_multiplos").toggle('slow');
        $("#div_simples").hide();
    });

    $('body').on('click', "#simpli", function () {
        $("#div_simples").toggle('slow');
        $("#div_multiplos").hide();
    });

    $('body').on('click', '#btn_save_multiplos', function () {
        if ($("#diferenca").maskMoney('unmasked')[0] != 0) {
            bootAlert('Lan�amentos Mult�plos com diferen�a!', 'ATEN��O!', null, 'danger');
        } else {
            salvarLancamento("#form_lancamento_multiplos");
            //        bootAlert('Lan�amentos Mult�plos Ok!','SALVO!',null,'success');
            $("#tbl_multiplos").html('');
        }
    });

    $('body').on('click', '#btn_save_simples', function () {
        salvarLancamento("#form_lancamento_simples");
    });

    $('body').on('click', ".excluir", function () {
        var $this = $(this);
        var titulo = 'CANCELAMENTO';
        var lote = $(this).data('lote');
        var nprojeto = $this.data('nome_projeto');
        var nrlote = $(this).data('nrlote');
        var projeto = $(this).data('nrprojeto');
        var buttons = [{
                label: 'Confirmar',
                cssClass: 'btn-danger',
                action: function (dialog) {
                    $.post('lancamento_controle.php', {method: 'cancelarlote', projeto: projeto, nrlote: nrlote}, function (data) {
                        if (data.status) {
                            bootAlert('LOTE ' + lote + ' - ' + nprojeto + ' CANCELADO.', titulo, function () {
                                $.each(BootstrapDialog.dialogs, function (nrlote, dialog) {
                                    dialog.close();
                                });
                            }, 'success');
                            $this.closest('tr').remove();
                        } else {
                            bootAlert('ERRO AO CANCELAR O LOTE ' + lote + ' ' + nprojeto, titulo, null, 'danger');
                        }
                    }, 'json');
                }
            }, {
                label: 'Cancelar',
                action: function (dialog) {
                    dialog.close();
                }
            }];
        bootDialog('CONFIRMA O CANCELAMENTO DO LOTE ' + lote + ' ' + nprojeto, titulo, buttons, 'danger');
    });

    $('body').on('click', "#incluir_conta", function () {

        if ($("#form_lancamento_multiplos").validationEngine('validate')) {
            var vlrLancado = $("#valor_m").maskMoney('unmasked')[0];
            var somacredora = $("#somacredora").maskMoney('unmasked')[0];
            var somadevedora = $("#somadevedora").maskMoney('unmasked')[0];
            var tipo;
            if ($("input[name=conta_tipo]:checked").val() == "1") {
                somadevedora += vlrLancado;
                tipo = 'Devedora';
            } else if ($("input[name=conta_tipo]:checked").val() == "2") {
                somacredora += vlrLancado;
                tipo = 'Credora';
            }

            $("#tbl_multiplos").removeClass('hidden');
            $("#tbl_multiplos tbody").append("<tr>\n\
                <td>" + $("#cod_conta").val() + geraInputHidden('tcod_conta[]', $("#cod_conta").val()) + geraInputHidden('tid_conta[]', $("#contam_id").val()) + "</td>\n\
                <td>" + $("#codconta").val() + geraInputHidden('tcodconta[]', $("#codconta").val()) + geraInputHidden('tdocumento[]', $("#documentom").val()) + "</td>\n\
                <td>" + $("#valor_m").val() + geraInputHidden('tvalor_m[]', $("#valor_m").val(), $("input[name=conta_tipo]:checked").val(), 'subtrair') + geraInputHidden('thistorico[]', $("#historicom").val()) + "</td>\n\
                <td>" + tipo + geraInputHidden('tconta_tipo[]', $("input[name=conta_tipo]:checked").val()) + "</td>\n\
                <td><button type='button' id='cancelar_linha' class='btn btn-danger btn-xs cancela'><i class='fa fa-times'></i></buttom></td>\n\
            </tr>");

            subtrairLancamento();
        }
        $("#cod_conta").val('');
        $("#codconta").val('');
        $("#valor_m").val('');
        $("#documentom").val('');
        $("#historicom").val('');
    });

    $('body').on('click', '.cancela', function () {
        $(this).closest('tr').remove();
        subtrairLancamento();
    });

    $('body').on('clear', '.limpaInput', function () {
        $(".cod-conta").val('');
    });

    $('body').on('keyup', "#s_devedora, #s_credora, #cod_conta, .conta", function () {
        var $this = $(this);
        var codigo = $(this).val();
        var projeto = $('#projetos').val();
        $.post('lancamento_controle.php', {method: 'retornaConta', codigo: codigo, projeto: projeto}, function (data) {
            $this.autocomplete({
                source: data.contas,
                change: function (event, ui) {
                    if (event.type == 'autocompletechange') {
                        var array_contas = $this.val().split(' - ');
                        $("#" + $this.data('conta')).val(array_contas[2]);
                        $this.val(array_contas[1]);
                        $("#" + $this.data('conta_id')).val(array_contas[4]);
                        $(".loading").html('');
                        $("#" + $this.data('historico')).val(array_contas[3]);
                    }
                }
            });
        }, 'json');
    });

    // ABA Concilicao ----------------------------------------------------------
    $("#form_consulta_conciliacao").ajaxForm({
        beforeSubmit: form_consulta_beforeSubmit,
        success: form_consulta_success
    });

    $("body").on('click', '.btn_verificar', function () {
        var $this = $(this);
        var id = $this.data('id');
        $("#form_post").append($('<input>', {type: 'hidden', name: 'id_lote', value: id})).prop('action', 'form_conciliacao.php').submit();
    });

    $("body").on('click', '.btnSalvar', function () {
        var id_lote = $("#id_lote").val();
//        console.log(id_lote);
        $("#form_conciliacao").ajaxSubmit({
            dataType: 'json',
            data: {method: 'salvar_conciliacao'},
            beforeSubmit: form_conciliacao_beforeSubmit,
            success: form_conciliacao_success
        });
    });

    $("body").on('click', '.btnFinalizar', function () {
        bootConfirm('<p>Deseja realmente <strong>FINALIZAR</strong> a Concilia��o do Lote?</p><p>Ap�s filanizar apenas o contador poder� realizar altera��es na concilia��o.', 'Finalizado...', function (resp) {
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

    $('body').on('click', '.panel-lancamento-head .toogle', function () {
        if ($(this).hasClass('fa-arrow-circle-o-up')) {
            $(this).addClass("fa-arrow-circle-o-down").removeClass("fa-arrow-circle-o-up");
        } else if ($(this).hasClass('fa-arrow-circle-o-down')) {
            $(this).addClass("fa-arrow-circle-o-up").removeClass("fa-arrow-circle-o-down");
        }
        $('.' + $(this).data('data')).toggle();
    });


    $('body').on('change', '.lancamento_item_valor, .lancamento_item_tipo, .saldo', function () {
        var vlrCredor = 0, vlrDevedor = 0;
        var id = $(this).data('id');
//        console.log(id);
        $(".lancamento_item_valor").each(function () {
            var $this = $(this);
            if ($this.data('id') == id) {
                var tipo = $this.closest('tr').find('.lancamento_item_tipo').val();
                if (tipo == 1) {
                    vlrCredor = parseFloat(vlrCredor) + parseFloat($this.maskMoney('unmasked')[0]);
                } else if (tipo == 2) {
                    vlrDevedor = parseFloat(vlrDevedor) + parseFloat($this.maskMoney('unmasked')[0]);
                }
            }
        });
        $(".lancamento_item_valor_label").each(function () {
            var $this = $(this);
            if ($this.data('id') == id) {
                var tipo = $this.closest('tr').find('.lancamento_item_tipo_label').data('tipo');
//                console.log($this.data('valor'));
                if (tipo == 1) {
                    vlrCredor = parseFloat(vlrCredor) + parseFloat($this.data('valor'));
                } else if (tipo == 2) {
                    vlrDevedor = parseFloat(vlrDevedor) + parseFloat($this.data('valor'));
                }
            }
        });

        var diferenca = vlrCredor.toFixed(2) - vlrDevedor.toFixed(2);
        var valor = number_format(diferenca, 2, ',', '.');
        $('#saldo_' + id).val(valor);
    });

    $('body').on('change', '.lancamento_item_tipo', function () {
        confereLancamentosItensTipo($(this).data('id'));
        if ($(this).val() == 1)
            $(this).parent().parent().removeClass('danger').addClass('success').next().removeClass('danger').addClass('success');
        else
            $(this).parent().parent().removeClass('success').addClass('danger').next().removeClass('success').addClass('danger');
    });

    $('body').on('click', ".btn_travar", function () {
        var $this = $(this);
        var mes_periodo = $this.data('mes');
        var ano_periodo = $this.data('ano');
        var projeto = $this.data('projeto');
        var lancamento = $this.data('lancamento');
        $.post('lancamento_controle.php', {method: 'travar', mes_periodo: mes_periodo, ano_periodo: ano_periodo, projeto: projeto,lancamento: lancamento } , function (data) {
            bootAlert(data.msg, 'Per�odo Fechado ...', null, data.status);
            if (data.status === 'success') {
                $this.closest('tr').remove();
            }
        }, 'json');
    });

    $("body").on('click', ".btn_visualizar", function () {
        var $this = $(this);
        var id = $this.data('id');
        var nome_projeto = $this.closest('tr').find('.projeto').text();
        var lote = $this.closest('tr').find('.lote').text();
        $.post('lancamento_controle.php', {method: 'ver_finalizado', id: id}, function (dados) {
            bootShow(dados, '<strong>Concilia&ccedil;&atilde;o</strong> <i class="fa fa-angle-double-right"></i> Lote ' + lote + ' - ' + nome_projeto);
        });
    });

    $("body").on('click', ".btn_reabrir", function () {
        var $this = $(this);
        var id = $this.data('id');
        $.post('lancamento_controle.php', {method: 'reabrir', id: id}, function (data) {
            bootAlert(data.msg, 'Reabrindo...', null, data.status);
            if (data.status === 'success') {
//                $this.closest('tr').remove();
//                $.post('lancamento_controle.php', {method: 'consultar_finalizados'}, function (data) {
//                    $("#tabela_finalizados").html(data);
//                });
                $("#consultar_conciliacao, #consultar_finalizados").trigger('click');
            }
        }, 'json');
    });

    // -------------------------------------------------------------------------

    $('body').on('click', '.editar_lancamento_item', function () {
        var conta = $(this).data('conta');
        var id_conta = $(this).data('id_conta');
        var descricao = $(this).data('descricao');
        var tipo = $(this).data('tipo');
        var valor = $(this).data('valor');
        var historico = $(this).data('historico_item');
        var id = $(this).data('id');
        var id_lancamento = $(this).data('id_lancamento');

        var html = 
                $('<form>', {id: 'form_edit_item', method: 'post', action: 'lancamento_controle.php', class: 'form-horizontal'}).append(
                    $('<div>', {class: 'form-group'}).append(
                        $('<label>', {class: 'control-label', text: 'Conta'}),
                        $('<input>', {type: 'text', name: 'conta[' + id_lancamento + '][]', id: 'conta_' + id, 'data-conta': 'desc_' + id, 'data-conta_id': 'id_conta_' + id, class: 'form-control input-sm conta validate[required]', value: conta}),
                        $('<input>', {type: 'hidden', name: 'id_conta[' + id_lancamento + '][]', id: 'id_conta_' + id, 'data-conta': 'desc_' + id, 'data-conta_id': 'id_conta_' + id, class: 'form-control input-sm conta validate[required]', value: id_conta})
                    ),
                    $('<div>', {class: 'form-group'}).append(
                        $('<label>', {class: 'control-label', text: 'Descri��o'}),
                        $('<input>', {type: 'text', name: 'descricao[' + id_lancamento + '][]', id: 'desc_' + id, class: 'form-control input-sm validate[required]', value: descricao})
                    ),
                    $('<div>', {class: 'form-group'}).append(
                        $('<label>', {class: 'control-label', text: 'Valor'}),
                        $('<input>', {type: 'text', name: 'valor[' + id_lancamento + '][]', id: 'valor', class: 'form-control input-sm money validate[required]', value: valor})
                    ),
                    $('<div>', {class: 'form-group'}).append(
                        $('<label>', {class: 'control-label', text: 'Natureza'}),
                        $('<select>', {name: 'tipo[' + id_lancamento + '][]', id: 'tipo', class: 'validate[required,custom[select]] lancamento_item_tipo form-control input-sm', value: tipo}).append(
                            $('<option>', {value: '1', text: 'Cr�dito', selected: (tipo == 1) ? true : false}),
                            $('<option>', {value: '2', text: 'D�bito', selected: (tipo == 2) ? true : false})
                        )
                    ),
                    $('<div>', {class: 'form-group'}).append(
                        $('<label>', {class: 'control-label', text: 'Hist�rico'}),
                        $('<input>', {type: 'text', name: 'historico_item[' + id_lancamento + '][]', id: 'historico', class: 'form-control input-sm validate[required]', value: historico}),
                        $('<input>', {type: 'hidden', name: 'id_lancamento[]', id: 'id_lancamento', value: id_lancamento}),
                        $('<input>', {type: 'hidden', name: 'id_lancamento_item[' + id_lancamento + '][]', id: 'id_lancamento_item', value: id})
                    )
                );
        
        $('.money').focus();
        bootConfirm( html,
            'Edi��o',
            function (data) {
                if (data == true) {
                    var id_lote = $("#id_lote").val();
                    $('.money').focus();
                    $("#form_edit_item").ajaxSubmit({
                        dataType: 'json',
                        data: {method: 'salvar_conciliacao'},
//                        beforeSubmit: form_conciliacao_beforeSubmit,
                            success: function () {
                                bootAlert('Sucesso.', 'Salvar...', function () {
                                    location.reload();
                                }, data.status);

                            }
                        });
                    }
                },
                'primary');
    })

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

function form_lote_beforeSubmit(formData, jqForm, options) {
    return jqForm.validationEngine('validate');
}

function form_lote_success(data, status, xhr, $form) {
    bootAlert(data.msg, 'Salvando...', function () {
        if (data.status === 'success') {
            $.post('lancamento_controle.php', {method: 'consultar_lancamentos'}, function (data) {
                $("#tabela_lancamento tbody").html(data);
            });
        }
    }, data.status);
}

// -----------------------------------------------------------------------------

function form_lancamento_success(data, status, xhr, $form) {
    $("#tabela_lancamento tbody").html(data);
}

// -----------------------------------------------------------------------------

function form_consulta_beforeSubmit(formData, jqForm, options) {
    return jqForm.validationEngine('validate');
}

function form_consulta_success(data, status, xhr, $form) {
//    $("#dados_conciliacao").html(data);
    if (data.length > 0) {
        $("#tabela_conciliacao").parent().removeClass('hidden');
        $("#tabela_conciliacao").html(data);
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
//        console.log(saldo);
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
//            $.post('lancamento_controle.php', {method: 'consultar_finalizados'}, function (data) {
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

function bootForm(msg, title, type, buttons) {
    if (typeof type === 'undefined' || type === '' || type === null) {
        type = 'primary';
    }
    BootstrapDialog.show({
        type: 'type-' + type,
        title: title,
        message: msg,
        nl2br: false,
        closable: false,
        buttons: buttons,
        onshown: function () {
            $('.datalancamento').datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '2005:c+1'
            });
        },
        size: 'size-wide'
    });
}

function bootShow(msg, title, type) {
    if (typeof type === 'undefined' || type === '' || type === null) {
        type = 'primary';
    }
    BootstrapDialog.show({
        type: 'type-' + type,
        title: title,
        message: msg,
        nl2br: false,
        size: 'size-wide'
    });
}

function geraInputHidden(name, value, tipos, classe) {
    return "<input class=\"" + classe + "\" type=\"hidden\" name=\"" + name + "\" value=\"" + value + "\" data-id=\"" + tipos + "\">";
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
   
function confereLancamentosItensTipo(id_lancamento) {
    var cred = 0;
    var debt = 0;

    $('.' + id_lancamento + ' .lancamento_item_tipo').each(function (i, value) {
        console.log(i);
        console.log(value);
        console.log($(this).val());
        if ($(this).val() == 1) {
            cred++;
        } else if ($(this).val() == 2) {
            debt++;
        }
    });

    if (cred > 1 && debt > 1) {
        bootAlert("1(um) Cr�dito X v�rios D�bitos ou 1(um) D�bito X v�rios Cr�ditos.", "Aten��o Lan�amento Mult�plos!", function () {
            $(".btnSalvar").prop('disabled', true);
        }, 'danger');
    } else {
        $(".btnSalvar").prop('disabled', false);
    }
}