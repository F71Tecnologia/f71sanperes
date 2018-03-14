<?php
include('conn.php');

		$qr_master_email = mysql_query("SELECT master.email_servidor, funcionario_email_assoc.email as email, funcionario_email_assoc.senha as senha, master.id_master, master.sigla  FROM master 
										INNER JOIN  funcionario_email_assoc
										ON master.id_master = funcionario_email_assoc.id_master
										WHERE master.status = 1 AND funcionario_email_assoc.id_funcionario = '$_COOKIE[logado]' ");
		while( $row_email_master = mysql_fetch_assoc($qr_master_email)):
		
		
			$verifica_email =	mysql_num_rows($qr_master_email);				
			if($verifica_email != 0){	
				
				$servidor = '{'.trim($row_email_master['email_servidor']).':143/novalidate-cert}INBOX';							
				$mbox = @imap_open($servidor , trim($row_email_master['email'])	 ,trim($row_email_master['senha'])) ;	
				
				$erro = imap_last_error();	
				//echo $erro;
			
				$status = imap_status($mbox, $servidor,SA_ALL);	
				
				$img = ($status->unseen ==0)?'email3.png':'email2.png';
				$style = ($status->unseen !=0)?'style="text-weight:bold;"' :'';		
				$color = ($i++ % 2 == 0)? '#F5F5F5' : '#F7F7F7';
				?>
		        
		        	<div style="height:10px;padding:10px;background-color:<?php echo $color?>; float:left; font-size:12px; margin-left:0px;margin-bottom:2px; border-left:solid #FFF 4px;">
		            	
						<?php echo $row_email_master['sigla']; ?> <span style="font-size:10px; font-weight:bold;"> (<?php echo $status->unseen?>) </span>
		                <input type="hidden" name="master[]" value="<?php echo $row_email_master['id_master']?>" rel="<?php echo $status->unseen?>" class="master_id"/> 
		                </td>
		            </div>
		        <?php
		        
				imap_close($mbox);
				
			}
			endwhile;		
?>