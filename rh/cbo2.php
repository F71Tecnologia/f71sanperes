<?php
// Verificando se o usuário está logado
if(empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

// Incluindo Arquivos
require('../conn.php');
include('../classes/abreviacao.php');
include('../classes/formato_valor.php');
include('../classes/formato_data.php');
include('../wfunction.php');

$usuario = carregaUsuario();

// Consulta da Região
$regiao     = $usuario['id_regiao'];
$qr_regiao  = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

// Consulta dos Cursos de CLT
$qr_cbo_clt    = mysql_query("SELECT * FROM curso WHERE id_regiao = '$regiao' AND tipo = '2' AND status = '1' AND status_reg = '1' ORDER BY nome ASC");
$total_cbo_clt = mysql_num_rows($qr_cbo_clt);

// Consulta dos Cursos de Cooperado
$qr_cbo_cooperado    = mysql_query("SELECT * FROM curso WHERE id_regiao = '$regiao' AND tipo = '3' AND status = '1' AND status_reg = '1' ORDER BY nome ASC");
$total_cbo_cooperado = mysql_num_rows($qr_cbo_cooperado);

// Consulta do Curso
$qr_curso  = mysql_query("SELECT * FROM curso WHERE id_curso = '$_GET[id]' AND status = '1' AND status_reg = '1'");
$row_curso = mysql_fetch_assoc($qr_curso);

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"CBO");
$breadcrumb_pages = array("Gestão de RH"=>"../rh");

// Update do Curso
if(isset($_POST['id_cbo'])) {
    $qr_cbo  = mysql_query("SELECT * FROM rh_cbo WHERE id_cbo = '$_POST[id_cbo]'");
    $row_cbo = mysql_fetch_assoc($qr_cbo);
    mysql_query("UPDATE curso SET cbo_nome = '$row_cbo[nome]', cbo_codigo = '$row_cbo[id_cbo]' WHERE id_curso = '$_POST[id_curso]' LIMIT 1") or die (mysql_error()); ?>

    <script type="text/javascript">
    parent.window.location.reload();
    </script>
<?php } ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: CBO</title>
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
        <!--link href="../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->
    </head>
    <body>
    <?php if(!isset($_GET['id'])) {
        include("../template/navbar_default.php"); ?>

        <div class="container">
            <form id="form1" method="post">
                <input type="hidden" name="home" id="home" value="">
            </form>
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS</h2></div>
                    <h3>CBO <small>Total de Atividades: <?=($total_cbo_clt + $total_cbo_cooperado)?></small></h3><!--img src="imagensrh/logo-cbo.gif"-->
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">Atividades de CLT (<?=$total_cbo_clt?>)</h3>
                            </div>
                            <div class="panel-body overflow" style="max-height: 450px;">
                                <table class="table table-striped table-hover">
                                    <tbody>
                                        <?php while($row_cbo_clt = mysql_fetch_assoc($qr_cbo_clt)) { ?>
                                            <tr>
                                                <td><a href="?id=<?=$row_cbo_clt['id_curso']?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )" class="participante" title="Editar CBO desta atividade"><?=$row_cbo_clt['nome']?></a></td>
                                            </tr>
                                        <?php } ?>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">Atividades de Cooperado (<?=$total_cbo_cooperado?>)</h3>
                            </div>
                            <div class="panel-body overflow" style="max-height: 450px;">
                                <table class="table table-striped table-hover">
                                    <tbody>
                                        <?php while($row_cbo_cooperado = mysql_fetch_assoc($qr_cbo_cooperado)) { ?>
                                            <tr>
                                                <td><a href="?id=<?=$row_cbo_cooperado['id_curso']?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )" class="participante" title="Editar CBO desta atividade"><?=$row_cbo_cooperado['nome']?></a></td>
                                            </tr>
                                        <?php } ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once '../template/footer.php'; ?>
        </div><!-- /.container -->
    <!--table cellspacing="4" cellpadding="0" id="topo">
    <tr>
        <td width="18%" rowspan="2" valign="middle" align="center">
          
        </td>
        <?php if(!isset($_GET['id'])) { ?>
        <td width="82%"><b>Região:</b> <?php echo $regiao.' - '.$row_regiao['regiao']; ?></td>
      </tr>
      <tr>
        <td><b>Total de Atividades:</b> <?php echo ($total_cbo_clt + $total_cbo_cooperado); ?></td>
        <?php } else { ?>
        <td width="82%"><?php echo $row_curso['id_curso'].' - '.$row_curso['nome']; ?></td>
      </tr>
      <tr>
        <td><b>CBO:</b> <?php echo $row_curso['cbo_codigo'].' - '.$row_curso['cbo_nome']; ?></td>
        <?php } ?>
      </tr>
    </table-->
    <?php } else if(isset($_GET['id'])) { ?>
    <table cellpadding="4" cellspacing="0" id="folha" style="line-height:22px;">
        <tr style="background-color:#ddd; font-weight:bold;">
            <td>Alterar CBO</td>
        </tr>
        <tr style="background-color:#ddd; font-weight:bold;">
            <td><?=$row_curso['id_curso'].' - '.$row_curso['nome']?></td>
        </tr>
        <tr>
            <td>
                <form method="post" action="<?=$_SERVER['PHP_SELF']?>">
                    Digite o nome da profiss&atilde;o no campo ao <br>
                    lado e selecione uma das op&ccedil;&otilde;es abaixo:
                    <input type="text" name="pesquisa_usuario" onKeyUp="searchSuggest();" size="30" id="pesquisa_usuario" autocomplete="off">
                    <input type="submit" value="Concluir" class="btn btn-primary">
                    <input type="hidden" name="id_cbo" id="id_cbo" maxlength="6">
                    <input type="hidden" name="id_curso" id="id_curso" value="<?php echo $_GET['id']; ?>">
                </form>
            </td>
        </tr>
        <tr>
            <td><div id="ajax"></div></td>
        </tr>
    </table>
    <?php } ?>
            
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        
        <link href="../js/highslide.css" type="text/css" rel="stylesheet">
        <script src="../js/ajax_cbo.js" type="text/javascript"></script>
        <script src="../js/ramon.js" type="text/javascript"></script>
        <script src="../js/highslide-with-html.js" type="text/javascript"></script>
        <script type="text/javascript">
            hs.graphicsDir = '../images-box/graphics/'; 
            hs.outlineType = 'rounded-white';
        </script>
        <style type="text/css">
	li {list-style:none !important;}
	.highslide-html-content { width:650px; padding:5px; height: 250px; }
	#ajax{ visibility:hidden; border:2px solid #CCC; width:auto; height: auto; position:absolute; background:#FFF; font:8pt Tahoma, "Trebuchet MS", Arial; padding-bottom:35px; z-index: 10; }
	#ajax h3{font:bold 10pt "Trebuchet MS", Arial;margin:5px 10px 0}
	#ajax small{margin:0 10px;position:relative;top:-3px;color:#666;display:block}
	#ajax li a{display:block;padding:5px 4px 4px 22px;color:#000;text-decoration:none;background:#fff url('/img/topic_default.gif') 2px 2px no-repeat}
	#ajax a:hover{color:#333333;text-decoration:none;background-color:#F5F5F5}
	#ajax ul{margin:0 5px;padding:0;list-style:none}
	#ajax #info{position:absolute;bottom:0;background:#ffe;padding:5px;text-align:center;font-size:7.5pt;border-top:1px solid #fc0;width:290px;*width:296px;}
</style>
    </body>
</html>
