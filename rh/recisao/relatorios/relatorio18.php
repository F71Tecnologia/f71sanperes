<?php
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='../login.php'>Logar</a>";
exit;
}

include "../conn.php";
include "../classes/regiao.php";
include "../classes/projeto.php";

$ClasReg = new regiao();
$ClasPro = new projeto();

#SELECIONANDO O MASTAR PARA CARREGAR A IMAGEM
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

#RECEBENDO VARIAVEIS DO GET
$projeto 	= $_REQUEST['pro'];
$regiao 	= $_REQUEST['reg'];
$data_hoje 	= date('d/m/Y');

#CLASSE PEGANDO OS DADOS DO PROJETO
$ClasPro -> MostraProjeto($projeto);
$nome_pro = $ClasPro -> nome;

#CLASSE PEGANDO O NOME DA REGIAO
$ClasReg -> MostraRegiao($regiao);
$nome_regiao = $ClasReg -> regiao;


#SELECIONANDO AS LOCAÇÕES
$relocacao = mysql_query("SELECT * FROM unidade WHERE id_regiao = '$regiao' AND campo1 = '$projeto'");
$num_locacao = mysql_num_rows($relocacao);

?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>Relat&oacute;rio de Atividades por Lota&ccedil;&atilde;o</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#FFF; margin-top:30px; margin-bottom:30px;">
<table cellspacing="0" cellpadding="0" class="relacao" style="width:920px; border:0px; page-break-after:always;">
 <tr> 
    <td width="20%" align="center">
          <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
    </td>
    <td width="80%" align="center">
         <strong>RELAT&Oacute;RIO DE ATIVIDADES POR LOTA&Ccedil;&Atilde;O</strong><br>
         <?=$row_master['razao']?>
         <table width="474" border="0" align="center" cellpadding="4" cellspacing="1" style="font-size:12px;">
            <tr style="color:#FFF;">
              <td width="155" height="22" class="top">PROJETO</td>
              <td width="154" class="top">REGIÃO</td>
              <td width="137" class="top">LOTA&Ccedil;&Otilde;ES</td>
            </tr>
            <tr style="color:#333; background-color:#efefef;">
              <td height="20" align="center"><b><?=$nome_pro?></b></td>
              <td align="center"><b><?=$nome_regiao?></b></td>
              <td align="center"><b><?=$num_locacao?></b></td>
            </tr>
        </table>
    </td>
  </tr>
  <tr> 
    <td colspan="2">
    <?php
	
	if(!empty($num_locacao)) {
		
		while($row_loc = mysql_fetch_array($relocacao)){
		
		$reatividade = mysql_query("SELECT * FROM curso WHERE id_regiao = '$regiao' and campo3 = '$projeto'");
		$numatividade = mysql_num_rows($reatividade);
		
	?>



  <div class="descricao"><b><?=$row_loc['unidade']?></b><br/><span style="font-size:10px; color:#265462;"><?=$row_loc['local']?></span></div>
	
  <table class="relacao" width="100%" cellpadding="3" cellspacing="1">
    <tr class="secao">
      <td width="75%">Atividade</td>  
      <td width="25%">Quantidade</td>
      </tr>

<?php 

while($row_ativ = mysql_fetch_array($reatividade)){ 
$res_aut = mysql_query("SELECT nome FROM autonomo WHERE id_regiao = '$regiao' and locacao = '$row_loc[unidade]' AND id_curso = '$row_ativ[0]' AND status = '1'");
$num_aut = mysql_num_rows($res_aut);

$res_clt = mysql_query("SELECT nome FROM rh_clt WHERE id_regiao = '$regiao' and locacao = '$row_loc[unidade]' AND id_curso = '$row_ativ[0]' AND status < '60'");
$num_clt = mysql_num_rows($res_clt);

$style = ($num_clt+$num_aut == 0) ? "style='display:none'" : "";

$total_unidade += $num_aut + $num_clt;
?>

      <tr class="<?php if(($num_clt+$num_aut != 0) and $alternateColor++%2==0 ) { echo "linha_um"; } else { echo "linha_dois"; } ?>" <?=$style?>>
        <td><?=$row_ativ['campo2']?></td>
        <td><?=$num_aut + $num_clt?></td>
        </tr>
  
       <?php 
	   unset($num_clt);
	   unset($num_aut);
	   }
	   
	   ?>

      <tr class="secao">
        <td colspan="2" align="center">TOTAL DE PROFISSIONAIS: <?=$total_unidade?></td>
      </tr>
  </table>

    <?php 
	
	$total_geral += $total_unidade;
	unset($total_unidade);
	} # END WHILE
	} # END IF
	
	?>
    <br />
    <table width="60%" border="0" cellspacing="0" cellpadding="0" align="center" class="relacao">
  <tr class="secao">
    <td align="center">Total Geral de Participantes: <?=$total_geral?></td>
  </tr>
</table>

    

    </td>
  </tr>
</table>
</body>
</html>
