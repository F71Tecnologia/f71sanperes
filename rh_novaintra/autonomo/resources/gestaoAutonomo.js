 $(document).ready(function () {

    $("#cep").mask("99999-999");
    $("#cpf").mask("999.999.999-99");
    $(".cpfMask").mask("999.999.999-99");
    $("#pis").mask("999.99999.99-9");
    $(".dataMask").mask("99/99/9999");
    $(".telMask").mask("(99) 9999-9999");
    $(".celMask").mask("(99) 99999-9999");
    
    $('body').on('change', '#chk_agencia', function () {
        var t = $(this);
        var chk_banco = t.val();
        
        if (chk_banco == 1) {
            $('#agencia_dv').val('N').prop("readonly", true);;
        } else if (chk_banco == 0) {
            $('#agencia_dv').val('').prop("readonly", false);;
        }
    });
    
    $('body').on('change', '#banco', function () {
        var t = $(this);
        var banco = t.val();
        
        if (banco == '9999') {
            $('#nome_banco').attr('disabled', false);
            $('#agencia').attr('disabled', false);
            $('#agencia_dv').attr('disabled', false);
            $('#conta').attr('disabled', false);
            $('#conta_dv').attr('disabled', false);
            $('#chk_agencia').attr('disabled', false);
            $('#tipo_conta').attr('disabled', false);
        } else if (banco == '0' || banco == '-1') {
            $('#nome_banco').attr('disabled', true);
            $('#agencia').attr('disabled', true);
            $('#agencia_dv').attr('disabled', true);
            $('#conta').attr('disabled', true);
            $('#conta_dv').attr('disabled', true);
            $('#chk_agencia').attr('disabled', true);
            $('#tipo_conta').attr('disabled', true);
        } else {
            $('#nome_banco').attr('disabled', true);
            $('#agencia').attr('disabled', false);
            $('#agencia_dv').attr('disabled', false);
            $('#conta').attr('disabled', false);
            $('#conta_dv').attr('disabled', false);
            $('#chk_agencia').attr('disabled', false);
            $('#tipo_conta').attr('disabled', false);
        }
    });
    
    $(document).on('blur', '.upperCase', function () {
        $(this).val($(this).val().toUpperCase());
    });
    
    $(document).on('blur', '#cpf', function () {
        var cpf = $('#cpf').val().replace('.', '').replace('.', '').replace('-', '');

        if (!validaCpf(cpf) && cpf != '___________') {
            bootAlert('Por favor, insira um cpf válido.',
                    'CPF Inválido!',
                    null,
                    'html');
            $('#cpf').val('');
        }
    });

    $(document).on('blur', '#pis', function () {
        var pis = $('#pis').val().replace('.', '').replace('.', '').replace('-', '');

        if (!ChecaPIS(pis) && pis != '___________') {
            bootAlert('Por favor, insira um pis válido.',
                    'PIS Inválido!',
                    null,
                    'danger');
        }
    });

    $(document).on('click', '.addProjeto', function () {
        var t = $(this);
        var box = t.parent().parent().parent().parent();
        var html = '<div class="margin-top boxProjeto"> <div class="col-sm-4"> <div class="input-group"> <span class="input-group-addon">Projeto</span> <select class="id_projeto form-control" name="id_projeto[]"><option value="-1">« Selecione »</option><option value="1">1 - IABAS - Institucional - SP</option><option value="2">2 - Contrato de Gestão Norte</option><option value="3">3 - Contrato de Gestão Centro</option></select> </div> </div> <div class="col-sm-8"> <div class="input-group"> <span class="input-group-addon">Unidade</span> <select class="id_unidade form-control" name="id_unidade[]"><option value="1">« Selecione »</option></select> <span class="unidade_loading input-group-addon hide"><i class="fa fa-spinner fa-spin"></i></span> <span title="Adicionar Unidade" class="addProjeto pointer input-group-addon"><i class="icon-success fa fa-plus-circle"></i></span><span title="Excluir Unidade" class="remProjeto pointer input-group-addon"><i class="icon-danger fa fa-minus-circle"></i></span></div></div><div style="clear:both"></div></div>';

        box.append(html);
    });

    $(document).on('click', '.remProjeto', function () {
        var t = $(this);
        var box = t.parent().parent().parent();
        box.remove();
    });
    
    $(document).on('click', '.addDependente', function () {
        var t = $(this);
        var box = t.parent().parent().parent().parent();
        var html = '<div class="margin-top boxDepe"> <div class="col-sm-4"> <div class="input-group"> <span class="input-group-addon">Projeto</span> <select class="id_projeto form-control" name="id_projeto[]"><option value="-1">« Selecione »</option><option value="1">1 - IABAS - Institucional - SP</option><option value="2">2 - Contrato de Gestão Norte</option><option value="3">3 - Contrato de Gestão Centro</option></select> </div> </div> <div class="col-sm-8"> <div class="input-group"> <span class="input-group-addon">Unidade</span> <select class="id_unidade form-control" name="id_unidade[]"><option value="1">« Selecione »</option></select> <span class="unidade_loading input-group-addon hide"><i class="fa fa-spinner fa-spin"></i></span> <span title="Adicionar Unidade" class="addProjeto pointer input-group-addon"><i class="icon-success fa fa-plus-circle"></i></span><span title="Excluir Unidade" class="remProjeto pointer input-group-addon"><i class="icon-danger fa fa-minus-circle"></i></span></div></div><div style="clear:both"></div></div>';

        box.append(html);
    });

    $(document).on('click', '.remDependente', function () {
        var t = $(this);
        var box = t.parent().parent().parent();
        box.remove();
    });

    $('body').on('change', '.id_projeto', function () {
        var t = $(this);
        var val_projeto = t.val();

        var load = t.parent().parent().next().find('.unidade_loading');
        load.removeClass('hide');
        $.post('../../methods.php', {method: 'carregaUnidades', projeto: val_projeto}, function (data) {
            t.parent().parent().next().find('.id_unidade').html(data);
            load.addClass('hide');
        });
    });

    function limpa_formulário_cep() {
        // Limpa valores do formulário de cep.
        $("#endereco").val("");
        $("#bairro").val("");
        $("#cidade").val("");
        $("#uf").val("");
    }

    //Quando o campo cep perde o foco.
    $("body").on('blur', '#cep', function () {

        //Nova variável "cep" somente com dígitos.
        var cep = $(this).val().replace(/\D/g, '');

        //Verifica se campo cep possui valor informado.
        if (cep != "") {

            //Expressão regular para validar o CEP.
            var validacep = /^[0-9]{8}$/;

            //Valida o formato do CEP.
            if (validacep.test(cep)) {

                //Preenche os campos com "..." enquanto consulta webservice.
                $("#endereco").val("...");
                $("#bairro").val("...");
                $("#cidade").val("...");
                $("#uf").val("...");

                //Consulta o webservice viacep.com.br/
                $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {

                    if (!("erro" in dados)) {
                        //Atualiza os campos com os valores da consulta.
                        $("#endereco").val(dados.logradouro);
                        $("#bairro").val(dados.bairro);
                        $("#cidade").val(dados.localidade);
                        $("#uf").val(dados.uf);
                    } //end if.
                    else {
                        //CEP pesquisado não foi encontrado.
                        limpa_formulário_cep();
                        alert("CEP não encontrado.");
                    }
                });
            } //end if.
            else {
                //cep é inválido.
                limpa_formulário_cep();
                alert("Formato de CEP inválido.");
            }
        } //end if.
        else {
            //cep sem valor, limpa formulário.
            limpa_formulário_cep();
        }
    });

    $('#subgrupo_loading').removeClass('hide');
    var grupo = $("#grupo").val();
    $.post("../../finan/actions/action.saida.php", {action: "load_subgrupo", opt: "-- Selecione --", grupo: grupo}, function (subgrupos) {
        $('#subgrupo_loading').remove();
        $('#subgrupo').html(subgrupos);
    });

    $('body').on('change', '#subgrupo', function () {
        $('#tipo_loading').removeClass('hide');
        var subgrupo = $("#subgrupo").val();
        $.post("../../finan/actions/action.saida.php", {action: "load_tipo", opt: "-- Selecione --", subgrupo: subgrupo}, function (tipos) {
            $('#tipo_loading').addClass('hide');
            $('#tipo').html(tipos);
        });
    });

});