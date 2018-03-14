<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$optContratacao = array("-1" => "« Selecione »", "2" => "CLT", "1" => "AUTÔNOMO", "3" => "COOPERADO", "4" => "AUTÔNOMO/PJ");

if (isset($_REQUEST['gerar'])) {
    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $tipoContratacao = $_REQUEST['contratacao'];
    
    if ($tipoContratacao == 2 ) {
    //Query para receber todos os CLTs que estejam em uma folha de pagamento fechada (status = 3)
    $sql = "SELECT DISTINCT id_clt FROM rh_folha_proc WHERE id_projeto = '{$id_projeto}' AND id_regiao = '{$id_regiao}' AND status IN ('2','3')";
    
    //Query para receber as informações do CLT que não estão em uma folha de pagamento
    $qr = mysql_query("SELECT A.*,B.nome AS projetos, A.id_clt AS id 
                            FROM rh_clt AS A 
                            LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
                            LEFT JOIN regioes AS C ON(A.id_regiao = C.id_regiao)
                            WHERE A.id_projeto = '{$id_projeto}' AND A.id_regiao = '{$id_regiao}' 
                            AND id_clt NOT IN ($sql)
                            ORDER BY A.nome");
    } else if ($tipoContratacao == 3) {
        //Query para receber todos os cooperados que estejam em uma folha de pagamento fechada
        $sql = "SELECT DISTINCT id_autonomo FROM folha_cooperado WHERE regiao = '{$id_regiao}' and projeto = '{$id_projeto}' AND status IN ('2','3')";
        
        //Query para receber todos os Cooperados que não estão em uma folha de pagamento
        $qr = mysql_query("SELECT A.*,B.nome AS projetos, A.id_autonomo AS id
                        FROM autonomo AS A 
                        LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
                        LEFT JOIN regioes AS C ON(A.id_regiao = C.id_regiao)
                        WHERE A.id_projeto = '{$id_projeto}' AND A.id_regiao = '{$id_regiao}' 
                        AND A.tipo_contratacao = 3 AND A.id_autonomo NOT IN ($sql)
                        ORDER BY A.nome
                        ");
    } else {
     $qr = mysql_query("SELECT A.*,B.nome AS projetos, A.id_autonomo AS id
                        FROM autonomo AS A 
                        LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
                        LEFT JOIN regioes AS C ON(A.id_regiao = C.id_regiao)
                        WHERE A.id_projeto = '{$id_projeto}' AND A.id_regiao = '{$id_regiao}' AND A.tipo_contratacao = '{$tipoContratacao}'                        
                        ORDER BY A.nome
                        ");
    }
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$contratacaoSel = (isset($_REQUEST['contratacao'])) ? $_REQUEST['contratacao'] : null;

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Remoção de Funcionário");
//$breadcrumb_pages = array("Gestão de Unidades" => "index2.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Remoção de Funcionário</title>
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
        <link href="../resources/css/bootstrap-note.css.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Remoção de Funcionário</small></h2></div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <fieldset>
                        <legend>Relatório</legend>
                        <form class="form-horizontal" method="post" id="form1">
                            <input type="hidden" name="hide_regiao" id="hide_regiao" value="<?=$regiaoSel?>" />
                            <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?=$projetoSel?>" />
                            <input type="hidden" name="hide_contratacao" id="hide_contratacao" value="<?=$contratacaoSel?>" />
                            <input type="hidden" name="home" id="home" value="" />
                            <div class="form-group">
                                <label class="control-label col-lg-1">Região:</label>
                                <div class="col-lg-11"><?=montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', "class" => "form-control"))?></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-1">Projeto:</label> 
                                <div class="col-lg-11"><?=montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', "class" => "form-control"))?></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-1">Contratação:</label> 
                                <div class="col-lg-11"><?=montaSelect($optContratacao, $contratacaoSel, array('name' => "contratacao", 'id' => 'contratacao', "class" => "form-control"))?></div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <input type="submit" class="btn btn-primary pull-right" name="gerar" value="Buscar" id="gerar"/>
                                </div>
                            </div>
                        </form>
                    </fieldset>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <?php if (!empty($qr) && isset($_POST['gerar'])) { ?>
                        <div class="alert alert-danger">
                            <strong>
                                <p>Só serão apresentados os funcionários que atenderem aos seguintes requisitos:</p>
                                <p>CLTs: Não podem estar em uma folha de pagamento.</p>
                                <p>Autônomos: Não podem ter um RPA gerado.</p>
                                <p>Cooperados: Não podem estar em uma folha de cooperado.</p>
                            </strong>
                        </div>
                        <table class="table table-condensed table-hover"> 
                            <thead>
                                <tr>
                                    <th>NOME</th>
                                    <th>CPF</th>
                                    <th>PROJETO</th>
                                    <th>AÇÃO</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php while ($row = mysql_fetch_assoc($qr)) { ?>
                                <tr>
                                    <td><?=$row['nome']?></td>
                                    <td> <?=$row['cpf']?></td>
                                    <td align="center"><?=$row['projetos']?></td>
                                    <td>
                                        <img src="../imagens/deletar_usuario.gif" title="Remover Funcionário" id="<?=$row['id']?>" class="deletar delete"/>
                                        <input type="hidden" name="tipoContratacao" id="tipoContratacao" value="<?=$linha_result['tipo_contratacao']?>"/>
                                    </td>
                                </tr>                                
                            <?php } ?>
                            </tbody>
                        </table>
                    <?php  } ?>
                </div>
            </div>
            <?php include_once '../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script>
            $(function() {
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
            });
            $(document).ready(function(){
                $(".delete").click(function(){
                    var deletar = confirm("Deseja Excluir Realmente?");
                    if(deletar == true){
                        var del_id = $(this).attr('id');
                        var idRegiao = $("#hide_regiao").val();
                        var idProjeto = $("#hide_projeto").val();
                        var tipoContratacao = $("#hide_contratacao").val();
                        console.del_id;
                        $.ajax({
                            type:'POST',
                            url:'ajax-delete.php',
                            data: 'delete_id='+del_id+'&idRegiao='+idRegiao+'&idProjeto='+idProjeto+'&tipoContratacao='+tipoContratacao,
                            success:function() { alert("Removido com Sucesso!"); $("#gerar").click(); }
                        }); 
                    }
                });
            });
        </script>
    </body>
</html>