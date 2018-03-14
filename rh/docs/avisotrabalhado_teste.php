<?php
if(empty($_COOKIE['logado'])){
	print "Efetue o Login<br><a href='login.php'>Logar</a> ";
	exit;
}

include "../../conn.php";
include "../../funcoes.php";
include "../../classes/regiao.php";
include "../../empresa.php";
$REG = new regiao();

//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 
	
$decript = explode("&",$link);
#clt=$id_clt&pro=$pro&id_reg=$id_reg&data_demi=$data_demi&data_aviso=$data_aviso
$id_clt 	= $decript[0];
$pro 		= 3228;
$id_reg 	= 8;
$data_demi 	= '30/06/2012';
$data_aviso = '01/06/2012';
//RECEBENDO A VARIA//VEL CRIPTOGRAFADA

$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$id_reg'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]' ");
$row_master = mysql_fetch_assoc($qr_master);



$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt IN(4164, 4254, 4467) ");
while($row_clt = mysql_fetch_assoc($qr_clt)):

$ids_clt[] = $row_clt['id_clt'];

endwhile;

$total_id = implode(',', $ids_clt);

$result_bol = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada,date_format(data_saida, '%d/%m/%Y')as data_saida FROM rh_clt where id_clt IN(4164, 4254, 4467) ORDER BY nome ASC");
while($row = mysql_fetch_array($result_bol)):

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>COMUNICADO DE DISPENSA</title>
<link href="../../net1.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="../../js/ramon.js"></script>
</head>

<body>

<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="bordaescura1px" style="margin-bottom:100px; height:1000px">
  <tr>
    <td width="21%" height="26"><p class="MsoHeader" align="center" style='text-align:center'><strong><span class="style5">
<?php

$img= new empresa();
$img -> imagem();



$result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row[id_curso]'");
$row_curso = mysql_fetch_array($result_curso);

$result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row[id_projeto]'");
$row_pro = mysql_fetch_array($result_pro);

$data = date('d/m/Y');


$REG -> MostraRegiao($row['id_regiao']);
$regiao_nome = $REG -> regiao;

$REG -> EmpresaRegiaoLogado();
$empresa = $REG -> nome;


?><!--<img src='imagens/certificadosrecebidos.gif' width='120' height='86' />--><br />
    </span></strong></p>    </td>
    <td width="58%"><p class="MsoHeader" align="center" style='text-align:center'><b><span
  style='font-size:12.0pt;color:red'><?=$row_pro['nome']?><?=" / $row_pro[regiao] <br><br> $row[locacao]"?></span></b></p>    </td>

    <td width="21%">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3"><div style="padding:7px;">
      <p align="center" style='text-align:right'>&nbsp;</p>
      <p align="center"><strong><u>AVISO  PR&Eacute;VIO DO EMPREGADOR</u></strong></p>
      <p>&nbsp;</p>
      <p><strong><?=$regiao_nome.", ".$REG -> MostraDataCompleta($data_aviso)?></strong>.</p>
<p>&nbsp;</p>
      <p>Sr.(a) <strong><?=$row['nome']?></strong></p>
      <p>Portador(a) da Carteira de Trabalho <strong><?='Número: '.$row['campo1'].' / Série: '.$row['serie_ctps'].' / UF: '.$row['uf_ctps']?></strong></p>
      <p>&nbsp;</p>
      <p>Por n&atilde;o mais convir a esta empresa mant&ecirc;-lo em nosso  quadro de funcion&aacute;rios, vimos comunicar-lhe que seu Contrato de Trabalho ser&aacute;  rescindido em  <span class="red"><b><?=$data_demi?></b></span>. A partir de <span class="red"><b><?=$data_aviso?></b></span>, haver&aacute; uma redu&ccedil;&atilde;o no seu hor&aacute;rio  normal de trabalho, sem preju&iacute;zo do sal&aacute;rio integral, sendo-lhe facultada, de  acordo com as disposi&ccedil;&otilde;es vigentes, a op&ccedil;&atilde;o por uma das seguintes alternativas:</p>
      <p>1- Redu&ccedil;&atilde;o de 02(duas) horas di&aacute;rias em seu  hor&aacute;rio normal de trabalho;</p>
      <p>2- Redu&ccedil;&atilde;o de 07 (sete) dias corridos.</p>
      <p>&nbsp;</p>
      <p>_____________________________________<br />
        <strong><?=$empresa?></strong></p>
<p>&nbsp;</p>
      <p>De  acordo com as disposi&ccedil;&otilde;es legais vigentes, declaro, para todos os fins de  direito, que, nesta data, opto pela alternativa de redu&ccedil;&atilde;o de hor&aacute;rio de  trabalho n. ____ (&nbsp;&nbsp;&nbsp;&nbsp; ) acima descrita.</p>
      <p>&nbsp;</p>
      <p><strong><?=$regiao_nome.", ".$REG -> MostraDataCompleta($data_aviso)?></strong>.</p>
      <p>&nbsp;</p>
      <p>______________________________________<br />
        <strong><?=$row['nome']?></strong></p>
    <br/>
    </div>
    </td>

  </tr>

  <tr>

    <td colspan="7"><div align="center">

      <p>
<?php
echo '<div style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;color: black" align="center">';
	echo '<div style="font-weight: bold"> '.$row_master['razao'].' </div>';
	echo '<br>';
	echo '<div>CNPJ: '.$row_master['cnpj'].'</div>';
	echo '<div> '.$row_master['endereco'].' </div>';
	echo '<div> '.$row_master['telefone'].' </div>';
	echo '</div>';

?><span class='style13 style3 style4'>&nbsp;</span>

        <span class='style13 style3 style4'>&nbsp;</span>    <span class='style13'></p>

      <p>&nbsp;</p>
    </div>    
      </tr>

</table>

<?php

endwhile;
?>
</body>
</html>
