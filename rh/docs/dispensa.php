<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "../../conn.php";
include "../../funcoes.php";
include "../../classes/regiao.php";

$REG = new regiao();

$id_clt = $_REQUEST['clt'];
$tab = $_REQUEST['tab'];
$pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['id_reg'];

$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$id_reg'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]' ");
$row_master = mysql_fetch_assoc($qr_master);


$result_bol = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada,date_format(data_saida, '%d/%m/%Y')as data_saida FROM rh_clt where id_clt = '$id_clt'");
$row = mysql_fetch_array($result_bol);

$result_curso = mysql_query("Select * from curso where id_curso = $row[id_curso]");
$row_curso = mysql_fetch_array($result_curso);

$result_pro = mysql_query("Select * from projeto where id_projeto = $pro");
$row_pro = mysql_fetch_array($result_pro);

$data = date('d/m/Y');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>COMUNICADO DE DISPENSA</title>
<link href="../../net1.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="../../js/ramon.js"></script>
<style type="text/css">
<!--
div.MsoHeader {font-size:12.0pt;
	font-family:"Arial","sans-serif";}
li.MsoHeader {font-size:12.0pt;
	font-family:"Arial","sans-serif";}
p.MsoHeader {font-size:12.0pt;
	font-family:"Arial","sans-serif";}
.style1 {color: #003300}
.style3 {
	font-size: 12px;
	font-family: Arial, Helvetica, sans-serif;
}
.style4 {font-family: Arial, Helvetica, sans-serif}
.style5 {color: red}
.style9 {font-size: 14}
.style13 {font-size: 14px}
.style14 {
	font-size: 13px;
	font-weight: bold;
}
.style15 {
	font-family: "Univers 45 Light", "sans-serif";
	font-size: 14.0pt;
	color: red;
}
-->
</style>

</head>

<body>
<?php
if(empty($_REQUEST['data_demi'])){
	$d = explode("-",date('Y-m-d'));
	$data_fim = date('d/m/Y', mktime(0,0,0, $d[1], $d[2] + 30, $d[0]));
?>
	<form method="post" action="dispensa.php" name="form">
	<table width="400" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="bordaescura1px">
	<tr height="35">
	  <td height="28" colspan="2" class="show">> Dispensa</td>
	  </tr>
	<tr height="35">
	  <td width="141" height="28" align="right" class="secao">Data do aviso:</td>
	  <td width="257" height="28">&nbsp;&nbsp;
      <input type="text" name="data_aviso" id="data_aviso" size="12" onkeyup="mascara_data(this)" class="campotexton" value="<?=date('d/m/Y')?>"/></td>
	  </tr>
	<tr height="35">
	  <td height="28" class="secao" ><span class="red">Data da demiss&atilde;o:</span></td>
	<td height="28">&nbsp;&nbsp;&nbsp;<input type="text" name="data_demi" id="data_demi" size="12"  value="<?=$data_fim?>" onkeyup="mascara_data(this)" class="campotexton"/></td></tr>
	<tr>
	  <td height="28" class="secao" >Aviso pr&eacute;vio:</td>
	  <td>&nbsp;&nbsp;
      <select name="aviso" id="aviso" class="campotexton">
        <option>Trabalhado</option>
        <option>Indenizado</option>
      </select>
      
      </td>
	  </tr>
	<tr><td height="100" colspan="2" align="center">
	        <div style="text-align:left; margin-left:15px;">
               <label><input type="radio" value="991" name="tipo_rescisao">Rescisão  sem justa causa</label><br>
                 
                        <label><input type="radio" value="994" name="tipo_rescisao">Rescisão  por justa causa</label><br>
                        <label><input type="radio" value="992" name="tipo_rescisao">Rescisão por término de contrato</label><br>
			<label><input type="radio" value="993" name="tipo_rescisao">Outros motivos de rescisão</label>
			</div>
		  </td></tr>
        <?php if (!empty($row['observacao'])) { ?>
            <tr>
                <td colspan="2" class="cor-4">
                    <h4 style="margin-left:15px; margin-right:15px;">Observações:</strong></h4>
                    <p style="margin-left:15px; margin-right:15px;"><?=$row['observacao']?></p>
                </td>
            </tr>
        <?php } ?>
	<tr height="30"><td height="37" colspan="2" align="center"><input type="submit" value="Enviar" class="botao" /></td></tr></table>
	<input type="hidden" name="clt" id="clt" value="<?=$id_clt?>"/>
	<input type="hidden" name="pro" id="pro" value="<?=$pro?>"/>
	<input type="hidden" name="id_reg" id="id_reg" value="<?=$id_reg?>"/>
</form>

<?php exit;
	
} else {
	
	$data_demi 	= $_REQUEST['data_demi'];
	$data_aviso = $_REQUEST['data_aviso'];
	$pro 		= $_REQUEST['pro'];
	$id_reg 	= $_REQUEST['id_reg'];
	
	// PEQUENA FUNÇÃO PARA QUEBRAR A DATA 
	if(strstr($data_demi, "/")){
		$Dat = implode('-', array_reverse(explode('/', $data_demi)));
	}
	
	if(strstr($data_aviso, "/")){
		$Dat2 = implode('-', array_reverse(explode('/', $data_aviso)));
	}
	//
	
	//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
	$data_cad = date('Y-m-d');
	$user_cad = $_COOKIE['logado'];
	
	$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '12' AND id_clt = '$id_clt'");
	$num_row_verifica = mysql_num_rows($result_verifica);
	
        
        
        if($_COOKIE['logado'] != 9){
	if(empty($num_row_verifica)) {

                    mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('12','$id_clt','$data_cad', '$user_cad')");

            } else {

                    mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$id_clt' and tipo = '12'");

            }
        }
        
        
	//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
	if($_COOKIE['logado'] != 9){
	// GRAVANDO NA RH_CLT A DATA DO PEDIDO DE EMISSÃO
	mysql_query("UPDATE rh_clt SET data_aviso = '$Dat2', data_demi = '$Dat', data_saida = '$Dat', status = '200' WHERE id_clt = '$id_clt' LIMIT 1");
	
	// GRAVANDO NA TABELA RH EVENTOS
//	mysql_query("INSERT INTO rh_eventos (id_clt, id_regiao, id_projeto, cod_status, data, status, status_reg) VALUES ('$id_clt', '$id_reg', '$pro', '".$_REQUEST['tipo_rescisao']."', '$Dat', '1', '1')") or die(mysql_error());
        }
	#RESOLVENDO QUAL ARQUIVO VAI SER CHAMADO
	//-- ENCRIPTOGRAFANDO A VARIAVEL
	$link = encrypt("$id_clt&$pro&$id_reg&$data_demi&$data_aviso"); 
	$link = str_replace("+","--",$link);
	// -----------------------------
	
	if($_REQUEST['tipo_rescisao'] == 991 and $_REQUEST['aviso'] == "Trabalhado"){
		echo "<script> location.href = 'avisotrabalhado.php?enc=$link'; </script>";
	}elseif($_REQUEST['tipo_rescisao'] == 991 and $_REQUEST['aviso'] == "Indenizado"){
		echo "<script> location.href = 'avisotraindenizado.php?enc=$link'; </script>";
	}elseif($_REQUEST['tipo_rescisao'] == 992 or $_REQUEST['tipo_rescisao'] == 993){
		echo "<script> location.href = 'avisoterminodecontrato.php?enc=$link'; </script>";
	}elseif($_REQUEST['tipo_rescisao'] == 994){
                echo "<script> location.href = 'dispensa_justa_causa.php?enc=$link'; </script>";
        }
	
}
?>

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
  style='font-size:12.0pt;color:red'><?php print "$row_pro[nome] / $row_pro[regiao] <br><br> $row[locacao]"; ?></span></b></p>    </td>

    <td width="21%">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3"><div style="padding:7px;">
      <p align="center" style='text-align:right'><span class="style4">
       <?php
        echo $REG -> RegiaoLogado();
		echo ", ";
		echo $REG -> MostraDataCompleta($data_demi);
        ?>
      
      </span></p>
      <p class="style3"><br />
        A(o) Sr(a),<br />
        <b><span style="font-size:11.0pt;line-height:150%;font-family:Univers 45 Light sans-serif; color:red"><?php print "$row[nome]"; ?></span></b><br />
      </p>
      <p>Portador(a) da Carteira de Trabalho <strong><?='Número: '.$row['campo1'].' / Série: '.$row['serie_ctps'].' / UF: '.$row['uf_ctps']?></strong></p>

      <p class="style3"><span class="style14">Ref: COMUNICADO DE DISPENSA.</span></p>

      <p class="style3"><br />

        Vimos pela presente, comunicar-lhe que a partir dessa data <b><span style="font-size:11.0pt;line-height:150%;font-family:Univers 45 Light sans-serif; color:red"><?=$data_demi?></span></b>,  rescindimos seu contrato de trabalho, conforme artigo 477 da CLT.  
<?php
if ($id_reg == 11){
	$data = $row_data_saida['data_saida30']; 
	$data30 =explode("-",$data);
	$d = $data30[2];
	$m = $data30[1];
	$a = $data30[0];
	echo 'Cumprindo aviso prévio até o dia <span style="font-size:11.0pt;line-height:150%;font-family:&quot;Univers 45 Light&quot;,&quot;sans-serif&quot;;color:red">'.$d.'/'.$m.'/'.$a.'</span> quando encerrara suas atividades.';
}
?>  <br />

        Por gentileza compare&ccedil;a ao Departamento Pessoal do <?php 
$nomEmp= new empresa();
$nomEmp -> nomeEmpresa2(); 
?>, para recebimento das verbas rescis&oacute;rias.<br />

      </p>

      <p class="style3">Sem mais,  agradecemos.<br />

      </p>

      <p class="style3">Atenciosamente,<br />
        <br />
      </p>

      <p align="center" class="style3">__________________________________________<br />

        <?php 
$nomEmp2= new empresa();
$nomEmp2 -> nomeEmpresa2(); 
?></p>

      <p class="style3"><br />
        Ciente, <span class="style3">
        <?php
        echo $REG -> RegiaoLogado();
		echo ", ";
		echo $REG -> MostraDataCompleta($data_demi);
        ?>
        </span>.<br />
        <br />
      </p>

      <p align="center" class="style3">_________________________________________.<br />

      <b><span style="font-size:9.0pt;line-height:150%;font-family:Univers 45 Light sans-serif; color:red"><?php print "$row[nome]"; ?></span></b></p>

    
    
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

<?php

}

?>