<?php

include ("include/restricoes.php");
include "../conn.php";
include "../funcoes.php";

$regiao = $_REQUEST['regiao'];
$id_user = $_COOKIE['logado'];

//ENCRIPTOGRAFANDO
$linkEnc = encrypt($regiao); 
$linkEnc = str_replace("+","--",$linkEnc);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - Financeiro - Sa&iacute;da de CAIXA</title>
<style type="text/css">
<!--
body {
		font-family:Arial, Helvetica, sans-serif;
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.menusCima {
	color:#FFF;
	font-size:12px;
	text-decoration:none;
}
.linkMenu {
	text-decoration:none;
	color:#FFF;
}
.titulosTab {
	color:#FFF;
	font-size:10px;
	font-weight:bold;
	border-bottom:#666 solid 1px;
}
.linhaspeq{
	font-size:11px;
}
.style25 {	font-size: 11px;
	font-weight: bold;
}
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

<link href="../net1.css" rel="stylesheet" type="text/css" />
<body>
<table width="650"  border="1" bordercolor="#FFFFFF" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="bordaescura1px">
<tr>
	<td colspan="4"><a href="../novoFinanceiro/index.php?enc=<?php echo $linkEnc; ?>" style="color: #069"><img src="../img_menu_principal/voltar.png" title="VOLTAR" /></a></td>
</tr>
  <tr>
    <td height="25" colspan="4" align="center" valign="middle" background="imagensfinanceiro/barra3.gif"><strong><span class="menusCima"> CADASTRAR SA&Iacute;DA DE CAIXA</span></strong><br /></td>
  </tr>
  <tr>
    <td width="694" height="208" colspan="2" align="center" valign="top" bgcolor="#FFFFFF" class="linhaspeq">
    <form action="../cadastro2.php" method="post" enctype="multipart/form-data" name="form1" id="form1" onSubmit="return validaForm()">
      <table width="97%" border="0" cellspacing="1" cellpadding="0" class="bordaescura1px">
        <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">
          <td width="25%" height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style2">PROJETO:&nbsp;</span></strong></td>
          <td width="75%" height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="style25">&nbsp;&nbsp;
            <?php
$result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao' AND status_reg = '1'");
print "<select name='projeto'>";
while($row_projeto = mysql_fetch_array($result_projeto)){
print "<option value=$row_projeto[0]>$row_projeto[id_projeto] - $row_projeto[nome]</option>";
}

print "</select>";

?></td>
        </tr>
        <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">
          <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style2">NOME:&nbsp;</span></strong></td>
          <td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="style25">&nbsp;&nbsp;&nbsp;
            <input name="nome" type="text" id="nome" size="70" onchange="this.value=this.value.toUpperCase()"/></td>
        </tr>
        <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">
          <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style16">ESPECIFICA&Ccedil;&Atilde;O:&nbsp;</span></strong></td>
          <td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="style25">&nbsp;&nbsp;&nbsp;
            <input name="descricao" type="text" id="descricao" size="70" onchange="this.value=this.value.toUpperCase()"/></td>
        </tr>
        <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">
          <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style16">VALOR ADICIONAL:&nbsp;</span></strong></td>
          <td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="style25">&nbsp;&nbsp;&nbsp;
            <input name="adicional" type="text" id="adicional" size="20" onkeydown="FormataValor(this,event,17,2)" /></td>
        </tr>
        <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">
          <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style16">VALOR REAL</span>:&nbsp;</strong></td>
          <td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="style25">&nbsp;&nbsp;&nbsp;
            <input name="valor" type="text" id="valor" size="20" onkeydown="FormataValor(this,event,17,2)"/></td>
        </tr>
        <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">
          <td colspan="2" align="center" valign="middle" bgcolor="#FFFFFF"><div align="center"> <br />
              <input type="submit" name="Submit3" value="GRAVAR SA&Iacute;DA" />
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
          </div></td>
        </tr>
      </table>
    </form></td>
  </tr>
  <form action="saidas.php" method="post" name="form2" id="form2" onsubmit="return validaForm2()">
  </form>
  <tr valign="top">
    <td colspan="4" align="center" valign="middle"><a href="javascript:window.close()" style="text-decoration:none; color:#000">Fechar</a></td>
  </tr>
</table>
</body>
</html>
