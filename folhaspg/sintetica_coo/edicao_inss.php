<?php
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="login.php">Logar</a>';
	exit;
}

include('../../conn.php');
include('../../funcoes.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');

if(isset($_POST['update'])) {
	
	$cooperado = $_REQUEST['cooperado'];
	$inss 	   = $_REQUEST['inss'];
	$tipo_inss = $_REQUEST['tipo_inss'];

	mysql_query("UPDATE autonomo SET inss = '".formato_banco($inss)."', tipo_inss = '".$tipo_inss."' WHERE id_autonomo = '".$cooperado."' LIMIT 1"); ?>
	
	<script type="text/javascript">
		parent.window.location.reload();
	</script>
    
<?php exit(); }

// Descriptografando a Variável
list($cooperado,$folha,$id_folha_cooperado) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));

// Selecionando a Folha	
$qr_folha_participante  = mysql_query("SELECT * FROM folha_cooperado WHERE id_folha_pro = '$id_folha_cooperado' ORDER BY nome ASC");
$row_folha_participante = mysql_fetch_assoc($qr_folha_participante);

$qr_participante  = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$cooperado'");
$row_participante = mysql_fetch_assoc($qr_participante);

$qr_inss = mysql_query("SELECT faixa, fixo, percentual, piso, teto
                    FROM rh_movimentos
                    WHERE cod = '5024'
                    AND '{$row_folha_participante['data_inicio']}' BETWEEN data_ini AND data_fim");
$row_inss = mysql_fetch_array($qr_inss);
$percentual = 20;
?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
<link type="text/css" href="folha.css">
</head>
<body>
<div id="corpo">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form1">
<table cellspacing="0" cellpadding="0" class="folha">
  <tr class="secao">
    <td colspan="2" align="center"><?php echo $row_folha_participante['nome']?></td>
  </tr>
  <tr class="linha_um">
    <td align="right">Tipo Recolhimento</td>
    <td>
	  <select name="tipo_inss" id="tipo_inss">
        <option value="1" <?php if($row_participante['tipo_inss'] == '1') { echo 'selected'; } ?>>VALOR FIXO</option>
        <option value="2" <?php if($row_participante['tipo_inss'] != '1') { echo 'selected'; } ?>>VALOR PERCENTUAL</option>
      </select>
	</td> 
  </tr>
  <tr class="linha_dois">
    <td align="right">INSS</td>
    <td>
		<input name="inss" type="text" id="inss" size="4" maxlength="13" value="<?php echo $percentual; ?>"/> % (valor máximo <?php echo $percentual; ?>%)
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Concluir"></td>
  </tr>
</table>
<input type="hidden" name="cooperado" value="<?php echo $cooperado; ?>" id="id_participante">
<input type="hidden" name="folha" value="<?php echo $folha; ?>" id="folha">
<input type="hidden" name="id_folha_cooperado" value="<?php echo $id_folha_cooperado; ?>" id="participante">
<input type="hidden" name="update" value="1" id="update">
</form>

	<script>
    function INSS() {
        d = document.all;
        if(d.inss.value > 11 & d.tipo_inss.value == 2){
            alert("Atenção, Inss não pode passar de 11%");
            d.inss.value = '11';
            d.inn.focus();
        }
    }
    </script>

  </td>
 </tr>
</table>
</div>
</body>
</html>