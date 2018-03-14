<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include("../conn.php");
include("../classes/funcionario.php");
include("../classes_permissoes/regioes.class.php");
include("../classes_permissoes/acoes.class.php");
include("../wfunction.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$optRegiao = getRegioes();

$ACOES = new Acoes();

$id_portal = $_REQUEST['id_portal'];
$titulo_mensagem = $_REQUEST['titulo'];
$mensagem_mensagem = $_REQUEST['mensagem'];
$id_usuario = $_COOKIE['logado'];
$data_cad = date('Y-m-d H:i:s');
$status = $_REQUEST['status_aviso'];
$editar_titulo = $_REQUEST['editar_titulo'];
$editar_mensagem = $_REQUEST['editar_mensagem'];

$sql_editarAviso = "SELECT * FROM portal_avisos WHERE id_usuario = {$id_usuario} AND status = 1 ORDER BY data_cad DESC";
$sql_aviso = mysql_query($sql_editarAviso);

$sql_gestaoPortal = "UPDATE portal_avisos SET
     titulo_mensagem = '{$editar_titulo}', texto_mensagem = '{$editar_mensagem}', data_cad = '{$data_cad}', status = {$status} WHERE id_portal_avisos = {$id_portal} ";
$sql_aviso_update = mysql_query($sql_gestaoPortal);

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Relatório de Admissões");
$breadcrumb_pages = array("Visualizar Projeto" => "../rh/ver.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Gestão do Portal</title>
        
        <link href="../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">

    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Gestão do Portal</small></h2></div>
            
            <a href="gestao_portal.php"><div class="btn btn-warning" id="btn_msg">Nova Mensagem Geral</div></a>
                <!--<div class="btn btn-success" id="editar_aviso">Editar Mensagens <i class="fa fa-pencil-square"></i></div>-->
                <br>
                <br>
            
                
            <form action="" method="post" class="form-horizontal top-margin1" name="form_editar" id="form"> 
                <div class="panel panel-default" id="mensagem_portal_editar">
                    <?php while($value = mysql_fetch_array($sql_aviso)){
                    
                   if($value['status'] == 1){ ?>
                    
                    
                    <div class="panel-heading text-bold hidden-print">Editar Mensagem</div>
                    <div class="panel-body">
                        <!--<input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />-->
                        <div class="form-group hidden">
                            <label for="select" class="col-sm-offset-1 col-sm-1 control-label " >ID PORTAL AVISOS</label>
                            <div class="col-sm-4">
                                <input type="text" name="id_portal" id="titulo" value="<?php echo $value['id_portal_avisos'] ?>" class="form-control"/>
                                <?php // echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-sm-offset-1 col-sm-1 control-label " >Titulo</label>
                            <div class="col-sm-4">
                                <input type="text" name="editar_titulo" id="titulo" value="<?php echo $value['titulo_mensagem'] ?>" class="form-control" required="required" placeholder="Titulo da Mensagem" />
                                <?php // echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="select" class="col-sm-offset-1 col-sm-1 control-label ">Mensagem</label>
                            <div class="col-sm-4">
                                <textarea name="editar_mensagem" id="mensagem" value="" class="form-control" required="required" placeholder="Escreva a mensagem global para o Portal onde todos poderão ver!"><?php echo $value['texto_mensagem'] ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="select" class="col-sm-offset-1 col-sm-1 control-label ">Exibir Mensagem?</label>
                            <div class="col-sm-4">   
                                <select name="status_aviso" required="required" class="form-control">
                                    <option value="">Selecione</option>
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right hidden-print">
                        <input type="submit" name="enviar" id="editar" value="Enviar" class="btn btn-success" />
                    </div>
                    <?php }else{ echo "<p>Não há mensagens para ser editada!</p>"; } 
                    }?>
                </div>
                
            </form>
            
            
            <?php 
            
            if($_POST['status']){
                mysql_query($sql_gestaoPortal);
                echo "Mensagem enviada com sucesso!<br>";
            }
            ?>

            <?php include('../template/footer.php'); ?>
            <div class="clear"></div>
        </div>
        
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.ui.datepicker-pt-BR.js" type="text/javascript"></script>
        <script src="../js/jquery.form.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                
                $('.data').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '2005:c+1'
                });
                
                $("#btn_msg").click(function(){
                    $("#mensagem_portal").removeClass('hide');
                    $("#mensagem_portal_editar").addClass('hide');
                });
                $("#editar_aviso").click(function(){
                    $("#mensagem_portal_editar").removeClass('hide');
                    $("#mensagem_portal").addClass('hide');
                });
               
            });
        </script>
    
    </body>
</html>