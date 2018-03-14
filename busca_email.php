<?php

$servidor = "{mail.sorrindo.org:143/novalidate-cert}";
$user = 'anderson@sorrindo.org';
$pass = 'as3399';


if(isset($_POST['enviar']) and !empty( $_POST['busca'])) {

?>

<table border="0" width="100%">

<tr> 
	<td>Pasta</td>
    	<td>Assunto</td>
    <td>Remetente</td>
    <td>Para</td>
    <td>Data</td>
    <td>Anexo</td>
   
</tr>
<?php

$pesquisa = $_POST['busca'];
$array_tipo_pesquisa = $_POST['tipo_pesquisa'];


//listar pastas
$mbox = imap_open($servidor.'INBOX', $user, $pass);
$pastas = imap_list($mbox,$servidor, '*');
$criterio ='';


if(sizeof($array_tipo_pesquisa) != 0){
		if(in_array(1, $array_tipo_pesquisa)){ $criterio .= 'SUBJECT "'.$pesquisa.'"'; }
		if(in_array(2, $array_tipo_pesquisa)){ $criterio .= 'FROM "'.$pesquisa.'"'; }
		if(in_array(3, $array_tipo_pesquisa)){ $criterio .= 'TO "'.$pesquisa.'"'; }
		if(in_array(4, $array_tipo_pesquisa)){ $criterio .= 'TEXT "'.$pesquisa.'"'; }	
		
					
					foreach($pastas as $pasta){	
						
					
					//BUSCA 
						$mbox = imap_open($servidor.substr($pasta,39 ), $user, $pass);		
						$busca = imap_search($mbox,$criterio);	
							
							
							
							if(empty($busca[0])) continue;
							
							foreach($busca as $email_id) {
							
								$email = @imap_headerinfo($mbox, $email_id);
								$verifica_anexo = @imap_fetchstructure($mbox, $email_id);	
								
								///verificando anexo
								if( $verifica_anexo->parts[1]->dparameters[0]->value != '' ) {
								
								$anexo = 'sim';
								
									
								}
								
								
								
									
								//nome da pasta	
								switch(substr($pasta,45 )) {
								
								case 'Trash': $nome_pasta = 'Lixeira';
								break;
								
								case 'Drafts': $nome_pasta = 'Rascunho';
								break;
								
								case 'Sent': $nome_pasta = 'Enviados';
								break;
								
								case '': $nome_pasta = 'Caixa de entrada';
								break;
								
								default: $nome_pasta = substr($pasta,45 );
								
									
								}
							
							
							
							
								
								?>
								<tr>
									<td><?php echo $nome_pasta;?></td>									
									<td>
                                   
                                       
									<?php 
									if(imap_utf8($email->subject) == '') { echo '(sem assunto)'; } else { echo utf8_encode(imap_utf8($email->subject));}
									
									?>
                                    
                                    </td>
									<td>
										<?php 
										if($email->from[0]->personal != '') {
											echo $email->from[0]->personal; 
										} else {
										echo $email->from[0]->mailbox.'@'.$email->from[0]->host; 
											
										}
										?>
                                    </td>
									<td>
										<?php 
										if($email->to[0]->personal != '') {
											echo $email->to->personal; 
										} else {
										echo $email->to[0]->mailbox.'@'.$email->to[0]->host; 
											
										}
										?>
                                    </td>
									<td><?php echo date('H:m d/m/Y',$email->udate); ?></td>
									<td><?php  echo $anexo; ?></td>
								</tr>				
						
								<?php			
									
							}
				}
				

}
imap_close($mbox);

} 
?>
<form name="form" method="post" action="" >
<table>
<tr>
	<td><input name="busca" type="text" id="busca"/> </td>
    <td><input name="enviar" type="submit"  value="Enviar" id="enviar"/></td>
</tr>
<tr>
	<td><input type="checkbox" name="tipo_pesquisa[]" value="1"/> Assunto</td>
    <td><input type="checkbox" name="tipo_pesquisa[]" value="2"/> Remetente</td>
    <td><input type="checkbox" name="tipo_pesquisa[]" value="3"/> Para</td>
    <td><input type="checkbox" name="tipo_pesquisa[]" value="4"/> Mensagem inteira</td>
    
</tr>
</table>
</form>
<?php
	










?>