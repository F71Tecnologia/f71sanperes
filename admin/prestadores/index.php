<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("./PrestadorServicoClass.php");
include("./PrestadorDocumentosClass.php");
include("./PrestadorTipoDocClass.php");
require("../../classes/FuncionarioClass.php");
include('../../classes_permissoes/acoes.class.php');

$objAcoes = new Acoes();
$usuario = carregaUsuario();

$objPrestador = new PrestadorServicoClass();
$objPrestadorDocumentos = new PrestadorDocumentosClass();
$objPrestadorTipoDoc = new PrestadorTipoDocClass();

$objFuncionario = new FuncionarioClass();

//Array com os tipos de contrato
$arrTipos = array(
    "1" => "Pessoa Jurídica",
    "2" => "Pessoa Jurídica - Cooperativa",
    "3" => "Pessoa Física",
    "4" => "Pessoa Jurídica - Prestador de Serviço",
    "5" => "Pessoa Jurídica - Administradora",
    "6" => "Pessoa Jurídica - Publicidade",
    "7" => "Pessoa Jurídica Sem Retenção",
    "9" => "Pessoa Jurídica - Médico"
);

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "2", "area" => "Administrativo", "id_form" => "form1", "ativo" => "Gestão de Prestadores");
$breadcrumb_pages = array("Principal" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão de Prestadores</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->

        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
    </head> 
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-admin-header"><h2><span class="glyphicon glyphicon-cog"></span> - ADMINISTRATIVO<small> - Gest&atilde;o de Prestadores (Contratos)</small></h2></div>
                </div>
            </div>

            <form action="" method="post" class="form-horizontal">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Projeto:</label>
                            <div class="col-sm-9"><?= montaSelect(getProjetos($usuario['id_regiao']), $_REQUEST['id_projeto'], 'class="form-control" name="id_projeto" id="id_projeto"') ?></div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button type="submit" class="btn btn-sm btn-primary text-uppercase" name="filtrar"><i class="fa fa-filter"></i> Filtrar</button>
                        <?php //if($objAcoes->verifica_permissoes(99)){ ?>
                        <a href="form_prestador.php" class="btn btn-sm btn-success text-uppercase"><i class="fa fa-plus"></i> Cadastrar Prestador</a>
                        <?php //} ?>
                    </div>
                </div>
            </form>

            <?php
            if (isset($_REQUEST['filtrar'])) {
                $objPrestador->setId_regiao($usuario['id_regiao']);
                $objPrestador->setId_projeto($_REQUEST['id_projeto']);
                $objPrestador->getPrestadorAtivo();
                if ($objPrestador->getNumRowsPrestador() > 0) {
                    ?>
                    <table class="table table-condensed table-bordered table-hover text-sm valign-middle">
                        <?php
                        while ($objPrestador->getRowPrestador()) {
                            $objPrestadorDocumentos->setId_prestador($objPrestador->getId_prestador());
                            $objPrestadorDocumentos->getDocumentoPrestador();

                            if ($objPrestador->getPrestador_tipo() != $auxTipoPrestador) {
                                $auxTipoPrestador = $objPrestador->getPrestador_tipo();
                                ?>
                                <thead>
                                    <tr>
                                        <th colspan="8" class="bg-primary text-center"><?= $objPrestador->getPrestador_tipo() . " - " . $arrTipos[$objPrestador->getPrestador_tipo()] ?></th>
                                    </tr>
                                    <tr class="active">
                                        <th class="text-center">#</th>
                                        <th class="text-center">Ações</th>
                                        <th class="text-center">Nome</th>
                                        <th class="text-center">CNPJ</th>
                                        <th class="text-center">Inicio</th>
                                        <th class="text-center">Término</th>
                                        <th class="text-center">Valor</th>
                                        <th class="text-center">Doc</th>
                                    </tr>
                                </thead>
                            <?php } ?>
                            <tr>
                                <td class="text-center" style="min-width: 55px;"><?= $objPrestador->getId_prestador() ?></td>
                                <td class="text-center" style="min-width: 130px;">
                                    <button type="button" class="btn btn-xs btn-info gerenciar_prestador" data-key="<?= $objPrestador->getId_prestador() ?>" data-toggle="tooltip" data-original-title="Gerenciar Prestador"><i class="fa fa-book"></i></button>
                                    <button type="button" class="btn btn-xs btn-primary ver_documentos" data-key="<?= $objPrestador->getId_prestador() ?>" data-toggle="tooltip" data-original-title="Documentos"><i class="fa fa-search"></i></button>
                                    <button type="button" class="btn btn-xs btn-warning editar_prestador" data-key="<?= $objPrestador->getId_prestador() ?>" data-toggle="tooltip" data-original-title="Editar"><i class="fa fa-edit"></i></button>
                                    <button type="button" class="btn btn-xs btn-default form_duplicar_prestador" data-key="<?= $objPrestador->getId_prestador() ?>" data-toggle="tooltip" data-original-title="Duplicar"><i class="fa fa-copy"></i></button>
                                </td>
                                <td class=""><?= $objPrestador->getC_razao() ?></td>
                                <td class="text-center" style="width: 150px;"><?= $objPrestador->getC_cnpj() ?></td>
                                <td class="text-center" style="width: 80px;"><?= $objPrestador->getContratado_em("d/m/Y") ?></td>
                                <td class="text-center" style="width: 80px;"><?= $objPrestador->getEncerrado_em("d/m/Y") ?></td>
                                <td class="text-center" style="width: 140px;"><?= $objPrestador->getValor(true) ?></td>
                                <td class="text-center" style="width: 40px;"><?= $objPrestadorDocumentos->getNumRowPrestadorDocumentos() ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="alert alert-warning">Nenhum Prestador Ativo!</div>
                    <?php } ?>
                </table>
                <?php
                $objPrestador->setId_regiao($usuario['id_regiao']);
                $objPrestador->setId_projeto($_REQUEST['id_projeto']);
                $objPrestador->getPrestadorEncerrado();
                if ($objPrestador->getNumRowsPrestador() > 0) {
                    ?>
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-red">
                            <div class="panel-heading" role="tab" id="headingOne">
                                <h3 class="panel-title">
                                    <i class="fa fa-table"></i> 
                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="color:#fff;">
                                        Contratos encerrados
                                    </a>
                                </h3>
                            </div>
                            <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">

                                <table class="table table-condensed table-bordered table-hover text-sm valign-middle">
                                    <thead>        
        <!--                                            <tr class="bg-danger">
                                            <th colspan="8" class="text-center">Contratos encerrados</th>
                                        </tr>-->
                                        <tr class="active">
                                            <th class="text-center">#</th>
                                            <th class="text-center">Ações</th>
                                            <th class="text-center">Nome</th>
                                            <th class="text-center">CNPJ</th>
                                            <th class="text-center">Inicio</th>
                                            <th class="text-center">Término</th>
                                            <th class="text-center">Valor</th>
                                            <th class="text-center">Doc</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($objPrestador->getRowPrestador()) {
                                            $objPrestadorDocumentos->setId_prestador($objPrestador->getId_prestador());
                                            $objPrestadorDocumentos->getStatusList();
                                            ?>

                                            <tr>
                                                <td class="text-center" style="width: 55px;"><?= $objPrestador->getId_prestador() ?></td>
                                                <td class="text-center" style="width: 100px;">
                                                    <button type="button" class="btn btn-xs btn-primary ver_documentos" data-key="<?= $objPrestador->getId_prestador() ?>" data-toggle="tooltip" data-original-title="Documentos"><i class="fa fa-search"></i></button>
                                                    <button type="button" class="btn btn-xs btn-warning editar_prestador" data-key="<?= $objPrestador->getId_prestador() ?>" data-toggle="tooltip" data-original-title="Editar"><i class="fa fa-edit"></i></button>
                                                    <button type="button" class="btn btn-xs btn-default form_duplicar_prestador" data-key="<?= $objPrestador->getId_prestador() ?>" data-toggle="tooltip" data-original-title="Duplicar"><i class="fa fa-copy"></i></button>
                                                </td>
                                                <td class=""><?= $objPrestador->getC_razao() ?></td>
                                                <td class="text-center" style="width: 145px;"><?= $objPrestador->getC_cnpj() ?></td>
                                                <td class="text-center" style="width: 80px;"><?= $objPrestador->getContratado_em("d/m/Y") ?></td>
                                                <td class="text-center" style="width: 80px;"><?= $objPrestador->getEncerrado_em("d/m/Y") ?></td>
                                                <td class="text-center" style="width: 140px;"><?= $objPrestador->getValor(true) ?></td>
                                                <td class="text-center" style="width: 40px;"><?= $objPrestadorDocumentos->getNumRowPrestadorDocumentos() ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                <?php } ?>
                            </table>

                        </div>
                    </div>

                </div>
                <form id="form_prestador" method="post"></form>

                <div id="table-contratos-vencendo" class="hidden">
                    <table class="table table-striped table-condensed table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Ações</th>
                                <th class="text-center">Nome</th>
                                <th class="text-center">CNPJ</th>
                                <th class="text-center">Término</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            <?php } ?>
            <?php include("../../template/footer.php"); ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/administrativo/prestadores.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
    </body>
</html>