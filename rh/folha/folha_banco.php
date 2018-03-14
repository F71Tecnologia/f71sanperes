<?php
if(empty($_COOKIE['logado'])){
    print 'Efetue o Login<br><a href="www.netsorrindo.com.br/intranet/login.php">Logar</a>';
    exit;
} 
                                    
include('../../conn.php');
include('../../funcoes.php');
include('../../classes/regiao.php');
include_once('../../wfunction.php');
require_once('../../classes/c_planodecontasClass.php');

$usuario = carregaUsuario();

$Regi = new regiao();
$objSaidaContabil = new c_planodecontasClass(); 
                                    
// RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = str_replace('--', '+', $_REQUEST['enc']);
list($regiao,$folha) = explode('&', decrypt($enc));

//if ($_COOKIE['logado'] == 259) {
//if(isset($_REQUEST['enviar']) == 'Gerar'){
//    // SALVAR LOTE ( CONTABILIDADE )
//    $banco          = $_REQUEST['id_banco'];        
//    $data           = $_REQUEST['data'];         
//    $projeto        = $_REQUEST['projeto_id'];
//    $mesreferenca   = $_REQUEST['mesdereferencia'];
//    $lote           = $_REQUEST['lote_numero'];
//    $ano            = SUBSTR($_REQUEST['data'],6,4); 
//    $mes            = SUBSTR($_REQUEST['data'],3,2);
//    $folha_id       = $_REQUEST['folha'];
//    
//    $qry_verifica_lote = "SELECT lote_numero FROM contabil_lote WHERE lote_numero = '{$lote}' AND status = 1";
//    
//    $verificar = mysql_num_rows(mysql_query($qry_verifica_lote));
//    
//    if ($verificar == 0) {
//        $qryLote = "INSERT INTO contabil_lote (id_projeto, lote_numero, data_criacao, usuario_criacao, ano, mes)
//                    VALUES ('{$projeto}','{$lote}',NOW(),'{$_COOKIE['logado']}', '{$ano}', '{$mes}')";
//                    
//        mysql_query($qryLote) or die (mysql_error());
//    }
//}

$verifica_finan = mysql_num_rows(mysql_query("SELECT * FROM saida WHERE n_documento = {$folha} AND id_banco = {$_REQUEST['banco']} AND tipo = 154 LIMIT 1"));

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'salvar_saida') {
    $vencimento     = implode('-', array_reverse (explode('/',$_REQUEST['vencimento'])));
    $lotenumero     = $_REQUEST['lotenumero'];
    $projeto        = $_REQUEST['projeto'];
    $mesreferente   = $_REQUEST['referente'];
    $folha          = $_REQUEST['folha_id'];
    $array_clts     = $_REQUEST['clts'];
    $banco          = $_REQUEST['banco'];
    $clts           = implode(',', $array_clts);
            
    // SALVAR LANÇAMENTO NO FINANCEIRO
    $qry_folha = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '{$folha}'");
    $row_folha = mysql_fetch_assoc($qry_folha);

    $qry_tipo = mysql_query("SELECT A.id_entradasaida, A.nome FROM entradaesaida AS A WHERE A.grupo = '10' ORDER BY A.id_entradasaida ");
    
    while ($row_tipo = mysql_fetch_assoc($qry_tipo)){
        $array[$row_tipo['id_entradasaida']] = $row_tipo['nome'];
    }
    if ($row_folha['terceiro'] == 1) { $tipo = 155; }
    else { $tipo = 154; }
    
    $qryFolha = "SELECT B.id_projeto, A.id_projeto, A.id_regiao, A.mes, A.ano,
                SUM(A.salliquido) AS '154', SUM(A.a5020) AS '169', SUM(A.imprenda) AS '168', 
                SUM(A.fgts) AS '167', (SUM(A.a6004) + SUM(A.a7009)) '72', B.nome AS nome_projeto
                FROM rh_folha_proc AS A  
                INNER JOIN projeto AS B ON (B.id_projeto = A.id_projeto)
                WHERE A.id_folha = '{$folha}' AND A.id_clt IN($clts) AND A.status = 3 AND A.salliquido > 0";
    
    $row_total = mysql_fetch_assoc(mysql_query($qryFolha));
//    $id_folha_proc = $row_total['id_folha_proc'];
    foreach ($array as $key => $value) {
        $nome               = $value.' - '.$row_total['mes'].'/'.$row_total['ano'];
        $especifica         = $value.' - '.$row_total['nome_projeto'].' - REF '.$row_total['mes'].'/'.$row_total['ano'];
        $valor              = $row_total[$key];
        $id_regiao          = $row_total['id_regiao'];
        $id_projeto         = $row_total['id_projeto'];
        $mes_competencia    = $row_total['mes']; 
        $ano_competencia    = $row_total['ano'];        
        if($valor > 0){
            $sqlSalario = "('{$id_regiao}','{$id_projeto}','{$banco}','{$_COOKIE[logado]}','{$nome}','{$especifica}','{$key}','{$valor}',NOW(),'{$vencimento}','1','13','1','{$folha}','{$mes_competencia}','{$ano_competencia}' )";
            $qryInsertSaida = "INSERT INTO saida (id_regiao,id_projeto,id_banco,id_user,nome,especifica,tipo,valor,data_proc,data_vencimento,status,id_tipo_pag_saida,entradaesaida_subgrupo_id,n_documento,mes_competencia, ano_competencia)
                       VALUES $sqlSalario";
            $qry_insert_saida = mysql_query($qryInsertSaida) or die(mysql_error());
            $id_saida = mysql_insert_id();
            //LANÇAMETO CONTABIL
            $array_lancamento = array('id_saida' => $id_saida, 'id_projeto' => $id_projeto, 'id_usuario' => $usuario['id_funcionario'], 'data_lancamento' => date("Y-m-d"), 'historico' => $especifica);
            $id_lancamento = $objSaidaContabil->inserirLancamento($array_lancamento);
        }
    }
    
//    if ($qry_insert_saida) {
//        mysql_query("UPDATE rh_folha_proc SET financeiro = 1 WHERE id_folha_proc IN($id_folha_proc)");
//    }
//    
//    $qryFolhaSalarios = "SELECT A.* 
//                FROM rh_folha_proc AS A
//                INNER JOIN projeto AS B ON (B.id_projeto = A.id_projeto)
//                WHERE A.id_folha = '{$folha}' AND A.id_clt IN($clts) AND A.status = 3 AND A.salliquido > 0";
//                
//    $result_folha_pro = mysql_query($qryFolhaSalarios) or die ("Erro na query da folha:<br/>".  mysql_error() ."<br/>");
//                 
//    while ($row_folha_proc = mysql_fetch_assoc($result_folha_pro)){
//        $valor = $row_folha_proc['salliquido'];
//        $id_folha_proc[] = $row_folha_proc['id_folha_proc'];
//        $sql[] = "('{$row_folha_proc[id_clt]}','{$folha}', '{$projeto}','{$valor}')";
//    }
//
//    $sql = implode(',', $sql);
//    $id_folha_proc = implode(',', $id_folha_proc);
//    
//    $qrFull = "INSERT INTO salarios_pagos (id_clt, id_folha, id_projeto, salario)
//            VALUES $sql";    
//    
//     $qr_insert = mysql_query($qrFull) or die(mysql_error() . ": <br/>{$qrFull}");

//    // SALVAR LANÇAMENTO ( CONTABILIDADE )
//    $sql_pesquisaLote = "SELECT id_lote FROM contabil_lote WHERE lote_numero = '{$lotenumero}' AND status = 1";
//    
//    $row = mysql_fetch_assoc(mysql_query($sql_pesquisaLote));
//    $lote_id = $row['id_lote'];
//    
//    $qryLancamento = "INSERT INTO contabil_lancamento (id_lote, id_projeto, id_usuario, data_lancamento, historico, contabil, status)
//                    VALUES ('{$lote_id}','{$projeto}','{$_COOKIE['logado']}',NOW(),'SALARIOS {$nomeprojeto} REF {$mesreferente}','1','1')";
//    
//    mysql_query($qryLancamento) or die (mysql_error());
//    
//    // SALVAR ITENS DO LANÇAMENTO ( CONTABILIDADE )
//    
//        
    echo json_encode(array('status'=>TRUE));
    exit();
} 

if(isset($_REQUEST['agencia']) && !empty($_REQUEST['agencia'])){

	$ag  		= $_REQUEST['agencia'];
	$folha  	= $_REQUEST['folha'];
	$cc  		= $_REQUEST['conta'];
	$clt 		= $_REQUEST['clt'];
	$tipo_conta     = $_REQUEST['radio_tipo_conta'];
        
        $qr1 = "UPDATE rh_clt        SET agencia='$ag', conta='$cc', tipo_conta='$tipo_conta' WHERE id_clt = '$clt' LIMIT 1";
        $qr2 = "UPDATE rh_folha_proc SET agencia='$ag', conta='$cc' WHERE id_clt = '$clt' AND id_folha = $folha LIMIT 1";
	
        echo "\r\n<!-- $qr1 \r\n $qr2 -->\r\n";
        
        mysql_query($qr1) or die (mysql_error());
	mysql_query($qr2) or die (mysql_error());

}

$banco              = $_REQUEST['banco'];
$banco_participante = $_REQUEST['banco_participante'];
$dataPagamento      = $_REQUEST['data'];
$numero_lote        = $_REQUEST['loteNumero'];
list($d,$m,$a) 		 = explode('/', $dataPagamento);
list($dia,$mes,$ano) = explode('/', date('d/m/Y'));

$id_user = $_COOKIE['logado'];

$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user    = mysql_fetch_array($result_user);

$result_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$banco'");
$row_banco 	  = mysql_fetch_array($result_banco);

$result_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row_banco[id_regiao]'");
$row_regiao    = mysql_fetch_array($result_regiao);

$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]'");
$row_master    = mysql_fetch_array($result_master);

$result_folha = mysql_query("SELECT * , date_format(data_proc, '%d/%m/%Y') AS data_proc2, date_format(data_inicio, '%d/%m/%Y') AS data_inicio, date_format(data_fim, '%d/%m/%Y') AS data_fim FROM rh_folha WHERE id_folha = '$folha'");
$row_folha 	  = mysql_fetch_array($result_folha);

$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_folha[projeto]'");
$row_projeto 	= mysql_fetch_array($result_projeto);

$meses        = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$mes_inteiro  = (int)$row_folha['mes'];
$mes_da_folha = $meses[$mes_inteiro];

$titulo = "Folha Sintética: Projeto $row_projeto[nome] mês de $mes_da_folha";

$data_menor14 = date('Y-m-d', mktime(0,0,0, $mes,$dia,$ano - 14));
$data_menor21 = date('Y-m-d', mktime(0,0,0, $mes,$dia,$ano - 21));

///EMPRESA
$qr_empresa = mysql_query("SELECT * FROM rhempresa where id_regiao = '$row_folha[regiao]' AND id_projeto = '$row_folha[projeto]'");
$row_empresa = mysql_fetch_assoc($qr_empresa);
?>
<html>
    <head>
        <script type="text/javascript" src="../../js/jquery-1.9.0.min.js"></script>
        <script src="../../resources/js/main.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script type="text/javascript" src="../../js/prototype.js"></script>
        <script type="text/javascript" src="../../js/scriptaculous.js?load=effects,builder"></script>
        <script type="text/javascript" src="../../js/lightbox.js"></script>
        <script type="text/javascript" src="../../js/highslide-with-html.js"></script>
        <script type="text/javascript" src="../../jquery/jquery_ui/js/jquery-ui-1.8.21.custom.min.js"></script>
        <script type="text/javascript" src="../../jquery.uploadify-v2.1.4/jquery.uploadify.v2.1.4.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        
        <link rel="stylesheet" href="../../js/lightbox.css" type="text/css" media="screen"/>
        <link rel="stylesheet" type="text/css" href="../../js/highslide.css" />
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        
        <title><?=$titulo?></title>
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript">
            hs.graphicsDir = '../../images-box/graphics/';
            hs.outlineType = 'rounded-white';
        </script>
        <style type="text/css">
            .bota_o1 {
                background-color:#eee;
                padding:4px;
                border:1px solid #ccc;
                display:block;
                width:260px;
                margin-right:5px;
                color:#222;
                font-size:12px;
                text-decoration:none;
                cursor:pointer;
                font-weight:bold;
                text-align:center;
            }
            .bota_o1:hover {
                background-color:#069;
                border:1px solid #FFF;
                color: #FFF;
            }
        </style>
    </head>
    <body>
        <table width="95%" border="0" align="center">
            <tr>
                <td align="center" valign="middle" bgcolor="#FFFFFF">
                    <div style="font-size:9px; text-align:left; color:#E2E2E2;  float: left ">
                        <b>
                            FOLHA ID : <?php
                            echo $folha . ", " . $Regi->MostraRegiao($row_folha['regiao']);
                            echo $Regi->regiao . " - " . $row_empresa['razao'];
                            echo " - CLT - Banco: $banco_participante " . $row_banco['nome'];
                            ?>
                        </b>
                    </div>
                    <div style="font-size:9px; text-align:right; color:#E2E2E2;">
                        <b>
                            <?php echo "LOTE CONTABIL: ".$_REQUEST['lote_numero']; ?> 
                        </b>
                    </div>

                    <table width="90%" border="0" align="center">
                        <tr>
                            <td width="100%" height="93" align="center" class="show">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="16%"><img src="../../imagens/logomaster1.gif" alt="" width="110" height="79" align="absmiddle" ></td>
                                        <td width="62%">
                                            <?= $row_empresa['razao'] ?><br>
                                            CNPJ  <?= $row_empresa['cnpj'] ?><br>
                                        </td>
                                        <td width="22%">
                                            Data de Processamento: <?= $row_folha['data_proc2'] ?> <br>
                                            Data para Pagamento: <?= $d . '/' . $m . '/' . $a ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <table width="325" border="0">
                        <tr>
                            <!--<td width="52"><img src="../../imagens/bancos/<?= $row_banco['id_nacional'] ?>.jpg" width="50" height="50"></td>-->
                            <td width="257"><div style="font-size:16px">&nbsp;<?= $row_banco['nome'] ?></div></td>
                        </tr>
                    </table>
                    <br/>
                    <span class="title">Folha de Pagamento - <?= $mes_da_folha ?> / <?= $ano ?></span>
                    <br/><br/>
                    <p style="text-align: right;">
                    <button type="button" onclick="tableToExcel('tbExcel', 'Folha Sintética')" class="exportarExcel">Exportar para Excel</button>
                    <a name="pdf" data-title="Folha Sintética - <?= $mes_da_folha ?> / <?= $ano ?>" data-id="tbRelatorio" id="pdf" value="Gerar PDF" class="exportarExcel" style="cursor: pointer"><i class="fa fa-file-pdf-o"></i> Gerar PDF</a></p>
                    <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="tbRelatorio" id="tbExcel">
                        <tr>
                            <td  height="25" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">C&oacute;digo</td>
                            <td  align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Nome </td>
                            <td  align="right" valign="middle" bgcolor="#CCCCCC" class="style23">CPF</td>
                            <td  align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Agência</td>
                            <td  align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Agência DV</td>
                            <td  align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Conta</td>
                            <td  align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Conta DV</td>
                            <td  align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Sal. Base</td>
                            <td  align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Rendim.</td>
                            <td  align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Descontos </td>
                            <td  align="right" valign="middle" bgcolor="#CCCCCC" class="style23">INSS</td>
                            <td  align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Imp. Renda</td>
                            <td  align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Sal. Fam. </td>
                            <td  align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Sal. L&iacute;q.</td>
                            <td  align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Tipo Conta</td>          
                        </tr>

                        <?php
                        // VERIFICA OS TIPOS DE PAGAMENTOS DA REGIÃO E PROJETO ATUAL
                        $tiposDePagamentos = mysql_query("SELECT * FROM tipopg WHERE id_regiao = '$regiao' AND campo1 = '1' AND id_projeto = '$row_projeto[0]'");
                        $rowTipoPg = mysql_fetch_array($tiposDePagamentos);

                        if ($row_banco['id_nacional'] == '237') {
                            // Nome do Arquivo Texto
                            $CONSTANTE = 'FP';
                            $DD = date('d');
                            $MM = date('m');
                            $NUM_ARQUIVO01 = '2';
                            $NUM_ARQUIVO02 = '1';
                            $TIPO = 'TST';

                            // VERIFICA QUAIS OS TIPOS DE CONTAS QUE O FECHAMENTO POSSUI E PREENCHE UMA VARIÁVEL ESPECÍFICA COM O TIPO DE CONTRA ENCONTRADO
                            $resultContas = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' AND status = '3' AND id_banco = '$banco_participante' AND tipo_pg = '$rowTipoPg[id_tipopg]' AND salliquido > '0.01'");
                            while ($rowContas = mysql_fetch_array($resultContas)) {
                                $resultTiposDeConta = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = '$rowContas[id_clt]'");
                                $rowTiposDeConta = mysql_fetch_array($resultTiposDeConta);

                                if (($rowTiposDeConta['tipo_conta'] == 'corrente') and ( $rowTiposDeConta['tipo_conta'] != '')) {

                                    $contaCorrente = 'corrente';
                                } elseif (($rowTiposDeConta['tipo_conta'] == 'salario') and ( $rowTiposDeConta['tipo_conta'] != '')) {
                                    
                                    $contaSalario = 'salario';
                                }
                            }

                            //EXECUTA OS CABEÇALHOS PARA OS TIPOS DE ARQUIVOS ENCONTRADOS
                            if ($contaCorrente != '') {
                                $NUM_ARQUIVO = $NUM_ARQUIVO01;
                                include "BANCOS/BRADESCO/header_bradesco_corrente.php";
                            }

                            if ($contaSalario != '') {
                                $NUM_ARQUIVO = $NUM_ARQUIVO02;
                                include "BANCOS/BRADESCO/header_bradesco_salario.php";
                            }
                        } else if ($row_banco['id_nacional'] == '356') {

                            $CONSTANTE = 'FP_BANCO_REAL_' . $regiao . '_' . $folha;
                            $DD = date('d');
                            $MM = date('m');
                            $ANO = date('Y');
                            $NUM_ARQUIVO01 = '1';
                            $NUM_ARQUIVO02 = '2';
                            $NUM_ARQUIVO03 = '3';

                            //VERIFICA QUAIS OS TIPOS DE CONTAS QUE O FECHAMENTO POSSUI E PREENCHE UMA VARIÁVEL ESPECÍFICA COM O TIPO DE CONTRA ENCONTRADO
                            $resultContas = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' and status = '3' and id_banco = '$banco_participante' and tipo_pg = '$rowTipoPg[id_tipopg]' AND salliquido > '0.01'");
                            while ($rowContas = mysql_fetch_array($resultContas)) {
                                $resultTiposDeConta = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = '$rowContas[id_clt]'");
                                $rowTiposDeConta = mysql_fetch_array($resultTiposDeConta);
                                if ($rowTiposDeConta['tipo_conta'] == 'corrente') {
                                    $contaCorrente = 'corrente';
                                } else if ($rowTiposDeConta['tipo_conta'] == 'salario') {
                                    $contaSalario = 'salario';
                                }
                            }

                            if ($contaCorrente != '') {
                                include "BANCOS/REAL/header_arquivo_real_corrente.php";
                                include "BANCOS/REAL/header_lote_real_corrente.php";
                            }

                            if ($contaSalario != '') {
                                include "BANCOS/REAL/header_arquivo_real_salario.php";
                                include "BANCOS/REAL/header_lote_real_salario.php";
                            }
                        } else if ($row_banco['id_nacional'] == '033') {
                            
                            $CONSTANTE = 'FP_BANCO_SANTANDER_' . $regiao . '_' . $folha;
                            $DD = date('d');
                            $MM = date('m');
                            $ANO = date('Y');
                            $NUM_ARQUIVO01 = '1';
                            $NUM_ARQUIVO02 = '2';
                            $NUM_ARQUIVO03 = '3';

                            //VERIFICA QUAIS OS TIPOS DE CONTAS QUE O FECHAMENTO POSSUI E PREENCHE UMA VARIÁVEL ESPECÍFICA COM O TIPO DE CONTRA ENCONTRADO
                            $resultContas = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' and status = '3' and id_banco = '$banco_participante' and tipo_pg = '$rowTipoPg[id_tipopg]' AND salliquido > '0.01'");

                            while ($rowContas = mysql_fetch_array($resultContas)) {
                                $resultTiposDeConta = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = '$rowContas[id_clt]'");
                                $rowTiposDeConta = mysql_fetch_array($resultTiposDeConta);
                                if ($rowTiposDeConta['tipo_conta'] == 'corrente') {
                                    $contaCorrente = 'corrente';
                                } else if ($rowTiposDeConta['tipo_conta'] == 'salario') {
                                    $contaSalario = 'salario';
                                }
                            }

                            if ($contaCorrente != '') {
                                include "BANCOS/SANTANDER/header_arquivo_santander_corrente.php";
                                include "BANCOS/SANTANDER/header_lote_santander_corrente.php";
                            }

                            if ($contaSalario != '') {
                                include "BANCOS/SANTANDER/header_arquivo_santander_salario.php";
                                include "BANCOS/SANTANDER/header_lote_santander_salario.php";
                            }
                        } else if ($row_banco['id_nacional'] == '341') {
                            $CONSTANTE = 'FP_BANCO_ITAU_' . $regiao . '_' . $folha;
                            $DD = date('d');
                            $MM = date('m');
                            $ANO = date('Y');
                            //VERIFICA QUAIS OS TIPOS DE CONTAS QUE O FECHAMENTO POSSUI E PREENCHE UMA VARIÁVEL ESPECÍFICA COM O TIPO DE CONTRA ENCONTRADO
                            $resultContas = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' and status = '3' and id_banco = '$banco_participante' and tipo_pg = '$rowTipoPg[id_tipopg]' AND salliquido > '0.01'");
                            while ($rowContas = mysql_fetch_array($resultContas)) {
                                $resultTiposDeConta = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = '$rowContas[id_clt]'");
                                $rowTiposDeConta = mysql_fetch_array($resultTiposDeConta);
                                if ($rowTiposDeConta['tipo_conta'] == 'corrente') {
                                    $contaCorrente = 'corrente';
                                } else if ($rowTiposDeConta['tipo_conta'] == 'salario') {
                                    $contaSalario = 'salario';
                                }
                            }

                            //EXECUTA OS CABEÇALHOS PARA OS TIPOS DE ARQUIVOS ENCONTRADOS
                            if ($contaCorrente != '') {
                                include "BANCOS/ITAU/header_itau_corrente.php";
                            }
                            if ($contaSalario != '') {
                                include "BANCOS/ITAU/header_itau_salario.php";
                            }
                        } else if ($row_banco['id_nacional'] == '001') {

                            $CONSTANTE = 'FP_BANCO_BRASIL_' . $regiao . '_' . $folha;
                            $DD = date('d');
                            $MM = date('m');
                            $ANO = date('Y');
                            //VERIFICA QUAIS OS TIPOS DE CONTAS QUE O FECHAMENTO POSSUI E PREENCHE UMA VARIÁVEL ESPECÍFICA COM O TIPO DE CONTRA ENCONTRADO
                            $resultContas = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' AND status = '3' AND id_banco = '$banco_participante' AND tipo_pg = '$rowTipoPg[id_tipopg]' AND salliquido > '0.01'");
                            while ($rowContas = mysql_fetch_array($resultContas)) {
                                $resultTiposDeConta = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = '$rowContas[id_clt]'");
                                $rowTiposDeConta = mysql_fetch_array($resultTiposDeConta);
                                if ($rowTiposDeConta['tipo_conta'] == 'corrente') {
                                    $contaCorrente = 'corrente';
                                } else if ($rowTiposDeConta['tipo_conta'] == 'salario') {
                                    $contaSalario = 'salario';
                                }
                            }

                            // EXECUTA OS CABEÇALHOS PARA OS TIPOS DE ARQUIVOS ENCONTRADOS
                            if ($contaCorrente != '') {
                                include "BANCOS/BRASIL/header_brasil_corrente.php";
                            }
                            if ($contaSalario != '') {
                                include "BANCOS/BRASIL/header_brasil_salario.php";
                            }
                        } else if ($row_banco['id_nacional'] == '399') {

                            $CONSTANTE = 'FP_BANCO_HSBC_' . $regiao . '_' . $folha;
                            $DD = date('d');
                            $MM = date('m');
                            $ANO = date('Y');
                            
                            $fileNameHsbc = "BANCOS/HSBC/CONTA_CORRENTE/" . $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
                            //VERIFICA QUAIS OS TIPOS DE CONTAS QUE O FECHAMENTO POSSUI E PREENCHE UMA VARIÁVEL ESPECÍFICA COM O TIPO DE CONTRA ENCONTRADO
                            $resultContas = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' AND status = '3' AND id_banco = '$banco_participante' AND tipo_pg = '$rowTipoPg[id_tipopg]' AND salliquido > '0.01'");
                            while ($rowContas = mysql_fetch_array($resultContas)) {
                                $resultTiposDeConta = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = '$rowContas[id_clt]'");
                                $rowTiposDeConta = mysql_fetch_array($resultTiposDeConta);
                                if ($rowTiposDeConta['tipo_conta'] == 'corrente') {
                                    $contaCorrente = 'corrente';
                                } else if ($rowTiposDeConta['tipo_conta'] == 'salario') {
                                    $contaSalario = 'salario';
                                }
                            }

                            // EXECUTA OS CABEÇALHOS PARA OS TIPOS DE ARQUIVOS ENCONTRADOS
                            if ($contaCorrente != '') {
                                include "BANCOS/HSBC/header_hsbc_corrente.php";
                            }
                            if ($contaSalario != '') {
                                include "BANCOS/HSBC/header_hsbc_salario.php";
                            }
                        }

                        $cont = "0";

                        $resultClt = mysql_query("SELECT A.*
                                                    FROM rh_folha_proc AS A
                                                    /*LEFT JOIN rh_clt AS B ON (B.id_clt = A.id_clt)*/
                                                    WHERE A.id_folha = '$folha' AND A.status = '3' AND A.id_banco = '$banco_participante'  AND A.salliquido > '0.01' 
                                                    ORDER BY A.nome ASC");
                        
                        while ($row_clt = mysql_fetch_array($resultClt)) {

                            $REtabCLT = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = '$row_clt[id_clt]'");
                            $RowTabCLT = mysql_fetch_array($REtabCLT);

                            // Verificando Tipo de Pagamento
                            $qr_tipo_pg = mysql_query("SELECT tipopg FROM tipopg WHERE id_tipopg = '$row_clt[tipo_pg]'");
                            @$tipo_pg = mysql_result($qr_tipo_pg, 0);

                            if (strstr($tipo_pg, 'Conta') or strstr($tipo_pg, 'conta')) {

                                //---- EMBELEZAMENTO DA PAGINA ----------------------------------
                                $nome = str_split($row_clt['nome'], 30);
                                $nomeT = sprintf("% -30s", $nome[0]);
                                //-----------------		  		  
                                //----FORMATANDO OS VALORES FORMATO BRASILEIRO PARA VISUALIZAÇÃO (5.100,00) ---------
                                $salario_brutoF = number_format($row_clt['salbase'], 2, ",", ".");
                                $total_rendiF = number_format($row_clt['rend'], 2, ",", ".");
                                $total_debitoF = number_format($row_clt['desco'], 2, ",", ".");
                                $valor_inssF = number_format($row_clt['a5020'], 2, ",", ".");
                                $valor_IRF = number_format($row_clt['a5021'], 2, ",", ".");
                                $valor_familiaF = number_format($row_clt['a5022'], 2, ",", ".");

                                $valor_final_individualF = number_format($row_clt['salliquido'], 2, ",", ".");
                                //-------------------

                                $resultTipoConta = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = '$row_clt[id_clt]'");
                                $rowTipoConta = mysql_fetch_array($resultTipoConta);

                                switch ($rowTipoConta['tipo_conta']) {
                                    case 'salario': $tipoConta = 'Conta Salário';
                                    break;
                                    case 'corrente': $tipoConta = 'Conta Corrente';
                                    break;
                                    default: $tipoConta = '&nbsp;';
                                }

                                $tipoR = $RowTabCLT['tipo_conta'];

                                if ($tipoR == 'salario') {
                                    $checkedSalario = 'checked';
                                } elseif ($tipoR == 'corrente') {
                                    $checkedCorrente = 'checked';
                                }


                                $alink = "<a href='#' onclick=\"return hs.htmlExpand(this, { outlineType: 'rounded-white', 
                                wrapperClassName: 'draggable-header',headingText: '$nomeT' } )\" class='highslide'>";

//                                $divTT = "<div class='highslide-maincontent'>
//                                            <form action='' method='post' name='form'>
//                                                <table width='526' border='0' cellspacing='0' cellpadding='0'>
//                                                    <tr>			  
//                                                        <td align='right'>Agencia</td>
//                                                        <td>&nbsp;<input name='agencia' type='text' size='15' maxlength='10' id='agencia' value='$row_clt[agencia]'/>&nbsp;</td>
//                                                        <td align='right'>Agencia DV</td>
//                                                        <td>&nbsp;<input name='agencia_dv' type='text' size='15' maxlength='10' id='agencia_dv' value='$row_clt[agencia_dv]'/>&nbsp;</td>
//                                                        <td align='right'>Conta</td>
//                                                        <td>&nbsp;<input name='conta' type='text' size='15' maxlength='10' id='conta' value='$row_clt[conta]'/></td>
//                                                        <td align='right'>Conta DV</td>
//                                                        <td>&nbsp;<input name='conta_dv' type='text' size='15' maxlength='10' id='conta_dv' value='$row_clt[conta_dv]'/></td>
//                                                        <td><input type='submit' value='Enviar' /></td>
//                                                    </tr>
//                                                    <tr>
//                                                        <td align='right'>Tipo de Conta</td>
//                                                        <td colspan='3'>&nbsp;
//                                                        <label><input type='radio' name='radio_tipo_conta' value='salario' $checkedSalario>Conta Salário </label>
//                                                        &nbsp;&nbsp;
//                                                        <label><input type='radio' name='radio_tipo_conta' value='corrente' $checkedCorrente>Conta Corrente </label></td>
//                                                    </tr>			  
//                                                </table>
//                                                <!-- <input type='hidden' name='enc' value='{$enc}'> -->
//                                                <input type='hidden' name='clt' value='{$row_clt['id_clt']}'>
//                                                <input type='hidden' name='banco' value='{$banco}'>
//                                                <input type='hidden' name='banco_participante' value='{$banco_participante}'>
//                                                <input type='hidden' name='data' value='{$dataPagamento}'>
//                                                <input type='hidden' name='folha' value='{$folha}'>
//                                            </form>
//                                        </div>";

                                $bgclass = ($cont % 2) ? "corfundo_um" : "corfundo_dois";

                                print"
                                <tr class='novalinha $bgclass'>
                                    <td align='center' valign='middle' $bord style='font-size:10px'>$row_clt[cod]
                                        <input type='hidden' name='clts[]' class='clts' value='{$row_clt['id_clt']}'> </td>
                                    <td align='lefth' valign='middle' $bord style='font-size:10px'>$nomeT</td>
                                    <td align='right' valign='middle' $bord style='font-size:10px' >$row_clt[cpf]</td>
                                    <td align='right' valign='middle' $bord style='font-size:10px'>$row_clt[agencia]</td>
                                    <td align='right' valign='middle' $bord style='font-size:10px'>$row_clt[agencia_dv]</td>
                                    <td align='right' valign='middle' $bord style='font-size:10px'>$row_clt[conta]</td>		  
                                    <td align='right' valign='middle' $bord style='font-size:10px'>$row_clt[conta_dv]</td>		  
                                    <td align='right' valign='middle' $bord style='font-size:10px'>$salario_brutoF</td>
                                    <td align='right' valign='middle' $bord style='font-size:10px'>$total_rendiF</td>
                                    <td align='right' valign='middle' $bord style='font-size:10px'>$total_debitoF</td>
                                    <td align='right' valign='middle' $bord style='font-size:10px'>$valor_inssF</td>
                                    <td align='right' valign='middle' $bord style='font-size:10px'>$valor_IRF</td>
                                    <td align='right' valign='middle' $bord style='font-size:10px'>$valor_familiaF</td>
                                    <td align='right' valign='middle' $bord style='font-size:10px'>$valor_final_individualF</td>
                                    <td align='right' valign='middle' $bord style='font-size:10px'> $tipoConta </td>		  
                                </tr>";

                                unset($checkedSalario);
                                unset($checkedCorrente);

                                if ($row_banco['id_nacional'] == '237') {
                                    $tipoContaCorrente01 = $rowTipoConta['tipo_conta'];
                                    if ($tipoContaCorrente01 == 'corrente') {
                                        $NUM_ARQUIVO = $NUM_ARQUIVO01;
                                        $statusContaCorrente = 'corrente';
                                        include "BANCOS/BRADESCO/detalhes_bradesco_corrente.php";
                                    } else if ($tipoContaCorrente01 == 'salario') {
                                        $NUM_ARQUIVO = $NUM_ARQUIVO02;
                                        $statusContaSalario = 'salario';
                                        include "BANCOS/BRADESCO/detalhes_bradesco_salario.php";
                                    }
                                }

                                if ($row_banco['id_nacional'] == '001') {
                                    $tipoContaCorrente01 = $rowTipoConta['tipo_conta'];
                                    if (($tipoContaCorrente01 == 'corrente') and ( $row_banco['id_nacional'] == '001')) {
                                        $statusContaCorrente = 'corrente';
                                        include "BANCOS/BRASIL/detalhes_brasil_corrente.php";
                                    } else if (($tipoContaCorrente01 == 'salario') and ( $row_banco['id_nacional'] == '001')) {
                                        $statusContaSalario = 'salario';
                                        include "BANCOS/BRASIL/detalhes_brasil_salario.php";
                                    }
                                } if ($row_banco['id_nacional'] == '356') {
                                    $tipoContaCorrente01 = $rowTipoConta['tipo_conta'];
                                    if ($tipoContaCorrente01 == 'corrente') {
                                        //VALOR TOTAL DO ARQUIVO TXT PARA CONTAS CORRENTE  
                                        $VALOR = $row_clt['salliquido'];
                                        //$arrayValorTotalCorrente[] = $VALOR;
                                        $remover = array(".", "-", "/", ",");
                                        $VALOR = str_replace($remover, "", $VALOR);
                                        $VALOR = sprintf("%013d", $VALOR);
                                        $statusContaCorrente = 'corrente';
                                        include "BANCOS/REAL/detalhes_real_corrente.php";
                                    } else if ($tipoContaCorrente01 == 'salario') {
                                        //VALOR TOTAL DO ARQUIVO TXT PARA CONTAS SALÁRIO	  
                                        $VALOR = $row_clt['salliquido'];
                                        //$arrayValorTotalSalario[] = $VALOR;
                                        $remover = array(".", "-", "/", ",");
                                        $VALOR = str_replace($remover, "", $VALOR);
                                        $VALOR = sprintf("%013d", $VALOR);
                                        $statusContaSalario = 'salario';
                                        include "BANCOS/REAL/detalhes_real_salario.php";
                                    }
                                }if ($row_banco['id_nacional'] == '033') {
                                    $tipoContaCorrente01 = $rowTipoConta['tipo_conta'];
                                    if ($tipoContaCorrente01 == 'corrente') {
                                        //VALOR TOTAL DO ARQUIVO TXT PARA CONTAS CORRENTE  
                                        $VALOR = $row_clt['salliquido'];
                                        //$arrayValorTotalCorrente[] = $VALOR;
                                        $remover = array(".", "-", "/", ",");
                                        $VALOR = str_replace($remover, "", $VALOR);
                                        $VALOR = sprintf("%013d", $VALOR);
                                        $statusContaCorrente = 'corrente';
                                        include "BANCOS/SANTANDER/detalhes_santander_corrente.php";
                                    } else if ($tipoContaCorrente01 == 'salario') {
                                        //VALOR TOTAL DO ARQUIVO TXT PARA CONTAS SALÁRIO	  
                                        $VALOR = $row_clt['salliquido'];
                                        //$arrayValorTotalSalario[] = $VALOR;
                                        $remover = array(".", "-", "/", ",");
                                        $VALOR = str_replace($remover, "", $VALOR);
                                        $VALOR = sprintf("%013d", $VALOR);
                                        $statusContaSalario = 'salario';
                                        include "BANCOS/SANTANDER/detalhes_santander_salario.php";
                                    }
                                } else if ($row_banco['id_nacional'] == '341') {

                                    $tipoContaCorrente01 = $rowTipoConta['tipo_conta'];
                                    if (($tipoContaCorrente01 == 'corrente') and ( $row_banco['id_nacional'] == '341')) {
                                        //VALOR TOTAL DO ARQUIVO TXT PARA CONTAS CORRENTE  
                                        $VALOR = $row_clt['salliquido'];
                                        //$arrayValorTotalCorrente[] = $VALOR;
                                        $remover = array(".", "-", "/", ",");
                                        $VALOR = str_replace($remover, "", $VALOR);
                                        $VALOR = sprintf("%013d", $VALOR);

                                        $statusContaCorrente = 'corrente';

                                        include "BANCOS/ITAU/detalhes_itau_corrente.php";
                                    } else if (($tipoContaCorrente01 == 'salario') and ( $row_banco['id_nacional'] == '341')) {
                                        //VALOR TOTAL DO ARQUIVO TXT PARA CONTAS SALÁRIO	  
                                        $VALOR = $row_clt['salliquido'];
                                        //	$arrayValorTotalSalario[] = $VALOR;
                                        $remover = array(".", "-", "/", ",");
                                        $VALOR = str_replace($remover, "", $VALOR);
                                        $VALOR = sprintf("%013d", $VALOR);
                                        $statusContaSalario = 'salario';
                                        include "BANCOS/ITAU/detalhes_itau_salario.php";
                                    }
                                    if (($tipoContaCorrente01 == 'corrente') and ( $row_banco['id_nacional'] == '001')) {
                                        $statusContaCorrente = 'corrente';
                                        include "BANCOS/BRASIL/detalhes_brasil_corrente.php";
                                    } else if (($tipoContaCorrente01 == 'salario') and ( $row_banco['id_nacional'] == '001')) {
                                        $statusContaSalario = 'salario';
                                        include "BANCOS/BRASIL/detalhes_brasil_salario.php";
                                    }
                                } else if ($row_banco['id_nacional'] == '399') {
                                    $tipoContaCorrente01 = $rowTipoConta['tipo_conta'];
                                    if ($tipoContaCorrente01 == 'corrente') {
                                        $NUM_ARQUIVO = $NUM_AR0QUIVO01;
                                        $statusContaCorrente = 'corrente';
                                        include "BANCOS/HSBC/detalhes_hsbc_corrente.php";
                                    } else if ($tipoContaCorrente01 == 'salario') {
                                        $NUM_ARQUIVO = $NUM_ARQUIVO02;
                                        $statusContaSalario = 'salario';
                                        include "BANCOS/HSBC/detalhes_hsbc_salario.php";
                                    }
                                }

                                unset($tipoContaCorrente01);
                                // AQUI TERMINA O LAÇO ONDE MOSTRA E CALCULA OS VALORES REFERENTES A UM ÚNICO FUNCIONARIO		  
                                // SOMANDO VARIAVIES PARA CHEGAR AO VALOR FINAL
                                $salario_brutoFinal = $salario_brutoFinal + $row_clt['salbase'];
                                $total_rendiFinal = $total_rendiFinal + $row_clt['rend'];
                                $total_debitoFinal = $total_debitoFinal + $row_clt['desco'];
                                $valor_inssFinal = $valor_inssFinal + $row_clt['a5020'];
                                $valor_IRFinal = $valor_IRFinal + $row_clt['a5021'];
                                $valor_familiaFinal = $valor_familiaFinal + $row_clt['a5022'];
                                $valor_liquiFinal = $valor_liquiFinal + $row_clt['salliquido'];

                                $cont ++;
                            }
                        }

                        // FORMATANDO OS DADOS FINAIS - FORMATO BRASILEIRO PARA VISUALIZAÇÃO (5.100,00)
                        $salario_brutoFinalF = number_format($salario_brutoFinal, 2, ",", ".");
                        $total_rendiFinalF = number_format($total_rendiFinal, 2, ",", ".");
                        $total_debitoFinalF = number_format($total_debitoFinal, 2, ",", ".");
                        $valor_inssFinalF = number_format($valor_inssFinal, 2, ",", ".");
                        $valor_IRFinalF = number_format($valor_IRFinal, 2, ",", ".");
                        $valor_familiaFinalF = number_format($valor_familiaFinal, 2, ",", ".");
                        $valor_liquiFinalF = number_format($valor_liquiFinal, 2, ",", ".");
                        ?>
                        <tr>
                            <td height="20" align="center" valign="middle" >&nbsp;</td>
                            <td height="20" align="center" valign="middle" >&nbsp;</td>
                            <td height="20" align="center" valign="middle" >&nbsp;</td>
                            <td height="20" align="right" valign="bottom" colspan="4" >TOTAIS:</td>
                            <td align="right" valign="bottom" ><?= $salario_brutoFinalF ?></td>
                            <td align="right" valign="bottom" ><?= $total_rendiFinalF ?></td>
                            <td align="right" valign="bottom" ><?= $total_debitoFinalF ?></td>
                            <td align="right" valign="bottom" ><?= $valor_inssFinalF ?></td>
                            <td align="right" valign="bottom" ><?= $valor_IRFinalF ?></td>
                            <td align="right" valign="bottom" ><?= $valor_familiaFinalF ?></td>
                            <td align="right" valign="bottom" ><?= $valor_liquiFinalF ?></td>
                            <td align="right" valign="bottom" >&nbsp;</td>
                        </tr>        
                    </table>
                    <br>
                    <br>
                    <?=$cont." Participantes<br/>"?>
                    
                    <?php
                    //ENCRIPTOGRAFANDO A VARIAVEL
                    $linkvolt = encrypt("$regiao&$folha"); 
                    $linkvolt = str_replace("+","--",$linkvolt);

                    $linkselect = encrypt("$regiao&$folha&$banco_participante&$dataPagamento");
                    $linkselect = str_replace("+","--",$linkselect);

                    //
                    ?>
                </td>
            </tr>
        </table>
        <?php
        if (($statusContaCorrente =='corrente') AND ($row_banco['id_nacional'] == '237')){
            $NUM_ARQUIVO = $NUM_ARQUIVO01;
            include "BANCOS/BRADESCO/trailler_bradesco_corrente.php";
        }
        if(($statusContaSalario =='salario') AND ($row_banco['id_nacional'] == '237')){
            $NUM_ARQUIVO = $NUM_ARQUIVO02;
            include "BANCOS/BRADESCO/trailler_bradesco_salario.php";
        }
        if (($statusContaSalario =='salario') AND ($row_banco['id_nacional'] == '356')){
            $valor_liquiFinalF = array_sum($arrayValorTotalSalario);
            $VALOR_TOTAL = $valor_liquiFinalF;
            $remover = array(".", "-", "/",",");
            $VALOR_TOTAL = str_replace($remover, "", $VALOR_TOTAL);
            $VALOR_TOTAL = sprintf("%013d",$VALOR_TOTAL);		
            include "BANCOS/REAL/trailler_lote_real_salario.php";	
            include "BANCOS/REAL/trailler_arquivo_real_salario.php";
            
        }
        if (($statusContaCorrente =='corrente') AND ($row_banco['id_nacional'] == '356')){
            $valor_liquiFinalF = array_sum($arrayValorTotalCorrente);
            $VALOR_TOTAL = $valor_liquiFinalF;
            $remover = array(".", "-", "/",",");
            $VALOR_TOTAL = str_replace($remover, "", $VALOR_TOTAL);
            $VALOR_TOTAL = sprintf("%013d",$VALOR_TOTAL);		
            include "BANCOS/REAL/trailler_lote_real_corrente.php";	
            include "BANCOS/REAL/trailler_arquivo_real_corrente.php";
        }
        if (($statusContaSalario =='salario') AND ($row_banco['id_nacional'] == '033')){
            $valor_liquiFinalF = array_sum($arrayValorTotalSalario);
            $VALOR_TOTAL = $valor_liquiFinalF;
            $remover = array(".", "-", "/",",");
            $VALOR_TOTAL = str_replace($remover, "", $VALOR_TOTAL);
            $VALOR_TOTAL = sprintf("%013d",$VALOR_TOTAL);		
            include "BANCOS/SANTANDER/trailler_lote_santander_salario.php";	
            include "BANCOS/SANTANDER/trailler_arquivo_santander_salario.php";
        }
        if (($statusContaCorrente =='corrente') AND ($row_banco['id_nacional'] == '033')){
            $valor_liquiFinalF = array_sum($arrayValorTotalCorrente);
            $VALOR_TOTAL = $valor_liquiFinalF;
            $remover = array(".", "-", "/",",");
            $VALOR_TOTAL = str_replace($remover, "", $VALOR_TOTAL);
            $VALOR_TOTAL = sprintf("%013d",$VALOR_TOTAL);		
            include "BANCOS/SANTANDER/trailler_lote_santander_corrente.php";	
            include "BANCOS/SANTANDER/trailler_arquivo_santander_corrente.php";
	}else if($row_banco['id_nacional'] == '341'){
            if($statusContaSalario != ''){	
                include "BANCOS/ITAU/trailler_itau_salario.php";
            }
            if($statusContaCorrente != ''){
                include "BANCOS/ITAU/trailler_itau_corrente.php";
            }
        }
        if($row_banco['id_nacional'] == '001'){
            if($statusContaSalario != ''){	
                include "BANCOS/BRASIL/trailler_brasil_salario.php";
            }
            if($statusContaCorrente != ''){
                include "BANCOS/BRASIL/trailler_brasil_corrente.php";
            }
        } 
        if($row_banco['id_nacional'] == '399'){
            if($statusContaSalario != ''){	
                include "BANCOS/HSBC/trailler_hsbc_salario.php";
            }
            if($statusContaCorrente != ''){
                include "BANCOS/HSBC/trailler_hsbc_corrente.php";
            }
        } 
        if ($row_banco['id_nacional'] == '341'){
            //$arquivo = 'BANCOS/ITAU/'.$CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
            }else if($row_banco['id_nacional'] == '356'){
                $arquivo = 'BANCOS/REAL/'.$CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";	
            }else if($row_banco['id_nacional'] == '033'){
                $arquivo = 'BANCOS/SANTANDER/'.$CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";	
            }else if($row_banco['id_nacional'] == '237'){
                $arquivo = 'BANCOS/BRADESCO/'.$CONSTANTE."_".$DD."_".$MM."_".$NUM_ARQUIVO."_".$TIPO.".txt";	
            }else if($row_banco['id_nacional'] == '001'){
                $arquivo = 'BANCOS/BRASIL/'.$CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";	
            }else if($row_banco['id_nacional'] == '399'){
                $arquivo = 'BANCOS/HSBC/'.$CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";	
            }
            
        
            $id_projeto = $row_projeto[0];	
            $id_banco = $row_banco[0];
            //$id_user;
            $nome = 'teste FOLHA DE PAGAMENTO';
            $especifica = 'teste FOLHA DE PAGAMENTO - CREDITO EM '.$DD."-".$MM."-".$ANO;
            $tipo = '30';
            //$valorTotalLiquido = number_format($valor_liquiFinalF, 2, ".",",");
	
            $valor = str_replace(".", "", $valor_liquiFinalF); 
            $valor = str_replace(",", ".", $valor); 
	
            $data_proc = date('Y-m-d H:i:s');
            $data_vencimento = $ANO."-".$MM."-".$DD;
            $status = '1';
            $linkfin = encrypt("$regiao&$folha&$row_banco[0]&$id_projeto&$id_user&$nome&$especifica&$tipo&$valor&$data_proc&$data_vencimento&$status");
            $linkfin = str_replace("+","--",$linkfin);	
            ?>
            
            <table width="95%" border="0" align="center">
                <tbody align="center" valign="middle" bgcolor="#999999">
                    <tr>
                        <td>
                            <a href='#' style="text-decoration:none; color:#000; font-size: 12px; font-weight: bold" onClick="Confirm(<?=$regiao?>,<?=$folha?>)" class="bota_o1">FINALIZAR ARQUIVO TXT</a>
                            <?php if(($_COOKIE['logado'] == 257 || $_COOKIE['logado'] == 259) && $verifica_finan == 0){ ?><a href='javascript:void(0);' style="text-decoration:none; color:#000; font-size: 12px; font-weight: bold" name="contabil_Lancamento" id="contabil_Lancamento" class="bota_o1"
                               data-projeto='<?= $_REQUEST['projeto_id'] ?>' 
                               data-referente='<?= $_REQUEST['mesdereferencia'] ?>'
                               data-nomeprojeto='<?= $row_empresa['razao'] ?>'
                               data-folha_id ='<?= $folha ?>'
                               data-banco='<?= $_REQUEST['banco']?>'
                               data-vencimento='<?= $_REQUEST['data'] ?>'
                            > GERAR SAÍDA FINANCEIRO</a> <?php } ?>
                            <a href='ver_folha.php?<?="enc=".$linkvolt."&tela=1"?>' style="text-decoration:none; color:#000; font-size: 12px; font-weight: bold" class="bota_o1">VOLTAR</a>
                            <a href='folha_banco_a.php?<?="enc=".$linkselect."&sel=1"?>' style="text-decoration:none; color:#000; font-size: 12px; font-weight: bold;" class="bota_o1">SELECIONAR FUNCIONÁRIO À PAGAR</a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br/>
            <script type="text/javascript">
//            <script language="javascript">

                function Confirm(a,b){
                    var Regiao = a;
                    var Folha = b;	
            
                    input_box=confirm("Deseja realmente FINALIZAR?\n\nLembrando que após a confirmação, não podera gerar novamente o ARQUIVO TEXTO do banco!");
            
                    if (input_box==true){ 
                        // Output when OK is clicked
                        // alert (\"You clicked OK\"); 
                        location.href="finalizando.php?enc=<?php $linkfin ?>";
                    } else {
                    // Output when Cancel is clicked
                    // alert (\"You clicked cancel\");
                    }

                }
            </script>
            <table bgcolor="#FFFFFF" width="60%" align="center">
                <tr>
                    <td align="center" valign="middle" bgcolor="#666666"><span class="igreja"> <strong style="color:#FFF">DOWNLOAD DO ARQUIVO TEXTO</strong> </span></td>
                </tr>
                <tr>
                    <td align='center'>&nbsp;</td>
                <tr>
                    <td></td>
                </tr>
                <?php
                if($statusContaSalario != ''){ //ESSA LINHA É EXECUTADA CASO A EXISTA "CONTA SALÁRIO" NO FACHAMENTO DA FOLHA
		
                    if ($row_banco['id_nacional'] == '341'){ //ANALIZA QUAL QUAL BANCO SERÁ ENVIADO A FOLHA QUE ESTÁ SENDO GERADA
                        $nomeArquivo = $CONSTANTE."_"."SALARIO"."_".$DD."_".$MM."_".$ANO.".txt";
                        $dirContaSalario = "BANCOS/ITAU/CONTA_SALARIO";
                        $arquivo = 'BANCOS/ITAU/CONTA_SALARIO/'.$nomeArquivo;
                        print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		
                    } else if ($row_banco['id_nacional'] == '356'){
                        $nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
                        $dirContaSalario = "BANCOS/REAL/CONTA_SALARIO";
                        $arquivo = 'BANCOS/REAL/CONTA_SALARIO/'.$nomeArquivo;
                        print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";	
		
                    } else if ($row_banco['id_nacional'] == '033'){
                        $nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
                        $dirContaSalario = "BANCOS/SANTANDER/CONTA_SALARIO";
                        $arquivo = 'BANCOS/SANTANDER/CONTA_SALARIO/'.$nomeArquivo;
                        print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";			
	
                    } else if ($row_banco['id_nacional'] == '237'){ //ANALIZA QUAL QUAL BANCO SERÁ ENVIADO A FOLHA QUE ESTÁ SENDO GERADA
                        $nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$NUM_ARQUIVO02."_".$TIPO.".txt";
                        $dirContaSalario = "BANCOS/BRADESCO/CONTA_SALARIO";
                        $arquivo = 'BANCOS/BRADESCO/CONTA_SALARIO/'.$nomeArquivo;
                        print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		
                    } else if ($row_banco['id_nacional'] == '001'){
                        $nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
                        $dirContaSalario = "BANCOS/BRASIL/CONTA_SALARIO";
                        $arquivo = 'BANCOS/BRASIL/CONTA_SALARIO/'.$nomeArquivo;
                        print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
                    } else if ($row_banco['id_nacional'] == '399'){
                        $nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
                        $dirContaSalario = "BANCOS/HSBC/CONTA_SALARIO";
                        $arquivo = 'BANCOS/HSBC/CONTA_SALARIO/'.$nomeArquivo;
                        print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
                    }
                } ?>
                <?php
                if($statusContaCorrente != ''){ //ESSA LINHA É EXECUTADA CASO A EXISTA "CONTA CORRENTE" NO FACHAMENTO DA FOLHA
		
                    if ($row_banco['id_nacional'] == '341'){ //ANALIZA QUAL QUAL BANCO SERÁ ENVIADO A FOLHA QUE ESTÁ SENDO GERADA
                        $nomeArquivo = $CONSTANTE."_"."CORRENTE"."_".$DD."_".$MM."_".$ANO.".txt";
                        $dirContaSalario = "BANCOS/ITAU/CONTA_CORRENTE";
                        $arquivo = 'BANCOS/ITAU/CONTA_CORRENTE/'.$nomeArquivo;
                        print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		
                    }else if ($row_banco['id_nacional'] == '356'){
                        $nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
                        $dirContaSalario = "BANCOS/REAL/CONTA_SALARIO";
                        $arquivo = 'BANCOS/REAL/CONTA_CORRENTE/'.$nomeArquivo;
                        print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		
                    }else if ($row_banco['id_nacional'] == '033'){
                        $nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
                        $dirContaSalario = "BANCOS/SANTANDER/CONTA_SALARIO";
                        $arquivo = 'BANCOS/SANTANDER/CONTA_CORRENTE/'.$nomeArquivo;
                        print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		
                
                    }else if ($row_banco['id_nacional'] == '237'){ //ANALIZA QUAL QUAL BANCO SERÁ ENVIADO A FOLHA QUE ESTÁ SENDO GERADA
                        $nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$NUM_ARQUIVO01."_".$TIPO.".txt";
                        $dirContaSalario = "BANCOS/BRADESCO/CONTA_CORRENTE";
                        $arquivo = 'BANCOS/BRADESCO/CONTA_CORRENTE/'.$nomeArquivo;
                        print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		
                    }else if ($row_banco['id_nacional'] == '001'){
                        $nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
                        $dirContaSalario = "BANCOS/BRASIL/CONTA_CORRENTE";
                        $arquivo = 'BANCOS/BRASIL/CONTA_CORRENTE/'.$nomeArquivo;
                        print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
                        
                    }else if ($row_banco['id_nacional'] == '399'){
                        $nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
                        $dirContaSalario = "BANCOS/HSBC/CONTA_CORRENTE";
                        $arquivo = 'BANCOS/HSBC/CONTA_CORRENTE/'.$nomeArquivo;
                        print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
                    }
                } ?>	
            </table>

            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style="background-color: #FFF; margin-top: 20px; font-size: 10px" class="tbRelatorio">
                <tr style="background-color: #cccccc; text-align: center; font-size: 12px; font-weight: bold; height: 50px;">
                    <td colspan="15">CLT's COM SALÁRIO LÍQUIDO ZERADO OU NEGATIVO</td>
                </tr>
                <tr style="text-align: left;">
                  <td height="25" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">C&oacute;digo</td>
                  <td   valign="middle" bgcolor="#CCCCCC" class="style23">Nome </td>
                  <td   valign="middle" bgcolor="#CCCCCC" class="style23">CPF</td>
                  <td   valign="middle" bgcolor="#CCCCCC" class="style23">Agência</td>
                  <td   valign="middle" bgcolor="#CCCCCC" class="style23">Agência DV</td>
                  <td   valign="middle" bgcolor="#CCCCCC" class="style23">Conta</td>
                  <td   valign="middle" bgcolor="#CCCCCC" class="style23">Conta DV</td>
                  <td   valign="middle" bgcolor="#CCCCCC" class="style23">Sal. Base</td>
                  <td   valign="middle" bgcolor="#CCCCCC" class="style23">Rendim.</td>
                  <td   valign="middle" bgcolor="#CCCCCC" class="style23">Descontos </td>
                  <td   valign="middle" bgcolor="#CCCCCC" class="style23">INSS</td>
                  <td   valign="middle" bgcolor="#CCCCCC" class="style23">Imp. Renda</td>
                  <td   valign="middle" bgcolor="#CCCCCC" class="style23">Sal. Fam. </td>
                  <td   valign="middle" bgcolor="#CCCCCC" class="style23">Sal. L&iacute;q.</td>
                  <td   valign="middle" bgcolor="#CCCCCC" class="style23">Tipo Conta</td>          
                </tr>

                <?php
                $qr_folha_proc = mysql_query("SELECT A.*,B.tipo_conta, B.agencia_dv, B.conta_dv 
                                    FROM rh_folha_proc as A
                                    INNER JOIN rh_clt as B
                                    ON A.id_clt = B.id_clt
                                    WHERE A.id_folha = $folha AND A.status = 3 AND A.salliquido <= '0.01' ORDER BY nome");
                while($row_proc = mysql_fetch_assoc($qr_folha_proc)){
            
                    switch($row_proc['tipo_conta']) {
                        case 'salario': $tipo_conta = 'Conta Salário';
                        break;
                        case 'corrente': $tipo_conta = 'Conta Corrente';
                        break;
                        default: $tipo_conta = '&nbsp;';
                    }
                    $cor = ($i++ %2 == 0)? 'novalinha corfundo_um':'novalinha corfundo_dois';
            
                    //totalizadores
                    $total_rendimento   += $row_proc['rend'];
                    $total_desconto     += $row_proc['desco'];
                    $total_a5020        += $row_proc['a5020'];
                    $total_a5021        += $row_proc['a5021'];
                    $total_a5022        += $row_proc['a5022'];
                    $total_salliquido   += $row_proc['salliquido'];
                    ?>    
                    <tr class="<?php echo $cor;?>" >
                        <td><?php echo $row_proc['id_clt']?>
                            <input type="hidden" name="clts[]" class="clts" value="<?php echo $row_proc['id_clt']?>">
                        </td>
                        <td><?php echo $row_proc['nome']?></td>
                        <td><?php echo $row_proc['cpf']?></td>
                        <td><?php echo $row_proc['agencia']?></td>
                        <td><?php echo $row_proc['agencia_dv']?></td>
                        <td><?php echo $row_proc['conta']?></td>
                        <td><?php echo $row_proc['conta_dv']?></td>
                        <td><?php echo number_format($row_proc['salbase'],2,',','.')?></td>
                        <td><?php echo number_format($row_proc['rend'],2,',','.')?></td>
                        <td><?php echo number_format($row_proc['desco'],2,',','.')?></td>
                        <td><?php echo number_format($row_proc['a5020'],2,',','.')?></td>
                        <td><?php echo number_format($row_proc['a5021'],2,',','.')?></td>
                        <td><?php echo number_format($row_proc['a5022'],2,',','.')?></td>
                        <td><?php echo number_format($row_proc['salliquido'],2,',','.')?></td>
                        <td><?php echo $tipo_conta;?></td>
                    </tr>   
            
                <?php } ?>
                <tr style="height: 40px; font-weight: bold;">
                    <td colspan="8" align="right">TOTAL:</td>
                    <td><?php echo number_format($total_rendimento,2,',','.');?></td>
                    <td><?php echo number_format($total_desco,2,',','.');?></td>
                    <td><?php echo number_format($total_a5020,2,',','.');?></td>
                    <td><?php echo number_format($total_a5021,2,',','.');?></td>
                    <td><?php echo number_format($total_a5022,2,',','.');?></td>
                    <td><?php echo number_format($total_salliquido,2,',','.');?></td>
                </tr>
        </table>
            
        <p>&nbsp;</p>
    </body>
    <script src="../../jquery-1.3.2.js"></script>
    <script>jQuery.noConflict();</script>


    <script src="../../js/jquery-1.10.2.min.js"></script>
    <script src="../../js/jquery-ui-1.11.2.custom/jquery-ui.min.js"></script>
    <script src="../../js/jquery.form.js"></script>
    <script src="../../resources/js/bootstrap.min.js"></script>
    <script src="../../resources/js/bootstrap-dialog.min.js"></script>
    <script src="../../resources/js/main.js"></script>
    <script src="../../js/jquery.maskMoney.js"></script>
    <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
    <script src="../../js/jquery.validationEngine-2.6.js"></script>
    <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
    
    <script type="text/javascript">
    
        $(document).ready(function(){
            $("#contabil_Lancamento").click(function() {
                cria_carregando_modal();
                var $this = $(this);
                var clts = new Object();
                var i = 0;
                $(".clts").each(function() {
                    clts[i] = $(this).val();
                    i++;
                });
                var banco = $this.data('banco');
                var projeto = $this.data('projeto');
                var folha_id = $this.data('folha_id');
                var lotenumero = $this.data('lote');
                var referente = $this.data('referente');
                var vencimento = $this.data('vencimento');
//                var clts = $this.data('clts');
                var totais = {
                    method:'salvar_saida', // para identificar qual o method
                    lotenumero:lotenumero,
                    projeto:projeto,
                    referente:referente,
                    folha_id:folha_id,
                    clts:clts,
                    banco:banco,
                    vencimento:vencimento
                };
                $.post('folha_banco.php',totais,function(dados){
                    if(dados.status == true){
                        bootAlert('Saída Gerada com Sucesso!', 'Alerta', function(){ location.reload(); }, 'success');
                    }else{
                        bootAlert('Erro ao gerar saída!', 'Alerta', null, 'danger');
                    }
                    remove_carregando_modal();
                },'json');

            });
            
        });
 
    </script>
</html>