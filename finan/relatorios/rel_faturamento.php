<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include("../../conn.php");
include("../../wfunction.php");
include("../../empresa.php");
include("../../classes/BotoesClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$master = mysql_fetch_assoc(mysql_query("SELECT * FROM master WHERE id_master = {$usuario['id_master']} LIMIT 1;"));
$global = new GlobalClass();

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;

$regioes_usuario = array_keys(getRegioes());
unset($regioes_usuario[0]);

$id_projeto = $_REQUEST['projeto'];
$id_banco = $_REQUEST['id_banco'];
$data_ini = (!empty($_REQUEST['data_ini'])) ? $_REQUEST['data_ini'] : "01/" . date('m/Y');
$data_fim = (!empty($_REQUEST['data_fim'])) ? $_REQUEST['data_fim'] : date('t/m/Y');
$auxStatus = ($_REQUEST['status_lancamento']) ? $_REQUEST['status_lancamento'] : '1,2';

//$bancos_opt = ['-1' => 'Selecione', '0' => 'Todos os Bancos'];
$bancos_opt = ['0' => 'Todos os Bancos'];
$query = "SELECT * FROM bancos WHERE id_regiao IN(" . implode(', ', $regioes_usuario) . ")";
$result = mysql_query($query);
while ($row = mysql_fetch_assoc($result)) {
    $bancos_opt[$row['id_banco']] = $row['id_banco'] . ' - ' . $row['nome'];
}

if (isset($_REQUEST['filtrar']) || isset($_REQUEST['excel'])) {

    $auxProjeto = ($_REQUEST['projeto']) ? " AND A.id_projeto = '{$_REQUEST['projeto']}' " : '';
    $auxBanco = ($_REQUEST['id_banco']) ? " AND A.id_banco = '{$_REQUEST['id_banco']}' " : '';
    
    $whereSaida = $whereEntrada = 0;
    if(empty($_REQUEST['anual'])) {
        $data1 = $data2 = "{$_REQUEST['ano']}-{$_REQUEST['mes']}-01";
        $colunas = date('t', $data1);
        $GROUP = ', A.data_vencimento';
    } else {
        $data1 = "{$_REQUEST['ano']}-01-01";
        $data2 = "{$_REQUEST['ano']}-12-31";
        $colunas = 12;
        $GROUP = ', MONTH(A.data_vencimento)';
    }
    
    $sqlEntrada = "
    SELECT MONTH(A.data_vencimento) AS mes, A.data_vencimento, CONCAT(B.id_projeto,' - ',B.nome) AS nome_projeto, SUM(REPLACE(valor, ',', '.')) AS valor, (SUM(REPLACE(valor, ',', '.'))/126.82) AS vistoria
    FROM entrada AS A
    LEFT JOIN projeto B ON (A.id_projeto = B.id_projeto)
    WHERE A.data_vencimento BETWEEN '{$data1}' AND LAST_DAY('{$data2}') AND status IN (2) AND A.tipo IN (/*419,*/420,421,422,423)
    $auxProjeto $auxBanco
    GROUP BY A.id_projeto $GROUP
    ORDER BY A.id_projeto, A.data_vencimento ASC, A.tipo;";
    if($_COOKIE['debug'] == 666) { print_array($sqlEntrada); }
    $qryEntrada = mysql_query($sqlEntrada) or die(mysql_error());
    while ($rowEntrada = mysql_fetch_assoc($qryEntrada)) {
        if(empty($_REQUEST['anual'])){
            $arrayEntrada[$rowEntrada['nome_projeto']][$rowEntrada['data_vencimento']] = $rowEntrada;
        } else {
            $arrayEntrada[$rowEntrada['nome_projeto']][$rowEntrada['mes']] = $rowEntrada;
        }
        $arrayTotalEntrada['valor'][$rowEntrada['mes']] += $rowEntrada['valor'];
        $arrayTotalEntrada['vistoria'][$rowEntrada['mes']] += $rowEntrada['vistoria'];
    }
    $totalEntrada = array_sum($arrayTotalEntrada['valor']);
    
    $sqlSaida = "
    SELECT MONTH(A.data_vencimento) AS mes, A.data_vencimento, CONCAT(B.id_projeto,' - ',B.nome) AS nome_projeto, SUM(REPLACE(valor, ',', '.')) AS valor
    FROM saida AS A
    LEFT JOIN projeto B ON (A.id_projeto = B.id_projeto)
    WHERE A.data_vencimento BETWEEN '{$data1}' AND LAST_DAY('{$data2}') AND status IN (2) AND A.tipo IN (427,428,429)
    $auxProjeto $auxBanco
    GROUP BY A.id_projeto $GROUP
    ORDER BY A.id_projeto, A.data_vencimento ASC, A.tipo;";
    if($_COOKIE['debug'] == 666) { print_array($sqlSaida); }
    $qrySaida = mysql_query($sqlSaida) or die(mysql_error());
    while ($rowSaida = mysql_fetch_assoc($qrySaida)) {
        if(empty($_REQUEST['anual'])){
            $arraySaida[$rowSaida['nome_projeto']][$rowSaida['data_vencimento']] = $rowSaida;
        } else {
            $arraySaida[$rowSaida['nome_projeto']][$rowSaida['mes']] = $rowSaida;
        }
        $arrayTotalSaida['valor'][$rowSaida['mes']] += $rowSaida['valor'];
    }
    if($_COOKIE['debug'] == 666) { 
    print_array($arrayEntrada);
    print_array($arrayTotalEntrada);
    print_array($arraySaida);
    print_array($arrayTotalSaida);
    }
    
    if(isset($_REQUEST['excel'])){
        $arquivo = 'Faturamento.xls';
        header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header ("Cache-Control: no-cache, must-revalidate");
        header ("Pragma: no-cache");
        header ("Content-type: application/x-msexcel");
        header ("Content-Disposition: attachment; filename={$arquivo}" );
        header ("Content-Description: PHP Generated Data" );
    }
}

// Configurações header para forçar o download
//header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
//header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
//header ("Cache-Control: no-cache, must-revalidate");
//header ("Pragma: no-cache");
//header ("Content-type: application/x-msexcel");
//header ("Content-Disposition: attachment; filename=\"MODELO - LALUR.xls\"" );
//header ("Content-Description: PHP Generated Data" );
$count = 0;
$nome_pagina = "Faturamento";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo" => $nome_pagina);
$breadcrumb_pages = array("Relatórios Contábeis" => "index.php");
$container_full = true;
?>
<?php if(!isset($_REQUEST['excel'])) { ?>
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
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" media="all">
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
                        <h2><?php echo $icon['4'] ?> - Financeiro <small>- <?= $nome_pagina ?></small></h2>
                    </div>
                    <form action="" method="post" name="form" id="form" class="form-horizontal top-margin hidden-print" enctype="multipart/form-data">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <label for="projeto" class="control-label">Projeto</label>
                                        <?=montaSelect($global->carregaProjetosByMaster($usuario['id_master'], array("0" => "Todos os Projetos")), $id_projeto, "id='projeto' name='projeto' class='form-control input-sm input-sm validate[required,custom[select]]'") ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="id_banco" class="control-label">Banco</label>
                                        <?= montaSelect($bancos_opt, $id_banco, "id='id_banco' name='id_banco' class='form-control input-sm input-sm validate[required,custom[select]]'") ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <label for="" class="control-label">Período</label>
                                        <div class="input-group">
                                            <?php echo montaSelect(mesesArray(), ($_REQUEST['mes']) ? $_REQUEST['mes'] : date('m'), "id='mes' name='mes' class='form-control input-sm validate[required,custom[select]]'") ?>
                                            <div class="input-group-addon">até</div>
                                            <?php echo montaSelect(anosArray(2015), ($_REQUEST['ano']) ? $_REQUEST['ano'] : date('Y'), "id='ano' name='ano' class='form-control input-sm validate[required,custom[select]]'") ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="" class="control-label">&nbsp;</label>
                                        <div class="input-group">
                                            <label class="input-group-addon" for="anual"><input type="checkbox" name="anual" id="anual" <?php echo ($_REQUEST['anual']) ? 'CHECKED' : null ?>></label>
                                            <label class="form-control input-sm" for="anual">Anual?</label>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <?php if (isset($_REQUEST['filtrar'])) { ?>
                                <button type="button" id="imprimir" class="btn btn-success btn-sm"><i class="fa fa-print"></i> Imprimir</button>
                                <button type="submit" name="excel" value="Excel" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Exportar Excel</button>
                                <?php } ?>
                                <button type="submit" id="criar" name="filtrar" value="Filtrar" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filtrar</button>
                            </div>
                        </div>
                    </form>
<?php } ?>
                    <?php if (isset($_REQUEST['filtrar']) || isset($_REQUEST['excel'])) { ?>
                        <?php if (empty($_REQUEST['anual'])) { ?>
                        <table id="tbRelatorio" class="table table-condensed valign-middle table-bordered table-striped">
                            <tr>
                                <td class="text-center text-bold">SALDO BRUTO</td>
                                <?php for($i=1; $i<=$colunas; $i++) { ?>
                                <td class="text-center text-bold" colspan="2"><?php echo sprintf('%02s', $i)."/".sprintf('%02s', $_REQUEST['mes'])."/".$_REQUEST['ano'] ?></td>
                                <?php } ?>
                                <td class="text-center text-bold" colspan="2">TOTAL</td>
                            </tr>
                            <tr>
                                <td class="text-center text-bold">UNIDADE</td>
                                <?php for($i=1; $i<=$colunas; $i++) { ?>
                                <td class="text-center text-bold">RECEBIDO</td>
                                <td class="text-center text-bold">VISTORIAS</td>
                                <?php } ?>
                                <td class="text-center text-bold">RECEBIDO</td>
                                <td class="text-center text-bold">VISTORIAS</td>
                            </tr>
                            <?php foreach ($arrayEntrada as $key => $value) { ?>
                            <tr>
                                <td class="text-left"><?php echo $key ?></td>
                                <?php for($i=1; $i<=$colunas; $i++) { ?>
                                <?php $indice = $_REQUEST['ano']."-".sprintf('%02s', $_REQUEST['mes'])."-".sprintf('%02s', $i); ?>
                                <?php $tot1[$indice]['valor'] += $value[$indice]['valor'] ?>
                                <?php $tot1[$indice]['vistoria'] += $value[$indice]['vistoria'] ?>
                                <?php $tot1[$key]['valor'] += $value[$indice]['valor'] ?>
                                <?php $tot1[$key]['vistoria'] += $value[$indice]['vistoria'] ?>
                                <td class="text-right"><?php echo number_format($value[$indice]['valor'], 2, ',', '.') ?></td>
                                <td class="text-right"><?php echo number_format($value[$indice]['vistoria'], 0, ',', '.') ?></td>
                                <?php } ?>
                                <?php $tot1['valor'] += $tot1[$key]['valor'] ?>
                                <?php $tot1['vistoria'] += $tot1[$key]['vistoria'] ?>
                                <td class="text-right"><?php echo number_format($tot1[$key]['valor'], 2, ',', '.') ?></td>
                                <td class="text-right"><?php echo number_format($tot1[$key]['vistoria'], 0, ',', '.') ?></td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td class="text-center text-bold">TOTAL</td>
                                <?php for($i=1; $i<=$colunas; $i++) { ?>
                                <?php $indice = $_REQUEST['ano']."-".sprintf('%02s', $_REQUEST['mes'])."-".sprintf('%02s', $i); ?>
                                <td class="text-right text-bold"><?php echo number_format($tot1[$indice]['valor'], 2, ',', '.') ?></td>
                                <td class="text-right text-bold"><?php echo number_format($tot1[$indice]['vistoria'], 0, ',', '.') ?></td>
                                <?php } ?>
                                <td class="text-right text-bold"><?php echo number_format($tot1['valor'], 2, ',', '.') ?></td>
                                <td class="text-right text-bold"><?php echo number_format($tot1['vistoria'], 0, ',', '.') ?></td>
                            </tr>
                            <tr><td colspan="<?php echo (($colunas*2)+3) ?>">&nbsp;</td></tr>
                            <tr>
                                <td class="text-center text-bold">SALDO LIQUIDO</td>
                                <?php for($i=1; $i<=$colunas; $i++) { ?>
                                <td class="text-center text-bold" colspan="2"><?php echo sprintf('%02s', $i)."/".sprintf('%02s', $_REQUEST['mes'])."/".$_REQUEST['ano'] ?></td>
                                <?php } ?>
                                <td class="text-center text-bold" colspan="2">TOTAL</td>
                            </tr>
                            <tr>
                                <td class="text-center text-bold">UNIDADE</td>
                                <?php for($i=1; $i<=$colunas; $i++) { ?>
                                <td class="text-center text-bold">RECEBIDO</td>
                                <td class="text-center text-bold">TX BANCARIA</td>
                                <?php } ?>
                                <td class="text-center text-bold">RECEBIDO</td>
                                <td class="text-center text-bold">TX BANCARIA</td>
                            </tr>
                            <?php foreach ($arrayEntrada as $key => $value) { ?>
                            <tr>
                                <td class="text-left"><?php echo $key ?></td>
                                <?php for($i=1; $i<=$colunas; $i++) { ?>
                                <?php $indice = $_REQUEST['ano']."-".sprintf('%02s', $_REQUEST['mes'])."-".sprintf('%02s', $i); ?>
                                <?php $tot2[$indice]['valor'] += $value[$indice]['valor'] - $arraySaida[$key][$indice]['valor'] ?>
                                <?php $tot2[$indice]['tx'] += $arraySaida[$key][$indice]['valor'] ?>
                                <?php $tot2[$key]['valor'] += $value[$indice]['valor'] - $arraySaida[$key][$indice]['valor'] ?>
                                <?php $tot2[$key]['tx'] += $arraySaida[$key][$indice]['valor'] ?>
                                <td class="text-right"><?php echo number_format($value[$indice]['valor'] - $arraySaida[$key][$indice]['valor'], 2, ',', '.') ?></td>
                                <td class="text-right"><?php echo number_format($arraySaida[$key][$indice]['valor'], 2, ',', '.') ?></td>
                                <?php } ?>
                                <?php $tot2['valor'] += $tot2[$key]['valor'] ?>
                                <?php $tot2['tx'] += $tot2[$key]['tx'] ?>
                                <td class="text-right"><?php echo number_format($tot2[$key]['valor'], 2, ',', '.') ?></td>
                                <td class="text-right"><?php echo number_format($tot2[$key]['tx'], 2, ',', '.') ?></td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td class="text-center text-bold">TOTAL</td>
                                <?php for($i=1; $i<=$colunas; $i++) { ?>
                                <?php $indice = $_REQUEST['ano']."-".sprintf('%02s', $_REQUEST['mes'])."-".sprintf('%02s', $i); ?>
                                <td class="text-right text-bold"><?php echo number_format($tot2[$indice]['valor'], 2, ',', '.') ?></td>
                                <td class="text-right text-bold"><?php echo number_format($tot2[$indice]['tx'], 2, ',', '.') ?></td>
                                <?php } ?>
                                <td class="text-right text-bold"><?php echo number_format($tot2['valor'], 2, ',', '.') ?></td>
                                <td class="text-right text-bold"><?php echo number_format($tot2['tx'], 2, ',', '.') ?></td>
                            </tr>
                        </table>
                        <?php } else { ?>
                        <table id="tbRelatorio" class="table table-condensed valign-middle table-bordered table-striped">
                            
                            <tr>
                                <td class="text-center text-bold"></td>
                                <?php for($i=1; $i<=$colunas; $i++) { ?>
                                <td class="text-center text-bold"><?php echo mesesArray($i) ?></td>
                                <?php } ?>
                                <td class="text-center text-bold">TOTAL</td>
                            </tr>
                            <tr>
                                <td class="text-left text-bold">Faturamento Bruto</td>
                                <?php for($i=1; $i<=$colunas; $i++) { ?>
                                <?php $tot3['valor'][$i] += $arrayTotalEntrada['valor'][$i] ?>
                                <td class="text-right"><?php echo number_format($arrayTotalEntrada['valor'][$i], 2, ',', '.') ?></td>
                                <?php } ?>
                                <td class="text-right"><?php echo number_format($totalEntrada, 2, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="text-left text-bold">Vistorias</td>
                                <?php for($i=1; $i<=$colunas; $i++) { ?>
                                <td class="text-right"><?php echo number_format($arrayTotalEntrada['vistoria'][$i], 0, ',', '.') ?></td>
                                <?php } ?>
                                <td class="text-right"><?php echo number_format(array_sum($arrayTotalEntrada['vistoria']), 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="text-left text-bold">(-) Taxa Bancária</td>
                                <?php for($i=1; $i<=$colunas; $i++) { ?>
                                <td class="text-right"><?php echo number_format($arrayTotalSaida['valor'][$i], 2, ',', '.') ?></td>
                                <?php } ?>
                                <td class="text-right"><?php echo number_format(array_sum($arrayTotalSaida['valor']), 2, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="text-left text-bold">% Taxa Bancária</td>
                                <?php for($i=1; $i<=$colunas; $i++) { ?>
                                <?php $porcentagem3 = ($arrayTotalSaida['valor'][$i-1] > 0 && $arrayTotalSaida['valor'][$i] > 0) ? 100.00 - (($arrayTotalSaida['valor'][$i-1] / $arrayTotalSaida['valor'][$i]) * 100) : 0 ?>
                                <!--<td class="text-right"><?php echo number_format($arrayTotalSaida['valor'][$i], 2, ',', '.') ?></td>-->
                                <td class="text-right"><?php echo number_format($porcentagem3, 2, ',', '.') ?>%</td>
                                <?php } ?>
                                <td class="text-right"></td>
                            </tr>
                            <tr>
                                <td class="text-left text-bold">Resultado Líquido</td>
                                <?php for($i=1; $i<=$colunas; $i++) { ?>
                                <?php $resultLiquido[$i] = $arrayTotalEntrada['valor'][$i] - $arrayTotalSaida['valor'][$i] ?>
                                <td class="text-right text-bold"><?php echo number_format($resultLiquido[$i], 2, ',', '.') ?></td>
                                <?php } ?>
                                <td class="text-right text-bold"><?php echo number_format(array_sum($arrayTotalEntrada['valor']) - array_sum($arrayTotalSaida['valor']), 2, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="text-left text-bold">% Faturamento</td>
                                <?php for($i=1; $i<=$colunas; $i++) { ?>
                                <?php $porcentagem3 = ($resultLiquido[$i-1] > 0 && $resultLiquido[$i] > 0) ? 100.00 - (($resultLiquido[$i-1] / $resultLiquido[$i]) * 100) : 0 ?>
                                <td class="text-right text-bold"><?php echo number_format($porcentagem3, 2, ',', '.') ?>%</td>
                                <?php } ?>
                                <td class="text-right text-bold"></td>
                            </tr>
                            
                            <tr><td colspan="<?php echo (($colunas)+2) ?>">&nbsp;</td></tr>
                            <tr>
                                <td class="text-center text-bold">UNIDADE</td>
                                <?php for($i=1; $i<=$colunas; $i++) { ?>
                                <td class="text-center text-bold"><?php echo mesesArray($i) ?></td>
                                <?php } ?>
                                <td class="text-center text-bold">TOTAL</td>
                                <td class="text-center text-bold">%</td>
                            </tr>
                            <?php foreach ($arrayEntrada as $key => $value) { ?>
                            <tr>
                                <td class="text-left"><?php echo $key ?></td>
                                <?php for($i=1; $i<=$colunas; $i++) { ?>
                                <?php $tot4[$i]['valor'] += $value[$i]['valor'] ?>
                                <?php $tot4[$key]['valor'] += $value[$i]['valor'] ?>
                                <td class="text-right"><?php echo number_format($value[$i]['valor'], 2, ',', '.') ?></td>
                                <?php } ?>
                                <?php $tot4['valor'] += $tot4[$key]['valor'] ?>
                                <td class="text-right text-bold"><?php echo number_format($tot4[$key]['valor'], 2, ',', '.') ?></td>
                                <td class="text-right text-bold"><?php echo number_format(($tot4[$key]['valor'] / $totalEntrada) * 100, 2, ',', '.') ?>%</td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td class="text-left text-bold">TOTAL</td>
                                <?php for($i=1; $i<=$colunas; $i++) { ?>
                                <td class="text-right text-bold"><?php echo number_format($tot4[$i]['valor'], 2, ',', '.') ?></td>
                                <?php } ?>
                                <td class="text-right text-bold"><?php echo number_format($tot4['valor'], 2, ',', '.') ?></td>
                                <td class="text-right text-bold"><?php echo number_format(($tot4['valor'] / $totalEntrada) * 100, 2, ',', '.') ?>%</td>
                            </tr>
                            <tr>
                                <td class="text-left text-bold">%</td>
                                <?php for($i=1; $i<=$colunas; $i++) { ?>
                                <?php $porcentagem4 = ($tot4[$i-1]['valor'] > 0 && $tot4[$i]['valor'] > 0) ? 100.00 - (($tot4[$i-1]['valor'] / $tot4[$i]['valor']) * 100) : 0 ?>
                                <!--<td class="text-right text-bold"><?php echo number_format(($tot4[$i]['valor'] / $totalEntrada) * 100, 2, ',', '.') ?>%</td>-->
                                <td class="text-right text-bold"><?php echo number_format($porcentagem4, 2, ',', '.') ?>%</td>
                                <?php } ?>
                                <!--<td class="text-right text-bold"><?php echo number_format(($tot4['valor'] / $totalEntrada) * 100, 2, ',', '.') ?>%</td>-->
                                <td class="text-right text-bold"></td>
                                <td class="text-right text-bold"></td>
                            </tr>
                        </table>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="alert alert-info">Nenhuma informação encontrada neste filtro!</div>
                    <?php } ?>
<?php if(!isset($_REQUEST['excel'])) { ?>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../js/jquery.form.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../resources/js/financeiro/saida.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap-fileclass.min.js" type="text/javascript"></script>
        <script src="../../resources/js/print.js" type="text/javascript"></script>
        <script>
        $(function () {
            $('#form').validationEngine();
            $('body').on('change', '#projeto', function () {
                console.log($("#contas").val());
                $.post("", {bugger: Math.random(), method: 'select_contas', projeto: "'" + $(this).val() + "'", conta: '<?= $_REQUEST['contas'] ?>'}, function (resultado) {
                    $("#contas").html(resultado);
                });
            });
            $('#projeto').trigger('change');
        })
        </script>
    </body>
</html>
<?php } ?>