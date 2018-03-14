<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');
include('PrestacaoContas.class.php');

$usuarioW = carregaUsuario();

$regiao = $usuarioW['id_regiao'];
$master = $usuarioW['id_master'];
$usuario = $usuarioW['id_funcionario'];

$result = null;
$btexportar = true;
$btfinalizar = true;
$dataMesIni = date("Y-m") . "-31";

//----- CARREGA OS BANCOS VIA AJAX, RETORNA UM JSON 
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "loadbancos") {
    $return['status'] = 1;
    $qr_bancos = mysql_query("SELECT * FROM bancos WHERE id_projeto = '{$_REQUEST['projeto']}' AND status_reg=1 ");
    $num_rows = mysql_num_rows($qr_bancos);
    $bancos = array();
    if ($num_rows > 0) {
        while ($row = mysql_fetch_assoc($qr_bancos)) {
            $bancos[$row['id_banco']] = $row['id_banco'] . " - " . utf8_encode($row['nome']);
        }
    } else {
        $bancos["-1"] = "Banco não encontrado";
    }
    $return['options'] = $bancos;
    echo json_encode($return);
    exit;
}

// CASO TENHA PROJETO (EM TODOS OS CASOS DPS DO POST)
if (isset($_REQUEST['projeto'])) {
    $result = true;
    $id_projeto = $_REQUEST['projeto'];
    $id_banco = $_REQUEST['banco'];
    $mes2d = sprintf("%02d", $_REQUEST['mes']); //mes com 2 digitos
    $mes2dAnt = sprintf("%02d", ($_REQUEST['mes'] - 1)); //Mes anterior
    $mesShow = mesesArray($_REQUEST['mes']) . "/" . $_REQUEST['ano'];
    $dataMesRef = "{$_REQUEST['ano']}-{$mes2d}-01";
    $dataMesAntRef = "{$_REQUEST['ano']}-{$mes2dAnt}-01";
    $anoMesReferencia = $_REQUEST['ano'] . "-" . $mes2d;
    $historico = false;


    if ((isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) || (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar']))) {
        //LÓGICA DE VERIFICAÇÃO DE OUTROS PROJETOS EM ABERTOS
        //VERIFICA SE OUTRO PROJETO PRECISA PRESTAR CONTAS NO MES SELECIONADO
        $dataMesIni = "{$_REQUEST['ano']}-{$mes2d}-31";
        $qr_verifica = PrestacaoContas::getQueryVerifica("fluxocaixa", $dataMesRef, $dataMesIni, $usuarioW['id_master']);
        $rs_verifica = mysql_query($qr_verifica);
        $total_verifica = mysql_num_rows($rs_verifica);
        $projetosFaltante = array();
        $contErro = 0;
        $finalizados = array();

        while ($rowVeri = mysql_fetch_assoc($rs_verifica)) {
            //VERIFICA SE OS OUTROS NÃO ESTÃO FINALIZADOS
            if ($rowVeri['gerado_embr'] == null && $rowVeri['id_banco'] != $id_banco) {
                $btexportar = false;
                $projetosFaltante[$contErro]['nome'] = $rowVeri['projeto'];
                $projetosFaltante[$contErro]['banco'] = " Banco: " . $rowVeri['id_banco'] . " AG: " . $rowVeri['agencia'] . " CC: " . $rowVeri['conta'];
                $contErro ++;
            } elseif ($rowVeri['gerado_embr'] != null && $rowVeri['id_projeto'] == $id_projeto && $rowVeri['id_banco'] == $id_banco) {  //VERIFICA SE O ATUAL ESTÁ FINALIZADO
                $btfinalizar = false;
            }

            //VERIFICA SE SÓ TEM 1 E SE JA FOI FINALIZADO
            if ($total_verifica == 1 && $rowVeri['id_projeto'] == $id_projeto && $rowVeri['gerado_embr'] != null) {
                $btfinalizar = false;
            }

            //PRESTAÇÕES FINALIZADAS PARA A EXPORTAÇÃO
            if ($rowVeri['gerado_embr'] != null && $rowVeri['administracao'] == "0") {
                $finalizados[] = $rowVeri['id_prestacao'];
            }

            //CASO A PESQUISADA ESTIVER FINALIZADA, PEGA DO HISTÓRICO
            if ($rowVeri['id_projeto'] == $id_projeto && $rowVeri['gerado_embr'] != null && $rowVeri['id_banco'] == $id_banco) {
                $historico = $rowVeri['id_prestacao'];
            }
        }

        if ($btfinalizar)
            $btexportar = false;

        $proj_faltantes = count($projetosFaltante);
    }

    //SELEÇÃO DE PRESTAÇÕES FINALIZADAS (DESPESAS E CONCILIACAO), UTILIZADO NO FILTRO(SEM HISTORICO) E NO FINALIZAR
    if ((isset($_REQUEST['finalizar']) && !empty($_REQUEST['finalizar'])) || (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar']) && $historico === false)) {
        //BUSCANDO PRETAÇÃO DE DESPESA FINALIZADA
        $rsPrestDesp = mysql_query("SELECT id_prestacao FROM prestacoes_contas WHERE tipo = 'despesa' AND data_referencia = '{$dataMesRef}' AND id_projeto = '{$id_projeto}' AND erros = 0");
        $rowPrestDesp = mysql_fetch_assoc($rsPrestDesp);
        if (!empty($rowPrestDesp)) {
            $toDespesa = 0;
            $qrBase = "SELECT A.id_grupo, B.id as idsub, A.nome_grupo, B.id_subgrupo, B.nome AS subgrupo, C.cod, COUNT(D.id_saida) AS qnt, SUM(D.valor) as total
                    FROM entradaesaida_grupo AS A
                    LEFT JOIN entradaesaida_subgrupo AS B ON (A.id_grupo=B.entradaesaida_grupo)
                    LEFT JOIN entradaesaida AS C ON (LEFT(C.cod,5) = B.id_subgrupo)
                    LEFT JOIN (SELECT id_saida, id_tipo,valor FROM prestacoes_contas_desp WHERE id_prestacao = {$rowPrestDesp['id_prestacao']}) AS D ON (D.id_tipo = C.id_entradasaida)
                    WHERE C.GRUPO > 5 AND C.cod NOT IN('06.03.01','06.04.01')
                    GROUP BY A.id_grupo";
                                            
            $rsDesp = mysql_query($qrBase);
            echo "<!-- $qrBase -->";
        }

        //BUSCANDO PRESTACAO DE CONCILIACAO BANCÁRIA FINALIZADA
        $rsPrestConci = mysql_query("SELECT id_prestacao FROM prestacoes_contas WHERE tipo = 'conciliacao' AND data_referencia = '{$dataMesRef}' AND id_projeto = '{$id_projeto}' AND erros = 0");
        $rowPrestConci = mysql_fetch_assoc($rsPrestConci);
        $totalD = 0;
        if (!empty($rowPrestConci)) {
            $qrConci = "SELECT *,SUM(valor) as total FROM prestacoes_contas_conci WHERE id_prestacao = {$rowPrestConci['id_prestacao']}  GROUP BY SUBSTR(posicao, 1,1)";
            $rsConci = mysql_query($qrConci);
            while ($rowsLines = mysql_fetch_assoc($rsConci)) {
                $letra = preg_replace('/\d/', "", $rowsLines['posicao']);
                $matrizA[$letra] = $rowsLines['total'];
                $totalD += $rowsLines['total'];
            }
        }
    }

    //QUERY EXPORTAÇÃO
    if (isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) {
        $qr = "SELECT A.*,C.cod_sesrj AS cod_unidade,C.cod_contrato
                FROM prestacoes_contas_fluxo AS A
                LEFT JOIN prestacoes_contas AS B ON (A.id_prestacao = B.id_prestacao)
                LEFT JOIN projeto AS C ON (B.id_projeto = C.id_projeto)
                WHERE A.id_prestacao IN (" . implode(",", $finalizados) . ")";
    }

    $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = {$_REQUEST['projeto']}");
    $projeto = mysql_fetch_assoc($qr_projeto);

    $qr_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = {$_REQUEST['banco']}");
    $banco = mysql_fetch_assoc($qr_banco);

    $qrMaster = "SELECT nome,cod_os FROM master WHERE id_master = {$master}";
    $reMaster = mysql_query($qrMaster);
    $roMaster = mysql_fetch_assoc($reMaster);
}

//FINALIZANDO A PRESTAÇÃO DESSE PROJETO
if (isset($_REQUEST['finalizar']) && !empty($_REQUEST['finalizar'])) {
    $referencia = "{$_REQUEST['ano']}-{$mes2d}-01";

    $campos = "id_projeto, id_regiao, id_banco, tipo, data_referencia, gerado_em, gerado_por, linhas, erros, valor_total,status";
    $valores = array(
        $_REQUEST['projeto'],
        $regiao,
        $_REQUEST['banco'],
        "fluxocaixa",
        $referencia,
        date("Y-m-d H:i:s"),
        $usuario,
        "0",
        "0",
        "0",
        "1");

    sqlInsert("prestacoes_contas", $campos, $valores);
    $id = mysql_insert_id();

    $matriz = array();
    $count = 0;

    $arDescricoes = array("a1" => "SALDO FINANCEIRO NO MÊS ANTERIOR", "b1" => "Contrato de Gestão", "b2" => "Receitas Financeiras", "b3" => "Outras Receitas", "d2" => "Fundo Fixo de Caixa (Caixa Pequena)");

    //REUNINDO INFORMAÇÕES ENVIADAS VIA POST
    foreach ($_REQUEST['valor'] as $k => $val) {
        $posicao = strtoupper($k);
        $matriz[$count][] = $id;
        $matriz[$count][] = $posicao;
        $matriz[$count][] = $arDescricoes[$k];
        $matriz[$count][] = valorBrtoUs($val);
        $matriz[$count][] = $usuario;
        $matriz[$count][] = date("Y-m-d");
        $matriz[$count][] = "1";
        $count++;
    }

    //REUNINDO INFORMAÇÕES DA PRESTAÇÃO DE DESPESAS
    $countD = 1;
    while ($rowDespesa = mysql_fetch_assoc($rsDesp)) {
        $matriz[$count][] = $id;
        $matriz[$count][] = "C" . $countD;
        $matriz[$count][] = $rowDespesa['nome_grupo'];
        $matriz[$count][] = $rowDespesa['total'];
        $matriz[$count][] = $usuario;
        $matriz[$count][] = date("Y-m-d");
        $matriz[$count][] = "1";
        $count++;
        $countD++;
    }

    //INFORMAÇÕES DA PRESTACAO DE CONTAS CONCILIACAO BANCARIA
    $matriz[$count][] = $id;
    $matriz[$count][] = "D1";
    $matriz[$count][] = "Saldo em C/C e Aplicações Financeiras";
    $matriz[$count][] = $matrizA['A'];
    $matriz[$count][] = $usuario;
    $matriz[$count][] = date("Y-m-d");
    $matriz[$count][] = "1";
    $count++;

    $matriz[$count][] = $id;
    $matriz[$count][] = "D3";
    $matriz[$count][] = "Avisos de Créditos não Lançados nos Extratos Bancários";
    $matriz[$count][] = $matrizA['B'];
    $matriz[$count][] = $usuario;
    $matriz[$count][] = date("Y-m-d");
    $matriz[$count][] = "1";
    $count++;

    $matriz[$count][] = $id;
    $matriz[$count][] = "D4";
    $matriz[$count][] = "Cheques Emitidos e não Descontados";
    $matriz[$count][] = $matrizA['C'];
    $matriz[$count][] = $usuario;
    $matriz[$count][] = date("Y-m-d");
    $matriz[$count][] = "1";
    $count++;

    $matriz[$count][] = $id;
    $matriz[$count][] = "D5";
    $matriz[$count][] = "Avisos de Débitos não Lançados nos Extratos Bancários";
    $matriz[$count][] = $matrizA['D'];
    $matriz[$count][] = $usuario;
    $matriz[$count][] = date("Y-m-d");
    $matriz[$count][] = "1";
    $count++;

    $campos = array(
        "id_prestacao",
        "posicao",
        "descricao",
        "valor",
        "criado_por",
        "criado_em",
        "status"
    );
    sqlInsert("prestacoes_contas_fluxo", $campos, $matriz);
    echo "<script>location.href='finan_fluxocaixa.php'</script>";
    exit;
}

/* MONTA O ARQUIVO PARA BAIXAR */
if (isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) {
    error_reporting(E_ERROR);
    //echo $qr;exit;
    $result = mysql_query($qr);
    $linhas = mysql_num_rows($result);
    $linhasArquivo = ($linhas == 0) ? 5 : $linhas + 5; //CASO NÃO TENHA RESULTADO VAI CONTAR OS PROJETOS A ADD 5 LINHAS (CABEÇALHO)

    $qrT = "SELECT SUM(valor) AS total FROM (" . $qr . ") AS B";
    $resultT = mysql_query($qrT);
    $rowT = mysql_fetch_assoc($resultT);
    $totalMes = number_format($rowT['total'], 2, ",", "");

    $folder = dirname(__FILE__) . "/arquivos/";
    $fname = "OS_{$roMaster['cod_os']}_FLUXO_" . date("Ymd") . "_" . $mes2d . "{$_REQUEST['ano']}.CSV";
    $filename = $folder . $fname;

    // ESCREVENDO NO ARQUIVO
    // HEADER
    $handle = fopen($filename, "w");
    fwrite($handle, "H;COD_OS;DATA_GERACAO;LINHAS;TIPO;ANO_MES_REF;TIPO_ARQUIVO;VER_DOC;SECRETARIA\r\n");
    fwrite($handle, "H;{$roMaster['cod_os']};" . date("Y-m-d") . ";{$linhasArquivo};N;{$anoMesReferencia};FLUXO;3.1;01.01.01.01\r\n");

    // DETAIL
    // --CASO NÃO TENHA BENS ADQUIRIDOS NO PERIODO SELECIONADO, MUDAR O CABEÇALHO DO DETALHE--
    fwrite($handle, "D;COD_OS;COD_UNIDADE;COD_CONTRATO;GRUPO;DESCRICAO;VALOR\r\n");

    //ESCREVENDO AS LINHAS NO ARQUIVO CASO TENHA BENS
    while ($row = mysql_fetch_assoc($result)) {
        $valor = str_replace(".", ",", $row['valor']);

        fwrite($handle, "D;{$roMaster['cod_os']};{$row['cod_unidade']};{$row['cod_contrato']};{$row['posicao']};{$row['descricao']};{$valor}\r\n");

        $id_projeto = $row['id_projeto'];
        $id_regiao = $row['id_regiao'];
    }
    unset($row);

    fwrite($handle, "T;QUANTIDADE_REGISTROS;TOTAL_VALOR\r\n");
    fwrite($handle, "T;{$linhas};{$totalMes}");

    // -------------
    fclose($handle);

    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Content-type: application/x-msdownload");
    header("Content-Length: " . filesize($filename));
    header("Content-Disposition: attachment; filename={$fname}");
    flush();

    readfile($filename);
    exit;
}

$showValues = false;

/* FILTRO PARA MOSTRAR O RELATÓRIO */
/* RECEBE AS INFORMÇÕES PRA MONTAR O SELECT */
if (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) {

    if ($historico !== false) {
        $showValues = true;

        $qr = "SELECT * FROM prestacoes_contas WHERE tipo = 'fluxocaixa' AND id_projeto = {$id_projeto} AND data_referencia = '{$dataMesRef}'";
        $rs = mysql_query($qr);
        $rowPrestacao = mysql_fetch_assoc($rs);

        $qrLines = "SELECT *,SUBSTRING(posicao,1,1) as letra,SUBSTRING(posicao,2,1) as numero  FROM prestacoes_contas_fluxo WHERE id_prestacao = '{$rowPrestacao['id_prestacao']}'";
        $rsLines = mysql_query($qrLines);
        $matrizA = array();
        $matrizB = array();
        $matrizC = array();
        $relacaoD = array("D1" => "A", "D2" => "E", "D3" => "B", "D4" => "C", "D5" => "D");
        $totalB = 0;
        $toDespesa = 0; //totalC
        $totalD = 0;
        while ($rowLines = mysql_fetch_assoc($rsLines)) {
            switch ($rowLines['letra']) {
                case "A":
                    $matrizA[1]['valor'] = $rowLines['valor'];
                    break;
                case "B":
                    $matrizB[$rowLines['numero']]['valor'] = $rowLines['valor'];
                    $totalB += $rowLines['valor'];
                    break;
                case "C":
                    $matrizC[$rowLines['numero']]['valor'] = $rowLines['valor'];
                    $matrizC[$rowLines['numero']]['descricao'] = $rowLines['posicao'] . " - " . $rowLines['descricao'];
                    $toDespesa += $rowLines['valor'];
                    break;
                case "D":
                    $matrizA[$relacaoD[$rowLines['posicao']]] = $rowLines['valor'];
                    $totalD += $rowLines['valor'];
                    break;
            }
        }
        $totalABC = $matrizA[1]['valor'] + $totalB - $toDespesa;
        
    } else {
        //RECUPERANDO Repasse PARA POR NAS ENTRADAS (RECEITA)
        $condicaoRepasse = array(
            "tipo" => 12,
            "MONTH(data_vencimento)" => $mes2d,
            "YEAR(data_vencimento)" => $_REQUEST['ano'],
            "id_projeto" => $id_projeto,
            "status" => 2
        );
        $rsRepasse = montaQueryFirst("entrada", "SUM(CAST( REPLACE(valor, ',', '.') as decimal(13,2))) as total", $condicaoRepasse);
        $valRepasse = number_format($rsRepasse['total'],2,',','.');
        
        $qrUltimoTotal = "SELECT SUM(B.valor) AS total FROM prestacoes_contas AS A
                            LEFT JOIN prestacoes_contas_fluxo AS B ON (A.id_prestacao = B.id_prestacao)
                            WHERE A.tipo = 'fluxocaixa' AND A.id_projeto = {$id_projeto} AND A.data_referencia = '{$dataMesAntRef}' AND posicao LIKE 'D%'";
        $rsUltimoTotal = mysql_query($qrUltimoTotal);
        echo "<!-- $qrUltimoTotal -->";
        if(mysql_num_rows($rsUltimoTotal) > 0){
            $rowUltimoTotal = mysql_fetch_assoc($rsUltimoTotal);
            $totalMesAnterior = number_format($rowUltimoTotal['total'],2,',','.');
        }else{
            $totalMesAnterior = 0;
        }
    }
}

$attrPro = array("id" => "projeto", "name" => "projeto", "class" => "validate[custom[select]]");
$meses = mesesArray(null);
$anos = anosArray(null, null);

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m') - 1;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$erros = 0;
$idsErros = array();
?>
<html>
    <head>
        <title>:: Intranet :: FLUXO DE CAIXA</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../js/jquery.maskMOney_2.1.2.js" type="text/javascript"></script>
        <script src="../js/jquery.maskedinput.js" type="text/javascript"></script>

        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {
                $("#form1").validationEngine();
                $(".add_container").css('cursor', 'pointer');
                $(".valor").maskMoney({decimal: ",", thousands: ".", allowZero: true, allowNegative: true});
                $(".data").mask("99/99/9999");

                $("#projeto").change(function() {
                    var $this = $(this);
                    if ($this.val() != "-1") {
                        showLoading($this, "../");
                        $.post('finan_fluxocaixa.php', {projeto: $this.val(), method: "loadbancos"}, function(data) {
                            removeLoading();
                            if (data.status == 1) {
                                var opcao = "";
                                var selected = "";
                                for (var i in data.options) {
                                    selected = "";
                                    if (i == $("#bancSel").val()) {
                                        selected = "selected=\"selected\" ";
                                    }
                                    opcao += "<option value='" + i + "' " + selected + ">" + data.options[i] + "</option>";
                                }
                                $("#banco").html(opcao);
                            }
                        }, "json");
                    }
                }).trigger("change");
                
                $("table tr td").on("blur", "input[data-tipo=a],input[data-tipo=b]", function() {
                    //O "A" NÃO TEM TOTAL, POREM FAZ CALCULO COM B E C PARA TOTAL DA RECEITA
                    //O "B" TEM SEU TOTAL, E TAMBÉM FAZ CALCULO COM A E C PARA TOTAL DA RECEITA
                    var $this = $(this);
                    var valores = new Array();
                    var totalA = parseFloat($("input[data-tipo=a]", "table tr td").val().replace(/\./g, "").replace(",", "."));
                    var totalB = 0;
                    var totalC = parseFloat($("#totalC").html().replace(/[a-zA-Z]\$/g, "").replace(/\./g, "").replace(",", "."));
                    var totalF = 0;

                    //VARRENDO INPUTS B PARA CALCULAR O TOTAL B
                    $("input[data-tipo=b]", "table tr td").each(function(i) {
                        if ($(this).val() != "")
                            valores[i] = parseFloat($(this).val().replace(/\./g, "").replace(",", "."));
                        else
                            valores[i] = 0;
                    });

                    totalB = calcular(valores, null);
                    totalF = totalA + totalB - totalC;
                    if ($this.data('tipo') === "b")
                        $("#totalB").html(totalB.formatMoney(2, ',', '.'));
                    $("#totalABC").html(totalF.formatMoney(2, ',', '.'));
                });

                $("table tr td").on("blur", "input[data-tipo=d]", function() {
                    var valores = new Array();
                    var cont = 0;
                    $(".conci").each(function(i) {
                        valores[i] = $(this).html().replace(/[a-zA-Z]\$/g, "").replace(/\./g, "").replace(",", ".");
                        cont++;
                    });
                    valores[cont] = $(this).val().replace(/\./g, "").replace(",", ".");
                    calcular(valores, "#totalD");
                });
                
                $("input[data-tipo=b]").trigger('blur');
            });

            var calcular = function(valores, retorno) {
                var total = 0;
                for (var i in valores) {
                    total += parseFloat(valores[i]);
                }

                if (retorno != null) {
                    var obj = $(retorno);
                    obj.html(total.formatMoney(2, ',', '.'));
                } else {
                    return total;
                }
            };

        </script>

        <style>
            @media print
            {
                fieldset{display: none;}
                .h2page{display: none;}
                .grAdm{display: none;}
                #message-box{display: none;}
                input{display: none;}
            }
            @media screen
            {
                #headerPrint{display: none;}
            }
        </style>
    </head>
    <body id="page-despesas" class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1">
                <div id="headerPrint">
                    <img src="../imagens/logomaster<?php echo $usuarioW['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <h2><?php echo $roMaster['nome'] ?></h2>
                    <p class="clear"></p>
                </div>

                <input type="hidden" name="bancSel" id="bancSel" value="<?php echo $bancoR ?>" />
                <h2>FLUXO DE CAIXA</h2>

                <fieldset>
                    <legend>Dados</legend>
                    <p><label class="first">Projeto:</label> <?php echo montaSelect(PrestacaoContas::carregaProjetos($master), $projetoR, $attrPro) ?></p>
                    <p><label class="first">Banco:</label> <?php echo montaSelect(array("-1" => "« Selecione »"), null, "id='banco' name='banco'") ?></p>
                    <p id="mensal" ><label class="first">Mês:</label> <?php echo montaSelect($meses, $mesR, "id='mes' name='mes' class='validate[custom[select]]'") ?>  <?php echo montaSelect($anos, $anoR, "id='ano' name='ano' class='validate[custom[select]]'") ?> (mês de contratação)</p>

                    <p class="controls"> <input type="submit" class="button" value="Filtrar" name="filtrar" /> </p>
                </fieldset>

                <?php if (!empty($result)) { ?>
                    <?php if (empty($rowPrestDesp) && $historico === false) { ?>
                        <div id='message-box' class='message-yellow'>
                            <p>Atenção, para continuar a prestação de Despesas precisa ser finalizada.</p>
                        </div>
                    <?php } else { ?>
                        <br/>
                        <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Fluxo de Caixa')" value="Exportar para Excel" class="exportarExcel"></p>    
                        <br/>
                        <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" width="900" class="grid">
                            <thead>
                                <tr>
                                    <th colspan="3">Unidade Gerenciada: <?php echo $projeto['nome'] ?></th>
                                    <th><?php echo $mesShow ?></th>
                                </tr>
                                <tr>
                                    <th colspan="4">O responsável: <?php echo $roMaster['nome'] ?></th>
                                </tr>
                                <tr>
                                    <th colspan="4">FLUXO DE CAIXA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="3"> A - SALDO FINANCEIRO NO MÊS ANTERIOR</td>
                                    <td class="txright">R$ <?php if ($showValues) { echo number_format($matrizA[1]['valor'], 2, ",", "."); } else { ?><input type="text" name="valor[a1]" id="asaldo" value="<?php echo $totalMesAnterior ?>" size="10" class="valor" data-tipo="a"/><?php } ?>  </td>
                                </tr>
                                <!-- PRIMEIRA TABELA -->
                                <!-- B - RECEITAS -->
                                <tr class="subtitulo">
                                    <td colspan="4" class="txcenter">RECEITAS</td>
                                </tr>

                                <tr>
                                    <td colspan="3">Contrato de Gestão:</td>
                                    <td class="txright"> R$ <?php if ($showValues) { echo number_format($matrizB[1]['valor'], 2, ",", "."); } else { ?><input type="text" name="valor[b1]" id="contratoges" value="<?php echo $valRepasse ?>" size="10" class="valor" data-tipo="b"/><?php } ?> </td>
                                </tr>
                                <tr>
                                    <td colspan="3">Receitas Financeiras:</td>
                                    <td class="txright"> R$ <?php if ($showValues) { echo number_format($matrizB[2]['valor'], 2, ",", "."); } else { ?><input type="text" name="valor[b2]" id="recfinan" value="" size="10" class="valor" data-tipo="b"/><?php } ?> </td>
                                </tr>
                                <tr>
                                    <td colspan="3">Outras Receitas:</td>
                                    <td class="txright"> R$ <?php if ($showValues) { echo number_format($matrizB[3]['valor'], 2, ",", "."); } else { ?><input type="text" name="valor[b3]" id="outrasrec" value="" size="10" class="valor" data-tipo="b"/><?php } ?> </td>
                                </tr>
                                <tr class="titulo">
                                    <td colspan="3" class="txright-i">B - TOTAL DE RECEITAS</td>
                                    <td class="txright-i">R$ <span id="totalB"><?php echo number_format($totalB, 2, ",", "."); ?></span></td>
                                </tr>
                                <tr class="subtitulo">
                                    <td colspan="4"></td>
                                </tr>

                                <!-- FIM - PRIMEIRA TABELA -->
                                <!-- SEGUNDA TABELA -->

                                <tr class="titulo">
                                    <td colspan="4" class="txcenter">DESPESAS</td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <!-- SUBTABELA -->
                                        <!-- BUSCAR FINAN_DESPESAS DO MES SELECIONADO -->
                                        <table border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" id="tabB">
                                            <thead>
                                                <tr>
                                                    <th>Nome Despesa</th>
                                                    <th>Valor</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($historico === false) { ?>
                                                    <?php while ($rowDespesa = mysql_fetch_assoc($rsDesp)) {$toDespesa += $rowDespesa['total']; ?>
                                                        <tr>
                                                            <td> <?php echo $rowDespesa['nome_grupo'] ?> </td>
                                                            <td class="txright"> R$ <?php echo number_format($rowDespesa['total'], 2, ",", ".") ?> </td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <?php foreach ($matrizC as $c) { ?>
                                                        <tr>
                                                            <td> <?php echo $c['descricao'] ?> </td>
                                                            <td class="txright"> R$ <?php echo number_format($c['valor'], 2, ",", ".") ?> </td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr class="titulo">
                                    <td colspan="3" class="txright-i">C - TOTAL DE DESPESAS</td>
                                    <td class="txright-i">R$ <span id="totalC"><?php echo number_format($toDespesa, 2, ",", "."); ?></span></td>
                                </tr>

                                <!-- FIM - SEGUNDA TABELA -->
                                <!-- TOTALIZADOR CENTRAL -->
                                <tr class="subtitulo">
                                    <td colspan="4"></td>
                                </tr>
                                <tr class="titulo">
                                    <td colspan="3" class="txright-i">SALDO MENSAL FINAL (A)+(B)-(C) </td>
                                    <td class="txright-i">R$ <span id="totalABC"> <?php echo number_format($totalABC, 2, ",", "."); ?> </span></td>
                                </tr>
                                <tr class="subtitulo">
                                    <td colspan="4"></td>
                                </tr>
                                <!-- FIM - TOTALIZADOR CENTRAL -->
                                <!-- TERCEIRA TABELA -->

                                <tr class="titulo">
                                    <td colspan="4" class="txcenter">D - SALDO FINANCEIRO DISPONÍVEL PARA O PERÍODO SEGUINTE</td>
                                </tr>
                                <tr>
                                    <td colspan="3">D1 - Saldo em C/C e Aplicações Financeiras</td>
                                    <td class="txright-i conci">R$ <?php echo number_format($matrizA['A'], 2, ",", ".") ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3">D2 - Fundo Fixo de Caixa (Caixa Pequena)</td>
                                    <td class="txright-i">R$ <?php if ($showValues) { echo number_format($matrizA['E'], 2, ",", "."); } else { ?><input type="text" name="valor[d2]" id="fundocaixa" value="" size="10" class="valor" data-tipo="d"/><?php } ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3">D3 - Avisos de Créditos não Lançados nos Extratos Bancários</td>
                                    <td class="txright-i conci">R$ <?php echo number_format($matrizA['B'], 2, ",", ".") ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3">D4 - Cheques Emitidos e não Descontados</td>
                                    <td class="txright-i conci">R$ <?php echo number_format($matrizA['C'], 2, ",", ".") ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3">D5 -  Avisos de Débitos não Lançados nos Extratos Bancários</td>
                                    <td class="txright-i conci">R$ <?php echo number_format($matrizA['D'], 2, ",", ".") ?></td>
                                </tr>
                                <tr class="titulo">
                                    <td colspan="3" class="txright-i">TOTAL (D1+D2+D3-D4-D5)</td>
                                    <td class="txright-i">R$ <span id="totalD"><?php echo number_format($totalD, 2, ",", "."); ?></span></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php } ?>

                    <?php if ($projetoR !== null) { ?>
                        <?php if ($btexportar) { ?>
                            <p class="controls">
                                <input type="submit" class="button" value="Exportar" name="exportar" />
                            </p>
                                <?php } ?>
                        <br/>
                        <?php if ($btfinalizar) { ?>
                            <?php if ($erros == 0) { ?>
                                <p class="controls"> 
                                    <input type="submit" class="button" value="Finalizar Prestação" name="finalizar" />
                                </p>
                            <?php } else { ?>
                                <div id='message-box' class='message-yellow'>
                                    <p><?php
                                echo $msgErro . " ";
                                echo (count($idsErros) > 0) ? implode(", ", $idsErros) : "";
                                ?></p>
                                </div>
                                    <?php } ?>
                                <?php } else { ?>
                            <div id='message-box' class='message-yellow'>
                                <p>Prestação finalizada.</p>
                            </div>
                        <?php } ?>


                        <?php if ($proj_faltantes > 0) { ?>
                            <div id='message-box' class='message-blue'>
                                <p>Foi verificado a existencia de <?php echo $contErro ?> projeto(s) para finalizar neste mês antes de gerar o arquivo de prestação de contas.</p>
                                <ul>
                                    <?php
                                    foreach ($projetosFaltante as $val) {
                                        echo "<li>" . $val['nome'] . $val['banco'] . "</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </form>
        </div>
    </body>
</html>