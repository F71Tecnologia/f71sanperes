<?php

include ("include/restricoes.php");

// GATO PARA CORRIGIR O 
//  SAIDA
foreach($_REQUEST as $chave => $val){
	$qr_string[] = $chave.'='.$val;
}
$serve_string = implode('&',$qr_string);
if(!isset($_GET['id'])){
	header("location: relfinanceiro2.php?".$serve_string);
}



include "../conn.php";
include "../funcoes.php";
$id = $_REQUEST['id'];
$projeto = $_REQUEST['projeto'];
$regiao = $_REQUEST['regiao'];
$id_user = $_COOKIE['logado2'];

////ENCRIPTAR REGI�O
$link_enc = encrypt($regiao);
$link_enc = str_replace('+', '--', $link_enc);


function grupo($valor){
	$query = mysql_query("SELECT * FROM entradaesaida_grupo WHERE status_grupo != '0'");
	while($row = mysql_fetch_assoc($query)){
		if($row['id_grupo'] == $valor) return $row['nome_grupo'];
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<title>Intranet</title>

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


 <link rel="stylesheet" href="../jquery/thickbox/thickbox.css" type="text/css" media="screen" />       

<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../jquery/jquery.tools.min.js"></script>
<script type="text/javascript" src="../jquery/thickbox/thickbox.js"></script>
<script type="text/javascript" >
$(function(){
	$('[title]').tooltip(
	{position: "center right",
	 tipClass: 'bloco',
	 onShow: function() {
		this.html()
	}
	});
});
</script>
<script type="text/javascript" src="../jquery/tablesorte/jquery.tablesorter.js"></script> 
<link rel="stylesheet" type="text/css" href="../jquery/tablesorte/blue/style.css"/>
<link rel="stylesheet" href="../js/lightbox.css" type="text/css" media="screen" />
<link href="../net1.css" rel="stylesheet" type="text/css">
<style type="text/css">
body {
	margin:0px;
	padding:0px;
	text-align:center;
	text-transform:uppercase;
}
#baseCentral{
	padding:5px;
	margin:0px auto;
	text-align:left;
	
}
#topo{
	padding:5px;
	width:100%;
	position:fixed;
	overflow:hidden;
	top:0px;
	background-color:#FFF;
	z-index:1000;
	height:135px;
	border-top-width: 1px;
	border-right-width: 1px;
	border-left-width: 1px;
	border-top-style: solid;
	border-right-style: solid;
	border-left-style: solid;
	border-top-color: #666;
	border-right-color: #666;
	border-left-color: #666;
}
.conteudo {
	width:100%;
	position:relative;
	top:140px;
	background-color:#FFF;
	border-right-width: 1px;
	border-bottom-width: 1px;
	border-left-width: 1px;
	border-right-style: solid;
	border-bottom-style: solid;
	border-left-style: solid;
	border-right-color: #666;
	border-bottom-color: #666;
	border-left-color: #666;
}
.titulo {	
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 14px;
	color: #FF0000;
}
/* Criado por maikom james usado para o Title */
.bloco {
	display: none;
	font-size:12px;
	color:#333;
	background-color:#FFD2D2;
	padding: 5px;
	border: 1px solid #CCC;
	text-align:center;
}
.bloco a{
	color:#333;
}

</style>
</head>
<body>
<div id="baseCentral">
<div id="topo">
<?php 
	$query_master = mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$regiao'");
	$query_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao'");
	$id_master = @mysql_result($query_master,0);
?>
	<table width="100%">
    	<tr>
          <td rowspan="3" width="110">
              <img src="../imagens/logomaster<?=$id_master?>.gif" width="110" height="79">
          </td>
      	  <td align="left" valign="top">
          	<br />
                Data:&nbsp;<strong><?=date("d/m/Y");?></strong>&nbsp;<br />
        voc&ecirc; est&aacute; visualizando a Regi&atilde;o:&nbsp;<strong><?=@mysql_result($query_regiao,0); ?></strong></td>
          <td>
          
            </td>
      </tr>          
    </table>
      <center>
        <img src="imagensfinanceiro/relatorio-32.png" alt="fornecedor" width="32" height="32" />&nbsp;<span class="titulo">RELATO&#769;RIOS FINANCEIROS</span>
      </center>
</div> 
<?php echo "oi77";
switch ($id){
case 1:
$data_ini = $_REQUEST['data_ini'];
$data_fim = $_REQUEST['data_fim'];
$d = explode ("/", $data_ini);            
$data_ini_f = "$d[2]-$d[1]-$d[0]";        
$data_ini_f = $data_ini_f." 00:00:00";
$d = explode ("/", $data_fim);            
$data_fim_f = "$d[2]-$d[1]-$d[0]";        
$data_fim_f = $data_fim_f." 23:59:59";
$result = mysql_query("SELECT *,date_format(data_vencimento,'%d/%m/%Y') as data2 FROM entrada where data_vencimento >= '$data_ini_f' and data_vencimento <= '$data_fim_f' and id_projeto = '$projeto' and status = '2' ORDER BY data_vencimento");
$result_pr = mysql_query("SELECT * from projeto where id_projeto = '$projeto' '");
$row_pr = mysql_fetch_array($result_pr);

?>
<script type="text/javascript">
$(function(){
		$("#tableOrden").tablesorter({
            dateFormat: 'dd/mm/yyyy',
			headers : {0: {sorter: false} }
		
		});
});
</script>

<table border="0" width="100%" align="center" cellpadding="5" cellspacing="0" class="conteudo">
  <tr>
    <td colspan="2"><div align="center"><b>CONTROLE DE ENTRADAS - <?=$row_pr['nome']?></b></div></td>
  </tr>
  <tr>
    <td colspan="2">
    <table align="center" width="100%" cellpadding="3" cellspacing="1" id="tableOrden" class="tablesorter">
    <thead>
      <tr>
       <th>&nbsp;</th>
       <th>C�DIGO ENTRADA</th>
       <th >DATA DE RECEBIMENTO</th>
       <th>NOME DO CR�DITO</th>
       <th>CONTA CREDITADA</th>
       <th>TIPO DE ENTRADA</th>
       <th>DESCRI��O</th>
       <th>CADASTRADA POR</th>
       <th>CONFIRMADA POR</th>
       <th>VALOR ADICIONAL</th>
       <th>VALOR TOTAL</th>
      </tr>
      </thead>
      <tbody>
<?php 	  
$cont1 = "1";

while($row = mysql_fetch_array($result)){
if($cont1 % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }


$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row[id_user]'");
$row_user = mysql_fetch_array($result_user);
$result_user_pg = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row[id_userpg]'");
$row_user_pg = mysql_fetch_array($result_user_pg);


$result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$row[id_banco]' and status_reg ='1'");
$row_banco = mysql_fetch_array($result_banco);
$result_tipo = mysql_query("SELECT * FROM entradaesaida where id_entradasaida = '$row[tipo]'");
$row_tipo = mysql_fetch_array($result_tipo);
if($row['especifica'] == ""){ $descricao = "&nbsp;"; }else{ $descricao = $row['descricao']; }
if($row['adicional'] == ""){ $adicional = "&nbsp;"; }else{ $adicional = $row['adicional']; }
unset($comprovante);
//////////////////////////////////////////////////////////////////////////////////
if($row['comprovante'] == 0){
	$comprovante = $row['nome'];
}elseif($row['comprovante'] == 1){
	$tipo = $row['tipo_arquivo'];
	if(empty($row['tipo_arquivo'])) $tipo = ".gif";
	$comprovante = '<a href="../comprovantes/'.$row['id_saida'].$tipo.'" target="_blank"> '.$row['nome'].'</a>';
}elseif($row['comprovante'] == 2){
	
	
	$comprovante = $row['nome'];
        $query = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row[0]'");
	$num_arquivos = mysql_num_rows($query);
	
	if($num_arquivos > 1){
		$cont = 1;
		while($row_files = mysql_fetch_assoc($query)){
		$arquivos[] = "<a href=\"../comprovantes/$row_files[id_saida_file].$row_files[id_saida]$row_files[tipo_saida_file]\" target='_blank' >" ."arquivo $cont". "</a>";
		$cont++;
		}
		$comprovante .=  "<br>".implode("<br>",$arquivos);
	}else{
		$row_files = mysql_fetch_assoc($query);
		$comprovante = "<a href=\"../comprovantes/$row_files[id_saida_file].$row_files[id_saida]$row_files[tipo_saida_file]\" target='_blank' title='Anexo'>".$row['nome']."</a>";
	}
	
}
//////////////////////////////////////////////////////////////////////////////////

?>
<tr bgcolor=<?=$color?>> 
		<td>
        	<a title="Editar Entrada"  href="../novoFinanceiro/view/editar.entrada.php?id_entrada=<?=$row[0];?>" onclick="return hs.htmlExpand(this, { objectType: 'iframe', width: 580 } )" ><img src="imagensfinanceiro/page_edit-32.png" /></a>
        	<?php
        		if($row['tipo'] == '12'):

						$qr_notas = mysql_query("SELECT notas_files.id_file, notas_files.tipo, notas_files.id_notas FROM (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
													INNER JOIN notas_files ON notas.id_notas = notas_files.id_notas 
											WHERE notas_assoc.id_entrada = '$row[id_entrada]' GROUP BY notas_files.id_file;") or die(mysql_error());
						$num_notas = mysql_num_rows($qr_notas);
						$row_notas = mysql_fetch_assoc($qr_notas);
						if (!empty($num_notas)): ?>
						<a target="_blank" href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/intranet/adm/adm_notas/visializa_files.php?id_nota='.$row_notas['id_notas']; ?>&entrada=<?php echo $row['id_entrada'];?>">
							<img src="../financeiro/imagensfinanceiro/attach-32.png" width="16" height="16"  border="0"/>
						</a>
						<?php 
						
						endif;
						
				endif;
        	?>
        </td>
        <td class=border2><span style="font-size:10px; font-family:Arial, Helvetica, sans-serif"><?=$row[0]?></span></td>
        <td class=border2><span style="font-size:10px; font-family:Arial, Helvetica, sans-serif"><?=$row['data2']?></span></td>
        <td class=border2><span style="font-size:10px; font-family:Arial, Helvetica, sans-serif"><?=$comprovante?></span></td>
        <td class=border2><span style="font-size:10px; font-family:Arial, Helvetica, sans-serif">AG: <?=$row_banco['agencia']?> / C: <?=$row_banco['conta']?></span></td>
        <td class=border2><span style="font-size:10px; font-family:Arial, Helvetica, sans-serif"><?=$row_tipo['nome']?></span></td>
        <td class=border2>&nbsp;<span style="font-size:10px; font-family:Arial, Helvetica, sans-serif"><?=$descricao?></span></td>
        <td class=border2><span style="font-size:10px; font-family:Arial, Helvetica, sans-serif"><?=$row_user[0]?></span></td>
		<td class=border2><span style="font-size:10px; font-family:Arial, Helvetica, sans-serif"><?=$row_user_pg[0]?></span></td>
        <td class=border2><span style="font-size:10px; font-family:Arial, Helvetica, sans-serif">R$ <?=number_format($adicional,2,',','.')?></span></td>
        <td class=border2><span style="font-size:10px; font-family:Arial, Helvetica, sans-serif">R$ <?=number_format($row['valor'],2,',','.')?></span></td>
      </tr>
<?php
$valor_soma = str_replace(",",".",$row['valor']);
$adicional = str_replace(",",".",$row['adicional']);
$valor_total1 = $valor_total1 + $valor_soma + $adicional;
$cont1 ++;
}
$valor_total1 = number_format($valor_total1,2,",",".");
?>
</tbody>
      <tr>
        <td colspan='9'><div align='right' class='style12'>TOTAL DE ENTRADAS <?=$data_ini?> a <?=$data_fim?>:</div></td>
        <td><b>R$ <?=$valor_total1?></b></td>
      </tr>
    </table>
    </td>
  </tr>
  <tr>
    <td colspan='2' height='15' align='center' valign='top' bgcolor='#FFFFFF' class='style3'><a href='../novoFinanceiro/relatorios.php?enc=<?=$link_enc?>'>voltar</a></td>
  </tr>
  
  <tr>
    <td width='532' height='15' align='right' valign='top' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
    <td align='center' valign='middle' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
  </tr>
  <tr>
    <td align='center' valign='middle' class='style3'></td>
    <td width='171' align='center' valign='middle' class='style3'></td>
  </tr>
  <tr valign='top'>
   </td>
 </tr>
</table>
<?php 
break;
case 2:
$data_ini = $_REQUEST['data_ini'];
$data_fim = $_REQUEST['data_fim'];
$d = explode ("/", $data_ini);            
$data_ini_f = "$d[2]-$d[1]-$d[0]";        
$data_ini_f = $data_ini_f." 00:00:00";
$d = explode ("/", $data_fim);            
$data_fim_f = "$d[2]-$d[1]-$d[0]";        
$data_fim_f = $data_fim_f." 23:59:59";

$result = mysql_query("SELECT *,date_format(data_vencimento,'%d/%m/%Y') as data2 FROM saida where data_vencimento >= '$data_ini_f' and data_vencimento <= '$data_fim_f' and id_projeto = '$projeto' and status = '2' ORDER BY data_vencimento");
$result_pr2 = mysql_query("SELECT * from projeto where id_projeto = '$projeto' ");
$row_pr2 = mysql_fetch_array($result_pr2);
?>
<table border='0' align='center' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class="conteudo">
  <tr>
    <td colspan='2'>&nbsp;</td>
  </tr>
  <tr>
    <td colspan='2'><div align='center' class='style7'></div></td>
    </tr>
  <tr>
    <td colspan='2'><div align='center'><b>CONTROLE DE SAI&#769;DAS - <?=$row_pr2['nome']?></b></div></td>
  </tr>
  <tr>
    <td colspan='2'>
    <table width="100%" align='center' cellpadding="3" cellspacing="1" id="tableOrden">
      <thead>
      <tr bgcolor="#999999">
      	<th>&nbsp;</th>
        <th><div align='center'><font size=1><b>C�DIGO SAIDA</b></font></div></th>
        <th><div align='center'><font size=1><b>DATA DE RECEBIMENTO</b></font></div></th>
        <th><div align='center'><font size=1><b>NOME DO CR�DITO</b></font></div></th>
		<th><div align='center'><font size=1><b>ESPECIFICA��O</b></font></div></th>
        <th><div align='center'><font size=1><b>CONTA CREDITADA</b></font></div></th>
        <th><div align='center'><font size=1><b>TIPO DE SAIDA</b></font></div></th>
        <th><div align='center'><font size=1><b>GRUPO</b></font></div></th>
        <th><div align='center'><font size=1><b>CADASTRADA POR</b></font></div></th>
		<th><div align='center'><font size=1><b>PAGO POR</b></font></div></th>
        <th><div align='center'><font size=1><b>VALOR ADICIONAL</b></font></div></th>
        <th><div align='center'><font size=1><b>VALOR TOTAL</b></font></div></th>
      </tr>
      </thead>
<?php  
$cont1 = "1";
while($row = mysql_fetch_array($result)){
if($cont1 % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row[id_user]'");
$row_user = mysql_fetch_array($result_user);
$result_user_pg = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row[id_userpg]'");
$row_user_pg = mysql_fetch_array($result_user_pg);
$result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$row[id_banco]' and status_reg ='1'");
$row_banco = mysql_fetch_array($result_banco);
$result_tipo = mysql_query("SELECT * FROM entradaesaida where id_entradasaida = '$row[tipo]'");
$row_tipo = mysql_fetch_array($result_tipo);
if($row['especifica'] == ""){ $descricao = "&nbsp;"; }else{ $descricao = $row['descricao']; }
if($row['adicional'] == ""){ $adicional = "&nbsp;"; }else{ $adicional = $row['adicional']; }
unset($comprovante);
//////////////////////////////////////////////////////////////////////////////////
if($row['comprovante'] == 0){
	$comprovante = $row['nome'];
}elseif($row['comprovante'] == 1){
	$tipo = $row['tipo_arquivo'];
	if(empty($row['tipo_arquivo'])) $tipo = ".gif";
	$comprovante = '<a href="../comprovantes/'.$row['id_saida'].$tipo.'" target="_blank"> '.$row['nome'].'</a>';
}elseif($row['comprovante'] == 2){
	
	
	$comprovante = $row['nome'];
	$query = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row[0]'");
	$num_arquivos = mysql_num_rows($query);
	
	if($num_arquivos > 1){
		$cont = 1;
		while($row_files = mysql_fetch_assoc($query)){
		$arquivos[] = "<a href=\"../comprovantes/$row_files[id_saida_file].$row_files[id_saida]$row_files[tipo_saida_file]\" target='_blank' rel='lightbox' title='Anexo'>" ."arquivo $cont". "</a>";
		$cont++;
		}
		$comprovante .=  "<br>".implode("<br>",$arquivos);
	}else{
		$row_files = mysql_fetch_assoc($query);
		$comprovante = "<a href=\"../comprovantes/$row_files[id_saida_file].$row_files[id_saida]$row_files[tipo_saida_file]\" target='_blank' title='Anexo'>".$row['nome']."</a>";
	}
	
}
//////////////////////////////////////////////////////////////////////////////////

$link_encryptado = encrypt('ID='.$row[0].'&tipo=0');
$link_encryptado_pg = encrypt('ID='.$row[0].'&tipo=1');

$qr_quant = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row[0]'");
$quanti = mysql_num_rows($qr_quant);
$qr_quant2 = mysql_query("SELECT * FROM saida_files_pg WHERE id_saida = '$row[0]'");
$quanti2 = mysql_num_rows($qr_quant2);
////////////////////////////////////////
?>

<tr bgcolor=<?=$color?>> 
		<td class=border2>
        <table border="0">
            <tr>
                <td>
                <?php $href = (($id_user == '87' ? "../novoFinanceiro/view/edicao.saidas.php?ID=$row[0]" : "../novoFinanceiro/view/edicao.saidas.php?ID=$row[0]"));?>
                
               <a title="Editar saida"  href="../novoFinanceiro/cad_edit_saida.php?ID=<?=$row[0]?>" onclick="return hs.htmlExpand(this, { objectType: 'iframe', width: 580 , height: 200} )" ><img src="imagensfinanceiro/page_edit-32.png" /></a></td>
           
                
                <td>
                    <?php if(!empty($quanti)): ?>
                    <a target="_blank" title="Comprovante" href="../novoFinanceiro/view/comprovantes.php?<?=$link_encryptado?>"><img src="imagensfinanceiro/attach-32.png"  /></a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($row['comprovante'] == 1): ?>
                    <a target="_blank" title="Comprovante" href="../novoFinanceiro/view/comprovantes.php?<?=$link_encryptado?>"><img src="imagensfinanceiro/attach-32.png"  /></a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if(!empty($quanti2)): ?>
                    <a target="_blank" title="Comprovante de pagamento" href="../novoFinanceiro/view/comprovantes.php?<?=$link_encryptado_pg?>"><img src="imagensfinanceiro/attach-32.png"  /></a>
                    <?php endif; ?>
                </td>
                
            </tr>
        </table>
        </td>
        <td class=border2><font size=1 face=Arial>&nbsp;<?=$row[0]?></font></td>
        <td class=border2><font size=1 face=Arial>&nbsp;<?=$row['data2']?></font></td>
        <td class=border2><font size=1 face=Arial><?=$row['nome']?></font></td>
	<td class=border2><font size=1 face=Arial>&nbsp;<?=$row['especifica']?></font></td>
        <td class=border2><font size=1 face=Arial>AG: <?=$row_banco['agencia'] . ' / C: ' . $row_banco['conta']?></font></td>
        <td class=border2><font size=1 face=Arial>&nbsp;<?=$row_tipo['nome']?></font></td>
        <td class=border2><font size=1 face=Arial>&nbsp;<?=grupo($row_tipo['grupo'])?></font></td>
        <td class=border2><font size=1 face=Arial>&nbsp;<?=$row_user[0]?></font></td>
	<td class=border2><font size=1 face=Arial>&nbsp;<?=$row_user_pg[0]?></font></td>
        <td class=border2><font size=1 face=Arial>R$ <?=$adicional?></font></td>
        <td class=border2><font size=1 face=Arial>R$ <?= $row['valor']?></font></td>
      </tr>
<?php
$adicional = str_replace(",",".",$row['adicional']);
$valor_soma = str_replace(",",".",$row['valor']);
$valor_total2 = $valor_total2 + $valor_soma + $adicional;
$cont1 ++;
}
$valor_total2 = number_format($valor_total2,2,",",".");
?>
 
      <tr>
        <td colspan='9'><div align='right' class='style12'>TOTAL DE SAI&#769;DAS <?=$data_ini?> a <?=$data_fim?>:</div></td>
        <td ><b>R$ <?=$valor_total2?></b></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan='2' height='15' align='center' valign='top' bgcolor='#FFFFFF' class='style3'><a href='../novoFinanceiro/relatorios.php?enc=<?=$link_enc?>'>voltar</a></td>
  </tr>
  
  <tr>
    <td width='532' height='15' align='right' valign='top' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
    <td align='center' valign='middle' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
  </tr>
  <tr>
    <td align='center' valign='middle' class='style3'></td>
    <td width='171' align='center' valign='middle' class='style3'></td>
  </tr>
  </table>
<?php
break;
case 3:


$data_ini = $_REQUEST['data_ini'];
$data_fim = $_REQUEST['data_fim'];
$d = explode ("/", $data_ini);            
$data_ini_f = "$d[2]-$d[1]-$d[0]";        
$data_ini_f = $data_ini_f." 00:00:00";
$d = explode ("/", $data_fim);            
$data_fim_f = "$d[2]-$d[1]-$d[0]";        
$data_fim_f = $data_fim_f." 23:59:59";
$result = mysql_query("SELECT *,date_format(data_proc,'%d/%m/%Y') as data3 FROM caixa where data_vencimento >= '$data_ini_f' and data_vencimento <= '$data_fim_f' and id_projeto = '$projeto' ORDER BY data_vencimento");
$result_pr3 = mysql_query("SELECT * from projeto where id_projeto = '$projeto' ");
$row_pr3 = mysql_fetch_array($result_pr3);
?>
<table border='0' align='center' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class="conteudo">
  <tr>
    <td colspan='2'><div align='center' class='style7'></div></td>
    </tr>
  <tr>
    <td colspan='2'><div align='center'><b>CONTROLE DE SA�DAS DE CAIXA - <?=$row_pr3['nome']?></b></div></td>
  </tr>
  <tr>
    <td colspan='2'><table width="100%" align='center' cellpadding="3" cellspacing="1" id="tableOrden">
      <tr bgcolor="#999999">
        <th width="4%">&nbsp;</th>
        <th width="3%"><div align='center'><font size=1 face=Arial><b>C�DIGO SA�DA</b></font></div></th>
        <th width="5%"><div align='center'><font size=1 face=Arial><b>DATA</b></font></div></th>
		<th width="28%"><div align='center'><font size=1 face=Arial><b>NOME DO D�BITO</b></font></div></th>
		<th width="16%"><div align='center'><font size=1 face=Arial><b>CADASTRADA POR</b></font></div></th>
        <th width="13%"><div align='center'><font size=1 face=Arial><b>ESPECIFICA��O</b></font></div></th>
        <th width="6%"><div align='center'><font size="1" face="Arial"><b>GRUPO</b></font></div></th>
        <th width="14%"><div align='center'><font size=1 face=Arial><b>VALOR ADICIONAL</b></font></div></th>
        <th width="11%"><div align='center'><font size=1 face=Arial><b>VALOR TOTAL</b></font></div></th>
      </tr>
<?php	  
$cont1 = "1";
while($row = mysql_fetch_array($result)){
if($cont1 % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row[id_user]'");
$row_user = mysql_fetch_array($result_user);
$result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$row[id_banco]' and status_reg ='1'");
$row_banco = mysql_fetch_array($result_banco);
$result_tipo = mysql_query("SELECT * FROM entradaesaida where id_entradasaida = '$row[tipo]'");
$row_tipo = mysql_fetch_array($result_tipo);
$valor_soma = str_replace(",",".",$row['valor']);
if($row['adicional'] == ""){ $adicional = "&nbsp;"; }else{ $adicional = $row['adicional']; }
unset($comprovante);
//////////////////////////////////////////////////////////////////////////////////
if($row['comprovante'] == 0){
	$comprovante = $row['nome'];
}elseif($row['comprovante'] == 1){
	$tipo = $row['tipo_arquivo'];
	if(empty($row['tipo_arquivo'])) $tipo = ".gif";
	$comprovante = '<a href="../comprovantes/'.$row['id_saida'].$tipo.'" target="_blank"> '.$row['nome'].'</a>';
}elseif($row['comprovante'] == 2){
	
	
	$comprovante = $row['nome'];
	$query = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row[0]'");
	$num_arquivos = mysql_num_rows($query);
	
	if($num_arquivos > 1){
		$cont = 1;
		while($row_files = mysql_fetch_assoc($query)){
		$arquivos[] = "<a href=\"../comprovantes/$row_files[id_saida_file].$row_files[id_saida]$row_files[tipo_saida_file]\" target='_blank' rel='lightbox' title='Anexo'>" ."arquivo $cont". "</a>";
		$cont++;
		}
		$comprovante .=  "<br>".implode("<br>",$arquivos);
	}else{
		$row_files = mysql_fetch_assoc($query);
		$comprovante = "<a href=\"../comprovantes/$row_files[id_saida_file].$row_files[id_saida]$row_files[tipo_saida_file]\" target='_blank' title='Anexo'>".$row['nome']."</a>";
	}
	
}
//////////////////////////////////////////////////////////////////////////////////
$link_encryptado = encrypt('ID='.$row[0].'&tipo=0');
$link_encryptado_pg = encrypt('ID='.$row[0].'&tipo=1');

$qr_quant = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row[0]'");
$quanti = mysql_num_rows($qr_quant);
$qr_quant2 = mysql_query("SELECT * FROM saida_files_pg WHERE id_saida = '$row[0]'");
$quanti2 = mysql_num_rows($qr_quant2);

////////////////////////////////////////
?>
<tr bgcolor=<?=$color?>>
		<td class=border2>
        <table border="0">
            <tr>
                <td>
                       <?php 
                
                if($_COOKIE['logado'] == 87){
                  $href = (($id_user == '75' ? "../novoFinanceiro/cad_edit_saida.php?ID=$row[0]" : "../novoFinanceiro/view/edicao.saidas.php?ID=$row[0]"));
                  
                 
                  $img ="../imagens/icone_lapis.png";
                  
                } else {
                $href ='../novoFinanceiro/view/edicao.saidas.php?ID=<?='.$row[0];
                $img ="imagensfinanceiro/page_edit-32.png";
                }
                ?>
               <!--     <a title="Editar saida"  href=<?php echo $href;?>" onclick="return hs.htmlExpand(this, { objectType: 'iframe', width: 580 } )" ><img src="<?php echo $img; ?>" border="0" /></a></td>
               -->
               <a title="Editar saida"  href="../novoFinanceiro/cad_edit_saida.php?ID=<?=$row[0]?>" onclick="return hs.htmlExpand(this, { objectType: 'iframe', width: 580 } )" ><img src="imagensfinanceiro/page_edit-32.png" /></a></td>
           
                
                <td>
                    <?php if(!empty($quanti)): ?>
                    <a target="_blank" title="Comprovante" href="../novoFinanceiro/view/comprovantes.php?<?=$link_encryptado?>"><img src="imagensfinanceiro/attach-32.png"  /></a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($row['comprovante'] == 1): ?>
                    <a target="_blank" title="Comprovante" href="../novoFinanceiro/view/comprovantes.php?<?=$link_encryptado?>"><img src="imagensfinanceiro/attach-32.png"  /></a>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if(!empty($quanti2)): ?>
                    <a target="_blank" title="Comprovante de pagamento" href="../novoFinanceiro/view/comprovantes.php?<?=$link_encryptado_pg?>"><img src="imagensfinanceiro/attach-32.png"  /></a>
                    <?php endif; ?>
                </td>
                
            </tr>
        </table>
        </td>
        <td class=border2><font size=1 face=Arial>&nbsp;<?=$row[0]?></font></td>
        <td class=border2><font size=1 face=Arial>&nbsp;<?=$row['data3']?></font></td>
		<td class=border2><font size=1 face=Arial>&nbsp;<?=$row['nome']?></font></td>
        <td class=border2><font size=1 face=Arial>&nbsp;<?=$row_user[0]?></font></td>
		<td class=border2><font size=1 face=Arial>&nbsp;<?=$row['descricao']?></font></td>
		<td class=border2>&nbsp;<font size=1 face=Arial><?=grupo($row['grupo'])?></font></td>
        <td class=border2><font size=1 face=Arial>R$ <?=$adicional?></font></td>
        <td class=border2><font size=1 face=Arial>R$ <?=  $row['valor']?></font></td>
      </tr>
<?php
$adicional = str_replace(",",".", $adicional);
$valor_total3 = $valor_total3 + $valor_soma + $adicional;
$cont1 ++;
}
$valor_total3 = number_format($valor_total3,2,",",".");
?>
      <tr>
        <td colspan='8'><div align='right' class='style12'>TOTAL DE SA�DAS DO CAIXINHA <?=$data_ini?> a <?=$data_fim?>:</div></td>
        <td ><b>R$ <?=$valor_total3?></b></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan='2' height='15' align='center' valign='top' bgcolor='#FFFFFF' class='style3'><a href='../novoFinanceiro/relatorios.php?enc=<?=$link_enc?>'>voltar</a></td>
  </tr>
  
  <tr>
    <td width='532' height='15' align='right' valign='top' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
    <td align='center' valign='middle' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
  </tr>
  <tr>
    <td align='center' valign='middle' class='style3'></td>
    <td width='171' align='center' valign='middle' class='style3'></td>
  </tr>
  </table>
<?php
break;
case 4: //                                    RELAT�RIO DE ENTRADAS E SAIDAS
$banco = $_REQUEST['banco'];
$mes = $_REQUEST['mes'];
$ano = $_REQUEST['ano'];
if(empty($_REQUEST['todas_contas'])){
$todas_contas = "0";
}else{
$todas_contas = $_REQUEST['todas_contas'];
}
$data_ini_f = $ano."-".$mes."-01";
$data_fim_f = $ano."-".$mes."-31"." 23:59:59";
if(empty($_REQUEST['todas_contas'])){
    
    //antes era ordenado por order by data_vencimento, data_proc, id_banco
$result1 = mysql_query("SELECT *,date_format(data_vencimento,'%d/%m/%Y') as data2 FROM entrada where data_vencimento >= '$data_ini_f' and data_vencimento <= '$data_fim_f' and id_banco = '$banco' and status = '2' order by data_vencimento, id_banco");
$result2 = mysql_query("SELECT *,date_format(data_vencimento,'%d/%m/%Y') as data2 FROM saida where data_vencimento >= '$data_ini_f' and data_vencimento <= '$data_fim_f' and id_banco = '$banco' and status = '2' order by data_vencimento, id_banco");

}else{
$result1 = mysql_query("SELECT *,date_format(data_vencimento,'%d/%m/%Y') as data2 FROM entrada 
where data_vencimento >= '$data_ini_f' and data_vencimento <= '$data_fim_f' and id_regiao = '$regiao' and status = '2' order by data_vencimento, id_banco ASC");

$result2 = mysql_query("SELECT *,date_format(data_vencimento,'%d/%m/%Y') as data2 FROM saida 
where data_vencimento >= '$data_ini_f' and data_vencimento <= '$data_fim_f' and id_regiao = '$regiao' and status = '2' order by data_vencimento, id_banco ASC");


}
$result_pr5 = mysql_query("SELECT * from projeto where id_projeto = '$projeto' ");
$row_pr5 = mysql_fetch_array($result_pr5);
?>

<table border='0' align='center' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class="conteudo">
  <tr>
    <td colspan='2'><div align='center' class='style7'></div></td>
    </tr>
  <tr>
    <td colspan='2'><div align='center'><b>CONTROLE DE ENTRADAS</b></div></td>
  </tr>
  <tr>
    <td colspan='2'>
	<table width="100%" align='center' cellpadding="3" cellspacing="1" id="tableOrden">
      <tr bgcolor="#999999">
       <th>&nbsp;</th>
       <th><div align='center'><font size=1 face=Arial><b>C�DIGO ENTRADA</b></font></div></th>
       <th><div align='center'><font size=1 face=Arial><b>DATA DE </b></font></div></th>
       <th><div align='center'><font size=1 face=Arial><b>NOME DO CR�DITO</b></font></div></th>
       <th><div align='center'><font size=1 face=Arial><b>CONTA CREDITADA</b></font></div></th>
       <th><div align='center'><font size=1 face=Arial><b>TIPO DE ENTRADA</b></font></div></th>
       <th><div align='center'><font size=1 face=Arial><b>DESCRI��O</b></font></div></th>
       <th><div align='center'><font size=1 face=Arial><b>CADASTRADA POR</b></font></div></th>
       <th><div align='center'><font size=1 face=Arial><b>VALOR ADICIONAL</b></font></div></th>
       <th><div align='center'><font size=1 face=Arial><b>VALOR TOTAL</b></font></div></th>
      </tr>
<?php	  
$cont1 = "1";
while($row1 = mysql_fetch_array($result1)){
if($cont1 % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row1[id_user]'");
$row_user = mysql_fetch_array($result_user);
$result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$row1[id_banco]' and status_reg ='1'");
$row_banco = mysql_fetch_array($result_banco);
$result_tipo = mysql_query("SELECT * FROM entradaesaida where id_entradasaida = '$row1[tipo]'");
$row_tipo = mysql_fetch_array($result_tipo);
$valor_soma = str_replace(",",".",$row1['valor']);
$adicional = str_replace(",",".",$row1['adicional']);
unset($comprovante);
//////////////////////////////////////////////////////////////////////////////////
if($row1['comprovante'] == 0){
	$comprovante = $row1['nome'];
}elseif($row1['comprovante'] == 1){
	$tipo = $row1['tipo_arquivo'];
	if(empty($row1['tipo_arquivo'])) $tipo = ".gif";
	$comprovante = '<a href="../comprovantes/'.$row1['id_saida'].$tipo.'" target="_blank"> '.$row1['nome'].'</a>';
}elseif($row1['comprovante'] == 2){
	
	
	$comprovante = $row1['nome'];
	$query = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row1[0]'");
	$num_arquivos = mysql_num_rows($query);
	
	if($num_arquivos > 1){
		$cont = 1;
		while($roW_files = mysql_fetch_assoc($query)){
		$arquivos[] = "<a href=\"../comprovantes/$row_files[id_saida_file].$row_files[id_saida]$row_files[tipo_saida_file]\" target='_blank' rel='lightbox' title='Anexo'>" ."arquivo $cont". "</a>";
		$cont++;
		}
		$comprovante .=  "<br>".implode("<br>",$arquivos);
	}else{
		$row_files = mysql_fetch_assoc($query);
		$comprovante = "<a href=\"../comprovantes/$row_files[id_saida_file].$row_files[id_saida]$row_files[tipo_saida_file]\" target='_blank' title='Anexo'>".$row1['nome']."</a>";
	}
	
}
//////////////////////////////////////////////////////////////////////////////////
?>
<tr bgcolor='<?=$color?>'> 
    
    
		<td>
        	<a title="Editar Entrada"  href="../novoFinanceiro/view/editar.entrada.php?id_entrada=<?=$row1[0];?>" onclick="return hs.htmlExpand(this, { objectType: 'iframe', width: 580 } )" ><img src="imagensfinanceiro/page_edit-32.png" /></a>
        	<?php
                if($row1['tipo'] == '12'):
                    $qr_notas = mysql_query("SELECT notas_files.id_file, notas_files.tipo, notas_files.id_notas FROM (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
                                            INNER JOIN notas_files ON notas.id_notas = notas_files.id_notas 
                                            WHERE notas_assoc.id_entrada = '$row1[id_entrada]' GROUP BY notas_files.id_file;") or die(mysql_error());

                    $num_notas = mysql_num_rows($qr_notas);
                    $row_notas = mysql_fetch_assoc($qr_notas);
                    if (!empty($num_notas)): ?>
                        <a target="_blank" href="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/intranet/adm/adm_notas/visializa_files.php?id_nota='.$row_notas['id_notas']; ?>&entrada=<?php echo $row1['id_entrada'];?>">
                                <img src="../financeiro/imagensfinanceiro/attach-32.png" width="16" height="16"  border="0"/>
                        </a>
                    <?php 
                    endif;		
                endif;

                $qr_entrada_files = mysql_query("SELECT * FROM entrada_files WHERE id_entrada = '$row1[id_entrada]' AND status = '1'");
                while($row_entrada_files = mysql_fetch_assoc($qr_entrada_files)): ?>
                <a href="../novoFinanceiro/comprovantes/entrada/<?=$row_entrada_files['id_files'].$row_entrada_files['tipo_files']?>" target="_blank">
                            <img src="../financeiro/imagensfinanceiro/attach-32.png" width="16" height="16"  border="0"/>
                </a>
                <?php endwhile; ?>
        </td>
        <td class=border2><font size='1' face=Arial>&nbsp;<?=$row1[0]?></font></td>
        <td class=border2><font size='1' face=Arial>&nbsp;<?=$row1['data2']?></font></td>
        <td class=border2><font size='1' face=Arial>&nbsp;<?=$row1['nome']?></font></td>
        <td class=border2><font size='1' face=Arial>AG: <?=$row_banco['agencia']?> / C: <?=$row_banco['conta']?></font></td>
        <td class=border2><font size='1' face=Arial>&nbsp;<?=$row_tipo['nome']?></font></td>
        <td class=border2><font size='1' face=Arial>&nbsp;<?=$row1['especifica']?></font></td>
        <td class=border2><font size='1' face=Arial>&nbsp;<?=$row_user[0]?></font></td>
        <td class=border2><font size='1' face=Arial>R$ <?=$row1['adicional']?></font></td>
        <td class=border2><font size='1' face=Arial>R$ <?=$row1['valor']?></font></td>
      </tr>
<?php
$valor_total_banco1 = $valor_total_banco1 + $valor_soma + $adicional;
$cont1 ++;
}
$valor_total_banco1_f = number_format($valor_total_banco1,2,",",".");
?>
      <tr>
        <td colspan='8'><div align='right' class='style12'>TOTAL DE ENTRADAS <?=$data_ini?> a <?=$data_fim?>:</div></td>
        <td><b>R$ <?=$valor_total_banco1_f?> </b></td>
        </tr>
    </table>
	
	<br><hr><br>
	
<table width="100%" align='center' cellpadding='0' cellspacing='0' id="tableOrden">
  <tr>
    <td colspan='12'><div align='center'><b>CONTROLE DE SAIDA </b></div></td>
  </tr>
      <tr bgcolor="#999999">
        <th >&nbsp;</th>
       <th ><div align='center'><font size=1 face=Arial>C�DIGO SA�DA</font></div></th>
       <th><div align='center'><font size=1 face=Arial>DATA DE RECEBIMENTO</font></div></th>
       <th><div align='center'><font size=1 face=Arial>NOME DO CR�DITO</font></div></th>
       <th><div align='center'><font size=1 face=Arial>CONTA DEBITADA</font></div></th>
       <th><div align='center'><font size=1 face=Arial>TIPO DE SA�DA</font></div></th>
       <th><div align='center'><font size=1 face=Arial>GRUPO</font></div></th>
       <th><div align='center'><font size=1 face=Arial>SUBGRUPO</font></div></th>
       <th><div align='center'><font size=1 face=Arial>DESCRI��O</font></div></th>
       <th><div align='center'><font size=1 face=Arial>CADASTRADA POR</font></div></th>
	   <th><div align='center'><font size=1 face=Arial>PAGO POR</font></div></th>
       <th><div align='center'><font size=1 face=Arial>VALOR ADICIONAL</font></div></th>
       <th><div align='center'><font size=1 face=Arial>VALOR TOTAL</font></div></th>
      </tr>
<?php  

///aqui
$cont2 = "1";
while($row2 = mysql_fetch_array($result2)){
if($cont2 % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row2[id_user]'");
$row_user = mysql_fetch_array($result_user);
$result_userpg = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row2[id_userpg]'");
$row_userpg = mysql_fetch_array($result_userpg);
$result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$row2[id_banco]' and status_reg ='1'");
$row_banco = mysql_fetch_array($result_banco);
$result_tipo = mysql_query("SELECT * FROM entradaesaida where id_entradasaida = '$row2[tipo]'");
$row_tipo = mysql_fetch_array($result_tipo);

$qr_subgrupo = mysql_query("SELECT * FROM entradaesaida_subgrupo WHERE id='$row2[entradaesaida_subgrupo_id]'");
$row_subgrupo = mysql_fetch_assoc($qr_subgrupo);

$valor_soma = str_replace(",",".",$row2['valor']);
$adicional = str_replace(",",".",$row2['adicional']);
//////////////////////////////////////////////////////////////////////////////////
unset($comprovante);

if($row2['comprovante'] == 1){
	$tipo = $row2['tipo_arquivo'];
	if(empty($row2['tipo_arquivo'])) $tipo = ".gif";
	$comprovante = '<a href="../comprovantes/'.$row2['id_saida'].$tipo.'" target="_blank"> '.$row2['nome'].'</a>';
}elseif($row2['comprovante'] == 2){	
	$comprovante = $row2['nome'];
	$query = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row2[0]'");
	$num_arquivos = mysql_num_rows($query);
	
	if($num_arquivos > 1){
		$cont = 1;
		while($row_files = mysql_fetch_assoc($query)){
		$arquivos[] = "<a href=\"../comprovantes/$row_files[id_saida_file].$row_files[id_saida]$row_files[tipo_saida_file]\" target='_blank' rel='lightbox' title='Comprovante'>" ."arquivo $cont". "</a>";
		$cont++;
		}
		$comprovante .=  "<br>".implode("<br>",$arquivos);
	}else{
		$row_files = mysql_fetch_assoc($query);
		$comprovante = "<a href=\"../comprovantes/$row_files[id_saida_file].$row_files[id_saida]$row_files[tipo_saida_file]\" target='_blank' title='Comprovante'>".$row2['nome']."</a>";
	}
	
}else{
	$comprovante = $row2['nome'];
}
//////////////////////////////////////////////////////////////////////////////////
/*if($row2['comprovante'] == "1"){
	$link_ima = "<a href='../comprovantes/$row2[0]".$row2['tipo_arquivo']."' target='_blank' rel='lightbox' title='Anexo'>$row2[nome]</a>";
}elseif($row2['comprovante'] == "2"){
	$query = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row2[0]'");
	while($row_files = mysql_fetch_assoc($query)){
			$link_ima = "<p><a href='../comprovantes/$row_files[id_saida_file].$row_files[id_saida]$row_files[tipo_saida_file]' target='_blank' title='Anexo'>$row2[nome]<a/></p>";
	}
}else{
$link_ima = "$row2[nome]";
}*/
$link_encryptado = encrypt('ID='.$row2[0].'&tipo=0');
$link_encryptado_pg = encrypt('ID='.$row2[0].'&tipo=1');

$qr_quant = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row2[0]'");
$quanti = mysql_num_rows($qr_quant);
$qr_quant2 = mysql_query("SELECT * FROM saida_files_pg WHERE id_saida = '$row2[0]'");
$quanti2 = mysql_num_rows($qr_quant2);


?>
<tr bgcolor=<?=$color?>>
  <td class=border2>
  	<table border="0">
    	<tr>
        	<td>
                 <?php
                 ////ESTE ARRAY CONT�M OS TIPOS DE SA�DA QUE S� PODEM SER EDITADO A DATA DE VENCIMENTO Ex: RESCIS�ES QUE VEM DOS "PAGAMENTOS" NA 
                    //GEST�O DE RH
                    
                 
                    // 171,175 LIBEREI PARA EDI��O ESSES 2 ITENS POIS S�O CADASTRADOS PELO FINANCEIRO RAMON 22/01/2014
                    // 171 FOI TRAVADA NOVAMENTE POIS O FINANCEIRO N�O SABE O Q QUER... RAMON 07/03/2014
                     $array_nao_editaveis = array(168,167,169, 170,156,154,32, 31, 30,29, 260, 171); 
                 
                    if($row2['tipo'] > 4 ){
                        
                                if(!in_array($row2['tipo'],$array_nao_editaveis)){
                                 ?> 
                                    <a title="Editar saida"  href="../novoFinanceiro/cad_edit_saida.php?id=<?=$row2[0]?>&rel&keepThis=true&TB_iframe=true&width=800&height=600" class="thickbox" ><img src="../imagens/icone_lapis.png" width="15" heigth="15"/></a></td>
                                  <?php }else { ?>
                                    <a title="Editar saida"  href="../novoFinanceiro/editar_data.php?id=<?=$row2[0]?>&rel" onclick="return hs.htmlExpand(this, { objectType: 'iframe', width: 650, height: 180 } )" ><img src="../imagens/icone_lapis.png" width="15" heigth="15"/></a></td>
                                <?php }
                       
                    }  else {?>
                                 <a title="Editar saida"  href="../novoFinanceiro/view/edicao.saidas.php?ID=<?=$row2[0]?>" onclick="return hs.htmlExpand(this, { objectType: 'iframe', width: 580 } )" ><img src="imagensfinanceiro/page_edit-32.png" /></a></td>
                                           
                    

                        <?php } 
                        
                          ?>
    
    
                <td>
            	<?php if(!empty($quanti)): ?>
            	<a target="_blank" title="Comprovante" href="../novoFinanceiro/view/comprovantes.php?<?=$link_encryptado?>"><img src="imagensfinanceiro/attach-32.png"  /></a>
            	<?php endif; ?>
            </td>
            <td>
            	<?php if($row2['comprovante'] == 1): ?>
            	<a target="_blank" title="Comprovante" href="../novoFinanceiro/view/comprovantes.php?<?=$link_encryptado?>"><img src="imagensfinanceiro/attach-32.png"  /></a>
            	<?php endif; ?>
            </td>
            <td>
            	<?php if(!empty($quanti2)): ?>
           		<a target="_blank" title="Comprovante de pagamento" href="../novoFinanceiro/view/comprovantes.php?<?=$link_encryptado_pg?>"><img src="imagensfinanceiro/attach-32.png"  /></a>
            	<?php endif; ?>
            </td>
            
        </tr>
    </table>
  </td> 
        <td class=border2><font size='1' face=Arial>&nbsp;<?=$row2[0]?></font></td>
        <td class=border2><font size='1' face=Arial>&nbsp;<?=$row2['data2']?></font></td>
        <td class=border2><font size='1' face=Arial><?=$row2['nome']?></font></td>
        <td class=border2><font size='1' face=Arial>AG: <?=$row_banco['agencia']?> / C: <?=$row_banco['conta']?></font></td>
        <td class=border2><font size='1' face=Arial>&nbsp;<?=$row_tipo['nome']?></font></td>
        <td class=border2><font size='1' face=Arial>&nbsp;<?=grupo($row_tipo['grupo'])?></font></td>
        <td class=border2><font size='1' face=Arial>&nbsp;<?=$row_subgrupo['nome']?></font></td>
        <td class=border2><font size='1' face=Arial><?=$row2['especifica']?></font></td>
        <td class=border2><font size='1' face=Arial>&nbsp;&nbsp;<?=$row_user[0]?></font></td>
        <td class=border2><font size='1' face=Arial>&nbsp;<?=$row_userpg[0]?></font></td>
        <td class=border2><font size='1' face=Arial><?=$row2['adicional']?></font></td>
        <td class=border2><font size='1' face=Arial><?=$row2['valor']?></font></td>
      </tr>
<?php
$valor_total_banco2 = $valor_total_banco2 + $valor_soma + $adicional;
$cont2 ++;
}
$valor_total_banco2_f = number_format($valor_total_banco2,2,",",".");
$valor_total_banco = "$valor_total_banco1" - "$valor_total_banco2";
$valor_total_banco_f = number_format($valor_total_banco,2,",",".");
?>
      <tr>
        <td colspan='11'><div align='right' class='style12'>TOTAL DE SA�DAS</div></td>
        <td>R$ <?=$valor_total_banco2_f?></td>
        </tr>
    </table>
	
	
	
	</td>
  </tr>
  <tr>
    <td colspan='2' height='15' align='center' valign='top' bgcolor='#FFFFFF' class='style3'>
	<br>
	<font color=#000000 size=3>
	Total: R$ <?=$valor_total_banco_f?>
	</font>
	</td>
  </tr>
  <tr>
    <td colspan='2' height='15' align='center' valign='top' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
  </tr>
  <tr>
    <td colspan='2' height='15' align='center' valign='top' bgcolor='#FFFFFF' class='style3'><a href='../novoFinanceiro/relatorios.php?enc=<?=$link_enc?>'>voltar</a></td>
  </tr>
  <tr>
    <td width='532' height='15' align='right' valign='top' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
    <td align='center' valign='middle' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
  </tr>
  <tr>
    <td align='center' valign='middle' class='style3'></td>
    <td width='171' align='center' valign='middle' class='style3'></td>
  </tr>
  </table>
<?php 
break;
case 5:
$data_now = date('Y-m-d');
$tipo_select = $_REQUEST['select'];
if($tipo_select == "1"){            //Visualizar lan�amentos n�o pagos
    $result = mysql_query("SELECT *,date_format(data_vencimento,'%d/%m/%Y') as data2 FROM saida where id_projeto = '$projeto' and status = '1' ORDER BY data_vencimento") or die ("Erro de digita��o na query1" . mysql_error());
}else{
    $result = mysql_query("SELECT *,date_format(data_vencimento,'%d/%m/%Y') as data2 FROM saida where id_projeto = '$projeto' and status = '1' and data_vencimento > '$data_now' ORDER BY data_vencimento") or die ("Erro de digita��o na query2" . mysql_error());
}
?>
<table border='0' align='center' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class="conteudo">
  <tr>
    <td colspan='2'><div align='center' class='style7'></div></td>
    </tr>
  <tr>
    <td colspan='2'><div align='center'><b>CONTROLE DE SAI&#769;DAS</b></div></td>
  </tr>
  <tr>
    <td colspan='2'><table width="100%" align='center' id="tableOrden">
      <tr bgcolor="#999999">
      	<th>&nbsp;</th>
      <th><div align='center'><font size=1 face=Arial>C�DIGO ENTRADA</font></div></th>
       <th><div align='center'><font size=1 face=Arial>DATA DE RECEBIMENTO</font></div></th>
        <th><div align='center'><font size=1 face=Arial>NOME DO CR�DITO</font></div></th>
        <th><div align='center'><font size=1 face=Arial>CONTA CREDITADA</font></div></th>
        <th><div align='center'><font size=1 face=Arial>TIPO DE SAIDA</font></div></th>
        <th><div align='center'><font size="1" face="Arial">GRUPO</font></div></th>
        <th><div align='center'><font size=1 face=Arial>DESCRI��O</font></div></th>
        <th><div align='center'><font size=1 face=Arial>CADASTRADA POR</font></div></th>
        <th><div align='center'><font size=1 face=Arial>VALOR ADICIONAL</font></div></th>
        <th><div align='center'><font size=1 face=Arial>VALOR TOTAL</font></div></th>
      </tr>
<?php 	  
$cont1 = "1";
while($row = mysql_fetch_array($result)){
if($cont1 % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row[id_user]'");
$row_user = mysql_fetch_array($result_user);
$result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$row[id_banco]' and status_reg ='1'");
$row_banco = mysql_fetch_array($result_banco);
$result_tipo = mysql_query("SELECT * FROM entradaesaida where id_entradasaida = '$row[tipo]'");
$row_tipo = mysql_fetch_array($result_tipo);
if($row['especifica'] == ""){ $descricao = "&nbsp;"; }else{ $descricao = $row['descricao']; }
if($row['adicional'] == ""){ $adicional = "&nbsp;"; }else{ $adicional = $row['adicional']; }
//////////////////////////////////////////////////////////////////////////////////
if($row['comprovante'] == 0){
	$comprovante = $row['nome'];
}elseif($row['comprovante'] == 1){
	$tipo = $row['tipo_arquivo'];
	if(empty($row['tipo_arquivo'])) $tipo = ".gif";
	$comprovante = '<a href="../comprovantes/'.$row['id_saida'].$tipo.'" target="_blank"> '.$row['nome'].'</a>';
}elseif($row['comprovante'] == 2){
	
	
	$comprovante = $row['nome'];
	$query = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row[0]'");
	$num_arquivos = mysql_num_rows($query);
	
	if($num_arquivos > 1){
		$cont = 1;
		while($row_files = mysql_fetch_assoc($query)){
		$arquivos[] = "<a href=\"../comprovantes/$row_files[id_saida_file].$row_files[id_saida]$row_files[tipo_saida_file]\" target='_blank' rel='lightbox' title='Anexo'>" ."arquivo $cont". "</a>";
		$cont++;
		}
		$comprovante .=  "<br>".implode("<br>",$arquivos);
	}else{
		$row_files = mysql_fetch_assoc($query);
		$comprovante = "<a href=\"../comprovantes/$row_files[id_saida_file].$row_files[id_saida]$row_files[tipo_saida_file]\" target='_blank' title='Anexo'>".$row['nome']."</a>";
	}
	
}
//////////////////////////////////////////////////////////////////////////////////
$link_encryptado = encrypt('ID='.$row[0].'&tipo=0');
$link_encryptado_pg = encrypt('ID='.$row[0].'&tipo=1');

$qr_quant = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row[0]'");
$quanti = mysql_num_rows($qr_quant);
$qr_quant2 = mysql_query("SELECT * FROM saida_files_pg WHERE id_saida = '$row[0]'");
$quanti2 = mysql_num_rows($qr_quant2);
/////////////////////////////////////////////
?>
<tr bgcolor=<?=$color?>>
<td class=border2>
  	<table border="0">
    	<tr>
        	<td>
                    
            <!--<a title="Editar saida"  href="../novoFinanceiro/view/edicao.saidas.php?ID=<?=$row[0]?>" onclick="return hs.htmlExpand(this, { objectType: 'iframe', width: 580 } )" ><img src="imagensfinanceiro/page_edit-32.png" /></a></td>
            -->
            <a title="Editar saida"  href="../novoFinanceiro/cad_edit_saida.php?id=<?=$row[0]?>" onclick="return hs.htmlExpand(this, { objectType: 'iframe', width: 580, height:200 } )" ><img src="imagensfinanceiro/page_edit-32.png" /></a></td>
           
                    <td>
            	<?php if(!empty($quanti)): ?>
            	<a target="_blank" title="Comprovante" href="../novoFinanceiro/view/comprovantes.php?<?=$link_encryptado?>"><img src="imagensfinanceiro/attach-32.png"  /></a>
            	<?php endif; ?>
            </td>
            <td>
            	<?php if($row['comprovante'] == 1): ?>
            	<a target="_blank" title="Comprovante" href="../novoFinanceiro/view/comprovantes.php?<?=$link_encryptado?>"><img src="imagensfinanceiro/attach-32.png"  /></a>
            	<?php endif; ?>
            </td>
            <td>
            	<?php if(!empty($quanti2)): ?>
           		<a target="_blank" title="Comprovante de pagamento" href="../novoFinanceiro/view/comprovantes.php?<?=$link_encryptado_pg?>"><img src="imagensfinanceiro/attach-32.png"  /></a>
            	<?php endif; ?>
            </td>
            
        </tr>
    </table>
  </td> 
 
        <td class=border2><font size=1 face=Arial>&nbsp;<?=$row[0]?></font></td>
        <td class=border2><font size=1 face=Arial>&nbsp;<?=$row['data2']?></font></td>
        <td class=border2><font size=1 face=Arial>&nbsp;<?=$row['nome']?></font></td>
        <td class=border2><font size=1 face=Arial>AG: <?=$row_banco['agencia']?> / C: <?=$row_banco['conta']?></font></td>
        <td class=border2><font size=1 face=Arial>&nbsp;<?=$row_tipo['nome']?></font></td>
        <td class=border2>&nbsp;<?=grupo($row_tipo['grupo'])?></td>
        <td class=border2><font size=1 face=Arial>&nbsp;<?=$descricao?></font></td>
        <td class=border2><font size=1 face=Arial>&nbsp;<?=$row_user[0]?></font></td>
        <td class=border2><font size=1 face=Arial>R$ <?=$adicional?></font></td>
        <td class=border2><font size=1 face=Arial>R$ <?=$row['valor']?></font></td>
      </tr>
<?php 
$valor_soma = str_replace(",",".",$row['valor']);
$adicional = str_replace(",",".",$row['adicional']);
$valor_total2 = $valor_total2 + $valor_soma + $adicional;
$cont1 ++;
}
$valor_total2 = number_format($valor_total2,2,",",".");
?>
      <tr>
        <td colspan='9'><div align='right' class='style12'>TOTAL DE ENTRADAS <?=$data_ini?> a <?=$data_fim?>:</div></td>
        <td>R$ <?=$valor_total2?></td>
        </tr>
    </table></td>
  </tr>
  
  <tr>
    <td width='532' height='15' align='right' valign='top' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
    <td align='center' valign='middle' bgcolor='#FFFFFF' class='style3'>&nbsp;</td>
  </tr>
  <tr>
    <td align='center' valign='middle' class='style3'></td>
    <td width='171' align='center' valign='middle' class='style3'></td>
  </tr>
  </table>
<?php
break;
}
/* Liberando o resultado */
/* Fechando a conex�o */
mysql_close($conn);

?>
</div>