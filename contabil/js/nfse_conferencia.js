$(document).ready(function () {
    $('body').on('focus', '.money', function () {
        $('.money').maskMoney({allowNegative: true, thousands: '.', decimal: ',', affixesStay: false});
    });
    $('body').on('click', ".btn-conferir", function () {
        var $this = $(this);
        var id = $this.data('id');
        var titulo = 'ERRO AO CONSULTAR SITE DA PREFEITURA';
        $.post('nfse_conferencia_controle.php', {method: 'conferenciaImposto', id: id}, function (data) {
            BootstrapDialog.show({
                nl2br: false,
                title: 'Conferência de Nota Fiscal de Serviço',
                message: data,
                type: 'type-warning',
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
                    // btn corrigir
                    {
                        id: 'corrigir',
                        label: '<i class="fa fa-reply"></i> Solicitação de Correção',
                        cssClass: 'btn-yellow btn-sm',
                        action: function (dialog) {
                            corrigir(id, $this);
//                            dialog.close();
                        }
                    },
                    // btn Link
//                    {
//                        id: 'link',
//                        label: '<i class="fa fa-link"></i> NFSe Link',
//                        cssClass: 'btn-info btn-sm',
//                        action: function (dialog) {
//                            var numeronota = $('#numeronf_link').val();
//                            var codigoverifica = $('#codverifica_link').val();
//                            var inscricao = $('#inscricao_link').val();
//                            if (inscricao == '' || inscricao == 'N/I' || inscricao == 'NI' || numeronota == '' || codigoverifica == '') {
//                                bootAlert('Nota Fiscal de Serviço! Verificar Número da Nota Fiscal e/ou Inscrição Estadual e/ou Código de Verificação', titulo, null, 'danger');
//                            } else {
//                                window.open('https://notacarioca.rio.gov.br/contribuinte/notaprint.aspx?nf=' + numeronota + '&cod=' + codigoverifica + '&inscricao=' + inscricao, '_blank');
//                            }
//                        }
//                    },
                    // btn cancelar
                    {
                        id: 'cancelar_pedido',
                        label: '<i class="fa fa-ban"></i> Cancelar NFSe',
                        cssClass: 'btn-danger btn-sm',
                        action: function (dialog) {
                            CancelarNFSe(id, $this);
//                            dialog.close();
                        }
                    },
                    // btn confirmar
                    {
                        id: 'confirma_ok',
                        label: '<i class="fa fa-check-square-o"></i> Conferência OK',
                        cssClass: 'btn-warning btn-sm',
                        action: function (dialog) {
                            confirmaOk($this);
//                            dialog.close();
                        }
                    }]
            });
        });
    });
    $("body").on('change', '#ValorServicos', function () {

        var valor_total = $("#ValorServicos").maskMoney('unmasked')[0];

        var cofins_retencao = $("#COFINS_retencao").maskMoney('unmasked')[0];
        res_cofins = (cofins_retencao / 100) * valor_total;
        $("#COFINS_VALOR").val(number_format(res_cofins, 2, ',', '.'));

        var csll_retencao = $("#CSLL_retencao").maskMoney('unmasked')[0];
        res_csll = (csll_retencao / 100) * valor_total;
        $("#CSLL_VALOR").val(number_format(res_csll, 2, ',', '.'));

        var inss_retencao = $("#INSS_retencao").maskMoney('unmasked')[0];
        res_inss = (inss_retencao / 100) * valor_total;
        $("#INSS_VALOR").val(number_format(res_inss, 2, ',', '.'));

        var irrfpj_retencao = $("#IRRFPJ_retencao").maskMoney('unmasked')[0];
        res_irrfpj = (irrfpj_retencao / 100) * valor_total;
        console.log(res_irrfpj +"="+ "("+irrfpj_retencao+" / 100) *" + valor_total);
        $("#IRRF_VALOR").val(number_format(res_irrfpj, 2, ',', '.'));

        var iss_retencao = $("#ISS_retencao").maskMoney('unmasked')[0];
        res_iss = (iss_retencao / 100) * valor_total;
        $("#ValorIss").val(number_format(res_iss, 2, ',', '.'));

        var pis_retencao = $("#PIS_retencao").maskMoney('unmasked')[0];
        res_pis = (pis_retencao / 100) * valor_total;
        console.log(res_pis +"= ("+pis_retencao + "/ 100) * "+valor_total)
        $("#PIS_VALOR").val(number_format(res_pis, 2, ',', '.'));

        $('#BaseCalculo').val($("#ValorServicos").val()).trigger('change');

    });


    $("body").on('change', '.impostos_deducao,#ValorServicos', function () {
        var somatorio_impostos = 0.0;
        $(".impostos_deducao").each(function (i, el) {
//            console.log($(this).maskMoney('unmasked')[0]);
            somatorio_impostos += $(this).maskMoney('unmasked')[0];
        });
        var liquido = $("#ValorServicos").maskMoney('unmasked')[0] - somatorio_impostos;
        $("#ValorLiquidoNfse").val(number_format(liquido, 2, ',', '.'));
    });
    
    $("body").on('change', '#BaseCalculo,#Aliquota', function () {
        var base = $("#BaseCalculo").maskMoney('unmasked')[0];
        var aliquota = $("#Aliquota").maskMoney('unmasked')[0];
        console.log(base + " * " + aliquota + " / 100 = " + (base * aliquota / 100));
        $("#ValorIss").val(number_format(base * aliquota / 100, 2, ',', '.')).trigger('change');
    });
});

function confirmaOk($this) {
    if ($("#COFINS_VALOR").maskMoney('unmasked')[0] == 0 &&
            $("#ValorIss").maskMoney('unmasked')[0] == 0 &&
            $("#CSLL_VALOR").maskMoney('unmasked')[0] == 0 &&
            $("#INSS_VALOR").maskMoney('unmasked')[0] == 0 &&
            $("#IRRF_VALOR").maskMoney('unmasked')[0] == 0 &&
            $("#PIS_VALOR").maskMoney('unmasked')[0] == 0
            ) {
        bootConfirm("TEM CERTEZA QUE NÃO HÁ RETENÇÕES NESTA NOTA?", 'ATENÇÃO', function (resultado) {
            if (!resultado) {
                $retorno = false;
            } else {
                fazer_submit($this);
            }
        });
    } else {
        fazer_submit($this);
    }
}

function fazer_submit($this) {
    $("#form_conferencia").ajaxSubmit({
        dataType: 'json',
        beforeSubmit: function () {
            var $retorno = true;
            if ($("#ValorLiquidoNfse").maskMoney('unmasked')[0] <= 0) {
                bootAlert("Valor Liquido zerado ou negativo!", "ATENÇÃO", null, 'danger');
                $retorno = false;
            }
            return $retorno;
        },
        data: {method: 'confirmaOk'},
        success: function (data) {
            bootAlert(data.msg, 'Confirmação', function () {
                $.each(BootstrapDialog.dialogs, function (id, dialog) {
                    dialog.close();
                });
                if (data.status === 'success') {
                    $this.closest('tr').remove();
                }
            }, data.status);
        }
    });
}

function CancelarNFSe(id, $this) {
    bootConfirm('O cancelamento da Nota Fiscal deve ser realizado quando os <strong>valores</strong> dos impostos constantes na mesma estiverem incorretos.<br> Clique em <strong>OK</strong> para confirmar o cancelamento ou clique em <strong>Cancelar</strong> para fechar esta janela.', 'Cancelamento da Nota', function (resp) {
        if (resp) {
            $.post('nfse_conferencia_controle.php', {method: 'cancelarNFSe', id: id}, function (data) {
                bootAlert(data.msg, 'Cancelamento da Nota', function () {
                    $.each(BootstrapDialog.dialogs, function (id, dialog) {
                        dialog.close();
                    });
                    if (data.status === 'success') {
                        $this.closest('tr').remove();
                    }
                }, data.status);
            }, 'json');
        }
    }, 'danger');
}

function corrigir(id, $this) {
    var botoes = [
        {
            id: 'cancelar_pedido',
            label: '<i class="fa fa-times"></i> Sair',
            cssClass: 'btn-default btn-sm',
            action: function (dialog) {
                dialog.close();
            }
        },
        {
            id: 'Solicitar',
            label: ' Solicitar Correção <i class="fa fa-arrow-right"></i>',
            cssClass: 'btn-primary btn-sm',
            action: function (dialog) {
                var motivo = $("#motivo_correcao").val();
                console.log(!motivo);
                console.log(motivo);
                if (motivo.length === 0) {
                    console.log('entrou no if');
                    $("#motivo_correcao").closest('.form-group').addClass('has-error');
                    $("#motivo_correcao").attr('placeholder', 'Campo obrigatório!').focus();
                    return;
                }

                $.post('nfse_conferencia_controle.php', {method: 'correcao', id: id, motivo: motivo}, function (data) {
                    if (data.status) {
                        bootAlert(data.msg, 'Solicitação da Correção', function () {
                            $.each(BootstrapDialog.dialogs, function (id, dialog) {
                                dialog.close();
                            });

                            $this.closest('tr').remove();

                        }, 'success');

                    } else {
                        bootAlert(data.msg, 'Solicitação da Correção', null, 'danger');
                    }

                }, 'json');
//                            dialog.close();
            }
        }
    ];

    var html = $('<div>').append(
            $("<p>", {class: 'text-justify'}).append('A NFSe só pode ser corrigida em caso de erros de digitação. Abaixo informe o erro encontrado.'),
            $("<div>", {class: 'form-group'}).append(
            $("<textarea>", {class: 'form-control', name: 'motivo_correcao', id: 'motivo_correcao', rows: '3', placeholder: 'Motivo da solicitação de correção'}))
            );

    bootDialog(html, 'Solicitação de Correção', botoes, 'primary');
}

function number_format(number, decimals, dec_point, thousands_sep) {
//  discuss at: http://phpjs.org/functions/number_format/
// original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
// improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
// improved by: davook
// improved by: Brett Zamir (http://brett-zamir.me)
// improved by: Brett Zamir (http://brett-zamir.me)
// improved by: Theriault
// improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
// bugfixed by: Michael White (http://getsprink.com)
// bugfixed by: Benjamin Lupton
// bugfixed by: Allan Jensen (http://www.winternet.no)
// bugfixed by: Howard Yeend
// bugfixed by: Diogo Resende
// bugfixed by: Rival
// bugfixed by: Brett Zamir (http://brett-zamir.me)
//  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
//  revised by: Luke Smith (http://lucassmith.name)
//    input by: Kheang Hok Chin (http://www.distantia.ca/)
//    input by: Jay Klehr
//    input by: Amir Habibi (http://www.residence-mixte.com/)
//    input by: Amirouche
//   example 1: number_format(1234.56);
//   returns 1: '1,235'
//   example 2: number_format(1234.56, 2, ',', ' ');
//   returns 2: '1 234,56'
//   example 3: number_format(1234.5678, 2, '.', '');
//   returns 3: '1234.57'
//   example 4: number_format(67, 2, ',', '.');
//   returns 4: '67,00'
//   example 5: number_format(1000);
//   returns 5: '1,000'
//   example 6: number_format(67.311, 2);
//   returns 6: '67.31'
//   example 7: number_format(1000.55, 1);
//   returns 7: '1,000.6'
//   example 8: number_format(67000, 5, ',', '.');
//   returns 8: '67.000,00000'
//   example 9: number_format(0.9, 0);
//   returns 9: '1'
//  example 10: number_format('1.20', 2);
//  returns 10: '1.20'
//  example 11: number_format('1.20', 4);
//  returns 11: '1.2000'
//  example 12: number_format('1.2000', 3);
//  returns 12: '1.200'
//  example 13: number_format('1 000,50', 2, '.', ' ');
//  returns 13: '100 050.00'
//  example 14: number_format(1e-8, 8, '.', '');
//  returns 14: '0.00000001'

    number = (number + '')
            .replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + (Math.round(n * k) / k)
                        .toFixed(prec);
            };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
            .split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '')
            .length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1)
                .join('0');
    }
    return s.join(dec);
}
