$(document).ready(function () {
    //datepicker
    $('.data').datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '2005:c+1'
    });

    // carregando projetos -----------------------------------------------------
    $('#regiao1,#regiao2').change(function () {
        var destino = $(this).data('for');
        $.post("../../methods.php", {method: "carregaProjetos", regiao: $(this).val()}, function (data) {
            $("#" + destino).html(data);
        });
    });
    $('#projeto1,#projeto2').change(function () {
        var destino = $(this).data('for');
        var tipo = ($('input[name=situacao_prestador]:checked').val() == '1') ? "carregaPrestadores" : "carregaPrestadoresInativos";
        $.post("../../methods.php", {method: tipo, projeto: $(this).val()}, function (data) {
            $("#" + destino).html(data);
        });
    });
    $('.situacao_prestador').change(function(){
        var tipo = ($('input[name=situacao_prestador]:checked').val() == 1)?"carregaPrestadores":"carregaPrestadoresInativos";
        $.post("../../methods.php", {method: tipo, projeto: $('#projeto1').val()}, function (data) {
            $("#prestador1").html(data);
        });
    });
    // -------------------------------------------------------------------------

    // mascaras e formatações --------------------------------------------------
    $('body').on('focus', '.money', function () {
        $('.money').maskMoney({allowNegative: true, thousands: '.', decimal: ',', affixesStay: false});
    });
    $("#dt_emissao_nf, #data_vencimento").mask("99/99/9999");
    $("#cnpj, #numero_cnpj").mask("99.999.999/9999-99");
    $("#codigoverificacao").mask("****-****?*");
    // -------------------------------------------------------------------------

    // form-serv cadastro de nfse ----------------------------------------------

    $("#form-serv").validationEngine({promptPosition: "topRight"});

    $("#form-serv").ajaxForm({
        beforeSubmit: function (formData, jqForm, options) {

            var valid = jqForm.validationEngine('validate');
            if (valid == true) {
                $("#resp_form_cad").html('<p class="text-center"><i class="fa fa-spinner fa-spin fa-4x"></i></p>');
                return true;
            } else {
                return false;
            }
        },
        success: function (data) {
            $("#resp_form_cad").html(data);
            $("#endeforn").html('');
            $("#fornecedor").html('');
        },
        resetForm: true
    });

    $('#vlr_bruto').bind('change', function () {
        var vlr_bruto = parseFloat($(this).maskMoney('unmasked')[0]);
        var cofins = vlr_bruto * 0.03;      //  3%       COFINS
        var csll = vlr_bruto * 0.01;        //  1%       CSLL
        var irpj = vlr_bruto * 0.015;       //  1,5%     IRPJ
        var pis = vlr_bruto * 0.0065;       //  0,65%    PIS
        var inss = 0;
        var resultado = vlr_bruto - (cofins + csll + irpj + pis + inss);
        console.log("valor bruto: " + vlr_bruto);
        
        console.log(cofins);
        console.log(csll);
        console.log(irpj);
        console.log(pis);
        console.log(inss);
        console.log(resultado);
    });
    


    // form2 -------------------------------------------------------------------
    $("#limpa").click(function () {
        $("#tabela").html('');
        $("#habilitar").addClass('hidden');
    });

    $("#form_ler_arquivo_xml").ajaxForm({
        beforeSubmit: showRequest,
        success: function (data) {
            if ($("#salvare").prop('disabled')) {
                $("#habilitar").removeClass('hidden');
                $("#salvare").prop('disabled', false);
            } else {
                $("#habilitar").addClass('hidden');
                $("#salvare").prop('disabled', true);
                $("#form2").each(function () {
                    this.reset();
                });
            }
            $("#tabela").html(data);
        }
    });

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
        $.post("visualizar_XML.php", {id_nfe: $(this).data('id'), projeto: $("#projeto3").val(), regiao: $("#regiao3").val(), method: 'Detalhes'}, function (dados) {
            BootstrapDialog.show({
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
                $.post('visualizar_XML.php', {method: 'cancelar', id: id}, function (data) {
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


 $("#CodigoTributacaoMunicipio").keyup(function () {
        var cod = $('#CodigoTributacaoMunicipio').val();
        $(this).after('<i class="fa fa-spinner fa-spin form-control-feedback" id="loading"></i>');
        $.post('nfse_atualiza.php', {method: 'cosulta_cod_servico', cod: cod}, function (data) {
            $("#loading").remove();
            $("#CodigoTributacaoMunicipio").autocomplete({
                source: data.servicos,
                minLength: 3,
                change: function (event, ui) {
                    if (event.type == 'autocompletechange') {
                        var array_item = $("#CodigoTributacaoMunicipio").val().split(' - ');
                        $("#CodigoTributacaoMunicipio").val(array_item[0]);
                        $("#txt_servico").val(array_item[1]);
                    }
                }
            });
        }, 'json');
    });
});

function formata_mascara(src, mascara) {
    var campo = src.value.length;
    var saida = mascara.substring(0, 1);
    var texto = mascara.substring(campo);
    if (texto.substring(0, 1) != saida) {
        src.value += texto.substring(0, 1);
    }
}

function showRequest(formData, jqForm, options) {
//    var valid = $("#form2").validationEngine('validate');
    var valid = jqForm.validationEngine('validate');
    if (valid == true) {
//        $(".loading").html("<br><p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");
        return true;
    } else {
        return false;
    }
}


