<?php
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="login.php">Logar</a>';
	exit;
}

include('../../conn.php');
include('../../upload/classes.php');
include('../../classes/funcionario.php');
$Fun = new funcionario();
$Fun -> MostraUser(0);
$Master = $Fun -> id_master;

// Obtendo o id do cadastro
$id_processo = mysql_real_escape_string($_GET['id_processo']);
$qr_processo = mysql_query("SELECT * FROM processo_trabalhista WHERE proc_trab_id = '$id_processo'");
$row_processo = mysql_fetch_assoc($qr_processo);


$id = 1;
$id_bol = $_REQUEST['bol'];
$id_pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['reg'];

$id_user = $_COOKIE['logado'];

$sql_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($sql_user);

$result = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS nova_data, date_format(data_saida, '%d/%m/%Y') AS data_saida2, date_format(dataalter, '%d/%m/%Y') AS dataalter2 FROM autonomo WHERE id_autonomo = ''$row_processo[id_autonomo]'");
$row = mysql_fetch_array($result);

$result_tab = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_processo[id_projeto]' AND status_reg = '1'");
$row_tab = mysql_fetch_array($result_tab);

$sql_user2 = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$row[useralter]'");
$row_user2 = mysql_fetch_array($sql_user2);

$result_ban = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$id_reg' and id_projeto = '$id_pro'");

if($row['status'] == '0') {
	$texto = "<font color=red>Data de sa�da: $row[data_saida2]</font>";
} else {
	$texto = NULL;
}

$nome_arq = str_replace(' ', '_', $row['nome']);	

$ano_cad = substr($row['data_cad'],0,4);

if($ano_cad <= '2008') {
	$coluna_foto = $row['id_bolsista'];
} else {
	$coluna_foto = $row['0'];
}

if($row['foto'] == "1") {
	$nome_imagem = $id_reg."_".$id_pro."_".$coluna_foto.".gif";
} else {
	$nome_imagem = "semimagem.gif";
}
?>
<html>
<head>
<title>:: Intranet ::</title>
<link rel='shortcut icon' href='favicon.ico'>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../rh/css/estrutura_participante.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../SpryAssets/SpryAccordion.js"></script>
<script type="text/javascript" src="../../js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../../uploadfy/scripts/swfobject.js"></script>
<script type="text/javascript" src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>
<script type="text/javascript" src="../../js/shadowbox.js"></script>
<link rel="stylesheet" type="text/css" href="../js/shadowbox.css">
<link rel="stylesheet" type="text/css" href="../rh/css/spry.css">
<link rel="stylesheet" type="text/css" href="../uploadfy/css/default.css" />
<link rel="stylesheet" type="text/css" href="../uploadfy/css/uploadify.css" />
<script type="text/javascript">
Shadowbox.init();
</script>
<script type="text/javascript">
$().ready(function(){
		<?php if($row['foto'] == '1') { ?>
			$("#bt_deletar").show();
		<?php }?>
	
			$("#bt_deletar").click(function(){
				$.post('../include/excluir_foto.php',
					   {nome: '<?=$_GET['reg']?>_<?=$_GET['pro']?>_<?=$_GET['bol']?>.gif', ID : '<?=$_GET['bol']?>'},
					   function(){
							$("#imgFile").attr('src','fotos/semimagem.gif');
							$("#bt_deletar").hide();
							$('#bt_enviar').uploadifySettings('buttonText','Adicionar foto');
						}
					   
					   );
			});
			
			$("#bt_enviar").uploadify({
				'uploader'       : '../../uploadfy/scripts/uploadify.swf',
				'script'         : '../../uploadfy/scripts/uploadify.php',
				'folder'         : '../../fotos',
				'buttonText'     : '<?php if($row['foto'] == '1') { ?>Alterar<?php } else { ?>Adicionar<?php } ?> foto',
				'queueID'        : 'fileQueue',
				'cancelImg'      : '../uploadfy/cancel.png',
				'auto'           : true,
				'method'         : 'post',
 				'multi'          : false,
				'fileDesc'       : 'Gif',
				'fileExt'        : '*.gif;',
				'onOpen'         : function(){
										$("#fileQueue").show('slow');
									},
				'onAllComplete'  : function(){
									$("#bt_deletar").show('slow'); 
									$('#imgFile').attr('src' , 'fotos/<?=$_GET['reg']?>_<?=$_GET['pro']?>_<?=$_GET['bol']?>.gif');									$("#fileQueue").hide('slow');
									$('#bt_enviar').uploadifySettings('buttonText','Alterar foto');
									},
				'scriptData'     : {'regiao' : <?=$_GET['reg']?>,'projeto': <?=$_GET['pro']?>,'id_participantes' : <?=$_GET['bol']?>}
			});
			
			
			
});
</script>
</head>
<body>
<div id="fileQueue"></div>
<div id="corpo">
<div id="conteudo">
<table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
  <tr>
  <td colspan="2">
  <div style="float:right;"><?php include('../../reportar_erro.php'); ?></div>
  <div style="clear:right;"></div>
  
  <?php if($_GET['sucesso'] == "cadastro") { ?>
  <div id="sucesso">
       Participante cadastrado com sucesso!
  </div>
  <?php } ?>
  <div style="border-bottom:2px solid #F3F3F3; margin-top:10px;">
       <h2 style="float:left; font-size:18px;">VISUALIZAR 
	   <?php switch($row['tipo_contratacao']) {
		        case 1:
	                echo '<span class="aut">AUT�NOMO</span>';
				break;
				case 3:
				    echo '<span class="coo">COOPERADO</span>';
				break;
				case 4:
				    echo '<span class="aut">AUT�NOMO / PJ</span>';
				break;
	   } ?>
       </span>
       </h2>
       <p style="float:right;">
       <?php if($_GET['sucesso'] == "cadastro") { 
	          switch($row['tipo_contratacao']) {
		        case 1:
	                echo "<a href='cadastro_bolsista.php?regiao=$id_reg&pro=$id_pro&id=4'>&laquo; Cadastrar Outro Participante</a>";
				break;
				case 3:
				    echo "<a href='cooperativas/cadcooperado.php?regiao=$id_reg&pro=$id_pro&tipo=3'>&laquo; Cadastrar Outro Participante</a>";
				break;
				case 4:
				    echo "<a href='cooperativas/cadcooperado.php?regiao=$id_reg&pro=$id_pro&tipo=4'>&laquo; Cadastrar Outro Participante</a>";
				break;
	          } 
		} else {
       		echo "<a href='bolsista.php?projeto=$id_pro&regiao=$id_reg'>&laquo; Visualizar Participantes</a>";
       } ?>
       </p>
       <div class="clear"></div>
  </div></td>
  </tr>
  <tr>
    <td width="16%" rowspan="2" valign="top" align="center">
    <img src="../../fotos/<?=$nome_imagem?>" name="imgFile" width="100" height="130" id="imgFile" style="margin-top:-12px; margin-bottom:5px;">
    <input type="file" id="bt_enviar">     
    <a href="#" id="bt_deletar" style="display:none; position:relative; top:5px;"><img src="../../imagens/excluir_foto.gif"></a>    
    </td>
    <td width="84%" bgcolor="#F3F3F3" valign="top">
         <b><?=$row['campo3'] . ' - ' . $row['nome']?></b><br>
         <b>Data de Entrada:</b> <?=$row['nova_data'] . '<br>' . $texto?><br>
         <b>Projeto:</b> <?=$row_tab['id_projeto'] . ' - ' . $row_tab['nome']?><br>
         <i><?='Ultima Altera��o feita por <b>' . $row_user2['nome1'] . '</b> na data ' . $row['dataalter2']?></i>
    </td>
  </tr>
  <tr>
    <td>
      <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <td>
            <div id="Accordion1" class="Accordion" tabindex="0">
              <div class="AccordionPanel">
                <div class="AccordionPanelTab">&nbsp;</div>
                <div class="AccordionPanelContent">
                  <?php $get_atividade = mysql_query("SELECT * FROM curso WHERE id_curso = '$row[id_curso]'");
					    $atividade = mysql_fetch_assoc($get_atividade);
						$get_pg = mysql_query("SELECT * FROM tipopg WHERE id_tipopg = '$row[tipo_pagamento]'");
					    $pg = mysql_fetch_assoc($get_pg);
						if($row['banco'] == '9999') {
							$nome_banco = $row['nome_banco'];
						} else {
							$get_banco = mysql_query("SELECT nome FROM bancos WHERE id_banco = '$row[banco]'");
							$row_banco = mysql_fetch_array($get_banco);
					        $nome_banco = $row_banco[0];
						} ?>
                  <b>Atividade:</b> <?=$atividade['id_curso']?> - <?=$atividade['nome']?> <?php if(!empty($atividade['cbo_codigo'])) { echo '('.$atividade['cbo_codigo'].')'; } ?><br>
                  <b>Unidade:</b> <?=$row['locacao']?><br>
                  <b>Sal�rio:</b>
                  <?php if(!empty($atividade['salario'])) { echo "R$ "; echo number_format($atividade['salario'], 2, ',', '.'); } else { echo "<i>N�o informado</i>"; } ?>
                  &nbsp;&nbsp;<b>Tipo de Pagamento:</b>
                  <?php if(!empty($pg['tipopg'])) { echo $pg['tipopg']; } else { echo "<i>N�o informado</i>"; } ?><br>
                  <b>Ag�ncia:</b> 
                  <?php if(!empty($row['agencia'])) { echo $row['agencia']; } else { echo "<i>N�o informado</i>"; } ?>
                  &nbsp;&nbsp;<b>Conta:</b> 
                  <?php if(!empty($row['conta'])) { echo $row['conta']; } else { echo "<i>N�o informado</i>"; } ?>
                  &nbsp;&nbsp;<b>Banco:</b>
                  <?php if(!empty($nome_banco)) { echo $nome_banco; } else { echo "<i>N�o informado</i>"; } ?>
                  </div>
                </div>
              </div>   
            </td>
          </tr>
          <tr>
          	<td>
            <?php
            $nome_func = mysql_resul(mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_processo[usuario_cad]'"));			
			?>
            
            Cadatrado por: <?php echo $nome_func; ?> em <?php echo $row_processo['data_atualizacacao'];?>
            
            </td>
          </tr>
        </table>
    </td>
  </tr>
  <tr>
    <td colspan="2"><div id="observacoes"><?php if(empty($row['observacao'])) { echo "Sem Observa��es"; } else { echo "<b>Observa��es</b><p>&nbsp;</p> $row[observacao]"; } ?></div></td>
  </tr>
   <tr>
    <td colspan="2"><h1><span>MENU DE EDI��O</span></h1></td>
  </tr>
  <tr>
  <td colspan="2" class="menu">
  <?php // Consulta para Links
        if($row_user['grupo_usuario'] == '3'){
		      $botao_editar = NULL;
	    } else {
		    if($row['tipo_contratacao'] == '1') {
			      $botao_editar = "<a href='alter_bolsista.php?bol=$row[0]&pro=$id_pro' class='botao'>Editar Cadastro</a>";
		    } elseif($row['tipo_contratacao'] == '3') {
			      $botao_editar = "<a href='cooperativas/altercoop.php?coop=$row[0]&tipo=3' class='botao'>Editar Cadastro</a>";
			} elseif($row['tipo_contratacao'] == '4') {
			      $botao_editar = "<a href='cooperativas/altercoop.php?coop=$row[0]&tipo=4' class='botao'>Editar Cadastro</a>";
		    }
		}
		
		if (!empty($row['pis'])) {
	        $statusBotao = 'none';
	        $emissao     = true;
	    } else {
	        $statusBotao = 'inline';	
	        $emissao     = false;
        }
		
       switch($row['tipo_contratacao']) {
		   // Links para Autonomos
		   case 1: ?>
       
       <!-- linha 1 -->
       <p><?=$botao_editar?>
         <?php 
	  
	   //verifica se o projeto est� desativado
	  if($row_tab['status_reg'] == 1){  
	  
	  ?>  
       
       
<!--          <a href="contrato.php?bol=<?=$row['0']?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Contrato</a>
          <a href="distrato.php?bol=<?=$row['0']?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Distrato</a></p>-->
       <!-- linha 2 -->
<!--       <p><a href="tvsorrindo2.php?bol=<?=$row['0']?>&pro=<?=$id_pro?>" class="botao" target="_blank">TV Sorrindo</a>
          <a href="declararenda.php?bol=<?=$row['0']?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Declara��o</a>
          <a href="certificado.php?bol=<?=$row['0']?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Certificado</a></p>-->
       <!-- linha 3 -->
      <!-- <p><a href="contrato2via.php?bol=<?=$row['0']?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Segunda Via</a>
          <a href="rendimento/index.php?bol=<?=$row['0']?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank" style="font-size:12px;">Informe de Rendimento</a></p>-->
          
          
       <?php } ?>
      <?php // Links para Cooperados
	        break;
			case 3: ?>
      
        
      <!-- linha 1 -->
      <p><?=$botao_editar?>
	  
     
	  <?php 
	  
	   //verifica se o projeto est� desativado
	  if($row_tab['status_reg'] == 1){  
	  
	  ?>  
        <!-- <a href="cooperativas/tvsorrindo.php?coop=<?=$row[0]?>&pro=<?=$id_pro?>" class="botao" target="_blank">TV Sorrindo</a>
         <a href="cooperativas/contratos/contrato<?=$row["id_cooperativa"]?>.php?coop=<?=$row[0]?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Ades�o</a></p>-->
      <!-- linha 2 -->
      <p><!--<a href="cooperativas/quotas.php?coop=<?=$row[0]?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Quotas</a>-->
         <a href="cooperativas/fichadecadastro.php?bol=<?=$row[0]?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Ver Ficha</a>
         <!--<a href="cooperativas/distrato.php?coop=<?=$row[0]?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Desligamento</a></p>
-->      <!-- linha 3 -->
      <!--<p><a href="rh/solicitapis_pdf.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&bol=<?=$row[0]?>" class="botao" target="_blank">Gerar PIS</a>
      	 <a href="cooperativas/devolucao_quotas.php?coop=<?=$row[0]?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Devolu&ccedil;&atilde;o de Quotas</a>
         <a href="rendimento/index.php?coo=<?=$row['0']?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank" style="font-size:12px;">Informe de Rendimento</a>
         <a href="cooperativas/recibocoop_individual.php?coo=<?=$row['0']?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Recibos de Pagamento</a></p>-->
       <?php } ?>
       
       
       
      <?php // Links para PJ
	        break;
			case 4: ?>
            
      <!-- linha 1 -->
      <p><?=$botao_editar?>
      
      <?php 
	  
	   //verifica se o projeto est� desativado
	  if($row_tab['status_reg'] == 1){  
	  
	  ?>     
       <!--  <a href="cooperativas/tvsorrindo.php?coop=<?=$row[0]?>&pro=<?=$id_pro?>" class="botao" target="_blank">TV Sorrindo</a>-->
         <a href="cooperativas/fichadecadastro.php?bol=<?=$row[0]?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Ver Ficha</a></p>
            <?php } ?>
     <?php } ?>

  </td>
  <tr id="ancora_documentos">
  <td colspan="2"><h1><span>UPLOAD DE DOCUMENTOS</span></h1></td>
  </tr>
  <tr>
  <td colspan="2">
  
  <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
      <tr>
        <td style="font-weight:bold;" id="fotosDocumentos">
         <?php if($_GET['foto'] == 'deletado') {
                   echo 'Documento deletado com sucesso!<br>';
         	   }
		
$diretorio_padrao  = $_SERVER["DOCUMENT_ROOT"]."/";
$diretorio_padrao .= "intranet/documentos/";
$dirInternet       = "documentos/";

$regiao  = sprintf("%03d", $row['id_regiao']);
$projeto = sprintf("%03d", $row['id_projeto']);

$Dir     = $regiao.'/'.$projeto.'/'; // RESOLVENDO O NOME DA PASTA ONDE VAI SER CRIADO A PASTA DO USUARIO
$novoDir = $row['tipo_contratacao'].'_'.$row[0]; // RESOLVENDO O NOME DA PASTA DO USUARIO
$DirCom  = $Dir.$novoDir;

$dir          = $diretorio_padrao.$DirCom;
$dirInternet .= $DirCom;

// Abre um diretorio conhecido, e faz a leitura de seu conteudo
if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            if($file == "." or $file == ".."){
				$nada;
			}else{
				$tipoArquivo = explode("_",$file);
				$tipoArquivo = explode(".",$tipoArquivo[2]);
				
				$select = new upload();
				$TIPO = $select -> mostraTipo($tipoArquivo[0]);
				
				$DirFinal = $dirInternet."/".$file;
				
				// Renomeia o arquivo se estiver sem extens�o
				if(!strstr($DirFinal, '.')) {
					$de = $DirFinal;
				    $para = $DirFinal.'.jpg';
				    rename($de, $para);
					$DirFinal .= '.jpg';
				}
				
				$ja_documentos[] = $file;
				
				echo "<div class='documentos'>";
				echo "<a class='documento' href='".$DirFinal."' rel='shadowbox[documentos]' title='Visualizar $TIPO'>";
				echo "<img src='".$DirFinal."' width='75' height='75' border='0' alt='$TIPO'></a>";
				echo "<a href='#' onClick=\"Confirm('$DirFinal')\" title='Deletar $TIPO'>deletar</a>";
				echo "</div>";
				unset($DirFinal);
				
			}
        }
        closedir($dh);
    }
}

if(!empty($ja_documentos)) { 
	foreach($ja_documentos as $documento) {
		$documento = explode('_', $documento);
		$tipo_documento = explode('.', $documento[2]);
		$tipo_documento = $tipo_documento[0];
		$tipos_ja_documentos[] = $tipo_documento;
	}
}
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
                    <?php $qr_documentos = mysql_query("SELECT * FROM upload WHERE status_reg = '1'");
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
			var tipo_contratacao = '<?php switch($row['tipo_contratacao']) {
										case 1:
											echo '1';
										break;
										case 3:
											echo '3';
										break;
										case 4:
											echo '4';
										break;
	   								} ?>';
			var regiao = '<?= sprintf('%03d',$_GET['reg']);?>';
			var projeto = '<?=  sprintf('%03d',$_GET['pro']);?>';
			var id_participante = '<?=  sprintf('%03d',$_GET['bol']);?>';
			
			
			
			$("#uploadDoc").uploadify({
				'uploader'       : 'uploadfy/scripts/uploadify.swf',
				'script'         : 'include/upload_doc.php',
				'buttonImg'      : 'imagens/botao_upload.jpg',
				'buttonText'     : '',
				'cancelImg'      : 'uploadfy/cancel.png',
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
											
											$.post('include/fotos_documentos.php',{
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
		     location.href="upload/uploads.php?enviado=2&participante=<?=$row[0]?>&contratacao=<?=$row['tipo_contratacao']?>&regiao=<?=$id_reg?>&pro=<?=$id_pro?>&arquivo=" + arquivo;
		}
}
</script></td>
              </tr>
            </table>
          </td>
        </tr>
</table>
  
  </td>
  </tr>
  <tr>
  <td colspan="2"><h1><span>ENCAMINHAMENTO DE CONTA</span></h1></td>
  </tr>
  <tr>
  <td colspan="2">
  <form action="declarabancos.php" method="post" name="form1" target="_blank">
      <b>Escolha o Banco:</b>&nbsp;&nbsp;
      <select name="banco" id="banco">
        <?php while($row_ban = mysql_fetch_array($result_ban)){
              print "<option value=$row_ban[id_banco]>$row_ban[nome]</option>"; 
			  }; ?>
        </select>
      <input type="submit" value="Gerar Encaminhamento de Conta">
      <input type="hidden" name="tipo" id="tipo" value="1">
      <input type="hidden" name="bolsista" id="bolsista" value="<?=$id_bol?>">
      <input type="hidden" name="regiao" id="regiao" value="<?=$id_reg?>">
    </form> 
  </td>
  </tr>
  <tr>
  <td colspan="2"><h1><span>CONTROLE DE DOCUMENTOS</span></h1></td>
  </tr>
  <tr>
  <td colspan="2">
  
  <table cellpadding="4" cellspacing="0" style="font-size:13px; border:0px; width:100%;">
      <tr style="font-weight:bold; background-color:#ddd;">
        <td width="70%">DOCUMENTO</td>
        <td width="15%" align="center">STATUS</td>
        <td width="15%" align="center">DATA</td>
      </tr>
      <?php $qr_documentos = mysql_query("SELECT * FROM rh_documentos WHERE tipo_contratacao = '$row[tipo_contratacao]'");
	  while($row_documento = mysql_fetch_array($qr_documentos)) { 
		  
		  $qr_verificacao  = mysql_query("SELECT *, date_format(data, '%d/%m/%Y') AS data FROM rh_doc_status WHERE tipo = '$row_documento[0]' AND id_clt = '$row[0]'");
		  $row_verificacao = mysql_fetch_array($qr_verificacao);
		  $num_verificacao = mysql_num_rows($qr_verificacao);
		  
		  if(!empty($num_verificacao) or ($row_documento['documento'] == 'PIS' and $emissao == true)) {
			  $status = 'imagens/assinado.gif';
		  } else {
			  $status = 'imagens/naoassinado.gif';
		  }
		  
		  if($cor++%2==0) { 
		  	  $fundo_cor = '#fafafa'; 
		  } else { 
		      $fundo_cor = '#f3f3f3';
		  }
		  
		  echo '<tr style="background-color:'.$fundo_cor.'">	  	
		          <td>'.$row_documento['documento'].'</td>
				  <td align="center"><img src="'.$status.'" width="15" height="17"></td>
				  <td align="center">'.$row_verificacao['data'].'</td>
		        </tr>';
		  
	  } ?>
      <tr>
        <td colspan="3" align="center" class="linha" style="font-size:16px;">
          <img src="imagens/assinado.gif" width="15" height="17" align="absmiddle"> Emitido  
          <img src="imagens/naoassinado.gif" width="15" height="17" align="absmiddle"> N&atilde;o Emitido
        </td>
      </tr>
    </table>
    
    </td>
    </tr>
</table>
</div>
       <div id="rodape">
<?php $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$Master'");
      $master = mysql_fetch_assoc($qr_master);
	  ?>
            <p class="left"><img style="position:relative; top:7px;" src="imagens/logomaster<?=$Master?>.gif" width="66" height="46"> <b><?=$master['razao']?></b>&nbsp;&nbsp;Acesso Restrito � Funcion&aacute;rios</p>
            <p class="right"><br><br><a href="#corpo">Subir ao topo</a></p>
            <div class="clear"></div>
  </div>
</div>
<script type="text/javascript">
var Accordion1 = new Spry.Widget.Accordion("Accordion1", { enableAnimation: false, useFixedPanelHeights: false, defaultPanel: -1 });
</script>
</body>
</html>