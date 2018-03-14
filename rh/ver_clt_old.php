<?php

if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "../conn.php";
include "../upload/classes.php";

//PEGANDO O ID DO CADASTRO

$id = 1;
$id_clt = $_REQUEST['clt'];
$id_ant = $_REQUEST['ant'];
$id_pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['reg'];
$id_user = $_COOKIE['logado'];

$pagina = $_REQUEST['pagina'];

$sql_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($sql_user);

$result = mysql_query(" SELECT *, date_format(data_entrada, '%d/%m/%Y')as nova_data, date_format(data_saida, '%d/%m/%Y')as data_saida2, date_format(dataalter, '%d/%m/%Y')as dataalter2 FROM rh_clt where id_clt = $id_clt ", $conn);
$row = mysql_fetch_array($result);
/*
$result_data_entrada = mysql_query("
SELECT data_entrada, DATE_ADD(data_entrada, INTERVAL '90' DAY) AS data_contratacao, CASE WHEN data_entrada < DATE_SUB(CURDATE(), INTERVAL '90' DAY) THEN 'Contratado' WHEN data_entrada > DATE_SUB(CURDATE(), INTERVAL '90' DAY) AND data_entrada <=CURDATE() THEN 'Em experiência' ELSE 'Aguradando' END AS status_contratacao FROM rh_clt where id_clt = '$id_clt' ", $conn) or die(mysql_error());
*/

$result_data_entrada = mysql_query("
SELECT data_entrada, DATE_ADD(data_entrada, INTERVAL '90' DAY) AS data_contratacao, CASE WHEN data_entrada < DATE_SUB(CURDATE(), INTERVAL '90' DAY) THEN 'Contratado' WHEN data_entrada > DATE_SUB(CURDATE(), INTERVAL '90' DAY) AND data_entrada <=CURDATE() THEN 'Em experiência até ' ELSE 'Aguardando' END AS status_contratacao FROM rh_clt where id_clt = '$id_clt' ", $conn) or die(mysql_error());

while($row2 = mysql_fetch_array($result_data_entrada)){
$data_contratacao = $row2["data_contratacao"]; //Data padrão DD-MM-YYYY
$status_contratacao = $row2["status_contratacao"];


}
$data_contratacao2 = explode("-",$data_contratacao);
$dia_data_contratacao = $data_contratacao2[2];
$mes_data_contratacao = $data_contratacao2[1];
$ano_data_contratacao = $data_contratacao2[0];

$data_contratacao = date("d/m/Y", mktime (0, 0, 0, $mes_data_contratacao  , $dia_data_contratacao , $ano_data_contratacao));

$result_pro = mysql_query("SELECT * FROM projeto where id_projeto = '$id_pro' ");
$row_pro = mysql_fetch_array($result_pro);

$sql_user2 = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row[useralter]'");
$row_user2 = mysql_fetch_array($sql_user2);

$result_ban = mysql_query(" SELECT * FROM bancos where id_regiao = '$id_reg' and id_projeto = '$id_pro'");

if($row['status'] =="62"){
$texto = "<font color=red>Data de saída: $row[data_saida2]</font>";
}else{
$texto = "";
}

$nome_para_arquivo = $row['1'];
	
if($row['foto'] == "1"){
	
	if($nome_para_arquivo == "0"){
		$nome_imagem = $id_reg."_".$id_pro."_".$row['0'].".gif";
	}else{
		$nome_imagem = $id_reg."_".$id_pro."_".$nome_para_arquivo.".gif";
	}
}else{
$nome_imagem = "semimagem.gif";
}

// INICIO DO PÁGINA QUE RODA EM TODOS OS TIPOS DE CADASTRO
?>

<html><head><title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../net1.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/prototype.js"></script>
<script type="text/javascript" src="../js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="../js/lightbox.js"></script>
<script type="text/javascript" src="../js/highslide-with-html.js"></script>
<link rel="stylesheet" type="text/css" href="../js/highslide.css" />
<link rel="stylesheet" href="../js/lightbox.css" type="text/css" media="screen" />
<style type='text/css'>
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
<!--
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var j
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>
</head>


<body bgcolor="#D7E6D5">
<table width='454' height='410' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='bordaescura1px' align='center'>
<tr><td colspan='2' bgcolor='#999999'><br> <div align='center' class='campotexto4'>VISUALIZAR PARTICIPANTE</div>
  <BR></td></tr>

<tr><td width='136'>&nbsp;</td><td width='318'>&nbsp;</td></tr>

<tr><td align='center' colspan='2'><b>
  <?
  if($row['status'] == "200"){
	  
	  echo '<span style="color:red">Aguardando Demissão</span>'; 
	  $divbotes = "style='display:none'";
	  
  }else{
	  
	  $divbotes = "style='display:'";
  
  
  	if ($status_contratacao == 'Contratado'){
  	echo '<span style="color:#00F">'.$status_contratacao.'</span>'; 
	}
  	if ($status_contratacao == 'Em experiência até '){
  	echo '<span style="font-size:14px; font-style:inherit; color:#F00">'.$status_contratacao.' '.$data_contratacao.'</span>'; 
	}
	if ($status_contratacao == 'Aguardando'){
  	echo '<span style="color:black">'.$status_contratacao.'</span>';
	}
	
	echo "<br>";
	
	$REStatus = mysql_query("SELECT especifica FROM rhstatus where codigo = '$row[status]'");
	$rowStatus = mysql_fetch_array($REStatus);
	
	if($row['status'] != 10){
		$MSGStatus = "<div style='color:#FF0000;font-size:14px;'>".$rowStatus['0']."</div>";
	}else{
		$MSGStatus = "<div style='color:#0066FF'>".$rowStatus['0']."</div>";
	}
	
	echo $MSGStatus;
	
	
  }
  ?></span>
</b></td></tr>
<tr><td align='center' colspan='2'>&nbsp;</td></tr>
<tr><td colspan='2' align='center' class="style2"><font size=3> <?=$row[nome]?></font><br><br>
<img src='../fotosclt/<?=$nome_imagem?>' border=1 width='100' height='130'>
</td></tr>
<tr><td align='right'>&nbsp;</td><td>&nbsp;&nbsp;</td></tr>
<tr>
<td align='center' class="style3">
  <div align="right">Data de Cadastro:&nbsp;   <br>
  </div></td>
<td align='center'><div align="left"><div class="style2">
  <?=$row[nova_data]?>
  <?=$texto?></div>
</div></td>
</tr>
<tr>
  <td align='center' class="style3"><div align="right">Projeto:&nbsp;&nbsp;
        </div>
  </div></td>
  <td align='center'><div class="style2" align="right">
    <div class="style2" align="left"><?=$row_pro[nome]?>
  </div></td>
</tr>
<tr>
<td colspan='2' align='center' bgcolor="#FFFFCC"><br>
  <span class="style3">Observações:</span><br>
   <div class="style2"><?=$row['observacao']?></div><br></td>
</tr>
<tr>
<td align='right'>&nbsp;</td>
<td>&nbsp;&nbsp; &nbsp;</td>
</tr>

<tr>
<td align='center' colspan='2'><div class="style2">
<?php print "Ultima Alteração feita por $row_user2[nome1] na data $row[dataalter2]"; ?></div></td>
</tr>

<tr>
<td height='33' align='left' valign='bottom'>&nbsp;</td>
<td align='right' valign='bottom'>&nbsp;</td>
</tr>
</table>



<center>
<?php
/*
print "
<a href='alter_clt.php?clt=$row[0]&pro=$id_pro' class='link'>
<img src='imagens/editar_bolsista.gif' border=0></a>
&nbsp;&nbsp;&nbsp;
<a href='tvsorrindo.php?bol=$row[id_bolsista]&tab=bolsista$id_pro&pro=$id_pro' class='link' target='_blak'><img src='imagens/tvsorrindo.gif' border=0></a>
&nbsp;&nbsp;&nbsp;
<a href='contrato_clt.php?bol=$row[id_bolsista]&tab=bolsista$id_pro&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/cadastrofuncionario.gif' border=0></a>
<br>
<a href='dispensa.php?bol=$row[id_bolsista]&tab=bolsista$id_pro&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/dispensa.gif' border=0></a>
&nbsp;&nbsp;&nbsp;
<a href='fichadecadastroclt.php?bol=$row[id_bolsista]&tab=bolsista$id_pro&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/fichadecadastroclt.gif' border=0></a>
&nbsp;&nbsp;&nbsp;
<a href='distrato_clt.php?bol=$row[id_bolsista]&tab=bolsista$id_pro&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/examedemissional.gif' border=0></a>
<BR>
<a href='cartadereferencia.php?bol=$row[id_bolsista]&tab=bolsista$id_pro&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/cartadereferencia.gif' border=0></a>
&nbsp;&nbsp;&nbsp;
<a href='solicitavale.php?bol=$row[id_bolsista]&tab=bolsista$id_pro&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/solicitavale.gif' border=0></a>
</center>";
*/

?>

<br>
<table width="620" border="0" align="center" cellpadding="0" cellspacing="0" class="bordaescura1px">
  <tr>
    <td align="center" valign="middle" bgcolor="#CCCCCC">
    <br />
    <a href="alter_clt.php?clt=<?=$row['0']?>&pro=<?=$id_pro?>&pagina=<?=$pagina?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image1','','../imagens/botoes_clts/editar2.gif',1)">
<img src="../imagens/botoes_clts/editar1.gif" name="Image1" width="150" height="22" border="0" id="Image1" /></a>
    
    <div <?=$divbotes?>>
    
<a href="../ctps.php?regiao=<?=$id_reg?>&id=1&clt=<?=$row['0']?>" target="_blank" onMouseOver="MM_swapImage('Image2','','../imagens/botoes_clts/receber_ctps2.gif',1)" onMouseOut="MM_swapImgRestore()"><img src="../imagens/botoes_clts/receber_ctps1.gif" name="Image2" width="150" height="22" border="0" id="Image2" /></a>

<?php
//http://www.netsorrindo.com.br/intranet/ -->
$result_entregar = mysql_query("SELECT * FROM controlectps where nome = '$row[nome]'");
$num_row_entregar = mysql_num_rows($result_entregar);
if($num_row_entregar != "0"){
	$row_entregar = mysql_fetch_array($result_entregar);
	$link_ctps = "'../ctps_entregar.php?case=1&regiao=$id_reg&id=$row_entregar[0]' target='_blank'";
}else{
	$link_ctps = "'#'";
}
?>

<a href=<?=$link_ctps?> onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image3','','../imagens/botoes_clts/entregar_ctps2.gif',1)">
<img src="../imagens/botoes_clts/entregar_ctps1.gif" name="Image3" width="150" height="22" border="0" id="Image3" /></a>

<a href="admissional_clt.php?clt=<?=$row['0']?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image4','','../imagens/botoes_clts/exame2.gif',1)" target="_blank">
<img src="../imagens/botoes_clts/exame1.gif" name="Image4" width="150" height="22" border="0" id="Image4" /></a>

<a href="contratoclt.php?id_reg=<?=$id_reg?>&clt=<?=$row['0']?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image5','','../imagens/botoes_clts/contrato2.gif',1)" target="_blank"><img src="../imagens/botoes_clts/contrato1.gif" name="Image5" width="150" height="22" border="0" id="Image5" /></a>

<a href="../fichadecadastroclt.php?bol=<?=$row['id_antigo']?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$row['0']?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image6','','../imagens/botoes_clts/ficha_cadastro2.gif',1)" target="_blank">
<img src="../imagens/botoes_clts/ficha_cadastro1.gif" name="Image6" width="150" height="22" border="0" id="Image6" /></a>

<a href="../tvsorrindo.php?bol=<?=$row['id_antigo']?>&clt=<?=$row['0']?>&pro=<?=$id_pro?>&tipo=2" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image7','','../imagens/botoes_clts/tvsorrindo2.gif',1)" target="_blank"><img src="../imagens/botoes_clts/tvsorrindo1.gif" name="Image7" width="150" height="22" border="0" id="Image7" /></a>

<a href="salariofamilia/safami.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$row['0']?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image8','','../imagens/botoes_clts/beneficios2.gif',1)" target="_blank">
<img src="../imagens/botoes_clts/beneficios1.gif" name="Image8" width="150" height="22" border="0" id="Image8" /></a>
  
  <br>
<a href="vt/vt.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$row['0']?>" target="_blank" onMouseOver="MM_swapImage('Image9','','../imagens/botoes_clts/valetransporte2.gif',1)" onMouseOut="MM_swapImgRestore()"> <img src="../imagens/botoes_clts/valetransporte1.gif" name="Image9" width="150" height="22" border="0" id="Image9" /></a>
<?
//Habilita ou desabilita o botão "Cadastrar PIS", caso o campo pis da tabela rh_clt, não esteja preenchido.
if ($row['pis'] != ""){
	$statusBotao = 	'none';
	$emissao = true;
	}else{
	$statusBotao = 	'inline';	
	$emissao = false;
}
?><a href="solicitapis_pdf.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$row['0']?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image133','','../imagens/botoes_clts/solicita2.gif',1)" target="_blank">
<img src="../imagens/botoes_clts/solicita1.gif" name="Image133" width="150" height="22" border="0" id="Image133" style="display:<?=$statusBotao?>" /></a>

<br>
    <hr>
    <a href="../rh/notifica/advertencia.php?clt=<?=$row['0']?>&tab=bolsista<?=$id_pro?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" 
    onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image100','','../imagens/botoes_clts/advertencia2.gif',1)" target="_blank">
<img src="../imagens/botoes_clts/advertencia1.gif" name="Image100" width="150" height="22" border="0" id="Image100" /></a>
    
    <a href="../rh/notifica/form_suspencao.php?clt=<?=$row['0']?>&tab=bolsista<?=$id_pro?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image101','','../imagens/botoes_clts/suspencao2.gif',1)" target="_blank">
<img src="../imagens/botoes_clts/suspencao1.gif" name="Image101" width="150" height="22" border="0" id="Image101" /></a>
    
    <a href="../dispensa.php?clt=<?=$row['0']?>&tab=bolsista<?=$id_pro?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image131','','../imagens/botoes_clts/dispensa2.gif',1)" target="_blank"></a>

<a href="demissionalclt.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$row['0']?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image132','','../imagens/botoes_clts/examedemi2.gif',1)" target="_blank"></a><a href="cartadereferencia.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$row['0']?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image10','','../imagens/botoes_clts/carta_referencia2.gif',1)" target="_blank"><img src="../imagens/botoes_clts/carta_referencia1.gif" name="Image10" width="150" height="22" border="0" id="Image10" /></a><br>
    <hr>
    <a href="cartadereferencia.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$row['0']?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image10','','../imagens/botoes_clts/carta_referencia2.gif',1)" target="_blank"></a><a href="#" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image13','','../imagens/botoes_clts/declaracao_renda2.gif',1)">
    </a>
    
    <a href="docs/dispensa.php?clt=<?=$row['0']?>&tab=bolsista<?=$id_pro?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image131','','../imagens/botoes_clts/dispensa2.gif',1)" target="_blank">
    <img src="../imagens/botoes_clts/dispensa1.gif" name="Image131" width="150" height="22" border="0" id="Image131" /></a>
    
    <a href="docs/demissao.php?clt=<?=$row['0']?>&tab=bolsista<?=$id_pro?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image135','','../imagens/botoes_clts/demissao2.gif',1)" target="_blank">
    <img src="../imagens/botoes_clts/demissao1.gif" name="Image135" width="150" height="22" border="0" id="Image135" /></a>
    
    <a href="demissionalclt.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$row['0']?>" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image132','','../imagens/botoes_clts/examedemi2.gif',1)" target="_blank"><img src="../imagens/botoes_clts/examedemi1.gif" name="Image132" width="150" height="22" border="0" id="Image132" /></a><a href="#" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image13','','../imagens/botoes_clts/declaracao_renda2.gif',1)"><br>
    </a>
    
    </div>
    
    <hr>
    <table width="454" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center" class="bordaescura1px" <?=$display?>>
      <tr>
        <td height="38" align="center" bgcolor="#CCCCCC">
          <div id="foto"></div>
          <a href='../upload/uploads.php?participante=<?=$row[0]?>&contratacao=<?=$row['tipo_contratacao']?>&regiao=<?=$id_reg?>&ant=<?=$id_ant?>&pro=<?=$id_pro?>'>
          <img src="../imagens/enviar_arquivo.gif" width="180" height="32" border="0"></a></td>
        </tr>
      <tr>
        <td height="19" align="center">
          <?php if($_GET['foto'] == "enviado") { ?>
          <span style="font-weight:bold;">Documento(s) enviado(s) com sucesso!</span>
          <?php } elseif($_GET['foto'] == "deletado") { ?>
          <span style="font-weight:bold;">Documento deletado com sucesso!</span>
          <?php } ?>
          <table border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="center" valign="middle">
                &nbsp;<?php

$diretorio_padrao = $_SERVER["DOCUMENT_ROOT"]."/";
$diretorio_padrao .= "intranet/documentos/";
$dirInternet = "../documentos/";
$DeldirInternet = "documentos/";

$regiao = sprintf("%03d", $row['id_regiao']);
$projeto = sprintf("%03d", $row['id_projeto']);

$Dir = $regiao."/".$projeto."/";					//RESOLVENDO O NOME DA PASTA ONDE VAI SER CRIADO A PASTA DO USUARIO
$novoDir = $row['tipo_contratacao']."_".$row[0];			//RESOLVENDO O NOME DA PASTA DO USUARIO
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
			}else{
				$tipoArquivo = explode("_",$file);
				$tipoArquivo = explode(".",$tipoArquivo[2]);
				
				$select = new upload();
				$TIPO = $select -> mostraTipo($tipoArquivo[0]);
				
				$DirFinal = $dirInternet."/".$file;
				$DelDirFinal = $DeldirInternet."/".$file;
				
				echo "<div class='bordafina' ";
				echo "onMouseOver=\"document.getElementById(this.id).className='bordafina2'\" ";
				echo "onMouseOut=\"document.getElementById(this.id).className='bordafina'\"";
				echo "id='$tipoArquivo[0]'><br>";
				echo "<a href='".$DirFinal."' rel='lightbox' title='$TIPO'>";
				echo "<img src='".$DirFinal."' width='25' height='25' border='0' alt='$TIPO'></a>";
				echo "<hr><a href='#' onClick=\"Confirm('$DelDirFinal')\" style='color:red'>";
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
		location.href="../upload/uploads.php?enviado=2&participante=<?=$row[0]?>&contratacao=<?=$row['tipo_contratacao']?>&regiao=<?=$id_reg?>&ant=<?=$id_ant?>&pro=<?=$id_pro?>&arquivo=" + arquivo;
		}else{
		// Output when Cancel is clicked
		// alert (\"You clicked cancel\");
	}

}
    
</script>


    <hr>
    <form action="../declarabancos.php" method='post' name='form1' target='_blanc'><br><center>
      <span class="campotexto4"><b>Escolha o Banco:</b>&nbsp;&nbsp;</span>&nbsp;
      <select name=banco id=banco>
        <?php

while($row_ban = mysql_fetch_array($result_ban)){
  print "<option value=$row_ban[id_banco]>$row_ban[nome]</option>";
  };


?>
        </select>
      <input type=submit value='Gerar Encaminhamento de Conta'>
      <input type='hidden' name='tipo' id='tipo' value="2">
      <input type='hidden' name='bolsista' id='bolsista' value=<?=$row['0']?>>
      <input type='hidden' name='regiao' id='regiao' value=<?=$id_reg?>>
      </center>
    </form></td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC"><br>
    
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
	  $tipo_contratacao = '2';
	  
	  $result_docs = mysql_query("SELECT * FROM rh_documentos WHERE tipo_contratacao = '$tipo_contratacao' ORDER BY documento");
	  
	  while($row_docs = mysql_fetch_array($result_docs)){  
	  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
	  
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
    echo "<td class='linha'>$data</td>";
    echo  "</tr>";
	
	
	  $cont ++;
	  $img = "";
	  $data = "";
	  }
	  
	  ?>
      <tr>
        <td colspan="3" align="center" class="linha">STATUS - <img src="../imagens/assinado.gif" width="15" height="17" align="absmiddle">= Emitido  <img src="../imagens/naoassinado.gif" width="15" height="17" align="absmiddle">= N&atilde;o Emitido</td>
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
    
    <br></td>
  </tr>
</table>

<br>
<?php 

if($pagina == "clt"){
	print "<a href='clt.php?regiao=$id_reg' class='link'><img src='../imagens/voltar.gif' border=0></a>";
}else{
	print "<a href='../bolsista.php?projeto=$id_pro&regiao=$id_reg' class='link'><img src='../imagens/voltar.gif' border=0></a>";
}
?>
</body>
</html>

<?php

}

?>