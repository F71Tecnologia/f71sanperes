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

$orcamento = $_GET['id'];

$qr_orcamento = mysql_query("
    SELECT A.inicio AS inicio, A.fim AS fim, GROUP_CONCAT(B.nome) unidade_nome, TIMESTAMPDIFF(MONTH,A.inicio,ADDDATE(A.fim, INTERVAL 1 DAY)) AS dif_meses, 
    CONCAT(B.id_banco, ' - ', B.nome, ' (Ag: ', B.agencia, ' CC: ', B.conta, ')') AS banco_nome
    FROM gestao_orcamentos AS A
    LEFT JOIN bancos B ON B.id_banco = A.banco_id
    WHERE A.id = '{$orcamento}'
    GROUP BY A.id
    ");
$row_orcamento = mysql_fetch_assoc($qr_orcamento);

//$date_inicio = new DateTime($row_orcamento['inicio']);
//$date_fim = new DateTime($row_orcamento['fim']);
//$diferenca_meses = $date_inicio->diff($date_fim)->m + ($date_inicio->diff($date_fim)->y*12);
$diferenca_meses = $row_orcamento['dif_meses'];

//$container_full = true;

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);

$saida = new Saida();
$global = new GlobalClass();

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Visualização de Orçamento");
$breadcrumb_pages = array("Gestão de Orçamentos"=>"index.php");

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
        <title>:: Intranet :: Visualização de Orçamento</title>
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
            	<h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Visualização de Orçamento</small>
            </div>
            <?php if(mysql_num_rows($qr_orcamento)) { ?>
                <div class="alert alert-dismissable alert-warning">
                    <strong class="">Conta Gerenciada: </strong> <?php echo $row_orcamento['banco_nome']; ?>
                    <strong class="borda_titulo">Validade: </strong> <?php echo data_brasileiro($row_orcamento['inicio']).' à '.data_brasileiro($row_orcamento['fim']); ?>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="relatorio">
                    <?php for($mes=1; $mes<=$diferenca_meses; $mes++) {
                        $qr_valores = mysql_query("SELECT * FROM gestao_orcamentos_valores WHERE orcamento_id = '{$orcamento}' AND mes = '{$mes}' ORDER BY codigo"); ?>
                        <tr>
                            <td colspan="3">
                                <h2>Mês <?php echo $mes; ?></h2>
                            </td>
                        </tr>
                        <tr>
                            <th>Código</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                        </tr>
                        <?php while($row_valor = mysql_fetch_assoc($qr_valores)) { ?>
                        <tr>
                            <td><?php echo $row_valor['codigo']; ?></td>
                            <td><?php echo $row_valor['propriedade']; ?></td>
                            <td><?php echo number_format($row_valor['valor'], 2, ',', '.'); ?></td>
                        </tr>
                        <?php } ?>
                    <?php } ?>
                    </table>
                </div>
            <?php } ?>
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
