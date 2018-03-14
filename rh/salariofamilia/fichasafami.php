<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}
//PEGANDO O ID DO CADASTRO
include "../../conn.php";
$id = 1;
$id_clt = $_REQUEST['clt'];
$id_pro = $_REQUEST['pro'];
$id_regiao = $_REQUEST['id_reg'];
$id_user = $_COOKIE['logado'];

$result = mysql_query(" SELECT *, date_format(data_entrada, '%d/%m/%Y')as nova_data FROM rh_clt where id_clt = $id_clt ", $conn);
$row = mysql_fetch_array($result);

$vinculo_tb_rh_clt_e_rhempresa = $row['rh_vinculo'];

$result_empresa= mysql_query("SELECT * FROM rhempresa WHERE id_empresa = $vinculo_tb_rh_clt_e_rhempresa");
$row_empresa = mysql_fetch_array($result_empresa);

$result_pro = mysql_query("SELECT * FROM projeto where id_projeto = '$id_pro' ");
$row_pro = mysql_fetch_array($result_pro);

$result_dependente = mysql_query("SELECT * , CURDATE(), 
DATE_ADD(data1, INTERVAL '14' YEAR) AS data_baixa1,
DATE_ADD(data2, INTERVAL '14' YEAR) AS data_baixa2,
DATE_ADD(data3, INTERVAL '14' YEAR) AS data_baixa3,
DATE_ADD(data4, INTERVAL '14' YEAR) AS data_baixa4,
DATE_ADD(data5, INTERVAL '14' YEAR) AS data_baixa5,
(YEAR(CURDATE())-YEAR(data1)) - (RIGHT(CURDATE(),5)<RIGHT(data1,5)) AS idade1,
(YEAR(CURDATE())-YEAR(data2)) - (RIGHT(CURDATE(),5)<RIGHT(data2,5)) AS idade2,
(YEAR(CURDATE())-YEAR(data3)) - (RIGHT(CURDATE(),5)<RIGHT(data3,5)) AS idade3,
(YEAR(CURDATE())-YEAR(data4)) - (RIGHT(CURDATE(),5)<RIGHT(data4,5)) AS idade4,
(YEAR(CURDATE())-YEAR(data5)) - (RIGHT(CURDATE(),5)<RIGHT(data5,5)) AS idade5
FROM dependentes WHERE id_bolsista = '$id_clt' AND id_projeto = '$id_pro'");
$row_dependente = mysql_fetch_array($result_dependente);

$result_dependente2 = mysql_query("SELECT *, 
date_format(data1, '%d/%m/%Y')AS data_nasc1 ,
date_format(data2, '%d/%m/%Y')AS data_nasc2 ,
date_format(data3, '%d/%m/%Y')AS data_nasc3 ,
date_format(data4, '%d/%m/%Y')AS data_nasc4 ,
date_format(data5, '%d/%m/%Y')AS data_nasc5 
FROM dependentes WHERE id_bolsista = '$id_clt' AND id_projeto = '$id_pro'");
$row_dependente2 = mysql_fetch_array($result_dependente2);

//Atualizando a tabela rh_doc_status para mostrar a data em que foi gerado a última ficha de cadastro do salário família
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '17' and id_clt = '$id_clt'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('17','$id_clt','$data_cad', '$user_cad')");
}else{
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$id_clt' and tipo = '17'");
}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SOLICITA&Ccedil;&Atilde;O DE SAL&Aacute;RIO FAM&Iacute;LIA</title>
<link href="../../net.css" rel="stylesheet" type="text/css" />
<link href="../../net1.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="1000px" border="0" align="center" cellpadding="5" cellspacing="0" class="bordaescura1px">
  <tr>
    <td width="680" colspan="2" align="center" bgcolor="#FFFFFF" class="campotexto">
<?php
include "../../empresa.php";
$img= new empresa();
$img -> imagemCNPJ();
?></td>
  </tr>
  <tr>
    <td colspan="2" align="center" bgcolor="#D6D6D6"><span class="campotexto"><strong><br />
    </strong></span><span class="title"><strong>Ficha Individual de Sal&aacute;rio Fam&iacute;lia</strong></span><span class="campotexto"><strong><br />
    <br />
    </strong></span></td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">      <blockquote>
        <p class="linha"><strong><br />
        Empresa:</strong> <span class="style2"><?=$row_empresa['nome']?></span>                       <strong>CNPJ</strong>: <span class="style2"><?=$row_empresa['cnpj']?><br />
        <br />
        </span><strong>Endere&ccedil;o:</strong> <span class="style2"><?=$row_empresa['endereco']?> </span>                                  <br />
          <br />
          <strong>Funcion&aacute;rio:</strong> <span class="style2"><?=$row['nome']?></span>&nbsp;<strong>C&oacute;digo:</strong> <span class="style2"><?=$row['id_clt']?></span> <strong>Admiss&atilde;o:</strong> <span class="style2"><?=$row['nova_data']?></span><strong><br />
          <br />
        CTPS:</strong> <span class="style2"><?=$row['campo1']?> - <?=$row['serie_ctps']?> / <?=$row['uf_ctps']?></span><br />
        <br />
        <strong>Endere&ccedil;o:</strong> <span class="style2"><?=$row['endereco']?>  </span>                                    <br />
          <br />
          <strong>Cidade: <span class="style2"><?=$row['cidade']?></span></strong> <strong>- Estado:</strong> <span class="style2"><?=$row['uf']?></span>  <br />
        <br />
        </p>
</blockquote>    </td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle" bgcolor="#FFFFFF" class="linha">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="center" bgcolor="#D6D6D6" class="linha"><strong class="linha">Filhos Menores de 14 Anos&nbsp; (dados extra&iacute;dos das certid&otilde;es)</strong></td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="3" cellspacing="0">
      <tr>
        <td align="center" bgcolor="#CCCCCC" style="width:150px;"><span class="linha"><strong>Nome</strong></span></td>
        <td align="center" bgcolor="#CCCCCC"><p><span class="linha"><strong>Nascimento</strong></span></p></td>
        <td align="center" bgcolor="#CCCCCC"><span class="linha"><strong>Local Nascimento</strong></span></td>
        <td align="center" bgcolor="#CCCCCC"><span class="linha"><strong>Cart&oacute;rio</strong></span></td>
        <td align="center" bgcolor="#CCCCCC"><span class="linha"><strong>N&ordm;. Registro</strong></span></td>
        <td align="center" bgcolor="#CCCCCC"><span class="linha"><strong>N&ordm;. Livro</strong></span></td>
        <td align="center" bgcolor="#CCCCCC"><span class="linha"><strong>N&ordm;. Folha</strong></span></td>
        <td align="center" bgcolor="#CCCCCC"><span class="linha"><strong>Recebimento Certid&atilde;</strong>o</span></td>
        <td align="center" bgcolor="#CCCCCC"><span class="linha">Encerramento sal&aacute;rio Fam&iacute;lia</span></td>
        </tr>
<?
		
		//Condição para andar pelos campos data1, nome1 ... data2, nome2.
		for($cont = 1;$cont<=5;$cont++){
			$nome = $row_dependente['nome'.$cont];
			$data_baixa = $row_dependente['data_baixa'.$cont];
			$data_nasc = $row_dependente2['data_nasc'.$cont];
			$idade = $row_dependente['idade'.$cont];
			//Caso alguma das variáveis sejam vazias, não irá exibir o dependente.
			if (($nome != '') or ($data != '0000-00-00')){
				//Formatando a data retirada do banco de dadose já com sua data de baixa calculada.
				if($idade < 14){
					$data_baixa= explode("-",$data_baixa); 
					$d = $data_baixa[2];
					$m = $data_baixa[1];
					$a = $data_baixa[0];
					
					$data_baixa = date("d/m/Y", mktime (0, 0, 0, $m  , $d , $a));
					
					echo '<tr>';					
					echo '<td align="center">'.$nome.'</td>';
					echo '<td align="center">'.$data_nasc.'</td>';
					echo '<td align="center">___________________</td>';
					echo '<td align="center">___________________</td>';
					echo '<td align="center">___________________</td>';
					echo '<td align="center">___________________</td>';
					echo '<td align="center">___________________</td>';
					echo '<td align="center">___________________</td>';
					echo '<td align="center">'.$data_baixa.'</td>';
					echo '</tr>';
				}
			}
		}
	?>
            
      <tr>
        </tr>
    
    </table></td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
</table>
</body>
</html>