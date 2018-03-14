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

$bancos_opt = ['0' => 'Todos os Bancos'];
$query = "SELECT * FROM bancos WHERE id_regiao IN(" . implode(', ', $regioes_usuario) . ") ORDER BY id_banco";
$result = mysql_query($query);
while ($row = mysql_fetch_assoc($result)) {
    $bancos_opt[$row['id_banco']] = $row['id_banco'] . ' - ' . $row['nome'];
}

$id_projeto = $_REQUEST['Selprojeto'];
$id_banco = $_REQUEST['id_banco'];
$data_ini = (!empty($_REQUEST['data_ini'])) ? $_REQUEST['data_ini'] : "01/" . date('m/Y');
$data_fim = (!empty($_REQUEST['data_fim'])) ? $_REQUEST['data_fim'] : date('t/m/Y');

if (isset($_REQUEST['data_ini']) && isset($_REQUEST['data_fim']) && isset($_REQUEST['Selprojeto']) && (isset($_REQUEST['filtrar']))) {

    $data_ini_bd = implode('-', array_reverse(explode('/', $data_ini)));
    $data_fim_bd = implode('-', array_reverse(explode('/', $data_fim)));

    $auxProjeto = ($_REQUEST['Selprojeto']) ? " AND A.id_projeto = '{$_REQUEST['Selprojeto']}' " : '';
    $auxBanco = ($_REQUEST['id_banco']) ? " AND A.id_banco = '{$_REQUEST['id_banco']}' " : '';
    
    $sql = "
    SELECT A.*, REPLACE(A.valor, ',', '.') AS valor, B.nome AS nomeProjeto, D.nome AS nomeDespesa, E.c_razao
    FROM saida AS A
    LEFT JOIN projeto B ON (A.id_projeto = B.id_projeto)
    LEFT JOIN entradaesaida D ON (A.tipo = D.id_entradasaida)
    LEFT JOIN prestadorservico E ON (A.id_prestador = E.id_prestador)
    WHERE A.status IN (1) {$auxProjeto} {$auxBanco} AND A.data_vencimento BETWEEN '$data_ini_bd' AND '$data_fim_bd'
    AND D.nome LIKE 'ISS%'";
//    print_array($sql);
    $qry = mysql_query($sql) or die(mysql_error());
    while ($row = mysql_fetch_assoc($qry)) {
        $arrayLancamentos[] = $row;
    }
}
//print_array($arrayLancamentos);
$nome_pagina = "Relatório de ISS";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "frmplanodeconta");
$breadcrumb_pages = array("Relatórios Contábeis" => "index.php");
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
        <style>
            .alert-float {
                display: inline-block;
                position: fixed;
                right: 20px;
                bottom: 40%;
                color: #555;
                text-decoration: none;
                font-size: 25px;
                opacity: 0.7;
                /*display: none;*/
                -webkit-transition: .150s;
                transition: .150s;
            }        
        </style>
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
                                    <div class="col-sm-6">
					<?php print $usuario['id_master']; ?>
                                        <label for="projeto" class="control-label">Projeto</label>
                                        <?=montaSelect($global->carregaProjetosByMaster($usuario['id_master'], array("0" => "Todos os Projetos")), $id_projeto, "id='Selprojeto' name='Selprojeto' class='form-control input-sm validate[required,custom[select]]'") ?>
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
                                </div>
                            </div>
                            <div class="panel-footer text-right">
				<button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i>
                        Imprimir
                    </button>
                                <?php if (count($qtd_lanc) > 0) { ?><button type="submit" name="excel" value="Excel" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Exportar Excel</button><?php } ?>
                                <button type="submit" id="criar" name="filtrar" value="Filtrar" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filtrar</button>
                            </div>
                        </div>
                    </form>
                    <?php if (isset($_REQUEST['data_ini']) && isset($_REQUEST['data_fim']) && isset($_REQUEST['Selprojeto']) && (isset($_REQUEST['filtrar']) || isset($_REQUEST['excel']))) { ?>
                        <table id="tbRelatorio" class="table table-condensed valign-middle table-bordered table-striped">
                            <tr>
                                <td class="text-left text-bold"><input type="hidden" id="recebeparaprint" value=""></td>
                                <td class="text-left text-bold">CÓD</td>
                                <td class="text-left text-bold">DATA</td>
                                <td class="text-left text-bold">DESCRIÇÃO</td>
                                <td class="text-left text-bold">Nº NOTA</td>
                                <td class="text-left text-bold">VALOR</td>
                            </tr>
                            <?php
                            foreach ($arrayLancamentos as $key => $row_lanc) { ?>
                                <tr class="">
                                    <td class="text-center"><input type="checkbox" name="saidas_check" data-val="<?php echo str_replace(',','.',$row_lanc['valor'])?>" class="saidas_check" value="<?= $row_lanc['id_saida'] ?>"></td>
                                    <td class="text-left"><?= $row_lanc['id_saida'] ?></td>
                                    <td class="text-left"><?= implode('/', array_reverse(explode('-', $row_lanc['data_vencimento']))) ?></td>
                                    <td class="text-left"><?= $row_lanc[''] ?></td>
                                    <td class="text-left"><?= $row_lanc['n_documento'] ?></td>
                                    <td class="text-right"><?= number_format($row_lanc['valor'], 2, ',', '.') ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                        <button type="button" class="btn btn-info pull-right" id="group_all" data-toggle="tooltip" title="Agrupar Selecionadas"><i class="fa fa-clone"></i> Agrupar</button>
                    <?php } else { ?>
                        <div class="alert alert-info">Nenhuma informação encontrada neste filtro!</div>
                    <?php } ?>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <div class="alert alert-info alert-float hide" style="width: auto !important;"></div> 
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <!--<script src="../../js/jquery.form.js"></script>-->
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../resources/js/financeiro/index.js"></script> 
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap-fileclass.min.js" type="text/javascript"></script>
        <script>
        $(function(){
            function somatorio(campos, result) {
                var valor = 0.00;
                $(campos).each(function (index, value) { 
                    if($(value).data('val') && $(value).prop('checked')){
                        valor += parseFloat($(value).data('val'));
                    }
                });
                
                if(valor > 0) {
                    $(result).removeClass('hide');
                    $(result).html(number_format(valor, 2, ',', '.'));
                } else {
                    $(result).addClass('hide');
                }
            }

            $('body').on('click', '.saidas_check', function(){
                somatorio('.saidas_check', '.alert-float');
            });
	    
	    // $('body').on('click', '#group_all', function(){
	    
	   // if(ImprimiAgrupa !=''){
		//alert("toaqui"); return false;
	    //});
	    
        })
        </script>
	 <script>
       
      $(function() {	 
	    $('#imprimir').click(function () {
		 $('#imprimir').hide();
	    })
	})
	</script>
	 <script src="../../resources/js/print.js" type="text/javascript"></script>
    </body>
</html>