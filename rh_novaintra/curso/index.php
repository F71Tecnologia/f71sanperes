<?php
session_start();
if (!isset($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/FuncoesClass.php');

$usuario = carregaUsuario();

if(isset($_REQUEST['id_departamento']) && !empty($_REQUEST['id_departamento']) && $_REQUEST['id_departamento'] != 0){
    $updateDepartamento = "UPDATE curso SET id_departamento = '{$_REQUEST['id_departamento']}' WHERE id_curso = '{$_REQUEST['id_curso']}' LIMIT 1;";
    $updateDepartamento = mysql_query($updateDepartamento);
    echo $_REQUEST['id_curso'].' - '.$_REQUEST['id_departamento'];
    exit;
}
$exibeColunaDepartamento = false;
$result = FuncoesClass::getCursos(false, false);
$total_curso = mysql_num_rows($result);

$sql_departamento = "SELECT * FROM departamentos ORDER BY nome";
$sql_departamento = mysql_query($sql_departamento);
$arrayDepartamentos[0] = 'Selecione';
while($row_departamento = mysql_fetch_assoc($sql_departamento)){
    $arrayDepartamentos[$row_departamento['id_departamento']] = $row_departamento['nome'];
}

// CARREGA SALÁRIO MÍNIMO
$valorSalMin = montaQuery("rh_movimentos", "*", "cod = 0001 AND anobase = YEAR(NOW())", null, null, 'array', false);

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Gestão de Funções");
//$breadcrumb_pages = array("Gestão de RH"=>"../../");

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Gestão de Funções</title>
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
        <style>
            .modal-header {
                background-color: #5bc0de;
                color: #fff;
            }
        </style>
    </head>
    <body>
    <?php include("../../template/navbar_default.php"); ?>
        <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" class="form-horizontal">
            <input type="hidden" name="logado" id="logado" value="<?= $_COOKIE['logado'] ?>" />
            <input type="hidden" name="curso" id="curso" value="" />
            <input type="hidden" name="form" id="form" value="" />
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Gestão de Funções</small></h2></div>
                    </div>
                </div>
                <!--resposta de algum metodo realizado-->
                <?php if(!empty($_SESSION['MESSAGE'])){ ?>
                    <div id="message-box" class="alert alert-dismissable alert-warning alinha2">
                        <?=$_SESSION['MESSAGE']; session_destroy(); ?>
                    </div>
                <?php } ?>
                <div class="row">
                    <div class="col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-footer text-right">
                                    <button type="submit" class="btn btn-success" value="cadastro" id="novaFuncao">Nova Função</button>
                                </div>
                            </div>
                    </div>
                </div>
                <?php
                if ($total_curso > 0) { ?>
                    <div class="row" style="padding: 0 16px 0 16px">
                        <button type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" class="btn btn-success pull-right"><span class="fa fa-file-excel-o"></span>&nbsp;&nbsp;Exportar para Excel</button>
                    </div>
                    <div class="table-responsive">
                    <table id="tbRelatorio" class="table table-striped table-hover table-bordered table-condensed">
                        <thead>
                            <tr class="bg-primary valign-middle">
                                <th width="5%">Cód.</th>
                                <th width="5%">Vínculos</th>
                                <th width="50%">Função</th>
                                <?php if($exibeColunaDepartamento){?><th width="10%">Departamento</th><?php } ?>
                                <th width="7%">CBO</th>
                                <th width="13%">Valor</th>
                                <th width="5%">Máximo</th>                                    
                                <th width="5%" colspan="4" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $contratacao = "";
                        while ($row = mysql_fetch_assoc($result)) {
                            $departamento = '';
                            //trata qtd de vinculos
                            if($row['tipo'] == 2){
                                $qtd_vinculos = FuncoesClass::getRhClt($row['id_curso']);
                                if($row['id_departamento'] == 0){
                                    $departamento = montaSelect($arrayDepartamentos, $value, 'name="departamento" class="departamento form-control no-padding-hr" data-curso="'.$row['id_curso'].'"');
                                    $departamento .= '<span class="nomeDepartamento"></span>';
                                } else {
                                    $departamento = $arrayDepartamentos[$row['id_departamento']];
                                }
                            }elseif(($row['tipo'] == 1) || ($row['tipo'] == 3)){
                                $qtd_vinculos = FuncoesClass::getAutonomo($row['id_curso']);
                            }

                            if($contratacao != $row['tipo_contratacao_nome']){
                                $contratacao = $row['tipo_contratacao_nome'];
                                echo "<tr class='tr_contratacao info'><th colspan='10'>".ucwords($row['tipo_contratacao_nome'])."</th></tr>";
                            } ?>
                            <tr id="<?=$row['id_curso']; ?>" class="valign-middle">
                                <td class="text-center"><?=$row['id_curso']; ?></td>
                                <td class="text-center"><?=$qtd_vinculos; ?></td>
                                <td><?=$row['nome']; ?></td>
                                <?php if($exibeColunaDepartamento){?><td><?=$departamento; ?></td><?php }?>
                                <td><?=$row['cod']; ?></td>
                                <td><?=formataMoeda($row['salario']); ?></td>
                                <td class="text-center"><?=$row['qnt_maxima']; ?></td>                                                                                
                                <td class="text-center"><button type="button" class="btn btn-xs btn-primary acao" value="visualizacao" data-curso="<?=$row['id_curso']; ?>" title="Visualizar Detalhes"><span title="Visualizar" class="bt-image fa fa-search"></span></button></td>
                                <td class="text-center"><button type="button" class="btn btn-xs btn-warning acao" value="edicao" data-curso="<?=$row['id_curso']; ?>" title="Editar Função"><span title="Editar" class="bt-image fa fa-pencil"></span></button></td>
                                <td class="text-center"><button type="button" class="btn btn-xs btn-info acao" value="preclonagem" data-curso="<?=$row['id_curso']; ?>" data-toggle="modal" data-target="#myModal" title="Duplicar Função"><span title="Clonar" class="bt-image fa fa-clone"></span></button></td>
                                <td class="text-center"><button type="button" class="btn btn-xs btn-danger acao" value="exclusao" data-curso="<?=$row['id_curso']; ?>" data-qtd="<?=$qtd_vinculos; ?>" title="Excluir Função"><span title="Excluir" class="bt-image fa fa-trash-o"></span></button></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    </div>
                <?php } else { ?>
                    <div class="alert alert-dismissable alert-warning">
                        <p>Nenhum registro encontrado</p>
                    </div>
                <?php } ?>
                <?php include_once '../../template/footer.php'; ?>
            </div>
            <div class="modal fade" id="myModal" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="bootstrap-dialog-title">Duplicar Função</div>
                        </div>
                        <div class="modal-body">
                            <div class="bootstrap-dialog-body"><div class="bootstrap-dialog-message">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <div class="col-xs-12">
                                                    <label class="control-label">Nome</label>
                                                    <input type="text" name="nome" id="nome" class="form-control validate[required]" value="<?= $curso['nome'] ?>" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-xs-12">
                                                    <label class="control-label">Salário</label>
                                                    <input type="text" name="salario" id="salario" class="form-control decimal validate[required,min[937.00]]" maxlength="14" placeholder="0,00">
                                                </div>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="display: block;">
                            <div class="bootstrap-dialog-footer">
                                <div class="bootstrap-dialog-footer-buttons">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-success acao" id="duplicar" data-curso="<?=$row['id_curso']; ?>" value="clonagem">Duplicar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskMoney.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function() {
                $('.decimal').maskMoney({allowNegative: false, thousands: '.', decimal: ','});
                $("body").on('click', '.acao', function() {
                    var action = $(this).val();
                    var curso = $(this).data("curso");
                    $(this).closest("form").find("#curso").val(curso);
                    if(curso.length === 0 || curso < 1) {
                        bootAlert("ID da função não encontrado. Contate um administrador.", "ERRO", null, 'warning');
                    }
                    else {
                        if (action === "visualizacao") {
                            $("#form1").attr('action', 'detalhes_curso.php');
                            $("#form").val(action);
                            $("#form1").submit();
                        } else if (action === "edicao") {
                            $("#form1").attr('action', 'form_curso.php');
                            $("#form").val(action);
                            $("#form1").submit();
                        } else if (action === "preclonagem") {
                            $("#duplicar").attr('data-curso',curso);
                        } else if (action === "clonagem") {
                            $("#form1").attr('action', 'form_curso.php');
                            $("#form").val(action);
                            $("#form1").submit();
                        }
                        else if(action === "exclusao"){
                            var qtd = $(this).data("qtd");
                            if(qtd != 0){
                                bootAlert("Função não pode ser excluida, pois existe vínculo à mesma", "Exclusão de Função", null, 'warning');
                            }else{
                                bootConfirm("Você deseja realmente excluir esta função?", "Exclusão de Função", function(data){
                                    if(data == true){
                                        $("#"+curso).remove();
                                        $.ajax({
                                            url:"del_curso.php?id="+curso
                                        });
                                    }
                                }, 'danger');
                            }
                        }
                    }
                });
                $("#novaFuncao").click(function(){
                    $("#form1").attr('action','form_curso.php');
                    $("#curso").val("");
                    $("#form").val($(this).val());
                    $("#form1").submit();
                });
                
                //validation engine
                $("#form1").validationEngine({promptPosition: "topRight"});
                
                $('#form1').on('keyup keypress', function(e) {
                    var keyCode = e.keyCode || e.which;
                    if (keyCode === 13) { 
                        e.preventDefault();
                        return false;
                    }
                });
            });
        </script>
    </body>
</html>