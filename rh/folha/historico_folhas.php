<?php // session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/FolhaClass.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$objFolha = new Folha();

$obj = new stdClass();
$obj->id_regiao = $usuario['id_regiao'];

if(!empty($_REQUEST['ano']) && $_REQUEST['ano'] > 0){
    $obj->ano = $_REQUEST['ano'];
}
if(!empty($_REQUEST['mes']) && $_REQUEST['mes'] > 0){
    $obj->mes = sprintf("%02s", $_REQUEST['mes']);
}
if(!empty($_REQUEST['id_projeto']) && $_REQUEST['id_projeto'] > 0){
    $obj->id_projeto = $_REQUEST['id_projeto'];
}
if($_REQUEST['id_projeto'] > 0 && $_REQUEST['ano'] > 0 && $_REQUEST['mes'] > 0) {
    $arrayFolha = $objFolha->getListaFolhas($obj, TRUE);
}

//print_array($arrayFolha);
//$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"6", "area"=>"Sistema", "id_form"=>"form1", "ativo"=>"Gestão de Funcionários");
//$breadcrumb_pages = array();
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão de Usuários</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Histórico de Folhas</small></h2></div>
            
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-md-offset-1 col-md-5">
                                <label class="text-bold">Projeto:</label>
                                <?= montaSelect(getProjetos($usuario['id_regiao']), $_REQUEST['id_projeto'], "id='id_projeto' name='id_projeto' class='form-control validate[required,custom[select]]'") ?>
                            </div>
                            <div class="col-md-5">
                                <label class="text-bold">Competência:</label>
                                <div class="input-group">
                                    <?= montaSelect(mesesArray(), $_REQUEST['mes'], "id='mes' name='mes' class='form-control validate[required,custom[select]]'") ?>
                                    <div class="input-group-addon">/</div>
                                    <?= montaSelect(anosArray(null,null,array(-1 => 'Selecione o ano')), $_REQUEST['ano'], "id='ano' name='ano' class='form-control validate[required,custom[select]]'") ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button type="submit" name="filtrar" id="filtrar" value="Filtrar" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                    </div>
                </div>
            </form>
            <?php if(count($arrayFolha) > 0) { ?>
                <table class='table table-condensed table-hover table-striped text-sm valign-middle'>
                    <thead>
                        <tr>
                            <th class="">ID</th>
                            <th class="">Projeto</th>
                            <th class="">Responsável</th>
                            <th class="text-center">Competencia</th>
                            <th class="">Período</th>
                            <th class="text-center">Qtd. Clts</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($arrayFolha as $row) { ?>
                        <tr class="<?= ($row['status'] == 2) ? 'sucess' : '' ?>">
                            <td class=""><?= $row['id_folha'] ?></td>
                            <td class=""><?= $row['nome_projeto'] ?></td>
                            <td class=""><?= $row['criado_por'] ?></td>
                            <td class="text-center">
                                <?php 
                                if($row['terceiro'] == 1) { 
                                    echo '13º / ';
                                  if($row['tipo_terceiro'] == 1) {
                                      echo '1ª PARCELA';
                                  } else if($row['tipo_terceiro'] == 2) {
                                      echo '2ª PARCELA';
                                  } else if($row['tipo_terceiro'] == 3) {
                                      echo 'INTEGRAL';
                                  }
                                } else { 
                                    echo mesesArray($row['mes'])." / {$row['ano']}";
                                } ?>
                            </td>
                            <td class=""><?= $row['periodo'] ?></td>
                            <td class="text-center"><?= $row['quant_clt'] ?></td>
                            <td class="text-center">
                                <a class="link-sem-get" data-url="ver_folha.php" data-id_regiao="<?= $usuario['id_regiao'] ?>" data-id_folha="<?= $row['id_folha'] ?>"><i class="btn btn-primary btn-sm fa fa-search" title="Ver Folha"></i></a>
                                <a class="link-sem-get" data-url="ver_folha_analitica.php" data-id_regiao="<?= $usuario['id_regiao'] ?>" data-id_folha="<?= $row['id_folha'] ?>"><i class="btn btn-warning btn-sm fa fa-bar-chart" title="Ver Folha Analitica"></i></a>
                                <a class="link-sem-get" data-url="relatorio_movimentos.php" data-id_regiao="<?= $usuario['id_regiao'] ?>" data-id_folha="<?= $row['id_folha'] ?>"><i class="btn btn-success btn-sm fa fa-times-circle-o" title="Relatorio Movimentos"></i></a>
                                <a class="link-sem-get" data-url="relatorio_rescisao_2.php" data-regiao="<?= $usuario['id_regiao'] ?>" data-id_folha="<?= $row['id_folha'] ?>"><i class="btn btn-info btn-sm fa fa-sign-out" title="Relatorio Rescisão"></i></a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <hr>
            <?php } else { ?>
            <div class="alert alert-warning text-bold">Nenhuma folha encontrada para o filtro selecionado!</div>
            <?php } ?>
            <?php include('../../template/footer.php'); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {                
                $("#form1").validationEngine({promptPosition : "topRight"});
                
                //datepicker
                var options = new Array();
                options['language'] = 'pt-BR';
                $('.datepicker').datepicker(options);
            });
        </script>
    </body>
</html>