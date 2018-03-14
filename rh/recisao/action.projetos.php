<?php
if(empty($_COOKIE['logado'])) {
	print "<script>location.href = '../login.php?entre=true';</script>";
	exit;
}

include('../../conn.php');
include('../../funcoes.php');


if(isset($_GET['regiao'])){


$id_regiao = mysql_real_escape_string($_GET['regiao']);
$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$id_regiao'");


if(mysql_num_rows($qr_projeto) !=0) { echo "<option value='todos'> TODOS</option>";}
while($row_projeto = mysql_fetch_assoc($qr_projeto)):
?>

<option value="<?php echo $row_projeto['id_projeto'];?>"> <?php echo htmlentities($row_projeto['nome']); ?></option>

<?php
endwhile;
}
?>