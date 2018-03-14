<?php
session_start();

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/SaidaClass.php");
include("../../classes/global.php");

$container_full = true;

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);

$saida = new Saida();
$global = new GlobalClass();

if(isset($_REQUEST['gerar'])){
    $id_projeto = $_REQUEST['projeto'];
    $id_unidade = $_REQUEST['unidade'];
    $nome_projeto = projetosId($_REQUEST['projeto']);
    $master_projeto = masterId($nome_projeto['id_master']); 
    $gerar = true;
    
	list($dia, $mes, $ano) = explode('/', $_REQUEST['inicio']);
	$mesAno = mesesArray($mes).'/'.$ano;
    $banco = $_REQUEST['banco'];
    
    $whereData = "month(data_vencimento) = {$mes} AND year(data_vencimento) = {$ano}";
    $completeWhere = $whereData." AND id_banco={$banco} AND status = 2 AND estorno IN (0,2)";
    $result_det = $saida->getDetalhado($completeWhere);
    
    $qr = $result_det." GROUP BY C.id_entradasaida ORDER BY C.cod";
    $result = mysql_query($qr);
    $total_detalhado = mysql_num_rows($result);
    
    $qr_totais = $result_det." GROUP BY A.id_grupo";
    $result_totais = mysql_query($qr_totais);
    $totais = array();
    while ($row_total = mysql_fetch_assoc($result_totais)) {
        $totais[$row_total['id_grupo']] = $row_total['total'];
    }
    
    $qr_subtotais = $result_det." GROUP BY B.id";
    $result_subtotais = mysql_query($qr_subtotais);
    $subtotais = array();
    while ($row_subtotal = mysql_fetch_assoc($result_subtotais)) {
        $subtotais[$row_subtotal['idsub']] = $row_subtotal['total'];
    }
    
    $qt_totalfinal = "SELECT SUM(CAST(
            REPLACE(total, ',', '.') AS DECIMAL(13,2))) AS total
            FROM ({$result_det}) as q";
    $result_totalfinal = mysql_query($qt_totalfinal);
    $row_totalfinal = mysql_fetch_assoc($result_totalfinal);
    
    $qr_unidade = mysql_query("SELECT unidade AS nome FROM unidade WHERE id_unidade = '{$id_unidade}'");
    $row_unidade = mysql_fetch_assoc($qr_unidade);
    
}

// VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO
if(isset($_REQUEST['projeto'])){
    $projetoR = $_REQUEST['projeto'];
    $unidadeR = $_REQUEST['unidade'];
    $bancoR = $_REQUEST['banco'];
    $inicioR = $_REQUEST['inicio'];
    $fimR = $_REQUEST['fim'];
} else {
	$inicioR = date('d/m/Y');
    $fimR = date('d/m/Y', strtotime('+1 year'));
}

// Orçamentos Salvos
$qr_orcamentos_salvos = mysql_query("SELECT gestao_orcamentos.id, gestao_orcamentos.inicio, gestao_orcamentos.fim, projeto.nome AS projeto_nome, unidade.unidade AS unidade_nome FROM gestao_orcamentos INNER JOIN projeto ON id_projeto = projeto_id LEFT JOIN unidade ON id_unidade = unidade_id WHERE projeto.id_regiao = '{$usuario[id_regiao]}' ORDER BY id DESC");
$total_orcamentos_salvos = mysql_num_rows($qr_orcamentos_salvos);

// Orçamento Existente
$qr_orcamento = mysql_query("SELECT * FROM gestao_orcamentos WHERE projeto_id = '{$projetoR}' AND unidade_id = '{$unidadeR}' AND inicio <= '".data_db($inicioR)."' AND fim >= '".data_db($fimR)."'");
$row_orcamento = mysql_fetch_assoc($qr_orcamento);
$orcamento_existente = mysql_num_rows($qr_orcamento);

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Criação de Orçamento");
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

        <title>:: Intranet :: Criação de Orçamento</title>

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
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Criação de Orçamento</small></h2></div>
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
                            <label for="select" class="col-lg-1 control-label">Projeto</label>
                            <div class="col-lg-5">
                                <?php echo montaSelect($global->carregaProjetosByRegiao($usuario['id_regiao']), $projetoR, "id='projeto' name='projeto' required='required' class='form-control'"); ?>
                            </div>
                            <label for="select" class="col-lg-1 control-label">Unidade</label>
                            <div class="col-lg-5">
                            	<select name="unidade" id="unidade" class="required[custom[select]] form-control">
                            		<option value="-1">Selecione</option>
                            		<?php $qr_unidades = mysql_query("SELECT * FROM unidade WHERE id_regiao = 2 AND campo1 = '{$projetoR}'");
                            		while($row_unidades = mysql_fetch_assoc($qr_unidades)) { ?>
                            			<option <?php if($row_unidades['id_unidade'] == $unidadeR) { echo 'selected="selected"'; } ?> value="<?php echo $row_unidades['id_unidade']; ?>">
                            				<?php echo $row_unidades['unidade']; ?>
                            			</option>
                            		<?php } ?>
                            	</select>
                            </div>
                        </div>
                        <div class="form-group">
                        	<label for="select" class="col-lg-1 control-label">Banco</label>
                            <div class="col-lg-5">
                                <?php echo montaSelect($global->carregaBancosByRegiao($usuario['id_regiao'], array(0 => "Selecione"), null), $bancoR, "id='banco' name='banco' class='required[custom[select]] form-control'"); ?>
                            </div>
                            <label for="select" class="col-lg-1 control-label">Início</label>
                            <div class="col-lg-2">
								<div class="input-group">
                                    <input type="text" class="form-control data validate[required]" id="inicio" name="inicio" value="<?php echo $inicioR; ?>">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                            <label for="select" class="col-lg-1 control-label">Fim</label>
                            <div class="col-lg-2">
                                <div class="input-group">
                                    <input type="text" class="form-control data validate[required]" id="fim" name="fim" value="<?php echo $fimR; ?>">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
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
			<?php
            if ($gerar) {
            	if(!$orcamento_existente) {
                	if ($total_detalhado > 0) {
            ?>
            <div class="alert alert-dismissable alert-warning">      
            	<strong>Projeto: </strong> <?php echo $nome_projeto['nome']; ?>          
                <strong class="borda_titulo">Unidade Gerenciada: </strong> <?php echo $row_unidade['nome']; ?>
                <strong class="borda_titulo">Validade: </strong> <?php echo data_brasileiro($inicioR).' à '.data_brasileiro($fimR); ?>
            </div>'0
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
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
                    while ($row = mysql_fetch_assoc($result)) {
                        $grafico[$row['id_grupo']]['nome'] = $row['nome_grupo'];
                        $grafico[$row['id_grupo']]['total'] = number_format($totais[$row['id_grupo']], 2, '.', '');
                        $grafico[$row['id_grupo']]['dados'][$row['id_subgrupo']]['nome'] = $row['subgrupo'];
                        $grafico[$row['id_grupo']]['dados'][$row['id_subgrupo']]['total'] = number_format($subtotais[$row['idsub']], 2, '.', '');
                        $grafico[$row['id_grupo']]['dados'][$row['id_subgrupo']]['dados'][$row['cod']]['nome'] = $row['nome'];
                        $grafico[$row['id_grupo']]['dados'][$row['id_subgrupo']]['dados'][$row['cod']]['total'] = number_format($row['total'], 2, '.', '');
                        
                        if ($antesGrupo != $row['id_grupo']) {
                            $antesGrupo = $row['id_grupo'];
                    ?>
                    <?php $codigo_pai = sprintf('%02d', substr($row['id_grupo'], 0, -1)); ?>
                    <tr class='active'>
                        <td><?php echo $codigo_pai; ?></td>
                        <td><?php echo $row['nome_grupo']; ?></td>
                        <td><input type="text" name="valor" data-propriedade="<?php echo $row['nome_grupo']; ?>" data-codigo="<?php echo $codigo_pai; ?>" data-grupo="<?php echo $antesGrupo; ?>"></td>
                    <tr>
                    
                    <?php
                        }
                        if ($antesSubGrupo != $row['id_subgrupo']) {
                            $antesSubGrupo = $row['id_subgrupo'];
                    ?>
                    
                    <tr class='active'>
                        <td><span class='artificio1'></span><?php echo $row['id_subgrupo']; ?></td>
                        <td><?php echo $row['subgrupo']; ?></td>
                        <td class='txright'><input disabled type="text" name="valor" data-propriedade="<?php echo $row['subgrupo']; ?>" data-codigo="<?php echo $row['id_subgrupo']; ?>" data-grupo="<?php echo $antesGrupo; ?>" data-subgrupo="<?php echo $antesSubGrupo; ?>"></td>
                    <tr>
                    
                    <?php } ?>
                    
                    <tr>
                        
                        <?php if($row['total'] == ""){ ?>
                        <td><span class='artificio2'></span><?php echo $row['cod']; ?></td>
                        <?php }else{ ?>
                        <td>
                            <span class='artificio2'></span>
                            <a href="javascript:;" class="clk" data-key="<?php echo str_replace(".", "", $row['cod']); ?>"><?php echo $row['cod']; ?></a>
                        </td>
                        <?php } ?>
                        
                        <td><?php echo $row['nome']; ?></td>
                        <td><input disabled type="text" name="valor" data-propriedade="<?php echo $row['nome']; ?>" data-codigo="<?php echo $row['cod']; ?>" data-subgrupo="<?php echo $antesSubGrupo; ?>"></td>
                    </tr>
                    
                    <?php
                    if($row['total'] != ""){
                        $res = $saida->getDespesas($row['cod'], $completeWhere);
                        $tot = mysql_num_rows($res);
                    ?>
                    <tr data-grupo="<?php echo $antesGrupo; ?>" id="tbl<?php echo $i++; ?>" class="occ <?php echo str_replace(".", "", $row['cod']); ?> show_print">
                        <td colspan="3">
                            <table class='table table-bordered'>
                                <tbody>
                                    <?php
                                    while ($rowd = mysql_fetch_assoc($res)) {
                                        
                                        $comprovante = "-";
                                        if($rowd['comprovante'] == 2){
                                            $comprovante = "<a class='btn btn-xs btn-info btn-outline arq' data-key='".str_replace(".", "", $rowd['id_saida'])."'><span class='fa fa-paperclip'></span></a>";
                                        }
                                        
                                        $especifica = ($rowd['especifica'] == "") ? "-" : $rowd['especifica'];
                                        
                                        if($rowd['estorno'] == 2){
                                            $valor = "R$".number_format($rowd['cvalor'],2,",",".")." - ".number_format($rowd['valor_estorno_parcial'],2,",",".");
                                        }else{
                                            $valor = "R$".number_format($rowd['cvalor'],2,",",".");
                                        }
                                    ?>
                                    <tr class="active">
                                        <td><?php echo $rowd['id_saida']; ?></td>
                                        <td><?php echo $rowd['nome']; ?></td>
                                        <td><?php echo $especifica; ?></td>
                                        <td><?php echo $valor; ?></td>
                                        <td><?php echo $rowd['dataBr']; ?></td>
                                        <td class="text-center hidden-print"><?php echo $comprovante; ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <?php }} ?>
                </tbody>
                <tfoot>
                    <tr class="info">
                        <td></td>
                        <td></td>
                        <td><strong>Total: <span id="total-parcial"></span></strong></td>
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
            <?php }
            } else { ?>
            	<div class="alert alert-danger top30">                    
                    Orçamento existente!
                </div>
            <?php
            	}
            } ?>
            
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
            	$('*[data-grupo]').blur(function(){
            		if($(this).val()) {
            			$('*[data-grupo="'+$(this).data('grupo')+'"]').prop('disabled', false);
            		}
            	});
            	$('*[data-subgrupo]').blur(function(){
            		if($(this).val()) {
            			$('*[data-subgrupo="'+$(this).data('subgrupo')+'"]').prop('disabled', false);
            		}
            	});
            	
            	// Orçamento
            	$('input[name=valor]').keyup(function(){
            		
            		$('.msg').fadeOut('slow', function(){
            			$(this).remove();
            		});
            		
            		if($(this).data('grupo')) {
            		
		        		var valorDisponivelGrupo = $('*[data-grupo="'+$(this).data('grupo')+'"]').first().val().replace('.','').replace(',','.');
		        		
		        		$('*[data-grupo="'+$(this).data('grupo')+'"]:not(:first)').each(function(){
		        			if($(this).val()) {
				    			valorDisponivelGrupo -= +$(this).val().replace('.','').replace(',','.');
				    		}
		        		});
		        		
		        		if(valorDisponivelGrupo < 0) {
		        			$(this).val('');
		        			$(this).parents('td').append('<span class="msg alert alert-warning">Valor ultrapassou o orçamento!</span>');
		        		}
		        		
		        	}
		        	
		        	if($(this).data('subgrupo')) {

		        		var valorDisponivelSubgrupo = $('*[data-subgrupo="'+$(this).data('subgrupo')+'"]').first().val().replace('.','').replace(',','.');
		        		
		        		$('*[data-subgrupo="'+$(this).data('subgrupo')+'"]:not(:first)').each(function(){
		        			if($(this).val()) {
				    			valorDisponivelSubgrupo -= +$(this).val().replace('.','').replace(',','.');
				    		}
		        		});
		        		
		        		if(valorDisponivelSubgrupo < 0) {
		        			$(this).val('');
		        			$(this).parents('td').append('<span class="msg alert alert-warning">Valor ultrapassou o orçamento!</span>');
		        		}
		        		
		        	}
            		
            	});
            	
            	// Total Parcial
 				$('input[name=valor]').blur(function(){
 					var total_parcial = 0;
 					$('input[name=valor]').each(function(){
 						if($(this).val()) {
	 						total_parcial += +$(this).val().replace(',', '.');
	 					}
 					});
 					$('#total-parcial').html(total_parcial.toFixed(2));
 				});
 
 				// AJAX para salvar orçamento
            	$('#salvar').click(function(event){
                	event.preventDefault();
                	var cabecalho = {
						'projeto': $('#projeto').val(),
						'unidade': <?php echo $unidadeR ?: 0; ?>,
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
						window.location.replace('index.php');
					});
                });
            	
            	// Gráfico
                $('#highcharts').highcharts({
                    lang: { drillUpText: '<< Voltar para {series.name}' },
                    chart: { type: 'column' },
                    title: { text: 'Despesas realizadas' },
                    //subtitle: { text: 'Click the columns to view versions. Source: <a href="http://netmarketshare.com">netmarketshare.com</a>.' },
                    xAxis: { type: 'category' },
                    yAxis: { title: { text: 'R$' } },
                    legend: { enabled: false },
                    plotOptions: {
                        series: {
                            borderWidth: 0,
                            dataLabels: {
                                enabled: true,
                                format: '{point.yText}'
                            }
                        }
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>R$ {point.yText}</b><br/>'
                    },
                    series: [{
                        name: "Todos",
                        colorByPoint: true,
                        data: [
                            <?php foreach ($grafico as $idGrupo => $grupoValue) { ?>
                                {
                                    name: "<?=$grupoValue['nome']?>",
                                    y: <?=$grupoValue['total']?>,
                                    yText: "<?=number_format($grupoValue['total'], 2,',','.')?>",
                                    drilldown: "<?=$idGrupo?>"
                                },
                            <?php } ?>
                        ]
                    }],
                    drilldown: {
                        drillUpButton: { relativeTo: 'spacingBox', position: { y: -4, x: -50 } },
                        series: [
                            <?php foreach ($grafico as $idGrupo => $grupoValue) { ?>
                                {
                                    name: "<?=$grupoValue['nome']?>",
                                    id: "<?=$idGrupo?>",
                                    data: [
                                        <?php foreach ($grupoValue['dados'] as $idSubGrupo => $subGrupoValue) { ?>
                                            {
                                                name: "<?=$subGrupoValue['nome']?>", 
                                                y: <?=$subGrupoValue['total']?>, 
                                                yText: "<?=number_format($subGrupoValue['total'], 2,',','.')?>",
                                                drilldown: "<?=$idSubGrupo?>"
                                            },
                                        <?php } ?>
                                    ]
                                },
                            <?php } ?>
                            <?php foreach ($grafico as $idGrupo => $grupoValue) { ?>
                                <?php foreach ($grupoValue['dados'] as $idSubGrupo => $subGrupoValue) { ?>
                                    {
                                        name: "<?=$subGrupoValue['nome']?>",
                                        id: "<?=$idSubGrupo?>",
                                        data: [
                                            <?php foreach ($subGrupoValue['dados'] as $tipoCod => $tipoValue) { ?>
                                                { 
                                                    name: "<?=$tipoValue['nome']?>", 
                                                    y: <?=$tipoValue['total']?>,
                                                    yText: "<?=number_format($tipoValue['total'], 2,',','.')?>",
                                                },
                                            <?php } ?>
                                        ]
                                    },
                                <?php } ?>
                            <?php } ?>
                        ]
                    }
                });
            });
        </script>
    </body>
</html>
