<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/FeriadosClass.php');

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$id_regiao = $usuario['id_regiao'];

$objFeriado = new FeriadosClass();


$feriados = $objFeriado->getFeriadoAll();
//$total_feriado = mysql_num_rows($result);

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
if (isset($_REQUEST['regiao'])) {
    $regiaoR = $_REQUEST['regiao'];
} elseif (isset($_SESSION['regiao'])) {
    $regiaoR = $_SESSION['regiao'];
} elseif (isset($_SESSION['regiao_select'])) {
    $regiaoR = $_SESSION['regiao_select'];
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Feriados");
$breadcrumb_pages = array("Gestão de RH" => "../../rh");

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>:: Intranet :: Feriados</title>
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
                <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Feriados</small></h2></div>
            </div>
        </div>
        <?php if (isset($_SESSION['MESSAGE'])) { ?>
            <div class="alert alert-warning <?=$_SESSION['MESSAGE_COLOR'] ?> alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <?=$_SESSION['MESSAGE']; session_destroy(); ?>
            </div>
        <?php } ?>
        <input type="hidden" id="feriado" name="feriado" value="" />
        <?php if($_COOKIE['logado'] != 395) { ?>
            <div class="panel panel-default">
                <div class="panel-body text-right">
                    <button type="submit" class="btn btn-success" name="novo" id="novoFeriado" ><i class="fa fa-plus"></i> Novo Feriado</button>
                </div>
            </div>
        <?php } ?>
        <?php if (count($feriados) > 0) { ?>
            <br/>
            <table class="table table-striped table-hover table-bordered table-condensed">
                <thead>
                <tr class="bg-primary valign-middle">
                    <th class="text-center">Cód.</th>
                    <th class="text-center">Data</th>
                    <th>Nome</th>
                    <th class="text-center">Tipo</th>
                    <th class="text-center">Móvel</th>
                    <th>Região</th>
                    <?php if($_COOKIE['logado'] != 395) { ?><th class="text-center">Ações</th><?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($feriados as $row) { ?>
                    <tr class="valign-middle" id="<?=$row['id_feriado']; ?>">
                        <td class="text-center"><?=$row['id_feriado']; ?></td>
                        <td class="text-center"><?=$row['data_m']; ?></td>
                        <td><?=acentoMaiusculo($row['nome']); ?></td>
                        <td class="text-center"><?=$row['tipo']; ?></td>
                        <td class="text-center"><?=($row['movel'] == 0) ? $movel = 'Não' : $movel = 'Sim'; ?></td>
                        <td><?=($row['nome_regiao'] != '') ? $regiao_f = $row['nome_regiao'] : $regiao_f = 'Federal'; ?></td>
                        <?php if($_COOKIE['logado'] != 395) { ?>
                            <td class="center">
                                <i class="bt-image2 btn btn-xs btn-warning fa fa-pencil" title="Editar" data-type="editar" data-key="<?=$row['id_feriado']?>" alt=""></i>
                                <i class="bt-image2 btn btn-xs btn-danger fa fa-trash-o" title="Excluir" data-type="excluir" data-key="<?=$row['id_feriado']?>" alt=""></i>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <br/>
            <div id='message-box' class='message-yellow'>
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

<script src="../../resources/js/rh/feriados/index2.js"></script>

</body>
</html>