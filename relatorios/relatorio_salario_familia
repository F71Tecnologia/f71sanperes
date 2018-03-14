<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='../login.php'>Logar</a>";
exit;
} else {

include "../conn.php";

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$projeto = $_REQUEST['pro'];
$regiao = $_REQUEST['reg'];

$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_projeto = mysql_fetch_array($result_projeto);

$data_hoje = date('d/m/Y');

$result_dependentes = mysql_query("SELECT *, date_format(data1, '%d/%m/%Y') AS data1, 
											 date_format(data2, '%d/%m/%Y') AS data2, 
											 date_format(data3, '%d/%m/%Y') AS data3, 
											 date_format(data4, '%d/%m/%Y') AS data4, 
											 date_format(data5, '%d/%m/%Y') AS data5 FROM dependentes 
											 WHERE nome1 != '' AND id_regiao = '$regiao' AND id_projeto = '$projeto' 
											 ORDER BY nome ASC");
$num_row = mysql_num_rows($result_dependentes);
?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>Relatório de Participantes com Dependentes</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#FFF; margin-top:30px; margin-bottom:30px;">
<table cellspacing="0" cellpadding="0" class="relacao" style="width:720px; border:0px; page-break-after:always;">
 <tr> 
    <td width="20%" align="center">
          <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
    </td>
    <td width="80%" align="center" colspan="2">
         <strong>RELATÓRIO DE PARTICIPANTES COM DEPENDENTES</strong><br>
         <?=$row_master['razao']?>
         <table width="500" border="0" align="center" cellpadding="4" cellspacing="1" style="font-size:12px;">
            <tr style="color:#FFF;">
              <td width="150" height="22" class="top">PROJETO</td>
              <td width="150" class="top">REGIÃO</td>
              <td width="200" class="top">TOTAL COM DEPENDENTES</td>
            </tr>
            <tr style="color:#333; background-color:#efefef;">
              <td height="20" align="center"><b><?=$row_projeto['nome']?></b></td>
              <td align="center"><b><?=$row_projeto['regiao']?></b></td>
              <td align="center"><b><?=$num_row?></b></td>
            </tr>
        </table>
    </td>
  </tr>
  <tr> 
    <td colspan="3">
  <?php while($row_dependentes = mysql_fetch_array($result_dependentes)) {
              $result_bolsista = mysql_query("SELECT nome FROM autonomo WHERE id_autonomo = '$row_dependentes[3]' 
														                AND id_regiao = '$regiao' 
																		AND id_projeto = '$projeto' 
																		AND tipo_contratacao = '$row_dependentes[5]'
																		UNION 
											  SELECT nome FROM rh_clt WHERE id_clt = '$row_dependentes[3]'
											  						  OR id_antigo = '$row_dependentes[3]'
																	  AND id_regiao = '$regiao'
																	  AND id_projeto = '$projeto'
																	  AND tipo_contratacao = '$row_dependentes[5]'");
              $row_bolsista = mysql_fetch_array($result_bolsista); ?>
          <p>&nbsp;</p>
          <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
             <tr class="secao_pai">
               <td colspan="2"><b><?=$row_bolsista['nome']?></b></td>
             </tr>
             <tr class="secao">
               <td width="75%">Nome do Dependente</td>
               <td>Data de Nascimento</td>
             </tr>
             <tr class="linha_um">
               <td width='75%'><?=$row_dependentes['nome1']?></td>
               <td><?=$row_dependentes['data1']?></td>
             </tr>
             <?php if(!empty($row_dependentes['nome2'])) { ?>
             <tr class="linha_dois">
               <td><?=$row_dependentes['nome2']?></td>
               <td><?=$row_dependentes['data2']?></td>
             </tr>
             <?php } if(!empty($row_dependentes['nome3'])) { ?>
             <tr class="linha_um">
               <td><?=$row_dependentes['nome3']?></td>
               <td><?=$row_dependentes['data3']?></td>
             </tr>
               <?php } if(!empty($row_dependentes['nome4'])) { ?>
             <tr class="linha_dois">
               <td><?=$row_dependentes['nome4']?></td>
               <td><?=$row_dependentes['data4']?></td>
             </tr>
               <?php } if(!empty($row_dependentes['nome5'])) { ?>
             <tr class="linha_um">
               <td><?=$row_dependentes['nome5']?></td>
               <td><?=$row_dependentes['data5']?></td>
             </tr>
               <?php } ?>
          </table>  
    <?php } ?> 
    </td>
  </tr>
</table>
</body>
</html>
<?php } ?>