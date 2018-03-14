$(function () {

    $('body').on('change', '.base_pensao', function () {

        var t = $(this);
        var val = t.val();

        if (val == 3) {
            t.parent().parent().parent().find('.quantSalMinimo').removeClass('hide');
            t.parent().parent().parent().find('.aliquota').addClass('hide');
            t.parent().parent().parent().find('.valorfixo').addClass('hide')
        } else if (val == 4) {
            t.parent().parent().parent().find('.quantSalMinimo').addClass('hide')
            t.parent().parent().parent().find('.aliquota').addClass('hide')
            t.parent().parent().parent().find('.valorfixo').removeClass('hide');
        } else if (val == 5) {
            t.parent().parent().parent().find('.quantSalMinimo').addClass('hide')
            t.parent().parent().parent().find('.aliquota').addClass('hide')
            t.parent().parent().parent().find('.valorfixo').addClass('hide')
        } else {
            t.parent().parent().parent().find('.quantSalMinimo').addClass('hide')
            t.parent().parent().parent().find('.aliquota').removeClass('hide');
            t.parent().parent().parent().find('.valorfixo').addClass('hide')
        }

    });

    $('body').on('click', '#suc_vinc_trab', function () {

        t = $(this);

        if (t.is(':checked')) {

            $('#box_suc_vinc_trab').removeClass('hide');

        } else {
            $('#box_suc_vinc_trab').addClass('hide');
        }

    });

    $("#tipo_prazo_contrato").on('change', function () {

        var t = $(this);
        tipo = t.val();

        if (tipo == 2) {
            $('.box_prazo_contrato').removeClass('hide');
            $('#prazo_contrato, #possui_clausula_asseguratoria').addClass('validate[required]');
        } else {
            $('.box_prazo_contrato').addClass('hide');
            $('#prazo_contrato, #possui_clausula_asseguratoria').val('').removeClass('validate[required]');
        }

    });
    $("#tipo_prazo_contrato").trigger('change');

    $('body').on("change", "#data_opcao_fgts", function () {

        var t = $(this);
        var data_opcao_fgts_sel = t.val();
        var opt_fgts = $('#opt_fgts').val();
        var data_entrada = $("#data_entrada").val();
        var data_opt_fgts = dateCompare(data_opcao_fgts_sel, '04/10/1988');
        var data_opt_fgts2 = dateCompare(data_opcao_fgts_sel, '01/01/1967');

        if (data_opt_fgts == 1 || data_opt_fgts2 == -1) {

            bootAlert('Selecione uma data entre 01/01/1967 e 04/10/1988.', 'Data Inválida', null, 'warning');

        }

    });

    $('body').on("change", "#opt_fgts", function () {

        var t = $(this);
        var opt = t.val();
        var data_entrada = $("#data_entrada").val();
        var data_opt_fgts = dateCompare(data_entrada, '04/10/1988');
        var data_opt_fgts2 = dateCompare(data_entrada, '01/01/1967');
        var style = "pointer-events: none;touch-action: none;cursor: not-allowed;background-color: #eee;opacity: 1;";

        if (opt == 1) {

            $("#data_opcao_fgts").val(null);
            $("#data_opcao_fgts").prop('required', true);
            $("#data_opcao_fgts").removeAttr("tabindex");
            $("#data_opcao_fgts").removeAttr('readonly');
            $("#data_opcao_fgts").removeAttr('style');

        } else if (opt == 2) {

            $("#data_opcao_fgts").val(null);
            $("#data_opcao_fgts").prop('readonly', true);
            $("#data_opcao_fgts").prop('style', style);

        }

    });

    $('body').on("change", "#data_entrada", function () {

        var t = $(this);
        var data_entrada = t.val();
        var data_opt_fgts = dateCompare(data_entrada, '04/10/1988');
        var data_opt_fgts2 = dateCompare(data_entrada, '01/01/1967');
        var style = "pointer-events: none;touch-action: none;cursor: not-allowed;background-color: #eee;opacity: 1;";

        if (data_opt_fgts == 1 || data_opt_fgts == 0) {

            $("#opt_fgts").val('1');
            $("#opt_fgts").prop("tabindex", -1);
            $("#opt_fgts").prop('style', style);

            $("#data_opcao_fgts").val(data_entrada);
            $("#data_opcao_fgts").prop('readonly', true);
            $("#data_opcao_fgts").prop('style', style);

        } else if ((data_opt_fgts2 > -1 && data_opt_fgts == -1) || data_opt_fgts2 < 0) {

            $("#opt_fgts").val('-1');
            $("#opt_fgts").removeAttr("tabindex");
            $("#opt_fgts").removeAttr('readonly');
            $("#opt_fgts").removeAttr('style');

            $("#data_opcao_fgts").val(null);
            $("#data_opcao_fgts").prop('readonly', true);
            $("#data_opcao_fgts").prop('style', style);

        }

    });

    $('body').on("change", "#id_pais_residencia", function () {

        t = $(this);
        pReside = t.val();
        if (pReside == 0) {
            $('.residente_extrangeiro').addClass('hide');
            $('.residente_brasil').addClass('hide');
        } else if (pReside == 1) {
            $('.residente_extrangeiro').addClass('hide');
            $('.residente_brasil').removeClass('hide');
        } else {
            $('.residente_extrangeiro').removeClass('hide');
            $('.residente_brasil').addClass('hide');
        }
    });
    $('#id_pais_residencia').trigger("change");

    $('body').on("change", "#id_pais_nasc", function () {
        var t = $(this);
        var pais = t.val();
        if (pais != 1) {
            $("#uf_nasc, #municipio_nasc").attr("disabled", "disabled");
        } else {
            $("#uf_nasc, #municipio_nasc").removeAttr("disabled");
        }
    });

    $('body').on("change", "#id_pais_nacionalidade", function () {
        var t = $(this);
        var pais = t.val();
        if (pais > 1) {
            $("#dtChegadaPais, #condicao_estrangeiro, #casado_brasileiro, #filhos_br").removeAttr("disabled");
            $("#dtChegadaPais, #condicao_estrangeiro").addClass("validate[required]");
        } else {
            $("#dtChegadaPais, #condicao_estrangeiro, #casado_brasileiro, #filhos_br").attr("disabled", "disabled");
            $("#dtChegadaPais, #condicao_estrangeiro").removeClass("validate[required]");
        }
    });
    $("#id_pais_nacionalidade").trigger('change');

    $('body').on("change", "#id_pais_nasc", function () {
        var t = $(this);
        var pais = t.val();
        if (pais != 1) {
            $("#dtChegadaPais").removeAttr("disabled");
        } else {
            $("#dtChegadaPais").attr("disabled", "disabled");
        }
    });

    $('body').on("change", "#uf_nasc", function () {
        var t = $(this);
        var uf = t.val();
        $.post("", {getAllMunicipios: true, uf: uf}, function (data) {
            data = JSON.parse(data);
            $("#municipio_nasc").html(data);
            $("#id_municipio_nasc").val("");
            $("#id_municipio_nasc_text").html("");
        });
    });

    $('body').on("change", "#uf", function () {
        var t = $(this);
        var uf = t.val();
        $.post("", {getAllMunicipios: true, uf: uf}, function (data) {
            data = JSON.parse(data);
            $("#municipio_end").html(data);
            $("#id_municipio_end").val("");
        });
    });

    $('body').on("change", "#municipio_nasc", function () {
        var t = $(this);
        var municipio = t.val();
        if (municipio != -1) {
            $("#id_municipio_nasc").val(municipio);
            $("#id_municipio_nasc_text").html(municipio);
        }
    });

    $('body').on("change", "#municipio_end", function () {
        var t = $(this);
        var municipio = t.val();
        if (municipio != -1) {
            $("#id_municipio_end").val(municipio);
        }
    });

    if ($('#deficiencia').is(":checked")) {
        $('.portador_deficiencia').show();
    } else {
        $('.portador_deficiencia').hide();
    }

    //desabilita envio do form pelo ENTER
    $('input').keypress(function (e) {
//        console.log("chamou a function");
        var code = null;
        code = (e.keyCode ? e.keyCode : e.which);
        return (code == 13) ? false : true;
    });


    $("#unidade_porcentagem1,#unidade_porcentagem2,#unidade_porcentagem3").on("blur", function () {
        if ($("#unidade_porcentagem1").val() > 100) {
            $("#unidade_porcentagem1").val(100);
        } else {
            if ($("#unidade_porcentagem1").val() < 0) {
                $("#unidade_porcentagem1").val(0);
            }
        }

        if ($("#unidade_porcentagem2").val() > 100) {
            $("#unidade_porcentagem2").val(100);
        } else {
            if ($("#unidade_porcentagem2").val() < 0) {
                $("#unidade_porcentagem2").val(0);
            }
        }
        if ($("#unidade_porcentagem3").val() > 100) {
            $("#unidade_porcentagem3").val(100);
        } else {
            if ($("#unidade_porcentagem3").val() < 0) {
                $("#unidade_porcentagem3").val(0);
            }
        }
    });

    //hide forms de dependentes
    if ($('#nome_filho1').val() === '') {
        $("#painel-filhos1").hide();
    }
    if ($('#nome_filho2').val() === '') {
        $("#painel-filhos2").hide();
    }
    if ($('#nome_filho3').val() === '') {
        $("#painel-filhos3").hide();
    }
    if ($('#nome_filho4').val() === '') {
        $("#painel-filhos4").hide();
    }
    if ($('#nome_filho5').val() === '') {
        $("#painel-filhos5").hide();
    }

    //action do botao ADICIONAR FILHO
    $('#add_filho').click(function () {
        if ($('#painel-filhos1').css('display') === 'none' && ($('#nome_filho0').val() !== '')) {
            $("#painel-filhos1").show();
            return;
        }
        if ($('#painel-filhos2').css('display') === 'none' && ($('#nome_filho1').val() !== '')) {
            $("#painel-filhos2").show();
            return;
        }
        if ($('#painel-filhos3').css('display') === 'none' && ($('#nome_filho2').val() !== '')) {
            $("#painel-filhos3").show();
            return;
        }
        if ($('#painel-filhos4').css('display') === 'none' && ($('#nome_filho3').val() !== '')) {
            $("#painel-filhos4").show();
            return;
        }
        if ($('#painel-filhos5').css('display') === 'none' && ($('#nome_filho4').val() !== '')) {
            $("#painel-filhos5").show();
            return;
        }
        if ($('#painel-filhos5').css('display') === 'block') {
            alert('Não é possível adicionar mais filhos');
        }
    });

    $('body').on('blur', '.nomeFavorecido', function () {
        var $this = $(this);
        if ($this.val()) {
            $('#data_nasc_filho' + $this.data('key')).addClass('validate[required]');
        }
    });

    $('body').on('blur', '.data_nasc_filho', function () {
        var $this = $(this);

        var nascimento = $this.datepicker("getDate");
        var hoje = new Date();

        var diferencaAnos = hoje.getFullYear() - nascimento.getFullYear();
        if (new Date(hoje.getFullYear(), hoje.getMonth(), hoje.getDate()) < new Date(hoje.getFullYear(), nascimento.getMonth(), nascimento.getDate()))
            diferencaAnos--;
        if (diferencaAnos >= 12) {
            $('#cpf_filho' + $this.data('key')).addClass('validate[required]');
        }
    });

    //ação para os checkboxes de deficiência
    $('#deficiencia').on('change', function () {
        if ($(this).is(":checked")) {
            $('.portador_deficiencia').show();
        } else {
            $('.portador_deficiencia').hide();
        }
    });

    /*
     * Validacao para ano de contribuicao
     */
    $("#ano_contribuicao_nao").on("change", function () {
        if ($("#ano_contribuicao_nao").prop("checked")) {
            $("#ano_contribuicao").val("");
        }
    });

    /*
     * Validacao para data de admissao/importacao
     */
    $("#status_admi").on("change", function () {
        if ($("#status_admi").val() != 70) {
            $("#data_importacao").val("");
        }
    });

    /*
     * Validacao para plano de saude
     */
    $("#medica_nao").on("change", function () {
        if ($("#medica_nao").prop("checked")) {
            $("#id_plano_saude").val("");
        }
    });

    /*
     * Controle para PDE e data
     */
    $("#data_pde").hide();
    if ($(this).is(":checked")) {
        $("#data_pde").show();
    } else {
        $("#data_pde").hide();
        $("#data_pde").val("");
    }
    $("#id_pde").on("change", function () {
    });

    $('#id_pde').trigger('change');
    /*
     * Efeitos para os campos de porcentagem
     * Impede que a validacao nao seja realizada por erro na soma dos campos
     */
    $("#unidade_porcentagem1,#unidade_porcentagem2,#unidade_porcentagem3").focus(function () {
        if ($(this).val() == 0) {
            $(this).val("")
        }
    });
    $("#unidade_porcentagem1,#unidade_porcentagem2,#unidade_porcentagem3").blur(function () {
        if ($(this).val() == "") {
            $(this).val("0")
        }
    });

    /*
     * Validação para impedir que o valor agregado 
     * das porcenttagens das unidades ultrapasse 100
     */
    $("#unidade_porcentagem1,#unidade_porcentagem2,#unidade_porcentagem3").on("change", function () {
        if (parseInt($("#unidade_porcentagem1").val()) + parseInt($("#unidade_porcentagem2").val()) + parseInt($("#unidade_porcentagem3").val()) > 100) {
            $("#unidade_porcentagem1").val("0");
            $("#unidade_porcentagem2").val("0");
            $("#unidade_porcentagem3").val("0");
            $('#alertaUnidades').modal('show');
        }
    });

    /*
     * Bloqueio de checkbox
     */
    //                $("#ckb_agencia").on("click",function(){
    //
    //                if($("#ckb_agencia").is(":checked")){
    //                         $("#agencia_dv").prop("disabled",true);
    //                         $("#agencia_dv").val("N");
    //                   }else{
    //                         $("#agencia_dv").prop("disabled",false);
    //                         $("#agencia_dv").val("");
    //                   }
    //                });


    $(".valor").maskMoney({prefix: 'R$ ', allowNegative: true, thousands: '.', decimal: ','});


    $(".dtformat").datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        yearRange: '1910:c+100',
        //yearRange: "nnnn:nnnn",
        beforeShow: function () {
            setTimeout(function () {
                $('.ui-datepicker').css('z-index', 5010);
            }, 0);
        }
    });

    /*
     * Gatilho para mostrar/esconder os dados de favorecidos
     * por pensao alimenticia.
     * A associacao e feita pelo atributo name do checkbox que
     * deve ser igual ao id da div
     */
    $("body").on('change', '.pensao', function () {
        //console.log($(this));
        //pensao_alimenticia
//        var div = "#" + $(this).attr("name");
        var div = "#pensao_alimenticia" + $(this).data("i");
//        console.log(div);
        if ($(this).prop('checked')) {
            //$(".div_favorecido").show();
            $(div).show();
            /*
             * 
             * Correcao para nome e cpf de dependentes
             * copiando os nomes para os inputs hidden
             */
            var target = $(this).attr("data-target");
            var nome = "#nome_" + target;
            var fnome = "#favorecidos_nome_" + target;
            var cpf = "#cpf_" + target;
            var fcpf = "favorecidos_cpf_" + target;

            /*
             * Atribuicao de valores
             */
            $(fnome).val($(nome).val());
            $(fcpf).val($(cpf).val());

        } else {
            //$(".div_favorecido").hide();
            $(div).hide();

            /**
             * Itera os elementos filhos da div ativada e reseta seus valores
             * @var index = o indice do elemento [0..n] <nao utilizado, apenas para referencia>
             * @var element = o elemento html
             */
//            $(div + " .form-control").each(function (index, element) {
//
//                if ($(element).attr("type") == "text" || $(element).attr("type") == "hidden") {
//                    $(element).val("");
//                } else {
//                    $(element).val(0);
//                }
//
//            });
        }
    });
    $('.pensao').trigger('change');



    $(".nomeFavorecido,.cpfFavorecido").on("blur", function () {

        var nome = "#favorecidos_" + $(this).attr("id");
        var parent = "#" + $(nome).attr("data-parent");
        if ($(parent).prop("checked")) {
            $(nome).val($(this).val());
        }
    });



    // máscaras
    $(".datemask").mask("99/99/9999");
    $(".cpf").mask("999.999.999-99");
    $("#cep").mask("99999-999", {placeholder: " "});
    $(".tel").brTelMask();
    $(".cnpj_mask").mask("99.999.999/9999-99");
    $(".hora").mask("999:99");

    $('#form_clt').validationEngine();
    // add class do validation engine
    $("#pis").change(function () {
        // verifica se o campo não está vazio 
        if ($("#pis").val() != '') {
            $("#pis").addClass('validate[required,custom[pis]]');    // adiciona classe
        } else {
            $("#pis").removeClass('validate[required,custom[pis]]'); // remove classe
        }
    });

    $("input[type='radio'][name='radio_contribuicao']").click(function () {
        var valor = $(this).val();
        if (valor === 'sim') {
            $("#div_ano_contribuicao").show();
            $("#radio_contribuicao").addClass("validate[required]").removeClass("disabled");
        } else {
            $("#div_ano_contribuicao").hide();
            $("#radio_contribuicao").removeClass("validate[required]").addClass("disable").val(''); // remove a classe
        }
    });
    var valorRadioEstuda = $("input[type='radio'][name='estuda']:checked").val();
    if (valorRadioEstuda === 'sim') {
        $("#termino_em").show();
        $("#termino_em_input").addClass("validate[required]");
    } else {
        $("#termino_em").hide();
    }

    $("input[type='radio'][name='estuda']").click(function () {
        var valor = $(this).val();
        if (valor === 'sim') {
            $("#termino_em").show();
            $("#termino_em_input").addClass("validate[required]");
        } else {
            $("#termino_em").hide();
            $("#termino_em_input").removeClass("validate[required]").val(''); // remove a classe
        }
    });

    $("body").on('change', '#status_admi', function () {
        var valor = $(this).val();
        if (valor === '70') {
            $("#div_data_importacao").show();
        } else {
            $("#div_data_importacao").hide().val('');
        }
    });
    $("#status_admi").trigger('change');

    $("input[type='radio'][name='trabalha_outra_empresa']").change(function () {
        var valor = $('#trabalha_outra_empresa_sim').prop('checked');
        if (valor) {
            $(".div_trabalha_outra_empresa").show();
            $("#outra_empresa").show();
        } else {
            $(".div_trabalha_outra_empresa").hide();
            $("#outra_empresa").hide();
            for (var i = 0; i <= $('#div_dados_outra_empresa > .form-group').length; i++) {
                $("input[name='outra_empresa[" + i + "][salario]'").val("");
                $("input[name='outra_empresa[" + i + "][desconto]'").val("");
                $("input[name='outra_empresa[" + i + "][inicio]'").val("");
                $("input[name='outra_empresa[" + i + "][fim]'").val("");
            }
        }
    });
    $("#trabalha_outra_empresa_sim").trigger('change');

    $("input[type='radio'][name='medica']").change(function () {
        var valor = $('#medica_sim').prop('checked');
        if (valor) {
            $(".div_medica").show();
        } else {
            $(".div_medica").hide();
            $('#id_plano_saude').val('');
        }
    });
    $("#medica_sim").trigger('change');

    $("body").on('change', '#desconto_inss', function () {
        if ($(this).prop('checked')) {
            $(".div_desconto_inss").show();
        } else {
            $(".div_desconto_inss, #div_dados_outra_empresa, .div_trabalha_outra_empresa").hide();
            $("#trabalha_outra_empresa_sim").prop('checked', false);
            $("#tipo_desconto_inss1").prop('checked', false);
            $("#tipo_desconto_inss2").prop('checked', false);
            for (var i = 0; i <= $('#div_dados_outra_empresa > .form-group').length; i++) {
                $("input[name='outra_empresa[" + i + "][salario]'").val("");
                $("input[name='outra_empresa[" + i + "][desconto]'").val("");
                $("input[name='outra_empresa[" + i + "][inicio]'").val("");
                $("input[name='outra_empresa[" + i + "][fim]'").val("");
            }
        }
    });
    $('#desconto_inss').trigger('change');

    $(".del_tp").click(function () {
        $(this).parents(".div_transporte").find(".qtd_class").val("0");
        $(this).parents(".div_transporte").find(".card_class").val("0");
        $(this).parents(".div_transporte").find(".vt_valor").val("0");
        $(this).parents(".div_transporte").find(".linha-vale").val("0");

        var linha1 = $("#vale1").val();
        if (linha1 === "0") {
            //uncheck no vale transporte
            $("#transporte").removeAttr('checked');
            $(".qtd_class").val("0");
            $(".card_class").val("0");
            $(".vt_valor").val("0");
            $(".linha-vale").val("0");
            $(".div_transporte").hide();
        }
        //$(this).parents(".div_transporte").hide();
    });

    $("body").on('change', '#transporte', function () {

        if ($(this).prop('checked')) {
            $(".div_transporte").show();
        } else {
            $(".div_transporte").hide();
//            console.log("xpto");
            for (var i = 1; i <= 5; i++) {
                $("#vale" + i).val(0);
                $("#vt_valor" + i).find('option').removeAttr('disabled').removeAttr('selected');
                $("#vt_valor" + i).val(0);
                $("#vt_qtd" + i).val("");
                $("#vt_card" + i).val("");
            }
        }
    });

    $('#transporte').trigger('change');


    $(".linha-vale").on("change", function () {
        if ($(this).val() != 0) {
            var linha = this;
            //linha = select com todas as linhas
            var linhaVal = $(this).val();
            //linhaVal = numero da linha
            $(this).closest(".div_transporte").next().removeClass("hide");
            $.ajax({
                url: '',
                method: 'POST',
                dataType: 'json',
                data: {method: 'valLinha', linha: linhaVal},
                success: function (data) {
                    var vtVal = $(linha).closest(".div_transporte").find("[id^=vt_valor]");
                    //console.log(vtVal);
                    vtVal.find('option').removeAttr('disabled').removeAttr('selected');
                    vtVal.find('option').each(function () {

                        if ($(this).text() == data) {
                            /*   console.log($(this).text(), data);
                             console.log(vtVal.val(),$(this).val());
                             console.log(vtVal);
                             console.log(this);*/
                            vtVal.val($(this).val());
                            // console.log(vtVal.val($(this).val()));
                        } else {
                            //$(this).attr('disabled', true);
                        }
                    });
                }
            });

        } else {
            var index = $(this).attr("data-index");

            $('.div_transporte').each(function (idx, obj) {
                var objIndex = $(obj).find('.linha-vale').attr("data-index");
                console.log(objIndex);
                if (objIndex >= index) {

                    if (objIndex > index) {
                        $(obj).addClass("hide");
                    }
                    $("#vt_valor" + objIndex).find('option').removeAttr('disabled').removeAttr('selected');
                    $("#vt_valor" + objIndex).val(0);
                    $("#vale" + objIndex).val(0);
                    $("#vt_qtd" + objIndex).val("");
                    $("#vt_card" + objIndex).val("");
                }
            });
        }
        var linha1 = $("#vale1").val();
        if (linha1 === "0") {
            //uncheck no vale transporte
            $("#transporte").removeAttr('checked');
            $(".qtd_class").val("0");
            $(".card_class").val("0");
            $(".vt_valor").val("0");
            $(".linha-vale").val("0");
            $(".div_transporte").hide();
        }
    });


    $('body').on('click', '.add_outro_dependente', function () {
        var n = $('.div_outros_dependentes').length;
        $('.container_outros_dependentes').append('<div class="div_outros_dependentes">' +
                '<div class="form-group">' +
                '<div class="col-sm-4">' +
                '<div class="text-bold">Nome:</div>' +
                '<div class="">' +
                '<input type="text" class="form-control" name="outro_dependente[' + n + '][nome]" id="" value="">' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-2">' +
                '<div class="text-bold">CPF:</div>' +
                '<div class="">' +
                '<input type="text" class="cpf form-control validate[custom[cpf]]" name="outro_dependente[' + n + '][cpf]" id="" value="">' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-3">' +
                '<div class="text-bold">Tipo de Dependente:</div>' +
                '<div class="">' +
                '<select class="form-control" id="" name="outro_dependente[' + n + '][parentesco]">' + $("#tipo_dependente").html() + '</select>' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-2">' +
                '<div class="text-bold">Data de Nascimento:</div>' +
                '<div class="">' +
                '<input type="text" class="dtformat form-control dateMask" name="outro_dependente[' + n + '][data_nasc]" id="" value="">' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-1">' +
                '<div class="text-bold">&nbsp;</div>' +
                '<button type="button" class="del_dados_outros_dependentes btn btn-danger" data-id="1" data-posicao_dependente="7"><i class="fa fa-trash-o"></i></button>' +
                '</div>' +
                '</div>' +
                '<div class="form-group">' +
                '<div class="col-sm-3">' +
                '<div class="input-group">' +
                '<div class="input-group-addon"><input type="checkbox" class="" name="outro_dependente[' + n + '][salario_familia]" id="outro_dependente[' + n + '][salario_familia]" value="1"></div>' +
                '<label class="form-control text-default" for="outro_dependente[' + n + '][salario_familia]">Salário Família</label>' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-3">' +
                '<div class="input-group">' +
                '<div class="input-group-addon"><input type="checkbox" class="" name="outro_dependente[' + n + '][nao_ir]" id="outro_dependente[' + n + '][nao_ir_filho]" value="1"></div>' +
                '<label class="form-control text-default" for="outro_dependente[' + n + '][nao_ir]">Não deduzir no IR</label>' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-3">' +
                '<div class="input-group">' +
                '<div class="input-group-addon"><input type="checkbox" class="" name="outro_dependente[' + n + '][plano_privado_de_saude]" id="outro_dependente[' + n + '][plano_privado_de_saude]" value="1"></div>' +
                '<label class="form-control text-default" for="outro_dependente[' + n + '][plano_privado_de_saude]">Plano Privado de Saúde</label>' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-3">' +
                '<div class="input-group">' +
                '<div class="input-group-addon"><input type="checkbox" class="" name="outro_dependente[' + n + '][incapaz_trab]" id="outro_dependente[' + n + '][incapaz_trab]" value="1"></div>' +
                '<label class="form-control text-default" for="outro_dependente[' + n + '][incapaz_trab]">Incapaz de Trabalhar</label>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<hr>' +
                '</div>');
//    

        $('.div_outros_dependentes').last().find('#tipo_dependente').attr("name", "outro_dependente[" + n + "][tipo_dependente]");

        $('.dtformat').datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            yearRange: '1930:c+1',
            beforeShow: function () {
                setTimeout(function () {
                    $('.ui-datepicker').css('z-index', 5010);
                }, 0);
            }
        });
        $(".datemask").mask("99/99/9999");
        $(".cpf").mask("999.999.999-99");
    });


    $('body').on('click', '#add_dados_outra_empresa', function () {
        var n = $('#div_dados_outra_empresa .dados_outra_empresa').length;
        $('#div_dados_outra_empresa').append(
                $('<div>', {class: "form-group dados_outra_empresa"}).append(
                $('<div>', {class: "col-sm-2"}).append(
                $('<div>', {class: "text-bold", html: "Salário outra empresa:"}),
                $('<div>', {class: ""}).append(
                $('<input>', {type: "text", name: "outra_empresa[" + n + "][salario]", class: "valor form-control"})
                )
                ),
                $('<div>', {class: "col-sm-3"}).append(
                $('<div>', {class: "text-bold", html: "Desconto da outra empresa:"}),
                $('<div>', {class: ""}).append(
                $('<input>', {type: "text", name: "outra_empresa[" + n + "][desconto]", class: "valor form-control"})
                )
                ),
                $('<div>', {class: "col-sm-2"}).append(
                $('<div>', {class: "text-bold", html: "Início:"}),
                $('<div>', {class: ""}).append(
                $('<input>', {type: "text", name: "outra_empresa[" + n + "][inicio]", class: "datainicio data form-control"})
                )
                ),
                $('<div>', {class: "col-sm-2"}).append(
                $('<div>', {class: "text-bold", html: "Fim:"}),
                $('<div>', {class: ""}).append(
                $('<input>', {type: "text", name: "outra_empresa[" + n + "][fim]", class: "datafim data form-control"})
                )
                ),
                $('<div>', {class: "col-sm-2"}).append(
                $('<div>', {class: "text-bold", html: "CNPJ:"}),
                $('<div>', {class: ""}).append(
                $('<input>', {type: "text", name: "outra_empresa[" + n + "][cnpj_outro_vinculo]", class: "form-control cnpj_mask"})
                )
                ),
                $('<div>', {class: "col-sm-1"}).append(
                $('<div>', {class: "text-bold", html: "&nbsp;"}),
                $('<button>', {type: "button", class: "del_dados_empresa btn btn-danger"}).append(
                $('<i>', {class: "fa fa-trash-o"})
                )
                )
                )
                );
        $('#div_dados_outra_empresa').find('.data').datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            yearRange: '1930:c+1',
            beforeShow: function () {
                setTimeout(function () {
                    $('.ui-datepicker').css('z-index', 5010);
                }, 0);
            }
        });

        $('#div_dados_outra_empresa').find('.valor').maskMoney({prefix: 'R$ ', allowNegative: true, thousands: '.', decimal: ','});
        $(".cnpj_mask").mask("99.999.999/9999-99");
    });



    $('body').on('click', '.del_dados_empresa', function () {
        var $this = $(this);
        if ($this.data('id') > 0) {
            bootConfirm('Deseja mesmo excluir?', 'Atenção', function (confirm) {
                if (confirm) {
                    $.post('#', {method: 'excluir_inss', id_inss: $this.data('id')}, function (resp) {
                        bootAlert(resp.msg, 'Atenção', null, resp.status);
                        if (resp.status == 'success') {
                            console.log($this.parent().parent().html());
                            $this.parent().parent().remove();
                        }
                    }, 'json');
                }
            }, 'danger');
        } else {
            $this.parent().parent().remove();
        }
    });

    $('body').on('click', '.del_dados_filhos', function () {
        var t = $(this);
        console.log('Passo 1');
        if (t.data('id') == 1) {
            console.log('Passo 2');
            bootConfirm('Deseja mesmo excluir?', 'Atenção', function (confirm) {
                console.log('Passo 3');
                if (confirm) {
                    console.log('Passo 4');
                    $.post('#', {method: 'exclusao_de_dependente', nome_dep_ajax: t.data('id'), posicao_dependente: t.data('posicao_dependente')}, function (resp) {
                        bootAlert(resp.msg, 'Atenção', null, resp.status);
                        if (resp.status == 'success') {
                            t.parents('.container_filho').remove();
                        }
                    }, 'json');
                }
            }, 'danger');
        } else {
            t.parents('.container_filho').remove();
        }
    });


    $('body').on('click', '.del_dados_outros_dependentes', function () {
        var t = $(this);
//        if (t.data('id') == 1) {
            bootConfirm('Deseja mesmo excluir?', 'Atenção', function (confirm) {
                if (confirm) {
                    $.post('#', {method: 'exclusao_de_outro_dependente', id_outro_dependente: t.data('id')}, function (resp) {
                        console.log(resp);
                        bootAlert(resp.msg, 'Atenção', null, resp.status);
                        if (resp.status == 'success') {
                            t.parents('.div_outros_dependentes').remove();
                        }
                    }, 'json');
                }
            }, 'danger');
//        }

    });

    $('body').on('click', '.nova_selecao_funcao', function () {
        $('#id_curso').val($(this).data('id')).trigger('change');
        $('.modal, .modal-backdrop').remove();
    });

    $('body').on('change', '#id_curso', function () {
        var rh_horario = $(this).data('horario');

        $.post("", {bugger: Math.random(), method: 'horarios', id: $(this).val(), rh_horario: rh_horario}, function (result) {
            $('#div_horario').html(result);
        });
    });


    $('#tipo_contrato').on('change', function () {
        var cod_trab = $(this).val();
        var classificacao = $(this).data('classificacao');
        var lotacao = $(this).data('lotacao');

        $.post("", {method: 'metodo_class_trib', cod_trab: cod_trab, classificacao: classificacao}, function (result) {
            $('#classif_trib_div').html(result);
        });

        $.post("", {method: 'metodo_tipo_lotacao', cod_trab: cod_trab, lotacao: lotacao}, function (result) {
            $('#tipo_lotacao_div').html(result);
        });

    });

    $('#tipo_contrato').trigger('change');


    $('#id_curso').trigger('change');

    $('body').on('change', '.unidade_projeto', function () {
        var $this = $(this);
        if ($this.val() > 0) {
            $.post("", {bugger: Math.random(), method: 'unidades', id_unidade: $this.data('unidade'), id_projeto: $this.val(), ordem: $this.data('ordem')}, function (result) {
                $('#div_unidade_projeto' + $this.data('ordem')).html(result);
            });
        }
    });
    $('.unidade_projeto').trigger('change');

    /**
     * carrega municípios para o campo município de nascimento
     */
//    $('#uf').change(function () {
//        var uf = $('#uf').val();
//        $('#cidade').val('');
//        $('#id_municipio_end').val('');
////        $.post('../busca_cep.php', {uf: uf, municipios: 1}, function (data) {
////
////            $("#cidade").autocomplete({source: data.municipios,
////                change: function (event, ui) {
////                    if (event.type == 'autocompletechange') {
////                        var valor_municipio = ui.item.value.split(')-');
////                        $('#id_municipio_end').val(valor_municipio[0].trim().substring(1, 5));
////                        $('#cidade').val(valor_municipio[1].trim());
////                    }
////                }
////            });
////
////        }, 'json');
//    });

    /**
     * BUSCA CEP
     */
    var cep_atual = $('#cep').val().replace("-", "").replace(".", "");
    var numero_atual = $('#numero').val();
    var complemento_atual = $('#complemento').val();
    var CEP_GLOBAL = $('#cep').val();

    $('body').on('focus', '#cep', function () {
        CEP_GLOBAL = $('#cep').val();
    });

    $('#cep').blur(function () {
        if ($('#cep').val() != '' && $('#cep').val() != undefined && CEP_GLOBAL != $('#cep').val()) {
//            $.post("//viacep.com.br/ws/"+ $('#cep').val().replace('-','') +"/json/?callback=?", function(dados) {
            $.post("http://cep.republicavirtual.com.br/web_cep.php?cep=" + $('#cep').val().replace('-', '') + "&formato=json", function (dados) {
                console.log(dados);
                limpa_formulario_cep();
                $("#endereco").val(dados.logradouro);
                $("#bairro").val(dados.bairro);
                $("#cidade").val(dados.localidade);
                $("#uf").val(dados.uf);
                $("#tipo_de_logradouro").val(dados.tipo_de_logradouro);
            }, 'json');
        }
    });
    function limpa_formulario_cep() {
        // Limpa valores do formulÃ¡rio de cep.
        $("#endereco, #bairro, #cidade, #uf, #tipo_de_logradouro, #complemento, #numero, #municipio_end, #id_municipio_end").val("");
        //$("#ibge").val("");
    }

    $('body').on('change', '#foto', function () {
        if ($(this).prop('checked') == true) {
            $('#arquivo').show();
        } else {
            $('#arquivo').val('').hide();
        }
    });
    $('#foto').trigger('change');

    $(".verificaCpfFunDemitidos").change(function () {
        var cpf = $(this).val();
        $.ajax({
            url: "",
            type: "POST",
            dataType: "json",
            data: {
                method: "verificaCpf",
                cpf: cpf
            },
            success: function (data) {
                if (data.status) {
                    $("#participanteDesativado").css({display: "block"});
                    $(data.dados).each(function (d, i) {
                        $("#part").html(i.nome + " | <b>PROJETO:</b> " + i.projeto + " | <b>MOTIVO:</b> " + i.status + "<br><a href='/intranet/registrodeempregado.php?bol=0&pro=" + i.idprojeto + "&clt=" + i.id + "'>Visualizar Ficha</a>");
                    });

                    $("input[name='Submit']").css({display: "none"});
                    $('html, body').animate({
                        scrollTop: $("#part").offset().top
                    }, 2000).trigger('click');
                }
            }
        });
    });

    /*
     * Função para validar CPF
     * Autor: Leonardo
     * data: 30/04/2014
     * @param {type} field
     * @returns {String}
     */
    var verificaCPF = function (field) {

        var value = field.val();

        value = value.replace('.', '');
        value = value.replace('.', '');
        var cpf = value.replace('-', '');

        if (!VerificaCPF(cpf)) {
            return "CPF inválido";
        }
    };

    /***
     * FEITO POR SINESIO - 24/03/2016 - 656
     */
    $("body").on("change", "input[name='data_entrada']", function () {

        /**
         * RECUPERANDO DATA DE ENTRADA
         */
        var dataEntrada = $(this).val(); /**24/03/2016**/

        /**
         * EXPLODE DE DATA DE ENTRADA
         */
        var explode = dataEntrada.split("/");

        /**
         * PREENCHENDO VARIAVEIS
         */
        var dia = parseInt(explode[0]);
        var mes = parseInt(explode[1]);
        //var mes = parseInt(explode[1]) - 1;
        var ano = parseInt(explode[2]);

        /**
         * OBJETO
         */
        var data = new Date(ano, mes, dia);
        data.setDate(data.getDate() + 90);

        var novaData = str_pad(data.getDate(), 2, '0', 'STR_PAD_LEFT') + "/" + str_pad(data.getMonth(), 2, '0', 'STR_PAD_LEFT') + "/" + data.getFullYear();

        $("#dataFinalExperiencia").html("Termino da Experiência: " + novaData);

    });


    //FECHAR TELA DE CARREGANDO AO TERMINAR DE CARREGAR A PAGINA
    $(window).load(function () {
        $('#carregando').remove();
        $('.modal-backdrop').remove();
    });


    var tipoVerifica = 0;
    /*
     * Desabilita campos, deposito por cheque
     * @returns void
     */
    function desabilita() {

        $("#conta").attr("disabled", true).val('');
        $("#conta_dv").attr("disabled", true).val('');
        $("input[name='tipo_conta'").attr("disabled", true).val('');
        $("#agencia").attr("disabled", true).val('');
        $("#agencia_dv").attr("disabled", true).val('');
        $("#ckb_agencia").attr("disabled", true).val('');
        $('#nome_banco').val('').attr('disabled', true);

    }

    /*
     * Ativa os campos para selecao de banco
     * @return void
     */
    function Ativa() {

        $("#conta").attr("disabled", false);
        $("#conta_dv").attr("disabled", false);
        $("input[name='tipo_conta'").attr("disabled", false);
        $("#agencia").attr("disabled", false);
        $('#nome_banco').attr('disabled', false);
        $('#nome_banco').addClass('validate[required]').validationEngine("validate");
        $("#agencia_dv").attr("disabled", false);
        $("#ckb_agencia").attr("disabled", false);

    }
    /*
     * Habilita os campos para banco pre-carregado
     * @returns void
     */
    function habilita() {
        $("#conta").attr("disabled", false);
        $("#conta_dv").attr("disabled", false);
        $("input[name='tipo_conta'").attr("disabled", false);
        $("#agencia").attr("disabled", false);
        $('#nome_banco').attr('disabled', true);
        $("#agencia_dv").attr("disabled", false);
        $("#ckb_agencia").attr("disabled", false);
    }

    $('#banco').change(function () {
        var $this = $(this);
        var val = $this.val();
        if (val == '9999') {
            $('#nome_banco').prop('disabled', false);
        } else {
            $('#nome_banco').prop('disabled', true);
        }
    });

    $("#banco").trigger("change");



    $('body').on('click', '#estatutario', function () {

        t = $(this);

        if (t.is(':checked')) {

            $('#boxTrabEstatutario').show();
            $('#boxTrabTemp').hide();
            $('#jovem_aprendiz').prop('checked', false);
            $('#trab_temporario').prop('checked', false);

        } else {
            $('#boxTrabEstatutario').hide();
            $('#boxTrabTemp').hide();
        }

    });
    $('body').on('click', '#trab_temporario', function () {

        t = $(this);

        if (t.is(':checked')) {

            $('#boxTrabTemp').show();
            $('#boxTrabEstatutario').hide();
            $('#jovem_aprendiz').prop('checked', false);
            $('#estatutario').prop('checked', false);

        } else {
            $('#boxTrabEstatutario').hide();
            $('#boxTrabTemp').hide();
        }

    });

    $('body').on('click', '#jovem_aprendiz', function () {

        t = $(this);

        if (t.is(':checked')) {
            $('#trab_temporario').prop('checked', false);
            $('#estatutario').prop('checked', false);
            $('#boxTrabTemp').hide();
            $('#boxTrabEstatutario').hide();
        } else {
            $('#boxTrabEstatutario').hide();
            $('#boxTrabTemp').hide();
        }

    });

    if ($('#trab_temporario').is(':checked')) {
        $('#boxTrabEstatutario').hide();
        $('#boxTrabTemp').show();
    } else if ($('#estatutario').is(':checked')) {
        $('#boxTrabEstatutario').show();
        $('#boxTrabTemp').hide();
    } else if ($('#jovem_aprendiz').is(':checked')) {
        $('#boxTrabEstatutario').hide();
        $('#boxTrabTemp').hide();
    } else {
        $('#boxTrabEstatutario').hide();
        $('#boxTrabTemp').hide();
    }

    $('body').on('change', '#uf_nasc', function () {
        $.post('', {method: 'getMunicipioByUf', uf_nasc: $(this).val()}, function (data) {
            $('#municipio_nasc').html(data);
        });
    });

    $('body').on('change', "#forma_contratacao", function () {
        if ($(this).val() == "1") {
            $("#raw").css("display", "none");
            $("#processo_sel").css("display", "block");
            $("#outras_formas").css("display", "none");
            $("#outros_processo").val("");
        } else if ($(this).val() == "4") {
            $("#raw").css("display", "none");
            $("#processo_sel").css("display", "none");
            $("#outras_formas").css("display", "block");
            $("#num_processo_seletivo").val("");
        } else {
            $("#raw").css("display", "block");
            $("#processo_sel").css("display", "none");
            $("#outras_formas").css("display", "none");
            $("#outros_processo").val("");
            $("#num_processo_seletivo").val("");
        }
    });
    $("#forma_contratacao").trigger('change');

    $("#hora_limpa, #hora_composta").on('change', function () {
        if ($(this).val() == 1) {
            $('#quantidade_horas_proporcional').removeClass('hide');
            $('#quantidade_horas').addClass('hide');
        } else {
            $('#quantidade_horas_proporcional').addClass('hide');
            $('#quantidade_horas').removeClass('hide');
        }
    });
//    $("input[name=tipo_quantidade_horas]:checked").trigger('change');

    $("#ad_transferencia_tipo").on('change', function () {
        if ($(this).val() == 1) {
            $('#ad_transferencia_valor_div').removeClass('hide');
        } else {
            $('#ad_transferencia_valor').val('');
            $('#ad_transferencia_valor_div').addClass('hide');
        }
    });
    $("#ad_transferencia_tipo").trigger('change');
});