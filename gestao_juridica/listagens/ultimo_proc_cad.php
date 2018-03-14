<?php 
include("include/restricoes.php");
?>
  

  
	
   

<table class="table table-striped table-hover text-sm valign-middle">
    
  
 <?php
 
$qr_processo = mysql_query("SELECT *,
                            (SELECT nome FROM processos_juridicos_nomes WHERE processos_juridicos_nomes.proc_id = processos_juridicos.proc_id ORDER BY proc_id,id_proc_jur_nome LIMIT 1) AS proc_nome
                            FROM processos_juridicos
                            INNER JOIN regioes
                            ON regioes.id_regiao = processos_juridicos.id_regiao
                            WHERE processos_juridicos.status = 1  
                            AND regioes.id_master = '$id_master' 
                            ORDER by proc_data_cad DESC LIMIT 0, 20") or die(mysql_error());

	if(mysql_num_rows($qr_processo) != 0){

	?>
    
    <thead>
        <tr class="secao">
        <td width="130">N<code>&deg;</code> DO PROCESSO</td>
       <!-- <td width="100">DATA DA DISTRIB.</td> -->
        <td>NOME</td>
        <td >PROCESSO</td>
       <td>ANDAMENTO</td>	   
       <td>CADASTRADO POR</td>
      
	 </tr>
    </thead>     

	<?php
	 while ($row_processo = mysql_fetch_assoc($qr_processo)):
	 
	$qr_n_processo = mysql_query("SELECT * FROM n_processos WHERE proc_id = '$row_processo[proc_id]' ORDER BY n_processo_ordem" ) or die(mysql_error());
	
	$qr_andamento  = mysql_query("SELECT * FROM proc_trab_andamento WHERE  proc_id= 72 AND andamento_status = 1 ORDER BY proc_status_id DESC");
	$row_andamento = mysql_fetch_assoc($qr_andamento);
	
	$tipo = @mysql_result(mysql_query("SELECT proc_tipo_nome FROM processo_tipo WHERE proc_tipo_id = '$row_processo[proc_tipo_id]'"),0);
	
	$status = @mysql_result(mysql_query("SELECT proc_status_nome FROM processo_status WHERE proc_status_id = '$row_andamento[proc_status_id]'"),0);
	
	$nome_func = @mysql_result(mysql_query("SELECT nome1 FROM funcionario WHERE id_funcionario = '$row_processo[usuario_cad]'"),0);
	
	
	$class = (($i++ % 2) == 0)? 'class="linha_um"' : 'class="linha_dois"'; 
	?>
	
    <tr <?php echo $class; ?> height="50">
    	<td>
        <?php
        while($row_n_processo = mysql_fetch_assoc($qr_n_processo)):
			
		  echo '('.$row_n_processo['n_processo_ordem'].') '.$row_n_processo['n_processo_numero'].'<br>';
		
		endwhile;
		?>
        </td>
    <!--	<td align="center"><?php echo implode('/',array_reverse(explode('-',$row_andamento['andamento_data_movi'])))?></td> -->
    	<td><?php echo $row_processo['proc_nome']; ?></td>
    	<td><?php echo $tipo?></td>
        <td align="center"><?php echo $status; ?></td>
    	<td align="center"> 
	     <?php echo $nome_func; ?> <br />
	      ( <?php echo implode('/',array_reverse(explode('-',$row_processo['proc_data_cad'])))?> )
        </td>
    </tr>
    
	 
					 
				  
<?php  
endwhile;
}
?> 
</table>		
	
