<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

$regiao = $_REQUEST['regiao'];
$id_user = $_COOKIE['logado'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - Financeiro - Sa&iacute;da de CAIXA</title>
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
.style6 {font-size: 14px; font-weight: bold; color: #FFFFFF; }
.style7 {color: #003300}
.style9 {color: #FF0000}
.style12 {
	font-size: 12px;
	font-weight: bold;
	color: #003300;
}
.style13 {font-size: 10px}
.style16 {font-size: 12px; font-weight: bold; }
.style18 {font-size: 9px; font-weight: bold; }
-->
</style></head>
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
    <td width="21" rowspan="4" background="layout/esquerdo.gif">&nbsp;</td>
    <td width="354" align="center" valign="middle" bgcolor="#FFFFFF"><div align="center" class="style6">
      <div align="left"><font face="Verdana, Arial, Helvetica, sans-serif"><span class="style7">&nbsp;<img src="imagensfinanceiro/caixa.gif" alt="entrdas" width="25" height="25" align="absmiddle" />&nbsp;<span class="style9">CADASTRAR SA&Iacute;DA DE CAIXA</span> </span></font></div>
    </div></td>
    <td width="349" align="left" bgcolor="#FFFFFF">&nbsp;</td>
    <td width="26" rowspan="4" background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td height="16" colspan="2" align="center" valign="top"><div align="left"><span class="style12">&nbsp;&nbsp;&nbsp;<span class="style13">DIGITE OS DADOS RELATIVOS A SA&Iacute;DA </span></span></div></td>
  </tr>
  <tr>
    <td height="96" colspan="2" align="center" valign="top" bgcolor="#CCFFCC">
    <form action="cadastro2.php" method="post" enctype="multipart/form-data" name="form1" id="form1" onSubmit="return validaForm()">
      <label>
      <div align="right"><span class="style2"><br />
      </span><span class="style2"><strong>PROJETO DESTINO:</strong></span>
      <?php
$result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao'");
print "<select name='projeto'>";
while($row_projeto = mysql_fetch_array($result_projeto)){
print "<option value=$row_projeto[0]> $row_projeto[nome] </option>";
}

print "</select>";

?>
&nbsp;&nbsp;&nbsp;<br />
<span class="style2"><br />
        <strong>NOME:</strong></span> 
        <input name="nome" type="text" size="80" id="nome" />
&nbsp;&nbsp;&nbsp;&nbsp; </div>
      <label>
      <div align="right"><span class="style16">DESCRI&Ccedil;&Atilde;O:</span>
        <input name="descricao" type="text" size="80" id="descricao" />
&nbsp;&nbsp;&nbsp;&nbsp;<br />
<br />
<span class="style16">&nbsp;CUSTO ADICIONAL: R$ </span>
<input name="adicional" type="text" size="20" id="adicional" OnKeyDown="FormataValor(this,event,17,2)"/>
<span class="style18">(SE NECESS&Aacute;RIO)</span>&nbsp;&nbsp;&nbsp;&nbsp;<br />
      <br />
      <span class="style16">VALOR: R$ </span>
      <input name="valor" type="text" size="20" id="valor" OnKeyDown="FormataValor(this,event,17,2)"/>
      &nbsp;<span class="style16">(ex 15000,00)</span>&nbsp;&nbsp;&nbsp;&nbsp; <br />
       <br />
       <input type="submit" name="Submit" value="GRAVAR SA&Iacute;DA" />
       &nbsp;&nbsp;&nbsp;
       <br />
       <?php
		print "
		<input name='id_cadastro' type='hidden' id='id_cadastro' value='24'>
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
		  
		return true;   }
</script> ";

		?>
       <br />
       
      </div>
      </form>    </td>
  </tr>
  
  <tr>
    <td height="18" colspan="2" align="center" valign="top">&nbsp;</td>
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
</body>
</html>
<?php 
}
?>