<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../funcoes.php");
include("../../wfunction.php");
include("../../classes/FolhaClass.php");

$objFolha = new Folha();
$usuario = carregaUsuario();
//$container_full = true;
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$mes = ($_REQUEST['mes']) ? $_REQUEST['mes'] : date('m');
$ano = ($_REQUEST['ano']) ? $_REQUEST['ano'] : date('Y');
$id_projeto = ($_REQUEST['id_projeto']) ? $_REQUEST['id_projeto'] : null;

/**
 * MONTA SELECT CNPJ
 */
$sqlCNPJ = "SELECT CONCAT(A.cnpj,' - ',GROUP_CONCAT(B.nome SEPARATOR ', ')) nome, GROUP_CONCAT(A.id_projeto) id_projeto FROM rhempresa A LEFT JOIN projeto B ON (A.id_projeto = B.id_projeto) WHERE A.id_master = {$usuario['id_master']} GROUP BY A.cnpj;";
$qryCNPJ = mysql_query($sqlCNPJ);
while($rowCNPJ = mysql_fetch_assoc($qryCNPJ)){
    $arrayCNPJ[$rowCNPJ['id_projeto']] = $rowCNPJ['nome'];
}

/**
* MONTA ARRAY COM O NOME DOS PROJETOS
*/
$sqlProjeto = "SELECT id_projeto, nome FROM projeto WHERE id_master = {$usuario['id_master']}";
$qryProjeto= mysql_query($sqlProjeto);
while($rowProjeto = mysql_fetch_assoc($qryProjeto)){
   $arrayProjeto[$rowProjeto['id_projeto']] = $rowProjeto['nome'];
}

if($_REQUEST['filtrar']){
    
    $sql = "SELECT H.id_autonomo, H.nome, G.valor_iss AS 'ISS', G.valor_inss AS 'INSS', G.valor_ir AS 'IR', G.valor AS 'VALOR'
    FROM rpa_autonomo AS G
    LEFT JOIN autonomo AS H ON(G.id_autonomo = H.id_autonomo)
    WHERE H.id_projeto = {$id_projeto} AND H.status_reg = 1 AND G.mes_competencia = {$mes} AND G.ano_competencia = {$ano} 
    AND H.pis IN (SELECT pis FROM rh_folha_proc rfp LEFT JOIN rh_clt rc ON (rfp.id_clt = rc.id_clt) WHERE rfp.mes = {$mes} AND rfp.ano = {$ano} AND rfp.id_projeto = {$id_projeto})
    ORDER BY H.nome";
    if($_COOKIE['debug'] == 666){
        print_array($sql);
    }
    $qry = mysql_query($sql);
    $num = mysql_num_rows($qry);
}

if($_COOKIE['debug'] == 666){
    print_array('////////////////////////////////$arrayLegenda////////////////////////////////');
    print_array($arrayLegenda);
}
/**
 * PARAMETROS DE CONFIG DA PAGINA
 */
$nome_pagina = "RELATÓRIO RPAs PAGOS NA FOLHA";
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"$nome_pagina");
$breadcrumb_pages = array("Gestão de Orçamentos"=>"../relatorios/gestao_orcamentos.php");

$borderMes = ' style="border: 2px solid #00F;" '; 
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
        <link href="http://<?=$_SERVER['SERVER_NAME']?>/intranet/resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="http://<?=$_SERVER['SERVER_NAME']?>/intranet/resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="http://<?=$_SERVER['SERVER_NAME']?>/intranet/resources/css/main.css" rel="stylesheet" media="all">
        <link href="http://<?=$_SERVER['SERVER_NAME']?>/intranet/resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="http://<?=$_SERVER['SERVER_NAME']?>/intranet/resources/dropzone/dropzone.css" rel="stylesheet" media="all">
        <link href="http://<?=$_SERVER['SERVER_NAME']?>/intranet/resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css" media="all">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="<?=($container_full) ? 'container-full' : 'container'?>">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro  - <small><?= $nome_pagina ?></small></h2></div>
                    <form action="" method="post" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data" autocomplete="off">
                    <div class="panel panel-default hidden-print">
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <div class="text-bold">Projeto:</div>
                                    <?= montaSelect($arrayProjeto, $_REQUEST['id_projeto'], 'class="form-control input-sm" id="id_projeto" name="id_projeto"') ?>
                                </div>
                                <div class="col-sm-4">
                                    <div class="text-bold">Competência:</div>
                                    <div class="input-group" id="">
                                        <?= montaSelect(mesesArray(), $mes, 'class="form-control input-sm" id="mes" name="mes"') ?>
                                        <div class="input-group-addon">/</div>
                                        <?= montaSelect(anosArray(2016), $ano, 'class="form-control input-sm" id="ano" name="ano"') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <button name="filtrar" class="btn btn-primary" value="filtrar"><i class="fa fa-filter"></i> FILTRAR</button>
                        </div>
                    </div>
                    </form>
                    <hr>
                    <?php if($num > 0) { ?>
                    <button type="button" form="formPdf" name="pdf" data-title="<?= $nome_pagina ?>" data-id="relatorio" id="pdf" data-orientacao="l" value="Gerar PDF" class="btn btn-danger">Gerar PDF</button>
                    <button type="button" id="tableToExcelWithCss" class="btn btn-success margin_b10 pull-right hidden-print"><i class="fa fa-file-excel-o"></i> Exportar para Excel</button>
                    <table id="relatorio" class="table table-bordered table-hover table-striped table-condensed valign-middle text-sm">
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">NOME</th>
                            <th class="text-center">VALOR</th>
                            <th class="text-center">INSS</th>
                            <th class="text-center">IR</th>
                            <th class="text-center">ISS</th>
                        </tr>
                        <?php while ($row = mysql_fetch_assoc($qry)) { 
                            $arrayTotal['VALOR'] += $row['VALOR']; 
                            $arrayTotal['INSS'] += $row['INSS']; 
                            $arrayTotal['IR'] += $row['IR']; 
                            $arrayTotal['ISS'] += $row['ISS']; 
                            ?>
                            <tr>
                                <td class="text-center"><?= $row['id_autonomo'] ?></td>
                                <td class="text-left"><?= $row['nome'] ?></td>
                                <td class="text-right"><?= number_format($row['VALOR'], 2, ',', '.') ?></td>
                                <td class="text-right"><?= number_format($row['INSS'], 2, ',', '.') ?></td>
                                <td class="text-right"><?= number_format($row['IR'], 2, ',', '.') ?></td>
                                <td class="text-right"><?= number_format($row['ISS'], 2, ',', '.') ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td colspan="2" class="text-right">TOTAIS:</td>
                            <td class="text-right"><?= number_format($arrayTotal['VALOR'], 2, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($arrayTotal['INSS'], 2, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($arrayTotal['IR'], 2, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($arrayTotal['ISS'], 2, ',', '.') ?></td>
                        </tr>
                    </table>
                    <?php } else { ?>
                        <div class="alert alert-info text-bold">Nenhuma informação encontrada!</div>
                    <?php } ?>
                </div>
            </div>
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
        <script>
        $(function(){
            $('body').on('change', '#id_projeto', function(){
                $.post('', {method: 'unidades', id_projeto: $(this).val()}, function(result){
                    $('#div_unidade').html(result);
                });
            });
            
            
            $('#tableToExcelWithCss').click(function(){
                $('th, td, tr').each(function($i,$v){
                    $($v).css('background-color', $($v).css('background-color'));
                    $($v).css('color', $($v).css('color'));
                    $($v).css('font', $($v).css('font'));
                    $($v).css('text-align', $($v).css('text-align'));
                    $($v).css('vertical-align', $($v).css('vertical-align'));
                });
                tableToExcel('relatorio', '', true);
            });
        });
        </script>
    </body>
</html>