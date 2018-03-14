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

if (isset($_REQUEST['data_ini']) && isset($_REQUEST['data_fim']) && isset($_REQUEST['projeto']) && (isset($_REQUEST['filtrar']) || isset($_REQUEST['excel']))) {

    $data_ini_bd = implode('-', array_reverse(explode('/', $data_ini)));
    $data_fim_bd = implode('-', array_reverse(explode('/', $data_fim)));

    if($_REQUEST['status_lancamento'] != 2) {
        $auxDataVenc1 = " OR data_vencimento < '$data_ini_bd'";
        $auxDataVenc2 = " OR data_vencimento BETWEEN '$data_ini_bd' AND '$data_fim_bd'";
    }
    
    $campo_data = ($_REQUEST['tp_data']) ? 'data_pg' : 'data_vencimento';
    
    $auxProjeto = ($_REQUEST['projeto']) ? " AND id_projeto = '{$_REQUEST['projeto']}' " : '';
    $auxBanco = ($_REQUEST['id_banco']) ? " AND id_banco = '{$_REQUEST['id_banco']}' " : '';
    
    $whereSaida = $whereEntrada = 0;
    if(empty($_REQUEST['chk'])) {
        $whereSaida = 1;
        $whereEntrada = 1;
    }
    
    if(in_array(1, $_REQUEST['chk'])) {
        $whereSaida = 1;
    }
    
    if(in_array(2, $_REQUEST['chk'])) {
        $whereEntrada = 1;
    }
    
    $sqlSaldo = "SELECT SUM(IF(M.tipo = 'e', REPLACE(valor, ',','.'),-REPLACE(valor, ',','.'))) saldo FROM
    (SELECT id_saida AS id, REPLACE(valor, ',','.') AS valor, 's' tipo FROM saida WHERE status IN ($auxStatus) {$auxProjeto} {$auxBanco} AND ($campo_data < '$data_ini_bd' $auxDataVenc1) AND id_banco > 0 AND $whereSaida
    UNION
    SELECT id_entrada AS id, REPLACE(valor, ',','.') AS valor, 'e' tipo FROM entrada WHERE status IN ($auxStatus) {$auxProjeto} {$auxBanco} AND ($campo_data < '$data_ini_bd' $auxDataVenc1) AND id_banco > 0 AND $whereEntrada) M;";
//print_array($sqlSaldo);
    $saldo = number_format(mysql_result(mysql_query($sqlSaldo), 0), 2, '.', '');

    if($_REQUEST['agrupamento'] == 1) {
        $agrupamento = [", CONCAT(A.id_projeto, ' - ', B.nome) AS nomeAgrupamento", 'A.id_projeto,'];
    } else if($_REQUEST['agrupamento'] == 2) {
        $agrupamento = [", DATE_FORMAT(IF(A.$campo_data IS NOT NULL AND A.$campo_data != '0000-00-00', A.$campo_data, A.data_vencimento), '%d/%m/%Y') AS nomeAgrupamento"];
    } else if($_REQUEST['agrupamento'] == 3) {
        $agrupamento = [", CONCAT(IF(D.cod, CONCAT(D.cod, ' - '), ''), D.nome) AS nomeAgrupamento", "D.cod, D.nome,"];
    } else if($_REQUEST['agrupamento'] == 4) {
        $agrupamento = [", E.c_razao AS nomeAgrupamento", "E.c_razao,"];
    }
    
    $sql = "
    SELECT A.*, B.nome AS nomeProjeto, CONCAT(IF(D.cod, CONCAT(D.cod, ' - '), ''), D.nome) AS nomeDespesa, E.c_razao $agrupamento[0]
    FROM (
        SELECT id_saida, $campo_data AS data_pg, data_vencimento, nome, especifica, REPLACE(valor, ',','.') AS valor, 's' tipo, id_projeto, n_documento, tipo AS id_entradasaida, id_prestador FROM saida WHERE status IN ($auxStatus) {$auxProjeto} {$auxBanco} AND ($campo_data BETWEEN '$data_ini_bd' AND '$data_fim_bd' $auxDataVenc2) AND $whereSaida
        UNION
        SELECT id_entrada, $campo_data AS data_pg, data_vencimento, nome, especifica, REPLACE(valor, ',','.') AS valor, 'e' tipo, id_projeto, numero_doc AS n_documento, tipo AS id_entradasaida, 0 AS id_prestador FROM entrada WHERE status IN ($auxStatus) {$auxProjeto} {$auxBanco} AND ($campo_data BETWEEN '$data_ini_bd' AND '$data_fim_bd' $auxDataVenc2) AND $whereEntrada) AS A
    LEFT JOIN projeto B ON (A.id_projeto = B.id_projeto)
    LEFT JOIN entradaesaida D ON (A.id_entradasaida = D.id_entradasaida)
    LEFT JOIN prestadorservico E ON (A.id_prestador = E.id_prestador)
    ORDER BY $agrupamento[1] IF(A.$campo_data IS NOT NULL AND A.$campo_data != '0000-00-00', A.$campo_data, A.data_vencimento) ASC, A.tipo";
//    print_array($sql);
    $qry = mysql_query($sql) or die(mysql_error());
    while ($row = mysql_fetch_assoc($qry)) {
        $arrayLancamentos[] = $row;
    }

    //$arrayLancamentos = $objLancamentoItens->getLivroDiario($projeto, $data_ini_bd, $data_fim_bd);
    $qtd_lanc = count($arrayLancamentos);
   // print_array($arrayLancamentos);
    
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

$count = 0;
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
        <div class="container">
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
                                            <input type="text" id='data_ini' name='data_ini' class='text-center data validate[required,custom[select]] form-control' value="<?= $data_ini ?>">
                                            <div class="input-group-addon">até</div>
                                            <input type="text" id='data_fim' name='data_fim' class='text-center data validate[required,custom[select]] form-control' value="<?= $data_fim ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Status de Lançamento</label>
                                        <?php echo montaSelect([2 => 'Pagos', 1 => 'Futuros', 0 => "Todos"], $_REQUEST['status_lancamento'], "id='status_lancamento' name='status_lancamento' class='form-control input-sm validate[required,custom[select]]'") ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <label for="" class="control-label">Agrupamento</label>
                                        <?php echo montaSelect([0 => "Sem Agrupamento", 1 => 'Projeto', 2 => 'Data', 3 => 'Código de Despesas', 4 => 'Prestador de Serviço'], $_REQUEST['agrupamento'], "id='agrupamento' name='agrupamento' class='form-control input-sm validate[required,custom[select]]'") ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="" class="control-label">Gerar por data de:</label>
                                        <?php echo montaSelect([0 => "Vencimento", 1 => 'Pagamento'], $_REQUEST['tp_data'], "id='tp_data' name='tp_data' class='form-control input-sm validate[required,custom[select]]'") ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <!--<label for="" class="control-label">Agrupamento</label>-->
                                        <div class="input-group">
                                            <label class="input-group-addon" for="chkSaida"><input type="checkbox" name="chk[]" id="chkSaida" value="1" <?php echo (empty($_REQUEST['chk']) || in_array(1, $_REQUEST['chk'])) ? 'CHECKED' : null ?>></label>
                                            <label class="form-control" for="chkSaida">Saída</label>
                                            <label class="input-group-addon" for="chkEntrada"><input type="checkbox" name="chk[]" id="chkEntrada" value="2" <?php echo (empty($_REQUEST['chk']) || in_array(2, $_REQUEST['chk'])) ? 'CHECKED' : null ?>></label>
                                            <label class="form-control" for="chkEntrada">Entrada</label>
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
                    <?php if (isset($_REQUEST['data_ini']) && isset($_REQUEST['data_fim']) && isset($_REQUEST['projeto']) && (isset($_REQUEST['filtrar']) || isset($_REQUEST['excel']))) { ?>
                        <table id="tbRelatorio" class="table table-condensed valign-middle table-bordered table-striped">
    <!--                            <tr class="active">
                                <td colspan="3" class="text-left"><?= $master['nome'] ?></td>
                                <td colspan="2" class="text-right"><?= $master['nome'] ?></td>
                            </tr>-->
                            <tr class="active">
                                <td colspan="8" class="text-center text-bold"><?= $master['razao'] ?></td>
                            </tr>
                            <tr class="active">
                                <td colspan="8" class="text-bold text-center">CNPJ: <?= $master['cnpj'] ?></td>
                            </tr>
                            <tr class="active">
                                <td colspan="8" class="text-left"><?= date("d/m/Y H:i:s") ?></td>
                                <!--<td colspan="3" class="text-right">Folha</td>-->
                            </tr>
                            <tr class="active">
                                <td colspan="8" class="text-left text-bold text-uppercase">Razão de <?= $data_ini ?> até <?= $data_fim ?></td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-right text-bold">SALDO ANTERIOR: </td>
                                <td colspan="2" class="text-right text-bold"><?= ($saldo < 0) ? '(' . number_format($saldo * -1, 2, ',', '.') . ')' : number_format($saldo, 2, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td class="text-left text-bold">CÓD</td>
                                <td class="text-left text-bold">DATA</td>
                                <td class="text-left text-bold">COD. DESPESA</td>
                                <td class="text-left text-bold">DESCRIÇÃO</td>
                                <td class="text-left text-bold">Nº NOTA</td>
                                <td class="text-left text-bold">ENTRADA</td>
                                <td class="text-right text-bold">SAÍDA</td>
                                <td class="text-right text-bold">SALDO</td>
                            </tr>

                            <?php
                            foreach ($arrayLancamentos as $key => $row_lanc) { 
                                if($auxAgrupamento != $row_lanc['nomeAgrupamento']) { 
                                    if($count > 0) {
//                                        echo 
//                                        "<tr>
//                                            <td colspan='5' class='text-right warning'></td>
//                                            <td class='text-right warning'>".number_format($totalEntrada, 2, ',', '.')."</td>
//                                            <td class='text-right warning'>".number_format($totalSaida, 2, ',', '.')."</td>
//                                            <td class='warning'></td>
//                                        </tr>";
                                        echo 
                                        "<tr>
                                            <td colspan='7' class='text-right warning'>Total Agrupamento: ".number_format($totalAgrupamento, 2, ',', '.')."</td>
                                            <td class='warning'></td>
                                        </tr>";
                                    }
                                    echo 
                                    "<tr>
                                        <td colspan='8' class='info'>{$row_lanc['nomeAgrupamento']}</td>
                                    </tr>";
                                    $auxAgrupamento = $row_lanc['nomeAgrupamento'];
                                    $totalAgrupamento = 0;
//                                    $totalSaida = $totalEntrada = 0;
                                }
                                $count++;
                                $totalEntrada += ($row_lanc['tipo'] == 'e') ? $row_lanc['valor'] : 0; 
                                $totalSaida += ($row_lanc['tipo'] == 'e') ? 0 : $row_lanc['valor']; 
                                $totalAgrupamento += ($row_lanc['tipo'] == 'e') ? $row_lanc['valor'] : - $row_lanc['valor']; 
                                $saldo += ($row_lanc['tipo'] == 'e') ? $row_lanc['valor'] : - $row_lanc['valor'];
                                $valor = $row_lanc['valor'];
                                ?>
                                <tr class="">
                                    <td class="text-left"><?= $row_lanc['id_saida'] ?></td>
                                    <td class="text-left"><?= implode('/', array_reverse(explode('-', (!empty($row_lanc['data_pg']) && $row_lanc['data_pg'] != '0000-00-00') ? $row_lanc['data_pg'] : $row_lanc['data_vencimento']))) ?></td>
                                    <td class="text-left"><?= $row_lanc['nomeDespesa'] ?></td>
                                    <td class="text-left"><?= $row_lanc['nome'] ?></td>
                                    <td class="text-left"><?= $row_lanc['n_documento'] ?></td>
                                    <td class="text-right"><?= ($row_lanc['tipo'] == 'e') ? number_format($valor, 2, ',', '.') : '' ?></td>
                                    <td class="text-right"><?= ($row_lanc['tipo'] == 's') ? number_format($valor, 2, ',', '.') : '' ?></td>
                                    <td class="text-right <?= ($saldo < 0) ? 'text-danger text-bold' : '' ?>"><?= ($saldo < 0) ? '(' . number_format($saldo * -1, 2, ',', '.') . ')' : number_format($saldo, 2, ',', '.') ?></td>
                                </tr>
                            <?php } ?>
                            <?php if($_REQUEST['agrupamento']) { ?>
                            <tr>
                                <td colspan='7' class='text-right warning'>Total Unidade: <?php echo number_format($totalAgrupamento, 2, ',', '.')?></td>
                                <td class='warning'></td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td colspan='5' class='text-right warning'></td>
                                <td class='text-right warning'><?php echo number_format($totalEntrada, 2, ',', '.') ?></td>
                                <td class='text-right warning'><?php echo number_format($totalSaida, 2, ',', '.') ?></td>
                                <td class='warning'></td>
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