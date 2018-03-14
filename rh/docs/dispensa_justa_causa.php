<?php
if(empty($_COOKIE['logado'])){
	print "Efetue o Login<br><a href='login.php'>Logar</a> ";
	exit;
}

include "../../conn.php";
include "../../funcoes.php";
include "../../classes/regiao.php";
include "../../empresa.php";
include "../../wfunction.php";


//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 
	
$decript = explode("&",$link);
#clt=$id_clt&pro=$pro&id_reg=$id_reg&data_demi=$data_demi&data_aviso=$data_aviso
$id_clt 	= $decript[0];
$pro 		= $_REQUEST['pro'];
$id_reg 	= $_REQUEST['id_reg'];
$data_demi 	= $_REQUEST['data_demi'];
$data_aviso         = $_REQUEST['data_aviso'];
//RECEBENDO A VARIA//VEL CRIPTOGRAFADA

$data_atual = date('d/m/Y');
$dia = date('d');
$mes = date('m');
$ano = date('Y');

$qr_clt = mysql_query("select A.nome, A.campo1, A.serie_ctps, A.uf_ctps, DATE_FORMAT(A.data_ctps,'%d/%m/%Y') as data_ctps, B.nome as nome_projeto, C.regiao,
                    A.rg
                    FROM rh_clt as A
                    INNER JOIN projeto as B
                    ON A.id_projeto = B.id_projeto
                    INNER JOIN regioes as C
                    ON C.id_regiao = A.id_regiao
                     WHERE id_clt = '$id_clt'");
$row_clt = mysql_fetch_assoc($qr_clt);


$qr_empresa = mysql_query("select * from rhempresa WHERE id_regiao = '$id_reg' AND id_projeto = '$id_pro'");
$row_empresa = mysql_fetch_assoc($qr_empresa);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>COMUNICADO DE DISPENSA</title>
<link href="../../net1.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="../../js/ramon.js"></script>
</head>
    <style>
        h3{ text-align: center;
            margin-bottom: 30px;}
        p{padding-left: 30px;
          padding-right: 25px;}
        p.ident{ text-indent: 20px;}
             p.espaco{margin-top:40px;
               margin-bottom:40px; }
    </style>

<body>

<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="bordaescura1px" style="margin-bottom:100px; height:1000px">
    <tr height="26">
    <td width="21%" ><p class="MsoHeader" align="center" style='text-align:center'><strong><span class="style5">
  </td>
    <td width="58%"><p class="MsoHeader" align="center" style='text-align:center'><b><span
  style='font-size:12.0pt;color:red'><?=$row_clt[nome_projeto]?><?=" / $row_clt[regiao] <br><br> $row[locacao]"?></span></b></p>    </td>

    <td width="21%">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3">
            
            <h3>NOTIFICAÇÃO DE DEMISSÃO COM JUSTA CAUSA</h3>
            <p>De: <strong><?php echo $row_empresa['nome'];?> - CNPJ: <?php echo $row_empresa['cnpj'];?></strong></p>
           <p>Para: <strong><?php echo $row_clt['nome'].' - RG:'.$row_clt['rg']?></strong></p>
           
  <p>  Ref.: "Dispensa por Justa Causa"</p>
  <p class="ident">Comunicamos que a partir desta data declaramos rescindido seu contrato de trabalho, por justa causa, nos termos do artigo 482 da CLT, 
      por sua conduta inapropriada com e totalmente em desacordo com as políticas da Empresa e os valores pela mesma impetrados em nosso 
      estatuto e apresentados a todos os nossos colaboradores. 
</p>
  <p  class="ident">Comunicamos ainda que o senhor deverá comparecer em nossa empresa na data de  <strong><?php echo $data_atual; ?></strong>, para homologação do termo de rescisão do contrato
     de trabalho, na forma da lei.
</p>
  <p>Atenciosamente,</p>
  <p>Rio de janeiro, <?php echo $dia.' de '.  mesesArray($mes).' de '.$ano;?>.</p>
  
  <p class="espaco"><strong>ASSINATURA E NOME DO RESPONSÁVEL</strong></p>
  <p>___________________________________________</p>
  
  <p>( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</p>
  
  <p class="espaco"><strong><?php echo $row_empresa['nome'];?></strong></p>
  <p>Ciente em ____/____/____</p>
  <p class="espaco"><strong>ASSINATURA E NOME DO EMPREGADO</strong></p>
  <p>__________________________________________</p>
  <p>( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</p>
  
   <p  class="espaco"><strong><?php echo $row_clt['nome'];?>         </strong></p>
  <p><strong>CTPS Nº: <?php echo $row_clt['campo1']?></strong> </p>
  <p><strong>SERIE: <?php echo $row_clt['serie_ctps']?></strong> </p>
  <p><strong>UF: <?php echo $row_clt['uf_ctps']?> </strong></p>
  <p><strong>EMITIDA EM: <?php echo $row_clt['data_ctps']?> </strong></p>
  
    </td>
  </tr>


</table>

</body>
</html>
