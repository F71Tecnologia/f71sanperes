<?php
session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/SaidaClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$saida = new Saida();
$global = new GlobalClass();

if(isset($_REQUEST['filtrar'])){
    $filtro = true;
    $result = $saida->getSaidaErro();
    $total_saidaerro = mysql_num_rows($result);
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
if(isset($_REQUEST['projeto']) && isset($_REQUEST['regiao'])){
    $regiaoR = $_REQUEST['regiao'];
    $projetoR = $_REQUEST['projeto'];
}

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Relatório de Saídas com erro 404");
$breadcrumb_pages = array("Principal" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Relatório de Saídas com erro 404</title>
        
        <link href="../../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
            <div class="container">
                <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Relatório de Saídas com erro 404</small></h2></div>

                <div class="panel panel-default">
                    <div class="panel-heading">Relatório de Saídas com erro 404</div>
                    <div class="panel-body">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoR ?>" />
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Região</label>
                            <div class="col-lg-4">                                                        
                                <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='validate[required,custom[select]] form-control'");  ?>
                            </div>
                            <label for="select" class="col-lg-1 control-label">Projeto</label>
                            <div class="col-lg-4">
                                <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto' name='projeto' class='validate[required,custom[select]] form-control'"); ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="submit" name="filtrar" id="filt" value="Gerar" class="btn btn-primary" />
                    </div>
                </div>

                <?php
                if ($filtro) {
                    if ($total_saidaerro > 0) {
                ?>

                <table class='table table-bordered table-hover table-striped table-condensed text-sm valign-middle'>
                    <thead>
                        <tr class="bg-primary">
                            <th>Nome</th>
                            <th>Função</th>                        
                            <th>Status</th>
                            <th>Data de admissão</th>                                                
                        </tr>
                    </thead>
                    <tbody>                    
                        <?php while ($row = mysql_fetch_assoc($result)) { ?>                    
                        <tr>
                            <td><?php echo $row['nome']; ?></td>
                            <td><?php echo $row['funcao']; ?></td>
                            <td><?php echo $row['especifica']; ?></td>
                            <td><?php echo $row['dt_admissao']; ?></td>                        
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <?php } else { ?>
                    <div class="alert alert-danger top30">                    
                        Nenhum registro encontrado
                    </div>
                <?php }
                } ?>

                <?php include('../../template/footer.php'); ?>
            </div>        
        </form>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>       
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {
                $("#regiao").ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");
                
                $("#form1").validationEngine({promptPosition : "topRight"});                
            });
        </script>
    </body>
</html>