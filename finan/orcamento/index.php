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

$container_full = true;

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);

$saida = new Saida();
$global = new GlobalClass();

// Orçamentos Salvos
$qr_orcamentos_salvos = mysql_query("SELECT A.id, A.inicio, A.fim, CONCAT(B.id_banco, ' - ', B.nome, ' (Ag: ', B.agencia, ' CC: ', B.conta, ')') AS banco_nome
FROM gestao_orcamentos A
LEFT JOIN bancos B ON B.id_banco = A.banco_id
ORDER BY id DESC");

//$qr_orcamentos_salvos = mysql_query("SELECT gestao_orcamentos.id, gestao_orcamentos.inicio, gestao_orcamentos.fim, projeto.nome AS projeto_nome, unidade.unidade AS unidade_nome, bancos.nome AS banco_nome FROM gestao_orcamentos INNER JOIN projeto ON id_projeto = projeto_id LEFT JOIN unidade ON id_unidade = unidade_id LEFT JOIN bancos ON id_banco = banco_id WHERE projeto.id_regiao = '2' ORDER BY id DESC");
$total_orcamentos_salvos = mysql_num_rows($qr_orcamentos_salvos);

$nome_pagina = "Gestão de Orçamentos";
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>$nome_pagina);

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
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link rel="shortcut icon" href="../../favicon.png">
        <!-- Bootstrap -->        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <!--<link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">-->
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?> 
        <div class="container-full">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-financeiro-header hidden-print">
                        <h2><span class="glyphicon glyphicon-usd"></span> - Financeiro <small>- <?= $nome_pagina ?></small></h2>
                    </div>
                    <div class="col-xs-12 text-right" style="margin-bottom: 10px;">
                        <a class="btn btn-success" id ="add" href="gestao_orcamentos_normal.php"><i class="fa fa-plus"></i> Novo Orçamento</a>
                        <a class="btn btn-warning" id ="gerar" href="previsto_realizado_provisionado_completo_1.php"><i class="fa fa-file-excel-o"></i> RELATÓRIO PREVISTO E REALIZADO</a>
                        <!--<a type="submit" class="btn btn-info" name="gerar" id ="gerar" href="gestao_orcamentos_importacao.php"><i class="fa fa-file"></i> Importar</a>-->
                    </div>
                    <?php if($total_orcamentos_salvos) { ?>
                        <table id="tbRelatorio" class="table table-striped table-hover table-bordered table-condensed">
                            <thead>
                                <tr class="bg-default valign-middle">
                                    <th class="text-center"></th>
                                    <th>Banco</th>
                                    <th class="text-center">Período</th>                             
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                            while($row_orcamento_salvo = mysql_fetch_assoc($qr_orcamentos_salvos)) { ?>
                                <tr class="valign-middle">
                                    <td class="text-center">#<?php echo $row_orcamento_salvo['id']; ?></td>
                                    <td><?php echo $row_orcamento_salvo['banco_nome']; ?></td>
                                    <td class="text-center"><?php echo data_brasileiro($row_orcamento_salvo['inicio']).' à '.data_brasileiro($row_orcamento_salvo['fim']); ?></td>
                                    <td class="text-center">
                                        <a class="btn btn-xs btn-primary" href="gestao_orcamentos_visualizacao.php?id=<?php echo $row_orcamento_salvo['id']; ?>"><i title="Visualizar" class="bt-image glyphicon glyphicon-eye-open"></i></a>
                                        <!--<a class="btn btn-xs btn-warning" href="gestao_orcamentos_edicao.php?id=<?php echo $row_orcamento_salvo['id']; ?>"><i title="Editar" class="bt-image fa fa-pencil"></i></a>-->
                                        <a class="btn btn-xs btn-danger" href="gestao_orcamentos_exclusao.php?id=<?php echo $row_orcamento_salvo['id']; ?>"><i title="Excluir" class="bt-image fa fa-trash-o"></i></a>
                                    </td>
                                </tr>
                            <?php $nomes = null;
                            } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                    <div class="col-xs-12">
                        <div class="alert alert-info">Nenhum orçamento cadastrado!</div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
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
