<?php
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../login.php">Logar</a>';
	exit;
}

include('../conn.php');

$regiao  = $_REQUEST['regiao'];
$id_user = $_COOKIE['logado'];
$mes     = date('m');

// Primeira fase do script, Selecionando as informaçãoes do select pricipal
$result_select = mysql_query("SELECT distinct(descicao), cod FROM rh_movimentos WHERE valor = 'imposto' AND incidencia = 'folha'");

// Segunda fase do script, Recebendo as variáveis que foram retiradas do banco de dados e atualizadas
$cod        = $_REQUEST['cod'];
$id_mov     = $_REQUEST['id_mov'];
$faixa      = $_REQUEST['faixa'];
$v_ini      = $_REQUEST['v_ini'];
$v_fim      = $_REQUEST['v_fim'];
$percentual = $_REQUEST['percentual'];
$fixo       = $_REQUEST['fixo'];
$valor      = $_REQUEST['valor'];
$descicao   = $_REQUEST['descicao'];
$categoria  = $_REQUEST['categoria'];
$incidencia = $_REQUEST['incidencia'];
$ano_base   = $_REQUEST['ano_base'];

// Variável que autoriza a atualização no banco de dados.
$status = $_REQUEST['gravar'];
$opcao  = $_REQUEST['opcao'];

if($status == 'gravar') {
	$status = NULL;
	mysql_query("UPDATE rh_movimentos SET descicao = '$descicao', valor = '$valor', categoria = '$categoria', incidencia = '$incidencia', faixa = '$faixa', v_ini = '$v_ini', v_fim = '$v_fim', percentual = '$percentual', fixo = '$fixo', user_alter = '$id_user', anobase = '$ano_base', ultima_alter = CURDATE() WHERE id_mov = '$id_mov'");
	echo "<script>alert('Alteração realizada com sucesso!');</script>";	
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>:: Intranet :: Impostos</title>
<link rel="shortcut icon" href="../favicon.ico">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="javascript" type="text/javascript" src="../js/ramon.js"></script>
<script>
function alterar(f) {
	document.getElementById(f).style.display = '';
}
</script>
<style>
body {
	background-color:#FAFAFA; text-align:center; margin:0px;
}
p {
	margin:0px;
}
#corpo {
	width:90%; background-color:#FFF; margin:0px auto; text-align:left; padding-top:20px; padding-bottom:10px;
}
table.secao {
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif; margin:20px auto; width:95%; color:#111; font-size:11px;
}
tr.secao_pai {
	font-weight:bold; background-color:#ECF2EC;
}
tr.secao_um {
	background-color:#f1f1f1;
}
tr.secao_dois {
	background-color:#efefef;
}
form, input, textarea {
	padding:0px; margin:0px;
}
input, textarea {
	font-size:10px;
}
</style>
</head>
<body>
<div id="corpo">

<div id="topo" style="width:95%; margin:0px auto; font-family:Arial;">
	<div style="float:left; width:25%;">
 		<a href="../principalrh.php?regiao=<?=$regiao?>&id=1">
       		<img src="../imagens/voltar.gif" border="0">
        </a>
    </div>
	<div style="float:left; width:50%; text-align:center; font-size:24px; font-weight:bold; color:#000;">
    	IMPOSTOS
    </div>
	<div style="float:right; width:25%; text-align:right; font-size:12px; color:#333;">
    	<br><b>Data:</b> <?=date('d/m/Y')?>&nbsp;&nbsp;&nbsp;


 <?php include('../reportar_erro.php');?>
      
        
        
    </div>
    
	<div style="clear:both;"></div>
</div>

<?php $result = mysql_query("SELECT * FROM rh_movimentos WHERE cod = '$opcao'"); ?>
      <table class="secao" cellspacing="1" cellpadding="4" align="center">
        <tr>
          <td colspan="11">
           <select name="tipo" class="campotexto" onChange="location.href=this.value;">
                <option>SELECIONE</option>
                <?php while($row_opcao = mysql_fetch_array($result_select)) { ?>
                    <option value="rh_impostos.php?opcao=<?=$row_opcao['cod']?>&regiao=<?=$regiao?>"
                    		<?php if($opcao == $row_opcao['cod']) { 
									echo 'selected'; 
								  } ?>>
                        <?=$row_opcao['cod']?> - <?=$row_opcao['descicao']?>
                    </option>
                <?php } ?>
           </select>
          </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <?php if(!empty($opcao)) { ?>
		<tr class="secao_pai">
			<td style="width:4%">Faixa</td>
			<td style="width:12%">Valor Inicial (R$)</td>
			<td style="width:11%">Valor Final (R$)</td>
		    <td style="width:11%">Percentual (%)</td>
		    <td style="width:9%">Fixo (R$)</td>
		    <td style="width:9%">Valor</td>
		    <td style="width:19%">Descrição</td>
		    <td style="width:10%">Categoria</td>
		    <td style="width:6%">Incidência</td>
			<td style="width:8%">Ano Base</td>
            <td style="width:1%">&nbsp;</td>
		</tr>
	
	<?php $cont = NULL;
		  while($row = mysql_fetch_array($result)) {
		  $cont++; ?>
    
      <tr class="<?php if($alternateColor++%2==0) { echo "secao_um"; } else { echo "secao_dois"; } ?>">
		<td style="width:4%"><?=$row['faixa']?></td>
		<td style="width:12%"><?php echo number_format($row['v_ini'], '2', ',', '.'); ?></td>
		<td style="width:11%"><?php echo number_format($row['v_fim'], '2', ',', '.'); ?></td>
		<td style="width:11%"><?=$row['percentual']?></td>
		<td style="width:9%"><?php echo number_format($row['fixo'], '2', ',', '.'); ?></td>
		<td style="width:9%"><?=$row['valor']?></td>
		<td style="width:19%"><?=$row['descicao']?></td>
		<td style="width:10%"><?=$row['categoria']?></td>
		<td style="width:6%"><?=$row['incidencia']?></td>
		<td style="width:8%"><?=$row['anobase']?></td>
		<td style="width:1%">
        	<a href="#" id="editar" onClick="document.all.linha<?=$cont?>.style.display = (document.all.linha<?=$cont?>.style.display == 'none') ? '' : 'none' ; " title="Editar"><img src="../imagens/editar.gif" width="16" height="16" border="0" alt="Editar"></a>
        </td>
      </tr>
      <tr style="display:none; background-color:#ccc;" id="linha<?=$cont?>">
       <form action="<?php echo $_SERVER['PHP_SELF'].'?opcao='.$opcao.'&regiao='.$regiao; ?>" method="post">
    	<td style="width:4%">
        	<input name="faixa" id="faixa" type="text" value="<?=$row['faixa']?>" size="2">
        </td>
		<td style="width:12%"><input name="v_ini" id="v_ini" type="text" value="<?=$row['v_ini']?>" size="11"></td>
		<td style="width:11%">
        	<input name="v_fim" id="v_fim" type="text" value="<?=$row['v_fim']?>" size="11">
        </td>
		<td style="width:11%">
        	<input name="percentual" id="percentual" type="text" value="<?=$row['percentual']?>" size="6">
        </td>
		<td style="width:9%">
        	<input name="fixo" id="fixo" type="text" value="<?=$row['fixo']?>" size="11">
        </td>
		<td style="width:9%">
        	<input name="valor" id="valor" type="text" value="<?=$row['valor']?>" size="11">
        </td>
		<td style="width:19%">
        	<textarea name="descicao" id="descicao" cols="20" rows="0"><?=$row['descicao']?></textarea>
        </td>
		<td style="width:10%">
        	<input name="categoria" id="categoria" type="text" value="<?=$row['categoria']?>" size="11">
        </td>
		<td style="width:6%">
        	<input name="incidencia" id="incidencia" type="text" value="<?=$row['incidencia']?>" size="8">
        </td>
		<td style="width:8%">
        	<input name="ano_base" id="ano_base" type="text" value="<?=$row['anobase']?>" size="5">
        </td>
        <td style="width:1%">
        	<input type="image" src="../imagens/salvar.gif" alt="Atualizar" title="Atualizar" width="16" height="16">
        </td>
       <input type="hidden" name="id_mov" id="id_mov" value="<?=$row['id_mov']?>">
       <input type="hidden" name="gravar" value="gravar">
    </form>
      </tr>
	<?php } } ?>
</table>
</div>
</div>
</body>
</html>