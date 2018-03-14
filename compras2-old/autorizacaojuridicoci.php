<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "../conn.php";


if(empty($_REQUEST['cadastro'])){

$regiao = $_REQUEST['regiao'];
$pedido = $_REQUEST['compra'];

$result = mysql_query("SELECT *,date_format(data_produto, '%d/%m/%Y')as data_produto, date_format(data_requisicao, '%d/%m/%Y')as data_requisicao FROM compra2 where id_compra = '$pedido'");
$row = mysql_fetch_array($result);

$result_reg = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'", $conn);
$row_reg = mysql_fetch_array($result_reg);

$result_user = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = '$row[id_user_pedido]'", $conn);
$row_user = mysql_fetch_array($result_user);

$result_anexo = mysql_query("SELECT * FROM anexo_abertura_proc WHERE id_compra='$pedido'");
$row_anexo = mysql_fetch_assoc($result_anexo);

$result_cham = mysql_query("SELECT * FROM anexo_chamamento2 WHERE id_compra = '$pedido'");
$dados_cham = mysql_fetch_assoc($result_cham);

$result_comp = mysql_query("SELECT * FROM compra2 WHERE id_compra= '$pedido'");
$dados_comp = mysql_fetch_assoc($result_comp);

$result_venc = mysql_query("SELECT * FROM fornecedor_site WHERE fornecedor_site_id = '$dados_comp[fornecedor_escolhido]'");
$dados_venc = mysql_fetch_assoc($result_venc);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../adm/css/estrutura.css" rel="stylesheet" type="text/css">
<title>Intranet - Controle de Cota&ccedil;&otilde;es</title>

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

</head>

<body>

<div id="corpo">
	<div id="conteudo">
  
      <table width="100%" cellpadding="0" cellspacing="0">
        <col width="44" />
        <col width="64" span="5" />
        <col width="9" />
        <col width="64" />
        <col width="62" />
        <col width="138" />
        <col width="133" />
        <col width="120" />
        <tr height="28">
          <td width="100%" height="28" align="left" valign="top"><div align="center"><br/>
  <?
  include("../empresa.php");
  $imgCNPJ = new empresa();
  $imgCNPJ -> imagemCNPJ()
  ?>
  <br>
              <span class="style2"><strong>CONTROLE DE COTA&Ccedil;&Otilde;ES DE PRODUTOS OU SERVI&Ccedil;OS</strong></span></div></td>
        </tr>
        <tr height="32">
          <td height="32">&nbsp;</td>
        </tr>
        <tr height="32">
          <td height="22" align="right">
          <form action="autorizacaojuridicoci.php" method="post" name="form1">
          
            <div align="center"><span class="style29">

              </span><br />
            </div>
            <br />
            <table width="96%" align="center" cellpadding="0" cellspacing="1">
              <col width="44" />
              <col width="64" span="5" />
              <col width="9" />
              <col width="64" />
              <col width="62" />
              <col width="138" />
              <col width="133" />
              <col width="120" />
              <tr height="32">
                <td colspan="2" height="30" align="center" bgcolor="#333333" class="style29" style="color:#FFF">ANEXOS DA SOLICITAÇÃO DE COMPRA </td>
             
              </tr>
              <tr>
              <td colspan="2">&nbsp;</td>
              </tr>
              <tr height="32">
                <td width="33%" height="30" align="right" valign="middle" bgcolor="#F7F7F7" class="style29">edital:</td>
                <td width="67%">&nbsp;&nbsp;   <a target="_blank" href="../anexo_edital/<?= $dados_cham['anexo_edital']; ?>"><img src='../imagens/ver_anexo2.gif' border=0></a></td>
              </tr>
              <tr height="32">
                <td height="30" align="right" valign="middle" bgcolor="#F7F7F7" class="style29">chamamento:</td>
                <td>&nbsp;&nbsp;  <a target="_blank" href="../anexo_chamamento/<?= $dados_cham['anexo_chamamento']; ?>"><img src='../imagens/ver_anexo2.gif' border=0></a></td>
              </tr>
              <tr height="32">
                <td height="30" align="right" valign="middle" bgcolor="#F7F7F7" class="style29">OR&Ccedil;AMENTO PR&Eacute;VIO</td>
                <td>&nbsp;&nbsp;  <a target="_blank" href="../anexo_cotacao/<?= $row_anexo['anexo_cotacao']; ?>"><img src='../imagens/ver_anexo2.gif' border=0></a></td>
              </tr>
              <tr height="32">
                <td height="30" align="right" valign="middle" bgcolor="#F7F7F7" class="style29">PESQUISA DE PRE&Ccedil;O</td>
                <td>&nbsp;&nbsp;  <a target="_blank" href="../anexo_docpreco/<?= $row_anexo['anexo_docpreco']; ?>"><img src='../imagens/ver_anexo2.gif' border=0></a></td>
              </tr> 
                 <tr height="32">
                <td height="30" align="right" valign="middle" bgcolor="#F7F7F7" class="style29">ATA DE LICITA&Ccedil;&Atilde;O:</td>
                <td>&nbsp;&nbsp;  <a target="_blank" href="../anexo_atas/<?= $dados_comp['anexo_ata']; ?>"><img src='../imagens/ver_anexo2.gif' border=0></a></td>
              </tr>    
                 <tr height="32">
                <td height="30" align="right" valign="middle" bgcolor="#F7F7F7" class="style29">OR&Ccedil;AMENTO DO VENCEDOR:</td>
                <td>&nbsp;&nbsp;  <a target="_blank" href="../anexo_propostas/<?= $dados_venc['anexo_proposta']; ?>"><img src='../imagens/ver_anexo2.gif' border=0></a></td>
              </tr>   
            </table>
          <br /><br />
            <div align="center">
              <p>APÓS ANÁLISE DOS ANEXOS CITADOS ACIMA ENCAMINHO PARA O CADASTRAMENTO NO PRESTADOR DE SERVI&Ccedil;O.</p>
<p>&nbsp;</p>
              <p>
                <input type="submit" name="GRAVAR3" id="GRAVAR3" value="DEFERIR" />
         
                &nbsp;&nbsp;&nbsp;&nbsp; <input type="button" name="INDEFERIR" value="INDEFERIR" onclick="location.href=' autorizacaojuridico.php?compra=<?=$pedido?>&regiao=<?=$regiao?>&cadastro=2'" />
          
                <input type="hidden" value="<?php print "$row[0]";?>" name="produto" />
                <input type="hidden" value="<?php print "$regiao";?>" name="regiao" />
                <input type="hidden" value="<?php print "$dados_venc[razao]";?>" name="razao" />
                <input type="hidden" value="<?php print "$dados_venc[cnpj]";?>" name="cnpj" />
                <input type="hidden" value="<?php print "$dados_venc[telefone]";?>" name="telefone" />
                <input type="hidden" value="<?php print "$dados_venc[email]";?>" name="email" />
                <input type="hidden" value="<?php print "$dados_comp[id_projeto]";?>" name="projeto" />





                <input type="hidden" value="1" name="cadastro" />

                <br />                
                <br />

              </p>
            </div>
            </form>
            
          <div align="center"><br />
              <?php print "<a href='../gestaocompras2.php?id=1&regiao=$regiao'><img src='../imagens/voltar.gif' border=0></a>"; ?>
            
           </div></td>
        </tr>
        
        <tr height="32">
          <td height="32"><div align="center"><span class="style12">&nbsp; &nbsp; </span></div></td>
        </tr>
      </table>


<?php
$rod = new empresa();
$rod -> rodape();
?>
</div>
</div>

</body>
</html>
<?php
}else{   //----------------- ALTERANDO OS REGISTRO NA BASE DE DADOS -----------------------//
$cadastro = $_REQUEST['cadastro'];
if ($cadastro == '1')
{

$regiao = $_REQUEST['regiao'];
$pedido = $_REQUEST['produto'];
$id_user = $_COOKIE['logado'];
$razao = $_REQUEST['razao'];
$cnpj = $_REQUEST['cnpj'];
$telefone = $_REQUEST['telefone'];
$email = $_REQUEST['email'];
$projeto = $_REQUEST['projeto'];

$result_cont = mysql_query("SELECT * FROM prestadorservico where id_regiao = '$regiao'");
$row_cont = mysql_num_rows($result_cont);
$row_cont = $row_cont + 1;
$num_id = sprintf("%03s", $row_cont);
$numero = $num_id."/".date('Y');


mysql_query("INSERT INTO prestadorservico (id_regiao, id_projeto, numero, c_razao, c_cnpj, c_tel, c_email, id_compra) VALUES('$regiao', '$projeto' , '$numero',  '$razao', '$cnpj', '$telefone','$email','$pedido')");



mysql_query("UPDATE compra2 SET acompanhamento='8' where id_compra = '$pedido' LIMIT 1") or die ("<center>ERRO!<br> tente novamente mais tarde<br><br>".mysql_error());


header("Location: ver_autorizacaojuridicoci.php?compra=$pedido&regiao=$regiao ");
}

if ($cadastro == '2')
{
	
$regiao = $_REQUEST['regiao'];
$pedido = $_REQUEST['compra'];
$id_user = $_COOKIE['logado'];





mysql_query("UPDATE compra2 SET acompanhamento='6' where id_compra = '$pedido' LIMIT 1") or die ("<center>ERRO!<br> tente novamente mais tarde<br><br>".mysql_error());	

header("Location: ../gestaocompras2.php ");
	
	
	
}


}

}




?>