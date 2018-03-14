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
$arrItensEditaveis = array("06.01.01", "01.03.04", "01.01.02","07.99.01");

$sql_prestador = "SELECT * FROM prestadorservico WHERE id_projeto = '{$_REQUEST['projeto']}' AND c_razao IS NOT NULL";
$prestador = mysql_query($sql_prestador);


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
        $bancos["-1"] = "Banco não encontrado";
    }
    $return['options'] = $bancos;
    echo json_encode($return);
    exit;
}

$fatorDivisivel = 8;

//CONTAR A QUANTIDADE DE PROJETOS DEPENDENTES DA ADM PARA OBTER O FATOR DIVISIVEL
//$rsFatorDivisivel = montaQueryFirst("projeto", "COUNT(id_projeto) as total", "id_master = {$master} AND administracao = 0 AND prestacontas = 1 AND '".date("Y-m")."-01' BETWEEN inicio AND termino ");
//$fatorDivisivel = $rsFatorDivisivel['total'];
// CASO TENHA PROJETO (EM TODOS OS CASOS DPS DO POST)
if (isset($_REQUEST['projeto'])) {
    $result = true;
    $id_projeto = $_REQUEST['projeto'];
    $id_banco = $_REQUEST['banco'];
    $mes2d = sprintf("%02d", $_REQUEST['mes']); //mes com 2 digitos
    $mesShow = mesesArray($_REQUEST['mes']) . "/" . $_REQUEST['ano'];
    $dataMesRef = "{$_REQUEST['ano']}-{$mes2d}-01";
    $historico = false;
    $fatorDivisivel = $_REQUEST['ftdiv'];

    if ((isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) || (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar']))) {
        //LÓGICA DE VERIFICAÇÃO DE OUTROS PROJETOS EM ABERTOS
        //VERIFICA SE OUTRO PROJETO PRECISA PRESTAR CONTAS NO MES SELECIONADO
        $dataMesIni = "{$_REQUEST['ano']}-{$mes2d}-31";
        $qr_verifica = PrestacaoContas::getQueryVerifica("rateio", $dataMesRef, $dataMesIni, $usuarioW['id_master']);

        $rs_verifica = mysql_query($qr_verifica);
        $total_verifica = mysql_num_rows($rs_verifica);
        $projetosFaltante = array();
        $contErro = 0;
        $finalizados = array();

        while ($rowVeri = mysql_fetch_assoc($rs_verifica)) {
            //VERIFICA SE OS OUTROS NÃO ESTÃO FINALIZADOS, NO CASO DO RATEIO, ADMINISTRAÇÃO NÃO ENTRA
            if ($rowVeri['gerado_embr'] == null && $rowVeri['id_banco'] != $id_banco && $rowVeri['administracao'] == 0) {
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

            //PRESTAÇÕES FINALIZADAS PARA A EXPORTAÇÃO (NÃO É ENVIADO O PROJETO ADM)
            if ($rowVeri['gerado_embr'] != null && $rowVeri['administracao'] == 0) {
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

    //SELEÇÃO DE PRESTAÇÕES FINALIZADAS (DESPESAS E TERCEIROS), UTILIZADO NO FILTRO(SEM HISTORICO) E NO FINALIZAR
    if ((isset($_REQUEST['finalizar']) && !empty($_REQUEST['finalizar'])) || (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar']) && $historico === false)) {
        //Buscando id do projeto ADM
        $projetoAdm = montaQueryFirst("projeto", "*", array("administracao" => "1", "id_master" => $master));
        $id_projetoAdm = $projetoAdm['id_projeto'];

        //BUSCANDO PRETAÇÃO DE DESPESA FINALIZADA
        $rsPrestDesp = mysql_query("SELECT id_prestacao FROM prestacoes_contas WHERE tipo = 'despesa' AND data_referencia = '{$dataMesRef}' AND id_projeto = '{$id_projetoAdm}' AND erros = 0");
        $rowPrestDesp = mysql_fetch_assoc($rsPrestDesp);
        if (!empty($rowPrestDesp)) {
            $toDespesa = 0;
            $qrBase = "SELECT A.id_grupo,B.id as idsub,A.nome_grupo,B.id_subgrupo,B.nome AS subgrupo,C.cod,C.nome,C.id_entradasaida,
                            COUNT(D.id_saida) AS qnt,
                            SUM(D.valor) as total
                            FROM entradaesaida_grupo AS A
                            LEFT JOIN entradaesaida_subgrupo AS B ON (A.id_grupo=B.entradaesaida_grupo)
                            LEFT JOIN entradaesaida AS C ON (LEFT(C.cod,5)=B.id_subgrupo)
                            LEFT JOIN (SELECT id_saida,id_tipo,valor 
                                            FROM prestacoes_contas_desp WHERE id_prestacao = {$rowPrestDesp['id_prestacao']}) AS D ON (D.id_tipo=C.id_entradasaida)
                            WHERE C.id_entradasaida >= 154 AND C.cod != '06.03.01' AND (id_grupo != 30 OR C.cod = '03.26.02')
                            GROUP BY  IF(id_grupo = 10, cod, B.id_subgrupo)
                            HAVING total > 0
                            ORDER BY cod
                        ";
            $rsDesp = mysql_query($qrBase);
            echo "<!--B $qrBase -->";
        }

        //BUSCANDO PRESTACAO TERCEIROS
        $qryPrest = "SELECT id_prestacao FROM prestacoes_contas WHERE tipo = 'terceiro' AND data_referencia = '{$dataMesRef}' AND id_projeto = '{$id_projetoAdm}' AND erros = 0";
        $rsPrestConci = mysql_query($qryPrest);
        $rowPrestConci = mysql_fetch_assoc($rsPrestConci);
        echo "<!--C $qryPrest -->";
        $totalD = 0;
        $matrizTerceiros = array();
        if (!empty($rowPrestConci)) {
            $qrConci = "SELECT id_prestador,servico,razao,mes_atual FROM prestacoes_contas_terceiro WHERE id_prestacao = {$rowPrestConci['id_prestacao']}";
            $rsConci = mysql_query($qrConci);
            echo "<!--D $qrConci -->";
            while ($row = mysql_fetch_assoc($rsConci)) {
                $matrizTerceiros[$row['id_prestador']]['nome'] = $row['servico'];
                $matrizTerceiros[$row['id_prestador']]['valor'] = $row['mes_atual'];
            }
        }
    }

    //CALCULO PRAA ACHAR O PERCENTUAL
    $percentVisual = 100 / $fatorDivisivel;
    $percentCalculo = $percentVisual / 100;

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

    $erro_cadastro = FALSE;
    // realiza teste para saber se já foi criada uma prestação de contas rateio para aquele mes
    $query_teste1 = "SELECT id_prestacao FROM prestacoes_contas WHERE id_projeto = '{$_REQUEST['projeto']}' AND DATE_FORMAT(data_referencia,'%Y-%m') = '$referencia' AND tipo = 'rateio';";
    $result_teste1 = mysql_fetch_assoc(mysql_query($query_teste1));
    $num_rows_teste1 = mysql_num_rows($result_teste1);
    if ($num_rows_teste1 > 0) {
        header("Location: finan_rateio.php?erro=1");
    } else { // se nao tiver continua com o processo normal
        $campos = "id_projeto, id_regiao, id_banco, tipo, data_referencia, gerado_em, gerado_por, linhas, erros, valor_total,status";
        $valores = array(
            $_REQUEST['projeto'],
            $regiao,
            $_REQUEST['banco'],
            "rateio",
            $referencia,
            date("Y-m-d H:i:s"),
            $usuario,
            "0",
            "0",
            "0",
            "1");

        sqlInsert("prestacoes_contas", $campos, $valores);
        $id = mysql_insert_id();

        $cont = 0;
        $matriz = array();

        foreach ($_REQUEST['despAlt'] as $k => $rowDespesa) {
            $matriz[$cont]['id'] = $id;
            $matriz[$cont]['cod'] = $k;
            $matriz[$cont]['nome'] = $_REQUEST['despNome'][$k];
            $matriz[$cont]['total'] = str_replace(",", ".", str_replace(".", "", $_REQUEST['despAlt'][$k]));
            $matriz[$cont]['rateio'] = str_replace(",", ".", str_replace(".", "", $_REQUEST['valor'][$k]));
            $matriz[$cont]['descricao'] = $_REQUEST['descricao'][$k];
            $cont++;
        }

        foreach ($_REQUEST['nometerceiro'] as $k => $terc) {
            $matriz[$cont]['id'] = $id;
            $matriz[$cont]['cod'] = $k;
            //$matriz[$cont]['nome'] = $terc['nome']; EM 24/04/2013 FOI SOLICITADO ESTA ALTERAÇÃO, ONDE SERIA ENVIADO NÃO O NOME DO PRESTADOR, E SIM O SERVIÇO
            $matriz[$cont]['nome'] = $_REQUEST['nometerceiro'][$k];
            $matriz[$cont]['total'] = str_replace(",", ".", str_replace(".", "", $_REQUEST['terceiro'][$k]));
            $matriz[$cont]['rateio'] = str_replace(",", ".", str_replace(".", "", $_REQUEST['valor'][$k]));
            $matriz[$cont]['descricao'] = $_REQUEST['descricao'][$k];
            $cont++;
        }
        
        $campos = array(
            "id_prestacao",
            "cod",
            "despesa",
            "valor",
            "rateio",
            "descricao"
        );
        sqlInsert("prestacoes_contas_rateio", $campos, $matriz);
        echo "<script>location.href='finan_rateio.php'</script>";
        exit;
    }
}
/* MONTA O ARQUIVO PARA BAIXAR */

if (isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) {
    error_reporting(E_ERROR);
    //echo $qr;exit;
    $result = mysql_query($qr);
    $linhas = mysql_num_rows($result);
    $linhasArquivo = ($linhas == 0) ? 5 : $linhas + 5; //CASO NÃO TENHA RESULTADO VAI CONTAR OS PROJETOS A ADD 5 LINHAS (CABEÇALHO)

    $qrT = "SELECT SUM(mes_atual) AS total FROM (" . $qr . ") AS B";
    $resultT = mysql_query($qrT);
    $rowT = mysql_fetch_assoc($resultT);
    $totalMes = number_format($rowT['total'], 2, ",", "");

    $folder = dirname(__FILE__) . "/arquivos/";
    $fname = "OS_{$roMaster['cod_os']}_RTER_" . date("Ymd") . "_" . $mes2d . "{$_REQUEST['ano']}.CSV";
    $filename = $folder . $fname;

    // ESCREVENDO NO ARQUIVO
    // HEADER
    $handle = fopen($filename, "w");
    fwrite($handle, "H;COD_OS;DATA_GERACAO;LINHAS;TIPO;ANO_MES_REF;TIPO_ARQUIVO;VER_DOC;SECRETARIA\r\n");
    fwrite($handle, "H;{$roMaster['cod_os']};" . date("Y-m-d") . ";{$linhasArquivo};N;{$anoMesReferencia};RTER;3.1;01.01.01.01\r\n");

    // DETAIL
    // --CASO NÃO TENHA BENS ADQUIRIDOS NO PERIODO SELECIONADO, MUDAR O CABEÇALHO DO DETALHE--
    if ($linhas == 0) {
        fwrite($handle, "S;COD_OS;COD_UNIDADE;COD_CONTRATO;ANO_MES_REF;DESCRICAO\r\n");
    } else {
        fwrite($handle, "D;COD_OS;COD_UNIDADE;COD_CONTRATO;DATA_APRESENTACAO;RAZAO_SOCIAL;CNPJ;SERVICO;VALOR_MES;VIGENCIA;");
        fwrite($handle, "CONTRATO_ANO_MES_INICIO;CONTRATO_ANO_MES_FIM;REF_TRI;REF_ANO_MES\r\n");
    }

    //ESCREVENDO AS LINHAS NO ARQUIVO CASO TENHA BENS
    if ($linhas >= 1) {
        while ($row = mysql_fetch_assoc($result)) {
            $valor = str_replace(".", ",", $row['mes_atual']);

            fwrite($handle, "D;{$roMaster['cod_os']};{$row['cod_unidade']};{$row['cod_contrato']};{$row['contrato_ini']};{$row['razao']};{$row['cnpj']};{$row['servico']};{$valor};");
            fwrite($handle, "{$row['vigencia']};{$row['contratado_em2']};{$row['encerrado_em2']};{$row['ref_trimestre']};{$anoMesReferencia}\r\n");

            $id_projeto = $row['id_projeto'];
            $id_regiao = $row['id_regiao'];
        }
        unset($row);
    } else {
        fwrite($handle, "S;{$row['cod_sesrj']};556;004/2012;{$anoMesReferencia};Sem movimento;");
        $id_projeto = $_REQUEST['projeto'];
        $id_regiao = 0;
        $linhas = 1; //está dando erro qnd nao tem registro
    }

    fwrite($handle, "T;QUANTIDADE_REGISTROS;TOTAL_VALOR1\r\n");
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
        $qrHistory = "SELECT * FROM prestacoes_contas_rateio WHERE id_prestacao = '{$historico}' AND valor != 0";
        $resultHistory = mysql_query($qrHistory);

        $qrTotalizador = "SELECT SUM(valor) as totalValor, SUM(rateio) AS totalRateio FROM prestacoes_contas_rateio WHERE id_prestacao = '{$historico}'";
        $resultTotais = mysql_query($qrTotalizador);
        $rowTotais = mysql_fetch_assoc($resultTotais);
        $toDespesa = $rowTotais['totalValor'];
        $totalRateio = $rowTotais['totalRateio'];
    }
}

$attrPro = array("id" => "projeto", "name" => "projeto", "class" => "validate[custom[select]]");
$meses = mesesArray(null);
$anos = anosArray(null, null);

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m') - 1;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$bancoR = (isset($_REQUEST['banco'])) ? $_REQUEST['banco'] : null;
$erros = 0;
$idsErros = array();
?>
<html>
    <head>
        <title>:: Intranet :: FINANCEIRO - RATEIO</title>
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
            $(function () {
                $("#model").css({display: "none"});
                var fator = parseFloat($("#fator").html());
                $("#form1").validationEngine();
                $(".valorTerc").maskMoney({decimal: ",", thousands: ".", allowZero: true, allowNegative: true});

                $("#projeto").change(function () {
                    var $this = $(this);
                    if ($this.val() != "-1") {
                        showLoading($this, "../");
                        $.post('finan_rateio.php', {projeto: $this.val(), method: "loadbancos"}, function (data) {
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

                $("body").on('blur', ".valorTerc", function () {
                    var $this = $(this);
                    var valores = new Array();
                    var cont = 0;
                    $(".valorTerc").each(function (i) {
                        var val = $(this).val();
                        if (val != "" && val != "0,00") {
                            valores[cont] = val.replace(/[.]+/g, "").replace(",", ".");
                            cont++;
                        }
                    });

                    $(".valSum").each(function (i) {
                        var val = $(this).html();
                        if (val != "" && val != "0,00") {
                            valores[cont] = val.replace(/[a-zA-Z]\$/g, "").replace(/\./g, "").replace(",", ".");
                            cont++;
                        }
                    });

                    switch (valores.length) {
                        case 0:
                            $("#totalDesp").html("0,00");
                            break;
                        case 1:
                            $("#totalDesp").html(valores[0]);
                            break;
                        default:
                            calcular(valores, "#totalDesp");
                            break;
                    }

                    //VALOR DO RATEIO
                    var rat = parseFloat($(this).val().replace(/[.]+/g, "").replace(",", ".")) / fator;
                    var id = $(this).attr("data-desp");
                    $this.parent().next().next().children('input').val(rat.formatMoney(2, ',', '.'));

                    totalRateio();

                });

                $("body").on('blur', ".valor", function () {
                    totalRateio();
                });

                $("body").on('click', "#adicionar", function () {
                    $("#model").css({display: "block"});
                    thickBoxModal("Cadastro de Item", "#model", "400", "1005");
                });

                $("body").on('change', '#valor_total', function () {
                    var v = parseFloat($(this).val());
                    var percentual = $(".percent").html();
                    var valor = v / 100;
                    var valorFinal = valor * parseFloat(percentual);
                    $("#valor").val(valorFinal.formatMoney(2, ",", "."));
                });
                
                $("body").on('click', "#cadastrar", function () {
                    var nomePrestador = $("#prestador :selected").text();
                    var prestador = $("#prestador").val();
                    if(prestador < 0) { 
                        var contador = parseInt($("#contador").val()) + 1;
                        prestador = contador; 
                        $("#contador").val(contador);
                    }
                    var valorTotal = parseFloat($("#valor_total").val().replace(/[.]+/g, "").replace(",", "."));
                    var valorT = valorTotal.formatMoney(2, ",", ".");
                    var rateio = parseFloat($("#valor").val().replace(/[.]+/g, "").replace(",", "."));
                    var obs = $("#observacao").val();
                    var totalDesp = parseFloat($("#totalDesp").html().replace(/[.]+/g, "").replace(",", "."));
                    var valorFinal = (totalDesp + valorTotal).formatMoney(2, ",", ".");
                    var totalRateio = parseFloat($("#totalRateio").html().replace(/[.]+/g, "").replace(",", "."));
                    var valorRateio = (totalRateio + rateio).formatMoney(2, ",", ".");
                    $("#tbRelatorio .totalizadores").before("<tr><td><input type='text' name='nometerceiro[p" + prestador + "]' id='nometerceiro[p" + prestador + "]' value='" + nomePrestador + "' data-desp='p" + prestador + "' size='60' /></td><td>R$ <input type='text' name='terceiro[p" + prestador + "]' id='terceiro[p" + prestador + "]' value='" + valorT + "' size='10' class='valorTerc' data-desp='p" + prestador + "' /></td><td><?=$percentVisual?></td><td>R$ <input type='text' name='valor[p" + prestador + "]' id='saldo[" + prestador + "]' value='" + rateio.formatMoney(2, ",", ".") + "' size='10' class='valor' data-key='p" + prestador + "' /></td><td><input type='text' name='descricao[p" + prestador + "]' id='descricao[p" + prestador + "]' value='" + obs + "' size='20' /></td></tr>");
                    $("#cadItem input[type=text]").val("");
                    $("#totalDesp").html(valorFinal);
                    $("#totalRateio").html(valorRateio);
                    $("#prestador").val("-1");
                    
                    thickBoxClose();
                });
                
                $("body").on('click', ".remover", function () {
                    $(this).parents("tr").remove();
                });

                $("body").on('click', '#btn_duplicar_rateio', function (){
                    var id_prestacao = $(this).data('prestacao');
                    $('#form_duplicar_rateio').append($('<input>', { type: 'hidden', name: 'id_prestacao', value: id_prestacao })).submit();
                })

            });

            var calcular = function (valores, retorno) {
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

            var totalRateio = function () {
                var valores = new Array();
                var cont = 0;
                $(".valor").each(function (i) {
                    var val = $(this).val();
                    if (val != "" && val != "0,00") {
                        valores[cont] = val.replace(/[.]+/g, "").replace(",", ".");
                        cont++;
                    }
                });

                switch (valores.length) {
                    case 0:
                        $("#totalRateio").html("0,00");
                        break;
                    case 1:
                        $("#totalRateio").html(valores[0]);
                        break;
                    default:
                        calcular(valores, "#totalRateio");
                        break;
                }
            }

            var calculaPercent = function (valor1, valor2, objresult) {
                var resultado = 0;
                var valoreReal = valor1;
                var valor = valor2;
                var A1 = parseFloat(valoreReal);
                var A2 = parseFloat(valor);

                if (A1 > A2) {
                    resultado = 100 - (((A1 - A2) / A1) * 100);
                } else {
                    resultado = 100 - (((A2 - A1) / A2) * 100);
                }

                $(objresult).html(resultado.formatMoney(1, ",", ""));
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
                .noprint{display: none;}
            }
            @media screen
            {
                #headerPrint{display: none;}
                .input{ width: 472px; height: 30px; padding: 6px; margin: 5px 0px; box-sizing: border-box;}
                .select{ width: 977px; height: 30px; padding: 6px; margin: 5px 0px; box-sizing: border-box;}
                .destaque{ float: left;  margin-top: -50px; margin-left: 487px;}
            }
            .remover{
                text-decoration: none;
                color:#333;
                text-align: center;
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
                <h2>FINANCEIRO - RATEIO</h2>

                <fieldset>
                    <legend>Dados</legend>
                    <p><label class="first">Projeto:</label> <?php echo montaSelect(PrestacaoContas::carregaProjetos($master, true), $projetoR, $attrPro) ?></p>
                    <p><label class="first">Banco:</label> <?php echo montaSelect(array("-1" => "« Selecione »"), null, "id='banco' name='banco'") ?></p>
                    <p id="mensal" ><label class="first">Mês:</label> <?php echo montaSelect($meses, $mesR, "id='mes' name='mes' class='validate[custom[select]]'") ?>  <?php echo montaSelect($anos, $anoR, "id='ano' name='ano' class='validate[custom[select]]'") ?> (mês de contratação)</p>
                    <p><label class="first">Fator Divisivel:</label> <input type="text" name="ftdiv" id='ftdiv' value='<?php echo $fatorDivisivel ?>' size='3' /></p>
                    <p class="controls"> <input type="submit" class="button" value="Filtrar" name="filtrar" /> </p>
                </fieldset>

                <?php if (!empty($result)) { ?>
                    <br/>  
                    <br/>
                    <?php if (empty($rowPrestDesp) && $historico === false) { ?>
                        <div id='message-box' class='message-yellow'>
                            <p>Atenção, para continuar a prestação de Despesas da ADMINISTRAÇÃO precisa ser finalizada.</p>
                        </div>
                    <?php } elseif (empty($rowPrestConci) && $historico === false) { ?>
                        <div id='message-box' class='message-yellow'>
                            <p>Atenção, para continuar a prestação de Presdador de Serviço da ADMINISTRAÇÃO precisa ser finalizada.</p>
                        </div>
                    <?php } else { ?>
                        <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Rateio')" value="Exportar para Excel" class="exportarExcel"></p>    
                        <br>
                        <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" width="auto" class="grid">
                            <thead>
                                <tr>
                                    <th colspan="5">Unidade Gerenciada: <?php echo $projeto['nome'] ?></th>
                                    <th><?php echo $mesShow ?></th>
                                </tr>
                                <tr>
                                    <th colspan="6">O responsável: <?php echo $roMaster['nome'] ?></th>
                                </tr>
                                <tr class="noprint">
                                    <th colspan="6">Fator Divisivel: <span id="fator"><?php echo $fatorDivisivel ?></span></th>
                                </tr>
                                <tr>
                                    <th colspan="6">RATEIO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="subtitulo">
                                    <td rowspan="2" class="txcenter">NATUREZA DA DESPESA</td>
                                    <td rowspan="2" class="txcenter">VALOR TOTAL</td>
                                    <td colspan="2" class="txcenter">RATEIO</td>
                                    <td rowspan="2" <?php if ($historico !== false) { echo "colspan='2'";}?> class="txcenter">OBSERVAÇÕES</td>
                                    <?php if ($historico === false) { ?><td rowspan="2" class="txcenter">AÇÃO</td><?php } ?>
                                </tr>
                                <tr class="subtitulo">
                                    <td class="txcenter">%</td>
                                    <td class="txcenter">VALOR</td>
                                </tr>
                                <?php if ($historico === false) { ?>
                                    <?php
                                    while ($rowDespesa = mysql_fetch_assoc($rsDesp)) {
                                        $toDespesa += $rowDespesa['total'];
                                        $valorRateio = $rowDespesa['total'] * $percentCalculo;
                                        $totalRateio += $valorRateio;
                                        ?>
                                        <tr>
                                            <td> <?php echo $rowDespesa['nome'] ?> <input type="hidden" name="despNome[<?php echo $rowDespesa['cod'] ?>]" id="despNome[<?php echo $rowDespesa['cod'] ?>]" value="<?php echo $rowDespesa['nome'] ?>" size="10" /></td>
                                            <td class="txright"> R$ <input type="text" name="despAlt[<?php echo $rowDespesa['cod'] ?>]" id="despAlt[<?php echo $rowDespesa['cod'] ?>]" value="<?php echo number_format($rowDespesa['total'], 2, ",", "."); ?>" size="10" class="valorTerc" data-desp="<?php echo $rowDespesa['cod'] ?>"/></td>
                                            <td class="txright"> <span class="percent"><?php echo $percentVisual ?></span> </td>
                                            <td class="txright"> R$ <input type="text" name="valor[<?php echo $rowDespesa['cod'] ?>]" id="saldo[<?php echo $rowDespesa['cod'] ?>]" value="<?php echo number_format($valorRateio, 2, ",", "."); ?>" size="10" class="valor" data-key="<?php echo $rowDespesa['cod'] ?>"/> </td>
                                            <td class="txcenter"> <input type="text" name="descricao[<?php echo $rowDespesa['cod'] ?>]" id="descricao[<?php echo $rowDespesa['cod'] ?>]" value="" size="20" /> </td>
                                            <td><a href="javascript:;" class="remover">Remover</a></td>
                                        </tr>
                                    <?php } ?>
                                    <?php
                                    foreach ($matrizTerceiros as $k => $terceiro) {
                                        $toDespesa += $terceiro['valor'];
                                        $valorRateio = $terceiro['valor'] * $percentCalculo;
                                        $totalRateio += $valorRateio;
                                        ?>
                                        <tr>
                                            <td> <input type="text" name="nometerceiro[<?php echo $k ?>]" id="nometerceiro[<?php echo $k ?>]" value="<?php echo $terceiro['nome'] ?>" data-desp="<?php echo $k ?>" size="60" /> </td>
                                            <td class="txright"> R$ <input type="text" name="terceiro[<?php echo $k ?>]" id="terceiro[<?php echo $k ?>]" value="<?php echo number_format($terceiro['valor'], 2, ",", "."); ?>" size="10" class="valorTerc" data-desp="<?php echo $k ?>"/> </td>
                                            <td class="txright"> <span class="percent"><?php echo $percentVisual ?></span> </td>
                                            <td class="txright"> R$ <input type="text" name="valor[<?php echo $k ?>]" id="saldo[<?php echo $k ?>]" value="<?php echo number_format($valorRateio, 2, ",", "."); ?>" size="10" class="valor" data-key="<?php echo $k ?>"/> </td>
                                            <td class="txcenter"> <input type="text" name="descricao[<?php echo $k ?>]" id="descricao[<?php echo $k ?>]" value="" size="20" /> </td>
                                            <td><a href="javascript:;" class="remover">Remover</a></td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <?php
                                    while ($row = mysql_fetch_assoc($resultHistory)) {
                                        if ($row['valor'] > $row['rateio']) {
                                            $percent = 100 - ((($row['valor'] - $row['rateio']) / $row['valor']) * 100);
                                        } else {
                                            $percent = 100 - ((($row['rateio'] - $row['valor']) / $row['rateio']) * 100);
                                        }
                                        ?>
                                        <tr>
                                            <td> <?php echo $row['despesa'] ?> </td>
                                            <td class="txright"> R$ <?php echo number_format($row['valor'], 2, ",", ".") ?> </td>
                                            <td class="txright"> <?php echo number_format($percent, 1, ",", ""); ?> </td>
                                            <td class="txright"> R$ <?php echo number_format($row['rateio'], 2, ",", ".") ?> </td>
                                            <td colspan="2" class="txcenter"><?php echo $row['descricao'] ?> </td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                                <tr class="titulo totalizadores">
                                    <td class="txright-i">TOTAL:<input type="hidden" id="contador" value="0"></td>
                                    <td class="txright-i">R$ <span id="totalDesp"><?php echo number_format($toDespesa, 2, ",", "."); ?></span></td>
                                    <td></td>
                                    <td class="txright-i">R$ <span id="totalRateio"><?php echo number_format($totalRateio, 2, ",", "."); ?></span></td>
                                    <td><?php if($btfinalizar){ ?><input type="button" name="adicionar" id="adicionar" value="Adicionar" class="botao" style="width: 153px" /><?php } ?></td>
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
                <?php if ($_REQUEST['erro'] == 1) { ?>
                    <div id='message-box' class='message-yellow'>
                        <h3>Erro ao finalizar Rateío.</h3>
                        <p>Já existe um Rateio para esse Projeto.</p>
                    </div>
                <?php } ?>
            </form>
            <div id="model">
                <form name="cadItem" id="cadItem" action="" method="post">
                    <p>
                        <label for="nome" style="display: block">Prestador</label>
                        <select name="prestador" id="prestador" class="select">
                            <option value="-1">SELECIONE UMA OPÇÃO</option>
                            <?php while ($linha = mysql_fetch_assoc($prestador)) { ?>
                                <option value="<?php echo $linha['id_prestador']; ?>"><?php echo $linha['c_razao']; ?></option>
                            <?php } ?>
                        </select>
                    </p>   
                    <p>
                        <label for="valor_total" style="display: block">Valor Total</label>
                        <input type="text" name="valor_total" id="valor_total" class="input" />
                    </p>
                    <p class="destaque">
                        <label for="porcetagem" style="display: block">Porcentagem</label>
                        <span>12.5%</span>
                    </p>
                    <p>
                        <label for="valor" style="display: block" >Valor</label>
                        <input type="text" name="valor" id="valor"  class="input" />
                    </p>
                    <p>
                        <label for="abservacao" style="display: block">Observação</label>
                        <input type="text" name="observacao" id="observacao"  class="input"  />
                    </p>
                    <p>
                        <input type="button" name="cadastrar" id="cadastrar" value="Cadastrar" class="botao  fright" />
                    </p>
                </form>
            </div>  
        </div>
    </body>
</html>