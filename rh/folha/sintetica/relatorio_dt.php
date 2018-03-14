<?php

function mesesdiferenca($data1, $data2) {

    if($data1 && $data2) {
        $vetorData1 = explode("/", $data1);
        $vetorData2 = explode("/", $data2);
        $resultado = ($vetorData2[2] - $vetorData1[2]) * 12;
        if ($vetorData1[1] > $vetorData2[1]) {
            $resultado -= ($vetorData1[1] - $vetorData2[1]);
        }else if ($vetorData2[1] > $vetorData1[1]) {
            $resultado += ($vetorData2[1] - $vetorData1[1]);
        }
    }else {
        $resultado = 0;
    }

    return $resultado + 1;
}

// Incluindo Arquivos
require('../../../conn.php');
include('../../../classes/calculos.php');
include('../../../classes/formato_valor.php');
include('../../../classes/formato_data.php');
include('../../../classes/valor_proporcional.php');
include('../../../classes/EventoClass.php');
include('../../../classes/FeriasClass.php');
include('../../../classes/MovimentoClass.php');
include('../../../classes/RescisaoClass.php');
include('../../../classes/CalculoRescisaoClass.php');
include('../../../classes/CalculoFolhaClass.php');
include('../../../funcoes.php');

// Definindo Classe Cálculos
$Calc = new calculos();
$Trab = new proporcional();

// Id do Participante e Id da Folha
list($clt,$folha) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));




// Consulta da Folha
$qr_folha    = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$folha' AND status = '2'");
$row_folha   = mysql_fetch_array($qr_folha);
$data_inicio = $row_folha['data_inicio'];
$data_fim    = $row_folha['data_fim'];
$ano         = $row_folha['ano'];
$mes         = $row_folha['mes'];
$mes_int     = (int)$mes;
$objFerias    = new Ferias();
$objEvento    = new Eventos();
$objRescisao  = new Rescisao();
$objCalcRescisao= new Calculo_rescisao();
$objCalcFolha= new Calculo_Folha();
$objMovimento = new Movimentos();
$objMovimento->carregaMovimentos($ano);

// Redefinindo Variáveis de Décimo Terceiro
if($row_folha['terceiro'] != 1) {
	$decimo_terceiro = NULL;
} else {
	$decimo_terceiro = 1;
	$tipo_terceiro   = $row_folha['tipo_terceiro'];
}

// Consulta da Região
$qr_regiao = mysql_query("SELECT id_regiao FROM regioes WHERE id_regiao = '$row_folha[regiao]'");
$regiao    = mysql_result($qr_regiao, 0);

// Consulta do Projeto
$qr_projeto = mysql_query("SELECT id_projeto FROM projeto WHERE id_projeto = '$row_folha[projeto]'");
$projeto    = mysql_result($qr_projeto, 0);

// Consulta dos Participantes da Folha
$qr_participante  = mysql_query("SELECT * FROM rh_folha_proc WHERE id_clt = '$clt' AND id_folha = '$folha' AND status = '2'");
$row_participante = mysql_fetch_array($qr_participante);



// Encriptografando Links
$link_movimento = encrypt("$regiao&$clt"); 
$link_movimento = str_replace('+','--',$link_movimento);



		  
// Calculando a Folha
include('calculos_folha.php');

// Rendimentos e Descontos
settype($rendimentos_listados, 'array');
settype($rendimentos_nome, 'array');
settype($rendimentos_valor, 'array');
settype($descontos_listados, 'array');
settype($descontos_nome, 'array');
settype($descontos_valor, 'array');

if(!empty($ids_movimentos_estatisticas)) {
	
	$ids_movimentos = implode(',', $ids_movimentos_estatisticas);
	$qr_movimentos  = mysql_query("SELECT * FROM rh_movimentos_clt 
								   WHERE id_movimento IN($ids_movimentos) 
								   ORDER BY cod_movimento ASC");
	while($movimento = mysql_fetch_array($qr_movimentos)) {
		
		if($movimento['tipo_movimento'] == 'CREDITO') {
			
			if(!in_array($movimento['id_mov'], $rendimentos_listados)) {
				$rendimentos_listados[] = $movimento['id_mov'];
				$rendimentos_nome[]     = $movimento['nome_movimento'];
				$rendimentos_valor[]    = $movimento['valor_movimento'];
			}
			
		} elseif($movimento['tipo_movimento'] == 'DEBITO') {
			
			if(!in_array($movimento['id_mov'], $descontos_listados)) {
				$descontos_listados[] = $movimento['id_mov'];
				$descontos_nome[]     = $movimento['nome_movimento'];
				$descontos_valor[]    = $movimento['valor_movimento'];
			}
	
		}
                
        }
        
        

}
	   
// Organizado as Arrays pelo Nome
array_multisort($rendimentos_nome, $rendimentos_valor);
array_multisort($descontos_nome, $descontos_valor);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: Intranet :: <?=$row_clt['nome']?></title>
<link href="folha.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#FFF; margin:0px;">
<div id="corpo">
   <table cellspacing="4" cellpadding="0" id="topo">
      <tr>
        <td>
        	<?='('.$clt.') '.$row_clt['nome']?>
        </td>
      </tr>
    </table>
     
    <table cellpadding="0" cellspacing="0" id="relatorio">
      <tr id="salario" style="border:0px;">
	    <td colspan="2">SAL&Aacute;RIO</td>
	    <td><a href="../../alter_clt.php?clt=<?=$clt?>&pro=<?=$projeto?>" target="_blank">Editar Cadastro <img src="seta_transparente.png"></a></td>
	  </tr>
      <tr class="linha_um">
        <td class="nome">SAL&Aacute;RIO CONTRATUAL</td>
        <td class="valor">R$ <?=formato_real($salario_limpo)?></td>
        <td class="descricao"><?=@mysql_result($qr_curso, 0, 1)?></td>    
      </tr>
      <tr class="linha_dois">
        <td class="nome">VALOR/MÊS</td>
        <td class="valor">R$ <?php echo formato_real($valor_mes); ?></td>
        <td class="descricao">R$ <?php echo formato_real($salario_limpo).' / 12 meses'; ?></td>
      </tr>
      <tr class="linha_um">
        <td class="nome">MESES TRABALHADOS</td>
        <td class="valor"><?=$meses?> meses</td>
        <td class="descricao"><?php if(!empty($sinaliza_evento) and $meses != 12) {
				  						echo 'FICOU '.$meses_evento.' MESES DE LICENÇA.<BR> VÍNCULO A '.($Calc->meses_trab).' MESE(S).';
									} elseif($meses != 12) {
										echo 'FOI CONTRATADO EM '.formato_brasileiro($row_clt['data_entrada']);
									}                                                                         
                                                                        ?>
        
        </td>
      </tr>
      <tr class="linha_dois">
        <td class="nome">SAL&Aacute;RIO</td>
        <td class="valor">R$ <?=formato_real($decimo_terceiro_credito)?></td>
        <td class="descricao">R$ <?=formato_real($valor_mes)?> x <?=$meses?> meses</td>   
      </tr>
      
      <?php if(!empty($rendimentos) || $decimo_terceiro) { ?>
            
	  <tr id="rendimentos">
	    <td colspan="2">RENDIMENTOS</td>
	    <td><a href="../../rh_movimentos_1.php?tela=2&enc=<?=$link_movimento?>#credito" target="_blank">Gerenciar Rendimentos <img src="seta_transparente.png"></a></td>
	  </tr>
      <tr style="display:none;">
        <td class="nome">D&Eacute;CIMO TERCEIRO SAL&Aacute;RIO</td>
        <td class="valor"></td>
        <td class="descricao"></td>
      </tr>
      
    <?php  if(!empty($salario_maternidade)) { ?>
      <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">SAL&Aacute;RIO MATERNIDADE</td>
        <td class="valor">R$ <?=formato_real($salario_maternidade)?></td>
        <td class="descricao"></td>    
      </tr>
      <?php } ?>
<?php foreach($rendimentos_valor as $chave => $valor) {
		if(!empty($valor)) { ?>
          
          <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
            <td class="nome"><?=$rendimentos_nome[$chave]?></td>
            <td class="valor">R$ <?=formato_real($valor)?></td>
            <td class="descricao"></td>   
          </tr>
          
      <?php } } } else { ?>
      
      <tr id="rendimentos">
	    <td colspan="3" align="right"><a href="../../rh_movimentos_1.php?tela=2&clt=<?=$clt?>&pro=<?=$projeto?>&enc=<?=$link_movimento?>#credito" target="_blank">Adicionar Rendimentos <img src="seta_transparente.png"></a></td>
	  </tr>
      
     <?php }
	  
	  		$descontos += $inss_completo + $irrf_completo;
			if(!empty($descontos) or !empty($row_clt['desconto_inss'])) { ?>
	  
	  <tr id="descontos">
	    <td colspan="2">DESCONTOS</td>
	    <td><a href="../../rh_movimentos_1.php?tela=2&clt=<?=$clt?>&pro=<?=$projeto?>&enc=<?=$link_movimento?>#debito" target="_blank">Gerenciar Descontos <img src="seta_transparente.png"></a></td>
      </tr>
       <?php if((!empty($inss_dt) and $inss_dt != '0.00') or !empty($row_clt['desconto_inss'])) { ?>
      <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">INSS</td>
        <td class="valor">R$ <?=formato_real($inss_dt)?></td>
        <td class="descricao">
        <?php if($row_clt['tipo_desconto_inss'] == 'isento') { ?>
        	INSS recolhido em outra empresa
        <?php } elseif($row_clt['tipo_desconto_inss'] == 'parcial') { ?>
        	INSS parcialmente recolhido em outra empresa
        <?php } else { ?>
        	R$ <?=formato_real($base_inss)?> x <?=$percentual_inss?>%
        <?php } ?> 
        </td>   
      </tr>
       <?php } if(!empty($irrf_dt)) { ?>
      <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">IMPOSTO DE RENDA</td>
        <td class="valor">R$ <?=formato_real($irrf_dt)?></td>
        <td class="descricao">
     (<?php if(!empty($deducao_irrf)) { ?>(R$ <?=formato_real($base_irrf)?> - R$ <?=formato_real($deducao_irrf)?>)<?php } else { ?>R$ <?=formato_real($base_irrf)?><?php } ?> x <?=$percentual_irrf?>%) - R$ <?=formato_real($fixo_irrf)?>
        </td>
      </tr>
      <?php } foreach($descontos_valor as $chave => $valor) {
			    	if(!empty($valor)) { ?>
          
          <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
            <td class="nome"><?=$descontos_nome[$chave]?></td>
            <td class="valor">R$ <?=formato_real($valor)?></td>
            <td class="descricao"></td>   
          </tr>
          
      <?php } } } else { ?>
      
      <tr id="descontos">
	    <td colspan="3" align="right"><a href="../../rh_movimentos_1.php?tela=2&clt=<?=$clt?>&pro=<?=$projeto?>&enc=<?=$link_movimento?>#debito" target="_blank">Adicionar Descontos <img src="seta_transparente.png"></a></td>
      </tr>
      
      <?php } ?>
      
      <tr id="liquido">
	    <td colspan="3">L&Iacute;QUIDO</td>
      </tr>
      <tr class="linha_um">
      	<td class="nome">TOTAL DE RENDIMENTOS</td>
	    <td class="valor">R$ <?=formato_real($rendimentos)?></td>
        <td class="descricao">&nbsp;</td>
      </tr>
      <tr class="linha_dois">
      	<td class="nome">TOTAL DE DESCONTOS</td>
	    <td class="valor">R$ <?=formato_real($descontos - $valorPensaoExibirPopUp)?></td>
        <td class="descricao">&nbsp;</td>
      </tr>
      
      <?php  if(isset($arrayPensoes) && !empty($arrayPensoes)){ ?>
        <?php foreach ($arrayPensoes as $key => $values){ ?>
            <tr class="linha_um">
              <td class="nome"><?php echo $values['nome']; ?></td>
              <td class="valor">R$ <?php echo number_format($values['valor'],2,',','.'); ?></td>
              <?php if($values['valorfixo'] > 0){ ?>
              <td class="descricao">Valor Fixo</td>
              <?php }else{ ?>
              <td class="descricao"><?php echo "R$ " . number_format($values['base'],2,',','.') . " x " . ($values['aliquota'] * 100) . "% (" .$values['legenda']. ")"; ?></td>
              <?php } ?>
            </tr>
        <?php } ?>
      <?php } ?>
      
<!--      <tr class="linha_um">
      	<td class="nome">SAL&Aacute;RIO L&Iacute;QUIDO</td>
	    <td class="valor">R$ <?=formato_real(abs($liquido))?></td>
        <td class="descricao">R$ <?=formato_real($decimo_terceiro_credito_final)?> + R$ <?=formato_real($rendimentos)?> - R$ <?=formato_real($descontos)?></td>
      </tr>-->
      
      <tr class="linha_dois">
      	<td class="nome">SAL&Aacute;RIO L&Iacute;QUIDO</td>
	    <td class="valor">R$ <?=formato_real($liquido)?></td>
        <td class="descricao">R$ <?=formato_real($decimo_terceiro_credito_final)?> + R$ <?=formato_real($rendimentos)?> - R$ <?=formato_real($descontos - $valorPensaoExibirPopUp) ?> 
            <?php if(isset($arrayPensoes) && !empty($arrayPensoes)){ ?>
                    <?php foreach ($arrayPensoes as $key => $values){ ?>
                        <?php echo " - R$ " . number_format($values['valor'],2,',','.'); ?>
                    <?php } ?>
            <?php } ?>
        </td>
      </tr>
    </table>
</div>
</body>
</html>