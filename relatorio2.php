<?php
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='../login.php'>Logar</a>";
exit;
} else {

include "../conn.php";

$projeto = $_REQUEST['projeto'];
$ano = $_REQUEST['ano'];
$regiao = $_REQUEST['regiao'];
$unidade = $_REQUEST['unidade'];

$result_pro = mysql_query("SELECT *, date_format(inicio, '%d/%m/%Y') as inicio2 FROM projeto where id_projeto = '$projeto'");
$row_pro = mysql_fetch_array($result_pro);

$data_hoje = date('d/m/Y');

if($row_pro['inicio2'] < "01/01/$ano") {
$data_inicio = "01/01/$ano";
} else {
$data_inicio = "$row_pro[inicio2]";
}

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);
?>
<html>
<head>
<title>Relatório de Participantes do Projeto</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#FFF; margin-top:30px;">
<table cellspacing="0" cellpadding="0" class="relacao" style="width:720px; border:0px;">
  <tr> 
    <td width="20%" align="center">
        <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
    </td>
    <td width="80%" align="center" colspan="2">
        <strong>RELAT&Oacute;RIO DE PARTICIPANTES DO PROJETO</strong><br><?=$row_master['razao']?>
         <table width="500" border="0" align="center" cellpadding="4" cellspacing="1" style="font-size:12px;">
            <tr style="color:#FFF;">
              <td width="150" height="22" class="top">PROJETO</td>
              <td width="150" class="top">REGIÃO</td>
              <td width="200" class="top">UNIDADE</td>
            </tr>
            <tr style="color:#333; background-color:#efefef;">
              <td height="20" align="center"><b><?php print "$row_pro[nome]"; ?></b></td>
              <td align="center"><b><?php print "$row_pro[regiao]"; ?></b></td>
              <?php $result_unidades = mysql_query("SELECT * FROM unidade WHERE id_unidade = '$unidade'");
	                $row_unidades = mysql_fetch_array($result_unidades); ?>
              <td align="center"><b><?php print "$row_unidades[unidade]"; ?></b></td>
            </tr>
        </table>
    </td>
  </tr>
  <tr> 
    <td colspan="3" style="font-weight:normal; text-align:center;"><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
      TOTAL DE PESSOAS ATENDIDAS PELO PROJETO NO ANO DE <b><?php echo $ano; ?></b> DENTRO DO PER&Iacute;ODO DE: <?php echo "<b>$data_inicio</b> ATÉ <b>$data_hoje</b>"; ?><p>&nbsp;</p><p>&nbsp;</p>

      <?php $tipo = $_REQUEST['tipo'];

	  for ($i=01; $i<=12; $i++) {

      switch ($i) {
	  case 1:
	  $mes = "Janeiro";
	  break;
  	  case 2:
	  $mes = "Fevereiro";
	  break;
	  case 3:
	  $mes = "Março";
	  break;
	  case 4:
	  $mes = "Abril";
	  break;
	  case 5:
	  $mes = "Maio";
	  break;
	  case 6:
	  $mes = "Junho";
	  break;
	  case 7:
	  $mes = "Julho";
	  break;
	  case 8:
	  $mes = "Agosto";
	  break;
	  case 9:
	  $mes = "Setembro";
	  break;
	  case 10:
	  $mes = "Outubro";
	  break;
	  case 11:
	  $mes = "Novembro";
	  break;
	  case 12:
	  $mes = "Dezembro";
	  break;
	  } ?>

	<table class="relacao" width="100%" border="0">
        <tr>
          <td colspan="2" class="ativo">Participantes do projeto em <?=$mes?></td>
        </tr>
        
	 <?php // Verifica se tem registros
	       $result_cursos2 = mysql_query("SELECT * FROM curso WHERE campo3 = '$projeto'"); 
           $quantidade = NULL;
		   while($row_cursos2 = mysql_fetch_array($result_cursos2)) {
			   
		   if($tipo == "1" or $tipo == "3" or $tipo == "4") {
              $result_cont2 = mysql_query("SELECT COUNT(*) FROM autonomo WHERE id_curso='$row_cursos2[0]' and locacao = '$row_unidades[unidade]' and data_entrada >= '$ano-$i-01' and data_entrada <= '$ano-$i-31' and tipo_contratacao != '2' AND id_projeto = '$result_pro'");
           } else {
              $result_cont2 = mysql_query("SELECT COUNT(*) FROM rh_clt where id_curso='$row_cursos2[0]' and locacao = '$row_unidades[unidade]' and data_entrada >= '$ano-$i-01' and data_entrada <= '$ano-$i-31'");
           }

	       $row_cont2 = mysql_fetch_array($result_cont2);
		   $quantidade = $quantidade + $row_cont2[0];
		   
		   }
		   
		   if(!empty($quantidade)) {
		   // Fim da Verificação
		   
		   // Loop dos Integrantes
		   $result_cursos = mysql_query("SELECT * FROM curso WHERE campo3 = '$projeto'"); 
           $primeira_linha = NULL;
	       while($row_cursos = mysql_fetch_array($result_cursos)) {

		   if($tipo == "1" or $tipo == "3" or $tipo == "4") {
              $result_cont = mysql_query("SELECT COUNT(*) FROM autonomo WHERE id_curso='$row_cursos[0]' and locacao = '$row_unidades[unidade]' and data_entrada >= '$ano-$i-01' and data_entrada <= '$ano-$i-31' and tipo_contratacao != '2' AND id_projeto = '$result_pro'");
           } else {
              $result_cont = mysql_query("SELECT COUNT(*) FROM rh_clt where id_curso='$row_cursos[0]' and locacao = '$row_unidades[unidade]' and data_entrada >= '$ano-$i-01' and data_entrada <= '$ano-$i-31'");
           }

	       $row_cont = mysql_fetch_array($result_cont);
		   $primeira_linha = $primeira_linha + 1;
		   
		   if($primeira_linha == "1") { ?>
          <tr class="secao">
             <td width="80%">ATIVIDADE</td>
             <td width="280%">QUANTIDADE</td>
         </tr>
         <?php } if(!empty($row_cont[0])) { ?>
	     <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
             <td><?=$row_cursos['nome']?></td>
             <td><?=$row_cont[0]?></td>
         </tr>
         <?php } } unset($primeira_linha); // Fim do Loop
		       } else { ?>
         <tr>
             <td colspan="2" align="center"><i>Sem Participantes</i></td>
         </tr>
     <?php } ?>
   </table>
	  
   <table class="relacao" width="100%" border="0">
       <tr>
         <td colspan="2" class="desativado">Participantes DESLIGADOS do projeto em <?=$mes?></td>
       </tr>
       
       <?php // Verifica se tem registros
	       $result_cursos3 = mysql_query("SELECT * FROM curso where campo3 = '$projeto'");
           $quantidade2 = NULL;
		   while($row_cursos3 = mysql_fetch_array($result_cursos3)) {
			   
		   if($tipo == "1" or $tipo == "3" or $tipo == "4") {
                $result_cont3 = mysql_query("SELECT COUNT(*) FROM autonomo where id_curso='$row_cursos3[0]' and locacao = '$row_unidades[unidade]' and data_saida >= '$ano-$i-01' and data_saida <= '$ano-$i-31' and tipo_contratacao != '2' AND id_projeto = '$result_pro'");
            } else {
                $result_cont3 = mysql_query("SELECT COUNT(*) FROM rh_clt where id_curso='$row_cursos3[0]' and locacao = '$row_unidades[unidade]' and data_saida >= '$ano-$i-01' and data_saida <= '$ano-$i-31'");
            }

	       $row_cont3 = mysql_fetch_array($result_cont3);
		   $quantidade2 = $quantidade2 + $row_cont3[0];
		   
		   }
		   
		   if(!empty($quantidade2)) {
		   // Fim da Verificação
		   
		   // Loop dos Integrantes
		   $result_cursos4 = mysql_query("SELECT * FROM curso where campo3 = '$projeto'");
           $primeira_linha2 = NULL;
	       while($row_cursos4 = mysql_fetch_array($result_cursos4)) {

		   if($tipo == "1" or $tipo == "3" or $tipo == "4") {
                $result_cont4 = mysql_query("SELECT COUNT(*) FROM autonomo where id_curso='$row_cursos4[0]' and locacao = '$row_unidades[unidade]' and data_saida >= '$ano-$i-01' and data_saida <= '$ano-$i-31' and tipo_contratacao != '2' AND id_projeto = '$result_pro'");
            } else {
                $result_cont4 = mysql_query("SELECT COUNT(*) FROM rh_clt where id_curso='$row_cursos4[0]' and locacao = '$row_unidades[unidade]' and data_saida >= '$ano-$i-01' and data_saida <= '$ano-$i-31'");
            }

	       $row_cont4 = mysql_fetch_array($result_cont4);
		   $primeira_linha2 = $primeira_linha2 + 1;
		   
		   if($primeira_linha2 == "1") { ?>
    <tr class="secao">
             <td width="80%">ATIVIDADE</td>
             <td width="280%">QUANTIDADE</td>
         </tr>
         <?php } if(!empty($row_cont4[0])) { ?>
	     <tr class="<?php if($alternateColor2++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
             <td><?=$row_cursos4['nome']?></td>
             <td><?=$row_cont4[0]?></td>
         </tr>
         <?php } } unset($primeira_linha2); // Fim do Loop
		       } else { ?>
         <tr>
             <td colspan="2" align="center"><i>Sem Participantes</i></td>
         </tr>
     <?php } ?>
   </table>
      <p style="margin-bottom:40px;"></p>
	  <?php } ?>    
    </td>
  </tr>
</table>
</body>
</html>
<?php } ?>