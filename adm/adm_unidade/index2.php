<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/UnidadeClass.php');

$usuario = carregaUsuario();

$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

$result = getUnidade($id_regiao, $id_projeto);
$total_unidade = mysql_num_rows($result);

$filtro = false;

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) || (isset($_SESSION['voltarCurso']))) {
    $filtro = true;
    if(isset($_SESSION['voltarCurso'])){
        $_REQUEST['regiao'] = $_SESSION['voltarCurso']['id_regiao'];
        $_REQUEST['projeto'] = $_SESSION['voltarCurso']['id_projeto'];
        unset($_SESSION['voltarCurso']);
    }
    $result = getUnidade($_REQUEST['regiao'], $_REQUEST['projeto']);
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
if(isset($_REQUEST['projeto']) && isset($_REQUEST['regiao'])){
    $projetoR = $_REQUEST['projeto'];
    $regiaoR = $_REQUEST['regiao'];
}elseif(isset($_SESSION['projeto']) && isset($_SESSION['regiao'])){
    $projetoR = $_SESSION['projeto'];
    $regiaoR = $_SESSION['regiao'];
}elseif (isset($_SESSION['projeto_select']) && isset($_SESSION['regiao_select'])) {
    $projetoR = $_SESSION['projeto_select'];
    $regiaoR = $_SESSION['regiao_select'];
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Gestão de Unidades");
//$breadcrumb_pages = array("Gestão de RH"=>"../../");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Gestão de Unidades</title>
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
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Gestão de Unidades</small></h2></div>
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
                    <form id="form1" class="form-horizontal" method="post">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?=$projetoR?>" />
                        <input type="hidden" name="hide_regiao" id="hide_regiao" value="<?=$regiaoR?>" />
                        <input type="hidden" name="unidade" id="unidade" value="" />
                        <input type="hidden" name="home" id="home" value="" />
                        <div class="form-group">
                            <label class="control-label col-lg-1">Região:</label> 
                            <div class="col-lg-5"><?=montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='form-control required[custom[select]]'")?></div>
                            <label class="control-label col-lg-1">Projeto:</label> 
                            <div class="col-lg-5"><?=montaSelect(array("-1"=>"« Selecione a Região »"), $projetoR, "id='projeto' name='projeto' class='form-control required[custom[select]]'")?></div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-9 col-lg-2 text-right">
                            <?php if ( $filtro ) { ?>
                            <button type="submit" class="btn btn-success" value="Nova Unidade" name="novo" id="novaUnidade" /><span class="fa fa-plus"></span> Nova Unidade</button>
                            <?php } ?>
                            </div>
                            <div class="col-lg-1 text-right"><button type="submit" class="btn btn-primary" value="Filtrar" name="filtrar" />Filtrar</button></div>
                        </div>
                    </form>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col">
                    <?php if ( $filtro ) {
                        if ($total_unidade > 0) { ?>
                            <p id="excel" style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="btn"></p>
                            <table id="tbRelatorio" class="table table-hover table-condensed">
                                <thead>
                                    <tr>
                                        <th>Cód.</th>
                                        <th>Qtd. de Vínculos</th>
                                        <th>Unidade</th>
                                        <th>Telefone</th>
                                        <th>Endereço</th>
                                        <th>Responsável</th>
                                        <th colspan="3">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $contratacao = "";
                                while ($row = mysql_fetch_assoc($result)) {
                                    $clt = getRhClt($row['id_unidade']);

                                    if($contratacao != $row['tipo_contratacao_nome']){
                                        $contratacao = $row['tipo_contratacao_nome'];
                                        echo "<tr class='tr_contratacao'><td colspan='9'>".ucwords($row['tipo_contratacao_nome'])."</td><tr />";
                                    } ?>
                                    <tr style="margin: 0 0 50px 0;" id="<?=$row['id_unidade']?>">
                                        <td><?=$row['id_unidade']?></td>
                                        <td><?=$clt?></td>
                                        <td><?=strtoupper($row['unidade'])?></td>
                                        <td><?=$row['tel']?></td>
                                        <td><?=strtoupper($row['endereco'])?></td>
                                        <td><?=strtoupper($row['responsavel'])?></td>
                                        <td class="center"><img src="../../imagens/icones/icon-docview.gif" title="Visualizar" class="bt-image" data-type="visualizar" data-key="<?=$row['id_unidade']?>" /></td>
                                        <td class="center"><img src="../../imagens/icones/icon-edit.gif" title="Editar" class="bt-image" data-type="editar" data-key="<?=$row['id_unidade']?>" /></td>                                        
                                        <td class="center"><img src="../../imagens/icones/icon-delete.gif" title="Excluir" class="bt-image" data-clt="<?=$clt?>" data-type="excluir" data-key="<?=$row['id_unidade']?>" /></td>
                                        <!--<td class="center"><img src="../../imagens/icones/icon-copy.png" title="Duplicar" class="bt-image" data-type="duplicar" data-key="<?php //echo $row['id_prestador']; ?>" /></td>-->
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <div class="col-lg-12">
                                <div class="alert alert-dismissable alert-warning">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong>Nenhum registro encontrado!</strong>
                                </div>
                            </div>
                                
                        <?php }
                    } ?>
                </div>
            </div>
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
                    var clt = $(this).data("clt");                     
                    
                    if(action === "visualizar") {
                        $("#unidade").val(key);
                        $("#form1").attr('action','detalhes_unidade2.php');
                        $("#form1").submit();
                    }else if(action === "editar"){
                        $("#unidade").val(key);
                        $("#form1").attr('action','form_unidade2.php');
                        $("#form1").submit();                        
                    }else if(action === "excluir"){                      
                        
                        if(clt != 0){
                            thickBoxAlert("Exclusão de Unidade", "Unidade não pode ser excluida, pois existe CLT vinculada a mesma", 300, 130, null);
                        }else{
                            thickBoxConfirm("Exclusão de Unidade", "Você deseja realmente excluir esta unidade?", 300, 200, function(data){
                                if(data){   
                                    if(data == true){
                                        $("#"+key).remove();
                                        $.ajax({
                                            url:"del_unidade.php?id="+key
                                        });
                                    }
                                }
                            });
                        }
                    }
                });
                
                $("#novaUnidade").click(function(){
                    $("#form1").attr('action','form_unidade2.php');
                    $("#form1").submit();
                });
            });
        </script>
    </body>
</html>