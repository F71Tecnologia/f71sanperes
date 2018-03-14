<?php
/*
 * rescisao_lote_finalizado.php
 * 
 * 00-00-0000
 * 
 * Rotina para efetivação das rescisões de lote finalizadas
 * 
 * Versão: 3.0.1829 - 28/08/2015 - Jacques - Implementação de todos os campos da provisão de gastos
 * Versão: 3.0.1831 - 28/08/2015 - Jacques - Adicionado título ao relatório
 * 
 * @author Não definido
 * 
 */

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include "../../wfunction.php";
include "../../classes_permissoes/acoes.class.php";
include "../../classes/FolhaClass.php";
include "../../classes/calculos.php";

$folha = new folha();
$calculos = new calculos();
$usuario = carregaUsuario();



$id_recisao_lote = (int)$_REQUEST['id'];

$sQuery = "
        SELECT 
            s.codigo AS codigo, 
            s.especifica,
            r.id_recisao,
            r.id_clt,
            r.nome,
            r.id_regiao,
            r.id_projeto,
            r.id_curso,
            r.data_adm,
            r.data_demi,
            r.data_proc,
            r.dias_saldo,
            r.um_ano,
            r.meses_ativo,
            r.motivo,
            r.fator,
            r.aviso,
            r.aviso_valor,
            r.avos_dt,
            r.avos_fp,
            r.dias_aviso,
            r.data_aviso,
            r.data_fim_aviso,
            r.fgts8,
            r.fgts40,
            r.fgts_anterior,
            r.fgts_cod,
            r.fgts_saque,
            r.sal_base,
            r.saldo_salario,
            r.inss_ss,
            r.ir_ss,
            r.terceiro_ss,
            r.previdencia_ss,
            r.dt_salario,
            r.inss_dt,
            r.ir_dt,
            r.previdencia_dt,
            r.ferias_vencidas,
            r.umterco_fv,
            r.ferias_pr,
            r.umterco_fp,
            r.inss_ferias,
            r.ir_ferias,
            r.sal_familia,
            r.to_sal_fami,
            r.ad_noturno,
            r.adiantamento,
            r.insalubridade,
            r.ajuda_custo,
            r.vale_refeicao,
            r.debito_vale_refeicao,
            r.a480,
            r.a479,
            r.a477,
            r.comissao,
            r.gratificacao,
            r.extra,
            r.outros,
            r.movimentos,
            r.valor_movimentos,
            r.total_rendimento,
            r.total_deducao,
            r.total_liquido,
            r.arredondamento_positivo,
            r.devolucao,
            r.faltas,
            r.valor_faltas,
            r.user,
            r.folha,
            r.status,
            r.adicional_noturno,
            r.dsr,
            r.desc_auxilio_distancia,
            r.um_terco_ferias_dobro,
            r.fv_dobro,
            r.aux_distancia,
            r.reembolso_vale_refeicao,
            r.periculosidade,
            r.desconto_vale_alimentacao,
            r.diferenca_salarial,
            r.ad_noturno_plantao,
            r.desconto,
            r.desc_vale_transporte,
            r.pensao_alimenticia_15,
            r.pensao_alimenticia_20,
            r.pensao_alimenticia_30,
            r.lei_12_506,
            r.ferias_aviso_indenizado,
            r.umterco_ferias_aviso_indenizado,
            r.adiantamento_13,
            r.fp_data_ini,
            r.fp_data_fim,
            r.fv_data_ini,
            r.fv_data_fim,
            r.qnt_dependente_salfamilia,
            r.base_inss_ss,
            r.percentual_inss_ss,
            r.base_irrf_ss,
            r.percentual_irrf_ss,
            r.parcela_deducao_irrf_ss,
            r.qnt_dependente_irrf_ss,
            r.valor_ddir_ss,
            r.base_fgts_ss,
            r.base_inss_13,
            r.percentual_inss_13,
            r.base_irrf_13,
            r.percentual_irrf_13,
            r.parcela_deducao_irrf_13,
            r.base_fgts_13,
            r.qnt_dependente_irrf_13,
            r.valor_ddir_13,
            r.salario_outra_empresa,
            r.desconto_inss_outra_empresa,
            r.vinculo_id_rescisao,
            r.rescisao_complementar,
            r.recisao_provisao_de_calculo,
            r.id_recisao_lote,
            r.reintegracao
        
        FROM rh_recisao r LEFT JOIN rh_clt AS c ON c.id_clt = r.id_clt
                          LEFT JOIN rhstatus AS s ON s.codigo = c.status
        WHERE r.id_recisao_lote = {$id_recisao_lote}
        AND r.status = 1
         ";
                                
$rsRescisao = mysql_query($sQuery) or die(mysql_error());

$sQuery = 
        "
        SELECT 
            C.codigo, 
            C.especifica 
        FROM rh_recisao AS A
            LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
            LEFT JOIN rhstatus AS C ON(B.`status` = C.codigo)
        WHERE 
            A.recisao_provisao_de_calculo = 1 
            AND A.id_recisao_lote = {$id_recisao_lote}
        GROUP BY B.`status`

        UNION ALL

        SELECT 
            C.codigo, 
            C.especifica 
        FROM rh_recisao_provisao_de_gastos AS A
            LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
            LEFT JOIN rhstatus AS C ON(B.`status` = C.codigo)
        WHERE 
            A.recisao_provisao_de_calculo = 1 
            AND A.id_recisao_lote = {$id_recisao_lote}
            AND A.id_clt NOT IN 
                    (
                    SELECT id_clt 
                    FROM rh_recisao 
                    WHERE 
                        recisao_provisao_de_calculo = 1 
                        AND id_recisao_lote = {$id_recisao_lote}
                    )
        GROUP BY B.`status`

        ";

$status_array = array();
$nome_status_array = array();
$rsStatus = mysql_query($sQuery);

while ($linhas = mysql_fetch_array($rsStatus)) {
    $status_array[] = $linhas["codigo"];
    $nome_status_array[$linhas["codigo"]] = $linhas["especifica"];
}

                                
    
?>
<html>
    <head>
        <title>:: Intranet :: Previsão de Gasto</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="../../js/ramon.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine_2.6.2.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>

        <script>
            $(function () {

                $("#form").validationEngine();

                $("#dataDemi").datepicker();
                $("#dataAviso").datepicker();

                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function (data) {
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");

                /****************************FILTRO DE FUNÇÃO************************************/
                $("body").on("click", "#filtro_funcao", function () {
                    
                    // desmarca todos os CLTs
                    $('#id_clt_todos').attr("checked", false);
                    $('.clts').attr("checked", false);
                    
                    $("#checkboxes").css('display', 'none'); // oculta as funcoes
                    
                    $("#tbRelatorio tbody tr").hide(); // esconde todas as linhas dos CLTs
                    $('.checkFuncao:checked').each(function () {
                        var funcao = $(this).val();
                        $("#tbRelatorio tbody tr[data-curso='" + funcao + "']").show(); // exibe os CLTs pro funcao
                    });
                });

                $("body").on('change', '.tudo', function () {
                    console.log('aloha');
                    if ($(this).is(":checked")) {
                        $(".checkFuncao").attr("checked", true);
                    } else {
                        $(".checkFuncao").attr("checked", false);
                    }
                });

                /****************************FILTRO DE FUNÇÃO************************************/

                $("body").on("click", ".calcula_multa", function () {
                    var clt = $(this).data("key");
                    var nome = $(this).data("nome");
                    var html = "";
                    var ano = 0;
                    var tamanho = 260;
                    var tamanhoNovo = 0;

                    $.ajax({
                        type: "post",
                        dataType: "json",
                        data: {
                            clt: clt,
                            method: "soma_fgts"
                        },
                        success: function (data) {
                            var total_anos = 0;
                            $.each(data, function (k, v) {
                                var qntAnos = Object.keys(data).length;
                                tamanhoNovo = tamanho * qntAnos;
                                html += "<div class='lista_fgts'>";

                                if (ano != k) {
                                    ano = k;
                                    var total = 0;
                                    html += "<h3>" + k + "</h3>";
                                    $.each(v, function (mes, tipo) {
                                        $.each(tipo, function (k, valor) {
                                            if (k == "normal") {
                                                html += "<p>" + mes + "/" + ano + " - " + valor + "</p>";
                                            } else {
                                                html += "<p>" + mes + "/" + ano + " - " + valor + " (13°)" + "</p>";
                                            }
                                            total = total + parseFloat(valor);
                                            total_anos += parseFloat(valor);
                                        });
                                    });
                                    html += "<h2>" + total.toFixed(2) + "</h2>";
                                }
                                html += "</div>";
                            });

                            html += "<div id='total_anos'><p><span>Total: </span>" + total_anos.toFixed(2) + "</p><p><span>Valor Multa FGTS 50%: </span>" + (total_anos * 0.50).toFixed(2) + "</p></div>";
                            $("#fgts_folha").html(html);


                            thickBoxModal("Dados de FGTS - " + nome, "#fgts_folha", 700, tamanhoNovo);
                        }
                    });

                });

                $("body").on("click", ".visualizar", function () {
                    $("#tbRelatorio").remove();
                    $(".totalizador").remove();
                    $(".imprime").remove();
                    var id_header = $(this).data("key");
                    var projeto = $(this).data("projeto");
                    $("#projeto_oculto").val(projeto);
                    $.ajax({
                        url: "provisao_de_gastos.php",
                        type: "POST",
                        dataType: "json",
                        data: {
                            method: "visualizarRescisao",
                            header: id_header
                        },
                        success: function (data) {
                            var html = "";
                            if (data.status == 1) {
                                html += "<input type='hidden' name='header_lote' id='header_lote' value='" + id_header + "' /><input type='submit' name='mostrar_prov_trab' id='mostrar_prov_trab' value='Provisão Trabalista' data-headerlote='" + id_header + "' style='margin: 10px; float:right' /><input type='submit' name='mostrar_rescisao' id='mostrar_rescisao' value='Visualizar Rescisão' data-headerlote='" + id_header + "' style='margin: 10px; float:right' />";
                                html += "<table id='tbRelatorio' border='0' cellpadding='0' cellspacing='0' class='grid' width='100%' style='page-break-after:auto;'><thead><tr><th colspan='6'></th></tr><tr style='font-size:10px !important;'><th rowspan='2'><input type='checkbox' name='id_clt_todos' id='id_clt_todos'/></th><th rowspan='2'>NOME</th><th rowspan='2'>AVISO</th><th rowspan='2'>STATUS</th><th rowspan='2'>SALÁRIO BRUTO</th></tr></thead>";
                                $.each(data.dados, function (k, v) {
                                    html += "<tr class='' style='font-size:11px;'><td align='center'><input type='checkbox' class='clts' name='id_clt[]' id='id_clt_" + v.id + "' value='" + v.id + "' /></td><td align='left'><label for='id_clt_" + v.id + "'>" + v.nome + "</label></td><td align='left'>" + v.aviso + "</td><td align='left'>" + v.status_clt + "</td><td align='right'>" + v.sal_base + "</td></tr>";
                                });
                                html += "</table>";
                                $("#lista_funcionarios").html(html);
                            }
                        },
                        error: function(data) {
                             alert("Erro ao selecionar recisão");
                }
                    });
                });

                $("#visualizar_participantes").click(function () {

                    var dados = $("#form").serialize();
                    $.ajax({
                        url: "provisao_de_gastos.php?method=verificaParticipantes&" + dados,
                        type: "POST",
                        dataType: "json",
                        success: function (data) {
                            if (data.status == 1) {
                                $.ajax({
                                    url: "",
                                    data: {
                                        method: "carregaFuncoes",
                                        regiao: data.id_regiao,
                                        projeto: data.id_projeto
                                    },
                                    type: "POST",
                                    dataType: "json",
                                    success: function (funcao) {
                                        var html = "";
                                        html += "<table id='tbRelatorio' border='0' cellpadding='0' cellspacing='0' class='grid' width='100%' style='page-break-after:auto; margin-top: 20px;'><thead><tr><th colspan='6' style='height:90px; text-align:left; background:white; border-top: 1px solid #ccc'> ";
                                        html += "<p>Selecione uma Função:</p>";
                                        html += "<div class=\"multiselect\"><div class=\"selectBox\" onclick=\"showCheckboxes()\">";
                                        html += "<select >";
                                        html += "<option value='0'>« Selecione »</option>";
                                        html += "</select>";

                                        html += "<div class=\"overSelect\"></div></div>";
                                        html += "<div id=\"checkboxes\">";
                                        html += "<label for=\"a-0\"><input name='filtro_funcao[]' class='tudo' type=\"checkbox\" id=\"a-0\" value='0'/>« Todos »</label>";
                                        $.each(funcao, function (k, v) {
                                            html += "<label for=\"a-" + k + "\"><input name='filtro_funcao[]' class='checkFuncao' type=\"checkbox\" id=\"a-" + k + "\" value='" + k + "'/>" + v + "</label>";
                                        });
                                        html += "</div>";
                                        html += "</div>";
                                        html += "<div class=\"class_button\">";
                                        html += "   <button type='button' id='filtro_funcao'>Filtrar</button>";
                                        html += "</div>";
                                        html += "<div class=\"class_button\">";
                                        html += "   <button type='button' id='gerar'>Gerar Provisão de Gastos</button>";
                                        html += "</div>";

//                                        html += "<p style='float:left;'>Selecione uma Função:</p><select name='filtro_funcao' id='filtro_funcao' style='width:320px; height:28px; clear: both; float:left;'>";
//                                        html += "<option value='todos'>« Todos »</option>";
//                                            $.each(funcao, function (k, v) {
//                                                html += "<option value='"+k+"'>"+v+"</option>";
//                                            });
//                                        html += "</select>";
                                        html += "</th></tr><tr style='font-size:10px !important;'><th rowspan='2'><input type='checkbox' name='id_clt_todos' id='id_clt_todos'/></th><th rowspan='2'>NOME</th><th rowspan='2'>FUNÇÃO</th><th rowspan='2'>STATUS</th><th rowspan='2'>SALÁRIO BRUTO</th></tr></thead>";
                                        $.each(data.dados, function (k, v) {
                                            html += "<tr class='' style='font-size:11px;' data-curso='" + v.id_curso + "'><td align='center'><input type='checkbox' class='clts validate[minCheckbox[1]]' name='id_clt[]' id='id_clt_" + v.id + "' value='" + v.id + "' /></td><td align='left'><label for='id_clt_" + v.id + "'>" + v.nome + "</label></td><td align='left'>" + v.funcao + "</td><td align='left'>" + v.status + "</td><td align='right'>R$ " + v.sal_base + "</td></tr>";
                                        });
                                        html += "</table>";

                                        $("#lista_funcionarios").html(html);
                                        //$("#gerar").remove();
                                        //$(".controls").append("<input type='button' name='gerar' value='Gerar' id='gerar'/>");
                                        $("#dispensa, #fator, #dataDemi").removeAttr("disabled");
                                    }
                                });
                            }
                        }
                    });

                });

                $("body").on("click", "#gerar", function () {
                    $("#projeto_oculto").val("");
                    var dados = $("#form").serialize();

                    //if invalid do nothing
                    if (!$("#form").validationEngine('validate')) {
                        return false;
                    }
                    $.ajax({
                        url: "provisao_de_gastos.php?method=verificaRescisao&" + dados,
                        type: "POST",
                        dataType: "json",
                        success: function (data) {
                            var html = "";
                            if (data.status == 1) {
                                html += "<table id='tbRelatorio' border='0' cellpadding='0' cellspacing='0' class='grid' width='100%' style='page-break-after:auto;'><thead><tr><th colspan='6'></th></tr><tr style='font-size:10px !important;'><th rowspan='2'><input type='checkbox' name='id_clt_todos' id='id_clt_todos'/></th><th rowspan='2'>NOME</th><th rowspan='2'>AVISO</th><th rowspan='2'>STATUS</th><th rowspan='2'>SALÁRIO BRUTO</th></tr></thead>";
                                $.each(data.dados, function (k, v) {
                                    html += "<tr class='' style='font-size:11px;'><td align='center'><input type='checkbox' class='clts' name='id_clt[]' id='id_clt_" + v.id + "' value='" + v.id + "' /></td><td align='left'><label for='id_clt_" + v.id + "'>" + v.nome + "</label></td><td align='left'>" + v.aviso + "</td><td align='left'>" + v.status_clt + "</td><td align='right'>" + v.sal_base + "</td></tr>";
                                });
                                html += "</table>";

                                $("#lista_funcionarios").html(html);
                            } else if (data.status == 2) {
                                $("#lista_funcionarios").html();
                                console.log('provisao_de_gastos.php?method=gerarRescisao&'+dados);
                                thickBoxConfirm("Gerar novas rescisões", "Não foi encontrado nenhuma rescisão com as configurações selecionadas, deseja criar agora?", 500, 350, function (data) {
                                    if (data == true) {
                                        $(".carregando").show();
                                        $.ajax({
                                            url: "provisao_de_gastos.php?method=gerarRescisao&" + dados,
                                            type: "POST",
                                            dataType: "json",
                                            success: function (data) {
                                                console.log(data);
                                                if (data) {
                                                    $(".carregando").hide();
                                                    if (data.status == 1) {
                                                        $.each(data.dados_projeto, function (k, v) {
                                                            console.log(v);
                                                            html += "<tr class='tr_" + v.id_header + "'><td>" + v.projeto + "</td><td>" + v.dispensa + "</td><td>" + v.fator + " </td><td>" + v.data_saida + "</td><td>" + v.aviso_previo + "</td><td>" + v.data_aviso + " </td><td>" + v.criado_por + "</td><td>" + v.criado_em + "</td><td align='center'>" + v.total_participantes + "</td><td><a href='javascript:;' data-key='" + v.id_header + "'data-projeto='" + v.id_projeto + "' class='visualizar' style='text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;'><img src='../imagens/icones/icon-view.gif' title='visualizar' /></a></td><td><a href='javascript:;' data-key='" + v.id_header + "' class='desprocessar' style='text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;'><img src='../imagens/icones/icon-delete.gif' title='desprocessar' /></a></td></tr>";
                                                        });
                                                        $("#historico_gerado").append(html);
                                                    }
                                                }
                                            }
                                        });
                                    }
                                });
                            }
                        }
                    });
                });

                $("body").on("click", "#id_clt_todos", function () {
                    var tudo = $(this).is(":checked");
                    $('.clts').each(function () {
                        var teste_funcao = $(this).closest('tr').css('display') !== 'none';
                        console.log($(this).closest('tr').css('display') !== 'none');
                        console.log($(this).html());
                        if (tudo && teste_funcao) {
                            console.log('oi');
                            $(this).attr("checked", true);
                        } else {
                            console.log('no');
                            $(this).attr("checked", false);
                        }

                    });

//                    var checado = $(this).is(":checked");
//                    var funcao_valida = ($('#'));
//                    if (checado && funcao_valida) {
//                        $(".clts").attr("checked", true);
//                    } else {
//                        $(".clts").attr("checked", false);
//                    }
                });

                $("body").on('click', ".xpandir", function () {
                    $(this).removeClass();
                    $(this).addClass("compactar");
                    $(".area-xpandir-1").attr({colspan: "9"});
                    $(".area-xpandir-2").attr({colspan: "30"});
                    $(".area-xpandir-3").attr({colspan: "1"});
                    $(".area-xpandir-4").attr({colspan: "1"});
                    $(".area-xpandir-5").attr({colspan: "1"});
                    $(".area-xpandir-6").attr({colspan: "1"});
                    $(".cabecalho_compactar").attr({colspan: "43"});
                    $(".area").css({display: "block"});
                    $(".esconder").show();
                    if ($('span').hasClass('compactarr') && $('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "9"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "4"});
                        $(".cabecalho_compactar").attr({colspan: "63"});
                    } else if ($('span').hasClass('compactarr')) {
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".cabecalho_compactar").attr({colspan: "60"});
                    } else if ($('span').hasClass('compactarrr')) {
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "3"});
                        $(".cabecalho_compactar").attr({colspan: "46"});
                    }
                });

                $("body").on("click", ".compactar", function () {
                    $(this).removeClass();
                    $(this).addClass("xpandir");
                    $(".area-xpandir-2").attr({colspan: "1"});
                    $(".area-xpandir-3").attr({colspan: "1"});
                    $(".area-xpandir-4").attr({colspan: "1"});
                    $(".area-xpandir-5").attr({colspan: "1"});
                    $(".cabecalho_compactar").attr({colspan: "12"});
                    $(".area").css({display: "none"});
                    $(".esconder").css({display: "none"});
                    if ($('span').hasClass('compactarr') && $('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "9"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "8"});
                        $(".cabecalho_compactar").attr({colspan: "32"});
                    } else if ($('span').hasClass('compactarr')) {
                        $(".area-xpandir-1").attr({colspan: "9"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "29"});
                    } else if ($('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "9"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "2"});
                        $(".cabecalho_compactar").attr({colspan: "15"});
                    }

                });

                $("body").on('click', ".xpandirr", function () {
                    $(this).removeClass();
                    $(this).addClass("compactarr");
                    $(".area-xpandir-1").attr({colspan: "9"});
                    $(".area-xpandir-2").attr({colspan: "1"});
                    $(".area-xpandir-3").attr({colspan: "1"});
                    $(".area-xpandir-4").attr({colspan: "17"});
                    $(".area-xpandir-5").attr({colspan: "1"});
                    $(".area-xpandir-6").attr({colspan: "1"});
                    $(".cabecalho_compactar").attr({colspan: "29"});
                    $(".areaa").css({display: "block"});
                    $(".esconderr").show();

                    if ($('span').hasClass('compactar') && $('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "9"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "4"});
                        $(".cabecalho_compactar").attr({colspan: "63"});
                    } else if ($('span').hasClass('compactar')) {
                        $(".area-xpandir-1").attr({colspan: "9"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "60"});
                    } else if ($('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "9"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "3"});
                        $(".cabecalho_compactar").attr({colspan: "32"});
                    }
                });

                $("body").on("click", ".compactarr", function () {
                    $(this).removeClass();
                    $(this).addClass("xpandirr");
                    if ($('span').hasClass('compactar') && $('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "9"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "3"});
                        $(".cabecalho_compactar").attr({colspan: "46"});
                    } else if ($('span').hasClass('compactar')) {
                        $(".area-xpandir-1").attr({colspan: "9"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "43"});
                    } else if ($('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "9"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "2"});
                        $(".cabecalho_compactar").attr({colspan: "15"});
                    } else {
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "12"});
                    }

                    $(".areaa").css({display: "none"});
                    $(".esconderr").css({display: "none"});
                });

                $("body").on('click', ".xpandirrr", function () {
                    $(this).removeClass();
                    $(this).addClass("compactarrr");
                    $(".area-xpandir-1").attr({colspan: "9"});
                    $(".area-xpandir-2").attr({colspan: "1"});
                    $(".area-xpandir-3").attr({colspan: "1"});
                    $(".area-xpandir-4").attr({colspan: "1"});
                    $(".area-xpandir-5").attr({colspan: "1"});
                    $(".area-xpandir-6").attr({colspan: "2"});
                    $(".cabecalho_compactar").attr({colspan: "15"});
                    $(".areaaa").css({display: "block"});
                    $(".esconderrr").show();
                    if ($('span').hasClass('compactar') && $('span').hasClass('compactarr')) {
                        $(".area-xpandir-1").attr({colspan: "9"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "4"});
                        $(".cabecalho_compactar").attr({colspan: "63"});
                    } else if ($('span').hasClass('compactar')) {
                        $(".area-xpandir-1").attr({colspan: "9"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "3"});
                        $(".cabecalho_compactar").attr({colspan: "46"});
                    } else if ($('span').hasClass('compactarr')) {
                        $(".area-xpandir-1").attr({colspan: "9"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "3"});
                        $(".cabecalho_compactar").attr({colspan: "32"});
                    }
                });

                $("body").on("click", ".compactarrr", function () {
                    $(this).removeClass();
                    $(this).addClass("xpandirrr");
                    $(".area-xpandir-1").attr({colspan: "9"});
                    $(".area-xpandir-2").attr({colspan: "1"});
                    $(".area-xpandir-3").attr({colspan: "1"});
                    $(".area-xpandir-4").attr({colspan: "1"});
                    $(".area-xpandir-5").attr({colspan: "1"});
                    $(".area-xpandir-6").attr({colspan: "1"});
                    $(".cabecalho_compactar").attr({colspan: "12"});

                    if ($('span').hasClass('compactar') && $('span').hasClass('compactarr')) {
                        $(".area-xpandir-1").attr({colspan: "9"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "60"});
                    } else if ($('span').hasClass('compactar')) {
                        $(".area-xpandir-1").attr({colspan: "9"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "43"});
                    } else if ($('span').hasClass('compactarr')) {
                        $(".area-xpandir-1").attr({colspan: "9"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "29"});
                    }

                    $(".areaaa").css({display: "none"});
                    $(".esconderrr").css({display: "none"});
                });

                $("#dispensa").change(function () {
                    var tipo = $(this).val();
                    if (tipo == 61 || tipo == 65) {
                        $("#diasIndOuTrab").removeAttr("disabled");
                        $("#aviso").removeAttr("disabled");
                        $("#dataAviso").removeAttr("disabled");
                    } else {
                        $("#diasIndOuTrab").attr({disabled: "disabled"});
                        $("#aviso").attr({disabled: "disabled"});
                        $("#dataAviso").attr({disabled: "disabled"});
                    }
                }).trigger("change");

                $("body").on("click", ".desprocessar", function () {
                    var header = $(this).data("key");
                    thickBoxConfirm("Desprocessar rescisões", "Deseja realmente desprocessar?", 500, 350, function (data) {
                        if (data == true) {
                            $.ajax({
                                type: "post",
                                dataType: "json",
                                data: {
                                    method: "desprocessarRecisao",
                                    header: header
                                },
                                success: function (data) {
                                    console.log(data.status);
                                    if(data.status==2) {
                                        alert("Exclusão não permitida pois existem rescisões efetivadas");
                                    }    
                                    else {
                                        $(".tr_" + header).remove();
                                    }    
                                }
                            });
                        }
                    });

                    $("#lista_funcionarios").html("");
                });
                

                $("#movimento").change(function () {
                    var movimento = $("#movimento :selected").text();
                    $("#nome_movimento").val(movimento);
                });

                $(".lanca_movimento").click(function () {
                    var rescisao = $(this).data("rescisao");
                    var clt = $(this).data("clt");
                    $("#id_rescisao").val(rescisao);
                    $("#id_clt").val(clt);
                    $("#lancamento_mov").show();
                    thickBoxModal("Lançamento de movimentos", "#lancamento_mov", 920, 700);

                    $("body").on("click", ".ui-icon-closethick", function () {
                        location.reload();
                    });

                    $.ajax({
                        type: "post",
                        dataType: "json",
                        data: {
                            rescisao: rescisao,
                            method: "carrega_movimentos"

                        },
                        success: function (data) {
                            if (data) {
                                var html = "";
                                html += "<table id='tab_movimentos' border='0' cellpadding='0' cellspacing='0' class='grid' width='100%'>";
                                html += "<thead><tr><td>COD</td><td>NOME</td><td>TIPO</td><td style='width:200px'>VALOR</td><td colspan='2'>AÇÕES</td></tr></thead><tbody>";
                                $.each(data.dados, function (k, v) {
                                    html += "<tr style='height: 46px;' class='tr_" + v.id_mov + "'><td>" + v.id_movimento + "</td><td> " + v.nome_movimento + " </td><td> " + v.tipo + " </td><td><span class='valor_" + v.id_mov + "'> " + v.valor + " </span></td><td><a href='javascript:;' class='editar_valor movimento_" + v.id_mov + "' data-movimento='" + v.id_mov + "' data-valor='" + v.valor + "'><img src='../imagens/icones/icon-edit.gif' title='Editar Valor' /></a></td><td><a href='javascript:;' class='remover_valor' data-movimento='" + v.id_mov + "' data-valor='" + v.valor + "'><img src='../imagens/icones/icon-delete.gif' title='Deletar Valor' /></a></td></tr>";
                                });
                                html += "</tbody></table>";
                                $("#dados_histarico").html(html);
                            }

                        }
                    });

                });

                $("body").on("click", ".remover_valor", function () {
                    var movimento = $(this).data("movimento");
                    thickBoxConfirm("Remover Movimento", "Deseja realmente Remover esse movimento?", 500, 350, function (data) {
                        if (data == true) {
                            $.ajax({
                                type: "post",
                                dataType: "json",
                                data: {
                                    method: "removerMovimento",
                                    movimento: movimento
                                },
                                success: function (data) {
                                    $(".tr_" + movimento).remove();
                                }
                            });
                        }
                    });

                });

                $("body").on("click", ".editar_valor", function () {
                    $(".valor_mov_edit").hide();
                    var movimento = $(this).data("movimento");
                    var valor_movimento = $(this).attr("data-valor");
                    $(".valor_" + movimento).html("<input type='text' name='valor_mov_edit' class='valor_mov_edit' data-mov_input='" + movimento + "' value='" + valor_movimento + "' class='input_edit' />");
                });

                $("body").on("blur", ".valor_mov_edit", function () {
                    var valor = $(this).val();
                    var movimento = $(this).data("mov_input");

                    $.ajax({
                        type: "post",
                        dataType: "json",
                        data: {
                            valor: valor,
                            movimento: movimento,
                            method: "atualizaValorMovimento"
                        },
                        success: function (data) {
                            if (data.status) {
                                $(".mensagem").html("<span class='vermelho'>Movimento atualizado com sucesso</span>");
                            }
                        }
                    });
                    $(".valor_" + movimento).text(valor);
                    $(".movimento_" + movimento).attr({"data-valor": valor});
                });

                $("#cadastrar_mov").click(function () {

                    var movimento = $("#movimento").val();
                    var valor_movimento = $("#valor_movimento").val();
                    var rescisao = $("#id_rescisao").val();
                    var clt = $("#id_clt").val();
                    var nome_mov = $("#nome_movimento").val();
                    $("#valor_movimento").val("");
                    $.ajax({
                        url: "provisao_de_gastos.php",
                        type: "post",
                        dataType: "json",
                        data: {
                            method: "cadastraMovimentos",
                            movimento: movimento,
                            valor_movimento: valor_movimento,
                            id_rescisao: rescisao,
                            id_clt: clt,
                            nome_movimento: nome_mov
                        },
                        success: function (data) {
                            if (data.status) {
                                $(".mensagem").html("<span class='vermelho'>Movimento cadastrado com sucesso</span>");
                                var html = "";
                                $.each(data.dados, function (k, v) {
                                    html += "<tr class='tr_" + v.id_mov + "'>";
                                    html += "<td>" + v.id_movimento + "</td><td> " + v.nome_movimento + " </td><td>" + v.tipo + "</td><td><span class='valor_" + v.id_mov + "'> " + v.valor + " </span></td><td><a href='javascript:;' class='editar_valor movimento_" + v.id_mov + "' data-movimento='" + v.id_mov + "' data-valor='" + v.valor + "'><img src='../imagens/icones/icon-edit.gif' title='Editar Valor' /></a></td><td><a href='javascript:;' class='remover_valor' data-movimento='" + v.id_mov + "' data-valor='" + v.valor + "'><img src='../imagens/icones/icon-delete.gif' title='Deletar Valor' /></a></td>";
                                    html += "</tr>";
                                });

                                $("#tab_movimentos").append(html);
                            }
                        }
                    });
                });
                
                
                
                $('body').on('click',"#mostrar_rescisao,#mostrar_prov_trab",function(){
                    $("#regiao").removeClass('validate[required, custom[select]]');
                    $("#projeto").removeClass('validate[required, custom[select]]');
                    $("#form").submit();
                });
                

                // download do excel -------------------------------------------
//                $(".exportarExcel").click(function(){
////                    $("#form").attr("action","provisao_de_gastos_xls_generator.php");
//                    $("#form").submit();
//                });

            });

            // MULTI SELECT
            var expanded = false;
            function showCheckboxes() {
                var checkboxes = document.getElementById("checkboxes");
                if (!expanded) {
                    checkboxes.style.display = "block";
                    expanded = true;
                } else {
                    checkboxes.style.display = "none";
                    expanded = false;
                }
            }
            // FIM MULTI SELECT


        </script>
        <style>

            .input_edit{
                height: 19px;
                width: 46px;
                box-sizing: border-box;
                padding: 3px;
            }


            #total_anos{
                display: block;
                margin-top: 555px;
                margin-left: 10px;
                text-align: right;
                margin-right: 10px;
            }
            #total_anos p{
                font-family: arial;
                color: #333;
                font-size: 15px;
            }
            #total_anos span{
                font-weight: bold;
            }
            #fgts_folha{
                display: none;
            }
            .lista_fgts{
                border: 1px solid #ccc;
                padding: 5px;
                width: 207px;
                height: 535px;
                float: left;
                margin: 0px 10px;
                box-sizing: border-box;
            }
            .lista_fgts h3{
                border-bottom: 3px solid #333;
            }
            .lista_fgts h2{
                font-size: 16px;
                text-align: right;
                margin: 0px;
                background: #F5F3F3;
                width: 100%;
                padding: 5px;
                box-sizing: border-box;
            }
            .lista_fgts p{
                border-bottom: 1px dotted #ccc;
            }
            .header{
                font-weight: bold;
                background: #F3F3F3 !important;
                font-size: 11px !important;
                color: #333;
            }
            .footer{
                font-weight: bold;
                background: #F3F3F3;
            }

            .totalizador{
                border: 1px solid #ccc;
                padding: 5px;
                margin: 10px 10px;
                width: 347px;
                height: 424px;
                background: #f3f3f3;
                float: left;
            }
            .totalizador p{
                border-bottom: 1px dotted #ccc;
                padding-bottom: 2px;
            }
            .totalizador span{
                font-weight: bold;
                float: right;
            }
            .semborda{
                border: 0px !important;
            }
            .titulo{
                font-weight: bold;
                color: #000;
                text-align: center;
                font-size: 14px;
                margin: 5px 0px 20px 0px;
                border: 2px solid #B1A8A8 !important;
                padding: 1px 0px;
                background: #DFDFDF;
                height: 35px;
            }
            .compactar, .compactarr, .compactarrr, .xpandir, .compactarr, .xpandirr, .xpandirrr{
                float: right;
                font-family: verdana;
                font-size: 10px;
                font-weight: bold;
                color: #CA1E17;
                text-transform: uppercase;
                cursor: pointer;
            }

            .compactar:before, .compactarr:before, .compactarrr:before{
                content: " -";
                background: #1D1A1A;
                border-radius: 65%;
                padding: 1px 5px;
                font-weight: bold;
                color: #fff;
                margin-right: 5px;
            }

            .xpandir:before, .xpandirr:before, .xpandirrr:before{
                content: " +";
                background: #1D1A1A;
                border-radius: 65%;
                padding: 1px 3px;
                font-weight: bold;
                color: #fff;
                margin-right: 5px;
            }

            .esconder, .esconderr, .esconderrr{
                display: none;
            }

            .area, .areaa, .areaaa{
                border: 2px solid;
                height: 16px;
                width: 99%;
                margin-left: 5px;
                border-bottom: 0px;
                display: none;
            }

            .box{
                border: 0px solid #ccc;
                padding: 10px;
                box-sizing: border-box;
                margin: 5px;
                width: 1285px;
            }
            .col-esq, .col-dir{
                float: left;
                margin: 0px 5px;
                width: 590px;
            }

            .col-esq label, .col-dir label{
                width: 200px !important;
            }

            .inputPequeno{
                width: 324px;
                height: 27px;
                padding: 10px;
            }

            .selectPequeno{
                width: 324px;
                height: 28px;
                padding: 0px;
            }
            .carregando{
                width: 100%;
                height: 100%;
                position: fixed;
                top: 0px;
                left: 0px;
                background: #fff;
                opacity: 0.95;
                display: none;
            }
            .carregando img{
                width: 160px;
                box-sizing: border-box;
                text-align: center;
                margin-left: 150px;
            }
            .carregando .box-message{
                position: absolute;
                top: 150px;
                left: 37%;
                background: #F8F8F8;
                padding: 15px;
                box-sizing: border-box;
                box-shadow: 5px 5px 80px #333;
            }
            .carregando .box-message p{
                font-family: arial;
                font-size: 14px;
                color: #333;
                font-weight: bold;
                text-align: center;
            }

            .historico{
                height: 436px;
                overflow: auto;
            }

            th > span{
                font-weight: bold !important;
                margin-right: 5px;
                color: #888;
                //display: block;
            }

            th{
                font-weight: 500 !important;
                font-size: 12px !important; 
                text-transform: uppercase;
            }

            #lancamento_mov{
                display: none;
            }

            #lancamento_mov label{
                display: block;
                margin: 5px 0px;
                text-align: left;
                width: 200px;
                text-transform: uppercase;
                font-size: 11px;
                color: #333;
            }

            #lancamento_mov input[type='text']{
                width: 90px;
                padding: 5px;
            }

            #lancamento_mov input[type='button']{
                width: 160px;
                padding: 9px;
                background: #f1f1f1;
                border: 1px solid #ccc;
                font-weight: bold;
                cursor: pointer;
            }

            #lancamento_mov input[type='button']:hover{
                color: #999;
            }

            #box-1{
                box-sizing: border-box;
                padding: 15px 0px;
            }

            #lancamento_mov fieldset{
                border: 0px;
                margin-left: 20px;
            }
            .descricao_box{
                font-family: arial;
                font-size: 14px;
                color: #666;
                text-transform: uppercase;
                border-bottom: 1px dotted #ccc;
                width: 670px;
                padding-bottom: 5px;
            }
            .texto_pequeno{
                font-size: 11px !important;
                text-transform: uppercase !important;
            }

            .vermelho{
                color: red;
            }

            #tab_movimentos td{
                padding: 8px !important;
            }



            /* MULTISELECT */
            .multiselect {
                display: inline-block;
                width: 400px;
            }
            .multiselect select{
                padding: 5px;
            }
            .selectBox {
                position: relative;
            }
            #filtro_funcao{
                padding: 5px;
            }
            .selectBox select {
                width: 100%;
                font-weight: bold;
            }
            .overSelect {
                position: absolute;
                left: 0; right: 0; top: 0; bottom: 0;
            }
            #checkboxes {
                overflow: auto;
                max-height: 300px;
                width: 400px;
                position: absolute;
                display: none;
                border: 1px #dadada solid;
                z-index: 100;
                background-color: #FFF;
            }
            #checkboxes label {
                display: block;
                text-align:left;
            }
            #checkboxes label:hover {
                background-color: #1e90ff;
            }
            /* FIM MULTISELECT */

        </style>
    </head>
    <body class="novaintra" >  
        <div id="content" style="width: 1300px; display: table;">
        
            <div id="head">
                <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                <div class="fleft">
                    <h2>Relatório de Rescisão de Lote Finalizado</h2>
                </div>
            </div>
            
            <h3><?php echo $projeto['nome'] ?></h3>    
            <!--<p>Total de participantes: <b><?php //echo $total_participantes["total_participantes"];      ?></b></p>-->
            <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto; border: 0px;"> 
                <thead>
                    <tr style="height: 30px; background: #fff; border: 0px;">
                        <td colspan="10" class="area-xpandir-1"><span class="xpandir"></span></td>
                        <td colspan="1" class="area-xpandir-2"><div class="area"></div></td>
                        <td colspan="1" class="area-xpandir-3"><span class="xpandirr"></span></td>
                        <td colspan="1" class="area-xpandir-4"><div class="areaa"></div></td>
                        <td colspan="1" class="area-xpandir-5"><span class="xpandirrr"></span></td>
                        <td colspan="1" class="area-xpandir-6"><div class="areaaa"></div></td>
                    </tr>
                </thead>
                <?php $status = 0; ?>

                <?php
                while ($row_rel = mysql_fetch_assoc($rsRescisao)) {

                    if($_COOKIE['logado'] == 179){
                        echo "<pre>" ;
                            print_r($row_rel);
                        echo "</pre>" ;
                    }

                    $mov = array();
                    $total_movimentos = array();
                    $movimentos_incide = 0;

                    /*
                     * Busca pelos movimentos para rescisão na tabela morta
                     */
                    $sQuery = "
                        SELECT 
                            A.id_mov, 
                            A.id_rescisao, 
                            A.id_clt, 
                            A.id_movimento, 
                            A.valor, 
                            TRIM(A.tipo) as tipos, 
                            B.incidencia_inss 
                        FROM tabela_morta_movimentos_recisao_lote AS A 
                            LEFT JOIN rh_movimentos AS B ON(A.id_movimento = B.cod)
                        WHERE 
                            A.id_clt = {$row_rel['id_clt']} 
                            AND A.id_rescisao = '{$row_rel['id_recisao']}'
                            ";

                    $rsMovimentoRecisao = mysql_query($sQuery) or die($sQuery);

                    while ($rows_movimentos = mysql_fetch_assoc($rsMovimentoRecisao)) {

                        $mov[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']][$rows_movimentos['tipos']][$rows_movimentos['id_movimento']]["valor"] = $rows_movimentos['valor'];

                        if ($rows_movimentos['tipos'] == "CREDITO" && $rows_movimentos['incidencia_inss'] == '1') {

                            $movimentos_incide += $rows_movimentos['valor'];

                        }
                        if ($rows_movimentos['tipos'] == "DEBITO") {

                            $total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['DEBITO'] += $rows_movimentos['valor'];

                        } else if ($rows_movimentos['tipos'] == "CREDITO") {

                            $total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['CREDITO'] += $rows_movimentos['valor'];

                        }
                    }

                    /////////////////////
                    // MOVIMENTOS FIXOS ///// 
                    ///////////////////

                    $sQuery = 
                    "
                    SELECT  
                        A.ids_movimentos_estatisticas, 
                        B.id_clt,A.mes
                    FROM rh_folha as A
                        INNER JOIN rh_folha_proc as B ON A. id_folha = B.id_folha
                    WHERE 
                        B.id_clt = {$row_rel['id_clt']}  
                        AND B.status = 3 
                        AND A.terceiro = 2 
                        AND A.data_inicio >= DATE_SUB(NOW(), INTERVAL 13 MONTH) 
                    ORDER BY 
                        A.ano,
                        A.mes
                    ";

                    $rsFolha = mysql_query($sQuery);

                    $movimentos = 0;
                    $total_rendi = 0;

                    while ($row_folha = mysql_fetch_assoc($rsFolha)) {

                        if (!empty($row_folha['ids_movimentos_estatisticas'])) {

                            $sQuery = "
                                SELECT *
                                FROM rh_movimentos_clt
                                WHERE 
                                    id_movimento IN({$row_folha['ids_movimentos_estatisticas']}) 
                                    AND incidencia = '5020,5021,5023'  
                                    AND tipo_movimento = 'CREDITO' 
                                    AND id_clt = '{$row_rel['id_clt']}' 
                                    AND id_mov NOT IN(56,200) ";

                            $rsMovimentos = mysql_query($sQuery);

                            while ($row_mov = mysql_fetch_assoc($rsMovimentos)) {

                                $movimentos += $row_mov['valor_movimento'];

                            }
                        }

                    }

    //                        echo "<pre>";
    //                            print_r($movimentos);
    //                        echo "</pre>";

                    if ($movimentos > 0) {
                        $total_rendi = $movimentos / 12;
                    } else {
                        $total_rendi = 0;
                    }


                    ///////////////////////////////////////////////
                    ////////// CÁLCULO DE INSS /////////////
                    ///////////////////////////////////////////////
                    /**
                     * FEITO POR SINESIO LUIZ
                     * REMOVIDO A LEI 12_506 JUNTO AO LEONARDO DO RH PARA EFEITO DE BASE DE INSS
                     */
                    $base_saldo_salario = $row_rel['saldo_salario'] + $row_rel['insalubridade'] + $movimentos_incide - $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"];
                    $data_exp = explode('-', $row_rel['data_demi']);

                    if ($base_saldo_salario > 0) {
                        //echo $base_saldo_salario;
                        $calculos->MostraINSS($base_saldo_salario, implode('-', $data_exp));
                        if($_COOKIE['logado'] == 179){
                            echo "Sinesio INSS: " . $calculos->valor;
                        }
                        $inss_saldo_salario = $calculos->valor;
                        $percentual_inss = $calculos->percentual;

                        if ($row_rel['desconto_inss'] == 1) {
                            if ($row_rel['desconto_outra_empresa'] + $inss_saldo_salario > $calculos->teto) {
                                $inss_saldo_salario = ($calculos->teto - $row_rel['desconto_outra_empresa'] );
                            }
                        }
                    } else {
                        $base_saldo_salario = 0;
                    }

                    //CALCULO IRRF
                    $irrf = 0;
                    $base_irrf = $base_saldo_salario - $inss_saldo_salario;
                    $calculos->MostraIRRF($base_irrf, $row_rel['id_clt'], $row_rel['id_projeto'], implode('-', $data_exp));

                    $inss_recolher = $folha->getInssARecolher($row_rel['id_clt']);
                    $class = ($cont++ % 2 == 0) ? "even" : "odd";

                    $status_old = $status;

                    if ($status != $row_rel["codigo"]) {
                        $status = $row_rel["codigo"];
                        ?>

                        <?php if (!empty($total_sal_base)) { ?>
                            <?php
                            if ($row_rel['codigo'] != 20) {
                                $total_recisao_nao_paga += $total_liquido;
                            }
                            ?>
                            <tfoot>
                                <tr class="footer">
                                    <td align="right" colspan="6">Total:</td>
                                    <td align="right"><?php echo "R$ " . number_format($total_das_medias_outras_remuneracoes, 2, ",", "."); ?></td>
                                    <td align="right"><?php echo "R$ " . number_format($total_sal_base, 2, ",", "."); ?></td>
                                    <!--<td align="right"><?php echo "R$ " . number_format($total_valor_aviso, 2, ",", "."); ?></td>-->
                                    <td align="right"><?php echo "R$ " . number_format($total_saldo_salario, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_comissoes, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_gratificacao, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_insalubridade, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_periculosidade, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_adicional_noturno, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_hora_extra, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_gorjetas, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_dsr, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_reflexo_dsr, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_multa_477, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_multa_479, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_sal_familia, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_dt_salario, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_terceiro_exercicio, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ferias_pr, 2, ",", "."); ?></td>    
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_fp, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ferias_aquisitivas, 2, ",", "."); ?></td>    
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_fv, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_terco_constitucional, 2, ",", "."); ?></td>    
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_aviso_indenizado, 2, ",", "."); ?></td>    
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_terceiro_ss, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_f_aviso_indenizado, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_f_dobro, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_f_dobro, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_ferias_aviso, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_diferenca_salarial, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ajuda_custo, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_lei_12_506, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_dif_dissidio, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_vale_transporte, 2, ",", "."); ?></td>
                                    <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ajuste_de_saldo, 2, ",", "."); ?></td>
                                    <td align="right"><?php echo "R$ " . number_format($total_grupo_rendimento[$status_old], 2, ",", "."); ?></td>



                                    <!-- TOTAL DE DEDUÇÃO -->
                                    <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_pensao_alimenticia, 2, ",", "."); ?></td>
                                    <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_adiantamento_salarial, 2, ",", "."); ?></td>
                                    <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_adiantamento_13_salarial, 2, ",", "."); ?></td>
                                    <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_aviso_indenizado_debito, 2, ",", ".");?></td>
                                    <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_multa_480, 2, ",", ".");?></td>
                                    <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_emprestimo_consignado, 2, ",", ".");?></td>
                                    <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_vale_transporte_debito, 2, ",", ".");?></td>
                                    <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_vale_alimentacao_debito, 2, ",", ".");?></td>
                                    <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_inss_ss, 2, ",", ".");?></td>
                                    <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_inss_dt, 2, ",", ".");?></td>
                                    <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_ir_ss, 2, ",", ".");?></td>
                                    <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_ir_dt, 2, ",", ".");?></td>
                                    <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_devolucao, 2, ",", ".");?></td>
                                    <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_outros, 2, ",", ".");?></td>
                                    <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_adiantamento_13, 2, ",", ".");?></td>
                                    <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_faltas, 2, ",", ".");?></td>
                                    <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_ir_ferias, 2, ",", ".");?></td>
                                    <td align="right" class="">(<?php echo "R$ " . number_format($total_grupo_deducao[$status_old], 2, ",", "."); ?>)</td>
                                    <td align="right"><?php echo "R$ " . number_format($total_grupo_rendimento[$status_old] - $total_grupo_deducao[$status_old], 2, ",", "."); ?></td>


                                    <!-- DETALHES IMPORTANTES -->
                                    <!-- BASES -->                        
                                    <td align="right" class="esconderrr"><?php echo "R$ " . number_format($total_base_inss, 2, ",", "."); ?></td>
                                    <td align="right" class="esconderrr"><?php echo "R$ " . number_format($total_base_fgts, 2, ",", "."); ?></td>
                                    <td align="right" class="esconderrr"><?php echo "R$ " . number_format($total_base_pis, 2, ",", "."); ?></td>
                                    <td align="right" style="background: #fff; border: 0px;"></td>                       
                                    <td align="right"><?php echo "R$ " . number_format($total_pis, 2, ",", "."); ?></td>                       
                                    <td align="right"><?php echo "R$ " . number_format($total_multa_fgts, 2, ",", "."); ?></td>                       
                                    <td align="right"><?php echo "R$ " . number_format($total_inss_empresa, 2, ",", "."); ?></td> 
                                    <td align="right"><?php echo "R$ " . number_format($total_inss_terceiro, 2, ",", "."); ?></td> 
                                    <td align="right"><?php echo "R$ " . number_format($total_fgts_recolher, 2, ",", "."); ?></td> 
                                </tr>
                                <tr>
                                    <td colspan="37" style="border: 0px;"></td>
                                </tr>
                            </tfoot>

                        <?php 

                            } else { 

                            ?>
                            <tfoot>
                                <tr class="footer">
                                    <td colspan="74"></td>
                                </tr>
                            </tfoot>                    
                        <?php } ?>
                        <thead>
                            <tr>
                                <th colspan="12" class="cabecalho_compactar"><?php echo "<p style='text-transform:uppercase; text-align:left' > » " . $row_rel['especifica'] . " - " . $row_rel['aviso'] . "</p>"; ?></th>
                                <th style="background: #fff; border: 0px;" ></th>
                                <th colspan="5">EMPRESA</th>
                            </tr>
                            <tr style="font-size:10px !important;">
                                <th rowspan="2">ID</th>
                                <th rowspan="2"><span class="numero_rescisao">[11]</span>NOME</th>
                                <th rowspan="2"><span class="numero_rescisao">[24]</span>DATA DE ADMISSÃO</th>
                                <th rowspan="2"><span class="numero_rescisao">[25]</span>Data do Aviso Prévio</th>  
                                <th rowspan="2"><span class="numero_rescisao">[26]</span>DATA DE AFASTAMENTO</th>                                
                                <th rowspan="2">FUNÇÃO</th>  
                                <th rowspan="2">MÉDIA DAS OUTRAS REMUNERAÇÕES</th>  
                                <th rowspan="2">SALÁRIO BASE</th>  
                                <!--<th rowspan="2">VALOR AVISO</th>-->  
                                <th rowspan="2"><span class="numero_rescisao">[50]</span>SALDO DE SALÁRIO</th>

                                <!--DISCRIMINAÇÃO DAS VERBAS RESCISÓRIAS--->
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[51]</span>COMISSÕES</th>
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[52]</span>GRATIFICAÇÃO</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[53]</span>ADICIONAL DE INSALUBRIDADE</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[54]</span>ADICIONAL DE PERICULOSIDADE</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[55]</span>ADICIONAL NOTURNO</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[56]</span>Horas Extras</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[57]</span>Gorjetas</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[58]</span>Descanso Semanal Remunerado (DSR)</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[59]</span>Reflexo do "DSR" sobre Salário Variável</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[60]</span>Multa Art. 477, § 8º/CLT</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[61]</span>Multa Art. 479/CLT</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[62]</span>Salário-Família</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[63]</span>13º Salário Proporcional</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[64]</span>13º Salário Exercício</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[65]</span>Férias Proporcionais</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[]</span>1/3 DE FÉRIAS PROPORCIONAL </th> 
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[66]</span>Férias Vencidas Per. Aquisitivo</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[]</span>1/3 DE FÉRIAS VENCIDAS</th> 
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[68]</span>Terço Constitucional de Férias</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[69]</span>Aviso Prévio indenizado</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[70]</span>13º Salário (Aviso-Prévio Indenizado)</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[71]</span>Férias (Aviso-Prévio Indenizado)</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[72]</span>Férias em dobro</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[73]</span>1/3 férias em dobro</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[82]</span> 1/3 DE FÉRIAS AVISO INDENIZADO </th>
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[80]</span>Diferença Salarial</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[82]</span>Ajuda de Custo Art. 470/CLT</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[95]</span>Lei 12.506</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[95]</span>Diferença Dissídio</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[106]</span>Vale Transporte</th>  
                                <th rowspan="2" class="esconder"><span class="numero_rescisao">[99]</span>Ajuste do Saldo Devedor</th>  
                                <th rowspan="2" ><span class="numero_rescisao"></span>TOTAL RESCISÓRIO BRUTO</th>  

                                <!--DEDUÇÕES--->
                                <th rowspan="2" class="esconderr"><span class="numero_rescisao">[100]</span>Pensão Alimentícia</th>  
                                <th rowspan="2" class="esconderr"><span class="numero_rescisao">[101]</span>Adiantamento Salarial</th>  
                                <th rowspan="2" class="esconderr"><span class="numero_rescisao">[102]</span>Adiantamento de 13º Salário</th>  
                                <th rowspan="2" class="esconderr"><span class="numero_rescisao">[103]</span>Aviso-Prévio Indenizado</th>  
                                <th rowspan="2" class="esconderr"><span class="numero_rescisao">[104]</span>Multa Art. 480/CLT</th>  
                                <th rowspan="2" class="esconderr"><span class="numero_rescisao">[105]</span>Empréstimo em Consignação</th>  
                                <th rowspan="2" class="esconderr"><span class="numero_rescisao">[106]</span>Vale Transporte</th>  
                                <th rowspan="2" class="esconderr"><span class="numero_rescisao">[109]</span>Vale Alimentação</th> 


                                <th rowspan="2" class="esconderr"><span class="numero_rescisao">[112.1]</span>Previdência Social</th>  
                                <th rowspan="2" class="esconderr"><span class="numero_rescisao">[112.2]</span>Previdência Social - 13º Salário</th>  
                                <th rowspan="2" class="esconderr"><span class="numero_rescisao">[114.1]</span>IRRF</th>  
                                <th rowspan="2" class="esconderr"><span class="numero_rescisao">[114.2</span>IRRF sobre 13º Salário</th>  
                                <th rowspan="2" class="esconderr"><span class="numero_rescisao">[115]</span>Devolução de Crédito Indevido</th>  
                                <th rowspan="2" class="esconderr"><span class="numero_rescisao">[115.1]</span>Outros</th>  
                                <th rowspan="2" class="esconderr"><span class="numero_rescisao">[115.2]</span>Adiantamento de 13º Salário</th>
                                <th rowspan="2" class="esconderr"><span class="numero_rescisao">[117]</span>Faltas</th>    
                                <th rowspan="2" class="esconderr"><span class="numero_rescisao">[116]</span>IRRF Férias</th>  

                                <th rowspan="2"><span class="numero_rescisao"></span>TOTAL DAS DEDUÇÕES</th>  
                                <th rowspan="2" >VALOR RESCISÓRIO LÍQUIDO</th> 

                                <!-- DETALHES IMPORTANTES --->
                                <!--BASES -->
                                <th rowspan="2" class="esconderrr">BASE INSS</th>   
                                <th rowspan="2" class="esconderrr">BASE FGTS</th>  
                                <th rowspan="2" class="esconderrr">BASE PIS</th>  

                                <!--EMPRESA-->
                                <th rowspan="2" style="background: #fff; border: 0px;"></th>   
                                <th rowspan="2">PIS</th>   
                                <th rowspan="2">MULTA DE 50% DO FGTS</th>   
                                <th colspan="2">INSS A RECOLHER</th>  
                                <th rowspan="2">FGTS A RECOLHER</th>

                            </tr>
                            <tr style="font-size:10px !important;">
                                <th>EMPRESA</th>   
                                <th>TERCEIRO</th>  
                            </tr>
                        </thead>
                        <?php
                        //VERBAS RESCISÓRIAS
                        $total_das_medias_outras_remuneracoes = 0;
                        $total_sal_base = 0;
                        $total_valor_aviso = 0;
                        $total_saldo_salario = 0;
                        $total_comissoes = 0;
                        $total_gratificacao = 0;
                        $total_insalubridade = 0;
                        $total_periculosidade = 0;
                        $total_adicional_noturno = 0;
                        $total_hora_extra = 0;
                        $total_gorjetas = 0;
                        $total_dsr = 0;
                        $total_reflexo_dsr = 0;
                        $total_multa_477 = 0;
                        $total_multa_479 = 0;
                        $total_sal_familia = 0;
                        $total_dt_salario = 0;
                        $total_terceiro_exercicio = 0;
                        $total_ferias_pr = 0;
                        $total_ferias_aquisitivas = 0;
                        $total_terco_constitucional = 0;
                        $total_aviso_indenizado = 0;
                        $total_terceiro_ss = 0;
                        $total_f_aviso_indenizado = 0;
                        $total_f_dobro = 0;
                        $total_umterco_f_dobro = 0;
                        $total_diferenca_salarial = 0;
                        $total_ajuda_custo = 0;
                        $total_lei_12_506 = 0;
                        $total_dif_dissidio = 0;
                        $total_vale_transporte = 0;
                        $total_ajuste_de_saldo = 0;
                        $total_rendimento = 0;


                        //DEDUÇÕES
                        $total_pensao_alimenticia = 0;
                        $total_adiantamento_salarial = 0;
                        $total_adiantamento_13_salarial = 0;
                        $total_aviso_indenizado_debito = 0;
                        $total_multa_480 = 0;
                        $total_emprestimo_consignado = 0;
                        $total_vale_transporte_debito = 0;
                        $total_vale_alimentacao_debito = 0;
                        $total_inss_ss = 0;
                        $total_inss_dt = 0;
                        $total_ir_ss = 0;
                        $total_ir_dt = 0;
                        $total_devolucao = 0;
                        $total_outros = 0;
                        $total_adiantamento_13 = 0;
                        $total_faltas = 0;
                        $total_ir_ferias = 0;
                        $total_deducao = 0;
                        $total_liquido = 0;

                        //DETALHES IMPORTANTES
                        $total_umterco_ferias_aviso = 0;
                        $total_umterco_fp = 0;
                        $total_umterco_fv = 0;
                        $total_ferias_vencida = 0;
                        $total_f_dobro_fv = 0;

                        //BASES
                        $total_base_inss = 0;
                        $total_base_fgts = 0;
                        $total_base_pis = 0;
                        $total_pis = 0;
                        $total_multa_fgts = 0;
                        $total_inss_empresa = 0;
                        $total_inss_terceiro = 0;
                        $total_fgts_recolher = 0;

                        //Totalizadores gerais
                        $total_geral_rendimento = 0;
                        $total_geral_deducao = 0;                                               

                        //TOTALIZADOR FÉRIAS
                        $total_ferias_a_pagar = 0;

                        //TOTALIZADOR 13° 
                        $total_decimo_a_pagar = 0;


                        ?>

                    <?php } ?>

                    <tr class="<?php echo $class ?>" style="font-size:11px;">
                        <td align="left">
                            <?php echo $row_rel['id_clt']; ?>
                            <input type="hidden" name="id_clt[]" value="<?php echo $row_rel['id_clt']; ?>">
                            <input type="hidden" name="id_recisao[]" value="<?php echo $row_rel['id_recisao']; ?>">
                        </td>
                        <td align="left"><a href='javascript:;' data-key='<?php echo $row_rel['id_clt']; ?>' data-nome='<?php echo $row_rel['nome']; ?>' class='calcula_multa' style='color: #4989DA; text-decoration: none;'><?php echo $row_rel['nome']; ?></a></td>
                        <td align="left"><?php echo (!empty($row_rel['data_adm'])) ? date("d/m/Y", str_replace("-", "/", strtotime($row_rel['data_adm']))) : "0000-00-00"; ?></td>
                        <td align="left"><?php echo (!empty($row_rel['data_aviso'])) ? date("d/m/Y", str_replace("-", "/", strtotime($row_rel['data_aviso']))) : "00/00/0000"; ?></td>
                        <td align="left"><?php echo (!empty($row_rel['data_demi'])) ? date("d/m/Y", str_replace("-", "/", strtotime($row_rel['data_demi']))) : "0000-00-00"; ?></td>
                        <td align="left"><?php echo $row_rel['nome_funcao']; ?></td>
                        <td align="left"><?php
                            echo "R$ " . number_format($total_rendi, 2, ",", ".");
                            $total_das_medias_outras_remuneracoes += $total_rendi;
                            ?></td>
                        <td align="right">
                            <?php
                            echo "R$ " . number_format($row_rel['sal_base'], 2, ",", "."); 
                            $total_sal_base += $row_rel['sal_base'];
    //                                    foreach ($status_array as $status_clt) {
    //                                        if ($row_rel['codigo'] == $status_clt) {
    //                                            $total_a_ser_pago[$status_clt] += $row_rel['total_rendimento'] + ($total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']) - ($row_rel['total_deducao'] + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO']);
    //                                        }
    //                                    }
                            ?>
                        </td> 
    <!--                                <td align="left" class="">
                        <?php
                        if ($row_rel['motivo'] != 60) {
                            //linha comentada por Renato(13/03/2015) por inconsistencia
                            //$valor_aviso = $row_rel['sal_base'] + $total_rendi + $row_rel['insalubridade'];
                            $valor_aviso = $row_rel['aviso_valor'];
                            echo "R$ " . number_format($valor_aviso, 2, ",", ".");
                            $total_valor_aviso += $valor_aviso;
                        } else {
                            $valor_aviso = 0;
                            echo "R$ " . number_format($valor_aviso, 2, ",", ".");
                            $total_valor_aviso += $valor_aviso;
                        }
                        ?>
                        </td>-->

                        <?php
    //                            echo "<pre>"; 
    //                                print_r($row_rel);
    //                            echo "<pre>"; 
                        ?>

                        <?php
                        if ($row_rel['fator'] == "empregador") {
                            $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] = $row_rel['aviso_valor'];
                        } else if ($row_rel['fator'] == "empregado") {
                            $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'] = $row_rel['aviso_valor'];
                        }
                        ?>  

                        <!--DISCRIMINAÇÃO DAS VERBAS RESCISÓRIAS--->
                        <td align="left" class=""><?php
                            echo "[" . $row_rel['dias_saldo'] . "/30] <br /> R$ " . number_format($row_rel['saldo_salario'], 2, ",", ".");
                            $total_saldo_salario += $row_rel['saldo_salario'];
                            $total_rendimento  += $row_rel['saldo_salario'];
                            ?></td>
                        <td align="left" class="esconder"><?php
                            echo "R$ " . number_format($row_rel['comissao'], 2, ",", ".");
                            $total_comissoes += $row_rel['comissao'];
                            $total_rendimento = $row_rel['comissao'];

                            ?></td> <!--- 51--->
                        <td align="left" class="esconder"><?php
                            echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5912]["valor"], 2, ",", ".");
                            $total_gratificacao += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5912]["valor"];
                            $total_rendimento += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5912]["valor"];

                            ?></td> <!--- 52--->
                        <td align="left" class="esconder"><?php
                            echo "R$ " . number_format($row_rel['insalubridade'], 2, ",", ".");
                            $total_insalubridade += $row_rel['insalubridade'];
                            $total_rendimento  += $row_rel['insalubridade'];

                            ?></td>  <!--- 53--->
                        <td align="left" class="esconder"><?php
                            echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"], 2, ",", ".");
                            $total_periculosidade += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"];
                            $total_rendimento += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"];

                            ?></td> <!--- 54--->
                        <td align="left" class="esconder"><?php
                            echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9000]["valor"], 2, ",", ".");
                            $total_adicional_noturno += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9000]["valor"];
                            $total_rendimento  += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9000]["valor"];

                            ?></td> <!-- 55 -->
                        <td align="left" class="esconder"><?php
                            echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][8080]["valor"], 2, ",", ".");
                            $total_hora_extra += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][8080]["valor"];
                            $total_rendimento += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][8080]["valor"];
                            ?></td> <!-- 56 -->
                        <td align="left" class="esconder"><?php
                            echo "R$ " . number_format(0, 2, ",", ".");
                            $total_gorjetas += 0;
                            $total_rendimento += 0;
                            ?></td> <!-- 57 -->
                        <td align="left" class="esconder"><?php
                            echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9997]["valor"], 2, ",", ".");
                            $total_dsr += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9997]["valor"];
                            $total_rendimento += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9997]["valor"];

                            ?></td> <!-- 58 -->
                        <td align="left" class="esconder"><?php
                            echo "R$ " . number_format(0, 2, ",", ".");
                            $total_reflexo_dsr += 0;
                            $total_rendimento += 0;
                            ?></td> <!-- 59 -->
                        <td align="left" class="esconder"><?php
                            echo "R$ 0,00";
    //                        $total_multa_477 += $row_rel['a477'];
    //                        echo "R$ 0,00" . number_format($row_rel['a477'], 2, ",", ".");
                            ?></td> <!-- 60 -->
                        <?php
                        if ($row_rel['motivo'] == 64) {
                            $multa_479 = $row_rel['a479'];
                        } else if ($row_rel['motivo'] == 63) {
                            $multa_479 = null;
                        }
                        ?>
                        <td align="left" class="esconder"><?php
                            echo "R$ " . number_format($multa_479, 2, ",", ".");
                            $total_multa_479 += $multa_479;
                            $total_rendimento += $multa_479;
                            ?></td> <!-- 61 -->
                        <td align="left" class="esconder"><?php
                            echo "R$ " . number_format($row_rel['sal_familia'], 2, ",", ".");
                            $total_sal_familia += $row_rel['sal_familia'];
                            $total_rendimento += $row_rel['sal_familia'];
                            ?></td> <!-- 62 -->
                        <td align="right" class="esconder"><?php
                            echo "[" . sprintf('%02d', $row_rel['avos_dt']) . "/12] <br /> R$ " . number_format($row_rel['dt_salario'], 2, ",", ".");
                            $total_dt_salario += $row_rel['dt_salario'];
                            $total_decimo_a_pagar += $row_rel['dt_salario'];
                            $total_rendimento += $row_rel['dt_salario'];
                            ?></td> <!-- 63 -->                      
                        <td align="right" class="esconder"><?php
                            echo "R$ " . number_format(0, 2, ",", ".");
                            $total_terceiro_exercicio += 0;
                            $total_decimo_a_pagar += 0;
                            $total_rendimento += 0;
                            ?></td>    <!-- 64 -->                     
                        <td align="right" class="esconder"><?php
                            echo "[" . sprintf('%02d', $row_rel['avos_fp']) . "/12] <br /> R$ " . number_format($row_rel['ferias_pr'], 2, ",", ".");
                            $total_ferias_pr += $row_rel['ferias_pr'];
                            $total_ferias_a_pagar += $row_rel['ferias_pr'];
                            $total_rendimento += $row_rel['ferias_pr'];
                            ?></td>  <!-- 65 -->  
                        <td align="right" class="esconder"><?php
                            echo "R$ " . number_format($row_rel['umterco_fp'], 2, ",", ".");
                            $total_umterco_fp += $row_rel['umterco_fp'];
                            $total_ferias_a_pagar += $row_rel['umterco_fp'];
                            $total_rendimento += $row_rel['umterco_fp'];

                            ?></td> 
                        <td align="right" class="esconder"><?php
                            echo "R$ " . number_format($row_rel['ferias_vencidas'], 2, ",", ".");
                            $total_ferias_aquisitivas += $row_rel['ferias_vencidas'];
                            $total_ferias_a_pagar += $row_rel['ferias_vencidas'];
                            $total_rendimento += $row_rel['ferias_vencidas'];
                            ?></td>  <!-- 66 -->                         
                        <td align="right" class="esconder"><?php
                            echo "R$ " . number_format($row_rel['umterco_fv'], 2, ",", ".");
                            $total_umterco_fv += $row_rel['umterco_fv'];
                            $total_ferias_a_pagar += $row_rel['umterco_fv'];
                            $total_rendimento += $row_rel['umterco_fv'];
                            ?></td> 
                        <td align="right" class="esconder"><?php
                            echo "R$ " . number_format($row_rel['umterco_fv'] + $row_rel['umterco_fp'], 2, ",", ".");
                            $total_terco_constitucional += $row_rel['umterco_fv'] + $row_rel['umterco_fp'];
                            //$total_rendimento += $row_rel['umterco_fv'] + $row_rel['umterco_fp'];
                            //linha comentada por Renato(13/03/2015) por já estar somando acima
                            //$total_ferias_a_pagar += $row_rel['umterco_fv'] + $row_rel['umterco_fp'];
                            ?></td>    <!-- 68 -->              
                        <td align="right" class="esconder"><?php
                            echo "R$ " . number_format($aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'], 2, ",", ".");
                            $total_aviso_indenizado += $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'];
                            $total_rendimento += $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'];
                            ?></td>    <!-- 69 -->              
                        <td align="right" class="esconder"><?php
                            echo "R$ " . number_format($row_rel['terceiro_ss'], 2, ",", ".");
                            $total_terceiro_ss += $row_rel['terceiro_ss'];
                            $total_decimo_a_pagar += $row_rel['terceiro_ss'];
                            $total_rendimento += $row_rel['terceiro_ss'];
                            ?></td>   <!-- 70 -->                      
                        <td align="right" class="esconder"><?php
                            echo "R$ " . number_format($row_rel['ferias_aviso_indenizado'], 2, ",", ".");
                            $total_f_aviso_indenizado += $row_rel['ferias_aviso_indenizado'];
                            $total_ferias_a_pagar += $row_rel['ferias_aviso_indenizado'];
                            $total_rendimento += $row_rel['ferias_aviso_indenizado'];
                            ?></td>              <!-- 71 -->           
                        <td align="right" class="esconder"><?php
                            echo "R$ " . number_format($row_rel['fv_dobro'], 2, ",", ".");
                            $total_f_dobro += $row_rel['fv_dobro'];
                            $total_rendimento += $row_rel['fv_dobro'];

                            ?></td>  <!-- 72 -->                           
                        <td align="right" class="esconder"><?php
                            echo "R$ " . number_format($row_rel['um_terco_ferias_dobro'], 2, ",", ".");
                            $total_umterco_f_dobro += $row_rel['um_terco_ferias_dobro'];
                            $total_ferias_a_pagar += $row_rel['um_terco_ferias_dobro'];
                            $total_rendimento  += $row_rel['um_terco_ferias_dobro'];

                            ?></td>  <!-- 73 -->                           
                        <td align="right" class="esconder"><?php
                            echo "R$ " . number_format($row_rel['umterco_ferias_aviso_indenizado'], 2, ",", ".");
                            $total_umterco_ferias_aviso += $row_rel['umterco_ferias_aviso_indenizado'];
                            $total_ferias_a_pagar += $row_rel['umterco_ferias_aviso_indenizado'];
                            $total_rendimento  += $row_rel['umterco_ferias_aviso_indenizado'];

                            ?></td>   <!-- 82 --> 
                        <td align="right" class="esconder"><?php
                            echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5012]["valor"], 2, ",", ".");
                            $total_diferenca_salarial += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5012]["valor"];
                            $total_rendimento  += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5012]["valor"];

                            ?></td> <!-- 80 -->
                        <td align="right" class="esconder"><?php
                            echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5011]["valor"], 2, ",", ".");
                            $total_ajuda_custo += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5011]["valor"];
                            $total_rendimento  += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5011]["valor"];

                            ?></td>  <!-- 82 -->                           
                        <td align="right" class="esconder"><?php
                            echo "R$ " . number_format($row_rel['lei_12_506'], 2, ",", ".");
                            $total_lei_12_506 += $row_rel['lei_12_506'];
                            $total_rendimento  += $row_rel['lei_12_506'];
                            ?></td>  <!-- 95 -->                           
                        <td align="right" class="esconder"><?php
                            echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][80017]["valor"], 2, ",", ".");
                            $total_dif_dissidio += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][80017]["valor"];
                            $total_rendimento  += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][80017]["valor"];

                            ?></td>  <!-- 95 -->                           
                        <td align="right" class="esconder"><?php
                            echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][7001]["valor"], 2, ",", ".");
                            $total_vale_transporte += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][7001]["valor"];
                            $total_rendimento += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][7001]["valor"];
                            ?></td>  <!-- 106 -->                           
                        <td align="right" class="esconder"><?php
                            echo "R$ " . number_format($row_rel['arredondamento_positivo'], 2, ",", ".");
                            $total_ajuste_de_saldo += $row_rel['arredondamento_positivo'];
                            $total_rendimento += $row_rel['arredondamento_positivo'];

                            ?></td>  <!-- 99 -->                           
                        <td align="right" class="">
                            <?php
                            echo "R$ " . number_format($total_rendimento, 2, ",", ".");
                            $total_grupo_rendimento[$status] += $total_rendimento;


                            //echo "R$ " . number_format($row_rel['total_rendimento'] + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'], 2, ",", ".");
                            //$total_rendimento += $row_rel['total_rendimento'] + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'];
                            ?>
                        </td>

                        <!--DEDUÇÕES--->

                        <?php
                        if (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][6004]["valor"])) {
                            $pensao = $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][6004]["valor"];
                        } elseif (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50222]["valor"])) {
                            $pensao = $mov[$row_rel['id_recisao']][$row_rel['id_clt']][50222]["valor"];
                        } elseif (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']][7009]["valor"])) {
                            $pensao = $mov[$row_rel['id_recisao']][$row_rel['id_clt']][7009]["valor"];
                        } else {
                            $pensao = 0;
                        }
                        ?>
                        <td align="right" class="esconderr"><?php
                            echo "R$ " . number_format($pensao, 2, ",", ".");
                            $total_pensao_alimenticia += $pensao;
                            $total_deducao_debito +=$pensao;
                            $total_deducao = $pensao;
                            ?></td>  <!-- 100 -->                           
                        <td align="right" class="esconderr"><?php
                            echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][7003]["valor"], 2, ",", ".");
                            $total_adiantamento_salarial += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][7003]["valor"];
                            $total_deducao += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][7003]["valor"];
                            ?></td>  <!-- 101 -->                           
                        <td align="right" class="esconderr"><?php
                            echo "R$ " . number_format(0, 2, ",", ".");
                            $total_adiantamento_13_salarial += 0;
                            ?></td>  <!-- 102 -->                           
                        <td align="right" class="esconderr"><?php
                            echo "R$ " . number_format($aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'], 2, ",", ".");
                            $total_aviso_indenizado_debito += $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'];
                            $total_deducao += $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'];
                            ?></td>  <!-- 103 -->                           
                        <?php
                        if ($row_rel['motivo'] == 64) {
                            $multa_480 = null;
                        } else if ($row_rel['motivo'] == 63) {
                            $multa_480 = $row_rescisao['a480'];
                        }
                        ?>
                        <td align="right" class="esconderr"><?php
                            echo "R$ " . number_format($multa_480, 2, ",", ".");
                            $total_multa_480 += $multa_480;
                            $total_deducao_debito += $multa_480;
                            $total_deducao += $multa_480;
                            ?></td>  <!-- 104 -->                           
                        <td align="right" class="esconderr"><?php
                            echo "R$ " . number_format(0, 2, ",", ".");
                            $total_emprestimo_consignado += 0;
                            ?></td>  <!-- 105 -->                           
                        <td align="right" class="esconderr"><?php
                            echo "R$ " . number_format(0, 2, ",", ".");
                            $total_vale_transporte_debito += 0;
                            ?></td>  <!-- 107 -->  
                        <td align="right" class="esconderr"><?php
                            echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][8006]["valor"], 2, ",", ".");
                            $total_vale_alimentacao_debito += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][8006]["valor"];
                            $total_deducao += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][8006]["valor"];
                            ?></td>  <!-- 109 -->  
                        <td align="right" class="esconderr"><?php
                            echo "R$ " . number_format($inss_saldo_salario, 2, ",", ".");
                            $total_inss_ss += $inss_saldo_salario;
                            $total_deducao_debito += $inss_saldo_salario;
                            $total_deducao += $inss_saldo_salario;
                            ?></td>  <!-- 112.1 --> 
                        <td align="right" class="esconderr"><?php
                            echo "R$ " . number_format($row_rel['inss_dt'], 2, ",", ".");
                            $total_inss_dt += $row_rel['inss_dt'];
                            $total_deducao_debito += $row_rel['inss_dt'];
                            $total_deducao += $row_rel['inss_dt'];
                            ?></td>   <!-- 112.2 -->                     
                        <td align="right" class="esconderr"><?php
                            echo "R$ " . number_format($calculos->valor, 2, ",", ".");
                            $total_ir_ss += $calculos->valor;
                            $total_deducao_debito += $calculos->valor;
                            $total_deducao += $calculos->valor;
                            ?></td>   <!-- 114.1 -->                     
                        <td align="right" class="esconderr"><?php
                            echo "R$ " . number_format($row_rel['ir_dt'], 2, ",", ".");
                            $total_ir_dt += $row_rel['ir_dt'];
                            $total_deducao_debito += $row_rel['ir_dt'];
                            $total_deducao += $row_rel['ir_dt'];
                            ?></td>    <!-- 114.2 -->                    
                        <td align="right" class="esconderr"><?php
                            echo "R$ " . number_format($row_rel['devolucao'], 2, ",", ".");
                            $total_devolucao += $row_rel['devolucao'];
                            $total_deducao_debito += $row_rel['devolucao'];
                            $total_deducao += $row_rel['devolucao'];
                            ?></td>    <!-- 115 -->                    
                        <td align="right" class="esconderr"><?php
                            echo "R$ " . number_format(0, 2, ",", ".");
                            $total_outros += 0;
                            ?></td>    <!-- 115.1 -->                    
                        <td align="right" class="esconderr"><?php
                            echo "R$ " . number_format($row_rel['adiantamento_13'], 2, ",", ".");
                            $total_adiantamento_13 += $row_rel['adiantamento_13'];
                            $total_deducao += $row_rel['adiantamento_13'];
                            ?></td>    <!-- 115.2 -->                    

                        <?php
                        if (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"])) {
                            $movimento_falta = $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"];
                        } else {
                            $movimento_falta = 0;
                        }
                        ?>
                        <td align="right" class="esconderr"><?php
                            echo "R$ " . number_format($row_rel['valor_faltas'] + $movimento_falta, 2, ",", ".");
                            $total_faltas += $row_rel['valor_faltas'] + $movimento_falta;
                            $total_deducao_debito -= $row_rel['valor_faltas'] + $movimento_falta;
                            $total_deducao += $row_rel['valor_faltas'] + $movimento_falta;
                            ?></td>    <!-- 117 -->                    
                        <td align="right" class="esconderr"><?php
                            echo "R$ " . number_format($row_rel['ir_ferias'], 2, ",", ".");
                            $total_ir_ferias += $row_rel['ir_ferias'];
                            $total_deducao_debito += $row_rel['ir_ferias'];
                            $total_deducao += $row_rel['ir_ferias'];

                            ?></td>    <!-- 116 -->                    
                        <td align="right" class=""><?php 
                            $total_grupo_deducao[$status] += $total_deducao;
                            echo "R$ " . number_format($total_deducao, 2, ",", ".");
                            ?></td> <!--echo "R$ " . number_format($total_deducao_debito + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'], 2, ",", "."); $total_deducao += $total_deducao_debito + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO']; -->         
                        <td align="right">
                            <?php
                            //@jacques 06/08/2015 - Foram criadas algumas variáveis de totalização parcial e geral para os campos total das deduções parciais e gerais e valor rescisório líquido
                            echo "R$ " . number_format($total_rendimento - $total_deducao, 2, ",", ".");

                            ?>
                        </td>  

                        <!-- OUTROS VALORES -->
                        <!-- BASES -->

                        <?php
    //                                echo "vasco";
    //                                echo '<pre>';
    //                                echo $row_rel['total_rendimento'].'</br>';
    //                                echo $row_rel['lei_12_506'].'</br>';
    //                                echo $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'].'</br>';
    //                                echo $row_rel['sal_familia'].'</br>';
    //                                echo '</pre>';

                        // Bases para calculo de PIS, FGTS e INSS 
                        $base_pis  = $total_rendimento - $row_rel['lei_12_506'] - $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']  - $row_rel['sal_familia'];
                        $base_fgts  = $total_rendimento - $row_rel['lei_12_506'];
                        $base_inss = $total_rendimento - $row_rel['sal_familia'];

                        // Fatores aplicados as bases
                        $empresa['pis'] = $base_pis * 0.01;
                        $empresa['multa_fgts'] = $base_fgts;
                        $empresa['inss_empresa'] = $base_inss * 0.2112;
                        $empresa['inss_terceiro'] = $base_inss * 0.058;
                        $empresa['fgts_recolher'] = $base_fgts * 0.08;

                        // Totalizadores de sub-grupos
                        $total_base_pis += $empresa['pis'];
                        $total_base_fgts += $empresa['multa_fgts'];
                        $total_base_inss += $empresa['inss_empresa'];
                        $total_inss_empresa += $empresa['inss_empresa'];

                       ?>
                        <td align="right" class="esconderrr"><?php
                            echo "R$ " . number_format($base_inss, 2, ",", ".");
                            //echo "R$ " . number_format($row_rel['total_rendimento'] + $movimentos_incide - $row_rel['lei_12_506'] - $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] - $row_rel['sal_familia'], 2, ",", ".");
                            //$total_base_inss += $row_rel['total_rendimento'] + $movimentos_incide - $row_rel['lei_12_506'] - $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] - $row_rel['sal_familia'];
                            ?></td> 
                        <td align="right" class="esconderrr"><?php
                            echo "R$ " . number_format($base_fgts, 2, ",", ".");
                            //echo "R$ " . number_format($row_rel['total_rendimento'] + $movimentos_incide - $row_rel['lei_12_506'] - $row_rel['sal_familia'], 2, ",", ".");
                            //$total_base_fgts += $row_rel['total_rendimento'] + $movimentos_incide - $row_rel['lei_12_506'] - $row_rel['sal_familia'];
                            ?></td> 
                        <td align="right" class="esconderrr"><?php
                            echo "R$ " . number_format($base_pis, 2, ",", ".");
    //                                    echo "R$ " . number_format($row_rel['total_rendimento'] + $movimentos_incide - $row_rel['lei_12_506'] - $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] - $row_rel['sal_familia'], 2, ",", ".");
    //                                    $total_base_pis += $row_rel['total_rendimento'] + $movimentos_incide - $row_rel['lei_12_506'] - $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] - $row_rel['sal_familia'];
                            ?></td> 
                        <td align="right" style="background: #fff; border: 0px;"></td>                       
                        <td align="right">                        
                            <?php
                            echo "R$ " . number_format($empresa['pis'],2,',','.'); $total_pis += $empresa['pis'];
    //                                    echo "R$ " . number_format(($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] + $row_rel['insalubridade'] + $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"]) * 0.01, 2, ",", ".");
    //                                    $total_pis += ( $row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] + $row_rel['insalubridade'] + $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"]) * 0.01;
                            foreach ($status_array as $status_clt) {
                                if ($row_rel['codigo'] == $status_clt) {
                                    $total_pis_a_pagar[$status_clt] += $empresa['pis'];
                                }
                            }
                            ?>
                           </td>                       
                        <td align="right">
                            <?php
                            echo "R$ " . number_format($folha->getMultaFgts($row_rel['id_clt']), 2, ",", ".");
                            $total_multa_fgts += $folha->getMultaFgts($row_rel['id_clt']);
                            foreach ($status_array as $status_clt) {
                                if ($row_rel['codigo'] == $status_clt) {
                                    if ($row_rel['motivo'] == 61 && $row_rel['fator'] == "empregador") {
                                        $total_multa_a_pagar[$status_clt] += $folha->getMultaFgts($row_rel['id_clt']);
                                    }
                                }
                            }
                            ?>
                        </td>                       
                        <td align="right">
                            <?php
                            echo "R$ " . number_format($empresa['inss_empresa'], 2, ",", ".");
    //                                    echo "R$ " . number_format(($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.20, 2, ",", ".");
                            foreach ($status_array as $status_clt) {
                                if ($row_rel['codigo'] == $status_clt) {
                                    $total_inss_empresa_a_pagar[$status_clt] += $empresa['inss_empresa'];
                                }
                            }
                            ?>
                        </td>  
                        <td align="right">
                            <?php
                            echo "R$ " . number_format($empresa['inss_terceiro'], 2, ",", ".");
    //                                    echo "R$ " . number_format(($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.068, 2, ",", ".");
                            $total_inss_terceiro += $empresa['inss_terceiro'];
                            foreach ($status_array as $status_clt) {
                                if ($row_rel['codigo'] == $status_clt) {
                                    $total_inss_terceiro_a_pagar[$status_clt] += $empresa['inss_terceiro'];
                                }
                            }
                            ?>
                        </td>  
                        <td align="right">
                            <?php
                            if ($_COOKIE['logado'] == 0) {
                                echo $row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] + $row_rel['insalubridade'] + $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"];
                                echo "<br>";
                                echo "<br>";
                                echo "Saldo de Salário:" . $row_rel['saldo_salario'] . "<br>";
                                echo "Dt Salário:" . $row_rel['dt_salario'] . "<br>";
                                echo "Movimentos Incide:" . $movimentos_incide . "<br>";
                                echo "Saldo de salario 13°:" . $row_rel['terceiro_ss'] . "<br>";
                                echo "Lei:" . $row_rel['lei_12_506'] . "<br>";
                                echo "Aviso:" . $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] . "<br>";
                                echo "<br>";
                            }

                            echo "R$ " . number_format($empresa['fgts_recolher'], 2, ",", ".");
                            $total_fgts_recolher += $empresa['fgts_recolher'];
    //                                    echo "R$ " . number_format(($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] + $row_rel['insalubridade'] + $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"]) * 0.08, 2, ",", ".");
    //                                    $total_fgts_recolher += ($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] + $row_rel['insalubridade'] + $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"]) * 0.08;
                            foreach ($status_array as $status_clt) {
                                if ($row_rel['codigo'] == $status_clt) {
                                    $total_fgts_recolher_a_pagar[$status_clt] += $empresa['fgts_recolher'];
                                }
                            }


                            ?>
                        </td>
                    </tr>                                

                <?php } 

                $total_recisao_nao_paga += $total_liquido;
                /*
                 * Impressão dos totalizadores de grupo da tabela
                 */
                ?>
                <tfoot>
                    <tr class="footer">
                        <td align="right" colspan="6">Total:</td>
                        <td align="right"><?php echo "R$ " . number_format($total_das_medias_outras_remuneracoes, 2, ",", "."); ?></td>
                        <td align="right"><?php echo "R$ " . number_format($total_sal_base, 2, ",", "."); ?></td>
                        <!--<td align="right"><?php echo "R$ " . number_format($total_valor_aviso, 2, ",", "."); ?></td>-->
                        <td align="right"><?php echo "R$ " . number_format($total_saldo_salario, 2, ",", "."); ?></td>

                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_comissoes, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_gratificacao, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_insalubridade, 2, ",", "."); ?></td> 
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_periculosidade, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_adicional_noturno, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_hora_extra, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_gorjetas, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_dsr, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_reflexo_dsr, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_multa_477, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_multa_479, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_sal_familia, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_dt_salario, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_terceiro_exercicio, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ferias_pr, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_fp, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ferias_aquisitivas, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_fv, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_terco_constitucional, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_aviso_indenizado, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_terceiro_ss, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_f_aviso_indenizado, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_f_dobro, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_f_dobro, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_ferias_aviso, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_diferenca_salarial, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ajuda_custo, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_lei_12_506, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_dif_dissidio, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_vale_transporte, 2, ",", "."); ?></td>
                        <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ajuste_de_saldo, 2, ",", "."); ?></td>
                        <td align="right"><?php echo "R$ " . number_format($total_grupo_rendimento[$status], 2, ",", "."); ?></td>


                        <!-- DEDUÇÕES  -->
                        <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_pensao_alimenticia, 2, ",", "."); ?></td>
                        <td align="right" class="esconderr" ><?php echo "R$ " . number_format($total_adiantamento_salarial, 2, ",", "."); ?></td>
                        <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_adiantamento_13_salarial, 2, ",", "."); ?></td>
                        <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_aviso_indenizado_debito, 2, ",", "."); ?></td>
                        <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_multa_480, 2, ",", "."); ?></td>
                        <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_emprestimo_consignado, 2, ",", "."); ?></td>
                        <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_vale_transporte_debito, 2, ",", "."); ?></td>
                        <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_vale_alimentacao_debito, 2, ",", "."); ?></td>
                        <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_inss_ss, 2, ",", "."); ?></td>
                        <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_inss_dt, 2, ",", "."); ?></td>
                        <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_ir_ss, 2, ",", "."); ?></td>
                        <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_ir_dt, 2, ",", "."); ?></td>
                        <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_devolucao, 2, ",", "."); ?></td>
                        <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_outros, 2, ",", "."); ?></td>
                        <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_adiantamento_13, 2, ",", "."); ?></td>
                        <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_faltas, 2, ",", "."); ?></td>
                        <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_ir_ferias, 2, ",", "."); ?></td>
                        <td align="right"><?php echo "R$ " . number_format($total_grupo_deducao[$status], 2, ",", "."); ?></td>
                        <td align="right"><?php echo "R$ " . number_format($total_grupo_rendimento[$status] - $total_grupo_deducao[$status], 2, ",", "."); ?></td>


                        <!-- DETALHES IMPORTANTES-->
                        <td align="right" class="esconderrr"><?php echo "R$ " . number_format($total_base_inss, 2, ",", "."); ?></td>
                        <td align="right" class="esconderrr"><?php echo "R$ " . number_format($total_base_fgts, 2, ",", "."); ?></td>
                        <td align="right" class="esconderrr"><?php echo "R$ " . number_format($total_base_pis, 2, ",", "."); ?></td>
                        <td align="right" style="background: #fff; border: 0px;"></td>                       
                        <td align="right"><?php echo "R$ " . number_format($total_pis, 2, ",", "."); ?></td>                       
                        <td align="right"><?php echo "R$ " . number_format($total_multa_fgts, 2, ",", "."); ?></td>                       
                        <td align="right"><?php echo "R$ " . number_format($total_inss_empresa, 2, ",", "."); ?></td> 
                        <td align="right"><?php echo "R$ " . number_format($total_inss_terceiro, 2, ",", "."); ?></td> 
                        <td align="right"><?php echo "R$ " . number_format($total_fgts_recolher, 2, ",", "."); ?></td> 
                    </tr>
                </tfoot>
            </table>
            <?php 
            /*
             * Impressão do div com totalizadores de grupo
             */

            foreach ($status_array as $status_clt) { 
            ?>
                <div class="totalizador">
                    <p class="titulo">TOTALIZADORES (<?php echo $nome_status_array[$status_clt]; ?>)</p>
                    <p>PIS: <span><?php
                            echo "R$ " . number_format($total_pis_a_pagar[$status_clt], 2, ",", ".");
                            $total_geral_pis += $total_pis_a_pagar[$status_clt];
                            ?></span></p>
                    <p>GRRF: <span><?php
                            echo "R$ " . number_format($total_multa_a_pagar[$status_clt], 2, ",", ".");
                            $total_geral_multa += $total_multa_a_pagar[$status_clt];
                            ?></span></p>
                    <p>FGTS RECOLHER: <span><?php
                            echo "R$ " . number_format($total_fgts_recolher_a_pagar[$status_clt], 2, ",", ".");
                            $total_geral_fgts_recolher += $total_fgts_recolher_a_pagar[$status_clt];
                            ?></span></p>
                    <p>INSS RECOLHER EMPRESA: <span><?php
                            echo "R$ " . number_format($total_inss_empresa_a_pagar[$status_clt], 2, ",", ".");
                            $total_geral_inss_emp += $total_inss_empresa_a_pagar[$status_clt];
                            ?></span></p>
                    <p>INSS RECOLHER TERCEIRO: <span><?php
                            echo "R$ " . number_format($total_inss_terceiro_a_pagar[$status_clt], 2, ",", ".");
                            $total_geral_inss_terceiro += $total_inss_terceiro_a_pagar[$status_clt];
                            ?></span></p>

                    <p class="semborda">(+) SUBTOTAL: <span><?php
                            echo "R$ " . number_format($total_pis_a_pagar[$status_clt] + $total_multa_a_pagar[$status_clt] + $total_inss_empresa_a_pagar[$status_clt] + $total_inss_terceiro_a_pagar[$status_clt] + $total_fgts_recolher_a_pagar[$status_clt], 2, ",", ".");
                            $sub_total_geral += $total_pis_a_pagar[$status_clt] + $total_multa_a_pagar[$status_clt] + $total_inss_empresa_a_pagar[$status_clt] + $total_inss_terceiro_a_pagar[$status_clt] + $total_fgts_recolher_a_pagar[$status_clt];
                            ?></span></p>
                    <p>(+) TOTAL A SER PAGO(RESCISÕES): <span><?php
                            // Total a ser pago
                            $total_geral_a_ser_pago += ($total_a_ser_pago[$status_clt] += $total_grupo_rendimento[$status_clt] - $total_grupo_deducao[$status_clt]);
                            echo "R$ " . number_format($total_a_ser_pago[$status_clt], 2, ",", ".");
                            ?></span></p>
                    <p class="semborda">(=) TOTAL: <span><?php
                            echo "R$ " . number_format($total_pis_a_pagar[$status_clt] + $total_multa_a_pagar[$status_clt] + $total_inss_empresa_a_pagar[$status_clt] + $total_inss_terceiro_a_pagar[$status_clt] + $total_fgts_recolher_a_pagar[$status_clt] + $total_a_ser_pago[$status_clt], 2, ",", ".");
                            $total_geral += $total_pis_a_pagar[$status_clt] + $total_multa_a_pagar[$status_clt] + $total_inss_empresa_a_pagar[$status_clt] + $total_inss_terceiro_a_pagar[$status_clt] + $total_fgts_recolher_a_pagar[$status_clt] + $total_a_ser_pago[$status_clt];
                            ?></span></p>
                </div>
            <?php 
            } 
            ?>

            <div class="totalizador">
                <p class="titulo">TOTALIZADOR GERAL</p>
                <p>PIS: <span><?php echo "R$ " . number_format($total_geral_pis, 2, ",", "."); ?></span></p>
                <p>GRRF: <span><?php echo "R$ " . number_format($total_geral_multa, 2, ",", "."); ?></span></p>
                <p>FGTS RECOLHER: <span><?php echo "R$ " . number_format($total_geral_fgts_recolher, 2, ",", "."); ?></span></p>
                <p>INSS RECOLHER EMPRESA: <span><?php echo "R$ " . number_format($total_geral_inss_emp, 2, ",", "."); ?></span></p>
                <p>INSS RECOLHER TERCEIRO: <span><?php echo "R$ " . number_format($total_geral_inss_terceiro, 2, ",", "."); ?></span></p>

                <p class="semborda">(+) SUBTOTAL: <span><?php echo "R$ " . number_format($sub_total_geral, 2, ",", "."); ?></span></p>
                <p>(+) TOTAL A SER PAGO(RESCISÕES): <span><?php echo "R$ " . number_format($total_geral_a_ser_pago, 2, ",", "."); ?></span></p>
                <p class="semborda">(=) TOTAL: <span><?php echo "R$ " . number_format($sub_total_geral + $total_geral_a_ser_pago, 2, ",", "."); ?></span></p>
                <p class="semborda">MARGEM DE ERRO DE 1% : <span ><?php echo "R$ " . number_format(($sub_total_geral + $total_geral_a_ser_pago) + (($sub_total_geral + $total_geral_a_ser_pago) * 0.01), 2, ",", "."); ?></span></p>
            </div>
        </div>    
        
        <footer>
            <div>
                <p>Pay All Fast 3.0 build 1831 - <?=date('d/m/Y - H:i')?></p>
                <p>Todos os direitos reservados <a href="http://f71.com.br" rel="nofollow" target="_blank">F71 Sistemas</a>.</p>
            </div>
        </footer>    
    
    </body>
</html>
