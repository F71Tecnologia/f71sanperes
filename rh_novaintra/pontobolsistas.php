<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a>";
exit;
} else {

include "conn.php";

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$unidade = $_REQUEST['unidade'];
$data_ini = $_REQUEST['data'];
$id_bol = $_REQUEST['id_bol'];
$tipo = $_REQUEST['tipo'];

function ConverteData($Data){
 if (strstr($Data, "/"))//verifica se tem a barra /
 {
  $d = explode ("/", $Data);//tira a barra
 $rstData = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
 return $rstData;
 } elseif(strstr($Data, "-")){
 $d = explode ("-", $Data);
 $rstData = "$d[2]/$d[1]/$d[0]"; 
 return $rstData;
 }else{
 return "";
 }
}

$data_ini = ConverteData($data_ini);

if($tipo == "clt"){
$result = mysql_query("Select * from rh_clt where id_clt = '$id_bol' AND id_projeto = '$projeto' ");
$row = mysql_fetch_array($result);
} else {
$result = mysql_query("Select * from autonomo where id_autonomo = '$id_bol' AND id_projeto = '$projeto' ");
$row = mysql_fetch_array($result);
}

$result_projeto = mysql_query("Select * from projeto where id_projeto = '$projeto'");
$row_projeto = mysql_fetch_array($result_projeto);
$result_curso = mysql_query("Select * from curso where id_curso = '$row[id_curso]'");
$row_curso = mysql_fetch_array($result_curso);
$mes1 = "01";
$ano = date('Y');
$linha = date('t', mktime(0, 0, 0, $mes1, 1, $ano));
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>FOLHA DE PONTO</title>
<link href="relatorios/css/estrutura.css" rel="stylesheet" type="text/css">
<style type="text/css">
table.relacao table.relacao td {
	height:24px;
}
</style>
</head>
<body style="background-color:#FFF;">
<table cellspacing="0" cellpadding="0" class="relacao" width="820" border="0">
  <tr>
    <td>
       <?php if($row['tipo_contratacao'] == "3") {
				$result_coop = mysql_query("SELECT * FROM cooperativas WHERE id_coop = '$row[id_cooperativa]'");
				$row_coop = mysql_fetch_array($result_coop); ?>
				<img src="cooperativas/logos/coop_<?=$row_coop['id_coop']?>.jpg" width="120" height="86" /> 
        <?php } else { ?>
                <img src="imagens/logomaster<?=$row_user['id_master']?>.gif" width="120" height="86" /> 
        <?php } ?>
    </td>
    <td align="center">
      <strong><?php if($row['tipo_contratacao'] == "2") { echo "FOLHA DE PONTO"; } else { echo "FOLHA DE PRODUÇÃO"; } ?></strong><br>              
	          <?php if($row['tipo_contratacao'] == "3") {
				    echo $row_coop['nome'];
                 } elseif($tipo_contratacao != "3") { 
			        echo $row_master['razao'];
			     } ?>
       <table width="400" border="0" align="center" cellpadding="4" cellspacing="1" style="font-size:12px;">
            <tr style="color:#FFF;">
              <td width="100" height="22" class="top">C&Oacute;DIGO</td>
              <td width="150" class="top">PROJETO</td>
              <td width="150" class="top">REGI&Atilde;O</td>
            </tr>
            <tr style="color:#333; background-color:#efefef;">
              <td height="20" align="center"><b><?=$row['campo3']?></b></td>
              <td align="center"><b><?=$row_projeto['nome']?></b></td>
              <td align="center"><b><?=$row_projeto['regiao']?></b></td>
            </tr>
        </table>
    </td>
    <td align="right">
          <?php if($row['foto'] == "1"){
	                   $nome_imagem = $row['id_regiao']."_".$row['id_projeto']."_".$row['0'].".gif"; 
                } else {
	                   $nome_imagem = 'semimagem.gif';
                } 
           print "<img src='fotos/$nome_imagem' width='100' height='130' border=1 align='absmiddle'>"; ?>
    </td>
  </tr>
  <tr>
    <td colspan="3" align="center">      
      <table class="relacao" width="815" border="0" cellspacing="0" cellpadding="0" style="margin-top:10px;" >
           <tr class="secao_pai">
              <td colspan="6"><?=$row['nome']?></td>
           </tr>
           <tr class="secao">
              <td colspan="6">HORA</td>
           </tr>
           <tr>
              <td colspan="6">&nbsp;</td>
           </tr>
           <tr class="secao">
              <td colspan="6">UNIDADE</td>
           </tr>
           <tr>
              <td colspan="6"><b><?=$row['locacao']?></b></td>
           </tr>
           <tr class="secao">
              <td width="212">DIA</td>
              <td width="121">ENTRADA</td>
              <td width="119">SAÍDA (ALM)</td>
              <td width="121">RETORNO (ALM)</td>
              <td width="121">SAÍDA</td>
              <td width="119" style="background-color:#F2DBDB;">ASSINATURA (VISTO)</td>
           </tr>
   <?php $result_data = mysql_query("SELECT *, date_format(data, '%d/%m/%Y') AS nova_data FROM ano WHERE data >= '$data_ini' LIMIT 0,31");
      while($row_data = mysql_fetch_array($result_data)) {
		if($row_data['2'] == "Sábado") {
		   $color = "#aaaaaa";
		} elseif($row_data['2'] == "Domingo") {
		   $color = "#aaaaaa"; 
		} else { 
		   $color = "#f5f5f5"; 
		} 
		
		$nomedat = explode("-",$row_data[2]);
		$bord = "style='border-bottom:1px solid #C7C7C7; border-left:1px solid #C7C7C7;'";
		
		?>
        <tr>
            <td bgcolor="<?=$color?>" <?=$bord?>><?php echo "$row_data[nova_data] - $nomedat[0]"; ?></td>
            <td bgcolor="<?=$color?>" <?=$bord?>>&nbsp;</td>
            <td bgcolor="<?=$color?>" <?=$bord?>>&nbsp;</td>
            <td bgcolor="<?=$color?>" <?=$bord?>>&nbsp;</td>
            <td bgcolor="<?=$color?>" <?=$bord?>>&nbsp;</td>
            <td bgcolor="<?=$color?>" <?=$bord?>>&nbsp;</td>
        </tr>
<?php } ?>
        <tr>
            <td colspan="3" align="center">
              <br>
              ______________________________________________<br>ASSINATURA
              <br></td>
            <td colspan="3" rowspan="2" align="center">
              <table cellpadding="0" cellspacing="0" style="height:70px; font-size:11px; font-weight:bold; width:100%; border:0px;">
                <tr style="height:5px;">
                  <td style="width:30%;" bgcolor="#cccccc">OBSERVA&Ccedil;&Otilde;ES</td>
                  <td style="width:70%;">
                    <table align="right" cellpadding="0" cellspacing="0" style="font-size:11px; font-weight:bold; width:90%; border:0px; margin:2px;">
                      <tr>
                        <td style="width:60%;" bgcolor="#cccccc">TOTAL DE FALTAS</td>
                        <td style="width:40%;" bgcolor="#F2DBDB"></td>
                       </tr>
                      </table>
                    </td>
                </tr>
                <tr style="height:60px;">
                  <td colspan="2" bgcolor="#F2DBDB"></td>
                </tr>
              </table>            </td>
        </tr>
        <tr>
            <td colspan="3" align="center"><br><!-- ____________________________________________<br>RESPONSÁVEL -->
              ______________________________________________<br>
            ASSINATURA COORDENADOR </td>
        </tr>
    </table>
   </td>
  </tr>
</table>
</body>
</html>
<?php } ?>