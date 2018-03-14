<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";

$hostname = $_SERVER['REMOTE_ADDR'];

echo "$hostname";

}else{

include "conn.php";
$regiao = $_REQUEST['regiao'];
$id_user = $_COOKIE['logado'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - Financeiro - Sa&iacute;das</title>
<style type="text/css">
<!--
body {
	background-color: #5C7E59;
}
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	color: #003300;
}
.style2 {font-size: 12px}
.style3 {
	color: #FF0000;
	font-weight: bold;
}
.style6 {font-size: 14px; font-weight: bold; color: #FFFFFF; }
.style7 {color: #003300}
.style9 {color: #FF0000}
.style12 {
	font-size: 12px;
	font-weight: bold;
	color: #003300;
}
.style13 {font-size: 10px}
.style14 {font-size: 14px}
.style16 {font-size: 12px; font-weight: bold; }
.style17 {font-size: 10px; font-weight: bold; }
.style19 {color: #FF0000; font-weight: bold; font-size: 14px; }
-->
</style>
<?php
print "
<script>
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
             

       situacao = \"\";  
       // verifica o dia valido para cada mes  
       if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31) {  
           situacao = \"falsa\";  
       }  

       // verifica se o mes e valido  
       if (mes < 01 || mes > 12 ) {  
              situacao = \"falsa\";  
       }  

      // verifica se e ano bissexto  
      if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) {  
            situacao = \"falsa\";  
      }  
   
     if (d.value == \"\") {  
          situacao = \"falsa\";  
    }  

    if (situacao == \"falsa\") {  
       alert(\"Data digitada é inválida, digite novamente!\"); 
       d.value = \"\";  
       d.focus();  
    }  
	
}

function FormataValor(objeto,teclapres,tammax,decimais) 
{

    var tecla            = teclapres.keyCode;
    var tamanhoObjeto    = objeto.value.length;

    if ((tecla == 8) && (tamanhoObjeto == tammax))
    {
        tamanhoObjeto = tamanhoObjeto - 1 ;
    }



if (( tecla == 8 || tecla == 88 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 ) && ((tamanhoObjeto+1) <= tammax))
    {

        vr    = objeto.value;
        vr    = vr.replace( \"/\", \"\" );
        vr    = vr.replace( \"/\", \"\" );
        vr    = vr.replace( \",\", \"\" );
        vr    = vr.replace( \".\", \"\" );
        vr    = vr.replace( \".\", \"\" );
        vr    = vr.replace( \".\", \"\" );
        vr    = vr.replace( \".\", \"\" );
        tam    = vr.length;
        
        if (tam < tammax && tecla != 8)
        {
            tam = vr.length + 1 ;
        }

        if ((tecla == 8) && (tam > 1))
        {
            tam = tam - 1 ;
            vr = objeto.value;
            vr = vr.replace( \"/\", \"\" );
            vr = vr.replace( \"/\", \"\" );
            vr = vr.replace( \",\", \"\" );
            vr = vr.replace( \".\", \"\" );
            vr = vr.replace( \".\", \"\" );
            vr = vr.replace( \".\", \"\" );
            vr = vr.replace( \".\", \"\" );
        }
    
        //Cálculo para casas decimais setadas por parametro
        if ( tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 )
        {
            if (decimais > 0)
            {
                if ( (tam <= decimais) )
                { 
                    objeto.value = (\"0,\" + vr) ;
                }
                if( (tam == (decimais + 1)) && (tecla == 8))
                {
                    objeto.value = vr.substr( 0, (tam - decimais)) + ',' + vr.substr( tam - (decimais), tam ) ;    
                }
                if ( (tam > (decimais + 1)) && (tam <= (decimais + 3)) &&  ((vr.substr(0,1)) == \"0\"))
                {
                    objeto.value = vr.substr( 1, (tam - (decimais+1))) + ',' + vr.substr( tam - (decimais), tam ) ;
                }
                if ( (tam > (decimais + 1)) && (tam <= (decimais + 3)) &&  ((vr.substr(0,1)) != \"0\"))
                {
                    objeto.value = vr.substr( 0, tam - decimais ) + ',' + vr.substr( tam - decimais, tam ) ; 
                }
                if ( (tam >= (decimais + 4)) && (tam <= (decimais + 6)) )
                {
                     objeto.value = vr.substr( 0, tam - (decimais + 3) ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
                }
                 if ( (tam >= (decimais + 7)) && (tam <= (decimais + 9)) )
                {
                     objeto.value = vr.substr( 0, tam - (decimais + 6) ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
                }
                if ( (tam >= (decimais + 10)) && (tam <= (decimais + 12)) )
                {
                     objeto.value = vr.substr( 0, tam - (decimais + 9) ) + '.' + vr.substr( tam - (decimais + 9), 3 ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
                }
                if ( (tam >= (decimais + 13)) && (tam <= (decimais + 15)) )
                {
                     objeto.value = vr.substr( 0, tam - (decimais + 12) ) + '.' + vr.substr( tam - (decimais + 12), 3 ) + '.' + vr.substr( tam - (decimais + 9), 3 ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
                }
            }
            else if(decimais == 0)
            {
                if ( tam <= 3 )
                { 
                     objeto.value = vr ;
                }
                if ( (tam >= 4) && (tam <= 6) )
                {
                    if(tecla == 8)
                    {
                        objeto.value = vr.substr(0, tam);
                        window.event.cancelBubble = true;
                        window.event.returnValue = false;
                    }
                    objeto.value = vr.substr(0, tam - 3) + '.' + vr.substr( tam - 3, 3 ); 
                }
                if ( (tam >= 7) && (tam <= 9) )
                {
                    if(tecla == 8)
                    {
                        objeto.value = vr.substr(0, tam);
                        window.event.cancelBubble = true;
                        window.event.returnValue = false;
                    }
                    objeto.value = vr.substr( 0, tam - 6 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ); 
                }
                if ( (tam >= 10) && (tam <= 12) )
                {
                     if(tecla == 8)
                    {
                        objeto.value = vr.substr(0, tam);
                        window.event.cancelBubble = true;
                        window.event.returnValue = false;
                    }
                    objeto.value = vr.substr( 0, tam - 9 ) + '.' + vr.substr( tam - 9, 3 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ); 
                }
                if ( (tam >= 13) && (tam <= 15) )
                {
                    if(tecla == 8)
                    {
                        objeto.value = vr.substr(0, tam);
                        window.event.cancelBubble = true;
                        window.event.returnValue = false;
                    }
                    objeto.value = vr.substr( 0, tam - 12 ) + '.' + vr.substr( tam - 12, 3 ) + '.' + vr.substr( tam - 9, 3 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ) ;
                }            
            }
        }
    }
    else if((window.event.keyCode != 8) && (window.event.keyCode != 9) && (window.event.keyCode != 13) && (window.event.keyCode != 35) && (window.event.keyCode != 36) && (window.event.keyCode != 46))
        {
            window.event.cancelBubble = true;
            window.event.returnValue = false;
        }
} 

</script></head>";

?>
<body>
<table width="750" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td colspan="4"><img src="layout/topo.gif" width="750" height="38" /></td>
  </tr>
  
  <tr>
    <td width="21" rowspan="6" background="layout/esquerdo.gif">&nbsp;</td>
    <td width="354" align="center" valign="middle" bgcolor="#FFFFFF"><div align="center" class="style6">
      <div align="left"><font face="Verdana, Arial, Helvetica, sans-serif"><span class="style7">&nbsp;<img src="imagensfinanceiro/saidas.gif" alt="saidas" width="25" height="25" align="absmiddle" /><span class="style9">CADASTRAR SA&Iacute;DA</span></span></font></div>
    </div></td>
    <td width="349" align="left" bgcolor="#FFFFFF">&nbsp;</td>
    <td width="26" rowspan="6" background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td height="16" colspan="2" align="center" valign="top"><div align="left"><span class="style12">&nbsp;&nbsp;&nbsp;<span class="style13">DIGITE OS DADOS RELATIVOS A SA&Iacute;DA </span></span></div></td>
  </tr>
  <tr>
    <td height="96" colspan="2" align="center" valign="top" bgcolor="#FFCCCC">

<form action="cadastro2.php" method="post" enctype="multipart/form-data" name='form1' onSubmit="return validaForm()" id="form1">
      <div align="right"><span class="style2"><br />
          <strong> PROJETO:</strong></span> 
          <?php
$result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao'");
print "<select name='projeto'>";
while($row_projeto = mysql_fetch_array($result_projeto)){
print "<option value=$row_projeto[0]>$row_projeto[id_projeto] - $row_projeto[nome]</option>";
}

print "</select>";

?>
          &nbsp;&nbsp;&nbsp;<br>
          <br><span class="style16">CONTA PARA D&Eacute;BITO :</span> 
          <?php
$result_banco = mysql_query("SELECT * FROM bancos where id_regiao = '$regiao'");
print "<select name='banco'>";
while($row_banco = mysql_fetch_array($result_banco)){
print "<option value=$row_banco[0]>$row_banco[id_banco] - $row_banco[nome] - $row_banco[agencia] / $row_banco[conta]</option>";
}

print "</select>";

?>
          &nbsp;&nbsp;&nbsp;&nbsp;<br />
        <span class="style2"><br />
        <strong>NOME:</strong></span> 
          <input name="nome" type="text" id="nome" size="80" />
&nbsp;&nbsp;&nbsp;&nbsp;<br> </div>
      <div align="right">
      </div>
      <div align="right"><span class="style16">ESPECIFICA&Ccedil;&Atilde;O:</span>
          <input name="especifica" type="text" id="especifica" size="80" />
&nbsp;&nbsp;&nbsp;&nbsp;<br />
<br />
          <span class="style16">TIPO:</span> 
          <?php
$result_tipo = mysql_query("SELECT * FROM entradaesaida WHERE tipo='0' ORDER BY nome");
print "<select name='tipo'>";
while($row_tipo = mysql_fetch_array($result_tipo)){
print "<option value=$row_tipo[0] title='$row_tipo[descricao]'>$row_tipo[0] - $row_tipo[nome]</option>";
}

print "</select>";

?>
          <span class="style16">&nbsp;&nbsp;<br /><br />CUSTO ADICIONAL: R$ </span> 
          <input name="adicional" type="text" id="adicional" size="15" OnKeyDown="FormataValor(this,event,17,2)" />
<span class="style17">(SE NECESS&Aacute;RIO)</span>&nbsp;&nbsp;&nbsp;&nbsp;<br />
      <br />
      <span class="style16">VALOR: R$ </span>
          <input name="valor" type="text" id="valor" size="20" OnKeyDown="FormataValor(this,event,17,2)"/>
      &nbsp;<span class="style16">(ex 15000,00) </span>&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;<span class="style16">DATA PARA CREDITO:</span>
      <input name="data_credito" type="text" id="data_credito" size="10" OnKeyUp="mascara_data(this)" maxlength="10">
      &nbsp;&nbsp;&nbsp;&nbsp; <br /><br>
          <span class="style16">ANEXAR COMPROVANTE:</span>&nbsp; 
          <input name="comprovante" type="checkbox" id="comprovante" onClick="document.all.tablearquivo.style.display = (document.all.tablearquivo.style.display == 'none') ? '' : 'none' ;" value="1"/>
          &nbsp;&nbsp;&nbsp; <br />
          <span class="style16">
          <br>
          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="display:none" id="tablearquivo">
            <tr>
              <td width="27%" align="right"><span class="style16">SELECIONE O 
                COMPROVANTE:</span></td>
              <td width="73%" align="right"><span class="style16"> 
                <input name="arquivo" type="file" id="arquivo" size="60" />
                <span class="style16"> &nbsp;&nbsp;&nbsp;&nbsp;</span> </span></td>
            </tr>
          </table>
          <br />
       <input type="submit" name="Submit" value="GRAVAR SA&Iacute;DA" />
       &nbsp;&nbsp;&nbsp;</div>
        <div align="right"></div>  
		<?php
		print "
		<input name='id_cadastro' type='hidden' id='id_cadastro' value='21'>
        <input type='hidden' name='regiao' value='$regiao'>";
		
		print "
<script>function validaForm(){
           d = document.form1;

           if (d.nome.value == \"\"){
                     alert(\"O campo Nome deve ser preenchido!\");
                     d.nome.focus();
                     return false;
          }

           if (d.valor.value == \"\"){
                     alert(\"O campo Valor deve ser preenchido!\");
                     d.valor.focus();
                     return false;
          }
		  
           if (d.data_credito.value == \"\"){
                     alert(\"O campo Data deve ser preenchido!\");
                     d.data_credito.focus();
                     return false;
          }


		return true;   }
</script> ";

		?>
	</form>
    </td>
  </tr>
  
  <tr>
    <td height="18" colspan="2" align="center" valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td height="18" colspan="2" align="center" valign="top"><div align="left"><font face="Verdana, Arial, Helvetica, sans-serif"><span class="style7">&nbsp;<img src="imagensfinanceiro/saidas.gif" alt="saidas" width="25" height="25" align="absmiddle" /></span><span class="style3">&nbsp;<span class="style14">CADASTRAR TIPOS DE</span></span><span class="style19"> SA&Iacute;DA</span><span class="style19"> </span></font></div></td>
  </tr>
  <tr>
    <td height="18" colspan="2" align="center" valign="top" bgcolor="#FFCCCC">
    
    <div align="right">
    <form action="cadastro2.php" method="post" name="form2" onSubmit="return validaForm2()">
    <span class="style2"><strong>
    
    <br />
      &nbsp;&nbsp;&nbsp;NOME:</strong></span>
      <input name="nome" type="text" size="40" id="nome" />
&nbsp;&nbsp;&nbsp;<span class="style16">DESCRI&Ccedil;&Atilde;O:</span>
<input name="descricao" type="text" size="40" id="descricao" />
        &nbsp;&nbsp;&nbsp;&nbsp;<br />
<br />
<input type="submit" name="Submit2" value="GRAVAR TIPO DE SA&Iacute;DA" />
&nbsp;&nbsp;&nbsp;<br />
<?php
		print "
		<input name='id_cadastro' type='hidden' id='id_cadastro' value='23'>
        <input type='hidden' name='tipo' value='0'> 
		<input type='hidden' name='regiao' value='$regiao'>";

		print "
<script>function validaForm2(){
           d = document.form2;

           if (d.nome.value == \"\"){
                     alert(\"O campo Nome deve ser preenchido!\");
                     d.nome.focus();
                     return false;
          }

           if (d.descricao.value == \"\"){
                     alert(\"O campo Descrição deve ser preenchido!\");
                     d.valor.focus();
                     return false;
          }
		  
		return true;   }
</script> ";

?>
<br />
&nbsp;&nbsp;<br />
</form>
<div align="center"><a href="<?php print "financeiro.php?regiao=$regiao"; ?>" class="style12">Voltar</a></div>
<br />
</div></td>
  </tr>
  
  <tr valign="top">
    <td height="37" colspan="4" bgcolor="#5C7E59"><img src="layout/baixo.gif" width="750" height="38" />
<?php
include "empresa.php";
$rod = new empresa();
$rod -> rodape();
?></td>
  </tr>
</table>
<?php

/* Liberando o resultado */
mysql_free_result($result_banco);
mysql_free_result($result_projeto);
mysql_free_result($result_tipo);

/* Fechando a conexão */
mysql_close($conn);
}

?>

</body>
</html>
