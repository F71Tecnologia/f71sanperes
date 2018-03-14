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

$id_projeto = $_REQUEST['projeto'];
$id_banco = $_REQUEST['id_banco'];
$data_ini = (!empty($_REQUEST['data_ini'])) ? $_REQUEST['data_ini'] : "01/" . date('m/Y');
$data_fim = (!empty($_REQUEST['data_fim'])) ? $_REQUEST['data_fim'] : date('t/m/Y');

if (isset($_REQUEST['data_ini']) && isset($_REQUEST['data_fim']) && (isset($_REQUEST['filtrar']) || isset($_REQUEST['excel']))) {

    $data_ini_bd = implode('-', array_reverse(explode('/', $data_ini)));
    $data_fim_bd = implode('-', array_reverse(explode('/', $data_fim)));
    
    $sql = "
    SELECT A.*
    FROM (
        SELECT id_saida AS Id, id_user, id_user_editou, id_userpg, id_deletado FROM saida WHERE (data_proc BETWEEN '$data_ini_bd' AND '$data_fim_bd') AND !(tipo BETWEEN 419 AND 431)
        UNION
        SELECT id_entrada AS Id, id_user, '', id_userpg, id_deletado FROM entrada WHERE (data_proc BETWEEN '$data_ini_bd' AND '$data_fim_bd') AND !(tipo BETWEEN 419 AND 431)
    ) AS A";
//    print_array($sql);
    $qry = mysql_query($sql) or die(mysql_error());
    while ($row = mysql_fetch_assoc($qry)) {
        
        if($row['id_user'])
        $array[$row['id_user']]['criou']++;
        
        if ($row['id_user_editou'])
        $array[$row['id_user_editou']]['editou']++;
        
        if($row['id_userpg'])
        $array[$row['id_userpg']]['baixou']++;
        
        if($row['id_deletado'])
        $array[$row['id_deletado']]['deletou']++;
    }
    //INICIO CONTADOR BORDERO    
    $SqlBordero= "SELECT id_funcionario, count( id_funcionario)  as conta FROM bordero GROUP BY (id_funcionario)";
    $QuerySqlBordero= mysql_query($SqlBordero);
   //FIM CONTADOR BORDERO
    
     //INICIO CONTAR AGRUPAMENTO
    $SqlAgrupamento=" SELECT  DISTINCT A.id_user, count(A.id_user) AS contarAgrupa FROM saida AS  A
INNER JOIN saida_agrupamento_assoc AS B WHERE A.id_saida= B.id_saida_pai group BY(A.id_user)";
    $QuerySqlAgrupamento= mysql_query($SqlAgrupamento);
    //FIM CONTAR AGRUPAMENTO
    

    $sqlF = "SELECT id_funcionario, nome FROM funcionario WHERE oculto = 0 ORDER BY nome";
    $qryF = mysql_query($sqlF);
    while($rowF = mysql_fetch_assoc($qryF)){
        $arrayFuncionarios[$rowF['id_funcionario']] = $rowF['nome'];
    }
    unset($array[''], $array[0]);
//    print_array($array);
    if(isset($_REQUEST['excel'])){
        $arquivo = 'Relatório Produção.xls';
        header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header ("Cache-Control: no-cache, must-revalidate");
        header ("Pragma: no-cache");
        header ("Content-type: application/x-msexcel");
        header ("Content-Disposition: attachment; filename={$arquivo}" );
        header ("Content-Description: PHP Generated Data" );
    }
}

$count = 0;
$nome_pagina = "Relatório Produção";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../", "key_btn" => "4", "area" => "Financeiro", "id_form" => "form1", "ativo" => $nome_pagina);
$breadcrumb_pages = array("Principal" => "../index.php");
?>
<?php if(!isset($_REQUEST['excel'])) { ?>
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
                    <div class="page-header box-financeiro-header hidden-print">
                        <h2><?php echo $icon['4'] ?> - Financeiro <small>- <?= $nome_pagina ?></small></h2>
                    </div>
                    <form action="" method="post" name="form" id="form" class="form-horizontal top-margin hidden-print" enctype="multipart/form-data">
                        <div class="panel panel-default">
                            <div class="panel-body">
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
                                <?php if (count($qtd_lanc) > 0) { ?><button type="submit" name="excel" value="Excel" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Exportar Excel</button><?php } ?>
                                <button type="submit" id="criar" name="filtrar" value="Filtrar" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filtrar</button>
                            </div>
                        </div>
                    </form>
<?php } ?>
                    <?php if (isset($_REQUEST['data_ini']) && isset($_REQUEST['data_fim']) && (isset($_REQUEST['filtrar']) || isset($_REQUEST['excel']))) { ?>
                        <table id="tbRelatorio" class="table table-condensed valign-middle table-bordered table-striped">
                            <tr>
                                <td class="text-center text-bold">NOME</td>
                                <td class="text-center text-bold">CRIOU</td>
                                <td class="text-center text-bold">EDITOU</td>
                                <td class="text-center text-bold">BAIXOU</td>
                                <td class="text-center text-bold">DELETOU</td>
				<td class="text-center text-bold">BORDERO</td>
				<td class="text-center text-bold">AGRPAMENTOS</td>
				
                            </tr>
			    <?php
			    //bordero
			    while($resu= mysql_fetch_array($QuerySqlBordero)){ 
			        $arraybordero[$resu['id_funcionario']] = $resu['conta']; 
			    }
			    //bordero
			    //agrupamento
			    while($resuAgrupa= mysql_fetch_array($QuerySqlAgrupamento)){ 
			        $arrayAgrupa[$resuAgrupa['id_user']] = $resuAgrupa['contarAgrupa']; 
			    }
			    
			    //agrupamento
			    
			    ?>
                            <?php foreach ($arrayFuncionarios as $key => $nome) {
			    //print '<pre>';
			    //print_r($key);
			    //print '</pre>';
				
			     ?>
                                <?php if($array[$key]) {			   
				   
					 ?>
                                <tr class="">
                                    <td class="text-left text-uppercase"><?= $nome ?></td>
                                    <td class="text-center"><?= $array[$key]['criou'] ?></td>
                                    <td class="text-center"><?= $array[$key]['editou'] ?></td>
                                    <td class="text-center"><?= $array[$key]['baixou'] ?></td>
                                    <td class="text-center"><?= $array[$key]['deletou'] ?></td>
				    <td class="text-center"><?php echo $arraybordero[$key]?></td>
				    <td class="text-center"><?php echo $arrayAgrupa[$key]?></td>
				    
				    
				      
                                </tr>
                                <?php }
				?>
                            <?php } ?>
                        </table>
                    <?php } else { ?>
                        <div class="alert alert-info">Nenhuma informação encontrada neste filtro!</div>
                    <?php } ?>
<?php if(!isset($_REQUEST['excel'])) { ?>
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