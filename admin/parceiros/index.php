<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/ParceiroClass2.php");
require("../../classes/FuncionarioClass.php");
include('../../classes_permissoes/acoes.class.php');

$objAcoes = new Acoes();
$usuario = carregaUsuario();
$objParceiro = new ParceiroClass();
$objFuncionario = new FuncionarioClass();

$objParceiro->setDefault();
$objParceiro->setIdRegiao($usuario['id_regiao']);
$objParceiro->setParceiroStatus(1);
if(!$objParceiro->selectAll()){
    echo $objParceiro->getError();
    exit;
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"2", "area"=>"Administrativo", "id_form"=>"form1", "ativo"=>"Gestão de Participantes");
$breadcrumb_pages = array("Principal" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão de Participantes</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <style>.bg-admin{padding: 10px 0 10px 0!important;}</style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-admin-header"><h2><span class="glyphicon glyphicon-cog"></span> - ADMINISTRATIVO<small> - Gestão de Participantes</small></h2></div>
                </div>
            </div>
            
            <?php if($objAcoes->verifica_permissoes(99)){ ?>
            <div class="panel panel-default">
                <div class="panel-footer text-right">
                    <a href="form_parceiros.php" class="btn btn-success text-uppercase"><i class="fa fa-plus"></i> Cadastrar Parceiro</a>
                </div>
            </div>
            <?php } ?>
            
            <?php if($objParceiro->getNumRow() > 0){ ?>
            <table class="table table-condensed table-bordered table-hover text-sm valign-middle">
                <thead>
                    <tr class="bg-primary">
                        <th class="text-center"></th>
                        <th class="text-center">NOME</th>
                        <th class="text-center"></th>
                        <th class="text-center">ÚLTIMA EDIÇÃO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($objParceiro->getRow()){ ?>
                        <tr>
                            <td class="text-center" width="160px;"><img style="width: 100px; height: auto;" src="../../adm/adm_parceiros/logo/<?=(!empty($objParceiro->getParceiroLogo())) ? $objParceiro->getParceiroLogo() : 'sem_imagem' ?>" /></td>
                            <td class=""><?=$objParceiro->getParceiroNome()?></td>
                            <td class="text-center" width="90px;">
                                <a href="javascript:;" class="btn btn-xs btn-warning editar_parceiro" data-key="<?=$objParceiro->getIdParceiro()?>"><i class="fa fa-edit"></i></a>
                                <?php if($objAcoes->verifica_permissoes(98)){ ?>
                                <a href="javascript:;" class="btn btn-xs btn-danger excluir_parceiro" data-key="<?=$objParceiro->getIdParceiro()?>"><i class="fa fa-trash-o"></i></a>
                                <?php } ?>
                            </td>
                            <td class="" width="170px;">
                                <?=($objParceiro->getParceiroDataAtualizacao() != '0000-00-00 00:00:00') 
                                    ? "Editado em {$objParceiro->getParceiroDataAtualizacao('d/m/Y')} por {$objFuncionario->getFuncionarioNome($objParceiro->getParceiroIdAtualizacao(),TRUE)}"
                                    : "Cadastrado em {$objParceiro->getParceiroData('d/m/Y')} por {$objFuncionario->getFuncionarioNome($objParceiro->getParceiroAutor(),TRUE)}" ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
                <?php } else { ?>
                <div class="alert alert-warning">Nenhum Parceiro Cadastrado Nesta Região!</div>
                <?php } ?>
            </table>
            <form id="editar_parceiro" method="post"></form>
            <?php include("../../template/footer.php"); ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/administrativo/parceiros.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
    </body>
</html>