<?php
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='login.php'>Logar</a>";
exit;
}

include('../conn.php');
include('../wfunction.php');
$id = $_REQUEST['id'];

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)

$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['pro'];
$id_bol = $_REQUEST['id_bol'];
$unidade = $_REQUEST['unidade'];
$tipo = $_REQUEST['tipo'];

$caminho = $_REQUEST['caminho'];
$breadcrumb_caminhos[0] = array("Gest�o de RH"=>"index.php", "Edi��o de Participantes"=>"clt.php");
$breadcrumb_caminhos[1] = array("Lista Projetos" => "ver.php", "Visualizar Projeto" => "javascript:void(0);");
$breadcrumb_caminhos[2] = array("Lista Projetos" => "ver.php", "Visualizar Projeto" => "javascript:void(0);", "Lista Participantes" => "javascript:void(0);");
$breadcrumb_config = array("nivel"=>"", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Rel�torio Folha de Ponto/Produ��o");
$breadcrumb_pages = $breadcrumb_caminhos[$caminho];
$breadcrumb_attr = array("Visualizar Projeto" => "class='link-sem-get' data-projeto='$projeto' data-form='form1' data-url='ver.php'", "Lista Participantes" => "class='link-sem-get' data-projeto='$projeto' data-form='form1' data-url='bolsista.php'");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Rel�torio Folha de Ponto/Produ��o</title>
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
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Rel�torio Folha de Ponto/Produ��o</small></h2></div>
                </div>
            </div>
            <div class="panel panel-default">
                <?php switch($id) {
                    case 1: ?>
                        <form role="form" id="form1" class="form-horizontal" action="copiapontobolsistas.php" method="post">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-xs-2 control-label">Tipo de Contrata&ccedil;&atilde;o:</label>
                                    <div class="col-xs-4">
                                        <select name="tipo" class="form-control">
                                            <option value="1">Aut�nomo</option>
                                            <option value="2">CLT</option>
                                            <option value="3">Cooperado</option>
                                        </select>
                                    </div>
                                    <label class="col-xs-2 control-label">Digite a Data Inicial:</label>
                                    <div class="col-xs-4">
                                        <input name="data" type="text" class="data form-control" maxlength="10" id="data">
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <input type="hidden" name="home" id="home" value="">
                                <input type="hidden" name="projeto" value="<?=$projeto?>">
                                <input type="hidden" name="regiao" value="<?=$regiao?>">
                                <input type="submit" name="submit" value="Gerar Folha" class="btn btn-primary">
                            </div>
                        </form>
                    <?php break;
                    case 2: 
                        if($tipo == "clt") {
                            $result_bol = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$id_bol' AND id_projeto = '$projeto'");
                        } else {
                            $result_bol = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$id_bol' AND id_projeto = '$projeto'");
                        } 
                        $row_bol = mysql_fetch_array($result_bol); ?>
                        <form role="form" id="form1" class="form-horizontal" action="pontobolsistas.php" method="post">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label style="color:#C30;"><?=$row_bol['nome']?></label>
                                </div>
                                <div class="form-group">
                                    <label><label class="col-xs-2 control-label">Digite a Data Inicial:</label>
                                    <div class="col-xs-10">
                                        <input name="data" type="text" class="data form-control" maxlength="10" id="data">
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <div class="form-group">
                                    <input type="hidden" name="home" id="home" value="">
                                    <input type="hidden" name="projeto" value="<?=$projeto?>">
                                    <input type="hidden" name="regiao" value="<?=$regiao?>">
                                    <input type="hidden" name="unidade" value="<?=$unidade?>">
                                    <input type="hidden" name="id_bol" value="<?=$id_bol?>">
                                    <input type="hidden" name="tipo" value="<?=$tipo?>">
                                    <input type="submit" name="submit" value="Gerar Folha" class="btn btn-primary">
                                </div>
                            </div>    
                        </form>
                    <?php break;
                } ?>
            </div>
            <?php include_once '../template/footer.php'; ?>
        </div>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.mask.min.js" type="text/javascript"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script>
        $(function(){
            $('.data').datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true
            });
        });
        </script>
    </body>
</html>