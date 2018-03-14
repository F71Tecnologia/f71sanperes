$(function () {
    // MÁSCARAS
    $('input[name^="horarios_alt"][type="text"],[name^="entrada_1"],[name^="entrada_2"],[name^="saida_1"],[name^="saida_2"]').mask("99:99");
    $('form').find('[maxlength="4"]').unmask().mask("?9999",{placeholder:""});

    // VALIDATE ENGINE
    $("#form1").validationEngine({promptPosition: "topRight"});
    
    // BOTÃO DE VOLTAR PÁGINA
    $("#voltar").click(function () {
        $("#form1").attr('action', 'index.php').find(':input').each(function () {if ($(this).is(':visible') && $(this).val() != 'Voltar') { $(this).prop('disabled', true)}});
        $("#form1").submit();
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
    
    // habilita/desabilita horas noturnas
    $(".fieldsets").on('change', '.adicional_noturno', function () {
        var noturno = Number($(this).val());
        if (noturno == 1) {
            $('.horas_noturnas').removeClass("hide").show();
            $('input[id="horas_noturnas"').addClass("validate[required,custom[onlyNumberSp]]");
            $('select[id="porcentagem_adicional"]').addClass("validate[required,custom[select]]");
        } else {
            $('.horas_noturnas').addClass("hide").hide();
            $('input[id="horas_noturnas"').val("").unmask().mask("?9999",{placeholder:""}).removeClass("validate[required,custom[onlyNumberSp]]");
            $('select[id="porcentagem_adicional"]').val("-1").removeClass("validate[required,custom[select]]");
        }
    });
    
    // habilita/desabilita flexibilidade
    $(".fieldsets").on('change', '.flexibilidade', function () {
        var flexibilidade = Number($(this).val());
        if (flexibilidade == 1) {
            $('.variavel').prop('disabled', false);
        } else {
            // DESABILITA CHECKBOXES DE FLEXIBILIDADE
            $('.variavel').prop('disabled', true).prop('checked', false);
            $('label[for^="horarios_alt"]').each(function () {
                var dataDay = Number($(this).attr('data-day'));
                var data = new Date('2017', '10', 5+dataDay).toLocaleString([],{weekday: 'long'}).replace(/(\w+)\-(\w+)/, "$1");
                $(this).text(data.charAt(0).toUpperCase() + data.slice(1)+":");
            });
        }
    });
    
    // TRATA MARCAÇÃO DE DIAS VARIÁVEIS
    $(".fieldsets").on('change', '.variavel, .IntervVariavel, .folga_new', function () {
        var dataDay = Number($(this).parent('div').parent('div').attr('data-day'));
        var data = new Date('2017', '10', 5+dataDay).toLocaleString([],{weekday: 'long'}).replace(/(\w+)\-(\w+)/, "$1");
        if ($(this).hasClass("variavel") && $(this).is(":checked")) {
            // REABILITA CAMPOS DE HORÁRIO
            $(this).parent('div').parent('div').find('.form-control').prop('disabled', false);
            // ALTERA LABEL DO DIA
            $(this).parent('div').parent('div').find('label[for^="horarios_alt"]').text("Variável:");
            // DESMARCA FOLGA NOVA
            $('input[name^="horarios_alt['+dataDay+'][folga]"]').prop('checked', false);
            // DESMARCA FOLGA ANTIGA
            $('[name^="folgaOld"][data-day="'+dataDay+'"]').prop('checked', false);
        }
        else if ($(this).hasClass("variavel") && $(this).is(":not(:checked)")) {
            // ALTERA LABEL DO DIA
            $(this).parent('div').parent('div').find('label[for^="horarios_alt"]').text(data.charAt(0).toUpperCase() + data.slice(1)+":");
        }
        else if ($(this).hasClass("folga_new") && $(this).is(":checked")) {
            // DESABILITA CAMPOS DE HORÁRIO
            $(this).parent('div').parent('div').find('.form-control').prop('disabled', true);
            // ALTERA LABEL DO DIA
            $(this).parent('div').parent('div').find('label[for^="horarios_alt"]').text(data.charAt(0).toUpperCase() + data.slice(1)+":");
            // MARCA FOLGA ANTIGA
            $('[name^="folgaOld"][data-day="'+dataDay+'"]').prop('checked', true);
            // DESMARCA PLANTONISTA
            $('input[name^="plantonista"]').prop('checked', false);
            // DESMARCA DIA VARIÁVEL
            $('input[name^="horarios_alt['+dataDay+'][variavel]"]').prop('checked', false);
            // DESMARCA INTERVALO VARIÁVEL
            $('input[name^="horarios_alt['+dataDay+'][tpInterv]"]').prop('checked', false);
        }
        else if (($(this).hasClass("IntervVariavel") && $(this).is(":checked")) || ($(this).hasClass("folga_new") && $(this).is(":not(:checked)"))) {
            // DESMARCA FOLGA NOVA
            $('input[name^="horarios_alt['+dataDay+'][folga]"]').prop('checked', false);
            // REABILITA CAMPOS DE HORÁRIO
            $(this).parent('div').parent('div').find('.form-control').prop('disabled', false);
            // DESMARCA FOLGA ANTIGA
            $('[name^="folgaOld"][data-day="'+dataDay+'"]').prop('checked', false);
        }
    });
    $('.folga_new').trigger('change');
    
    $(".fieldsets").on('change', '.folga_old', function () {
        var dataDay = Number($(this).attr('data-day'));
        var data = new Date('2017', '10', 5+dataDay).toLocaleString([],{weekday: 'long'}).replace(/(\w+)\-(\w+)/, "$1");
        if ($(this).is(":checked")) {
            // DESABILITA CAMPOS DE HORÁRIO
            $('div[data-day="'+dataDay+'"]').find('.form-control').prop('disabled', true);
            // MARCA FOLGA NOVA
            $('div[data-day="'+dataDay+'"]').find('input[type="checkbox"]').prop('checked', true);
            // DESMARCA INTERVALO VARIÁVEL
            $('input[name^="horarios_alt['+dataDay+'][tpInterv]"]').prop('checked', false);
            // SE FOR PLANTONISTA...
            if (dataDay == 8) {
                // DESMARCA FOLGAS ANTIGAS
                $('input[name^="folgaOld"]').prop('checked', false);
                
                $('input[type="checkbox"]').each(function () {
                    if ($(this).hasClass("folga_new")) {
                        // DESMARCA FOLGAS NOVAS
                        $(this).prop('checked', false);
                        // REABILITA CAMPOS DE HORÁRIOS
                        $('.form-control').prop('disabled', false);
                    }
                });
            }
            else {
                // DESMARCA PLANTONISTA
                $('input[name^="plantonista"]').prop('checked', false);
                // ALTERA LABEL DO DIA
                $('label[for^="horarios_alt"][data-day="'+dataDay+'"]').text(data.charAt(0).toUpperCase() + data.slice(1)+":");
                // DESMARCA DIA VARIÁVEL
                $('input[name^="horarios_alt['+dataDay+'][variavel]"]').prop('checked', false);
            }
        }
        else {
            // REABILITA CAMPOS DE HORÁRIOS
            $('div[data-day="'+dataDay+'"]').find('.form-control').prop('disabled', false);
            // DESMARCA FOLGA NOVA
            $('div[data-day="'+dataDay+'"]').find('input[type="checkbox"]').prop('checked', false);
            
        }
    });
});