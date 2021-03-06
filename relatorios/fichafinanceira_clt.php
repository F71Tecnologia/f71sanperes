<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include '../classes/FolhaClass.php';
include '../classes/RescisaoClass.php';
include '../classes/FeriasClass.php';
include('../classes/clt.php');
include('../classes/formato_data.php');
include("../wfunction.php");

function formata_numero($num) {
    if (strstr($num, '.') and !empty($num)) {
        return number_format($num, 2, ',', '.');
    } else {
        return $num;
    }
}

//OBJETO
$folha = new Folha();
$rescisao = new Rescisao();
$ferias = new Ferias();

//VARI�VEIS
$id_clt = $_REQUEST['id'];
$ano = $_REQUEST['ano'];
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$total = 0;
$totalF = 0;
$totalCredito = array();
$totalGeralCred = 0;
$totalDebito = array();
$totalGeralDeb = 0;

$creditoMenosDebito = array();

//DADOS DE FICHA FINANCEIRA POR CLT
$dados = $folha->getDadosClt($id_clt, 12, $ano);
$d = mysql_fetch_assoc($dados);

//CARREGA DADOS DO USU�RIO (NESSE CASO, LOGO DA EMPRESA VINCULADA AO USU�RIO)
$usuario = carregaUsuario();

//Array de tipos de creditos
$creditos[] = " - ";
$mov_credito = $folha->getMovCredito();
while ($linha = mysql_fetch_assoc($mov_credito)) {
    $creditos[] = $linha["cod"];
}


//Array de tipos de debitos
$mov_debito = $folha->getMovDebito();
while ($linha = mysql_fetch_assoc($mov_debito)) {
    $debitos[] = $linha["cod"];
}

//ARRAY DE ANOS
for ($i = 2010; $i <= date('Y'); $i++) {
    $optAnos[$i] = $i;
}

//GERARA
if (isset($_REQUEST['gerar'])) {
    
    /**
     * SINESIO LUIZ 
     * QUERY COM TODAS AS FOLHAS
     * 12/01/2017
     */
    $qryFolhasDoAno = "SELECT id_folha FROM rh_folha AS A WHERE A.ano = '{$ano}' AND A.`status` = 3 AND A.terceiro = 2";
    $sqlFolhasDoAno = mysql_query($qryFolhasDoAno);
    $idsFolhas = array();
    while($rows = mysql_fetch_assoc($sqlFolhasDoAno)){
        $idsFolhas[] = $rows['id_folha'] ;
    }
    
    /**
     * SINESIO LUIZ 
     * QUERY COM FOLHAS DE 13
     * 12/01/2017
     */
    $qryFolhasDoAno13 = "SELECT id_folha FROM rh_folha AS A WHERE A.ano = '{$ano}' AND A.`status` = 3 AND A.terceiro = 1";
    $sqlFolhasDoAno13 = mysql_query($qryFolhasDoAno13);
    $idsFolhas13 = array();
    while($rows13 = mysql_fetch_assoc($sqlFolhasDoAno13)){
        $idsFolhas13[] = $rows13['id_folha'] ;
    }     
     
    $arrayPensoes = array();
    foreach ($idsFolhas as $keys => $valuesIdsFolhas){
       $qryPensao = "SELECT C.id_clt,B.mes, B.ano, A.valor_mov, A.cpf_favorecido FROM itens_pensao_para_contracheque AS A 
                        LEFT JOIN rh_folha AS B ON(A.id_folha = B.id_folha)
                        LEFT JOIN favorecido_pensao_assoc AS C ON(A.cpf_favorecido = REPLACE(REPLACE(C.cpf,'.',''),'-',''))
                    WHERE A.id_folha = '{$valuesIdsFolhas}' AND A.`status` = 1 AND C.cpf IS NOT NUll  AND C.id_clt = '{$id_clt}'";    
       $sqlPensao = mysql_query($qryPensao);
       while($rowsPensoes = mysql_fetch_assoc($sqlPensao)){
           $arrayPensoes[$rowsPensoes['ano']][$rowsPensoes['mes']] = $rowsPensoes['valor_mov'];  
       }
    }
    
    $arrayPensoes13 = array();
    foreach ($idsFolhas13 as $keys => $valuesIdsFolhas){
       $qryPensao = "SELECT C.id_clt,B.mes, B.ano, A.valor_mov, A.cpf_favorecido FROM itens_pensao_para_contracheque AS A 
                        LEFT JOIN rh_folha AS B ON(A.id_folha = B.id_folha)
                        LEFT JOIN favorecido_pensao_assoc AS C ON(A.cpf_favorecido = REPLACE(REPLACE(C.cpf,'.',''),'-',''))
                    WHERE A.id_folha = '{$valuesIdsFolhas}' AND A.`status` = 1 AND C.cpf IS NOT NUll  AND C.id_clt = '{$id_clt}'";    
       $sqlPensao = mysql_query($qryPensao);
       while($rowsPensoes = mysql_fetch_assoc($sqlPensao)){
           $arrayPensoes13[$rowsPensoes['ano']][$rowsPensoes['mes']] = $rowsPensoes['valor_mov'];  
       }
    }
    
    //print_r($arrayPensoes13);
     

    //DADOS PESSOAIS
    $cabecalho = $folha->getCabecalho();
    //MONTA MATRIZ
    $ficha = $folha->getFichaFinanceiraIabas($id_clt, $ano);
    //ITENS FICHA
    $itensFicha = $folha->getDadosFicha();
    
    /**
     * TRAZENDO DADOS DA RESCISAO
     * 
     */
    $flagFichaParaRescisao = 1;
    $flagRegiao = $d['id_regiao'];
    $flagId_clt = $d['id_clt'];
    
    $dadosRescisaoNova = $rescisao->getRescisaoByClt($d['id_clt']);
    
    $itensRescisaoNova = array();
    while($rowsRescisaoNova = mysql_fetch_assoc($dadosRescisaoNova)){
        $itensRescisaoNova = $rowsRescisaoNova;
    }
    
    $flagIdRescisao = $itensRescisaoNova['id_recisao']; 
    
    /**
     * RESCISAO COMPLEMENTAR
     */
    $dadosRescisaoNovaComplementar = $rescisao->getRescisaoComplementarByClt($d['id_clt']);
    
    $itensRescisaoComplementarNova = array();
    while($rowsRescisaoComplementarNova = mysql_fetch_assoc($dadosRescisaoNovaComplementar)){
        $itensRescisaoComplementarNova = $rowsRescisaoComplementarNova;
    }
    
    $flagIdRescisaoComplementar = $itensRescisaoComplementarNova['id_recisao']; 
    
    /**
     * CRIANDO UM INDICE
     * NO MES DA RESCISAO
     */
    $dataDemi = explode("-",$itensRescisaoNova['data_demi']);
    $mesDemi  = str_pad($dataDemi[1],2, "0", STR_PAD_LEFT);
    
    if(!empty($flagIdRescisao)){
        $dadosRescisao = $rescisao->listaItensRescisaoById($flagIdRescisao);
        if(isset($mesDemi) && !empty($mesDemi)){
            $arrayDadosRescisao[$mesDemi] = $dadosRescisao;
        }
    }
    
    if(!empty($flagIdRescisaoComplementar)){
        $dadosRescisaoComplementar = $rescisao->listaItensRescisaoById($flagIdRescisaoComplementar);
        if(isset($mesDemi) && !empty($mesDemi)){
            $arrayDadosRescisaoComplementar[$mesDemi] = $dadosRescisaoComplementar;
        }
    }
    
//    echo "<pre>";
//        print_r($itensFicha);
//        echo "<br><br>"; 
//    echo "</pre>";
//    exit();
     
    /**
     * TRAZENDO DADOS DE FERIAS
     */
    $dadosFeriasNova = $ferias->montaArrayFeriasByClt($d['id_clt'], $ano);
     
}
?>
<html>
    <head>
        <title>Ficha Financeira</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../net1.css" rel="stylesheet" type="text/css">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css">
        <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript" ></script>
        <script src="../js/printElement.js" type="text/javascript" ></script>
        <script>
            $(function() {
                $(".det_rend_rescisao").click(function(){
                    thickBoxModal("Detalhes de Rendimentos na Rescis�o", "#dados_rend_rescisao", 400, 600);
                });
                
                $(".imprimirDetalhe").click(function(){
                    var conteudo = document.getElementById('dados_rend_rescisao').innerHTML,
                    tela_impressao = window.open('about:blank');
                    tela_impressao.document.write(conteudo);
                    tela_impressao.window.print();
                    tela_impressao.window.close();
                });
            });
        </script>
        <style type="text/css">
            table{
                border: 1px solid #E2E2E2;
            }

            #result th{
                background: #EEEEEE; 
                color: #000;
                border: 1px solid #E2E2E2;
                font-weight: bold;
                padding: 3px;
            }
            #result tr{
                padding: 5px; 
                font-size: 11px; 
                height: 25px; 
                padding: 1px;
                border: 1px solid #EEEEEE;
                text-align: right;
            }
            #result td{
                padding: 5px;
                box-sizing: border-box;
                border: 1px solid #EEEEEE;
                text-align: right;
            }
            .destaque{
                background: #e1e1e1;
                font-weight: bold;
                text-align: right;
            }
            .det_rend_rescisao{
                text-decoration: none;
                color:#666;
                background: url('../imagens/icones/icon-view.gif') no-repeat;
                padding-left: 20px;
                padding-top: 4px;
            }
            .imprimirDetalhe{
                text-decoration: none;
                color:#666;
                background: url('../imagens/icones/icon-print.gif') no-repeat;
                padding-left: 20px;
                padding-top: 2px;
                display: block;
                margin-top: 10px;
            }
            
            #dados_rend_rescisao{
                display: none;
            }
            
            @media print {
                #head, form { display: none; }
            }
            
        </style>
        
    </head>
    <body class="novaintra">       
        <div id="content">
            <div id="head">
                <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="height: 83; width: 163px; " width="140" height="100"/>
                <div class="fleft" style="width:86.3%">
                    <h2 style="margin-left: 80px;">FICHA FINANCEIRA CLT</h2>
                    <p></p>
                    
                </div>
            </div>
            <br class="clear">
            <br/>

            <form  name="form" action="" method="post" id="form">
                <fieldset>
                    <input type="hidden" name="id" value="<?php echo $_REQUEST['id'] ?>">
                    <legend>FICHA FINANCEIRA</legend>
                    <div class="fleft">
                        <p><label class="first" style='margin-top: 10px;'>Ano:</label> <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano', 'style' => 'padding: 4px; width: 200px; border: 1px solid #ccc;')); ?></p>
                    </div>
                    <br class="clear"/>
                    <p class="controls" style="margin-top: 10px;">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo j� existente!'; ?></span>
                        <input type="hidden" name="id_master" value="<?php echo $id_master; ?>"/>
                        <input type="hidden" name="id_clt" value="<?php echo $id_clt; ?>"/>
                        <!--<input type="submit" name="historico" value="Exibir hist�rico" id="historico"/>-->
                        <input type="submit" name="gerar" value="Gerar" id="gerar" style=" padding: 7px 35px; border: 1px solid #ccc;"/>
                        <br/>
                           <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('fichaFinanceira', 'Ficha Financeira')" value="Exportar para Excel" class="exportarExcel"></p>    
                        <br/>
                    </p>
                </fieldset>
            </form>
            
            <table id="fichaFinanceira" width="100%">
                <tr>
                    <td>
                        <table class="grid" border="1" cellspacing="0" cellpadding="0" width="100%" style=" border: 1px solid #ccc; background: #f1f1f1;"> 
                            <tr>
                                <td colspan="5" align="right"><strong>ANO:</strong></td>
                                <td><?=$anoSel?></td>
                            </tr>
                            <tr>
                                <td align="right"><strong>COD.:</strong></td>
                                <td colspan="5"><?php echo $d['id_clt']; ?></td>
                            </tr>
                            <tr>
                                <td align="right"><strong>Nome:</strong></td>
                                <td colspan="5"><?php echo $d['nome']; ?></td>
                            </tr>
                            <tr>
                                <td align="right"><strong>Data de Nascimento:</strong></td>
                                <td><?php echo $d['data_nasci']; ?></td>
                                <td align="right"><strong>Nacionalidade:</strong></td>
                                <td ><?php echo $d['nacionalidade']; ?></td>
                                <td align="right"><strong>Naturalidade:</strong></td>
                                <td><?php echo $d['naturalidade']; ?></td>
                            </tr>
                            <tr>
                                <td align="right"><strong>CPF:</strong></td>
                                <td><?php echo $d['cpf']; ?></td>
                                <td align="right"><strong>RG:</strong></td>
                                <td><?php echo $d['rg']; ?></td>
                                <td align="right"><strong>T�tulo:</strong></td>
                                <td><?php echo $d['titulo']; ?></td>
                            </tr>
                            <tr>
                                <td align="right"><strong>CTPS:</strong></td>
                                <td><?php echo $d['ctps']; ?></td>
                                <td align="right"><strong>PIS/PASEP:</strong></td>
                                <td colspan="3"><?php echo $d['pis']; ?></td>
                            </tr>
                            <tr>
                                <td align="right"><strong>Fun��o:</strong></td>
                                <td><?php echo $d['nome_curso']; ?></td>
                                <td align="right"><strong>Admiss�o:</strong></td>
                                <td><?php echo $d['data_entrada']; ?></td>
                                <td align="right"><strong>Afastamento:</strong></td>
                                <td><?php echo $d['data_demis']; ?></td>
                            </tr>
                            <tr>
                                <td align="right"><strong>Tipo de Pag.:</strong></td>
                                <td><?php echo $d['tipo_conta']; ?></td>
                                <td align="right"><strong>Sal�rio:</strong></td>
                                <td><?php echo $d['salario']; ?></td>
                                <td align="right"><strong>Ag�ncia:</strong></td>
                                <td><?php echo $d['agencia']; ?></td>
                            </tr>
                            <tr>
                                <td align="right"><strong>Conta:</strong></td>
                                <td><?php echo $d['conta']; ?></td>
                                <td align="right"><strong>Banco:</strong></td>
                                <td colspan="5"><?php echo $d['nome_banco']; ?></td>                          
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table cellspacing="0" cellpadding="0" class="" border="1" width="100%" id="result" >

                            <!---------------------
                            --CABE�ALHO DA TABELA--
                            ---------------------->
                            <tr>
                                <th align="center" style=" padding: 5px; font-size: 11px; text-align: center" >COD</th>
                                <th align="center" style="text-align: left;" >NOME</th>
                                <?php foreach ($cabecalho as $cab) { ?>
                                    <th align="center"><?php echo $cab; ?></th>
                                <?php } ?>
                                <th align="center">TOTAL</th>
                            </tr>

                            <!---------------------
                            -MOVIMENTOS DE CREDITO-
                            ---------------------->
                            <?php foreach ($itensFicha as $k => $values) { ?>

                                <?php if (in_array($k, $creditos)) { ?>
                                    <tr>
                                        <td style="text-align: center"><?php echo $k; ?></td>
                                        <td style="text-align: left"><?php echo $values["nome"]; ?></td>
                                        <?php foreach ($cabecalho as $key => $cab) { ?> 
                                            <?php if (!empty($values[$key])) { ?> 
                                                
                                                <?php if($values["nome"] == "RENDIMENTOS" || $values['nome'] == "F�RIAS NO M�S" ||
                                                        (($values["nome"] == "SALARIO PLANTONISTA" || 
                                                          $values["nome"] == "SALARIO HORISTA" || 
                                                          $values['nome'] == "SALARIO" 
                                                          )) && $key == $mesDemi){ ?>    
                                                    <td style="text-align: right;"><?php echo " ";//echo "R$ " . number_format($values[$key], "2", ",", ".") ; ?></td>
                                                <?php }else{ ?> 
                                                    <td style="text-align: right;"><?php echo "R$ " . number_format($values[$key], "2", ",", ".") ; ?></td>
                                                    <?php $totalCredito[$key] += $values[$key]; ?> 
                                                    <?php $total += $values[$key]; //SUBTOTAL POR M�S ?>
                                                <?php } ?> 
                                            
                                            <?php } else { ?>
                                                <td style="text-align: center;"> - </td>
                                            <?php } ?>    
                                               
                                        <?php } ?>
                                                
                                        <?php if ($k == "0001") { ?>        
                                            <td><?php echo "R$ " . number_format($total, "2", ",", "."); ?></td>    
                                        <?php } else { ?>
                                            <td style="text-align: center;"> - </td>    
                                        <?php } ?>   
                                        
                                    </tr>
                                <?php } ?>                   

                                <?php $total = 0; ?>
                            <?php } ?>
                                    
                                    
                            <?php if(!empty($dadosFeriasNova)){ ?>        
                                <!-------------------------
                                --RENDIMENTOS FERIAS----
                                -------19/01/2017---------
                                -------------------------->
<!--                                <tr class="destaque">
                                    <td colspan="15" style="text-align: left;">RESCIS�O RENDIMENTOS</td>
                                </tr>-->
                                <?php foreach ($dadosFeriasNova as $mes => $dadosArrayFerias){ ?>
                                    <?php foreach ($dadosArrayFerias as $campos => $itens){ ?>
                                        <?php if($itens['tipo'] == 'CREDITO' && $itens['valor'] > 0){ ?>
                                            <tr>
                                                <td colspan="1"><?php echo $campos; ?></td>
                                                <td colspan="1" style="text-align: left;"><?php echo $itens['movimento']; ?></td>
                                                    <?php foreach ($cabecalho as $key => $cab) { ?> 

                                                        <?php if($mes == $key){ ?>
                                                            <td style="text-align: right;"> <?php echo "R$ " . number_format($itens['valor'], "2", ",", "."); ?> </td>
                                                            <?php $totalCredito[$key] += $itens['valor']; ?>   
                                                        <?php }else{ ?>
                                                            <td style="text-align: center;"> - </td>
                                                        <?php } ?>

                                                    <?php } ?>   
                                                <td align="center"> - </td>    
                                            </tr>     
                                             
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                                            
                                            
                            <?php if(!empty($arrayDadosRescisao)){ ?>        
                                <!-------------------------
                                --RENDIMENTOS RESCISAO----
                                -------19/01/2017---------
                                -------------------------->
                                <tr class="destaque">
                                    <td colspan="15" style="text-align: left;">RESCIS�O RENDIMENTOS</td>
                                </tr>
                                <?php foreach ($arrayDadosRescisao as $mes => $dadosArrayResc){ ?>
                                    <?php foreach ($dadosArrayResc as $campos => $itens){ ?>
                                        <?php if($itens['tipo'] == 'CREDITO' && $itens['valor'] > 0){ ?>
                                            <tr>
                                                <td colspan="1"><?php echo $campos; ?></td>
                                                <td colspan="1" style="text-align: left;"><?php echo $itens['movimento']; ?></td>
                                                    <?php foreach ($cabecalho as $key => $cab) { ?> 

                                                        <?php if($mes == $key){ ?>
                                                            <td style="text-align: right;"> <?php echo "R$ " . number_format($itens['valor'], "2", ",", "."); ?> </td>
                                                            <?php $totalCredito[$key] += $itens['valor']; ?> 
                                                        <?php }else{ ?>
                                                            <td style="text-align: center;"> - </td>
                                                        <?php } ?>

                                                    <?php } ?>   
                                                <td align="center"> - </td>    
                                            </tr>     
                                               
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                                            
                            <?php if(!empty($arrayDadosRescisaoComplementar)){ ?>        
                                <!-------------------------
                                --RENDIMENTOS RESCISAO COMPLEMENTAR ----
                                -------08/02/2017---------
                                -------------------------->
                                <tr class="destaque">
                                    <td colspan="15" style="text-align: left;">RESCIS�O COMPLEMENTAR RENDIMENTOS</td>
                                </tr>
                                <?php foreach ($arrayDadosRescisaoComplementar as $mes => $dadosArrayResc){ ?>
                                    <?php foreach ($dadosArrayResc as $campos => $itens){ ?>
                                        <?php if($itens['tipo'] == 'CREDITO' && $itens['valor'] > 0){ ?>
                                            <tr>
                                                <td colspan="1"><?php echo $campos; ?></td>
                                                <td colspan="1" style="text-align: left;"><?php echo $itens['movimento']; ?></td>
                                                    <?php foreach ($cabecalho as $key => $cab) { ?> 

                                                        <?php if($mes == $key){ ?>
                                                            <td style="text-align: right;"> <?php echo "R$ " . number_format($itens['valor'], "2", ",", "."); ?> </td>
                                                            <?php $totalCredito[$key] += $itens['valor']; ?>    
                                                        <?php }else{ ?>
                                                            <td style="text-align: center;"> - </td>
                                                        <?php } ?>

                                                    <?php } ?>   
                                                <td align="center"> - </td>    
                                            </tr>     
                                            
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                            
                            <!-------------------------
                            --TOTAL DE MOV DE CREDITO--
                            -------------------------->
                            <tr class="destaque">
                                <td colspan="2" style="text-align: left">TOTAL RENDIMENTOS</td>
                                <?php foreach ($cabecalho as $key => $cab) { ?> 
                                    <?php if ($totalCredito[$key] != 0.00) { ?>
                                        <?php $totalGeralCred += $totalCredito[$key]; //TOTAL GERAL ?>
                                        <td><?php echo "R$ " . number_format($totalCredito[$key], "2", ",", "."); ?></td>
                                    <?php } else { ?>
                                        <td style="text-align: center;"> - </td>
                                    <?php } ?> 

                                    <?php $creditoMenosDebito[$key] += $totalCredito[$key]; ?>    
                                <?php } ?>
                                <td align="center"><?php echo "R$ " . number_format($totalGeralCred, "2", ",", "."); ?></td>    
                            </tr> 


                            <!---------------------
                            -MOVIMENTOS DE DEBITO--
                            ---------------------->
                            <?php foreach ($itensFicha as $k => $values) { ?>

                                <?php if (in_array($k, $debitos) && $k != 5049) { ?>
                                    <tr>
                                        <td style="text-align: center;"><?php echo $k; ?></td>
                                        <td style="text-align: left;"><?php echo $values["nome"]; ?></td>
                                        <?php foreach ($cabecalho as $key => $cab) { ?> 
                                        
                                            <?php if (!empty($values[$key])) { ?>
                                        
                                                <?php if($values["nome"] == "DESCONTO" || 
                                                            (($values["nome"] == "SALARIO PLANTONISTA" || $values["nome"] == "SALARIO HORISTA" || $values['nome'] == "SALARIO") 
                                                              && $key == $mesDemi)){ ?>    
                                                    <td style="text-align: right;"><?php echo " ";//echo "R$ " . number_format($values[$key], "2", ",", ".") ; ?></td>
                                                <?php }else{ ?> 
                                                    <td style="text-align: right;"><?php echo "R$ " . number_format($values[$key], "2", ",", ".") ; ?></td>
                                                    <?php $totalDebito[$key] += $values[$key]; ?> 
                                                    <?php $total += $values[$key]; ?>
                                                <?php } ?> 
                                            <?php } else { ?>
                                                <td style="text-align: center"> - </td>    
                                            <?php } ?>  
                                                
                                        <?php } ?>
                                        <td  style="text-align: center"><?php echo " - "; ?></td>    
                                    </tr>
                                <?php } ?>
                                <?php $total = 0; ?>
                            <?php } ?>
                                    
                            
                            <?php if(!empty($arrayDadosRescisao)){ ?>        
                                <!-------------------------
                                --DESCONTOS RESCISAO----
                                -------19/01/2017---------
                                -------------------------->
                                <tr class="destaque">
                                    <td colspan="15" style="text-align: left;">RESCIS�O DESCONTOS</td>
                                </tr>
                                <?php foreach ($arrayDadosRescisao as $mes => $dadosArrayResc){ ?>
                                    <?php foreach ($dadosArrayResc as $campos => $itens){ ?>                                        
                                        <?php if($itens['tipo'] == 'DEBITO' && $itens['valor'] > 0){ ?>
                                            <tr>
                                                <td colspan="1"><?php echo $campos; ?></td>
                                                <td colspan="1" style="text-align: left;"><?php echo $itens['movimento']; ?></td>
                                                    <?php foreach ($cabecalho as $key => $cab) { ?> 

                                                        <?php if($mes == $key){ ?>
                                                            <td style="text-align: right;"> <?php echo "R$ " . number_format($itens['valor'], "2", ",", "."); ?> </td>
                                                            <?php $totalDebito[$key] += $itens['valor']; ?>
                                                        <?php }else{ ?>
                                                            <td style="text-align: center;"> - </td>
                                                        <?php } ?>

                                                    <?php } ?>   
                                                <td align="center"> - </td>    
                                            </tr>     
                                                
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>        
                                            
                                            
                            <?php if(!empty($arrayDadosRescisaoComplementar)){ ?>        
                                <!-------------------------
                                --DESCONTOS RESCISAO COMPLEMENTAR----
                                -------19/01/2017---------
                                -------------------------->
                                <tr class="destaque">
                                    <td colspan="15" style="text-align: left;">RESCIS�O COMPLEMENTAR DESCONTOS</td>
                                </tr>
                                <?php foreach ($arrayDadosRescisaoComplementar as $mes => $dadosArrayResc){ ?>
                                    <?php foreach ($dadosArrayResc as $campos => $itens){ ?>
                                        <?php if($itens['tipo'] == 'DEBITO' && $itens['valor'] > 0){ ?>
                                            <tr>
                                                <td colspan="1"><?php echo $campos; ?></td>
                                                <td colspan="1" style="text-align: left;"><?php echo $itens['movimento']; ?></td>
                                                    <?php foreach ($cabecalho as $key => $cab) { ?> 

                                                        <?php if($mes == $key){ ?>
                                                            <td style="text-align: right;"> <?php echo "R$ " . number_format($itens['valor'], "2", ",", "."); ?> </td>
                                                            <?php $totalDebito[$key] += $itens['valor']; ?>    
                                                        <?php }else{ ?>
                                                            <td style="text-align: center;"> - </td>
                                                        <?php } ?>
                                                    <?php } ?>   
                                                <td align="center"> - </td>    
                                            </tr>     
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>        

                            <!-------------------------
                            --TOTAL DE MOV DE DEBITO---
                            -------------------------->
                            <tr class="destaque">
                                <td colspan="2" style="text-align: left;">TOTAL DESCONTOS</td>
                                <?php foreach ($cabecalho as $key => $cab) { ?> 
                                    <?php if ($totalDebito[$key] != 0.00) { ?>
                                        <?php $totalGeralDeb += $totalDebito[$key]; //TOTAL GERAL ?>
                                        <td style="text-align: right"><?php echo "R$ " . number_format($totalDebito[$key], "2", ",", "."); ?></td>
                                    <?php } else { ?>
                                        <td style="text-align: center"> - </td>    
                                    <?php } ?>
                                    <?php $creditoMenosDebito[$key] -= $totalDebito[$key]; ?>      
                                <?php } ?>                          
                                <td align="center"><?php echo "R$ " . number_format($totalGeralDeb, "2", ",", "."); ?></td>    
                            </tr> 
                            
                            <!-------------------------
                            --TOTAL DE PENSAO FOLHA NORMAL---
                            -------------------------->
                             
<!--                            <tr class="destaque">
                                <td colspan="2" style="text-align: left; ">PENS�O</td>
                                <?php foreach ($cabecalho as $key => $cab) { ?> 
                                    <?php if ($arrayPensoes[$ano][$key] != 0.00) { ?>
                                        <?php $totalGeralPensao += $arrayPensoes[$ano][$key]; //TOTAL GERAL ?>
                                        <td style="text-align: right"><?php echo "R$ " . number_format($arrayPensoes[$ano][$key], "2", ",", "."); ?></td>
                                    <?php } else { ?>
                                        <td style="text-align: center"> - </td>    
                                    <?php } ?>
                                    <?php $creditoMenosDebito[$key] -= $arrayPensoes[$ano][$key]; ?>      
                                <?php } ?>                          
                                <td align="center"><?php echo "R$ " . number_format($totalGeralPensao, "2", ",", "."); ?></td>    
                            </tr>  -->
                            
                            <!-------------------------
                            --TOTAL DE PENSAO FOLHA 13---
                            -------------------------->
                             
<!--                            <tr class="destaque">
                                <td colspan="2" style="text-align: left; ">PENS�O 13</td>
                                <?php foreach ($cabecalho as $key => $cab) { ?> 
                                    <?php if ($arrayPensoes13[$ano][$key] != 0.00) { ?>
                                        <?php $totalGeralPensao13 += $arrayPensoes13[$ano][$key]; //TOTAL GERAL ?>
                                        <td style="text-align: right"><?php echo "R$ " . number_format($arrayPensoes13[$ano][$key], "2", ",", "."); ?></td>
                                    <?php } else { ?>
                                        <td style="text-align: center"> - </td>    
                                    <?php } ?>
                                    <?php $creditoMenosDebito[$key] -= $arrayPensoes13[$ano][$key]; ?>      
                                <?php } ?>                          
                                <td align="center"><?php echo "R$ " . number_format($totalGeralPensao13, "2", ",", "."); ?></td>    
                            </tr> -->

                            <!-------------------------
                            --SOMA DE TODOS OS MOVIMENTOS---
                            -------------------------->
                            <tr class="destaque">
                                <td colspan="2">Valor L�quido</td>
                                <?php foreach ($cabecalho as $key => $cab) { ?> 
                                    <?php if ($creditoMenosDebito[$key] != 0.00) { ?>
                                        <?php $totalF += $creditoMenosDebito[$key]; //TOTAL GERAL ?>
                                        <td style="text-align: right"><?php echo "R$ " . number_format($creditoMenosDebito[$key], "2", ",", "."); ?></td>
                                    <?php } else { ?>
                                        <td style="text-align: center"> - </td>    
                                    <?php } ?>
                                <?php } ?>
                                <td align="center"><?php echo "R$ " . number_format($totalF, "2", ",", "."); ?></td>    
                            </tr> 

                            <!-----------------------------------
                            -DDIR n�o � nem desconto e cr�dito--
                            ------------------------------------>
                            <tr>
                                <td colspan="15" style="text-align: center; background: #ccc">
                                    Movimento n�o tribut�rio
                                </td>
                            </tr>
                            <?php foreach ($itensFicha as $k => $values) { ?>

                                <?php if($k == 5049){ ?>
                                    <tr>
                                        <td style="text-align: center;"><?php echo $k; ?></td>
                                        <td style="text-align: left;"><?php echo $values["nome"]; ?></td>
                                        <?php foreach ($cabecalho as $key => $cab) { ?> 
                                            <?php if (!empty($values[$key])) { ?>
                                                <td style="text-align: right"><?php echo "R$ " . number_format($values[$key], "2", ",", "."); ?></td>
                                            <?php } else { ?>
                                                <td style="text-align: center"> - </td>    
                                            <?php } ?>    
                                        <?php } ?>
                                        <td  style="text-align: center"><?php echo " - "; ?></td>    
                                    </tr>
                                <?php } ?>

                            <?php } ?>
                        </table>  
                   </td>
                </tr>
            </table>            
              
            <div class="clear"></div>
            <div id="dados_rend_rescisao" class="area_print">
                <?php foreach ($dadosRescisao as $key => $dados){  ?>
                    <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" >
                        <thead>
                            <tr><th colspan="2">Nome: <?php echo $dados['nome']; ?></th></tr>
                        </thead>
                        <tbody>
                            <?php foreach($dados['CREDITO'] as $nomes => $inf){  ?> 
                                <tr>
                                    <td><?php echo $inf['nome']; ?></td>
                                    <td><?php echo number_format($inf['valor'],2,',','.'); ?></td>
                                </tr>
                                <?php $totalValor += $inf['valor']; ?>
                            <?php } ?>
                        </tbody>
                        <tfoot style="background:#f2f2f2">
                            <tr>
                                <td>TOTAL</td>
                                <td><?php echo number_format($totalValor,2,',','.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php } ?>
                <a href="javascript:;" class="imprimirDetalhe">Imprimir</a>
            </div>
        </div>
    </body>
</html>