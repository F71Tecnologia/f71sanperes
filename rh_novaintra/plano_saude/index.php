<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include("../../classes/PlanoSaudeClass.php");
include('../../classes_permissoes/acoes.class.php');

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$hoje = new DateTime();

$objAcoes = new Acoes();
$objPlanoSaude = new PlanoSaudeClass();

$qtdPorPlanoSaude = $objPlanoSaude->getCltPlanoSaude($usuario['id_regiao']);

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Gestão de Plano de Saúde");
$breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php"); ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Gestão de Plano de Saúde</title>
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
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Gestão de Plano de Saúde</small></h2></div>
                    <?php if($objAcoes->verifica_permissoes(105) || $objAcoes->verifica_permissoes(107)){ ?>
                    <div class="panel panel-default">
                        <div class="panel-footer text-right">
                            <?php if($objAcoes->verifica_permissoes(105)){ ?><a href="linkar_plano_clt.php" class="btn btn-sm btn-primary"><i class="fa fa-link"></i> Associar CLT ao Plano de Saúde</a><?php } ?>
                            <?php if($objAcoes->verifica_permissoes(107)){ ?><button class="btn btn-sm btn-success action_plano" data-acao="Cadastrar" data-key=""><i class="fa fa-plus"></i> Novo Plano de Saúde</button><?php } ?>
                        </div>
                    </div>
                    <?php 
                    }
                    $objPlanoSaude->getPlanoSaude();
                    if($objPlanoSaude->getNumRowPlanoSaude() > 0) { ?>
                        <table class="table table-bordered table-condensed table-hover valign-middle">
                            <thead>
                                <tr>
                                    <th colspan="6" class="bg-primary text-center"><h4>Lista de Planos de Saúde Cadastrados</h4></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($objPlanoSaude->getRowPlanoSaude()) { ?>
                                    <tr>
                                        <td width="45%"><?= $objPlanoSaude->getRazao() ?></td>
                                        <td width="45%"><?= $objPlanoSaude->getCnpj() ?></td>
                                        <td class="text-center">
                                            <?php if($qtdPorPlanoSaude[$objPlanoSaude->getIdPlanoSaude()] == 0){ ?><button class="btn btn-xs btn-success pointer detalhe_saude" title="Visualizar Participantes" data-toggle="tooltip" data-placement="top" data-acao="Visualizar" data-key="<?= $objPlanoSaude->getIdPlanoSaude() ?>"><i class="fa fa-search"></i></button><?php } ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($objAcoes->verifica_permissoes(107)){ ?><button class="btn btn-xs btn-warning pointer action_plano" title="Editar Plano" data-toggle="tooltip" data-placement="top" data-acao="Editar" data-key="<?= $objPlanoSaude->getIdPlanoSaude() ?>"><i class="fa fa-pencil"></i></button><?php } ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($qtdPorPlanoSaude[$objPlanoSaude->getIdPlanoSaude()] == 0 && $objAcoes->verifica_permissoes(106)) { ?><button class="btn btn-xs btn-danger pointer deletar" title="Excluir" data-placement="top" data-toggle="tooltip" data-key="<?= $objPlanoSaude->getIdPlanoSaude() ?>" data-nome="<?= $objPlanoSaude->getRazao() ?>"><i class="fa fa-trash-o"></i></button><?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php } else {
                            echo '<div class="alert alert-warning">Nenhum Plano de Saúde Cadastrado!</div>';
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
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>

        <script src="../../resources/js/rh/plano_saude/index.js"></script>
    </body>
</html>
