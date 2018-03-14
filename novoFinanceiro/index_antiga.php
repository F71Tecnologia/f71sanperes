<?php
include ("include/restricoes.php");
include "../conn.php";
include "../funcoes.php";

$id_user = $_COOKIE['logado2'];
if(empty($id_user)){
	exit;
}
//$regiao = $_GET['regiao'];

// RECEBENDO A VARIAVEL CRIPTOGRAFADA
list($regiao) = explode('&', decrypt(str_replace('--','+',$_REQUEST['enc'])));

$qr_regiao = mysql_query("SELECT id_regiao,regiao FROM regioes WHERE id_regiao = '$regiao'");
$rw_regiao = mysql_fetch_array($qr_regiao);
$query_funcionario = mysql_query("SELECT id_funcionario, nome, tipo_usuario FROM funcionario WHERE id_funcionario = '$id_user'");
$row_funcionario = mysql_fetch_array($query_funcionario);
$tipo_user  = $row_funcionario['tipo_usuario'];
$query_master = mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$regiao'");
$id_master = @mysql_result($query_master,0);

/*Controle de combustivel*/
if(!empty($_REQUEST['apro'])){
	$apro = $_REQUEST['apro'];
	$vale = $_REQUEST['vale'];
	$valor = $_REQUEST['valor'];
	$regiao = $_REQUEST['regiao'];
	$idComb = $_REQUEST['idcomb'];
	$dataCad = date('Y-m-d');
	if($apro == 1){		
		mysql_query("UPDATE fr_combustivel SET status_reg = '2', data_libe = '$dataCad', numero='$vale', user_libe = '$id_user' WHERE 
		id_combustivel = '$idComb'");
		$link = "../frota/printcombustivel.php?com=$idComb&regiao=$regiao";
	}else{
		mysql_query("UPDATE fr_combustivel SET status_reg = '0', data_libe = '$dataCad', user_libe = '$id_user' WHERE id_combustivel = '$idComb'");
		$link = "index.php?regiao=$regiao";
	}
	print "<script>
	location.href=\"$link\";
	</script>";
	exit;
}

/*FIM do CONT|ROLE de COMBUSTIVEL*/



function format_date($data){
	return implode('/',array_reverse(explode('-',$data)));
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<title>Financeiro - Beta</title>

<script type="text/javascript" src="../js/highslide-with-html.js"></script>
<link rel="stylesheet" type="text/css" href="../js/highslide.css" />
<script type="text/javascript">
    hs.graphicsDir = '../images-box/graphics/';
    hs.outlineType = 'rounded-white';
</script>


<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript" src="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js" ></script>
<link rel="stylesheet" type="text/css" href="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" />


<script type="text/javascript">
function confirmacao(url,mensagem){
	if(window.confirm(mensagem)){
		location.href = url;
	}
}
function abrir(URL,w,h,NOMEZINHO) {
	var width = w;
  	var height = h;
	var left = 99;
	var top = 99;
window.open(URL,NOMEZINHO, 'width='+width+', height='+height+', top='+top+', left='+left+', scrollbars=yes, status=no, toolbar=no, location=no, directories=no, menubar=no, resizable=yes, fullscreen=no');
}
$(function(){
	/*Ajusta a tela de acordo com a resolução*/
	var w = screen.width - 40;
	$('#topo').width(w);
	$('#topo').css('left','50%');
	$('#topo').css('margin-left','-'+((w/2)+6)+'px');
	$('#base').width(w);
		
	
	var iten_banco = $('.bancos');
	var iten_loading = $('.loading');
	
	iten_banco.click(function(){
		var iten_lista = $(this).next();
		
		iten_lista.slideToggle('fast');
		
	});
	
	var checkbox = $('.saidas_check');
	var linha_checkbox = $('.saidas_check').parent().parent();
	
	linha_checkbox.click(function(){
		$(this).find('.saidas_check').attr('checked',!$(this).find('.saidas_check').attr('checked'));
		if($(this).find('.saidas_check').attr('checked')){
			$(this).addClass('linha_selectd');
		}else{
			$(this).removeClass('linha_selectd');
		}
	});
	
	checkbox.change(function(){
		$(this).attr('checked',!$(this).attr('checked'));
		if($(this).attr('checked')){
			$(this).parent().parent().addClass('linha_selectd');
		}else{
			$(this).parent().parent().removeClass('linha_selectd');
		}
	});
	
	$('#Pagar_all').click(function(){
		/*var ids = new Array;
		$('.saidas_check:checked').each(function(){
			ids.push($(this).val());
		});*/
		var msg = 'Você tem certeza que deseja PAGAR as saidas:\n';
		$('.saidas_check:checked').each(function(){
			var id = $(this).parent().next().next().text();
			var nome = $(this).parent().next().next().next().find('span').text();
			var valor = $(this).parent().next().next().next().next().next().text();
			msg += '\n'+id+' - '+nome+' '+ valor;
			
		});
		if(window.confirm(msg)){
			var ids = $('#form').serialize();
			$.post('actions/pagar.selecao_old.php',ids,function(retorno){ window.location.reload();});
		}
	});
	
	
	$('#Deletar_all').click(function(){
		/*var ids = new Array;
		$('.saidas_check:checked').each(function(){
			ids.push($(this).val());
		});*/
		var msg = 'Você tem certeza que deseja DELETAR as saidas:\n';
		$('.saidas_check:checked').each(function(){
			var id = $(this).parent().next().next().text();
			var nome = $(this).parent().next().next().next().find('span').text();
			var valor = $(this).parent().next().next().next().next().next().text();
			msg += '\n'+id+' - '+nome+' '+ valor;
			
		});
		if(window.confirm(msg)){
			var ids = $('#form').serialize();
			$.post('actions/apaga.selecao_old.php',ids,function(retorno){ window.location.reload();});
		}
	});
	
	
	$('.date').datepicker({
					dateFormat: 'dd/mm/yy',
					changeMonth: true,
					changeYear: true
	});
	
	/*linha_selectd*/

	/*$('.bancos a').click(function(){
		
		$('.loading').clone(true).prependTo($(this).parent().next('.lista'));
		
		$('.bancos a').not(this).parent().next('.lista').slideUp('fast');
		$(this).parent().next('.lista').slideToggle('fast');
		$(this).parent().next('.lista').load($(this).attr('href'));
	});*/
});
</script>
<link rel="stylesheet" type="text/css" href="style/form.css" />
<link href="style/estilo_financeiro.css" rel="stylesheet" type="text/css" />
<style type="text/css">
span.nome {	color:#F00000;
}
</style>
<script type="text/javascript">
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
</script>
</head>
<body>
<!-- TOPO -->
<div id="topo">
    


  <div id="dentro_topo">
    <table width="100%">
     
      <tr>
        <td width="11%" height="81" rowspan="3" align="center"><img src="../imagens/logomaster<?=$id_master?>.gif" width="110" height="79" /></td>
        <td width="36%" rowspan="3" align="left" valign="top"><br />
          <span>Financeiro</span><br />
          <span class="nome"><?php echo $row_funcionario[1] ?></span><br />
          14/10/2010<br />
          Regiao: <?php echo $rw_regiao[1]; ?></td>
        <td width="53%" align="right">
        </td>
      </tr>
      <tr>
        <td align="right">
        	<form name="formRegiao" id="formRegiao" method="get">
        <table>
        <tr>
            <td><span style="color:#000">Região</span></td>
            <td>
            
           
           
           <?php // Visualizando Regiões
                      if($tipo_user == '1' or $tipo_user == '4') : ?>
                    <span id="labregiao1">
                    <select name="regiao" class="campotexto" id="regiao" onchange="MM_jumpMenu('parent',this,0)">
                        <option value="">- Selecione -</option>
                        <optgroup label="Regiões em Funcionamento">
                
                <?php
                // Acesso a Administração
                $ids_administracao = array('5','9','27','28','33','64','71','77','24','82', '24','22','89');
                $ids_sistema = array('9','68','75','87','89');
                
                if(in_array($id_user,$ids_administracao)) {
                    $acesso_administracao = true;
                }
                if(in_array($id_user,$ids_sistema)) {
                    $acesso_sistema = true;
                }
                //
                
                    $qr_regioes_ativas = mysql_query("SELECT * FROM regioes WHERE status = '1'");
                    while($row_regiao = mysql_fetch_array($qr_regioes_ativas)) {
                        
                        if($regiao == $row_regiao['id_regiao']) {
                            $selected = 'selected';
                        } else {
                            $selected = NULL;
                        }
                        
                        if(($row_regiao['id_regiao'] == '15' and isset($acesso_administracao)) or
                           ($row_regiao['id_regiao'] != '15')) {
                        
                        if(($row_regiao['id_regiao'] == '36' and isset($acesso_sistema)) or 
                           ($row_regiao['id_regiao'] != '36')) { 
						   
						   	$linkEnc = encrypt($row_regiao['id_regiao']); 
							$linkEnc = str_replace("+","--",$linkEnc);
						   ?>
                				
                                <option value="<?="?enc=$linkEnc";?>" <?=$selected?>><?=$row_regiao['id_regiao'].' - '.$row_regiao['regiao']?></option>
                        
                    <?php } } } ?>
                    
                </optgroup>
                <optgroup label="Regiões Desativadas">
                
                <?php // Acesso a Regiões Desativadas
                $ids_desativadas = array('1','5','9','27','57','64','68','51','77','75','87','24','71','22','89');
                
                if(in_array($id_user,$ids_desativadas)) {
                    
                    $qr_desativadas = mysql_query("SELECT * FROM regioes WHERE status = '0'");
                    while($row_regiao = mysql_fetch_array($qr_desativadas)) {
                        
                        if($regiao_usuario == $row_regiao['id_regiao']) {
                            $selected = 'selected';
                        } else {
                            $selected = NULL;
                        } 
						
							$linkEnc = encrypt($row_regiao['id_regiao']); 
							$linkEnc = str_replace("+","--",$linkEnc);
						?>
                        
                        <option value="<?="?enc=$linkEnc";?>" <?=$selected?>><?=$row_regiao['id_regiao'].' - '.$row_regiao['regiao']?></option>
                        
                <?php } } ?>
                
                </optgroup>
                </select>
                </span>
                <?php endif; // Fim de Regiões?>
            </td>
        </tr>
        </table>
        </form>
        </td>
      </tr>
      <tr>
        <td align="right"><table>
          <tr>
            <td align="center"></td>
            <td align="center"></td>
          </tr>
        </table></td>
      </tr>
       <tr>
        <td height="31" colspan="3">
        <ul id="menu">
        	<li><a href="javascript:abrir('cadastrarsaida.php?regiao=<?=$regiao?>','750','550','Saída');">Cadastrar Saídas</a></li>
            <li><a href="javascript:abrir('../financeiro/entradas.php?regiao=<?=$regiao?>','750','550','Entrada');">Cadastrar Entradas</a></li>
            <li><a href="javascript:abrir('../financeiro/login_adm2.php?regiao=<?=$regiao;?>','600','400','Rel');">Relatórios</a></li>
            <li><a href="javascript:abrir('../financeiro/saidacaixinha.php?regiao=<?=$regiao?>','680','280','Caixa');">Saídas de Caixa</a></li>
            <!--<li><a href="javascript:alert('Em breve.')">Remesa</a></li>-->
            <li><a id="Pagar_all" href="#" onclick="return false">Confirmar&nbsp;<img src="../financeiro/imagensfinanceiro/Money-32.png" alt="Editar" border="0" align="absmiddle" /></a></li>
            <li><a id="Deletar_all" href="#" onclick="return false">Deletar&nbsp;<img src="../financeiro/imagensfinanceiro/Delete-32.png" alt="Deletar" border="0" align="absmiddle" /></a></li>
        </ul>
        </td>
      </tr>
    </table>
  </div>
</div>
<!-- FIM TOPO -->


<?php
//BLOQUEIO PAULO MONTEIRO SJR 16-03 - 17hs
// or $userlog == '27'  or $userlog == '1'
if($id_user != '73'):
?>
<div id="base">



<!-- TOTALIZADOR -->
<div class="Totalizador">
 <!-- INICIO DO CONTROLE DE COMBUSTIVEL -->
      <?php
  		//SOMENTE PODEM VER CONTROLE DE COMBUSTIVEL
		// or $id_user == '27'  or $id_user == '1'
  		if($id_user == '27' or $id_user == '52' or $id_user == '5' or $id_user == '1' or $id_user == '65' or $id_user == '9' or $id_user == '64' or $id_user == '77' or $id_user == '75' or $id_user == '85' or $id_user == '87'){?>
        <div id="apDiv2" >
        <fieldset>
        	<legend> &nbsp;&nbsp;CONTROLE DE COMBUST&Iacute;VEL:</legend>
        
      	 
          <span id="FimComb"></span>
          <?php
	echo "<table width='100%' border='0' cellspacing='1' cellpadding='0' bgcolor='#CCCCCC' id='TabelaCombustivel'>";
	$REComb = mysql_query("SELECT *,date_format(data_cad, '%d/%m/%Y')as data_cad FROM fr_combustivel where status_reg='1'");
	$cont = "0";
	while($RowComb = mysql_fetch_array($REComb)){
		if($cont % 2){ $color="#FFFFFF"; }else{ $color="#EEEEEE"; }
		if($RowComb['funcionario'] == 2){ //FUNCIONARIO EXTERNO ( N&Atilde;O ESTA CADASTRADO NA TABELA FUNCIONARIOS )
			$REFuncionario = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = '$RowComb[id_user]'");
			$RowFuncionario = mysql_fetch_array($REFuncionario);
			$NOME = $RowComb['nome'];
			$RG = $RowComb['rg'];
		}else{//FUNCIONARIO INTERNO ( SELECIONAMOS O NOME E O CPF DELE CADASTRADO NA BASE DE DADOS )
			$REUser = mysql_query("SELECT nome,rg FROM funcionario where id_funcionario = '$RowComb[id_user]'");
			$RowUser = mysql_fetch_array($REUser);
			$NOME = $RowUser['nome'];
			$RG = $RowUser['rg'];
		}
		$REREG = mysql_query("SELECT regiao FROM regioes where id_regiao = '$RowComb[id_regiao]'");
		$RowREG = mysql_fetch_array($REREG);
		$NOME = explode(" ",$NOME);
		$codigo = sprintf("%04d",$RowComb['0']);
	print "<tr class='linhaspeq' bgcolor=$color>
	<td align='center' >$NOME[0]</td>
	<td align='center' >$RowREG[regiao]</td>
	<td align='center' >$RowComb[destino]</td>
	<td align='center' >$RowComb[data_cad]</td>
	<td align='center' >
	<a href='#' 
	onclick=\"return hs.htmlExpand(this, { outlineType: 'rounded-white', wrapperClassName: 'draggable-header',headingText: 'Liberar' } )\" 
	class='highslide'> Liberar </a>
	<div class='highslide-maincontent'>
	<form action='' method='post' name='form'>
	<table width='526' border='0' cellspacing='1' cellpadding='0' bgcolor='#CCCCCC'>
		<tr>
			<td align='center' colspan='2' bgcolor='#FFFFFF'>
			<label><input type='radio' name='apro' id='apro' value='1'>&nbsp;Aprovar</label> &nbsp;&nbsp;
			<label><input type='radio' name='apro' id='apro' value='2'>&nbsp;Recusar</label>
			</td>
		</tr>
		<tr>
			<th align='right'>N&uacute;mero do Vale:</th>
			<td>&nbsp;<input name='vale' type='text' size='20' id='vale' value='$codigo'/>&nbsp;</td>
		</tr>
		<tr>
			<th align='right'>Valor do Vale:</th>
			<td>&nbsp;<input name='valor' type='text' size='13' id='valor' OnKeyDown=\"FormataValor(this,event,17,2)\"/>&nbsp;</td>
		</tr>
		<tr>
			<td align='center' colspan='2' bgcolor='#FFFFFF'><input type='submit' value='Enviar' /></td>
		</tr>
	</table>
	<input type='hidden' id='regiao' name='regiao' value='$regiao'/>
	<input type='hidden' id='idcomb' name='idcomb' value='$RowComb[0]'/>
	</form>
	</div>
	</td>
	</tr>";
	$cont ++;
	}
	echo "</table>";
    ?>
    	</fieldset>
        </div>
		<?php
  		}
  		?>
<!-- FINALIZANDO A DIV DO CONTROLE DE COMBUSTIVEL -->  
<?php
$users = array('9','27','5','64','77','75','24','71','77'); // filtro de usuarios
if(in_array($id_user,$users)):?>
<fieldset>
	<legend>&nbsp;&nbsp; RESUMO DE CONTAS :</legend>
          
	<table width='100%' border='0' cellspacing='1' cellpadding='3'  id='TabelaCombustivel'>
	<tr>
	<td><strong>Regi&atilde;o</strong></td>
    <td align="center"><strong>Proximas</strong></td>
	<td align="center"><strong>Hoje</strong></td>
	<td align="center"><strong>Vencidas</strong></td>
	<td >&nbsp;</td>
	</tr>
    <?php 
	$qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '1' AND (id_master = '1' OR id_master = '4')");
	while($row_regioes = mysql_fetch_assoc($qr_regioes)):
		$qr_cont_hoje = mysql_query("SELECT * 
								FROM saida
								WHERE id_regiao =  '$row_regioes[id_regiao]'
								AND STATUS =  '1'
								AND data_vencimento =  CURDATE()");
		$qr_cont_vencidas = mysql_query("SELECT * 
								FROM saida
								WHERE id_regiao =  '$row_regioes[id_regiao]'
								AND STATUS =  '1'
								AND data_vencimento < CURDATE()
								AND data_vencimento != '0000-00-00'
								AND YEAR(data_vencimento) = '".date('Y')."'
								");
		$qr_cont_avencer = mysql_query("SELECT * 
								FROM saida
								WHERE id_regiao =  '$row_regioes[id_regiao]'
								AND STATUS =  '1'
								AND data_vencimento > CURDATE()
							");
		$num_hoje = mysql_num_rows($qr_cont_hoje);
		$num_vencimento = mysql_num_rows($qr_cont_vencidas);
		$num_avencer = mysql_num_rows($qr_cont_avencer);
	if(!empty($num_hoje) or !empty($num_vencimento) or !empty($num_avencer)):
?>
    <tr  class="linha_<?php if($linha++%2==0) { echo 'dois'; } else { echo 'um'; } ?>">
    	<td class="linhaspeq"><?=$row_regioes['id_regiao'].' - '.$row_regioes['regiao']?></td>
        <td align="center" class="linhaspeq"><?=$num_avencer?></td>
        <td align="center" class="linhaspeq"><?=$num_hoje?></td>
        <td align="center" class="linhaspeq"><?=$num_vencimento?></td>
        <td align="center" class="linhaspeq"><a href="?regiao=<?=$row_regioes['id_regiao']?>">ver</a></td>
    </tr>    
    <?php endif;?>
    <?php endwhile;?>
	</table>
</fieldset>


<?php endif;?>
</div>

<!-- TOTALIZADOR -->  

<!--CONTROLE DE REEMBOLSO -->
<div class="reembolso" >
<fieldset>
<legend>&nbsp;&nbsp;CONTROLE DE REEMBOLSO:</legend>
<table width='100%' border='0'>
        

<?php
	$REReem = mysql_query("SELECT *,date_format(data, '%d/%m/%Y %H:%i:%s')as data FROM fr_reembolso WHERE status = '1'");
	$cont = '0';
	while($RowReem = mysql_fetch_array($REReem)):
	  if($cont % 2){ $color='#FFFFFF'; }else{ $color='#EEEEEE'; }
	  if($RowReem['funcionario'] == '1'){
	  	$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$RowReem[id_user]'");
	  	$row_user = mysql_fetch_array($result_user);
	  	$NOME = $row_user['nome1'];  
	 }else{
	  	$NOME = $RowReem['nome']; 
	  }
	  $pagar_imagem = '-';	  
	  $codigo = sprintf('%05d',$RowReem['0']);
	  $valor = $RowReem['valor'];	  
	  $valorF = number_format($valor,2,",",".");
	  $link = "<a href='../frota/ver_reembolso.php?id=1&reembolso=$RowReem[0]' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" title=\"Confirmar reembolso\">";
?>
            <tr class="linha_<?php if($linha++%2==0) { echo 'dois'; } else { echo 'um'; } ?>">
            <td width='5%' align='center' class="linhaspeq"><?=$codigo?></td>
            <td width='36%' class="linhaspeq"align='center'><?=$RowReem['data']?></td>
            <td width='39%' class="linhaspeq"align='left'><?=$NOME?></td>
            <td width="11%" class="linhaspeq" ><b>R$ <?=$valorF?></b></td>
            <td width="9%" align='center' class="linhaspeq" ><?=$link?><img src='../financeiro/imagensfinanceiro/checked.png' alt='Editar' width="16" height="16" border=0> </a></td>
            </tr>
<?php    
		$soma = $soma + $valor;
		$cont ++;
	endwhile;
    $soma_f = number_format($soma,2,",",".");
?>
	<tr>
    <td colspan='3' align="right">
    	<b>TOTAL DE REEMBOLSO: </b>
    </td>
    <td>
   <b>
		R$  <?=$soma_f?>
    </b></td>
    <td colspan='3'>
    </td>
    </tr>
    <?php   
	unset($soma_f,$cont,$soma,$valor);
	?> 
   </table>
</fieldset>
</div>
<!-- FIM CONTROLE DE REEMBOLSO -->
<div class="clear"></div>
<!-- PRESTADOR DE SERVIÇO -->
<?php 
	
		
		include "Prestador/view.php";
	
?>
<!-- FIM PRESTADOR DE SERVIÇO -->
<div class="clear"></div>
<form method="post" onsubmit="return false" action="" id="form" name="forma"  >
<fieldset>
<legend><img src="../financeiro/imagensfinanceiro/saida-32.png" align="absmiddle"  /><img src="../financeiro/imagensfinanceiro/entradas-up-32.png" align="absmiddle" />&nbsp;RELA&Ccedil;&Atilde;O DE ENTRADAS E SA&Iacute;DAS CADASTRADAS POR DATA: </legend>
<?php 
$qr_bancos = mysql_query("SELECT *,bancos.nome AS nome_banco FROM bancos INNER JOIN projeto ON projeto.id_projeto = bancos.id_projeto WHERE bancos.id_regiao = '$regiao' AND bancos.status_reg = '1' AND bancos.interno = '1' AND projeto.status_reg = '1'");	
$row_bancos = mysql_fetch_assoc($qr_bancos);
?>
<?php do{ ?>
<?php 
/*ATRIBUIÇÕES E CONTADORES*/
$id_banco = $row_bancos['id_banco'];
$qr_saidas_hoje = mysql_query("SELECT * FROM saida
								WHERE id_regiao =  '$regiao'
								AND STATUS =  '1'
								AND data_vencimento =  CURDATE()
								AND id_banco = '$id_banco'
								");
$qr_saidas_venciada = mysql_query("SELECT * FROM saida
								WHERE id_regiao =  '$regiao'
								AND STATUS =  '1'
								AND data_vencimento < CURDATE()
								AND data_vencimento != '0000-00-00'
								AND (YEAR(data_vencimento) = '".date('Y')."' OR YEAR(data_vencimento) = '".(date('Y')-1)."')
								AND id_banco = '$id_banco'
								ORDER BY data_vencimento ASC
								");
$qr_saidas_futuras = mysql_query("SELECT * FROM saida
								WHERE id_regiao =  '$regiao'
								AND STATUS =  '1'
								AND data_vencimento > CURDATE()
								AND id_banco = '$id_banco'
								ORDER BY data_vencimento ASC");
$qr_entradas = mysql_query("SELECT * FROM entrada WHERE id_regiao = '$regiao' AND id_banco = '$id_banco' AND status = '1' ORDER BY data_vencimento");

$num_saidas_hoje = mysql_num_rows($qr_saidas_hoje);
$num_saidas_vencidas = mysql_num_rows($qr_saidas_venciada);
$num_saidas_futuras = mysql_num_rows($qr_saidas_futuras);
$num_entradas = mysql_num_rows($qr_entradas);
$Array_saidas = array();
while($row_saida_hoje = mysql_fetch_assoc($qr_saidas_hoje)):
	$saida_valor  = (float) str_replace(',','.',$row_saida_hoje['valor']);
	$saida_adicional  = (float) str_replace(',','.',$row_saida_hoje['adicional']);
	$Total = $saida_valor + $saida_adicional;
	$row_saida_hoje['TOTAL'] = $Total;
	$totalizador += $Total;
	$Array_saidas[1][] = $row_saida_hoje;
endwhile;
$Array_saidas[1]['TOTALIZADOR'] = $totalizador;
unset($totalizador);
while($row_saida_venciada = mysql_fetch_assoc($qr_saidas_venciada)):
	$saida_valor  = (float) str_replace(',','.',$row_saida_venciada['valor']);
	$saida_adicional  = (float) str_replace(',','.',$row_saida_venciada['adicional']);
	$Total = $saida_valor + $saida_adicional;
	$row_saida_venciada['TOTAL'] = $Total;
	$totalizador += $Total;
	$Array_saidas[2][] = $row_saida_venciada;
endwhile;
$Array_saidas[2]['TOTALIZADOR'] = $totalizador;
unset($totalizador);
while($row_saida_futuras = mysql_fetch_assoc($qr_saidas_futuras)):
	$saida_valor  = (float) str_replace(',','.',$row_saida_futuras['valor']);
	$saida_adicional  = (float) str_replace(',','.',$row_saida_futuras['adicional']);
	$Total = $saida_valor + $saida_adicional;
	$row_saida_futuras['TOTAL'] = $Total;
	$totalizador += $Total;
	$Array_saidas[3][] = $row_saida_futuras;
endwhile;
$Array_saidas[3]['TOTALIZADOR'] = $totalizador;
unset($totalizador);
while($row_entradas = mysql_fetch_assoc($qr_entradas)):
	$saida_valor  = (float) str_replace(',','.',$row_entradas['valor']);
	$saida_adicional  = (float) str_replace(',','.',$row_entradas['adicional']);
	$Total = $saida_valor + $saida_adicional;
	$row_entradas['TOTAL'] = $Total;
	$totalizador += $Total;
	$Array_saidas[4][] = $row_entradas;
endwhile;
$Array_saidas[4]['TOTALIZADOR'] = $totalizador;
unset($totalizador);
$totalizador_geral_entrada = $Array_saidas[4]['TOTALIZADOR'];
$totalizador_geral = $Array_saidas[1]['TOTALIZADOR'] + $Array_saidas[2]['TOTALIZADOR'] + $Array_saidas[3]['TOTALIZADOR'];
unset($Array_saidas[1]['TOTALIZADOR'],$Array_saidas[2]['TOTALIZADOR'],$Array_saidas[3]['TOTALIZADOR'],$Array_saidas[4]['TOTALIZADOR']);
?>
	<div class="blocos">
    <div class="bancos" href="view/lista-saidas.php?banco=<?=$row_bancos['id_banco']?>&regiao=<?=$_GET['regiao'];?>">
    	<table width="90%" align="center">
        	<tr>
        	  <td width="2%" rowspan="2"><div style="color: rgb(255, 153, 0); float: left; font-size: 32px; ">&rsaquo;</div></td>
        	  <td colspan="5"><div style="font-size:13px; text-transform:uppercase; font-weight:bold;"><?php echo "$row_bancos[id_banco] - $row_bancos[nome_banco] conta: $row_bancos[conta] / ag&ecirc;ncia: $row_bancos[agencia]"?></div></td>
        	  </tr>
        	<tr>
        	  <td width="14%"> Vencidas hoje :
        	    <?=$num_saidas_hoje?></td>
        	  <td width="15%"> Vencidas :
        	    <?=$num_saidas_vencidas?></td>
        	  <td width="16%"> Proximas :
        	    <?=$num_saidas_futuras?></td>
        	  <td width="11%"> Entradas:
        	    <?=$num_entradas?></td>
        	  <td width="42%"><span class="total"> Total: R$
        	    <?=number_format($totalizador_geral,2,',','.');?>
      	    </span></td>
           	  </tr>
        </table>
    </div>
    <div class="lista">    
    <!-- LISTA DE SAIDAS -->
<?php if(empty($num_saidas_hoje) && empty($num_saidas_vencidas) && empty($num_saidas_futuras) && empty($num_entradas)) :
			echo "<center><b>Nenhuma saida encontrada.</b></center>";
		else:
?>
<table width="100%">
	<tr>
    	<td></td>
        <td></td>
        <td>Cod.</td>
        <td>Nome</td>
        <td>Data vencimento</td>
        <td>Valor</td>
        <td>Pagar</td>
        <td>Deletar</td>
  	</tr>
    <?php 
	foreach($Array_saidas  as $chave => $itens):
	if(empty($itens)) continue;
	?>
    <tr>
    	<td colspan="8">
        	<div class="divisor">
            	<?php 
					switch($chave){
						case 1: echo "SAIDAS COM VENCIMENTO HOJE.";
							break;
						case 2: echo "SAIDAS VENCIDAS.";
							break;
						case 3: echo "PROXIMAS SAIDAS.";
							break;
						case 4: echo "ENTRADAS.";
							break;
					}
				?>
            </div>
        </td>
    </tr>
	<?php foreach($itens as $row_saida):?>
     <tr class="<? if($alternateColor++%2==0) { ?>linha_um<? } else { ?>linha_dois<? } ?>">
     <?php $id = (empty($row_saida['id_entrada'])) ? $row_saida['id_saida'] : $row_saida['id_entrada'];?>
     	<?php $totalizador_individual += $row_saida['TOTAL'];?>
    	<td>
        <?php if(empty($row_saida['id_entrada'])): 
		$tipo = 'saida';
		?>
        	<input type="checkbox" class="saidas_check" name="saidas[]" value="<?=$id?>" />
        <?php else: ?>
        	<input type="checkbox" class="saidas_check" name="entradas[]" value="<?=$id?>" />
        <?php $tipo = 'entrada'; endif; ?>
        </td>
        <td>
        	<?php 
        	// Botão duplicar saida
        	if(empty($row_saida['id_entrada'])):
        	?>
        	<a title="Duplicar saida" href="view/duplicar.saida.php?ID=<?=$row_saida['id_saida']?>&tipo=saida"  onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )">
        	<img src="http://aux.iconpedia.net/uploads/16057974131954430258.png"  width="16" height="16"  border="0"/>
        	</a>
        	<?php endif;?>
        	
        	<?php
        	if($row_saida['tipo'] != '51') : // se tipo for dirente de rescisao.
			$qr_arquios = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row_saida[id_saida]'");
			$num_saida_file = mysql_num_rows($qr_arquios);
			 if($tipo == 'saida' and !empty($num_saida_file )):?>
				<?php $link_encryptado = encrypt('ID='.$id.'&tipo=0');?>
                <a target="_blank" title="Comprovante" href="view/comprovantes.php?<?=$link_encryptado?>">
                <img src="../financeiro/imagensfinanceiro/attach-32.png" width="16" height="16"  border="0"/>
                </a>
            <?php
            
            
            	// SE o tipo o laço for de entrada 
            	elseif($tipo == 'entrada'): 
					// verifica se a entrada é do tipo 12 e	
					
					if($row_saida['tipo'] == 12):

						$qr_notas = mysql_query("SELECT notas_files.id_file, notas_files.tipo, notas_files.id_notas FROM (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
													INNER JOIN notas_files ON notas.id_notas = notas_files.id_notas 
											WHERE notas_assoc.id_entrada = '$row_saida[id_entrada]' GROUP BY notas_files.id_file;") or die(mysql_error());
						$num_notas = mysql_num_rows($qr_notas);
						$row_notas = mysql_fetch_assoc($qr_notas);
						
					if(!empty($num_notas)):
            ?>
            	<a target="_blank" href="<?='http://'.$_SERVER['HTTP_HOST'].'/intranet/adm/adm_notas/visializa_files.php?id_nota='.$row_notas['id_notas']?>" >
            		<img src="../financeiro/imagensfinanceiro/attach-32.png" width="16" height="16"  border="0"/>
            	</a>
            <?php 		endif;
					endif;
            endif;
			?>
           	<?php else: 
					$qr_rescisao = mysql_query("SELECT rh_recisao.id_regiao,rh_recisao.id_clt, rh_recisao.id_recisao	 
												FROM (saida
												INNER JOIN pagamentos_especifico ON saida.id_saida = pagamentos_especifico.id_saida) 
												INNER JOIN  rh_recisao ON rh_recisao.id_clt = pagamentos_especifico.id_clt  
												
												WHERE saida.id_saida =  '$row_saida[id_saida]' AND rh_recisao.status = '1' ");
					$num_rescisao = mysql_num_rows($qr_rescisao);
					if(!empty($num_rescisao)){
						$row_recisao = mysql_fetch_array($qr_rescisao);
						$link = str_replace('+','--',encrypt("$row_recisao[0]&$row_recisao[1]&$row_recisao[2]"));
					//echo "nova_rescisao.php?enc=$link";
			?>
            <a target="_blank" href="<?='http://'.$_SERVER['HTTP_HOST'].'/intranet/rh/recisao/nova_rescisao.php?enc='.$link;?>" >
            		<img src="../financeiro/imagensfinanceiro/attach-32.png" width="16" height="16"  border="0"/>
           	</a>
            <?php } //?>
            
            <?php endif; // RESCISAO ?>
            	
                <?php 
				/*$id_edicao = (empty($row_saida['id_saida'])) ? $row_saida['id_entrada'] : $row_saida['id_saida'];
				if(empty($row_saida['id_saida'])){
					$entrada = '&entrada=true';
					$tipo = '&tipo=entrada';
				}else{
					$tipo = '&tipo=saida';	
				}*/
				?>
                
                <?php 
					if(!empty($row_saida['id_saida'])) :
				?>
            	 <a href="view/editar.saida.naopaga.php?id=<?=$row_saida['id_saida']?>&tipo=saida"  onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )"><img src="image/editar.gif" width="16" height="16" border="0"></a>
                 <?php endif;?>
               
            
           <!-- <img src="image/editar.gif"  width="16" height="16" border="0" /> -->
        </td>
        <td><?=$id?></td>
        <td>
		<?php $tipo = (empty($row_saida['id_saida'])) ? '&tipo=entrada' : '&tipo=saida'; ?>
			<?=$row_saida['nome']?>
            <span style="display:none"><?=$row_saida['nome']?></span>
            <a title="Detalhes" href="view/detalhes.saidas.php?ID=<?=$id?><?=$tipo.$entrada?>" onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )">
            <img src="image/seta.gif" border="0" >
            </a>
        </td>
        <td><?=format_date($row_saida['data_vencimento'])?>
        </td>
        <td>R$ <?=number_format($row_saida['TOTAL'],2,',','.');?></td>
        <td align="center">
        	<?php 
			$Comando_pagar_entrada = "'../ver_tudo.php?id=17&pro=$row_saida[id_entrada]&tipo=pagar&tabela=entrada&regiao=$regiao&idtarefa=2','Deseja CONFIRMAR esta ENTRADA?'";
			$Comando_pagar_saida = "'../ver_tudo.php?id=17&pro=$row_saida[id_saida]&tipo=pagar&tabela=saida&regiao=$regiao&idtarefa=1','Deseja PAGAR esta SAIDA?'";
			$Comando_delet_entrada = "'../ver_tudo.php?id=17&pro=$row_saida[id_entrada]&tipo=deletar&tabela=entrada&regiao=$regiao','Deseja DELETAR esta ENTRADA?'";
			$Comando_delet_saida = "'../ver_tudo.php?id=17&pro=$row_saida[id_saida]&tipo=deletar&tabela=saida&regiao=$regiao','Deseja DELETAR esta SAIDA?'";
			if(empty($row_saida['id_entrada'])){
				$comando_pagar = $Comando_pagar_saida;
				$comando_deletar = $Comando_delet_saida;
			}else{
				$comando_pagar = $Comando_pagar_entrada;
				$comando_deletar = $Comando_delet_entrada;
			}
			
			?>
			<a href="#" onclick="confirmacao(<?=$comando_pagar?>)">
        	<img src="../financeiro/imagensfinanceiro/Money-32.png" alt="Editar" border="0">
			</a>
        </td>
        <td align="center">
        	<a href="#" onclick="confirmacao(<?=$comando_deletar?>)">
            <img src="../financeiro/imagensfinanceiro/Delete-32.png" alt="Deletar" border="0" >
        	</a>
        </td>
    </tr>
    <?php endforeach;?>
    <tr>
    	<td colspan="5" align="right"><b>Total:</b></td>
        <td colspan="3"><b>R$ <?=number_format($totalizador_individual,2,',','.');?></b></td>
        <?php unset($totalizador_individual);?>
    </tr>
	<?php endforeach;?>
    <tr>
    	<td colspan="5" align="right"><b>Total de saidas Final: </b></td>
        <td colspan="3"><b>R$ <?=number_format($totalizador_geral,2,',','.')?></b></td>
    </tr>
     <tr>
    	<td colspan="5" align="right"><b>Total de entradas Final: </b></td>
        <td colspan="3"><b>R$ <?=number_format($totalizador_geral_entrada,2,',','.')?></b></td>
    </tr>
</table>
	<!-- FIM DA LISTA DE SAIDAS-->
    <?php endif;?>
   	</div>

    </div>
<?php }while($row_bancos = mysql_fetch_assoc($qr_bancos)); ?>
</fieldset>
<input type="hidden" name="logado" id="logado" value="<?= $id_user; ?>" />
</form>
<!-- SAIDA DE CAIXA -->
<fieldset >
	<legend><img src="../financeiro/imagensfinanceiro/caixa-32.png" align="absmiddle" />&nbsp;RELA&Ccedil;&Atilde;O DE SA&Iacute;DAS DO CAIXA:</legend>
   
            <?php
    $mes_h = date('m');
	$ano = date('Y');
	$somaCA = "0";
	$cont = "";
	print "<table width='100%' border='0' cellpadding='0' cellspacing='0' id='TabelaCaixinha'>";
	$result_caixa = mysql_query("SELECT *,date_format(data_vencimento, '%d/%m/%Y')as data_vencimento2 ,date_format(data_proc, '%d/%m/%Y')as data_proc 
	FROM caixa where id_regiao = '$regiao' and status = '1' and data_proc >= '$ano-$mes_h-01'");
	while($row_caixa = mysql_fetch_array($result_caixa)){
		if($cont % 2){ $color="#FFFFFF"; }else{ $color="#EEEEEE"; }
  $valorCA = "$row_caixa[valor]";
  $adicionalCA = "$row_caixa[adicional]";
  $valorCA = str_replace(".", "", $valorCA);
  $valorCA = str_replace(",", ".", $valorCA);
  $adicionalCA = str_replace(".", "", $adicionalCA);
  $adicionalCA = str_replace(",", ".", $adicionalCA);
  $valor_finaCA = $valorCA + $adicionalCA;
  $valor_fCA = number_format($valor_finaCA,2,",",".");
  $valor2_fCA = number_format($valorCA,2,",",".");
	print "
	<tr class='linhaspeq' bgcolor=$color height=20>
	<td align='left' class='linhaspeq' >$row_caixa[data_proc] - Nome: $row_caixa[nome]</td>
	<td class='linhaspeq' ><b>R$ $valor2_fCA<b></td>
	<td class='linhaspeq'><b>R$ $adicionalCA</b></td>
	</tr>";
	$somaCA = $somaCA + $valor_finaCA;
	$cont ++;
	}
    $somaCA_F = number_format($somaCA,2,",",".");
	$result_caixinha = mysql_query("SELECT saldo FROM caixinha WHERE id_regiao = '$regiao'");
	while($row_caixinha = mysql_fetch_array($result_caixinha)){
		$saldo_caixinha = (float) str_replace(",",".", $row_caixinha['saldo']);
		$saldo_caixinha_formatado = number_format($saldo_caixinha,2,",",".");
		$soma_saldo = $soma_saldo + $saldo_caixinha;
	}
	$saldo_caixinha = number_format($soma_saldo,2,",",".");
	$calculo_caixinha = $soma_saldo - $soma2;
	$calculo_caixinha_f = number_format($calculo_caixinha,2,",",".");
	print "
    <tr class='linhaspeq' >
	<td height='18' colspan='3' align='center'>
	<table width='100%'>
	<tr> 
    <td width='50%' bgcolor='#CCCCCC'><div align='center' style='color:#000000; font-size:14px'><b>TOTAL DE SA&Iacute;DAS DO CAIXA</b></div></td>
    <td width='50%' bgcolor='#CCCCCC'><div align='center' style='color:#000000; font-size:14px'><b>SALDO DO CAIXA</b></div></td>
	</tr>
	<tr class='linhaspeq' >
	<td class='linhaspeq'><div align='center' style='color:#000000; font-size:14px'><b>R$ $somaCA_F</b></div></td>
	<td class='linhaspeq'><div align='center' style='color:#000000; font-size:14px'><b>R$ $saldo_caixinha_formatado </b></div></td>
	</tr>
	</table>
	</td></tr></table>"; 
	  unset($soma_f);
	  unset($cont);
	  unset($soma);
	  unset($valor);
	  ?>
</fieldset>

<!-- FIM SAIDA DE CAIXA -->

</div>
<?php endif;?>
</body>
</html>