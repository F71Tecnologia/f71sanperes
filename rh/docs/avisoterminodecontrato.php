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
$data_aviso = $decript[4];
//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$id_reg'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]' ");
$row_master = mysql_fetch_assoc($qr_master);




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
$img -> imagem();
?><!--<img src='imagens/certificadosrecebidos.gif' width='120' height='86' />--><br />
    </span></strong></p>    </td>
    <td width="58%"><p class="MsoHeader" align="center" style='text-align:center'><b><span
  style='font-size:12.0pt;color:red'><?php print $row_pro[nome] ?></span></b></p>    </td>

    <td width="21%">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3"><div style="padding:7px;">
      <p align="center" style='text-align:right'>&nbsp;</p>
      <p><center>
        <p><strong>
          <?=$regiao_nome.", ".$REG -> MostraDataCompleta($data_aviso)?>
        </strong><br />
          <br />
        </p>
        <p align="center">&nbsp;</p>
        <p>Sr.(a)<strong>
          <?=$row['nome']?>
        </strong></p>
        <p>Portador(a) da Carteira de Trabalho <strong><?='Número: '.$row['campo1'].' / Série: '.$row['serie_ctps'].' / UF: '.$row['uf_ctps']?></strong></p>
<p>&nbsp;</p>
        <p>Comunicamos a V.S&ordf; que seu contrato de experi&ecirc;ncia se  extingue no dia <span class="red"><b><?=$data_aviso?></b></span> e, inexistindo interesse de nossa parte na  continuidade do contrato de trabalho, solicitamos V.S&ordf; compare&ccedil;a ao Departamento  de Pessoal do(a) <strong><?=$row_master['razao']?></strong> <u>no dia <span class="red"><b><?=$data_demi?></b></span> at&eacute; &agrave;s 17h</u> munido de sua CTPS-Carteira de  Trabalho e Previd&ecirc;ncia Social para as devidas anota&ccedil;&otilde;es e o pagamento das  parcelas decorrentes.</p>
<p>&nbsp;</p>
        <p><strong><?=$row_pro['nome']?></strong></p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>Ciente:&nbsp;_____  /&nbsp;_____ /________.</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>__________________________<br />
          Assinatura do Empregado</p>
      </center>
      </u>
      </p>
      
     
      <p align="center" style='text-align:right'>&nbsp;</p>
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