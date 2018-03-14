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
list($regiao) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
// ---- FINALIZANDO MASTER -----------------

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

    $dt_refIni = $_REQUEST['anoini'] . "-" . str_pad($_REQUEST['mesini'], 2, "0", STR_PAD_LEFT) . "-01";
    $dt_refFim = date("Y-m-d", strtotime($dt_refIni . " + 6 month"));

    $where = "tipo = 'despesa' AND data_referencia BETWEEN '{$dt_refIni}' AND '{$dt_refFim}' AND id_projeto = {$_REQUEST['projeto']}";

    $rows = montaQuery("prestacoes_contas", "id_prestacao,DATE_FORMAT(data_referencia, '%Y') as ano, DATE_FORMAT(data_referencia, '%m') as mes ", $where, "data_referencia", 6, "array");
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

    $whereData = "month(data_vencimento) = {$_REQUEST['mes']} AND year(data_vencimento) = {$_REQUEST['ano']}";
    $completeWhere = $whereData . " AND id_banco={$_REQUEST['banco']} AND `status` = 2 AND estorno IN (0,2)";
    $mesShow = mesesArray($_REQUEST['mes']) . "/" . $_REQUEST['ano'];

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
    echo "<!-- {$qt_totalfinal} -->\r\n";

    $arrCodigos = array();
    $matriz = array();
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
            $matriz[$row['id_prestacao']][$cod] = number_format($totais[$row['id_prestacao']][$row['id_grupo']], 2, ",", ".");
            $coutn++;
        }

        // CÓDIGOS DOS SUBGRUPOS EX 01.01
        if ($antesSubGrupo[$row['id_prestacao']] != $row['id_subgrupo']) {
            $antesSubGrupo[$row['id_prestacao']] = $row['id_subgrupo'];
            $cod = $row['id_subgrupo'];
            $arrCodigos[$cod]['nome'] = $row['subgrupo'];
            $arrCodigos[$cod]['codigo'] = $cod;
            $matriz[$row['id_prestacao']][$cod] = number_format($subtotais[$row['id_prestacao']][$row['idsub']], 2, ",", ".");
            $coutn++;
        }

        //CÓDIGO NORMAIS EX 01.01.01
        if (!in_array($row['cod'], $arrCodigos)) {
            $arrCodigos[$row['cod']]['nome'] = $row['nome'];
            $arrCodigos[$row['cod']]['codigo'] = $row['cod'];
        }

        //CONTEÚDO DAS COLUNAS GERADAS DINAMICAMENTE (VALORES DOS MESES SELECIONADOS)
        if (!empty($row['id_prestacao'])) {
            $matriz[$row['id_prestacao']][$row['cod']] = number_format($row['total'], 2, ",", ".");
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
    /*
      $qr_totais = $qrBase." GROUP BY A.id_grupo";
      $result_totais = mysql_query($qr_totais);
      $totais = array();
      while ($row_total = mysql_fetch_assoc($result_totais)) {
      $totais[$row_total['id_grupo']] = $row_total['total'];
      }


      $qr_subtotais = $qrBase." GROUP BY B.id";
      $result_subtotais = mysql_query($qr_subtotais);
      $subtotais = array();
      while ($row_subtotal = mysql_fetch_assoc($result_subtotais)) {
      $subtotais[$row_subtotal['idsub']] = $row_subtotal['total'];
      }

      //print_r($subtotais);exit;

      $qt_totalfinal = "SELECT SUM(CAST(
      REPLACE(total, ',', '.') AS DECIMAL(13,2))) AS total
      FROM ({$qrBase}) as q";

      $result_totalfinal = mysql_query($qt_totalfinal);
      $row_totalfinal = mysql_fetch_assoc($result_totalfinal);

      $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = {$_REQUEST['projeto']}");
      $projeto = mysql_fetch_assoc($qr_projeto);

      $qr_master = mysql_query("SELECT * FROM master WHERE id_master = {$projeto['id_master']}");
      $master = mysql_fetch_assoc($qr_master);

      echo "<!--" . $qr . "-->\n\r";
      echo "<!--" . $qr_totais . "-->\n\r";
      echo "<!--" . $qr_subtotais . "-->\n\r";
      echo "<!--" . $qt_totalfinal . "-->\n\r"; */
    
    //ALIMENTANDO A VARIAVEL DO GRAFICO
    foreach($arMesesS as $k => $val){
        //$valor = rand(9000,10000);
        $valor = str_replace(".", "", $matriz[$k]["02"]);
        array_push($chart, array('mes'=>$arMeses[$k],'pessoal'=>$valor));
    }
    $chartLabel = array("pessoal","Pessoal");
    $chartJs = json_encode($chart);
}

$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_assoc($qr_regiao);
$id_master = $row_regiao['id_master'];

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_master = '$id_master' AND status_reg = 1 ORDER BY nome");
$projetos = array("-1" => "« Selecione »");
while ($row_projeto = mysql_fetch_assoc($qr_projeto)) {
    //POG PARA BLOQUEAR VIAMÃO   
    if (($_COOKIE['logado'] == 161 or $_COOKIE['logado'] == 178 or $_COOKIE['logado'] == 180) and $row_projeto['id_projeto'] == 3305)
        continue;

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
            #resposta ul{
                list-style: none;
                list-style: none;
                margin: 0;
                padding: 0;
            }
            #resposta {
                display: inline-block;
                height: 100px;
            }
        </style>
        <script>
            $(function(){
                $("#form1").validationEngine();
                
                $("#projeto").change(function(){
                    var $this = $(this);
                    if($this.val() != "-1"){
                        $.post('rel_detalhado.php', { projeto: $this.val(), method: "loadbancos" }, function(data) {
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
            $(function ()  
            {
                /*var dataSource = [
                    { mes: "Jan", pessoal: 150.22 },
                    { mes: "Fev", pessoal: 160.12 },
                    { mes: "Mar", pessoal: 100.55  },
                    { mes: "Abr", pessoal: 57.65 },
                    { mes: "Mai", pessoal: 15.15 },
                    { mes: "Jun", pessoal: 300.00 }
                ];*/
                var dataSource = <?php echo $chartJs;?>;

                var chart = $("#chartContainer").dxChart({
                    dataSource: dataSource,
                    commonSeriesSettings: {
                        type: 'spline',
                        argumentField: 'mes'
                    },
                    commonAxisSettings: {
                        grid: {
                            visible: true
                        }
                    },
                    series: [
                        { valueField: '<?php echo $chartLabel[0]?>', name: '<?php echo $chartLabel[1]?>' }
                    ],
                    tooltip:{
                        enabled: true
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
            }
        );
        </script>
    </head>
    <body id="page-despesas" class="novaintra">
        <div id="content" style="overflow: hidden; width: 90%;">
            <form action="" method="post" name="form1" id="form1">
                <input type="hidden" name="bancSel" id="bancSel" value="<?php echo $bancoR ?>" />
                <input type="hidden" name="prestSel" id="prestSel" value="<?php echo $prestacoesR ?>" />
                <h2>RELATÓRIO COMPARATIVO DETALHADO</h2>

                <fieldset>
                    <legend>Dados</legend>
                    <p><label class="first">Projeto:</label> <?php echo montaSelect($projetos, $projetoR, $attrPro) ?></p>
                    <p><label class="first">Banco:</label> <?php echo montaSelect(array("-1" => "« Selecione o projeto »"), null, "id='banco' name='banco' class='validate[custom[select]]'") ?></p>
                    <p><label class="first">Mês Inicio:</label> <?php echo montaSelect($meses, $mesR, "id='mesini' name='mesini' class='validate[custom[select]]'") ?>  <?php echo montaSelect($anos, $anoR, "id='anoini' name='anoini' class='validate[custom[select]]'") ?> </p>
                    <p><label class="first">Selecione:</label> <span id="resposta"></span> </p>
                    <p class="controls clear"> <input type="submit" class="button" value="Filtrar" name="filtrar" /> </p>
                </fieldset>

                <?php if (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) { ?>
                    <?php if (!empty($result) && mysql_num_rows($result) > 0) { ?>
                        <br/><br/>

                        <div id="dvTable">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="grid">
                                <thead>
                                    <tr>
                                        <th>CÓDIGO</th>
                                        <th>DESCRIÇÃO</th>
                                        <?php
                                        foreach ($arMesesS as $k => $mes) {
                                            echo "<th>" . $arMeses[$k] . "</th>";
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($arrCodigos as $codigo) {
                                        $len = strlen($codigo['codigo']);
                                        $art = "";
                                        $clas = ($len < 6) ? " class='subtitulo'" : "";
                                        if ($len > 2)
                                            $art = (strlen($codigo['codigo']) > 6) ? "<span class='artificio2'>----</span>" : "<span class='artificio1'>--</span>";

                                        echo "<tr$clas><td>{$art}{$codigo['codigo']}</td><td>{$codigo['nome']}</td>";

                                        foreach ($arMesesS as $k => $mes) {
                                            $valor = (!empty($matriz[$k][$codigo['codigo']])) ? $matriz[$k][$codigo['codigo']] : "-";
                                            echo "<td class='txright'>" . $valor . "</td>";
                                        }
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="txright">Total:</td>
                                        <?php
                                        foreach ($arMesesS as $k => $mes) {
                                            echo "<td class='txright'>" . number_format($totaisFinais[$k], 2, ",", ".") . "</td>";
                                        }
                                        ?>
                                    </tr>
                                </tfoot>
                            </table>

                            <!-- CHART -->

                            <div class="pane">
                                <div class="long-title"><h3></h3></div>
                                <div id="chartContainer" style="width: 700px; height: 440px;"></div>
                            </div>

                            <!-- CHART FIM -->

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