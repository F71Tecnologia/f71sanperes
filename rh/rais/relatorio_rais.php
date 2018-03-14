<?php include('../../conn.php');
      include('../../classes/funcionario.php');

	$Fun = new funcionario();
	$Fun -> MostraUser(0);
	$Master   = $Fun -> id_master;
    $ano_base = '2010';
	$qr_projetos_ativos = mysql_query("SELECT * FROM projeto WHERE status_reg = '1'");
	while($row_projetos_ativos = mysql_fetch_assoc($qr_projetos_ativos)) {
		$projetos_ativos[] = $row_projetos_ativos['id_projeto'];
	}
	$projetos_ativos = implode(',',$projetos_ativos);
	$qr_empregado  = mysql_query("SELECT * FROM rh_clt WHERE year(data_entrada) <= '$ano_base' AND (year(data_saida) >= '$ano_base' OR data_saida = '0000-00-00' OR data_saida = NULL) AND id_regiao != '36' AND id_projeto IN($projetos_ativos) ORDER BY id_regiao, id_projeto ASC");
    $num_empregado = mysql_num_rows($qr_empregado); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" Content="text/html; charset=iso-8859-1">
<link href="../folha/sintetica/folha.css" rel="stylesheet" type="text/css">
<title>Rela&ccedil;&atilde;o de participantes para edi&ccedil;&atilde;o de cadastro - RAIS</title>
</head>
<body>
<div id="corpo">
<table cellspacing="4" cellpadding="0" id="topo" style="margin-top:10px;">
  <tr>
    <td width="15%" valign="middle" align="center">
        <img src="../../imagens/logomaster<?=$Master?>.gif" width="110" height="79">
    </td>
    <td style="font-size:12px; line-height:26px;">
        <b>Relação de participantes para edição de cadastro - RAIS <?php echo $ano_base; ?></b><br />
        <b>Total de Participantes:</b> <?php echo $num_empregado; ?>
    </td>
  </tr>
</table>
    
<table width="100%" cellpadding="0" cellspacing="1" id="folha">
  <tr class="secao">
    <td width="5%">&nbsp;</td>
    <td width="10%">ID</td>
    <td width="30%" style="padding-left:5px; text-align:left;">Nome</td>
    <td width="8%">Data de Entrada</td>
    <td width="8%">Data de Saída</td>
    <td width="18%">Regi&atilde;o</td>
    <td width="20%">Projeto</td>
  </tr>
	<?php while($row_empregado = mysql_fetch_assoc($qr_empregado)) {
			    $numeracao++;
				$qr_regiao  = mysql_query("SELECT regiao FROM regioes WHERE id_regiao  = '$row_empregado[id_regiao]'");
				$qr_projeto = mysql_query("SELECT nome   FROM projeto WHERE id_projeto = '$row_empregado[id_projeto]'");
			    echo '<tr class="linha_'; if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } echo '" style="text-transform:uppercase;">
				        <td>'.$numeracao.'</td>
						<td>'.$row_empregado['id_clt'].'</td>
					    <td style="padding-left:5px; text-align:left;"><a href="../alter_clt.php?clt='.$row_empregado['id_clt'].'&pro='.$row_empregado['id_projeto'].'" target="_blank"><img src="seta_transparente.png">'.$row_empregado['nome'].'</a></td>
						<td>'.implode('/', array_reverse(explode('-', $row_empregado['data_entrada']))).'</td>
						<td>'; if($row_empregado['data_saida'] == '0000-00-00') { echo 'EM ATIVIDADE'; } else { echo implode('/', array_reverse(explode('-', $row_empregado['data_saida']))); } echo '</td>
					    <td>'.@mysql_result($qr_regiao,0).'</td>
					    <td>'.@mysql_result($qr_projeto,0).'</td>
				      </tr>';
		  } ?>
   <tr class="totais">
    <td colspan="5">
	  <?php if($num_empregado > 10) { ?>
          <a href="#corpo" class="ancora">Subir ao topo</a>
      <?php } ?>
    </td>
    <td colspan="2" style="text-align:right;">Total de Participantes: <?php echo $num_empregado; ?></td>
  </tr>
</table>
</div>
</body>
</html>