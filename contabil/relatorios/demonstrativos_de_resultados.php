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
$global = new GlobalClass();

$projeto = ($_REQUEST['projeto'] > 0) ? $_REQUEST['projeto'] : null;
$ano = (!empty($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$inicio = (!empty($_REQUEST['inicio'])) ? $_REQUEST['inicio'] : "01/" . date('m/Y');
$final = (!empty($_REQUEST['final'])) ? $_REQUEST['final'] : date('t', date('m-Y') . "-01") . date('/m/Y');

if (isset($_REQUEST['filtrar'])) {
    if ($_REQUEST['exercicio'] == 1) {
        $array = $objClassificador->dre($projeto, $ano, 12, null, null, true);
    } elseif ($_REQUEST['exercicio'] == 2) {
        $array = $objClassificador->dre($projeto, null, null, ConverteData($inicio, 'Y-m-d'), ConverteData($final, 'Y-m-d'), true);
    }
}
 
$nome_pagina = "Demonstrativo do Resultado do Exercício";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "form_dre");
$breadcrumb_pages = array("Relatórios Contábeis"=>"index.php");

if ($_REQUEST['filtrar'] == 'Imprimir')  {

    class PDF extends FPDF {

        public $master;

        function Header() {
            if ($_REQUEST['exercicio'] == 1) { $exercicio = 'Exercício do Ano '.$_REQUEST['ano']; } 
            if ($_REQUEST['exercicio'] == 2) { $exercicio = 'Período de '.$_REQUEST['inicio'].' '.$_REQUEST['final']  ; }

            $this->SetFont('Arial', '', 8);
            $this->SetLineWidth(0);
            $this->Image("../../imagens/logomaster{$this->master['id_master']}.gif", 1, .75, 2);
            $this->Cell(3);
            $this->Cell(10, .3, $this->master['nome'], 0, 0, 'L');
            $this->Ln();
            $this->Cell(3);
            $this->Cell(10, .3, 'CNPJ ' . $this->master['cnpj'], 0, 0, 'L');
            $this->Ln();
            $this->Cell(3);
            $this->Cell(10, .3, $this->master['endereco'], 0, 'B', 'L');
            $this->Cell(3,.8);
            $this->Ln();
            $this->SetFont('Arial', '', 10);
            $this->SetLineWidth(0);
            $this->Cell(19, .3, 'DEMONSTRATIVO DO RESULTADO DO EXERCÍCIO', 0, 0, 'C');
            $this->Ln();
            $this->Cell(19, .3, $exercicio,0, 0, 'C');
            $this->Ln(.5);
            $this->Cell(19, 0, NULL, 0, 0, 'B' , 'C');
            $this->Ln(.8);
        }

        function Footer() {
            $this->SetY(-1);
            $this->SetFont('Arial', '', 6.5);
            $this->SetLineWidth(0);
            $this->Cell(8, .8, 'F71 SISTEMAS WEB', 'T', 0,'L');
            $this->Cell(8, .8, 'Módulo Contabilidade - versão 1.0', 'T', 0,'L');
            $this->Cell(3, .8, 'Pág ' . $this->PageNo(), 'T',0, 'R');
        }
    }
    
    $pdf = new PDF('P', 'cm', 'A4');
    $pdf->master = $master;
    
    $pdf->setMargins(1, 1, 1);
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(1, 1.5);
    
    foreach($array as $key => $arr_tipos) { 
        $total = 0;
        foreach ($arr_tipos as $conta_pai3 => $pai3) {   
            $subtotal = 0;
            foreach ($pai3['array'] as $conta_pai2 => $pai2) {
                $pdf->SetFont('Arial', '', 8);
                $pdf->SetLineWidth(0);
                $pdf->Cell(10, .4, $pai2['descricao'],0,0,'L');
                $pdf->SetLineWidth(0);
                $pdf->Ln();
                foreach ($pai2['array'] as $conta_pai1 => $pai1) {
                    $subtotal1 = 0;
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->Cell(1, .4, null, 0);
                    $pdf->Cell(18, .4, $pai1['descricao'],0,0,'L');
                    $pdf->SetLineWidth(0);
                    $pdf->Ln();                    
                    foreach ($pai1['array'] as $key => $contas) {
                        $conta_pai_atual = '';
                        if ($conta_pai == $conta_pai_atual) {
                            $subtotal += $contas['total'];
                            $subtotal1 += $contas['total'];
                            $total += $contas['total'];
                        } else {
                            $subtotal = $contas['total'];
                            $subtotal1 += $contas['total'];
                            $total += $contas['total'];
                            $conta_pai_atual = $conta_pai;
                        }
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->cell(2, .4);
                        $pdf->Cell(10, .4, $contas['descricao'],0,0,'L');
                        $pdf->Cell(7, .4, $contas['total'] < 0 ? '(' . number_format($contas['total'] * -1, 2, ',', '.') . ')' : number_format($contas['total'], 2, ',', '.'),0,0,'R');
                        $pdf->Ln();
                    }
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->Cell(12, .4, 'SUB TOTAL R$',0,0,'R' );
                    $pdf->Cell(7, .4, $subtotal1 < 0 ? '(' . number_format($subtotal1 * -1, 2, ',', '.') . ')' : number_format($subtotal1, 2, ',', '.'),0,0,'R');
                    $pdf->SetLineWidth(0);
                    $pdf->Ln();                    
                }
            }
            $pdf->Cell(10, .6, $pai3['descricao'],0,0,'R');                
            $pdf->Cell(2, .6, 'TOTAL R$','',0,'R');                
            $pdf->Cell(7, .6, ($subtotal < 0) ? '(' . number_format($subtotal * -1, 2, ',', '.') . ')' : number_format($subtotal, 2, ',', '.'), 'T', 0, 'R');
            $pdf->Ln();
            $pdf->SetLineWidth(0);
            if($contas['tipo'] == 1) {
                $somaR +=$subtotal;
            } elseif ($contas['tipo'] == 2) {
                $somaD +=$subtotal;
            }
            $saldo = $somaD + $somaR;
        }
        $pdf->Cell(19, .6, ($total < 0) ? '(' . number_format($total * -1, 2, ',', '.') . ')' : number_format($total, 2, ',', '.'), 'B', 0, 'R');
        $pdf->Ln(1.5);
    }
    $pdf->SetLineWidth(0);
    $pdf->Cell(10,.6,'SALDO DO EXERCÍCIO','LTB',0,'C' );
    $pdf->Cell(9, .6, $saldo < 0 ? '(' . number_format($saldo * -1, 2, ',', '.') . ')' : number_format($saldo, 2, ',', '.'), 'TBR', 0, 'R');

    $pdf->Output();
} else { ?>

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
                        <form action="" method="" name="form_dre" id="frm_dre" class="form-horizontal top-margin hidden-print" enctype="multipart/form-data">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="projeto1" class="col-sm-2 text-sm control-label">Projeto</label>
                                        <div class="col-sm-6">
                                            <?= montaSelect(getProjetos($usuario['id_regiao']), $projeto, "id='projeto' name='projeto' class='form-control input-sm validate[required,custom[select]]'") ?>
                                        </div>
                                    </div>
                                                                    <div class="form-group">
                                    <label for="projeto" class="col-sm-2 control-label">Projeto</label>
                                    <div class="col-sm-5">
                                        <?php echo montaSelect($global->carregaProjetosByRegiao($usuario['id_regiao'], array("-1" => " ", "9999" => "CONSOLIDADO"), 1), $projetos, "id='projetos' name='projetos' class='validate[required,custom[select]] form-control'"); ?>
                                    </div>
                                </div>

                                    <div class="form-group">
                                        <label for="projeto1" class="col-sm-2 text-sm control-label">Exercício</label>
                                        <div class="col-sm-3">
                                            <div class="input-group">
                                                <div class="input-group-addon"><input type="radio" id="ano_exercicio" name="exercicio" <?= (isset($_REQUEST['exercicio']) && $_REQUEST['exercicio'] == 1) ? 'checked' : '' ?>  value="1"></div>
                                                <label class="form-control pointer input-sm" for="ano_exercicio">Ano</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="input-group">
                                                <div class="input-group-addon"><input type="radio" id="periodo_exercicio" name="exercicio" <?= (isset($_REQUEST['exercicio']) && $_REQUEST['exercicio'] == 2) ? 'checked' : '' ?>  value="2"></div>
                                                <label class="form-control pointer input-sm" for="periodo_exercicio">Período</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 text-sm text-right">
                                            <div id="exercicio_periodo" class="input-group text-sm" <?= ($_REQUEST['exercicio'] == 1 || !isset($_REQUEST['exercicio'] )) ? 'style="display:none"' : '' ?>>
                                                <input type="text" id='inicio' name='inicio' class='text-sm text-center data validate[required,custom[select]] form-control' value="<?= $inicio ?>">
                                                <div class="input-group-addon">até</div>
                                                <input type="text" id='final' name='final' class='text-sm text-center data validate[required,custom[select]] form-control' value="<?= $final ?>">
                                            </div>
                                            <div id="exercicio_ano" class="input-group"  <?= ($_REQUEST['exercicio'] == 2  || !isset($_REQUEST['exercicio'])) ? 'style="display:none"' : '' ?>>
                                                <span class="input-group-addon text-sm"><label class="glyphicon  glyphicon-calendar"></label></span>
                                                <?php echo montaSelect(anosArray(), $ano, "id='ano' name='ano' class='input-sm validate[required,custom[select]] form-control'"); ?>
                                            </div>
                                        </div>
                                     </div>  
                                    <div class="panel-footer text-right">
                                        <?php if (count($array) > 0) { ?><button type="submit" name="filtrar" id="imprimirPDF" value="Imprimir" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Imprimir</button>
                                        <button type="button" onclick="tableToExcel('tbRelatorio', 'Balanco')" name="Excel" value="Excel" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Exportar Excel</button><?php } ?>
                                        <button type="submit" id="criar" name="filtrar" value="Filtrar" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filtrar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <?php 
                        if (isset($_REQUEST['filtrar'])) { ?>
                            <?php 
                            foreach ($array as $key => $arr_tipos) {
                                $total = 0; ?>
                                <div class="panel panel-default">
                                    <?php
                                    foreach ($arr_tipos as $conta_pai3 => $pai3) {
                                        $subtotal = 0; ?>
                                        <table class="table table-condensed text-sm text-uppercase valign-middle"> 
                                            <?php foreach ($pai3['array'] as $conta_pai2 => $pai2) { ?>
                                                <thead>
                                                    <tr>
                                                        <th class="text-info"><?= $pai2['descricao'] ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($pai2['array'] as $conta_pai1 => $pai1) {
                                                        $subtotal1 = 0; ?>
                                                        <tr  class="text-danger">
                                                            <td><?= $pai1['descricao'] ?></td>
                                                        </tr>
                                                        <?php foreach ($pai1['array'] as $key => $contas) { ?>
                                                            <?php
                                                            $conta_pai_atual = '';
                                                            if ($conta_pai == $conta_pai_atual) {
                                                                $subtotal += $contas['total'];
                                                                $subtotal1 += $contas['total'];
                                                                $total += $contas['total'];
                                                            } else {
                                                                $subtotal = $contas['total'];
                                                                $subtotal1 += $contas['total'];
                                                                $total += $contas['total'];
                                                                $conta_pai_atual = $conta_pai;
                                                            } ?>
                                                            <tr class="text-primary">
                                                                <td colspan="2"> 
                                                                    <?= $contas['descricao'] ?>
                                                                </td>
                                                                <td class="text-right">
                                                                    <?= $contas['total'] < 0 ? '(' . number_format($contas['total'] * -1, 2, ',', '.') . ')' : number_format($contas['total'], 2, ',', '.') ?>                                     
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                        <tr class="text-semibold">
                                                            <td colspan="2" class="text-right">
                                                                SUB TOTAL R$
                                                            </td>
                                                            <td class="text-right">
                                                                <?= ($subtotal1 < 0) ? '(' . number_format($subtotal1 * -1, 2, ',', '.') . ')' : number_format($subtotal1, 2, ',', '.') ?>
                                                            </td> 
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            <?php } ?>
                                            <tfoot class="valign-middle">
                                                <tr class="text-semibold text-success">
                                                    <td>
                                                        <?= $pai3['descricao'] ?>
                                                    </td>
                                                    <td class="text-right">Total R$</td>
                                                    <td  class="text-right">
                                                        <?= ($subtotal < 0) ? '(' . number_format($subtotal * -1, 2, ',', '.') . ')' : number_format($subtotal, 2, ',', '.') ?>
                                                    </td>
                                                </tr>
                                            </tfoot> 
                                        </table>
                                        <?php
                                        if ($contas['tipo'] == 1) {
                                            $somaR +=$subtotal;
                                        } elseif ($contas['tipo'] == 2) {
                                            $somaD +=$subtotal;
                                        }
                                        $saldo = $somaD + $somaR; ?>
                                    <?php } ?>
                                    <div class="panel-footer text-right text-bold"> 
                                        R$ <?= $total < 0 ? '(' . number_format($total * -1, 2, ',', '.') . ')' : number_format($total, 2, ',', '.') ?>                                     
                                    </div>
                                </div>
                            <?php } ?>
                            <table class="table table-condensed valign-middle">
                                <thead>
                                    <?php
                                    if ($saldo < 0) { $cor = "danger"; } else { $cor = "info"; } ?>
                                    <tr class="text-<?= $cor ?>">
                                        <th class="text-right text-bold">
                                            <label>SALDO DO EXERCÍCIO</label>
                                            R$ <?= $saldo < 0 ? '(' . number_format($saldo * -1, 2, ',', '.') . ')' : number_format($saldo, 2, ',', '.') ?>                                     
                                        </th>
                                </thead>
                            </table>
                        <?php } else { ?>
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
            <script src="js/demonstrativos_de_resultados.js" type="text/javascript"></script>
        </body>
    </html>
<?php } ?>