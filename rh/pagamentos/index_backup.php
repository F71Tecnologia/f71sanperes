<?php
if(empty($_COOKIE['logado'])) {
   print "<script>location.href = '../../login.php?entre=true';</script>";
   exit;
} 
include('../../conn.php');
$regiao = $_GET['regiao'];
?>
<html>
<head>
<title>Pagamentos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<script src="../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../../jquery/jquery.tools.min.js"></script>
<script src="../../js/highslide-with-html.js" type="text/javascript"></script>
<script type="text/javascript"> 
function abrir(URL,w,h,NOMEZINHO) {
	var width = w;
  	var height = h;
	var left = 99;
	var top = 99;
window.open(URL,NOMEZINHO, 'width='+width+', height='+height+', top='+top+', left='+left+', scrollbars=yes, status=no, toolbar=no, location=no, directories=no, menubar=no, resizable=yes, fullscreen=no');
}
    hs.graphicsDir = '../../images-box/graphics/';
    hs.outlineType = 'rounded-white';
	onload=function(){
document.body.style.visibility="visible"
}
</script> 
<script language="javascript">
$().ready(function(){
	$('.ano').find('table').hide();
	$('.ano').click(function(){
		$('.ano').find('table').hide();
		$('.titulo').css('background-color','#F1F1F1');
		$(this).find('.titulo').css('background-color','#bbb');
		$(this).find('table').toggle();
	});
	
	$('.folha_mes').click(function(){
		$('.folha_mes').not(this).next('div').hide();
		$(this).next('div').toggle();
	});
	$('[title]').tooltip({  tipClass: 'bloco'});
	
	$('.ano2').parent().next().hide();
	$(".ano2").click(function(){
		$(this).parent().next().slideToggle();
		$('.ano2').parent().next().hide();
	});
	$('.dataautonomos').parent().next().hide();
	$('.dataautonomos').click(function(){
		$(this).parent().next().slideToggle();
		$('.dataautonomos').parent().next().hide();
	});
	$('a.recisao').click(function(){
		$(this).next().toggle();
	});
});
</script>
<link rel="stylesheet" type="text/css" href="../../js/highslide.css" />
<style type="text/css">
/* Criado por maikom james usado para o Title */
.bloco {
	display:none; background-color:#fff; border:1px solid #777; padding:5px; font-size:13px; -moz-box-shadow:2px 2px 11px #666; -webkit-box-shadow:2px 2px 11px #666; text-align:left; line-height:30px;
}
.bloco a {
	color:#222; text-decoration:none;
}
/* Criado por maikom james usado para o Title */
.ano {
	text-align:center;
}
/*
.ano table {
	display:none;
}
*/
.folha_mes {
	cursor:pointer; width:100%;
}
.titulo {
	background-color:#F1F1F1; cursor:pointer; font-size:13px; padding:4px 0px 4px 0px; width:100%; text-align:center; font-weight:bold; margin-top:10px; clear:both;
}
.tooltip {
	display:none; background-color:#fff; border:1px solid #777; padding:5px; font-size:13px; -moz-box-shadow:2px 2px 11px #666; -webkit-box-shadow:2px 2px 11px #666; text-align:left; line-height:30px;
}
.dados {
	font-size:13px;
}
.cabecalho {
	font-weight: bold; font-size:13px;
}
.provisoes{
	font-size:12px !important;
}
.linha_um {
 background-color:#f5f5f5;
}
.linha_dois {
 background-color:#ebebeb;
}
.linha_um td, .linha_dois td {
 	border-bottom:1px solid #ccc;
}
</style>
</head>
<body>
<table align="center" cellpadding="0" cellspacing="0" class="corpo" id="topo">
  <tr>
	<td align="center">
    <img src="imagens/logo_pagamentos.jpg" width="357" height="150"></td>
  </tr>
  <tr>
    <td align="center">
	<img src="imagens/status.jpg" >
 <?php 
 $meses = array('Janeiro' => '01',
 					  'Fevereiro' => '02',
					  'Março' => '03',
					  'Abril' => '04',
					  'Maio' => '05',
					  'Junho' => '06',
					  'Julho' => '07',
					  'Agosto' => '08',
					  'Setembro' => '09',
					  'Outubro' => '10',
					  'Novembro' => '11',
					  'Dezembro' => '12');	   
	   // Loop dos Anos
	   for($ano=2009; $ano<=date('Y'); $ano++) { ?>
       <div class="ano">
            <div class="titulo">FOLHAS DE PAGAMENTO <?=$ano?> <span class="destaque">CLT</span></div>
                  <table cellpadding="4" cellspacing="0" class="relacao">
                      <tr class="secao">
                        <td colspan="3">Mês Referente</td>
                      </tr>
            			 <?php // Loop dos Meses
                  			foreach($meses as $nome_mes => $mes){
                      			 $qr_folha = mysql_query("SELECT f.id_folha, f.projeto, p.id_regiao, p.regiao, p.nome
                                                  FROM rh_folha f INNER JOIN projeto p ON f.projeto = p.id_projeto
                                                 WHERE p.id_master = '$_GET[id]' 
												   AND f.status = '3' 
                                                   AND f.mes = '$mes' 
                                                   AND f.ano = '$ano' 
												   ");
                      		 	$total_folha = mysql_num_rows($qr_folha);
                      			 if(!empty($total_folha)) { ?>
                               
                <tr class="linha_<?php if($cor++%2==0) { ?>um<? } else { ?>dois<? } ?>">
                   <td colspan="2">
                      <div >
                            	<span class="folha_mes"><?=$nome_mes?>&nbsp;<img src="../folha/sintetica/seta_dois.gif"/></span>
                                <div style="display: none" >
									<table width="100%" bgcolor="#FFFFFF"  cellspacing="1" cellpadding="5">
                                    	<tr bgcolor="#CCCCCC">
                                        	<td><span class="cabecalho">ID folha</span></td>
                                        	<td ><span class="cabecalho">Regiao</span></td>
                                        	<td><span class="cabecalho">Projeto</span></td>
                                            <td><span class="cabecalho">GPS</span></td>
                                            <td><span class="cabecalho">FGTS</span></td>
                                            <td><span class="cabecalho">PIS</span></td>
                                            <td><span class="cabecalho">IR</span></td>
                                            <td><span class="cabecalho">RECISÃO</span></td>
                                            <td><span class="cabecalho">FÉRIAS</span></td>
                                        </tr>
								<?php 
									while($row_folha = mysql_fetch_assoc($qr_folha)){
								?>
                                
                                        <tr class="linha_<?php if($cor2++%2==0) { ?>um<? } else { ?>dois<? } ?>">
                                        	<td><span class="dados"><?=$row_folha['id_folha']?></span></td>
                                        	<td><span class="dados">
											<?=
												$row_folha['id_regiao'] . " - " . $row_folha['regiao'];
											?></span>
                                            </td>
                                        	<td><span class="dados"><?=$row_folha['nome']?></span></td>
                                            <?php 
												$query_controle = mysql_query("
												SELECT saida.status,pagamentos.tipo_pg,saida.valor,saida.id_saida,date_format(saida.data_proc, '%d/%m/%Y') AS DATA, 
												saida.id_user, date_format(saida.data_pg, '%d/%m/%Y') AS DATAPG, saida.id_userpg FROM saida INNER JOIN pagamentos
												ON saida.id_saida = pagamentos.id_saida
												WHERE pagamentos.mes_pg = '$mes'
												AND pagamentos.ano_pg = '$ano'
												AND pagamentos.id_folha = '$row_folha[id_folha]'
												AND saida.id_projeto = '$row_folha[projeto]'
												");
												$num_controle = mysql_num_rows($query_controle);
												while($row_controle = mysql_fetch_assoc($query_controle)){
													$tipo = $row_controle['tipo_pg'];
													switch($row_controle['status']){
														case 1:
															$color[$tipo] = "bgColor='#FF473E'";
															break;
														case 2:
															$color[$tipo] = "bgColor='#9BD248'";
															break;
														default: $color[$tipo] = '';
													}	
													
													//  COLOCANDO AS MENSAGENS DE STATUS
													switch($row_controle['status']){
														case 1: 
															$qr_fun = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario  = '$row_controle[id_user]'");
															$title[$tipo] = 'title="<b>Gerado em :</b> '.$row_controle['DATA'].'<br>';
															$title[$tipo] .= '<b>Por :</b> '.$row_controle['id_user'].' - '.@mysql_result($qr_fun,0).'<br>';
															$title[$tipo] .= '<b>Valor :</b> R$ '.$row_controle['valor'].'"';
															break;
														case 2:
															$qr_fun = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario  = '$row_controle[id_user]'");
															$title[$tipo] = 'title="<b>Gerado em :</b> '.$row_controle['DATA'].'<br>';
															$title[$tipo] .= '<b>Por :</b> '.$row_controle['id_user'].' - '.@mysql_result($qr_fun,0).'<br>';
															$title[$tipo] .= '<b>Pago em :</b> '.$row_controle['DATAPG'].'<br>';
															$qr_fun = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario  = '$row_controle[id_userpg]'");
															$title[$tipo] .= '<b>Por :</b> '.$row_controle['id_userpg'].' - '.@mysql_result($qr_fun,0).'<br>';
															$title[$tipo] .= '<b>Valor :</b> R$ '.$row_controle['valor'].'"';
															break;															
													}
												}
												if(empty($num_controle)){
													$color[1] = '';
													$color[2] = '';
													$color[3] = '';
													$color[4] = '';
													$color[5] = '';
													$title[1] = '';
													$title[2] = '';
													$title[3] = '';
													$title[4] = '';
													$title[5] = '';
												}
												
												
												// VERIFICANDO SE EXISTEM RECISÕES OU FERIAS
										$qr_recisao = mysql_query("SELECT * FROM rh_recisao 
																	WHERE
																	MONTH(data_demi) = '$mes' 
																	AND YEAR(data_demi) = '$ano' 
																	AND id_regiao = '$row_folha[id_regiao]' 
																	AND id_projeto = '$row_folha[projeto]'
																	AND status = '1'
																	");

										$qr_ferias = mysql_query("SELECT * FROM rh_ferias 
																	WHERE 
																	MONTH(data_ini) = '$mes' 
																	AND YEAR(data_ini) = '$ano' 
																	AND regiao = '$row_folha[id_regiao]' 
																	AND projeto = '$row_folha[projeto]'
																	AND status = '1'
																	");
																											
										$num_recisao = mysql_num_rows($qr_recisao);
										$num_ferias = mysql_num_rows($qr_ferias);
										
												
											?>
                                            <td <?=$title[1]?> <?=$color[1]?> align="center">  
                                            <span class="dados">
                                            <a href="cadastro.php?gps&tipo=CLT&mes=<?=$mes?>&ano=<?=$ano?>&projeto=<?=urlencode($row_folha['nome'])?>&folha=<?=$row_folha['id_folha']?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe', height: '400' } )">
                                            	<img src="../imagensrh/gps.jpg" />
                                            </a>
                                            </span>
                                            </td>
                                            <td <?=$title[2]?> <?=$color[2]?> align="center"><span class="dados"><a href="cadastro.php?fgts&tipo=CLT&mes=<?=$mes?>&ano=<?=$ano?>&projeto=<?=urlencode($row_folha['nome'])?>&folha=<?=$row_folha['id_folha']?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe', height: '400' } )"><img src="../imagensrh/log_fgts.jpg"/></a></span></td>
                                            <td <?=$title[3]?> <?=$color[3]?> align="center"><span class="dados"><a href="cadastro.php?pis&tipo=CLT&mes=<?=$mes?>&ano=<?=$ano?>&projeto=<?=urlencode($row_folha['nome'])?>&folha=<?=$row_folha['id_folha']?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe', height: '400' } )"><img src="../imagensrh/pis.jpg"/></a></span></td>
                                            <td <?=$title[4]?> <?=$color[4]?> align="center"><span class="dados"><a href="cadastro.php?ir&tipo=CLT&mes=<?=$mes?>&ano=<?=$ano?>&projeto=<?=urlencode($row_folha['nome'])?>&folha=<?=$row_folha['id_folha']?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe', height: '400' } )"><img src="../imagensrh/ir.jpg"/></a></span></td>
                                           <td <?=$title[5]?> <?=$color[5]?> align="center"><span class="dados">
                                           <?php if(!empty($num_recisao)): ?>
                                           <a href="cadastro2.php?id_folha=<?=$row_folha['id_folha']?>&tipo=2&mes=<?=$mes?>&ano=<?=$ano?>&projeto=<?=$row_folha['projeto']?>&regiao=<?=$row_folha['id_regiao']?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe', height: '400' } )">
                                           <img src="../imagensrh/recisao.png"/>
                                           </a>
                                           <?php endif;?>
                                           </span></td> 
                                           <td <?=$color[5]?> align="center"><span class="dados">
                                           <?php if(!empty($num_ferias)): ?>
                                           <a href="cadastro2.php?id_folha=<?=$row_folha['id_folha']?>&tipo=1&mes=<?=$mes?>&ano=<?=$ano?>&projeto=<?=$row_folha['projeto']?>&regiao=<?=$row_folha['id_regiao']?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe', height: '400' } )">
                                           <img src="../imagensrh/ferias.png"/>                                           
                                           </a>
                                           <?php endif; ?>
                                           </span></td> 
                                        </tr>
                               
                                <?php } ?>
                                 
                                </table>
                                </div>
                      </div>
                   </td>
                   <td align="center">
                        		<?=$total_participantes?>
                   </td>
                </tr>  
                                  
                  <?php unset($total_participantes); 
                  
                       }
                      
                   } ; // Fim do Loop dos Meses ?>
                  
                  </table>
		 </div> 
         
        
         
         
 	   <?php } // Fim do Loop dos Anos ?>
       
       <?php  for($ano=2009; $ano<=date('Y'); $ano++) { ?>
        <!-- Cooperado -->
         <div class="ano">
            <div class="titulo">FOLHAS DE PAGAMENTO <?=$ano?> <span class="destaque">COOP</span></div>
                  <table cellpadding="4" cellspacing="0" class="relacao">
                      <tr class="secao">
                        <td colspan="3">Mês Referente</td>
                      </tr>
            			 <?php // Loop dos Meses
                  			foreach($meses as $nome_mes => $mes){
                      			 $qr_folha_coop = mysql_query("SELECT *
                                                  FROM folhas f INNER JOIN projeto p ON f.projeto = p.id_projeto
                                                 WHERE p.id_master = '$_GET[id]' 
												   AND f.status = '3' 
                                                   AND f.mes = '$mes' 
                                                   AND f.ano = '$ano' 
												   AND f.contratacao = '3'
												   ");
                      		 $total_folha_coop = mysql_num_rows($qr_folha_coop);
                      			 if(!empty($total_folha_coop)) { ?>
                               
                <tr class="linha_<?php if($cor++%2==0) { ?>um<? } else { ?>dois<? } ?>">
                   <td colspan="2">
                      <div >
                            	<span class="folha_mes"><?=$nome_mes?>&nbsp;<img src="../folha/sintetica/seta_dois.gif"/></span>
                                <div style="display: none" >
									<table width="100%" bgcolor="#FFFFFF"  cellspacing="1" cellpadding="5">
                                    	<tr bgcolor="#CCCCCC">
                                        	<td><span class="cabecalho">ID folha</span></td>
                                        	<td><span class="cabecalho">Regiao</span></td>
                                        	<td><span class="cabecalho">Projeto</span></td>
                                            <td><span class="cabecalho">GPS</span></td>
                                            <td><span class="cabecalho">IR</span></td>
                                        </tr>
								<?php 
									while($row_folha_coop = mysql_fetch_assoc($qr_folha_coop)){
								?>
                                
                                        <tr class="linha_<?php if($cor2++%2==0) { ?>um<? } else { ?>dois<? } ?>">
                                        <?php 
												$query_controle = mysql_query("
												SELECT saida.status,pagamentos.tipo_pg FROM saida INNER JOIN pagamentos
												ON saida.id_saida = pagamentos.id_saida
												WHERE pagamentos.mes_pg = '$mes'
												AND pagamentos.ano_pg = '$ano'
												AND pagamentos.id_folha = '$row_folha_coop[id_folha]'
												AND saida.id_projeto = '$row_folha_coop[projeto]'
												");
												$num_controle = mysql_num_rows($query_controle);
												while($row_controle = mysql_fetch_assoc($query_controle)){
													$tipo = $row_controle['tipo_pg'];
													switch($row_controle['status']){
														case 1:
															$color[$tipo] = "bgColor='#FF473E'";
															break;
														case 2:
															$color[$tipo] = "bgColor='#9BD248'";
															break;
														default: $color[$tipo] = '';
													}
													//  COLOCANDO AS MENSAGENS DE STATUS
													switch($row_controle['status']){
														case 1: 
															$title[$tipo] = 'title="<b>Gerado em :</b> '.$row_controle['DATA'].'<br>';
															$title[$tipo] .= '<b>Por :</b> '.$row_controle['id_user'].'<br>"';
															break;
														case 2:
															$title[$tipo] = 'title="<b>Gerado em :</b> '.$row_controle['DATA'].'<br>';
															$title[$tipo] .= '<b>Por :</b> '.$row_controle['id_user'].'<br>';
															$title[$tipo] .= '<b>Pago em :</b> '.$row_controle['DATAPG'].'<br>';
															$title[$tipo] .= '<b>Por :</b> '.$row_controle['id_userpg'].'<br>"';
															break;
															
													}
												}
												if(empty($num_controle)){
													$color[1] = '';
													$color[4] = '';
													$title[1] = '';
													$title[4] = '';
												}
											?>
                                        
                                        	<td><span class="dados"><?=$row_folha_coop['id_folha']?></span></td>
                                        	<td><span class="dados">
											<?=
												$row_folha_coop['id_regiao'] . " - " . $row_folha_coop['regiao'];
											?></span>
                                            </td>
                                        	<td><span class="dados"><?=$row_folha_coop['nome']?></span></td>
                                            <td <?=$color[1]?>><span class="dados"><a href="cadastro.php?gps&tipo=COOP&mes=<?=$mes?>&ano=<?=$ano?>&projeto=<?=urlencode($row_folha_coop['nome'])?>&folha=<?=$row_folha_coop['id_folha']?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe', height: '400' } )"><img src="../imagensrh/gps.jpg"/></a></span></td>
                                            <td <?=$color[4]?>><span class="dados"><a href="cadastro.php?ir&tipo=COOP&mes=<?=$mes?>&ano=<?=$ano?>&projeto=<?=urlencode($row_folha_coop['nome'])?>&folha=<?=$row_folha_coop['id_folha']?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe', height: '400' } )"><img src="../imagensrh/ir.jpg"/></a></span></td>
                                        </tr>
                                 
                                <?php } ?> 
                                </table>
                                </div>
                      </div>
                   </td>
                   <td align="center">
                        		<?=$total_participantes?>
                   </td>
                </tr>  
                                  
                  <?php unset($total_participantes); 
                  
                       }
                      
                   } ; // Fim do Loop dos Meses ?>
                  
                  </table>
		 </div> 
          <!--///// Cooperado -->
       
       <?php } // Fim do Loop dos Anos ?> 
  </td>
 </tr>
 
 <tr>
 	<td>
<!-- ////////////////////////////////////// PROVISÂO  //////////////////////////////////////////-->
<?php 
$permissao = array('5','27','9','75','77','64','82');
if(in_array($_COOKIE['logado'],$permissao)){ ?>
<br>
<div id="provisoes">
	<div class="apDiv1">
	<table width="100%"   border="0" bordercolor="#FFFFFF" align="center" cellpadding="0" cellspacing="0" class="bordaescura1px">
	  <tr>
	    <td  height="25" colspan="5" align="left" valign="middle" bgcolor="#999999" ><span style="color:#FFF;"><strong>PROVIS&Otilde;ES AUTONOMOS
        <a style="font-size:12px; color:#FFF;" href="javascript:abrir('../../financeiro/cadastro.provisao.php?regiao=<?=$regiao?>','700','500','Cadastro de provisão')" class="linkMenu">Cadastrar provisão</a>
        </strong></span></td>
	    </tr>
	  <tr>
	    <td colspan="5">
        <?php 
		$query_provisao = mysql_query("SELECT 
									p.id_provisao,		 	 	 	
									p.id_projeto,		 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 
									p.ano_provisao		 	 	 	 	 	 	 
									FROM provisao AS p  LEFT JOIN projeto AS pr 
									ON pr.id_projeto = p.id_projeto 
									WHERE p.status_provisao = '1' AND pr.id_regiao = '$regiao' 
									ORDER BY p.id_provisao ASC;
										");
		$provisao_autonomo = array();
		while($row_provisao = mysql_fetch_assoc($query_provisao)){
			$projeto	= $row_provisao['id_projeto'];
			$ano 		= $row_provisao['ano_provisao'];
			$provisao_autonomo[$projeto][$ano][] = $row_provisao['id_provisao'];
			ksort($provisao_autonomo[$projeto]);
		}
		/*
		echo '<pre>';
		print_r($provisao_autonomo);
		exit;	
		*/		
		?>
        <table width="100%" border="0"  cellspacing='1' cellpadding='3'>
              <?php foreach($provisao_autonomo as $projeto => $anos){?>
                <tr bgcolor="#FBFBFB" class="linha_um" >
                  <td colspan="5" align="center" bgcolor="#CCCCCC" >
                  <span style="font-weight:bold; font-size:16px; color:#333; ">
                  	<?php 
					if($projeto_anterior != $projeto){
							$qr_projeto = mysql_query("SELECT nome,id_projeto FROM projeto WHERE id_projeto = '$projeto'");
							print mysql_result($qr_projeto,0);
						}
					?>
                  </span>
                  </td>
               	</tr>
                  <?php foreach($anos as $ano => $provisoes){ ?>
                  <tr bgcolor="#FBFBFB">
                  	<td  class="dataautonomos" colspan="5" align="center"  style="cursor:pointer;" bgcolor="<? if($alternateColorAno++%2==0) { ?>#EEEEEE<? } else { ?>#FFFFFF<? } ?>">
                    <span style="padding:10; margin-bottom:10px; font-size:14px; font-weight:bold">
                  		<?php
							if($ano_anterior != $ano){
								echo $ano;
							}
						?>
                    </span>
                  	</td>
                  </tr>
                  <tr>
                  	<td colspan="5"  >
             		<table width="100%" class="autonomos"  cellspacing='1' cellpadding='3'>
                      <tr class="linha_dois">
                        <td width="20%" bgcolor="#CCCCCC"><b>Provisão</b></td>
                        <td width="20%" bgcolor="#CCCCCC"><b>Mês</b></td>
                        <td bgcolor="#CCCCCC">&nbsp;</td>
                        <td width="20%" bgcolor="#CCCCCC"><b>Valor</b></td>
                        <td bgcolor="#CCCCCC">&nbsp;</td>
                      </tr>
                      <?php 
                        foreach($provisoes as $provisao){
                    ?>
                      <tr class="<? if($alternateColor++%2==0) { ?>linha_um<? } else { ?>linha_dois<? } ?>">
                          <?php 
                            $qr_provisao = mysql_query("SELECT * FROM provisao WHERE id_provisao = '$provisao'");
                            $rw_provisao = mysql_fetch_assoc($qr_provisao);
                          ?>
                      <td>
                           <?=$rw_provisao['id_provisao']?>
                      </td>
                     <td>
                            <?php
                               $qr_mes = mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$rw_provisao[mes_provisao]'"); 
                                echo mysql_result($qr_mes,0);
                            ?>
                      </td>
                      <td >
                      </td>
                      <td width="20%"><?php echo "R$ ".number_format($rw_provisao['valor_provisao'], 2, ',', ' '); $valor_total += $rw_provisao['valor_provisao']; ?></td>
                      <td width="10%" align="right">
                        <a href="javascript:abrir('cadastro.provisao.php?ID=<?=$rw_provisao['id_provisao']?>&regiao=<?=$regiao?>','700','500','Cadastro de provisão')" ><img  src="../../imagensmenu2/Edit.png"  width="20px" height="20px" border="0" /></a>
                        <a onclick="confirmacao('actions/cadastro.provisao.php?regiao=<?=$regiao?>&log=3&id=<?=$rw_provisao['id_provisao']?>','Tem certeza que deseja deletar esta provisao?')" href="#"><img src="../../imagensmenu2/Symbol-Delete.png" width="20px" height="20px" border="0" /></a>
                      </td>
                      </tr>
                      <?php } ?>	   
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right"><b>Total: </b></td>
                        <td><b><?php echo " R$ ".number_format ($valor_total, 2, ',', ' '); $valor_total = 0;?></b></td>
                     </tr>
                      </table>
                    </td>
                  </tr>
                  <?php
				  	$ano_anterior = $ano;	
				   	}?>
              	<?php 
					$ano_anterior = NULL;
					$projeto_anterior = $projeto;
				} ?>
              </table>
          </td>
	    </tr>
	  </table>
      </div>
    <br />
    <!-- ///////////////////// CLT ///////////////////////-->
    <?php
		$projeto = array();
		$sql_folha = "SELECT f.rendi_final, p.nome, f.mes, f.projeto, f.id_folha, f.ano, f.terceiro, f.tipo_terceiro
						FROM rh_folha f
						LEFT JOIN projeto p ON p.id_projeto = f.projeto
						WHERE p.id_regiao =  '$regiao'
						AND f.status =  '3' 
						AND p.status_reg != 0 
						ORDER BY f.mes ASC;";
					
		$query_folha = mysql_query($sql_folha);
		while($row_folha = mysql_fetch_assoc($query_folha)){
			$chave_projeto = $row_folha['projeto'];
			$ano_folha = $row_folha['ano'];
			$projetos[$chave_projeto][$ano_folha][] =  $row_folha['id_folha'];
			ksort($projetos[$chave_projeto][$ano_folha]);
		}
	?>
   <div class="apDiv1">
<table width="100%"   border="0" bordercolor="#FFFFFF" align="center" cellpadding="0" cellspacing="0" class="bordaescura1px">
  <tr>
    <td  height="25" align="left" valign="middle" bgcolor="#999999"  >
        <span style="color:#FFF;"><strong>PROVIS&Otilde;ES CLT</strong></span>
    </td>
  </tr>
  <tr>
      <?php 
	  	if(!empty($projetos)){
			foreach($projetos as $projeto => $anos) {
				foreach($anos as $ano => $folhas) {
					if($projeto != $ultimo_projeto) { ?>
						<tr>
						  <td align="center" bgcolor="#CCCCCC" ><span style="font-weight:bold; font-size:16px; color:#333; ">
						  <?php 
						  		$query_projetos = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$projeto'");
						  		print mysql_result($query_projetos,0);
							?>
                            </span>
                           </td>
						</tr>
              <?php } if($ano != $ultimo_ano) { ?>
              		<tr style="cursor:pointer;" >
                      <td height="20" class="ano2" align="center" bgcolor="<? if($alternateColorAno++%2==0) { ?>#EEEEEE<? } else { ?>#FFFFFF<? } ?>" >
                          <span style="padding:10; margin-bottom:10px; font-size:14px; font-weight:bold">
							<?=$ano?>
                          </span>
                      </td>
                    </tr>
     		<?php }?>
     			<tr>
        	 		<td >
                    	<table style="clear:both;" width="100%" align="center"  cellspacing='1' cellpadding='3'>
                        	<tr>
                            	<td><b>Folha</b></td>
                                <td><b>Mês</b></td>
                                <td><b>Valor total da folha</b></td>
                                <td colspan="2"><b>Total gasto</b></td>
                                <td colspan="2"><b>Valor da provis&atilde;o</b></td>
                                <td><b>Valor a pagar</b></td>
                            </tr>
                    <?php foreach($folhas as $folha) { 
							$qr_folha = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$folha';");
							$rw_folha = mysql_fetch_assoc($qr_folha);
					?>
                            <tr class="<? if($alternateColor++%2==0) { ?>linha_um<? } else { ?>linha_dois<? } ?>">
                              <td><?=$rw_folha['id_folha']?></td>
                              <td>
							<?php
                                $query_meses = mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$rw_folha[mes]';");
                                $print_mes = mysql_result($query_meses,0);
                                if($rw_folha['terceiro'] == '1'){
                                    if($rw_folha['tipo_terceiro'] == 3){
                                        $print_mes .= "13ª integral";
                                    }else{
                                        $print_mes .=" 13ª ($rw_folha[tipo_terceiro]ª) Parcela";
                                    }
                                }						  			
                                echo $print_mes;
                                $qr_recisao = mysql_query("SELECT * FROM rh_recisao WHERE MONTH(data_demi) = '$rw_folha[mes]' AND YEAR(data_demi) = '$rw_folha[ano]' AND id_regiao = '$rw_folha[regiao]' AND id_projeto = '$rw_folha[projeto]'");
								$qr_ferias = mysql_query("SELECT * FROM rh_ferias WHERE MONTH(data_ini) = '$rw_folha[mes]' AND YEAR(data_ini) = '$rw_folha[ano]' AND regiao = '$rw_folha[regiao]' AND projeto = '$rw_folha[projeto]'");
								$num_recisao = mysql_num_rows($qr_recisao);
								$num_ferias = mysql_num_rows($qr_ferias);
								?>
                                <?php if(!empty($num_recisao) or !empty($num_ferias)):?>
                                <a class="recisao"><img src="../folha/sintetica/seta_um.gif" width="9" height="9" style="cursor:pointer"/></a>
                                <div style="display:none">
                                <table>
                                	<tr>
                                    	<td>ID</td>
                                    	<td>Nome</td>
                                        <td>Total</td>
                                    </tr>
									<?php 
									if(!empty($num_recisao)):
									while($row_recisao = mysql_fetch_assoc($qr_recisao)): ?>
                                    <tr>
                                    	<td>
                                    		<?=$row_recisao['id_clt']?>
                                    	</td>
                                        <td>
                                    		<?=$row_recisao['nome']?>
                                    	</td>
                                        <td>
                                    		<?="R$ " .number_format ($row_recisao['total_liquido'], 2, ',', ' ')?>
                                    	</td>
                                    </tr>
                                   <?php 
								   		$Total_recisao += $row_recisao['total_liquido'];
								   endwhile;?>
                                   <tr>
                                        <td>&nbsp;</td>
                                        <td align="right">Total recisão:</td>
                                        <td>
                                            <?="R$ " .number_format ($Total_recisao, 2, ',', ' ')?>
                                        </td>
                                    </tr>
                                   <?php endif;?>
                                   <?php 
								   if(!empty($num_ferias)):
								   while($row_ferias = mysql_fetch_assoc($qr_ferias)):?>
                                   <tr>
                                    	<td>
                                    		<?=$row_ferias['id_clt']?>
                                    	</td>
                                        <td>
                                    		<?php
												$qr = mysql_query("SELECT nome FROM rh_clt WHERE id_clt = '$row_ferias[id_clt]'");
												$nome = @mysql_result($qr,0);
											 	echo $nome;
											?>
                                    	</td>
                                        <td>
                                    		<?="R$ " .number_format ($row_ferias['total_liquido'], 2, ',', ' ')?>
                                    	</td>
                                    </tr>
                                   <?php
								   	$Total_ferias += $row_ferias['total_liquido'];
								    endwhile;?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td align="right">Total Ferias:</td>
                                        <td>
                                            <?php 
											echo"R$ " .number_format ($Total_ferias, 2, ',', ' ');
											?>
                                        </td>
                                    </tr>
                                   <tr>
                                   <?php endif;?>
                                   	<td>&nbsp;</td>
                                    <td align="right"><b>Total: </b></td>
                                   	<td><b><?php 
											$Total_gasto = $Total_recisao+$Total_ferias;
											 
											$total_final += $Total_gasto;
											echo "R$ " .number_format ($Total_gasto, 2, ',', ' ');
											$Total_recisao = NULL;
											$Total_ferias = NULL;
										?>
                                        </b>
                                    </td>
                                   </tr>
                                </table>
                                </div>
                                <?php endif;?>
                              </td>
                              <td>
							  	<?php 
									// se for folha de 13° salario pegar o valor da valor_dt
							  		if($rw_folha['terceiro'] == '1'){
										$total = $rw_folha['valor_dt'];
									}else{
										$total = $rw_folha['rendi_final'];
									}
									echo "R$ " . number_format ($total, 2, ',', ' ');
									?>
                              </td>
								<td colspan="2">
									<?php
									echo "R$ " . number_format ($Total_gasto, 2, ',', ' ');
									?></td>
                              <td colspan="2">
							  	<?php
                              		$totalF=($total* 33.93)/100;
									$somatorio += $totalF;
									echo "R$ ".number_format ($totalF, 2, ',', ' ');
								?>
                                </td>
                                <td>
								<?php 
									$total_a_pagar = $totalF - $Total_gasto;
									$total_a_pagar_final += $total_a_pagar;
									echo "R$ ".number_format ($total_a_pagar, 2, ',', ' ');
									unset($total_a_pagar);
								?></td>
                            </tr>
                      <?php unset($Total_gasto); } ?>
                      		<tr>
                            	<td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td align="right">Total:</td>
                                <td align="left"><?="R$ ".number_format ($total_final, 2, ',', ' ')?></td>
                                <td align="right">Total:</td>
                                <td><?="R$ ".number_format ($somatorio, 2, ',', ' ')?></td>
                                <td align="right">Total:</td>
								<td>
								<?php 
									echo "R$ ".number_format ($total_a_pagar_final, 2, ',', ' ');
									unset($total_a_pagar_final);
								?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td align="right"><b>Total final:</b></td>
                                <td colspan="2"><b>
                                	<?php 
										$Calc_final = $somatorio - $total_final;
										echo "R$ ".number_format ($Calc_final, 2, ',', ' ');
									?>
                                    </b>
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                      	</table>
          			</td>
        		</tr>
<?php
	 		$ultimo_projeto = $projeto;
			$ultimo_ano		= $ano;
			unset($Total_gasto,$somatorio,$total_final);
				}
			unset($ultimo_ano);
			}
		}
	}// fim do if da permisao
?>
</table>
</div>
<!-- ///////////////////////// CLT //////////////////// -->  
</div>
	<!-- ////////////////////////////////////// PROVISÂO  //////////////////////////////////////////-->

    </td>
 </tr>
</table>
</body>
</html>