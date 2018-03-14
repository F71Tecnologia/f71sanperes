<?php
// session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/NFSeClass.php");
include("../../classes/global.php");

class soma{
    public $sub;
    public $total_465;
    public $total_ir;
    public $total_inss;
    public $total_iss;
    public $total_bruto;
    public $total_liquido;
    
    function soma($array, $soma = true){
        $this->sub[$array['id_projeto']]['sub_a465'] += ($soma) ? $array['a465'] : $array['a465'] * -1;
        $this->sub[$array['id_projeto']]['sub_a465'] = round($this->sub[$array['id_projeto']]['sub_a465'],4);
        
        $this->sub[$array['id_projeto']]['sub_ir'] += ($soma) ? $array['ir'] : $array['ir'] * -1;
        $this->sub[$array['id_projeto']]['sub_ir'] = round($this->sub[$array['id_projeto']]['sub_ir'],4);
        
        $this->sub[$array['id_projeto']]['sub_inss'] += ($soma) ? $array['inss'] : $array['inss'] * -1;
        $this->sub[$array['id_projeto']]['sub_inss'] = round($this->sub[$array['id_projeto']]['sub_inss'],4);

        $this->sub[$array['id_projeto']]['sub_iss'] += ($soma) ? $array['iss'] : $array['iss'] * -1;
        $this->sub[$array['id_projeto']]['sub_iss'] = round($this->sub[$array['id_projeto']]['sub_iss'],4);

        $this->sub[$array['id_projeto']]['sub_bruto'] += ($soma) ? $array['bruto'] : $array['bruto'] * -1;
        $this->sub[$array['id_projeto']]['sub_bruto'] = round($this->sub[$array['id_projeto']]['sub_bruto'],4);

        $this->sub[$array['id_projeto']]['sub_liquido'] += ($soma) ? $array['liquido'] : $array['liquido'] * -1;
        $this->sub[$array['id_projeto']]['sub_liquido'] = round($this->sub[$array['id_projeto']]['sub_liquido'],4);

        $this->total_465 += ($soma) ? $array['a465'] : $array['a465'] * -1;
        $this->total_465 = round($this->total_465,4);
        
        $this->total_ir += ($soma) ? $array['ir'] : $array['ir'] * -1;
        $this->total_ir = round($this->total_ir,4);
        
        $this->total_inss += ($soma) ? $array['inss'] : $array['inss'] * -1;
        $this->total_inss = round($this->total_inss,4);
        
        $this->total_iss += ($soma) ? $array['iss'] : $array['iss'] * -1;
        $this->total_iss = round($this->total_iss,4);
        
        $this->total_bruto += ($soma) ? $array['bruto'] : $array['bruto'] * -1;
        $this->total_bruto = round($this->total_bruto,4);
        
        $this->total_liquido += ($soma) ? $array['liquido'] : $array['liquido'] * -1;
        $this->total_liquido = round($this->total_liquido,4);
        
    }
}

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$objNFSe = new NFSe();
$global = new GlobalClass();
$objSoma = new soma();

$id_projeto = $_REQUEST['projeto'];
$id_regiao = $usuario['id_regiao'];

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
if (isset($_REQUEST['consultar'])) {
    $data_ini = converteData($_REQUEST['data_ini']);
    $data_fim = converteData($_REQUEST['data_fim']);
    $where_fornecedor = ($_REQUEST['id_fornecedor'] !== '-1') ? "AND REPLACE(REPLACE(REPLACE(c.c_cnpj, '-', ''), '.', ''), '/', '') = '{$_REQUEST['id_fornecedor']}'" : '';
    $where_projeto = ($_REQUEST['id_projeto'] !== '-1') ? "AND a.id_projeto = '{$_REQUEST['id_projeto']}'" : '';
    $where_nota = ($_COOKIE['n']) ? "AND e.Numero = {$_COOKIE['n']}" : null;
//    $where_status = ($_REQUEST['status'] == 1) ? "AND a.n_documento IN (SELECT n_documento FROM saida WHERE status NOT IN (2) AND id_prestador = c.id_prestador)" : null;
////    $where_status = ($_REQUEST['status'] == 2) ? "AND a.n_documento IN (SELECT n_documento FROM saida WHERE id_prestador = c.id_prestador /*AND status > 0*/ GROUP BY n_documento HAVING SUM(status) = (2*COUNT(id_saida)))" : $where_status;
//    $where_status = ($_REQUEST['status'] == 2) 
//        ? "AND a.n_documento IN (
//	 	SELECT n_documento FROM saida WHERE id_saida IN (
//			(
//				SELECT id_saida
//				FROM nfse_saidas
//				WHERE id_nfse = e.id_nfse
//				UNION
//				SELECT id_saida FROM saida WHERE data_conciliar > '0000-00-00' AND id_projeto = e.id_projeto
//			)
//		)
//		GROUP BY n_documento
//                HAVING SUM(status) = (2*COUNT(id_saida))
//            )" 
//        : $where_status;

    $query = "SELECT a.id_saida, a.tipo, SUM(REPLACE(a.valor, ',', '.')) AS sValor, a.status AS sStatus, e.id_nfse, a.data_proc AS data_cad, a.nome AS sNome, a.especifica AS sEspecifica, a.data_pg AS sDataPg, a.agrupada AS sAgrupada,
    s.id_saida AS cIdSaida, s.tipo AS cTipo, SUM(REPLACE(s.valor, ',', '.')) AS cValor, s.status AS cStatus, s.data_proc AS cdata_cad, s.nome AS cNome, s.especifica AS cEspecifica, s.data_pg AS cDataPg, s.agrupada AS cAgrupada,
    g.id_saida AS eIdSaida, g.tipo AS eTipo, SUM(REPLACE(g.valor, ',', '.')) AS eValor, g.status AS eStatus, g.data_proc AS edata_cad, g.nome AS eNome, g.especifica AS eEspecifica, g.data_pg AS eDataPg, g.agrupada AS eAgrupada,
    e.Numero, e.DataEmissao,e.id_projeto,a.data_proc,a.n_documento,b.nome AS projeto_nome, c.c_razao AS prestador_razao, a.id_saida,e.ValorLiquidoNfse AS liquido, e.ValorServicos AS bruto,IF(e.ValorPisCofinsCsll > 0, e.ValorPisCofinsCsll,(e.ValorPis + e.ValorCofins + e.ValorCsll)) AS 'a465', e.ValorIr AS ir, e.ValorInss AS inss, e.ValorIss AS iss
    FROM nfse AS e
    INNER JOIN projeto AS b ON e.id_projeto = b.id_projeto
    INNER JOIN prestadorservico AS c ON e.PrestadorServico = c.id_prestador
    INNER JOIN nfse_saidas AS d ON e.id_nfse = d.id_nfse
    INNER JOIN saida AS a ON d.id_saida = a.id_saida AND e.id_projeto = a.id_projeto
    LEFT JOIN (SELECT * FROM saida WHERE data_conciliar > 0000-00-00 AND status = 2) AS s ON (a.n_documento = s.n_documento AND a.tipo = s.tipo AND a.id_projeto = s.id_projeto)
    -- LEFT JOIN (SELECT id_nfse, MIN(data_cad) AS data_cad FROM nfse_log WHERE status = 2 GROUP BY id_nfse) AS f ON e.id_nfse = f.id_nfse
    LEFT JOIN (SELECT * FROM saida WHERE id_saida_estorno) AS g ON a.id_saida = g.id_saida_estorno
    WHERE /*a.status != 0 AND*/ (e.DataEmissao BETWEEN '{$data_ini}' AND '{$data_fim}') AND e.`status` = 4
    $where_status
    $where_fornecedor
    $where_projeto
    $where_nota
    GROUP BY e.PrestadorServico, e.Numero, a.tipo, a.id_saida
    ORDER BY a.id_saida, a.id_projeto, a.data_vencimento";
//    $query = "SELECT a.data_vencimento,a.id_projeto,a.data_proc,a.n_documento,b.nome AS projeto_nome, c.c_razao AS prestador_razao, a.id_saida,e.ValorLiquidoNfse AS liquido, e.ValorServicos AS bruto,(e.ValorPis + e.ValorCofins + e.ValorCsll) AS 'a465', e.ValorIr AS ir, e.ValorInss AS inss, e.ValorIss AS iss
//                FROM saida AS a
//                LEFT JOIN projeto AS b ON a.id_projeto = b.id_projeto
//                LEFT JOIN prestadorservico AS c ON a.id_prestador = c.id_prestador
//                LEFT JOIN nfse_saidas AS d ON a.id_saida = d.id_saida
//                LEFT JOIN nfse AS e ON d.id_nfse = e.id_nfse
//                WHERE a.status != 0 AND a.id_regiao = $id_regiao AND (data_vencimento BETWEEN '$data_ini' AND '$data_fim') $where_fornecedor $where_projeto
//                GROUP BY a.n_documento
//                ORDER BY a.id_projeto, a.data_vencimento";
    if($_COOKIE['debug'] == 666) { print_array($query);} 
    $result = mysql_query($query) or die($query . " <br> " . mysql_error());
    
    $arrayNaoPagas = [];
    while ($row = mysql_fetch_assoc($result)) {
        
        if($row['tipo'] == 349 || stripos($row['sNome'],"IR") === 0 || stripos($row['sEspecifica'],"IR") === 0){
            $tipo = 'ir';
        } else if($row['tipo'] == 395|| stripos($row['sNome'],"PIS/COFINS/CSLL") === 0 || stripos($row['sEspecifica'],"PIS/COFINS/CSLL") === 0){
            $tipo = 'pis';
        } else if($row['tipo'] == 348|| stripos($row['sNome'],"INSS") === 0 || stripos($row['sEspecifica'],"INSS") === 0){
            $tipo = 'inss';
        } else if($row['tipo'] == 346|| stripos($row['sNome'],"ISS") === 0 || stripos($row['sEspecifica'],"ISS") === 0){
            $tipo = 'iss';
        } else {
            $tipo = 'valor';
        }

        if(!array_key_exists($row['id_nfse'], $saidas[$row['id_projeto']])) {
            $saidas[$row['id_projeto']][$row['id_nfse']] = $row;
            $objSoma->soma($row);
        }
//        echo "({$row['sStatus']} + {$row['cStatus']}) < 2 && ({$row['sAgrupada']} + {$row['cAgrupada']}) == 0<br>";
        if(($row['sStatus'] + $row['cStatus']) < 2 && ($row['sAgrupada'] + $row['cAgrupada']) == 0 && !array_key_exists($row['id_nfse'],$arrayNaoPagas[$row['id_projeto']])){
//            print_array($row);
            $arrayNaoPagas[$row['id_projeto']][$row['id_nfse']] = 1;
        }
        
        $saidas[$row['id_projeto']][$row['id_nfse']]['saidas'][$tipo]['v'] = ($row['eIdSaida']) ? $row['eValor'] : ((!$row['cIdSaida']) ? $row['sValor'] : $row['cValor']);
        $saidas[$row['id_projeto']][$row['id_nfse']]['saidas'][$tipo]['s'] = ($row['eIdSaida']) ? $row['eStatus'] : ((!$row['cIdSaida']) ? $row['sStatus'] : $row['cStatus']);
        $saidas[$row['id_projeto']][$row['id_nfse']]['saidas'][$tipo]['n'] = ($row['eIdSaida']) ? $row['eNome'] : ((!$row['cIdSaida']) ? $row['sNome'] : $row['cNome']);
        $saidas[$row['id_projeto']][$row['id_nfse']]['saidas'][$tipo]['d'] = ($row['eIdSaida']) ? $row['eDataPg'] : ((!$row['cIdSaida']) ? $row['sDataPg'] : $row['cDataPg']);
        $saidas[$row['id_projeto']][$row['id_nfse']]['saidas'][$tipo]['saida'] = ($row['eIdSaida']) ? $row['eIdSaida'] : ((!$row['cIdSaida']) ? $row['id_saida'] : $row['cIdSaida']);
        if($row['eIdSaida']){
            echo $saidas[$row['id_projeto']][$row['id_nfse']]['saidas'][$tipo]['cor'] = ($row['eAgrupada']) ? 'info' : (($row['eStatus'] == 2) ? 'success' : (($row['eStatus'] == 1) ? 'warning' : 'danger'));
        } else {
            if(!$row['cIdSaida']){
                $saidas[$row['id_projeto']][$row['id_nfse']]['saidas'][$tipo]['cor'] = ($row['sAgrupada']) ? 'info' : (($row['sStatus'] == 2) ? 'success' : (($row['sStatus'] == 1) ? 'warning' : 'danger'));
            } else {
                $saidas[$row['id_projeto']][$row['id_nfse']]['saidas'][$tipo]['cor'] = ($row['cAgrupada']) ? 'info' : (($row['cStatus'] == 2) ? 'success' : (($row['cStatus'] == 1) ? 'warning' : 'danger'));
            }
        }
    }
    if($_COOKIE['debug'] == 666) { print_array($saidas); print_array($arrayNaoPagas); } 
}

if($_REQUEST['status'] == 1) {
    foreach ($saidas as $key => $value) {
        foreach ($value as $k => $v) {
            if(!array_key_exists($k, $arrayNaoPagas[$key])) {
                
                $objSoma->soma($saidas[$key][$k], false);
                unset($saidas[$key][$k]);
            }
        }
    }
} else if($_REQUEST['status'] == 2) {
    foreach ($arrayNaoPagas as $key => $value) {
        foreach ($value as $k => $v) {
            
            $objSoma->soma($saidas[$key][$k], false);
            unset($saidas[$key][$k]);
        }
    }
}
$saidas = array_filter($saidas);

$selectProjeto = $global->carregaProjetosByMaster($usuario['id_master'], array('-1' => 'Todos'));

$query = "SELECT REPLACE(REPLACE(REPLACE(a.c_cnpj, '-', ''), '.', ''), '/', '') AS cnpj, a.c_razao 
            FROM prestadorservico AS a
            iNNER JOIN saida AS b ON a.id_prestador = b.id_prestador
            WHERE b.status = 2 AND b.id_regiao = $id_regiao
            GROUP BY REPLACE(REPLACE(REPLACE(a.c_cnpj, '-', ''), '.', ''), '/', '')
            ORDER BY a.c_razao";
$result = mysql_query($query);
$selectFornecedor[-1] = "Todos";
while ($row = mysql_fetch_assoc($result)) {
    $selectFornecedor[$row['cnpj']] = $row['c_razao'];
}

$dataIni = (isset($_REQUEST['data_ini'])) ? $_REQUEST['data_ini'] : '';
$dataFim = (isset($_REQUEST['data_fim'])) ? $_REQUEST['data_fim'] : '';
$op_fornecedor = (isset($_REQUEST['id_fornecedor'])) ? $_REQUEST['id_fornecedor'] : '';
$op_projeto = (isset($_REQUEST['id_projeto'])) ? $_REQUEST['id_projeto'] : '';

$nome_pagina = 'Relatório de Notas à Pagar';
$breadcrumb_config = array("nivel" => "../", "key_btn" => "4", "area" => "Financeiro", "id_form" => "form1", "ativo" => $nome_pagina);
$breadcrumb_pages = array("Principal" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />

    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <!--<div class="container-full over-x">-->
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - <?= $nome_pagina ?></small></h2></div>
            <?php if (isset($_GET['s'])) { ?><div class="alert alert-dismissable alert-success text-bold"><button type="button" class="close" data-dismiss="alert">×</button>Saídas Geradas com Sucesso!</div><?php } ?>
            <?php if (isset($_GET['e'])) { ?><div class="alert alert-dismissable alert-danger text-bold"><button type="button" class="close" data-dismiss="alert">×</button>Erro: <?= $_GET['e'] ?>. Entre em contato com o suporte.</div><?php } ?>
            <form action="rel_impostos_retidos.php" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="fornecedor" class="col-lg-2 control-label">Projeto</label>
                            <div class="col-lg-9">
                                <?= montaSelect($selectProjeto, $op_projeto, 'name="id_projeto" id="projeto" class="input form-control"') ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="fornecedor" class="col-lg-2 control-label">Fornecedor</label>
                            <div class="col-lg-9">
                                <?= montaSelect($selectFornecedor, ($op_fornecedor) ? $op_fornecedor : -1, 'name="id_fornecedor" id="fornecedor" class="input form-control"') ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="status" class="col-lg-2 control-label">Status da Nota</label>
                            <div class="col-lg-9">
                                <?= montaSelect([0 => "Todas as Notas", 1 => "Notas à Pagar", 2 => "Notas Pagas"], $_REQUEST['status'], 'name="status" id="status" class="input form-control"') ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Visualizar Notas Emitidas em</label>
                            <div class="col-lg-4">
                                <div class="input-group">
                                    <input type="text" class="input form-control data validate[required]" name="data_ini" id="data_ini" placeholder="Data Inicial" value="<?php echo ($dataIni) ? $dataIni : date('01/m/Y'); ?>">
                                    <span class="input-group-addon">até</span>
                                    <input type="text" class="input form-control data validate[required]" name="data_fim" id="data_fim" placeholder="Data Final" value="<?php echo ($dataFim) ? $dataFim : date('t/m/Y'); ?>">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="submit" name="consultar" id="filt" value="Consultar" class="btn btn-primary" />
                    </div>
                </div>
                <table class='table table-bordered table-condensed text-sm valign-middle'>
                    <thead>
                        <tr class="">
                            <th class="text-center" style="width: 200px;">Saída não encontrada</th>
                            <th class="text-center danger" style="width: 200px;">Saída Excluida</th>
                            <th class="text-center warning" style="width: 200px;">Saída À Pagar</th>
                            <th class="text-center success" style="width: 200px;">Saída Paga</th>
                            <th class="text-center info" style="width: 200px;">Saída Agrupada</th>
                        </tr>
                    </thead>
                </table>
                <?php
                if ($_REQUEST['consultar']) {
                    if (count($saidas) > 0) {
                        foreach ($saidas as $key => $projetos) {
                            ?>
                            <p style="text-align: right; margin-top: 20px"><button type="button" onclick="tableToExcel('tbRelatorio<?= $key?>', 'Pagamento de Fornecedores')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button></p>
                            <table class='table table-bordered table-hover table-striped table-condensed text-sm valign-middle' id="tbRelatorio<?= $key?>">
                                <thead>
                                    <tr class="bg-primary">
                                        <th class="text-center" style="width: 200px;">Projeto</th>
                                        <th class="text-center">Fornecedor</th>
                                        <th class="text-center">N&ordm;</th>
                                        <th class="text-center">Cadastro</th>
                                        <th class="text-center">Emissão</th>
                                        <th class="text-center" style="width: 100px;">Valor Bruto (R$)</th>
                                        <th class="text-center" style="width: 100px;">4,65&percnt; (R$)</th>
                                        <th class="text-center" style="width: 100px;">IR (R$)</th>
                                        <th class="text-center" style="width: 100px;">INSS (R$)</th>
                                        <th class="text-center" style="width: 100px;">ISS (R$)</th>
                                        <th class="text-center" style="width: 100px;">Valor Liquido (R$)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($projetos as $value) { ?>
                                        <tr>
                                            <td><?= $value['projeto_nome'] ?> </td>
                                            <td><?= $value['prestador_razao'] ?> </td>
                                            <td class="text-center"><?= $value['Numero'] ?> </td>
                                            <td class="text-center"><?= ConverteData($value['data_cad'],'d/m/Y') ?> </td>
                                            <td class="text-center"><?= ConverteData($value['DataEmissao'],'d/m/Y') ?> </td>
                                            <td class="text-right"><?= number_format($value['bruto'], 2, ',', '.') ?></td>
                                            <td class="text-right <?php echo ($value['a465'] > 0) ? $value['saidas']['pis']['cor'] : null ?>">
                                                <?php echo ((float) $value['saidas']['pis']['v'] != (float) $value['a465']) ? '<i class="fa fa-exclamation-triangle"></i> ' : null ?>
                                                <?php echo ($value['saidas']['pis']['saida']) ? '<u class="verSaida pointer" data-key="'.$value['saidas']['pis']['saida'].'" data-s="'.$value['saidas']['pis']['cor'].'">' . number_format($value['a465'], 2, ',', '.') . '</u>' : number_format($value['a465'], 2, ',', '.') ?>
                                                <?php echo ($value['saidas']['pis']['s'] == 2) ? '<br>Dt. Bx. '.ConverteData($value['saidas']['pis']['d'],'d/m/Y') : null ?>
                                            </td>
                                            <td class="text-right <?php echo ($value['ir'] > 0) ? $value['saidas']['ir']['cor'] : null ?>">
                                                <?php echo ((float) $value['saidas']['ir']['v'] != (float) $value['ir']) ? '<i class="fa fa-exclamation-triangle"></i> ' : null ?>
                                                <?php echo ($value['saidas']['ir']['saida']) ? '<u class="verSaida pointer" data-key="'.$value['saidas']['ir']['saida'].'" data-s="'.$value['saidas']['ir']['cor'].'">' . number_format($value['ir'], 2, ',', '.') . '</u>' : number_format($value['ir'], 2, ',', '.') ?>
                                                <?php echo ($value['saidas']['ir']['s'] == 2) ? '<br>Dt. Bx. '.ConverteData($value['saidas']['ir']['d'],'d/m/Y') : null ?>
                                            </td>
                                            <td class="text-right <?php echo ($value['inss'] > 0) ? $value['saidas']['inss']['cor'] : null ?>">
                                                <?php echo ((float) $value['saidas']['inss']['v'] != (float) $value['inss']) ? '<i class="fa fa-exclamation-triangle"></i> ' : null ?>
                                                <?php echo ($value['saidas']['inss']['saida']) ? '<u class="verSaida pointer" data-key="'.$value['saidas']['inss']['saida'].'" data-s="'.$value['saidas']['inss']['cor'].'">' . number_format($value['inss'], 2, ',', '.') . '</u>' : number_format($value['inss'], 2, ',', '.') ?>
                                                <?php echo ($value['saidas']['inss']['s'] == 2) ? '<br>Dt. Bx. '.ConverteData($value['saidas']['inss']['d'],'d/m/Y') : null ?>
                                            </td>
                                            <td class="text-right <?php echo ($value['iss'] > 0) ? $value['saidas']['iss']['cor'] : null ?>">
                                                <?php echo ((float) $value['saidas']['iss']['v'] != (float) $value['iss']) ? '<i class="fa fa-exclamation-triangle"></i> ' : null ?>
                                                <?php echo ($value['saidas']['iss']['saida']) ? '<u class="verSaida pointer" data-key="'.$value['saidas']['iss']['saida'].'" data-s="'.$value['saidas']['iss']['cor'].'">' . number_format($value['iss'], 2, ',', '.') . '</u>' : number_format($value['iss'], 2, ',', '.') ?>
                                                <?php echo ($value['saidas']['iss']['s'] == 2) ? '<br>Dt. Bx. '.ConverteData($value['saidas']['iss']['d'],'d/m/Y') : null ?>
                                            </td>
                                            <td class="text-right <?php echo ($value['liquido'] > 0) ? $value['saidas']['valor']['cor'] : null ?>">
                                                <?php echo ((float) $value['saidas']['valor']['v'] != (float) $value['liquido']) ? '<i class="fa fa-exclamation-triangle"></i> ' : null ?>
                                                <?php echo ($value['saidas']['valor']['saida']) ? '<u class="verSaida pointer" data-key="'.$value['saidas']['valor']['saida'].'" data-s="'.$value['saidas']['valor']['cor'].'">' . number_format($value['liquido'], 2, ',', '.') . '</u>' : number_format($value['liquido'], 2, ',', '.') ?>
                                                <?php echo ($value['saidas']['valor']['s'] == 2) ? '<br>Dt. Bx. '.ConverteData($value['saidas']['valor']['d'],'d/m/Y') : null ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-right text-bold">Total:</td>
                                        <td class="text-right"> <?= number_format($objSoma->sub[$key]['sub_bruto'], 2, ',', '.') ?></td>
                                        <td class="text-right"> <?= number_format($objSoma->sub[$key]['sub_a465'], 2, ',', '.') ?></td>
                                        <td class="text-right"> <?= number_format($objSoma->sub[$key]['sub_ir'], 2, ',', '.') ?></td>
                                        <td class="text-right"> <?= number_format($objSoma->sub[$key]['sub_inss'], 2, ',', '.') ?></td>
                                        <td class="text-right"> <?= number_format($objSoma->sub[$key]['sub_iss'], 2, ',', '.') ?></td>
                                        <td class="text-right"> <?= number_format($objSoma->sub[$key]['sub_liquido'], 2, ',', '.') ?></td>
                                    </tr>
                                </tfoot>
                            </table>

                        <?php } ?>
                        <div class="row">
                            <div class="col-md-4">
                                <table class="table table-bordered table-condensed text-sm  table-striped">
                                    <thead>
                                        <tr class="bg-primary">
                                            <th colspan="2" class="text-center">Totalizadores</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-bold text-right">4,65&percnt;</td>
                                            <td><?= number_format($objSoma->total_465, 2, ',', '.') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-bold text-right">IR</td>
                                            <td><?= number_format($objSoma->total_ir, 2, ',', '.') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-bold text-right">INSS</td>
                                            <td><?= number_format($objSoma->total_inss, 2, ',', '.') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-bold text-right">ISS</td>
                                            <td><?= number_format($objSoma->total_iss, 2, ',', '.') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-bold text-right">Total:</td>
                                            <td><?= number_format($objSoma->total_465 + $objSoma->total_inss + $objSoma->total_ir + $objSoma->total_iss, 2, ',', '.') ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-danger top30">
                            Nenhum registro encontrado
                        </div>
                        <?php
                    }
                }
                ?>
            </form>
            <?php include('../../template/footer.php'); ?>
        </div>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
        $(function () {
            $("#form1").validationEngine({promptPosition: "topRight"});
            $("body").on('click', ".verSaida", function(){
                var id = $(this).data("key");
                var cor = $(this).data("s");
                cria_carregando_modal();
                $.post("../actions/action.saida.php", { bugger:Math.random(), action: 'verSaida', id:id }, function(resultado){
                    BootstrapDialog.show({
                        size: BootstrapDialog.SIZE_WIDE,
                        nl2br: false,
                        title: 'Detalhe da Saida ID:' + id,
                        message: resultado,
                        type: 'type-' + cor,
                        buttons: []
                    });
                    remove_carregando_modal();
                });
            });
        });
        </script>
    </body>
</html>
