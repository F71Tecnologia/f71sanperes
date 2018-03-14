<?php
if(empty($_COOKIE['logado'])) {
	print "Efetue o Login<br><a href='../login.php'>Logar</a>";
	exit;
} else {

include('../conn.php');

$id_user       = $_COOKIE['logado'];
$result_user   = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user      = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master    = mysql_fetch_array($result_master);

$projeto = $_REQUEST['pro'];
$regiao  = $_REQUEST['reg'];

$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_projeto    = mysql_fetch_array($result_projeto);

$data_hoje = date('d/m/Y');

$result_bolsista = mysql_query("SELECT *,date_format(data_saida, '%d/%m/%Y')as data_saida FROM autonomo WHERE status != '1' AND tipo_contratacao = '1' AND id_projeto = '$projeto' ORDER BY year(data_saida),month(data_saida),day(data_saida) DESC");
$num_bolsista    = mysql_num_rows($result_bolsista);

$result_clt = mysql_query("SELECT *,date_format(data_demi, '%d/%m/%Y')as data_saida FROM rh_clt WHERE status >= '60' AND tipo_contratacao = '2' AND id_projeto = '$projeto' ORDER BY year(data_demi),month(data_demi),day(data_demi) DESC");
$num_clt    = mysql_num_rows($result_clt);

$result_cooperado = mysql_query("SELECT *,date_format(data_saida, '%d/%m/%Y')as data_saida FROM autonomo WHERE status != '1' AND tipo_contratacao = '3' AND id_projeto = '$projeto' ORDER BY year(data_saida),month(data_saida),day(data_saida) DESC");
$num_cooperado    = mysql_num_rows($result_cooperado);

$result_pj = mysql_query("SELECT *,date_format(data_saida, '%d/%m/%Y')as data_saida FROM autonomo WHERE status != '1' AND tipo_contratacao = '4' AND id_projeto = '$projeto' ORDER BY year(data_saida),month(data_saida),day(data_saida) DESC");
$num_pj    = mysql_num_rows($result_pj);
?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>Relat&oacute;rio de Participantes Desligados do Projeto em Ordem Alfabética</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#FFF; margin-top:30px; margin-bottom:30px;">
<table cellspacing="0" cellpadding="0" class="relacao" style="width:920px; border:0px; page-break-after:always;">
 <tr> 
    <td width="20%" align="center">
          <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
    </td>
    <td width="80%" align="center" colspan="2">
         <strong>RELAT&Oacute;RIO DE PARTICIPANTES DESLIGADOS DO PROJETO EM ORDEM ALFAB&Eacute;TICA</strong><br>
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
              <td align="center"><b><?php echo $num_clt+$num_cooperado+$num_bolsista+$num_pj; ?></b></td>
            </tr>
        </table>
    </td>
  </tr>
  <tr> 
    <td colspan="3">
    
	<?php if(!empty($num_bolsista)) {   
            /*
            
            ?>
 
      <div class="descricao">Relat&oacute;rio de Autonômos Desligados do Projeto em Ordem Alfabética</div>
      <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
         <tr class="secao">
             <td>Nome</td>  
             <td>Atividade</td>
             <td>Salário</td>
             <td>CPF</td>
             <td>RG</td>
             <td>PIS</td>
             <td>Banco</td>
             <td>Agência</td>
             <td>C.C.</td>
             <td>Data Saída</td>
        </tr>

      <?php while($row_bolsista = mysql_fetch_array($result_bolsista)) {

           $result_atividade = mysql_query("SELECT * FROM curso where id_curso = '$row_bolsista[id_curso]'");
           $row_atividade = mysql_fetch_array($result_atividade);
           $result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$row_bolsista[banco]'");
           $row_banco = mysql_fetch_array($result_banco); ?>

      <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
        <td><?=$row_bolsista['nome']?></td>
        <td><?=$row_atividade['nome']?></td>
        <td><?=$row_atividade['valor']?></td>
        <td><?=$row_bolsista['cpf']?></td>
        <td><?=$row_bolsista['rg']?></td>
        <td><?=$row_bolsista['pis']?></td>
        <td><?=$row_banco['nome']?></td>
        <td><?=$row_bolsista['agencia']?></td>
        <td><?=$row_bolsista['conta']?></td>
        <td><?=$row_bolsista['data_saida']?></td>
      </tr>
      
     <?php } ?>
     
     <tr class="secao">
        <td colspan="10" align="center">TOTAL DE AUTÔNOMOS: <?php echo $num_bolsista; ?></td>
      </tr>
  </table>
  
     <?php */ }  ?>

    </td>
  </tr>
   <tr>
     <td colspan="3">
    
    <?php if(!empty($num_clt)) { ?>

      <div class="descricao">Relat&oacute;rio de CLTs Desligados do Projeto em Ordem Alfabética</div>
    <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
      <tr class="secao">
        <td>Nome</td>
        <td>Atividade</td>
        <td>Salário</td>
        <td>CPF</td>
        <td>RG</td>
        <td>PIS</td>
        <td>Banco</td>
        <td>Agência</td>
        <td>C.C.</td>
        <td>Data Saída</td>
      </tr>

	    <?php while($row_clt = mysql_fetch_array($result_clt)) {
			
			$result_atividade2 = mysql_query("SELECT * FROM curso where id_curso = '$row_clt[id_curso]'");
			$row_atividade2 = mysql_fetch_array($result_atividade2);
			$result_banco2 = mysql_query("SELECT * FROM bancos where id_banco = '$row_clt[banco]'");
			$row_banco2 = mysql_fetch_array($result_banco2); ?>
            
      <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
        <td><?=$row_clt['nome']?></td>
        <td><?=$row_atividade2['nome']?></td>
        <td><?=$row_atividade2['valor']?></td>
        <td><?=$row_clt['cpf']?></td>
        <td><?=$row_clt['rg']?></td>
        <td><?=$row_clt['pis']?></td>
        <td><?=$row_banco2['nome']?></td>
        <td><?=$row_clt['agencia']?></td>
        <td><?=$row_clt['conta']?></td>
        <td><?=$row_clt['data_saida']?></td>
      </tr>
	  
<?php } ?>

       <tr class="secao">
        <td colspan="10" align="center">TOTAL DE CLTS: <?php echo $num_clt; ?></td>
      </tr>
</table>

<?php } ?>

  </td>
   </tr>
   <tr>
  <td colspan="3">
  
  <?php if(!empty($num_cooperado)) { ?>
  
      <div class="descricao">Relat&oacute;rio de Colaboradores Desligados do Projeto em Ordem Alfabética</div>
    <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
      <tr class="secao">
        <td>Nome</td>  
        <td>Atividade</td>
        <td>Salário</td>
        <td>CPF</td>
        <td>RG</td>
        <td>PIS</td>
        <td>Banco</td>
        <td>Agência</td>
        <td>C.C.</td>
        <td>Data Saída</td>
      </tr>
      
		<?php while($row_cooperado = mysql_fetch_array($result_cooperado)) {
			
        $result_atividade3 = mysql_query("SELECT * FROM curso where id_curso = '$row_cooperado[id_curso]'");
        $row_atividade3 = mysql_fetch_array($result_atividade3);
        $result_banco3 = mysql_query("SELECT * FROM bancos where id_banco = '$row_cooperado[banco]'");
        $row_banco3 = mysql_fetch_array($result_banco3); ?>

          <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
            <td><?=$row_cooperado['nome']?></td>
            <td><?=$row_atividade3['nome']?></td>
            <td><?=$row_atividade3['valor']?></td>
            <td><?=$row_cooperado['cpf']?></td>
            <td><?=$row_cooperado['rg']?></td>
            <td><?=$row_cooperado['pis']?></td>
            <td><?=$row_banco3['nome']?> <?=$row_cooperado['nome_banco']?></td>
            <td><?=$row_cooperado['agencia']?></td>
            <td><?=$row_cooperado['conta']?></td>
            <td><?=$row_cooperado['data_saida']?></td>
          </tr>
         
      <?php } ?>
      
      <tr class="secao">
        <td colspan="10" align="center">TOTAL DE COLABORADORES: <?php echo $num_cooperado; ?></td>
      </tr>
   </table>
   
   <?php } ?>
   
   </td>
   </tr>
   <tr>
  <td colspan="3">
  
  <?php if(!empty($num_pj)) { ?>

      <div class="descricao">Relat&oacute;rio de Autônomos / PJ Desligados do Projeto em Ordem Alfabética</div>
    <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
      <tr class="secao">
        <td>Nome</td>  
        <td>Atividade</td>
        <td>Salário</td>
        <td>CPF</td>
        <td>RG</td>
        <td>PIS</td>
        <td>Banco</td>
        <td>Agência</td>
        <td>C.C.</td>
        <td>Data Saída</td>
      </tr>
      
		<?php while($row_pj = mysql_fetch_array($result_pj)) {
			
        $result_atividade4 = mysql_query("SELECT * FROM curso where id_curso = '$row_pj[id_curso]'");
        $row_atividade4 = mysql_fetch_array($result_atividade4);
        $result_banco4 = mysql_query("SELECT * FROM bancos where id_banco = '$row_pj[banco]'");
        $row_banco4 = mysql_fetch_array($result_banco4); ?>

          <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
            <td><?=$row_pj['nome']?></td>
            <td><?=$row_atividade4['nome']?></td>
            <td><?=$row_atividade4['valor']?></td>
            <td><?=$row_pj['cpf']?></td>
            <td><?=$row_pj['rg']?></td>
            <td><?=$row_pj['pis']?></td>
            <td><?=$row_banco4['nome']?> <?=$row_pj['nome_banco']?></td>
            <td><?=$row_pj['agencia']?></td>
            <td><?=$row_pj['conta']?></td>
            <td><?=$row_pj['data_saida']?></td>
          </tr>
         
      <?php } ?>
      
      <tr class="secao">
        <td colspan="10" align="center">TOTAL DE AUTÔNOMOS / PJ: <?php echo $num_pj; ?></td>
      </tr>
   </table>
   
   <?php } ?>
  </td>
  </tr>
  </table>
  </body>
</html>
<?php } ?>