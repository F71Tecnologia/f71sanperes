<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";
$regiao = $_REQUEST['regiao'];

$id_user = $_COOKIE['logado'];
$result_user_logado = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'", $conn);
$row_user_logado = mysql_fetch_array($result_user_logado);

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
	width: 700px;
	padding: 0 5px 5px 5px;
}
</style>
</head>

<body>

<div id="corpo">
	<div id="conteudo">
    
    <img src="imagens/logomaster<?php echo $row_master['id_master']; ?>.gif"/>
  <h3>    GESTÃO DE COMPRAS</h3>
    
    
       <div align="left">
       <strong>
			<a href='solicitacompra2.php?regiao=<?php echo $regiao?>' style='TEXT-DECORATION: none;' onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )" >
	        <BR />
	 		 <img src="http://www.netsorrindo.com/intranet/img_menu_principal/compras.png" width="25" height="25" align="absmiddle" border="0"/>
	  		 SOLICITAR COMPRA
       
        </a> </strong>
      </div>
        <?php
		$result_1 = mysql_query("SELECT *,date_format(data_requisicao, '%d/%m/%Y')as data_requisicao FROM compra
		WHERE status_requisicao = '1' and acompanhamento = '1' and id_regiao = '$regiao'");
		
		
		if(mysql_num_rows($result_1) !=0){
		?>
         
		     <h3 class="titulo_projeto">Visualiza&ccedil;&atilde;o dos Pedidos em andamento:</h3>
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
		        <td><a href=compras/pedidocompra.php?id=1&compra=$row_1[0]&regiao=$regiao>$row_1[num_processo]
				</a></td>
		        <td>$row_1[data_requisicao]</td>
				<td>$tipo</td>
		        <td>$row_1[nome_produto]</td>
		        <td>$row_user1[0]</td>
		        <td>R$ $row_1[valor_medio]</td>
				</tr>";
				
				$cont_color1 ++;
				
			  }
	}
	  
	  ?>
    </table>
    <br />
    
  
        <?php
		$result_2 = mysql_query("SELECT *,date_format(data_requisicao, '%d/%m/%Y')as data_requisicao FROM compra
		WHERE status_requisicao = '2' and acompanhamento = '2' and id_regiao = '$regiao'");
		
		if(mysql_num_rows($result_2) !=0){
		?>
		  		<h3 class="titulo_projeto">Visualiza&ccedil;&atilde;o dos Processos aguardando pesquisa:</h3>
     
		      <table width="100%" class="relacao">
		        <tr class="titulo_tabela1">
		        <td width="16%"  >N. PROCESSO</td>
		        <td width="8%" >DATA</td>
		        <td width="15%" >TIPO</td>
		        <td width="29%"  >NOME</td>
		        <td width="14%">SOLICITADO POR:</td>
		        <td width="18%">VALOR</td>
		      </tr>
				<?php
				while($row_2 = mysql_fetch_array($result_2)){
				
				$result_user2 = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = $row_2[id_user_pedido]");
				$row_user2 = mysql_fetch_array($result_user2);
				
				if($row_2['tipo'] == "1"){ $tipo="PRODUTO"; }else{ $tipo="SERVIÇO"; }
				if($cont_color++ % 2){ $class = "class=linha_um"; }else{ $class = "class=linha_dois"; }
				
				print "
				<tr  $class>
		        <td><a href=compras/cotacoes.php?id=1&compra=$row_2[0]&regiao=$regiao>$row_2[num_processo]
				</a></td>
		        <td>$row_2[data_requisicao]</td>
				<td>$tipo</td>
		        <td>$row_2[nome_produto]</td>
		        <td>$row_user2[0]</td>
		        <td>R$ $row_2[valor_medio]</td>
				</tr>";
				
				$cont_color2 ++;
				
			  }
		}
	  ?>
    </table>
    <br />
      
        <?php
		$result_3 = mysql_query("SELECT *,date_format(data_requisicao, '%d/%m/%Y')as data_requisicao FROM compra
		WHERE acompanhamento = '3' and id_regiao = '$regiao'");
		
		
		if(mysql_num_rows($result_3) !=0){
		?>
	        <h3 class="titulo_projeto">Visualiza&ccedil;&atilde;o das Decis&otilde;es a serem tomadas:</h3>
		    <table width="100%" class="relacao">
		     
		      <tr class="titulo_tabela1">
		        <td width="16%" >N. PROCESSO</td>
                  <td>ANEXO(S)</td>
		        <td width="8%" >DATA</td>
		        <td width="14%" >TIPO</td>
		        <td width="30%" >NOME</td>
		        <td width="15%">SOLICITADO POR:</td>
		        <td width="17%" >VALOR</td>
		      </tr>
      
		<?php
		while($row_3 = mysql_fetch_array($result_3)){
		
		$result_user3 = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = $row_3[id_user_pedido]");
		$row_user3 = mysql_fetch_array($result_user3);
		
		if($row_3['tipo'] == "1"){ $tipo="PRODUTO"; }else{ $tipo="SERVIÇO"; }
		if($cont_color % 2){ $class = "class=linha_um"; }else{ $class = "class=linha_dois"; }
		
		print "
		<tr $class>
        <td><a href=compras/decisao.php?id=1&compra=$row_3[0]&regiao=$regiao>$row_3[num_processo]
		</a></td>";
		?>
		<td><a href="compras/visualiza_anexo.php?compra=<?php echo $row_3['id_compra'];?>" onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )">ver</a></td>
		
		<?php
		echo "
        <td>$row_3[data_requisicao]</td>
		<td>$tipo</td>
        <td>$row_3[nome_produto]</td>
        <td>$row_user3[0]</td>
        <td>R$ $row_3[valor_medio]</td>
		</tr>";
		
		$cont_color3 ++;
		
	  }
		}
	  ?>
    </table>    
    <br />
    
     
        <?php
		$result_4 = mysql_query("SELECT *,date_format(data_requisicao, '%d/%m/%Y')as data_requisicao, 
		date_format(prazo, '%d/%m/%Y')as prazo FROM compra WHERE acompanhamento = '4' and id_regiao = '$regiao'");

		if(mysql_num_rows($result_4) !=0){
			
		?>
		 <h3 class="titulo_projeto"> Visualiza&ccedil;&atilde;o das Autoriza&ccedil;&otilde;es a serem efetuadas:</h3>
        <table width="100%" class="relacao" >
             <tr class="titulo_tabela1">
                <td width="19%" >N. PROCESSO</td>
                <td width="13%">NECESS&Aacute;RIO PARA</td>
                <td>DATA</td>
                <td width="9%" >TIPO</td>
                <td width="11%" >NOME</td>
                <td width="16%" >DECIDIDO POR</td>
                <td width="10%" >VALOR</td>
                <td width="22%" > FORNECEDOR<br />
         DATA ENTREGA</td>
            </tr>
		<?php
		while($row_4 = mysql_fetch_array($result_4)){
		
		$result_user4 = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = $row_4[id_user_escolha]");
		$row_user4 = mysql_fetch_array($result_user4);
		
		$result_f4 = mysql_query("SELECT * FROM fornecedores where id_fornecedor = '$row_4[fornecedor_escolhido]'");
		$row_for4 = mysql_fetch_array($result_f4);
		
		if($row_4['tipo'] == "1"){ $tipo="PRODUTO"; }else{ $tipo="SERVIÇO"; }
		if($cont_color++ % 2){ $class = "class=linha_um"; }else{ $class = "class=linha_dois"; }
		
		?>
		<tr <?php echo $class;?>>
        <td><a href='compras/confirmandocompra.php?compra=<?php echo $row_4[0];?>&regiao=<?php echo $regiao; ?>'>
		<br />
<?php echo $row_4['num_processo'];?>
		</a>
        </td>
		<td><?php echo $row_5['necessidade']; ?></td>
		<td><?php echo $row_4['data_requisicao']; ?>
		<td><?php echo $tipo; ?>	
		<td><?php echo $row_4['nome_produto']; ?></td>
        <td><?php echo $row_user4[0]; ?></td>
        <td><?php echo 'R$ '.$row_4['preco_final']; ?></td>
        <td><?php echo $row_for4['nome'].'<br>'.$row_4['prazo']; ?></td>
		
		</tr>
		
	<?php	
	  }
}
	  ?>
    </table>
    <br />
    
   
      <?php
	$result_5 = mysql_query("SELECT *,date_format(data_produto, '%d/%m/%Y')as data_produto, 
	date_format(prazo, '%d/%m/%Y')as prazo FROM compra WHERE id_regiao = '$regiao' and acompanhamento >= '5' or acompanhamento = '0' and id_regiao = '$regiao'");
	
	if(mysql_num_rows($result_5) !=0){
	?>
         <h3 class="titulo_projeto">Acompanhamento Geral:</h3>
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
		
		if($row_5['acompanhamento'] == "5"){ 
		$status="Enviado para Financeiro"; 
		}else if($row_5['acompanhamento'] == "0"){
		$status="Não autorizado"; 
		}else{		
		$status="Pago"; 
		}
		
	?>
		<tr <?php echo $class; ?>>
        <td><?php echo $row_5['num_processo']; ?></td>
		<td><?php echo $row_5['necessidade']; ?><td><?php echo $row_5['data_produto']; ?>
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
    <br />
    
    
    
     
    <br />
   <!--<font face="Verdana, Geneva, sans-serif" color="#FF0000" size="-2"><strong>Informa&ccedil;&otilde;es: PEDIDO = Compra solicitada sendo avaliada<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ABERTURA = Compra em processo de aprova&ccedil;&atilde;o<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PESQUISA = Comrpa em cota&ccedil;&atilde;o com os fornecedores<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DECIS&Atilde;O = Setor financeiro avaliando o fornecedor<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AUTORIZA&Ccedil;&Atilde;O = Compra autorizada pelo Gerente do Projeto e encaminhada para financeiro</strong></td>

--->
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