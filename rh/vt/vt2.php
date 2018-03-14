<?php
$id_clt = $_REQUEST['clt'];
$id_user = $_COOKIE['logado'];
$id_pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['id_reg'];

include ("../../conn.php");
include("../../wfunction.php");

$usuario = carregaUsuario();

$result_rh_status_doc = mysql_query("SELECT * FROM rh_doc_status WHERE tipo='14' AND id_clt='$id_clt' AND status_reg=1");
$row_status_doc = mysql_fetch_array($result_rh_status_doc);

$row_regiao_id = mysql_result(mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$id_reg'"),0);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao_id'");
$row_master = mysql_fetch_assoc($qr_master);

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Vale Transporte");
$breadcrumb_pages = array(
    "Lista Projetos" => "../../ver2.php", 
    "Visualizar Projeto" => "../../ver2.php?projeto={$id_pro}",
    "Lista Participantes" => "../../bolsista2.php?projeto={$id_pro}",
    "Visualizar Participante" => "../ver_clt2.php?pro={$id_pro}&clt={$id_clt}");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Vale Transporte</title>
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
    </head>
    <body>
    <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Vale Transporte</small></h2></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <a class="btn btn-default col-md-12" href="solicita.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$id_clt?>" target="_blank">Solicitação de Vale</a>
                    </div>
                    <div class="col-md-4">
                        <a class="btn btn-default col-md-12" href="dispensa.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$id_clt?>" target="_blank">Dispensa de Vale</a>
                    </div>
                    <?php
                    $status_solicitacao = $row_status_doc;
                    //Caso exista uma solicitaÃ§Ã£o de vale criada, habilita ou desabilita os botÃµes "Dispensa de Vale" e "Recibo Individual".
                    if($status_solicitacao == FALSE){
                        $statusBotao = 'none';
                    } else {
                        $statusBotao = 'inline';	
                    } ?>
                    <div class="col-md-4">
                        <a class="btn btn-default col-md-12" href="mes_recibo.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$id_clt?>" target="_blank" style="display:<?=$statusBotao?>"> Recibo Individual</a>
                    </div>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
    </body>
</html>