<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "conn.php";

if (empty($_REQUEST['projeto'])){       //Esta tela será apresentada 1º e listará todos os projetos

$regiao = $_REQUEST['regiao'];

$result = mysql_query("SELECT * FROM projeto WHERE id_regiao = $regiao AND status_reg = '1'");
$row_cont = mysql_num_rows($result);

$cont = $row_cont;
?>
<html><head><title>Intranet</title></head>
<link href="net1.css" rel="stylesheet" type="text/css">
<body>
<style type="text/css">
body {
	background-color:#FFF;
}
</style>
<?php
//VERIFICANDO SE EXISTE PROJETO CADASTRADO PARA A REGIÃO SELECIONADA
if ($cont == "0"){                                     
?>
<div align="center">
<br>
<img src="imagens/visualizaprojeto.gif">
<br><br><span class="style2">Nenhum Projeto encontrado para sua região!</span></div>
<br><a href="javascript:window.close()" class="link"><img src="imagens/voltar.gif"></a>

<?php } else {
print "<html><head><title>:: Intranet :: Projetos</title>";
?>

<br>
<div style="width:80%; margin:0px auto; height:36px;">
<div style="float:left; width:25%;"><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a></div>
<div style="float:left; width:50%; text-align:center; font-family:Arial; font-size:24px; font-weight:bold; color:#000;">Projetos Cadastrados</div>
<div style="float:right; width:25%; text-align:right;"></div>
<div style="clear:both;"></div>
</div>
<table width='80%' border='0' cellpadding='0' cellspacing='4' align='center' style="margin:0px auto;">
<?php
$contP = 1;
while ($row = mysql_fetch_array($result)){
	
	if($contP % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
	
	if($contP < $cont){
		$bord = "style='border-bottom:#777 solid 1px;'";
	}else{
		$bord = "";
	}
	
	if($contP % 2){ $projeto="projeto"; }else{ $projeto="projeto"; }
	
	print "
	<tr>
	<td class='show'>
	<a title='Abrir: $row[nome]' href=ver.php?projeto=$row[0]&regiao=$regiao class=$projeto>
	&nbsp;<span style='color:#F90; font-size:32px;'>&#8250;</span> $row[id_projeto] - $row[nome]<br>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style='font-weight:normal; font-size:13px;'>$row[tema]</a>
	</td>
	</tr>";
	
	$contP ++;
}
?>

    </table>

<?php
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


print "<html><head><title>:: Intranet :: Projetos</title>";
?>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel='shortcut icon' href='favicon.ico'> <link href="net1.css" rel="stylesheet" type="text/css">

<!-- JAVASCRIPT PARA FORMATAR O CAMPO DATA DOS CAMPOS DATA -->

<script language="JavaScript">

function mascara_data(d){  
       var mydata = '';  
       data = d.value;  
       mydata = mydata + data;  
       if (mydata.length == 2){  
          mydata = mydata + '/';  
          d.value = mydata;  
       }  
          if (mydata.length == 5){  
          mydata = mydata + '/';  
          d.value = mydata;  
       }  
          if (mydata.length == 10){  
          verifica_data(d);  
         }  
      } 
           
         function verifica_data (d) {  

         dia = (d.value.substring(0,2));  
         mes = (d.value.substring(3,5));  
         ano = (d.value.substring(6,10));  
             

       situacao = "";  
       // verifica o dia valido para cada mes  
       if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31) {  
           situacao = "falsa";  
       }  

       // verifica se o mes e valido  
       if (mes < 01 || mes > 12 ) {  
              situacao = "falsa";  
       }  

      // verifica se e ano bissexto  
      if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) {  
            situacao = "falsa";  
      }  
   
     if (d.value == "") {  
          situacao = "falsa";  
    }  

    if (situacao == "falsa") {  
       alert("Data digitada é inválida, digite novamente!"); 
       d.value = "";  
       d.focus();  
    }  
  
}

</script>
<!-- FIM DO JAVASCRIPT PARA FORMATAÇÃO DOS CAMPOS DATA -->

</head>


<body bgcolor='#D7E6D5'>

<!-- INÍCIO DO FORMULÁRIO PARA ENVIAR AS DATAS REFERENTE A ENTREGA DOS PROJETOS -->

<form action='projeto_avancado.php' method='get'>

<input name='regiao' id='regiao' type='hidden' value='<?=$regiao?>' />
<input name='id_projeto' id='id_projeto' type='hidden' value='<?=$id_projeto?>' />

<br />
<table width="750" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td colspan="3"><img src='layout/topo.gif' width='750' height='38' /></td>
  </tr>
  <tr>
    <td width="21" height="49" background='layout/esquerdo.gif'>&nbsp;</td>
    <td width="703" align="center" bgcolor="#FFFFFF">
	
	<div style="font-family:Arial, Helvetica, sans-serif; font-size:14px"><b><?=$row['0']." - ".$row['nome']." - ".$row['tema']?></b></div>
    
    <br />
    <img src="imagens/verprojeto.gif" width="190" height="31" border="0" style="cursor:pointer"
    onClick="document.all.dados.style.display = (document.all.dados.style.display == 'none') ? '' : 'none' ;"/>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <img src="imagens/relatoriosprojeto.gif" width="190" height="31" border="0" style="cursor:pointer"
    onClick="document.all.relatorios.style.display = (document.all.relatorios.style.display == 'none') ? '' : 'none' ;"/><br /></td>
    <td width="26" background='layout/direito.gif'>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3"><img src='layout/baixo.gif' width='750' height='38' /></td>
  </tr>
</table>
<br />
<table width='750' border='0' align='center' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha' id="dados" style="display:none">
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
    <td align='left' valign='top' class='linha'>&nbsp;&nbsp; <font color=#FF0000><?=$row['nome']?></font></td>
  </tr>
  <tr>
    <td height='20' align='right' valign='top'>Tema:</td>
    <td align='left' valign='top'>&nbsp;&nbsp; <font color=#FF0000><?=$row['tema']?></font></td>
  </tr>
  <tr>
    <td height='20' align='right' valign='top'>&Aacute;rea:</td>
    <td align='left' valign='top'>&nbsp; &nbsp;<font color=#FF0000><?=$row['area']?></font></td>
  </tr>
  <tr>
    <td height='20' align='right' valign='top'>Local:</td>
    <td align='left' valign='top'><font color=#FF0000>&nbsp;&nbsp; <?=$row['local']?></font></td>
  </tr>
  <tr>
    <td height='20' align='right' valign='top'>Região:</td>
    <td align='left' valign='top'><font color=#FF0000>&nbsp;&nbsp; <?=$row['regiao']?></font></td>
  </tr>
  <tr>
    <td height='20' align='right' valign='top'>Inicio:</td>
    <td align='left' valign='top'>&nbsp;&nbsp; <font color=#FF0000><?=$row['data_ini2']?></font></td>
  </tr>
  <tr>
    <td height='19' align='right' valign='top'>Previs&atilde;o de T&eacute;rmino:</td>
    <td align='left' valign='top'>&nbsp;&nbsp; <font color=#FF0000><?=$row['data_ini3']?></font></td>
  </tr>
  <tr>
    <td height='19' align='right' valign='top'>Descri&ccedil;&atilde;o:</td>
    <td align='left' valign='top'>&nbsp;&nbsp; <font color=#FF0000><?=$row['descricao']?></font> <BR><BR>
	<a href='folha_ponto.php?id=1&regiao=<?=$regiao?>&pro=<?=$id_projeto?>' class='link'><img src='imagens/gerarapontamento.gif' border=0></a>
	<BR><BR>
	<?php
	
	if($id_projeto == 11){
	print "<a href='declarabancos2.php' class='link' target='_blanck'>IMPRIMIR DECLARAÇÃO DE BANCO EM LOTE </a>";
	}
	
	?>
	</td>
  </tr>
  <tr>
    <td width='155' height='19' align='right' valign='top'><p>&nbsp;</p>        </td>
    <td width='548' align='center' valign='middle'>&nbsp;</td>
  </tr>
  <tr valign='top'>
    <td height='38' colspan='4' bgcolor="#E2E2E2"><img src='layout/baixo.gif' width='750' height='38'>
        <div align='center' class='style6'></div></td>
  </tr>
</table>
<br /><table width="750" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td colspan="3"><img src='layout/topo.gif' width='750' height='38' /></td>
  </tr>
  <tr>
    <td width="21" height="49" background='layout/esquerdo.gif'>&nbsp;</td>
    <td width="703" align="center" bgcolor="#FFFFFF">
    <?php
	
	if ($row_user['grupo_usuario'] <= 2){
	
	print "
	<a href='cadastro.php?id=4&pro=$row[id_projeto]&regiao=$regiao' class='link'><img src='imagens/cadastroautonomo.gif' border=0 ></a>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<a href='rh/cadastroclt.php?regiao=$regiao' class='link'><img src='imagens/castrobolclt.gif' border=0 ></a>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<a href='cooperativas/cadcooperado.php?pro=$row[id_projeto]&regiao=$regiao' class='link'><img src='imagens/bt_cadcooperado.gif' border=0 ></a>
	<br><br>
	<a href='bolsista.php?projeto=$row[id_projeto]&regiao=$regiao' class='link'><img src='imagens/verbolsista.gif' border=0></a>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<a href='ver_avancado.php?projeto=$row[id_projeto]&regiao=$regiao' class='link'><img src='imagens/localizarcadastro.gif' border=0></a>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<a href='ver.php?id=$id_projeto&regiao=$row[id_regiao]' class='link'><img src='imagens/voltar.gif' border=0></a> &nbsp;&nbsp;&nbsp;&nbsp;";
	
	}elseif($row_user['grupo_usuario'] <= 3){
	
	print "
	<a href='bolsista.php?projeto=$row[id_projeto]&regiao=$regiao' class='link'><img src='imagens/verbolsista.gif' border=0></a>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<a href='ver_avancado.php?projeto=$row[id_projeto]&regiao=$regiao' class='link'><img src='imagens/localizarcadastro.gif' border=0></a>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<a href='ver.php?id=$id_projeto&regiao=$row[id_regiao]' class='link'><img src='imagens/voltar.gif' border=0></a>";
	
	
	}elseif ($row_user['grupo_usuario'] == 5){
	
	print "
	<a href='cadastro.php?id=4&pro=$row[id_projeto]&regiao=$regiao' class='link'><img src='imagens/cadastroautonomo.gif' border=0 ></a>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<a href='rh/cadastroclt.php?regiao=$regiao' class='link'><img src='imagens/castrobolclt.gif' border=0 ></a>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<a href='cooperativas/cadcooperado.php?pro=$row[id_projeto]&regiao=$regiao' class='link'><img src='imagens/bt_cadcooperado.gif' border=0 ></a>
	<br><br>
	<a href='bolsista.php?projeto=$row[id_projeto]&regiao=$regiao' class='link'><img src='imagens/verbolsista.gif' border=0></a>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<a href='ver_avancado.php?projeto=$row[id_projeto]&regiao=$regiao' class='link'><img src='imagens/localizarcadastro.gif' border=0></a>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<a href='ver.php?id=$id_projeto&regiao=$row[id_regiao]' class='link'><img src='imagens/voltar.gif' border=0></a> &nbsp;&nbsp;&nbsp;&nbsp;";
	
	}else{
	
	print "<a href='ver.php?id=$id_projeto&regiao=$row[id_regiao]' class='link'><img src='imagens/voltar.gif' border=0></a>";
	
	}
	?>
    </td>
    <td width="26" background='layout/direito.gif'>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3"><img src='layout/baixo.gif' width='750' height='38' /></td>
  </tr>
</table>

<br />
<table width="740" border="0" cellspacing="0" cellpadding="0" align="center" id="relatorios" style="display:none">
  <tr>
    <td colspan="3"><img src='layout/topo.gif' width='750' height='38' /></td>
  </tr>
  <tr>
    <td width="21" height="31" background='layout/esquerdo.gif'>&nbsp;</td>
    <td width="703" bgcolor="#FFFFFF">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="35" align="center" class="title">RELAT&Oacute;RIOS </td>
      </tr>
      <tr>
        <td><table width='100%' border='0' align='center' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha'>
          <tr>
            <td width="362" height='34' align='right'> Ficha Financeira: </td>
            <td width="388">&nbsp;&nbsp; <a href='relatorios/fichafinanceira.php?reg=<?=$regiao?>&pro=<?=$id_projeto?>&tela=1' target='_blank'> <img src='imagens/ver_relatorio.gif' border="0" /></a></td>
          </tr>
          <tr>
            <td width="362" height='34' align='right'> Fichas de Cadastro: </td>
            <td width="388">&nbsp;&nbsp; <a href='relatorios/fichadecadastro.php?reg=<?=$regiao?>&pro=<?=$id_projeto?>&tela=1' target='_blank'> <img src='imagens/ver_relatorio.gif' border="0" /></a></td>
          </tr>
          <tr>
            <td height='34' align='right'> Relat&oacute;rio De Pagamentos por Banco: </td>
            <td>&nbsp;&nbsp; <a href='relatorios/relatorio10.php?reg=<?=$regiao?>&pro=<?=$id_projeto?>' target='_blank'> <img src='imagens/ver_relatorio.gif' border="0" /></a></td>
          </tr>
          <tr>
            <td width="362" height='34' align='right'> Encaminhamento de Conta: </td>
            <td width="388">&nbsp;&nbsp; <a href='declarabancos2.php?reg=<?=$regiao?>&pro=<?=$id_projeto?>&tela=1' target='_blank'> <img src='imagens/ver_relatorio.gif' border="0" /></a></td>
          </tr>
          <tr>
            <td height='34' align='right'> Relat&oacute;rios de Gest&atilde;o: </td>
            <td>&nbsp;&nbsp; <a href='relatorios/relatorios_gestao.php?reg=<?=$regiao?>&pro=<?=$id_projeto?>' target='_blank'> <img src='imagens/ver_relatorio.gif' border="0" /></a></td>
          </tr>
          <tr>
            <td height='34' align='right'> Relat&oacute;rio de Participantes com Dependentes: </td>
            <td>&nbsp;&nbsp; <a href='relatorios/relatorio9.php?reg=<?=$regiao?>&pro=<?=$id_projeto?>' target='_blank'> <img src='imagens/ver_relatorio.gif' border="0" /></a></td>
          </tr>
          <tr>
            <td height='34' align='right'> Relat&oacute;rio de Assegurados: </td>
            <td>&nbsp;&nbsp; <a href='relatorios/relatorio6.php?reg=<?=$regiao?>&pro=<?=$id_projeto?>' target='_blank'> <img src='imagens/ver_relatorio.gif' border="0" /></a></td>
          </tr>
          <tr>
            <td height='34' align='right'> Relat&oacute;rio de Participantes com Assist&ecirc;ncia M&eacute;dica: </td>
            <td>&nbsp;&nbsp; <a href='relatorios/relatorio8.php?reg=<?=$regiao?>&pro=<?=$id_projeto?>' target='_blank'> <img src='imagens/ver_relatorio.gif' border="0" /></a></td>
          </tr>
          <tr>
            <td height='34' align='right'> Participantes do Projeto em Ordem Alfab&eacute;tica: </td>
            <td>&nbsp;&nbsp; <a href='relatorios/relatorio7.php?reg=<?=$regiao?>&pro=<?=$id_projeto?>' target='_blank'> <img src='imagens/ver_relatorio.gif' border="0" /></a></td>
          </tr>
          <tr>
            <td height='34' align='right'> Participantes DESLIGADOS do Projeto em Ordem Alfab&eacute;tica: </td>
            <td>&nbsp;&nbsp; <a href='relatorios/relatorio13.php?reg=<?=$regiao?>&pro=<?=$id_projeto?>' target='_blank'> <img src='imagens/ver_relatorio.gif' border="0" /></a></td>
          </tr>
          <tr>
            <td height='34' align='right'> Relat&oacute;rio de Usu&aacute;rios de Vale Tranporte: </td>
            <td>&nbsp;&nbsp; <a href='relatorios/relatorio11.php?reg=<?=$regiao?>&pro=<?=$id_projeto?>' target='_blank'> <img src='imagens/ver_relatorio.gif' border="0" /></a></td>
          </tr>
          <tr>
            <td height='34' align='right'> Relat&oacute;rio de Documentos: </td>
            <td>&nbsp;&nbsp; <a href='relatorios/relatorio12.php?reg=<?=$regiao?>&pro=<?=$id_projeto?>' target='_blank'> <img src='imagens/ver_relatorio.gif' border="0" /></a></td>
          </tr>
          <tr>
            <td height='34' align='right'> Relat&oacute;rios de Participantes por datas de Entrada e Sa&iacute;da: </td>
            <td>&nbsp;&nbsp; <a href='relatorios/relatorio14.php?reg=<?=$regiao?>&pro=<?=$id_projeto?>' target='_blank'> <img src='imagens/ver_relatorio.gif' border="0" /></a></td>
          </tr>
          <tr>
            <td height='34' align='right'> Relat&oacute;rios de Acesso para a TV Sorrindo: </td>
            <td>&nbsp;&nbsp; <a href='relatorios/relatorio16.php?reg=<?=$regiao?>&pro=<?=$id_projeto?>' target='_blank'> <img src='imagens/ver_relatorio.gif' border="0" /></a></td>
          </tr>
          <tr>
            <td height='34' align='right'> Relat&oacute;rios por idade: </td>
            <td>&nbsp;&nbsp; <a href='relatorios/relatorio20.php?reg=<?=$regiao?>&pro=<?=$id_projeto?>' target='_blank'> <img src='imagens/ver_relatorio.gif' border="0" /></a></td>
            </tr>
          <tr>
            <td height='34' align='right'> Relat&oacute;rio de Participantes com  PIS: </td>
            <td>&nbsp;&nbsp; <a href='/intranet/relatorios/relatorio30.php?reg=<?=$regiao?>&pro=<?=$id_projeto?>' target='_blank'> <img src='imagens/ver_relatorio.gif' border="0" /></a></td>
          </tr>
          <tr>
            <td height='34' align='right'> Relat&oacute;rio de Quotas e Parcelas: </td>
            <td>&nbsp;&nbsp; <a href='/intranet/relatorios/relatorio17.php?reg=<?=$regiao?>&pro=<?=$id_projeto?>' target='_blank'> <img src='imagens/ver_relatorio.gif' border="0" /></a></td>
          </tr>
          <tr>
            <td colspan='5'>&nbsp;</td>
          </tr>
          <tr>
        <td align="center" colspan="5">
        <strong>CADASTRO AVAN&Ccedil;ADO DO PROJETO</strong>
        <br /><br />
        Gestores:<br />
		<textarea name='gestores' cols='50' rows='5' id='gestores'></textarea></td>
      </tr>
        </table></td>
      </tr>
    </table></td>
    <td width="26" background='layout/direito.gif'>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3"><img src='layout/baixo.gif' width='750' height='38' /></td>
  </tr>
</table>
<br />
<br />
<br />
</form>
<?php
//FIM DO FORMULÁRIO PARA ENVIAR AS DATAS REFERENTE A ENTREGA DOS PROJETOS

}


?>