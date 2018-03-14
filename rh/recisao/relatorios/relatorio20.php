<?
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

$result_autonomo = mysql_query("SELECT campo3, nome, data_nasci,(YEAR(CURDATE())-YEAR(data_nasci)) - (RIGHT(CURDATE(),5)<RIGHT(data_nasci,5)) AS idade, locacao, date_format(data_nasci, '%d/%m/%Y') AS data_nasci FROM autonomo WHERE tipo_contratacao = '1' AND status = '1' AND id_regiao = '$regiao' AND id_projeto = '$projeto' ORDER BY idade DESC");
$num_autonomo = mysql_num_rows($result_autonomo);

$result_clt = mysql_query("SELECT campo3, nome, data_nasci,(YEAR(CURDATE())-YEAR(data_nasci)) - (RIGHT(CURDATE(),5)<RIGHT(data_nasci,5)) AS idade, locacao, date_format(data_nasci, '%d/%m/%Y') AS data_nasci FROM rh_clt WHERE tipo_contratacao = '2' AND status = '10' AND id_regiao = '$regiao' AND id_projeto = '$projeto' ORDER BY idade DESC");
$num_clt = mysql_num_rows($result_clt);

$result_cooperado = mysql_query("SELECT campo3, nome, data_nasci,(YEAR(CURDATE())-YEAR(data_nasci)) - (RIGHT(CURDATE(),5)<RIGHT(data_nasci,5)) AS idade, locacao, date_format(data_nasci, '%d/%m/%Y') AS data_nasci FROM autonomo WHERE tipo_contratacao = '3' AND status = '1' AND id_regiao = '$regiao' AND id_projeto = '$projeto' ORDER BY idade DESC");
$num_cooperado = mysql_num_rows($result_cooperado);

$result_pj = mysql_query("SELECT campo3, nome, data_nasci,(YEAR(CURDATE())-YEAR(data_nasci)) - (RIGHT(CURDATE(),5)<RIGHT(data_nasci,5)) AS idade, locacao, date_format(data_nasci, '%d/%m/%Y') AS data_nasci FROM autonomo WHERE tipo_contratacao = '4' AND status = '1' AND id_regiao = '$regiao' AND id_projeto = '$projeto' ORDER BY idade DESC");
$num_pj = mysql_num_rows($result_pj);
?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>Relat&oacute;rio de Participantes do Projeto por Idade</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#FFF; margin-top:30px; margin-bottom:30px;">
<table cellspacing="0" cellpadding="0" class="relacao" style="width:920px; border:0px; page-break-after:always;">
 <tr> 
    <td width="20%" align="center">
          <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
    </td>
    <td width="80%" align="center" colspan="2">
         <strong>RELAT&Oacute;RIO DE PARTICIPANTES DO PROJETO POR IDADE</strong><br>
         <?=$row_master['razao']?>
         <table width="500" border="0" align="center" cellpadding="4" cellspacing="1" style="font-size:12px;">
            <tr style="color:#FFF;">
              <td width="150" height="22" class="top">PROJETO</td>
              <td width="150" class="top">REGIÃO</td>
              <td width="200" class="top">TOTAL DE PARTICIPANTES</td>
            </tr>
            <tr style="color:#333; background-color:#efefef;">
              <td height="20" align="center"><b><?=$row_projeto['nome']?></b></td>
              <td align="center"><b><?=$row_projeto['regiao']?></b></td>
              <td align="center"><b><?php echo $num_clt+$num_cooperado+$num_autonomo+$num_pj; ?></b></td>
            </tr>
        </table>
    </td>
  </tr>
  <tr> 
    <td colspan="3">
       <?php if(!empty($num_autonomo)) { ?>
 
      <div class="descricao">Relatório de Autonômos do Projeto por Idade</div>
      
   <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
    <tr class="secao">
			<td width="5%">Código</td>
			<td width="40%">Nome</td>
			<td width="5%">Idade</td>
			<td width="10%">Nascimento</td>
			<td width="40%">Loca&ccedil;&atilde;o</td>
        </tr>
		<?php while($row_autonomo = mysql_fetch_array($result_autonomo)) { ?>
        
	    <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
		   <td><?=$row_autonomo['campo3']?></td>
		   <td><?=$row_autonomo['nome']?></td>
		   <td><?=$row_autonomo['idade']?></td>
		   <td><?=$row_autonomo['data_nasci']?></td>
		   <td><?=$row_autonomo['locacao']?></td>
	    </tr>
        
		<?php } ?>
        
        <tr class="secao">
          <td colspan="7" align="center">TOTAL DE AUTÔNOMOS: <?php echo $num_autonomo; ?></td>
        </tr>
     </table>

    <?php } ?>
    
	</td>
  </tr>
  <tr> 
    <td colspan="3">
    <?php if(!empty($num_clt)) { ?>

      <div class="descricao">Relatório de CLTs do Projeto por Idade</div>
      
      <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
    <tr class="secao">
			<td width="5%">Código</td>
			<td width="40%">Nome</td>
			<td width="5%">Idade</td>
			<td width="10%">Nascimento</td>
			<td width="40%">Loca&ccedil;&atilde;o</td>
        </tr>
		<?php while($row_clt = mysql_fetch_array($result_clt)) { ?>
        
	    <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
		   <td><?=$row_clt['campo3']?></td>
		   <td><?=$row_clt['nome']?></td>
		   <td><?=$row_clt['idade']?></td>
		   <td><?=$row_clt['data_nasci']?></td>
		   <td><?=$row_clt['locacao']?></td>
	    </tr>
        
		<?php } ?>
        
        <tr class="secao">
          <td colspan="7" align="center">TOTAL DE CLTs: <?php echo $num_clt; ?></td>
        </tr>
     </table>

    <?php } ?>
    
	</td>
  </tr>
  <tr> 
    <td colspan="3">
    <?php if(!empty($num_cooperado)) { ?>

      <div class="descricao">Relatório de Colaboradores do Projeto por Idade</div>
      
      <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
    <tr class="secao">
			<td width="5%">Código</td>
			<td width="40%">Nome</td>
			<td width="5%">Idade</td>
			<td width="10%">Nascimento</td>
			<td width="40%">Loca&ccedil;&atilde;o</td>
        </tr>
		<?php while($row_cooperado = mysql_fetch_array($result_cooperado)) { ?>
        
	    <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
		   <td><?=$row_cooperado['campo3']?></td>
		   <td><?=$row_cooperado['nome']?></td>
		   <td><?=$row_cooperado['idade']?></td>
		   <td><?=$row_cooperado['data_nasci']?></td>
		   <td><?=$row_cooperado['locacao']?></td>
	    </tr>
        
		<?php } ?>
        
        <tr class="secao">
          <td colspan="7" align="center">TOTAL DE COLABORADORES: <?php echo $num_cooperado; ?></td>
        </tr>
     </table>

    <?php } ?>
    
    </td>
  </tr>
  <tr> 
    <td colspan="3">
    <?php if(!empty($num_pj)) { ?>
 
      <div class="descricao">Relatório de Autônomos / PJ do Projeto por Idade</div>
      
      <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
    <tr class="secao">
			<td width="5%">Código</td>
			<td width="40%">Nome</td>
			<td width="5%">Idade</td>
			<td width="10%">Nascimento</td>
			<td width="40%">Loca&ccedil;&atilde;o</td>
        </tr>
		<?php while($row_pj = mysql_fetch_array($result_pj)) { ?>
        
	    <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
		   <td><?=$row_pj['campo3']?></td>
		   <td><?=$row_pj['nome']?></td>
		   <td><?=$row_pj['idade']?></td>
		   <td><?=$row_pj['data_nasci']?></td>
		   <td><?=$row_pj['locacao']?></td>
	    </tr>
        
		<?php } ?>
        
        <tr class="secao">
          <td colspan="7" align="center">TOTAL DE AUTÔNOMO / PJ: <?php echo $num_pj; ?></td>
        </tr>
     </table>

    <?php } ?>
    
	</td>
  </tr>
</table>
</body>
</html>
<?php } ?>