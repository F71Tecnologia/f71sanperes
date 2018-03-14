<?php // session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../funcoes.php");
include("../../classes/BotoesClass.php");
include("../../classes/SaidaClass.php");
include("../../classes/FinaceiroClass.php");
include("../../classes/global.php");
//print_array($_REQUEST);
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$saida = new Saida();
$objFinan = new Financeiro();
$global = new GlobalClass();

if(isset($_REQUEST['filtrar'])){
    $filtro = true;
    $result = $saida->getContasPagas(true);
    $total = mysql_num_rows($result);
}

$count=0;
$nome_pagina = "Contas Pagas";
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>$nome_pagina);
$breadcrumb_pages = array("Principal" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: <?php echo $nome_pagina ?></title>
        
        <link href="../../favicon.png" rel="shortcut icon" />
        
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <!--<link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">-->
        <link href="../../resources/css/datepicker.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="all">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <style>
            table { page-break-after: always; }
            tr    { page-break-inside:avoid; page-break-after:auto }
            thead { display:table-header-group }
            tfoot { display:table-footer-group }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - <?php echo $nome_pagina ?></small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                <div class="panel panel-default">
                    <div class="panel-heading text-bold">Buscar Lançamentos</div>
                    <div class="panel-body">
                        <div class="form-group no-margin-b no-margin-b">
                            <div class="col-xs-6">
                                <label for="select" class="control-label">Projeto</label>
                                <?php echo montaSelect($global->carregaProjetosByMaster($usuario['id_master'], array("0" => "Todos os Projetos")), $_REQUEST['id_projeto'], "id='id_projeto' name='id_projeto' class='form-control input-sm validate[required,custom[select]]'") ?>
                            </div>
                            <div class="col-xs-6">
                                <label for="select" class="control-label">Vencimento</label>
                                <div class="input-group">
                                <input type="text" class="form-control input-sm data" name="data_vencimento_ini" value="<?php echo ($_REQUEST['data_vencimento_ini']) ? $_REQUEST['data_vencimento_ini'] : '01' . date('/m/Y') ?>">
                                <div class="input-group-addon"> até </div>
                                <input type="text" class="form-control input-sm data" name="data_vencimento_fim" value="<?php echo ($_REQUEST['data_vencimento_fim']) ? $_REQUEST['data_vencimento_fim'] : date('t/m/Y') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group no-margin-b">
                            <div class="col-xs-6">
                                <label for="select" class="control-label">Tipo</label>
                                <?php echo montaSelect($objFinan->getTiposFiltro(), $_REQUEST['tipo'], " name='tipo' id='tipo' class='form-control input-sm'"); ?>
                            </div>
                            <div class="col-xs-6">
                                <label for="select" class="control-label">Banco</label>
                                <?php echo montaSelect($global->carregaBancosByMaster($usuario['id_master'], []), $_REQUEST['id_banco'], " name='id_banco' id='id_banco' class='form-control input-sm'"); ?>
                            </div>
                        </div>
                        <div class="form-group no-margin-b">
                            <div class="col-xs-6">
                                <label for="" class="control-label">Agrupamento</label>
                                <?php echo montaSelect([0 => "Sem Agrupamento", 1 => 'Projeto', 2 => 'Data', 3 => 'Centro de Custo', 4 => 'Prestador de Serviço'], $_REQUEST['agrupamento'], "id='agrupamento' name='agrupamento' class='form-control input-sm validate[required,custom[select]]'") ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right hidden-print">
                        <?php if ($total > 0) { ?><button type="button" onclick="tableToExcel('tbRelatorio', '<?php echo $nome_pagina ?>')" name="Excel" value="Excel" class="btn btn-success btn-sm"><i class="fa fa-file-excel-o"></i> Exportar Excel</button><?php } ?>
                        <!--<button type="button" id="imprimir" value="Imprimir" class="btn btn-sm btn-default"><i class="fa fa-print"></i> Imprimir</button>-->
                        <button type="submit" name="filtrar" id="filt" value="filtrar" class="btn btn-sm btn-primary"><i class="fa fa-filter"></i> Gerar</button>
                        <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                    </div>
                </div>
            </form>
            <?php if ($total > 0) { ?>
                <form action="" method="post" target="_blank" id="">
                    <table id="tbRelatorio" class='table table-hover table-striped table-condensed table-bordered valign-middle'>
                    <thead>
                        <tr class="bg-primary">
                            <!--<th><input type="checkbox" class="sel_todos" name=""></th>-->
                            <th class="text-center">ID</th>
                            <th>Nome</th>
                            <th class="text-center">Nº Nota</th>
                            <th class="text-center">Cadastro</th>
                            <th class="text-center">Emissão</th>
                            <th class="text-center">Vencto</th>
                            <th class="text-right">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysql_fetch_assoc($result)) { 
                            if($auxAgrupamento != $row['idAgrupamento']) { 
                                if($count > 0) {
                                    echo 
                                    "<tr>
                                        <td colspan='7' class='text-right warning'>Total Unidade: ".number_format($totalAgrupamento, 2, ',', '.')."</td>
                                    </tr>";
                                }
                                echo 
                                "<tr>
                                    <td colspan='7' class='info'>{$row['nomeAgrupamento']}</td>
                                </tr>";
                                $auxAgrupamento = $row['idAgrupamento'];
                                $totalAgrupamento = 0;
                            }
                            $count++;
                            $totalAgrupamento += $row['valor']; 
                            $totalValor += $row['valor']; 
                            ?>
                            <tr>
                                <!--<td class="text-center"><input type="checkbox" class="saidas_check" name="saidas[]" value="<?php echo $row['id_saida']; ?>"></td>-->
                                <td class="text-center"><?php echo $row['id_saida'] ?></td>
                                <td><?php echo $row['nome'] ?></td>
                                <td class="text-center"><?php echo $row['n_documento'] ?></td>
                                <td class="text-center"><?php echo implode('/', array_reverse(explode('-',$row['cadastro']))) ?></td>
                                <td class="text-center"><?php echo implode('/', array_reverse(explode('-',$row['emissao']))) ?></td>
                                <td class="text-center"><?php echo implode('/', array_reverse(explode('-',$row['vencto']))) ?></td>
                                <td class="text-right"><?php echo number_format($row['valor'], 2, ',', '.') ?></td>
                            </tr>
                        <?php } ?>
                        <?php if($auxAgrupamento) { ?>
                        <tr>
                            <td colspan='7' class='text-right warning'>Total Unidade: <?php echo number_format($totalAgrupamento, 2, ',', '.') ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td colspan='7' class='text-right text-bold'>Total: <?php echo number_format($totalValor, 2, ',', '.') ?></td>
                        </tr>
                    </tbody>
                </table>
                </form>
            <?php } else { ?>
                <div class="alert alert-danger top30">
                    Nenhum registro encontrado
                </div>
            <?php } ?>
            
            <?php include('../../template/footer.php'); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-datepicker.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <!--<script src="../../resources/js/financeiro/saida.js"></script>-->
        <script src="../../js/global.js"></script>        
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../../resources/js/print.js" type="text/javascript"></script>
        <script>
            $(function() {
                $("#form1").validationEngine({promptPosition : "topRight"});
                
                $('body').on('click', '#imprimir', function(){
                    $('#printForm').submit();
                });
                
                $('body').on('click', '.sel_todos', function(){
                    $('.saidas_check').prop('checked', $(this).prop('checked'));
                });
                
                //datepicker
                var options = new Array();
                options['language'] = 'pt-BR';
                $('.datepicker').datepicker(options);
            });
        </script>
    </body>
</html>