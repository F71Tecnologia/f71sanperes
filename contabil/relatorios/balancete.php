<?php
error_reporting(E_ALL);

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/BancoClass.php");
include("../../classes/global.php");
include("../../classes/c_planodecontasClass.php");
include("../../classes/c_classificacaoClass.php");
include("../../classes/ContabilLancamentoClass.php");
include("../../classes_permissoes/acoes.class.php");
require_once("../../classes/pdf/fpdf.php");

$usuario = carregaUsuario();

$id_regiao = $usuario['id_regiao'];
$id_master = $usuario['id_master'];

$global = new GlobalClass();

$qr_func = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_func = mysql_fetch_assoc($qr_func);


$query_master = "SELECT * FROM master WHERE id_master = $id_master";
$master = mysql_fetch_assoc(mysql_query($query_master));

$query = "SELECT * FROM projeto A WHERE A.id_projeto = '{$_REQUEST['projeto']}'";
$master1 = mysql_fetch_assoc(mysql_query($query));

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;

$objAcao = new Acoes();
$objLancamento = new ContabilLancamentoClass();
$objClassificador = new c_classificacaoClass();


$projeto = ($_REQUEST['projeto'] >= 0) ? $_REQUEST['projeto'] : null;
$data_ini = $_REQUEST['inicio']; //(!empty($_REQUEST['inicio'])) ? $_REQUEST['inicio'] : "01/".date('m/Y');
$data_fim = $_REQUEST['final']; //(!empty($_REQUEST['final'])) ? $_REQUEST['final'] : date('t', date('m-Y')."-01").date('/m/Y');

if (isset($_REQUEST['filtrar']) || isset($_REQUEST['filtra'])) {

 $arrayClassificacao = $objClassificador->balancete($projeto, $data_ini, $data_fim, true);
 $arrayProjeto = $objClassificador->carregarProjeto($projeto);
}

$nome_pagina = "Balancete";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina , "id_form" => "frmplanodeconta");
$breadcrumb_pages = array("Relatórios Contábeis" => "index.php");

if ($_REQUEST['filtrar'] == 'Imprimir')  {

    class PDF extends FPDF {

        public $master, $master1, $projeto;

        function Header() {
            if ($_REQUEST['sintetico'] == on) { $sintetico[] = "SINTÉTICO"; } 
            if ($_REQUEST['analitico'] == on) { $sintetico[] = "ANALÍTICO"; }
            $sintetico = implode(" / ", $sintetico);
            $this->Image("../../imagens/logomaster{$this->master['id_master']}.gif", 1.2, 0.8, 2.3);
            $this->SetFont('Arial', 'B', 7);
            $this->SetLineWidth(0);
            $this->Cell(2.5);
            $this->Cell(10, .3, $this->master['nome'], 0, 0, 'L');
            $this->SetFont('Arial', 'B', 7);
            $this->cell(6.5, .3, $sintetico, 0, 0, 'R' );
            $this->SetLineWidth(0);
            $this->Ln();
            $this->Cell(2.5);
            $this->Cell(10, .3, 'CNPJ ' . $this->projeto['cnpj'], 0, 0, 'L');
            $this->SetFont('Arial', 'B', 6);
            $this->SetLineWidth(0);
            $this->Cell(6.5, .3, $_REQUEST['inicio']." a ".$_REQUEST['final'],0, 0, 'R');
            $this->SetFont('Arial', 'B', 5.5);
            $this->SetLineWidth(0);
            $this->Ln();
            $this->Cell(2.5);
            $this->Cell(10, .3, $this->projeto['endereco'], 0, 'B', 'L');
            $this->Ln();
            $this->Ln();
            $this->SetFont('Arial', 'B', 10.5);
            $this->SetLineWidth(0);
            $this->Cell(19, .4, 'BALANCETE', 0, 0, 'C');
            $this->SetFont('Arial', 'B', 6.5);
            $this->SetLineWidth(0);
            $this->Ln();
            $this->Cell(19, .4,"(". $_REQUEST['projeto']." - ".  $this->projeto['nome'].")", 0, 0, 'C');
            $this->Ln();
            $this->Ln();
        }

        function Footer() {
            // Position at 1.5 cm from bottom
            $this->SetY(-1);
            // Arial italic 8
            $this->SetFont('Arial', NULL, 6.5);
            $this->SetLineWidth(0);
            // Page number
            $this->Cell(16, .8, 'F71 SISTEMAS WEB - módulo contábil', 'T', 0, 'L');
            $this->Cell(3, .8, 'Pagina '.$this->PageNo(), 'T', 0, 'R');
        }
    }
    
    $pdf = new PDF('P', 'cm', 'A4');
    $pdf->master = $master;
    $pdf->projeto = $arrayProjeto;    
    $pdf->setMargins(1, 1, 1);

    $pdf->SetAutoPageBreak(1, 1.5);
    
    foreach ($arrayClassificacao as $key => $value) { //   print_array($value);
    
        if($value['classificador'] == 1 || $value['classificador'] == 2 || $value['classificador'] == 3 || $value['classificador'] == 4) {
            $pdf->AddPage();    
            $pdf->Cell(19.2, 0, NULL, 0, 0, 'B' , 'C');
            $pdf->Ln();
            $pdf->SetFont('Arial', 'B', 6);
            $pdf->SetLineWidth(0);
            $pdf->Ln();
            $pdf->Cell(1.2, .3, 'ACESSO', 0, 0, 'L');
            $pdf->Cell(2, .3, 'CLASSIFICADOR', 0, 0, 'L');
            $pdf->Cell(7.8, .3, 'DESCRIÇÃO', 0, 0, 'L');
            $pdf->Cell(2.8, .3, 'SALDO INICIAL R$', 0, 0, 'L');
            $pdf->Cell(1.8, .3, 'DÉBITO R$', 0, 0, 'L');
            $pdf->Cell(1.6, .3, 'CRÉDITO R$', 0, 0, 'L');
            $pdf->Cell(1, .3, 'SALDO FINAL R$', 0, 0, 'L');
            $pdf->Ln();          
            $pdf->Cell(19.2, .2, NULL, 'T', 0, 'C');
            $pdf->Ln();              
        }
        
        $tipo_balancete = (!isset($_REQUEST['analitico']) && !isset($_REQUEST['sintetico'])) ? true : false;
        $n = explode('.', $value['classificador']);
        if ($_REQUEST['analitico'] == on && $value['analitica_sintetica'] == "A") {
            $tipo_balancete = true;
        }
        if ($_REQUEST['sintetico'] == on && $value['analitica_sintetica'] == "S") {
            $tipo_balancete = true;
        }
        if (!isset($value['classificador'])) {
            $tipo_balancete = false;
        }
        if (($value['saldoAnterior'] != 0.00 || $value['saldoAtual'] != 0.00 || $value['credora'] != 0.00 || $value['devedora'] != 0.00) && $tipo_balancete) {
            
            if($value['acesso'] == 0 || $value['analitica_sintetica'] == "S") {
                $acesso = '';                 
            } else { 
                $acesso = $value['acesso'];
            }
            
            if($value['analitica_sintetica'] == "S"){
                $pdf->Ln(); 
                $pdf->setFont('Arial', 'B', 6);
                $pdf->SetLineWidth(0);
                $pdf->Ln(); 
                $pdf->cell(1.2, .35, $acesso, 0, 0 );
                $pdf->cell(2, .35, $value['classificador'], 0, 0 );
                $pdf->cell(8, .35, substr($value['descricao'], 0,58 ),0, 0);
                $pdf->Cell(2, .35, ($value['saldoAnterior'] < 0) ? '(' . number_format($value['saldoAnterior'] * -1, 2, ',', '.') . ')' : number_format($value['saldoAnterior'], 2, ',', '.'), NULL, 0, 'R');
                $pdf->cell(2, .35, number_format($value['devedora'], 2, ',', '.'), NULL, 0, 'R');
                $pdf->cell(2, .35, number_format($value['credora'], 2, ',', '.'), NULL, 0, 'R');
                $pdf->Cell(2, .35, ($value['saldoAtual'] < 0) ? '(' . number_format($value['saldoAtual'] * -1, 2, ',', '.') . ')' : number_format($value['saldoAtual'], 2, ',', '.'), NULL, 0, 'R');
               
            } else {
                $pdf->setFont('Arial', '', 6);
                $pdf->SetLineWidth(0);
                $pdf->Ln(); 
                $pdf->cell(1.2, .35, $acesso, 0, 0 );
                $pdf->cell(2, .35, $value['classificador'], 0, 0 );
                $pdf->cell(8, .35, substr($value['descricao'], 0,58 ),0, 0);
                $pdf->Cell(2, .35, ($value['saldoAnterior'] < 0) ? '(' . number_format($value['saldoAnterior'] * -1, 2, ',', '.') . ')' : number_format($value['saldoAnterior'], 2, ',', '.'), NULL, 0, 'R');
                $pdf->cell(2, .35, number_format($value['devedora'], 2, ',', '.'), NULL, 0, 'R');
                $pdf->cell(2, .35, number_format($value['credora'], 2, ',', '.'), NULL, 0, 'R');
                $pdf->Cell(2, .35, ($value['saldoAtual'] < 0) ? '(' . number_format($value['saldoAtual'] * -1, 2, ',', '.') . ')' : number_format($value['saldoAtual'], 2, ',', '.'), NULL, 0, 'R');
            }       
        }
    }
    
    $pdf->AddPage(); 
    $pdf->Ln(); 
    $pdf->setFont('Arial', 'B', 9);
    $pdf->SetLineWidth(0);
    $pdf->Cell(19.2, 0, NULL, 0, 0, 'B' , 'C');
    $pdf->Ln();
    $pdf->cell(19.2, 3, '', 0, 0 , 'C');
    $pdf->Ln();
    $pdf->cell(19.2, 1, 'RESUMO', 0, 0 , 'C');
    $pdf->Ln();
    $pdf->cell(4, .2, '', 0, 0, 'L');
    $pdf->Cell(11, .2, NULL, 'T', 0, 'C');
    $pdf->cell(4, .2, '', 0, 0, 'L');
    $pdf->Ln(); 
    $pdf->cell(4, .5, '', 0, 0, 'L');
    $pdf->cell(5, .5, 'ATIVO', 0, 0, 'L');
    $pdf->cell(6, .5, number_format($arrayClassificacao['10000000000000']['saldoAtual'], 2, ',', '.'), 0, 0, 'R');
    $pdf->cell(4, .5, '', 0, 0, 'L');
    $pdf->Ln(); 
    $pdf->cell(4, .5, '', 0, 0, 'L');
    $pdf->cell(5, .5, 'PASSIVO', 0, 0, 'L');
    $pdf->cell(6, .5, number_format($arrayClassificacao['20000000000000']['saldoAtual'], 2, ',', '.'), 0, 0, 'R');
    $pdf->cell(4, .5, '', 0, 0, 'L');
    $pdf->Ln();
    $pdf->cell(4, .2, '', 0, 0, 'L');
    $pdf->Cell(11, .2, NULL, 'T', 0, 'C');
    $pdf->cell(4, .2, '', 0, 0, 'L');
    $pdf->Ln();     
    $pdf->cell(4, .5, '', 0, 0, 'L');
    $pdf->cell(5, .5, '', 0, 0, 'L');
    $pdf->cell(6, .5, number_format($arrayClassificacao['20000000000000']['saldoAtual'] - $arrayClassificacao['10000000000000']['saldoAtual'], 2, ',', '.'), 0, 0, 'R');
    $pdf->cell(4, .5, '', 0, 0, 'L');
    $pdf->cell(4, 2, '', 0, 0, 'L');
    $pdf->Ln(); 
    $pdf->cell(4, .5, '', 0, 0, 'L');
    $pdf->cell(5, .5, 'RECEITAS', 0, 0, 'L');
    $pdf->cell(6, .5, number_format($arrayClassificacao['40100000000000']['saldoAtual'], 2, ',', '.'), 0, 0, 'R');
    $pdf->cell(4, .5, '', 0, 0, 'L');
    $pdf->Ln(); 
    $pdf->cell(4, .5, '', 0, 0, 'L');
    $pdf->cell(5, .5, 'DESPESAS', 0, 0, 'L');
    $pdf->cell(6, .5, number_format($arrayClassificacao['40200000000000']['saldoAtual'], 2, ',', '.'), 0, 0, 'R');
    $pdf->cell(4, .5, '', 0, 0, 'L');
    $pdf->Ln();
    $pdf->cell(4, .2, '', 0, 0, 'L');
    $pdf->Cell(11, .2, NULL, 'T', 0, 'C');
    $pdf->cell(4, .2, '', 0, 0, 'L');
    $pdf->Ln();     
    $pdf->cell(4, .5, '', 0, 0, 'L');
    $pdf->cell(5, .5, '', 0, 0, 'L');
    $pdf->cell(6, .5, number_format($arrayClassificacao['40100000000000']['saldoAtual'] - $arrayClassificacao['40200000000000']['saldoAtual'], 2, ',', '.'), 0, 0, 'R');
    $pdf->cell(4, .5, '', 0, 0, 'L');
    
    $pdf->Output($_REQUEST['projeto'].'_'.substr($_REQUEST['inicio'], 0, 2).substr($_REQUEST['inicio'], 3, 2).substr($_REQUEST['inicio'], 6, 4).substr($_REQUEST['final'], 0, 2).substr($_REQUEST['final'], 3, 2).substr($_REQUEST['final'], 6, 4).'B.pdf','D');
    
}  else {
    
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão Contabilidade</title>
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
                        <h2><?php echo $icon['38'] ?> - Contabilidade <small>- Balancete</small></h2>
                    </div>
                    <form action="" method="post" name="form_lote" id="form_balancete" class="form-horizontal top-margin hidden-print" enctype="multipart/form-data">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="projeto" class="col-sm-2 control-label">Projeto</label>
                                    <div class="col-sm-5">
                                        <?php echo montaSelect($global->carregaProjetosByRegiao($usuario['id_regiao'], array("-1" => " ", "0" => "CONSOLIDADO"), 1), $projetoR, "id='projeto' name='projeto' class='required[custom[select]] form-control'"); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 text-sm control-label">Período</label>
                                    <div class="col-sm-5">
                                        <div class="input-group">
                                            <div class="input-group-addon text-sm control-label">de</div>
                                            <input type="text" id='inicio' name='inicio' class='text-sm text-center data validate[required,custom[select]] form-control' value="<?= $data_ini ?>">
                                            <div class="input-group-addon text-sm control-label">até</div>
                                            <input type="text" id='final' name='final' class='text-sm text-center data validate[required,custom[select]] form-control' value="<?= $data_fim ?>">
                                        </div>
                                    </div> 
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-3">
                                        <div class="input-group">
                                            <div class="input-group-addon"><input type="checkbox" id="analitico" name="analitico" <?= (isset($_REQUEST['analitico']) || (!isset($_REQUEST['sintetico']))) ? 'CHECKED' : null ?> class=''></div>
                                            <label class="form-control pointer input-sm" for="analitico">Analítico</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="input-group">
                                            <div class="input-group-addon"><input type="checkbox" id="sintetico" name="sintetico" <?= (isset($_REQUEST['sintetico']) || (!isset($_REQUEST['analitico']))) ? 'CHECKED' : null ?> class=''></div>
                                            <label class="form-control pointer input-sm" for="sintetico">Sintético</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <?php if (count($arrayClassificacao) > 0) { ?>
                                    <button type="button" onclick="tableToExcel('tbRelatorio', 'Balancete')" name="Excel" value="Excel" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Exportar Excel</button> 
                                    <button type="submit" id="" name="filtrar" value="Imprimir" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Impressão</button>
                                    <input type="hidden" id="" name="filtra" value="<?= $_REQUEST['filtra'] ?>">
                                <?php } ?>
                                <button type="submit" id="mensal" name="filtra" value="" class="btn btn-info btn-sm"><i class="fa fa-calendar-check-o"></i> Filtrar</button>
                            </div>
                        </div>
                        <?php if (isset($_REQUEST['filtrar']) || isset($_REQUEST['filtra'])) { ?>
                        <table id="tbRelatorio" class="table table-condensed table-hover text-sm valign-middle">
                            <thead>
                                <tr>
                                    <td class="text-center text-bold">Acesso</td>
                                    <td class="text-center text-bold">Classificador</td>
                                    <td class="text-center text-bold">Descrição</td>
                                    <td class="text-right text-bold">Saldo Anterior</td>
                                    <td class="text-right text-bold">Débito</td>
                                    <td class="text-right text-bold">Crédito</td>
                                    <td class="text-right text-bold">Saldo Atual</td>
                                </tr>
                            </thead> 
                            <tbody>
                                <?php
                                foreach ($arrayClassificacao as $key => $value) {
                                    
                                    $tipo_balancete = (!isset($_REQUEST['analitico']) && !isset($_REQUEST['sintetico'])) ? true : false;
                                    $n = explode('.', $value['classificador']);
                                    if ($_REQUEST['analitico'] == on && $value['analitica_sintetica'] == "A") {
                                        $tipo_balancete = true;
                                    }
                                    if ($_REQUEST['sintetico'] == on && $value['analitica_sintetica'] == "S") {
                                        $tipo_balancete = true;
                                    }
                                    if (!isset($value['classificador'])) {
                                            $tipo_balancete = false;
                                    }
                                    if (($value['saldoAtual'] != 0.00 || $value['saldoAnterior'] != 0.00 || $value['credora'] != 0.00 || $value['devedora'] != 0.00) && $tipo_balancete) {                                        
                                       
                                        if($value['analitica_sintetica'] == "S") {
                                            $acesso = '';                 
                                        } else {
                                            $acesso = $value['acesso'];                                             
                                        } ?>                                        
                                        <tr class="<?= $value['analitica_sintetica'] == "S" ? 'text-bold text-sm active text-default uppercase' : 'text-sm lowercase' ?>">
                                            <td><?= $acesso ?><!--<?= $value['id_conta'] ?>--></td>
                                            <td><?= $value['classificador'] ?><!--<?= $value['id_conta'] ?>--></td>
                                            <td><?= $value['descricao'] ?></td>
                                            <td class="text-<?= ($value['saldoAnterior'] < 0) ? 'danger' : 'info' ?> text-right"><?= number_format($value['saldoAnterior'], 2, ',', '.') ?></td>
                                            <td class="text-<?= ($value['devedora'] < 0) ? 'danger' : 'info' ?> text-right"><?= number_format($value['devedora'], 2, ',', '.') ?></td>
                                            <td class="text-<?= ($value['credora'] < 0) ? 'danger' : 'info' ?> text-right"><?= number_format($value['credora'],  2, ',', '.') ?></td>
                                            <td class="text-<?= ($value['saldoAtual'] < 0) ? 'danger' : 'info' ?> text-right"><?= number_format($value['saldoAtual'], 2, ',', '.') ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr><td colspan="7">&emsp;</tr>
                            </tfoot>
                        </table>
                        <?php 
                            $saldo_ap = $arrayClassificacao['10000000000000']['saldoAtual'] - $arrayClassificacao['20000000000000']['saldoAtual'];
                            $saldo_dre = $arrayClassificacao['40100000000000']['saldoAtual'] - $arrayClassificacao['40200000000000']['saldoAtual'];
                        ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th colspan="4">RESUMO</th> 
                                </tr>                         
                            </thead>
                            <tbody class="text-sm">
                                <tr>
                                    <td class="col-md-7">&emsp;</td>
                                    <td class="text-left text-bold <?= $arrayClassificacao['10000000000000']['saldoAtual'] < 0 ? 'text-danger' : 'text-info' ?>">ATIVO</td>
                                    <td class="text-right text-bold <?= $arrayClassificacao['10000000000000']['saldoAtual'] < 0 ? 'text-danger' : 'text-info' ?>">R$</td>
                                    <td class="text-right text-bold <?= $arrayClassificacao['10000000000000']['saldoAtual'] < 0 ? 'text-danger' : 'text-info' ?>"><?= number_format($arrayClassificacao['10000000000000']['saldoAtual'], 2, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td>&emsp;</td>
                                    <td class="text-left text-bold <?= $arrayClassificacao['20000000000000']['saldoAtual'] < 0 ? 'text-danger' : 'text-info' ?>">PASSIVO</td>
                                    <td class="text-right text-bold <?= $arrayClassificacao['20000000000000']['saldoAtual'] < 0 ? 'text-danger' : 'text-info' ?>">R$</td>
                                    <td class="text-right text-bold <?= $arrayClassificacao['20000000000000']['saldoAtual'] < 0 ? 'text-danger' : 'text-info' ?>"><?= number_format($arrayClassificacao['20000000000000']['saldoAtual'], 2, ',', '.') ?></td>
                                </tr>
                                <?php if((int) $saldo_ap != 0) { ?> 
                                    <tr class="text-<?= ($saldo_ap < 0) ? 'danger' : 'info' ?> text-right text-bold">
                                        <td>&emsp;</td>
                                        <td class="text-left">DIFERENÇA <i class="text-sm">(ATIVO - PASSIVO)</i></td>
                                        <td>R$</td>
                                        <td><?= number_format(round($saldo_ap,2), 2, ',', '.') ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td>&emsp;</td>
                                    <?php $cor4 = $arrayClassificacao['401000000000000']['saldoAtual'] < 0 ? "text-danger" : "text-info"; ?>
                                    <td class="text-left text-bold <?= $arrayClassificacao['40100000000000']['saldoAtual'] < 0 ? 'text-danger' : 'text-info' ?>">RECEITAS</td>
                                    <td class="text-right text-bold <?= $arrayClassificacao['40100000000000']['saldoAtual'] < 0 ? 'text-danger' : 'text-info' ?>">R$</td>
                                    <td class="text-right text-bold <?= $arrayClassificacao['40100000000000']['saldoAtual'] < 0 ? 'text-danger' : 'text-info' ?>"><?= number_format($arrayClassificacao['40100000000000']['saldoAtual'], 2, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td>&emsp;</td>
                                    <?php $cor5 = $arrayClassificacao['402000000000000']['saldoAtual']  < 0 ? "text-danger" : "text-info"; ?>
                                    <td class="text-left text-bold <?= $arrayClassificacao['402000000000000']['saldoAtual'] < 0 ? 'text-danger' : 'text-info' ?>">DESPESAS</td>
                                    <td class="text-right text-bold <?= $arrayClassificacao['402000000000000']['saldoAtual'] < 0 ? 'text-danger' : 'text-info' ?>">R$</td>
                                    <td class="text-right text-bold <?= $arrayClassificacao['402000000000000']['saldoAtual'] < 0 ? 'text-danger' : 'text-info' ?>"><?= number_format($arrayClassificacao['40200000000000']['saldoAtual'], 2, ',', '.') ?></td>
                                </tr>
                                <?php if((int) $saldo_dre != 0) { ?> 
                                    <tr class="text-<?= ($saldo_dre < 0) ? 'danger' : 'info' ?> text-right text-bold">
                                        <td>&emsp;</td>
                                        <td class="text-left">DIFERENÇA <i class="text-sm">(RECEITA - DESPESAS)</i></td>
                                        <td>R$</td>
                                        <td><?= number_format(round($saldo_dre,2), 2, ',', '.') ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>                  
                    <?php } else { ?>
                        <div class="alert alert-info">Nenhuma informação encontrada neste filtro!</div>
                    <?php } ?>
                    </form>
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
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="../js/balancete.js" type="text/javascript"></script>
    </body>
</html>
<?php } ?>