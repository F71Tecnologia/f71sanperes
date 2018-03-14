<?php
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
exit;
} else {
	
include "../conn.php";

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

if(empty($_REQUEST['limit_2'])) {

$limit_1 = "0";
$limit_2 = "30";
$projeto = $_REQUEST['projeto'];
$regiao = $_REQUEST['regiao'];
$modalidade = $_REQUEST['modalidade'];
$curso = $_REQUEST['curso'];
$ini_dia = $_REQUEST['ini_dia'];
$ini_mes = $_REQUEST['ini_mes'];
$ini_ano = $_REQUEST['ini_ano'];
$fim_dia = $_REQUEST['fim_dia'];
$fim_mes = $_REQUEST['fim_mes'];
$fim_ano = $_REQUEST['fim_ano'];
$carga_horaria = $_REQUEST['carga_horaria'];
$data_ini = "$ini_dia/$ini_mes/$ini_ano";
$data_fim = "$fim_dia/$fim_mes/$fim_ano";

} else {

$limit_1 = $_REQUEST['limit_1'];
$limit_2 = $_REQUEST['limit_2'];
$projeto = $_REQUEST['projeto'];
$regiao = $_REQUEST['regiao'];
$modalidade = $_REQUEST['modalidade'];
$curso = $_REQUEST['curso'];
$carga_horaria = $_REQUEST['carga_horaria'];
$data_ini = $_REQUEST['data_ini'];
$data_fim = $_REQUEST['data_fim'];

}

$result_projeto = mysql_query("SELECT *, date_format(inicio, '%d/%m/%Y') as inicio2 FROM projeto WHERE id_projeto = '$projeto'");
$row_projeto = mysql_fetch_array($result_projeto);

$result_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_array($result_regiao);

$result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$curso'");
$row_curso = mysql_fetch_array($result_curso);

$data_hoje = date('d/m/Y');

if($row_projeto['inicio2'] <= "01/01/$ano") {
     $data_inicio = "01/01/$ano";
} else {
     $data_inicio = $row_projeto['inicio2'];
}

$result_cc = mysql_query("SELECT * FROM rh_clt WHERE id_curso = '$curso' AND status = '1' ORDER BY locacao LIMIT 0,30");
$row_cc = mysql_fetch_array($result_cc);
$num_cc = mysql_num_rows($result_cc);

$nun_turmas = $num_cc / 30;
$nun_turmas = ceil($nun_turmas);
$pagina = $limit_2 / 30;
?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>Relatório de Capacitação</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#FFF; margin-top:30px;">
<table cellspacing="0" cellpadding="0" class="relacao" style="width:720px; border:0px; page-break-after:always;">
  <tr> 
    <td width="20%" align="center">
          <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
    </td>
    <td width="80%" align="center" colspan="2">
         <strong>DADOS DE CAPACITA&Ccedil;&Atilde;O</strong><br>
         <?=$row_master['razao']?>
         <table width="500" border="0" align="center" cellpadding="4" cellspacing="1" style="font-size:12px;">
            <tr style="color:#FFF;">
              <td width="150" height="22" class="top">PROJETO</td>
              <td width="150" class="top">REGIÃO</td>
              <td width="200" class="top">CAPACITA&Ccedil;&Atilde;O OFERTADA</td>
            </tr>
            <tr style="color:#333; background-color:#efefef;">
              <td height="20" align="center"><b><?=$row_projeto['nome']?></b></td>
              <td align="center"><b><?=$row_projeto['regiao']?></b></td>
              <td align="center"><b><?=$row_curso['nome']?></b></td>
            </tr>
        </table>
    </td>
  </tr>
  <tr> 
    <td colspan="3">

       <div class="descricao">
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size:12px;">
          <tr>
            <td width="14%" align="right"><strong>In&iacute;cio:</strong></td>
			<td>&nbsp;<?=$data_ini?></td>
          </tr>
          <tr>
            <td align="right"><strong>T&eacute;rmino:</strong></td>
			<td>&nbsp;<?=$data_fim?></td>
          </tr>
          <tr> 
            <td align="right"><strong>Modalidade:</strong></td>
            <td>&nbsp;<?=$modalidade?></td>
          </tr>
          <tr>
            <td align="right"><strong>Carga hor&aacute;ria:</strong></td>
            <td>&nbsp;<?=$carga_horaria?>hs</td>
          </tr>
        </table>
        </div>
        
        <p>Turma <strong><?=$pagina?></strong></p>
        
        <table width="95%" cellspacing="0" cellpadding="0" class="border2">
          <tr> 
            <td width="3%">&nbsp;</td>
            <td width="33%"><strong>Participante</strong></td>
            <td width="23%"><strong>Matricula</strong></td>
            <td width="41%"><strong>Unidade de lota&ccedil;&atilde;o</strong></td>
          </tr>
            
     <?php $linha = "1";
           $result = mysql_query("SELECT * FROM bolsista$projeto WHERE id_curso = '$curso' AND status = '1' ORDER BY locacao LIMIT $limit_1 , 30");                  
	 while($row = mysql_fetch_array($result)) {
		   $result_mat = mysql_query("SELECT * FROM tvsorrindo WHERE id_bolsista = '$row[0]' AND id_projeto = '$projeto'");
		   $row_mat = mysql_fetch_array($result_mat); ?>
           
        <tr>
         <td align="center"><strong><?=$linha?></strong></td>
         <td><strong><?=$row['nome']?></strong></td>
         <td align="center"><strong><?=$row_mat['matricula']?></strong></td>
         <td><strong><?=$row['locacao']?></strong></td>
       </tr>

          <?php $linha ++; } ?>

        </table>

<?php
if($num_cc <= "30") {

} else {

if($pagina == "1") {

$limit_1 = $limit_1 + 30;
$limit_2 = $limit_2 + 30;

$botao1 = NULL;
$botao2 = "<a href='relatorio4.php?id=2&limit_1=$limit_1&limit_2=$limit_2&projeto=$projeto&regiao=$regiao&modalidade=$modalidade&curso=$curso&carga_horaria=$carga_horaria&data_ini=$data_ini&data_fim=$data_fim' class='link1'>Próxima Turma >></a>";

} elseif($pagina == $nun_turmas) {

$limit_1_v = $limit_1 - 30;
$limit_2_v = $limit_2 - 30;

$botao1 = "<a href='relatorio4.php?id=2&limit_1=$limit_1_v&limit_2=$limit_2_v&projeto=$projeto&regiao=$regiao&modalidade=$modalidade&curso=$curso&carga_horaria=$carga_horaria&data_ini=$data_ini&data_fim=$data_fim' class='link1'><< Turma Anterior</a>";
$botao2 = NULL;

} else {

$limit_1_v = $limit_1 - 30;
$limit_2_v = $limit_2 - 30;
$limit_1 = $limit_1 + 30;
$limit_2 = $limit_2 + 30;

$botao1 = "<a href='relatorio4.php?id=2&limit_1=$limit_1_v&limit_2=$limit_2_v&projeto=$projeto&regiao=$regiao&modalidade=$modalidade&curso=$curso&carga_horaria=$carga_horaria&data_ini=$data_ini&data_fim=$data_fim' class='link1'><< Turma Anterior</a>";
$botao2 = "<a href='relatorio4.php?id=2&limit_1=$limit_1&limit_2=$limit_2&projeto=$projeto&regiao=$regiao&modalidade=$modalidade&curso=$curso&carga_horaria=$carga_horaria&data_ini=$data_ini&data_fim=$data_fim' class='link1'>Próxima Turma >></a>";

}

print "$botao1&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$botao2"; 

}

?>
      </div>
    </td>
  </tr>
</table>
</body>
</html>
<?php } ?>