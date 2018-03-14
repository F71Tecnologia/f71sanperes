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
include("../../classes/ContabilLancamentoItemClass.php");
include("../../classes/ContabilLancamentoClass.php");
include("../../classes/ContabilLoteClass.php");
require_once("../../classes/pdf/fpdf.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$id_master = $usuario['id_master'];

$query_master = "SELECT * FROM master WHERE id_master = $id_master";
$master = mysql_fetch_assoc(mysql_query($query_master));

$objLancamentoItens = new ContabilLancamentoItemClass();

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;

$id_projeto = $_REQUEST['projeto'];
$data_ini = (!empty($_REQUEST['data_ini'])) ? $_REQUEST['data_ini'] : "01/".date('m/Y');
$data_fim = (!empty($_REQUEST['data_fim'])) ? $_REQUEST['data_fim'] : date('t', date('m-Y')."-01").date('/m/Y');

if(isset($_REQUEST['data_ini']) && isset($_REQUEST['data_fim']) && isset($_REQUEST['projeto']) && isset($_REQUEST['filtrar']) && $_REQUEST['projeto'] >  0) {
    
    $data_ini_bd = implode('-',array_reverse(explode('/', $data_ini)));
    $data_fim_bd = implode('-',array_reverse(explode('/', $data_fim)));
    $projeto = $_REQUEST['projeto'];
    
    $arrayLancamentos = $objLancamentoItens->getLivroDiario($projeto, $data_ini_bd, $data_fim_bd);
    $qtd_lanc = count($arrayLancamentos);
}
      
$nome_pagina = "Livro Diário";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "frmplanodeconta");
$breadcrumb_pages = array("Relatórios Contábeis"=>"index.php"); 

if ($_REQUEST['filtrar'] == 'Imprimir')  {

    class PDF extends FPDF {

        public $master;

        function Header() {
            $this->SetFont('Arial', 'B', 8);
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
            $this->SetFont('Arial', 'B', 10);
            $this->SetLineWidth(0);
            $this->Ln();
            $this->Ln();
            $this->Cell(9.5, .4, 'LIVRO DIÁRIO', 0, 0, 'R');
            $this->Cell(9.5, .4, $_REQUEST['data_ini']." a ".$_REQUEST['data_fim'],0, 0, 'R');
            $this->SetFont('Arial', 'B', 8);
            $this->SetLineWidth(0);
            $this->cell(6, .3, $sintetico, 0, 0, 'R' );
            $this->Ln();
            $this->Ln();
            $this->Cell(19, 0, NULL, 0, 0, 'B' , 'C');
            $this->Cell(3,.5);
            $this->Ln();
            $this->SetFont('Arial', 'B', 6);
            $this->SetLineWidth(0);
            $this->Cell(1.3, .3, 'DATA', NULL, 0, 'L');
            $this->Cell(3, .3, 'CONTA', 0, 'L');
            $this->Cell(10, .3, 'HISTÓRICO', 0, 0, 'L');
            $this->Cell(3, .3, 'VALOR R$', 0, 0, 'R');
            $this->Ln();          
        }

        function Footer() {
            // Position at 1.5 cm from bottom
            $this->SetY(-1);
            // Arial italic 8
            $this->SetFont('Arial', NULL, 6.5);
            $this->SetLineWidth(0);
            // Page number
            $this->Cell(16, .8, 'F71 SISTEMAS WEB - módulo contábil', 'T', 0,'L');
            $this->Cell(3, .8, 'Pagina ' . $this->PageNo(), 'T',0, 'R');
        }
    }
    
    $pdf = new PDF('P', 'cm', 'A4');
    $pdf->master = $master;
    $pdf->SetY("-1");
    
    $pdf->setMargins(1, 1, 1);
    $pdf->AddPage();

    $pdf->SetAutoPageBreak(1, 1.5);
    
    foreach ($arrayLancamentos as $key => $row_lanc) {

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetLineWidth(0);
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 6);
        $pdf->SetLineWidth(0);
        $pdf->cell(1.3, .35, $row_lanc['data_lancamento'], 0, 0 );
        $pdf->Cell(4.7, .35, $row_lanc['classificador'].' - '. substr($row_lanc['descricao'], 0, 20), NULL, 0, 'L');
        $pdf->Cell(10, .35, $row_lanc['historico_l'], 0, 'J');
        $pdf->cell(3, .35,  ($row_lanc['valor'] < 0) ? "(".number_format($row_lanc['valor']*-1, 2, ',', '.').")" : number_format($row_lanc['valor'], 2, ',', '.'), NULL, 0, 'R');
        $pdf->Cell(1, .35, ($row_lanc['tipo'] == 2) ? 'D' : 'C', NULL, 0, 'L');
    }    
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
                            </div>
                            <div class="panel-footer text-right">
                                <?php if(count($qtd_lanc) > 0){ ?><button type="button" onclick="tableToExcel('tbRelatorio', 'Livro Razao')" name="Excel" value="Excel" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Exportar Excel</button>
                                <button type="submit" id="" name="filtrar" value="Imprimir" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Impressão</button><?php } ?>
                                <input type="hidden" id="" name="filtra" value="<?= $_REQUEST['filtra'] ?>">
                                <button type="submit" id="criar" name="filtrar" value="Filtrar" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filtrar</button>
                            </div>
                        </div>
                    </form>
                    <?php if(isset($_REQUEST['data_ini']) && isset($_REQUEST['data_fim']) && isset($_REQUEST['projeto']) && isset($_REQUEST['filtrar']) && $_REQUEST['projeto'] >  0) { ?>
                        
                        <table id="tbRelatorio" class="table table-condensed valign-middle">
                            <tr class="active">
                                <td colspan="4" class="text-center text-bold"><?= $master['razao'] ?></td>
                            </tr>
                            <tr class="active">
                                <td colspan="4" class="text-bold text-center">CNPJ <?= $master['cnpj'] ?></td>
                            </tr>
                            <tr class="active">
                                <td class="text-left"><?= date("d/m/Y - H:i:s") ?></td>
                                <td colspan="2" class="text-right">Folha</td>
                                <td class="text-center">1</td>
                            </tr>
                            <tr class="active">
                                <td colspan="4" class="text-left text-bold text-uppercase">Período de <?= $data_ini ?> à <?= $data_fim ?></td>
                            </tr>
                        </table>
                    
                        <?php foreach ($arrayLancamentos as $data => $row_data) { ?>
                            <table class="table table-condensed valign-middle">
                                <tr class="text text-bold text-info info text-sm" >
                                    <td colspan="5"><?= $data ?></td>
                                </tr>
                                <?php foreach ($row_data as $lanc => $row_lanc) { 
                                    foreach ($row_lanc as $key => $row_itens_lanc) { ?>
                                        <tr class="text text-sm text-default active">
                                            <td class="col-md-1">Lançamento<br><i class="text text-sm text-black"><?= $lanc ?></i></td>
                                            <td colspan="4">Histórico<br><i class="text text-sm text-black"><?= $key ?></i></td>
                                        </tr>
                                        <tr class="text text-sm text-default">
                                            <td>Conta</td>
                                            <td>Descrição</td>
                                            <td class="text text-right">Valor R$</td>
                                            <td></td>
                                        </tr>
                                        <?php foreach ($row_itens_lanc as $value) { ?>
                                            <tr class="text-sm text-<?= ($value['tipo'] == 1) ? 'danger' : 'info' ?>">
                                                <td class="col-md-2 "><?= $value[classificador] ?></td>
                                                <td><?= $value[descricao] ?></td>
                                                <td class="text text-right"><?= number_format($value[valor], 2, ',','.') ?></td>
                                                <td class="text-right"><?= ($value['tipo'] == 2) ? 'D' : 'C' ?></td>
                                        </tr>
                                    <?php } 
                                }
                            }
                        } ?>
                           
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
        <script src="../../resources/js/bootstrap-fileclass.min.js" type="text/javascript"></script>
    </body>
</html>
<?php } ?>