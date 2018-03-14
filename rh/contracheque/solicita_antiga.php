<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a>";
exit;
}
include "../../conn.php";
include "../../funcoes.php";
include "../../classes/projeto.php";
include "../../classes/regiao.php";
include "../../classes/clt.php";
include "../../classes/curso.php";

$Projeto = new projeto();
$ClassRegiao = new regiao();
$ClassCLT = new clt();
$ClassCurso = new tabcurso();

$id = $_REQUEST['id'];

//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 
$decript = explode("&",$link);

$regiao = $decript[0];
$folha = $decript[1];
//RECEBENDO A VARIAVEL CRIPTOGRAFADA


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: INTRANET :: Contracheques</title>
<meta http-equiv="Cache-Control" content="No-Cache">
<meta http-equiv="Pragma"        content="No-Cache">
<meta http-equiv="Expires"       content="0">

<link href="../../net1.css" rel="stylesheet" type="text/css">


</head>

<body>
<?php
if($id == 1){
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" valign="middle"><br />
      <table width="80%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
      <tr>
      		<td align="right"><?php include("../../reportar_erro.php"); ?> </td>
      </tr>
        <tr class="fundo_azul">
          <td height="27"><div class="titulo_claro">Recibos de Pagamentos</div></td>
        </tr>
        <tr>
          <td width="20%" height="72" align="center">
          <br>
          <table width="90%" border="0" cellspacing="0" cellpadding="0" class="bordaescura1px">
            <tr>
              <td height="26" colspan="7" class="fundo_claro"><div class="titulo">Folhas Finalizadas</div></td>
              </tr>
            <?php
            
			$RE = mysql_query("SELECT *,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim 
			FROM rh_folha where regiao = '$regiao' and status = '3' ORDER BY projeto,mes");
			
			$cont = 0;

			while($Row = mysql_fetch_array($RE)){
				
				$Projeto -> MostraProjeto($Row['projeto']);
				$Pnome = $Projeto -> nome;
				
				
				$mes = $ClassRegiao -> MostraMes($Row['mes']);
				
				//-- ENCRIPTOGRAFANDO A VARIAVEL
				$linkunico = encrypt("$regiao&$Row[0]"); 
				$linkunico = str_replace("+","--",$linkunico);
				//-- ---------------------------
				
				//-- ENCRIPTOGRAFANDO A VARIAVEL
				$linkvario = encrypt("$regiao&todos&$Row[id_folha]");
				$linkvario = str_replace("+","--",$linkvario);
				//-- ---------------------------
				
				if($cont % 2){ $classcor="corfundo_um"; }else{ $classcor="corfundo_dois"; }
				
			?>
            <tr class="novalinha <?=$classcor?>">
              <td width="4%" height="30" align="left">&nbsp;&nbsp;<?=$Row['0']?></td>
              <td width="34%" align="left"><?=$Pnome?></td>
              <td width="9%" align="left"><span style="color:#F00"><?=$mes?></span></td>
              <td width="19%" align="left"><?=$Row['data_inicio']." at&eacute; ".$Row['data_fim']?></td>
              <td width="18%" align="left"><span style="color:#00F"><?=$Row['clts']." Participantes "?></span></td>
              <td width="8%" align="center">
              <a href="solicita.php?id=2&enc=<?=$linkunico?>" target="_blank">
              <img src="../imagensrh/user.gif" border="0" alt="Gerar para uma única pessoa" title="Gerar para uma única pessoa"></a></td>
              <td width="8%" align="center">
              <a href="solicita.php?enc=<?=$linkvario?>&id=3">
              <img src="../imagensrh/group.gif" alt="Gerar de Todos" title="Gerar de Todos" border="0" /></a></td>
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
      </table>
      <br />
      <span aling=rigth><a href='../../principalrh.php?regiao=<?=$regiao?>' class='link'><img src='../../imagens/voltar.gif' border=0></a></span>
      <br /></td>
  </tr>
</table>
<?php

mysql_free_result($RE);

}elseif($id == 2){



?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" valign="middle"><br />
      <table width="80%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
        <tr class="fundo_azul">
          <td height="27"><div class="titulo_claro">Recibos de Pagamentos</div></td>
        </tr>
        <tr>
          <td width="20%" height="72" align="center"><br />
            <table width="90%" border="0" cellspacing="0" cellpadding="0" class="bordaescura1px">
              <tr>
                <td height="26" class="fundo_claro">cod</td>
                <td height="26" class="fundo_claro">NOME</td>
                <td height="26" class="fundo_claro">ATIVIDADE</td>
                <td height="26" class="fundo_claro">VALOR</td>
                <td height="26" class="fundo_claro">GERAR</td>
              </tr>
              <?php
			
			$RE = mysql_query("SELECT *	FROM rh_folha_proc WHERE id_folha = '$folha' and status = '3' ORDER BY nome");
			$cont = 0;
            while($Row = mysql_fetch_array($RE)){
				
				
				$ClassCLT -> MostraClt($Row['id_clt']);
				$id_curso = $ClassCLT -> id_curso;
				
				$ClassCurso -> MostraCurso($id_curso);
				$NomeCurso = $ClassCurso -> campo2;
				
			
				//-- ENCRIPTOGRAFANDO A VARIAVEL
				$linkunico = encrypt("$regiao&$Row[id_clt]&$Row[id_folha]"); 
				$linkunico = str_replace("+","--",$linkunico);
				//-- ---------------------------
				
				if($cont % 2){ $classcor="corfundo_um"; }else{ $classcor="corfundo_dois"; }
			?>
              <tr class="novalinha <?=$classcor?>">
                <td width="7%" height="30" align="center"><?=$Row['cod']?></td>
                <td width="42%" align="left"><?=$Row['nome']?></td>
                <td width="33%" align="left"><?=$NomeCurso?></td>
                <td width="11%" align="center"><span style="color:#00F">
                  <?=" R$ ".$Row['salliquido']?>
                </span></td>
                <td width="7%" align="center">
                <a href="geracontra.php?enc=<?=$linkunico?>">
                <img src="../../imagens/pdf.gif" width="24" height="30" border="0" alt="Gerar PDF" title="Gerar PDF"/>
                </a>
                </td>
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
      </table>
<?php
//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkvoltar = encrypt("$regiao&1"); 
$linkvoltar = str_replace("+","--",$linkvoltar);
//-- ---------------------------
?>
      
      <br />
      <span aling="rigth"><a href='solicita.php?id=1&enc=<?=$linkvoltar?>' class='link'><img src='../../imagens/voltar.gif' border="0" /></a>
      </span> <br /></td>
  </tr>
</table>
<br />


<?php
}elseif($id == 3){
	
// RECEBENDO VARIAVEIS
$enc1 = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc1);
$link = decrypt($enc);

$decript = explode("&",$link);

$regiao 	= $decript[0];
$clt 		= $decript[1];
$id_folha 	= $decript[2];
// RECEBENDO VARIAVEIS

$RE = mysql_query("SELECT * FROM rh_folha where id_folha = '$id_folha' and status = '3' ");
$Row = mysql_fetch_array($RE);


//-- ENCRIPTOGRAFANDO A VARIAVEL
$vai = encrypt("$regiao&todos&$id_folha"); 
$vai = str_replace("+","--",$vai);
//-- ---------------------------
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" valign="middle"><br />
      <table width="80%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
        <tr class="fundo_azul">
          <td height="27"><div class="titulo_claro">Gerando</div></td>
        </tr>
        <tr>
          <td width="20%" height="72" align="center">
          
          <?php
		  $max    = 50;
		  $pedaco = ceil($Row['clts'] / $max);
		  
		  	  
		  echo '<table width="80%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">';
		  echo '<tr class="novalinha">';
		  
		  $a = 1;
		  $maxini = 0;
		  $maxfim = 0;
		  
	  
		  for($i=1 ; $i <= $pedaco ; $i ++){
			  
			  $maxfim = $maxfim + $max;
			  if($i != 1){
				  $maxini = $maxini + $max;
			  }
			  
			  if($pedaco == $i){
				  $maxfim = $Row['clts'];
			  }
			  
			  echo "<td>
			  <a href='geracontra.php?enc=$vai&ini=$maxini' target='_blank'>
			  <img src='../../imagens/pdf.gif' width='24' height='30' border='0' alt='Gerar PDF' title='Gerar PDF'/>
			  <br /> Gerar de $maxini a $maxfim</a>
			  </td>";
			  
			  if($a == 5){
				  echo '</tr><tr>';
				  $a = 1;
			  }
			  
			  $a ++;
		  }
		  
		  echo '</tr>';
		  echo '</table>';
		  
          ?>
		  
          <br /></td>
        </tr>
      </table>
      <?php
//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkvoltar = encrypt("$regiao&1"); 
$linkvoltar = str_replace("+","--",$linkvoltar);
//-- ---------------------------

?>
      
      
      
      <br />
      <span aling="rigth"><a href='solicita.php?id=1&enc=<?=$linkvoltar?>' class='link'><img src='../../imagens/voltar.gif' border="0" /></a>
      </span> <br /></td>
  </tr>
</table>




<?php
}
?>
</body>
</html>