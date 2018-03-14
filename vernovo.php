<?php
if(empty($_COOKIE['logado'])){

print "Efetue o Login<br><a href='login.php'>Logar</a> ";

}else{

include "conn.php";

if (empty($_REQUEST['projeto'])){       //Esta tela será apresentada 1º e listará todos os projetos
$regiao = $_REQUEST['regiao'];

$result = mysql_query("Select * from projeto where id_regiao = $regiao and status_reg = '1'");

$result_cont = mysql_query("Select COUNT(*) from projeto where id_regiao = $regiao", $conn);
$row_cont = mysql_fetch_array($result_cont);

$cont = $row_cont['0'];

if ($cont == "0"){                                     //VERIFICANDO SE EXISTE PROJETO CADASTRADO PARA A REGIÃO SELECIONADA

print "<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\"><body bgcolor='#D7E6D5'>";

print "<center><br><img src='imagens/visualizaprojeto.gif'><br><br><span class='style1'>Nenhum Projeto encontrado para sua região!</span></center>";
print "<br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>";

} else {

print "<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\"><body bgcolor='#D7E6D5'>";
print "<br><img src='imagens/visualizaprojeto.gif'><br><br>";

print "
<table width='70%' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' align='center'>
  <tr>
    <td width='3%' valign='top'><img src='imagens/arre_cima1.gif' width='21' height='18' /></td>
    <td width='94%'>&nbsp;</td>
    <td width='3%' align='right' valign='top'><img src='imagens/arre_cima2.gif' alt='' width='18' height='21' /></td>
  </tr>
  <tr>
    <td height='100'>&nbsp;</td>
    <td><table width='100%' border='0' cellspacing='0' cellpadding='0'>	";
	  
while ($row = mysql_fetch_array($result)){

print "
<tr>
<td width='17%' class='linha'>Projeto:</td>
<td width='83%'>&nbsp;<a href=ver.php?projeto=$row[0]&regiao=$regiao class=link>$row[nome]</a></td>
</tr>

<tr>
<td class='linha'>Tema:</td>
<td>&nbsp;<span class='style3'>$row[tema]</span></td>
</tr>

<tr>
<td colspan=2><hr></td>
</tr>


";
	  
	  }
	  
print "	  
    </table></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td valign='bottom'><img src='imagens/arre_baixo1.gif' alt='' width='18' height='21' /></td>
    <td>&nbsp;</td>
    <td valign='bottom' align='right'><img src='imagens/arre_baixo2.gif' alt='' width='21' height='18' /></td>
  </tr>
</table><br><br>";


print "<br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>";

//$result_soma = mysql_query("SELECT SUM(salario) AS salario FROM funcionario", $conn);

}

} else {//Esta tela será apresentada na 2º vez e mostrará os detalhes do projeto selecionado anteriormente.

$id_projeto = $_REQUEST['projeto'];
$regiao = $_REQUEST['regiao'];
$id_user = $_COOKIE['logado'];

$sql = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($sql);

$result = mysql_query("Select *, date_format(inicio, '%d/%m/%Y') as data_ini2, date_format(termino, '%d/%m/%Y') as data_ini3 from projeto where id_projeto = '$id_projeto' ");
$row = mysql_fetch_array($result);

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\"></head>";

print "
<body bgcolor='#D7E6D5'>
<table width='750' border='0' align='center' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha'>
  <tr>
    <td colspan='4'><img src='layout/topo.gif' width='750' height='38'></td>
  </tr>
  <tr>
    <td width='21' rowspan='12' background='layout/esquerdo.gif'>&nbsp;</td>
    <td colspan='2'><div align='center'></div></td>
    <td width='26' rowspan='12' background='layout/direito.gif'>&nbsp;</td>
  </tr>
  <tr>
    <td height='20' colspan='2' align='right' valign='top'><div align='center'>DADOS DO PROJETO</div></td>
  </tr>
  <tr>
    <td height='20' align='right' valign='top'>&nbsp;</td>
    <td align='center' valign='middle'>&nbsp;</td>
  </tr>
  <tr>
    <td height='20' align='right' valign='top'>Nome do Projeto</td>
    <td align='left' valign='top' class='linha'>&nbsp;&nbsp; <font color=#FF0000>$row[nome]</font></td>
  </tr>
  <tr>
    <td height='20' align='right' valign='top'>Tema:</td>
    <td align='left' valign='top'>&nbsp;&nbsp; <font color=#FF0000>$row[tema]</font></td>
  </tr>
  <tr>
    <td height='20' align='right' valign='top'>&Aacute;rea:</td>
    <td align='left' valign='top'>&nbsp; &nbsp;<font color=#FF0000>$row[area]</font></td>
  </tr>
  <tr>
    <td height='20' align='right' valign='top'>Local:</td>
    <td align='left' valign='top'><font color=#FF0000>&nbsp;&nbsp; $row[local]</font></td>
  </tr>
  <tr>
    <td height='20' align='right' valign='top'>Região:</td>
    <td align='left' valign='top'><font color=#FF0000>&nbsp;&nbsp; $row[regiao]</font></td>
  </tr>
  <tr>
    <td height='20' align='right' valign='top'>Inicio:</td>
    <td align='left' valign='top'>&nbsp;&nbsp; <font color=#FF0000>$row[data_ini2]</font></td>
  </tr>
  <tr>
    <td height='19' align='right' valign='top'>Previs&atilde;o de T&eacute;rmino:</td>
    <td align='left' valign='top'>&nbsp;&nbsp; <font color=#FF0000>$row[data_ini3]</font></td>
  </tr>
  <tr>
    <td height='19' align='right' valign='top'>Descri&ccedil;&atilde;o:</td>
    <td align='left' valign='top'>&nbsp;&nbsp; <font color=#FF0000>$row[descricao]</font> <BR><BR>
	<a href='folha_ponto.php?id=1&regiao=$regiao&pro=$id_projeto' class='link'><img src='imagens/gerarapontamento.gif' border=0></a>
	<BR>
	</td>
  </tr>
  <tr>
    <td width='155' height='19' align='right' valign='top'><p>&nbsp;</p>        </td>
    <td width='548' align='center' valign='middle'>&nbsp;</td>
  </tr>
  <tr valign='top'>
    <td height='37' colspan='4' bgcolor='#5C7E59'><img src='layout/baixo.gif' width='750' height='38'>
        <div align='center' class='style6'></div></td>
  </tr>
</table>
<p>&nbsp;</p>
<table width='750' border='0' align='center' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha'>
  <tr>
    <td colspan='4'><img src='layout/topo.gif' width='750' height='38'></td>
  </tr>
  <tr>
    <td width='21' rowspan='20' background='layout/esquerdo.gif'>&nbsp;</td>
    <td colspan='2'><div align='center'></div></td>
    <td width='26' rowspan='20' background='layout/direito.gif'>&nbsp;</td>
  </tr>
  <tr>
    <td height='19' colspan='2' align='center' valign='middle'><strong>CADASTRO AVANÇADO DO PROJETO</strong> <br><br>
	Gestores:<br>
 <textarea name='1' cols='50' rows='5' id='1'></textarea>
	</td>
  </tr>
  <tr>
    <td height='32' align='right' valign='middle'>Projeto Entregue:</td>
    <td align='left' valign='middle'>&nbsp;Sim:
      <input type='checkbox' name='simprojeto' id='simprojeto' />
Não:
<input type='checkbox' name='simprojeto2' id='simprojeto2' />
-
<label> Data de entrega:
<input name='dataprojeto' type='text' id='dataprojeto' size='10' />
</label></td>
  </tr>
  <tr>
    <td height='32' align='right' valign='middle'>Relatórios Trimestral:</td>
    <td align='left' valign='middle'>&nbsp;Sim:
      <input type='checkbox' name='simprojeto7' id='simprojeto11' />
Não:
<input type='checkbox' name='simprojeto7' id='simprojeto12' />
-
<label> Data de entrega:
<input name='dataprojeto6' type='text' id='dataprojeto6' size='10' />
</label></td>
  </tr>
  <tr>
    <td height='28' align='right' valign='middle'>Relatórios SemestralRelatórios de Capacitação:</td>
    <td align='left' valign='middle'>&nbsp;Sim:
      <input type='checkbox' name='simprojeto8' id='simprojeto13' />
Não:
<input type='checkbox' name='simprojeto8' id='simprojeto14' />
-
<label> Data de entrega:
<input name='dataprojeto7' type='text' id='dataprojeto7' size='10' />
</label></td>
  </tr>
  <tr>
    <td height='31' align='right' valign='middle'>Relatórios de Desempenho:
</td>
    <td align='left' valign='middle'>&nbsp;Sim:
      <input type='checkbox' name='simprojeto9' id='simprojeto15' />
Não:
<input type='checkbox' name='simprojeto9' id='simprojeto16' />
-
<label> Data de entrega:
<input name='dataprojeto8' type='text' id='dataprojeto8' size='10' /> 


</label></td>

  </tr>
    <tr>
    <td height='34' align='center' colspan='2'>
	<hr>
	RELATÓRIOS
	</td>
  </tr>
  <tr>
  <td height='34' align='right'>
	Fichas de Cadastro:
	</td><td>
	&nbsp;&nbsp;
	<a href='fichadecadastro.php?reg=$regiao&pro=$id_projeto&tela=1' target='_blank'>
	<img src='imagens/ver_relatorio.gif' border=0></a>
	</td>
	</tr>
	 <tr>
  <td height='34' align='right'>
	Relatório De Pagamentos por Banco:
	</td><td>
	&nbsp;&nbsp;
	<a href='relatorio10.php?reg=$regiao&pro=$id_projeto' target='_blank'>
	<img src='imagens/ver_relatorio.gif' border=0></a>
	</td>
	</tr>
	<tr>
    <td height='34' align='right'>
	Relatórios de Gestão:
	</td>
	
	<td>
	&nbsp;&nbsp;
	<a href='ver_tudo.php?id=15&id_reg=$regiao&pro=$id_projeto' target='_blank'>
	<img src='imagens/ver_relatorio.gif' border=0></a>
	</td>
  </tr>
  <tr>
    <td height='34' align='right'>
	Relatório de Participantes com Dependentes:
	</td><td>
	&nbsp;&nbsp;
	<a href='relatorio9.php?reg=$regiao&pro=$id_projeto' target='_blank'>
	<img src='imagens/ver_relatorio.gif' border=0 ></a>
	
	</td>
  </tr>
  <tr>
    <td height='34' align='right'>
	Relatório de Assegurados:
	</td><td>
	&nbsp;&nbsp;
	<a href='relatorio6.php?reg=$regiao&pro=$id_projeto' target='_blank'>
	<img src='imagens/ver_relatorio.gif' border=0></a>
	</td>
  </tr>
  <tr>
    <td height='34' align='right'>
	Relatório de Participantes com Assistência Médica:
	</td><td>
	&nbsp;&nbsp;
	<a href='relatorio8.php?reg=$regiao&pro=$id_projeto' target='_blank'>
	<img src='imagens/ver_relatorio.gif' border=0></a>
	</td>
  </tr>
  <tr>
    <td height='34' align='right'>
	Participantes do Projeto em Ordem Alfabética:
	</td><td>
	&nbsp;&nbsp;
	<a href='relatorio7.php?reg=$regiao&pro=$id_projeto' target='_blank'>
	<img src='imagens/ver_relatorio.gif' border=0></a>
	</td>
  </tr>
  <tr>
    <td height='34' align='right'>
	Participantes DESLIGADOS do Projeto em Ordem Alfabética:
	</td><td>
	&nbsp;&nbsp;
	<a href='relatorio13.php?reg=$regiao&pro=$id_projeto' target='_blank'>
	<img src='imagens/ver_relatorio.gif' border=0></a>
	</td>
  </tr>
  <tr>
    <td height='34' align='right'>
	Relatório de Usuários de Vale Tranporte:
	</td><td>
	&nbsp;&nbsp;
	<a href='relatorio11.php?reg=$regiao&pro=$id_projeto' target='_blank'>
	<img src='imagens/ver_relatorio.gif' border=0></a>
	</td>
  </tr>
  <tr>
    <td height='34' align='right'>
	Relatório de Contratos NÃO ASSINADOS:
	</td><td>
	&nbsp;&nbsp;
	<a href='relatorio12.php?reg=$regiao&pro=$id_projeto' target='_blank'>
	<img src='imagens/ver_relatorio.gif' border=0></a>
	</td>
  </tr>
  <tr>
    <td height='34' align='right'>
	Relatório de Distratos NÃO ASSINADOS:
	</td><td>
	&nbsp;&nbsp;
	<a href='relatorio15.php?reg=$regiao&pro=$id_projeto' target='_blank'>
	<img src='imagens/ver_relatorio.gif' border=0></a>
	</td>
  </tr>
  <tr>
    <td height='34' align='right'>
	Relatórios de Participantes por datas de Entrada e Saída:
	</td><td>
	&nbsp;&nbsp;
	<a href='relatorio14.php?reg=$regiao&pro=$id_projeto' target='_blank'>
	<img src='imagens/ver_relatorio.gif' border=0></a>
	</td>
  </tr>
  
  <tr>
    <td width='304' height='21' align='right' valign='top'><p>&nbsp;</p>    </td>
    <td width='399' align='left' valign='middle'>&nbsp;</td>
  </tr>
  <tr valign='top'>
    <td height='37' colspan='5' bgcolor='#5C7E59'><img src='layout/baixo.gif' width='750' height='38'>
        <div align='center' class='style6'></div></td>
  </tr>
</table>";

if ($row_user['grupo_usuario'] <= 2){

print "
<br><a href='rh/cadastroclt.php?regiao=$regiao' class='link'><img src='imagens/castrobolclt.gif' border=0 ></a>
<br><a href='cadastro.php?id=4&pro=$row[id_projeto]&regiao=$regiao' class='link'><img src='imagens/castrobolsista.gif' border=0 ></a>
<br><a href='bolsista.php?projeto=$row[id_projeto]&regiao=$regiao' class='link'><img src='imagens/verbolsista.gif' border=0></a>
<br><a href='ver.php?id=$id_projeto&regiao=$row[id_regiao]' class='link'><img src='imagens/voltar.gif' border=0></a>";

}elseif($row_user['grupo_usuario'] <= 3){

print "
<br><a href='bolsista.php?projeto=$row[id_projeto]&regiao=$regiao' class='link'><img src='imagens/verbolsista.gif' border=0></a>
<br><a href='ver.php?id=$id_projeto&regiao=$row[id_regiao]' class='link'><img src='imagens/voltar.gif' border=0></a>";

}else{

print "<br><a href='ver.php?id=$id_projeto&regiao=$row[id_regiao]' class='link'><img src='imagens/voltar.gif' border=0></a>";

}

}

}

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>GERENCIAMENTO DE NOTIFICA&Ccedil;&Otilde;ES</title>
<link href="rh/net.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
body {
 background-color: #CCC;
}
-->
</style>
<script src="SpryAssets/SpryAccordion.js" type="text/javascript"></script>
<script type="text/javascript">
<!--
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
//-->
</script>
<link href="SpryAssets/SpryAccordion.css" rel="stylesheet" type="text/css">
<link href="net.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="80%" border="0" align="center"  bgcolor="#FFFFFF" cellpadding="0" cellspacing="0">
  <tr>
    <td align="left" valign="top"><img src="rh/notifica/imagens/arre_cima1.gif" width="21" height="18"></td>
    <td align="right" valign="top"><img src="rh/notifica/imagens/arre_cima2.gif" width="21" height="18"></td>
  </tr>
  <tr>
    <td width="100%" colspan="2" align="center" valign="middle"><p class="linha"><?php
include "empresa.php";
$img= new empresa();
$img -> imagemCNPJ();
?></p></td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle"><br>
      <div id="Accordion1" class="Accordion" tabindex="0">
<!-- Observações individuais -->
<?
// OBSERCAÇÕES INDIVIDUAIS
$resultOBS = mysql_query("SELECT * FROM rh_clt  WHERE id_regiao = '$id_regiao' and observacao != '' and status < '60' ORDER BY nome");
$quantidade = mysql_affected_rows();
?>
        <!-- Final de observações individuais -->

        <div class="AccordionPanel">
          <div class="AccordionPanelTab">DADOS DO PROJETO</div>
          <div class="AccordionPanelContent"></div>
        </div>
        <div class="AccordionPanel">
          <div class="AccordionPanelTab">CADASTRO AVAN&Ccedil;ADO DO PROJETO</div>
          <div class="AccordionPanelContent"></div>
        </div>
        <div class="AccordionPanel">
          <div class="AccordionPanelTab">RELAT&Oacute;RIOS</div>
          <div class="AccordionPanelContent"></div>
        </div>
      </div>
      <br>      
      &nbsp;</td>
  </tr>
  <tr>
    <td align="left" valign="top"><img src="rh/notifica/imagens/arre_baixo1.gif" width="21" height="18"></td>
    <td align="right" valign="top"><img src="rh/notifica/imagens/arre_baixo2.gif" width="21" height="18"></td>
  </tr>
</table>
<p class="linha">&nbsp;</p>
<script type="text/javascript">
<!--
var Accordion1 = new Spry.Widget.Accordion("Accordion1");
//-->
</script>
</body>
</html>
