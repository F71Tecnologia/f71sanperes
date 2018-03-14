<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/EduEventosClass.php');

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$id_regiao = $usuario['id_regiao'];

$eventosClass = new EduEventosClass();

$row_eventos = $eventosClass->listEventoAll();
//$total_feriado = mysql_num_rows($result);

//VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO
if (isset($_REQUEST['regiao'])) {
    $regiaoR = $_REQUEST['regiao'];
} elseif (isset($_SESSION['regiao'])) {
    $regiaoR = $_SESSION['regiao'];
} elseif (isset($_SESSION['regiao_select'])) {
    $regiaoR = $_SESSION['regiao_select'];
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"52", "area"=>"Educacional", "id_form"=>"form1", "ativo"=>"Eventos");
//$breadcrumb_pages = array("Gestão de RH" => "../../rh");

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Eventos</title>
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
            <form name="form" action="" method="post" id="form1" class="form-horizontal">
                <div class="row">
                    <div class="col-md-12">
                        <div class="page-header box-educacional-header"><h2><span class="fa fa-graduation-cap"></span> - EDUCACIONAL<small> - Eventos</small></h2></div>
                    </div>
                </div>
                <input type="hidden" id="evento" name="evento" value="" />
                <div class="panel panel-default">
                    <div class="panel-body text-right">
                        <button type="submit" class="btn btn-success" name="novo" id="novoEvento" ><i class="fa fa-plus"></i> Cadastrar Evento</button>
                    </div>
                </div>
                <?php if (count($row_eventos) > 0) { ?>
                    <br/>
                    <table class="table table-striped table-hover table-bordered table-condensed">
                        <thead>
                            <tr class="valign-middle">
                                <th class="text-center">Cód.</th>
                                <th class="text-center">Data</th>
                                <th class="text-center">Nome</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($row_eventos as $row) {
                                ?>
                                <tr class="valign-middle" id="<?=$row['id_evento']; ?>">
                                    <td class="text-center"><?=$row['id_evento']; ?></td>
                                    <td class="text-center"><?=$row['data'] = implode("/", array_reverse(explode("-", $row['data'])));?></td>
                                    <td><?=acentoMaiusculo($row['nome']); ?></td>
                                    <td class="center">
                                        <i class="bt-image2 btn btn-xs btn-warning fa fa-pencil" title="Editar" data-type="editar" data-key="<?=$row['id_eventoo']?>" alt=""></i>
                                        <i class="bt-image2 btn btn-xs btn-danger fa fa-trash-o" title="Excluir" data-type="excluir" data-key="<?=$row['id_evento']?>" alt=""></i>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <br/>
                    <div id='message-box' class='alert alert-danger'>
                        <p>Nenhum registro encontrado</p>
                    </div>
                <?php } ?>
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
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js" type="text/javascript"></script>

        <script src="../../resources/js/edueventos/index2.js"></script>

    </body>
</html>