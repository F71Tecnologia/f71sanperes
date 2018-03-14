<?
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}
//PEGANDO O ID DO CADASTRO
include "../../conn.php";

$clt = $_REQUEST['clt'];
$id_pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['id_reg'];
$STATUS = $_REQUEST['status'];

$row_regiao_id = mysql_result(mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$id_reg'"),0);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao_id'");
$row_master = mysql_fetch_assoc($qr_master);


if ($STATUS == 'Gerar'){
	$data = $_REQUEST['data'];
	$dia = $_REQUEST['dia'];
	$obs = $_REQUEST['obs'];
	
	$data = explode("/", $data);
	$d=$data[0];
	$m=$data[1];
	$a=$data[2];

	//Data da suspenção
	$data = date("Y-m-d", mktime(0, 0, 0, $m, $d, $a));
	
	

	if (($data != '') and ($dia != '') and ($obs != '')){
		//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
		$data_cad = date('Y-m-d');
		$user_cad = $_COOKIE['logado'];

	    mysql_query("INSERT INTO rh_doc_status(tipo,obs,obs2,data_obs,id_clt,data,id_user) VALUES ('9','$obs','$dia','$data','$clt','$data_cad', '$user_cad')");

		//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
		
		echo "<script>location.href='suspencao.php?clt=$clt&pro=$id_pro&id_reg=$id_reg&data=$data&dia=$dia&obs=$obs'</script>";	
	
	} else {
		echo "<script> alert('Todos os campos são de preenchimento obrigatório.'); </script>";
	}
	
}



$result = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$clt'");
$row = mysql_fetch_array($result);

$result_emp = mysql_query("SELECT * FROM rhempresa WHERE id_empresa = '$row[rh_vinculo]'");
$row_emp = mysql_fetch_array($result_emp);

?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SUSPEN&Ccedil;&Atilde;O</title>
<link href="../../net.css" rel="stylesheet" type="text/css" />
<link href="../../net1.css" rel="stylesheet" type="text/css">


<script language=\"JavaScript\">

function formatar(mascara, documento){ 
var i = documento.value.length; 
var saida = mascara.substring(0,1); 
var texto = mascara.substring(i) 
if (texto.substring(0,1) != saida){ 
documento.value += texto.substring(0,1); 
} 
} 

function mascara_data(d){  
var mydata = '';  
data = d.value;  
mydata = mydata + data;  
if (mydata.length == 2){  
mydata = mydata + '/';  
d.value = mydata;  
}  
if (mydata.length == 5){  
mydata = mydata + '/';  
d.value = mydata;  
}  
if (mydata.length == 10){  
verifica_data(d);  
}  
} 

</script>
</head>
<body>
<table width="700" border="0" align="center" cellpadding="5" cellspacing="0" class="bordaescura1px">
  <tr>
    <td width="680" colspan="2" align="center" bgcolor="#FFFFFF" class="campotexto">
<img src="../../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif"/></td>
  </tr>
  <tr>
    <td colspan="2" align="center" bgcolor="#D6D6D6"><span class="campotexto"><strong><br />
    </strong></span><span class="title"><strong>Emiss&atilde;o de suspen&ccedil;&atilde;o para <?=$row['nome']?> </strong></span><span class="campotexto"><strong><br />
    <br />
    </strong></span></td>
  </tr>
  <tr>
    <td colspan="2" align="center" bgcolor="#FFFFFF">      <blockquote>
    <form name="suspencao" action="form_suspencao.php?status=gravar" method="get">
        <p class="linha">
          <label>
            Data<br>
            <input name="data" type="text" id="data" size="11" maxlength="10" onKeyUp="mascara_data(this)" class='campotexto'   >
            <br>Dias<br>
            <input name="dia" type="text" id="dia" size="2" maxlength="2" class='campotexto' >
            <br>Motivo<br>
            <textarea name="obs" id="obs" cols="60" rows="10" class="campotexto" ></textarea>
          </label>
          <label>
          <br>
            <input name="clt" type="hidden" id="clt" value="<?=$row['0']?>">
            <input name="pro" type="hidden" id="clt" value="<?=$id_pro?>">
            <input name="id_reg" type="hidden" id="clt" value="<?=$id_reg?>">
            <input type="submit" name="status" id="status" value="Gerar">
          </label>
        </form>
        </p>
        <p class="linha"><span class="linha"><br />
        </span> </p>
    </blockquote>    </td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
</table>

</body>
</html>