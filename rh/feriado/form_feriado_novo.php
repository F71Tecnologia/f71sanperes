<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/FeriadosClass.php');

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$objFeriado = new FeriadosClass();

$master = $usuario['id_master'];
$id_regiao = $usuario['id_regiao'];
$id_usuario = $_COOKIE['logado'];

$listaRegioes = getRegioes();

if ($_REQUEST['feriado'] != '') {
    $feriado = $_REQUEST['feriado'];
} elseif ($_SESSION['feriado'] != '') {
    $feriado = $_SESSION['feriado'];
}

$row = $objFeriado->getFeriadoID($feriado);

//insert
if (isset($_REQUEST['cadastrar']) && $_REQUEST['cadastrar'] == "Cadastrar") {
    $dados['usuario'] = $usuario;
    $dados['id_regiao'] = $_REQUEST['regiao_feriado'];
    $dados['id_usuario'] = $dados['usuario']['id_funcionario'];
    $dados['data_cad'] = date('Y-m-d');
    $dados['nome_feriado'] = acentoMaiusculo($_REQUEST['nome_feriado']);
    $dados['data_feriado'] = ($_REQUEST['data_feriado'] != '') ? converteData($_REQUEST['data_feriado']) : '';
    $dados['tipo'] = $_REQUEST['tipo_feriado'];
    $dados['movel'] = $_REQUEST['movel'];

    $objFeriado->cadFeriado($dados);
    $regiao_selecionada = $_SESSION['regiao'];
} else {
    $regiao_selecionada = $_REQUEST['hide_regiao'];
}

//update
if (isset($_REQUEST['atualizar']) && $_REQUEST['atualizar'] == "Atualizar") {
    $dados['usuario'] = $usuario;
    $dados['id_regiao'] = $_REQUEST['regiao_feriado'];
    $dados['id_usuario'] = $usuario['id_funcionario'];
    $dados['nome_feriado'] = acentoMaiusculo($_REQUEST['nome_feriado']);
    $dados['data_feriado'] = ($_REQUEST['data_feriado'] != '') ? converteData($_REQUEST['data_feriado']) : '';
    $dados['tipo'] = $_REQUEST['tipo_feriado'];
    $dados['movel'] = $_REQUEST['movel'];
    $dados['id_feriado'] = $_REQUEST['feriado'];
    $objFeriado->alteraFeriado($dados);
}

$projeto_selecionado = $_REQUEST['hide_projeto'];
$regiao_edita = $row['id_regiao'];
$projeto_edita = $row['id_projeto'];

//trata insert/update
if ($feriado == '') {
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

//trazer todos os ufs
$qr_uf = mysql_query("SELECT * FROM uf");

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
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <title>:: Intranet :: Feriado</title>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="shortcut icon" href="../../favicon.ico" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css">

        <style>
            /*            .ui-datepicker {
                            font-size: 12px;
                        }*/
        </style>

    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">

                <div class="col-lg-12">
                    <form action="#" method="post" name="form1" id="form1" class="form-horizontal" autocomplete="off" enctype="multipart/form-data">
                        <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS</h2></div>

                        <input type="hidden" id="feriado" name="feriado" value="<?php echo $row['id_feriado']; ?>" />

                        <!--resposta de algum metodo realizado-->
                        <?php if (isset($_SESSION['MESSAGE'])) { ?>
                            <div class="alert <?php echo $_SESSION['MESSAGE_COLOR']; ?> alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                <?php
                                switch ($_SESSION['MESSAGE_COLOR']) {
                                    case 'alert-warning':
                                    case 'alert-danger':
                                        echo '<i class="fa fa-warning"></i> ';
                                        break;
                                    case 'alert-success':
                                    case 'alert-info':
                                    default:
                                        echo '<i class="fa fa-info-circle"></i> ';
                                        break;
                                }
                                echo $_SESSION['MESSAGE'];
                                session_destroy();
                                ?>
                            </div>
                        <?php } ?>

                        <div class="form_funcoes">

                            <fieldset id="func1">
                                <legend><?php echo $acao; ?> de Feriado</legend>
                                <div class="panel panel-default">
                                    <div class="panel-body">

                                        <div class="row">
                                            <div class="form-group">
                                                <label for="nome_feriado" class="col-lg-2 control-label">Nome do Feriado:</label>
                                                <div class="col-lg-9">
                                                    <input type="text" name="nome_feriado" id="nome_feriado" size="108" class="form-control validate[required]" value="<?php echo $row['nome']; ?>" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group">
                                                <label for="data_feriado" class="col-lg-2 control-label">Data:</label>
                                                <div class="col-lg-4">
                                                    <div class="input-group">
                                                        <input type="text" name="data_feriado" id="data_feriado" size="30" value="<?php echo ($row['data'] != '') ? date('d/m/Y', strtotime($row['data'])) : ""; ?>" class="form-control data validate[required,custom[dateBr]]" />
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group">
                                                <label class="col-lg-2 control-label">Tipo:</label>
                                                <div class="col-lg-9">
                                                    <div class="radio">
                                                        <label for="tipo_feriado1">
                                                            <input type="radio" name="tipo_feriado" id="tipo_feriado1" value="Federal" <?= ($row['tipo'] == 'Federal') ? "checked" : '' ?> /> Federal
                                                        </label>
                                                    </div>
                                                    <div class="radio">
                                                        <label for="tipo_feriado2">
                                                            <input type="radio" name="tipo_feriado" id="tipo_feriado2" value="Regional" <?= ($row['tipo'] == 'Regional') ? "checked" : "" ?> /> Regional
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group">
                                                <label for="movel" class="col-lg-2 control-label">Festa móvel:</label>
                                                <div class="col-lg-9">
                                                    <div class="checkbox">
                                                        <label for="movel">
                                                            <input type="checkbox" name="movel" id="movel" value="1" <?php echo ($row['movel'] == '1') ? 'checked' : ''; ?> />
                                                            Marque se o feriádo for móvel
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group">
                                                <label class="col-lg-2 control-label">Região:</label>
                                                <div class="col-lg-9">
                                                    <?= montaSelect($listaRegioes, $row['id_regiao'], array('name' => 'regiao_feriado', 'class' => 'form-control')) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- /.panel-body -->
                                    <div class="panel-footer text-right">
                                        <input type="submit" class="btn btn-primary" name="<?php echo strtolower($botao); ?>" id="<?php echo strtolower($botao); ?>" value="<?php echo $botao; ?>" />

                                    </div>
                                </div><!-- /.painel-default -->
                            </fieldset>

                            <a class="btn btn-default" id="voltar"  href="index2.php" ><i class="fa fa-reply"></i> Voltar</a>

                        </div><!--form_funcoes-->                             

                    </form>
                </div><!-- /.col-lg-12 -->

            </div><!-- /.row -->
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

        <script src="../../resources/js/rh/feriados/form_feriado_novo.js"></script>

    </body>
</html>