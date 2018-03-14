<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/BancoClass.php");
include("../../classes/global.php");
include("../../classes/c_classificacaoClass.php");
//include("../../classes/c_planodecontasClass.php");
include("../../classes/ContabilLoteClass.php");
require_once("../../classes/pdf/fpdf.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$id_master = $usuario['id_master'];

$query_master = "SELECT * FROM master WHERE id_master = $id_master";
$master = mysql_fetch_assoc(mysql_query($query_master));

$query_projeto = "SELECT * FROM rhempresa WHERE id_projeto = '{$_REQUEST['projeto']}'";
$projeto = mysql_fetch_assoc(mysql_query($query_projeto));

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;

$objClassificador = new c_classificacaoClass();

if ($_REQUEST['method'] == 'select_contas') {
    $sqlConta = "SELECT * FROM contabil_planodecontas WHERE id_projeto = {$_REQUEST['projeto']} AND status = 1 ORDER BY classificador";
    $qryConta = mysql_query($sqlConta);
    ?>
    <option value=''>Todas</option>
    <?php while ($rowConta = mysql_fetch_assoc($qryConta)) { ?>
        <option value="<?= $rowConta['id_conta'] ?>" <?= ($rowConta['id_conta'] == $_REQUEST['conta']) ? 'selected=""' : '' ?> ><?= $rowConta['classificador'] ?> - <?= utf8_encode($rowConta['descricao']) ?></option>
        <?php
    }

    exit;
}

$id_projeto = $_REQUEST['projeto'];
$data_ini = (!empty($_REQUEST['data_ini'])) ? $_REQUEST['data_ini'] : "01/" . date('m/Y');
$data_fim = (!empty($_REQUEST['data_fim'])) ? $_REQUEST['data_fim'] : date('t', date('m-Y') . "-01") . date('/m/Y');

if (isset($_REQUEST['filtrar']) || isset($_REQUEST['filtra'])) {

    $data_ini_bd = implode('-', array_reverse(explode('/', $data_ini)));
    $data_fim_bd = implode('-', array_reverse(explode('/', $data_fim)));
    if (!empty($_REQUEST['contas'])) {
        $auxWhere = " AND A.id_conta = {$_REQUEST['contas']} ";
    }
    $sql_conta = "SELECT A.id_conta, A.acesso, A.classificador, A.descricao, RPAD(REPLACE(A.classificador,'.',''),'14','0') indice, 
                (SELECT IF(SUBSTRING(A.classificador, 1, 1) = 1 OR SUBSTRING(A.classificador, 1, 4) = 4.02, SUM(IF(B.tipo = 2, B.valor, -B.valor)), SUM(IF(B.tipo = 1, B.valor, -B.valor))) FROM contabil_lancamento_itens B INNER JOIN contabil_lancamento C ON (C.id_lancamento = B.id_lancamento AND C.`status` != 0) WHERE B.id_conta = A.id_conta AND B.`status` != 0 AND A.`status` = 1 AND B.tipo IN(1,2) AND C.data_lancamento < '$data_ini_bd' AND C.id_projeto = {$_REQUEST['projeto']}) saldo_anterior,
                (SELECT IF(SUBSTRING(A.classificador, 1, 1) = 1 OR SUBSTRING(A.classificador, 1, 4) = 4.02, SUM(IF(B.tipo = 2, B.valor, -B.valor)), SUM(IF(B.tipo = 1, B.valor, -B.valor))) FROM contabil_lancamento_itens B INNER JOIN contabil_lancamento C ON (C.id_lancamento = B.id_lancamento AND C.`status` != 0) WHERE B.id_conta = A.id_conta AND B.`status` != 0 AND A.`status` = 1 AND B.tipo IN(1,2) AND C.data_lancamento <= '$data_fim_bd' AND C.id_projeto = {$_REQUEST['projeto']}) saldo_atual, 
                 E.nome nome, E.endereco endereco, E.cnpj cnpj    
                FROM contabil_planodecontas A
                INNER JOIN rhempresa E ON (E.id_projeto = {$_REQUEST['projeto']})
                WHERE A.status = 1 AND A.id_projeto = {$_REQUEST['projeto']} $auxWhere
                GROUP BY A.id_conta 
                ORDER BY A.classificador";

    $qry_conta = mysql_query($sql_conta);
    while ($row_conta = mysql_fetch_assoc($qry_conta)) {
        $saldoArray[$row_conta['id_conta']] = $row_conta;
    }

    $arrayProjeto = $objClassificador->carregarProjeto($_REQUEST['projeto']);
}
//print_array($saldoArray);
// Configurações header para forçar o download
//header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
//header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
//header ("Cache-Control: no-cache, must-revalidate");
//header ("Pragma: no-cache");
//header ("Content-type: application/x-msexcel");
//header ("Content-Disposition: attachment; filename=\"MODELO - LALUR.xls\"" );
//header ("Content-Description: PHP Generated Data" );

$nome_pagina = "Livro Razão";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "frmplanodeconta");
$breadcrumb_pages = array("Relatórios Contábeis" => "index.php");

if ($_REQUEST['filtrar'] == 'Imprimir') {

    class PDF extends FPDF {

        public $master, $projeto;

        function Header() {
            $this->SetFont('Arial', 'B', 7);
            $this->SetLineWidth(0);
            $this->Image("../../imagens/logomaster{$this->master['id_master']}.gif", 1.5, .7, 2);
            $this->Cell(3);
            $this->Cell(10, .3, $this->projeto['nome'], 0, 0, 'L');
            $this->SetFont('Arial', 'B', 5.5);
            $this->SetLineWidth(0);
            $this->Cell(6, .3, 'RAZÃO ANALITÍCO INDIVIDUAL', 0, 0, 'R');
            $this->Ln();
            $this->SetFont('Arial', 'B', 6);
            $this->SetLineWidth(0);
            $this->Cell(3);
            $this->Cell(10, .3, 'CNPJ ' . $this->projeto['cnpj'], 0, 0, 'L');
            $this->SetFont('Arial', 'B', 5.5);
            $this->SetLineWidth(0);
            $this->Cell(6, .5, $_REQUEST['data_ini'] . " a " . $_REQUEST['data_fim'], 0, 0, 'R');
            $this->Ln();
            $this->SetFont('Arial', 'B', 6);
            $this->SetLineWidth(0);
            $this->Ln();
            $this->Cell(19, 0, NULL, 0, 0, 'B', 'C');
            $this->Ln();
            $this->Cell(3, .5);
            $this->Ln();
            $this->SetFont('Arial', 'B', 5);
            $this->SetLineWidth(0);
            $this->Cell(1.2, .3, 'LCTO', 0, 0, 'L');
            $this->Cell(1.2, .3, 'DCTO', 0, 0, 'L');
            $this->Cell(1.1, .3, 'DATA', 0, 0, 'L');
            $this->Cell(1.5, .3, 'C/PARTIDA', 0, 0, 'L');
            $this->Cell(8, .3, 'HISTÓRICO', 0, 0, 'L');
            $this->Cell(2, .3, 'DÉBITO R$', 0, 0, 'R');
            $this->Cell(2, .3, 'CRÉDITO R$', 0, 0, 'R');
            $this->Cell(2, .3, 'SALDO R$', 0, 0, 'R');
            $this->Ln();
            $this->Cell(19, 0, NULL, 0, 0, 'B', 'C');
        }

        function Footer() {
            // Position at 1.5 cm from bottom
            $this->SetY(-1);
            // Arial italic 8
            $this->SetFont('Arial', NULL, 6.5);
            $this->SetLineWidth(0);
            // Page number
            $this->Cell(16, .8, 'F71 SISTEMAS WEB - módulo contábil', 'T', 0, 'L');
            $this->Cell(3, .8, 'Pagina ' . $this->PageNo(), 'T', 0, 'R');
        }

    }

    $pdf = new PDF('P', 'cm', 'A4');
    $pdf->master = $master;
    $pdf->projeto = $arrayProjeto;

    $pdf->setMargins(1, 1, 1);
    $pdf->AddPage();

    $pdf->SetAutoPageBreak(1, 1.5);


    foreach ($saldoArray as $id_conta => $value) {
        $saldo_anterior = $value['saldo_anterior']; ///RPAD(REPLACE(A.classificador,'.',''),'14','0') indice,
        $saldo_atual = $value['saldo_atual'];
        $sql_lanc = "SELECT A.id_lancamento, D.classificador, D.descricao nomeclatura, B.id_lancamento_itens lcto, C.id_lancamento_itens cpartida, D.acesso, C.historico AS historico, A.historico AS descricao, D.natureza, B.tipo, B.valor, DATE_FORMAT(A.data_lancamento, '%d/%m/%Y') data_lancamento, B.id_conta
                    FROM contabil_lancamento A
                    INNER JOIN contabil_lancamento_itens B ON(B.id_lancamento = A.id_lancamento AND B.`status` != 0)
                    INNER JOIN contabil_lancamento_itens C ON(C.id_lancamento = B.id_lancamento AND B.tipo != C.tipo AND C.`status` != 0)
                    INNER JOIN contabil_planodecontas D ON(D.id_conta = C.id_conta AND D.id_conta != {$id_conta}) 
                    WHERE B.id_conta = {$id_conta} AND A.`status` != 0 AND A.data_lancamento BETWEEN '$data_ini_bd' AND '$data_fim_bd'
                    GROUP BY A.id_lancamento, B.id_lancamento_itens
                    ORDER BY A.data_lancamento, B.tipo DESC, B.valor DESC";

        $query_lanc = mysql_query($sql_lanc) or die(mysql_error());
        $qtd_lanc = mysql_num_rows($query_lanc);

        $pdf->SetFont('Arial', 'B', 6.5);
        $pdf->Cell(19, 0, NULL, 0, 0, 'B', 'C');
        $pdf->Ln(.3);
        $pdf->SetLineWidth(0);
        $pdf->Cell(2.2, .35, 'CONTA', 0, 0);
        $pdf->Cell(1, .35, $value['acesso'], 0, 0);
        $pdf->Cell(3, .35, $value['indice'], 0, 0);
        $pdf->Cell(8, .35, $value['descricao'], 0, 0);
        $pdf->SetLineWidth(0);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 6);
        $pdf->SetLineWidth(0);
        $pdf->Cell(5);
        $pdf->Cell(2, .35, 'SALDO INICIAL', null, 0, 0);
        $pdf->Cell(10);
        $pdf->Cell(2, .35, ($saldo_anterior < 0) ? '(' . number_format($saldo_anterior * -1, 2, ',', '.') . ')' : number_format($saldo_anterior, 2, ',', '.'), NULL, 0, 'R');

        while ($row_lanc = mysql_fetch_assoc($query_lanc)) {
            $saldo1 = $saldo_anterior;
            $resumo_total[$value['acesso']][$row_lanc['tipo']] += $row_lanc['valor'];
            $valor = $row_lanc['valor'];
            $saldo_dia += $row_lanc['data_lancamento'];

            if (substr($value['classificador'], 0, 1) == '2' || substr($value['classificador'], 0, 4) == '4.01') {
                if ($row_lanc['tipo'] == 2) {
                    $saldo1 -= round($valor, 2);
                } elseif ($row_lanc['tipo'] == 1) {
                    $saldo1 += round($valor, 2);
                }
            } elseif (substr($value['classificador'], 0, 1) == '1' || substr($value['classificador'], 0, 4) == '4.02') {
                if ($row_lanc['tipo'] == 1) {
                    $saldo1 -= round($valor, 2);
                } elseif ($row_lanc['tipo'] == 2) {
                    $saldo1 += round($valor, 2);
                }
            }

            $saldo1 = round($saldo_anterior, 2) + round($saldo, 2);

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetLineWidth(0);
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 6);
            $pdf->SetLineWidth(0);
            $pdf->Cell(1.2, .35, $row_lanc['lcto'], 0, 0);
            $pdf->Cell(1.1);
            $pdf->Cell(1.1, .35, substr($row_lanc['data_lancamento'], 0, 2) . '/' . substr($row_lanc['data_lancamento'], 3, 2), 0, 0);
            $pdf->Cell(1.6, .35, $row_lanc['acesso'], 0, 0);
            $pdf->Cell(8, .35, substr($row_lanc['descricao'].' '.$row_lanc['historico'], 0, 72), 0, 0);
            $pdf->Cell(2, .35, ($row_lanc['tipo'] == 2) ? ($valor < 0) ? "(" . number_format($valor * -1, 2, ',', '.') . ")" : number_format($valor, 2, ',', '.') : '', NULL, 0, 'R');
            $pdf->Cell(2, .35, ($row_lanc['tipo'] == 1) ? ($valor < 0) ? "(" . number_format($valor * -1, 2, ',', '.') . ")" : number_format($valor, 2, ',', '.') : '', NULL, 0, 'R');
        }

        $pdf->Ln();
        $pdf->SetFont('Arial', '', 6);
        $pdf->SetLineWidth(0); // $row_lanc['data_lancamento']
        $pdf->Cell(5);
        $pdf->Cell(2, .35, 'SALDO FINAL', null, 0, 0);
        $pdf->Cell(6.1);
        $pdf->Cell(2, .35, number_format($resumo_total[$value['acesso']][2], 2, ',', '.'), NULL, 0, 'R');
        $pdf->Cell(2, .35, number_format($resumo_total[$value['acesso']][1], 2, ',', '.'), NULL, 0, 'R');
        $pdf->Cell(2, .35, (round($saldo_atual, 2) < 0) ? '(' . number_format($saldo_atual * -1, 2, ',', '.') . ')' : number_format($saldo_atual, 2, ',', '.'), NULL, 0, 'R');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(19, 0, NULL, 0, 0, 'B', 'C');
        $pdf->Ln();

        $valor = 0;
        $saldo_dia = 0;
        $saldo = 0;
        $saldo1 = 0;

//        $total[$value['acesso']][$row_lanc['tipo']] += $row_lanc['valor'];
    }
    $pdf->Output($_REQUEST['projeto'] . '_' . substr($_REQUEST['data_ini'], 0, 2) . substr($_REQUEST['data_ini'], 3, 2) . substr($_REQUEST['data_ini'], 6, 4) . substr($_REQUEST['data_fim'], 0, 2) . substr($_REQUEST['data_fim'], 3, 2) . substr($_REQUEST['data_fim'], 6, 4) . 'R.pdf', 'D');


    $pdf->Output();
} else {
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
                        <div class="page-header box-contabil-header hidden-print">
                            <h2><?php echo $icon['38'] ?> - Contabilidade <small>- <?= $nome_pagina ?></small></h2>
                        </div>
                        <form action="" method="post" name="form" id="form" class="form-horizontal top-margin hidden-print" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="projeto1" class="col-sm-2 text-sm control-label">Projeto</label>
                                        <div class="col-sm-4"><?= montaSelect(getProjetos($usuario['id_regiao']), $id_projeto, "id='projeto' name='projeto' class='form-control input-sm validate[required,custom[select]]'") ?></div>
                                        <label for="" class="col-sm-1 control-label">Período</label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <input type="text" id='data_ini' name='data_ini' class='text-center data validate[required,custom[select]] form-control' value="<?= $data_ini ?>">
                                                <div class="input-group-addon">até</div>
                                                <input type="text" id='data_fim' name='data_fim' class='text-center data validate[required,custom[select]] form-control' value="<?= $data_fim ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="conta" class="col-sm-2 text-sm control-label">Conta</label>
                                        <div class="col-sm-9"><select id='contas' name='contas' class='form-control input-sm'><option value="">SELECIONE O PROJETO</option></select></div>
                                    </div>
                                </div>
                                <div class="panel-footer text-right">
                                    <?php if (count($saldoArray) > 0) { ?>
                                        <button type="button" onclick="tableToExcel('tbRelatorio', 'Livro Razao')" name="Excel" value="Excel" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Exportar Excel</button>
                                        <button type="submit" id="" name="filtrar" value="Imprimir" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Impressão</button>
                                        <input type="hidden" id="" name="filtra" value="<?= $_REQUEST['filtra'] ?>">

                                    <?php } ?>
                                    <button type="submit" id="criar" name="filtrar" value="Filtrar" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filtrar</button>
                                </div>
                            </div>
                        </form>
                        <?php if (isset($_REQUEST['filtrar']) || isset($_REQUEST['filtra'])) { ?>
                            <table id="tbRelatorio" class="table table-condensed valign-middle">
                                <tr class="active">
                                    <td colspan="3" class="text-left"><?= $master['nome'] ?></td>
                                    <td colspan="3" class="text-right"><?= $master['nome'] ?></td>
                                </tr>
                                <tr class="active">
                                    <td colspan="6" class="text-center text-bold"><?= $master['razao'] ?></td>
                                </tr> 
                                <tr class="active">
                                    <td colspan="6" class="text-bold text-center">CNPJ: <?= $projeto['cnpj'] ?></td>
                                </tr>
                                <tr class="active">
                                    <td colspan="3" class="text-left"><?= date("d/m/Y H:i:s") ?></td>
                                    <td colspan="2" class="text-right">Folha</td>
                                    <td class="text-center">1</td>
                                </tr>
                                <?php
                                foreach ($saldoArray as $id_conta => $value) {
                                    $saldo_anterior = $value['saldo_anterior'];
                                    $saldo_antual = $value['saldo_atual'];
                                    $sql_lanc = "SELECT A.id_lancamento, D.descricao nomeclatura, D.classificador, D.acesso, C.historico AS historico, A.historico AS descricao, D.natureza, B.tipo, B.valor, DATE_FORMAT(A.data_lancamento, '%d/%m/%Y') data_lancamento, B.id_conta
                                        FROM contabil_lancamento A
                                        INNER JOIN contabil_lancamento_itens B ON(B.id_lancamento = A.id_lancamento AND B.`status` != 0)
                                        INNER JOIN contabil_lancamento_itens C ON(C.id_lancamento = B.id_lancamento AND B.tipo != C.tipo AND C.`status` != 0)
                                        INNER JOIN contabil_planodecontas D ON(D.id_conta = C.id_conta AND D.id_conta != {$id_conta}) 
                                        WHERE B.id_conta = {$id_conta} AND A.`status` != 0 AND A.data_lancamento BETWEEN '$data_ini_bd' AND '$data_fim_bd'
                                        GROUP BY A.id_lancamento, B.id_lancamento_itens
                                        ORDER BY A.data_lancamento, B.tipo DESC, B.valor DESC";
                                    $query_lanc = mysql_query($sql_lanc) or die(mysql_error());
                                    $qtd_lanc = mysql_num_rows($query_lanc);
                                    ?>
                                    <tr class="active"> 
                                        <td colspan="6" class="text-left text-bold text-uppercase">Razão da Conta: <?= $value['classificador'] ?> - <?= $value['descricao'] ?></td>
                                    </tr>
                                    <tr class="active">
                                        <td colspan="6" class="text-left text-bold text-uppercase">Razão de <?= $data_ini ?> até <?= $data_fim ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right text-bold text-uppercase">Saldo Inicial</td>
                                        <td colspan="3" class="text-right text-bold text-uppercase"><?= ($saldo_anterior < 0) ? "(" . number_format($saldo_anterior * -1, 2, ',', '.') . ")" : number_format($saldo_anterior, 2, ',', '.') ?></td>
                                    </tr> 
                                    <tr>
                                        <td class="text-left text-bold">DATA</td>
                                        <td class="text-left text-bold">ACESSO</td>
                                        <td class="text-left text-bold">HISTÓRICO</td>
                                        <td class="text-right text-bold">DÉBITO</td>
                                        <td class="text-right text-bold">CRÉDITO</td>
                                        <td class="text-right text-bold">SALDO</td>
                                    </tr>
                                    <?php
                                    if (mysql_num_rows($query_lanc) > 0) {
                                        $saldo1 = $saldo_anterior;
                                        while ($row_lanc = mysql_fetch_assoc($query_lanc)) {
                                            $valor = $row_lanc['valor'];
                                            $saldo_dia += $row_lanc['data_lancamento'];

                                            if (substr($value['classificador'], 0, 1) == '2' || substr($value['classificador'], 0, 4) == '4.01') {
                                                if ($row_lanc['tipo'] == 2) {
                                                    $saldo1 -= round($valor, 2);
                                                } elseif ($row_lanc['tipo'] == 1) {
                                                    $saldo1 += round($valor, 2);
                                                }
                                            } elseif (substr($value['classificador'], 0, 1) == '1' || substr($value['classificador'], 0, 4) == '4.02') {
                                                if ($row_lanc['tipo'] == 1) {
                                                    $saldo1 -= round($valor, 2);
                                                } elseif ($row_lanc['tipo'] == 2) {
                                                    $saldo1 += round($valor, 2);
                                                }
                                            }
                                            ?>
                                            <tr class="text-<?= ($row_lanc['tipo'] == 1) ? 'danger' : 'info' ?>" id="tr-<?= substr($row_lanc['data_lancamento'], 0, 2) . '/' . substr($row_lanc['data_lancamento'], 3, 2) ?>">
                                                <td class="text-left"><?= substr($row_lanc['data_lancamento'], 0, 2) . '/' . substr($row_lanc['data_lancamento'], 3, 2) ?></td>
                                                <td><i class="text-bold" onmouseover="Tip('<?= $row_lanc['classificador'].' - '.$row_lanc['nomeclatura'] ?>')" onmouseout="UnTip()" style="cursor:pointer"><?= $row_lanc['acesso'] ?></i></td>
                                                <td class="text-left"><?= $row_lanc['descricao'] . ' ' . $row_lanc['historico'] ?></td>
                                                <td class="text-right"><?= ($row_lanc['tipo'] == 2) ? ($valor < 0) ? "(" . number_format($valor * -1, 2, ',', '.') . ")" : number_format($valor, 2, ',', '.') : '' ?></td>
                                                <td class="text-right"><?= ($row_lanc['tipo'] == 1) ? ($valor < 0) ? "(" . number_format($valor * -1, 2, ',', '.') . ")" : number_format($valor, 2, ',', '.') : '' ?></td>
                                                <td class="text-right <?= $saldo1 < 0 ? 'text-danger' : 'text-info' ?>"><?= (round($saldo1, 2) < 0) ? number_format(round($saldo1, 2) * -1, 2, ',', '.') : number_format(round($saldo1, 2), 2, ',', '.') ?></td>
                                            </tr>
                                        <?php
                                        }
                                        $valor = 0;
                                        $saldo_dia = 0;
                                        $saldo = 0;
                                        $saldo1 = 0;
                                    } else {
                                        ?>
                                        <tr class="" id="tr-<?= substr($row_lanc['data_lancamento'], 0, 2) . '/' . substr($row_lanc['data_lancamento'], 3, 2) ?>">

                                            <td colspan="6" class="text-right"><?= ($saldo_anterior < 0) ? "(" . number_format($saldo_anterior * -1, 2, ',', '.') . ")" : number_format($saldo_anterior, 2, ',', '.') ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    <tr><td colspan="6"><hr></td></tr>
                            <?php } ?>
                            </table>
                        <?php } else { ?>
                            <div class="alert alert-info">Nenhuma informação encontrada neste filtro!</div>
    <?php } ?>
                    </div>
                </div>
    <?php include_once '../../template/footer.php'; ?>
            </div>
            <script type="text/javascript" src="wz_tooltip.js"></script>
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