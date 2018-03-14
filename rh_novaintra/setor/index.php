<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include("../../classes/SetorClass.php");
include('../../classes_permissoes/acoes.class.php');

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$hoje = new DateTime();

$objAcoes = new Acoes();
$setorObj = new SetorClass();

$qtdPorSetor = $setorObj->getCltSetor($usuario['id_regiao']);

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Setor");
$breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php"); ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Setor</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <!--link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Setor</small></h2></div>
                    <?php if($objAcoes->verifica_permissoes(104) || $objAcoes->verifica_permissoes(102)){ ?>
                    <?php if ($_COOKIE['logado'] != 395) { ?>
                    <div class="panel panel-default">
                        <div class="panel-footer text-right">
                            <?php if($objAcoes->verifica_permissoes(104)){ ?><a href="linkar_setor_clt.php" class="btn btn-sm btn-primary"><i class="fa fa-link"></i> Associar CLT ao Setor</a><?php } ?>
                            <?php if($objAcoes->verifica_permissoes(102)){ ?><button class="btn btn-sm btn-success action_setor" data-acao="Cadastrar" data-key=""><i class="fa fa-plus"></i> Novo Setor</button><?php } ?>
                        </div>
                    </div>
                    <?php 
                    } }
                    $setorObj->getSetor();
                    if($setorObj->getNumRowSetor() > 0) { ?>
                        <table class="table table-bordered table-condensed table-hover valign-middle">
                            <thead>
                                <tr>
                                    <th colspan="6" class="bg-primary text-center"><h4>Lista de Setores Cadastrados</h4></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php 
                                    $count=0;
                                    while($setorObj->getRowSetor()) { ?>
                                        <td width="45%"><?= $setorObj->getNome() ?></td>
                                        <td class="text-center">
                                            <?php if ($_COOKIE['logado'] != 395) { ?>
                                            <?php if($objAcoes->verifica_permissoes(102)){ ?><button class="btn btn-xs btn-warning pointer action_setor" title="Editar" data-toggle="tooltip" data-acao="Editar" data-key="<?= $setorObj->getIdSetor() ?>"><i class="fa fa-pencil"></i></button><?php } ?>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($qtdPorSetor[$setorObj->getIdSetor()] == 0 && $objAcoes->verifica_permissoes(103)) { ?><button class="btn btn-xs btn-danger pointer deletar" title="Excluir" data-toggle="tooltip" data-key="<?= $setorObj->getIdSetor() ?>" data-nome="<?= $setorObj->getNome() ?>"><i class="fa fa-trash-o"></i></button><?php } ?>
                                        </td>
                                        <?php if((++$count%2) == 0){ echo '</tr><tr>'; }
                                    } ?>
                                </tr>
                            </tbody>
                        </table>
                        <?php } else {
                            echo '<div class="alert alert-warning">Nenhum Setor Cadastrado!</div>';
                        } ?>
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->


            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>

        <script src="../../resources/js/rh/setor/index.js"></script>
    </body>
</html>
