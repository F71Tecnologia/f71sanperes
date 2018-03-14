<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}
error_reporting(E_ALL);
include "../include/restricoes.php";
include "../../conn.php";
include "../../funcoes.php";
include "../../wfunction.php";

$arMeses = null;
$chart = array();
$usuario = carregaUsuario();
$regiao = $usuario['id_regiao'];

/* CARREGA OS BANCOS VIA AJAX, RETORNA UM JSON */
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "loadbancos") {
    $return['status'] = 1;

    $qr_bancos = mysql_query("SELECT * FROM bancos WHERE id_projeto = '{$_REQUEST['projeto']}' AND status_reg=1");
    $num_rows = mysql_num_rows($qr_bancos);
    $bancos = array();
    if ($num_rows > 0) {
        while ($row = mysql_fetch_assoc($qr_bancos)) {
            if ($_COOKIE['logado'] == 161 and $row_banco['id_banco'] == 107)
                continue;
            $bancos[$row['id_banco']] = utf8_encode($row['nome']);
        }
    } else {
        $bancos["-1"] = "Banco não encontrado";
    }

    $return['options'] = $bancos;

    echo json_encode($return);
    exit;
}

/* CALCULA OS MESES VIA AJAX, RETORNA UM HTML */
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "loadMes") {
    $html = "<ul>";
    $meses = 12;
    $dt_refIni = $_REQUEST['anoini'] . "-" . str_pad($_REQUEST['mesini'], 2, "0", STR_PAD_LEFT) . "-01";
    $dt_refFim = date("Y-m-d", strtotime($dt_refIni . " + {$meses} month"));

    $where = "tipo = 'despesa' AND data_referencia BETWEEN '{$dt_refIni}' AND '{$dt_refFim}' AND id_projeto = {$_REQUEST['projeto']}";

    $rows = montaQuery("prestacoes_contas", "id_prestacao,DATE_FORMAT(data_referencia, '%Y') as ano, DATE_FORMAT(data_referencia, '%m') as mes ", $where, "data_referencia", $meses, "array");
    if (count($rows)) {
        foreach ($rows as $linhas) {
            $mes = utf8_encode(mesesArray($linhas['mes']));
            $html .= "<li><input type='checkbox' name='prestacoes[]' id='prestacoes{$linhas['id_prestacao']}' value='{$linhas['id_prestacao']}' /> <label for='prestacoes{$linhas['id_prestacao']}'>{$mes} de {$linhas['ano']}</spa></li>";
        }
    } else {
        $html = utf8_encode("Não foi encontrado");
    }

    echo $html;
    exit;
}

/* RECEBE AS INFORMÇÕES PRA MONTAR O SELECT */
if (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) {

    $arPrestacoes = $_REQUEST['prestacoes'];
    $qrBase = "SELECT A.id_grupo,B.id as idsub,A.nome_grupo,B.id_subgrupo,B.nome AS subgrupo,C.cod,C.nome,C.id_entradasaida,D.id_prestacao,DATE_FORMAT(D.ano_mes_ref, '%m') as mes, DATE_FORMAT(D.ano_mes_ref, '%Y') as ano,
                    COUNT(D.id_saida) AS qnt,
                    SUM(D.valor) as total
                    FROM entradaesaida_grupo AS A
                    LEFT JOIN entradaesaida_subgrupo AS B ON (A.id_grupo=B.entradaesaida_grupo)
                    LEFT JOIN entradaesaida AS C ON (LEFT(C.cod,5)=B.id_subgrupo)
                    LEFT JOIN (SELECT id_saida,id_tipo,valor,id_prestacao,ano_mes_ref FROM prestacoes_contas_desp WHERE id_prestacao IN (" . implode(',', $arPrestacoes) . ")) AS D ON (D.id_tipo=C.id_entradasaida)
                    WHERE C.id_entradasaida >= 154 AND C.cod != '06.03.01'";

    $qr = $qrBase . " GROUP BY D.id_prestacao,C.id_entradasaida ORDER BY C.cod";
    $result = mysql_query($qr);

    $qr_totais = $qrBase . " GROUP BY D.id_prestacao,A.id_grupo";
    $result_totais = mysql_query($qr_totais);
    $totais = array();
    while ($row_total = mysql_fetch_assoc($result_totais)) {
        $totais[$row_total['id_prestacao']][$row_total['id_grupo']] = $row_total['total'];
    }

    $qr_subtotais = $qrBase . " GROUP BY D.id_prestacao,B.id";
    $result_subtotais = mysql_query($qr_subtotais);
    $subtotais = array();
    while ($row_subtotal = mysql_fetch_assoc($result_subtotais)) {
        $subtotais[$row_subtotal['id_prestacao']][$row_subtotal['idsub']] = $row_subtotal['total'];
    }

    $qr_totaisFinais = $qrBase . " GROUP BY D.id_prestacao";
    $result_totaisFinais = mysql_query($qr_totaisFinais);
    $totaisFinais = array();
    while ($row_totalFinal = mysql_fetch_assoc($result_totaisFinais)) {
        $totaisFinais[$row_totalFinal['id_prestacao']] = $row_totalFinal['total'];
    }

    echo "<!-- {$qr} -->\r\n";
    echo "<!-- {$qr_totais} -->\r\n";
    echo "<!-- {$qr_subtotais} -->\r\n";

    $arrCodigos = array();
    $matriz = array();
    $matrizTotais = array();
    $matrizChart = array();
    $antesGrupo = array();
    $antesSubGrupo = "";
    $coutn = 0;
    while ($row = mysql_fetch_assoc($result)) {
        // CÓDIGOS DOS GRUPOS EX 01
        if ($antesGrupo[$row['id_prestacao']] != $row['id_grupo']) {
            $antesGrupo[$row['id_prestacao']] = $row['id_grupo'];
            $cod = str_pad(str_replace("0", "", $row['id_grupo']), 2, "0", STR_PAD_LEFT);
            $arrCodigos[$cod]['nome'] = $row['nome_grupo'];
            $arrCodigos[$cod]['codigo'] = $cod;
            $matriz[$row['id_prestacao']][$cod] = $totais[$row['id_prestacao']][$row['id_grupo']];
            $coutn++;
        }

        // CÓDIGOS DOS SUBGRUPOS EX 01.01
        if ($antesSubGrupo[$row['id_prestacao']] != $row['id_subgrupo']) {
            $antesSubGrupo[$row['id_prestacao']] = $row['id_subgrupo'];
            $cod = $row['id_subgrupo'];
            $codChart = str_replace(".", "", $cod);
            $arrCodigos[$cod]['nome'] = $row['subgrupo'];
            $arrCodigos[$cod]['codigo'] = $cod;
            $matriz[$row['id_prestacao']][$cod] = $subtotais[$row['id_prestacao']][$row['idsub']];
            $matrizChart[$codChart] = $cod." - ".$row['subgrupo'];
            $coutn++;
        }

        //CÓDIGO NORMAIS EX 01.01.01
        if (!in_array($row['cod'], $arrCodigos)) {
            $arrCodigos[$row['cod']]['nome'] = $row['nome'];
            $arrCodigos[$row['cod']]['codigo'] = $row['cod'];
        }

        //CONTEÚDO DAS COLUNAS GERADAS DINAMICAMENTE (VALORES DOS MESES SELECIONADOS)
        if (!empty($row['id_prestacao'])) {
            $matriz[$row['id_prestacao']][$row['cod']] = $row['total'];
        }

        //GERANDO ARRAY COM OS NOMES DOS MESES SELECIONADOS (CHAVE PARA TODOS OS OUTROS ARRAYS)
        if (!empty($row['id_prestacao']) && !in_array($row['id_prestacao'], $arMeses)) {
            $arMeses[$row['id_prestacao']] = mesesArray($row['mes']) . " " . $row['ano'];
            $arMesesS[$row['id_prestacao']] = $row['ano'] . "-" . $row['mes'] . "-01";
        }
        $coutn++;
    }

    asort($arMesesS);
    //echo "<pre>";
    //print_r($arrCodigos);
    //print_r($matriz['79']);

    //ALIMENTANDO A VARIAVEL DO GRAFICO
    $chartLabel = array();
    array_push($chartLabel, array("valueField" => "consumo", "name" => "Item Selecionado"));
    $chartLb = json_encode($chartLabel);
    
    $rsPro = mysql_query("SELECT nome FROM projeto WHERE id_projeto = {$_REQUEST['projeto']}");
    $linhaPro = mysql_fetch_assoc($rsPro);
}

$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_assoc($qr_regiao);
$id_master = $row_regiao['id_master'];

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao IN (SELECT id_regiao FROM funcionario_regiao_assoc WHERE id_funcionario = {$usuario['id_funcionario']} AND id_master = '{$id_master}') AND status_reg = 1 ORDER BY nome");
$projetos = array("-1" => "« Selecione »");
while ($row_projeto = mysql_fetch_assoc($qr_projeto)) {
    $projetos[$row_projeto['id_projeto']] = $row_projeto['id_projeto'] . " - " . $row_projeto['nome'];
}
$attrPro = array("id" => "projeto", "name" => "projeto", "class" => "validate[custom[select]]");
$meses = mesesArray("id_nome,nome", "rh_clt", "id_regiao='45'");
$anos = anosArray(null, null, array("-1" => "« Selecione »"));

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$bancoR = (isset($_REQUEST['banco'])) ? $_REQUEST['banco'] : null;
$mesR = (isset($_REQUEST['mesini'])) ? $_REQUEST['mesini'] : null;
$anoR = (isset($_REQUEST['anoini'])) ? $_REQUEST['anoini'] : null;
$prestacoesR = (isset($_REQUEST['prestacoes'])) ? implode(",", $_REQUEST['prestacoes']) : null;
?>
<html>
    <head>
        <title>:: Intranet :: RELATÓRIO COMPARATIVO DETALHADO</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>

        <script src="../../js/chartjs/knockout-2.2.1.js"></script>
        <script src="../../js/chartjs/globalize.js"></script>
        <script src="../../js/chartjs/dx.chartjs.js"></script>

        <script src="../../js/global.js" type="text/javascript"></script>
        <style>
            #dvTable{position: relative;}
            #tabs{overflow: auto;}
            #resposta ul{
                list-style: none;
                list-style: none;
                margin: 0;
                padding: 0;
            }
            #resposta li{
                float: left;
                margin: 5px 5px;
            }
            
            @media print{
                .ui-tabs-nav, fieldset, #pbuttons{
                    display: none;
                }
                #tabs, #tabs-1, #dvTable{
                    width: 200px;
                }
                .grid thead tr th, .grid tbody tr td{
                    font-size: 8px;
                }
            }
            
        </style>
        <script type="text/javascript">
            var formatMoney = function(value, c, d, t){
                var n = value, 
                c = isNaN(c = Math.abs(c)) ? 2 : c, 
                d = d == undefined ? "." : d, 
                t = t == undefined ? "," : t, 
                s = n < 0 ? "-" : "", 
                i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
                j = (j = i.length) > 3 ? j % 3 : 0;
                return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
            };
            
            $(function(){
                $( "#tabs" ).tabs();
                
                $("#form1").validationEngine();
                
                $("#projeto").change(function(){
                    var $this = $(this);
                    if($this.val() != "-1"){
                        $.post('rel_comparativo.php', { projeto: $this.val(), method: "loadbancos" }, function(data) {
                            if(data.status==1){
                                var opcao = "";
                                var selected = "";
                                for (var i in data.options){
                                    selected = "";
                                    if(i==$("#bancSel").val()){
                                        selected = "selected=\"selected\" ";
                                    }
                                    opcao += "<option value='" + i + "' " + selected + ">" + data.options[i] + "</option>";
                                }
                                $("#banco").html(opcao);
                            }
                        },"json");
                    }
                }).trigger("change");
                
                $("#mesini").change(function(){
                    if($(this).val() != "-1" && $("#anoini").val() != "-1" && $("#projeto").val() != "-1")
                        calculaMeses();
                });
                $("#anoini").change(function(){
                    if($(this).val() != "-1" && $("#mesini").val() != "-1" && $("#projeto").val() != "-1")
                        calculaMeses();
                }).trigger("change");
                
                $("#linktabs2").click(function(){
                    $("#loadChart").trigger("click");
                });
                
                $("#seltudo").click(function(){
                    var marca = $("#seltudo:checked").length;
                    $(":checkbox[id*=prestacoes]").each(function() {
                        this.checked = marca;
                    });
                });
                
                $("#loadChart").click(function(){
                    montaGrafico($("#tipoChart").val());
                });
                
                $("#botaoExcel").click(function() {
                    $("#geraExcel").val( $("<div>").append( $(".grid").eq(0).clone()).html() );
                    $("#exportaExcel").submit();
                });

            });
            
            var calculaMeses = function(){
                $.post('rel_comparativo.php', { mesini: $("#mesini").val(), anoini: $("#anoini").val(), projeto: $("#projeto").val(), method: "loadMes" }, function(data) {
                    $("#resposta").html(data);
                    
                    if($("#prestSel").val() != ""){
                        var prest = $("#prestSel").val().split(",");
                        for(i=0; i <= prest.length; i++){
                            $("#prestacoes"+prest[i]).attr('checked','checked');
                        }
                    }
                },"html");
            }
        </script>

        <script type="text/javascript">
            var montaGrafico = function(num)
            {
                var myvars = new Array();
                <?php
                //ALIMENTANDO A VARIAVEL DO GRAFICO
                foreach ($arrCodigos as $codigo) {
                    $len = strlen($codigo['codigo']);
                    $codJs = str_replace(".","",$codigo['codigo']);
                    $chart=array();
                    if ($len == 5){
                        foreach ($arMesesS as $k => $mes) {
                            $valor = (!empty($matriz[$k][$codigo['codigo']])) ? $matriz[$k][$codigo['codigo']] : 0;
                            array_push($chart, array('mes' => utf8_encode($arMeses[$k]), 'consumo' => (float) $valor));
                        }
                        echo "myvars['{$codJs}'] = ".json_encode($chart).";\r\n";
                    }
                }
                ?>
                
                var chart = $("#chartContainer").dxChart({
                    dataSource: myvars[num],
                    commonSeriesSettings: {
                        type: 'spline',
                        argumentField: 'mes'
                    },
                    commonAxisSettings: {
                        grid: {
                            visible: true
                        }
                    },
                    series: <?php echo $chartLb; ?>,
                    tooltip:{
                        enabled: true,
                        customizeText: function () {
                            return 'R$ '+formatMoney(this.valueText, 2, ',', '.');
                        }
                    },
                    legend: {
                        verticalAlignment: 'bottom',
                        horizontalAlignment: 'center'
                    },
                    title: 'Comparativo Mensal',
                    commonPaneSettings: {
                        border:{
                            visible: true,
                            bottom: false
                        }
                    }
                });
            };
        </script>
    </head>
    <body id="page-despesas" class="novaintra">
        <div id="content" style="overflow: hidden; width: 90%;">
            <form action="" method="post" name="exportaExcel" id="exportaExcel">
                <input type="hidden" name="geraExcel" id="geraExcel" value="" />
            </form>
            
            <form action="" method="post" name="form1" id="form1">
                <input type="hidden" name="bancSel" id="bancSel" value="<?php echo $bancoR ?>" />
                <input type="hidden" name="prestSel" id="prestSel" value="<?php echo $prestacoesR ?>" />
                
                <h2>RELATÓRIO COMPARATIVO DETALHADO</h2>

                <fieldset>
                    <legend>Dados</legend>
                    <div class="fleft">
                        <p><label class="first">Projeto:</label> <?php echo montaSelect($projetos, $projetoR, $attrPro) ?></p>
                        <p><label class="first">Banco:</label> <?php echo montaSelect(array("-1" => "« Selecione o projeto »"), null, "id='banco' name='banco' class='validate[custom[select]]'") ?></p>
                        <p><label class="first">Mês Inicio:</label> <?php echo montaSelect($meses, $mesR, "id='mesini' name='mesini' class='validate[custom[select]]'") ?>  <?php echo montaSelect($anos, $anoR, "id='anoini' name='anoini' class='validate[custom[select]]'") ?> </p>
                    </div>
                    <div class="fleft" style="width: 70%; margin-left: 15px;">
                        <p><label style="font-weight: bold;">Selecione:</label>
                            <input type="checkbox" name="seltudo" id="seltudo" value="0" /> <label for="seltudo">Selecionar Todos</label>
                            <span id="resposta"></span><br class="clear"/> </p>
                    </div>
                    <p class="controls clear"> <input type="submit" class="button" value="Filtrar" name="filtrar" /> </p>
                </fieldset>

                <?php if (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) { ?>
                    <?php if (!empty($result) && mysql_num_rows($result) > 0) { ?>
                        <br/><br/>
                        <div id="tabs">
                            <ul>
                                <li><a href="#tabs-1" id="linktabs1">Relatório</a></li>
                                <li><a href="#tabs-2" id="linktabs2">Gráficos</a></li>
                            </ul>
                            <div id="tabs-1">
                                <div id="dvTable">
                                    <div class="left"><h3><?php echo $linhaPro['nome']; ?></h3></div>
                                    <p id="pbuttons" style="text-align: right">
                                        <input type="button" onclick="tableToExcel('toexcel', 'Comparativo')" value="Exportar para Excel" class="exportarExcel">
                                        <input type="button" id="imprimir" class="button" value="Imprimir" name="imprimir" onclick="window.print();" />
                                    </p>
                                    <table id="toexcel" border="0" cellpadding="0" cellspacing="0" width="100%" class="grid">
                                        <thead>
                                            <tr>
                                                <th width="78">CÓDIGO</th>
                                                <th>DESCRIÇÃO</th>
                                                <?php
                                                foreach ($arMesesS as $k => $mes) {
                                                    echo "<th>" . $arMeses[$k] . "</th>";
                                                }
                                                ?>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($arrCodigos as $codigo) {
                                                $len = strlen($codigo['codigo']);
                                                $art = "";
                                                $clas = ($len < 6) ? " class='subtitulo'" : "";
                                                if ($len > 2)
                                                    $art = (strlen($codigo['codigo']) > 6) ? "<span class='artificio2'></span>" : "<span class='artificio1'></span>";

                                                echo "<tr$clas><td>{$art}{$codigo['codigo']}</td><td>{$codigo['nome']}</td>";

                                                foreach ($arMesesS as $k => $mes) {
                                                    $valor = (!empty($matriz[$k][$codigo['codigo']])) ? number_format($matriz[$k][$codigo['codigo']],2,",",".") : "-";
                                                    $matrizTotais[$codigo['codigo']] += $matriz[$k][$codigo['codigo']];
                                                    echo "<td class='txright'>" . $valor . "</td>";
                                                }
                                                echo "<td class='txright'>".number_format($matrizTotais[$codigo['codigo']],2,",",".")."</td>";
                                                echo "</tr>";
                                            }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2" class="txright">Total:</td>
                                                <?php
                                                $totalFinal = 0;
                                                foreach ($arMesesS as $k => $mes) {
                                                    echo "<td class='txright'>" . number_format($totaisFinais[$k], 2, ",", ".") . "</td>";
                                                    $totalFinal += $totaisFinais[$k];
                                                }
                                                echo "<td class='txright'>".number_format($totalFinal, 2, ",", ".")."</td>";
                                                ?>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div id="tabs-2">
                                <fieldset>
                                    <p><label class="first">Tipo:</label>
                                        <?php echo montaSelect($matrizChart, null, "id='tipoChart' name='tipoChart'") ?>
                                        <input type="button" name="loadChart" id="loadChart" value="Carrega Gráfico" />
                                    </p>
                                </fieldset>
                                    
                                <div class="pane" style="margin: 0 auto; width: 700px;">
                                    <div class="long-title"><h3></h3></div>
                                    <div id="chartContainer" style="width: 700px; height: 460px;"></div>
                                </div>
                            </div>
                        <?php } else { ?>     
                            <div id='message-box' class='message-yellow'>
                                <p>Nenhum registro encontrado</p>
                            </div>
                        <?php } ?>
                    <?php } ?>
            </form>
        </div>
    </body>
</html>