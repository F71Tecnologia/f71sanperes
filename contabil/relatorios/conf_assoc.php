<?php
error_reporting(E_ALL);

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
}
 
include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/BancoClass.php");
include("../../classes/global.php");
include("../../classes/c_classificacaoClass.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;

$objClassificador = new c_classificacaoClass();

$id_projeto = ($_REQUEST['id_projeto'] > 0) ? $_REQUEST['id_projeto'] : null;
$tipo_assoc = ($_REQUEST['tipo_assoc'] > 0) ? $_REQUEST['tipo_assoc'] : null;

if(isset($_REQUEST['tipo_assoc']) && isset($_REQUEST['gerar'])) { 
    
    $sqlFlPagamento = "SELECT DISTINCT cod, descicao FROM rh_movimentos ORDER BY cod";
    $qryFlPagamento = mysql_query($sqlFlPagamento) or die(mysql_error());
    $optFolha[0] = "SELECIONE O MOVIMENTO DE FOLHA";
    while($rowFolha = mysql_fetch_assoc($qryFlPagamento)){
        $optFolha[(int) $rowFolha['cod']] = $rowFolha['descicao']. ' ( '.$rowFolha['cod'].' )';
    }
    
    $arrayConferencia = $objClassificador->conferencia_associacao($id_projeto, $tipo_assoc);
    $arrayTitulo = array( 
        1 => array('C' => 'Credor', 'D' => 'Devedor'),
        2 => array('C' => 'Credor', 'D' => 'Devedor'),
        3 => array('C' => 'Credor', 'D' => 'Devedor'),
        4 => array('C' => 'Credito', 'D' => 'Debito')
    );
    //print_array($optFolha);
    $count = (count($arrayConferencia['D']) >= count($arrayConferencia['C']))? count($arrayConferencia['D']) : count($arrayConferencia['C']);
}

$nome_pagina = 'Conferênciade Associações de Contas';
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "frmplanodeconta"); ?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link rel="shortcut icon" href="../../favicon.png">
        <!-- Bootstrap -->        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" media="all">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?> 
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-contabil-header hidden-print">
                        <h2><?php echo $icon['38'] ?> - Contabilidade <small>- <?= $nome_pagina ?></small></h2>
                    </div>
                    <form action="" method="post" name="form_lote" id="form_lote" class="form-horizontal top-margin hidden-print" enctype="multipart/form-data">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="id_projeto" class="col-sm-2 text-sm control-label">Projeto</label>
                                    <div class="col-sm-4"><?= montaSelect(getProjetos($usuario['id_regiao']), $id_projeto, "id='id_projeto' name='id_projeto' class='form-control input-sm validate[required,custom[select]]'") ?></div>
                                </div>
                                <div class="form-group">
                                    <label for="projeto1" class="col-sm-2 text-sm control-label">Tipo: </label>
                                    <div class="col-sm-4"><?= montaSelect(array('1' => 'Contas X Entrada/Saída', '2' => 'Contas X Folha', '3' => 'Contas X Bancos',  '4' => 'Movimentos Folha'), $tipo_assoc, "id='tipo_assoc' name='tipo_assoc' class='form-control input-sm validate[required,custom[select]]'") ?></div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <button type="submit" id="criar" name="gerar" value="Gerar" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Gerar</button>
                            </div>
                        </div>
                    </form>
                    <table class="table table-condensed table-bordered table-hover table-striped text-sm valign-middle">
                        <thead>
                            <tr class="bg-primary">
                                <td class="text-center text-bold"><?= $arrayTitulo[$tipo_assoc]['D'] ?> (<?= count($arrayConferencia['D']) ?>)</td>
                                <td class="text-center text-bold"><?= $arrayTitulo[$tipo_assoc]['C'] ?> (<?= count($arrayConferencia['C']) ?>)</td>
                            </tr>
                        </thead>
                        <?php if($tipo_assoc != 2) { ?>
                        <tbody>
                            <?php for ($i = 0; $i < $count; $i++) { ?>
                            <tr>
                                <td class=""><?= (!empty($arrayConferencia['D'][$i]['id'])) ? "{$arrayConferencia['D'][$i]['nome']} ( {$arrayConferencia['D'][$i]['id']} )" : "" ?></td>
                                <td class=""><?= (!empty($arrayConferencia['C'][$i]['id'])) ? "{$arrayConferencia['C'][$i]['nome']} ( {$arrayConferencia['C'][$i]['id']} )" : "" ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <?php } else if($tipo_assoc == 2) { ?>
                        <tbody>
                            <?php for ($i = 0; $i < $count; $i++) { ?>
                            <tr>
                                <td class=""><?= (!empty($arrayConferencia['D'][$i]['id'])) ? "{$optFolha[$arrayConferencia['D'][$i]['id']]}" : "" ?></td>
                                <td class=""><?= (!empty($arrayConferencia['C'][$i]['id'])) ? "{$optFolha[$arrayConferencia['C'][$i]['id']]}" : "" ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <?php } ?>
                    </table>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../js/jquery.form.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../resources/js/financeiro/saida.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="js/classificacao.js" type="text/javascript"></script>
    </body>
</html>