<?php
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../login.php">Logar</a>';
	exit;
} else {

include('../conn.php');

$id_user 	   = $_COOKIE['logado'];
$result_user   = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user      = mysql_fetch_array($result_user);





$projeto  = $_REQUEST['pro'];
$regiao   = $_REQUEST['reg'];
$tipo     = $_REQUEST['tipo'];
$tela     = $_REQUEST['tela'];
$id       = $_REQUEST['id'];
$ano_base = $_REQUEST['ano_base'];


$row_regiao_id = mysql_result(mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$regiao'"),0);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao_id'");
$row_master = mysql_fetch_assoc($qr_master);



$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_projeto    = mysql_fetch_array($result_projeto);

$data_hoje = date('d/m/Y');

function formata($valor) {
	$valor_formatado = str_replace('', ',', $valor);
	return $valor_formatado;
}

function formata2($valor) {
	$valor_formatado = number_format($valor, 2, ',', '');
	return $valor_formatado;
}

switch($tela) {
case 2:
	// Consultas para Seleção de Participante
    if(empty($id)) {
	     switch($tipo) {
			 case 1:
					$result    = mysql_query("SELECT * FROM autonomo WHERE tipo_contratacao = '1' AND id_regiao = '$regiao' AND id_projeto = '$projeto' ORDER BY nome ASC");
					$verifica  = mysql_num_rows($result);
					$coluna_id = 'id_autonomo';
             break;
			 case 2:
					$result    = mysql_query("SELECT * FROM rh_clt WHERE tipo_contratacao = '2' AND id_regiao = '$regiao' AND id_projeto = '$projeto' ORDER BY nome ASC");
					$verifica  = mysql_num_rows($result);
					$coluna_id = 'id_clt';
             break;
			 case 3:
					$result    = mysql_query("SELECT * FROM autonomo WHERE tipo_contratacao = '3' AND id_regiao = '$regiao' AND id_projeto = '$projeto' ORDER BY nome ASC");
					$verifica  = mysql_num_rows($result);
					$coluna_id = 'id_autonomo';
             break;
			 case 4:
					$result    = mysql_query("SELECT * FROM autonomo WHERE tipo_contratacao = '4' AND id_regiao = '$regiao' AND id_projeto = '$projeto' ORDER BY nome ASC");
					$verifica  = mysql_num_rows($result);
					$coluna_id = 'id_autonomo';
             break;
		 }
	} else {
	  	switch($tipo) {
			 case 1:
					$result       = mysql_query("SELECT * FROM autonomo WHERE tipo_contratacao = '1' AND id_regiao = '$regiao' AND id_projeto = '$projeto' AND id_autonomo = '$id' ORDER BY nome ASC");
					$participante = mysql_fetch_assoc($result);
					$verifica     = mysql_num_rows($result);
					$coluna_id    = 'id_autonomo';
             break;
			 case 2:
					$result       = mysql_query("SELECT * FROM rh_clt WHERE tipo_contratacao = '2' AND id_regiao = '$regiao' AND id_projeto = '$projeto' AND id_clt = '$id' ORDER BY nome ASC");
					$participante = mysql_fetch_assoc($result);
					$verifica     = mysql_num_rows($result);
					$coluna_id    = 'id_clt';
             break;
			 case 3:
					$result       = mysql_query("SELECT * FROM autonomo WHERE tipo_contratacao = '3' AND id_regiao = '$regiao' AND id_projeto = '$projeto' AND id_autonomo = '$id' ORDER BY nome ASC");
					$participante = mysql_fetch_assoc($result);
					$verifica     = mysql_num_rows($result);
					$coluna_id    = 'id_autonomo';
             break;
			 case 4:
					$result       = mysql_query("SELECT * FROM autonomo WHERE tipo_contratacao = '4' AND id_regiao = '$regiao' AND id_projeto = '$projeto' AND id_autonomo = '$id' ORDER BY nome ASC");
					$participante = mysql_fetch_assoc($result);
					$verifica     = mysql_num_rows($result);
					$coluna_id    = 'id_autonomo';
             break;
		 }
	}
	
break;
case 3:
          // Consultas para Dados Pessoais
		  switch($tipo) {
			 case 1:
					$result = mysql_query("SELECT * , date_format(data_nasci, '%d/%m/%Y') AS data_nasci, date_format(data_entrada, '%d/%m/%Y') AS data_entrada, date_format(data_rg, '%d/%m/%Y') AS data_rg, date_format(data_saida, '%d/%m/%Y') AS data_saida FROM autonomo WHERE id_autonomo = '$id' AND tipo_contratacao = '1' AND id_regiao = '$regiao' AND id_projeto = '$projeto'");
             break;
			 case 2:
					$result = mysql_query("SELECT * , date_format(data_nasci, '%d/%m/%Y') AS data_nasci, date_format(data_entrada, '%d/%m/%Y') AS data_entrada, date_format(data_rg, '%d/%m/%Y') AS data_rg, date_format(data_saida, '%d/%m/%Y') AS data_saida FROM rh_clt WHERE id_clt = '$id' AND tipo_contratacao = '2' AND id_regiao = '$regiao' AND id_projeto = '$projeto'");
             break;
			 case 3:
					$result = mysql_query("SELECT * , date_format(data_nasci, '%d/%m/%Y') AS data_nasci, date_format(data_entrada, '%d/%m/%Y') AS data_entrada, date_format(data_rg, '%d/%m/%Y') AS data_rg, date_format(data_saida, '%d/%m/%Y') AS data_saida FROM autonomo WHERE id_autonomo = '$id' AND tipo_contratacao = '3' AND id_regiao = '$regiao' AND id_projeto = '$projeto'");
             break;
			 case 4:
					$result = mysql_query("SELECT * , date_format(data_nasci, '%d/%m/%Y') AS data_nasci, date_format(data_entrada, '%d/%m/%Y') AS data_entrada, date_format(data_rg, '%d/%m/%Y') AS data_rg, date_format(data_saida, '%d/%m/%Y') AS data_saida FROM autonomo WHERE id_autonomo = '$id' AND tipo_contratacao = '4' AND id_regiao = '$regiao' AND id_projeto = '$projeto'");
             break;
		 }
		 
		 $participante = mysql_fetch_assoc($result);
		 
$get_pg = mysql_query("SELECT * FROM tipopg WHERE id_tipopg = '$participante[tipo_pagamento]'");
$pg     = mysql_fetch_assoc($get_pg);

$get_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$participante[id_curso]'");
$curso     = mysql_fetch_assoc($get_curso);

$get_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$participante[banco]'");
$banco     = mysql_fetch_assoc($get_banco);

// Variáveis
switch($tipo) {
   case 1:
   $banco1 = "folhas";
   $banco2 = "folha_autonomo";
   $coluna_id = "id_autonomo";
   $coluna_salario = "salario_liq";
   $coluna_contratacao = "AND contratacao = '1'";
   $ferias = NULL;
   break;
   
   case 2:   
   $banco1 = "rh_folha";
   $banco2 = "rh_folha_proc";  
   $coluna_id = "id_clt";
   $coluna_salario = "sallimpo_real";
   $coluna_contratacao = NULL;  
   $ferias = "AND ferias != '1'";
   break;
   
   case 3:
   $banco1 = "folhas";
   $banco2 = "folha_cooperado";
   $coluna_id = "id_autonomo";
   $coluna_salario = "salario_liq";
   $coluna_contratacao = "AND contratacao = '3'";
   $ferias = NULL;
   break;
   
   case 4:
   $banco1 = "folhas";
   $banco2 = "folha_cooperado";
   $coluna_id = "id_autonomo";
   $coluna_salario = "salario_liq";
   $coluna_contratacao = "AND contratacao = '4'";
   $ferias = NULL;
   break;
}



// MOVIMENTOS
if($tipo == '2') {

$qr_codigos = mysql_query("SELECT distinct(cod) FROM rh_movimentos ORDER BY cod ASC");
       while($row_codigos = mysql_fetch_array($qr_codigos)){
             $codigos[] = "a".$row_codigos['0'];
	   }

$qr_colunas_folha = mysql_query("DESCRIBE rh_folha_proc");
       while($row_colunas_folha = mysql_fetch_assoc($qr_colunas_folha)) {
             $colunas[] = $row_colunas_folha['Field'];
       }

$movimentos_consulta = array_intersect($codigos,$colunas);

// Loop dos Movimentos
for($a=0; $a<sizeof($movimentos_consulta); $a++) {
	
	// Loop dos Meses
	for($b=1; $b<13; $b++) {
		
		$tubarao = sprintf('%02d', $b);
		
		$qr_folha = mysql_query("SELECT * FROM $banco1 WHERE mes = '$tubarao' AND year(data_inicio) = '$ano_base' $coluna_contratacao AND projeto = '$projeto' AND regiao = '$regiao' AND terceiro != '1' AND ferias != '1' AND status = '3'");
		$folha = mysql_fetch_assoc($qr_folha);

		$qr_folha_individual = mysql_query("SELECT * FROM $banco2 WHERE id_folha = '$folha[id_folha]' AND $coluna_id = '$id' AND status = '3'");
		$folha_individual = mysql_fetch_assoc($qr_folha_individual);

		$cod_movimento = substr($movimentos_consulta[$a], 1);
		$qr_movimentos = mysql_query("SELECT * FROM rh_movimentos WHERE cod = '$cod_movimento'");
		$row_movimento = mysql_fetch_assoc($qr_movimentos);
		
		if($tubarao == '01') {
			$valores[] = "$cod_movimento";
			$valores[] = "$row_movimento[descicao]";
		}
		
		if((!empty($folha_individual[$movimentos_consulta[$a]])) and ($folha_individual[$movimentos_consulta[$a]] != "0.00")) {
			$valores[] = $folha_individual[$movimentos_consulta[$a]];
		} else {
			$valores[] = NULL;
		}
		
	} // Fim do Loop dos Meses
	
	for($c=2; $c<14; $c++) {
		$soma = $soma + $valores[$c];
	}
	
	// Se estiver vazio o total, não jogo a array Valores em Movimentos
	if(!empty($soma)) {
		$valores[] = $soma;
		$valores[] = "$row_movimento[categoria]";
		$movimentos[] = $valores;
	}
	
	// Resetando variáveis Soma e Valores
	unset($soma);
	unset($valores);
	
} // Fim do Loop dos Movimentos

unset($a);
unset($b);
unset($c);
unset($tubarao);

}
// FIM DOS MOVIMENTOS



// Rodando os 12 meses
for($m=1; $m<13; $m++) {
	
	$tubarao = sprintf('%02d', $m);
	
	
	
	// Salário Base
	if($tipo == '2') {
		if(date("$ano_base-$tubarao-01") > date('2010-06-09')) {
			$coluna_salario = "sallimpo_real";
		} else {
			$coluna_salario = "sallimpo";
		}
	}
	
	$qr_folha = mysql_query("SELECT * FROM $banco1 WHERE mes = '$tubarao' AND year(data_inicio) = '$ano_base' $coluna_contratacao AND projeto = '$projeto' AND regiao = '$regiao' AND terceiro != '1' $ferias AND status = '3'");
	$folha = mysql_fetch_assoc($qr_folha);
	
	$qr_folha_individual = mysql_query("SELECT * FROM $banco2 WHERE id_folha = '$folha[id_folha]' AND $coluna_id = '$id' AND status = '3'");
	$folha_individual = mysql_fetch_assoc($qr_folha_individual);
	
	if($tubarao == '01') {
		$salario[] = '0001';
		$salario[] = 'Salário Base';
	}
	
	if(!empty($folha_individual[$coluna_salario])) {
		$salario[] = $folha_individual[$coluna_salario];
	} else {
		$salario[] = NULL;
	}
	
	$soma1 += $folha_individual[$coluna_salario];
	
	

	// 13º Salário
	if($tipo == '2') {
		$coluna_salario = "salliquido";
	}
	
	$qr_folha_13 = mysql_query("SELECT * FROM $banco1 WHERE mes = '$tubarao' AND year(data_inicio) = '$ano_base' $coluna_contratacao AND projeto = '$projeto' AND regiao = '$regiao' AND terceiro = '1' $ferias AND status = '3'");
	$folha_13 = mysql_fetch_assoc($qr_folha_13);
	
	$qr_folha_individual_13 = mysql_query("SELECT * FROM $banco2 WHERE id_folha = '$folha_13[id_folha]' AND $coluna_id = '$id' AND status = '3'");
	$folha_individual_13 = mysql_fetch_assoc($qr_folha_individual_13);
	
	if($tubarao == '01') {
		$salario13[] = '5029';
		$salario13[] = '13º Salário';
	}
	
	if(!empty($folha_individual_13[$coluna_salario])) {
		$salario13[] = $folha_individual_13[$coluna_salario];
	} else {
		$salario13[] = NULL;
	}
	
	$soma2 += $folha_individual_13[$coluna_salario];


	
	// Férias
	if($tipo == '2') {
		
		$qr_ferias  = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id' AND ano = '$ano_base' AND mes = '$tubarao' AND status = '1'");
		$row_ferias = mysql_fetch_assoc($qr_ferias);
		
		if($tubarao == '01') {
			$salario_ferias[] = '0003';
			$salario_ferias[] = 'Férias';
		}
		
		if(!empty($row_ferias['total_liquido'])) {
			$salario_ferias[] = $row_ferias['total_liquido'];
		} else {
			$salario_ferias[] = NULL;
		}
		
		$soma3 += $row_ferias['total_liquido'];
	
	}


	
	// Rescisão
	$qr_rescisao  = mysql_query("SELECT * FROM rh_recisao WHERE id_clt = '$id' AND year(data_demi) = '$ano_base' AND month(data_demi) = '$tubarao' AND motivo IN (60,61,62,80,81,100) AND status = '1'");
	$row_rescisao = mysql_fetch_assoc($qr_rescisao);
	
	if($tubarao == '01') {
		$salario_rescisao[] = '4007';
		$salario_rescisao[] = 'Rescisão';
	}
	
	if(!empty($row_rescisao['total_liquido'])) {
		$salario_rescisao[] = $row_rescisao['total_liquido'];
	} else {
		$salario_rescisao[] = NULL;
	}
	
	$soma4 += $row_rescisao['total_liquido'];




} // Fim do Loop de 12 meses



$salario[]          = $soma1;
$salario13[]        = $soma2;
$salario_ferias[]   = $soma3;
$salario_rescisao[] = $soma4;



// Criando os Arrays
if(!empty($soma1)) {
	$rendimentos[] = $salario;
}

if(!empty($soma2)) {
	$rendimentos[] = $salario13;
}

if(!empty($soma3)) {
	$rendimentos[] = $salario_ferias;
}

if(!empty($soma4)) {
	$rendimentos[] = $salario_rescisao;
}

for($a2=0; $a2<sizeof($movimentos); $a2++) {
	if($movimentos[$a2][15] == 'CREDITO') {
	   $rendimentos[] = $movimentos[$a2];
	}
}

for($b2=0; $b2<sizeof($movimentos); $b2++) {
	if($movimentos[$b2][15] == 'DEBITO') {
	   $descontos[] = $movimentos[$b2];
	}
}

break;
}
?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>Ficha Financeira</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css">
</head>

<?php switch($tela) {
	case 1:
?>
<body>
    <div id="corpo">
         <div id="topo">
              <?php include('include/topo.php'); ?>
         </div>
         <div id="conteudo">
              <h1 style="margin:70px;"><span>RELATÓRIOS</span> FICHA FINANCEIRA</h1>
     <form style="margin-bottom:190px;">
        Selecione o Tipo de Contratação:
        <select onChange="location.href=this.value;" class="campotexto">
           <option disabled="disabled" selected>Escolha um tipo abaixo</option>
           <option value="<?=$_SERVER['PHP_SELF']?>?reg=<?=$regiao?>&pro=<?=$projeto?>&tipo=1&tela=2">Autônomo</option>
           <option value="<?=$_SERVER['PHP_SELF']?>?reg=<?=$regiao?>&pro=<?=$projeto?>&tipo=2&tela=2">CLT</option>
           <option value="<?=$_SERVER['PHP_SELF']?>?reg=<?=$regiao?>&pro=<?=$projeto?>&tipo=3&tela=2">Colaborador</option>
           <option value="<?=$_SERVER['PHP_SELF']?>?reg=<?=$regiao?>&pro=<?=$projeto?>&tipo=4&tela=2">Autônomo / PJ</option>  
        </select>
    </form>
</div>
<div id="rodape"></div>
</div>

<?php
break;
case 2:
?>
<script>
function exibe() {
        if (document.getElementById("ano_base").style.display == "none") { 
             document.getElementById("ano_base").style.display = "block";
        }
}
</script>
<body>
    <div id="corpo">
         <div id="topo">
              <?php include('include/topo.php'); ?>
         </div>
         <div id="conteudo">
              <h1 style="margin:70px;"><span>RELATÓRIOS</span> FICHA FINANCEIRA</h1>
        <?php if(empty($verifica)) { ?>
           <META HTTP-EQUIV=Refresh CONTENT="1; URL=<?=$_SERVER['PHP_SELF']?>?reg=<?=$regiao?>&pro=<?=$projeto?>&tela=1">
           <span style="color:#C30; font-size:13px; font-weight:bold;">Nenhum Participante</span>
        <?php } else { ?>
      <form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="form" style="margin-bottom:200px;">
       <?php if(empty($id)) { ?>
        Selecione o Participante:
        <select name="id" class="campotexto">
           <option disabled="disabled" selected>Escolha um participante abaixo</option>
             <?php while($participante = mysql_fetch_assoc($result)) { ?>
           <option id="participante" onClick="exibe()" value="<?=$participante[$coluna_id]?>"><?=$participante['nome']?></option> 
             <?php } ?>
             </select>
              <?php } else { ?>
             <span style="color:#C30"><?=$participante['nome']?></span>
             <?php } ?>
        
        <p id="ano_base" style=" <?php if(empty($id)) { ?>display:none;<?php } ?> margin:0px auto;">Selecione o Ano:
        <select name="ano_base" class="campotexto">
           <option disabled="disabled" selected>Escolha um ano abaixo</option>
             <?php for($a='2004'; $a<=date('Y'); $a++) { ?>
           <option value="<?=$a?>" <?php if($a == date('Y')) { ?>selected<?php } ?>><?=$a?></option> 
             <?php } ?>
        </select>
        <input type="submit" class="botao" value="Gerar Ficha">
        </p>
         <?php if(!empty($id)) { ?>
       <input type="hidden" name="id" value="<?=$participante[$coluna_id]?>">
         <?php } ?>
       <input type="hidden" name="pro" value="<?=$projeto?>">
       <input type="hidden" name="reg" value="<?=$regiao?>">
       <input type="hidden" name="tipo" value="<?=$tipo?>">
       <input type="hidden" name="tela" value="3">
     </form>
       <?php } ?>
</div>
<div id="rodape"></div>
</div>

<?php
break;
case 3:
?>
<body style="background-color:#FFF; margin-top:30px;">
<table cellspacing="0" cellpadding="0" class="relacao" style="width:920px; border:0px; page-break-after:always;">
 <tr> 
    <td width="20%" align="center">
          <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
    </td>
    <td width="80%" align="center" colspan="2">
         <strong>FICHA FINANCEIRA <?=$ano_base?></strong><br>
         <?=$row_master['razao']?>
         <table width="300" border="0" align="center" cellpadding="4" cellspacing="1" style="font-size:12px;">
            <tr style="color:#FFF;">
              <td width="150" height="22" align="center" class="top">PROJETO</td>
              <td width="150" align="center" class="top">REGIÃO</td>
            </tr>
            <tr style="color:#333; background-color:#efefef;">
              <td height="20" align="center"><b><?=$row_projeto['nome']?></b></td>
              <td align="center"><b><?=$row_projeto['regiao']?></b></td>
            </tr>
        </table>
    </td>
  </tr>
  <tr> 
    <td colspan="3">
    <table width="980" border="0" cellspacing="2" cellpadding="3" class="relacao" style="font-weight:normal; line-height:22px; margin-top:30px;">
      <tr class="secao_pai">
        <td colspan="15"><?=$participante[$coluna_id]?> - <?=$participante['nome']?></td>
        </tr>
      <tr>
        <td class="secao" style="width:10%">Endere&ccedil;o:</td>
        <td colspan="8" style="width:50%">
		<?php echo "$participante[endereco]"; 
		      if(!empty($participante['bairro'])) { 
			       echo ", $participante[bairro]"; 
			  } if(!empty($participante['cidade'])) { 
			       echo ", $participante[cidade]"; 
			  } if(!empty($participante['uf'])) { 
			       echo " - $participante[uf]"; 
			  } ?></td>
        <td class="secao" style="width:10%">Nascimento:</td>
        <td colspan="2" style="width:10%"><?=$participante['data_nasci']?></td>
        <td class="secao" style="width:10%">Nacionalidade:</td>
        <td colspan="2" style="width:10%"><?=$participante['nacionalidade']?></td>
        </tr>
      <tr>
        <td class="secao" style="width:10%">Cargo:</td>
        <td colspan="8" style="width:50%"><?=$curso['nome']?></td>
        <td class="secao" style="width:10%">Admiss&atilde;o:</td>
        <td colspan="2" style="width:10%"><?=$participante['data_entrada']?></td>
        <td class="secao" style="width:10%">Afastamento:</td>
        <td colspan="2" style="width:10%"><?php if($participante['data_saida'] != "00/00/0000") { echo $participante['data_saida']; } ?></td>
        </tr>
        <tr>
        <td class="secao">Tipo de Pag:</td>
        <td colspan="2"><?=$pg['tipopg']?></td>
        <td class="secao">Sal&aacute;rio:</td>
        <td colspan="2"><?php echo "R$ "; echo number_format($curso['salario'], 2, ',', '.'); ?></td>
        <td class="secao">Ag&ecirc;ncia:</td>
        <td colspan="2"><?=$participante['agencia']?></td>
        <td class="secao">Conta:</td>
        <td colspan="2"><?=$participante['conta']?></td>
        <td class="secao">Banco:</td>
        <td colspan="2"><?=$banco['nome']?></td>
        </tr>
      <tr>
        <td class="secao">CPF:</td>
        <td colspan="2"><?=$participante['cpf']?></td>
        <td class="secao">RG:</td>
        <td colspan="2"><?=$participante['rg']?></td>
        <td class="secao">T&iacute;tulo:</span></td>
        <td colspan="2"><?=$participante['titulo']?></td>
        <td class="secao">CTPS:</td>
        <td colspan="2"><?=$participante['serie_ctps']?></td>
        <td class="secao">PIS/PASEP:</td>
        <td colspan="2"><?=$participante['pis']?></td>
        </tr>
      <tr class="secao">
        <td>Evento</td>
        <td>Descri&ccedil;&atilde;o</td>
        <td colspan="13">
          
          <table cellpadding="0" cellspacing="0" width="100%" class="relacao" style="border:0px;">
            <tr>
              <td style="width:7%" align="center">Jan</td>
              <td style="width:7%" align="center">Fev</td>
              <td style="width:7%" align="center">Mar</td>
              <td style="width:7%" align="center">Abr</td>
              <td style="width:7%" align="center">Mai</td>
              <td style="width:7%" align="center">Jun</td>
              <td style="width:7%" align="center">Jul</td>
              <td style="width:7%" align="center">Ago</td>
              <td style="width:7%" align="center">Set</td>
              <td style="width:7%" align="center">Out</td>
              <td style="width:7%" align="center">Nov</td>
              <td style="width:7%" align="center">Dez</td>
              <td style="width:16%" align="center">Total</td>
              </tr>
            </table>
          
          </td>
      </tr>
      
    <?php for($a=0; $a<sizeof($rendimentos); $a++) { ?>
         <tr class="<?php if($alternateColor++%2==0) { echo 'linha_um'; } else { echo 'linha_dois'; } ?>">
              <?php for($b=0; $b<2; $b++) { ?>
                  <td><?=$rendimentos[$a][$b]?></td>
			  <?php } ?>
                    
                  <td colspan="13">
                        <table cellpadding="0" cellspacing="0" width="100%" class="relacao" style="border:0px;">
                            <tr style="font-size:10px;">
                                  <?php for($c=2; $c<14; $c++) { ?>
                                        <td style="width:7%" align="center"><?php if(!empty($rendimentos[$a][$c])) { echo formata($rendimentos[$a][$c]); } else { echo "&nbsp;"; } ?></td>
                                  <?php } ?>
                                        <td style="width:16%" align="center"><?php echo formata2($rendimentos[$a][14]); ?></td>
                                  </tr>
                               </table>
                              </td>         
                            </tr>
                                  <?php $soma_total_rendimentos = $soma_total_rendimentos + $rendimentos[$a][14]; }
								  
								  for($d=2; $d<14; $d++) {
									  for($e=0; $e<sizeof($rendimentos); $e++) {
										      $in_soma_rendimento = $in_soma_rendimento + $rendimentos[$e][$d];
									  }								  
										$soma_rendimento = $soma_rendimento + $in_soma_rendimento;
										$total_rendimentos[] = $soma_rendimento;
										unset($in_soma_rendimento);
										unset($soma_rendimento);
	                               } ?>
      <tr class="secao">
        <td style="background-color:#FFF;">&nbsp;</td>
        <td>Total Rendimentos:</td>
        <td colspan="13">
                        <table cellpadding="0" cellspacing="0" width="100%" class="relacao" style="border:0px;">
                            <tr style="font-size:10px;">
                            <?php for($f=0; $f<12; $f++) { ?>
        <td style="width:7%" align="center"><?php if(!empty($total_rendimentos[$f])) { echo formata2($total_rendimentos[$f]); } else { echo "&nbsp;"; } ?></td>
                            <?php } ?>
        <td style="width:16%" align="center"><?php echo formata2($soma_total_rendimentos); ?></td>
      </tr>
                               </table>
                              </td>         
                            </tr>
     
     <?php for($g=0; $g<sizeof($descontos); $g++) { ?>
         <tr class="<?php if($alternateColor++%2==0) { echo "linha_um"; } else { echo "linha_dois"; } ?>">
              <?php for($h=0; $h<2; $h++) { ?>
                  <td><?=$descontos[$g][$h]?></td>
			  <?php } ?>
                    
                  <td colspan="13">
                        <table cellpadding="0" cellspacing="0" width="100%" class="relacao" style="border:0px;">
                            <tr style="font-size:10px;">
                                  <?php for($i=2; $i<14; $i++) { ?>
                                        <td style="width:7%" align="center"><?php if(!empty($descontos[$g][$i])) { echo formata($descontos[$g][$i]); } else { echo "&nbsp;"; } ?></td>
                                  <?php } ?>
                                        <td style="width:16%" align="center"><?php echo formata2($descontos[$g][14]); ?></td>
                                  </tr>
                               </table>
                              </td>         
                            </tr>
                        <?php $soma_total_descontos = $soma_total_descontos + $descontos[$g][14]; }
								  
								  for($j=2; $j<14; $j++) {
									  for($k=0; $k<sizeof($descontos); $k++) {
										      $in_soma_desconto = $in_soma_desconto + $descontos[$k][$j];
									  }								  
										$soma_desconto = $soma_desconto + $in_soma_desconto;
										$total_descontos[] = $soma_desconto;
										unset($in_soma_desconto);
										unset($soma_desconto);
	                               } ?>
      <tr class="secao">
        <td style="background-color:#FFF;">&nbsp;</td>
        <td>Total Descontos:</td>
        <td colspan="13">
     <table cellpadding="0" cellspacing="0" width="100%" class="relacao" style="border:0px;">
       <tr style="font-size:10px;">
         <?php for($l=0; $l<12; $l++) { ?>
        <td style="width:7%" align="center"><?php if(!empty($total_descontos[$l])) { echo formata2($total_descontos[$l]); } else { echo "&nbsp;"; } ?></td>
         <?php } ?>
        <td style="width:16%" align="center"><?php echo formata2($soma_total_descontos); ?></td>
      </tr>
        </table>
       </td> 
      </tr>
      <tr class="secao">
        <td style="background-color:#FFF;">&nbsp;</td>
        <td>Valor L&iacute;quido:</td>
        <td colspan="13">
     <table cellpadding="0" cellspacing="0" width="100%" class="relacao" style="border:0px;">
       <tr style="font-size:10px;">
        <?php for($n=0; $n<12; $n++) { ?>
        <td style="width:7%" align="center">
		<?php $valor_liquido = $total_rendimentos[$n] - $total_descontos[$n]; if(!empty($valor_liquido)) { echo formata2($valor_liquido); } else { echo "&nbsp;"; } unset($valor_liquido); ?>
        </td>
         <?php } ?>
        <td style="width:16%" align="center"><?php $valor_liquido_total = $soma_total_rendimentos - $soma_total_descontos; echo formata2($valor_liquido_total); ?></td>
      </tr>
    </table>
       </td>
      </tr>
    </table>
	</td>
  </tr>
</table>
<?php break; } ?>
</body>
</html>
<?php } ?>