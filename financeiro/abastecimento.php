<?php
include ("include/restricoes.php");
include('../conn.php');

$ids_acesso = array('64','65','68','9','27','5','1','77','80','85','87','260');

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

$ano    = $_REQUEST['ano'];
$mes    = $_REQUEST['mes'];
$inicio = implode('-', array_reverse(explode('/',$_REQUEST['inicio'])));
$fim    = implode('-', array_reverse(explode('/',$_REQUEST['fim'])));

if(!empty($_REQUEST['periodos'])) {
	$select   = 'AND data_libe BETWEEN \''.$inicio.'\' AND \''.$fim.'\'';
	$mensagem = 'ENTRE '.implode('/',array_reverse(explode('-',$inicio))).' E '.implode('/',array_reverse(explode('-',$inicio)));
	
} else {
	
	if(!empty($_REQUEST['anotodo'])) {
		$select   = 'AND YEAR(data_libe)  = '.$ano;
		$mensagem = 'EM '.$ano;
	} else {
		$select   = 'AND MONTH(data_libe) = '.$mes.' AND YEAR(data_libe) = '.$ano;
		$mensagem = 'EM <span style="text-transform:uppercase;">'.$meses[(int)$mes].'</span> DE '.$ano;
	}
}


$id_user    = $_COOKIE['logado'];
$qr_user    = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user   = mysql_fetch_array($qr_user);
$qr_master  = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($qr_master);

settype($array_regioes,'array');
$qr_regioes_cadastro = mysql_query("SELECT DISTINCT(id_regiao) FROM fr_combustivel WHERE status_reg = '2'");
while($row_regioes_cadastro = mysql_fetch_array($qr_regioes_cadastro)) {
	$array_regioes[] = $row_regioes_cadastro['id_regiao'];
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: Intranet :: Abastecimentos</title>
<link href="../rh/folha/sintetica/folha.css" rel="stylesheet" type="text/css">
<link href="../favicon.ico" rel="shortcut icon">
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/ramon.js"></script>
<script type="text/javascript">
$(function(){
$('.ajax').change(function(){
	$.post('../classes/ajaxupdate.php',{
		tabela:'fr_combustivel',
		valor:$(this).val(),
		campo:'valor',
		nomeid:'id_combustivel',
		id:$(this).attr('alt'),
		tipo:'2'
	}, function(r){
		if(r == "ERRO") {
			$(this).css('background','#FFC8BF');
		}
	});
});
});

$(function(){
  $('.ajax2').change(function(){
   $.post('../frota/update_data_abastecimento.php',
       {id:$(this).attr('alt'), data:$(this).val()});
  })
})
</script>
</head>
<body>
<div id="corpo">
<table cellspacing="4" cellpadding="0" id="topo">
  <tr>
    <td valign="middle" align="center">
      <img src="../imagensmenu2/c1.gif" alt="cobrinha" width="20" height="14">
      RELAT&Oacute;RIOS DE ABASTECIMENTOS <strong><?=$mensagem?></strong>
    </td>
  </tr>
</table>

<table cellpadding="0" cellspacing="1" id="folha" style="margin-bottom:-45px;">
 <tr>
  <td>
    <a href="../frota/frota.php" class="voltar">Voltar</a>
  </td>
 </tr>
</table>
     <table cellpadding="0" cellspacing="1" id="folha" style="margin-top:30px;">       
    <?php foreach($array_regioes as $valor) {
			
		$qr_regioes = mysql_query("SELECT * FROM regioes WHERE id_master = '$row_user[id_master]' AND id_regiao = '$valor'");
		$row_regiao = mysql_fetch_array($qr_regioes);
		
		
		$qr = mysql_query("SELECT *, date_format(data_libe, '%d/%m/%Y') AS data_libe FROM fr_combustivel WHERE status_reg = '2' AND id_regiao = '$valor' AND status_reg = '2' $select ORDER BY data_libe ASC");  		
		$total = mysql_num_rows($qr);
	
			
		if(!empty($total)) { ?>
    
    
        <tr>
          <td class="secao_pai" colspan="7">
            Abastecimentos em <?=$row_regiao['regiao']?>	
          </td>
        </tr>
        <tr class="secao">
          <td>C&oacute;digo</td>
          <td>Autorizado Para:</td>
          <td>Placa</td>
          <td>Autorizado em:</td>
          <td>Autorizado Por:</td>
          <td>N&uacute;mero da Nota</td>
          <td>Valor</td>
        </tr>
        
        <?php while($row = mysql_fetch_array($qr)) {
				
				$valor = number_format($row['valor'],2,',','.');
			
				$qr_autorizado = mysql_query("SELECT nome1 FROM funcionario WHERE id_funcionario = '$row[user_libe]'");
				$autorizado = @mysql_result($qr_autorizado,0);
			
				if($row['funcionario'] == 1) {
					$qr_autorizador = mysql_query("SELECT nome1 FROM funcionario WHERE id_funcionario = '$row[id_user]'");  $nome = @mysql_result($qr_autorizador,0);
				} else {
					$nome = $row['nome'];
				}
				
				if($row['interno'] == 1) {
					$qr_carro = mysql_query("SELECT placa FROM fr_carro WHERE id_carro = '$row[id_carro]'");
					$placa    = @mysql_result($qr_carro,0);
				} else {
					$placa = $row['placa'];
				} ?>
            
        <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
          <td><?=$row[0]?></td>
          <td><?=$nome?></td>
		  <td><?=$placa?></td>
		  <td><?php if(in_array($id_user,$ids_acesso)) { ?>
          	      <input alt="<?=$row[0]?>" style="text-align:center;" type="text" name="data_libe" id="data_libe[]" value="<?=$row['data_libe']?>" size="10" class="ajax2">
              <?php } else {
				        echo $row['data_libe'];
			  		} ?></td>
          <td><?=$autorizado?></td>
          <td><?=$row['numero']?></td>
          <td><?php if(in_array($id_user,$ids_acesso)) { ?>
          	      <input alt="<?=$row[0]?>" style="text-align:center;" type="text" name="valor" id="valor[]" value="<?=$valor?>" size="10" class="ajax" onKeyDown="FormataValor(this,event,17,2)">
		 	  <?php } else {
						echo $valor;
					} ?></td>
        </tr>
        
		<?php $valor_total += str_replace(',','.',str_replace('.', '',$valor));
		
		} ?>
        
        <tr>
          <td><a href="#corpo" class="ancora">Subir ao topo</a></td>
          <td colspan="5" align="right"><b>Total em <?=$row_regiao['regiao']?>:</b></td>
          <td align="center"><b><?=number_format($valor_total, 2,',','.')?></b></td>
        </tr>
 
      
      <?php } 
	  
	  $valor_total_geral += $valor_total;
	  unset($valor_total,$valor);
	  
	  } ?>
     </table>
<div style="font-size:13px; font-weight:bold; background-color:#C30; color:#FFF; text-align:center; padding:4px; margin:30px 0 60px 0;">TOTAL DE ABASTECIMENTO NO PER&Iacute;ODO: <?=number_format($valor_total_geral, 2,',','.')?></div>

<?php if(in_array($id_user,$ids_acesso)) {
	
	  include('../classes/funcionario.php');
      $Nfun = new funcionario(); ?>


    <table cellpadding="0" cellspacing="1" id="folha">
    <tr>
       <td class="secao_pai" colspan="3">Relat&oacute;rio Individual</td>
    </tr>
    <tr class="secao">
      <td colspan="2">Funcion&aacute;rio</td>
      <td>Valor</td>
    </tr>
    
     <?php $REOutros = mysql_query("SELECT DISTINCT(nome) AS nome FROM fr_combustivel WHERE funcionario = '2'");     while($RowOutros = mysql_fetch_array($REOutros)) {
		   	   $arNomes1[] = $RowOutros['nome'];
		   }
	
		   $REFunc = mysql_query("SELECT DISTINCT(id_user) AS nome FROM fr_combustivel WHERE funcionario = '1'");
		   while($RowFunc = mysql_fetch_array($REFunc)){
			   $Nfun      -> MostraUser($RowFunc['nome']);
			   $arNomes2[] = $Nfun -> nome1;
			   $Ids[]      = $RowFunc['nome'];
		   }
		   
		   foreach($arNomes1 as $chave => $valor) {
			   $RE1  = mysql_query("SELECT SUM(valor) AS valor FROM fr_combustivel WHERE funcionario = '2' AND nome = '$valor' AND MONTH(data) = '$mes'");
			   $Row1 = mysql_fetch_array($RE1);
			   
			   $REConsulta  = mysql_query("SELECT * FROM fr_combustivel_to WHERE nome = '$valor' AND mes = '$mes'");
			   $RowConsulta = mysql_fetch_array($REConsulta);
			   $NumCon      = mysql_num_rows($REConsulta);
			   
			   if(!empty($NumCon)) {
				   mysql_query("UPDATE fr_combustivel_to SET total = '$Row1[valor]' WHERE id = '$RowConsulta[0]'");
			   } else {
				   mysql_query("INSERT INTO fr_combustivel_to(mes,nome,total) VALUES ('$mes','$valor','$Row1[valor]')");
			   }
		   }

		   foreach($arNomes2 as $chave => $valor) {
			   $RE2  = mysql_query("SELECT SUM(valor) AS valor FROM fr_combustivel WHERE funcionario = '1' AND id_user = '$Ids[$chave]' AND MONTH(data) = '$mes'");
			   $Row2 = mysql_fetch_array($RE2);
			
			   $REConsulta  = mysql_query("SELECT * FROM fr_combustivel_to WHERE nome = '$valor' AND mes = '$mes'");
			   $RowConsulta = mysql_fetch_array($REConsulta);
			   $NumCon      = mysql_num_rows($REConsulta);
			
			   if(!empty($NumCon)) {
				   mysql_query("UPDATE fr_combustivel_to SET total = '$Row2[valor]' WHERE id = '$RowConsulta[0]'");
			   } else {
				   mysql_query("INSERT INTO fr_combustivel_to(mes,nome,total) VALUES ('$mes','$valor','$Row2[valor]')");
			   }
		   }


		  $consulta = mysql_query ("SELECT DISTINCT(nome) FROM fr_combustivel WHERE nome != '' AND funcionario = '2'");
		  while($consulta_nome = mysql_fetch_array($consulta)) {
			
			  $REtabela = mysql_query("SELECT SUM(valor) AS total FROM fr_combustivel WHERE MONTH(data_libe) = '$mes' AND YEAR(data_libe) = '$ano' AND nome = '$consulta_nome[nome]' AND funcionario = '2'");		
			  $consulta_2 = mysql_fetch_array($REtabela);
					
			  $nomes[]   = $consulta_nome['nome'];
			  $valores[] = $consulta_2['total'];
					
		  }
		
		  $consulta = mysql_query ("SELECT DISTINCT(id_user) FROM fr_combustivel WHERE funcionario = '1'");
		  while($consulta_nome = mysql_fetch_array($consulta)) {
			
			  $REtabela = mysql_query("SELECT SUM(valor) AS total FROM fr_combustivel WHERE MONTH(data_libe) = '$mes' AND YEAR(data_libe) = '$ano' AND id_user = '$consulta_nome[id_user]' AND funcionario = '1'");		
			  $consulta_2 = mysql_fetch_array($REtabela);
			
			  $qr_nome = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$consulta_nome[id_user]'");
			
			  $nomes[]   = @mysql_result($qr_nome,0);
			  $valores[] = $consulta_2['total'];
			
		  } 
		
		  array_multisort($valores,SORT_DESC,$nomes);
		
		  foreach($valores as $chave => $valor) {
			
			  $valor = number_format($valor,2,',','.');
						
			  if($valor != '0,00') { ?>
		
			<tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">            
                <td colspan="2"><?=$nomes[$chave]?></td>
                <td><?=$valor?></td>
			</tr>         
			
		<?php } } if(array_sum($valores) != 0) { ?>

  <tr>
    <td><a href="#corpo" class="ancora">Subir ao topo</a></td>
    <td align="right"><b>Total de Abastecimentos:</b></td>
    <td align="center"><b><?=number_format(array_sum($valores),2,',','.')?></b></td>
  </tr>
  <?php } ?>
</table>
<?php } ?>
</div>
</body>
</html>