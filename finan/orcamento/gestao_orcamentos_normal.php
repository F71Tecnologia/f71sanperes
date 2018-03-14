<?php
session_start();

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/SaidaClass.php");
include("../../classes/BancoClass.php");
include("../../classes/global.php");

$container_full = true;

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);

$saida = new Saida();
$global = new GlobalClass();
$objBanco = new Banco();

if(isset($_REQUEST['gerar'])){
    
    $bancoR = $_REQUEST['banco'];
    $inicioR = $_REQUEST['inicio'];
    $fimR = $_REQUEST['fim'];
    
    $rowBanco = getBancoID($bancoR);
    
    $gerar = true;
    list($dia, $mes, $ano) = explode('/', $_REQUEST['inicio']);
    $mesAno = mesesArray($mes).'/'.$ano;
    $banco = $_REQUEST['banco'];
    
    $auxBanco = ($_REQUEST['banco']) ? " AND id_banco IN ({$_REQUEST['banco']}) " : null;
    
    $sql = "SELECT C.id_grupo, C.nome_grupo, B.id_subgrupo, B.nome AS nome_subgrupo, A.cod, A.nome AS nome_tipo, A.id_entradasaida, SUM(REPLACE(IFNULL(D.valor, 0), ',', '.')) AS total
    FROM entradaesaida A
    LEFT JOIN entradaesaida_subgrupo B ON (A.cod LIKE CONCAT(B.id_subgrupo,'%'))
    LEFT JOIN entradaesaida_grupo AS C ON (C.id_grupo = B.entradaesaida_grupo)
    LEFT JOIN (
        SELECT tipo, SUM(IFNULL(valor, 0)) AS valor FROM (
            (SELECT tipo, SUM(valor) / 3 AS valor FROM entrada WHERE status = 2 AND data_vencimento BETWEEN DATE_FORMAT(ADDDATE(CURDATE(), INTERVAL -3 MONTH), '%Y-%m-01') AND LAST_DAY(CURDATE()) $auxBanco GROUP BY tipo)
            UNION 
            (SELECT tipo, SUM(REPLACE(valor, ',', '.')) / 3 AS valor FROM saida WHERE status = 2 AND data_vencimento BETWEEN DATE_FORMAT(ADDDATE(CURDATE(), INTERVAL -3 MONTH), '%Y-%m-01') AND LAST_DAY(CURDATE()) $auxBanco GROUP BY tipo)
        ) AS t
        GROUP BY tipo
    ) D ON (D.tipo = A.id_entradasaida)
    WHERE A.grupo > 5 
    GROUP BY A.id_entradasaida
    ORDER BY A.cod;";
    if($_COOKIE['debug'] == 666) { print_array($sql); }
    $qry = mysql_query($sql) or die(mysql_error());
    while($row = mysql_fetch_assoc($qry)) {
        $arrayGrupo[$row['id_grupo']]['total'] += $row['total'];
        $arrayGrupo[$row['id_grupo']]['nome'] = $row['nome_grupo'];
        
        $arraySubGrupo[$row['id_grupo']][$row['id_subgrupo']]['total'] += $row['total'];
        $arraySubGrupo[$row['id_grupo']][$row['id_subgrupo']]['nome'] = $row['nome_subgrupo'];
        
        $arrayTipo[$row['id_subgrupo']][$row['cod']]['total'] += $row['total'];
        $arrayTipo[$row['id_subgrupo']][$row['cod']]['nome'] = $row['nome_tipo'];
        
        $total += $row['total'];
        
    }
    
    
    
//    $whereData = "month(data_vencimento) = {$mes} AND year(data_vencimento) = {$ano}";
//    $completeWhere = $whereData." AND id_banco={$banco} AND status = 2 AND estorno IN (0,2)";
//    $result_det = $saida->getDetalhado($completeWhere);
//    
//    $qr = $result_det." GROUP BY C.id_entradasaida ORDER BY C.cod";
//    $result = mysql_query($qr);
//    $total_detalhado = mysql_num_rows($result);
//    
//    $qr_totais = $result_det." GROUP BY A.id_grupo";
//    $result_totais = mysql_query($qr_totais);
//    $totais = array();
//    while ($row_total = mysql_fetch_assoc($result_totais)) {
//        $totais[$row_total['id_grupo']] = $row_total['total'];
//    }
//    
//    $qr_subtotais = $result_det." GROUP BY B.id";
//    $result_subtotais = mysql_query($qr_subtotais);
//    $subtotais = array();
//    while ($row_subtotal = mysql_fetch_assoc($result_subtotais)) {
//        $subtotais[$row_subtotal['idsub']] = $row_subtotal['total'];
//    }
//    
//    $qt_totalfinal = "SELECT SUM(CAST(
//            REPLACE(total, ',', '.') AS DECIMAL(13,2))) AS total
//            FROM ({$result_det}) as q";
//    $result_totalfinal = mysql_query($qt_totalfinal);
//    $row_totalfinal = mysql_fetch_assoc($result_totalfinal);
//    
//    $qr_unidade = mysql_query("SELECT unidade AS nome FROM unidade WHERE id_unidade = '{$id_unidade}'");
//    $row_unidade = mysql_fetch_assoc($qr_unidade);
    
} else {
    $inicioR = '01/01/'.date('Y');
    $fimR = '31/12/'.date('Y');
}

// Orçamentos Salvos
$qr_orcamentos_salvos = mysql_query("SELECT gestao_orcamentos.id, gestao_orcamentos.inicio, gestao_orcamentos.fim, projeto.nome AS projeto_nome, unidade.unidade AS unidade_nome FROM gestao_orcamentos INNER JOIN projeto ON id_projeto = projeto_id LEFT JOIN unidade ON id_unidade = unidade_id WHERE projeto.id_regiao = '{$usuario[id_regiao]}' ORDER BY id DESC");
$total_orcamentos_salvos = mysql_num_rows($qr_orcamentos_salvos);

// Orçamento Existente
$qr_orcamento = mysql_query("SELECT * FROM gestao_orcamentos WHERE banco_id = '{$banco}' inicio <= '".data_db($inicioR)."' AND fim >= '".data_db($fimR)."'");
$row_orcamento = mysql_fetch_assoc($qr_orcamento);
$orcamento_existente = mysql_num_rows($qr_orcamento);

$nome_pagina = "Lançar Orçamento";
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"$nome_pagina");
$breadcrumb_pages = array("Gestão de Orçamentos"=>"index.php");

// Formato de Data Brasileiro
function data_brasileiro($data) {
    return implode('/', array_reverse(explode('-', $data)));
}

function data_db($data) {
    return implode('-', array_reverse(explode('/', $data)));
}
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
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="all">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <style>
            @media print {
                .show_print {
                    display: table-row!important;
                }
            }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="<?=($container_full) ? 'container-full' : 'container'?>">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - <?php echo $nome_pagina ?></small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
            
                <?php if(isset($_SESSION['regiao'])){ ?>                
                <!--resposta de algum metodo realizado-->
                <div class="alert alert-<?php echo $_SESSION['MESSAGE_TYPE']; ?> msg_cadsuporte">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <p><?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?></p>
                </div>
                <?php } ?>

                <div class="panel panel-default hidden-print">
                    <div class="panel-heading">Gestão de Orçamentos</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-lg-1 control-label">Banco</label>
                            <div class="col-lg-5">
                                <?php echo montaSelect($global->carregaBancosByMaster($usuario['id_master'], ['' => '« Selecione »'], null), $bancoR, "id='banco' required name='banco' class='required[custom[select]] form-control'"); ?>
                            </div>
                            <label for="select" class="col-lg-1 control-label">Periodo</label>
                            <div class="col-lg-5">
                                <div class="input-group">
                                    <input type="text" class="form-control data validate[required]" required id="inicio" name="inicio" value="<?php echo $inicioR; ?>">
                                    <span class="input-group-addon">até</span>
                                    <input type="text" class="form-control data validate[required]" required id="fim" name="fim" value="<?php echo $fimR; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button type="submit" class="btn btn-success" name="gerar" id ="gerar">
                            <i class="fa fa-plus"></i> Gerar
                        </button>
                    </div>
                </div>
            </form>
            <?php if ($gerar) { ?>
            	<?php if(!$orcamento_existente) { ?>
                    <?php if (count($arrayGrupo) > 0) { ?>
                        <div class="alert alert-dismissable alert-warning">      
                            <strong>Projeto: </strong> <?php echo "{$rowBanco['id_banco']} - {$rowBanco['nome']} (Ag: {$rowBanco['agencia']} CC: {$rowBanco['conta']})" ?>
                            <strong class="borda_titulo">Validade: </strong> <?php echo data_brasileiro($inicioR).' à '.data_brasileiro($fimR); ?>
                        </div>
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <button type="button" class="btn btn-outline btn-default limpar">Limpar</button>
                            <table class='table table-bordered table-hover table-condensed text-sm valign-middle'>
                                <thead>                    
                                    <tr>
                                        <th colspan="3" class="text-center fundo_titulo">Despesas realizadas</th>
                                    </tr>
                                    <tr class="bg-primary">
                                        <th width="10%">Código</th>
                                        <th width="50%">Despesa</th>
                                        <th width="40%">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php    
                                    $antesGrupo = "";
                                    $antesSubGrupo = "";
                                    $i = 0;
                                    foreach ($arrayGrupo as $idGrupo => $grupo) { ?>
                                        <?php $codigo_pai = sprintf('%02d', substr($idGrupo, 0, -1)); ?>
                                        <tr class='active'>
                                            <td><?php echo $codigo_pai ?></td>
                                            <td><?php echo $grupo['nome'] ?></td>
                                            <td><input class="form-control input-sm grupo" id="<?php echo $codigo_pai ?>" value="<?php echo number_format($grupo['total'], 2, ',', '.') ?>" type="text" name="valor" readonly="readonly" data-propriedade="<?php echo $grupo['nome']; ?>" data-codigo="<?php echo $codigo_pai; ?>" data-grupo="<?php echo $codigo_pai; ?>"></td>
                                        <tr>
                                        <?php foreach ($arraySubGrupo[$idGrupo] as $idSubGrupo => $subgrupo) { ?>
                                            <tr class='active'>
                                                <td><span class='artificio1'></span><?php echo $idSubGrupo ?></td>
                                                <td><?php echo $subgrupo['nome'] ?></td>
                                                <td><input class="form-control input-sm subgrupo" id="<?php echo str_replace('.', '' ,$idSubGrupo) ?>" value="<?php echo number_format($subgrupo['total'], 2, ',', '.') ?>" readonly="readonly" type="text" name="valor" data-propriedade="<?php echo $subgrupo['nome']; ?>" data-codigo="<?php echo $idSubGrupo ?>" data-grupo="<?php echo $codigo_pai; ?>" data-subgrupo="<?php echo $idSubGrupo; ?>"></td>
                                            <tr>
                                            <?php foreach ($arrayTipo[$idSubGrupo] as $idTipo => $tipo) { ?>
                                                <tr>
                                                    <td><span class='artificio2'></span><?php echo $idTipo ?></td>
                                                    <td><?php echo $tipo['nome']; ?></td>
                                                    <td><input class="form-control input-sm tipo" value="<?php echo number_format($tipo['total'], 2, ',', '.') ?>" type="text" name="valor" data-propriedade="<?php echo $tipo['nome']; ?>" data-codigo="<?php echo $idTipo; ?>" data-subgrupo="<?php echo str_replace('.', '' ,$idSubGrupo); ?>" data-grupo="<?php echo $codigo_pai; ?>"></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr class="info">
                                        <td></td>
                                        <td></td>
                                        <td><strong>Total: <span id="total-parcial"><?php echo number_format($total, 2, ',', '.') ?></span></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                            <div id="status-envio" class="alert alert-info pull-left" style="display:none;"></div>
                            <input type="submit" name="salvar" value="Salvar" id="salvar" class="btn btn-success pull-right" />
                        </form>
                    <?php } else { ?>
                        <div class="alert alert-danger top30">
                            Nenhum registro encontrado
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="alert alert-danger top30">                    
                        Orçamento existente!
                    </div>
                <?php } ?>
            <?php } ?>
            <?php include('../../template/footer.php'); ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/highcharts/highcharts.js"></script>
        <script src="../../resources/js/highcharts/highcharts.drilldown.js"></script>
        <script src="../../resources/js/highcharts/highcharts.exporting.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/financeiro/detalhado.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskMoney.js"></script>
        <script>
            $(function() {
            	// Validação
            	$('#form1').validationEngine({promptPosition : 'topRight'});
            	
            	// Select de Unidades a partir de Projeto selecionado
            	$('#projeto').change(function(event){
                    event.preventDefault();
                    $.ajax({
                        method: 'POST',
                        url: 'gestao_orcamentos_unidades.php',
                        data: {
                            projeto_id: $(this).val()
                        }
                    }).done(function(resposta) {
                        $('#unidade').html(resposta);
                    });
                });
                
                // Datepicker
                $('.data').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '2005:c+1'
                });
                
                // Exibir / Ocultar Orçamentos Salvos
                $('#btn-orcamentos-salvos').click(function(event){
                    event.preventDefault();
                    $('#orcamentos-salvos').toggle();
                });
                
                // Valor para Real
            	$('input[name=valor]').maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
            	
            	// Habilitar / Desabilitar Campos
//            	$('*[data-grupo]').blur(function(){
//                    console.log($(this).val(), $(this).data());
//                    if($(this).val()) {
//                        $('*[data-grupo="'+$(this).data('grupo')+'"]').prop('disabled', false);
//                    }
//            	});
//            	$('*[data-subgrupo]').blur(function(){
//                    if($(this).val()) {
//                        $('*[data-subgrupo="'+$(this).data('subgrupo')+'"]').prop('disabled', false);
//                    }
//            	});
            	
            	// Orçamento
            	$('input[name=valor]').keyup(function(){
            		
                    $('.msg').fadeOut('slow', function(){
                        $(this).remove();
                    });

//                    if($(this).data('grupo')) {
//                        var valorDisponivelGrupo = $('*[data-grupo="'+$(this).data('grupo')+'"]').first().val().replace('.','').replace(',','.');
//
//                        $('*[data-grupo="'+$(this).data('grupo')+'"]:not(:first)').each(function(){
//                            if($(this).val()) {
//                                valorDisponivelGrupo -= +$(this).val().replace('.','').replace(',','.');
//                            }
//                        });
//
//                        if(valorDisponivelGrupo < 0) {
//                            $(this).val('');
//                            $(this).parents('td').append('<span class="msg alert alert-warning">Valor ultrapassou o orçamento!</span>');
//                        }
//                    }

//                    if($(this).data('subgrupo')) {
//                        var valorDisponivelSubgrupo = $('*[data-subgrupo="'+$(this).data('subgrupo')+'"]').first().val().replace('.','').replace(',','.');
//
//                        $('*[data-subgrupo="'+$(this).data('subgrupo')+'"]:not(:first)').each(function(){
//                            if($(this).val()) {
//                                valorDisponivelSubgrupo -= +$(this).val().replace('.','').replace(',','.');
//                            }
//                        });
//
//                        if(valorDisponivelSubgrupo < 0) {
//                            $(this).val('');
//                            $(this).parents('td').append('<span class="msg alert alert-warning">Valor ultrapassou o orçamento!</span>');
//                        }
//                    }
            	});
            	
            	// Total Parcial
                $('body').on('blur', '.tipo', function(){
                    var total = [];
                    var valor = 0;
                    var valorG = 0;
                    var valorSG = 0;
                    var $this = null;
                    $('.grupo, .subgrupo').val('');
                    $('.tipo').each(function(){
                        $this = $(this);
                        valor = parseFloat($this.val().replace(/\./g, '').replace(/\,/g, '.'));
                        if(valor > 0) {
                            valorG = parseFloat(($('#' + $this.data('grupo')).val()) ? $('#' + $this.data('grupo')).val().replace(/\./g, '').replace(/\,/g, '.') : 0);
//                            console.log(valorG);
                            $('#' + $this.data('grupo')).val(number_format(valorG + valor, 2, ',', '.'));
                            valorSG = parseFloat(($('#' + $this.data('subgrupo')).val()) ? $('#' + $this.data('subgrupo')).val().replace(/\./g, '').replace(/\,/g, '.') : 0);
//                            console.log($this.data('subgrupo'), $('#' + $this.data('subgrupo')).val(), valorSG, valorSG + valor);
                            $('#' + $this.data('subgrupo')).val(number_format(valorSG + valor, 2, ',', '.'));
                            total += valor;
                            
//                            total[$(this).data('grupo')] =  parseFloat((total[$(this).data('grupo')]) ? total[$(this).data('grupo')] : 0) + parseFloat(valor);
//                            total[$(this).data('subgrupo')] =  parseFloat((total[$(this).data('subgrupo')]) ? total[$(this).data('subgrupo')] : 0) + parseFloat(valor);
                        } else {
                            if(valorG == 0) {
                                $('#' + $this.data('grupo')).val(number_format(0, 2, ',', '.'));
                            }
                            if(valorSG == 0) {
                                $('#' + $this.data('subgrupo')).val(number_format(0, 2, ',', '.'));
                            }
                        }
                    });
//                    $('#total-parcial').html(total.toFixed(2));
                });
 
 				// AJAX para salvar orçamento
            	$('#salvar').click(function(event){
                    event.preventDefault();
                    var cabecalho = {
                        'banco': $('#banco').val(),
                        'inicio': $('#inicio').val(),
                        'fim': $('#fim').val()
                    };
                    var valores = new Array();
                    $('input[name=valor]').each(function(){
                        valores.push({
                            'codigo': $(this).data('codigo'),
                            'propriedade': $(this).data('propriedade'),
                            'valor': $(this).val()
                        });
                    });
                    $('#status-envio').fadeIn('fast', function(){
                        $(this).html('Salvando...');
                    });
                    $.ajax({
                        method: 'POST',
                        url: 'gestao_orcamentos_salvar.php',
                        data: {
                                cabecalho: cabecalho,
                                valores: valores
                        },
                        dataType: 'json'
                    }).done(function(response) {
//                        console.log(response);
                        window.location.replace('index.php');
                    });
                });
                
                $('.tipo').first().trigger('blur');
                
                $('body').on('click', '.limpar', function(){
                    $('input[name=valor]').val('');
                });
            });
        </script>
    </body>
</html>
