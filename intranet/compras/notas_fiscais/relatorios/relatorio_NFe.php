<?php

//session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
}

include("../../../conn.php");
include("../../../wfunction.php");
include("../../../classes/BotoesClass.php");
include("../../../classes/NFeClass.php");
include("../../../classes/BancoClass.php");
include("../../../classes/global.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);

// seta um valor para variável aba. usado para definir a aba aberta.
$aba = (isset($_REQUEST['aba'])) ? $_REQUEST['aba'] : 'visualiza';

$projeto1 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto1' name='projeto' class='form-control validate[required,custom[select]]' data-for='prestador1'");
$projeto2 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto2' name='projeto' class='form-control validate[required,custom[select]]' data-for='prestador2'");
$projeto3 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto3' name='projeto' class='form-control validate[required,custom[select]]'");

$global = new GlobalClass();

function checkAba($aba1, $aba2) {
    return ($aba1 == $aba2) ? 'active' : '';
}

$breadcrumb_config = array("nivel" => "../../../", "key_btn" => "35", "area" => "Gestão de Compras e Contratos", "ativo" => "NFe", "id_form" => "form1");

if (isset($_REQUEST['visualiza']) && $_REQUEST['visualiza'] == 'Visualizar') {
    $dataIni = converteData($_REQUEST['ini']);
    $dataFim = converteData($_REQUEST['fim']);
    
    $qry = "SELECT a.*, DATE_FORMAT(a.dEmi,'%d/%m/%Y') AS data, b.regiao AS nome_regiao, c.nome AS nome_projeto, d.c_razao AS nome_emitente, d.c_cnpj AS cnpj FROM nfe AS a
            INNER JOIN regioes  AS b ON (a.id_regiao = b.id_regiao)
            INNER JOIN projeto AS c ON (a.id_projeto = c.id_projeto)
            INNER JOIN prestadorservico AS d ON (emitente = d.id_prestador)
            WHERE a.dEmi BETWEEN '$dataIni' AND '$dataFim' ORDER BY a.id_projeto";
//    echo $qry;
    $lista = mysql_query($qry);
    
    while ($row = mysql_fetch_assoc($lista)) {
        $listaNfe[] = $row;
    }
    
}
    $dataIni = (isset($_REQUEST['ini']))? $_REQUEST['ini']: '';
    $dataFim = (isset($_REQUEST['fim']))? $_REQUEST['fim']: '';
    
    ?>



<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão NFe</title>
        <link rel="shortcut icon" href="../../../favicon.png">
        <!-- Bootstrap -->        
        <link href="../../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-compras.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../../../template/navbar_default.php"); ?> 
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-compras-header">
                        <h2><span class="glyphicon glyphicon-shopping-cart"></span> - Gestão de Compras e Contratos <small>- Relatórios</small></h2>
                    </div>
                    <!--resposta de algum metodo realizado-->
                    <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>                                
                    <div class="bs-component">
                        <ul class="nav nav-tabs" style="margin-bottom: 15px;">
                            <li class="active"><a href="#consulta1" data-toggle="tab">Consulta NFe</a></li>
                            <li><a href="#arquivoXML" data-toggle="tab">Arquivo</a></li>
                        </ul>
                        <div class="tab-pane" id="consulta1"> 
                            <form action="" id="form1" method="post" class="form-horizontal" enctype="multipart/form-data">
                                <fieldset>
                                    <div class="panel panel-default">
                                        <div class="panel-body">
<!--                                            <div class="form-group">
                                                <label for="regiao" class="col-lg-2 control-label">Região</label>
                                                <div class="col-lg-4">
                                                    <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao1' name='regiao' class='validate[required,custom[select]] form-control' data-for='projeto1'"); ?>
                                                </div>
                                                <label for="projeto" class="col-lg-1 control-label">Projeto</label>
                                                <div class="col-lg-4">
                                                    <?php echo $projeto1; ?>
                                                </div>
                                            </div>
-->                                         <div class="form-group">
                                                <label for="" class="col-lg-2 control-label">Data Inicial</label>
                                                <div class="col-lg-2">
                                                    <div class="input-group">                                                                                                            
                                                        <span class="input-group-addon"><label class="glyphicon glyphicon-calendar"></label></span>
                                                        <input type="text" class="form-control text-center" id="inicio" name="ini" value="<?= $dataIni ?>">
                                                            
                                                    </div>
                                                </div>
                                                <label for="" class="col-lg-3 control-label">Data Final</label>
                                                <div class="col-lg-2">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><label class="glyphicon glyphicon-calendar"></label></span>
                                                        <input type="text" class="form-control text-center" id="final" name="fim" value="<?= $dataFim ?>">
                                                    </div>    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer text-right">
                                            <input type="hidden" name="aba" id="aba" value="Visualizar">
                                            <input type="submit" value="Visualizar" name="visualiza" class="btn btn-success btn-tm" id="visualiza">
                                        </div>                                    
                                    </div>
                                </fieldset>
                            </form>
                            <div id="visualizar-NFe" class="">
                            <table class="table table-striped info table-hover table-condensed">
                                <thead>
                                    <tr class="text-danger textocondensado">
                                        <th style="width: 7%;">Emissão</th>
                                        <th class="text text-center" style="width: 5%;">NF</th>
                                        <th style="width: 25%;">Emitente</th>
                                        <th style="width: 12%;">CNPJ/CPF</th>
                                        <th style="width: 22%;">Projeto</th>
                                        <th style="width: 12%;">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="textocondensado">
                                    <?php 
                                    $total = 0;
                                    foreach ($listaNfe as $value) {
                                        $total += $value['vNF']; ?>
                                        <tr id="tr-<?= $value['id_nfe'] ?>">
                                            <td><?= $value['data'] ?></td>
                                            <td class="text-center"><?= $value['nNF'] ?></td>
                                            <td><?= $value['nome_emitente'] ?></td>
                                            <td><?= $value['cnpj'] ?></td>
                                            <td><?= $value['nome_projeto'] ?></td>
                                            <td>R$<span class="pull-right"><?= number_format($value['vNF'], 2, ",", '.') ?></span></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr class="text-danger">
                                        <td colspan="5"> Total no período</td>
                                        <td>R$<span class="pull-right"><?= number_format($total, 2, ",", '.') ?></span></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- FIM DO VISUALIZAR XML -->
                </div>
            </div>
        </div>           
        <?php include_once '../../../template/footer.php'; ?>
    </div>
    <script src="../../../js/jquery-1.10.2.min.js"></script>
    <script src="../../../js/jquery-ui-1.9.2.custom.min.js"></script>
    <script src="../../../resources/js/bootstrap.min.js"></script>
    <script src="../../../resources/js/bootstrap-dialog.min.js"></script>
    <script src="../../../resources/js/main.js"></script>
    <script src="../../../js/global.js"></script>
    <script src="../../../js/jquery.maskedinput-1.3.1.js"></script>
    <script src="../../../resources/js/financeiro/saida.js"></script>
    <script src="../../../js/jquery.validationEngine-2.6.js"></script>
    <script src="../../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
    <script src="../../../js/jquery.maskMoney_3.0.2.js"></script>
    <script src="../../../js/jquery.form.js" type="text/javascript"></script>
    <script src="../../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
    <script src="form_NFe.js" type="text/javascript"></script>
    <script>
        $('#regiao1,#regiao2,#regiao3').change(function () {
            var destino = $(this).data('for');
            $.post("../../../methods.php", {method: "carregaProjetos", regiao: $(this).val()}, function (data) {
                $("#" + destino).html(data);
            });
        });
        $('#projeto1,#projeto2').change(function () {
            var destino = $(this).data('for');
                $.post("../../../methods.php", {method: "carregaPrestadores", projeto: $(this).val()}, function (data) {
                    $("#" + destino).html(data);
                });
            });
            $(function() {
                $( "#inicio" ).datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 1,
                    onClose: function( selectedDate ) {
                        $( "#final" ).datepicker( "option", "minDate", selectedDate );
                    }
                });
                $( "#final" ).datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 1,
                    onClose: function( selectedDate ) {
                        $( "#inicio" ).datepicker( "option", "maxDate", selectedDate );
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
