<?php
if(empty($_COOKIE['logado'])){
	print 'Efetue o Login<br><a href="../login.php">Logar</a>';
	exit;
}

include('../conn.php');

$id_user     = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user    = mysql_fetch_array($result_user);
$id_regiao   = $_REQUEST['regiao'];
$projeto     = $_REQUEST['pro'];
$REPro       = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$RowPro      = mysql_fetch_array($REPro);
$tipo_contratacao = $_GET['tipo'];


// Bloqueio Administração
echo bloqueio_administracao($id_regiao);

include('../classes/regiao.php');
$REG = new regiao();
$resut_maior = mysql_query ("SELECT CAST(campo3 AS UNSIGNED) campo3, MAX(campo3) FROM terceirizado WHERE id_regiao= '$id_regiao' AND id_projeto ='$projeto' AND campo3 != 'INSERIR' GROUP BY campo3 DESC LIMIT 0,1");
$row_maior = mysql_fetch_array ($resut_maior); 
$codigo = $row_maior[0] + 1;
$codigo = sprintf("%04d",$codigo);

$sql = "select count(t.id_terceirizado) as total from terceirizado t inner join curso c on t.id_curso = c.id_curso
	where t.id_regiao = {$id_regiao} and t.id_projeto = {$projeto};";
$res = mysql_query($sql);
$row = mysql_fetch_row($res);

$tot = $row[0];

$pag = $_REQUEST['pag'] > 0? $_REQUEST['pag']:0;
$pag = $pag > ($tot)? ($tot - 8): $pag;

$sql = "select t.id_terceirizado, t.nome, t.foto, c.nome as funcao from terceirizado t inner join curso c on t.id_curso = c.id_curso
	where t.id_regiao = {$id_regiao} and t.id_projeto = {$projeto} limit 8 offset {$pag};";
$res = mysql_query($sql);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>:: Intranet ::</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'/>
<link rel="shortcut icon" href="../favicon.ico"/>
<link href="../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css"/>
<script language="javascript" src="../js/ramon.js"></script>
<link href="../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
<link href="../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../js/jquery.ui.core.js"></script>
<script type="text/javascript" src="../js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../js/jquery.ui.datepicker-pt-BR.js"></script>
<script type="text/javascript" src="../js/valida_documento.js"></script>

<style>
div#trescol {column-count:3; -moz-column-count:3; column-gap:15px; -moz-column-gap:15px; column-rule:0px solid #fc0; -moz-column-rule:0px solid #fc0; -webkit-column-count:3; -webkit-column-gap:15px; -webkit-column-rule:0px solid #fc0;}
div#duascol {column-count:2; -moz-column-count:2; column-gap:15px; -moz-column-gap:15px; column-rule:0px solid #fc0; -moz-column-rule:0px solid #fc0; -webkit-column-count:2; -webkit-column-gap:15px; -webkit-column-rule:0px solid #fc0;}
</style>
</head>
<body>
<center>
    <div style="width: 700px;">
	<div><br /></div>
	<div id="duascol">
	    <?php while($row = mysql_fetch_array($res)): ?>
		<table align="center" width="250px" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">

		    <tr>
			<td style="text-align: center">
			    <img src="<?=$row['foto']?>" style="width: 100px; height: 130px" />
			</td>
		    </tr>
		    <tr>
			<td style="text-align: center">
			    <?=$row['nome']?>
			</td>
		    </tr>
		    <tr>
			<td style="text-align: center">
			    <?=$row['funcao']?>
			</td>
		    </tr>
		    <tr>
			<td style="text-align: center">
			    <?=gera_barras("*4000".$row['id_terceirizado']."*")?>
			</td>
		    </tr>

		</table><br/>
	    <?php endwhile; ?>
	</div>
	<div>
	    <table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
		<tr>
		    <td style="text-align: center;">
			<a href="viewcracha.php?regiao=<?=$_REQUEST['regiao']?>&pro=<?=$_REQUEST['pro']?>&tipo=<?=$_REQUEST['tipo']?>&pag=<?=($pag-8)?>"><-</a>&nbsp;&nbsp;
			<a href="viewcracha.php?regiao=<?=$_REQUEST['regiao']?>&pro=<?=$_REQUEST['pro']?>&tipo=<?=$_REQUEST['tipo']?>&pag=<?=($pag+8)?>">-></a>
		    </td>
		</tr>
	    </table>
	    <br/>
	</div>
    </div>
</center>
</body>
</html>
<?php
function gera_barras($id)
{
    $resp = array();
    $resp[0] = "zero.jpg";
    $resp[1] = "um.jpg";
    $resp[2] = "dois.jpg";
    $resp[3] = "tres.jpg";
    $resp[4] = "quatro.jpg";
    $resp[5] = "cinco.jpg";
    $resp[6] = "seis.jpg";
    $resp[7] = "sete.jpg";
    $resp[8] = "oito.jpg";
    $resp[9] = "nove.jpg";
    $resp['*'] = "asterisco.jpg";
    
    $ret = "";
    for($i = 0; $i < strlen($id); $i++)
    {
	// Tam original width:22px; height:83px;
	$ret .= "<img src = '../barcode/" . $resp[$id[$i]] . "' style = 'width:14px; height:55px;'/>\n";
    }
    return $ret;
}
?>