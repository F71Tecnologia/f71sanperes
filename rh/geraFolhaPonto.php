<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a>";
exit;
} 

include "../conn.php";


$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$clt = $_REQUEST['idclt'];
$id_reg = $_REQUEST['id_reg'];
$tipo_contratacao = $_REQUEST['tipo'];
$data = explode("/", $_REQUEST['data']);
$ini_dia = $data[0];
$ini_mes = $data[1];
$ini_ano = $data[2];
$data_ini = "$ini_ano-$ini_mes-$ini_dia";
$mes1 = "01";
$ano = date('Y');
$linha = date('t', mktime(0, 0, 0, $mes1, 1, $ano));

/*$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$id_pro'");
$row_projeto = mysql_fetch_array($result_projeto);*/


if($tipo_contratacao != "2"){
//	  $result = mysql_query("SELECT * FROM autonomo WHERE id_projeto = '$projeto' AND tipo_contratacao = '$tipo_contratacao' and id_autonomo = '$clt'") or die(mysql_error());
        $result = mysql_query("SELECT *, B.local as projeto, A.nome as nomeFuncionario FROM rh_clt A
                                INNER JOIN projeto B ON B.id_projeto = A.id_projeto
                                INNER JOIN regioes C ON C.id_regiao = A.id_regiao 
                                INNER JOIN curso D ON D.id_curso = A.id_curso
                                INNER JOIN rh_horarios E ON E.id_horario = A.rh_horario
                                WHERE id_clt = '$clt'");

          
} else {
	  //$result = mysql_query("SELECT * FROM rh_clt WHERE id_projeto = '$projeto' AND status < '60' and id_clt = '$clt'");
          $result = mysql_query("SELECT *, B.local as projeto, A.nome as nomeFuncionario FROM rh_clt A
                                INNER JOIN projeto B ON B.id_projeto = A.id_projeto
                                INNER JOIN regioes C ON C.id_regiao = A.id_regiao 
                                INNER JOIN curso D ON D.id_curso = A.id_curso
                                INNER JOIN rh_horarios E ON E.id_horario = A.rh_horario
                                WHERE id_clt = '$clt'");
          
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>:: Intranet ::</title>
<link href="../relatorios/css/estrutura.css" rel="stylesheet" type="text/css">
<style type="text/css">
body {
	margin-bottom:-100px;
}
table.relacao table.relacao td {
	height:24px;
}
.bordaPreta{
    border: 1px #000 solid;
}
</style>
</head>
<body style="background-color:#FFF;">
<?php while($row = mysql_fetch_array($result)) { 
	  /*$result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row[id_curso]'");
	  $row_curso = mysql_fetch_assoc($result_curso); 
          $result_hora = mysql_query("SELECT * FROM rh_horarios WHERE funcao = '$row[rh_horario]'");
	  $row_hora = mysql_fetch_array($result_hora);*/
          ?>
<table cellspacing="0" cellpadding="0" class="relacao" style="width:820px; border:0px; page-break-after:always; margin-bottom:100px;">
  <tr>
    <td>
       <?php if($tipo_contratacao == '3') {
				$result_coop = mysql_query("SELECT * FROM cooperativas WHERE id_coop = '$row[id_cooperativa]'");
				$row_coop = mysql_fetch_array($result_coop); ?>
				<img src="../cooperativas/logos/coop_<?=$row_coop['id_coop']?>.jpg" width="120" height="86" /> 
        <?php } elseif($tipo_contratacao != '3') { ?>
                <img src="../imagens/logomaster<?=$row_user['id_master']?>.gif" width="120" height="86" /> 
        <?php } ?>
    </td>
    <td align="center">
      <strong><?php if($tipo_contratacao == "2") { echo "FOLHA DE PONTO"; } else { echo "FOLHA DE PRODUÇÃO"; } ?></strong><br>              <?php if($tipo_contratacao == "3") {
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
              <td height="20" align="center"><b><?php echo $row['campo3']?></b></td>
              <td align="center"><b><?php echo $row['projeto']?></b></td>
              <td align="center"><b><?php echo $row['regiao']?></b></td>
            </tr>
        </table>
    </td>
    <td align="right">
          <?php if($row['foto'] == '1') {
			  		if($tipo_contratacao == '2') {
	                   $nome_imagem = '../fotosclt/'.$row['id_regiao'].'_'.$row['id_projeto'].'_'.$row['0'].'.gif'; 
					} else {
					   $nome_imagem = '../fotos/'.$regiao.'_'.$projeto.'_'.$row['0'].'.gif'; 
					}
                } else {
	                   $nome_imagem = '../fotos/semimagem.gif';
                } 
           print "<img src='$nome_imagem' width='100' height='130' border=1 align='absmiddle'>"; ?>
    </td>
  </tr>
  <tr>
    <td colspan="3">      
      <table class="relacao" style="width:100%; margin-top:10px;">
           <tr class="secao_pai">
               <td colspan="6" ><?php echo $row['nomeFuncionario'];?></td>
           </tr>
           <tr class="secao">
              <td colspan="6" >HORÁRIO</td>
           </tr>
           <tr>
              <td colspan="6" class="bordaPreta">Entrada: <?=$row['entrada_1'];?> Saída(Alm): <?=$row['saida_1'];?> Retorno(Alm): <?=$row['entrada_2'];?> Saída: <?=$row['saida_2'];?></td>
           </tr>
           <tr class="secao">
              <td colspan="6">CARGO</td>
           </tr>
           <tr>
              <td colspan="6 " class="bordaPreta"><b><?=$row['campo2']?></b></td>
           </tr>
           <tr class="secao">
              <td width="25%">DIA</td>
              <td width="12%">ENTRADA</td>
              <td width="13%">SAÍDA (ALM)</td>
              <td width="13%">RETORNO (ALM)</td>
              <td width="12%">SAÍDA</td>
              <td width="25%" style="background-color:#F2DBDB;">ASSINATURA (VISTO)</td>
           </tr>
   <?php $result_data = mysql_query("SELECT *, date_format(data, '%d/%m/%Y') AS nova_data FROM ano WHERE data >= '$data_ini' LIMIT 0,31");
      while($row_data = mysql_fetch_array($result_data)) {
		if($row_data['2'] == "Sábado") {
		   $color = "#aaaaaa";
		} elseif($row_data['2'] == "Domingo") {
		   $color = "#aaaaaa"; 
		} else { 
		   $color = "#f5f5f5"; 
		} ?>
        <tr>
            <td bgcolor="<?=$color?>" ><?php echo "$row_data[nova_data] - $row_data[2]"; ?></td>
            <td bgcolor="<?=$color?>" class="bordaPreta">&nbsp;</td>
            <td bgcolor="<?=$color?>" class="bordaPreta">&nbsp;</td>
            <td bgcolor="<?=$color?>" class="bordaPreta">&nbsp;</td>
            <td bgcolor="<?=$color?>" class="bordaPreta">&nbsp;</td>
            <td bgcolor="<?=$color?>" class="bordaPreta">&nbsp;</td>
        </tr>
<?php } ?>
        <tr>
            <td colspan="2" align="center">
        <br>__________________________<br>ASSINATURA
            </td>
            <td colspan="2" align="center">
        <br>__________________________ <br>ASSINATURA COORDENADOR 
            </td>
            <td align="center" colspan="2" rowspan="2">
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
    </table>
            </td>
        </tr>
        <tr>
            <td colspan="4" align="center"><br>____________________________________________<br>
            <br></td>
        </tr>
    </table>
   </td>
  </tr>
</table>
<?php } ?>
</body>
</html>
