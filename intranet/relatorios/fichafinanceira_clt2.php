<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
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
$projeto = mysql_fetch_assoc(mysql_query("SELECT id_projeto FROM rh_clt WHERE id_clt = $id_clt LIMIT 1"));
$id_projeto = $projeto[id_projeto];

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

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Ficha Financeira");
$breadcrumb_pages = array(
    "Lista Projetos" => "../ver2.php", 
    "Visualizar Projeto" => "../ver2.php?projeto={$id_projeto}", 
    "Lista Participantes" => "../bolsista2.php?projeto={$id_projeto}", 
    "Visualizar Participante" => "../rh/ver_clt2.php?pro={$id_projeto}&clt={$id_clt}");

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Ficha Financeira</title>
        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/add-ons.min.css" rel="stylesheet">
    </head>
    <body>
    <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Ficha Financeira</small></h2></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <form method="post" id="form1" class="form-horizontal">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="control-label col-md-1">Função: </label>
                                    <div class="col-md-11">
                                        <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'form-control')); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <input type="hidden" name="id_master" value="<?php echo $id_master; ?>"/>
                                <input type="hidden" name="id_clt" value="<?php echo $id_clt; ?>"/>
                                <input type="submit" name="gerar" class="btn btn-primary" value="Gerar" id="gerar"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-right">
                    <input type="button" class="btn btn-success" onclick="tableToExcel('fichaFinanceira', 'Ficha Financeira')" value="Exportar para Excel" class="exportarExcel">
                        <table id="fichaFinanceira" class="col-md-12 no-padding">
                        <tr>
                            <td>
                                <table class="table table-bordered tr-bg-active"> 
                                    <tr>
                                        <td class="right"><strong>COD.:</strong></td>
                                        <td colspan="5" class="left"><?php echo $d['id_clt']; ?></td>
                                    </tr>
                                    <tr>
                                        <td align="right"><strong>Nome:</strong></td>
                                        <td colspan="5" class="left"><?php echo $d['nome']; ?></td>
                                    </tr>
                                    <tr>
                                        <td width="17%" class="right"><strong>Data de Nascimento:</strong></td>
                                        <td width="27%"><?php echo $d['data_nasci']; ?></td>
                                        <td width="14%" class="right"><strong>Nacionalidade:</strong></td>
                                        <td width="14%"><?php echo $d['nacionalidade']; ?></td>
                                        <td width="14%" class="right"><strong>Naturalidade:</strong></td>
                                        <td width="14%"><?php echo $d['naturalidade']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="right"><strong>CPF:</strong></td>
                                        <td><?php echo $d['cpf']; ?></td>
                                        <td class="right"><strong>RG:</strong></td>
                                        <td><?php echo $d['rg']; ?></td>
                                        <td class="right"><strong>Título:</strong></td>
                                        <td><?php echo $d['titulo']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="right"><strong>CTPS:</strong></td>
                                        <td><?php echo $d['ctps']; ?></td>
                                        <td class="right"><strong>PIS/PASEP:</strong></td>
                                        <td colspan="3"><?php echo $d['pis']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="right"><strong>Função:</strong></td>
                                        <td><?php echo $d['nome_curso']; ?></td>
                                        <td class="right"><strong>Admissão:</strong></td>
                                        <td><?php echo $d['data_entrada']; ?></td>
                                        <td class="right"><strong>Afastamento:</strong></td>
                                        <td><?php echo $d['data_demis']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="right"><strong>Tipo de Pag.:</strong></td>
                                        <td><?php echo $d['tipo_conta']; ?></td>
                                        <td class="right"><strong>Salário:</strong></td>
                                        <td><?php echo $d['salario']; ?></td>
                                        <td class="right"><strong>Agência:</strong></td>
                                        <td><?php echo $d['agencia']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="right"><strong>Conta:</strong></td>
                                        <td><?php echo $d['nome_banco']; ?></td>
                                        <td class="right"><strong>Banco:</strong></td>
                                        <td colspan="3"><?php echo $d['conta']; ?></td>                          
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table cellspacing="0" cellpadding="0" class="" border="1" width="100%" id="result" >

                                    <!---------------------
                                    --CABEÇALHO DA TABELA--
                                    ---------------------->
                                    <tr>
                                        <th width="4%" class="center">COD</th>
                                        <th width="18%" class="center">NOME</th>
                                        <?php foreach ($cabecalho as $cab) { ?>
                                            <th width="6%" class="center"><?php echo $cab; ?></th>
                                        <?php } ?>
                                        <th width="6%" class="center">TOTAL</th>
                                    </tr>

                                    <!---------------------
                                    -MOVIMENTOS DE CREDITO-
                                    ---------------------->
                                    <?php foreach ($itensFicha as $k => $values) { ?>
                                        <?php if (in_array($k, $creditos)) { ?>
                                            <tr>
                                                <td class="center"><?php echo $k; ?></td>
                                                <td class="left"><?php echo $values["nome"]; ?></td>
                                                <?php foreach ($cabecalho as $key => $cab) { ?> 
                                                    <?php $total += $values[$key]; //SUBTOTAL POR MÊS ?>
                                                    <?php if (!empty($values[$key])) { ?> 
                                                        <td class="right"><?php echo number_format($values[$key], "2", ",", ".") ; ?></td>
                                                    <?php } else { ?>
                                                        <td class="center"> - </td>
                                                    <?php } ?>    
                                                    <?php $totalCredito[$key] += $values[$key]; ?>    
                                                <?php } ?>
                                                <?php if ($k == "0001") { ?>        
                                                    <td class="right"><?php echo number_format($total, "2", ",", "."); ?></td>    
                                                <?php } else { ?>
                                                    <td class="center"> - </td>    
                                                <?php } ?>    
                                            </tr>
                                        <?php } ?>                   
                                        <?php $total = 0; ?>
                                    <?php } ?>

                                    <!-------------------------
                                    --TOTAL DE MOV DE CREDITO--
                                    -------------------------->
                                    <tr class="tr-bg-active text-bold">
                                        <td colspan="2">Total de rendimentos</td>
                                        <?php foreach ($cabecalho as $key => $cab) { ?> 
                                            <?php if ($totalCredito[$key] != 0.00) { ?>
                                                <?php $totalGeralCred += $totalCredito[$key]; //TOTAL GERAL ?>
                                                <td><?php echo number_format($totalCredito[$key], "2", ",", "."); ?></td>
                                            <?php } else { ?>
                                                <td class="center"> - </td>
                                            <?php } ?> 
                                            <?php $creditoMenosDebito[$key] += $totalCredito[$key]; ?>    
                                        <?php } ?>
                                        <td align="center"><?php echo number_format($totalGeralCred, "2", ",", "."); ?></td>    
                                    </tr> 


                                    <!---------------------
                                    -MOVIMENTOS DE DEBITO--
                                    ---------------------->
                                    <?php foreach ($itensFicha as $k => $values) { ?>
                                        <?php if (in_array($k, $debitos) && $k != 5049) { ?>
                                            <tr>
                                                <td class="center"><?php echo $k; ?></td>
                                                <td style="text-align: left;"><?php echo $values["nome"]; ?></td>
                                                <?php foreach ($cabecalho as $key => $cab) { ?> 
                                                    <?php $total += $values[$key]; ?>
                                                    <?php if (!empty($values[$key])) { ?>
                                                       <td class="right"><?php echo number_format($values[$key], "2", ",", "."); ?></td>
                                                       <?php $totalDebito[$key] += $values[$key]; ?>    
                                                    <?php } else { ?>
                                                        <td class="center"> - </td>    
                                                    <?php } ?>    
                                                <?php } ?>
                                                <td  class="center"><?php echo " - "; ?></td>    
                                            </tr>
                                        <?php } ?>
                                        <?php $total = 0; ?>
                                    <?php } ?>

                                    <!-------------------------
                                    --TOTAL DE MOV DE DEBITO---
                                    -------------------------->
                                    <tr class="tr-bg-active text-bold">
                                        <td colspan="2">Total de descontos</td>
                                        <?php foreach ($cabecalho as $key => $cab) { ?> 
                                            <?php if ($totalDebito[$key] != 0.00) { ?>
                                                <?php $totalGeralDeb += $totalDebito[$key]; //TOTAL GERAL ?>
                                                <td class="right"><?php echo number_format($totalDebito[$key], "2", ",", "."); ?></td>
                                            <?php } else { ?>
                                                <td class="center"> - </td>    
                                            <?php } ?>
                                            <?php $creditoMenosDebito[$key] -= $totalDebito[$key]; ?>      
                                        <?php } ?>                          
                                        <td align="center"><?php echo number_format($totalGeralDeb, "2", ",", "."); ?></td>    
                                    </tr> 

                                    <!-------------------------
                                    --SOMA DE TODOS OS MOVIMENTOS---
                                    -------------------------->
                                    <tr class="tr-bg-active text-bold">
                                        <td colspan="2">Valor Líquido</td>
                                        <?php foreach ($cabecalho as $key => $cab) { ?> 
                                            <?php if ($creditoMenosDebito[$key] != 0.00) { ?>
                                                <?php $totalF += $creditoMenosDebito[$key]; //TOTAL GERAL ?>
                                                <td class="right"><?php echo number_format($creditoMenosDebito[$key], "2", ",", "."); ?></td>
                                            <?php } else { ?>
                                                <td class="center"> - </td>    
                                            <?php } ?>
                                        <?php } ?>
                                        <td align="center"><?php echo number_format($totalF, "2", ",", "."); ?></td>    
                                    </tr> 

                                    <!-----------------------------------
                                    -DDIR não é nem desconto e crédito--
                                    ------------------------------------>
                                    <tr>
                                        <td colspan="15" class="bg-default text-bold center">
                                            Movimento não tributário
                                        </td>
                                    </tr>
                                    <?php foreach ($itensFicha as $k => $values) { ?>
                                        <?php if($k == 5049){ ?>
                                            <tr>
                                                <td class="center"><?php echo $k; ?></td>
                                                <td style="text-align: left;"><?php echo $values["nome"]; ?></td>
                                                <?php foreach ($cabecalho as $key => $cab) { ?> 
                                                    <?php if (!empty($values[$key])) { ?>
                                                        <td class="right"><?php echo number_format($values[$key], "2", ",", "."); ?></td>
                                                    <?php } else { ?>
                                                        <td class="center"> - </td>    
                                                    <?php } ?>    
                                                <?php } ?>
                                                <td  class="center"><?php echo " - "; ?></td>    
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                </table>
                           </td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php include_once '../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
    </body>
</html>