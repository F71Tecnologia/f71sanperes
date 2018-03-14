<?php
require_once("../../conn.php");

$mes = $_REQUEST['mes'];
$ano = $_REQUEST['ano'];
$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]' ") or die(mysql_error());
$row_user = mysql_fetch_assoc($qr_user);

$qr_master   = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);

$qr_regioes = mysql_query("SELECT * FROM regioes WHERE id_master = '$row_master[id_master]'");
while($row_regioes = mysql_fetch_assoc($qr_regioes)):

$regioes[] = $row_regioes['id_regiao'];


endwhile;
$regioes = implode(',', $regioes);

$mes2 	  = sprintf('%02d',$mes);
$nome_mes = mysql_result(mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$mes2' "), 0);



$qr_clt_demitidos = mysql_query("SELECT * FROM rh_clt WHERE YEAR(data_demi) = '$ano' AND MONTH(data_demi) = '$mes' AND status IN('60','61','62','81','100','80','63') AND id_regiao IN($regioes) ORDER BY nome ASC;") ;

$qr_clt_admitidos = mysql_query("SELECT * FROM rh_clt WHERE YEAR(data_entrada) = '$ano' AND MONTH(data_entrada) = '$mes' AND (status != '60' OR status != '61' OR status != '62' OR status != '81' OR status != '100' OR status != '80' OR status != '63') AND id_regiao IN($regioes) ORDER BY nome ASC;") ;

function verifica($str,$quant){
	if(empty($str)) return false;
	$str = str_replace('.','',$str);
	$str = str_replace('-','',$str);
	$str = str_replace(',','',$str);
	$str = str_replace('/','',$str);
	if(strlen($str) == $quant){
		return false;
	}else{
		return true;
	}
}

function Limpar($str){
	return str_replace(',','',str_replace('-','',str_replace('.','',$str)));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Rela&ccedil;&atilde;o de dados Caged</title>
<link href="css/estilo_relacao.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../../js/highslide.css" />
<script type="text/javascript" src="../../js/highslide-with-html.js"></script>
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript" src="../../jquery/jquery.tools.min.js" ></script>
<script type="text/javascript">
$(function(){
	var parametros = {
		position: "center right",
		onShow: function() {
			this.getTrigger().css('color','#FFF');
		},
		onHide: function() {
			this.getTrigger().css('color','#333');
		}
	};
	$('a.erros').tooltip(parametros);
});
</script>
<script type="text/javascript">
    hs.graphicsDir = '../../images-box/graphics/';
    hs.outlineType = 'rounded-white';
	
</script>
<style type="text/css">
a.erros{
	font-size:12px;
	text-decoration: none;
	font-weight: bold;
	color: #333;
}
.tooltip{
	background-color:#FFF;
	padding:10px;
	-moz-border-radius: 5px; 
	-webkit-border-radius: 5px;
	border-radius: 5px;
	-moz-box-shadow: 0 0 15px #666;
	-webkit-box-shadow: 0 0 15px #666;	
}
</style>
</head>
<body>
<div id="conteiner">

<div id="logo">
	
    
    
</div>


  <table width="100%" border="0" cellspacing="1" cellpadding="2">
  <tr height="100">
  	 <td colspan="17" align="center" valign="middle">
     
     
     <img src="../../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif"/>
    <br />
    <strong><?php echo $nome_mes; ?>/<?php echo $ano;?></strong>
     
     
     </td>
  </tr>
    <tr>
      <td colspan="17"><span class="titulo1">Admitidos</span></td>
    </tr>
    <tr>
      <th align="left">&nbsp;</th>
      <th align="left">id</th>
      <th align="left">nome</th>
      <th align="left">sexo</th>
      <th align="left">salario</th>
      <th align="left">horas</th>
      <th align="left">admiss&atilde;o</th>
      <th align="left">movimento</th> 
    </tr>
<?php while($row_admitidos = mysql_fetch_assoc($qr_clt_admitidos)):?>
<?php
	// pegando o salario
	$qr_curso = mysql_query("SELECT salario,cbo_codigo,id_curso FROM curso WHERE id_curso = '$row_admitidos[id_curso]'");
	$row_curso = mysql_fetch_assoc($qr_curso);
	$num_salario = mysql_num_rows($qr_curso);
	$salario = number_format($row_curso['salario'],2,',','.');
	
	//pegando a quantidade de horas 
	$qr_horario = mysql_query("SELECT horas_mes FROM rh_horarios WHERE funcao = '$row_curso[id_curso]'");
	$total_mes = mysql_fetch_assoc($qr_horario);
	$total_mes = $total_mes['horas_mes'];
	$horas_semanal = ceil($total_mes/4);
	if($horas_semanal > 44){
		$horas_semanal = 44;
	}
	$horas = sprintf('%02d',$horas_semanal);
	// BUSCANDO 
	$qr_etnia 	= mysql_query("SELECT cod FROM etnias WHERE id = '$row_admitidos[etnia]'");
	$row_etinia 		= mysql_fetch_assoc($qr_etnia);

	//
	  $tipo_movimento[10] = "Primeiro emprego";
	  $tipo_movimento[20] = "Reemprego";
	  $tipo_movimento[25] = "Contrato por prazo determinado";	
	  $tipo_movimento[35] = "Reintegração";
	  $tipo_movimento[70] = "Transferência de entrada";
	  $tipo_movimento[51] = "Dispensa sem justa causa";
	  $tipo_movimento[31] = "Dispensa por justa causa";
	  $tipo_movimento[32] = "A pedido(espontâneo)";
	  $tipo_movimento[40] = "Término de contrato por prazo determinado";
	  $tipo_movimento[43] = "Término de contrato";
	  $tipo_movimento[45] = "Aposentado";
	  $tipo_movimento[50] = "Morte";
	  $tipo_movimento[60] = "Transferência de saída";
	  foreach($tipo_movimento as $cod => $tipo){
			if($row_admitidos['status_admi'] == $cod){
				$movimento = $tipo;
				$cod_movimento = $cod;
				break;
			}
	  }
	  
// VERIFICAÇÔES 
	// PIS
	if(empty($row_admitidos['pis']) or strlen(Limpar($row_admitidos['pis'])) <> 11)
		$erro['pis'] = "PIS não e válido.";
	//SEXO
	if(empty($row_admitidos['sexo']))
		$erro['sexo'] = "O sexo não pode estar vazio";
	// NASCIMENTO
	if(empty($row_admitidos['data_nasci']))
		$erro['nascimento'] = "Data de nascimento inválida!";
	// Grau de instrução	
	$valores = array(1,2,3,4,5,6,7,8,9,10,11);
	if(empty($row_admitidos['escolaridade']) or !in_array($row_admitidos['escolaridade'],$valores))
		$erro['escolaridade'] = "Grau de instrução invalido.";		
	// Horario
	if(empty($total_mes['horas_mes']))
		$erro['horas'] = "Horario não expecificado.";
	// Salário
	if(empty($num_salario))
		$erro['salario'] = "Salario não expecificado.";
	// Data de admissão
	if(empty($row_admitidos['data_entrada']))
		$erro['admissao'] = "Data de admissão não expecificada";
	// MOVIMENTO
	$valores = array(10,20,25,35,70,31,32,40,43,45,50,60,80);
	if(empty($row_admitidos['status_admi']) or !in_array($row_admitidos['status_admi'],$valores))
		$erro['status_admin'] = "Movimento não expecificado.";
	//NOME do empregado
	if(empty($row_admitidos['nome']))
		$erro['nome'] = "Nome inválido";
	//CARTEIRA DE TRABALHO
	if(empty($row_admitidos['campo1']))
		$erro['ctps'] = "Numero da carteira de trabalho inválido!";
	// UF da carteira de trabalho
	if(empty($row_admitidos['serie_ctps']))
		$erro['serie'] = "Serie da carteira de trabalho inválido.";
	// Raça
	if(empty($row_etinia['cod']))
		$erro['raca'] = "Etnia inválida.";
	// CBO
	if(empty($row_curso['cbo_codigo']))
		$erro['cbo'] = "CBO inválido";
	// UF ctps
	if(empty($row_admitidos['uf_ctps']))
		$erro['uf'] = "UF da carteira de trabalho invalido.";
	// CPF
	if(empty($row_admitidos['cpf']) or strlen(Limpar($row_admitidos['cpf'])) <> 11)
		$erro['cpf'] = "CPF inválido.";
	// CEP
	if(empty($row_admitidos['cep']) or strlen(Limpar($row_admitidos['cep'])) <> 8)
		$erro['cep'] = "CEP inválido.";
	
	// VERIFICAÇÔES 
	if($linha++%2==0){
		$classe_linha = "linha_um";
	}else{
		$classe_linha = "linha_dois";
	}
	if(!empty($erro)){
		$classe_linha = "linha_erro";
	}
?>
    
    <tr class="<?=$classe_linha?>">
      <td><input name="check[]" type="checkbox" value="<?=$row_admitidos['id_clt']?>" checked="checked" class="checkbox" /></td>
      <td><?=$row_admitidos['id_clt']?></td>
      <td>
		<?php if(!empty($erro)):?>
    <a class="erros" title="<?=implode("<br>",$erro)?>" href="Edita.php?ID=<?=$row_admitidos['id_clt']?>&<?=implode('&',array_keys($erro))?>" onclick="return hs.htmlExpand(this, { objectType: 'iframe', width: 540 } )">
        	<?=$row_admitidos['nome']?>
        </a>
        <?php else:?>
        <?=$row_admitidos['nome']?>
        	
        <?php endif;?>
        (<?php 
			$qr_regiao = mysql_query("SELECT id_regiao, regiao FROM regioes WHERE id_regiao = '$row_admitidos[id_regiao]' LIMIT 1;");
			$row_regiao = mysql_fetch_array($qr_regiao);
			echo $row_regiao[0] . '-' . $row_regiao[1];
		?>)
      </td>
      <td><?=$row_admitidos['sexo']?></td>
      <td><?= "R$ $salario"?></td>
      <td><?=$horas."hs"?></td>
      <td><?=implode('/',array_reverse(explode('-',$row_admitidos['data_entrada'])))?></td>
      <td><?=$movimento?></td>
    </tr>
   <?php 
   unset($erro);
   endwhile;?>
  </table>
  
  <table width="100%" border="0" cellspacing="1" cellpadding="2">
  <tr>
    <td colspan="19"><span class="titulo1">Demitidos</span></td>
  </tr>
  <tr>
    <th align="left">&nbsp;</th>
    <th align="left">id</th>
    <th align="left">nome</th>
    <th align="left">sexo</th>
    <th align="left">salario</th>
    <th align="left">horas</th>
    <th align="left">admiss&atilde;o</th>
    <th align="left">Demiss&atilde;o</th>
    <th align="left">movimento</th>
  </tr>
<?php while($row_demitido = mysql_fetch_assoc($qr_clt_demitidos)):?> 
<?php
	// pegando o salario
	$qr_curso = mysql_query("SELECT salario,cbo_codigo FROM curso WHERE id_curso = '$row_demitido[id_curso]'");
	$row_curso = mysql_fetch_assoc($qr_curso);
	$num_salario = mysql_num_rows($qr_curso);
	$salario = number_format($row_curso['salario'],2,',','.');
	
	//pegando a quantidade de horas 
	$qr_horario = mysql_query("SELECT horas_mes FROM rh_horarios WHERE id_horario = '$row_demitido[rh_horario]'");
	$total_mes = mysql_fetch_assoc($qr_horario);
	$total_mes = $total_mes['horas_mes'];
	$horas_semanal = ceil($total_mes/4);
	if($horas_semanal > 44){
		$horas_semanal = 44;
	}
	$horas = sprintf('%02d',$horas_semanal);
	// 
	$qr_etnia 	= mysql_query("SELECT cod FROM etnias WHERE id = '$row_demitido[etnia]'");
	$row_etinia = mysql_fetch_assoc($qr_etnia);

	//
	  $tipo_movimento[10] = "Primeiro emprego";
	  $tipo_movimento[20] = "Reemprego";
	  $tipo_movimento[25] = "Contrato por prazo determinado";	
	  $tipo_movimento[35] = "Reintegração";
	  $tipo_movimento[70] = "Transferência de entrada";
	  $tipo_movimento[51] = "Dispensa sem justa causa";
	  $tipo_movimento[31] = "Dispensa por justa causa";
	  $tipo_movimento[32] = "A pedido(espontâneo)";
	  $tipo_movimento[40] = "Término de contrato por prazo determinado";
	  $tipo_movimento[43] = "Término de contrato";
	  $tipo_movimento[45] = "Aposentado";
	  $tipo_movimento[50] = "Morte";
	  $tipo_movimento[60] = "Transferência de saída";
	  foreach($tipo_movimento as $cod => $tipo){
			if($row_demitido['status_admi'] == $cod){
				$movimento = $tipo;
				$cod_movimento = $cod;
				break;
			}
	  }
	  
// VERIFICAÇÔES 
	// PIS
	if(empty($row_demitido['pis']) or strlen(Limpar($row_demitido['pis'])) <> 11)
		$erro['pis'] = "PIS não e válido.";
	//SEXO
	if(empty($row_demitido['sexo']))
		$erro['sexo'] = "O sexo não pode estar vazio";
	// NASCIMENTO
	if(empty($row_demitido['data_nasci']))
		$erro['nascimento'] = "Data de nascimento inválida!";
	// Grau de instrução	
	$valores = array(1,2,3,4,5,6,7,8,9,10,11);
	if(empty($row_demitido['escolaridade']) or !in_array($row_demitido['escolaridade'],$valores))
		$erro['escolaridade'] = "Grau de instrução invalido.";		
	// Horario
	if(empty($total_mes['horas_mes']))
		$erro['horas'] = "Horario não expecificado.";
	// Salário
	if(empty($num_salario))
		$erro['salario'] = "Salario não expecificado.";
	// Data de admissão
	if(empty($row_demitido['data_entrada']))
		$erro['admissao'] = "Data de admissão não expecificada";
	// MOVIMENTO
	$valores = array(10,20,25,35,70,31,32,40,43,45,50,60,80);
	if(empty($row_demitido['status_admi']) or !in_array($row_demitido['status_admi'],$valores))
		$erro['status_admin'] = "Movimento não expecificado.";
	//NOME do empregado
	if(empty($row_demitido['nome']))
		$erro['nome'] = "Nome inválido";
	//CARTEIRA DE TRABALHO
	if(empty($row_demitido['campo1']))
		$erro['ctps'] = "Numero da carteira de trabalho inválido!";
	// UF da carteira de trabalho
	if(empty($row_demitido['serie_ctps']))
		$erro['serie'] = "Serie da carteira de trabalho inválido.";
	// Raça
	if(empty($row_etinia['cod']))
		$erro['raca'] = "Etnia inválida.";
	// CBO
	if(empty($row_curso['cbo_codigo']))
		$erro['cbo'] = "CBO inválido";
		
	// UF ctps
	if(empty($row_demitido['uf_ctps']))
		$erro['uf'] = "UF da carteira de trabalho invalido.";
	// CPF
	if(empty($row_demitido['cpf']) or strlen(Limpar($row_demitido['cpf'])) <> 11)
		$erro['cpf'] = "CPF inválido.";
	// CEP
	if(empty($row_demitido['cep']) or strlen(Limpar($row_demitido['cep'])) <> 8)
		$erro['cep'] = "CEP inválido.";
		
		  
  	if($linha2++%2==0){
		$classe_linha = "linha_um";
	}else{
		$classe_linha = "linha_dois";
	}
    if(!empty($erro)){
	 	$classe_linha = "linha_erro";
	}
?>

  <tr class="<?=$classe_linha?>">
    <td><input name="check[]" type="checkbox" value="<?=$row_demitido['id_clt']?>" checked="checked" class="checkbox" /></td>
    <td><?=$row_demitido['id_clt']?></td>
    <td>
    <?php if(!empty($erro)):?>
    	<a class="erros" title="<?=implode("<br>",$erro)?>" href="Edita.php?ID=<?=$row_demitido['id_clt']?>&<?=implode('&',array_keys($erro))?>" onclick="return hs.htmlExpand(this, { objectType: 'iframe'})">
		<?=$row_demitido['nome']?>
        </a>
    <?php else:?>
    <?=$row_demitido['nome']?> (<?php 
			$qr_regiao = mysql_query("SELECT id_regiao, regiao FROM regioes WHERE id_regiao = '$row_demitido[id_regiao]' LIMIT 1;");
			$row_regiao = mysql_fetch_array($qr_regiao);
			echo $row_regiao[0] . '-' . $row_regiao[1];
		?>)
    <?php endif;?>
    </td>
    <td><?=$row_demitido['sexo']?></td>
    <td><?= "R$ $salario"?></td>
    <td><?=$horas."hs"?></td>
    <td><?=implode('/',array_reverse(explode('-',$row_demitido['data_entrada'])))?></td>
    <td><?=implode('/',array_reverse(explode('-',$row_demitido['data_demi'])))?></td>
    <td><?=$movimento?></td>
  </tr>
  <?php 
   unset($erro);
   endwhile;?>
</table>
<table width="100%">
	<tr>
      <td align="center">
      <script type="text/javascript">
      $(function(){
		 
			$('.checkbox').change(function(){
				 var intercesao = new Array;
				$('.checkbox').not(':checked').each(function(){
					
					intercesao.push($(this).val());
                                       
				});
			   var valor_f =  intercesao.join(',');
				$('#intercesao').val(valor_f);
				intercesao = null;
                                
			}); 
	  });
      </script>
      <form id="form1" name="form1" method="post" action="actions/cadastro.caged.php">
         <input type="hidden" name="intercesao" value="" id="intercesao" />
      	 <input type="hidden" name="mes" value="<?=$_GET['mes']?>" />
         <input type="hidden" name="ano" value="<?=$_GET['ano']?>" />
      	 <input type="submit" value="Gerar arquivo do caged"/>
  	  </form>
      </td>
    </tr>
</table>
</div>

</body>
</html>