<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
}else{

include "../conn.php";

$id = $_REQUEST['id'];
$id_user = $_COOKIE['logado'];
$regiao = $_REQUEST['regiao'];

$data = date('d/m/Y');

$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'");
$row_local = mysql_fetch_array($result_local);

if(empty($_REQUEST['nome'])){
	
?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../net1.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript">
$(function(){



$( '.horas_mes, .dias_mes, .entrada1, .saida1, .entrada2, .saida2').change(function(){

	var valor = $(this).val();
	var id_horario = $(this).attr('rel');

	
	
	if($(this).attr('name') == 'horas_mes') {  
			$.ajax({
				url: 'action.altera_horarios.php?horas='+valor+'&horario='+id_horario,
				success:function(resposta){
				
					
					
					
				}
				
				})
    }
	
				
	if($(this).attr('name') == 'dias_mes') {  
		
			$.ajax({
				url: 'action.altera_horarios.php?dias='+valor+'&horario='+id_horario,
				success:function(resposta){
				
					
					
				}
				
				})
	}


	if($(this).attr('name') == 'entrada1') {  
		
			$.ajax({
				url: 'action.altera_horarios.php?entrada1='+valor+'&horario='+id_horario,
				success:function(resposta){
				
					
					
				}
				
				})
	}

	if($(this).attr('name') == 'saida1') {  
		
			$.ajax({
				url: 'action.altera_horarios.php?saida1='+valor+'&horario='+id_horario,
				success:function(resposta){
				
					
					
				}
				
				})
	}
	
	if($(this).attr('name') == 'entrada2') {  
		
			$.ajax({
				url: 'action.altera_horarios.php?entrada2='+valor+'&horario='+id_horario,
				success:function(resposta){
				
					
					
				}
				
				})
	}
	
	if($(this).attr('name') == 'saida2') {  
		
			$.ajax({
				url: 'action.altera_horarios.php?saida2='+valor+'&horario='+id_horario,
				success:function(resposta){
				
					
					
				}
				
				})
	}
	
	});


});
</script>

<style type="text/css">
<!--

#horas_mes, #dias_mes, #entrada1, #saida1, #entrada2, #saida2{
border:0;
background-color:transparent;	
	
	
}
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.style35 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style36 {
	font-size: 14px;
	font-family: Verdana, Geneva, sans-serif;
}
.style38 {
	font-size: 16px;
	font-weight: bold;
	font-family: Geneva, Arial, Helvetica, sans-serif;
	color: #FFFFFF;
}
a:link {
	color: #006600;
}
a:visited {
	color: #006600;
}
a:hover {
	color: #006600;
}
a:active {
	color: #006600;
}
.style40 {font-family: Geneva, Arial, Helvetica, sans-serif}
.style50 {font-family: Geneva, Arial, Helvetica, sans-serif; font-size: 10; font-weight: bold; color: #FFFFFF; }
.style51 {
	font-family: arial, verdana, "ms sans serif";
	font-weight: bold;
}
.style52 {font-family: arial, verdana, "ms sans serif"}
.style53 {font-family: Arial, Verdana, Helvetica, sans-serif}
.style55 {font-size: 10}
.style56 {font-family: Arial, Verdana, Helvetica, sans-serif; font-weight: bold; }
-->

.statusmsg,.warningmsg,.errormsg,.okmsg{font-weight:normal; margin-right:15px}

.warningmsg{background:transparent url(../imagens/yellow-status.gif) no-repeat scroll right center;}
.errormsg{background:transparent url(../imagens/red-status.gif) no-repeat scroll right center;}
.okmsg{background:transparent url(../imagens/green-status.gif) no-repeat scroll right center;}


.errormsg1 {font-weight:normal; margin-right:15px}
</style>

<script language="javascript"> 

  //o parâmentro form é o formulario em questão e t é um booleano 
  function ticar(form, t) { 
    campos = form.elements; 
    for (x=0; x<campos.length; x++) 
      if (campos[x].type == "checkbox") campos[x].checked = t; 
  } 

  function formatar(mascara, documento){ 
  var i = documento.value.length; 
  var saida = mascara.substring(0,1); 
  var texto = mascara.substring(i) 
   
  if (texto.substring(0,1) != saida){ 
            documento.value += texto.substring(0,1); 
  } 
   
} 

function pula(maxlength, id, proximo){ 
   if(document.getElementById(id).value.length >= maxlength){ 
     document.getElementById(proximo).focus();
 }
} 

</script> 
</head>

<body bgcolor="#FFFFFF">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="top"><br>
    <table width="90%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" >
    <tr>
        <td height="53" ><div align="right" style="padding-right:10px;"><?php include('../reportar_erro.php'); ?></div></td>
      </tr>
      <tr>
        <td height="53" background="../imagens/fundo_cima.gif"><div align="center"><span class="style38">CONTROLE DE HOR&Aacute;RIOS</span></div></td>
      </tr>
      <tr>
        <td>
        <br /><br/ >
        <table  height="350" width="95%" border="0" align="center" cellspacing="0" class="bordaescura1px">
          <tr>
            <td height="34" bgcolor="#666666"><div align="right" class="style35">
              <div align="center" class="style27 style36">DADOS DO HOR&Aacute;RIO<br>
              </div>
            </div></td>
          </tr>
          <tr>
            <td height="300">
            <form action="rh_horarios.php" method="post" name='form1' id="form1">
              
              <table width="100%" border="0" cellpadding="0" cellspacing="1">
                <tr>
                  <td width="14%" height="35" class="style19"><div align="right"><span class="style40"><strong>Nome</strong>:</span></div></td>
                  <td height="35" colspan="5">&nbsp;&nbsp;
                    <input name="nome" type="text" id="nome" size="80" class="warningmsg"
                         onChange="this.value=this.value.toUpperCase()"></td>
                </tr>
                <tr>
                  <td height="35" class="style19"><div align="right"><span class="style40"><strong>Observa&ccedil;&otilde;es:</strong></span></div></td>
                  <td height="35" colspan="5">&nbsp;&nbsp;
                    <input name="obs" type="text" id="obs" size="80" class="okmsg" onChange="this.value=this.value.toUpperCase()"></td>
                </tr>
                <tr>
                  <td height="35" class="style19"><div align="right"><span class="style40"><strong>Atividade:</strong></span></div></td>
                  <td height="35" colspan="5">&nbsp;&nbsp;&nbsp;
                    <select name='salario' id='salario' class='campotexto'>
                      <?php

$result_curso = mysql_query("Select * from  curso where id_regiao = '$regiao' and tipo = '2'");

while ($row_curso = mysql_fetch_array($result_curso)){

   print "<option value='$row_curso[salario]-$row_curso[0]' > $row_curso[id_curso] - $row_curso[campo2] / $row_curso[salario]</option>";   
   
}
					  
?>
                    </select></td>
                </tr>
                <tr>
                  <td height="72" class="style19"><div align="right"><span class="style40"><strong>Preenchimento:</strong></span></div></td>
                  <td colspan="5"><table width="100%" border="1">
                    <tr> <font face="Verdana, Geneva, sans-serif">
                      <td width="22%" align="center" bgcolor="#CCCCCC"><strong>Entrada </strong></td>
                      <td width="29%" align="center" bgcolor="#CCCCCC"><strong>Sa&iacute;da almo&ccedil;o</strong></td>
                      <td width="26%" align="center" bgcolor="#CCCCCC"><strong>Retorno almo&ccedil;o</strong></td>
                      <td width="23%" align="center" bgcolor="#CCCCCC"><strong>Sa&iacute;da</strong></td>
                    </font></tr>
                    <tr>
                      <td height="26" align="center" bgcolor="#CCCCCC"><input name="entrada1" type="text" id="entrada1" size="10" OnKeyUP="formatar('##:##:##', this); pula(8,this.id,saida1.id)" maxlength="8" 
onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'"  style="background:#FFFFFF;"></td>
                      <td align="center" bgcolor="#CCCCCC"><input name="saida1" type="text" id="saida1" size="10" OnKeyUP="formatar('##:##:##', this); pula(8,this.id,entrada2.id)" maxlength="8" 
onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'"  style="background:#FFFFFF;"></td>
                      <td align="center" bgcolor="#CCCCCC"><input name="entrada2" type="text" id="entrada2" size="10" OnKeyUP="formatar('##:##:##', this); pula(8,this.id,saida2.id)" maxlength="8" 
onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'"  style="background:#FFFFFF;"></td>
                      <td align="center" bgcolor="#CCCCCC"><input name="saida2" type="text" id="saida2" OnKeyUP="formatar('##:##:##', this); pula(8,this.id,hora_mes.id)" size="10" maxlength="8" 
onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'"  style="background:#FFFFFF;"></td>
                    </tr>
                  </table></td>
                </tr>
                <tr>
                	<td height="52" class="style19"><div align="right"><span class="style40"><strong>Horas M&ecirc;s:</strong></span></div></td>
                    <td width="12%">&nbsp;&nbsp;&nbsp;
                    <input name="hora_mes" type="text" id="hora_mes" size="10"
                      onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'"  style="background:#FFFFFF;"></td>
                      
                    <td height="52" class="style19"><div align="right"><span class="style40"><strong>Dias M&ecirc;s:</strong></span></div></td>
                    <td width="12%">&nbsp;&nbsp;&nbsp;
                      <input name="dias_mes" type="text" size="10"
                      onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'"  style="background:#FFFFFF;"></td>
                </tr>
                
                <tr>                  
                  
                  <td width="15%"><div align="right"><span class="style40"><strong>Dias Semana:</strong></span></div></td>
                  <td width="10%">&nbsp;&nbsp;
                    <input name="dias_semana" type="text" id="dias_semana" size="10" 
                      onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'"  style="background:#FFFFFF;"></td>
                  <td width="7%"><div align="right"><span class="style40"><strong>Folgas:</strong></span></div></td>
                  <td width="42%" align="center" valign="middle"><div align="left"><span class="style40"><strong>
                    <label>
                      <input name="folga1" type="checkbox" id="folga1" value="1">
                      S&aacute;bado</label>
                    /
                    <label>
                      <input name="folga2" type="checkbox" id="folga2" value="2">
                      Domingo</label>
                    /
                    <label>
                      <input name="folga3" type="checkbox" id="folga3" value="5">
                      Plantonista</label>
                  </strong></span></div></td>
                </tr>
              </table>
              <br>
              <div align="center">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="display:none" id="tablearquivo">
                  <tr>
                    <td width="15%" align="right"><span class="style19">SELECIONE:</span></td>
                    <td width="85%"><span class="style19"> &nbsp;&nbsp;
                      <input name="arquivo" type="file" id="arquivo" size="60" />
                    </span></td>
                  </tr>
                </table>
                <p><br>
                  <input type="hidden" value="<?=$regiao?>" name="regiao">
                  <input type="submit" name="gerar" id="gerar" value="CRIAR HOR&Aacute;RIO">
                  <br>
                  <br>
                </p>
              </div>
            </form></td>
          </tr>
        </table>
          <br>
          <br>
          <table  height="114" width="95%"align="center" cellspacing="0" class="bordaescura1px">
            <tr>
              <td height="45" bgcolor="#666666"><div align="right" class="style35">
                <div align="center" class="style27 style36">HOR&Aacute;RIOS CADASTRADADOS</div>
              </div></td>
            </tr>
            <tr>
              <td><span class="style40">
                <label> </label>
                </span>
                <label> </label>
                <span class="style40"><strong>
                  <label></label>
                  </strong></span> <br>
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr style="background-color:  #A6A6A6; border:solid 1px #000;">
                    <td height="37" align="left">C&oacute;d</td>
                    <td width="20%" >Atividade</td>
                    <td width="14%" >Horario</td>
                    <td width="10%" >Entrada 1</td>
                    <td width="10%" >Saida 1</td>
                    <td width="10%" >Entrada 2</td>
                    <td width="10%" >Saida 2</td>
                    <td width="5%" >Dias</td>
                    <td>Horas mês</td>
                    <td>Dias mês</td>
                    <td width="10%" >Folgas</td>
                    <td width="10%" >Editar</td>

                  </tr>
                  <?php

$result_horarios = mysql_query("SELECT * FROM rh_horarios where id_regiao = '$regiao'");
$cont = 0;
while($row_horarios = mysql_fetch_array($result_horarios)){

if($row_horarios['folga'] == "0"){
	$folga_p = "Sem Folgas";
}elseif($row_horarios['folga'] == "1"){
	$folga_p = "S&aacute;bado";
}elseif($row_horarios['folga'] == "2"){
	$folga_p = "Domingo";
}elseif($row_horarios['folga'] == "3"){
	$folga_p = "Sabado e Domingo";
}elseif($row_horarios['folga'] == "5"){
	$folga_p = "Plantonista";
}


$result_atividade = mysql_query("SELECT * FROM curso where id_curso = '$row_horarios[funcao]'");
$row_atividade = mysql_fetch_array($result_atividade); 

if($cont % 2){ $classcor="corfundo_um"; }else{ $classcor="corfundo_dois"; };

?>
<tr  class='<?php echo  $classcor;?>'>
<td><?php echo $row_horarios[0] ?></td>
<td><?php echo $row_atividade['id_curso'] - $row_atividade['nome']?></td>
<td><?php echo $row_horarios['nome']?></td>
<td>

<!--<select name="entrada1" class="entrada1" rel="<?php echo $row_horarios['id_horario']?>">
<?php
for($i=0; $i < 24; $i++){	
	
	$horario = sprintf('%02s',$i).':00';
	$selected = ($horario == substr($row_horarios['entrada_1'], 0, 5))? 'selected="selected"' :'';		
	echo '<option value="'.$horario.'" '.$selected.'> &nbsp;'.$horario.'&nbsp; </option>';
}
?>
</select>

-->
<input type="text" name="entrada1" value="<?php echo $row_horarios['entrada_1']?>" class="entrada1" rel="<?php echo $row_horarios['id_horario']?>"  OnKeyUP="formatar('##:##:##', this); pula(8,this.id,saida1.id)" maxlength="8"   size="5"/>
</td>
<td>
<input type="text" name="saida1" value="<?php echo $row_horarios['saida_1']?>" class="saida1" rel="<?php echo $row_horarios['id_horario']?>" OnKeyUP="formatar('##:##:##', this); pula(8,this.id,saida1.id)" maxlength="8" size="5"/>

</td>
<td><input type="text" name="entrada2" value="<?php echo $row_horarios['entrada_2']?>" class="entrada2" rel="<?php echo $row_horarios['id_horario']?>" OnKeyUP="formatar('##:##:##', this); pula(8,this.id,saida1.id)" maxlength="8" size="5"/></td>
<td><input type="text" name="saida2" value="<?php echo $row_horarios['saida_2']?>" class="saida2" rel="<?php echo $row_horarios['id_horario']?>" OnKeyUP="formatar('##:##:##', this); pula(8,this.id,saida1.id)" maxlength="8" size="5"/></td>
<td><?php echo $row_horarios['dias_semana']?></td>
<td><input type="text" name="horas_mes" value="<?php echo $row_horarios['horas_mes']?>" class="horas_mes" rel="<?php echo $row_horarios['id_horario']?>" size="5"/></td>
<td><input type="text" name="dias_mes" value="<?php echo $row_horarios['dias_mes']?>"   class="dias_mes" rel="<?php echo $row_horarios['id_horario']?>" size="5"/></td>
<td><?php echo $folga_p;?></td>
<td><a href='rh_horarios_alterar.php?regiao=<?=$regiao;?>&horario=<?=$row_horarios[0];?>'>EDITAR</a></td>
</tr>


<?php
$cont ++;
}

?>
                </table></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td><div align="center"></div></td>
            </tr>
          </table></td>
      </tr>
    </table></td>
  </tr>
</table>
</body>
</html>
 <?php
include "../empresa.php";
$rod = new empresa();
$rod -> rodape();


/* Liberando o resultado */
mysql_free_result($result_local);
mysql_free_result($result_curso);

}else{ // AKI VAI RODAR O CADASTRO
		  
		  
$regiao = $_REQUEST['regiao'];

$nome = $_REQUEST['nome'];
$obs = $_REQUEST['obs'];
$salario = $_REQUEST['salario'];

$entrada1 = $_REQUEST['entrada1'];
$saida1 = $_REQUEST['saida1'];
$entrada2 = $_REQUEST['entrada2'];
$saida2 = $_REQUEST['saida2'];
$hora_mes = $_REQUEST['hora_mes'];
$dias_semana = $_REQUEST['dias_semana'];
$dias_mes    =  $_REQUEST['dias_mes'];

$folga1 = $_REQUEST['folga1'];
$folga2 = $_REQUEST['folga2'];
$folga3 = $_REQUEST['folga3'];

if($folga1 == "1" and $folga2 == "2"){// SEGUNDA A SEXTA
	$folga = "3";
}elseif($folga1 == "1" ){// FOLGA NO SABADO
	$folga = "1";
}elseif($folga2 == "2" ){// FOLGA NO DOMINGO
	$folga = "2";
}elseif($folga3 == "5" ){// PLANTONISTA
	$folga = "5";
}else{
	$folga = "0";		//SEM FOLGAS ( SEGUNDA À SEGUNDA )
}


$id_user = $_COOKIE['logado'];
$data_cad = date('Y-m-d');

$salario = explode("-", $salario);


//-- INICIANDO O CALCULO DO SALARIO PARA RETIRAR O VALOR DIARIO E O VALOR HORA

$diaria = $salario[0] / 30;
$hora = $diaria / 8;


$diaria = str_replace(",",".", $diaria);
$diaria_f = number_format($diaria,2,",",".");

$hora = str_replace(",",".", $hora);
$hora_f = number_format($hora,2,",",".");

mysql_query("INSERT INTO rh_horarios(id_regiao,nome,obs,entrada_1,saida_1,entrada_2,saida_2,dias_semana,horas_mes,
salario,funcao,valor_dia,valor_hora,folga, dias_mes)
VALUES ('$regiao','$nome','$obs','$entrada1','$saida1','$entrada2','$saida2','$dias_semana','$hora_mes',
'$salario[0]','$salario[1]','$diaria_f','$hora_f','$folga', '$dias_mes') ") or die ("<hr>Erro no insert<br><hr>".mysql_error());

print "
<script>
alert (\"Informações gravadas com sucesso\");
location.href=\"rh_horarios.php?regiao=$regiao\"
</script>
";

}

/* Fechando a conexão */
mysql_close($conn);

}

?>
