<?
$id_clt = $_REQUEST['clt'];
$id_user = $_COOKIE['logado'];
$id_pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['id_reg'];

include "../../conn.php";

$result_rh_status_doc = mysql_query("SELECT * FROM rh_doc_status WHERE tipo='14' AND id_clt='$id_clt' AND status_reg=1");
$row_status_doc = mysql_fetch_array($result_rh_status_doc);

$row_regiao_id = mysql_result(mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$id_reg'"),0);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao_id'");
$row_master = mysql_fetch_assoc($qr_master);



?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GERENCIAMENTO DE VALE TRANPORTE</title>
<link href="../net.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
body {
 background-color: #CCC;
}
-->
</style>
<link href="../../net.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="80%" border="0" align="center"  bgcolor="#FFFFFF" cellpadding="5" cellspacing="5">
  <tr>
    <td width="100%" align="center" valign="middle"><p class="linha">
<img src="../../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif"/>
    </p></td>
  </tr>
  <tr>
    <td align="center" valign="middle">
    <?
	$status_solicitacao = $row_status_doc;
	//Caso exista uma solicitação de vale criada, habilita ou desabilita os botões "Dispensa de Vale" e "Recibo Individual".
	if($status_solicitacao == FALSE){
		$statusBotao = 	'none';
	}else{
			$statusBotao = 	'inline';	
	}
	?>
    <a href="solicita.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$id_clt?>" target="_blank"><img src="imagens/solicita.gif" width="150" height="22" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="dispensa.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$id_clt?>" target="_blank"><img src="imagens/dispensa.gif" width="150" height="22" border="0" ></a>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="mes_recibo.php?pro=<?=$id_pro?>&id_reg=<?=$id_reg?>&clt=<?=$id_clt?>" target="_blank"><img src="imagens/recibo.gif" width="150" height="22" border="0" style="display:<?=$statusBotao?>"></a></td>
  </tr>
</table>
<p class="linha">&nbsp;</p>
</body>
</html>
