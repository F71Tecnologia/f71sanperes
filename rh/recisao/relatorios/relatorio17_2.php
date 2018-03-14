<?php
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='../login.php'>Logar</a>";
exit;
} else {

include "../conn.php";

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$projeto = $_REQUEST['pro'];
$regiao = $_REQUEST['reg'];

$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_projeto = mysql_fetch_array($result_projeto);

$data_hoje = date('d/m/Y');

$result_cooperado = mysql_query("SELECT * FROM autonomo WHERE tipo_contratacao = '3' AND status = '1' AND id_regiao = '$regiao' AND id_projeto = '$projeto' ORDER BY nome ASC");
$num_cooperado = mysql_num_rows($result_cooperado);
?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>RELAT&Oacute;RIO DE QUOTAS PAGAS</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#FFF; margin-top:30px; margin-bottom:30px;">
<table cellspacing="0" cellpadding="0" class="relacao" style="width:920px; border:0px; page-break-after:always;">
 <tr> 
    <td width="20%" align="center">
          <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
    </td>
    <td width="80%" align="center" colspan="2">
      <strong>RELAT&Oacute;RIO DE QUOTAS PAGAS</strong><br>
         <?=$row_master['razao']?>
         <table width="500" border="0" align="center" cellpadding="4" cellspacing="1" style="font-size:12px;">
            <tr style="color:#FFF;">
              <td width="150" height="22" class="top">PROJETO</td>
              <td width="150" class="top">REGIAO</td>
              <td width="200" class="top">TOTAL DE PARTICIPANTES</td>
            </tr>
            <tr style="color:#333; background-color:#efefef;">
              <td height="20" align="center"><b><?=$row_projeto['nome']?></b></td>
              <td align="center"><b><?=$row_projeto['regiao']?></b></td>
              <td align="center"><b><?php echo $num_cooperado; ?></b></td>
            </tr>
        </table>
    </td>
  </tr>
  <tr> 
    <td colspan="3">
    <?php if(empty($num_cooperado)) { ?>
      <div class="descricao">Nenhum Colaborador</div>
    <?php } else { ?>
      <div class="descricao">RELAT&Oacute;RIO DE QUOTAS PAGAS</div>
      
      <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
    <tr class="secao">
			<td width="10%">C&oacute;digo</td>
			<td width="50%">Nome</td>
			<td width="20%">Total Pago</td>
			<td width="20%">Quantidade de parcelas</td>
			
        </tr>
		<?php 
		$totalizador_quota = 0;
		while($row_cooperado = mysql_fetch_array($result_cooperado)) {
			
			
			// CALCULO DAS COTAS PAGAS
			$query_quotas = mysql_query("SELECT SUM(quota) AS Soma_quota, COUNT(id_folha_pro) AS Count_quota FROM folha_cooperado WHERE quota != '0.00' AND id_autonomo = '$row_cooperado[id_autonomo]'");			
			$row_quota = mysql_fetch_assoc($query_quotas);
			
			?>
        
	    <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
		   <td><?=$row_cooperado['campo3']?></td>
		   <td><?=$row_cooperado['nome']?></td>
		   <td>R$ <?=$row_quota['Soma_quota']?></td>
		   <td><?=$row_quota['Count_quota']?></td>
		   <?php 
		   		$totalizador_quota += $row_quota['Soma_quota'];
		   ?>
	    </tr>
        
		<?php } ?>
        <tr>
        	<td>&nbsp</td>
        	<td align="right">Total</td>
        	<td>R$ <?=number_format($totalizador_quota,2,'.',',');?></td>
        	<td>&nbsp</td>	
        </tr>
        <tr class="secao">
          <td colspan="7" align="center">TOTAL DE COLABORADORES: <?php echo $num_cooperado; ?></td>
        </tr>
     </table>

    <?php } ?>
    
	</td>
  </tr>
</table>
</body>
</html>
<?php } ?>
