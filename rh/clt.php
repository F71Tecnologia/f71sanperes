<?php
if(empty($_COOKIE['logado'])){
	print 'Efetue o Login<br><a href="login.php">Logar</a>';
	exit;
}

header('Content-Type:text/html; charset=ISO-8859-1', true);

include('../conn.php');
include('../wfunction.php');
include('../funcoes.php');
include('../classes/abreviacao.php');
include('../classes/formato_data.php');

$usuario = carregaUsuario();

$regiao = $usuario['id_regiao'];

$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_Funcionario= '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user);

$qr_master  = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]' ");
$row_master =  mysql_fetch_assoc($qr_master);



?>
<html>
<head>
<title>:: Intranet :: Edi&ccedil;&atilde;o</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="../favicon.ico">
<link href="folha/sintetica/folha.css" rel="stylesheet" type="text/css">
<script src="../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
<script type="text/javascript">
$().ready(function(){
	$('.show').click(function() {
		$('#eventos table').hide();
		var div = $(this).attr('href');
		$(div).show();
		$(this).addClass('ativo');
		$('.show').not(this).removeClass('ativo');
	});
	$('#botao_localizacao').click(function() {
		$('#localizacao').show();
		$('#botao_localizacao').hide();
	});
	$('#fecha_localizacao').click(function() {
		$('#localizacao').hide();
		$('#botao_localizacao').show();
	});
});

function ajaxFunction(){
var xmlHttp;
try
  {
  xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  try
    {
    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    try
      {
      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
    catch (e)
      {
      alert("Your browser does not support AJAX!");
      return false;
      }
    }
  }
  xmlHttp.onreadystatechange=function() {
    if(document.getElementById('username').value == ''){
		document.all.ttdiv.style.display="none";
	}else{
		document.all.ttdiv.style.display="";
		if(xmlHttp.readyState==3){
			document.all.spantt.innerHTML="<div align='center' style='background-color:#5C7E59'><img src='../imagens/carregando/CIRCLE_BALL.gif' align='absmiddle'>Aguarde</div>";
		}else if(xmlHttp.readyState==4){
		  document.all.spantt.innerHTML=xmlHttp.responseText;
      }
    }
  }

  var enviando = escape(document.getElementById('username').value);
  xmlHttp.open("GET",'clt.php?procura=' + enviando + '&id=1&regiao=<?=$regiao?>',true);
  xmlHttp.send(null);
  
}
</script>
<style type="text/css">
.show,.ativo {
	font-weight:bold; font-size:18px; font-family:'Trebuchet MS', Arial, Helvetica, sans-serif; background-color:#f5f5f5; border:1px solid #e5e5e5; margin:0px auto; margin-top:5px; width:97%; padding:10px; display:block;
}
.ativo {
	background-color:#FC9 !important; color:#222; border:0;
}
.show:hover {
	background-color:#e5e5e5; color:#222;
}
tr.novo_tr {
	background-color:#dee3ed; color:#000; padding:8px; text-align:left; font-weight:bold; font-size:13px; border-top:1px solid #777;
}
tr.novo_tr td {
	font-weight:bold; border-top:1px solid #777;
}
#eventos table {
	margin:5px auto; text-align:left; width:98%; font-size:11px; line-height:40px;
}
#eventos table .linha_um td, #eventos table .linha_dois td {
	border-bottom:1px solid #ccc;
}
td.td_show {
	font-weight:bold; font-size:18px; font-family:'Trebuchet MS', Arial, Helvetica, sans-serif; background-color:#f5f5f5; border:1px solid #ddd; margin-top:5px;
}
td.td_show .seta {
	 color:#F90; font-size:32px;
}
.nota {
	width:10px; height:10px; -moz-border-radius:3px; -webkit-border-radius:3px; border-radius:3px; margin-left:10px;
}
.vermelho {
	background-color:#C30;	
}
.vermelho_claro {
	background-color:#FF0500;	
}
.amarelo {
	background-color:#F5B200;
}
.verde_claro {
	background-color:#868652;
}
.verde {
	background-color:#4B4B00;
}
.descricao {
	color:#777; font-style:italic; font-size:12px;
}
#localizacao {
	background-color:#FFC;
	border:1px solid #FC0;
	padding:4px;
	clear:both;
	width:620px;
	float:right;
}
#localizacao a {
	color:#444;
	font-size:13px;
	margin-top:3px;
}
a.busca {
	text-decoration:none;
	display:block;
	padding:3px;
	padding-left:5px;
}
a.busca:hover {
	background-color:#069;
	color:#FFF;
}
#Flutuante {
	position:absolute;
	width:327px;
	height:44px;
	z-index:1;
	left:9px;
	top:16px;
	float:left;
}
</style>
</head>
<body>

<?php // Tela para Busca Avançada
	  if(empty($_REQUEST['id'])) { ?>
      
<div id="corpo">
<table cellspacing="4" cellpadding="0" id="topo">
<tr>
    <td  align="right">
        <?php include('../reportar_erro.php');?>
    </td>
  <tr>
    <td valign="middle" align="center">
          <img src="../imagens/logomaster<?php echo $row_master['id_master'];?>.gif" width="110" height="79">
          <h2>Edi&ccedil;&atilde;o de Participantes em <?php echo @mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao'"),0); ?></h2>
    </td>
  </tr>
</table>

<!--<table style="width:900px; margin:0px auto;">
  <tr>
    <td><div class="nota verde"></div></td>
    <td class="descricao">regularizado com foto</td>
    <td><div class="nota verde_claro"></div></td>
    <td class="descricao">regularizado sem foto</td>
    <td><div class="nota amarelo"></div></td>
    <td class="descricao">regularizado com observa&ccedil;&otilde;es</td>
    <td><div class="nota vermelho_claro"></div></td>
    <td class="descricao">faltam informa&ccedil;&otilde;es</td>
    <td><div class="nota vermelho"></div></td>
    <td class="descricao">faltam informa&ccedil;&otilde;es importantes</td>
  </tr>
</table>-->

<table style="width:98%; margin:0 auto;">
  <tr>
    <td colspan="10">
    
    	<table width="100%;">
          <tr>
            <td width="33%"><a href="principalrh.php?regiao=<?=$regiao?>" class="voltar" style="font-size:11px; float:left; width:50px; margin:10px auto;">Voltar</a></td>
            <td width="34%"><a href="cadastroclt.php?regiao=<?=$regiao?>&pagina=clt" class="ancora" style="font-size:11px; float:none; width:inherit; width:110px; margin:10px auto;">Novo Cadastro</a></td>
            <td width="33%"><a href="#corpo" class="ancora" id="botao_localizacao" style="font-size:11px; float:right; width:142px; margin:10px auto; text-align:right;">Localizar Participante</a></td>
          </tr>
        </table>
            
        <div id="localizacao" class="localizacao" style="display:none;">
            <a id="fecha_localizacao" style="float:right; cursor:pointer;">x fechar</a>
            <form>
             <input type="text" name="username" value="Insira o nome do participante" onBlur="if(this.value=='') {this.value='Insira o nome do participante';}" onFocus="if(this.value=='Insira o nome do participante') {this.value='';}; getPosicaoElemento();" onKeyUp="ajaxFunction();getPosicaoElemento();" id="username" style="color:#999; font-style:italic; width:550px; float:left;" /><img src='imagens/carregando/CIRCLE_BALL.gif' style="display:none;">
            </form>
            <div class="clear"></div>
            <div id="Flutuante" style="float:left;">
              <table border="0" cellpadding="0" cellspacing="0" id="ttdiv" style="border:solid 1px #000; display:none;" background="imagens/trans.png">
                 <tr>
                   <td><span style="font-size:13px;" id="spantt"></span></td>
                 </tr>
              </table>
            </div>
        </div>
        
    </td>
  </tr>
</table>

<div id="eventos">
<?php // Eventos que exibirei
      // $eventos = array('= 40','= 200','= 20','= 50','= 90','= 10','>= 60 AND codigo != 90 AND codigo != 200');
	  $eventos = array('= 200','= 20','= 30','= 40','= 50','= 51','= 52','= 70','= 80','= 90','= 100','= 110','= 10','= 60','= 61','= 62','= 63','= 64','= 65','= 81','= 101');
	  $eventos_rescisao = array('60','61','62','63','64','65','81','101');
	  foreach($eventos as $evento) {
		  
	  $status++;

      // Consulta dos Eventos
      $qr_status = mysql_query("SELECT * FROM rhstatus WHERE status_reg = '1' AND codigo $evento");
	  
	  // Loop dos Eventos
      while($row_status = mysql_fetch_assoc($qr_status)) {
		     
			 // Consulta dos Participantes
		     $qr_participantes    = mysql_query("SELECT b.nome AS nome_projeto, a.id_projeto, a.id_curso, a.data_entrada, a.id_clt, a.status, a.nome, a.assinatura, a.distrato, a.outros, a.campo3, a.locacao, a.foto, a.observacao
												   FROM rh_clt a
											  LEFT JOIN projeto b
													 ON a.id_projeto = b.id_projeto
												  WHERE a.id_regiao  = '$regiao'
												    AND b.status_reg = '1'
												    AND a.status     = '$row_status[codigo]'
											   ORDER BY b.id_projeto, a.nome ASC");
	   		 $total_participantes = mysql_num_rows($qr_participantes);

			 // Verificando se existe Participantes no Evento
			 if(!empty($total_participantes)) {
				 
			   $primeiro++;
               
			   // Loop dos Participantes
			   while($row_participante = mysql_fetch_assoc($qr_participantes)) {
				   
				 $projeto = $row_participante['id_projeto'];
			     $id_clt  = $row_participante['id_clt'];
				 
				 // Verificando se é um novo Evento
				 if($ultimo_status != $row_status['especifica']) { ?>
  
                      <a class="show" href=".<?=$status?>" onClick="return false">
                        <?php echo $row_status['especifica']; ?> (<?=$total_participantes?>) 
                      </a>
                          
				 <?php } // Fim da verificação de novo Evento
                  
                 // Verificando se é um novo Projeto
                 if($ultimo_projeto != $row_participante['nome_projeto']) {
					 
					 // Fechando a tabela do último projeto
					 if($ultimo_status == $row_status['especifica']) { ?>
                 		
                        </table>
                        
                     <?php } ?>
                     
                 		<table cellpadding="0" cellspacing="1" class="<?=$status?>" <?php if($primeiro != 1) { echo 'style="display:none;"'; } ?>>
						  <tr>
							<td colspan="7" class="td_show">
							  &nbsp;<span class="seta">&#8250;</span> <?php echo $row_participante['nome_projeto']; ?> 
							</td>
						  </tr>
						  <tr class="novo_tr">
                            <td>&nbsp;</td>
							<td width="5%" align="center">COD</td>
							<td width="30%">&nbsp;&nbsp;NOME</td>
							<td width="25%">&nbsp;&nbsp;CARGO</td>
							<td width="20%" align="center"><?php if($row_participante['status'] == 10) { echo 'ENTRADA'; } else { echo 'DURA&Ccedil;&Atilde;O'; } ?></td>
           					<td width="10%" align="center">PONTO</td>
                            <td width="10%" align="center">DOCUMENTOS</td>
						  </tr>
                          
                 <?php } // Fim da verificação de novo Projeto
						  
					   $qr_curso     = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_participante[id_curso]'");
					   $row_curso    = mysql_fetch_array($qr_curso);
						
					   $qr_evento    = mysql_query("SELECT * FROM rh_eventos WHERE id_clt = '$id_clt' AND cod_status = '$row_participante[status]' ORDER BY id_evento DESC");
					   $row_evento   = mysql_fetch_array($qr_evento);
					   
					   $qr_ferias    = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id_clt' ORDER BY id_ferias DESC");
					   $row_ferias   = mysql_fetch_array($qr_ferias);
					   
					   $qr_rescisao  = mysql_query("SELECT * FROM rh_recisao WHERE id_clt = '$id_clt' ORDER BY id_recisao DESC");
					   $row_rescisao = mysql_fetch_array($qr_rescisao);
					   
					    if($row_participante['assinatura'] == 1) {
							$botao1 = '<a href="ver_tudo.php?id=18&projeto='.$projeto.'&regiao='.$regiao.'&ass=0&bolsista='.$id_clt.'&tipo=1&tab=rh_clt" title="Remover ASSINATURA do Contrato de '.$row_participante['nome'].'">
										  <img src="../imagens/assinado.gif" alt="Contrato">
									   </a>';
						} else {
							$botao1 = '<a href="ver_tudo.php?id=18&projeto='.$projeto.'&regiao='.$regiao.'&ass=1&bolsista='.$id_clt.'&tipo=1&tab=rh_clt" title="Alterar o Contrato para ASSINADO de '.$row_participante['nome'].'">
										  <img src="../imagens/naoassinado.gif" alt="Contrato">
									   </a>';
						}
						
						if($row_participante['distrato'] == 1) {
							$botao2 = '<a href="ver_tudo.php?id=18&projeto='.$projeto.'&regiao='.$regiao.'&ass=0&bolsista='.$id_clt.'&tipo=2&tab=rh_clt" title="Remover ASSINATURA do Distrato de '.$row_participante['nome'].'">
										  <img src="../imagens/assinado.gif" alt="Distrato">
									   </a>';
						} else {
							$botao2 = '<a href="ver_tudo.php?id=18&projeto='.$projeto.'&regiao='.$regiao.'&ass=1&bolsista='.$id_clt.'&tipo=2&tab=rh_clt" title="Alterar o Distrato para ASSINADO de '.$row_participante['nome'].'">
										  <img src="../imagens/naoassinado.gif" alt="Distrato">
									   </a>';
						}
						
						if($row_participante['outros'] == 1) {
							$botao3 = '<a href="ver_tudo.php?id=18&projeto='.$projeto.'&regiao='.$regiao.'&ass=0&bolsista='.$id_clt.'&tipo=3&tab=rh_clt" title="Remover ASSINATURA de Outros Documentos de '.$row_participante['nome'].'">
										  <img src="../imagens/assinado.gif" alt="Outros Documentos">
									   </a>';
						} else {
							$botao3 = '<a href="ver_tudo.php?id=18&projeto='.$projeto.'&regiao='.$regiao.'&ass=1&bolsista='.$id_clt.'&tipo=3&tab=rh_clt" title="Alterar Outros Documentos para ASSINADO de '.$row_participante['nome'].'">
										  <img src="../imagens/naoassinado.gif" alt="Outros Documentos">
									   </a>';
						}

						if(strstr($row_participante['campo3'],'INSERIR')) { 
							$classe = 'amarelo'; 
						} elseif(strstr($row_participante['locacao'],'A CONFIRMAR')) { 
							$classe = 'vermelho'; 
						} elseif($row_participante['foto'] == '1') {
							$classe = 'verde_foto'; 
						} elseif(!empty($row_participante['observacao'])) {
							$classe = 'amarelo'; 
							$observacao = 'title="Observações: '.$row_participante['observacao'].'"';
						} else {
							$classe = 'verde';
						} ?>
							
                        <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
                            <td class="<?=$classe?>">&nbsp;</td>
                            <td><?=$id_clt?></td>
                            <td align="left">
                              <a href="ver_clt.php?reg=<?=$regiao?>&clt=<?=$id_clt?>&pro=<?=$projeto?>&pagina=clt" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )" class="participante" title="Editar cadastro de <?=$row_participante['nome']?>">
                                <?=abreviacao($row_participante['nome'], 4, 1)?> <img src="folha/sintetica/seta_<?php if($seta++%2==0) { echo 'um'; } else { echo 'dois'; } ?>.gif">
                              </a>
                            </td>
                            <td align="left">&nbsp;&nbsp;<?=str_replace('CAPACITANDO EM', '', $row_curso['nome'])?></td>
                            <td><?php if($row_participante['status'] == 40) {
									      echo formato_brasileiro($row_ferias['data_ini']).' - '.formato_brasileiro($row_ferias['data_fim']);
									  } elseif(in_array($row_participante['status'],$eventos_rescisao)) {
                                          echo formato_brasileiro($row_rescisao['data_adm']).' - '.formato_brasileiro($row_rescisao['data_demi']);
							          } elseif($row_participante['status'] != 10) {
                                          echo formato_brasileiro($row_evento['data']).' - '.formato_brasileiro($row_evento['data_retorno']);
                                      } else {
                                          echo formato_brasileiro($row_participante['data_entrada']);
                                      } ?></td>
                            <td><a href="../folha_ponto.php?id=2&unidade=&regiao=<?=$regiao?>&pro=<?=$projeto?>&id_bol=<?=$id_clt?>&tipo=clt" title="Gerar folha de ponto para <?=$row_participante['nome']?>">Gerar</a></td>
                            <td><?=$botao1.' '.$botao2.' '.$botao3?></td>
                        </tr>
                        
				 <?php $ultimo_projeto = $row_participante['nome_projeto'];
				 	   $ultimo_status  = $row_status['especifica'];
			 
			    } // Fim do Loop de Participante
				
				 unset($ultimo_projeto);
				
				// Verificação de Âncora
		 		if($total_participantes > 7) { ?>
           	   <tr>
                  <td colspan="7"><a href="#corpo" class="ancora">Subir ao topo</a></td>
               </tr>
               
         <?php } // Fim da verificação de Âncora ?>
         
           </table>
           
	 <?php } // Fim da verificação se existe Participantes
	 
	  } // Fim da Loop de Status
	  
} // Fim dos Eventos ?>

</div>
</div>

<?php } else {

$id = $_REQUEST['id'];

switch($id) {
case 1:

$busca  = $_REQUEST['procura'];
$regiao = $_REQUEST['regiao'];

$qr_busca    = mysql_query("SELECT id_clt, nome, id_regiao, id_projeto FROM rh_clt WHERE id_regiao = '$regiao' AND nome LIKE '%$busca%' AND status != '' AND status != '0'");
$total_busca = mysql_num_rows($qr_busca);

if(empty($total_busca) or empty($busca) or strlen($busca) == 1 or strlen($busca) == 2) {
	echo '<a href="#" style="color:#C30; text-decoration:none; display:block; padding:3px; padding-left:5px;">Sua busca n&atilde;o retornou resultado</a>';
} else {
	while($row_busca = mysql_fetch_array($qr_busca)) {
		echo '<a class="busca"   
			  href="ver_clt.php?reg='.$row_busca['id_regiao'].'&clt='.$row_busca[0].'&pro='.$row_busca['id_projeto'].'&pagina=clt"
			  onclick="document.all.ttdiv.style.display=none; 
			  document.all.username.value='.$row_busca['nome'].'">'.$row_busca['nome'].'</a>';
	}
}

break;

}
}
?>
</body>
</html>