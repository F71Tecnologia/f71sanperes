<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
exit;
}

include "../conn.php";
	
$userlog = $_COOKIE['logado'];
$id = $_REQUEST['id'];
$id_regiao = $_REQUEST['regiao'];

$reembolso = $_REQUEST['reembolso'];

$RELOGADO = mysql_query("SELECT id_funcionario,nome1,id_master FROM funcionario where id_funcionario = '$userlog'");
$ROWLOGADO = mysql_fetch_array($RELOGADO);

$master = $ROWLOGADO['id_master'];

$RE_ree = mysql_query("SELECT *,date_format(data, '%d/%m/%Y') as data FROM fr_reembolso WHERE id_reembolso = '$reembolso'");
$RowRee = mysql_fetch_array($RE_ree);

$codigo = sprintf("%05d",$RowRee['0']);


$dataCAD = date('Y-m-d');
?>
<html>
<head>
<title>:: Intranet :: Reembolso</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Cache-Control" content="No-Cache">
<meta http-equiv="Pragma"        content="No-Cache">
<meta http-equiv="Expires"       content="0">
<link href="../net1.css" rel="stylesheet" type="text/css">
<script language="javascript" src="../jquery-1.3.2.js"></script>

<style type="text/css">
  <!--
.style35 {	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style36 {	font-size: 14px;
	font-family: Verdana, Geneva, sans-serif;
}
.style12 {	font-size: 12px;
	font-weight: bold;
	color: #003300;
}
.style131 {font-size: 10px}
.style14 {font-size: 14px}
.style16 {font-size: 12px; font-weight: bold; }
.style171 {font-size: 10px; font-weight: bold; }
.style191 {color: #FF0000; font-weight: bold; font-size: 14px; }
.style21 {font-size: 12px}
.style31 {	color: #FF0000;
	font-weight: bold;
}
.style61 {font-size: 14px; font-weight: bold; color: #FFFFFF; }
.style71 {color: #003300}
.style9 {color: #FF0000}
-->
</style>


<script type="text/javascript">
    $(function(){
        
       
        $('#projeto').change(function(){
            
        var projeto = $(this).val();
        
        $.ajax({
            
            url : 'action.bancos.php?projeto='+projeto,
            success: function(resposta){
                
                $('#banco').html(resposta);
            }
            
        })
        
            
        })
        
    })
    
    
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
        vr    = vr.replace( "/", "" );
        vr    = vr.replace( "/", "" );
        vr    = vr.replace( ",", "" );
        vr    = vr.replace( ".", "" );
        vr    = vr.replace( ".", "" );
        vr    = vr.replace( ".", "" );
        vr    = vr.replace( ".", "" );
        tam    = vr.length;
        
        if (tam < tammax && tecla != 8)
        {
            tam = vr.length + 1 ;
        }

        if ((tecla == 8) && (tam > 1))
        {
            tam = tam - 1 ;
            vr = objeto.value;
            vr = vr.replace( "/", "" );
            vr = vr.replace( "/", "" );
            vr = vr.replace( ",", "" );
            vr = vr.replace( ".", "" );
            vr = vr.replace( ".", "" );
            vr = vr.replace( ".", "" );
            vr = vr.replace( ".", "" );
        }
    
        //Cálculo para casas decimais setadas por parametro
        if ( tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 )
        {
            if (decimais > 0)
            {
                if ( (tam <= decimais) )
                { 
                    objeto.value = ("0," + vr) ;
                }
                if( (tam == (decimais + 1)) && (tecla == 8))
                {
                    objeto.value = vr.substr( 0, (tam - decimais)) + ',' + vr.substr( tam - (decimais), tam ) ;    
                }
                if ( (tam > (decimais + 1)) && (tam <= (decimais + 3)) &&  ((vr.substr(0,1)) == "0"))
                {
                    objeto.value = vr.substr( 1, (tam - (decimais+1))) + ',' + vr.substr( tam - (decimais), tam ) ;
                }
                if ( (tam > (decimais + 1)) && (tam <= (decimais + 3)) &&  ((vr.substr(0,1)) != "0"))
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

</script>

</head>
<body>
<?php
switch($id){
	case 1:

if($RowRee['funcionario'] == "1"){
	$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$RowRee[id_user]'");
	$row_user = mysql_fetch_array($result_user);
	$NOME = $row_user['nome1'];  
}else{
	$NOME = $RowRee['nome']; 
}

$obs = "Banco: ".$RowRee['banco']." AG: ".$RowRee['agencia']." CC: ".$RowRee['conta']." Favorecido: ".$RowRee['favorecido']." cpf: ".$RowRee['cpf'];

$valor = number_format($RowRee['valor'],2,",",".");
?>
<br><img src='../imagens/carregando/CIRCLE_BALL.gif' align='absmiddle' style="display:none">
<table width="95%" border="0" cellpadding="0" cellspacing="1" bgcolor="#999999" align="center">
  <tr class="campotexto">
    <td width="209" height="28" align="right" valign="middle" bgcolor="#CCFFCC">Nome:&nbsp;</td>
    <td height="28" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
      <?=$NOME?></td>
  </tr>
  <tr class="campotexto">
    <td height="28" align="right" valign="middle" bgcolor="#CCFFCC">Valor:&nbsp;</td>
    <td width="487" height="28" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
    <?=$valor?></td>
    <td width="89" height="28" align="right" valign="middle" bgcolor="#CCFFCC">Data:&nbsp;</td>
    <td width="265" height="28" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
    <?=$RowRee['data']?></td>
  </tr>
  <tr class="campotexto">
    <td height="44" align="right" valign="middle" bgcolor="#CCFFCC">Descri&ccedil;&atilde;o
      :&nbsp;</td>
    <td height="44" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">
    <div style="margin:13px">
    <?=$RowRee['descricao']?>
    </div>
    </td>
  </tr>
  <tr class="campotexto">
    <td height="28" colspan="4" align="center" valign="middle" bgcolor="#666666"><div align="right" class="style35">
      <div align="center" class="style27 style36">Dados  para o Dep&oacute;sito</div>
    </div></td>
  </tr>
  <tr class="campotexto">
    <td height="28" align="right" valign="middle" bgcolor="#CCFFCC">Banco:&nbsp;</td>
    <td height="28" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
      <?=$RowRee['banco']?></td>
  </tr>
  <tr class="campotexto">
    <td height="28" align="right" valign="middle" bgcolor="#CCFFCC">Agencia:&nbsp;</td>
    <td height="28" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
      <?=$RowRee['agencia']?></td>
    <td height="28" align="right" valign="middle" bgcolor="#CCFFCC">Conta:&nbsp;</td>
    <td height="28" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
      <?=$RowRee['conta']?></td>
  </tr>
  <tr class="campotexto">
    <td height="28" align="right" valign="middle" bgcolor="#CCFFCC">Favorecido:&nbsp;</td>
    <td height="28" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
      <?=$RowRee['favorecido']?></td>
    <td height="28" align="right" valign="middle" bgcolor="#CCFFCC">CPF:&nbsp;</td>
    <td height="28" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
    <?=$RowRee['cpf']?></td>
  </tr>
  <tr class="campotexto">
    <td height="52" colspan="4" align="center" valign="middle" bgcolor="#CCFFCC"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="center"><form action="ver_reembolso.php" name="form1" method="post">
          <input name="liberar" type="submit" value="Liberar">
          <input type="hidden" name="nomeE" value="<?=$NOME?>">
          <input type="hidden" name="obs" value="<?=$obs?>">
          <input type="hidden" name="id" id="id" value="2">
          <input type="hidden" name="reembolso" id="reembolso" value="<?=$reembolso?>">
          <input type="hidden" name="regiao" value="<?php echo $id_regiao?>"/>
        </form></td>
        <td align="center"><form action="ver_reembolso.php" name="form1" method="post">
          <input name="liberar2" type="submit" value="Recusar">
          <input type="hidden" name="id" id="id" value="3">
          <input type="hidden" name="reembolso" id="reembolso" value="<?=$reembolso?>">
        </form></td>
      </tr>
    </table></td>
  </tr>
</table>
<br>
<?php

break;
	
	case 2:
	
	$nomeE = $_REQUEST['nomeE'];
	$obs = $_REQUEST['obs'];
	$valor = number_format($RowRee['valor'],2,",",".");
	$id_regiao = $_REQUEST['regiao'];
        
  
	
?>
<br>
<form action="../cadastro2.php" method="post" name='form1' id="form1" onSubmit="return validaForm()">
<table width="95%" border="0" cellpadding="0" cellspacing="1" bgcolor="#999999" align="center">
  <tr class="campotexto">
    <td height="28" colspan="4" align="center" valign="middle" bgcolor="#666666">
      <div align="right" class="style35">
        <div align="center" class="style27 style36"><span class="style131">DIGITE OS DADOS RELATIVOS A SA&Iacute;DA</span></div>
      </div></td>
  </tr>
  <tr class="campotexto">
    <td width="209" height="28" align="right" valign="middle" bgcolor="#CCFFCC">Nome da Sa&iacute;da:&nbsp;</td>
    <td height="28" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
      <input name="nome" type="text" id="nome" size="40" value="REEMBOLSO <?=$codigo." - ".$nomeE?>"  class="campotexto"/></td>
  </tr>
  <tr class="campotexto">
    <td height="28" align="right" valign="middle" bgcolor="#CCFFCC">Especifica&ccedil;&atilde;o:&nbsp; </td>
    <td height="28" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
      <input name="especifica" type="text" id="especifica" size="40" value="<?=$obs." Descricao: ".$RowRee['descricao']?>" class="campotexto"/></td>
  </tr>
  <tr class="campotexto">
    <td height="28" align="right" valign="middle" bgcolor="#CCFFCC">Tipo:&nbsp;</td>
    <td height="28" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
    <?php
$result_tipo = mysql_query("SELECT * FROM entradaesaida WHERE grupo = 30 AND id_entradasaida IN(218, 219,221,222,223,226,227)");
print "<select name='tipo'>";
while($row_tipo = mysql_fetch_array($result_tipo)){
    
    print "<option value='$row_tipo[0]' title='$row_tipo[descricao]'>$row_tipo[0] - $row_tipo[nome]</option>";

}

print "</select>";

?></td>
  </tr>
   <tr class="campotexto" >
      <td height="28" align="right" valign="middle" bgcolor="#CCFFCC"> Projeto:</td>
      <td colspan="3" height="28" align="left" valign="middle"  bgcolor="#CCCCC">
          <select name="projeto" id="projeto">
              
              <option value="">Selecione o projeto...</option>            
            <?php
            $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$id_regiao'");
            while($row_projeto = mysql_fetch_assoc($qr_projeto)):
            
                echo '<option value="'.$row_projeto['id_projeto'].'">'.$row_projeto['id_projeto'].' - '.$row_projeto['nome'].'</option>';    
                
            endwhile;
            ?>
              
          </select>
          
      </td>
   </tr>
  
  <tr class="campotexto" >
      <td height="28" align="right" valign="middle" bgcolor="#CCFFCC"> Banco:</td>
      <td colspan="3" height="28" align="left" valign="middle"  bgcolor="#CCCCCC">
          <select name="banco" id="banco">
           
          </select>
                   
      </td>
  </tr>
  <tr class="campotexto">
    <td height="28" align="right" valign="middle" bgcolor="#CCFFCC">Custo Adicional:&nbsp;</td>
    <td height="28" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
    <input name="adicional" type="text" id="adicional" size="15" OnKeyDown="FormataValor(this,event,17,2)" class="campotexto"/></td>
  </tr>
  <tr class="campotexto">
    <td height="28" align="right" valign="middle" bgcolor="#CCFFCC">Valor:&nbsp;</td>
    <td width="303" height="28" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
   <input name="valor" type="text" id="valor" size="20" value="<?=$valor?>" style="display:none"/> <?=$valor?></td>
    <td width="178" align="right" valign="middle" bgcolor="#CCFFCC">Data para Cr&eacute;dito:</td>
    <td width="360" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
    <input name="data_credito" type="text" id="data_credito" size="13" OnKeyUp="mascara_data(this)" maxlength="10" class="campotexto"></td>
  </tr>
  
  
  
  <tr class="campotexto">
    <td height="52" colspan="4" align="center" valign="middle" bgcolor="#CCFFCC">
    <input name='reembolso' type='hidden' id='reembolso' value='<?=$reembolso?>'>
    <input name='regiao' type='hidden' id='regiao' value='<?php echo $id_regiao; ?>'>   
    <input name='id_cadastro' type='hidden' id='id_cadastro' value='21'>
    <input name="comprovante" type="checkbox" id="comprovante" value="1" style="display:none"/>
    <input type="submit" name="Submit" value="GRAVAR SA&Iacute;DA" />
    
    <script>function validaForm(){
           d = document.form1;

           if (d.regiao.value == "0"){
                     alert("Você deve selecionar uma região!");
                     d.nome.focus();
                     return false;
          }

           if (d.nome.value == ""){
                     alert("O campo Nome deve ser preenchido!");
                     d.nome.focus();
                     return false;
          }
		  
           if (d.data_credito.value == ""){
                     alert("O campo Data deve ser preenchido!");
                     d.data_credito.focus();
                     return false;
          }


		return true;   }
	</script>
    
   </td>
  </tr>
</table>
</form>
<br>
<?php
	//ENVIANDO UMA TAREFA AVISANDO QUE O REEMBOLSO FOI APROVADO
	$RE22 = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = '$RowRee[id_usercad]'");
	$ROW22 = mysql_fetch_array($RE22);
	
	/*
	mysql_query("INSERT INTO tarefa(id_regiao,tipo_tarefa,criador,usuario,tarefa,descricao,data_criacao,data_entrega) 
	VALUES ('$RowRee[id_regiao]', '1', '$ROWLOGADO[nome1]','$ROW22[0]', 'REEMBOLSO APROVADO', 'SEU PEDIDO DE REEMBOLSO NUMERO $codigo FOI APROVADO!', '$dataCAD', '$dataCAD');") or die (mysql_error());*/

break;

	case 3:
	
	$reembolso = $_REQUEST['reembolso'];
	$RE_ree = mysql_query("UPDATE fr_reembolso SET status = '0' WHERE id_reembolso = '$reembolso'");
	
		echo 'Reembolso RECUSADO!';
	
	
	print"<script>

	parent.window.location.reload();
		if (parent.window.hs) {
		var exp = parent.window.hs.getExpander();
		if (exp) { exp.close(); }
		}
</script>";
	
	
break;

}
?>
<script language="javascript" src="../designer_input.js"></script>
</body>
</html>
