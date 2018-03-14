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

$orcamento = $_GET['orcamento'];

$qr_orcamento = mysql_query("
							SELECT
								projeto.nome AS projeto_nome,
								unidade.unidade AS unidade_nome,
								orcamento.inicio AS inicio,
								orcamento.fim AS fim
							FROM gestao_orcamentos AS orcamento
							INNER JOIN projeto
								ON projeto_id = id_projeto
							LEFT JOIN unidade
								ON unidade_id = id_unidade
							WHERE id = '{$orcamento}'
							");
$row_orcamento = mysql_fetch_assoc($qr_orcamento);

$qr_valores = mysql_query("SELECT * FROM gestao_orcamentos_valores WHERE orcamento_id = '{$orcamento}'");

$container_full = true;

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);

$saida = new Saida();
$global = new GlobalClass();

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Orçamentos Salvos");
$breadcrumb_pages = array("Gestão de Orçamentos" => "index.php");

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
        <title>:: Intranet :: Orçamentos Salvos</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="all">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <style>
            @media print {
                .show_print {
                    display: table-row!important;
                }
            }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="<?=($container_full) ? 'container-full' : 'container'?>">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Orçamentos Salvos</small></h2></div>
            <?php
            if(mysql_num_rows($qr_orcamento)) {
            ?>
            <div class="alert alert-dismissable alert-warning">                
                <strong>Projeto: </strong> <?php echo $row_orcamento['projeto_nome']; ?>
                <strong class="borda_titulo">Unidade Gerenciada: </strong> <?php echo $row_orcamento['unidade_nome']; ?>
                <strong class="borda_titulo">O responsável: </strong> <?php echo 'IABAS'; ?>
                <strong class="borda_titulo">Validade: </strong> <?php echo data_brasileiro($row_orcamento['inicio']).' à '.data_brasileiro($row_orcamento['fim']); ?>
            </div>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <table class='table table-bordered table-hover table-condensed text-sm valign-middle'>
                <thead>
                    <tr>
                        <th colspan="3" class="text-center fundo_titulo">Despesas realizadas</th>
                    </tr>
                    <tr class="bg-primary">
                        <th>Código</th>
                        <th>Despesa</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $total = 0;
                while($row_valor = mysql_fetch_assoc($qr_valores)) { ?>
                    <tr class='active'>
                        <td><?php echo $row_valor['codigo']; ?></td>
                        <td><?php echo $row_valor['propriedade']; ?></td>
                        <td><?php echo number_format($row_valor['valor'], 2, ',', '.'); ?></td>
                    <tr>
                <?php 
                	$total += $row_valor['valor'];
                } ?>
    			</tbody>
                <tfoot>
                    <tr class="info">
                        <td></td>
                        <td></td>
                        <td><strong>Total: <?php echo number_format($total, 2, ',', '.'); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </form>
            <?php } else { ?>
                <div class="alert alert-danger top30">                    
                    Nenhum registro encontrado
                </div>
            <?php }
            include('../../template/footer.php'); ?>
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
