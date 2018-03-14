<?php
include ("include/restricoes.php");
$q_adv = mysql_query("SELECT * FROM advogados WHERE adv_status = 1");


?>
<table class="table table-striped table-hover text-sm valign-middle" >
<tr>
	<td colspan="10" class="titulo">
    		                                               
                  <h5> ADVOGADOS</h5>             

   </td>
</tr>




<tr class="secao">
	<td width="30%">NOME</td>
   <!-- <td>OAB</td>
    <td>RG</td>
    <td>CPF</td>--->
    <td width="10%">E-MAIL</td>
   <!---  <td width="20%">ENDERE&Ccedil;O</td>-->
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
        <td style="text-transform:lowercase;"><?php echo $row_adv['adv_email'];?></td>
      <!---   <td><?php echo $row_adv['adv_endereco'];?></td>-->
       <td align="center"><?php echo $row_adv['adv_tel'];?></td>
        <td align="center"><?php echo $row_adv['adv_cel'];?></td> 
        <td  align="center"><a href="editar_advogado.php?id=<?php echo $row_adv['adv_id'];?>&regiao=<?php echo $regiao;?>"> <img src="../imagens/editar_projeto.png"  title="Editar" width="25" height="25"/></a></td>
        <td  align="center"><a href="excluir.php?id=<?php echo $row_adv['adv_id'];?>&regiao=<?php echo $regiao;?>&tp=1"  onclick="return(confirm('Excluir o advogado:<?php echo $row_adv['adv_nome'];?>'))">
            <img src="../imagens/desativar.png"  title="Excluir" width="25" height="25"/>
            
            </a>
        </td>
               
    </tr>

<?php
endwhile;


?>
<tr>
	<td colspan="10">&nbsp;</td>
</tr>


</table>



<?php
$q_preposto = mysql_query("SELECT * FROM prepostos WHERE prep_status= 1");

if(mysql_num_rows($q_preposto) != 0) {
?>
 
        
<table class="table table-striped table-hover text-sm valign-middle" >
<thead>
<td colspan="10" class="titulo">

              <h5> PREPOSTO</h5>             

     </td>
</thead>




<tr class="secao">
	<td width="20%">NOME</td>
 
    <td width="20%">E-MAIL</td>
   <!--- <td width="%">ENDERE&Ccedil;O</td>-->
 	 <td width="20%">TEL</td>
    <td width="20%">CEL</td>   
    <td width="10%">EDITAR</td>
    <td width="10%">EXCLUIR</td>
</tr>
<?php
	while($row_preposto = mysql_fetch_assoc($q_preposto)):
$i++;
?>
	<tr class="<?php if($i % 2 == 0) { echo 'linha_um'; } else { echo 'linha_dois'; }?>">
    	<td width="30%"><?php echo $row_preposto['prep_nome'];?></td>           
        <td  style="text-transform:lowercase;" align="center"><?php echo $row_preposto['prep_email'];?></td>
       <!---  <td><?php echo $row_preposto['prep_endereco'];?></td>-->
       <td align="center"><?php echo $row_preposto['prep_tel'];?></td>
        <td align="center"><?php echo $row_preposto['prep_cel'];?></td> 
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

