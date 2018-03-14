<?php
// session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../../login.php?entre=true';</script>";
}

include("../../../conn.php");
include("../../../wfunction.php");
include("../../../classes/BotoesClass.php");
include("../../../classes/EntradaESaidaGrupo.php");
include("../../../classes/EntradaESaidaSubGrupo.php");
include("../../../classes/EntradaESaida.php");
include("../../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$global = new GlobalClass();

$objConta = new EntradaESaidaClass();

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'excluir') {
    $id = addslashes($_REQUEST['id']);
    $objConta->setIdEntradasaida($id);
    $objConta->inativa();
    echo json_encode(['status' => $objConta->inativa()]);
    exit();
}



$nome_pagina = 'Gestão de Contas';
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "4", "area" => "Financeiro", "id_form" => "form1", "ativo" => $nome_pagina);
$breadcrumb_pages = array("Principal" => "../../index.php", "Contas Financeiro" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link href="../../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../../template/navbar_default.php"); ?>

        <!--<div class="container-full over-x">-->
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - <?= $nome_pagina ?></small></h2></div>
            
            <div class="row">
                <div class="col-sm-2 col-sm-offset-10">
                    <a href="form_conta.php" class="btn btn-success btn-block"><i class="fa fa-plus"></i> Novo</a>
                    <br>
                </div>
            </div>
            
            <?php
            $objConta->listaEntradaESaida();
            if ($objConta->getNumRows() > 0) {
                ?>
            <div class="panel panel-default">
                <table class="table valign-middle text-sm">
                    <thead>
                        <tr class="info">
                            <th>Cod</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>SubGrupo</th>
                            <th style="width: 100px">&emsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($objConta->getRow()) { ?>
                            <tr id="p<?= $objConta->getIdEntradasaida() ?>">
                                <td><?= $objConta->getCod() ?></td>
                                <td><?= $objConta->getNome() ?></td>
                                <td><?= $objConta->getDescricao() ?></td>
                                <td><?= $objConta->getIdSubgrupo() ?></td>
                                <td class="text-right">
                                    <a class="btn btn-xs btn-success" href="form_conta.php?id=<?= $objConta->getIdEntradasaida() ?>"><i class="fa fa-pencil"></i></a>
                                    <button class="btn btn-xs btn-danger btn_excluir" data-id="<?= $objConta->getIdEntradasaida() ?>"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php } else { ?>
                <div class="alert alert-info">Nenhum patrimonio cadastrado</div>
            <?php } ?>

            <?php include('../../../template/footer.php'); ?>
        </div>

        <script src="../../../js/jquery-1.10.2.min.js"></script>
        <script src="../../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../../resources/js/bootstrap.min.js"></script>
        <script src="../../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../../resources/dropzone/dropzone.js"></script>
        <script src="../../../js/jquery.form.js"></script>
        <script src="../../../resources/js/main.js"></script>
        <script src="../../../js/global.js"></script>
        <script src="../../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../js/excluir.js" type="text/javascript"></script>
    </body>
</html>
