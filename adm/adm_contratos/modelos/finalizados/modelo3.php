<?php
include('../../include/restricoes.php');
include('../../../../conn.php');
include('../../../../classes/formato_valor.php');
include('../../../../classes/formato_data.php');
include('../../../../classes/valor_extenso.php');


$id_entregue     = $_POST['entregue_id'];
$ano_competencia = $_POST['ano_competencia'];
$qr_anexo_xv     = mysql_query("SELECT * FROM obrigacoes_anexo_1 WHERE entregue_id = '$id_entregue' AND ano_competencia = '$ano_competencia'");
$row_anexo_xv 	 = mysql_fetch_assoc($qr_anexo_xv);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Publica&ccedil;&atilde;o</title>
<style type="text/css">
.pontilhado {
	border-bottom:1px dotted #999;
	padding-bottom:2px;
	font-style:italic;
	font-weight:bold;
}
td.secao {
	background-color:#eee;
	text-align:right;
	font-weight:bold;
	padding-right:5px;
}
tr.secao td {
	background-color:#eee;
	text-align:center;
	font-weight:bold;
}
</style>
</head>
<body style="text-align:center; margin:0; background-color:#efefef; font-family:Arial, Helvetica, sans-serif; font-size:13px;">
<table style="margin:50px auto; width:790px; border:1px solid #222; text-align:left; padding:10px; background-color:#fff;" cellpadding="4" cellspacing="1">
  <tr>
    <td align="center" colspan="6">
      <p><strong>LEI 9790/99 - DECRETO N&ordm; 3.100, DE  30 DE JUNHO DE 1999.</strong></p>
      <p>ANEXO I</p>
      <p>&nbsp;</p>
      <p class="pontilhado"><?php echo $row_anexo_xv['prefeitura']; ?></p>
    </td>
  </tr>
  <tr class="secao">
    <td align="center" colspan="6">Extrato de Termo de Parceria</td>
  </tr>
  <tr>
    <td class="secao">Custo do Projeto:</td>
    <td colspan="5">R$ <?php echo number_format($row_anexo_xv['custo_projeto'],2,',','.').' ('.htmlentities(valorPorExtenso($row_anexo_xv['custo_projeto']),ENT_COMPAT,'UTF-8'),')';?></td>
  </tr>
  <tr>
    <td class="secao">Local de Realiza&ccedil;&atilde;o do Projeto:</td>
    <td colspan="5"> <?php echo $row_anexo_xv['local_projeto']; ?>
    </td>
  </tr>
  <tr>
    <td width="25%" class="secao">Data de assinatura do TP:
    <td width="13%"><?php echo implode('/', array_reverse(explode('-', $row_anexo_xv['data_assinatura']))); ?></td>
    <td width="19%" class="secao">In&iacute;cio do Projeto: </td>
    <td width="17%"><?php echo implode('/', array_reverse(explode('-', $row_anexo_xv['inicio_projeto']))); ?></td>
    <td width="13%" class="secao">T&eacute;rmino:</td>
    <td width="13%"><?php echo implode('/', array_reverse(explode('-', $row_anexo_xv['termino_projeto']))); ?></td>
  </tr>
  <tr>
    <td colspan="6"></td>
  </tr>
  <tr >
  	<td colspan="6" align="left" style="text-align:justify"><br />
  	  <strong>Objeto do Termo de Parceria: </strong><br />
    <?php echo implode('/', array_reverse(explode('-', $row_anexo_xv['obj_termo']))); ?><br />    <br /></td>   
  </tr>
  <tr>
  	<td class="secao">Nome da OSCIP:</td>   
    <td colspan="5"><?php echo $row_master['razao']; ?></td>
  <tr>
  <tr>
  	<td class="secao">Endere&ccedil;o:</td>   
    <td colspan="5"><?php echo $row_anexo_xv['endereco_projeto'] ?></td>
  </tr>
  	
  <tr>
    <td class="secao">Cidade:</td>
    <td><?php echo $row_anexo_xv['cidade_oscip']; ?></td>
    <td class="secao">UF:</td>
    <td><?php echo $row_anexo_xv['uf_oscip']; ?></td>
    <td class="secao">CEP:</td>
    <td><?php echo $row_anexo_xv['cep_oscip']; ?></td>
  </tr>
  <tr>
    <td class="secao">Tel:</td>
    <td><?php echo $row_anexo_xv['tel_oscip']; ?></td>
    <td class="secao">Fax:</td>
    <td><?php echo $row_anexo_xv['fax_oscip']; ?></td>
    <td class="secao">E-mail:</td>
    <td><?php echo $row_anexo_xv['email_oscip']; ?></td>
  </tr>
  <tr>
    <td class="secao">Nome do respons&aacute;vel pelo projeto:</td>   
    <td colspan="5"><?php echo $row_anexo_xv['responsavel_projeto']; ?></td>
  </tr>
  <tr>
    <td class="secao">Cargo / Fun&ccedil;&atilde;o:</td>   
    <td colspan="5"><?php echo $row_anexo_xv['cargo_responsavel'];?></td>
  </tr>
</table>



</body>
</html>


