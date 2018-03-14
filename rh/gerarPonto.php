<?php
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='login.php'>Logar</a>";
exit;
} 
	
include "../conn.php";
$id = $_REQUEST['id'];
$clt = $_REQUEST['clt'];
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master ='$row_user[1]'");
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
<link href="../relatorios/css/estrutura.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="../js/ramon.js"></script>
</head>
<body>
    <div id="corpo" >
         <div id="topo" style="background-color: #fff;">
            <span style="clear:right">
                <img src="../imagens/logomaster<?php echo $row_master[0];?>.gif" alt="" width='120' height='86' />
            </span>
         </div>
         <div id="conteudo">
           <h1 style="margin:70px;"><span>RELATÓRIOS</span> FOLHA DE PONTO/PRODUÇÃO</h1>
<form action="geraFolhaPonto.php" method="post" style="margin-bottom:120px;">
    <input type="hidden" name="idClt" value="<?php echo $clt; ?>"/>
    Tipo de Contrata&ccedil;&atilde;o:
    <select name="tipo">
        <!--option value="1">Autônomo</option-->
        <option value="2">CLT</option>
        <!--option value="3">Cooperado</option-->
    </select>
    Digite a Data Inicial:
    <input name="data" type="text" class="campotexto" size="8" maxlength="10" onKeyUp="mascara_data(this)">
    <br><br>
    <input type="hidden" name="projeto" value="<?=$projeto?>">
    <input type="hidden" name="regiao" value="<?=$regiao?>">
    <input type="hidden" name="idclt" value="<?=$clt?>">
    <input type="submit" name="submit" value="Gerar Folha" class="botao">
</form>
<div id="rodape"></div>
</div>
</div>
</body>
</html>