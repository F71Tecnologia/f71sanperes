<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../../conn.php');
include('../../../wfunction.php');
include('../../../classes/global.php');
include('../../../classes/PrestadorServicoClass.php');

$usuario = carregaUsuario();
$master = $usuario['id_master'];
$id_regiao = $usuario['id_regiao'];
$id_usuario = $_COOKIE['logado'];

$objPrestador = new PrestadorServico();

$id_layout = $_REQUEST['layout'];

$arrTiposServicos = $objPrestador->getListaServicos();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

if(!empty($id_layout)){
    $res = $objPrestador->getLayoutContratos($id_layout);
    
    $tipo = "Edição";
    $btn = "Atualizar";
}else{
    $tipo = "Cadastro";
    $btn = "Cadastrar";
}

$campos = $objPrestador->getCamposContrato();

$breadcrumb_config = array("nivel" => "../../../", "key_btn" => "2", "area" => "Administrativo", "id_form" => "form1", "ativo" => $tipo." de Layout");
$breadcrumb_pages = array("Principal" => "../../../admin/index.php", "Layouts de contratos"=>"index.php");

if (isset($_REQUEST['cadastrar'])) {
    $action = $objPrestador->cadLayoutContrato();
}

if (isset($_REQUEST['atualizar'])) {
    $action = $objPrestador->editLayoutContrato($_REQUEST['layout']);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: <?php echo $tipo; ?> de Layout</title>
        <link href="../../../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../../js/autocomplete/chosen.jquery.css" rel="stylesheet" type="text/css">
        <!--link href="../../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->

        <link href="../../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="jquery.autocomplete.css" rel="stylesheet" type="text/css" />  
        <style>
            .cooperado, #p_valor_hora{
                display: none;
            }
            .some_insa, #hide_noturno, #del_hor {
                display: none;
            }
            .chosen-container{
                width: 100%;
            }
            .cke_panel
            {                
                width: auto !important;
            }
        </style>
    </head>
    <body>
        <?php include("../../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">                    
                    <div class="page-header box-admin-header"><h2><span class="glyphicon glyphicon-cog"></span> - ADMINISTRATIVO<small> - <?php echo $tipo; ?> de Layout</small></h2></div>
                    
                    <?php if(!empty($_SESSION['MESSAGE'])){ ?>
                        <div id="message-box" class="alert alert-dismissable alert-<?= $_SESSION['MESSAGE_COLOR']; ?> alinha2">
                            <?= $_SESSION['MESSAGE']; session_destroy(); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="row">
                <form action="" class="form-horizontal" method="post" name="form1" id="form1" autocomplete="off">
                    <input type="hidden" name="layout" id="layout" value="<?php echo $id_layout; ?>" />
                    
                    <div class="col-xs-12 form_funcoes">
                        <div class="panel panel-default">
                            <?php if (!isset($_REQUEST['atualizar'])) { ?>
                            <div class="panel-heading">Dados do Layout</div>
                            <div class="panel-body">
                                <fieldset id="func1">                                    
                                    <div class="form-group">
                                        <label for="nome" class="col-xs-2 control-label">Nome do Contrato:</label>
                                        <div class="col-xs-10">
                                            <input type="text" name="nome_contrato" id="nome_contrato" class="form-control validate[required]" value="<?php echo $res['nome']; ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-xs-2 control-label">Tipo de serviço:</label>
                                        <div class="col-xs-10">
                                            <?= montaSelect($arrTiposServicos, $res['id_cnae'], 'name="tipo_servico" id="tipo_servico" class="chosen-select-deselect form-control validate[required,custom[select]] setor"'); ?>
                                        </div>
                                    </div>                                    
                                    <div class="form-group">
                                        <label for="nome" class="col-xs-2 control-label">Conteúdo:</label>
                                        <div class="col-xs-10">
                                            <textarea name="editor1" id="editor1" class="form-control ckeditor"><?php echo $res['conteudo'] ?></textarea>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <?php } ?>
                            <div class="panel-footer text-right">
                                <?php if (!isset($_REQUEST['atualizar'])) { ?>
                                <input type="submit" class="btn btn-primary" name="<?php echo strtolower($btn); ?>" id="<?php echo strtolower($btn); ?>" value="<?php echo $btn; ?>" />
                                <a class="btn btn-default" href="index.php"><i class="fa fa-reply"></i> Voltar</a>
                                <?php }else{ ?>
                                <a class="btn btn-default" href="index.php"><i class="fa fa-reply"></i> Voltar</a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        <?php include_once '../../../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../../../js/jquery-1.10.2.min.js"></script>
        <script src="../../../resources/js/bootstrap.min.js"></script>
        <script src="../../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../../resources/js/main.js"></script>
        <script src="../../../js/global.js"></script>

        <script src="../../../js/jquery.price_format.2.0.min.js" type="text/javascript"></script>
        <script src="../../../js/jquery.maskedinput-1.3.1.js" type="text/javascript"></script>
        <script src="../../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../../js/autocomplete/chosen.jquery.js" type="text/javascript"></script>
        <script src="../../../js/ckeditor/ckeditor.js"></script>
        <script>
            $(function () {                
                //validation engine
                $("#form1").validationEngine({promptPosition: "topRight"});              
                
                //AUTOCOMPLETE
                if($(".chosen-select").length){$('.chosen-select').chosen();}
                if($(".chosen-select-deselect").length){$('.chosen-select-deselect').chosen({ allow_single_deselect: true });}
                
                CKEDITOR.replace('editor1',{
                    placeholder_select: {
//                        placeholders: [
//                            'Contratante',
//                            'CNPJ do Contratante',
//                            'Endereço do Contratante',
//                            'Bairro do Contratante',
//                            'Cidade do Contratante',
//                            'Estado do Contratante'
//                        ]
                        placeholders: [<?php echo join($campos, ','); ?>]
                    }
                });
            });
        </script>
    </body>
</html>
