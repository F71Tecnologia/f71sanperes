<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}
include("../conn.php");
include("../classes/funcionario.php");
include("../wfunction.php");
include('../classes/global.php');
include '../classes_permissoes/regioes.class.php';
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$projeto = $_REQUEST['projeto'];
$regiao = $usuario['id_regiao'];
$mes = $_REQUEST['mes'];

$meses = mesesArray(null);
$anos = anosArray(2012);

$projetosOp = array("-1" => "« Selecione »");
$query = "SELECT id_projeto,nome FROM projeto WHERE id_regiao = '$regiao'";
$result = mysql_query($query) or die(mysql_error());
while ($row = mysql_fetch_assoc($result)) {
    $projetosOp[$row['id_projeto']] = $row['id_projeto'] . " - " . $row['nome'];
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Rescisões por Período</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>

        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório <small> - Relatório de Rescisões por Período</small></h2></div>
            <form action="../rh/recisao/relatorio_recisao.php" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Aniversáriantes</div>
                    <div class="panel-body">
                        <div class="form-group" >
                            <label for="select" class="col-sm-1 control-label hidden-print" >Projeto</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($projetosOp, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control')); ?><span class="loader"></span>
                            </div>
                            <label for="select" class="col-sm-1 control-label hidden-print">Mês</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect($meses, date('m'), array('name' => "mes", 'id' => 'mes', 'class' => 'validate[custom[select]] form-control')); ?><span class="loader"></span>
                            </div>
                            <label for="select" class="col-sm-1 control-label hidden-print">Ano</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect($anos, date('Y'), array('name' => "ano", 'id' => 'ano', 'class' => 'validate[custom[select]] form-control')); ?><span class="loader"></span>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right hidden-print">
                        <button type="submit" name="filtrar" id="filtrar" value="filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                    </div>               
                </div>
            </form>
            <?php include('../template/footer.php'); ?>
            <div class="clear"></div>
        </div>

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <link href="../net1.css" rel="stylesheet" type="text/css" />


    </body>
</html>
