<?php
$q_adv = mysql_query("SELECT * FROM advogados WHERE adv_status= 1");

if(mysql_num_rows($q_adv) != 0) {
?>

<table width="100%" class="tabela2">
 <?php
 
 $qr_status = mysql_query("SELECT * FROM processo_status WHERE 1");
 while($row_status = mysql_fetch_assoc($qr_status)):
  
	 $qr_processo = mysql_query("SELECT * FROM processo_trabalhista WHERE  proc_trab_status_processo = '$row_status[proc_status_id]'");
	 
	 
	 if(mysql_num_rows($qr_processo) != 0) {		 
	
		?> 
        <tr>
       	  <td class="titulo_tabela" colspan="5">
		  <div class="sombra1">  <?php echo $row_status['proc_status_nome'];?>                                                
                  <div class="texto">  <?php echo $row_status['proc_status_nome'];?></div>             
                 </div>
		  </td>
        </tr>
        
		  <tr class="secao">
            <td width="96">N<code>&deg;</code> DO PROCESSO</td>
           <td width="100">PR&Oacute;XIMO EVENTO</td> 
           <td>NOME</td>
           <td>LOCAL</td>
           <td>EDITAR</td>
         </tr>
		 
	    <?php
		 while($row_processo = mysql_fetch_assoc($qr_processo)):
		 extract($row_processo);
		 ?>
		<tr class="<?php if($i % 2 == 0) { echo 'linha_um'; } else { echo 'linha_dois'; }?>">
		
        	<td align="center"><?php echo $row_processo['proc_trab_numero_processo']?></td>
            <td  align="center"><?php echo implode('/', array_reverse(explode('-',$row_processo['proc_trab_data_cad'])))?></td>
            <td  align="left">
            
            <a href="/processo_trabalhista/dados_trabalhador.php?id_processo=<?php echo $row_processo['proc_trab_id'];?>"> </a>
			<?php
			if($row_processo['id_autonomo'] != 0 ) {
				
			echo' <a href="processo_trabalhista/dados_trabalhador/ver_trabalhador.php?id_processo='.$row_processo['proc_trab_id'].'">'.$row_processo['proc_trab_nome'].'</a>';
			
			} else {
			echo' <a href="processo_trabalhista/dados_trabalhador/ver_trabalhador_clt.php?id_processo='.$row_processo['proc_trab_id'].'">'.$row_processo['proc_trab_nome'].'</a>';
			
			}
			?>
            
            </td>
            <td align="left"><?php echo $row_processo['proc_trab_local']?></td>     
            <td align="left">
            <?php 
			if($row_processo['id_autonomo'] != 0 ) {
            
                 echo ' <a href="processo_trabalhista/edit_processo_coop.php?id_processo='.$row_processo['proc_trab_id'].'&tp='.$row_processo['id_autonomo'].'&regiao='.$regiao.'">  <img src="../imagens/editar_projeto.png"  title="Editar" width="25" height="25"/></a>';          
		
			}else {
			
			     echo ' <a href="processo_trabalhista/edit_processo_clt.php?id_processo='.$row_processo['proc_trab_id'].'&regiao='.$regiao.'">  <img src="../imagens/editar_projeto.png"  title="Editar" width="25" height="25"/></a>';          
			}
			?>
        
		</tr> 
		 <?php
		 
		 endwhile;
	}
	 	 
		 if($row_status['proc_status_id'] != $status_anterior ){
		 
		 echo '<tr><td>&nbsp;</td></tr>';
		 
		 
		 }
	 $status_anterior = $row_status['proc_status_id']; 
 endwhile;
 
 

 ?>
 

        
<table width="100%"  class="tabela2" >
<tr>
	<td colspan="10" class="titulo_tabela">
    		<div class="sombra1"> ADVOGADOS                                                
                  <div class="texto"> ADVOGADOS</div>             
                 </div>
   </td>
</tr>




<tr class="secao">
	<td width="30%">NOME</td>
   <!-- <td>OAB</td>
    <td>RG</td>
    <td>CPF</td>--->
    <td width="10%">E-MAIL</td>
    <td width="20%">ENDERE&Ccedil;O</td>
 	 <td width="20%">TEL</td>
    <td width="20%">CEL</td>   
    <td width="10%">EDITAR</td>
    <td width="10%">EXCLUIR</td>
</tr>
<?php
	while($row_adv = mysql_fetch_assoc($q_adv)):
$i++;
?>
	<tr class="<?php if($i % 2 == 0) { echo 'linha_um'; } else { echo 'linha_dois'; }?>">
    	<td width="30%"><?php echo $row_adv['adv_nome'];?></td>
      <!--  <td><?php echo $row_adv['adv_oab'];?></td>
        <td><?php echo $row_adv['adv_rg'];?></td>
        <td><?php echo $row_adv['adv_cpf'];?></td>-->
        <td><?php echo $row_adv['adv_email'];?></td>
        <td><?php echo $row_adv['adv_endereco'];?></td>
       <td><?php echo $row_adv['adv_tel'];?></td>
        <td><?php echo $row_adv['adv_cel'];?></td> 
        <td  align="center"><a href="editar_advogado.php?id=<?php echo $row_adv['adv_id'];?>&regiao=<?php echo $regiao;?>"> <img src="../imagens/editar_projeto.png"  title="Editar" width="25" height="25"/></a></td>
        <td  align="center"><a href="excluir.php?id=<?php echo $row_adv['adv_id'];?>&regiao=<?php echo $regiao;?>&tp=1"  onclick="return(confirm('Excluir o advogado:<?php echo $row_adv['adv_nome'];?>'))">
            <img src="../imagens/desativar.png"  title="Excluir" width="25" height="25"/>
            
            </a>
        </td>
               
    </tr>

<?php
endwhile;

}
?>
<tr>
	<td colspan="10">&nbsp;</td>
</tr>


</table>



<?php
$q_preposto = mysql_query("SELECT * FROM prepostos WHERE prep_status= 1");

if(mysql_num_rows($q_preposto) != 0) {
?>
 
        
<table width="100%"  class="tabela2">
<tr>
	<td colspan="10" class="titulo_tabela">
    		<div class="sombra1"> PREPOSTO                                                
                  <div class="texto"> PREPOSTO</div>             
                 </div>
   </td>
</tr>




<tr class="secao">
	<td width="70">NOME</td>
 
    <td width="30">E-MAIL</td>
    <td width="%">ENDERE&Ccedil;O</td>
 	 <td width="">TEL</td>
    <td width="">CEL</td>   
    <td width="">EDITAR</td>
    <td width="">EXCLUIR</td>
</tr>
<?php
	while($row_preposto = mysql_fetch_assoc($q_preposto)):
$i++;
?>
	<tr class="<?php if($i % 2 == 0) { echo 'linha_um'; } else { echo 'linha_dois'; }?>">
    	<td width="30%"><?php echo $row_preposto['prep_nome'];?></td>           
        <td><?php echo $row_preposto['prep_email'];?></td>
        <td><?php echo $row_preposto['prep_endereco'];?></td>
       <td><?php echo $row_preposto['prep_tel'];?></td>
        <td><?php echo $row_preposto['prep_cel'];?></td> 
        <td  align="center"><a href="editar_preposto.php?id=<?php echo $row_preposto['prep_id'];?>&regiao=<?php echo $regiao;?>"> <img src="../imagens/editar_projeto.png"  title="Editar" width="25" height="25"/></a></td>
        <td  align="center"><a href="excluir.php?id=<?php echo $row_preposto['prep_id'];?>&regiao=<?php echo $regiao;?>&tp=2" onclick="return(confirm('Excluir o preposto:<?php echo $row_preposto['prep_nome'];?>'))">
        <img src="../imagens/desativar.png"  title="Editar" width="25" height="25"/>
        </a>
        </td>
               
    </tr>

<?php
endwhile;

}
?>
<tr>
	<td colspan="10">&nbsp;</td>
</tr>


</table>

