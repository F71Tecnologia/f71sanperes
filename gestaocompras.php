<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";
$regiao = $_REQUEST['regiao'];

$id_user = $_COOKIE['logado'];
$result_user_logado = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'", $conn);
$row_user_logado = mysql_fetch_array($result_user_logado);
$regiao = $row_user_logado['id_regiao'];

$nome_regiao = mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao'"),0);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user_logado[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);

$verifica = $row_user_logado['tipo_usuario'];

if($verifica == "1" or $verifica == "2"){		
$class_3 = "";
}else{
$class_3 = "style='display:none'";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="adm/css/estrutura.css" rel="stylesheet" type="text/css">

<title>Intranet - GEST&Atilde;O DE COMPRAS</title>
<link href="js/highslide.css" rel="stylesheet" type="text/css"  /> 
<script type="text/javascript" src="js/highslide-with-html.js"></script> 
<script type="text/javascript" src="jquery-1.3.2.js"></script> 
<script type="text/javascript"> 
    hs.graphicsDir = 'images-box/graphics/';
    hs.outlineType = 'rounded-white';
</script>
<style>
.highslide-html-content {
	display: none;
	width: 800px;
	padding: 0 5px 5px 5px;
}
</style>
</head>

<body>

<div id="corpo">
	<div id="conteudo">
    
      <img src="imagens/logomaster<?php echo $row_master['id_master']; ?>.gif"/>
      
  	  <h3>    GESTÃO DE COMPRAS <br />
     		REGIÃO:  <?php echo $nome_regiao; ?>
      			
      </h3>
      
	  <div align="left">
	       <strong>
				<a href='compras/solicitacompra.php?regiao=<?php echo $regiao?>' style='TEXT-DECORATION: none;' onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )" >
		        <BR />
		 		 <img src="http://www.netsorrindo.com/intranet/img_menu_principal/compras.png" width="25" height="25" align="absmiddle" border="0"/>
		  		 SOLICITAR COMPRA
	       
	        </a> </strong>
	      </div>
  
    <?php
	
$qr_acompanhamento = mysql_query("SELECT *
								FROM acompanhamento_compra
								INNER JOIN func_acompanhamento_assoc ON func_acompanhamento_assoc.id_acompanhamento = acompanhamento_compra.acompanhamento_id
								WHERE acompanhamento_compra.status =1
								AND acompanhamento_id <8
								AND func_acompanhamento_assoc.id_funcionario = '$_COOKIE[logado]' ORDER BY  acompanhamento_id");
								
	while($row_acomp = mysql_fetch_assoc($qr_acompanhamento)):
				
		$result_1 = mysql_query("SELECT *,date_format(data_requisicao, '%d/%m/%Y')as data_requisicao FROM compra
		WHERE status_requisicao != '0' and acompanhamento = '$row_acomp[acompanhamento_id]' and id_regiao = '$regiao'");
		
		
		if(mysql_num_rows($result_1) !=0){
						
if($row_acomp["acompanhamento_id"] < 3) {
		?>
         
		     <h3 class="titulo_projeto"><?php echo $row_acomp['acompanhamento_nome'];?></h3>
		      <table width="100%" class="relacao">
		      
		      <tr class="titulo_tabela1">
		        <td width="16%">N. REQUISI&Ccedil;&Atilde;O</td>
		        <td width="8%">DATA</td>
		        <td width="15%" >TIPO</td>
		        <td width="29%" >NOME</td>
		        <td width="15%">SOLICITADO POR:</td>
		        <td width="17%" >VALOR</td>
		       </tr>
		<?php
				while($row_1 = mysql_fetch_array($result_1)){
				
				$result_user1 = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = $row_1[id_user_pedido]");
				$row_user1 = mysql_fetch_array($result_user1);
				
				if($row_1['tipo'] == "1"){ $tipo="PRODUTO"; }else{ $tipo="SERVIÇO"; }
				if($cont_color2 % 2){ $class = "class=linha_um"; }else{ $class = "class=linha_dois"; }
				
				print "
				<tr $class>
		        <td><a href=$row_acomp[pagina]?id=1&compra=$row_1[0]&regiao=$regiao>$row_1[num_processo]
				</a></td>
		        <td>$row_1[data_requisicao]</td>
				<td>$tipo</td>
		        <td>$row_1[nome_produto]</td>
		        <td>$row_user1[0]</td>
		        <td>R$ $row_1[valor_medio]</td>
				</tr>";
				
				$cont_color1 ++;
				
			 												 }
															 
}else{ ?>
	 <h3 class="titulo_projeto"><?php echo $row_acomp['acompanhamento_nome'];?></h3>
		      <table width="100%" class="relacao">
		      
		      <tr class="titulo_tabela1">
                <td width="9%">N. EDITAL</td>
		        <td width="16%">N. REQUISI&Ccedil;&Atilde;O</td>
		        <td width="8%">DATA</td>
		        <td width="15%" >TIPO</td>
		        <td width="29%" >NOME</td>
		        <td width="15%">SOLICITADO POR:</td>
		        <td width="17%" >VALOR</td>
                <td width="8%" >EDITAL</td>
                <td width="8%" >CHAMAMENTO</td>
		       </tr>
		<?php
				while($row_1 = mysql_fetch_array($result_1)){
				
				
				
				$result_user1 = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = $row_1[id_user_pedido]");
				$row_user1 = mysql_fetch_array($result_user1);
				
				$result_edital1 = mysql_query("SELECT * FROM anexo_chamamento where compras_id = $row_1[id_compra] AND tipo =1");
				$row_edital1 = mysql_fetch_array($result_edital1);
				
				$result_edital2 = mysql_query("SELECT * FROM anexo_chamamento where compras_id = $row_1[id_compra]  AND tipo =2");
				$row_edital2 = mysql_fetch_array($result_edital2);
				
				if($row_1['tipo'] == "1"){ $tipo="PRODUTO"; }else{ $tipo="SERVIÇO"; }
				if($cont_color2 % 2){ $class = "class=linha_um"; }else{ $class = "class=linha_dois"; }
				
				if ($row_acomp["acompanhamento_id"] <> 4){
				
				print "
				<tr $class>
				<td>$row_1[nedital]</td>
		        <td><a href=$row_acomp[pagina]?id=1&compra=$row_1[0]&regiao=$regiao>$row_1[num_processo]
				</a></td>
				
		        <td>$row_1[data_requisicao]</td>
				<td>$tipo</td>
		        <td>$row_1[nome_produto]</td>
		        <td>$row_user1[0]</td>
		        <td>R$ $row_1[valor_medio]</td>";
				if (mysql_num_rows($result_edital1) == 0)
				{print "<td>&nbsp;</td>";}
				else
				{
			    print "<td><a href='./anexo_edital/$row_edital1[nome_arquivo]'><img src='./imagens/download.png'></a></td>";
				}
				
				if (mysql_num_rows($result_edital2) == 0)
				{print "<td>&nbsp;</td>";}
				else
				{
			    print "<td><a href='./anexo_chamamento/$row_edital2[nome_arquivo]'><img src='./imagens/download.png'></a></td>";
				}				
				
				print "</tr>";
				
				
				
				$cont_color1 ++;
				
							}else{
								$consultaprest = mysql_query("SELECT * FROM prestadorservico WHERE id_compra = ".$row_1['id_compra']);
								$contprest = mysql_num_rows($consultaprest);
								
				print "
				<tr $class>
				<td>$row_1[nedital]</td>";
				if ($contprest == 0) {
		        echo "<td><a href=$row_acomp[pagina]?id=1&compra=$row_1[0]&regiao=$regiao>$row_1[num_processo]
				</a>
				</td>";				}else{ 
				echo "<td>AGUARDANDO CADASTRO </td>";}
				
		        echo "<td>$row_1[data_requisicao]</td>
				<td>$tipo</td>
		        <td>$row_1[nome_produto]</td>
		        <td>$row_user1[0]</td>
		        <td>R$ $row_1[valor_medio]</td>";
				if (mysql_num_rows($result_edital1) == 0)
				{print "<td>&nbsp;</td>";}
				else
				{
			    print "<td><a href='./anexo_edital/$row_edital1[nome_arquivo]'><img src='./imagens/download.png'></a></td>";
				}
				
				if (mysql_num_rows($result_edital2) == 0)
				{print "<td>&nbsp;</td>";}
				else
				{
			    print "<td><a href='./anexo_chamamento/$row_edital2[nome_arquivo]'><img src='./imagens/download.png'></a></td>";
				}				
				
				print "</tr>";
								
								
								
								
				}
								
				
			 												 } 
	
	
	}
	}
	  
	  ?>
    </table>
	 <br />
    
	<?php
	endwhile;
	
	  ?>
    </table>
	 <br />
    
    
    
   <?php
	$result_5 = mysql_query("SELECT *,date_format(data_requisicao, '%d/%m/%Y')as data_requisicao, 
	date_format(prazo, '%d/%m/%Y')as prazo FROM compra WHERE id_regiao = '$regiao' and acompanhamento = 8 or acompanhamento = '0' and id_regiao = '$regiao'");
	
	$verifica_permissao = mysql_num_rows(mysql_query("SELECT * FROM func_acompanhamento_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND id_acompanhamento = 8"));
	
	
	if(mysql_num_rows($result_5) !=0  and $verifica_permissao != 0){
	?>
         <h3 class="titulo_projeto">ACOMPANHAMENTO GERAL</h3>
    <table width="100%" class="relacao">
      
      <tr class="titulo_tabela1">
        <td width="19%" >N. PROCESSO</td>
        <td width="13%" >NECESS&Aacute;RIO PARA</td>
        <td>DATA</td>
        <td width="15%" >NOME</td>
        <td width="20%" >PEDIDO POR</td>
        <td width="14%" >VALOR</td>
        <td width="19%" >STATUS</td>
      </tr>
   <?php
	while($row_5 = mysql_fetch_array($result_5)){
		
		$result_user5 = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = $row_5[id_user_pedido]");
		$row_user5 = mysql_fetch_array($result_user5);
		
	if($cont_color++ % 2){ $class = "class='linha_um'"; }else{ $class = "class='linha_dois'"; }
		
		if($row_5['acompanhamento'] == "8"){ 
		$status="Enviado para Financeiro"; 
		}else if($row_5['acompanhamento'] == "0"){
		$status="Não autorizado"; 
		}else{		
		$status="Pago"; 
		}
		
	?>
		<tr <?php echo $class; ?>>
        <td><?php echo $row_5['num_processo']; ?></td>
		<td><?php echo $row_5['necessidade']; ?><td><?php echo $row_5['data_requisicao']; ?>
		<td><?php echo $row_5['nome_produto']; ?>
		  <td><?php echo $row_user5[0]; ?>
		  <td><?php echo 'R$'.$row_5['preco_final']?></td>
        <td><?php echo $status ?></td>
      	</tr>
	<?php
		
	  }
	}
	  ?>
    </table>
   
      <?php
	$qr_financeiro = mysql_query("SELECT compra.id_compra, saida.id_saida, saida.status,compra.necessidade, compra.num_processo, compra.nome_produto, compra.id_user_pedido, compra.preco_final,date_format(saida.data_vencimento, '%d/%m/%Y')as data_vencimento
									FROM `compra`
									INNER JOIN compra_saida_assoc ON compra_saida_assoc.id_compra = compra.id_compra
									INNER JOIN saida ON saida.id_saida = compra_saida_assoc.id_saida
									WHERE compra.id_regiao = '$regiao'
									AND compra.acompanhamento = 8 ORDER BY data_vencimento ASC");
	$verifica_permissao = mysql_num_rows(mysql_query("SELECT * FROM func_acompanhamento_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND id_acompanhamento = 8"));
	
	if(mysql_num_rows($qr_financeiro) !=0 and $verifica_permissao != 0){
	?>
         <h3 class="titulo_projeto">STATUS DO FINANCEIRO</h3>
    <table width="100%" class="relacao">
      
      <tr class="titulo_tabela1">
        <td width="19%" >N. PROCESSO</td>
        <td width="13%" >NECESS&Aacute;RIO PARA</td>
        <td>DATA</td>
        <td width="15%" >NOME</td>
        <td width="20%" >PEDIDO POR</td>
        <td width="14%" >VALOR</td>
        <td width="19%" >STATUS</td>
      </tr>
   <?php
	while($row_5 = mysql_fetch_array($qr_financeiro)){
		
		$result_user5 = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = $row_5[id_user_pedido]");
		$row_user5 = @mysql_fetch_array($result_user5);
		
	if($cont_color++ % 2){ $class = "class='linha_um'"; }else{ $class = "class='linha_dois'"; }
		
		if($row_5['acompanhamento'] == "0"){
		$status="Não autorizado"; 
		}else
		
		if($row_5['status'] == "1"){ 
			$title = 'Aguardando pagamento';
			$status="não pago"; 
		}else if($row_5['status'] == "2"){
			$title = 'Pago';
			$status="Pago"; 
		}
		
	?>
		<tr <?php echo $class; ?>>
        <td><?php echo $row_5['num_processo']; ?></td>
		<td><?php echo $row_5['necessidade']; ?><td><?php echo $row_5['data_vencimento']; ?>
		<td><?php echo $row_5['nome_produto']; ?>
		  <td><?php echo $row_user5[0]; ?>
		  <td><?php echo 'R$'.$row_5['preco_final']?></td>
        <td><img src="imagens/bolha<?php echo $row_5['status']?>.png" title="<?php echo $title; ?>" width="20" height="20"/></td>
      	</tr>
	<?php
		
	  }
	}
	  ?>
    </table>
   
  <?php
include "empresa.php";
$rod = new empresa();
$rod -> rodape();
?>
<?php

}

?>  
  </div>
 </div>
</body>
</html>