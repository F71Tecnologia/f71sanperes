<?php
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='../login.php'>Logar</a>";
exit;
} else {


function data($data) {
	
if($data!= '0000-00-00'){
		
		echo implode('/',array_reverse(explode('-',$data)));
		
		}
			
	
	
}

function valor_real($valor) {


return number_format($valor,2,',','.');





}

include "../conn.php";

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$projeto = $_REQUEST['pro'];
$regiao = $_REQUEST['reg'];

$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_projeto = mysql_fetch_array($result_projeto);

$data_hoje = date('d/m/Y');

$result_clt = mysql_query("SELECT * FROM rh_clt WHERE status <60  AND id_regiao ='$regiao' AND id_projeto ='$projeto' ORDER BY nome");
$num_clt = mysql_num_rows($result_clt);

$result_cooperado = mysql_query("SELECT * FROM autonomo WHERE status = '1'  AND tipo_contratacao ='3' AND id_regiao ='$regiao' AND id_projeto ='$projeto' ORDER BY nome");
$num_cooperado = mysql_num_rows($result_cooperado);

$result_bolsista = mysql_query("SELECT * FROM autonomo WHERE status = '1'  AND tipo_contratacao ='1' AND id_regiao ='$regiao' AND id_projeto ='$projeto' ORDER BY nome");
$num_bolsista = mysql_num_rows($result_bolsista);

$result_pj = mysql_query("SELECT * FROM autonomo WHERE status = '1'  AND tipo_contratacao ='4' AND id_regiao ='$regiao' AND id_projeto ='$projeto' ORDER BY nome");
$num_pj = mysql_num_rows($result_pj);
?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>Relat&oacute;rio de Participantes do Projeto em Ordem Alfab�tica</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css">

<style>
table tr.linha_um:hover {
	
background-color: #E1F0FF;

}
table tr.linha_dois:hover {
	
background-color: #E1F0FF;

}


table tr#duplicado {
	background-color:#FF8080;


}

table tr#duplicado:hover {
	
background-color: #F66;


}

</style>

</head>
<body style="background-color:#FFF; margin-top:30px; margin-bottom:30px;">
<table cellspacing="0" cellpadding="0"  style="width:auto; border:0px; margin-left:30px;">

    
    
    <tr>
    <td width="80%" align="left">
        
         
         <table width="500" border="0" align="left" cellpadding="4" cellspacing="1" style="font-size:12px;margin-left:30px;">
          
           <tr> 
            <td width="20%" align="left" colspan="3">
                  <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
            </td>
            </tr>
            <tr>
            <td width="20%" align="left" colspan="3">
                  <strong>RELAT&Oacute;RIO DE PARTICIPANTES PARA IMPRESS&Atilde;O DE CRACH&Aacute;S</strong><br>
         <?=$row_master['razao']?>
            </td>
           
            </tr>
          
          
            <tr style="color:#FFF;">
              <td width="150" height="22" class="top">PROJETO</td>
              <td width="150" class="top">REGI�O</td>
              <td width="200" class="top">TOTAL DE PARTICIPANTES</td>
            </tr>
            <tr style="color:#333; background-color:#efefef;">
              <td height="20" align="center"><b><?=$row_projeto['nome']?></b></td>
              <td align="center"><b><?=$row_projeto['regiao']?></b></td>
              <td align="center"><b><?php echo $num_clt+$num_cooperado+$num_bolsista+$num_pj; ?></b></td>
            </tr>
        </table>
    </td>
  </tr>
  <tr> 
    <td colspan="2">&nbsp;</td>
  </tr>
   <tr>
     <td colspan="2">
    
    <?php if(!empty($num_clt)) { ?>

      <div class="descricao" style="text-align:left;font-weight:bold;">Relat&oacute;rio em Ordem Alfab�tica</div>
      <br>
      <table class="relacao" width="100%" cellpadding="3" cellspacing="1"  style="margin-left:30px;margin-right:30px;">
        <tr class="secao">
          <td align="center">Cod.</td> 
             <td align="center">Nome</td> 
             <td align="center">Atividade</td>
             <td align="center">PIS</td>
             <td align="center">Data de entrada</td>
        </tr>

	    <?php
		
	  
	  ////EXIBE OS REGITROS DUPLICADOS SE EXISTIREM (CLT)
	  
	  

											
											
											
	 $qr_duplicado_clt= mysql_query("SELECT id_clt,
											nome, 
											matricula,
											rg,
											cpf,
											pis,
											titulo,
											serie_ctps,
											COUNT(nome) as total_nome, 
											COUNT(rg) as total_rg, 
											COUNT(cpf) as total_cpf ,
										    COUNT(serie_ctps) as total_ctps,
										    COUNT(pis) as total_pis,
										    COUNT(titulo) as total_titulo
											FROM rh_clt
											WHERE  status<60 AND id_regiao ='$regiao' AND id_projeto ='$projeto'
											GROUP BY nome HAVING  total_nome>1 or total_rg>1 or total_cpf>1   or  total_ctps>1  or  total_pis>1  or total_titulo>1 ");
				
				$num_duplicado_clt = mysql_num_rows($qr_duplicado_clt);				
				
			if($num_duplicado_clt !=0)
			
			while($row_duplicado_clt = mysql_fetch_assoc($qr_duplicado_clt)):
			
			
			
						$result_atividade = mysql_query("SELECT * FROM curso where id_curso = '$row_duplicado_clt[id_curso]'");
					   $row_atividade = mysql_fetch_array($result_atividade);
					   $result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$row_duplicado_clt[banco]'");
					   $row_banco = mysql_fetch_array($result_banco);
					   
					   ///PEGA A ESCOLARIDADE
						$qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE id = '$row_duplicado_clt[escolaridade]';");
						$num_escolaridade = mysql_num_rows($qr_escolaridade);
						
						if($num_escolaridade == 0) {
						
						$escolaridade = $row_duplicado_clt['escolaridade'];
							
						} else {
						
						$row = mysql_fetch_assoc($qr_escolaridade);
						
						$escolaridade = $row['nome'];
						
						}
					   //////////////////////////
					   
					   ///////PEGA ETNIAS
						$qr_etnia = mysql_query("SELECT * FROM etnias WHERE id = '$row_duplicado_clt[etnia]';");
						$etnia= mysql_fetch_assoc($qr_etnia);
					   /////////////////
						
						$nome = str_split($row_duplicado_clt['nome'], 30);
						$nomeT = sprintf("% -30s", $nome[0]);
						
							$Atividade = str_replace("CAPACITANDO ","CAP. ",$row_atividade['nome']);	
							$Escola = str_replace("ESCOLA ","E. ",$row_duplicado_clt['locacao']);
							$Escola = str_replace("MUNICIPAL ","M. ",$Escola);
							$Escola = str_replace("MUNICIPALIZADA ","Mzd. ",$Escola); 
						
						?>
                             <tr id="duplicado">
                            <td align="center"><?=$row_duplicado_clt['matricula']?></td>
                            <td align="center"><?=$nomeT?></td>
                            <td align="center"><?=$Atividade?></td>
                            <td align="center"><?=$row_duplicado_clt['pis']?></td>
                            <td align="center"><?=data($row_duplicado_clt['data_entrada'])?></td>
                          </tr>
			
            
            
            
	<?php
			$nomes_duplicados[] = trim($row_duplicado_clt['nome']); 
			$rg_duplicados[] = trim($row_duplicado_clt['rg']); 
			$cpf_duplicados[] = trim($row_duplicado_clt['cpf']); 
			$ctps_duplicados[] = trim($row_duplicado_clt['serie_ctps']); 
			$pis_duplicados[] = trim($row_duplicado_clt['pis']); 
			$titulo_duplicados[] = trim($row_duplicado_clt['titulo']); 
			endwhile;
	/////////////////////////////////////////////////////  	FIM DUPLICADO     ////////////////////////////////////////////////////		
		
		
		
		 while($row_clt = mysql_fetch_array($result_clt)) {
			
			
			
		   ///	CONDI��O PARA N�O EXIBIR OS REGISTROS DUPLICADOS
		  
		  if($num_duplicado_clt !=0) {
		  
		  
		  if(in_array(trim($row_clt['nome']),$nomes_duplicados) or in_array(trim($row_clt['rg']),$rg_duplicados) or in_array(trim($row_clt['cpf']),$cpf_duplicados) or in_array(trim($row_clt['serie_ctps']),$ctps_duplicados) or in_array(trim($row_clt['pis']),$pis_duplicados) or in_array(trim($row_clt['titulo']),$titulo_duplicados) ) continue; 
			
		  }
										
			$qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE id = '$row_clt[escolaridade]';");
			$num_escolaridade = mysql_num_rows($qr_escolaridade);
			
			
			
			if($num_escolaridade == 0) {
			
			$escolaridade = $row_clt['escolaridade'];
				
			} else {
			
			$row = mysql_fetch_assoc($qr_escolaridade);
			
			$escolaridade = $row['nome'];
			
			}					
			   
			   
			   
		   ///////PEGA ETNIAS		   
		    $qr_etnia = mysql_query("SELECT * FROM etnias WHERE id = '$row_clt[etnia]';");
			$etnia= mysql_fetch_assoc($qr_etnia);
		   
		   
		   /////////////////								
											
			$result_atividade2 = mysql_query("SELECT * FROM curso where id_curso = '$row_clt[id_curso]'");
			$row_atividade2 = mysql_fetch_array($result_atividade2);
			$result_banco2 = mysql_query("SELECT * FROM bancos where id_banco = '$row_clt[banco]'");
			$row_banco2 = mysql_fetch_array($result_banco2); ?>
            
      <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>"  <?php  if($row_duplicado_clt['total_nome'] >1 and ($row_duplicado_clt['total_rg'] >1  or  $row_duplicado_clt['total_cpf'] >1   or  $row_duplicado_clt['total_ctps'] >1  or  $row_duplicado_clt['total_pis'] >1  or  $row_duplicado_clt['total_titulo']>1)   ) {  echo 'id="duplicado"';}?> >
       
        
        <td align="center"><?=$row_clt['matricula']?></td>
        <td align="center"><?=$row_clt['nome']?></td>
        <td align="center"><?=$row_atividade2['nome']?></td>
        <td align="center"><?=$row_clt['pis']?></td>
        <td align="center"><?=data($row_clt['data_entrada'])?></td>
        </tr>
	  
<?php }


unset($nomes_duplicados,$rg_duplicados, $cpf_duplicados,$titulo_duplicados,$pis_duplicados,$ctps_duplicados,$num_duplicado_autonomo);
 ?>

       <tr class="secao">
        <td colspan="5" align="left">TOTAL DE CLTS: <?php echo $num_clt; ?></td>
      </tr>
</table>

<?php } ?>

  </td>
   </tr>
  </table>
  </body>
</html>
<?php } ?>