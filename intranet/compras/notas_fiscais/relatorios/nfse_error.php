<?php
//session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../../login.php?entre=false';</script>";
}
error_reporting(E_ALL);

include("../../../conn.php");
include("../../../wfunction.php");
include("../../../classes_permissoes/acoes.class.php");
include("../../../classes/BotoesClass.php");
include("../../../classes/NFSeClass.php");
include("../../../classes/BancoClass.php");
include("../../../classes/global.php");

$usuario = carregaUsuario();
$id_regiao = (isset($_REQUEST['id_regiao'])) ? $_REQUEST['id_regiao'] : $usuario['id_regiao'];
$acoes = new Acoes();

$global = new GlobalClass();

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'salvar') {
    $string = '-- ' . date('d/m/Y H:i:s') . " - Relatório de erro do CodigoTributacaoMunicipio - Correcao\r\n\r\n# ---- Alteracoes ---- #\r\n\r\n";
    foreach ($_REQUEST['CodigoTributacaoMunicipio'] as $key => $codigo) {
        if (!empty($codigo)) {
            $query_update = "UPDATE nfse SET CodigoTributacaoMunicipio = '$codigo' WHERE id_nfse = '{$_REQUEST['id_nfse'][$key]}'";
            mysql_query($query_update) or die(mysql_error());
            $string .= "-- Editando nfse: id_nfse = {$_REQUEST['id_nfse'][$key]} :: CodigoTributacaoMunicipio = $codigo\r\n";
            $arr_ids[] = $_REQUEST['id_nfse'][$key];
        }
    }

    $query = "\r\nSELECT * FROM nfse WHERE id_nfse IN(" . implode(',', $arr_ids) . ");\r\n\r\n\r\n\r\n\r\n\r\n";

    // Abre ou cria o arquivo bloco1.txt
// "a" representa que o arquivo é aberto para ser escrito
    $fp = fopen("correcao-" . date('Y-m') . ".sql", "a");

// Escreve "exemplo de escrita" no bloco1.txt
    $escreve = fwrite($fp, $string . $query);

// Fecha o arquivo
    fclose($fp);
}

//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
switch ($_REQUEST['mod']) {
    case 'finan':
        $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
        $breadcrumb_config = array("nivel" => "../../../", "key_btn" => "4", "area" => "Financeiro", "ativo" => "CONSULTAR NOTAS FISCAIS DE SERVIÇO", "id_form" => "form1");
        $breadcrumb_pages = array("Principal" => "../../../finan/index.php");
        break;
    case 'contabil':
        $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
        $breadcrumb_config = array("nivel" => "../../../", "key_btn" => "38", "area" => "Gestão Contábil", "ativo" => "CONSULTAR NOTAS FISCAIS DE SERVIÇO", "id_form" => "form1");
        break;
    case 'contratos':
    default:
        $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
        $breadcrumb_config = array("nivel" => "../../../", "key_btn" => "35", "area" => "Gestão de Contratos", "ativo" => "CONSULTAR NOTAS FISCAIS DE SERVIÇO", "id_form" => "form1");
        break;
}


// a consulta do relatorio
$query = "  SELECT a.id_nfse,a.id_regiao,a.id_projeto,a.DataEmissao,a.Competencia,a.PrestadorServico,a.Numero,a.CodigoTributacaoMunicipio, b.nome AS nome_projeto, c.*,a.status AS nfse_status, 
                (SELECT arquivo_pdf 
                    FROM nfse_anexos AS e 
                    WHERE a.Numero = e.numero_nota AND a.CodigoVerificacao = e.codigo_verificador 
                    AND a.id_projeto = e.id_projeto AND a.PrestadorServico = e.id_prestador 
                    ORDER BY id DESC LIMIT 1) AS arquivo_pdf
            FROM nfse AS a 
            INNER JOIN projeto AS b ON a.id_projeto = b.id_projeto 
            INNER JOIN prestadorservico AS c ON a.PrestadorServico = c.id_prestador 
            WHERE (a.CodigoTributacaoMunicipio = '' OR a.CodigoTributacaoMunicipio IS NULL) AND a.id_regiao = {$id_regiao}
            ORDER BY a.id_projeto,a.Competencia,a.PrestadorServico;";
$result = mysql_query($query) or die(mysql_error());
$arr_nfse = array();
while ($row = mysql_fetch_assoc($result)) {
    $arr_nfse[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: CONSULTAR NOTAS FISCAIS DE SERVIÇO</title>
        <link rel="shortcut icon" href="../../../favicon.png">
        <!-- Bootstrap -->        
        <link href="../../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-compras.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <style>
            table.nfse{
                width: 100%;
                border-collapse: collapse;
                border: 1px solid #000;
            }
            table.nfse td, table.nfse th{
                border: 1px solid #000;
                padding: 3px;
            }

            tr.warning td{
                background-color: #ffff99;
            }
            tr.danger td{
                background-color: #ff9999;
            }

            tr.correcao td {
                background-color:  #ffe0b3;
            }

        </style>
    </head>
    <body>
        <?php include("../../../template/navbar_default.php"); ?> 
        <div class="container"> 
            <div class="row">
                <div class="col-lg-12">
                    <?php
                    switch ($_REQUEST['mod']) {
                        case 'finan':
                            ?>
                            <div class="page-header box-financeiro-header">
                                <h2><span class="glyphicon glyphicon-usd"></span> - Financeiro <small>- CONSULTAR NOTAS FISCAIS DE SERVIÇO</small></h2>
                            </div>
                            <?php
                            break;
                        case 'contabil':
                            ?>
                            <div class="page-header box-contabil-header">
                                <h2><span class="fa fa-bar-chart"></span> - Contabilidade <small>- CONSULTAR NOTAS FISCAIS DE SERVIÇO</small></h2>
                            </div>
                            <?php
                            break;
                        case 'contratos':
                        default:
                            ?>
                            <div class="page-header box-compras-header">
                                <h2><span class="glyphicon glyphicon-shopping-cart"></span> - Gestão de Contratos <small>- CONSULTAR NOTAS FISCAIS DE SERVIÇO</small></h2>
                            </div>
                            <?php
                            break;
                    }
                    ?>
                    <!--resposta de algum metodo realizado-->
                    <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>                                
                    <!--<div role="tabpanel">-->
                    <form class="form-horizontal" method="post">

                        <?php if (count($arr_nfse)) {
                            ?>
                            <div class="row">
                                <?php
                                $cor = array(1 => ' #ffe0b3', 2 => "#d9edf7", 3 => "#fcf8e3", 4 => "#dff0d8", 0 => "#f2dede");
                                foreach ($opcoes as $key => $value) {
                                    if ($key >= 0) {
                                        ?>
                                        <div class="col-md-3"><span style="border:1px solid #ccc; background-color: <?= $cor[$key] ?>">&emsp;</span> <?= $value ?></div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            <br>
                            <div class="panel panel-default">
                                <div class="table-responsive">
                                    <table class="table table-condensed text-sm">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Numero</th>
                                                <th>Data</th>
                                                <th>Projeto</th>
                                                <th>Prestador</th>
                                                <th style="width: 155px;">CNPJ</th>
                                                <th colspan="4">&emsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($arr_nfse as $values) { ?>
                                                <?php $class = array(1 => 'correcao', 2 => "info", 3 => "warning", 4 => "success", 0 => "danger") ?>
                                                <?php // $class = array(1 => 'bg-yellow-light', 2 => "bg-info-light", 3 => "bg-warning-light", 4 => "bg-success-light", 0 => "bg-danger-light") ?>
                                                <tr class="<?= $class[$values['nfse_status']] ?>">
                                                    <td><?= $values['id_nfse'] ?></td>
                                                    <td><?= $values['Numero'] ?></td>
                                                    <td><?= ConverteData($values['DataEmissao'], 'd/m/Y') ?></td>
                                                    <td><?= $values['nome_projeto'] ?></td>
                                                    <td><?= $values['c_razao'] ?></td>
                                                    <td><?= $values['c_cnpj'] ?></td>
                                                    <td>
                                                        <?php if (!empty($values['arquivo_pdf'])) { ?>
                                                            <a href="../nfse_anexos/<?= $values['id_projeto'] ?>/<?= $values['arquivo_pdf'] ?>" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-file-pdf-o text-danger"></i> PDF</a>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="id_nfse[]" class="from-control" value="<?= $values['id_nfse'] ?>">
                                                        <input type="text" name="CodigoTributacaoMunicipio[]" class="form-control input-sm">
                                                    </td>

                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="panel-footer text-right">
                                    <a class="btn btn-info" href="correcao-<?= date('Y-m') ?>.sql"><i class="fa fa-download"></i> Download SQL</a>
                                    <button type="submit" name="method" value="salvar" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Salvar</button>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-danger">
                                <i class="fa fa-info-circle fa-lg"></i> Não há Notas fiscais com o para esta consulta.
                            </div>
                        <?php } ?>
                            
                    </form>
                </div>
            </div> 
            <?php include_once '../../../template/footer.php'; ?>
        </div>
        <script src="../../../js/jquery-1.10.2.min.js"></script>
        <script src="../../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../../resources/js/bootstrap.min.js"></script>
        <script src="../../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../../resources/js/main.js"></script>
        <script src="../../../js/global.js"></script>
        <script src="../../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../../resources/js/financeiro/saida.js"></script>
        <script src="../../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../../js/jquery.form.js" type="text/javascript"></script>
        <script src="../../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="../js/relatorio_nfse.js" type="text/javascript"></script>
        <?php if ($acoes->verifica_permissoes(113)) { // apenas se houver permissão para editar que a mensagem de notas com erro de cadastro aparecerão    ?>
            <script src="../js/verifica_nfse_correcao.js" type="text/javascript"></script>
        <?php } ?>
        <style>
            .legends{
                font-size: 0.9em;
                color: silver;
            }
        </style>
    </body>
</html>