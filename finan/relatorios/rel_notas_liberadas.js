$(function () {
    var link = 'rel_notas_liberadas_controle.php';
    $(".check_nfse").change(function () {
        var $this = $(this);
        var validacao = $this.data('validacao');

        if ($this.prop('checked') && validacao == 0) {
            var codigo_servico = $this.data('cod-serv');
            var id_prestador = $this.data('prestador');
//                        console.log(codigo_servico + " " + id_prestador);
            $.post(link, {method: 'form_subgrupo', codigo_servico: codigo_servico, id_prestador: id_prestador}, function (data) {
                BootstrapDialog.show({
                    nl2br: false,
                    title: 'Informe os campos abaixo',
                    message: data,
                    type: 'type-warning',
                    buttons: [
                        {
                            label: '<i class="fa fa-times"></i> Fechar',
                            action: function (dialogRef) {
                                $this.prop('checked', false);
                                dialogRef.close();
                            }
                        },
                        {
                            label: '<i class="fa fa-floppy-o"></i> Salvar',
                            cssClass: 'btn-warning',
                            action: function (dialogRef) {
                                if (enviar()) {
                                    $this.closest('tr').removeClass('warning');
                                    dialogRef.close();
                                }
                            }
                        }
                    ],
                    closable: false
                });
            });

        }


        if ($("#checkAll").prop('checked', false)) {
            if ($this.prop('checked')) {
                $this.closest('tr').find('.data_vencimento_individual').prop('disabled', false);
                $(".btn_parcelar").prop('disabled', false);

            } else {
                $this.closest('tr').find('.data_vencimento_individual').prop('disabled', true);
                $(".btn_parcelar").prop('disabled', true);
            }
        }

    });

    $("#form1").validationEngine({promptPosition: "topRight"});

    $("#filt").click(function () {
        $("#data_vencimento").removeClass('validate[required]');
    });

//                $("#projeto").change(function(){
//                    $.post(window.location.href,{method:'carregaPrestadores',projeto:$(this).val()},function(data){
//                        $("#prestador").html(data);
//                    });
//                });

    $('body').on('change', '#subgrupo', function () {
        $.post(link, {method: 'getTipo', id_sub: $(this).val()}, function (data) {
            $("#tipo").html(data);
        });
    });

    $("#banco_all").change(function () {
        var banco_all = $(this).val();
        $('.banco').val(banco_all);
    });

    $("#checkAll").click(function () {
        $('.check_nfse.tem_tipo_class:checkbox').not(this).prop('checked', this.checked);

        if ($(this).prop('checked')) {
            $("#data_vencimento").prop('disabled', false);
        } else {
            $("#data_vencimento").prop('disabled', true);
        }

    });

    $('.btn_parcelar').click(function () {
        var valor = $(this).data('valor-liquido');
        var id_nfse = $(this).data('id');
        var data_vencimento = $(this).closest('tr').find('.data_vencimento_individual').val();
        var formulario = $('<div>').append(
                $('<div>', {'class': 'row'}).append(
                $('<div>', {'class': 'col-sm-4'}).append(
                $('<div>', {'class': 'form-group'}).append(
                $('<label>', {'for': 'n_parcelas'}).append('Número de Parcelas'),
                $('<input>', {'type': 'text', 'class': 'form-control', id: 'n_parcelas', name: 'n_parcelas'})
                ),
                ),
                $('<div>', {'class': 'col-sm-4'}).append(
                $('<div>', {'class': 'form-group'}).append(
                $('<label>', {'for': 'n_parcelas'}).append('Total Liquido'),
                $('<div>', {'class': 'input-group'}).append(
                $('<span>', {'class': 'input-group-addon'}).text('R$'),
                $('<input>', {'type': 'text', 'class': 'form-control', id: 'valor_liquido', disabled: 'disabled'}).val(number_format(valor, 2, ',', '.')),
                $('<input>', {'type': 'hidden', 'class': 'form-control', id: 'data_parcelado', disabled: 'disabled'}).val(data_vencimento)
                )
                ),
                ),
                ),
                $('<div>', {'id': 'parcelas'})
                );

        BootstrapDialog.show({
            nl2br: false,
            title: 'Parcelas',
            message: formulario,
            type: 'type-info',
            size: 'size-wide',
            closable: false,
            buttons: [
                // btn fechar
                {
                    id: 'fechar',
                    label: '<i class="fa fa-times"></i> Fechar',
                    cssClass: 'btn-default btn-sm',
                    action: function (dialog) {
                        dialog.close();
                    }
                },
                // btn confirmar
                {
                    id: 'salvar',
                    label: '<i class="fa fa-check"></i> Confirma',
                    cssClass: 'btn-sm btn-info',
                    action: function (dialog) {
                        prepara_parcelas_post(id_nfse);
                        dialog.close();
                    }
                }]
        });
    });

    function prepara_parcelas_post(id_nfse) {
        var div = $('#parcelas_' + id_nfse);
        console.log(div.html());
        console.log(div);
        div.html('');
        $(".procentagem_parcela").each(function (i, v) {
            console.log('procentagem_parcela: ' + $(v).val());
            div.append($('<input>', {type: 'hidden', name: 'porcentagem_p[' + id_nfse + '][' + i + ']', id: 'porcentagem_p_' + id_nfse, value: $(v).val()}));
        });
        $(".valor_parcela").each(function (i, v) {
            console.log('valor_parcela: ' + $(v).val());
            div.append($('<input>', {type: 'hidden', name: 'valor_p[' + id_nfse + '][' + i + ']', id: 'valor_p_' + id_nfse, value: $(v).val()}));
        });
        $(".data_parcela").each(function (i, v) {
            console.log('data_parcela: ' + $(v).val());
            div.append($('<input>', {type: 'hidden', name: 'data_p[' + id_nfse + '][' + i + ']', id: 'data_p_' + id_nfse, value: $(v).val()}));
        });
        console.log(div.html());
    }

    $('body').on('keyup', '#n_parcelas', function () {
        var n_parcelas = parseInt($('#n_parcelas').val());
        var valor_liquido = parseFloat($('#valor_liquido').val().replace(/\./g, '').replace(/\,/g, '.'));
        var pc = 100 / n_parcelas;
        var v = valor_liquido / n_parcelas;
        $('#parcelas').html('');
        var html = null;
        var data_parcela = $('#data_parcelado').val();
        for (var i = 0; i < n_parcelas; i++) {
            if(i > 0){
                data_parcela = calc_data_parcela(data_parcela);
            }
            
            var texto_parcela = "(" + (i + 1) + "a. Parcela)";
            html =
                    $('<div>', {'class': 'row'}).append(
                    $('<div>', {'class': 'col-sm-4'}).append(
                    $('<div>', {'class': 'form-group'}).append(
                    $('<label>', {'for': 'n_parcelas'}).append('Porcentagem ' + texto_parcela),
                    $('<div>', {'class': 'input-group'}).append(
                    $('<input>', {'type': 'text', 'class': 'form-control input-sm procentagem_parcela', id: 'porcentagem_' + i, name: 'porcentagem[]', 'data-key': i}),
                    $('<span>', {'class': 'input-group-addon'}).text('%')
                    )
                    )
                    ),
                    $('<div>', {'class': 'col-sm-4'}).append(
                    $('<div>', {'class': 'form-group'}).append(
                    $('<label>', {'for': 'n_parcelas'}).append('Valor ' + texto_parcela),
                    $('<div>', {'class': 'input-group'}).append(
                    $('<span>', {'class': 'input-group-addon'}).text('R$'),
                    $('<input>', {'type': 'text', 'class': 'form-control input-sm valor_parcela', id: 'valor_' + i, name: 'valor[]', 'data-key': i}),
                    )
                    )
                    ),
                    $('<div>', {'class': 'col-sm-4'}).append(
                    $('<div>', {'class': 'form-group'}).append(
                    $('<label>', {'for': 'n_parcelas'}).append('Data ' + texto_parcela),
                    $('<input>', {'type': 'text', 'class': 'form-control input-sm data_parcela', id: 'data_' + i, name: 'data[]'})
                    )
                    ),
                    );

            html.find('.valor_parcela').maskMoney({allowNegative: true, thousands: '.', decimal: ','});
            html.find('#valor_' + i).maskMoney('mask', v);
            html.find('.procentagem_parcela').maskMoney({allowNegative: true, thousands: '.', decimal: ','});
            html.find('#porcentagem_' + i).maskMoney('mask', pc);
            html.find('.data_parcela').mask("99/99/9999");
            html.find('#data_' + i).val(data_parcela);
            $('#parcelas').append(html);
        }

    });

    var totalRateio = parseFloat(0);
    function somaValorUnidades(key) {
        var valor_liquido = parseFloat($('#valor_liquido').val().replace(/\./g, '').replace(/\,/g, '.'));
        var valor_total = parseFloat(0);
        var valor_uni;
        $('.valor_parcela').each(function (index, value) {
            if (value.value) {
                valor_uni = parseFloat(value.value.replace(/\./g, '').replace(/\,/g, '.'));
                //                        console.log(valor_uni);
                valor_total = valor_total + valor_uni;
            }
        });
        totalRateio = valor_total.toFixed(2);
        if (valor_liquido < valor_total.toFixed(2)) {
            bootAlert('A SOMA DOS VALORES DAS PARCELAS (' + valor_total.toFixed(2) + ') ESTÁ MAIOR QUE O VALOR LIQUIDO (' + valor_liquido.toFixed(2) + ')', 'SOMA DOS VALORES DAS PARCELAS', null, 'danger');
            $('#valor_' + key + ', #porcentagem_' + key).val('');
            return false;
        }
        return true;
    }


    $('body').on('keyup', '.valor_parcela, .procentagem_parcela', function () {
        var key = $(this).data('key');
        var valor_liquido = parseFloat($('#valor_liquido').val().replace(/\./g, '').replace(/\,/g, '.'));
        var valor_uni = parseFloat($('#valor_' + key).val().replace(/\./g, '').replace(/\,/g, '.'));
        var percent_uni = parseFloat($('#porcentagem_' + key).val().replace(/\./g, '').replace(/\,/g, '.'));
        console.log(percent_uni);
        if ($(this).prop('id') == 'valor_' + key) {
            var percent = ((valor_uni * 100) / valor_liquido);
//                        console.log(percent.toFixed(2));
            if (percent > 0) {
                $('#porcentagem_' + key).val(percent.toFixed(2));
            } else {
                $('#porcentagem_' + key).val(0);
            }
        } else if ($(this).prop('id') == 'porcentagem_' + key) {
            console.log('valor_liquido' + valor_liquido);
            console.log('percent_uni' + percent_uni);
            console.log('(percent_uni / 100)' + (percent_uni / 100));
            var valor = (valor_liquido * (percent_uni / 100));
            console.log(valor.toFixed(2));
            valor = number_format(valor.toFixed(2), 2, ',', '.');
            $('#valor_' + key).val(valor).maskMoney({thousands: '.', decimal: ','});
            ;
        }
        somaValorUnidades(key);
    });

});

function enviar() {
    var retorno = true;
    $("#form_subgrupo").ajaxSubmit({
        beforeSubmit: function () {
            return $("#form_subgrupo").validationEngine('validate');
        },
        data: {'method': 'salvar'},
        dataType: 'json',
        success: function (dados) {
            if (dados.status === true) {
                bootAlert("Salvo com Sucesso!", "Salvar", null, 'success');
            } else {
                bootAlert("Erro ao Salvar!", "Salvar", null, 'danger');
                retorno = false;
            }
        }
    });
    return retorno;
}

function calc_data_parcela(data_anterior) {
    if (data_anterior == '') {
        return '';
    }
    var arr = data_anterior.split('/');
    var data = new Date(arr[2], arr[1]-1, arr[0]);
    data.setMonth(data.getMonth() + 1);
    var pad = "00";
    var diaX = data.getDate() + '';
    var dia = pad.substring(0, pad.length - diaX.length) + diaX;
    var mesX = data.getMonth()+1 + '';
    var mes = pad.substring(0, pad.length - mesX.length) + mesX;
    return [dia, '/', mes, '/', data.getFullYear()].join('');
}