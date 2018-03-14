<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../conn.php');
include('../classes/global.php');
include('../wfunction.php');
include('../classes/cooperativa.php');

$usuario = carregaUsuario();
$filtro = false;

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) || (isset($_SESSION['voltarCooperativa']))) {
    $filtro = true;
    if (isset($_SESSION['voltarCooperativa'])) {
        $_REQUEST['regiao'] = $_SESSION['voltarCooperativa']['id_regiao'];
        $_REQUEST['projeto'] = $_SESSION['voltarCooperativa']['id_projeto'];
        unset($_SESSION['voltarCooperativa']);
    }
    $rs = montaQuery("cooperativas", "id_coop,tipo,nome,tel,contato,taxa", "id_regiao = {$_REQUEST['regiao']} AND status_reg = 1", NULL, null, null, false);
    $num_rows = mysql_num_rows($rs);
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$regiaoR = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Cooperativas");
$breadcrumb_pages = array("Gestão de RH"=>"../");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Cooperativas</title>
        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Cooperativas</small></h2></div>
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
            <div class="row">
                <div class="col-md-12">
                    <form id="form1" class="form-horizontal" method="post">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <label class="control-label col-md-1">Região:</label>
                                <div class="col-md-11">
                                    <?=montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='form-control required[custom[select]]'")?>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?=$projetoR?>" />
                                <input type="hidden" name="home" id="home" value="">
                                <input type="hidden" name="caminho" id="caminho" />
                                <input type="hidden" name="cooperativa" id="cooperativa" value="" />
                                <button type="submit" class="btn btn-primary" name="filtrar" value="Filtrar" />Filtrar</button>
                                <?php if ($filtro) { ?>
                                    <button type="submit" class="btn btn-success" value="Nova Cooperativa" name="novo" id="novoPrest" /><span class="fa fa-plus"></span> Nova Cooperativa</button>
                                <?php } ?>
                            </div>
                        </div>
                    </form>
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
            <?php if ($filtro) {
                if ($num_rows > 0) { ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <h2><button type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" class="btn btn-success pull-right"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button></h2>
                            <table id="tbRelatorio" class="table table-striped table-condensed table-hover">
                                <thead>
                                    <tr>
                                        <th width="4%" class="text-center">#</th>
                                        <th width="9%" class="text-center">TIPO</th>
                                        <th width="50%" class="text-center">NOME</th>
                                        <th width="11%" class="text-center">TEL</th>
                                        <th width="15%" class="text-center">CONTATO</th>
                                        <th width="5%" class="text-center">TAXA</th>
                                        <th width="6%" colspan="3" class="text-center">AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysql_fetch_assoc($rs)) { ?>
                                        <tr>
                                            <td class="center"><?=$row['id_coop']?></td>
                                            <td class="center"><?=($row['tipo'] == 1) ? 'Cooperativa' : 'Pessoa Jurídica'?></td>
                                            <td class="center"><?=$row['nome']?></td>
                                            <td class="center"><?=$row['tel']?></td>
                                            <td class="center"><?=$row['contato']?></td>
                                            <td class="center"><?=$row['taxa']?></td>
                                            <td class="center"><img src="../imagens/icones/icon-doc.gif" title="Ver Cooperativa" class="bt-image" data-type="cooperativa" data-key="<?=$row['id_coop']?>" /></td>
                                            <td class="center"><img src="../imagens/icones/icon-edit.gif" title="Editar" class="bt-image" data-type="editar" data-key="<?=$row['id_coop']?>" /></td>
                                            <td class="center"><img src="../imagens/icones/icon-trash.gif" title="Excluir" class="bt-image" data-type="excluir" data-key="<?=$row['id_coop']?>" /></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div><!-- /.col-lg-12 -->
                    </div><!-- /.row -->
                <?php } else { ?>
                    <div class="alert alert-dismissable alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        Nenhum registro encontrado!
                    </div>
                <?php }
            } ?>
            <?php include_once ('../template/footer.php'); ?>
        </div>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        
        <script src="../js/jquery.maskedinput-1.3.1.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                $(".bt-image").on("click", function() {
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    var emp = $(this).parents("tr").find("td:first").next().html();
                    var botao = $(this);

                    //THICKBOX VISUALIZA DOCUMENTOS
                    if (action === "excluir") {
                        if (confirm("Tem certaza que deseja excluir esta cooperativa ou PJ?")) {
                            $.post('action.php', {id_coop: key, action: 'excluir'}, function(data) {
                                alert(data);
                                botao.closest("tr").remove();
                            });
                        }
                    } else if (action === "cooperativa") {
                        $("#cooperativa").val(key);
                        $("#form1").attr('action', 'ver_cooperativa2.php');
                        $("#form1").submit();
                    } else if (action === "editar") {
                        $("#cooperativa").val(key);
                        $("#caminho").val(1);
                        $("#form1").attr('action', 'form_cooperativa2.php');
                        $("#form1").submit();
                    }
                });

                $("#novoPrest").click(function() {
                    $("#form1").attr('action', 'form_cooperativa2.php');
                    $("#caminho").val(0);
                    $("#form1").submit();
                });
            });
        </script>
    </body>
</html>