<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "../conn.php";
include "../classes/regiao.php";

$id_user = $_COOKIE['logado'];
$cooperado = $_REQUEST['coop'];

// PEGA O ID DO FUNCIONÁRIO LOGADO E SELECIONA OS DADOS DELE NA BASE DE DADOS
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

//FAZENDO UM SELECT NA TABELA MASTAR PARA PEGAR AS INFORMAÇÕES DA EMPRESA
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

//SELECIONANDO A REGIAO AO QUAL ESTA LOGADO
$result_re = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_user[id_regiao]'");
$row_re = mysql_fetch_array($result_re);


//INICIANDO O SELECT DO COOPERADO
$RE_ree = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y') as data_entrada, date_format(data_saida, '%d/%m/%Y') as data_saida FROM autonomo WHERE id_autonomo = '$cooperado'");
$Row = mysql_fetch_array($RE_ree);

$RECoope = mysql_query("SELECT * FROM cooperativas WHERE id_coop = '$Row[id_cooperativa]'");
$RowCoop = mysql_fetch_array($RECoope);

//VERIFICANDO SE VAI TER LOGO OU NÃO
if($RowCoop['foto'] != "0"){
	$LOGO = "<img src='logos/coop_".$RowCoop['0'].$RowCoop['foto']."' width='120' height='86' />";
}else{
	$LOGO = "";
}


$codigo = sprintf("%04d",$Row['campo3']);

$dia = date('d');
$mes_h = date('m');
$ano = date('Y');

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$MesInt = (int)$mes_h;
$mes = $meses[$MesInt];



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>INTRANET - SUBSCRI&Ccedil;&Atilde;O DE QUOTAS - COOPERADO</title>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	font-family:Arial, Helvetica, sans-serif;
}
-->
</style>

<style type='text/css' media='print'> 
.noprint
{ 
   display: none; 
} 
</style>

</head>

<!-- <body onload="javascript:window.print()"> -->
<body>

<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EAEAEA">
  <tr>
    <td align="center" valign="top">
    
    <table width="700" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:2px solid #666">
      <tr>
        <td height="124"><p class="MsoHeader" align="center" style='text-align:center; color:#666'>
        <?=$LOGO?>
        <br />
        <span style="font-size:12px"><?=$RowCoop['nome']?></span>
        <br />
        <span style="font-size:10px"><?=$RowCoop['endereco']." Tel.: ".$RowCoop['tel']." CNPJ: ".$RowCoop['cnpj']?></span>
        
         </p></td>
        </tr>
      <tr>
        <td>
          <div style="margin:15px; font-family:Arial, Helvetica, sans-serif; font-size:13px" align="left">
            <p><strong>
              <?php
			    $data = $Row['data_saida'];	
				
			  	$completa= new regiao();
				echo $completa -> RegiaoLogado();
				echo ", ";
				echo $completa -> MostraDataCompleta($data);
				
			  ?>
            </strong></p>
            <p><strong>At. 
              <?=$Row['nome']?>
            </strong></p>
            <p><strong>Prezad(o)a cooperad(o)a:</strong></p>
            <p>&nbsp;</p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Informamos que seu pedido de  desligamento da<strong>
              <?=$RowCoop['nome']?>
            </strong>foi  apreciado e aprovado em reuni&atilde;o da Diretoria Executiva desta Cooperativa em <strong><?=$Row['data_saida']?></strong> de acordo com o  artigo 11&ordm; do seu Estatuto Social:</p>
            <p><em>Art. 11&ordm; - A demiss&atilde;o  do cooperado, que n&atilde;o poder&aacute; ser negada, dar-se-&aacute; unicamente a seu pedido,  sendo levada ao conhecimento do Conselho de Administra&ccedil;&atilde;o, em sua primeira  reuni&atilde;o e averbada no livro de Matr&iacute;culas, mediante Termo assinado pelo Presidente.</em></p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Conforme prev&ecirc; o Estatuto Social em  seu Artigo 16&ordm; observe que:</p>
            <p>Art.  16&ordm; - A responsabilidade do cooperado demitido, eliminado ou exclu&iacute;do, somente  termina na data em que for aprovado, pela Assembl&eacute;ia Geral, o Balan&ccedil;o  Patrimonial e as contas do ano social em que ocorreu a demiss&atilde;o, elimina&ccedil;&atilde;o ou  exclus&atilde;o.</p>
            <p>O  valor correspondente a sua Cota-Parte estar&aacute; a sua disposi&ccedil;&atilde;o ap&oacute;s a aprova&ccedil;&atilde;o  do balan&ccedil;o financeiro do ano em curso, na Assembl&eacute;ia Geral Ordin&aacute;ria a ser  realizada em mar&ccedil;o do pr&oacute;ximo ano.</p>
            <p><em>Art. 21&ordm; - A  restitui&ccedil;&atilde;o do capital e das sobras l&iacute;quidas, em caso de demiss&atilde;o, elimina&ccedil;&atilde;o  ou exclus&atilde;o, ser&aacute; sempre feita ap&oacute;s a aprova&ccedil;&atilde;o do Balan&ccedil;o Patrimonial, do ano  social em que o cooperado deixou de fazer parte da cooperativa.</em><br />
                <em>Par&aacute;grafo &uacute;nico:  Ocorrendo demiss&atilde;o, elimina&ccedil;&atilde;o ou exclus&atilde;o de cooperados, em n&uacute;mero tal que a  devolu&ccedil;&atilde;o do capital social possa afetar a estabilidade econ&ocirc;mico-financeira da  cooperativa, esta poder&aacute; efetu&aacute;-la em prazo id&ecirc;ntico ao do maior prazo para  integraliza&ccedil;&atilde;o.</em></p>
            <p>&nbsp;</p>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Estamos a disposi&ccedil;&atilde;o para qualquer  esclarecimento adicional.</p>
            <p>&nbsp;</p>
            <p>Atenciosamente:</p>
            <p>
              <b><?=$RowCoop['nome']?></b>
            <br />
            <br />
<br />
            </p>
<p align="center"><span style="font-size:10px">
  <?=$RowCoop['endereco']." Tel.: ".$RowCoop['tel']." CNPJ: ".$RowCoop['cnpj']?>
</span></p>
            </div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="center" valign="top" class='noprint'><a href="javascript:window.close()" style="text-decoration:none; color:#000">fechar</a></td>
  </tr>
</table>
</body>
</html>