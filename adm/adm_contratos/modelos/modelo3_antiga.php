<?php
include('../include/restricoes.php');
include('../../../conn.php');
include('../../../classes/formato_valor.php');
include('../../../classes/formato_data.php');
include('../../../classes/valor_extenso.php');

$projeto = $_POST['projeto'];
$master  = $_POST['master'];
$ano	 = $_POST['ano'];

// Consulta do Projeto
$qr_projeto  = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_projeto = mysql_fetch_assoc($qr_projeto);

// Consulta de Região
$qr_regiao  = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row_projeto[id_regiao]'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

// Consulta da Empresa
$qr_empresa  = mysql_query("SELECT * FROM rhempresa WHERE id_empresa = '$row_projeto[id_regiao]'");
$row_empresa = mysql_fetch_assoc($qr_empresa);

// Consulta do Master
$qr_master  = mysql_query("SELECT * FROM master WHERE id_master = '$master'");
$row_master = mysql_fetch_assoc($qr_master);

// Consulta
for($a=1; $a<14; $a++) {

	$mes            = sprintf('%02d', $a);
	$qr_repasse     = mysql_query("SELECT * FROM entrada WHERE tipo = '12' AND id_projeto = '$projeto' AND month(data_pg) = '$mes' AND year(data_pg) = '$ano'");
	$row_repasse    = mysql_fetch_assoc($qr_repasse);
	$total_repasse += str_replace(',', '.', $row_repasse['valor']);

}
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
      <p class="pontilhado">Prefeitura Municipal de <?php echo $row_regiao['regiao']; ?></p>
    </td>
  </tr>
  <tr class="secao">
    <td align="center" colspan="6">Extrato de Termo de Parceria</td>
  </tr>
  <tr>
    <td class="secao">Custo do Projeto:</td>
    <td colspan="5">R$ <?php echo number_format($row_projeto['verba_destinada'],2,',','.').' ('.htmlentities(valorPorExtenso($row_projeto['verba_destinada']),ENT_COMPAT,'UTF-8'),')';?></td>
  </tr>
  <tr>
    <td class="secao">Local de Realiza&ccedil;&atilde;o do Projeto:</td>
    <td colspan="5">Munic&iacute;pio de <?php echo $row_regiao['regiao']; ?>
    </td>
  </tr>
  <tr>
    <td width="25%" class="secao">Data de assinatura do TP:
    <td width="13%"><?php echo implode('/', array_reverse(explode('-', $row_projeto['inicio']))); ?></td>
    <td width="19%" class="secao">In&iacute;cio do Projeto: </td>
    <td width="17%"><?php echo implode('/', array_reverse(explode('-', $row_projeto['inicio']))); ?></td>
    <td width="13%" class="secao">T&eacute;rmino:</td>
    <td width="13%"><?php echo implode('/', array_reverse(explode('-', $row_projeto['termino']))); ?></td>
  </tr>
  <tr>
    <td colspan="6"></td>
  </tr>
  <tr >
  	<td colspan="6" align="left" style="text-align:justify"><br />
  	  <strong>Objeto do Termo de Parceria: </strong><br />
    <?php echo implode('/', array_reverse(explode('-', $row_projeto['descricao']))); ?><br />    <br /></td>   
  </tr>
  <tr>
  	<td class="secao">Nome da OSCIP:</td>   
    <td colspan="5"><?php echo $row_master['razao']; ?></td>
  <tr>
  <tr>
  	<td class="secao">Endere&ccedil;o:</td>   
    <td colspan="5"><?php echo implode(', ', explode(',',$row_master['endereco'],-3)); ?></td>
  </tr>
  	<?php list($nulo,$nulo,$nulo,$cidade,$uf,$cep) = explode(',',$row_master['endereco']); ?>
  <tr>
    <td class="secao">Cidade:</td>
    <td><?php echo $cidade; ?></td>
    <td class="secao">UF:</td>
    <td><?php echo $uf; ?></td>
    <td class="secao">CEP:</td>
    <td><?php echo $cep; ?></td>
  </tr>
  <tr>
    <td class="secao">Tel:</td>
    <td><?php echo $row_master['telefone']; ?></td>
    <td class="secao">Fax:</td>
    <td><?php echo $row_master['fax']; ?></td>
    <td class="secao">E-mail:</td>
    <td><?php echo $row_master['email']; ?></td>
  </tr>
  <tr>
    <td class="secao">Nome do respons&aacute;vel pelo projeto:</td>   
    <td colspan="5"><?php echo $row_master['responsavel']; ?></td>
  </tr>
  <tr>
    <td class="secao">Cargo / Fun&ccedil;&atilde;o:</td>   
    <td colspan="5">Presidente</td>
  </tr>
</table>
</body>
</html>