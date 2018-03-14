<?php
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='login.php'>Logar</a>";
exit;
} else {
	
include "conn.php";
$id = $_REQUEST['id'];

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['pro'];
$id_bol = $_REQUEST['id_bol'];

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_projeto = mysql_fetch_array($qr_projeto);
?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>:: Intranet ::</title>
<link href="relatorios/css/estrutura.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="js/ramon.js"></script>
</head>
<?php
switch($id) {
case 1:
?>
<body>
    <div id="corpo">
         <div id="topo">
         	<span style="float:right"><?php include('reportar_erro.php'); ?></span>
            <span style="clear:right">
	       <img src='imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
           <br><br><?=$row_master['razao']?>
           <br><span style="color:#363;">CNPJ:</span> <?=$row_master['cnpj']?>
           
           
           
         </div>
         <div id="conteudo">
           <h1 style="margin:70px;"><span>RELATÓRIOS</span> FOLHA DE PONTO/PRODUÇÃO</h1>
<form action="copiapontobolsistas.php" method="post" style="margin-bottom:120px;">
    Tipo de Contrata&ccedil;&atilde;o:
    <select name="tipo">
        <option value="1">Autônomo</option>
        <option value="2">CLT</option>
        <option value="3">Cooperado</option>
    </select>
    Digite a Data Inicial:
    <input name="data" type="text" class="campotexto" size="8" maxlength="10" onKeyUp="mascara_data(this)">
    <br><br>
    <input type="hidden" name="projeto" value="<?=$projeto?>">
    <input type="hidden" name="regiao" value="<?=$regiao?>">
    <input type="submit" name="submit" value="Gerar Folha" class="botao">
</form>
<div id="rodape"></div>
</div>
</div>
<?php
break;
case 2: 

$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['pro'];
$unidade = $_REQUEST['unidade'];
$id_bol = $_REQUEST['id_bol'];
$tipo = $_REQUEST['tipo'];

$result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_pro = mysql_fetch_array($result_pro);

if($tipo == "clt") {
	$result_bol = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$id_bol' AND id_projeto = '$projeto'");
	$row_bol = mysql_fetch_array($result_bol);
} else {
	$result_bol = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$id_bol' AND id_projeto = '$projeto'");
	$row_bol = mysql_fetch_array($result_bol);
} 
?>
<body>
    <div id="corpo">
         <div id="topo">
         <span style="float:right"><?php include('reportar_erro.php'); ?></span>
            <span style="clear:right">
	       <img src='imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
           <br><br><?=$row_master['razao']?>
           <br><span style="color:#363;">CNPJ:</span> <?=$row_master['cnpj']?>
         </div>
         <div id="conteudo">
           <h1 style="margin:70px;"><span>RELATÓRIOS</span> FOLHA DE PONTO/PRODUÇÃO</h1>
<form action="pontobolsistas.php" method="post" style="margin-bottom:120px;">
    <span style="color:#C30"><?=$row_bol['nome']?></span><br>
    Digite a Data Inicial:  
    <input style="margin-top:14px;" name="data" type="text" class="campotexto" size="8" maxlength="10" onKeyUp="mascara_data(this)">
    <br><br>
    <input type="hidden" name="projeto" value="<?=$projeto?>">
    <input type="hidden" name="regiao" value="<?=$regiao?>">
    <input type="hidden" name="unidade" value="<?=$unidade?>">
    <input type="hidden" name="id_bol" value="<?=$row_bol[0]?>">
    <input type="hidden" name="tipo" value="<?=$tipo?>">
    <input type="submit" name="submit" value="Gerar Folha" class="botao">
</form>
<div id="rodape"></div>
</div>
</div>
<?php break;
} ?>
</body>
</html>
<?php } ?>