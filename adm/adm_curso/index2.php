<?php
session_start();
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
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

$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

$result = FuncoesClass::getCursos($id_regiao, $id_projeto);
$total_curso = mysql_num_rows($result);

$filtro = false;

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) || (isset($_SESSION['voltarCurso'])) || ($_REQUEST['atualizar'])) {
    $filtro = true;
    if (isset($_SESSION['voltarCurso'])) {
        $_REQUEST['regiao'] = $_SESSION['voltarCurso']['id_regiao'];
        $_REQUEST['projeto'] = $_SESSION['voltarCurso']['id_projeto'];
        unset($_SESSION['voltarCurso']);
    }
    $result = FuncoesClass::getCursos($_REQUEST['regiao'], $_REQUEST['projeto']);
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
if (isset($_REQUEST['projeto']) && isset($_REQUEST['regiao'])) {
    $projetoR = $_REQUEST['projeto'];
    $regiaoR = $_REQUEST['regiao'];
} elseif (isset($_SESSION['projeto']) && isset($_SESSION['regiao'])) {
    $projetoR = $_SESSION['projeto'];
    $regiaoR = $_SESSION['regiao'];
} elseif (isset($_SESSION['projeto_select']) && isset($_SESSION['regiao_select'])) {
    $projetoR = $_SESSION['projeto_select'];
    $regiaoR = $_SESSION['regiao_select'];
}

$sql_departamento = "SELECT * FROM departamentos ORDER BY nome";
$sql_departamento = mysql_query($sql_departamento);
$arrayDepartamentos[0] = 'Selecione';
while($row_departamento = mysql_fetch_assoc($sql_departamento)){
    $arrayDepartamentos[$row_departamento['id_departamento']] = $row_departamento['nome'];
}

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
        <link href="../../resources/css/bootstrap-note.css.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Gestão de Funções</small></h2></div>
                </div>
            </div>
            <!--resposta de algum metodo realizado-->
            <?php if(!empty($_SESSION['MESSAGE'])){ ?>
                <div id="message-box" class="alert alert-dismissable alert-warning <?php echo $_SESSION['MESSAGE_COLOR']; ?> alinha2">
                    <?php echo $_SESSION['MESSAGE']; session_destroy(); ?>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-lg-12">
                    <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" class="form-horizontal" >
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="form-group">
                                    <input type="hidden" name="projeto_select" id="projeto_select" value="<?php echo $_SESSION['projeto_select']; ?>" />
                                    <input type="hidden" name="regiao_select" id="regiao_select" value="<?php echo $_SESSION['regiao_select']; ?>" />
                                    <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoR; ?>" />
                                    <input type="hidden" name="hide_regiao" id="hide_regiao" value="<?php echo $regiaoR; ?>" />
                                    <input type="hidden" name="curso" id="curso" value="" />
                                    <label class="col-lg-1 control-label">Região: </label>
                                    <div class="col-lg-3">
                                        <?php echo montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='required[custom[select]] form-control'"); ?>
                                    </div>
                                    <label class="col-lg-1 control-label">Projeto: </label>
                                    <div class="col-lg-3">
                                        <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto' name='projeto' class='required[custom[select]] form-control' ") ?>
                                    </div>
                                </div>
                            </div><!-- /.panel-body -->
                            <div class="panel-footer text-right">
                                <input type="submit" class="btn btn-primary" value="Filtrar" name="filtrar" id="filt" />
                                <?php if($filtro){ ?>
                                    <input type="submit" class="btn btn-success" value="Nova Função" name="novo" id="novoCurso" />
                                    <!--a class="" name="novo" id="novoCurso" href="form_curso2.php"><i class="fa fa-plus"></i> Nova Função</a-->
                                <?php } ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <hr>
                <?php
            if ($filtro) {
                if ($total_curso > 0) {
                    $count = 0; ?>
                    <button type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" class="btn btn-success pull-right"><span class="fa fa-file-excel-o"></span>&nbsp;&nbsp;Exportar para Excel</button>
                    <div class="table-responsive">
                    <table id="tbRelatorio" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="5%">Cód.</th>
                                <th width="5%">Qtd. de Vínculos</th>
                                <th width="50%">Função</th>
                                <th width="10%">Departamento</th>
                                <th width="7%">CBO</th>
                                <th width="13%">Valor</th>
                                <th width="5%">Qtd. Máxima</th>                                    
                                <th width="5%" colspan="3">Ações</th>
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
                                    $departamento = montaSelect($arrayDepartamentos, $value, 'name="departamento" class="departamento" data-curso="'.$row['id_curso'].'"');
                                    $departamento .= '<span class="nomeDepartamento"></span>';
                                } else {
                                    $departamento = $arrayDepartamentos[$row['id_departamento']];
                                }
                            }elseif(($row['tipo'] == 1) || ($row['tipo'] == 3)){
                                $qtd_vinculos = FuncoesClass::getAutonomo($row['id_curso']);
                            }

                            if($contratacao != $row['tipo_contratacao_nome']){
                                $contratacao = $row['tipo_contratacao_nome'];
                                echo "<tr class='tr_contratacao'><th colspan='10' style='background: #F0F0F7'>".ucwords($row['tipo_contratacao_nome'])."</th><tr />";
                            }
                        ?>
                                <tr id="<?php echo $row['id_curso']; ?>">
                                    <td><?php echo $row['id_curso']; ?></td>
                                    <td><?php echo $qtd_vinculos; ?></td>
                                    <td><?php echo $row['nome']; ?></td>
                                    <td><?php echo $departamento; ?></td>
                                    <td><?php echo $row['cod']; ?></td>
                                    <td><?php echo formataMoeda($row['salario']); ?></td>
                                    <td><?php echo $row['qnt_maxima']; ?></td>                                                                                
                                    <td class="center"><img src="../../imagens/icones/icon-docview.gif" title="Visualizar" class="bt-image" data-type="visualizar" data-key="<?php echo $row['id_curso']; ?>" /></td>
                                    <td class="center"><img src="../../imagens/icones/icon-edit.gif" title="Editar" class="bt-image" data-type="editar" data-key="<?php echo $row['id_curso']; ?>" /></td>
                                    <td class="center"><img src="../../imagens/icones/icon-delete.gif" title="Desativar" class="bt-image" data-qtd="<?php echo $qtd_vinculos; ?>" data-type="excluir" data-key="<?php echo $row['id_curso']; ?>" /></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    </div>
                <?php } else { ?>
                    <div class="alert alert-dismissable alert-warning">
                        <p>Nenhum registro encontrado</p>
                    </div>
                <?php }
            } ?>

            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function() {
                $("#regiao").ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");

                $(".bt-image").on("click", function() {
                    var action = $(this).data("type");
                    var key = $(this).data("key");
                    var emp = $(this).parents("tr").find("td:first").next().html();
                    var qtd = $(this).data("qtd");

                    if (action === "visualizar") {
                        $("#curso").val(key);
                        $("#form1").attr('action', 'detalhes_curso2.php');
                        $("#form1").submit();
                    } else if (action === "editar") {
                        $("#curso").val(key);
                        $("#form1").attr('action', 'edit_curso2.php');
                        $("#form1").submit();
                    }
                    else if(action === "excluir"){
                        
                        if(qtd != 0){
                            thickBoxAlert("Exclusão de Função", "Função não pode ser excluida, pois existe vínculo a mesma", 300, 130, null);
                        }else{
                            thickBoxConfirm("Exclusão de Função", "Você deseja realmente excluir esta função?", 300, 200, function(data){
                                if(data){                                       
                                    if(data == true){
                                        $("#"+key).remove();
                                        $.ajax({
                                            url:"del_curso.php?id="+key
                                        });
                                    }
                                }
                            });
                        }
                    }
                });
                
                $("#novoCurso").click(function(){
                    $("#form1").attr('action','form_curso2.php');
                    $("#form1").submit();
                });
                
                var proj = $("#projeto_select").val();
                var regi = $("#regiao_select").val();
                var edit_projeto = $(hide_projeto).val();
                var edit_regiao = $(hide_regiao).val();
                
                $(".departamento").change(function(){
                    var text = $("option:selected", this).text();
                    var curso = $(this).data('curso');
                    var departamento = $(this).val();
                    
                    $.post("", {bugger:Math.random(), id_departamento:departamento, id_curso:curso}, function(resultado){
                        console.log(resultado);
                    });
                    
                    $(this).next().html(text);
                    $(this).hide();
                    $(this).next().show();
                });
            });
        </script>
    </body>
</html>