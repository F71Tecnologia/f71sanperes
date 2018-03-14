<?php 

include ("include/restricoes.php");
include "../classes/mes.php";

$ordem = $_REQUEST['ordem'];
$mes = $_REQUEST['mes'];
$ano = $_REQUEST['ano'];
$projeto = $_REQUEST['projeto'];




$campo_ordem = "data_vencimento";


$totalizador_entrada = 0;
$totalizador_folha = 0;
$totalizador_reserva = 0;
$totalizador_taxa = 0;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>RELATORIO GERENCIAL</title>
<!-- highslide -->
<link rel="stylesheet" type="text/css" href="../js/highslide.css" />
<script type="text/javascript" src="../js/highslide-with-html.js"></script>
<script type="text/javascript" >
	hs.graphicsDir = '../images-box/graphics/';
	hs.outlineType = 'rounded-white';
	hs.showCredits = false;
	hs.wrapperClassName = 'draggable-header';
</script>
<!-- highslide -->
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<link href="style/relatorio.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="base">
	<div id="topo">
    	<table align="center">
        	<tr>
            	<td width="110px"> 
                <?php 
					$query_master = mysql_query("SELECT id_master,id_projeto,nome FROM projeto WHERE id_projeto = '$projeto'");
				?>
                <img src="../imagens/logomaster<?=@mysql_result($query_master,0);?>.gif" width="110" height="79"></td>
                <td >
                	<?php
                    	$mes_class = new mes();
						foreach($mes_class->getItens() as $num => $nome){
							if($mes == $num) {
									$data =  $nome.' / '.$ano; 
									break;
							}
						}
					?>
					
					<table align="left">
                    	<tr>
                        	<td align="center"><span style="font-size:18px"><?php echo @mysql_result($query_master,0,1)." - ".@mysql_result($query_master,0,2);?></span></td>
                        </tr>
                        <tr>
                        	<td align="center"><span style="font-size:14px"><?=$data?></span></td>
                        </tr>
                        <tr>
                        	<td align="center"><span style="font-size:14px">Por <?php if($ordem == 1){echo "Vencimento";}else{echo "Pagamento";}?></span></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
  <div id="entradas">
  <?php 
  	$query_tipo_entradas = mysql_query("SELECT * FROM entradaesaida WHERE id_entradasaida = '12'");
  ?>
    <h2 class="titulo">Entradas</h2>
    <table border="0" align="center" cellpadding="5" cellspacing="1">
    <?php while($row_tipo_entradas = mysql_fetch_assoc($query_tipo_entradas)):?>
    <?php
        // gato do urgento do banco Maikom james 14/03/2011
        $sql_banco = (!empty($_REQUEST['bancos'])) ? "AND ent.id_banco = '$_REQUEST[bancos]'" : "";

	$query_entradas = mysql_query('
	SELECT SUM(REPLACE(ent.valor,\',\',\'.\') + REPLACE(ent.adicional,\',\',\'.\')) FROM entradaesaida AS tip INNER JOIN entrada AS ent 
	ON ent.tipo =  tip.id_entradasaida 
	WHERE tip.grupo = 5
	AND tip.id_entradasaida = '.$row_tipo_entradas['id_entradasaida'].'
	AND MONTH(ent.'.$campo_ordem.') = '.$mes.'
	AND YEAR(ent.'.$campo_ordem.') = '.$ano.'
	AND ent.id_projeto = '.$projeto.' 
	AND ent.status = 2
        '.$sql_banco.'
	ORDER BY ent.'.$campo_ordem);
	$Total_entradas = @mysql_result($query_entradas,0);
	if(!empty($Total_entradas)):
	$totalizador_entrada += $Total_entradas;
	?>
      <tr class="<? if($alternateColor++%2==0) { ?>linha_um<? } else { ?>linha_dois<? } ?>">
        <td width="68%"><?=$row_tipo_entradas['id_entradasaida'].' - '.$row_tipo_entradas['nome'];?>
        	<a href="view/detalhes.relarorio.gerencial.php?grupo=5&mes=<?=$mes?>&ano=<?=$ano?>&projeto=<?=$projeto?>&id_entradasaida=<?=$row_tipo_entradas['id_entradasaida']?>&ordem=<?=$campo_ordem?>&bancos=<?=$_REQUEST['bancos']?>" onclick="return hs.htmlExpand(this, { contentId: 'highslide-html-ajax', wrapperClassName: 'highslide-white', outlineType: 'rounded-white', outlineWhileAnimating: true, objectType: 'ajax', preserveContent: true, width: 700 } )" class="detalhar"  title="VER DETALHES">
            <img src="image/seta.gif" width="9" height="9" border="0" />
            </a>
        </td>
        <td width="32%">R$ <?=number_format($Total_entradas,2,',','.');?></td>
      </tr>
    <?php 
	endif;
	endwhile; ?>
    	<tr>
        	<td align="right"><span class="totais">Total de entradas: </span></td>
            <td><span class="totais">R$ <?=number_format($totalizador_entrada,2,',','.');?></span></td>
        </tr>
    </table>
  </div>
  <div id="saidas">
  	<hr />

  	<h2  class="titulo">Saidas</h2>
    <div class="bloco1">
    <span class="subtitulos">FOLHA</span>
    <?php 
	$query_folha_tipo = mysql_query("SELECT * FROM entradaesaida WHERE grupo = '1';");
	?>
<table width="100%" border="0"  cellpadding="5" cellspacing="1">
	<?php while($row_folha_tipo = mysql_fetch_assoc($query_folha_tipo)):?>
    <?php
        // gato do urgento do banco Maikom james 14/03/2011
        $sql_banco = (!empty($_REQUEST['bancos'])) ? "AND sai.id_banco = '$_REQUEST[bancos]'" : "";
		$sql = "SELECT SUM(REPLACE(sai.valor,',','.') + REPLACE(sai.adicional,',','.')) FROM entradaesaida AS tip INNER JOIN saida AS sai
		ON tip.id_entradasaida = sai.tipo
		WHERE tip.grupo = '1'
		AND MONTH(sai.$campo_ordem) = '$mes' 
		AND YEAR(sai.data_vencimento) = '$ano'
		AND sai.id_projeto = '$projeto'
		AND tip.id_entradasaida = '$row_folha_tipo[id_entradasaida]'
		AND sai.status = '2'
                $sql_banco
		ORDER BY sai.data_pg;";
		$query_folha = mysql_query($sql);
		$Total_folha = @mysql_result($query_folha,0);
		if(!empty($Total_folha)):
		$totalizador_folha += $Total_folha;
	?>
       <tr class="<? if($alternateColor++%2==0) { ?>linha_um_bloco1<? } else { ?>linha_dois_bloco1<? } ?>">
          <td width="70%"><?=$row_folha_tipo['id_entradasaida'].' - '.$row_folha_tipo['nome']?>
          		<a href="view/detalhes.relarorio.gerencial.php?grupo=1&mes=<?=$mes?>&ano=<?=$ano?>&projeto=<?=$projeto?>&id_entradasaida=<?=$row_folha_tipo['id_entradasaida']?>&ordem=<?=$campo_ordem?>&bancos=<?=$_REQUEST['bancos']?>" onclick="return hs.htmlExpand(this, { contentId: 'highslide-html-ajax', wrapperClassName: 'highslide-white', outlineType: 'rounded-white', outlineWhileAnimating: true, objectType: 'ajax', preserveContent: true, width: 700 } )" class="detalhar"  title="VER DETALHES">
          		<img src="image/seta.gif" width="9" height="9" border="0" />
                </a>
                </td>
          <td width="30%">R$ <?=number_format($Total_folha,2,',','.')?></td>
        </tr>
    <?php 
		endif;
	endwhile; ?>
        <tr>
          <td align="right"><span class="totais">Total folha: </span></td>
          <td><span class="totais">R$ <?=number_format($totalizador_folha,2,',','.')?></span></td>
        </tr>
      </table>
    </div>
    <div class="bloco3">
	  <span class="subtitulos">TAXA ADMINISTRATIVA</span>
 <?php $query_taxa_tipo = mysql_query("SELECT * FROM entradaesaida WHERE id_entradasaida = '65';");?>
    	<table width="100%" border="0"  cellpadding="5" cellspacing="1">
      <?php while($row_taxa_tipo = mysql_fetch_assoc($query_taxa_tipo)):?>
	  <?php 
                // gato do urgento do banco Maikom james 14/03/2011
                $sql_banco = (!empty($_REQUEST['bancos'])) ? "AND sai.id_banco = '$_REQUEST[bancos]'" : "";
               

		$sql = "SELECT SUM(REPLACE(sai.valor,',','.') + REPLACE(sai.adicional,',','.')) FROM entradaesaida AS tip INNER JOIN saida AS sai
		ON tip.id_entradasaida = sai.tipo
		WHERE tip.grupo = '3'
		AND MONTH(sai.data_vencimento) = '$mes' 
		AND YEAR(sai.data_vencimento) = '$ano'
		AND sai.id_projeto = '$projeto'
		AND tip.id_entradasaida = '$row_taxa_tipo[id_entradasaida]'
		AND sai.status = '2'
                $sql_banco
		ORDER BY sai.data_pg;";
		$query_taxa = mysql_query($sql);
		$Total_taxa = @mysql_result($query_taxa,0);
		$totalizador_taxa += $Total_taxa;
		if(!empty($Total_taxa)):
	?>
    	<tr class="<? if($alternateColor++%2==0) { ?>linha_um_bloco3<? } else { ?>linha_dois_bloco3<? } ?>">
          <td width="70%"><?=$row_taxa_tipo['id_entradasaida'].' - '.$row_taxa_tipo['nome']?>
          	<a href="view/detalhes.relarorio.gerencial.php?grupo=3&mes=<?=$mes?>&ano=<?=$ano?>&projeto=<?=$projeto?>&id_entradasaida=<?=$row_taxa_tipo['id_entradasaida']?>&ordem=<?=$campo_ordem?>&bancos=<?=$_REQUEST['bancos']?>" onclick="return hs.htmlExpand(this, { contentId: 'highslide-html-ajax', wrapperClassName: 'highslide-white', outlineType: 'rounded-white', outlineWhileAnimating: true, objectType: 'ajax', preserveContent: true, width: 700 } )" class="detalhar"  title="VER DETALHES">
          <img src="image/seta.gif" width="9" height="9" border="0"/>
          	</a>
          </td>
          <td width="30%">R$ <?=number_format($Total_taxa,2,',','.')?></td>
        </tr>
	<?php
		endif;
		endwhile;
	?>
    	<tr>
        	<td align="right"><span class="totais">Total taxa: </span></td>
            <td><span class="totais">R$ <?=number_format($totalizador_taxa,2,',','.')?></span></td>
        </tr>
  	  </table>
    </div>
    <div class="bloco2">
     <span class="subtitulos">RESERVA</span>
	   <?php $query_reserva_tipo = mysql_query("SELECT * FROM entradaesaida WHERE grupo = '2';");?>
        <table width="100%" border="0"  cellpadding="5" cellspacing="1">
        <?php while($row_reserva_tipo = mysql_fetch_assoc($query_reserva_tipo)):?>
        <?php
                // gato do urgento do banco Maikom james 14/03/2011
                $sql_banco = (!empty($_REQUEST['bancos'])) ? "AND sai.id_banco = '$_REQUEST[bancos]'" : "";

		$sql = "SELECT SUM(REPLACE(sai.valor,',','.') + REPLACE(sai.adicional,',','.')) FROM entradaesaida AS tip INNER JOIN saida AS sai
		ON tip.id_entradasaida = sai.tipo
		WHERE tip.grupo = '2'
		AND MONTH(sai.data_vencimento) = '$mes' 
		AND YEAR(sai.data_vencimento) = '$ano'
		AND sai.id_projeto = '$projeto'
		AND tip.id_entradasaida = '$row_reserva_tipo[id_entradasaida]'
		AND sai.status = '2'
                $sql_banco
		ORDER BY sai.data_pg;";
		$query_reserva = mysql_query($sql);
		$Total_reserva = @mysql_result($query_reserva,0);
		$totalizador_reserva += $Total_reserva;
		if(!empty($Total_reserva)):
	?>
       <tr class="<? if($alternateColor++%2==0) { ?>linha_um_bloco2<? } else { ?>linha_dois_bloco2<? } ?>">
    	    <td><?=$row_reserva_tipo['id_entradasaida'].' - '.$row_reserva_tipo['nome']?>
            <a href="view/detalhes.relarorio.gerencial.php?grupo=2&mes=<?=$mes?>&ano=<?=$ano?>&projeto=<?=$projeto?>&id_entradasaida=<?=$row_reserva_tipo['id_entradasaida']?>&ordem=<?=$campo_ordem?>&bancos=<?=$_REQUEST['bancos']?>" onclick="return hs.htmlExpand(this, { contentId: 'highslide-html-ajax', wrapperClassName: 'highslide-white', outlineType: 'rounded-white', outlineWhileAnimating: true, objectType: 'ajax', preserveContent: true, width: 700 } )" class="detalhar"  title="VER DETALHES">
            <img src="image/seta.gif" width="9" height="9" border="0" />
            </a>
            </td>
    	    <td>R$ <?=number_format($Total_reserva,2,',','.')?></td>
  	    </tr>
    <?php 
		endif;
		endwhile;?>
    	<tr>
    	    <td align="right"><span class="totais">Total da reserva: </span></td>
    	    <td><span class="totais">R$ <?=number_format($totalizador_reserva,2,',','.')?></span></td>
  	    </tr>
      </table>
    </div>
  </div>
  <div id="totalizador">
  	<table width="100%">
    	<tr>
        	<td align="right"><span class="totais">Total de entradas</span></td>
            <td><span class="totais">R$ <?=number_format($totalizador_entrada,2,',','.')?></span></td>
            <td align="right"><span class="totais">Total de saidas</span></td>
          <td><span class="totais">R$ <?php 
					$totalalizador_saidas = $totalizador_folha+$totalizador_reserva+$totalizador_taxa;
					echo number_format($totalalizador_saidas,2,',','.');
					?></span></td>
            <td align="right"><span class="totais">TOTAL FINAL: </span></td>
            <td>
            <?php if($totalizador_final <=0){
				$vermelho = "style=\"color:#F00\"";
			}?>
            <span class="totais" <?=$vermelho?>>R$ <?php 
						$totalizador_final = $totalizador_entrada - $totalalizador_saidas;
						echo number_format($totalizador_final,2,',','.');
					?></span>
          </td>
        </tr>
    </table>
  </div>
</div>
</body>
</html>