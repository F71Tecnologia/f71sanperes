<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/BancoClass.php");
include("../../classes/global.php");
include("../../classes/c_classificacaoClass.php");
include("../../classes/ContabilLancamentoClass.php");
include("../../classes/ContabilLoteClass.php");
require_once("../../classes/pdf/fpdf.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$id_master = $usuario['id_master'];

$query_master = "SELECT * FROM master WHERE id_master = $id_master";
$master = mysql_fetch_assoc(mysql_query($query_master));


$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;

$objClassificador = new c_classificacaoClass();

$mes = (!empty($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$ano = (!empty($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');

if (isset($_REQUEST['ano']) && isset($_REQUEST['mes']) && isset($_REQUEST['filtrar'])) {
    $array = $objClassificador->balancoSamperes($ano, $mes);
    $arrayProjeto = $objClassificador->carregarProjeto($_REQUEST['projeto']);
    
}

$nome_pagina = "Balanço";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "frmplanodeconta");
$breadcrumb_pages = array("Relatórios Contábeis" => "index.php");

if ($_REQUEST['filtrar'] == 'Imprimir') {

    class PDF extends FPDF {

        public $master, $projeto;

        function Header() {


            $this->SetFont('Arial', 'B', 6);
            $this->SetLineWidth(0);
            $this->Image("../../imagens/logomaster{$this->master['id_master']}.gif", 1, .75, 2);
            $this->Cell(3);
            $this->Cell(10, .3, $this->master['nome'], 0, 0, 'L');
            $this->SetFont('Arial', 'B', 10);
            $this->SetLineWidth(0);
            $this->Cell(6, .3, 'BALANÇO EXERCÍCIO ' . $_REQUEST['ano'], 0, 0, 'R');
            $this->SetFont('Arial', 'B', 6);
            $this->SetLineWidth(0);
            $this->Ln();
            $this->Cell(3);
            $this->Cell(10, .3, 'CNPJ ' . $this->projeto['cnpj'], 0, 0, 'L');
            $this->Ln();
            $this->Cell(3);
            $this->Cell(16, .3, $this->projeto['endereco'], 0, 'B', 'L');
            $this->Ln();
            $this->Ln();
            $this->Cell(19, 0, NULL, 0, 0, 'B' , 'C');
            $this->Ln();
            $this->Cell(3,.5);
            $this->Ln();
        }

        function Footer() {
            // Position at 1.5 cm from bottom
            $this->SetY(-1);
            // Arial italic 8
            $this->SetFont('Arial', NULL, 6);
            $this->SetLineWidth(0);
            // Page number
            $this->Cell(16, .8, 'F71 SISTEMAS WEB - módulo contábil', 'T', 0,'L');
            $this->Cell(3, .8, 'Pagina ' . $this->PageNo(), 'T',0, 'R');
        }

    }

    $pdf = new PDF('P', 'cm', 'A4');
    $pdf->master = $master;
    $pdf->projeto = $arrayProjeto;    
 
    $pdf->setMargins(1, 1, 1);
    $pdf->AddPage();

    $pdf->SetAutoPageBreak(1, 1);

    foreach ($array as $key2 => $array2) {
        if (!empty($array2['descricao'])) {
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetLineWidth(.01);
            $pdf->Cell(12, .8, $array2['descricao'], 'B', 0);
            $pdf->Cell(7, .8, null, 'B', 0, 'R');
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 6);
            $pdf->SetLineWidth(0);
        }
        foreach ($array2['array'] as $key3 => $array3) { //print_array($array3);
            if (!empty($array3['descricao'])) {
                $pdf->SetFont('Arial', 'B', 6);
                $pdf->Cell(.5, .4, null, NULL, 0);
                $pdf->Cell(11.5, .4, $array3['descricao'], NULL, 0);
                $pdf->Cell(7, .4, null, 0, 'R');
                $pdf->Ln();
                $pdf->SetFont('Arial', '', 6);
            }
            foreach ($array3['array'] as $key4 => $array4) { //print_array($array4);
                if (!empty($array4['descricao'])) {
                    $pdf->SetFont('Arial', 'B', 6);
                    $pdf->Cell(1, .4, null, NULL, 0);
                    $pdf->Cell(11, .4, $array4['descricao'], NULL, 0);
                    $pdf->Cell(7, .4, null, NULL, 0, 'R');
                    $pdf->Ln();
                    $pdf->SetFont('Arial', '', 6);
                }
                foreach ($array4['array'] as $key5 => $array5) { //print_array($array5);
                    if (!empty($array5['descricao'])) {
                        $pdf->Cell(1.5, .4, null, NULL, 0);
                        $pdf->Cell(10.5, .4, $array5['descricao'], NULL, 0);
                        $pdf->Cell(7, .4, ($array5['saldoAtual'] < 0) ? '(' . number_format($array5['saldoAtual'] * -1, 2, ',', '.') . ')' : number_format($array5['saldoAtual'], 2, ',', '.'), NULL, 0, 'R');
                        $pdf->Ln();
                    }
                    foreach ($array5['array'] as $key6 => $array6) { //print_array($array6);
                        if (!empty($array6['descricao'])) {
                            $pdf->Cell(1.5, .4, null, NULL, 0);
                            $pdf->Cell(10.5, .4, $array6['descricao'], NULL, 0);
                            $pdf->Cell(7, .4, ($array6['saldoAtual'] < 0) ? '(' . number_format($array6['saldoAtual'] * -1, 2, ',', '.') . ')' : number_format($array6['saldoAtual'], 2, ',', '.'), NULL, 0, 'R');
                            $pdf->Ln();
                        }
                    }
                }
                $pdf->SetLineWidth(.05);
                $pdf->Cell(12, .4, null, 0, 0);
                $pdf->Cell(7, .4, ($array4['saldoAtual'] < 0) ? '(' . number_format($array4['saldoAtual'] * -1, 2, ',', '.') . ')' : number_format($array4['saldoAtual'], 2, ',', '.'), NULL, 0, 'R');
                $pdf->Ln();
                $pdf->SetLineWidth(0);
            }
//            $pdf->SetLineWidth(.05);
//            $pdf->Cell(12,.6,null,0,0);
//            $pdf->Cell(7,.6,($array3['saldoAtual'] < 0) ? '(' . number_format($array3['saldoAtual']*-1,2,',','.') . ')' : number_format($array3['saldoAtual'],2,',','.'),NULL,0,'R');
//            $pdf->Ln();
//            $pdf->SetLineWidth(0);
        }
        $pdf->SetLineWidth(.02);
        $pdf->Cell(8, .6, null, 0, 0, 'R');
        $pdf->Cell(4, .6, "TOTAL DO {$array2['descricao']}:", "TBL", 0, 'R');
        $pdf->Cell(7, .6, ($array2['saldoAtual'] < 0) ? '(' . number_format($array2['saldoAtual'] * -1, 2, ',', '.') . ')' : number_format($array2['saldoAtual'], 2, ',', '.'), "TBR", 0, 'R');
        $pdf->Ln();
        $pdf->SetLineWidth(0);
        $pdf->Cell(19, .6, null, 0, 0);
        $pdf->Ln();
    }

    $pdf->Output($_REQUEST['projeto'].'_'. $_REQUEST['ano'].'B.pdf','D');

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
                        <form action="" method="" name="form_lote" id="form_lote" class="form-horizontal top-margin hidden-print" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="" class="col-sm-2 text-sm control-label">Competência</label>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <?php echo montaSelect(mesesArray(), $mes, "id='mes' name='mes' class='input-sm validate[required,custom[select]] form-control'"); ?>
                                            </div>
                                            <div class="col-sm-2">
                                                <?php echo montaSelect(anosArray(), $ano, "id='ano' name='ano' class='input-sm validate[required,custom[select]] form-control'"); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer text-right">
                                    <?php if (count($array) > 0) { ?><button type="submit" name="filtrar" id="imprimirPDF" value="Imprimir" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Imprimir</button><?php } ?>
                                    <?php if (count($array) > 0) { ?><button type="button" onclick="tableToExcel('tbRelatorio', 'Balanco')" name="Excel" value="Excel" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Exportar Excel</button><?php } ?>
                                    <button type="submit" id="criar" name="filtrar" value="Filtrar" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filtrar</button>
                                </div>
                            </div>
                        </form>
                        <?php if (isset($_REQUEST['ano']) && isset($_REQUEST['mes']) && isset($_REQUEST['filtrar'])) { ?>
                            <table id="tbRelatorio" class="table table-condensed table-hover text-sm valign-middle">
                                <thead>
                                    <tr>
                                        <td class="text-center text-bold"></td>
                                        <td colspan="2" class="text-right text-bold">Saldos Acumulados</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($array as $key2 => $array2) { ?>
                                        <?php if (!empty($array2['descricao'])) { ?>
                                            <tr class="text-lg" style="border-top: 2px solid #000; border-bottom: 2px solid #000;">
                                                <td class="text-uppercase"><?= $array2['descricao'] ?></td>
                                                <td class=""></td>
                                                <td class="text-right"><!--<?= $array2['saldoAtual']; ?>--></td>
                                            </tr>
                                        <?php } ?>
                                        <?php foreach ($array2['array'] as $key3 => $array3) { //print_array($array3);  ?>
                                            <?php if (!empty($array3['descricao'])) { ?>
                                                <tr>
                                                    <td class="text-uppercase text-left text-bold" style="padding-left: 20px;"><?= ($_COOKIE['logado'] == 257) ? $array3['classificador'] . ' - ' : '' ?><?= $array3['descricao'] ?></td>
                                                    <td class=""></td>
                                                    <td class="text-right"><!--<?= ($array3['saldoAtual'] < 0) ? '(' . number_format($array3['saldoAtual'] * -1, 2, ',', '.') . ')' : number_format($array3['saldoAtual'], 2, ',', '.') ?>--></td>
                                                </tr>
                                            <?php } ?>
                                            <?php foreach ($array3['array'] as $key4 => $array4) { //print_array($key4);  ?>
                                                <?php if (!empty($array4['descricao'])) { ?>
                                                    <tr>
                                                        <td class="text-uppercase text-left text-bold" style="padding-left: 40px;"><?= ($_COOKIE['logado'] == 257) ? $array4['classificador'] . ' - ' : '' ?><?= $array4['descricao'] ?></td>
                                                        <td class=""></td>
                                                        <td class="text-right"><!--<?= ($array4['saldoAtual'] < 0) ? '(' . number_format($array4['saldoAtual'] * -1, 2, ',', '.') . ')' : number_format($array4['saldoAtual'], 2, ',', '.') ?>--></td>
                                                    </tr>
                                                <?php } ?>
                                                <?php foreach ($array4['array'] as $key5 => $array5) { //print_array($array5);  ?>
                                                    <?php if (!empty($array5['descricao'])) { ?>
                                                        <tr>
                                                            <td class="text-uppercase text-left" style="padding-left: 60px;"><?= ($_COOKIE['logado'] == 257) ? $array5['classificador'] . ' - ' : '' ?><?= $array5['descricao'] ?></td>
                                                            <td class=""></td>
                                                            <td class="text-right"><?= ($array5['saldoAtual'] < 0) ? '(' . number_format($array5['saldoAtual'] * -1, 2, ',', '.') . ')' : number_format($array5['saldoAtual'], 2, ',', '.') ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                    <?php foreach ($array5['array'] as $key6 => $array6) { //print_array($array6);  ?>
                                                        <?php if (!empty($array6['descricao'])) { ?>
                                                            <tr>
                                                                <td class="text-uppercase text-left" style="padding-left: 60px;"><?= ($_COOKIE['logado'] == 257) ? $array6['classificador'] . ' - ' : '' ?><?= $array6['descricao'] ?></td>
                                                                <td class=""></td>
                                                                <td class="text-right"><?= ($array6['saldoAtual'] < 0) ? '(' . number_format($array6['saldoAtual'] * -1, 2, ',', '.') . ')' : number_format($array6['saldoAtual'], 2, ',', '.') ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                                <tr>
                                                    <td class="text-uppercase"></td>
                                                    <td class="text-uppercase text-bold" style="border-top: 2px solid #000;"></td>
                                                    <td class="text-right text-bold" style="border-top: 2px solid #000;"><?= ($array4['saldoAtual'] < 0) ? '(' . number_format($array4['saldoAtual'] * -1, 2, ',', '.') . ')' : number_format($array4['saldoAtual'], 2, ',', '.') ?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                        <tr>
                                            <td class="text-uppercase text-right text-bold"></td>
                                            <td class="text-bold" style="border-top: 2px solid #000; border-bottom: 2px solid #000; border-left: 2px solid #000;">TOTAL DO <?= $array2['descricao'] ?>:</td>
                                            <td class="text-right text-bold" style="border-top: 2px solid #000; border-bottom: 2px solid #000; border-right: 2px solid #000;"><?= ($array2['saldoAtual'] < 0) ? '(' . number_format($array2['saldoAtual'] * -1, 2, ',', '.') . ')' : number_format($array2['saldoAtual'], 2, ',', '.') ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">&nbsp;</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else {
                            ?>
                            <div class="alert alert-info">Nenhuma informação encontrada neste filtro!</div>
                        <?php } ?>
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
            <script src="js/classificacao.js" type="text/javascript"></script>
            <script>
                                        //            $(function(){
                                        //                $('body').on('click', '#imprimirPDF', function(){
                                        //                    var t = $('#tbRelatorio2').html().replace(/^\s+|\s+$|\n|\r/g,"");
                                        ////                    console.log(t); return false;
                                        //                    window.location.href = '../html2pdf/imprimirPDF.php?t=' + t;
                                        //                });
                                        //            });
            </script>
        </body>
    </html>
<?php } ?>