<?php
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='../login.php'>Logar</a>";
exit;
}

include "../conn.php";
$regiao = $_REQUEST['regiao'];

// FUNÇÃO NOME
function abreviacao($nome) {
	$extraido = explode(' ', $nome);
	$primeiro_nome = $extraido[0];
	$segundo_nome = $extraido[1];
	$terceiro_nome = $extraido[2];
	$quarto_nome = $extraido[3];
	$quinto_nome = $extraido[4];
				
	if ($quarto_nome == "DAS" or $quarto_nome == "DA" or $quarto_nome == "DE" or $quarto_nome == "DOS" or $quarto_nome == "DO" or $quarto_nome == "E") {
		$nome_abreviado = "$primeiro_nome $segundo_nome $terceiro_nome $quarto_nome $quinto_nome";
	} else {
		$nome_abreviado = "$primeiro_nome $segundo_nome $terceiro_nome $quarto_nome";
	}
	
	return $nome_abreviado;
}
?>
<html>
<head>
<title>:: Intranet :: Edi&ccedil;&atilde;o</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="../favicon.ico">
<link href="../net1.css" rel="stylesheet" type="text/css">
<link href="../autocomp/css.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="ajax2.js"></script>
<style>
body {
	background-color:#FAFAFA;
	text-align:center;
	margin:0px;
}
p {
	margin:0px;
}
a {
text-decoration:none;
	color:#444;
}
a.participante {
	color:#444;
	display:block;
	width:95%;
	padding:5px;
}

a.participante:hover {
	color:#000;
	background-color:#dee3ed;
}
#corpo {
	width:95%;
	background-color:#FFF;
	margin:0px auto;
	text-align:left;
	padding-top:20px;
	padding-bottom:10px;
}
#localizacao {
	background-color:#FFC;
	border:1px solid #FC0;
	padding:4px;
	clear:both;
}
</style>
</head>
<body>
<div id="corpo">
<div id="topo" style="width:95%; margin:0px auto; font-family:Arial;">
	<div style="float:left; width:25%;">
        <a href="../principalrh.php?regiao=<?=$regiao?>">
        	<img src="../imagens/voltar.gif" border="0">
        </a>
    </div>
	<div style="float:left; width:50%; text-align:center; font-size:24px; font-weight:bold; color:#000;">
    	EDI&Ccedil;&Atilde;O
    </div>
	<div style="float:right; width:25%; text-align:right; font-size:12px; color:#333;">
    	<br><b>Data:</b> <?=date('d/m/Y')?>&nbsp;
    </div>
	<div style="clear:both;"></div>
    
    <table cellpadding="3" cellspacing="3" style="font-size:11px; width:30%; background-color:#FAFAFA; margin-top:10px; float:left;">
      <tr>
        <td width="8%"><div style="background-color:#339933; text-align:center;">ok</div></td>
        <td width="92%">Regularizado com foto</td>
      </tr>
      <tr>
        <td><div style="background-color:#FFFFCC; text-align:center;">ok</div></td>
        <td>Regularizado</td>
      </tr>
      <tr>
        <td><div id="divquadrado" style="background-color:#FB797C; text-align:center;">!</div></td>
        <td>Com Observa&ccedil;&atilde;o / Sem C&oacute;digo / Sem Unidade</td>
      </tr>
    </table>
    
    <div id="ajax" name="ajax" style="width:250px;"></div>
    <table cellpadding="0" cellspacing="0" border="0" style="float:right;">
     <tr>
      <td width="99" height="31" align="right" class="show">Localizar:</td>
      <td width="362" class="show">
        <input type="text" id="pesquisa_usuario" name="pesquisa_usuario" size="40" class="campotexto" onKeyUp="searchSuggest();"/>
        <input type="hidden" id="reg" name="reg" value="<?=$regiao?>" />
      </td>
     </tr>
    </table>
    <br>
    <a href="cadastroclt.php?regiao=<?=$regiao?>&pagina=clt" style="float:right;">
		<img src="../imagens/castrobolsista.gif" border="0" >
	</a>
    
    <div style="clear:both;"></div>
</div>

<?php if($_GET['sucesso'] == 'edicao') { ?>
	<div style="background-color:#696; border:1px solid #033; color:#FFF; padding:4px; font-size:13px; width:95%; margin:0px auto;">Participante atualizado com sucesso!</div>
<?php } 

	$qr_projetos = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' AND status_reg = '1' ORDER BY nome ASC");
	while($projetos = mysql_fetch_array($qr_projetos)) {

	$qr_clt = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y')AS data_saida2 FROM rh_clt WHERE id_projeto = '$projetos[0]' AND (status < '60' OR status = '200') ORDER BY nome ASC");
	$total_clt = mysql_num_rows($qr_clt);

	if(!empty($total_clt)) { ?>
    
<table cellpadding="8" cellspacing="0" style="width:95%; border:0px; background-color:#f5f5f5; margin:20px auto;">
        <tr>
          <td colspan="7" class="show">
            &nbsp;<span style='color:#F90; font-size:32px;'>&#8250;</span> <?=$projetos['nome']?>
          </td>
        </tr>
        <tr class="novo_tr">
          <td width="3%">&nbsp;</td>
           <td width="3%" align="center">COD</td>
           <td width="35%">NOME</td>
           <td width="36%">CARGO</td>
           <td width="10%" align="center">ENTRADA</td>
           <td width="4%" align="center">PONTO</td>
           <td width="9%">DOCUMENTOS</td>
         </tr>

<?php while($row_clt = mysql_fetch_array($qr_clt)) {

if($row_clt['assinatura'] == "1") {
	$ass = "<a href='ver_tudo.php?id=18&projeto=$projeto&regiao=$regiao&ass=0&bolsista=$row_clt[0]&tipo=1&tab=rh_clt' title='Clique para REMOVER ASSINATURA do Contrato'>
			   	<img src='../imagens/assinado.gif' border='0' alt='Contrato'>
			</a>";
} else {
	$ass = "<a href='ver_tudo.php?id=18&projeto=$projeto&regiao=$regiao&ass=1&bolsista=$row_clt[0]&tipo=1&tab=rh_clt' title='Clique para alterar o Contrato para ASSINADO'>
			    <img src='../imagens/naoassinado.gif' border='0' alt='Contrato'>
			</a>";
}

if($row_clt['distrato'] == "1") {
	$ass2 = "<a href='ver_tudo.php?id=18&projeto=$projeto&regiao=$regiao&ass=0&bolsista=$row_clt[0]&tipo=2&tab=rh_clt' title='Clique para REMOVER ASSINATURA do Distrato'>
			     <img src='../imagens/assinado.gif' border='0' alt='Distrato'>
		     </a>";
} else {
	$ass2 = "<a href='ver_tudo.php?id=18&projeto=$projeto&regiao=$regiao&ass=1&bolsista=$row_clt[0]&tipo=2&tab=rh_clt' title='Clique para alterar o Distrato para ASSINADO'>
				 <img src='../imagens/naoassinado.gif' border='0' alt='Distrato'>
			 </a>";
}

if($row_clt['outros'] == "1") {
	$ass3 = "<a href='ver_tudo.php?id=18&projeto=$projeto&regiao=$regiao&ass=0&bolsista=$row_clt[0]&tipo=3&tab=rh_clt' title='Clique para REMOVER ASSINATURA de Outros Documentos'>
				<img src='../imagens/assinado.gif' border='0' alt='Outros Documentos'>
			 </a>";
} else {
	$ass3 = "<a href='ver_tudo.php?id=18&projeto=$projeto&regiao=$regiao&ass=1&bolsista=$row_clt[0]&tipo=3&tab=rh_clt' title='Clique para alterar Outros Documentos para ASSINADO'>
				<img src='../imagens/naoassinado.gif' border='0' alt='Outros Documentos'>
		     </a>";
}

$color = "background-color:#FFC;";
$textcor = "ok";

if($row_clt['campo3'] == "INSERIR" ) { 
	$color = "background-color:#FB797C;"; 
	$textcor = "!";
}

if($row_clt['locacao'] == "1 - A CONFIRMAR") { 
	$color = "background-color:#FB797C;"; 
	$textcor = "!";
}

if($row_clt['foto'] == "1") {
	$color="background-color:#393; color:#000;"; 
	$textcor="ok";
}

if(!empty($row_clt['observacao'])) {
	$color="background-color:#FB797C;"; 
	$obs="title=\"Observações: $row_clt[observacao]\"";
	$textcor="!";
}

$result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt[id_curso]'");
$row_curso = mysql_fetch_array($result_curso);
?>

<tr style="background-color:<?php if($alternateColor++%2!=0) { echo "#F0F0F0"; } else { echo "#FDFDFD"; } ?>; font-size:12px;">
   <td><div style="text-align:center;<?=$color?>"><?=$textcor?></div></td>
   <td align="center"><?=$row_clt['campo3']?></td>
   <td><a class="participante" href="ver_clt.php?reg=<?=$regiao?>&clt=<?=$row_clt['0']?>&ant=<?=$row_clt['id_antigo']?>&pro=<?=$projetos['0']?>&pagina=clt" <?=$obs?>><?=abreviacao($row_clt['nome'])?></a>
   <?php if($row_clt['status'] == "40") { 
   	     	 echo '<span style="color:#069; font-weight:bold;">(Em Férias)</span>';
         } elseif($row_clt['status'] == "200") {
	   		 echo '<span style="color:red; font-weight:bold;">(Aguardando Demissão)</span>';
   		 } ?>
   </td>
   <td>
   <?php echo str_replace('CAPACITANDO EM', '', $row_curso['nome']); ?>
   </td>
   <td align="center"><?=$row_clt['data_entrada2']?></td>
   <td align="center">
   <a href="../folha_ponto.php?id=2&unidade=<?=$row_unidades['0']?>&regiao=<?=$regiao?>&pro=<?=$projetos['0']?>&id_bol=<?=$row_clt['0']?>&tipo=clt">Gerar</a></td>
   <td align="center"><?=$ass.' '.$ass2.' '.$ass3?></td>
</tr>
   
   <?php }
	} ?>

</table>
<?php }

$qr_projetos2 = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' AND status_reg = '1' ORDER BY nome ASC");
while($projetos2 = mysql_fetch_array($qr_projetos2)) {

	$qr_clt_off = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada, date_format(data_saida, '%d/%m/%Y') AS data_saida FROM rh_clt WHERE id_projeto = '$projetos2[0]' AND (status >= '60' AND status != '200') ORDER BY nome ASC");
	$total_clt_off = mysql_num_rows($qr_clt_off);

	if(!empty($total_clt_off)) { ?>

 <table cellpadding="8" cellspacing="0" style="border:0px; background-color:#f5f5f5; margin:0px auto; margin-top:100px; width:95%;">
   <tr>
     <td colspan="5" class="show" align="center" style="background-color:#930; color:#FFF;">PARTICIPANTES DESATIVADOS</td>
   </tr>
   <tr class="novo_tr">
       <td width="5%" align="center">COD</td>
       <td width="35%">NOME</td>
       <td width="40%">UNIDADE</td>
       <td width="20%" align="center">ENTRADA - SAÍDA</td>
    </tr>
 <?php while($row_clt_off = mysql_fetch_array($qr_clt_off)) { ?>
    <tr style="background-color:<?php if($alternateColor++%2!=0) { echo "#F0F0F0"; } else { echo "#FDFDFD"; } ?>; font-size:12px;">
      <td align="center"><?=$row_clt_off['campo3']?></td>
      <td><a class="participante" href="ver_clt.php?reg=<?=$regiao?>&clt=<?=$row_clt_off['id_clt']?>&ant=<?=$row_clt_off['id_antigo']?>&pro=<?=$projetos['0']?>&pagina=clt"><?=abreviacao($row_clt_off['nome'])?></a></td>
      <td><?=$row_clt_off['locacao']?></td>
      <td align="center"><?=$row_clt_off['data_entrada'].' - '.$row_clt_off['data_saida']?></td>
	</tr>
 <?php }
	} ?>
</table>
<?php } ?>
</div>
</body>
</html>