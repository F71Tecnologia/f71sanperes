<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/EduEventosClass.php');

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$eventosClass = new EduEventosClass();

$master = $usuario['id_master'];
$id_regiao = $usuario['id_regiao'];
$id_usuario = $_COOKIE['logado'];

$listaRegioes = getRegioes();

if ($_REQUEST['evento'] != '') {
    $evento = $_REQUEST['evento'];
} elseif ($_SESSION['evento'] != '') {
    $evento = $_SESSION['evento'];
}

//insert
if (isset($_REQUEST['cadastrar']) && $_REQUEST['cadastrar'] == "Cadastrar") {
    
    $dados['usuario'] = $usuario;
    $dados['id_regiao'] = $_REQUEST['regiao_evento'];
    $dados['id_usuario_cad'] = $dados['usuario']['id_funcionario'];
    $dados['data_cad'] = date('Y-m-d');
    $dados['nome'] = acentoMaiusculo($_REQUEST['nome']);
    $dados['data'] = ($_REQUEST['data'] != '') ? converteData($_REQUEST['data']) : '';
    
    $eventosClass->insereEvento($dados);
    
    $regiao_selecionada = $_SESSION['regiao'];
} else {
    $regiao_selecionada = $_REQUEST['hide_regiao'];
}

//update
if (isset($_REQUEST['atualizar']) && $_REQUEST['atualizar'] == "Atualizar") {
    $dados['usuario'] = $usuario;
    $dados['id_regiao'] = $_REQUEST['regiao_evento'];
    $dados['id_usuario'] = $usuario['id_funcionario'];
    $dados['nome'] = acentoMaiusculo($_REQUEST['nome']);
    $dados['data'] = ($_REQUEST['data'] != '') ? converteData($_REQUEST['data']) : '';
    $dados['id_evento'] = $_REQUEST['evento'];
    $eventosClass->editaEvento($dados);
}

$projeto_selecionado = $_REQUEST['hide_projeto'];
$regiao_edita = $row['id_regiao'];
$projeto_edita = $row['id_projeto'];

//trata insert/update
if ($evento == '') {
    $regiao = $regiao_selecionada;
    $acao = 'Cadastro';
    $botao = 'Cadastrar';
    $projeto = montaSelect(getProjetos($regiao), null, "id='projeto' name='projeto'");
} else {
    $regiao = $regiao_edita;
    $acao = 'Edição';
    $botao = 'Atualizar';
    $projeto = $row['id_projeto'] . " - " . $row['nome_projeto'];
}


//dados para voltar no index com select preenchido
$regiao_selecionada = $_REQUEST['hide_regiao'];
$projeto_selecionado = $_REQUEST['hide_projeto'];

if ($regiao_selecionada == '') {
    $_SESSION['regiao_select'];
    $_SESSION['projeto_select'];
    session_write_close();
} else {
    $_SESSION['regiao_select'] = $regiao_selecionada;
    $_SESSION['projeto_select'] = $projeto_selecionado;
    session_write_close();
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"52", "area"=>"Educacional", "id_form"=>"form1", "ativo"=>"$acao de Evento");
//$breadcrumb_pages = array("Evento"=>"index.php");

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: <?=$acao?> de Evento</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../../jquery/thickbox/thickbox.css" type="text/css" media="screen" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <form action="#" method="post" name="form1" id="form1" class="form-horizontal" autocomplete="off" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12">
                        <div class="page-header box-educacional-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - <?=$acao?> de Evento</small></h2></div>
                    </div>
                </div>
                <input type="hidden" id="evento" name="evento" value="<?php echo $row['id_evento']; ?>" />
                <!--resposta de algum metodo realizado-->
                <?php if (isset($_SESSION['MESSAGE'])) { ?>
                    <div class="alert <?=$_SESSION['MESSAGE_COLOR']?> alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <?php
                        switch ($_SESSION['MESSAGE_COLOR']) {
                            case 'alert-warning':
                            case 'alert-danger': echo '<i class="fa fa-warning"></i> '; break;
                            case 'alert-success':
                            case 'alert-info':
                            default: echo '<i class="fa fa-info-circle"></i> '; break;
                        }
                        echo $_SESSION['MESSAGE'];
                        session_destroy(); ?>
                    </div>
                <?php } ?>
                <div class="panel panel-default">
                    <div class="panel-body">
                            <div class="form-group">
                                <label for="nome" class="col-lg-2 control-label">Nome do Evento:</label>
                                <div class="col-lg-9">
                                    <input type="text" name="nome" id="nome" size="108" class="form-control validate[required]" value="<?php echo $row['nome']; ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="data" class="col-lg-2 control-label">Data:</label>
                                <div class="col-lg-4">
                                    <div class="input-group">
                                        <input type="text" name="data" id="data" size="30" value="<?php echo ($row['data'] != '') ? date('d/m/Y', strtotime($row['data'])) : ""; ?>" class="form-control data validate[required,custom[dateBr]]" />
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Região:</label>
                                <div class="col-lg-9">
                                    <?= montaSelect($listaRegioes, $row['id_regiao'], array('name' => 'regiao_evento', 'class' => 'form-control')) ?>
                                </div>
                            </div>
                    </div><!-- /.panel-body -->
                    <div class="panel-footer">
                        <div class="col-xs-6 text-left">
                            <a class="btn btn-default" id="voltar"  href="index.php" ><i class="fa fa-reply"></i> Voltar</a>
                        </div>
                        <div class="col-xs-6 text-right">
                            <input type="submit" class="btn btn-primary" name="<?=strtolower($botao)?>" id="<?=strtolower($botao)?>" value="<?=$botao?>" />
                        </div>
                        <div class="clear"></div>
                    </div>
                </div><!-- /.painel-default -->
            </form>
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput.min.js" type="text/javascript" ></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/main.js" type="text/javascript"></script>

        <script src="../../resources/js/edueventos/form_evento_novo.js"></script>

    </body>
</html>