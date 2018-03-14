<?php
include('../adm/include/restricoes.php');
include('../conn.php');
include('../funcoes.php');
include('include/criptografia.php');
include('../classes/formato_data.php');



$ano = $_POST['ano'];
$id_prestador = $_POST['prestador'];
$regiao =$_POST['regiao'];
$id_projeto =$_POST['projeto'];
$id_user = $_COOKIE['logado'];

//SELECIONANDO O INSTITUTO PARAR CARREGAR A LOGO
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);


$qr_regioes = mysql_query("SELECT * FROM regioes WHERE id_regiao= '$regiao';");
$row_regiao = mysql_fetch_assoc($qr_regioes);

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao= '$regiao' AND id_projeto = '$id_projeto';");
$row_projeto = mysql_fetch_assoc($qr_projeto);

$qr_prestador = mysql_query("SELECT * FROM prestadorservico WHERE id_regiao= '$regiao' AND id_projeto ='$id_projeto';");
$row_prestador = mysql_fetch_assoc($qr_prestador);


?>

<html>
<head>
<title>Administra&ccedil;&atilde;o de Projetos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../adm/css/estrutura.css" rel="stylesheet" type="text/css">
<link href="../js/highslide.css" rel="stylesheet" type="text/css"  /> 
<script type="text/javascript" src="../js/highslide-with-html.js"></script> 
<script type="text/javascript" src="../jquery-1.3.2.js"></script> 
<script type="text/javascript"> 
    hs.graphicsDir = '../images-box/graphics/';
    hs.outlineType = 'rounded-white';
	
$(document).ready(function(){
  
	
});	

</script>

<style>
body{
background-color:#FFF;
visibility:visible;
font-family:"Times New Roman", Times, serif;
}
#tabela{
padding:20px;


}
.linha_lista{
	font-size:14px;
	color:#000;
	
	
}
.linha_1{
	font-size:12px;
	background-color:#D2E9FF;}
.linha_2{
	font-size:12px;
	background-color:#F3F3F3;
}

.editar {
	margin-left:16px;	
}

#topo{
	background-color: #EEE;
	visibility: visible;
	margin:20px;
	font-size:12px;
	
	}
h1{

font-size:16px;

}
#razao{
text-align:center;
	
}


</style>


<style media="print">
body{
background-color:#FFF;
visibility:visible;
font-family:Arial, Helvetica, sans-serif;
}

#tabela{

padding:20px;
visibility: visible;

}
.linha_lista{
	font-size:14px;
	color:#000;
	visibility: visible;
	
}
.linha_1{
	font-size:12px;
	background-color:#D2E9FF;
	visibility: visible;
	}
.linha_2{
	font-size:12px;
	background-color:#F3F3F3;
	visibility: visible;
}

.editar {
	margin-left:16px;	
	visibility: visible;
}

#topo{
	background-color: #EEE;
	visibility: visible;
	margin:20px;
	font-size:12px;
	
	}
	
#razao{
	text-align:center;
	visibility: visible;
}
h1{

font-size:16px;
visibility: visible;

}
</style>
</head>
<body>
<div id="corpo">
        
<div id="conteudo">
         <?php
include "../empresa.php";
$img= new empresa();
$img -> imagem();
?>


<h1>FICHA FINANCEIRA</h1>
<div id="topo">

<table class="linha_lista">
    
    
    
    <tr>
    	<td><strong>Regi�o:</strong></td>
        <td><?php echo $row_regiao['regiao'].' - ('.$row_regiao['sigla'].')'	;?></td>
    </tr>
   
    
     <tr>
    	<td><strong>Projeto:</strong></td>
        <td><?php echo $row_projeto['nome']; ?></td>
    </tr>
     <tr>
    	<td><strong>Ano:</strong></td>
        <td><?php echo $ano;?></td>
    </tr>
    
    <tr>
        <td width="153" valign="top"><strong>Prestador de Servi�o:</strong></td>
        <td width="263"><?php echo $row_prestador['c_razao'];?></td>
    </tr>
   
     <tr>
        <td width="153" valign="top"><strong>CNPJ:</strong></td>
        <td width="263"><?php echo $row_prestador['cnpj'];?></td>
    </tr>
</table>

</div>




<?php		
		///CALCULA O VALOR TOTAL DAS SAIDAS POR M�S E ANOS
		
		$meses = array(1 => 'Janeiro', 2=> 'Fevereiro', 3 => 'Mar�o', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro');
		
	
		
		
			 	foreach($meses as $chave => $mes):
			
		 
					 $query_pg_total = mysql_query("SELECT saida.id_saida, saida.data_vencimento, saida.valor,saida.status, saida.data_pg, saida.id_regiao, saida.id_projeto, prestador_pg.id_pg,prestador_pg.gerado, prestador_pg.comprovante, saida.comprovante  FROM  prestador_pg INNER JOIN saida ON prestador_pg.id_saida = saida.id_saida WHERE prestador_pg.status_reg = '1' AND prestador_pg.id_prestador = '$id_prestador' AND saida.status != '0'  AND YEAR(saida.data_pg) = '$ano' AND MONTH(saida.data_pg) = '$chave'") or die(mysql_error());
						$linha = mysql_num_rows($query_pg_total);
						while($row_pg_total = mysql_fetch_array($query_pg_total)):
						
								$valor_total =str_replace(',','.', $row_pg_total['valor']);
								$total_Pg[$ano][$mes] += $valor_total;
								
						
						endwhile;
					
			endforeach;
		 
		
		 
	///CALCULA O VALOR TOTAL DAS SAIDAS POR M�S E ANOS	
		
         ?>                               

		<table width="100%" id="tabela" class="linha_lista" border="0">				 		
			<tr>
						<td  width="50%" colspan="2" style="text-align:center;background-color:#EEE;">TOTAL POR M�S</td>
                       
             </tr>					
                         
           <?php                     
		    foreach($meses as $chave => $mes):
			 					$i++;
			 
			 		 if($total_Pg[$ano][$mes] != ''){
						 
						 $total += $total_Pg[$ano][$mes];
						 ?>
					
					
					<tr <?php if($i % 2 ==0){echo 'style = "background-color: #ECF7FF;border: 1px solid #000;"';} else {echo 'style = "background-color: #:#C7F1FE;"border: 1px solid #000;'; } ?>>
						<td  width="50%"><?php echo $meses[$chave]; ?></td>
						<td width="50%" style="text-align:right;" > R$ <?php echo number_format($total_Pg[$ano][$mes],2,',','.');?></td>						
					</tr>
	<?php								   
					 }
					 
			 endforeach;
                         
               
                     
            ?>
              <tr>
              		<td style="text-align:right;">TOTAL: </td>
                    <td>R$ <?php echo number_format($total,2,',','.'); ?> </td>
              
              </tr>
              
               <tr>
              		<td style="text-align:center;" colspan="2"></td>
              
              </tr>
                
			       
           </table>
           
  
  
  

 </div>
 <div id="razao">
 <?php echo $row_master['razao']; ?>
 </div>
 
 
</div>
</body>
</html>