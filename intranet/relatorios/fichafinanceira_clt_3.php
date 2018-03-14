<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcion ario.php";
include '../classes_permissoes/regioes.class.php';
include '../classes/FolhaClass.php';
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

//VARIÁVEIS
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
$dados = $folha->getDadosClt($id_clt);
$d = mysql_fetch_assoc($dados);


//CARREGA DADOS DO USUÁRIO (NESSE CASO, LOGO DA EMPRESA VINCULADA AO USUÁRIO)
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
if (isset($_POST['gerar'])) {

    //DADOS PESSOAIS
    $cabecalho = $folha->getCabecalho();
    //MONTA MATRIZ
    $ficha = $folha->getFichaFinanceira($id_clt, $ano);
    //ITENS FICHA
    $itensFicha = $folha->getDadosFicha();
}
?>
<html>
    <head>
        <title>Ficha Financeira - Teste</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../net1.css" rel="stylesheet" type="text/css">
        <script src="../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <!--<script src="../jquery/jquery.tools.min.js" type="text/javascript" ></script>-->
        <!--<script src="../js/global.js" type="text/javascript" ></script>-->
        <script>
            $(function() {
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

        </style>
        <style media="print">
            fieldset{ display: none;}
            body{ background-color: #FFF;}
            tr{ padding: 20px; background: #333;}
        </style>
    </head>
    <body class="novaintra">       
        <div id="content">
            <div id="head">
                <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="height: 83; width: 163px; " width="140" height="100"/>
                <div class="fleft" style="width:86.3%">
                    <h2 style="margin-left: 80px;">FICHA FINANCEIRA CLT</h2>
                    <p></p>
                    <table class="grid" border="1" cellspacing="0" cellpadding="0" width="95%" style="margin-left:59px; border: 1px solid #ccc; background: #f1f1f1;"> 
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
                            <td align="right"><strong>Título:</strong></td>
                            <td><?php echo $d['titulo']; ?></td>
                        </tr>
                        <tr>
                            <td align="right"><strong>CTPS:</strong></td>
                            <td><?php echo $d['ctps']; ?></td>
                            <td align="right"><strong>PIS/PASEP:</strong></td>
                            <td colspan="3"><?php echo $d['pis']; ?></td>
                        </tr>
                        <tr>
                            <td align="right"><strong>Função:</strong></td>
                            <td><?php echo $d['nome_curso']; ?></td>
                            <td align="right"><strong>Admissão:</strong></td>
                            <td><?php echo $d['data_entrada']; ?></td>
                            <td align="right"><strong>Afastamento:</strong></td>
                            <td><?php echo $d['data_demis']; ?></td>
                        </tr>
                        <tr>
                            <td align="right"><strong>Tipo de Pag.:</strong></td>
                            <td><?php echo $d['tipo_conta']; ?></td>
                            <td align="right"><strong>Salário:</strong></td>
                            <td><?php echo $d['salario']; ?></td>
                            <td align="right"><strong>Agência:</strong></td>
                            <td><?php echo $d['agencia']; ?></td>
                        </tr>
                        <tr>
                            <td align="right"><strong>Conta:</strong></td>
                            <td><?php echo $d['nome_banco']; ?></td>
                            <td align="right"><strong>Banco:</strong></td>
                            <td colspan="5"><?php echo $d['conta']; ?></td>                          
                        </tr>

                    </table>
                </div>
            </div>
            <br class="clear">
            <br/>

            <form  name="form" action="" method="post" id="form">
                <fieldset>
                    <legend>FICHA FINANCEIRA</legend>
                    <div class="fleft">
                        <p><label class="first" style='margin-top: 10px;'>Ano:</label> <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano', 'style' => 'padding: 4px; width: 200px; border: 1px solid #ccc;')); ?></p>
                    </div>
                    <br class="clear"/>
                    <p class="controls" style="margin-top: 10px;">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <input type="hidden" name="id_master" value="<?php echo $id_master; ?>"/>
                        <input type="hidden" name="id_clt" value="<?php echo $id_clt; ?>"/>
                        <!--<input type="submit" name="historico" value="Exibir histórico" id="historico"/>-->
                        <input type="submit" name="gerar" value="Gerar" id="gerar" style=" padding: 7px 35px; border: 1px solid #ccc;"/>
                    </p>
                </fieldset>
            </form>

            <p></p> 
            <table cellspacing="0" cellpadding="0" class="" border="1" width="100%" id="result" >

                <!---------------------
                --CABEÇALHO DA TABELA--
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
                                <?php $total += $values[$key]; //SUBTOTAL POR MÊS ?>
                                <?php if (!empty($values[$key])) { ?> 
                                    <td style="text-align: right;"><?php echo "R$ " . $values[$key] ; ?></td>
                                <?php } else { ?>
                                    <td style="text-align: center;"> - </td>
                                <?php } ?>    
                                <?php $totalCredito[$key] += $values[$key]; ?>    
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

                <!-------------------------
                --TOTAL DE MOV DE CREDITO--
                -------------------------->
                <tr class="destaque">
                    <td colspan="2">Total de rendimentos</td>
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
                                <?php $total += $values[$key]; ?>
                                <?php if (!empty($values[$key])) { ?>
                                   <td style="text-align: right"><?php echo "R$ " . number_format($values[$key], "2", ",", "."); ?></td>
                                   <?php $totalDebito[$key] += $values[$key]; ?>    
                                <?php } else { ?>
                                    <td style="text-align: center"> - </td>    
                                <?php } ?>    
                            <?php } ?>
                            <td  style="text-align: center"><?php echo " - "; ?></td>    
                        </tr>
                    <?php } ?>
                    <?php $total = 0; ?>
                <?php } ?>

                <!-------------------------
                --TOTAL DE MOV DE DEBITO---
                -------------------------->
                <tr class="destaque">
                    <td colspan="2">Total de descontos</td>
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
                --SOMA DE TODOS OS MOVIMENTOS---
                -------------------------->
                <tr class="destaque">
                    <td colspan="2">Valor Líquido</td>
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
                -DDIR não é nem desconto e crédito--
                ------------------------------------>
                <tr>
                    <td colspan="15" style="text-align: center; background: #ccc">
                        Movimento não tributário
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
            <div class="clear"></div>
        </div>
    </body>
</html>