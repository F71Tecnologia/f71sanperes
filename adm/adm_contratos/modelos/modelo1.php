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
<title>Extrato F&iacute;sico Financeiro</title>
</head>
<body style="text-align:center; margin:0; background-color:#efefef; font-family:Arial, Helvetica, sans-serif; font-size:13px;">
<table style="margin:50px auto; width:460px; border:1px solid #222; text-align:left; padding:10px; background-color:#fff;">
  <tr>
    <td>
    
        <p align="center"><img src="../../../imagens/logomaster<?=$master?>.gif" alt="" width="110" height="86" /></p>
        <p>&nbsp;</p>
        <p align="right"><?php echo $row_regiao['regiao'].' , '.date('d').' de '.$mes_portugues.' de '.date('Y'); ?></p>
        <p>A <strong>Prefeitura Municipal de <?php echo $row_regiao['regiao']; ?> </strong>apresenta o extrato de relat&oacute;rio de  execu&ccedil;&atilde;o f&iacute;sica e financeira do termo de parceria assinado com a <strong>OSCIP -  Instituto Sorrindo Para a Vida</strong>, apresentado conforme o modelo do anexo II &ndash;  Decreto n.&ordm; 3.100/99, e faz saber:</p>
        <p>Extrato de  Relat&oacute;rio de Execu&ccedil;&atilde;o F&iacute;sica e Financeira do Termo de Parceria</p>
        <p>Custo  do Projeto: <em>Valor global </em><em>estimado</em><em> de at&eacute;</em> <em>R$ <?php echo number_format($row_projeto['verba_destinada'],2,',','.').' ('.valorPorExtenso($row_projeto['verba_destinada']).')'; ?></em>	<br />         Local de Realiza&ccedil;&atilde;o do Projeto: <em>Munic&iacute;pio de <?php echo $row_regiao['regiao']; ?></em><br />
           Data de Assinatura do TP: <em><?php echo implode('/', array_reverse(explode('-', $row_projeto['inicio']))); ?></em><br />
           In&iacute;cio do Projeto: <em><?php echo implode('/', array_reverse(explode('-', $row_projeto['inicio']))); ?></em><br />
           T&eacute;rmino: <em><?php echo implode('/', array_reverse(explode('-', $row_projeto['termino']))); ?></em><br />
           Objetivos do Projeto: <em><?php echo $row_projeto['descricao']; ?></em><br />
           Resultados Alcan&ccedil;ados: <em>Os resultados alcan&ccedil;ados est&atilde;o em conson&acirc;ncia com o cronograma f&iacute;sico-financeiro aprovado pelo poder p&uacute;blico.</em></p>
        
        <table>
          <tr>
            <td colspan="4">Custos de Implanta&ccedil;&atilde;o do Projeto</td>
          </tr>
          <tr>
            <td>Categoria da despesa</td>
            <td>Previsto</td>
            <td>Realizado</td>
            <td>Diferen&ccedil;a</td>
          </tr>
          <tr>
            <td><em>Despesas Operacionais do projeto</em></td>
            <td><em>R$ <?php echo number_format($total_repasse,'2',',','.'); ?></em></td>
            <td><em>R$ <?php echo number_format($total_repasse,'2',',','.'); ?></em></td>
            <td><em>R$ 0,00</em></td>
          </tr>
          <tr>
            <td>TOTAIS:</td>
            <td><em>R$ <?php echo number_format($total_repasse,'2',',','.'); ?></em></td>
            <td><em>R$ <?php echo number_format($total_repasse,'2',',','.'); ?></em></td>
            <td><em>R$ 0,00</em></td>
          </tr>
        </table>
        
       <p>Nome da OSCIP:   <em><?php echo $row_empresa['nome']; ?></em><br />
          Endere&ccedil;o: <em><?php echo implode(' ', explode('-',$row_empresa['endereco'],-2)); ?></em><br />
          <?php list($nulo,$nulo,$cidade,$uf) = explode('-',$row_empresa['endereco']); ?>
          Cidade:   	   <em><?php echo $cidade; ?></em><br />
          UF:              <em><?php echo $uf; ?></em><br />
          CEP:             <em><?php echo $row_empresa['cep']; ?></em><br />
          Tel:             <em><?php echo $row_empresa['tel']; ?></em><br />
          Fax:             <em><?php echo $row_empresa['fax']; ?></em><br />
          E-mail:          <em><?php echo $row_empresa['email']; ?></em><br />
          Nome do Respons&aacute;vel pelo Projeto: <em><?php echo $row_empresa['responsavel']; ?></em><br />
          Cargo / Fun&ccedil;&atilde;o: <em>Diretor</em></p>
          
    </td>
  </tr>
</table>
</body>
</html>