<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../classes/EstInstClass.php');
include("../../classes/LogClass.php");
include('../../funcoes.php');
include('../../wfunction.php');
require_once('../../classes/LogClass.php');

$log = new Log();
$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

//array de programadores
$func_f71 = array('255', '258', '256', '259', '260', '158', '257', '179');

$centrocusto = new EstInst();
$global = new GlobalClass();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Cadastro de Centro de Custo");
$breadcrumb_pages = array("Gestão de Centro de Custo" => "index.php");

$id = $_REQUEST['id'];

$row = $centrocusto->getEstInstId($id);

$regiao = $row['id_regiao'];

//insert
if(isset($_REQUEST['cadastrar']) && $_REQUEST['cadastrar'] == "Cadastrar"){
    $idInst = $centrocusto->cadEstInst();
    $log->log('2', "Instituição ID $idInst cadastrada",'instituicoes_estagiario');
    $regiao = $_REQUEST['regiao'];
}

if(isset($_REQUEST['atualizar']) && $_REQUEST['atualizar'] == "Atualizar"){
    $antigo = $log->getLinha('instituicoes_estagiario',$id);
    $centrocusto->editEstInst($id);
    $novo = $log->getLinha('instituicoes_estagiario',$id);
    $log->log('2',"Instituição de Estágio editada ID: $id",'instituicoes_estagiario',$antigo,$novo);
}

if($id == ""){
    $acao = 'Cadastro';
    $botao = 'Cadastrar';
}else{
    $acao = 'Edição';
    $botao = 'Atualizar';    
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Gestão de Instituições de Estágio</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
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
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Instituições de Estágio</small></h2></div>
                    
                    <form action="" method="post" id="form_mov" class="form-horizontal top-margin1" enctype="multipart/form-data">
                        <!--resposta de algum metodo realizado-->
                        <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>
                                                
                        <input type="hidden" id="id" name="id" value="<?php echo $id; ?>" />                        
                        <input type="hidden" name="home" id="home" value="" />
                        
                        <div class="panel panel-default">
                            <div class="panel-heading text-bold"><h4><?=$acao?> de Instituição de Estágio</h4></div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Nome:</label>
                                        <div class="col-lg-9">
                                            <input type="text" class="form-control validate[required]" id="nome" name="nome" value="<?php echo $row['nome']; ?>" />
                                        </div>
                                    </div>                                    
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <a href="index.php"class="btn btn-default"><span class="fa fa-reply"></span>&nbsp;&nbsp;Voltar</a>                                
                                <input type="submit" class="btn btn-primary botaoSubmit" name="<?php echo strtolower($botao); ?>" value="<?=$botao?>" />
                                <input type="hidden" name="<?=strtolower($botao)?>" id="<?=strtolower($botao)?>" value="<?=$botao?>" />
                            </div>
                        </div>
                    </form>

                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->

            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
                
        <script>
            $(function() {
                $("#form_mov").validationEngine({promptPosition : "topRight"});
            });
        </script>
    </body>
</html>