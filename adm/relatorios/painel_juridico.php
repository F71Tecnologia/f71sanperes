<?php
 //$BASEURLINTRANETINTRANET = $_SERVER['DOCUMENT_ROOT']."intranet/";
$BASEURLINTRANET = "../../";

if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='{$BASEURLINTRANET}login.php'>Logar</a>";
    exit;
}

include($BASEURLINTRANET.'conn.php');
include($BASEURLINTRANET.'wfunction.php');
include($BASEURLINTRANET.'classes/BotoesClass.php');
include($BASEURLINTRANET.'classes/PainelAdmClass.php');

$usuario = carregaUsuario();
$painel = new PainelAdmClass();

/**
 * CONFIGURAÇÃO 
 */
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$botoes = new BotoesClass($dadosHeader['defaultPath'],$dadosHeader['fullRootPath']);
$nome_pagina = "PAINEL DO JURÍDICO";
$breadcrumb_config = array("nivel"=>"$BASEURLINTRANET", "key_btn"=>"2", "area"=>"Administrativo", "id_form"=>"form1", "ativo"=>$nome_pagina);
$breadcrumb_pages = array('Relatórios de Gestão' => '/intranet/adm/relatorios/');

$painel->getPainelJuridico($usuario['id_regiao'], $_REQUEST['id_projeto']);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: <?php echo $nome_pagina ?></title>
        <link href="<?php echo $BASEURLINTRANET ?>favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="<?php echo $BASEURLINTRANET ?>resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="<?php echo $BASEURLINTRANET ?>resources/css/main.css" rel="stylesheet" media="all">
        <link href="<?php echo $BASEURLINTRANET ?>resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="<?php echo $BASEURLINTRANET ?>resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="<?php echo $BASEURLINTRANET ?>resources/css/bootstrap-note.css" rel="stylesheet" media="all">
        <link href="<?php echo $BASEURLINTRANET ?>resources/css/add-ons.min.css" rel="stylesheet" media="all">
        <link href="<?php echo $BASEURLINTRANET ?>resources/css/dropzone.css" rel="stylesheet" media="all">
    </head>
    <style>
        .table tr td {
            padding-bottom: 4px!important;
            padding-top: 4px!important;
        }
    </style>
    <body>
    <?php include("$BASEURLINTRANET/template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header <?php echo $botoes->classModulos[2] ?>-header"><h2><?php echo $botoes->iconsModulos[2] ?> - ADMINISTRATIVO<small> - <?php echo $nome_pagina ?></small></h2></div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <form id="form_projeto" method="post" class="form-horizontal">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="col-xs-offset-1 col-xs-10">
                                    <div class="form-group">
                                        <?php echo montaSelect(getProjetos($usuario['id_regiao']), $_REQUEST['id_projeto'], 'class="form-control" name="id_projeto" id="id_projeto"') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <ul class="nav nav-tabs margin_b10">
                        <?php foreach ($painel->getMeses() as $key => $value) { ?>
                        <li class="text-bold <?php echo ($key == date('m')) ? 'active' : null ?>">
                            <a href=".<?php echo $key ?>" data-toggle="tab">
                                <?php echo "{$key} - {$value} ( " . (int) ($painel->getTotaisMes($key)) . " )";  ?>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div id="myTabContent" class="tab-content">
                <?php if(!count($painel->getArray())) { ?>
                <div class="alert alert-info">NENHUM PROCESSO ENCONTRADO PARA O PERÍODO!</div>
                <?php } ?>
                <?php foreach ($painel->getArray() as $mes => $ar1) { ?>
                    <?php if(!count($ar1)) { ?>
                    <div class="alert alert-info">NENHUM PROCESSO ENCONTRADO PARA O PERÍODO!</div>
                    <?php } else { ?>
                        <div class="tab-pane <?php echo ($mes == date('m')) ? 'active' : null ?> <?php echo $mes ?>">
                        <div class="row">
                            <div class="col-xs-12">
                                <?php foreach ($ar1 as $projeto => $ar2) { ?>
                                <div class="panel panel-info">
                                    <div class="panel-heading text-bold"><i class="fa fa-home"></i> <?php echo "$projeto ( {$painel->getTotaisMes($projeto)[$mes]} )" ?></div>
                                    <div class="panel-body">
                                        <?php foreach ($ar2 as $tipo => $ar3) { ?>
                                        <div class="panel panel-default">
                                            <div class="panel-heading text-bold"><i class="fa fa-balance-scale"></i> <?php echo "$tipo ( " . count($ar3) . " )" ?></div>
                                            <div class="panel-body">
                                                <table class="table table-hover text-sm">
                                                    <tr>
                                                        <th>DATA</th>
                                                        <th>N PROCESSO</th>
                                                        <th>NOME</th>
                                                        <th>ADVOGADO</th>
                                                        <th>PREPOSTO</th>
                                                        <th>STATUS</th>
                                                    </tr>
                                                    <?php foreach ($ar3 as $key => $value) { ?>
                                                        <tr class="<?php echo ($value['data'] == date('Y-m-d')) ? 'warning' : null ?>">
                                                            <td><?php echo implode('/',array_reverse(explode('-',$value['data']))) . ' ' . $value['hora'] ?></td>
                                                            <td><?php echo $value['nprocesso'] ?></td>
                                                            <td><?php echo $value['nome'] ?></td>
                                                            <td><?php echo $value['advogados'] ?></td>
                                                            <td><?php echo $value['preposto'] ?></td>
                                                            <td><?php echo $value['status'] ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                </table>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <?php include_once($BASEURLINTRANET.'template/footer.php') ?>
        </div><!-- /.content -->
        <script src="<?php echo $BASEURLINTRANET ?>js/jquery-1.10.2.min.js"></script>
        <script src="<?php echo $BASEURLINTRANET ?>resources/js/bootstrap.min.js"></script>
        <script src="<?php echo $BASEURLINTRANET ?>resources/js/bootstrap-dialog.min.js"></script>
        <script src="<?php echo $BASEURLINTRANET ?>resources/js/main.js"></script>
        <script src="<?php echo $BASEURLINTRANET ?>resources/js/highcharts/highcharts.js"></script>
        <script src="<?php echo $BASEURLINTRANET ?>js/global.js"></script>
        <script language="javascript">
            $(function(){
                $('body').on('change', '#id_projeto', function(){
                    if($(this).val() < 0){
                        $(this).val('');
                    }
                    $('#form_projeto').submit();
                });
            });
        </script>
    </body>
</html>