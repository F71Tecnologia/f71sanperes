<?php
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../login.php">Logar</a>';
	exit;
}

include('../conn.php');
include('../funcoes.php');
include('../upload/classes.php');
include('../classes/funcionario.php');
include('../classes/formato_data.php');
include('../classes/formato_valor.php');
include('../classes_permissoes/acoes.class.php');

$Fun = new funcionario();
$Fun -> MostraUser(0);
$Master = $Fun -> id_master;
$ACOES = new Acoes();
//PEGANDO O ID DO CADASTRO

$id      = 1;
$id_clt  = $_REQUEST['clt'];
$id_ant  = $_REQUEST['ant'];
$id_pro  = $_REQUEST['pro'];
$id_reg  = $_REQUEST['reg'];
$id_user = $_COOKIE['logado'];

$pagina = $_REQUEST['pagina'];

$sql_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($sql_user);

$result = mysql_query(" SELECT *, date_format(data_entrada, '%d/%m/%Y') AS nova_data, date_format(data_saida, '%d/%m/%Y') AS data_saida2, date_format(dataalter, '%d/%m/%Y') AS dataalter2 FROM rh_clt WHERE id_clt = $id_clt");
$row    = mysql_fetch_array($result);

$result_data_entrada = mysql_query("
SELECT data_entrada, DATE_ADD(data_entrada, INTERVAL '90' DAY) AS data_contratacao, CASE WHEN data_entrada < DATE_SUB(CURDATE(), INTERVAL '90' DAY) THEN 'Contratado' WHEN data_entrada > DATE_SUB(CURDATE(), INTERVAL '90' DAY) AND data_entrada <= CURDATE() THEN 'Em experiência até ' ELSE 'Aguardando' END AS status_contratacao FROM rh_clt WHERE id_clt = '$id_clt'") or die(mysql_error());
$row2 = mysql_fetch_assoc($result_data_entrada);

$data_contratacao   = implode('/', array_reverse(explode('-', $row2['data_contratacao'])));
$status_contratacao = $row2['status_contratacao'];

$result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$id_pro'");
$row_pro    = mysql_fetch_array($result_pro);

$sql_user2 = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$row[useralter]'");
$row_user2 = mysql_fetch_array($sql_user2);

$result_ban = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$id_reg' AND id_projeto = '$id_pro'");

if($row['status'] == '62') {
	$texto = "<font color=red><b>Data de saída:</b> $row[data_saida2]</font><br>";
} else {
	$texto = NULL;
}

$nome_para_arquivo = $row['1'];
	
if($row['foto'] == '1') {
	$nome_imagem = $id_reg.'_'.$id_pro.'_'.$row['0'].'.gif';
} else {
	$nome_imagem = 'semimagem.gif';
}

$qr_status = mysql_query("SELECT tipo FROM rhstatus WHERE codigo = '$row[status]'");
$ativo = (mysql_result($qr_status,0)=="recisao")?false:true;

?>
<html>
<head>
<title>:: Intranet ::</title>
<link rel='shortcut icon' href='../favicon.ico'>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/estrutura_participante.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../SpryAssets/SpryAccordion.js"></script>
<script type="text/javascript" src="../js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../uploadfy/scripts/swfobject.js"></script>
<script type="text/javascript" src="../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>
<script type="text/javascript" src="../js/shadowbox.js"></script>
<link rel="stylesheet" type="text/css" href="../js/shadowbox.css">
<link rel="stylesheet" type="text/css" href="css/spry.css">
<link rel="stylesheet" type="text/css" href="../uploadfy/css/default.css" />
<link rel="stylesheet" type="text/css" href="../uploadfy/css/uploadify.css" />

<link href="../js/highslide.css" rel="stylesheet" type="text/css"  /> 
<script type="text/javascript" src="../js/highslide-with-html.js"></script> 
<script type="text/javascript">
Shadowbox.init();
</script>
<script type="text/javascript">


hs.graphicsDir = '../images-box/graphics/';
hs.outlineType = 'rounded-white';

$().ready(function(){
		<?php if($row['foto'] == '1') { ?>
			$("#bt_deletar").show();
		<?php }?>

			$("#fileQueue").hide();
			$("#bt_deletar").click(function(){
				$.post('../include/excluir_foto.php',
					   {nome: '<?=$_GET['reg']?>_<?=$_GET['pro']?>_<?=$_GET['clt']?>.gif', clt : '<?=$_GET['clt']?>'},
					   function(){
							$("#imgFile").attr('src','../fotos/semimagem.gif');
							$("#bt_deletar").hide();
							$('#bt_enviar').uploadifySettings('buttonText','Adicionar foto');							
						}
					   
					   );
			});
			
			$("#bt_enviar").uploadify({
				'uploader'       : '../uploadfy/scripts/uploadify.swf',
				'script'         : '../uploadfy/scripts/uploadify.php',
				'folder'         : '../../../fotos',
				'buttonText'     : '<?php if($row['foto'] == '1') { ?>Alterar<?php } else { ?>Adicionar<?php } ?> foto',
				'queueID'        : 'fileQueue',
				'cancelImg'      : '../uploadfy/cancel.png',
				'auto'           : true,
				'method'         : 'post',
 				'multi'          : false,
				'fileDesc'       : 'Gif',
				'fileExt'        : '*.gif;*.jpg;',
				'onOpen'         : function(){
										$("#fileQueue").show();
									},
				'onAllComplete'  : function(){
									$("#bt_deletar").show('slow'); 
									$('#imgFile').attr('src' , '../fotosclt/<?=$_GET['reg']?>_<?=$_GET['pro']?>_<?=$_GET['clt']?>.gif');									
									$("#fileQueue").hide('slow');
									$('#bt_enviar').uploadifySettings('buttonText','Alterar foto');
									},
				'scriptData'     : {'regiao' : <?=$_GET['reg']?>,'projeto': <?=$_GET['pro']?>,'clt' : <?=$_GET['clt']?>}
			});
							   
});
</script>
<link rel="stylesheet" type="text/css" href="../js/highslide.css" />
<link rel="stylesheet" href="../js/lightbox.css" type="text/css" media="screen" />
</head>
<body>
<div id="fileQueue"></div>
<div id="corpo">
<div id="conteudo">
<table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
  <tr>
  <td colspan="2">
  
   <div style="float:right;"><?php include('../reportar_erro.php'); ?></div>
  <div style="clear:right;"></div>
  
  <?php if($_GET['sucesso'] == 'cadastro') { ?>
  <div id="sucesso">
       Participante cadastrado com sucesso!
  </div>
  <?php } ?>
  <div style="border-bottom:2px solid #F3F3F3; margin-top:10px;">
       <h2 style="float:left; font-size:18px;">VISUALIZAR <span class="clt">CLT</span> <br><br>
       MATRÍCULA: <?php echo formato_matricula($row['matricula']) ?>
       </h2>
       <p style="float:right;">
       <?php if($_GET['sucesso'] == 'cadastro') { ?>
           <a href="cadastroclt.php?regiao=<?=$id_reg?>&projeto=<?=$id_pro?>">&laquo; Cadastrar Outro Participante</a>
       <?php } else { if($_GET['pagina'] == 'clt') { ?>
           <a href="clt.php?regiao=<?=$id_reg?>">&laquo; Visualizar Participantes</a>
       <?php } elseif($_GET['pagina'] == 'bol') { ?>
           <a href="../bolsista.php?regiao=<?=$id_reg?>&projeto=<?=$id_pro?>">&laquo; Visualizar Participantes</a>
       <?php } } ?>
       </p>
       <div class="clear"></div>
  </div>
    </td>
  </tr>
  <tr>
    <td width="16%" rowspan="2" valign="top" align="center">
    <img src="../fotosclt/<?=$nome_imagem?>" name="imgFile" width="100" height="130" id="imgFile" style="margin-top:-12px; margin-bottom:5px;"/>
    
    
    <input type="file" id="bt_enviar" name="bt_enviar"/>
    
    
    <a href="#" id="bt_deletar" style="display:none; position:relative; top:5px;"><img src="../imagens/excluir_foto.gif"></a>
    </td>
    <td width="84%" bgcolor="#F3F3F3" valign="top">
     <b>Nº do processo:</b> <?php echo formato_num_processo($row['n_processo']) ?> / <?php echo formato_matricula($row['matricula']) ?><br>
    <b><?=$row['campo3']?> - <?=$row['nome']?></b><br>
     <b>CPF:</b> <?=$row['cpf']?><br>
    <b>Data de Entrada:</b> <?=$row['nova_data']?><br>
    <?=$texto?>
    <b>Projeto:</b> <?=$row_pro['id_projeto']?> - <?=$row_pro['nome']?><br>
    
	<?php if($row['status'] == 200){
	  
	    echo '<span style="color:red;">Aguardando Demissão</span><br>';
	  
    } else {
        
		if ($status_contratacao == 'Contratado') {
			echo '<span style="color:#00F;">'.$status_contratacao.'</span><br>';
		} elseif ($status_contratacao == 'Em experiência até ') {
			echo '<span style="font-size:14px; font-style:inherit; color:#F00;">'.$status_contratacao.' '.$data_contratacao.'</span><br>';
		} elseif ($status_contratacao == 'Aguardando') {
			echo '<span style="color:black;">'.$status_contratacao.'</span><br>';
		}
		
		$qr_status = mysql_query("SELECT especifica FROM rhstatus WHERE codigo = '$row[status]'");
		
		if($row['status'] != 10) {
			echo '<div style="color:#F00; font-size:14px;">'.@mysql_result($qr_status,0).'</div>';
		} else {
			echo '<div style="color:#06F;">'.@mysql_result($qr_status,0).'</div>';
		}
	
  	} ?>
     <br>
     <?php    
     if(!empty($row['orgao'])) {
        
            if(!empty($row['verifica_orgao'])){ echo '<span style="background-color:  #8bdd5e;"> Orgão regulamentador verificado. </span>';}
             else {  echo '<span style="background-color:   #fe9898"; color: #FFF;">Orgão regulamentador não verificado.</span>'; }
     } ?>
     <br>
     <i><?php echo 'Ultima Alteração feita por <b>'.$row_user2['nome1'].'</b> na data '.$row['dataalter2']; ?></i>
    </td>
  </tr>
  <tr>
    <td>
        <table cellpadding="0" cellspacing="0" width="100%" style="color: #fe9898">
             <tr>
               <td>
       <div id="Accordion1" class="Accordion" tabindex="0">
            <div class="AccordionPanel">
                <div class="AccordionPanelTab">&nbsp;</div>
                <div class="AccordionPanelContent">
                      <?php $get_atividade = mysql_query("SELECT * FROM curso WHERE id_curso = '$row[id_curso]'");
					        $atividade     = mysql_fetch_assoc($get_atividade);
							$get_pg        = mysql_query("SELECT * FROM tipopg WHERE id_tipopg = '$row[tipo_pagamento]'");
					        $pg            = mysql_fetch_assoc($get_pg);
							
							if($row['banco'] == '9999') {
								$nome_banco = $row['nome_banco'];
							} else {
								$get_banco = mysql_query("SELECT nome FROM bancos WHERE id_banco = '$row[banco]'");
								$row_banco = mysql_fetch_array($get_banco);
					        	$nome_banco = $row_banco[0];
							} ?>
                            
         <b>Atividade:</b> <?=$atividade['id_curso']?> - <?=$atividade['nome']?> <?php if(!empty($atividade['cbo_codigo'])) { echo '('.$atividade['cbo_codigo'].')'; } ?><br>
                       <b>Unidade:</b> <?=$row['locacao']?><br>
                       <b>Salário:</b>
                       <?php if(!empty($atividade['salario'])) { echo "R$ "; echo number_format($atividade['salario'], 2, ',', '.'); } else { echo "<i>Não informado</i>"; } ?>
                       &nbsp;&nbsp;<b>Tipo de Pagamento:</b> 
					   <?php if(!empty($pg['tipopg'])) { echo $pg['tipopg']; } else { echo "<i>Não informado</i>"; } ?><br>
                       <b>Agência:</b> 
					   <?php if(!empty($row['agencia'])) { echo $row['agencia']; } else { echo "<i>Não informado</i>"; } ?>
                       &nbsp;&nbsp;<b>Conta:</b> 
					   <?php if(!empty($row['conta'])) { echo $row['conta']; } else { echo "<i>Não informado</i>"; } ?>
                       &nbsp;&nbsp;<b>Banco:</b>
                       <?php if(!empty($nome_banco)) { echo $nome_banco; } else { echo "<i>Não informado</i>"; } ?>
                </div>
            </div>
       </div>   
               </td>
             </tr>
         </table>
    </td>
  </tr>
  <tr>
    <td colspan="2"><div id="observacoes"><?php if(empty($row['observacao'])) { echo "Sem Observações"; } else { echo "Observações<p>&nbsp;</p> $row[observacao]"; } ?></div></td>
  </tr>
   <tr>
  <td colspan="2"><h1><span>MENU DE EDIÇÃO</span></h1></td>
  </tr>
  <tr>
  <td colspan="2" class="menu">
    <?php // Consulta para Links
        $result_entregar = mysql_query("SELECT * FROM controlectps WHERE nome = '$row[nome]'");
        $num_row_entregar = mysql_num_rows($result_entregar);
          if($num_row_entregar != "0"){
	           $row_entregar = mysql_fetch_array($result_entregar);
	           $link_ctps = "../ctps_entregar.php?case=1&regiao=$id_reg&id=$row_entregar[0]";
          } else {
	           $link_ctps = "'#'";
          }
		  
		  if (!empty($row['pis'])) {
	          $statusBotao = 'none';
	          $emissao = true;
	      } else {
	          $statusBotao = 'inline';	
	          $emissao = false;
         }
    ?>
    
    <p>
    <?php  
	
	
	
	if($ACOES->verifica_permissoes(72) && $ativo) {
	?>
    <!-- linha 1 -->
    <a href="abertura_processo.php?clt=<?=$row['0']?>&pro=<?=$id_pro?>&pagina=<?=$pagina?>&reg=<?=$id_reg?>" class="botao">Abertura de processo</a>
    
    
    <?php 
	}
    
	if($ACOES->verifica_permissoes(14)) {
	?>
    <!-- linha 1 -->
    <a href="alter_clt.php?clt=<?=$row['0']?>&pro=<?=$id_pro?>&pagina=<?=$pagina?>" class="botao">Editar</a>
    
    
    <?php 
	}
	//VERIFICA SE O PROJETO ESTÁ DESATIVADO
	if($row_pro['status_reg'] == 1) { 
	
	
	if($ACOES->verifica_permissoes(15) && $ativo) {
	?>
    
       <a href="../tvsorrindo.php?bol=<?=$row['id_antigo']?>&clt=<?=$row['0']?>&pro=<?=$id_pro?>&tipo=2" target="_blank" class="botao">TV Sorrindo</a>
     <?php
	}
	
	if($ACOES->verifica_permissoes(78) && $ativo) {
	?>
    <a href="salariofamilia/safami.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$row['0']?>" target="_blank" class="botao"> Cad. do Salário Família</a>
	<?php	
	}
	
	if($ACOES->verifica_permissoes(16)) {
	 ?>         
       <a href="../rendimento/index.php?clt=<?=$row['0']?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank" style="font-size:12px;">Informe de Rendimento</a>
       
       </p>
       
    <!-- linha 2 -->
    <p> <?php
	}
	if($ACOES->verifica_permissoes(17) && $ativo) {
	 ?>  
    
    <a href="../ctps.php?regiao=<?=$id_reg?>&id=1&clt=<?=$row['0']?>" target="_blank" class="botao">Receber CTPS</a>
     <?php
	}
	if($ACOES->verifica_permissoes(18)) {
	 ?> 
       <a href="<?=$link_ctps?>" target="_blank" class="botao">Entregar CTPS</a>    
      <?php
	}

	
	if($ACOES->verifica_permissoes(61)) {
	 ?>       
    
       <a href="solicitacaopis.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$row['0']?>" target="_blank" class="botao" style="font-size:12px;"> Cadastro PIS</a>
     <?php
	}
	if($ACOES->verifica_permissoes(19) && $ativo) {
	 ?>    
       <!-- linha 3 -->
    <p><a href="admissional_clt.php?clt=<?=$row['0']?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" target="_blank" class="botao" style="font-size:12px;">Exame Admissional</a>
     <?php
	}
	if($ACOES->verifica_permissoes(20)) {
	 ?>  
       <a href="contratoclt.php?id_reg=<?=$id_reg?>&clt=<?=$row['0']?>" target="_blank" class="botao" style="font-size:12px;">Contrato de Trabalho</a>
        <?php
	}
		if($ACOES->verifica_permissoes(20)) {
	 ?>  
       <a href="contratocltexp.php?id_reg=<?=$id_reg?>&clt=<?=$row['0']?>" target="_blank" class="botao" style="font-size:12px;">Contrato de Experiência</a>
       <?php
		}
		if($ACOES->verifica_permissoes(80) && $ativo) {
	 ?>  
       <a href="rh_transferencia.php?clt=<?=$row['0']?>" target="_blank" class="botao" style="font-size:12px;">Transferência de Unidade</a>
       <?php
		}
	//if($ACOES->verifica_permissoes(79)) {
	 ?>  
       <a href="../registrodeempregado.php?bol=<?=$row['id_antigo']?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$row['0']?>" target="_blank" class="botao">Registro de empregado</a></p>
     <?php
	//}
        
        if($ACOES->verifica_permissoes(21)) {
	 ?>  
       <a href="../fichadecadastroclt.php?bol=<?=$row['id_antigo']?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$row['0']?>" target="_blank" class="botao">Ficha de Cadastro</a></p>
     <?php
	}
        
	if($ACOES->verifica_permissoes(22) && $ativo) {
	 ?>  
    <!-- linha 4 -->
    <p><a href="salariofamilia/safami.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$row['0']?>" target="_blank" class="botao">Benefícios</a>
     <?php
	}
	if($ACOES->verifica_permissoes(23) && $ativo) {
	 ?>  
       <a href="vt/vt.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$row['0']?>" target="_blank" class="botao">Vale Transporte</a>
        <?php
	}
	if($ACOES->verifica_permissoes(24) && $ativo) {
	 ?>  
       <a href="cartadereferencia.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$row['0']?>" target="_blank" class="botao" style="font-size:12px;">Carta de Referência</a></p>
    <?php
	}
	if($ACOES->verifica_permissoes(25) && $ativo) {
	 ?>   
    <!-- linha 5 -->
    <p><a href="../rh/notifica/advertencia.php?clt=<?=$row['0']?>&tab=bolsista<?=$id_pro?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" target="_blank" class="botao">Advertência</a>
     <?php
	}
	if($ACOES->verifica_permissoes(26) && $ativo) {
	 ?>  
       <a href="../rh/notifica/form_suspencao.php?clt=<?=$row['0']?>&tab=bolsista<?=$id_pro?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" target="_blank" class="botao">Suspensão</a>
        <?php
	}
	if($ACOES->verifica_permissoes(27)) {
	 ?>  
       <a href="../relatorios/fichafinanceira_clt.php?reg=<?=$id_reg?>&pro=<?=$id_pro?>&tipo=2&tela=2&id=<?=$row['0']?>" target="_blank" class="botao">Ficha Financeira</a></p>
        <?php
	}
	if($ACOES->verifica_permissoes(28) ) {
	 ?>  
    <!-- linha 6 -->
    <?php// if(!in_array($row['status'],array('60','61','62','63','64','65','66','81','101'))) { ?>
        <p><a href="docs/dispensa.php?clt=<?=$row['0']?>&tab=bolsista<?=$id_pro?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" target="_blank" class="botao">Dispensa</a>
         <?php
	//}
	//if($ACOES->verifica_permissoes(29)  && $ativo) {
	 ?>    
           <a href="docs/demissao.php?clt=<?=$row['0']?>&tab=bolsista<?=$id_pro?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" target="_blank" class="botao">Demissão</a>
      <?php
	//}
	if($ACOES->verifica_permissoes(30)) {
	 ?>  
           <a href="demissionalclt.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$row['0']?>" target="_blank" class="botao" style="font-size:12px;">Exame Demissional</a></p>
    <?php 
	}
	} ?>
    
     <a href="declaracao_jornada_semanal.php?pro=<?=$id_pro?>&reg=<?=$id_reg?>&clt=<?=$row['0']?>" target="_blank" class="botao" style="font-size:12px;">Declaração de Jornada Semanal</a></p>
   
     <?php }   //FIM VERIFICAÇÃO ?>
    
  </td>
 
</tr>
	
<?php
	if($ACOES->verifica_permissoes(62)) {
	 ?>  
	 
<tr>
	<td colspan="2">
     <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
      <tr bgcolor="#dddddd">
        <td><strong>DOCUMENTOS</strong></td>
         <td colspan="2"> </td>         
        <td  align="center"><strong>STATUS</strong></td>
        <td  align="center"><strong>DATA</strong></td>
      </tr>
     <?php
	 $qr_documentos = mysql_query("SELECT * FROM upload ORDER BY ordem") or die(mysql_error());
	 while($row_documentos = mysql_fetch_assoc($qr_documentos)):
	 
	 $verifica_anexo = mysql_num_rows(mysql_query("SELECT * FROM documento_clt_anexo WHERE id_upload = '$row_documentos[id_upload]' AND id_clt = '$row[0]' AND anexo_status = 1"));
	 
	 if($row_documentos['id_upload'] == 13 and $row['contrato_medico'] == 0) continue;
	 
	 if($row_documentos['id_upload'] != 14){	 
		 
				 
				$onclick = "OnClick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\"";
				$visualizar = ($verifica_anexo != 0)? '<a href="ver_documentos.php?id='.$row_documentos['id_upload'].'&clt='.$id_clt.'" '.$onclick.' title="VISUALIZAR">
													 		 <img src="../imagens/ver_anexo.gif" width="20" height="20" />	  	
														  </a>' : '';
				$status =  ($verifica_anexo == 0)? '<img src="../imagens/naoassinado.gif" />':'<img src="../imagens/assinado.gif" />';
				
				$anexar = '<a href="anexar_documento.php?clt='.$row['0'].'&id='.$row_documentos['id_upload'].'" title="ANEXAR/EDITAR"> <img src="../img_menu_principal/anexo.png" width="20" height="20"/></a>'; 
				
				$data = @mysql_result(mysql_query("SELECT date_format(data_cad, '%d/%m/%Y') as data FROM documento_clt_anexo WHERE id_clt = '$id_clt' AND id_upload = '$row_documentos[id_upload]'  ORDER BY data_cad DESC"),0) ;
			
				if($row_documentos['id_upload'] == 13){	
				$visualizar = '<a href="contrato_medico.php?clt='.$row[0].'" title="VISUALIZAR"> 
								<img src="../imagens/ver_anexo.gif" width="20" height="20" />	  	
							</a>';
				$anexar ='';
				$status =  '<img src="../imagens/assinado.gif" />';
				}
				
//BRUNO CRITÉRIOS DE AVALIAÇÃO
		    	if($row_documentos['id_upload'] == 19){	
				$verifica_linha = mysql_num_rows(mysql_query("SELECT * FROM rh_avaliacao_clt WHERE clt_id = ".$row[0]));
				
  				$visualizar =  ($verifica_linha == 0)?'':'<a href="ver_avaliacao_clt.php?clt='.$row[0].'"><img src="../imagens/ver_anexo.gif" width="20" height="20" /></a>';
							   $anexar = ($verifica_linha == 0)?'<a href="avaliacao_clt.php?clt='.$row[0].'&reg='.$_GET["reg"].'&pro='.$_GET["pro"].'"><img src="../img_menu_principal/anexo.png" width="20" height="20" /></a>':'';		  	
			  				
				$status =  ($verifica_linha == 0)? '<img src="../imagens/naoassinado.gif" />':'<img src="../imagens/assinado.gif" />';
				$data = @mysql_result(mysql_query("SELECT date_format(data_cadastro, '%d/%m/%Y') as data FROM rh_avaliacao_clt WHERE clt_id = '$row[0]'"),0) ;
				}
// FIM CRITÉRIOS DE AVALIAÇÃO
	
	 } else {
	
	 $qr_processo 		=	mysql_query("SELECT *,DATE_FORMAT(data_cad, '%d/%m/%Y') as data FROM processos_interno WHERE id_clt = '$id_clt' AND proc_interno_status = 1");
	 $row_processo 		= mysql_fetch_assoc($qr_processo);
	 $verifica_processo = mysql_num_rows($qr_processo);
	 
	 $status = ($verifica_processo ==0)? '<img src="../imagens/naoassinado.gif" />':'<img src="../imagens/assinado.gif" />'; 	
	 $data 	 = $row_processo['data']; 
	 $visualizar = '<a href="ver_abertura_proc.php?clt='.$row[0].'" title="VISUALIZAR"> 
					<img src="../imagens/ver_anexo.gif" width="20" height="20" />	  	
				</a>';
	 
	 }
	 
	 if($cont++ % 2){ $color="#fafafa"; }else{ $color="#f3f3f3"; }
	 ?>
	 <tr bgcolor="<?php echo $color;?>" height="25">
     	<td ><?php echo $row_documentos['arquivo']?></td>
        <td align="center"><?php echo $anexar;?></td>
        <td align="center"><?php echo $visualizar; ?></td>
        <td align="center"><?php echo $status; ?></td>  
        <td align="center"><?php echo $data;?></td>      
     </tr>
     
	 <?php
	 endwhile;
	 
	 ?>
     
     
     
         </table>
    </td>
</tr>	 
	
	
	
	

  
  <tr id="ancora_documentos">
  	<td colspan="2"><h1><span>UPLOAD DE DOCUMENTOS</span></h1></td>
  </tr>
  <tr>
  	<td colspan="2">
  
  <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
      <tr>
        <td style="font-weight:bold;" id="fotosDocumentos">
         <?php // Exclusão do Documento
				if(isset($_GET['deleta_documento'])) {
					if(file_exists($_GET['deleta_documento'])) {
						unlink($_GET['deleta_documento']);
						echo 'Documento deletado com sucesso!<br>';
					}
				}
				//
		
			$diretorio_padrao = $_SERVER["DOCUMENT_ROOT"]."/";
			$diretorio_padrao .= "intranet/documentos/";
			$dirInternet = "../documentos/";
			$DeldirInternet = "documentos/";
			
			$regiao = sprintf("%03d", $id_reg);
			$projeto = sprintf("%03d", $id_pro);
			
			$Dir = $regiao."/".$projeto."/"; // O NOME DA PASTA ONDE VAI SER CRIADO A PASTA DO USUARIO
			$novoDir = $row['tipo_contratacao']."_".$row[0]; // O NOME DA PASTA DO USUARIO
			$DirCom = $Dir.$novoDir;
			
			$dir = $diretorio_padrao.$DirCom;
			$dirInternet .= $DirCom;
			$DeldirInternet .= $DirCom;
			// Abre um diretorio conhecido, e faz a leitura de seu conteudo
			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						if($file == "." or $file == ".."){
							$nada;
						} else {
							$tipoArquivo = explode("_",$file);
							$tipoArquivo = explode(".",$tipoArquivo[2]);
							
							$select = new upload();
							$TIPO = $select -> mostraTipo($tipoArquivo[0]);
							
							$DirFinal = $dirInternet."/".$file;
							$DelDirFinal = $DeldirInternet."/".$file;

							// Renomeia o arquivo se estiver sem extensão
							if(!strstr($DirFinal, '.jpg') and !strstr($DirFinal, '.gif') and !strstr($DirFinal, '.png')) {
								$de = $DirFinal;
								$para = $DirFinal.'.jpg';
								rename($de, $para);
								$DirFinal .= '.jpg';
							}
							
							// Criando Array para Options no Select
							$ja_documentos[] = $file;
							
							echo "<div class='documentos'>";
							echo "<a class='documento' href='".$DirFinal."' rel='shadowbox[documentos]' title='Visualizar $TIPO'>";
							echo "<img src='".$DirFinal."' width='75' height='75' border='0' alt='$TIPO'></a>";
							echo "<a href='$_SERVER[PHP_SELF]?$_SERVER[QUERY_STRING]&deleta_documento=$DirFinal#ancora_documentos'>deletar</a>";
							echo "</div>";
			
						}
					}
				  closedir($dh);
				}
			}
			
			// Criando Array para Options no Select
			if(!empty($ja_documentos)) { 
				foreach($ja_documentos as $documento) {
					$documento = explode('_', $documento);
					$tipo_documento = explode('.', $documento[2]);
					$tipo_documento = $tipo_documento[0];
					$tipos_ja_documentos[] = $tipo_documento;
				}
			}
			//
			?>
        </td>
      </tr>
  <?php if(count($tipos_ja_documentos) != 5 ) { ?>
      <tr>
        <td>
          <div id="foto">
            <br><input type="file" name="uploadDoc" id="uploadDoc">
          </div></td>
        </tr>
        <tr>
        <td>
        
        <div id="upload_documentos" style="display:none;">
          		<table width="0%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td>
                    <div id="BarUploadDoc" style="margin-bottom:10px; display:none;"></div>
                    <b>Tipo de Documento:</b>&nbsp;&nbsp;
                    <select name="select" id="select_doc" >
                      <option  selected value="">Escolha um tipo abaixo</option>
                    <?php $qr_documentos = mysql_query("SELECT * FROM  upload	 WHERE status_reg = '1'");
						  while($documento = mysql_fetch_assoc($qr_documentos)) {
							  if(!in_array($documento['id_upload'], $tipos_ja_documentos)) {
					?>
                      <option value="<?=$documento['id_upload']?>"><?=$documento['arquivo']?></option>
                    <?php } } ?>
                    </select>
                  <a class="botao" id="Upar" style="cursor:pointer; float:none; margin-top:8px;">Enviar Documento</a>
                    </td>
                  </tr>
                </table>
               </div>
				</td>
                </tr>
                <?php } ?>
                <tr>
                <td>
                
          <table border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td>
                
<script language="javascript">
$().ready(function(){		    
			var tipo_contratacao = '<?=$row['tipo_contratacao']?>';
			var regiao = '<?=sprintf('%03d',$id_reg);?>';
			var projeto = '<?=sprintf('%03d',$id_pro);?>';
			var id_participante = '<?=sprintf('%03d',$id_clt);?>';
			
			$("#uploadDoc").uploadify({
				'uploader'       : '../uploadfy/scripts/uploadify.swf',
				'script'         : '../include/upload_doc.php',
				'buttonImg'      : '../imagens/botao_upload.jpg',
				'buttonText'     : '',
				'cancelImg'      : '../uploadfy/cancel.png',
				'width'          : '156',
				'height'         : '46',
				'fileDesc'       : 'Jpg, Gif, Png',
				'fileExt'        : '*.gif;*.jpg;*.png',
				'auto'           : false,
				'method'         : 'post',
 				'multi'          : false,
				'queueID'        : 'BarUploadDoc',
				'onSelect'     	 : function(){
									$("#upload_documentos").show();
									
								     },
				'onComplete'  : function(event, queueID, fileObj, response, data){ 
										
										if(response != 1){
											$("#upload_documentos").hide();
											
											$.post('../include/fotos_documentos.php',{
												   'id_regiao' : regiao,
												   'id_projeto' :  projeto,
												   'tipo_contratacao' : tipo_contratacao,
												   'id_participante'  : id_participante
												   },function(dados){
													   $("#fotosDocumentos").html(dados);
													   });
										}else{
											alert('Erro ao enviar o arquivo!');
										}
									},
				'scriptData'     : { 'reg' : regiao,
									 'projeto' : projeto, 
									 'ID_participante' : id_participante, 
									 'tipo_contratacao' : tipo_contratacao
									 
									}		
			});
			
		
			
			$('#Upar').click(function(){
				if($('#select_doc').val() != ''){
					$('#uploadDoc').uploadifySettings('scriptData', {'tipo_documento' : $('#select_doc').val()});
					$('#uploadDoc').uploadifyUpload();
					$('#BarUploadDoc').slideDown('slow');
					
					
				} else {
					alert('Selecione um tipo de documento');
				}
			});
});

function Confirm(a){
	var arquivo = a;
	input_box = confirm("Deseja realmente excluir o documento?");
	    if(input_box==true) {
		    location.href="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>&foto=deletado#ancora_documentos";
		}
}
</script>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
  
   <?php
	}
	 ?>  </td>
  </tr>



   <?php
	
	if($ACOES->verifica_permissoes(63)) {
            if($ativo){ 
	 ?>  
  <tr>
  <td colspan="2"><h1><span>ENCAMINHAMENTO DE CONTA</span></h1></td>
  </tr>
  <tr>
  <td colspan="2">
  <form action="../declarabancos.php" method="post" name="form1" target="_blank">
      <b>Escolha o Banco:</b>&nbsp;&nbsp;
      <select name="banco" id="banco">
        <?php while($row_ban = mysql_fetch_array($result_ban)){
              print "<option value=$row_ban[id_banco]>$row_ban[nome]</option>"; 
			  }; ?>
        </select>
      <input type="submit" value="Gerar Encaminhamento de Conta">
      <input type="hidden" name="tipo" id="tipo" value="2">
      <input type="hidden" name="bolsista" id="bolsista" value="<?=$row['0']?>">
      <input type="hidden" name="regiao" id="regiao" value="<?=$id_reg?>">
    </form> 
  </td>
  </tr>
  <?php } ?>
  <tr>
  <td colspan="2"><h1><span>CONTROLE DE DOCUMENTOS</span></h1></td>
  </tr>
  <tr>
  <td colspan="2">
  
  <table width="100%" border="0" cellpadding="4" cellspacing="0" style="font-size:13px;">
      <tr bgcolor="#dddddd">
        <td width="70%"><strong>DOCUMENTO</strong></td>
        <td width="15%" align="center"><strong>STATUS</strong></td>
        <td width="15%" align="center"><strong>DATA</strong></td>
      </tr>
      <?php
	  $cont = "1";
	  $tipo_contratacao = '2';
	  
	  $result_docs = mysql_query("SELECT * FROM rh_documentos WHERE tipo_contratacao = '$tipo_contratacao' ORDER BY documento");
	  
	  while($row_docs = mysql_fetch_array($result_docs)){  
	  if($cont % 2){ $color="#fafafa"; }else{ $color="#f3f3f3"; }
	  
	  $result_verifica = mysql_query("SELECT *,date_format(data, '%d/%m/%Y')as data FROM rh_doc_status WHERE tipo = '$row_docs[0]' and id_clt = '$row[0]'");
	  $num_row_verifica = mysql_num_rows($result_verifica);
	  $row_verifica_doc = mysql_fetch_array($result_verifica);
	  
	  if($num_row_verifica != "0"){
	  $img = "<img src='../imagens/assinado.gif' width='15' height='17' align='absmiddle'>";
	  $data = $row_verifica_doc['data'];
	  }else{
	  $img = "<img src='../imagens/naoassinado.gif' width='15' height='17' align='absmiddle'>";
  	  $data = "";
	  }
	echo "<tr bgcolor=$color>";	  	
    echo "<td class='linha'>$row_docs[documento]</td>";
    //echo "<td class='linha' align='center'>$img</td>";
	if (($row_docs['documento']=='Inscrição no PIS')and($emissao==true)){
	  $img = "<img src='../imagens/assinado.gif' width='15' height='17' align='absmiddle'>";
	  echo "<td class='linha' align='center'>$img</td>";
	  }elseif(($row_docs['documento']!='Inscrição no PIS')or($emissao==false)){
		  echo "<td class='linha' align='center'>$img</td>";
	  }
    echo "<td align='center'>$data</td>";
    echo  "</tr>";
	
	
	  $cont ++;
	  $img = "";
	  $data = "";
	  }
	  
	  ?>
      <tr>
        <td colspan="3" align="center" class="linha" style="font-size:16px;"><img src="../imagens/assinado.gif" width="15" height="17" align="absmiddle"> Emitido  <img src="../imagens/naoassinado.gif" width="15" height="17" align="absmiddle"> N&atilde;o Emitido</td>
      </tr>
    </table>
    
    </td>
    </tr>
    <tr>
      <td colspan="2"><h1><span>CONTROLE DE EVENTOS</span></h1></td>
    </tr>
    <tr>
      <td colspan="2">
                  <table width="100%" border="0" cellpadding="4" cellspacing="0" style="font-size:13px;">
                <tr bgcolor="#dddddd">
                
                
                <td>Evento</td>
                <td>Data</td>
                <td>Data de retorno</td>
                <td>Dias</td>
                </tr>
                <?php
                
                
                $qr_historico_eventos=mysql_query("SELECT * FROM rh_eventos WHERE id_clt = '$id_clt' AND id_regiao = '$id_reg' AND id_projeto = '$id_pro' AND nome_status!='' AND status = '1' ")or die (mysql_error());
                
                $qr_historico_ferias=mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id_clt' AND regiao = '$id_reg' AND projeto = '$id_pro' AND status = '1' ")or die(mysql_error());
                
                $qr_historico_rescisao=mysql_query("SELECT * FROM rh_recisao WHERE id_clt = '$id_clt' AND id_regiao = '$id_reg' AND id_projeto = '$id_pro' AND status = '1' ")or die(mysql_error());
                $qr_historico_clt=mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$id_clt' AND id_regiao = '$id_reg' AND id_projeto = '$id_pro' AND status!=0")or die(mysql_error());
                
                settype($historico_nome,'array');
                settype($historico_inicio,'array');
                settype($historico_fim,'array');
                settype($historico_duracao,'array');
                
                
                
                
                while($row_clt=mysql_fetch_assoc($qr_historico_clt)):
                
                    $historico_nome[] = 'Admissão';
                    $historico_inicio[] = $row_clt['data_entrada'];
                    $historico_fim[] = '';
                    $historico_duracao[] ='';
                
                
                endwhile;	
                
                
                
                
                while($row_evento=mysql_fetch_assoc($qr_historico_eventos)):
                
                    $historico_nome[]=$row_evento['nome_status'];
                    $historico_inicio[]=$row_evento['data'];
                    $historico_fim[]=$row_evento['data_retorno'];
                    $historico_duracao[]=$row_evento['dias'];
                
                
                endwhile;
                
                
                
                
                while($row_ferias=mysql_fetch_assoc($qr_historico_ferias)):
                
                    $historico_nome[] = 'Férias';
                    $historico_inicio[] = $row_ferias['data_ini'];
                    $historico_fim[] = $row_ferias['data_fim'];
                    $historico_duracao[] =($row_ferias['data_fim']- $row_ferias['data_ini']);
                
                
                endwhile;		
                    
                
                
                while($row_recisao=mysql_fetch_assoc($qr_historico_rescisao)):
                
                    $historico_nome[] = 'Rescisão';
                    $historico_inicio[] = $row_recisao['data_demi'];
                    $historico_fim[] = '';
                    $historico_duracao[] ='';
                
                
                endwhile;		
                    
                
                array_multisort($historico_inicio,$historico_fim,$historico_duracao,$historico_nome);
                
                foreach($historico_inicio as $chave=> $inicio) {
                    ?>
                    
                    <tr class="linha_<?php if($cor++%2){ echo 'um';} else {echo 'dois';}?>">
                        <td><?php echo $historico_nome[$chave]; ?></td>
                        <td><?php echo formato_brasileiro($historico_inicio[$chave]); ?></td>
                        <td><?php if($historico_fim[$chave] !='0000-00-00'){ echo formato_brasileiro($historico_fim[$chave]);}?></td>
                        <td><?php if(!empty($historico_duracao[$chave]))echo $historico_duracao[$chave];?></td>
                    </tr>
                    
                    <?php
                    }
                
                
                ?>
                
                
                
                </table>

      </td>
    </tr>
    
  <?php
	}
	
	if($ACOES->verifica_permissoes(14)) {
	 ?>     
    <tr>
      <td colspan="2"><h1><span>CONTROLE DE MOVIMENTOS</span></h1></td>
    </tr>
    <tr>
      <td colspan="2"></td>
    </tr>
    
     <?php 
	}
	
	 ?>  
</table>
</div>
<div id="rodape">
<?php $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$Master'");
      $master = mysql_fetch_assoc($qr_master);
	  ?>
            <p class="left"><img style="position:relative; top:7px;" src="../imagens/logomaster<?=$Master?>.gif" width="66" height="46"> <b><?=$master['razao']?></b>&nbsp;&nbsp;Acesso Restrito à Funcion&aacute;rios</p>
            <p class="right"><br><br><a href="#corpo">Subir ao topo</a></p>
            <div class="clear"></div>
        </div>
</div>
<script type="text/javascript">
var Accordion1 = new Spry.Widget.Accordion("Accordion1", { enableAnimation: false, useFixedPanelHeights: false, defaultPanel: -1 });
</script>
</body>
</html>