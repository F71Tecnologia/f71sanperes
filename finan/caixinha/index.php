<?php
header("Location: /intranet");
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../funcoes.php");
include("../../wfunction.php");
include("../../classes/CaixinhaClass.php");
include("../../classes/UnidadeClass.php");
include("../../classes/ProjetoClass.php");
include("../../classes_permissoes/acoes.class.php");

$acoes = new Acoes();
$objCaixinha = new CaixinhaClass();
$objProjeto = new ProjetoClass();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)


$arrayProjetos = $objProjeto->getProjetosMaster();
$optProjetos[''] = '--TODOS--';
foreach ($arrayProjetos as $key => $value) {
    $optProjetos[$value['id_projeto']] = $value['nome'];
}

$mes = ($_REQUEST['mes']) ? $_REQUEST['mes'] : date('m');
$ano = ($_REQUEST['ano']) ? $_REQUEST['ano'] : date('Y');
$data = ($_REQUEST['data']) ? $_REQUEST['data'] : date('d/m/Y');
$id_projeto = ($_REQUEST['id_projeto'] > 0) ? $_REQUEST['id_projeto'] : null;
$competencia = implode('-', array_reverse(explode('/', $data)));

$nome_pagina = "MOVIMENTAÇÃO DE CAIXINHA";
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"$nome_pagina");
//$breadcrumb_pages = array("Principal" => "../");
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
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <!--<link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">-->
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <!--<link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="all">-->
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro  - <small><?= $nome_pagina ?></small></h2></div>
                    <form action="" method="post" id="form1" class="form-horizontal top-margin1 hidden-print" enctype="multipart/form-data" autocomplete="off">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-sm-3 hidden-print">
                                    <div class="text-bold">Projeto:</div>
                                    <div class="" id="">
                                        <?php echo montaSelect($optProjetos, $id_projeto, 'class="form-control input-sm validate[required]" id="id_projeto" name="id_projeto"') ?>
                                    </div>
                                </div>
<!--                                <div class="col-sm-3 hidden-print">
                                    <div class="text-bold">Data de Lançamento:</div>
                                    <div class="input-group" id="">
                                        <?php echo montaSelect(mesesArray(), $mes, 'class="form-control input-sm" id="mes" name="mes"') ?>
                                        <div class="input-group-addon">/</div>
                                        <?php echo montaSelect(anosArray(), $ano, 'class="form-control input-sm" id="ano" name="ano"') ?>
                                    </div>
                                </div>-->
                                <div class="col-sm-3 hidden-print">
                                    <div class="text-bold">Data de Lançamento:</div>
                                    <input type="text" class="form-control input-sm data" id="data" name="data" value="<?php echo $data ?>">
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <a href="form_caixinha.php" class="btn btn-success hidden-print"><i class="fa fa-plus"></i> Novo Lançamento</a>
                            <button type="button" class="hidden-print btn btn-primary" title='Imprimir Tabela de Movimento' onClick='window.print()' target='_blank'><i class='fa fa-print'></i> Imprimir</button>
                            <button type="button" class="hidden-print btn btn-success" target='_blank' onClick="tableToExcel('table', '');"><i class='fa fa-file-excel-o'></i> Exportar</button>
                        </div>
                    </div>
                    </form>
                    <table id='table' class="table table-bordered  table-condensed table-hover valign-middle">
                        <tr>
                            <th class="text-center" colspan="5"><?php echo $data; echo ($id_projeto) ? ' - ' . $objProjeto->getNome($id_projeto) : ' - TODOS PROJETOS' ; ?></th>
                        </tr>
                        <tr>
                            <th class="text-center">Data</th>
                            <th class="text-center">Descrição</th>
                            <th class="text-center">Entrada</th>
                            <th class="text-center">Saída</th>
                            <th class="text-center">Saldo</th>
                        </tr>
                        <tr class="warning">
                            <th colspan="2">SALDO ANTERIOR</th>
                            <th colspan="3" class="text-right"><?php $objCaixinha->setDefault(); $objCaixinha->setIdProjeto($id_projeto); $saldoAnterior = $objCaixinha->getSaldoCaixinhasByMes($competencia, true); echo number_format($saldoAnterior,2,',','.') ?></th>
                        </tr>
                        <?php $objCaixinha->getAllCaixinhas($competencia, $id_projeto); ?>
                        <?php while($objCaixinha->getRow()) { ?>
                            <tr class="t<?= $objCaixinha->getIdProjeto() ?><?= $objCaixinha->getIdTipo() ?> ">
                                <td><?= $objCaixinha->getData('d/m/Y') ?></td>
                                <td>
                                    <?= ($objCaixinha->getIdItem()) ? "<i class='text-info'>({$objCaixinha->getItensDespesasNome()})</i> " : "" ?>
                                    <?= $objCaixinha->getDescricao() ?>

                                    <?= ($objCaixinha->getEspecifica() != "") ? " - ".$objCaixinha->getEspecifica() : "" ?>

                                </td>
                                <td class="text-right"><?php if($objCaixinha->getTipo() == 2) { echo number_format($objCaixinha->getSaldo() ,2,',','.'); $saldoAnterior += $objCaixinha->getSaldo(); } ?></td>
                                <td class="text-right"><?php if($objCaixinha->getTipo() == 1) { echo number_format($objCaixinha->getSaldo() ,2,',','.'); $saldoAnterior -= $objCaixinha->getSaldo(); } ?></td>
                                <td class="text-right">
                                    <?php echo number_format($saldoAnterior, 2, ',', '.') ?>
                                    <?php if(!$objCaixinha->getIdSaida()) { ?>
                                    <?php if($acoes->verifica_permissoes(130)) { ?><a href="form_caixinha.php?id_caixinha=<?= $objCaixinha->getIdCaixinha() ?>" class="btn btn-xs btn-warning hidden-print"><i class="fa fa-edit"></i></a><?php } ?>
                                    <?php if ($acoes->verifica_permissoes(129)) { ?><button type="button" class="del_caixinha btn btn-xs btn-danger hidden-print" data-key="<?= $objCaixinha->getIdCaixinha() ?>"><i class="fa fa-trash-o"></i></button><?php } ?>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr class="info">
                            <th colspan="2">SALDO</th>
                            <th colspan="3" class="text-right"><?php $objCaixinha->setDefault(); $objCaixinha->setIdProjeto($id_projeto); echo number_format($objCaixinha->getSaldoCaixinhasByMes($competencia),2,',','.') ?></th>
                        </tr>
                    </table>
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
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        
       
        <script>
        $(function(){
            $('body').on('change', '#mes, #ano, #data, #id_projeto', function(){
                $('#form1').submit();
            });
            
            $('body').on('click', '.del_caixinha', function(){
                var $this = $(this);
                bootConfirm('Confirma a exclusão do caixinha?','Confirmação',function(data){
                    if(data){
                        $.post("form_caixinha.php", {bugger:Math.random(), method:'inativar', id_caixinha:$this.data('key') }, function(result){
//                            $this.parent().parent().remove();
                            location.reload();
                        });
                    }
                },'warning');
            });
        })
        </script>
    </body>
</html>