<?php
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='../login.php'>Logar</a>";
exit;
}

include "../conn.php";
include "../funcoes.php";

$regiao = $_REQUEST['regiao'];
$tela = $_REQUEST['tela'];

switch($tela) {
	case 2:
// Recebendo a variável criptografada
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link1 = decrypt($enc); 

$teste = explode("&",$link1);
$regiao = $teste[0];
$clt = $teste[1];

$telaF = 3;
$linkF = encrypt("$regiao&$telaF&$clt");
$linkF = str_replace("+","--",$linkF);
//-----------------------------
}
?>
<html>
<head>
<title>:: Intranet :: Movimentos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="../favicon.ico">
<link href="../net1.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="../js/ramon.js"></script>
<script language="javascript" type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<script language="javascript" type="text/javascript" >
$(function(){
});

function valida1(){
	d = document.form1;

	if (!d.mov1[0].checked && !d.mov1[1].checked && !d.mov1[2].checked && !d.mov1[3].checked && !d.mov1[4].checked && !d.mov1[5].checked && !d.mov1[6].checked && !d.mov1[7].checked && !d.mov1[8].checked && !d.mov1[9].checked && !d.mov1[10].checked && !d.mov1[11].checked && !d.mov1[12].checked && !d.mov1[13].checked && !d.mov1[14].checked && !d.mov1[15].checked && !d.mov1[16].checked){
	   alert ("Escolha um TIPO DE MOVIMENTO de CRÉDITO");
	   document.getElementById('tabelacredito').className = "style7 linhastabela2";
	   return false;
	}

	if (d.valor.value == "" ){
		alert("O campo Valor deve ser preenchido!");
		d.valor.focus();
		return false;
	}
	
  return true;   
}

function valida2(){
	d = document.form2;

	if (!d.mov2[0].checked && !d.mov2[1].checked && !d.mov2[2].checked && !d.mov2[3].checked && !d.mov2[4].checked){
	   alert ("Escolha um TIPO DE MOVIMENTO de DESCONTO");
	   document.getElementById('tabeladebito').className = "style7 linhastabela2";
	   return false;
	}

	if (d.valor2.value == "" && !d.mov2[4].checked){
		alert("O campo Valor deve ser preenchido!");
		d.valor2.focus();
		return false;
	}
	
  return true;   
}

function validaFALTA(){
	d = document.frmfaltas;

	if (d.faltas.value == "" ){
		alert("O campo QUANTIDADE DE FALTAS deve ser preenchido!");
		d.faltas.focus();
		return false;
	}
	
  return true;   
}

</script>
<style>
body {
	background-color:#FAFAFA;
	text-align:center;
	margin:0px;
}
p {
	margin:0px;
}
#corpo {
	width:90%;
	background-color:#FFF;
	margin:0px auto;
	text-align:left;
	padding-top:20px;
	padding-bottom:10px;
}
td.escuro_claro {
	height:35px;
	text-align:center;
	background-color:#999;
}
td.escuro {
	height:35px;
	text-align:center;
	background-color:#333;
	color:#FFF;
}
td.claro {
	height:35px;
	text-align:center;
	background-color:#FFF;
}
</style>
</head>
<body>
<div id="corpo">
<div id="topo" style="width:95%; margin:0px auto; font-family:Arial;">

<div style="float:right; margin-right:7px;">

    	<?php include('../reportar_erro.php'); ?>
 
</div>
<div style="clear:right"></div>

	<div style="float:left; width:25%;">
    <?php
	switch($tela) {
	case 1:
		echo "<a href='../principalrh.php?regiao=$regiao'>";
	break;
	case 2:
		if(isset($_GET['ferias'])) {
    		echo "<a href='ferias/index.php?enc=$linkF'>";
    	} else {
    		echo "<a href='rh_movimentos.php?regiao=$regiao&tela=1'>";
    	}
	break;
	} ?>
        	<img src='../imagens/voltar.gif' border='0'>
        </a>
    </div>
    
	<div style="float:left; width:50%; text-align:center; font-size:24px; font-weight:bold; color:#000;">
    	MOVIMENTOS
    </div>
	<div style="float:right; width:25%; text-align:right; font-size:12px; color:#333;">
    	<br><b>Data:</b> <?=date('d/m/Y')?>&nbsp;
    </div>
	<div style="clear:both;"></div>
</div>

<?php
switch($tela) {
	case 1:
    
	$total_clt = NULL;
	$qr_projetos = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' AND status_reg = '1' ORDER BY nome ASC");
	while($projetos = mysql_fetch_array($qr_projetos)) {
    
	$result_clt = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE id_projeto = '$projetos[0]' AND (status < '60' OR status = '200') ORDER BY nome ASC");
	$num_clt = mysql_num_rows($result_clt);
	
	if(!empty($num_clt)) {
	$total_clt++; ?>

    <table cellpadding="8" cellspacing="0" style="width:95%; border:0px; background-color:#f5f5f5; margin:20px auto;">
        <tr>
          <td colspan="6" class="show">
            &nbsp;<span style='color:#F90; font-size:32px;'>&#8250;</span> <?=$projetos['nome']?>
          </td>
        </tr>
        <tr class="novo_tr">
          <td width="5%">COD</td>
          <td width="32%">NOME</td>
          <td width="30%">CARGO</td>
          <td width="33%">UNIDADE</td>
        </tr>
        
  <?php while($row_clt = mysql_fetch_array($result_clt)) {
		
		$result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt[id_curso]'");
		$row_curso = mysql_fetch_array($result_curso);

		// Encriptografando a Variável
		$link2 = encrypt("$regiao&$row_clt[0]"); 
		$link3 = str_replace("+","--",$link2);
		//----------------------------
	
		if(isset($_GET['ferias'])) {
			$link4 = "rh_movimentos.php?ferias=true&tela=2&enc=$link3";
		} else {
			$link4 = "rh_movimentos.php?tela=2&enc=$link3";
		} ?>
	
      <tr style="background-color:<?php if($alternateColor++%2!=0) { echo "#F0F0F0"; } else { echo "#FDFDFD"; } ?>">
		 <td><?=$row_clt['id_clt']?></td>
		 <td><a href="<?=$link4?>"><?=$row_clt['nome']?></a> 
		 <?php if($row_clt['status'] == '40') { 
   					echo '<span style="color:#069; font-weight:bold;">(Em Férias)</span>';
   			   } elseif($row_clt['status'] == '200') {
	   				echo '<span style="color:red; font-weight:bold;">(Aguardando Demissão)</span>';
   			   } ?></td>
		 <td><?=$row_curso['nome']?></td>
		 <td><?=$row_clt['locacao']?></td>
   	  </tr>
   
   <?php } 
} ?>
   
</table>
<?php }

	// Se não tem nenhum CLT na região
	if(empty($total_clt)) { ?>
    
      <META HTTP-EQUIV=Refresh CONTENT="2; URL=/intranet/principalrh.php?regiao=<?=$regiao?>&id=1">
      <p style="color:#C30; font-size:12px; font-weight:bold; margin:30px auto; width:50%; text-align:center;">
               Obs: A região não possui participantes CLTs.
      </p>
      
	<?php } else { ?>

        <div style="width:95%; margin:0px auto; font-size:13px; padding-bottom:4px; margin-top:15px; text-align:right;">
            <a href="#corpo" title="Subir navegação">Subir ao topo</a>
        </div>
    
    <?php }
	
break;
case 2:

$meses = array('-','JANEIRO','FEVEREIRO','MARÇO','ABRIL','MAIO','JUNHO','JULHO','AGOSTO','SETEMBRO','OUTUBRO','NOVEMBRO','DEZEMBRO','13º NOV.','13º DEZ.','13º INT.','RESCISÃO');

$ar_incidencia = array(5020=>'INSS',5021=>'IRRF',5023=>'FGTS',9999=>'ENCARGOS',7004=>'REP.REMUNERADO',5001=>'INF. RENDIMENTOS');

// Selecionando os dados do CLT
$result_clt = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE id_clt = '$clt'");
$row_clt = mysql_fetch_array($result_clt);
//----------------------------

// Selecionando os Dependentes
$result_depe = mysql_query("SELECT *,date_format(data1, '%d/%m/%Y') AS data1, date_format(data2, '%d/%m/%Y') AS data2, date_format(data3, '%d/%m/%Y') AS data3, date_format(data4, '%d/%m/%Y') AS data4, date_format(data5, '%d/%m/%Y') AS data5 FROM dependentes WHERE id_bolsista = '$clt' AND id_projeto = '$row_clt[id_projeto]'");
$row_depe = mysql_fetch_array($result_depe);
$num_row_depe = mysql_num_rows($result_depe);
//------------------------------------------------------------

// Verificando qual Folha entrará todos os Movimentos Lançados
$result_folhas = mysql_query("SELECT MAX(mes) FROM rh_folha WHERE regiao = '$regiao' AND status = '3' AND projeto = '$row_clt[id_projeto]'");
$row_folhas = mysql_fetch_array($result_folhas);
$mes_mov = $row_folhas['0'] + 1;
//---------------------

// Selecionando o Curso
$result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = $row_clt[id_curso]");
$row_curso = mysql_fetch_array($result_curso);

// Data Corrente
$data = date('d/m/Y');

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- FUNCAO PARA CALCULAR IDADE

function CalcularIdade($nascimento) {

$hoje = date("d/m/Y"); //pega a data d ehoje
$aniv = explode("/", $nascimento); //separa a data de nascimento em array, utilizando o símbolo de - como separador
$atual = explode("/", $hoje); //separa a data de hoje em array
  
$idade = $atual[2] - $aniv[2];

//verifica se o mês de nascimento é maior que o mês atual
if($aniv[1] > $atual[1]) { 
    
	//tira um ano, já que ele não fez aniversário ainda
	$idade--; 

//verifica se o dia de hoje é maior que o dia do aniversário
} elseif($aniv[1] == $atual[1] && $aniv[0] > $atual[0]) { 
    
	//tira um ano se não fez aniversário ainda
	$idade--; 

}

//retorna a idade da pessoa em anos
return $idade; 
}

//------------------ FUNCAO PARA CALCULAR IDADE

//-- INICIANDO CALCULOS

$salario_calc = $row_curso['salario'];

// ------------ VERIFICANDO SE TEM FILHOS PARA CALCULAR A DEDUÇÃO DO IMPOSTO DE RENDA
if($num_row_depe != 0) { 

$nomes_ar = array($row_depe['nome1'], $row_depe['nome2'], $row_depe['nome3'], $row_depe['nome4'], $row_depe['nome5']);
$cont_nomes_vazios = array_count_values($nomes_ar);
$cont_nomes_vazios = $cont_nomes_vazios[''];

$datas_ar = array($row_depe['data1'], $row_depe['data2'], $row_depe['data3'], $row_depe['data4'], $row_depe['data5']);

$num_row_depe = array_count_values($datas_ar);

if($num_row_depe["00/00/0000"] == "5"){
	$tabela_depe = "display:none;";
	$mostra_depe = "0";
}else{
	$tabela_depe = NULL;
	$mostra_depe = "1";

for ($i = 0; $i <= 4; $i++) {
	if($datas_ar[$i] != "00/00/0000"){
		$style[$i] = "";
		$idade[$i] = CalcularIdade($datas_ar[$i]);
	}else{
		 $style[$i] = "style='display:none'";
	}
	
	//------------- DEDUÇÃO DO IMPOSTO DE RENDA ----------------//
	$contagem_menor_idade = "0";
	if($idade[$i] < "21" and $datas_ar[$i] != "00/00/0000") {
		$resposta[$i] = '<span style="color:#039;">Menor de 21 Anos</span>';
	} else {
		$resposta[$i] = 'Maior de 21 Anos';
	}			
}

$result_valor_deducao = mysql_query("SELECT * FROM rh_movimentos WHERE cod = '5049'");
$row_valor_deducao = mysql_fetch_array($result_valor_deducao);

$total_menor = array_count_values($resposta); 
$totaldeducao = $total_menor['<font color=#993300>Menor de 21 Anos</font>'] * $row_valor_deducao['fixo'];

}

} else {
	$tabela_depe = "display:none;";
}

// ----------- TERMINA TUDO SOBRE DEPENDENTES
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center">
  
    
    	<table cellpadding="0" cellspacing="0" style="width:95%; border:0px; background-color:#f5f5f5; margin-top:20px;">
  		  <tr>
    	    <td>
              <div align="center" style="font-family:Arial; font-size:18px; color:#FFF; background:#036">
                 <?php echo $clt." - ".$row_clt['nome']; ?>
              </div>
              <div align="center" style="font-family:Arial; font-size:13px; background:#efefef; padding:4px;">
                 <?php echo "<b>Unidade:</b> ".$row_clt['locacao']."<br><b>Atividade:</b> ".$row_curso['nome']."<br><b>Salário Contratual:</b> R$ ".number_format($row_curso['salario'], '2', ',', '.'); ?>
              </div>
              <div align="center" style="font-family:Arial; font-size:13px; background:#efefef; padding:0px; <?=$tabela_depe?>" id="tabeladepe">
                	<b>Dependentes</b>
      <table cellpadding="4" cellspacing="0" style="width:100%; border:0px; margin-top:3px;">
  		  <tr bgcolor="#DDDDDD">
            <td width="26%" height="22">Nome</td>
            <td width="17%">Data de Nascimento</td>
            <td width="18%">Idade</td>
            <td width="39%">Informa&ccedil;&atilde;o de DDIR</td>
  		  </tr>
          <tr <?=$style[0]?>>
            <td><?=$row_depe['nome1']?></td>
            <td><?=$row_depe['data1']?></td>
            <td><?=$idade[0].' anos'?></td>
            <td><?=$resposta[0]?></td>
          </tr>
          <tr <?=$style[1]?> style="background-color:#f0f0f0;">
            <td><?=$row_depe['nome2']?></td>
            <td><?=$row_depe['data2']?></td>
            <td><?=$idade[1].' anos'?></td>
            <td><?=$resposta[1]?></td>
          </tr>
          <tr <?=$style[2]?>>
            <td><?=$row_depe['nome3']?></td>
            <td><?=$row_depe['data3']?></td>
            <td><?=$idade[2].' anos'?></td>
            <td><?=$resposta[2]?></td>
          </tr>
          <tr <?=$style[3]?> style="background-color:#f0f0f0;">
            <td><?=$row_depe['nome4']?></td>
            <td><?=$row_depe['data4']?></td>
            <td><?=$idade[3].' anos'?></td>
            <td><?=$resposta[3]?></td>
          </tr>
          <tr <?=$style[4]?>>
            <td><?=$row_depe['nome5']?></td>
            <td><?=$row_depe['data5']?></td>
            <td><?=$idade[4].' anos'?></td>
            <td><?=$resposta[4]?></td>
          </tr>
	  </table>
              </div>
            </td>
          </tr>
 		</table>


    </td>
  </tr>
  <tr>
    <td align="center">
    
    
    <table cellpadding="0" cellspacing="0" width="95%" style="margin-top:50px;">
  	  <tr bgcolor="#cccccc">
    	<td height="30" colspan="7" align="center" bgcolor="#990000" id="falta">
        	<span class="style7">FALTAS</span>
        </td>
      </tr>
      <tr bgcolor="#cccccc">
      	<td align="center" bgcolor="#F1F1F1">
        <br>
        <form action="rh_movimentos.php" method="post" name="frmfaltas" onSubmit="return validaFALTA()">
        <table width="75%" border="0" cellspacing="0" cellpadding="0" class="bordaescura1px">
          <tr>
      		<td class="escuro_claro">DATA DO MOVIMENTO</td>
          </tr>
          <tr>
            <td class="escuro">
            <select id="mes_mov" name="mes_mov" class="campotexto">
						<?php
                        for($i=1; $i<=12; $i ++){
                            if($i == date('m')){
                                echo '<option value="'.$i.'" selected>'.$meses[$i].'</option>';
                            }else{
                                echo '<option value="'.$i.'">'.$meses[$i].'</option>';
                            }	
                        }
                        ?>
            		</select>
            		de 
            		<select id="ano_mov" name="ano_mov" class="campotexto">
						<?php
                        for($i=(date('Y')-3); $i<=(date('Y')+4); $i ++){
                            if($i == date('Y')){
                                echo '<option value="'.$i.'" selected>'.$i.'</option>';
                            }else{
                                echo '<option value="'.$i.'" >'.$i.'</option>';
                            }
                        }
                        ?>
            		</select>
                </td>
              </tr>
              <tr>
                <td class="escuro_claro">QUANTIDADE DE FALTAS</td>
              </tr>
              <tr>
                <td class="escuro">
                	<input name="faltas" type="text" class="campotexto" id="faltas" size="3" maxlength="2" />
                </td>
              </tr>
              <tr>
                <td class="claro">
                <input type="hidden" name="clt" value="<?=$clt?>" />
                <input type="hidden" name="regiao" value="<?=$regiao?>" />
                <input type="hidden" name="salario" value="<?=$row_curso['salario']?>" />
                <input type="hidden" name="projeto" value="<?=$row_clt['id_projeto']?>" />
                <?php if(isset($_GET['ferias'])) { ?>
                	<input name="ferias" type="hidden" value="true" />
                <?php } ?>
                <input type="hidden" name="tela" value="6" />
                <input type="submit" value="Lan&ccedil;ar Faltas">    
                </td>
              </tr>
            </table>
          </form>
           <br />
           <br /> 
      </td>
    </tr> 
    </table>
    
    </td>
  </tr>
  <tr>
    <td align="center">
    
    <?php $qr_faltas = mysql_query("SELECT * FROM rh_movimentos_clt WHERE tipo_movimento = 'DEBITO' AND id_clt = '$clt' AND id_mov = '62' AND status = '1'");
		  $numero_faltas = mysql_num_rows($qr_faltas);
		  if(!empty($numero_faltas)) { ?>

     <table cellpadding="4" cellspacing="0" style="width:95%; border:0px; background-color:#F1F1F1; margin-bottom:30px; line-height:20px;">
        <tr>
          <td height="30" colspan="7" align="center" bgcolor="#990000" class="style7">
        	GERENCIAMENTO DE FALTAS
          </td>
        </tr>
        <tr style="text-align:center; background-color:#ddd">
          <td width="4%">COD</td>
          <td width="10%">VALOR</td>
          <td width="12%">LAN&Ccedil;AMENTO</td>
          <td width="10%">QUANTIDADE</td>
          <td width="8%">DELETAR</td>
        </tr>
        
    <?php
	while($faltas = mysql_fetch_array($qr_faltas)) {
	
	if($faltas['lancamento'] == '1') { 
		$lancamento = $meses[$faltas['mes_mov']]."/".$faltas['ano_mov'];
	} else { 
		$lancamento = "Sempre"; 
	} ?>
    
    <tr align="center" style="background-color:<?php if($alternateColor++%2!=0) { echo "#ddd"; } else { echo "#f0f0f0"; } ?>" class="linha">
      <td><?=$faltas[0]?></td>
      <td><?php echo 'R$ '.number_format($faltas['valor_movimento'], '2', ',', '.'); ?></td>
      <td><?=$lancamento?></td>
	  <td><?=$faltas['qnt']?></td>
	  <?php if(isset($_GET['ferias'])) { ?>
	  		<td><a href="rh_movimentos.php?ferias=true&tela=5&clt=<?=$clt?>&regiao=<?=$regiao?>&movimento=<?=$faltas[0]?>"><img src="../imagens/deletar_usuario.gif" border="0"></a></td>
	  <?php } else { ?>
	  		<td><a href="rh_movimentos.php?tela=5&clt=<?=$clt?>&regiao=<?=$regiao?>&movimento=<?=$faltas[0]?>"><img src="../imagens/deletar_usuario.gif" border="0"></a></td>
	  <?php } ?>
    </tr>
  <?php } ?>
  
</table>
    
<?php } ?>
    
    </td>
  </tr>
  <tr>
    <td align="center">
    
    
<table cellpadding="0" cellspacing="0" style="width:95%; border:0px; margin-top:50px;">
  <tr>
    <td height="30" colspan="6" align="center" bgcolor="#003399" class="style7" id="credito">
    		MOVIMENTOS VARI&Aacute;VEIS PARA CR&Eacute;DITO
    </td>
  </tr>
    <tr bgcolor="#cccccc">
      <td colspan="6" align="center" valign="center" bgcolor="#F1F1F1">
      <form action="rh_movimentos.php" method="post" name="form1" onSubmit="return valida1()">
      	<br>
        <table width="75%" border="0" cellspacing="0" cellpadding="0" class="bordaescura1px">
          <tr>
            <td class="escuro_claro">DATA DO MOVIMENTO</td>
          </tr>
          <tr>
            <td class="escuro">
              <select id="mes_mov" name="mes_mov" class="campotexto">
                <?php
            for($i=1; $i<=12; $i ++){
				if($i == date('m')){
					echo '<option value="'.$i.'" selected>'.$meses[$i].'</option>';
				}else{
					echo '<option value="'.$i.'">'.$meses[$i].'</option>';
				}	
			}
			echo '<option value="13">13º Primeira parcela</option>';
			echo '<option value="14">13º Segunda parcela</option>';
			echo '<option value="15">13º Integral</option>';
			echo '<option value="16">Rescisão</option>';
            ?>
              </select> de <select id="ano_mov" name="ano_mov" class="campotexto">
  	    <?php for($i=(date('Y')-3); $i<=(date('Y')+4); $i ++){
				if($i == date('Y')){
					echo '<option value="'.$i.'" selected>'.$i.'</option>';
				}else{
					echo '<option value="'.$i.'" >'.$i.'</option>';
				}
			}
            ?>
</select></td>
          </tr>
          <tr>
            <td class="escuro_claro">TIPO DO MOVIMENTO</td>
          </tr>
          <tr>
            <td class="escuro"><table width="97%" border="0" cellspacing="0" cellpadding="0" class="style7 linhastabela1" id="tabelacredito" align="center">
          
              <tr>
                <td height="35"><label>
                  <input type="radio" name="mov1" id="1" align="absmiddle" value="66" onClick="verifica(1,1)">
                  ADICIONAL NOTURNO </label></td>
                <td><label>
                  <input type="radio" name="mov1" id="2" align="absmiddle" value="13" onClick="verifica(1,3)">
                  AJUDA DE CUSTO</label></td>
                <td><label>
                  <input type="radio" name="mov1" id="3" align="absmiddle" value="57" onClick="verifica(1,1)">
                  PERICULOSIDADE 30%</label></td>
              </tr>
              
              <tr>
                <td height="35"><label>
                  <input type="radio" name="mov1" id="4" align="absmiddle" value="56" onClick="verifica(1,1)">
                  INSALUBRIDADE 20% </label></td>
                <td><label>
                  <input type="radio" name="mov1" id="22" align="absmiddle" value="14" onClick="verifica(1,1)">
                  DIFEREN&Ccedil;A SALARIAL</label></td>
                <td><label>
                  <input type="radio" name="mov1" id="222" align="absmiddle" value="94" onClick="verifica(1,3)">
                  SAL&Aacute;RIO FAM&Iacute;LIA MES ANTERIOR</label></td>
              </tr>
              
              <tr>
                <td height="35"><label>
                  <input type="radio" name="mov1" id="97" align="absmiddle" value="150" onClick="verifica(1,1)"> 
                  ADICIONAL NOTURNO PLANTONISTA</label></td>
                <td><label>
                  <input type="radio" name="mov1" id="98" align="absmiddle" value="151" onClick="verifica(1,3)">
                  VALE REFEI&Ccedil;&Atilde;O</label></td>
                <td><label>
                <input type="radio" name="mov1" id="99" align="absmiddle" value="149" onClick="verifica(1,1)">
                  RISCO DE VIDA 30%</label></td>
              <tr>
              <tr>
              	<td><label>
                  <input type="radio" name="mov1" id="100" align="absmiddle" value="152" onClick="verifica(1,1)"> 
                    HORA EXTRA</label></td>
                <td ><label>
                  <input type="radio" name="mov1" id="333" align="absmiddle" value="172" onClick="verifica(1,3)">
                    AJUDA DE CUSTO (PARCELA &Uacute;NICA)</label></td>
                    
                    <td><label>
                  <input type="radio" name="mov1" id="22" align="absmiddle" value="192" onClick="verifica(1,1)">
                  GRATIFICAÇÃO</label></td>
                
              </tr>
              
              <tr>
              	<td><label>
                  <input type="radio" name="mov1" id="100" align="absmiddle" value="193" onClick="verifica(1,1)"> 
                   AUXILIO DISTÂNCIA</label></td>

		  		 <td><label>
                  <input type="radio" name="mov1" id="101" align="absmiddle" value="196" onClick="verifica(1,1)">
                  GRATIFICAÇÃO UPA</label></td>
             
                
		  		 <td align="left"><label>
                  <input type="radio" name="mov1" id="102" align="absmiddle" value="197" onClick="verifica(1,1)">
                  GRATIFICAÇÃO FDS</label></td>
                </tr>
                
                <tr>
                	<td align="left"><label>
                  <input type="radio" name="mov1" id="103" align="absmiddle" value="198" onClick="verifica(1,0)"> 
                   REEMBOLSO AUXILIO DISTÂNCIA</label></td>
                   
                   <td colspan="2" align="left"><label>
                  <input type="radio" name="mov1" id="103" align="absmiddle" value="199" onClick="verifica(1,1)"> 
                   DSR</label></td>
                
              </tr>
            
              
            </table>
           </td>
          </tr>
          <tr>
            <td class="escuro_claro">VALOR E LAN&Ccedil;AMENTO DO MOVIMENTO</td>
          </tr>
          <tr>
            <td class="escuro">
            <input name="valor" type="text" id="valor" size="20" OnKeyDown="FormataValor(this,event,20,2)"/>
            <span id="mostrartexto3" class="style7"></span>&nbsp;&nbsp;
            <select name="lancamento1" id="lancamento1" onChange="verifica(1,3)">
              <option value="1">Pr&oacute;xima Folha</option>
              <option value="2">Sempre</option>
            </select>
           </td>
          </tr>
          <tr>
            <td class="escuro_claro">INCID&Ecirc;NCIA</td>
          </tr>
          <tr>
            <td class="escuro">
            <table width="300" border="0" cellspacing="0" cellpadding="0" class="style7" style="border:solid 1px #FFF;" align="center">
              <tr>
                <td height="35" align="center">
                  <input type="checkbox" name="inc1" id="inc1" align="absmiddle" value="5020" style="display:none">
                  <input type="checkbox" name="inc2" id="inc2" align="absmiddle" value="5021" style="display:none">
                  <input type="checkbox" name="inc3" id="inc3" align="absmiddle" value="5023" style="display:none">
                  <span id="mostrartexto1" class="style7"></span>
                </td>
              </tr>
          </table>
             
              </td>
          </tr>
          <tr>
            <td class="claro">
              <input type="hidden" name="clt" value="<?=$clt?>" />
              <input type="hidden" name="regiao" value="<?=$regiao?>" />
              <input type="hidden" name="projeto" value="<?=$row_clt['id_projeto']?>" />
              <?php if(isset($_GET['ferias'])) { ?>
              <input name="ferias" type="hidden" value="true" />
              <?php } ?>
              <input type="hidden" name="tela" value="3" />
              <input type="submit" value="Lan&ccedil;ar Movimento"/></td>
          </tr>
        </table>
        <br>
        <span id='testescript' class="style7"></span>
		<script language="javascript">
        
        function verifica(a,b){
			
			if(a == 1){			
			
			var lancamento = document.getElementById("lancamento1").value;
			var inc = b;
			
		
			
			if(document.all.mov1[0].checked){
				var mov1 = "1";
				document.all.valor.style.display = '';				 
			
				document.all.valor.value = "0";
				
			}else if(document.all.mov1[1].checked){
				var mov1 = "2";
				document.all.valor.style.display = '';
				document.getElementById("mostrartexto3").innerText = "";
				document.all.valor.value = "";
			}else if(document.all.mov1[2].checked){
				var mov1 = "3";
				document.all.valor.style.display = '';
				document.getElementById("mostrartexto3").innerText = "";
				document.all.valor.value = "";
			}else if(document.all.mov1[3].checked){
				var mov1 = "4";
				document.all.valor.style.display = '';
				document.getElementById("mostrartexto3").innerText = "";
				document.all.valor.value = "";
			}else if(document.all.mov1[4].checked){
				var mov1 = "22";
				document.all.valor.style.display = '';
				document.getElementById("mostrartexto3").innerText = "";
				document.all.valor.value = "";
			}else if(document.all.mov1[5].checked){
				var mov1 = "23";
				document.all.valor.style.display = '';
				document.getElementById("mostrartexto3").innerText = "";
				document.all.valor.value = "";
			}else if(document.all.mov1[6].checked){
				var mov1 = "97";
				document.all.valor.style.display = '';
				document.getElementById("mostrartexto3").innerText = "";
				document.all.valor.value = "";
			}else if(document.all.mov1[7].checked){
				var mov1 = "98";
				document.all.valor.style.display = '';
				document.getElementById("mostrartexto3").innerText = "";
				document.all.valor.value = "";
			}else if(document.all.mov1[8].checked){
				var mov1 = "99";
				document.all.valor.style.display = 'none';
				document.getElementById("mostrartexto3").innerText = "30% do salario";
				document.all.valor.value = "0";
			}else if(document.all.mov1[9].checked){
				var mov1 = "100";
				document.all.valor.style.display = '';
				document.getElementById("mostrartexto3").innerText = "";
				document.all.valor.value = "";
		    }else if(document.all.mov1[10].checked){
				var mov1 = "333";
				document.all.valor.style.display = '';
				document.getElementById("mostrartexto3").innerText = "";
				document.all.valor.value = "";
			}else if(document.all.mov1[15].checked){
				var mov1 = "103";
				document.all.valor.style.display = '';
				document.getElementById("mostrartexto3").innerText = "";
				document.all.valor.value = "";
				
			}else if(document.all.mov1[16].checked){
				var mov1 = "104";
				document.all.valor.style.display = '';
				document.getElementById("mostrartexto3").innerText = "";
				document.all.valor.value = "";
				
			}else{
				var mov1 = "";
			}
			
			if(mov1 == 1 || mov1 == 3 || mov1 == 4 || mov1 == 22 || inc == 1 || mov1 == 99 || mov1 == 97 || mov1 == 100 || mov1 == 101 || mov1 == 102){
				document.getElementById("inc1").checked = true;
				document.getElementById("inc2").checked = true;
				document.getElementById("inc3").checked = true;
				document.getElementById("mostrartexto1").innerText = "INSS - IRRF - FGTS";
				
			}else{
				document.getElementById("inc1").checked = false;
				document.getElementById("inc2").checked = false;
				document.getElementById("inc3").checked = false;
				document.getElementById("mostrartexto1").innerText = "NENHUMA INCIDENCIA";
			}
			
			
			}else{
				
				
			var lancamento2 = document.getElementById("lancamento2").value;
			var inc2 = b;

			if(document.all.mov2[0].checked){
				var mov2 = "1";
			}else if(document.all.mov2[1].checked){
				var mov2 = "2";
			}else if(document.all.mov2[2].checked){
				var mov2 = "3";
			}else if(document.all.mov2[3].checked){
				var mov2 = "4";
			}else if(document.all.mov2[4].checked){
				var mov2 = "5";
			}else {
				var mov2 = "";
			}
			
			if(mov2 == 3 || mov2 == 4){
				document.getElementById("inc4").checked = true;
				document.getElementById("inc5").checked = true;
				document.getElementById("inc6").checked = true;
				document.getElementById("mostrartexto2").innerText = "INSS - IRRF - FGTS";
				document.all.valor2.style.display = '';
			
				
			}else if(mov2 == 1 || mov2 == 2){
				document.getElementById("inc4").checked = false;
				document.getElementById("inc5").checked = false;
				document.getElementById("inc6").checked = false;
				document.getElementById("mostrartexto2").innerText = "NENHUMA INCIDENCIA";
				document.all.valor2.style.display = '';
			}else if(mov2 == 5)  {
			
			document.all.valor2.style.display = 'none';
				
			}
				

			}
			
		}
        
        </script>
        <br />
        </form>
      </td>
    </tr>
</table>
    
    
    </td>
  </tr>
  <tr>
    <td align="center">
    
    <?php $qr_creditos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE tipo_movimento = 'CREDITO' AND id_clt = '$clt' AND status = '1'");
	      $numero_creditos = mysql_num_rows($qr_creditos);
		  if(!empty($numero_creditos)) { ?>
          
 		<table cellpadding="4" cellspacing="0" style="width:95%; border:0px; background-color:#F1F1F1; line-height:20px;">
  		  <tr>
            <td height="30" colspan="6" align="center" bgcolor="#003399" class="style7">
    			GERENCIAMENTO DE MOVIMENTOS VARIÁVEIS PARA CR&Eacute;DITO
            </td>
          </tr>
  		  <tr style="text-align:center; background-color:#ddd;">
              <td width="4%">COD</td>
              <td width="26%">MOVIMENTO</td>
              <td width="10%">VALOR</td>
              <td width="12%">LAN&Ccedil;AMENTO</td>
              <td width="30%">INCID&Ecirc;NCIA</td>
              <td width="8%">DELETAR</td>
          </tr>
    
	<?php
	while($creditos = mysql_fetch_array($qr_creditos)) {
	
	if($creditos['lancamento'] == '1') {
		$lancamento = $meses[$creditos['mes_mov']]."/".$creditos['ano_mov'];
	} else { 
		$lancamento = 'Sempre';
	} ?>
    
    <tr align="center" style="background-color:<?php if($alternateColor2++%2!=0) { echo "#ddd"; } else { echo "#f0f0f0"; } ?>" class="linha">
      <td><?=$creditos[0]?></td>
      <td><?=$creditos['nome_movimento']?></td>
      <td><?php echo 'R$ '.number_format($creditos['valor_movimento'], '2', ',', '.'); ?></td>
      <td><?=$lancamento?></td>
      <td> 
	 <?php for($i=0; $i<=2; $i++) {
		  
			  $numero_in = $creditos['incidencia'];
			  $numero_in = explode(",",$numero_in);
			  
			  echo $ar_incidencia[$numero_in[$i]];
			  
			  if(!empty($numero_in[$i]) and $i != 2) {
					echo " - ";
			  }
			  
	  	   } ?>
           </td>
		   
	  <?php if(isset($_GET['ferias'])) { ?>
		   <td align="center"><a href="rh_movimentos.php?ferias=true&tela=5&clt=<?=$clt?>&regiao=<?=$regiao?>&movimento=<?=$creditos[0]?>"><img src="../imagens/deletar_usuario.gif" border="0"></a></td>
	  <?php } else { ?>
		   <td align="center"><a href="rh_movimentos.php?tela=5&clt=<?=$clt?>&regiao=<?=$regiao?>&movimento=<?=$creditos[0]?>"><img src="../imagens/deletar_usuario.gif" border="0"></a></td>
	  <?php } ?>
      
     </tr>
   <?php } ?>
</table>
<?php } ?> 
    
    </td>
  </tr>
  <tr>
    <td align="center">
    
    
    <table cellpadding="0" cellspacing="0" width="95%" style="margin-top:80px;">
  	  <tr bgcolor="#cccccc">
    	<td height="30" colspan="7" align="center" bgcolor="#990000" id="debito">
        	<span class="style7">MOVIMENTOS VARI&Aacute;VEIS PARA DESCONTO</span>
        </td>
      </tr>
    <tr bgcolor="#cccccc">
      <td colspan="7" align="center" valign="middle" bgcolor="#F1F1F1">
      <form action="rh_movimentos.php" method="post" name="form2" onSubmit="return valida2()">
      <div align="center">
      <br>
      <table width="75%" border="0" cellspacing="0" cellpadding="0" class="bordaescura1px">
      	  <tr>
            <td class="escuro_claro">DATA DO MOVIMENTO</td>
          </tr>
          <tr>
          	<td class="escuro">
          <select id="mes_mov" name="mes_mov" class="campotexto">
              <?php
            for($i=1; $i<=12; $i ++){
				if($i == date('m')){
					echo '<option value="'.$i.'" selected>'.$meses[$i].'</option>';
				}else{
					echo '<option value="'.$i.'">'.$meses[$i].'</option>';
				}	
			}
			echo '<option value="13">13º Primeira parcela</option>';
			echo '<option value="14">13º Segunda parcela</option>';
			echo '<option value="15">13º Integral</option>';
			echo '<option value="16">Rescisão</option>';
            ?>
            </select> 
            de
			<select id="ano_mov" name="ano_mov" class="campotexto">
  			<?php
            for($i=(date('Y')-3); $i<=(date('Y')+4); $i ++){
				if($i == date('Y')){
					echo '<option value="'.$i.'" selected>'.$i.'</option>';
				}else{
					echo '<option value="'.$i.'" >'.$i.'</option>';
				}
			}
            ?>
			</select>
         </td>
        </tr>
          <tr>
            <td class="escuro_claro">TIPO DO MOVIMENTO</td>
          </tr>
        <tr>
          <td class="escuro">
          <table width="97%" border="0" cellspacing="0" cellpadding="0" class="style7 linhastabela1" id="tabeladebito" align="center">
            <tr>
              <td width="25%" height="35" align="center"><label>
                <input type="radio" name="mov2" id="5" align="absmiddle" value="60" onClick="verifica(2,3)">
                ADIANTAMENTO </label></td>
              <td width="17%" height="35" align="center"><label>
                <input type="radio" name="mov2" id="6" align="absmiddle" value="76" onClick="verifica(2,5)">
                DESCONTO</label></td>
              <td width="31%" height="35" align="center"><label>
                <input type="radio" name="mov2" id="7" align="absmiddle" value="54" onClick="verifica(2,1)">
                PENS&Atilde;O ALIMENTICIA 15%</label></td>
              <td width="27%" align="center"><label>
                <input type="radio" name="mov2" id="8" align="absmiddle" value="63" onClick="verifica(2,1)">
                PENS&Atilde;O ALIMENTICIA 30%</label></td>
            </tr>
            <tr>
            	  <td width="27%" align="center">
                  <label>
                <input type="radio" name="mov2" id="8" align="absmiddle" value="195" onClick="verifica(2,1)">
               DESCONTO AUXILIO DISTÂNCIA</label>
                </td>
                
            </tr>
            </table></td>
          </tr>
        <tr>
          <td class="escuro_claro">VALOR E LAN&Ccedil;AMENTO DO MOVIMENTO</td>
        </tr>
        <tr>
          <td class="escuro">
          <input name="valor2" type="text" id="valor2" size="20" OnKeyDown="FormataValor(this,event,20,2)" />
            &nbsp;&nbsp;
            <select name="lancamento2" id="lancamento2" onChange="verifica(2,5)">
              <option value="1">Pr&oacute;xima Folha</option>
              <option value="2">Sempre</option>
            </select></td>
          </tr>
        <tr>
          <td class="escuro_claro">INCID&Ecirc;NCIA</td>
        </tr>
        <tr>
          <td class="escuro">
          <table width="300" border="0" cellspacing="0" cellpadding="0" class="style7" style="border:solid 1px #FFF;" align="center">
            <tr>
              <td height="35" align="center">
                <input type="checkbox" name="inc4" id="inc4" align="absmiddle" value="5020" style="display:none">
                <input type="checkbox" name="inc5" id="inc5" align="absmiddle" value="5021" style="display:none">
                <input type="checkbox" name="inc6" id="inc6" align="absmiddle" value="5023" style="display:none">
                <span id="mostrartexto2" class="style7"></span>
                </td>
              </tr>
            </table>
            </td>
          </tr>
          <tr>
            <td class="claro">
            <input name="tela" type="hidden" id="tela" value="4" />
            <input name="clt" type="hidden" id="clt" value="<?=$clt?>" />
            <input name="regiao" type="hidden" id="regiao" value="<?=$regiao?>" />
            <input name="projeto" type="hidden" value="<?=$row_clt['id_projeto']?>" />
            <?php if(isset($_GET['ferias'])) { ?>
            <input name="ferias" type="hidden" value="true" />
            <?php } ?>
            <input type="submit" value="Lan&ccedil;ar Movimento" /></td>
          </tr>
      </table>
      <br>
      </div></form>
	  </td>
    </tr>
  </table>
    
    
    </td>
  </tr>
  <tr>
    <td align="center">
    
    <?php $qr_debitos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE  (tipo_movimento = 'DEBITO' AND id_clt = '$clt' AND id_mov != '62' AND status = '1') OR (id_mov = '195' AND status = '1' AND id_clt = '$clt' ) ");
	      $numero_debitos = mysql_num_rows($qr_debitos);
		  if(!empty($numero_debitos)) { ?>
    
     <table cellpadding="4" cellspacing="0" style="width:95%; border:0px; background-color:#F1F1F1; margin-bottom:30px; line-height:20px;">
        <tr>
          <td height="30" colspan="7" align="center" bgcolor="#990000" class="style7">
        		GERENCIAMENTO DE MOVIMENTOS VARI&Aacute;VEIS PARA DESCONTO
          </td>
        </tr>
        <tr style="text-align:center; background-color:#ddd;">
          <td width="4%">COD</td>
          <td width="26%">MOVIMENTO</td>
          <td width="10%">VALOR</td>
          <td width="12%">LAN&Ccedil;AMENTO</td>
          <td width="30%">INCID&Ecirc;NCIA</td>
          <td width="8%">DELETAR</td>
        </tr>
    <?php
	while($debitos = mysql_fetch_array($qr_debitos)) {
		
	if($debitos['lancamento'] == '1') {
		$lancamento = $meses[$debitos['mes_mov']]."/".$debitos['ano_mov']; 
	} else { 
		$lancamento = 'Sempre'; 
	} ?>
    <tr style="background-color:<?php if($alternateColor3++%2!=0) { echo "#ddd"; } else { echo "#f0f0f0"; } ?>" class='linha' align='center'>
      <td><?=$debitos[0]?></td>
      <td><?=$debitos['nome_movimento']?></td>
      <td><?php echo 'R$ '.number_format($debitos['valor_movimento'], '2', ',', '.'); ?></td>
      <td><?=$lancamento?></td>
      <td>
	<?php for($i=0; $i<=2; $i++) {
		  
			  $numero_in = $debitos['incidencia'];
			  $numero_in = explode(",",$numero_in);
			  
			  echo $ar_incidencia[$numero_in[$i]];
			  
			  if(!empty($numero_in[$i]) and $i != 2) {
					echo " - ";
			  }
		  
	  	  } ?>
	  </td>
	  <?php if(isset($_GET['ferias'])) { ?>
	  		<td align='center'><a href='rh_movimentos.php?ferias=true&tela=5&clt=<?=$clt?>&regiao=<?=$regiao?>&movimento=<?=$debitos[0]?>'><img src='../imagens/deletar_usuario.gif' border='0'></a></td>
	  <?php } else { ?>
	  		<td align='center'><a href='rh_movimentos.php?tela=5&clt=<?=$clt?>&regiao=<?=$regiao?>&movimento=<?=$debitos[0]?>'><img src='../imagens/deletar_usuario.gif' border='0'></a></td>
	  <?php } ?>
    </tr>
   <?php } ?>
</table>
<?php } ?>
    
    
    </td>
  </tr>
</table>


<?php
break;
case 3:  //GRAVANDO RENDIMENTOS

$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$mes_mov = $_REQUEST['mes_mov'];
$ano_mov = $_REQUEST['ano_mov'];
$clt = $_REQUEST['clt'];
$data = date('Y-m-d');
$user = $_COOKIE['logado'];

$cod_movimento = $_REQUEST['mov1'];
$valor = $_REQUEST['valor'];
$valor = str_replace(".","",$valor);
$valor = str_replace(",",".",$valor);
$lancamento = $_REQUEST['lancamento1'];
$inc1 = $_REQUEST['inc1'];
$inc2 = $_REQUEST['inc2'];
$inc3 = $_REQUEST['inc3'];
$incidencia = "$inc1,$inc2,$inc3";




$RSClt = mysql_query("SELECT id_clt,id_curso FROM rh_clt WHERE id_clt = '$clt'");
$RowCLT = mysql_fetch_array($RSClt);

$RSCurso = mysql_query("SELECT salario FROM curso WHERE id_curso = '$RowCLT[id_curso]'");
$RowCurso = mysql_fetch_array($RSCurso);

$result_movimento = mysql_query("SELECT * FROM rh_movimentos WHERE id_mov = '$cod_movimento'");
$row_movimento = mysql_fetch_array($result_movimento);

// ADICIONAIS NOTURNOS
if( $cod_movimento == 149) {
	$valor = $RowCurso['0'] * $row_movimento['percentual'];
}

// VERIFICANDO SE O VALOR DA AJUDA DE CUSTO PASSA DE 50% DO SALARIO DO CARA, PARA COLOCAR INCIDENCIA EM INSS,IRRF,FGTS
if($cod_movimento == 13) {
	$metade = $RowCurso['salario'] / 2;
	if($valor > $metade){
		$incidencia = "5020,5021,5023";
	}
}

mysql_query("INSERT INTO rh_movimentos_clt(id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,
data_movimento,user_cad,valor_movimento,percent_movimento,lancamento,incidencia) VALUES 
('$clt','$regiao','$projeto','$mes_mov','$ano_mov','$cod_movimento','$row_movimento[cod]','$row_movimento[categoria]','$row_movimento[descicao]','$data','$user','$valor','$percentual','$lancamento','$incidencia')");


//-- ENCRIPTOGRAFANDO A VARIAVEL
$link = encrypt("$regiao&$clt"); 
$link = str_replace("+","--",$link);
// -----------------------------

if(empty($_POST['ferias']) and empty($_GET['ferias'])) {
	print "<script>location.href=\"rh_movimentos.php?tela=2&enc=$link#credito\"</script>";
} else {
	print "<script>location.href=\"rh_movimentos.php?ferias=true&tela=2&enc=$link#credito\"</script>";
}


break;
case 4:

$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$mes_mov = $_REQUEST['mes_mov'];
$ano_mov = $_REQUEST['ano_mov'];


$clt = $_REQUEST['clt'];
$cod_movimento = $_REQUEST['mov2'];
$valor = $_REQUEST['valor2'];
$lancamento = $_REQUEST['lancamento2'];

$inc1 = $_REQUEST['inc4'];
$inc2 = $_REQUEST['inc5'];
$inc3 = $_REQUEST['inc6'];

$incidencia = "$inc1,$inc2,$inc3";

$data = date('Y-m-d');
$user = $_COOKIE['logado'];

$valor = str_replace(".","",$valor);
$valor = str_replace(",",".",$valor);

$result_movimento = mysql_query("SELECT * FROM rh_movimentos WHERE id_mov = '$cod_movimento'");
$row_movimento = mysql_fetch_array($result_movimento);

mysql_query("INSERT INTO rh_movimentos_clt(id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,
data_movimento,user_cad,valor_movimento,percent_movimento,lancamento,incidencia) VALUES 
('$clt','$regiao','$projeto','$mes_mov','$ano_mov','$cod_movimento','$row_movimento[cod]','$row_movimento[categoria]','$row_movimento[descicao]','$data','$user','$valor','$percentual',
'$lancamento','$incidencia')");


//-- ENCRIPTOGRAFANDO A VARIAVEL
$link = encrypt("$regiao&$clt"); 
$link = str_replace("+","--",$link);
// -----------------------------

if(empty($_POST['ferias']) and empty($_GET['ferias'])) {
	print "<script>location.href=\"rh_movimentos.php?tela=2&enc=$link#debito\"</script>";
} else {
	print "<script>location.href=\"rh_movimentos.php?ferias=true&tela=2&enc=$link#debito\"</script>";
}

break;
case 5:

$regiao = $_REQUEST['regiao'];
$clt = $_REQUEST['clt'];
$movimento = $_REQUEST['movimento'];

$qr_tipo_movimento = mysql_query("SELECT tipo_movimento, id_mov FROM rh_movimentos_clt WHERE id_movimento = '$movimento'");
$row_tipo_movimento = mysql_fetch_assoc($qr_tipo_movimento);
$tipo_movimento = $row_tipo_movimento['tipo_movimento'];
$id_mov = $row_tipo_movimento['id_mov']; 

if($tipo_movimento == 'CREDITO') {
	$ancora = '#credito';
} elseif($tipo_movimento == 'DEBITO') {
	if($id_mov == '62') {
		$ancora = '#falta';
	} else {
		$ancora = '#debito';
	}
}

mysql_query("UPDATE rh_movimentos_clt SET status = '0' WHERE id_movimento = '$movimento'");

//-- ENCRIPTOGRAFANDO A VARIAVEL
$link = encrypt("$regiao&$clt"); 
$link = str_replace("+","--",$link);
// -----------------------------

if(empty($_POST['ferias']) and empty($_GET['ferias'])) {
	print "<script>location.href=\"rh_movimentos.php?tela=2&enc=$link$ancora\"</script>";
} else {
	print "<script>location.href=\"rh_movimentos.php?ferias=true&tela=2&enc=$link$ancora\"</script>";
}

break;
case 6:

$clt = $_REQUEST['clt'];
$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$mes_mov = $_REQUEST['mes_mov'];
$ano_mov = $_REQUEST['ano_mov'];

$faltas = $_REQUEST['faltas'];
$salario = $_REQUEST['salario'];

$valorDia = $salario / 30;
$valorFaltas = $faltas * $valorDia;

$valorFaltasF = number_format($valorFaltas,2,",",".");
$valorFaltasF2 = number_format($valorFaltas,2,".","");

$result_movimento = mysql_query("SELECT * FROM rh_movimentos WHERE cod = '8000'");
$row_movimento = mysql_fetch_array($result_movimento);

$data = date('Y-m-d');
$user = $_COOKIE['logado'];

mysql_query("INSERT INTO rh_movimentos_clt(id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,data_movimento,user_cad,valor_movimento,lancamento,qnt) VALUES 
('$clt','$regiao','$projeto','$mes_mov','$ano_mov','$row_movimento[id_mov]','$row_movimento[cod]','$row_movimento[categoria]','$row_movimento[descicao]',
'$data','$user','$valorFaltasF2','1','$faltas')");

//-- ENCRIPTOGRAFANDO A VARIAVEL
$link = encrypt("$regiao&$clt"); 
$link = str_replace("+","--",$link);
// -----------------------------

if(empty($_POST['ferias']) and empty($_GET['ferias'])) {
	print "<script>location.href=\"rh_movimentos.php?tela=2&enc=$link#falta\"</script>";
} else {
	print "<script>location.href=\"rh_movimentos.php?ferias=true&tela=2&enc=$link#falta\"</script>";	
}

break;
}
?>
</div>
</body>
</html>