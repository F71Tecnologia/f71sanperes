<?php
session_start();
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../conn.php');
include('../classes/global.php');
include('../wfunction.php');
//include('../classes/RhCltClass.php');

$usuario = carregaUsuario();
//$objClt = new RhCltClass();


//$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];
$id_unidade = $_REQUEST['unidade'];

$filtro = false;

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) || (isset($_SESSION['voltarCurso'])) || ($_REQUEST['atualizar'])) {
    $filtro = true;
    
//    $objClt->setDefault();
//    $objClt->setIdProjeto($id_projeto);
    $queryClt = mysql_query("SELECT id_clt, nome, DATE_FORMAT(data_entrada, '%d/%m/%Y') data_entrada FROM rh_clt WHERE id_projeto = $id_projeto AND id_unidade = $id_unidade AND (status < 60 OR status = 200) ;");
    if(!$queryClt){
        echo $objClt->getError();
        exit("</br>Houve um erro ao selecionar os CLTs  do projeto ({$id_projeto})");
    }
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
if (isset($_REQUEST['projeto']) && isset($_REQUEST['regiao'])) {
    $projetoR = $_REQUEST['projeto'];
    $regiaoR = $_REQUEST['regiao'];
} elseif (isset($_SESSION['projeto']) && isset($_SESSION['regiao'])) {
    $projetoR = $_SESSION['projeto'];
    $regiaoR = $_SESSION['regiao'];
} elseif (isset($_SESSION['projeto_select']) && isset($_SESSION['regiao_select'])) {
    $projetoR = $_SESSION['projeto_select'];
    $regiaoR = $_SESSION['regiao_select'];
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Cracha Provisorio");
//$breadcrumb_pages = array("Gestão de RH"=>"../");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Cracha Provisorio</title>
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
        <link href="../resources/css/bootstrap-note.css.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Cracha Provisorio</small></h2></div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" class="form-horizontal" >
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="form-group">
                                    
                                    <label class="col-xs-offset-1 col-xs-1 control-label">Projeto: </label>
                                    <div class="col-xs-4">
                                        <?=montaSelect(GlobalClass::carregaProjetosByRegiao($usuario['id_regiao']), $id_projeto, "id='projeto' name='projeto' class='required[custom[select]] form-control' ") ?>
                                    </div>
                                    
                                    <label class="col-xs-1 control-label">Unidade: </label>
                                    <div class="col-xs-4">
                                        <?=montaSelect(array('' => 'Selecione o projeto'), $id_unidade, "id='unidade' name='unidade' class='required[custom[select]] form-control' ") ?>
                                    </div>
                                </div>
                            </div><!-- /.panel-body -->
                            <div class="panel-footer text-right">
                                <input type="submit" class="btn btn-primary" value="Filtrar" name="filtrar" id="filt" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php
            if ($filtro) { ?>
            <form action="../rh_novaintra/cracha_provisorio_imprimir.php" method="post">
                <div class="table-responsive">
                    <table id="tbRelatorio" class="table table-striped table-hover table-bordered table-condensed text-sm valign-middle">
                        <thead>
                            <tr class="bg-primary valign-middle">
                                <th class="text-center" width="5%"><input type="checkbox" id="CheckAll"></th>
                                <th width="80%">Nome</th>
                                <th class="text-center" width="15%">Data Entrada</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        while ($rowClt = mysql_fetch_assoc($queryClt)) { ?>
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="id_clt[]" class="Check" value="<?=$rowClt['id_clt']?>">
                                </td>
                                <td class=""><?=$rowClt['nome']?></td>
                                <td class="text-center"><?=$rowClt['data_entrada']?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right">
                                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-print"></i> Visualizar Impressão</button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </form>
            <?php } ?>

            <?php include_once '../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script>
            $(function() {
                $("#projeto").ajaxGetJson("../methods.php", {method: "carregaUnidades", projeto: $("#projeto").val(), regiao: "<?=$usuario['id_regiao']?>", unidade: "<?=$id_unidade?>"}, null, "unidade");
                $('body').on('change', '#CheckAll', function () {
                    var sel = $(this).prop('checked');
                    $('.Check').each(function() {
                        $(this).prop('checked',sel);
                    });
                });
            });
        </script>
    </body>
</html>