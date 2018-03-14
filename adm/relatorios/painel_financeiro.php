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
$nome_pagina = "PAINEL DO FINANCEIRO";
$breadcrumb_config = array("nivel"=>"$BASEURLINTRANET", "key_btn"=>"2", "area"=>"Administrativo", "id_form"=>"form1", "ativo"=>$nome_pagina);
$breadcrumb_pages = array('Relatórios de Gestão' => '/intranet/adm/relatorios/');

$painel->getPainelFinanceiro($usuario['id_regiao']);
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
                    <?php foreach ($painel->getArray() as $key => $array2) { ?>
                    <div class="col-xs-12  panel_projeto" id="p<?php echo $key ?>">
                        <div class='panel panel-default'>
                            <div class='panel-heading text-bold'><i class="fa fa-home"></i> <?php echo mysql_result(mysql_query("SELECT CONCAT(id_projeto, ' - ', nome) FROM projeto WHERE id_projeto = '{$key}' LIMIT 1"), 0) ?></div>
                            <div class='panel-body'>
                                <table class="table valign-middle text-sm">
                                    <tr>
                                        <td class="text-center"><button class="btn btn-xs btn-info text-sm ver_grafico" data-key="<?php echo $key ?>"><i class="fa fa-sort"> VISUALIZAR GRÁFICO</i></button></td>
                                        <?php foreach ($painel->getMeses() as $mes => $nome_mes) { ?>
                                            <td class="text-center"><?php echo $nome_mes; ?></td>
                                        <?php } ?>
                                        <td class="text-center">Média</td>
                                    </tr>
                                    <?php foreach ($painel->getIndices() as $indice => $indice_nome) { $tot = 0; ?>
                                        <tr>
                                            <td><?php echo $indice_nome ?></td>
                                            <?php foreach ($painel->getMeses() as $m => $v) { $cor = null;
                                                if($array2[$indice][$m] == min($array2[$indice])) { 
                                                    $cor = 'success'; 
                                                } else if(max($array2[$indice]) == $array2[$indice][$m]) { 
                                                    $cor = 'danger'; 
                                                } else { $cor = 'warning'; }
                                            ?>
                                            <td class="text-right <?php echo $cor ?>"><?php echo number_format($array2[$indice][$m], 2, ',', '.'); ?></td>
                                            <?php } ?>
                                            <td class="text-right"><?php echo number_format(array_sum($array2[$indice]) / count(/*array_keys($array2[$indice])*/ $painel->getMeses()), 2, ',', '.'); ?></td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>
                            <div class="panel-body divGrafico" id="g<?php echo $key ?>"></div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
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
            <?php foreach ($painel->getArray() as $id_projeto => $array) { ?> 
                $('#g<?php echo $id_projeto ?>').highcharts({ 
                    title: { text: '' }, 
                    xAxis: { 
                        categories: [ <?php foreach ($painel->getIndices() as $value) { echo "'$value', "; } ?> ] 
                    }, 
                    yAxis: { 
                        min: 0, 
                        allowDecimals: true, 
                        title: { text: 'R$' } 
                    }, 
                    tooltip: { 
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>', 
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' + '<td style="padding:0"><b>{point.y}</b></td></tr>', 
                        footerFormat: '</table>', 
                        shared: true, 
                        useHTML: true,
                        valueDecimals: 2,
                        valuePrefix: 'R$ '
                    }, 
                    series: [ 
                        <?php foreach ($painel->getMeses() as $key => $value) { ?> 
                            { 
                                type: 'column', 
                                name: '<?php echo $value ?>', 
                                data: [ <?php foreach ($painel->getIndices() as $key2 => $value2) { echo $array[$key2][$key] . ","; } ?> ] 
                            }, 
                        <?php } ?> 
                    ] 
                }); 
            <?php } ?>
            
            function esconder (){
                $('.panel_projeto').addClass('col-sm-6');
                $('.divGrafico').addClass('hide');
            }
            
            esconder();
            $('body').on('click', '.ver_grafico', function(){
                $this = $(this);
                esconder();
                $('#g'+$this.data('key')).removeClass('hide');
                $('#p'+$this.data('key')).removeClass('col-sm-6');
            });
        </script>
    </body>
</html>