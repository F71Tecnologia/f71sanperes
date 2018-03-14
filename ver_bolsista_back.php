<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a>";
exit;
}

include "conn.php";
include "upload/classes.php";
include "classes/funcionario.php";
$Fun = new funcionario();
$Fun -> MostraUser(0);
$Master = $Fun -> id_master;

// Obtendo o id do cadastro

$id = 1;
$id_bol = $_REQUEST['bol'];
$id_pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['reg'];
$id_user = $_COOKIE['logado'];

$sql_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($sql_user);

$result = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y')as nova_data, date_format(data_saida, '%d/%m/%Y')as data_saida2, date_format(dataalter, '%d/%m/%Y')as dataalter2 FROM autonomo WHERE id_autonomo = '$id_bol' ");
$row = mysql_fetch_array($result);

$result_tab = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$id_pro' AND status_reg = '1'");
$row_tab = mysql_fetch_array($result_tab);

$sql_user2 = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$row[useralter]'");
$row_user2 = mysql_fetch_array($sql_user2);

$result_ban = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$id_reg' and id_projeto = '$id_pro'");

if($row['status'] =="0") {
$texto = "<font color=red>Data de saída: $row[data_saida2]</font>";
} else {
$texto = "";
}

$nome_arq = str_replace(" ", "_", $row['nome']);	

/* if($row['id_bolsista'] == "0") { // Verificando se o autônomo foi cadastrado depois da mudança da tabela
$id_bolsistaaa = $row['0'];
} else {
$id_bolsistaaa = $row['id_bolsista'];
} */

$id_bolsistaaa = $row['0'];

if($row['foto'] == "1") {
$nome_imagem = $id_reg."_".$id_pro."_".$id_bolsistaaa.".gif";
} else {
$nome_imagem = "semimagem.gif";
}
?>
<html>
<head>
<title>:: Intranet ::</title>
<link rel='shortcut icon' href='favicon.ico'>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="rh/css/estrutura_participante.css" rel="stylesheet" type="text/css">
<link href="rh/css/spry.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="SpryAssets/SpryAccordion.js"></script>
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="js/lightbox.js"></script>
<script type="text/javascript" src="js/highslide-with-html.js"></script>
<link rel="stylesheet" href="js/highslide.css" type="text/css" />
<link rel="stylesheet" href="js/lightbox.css" type="text/css" media="screen" />
</head>
<body>
<div id="corpo">
<div id="conteudo">
<table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
  <tr>
  <td colspan="2">
  <?php if($_GET['sucesso'] == "cadastro") { ?>
  <div id="sucesso">
       Participante cadastrado com sucesso!
  </div>
  <?php } ?>
  <div style="border-bottom:2px solid #F3F3F3; margin-top:10px;">
       <h2 style="float:left; font-size:18px;">VISUALIZAR 
	   <?php switch($row['tipo_contratacao']) {
		        case 1:
	                echo '<span class="aut">AUTÔNOMO</span>';
				break;
				case 3:
				    echo '<span class="coo">COOPERADO</span>';
				break;
				case 4:
				    echo '<span class="aut">AUTÔNOMO / PJ</span>';
				break;
	   } ?>
       </span>
       </h2>
       <p style="float:right;">
       <?php if($_GET['sucesso'] == "cadastro") { 
	          switch($_GET['tipo']) {
		        case 1:
	                echo "<a href='cadastro.php?regiao=$id_reg&pro=$id_pro&id=4'>&laquo; Cadastrar Outro Participante</a>";
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
    <td width="16%">
         <a href="<?php if($row['tipo_contratacao'] == "1") { ?>alter_bolsista.php?bol=<?=$row[0]?>&pro=<?=$id_pro?><?php } else { ?>cooperativas/altercoop.php?coop=<?=$row[0]?><?php } ?>#ancora_foto" title="Alterar Foto"><img src='fotos/<?=$nome_imagem?>' border=1 width='100' height='130'></a></td>
    <td width="84%" bgcolor="#F3F3F3">
         <b><?=$row['campo3']?> - <?=$row['nome']?></b><br>
         <b>Data de Cadastro:</b> <?=$row['nova_data']."<br>".$texto?><br>
         <b>Projeto:</b> <?=$row_tab['id_projeto']?> - <?=$row_tab['nome']?><br>
         <i><?php print "Ultima Alteração feita por <b>$row_user2[nome1]</b> na data $row[dataalter2]"; ?></i>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
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
							$get_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row[banco]'");
					        $banco = mysql_fetch_assoc($get_banco);?>
             <b>Atividade:</b> <?=$atividade['id_curso']?> - <?=$atividade['nome']?><br>
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
                       <?php if(!empty($banco['nome'])) { echo $banco['nome']; } else { echo "<i>Não informado</i>"; } ?>
                </div>
            </div>
       </div>   
               </td>
             </tr>
         </table>
    </td>
  </tr>
  <tr>
    <td colspan="2"><div id="observacoes"><?php if(empty($row['observacao'])) { echo "Sem Observações"; } else { echo "<b>Observações</b><p>&nbsp;</p> $row[observacao]"; } ?></div></td>
  </tr>
   <tr>
    <td colspan="2"><h1><span>MENU DE EDIÇÃO</span></h1></td>
  </tr>
  <tr>
  <td colspan="2" class="menu">
  <?php // Consulta para Links
        if($row_user['grupo_usuario'] == "3"){
		      $botao_editar = NULL;
	    } else {
		    if($row['tipo_contratacao'] == "1") {
			      $botao_editar = "<a href='alter_bolsista.php?bol=$row[0]&pro=$id_pro' class='botao'>Editar Cadastro</a>";
		    } elseif($row['tipo_contratacao'] == "3") {
			      $botao_editar = "<a href='cooperativas/altercoop.php?coop=$row[0]&tipo=3' class='botao'>Editar Cadastro</a>";
			} elseif($row['tipo_contratacao'] == "4") {
			      $botao_editar = "<a href='cooperativas/altercoop.php?coop=$row[0]&tipo=4' class='botao'>Editar Cadastro</a>";
		    }
		}
		
		if (!empty($row['pis'])) {
	          $statusBotao = 'none';
	          $emissao = true;
	      } else {
	          $statusBotao = 'inline';	
	          $emissao = false;
         }
		
       switch($row['tipo_contratacao']) {
		   // Links para Autonomos
		   case 1: ?>
       
       <!-- linha 1 -->
       <p><?=$botao_editar?>
          <a href="contrato.php?bol=<?=$row['0']?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Contrato</a>
          <a href="distrato.php?bol=<?=$row['0']?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Distrato</a></p>
       <!-- linha 2 -->
       <p><a href="tvsorrindo2.php?bol=<?=$row['0']?>&pro=<?=$id_pro?>" class="botao" target="_blank">TV Sorrindo</a>
          <a href="declararenda.php?bol=<?=$row['0']?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Declaração</a>
          <a href="certificado.php?bol=<?=$row['0']?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Certificado</a></p>
       <!-- linha 3 -->
       <p><a href="contrato2via.php?bol=<?=$row['0']?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Segunda Via</a>
          <a href="rendimento/index.php?bol=<?=$row['0']?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank" style="font-size:12px;">Informe de Rendimento</a></p>
       
      <?php // Links para Cooperados
	        break;
			case 3: ?>
      
      <!-- linha 1 -->
      <p><?=$botao_editar?>
         <a href="cooperativas/tvsorrindo.php?coop=<?=$row[0]?>&pro=<?=$id_pro?>" class="botao" target="_blank">TV Sorrindo</a>
         <a href="cooperativas/contratos/contrato<?=$row["id_cooperativa"]?>.php?coop=<?=$row[0]?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Adesão</a></p>
      <!-- linha 2 -->
      <p><a href="cooperativas/quotas.php?coop=<?=$row[0]?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Quotas</a>
         <a href="cooperativas/fichadecadastro.php?bol=<?=$row[0]?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Ver Ficha</a>
         <a href="cooperativas/distrato.php?coop=<?=$row[0]?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Desligamento</a></p>
      <!-- linha 3 -->
      <p><a href="rh/solicitapis_pdf.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&bol=<?=$row[0]?>" class="botao" target="_blank">Gerar PIS</a></p>
      
      <?php // Links para PJ
	        break;
			case 4: ?>
            
      <!-- linha 1 -->
      <p><?=$botao_editar?>
         <a href="cooperativas/tvsorrindo.php?coop=<?=$row[0]?>&pro=<?=$id_pro?>" class="botao" target="_blank">TV Sorrindo</a>
         <a href="cooperativas/fichadecadastro.php?bol=<?=$row[0]?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Ver Ficha</a></p>
         
     <?php } ?>

  </td>
  <tr>
  <td colspan="2"><h1><span>UPLOAD DE DOCUMENTOS</span></h1></td>
  </tr>
  <tr>
  <td colspan="2">
  
  <table width="454" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
      <tr>
        <td>
          <div id="foto"></div>
          <a href='upload/uploads.php?participante=<?=$row[0]?>&contratacao=<?=$row['tipo_contratacao']?>&regiao=<?=$id_reg?>&pro=<?=$id_pro?>'>
          <img src="imagens/botao_upload.jpg" width="156" height="42" border="0"></a></td>
        </tr>
      <tr>
        <td style="font-weight:bold;">
          <?php if($_GET['foto'] == "enviado") { ?>
          Documento(s) enviado(s) com sucesso!
          <?php } elseif($_GET['foto'] == "deletado") { ?>
          Documento deletado com sucesso!
          <?php } ?>
          <table border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td>
                &nbsp;<?php

$diretorio_padrao = $_SERVER["DOCUMENT_ROOT"]."/";
$diretorio_padrao .= "intranet/documentos/";
$dirInternet = "documentos/";

$regiao = sprintf("%03d", $row['id_regiao']);
$projeto = sprintf("%03d", $row['id_projeto']);

$Dir = $regiao."/".$projeto."/";					//RESOLVENDO O NOME DA PASTA ONDE VAI SER CRIADO A PASTA DO USUARIO
$novoDir = $row['tipo_contratacao']."_".$row[0];			//RESOLVENDO O NOME DA PASTA DO USUARIO
$DirCom = $Dir.$novoDir;

$dir = $diretorio_padrao.$DirCom;
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
				
				echo "<div class='documentos'>";
				echo "<a class='documento' href='".$DirFinal."' rel='lightbox' title='Visualizar $TIPO'>";
				echo "<img src='".$DirFinal."' width='75' height='75' border='0' alt='$TIPO'></a>";
				echo "<a href='#' onClick=\"Confirm('$DirFinal')\">deletar</a>";
				echo "</div>";

			}
        }
        closedir($dh);
    }
}
?>
<script language="javascript">
function Confirm(a){
	var arquivo = a;
	input_box = confirm("Deseja realmente excluir o documento?");
	    if(input_box==true) { 
		     location.href="upload/uploads.php?enviado=2&participante=<?=$row[0]?>&contratacao=<?=$row['tipo_contratacao']?>&regiao=<?=$id_reg?>&pro=<?=$id_pro?>&arquivo=" + arquivo;
		}
}
</script>
</td>
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
  <form action="declarabancos.php" method='post' name='form1' target='_blank'>
      <b>Escolha o Banco:</b>&nbsp;&nbsp;
      <select name="banco" id="banco">
        <?php while($row_ban = mysql_fetch_array($result_ban)){
              print "<option value=$row_ban[id_banco]>$row_ban[nome]</option>"; 
			  }; ?>
        </select>
      <input type=submit value='Gerar Encaminhamento de Conta'>
      <input type='hidden' name='tipo' id='tipo' value="1">
      <input type='hidden' name='bolsista' id='bolsista' value=<?=$id_bol?>>
      <input type='hidden' name='regiao' id='regiao' value=<?=$id_reg?>>
    </form> 
  </td>
  </tr>
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
	  $bolsista = $_GET['bol'];

	      $qr_tipo = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$bolsista'");
	      $tipo = mysql_fetch_assoc($qr_tipo);
	      $tipo_contratacao = $tipo['tipo_contratacao'];
		
	  $result_docs = mysql_query("SELECT * FROM rh_documentos WHERE tipo_contratacao = '$tipo_contratacao'");
	  
	  while($row_docs = mysql_fetch_array($result_docs)){  
	  if($cont % 2){ $color="#fafafa"; }else{ $color="#f3f3f3"; }
	  
	  $result_verifica = mysql_query("SELECT *,date_format(data, '%d/%m/%Y')as data FROM rh_doc_status WHERE tipo = '$row_docs[0]' and id_clt = '$row[0]'");
	  $num_row_verifica = mysql_num_rows($result_verifica);
	  $row_verifica_doc = mysql_fetch_array($result_verifica);
	  
	  if($num_row_verifica != "0"){
	  $img = "<img src='imagens/assinado.gif' width='15' height='17' align='absmiddle'>";
	  $data = $row_verifica_doc['data'];
	  }else{
	  $img = "<img src='imagens/naoassinado.gif' width='15' height='17' align='absmiddle'>";
  	  $data = "";
	  }
	echo "<tr bgcolor=$color>";	  	
    echo "<td class='linha'>$row_docs[documento]</td>";
	if (($row_docs['documento']=='PIS')and($emissao==true)){
	  $img = "<img src='imagens/assinado.gif' width='15' height='17' align='absmiddle'>";
	  echo "<td class='linha' align='center'>$img</td>";
	  }elseif(($row_docs['documento']!='PIS')or($emissao==false)){
		  echo "<td class='linha' align='center'>$img</td>";
	  }
    echo "<td class='linha'>$data</td>";
    echo  "</tr>";
	
	  $cont ++;
	  $img = "";
	  $data = "";
	  }
	  
	  ?>
      <tr>
        <td colspan="3" align="center" class="linha" style="font-size:16px;"><img src="imagens/assinado.gif" width="15" height="17" align="absmiddle"> Emitido  <img src="imagens/naoassinado.gif" width="15" height="17" align="absmiddle"> N&atilde;o Emitido</td>
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
            <p class="left"><img style="position:relative; top:7px;" src="imagens/logomaster<?=$Master?>.gif" width="66" height="46"> <b><?=$master['razao']?></b>&nbsp;&nbsp;Acesso Restrito à Funcion&aacute;rios</p>
            <p class="right"><br><br><a href="#corpo">Subir ao topo</a></p>
            <div class="clear"></div>
        </div>
</div>
<script type="text/javascript">
var Accordion1 = new Spry.Widget.Accordion("Accordion1", { enableAnimation: false, useFixedPanelHeights: false, defaultPanel: -1 });
</script>
</body>
</html>