<?php
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='login.php'>Logar</a>";
exit;
}

include('conn.php');
include('wfunction.php');
$id = $_REQUEST['id'];

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['pro'];
$id_bol = $_REQUEST['id_bol'];
$unidade = $_REQUEST['unidade'];
$tipo = $_REQUEST['tipo'];

$caminho = $_REQUEST['caminho'];
$breadcrumb_caminhos[0] = array("Gestão de RH"=>"rh/", "Edição de Participantes"=>"rh/clt2.php");
$breadcrumb_caminhos[1] = array("Lista Projetos" => "ver2.php", "Visualizar Projeto" => "javascript:void(0);");
$breadcrumb_caminhos[2] = array("Lista Projetos" => "ver2.php", "Visualizar Projeto" => "javascript:void(0);", "Lista Participantes" => "javascript:void(0);");
$breadcrumb_config = array("nivel"=>"", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Relátorio Folha de Ponto/Produção");
$breadcrumb_pages = $breadcrumb_caminhos[$caminho];
$breadcrumb_attr = array("Visualizar Projeto" => "class='link-sem-get' data-projeto='$projeto' data-form='form1' data-url='ver2.php'", "Lista Participantes" => "class='link-sem-get' data-projeto='$projeto' data-form='form1' data-url='bolsista2.php'");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relátorio Folha de Ponto/Produção</title>
        <link href="favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="resources/css/main.css" rel="stylesheet" media="screen">
        <link href="resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="css/progress.css" rel="stylesheet" type="text/css">
        <link href="resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("template/navbar_default.php"); ?>
        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS</h2></div>
                    <h3>Relátorio Folha de Ponto/Produção</h3>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-10 col-lg-offset-1">
                    <?php switch($id) {
                        case 1: ?>

                            <form role="form" id="form1" class="form-horizontal" action="copiapontobolsistas.php" method="post">
                                
                                
                                <div class="form-group">
                                    <label>Tipo de Contrata&ccedil;&atilde;o:</label>
                                    <select name="tipo" class="form-control">
                                        <option value="1">Autônomo</option>
                                        <option value="2">CLT</option>
                                        <option value="3">Cooperado</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Digite a Data Inicial:</label>
                                    <input name="data" type="text" class="form-control" size="8" maxlength="10" onKeyUp="mascara_data(this)">
                                </div>
                                <div class="form-group">
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
                                <div class="form-group"><label style="color:#C30;"><?=$row_bol['nome']?></label></div>
                                <div class="form-group">
                                    <label>Digite a Data Inicial:</label>
                                    <input name="data" type="text" class="form-control" size="8" maxlength="10" onKeyUp="mascara_data(this)">
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="home" id="home" value="">
                                    <input type="hidden" name="projeto" value="<?=$projeto?>">
                                    <input type="hidden" name="regiao" value="<?=$regiao?>">
                                    <input type="hidden" name="unidade" value="<?=$unidade?>">
                                    <input type="hidden" name="id_bol" value="<?=$id_bol?>">
                                    <input type="hidden" name="tipo" value="<?=$tipo?>">
                                    <input type="submit" name="submit" value="Gerar Folha" class="btn btn-primary">
                                </div>
                            </form>
                        <?php break;
                    } ?>
                </div>
            </div>
        <?php include_once 'template/footer.php'; ?>
        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="resources/js/bootstrap.min.js"></script>
        <script src="resources/js/bootstrap-dialog.min.js"></script>
        <script src="js/jquery.validationEngine-2.6.js"></script>
        <script src="js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="resources/js/main.js"></script>
        <script src="js/global.js"></script>
    </body>
</html>