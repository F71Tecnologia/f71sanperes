<?php
session_start();
if (!isset($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');

$usuario = carregaUsuario();
$filtro = $_REQUEST['filtro'];

if (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) {
    $filtroQuery = "nome LIKE '%{$filtro}%' AND";
}
else if (isset($_REQUEST['limpar'])) {
    $filtroQuery = "";
    $filtro = "";
}
$result = montaQuery("rh_horarios", "*", "$filtroQuery status_reg = '1' ORDER BY nome ASC", null, null, '', false);
$total_horario= mysql_num_rows($result);

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Gestão de Horários");

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Gestão de Horários</title>
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
            <input type="hidden" name="horario" id="horario" value="" />
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
                                    <div class="col-xs-3">
                                        <input name="filtro" id="filtro" value="<?= $filtro; ?>" type="text" class="form-control" placeholder="Filtrar por nome">
                                    </div>
                                    <div class="col-xs-1">
                                        <button type="submit" class="btn btn-primary" value="Filtrar" name="filtrar" id="filt" ><i class="fa fa-filter"></i> Filtrar </button>
                                    </div>
                                    <div class="col-xs-1">
                                        <button type="submit" class="btn btn-danger" value="Limpar" name="limpar" id="filt" ><i class="fa fa-eraser"></i> Limpar Filtro</button>
                                    </div>
                                    <button type="submit" class="btn btn-success" value="cadastro" id="novoHorario"><i class="fa fa-plus"></i> Novo Horário</button>
                                </div>
                            </div>
                    </div>
                </div>
                <?php
                if ($total_horario > 0) { ?>
                    <div class="row" style="padding: 0 16px 0 16px">
                        <button type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" class="btn btn-success pull-right"><span class="fa fa-file-excel-o"></span>&nbsp;&nbsp;Exportar para Excel</button>
                    </div>
                <div class="table-responsive">
                    <table id="tbRelatorio" class="table table-striped table-hover table-bordered table-condensed">
                        <thead>
                            <tr class="bg-primary valign-middle">
                                <th width="10%" class="text-center">Cód.</th>
                                <th width="50%">Nome</th>
                                <th width="15%" class="text-center">Adicional Noturno</th>
                                <th width="15%" class="text-center">Hora Noturna</th>                                
                                <th width="10%" colspan="4" class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysql_fetch_assoc($result)) {
                                $queryVinculos = montaQuery("rh_clt", "COUNT(id_clt) as vinculos", "rh_horario = '{$row['id_horario']}' AND (status < 60 OR status = 200)", null, null, '', false);
                                $qtd = mysql_fetch_assoc($queryVinculos);
                                ?>
                                <tr class="valign-middle" id="horario<?= $row['id_horario']; ?>">
                                    <td class="text-center"><?= $row['id_horario']; ?></td>
                                    <td><?= $row['nome']; ?></td>
                                    <td class="text-center"><?= ($row['adicional_noturno']) ? "<span class='label label-success'>Sim</span>":"-"; ?></td>
                                    <td class="text-center"><?= ($row['horas_noturnas']==0)?"-":$row['horas_noturnas']; ?></td>                                  
                                    <td class="text-center"><button type="button" class="btn btn-xs btn-primary acao" value="visualizacao" data-horario="<?=$row['id_horario']; ?>" title="Visualizar Detalhes"><span title="Visualizar" class="bt-image fa fa-search"></span></button></td>
                                    <td class="text-center"><button type="button" class="btn btn-xs btn-warning acao" value="edicao" data-horario="<?=$row['id_horario']; ?>" title="Editar Horário"><span title="Editar" class="bt-image fa fa-pencil"></span></button></td>
                                    <td class="text-center"><button type="button" class="btn btn-xs btn-info acao" value="preclonagem" data-horario="<?=$row['id_horario']; ?>" data-toggle="modal" data-target="#myModal" title="Duplicar Horário"><span title="Clonar" class="bt-image fa fa-clone"></span></button></td>
                                    <td class="text-center"><button type="button" class="btn btn-xs btn-danger acao" value="exclusao" data-horario="<?=$row['id_horario']; ?>" data-qtd="<?=$qtd['vinculos']; ?>" title="Excluir Horário"><span title="Excluir" class="bt-image fa fa-trash-o"></span></button></td>
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
                            <div class="bootstrap-dialog-title">Duplicar Horário</div>
                        </div>
                        <div class="modal-body">
                            <div class="bootstrap-dialog-body"><div class="bootstrap-dialog-message">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <div class="col-xs-12">
                                                    <label class="control-label">Nome</label>
                                                    <input type="text" name="nome" id="nome" class="form-control validate[required]" value="<?= $horario['nome'] ?>" />
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
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                                    <button type="button" class="btn btn-success acao" id="duplicar" data-horario="<?=$row['id_horario']; ?>" value="clonagem">Duplicar</button>
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
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function() {
                $("body").on('click', '.acao', function() {
                    var action = $(this).val();
                    var horario = $(this).data("horario");
                    $(this).closest("form").find("#horario").val(horario);
                    if(horario.length === 0 || horario < 1) {
                        bootAlert("ID do horário não encontrado. Contate um administrador.", "ERRO", null, 'warning');
                    }
                    else {
                        if (action === "visualizacao") {
                            $("#form1").attr('action', 'detalhes_horario.php');
                            $("#form").val(action);
                            $("#form1").submit();
                        } else if (action === "edicao") {
                            $("#form1").attr('action', 'form_horario.php');
                            $("#form").val(action);
                            $("#form1").submit();
                        } else if (action === "preclonagem") {
                            $("#duplicar").attr('data-horario',horario);
                        } else if (action === "clonagem") {
                            $("#form1").attr('action', 'form_horario.php');
                            $("#form").val(action);
                            $("#form1").submit();
                        }
                        else if(action === "exclusao"){
                            var qtd = $(this).data("qtd");
                            if(qtd != 0){
                                bootAlert("Horário não pode ser excluido, pois existe vínculo à mesma", "Exclusão de Horário", null, 'warning');
                            }else{
                                bootConfirm("Você deseja realmente excluir este horário?", "Exclusão de Horário", function(data){
                                    if(data == true){
                                        $("#horario"+horario).remove();
                                        $.ajax({
                                            url:"del_horario.php?id_horario="+horario
                                        });
                                    }
                                }, 'danger');
                            }
                        }
                    }
                });
                $("#novoHorario").click(function(){
                    $("#form1").attr('action','form_horario.php');
                    $("#horario").val("");
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