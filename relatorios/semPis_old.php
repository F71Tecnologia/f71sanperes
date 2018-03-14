<?php
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='../login.php'>Logar</a>";
exit;
}

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

$result_autonomo = mysql_query("SELECT *, date_format(dada_pis, '%d/%m/%Y') as dada_pis FROM autonomo WHERE tipo_contratacao = '1' AND pis in(0,'') AND status = '1' AND id_regiao = '$regiao' AND id_projeto = '$projeto' ORDER BY nome ASC");
$num_autonomo = mysql_num_rows($result_autonomo);

$result_clt = mysql_query("SELECT *, date_format(dada_pis, '%d/%m/%Y') as dada_pis FROM rh_clt WHERE tipo_contratacao = '2'  AND pis in(0,'') AND id_regiao = '$regiao' AND id_projeto = '$projeto' ORDER BY nome ASC");
$num_clt = mysql_num_rows($result_clt);

$result_cooperado = mysql_query("SELECT *, date_format(dada_pis, '%d/%m/%Y') as dada_pis FROM autonomo WHERE tipo_contratacao = '3'  AND id_regiao = '$regiao' AND id_projeto = '$projeto' ORDER BY nome ASC");
$num_cooperado = mysql_num_rows($result_cooperado);

$result_pj = mysql_query("SELECT *, date_format(dada_pis, '%d/%m/%Y') as dada_pis FROM autonomo WHERE tipo_contratacao = '4' AND status = '1' AND id_regiao = '$regiao' AND id_projeto = '$projeto' ORDER BY nome ASC");
$num_pj = mysql_num_rows($result_pj);
?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>Relat&oacute;rio de Participantes do Projeto Sem PIS</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#FFF; margin-top:30px; margin-bottom:30px;">
<table cellspacing="0" cellpadding="0" class="relacao" style="width:920px; border:0px; page-break-after:always;">
 <tr> 
    <td width="20%" align="center">
          <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
    </td>
    <td width="80%" align="center" colspan="2">
         <strong>RELAT&Oacute;RIO DE PARTICIPANTES DO PROJETO SEM PIS</strong><br>
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
        <form action="gravaPis.php" method="POST">
       <?php if(!empty($num_autonomo)) { ?>
   <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
           <tr>
               <th class="descricao" colspan="5">Relatório de Autonômos</th>
           </tr>
        <tr class="secao">
                        
			<td width="38%">Nome</td>
			<td width="12%">PIS</td>
			<td width="10%">Data</td>
			<td width="35%">Loca&ccedil;&atilde;o</td>
                        <td width="35%">Arquivo</td>
        </tr>
		<?php while ($row_autonomo = mysql_fetch_array($result_autonomo)) { ?>
                           <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
				<td><?=$row_autonomo['nome']?><input type="hidden" name="idaut[]" value="<?php echo $row_autonomo['id_autonomo']; ?>" /></td>
                                <td><input type="text" name="pisAut[<?php echo $row_autonomo['id_autonomo']?>]]"/></td>
                                <td><input type="date" name="dataPisAut[<?php echo $row_autonomo['id_autonomo']?>]]"/></td>
				<td><?=$row_autonomo['locacao']?></td>
                                <td><a href="../rh/solicitacaoPisAut.php?pro=<?php echo $projeto ?>&id_reg=<?php echo $regiao; ?>&aut=<?php echo $row_autonomo['id_autonomo']; ?>" target="_blank">
                                    <img src="icones/icon-doc.gif"/>
                                    </a></td>
			   </tr>	
		<?php } ?>
        <tr class="secao">
          <td colspan="7" align="left">TOTAL DE AUTÔNOMOS: <?php echo $num_autonomo; ?></td>
        </tr>
    <?php } ?>
        
    <?php if(!empty($num_clt)) { ?>
     
            <tr>
               <th class="descricao" colspan="5">Relatório de CLTs</th>
           </tr>
            <tr class="secao">
                    <td width="38%">Nome</th>
                    <td width="12%">PIS</th>
                    <td width="10%">Data</th>
                    <td width="35%">Loca&ccedil;&atilde;o</th>
                    <td width="35%">Arquivo</td>    
            </tr>
		<?php while($row_clt = mysql_fetch_array($result_clt)) { ?>
        
	    <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
		   <td><?=$row_clt['nome']?><input type="hidden" name="idclt[]" value="<?php echo $row_clt['id_clt']; ?>" /></td>
                   <td><input type="text" name="pisClt[<?php echo $row_clt['id_clt']?>]]"/></td>
                   <td><input type="date" name="dataPisClt[<?php echo $row_clt['id_clt']?>]]"/></td>
		   <td><?=$row_clt['locacao']?></td>
                   <td><a href="../rh/solicitacaopis.php?pro=<?php echo $projeto ?>&id_reg=<?php echo $regiao; ?>&clt=<?php echo $row_clt['id_clt']; ?>" target="_blank">
                           <img src="icones/icon-doc.gif"/>
                       </a></td>
	    </tr>
        
		<?php } ?>
        
        <tr class="secao">
          <td colspan="7" align="left">TOTAL DE CLTs: <?php echo $num_clt; ?></td>
        </tr>
     </table>

    <?php } ?>
            <input type="hidden" name="regiao" value="<?php echo $regiao; ?>" />
            <input type="hidden" name="projeto" value="<?php echo $projeto; ?>" />
            <input type="submit" value="Gravar"/>
            </form>
	</td>
  </tr>
  <tr> 
    <td colspan="3">
    <?php if(!empty($num_cooperado)) { ?>

    <div class="descricao">Relatório de Colaboradores do Projeto por PIS</div>
      
      <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
            <tr class="secao">
                    <td width="5%">Código</th>
                    <td width="38%">Nome</th>
                    <td width="12%">PIS</th>
                    <td width="10%">Data</th>
                    <td width="35%">Loca&ccedil;&atilde;o</th>
            </tr>
		<?php while($row_cooperado = mysql_fetch_array($result_cooperado)) { ?>
        
	    <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
		   <td><?=$row_cooperado['campo3']?></td>
		   <td><?=$row_cooperado['nome']?></td>
		   <td><?php if(!empty($row_cooperado['pis'])) { echo $row_cooperado['pis']; } else { echo "NÃO TEM"; } ?></td>
		   <td><?=$row_cooperado['dada_pis']?></td>
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
 
      <div class="descricao">Relatório de Autônomo / PJ do Projeto por PIS</div>
      
      <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
    <tr class="secao">
			<td width="5%">Código</th>
			<td width="38%">Nome</th>
			<td width="12%">PIS</th>
			<td width="10%">Data</th>
			<td width="35%">Loca&ccedil;&atilde;o</th>
        </tr>
		<?php while($row_pj = mysql_fetch_array($result_pj)) { ?>
        
	    <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
		   <td><?=$row_pj['campo3']?></td>
		   <td><?=$row_pj['nome']?></td>
		   <td><?php if(!empty($row_pj['pis'])) { echo $row_pj['pis']; } else { echo "NÃO TEM"; } ?></td>
		   <td><?=$row_pj['dada_pis']?></td>
		   <td><?=$row_pj['locacao']?></td>
	    </tr>
        
		<?php } ?>
        
        <tr class="secao">
          <td colspan="7" align="center">TOTAL DE AUTÔNOMOS / PJ: <?php echo $num_pj; ?></td>
        </tr>
     </table>

    <?php } ?>
	</td>
  </tr>
</table>
</body>
</html>
