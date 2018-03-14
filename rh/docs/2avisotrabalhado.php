<?php
if(empty($_COOKIE['logado'])){
	print "Efetue o Login<br><a href='login.php'>Logar</a> ";
	exit;
}

include "../../conn.php";
include "../../funcoes.php";
include "../../classes/regiao.php";

$REG = new regiao();

//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 
	
$decript = explode("&",$link);
#clt=$id_clt&pro=$pro&id_reg=$id_reg&data_demi=$data_demi&data_aviso=$data_aviso
$id_clt 	= $decript[0];
$pro 		= $decript[1];
$id_reg 	= $decript[2];
$data_demi 	= $decript[3];
$data_aviso     = $decript[4];

if(!empty($_REQUEST['p'])){
	$dias = "zero";
}else{
	$dias = "trinta";
}




$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$id_reg'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]' ");
$row_master = mysql_fetch_assoc($qr_master);

$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE  id_regiao = $id_reg AND  id_projeto = '$pro'");
$row_empresa = mysql_fetch_assoc($qr_empresa);


//RECEBENDO A VARIAVEL CRIPTOGRAFADA

$result_bol = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada,date_format(data_saida, '%d/%m/%Y')as data_saida FROM rh_clt where id_clt = '$id_clt'");
$row = mysql_fetch_array($result_bol);

$result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row[id_curso]'");
$row_curso = mysql_fetch_array($result_curso);

$result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$pro'");
$row_pro = mysql_fetch_array($result_pro);

$data = date('d/m/Y');


$REG -> MostraRegiao($id_reg);
$regiao_nome = $REG -> regiao;

$REG -> EmpresaRegiaoLogado();
$empresa = $REG -> nome;

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

<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="bordaescura1px">
  <tr>
    <td width="21%" height="26"><p class="MsoHeader" align="center" style='text-align:center'><strong><span class="style5">
<?php
include "../../empresa.php";
$img= new empresa();
$img->imagem();
?><!--<img src='imagens/certificadosrecebidos.gif' width='120' height='86' />--><br />
    </span></strong></p>    </td>
    <td width="58%"><p class="MsoHeader" align="center" style='text-align:center'><b><span
  style='font-size:12.0pt;color:red'><?=" $regiao_nome <br><br> $row_pro[nome]"?></span> </b></p>    </td>

    <td width="21%">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3"><div style="padding:7px;">
      <p align="center" style='text-align:right'>&nbsp;</p>
      <p align="center"><strong><u>AVISO  PR&Eacute;VIO DO EMPREGADO</u></strong></p>
      <p>&nbsp;</p>
      <p align="center"><strong><?=$regiao_nome.", ".$REG -> MostraDataCompleta($data_aviso)?></strong>.</p>
      <p>&nbsp;</p>
      <p align="center">&Agrave; <strong><?=$row_pro['nome']?></strong> </p>
      <p align="center">Ref : Aviso Pr&eacute;vio do Empregado</p>
      <p align="center">Eu, <span class="red"><?=$row['nome']?></span> portador da carteira de trabalho <strong>
        <?='N&uacute;mero: '.$row['campo1'].' / S&eacute;rie: '.$row['serie_ctps'].' / UF: '.$row['uf_ctps']?></strong>,
exercendo a função de <?=$row_curso['campo2']?> , venho por meio desta , na forma da legislação vigente ,
comunicar a minha intenção de deixar o emprego, <span class="red"><b><?=$dias?></b></span> dias a partir da entrega deste aviso ,
por minha livre e espontânea vontade.</p>
      <p>&nbsp;</p>
      <p align="center">Solicito a confirma&ccedil;&atilde;o do recebimento deste aviso.<br />
        <br />
        Sauda&ccedil;&otilde;es,<br />
        <br />
        <br />
      </p>
      <p align="center">_____________________________________<br />
        <strong><?=$row['nome']?></strong></p>
<p>&nbsp;</p>
<p align="center"><br />
  Ciente da Empresa _____/_____/_________<br />
  <br />
  <br />
  <br />
  <br />
  _____________________________<br />
  <strong><?=$row_empresa['razao']?></strong>
</p>
<br />
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
</body>
</html>
