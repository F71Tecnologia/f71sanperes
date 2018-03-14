<?php
include('../conn.php');

if($_COOKIE['logado'] == 122) {


	$qr_funcionarios =  mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'") or die(mysql_error());
	$row_funcionario = mysql_fetch_assoc($qr_funcionarios);
		
	$qr_master_email = mysql_query("SELECT * FROM master WHERE id_master = '$row_funcionario[id_master]'");
	$row_email_master = mysql_fetch_assoc($qr_master_email);
	
	$qr_usuario = mysql_query("SELECT * FROM funcionario_email_assoc WHERE id_master = '$row_funcionario[id_master]' AND id_funcionario = '$_COOKIE[logado]'");
	$row_usuario = mysql_fetch_assoc($qr_usuario);
		
		
	$servidor = '{'.$row_email_master['email_servidor'].':143/novalidate-cert}';
	$user = $row_usuario['email'];
	$pass = $row_usuario['senha'];
	
} else {


	$qr_funcionarios =  mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'") or die(mysql_error());
	$row_funcionario = mysql_fetch_assoc($qr_funcionarios);
	$servidor = "{mail.sorrindo.org:143/novalidate-cert}";
	$user = $row_funcionario['email_login'];
	$pass = $row_funcionario['email_senha'];
}

if(isset($_GET['busca']) and !empty( $_GET['busca'])) {

?>

<style>
.result_email {
	background-color:#FCFCFC;
	cursor:pointer;
	font-size:
	}
.result_email:hover {
	background-color: #C5C5C5;
	 
}
</style>





<table width="100%">

<thead bgcolor="#EEEEEE" style="font-size:10px;font-weight:bold;background-image:url('skins/default/images/listheader.gif');">
    <td class="threads" id="rcmthreads">
    <div onclick="return rcmail.command('menu-open', 'messagelistmenu')" class="listmenu" id="listmenulink" title="Lista de opções..."/>
    </td>	
 	<td>Pasta de origem</td>
    <td >Assunto</td>
    <td>De</td>
    <td>Para</td>
    <td>Data</td>
    <td>Anexo</td>   
</thead>
<?php

$pesquisa = $_GET['busca'];
$array_tipo_pesquisa = explode('|',$_GET['tipo_pesquisa']);


//listar pastas
$mbox = imap_open($servidor.'INBOX', $user, $pass);
$pastas = imap_list($mbox,$servidor, '*');
$criterio ='';



if(sizeof($array_tipo_pesquisa) != 0){
		if(in_array(1, $array_tipo_pesquisa)){ $criterio .= ' SUBJECT "'.$pesquisa.'"'; }
		if(in_array(2, $array_tipo_pesquisa)){ $criterio .= ' FROM "'.$pesquisa.'"'; }
		if(in_array(3, $array_tipo_pesquisa)){ $criterio .= ' TO "'.$pesquisa.'"'; }
		if(in_array(4, $array_tipo_pesquisa)){ $criterio .= ' TEXT "'.$pesquisa.'"'; }	
		
					
					foreach($pastas as $pasta){	
						
				
					//BUSCA 
						$mbox = imap_open($servidor.substr($pasta, strpos($pasta, '}') +1), $user, $pass);		
						$busca = imap_search($mbox,$criterio);	
							
							
							
							
							if(empty($busca[0])) continue;
								
							foreach($busca as $email_id) {
							
								$email = @imap_headerinfo($mbox, $email_id);
								$verifica_anexo = @imap_fetchstructure($mbox, $email_id);	
								
								///verificando anexo
								if( !empty($verifica_anexo->parts[1]->dparameters[0]->value)  ) {								
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
								<tr  id="rcmrow<?php echo $email_id; ?>"  onclick="location.href='?_task=mail&_action=show&_uid=<?php echo imap_uid($mbox, $email_id); ?>&_mbox=<?php echo substr($pasta,39 ); ?>'" onmouseover="rcube_webmail.long_subject_title(this,1)" class="result_email" title="Clique para visualizar">
                                	<td></td>
							<td  class="threads"><?php echo $nome_pasta;?></td>							
									<td class="subject">                                   
                                    <span id="msgicn<?php echo $email_id; ?>" class="msgicon status"/>                                      
									<?php 									
										if(imap_utf8($email->subject) == '') { echo '(sem assunto)'; } else { echo imap_utf8($email->subject);}									
									?>                                   
                                    </td>
									<td  class="from">
										<?php 
										if($email->from[0]->personal != '') {
											echo imap_utf8($email->from[0]->personal); 
										} else {
											echo $email->from[0]->mailbox.'@'.$email->from[0]->host; 											
										}
										?>
                                    </td>
									<td  class="to"> 
										<?php 
										if($email->to[0]->personal != '') {
											echo imap_utf8($email->to->personal); 
										} else {
										echo $email->to[0]->mailbox.'@'.$email->to[0]->host; 
											
										}
										?>
                                    </td>
									<td  class="threads"><?php echo date('H:m d/m/Y',$email->udate); ?></td>
									<td><?php  echo $anexo; ?></td>
								</tr>				
						
								<?php			
									
							}
				}
				

}

imap_close($mbox);

} 
?>
</table>