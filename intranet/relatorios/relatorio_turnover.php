<?php
error_reporting(E_ALL);

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=false';</script>";
}
 
include("../conn.php");
include("../wfunction.php");
include("../classes/BotoesClass.php");
include("../classes/BancoClass.php");
include("../classes/global.php");
include("../classes/c_classificacaoClass.php");
include("../classes/ContabilLancamentoClass.php");
include("../classes/ContabilLoteClass.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$botoes = new BotoesClass("../img_menu_principal/");
$icon = $botoes->iconsModulos;

$objClassificador = new c_classificacaoClass();

$projeto = ($_REQUEST['projeto'] > 0) ? $_REQUEST['projeto'] : null;
$mes = (!empty($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$ano = (!empty($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
//print_array($_REQUEST);
if(isset($_REQUEST['ano']) && isset($_REQUEST['mes']) && isset($_REQUEST['projeto']) && isset($_REQUEST['filtrar']) && $_REQUEST['projeto'] >  0) { 
    
    $sql = "
        SELECT B.nome, DATE_FORMAT(B.data_saida, '%d/%m/%Y') data_saida, DATE_FORMAT(B.data_entrada, '%d/%m/%Y') data_entrada, C.nome cargo,
        IF(B.data_saida BETWEEN '{$ano}-{$mes}-01' AND LAST_DAY('{$ano}-{$mes}-01'), 'DEMITIDO',IF(B.data_entrada BETWEEN '{$ano}-{$mes}-01' AND LAST_DAY('{$ano}-{$mes}-01'), 'ADMITIDO','')) st
        FROM rh_folha_proc A
        INNER JOIN rh_clt B ON (A.id_clt = B.id_clt)
        INNER JOIN curso C ON (B.id_curso = C.id_curso)
        WHERE A.id_projeto = {$projeto} AND A.mes = {$mes} AND A.ano = {$ano}
        ORDER BY B.nome";
    $qry = mysql_query($sql);
    $num = mysql_num_rows($qry);
    while($row = mysql_fetch_assoc($qry)){
        $array[] = $row;
    }
    $nomeProjeto = mysql_result(mysql_query("SELECT nome FROM projeto WHERE id_projeto = {$projeto} LIMIT 1"),0);
}

$nome_pagina = "Relatório TurnOver";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../", "key_btn" => "3", "area" => "Gestão de RH", "ativo" => $nome_pagina, "id_form" => "frmplanodeconta");
$breadcrumb_pages = array("Lista Projetos"=>"../rh/ver.php"); ?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link rel="shortcut icon" href="../favicon.png">
        <!-- Bootstrap -->        
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-compras.css" rel="stylesheet" media="all">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="all">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?> 
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-rh-header hidden-print">
                        <h2><?php echo $icon['3'] ?> - Contabilidade <small>- <?= $nome_pagina ?></small></h2>
                    </div>
                    <form action="" method="post" name="form_lote" id="form_lote" class="form-horizontal top-margin hidden-print" enctype="multipart/form-data">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="projeto1" class="col-sm-2 control-label">Projeto</label>
                                    <div class="col-sm-4"><?= montaSelect(getProjetos($usuario['id_regiao']), $projeto, "id='projeto' name='projeto' class='form-control validate[required,custom[select]]'") ?></div>
                                
                                    <label for="" class="col-sm-1 control-label">Folha</label>
                                    <div class="col-sm-4">
                                        <div class="input-group">
                                            <?php echo montaSelect(mesesArray(), $mes, "id='mes' name='mes' class='validate[required,custom[select]] form-control'"); ?>
                                            <div class="input-group-addon">/</div>
                                            <?php echo montaSelect(anosArray(), $ano, "id='ano' name='ano' class='validate[required,custom[select]] form-control'"); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="panel-footer hidden-print text-right">
                                <?php if(count($array) > 0){ ?>
                                <button type="button" onclick="tableToExcel('tbRelatorio', 'Relatório Turnover')" name="Excel" value="Excel" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Exportar Excel</button><?php } ?>
                                <button type="submit" id="criar" name="filtrar" value="0" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                            </div>
                        </div>
                    </form>
                    <?php if(isset($_REQUEST['ano']) && isset($_REQUEST['mes']) && isset($_REQUEST['projeto']) && isset($_REQUEST['filtrar']) && $_REQUEST['projeto'] >  0) { ?>
                    <table id="tbRelatorio" class="table  table-bordered table-condensed table-hover text-sm valign-middle">
                        <thead>
                            <tr>
                                <td colspan="5" class="text-center text-bold"><?= $nomeProjeto ?></td>
                            </tr>
                            <tr>
                                <td class="text-center text-bold">NOME</td>
                                <td class="text-center text-bold">CARGO</td>
                                <td class="text-right text-bold">DATA ADMISSÃO</td>
                                <td class="text-right text-bold">DATA DEMISSÃO</td>
                                <td class="text-right text-bold">STATUS</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($array as $key => $value) { ?>
                                <?php if(!empty($value['st'])){ ?>
                                    <?php $tot[$value['st']]++; ?>
                                    <tr>
                                        <td class=""><?= $value['nome'] ?></td>
                                        <td class="text-uppercase"><?= $value['cargo'] ?></td>
                                        <td class="text-right"><?= $value['data_entrada'] ?></td>
                                        <td class="text-right"><?= $value['data_saida'] ?></td>
                                        <td class="text-right"><?= $value['st'] ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                            <tr>
                                <td colspan="2"></td>
                                <td class="text-right text-bold">Total Admitidos: <?= (!empty($tot['ADMITIDO'])) ? $tot['ADMITIDO'] : 0 ?></td>
                                <td class="text-right text-bold">Total Demitidos: <?= (!empty($tot['DEMITIDO'])) ? $tot['DEMITIDO'] : 0 ?></td>
                                <td class=""></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="text-right text-bold">Total de participantes ativos na folha: <?= ($num - $tot['DEMITIDO']) ?></td>
                                <td colspan="3"></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php } else { ?>
                    <div class="alert alert-warning">Nenhuma informação encontrada neste filtro!</div>
                    <?php } ?>
                </div>
            </div>
            <?php include_once '../template/footer.php'; ?>
        </div>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../js/jquery.form.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../resources/js/financeiro/saida.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.form.js" type="text/javascript"></script>
        <script src="../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="js/classificacao.js" type="text/javascript"></script>
    </body>
</html>
