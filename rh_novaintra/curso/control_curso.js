$(function () {
    // MÁSCARAS
    $(".decimal").maskMoney({allowNegative: false, thousands: '.', decimal: ','});
    $("#inicio, #termino").mask("99/99/9999");
    $('form').find('[maxlength="4"]').unmask().mask("?9999",{placeholder:""});

    // AUTOCOMPLETE
    $("#cbo").autocomplete("lista_cbo.php", {
        width: 600,
        matchContains: false,
        minChars: 3,
        selectFirst: false
    });

    // VALIDATE ENGINE
    $("#form1").validationEngine({promptPosition: "topRight"});
    
    // BOTÃO DE VOLTAR PÁGINA
    $("#voltar").click(function () {
        $("#form1").attr('action', 'index.php').find(':input').each(function () {if ($(this).is(':visible') && $(this).val() != 'Voltar') { $(this).prop('disabled', true)}});
        $("#form1").submit();
    });
                
    // RESETA PARÂMETROS DA ALTERAÇÃO SALARIAL NA ABERTURA DO MODAL
    $(".bt-image").on("click", function() {
        $("#erro2").html("");
        $("#salario_novo").val("");
        $("#diferenca").html("");
        $("#motivo").val("");
        $('#textSuccess').html("");
    });
    
    // CÁLCULO DE DIFERENÇA SALARIAL
    $(".fa-calculator").click(function() {
        var antigo = $('#salario_antigo').val();
        var novo = $('#salario_novo').val().replace(/\./g, '').replace(',', '.');
        var diferenca = parseFloat(novo - antigo).toFixed(2).replace('.', ',').replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.");
        var difere = parseFloat(novo - antigo).toFixed(2);
        if (difere > 0) {
            $("#erro2").html("");
        }
        else {
            $("#erro2").html('O Novo Salário deve ser maior que o Salário Antigo');
        }
        $("#diferenca").html(diferenca);
        $("#difere").val(difere);
        $("#salario_new").val(novo);
    });
    
    // CONFIRMA ALTERAÇÃO SALARIAL
    $("#altera_salario").click(function() {
        var data = $("#form2 :input").serialize();
        var novo = parseFloat($('#salario_novo').val().replace(/\./g, '').replace(',', '.')+0).toFixed(2);
        var salarioNew = parseFloat($('#salario_new').val());
        var diferenca = parseFloat($("#diferenca").html().replace(/\./g, '').replace(',', '.')+0).toFixed(2);
        var diferencaCheck = parseFloat($("#diferenca").html());
        if (novo == 0) {
            $("#erro2").html('Preencha o Salário Novo');
        }
        else if (diferenca == 0 && diferencaCheck != '' && novo >= 0.01) {
            $("#erro2").html('Calcule a Diferença');
        }
        else if (salarioNew != novo) {
            $("#erro2").html('Os Salário Novo foi alterado e não foi calculada a diferença');
        }
        else if (diferenca <= 0) {
            $("#erro2").html('O Novo Salário deve ser maior que o Salário Antigo');
        }
        else {
            $.post('control_curso.php?method=alteraSalario&' + data, null, function(data) {
                if(data.status == 0) {
                    $("#erro2").html(data.erro);
                }
                else if(data.status == 1) {
                    $('#textVal').html(data.valor);
                    $('#textSuccess').html("(salário alterado com sucesso)");
                    $('#salarioAntigo').html(data.valor);
                    $('#salario_antigo').val(data.valor.replace('R$ ', '').replace(/\./g, '').replace(',', '.'));
                    $('#salario').val(data.valor.replace('R$ ', '').replace(/\./g, '').replace(',', '.'));
                    $('#box_salario').modal('toggle');
                }
            },'json');
        }
    });
    
    // EXIBE DESCRIÇÃO DA JORNADA SE JORNADA FOR TIPO 9
    $(".fieldsets").on('change', '[name^="tpJornada"]', function () {
        var dscTpJorn = $(this).parent().parent().next();
        var tpJornada = $(this).val();
        if (tpJornada == 9) {
            dscTpJorn.removeClass("hide").show();
            dscTpJorn.find('input').addClass("validate[required]")
        } else {
            $('.div_jornada').addClass("hide").hide();
            dscTpJorn.find('input').removeClass("validate[required]").val("");
        }
    });
    
    // EXIBE INSALUBRIDADE
    $("#insal").click(function () {
        if($(this).is(':checked')){
            $(".insalubridade").removeClass("hide").show();
        }
        else {
            $(".insalubridade").addClass("hide").hide();
            $("#tipo_insalubridade").val("-1");
            $("#qnt_salminimo_insalu").val("").unmask().mask("?9999",{placeholder:""});
        }
    });
    
    // EXIBE PENOSIDADE
    $("#penos").click(function () {
        if($(this).is(':checked')){
            $(".penosidade").removeClass("hide").show();
        }
        else {
            $(".penosidade").addClass("hide").hide();
            $("#penosidade").val("-1");
        }
    });
    
    // TRATA UNIDADE DE SALÁRIO
    $("#undSalFixo").on('change', function () {
        var undSalFixo = Number($(this).val());
        if(undSalFixo == 6 || undSalFixo == 7){
            $('input[name="dscSalVar"]').addClass("validate[required]");
        }
        else {
            $('input[name="dscSalVar"]').removeClass("validate[required]");
        }
    });
    
    // habilita/desabilita campos de acordo com tipo de contratação
    $("#tipo").on('change', function () {
        var tipo = Number($(this).val());
        if (tipo == 1) {
            var hidden = '.hidden2,.hidden3';
            var display = '.display2,.display3';
            $('.horario').addClass("hide").hide();
        }
        else if (tipo == 2) {
            var hidden = '.hidden1,.hidden3';
            var display = '.display1,.display3';
            $('.horario').removeClass("hide").show();
        }
        else if (tipo == 3) {
            var hidden = '.hidden1,.hidden2';
            var display = '.display1,.display2';
            $('.horario').addClass("hide").hide();
        }
        $(display).removeClass("hide").show();
        $(hidden).find(':input').prop('disabled', false);
        $('.hidden'+tipo).addClass("hide").hide().find(':input').prop('value', function() { if ($(this).is(':radio')) { $("input:radio[name="+$(this).attr("name")+"]:first").prop('checked', true); } else if ($(this).is('select')) { $(this).val($("option:first").val()); } else if ($(this).is(':checkbox')) { $("input:checkbox[name="+$(this).attr("name")+"]").prop('checked', false);  } else if ($(this).is(':not(:checkbox)')) { return "";  }}).prop('disabled', true);
    });
    
    // habilita/desabilita Adicional por cargo de confiança
    $('input[name="tipo_ad_cargo_confianca"]').on('change', function () {
        var confianca = Number($(this).val());
        $('input[name="tipo_ad_cargo_confianca"]').parent().removeClass("text-bold");
        $(this).parent().addClass("text-bold");
        if (confianca == 0) {
            $('#valor_ad_cargo_confianca').val("").addClass("hide").hide().removeClass("validate[required]");
            $('#percentual_ad_cargo_confianca').val("").addClass("hide").hide().removeClass("validate[required]");
        } else if (confianca == 1) {
            $('#valor_ad_cargo_confianca').removeClass("hide").show().addClass("validate[required]");
            $('#percentual_ad_cargo_confianca').val("").addClass("hide").hide().removeClass("validate[required]");
        }
        else if (confianca == 2) {
            $('#percentual_ad_cargo_confianca').removeClass("hide").show().addClass("validate[required]");
            $('#valor_ad_cargo_confianca').val("").addClass("hide").hide().removeClass("validate[required]");
        }
    });
    
    // habilita/desabilita função para horista
    $('#horista_plantonista').on('change', function () {
        if ($(this).val() == 1) {
            $('.horista_plantonista').removeClass("hide").show().find(':input').addClass("validate[required]");
            $('#salario_label').html("Salário/Hora:");
        } else {
            $('.horista_plantonista').addClass("hide").hide().find(':input').val("").removeClass("validate[required]");
            $('#salario_label').html("Salário:");
        }
    });
});