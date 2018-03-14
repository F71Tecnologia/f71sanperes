<?php
include('include/restricoes.php');
include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');
include "include/criptografia.php";

$usuario = carregaUsuario();
//echo '<pre>';print_r($_REQUEST);
$query_funcionario = mysql_query("SELECT id_funcionario, nome, tipo_usuario,id_master FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_funcionario   = mysql_fetch_array($query_funcionario);

$id_clt  = $_REQUEST['clt'];
$projeto = $_REQUEST['pro'];
$regiao  = $_REQUEST['id_reg'];

$query_master      = mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$id_reg'");
$id_master         = @mysql_result($query_master,0);

if(isset($_REQUEST['cadastrar'])){

    $nome 	= $_REQUEST['nome'];
    $id_clt 	= $_REQUEST['id_clt'];
    $atividade 	= $_REQUEST['atividade'];
    $n_processo = $_REQUEST['n_processo'];
    $projeto    = $_REQUEST['projeto'];
    $regiao     = $_REQUEST['regiao'];

    mysql_query("INSERT INTO processos_interno (id_clt, proc_interno_nome, proc_interno_numero, proc_interno_atividade, data_cad, proc_interno_status)
                                                                                            VALUES
                                                                                            ('$id_clt', '$nome', '$n_processo', '$atividade',NOW(),  '1')");
    header("Location: ver_clt.php?clt=$id_clt&reg=$regiao&pro=$projeto");
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Abertura de Processo");
$breadcrumb_pages = array("Lista Projetos" => "../ver2.php", "Visualizar Projeto" => "javascript:void(0);", "Lista Participantes" => "javascript:void(0);", "Visualizar Participante" => "javascript:void(0);");
$breadcrumb_attr = array(
    "Visualizar Projeto" => "class='link-sem-get' data-projeto='$projeto' data-form='form1' data-url='../ver2.php'",
    "Lista Participantes" => "class='link-sem-get' data-projeto='$projeto' data-form='form1' data-url='../bolsista2.php'",
    "Visualizar Participante" => "class='link-sem-get' data-pro='$projeto' data-clt='$id_clt' data-form='form1' data-url='ver_clt2.php'"
); ?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Abertura de Processo</title>
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
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/add-ons.min.css" rel="stylesheet">
    </head>
    <body>
    <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <form id="form1" method="post"></form>
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Abertura de Processo</small></h2></div>
                </div>
            </div>
            <form name="form" action="abertura_processo.php" method="post" class="form-horizontal" id="form1">
                <fieldset>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="regiao" class="col-md-2 control-label">PROCESSO Nº:</label>
                                <div class="col-md-9">
                                    <input class="form-control" type="text" name="n_processo" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="projeto" class="col-md-2 control-label">ATIVIDADE:</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="atividade" type="text"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="projeto" class="col-md-2 control-label">NOME:</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="nome" type="text"/>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <input type="hidden" name="id_clt" value="<?php echo $id_clt;?>"/>
                            <input type="hidden" name="regiao" value="<?php echo $regiao;?>"/>
                            <input type="hidden" name="projeto" value="<?php echo $projeto;?>"/>
                            <input type="submit" class="btn btn-success" name="cadastrar" value="CADASTRAR"/>
                        </div>
                    </div>
                </fieldset>
            </form>
            <?php include_once '../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="http://malsup.github.com/jquery.form.js" type="text/javascript"></script>
        <script type="text/javascript" src="../uploadfy/scripts/swfobject.js"></script>
        <script type="text/javascript" src="../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>
    </body>
</html>