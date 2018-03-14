<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

$regiao = $_REQUEST['regiao'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - REQUISI&Ccedil;&Atilde;O DE COMPRA</title>
<style type="text/css">
<!--

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
.style7 {font-size: 14px}
.style8 {color: #FF0000}
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
<link href="net1.css" rel="stylesheet" type="text/css" />
</head>
<script language="javascript" src="jquery-1.3.2.js"></script>
<?php
if(empty($_REQUEST['tipo'])){

?>

<body>
<table width="750" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td colspan="4"><img src="layout/topo.gif" width="750" height="38" /></td>
  </tr>
  
  <tr>
    <td width="21" rowspan="2" background="layout/esquerdo.gif">&nbsp;</td>
    <td height="25" colspan="2" align="left" valign="middle" bgcolor="#FFFFCC"><img src="imagensfinanceiro/controledecotacoes.gif" alt="cotas" width="25" height="25" align="absmiddle" /> <span class="style3">REQUISI&Ccedil;&Atilde;O  DE COMPRA </span></td>
    <td width="26" rowspan="2" background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td height="18" colspan="2" align="right" valign="top"><div align="center">
      <p align="left"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;</strong></p>
      </div></td>
  </tr>
  
  <tr>
    <td height="27" background="layout/esquerdo.gif">&nbsp;</td>
    <td colspan="2" rowspan="2" align="center" valign="top"><br />
      <form id="form1" name="form1" method="post" action="solicitacompra.php">
        <label class="style2">
        <div align="left"><strong>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php
		$result_cont1 = mysql_query("SELECT id_compra FROM compra");
		$row_cont1 = mysql_num_rows($result_cont1);
		$row_cont1 ++;
		$regiao_re = sprintf("%03d",$regiao);
		$n_registros = sprintf("%06d",$row_cont1);
		$aleatorio = mt_rand(1,99);
		$n_aleatorio = sprintf("%02d",$aleatorio);
		$n_ano = date("y");
		
		$n_requisicao = $regiao_re.".".$n_registros."-".$n_aleatorio."/".$n_ano;
		print "Nº. Requisição: $n_requisicao<br>";

		?><br />
          &nbsp;&nbsp;&nbsp;Tipo:
          <select name="tipo" id="tipo">
                <option value="1">PRODUTO</option>
                <option value="2">SERVI&Ccedil;O</option>
          </select>
&nbsp;              &nbsp;&nbsp;&nbsp;Integra&ccedil;&atilde;o ao Patrim&ocirc;nio:
<select name="patrimonio" id="patrimonio">
  <option value="1">BEM DE CONSUMO</option>
  <option value="2">BEM DUR&Aacute;VEL</option>
</select>
&nbsp; <br />
<br />
&nbsp;&nbsp;&nbsp; Produto:
          <input name="produto" type="text" id="produto" size="80" />
          </strong><br />  
          <br />
          <strong>&nbsp;&nbsp;&nbsp;Descri&ccedil;&atilde;o do Produto:
          <input name="descricao" type="text" id="descricao" size="84" maxlength="300" />
          <br />
          <br />
          <strong>&nbsp;&nbsp;&nbsp;Quantidade:
          <input name="quantidade" type="text" id="quantidade" size="10" />
&nbsp;&nbsp;&nbsp;Valor M&eacute;dio:
          R$ 
<input name="valor_medio" type="text" id="valor_medio" size="20"  OnKeyDown="FormataValor(this,event,17,2)"/>
          </strong> </strong>&nbsp;&nbsp;&nbsp;<strong><strong>&nbsp;Necess&aacute;rio para:
          <input name="data" type="text" id="data" size="10" OnKeyUp="mascara_data(this)" maxlength="10"/>
          </strong></strong><br />
          <br />
          <strong>&nbsp;&nbsp;&nbsp;Descri&ccedil;&atilde;o da Necessidade:
          <input name="necessidade" type="text" id="necessidade" size="77" maxlength="250" />
          </strong><br />
          <br />
        </div>
        </label>
        <input type="hidden" name="requisicao" id="requisicao" value="<?php echo $n_requisicao; ?>" />
        <input type="hidden" name="regiao" id="regiao" value="<?php echo $regiao; ?>" />
        <div align="left"></div>
        <span class="style2">
        <label class="style2">        </label>
        </span>
        <label class="style2"><div align="left" class="style2"></div>
        <div align="left" class="style2"></div>
        <br />
        <label>
        <input type="submit" name="solicitar" id="button" value="SOLICITAR" />
        </label>
        &nbsp;&nbsp;&nbsp;
        <label>
        <input type="reset" name="cancela" id="cancela" value="CANCELAR" />
        </label>
        <br />
        <br />
      </form>
      </td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td height="69" background="layout/esquerdo.gif">&nbsp;</td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  
  
  
  <tr>
    <td background="layout/esquerdo.gif">&nbsp;</td>
    <td width="349" height="19" align="right" valign="top" class="style3">&nbsp;</td>
    <td width="354" align="center" valign="middle" class="style3"></td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr valign="top">
    <td height="37" colspan="4" bgcolor="#E2E2E2"><img src="layout/baixo.gif" width="750" height="38" />
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
}else{                   //------------------------------------GRAVANDO OS DADOS DO FORMULÁRIO -------------------

$id_user_pedido = $_COOKIE['logado'];
$tipo = $_REQUEST['tipo'];
$produto = $_REQUEST['produto'];
$descricao = $_REQUEST['descricao'];
$valor_medio = $_REQUEST['valor_medio'];
$data = $_REQUEST['data'];
$requisicao = $_REQUEST['requisicao'];
$quantidade = $_REQUEST['quantidade'];
$necessidade = $_REQUEST['necessidade'];

/* 
Função para converter a data
De formato nacional para formato americano.
Muito útil para você inserir data no mysql e visualizar depois data do mysql.
*/


function ConverteData($Data){
 if (strstr($Data, "/"))//verifica se tem a barra /
 {
  $d = explode ("/", $Data);//tira a barra
 $rstData = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
 return $rstData;
 } elseif(strstr($Data, "-")){
 $d = explode ("-", $Data);
 $rstData = "$d[2]/$d[1]/$d[0]"; 
 return $rstData;
 }else{
 return "Data invalida";
 }
}

$data_c = ConverteData($data);
$data_hoje = date("Y-m-d");
$valor_medio = str_replace(".","",$valor_medio);


mysql_query ("INSERT INTO compra (id_regiao,id_user_pedido,num_processo,tipo,nome_produto,descricao_produto,necessidade,quantidade,valor_medio,data_produto,data_requisicao,status_requisicao,acompanhamento) 
VALUES 
('$regiao','$id_user_pedido','$requisicao','$tipo','$produto','$descricao','$necessidade','$quantidade','$valor_medio','$data_c','$data_hoje','1','1')");


print "<br>
<hr>
<center>
<font color=#FFFFFF> 
Seu pedido foi cadastrado com sucesso!<br><br>
Faça o acompanhamento periódicamente.
</font>
<br><br>
<a href='gestaocompras.php?id=1&regiao=$regiao'><img src='imagens/voltar.gif' border=0></a>
</center>
";


}
}

?>
<script language="javascript" src="designer_input.js"></script>