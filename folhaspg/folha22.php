<?php
ob_start();

if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a>";
exit;
}

include('../conn.php');
include('../funcoes.php');
include('../classes/listaparticipantes.php');

if($_GET['go_pagina'] == true) {
	$nova_pagina = $_GET['pagina'] - 1;
	header("Location: folha_new2.php?pagina=$nova_pagina&enc=$_GET[enc]");
	exit;
}

$id_user = $_COOKIE['logado'];
$Part    = new participantes();

// Recebendo a Variável Criptografada
$enc = $_REQUEST['enc'];
list($regiao, $folha, $st) = explode('&', decrypt(str_replace('--', '+', $enc)));

// Selecionando o Usuário
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user    = mysql_fetch_array($result_user);

// Verificando o Master
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master    = mysql_fetch_array($result_master);

// Consulta da Folha
$result_folha = mysql_query("SELECT *, date_format(data_proc, '%d/%m/%Y') AS data_proc2, date_format(data_inicio, '%d/%m/%Y') AS data_inicio2, date_format(data_fim, '%d/%m/%Y') AS data_fim2 FROM folhas WHERE id_folha = '$folha'");
$row_folha    = mysql_fetch_array($result_folha);

// Consulta do Projeto da Folha
$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_folha[projeto]'");
$row_projeto    = mysql_fetch_array($result_projeto);

// Verificação de Tabela
if($row_folha['contratacao'] == 1) {
	$tabela_now = 'folha_autonomo';
} else {
	$tabela_now = 'folha_cooperado';
}

// Verificação para Insert da Folha
if(!empty($_REQUEST['m'])) {
	
	$folha_ini = $row_folha['data_inicio'];
	$folha_fim = $row_folha['data_fim'];
	
	// Acréscimo se for Cooperado
	if($row_folha['contratacao'] == 3 or $row_folha['contratacao'] == 4) {
		$acrescimo = "AND id_cooperativa = '$row_folha[coop]'";
	}
	
	// Consulta para Total de Participantes
	$qr_total = mysql_query("SELECT * FROM autonomo 
							  WHERE id_projeto = '$row_folha[projeto]'
								AND tipo_contratacao = '$row_folha[contratacao]' $acrescimo 
								AND (status = '1' 
									 OR  data_saida > '$row_folha[data_inicio]' 
									 AND data_saida <= '$row_folha[data_fim]' 
									 AND status = '0')");
	$total = mysql_num_rows($qr_total);
	
	// Itens
	$partes	= ceil($total / 200);
	$parte	= $_GET['parte'];
	$inicio = $_GET['parte'] * 200;
	
	// Verificação para Insert dos Partipantes
	if($parte <= $partes) {
				
		// Consulta dos Participantes
		$RE_todos = mysql_query("SELECT * 
								   FROM autonomo 
								  WHERE id_projeto = '$row_folha[projeto]'
									AND tipo_contratacao = '$row_folha[contratacao]' $acrescimo 
									AND (status = '1' 
										 OR  data_saida > '$row_folha[data_inicio]' 
										 AND data_saida <= '$row_folha[data_fim]' 
										 AND status = '0') 
							   ORDER BY nome ASC
							      LIMIT $inicio,200");
		$num_todos = mysql_num_rows($RE_todos);
		
		// Loop dos Participantes
		while($row_aut = mysql_fetch_array($RE_todos)) {
					
			$entrada = $row_aut['data_entrada'];
			$saida   = $row_aut['data_saida'];
			$status  = $row_aut['status'];
					
			// ENTROU ANTES DA DATA INICIAL E SAIU ANTES DE FECHAR A FOLHA
			if($entrada < $folha_ini and $saida <= $folha_fim and $status == '0') {
				
				$REDatas   = mysql_query("SELECT data FROM ano WHERE data >= '$folha_ini' and data <= '$saida'");
				$dias_trab = mysql_num_rows($REDatas);
				$resultTT  = '2';
					
			// ENTROU DEPOIS DA DATA INICIAL E NÃO SAIU
			} elseif($entrada >= $folha_ini and $saida == '0000-00-00' and $entrada < $folha_fim) {
				
				$REDatas   = mysql_query("SELECT data FROM ano WHERE data >= '$entrada' and data <= '$folha_fim'");
				$dias_trab = mysql_num_rows($REDatas);
				$resultTT  = '3';
						
			// ENTROU DEPOIS DA DATA INICIAL E SAIU ANTES DE FECHAR A FOLHA
			} elseif($entrada >= $folha_ini and $saida <= $folha_fim and $status == '0') {
				
				$REDatas   = mysql_query("SELECT data FROM ano WHERE data >= '$entrada' AND data <= '$saida'");
				$dias_trab = mysql_num_rows($REDatas) + 1;
				$resultTT  = '4';
						
			// ENTROU ANTES DA DATA INICIAL E NÃO SAIU
			} else {
				
				$dias_trab = $row_folha['qnt_dias'];
				$resultTT  = '1';
				
			}
	
			// VERIFICA SE JA ESTÁ NA OUTRA FOLHA DO MESMO MES FINALIZADA
			//$qr_verificacao = mysql_query("SELECT * FROM $tabela_now WHERE mes = '$row_folha[mes]' AND id_autonomo = '$row_aut[0]' AND terceiro != '1' AND ano = '$row_folha[ano]' AND status = '3'");
			//$verificacao    = mysql_num_rows($qr_verificacao);
			
			// Inserindo os Participantes
			if(empty($verificacao)) {
				
				mysql_query("INSERT INTO $tabela_now (id_folha, mes, ano, regiao, projeto, data_pro, id_autonomo, nome, cpf, banco, agencia, conta, dias_trab, tipo_pg, sit, result, status) VALUES ('$folha', '$row_folha[mes]', '$row_folha[ano]', '$regiao', '$row_folha[projeto]', '$row_folha[data_proc]', '$row_aut[0]', '$row_aut[nome]', '$row_aut[cpf]', '$row_aut[banco]', '$row_aut[agencia]', '$row_aut[conta]', '$dias_trab', '$row_aut[tipo_pagamento]', '1', '$resultTT', '2');") or die ("Erro<br><br>".mysql_error());
				
			} // Fim da Inserção dos Participantes

		} // Fim do Loop dos Participantes

		$parte += 1;
		header("Location: folha_new2.php?m=1&enc=$enc&parte=$parte");
		exit;

	} // Fim da verificação para Insert dos Partipantes

mysql_query("UPDATE folhas SET status = '2', participantes = '$total' WHERE id_folha = '$folha' LIMIT 1");

print "<script>location.href=\"folha_new2.php?enc=$enc\"</script>";
exit;

} // Fim da verificação para Insert da Folha






// EXECUTANDO AJAX FORA DE TUDO
if(!empty($_REQUEST['ajax'])){
	$nom = $_REQUEST['ajax'];
	$proje = $_REQUEST['id'];
	$proje = explode("-",$proje);
	$re_autonomo = mysql_query("SELECT id_autonomo,nome,campo3,tipo_contratacao FROM autonomo WHERE nome LIKE '%$nom%' AND id_projeto = '$proje[0]' ");
	$cont = '0';
	$retorno .= "<table width=\"700\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">\n";
	
	while($row_aut = mysql_fetch_array($re_autonomo)){
		if($row_aut['tipo_contratacao'] == 1){
			$tabela_now = "folha_autonomo";
		}else{
			$tabela_now = "folha_cooperado";
		}
		
		$re_folha_proc = mysql_query("SELECT id_autonomo FROM $tabela_now WHERE id_autonomo = '$row_aut[0]' AND id_folha = '$proje[1]'");
		$num = mysql_num_rows($re_folha_proc);
		
		if($num == 0){
			if($cont % 2){ $classcor="corfundo_um"; }else{ $classcor="corfundo_dois"; };
			$retorno .= "<tr class=\"novalinha $classcor\">\n<td><input type=\"checkbox\" name='aut[]' id='aut' value='$row_aut[0]'></td>";
			$retorno .= "<td align='left'>$row_aut[campo3]</td><td align='left'>$row_aut[nome]</td>\n</tr>\n";
			$cont ++;
		}
		
	}
	$retorno .= "</table>\n";
	print $retorno;
	
	exit;
}

# EXECUTANDO A INCLUSÃO
if(!empty($_REQUEST['inclusao'])){
	
	$aut = $_REQUEST['aut'];
	$id_folha = $_REQUEST['folha'];
	
	//SELECIONANDO OS DADOS DA FOLHA PELO ID_FOLHA
	$result_folha = mysql_query("SELECT * FROM folhas where id_folha = '$id_folha' LIMIT 1");
	$row_folha = mysql_fetch_array($result_folha);
	
	foreach ($aut as $id_autonomo){
		$re = mysql_query("SELECT nome,cpf,banco,agencia,conta,tipo_contratacao,tipo_pagamento FROM autonomo WHERE id_autonomo = '$id_autonomo'");
		$row = mysql_fetch_array($re);
		
		if($row['tipo_contratacao'] == 1){
			mysql_query("INSERT INTO folha_autonomo(id_folha,mes,regiao,projeto,data_pro,id_autonomo,nome,cpf,banco,agencia,conta,
			dias_trab,tipo_pg,sit,result,status) VALUES ('$id_folha','$row_folha[mes]','$row_folha[regiao]','$row_folha[projeto]',
			'$row_folha[data_proc]','$id_autonomo','$row[nome]','$row[cpf]','$row[banco]','$row[agencia]','$row[conta]','$row_folha[qnt_dias]',
			'$row[tipo_pagamento]','1','4','2')");
		}else{
			mysql_query("INSERT INTO folha_cooperado(id_folha,mes,regiao,projeto,data_pro,id_autonomo,nome,cpf,banco,agencia,conta,
			dias_trab,tipo_pg,sit,result,status) VALUES ('$id_folha','$row_folha[mes]','$row_folha[regiao]','$row_folha[projeto]',
			'$row_folha[data_proc]','$id_autonomo','$row[nome]','$row[cpf]','$row[banco]','$row[agencia]','$row[conta]','$row_folha[qnt_dias]',
			'$row[tipo_pagamento]','1','4','2')");
		}
	}
	
	$encinc = encrypt("$row_folha[regiao]&$id_folha&2"); 
	$encinc = str_replace("+","--",$encinc);
	
	echo "<script>location.href = 'folha2.php?enc=$encinc'; </script>";
	
	exit;
}

//ENCRIPTOGRAFANDO MARCADOS / DESMARCADOS
$encmar = encrypt("$regiao&$folha&2"); 
$encmar = str_replace("+","--",$encmar);

$encdes = encrypt("$regiao&$folha&1"); 
$encdes = str_replace("+","--",$encdes);

$encinc = encrypt("$regiao&$folha&5"); 
$encinc = str_replace("+","--",$encinc);
// -----------------------------

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Cache-Control" content="No-Cache">
<meta http-equiv="Pragma"        content="No-Cache">
<meta http-equiv="Expires"       content="0">
<title>:: Intranet :: Inicio da Folha Sint&eacute;tica</title>
<link href="../net1.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="../js/ramon.js"></script>
</head>

<body onLoad="limpaCache('folha2.php');">

<table width="95%" border="0" align="center">
  <tr>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><br />
      <table width="90%" border="0" align="center">
      <tr>
        <td height="115" colspan="3" align="center" valign="middle" class="show">
      <img src="../imagens/logomaster<?=$row_user['id_master']?>.gif" alt="" width="110" height="79"><br />
      <?=$row_master['razao']?>
        
        </td>
      </tr>
      <tr class="linha">
        <td width="29%" height="29" align="center" valign="middle" bgcolor="#E2E2E2">Data de Processamento:
          <?=$row_folha['data_proc2']?></td>
        <td width="43%" align="center" valign="middle" bgcolor="#E2E2E2">CNPJ :  <?=$row_master['cnpj']?></td>
        <td width="28%" align="center" valign="middle" bgcolor="#E2E2E2"><?="de: ".$row_folha['data_inicio2']." até ".$row_folha['data_fim2']?></td>
      </tr>
    </table>
<?php
if($st != 5) {

// PAGINAÇÃO
$nav           = "%s?pagina=%d%s&enc=$enc";
$max_logs      = 50;
$numero_pagina = 0;
if(!empty($_GET['pagina'])) {
  $numero_pagina = $_GET['pagina'];
}

$start_log = $numero_pagina * $max_logs;

$qr_prelog     = "SELECT * FROM $tabela_now WHERE id_folha = '$folha' AND status = '$st' ORDER BY nome ASC";
$qr_limit_log  = sprintf("%s LIMIT %d, %d", $qr_prelog, $start_log, $max_logs);
$qr_log        = mysql_query($qr_limit_log) or die(mysql_error());
//$log         = mysql_fetch_assoc($qr_log);
$all_logs      = mysql_query($qr_prelog);
$total_logs    = mysql_num_rows($all_logs);
$total_paginas = ceil($total_logs/$max_logs)-1;

$meses       = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$mes_INT      = (int)$row_folha['mes'];
$mes_da_folha = $meses[$mes_INT];

$ano = date("Y");
$mes = date("m");
$dia = date("d");

$data = date("d/m/Y");

if($total_logs == 0) {
	
	//-- ENCRIPTOGRAFANDO A VARIAVEL
	$linkvolt1 = encrypt("$regiao&regiao"); 
	$linkvolt1 = str_replace("+","--",$linkvolt1);
	// -----------------------------
	
	echo '<table width="95%" border="0" align="center"><tr><td align="center" valign="middle" class="show">';
	print "<br><div class='title'>Não foi encontrado nenhum Participante na opção requisitada!
	</div><br><br>";
	print "<b><a href='javascript:history.go(-1);' class='botao'>VOLTAR</a></b><br /><br />";
	print "<b><a href='folha.php?enc=$linkvolt1&id=9' class='botao'>INICIO</a></b>";
	print "</td></tr></table>";
	exit;
	
}
?>
<br>
      <table width="500" border="0">
  <tr>
    <td width="166.66" align="center" bgcolor="#E2E2E2"><a href="folha2.php?enc=<?=$encmar?>">Marcados</a></td>
    <td width="166.66" align="center" bgcolor="#E2E2E2"><a href="folha2.php?enc=<?=$encdes?>">Desmarcados</a></td>
    <td width="166.66" align="center" bgcolor="#E2E2E2"><a href="folha2.php?enc=<?=$encinc?>">Inclusão</a></td>
  </tr>
</table>

      <br>
	<br />
     <span class="title">Folha - <?=$mes_da_folha?> / <?=$row_folha['ano']?></span><br />
      <br />
  
    <form action="" method="post" name="Form" onSubmit="return ValidaForm()">
      <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr class="novo_tr_dois">
          <td width="3%" height="20" align="center" valign="middle" class="style23">&nbsp;
          </td>
          <td align="center" valign="middle">Nome </td>
          <td width="28%" align="center" valign="middle" class="style23">Atividade</td>
          <td width="31%" align="center" valign="middle" class="style23">Unidade</td>
          </tr>
        
        <?php
        #INFORMAÇÕES PARA O AJAX
		#ajupdatecheck(tabela,campo,nomeid,id,tipoaj)
		$tb_aj 		= $tabela_now;
		$nomeid_aj 	= "id_folha_pro";
		$campo_aj 	= "status";
	
		$cont = 0;
		$num_total = mysql_num_rows($qr_log);
		while($row_aut = mysql_fetch_array($qr_log)){
		
		//RECOLHENDO INFORMAÇÕES DO AUTONOMO/COOPERADO
		
		$Part -> MostraParticipante($row_aut['id_autonomo']);
		$locacao = $Part -> locacao;
		$campo3 = $Part -> campo3;
		
		$Part -> CursoParticipante($row_aut['id_autonomo']);
		$nome_curso = $Part -> campo2;

		//---- EMBELEZAMENTO DA PAGINA ----------------------------------
		if($cont % 2){ $classcor="corfundo_um"; }else{ $classcor="corfundo_dois"; };
		$nome = str_split($row_aut['nome'], 35);
		$nomeT = sprintf("% -40s", $nome[0]);
		$bord = "style='border-bottom:#000 solid 1px;'";
		  
		$nomeC = str_replace("CAPACITANDO EM","CAP. EM",$nome_curso);
		//-----------------
		
		$aj = " onClick=\"ajupdatecheck('$tb_aj',this.id,'$nomeid_aj','$row_aut[0]','1')\" ";
		if($row_aut['status'] == '1'){
			$chek = "";
		}else{
			$chek = "checked";
		}
		
		print"
		<tr class=\"novalinha $classcor\" $linhaTab>
		<td><div id='retorno_".$cont."'>&nbsp;</div></td>
		<td align='lefth' valign='middle'><label>&nbsp;
		<input name='status_".$cont."' id='status_".$cont."' type='checkbox' value='$row_aut[0]' $chek $desabilitado $aj>
		&nbsp;&nbsp;$campo3 - $nomeT </label></td>
		<td align='lefth' valign='middle'>$nomeC</td>
		<td align='lefth' valign='middle'>$locacao
		</td>
		</tr>";
		  
		$dias_trab = "";
		$resultTT = "";
		
		$cont ++;
		
		}
		  
		  // -------- ATUALIZANDO A TAB GERAL DAS FOLHAS PARA STATUS 2 = GERADO
		  
		?>
        </table>
		<br>
        <table cellpadding="0" cellspacing="0" border="0">
        <tr><td align="center">&nbsp;
          <?php
		if($numero_pagina == $total_paginas){
			$pg_now = $numero_pagina;
		}else{
			$pg_now = $numero_pagina + 1;
		}
		
		echo $total_logs." Participantes em ".$total_paginas." paginas<br /><br />Página atual: ".$pg_now;
		?>
          &nbsp;</td>
        </tr></table>


      <input type="hidden" name="id_regiao" value="<?=$regiao?>">
      <input type="hidden" name="id_projeto" value="<?=$row_folha['projeto']?>">
      <input type="hidden" name="id_folha" value="<?=$folha?>">
      <input type="hidden" name="data_proc" value="<?=$row_folha['data_proc']?>">
      <input type="hidden" name="mes" value="<?=$row_folha['mes']?>">
      <input type="hidden" name="vale" value="<?=$vale?>">
      <input type="hidden" name="total" value="<?=$num_total?>">
      <img src='../imagens/carregando/loading.gif' border='0' style="display:none">
      <br>
      <?php
// Paginação

if ($numero_pagina > 0) { ?>
<a href="<?php printf($nav, $currentPage, 0, $string); ?>">&laquo; Primeira</a>&nbsp;
<?php }
if ($numero_pagina == 0) { ?>
<span class="morto">&laquo; Primeira</span>&nbsp;
<?php } 
if ($numero_pagina > 0) { ?>
<a href="<?php printf($nav, $currentPage, max(0, $numero_pagina - 1), $string); ?>">&#8249; Anterior</a>&nbsp;
<?php } 
if ($numero_pagina == 0) { ?>
<span class="morto">&#8249; Anterior</span>&nbsp;
<?php }
if ($numero_pagina < $total_paginas) { ?>
<a href="<?php printf($nav, $currentPage, min($total_paginas, $numero_pagina + 1), $string); ?>">Próxima &#8250;</a>&nbsp;
<?php } 
if ($numero_pagina >= $total_paginas) { ?>
<span class="morto">Próxima &#8250;</span>&nbsp;                   
<?php } 
if ($numero_pagina < $total_paginas) { ?>
<a href="<?php printf($nav, $currentPage, $total_paginas, $string); ?>">Última &raquo;</a>
<?php }                    
if ($numero_pagina >= $total_paginas) { ?>
<span class="morto">Última &raquo;</span>
<?php }
// Fim da Paginação
?>
<script language="javascript" type="text/javascript">

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

      <br>
      <br>
      <?php
	  if($row_folha['contratacao'] == '1'){
		  echo '<a href="sintetica.php?enc='.$encmar.'" class="botao">CONTINUAR</a><br>';
	  }else{
		  echo '<a href="sinteticacoo.php?enc='.$encmar.'" class="botao">CONTINUAR</a><br>';
	  }
	  ?>
      <br>
    </form>
    Página:
          <form action="<?=$_SERVER['PHP_SELF']?>" method="get" name="pagina" id="pagina">
              <input type="text" name="pagina" size="2">
              <input type="hidden" name="enc" value="<?=$_GET['enc']?>">
              <input type="submit" value="Ir">
              <input type="hidden" name="go_pagina" value="true">
          </form>
          <br>
      <br>

<?php
}else{


//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkreg = encrypt("$id_regiao&$folha"); 
$linkreg = str_replace("+","--",$linkreg);
// -----------------------------


?>
<br />
<table width="500" border="0">
  <tr>
    <td width="166.66" align="center" bgcolor="#E2E2E2"><a href="folha2.php?enc=<?=$encmar?>">Marcados</a></td>
    <td width="166.66" align="center" bgcolor="#E2E2E2"><a href="folha2.php?enc=<?=$encdes?>">Desmarcados</a></td>
    <td width="166.66" align="center" bgcolor="#E2E2E2"><a href="folha2.php?enc=<?=$encinc?>">Inclus&atilde;o</a></td>
  </tr>
</table>

<br>
<br>
<table width="500" border="0" cellspacing="2" cellpadding="0">
  <tr>
    <td width="12%" height="51" class="secao">NOME:</td>
    <td width="68%">&nbsp;&nbsp;
    <input name="nome" type="text" id="nome" size="45" onBlur="AjaxVarios('folha2.php',this.id,'retorno','<?=$row_folha['projeto']."-".$folha?>');"></td>
    <td width="20%" align="center"><input type="button" value="Procurar" class="botaodois"></td>
  </tr>
</table>
<br /><br />
<form action="folha2.php" method="post" id="form1">
<div id="retorno">&nbsp;</div>
<input type="hidden" name="inclusao" id="inclusao" value="1">
<input type="hidden" name="folha" id="folha" value="<?=$folha?>">
<br />
<input type="submit" value="Continuar" class="botao">

</form>
<?php


}
?>
</td>
  </tr>
</table>
</body>
</html>