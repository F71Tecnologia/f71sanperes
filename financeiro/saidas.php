<?php
include ("include/restricoes.php");






include "../conn.php";



$regiao = $_REQUEST['regiao'];



$id_user = $_COOKIE['logado'];







if(!isset($_REQUEST['id'])) {







?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">



<html xmlns="http://www.w3.org/1999/xhtml">



<head>



<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />



<title>Intranet - Financeiro - Sa&iacute;das</title>



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



       alert(\"Data digitada � inv�lida, digite novamente!\"); 



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



    



        //C�lculo para casas decimais setadas por parametro



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



<link href="../uploadfy/css/default.css" rel="stylesheet" type="text/css" />



<link href="../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css" />



<script language="javascript" type="text/javascript" src="../js/jquery-1.3.2.js"></script>



<script type="text/javascript" src="../uploadfy/scripts/swfobject.js"></script>



<script language="javascript" type="text/javascript" src="../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>



<script language="javascript" type="text/javascript">



$().ready(function(){



	var ultimo_ID = "";



	







	



	if(ultimo_ID == ""){



		$.post('../include/insert_finaceiro.php',



			   {'insert' : 'true'},



			   function(ID){



				   			



							$("#Ultimo_ID").val(ID);



							ultimo_ID = $("#Ultimo_ID").val();



							



						}



		);



	}







	var quantidade = 0;



	var file = 0;



	



	$('#FileUp').uploadify({



				'uploader'       : '../uploadfy/scripts/uploadify.swf',



				'script'         : '../include/upload_financeiro.php',



				'folder'         : 'fotos',



				'buttonText'     : 'Enviar foto',



				'queueID'        : 'barra_upload',



				'cancelImg'      : '../uploadfy/cancel.png',



				'auto'           : true,



				'method'         : 'post',



 				'multi'          : true,



				'fileDesc'       : 'Gif, Jpg e Png',



				'fileExt'        : '*.gif;*.jpg;*.png;',



				'scriptData'     : { 



										'Ultimo_ID' : ultimo_ID



									},



				'onSelectOnce'	 : function (Evento,dados){



										



										quantidade = dados.filesSelected;



									},



				'onSelect'       : function(a,queueID,fileObj){



											$('#FileUp').uploadifySettings('scriptData', {



																  'Tipo' : fileObj.type,



																  'Ultimo_ID' : ultimo_ID



																  });



									},



				'onComplete'     : function(a,b,c,d){



											



											file = d;



											



									},



			



				'onAllComplete'   : function(){



										if(quantidade > 1){



											$("#barra_upload").html("");



											$('#linhaUpload').html('Enviado com sucesso');



										}else{



											$("#barra_upload").html("<img width='100px' height='100px' src='../comprovantes/"+file+"' />");



										}



									}



	});







});



</script>



<body>



<table width="700"  border="0" bordercolor="#FFFFFF" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="bordaescura1px">



  <tr>



    <td height="25" colspan="4" align="center" valign="middle" background="imagensfinanceiro/barra3.gif"><strong><span class="menusCima">    CADASTRO DE SA&Iacute;DAS DO FINANCEIRO</span></strong><br /></td>



  </tr>



  



  <tr>



    



    <td height="376" colspan="2" align="center" valign="top" class="linhaspeq">



    <form action="saidas.php" method="post" name='form1' onsubmit="return validaForm()" id="form2">



    <table width="97%" border="0" cellspacing="1" cellpadding="0" class="bordaescura1px">



      <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">



        <td width="25%" height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style2">PROJETO:&nbsp;</span></strong></td>



        <td width="75%" height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="style25">&nbsp;&nbsp;



          <?php



$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' AND status_reg = '1'");



print "<select name='projeto'>";



while($row_projeto = mysql_fetch_array($result_projeto)){



print "<option value=$row_projeto[0]>$row_projeto[id_projeto] - $row_projeto[nome]</option>";



}







print "</select>";







?></td>



      </tr>



      <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">



        <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style16">CONTA PARA D&Eacute;BITO:</span>&nbsp;</strong></td>



        <td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="style25">&nbsp;&nbsp;



          <?php



$result_banco = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$regiao' and interno = '1' AND status_reg = '1' ORDER BY nome ASC");



print "<select name='banco'>";



while($row_banco = mysql_fetch_array($result_banco)){



print "<option value=$row_banco[0]>$row_banco[id_banco] - $row_banco[nome] - $row_banco[agencia] / $row_banco[conta]</option>";



}







print "</select>";







?></td>



      </tr>



      <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">



        <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style2">NOME:&nbsp;</span></strong></td>



        <td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="style25">&nbsp;&nbsp;&nbsp;



        <input name="nome" type="text" id="nome" size="70" onChange="this.value=this.value.toUpperCase()"/></td>



      </tr>



      <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">



        <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style16">ESPECIFICA&Ccedil;&Atilde;O:&nbsp;</span></strong></td>



        <td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="style25">&nbsp;&nbsp;&nbsp; 



        <input name="especifica" type="text" id="especifica" size="70" onChange="this.value=this.value.toUpperCase()"/></td>



      </tr>



      <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">



        <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style16">TIPO:&nbsp;</span></strong></td>



        <td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="style25">&nbsp;&nbsp;&nbsp;



        <?php



$result_tipo = mysql_query("SELECT * FROM entradaesaida WHERE tipo='0' ORDER BY nome");



print "<select name='tipo'>";



while($row_tipo = mysql_fetch_array($result_tipo)){



print "<option value=$row_tipo[0] title='$row_tipo[descricao]'>$row_tipo[0] - $row_tipo[nome]</option>";



}







print "</select>";







?></td>



      </tr>



      <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">



        <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style16">VALOR ADICIONAL:&nbsp;</span></strong></td>



        <td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="style25">&nbsp;&nbsp;&nbsp;          <input name="adicional" type="text" id="adicional" size="20" onkeydown="FormataValor(this,event,17,2)" /></td>



      </tr>



      <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">



        <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style16">VALOR REAL</span>:&nbsp;</strong></td>



        <td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="style25">&nbsp;&nbsp;&nbsp;          <input name="valor" type="text" id="valor" size="20" onkeydown="FormataValor(this,event,17,2)"/></td>



      </tr>



      <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">



        <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong><span class="style16">DATA PARA CREDITO:&nbsp;</span></strong></td>



        <td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="style25">&nbsp;&nbsp;&nbsp;          <input name="data_credito" type="text" id="data_credito" size="10" onkeyup="mascara_data(this)" maxlength="10" /></td>



      </tr>



      <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF"  >



        <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq">



          <strong><span class="style16">SELECIONE O 



            COMPROVANTE:&nbsp;</span></strong></td>



        <td height="30" align="left" valign="middle" bgcolor="#F6F6F6" class="style25" id="linhaUpload">



          <div id="barra_upload"></div>



          <label>



            <input type="file" name="FileUp" id="FileUp" />



            </label>        <br /></td>



      </tr>



      <tr bordercolor="#FFFFFF" bgcolor="#FFFFFF">



        <td colspan="2" align="center" valign="middle" bgcolor="#FFFFFF"><div align="center">



          <br />



          <input type="submit" name="Submit3" value="GRAVAR SA&Iacute;DA" />



		 <input name='id' type='hidden' id='id' value='1'>



         <input type='hidden' name='regiao' value=<?=$regiao?>>



		



		<?php print "<script>function validaForm(){



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



          <input type="hidden" name="Ultimo_ID" id="Ultimo_ID"  value=""/>



        </div></td>



      </tr>



    </table>



    </form>



    



    </td>



    



  </tr>

<?php /* */?>

<!--

  <tr>



    <td height="23" colspan="2" align="center" valign="middle" background="imagensfinanceiro/barra3.gif"><strong><span class="menusCima">CADASTRAR DE TIPOS DE SA&Iacute;DA</span></strong></td>



  </tr>



   <form action="saidas.php" method="post" name="form2" onSubmit="return validaForm2()">



  <tr>



    <td width="173" height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong>



    <span class="style2">&nbsp;NOME:</span>&nbsp;</strong></td>



    <td width="521" height="30" align="left" valign="middle" bgcolor="#F6F6F6">



    <input name="nome" type="text" size="70" id="nome" onChange="this.value=this.value.toUpperCase()"/></td>



  </tr>



  <tr>



    <td height="30" align="right" valign="middle" bgcolor="#EBEBEB" class="linhaspeq"><strong>&nbsp;<span class="style16">DESCRI&Ccedil;&Atilde;O:</span>&nbsp;&nbsp;</strong></td>



    <td height="30" align="left" valign="middle" bgcolor="#F6F6F6">



    <input name="descricao" type="text" size="70" id="descricao" onChange="this.value=this.value.toUpperCase()"/></td>



  </tr>



  <tr>



    <td colspan="2" align="center" valign="top" bgcolor="#FFFFFF"><div align="center">



          <input type="submit" name="Submit2" value="GRAVAR TIPO DE SA&Iacute;DA" />



          <?php



		print "



		<input name='id' type='hidden' id='id' value='2'>



        <input type='hidden' name='tipo' value='0'> 



		<input type='hidden' name='regiao' value='$regiao'>";







		print "<script>function validaForm2(){



           d = document.form2;







           if (d.nome.value == \"\"){



                     alert(\"O campo Nome deve ser preenchido!\");



                     d.nome.focus();



                     return false;



          }







           if (d.descricao.value == \"\"){



                     alert(\"O campo Descri��o deve ser preenchido!\");



                     d.descricao.focus();



                     return false;



          }



		  



		return true;   }



</script> ";







?>



 </div></td>



  </tr>



  </form>



  



  <tr valign="top">



    <td colspan="4" align="center" valign="middle" bgcolor="#E2E2E2">&nbsp;</td>



  </tr>

-->

<?php /**/?>

  <tr valign="top">



    <td colspan="4" align="center" valign="middle"><a href="javascript:window.close()" style="text-decoration:none; color:#000">Fechar</a></td>



  </tr>



</table>



<?php







/* Liberando o resultado */



mysql_free_result($result_banco);



mysql_free_result($result_projeto);



mysql_free_result($result_tipo);







/* Fechando a conex�o */



mysql_close($conn);











}elseif(isset($_REQUEST['id'])){







	



	//--------------------------------------------------------------------||



	//- AQUI COME�A A RODAR A SEGUNDA PARTE.. ONDE CADASTRAREMOS A SA�DA -||



	//- CASO SEJA 1 VAI CADASTRAR UMA SA�DA, CASE SEJA 2 VAI CADASTAR UM -||



	//- NOVO TIPO DE SA�DA												 -||



	//--------------------------------------------------------------------||



	



	$id = $_REQUEST['id'];



	



	switch($id){



		case 1:



	



	//CADASTRANDO SAIDAS



	$ultimo_id = $_REQUEST['Ultimo_ID'];



	$id_user = $_COOKIE['logado'];



	$regiao = $_REQUEST['regiao'];



	$projeto = $_REQUEST['projeto'];



	$banco = $_REQUEST['banco'];



	$nome = $_REQUEST['nome'];



	$especifica = $_REQUEST['especifica'];



	$tipo = $_REQUEST['tipo'];



	$adicional = $_REQUEST['adicional'];



	$valor = $_REQUEST['valor'];



	$data_credito = $_REQUEST['data_credito'];



	$comprovante = $_REQUEST['comprovante'];



	$data_proc = date('Y-m-d H:i:s');



	$data_proc2 = date('Y-m-d');







	$valor = str_replace(".","", $valor);



	$adicional = str_replace(".","", $adicional);











function ConverteData($Data){



 if(strstr($Data, "/")) {



	 $rstData = implode('-', array_reverse(explode('/', $Data)));



     return $rstData;



 } elseif(strstr($Data, "-")) {



    $rstData = implode('/', array_reverse(explode('-', $Data)));



    return $rstData;



 }



}







$data_credito2 = ConverteData($data_credito);







if($tipo == "19") { // VERIFICA SE � IGUAL A SA�DA DE CAIXA







$result_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$banco'");



$row_banco = mysql_fetch_array($result_banco);







$saldo_atual = $row_banco['saldo'];



$adicional = str_replace(",",".",$adicional);



$valor = str_replace(",",".",$valor);



$saldo_atual = str_replace(",",".",$saldo_atual);



$valor_adicional = $adicional + $valor;



$sobra = $saldo_atual - $valor_adicional;



$adicional = number_format($adicional,2,",","");



$valor = number_format($valor,2,",","");



$valor_adicional = number_format($valor_adicional,2,",","");



$sobra = number_format($sobra,2,",","");







$verifica_caixinha = mysql_query("SELECT * FROM caixinha WHERE id_regiao = '$regiao'");



$row_saldo_verifica = mysql_fetch_array($verifica_caixinha);



$row_verifica = mysql_num_rows($verifica_caixinha);







  if(!empty($row_verifica)) {  // VERIFICA SE JA HOUVE SA�DA DE CAIXA PARA REGI�O SELECIONADA







	  $saldo_atual_caixinha = str_replace(",",".", $row_saldo_verifica['saldo']);



	  $valor_adicional_ff = str_replace(",",".", $valor_adicional);



	  $soma_do_caixinha = $saldo_atual_caixinha + $valor_adicional_ff;



	  $saldo_somado_caixinha = number_format($soma_do_caixinha,2,",","");



	  



	  mysql_query("UPDATE caixinha SET saldo = '$saldo_somado_caixinha' WHERE id_caixinha = '$row_saldo_verifica[0]'") or die(mysql_error());







  } else {  // SE N�O HOUVE SA�DA DE CAIXA, ELE INSERE A 1� SA�DA DE CAIXA DESSA REGI�O







   mysql_query("INSERT INTO caixinha(id_projeto,id_regiao,saldo,id_banco) VALUES 



   								    ('$projeto','$regiao','$valor_adicional','$banco')") 



   or die ("$mensagem_erro<br><br>".mysql_error());



  



  } // AQUI TERMINA SE JA HOUVE OU N�O SA�DA DE CAIXA







// INSERE SA�DA!



mysql_query("UPDATE saida SET  id_regiao = '$regiao' ,id_projeto = '$projeto'  ,id_banco = '$banco' ,id_user = '$id_user' ,nome = '$nome' ,especifica = '$especifica' ,tipo = '$tipo',adicional = '$adicional',valor = '$valor' ,data_proc = '$data_proc',data_vencimento = '$data_proc2',status = '2' WHERE id_saida = '$ultimo_id' LIMIT 1;") 



or die ("$mensagem_erro<br><br>".mysql_error());























mysql_query("UPDATE bancos SET saldo = '$sobra' WHERE id_banco = '$banco'") or die(mysql_error()); ?>







<script language= "JavaScript">



alert("Informa��es cadastradas com sucesso CAIXINHA!");



opener.location.reload();



location.href="saidas.php?regiao=<?=$regiao?>&insert=true";



</script>







<?php exit;



}







// AKI TERMINA TUDO QUE FOR REFERENTE A CAIXINHA







mysql_query("UPDATE saida SET  id_regiao = '$regiao', id_projeto =  '$projeto', id_banco =  '$banco', 



			id_user = '$id_user', nome = '$nome', especifica = '$especifica', tipo = '$tipo', adicional = '$adicional',



			valor = '$valor', data_proc = '$data_proc', data_vencimento = '$data_credito2'



			WHERE id_saida = '$ultimo_id' LIMIT 1;") or die ("$mensagem_erro<br><br>".mysql_error());



















// VERIFICANDO SE ESSA SA�DA JA FOI CADASTRADA POR OUTRO USU�RIO



$result_verifica = mysql_query("SELECT * FROM saida WHERE valor = '$valor' AND data_vencimento = '$data_credito2'") or die(mysql_error());



$row_num_verifica = mysql_num_rows($result_verifica);







/*if(!empty($row_num_verifica)) { ?>







<script language= "JavaScript">



alert("Aten��o!\\n\\n Existem <?=$row_num_verifica?> contas com o mesmo valor e data de vencimento!\\n\\n Aten��o na hora de pagar!");



</script>







<?php  }*/



?>







<script language= "JavaScript">



alert("Informa��es cadastradas com sucesso!");



opener.location.reload();



location.href="saidas.php?regiao=<?=$regiao?>&insert=true";



</script>







<?php







break;







	case 2:



	



	// CADASTRANDO TIPOS DE ENTRADAS E SAIDAS







	// QUANDO O TIPO FOR 0(ZERO) SER� SA�DA / SE FOR 1 SER� ENTRADA







	$tipo = $_REQUEST['tipo'];



	$regiao = $_REQUEST['regiao'];



	$nome = $_REQUEST['nome'];



	$descricao = $_REQUEST['descricao'];







	mysql_query("INSERT INTO entradaesaida(nome,descricao,tipo) VALUES ('$nome','$descricao','$tipo')") 



	or die ("$mensagem_erro<br><br>".mysql_error());







	



	if($tipo == 0) {



		$link = "saidas.php?regiao=$regiao?insert=true";



	} else {



		$link = "entradas.php?regiao=$regiao?insert=true";



	}



	



	print "



	<script>



	alert(\"Informa��es cadastradas com sucesso!\");



	opener.location.reload();



	location.href=\"$link\"



	</script>";







break;







} // FINALIZANDO CASE







} //FINALIZANDO IF (ID)



?>



</body>



</html>



