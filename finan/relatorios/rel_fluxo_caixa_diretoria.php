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

$arrayTrimestre = [1 => [1,2,3,4], 2 => [5,6,7,8], 3 => [9,10,11,12]];
$usuario = carregaUsuario();
$master = mysql_fetch_assoc(mysql_query("SELECT * FROM master WHERE id_master = {$usuario['id_master']} LIMIT 1;"));
$global = new GlobalClass();

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;

$regioes_usuario = array_keys(getRegioes());
unset($regioes_usuario[0]);

$id_projeto = $_REQUEST['projeto'];
$id_banco = $_REQUEST['id_banco'];
$ano = (!empty($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$auxStatus = ($_REQUEST['status_lancamento']) ? $_REQUEST['status_lancamento'] : '1,2';

$bancos_opt = ['0' => 'Todos os Bancos'];
$query = "SELECT * FROM bancos WHERE id_regiao IN(" . implode(', ', $regioes_usuario) . ")";
$result = mysql_query($query);
while ($row = mysql_fetch_assoc($result)) {
    $bancos_opt[$row['id_banco']] = $row['id_banco'] . ' - ' . $row['nome'];
}

if (isset($_REQUEST['filtrar']) || isset($_REQUEST['excel'])) {
    $auxDataVenc2 = " YEAR(data_vencimento) = '{$ano}' AND MONTH(data_vencimento) IN (".implode(',', $arrayTrimestre[$_REQUEST['trimestre']]).")";
    $auxProjeto = ($_REQUEST['projeto']) ? " AND id_projeto = '{$_REQUEST['projeto']}' " : '';
    $auxBanco = ($_REQUEST['id_banco']) ? " AND id_banco = '{$_REQUEST['id_banco']}' " : '';
    
    foreach ($arrayTrimestre[$_REQUEST['trimestre']] as $value) { 
        $sqlSaldo = "
        SELECT SUM(IF(tipo = 'ENTRADAS', REPLACE(valor, ',','.'),-REPLACE(valor, ',','.'))) saldo, status
        FROM (
            SELECT id_saida AS id, data_vencimento, REPLACE(valor, ',','.') AS valor, 'SAIDAS' tipo, status FROM saida WHERE (data_vencimento < '{$ano}-{$value}-01') AND status IN ($auxStatus) {$auxProjeto} {$auxBanco}
            UNION
            SELECT id_entrada AS id, data_vencimento, REPLACE(valor, ',','.') AS valor, 'ENTRADAS' tipo, status FROM entrada WHERE (data_vencimento < '{$ano}-{$value}-01') AND status IN ($auxStatus) {$auxProjeto} {$auxBanco}
        ) AS A
        GROUP BY status;";
        $qrySaldo = mysql_query($sqlSaldo);
        while ($rowSaldo = mysql_fetch_assoc($qrySaldo)) {
            $saldo[$value][$rowSaldo['status']] = $rowSaldo['saldo'];
        }
    }
    
    $sql = "
    SELECT A.*, B.nome AS nomeProjeto, D.nome AS nomeDespesa, E.c_razao, SUM(A.valor) AS valor, MONTH(data_vencimento) AS mes, IF(D.grupo = 5, 'ENTRADA', 'SAIDA') tipo,
    D.cod AS codTipo, 
    F.id_subgrupo AS codSubGrupo, F.nome AS nomeSubGrupo,
    G.id_grupo AS codGrupo, G.nome_grupo AS nomeGrupo
    FROM entradaesaida D
    LEFT JOIN 
        (
        SELECT id_saida, data_vencimento, nome, especifica, REPLACE(valor, ',','.') AS valor, id_projeto, n_documento, tipo AS id_entradasaida, id_prestador, status FROM saida WHERE tipo > 0 AND status IN ($auxStatus) {$auxProjeto} {$auxBanco} AND ($auxDataVenc2)
        UNION
        SELECT id_entrada, data_vencimento, nome, especifica, REPLACE(valor, ',','.') AS valor, id_projeto, numero_doc AS n_documento, tipo AS id_entradasaida, 0 AS id_prestador, status FROM entrada WHERE tipo > 0 AND status IN ($auxStatus) {$auxProjeto} {$auxBanco} AND ($auxDataVenc2)
    ) AS A ON (A.id_entradasaida = D.id_entradasaida)
    LEFT JOIN projeto B ON (A.id_projeto = B.id_projeto)
    LEFT JOIN prestadorservico E ON (A.id_prestador = E.id_prestador)
    
    LEFT JOIN entradaesaida_subgrupo F ON (D.cod LIKE CONCAT(F.id_subgrupo,'%'))
    LEFT JOIN entradaesaida_grupo G ON (G.id_grupo = F.entradaesaida_grupo)

    WHERE D.grupo >= 5 AND A.id_entradasaida > 0
    GROUP BY D.id_entradasaida, MONTH(data_vencimento), A.status
    ORDER BY IF(D.grupo = 5, 0, 1), D.cod, D.nome";
    $qry = mysql_query($sql) or die(mysql_error());
    while ($row = mysql_fetch_assoc($qry)) {
//        $arrayLancamentos[$row['tipo']][$row['nomeDespesa']][$row['mes']][$row['status']] = $row['valor'];
        
        $id_grupo = sprintf('%02d', substr($row['codGrupo'], 0, -1));
        $arrayGrupos[$id_grupo]['descricao'] = $id_grupo . ' - ' . (($id_grupo == '00') ? 'ENTRADA' : $row['nomeGrupo']);
        $arrayGrupos[$id_grupo][$row['mes']][$row['status']] += $row['valor'];
        
        $arraySubGrupos[$id_grupo][$row['codSubGrupo']]['descricao'] = ($id_grupo == '00') ? 'ENTRADA' : $row['codSubGrupo'] . ' - ' . $row['nomeSubGrupo'];
        $arraySubGrupos[$id_grupo][$row['codSubGrupo']][$row['mes']][$row['status']] += $row['valor'];
        
        $arrayTipos[$row['codSubGrupo']][$row['codTipo']][(($id_grupo == '00') ? '' : $row['codTipo'] . ' - ') . "{$row['nomeDespesa']}<!-- ({$row['id_entradasaida']})-->"][$row['mes']][$row['status']] = $row['valor'];
        
        if($id_grupo == '00') {
            $arrayEntrada[$row['mes']][$row['status']] += $row['valor'];
        } else {
            $arraySaida[$row['mes']][$row['status']] += $row['valor'];
        }
    }
    
//    print_array($arrayGrupos);
    
//    unset($arrayLancamentos['SAIDAS']);
    $qtd_lanc = count($arrayLancamentos);
//    print_array($arrayLancamentos);
    
    if(isset($_REQUEST['excel'])){
        $arquivo = 'Fluxo de Caixa.xls';
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
$container_full = 'container-full';
$nome_pagina = "Fluxo de Caixa";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "frmplanodeconta");
$breadcrumb_pages = array("Relatórios Contábeis" => "index.php");
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
        <div class="<?php echo $container_full ?>">
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
                                        <?=montaSelect($global->carregaProjetosByMaster($usuario['id_master'], array("0" => "Todos os Projetos")), $id_projeto, "id='projeto' name='projeto' class='form-control input-sm validate[required,custom[select]]'") ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="id_banco" class="control-label">Banco</label>
                                        <?= montaSelect($bancos_opt, $id_banco, "id='id_banco' name='id_banco' class='form-control input-sm validate[required,custom[select]]'") ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <label for="" class="control-label">Período</label>
                                        <div class="input-group">
                                            <?php echo montaSelect([1 => '1º Trimestre', 2 => '2º Trimestre', 3 => '3º Trimestre'], $_REQUEST['trimestre'], 'class="form-control input-sm" name="trimestre" id="trimestre"') ?>
                                            <div class="input-group-addon">até</div>
                                            <?php echo montaSelect(anosArray(2017), $ano, 'class="form-control input-sm" name="ano" id="ano"') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <?php if (count($qtd_lanc) > 0) { ?><button type="submit" name="excel" value="Excel" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Exportar Excel</button><?php } ?>
                                <button type="submit" id="criar" name="filtrar" value="Filtrar" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filtrar</button>
                            </div>
                        </div>
                    </form>
<?php } ?>
                    <?php if (isset($_REQUEST['filtrar']) || isset($_REQUEST['excel'])) { ?>
                        <table id="tbRelatorio" class="table table-condensed valign-middle table-bordered">
                            <tr><td colspan="11" class="text-left text-bold"><?= $master['razao'] ?></td></tr>
                            <tr><td colspan="11" class="text-left text-bold">FLUXO DE CAIXA TRIMESTRAL</td></tr>
                            <tr><td colspan="11" class="text-bold text-center">&nbsp;</td></tr>
                            <tr>
                                <td class="text-center text-bold"></td>
                                <?php foreach ($arrayTrimestre[$_REQUEST['trimestre']] as $key => $value) { ?><td colspan="2" class="text-center text-bold"><?php echo mesesArray($value) ?></td><?php } ?>
                                <td colspan="2" class="text-center text-bold">Total</td>
                            </tr>
                            <tr>
                                <td class="text-center text-bold"><?php echo $_REQUEST['ano'] ?></td>
                                <?php foreach ($arrayTrimestre[$_REQUEST['trimestre']] as $key => $value) { ?>
                                    <td class="text-justify text-bold">Previsto</td>
                                    <td class="text-justify text-bold">Realizado</td>
                                <?php } ?>
                                <td class="text-justify text-bold">Previsto</td>
                                <td class="text-justify text-bold">Realizado</td>
                            </tr>
                            <?php $totalSaldo = []; ?>
                            <tr class="active hide">
                                <td class="text-left text-bold">Saldo Anterior</td>
                                <?php foreach ($arrayTrimestre[$_REQUEST['trimestre']] as $key => $value) { ?>
                                    <?php 
                                    $totalSaldo[1] += $saldo[$value][1];
                                    $totalSaldo[2] += $saldo[$value][2]; 
                                    ?>
                                    <td class="text-right text-bold"><?php echo number_format($saldo[$value][1],2,',','.') ?></td>
                                    <td class="text-right text-bold"><?php echo number_format($saldo[$value][2],2,',','.') ?></td>
                                <?php } ?>
                                <td class="text-right text-bold"><?php echo number_format($totalSaldo[1],2,',','.') ?></td>
                                <td class="text-right text-bold"><?php echo number_format($totalSaldo[2],2,',','.') ?></td>
                            </tr>
                            <?php foreach ($arrayGrupos as $idGrupo => $grupo) { $totalLinhaG = []; ?>
                            <tr>
                                <td class="text-left text-bold"><?php echo $grupo['descricao'] ?></td>
                                <?php foreach ($arrayTrimestre[$_REQUEST['trimestre']] as $key => $value) { ?>
                                    <?php 
                                    $totalLinhaG[1] += $grupo[$value][1];
                                    $totalLinhaG[2] += $grupo[$value][2]; ?>
                                    <td class="text-right text-bold"><?php echo number_format($grupo[$value][1],2,',','.') ?></td>
                                    <td class="text-right text-bold"><?php echo number_format($grupo[$value][2],2,',','.') ?></td>
                                <?php } ?>
                                <td class="text-right text-bold active"><?php echo number_format($totalLinhaG[1],2,',','.') ?></td>
                                <td class="text-right text-bold active"><?php echo number_format($totalLinhaG[2],2,',','.') ?></td>
                            </tr>
                                <?php foreach ($arraySubGrupos[$idGrupo] as $idSubGrupo => $subgrupo) { $totalLinhaSG = []; ?>
                                <tr>
                                    <td class="text-left text-bold"><?php echo '&nbsp;&nbsp;' . $subgrupo['descricao'] ?></td>
                                    <?php foreach ($arrayTrimestre[$_REQUEST['trimestre']] as $key => $value) { ?>
                                        <?php 
                                        $totalLinhaSG[1] += $subgrupo[$value][1];
                                        $totalLinhaSG[2] += $subgrupo[$value][2]; ?>
                                        <td class="text-right text-bold"><?php echo number_format($subgrupo[$value][1],2,',','.') ?></td>
                                        <td class="text-right text-bold"><?php echo number_format($subgrupo[$value][2],2,',','.') ?></td>
                                    <?php } ?>
                                    <td class="text-right text-bold active"><?php echo number_format($totalLinhaSG[1],2,',','.') ?></td>
                                    <td class="text-right text-bold active"><?php echo number_format($totalLinhaSG[2],2,',','.') ?></td>
                                </tr>
                            <?php 
			    // EESE ARRAY VEM A KEY QUE NA VERDADE TRAZ OS VALORES PARA MONTAR SUB GRUPO
			    
			    unset($arrayTipos[$idSubGrupo]['06.01.06']);
			    foreach ($arrayTipos[$idSubGrupo] as $k1 => $v1) { 
				   
				
				?>
                                <?php foreach ($v1 as $k2 => $v2) { $totalLinha = []; ?>
                                    <tr>
                                        <td class="text-left"><?php echo '&nbsp;&nbsp;&nbsp;&nbsp;' . $k2 ?></td>
                                        <?php foreach ($arrayTrimestre[$_REQUEST['trimestre']] as $key => $value) { ?>
                                            <?php 
                                            $totalLinha[1] += $v2[$value][1];
                                            $totalLinha[2] += $v2[$value][2]; 
                                            $totalColuna[$value][1] += $v2[$value][1];
                                            $totalColuna[$value][2] += $v2[$value][2]; 
//                                            $totalColuna[$k1][$value][1] += $v2[$value][1];
//                                            $totalColuna[$k1][$value][2] += $v2[$value][2]; 
                                            ?>
                                            <td class="text-right"><?php echo number_format($v2[$value][1],2,',','.') ?></td>
                                            <td class="text-right"><?php echo number_format($v2[$value][2],2,',','.') ?></td>
                                        <?php } ?>
                                        <td class="text-right active"><?php echo number_format($totalLinha[1],2,',','.') ?></td>
                                        <td class="text-right active"><?php echo number_format($totalLinha[2],2,',','.') ?></td>
                                    </tr>
                                <?php } ?>
                                <tr class="active hide">
                                    <td class="text-bold text-left">TOTAL <?php echo $k1 ?></td>
                                    <?php $totalLinha = []; foreach ($arrayTrimestre[$_REQUEST['trimestre']] as $key => $value) { ?>
                                        <?php 
                                        $totalLinha[1] += $totalColuna[$k1][$value][1];
                                        $totalLinha[2] += $totalColuna[$k1][$value][2]; 
                                        ?>
                                        <td class="text-right text-bold"><?php echo number_format($totalColuna[$k1][$value][1],2,',','.') ?></td>
                                        <td class="text-right text-bold"><?php echo number_format($totalColuna[$k1][$value][2],2,',','.') ?></td>
                                    <?php } ?>
                                    <td class="text-right text-bold"><?php echo number_format($totalLinha[1],2,',','.') ?></td>
                                    <td class="text-right text-bold"><?php echo number_format($totalLinha[2],2,',','.') ?></td>
                                </tr>
                            <?php } $totalSaldo = []; ?>
                                <?php } ?>
                            <?php } ?>
                            <tr class="active">
                                <td class="text-bold text-left">TOTAL</td>
                                <?php foreach ($arrayTrimestre[$_REQUEST['trimestre']] as $key => $value) { ?>
                                    <?php 
                                    $totalSaldo[1] += $arrayEntrada[$value][1] - $arraySaida[$value][1];
                                    $totalSaldo[2] += $arrayEntrada[$value][2] - $arraySaida[$value][2];
//                                    $totalSaldo[1] += $saldo[$value][1] + $totalColuna['ENTRADAS'][$value][1] - $totalColuna['SAIDAS'][$value][1];
//                                    $totalSaldo[2] += $saldo[$value][2] + $totalColuna['ENTRADAS'][$value][2] - $totalColuna['SAIDAS'][$value][2];
                                    ?>
                                    <td class="text-right text-bold"><?php echo number_format($arrayEntrada[$value][1] - $arraySaida[$value][1],2,',','.') ?></td>
                                    <td class="text-right text-bold"><?php echo number_format($arrayEntrada[$value][2] - $arraySaida[$value][2],2,',','.') ?></td>
                                <?php } ?>
                                <td class="text-right text-bold"><?php echo number_format($totalSaldo[1],2,',','.') ?></td>
                                <td class="text-right text-bold"><?php echo number_format($totalSaldo[2],2,',','.') ?></td>
                            </tr>
                        </table>
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