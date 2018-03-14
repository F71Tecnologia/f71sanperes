$(document).ready(function () {

    $("#form-lancamento").validationEngine();


    $(".money").maskMoney({prefix: '', allowNegative: true, thousands: '.', decimal: ','});

    /*
     * remove a classe hidden e oculta os steps. serve para não mostrar todos os
     * steps ao carregar a tela e melhora a animação jquery
     */
    $(".step").hide().removeClass("hidden");
    $("#step-1").show(); // exibe apenas o #step-1
    
    // oculta alertas
    $(".alerta-dobrada").hide(); 

// -----------------------------------------------------------------------------
// botoes de navegacao ---------------------------------------------------------
    // botao cancelar
    $(".cancel").click(function () {
        window.history.back();
    });
 
    // botao prosseguir (o sim do step-1 tbm)
    $(".next").click(function () {
        var valida = $("#form-lancamento").validationEngine('validate');
        if (!valida) {
            return valida;
        }
        var next = parseInt($(this).data('next-step')); // recebe o data-atribute next-step
        var back = next - 1; // calcula o back
        $("#step-" + back).slideUp('slow'); // oculta o step anterior
        $("#step-" + next).slideDown('slow'); // exibe o proximo step
        $(".li-step").removeClass("active");
        $("#li-step-" + next).addClass("active");
    });

    // botao voltar
    $(".back").click(function () {
        var back = parseInt($(this).data('back-step')); // recebe o data-atribute next-step
        var next = back + 1; // calcula o back
        $("#step-" + back).slideDown('slow'); // exibe o proximo step
        $("#step-" + next).slideUp('slow'); // oculta o step anterior
        $(".li-step").removeClass("active");
        $("#li-step-" + back).addClass("active");
    });
// -----------------------------------------------------------------------------
 
// -----------------------------------------------------------------------------
// acoes ao mudar inputs ------------------------------------------------------- 
 
    // exibe o período aquisitivo no step-3 e step-4
    $("input[name=periodo_aquisitivo]").click(function () {
        var val = $(this).val();
        var datas = val.split("/");
        var data_ini = datas[0];
        var data_fim = datas[1];

        // coloca as datas no parágrafo
        var p_aquisitivo = converteData(data_ini, '-') + " à " + converteData(data_fim, '-');
        $("#p_aquisitivo").html(p_aquisitivo);
        $("#tb-aquisitivo").html(p_aquisitivo);

        // define data de inicio das ferias como a data fim do periodo aquisitivo
        $("#data_ini").val(converteData(data_fim, '-'));
    });
    
    // verifica se férias é dobrada e exibe alerta
    $("#data_ini").change(function(){
        var dobradas = $("#ferias_dobradas").val();
        var data_ini = $(this).val();
        var periodo_aquisitivo = $("input[name=periodo_aquisitivo]").val();
        $.post('ferias_methods.php',{dobradas:dobradas,data_ini:data_ini,periodo_aquisitivo:periodo_aquisitivo,method:'calc_dobrado'},function(data){
            console.log(data.periodo_consessivo.fim);
            var periodo_concessivo_fim = converteData(data.periodo_consessivo.fim,'-');
            console.log(periodo_concessivo_fim);
            if(data.status === true){
                $(".alerta-dobrada").slideUp('slow');
                
            }else{
                $(".ini-ferias-dobro").html(periodo_concessivo_fim);
                $(".alerta-dobrada").slideDown('slow');
            }
        },'json');
        
    });
// -----------------------------------------------------------------------------

// -----------------------------------------------------------------------------
// ajax quando muda step -------------------------------------------------------
    $(".next[data-next-step=3]").click(function () {
        var id_clt = $("#id_clt").val();
        var id_projeto = $("#id_projeto").val();
        var periodo_aquisitivo = $("#input[name=periodo_aquisitivo]").val();

        $.ajax({
            url: 'ferias_methods.php',
            type: 'post',
            data: {method: 'calc_data', id_clt: id_clt, id_projeto: id_projeto, periodo_aquisitivo: periodo_aquisitivo},
            dataType: 'json',
            success: function (data) {

                // insere valores nos input hidden
                $("#direito_dias").val(data.direito_dias);
                $("#faltas").val(data.faltas);
                $("#faltas_real").val(data.faltas_real);
                $("#update_movimentos_clt").val(data.update_movimentos_clt);

                if(data.faltas > 0 && data.faltas != null && data.faltas != ''){
                    $("#tem-faltas").removeClass("hidden");
                    $("input[name=despreza_faltas]").removeAttr('disabled');
                    $("#qtd-faltas").html(data.faltas);
                }

                // monta select de dias
                var html = "\n";
                for (var a = 1; a <= data.qnt_dias; a++) {
                    var selected = ((a == data.qnt_dias) ? "selected" : "");
                    html += "<option value=\"" + a + "\" " + selected + ">" + a + "</option>\n";
                }
                $("#dias").html(html);
            }
        });

    });

    $(".next[data-next-step=4]").click(function () {
        var id_clt = $("#id_clt").val();
        var id_projeto = $("#id_projeto").val();
        var id_regiao = $("#id_regiao").val();
        var periodo_aquisitivo = $("input[name=periodo_aquisitivo]").val();
        var direito_dias = $("#direito_dias").val();
        var faltas = $("#faltas").val();
        var faltas_real = $("#faltas_real").val();
        var update_movimentos_clt = $("#update_movimentos_clt").val();
        var dias = $("#dias").val();
        var data_ini = $("#data_ini").val();

        $("#tb-dias").html(dias);

        $.ajax({
            url: 'ferias_methods.php',
            type: 'post',
            data: {
                method: 'calc_ferias',
                id_clt: id_clt,
                id_projeto: id_projeto,
                periodo_aquisitivo: periodo_aquisitivo,
                direito_dias: direito_dias,
                faltas: faltas,
                faltas_real: faltas_real,
                update_movimentos_clt: update_movimentos_clt,
                dias: dias,
                data_ini:data_ini,
                id_regiao: id_regiao
            },
            dataType: 'json',
            success: function (data) {
                
//                $.each(data,function(){
//                    console.log(this);
//                });
                
                // remove class hidden e exibe o texto (ferias em dobro)
                if (data.verifica_dobrado <= data.data_inicio) {
                    $(".alerta-dobrada").slideUp('slow');
                }
                
                // remove class hidden e exibir as faltas
                if (data.faltas != '') {
                    $("#tb-faltas").removeClass("hidden");
                    $("#tb-faltas").html(data.faltas);
                }
                
                $("#tb-periodo-ferias").html(data.data_inicioT+" à "+data.data_fimT);
                $("#tb-retorno").html(data.data_retornoT);
                $("#tb-dias").html(data.quantidade_dias);
                
                $("#tb-salario").html(data.salarioT);
                $("#tb-salario-variavel").html(data.salario_variavelT);
                $("#tb-1-3-salario").html(data.um_tercoT);
                $("#tb-remuneracao").html(data.total_remuneracoesT);
                $("#tb-inss").html(data.inssT);
                $("#tb-irrf").html(data.irT);
                $("#tb-pensao").html(data.pensao_alimenticiaT);
                $("#tb-desconto").html(data.total_descontosT);
                $("#tb-liquido").html(data.total_liquidoT);
                
                if(data.dias_abono_pecuniario > 0) {
                    $("#tr-abono").removeClass('hidden');
                    $("#tb-dias-abono").html(data.dias_abono_pecuniario);
                    $("#tb-abono-pecuniario").html(data.dias_abono_pecuniario);
                    $("#tb-1-3-pecuniario").html(data.dias_abono_pecuniario);
                }
                                
            }
        });

    });
});