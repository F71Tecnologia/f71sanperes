<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');
include('PrestacaoContas.class.php');
include('../classes/global.php');

$usuarioW = carregaUsuario();

$regiao = $usuarioW['id_regiao'];
$master = $usuarioW['id_master'];
$usuario = $usuarioW['id_funcionario'];

$result = null;
$btexportar = true;
$btfinalizar = true;
$dataMesIni = date("Y-m") . "-31";
$path = dirname(__FILE__) . "/arquivos/conciliacao/";

// CALULAR VALORES PARA TOTAL
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "calcula") {

    $valores = $_REQUEST['dados'];
    $total = 0;
    foreach ($valores as $valor) {
        $v = str_replace(".", "", $valor);
        $v = str_replace(",", ".", $v);
        $total += $v;
    }

    echo number_format($total, 2, ",", ".");
    exit;
}

//----- CARREGA OS BANCOS VIA AJAX, RETORNA UM JSON 
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "loadbancos") {
    $return['status'] = 1;
    $qr_bancos = mysql_query("SELECT * FROM bancos WHERE id_projeto = '{$_REQUEST['projeto']}' AND status_reg=1");
    $num_rows = mysql_num_rows($qr_bancos);
    $bancos = array();
    if ($num_rows > 0) {
        while ($row = mysql_fetch_assoc($qr_bancos)) {
            $bancos[$row['id_banco']] = $row['id_banco'] . " - " . utf8_encode($row['nome']);
        }
    } else {
        $bancos["-1"] = "Banco n�o encontrado";
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
    $mesShow = mesesArray($_REQUEST['mes']) . "/" . $_REQUEST['ano'];
    $dataMesRef = "{$_REQUEST['ano']}-{$mes2d}-01";
    $anoMesReferencia = $_REQUEST['ano'] . "-" . $mes2d;
    $historico = false;

    $primeiroDiaMes = "01/{$mes2d}/{$_REQUEST['ano']}";
    $ultimoDiaMes = date("t", strtotime($dataMesRef)) . "/{$mes2d}/{$_REQUEST['ano']}";

    if ((isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) || (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) || (isset($_REQUEST['uploadArquivos']) && !empty($_REQUEST['uploadArquivos']))) {
        //L�GICA DE VERIFICA��O DE OUTROS PROJETOS EM ABERTOS
        //VERIFICA SE OUTRO PROJETO PRECISA PRESTAR CONTAS NO MES SELECIONADO
        $dataMesIni = "{$_REQUEST['ano']}-{$mes2d}-31";
        $qr_verifica = PrestacaoContas::getQueryVerifica("conciliacao", $dataMesRef, $dataMesIni, $usuarioW['id_master']);
        $rs_verifica = mysql_query($qr_verifica);
        $total_verifica = mysql_num_rows($rs_verifica);
        $projetosFaltante = array();
        $contErro = 0;
        $finalizados = array();

        while ($rowVeri = mysql_fetch_assoc($rs_verifica)) {

            //VERIFICA SE OS OUTROS N�O EST�O FINALIZADOS
            if ($rowVeri['gerado_embr'] == null && $rowVeri['id_banco'] != $id_banco) {
                $btexportar = false;
                $projetosFaltante[$contErro]['nome'] = $rowVeri['projeto'];
                $projetosFaltante[$contErro]['banco'] = " Banco: " . $rowVeri['id_banco'] . " AG: " . $rowVeri['agencia'] . " CC: " . $rowVeri['conta'];
                $contErro ++;
            } elseif ($rowVeri['gerado_embr'] != null && $rowVeri['id_projeto'] == $id_projeto && $rowVeri['id_banco'] == $id_banco) {  //VERIFICA SE O ATUAL EST� FINALIZADO
                $btfinalizar = false;
            }

            //VERIFICA SE S� TEM 1 E SE JA FOI FINALIZADO
            if ($total_verifica == 1 && $rowVeri['id_projeto'] == $id_projeto && $rowVeri['gerado_embr'] != null) {
                $btfinalizar = false;
            }

            //PRESTA��ES FINALIZADAS PARA A EXPORTA��O
            if ($rowVeri['gerado_embr'] != null && $rowVeri['administracao'] == "0") {
                $finalizados[] = $rowVeri['id_prestacao'];
            }

            //CASO A PESQUISADA ESTIVER FINALIZADA, PEGA DO HIST�RICO
            if ($rowVeri['id_projeto'] == $id_projeto && $rowVeri['gerado_embr'] != null && $rowVeri['id_banco'] == $id_banco) {
                $historico = $rowVeri['id_prestacao'];
            }
        }

        if ($btfinalizar)
            $btexportar = false;

        $proj_faltantes = count($projetosFaltante);
    }

    //QUERY EXPORTA��O
    if (isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) {
        $qr = "SELECT A.*,C.cod_sesrj AS cod_unidade,C.cod_contrato
                FROM prestacoes_contas_conci AS A
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

//FINALIZANDO A PRESTA��O DESSE PROJETO
if (isset($_REQUEST['finalizar']) && !empty($_REQUEST['finalizar'])) {
    $referencia = "{$_REQUEST['ano']}-{$mes2d}-01";

    $campos = "id_projeto, id_regiao, id_banco, tipo, data_referencia, gerado_em, gerado_por, linhas, erros, valor_total,status";
    $valores = array(
        $_REQUEST['projeto'],
        $regiao,
        $_REQUEST['banco'],
        "conciliacao",
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

    //LINHA 1 (CONTEM O A1 - Saldo em Conta Corrente)
    $matriz[$count][] = $id;
    $matriz[$count][] = "A1";
    $matriz[$count][] = "";
    $matriz[$count][] = "A1 - Saldo em Conta Corrente";
    $matriz[$count][] = "";
    $matriz[$count][] = valorBrtoUs($_REQUEST['a1']);
    $matriz[$count][] = $usuario;
    $matriz[$count][] = date("Y-m-d");
    $matriz[$count][] = "1";
    $count++;

    //LINHA 2 (CONTEM O A2 - Saldo em Aplica��es Financeiras:)
    $matriz[$count][] = $id;
    $matriz[$count][] = "A2";
    $matriz[$count][] = "";
    $matriz[$count][] = "A2 - Saldo em Aplica��es Financeiras:";
    $matriz[$count][] = "";
    $matriz[$count][] = valorBrtoUs($_REQUEST['a2']);
    $matriz[$count][] = $usuario;
    $matriz[$count][] = date("Y-m-d");
    $matriz[$count][] = "1";
    $count++;

    //MONTANDO LINHAS DA TABELA B
    for ($i = 0; $i < count($_REQUEST['dataB']); $i++) {
        if ($_REQUEST['dataB'][$i] != "") {
            $matriz[$count][] = $id;
            $matriz[$count][] = "B" . ($i + 1);
            $matriz[$count][] = $_REQUEST['numB'][$i];
            $matriz[$count][] = $_REQUEST['historicoB'][$i];
            $matriz[$count][] = converteData($_REQUEST['dataB'][$i]);
            $matriz[$count][] = valorBrtoUs($_REQUEST['valorB'][$i]);
            $matriz[$count][] = $usuario;
            $matriz[$count][] = date("Y-m-d");
            $matriz[$count][] = "1";
        }
        $count++;
    }
    unset($i);

    //MONTANDO LINHAS DA TABELA C
    for ($i = 0; $i < count($_REQUEST['dataC']); $i++) {
        if ($_REQUEST['dataC'][$i] != "") {
            $matriz[$count][] = $id;
            $matriz[$count][] = "C" . ($i + 1);
            $matriz[$count][] = $_REQUEST['numC'][$i];
            $matriz[$count][] = $_REQUEST['historicoC'][$i];
            $matriz[$count][] = converteData($_REQUEST['dataC'][$i]);
            $matriz[$count][] = valorBrtoUs($_REQUEST['valorC'][$i]);
            $matriz[$count][] = $usuario;
            $matriz[$count][] = date("Y-m-d");
            $matriz[$count][] = "1";
        }
        $count++;
    }
    unset($i);


    //MONTANDO LINHAS DA TABELA D
    for ($i = 0; $i < count($_REQUEST['dataD']); $i++) {
        if ($_REQUEST['dataD'][$i] != "") {
            $matriz[$count][] = $id;
            $matriz[$count][] = "D" . ($i + 1);
            $matriz[$count][] = $_REQUEST['numD'][$i];
            $matriz[$count][] = $_REQUEST['historicoD'][$i];
            $matriz[$count][] = converteData($_REQUEST['dataD'][$i]);
            $matriz[$count][] = valorBrtoUs($_REQUEST['valorD'][$i]);
            $matriz[$count][] = $usuario;
            $matriz[$count][] = date("Y-m-d");
            $matriz[$count][] = "1";
        }
        $count++;
    }
    unset($i);

    $campos = array(
        "id_prestacao",
        "posicao",
        "numero",
        "descricao",
        "data",
        "valor",
        "criado_por",
        "criado_em",
        "status"
    );
    sqlInsert("prestacoes_contas_conci", $campos, $matriz);
    echo "<script>location.href='finan_concilia.php'</script>";
    exit;
}

/* MONTA O ARQUIVO PARA BAIXAR */
if (isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) {

    $result = mysql_query($qr);
    $linhas = mysql_num_rows($result);
    $linhasArquivo = ($linhas == 0) ? 5 : $linhas + 5; //CASO N�O TENHA RESULTADO VAI CONTAR OS PROJETOS A ADD 5 LINHAS (CABE�ALHO)

    $qrT = "SELECT SUM(valor) AS total FROM (" . $qr . ") AS B";
    $resultT = mysql_query($qrT);
    $rowT = mysql_fetch_assoc($resultT);
    $total = number_format($rowT['total'], 2, ",", "");

    $folder = dirname(__FILE__) . "/arquivos/";
    $fname = "OS_{$roMaster['cod_os']}_CONC_" . date("Ymd") . "_" . $mes2d . "{$_REQUEST['ano']}.CSV";
    $filename = $folder . $fname;

    // ESCREVENDO NO ARQUIVO
    // HEADER
    $handle = fopen($filename, "w");
    fwrite($handle, "H;COD_OS;DATA_GERACAO;LINHAS;TIPO;ANO_MES_REF;TIPO_ARQUIVO;VER_DOC;SECRETARIA\r\n");
    fwrite($handle, "H;{$roMaster['cod_os']};" . date("Y-m-d") . ";{$linhasArquivo};N;{$anoMesReferencia};CONC;3.1;01.01.01.01\r\n");

    // DETAIL
    // --CASO N�O TENHA BENS ADQUIRIDOS NO PERIODO SELECIONADO, MUDAR O CABE�ALHO DO DETALHE--

    fwrite($handle, "D;COD_OS;COD_UNIDADE;COD_CONTRATO;GRUPO;NUMERO;DESCRICAO;DATA;VALOR\r\n");


    //ESCREVENDO AS LINHAS NO ARQUIVO
    while ($row = mysql_fetch_assoc($result)) {
        print_r($row);
        exit();
        $valor = str_replace(".", ",", $row['valor']);
        fwrite($handle, "D;{$roMaster['cod_os']};{$row['cod_unidade']};{$row['cod_contrato']};{$row['posicao']};{$row['numero']};{$row['descricao']};{$row['data']};{$valor}\r\n");
        $id_projeto = $row['id_projeto'];
        $id_regiao = $row['id_regiao'];
    }
    unset($row);


    fwrite($handle, "T;QUANTIDADE_REGISTROS;TOTAL_VALOR\r\n");
    fwrite($handle, "T;{$linhas};{$total}");

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

/* FILTRO PARA MOSTRAR O RELAT�RIO */
/* RECEBE AS INFORM��ES PRA MONTAR O SELECT */
if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) || (isset($_REQUEST['uploadArquivos']) && !empty($_REQUEST['uploadArquivos']))) {

    if ($historico !== false) {
        $showValues = true;

        $qr = "SELECT * FROM prestacoes_contas WHERE tipo = 'conciliacao' AND id_projeto = {$id_projeto} AND data_referencia = '{$dataMesRef}'";
        $rs = mysql_query($qr);
        $rowPrestacao = mysql_fetch_assoc($rs);

        $qrLines = "SELECT *,DATE_FORMAT(data, '%d/%m/%Y') as databr FROM prestacoes_contas_conci WHERE id_prestacao = '{$rowPrestacao['id_prestacao']}'";
        $rsLines = mysql_query($qrLines);
        $matrizA = array();
        $matrizB = array();
        $matrizC = array();
        $matrizD = array();
        $contB = 0;
        $contC = 0;
        $contD = 0;
        $totalA = 0;
        $totalB = 0;
        $totalC = 0;
        $totalD = 0;
        $totalFinal = 0;
        while ($rowsLines = mysql_fetch_assoc($rsLines)) {
            switch ($rowsLines['posicao']) {
                case "A1":
                    $matrizA[1]['valor'] = (empty($rowsLines['valor'])) ? "0" : $rowsLines['valor'];
                    $totalA+=$rowsLines['valor'];
                    break;
                case "A2":
                    $matrizA[2]['valor'] = (empty($rowsLines['valor'])) ? "0" : $rowsLines['valor'];
                    $totalA+=$rowsLines['valor'];
                    break;
                default :
                    $letra = preg_replace('/\d/', "", $rowsLines['posicao']);
                    if ($letra == "B") {
                        $matrizB[$contB]['data'] = $rowsLines['databr'];
                        $matrizB[$contB]['numero'] = $rowsLines['numero'];
                        $matrizB[$contB]['descricao'] = $rowsLines['descricao'];
                        $matrizB[$contB]['valor'] = $rowsLines['valor'];
                        $totalB+=$rowsLines['valor'];
                        $contB++;
                    } elseif ($letra == "C") {
                        $matrizC[$contC]['data'] = $rowsLines['databr'];
                        $matrizC[$contC]['numero'] = $rowsLines['numero'];
                        $matrizC[$contC]['descricao'] = $rowsLines['descricao'];
                        $matrizC[$contC]['valor'] = $rowsLines['valor'];
                        $totalC+=$rowsLines['valor'];
                        $contC++;
                    } elseif ($letra == "D") {
                        $matrizD[$contD]['data'] = $rowsLines['databr'];
                        $matrizD[$contD]['numero'] = $rowsLines['numero'];
                        $matrizD[$contD]['descricao'] = $rowsLines['descricao'];
                        $matrizD[$contD]['valor'] = $rowsLines['valor'];
                        $totalD+=$rowsLines['valor'];
                        $contD++;
                    }
                    break;
            }
        }
        $totalFinal = ($totalA + $totalB) - ($totalC + $totalD);

        //VERIFICA��O DO ARQUIVO CONTA CORRENTE
        $nomeCC = $_REQUEST['projeto'] . "_" . $historico . "_CC_" . str_replace("-", "", $anoMesReferencia);
        $fileCC = $path . $nomeCC;
        $resultGlobCC = glob($fileCC . "*");
        $totalGlobCC = count($resultGlobCC);
        $fileUpCC = null;
        if ($totalGlobCC == 1) {
            $fileUpCC = end(explode("/", $resultGlobCC[0]));
        }

        //VERIFICA��O DO ARQUIVO CONTA CORRENTE
        $nomeCP = $_REQUEST['projeto'] . "_" . $historico . "_CP_" . str_replace("-", "", $anoMesReferencia);
        $fileCP = $path . $nomeCP;
        $resultGlobCP = glob($fileCP . "*");
        $totalGlobCP = count($resultGlobCP);
        $fileUpCP = null;
        if ($totalGlobCP == 1) {
            $fileUpCP = end(explode("/", $resultGlobCP[0]));
        }
    }

    echo "<!-- " . $qr . " -->";
    echo "<!-- " . $qr_verifica . " -->";
}

/* UPLOAD DE ARQUIVO CONTA CORRENTE E CONTA POUPAN�A, DA PRA MELHORAR, ESTOU COM MUITA PRESSA PRA TERMINAR */
if (isset($_REQUEST['uploadArquivos']) && !empty($_REQUEST['uploadArquivos'])) {

    //ARQUIVO DE CONTA CORRENTE
    if (isset($_FILES['cc'])) {
        $arquivo = $_FILES['cc'];
        $infos = explode(".", $arquivo["name"]);
        $tipoArquivo = "." . end($infos);
        $tipos = array('jpg', 'png', 'gif', 'pdf', 'doc');

        //NOME -> PROJETO_PRESTACAO_CC_ANOMES.PDF
        $nome = $_REQUEST['projeto'] . "_" . $_REQUEST['id_prestacao'] . "_CC_" . str_replace("-", "", $anoMesReferencia);
        $enviar = GlobalClass::uploadFile($arquivo, $path, $tipos, $nome);

        if ($enviar['erro']) {
            echo "<div id='message-box' class='message-red'>Erro ao enviar o arquivo de Conta Corrente. Descri��o do erro: {$enviar['erro']}";
            exit;
        } else {
            $fileUpCC = end(explode("/", $enviar["caminho"]));
        }
    }

    //ARQUIVO DE CONTA POUPAN�A
    if (isset($_FILES['cp'])) {
        $arquivo = $_FILES['cp'];
        $infos = explode(".", $arquivo["name"]);
        $tipoArquivo = "." . end($infos);
        $tipos = array('jpg', 'png', 'gif', 'pdf', 'doc');

        //NOME -> PROJETO_PRESTACAO_CC_ANOMES.PDF
        $nome = $_REQUEST['projeto'] . "_" . $_REQUEST['id_prestacao'] . "_CP_" . str_replace("-", "", $anoMesReferencia);
        $enviar = GlobalClass::uploadFile($arquivo, $path, $tipos, $nome);

        if ($enviar['erro']) {
            echo "<div id='message-box' class='message-red'>Erro ao enviar o arquivo de conta Poupan�a. Descri��o do erro: {$enviar['erro']}";
            exit;
        } else {
            $fileUpCP = end(explode("/", $enviar["caminho"]));
        }
    }
}

$attrPro = array("id" => "projeto", "name" => "projeto", "class" => "validate[custom[select]]");
$meses = mesesArray(null);
$anos = anosArray(null, null);

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMUL�RIO SELECIONADO */
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m') - 1;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$erros = 0;
$idsErros = array();
?>
<html>
    <head>
        <title>:: Intranet :: CONCILIA��O BANC�RIA</title>
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
        <script src="../uploadfy/scripts/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>

        <script src="../js/global.js" type="text/javascript"></script>

        <script>

            $(function() {
                $.validationEngineLanguage.allRules["funOnlyPdf"] = {
                    "alertText": "Somente PDF."
                };
                $("#form1").validationEngine();

                $(".add_container").css('cursor', 'pointer');
                mascararCampos();

                $("#projeto").change(function() {
                    var $this = $(this);
                    if ($this.val() != "-1") {
                        showLoading($this, "../");
                        $.post('finan_concilia.php', {projeto: $this.val(), method: "loadbancos"}, function(data) {
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

                /*CLONA ELEMENTOS*/
                $(".add_container").click(function() {
                    var tab = "#" + $(this).data('key');
                    clonaContainer($(this), tab);
                });

                $("table tr td").on("blur", "input.valor", function() {
                    var id = $(this).attr('id');
                    if (id === "a1" || id === "a2") {
                        var val1 = $("#a1").val();
                        var val2 = $("#a2").val();
                        if (val1 != "" && val2 != "") {
                            calcular([val1, val2], "#totalA");
                        }
                    } else {
                        var tab = id.replace(/([a-z]+)/, '');
                        tab = tab.match(/[A-Z]/gi);
                        var valores = new Array();
                        $("#tab" + tab[0] + " input[class=valor]").each(function(i) {
                            valores[i] = $(this).val();
                        });
                        calcular(valores, "#total" + tab[0]);
                    }
                });
            });

            var calcular = function(valores, retorno) {
                var obj = $(retorno);
                $.post('finan_concilia.php', {method: "calcula", dados: valores}, function(data) {
                    obj.html(data);
                    calculaTotais();
                }, 'html');
            };

            var calculaTotais = function() {
                var toA = parseFloat($("#totalA").html().replace(/\./g, "").replace(",", "."));
                var toB = parseFloat($("#totalB").html().replace(/\./g, "").replace(",", "."));
                var toC = parseFloat($("#totalC").html().replace(/\./g, "").replace(",", "."));
                var toD = parseFloat($("#totalD").html().replace(/\./g, "").replace(",", "."));
                var total = (toA + toB) - (toC + toD);
                $("#totalFinal").html(total.formatMoney(2, ',', '.'));
            };

            var clonaContainer = function(buttom, perimetro) {

                var $this = buttom;
                if (typeof (perimetro) === "undefined") {
                    perimetro = "body";
                }

                var container = $(".clona_container", perimetro);
                var superContainer = container.parent();

                var clone = container.clone();
                //clone.children().eq(4);
                clone.removeClass("clona_container").addClass("remove_container").appendTo(superContainer);
                clone.children().children("input[type='text']").val("");
                mascararCampos();
            };

            var mascararCampos = function() {
                $(".valor").maskMoney({decimal: ",", thousands: "."});
                $(".data").mask("99/99/9999");
            };

            var onlyPdf = function(field, rules, i, options) {
                var filename = field.val();
                if (filename != "") {
                    var extension = filename.substr(filename.lastIndexOf('.') + 1).toLowerCase();
                    if (extension != "pdf") {
                        return options.allrules.funOnlyPdf.alertText;
                    }
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
                .divUp{border:#CCC; background: #E2E2E2;}
            }
            @media screen
            {
                #headerPrint{display: none;}
            }
        </style>
    </head>
    <body id="page-despesas" class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data">
                <div id="headerPrint">
                    <img src="../imagens/logomaster<?php echo $usuarioW['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <h2><?php echo $roMaster['nome'] ?></h2>
                    <p class="clear"></p>
                </div>

                <input type="hidden" name="bancSel" id="bancSel" value="<?php echo $bancoR ?>" />
                <h2>CONCILIA��O BANC�RIA</h2>

                <fieldset>
                    <legend>Dados</legend>
                    <p><label class="first">Projeto:</label> <?php echo montaSelect(PrestacaoContas::carregaProjetos($master), $projetoR, $attrPro) ?></p>
                    <p><label class="first">Banco:</label> <?php echo montaSelect(array("-1" => "� Selecione �"), null, "id='banco' name='banco'") ?></p>
                    <p id="mensal" ><label class="first">M�s:</label> <?php echo montaSelect($meses, $mesR, "id='mes' name='mes' class='validate[custom[select]]'") ?>  <?php echo montaSelect($anos, $anoR, "id='ano' name='ano' class='validate[custom[select]]'") ?> (m�s de contrata��o)</p>

                    <p class="controls"> <input type="submit" class="button" value="Filtrar" name="filtrar" /> </p>
                </fieldset>

<?php if (!empty($result)) { ?>
                    <br/>
                    <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Conciliacao Bancaria')" value="Exportar para Excel" class="exportarExcel"></p>    
                    <br/>
                    <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" width="900" class="grid">
                        <thead>
                            <tr>
                                <th colspan="3">Unidade Gerenciada: <?php echo $projeto['nome'] ?></th>
                                <th><?php echo $mesShow ?></th>
                            </tr>
                            <tr>
                                <th colspan="4">O respons�vel: <?php echo $roMaster['nome'] ?></th>
                            </tr>
                            <tr>
                                <th colspan="4">CONCILIA��O BANC�RIA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="subtitulo">
                                <td class="txright">Banco:</td>
                                <td><?php echo $banco['razao'] ?></td>
                                <td class="txright">Agencia:</td>
                                <td><?php echo $banco['agencia'] ?></td>
                            </tr>

                            <tr class="subtitulo">
                                <td class="txright">Conta Corrente:</td>
                                <td colspan="3"><?php echo $banco['conta'] ?></td>
                            </tr>

                            <tr class="subtitulo">
                                <td class="txright">Per�odo de Referencia:</td>
                                <td colspan="3">de <?php echo $primeiroDiaMes . " at� " . $ultimoDiaMes; ?></td>
                            </tr>

                            <!-- PRIMEIRA TABELA -->

                            <tr class="titulo">
                                <td colspan="4" class="txcenter">A - SALDO CONFORME EXTRATO BANC�RIO EM <?php echo $ultimoDiaMes ?></td>
                            </tr>
                            <tr class="subtitulo">
                                <td colspan="3" class="txcenter">DESCRI��O:</td>
                                <td class="txcenter">VALOR (R$):</td>
                            </tr>

                            <tr>
                                <td colspan="3">A1 - Saldo em Conta Corrente:</td>
                                <td class="txright"> R$ <?php
    if ($showValues) {
        echo number_format($matrizA[1]['valor'], 2, ",", ".");
    } else {
        ?><input type="text" name="a1" id="a1" value="" size="10" class="valor"/><?php } ?> </td>
                            </tr>
                            <tr>
                                <td colspan="3">A2 - Saldo em Aplica��es Financeiras:</td>
                                <td class="txright"> R$ <?php
                                if ($showValues) {
                                    echo number_format($matrizA[2]['valor'], 2, ",", ".");
                                } else {
        ?><input type="text" name="a2" id="a2" value="" size="10" class="valor"/><?php } ?> </td>
                            </tr>
                            <tr class="titulo">
                                <td colspan="3" class="txright-i">A3 - TOTAL</td>
                                <td class="txright-i">R$ <span id="totalA"><?php echo number_format($totalA, 2, ",", "."); ?></span></td>
                            </tr>
                            <tr class="subtitulo">
                                <td colspan="4"></td>
                            </tr>

                            <!-- FIM - PRIMEIRA TABELA -->
                            <!-- SEGUNDA TABELA -->

                            <tr class="titulo">
                                <td colspan="4" class="txcenter">B - AVISOS DE CR�DITO N�O LAN�ADOS NO EXTRATO</td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <!-- SUBTABELA -->
                                    <table border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" id="tabB">
                                        <thead>
                                            <tr>
                                                <th>DATA</th>
                                                <th>N�</th>
                                                <th>HIST�RICO</th>
                                                <th>VALOR (R$) <?php if (!$showValues) { ?><span class="fright bt-mais add_container" data-key="tabB">Mais</span><?php } ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
    <?php if (!$showValues) { ?>           
                                                <tr class="noremove clona_container">
                                                    <td> <input type="text" name="dataB[]" id="dataB[]" value="" size="12" class="data" /> </td>
                                                    <td> <input type="text" name="numB[]" id="numB[]" value="" size="8" /> </td>
                                                    <td> <input type="text" name="historicoB[]" id="historicoB[]" value="" size="30" /> </td>
                                                    <td class="txright"> R$ <input type="text" name="valorB[]" id="valorB[]" value="" size="10" class="valor" /> </td>
                                                </tr>
    <?php } else { ?>
                                                <?php foreach ($matrizB as $k => $linesB) { ?>
                                                    <tr class="<?php echo ($k % 2) ? 'even' : 'odd'; ?>">
                                                        <td> <?php echo $linesB['data'] ?> </td>
                                                        <td> <?php echo $linesB['numero'] ?> </td>
                                                        <td> <?php echo $linesB['descricao'] ?> </td>
                                                        <td class="txright"> R$ <?php echo number_format($linesB['valor'], 2, ",", ".") ?> </td>
                                                    </tr>
        <?php
        }
    }
    ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr class="titulo">
                                <td colspan="3" class="txright-i">B1 - TOTAL</td>
                                <td class="txright-i">R$ <span id="totalB"><?php echo number_format($totalB, 2, ",", "."); ?></span></td>
                            </tr>
                            <tr class="subtitulo">
                                <td colspan="4"></td>
                            </tr>

                            <!-- FIM - SEGUNDA TABELA -->
                            <!-- TERCEIRA TABELA -->

                            <tr class="titulo">
                                <td colspan="4" class="txcenter">C - CHEQUES EMITIDOS E N�O DESCONTADOS</td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <!-- SUBTABELA -->
                                    <table border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" id="tabC">
                                        <thead>
                                            <tr>
                                                <th>DATA</th>
                                                <th>N�</th>
                                                <th>HIST�RICO</th>
                                                <th>VALOR (R$) <?php if (!$showValues) { ?><span class="fright bt-mais add_container" data-key="tabC">Mais</span><?php } ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$showValues) { ?>
                                                <tr class="noremove clona_container">
                                                    <td> <input type="text" name="dataC[]" id="dataC[]" value="" size="12" class="data" /> </td>
                                                    <td> <input type="text" name="numC[]" id="numC[]" value="" size="8" /> </td>
                                                    <td> <input type="text" name="historicoC[]" id="historicoC[]" value="" size="30" /> </td>
                                                    <td class="txright"> R$ <input type="text" name="valorC[]" id="valorC[]" value="" size="10" class="valor" /> </td>
                                                </tr>
                                            <?php } else { ?>
                                                <?php foreach ($matrizC as $k => $linesC) { ?>
                                                    <tr class="<?php echo ($k % 2) ? 'even' : 'odd'; ?>">
                                                        <td> <?php echo $linesC['data'] ?> </td>
                                                        <td> <?php echo $linesC['numero'] ?> </td>
                                                        <td> <?php echo $linesC['descricao'] ?> </td>
                                                        <td class="txright"> R$ <?php echo number_format($linesC['valor'], 2, ",", ".") ?> </td>
                                                    </tr>
                                                <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr class="titulo">
                                <td colspan="3" class="txright-i">C1 - TOTAL</td>
                                <td class="txright-i">R$ <span id="totalC"><?php echo number_format($totalC, 2, ",", "."); ?></span></td>
                            </tr>
                            <tr class="subtitulo">
                                <td colspan="4"></td>
                            </tr>

                            <!-- FIM - TERCEIRA TABELA -->
                            <!-- QUARTA TABELA -->

                            <tr class="titulo">
                                <td colspan="4" class="txcenter">D - AVISOS DE D�BITO N�O LAN�ADOS NO EXTRATO</td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <!-- SUBTABELA -->
                                    <table border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" id="tabD">
                                        <thead>
                                            <tr>
                                                <th>DATA</th>
                                                <th>N�</th>
                                                <th>HIST�RICO</th>
                                                <th>VALOR (R$) <?php if (!$showValues) { ?><span class="fright bt-mais add_container" data-key="tabD">Mais</span><?php } ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!$showValues) { ?>
                                                <tr class="noremove clona_container">
                                                    <td> <input type="text" name="dataD[]" id="dataD[]" value="" size="12" class="data" /> </td>
                                                    <td> <input type="text" name="numD[]" id="numD[]" value="" size="8" /> </td>
                                                    <td> <input type="text" name="historicoD[]" id="historicoD[]" value="" size="30" /> </td>
                                                    <td class="txright"> R$ <input type="text" name="valorD[]" id="valorD[]" value="" size="10" class="valor" /> </td>
                                                </tr>
                                            <?php } else { ?>
                                                <?php foreach ($matrizD as $k => $linesD) { ?>
                                                    <tr class="<?php echo ($k % 2) ? 'even' : 'odd'; ?>">
                                                        <td> <?php echo $linesD['data'] ?> </td>
                                                        <td> <?php echo $linesD['numero'] ?> </td>
                                                        <td> <?php echo $linesD['descricao'] ?> </td>
                                                        <td class="txright"> R$ <?php echo number_format($linesD['valor'], 2, ",", ".") ?> </td>
                                                    </tr>
                                                <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr class="titulo">
                                <td colspan="3" class="txright-i">D1 - TOTAL</td>
                                <td class="txright-i">R$ <span id="totalD"><?php echo number_format($totalD, 2, ",", "."); ?></span></td>
                            </tr>
                            <tr class="subtitulo">
                                <td colspan="4"></td>
                            </tr>

                            <!-- FIM - QUARTA TABELA -->

                            <!-- SALDO TOTAL -->
                            <tr class="titulo">
                                <td colspan="3" class="txright-i">E- SALDO CONT�BIL (A3+B1-C1-D1)</td>
                                <td class="txright-i">R$ <span id="totalFinal"><?php echo number_format($totalFinal, 2, ",", ".") ?></span></td>
                            </tr>

                        </tbody>
                    </table>
                    <br/>

    <?php if (!$btfinalizar) { ?>
                        <div class="divUp">
                            <fieldset>
                                <legend>Arquivos</legend>
                                <input type="hidden" name="id_prestacao" id="id_prestacao" value="<?php echo $historico ?>" />
                                <p><label class="first-2">Extrato Conta Corrente</label> 
                                    <?php if ($fileUpCC) { ?>
                                        <a href="arquivos/conciliacao/<?php echo $fileUpCC ?>" target="_blanck">Download arquivo</a>
                                    <?php } else { ?>
                                        <input type="file" name="cc" id="cc" />
        <?php } ?>
                                </p>
                                <p><label class="first-2">Extrato Conta Poupan�a</label>
                                    <?php if ($fileUpCP) { ?>
                                        <a href="arquivos/conciliacao/<?php echo $fileUpCP ?>" target="_blanck">Download arquivo</a>
                                    <?php } else { ?>
                                        <input type="file" name="cp" id="cp" />
                                <?php } ?>
                                </p>
                                <?php if (!$fileUpCP) { ?>
                                    <p class="controls"> <input type="submit" class="button" value="Enviar Arquivos" name="uploadArquivos" /> </p>
        <?php } ?>
                            </fieldset>
                        </div>
                    <?php } ?>

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
                                <input type="submit" class="button" value="Finalizar Presta��o" name="finalizar" />
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
                            <p>Presta��o finalizada.</p>
                        </div>
                    <?php } ?>


    <?php if ($proj_faltantes > 0) { ?>
                        <div id='message-box' class='message-blue'>
                            <p>Foi verificado a existencia de <?php echo $contErro ?> projeto(s) para finalizar neste m�s antes de gerar o arquivo de presta��o de contas.</p>
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
            </form>
        </div>
    </body>
</html>