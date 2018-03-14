<?php

session_start();

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/NFeClass.php");
include("../../classes/NFSeClass.php");
include("../../classes/BancoClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);

// seta um valor para variável aba. usado para definir a aba aberta.
$aba = (isset($_REQUEST['aba'])) ? $_REQUEST['aba'] : 'visualiza';

$global = new GlobalClass();

function checkAba($aba1, $aba2) {
    return ($aba1 == $aba2) ? 'active' : '';
}

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "35", "area" => "Gestão Financeiro", "ativo" => "NFe - NFSe", "id_form" => "form1");

if (isset($_REQUEST['visualizaa']) && $_REQUEST['visualizaa'] == 'Visualizar') {
    $dataIni = converteData($_REQUEST['ini']);
    $dataFim = converteData($_REQUEST['fim']);
    $id_projeto = $_REQUEST['projeto'];
    $id_regiao = $_REQUEST['regiao'];

    $qry_nfse = "SELECT a.*, DATE_FORMAT(a.DataEmissao,'%d/%m/%Y') AS data, b.regiao AS nome_regiao, c.nome AS nome_projeto, d.c_razao AS nome_emitente, d.c_cnpj AS cnpj
            FROM nfse AS a
            INNER JOIN regioes  AS b ON (a.id_regiao = b.id_regiao)
            INNER JOIN projeto AS c ON (a.id_projeto = c.id_projeto)
            INNER JOIN prestadorservico AS d ON (a.PrestadorServico = d.id_prestador)
            WHERE b.id_regiao = '$id_regiao' AND c.id_projeto = '$id_projeto' AND (a.DataEmissao BETWEEN '$dataIni' AND '$dataFim') ORDER BY a.id_projeto,a.DataEmissao";
    $lista_nfse = mysql_query($qry_nfse);
    
    while ($row1 = mysql_fetch_assoc($lista_nfse)) {
        $listaNFSe[$row1['DataEmissao']][] = $row1;
    }
    
    $qry_nfe = "SELECT a.*, DATE_FORMAT(a.dEmi,'%d/%m/%Y') AS data, b.regiao AS nome_regiao, c.nome AS nome_projeto, d.c_razao AS nome_emitente, d.c_cnpj AS cnpj 
            FROM nfe AS a
            INNER JOIN regioes  AS b ON (a.id_regiao = b.id_regiao)
            INNER JOIN projeto AS c ON (a.id_projeto = c.id_projeto)
            INNER JOIN prestadorservico AS d ON (emitente = d.id_prestador)
            WHERE b.id_regiao = '$id_regiao' AND c.id_projeto = '$id_projeto' AND (a.dEmi BETWEEN '$dataIni' AND '$dataFim') ORDER BY a.id_projeto,a.dEmi";

    $lista = mysql_query($qry_nfe);

    while ($row = mysql_fetch_assoc($lista)) {
        $listaNFe[$row['dEmi']][] = $row;
    }
    
//    echo '<pre>';
//    print_r($listaNFSe);
//    print_r($listaNFe);
//    echo '</pre>';
}

$dataIni = (isset($_REQUEST['ini'])) ? $_REQUEST['ini'] : '';
$dataFim = (isset($_REQUEST['fim'])) ? $_REQUEST['fim'] : '';
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : '';
$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : '';

$projeto1 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, "id='projeto1' name='projeto' class='form-control validate[required,custom[select]]' data-for='prestador1'");
//$projeto2 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, "id='projeto2' name='projeto' class='form-control validate[required,custom[select]]' data-for='prestador2'");
//$projeto3 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, "id='projeto3' name='projeto' class='form-control validate[required,custom[select]]'");
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão NFe</title>
        <link rel="shortcut icon" href="../../favicon.png">
        <!-- Bootstrap -->        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?> 
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Relatórios de Notas Fiscais</h2></div>
                    <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>                                
                    <div class="bs-component">
                        <ul class="nav nav-tabs" style="margin-bottom: 15px;">
                            <li class="active"><a href="#consulta1" data-toggle="tab">Consulta</a></li>
                            <li><a href="#arquivo1" data-toggle="tab">Arquivo</a></li>
                        </ul>
                        <div class="tab-content">
                        <div class="tab-pane active" id="consulta1">
                            <form action="" id="form1" method="post" class="form-horizontal" enctype="multipart/form-data">
                                <input type="hidden" name="hide_projeto1" id="hide_projeto1" value="<?php echo $projetoSel ?>" />
                                <fieldset>
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="regiao" class="col-lg-2 control-label">Região</label>
                                                <div class="col-lg-4">
                                                    <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoSel, "id='regiao1' name='regiao' class='validate[required,custom[select]] form-control' data-for='projeto1'"); ?>
                                                </div>
                                                <label for="projeto" class="col-lg-1 control-label">Projeto</label>
                                                <div class="col-lg-4">
                                                    <?php echo $projeto1; ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-lg-2 control-label">Período</label>
                                                <div class="col-lg-9">
                                                    <div class="input-group">                                                                                                            
                                                        <span class="input-group-addon"><label class="glyphicon glyphicon-calendar"></label></span>
                                                        <input type="text" class="form-control text-center" id="inicio" name="ini" value="<?= $dataIni ?>">
                                                        <span class="input-group-addon">à</span>
                                                        <input type="text" class="form-control text-center" id="final" name="fim" value="<?= $dataFim ?>">
                                                        <span class="input-group-addon"><label class="glyphicon glyphicon-calendar"></label></span>
                                                    </div>    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer text-right">
                                            <input type="hidden" name="aba" id="aba" value="Visualizar">
                                            <input type="submit" value="Visualizar" name="visualizaa" class="btn btn-success btn-tm" id="visualizaa">
                                        </div>                                    
                                    </div>
                                </fieldset>
                            </form>
                            <div class="form-goup">
                                <div class="text text-center">
                                    <label class="text text-info">Notas Fiscais (NFe)</label>
                                </div>
                                <div id="notafiscal">
                                    <?php if (!empty($listaNFe)) { 
                                        foreach ($listaNFe as $data => $nfe) { ?>
                                            <hr>
                                            <div class="bg-info">
                                                <label class="text text-sm text-info"><?= converteData($data,"d/m/Y") ?></label>
                                            </div>
                                            <table class="table table-hover" style="table-layout: fixed">
                                                <thead>
                                                    <tr class="text text-sm">
                                                        <th style="width: 30%">Fornecedor</th>
                                                        <th style="width: 20%"></th>
                                                        <th style="width: 10%"></th>
                                                        <th style="width: 10%"></th>
                                                        <th class="text text-center" style="width: 20%">Nota Nr</th>
                                                        <th class="text text-right" style="width: 20%">Valor R$</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="textocondensado">
                                            <?php
                                            $total = 0;
                                            foreach ($nfe as $value) {
                                                $total += $value['vNF']; ?>
                                                <tr id="tr-<?= $value['id_nfe'] ?>">
                                                    <td><?= $value['nome_emitente'] ?></td>
                                                    <td><?= $value['cnpj'] ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td class="text-center"><?= $value['nNF'] ?></td>
                                                    <td><span class="pull-right"><?= number_format($value['vNF'], 2, ",", '.') ?></span></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot class="bg-warning text text-sm text-info">
                                            <tr>
                                                <td colspan="5"> Total no período</td>
                                                <td>R$<span class="pull-right"><?= number_format($total, 2, ",", '.') ?></span></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <?php
                                        } 
                                    } ?>
                                </div>
                            </div>
                            <hr>
                            <div class="form-goup">
                                <div class="text text-center">
                                    <label class="text text-info">Notas Fiscais de Serviços (NFSe)</label>
                                </div>
                                <div id="notaservico">
                                    <?php if (!empty($listaNFSe)) {
                                        foreach ($listaNFSe as $data => $nfse) { ?>
                                            <div class="bg-info">
                                                <label class="text text-sm text-info"><?= converteData($data,"d/m/Y") ?></label>
                                            </div>
                                            <table class="table table-hover" style="table-layout: fixed">
                                                <thead>
                                                    <tr class="text text-sm">
                                                        <th style="width: 30%">Prestador</th>
                                                        <th style="width: 30%"></th>
                                                        <th style="width: 10%">Nota Nr</th>
                                                        <th class="text text-right" style="width: 10%">Valor Total R$</th>
                                                        <th class="text text-right" style="width: 10%">Valor R$</th>
                                                        <th style="width: 10%"></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="textocondensado">
                                                    <?php $tot = 0;
                                                    foreach ($nfse as $campo){
                                                    $tot += $campo['ValorLiquidoNfse']; ?>
                                                    <tr id="tr-<?= $campo['id_nfse'] ?>">
                                                    <tr>
                                                    <td><?= $campo['nome_emitente'] ?></td>
                                                    <td><?= $campo['cnpj'] ?></td>
                                                    <td><?= $campo['Numero'] ?></td>
                                                    <td><span class="pull-right"><?= number_format($campo['ValorServicos'], 2, ",", '.') ?></span></td>
                                                    <td><span class="pull-right"><?= number_format($campo['ValorLiquidoNfse'], 2, ",", '.') ?></span></td>
                                                    <td><?php if ($campo['ValorServicos'] == $campo['ValorLiquidoNfse']){
                                                        echo '- Sem Retenção';
                                                        } else {
                                                            echo
                                                            '<tr class="text text-sm">
                                                                <td width="">COFINS</td>
                                                                <td>CSLL</td>
                                                                <td>INSS</td>
                                                                <td>IRPJ</td>
                                                                <td>PIS</td>
                                                                <td>Outras</td>
                                                            </tr>
                                                            <tr>
                                                                <td>'. number_format($campo['ValorCofins'], 2, ",", '.') .'</td>
                                                                <td>'. number_format($campo['ValorCsll'], 2, ",", '.').' </td>
                                                                <td>'. number_format($campo['ValorInss'], 2, ",", '.') .'</td>
                                                                <td>'. number_format($campo['ValorIr'], 2, ",", '.').' </td>
                                                                <td>'. number_format($campo['ValorPis'], 2, ",", '.') .'</td>
                                                                <td>'. number_format($campo['ValorOutras'], 2, ",", '.').' </td>
                                                            </tr>';
                                                        } ?>
                                                    </td>
                                                </tr>
                                        <?php }?>                                                
                                        </tbody>
                                        <tfoot class="bg-warning text text-sm text-info">
                                            <tr>
                                                <td colspan="5"> Total no período</td>
                                                <td>R$<span class="pull-right"><?= number_format($tot, 2, ",", '.') ?></span></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <?php }
                                    }?>
                                </div>
                            </div>
                        </div>
                            <div id="arquivo1" class="tab-pane text-center">
                                <label class="bg-warning">Gerar Arquivo de exportação para o Prosoft<p>EM ANDAMENTO</p></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>           
            <?php include('../../template/footer.php'); ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../resources/js/financeiro/saida.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<!--        <script src="form_NFe.js" type="text/javascript"></script>-->
        <script>

            $(function () {
                var id_destination = "projeto1";
                $('#regiao1').ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto1");

                $('#projeto1').change(function () {
                    var destino = $(this).data('for');
                    $.post("../../methods.php", {method: "carregaPrestadores", projeto: $(this).val()}, function (data) {
                        $("#" + destino).html(data);
                    });
                });

                $("#regiao1").trigger('change');

                $("#inicio").datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 1,
                    onClose: function (selectedDate) {
                        $("#final").datepicker("option", "minDate", selectedDate);
                    }
                });

                $("#final").datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 1,
                    onClose: function (selectedDate) {
                        $("#inicio").datepicker("option", "maxDate", selectedDate);
                    }
                });
            });
        </script>
        <style>
            .textocondensado{
                font-size: 0.9em;
            }
        </style>
    </body>    
</html>
