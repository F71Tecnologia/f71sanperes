<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include("../../conn.php");
include("../../wfunction.php");
include("../../empresa.php");
include("../../classes/BotoesClass.php");

$usuario = carregaUsuario(); 
$master = mysql_fetch_assoc(mysql_query("SELECT * FROM master WHERE id_master = {$usuario['id_master']} LIMIT 1;"));

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;

$id_projeto = $_REQUEST['projeto'];
$data_ini = (!empty($_REQUEST['data_ini'])) ? $_REQUEST['data_ini'] : "01/".date('m/Y');
$data_fim = (!empty($_REQUEST['data_fim'])) ? $_REQUEST['data_fim'] : date('t', date('m-Y')."-01").date('/m/Y');

if(isset($_REQUEST['data_ini']) && isset($_REQUEST['data_fim']) && isset($_REQUEST['projeto']) && isset($_REQUEST['filtrar']) && $_REQUEST['projeto'] >  0) {
    
    $data_ini_bd = implode('-',array_reverse(explode('/', $data_ini)));
    $data_fim_bd = implode('-',array_reverse(explode('/', $data_fim)));
    $projeto = $_REQUEST['projeto'];
    
    $sqlSaldo = "SELECT SUM(IF(M.tipo = 'e', valor,-valor)) saldo FROM
    (SELECT valor, 's' tipo FROM saida WHERE status = 2 AND id_projeto = '{$projeto}' AND data_pg < '$data_ini_bd'
    UNION
    SELECT valor, 'e' tipo FROM entrada WHERE status = 2 AND id_projeto = '{$projeto}' AND data_pg < '$data_ini_bd') M;";
    $saldo = number_format(mysql_result(mysql_query($sqlSaldo),0), 2, '.', '');
    
    $sql = "
    SELECT data_pg, nome, especifica, valor, 's' tipo FROM saida WHERE status = 2 AND id_projeto = '{$projeto}' AND data_pg BETWEEN '$data_ini_bd' AND '$data_fim_bd'
    UNION
    SELECT data_pg, nome, especifica, valor, 'e' tipo FROM entrada WHERE status = 2 AND id_projeto = '{$projeto}' AND data_pg BETWEEN '$data_ini_bd' AND '$data_fim_bd'
    ORDER BY data_pg ASC, tipo";
    $qry = mysql_query($sql);
    while($row = mysql_fetch_assoc($qry)){
        $arrayLancamentos[] = $row;
    }
    
    //$arrayLancamentos = $objLancamentoItens->getLivroDiario($projeto, $data_ini_bd, $data_fim_bd);
    $qtd_lanc = count($arrayLancamentos);
//    print_array($arrayLancamentos);
}

// Configurações header para forçar o download
//header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
//header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
//header ("Cache-Control: no-cache, must-revalidate");
//header ("Pragma: no-cache");
//header ("Content-type: application/x-msexcel");
//header ("Content-Disposition: attachment; filename=\"MODELO - LALUR.xls\"" );
//header ("Content-Description: PHP Generated Data" );
      
$nome_pagina = "Fluxo de Caixa";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "frmplanodeconta");
$breadcrumb_pages = array("Relatórios Contábeis"=>"index.php"); ?>

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
                                <?php if(count($qtd_lanc) > 0){ ?><button type="button" onclick="tableToExcel('tbRelatorio', 'Livro Razao')" name="Excel" value="Excel" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Exportar Excel</button><?php } ?>
                                <button type="submit" id="criar" name="filtrar" value="Filtrar" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filtrar</button>
                            </div>
                        </div>
                    </form>
                    <?php if(isset($_REQUEST['data_ini']) && isset($_REQUEST['data_fim']) && isset($_REQUEST['projeto']) && isset($_REQUEST['filtrar']) && $_REQUEST['projeto'] >  0) { ?>
                    <table id="tbRelatorio" class="table table-condensed valign-middle">
                        <tr class="active">
                            <td colspan="3" class="text-left"><?= $master['nome'] ?></td>
                            <td colspan="2" class="text-right"><?= $master['nome'] ?></td>
                        </tr>
                        <tr class="active">
                            <td colspan="5" class="text-center text-bold"><?= $master['razao'] ?></td>
                        </tr>
                        <tr class="active">
                            <td colspan="5" class="text-bold text-center">CNPJ: <?= $master['cnpj'] ?></td>
                        </tr>
                        <tr class="active">
                            <td colspan="2" class="text-left"><?= date("d/m/Y H:i:s") ?></td>
                            <td colspan="2" class="text-right">Folha</td>
                            <td class="text-center">1</td>
                        </tr>
                        <tr class="active">
                            <td colspan="5" class="text-left text-bold text-uppercase">Razão de <?= $data_ini ?> até <?= $data_fim ?></td>
                        </tr>
                        <tr>
                            <td class="text-left text-bold">DATA</td>
                            <td class="text-left text-bold">DESCRIÇÃO</td>
                            <td class="text-left text-bold">ENTRADA</td>
                            <td class="text-right text-bold">SAÍDA</td>
                            <td class="text-right text-bold">SALDO</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right text-bold">SALDO ANTERIOR: </td>
                            <td colspan="2" class="text-right text-bold"><?= ($saldo < 0) ? '('.number_format($saldo*-1, 2, ',', '.').')' : number_format($saldo, 2, ',', '.') ?></td>
                        </tr>
                        <?php foreach ($arrayLancamentos as $key => $row_lanc) { 
                            $saldo += ($row_lanc['tipo'] == 'e') ? $row_lanc['valor'] : -$row_lanc['valor']; ?>
                            <tr class="">
                                <td class="text-left"><?= implode('/', array_reverse(explode('-', $row_lanc['data_pg']))) ?></td>
                                <td class="text-left"><?= (!empty($row_lanc['especifica'])) ? $row_lanc['especifica'] : $row_lanc['nome'] ?></td>
                                <td class="text-right"><?= ($row_lanc['tipo'] == 'e') ? number_format($row_lanc['valor'], 2, ',', '.') : '0,00' ?></td>
                                <td class="text-right"><?= ($row_lanc['tipo'] == 's') ? number_format($row_lanc['valor'], 2, ',', '.') : '0,00' ?></td>
                                <td class="text-right"><?= ($saldo < 0) ? '('.number_format($saldo*-1, 2, ',', '.').')' : number_format($saldo, 2, ',', '.') ?></td>
                            </tr>
                        <?php } ?>
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
        <script>
            $(function(){
                $('#form').validationEngine();
                $('body').on('change', '#projeto', function(){
                    console.log($("#contas").val());
                    $.post("", {bugger:Math.random(), method:'select_contas', projeto:"'"+$(this).val()+"'", conta:'<?= $_REQUEST['contas'] ?>' }, function(resultado){
                        $("#contas").html(resultado);
                    });
                });
                $('#projeto').trigger('change');
            })
        </script>
    </body>
</html>