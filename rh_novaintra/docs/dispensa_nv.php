<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://f71lagos.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/CltClassObj.php');

$usuario = carregaUsuario();
$objClt = new CltClassObj();

$id_clt = (!empty($_REQUEST['id_clt'])) ? $_REQUEST['id_clt'] : $_REQUEST['clt'];

$objClt->setDefault();
$objClt->setIdClt($id_clt);
if($objClt->select($id_clt)){
    $dadosClt = $objClt->getRow();
}
else{
    echo $objClt->getError();
    exit;
}

//$dadosClt = (object) mysql_fetch_assoc(mysql_query("SELECT * FROM rh_clt where id_clt = '$id_clt' LIMIT 1"));
//print_array($dadosClt);

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Dispensa");
$breadcrumb_pages = array("Gestão de RH" => "../");

$hoje = new DateTime(date("Y-m-d")); ?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Dispensa</title>
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
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>- Dispensa</small></h2></div>
                </div>
            </div>
            <form action="comunicado_de_dispensa.php" class="form-horizontal" method="post" name="form1" id="comunicado_de_dispensa_form" enctype="multipart/form-data" >
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tipo Rescisão</label>
                            <div class="col-sm-4">
                                <select name="tipo_rescisao" id="tipo_rescisao" class="data form-control validate[required]">
                                    <option value="">Selecione</option>
                                    <option value="991">Rescisão Sem Justa Causa</option>
                                    <option value="992">Rescisão Com Justa Causa</option>
                                    <option value="993">Rescisão Por Término de Contrato</option>
                                    <option value="994">Rescisão Por Término de Experiência</option>
                                    <option value="995">Pedido de Dispensa</option>
                                </select>
                            </div>
                            <label class="col-sm-2 control-label">Data do Demissão</label>
                            <div class="col-sm-4"><input type="text" name="data_demissao" id="data_demissao" class="data form-control validate[required]" value="<?=$hoje->modify('+30 day')->format('d/m/Y')?>"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label div_tipo_aviso">Aviso Prévio</label>
                            <div class="col-sm-4 div_tipo_aviso">
                                <select name="tipo_aviso" id="tipo_aviso" class="data form-control validate[required]">
                                    <option value="">Selecione</option>
                                    <option value="trabalhado">Trabalhado</option>
                                    <option value="indenizado">Indenizado</option>
                                </select>
                            </div>
                            <label class="col-sm-2 control-label">Data do Aviso</label>
                            <div class="col-sm-4"><input type="text" name="data_aviso" id="data_aviso" class="data form-control validate[required]" value="<?=$hoje->format('d/m/Y')?>"></div>
                        </div>
                        <div class="form-group div_justa_causa" style="display: none;">
                            <div class="col-sm-12">
                                <label class="control-label text-left">Motivo Justa Causa</label>
                                <textarea class="form-control validate[required]" id="motivo_justa_causa" name="motivo_justa_causa" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($objClt->getObservacao())) { ?>
                        <div class="panel-footer">
                            <div class="alert alert-warning">
                                <h4>Observações:</strong></h4>
                                <p><?=$objClt->getObservacao()?></p>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="panel-footer text-right">
                        <input type="submit" class="btn btn-primary" value="Enviar" name="enviar" id="filt" />
                        <input type="hidden" name="id_clt" id="id_clt" value="<?=$objClt->getIdClt()?>"/>
                    </div>
                </div>
            </form>
        <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.mask.min.js" type="text/javascript"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function() {
                $('#comunicado_de_dispensa_form').validationEngine();
                $("body").on("change", "#tipo_rescisao", function() {
                    $(".div_tipo_aviso, .div_justa_causa").hide();
                    $("#tipo_aviso, #motivo_justa_causa").removeClass("validate[required]");
                    if($(this).val() == '992'){
                        $("#tipo_aviso").val("");
                        $(".div_justa_causa").show();
                        $("#motivo_justa_causa").addClass("validate[required]");
                    } else if($(this).val() == '993' || $(this).val() == '994'){
                        $("#tipo_aviso").val("");
                    } else {
                        $(".div_tipo_aviso").show();
                        $("#tipo_aviso").addClass("validate[required]");
                    }
                });
            });
        </script>
    </body>
</html>