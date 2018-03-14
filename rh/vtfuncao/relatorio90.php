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

$result = mysql_query("SELECT nome, locacao, endereco, bairro FROM rh_clt WHERE status = '10' AND id_regiao = '$regiao' AND id_projeto = '$projeto' UNION SELECT nome, locacao, endereco, bairro FROM autonomo WHERE status = '1' AND id_regiao = '$regiao' AND id_projeto = '$projeto' ORDER BY nome ASC");
$num = mysql_num_rows($result);
?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>Relat&oacute;rio de Participantes do Projeto por Endere&ccedil;o</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#FFF; margin-top:30px; margin-bottom:30px;">
<table cellspacing="0" cellpadding="0" class="relacao" style="width:920px; border:0px; page-break-after:always;">
 <tr> 
    <td width="20%" align="center">
          <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
    </td>
    <td width="80%" align="center" colspan="2">
         <strong>RELAT&Oacute;RIO DE PARTICIPANTES DO PROJETO POR ENDERE&Ccedil;O</strong><br>
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
              <td align="center"><b><?=$num?></b></td>
            </tr>
        </table>
    </td>
  </tr>
  <tr> 
    <td colspan="3">
    
	<?php if(!empty($num)) { ?>
   
      <div class="descricao">Relat&oacute;rio de Participantes do Projeto por Endere&ccedil;o</div> 
      <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
         <tr class="secao">
             <td width="34%">Nome</td>  
             <td width="33%">Endere&ccedil;o</td>  
             <td width="33%">Unidade</td>
        </tr>

      <?php while($row = mysql_fetch_array($result)) {
				
			$Escola = str_replace("ESCOLA ","E. ",$row['locacao']);
			$Escola = str_replace("MUNICIPAL ","M. ",$Escola);
			$Escola = str_replace("MUNICIPALIZADA ","M. ",$Escola); ?>

      <tr bgcolor="<?php if($alternateColor++%2==0) { echo "#FAFAFA"; } else { echo "#F3F3F3"; } ?>" style="font-weight:normal; padding:4px;">
        <td><?=$row['nome']?></td>
        <td><?php echo ', '.$row['bairro']; ?></td>
        <td><?=$Escola?></td> 
      </tr>
      
     <?php } ?>
     
     <tr class="secao">
        <td colspan="10" align="center">TOTAL DE PARTICIPANTES: <?php echo $num; ?></td>
     </tr>
  </table>
  
     <?php } ?>

    </td>
  </tr>
</table>
</body>
</html>
<?php } ?>