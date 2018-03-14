<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}
error_reporting(E_ALL);
include "../../conn.php";
include "../../funcoes.php";
include "../../wfunction.php";

$usuario = carregaUsuario();
$regiao = $usuario['id_regiao'];
$master = $usuario['id_master'];
$message = false;

/* // Resgatar nomes que começam com "go", case-insensitive
  $names = array("Ramon","Ronaldo","Ramires","Rayane","Rodrigo");
  $t = "ra";
  $er = '/^'.$t.'/i';
  $pregGrep = preg_grep($er, $names);
  print_r($pregGrep);
  exit; */

session_start();
if (isset($_SESSION['MSG_MESSAGE']) && !empty($_SESSION['MSG_MESSAGE'])) {
    $message = $_SESSION['MSG_MESSAGE'];
    unset($_SESSION['MSG_MESSAGE']);
}

if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "salvaValor") {
    $return['status'] = true;
    $valor = str_replace(",", ".", str_replace(".", "", $_REQUEST['valor']));
    if (!sqlUpdate("projeto_propostas", array("valor" => $valor), "id_proposta={$_REQUEST['id']}")) {
        $return['status'] = false;
    }
    echo json_encode($return);
    exit;
}

if (isset($_REQUEST['cadproposta']) && !empty($_REQUEST['cadproposta'])) {
    $valor = str_replace(",", ".", str_replace(".", "", $_REQUEST['cadValor']));
    $dtIni = "{$_REQUEST['anoini']}-{$_REQUEST['mesini']}-01";
    $dtFim = "{$_REQUEST['anofim']}-{$_REQUEST['mesfim']}-30";

    $campos = array(
        "id_projeto",
        "id_grupo",
        "nome",
        "valor",
        "data_ini",
        "data_fim",
        "status"
    );

    $valores = array(
        $_REQUEST['cadprojeto'],
        $_REQUEST['cadGrupo'],
        $_REQUEST['cadNome'],
        $valor,
        $dtIni,
        $dtFim,
        "1"
    );

    sqlInsert("projeto_propostas", $campos, $valores);
    $_SESSION['MSG_MESSAGE'] = "Proposta cadastrada com sucesso";
    header("Location: rel_proposta.php");
    exit;
}

if (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) {
    //SELECIONANDO AS PRESTACOES DE CONTAS, PARA GERAR AS COLUNAS NA HORIZONTAL (JAN-FEV-MAR-ABR..)
    $dt_ini = $_REQUEST['sanoini'] . "-" . $_REQUEST['smesini'] . "-01";
    $dt_fim = date("Y-m-d", strtotime($dt_ini . " + 12 month"));
    $qr_prest = "SELECT * FROM prestacoes_contas WHERE id_projeto = {$_REQUEST['projeto']} AND tipo = 'despesa' AND data_referencia BETWEEN '{$dt_ini}' AND '{$dt_fim}' AND erros = 0 ORDER BY data_referencia";
    $rs_prest = mysql_query($qr_prest);
    $prestacaoContas = array();
    $prestQuery = array();
    while ($rowPrest = mysql_fetch_assoc($rs_prest)) {
        $prestacaoContas[$rowPrest['id_prestacao']]['prestacao'] = $rowPrest['id_prestacao'];
        $prestacaoContas[$rowPrest['id_prestacao']]['mes'] = date("m", strtotime($rowPrest['data_referencia']));
        $prestacaoContas[$rowPrest['id_prestacao']]['ano'] = date("Y", strtotime($rowPrest['data_referencia']));
        $prestQuery[] = $rowPrest['id_prestacao'];
    }
    $totalPrestacoes = count($prestacaoContas);

    //QUERY QUE RETORNA TODOS OS VALORES JA AGRUPADOS PELOS TIPOS, SEPARADOS PELAS PRESTACOES DE CONTAS
    $qr_base = "SELECT 
        A.id_proposta,A.codigof71,
        B.nome as proposta,B.id_grupo,
        C.cod,C.nome as entradsaida,C.id_entradasaida,
        D.id_saida,D.id_prestacao,
        E.id_projeto,E.data_referencia,
        SUM(D.valor) AS valor FROM projeto_propostas_assoc AS A
        LEFT JOIN projeto_propostas AS B ON (A.id_proposta=B.id_proposta)
        LEFT JOIN entradaesaida AS C ON (A.codigof71=C.codigof71)
        LEFT JOIN (SELECT * FROM prestacoes_contas_desp WHERE id_prestacao IN (" . implode(",", $prestQuery) . ")) AS D ON (D.id_tipo=C.id_entradasaida)
        LEFT JOIN prestacoes_contas AS E ON (D.id_prestacao=E.id_prestacao)
        WHERE B.id_projeto = {$_REQUEST['projeto']}
        GROUP BY D.id_prestacao,B.nome
        ORDER BY A.id_proposta";

    $rs_query = mysql_query($qr_base);
    $matriz = array();
    while ($row = mysql_fetch_assoc($rs_query)) {
        $matriz[$row['id_prestacao']][$row['id_proposta']] = $row['valor'];
    }

    //LISTAGEM QUE POPULA A GRID NA VERTICAL
    $qr_tipos = "SELECT A.id_proposta,A.id_projeto,A.id_grupo,A.nome AS proposta,B.nome AS grupo, A.valor FROM projeto_propostas AS A
                    LEFT JOIN projeto_propostas_grupo AS B ON (A.id_grupo=B.id_grupo)
                    WHERE A.id_projeto = {$_REQUEST['projeto']}
                    ORDER BY A.id_grupo,A.id_proposta";
    $rs_tipos = mysql_query($qr_tipos);
    
    //SUBTOTAL PELO GRUPO
    $qr_tipoto = "SELECT SUM(valor) as val,id_grupo FROM ({$qr_tipos}) AS grid GROUP BY id_grupo";
    $rs_totalTipo = mysql_query($qr_tipoto);
    $totalTipo = array();
    while ($rtotalTp = mysql_fetch_assoc($rs_totalTipo)) {
        $totalTipo[$rtotalTp['id_grupo']] = $rtotalTp['val'];
    }

    //TOTAL DAS PROPOSTAS, SUM DA QUERY BASE
    $qr_totalBase = "SELECT id_proposta,proposta,SUM(valor) AS val FROM ({$qr_base}) AS grid GROUP BY proposta ORDER BY id_proposta";
    $rs_totalBase = mysql_query($qr_totalBase);
    $totalBase = array();
    while ($rtotal = mysql_fetch_assoc($rs_totalBase)) {
        $totalBase[$rtotal['id_proposta']] = $rtotal['val'];
    }
    
    //SUBTOTAL DOS TOTAIS DAS PROPOSTAS FINANCEIRO
    $qr_totalSubFin = "SELECT id_proposta,SUM(valor) AS val,id_grupo FROM ({$qr_base}) AS grid GROUP BY id_grupo ORDER BY id_proposta";
    $rs_totalSubFin = mysql_query($qr_totalSubFin);
    $totalSubFin = array();
    while ($rtotalSubF = mysql_fetch_assoc($rs_totalSubFin)) {
        $totalSubFin[$rtotalSubF['id_grupo']] = $rtotalSubF['val'];
    }

    $qr_SubtotalBase = "SELECT id_proposta,proposta,SUM(valor) AS val,id_grupo,id_prestacao FROM ({$qr_base}) AS grid GROUP BY id_grupo,id_prestacao ORDER BY id_proposta";
    $rs_SubtotalBase = mysql_query($qr_SubtotalBase);
    $subTotalBase = array();
    while ($rsubtotal = mysql_fetch_assoc($rs_SubtotalBase)) {
        $subTotalBase[$rsubtotal['id_prestacao']][$rsubtotal['id_grupo']] = $rsubtotal['val'];
        //echo $rsubtotal['id_prestacao']." - ".$rsubtotal['id_grupo']." - ".$rsubtotal['val']."<br/>";
    }
    /* echo "<pre>";
      print_r($subTotalBase); */

    echo "<!-- QR_BASE: {$qr_base} -->\n\r";
    echo "<!-- QR_TOTAL: {$qr_totalBase} -->\n\r";
    echo "<!-- QR_SUBTOTAL: {$qr_SubtotalBase} -->\n\r";
}


//SELECIONA PROJETO PARA O FILTRO
$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao IN (SELECT id_regiao FROM funcionario_regiao_assoc WHERE id_funcionario = {$usuario['id_funcionario']} AND id_master = '{$master}') AND status_reg = 1 ORDER BY nome");
$projetos = array("-1" => "« Selecione »");
while ($row_projeto = mysql_fetch_assoc($qr_projeto)) {
    $projetos[$row_projeto['id_projeto']] = $row_projeto['id_projeto'] . " - " . $row_projeto['nome'];
}

//------------------------- CADASTRO PROPOSTA -----------------------------//
//SELECIONA OS GRUPOS PARA O CADASTRO DA PROPOSTA
$Rsgrupos = montaQuery("projeto_propostas_grupo", "*");
$grupos = array();
foreach ($Rsgrupos as $grupo) {
    $grupos[$grupo['id_grupo']] = $grupo['nome'];
}

$qr_propostas = "SELECT A.*,B.nome AS grupo,C.nome AS projeto,DATE_FORMAT(data_ini, '%m/%Y') AS data_iniBr,DATE_FORMAT(data_fim, '%m/%Y') AS data_fimBr FROM projeto_propostas AS A
                    LEFT JOIN projeto_propostas_grupo AS B ON (A.id_grupo=B.id_grupo)
                    LEFT JOIN projeto AS C ON (A.id_projeto=C.id_projeto)
                    ORDER BY C.nome,B.nome,A.nome";
$rs_propostas = mysql_query($qr_propostas);
$num_linhas = mysql_num_rows($rs_propostas);

$qr_total = mysql_query("SELECT SUM(valor) as total,id_projeto FROM ({$qr_propostas}) AS B GROUP BY id_projeto");
$totais = array();
while ($rowTotal = mysql_fetch_assoc($qr_total)) {
    $totais[$rowTotal['id_projeto']] = $rowTotal['total'];
}

//--------------------------- TELA -----------------------------------//
$meses = mesesArray("id_nome,nome", "rh_clt", "id_regiao='45'");
$anos = anosArray(null, null, array("-1" => "« Selecione »"));
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : "";
$mesR = (isset($_REQUEST['smesini'])) ? $_REQUEST['smesini'] : "";
$anoR = (isset($_REQUEST['sanoini'])) ? $_REQUEST['sanoini'] : "";
?>
<html>
    <head>
        <title>:: Intranet :: RELATÓRIO DE PROPOSTA FINANCEIRA</title>
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
        <script src="../../js/jquery.maskMOney_2.1.2.js" type="text/javascript"></script>

        <script src="../../js/chartjs/knockout-2.2.1.js"></script>
        <script src="../../js/chartjs/globalize.js"></script>
        <script src="../../js/chartjs/dx.chartjs.js"></script>

        <script src="../../js/global.js" type="text/javascript"></script>
        <style>
            @media print{
                .ui-tabs-nav, fieldset, #pbuttons{
                    display: none;
                }
                #tabs, #tabs-1, #dvTable{
                    width: 200px;
                }
                body.novaintra table.grid thead tr th{
                    font-size: 10px;
                }
                .grid{
                    font-size: 8px;
                }
            }
        </style>
        <script>
            $(function() {
                $(document).keyup(function(e) {
                    if (e.keyCode == 27) {
                        if (confirm("As alterações feitas não terão efeito se continuar, deseja continuar?")) {
                            $(".dvmestre").hide();
                            $(".spanVal").show();
                        }
                    }
                });

                $("#cadValor").maskMoney({showSymbol: true, symbol: "R$ ", decimal: ",", thousands: "."});
                $(".numberFormat").maskMoney({showSymbol: true, symbol: "R$ ", decimal: ",", thousands: "."});

                $("#btnovapro").click(function() {
                    $("#fcad").show().removeClass("hidden");
                    $("#fdados").hide();
                });

                $("#bt-voltar").click(function() {
                    $("#fcad").hide();
                    $("#fdados").show();
                });

                $(".bt-save").click(function() {
                    var key = $(this).data('key');
                    var valor = $("#altValor_" + key).val();
                    $.post('rel_proposta.php', {valor: valor, id: key, method: "salvaValor"}, function(data) {
                        if (data.status) {
                            $("#span_" + key).html(valor);
                            $(".dvmestre").hide();
                            $(".spanVal").show();
                        }
                    }, "json");
                }).css('cursor', 'pointer');

                $("#bt-lista").click(function() {
                    var k = $(this).data('key');
                    if (k == 0)
                        $("#lista_propostas").show().removeClass("hidden").attr('data-key', '1');
                    else
                        $("#lista_propostas").hide().attr('data-key', '0');

                });

                $(".spanVal").dblclick(function() {
                    var key = $(this).data('key');
                    $("#dv_" + key).show().removeClass("hidden");
                    $(this).hide();
                });
            });
        </script>
    </head>
    
    <body id="page-despesas" class="novaintra">
        <div id="content" style="width: auto;">
            <form action="" method="post" name="exportaExcel" id="exportaExcel">
                <input type="hidden" name="geraExcel" id="geraExcel" value="" />
            </form>

            <form action="" method="post" name="form1" id="form1">
                <h2>RELATÓRIO DE PROPOSTA FINANCEIRA</h2>

                <?php if ($message !== false) { ?>
                    <div id='message-box' class='message-yellow'><p><?php echo $message ?></p></div>
                    <br/>
                <?php } ?>

                <fieldset id="fdados">
                    <legend>Filtro</legend>
                    <p><label class="first">Projeto:</label> <?php echo montaSelect($projetos, $projetoR, "id='projeto' name='projeto' class='validate[custom[select]]'") ?></p>
                    <p><label class="first">Inicio:</label> <?php echo montaSelect($meses, $mesR, "id='smesini' name='smesini' class='validate[custom[select]]'") ?>  <?php echo montaSelect($anos, $anoR, "id='sanoini' name='sanoini' class='validate[custom[select]]'") ?> <span class="example">12 meses após o inicio</span> </p>
                    <div>

                    </div>
                    <br class="clear"/>
                    <p class="controls"> 
                        <input type="submit" name="filtrar" id="filtrar" value="Filtrar" />
                        <input type="button" name="btnovapro" id="btnovapro" value="Cadastrar Proposta" />
                    </p>
                </fieldset>

                <fieldset id="fcad" class="hidden">
                    <legend>Cadastro de Proposta</legend>
                    <div class="fleft" style="width: 40%;">
                        <p><label class="first">Projeto:</label> <?php echo montaSelect($projetos, $projetoR, "id='cadprojeto' name='cadprojeto' class='validate[custom[select]]'") ?></p>
                        <p><label class="first">Nome:</label> <input type="text" name="cadNome" id="cadNome" value="" /></p>
                        <p><label class="first">Inicio:</label> <?php echo montaSelect($meses, $mesR, "id='mesini' name='mesini' class='validate[custom[select]]'") ?>  <?php echo montaSelect($anos, $anoR, "id='anoini' name='anoini' class='validate[custom[select]]'") ?> </p>
                    </div>
                    <div class="fleft">
                        <p><label class="first">Grupo:</label> <?php echo montaSelect($grupos, $grupoR, "id='cadGrupo' name='cadGrupo' class='validate[custom[select]]'") ?></p>
                        <p><label class="first">Valor:</label> <input type="text" name="cadValor" id="cadValor" value="" /></p>
                        <p><label class="first">Fim:</label> <?php echo montaSelect($meses, $mesR, "id='mesfim' name='mesfim' class='validate[custom[select]]'") ?>  <?php echo montaSelect($anos, $anoR, "id='anofim' name='anofim' class='validate[custom[select]]'") ?> </p>
                    </div>
                    <div id="lista_propostas" class="clear hidden">
                        <table cellpadding="0" cellspacing="0" border="0" class="grid" width="80%" align="center">
                            <thead>
                                <tr>
                                    <th>Grupo</th>
                                    <th>Nome</th>
                                    <th>Valor</th>
                                    <th>Inicio</th>
                                    <th>Fim</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $proAntes = "";
                                $idProAntes = "";
                                $count = 0;
                                while ($row = mysql_fetch_assoc($rs_propostas)) {
                                    if ($proAntes != $row['projeto']) {
                                        if (!emptY($proAntes)) {
                                            echo "<tr class='subtitulo'><td colspan='2' class='txright'>Total:</td><td colspan='3'>" . number_format($totais[$idProAntes], 2, ",", ".") . "</td></tr>";
                                        }
                                        $proAntes = $row['projeto'];
                                        $idProAntes = $row['id_projeto'];
                                        echo "<tr class='subtitulo'><td colspan='5' class='txcenter'>{$row['projeto']}</td></tr>";
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo $row['grupo']; ?></td>
                                        <td><?php echo $row['nome']; ?></td>
                                        <td>
                                            <div id="dv_<?php echo $row['id_proposta']; ?>" class="dvmestre hidden">
                                                <input type="text" name="altvalor" id="altValor_<?php echo $row['id_proposta']; ?>" value="<?php echo $row['valor']; ?>" class="numberFormat" size="9" />
                                                <img src="../../imagens/arquivo.gif" alt="Salvar Valor" id="imgbt_<?php echo $row['id_proposta']; ?>" data-key="<?php echo $row['id_proposta']; ?>" class="bt-save" />
                                            </div>
                                            <span id="span_<?php echo $row['id_proposta']; ?>" data-key="<?php echo $row['id_proposta']; ?>" class="spanVal"><?php echo number_format($row['valor'], 2, ",", "."); ?></span>
                                        </td>
                                        <td><?php echo $row['data_iniBr']; ?></td>
                                        <td><?php echo $row['data_fimBr']; ?></td>
                                    </tr>
                                    <?php
                                    $count++;
                                    if ($num_linhas == $count) {
                                        echo "<tr class='subtitulo'><td colspan='2' class='txright'>Total:</td><td colspan='3'>" . number_format($totais[$row['id_projeto']], 2, ",", ".") . "</td></tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <p class="clear controls"> 
                        <input type="button" name="bt-voltar" id="bt-voltar" value="Voltar" />
                        <input type="button" name="bt-lista" id="bt-lista" data-key="0" value="Lista de Propostas Cadastradas" />
                        <input type="submit" name="cadproposta" id="cadproposta" value="Cadastrar" />
                    </p>
                </fieldset>

                <br/>
                <?php if (count($prestQuery) > 0) { ?>
                    <p id="pbuttons" style="text-align: right">
                        <input type="button" onclick="tableToExcel('toexcel', 'Comparativo')" value="Exportar para Excel" class="exportarExcel">
                        <input type="button" id="imprimir" class="button" value="Imprimir" name="imprimir" onclick="window.print();" />
                    </p>
                    <table id="toexcel" cellpadding="0" cellspacing="0" border="0" class="grid">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Proposto</th>
                                <th>Total Proposto</th>
                                <?php
                                //PRINTANTDO CADA MES NO CABEÇALHO DA TABELA
                                foreach ($prestacaoContas as $pconta) {
                                    echo "<th>" . mesesArray($pconta['mes']) . " {$pconta['ano']}</th>";
                                }
                                ?>
                                <th>Total Financeiro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grupoAnt = "";
                            $totalCol = count($prestQuery) + 4;
                            $count = 0;
                            $num_linhasTo = mysql_num_rows($rs_tipos);
                            while ($rowTp = mysql_fetch_assoc($rs_tipos)) {
                                //VERIFICAÇÃO, PARA A IMPRESSÃO DO TITULO DO GRUPO E DA LINHA SUBTOTAL
                                if ($grupoAnt != $rowTp['id_grupo']) {
                                    //VERIFICAÇÃO DA IMPRESSÃO DA LINHA SUBTOTAL
                                    if ($grupoAnt != "") {
                                        echo "<tr class='subtitulo'><td class='txcenter'>Subtotal</td>";
                                        echo "<td>" . number_format($totalTipo[$grupoAnt], 2, ",", ".") . "</td><td>" . number_format($totalTipo[$grupoAnt]*$totalPrestacoes, 2, ",", ".") . "</td>";
                                        //IMPRIMINDO OS SUBTOTAIS DE ACORDO COM O MES
                                        foreach ($prestacaoContas as $pconta) {
                                            echo "<td class='txcenter'>" . number_format($subTotalBase[$pconta['prestacao']][$grupoAnt], 2, ",", ".") . "</td>";
                                        }
                                        echo "<td>" . number_format($totalSubFin[$grupoAnt], 2, ",", ".") . "</td>";
                                        echo "</tr>";
                                    }
                                    //IMPRIMINDO A LINHA SEPARADORA DE GRUPO
                                    echo "<tr class='subtitulo'><td class='txcenter' colspan='{$totalCol}'>{$rowTp['grupo']}</td></tr>";
                                    $grupoAnt = $rowTp['id_grupo'];
                                }
                                
                                //MOSTRANDO O GRUPO DO MOMENTO E O VALOR DA PROPOSTA DA QUERY NORMAL
                                echo "<tr><td>{$rowTp['proposta']}</td>";
                                echo "<td>" . number_format($rowTp['valor'], 2, ",", ".") . "</td>";
                                echo "<td>" . number_format(($rowTp['valor'] * $totalPrestacoes), 2, ",", ".") . "</td>";
                                
                                //RODANDO CADA MES E MOSTRANDO SEU TOTAL DE ACORDO COM O GRUPO DO MOMENTO
                                foreach ($prestacaoContas as $pconta) {
                                    $valor = $matriz[$pconta['prestacao']][$rowTp['id_proposta']];
                                    echo "<td>" . number_format($valor, 2, ",", ".") . "</td>";
                                }
                                
                                //ULTIMA COLUNA COM O TOTAL DO FINANCEIRO
                                echo "<td>" . number_format($totalBase[$rowTp['id_proposta']], 2, ",", ".") . "</td>";
                                echo "</tr>";
                                
                                /*------------SOMENTE PARA A ULTIMA LINHA SUBTOTAL------------------*/
                                $count++;
                                if($count == $num_linhasTo){
                                    echo "<tr class='subtitulo'><td class='txcenter'>Subtotal</td>";
                                     echo "<td>" . number_format($totalTipo[$rowTp['id_grupo']], 2, ",", ".") . "</td><td>" . number_format($totalTipo[$rowTp['id_grupo']]*$totalPrestacoes, 2, ",", ".") . "</td>";
                                    foreach ($prestacaoContas as $pconta) {
                                        echo "<td class='txcenter'>" . number_format($subTotalBase[$pconta['prestacao']][$rowTp['id_grupo']], 2, ",", ".") . "</td>";
                                    }
                                    echo "<td>" . number_format($totalSubFin[$rowTp['id_grupo']], 2, ",", ".") . "</td>";
                                    echo "</tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                <?php } ?>
            </form>
        </div>
    </body>
</html>