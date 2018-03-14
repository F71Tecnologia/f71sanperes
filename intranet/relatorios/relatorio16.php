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

$result_autonomo = mysql_query("SELECT * FROM autonomo WHERE status = '1'  AND id_projeto = '$projeto' AND tipo_contratacao = '1' ORDER BY nome ASC");
$num_row = mysql_num_rows($result_autonomo);

$result_clt = mysql_query("SELECT * FROM rh_clt WHERE status = '10' AND id_projeto = '$projeto' ORDER BY nome ASC");
$num_clt = mysql_num_rows($result_clt);

$result_cooperado = mysql_query("SELECT * FROM autonomo WHERE status = '1' AND id_projeto = '$projeto' AND tipo_contratacao = '3' ORDER BY nome ASC");
$num_cooperado = mysql_num_rows($result_cooperado);

$result_pj = mysql_query("SELECT * FROM autonomo WHERE status = '1' AND id_projeto = '$projeto' AND tipo_contratacao = '4' ORDER BY nome ASC");
$num_pj = mysql_num_rows($result_pj);
?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>Relat&oacute;rio de Participantes com Acesso &agrave; TV Sorrindo</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#FFF; margin-top:30px; margin-bottom:30px;">
<table cellspacing="0" cellpadding="0" class="relacao" style="width:920px; border:0px; page-break-after:always;">
 <tr> 
    <td width="20%" align="center">
          <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
    </td>
    <td width="80%" align="center" colspan="2">
         <strong>RELAT&Oacute;RIO DE PARTICIPANTES DO PROJETO COM ACESSO &Agrave; TV SORRINDO</strong><br>
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
              <td align="center"><b></b></td>
            </tr>
        </table>
    </td>
  </tr>
  <tr> 
    <td colspan="3">
        <?php if(!empty($num_autonomo)) { ?>
 
      <div class="descricao">Relatório de Autonômos do Projeto com Acesso &agrave; TV Sorrindo</div>
      
   <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
    <tr class="secao">
      <td width="35%">Nome</td>  
      <td width="35%">Atividade</td>
      <td width="15%">CPF</td>
      <td width="15%">Senha TV</td>
    </tr>

<?php while($row_autonomo = mysql_fetch_array($result_autonomo)) {

		$result_atividade = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_autonomo[id_curso]'");
		$row_atividade = mysql_fetch_array($result_atividade);
		
		$result_tv = mysql_query("SELECT * FROM tvsorrindo WHERE id_bolsista = '$row_autonomo[id_bolsista]' AND id_projeto = '$projeto'");
		$row_tv = mysql_fetch_array($result_tv);
		$verifica_tv = mysql_num_rows($result_tv);
		
		if(!empty($verifica_tv)) {
?>

  <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
    <td><?=$row_autonomo['nome']?></td>
    <td><?=$row_atividade['nome']?></td>
    <td><?=$row_autonomo['cpf']?></td>
    <td><?=$row_tv['senha']?></td>
  </tr>
  
  <?php } } ?>
        
        <tr class="secao">
          <td colspan="7" align="center">TOTAL DE AUTÔNOMOS: <?php echo $verifica_tv; ?></td>
        </tr>
     </table>

    <?php } ?>
    
	</td>
  </tr>
  <tr> 
    <td colspan="3">
    <?php if(!empty($num_clt)) { ?>

      <div class="descricao">Relatório de CLTs do Projeto com Acesso &agrave; TV Sorrindo</div>

  <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
    <tr class="secao">
      <td width="35%">Nome</td>  
      <td width="35%">Atividade</td>
      <td width="15%">CPF</td>
      <td width="15%">Senha TV</td>
    </tr>

<?php while($row_clt = mysql_fetch_array($result_clt)){

		$result_atividade2 = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt[id_curso]'");
		$row_atividade2 = mysql_fetch_array($result_atividade2);
		
		$result_tv2 = mysql_query("SELECT * FROM tvsorrindo WHERE id_clt = '$row_clt[id_clt]' AND id_projeto = '$projeto'");
		$row_tv2 = mysql_fetch_array($result_tv2);
		$verifica_tv2 = mysql_num_rows($result_tv2);
		
		if(!empty($verifica_tv2)) {
?>

  <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
    <td><?=$row_clt['nome']?></td>
    <td><?=$row_atividade2['nome']?></td>
    <td><?=$row_clt['cpf']?></td>
    <td><?=$row_tv2['senha']?></td>
  </tr>
  
  <?php } } ?>
        
        <tr class="secao">
          <td colspan="7" align="center">TOTAL DE CLTs: <?php echo $verifica_tv2; ?></td>
        </tr>
     </table>

    <?php } ?>
    
	</td>
  </tr>
  <tr> 
    <td colspan="3">
    <?php if(!empty($num_cooperado)) { ?>

      <div class="descricao">Relatório de Colaboradores do Projeto com Acesso &agrave; TV Sorrindo</div>
      
  <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
    <tr class="secao">
      <td width="35%">Nome</td>  
      <td width="35%">Atividade</td>
      <td width="15%">CPF</td>
      <td width="15%">Senha TV</td>
    </tr>

<?php while($row_cooperado = mysql_fetch_array($result_cooperado)){

		$result_atividade3 = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_cooperado[id_curso]'");
		$row_atividade3 = mysql_fetch_array($result_atividade3);
		
		$result_tv3 = mysql_query("SELECT * FROM tvsorrindo WHERE id_bolsista = '$row_cooperado[id_bolsista]' AND id_projeto = '$projeto'");
		$row_tv3 = mysql_fetch_array($result_tv3);
		$verifica_tv3 = mysql_num_rows($result_tv3);
		
		if(!empty($verifica_tv3)) {
?>

  <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
    <td><?=$row_cooperado['nome']?></td>
    <td><?=$row_atividade3['nome']?></td>
    <td><?=$row_cooperado['cpf']?></td>
    <td><?=$row_tv3['senha']?></td>
  </tr>
  
  <?php } } ?>
        
        <tr class="secao">
          <td colspan="7" align="center">TOTAL DE COLABORADORES: <?php echo $verifica_tv3; ?></td>
        </tr>
     </table>

    <?php } ?>
  
    </td>
  </tr>
  <tr> 
    <td colspan="3">
    <?php if(!empty($num_pj)) { ?>

      <div class="descricao">Relatório de Autônomos / PJ do Projeto com Acesso &agrave; TV Sorrindo</div>
      
  <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
    <tr class="secao">
      <td width="35%">Nome</td>  
      <td width="35%">Atividade</td>
      <td width="15%">CPF</td>
      <td width="15%">Senha TV</td>
    </tr>

<?php while($row_pj = mysql_fetch_array($result_pj)){

		$result_atividade4 = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_pj[id_curso]'");
		$row_atividade4 = mysql_fetch_array($result_atividade4);
		
		$result_tv4 = mysql_query("SELECT * FROM tvsorrindo WHERE id_bolsista = '$row_pj[id_bolsista]' AND id_projeto = '$projeto'");
		$row_tv4 = mysql_fetch_array($result_tv4);
		$verifica_tv4 = mysql_num_rows($result_tv4);
		
		if(!empty($verifica_tv4)) {
?>

  <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
    <td><?=$row_pj['nome']?></td>
    <td><?=$row_atividade4['nome']?></td>
    <td><?=$row_pj['cpf']?></td>
    <td><?=$row_tv4['senha']?></td>
  </tr>
  
  <?php } } ?>
        
        <tr class="secao">
          <td colspan="7" align="center">TOTAL DE AUTÔNOMOS / PJ: <?php echo $verifica_tv4; ?></td>
        </tr>
     </table>

    <?php } ?>
  
 </table>
</td>
</tr>
</table>
</body>
</html>
<?php } ?>