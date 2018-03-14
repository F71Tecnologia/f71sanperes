<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include ("../conn.php");
include ("../classes/funcionario.php");
include ("../classes_permissoes/regioes.class.php");
include ("../wfunction.php");

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$optContratacao = array("-1" => "� Selecione �", "2" => "CLT", "1" => "AUT�NOMO", "3" => "COOPERADO", "4" => "AUT�NOMO/PJ");

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)
$breadcrumb_config = array("nivel" => " ../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form-lista", "ativo" => "Remover Funcion�rio");

if (isset($_REQUEST['gerar'])) {
    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $tipoContratacao = $_REQUEST['contratacao'];

    if ($tipoContratacao == 2) {
        //Query para receber todos os CLTs que estejam em uma folha de pagamento fechada (status = 3)
        $sql = "SELECT DISTINCT id_clt FROM rh_folha_proc WHERE id_projeto = '{$id_projeto}' AND id_regiao = '{$id_regiao}' AND status IN ('2','3')";

        //Query para receber as informa��es do CLT que n�o est�o em uma folha de pagamento
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

        //Query para receber todos os Cooperados que n�o est�o em uma folha de pagamento
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
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Remover Funcion�rio</title>
        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href=" ../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href=" ../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href=" ../resources/css/main.css" rel="stylesheet" media="all">
        <link href=" ../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href=" ../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href=" ../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href=" ../css/progress.css" rel="stylesheet" type="text/css">
        <link href=" ../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <!--link href=" ../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->

    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Remover Funcion�rio</small></h2></div>

                    <div class="tab-content">                        
                        <div role="tabpanel" class="tab-pane active" id="lista">
                            <form action="" class="form-horizontal" role="form" id="form" method="post" autocomplete="off">

                                <div class="panel panel-default hidden-print">
                                    <div class="panel-body">
                                        <input type="hidden" name="hide_regiao" id="hide_regiao" value="<?php echo $regiaoSel ?>" />
                                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                                        <input type="hidden" name="hide_contratacao" id="hide_contratacao" value="<?php echo $contratacaoSel ?>" />

                                        <div class="form-group">
                                            <label for="categoria_lista" class="col-lg-2 control-label">Regi�o:</label>
                                            <div class="col-lg-9">
                                                <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="categoria_lista" class="col-lg-2 control-label">Projeto:</label>
                                            <div class="col-lg-9">
                                                <?php echo montaSelect(array("-1" => "� Selecione a Regi�o �"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="nome_centrocusto" class="col-lg-2 control-label">Contrata��o:</label>
                                            <div class="col-lg-9">
                                                <?php echo montaSelect($optContratacao, $contratacaoSel, array('name' => "contratacao", 'id' => 'contratacao', 'class' => 'form-control')); ?>
                                            </div>
                                        </div>

                                    </div><!-- /.panel-body -->

                                    <div class="panel-footer text-right">
                                        <input type="submit" name="gerar" value="Buscar" id="gerar"  class="btn btn-primary"/>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-lg-12">
                    <?php if (!empty($qr) && isset($_POST['gerar'])) { ?>
                        <div class="alert alert-dismissable alert-warning">
                            <p>S� ser�o apresentados os funcion�rios que atenderem aos seguintes requisitos:</p>
                            <p>CLTs: N�o podem estar em uma folha de pagamento.</p>
                            <p>Aut�nomos: N�o podem ter um RPA gerado.</p>
                            <p>Cooperados: N�o podem estar em uma folha de cooperado.</p>
                        </div>
                        <table class="table table-striped table-hover" id="tbRelatorio">
                            <thead>
                                <tr>
                                    <th>NOME</th>
                                    <th>CPF</th>
                                    <th>PROJETO</th>
                                    <th class="text-center">A��O</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysql_fetch_assoc($qr)) { ?>
                                    <tr>
                                        <td><?php echo $row['nome'] ?></td>
                                        <td> <?php echo $row['cpf']; ?></td>
                                        <td><?php echo $row['projetos']; ?></td>                       
                                        <td class="text-center">
                                            <!--a href="javascript:;" class="btn btn-error btn-sm deletar delete"  id="<?php echo $row['id']; ?>"-->
                                            <a href="javascript:;" title="Remover Funcion�rio" class="btn btn-danger btn-sm deletar delete" id="<?php echo $row['id']; ?>">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                            
                                            <input type="hidden" name="tipoContratacao" id="tipoContratacao" value="<?php echo $linha_result['tipo_contratacao']; ?>"/>
                                        </td>
                                    </tr>                                
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } ?>
                </div>
            </div>
            <?php include_once '../template/footer.php'; ?>
        </div><!-- /.container -->
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        
        <script>
            $(function () {
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
            });
            $(document).ready(function () {
                $(".delete").click(function () {
                    var deletar = confirm("Deseja Excluir Realmente?");
                    if (deletar == true) {
                        var del_id = $(this).attr('id');
                        var idRegiao = $("#hide_regiao").val();
                        var idProjeto = $("#hide_projeto").val();
                        var tipoContratacao = $("#hide_contratacao").val();
                        console.del_id;
                        $.ajax({
                            type: 'POST',
                            url: 'ajax-delete.php',
                            data: 'delete_id=' + del_id + '&idRegiao=' + idRegiao + '&idProjeto=' + idProjeto + '&tipoContratacao=' + tipoContratacao,
                            success: function () {
                                alert("Removido com Sucesso!");
                                $("#gerar").click();
                            }
                        });
                    }
                });
            });
        </script>
   </body>
</html>