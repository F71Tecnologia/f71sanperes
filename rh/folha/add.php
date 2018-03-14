<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a> ";
exit;
}

include "../../conn.php";
include "../../funcoes.php";
include "../vtfuncao/dias_trab.php";

$obj = new dias_trab();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: Intranet :: Inicio da Folha Sint&eacute;tica</title>
<link href="../../net1.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?
if(empty($_REQUEST['id_clt'])){

//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 

$decript = explode("&",$link);

$regiao = $decript[0];
$folha = $decript[1];
//RECEBENDO A VARIAVEL CRIPTOGRAFADA

$id_user = $_COOKIE['logado'];

$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

$result_master = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$result_folha = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim FROM rh_folha where id_folha = '$folha'");
$row_folha = mysql_fetch_array($result_folha);

$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$row_folha[projeto]'");
$row_projeto = mysql_fetch_array($result_projeto);

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$MesFolhaINT = (int)$row_folha['mes'];
$mes_da_folha = $meses[$MesFolhaINT];

$ano = date("Y");
$mes = date("m");
$dia = date("d");

$data = date("d/m/Y");

?>


<table width="95%" border="0" align="center">
  <tr>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><br />
      <table width="90%" border="0" align="center">
      <tr>
        <td colspan="3" align="center" valign="middle" bgcolor="#666666" class="title"><span class="style1"><img src="../../imagens/logomaster<?=$row_user['id_master']?>.gif" alt="" width="110" height="79"><br />
             <?=$row_master['razao']?>
        </span><br />
        </td>
      </tr>
      <tr class="linha">
        <td width="29%" height="14" align="center" valign="middle" bgcolor="#CCCCCC">Data de Processamento:
          <?=$row_folha['data_proc2']?></td>
        <td width="43%" align="center" valign="middle" bgcolor="#CCCCCC">CNPJ :  <?=$row_master['cnpj']?></td>
        <td width="28%" align="center" valign="middle" bgcolor="#CCCCCC">&nbsp;</td>
      </tr>
    </table>
      <br />
     <span class="title">Folha -

      <?=$mes_da_folha?> / <?=$ano?></span><br />
      <br />
      <form action="" method="post" name="Form" onSubmit="return ValidaForm()">
        <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td width="3%" height="25" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">
          <input type="checkbox" name="CheckTodos" onClick="selecionar_tudo();" checked >&nbsp;</td>
          <td width="5%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">C&oacute;digo</td>
          <td width="33%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Nome </td>
          <td width="28%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Atividade</td>
          <td width="31%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Unidade</td>
          </tr>
        
        <?php
		
		$cont = 0;
		
		$resultCltSL = mysql_query("SELECT * FROM rh_folha_proc where id_folha = '$folha' ORDER BY nome ASC");
		
		  while($rowCltSL = mysql_fetch_array($resultCltSL)){
		  
		  $result_clt = mysql_query("SELECT * FROM rh_clt where id_clt = '$rowCltSL[id_clt]'");
		  $row_clt = mysql_fetch_array($result_clt);
		  
		  $result_curso = mysql_query("SELECT * FROM curso where id_curso = '$row_clt[id_curso]'");
		  $row_curso = mysql_fetch_array($result_curso);
		  
		  if($rowCltSL['status'] == 2){ $checked = "checked"; }else{ $checked = ""; }
	  
		  //---- EMBELEZAMENTO DA PAGINA ----------------------------------
			if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
			$nome = str_split($row_clt['nome'], 35);
			$nomeT = sprintf("% -40s", $nome[0]);
			$bord = "style='border-bottom:#000 solid 1px;'";
		  
			$nomeC = str_replace("CAPACITANDO EM","CAP. EM",$row_curso['nome']);
		//-----------------
		  
		  print"
		  <tr bgcolor=$color height='20'>
		  <td align='lefth' valign='middle' $bord colspan=3><label>&nbsp;
		  <input name='id_clt[]' id='id_clt' type='checkbox' value='$rowCltSL[id_clt]' $checked>
		  &nbsp;&nbsp;$row_clt[campo3] - $nomeT</label></td>
          <td align='lefth' valign='middle' $bord>$nomeC</td>
          <td align='lefth' valign='middle' $bord>$row_clt[locacao]
		  </td>
		  </tr>";
		  
		  $cont ++;
		  
		  }
		  
		?>
        </table>


      <input type="hidden" name="id_regiao" value="<?=$regiao?>">
      <input type="hidden" name="id_projeto" value="<?=$row_folha['projeto']?>">
      <input type="hidden" name="id_folha" value="<?=$folha?>">
      <input type="hidden" name="data_proc" value="<?=$row_folha['data_proc']?>">
      <input type="hidden" name="mes" value="<?=$row_folha['mes']?>">
      <input type="hidden" name="vale" value="<?=$vale?>">
      <input type="hidden" name="total" value="<?=$cont?>">
      <img src='../../imagens/carregando/loading.gif' border='0' style="display:none">
      <br>
      <input type="submit" value="Enviar">

<script language="javascript" type="text/javascript">

function selecionar_tudo(a){
	var contaForm = document.Form.elements.length;
	contaForm = contaForm - 7;
    var campo = document.Form;  
    var i; 

	for (i=0 ; i<contaForm ; i++){
		if (campo.elements[i].id == "id_clt") {
			campo.elements[i].checked = campo.CheckTodos.checked;
		}
	}
	
	
}

function ValidaForm(){
	var Nocheck = 0;
	var Yescheck = 0;
	var d = document.Form;
	var contaForm = d.elements.length;
	contaForm = contaForm - 8;
	
	for (i=0 ; i<contaForm ; i++){
		if (d.elements[i].id == "id_clt") {
			if (!d.elements[i].checked){
				Yescheck ++;
			}else{
				Nocheck++;
			}
		}
	}
	
	if(Nocheck == 0){
		alert ("Escolha ao menos 1 CLT");
		return false;
	}
	
}

</script>

      </form>
    </td>
  </tr>
</table>

<?php
}else{

$clt = $_REQUEST['id_clt'];

$id_regiao = $_REQUEST['id_regiao'];
$id_projeto = $_REQUEST['id_projeto'];
$id_folha = $_REQUEST['id_folha'];
$total_CLTs = $_REQUEST['total'];

$cont = 0;
$contCLT = count($clt);

$result_folha = mysql_query("SELECT * FROM rh_folha where id_folha = '$id_folha'");
$row_folha = mysql_fetch_array($result_folha);

echo "<br><br>";
echo "<div class='style7' align='center'><img src='../../imagens/carregando/loading.gif' border='0'><br>Aguarde...<br> Estamos trabalando 
em sua solicitação</div><br><br>";

mysql_query("UPDATE rh_folha SET clts = '$contCLT' WHERE id_folha = '$id_folha' LIMIT 1 ;");
mysql_query("UPDATE rh_folha_proc SET status = '1', a7001 = '0' WHERE id_folha = '$id_folha'");
mysql_query("UPDATE rh_movimentos_clt SET status_folha='0' WHERE mes_mov='$row_folha[mes]'");


for($i=0 ; $i < $contCLT; $i++){

	$result_clt = mysql_query("SELECT * FROM rh_clt where id_clt = '$clt[$cont]'");
	$row_clt = mysql_fetch_array($result_clt);
	
	$result_curso = mysql_query("SELECT * FROM curso where id_curso = '$row_clt[id_curso]'");
	$row_curso = mysql_fetch_array($result_curso);

	$cont ++;
	
	//SE A OPÇÃO DE VALE NÕA ESTIVER MARCADA PARA O CLT, NÃO VAI CHAMAR A FUNÇÃO VALE

		$obj -> calcperiodo($row_folha['data_inicio'], $row_folha['data_fim'],$row_clt['0']);
		$DIASTRABALHADOS = $obj ->imprimir();
		$ValorVale = $obj ->imprimir_valor();
		$ValorVale = number_format($ValorVale,2,".","");
		
		$PercentVale = $row_curso['salario'] * 0.06;
		
		if($PercentVale <= $valor){
			$ValorFinal = $PercentVale;
		}else{
			$ValorFinal = $valor;
		}
		
		$valor = number_format($ValorFinal,2,".","");
	
		//$ValorVale = '2.22';
		
	
	//COLOCANDO O STATUS 2 NOS CLTS MARCADOS NA TELA ANTERIOR E ACHRESCENTANDO O VALOR DO VALE
	mysql_query("UPDATE rh_folha_proc SET status = '2', a7001 = '$ValorVale' WHERE id_clt = '$row_clt[0]' and id_folha = '$id_folha' LIMIT 1");
	
	//COLOCANDO STATUS 1 NOS MOVIMENTOS DOS CLTS SELECIONADOS PARA ESSA FOLHA
	mysql_query("UPDATE rh_movimentos_clt SET status_folha=1 WHERE id_clt='$clt[$i]' and mes_mov='$row_folha[mes]'") 
	or die ("Erro <br><br>".mysql_error());
	
	$ValorVale = ""; 
}

//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkreg = encrypt("$id_regiao&$id_folha"); 
$linkreg = str_replace("+","--",$linkreg);
// -----------------------------

print "
<script>
location.href=\"sintetica.php?enc=$linkreg\"
</script>";


}
?>
<p>&nbsp;</p>
</body>
</html>