<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "conn.php";

$id_user = $_REQUEST['id_user'];
$id = $_REQUEST['id'];

$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

$id_regiao = $row_user['id_regiao'];

$result_funcionarios = mysql_query("SELECT id_funcionario, nome1 FROM funcionario WHERE status_reg = '1' ORDER BY nome1 ASC");
//$result_funcionarios = mysql_query("SELECT id_funcionario,nome1 FROM funcionario where id_regiao = '$id_regiao' as normal, SELECT DISTINCT (id_funcionario,nome1) FROM funcionario where tipo_usuario = '1' as teste", $conn);

$dia = date('d');
$mes = date('m');
$ano = date('Y');

$DatA = date('d/m/Y');

?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="net1.css" rel="stylesheet" type="text/css">
<script src="jquery/jquery-1.4.2.min.js" type="text/javascript"></script>

<script src="jquery/nicEdit.js" type="text/javascript"></script>

<script type="text/javascript" src="jquery.uploadify-v2.1.4/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript" src="jquery.uploadify-v2.1.4/swfobject.js"></script>

<script type="text/JavaScript">

//<![CDATA[
        bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
  //]]>
  
$(function() {
	
	$('.op_anexo').change(function(){
		
		
		if($(this).val() == 'sim') {
			$('#envio_anexo').show();
			
		} else {
		
		$('#envio_anexo').hide();
			
		}
		
		
	});
	
	/*$('#up_anexo').uploadify({
			uploader'  : 'jquery.uploadify-v2.1.4/uploadify.swf',
			script'    : '',
			cancelImg' : 'jquery.uploadify-v2.1.4/cancel.png',
			folder'    : 'anexo_tarefa',
			auto'      : true,
            multi'     : true,
			 buttonText'  : 'Enviar',
            fileExt'   : '*.jpg,*.gif,*.pdf,*.doc,*.docx',
            queueID'   : 'exibir_anexo',
			scriptData': {   },
            onComplete'  : function(){				
						
				
				},
			onError'     : function (event,ID,fileObj,errorObj) {
		      	alert(errorObj.type + ' Error: ' + errorObj.info);
		    }		
	});*/
	
	
	
});



<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
if (restore) selObj.selectedIndex=0;
}
function validaForm() {
d = document.form1;
if (d.tarefa.value == ""){
alert("O campo Tarefa deve ser preenchido!");
d.tarefa.focus();
return false;
}
if (d.ent_dia.value == "" && d.ent_mes.value == "" && d.ent_ano.value == ""){
alert("O campo Data deve ser preenchido!");
d.ent_dia.focus();
return false;
}
if (d.descricao.value == ""){
alert("O campo Descrição deve ser preenchido!");
d.descricao.focus();
return false;
}
if (!d.radio[0].checked && !d.radio[1].checked) {
alert ("Escolha uma Forma de Envio - Individual ou Para o grupo inteiro !");
document.all.atencaoo.style.display = (document.all.atencaoo.style.display == 'none') ? '' : '';
return false;
}
return true;   }
//-->
</script>

<style type="text/css">
<!--

body{
	
background-color:  #F4F4F4
	
}

input#anexo {
	width:300px;
	background:  #F4F4F4;
	font-weight:400;
	}

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
-->
#topo {

background-image:url('imagens/enviar_tarefa.gif');	
width:240px;
height:80px;
display:block;
float:left;
}



</style>


</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<form action='cadastro2.php' method='post' name='form1' class='style3' id='form1' onSubmit="return validaForm()" enctype="multipart/form-data"> 
<table width='80%' align='center' cellspacing='2' class='bordaescura1px' bgcolor="#FFFFFF">
 <tr>
           	<td> 
            
            	<div id="topo">

			</div>
		<div style="clear:left;"></div>

            </td>
            
            <td align="right"  colspan='3' >
			
		
			
			<?php include('reportar_erro.php');?></td>
 </tr>

<tr>

<td height='25' colspan='4' align='center' valign='middle'> Tipo de mensagem: 


<?php
switch ($id) {

case 1:
print " <select name='tipo' onchange=\"MM_jumpMenu('parent',this,0)\" class='formselect'>
<option value='1' selected>Tarefa</option>
<option value='tarefas.php?id=2&id_user=$row_user[id_funcionario]'>Solicitação</option>
<option value='tarefas.php?id=3&id_user=$row_user[id_funcionario]'>Informação</option>
<option value='tarefas.php?id=4&id_user=$row_user[id_funcionario]'>Mesagem</option>
</select></td>";
$titulo = "Tarefa";		  
break;

case 2:
print " <select name='tipo' onchange=\"MM_jumpMenu('parent',this,0)\">
<option value='tarefas.php?id=1&id_user=$row_user[id_funcionario]'>Tarefa</option>
<option value='2' selected>Solicitação</option>
<option value='tarefas.php?id=3&id_user=$row_user[id_funcionario]'>Informação</option>
<option value='tarefas.php?id=4&id_user=$row_user[id_funcionario]'>Mesagem</option>
</select></td>";
$titulo = "Solicitação";		  
break;

case 3:
print " <select name='tipo' onchange=\"MM_jumpMenu('parent',this,0)\">
<option value='tarefas.php?id=1&id_user=$row_user[id_funcionario]'>Tarefa</option>
<option value='tarefas.php?id=2&id_user=$row_user[id_funcionario]'>Solicitação</option>
<option value='3' selected>Informação</option>
<option value='tarefas.php?id=4&id_user=$row_user[id_funcionario]'>Mesagem</option>
</select></td>";
$titulo = "Informação";
break;

case 4:
print " <select name='tipo' onchange=\"MM_jumpMenu('parent',this,0)\">
<option value='tarefas.php?id=1&id_user=$row_user[id_funcionario]'>Tarefa</option>
<option value='tarefas.php?id=2&id_user=$row_user[id_funcionario]'>Solicitação</option>
<option value='tarefas.php?id=3&id_user=$row_user[id_funcionario]'>Informação</option>
<option value='4' selected>Mesagem</option>
</select></td>";
$titulo = "Mensagem";		  
break;
}

?>

</tr>
<tr>
	<td height='25' colspan='4' align='center' valign='middle' bgcolor='#cccccc'>
		<?=$row_user['nome']?>, digite abaixo os dados da <?=$titulo?><br />
        Data Criação <?="$dia/$mes/$ano - $row_user[regiao]"?>&nbsp;&nbsp; 
	</td>
</tr>

<tr>
  <td class='style11' align='right'>
    <span  class='fontWhite'>Regi&atilde;o:</span>
    </td>
  <td colspan="3">
    <input type="criador" name="regiao" value="<?=$row_user['regiao']?>" disabled />
    </td>
</tr>	
<td width='27%' class='style13' align='right'>
		<span class='fontWhite'><b>Criador:</b></span>
	</td>
	<td width='73%' colspan="3">
    <input type='criador' name='criador1' value='<?=$row_user['nome1']?>' class='formularios' disabled/>
	</td>
</tr>
<tr>
	<td class='style15' colspan="4">
    <div align='center'>
		<span style='display:none' id='atencaoo' name='atencaoo'> <img src='imagens/atencao.gif' width='40' height='10'></span>
        </div>
	</td>
</tr>	
<tr>
	<td></td>
	<tr>
		<td align='right'><span class='fontWhite'><b>Forma de Envio:</b></span></td>
		<td colspan='2'>
			<input type='radio' name='radio' value='1' 
            onClick="document.all.individual.style.display = (document.all.individual.style.display == 'none') ? 'none' : 'none';  document.all.linhagrupo.style.display = (document.all.linhagrupo.style.display == 'none') ? '' : ''; ">		
			<span class='fontWhite'>Individual</span>			
		</td>
	</tr>	
	<tr>
		<td>&nbsp;</td>
		<td colspan='3'>
        <label>
		<input type='radio' name='radio' value='2' 
        onClick="document.all.linhagrupo.style.display = (document.all.linhagrupo.style.display == 'none') ? 'none' : 'none';  document.all.individual.style.display = (document.all.individual.style.display == 'none') ? '' : ''; " />
        Para o grupo inteiro</label>
		</td>
	</tr>
    
   <!-- <tr>
		<td>&nbsp;</td>
		<td colspan='3'>
        <label>
		<input type='radio' name='radio' value='3' 
        onClick="document.all.linhagrupo.style.display = (document.all.linhagrupo.style.display == 'none') ? 'none' : 'none';  document.all.individual.style.display = (document.all.individual.style.display == 'none') ? 'none' : 'none'; " />
       Todos</label>
		</td>
	</tr>
-->

<tr id="individual" style="display:none">
	<td class='style15' align='right'> <div>Grupo:</div>
	</td>
	
	<td colspan="3">
	<select name="grupo" class='formselect'>
	<?php
	$result_grupo = mysql_query("SELECT * FROM grupo");
	while ($row_grupo = mysql_fetch_array($result_grupo)){
		print "<option value=$row_grupo[id_grupo]>$row_grupo[nome]</option>";
	}
	?>
	</select>
	</td>
</tr>



<tr id="linhagrupo" style="display: none">
	<td class='style15' align='right'>
		<div align='right' class='fontWhite'>Usu&aacute;rio Destino:</div>
	</td>
	<td>
		<select name='user' class='formselect'>
		<?php
        while ($row_funcionarios = mysql_fetch_array($result_funcionarios)){
		print "<option>$row_funcionarios[nome1]</option>";
		}
		?></select></strong></td>
</tr>


<tr>
	<td class='style19'>
		<div align='right' class='fontWhite'>Titulo da Mensagem:</div>
	</td>
	<td colspan="3">
	<input type='text' name='tarefa' id='tarefa' class='formularios' size = '50' onFocus="document.all.tarefa.style.background='#CCFFCC'" 
    onBlur="document.all.tarefa.style.background='#FFFFFF'" style='background:#FFFFFF;' />
	</td>
</tr>



<tr>
	<td  class='style23'><div align='right' class='fontWhite'>Entrega Dia:</div></td>
	
    <td width='66' colspan="3">
    <input name="data_entrega" type="text" 
    style='background:#FFFFFF;'
    onFocus="document.all.data_nasci.style.background='#CCCCCC'" 
    onBlur="document.all.data_nasci.style.background='#FFFFFF'"
    onKeyUp="mascara_data(this);" value="<?=$DatA?>" size="10">
    </td>
</tr>


<tr>
<td valign='top' class='style23'><div align='right' class='fontWhite'>Descri&ccedil;&atilde;o:</div></td>
<td colspan='3'><strong>
<textarea name='descricao' id='descricao' cols='50' rows='7' class='formTextBox' 
onFocus="document.all.descricao.style.background='#CCFFCC'" 
onBlur="document.all.descricao.style.background='#FFFFFF'" style='background:#FFFFFF;'></textarea>
</strong></td>
</tr>


<tr>
    <td align="right" valign="top"> <strong>Enviar anexo?</strong> </td>
    
    <td colspan='3' valign='top' align="left">
        <input name="op_anexo"  class="op_anexo"  type="radio"  value="sim"/> Sim 
        <input name="op_anexo"  class="op_anexo"  type="radio"  value="nao" checked/> Não <br>
    </td>
    
</tr>

<tr id="envio_anexo" style="display:none;" >
	<td></td>
    <td align="left" colspan="3">
        <input  name="anexo" type="file"  id="anexo"/> 
        <div id="exibir_anexo"></div>	
    
    </td>
</tr>

<tr>
<td colspan=4 align=center class='fontWhite' height="50">Deseja salvar uma cópia dessa mensagem? 
<input type='checkbox' value=1 name='copia' id='copia'></td>
</tr>
<tr>
<td valign='top'></td>
<td colspan='3'><strong>
<input type='hidden' name='id_cadastro' value='5'/>
<input type='hidden' name='id_regiao' value='<?=$row_user['id_regiao']?>'/>
<input type='hidden' name='criador' value='<?=$row_user['nome1']?>'/>
</strong></td>
</tr>


<tr>
<td colspan='4' valign='top' bgcolor='#cccccc'>
<div align='center' class='style24'>
OBS: verifique todas as informa&ccedil;&otilde;es antes de postar a mensagem, caso corretas clique em enviar </div></td>
</tr>

</table>


<div align='center'>

<label>
<input type='submit' name='enviar' value='ENVIAR'  class='formularios'/>
</label>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label></label>
</div>
</form>


</td>
</tr>
</table>

</body>
</html>