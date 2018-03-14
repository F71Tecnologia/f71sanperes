   <?php

  		//SOMENTE PODEM VER CONTROLE DE COMBUSTIVEL		

  	/*	if($id_user == '27' or $id_user == '52' or $id_user == '5' or $id_user == '1' or $id_user == '65' or $id_user == '9' or $id_user == '64' or $id_user == '77' or $id_user == '75' or $id_user == '85' or $id_user == '87'){*/

			

	$qr_regioes = mysql_query("SELECT * FROM regioes WHERE id_master = '$id_master'");

	while($row_regioes = mysql_fetch_assoc($qr_regioes)):

	

	 $regioes[] = $row_regioes['id_regiao'];

	

	endwhile;

   $regioes = implode(',',$regioes);

 







if($acoes->verifica_permissoes(9)) {

							?>

        <fieldset>

        	<legend> &nbsp;&nbsp;CONTROLE DE COMBUST&Iacute;VEL:</legend>

          <span id="FimComb"></span>

          <?php

	
      $REComb = mysql_query("SELECT *,date_format(data_cad, '%d/%m/%Y')as data_cad FROM fr_combustivel WHERE id_regiao IN($regioes) AND status_reg = '1' ") ;
      $cont = "0";

	

	if(@mysql_num_rows($REComb) != 0) {

		echo "<table width='100%' border='0' cellspacing='1' cellpadding='0' bgcolor='#CCCCCC' id='TabelaCombustivel'>";

		

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

	

	} else {

	

		echo '<div class="msg_aviso">Sem registros nesta empresa.</div>';	

		

	}

    ?>

    	</fieldset>

       

		<?php

  		}

  		?>





<!-- FINALIZANDO A DIV DO CONTROLE DE COMBUSTIVEL -->  













<?php



/////PERMISSAO RESUMO DE CONTAS

/*

$verifica_acoes = mysql_num_rows(mysql_query("SELECT * FROM funcionario_acoes_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND acoes_id  = '10' "));





if($acoes->verifica_permissoes(10)) {



?>

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

    <tr  class="linha_<?php if($linha++%2==0) { echo 'dois'; } else { echo 'um'; } ?>" >

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





<?php } 

*/

?>





<!-- TOTALIZADOR -->  





<!--------------------CONTROLE DE REEMBOLSO ---------------------------------------------------------------------->

<?php

/////PERMISSAO CONTROLE DE REEMBOLSO

if($acoes->verifica_permissoes(11)) {

?>



<table width='100%' class="tabela" style="font-size:12px;">



<tr>

	<td class="titulo_tabela" colspan="5">CONTROLE DE REEMBOLSO:</td>

    

</tr>

        



<?php



     

	$REReem = mysql_query("SELECT *,date_format(data, '%d/%m/%Y %H:%i:%s')as data FROM fr_reembolso WHERE status = '1' AND id_regiao IN ($regioes)");

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

	  $link = "<a href='../frota/ver_reembolso.php?id=1&reembolso=$RowReem[0]&regiao=".$regiao."' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" title=\"Confirmar reembolso\">";

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

    <tr>

	<td>&nbsp;</td>

</tr>

   </table>



<?php } ?>

<!----------- FIM CONTROLE DE REEMBOLSO -->









<!--------------------------------- PRESTADOR DE SERVIÇO ------------------------------------------>

<?php 



/////PERMISSAO  PRESTADOR DE SERVIÇO
/*
$verifica_acoes = mysql_num_rows(mysql_query("SELECT * FROM funcionario_acoes_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND acoes_id  = '12'"));



if($verifica_acoes !=0) {

		

		include "Prestador/view_teste2.php";

}

	
*/
?>



<!-- FIM PRESTADOR DE SERVIÇO -->











