<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "conn.php";
include "upload/classes.php";

//PEGANDO O ID DO CADASTRO

$id = 1;
$id_bol = $_REQUEST['bol'];
$id_pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['reg'];
$id_user = $_COOKIE['logado'];

$sql_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($sql_user);

$result = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y')as nova_data, date_format(data_saida, '%d/%m/%Y')as data_saida2, date_format(dataalter, '%d/%m/%Y')as dataalter2 FROM autonomo where id_autonomo = '$id_bol' ");
$row = mysql_fetch_array($result);

$result_tab = mysql_query("SELECT * FROM projeto where id_projeto = '$id_pro' ");
$row_tab = mysql_fetch_array($result_tab);

$sql_user2 = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row[useralter]'");
$row_user2 = mysql_fetch_array($sql_user2);

$result_ban = mysql_query("SELECT * FROM bancos where id_regiao = '$id_reg' and id_projeto = '$id_pro'");

	if($row_user['grupo_usuario'] == "3"){
		$botao_editar = "";
	}else{
		if ($row['tipo_contratacao'] == "1"){
			$botao_editar = "<a href='alter_bolsista.php?bol=$row[0]&pro=$id_pro' class='link'><img src='imagens/editar_bolsista.gif' border=0></a>";
			$display="style='display:none'";
		}else{
			$botao_editar="<a href='cooperativas/altercoop.php?coop=$row[0]' class='link'><img src='imagens/editar_bolsista.gif' border=0></a>";
			$display="style='display:'";
			
		}
	}

//MONTANDO OS BOTÕES QUE VAO APARECER
if ($row['tipo_contratacao'] == "1"){	// BOTÕES PARA AUTONOMOS
$tipo_contra = "
<center>
$botao_editar
&nbsp;&nbsp;&nbsp;
<a href='contrato.php?bol=$row[0]&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/gerar_contrato.gif' border=0></a>
&nbsp;&nbsp;&nbsp;
<a href='distrato.php?bol=$row[0]&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/gerar_distrato.gif' border=0></a>
<BR>
<a href='tvsorrindo2.php?bol=$row[0]&pro=$id_pro' class='link' target='_blak'><img src='imagens/tvsorrindo.gif' border=0></a>
&nbsp;&nbsp;&nbsp;
<a href='declararenda.php?bol=$row[0]&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/declara_renda.gif' border=0></a>
&nbsp;&nbsp;&nbsp;
<a href='certificado.php?bol=$row[0]&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/certificado.gif' border=0></a>
<BR>
&nbsp;&nbsp;&nbsp;
<a href='contrato2via.php?bol=$row[0]&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/contrato2via.gif' border=0></a>
</center>";
}else{		//--------------- BOTÕES PARA COOPERADOS
$tipo_contra = "
<center>
$botao_editar
&nbsp;&nbsp;&nbsp;
<a href='cooperativas/tvsorrindo.php?coop=$row[0]&pro=$id_pro' class='link' target='_blak'><img src='imagens/tvsorrindo.gif' border=0></a>
&nbsp;&nbsp;&nbsp;
<a href='cooperativas/contratos/contrato".$row['id_cooperativa'].".php?coop=$row[0]&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/adesao.gif' border=0></a>
<br>
<a href='cooperativas/quotas.php?coop=$row[0]&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/quota.gif' border=0></a>
&nbsp;&nbsp;&nbsp;
<a href='cooperativas/fichadecadastro.php?bol=$row[0]&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/fichadecadastroclt.gif' border=0></a>
&nbsp;&nbsp;&nbsp;
<a href='cooperativas/distrato.php?coop=$row[0]&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/desligamento.gif' border=0></a>
&nbsp;&nbsp;&nbsp;
<a href='rh/solicitapis_pdf.php?pro=$id_pro&id_reg=$id_reg&bol=$row[0]' class='link' target='_blak'><img src='imagens/gerapis.gif' border=0></a>
<br>
$movendo
<br>

</center>";
}

if($row['status'] =="0"){
$texto = "<font color=red>Data de saída: $row[data_saida2]</font>";
}else{
$texto = "";
}

$nome_arq = str_replace(" ", "_", $row['nome']);	

if($row['id_bolsista'] == "0"){ //VERIFICANDO SE O AUTONOMO FOI CADASTRADO DEPOIS DA MUDANÇA DA TABELA
$id_bolsistaaa = $row['0'];
}else{
$id_bolsistaaa = $row['id_bolsista'];
}

if($row['foto'] == "1"){
$nome_imagem = $id_reg."_".$id_pro."_".$id_bolsistaaa.".gif";
}else{
$nome_imagem = "semimagem.gif";
}

// INICIO DO PÁGINA QUE RODA EM TODOS OS TIPOS DE CADASTRO
?>

<html><head><title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="js/lightbox.js"></script>
<script type="text/javascript" src="js/highslide-with-html.js"></script>
<link rel="stylesheet" type="text/css" href="js/highslide.css" />
<link rel="stylesheet" href="js/lightbox.css" type="text/css" media="screen" />
<link href="net1.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style2 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
}
.style5 {color: #FF0000}
.style6 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.style11 {font-weight: bold}
.style13 {font-weight: bold}
.style15 {font-weight: bold}
.style17 {font-weight: bold}
.style19 {font-weight: bold}
.style23 {font-weight: bold}

.style24 {
	font-size: 10px;
	font-weight: bold;
	color: #003300;
}
.style25 {color: #003300}
.style26 {
	color: #FFFFFF;
	font-size: 10px;
}
.style27 {color: #FFFFFF; }
.borda {border:#999 2px solid; }
.bordafina {border:#999 1px solid; float: left; margin:5px; width:50px; }
.bordafina2 {border:#00F 1px solid; float: left; margin:5px; width:50px; background-color: #A0ADEB}

-->
</style>
<script type="text/javascript">
    hs.graphicsDir = 'images-box/graphics/';
    hs.outlineType = 'rounded-white';
</script>
</head>


<body bgcolor='#D7E6D5'>
<table width='454' border='0' cellpadding='5' cellspacing='0' bgcolor='#FFFFFF' class='bordaescura1px'  align='center'>
<tr><td colspan='2' bgcolor="#CCCCCC">
<br><div align='center' class='campotexto4'>VISUALIZAR PARTICIPANTE</div>
<br></td></tr>



<tr>
  <td align='center' colspan='2' class="style3">Integrante:</td>
</tr>

<tr><td colspan='2' align='center'><div class="style2"> <font size=3> <?=$row['nome']?></font></div><br>
<img src='fotos/<?=$nome_imagem?>' border=1 width='100' height='130'>
</td></tr>

<tr>
  <td width="190" align='right' class="style3">Data de Cadastro:&nbsp;</td>
  <td width="262" align='center' class="style3"><div align="left" class="style2">
    <?=$row['nova_data']."  ".$texto?>
  </div></td>
</tr>
<tr>
<td align='right' class="style3">Projeto:&nbsp;</td>
<td align='center' class="style3"><div align="left" class="style2">
  <?=$row_tab['nome']?>
</div></td>
</tr>
<tr>
<td colspan='2' align='center' bgcolor="#FFFFCC" class="style3"><div align="center">Observações: 
</div>
  <div class="style2">
    <?=$row['observacao']?></div></td>
</tr>


<tr>
  <td align='center' class="style3"><div align="right">Ultima Alteração feita por:&nbsp;</div>    </td>
  <td align='center' class="style3"><div class="style2">
    <div align="left">
      <?=$row_user2['nome1']?>
      </div>
  </div></td>
</tr>
<tr>
<td align='center' class="style3"><div align="right">Data:&nbsp;</div>  </td>
<td align='center' class="style3"><div class="style2">
  <div align="left">
    <?=$row['dataalter2']?>
  </div>
</div></td>
</tr>

<tr>
<td align='center' colspan=2>&nbsp;</td>
</tr>
</table>
<br>
<?=$tipo_contra?>
<table width="454" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center" class="bordaescura1px" <?=$display?>>
  <tr>
    <td height="38" align="center" bgcolor="#CCCCCC">
    <div id="foto"></div>
    <a href='upload/uploads.php?participante=<?=$row[0]?>&contratacao=<?=$row['tipo_contratacao']?>&regiao=<?=$id_reg?>&pro=<?=$id_pro?>'>
    <img src="imagens/enviar_arquivo.gif" width="180" height="32" border="0"></a></td>
  </tr>
  <tr>
    <td height="19" align="center">
    <?php if($_GET['foto'] == "enviado") { ?><span style="font-weight:bold;">Documento(s) enviado(s) com sucesso!</span><?php } elseif($_GET['foto'] == "deletado") { ?><span style="font-weight:bold;">Documento deletado com sucesso!</span><?php } ?>
      <table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="center" valign="middle">&nbsp;<?php

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
				
				echo "<div class='bordafina' ";
				echo "onMouseOver=\"document.getElementById(this.id).className='bordafina2'\" ";
				echo "onMouseOut=\"document.getElementById(this.id).className='bordafina'\"";
				echo "id='$tipoArquivo[0]'><br>";
				echo "<a href='".$DirFinal."' rel='lightbox' title='$TIPO'>";
				echo "<img src='".$DirFinal."' width='25' height='25' border='0' alt='$TIPO'></a>";
				echo "<hr><a href='#' onClick=\"Confirm('$DirFinal')\" style='color:red'>";
				echo "deletar</a></div>";

			}
        }
        closedir($dh);
    }
}
?></td>
      </tr>
    </table>
    <br></td>
  </tr>
</table>

<script language="javascript">
    
function Confirm(a){
	var arquivo = a;
	
	input_box=confirm("Deseja realmente DELETAR?");
	
	if (input_box==true){ 
		// Output when OK is clicked
		// alert (\"You clicked OK\"); 
		location.href="upload/uploads.php?enviado=2&participante=<?=$row[0]?>&contratacao=<?=$row['tipo_contratacao']?>&regiao=<?=$id_reg?>&pro=<?=$id_pro?>&arquivo=" + arquivo;
		}else{
		// Output when Cancel is clicked
		// alert (\"You clicked cancel\");
	}

}
    
</script>

<form action='declarabancos.php' method='post' name='form1' target='_blanc' class="campotexto4">
  <br><br><center><b>Escolha o Banco:</b>&nbsp;&nbsp;&nbsp;
<select name='banco' id='banco'>
<?php
while($row_ban = mysql_fetch_array($result_ban)){
  print "<option value=$row_ban[id_banco]>$row_ban[nome]</option>";
  }

?>
</select>
<input type='hidden' name='bolsista' id='bolsista' value='<?=$id_bol?>'>
<input type='hidden' name='regiao' id='regiao' value='<?=$id_reg?>'>
<input type='hidden' name='tipo' id='tipo' value='1'>
&nbsp;&nbsp;&nbsp;<input type=submit value='Gerar Encaminhamento de Conta'></center></form>

<p>&nbsp;</p>

<table width='500' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' align='center' class="bordaescura1px">
  <tr>
    <td width='3%' valign='top'>&nbsp;</td>
    <td width='94%'>&nbsp;</td>
    <td width='3%' align='right' valign='top'>&nbsp;</td>
  </tr>
  <tr>
    <td height='100'>&nbsp;</td>
    <td><table width="100%" border="0" cellpadding="0" cellspacing="4">
      <tr>
        <td colspan="3" align="center" bgcolor="#CCCCCC" class="styleobs">CONTROLE DE DOCUMENTOS</td>
      </tr>
      <tr class="linha">
        <td width="70%" align="center" bgcolor="#CCCCCC"><strong>DOCUMENTO</strong></td>
        <td width="15%" align="center" bgcolor="#CCCCCC"><strong>STATUS</strong></td>
        <td width="15%" align="center" bgcolor="#CCCCCC">DATA</td>
      </tr>
      <?php
	  $cont = "1";
	  $bolsista = $_GET['bol'];

	      $qr_tipo = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$bolsista'");
	      $tipo = mysql_fetch_assoc($qr_tipo);
	      $tipo_contratacao = $tipo['tipo_contratacao'];
		
	  $result_docs = mysql_query("SELECT * FROM rh_documentos WHERE tipo_contratacao = '$tipo_contratacao'");
	  
	  while($row_docs = mysql_fetch_array($result_docs)){  
	  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
	  
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
    //echo "<td class='linha' align='center'>$img</td>";
	if (($row_docs['documento']=='Inscrição no PIS')and($emissao==true)){
	  $img = "<img src='imagens/assinado.gif' width='15' height='17' align='absmiddle'>";
	  echo "<td class='linha' align='center'>$img</td>";
	  }elseif(($row_docs['documento']!='Inscrição no PIS')or($emissao==false)){
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
        <td colspan="3" align="center" class="linha">STATUS - <img src="imagens/assinado.gif" width="15" height="17" align="absmiddle">= Emitido  <img src="imagens/naoassinado.gif" width="15" height="17" align="absmiddle">= N&atilde;o Emitido</td>
      </tr>
    </table></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td valign='bottom'>&nbsp;</td>
    <td>&nbsp;</td>
    <td valign='bottom' align='right'>&nbsp;</td>
  </tr>
</table>



<br><a href='bolsista.php?projeto=<?=$id_pro?>&regiao=<?=$id_reg?>' class='link'><img src='imagens/voltar.gif' border=0></a>



</body>
</html>
