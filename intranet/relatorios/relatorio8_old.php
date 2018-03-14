<?php
if(empty($_COOKIE['logado'])){
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

$result_abolsista = mysql_query("SELECT * FROM autonomo WHERE medica = '1' AND tipo_contratacao = '1' AND status = '1' AND id_regiao = '$regiao' ORDER BY plano, nome");	
$num_row = mysql_num_rows($result_abolsista);

$result_abolsista2 = mysql_query("SELECT * FROM rh_clt WHERE medica = '1' AND tipo_contratacao = '2' AND status = '10' AND id_regiao = '$regiao' ORDER BY plano, nome");
$num_row2 = mysql_num_rows($result_abolsista2);

$result_abolsista3 = mysql_query("SELECT * FROM autonomo WHERE medica = '1' AND tipo_contratacao = '3' AND status = '1' AND id_regiao = '$regiao' ORDER BY plano, nome");
$row = mysql_fetch_array($result_abolsista3);
?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>Relat&oacute;rio de Participantes com Assistência Médica</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#FFF; margin-top:30px; margin-bottom:30px;">
<table cellspacing="0" cellpadding="0" class="relacao" style="width:720px; border:0px; page-break-after:always;">
 <tr> 
    <td width="20%" align="center">
          <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
    </td>
    <td width="80%" align="center" colspan="2">
         <strong>RELAT&Oacute;RIO DE PARTICIPANTES COM ASSIST&Ecirc;NCIA M&Eacute;DICA</strong><br>
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
              <td align="center"><b><?php echo $num_row+$num_row2; ?></b></td>
            </tr>
        </table>
    </td>
  </tr>
  <tr> 
    <td colspan="3">
          <div class="descricao">
          TOTAL DE AUTÔNOMO: <?php echo $num_row; ?><br>
          TOTAL DE CLT: <?php echo $num_row2; ?>
          </div>
   <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
     <tr class="secao">
       <td>Nome</td>  
       <td>Identidade</td>  
       <td>CPF</td>
       <td>Tipo de Plano</td>
       <td>Tipo de Funcionário</td>
     </tr>

        <?php $cont = "0";
              while($row_abolsista = mysql_fetch_array($result_abolsista)) {

				$result_bolsista = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$row_abolsista[0]'");
				$row_bolsista = mysql_fetch_array($result_bolsista);
				$tipoFuncionario = "AUTÔNOMO";

				if($row_bolsista['plano'] == '1') {
				     $plano = "Familiar";
				} elseif($row_bolsista['plano'] == '2') {
				     $plano = "Individual";
				} else {
				     $plano = "<font color=red>Nenhum</font>";
				} ?>

    <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
      <td><?=$row_bolsista['nome']?></td>
      <td><?=$row_bolsista['rg']?></td>
      <td><?=$row_bolsista['cpf']?></td>
      <td><?=$plano?></td>
      <td><?=$tipoFuncionario?></td>
    </tr>

      <?php $cont ++; }
            
			//Bloco de código dos CLT
            $cont = "0";
            while($row_abolsista = mysql_fetch_array($result_abolsista2)){

			  $result_bolsista = mysql_query("SELECT * FROM rh_clt where id_clt = '$row_abolsista[0]'");
			  $row_bolsista = mysql_fetch_array($result_bolsista);
			  $tipoFuncionario = "CLT";
			
			  if($row_bolsista['plano'] == '1') {
			       $plano = "Familiar";
			  } elseif($row_bolsista['plano'] == '2') {
			       $plano = "Individual";
			  } else {
			       $plano = "<font color=red>Nenhum</font>";
			  } ?>
              
    <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
      <td><?=$row_bolsista['nome']?></td>
      <td><?=$row_bolsista['rg']?></td>
      <td><?=$row_bolsista['cpf']?></td>
      <td><?=$plano?></td>
      <td><?=$tipoFuncionario?></td>
    </tr>

            <?php $cont ++;	} ?>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
<?php } ?>