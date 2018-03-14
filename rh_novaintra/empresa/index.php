<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/EmpresaClass.php');

$usuario = carregaUsuario();

$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

$result = getEmpresa($id_regiao);
$total_empresa = mysql_num_rows($result);

$filtro = false;

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) || (isset($_SESSION['voltarCurso']))) {
    $filtro = true;
    if(isset($_SESSION['voltarCurso'])){
        $_REQUEST['regiao'] = $_SESSION['voltarCurso']['id_regiao'];
        $_REQUEST['projeto'] = $_SESSION['voltarCurso']['id_projeto'];
        unset($_SESSION['voltarCurso']);
    }
    $result = getEmpresa($_REQUEST['regiao']);
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMUL�RIO SELECIONADO */
if(isset($_REQUEST['regiao'])){    
    $regiaoR = $_REQUEST['regiao'];
}elseif(isset($_SESSION['regiao'])){    
    $regiaoR = $_SESSION['regiao'];
}elseif(isset($_SESSION['regiao_select'])) {    
    $regiaoR = $_SESSION['regiao_select'];
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Empresas");
$breadcrumb_pages = array("Gest�o de RH" => "../../rh");

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Empresas</title>
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
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Empresas</small></h2></div>
                </div>
            </div>
            <?php if(!empty($_SESSION['MESSAGE'])){ ?>
                <!--resposta de algum metodo realizado-->
                <div id="message-box" class="alert alert-warning <?php echo $_SESSION['MESSAGE_COLOR']; ?> alinha2">
                    <?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?>
                </div>
            <?php } ?>
            <form action="" class="form-horizontal" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                <div class="panel panel-default">
                    <div class="panel-body">
                        <label class="col-xs-offset-1 col-xs-1 control-label">Regi�o:</label>
                        <div class="col-xs-9"><?php echo montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='form-control required[custom[select]]'") ?></div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="hidden" name="pausa" id="pausa" value="<?php echo $_SESSION['pausa']; ?>" />
                        <input type="hidden" name="volta" id="volta" value="<?php echo $_SESSION['regiao_select']; ?>" />
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoR ?>" />
                        <input type="hidden" name="hide_regiao" id="hide_regiao" value="<?php echo $regiaoR ?>" />
                        <input type="hidden" name="empresa" id="empresa" value="" />
                        <?php if ($filtro) { ?>
                            <?php if($_COOKIE['logado'] != 395) { ?><input type="submit" class="btn btn-success" value="Nova Empresa" name="novo" id="novaEmpresa" /><?php } ?>
                        <?php } ?>
                        <input type="submit" class="btn btn-primary" value="Filtrar" name="filtrar" id="filt" />
                    </div>
                </div>
            </form>
            <?php
            if ($filtro) {
                if ($total_empresa > 0) { ?>
                    <table class="table table-hover table-bordered table-condensed">
                        <thead>
                            <tr class="bg-primary valign-middle">
                                <th>C�d.</th>                                    
                                <th>Empresa</th>
                                <th>CNPJ</th>
                                <th>Respons�vel</th>
                                <th colspan="3" class="center">A��es</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php                            
                        while ($row = mysql_fetch_assoc($result)) {                                
                            $vinc_projeto = getRhProjeto($row['id_projeto']); ?>
                            <tr id="<?php echo $row['id_empresa']; ?>" class="valign-middle">
                                <td><?php echo $row['id_empresa']; ?></td>
                                <td><?php echo acentoMaiusculo($row['nome']); ?></td>
                                <td><?php echo $row['cnpj']; ?></td>
                                <td><?php echo strtoupper($row['responsavel']); ?></td>
                                <td class="center valign-middle">
                                    <!--img src="../../imagens/icones/icon-docview.gif" title="Visualizar" class="bt-image2" data-type="visualizar" data-key="<?php echo $row['id_empresa']; ?>" /-->
                                    <a class="btn btn-xs btn-primary" href="javascript:;"><i title="Visualizar" class="fa fa-search bt-image2" data-type="visualizar" data-key="<?=$row['id_empresa']?>"></i></a>
                                </td>
                                <?php if($_COOKIE['logado'] != 395) { ?>
                                <td class="center valign-middle">
                                    <!--img src="../../imagens/icones/icon-edit.gif" title="Editar" class="bt-image2" data-type="editar" data-key="<?php echo $row['id_empresa']; ?>" /-->
                                    <a class="btn btn-xs btn-warning" href="javascript:;"><i title="Editar" class="fa fa-pencil bt-image2" data-type="editar" data-key="<?=$row['id_empresa']?>"></i></a>
                                </td>
                                <td class="center valign-middle">
                                    <!--img src="../../imagens/icones/icon-delete.gif" title="Excluir" class="bt-image2" data-proj="<?php echo $vinc_projeto; ?>" data-type="excluir" data-key="<?php echo $row['id_empresa']; ?>" /-->
                                    <a class="btn btn-xs btn-danger" href="javascript:;"><i title="Excluir" class="fa fa-trash-o bt-image2" data-proj="<?=$vinc_projeto?>" data-type="excluir" data-key="<?=$row['id_empresa']?>"></i></a>
                                </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                    <br/>
                    <div id='message-box' class='alert alert-warning'>
                        <p>Nenhum registro encontrado</p>
                    </div>
                <?php }
            } ?>
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
        <script src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>
        <script src="../../uploadfy/scripts/swfobject.js" type="text/javascript"></script>
        <script src="../../jquery/priceFormat.js" type="text/javascript"></script>
        <script>
            $(function() {
                $(".bt-image2").on("click", function() {
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    var emp = $(this).parents("tr").find("td:first").next().html();
                    var proj = $(this).data("proj");
                    
                    if(action === "visualizar") {
                        $("#empresa").val(key);
                        $("#form1").attr('action','detalhes_empresa.php');
                        $("#form1").submit();
                    }else if(action === "editar"){
                        $("#empresa").val(key);
                        $("#form1").attr('action','form_empresa.php');
                        $("#form1").submit();
                    }else if(action === "excluir"){
                        
                        if(proj != 0){
                            thickBoxAlert("Exclus�o de Empresa", "Empresa n�o pode ser excluida, pois existe Projeto vinculado a mesma", 300, 130, null);
                        }else{
                            thickBoxConfirm("Exclus�o de Empresa", "Voc� deseja realmente excluir esta empresa?", 300, 200, function(data){
                                if(data){
                                    
                                    if(data == true){
                                        $("#"+key).remove();
                                        $.ajax({
                                            url:"del_empresa.php?id="+key
                                        });
                                    }
                                }
                            });
                        }
                    }
                });
                
                $("#novaEmpresa").click(function(){
                    $("#form1").attr('action','form_empresa.php');
                    $("#form1").submit();
                });
                
                //acao de clique ao voltar
//                var reg = $("#volta").val();  
//                var pausa = $("#pausa").val();
//                
//                if((reg != '') && (pausa == '')){
//                    $("#filt").click();
//                }
            });
        </script>
    </body>
</html>