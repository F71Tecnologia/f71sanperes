<?php
session_start();

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/BotoesClass.php');
include('../../classes/SaidaClass.php');
include('../../classes/global.php');
include('../../classes/LogClass.php');

$log = new Log();

$container_full = true;

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);

$saida = new Saida();
$global = new GlobalClass();

$orcamento_id = $_REQUEST['id'];

// Orçamento
$qr_orcamento = mysql_query("
        SELECT A.id, A.inicio, A.fim, CONCAT(B.id_banco, ' - ', B.nome, ' (Ag: ', B.agencia, ' CC: ', B.conta, ')') AS banco_nome
        FROM gestao_orcamentos A
        LEFT JOIN bancos B ON (A.banco_id = B.id_banco)
        WHERE id = '{$orcamento_id}'"
        . "ORDER BY id DESC");
$row_orcamento = mysql_fetch_assoc($qr_orcamento);
$total_orcamento = mysql_num_rows($qr_orcamento);

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Exclusão de Orçamento");
$breadcrumb_pages = array("Gestão de Orçamentos"=>"index.php");

// Exclusão
if(isset($_POST['envio'])) {
	mysql_query("DELETE FROM gestao_orcamentos WHERE id = '{$orcamento_id}' LIMIT 1");
	mysql_query("DELETE FROM gestao_orcamentos_valores WHERE orcamento_id = '{$orcamento_id}'");
        $log->gravaLog('Financeiro - Gestão de Orçamentos', "Orçamento Excluido: ID{$orcamento_id}");
	header("Location: index.php");
}

// Formato de Data Brasileiro
function data_brasileiro($data) {
	return implode('/', array_reverse(explode('-', $data)));
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Exclusão de Orçamento</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="all">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include('../../template/navbar_default.php'); ?>
        <div class="<?php echo ($container_full) ? 'container-full' : 'container'; ?>">
            <div class="page-header box-financeiro-header">
            	<h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Exclusão de Orçamento</small></h2>
            </div>
	    	<div class="panel panel-default hidden-print">
	        	<form method="post">
			        <div class="panel-body">
			        	<h3>Dados do Orçamento</h3>
						<strong>Conta:</strong> <?php echo $row_orcamento['banco_nome']; ?><br>
						<strong>Validade:</strong> <?php echo data_brasileiro($row_orcamento['inicio']); ?> à <?php echo data_brasileiro($row_orcamento['fim']); ?>
					</div>
					<div class="panel-footer text-right">
						<input type="hidden" name="envio" value="1">
						<input type="hidden" name="id" value="<?php echo $row_orcamento['id']; ?>">
						<input type="submit" value="Confirmar Exclusão" class="btn btn-danger">
						<a href="index.php" class="btn btn-info">Cancelar</a>
			        </div>
			    </form>
			</div>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/highcharts/highcharts.js"></script>
        <script src="../../resources/js/highcharts/highcharts.drilldown.js"></script>
        <script src="../../resources/js/highcharts/highcharts.exporting.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/financeiro/detalhado.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskMoney.js"></script>
    </body>
</html>
