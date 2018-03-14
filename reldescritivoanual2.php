<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

$hostname = $_SERVER['REMOTE_ADDR'];

include "conn.php";
$regiao = $_REQUEST['regiao'];
$id_user = $_COOKIE['logado'];
$bancos  = $_REQUEST['banco'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - Relat&oacute;rio descritivo Mensal</title>
<style type="text/css">
<!--
body{
	font-family: Arial, Helvetica, sans-serif;
	text-transform: uppercase!important; 
	font-size: 11px;
	color:#000;
}
h1{
	margin:0px;
	padding:0px;
	font-size:14px;
	font-weight:bold;
}
.style2 {font-size: 12px}
.style6 {font-size: 14px; font-weight: bold; color: #FFFFFF; }
.style7 {color: #003300}
.style16 {
	font-size: 12px;
	font-weight: bold;
	color: #000;
}
.style19 {
	color: #000;
	font-weight: bold;
	font-size: 12px;
}
.style21 {
	font-size: 12px;
	font-weight: bold;
}
.style23 {font-size: 11px; font-weight: bold; color: #FF0000; }
.style24 {font-size: 12px; font-weight: bold; }
-->
</style>
<?php
print "
<script>
   function mascara_data(d){  
       var mydata = '';  
       data = d.value;  
       mydata = mydata + data;  
       if (mydata.length == 2){  
          mydata = mydata + '/';  
          d.value = mydata;  
       }  
          if (mydata.length == 5){  
          mydata = mydata + '/';  
          d.value = mydata;  
       }  
          if (mydata.length == 10){  
          verifica_data(d);  
         }  
      } 
           
         function verifica_data (d) {  

         dia = (d.value.substring(0,2));  
         mes = (d.value.substring(3,5));  
         ano = (d.value.substring(6,10));  
             

       situacao = \"\";  
       // verifica o dia valido para cada mes  
       if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31) {  
           situacao = \"falsa\";  
       }  

       // verifica se o mes e valido  
       if (mes < 01 || mes > 12 ) {  
              situacao = \"falsa\";  
       }  

      // verifica se e ano bissexto  
      if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) {  
            situacao = \"falsa\";  
      }  
   
     if (d.value == \"\") {  
          situacao = \"falsa\";  
    }  

    if (situacao == \"falsa\") {  
       alert(\"Data digitada é inválida, digite novamente!\"); 
       d.value = \"\";  
       d.focus();  
    }  
	
}
</script></head>";

?>
<script type="text/javascript" src="jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript" src="jquery/combo.js" ></script>
<script type="text/javascript" src="js/global.js" ></script>
<script type="text/javascript">

$(function(){
	$('#projeto').combo({
					reposta : '#banco',
					url : 'novoFinanceiro/actions/combo.bancos.json.php'
				});
				
				
	$('#tipo').change(function(){ 
	
		if($(this).val() == 'entrada') {
			
			$('#select_entrada').show();
			$('#select_saida').hide();
		
		} else {
			
			$('#select_entrada').hide();
			$('#select_saida').show();
		
		}
	
	
	});
});
</script>

<link href="net1.css" rel="stylesheet" type="text/css" />
<link href="novoFinanceiro/style/form.css" rel="stylesheet" type="text/css">
<link href="novoFinanceiro/style/estilo_financeiro.css" rel="stylesheet" type="text/css">
<body>
<table width="750" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td colspan="4"><img src="layout/topo.gif" width="750" height="38" /></td>
  </tr>
  
  <tr>
    <td width="21" rowspan="4" background="layout/esquerdo.gif">&nbsp;</td>
    <td width="354" align="center" valign="middle" bgcolor="#FFFFFF">&nbsp;</td>
    <td width="349" align="left" bgcolor="#FFFFFF">&nbsp;</td>
    <td width="26" rowspan="4" background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle" bgcolor="#FFFFFF">
<?php
include "empresa.php";
$img= new empresa();
$img -> imagem();
?>
<h1>
RELAT&Oacute;RIO ANUAL DESCRITIVO<br /><br />

<?php

 if(isset($_REQUEST['gerar'])) {
	 
	 
	  echo'Tipo: '.$_REQUEST['tipo'].' - '; 
 
		
			if($_REQUEST['tipo_anual'] == 'entrada' ) {
			 
							 
							 if($_REQUEST['tipo_entrada'] != 'todos'){
								 
							  $qr_nome = mysql_query("SELECT nome FROM entradaesaida WHERE id_entradasaida ='$_REQUEST[tipo_entrada]'") or die(mysql_error());
							   $row_nome = mysql_fetch_assoc($qr_nome);
								echo $row_nome['nome'];
							 } else {	 
							 echo $_REQUEST['tipo_entrada'];
							 }
			 
		 
		 
				 } elseif($_REQUEST['tipo_anual'] == 'saida' ) {
					 
					 if($_REQUEST['tipo_saida'] != 'todos'){
								 
							  $qr_nome = mysql_query("SELECT nome FROM entradaesaida WHERE id_entradasaida ='$_REQUEST[tipo_saida]'") or die(mysql_error());
							   $row_nome = mysql_fetch_assoc($qr_nome);
								echo $row_nome['nome'];
								
								
							 } else {	
							  
							 echo $_REQUEST['tipo_saida'];
							 }
							 
					
		}

}
 ?>
 
 <br>
<?php if(isset($_REQUEST['gerar'])) echo'('.$_REQUEST['ano'].')';?>
 


</h1>
</td>
  </tr>
  
  <tr>
    <td height="96" colspan="2" align="center" valign="top" bgcolor="#FFFFFF">

<form action="reldescritivoanual2.php" method="post" enctype="multipart/form-data" name='form1' onSubmit="return validaForm()" id="form1">
      <div align="right">
        <p align="center">
          <?php

if(!isset($_REQUEST['gerar'])){
		
?>
          <br />
          <br />
        </p>
        <table width="90%" border="0" align="center" cellspacing="0" bordercolor="#999999">
        
            <td height="27" align="right" bgcolor="#FFFFFF">SELECIONE O ANO:</td>
            <td height="27" align="center" bgcolor="#FFFFFF"><div align="left">
              &nbsp;&nbsp;
              <select name="ano" id="ano">
                <?php
				for($i = 2005; $i <= (date('Y')); $i ++){
					$selected = ($i == date('Y')) ? 'selected="selected"' : '';
					echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
				}
				?>
                
              </select>
            </div></td>
          </tr>
          <tr>
            <td height="27" align="right" bgcolor="#FFFFFF">
              SELECIONE O TIPO:            
            </span></td>
            <td height="27" align="center" bgcolor="#FFFFFF"><span class="style24">
              <div align="left">&nbsp;&nbsp;
                <select name="tipo" id="tipo">
                <option value="">Escolha o tipo...</option>
                  <option value="entrada">Entrada</option>
                  <option value="saida" >Saída</option>
            </select>
                  </label>
            </div>
              </span></td>
          </tr>
          
          <!---------- TIPOS DE ENTRADA --------------------->
          <tr id="select_entrada"  style="display:none;">
            <td height="27" align="right" bgcolor="#FFFFFF">SELECIONE O TIPO DE ENTRADA:</td>
            <td height="27" align="center" valign="middle" bgcolor="#FFFFFF"><span class="style24">
              <div align="left">&nbsp;&nbsp;
                <select name="tipo_entrada" id="tipo_entrada">
                
                 <option value="">Escolha o tipo...</option>
                  <option value="todos" >TODOS</option>
                 <?php
                 $qr_tipo_entrada = mysql_query("SELECT * FROM entradaesaida WHERE tipo = 1");
				 while($row_tipo_entrada = mysql_fetch_assoc($qr_tipo_entrada)):
				 
				 	echo '<option value="'.$row_tipo_entrada['id_entradasaida'].'">'.$row_tipo_entrada['cod'].' - '.$row_tipo_entrada['nome'].'</option>';
					
				 endwhile;
				 ?>
            </select>
              </div>
            </span></td>
          </tr>
          
          <!---------- TIPOS DE SAÍDA  --------------------->
          <tr id="select_saida" style="display:none;">
            <td height="27" align="right" bgcolor="#FFFFFF">SELECIONE O TIPO DE SÁIDA</td>
            <td height="27" align="center" valign="middle" bgcolor="#FFFFFF"><span class="style24">
              <div align="left">&nbsp;&nbsp;
                <select name="tipo_saida" id="tipo_saida">
               		
                      <option value="">Escolha o tipo...</option>
                     <option value="todos" >TODOS</option>
                      <?php
                 $qr_tipo_entrada = mysql_query("SELECT * FROM entradaesaida WHERE tipo = 0");
				 while($row_tipo_entrada = mysql_fetch_assoc($qr_tipo_entrada)):
				 
				 	echo '<option value="'.$row_tipo_entrada['id_entradasaida'].'">'.$row_tipo_entrada['cod'].' - '.$row_tipo_entrada['nome'].'</option>';
					
				 endwhile;
				 ?>
           		</select>
              </div>
            </span></td>
          </tr>
          
          <tr>
            <td height="39" colspan="2" align="center" bgcolor="#FFFFFF"><label>
              <input name="gerar" type="submit" class="submit-go" id="gerar" value="GERAR RELATORIO" />
            </label></td>
            </tr>
        </table>
        <br />
        <?php
/* Liberando o resultado */
//mysql_free_result($result_banco);
//mysql_free_result($result_projeto);
//mysql_free_result($result_tipo);

}else{
	
$tipo = $_REQUEST['tipo_anual'];	
$tipo_entrada = $_REQUEST['tipo_entrada'];
$tipo_saida = $_REQUEST['tipo_saida'];
$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$banco = $_REQUEST['banco'];
$ano = $_REQUEST['ano'];

$tipodata = $_REQUEST['tipodata'];

if($tipodata == "data_proc"){
$tipodataf = "Processamento";
}elseif($tipodata == "data_vencimento"){
$tipodataf = "Vencimento";
}else{
$tipodataf = "Pagamento";
}

if($banco == 'todos'){
	$result_banco = mysql_query("SELECT * FROM bancos where  status_reg ='1'");
} else
{
	$result_banco = mysql_query("SELECT * FROM bancos where   id_banco = '$banco' AND status_reg ='1'");
}

while($row_banco = mysql_fetch_array($result_banco)):

/*

if($tipo == 'entrada'){

		if($tipo_entrada =='todos')  {
			
			$qr_tipo = mysql_query("SELECT * FROM entrada WHERE id_banco = '$row_banco[id_banco]' AND YEAR(data_pg ) = '$ano' ORDER BY data_pg  ASC  ");
			
			}else { 
			$qr_tipo = mysql_query("SELECT DISTINCT(tipo) FROM entrada WHERE id_banco = '$row_banco[id_banco]' AND tipo = '$tipo_entrada' AND YEAR(data_pg ) = '$ano' AND status = 2  ORDER BY data_pg  ASC");
			
			
			
			}


$qr_tipo = mysql_query("SELECT * FROM entrada WHERE id_banco = '$row_banco[id_banco]' AND YEAR(data_pg ) = '$ano' ORDER BY data_vencimento ASC ");

} elseif($tipo == 'saida') {
	

		if($tipo_saida =='todos')  {
			
			$qr_tipo = mysql_query("SELECT * FROM saida  WHERE id_banco = '$row_banco[id_banco]' AND YEAR(data_pg) = '$ano'  ORDER BY data_pg  ASC");
			
			}else { 
			
			$qr_tipo = mysql_query("SELECT DISTINCT(tipo) FROM saida  WHERE id_banco = '$row_banco[id_banco]' AND tipo = '$tipo_saida' AND YEAR(data_pg ) = '$ano' ORDER BY data_pg  ASC") or die(mysql_error());
			
			}
}
	

if(mysql_num_rows($qr_tipo) == 0)  

continue;	*/

	

?>

<p id="excel" style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="exportarExcel"></p>
<table id="tbRelatorio" width="97%" border="0" align="center" cellspacing="0" bordercolor="#003300">
        <tr>
        	<td>
            	<table width="100%" cellspacing="0">
					<?php			
                    
$mostrar_titulo = 0;
					

for($mes=1;$mes<=12;$mes++):
						
						if(($tipo == 'entrada'   )){

	
							if($tipo_entrada == 'todos'){
							
							$qr_tipo = mysql_query("SELECT DISTINCT(tipo) FROM $tipo WHERE id_banco = '$row_banco[id_banco]' AND YEAR(data_pg ) = '$ano' AND month(data_pg ) = '$mes'   ORDER BY data_pg  ASC  ");
							
							
							} else {
								$qr_tipo = mysql_query("SELECT DISTINCT(tipo) FROM $tipo WHERE id_banco = '$row_banco[id_banco]' AND YEAR(data_pg ) = '$ano' AND month(data_pg ) = '$mes'  AND tipo = '$tipo_entrada' ORDER BY data_pg  ASC  ");
							}
							
							
						}elseif($tipo == 'saida'){
							
								if($tipo_saida == 'todos'){
								
										$qr_tipo = mysql_query("SELECT DISTINCT(tipo) FROM $tipo WHERE id_banco = '$row_banco[id_banco]' AND YEAR(data_pg ) = '$ano' AND month(data_pg ) = '$mes'   ORDER BY data_pg  ASC  ")or die(mysql_error());
								} else{
									
									$qr_tipo = mysql_query("SELECT DISTINCT(tipo) FROM $tipo WHERE id_banco = '$row_banco[id_banco]' AND YEAR(data_pg ) = '$ano' AND month(data_pg ) = '$mes'  AND tipo = '$tipo_saida' ORDER BY data_pg  ASC  ");
									
								}
							
						}
						
						if(mysql_num_rows($qr_tipo) == 0) {
							
							
							 continue; 
						}
						
						
						
							while($row_tipo = mysql_fetch_assoc($qr_tipo)):
							
							
							$class = ($alter_Color++%2) ? 'linha_um' : 'linha_dois';
							
							//NOMES DOS TIPOS
							$qr_entradaesaida = mysql_query("SELECT * FROM entradaesaida WHERE id_entradasaida = '$row_tipo[tipo]'  ");
							$row_entradaesaida = mysql_fetch_assoc($qr_entradaesaida);
							
							//QUANTIDADE DE TIPOS
                            $qr_quantidade = mysql_query("SELECT COUNT(tipo) as tipo_total FROM $tipo WHERE id_banco = '$row_banco[id_banco]'  AND month(data_vencimento ) = '$mes' AND YEAR(data_vencimento ) = '$ano' AND tipo = '$row_tipo[tipo]' AND status = 2 ") or die(mysql_error());
                            $row_quantidade = mysql_fetch_assoc($qr_quantidade);
							
							//SOMA DOS VALORES NO MES
							$qr_tipo_mes = mysql_query("SELECT SUM( REPLACE( valor, ',', '.' ) ) as valor FROM $tipo WHERE id_banco = '$row_banco[id_banco]' AND YEAR(data_vencimento ) = '$ano' AND month(data_vencimento ) = '$mes' AND tipo = '$row_tipo[tipo]' AND status = 2 ORDER BY data_vencimento  ASC  ");
						
							
							
							$row_tipo_mes = mysql_fetch_assoc($qr_tipo_mes);
							
							if(mysql_num_rows($qr_quantidade) == 0 or mysql_num_rows($qr_tipo) == 0 or  mysql_num_rows($qr_tipo_mes) == 0 ) {
								
								
								
								}
					
					
					
					switch ($mes) {

						case 1:
						$meses_1 = "Janeiro";
						break;
						case 2:
						$meses_1 = "Fevereiro";
						break;
						case 3:
						$meses_1 = "Março";
						break;
						case 4:
						$meses_1 = "Abril";
						break;
						case 5:
						$meses_1 = "Maio";
						break;
						case 6:
						$meses_1 = "Junho";
						break;
						case 7:
						$meses_1 = "Julho";
						break;
						case 8:
						$meses_1 = "Agosto";
						break;
						case 9:
						$meses_1 = "Setembro";
						break;
						case 10:
						$meses_1 = "Outubro";
						break;
						case 11:
						$meses_1 = "Novembro";
						break;
						case 12:
						$meses_1 = "Dezembro";
						break;
					}
					 
					 
					 
					 if($mostrar_titulo == 0){?>
						 
                                         <tr>
                                             <td colspan="3"> <hr /> </td>
                                         </tr>
                                              <tr>
                                            <td colspan="3" align="center"><div align="center"><span class="style16"></span><span class="style19">
                                            <?=$row_banco['nome'];?><br />
                                           (Agência: <?=$row_banco['agencia'];?>   Conta:<?=$row_banco['conta'];?>)</span></div>
                                           </td>
                                          </tr>
                                          
                                         <tr>
                                                 <td colspan="3"> <hr /> </td>
                                             </tr>
                                        <?php		
                                            $mostrar_titulo = 1;
                         }
                         
                         
                            if($mes != $mes_anterior){
                            
                                echo' <tr>
                                         <td colspan="3" height="5"> </td>
                                     </tr>
                                     
                                <tr bgcolor="#D8D8D8">
                                        <td style="font-weight:bold;text-align:center;" colspan="3">'.$meses_1.'</td>							
                                    </tr>
                                    ';
                                }
                            ?>
                            <tr class="<?=$class?>">
                            <td>
                                <?php
                                
                                echo $row_quantidade['tipo_total'];
                                
                                ?>
						</td>
                        <td width="center">
						<?php 
						
							echo $row_entradaesaida['nome'];
						?>
                        
                        </td>
                        <td align="center"><?php 
						
						
						echo 'R$ '.number_format($row_tipo_mes['valor'],2,',','.');
						
						$totalizador += $row_tipo_mes['valor'];
						
						?></td>
                        </tr>
						<?php
						$mes_anterior = $mes;
						
                    endwhile;//fim nomes tipo
					
                  endfor;
                    ?>
                    
                </table>
                
                </td>
             </tr>
     
     <?php
     if($totalizador !=0){
		 $totalizador_geral += $totalizador; 
		 ?>
     <tr>
        <td bgcolor="#FFFFFF"  align="right" colspan="2"><strong>TOTAL DO ANO:  </strong><span class="style23"><?='R$ '.number_format($totalizador,2,',','.');$totalizador?></span></td>
     </tr>
     <?php }  ?>
             
   
    
     
<?php
unset($mes_anterior,$mes,$mostrar_titulo,$totalizador);
	
endwhile;

}

 
?>
  
  <TR>
  	<TD colspan="2" pan class="style23" align="right"> TOTAL DE TODAS AS CONTAS NO ANO: <?PHP echo 'R$ '.number_format($totalizador_geral,2,',','.');?></TD>
  </TR> 
  </table>  
 
  
  <tr>
    <td height="18" colspan="2" align="center" valign="top">&nbsp;</td>
  </tr>

  
  
  <tr valign="top">
    <td height="37" colspan="4" bgcolor="#E2E2E2"><img src="layout/baixo.gif" width="750" height="38" />
<?php
$rod = new empresa();
$rod -> rodape();
?>
</td>
  </tr>
</table>

<?php

}
?>
</body>
</html>
