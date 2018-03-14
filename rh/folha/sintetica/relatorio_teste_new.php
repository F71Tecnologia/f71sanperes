
<?php
// Incluindo Arquivos
require('../../../conn.php');
include('../../../classes/calculos.php');
include('../../../classes/calculos_new.php');
include('../../../classes/formato_valor.php');
include('../../../classes/formato_data.php');
include('../../../classes/valor_proporcional.php');
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

$CALC_NEW = new Calculos_new($ano);

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
$qr_participante  = mysql_query("SELECT A.id_folha_proc,  C.salario, C.tipo_insalubridade, C.qnt_salminimo_insalu,
                                      B.id_clt,B.nome,  B.data_entrada,B.insalubridade, B.status,B.desconto_inss,B.tipo_desconto_inss, B.valor_desconto_inss,B.tipo_contratacao,
                                      B.transporte,B.rh_sindicato,B.ano_contribuicao,B.id_curso,C.nome as funcao
    

                                    FROM rh_folha_proc as A
                                    INNER  JOIN rh_clt as B
                                    ON A.id_clt = B.id_clt
                                    LEFT JOIN curso as C ON(C.id_curso = B.id_curso)
                                    
                                    WHERE A.id_folha = '$folha' AND A.status = '2' AND A.id_clt = '$clt'
                                    ORDER BY A.nome ASC");

$row_participante = mysql_fetch_array($qr_participante);

// Encriptografando Links
$link_movimento = encrypt("$regiao&$clt"); 
$link_movimento = str_replace('+','--',$link_movimento);
		  


//////////////////////////////////////////////////////////////////
/////DIAS TRABALHADOS DE ACORDO COM PERÍODO SELECIONADO NA FOLHA
//////////////////////////////////////////////////////////////////
  $dt_ini     = explode('-',$data_inicio);
  $dt_ini_seg = mktime(0, 0, 0, $dt_ini[1], $dt_ini[2], $dt_ini[0]);  
  $dt_fim     = explode('-',$data_fim);
  $dt_fim_seg = mktime(0, 0, 0, $dt_fim[1], $dt_fim[2], $dt_fim[0]);
  
  $total_dias_folha = round(($dt_fim_seg - $dt_ini_seg)/86400)+1;    //TOTAL DE DIAS ENTRE A DATA DE INICIO E TÉRMINO DA FOLHA
  $ultimo_dia_mes   = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
  
   if($mes_int != 2) {                
                if($ultimo_dia_mes == 31) {$total_dias_folha = $total_dias_folha - 1;}
           }else {   
               if( $total_dias_folha == $ultimo_dia_mes){ 
                   $total_dias_folha = 30;
               }     
           }


// Calculando a Folha
include('calculos_folha_teste_new.php');
    

    
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
				$rendimentos_listados[] = $movimento['id_movimento'];
				$rendimentos_nome[]     = $movimento['nome_movimento'];
				$rendimentos_valor[]    = $movimento['valor_movimento'];
			}
			
		} elseif($movimento['tipo_movimento'] == 'DEBITO' or $movimento['tipo_movimento'] == 'DESCONTO') {
			
			if(!in_array($movimento['id_mov'], $descontos_listados)) {
				$descontos_listados[] = $movimento['id_movimento'];
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
<title>:: Intranet :: <?=$row_participante['nome']?></title>
<link href="folha.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color:#FFF; margin:0px;">
<div id="corpo">
   <table cellspacing="4" cellpadding="0" id="topo">
      <tr>
        <td>
        	<?='('.$clt.') '.$row_participante['nome']?>
        </td>
      </tr>
    </table>
     
    <table cellpadding="0" cellspacing="0" id="relatorio">
      <tr id="salario" style="border:0px;">
	    <td colspan="2">SAL&Aacute;RIO</td>
	    <td><a href="../../alter_clt.php?clt=<?=$clt?>&pro=<?=$projeto?>&folha" target="_blank">Editar Cadastro <img src="seta_transparente.png"></a></td>
	  </tr>
      <tr class="linha_um">
        <td class="nome">SAL&Aacute;RIO CONTRATUAL</td>
        <td class="valor">R$ <?=formato_real($salario_limpo)?></td>
        <td class="descricao"><?php echo $row_participante['funcao']?></td>    
      </tr>
      <tr class="linha_dois">
        <td class="nome">VALOR/DIA</td>
        <td class="valor">R$ <?php echo formato_real($valor_dia); ?></td>
        <td class="descricao">R$ <?php echo formato_real($salario_limpo).' / 30 dias'; ?></td>
      </tr>
      <tr class="linha_um">
        <td class="nome">DIAS TRABALHADOS</td>
        <td class="valor"><?=$dias?> dias</td>
        <td class="descricao">
        <?php if(!empty($dias_entrada)) {
				  echo "FOI CONTRATADO EM ".formato_brasileiro($row_participante['data_entrada'])."";
		      } elseif(!empty($sinaliza_evento)) {
				  echo "FICOU $dias_evento DIAS DE LICENÇA "."<br>PERÍODO: ".$msg_evento;
			  } elseif(!empty($dias_ferias)) {
				  echo "TEVE $dias_ferias DIAS DE FÉRIAS";
			  } elseif(!empty($dias_rescisao)) {
				  echo "FOI RESCINDIDO EM ".formato_brasileiro($row_participante['data_saida'])."";
			  } elseif(!empty($dias_faltas)) {
				  echo "FALTOU $dias_faltas DIAS";
			  } ?>
        </td>
      </tr>
      <tr class="linha_dois">
        <td class="nome">SAL&Aacute;RIO</td>
        <td class="valor">R$ <?=formato_real($salario)?></td>
        <td class="descricao">R$ <?=formato_real($valor_dia)?> x <?=$dias?> dias</td>   
      </tr>
      
      <?php $rendimentos += $familia;
   
	  		if(!empty($rendimentos)) { ?>
            
	  <tr id="rendimentos">
	    <td colspan="2">RENDIMENTOS</td>
	    <td><a href="../../rh_movimentos_1.php?tela=2&clt=<?php echo $clt?>&enc=<?=$link_movimento?>&folha#credito" target="_blank">Gerenciar Rendimentos <img src="seta_transparente.png"></a></td>
	  </tr>
      <?php if(!empty($familia)) { ?>
      <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">SAL&Aacute;RIO FAMILIA</td>
        <td class="valor">R$ <?=formato_real($familia)?></td>
        <td class="descricao">R$ <?=formato_real($fixo_familia)?> x <?=$filhos_familia?> filhos</td>    
      </tr>
      <?php } if(!empty($salario_maternidade)) { ?>
      <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">SAL&Aacute;RIO MATERNIDADE</td>
        <td class="valor">R$ <?=formato_real($salario_maternidade)?></td>
        <td class="descricao"></td>    
      </tr>
      <?php } if(!empty($valor_ferias)) { ?>
      <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">F&Eacute;RIAS</td>
        <td class="valor">R$ <?=formato_real($valor_ferias)?></td>
        <td class="descricao"></td>    
      </tr>
      <?php } if(!empty($valor_rescisao)) { ?>
      <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">RESCIS&Atilde;O</td>
        <td class="valor">R$ <?=formato_real($valor_rescisao)?></td>
        <td class="descricao"></td>    
      </tr>
      <?php } 

	 foreach($rendimentos_valor as $chave => $valor) {
		if(!empty($valor)) { ?>
          
          <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
            <td class="nome"><?=$rendimentos_nome[$chave]?></td>
            <td class="valor">R$ <?=formato_real($valor)?></td>
            <td class="descricao"></td>   
          </tr>
          
      <?php } } } else { ?>
      
      <tr id="rendimentos">
	    <td colspan="3" align="right"><a href="../../rh_movimentos_1.php?tela=2&clt=<?php echo $clt?>&enc=<?=$link_movimento?>&folha#credito" target="_blank">Adicionar Rendimentos <img src="seta_transparente.png"></a></td>
	  </tr>
      
     <?php }
	  
	  		$descontos += $inss_completo + $irrf_completo;
			if(!empty($descontos) or !empty($row_participante['desconto_inss'])) { ?>
	  
	  <tr id="descontos">
	    <td colspan="2">DESCONTOS</td>
	    <td><a href="../../rh_movimentos_1.php?tela=2&clt=<?php echo $clt?>&enc=<?=$link_movimento?>&folha#debito" target="_blank">Gerenciar Descontos <img src="seta_transparente.png"></a></td>
      </tr>
       <?php if(!empty($sindicato)) { ?>
      <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">CONTRIBUI&Ccedil;&Atilde;O SINDICAL</td>
        <td class="valor">R$ <?=formato_real($sindicato)?></td>
        <td class="descricao"></td>    
      </tr>
       <?php } if((!empty($inss) and $inss != '0.00') or !empty($row_participante['desconto_inss'])) { ?>
      <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">INSS</td>
        <td class="valor">R$ <?=formato_real($inss)?></td>
        <td class="descricao">
        <?php if($row_participante['tipo_desconto_inss'] == 'isento') { ?>
        	INSS recolhido em outra empresa
        <?php } elseif($row_participante['tipo_desconto_inss'] == 'parcial') { ?>
        	INSS parcialmente recolhido em outra empresa
        <?php } else { ?>
        	R$ <?=formato_real($base_inss)?> x <?=$percentual_inss?>%
        <?php } ?> 
        </td>   
      </tr>
       <?php } if(!empty($irrf)) { ?>
      <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">IMPOSTO DE RENDA</td>
        <td class="valor">R$ <?=formato_real($irrf)?></td>
        <td class="descricao">
     <?php if(!empty($deducao_irrf)) { ?> Base do IRRF: R$ <?=formato_real($base_irrf)?> <br>
                                          - DDIR: <?php echo 'R$ '.formato_real($deducao_irrf).' ('.$filhos_irrf.' dependentes)';?>
                                          
                                          <?php } else { ?> Base do IRRF: R$ <?=formato_real($base_irrf)?><?php } ?><br>
                                                            x Percentual: <?=$percentual_irrf?>%<br>
                                                            - Parcela a Deduzir: R$ <?=formato_real($fixo_irrf)?>
        </td>
      </tr>
      <?php } if(!empty($vale_transporte) and $vale_transporte != '0.00') { ?>
      <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">DESCONTO VALE TRANSPORTE</td>
        <td class="valor">R$ <?=formato_real($vale_transporte)?></td>
        <td class="descricao">R$ <?=formato_real($salario_limpo)?> x 6%</td>    
      </tr> 
       <?php } if(!empty($desconto_aux_distancia) and $desconto_aux_distancia != '0.00') { ?>
      <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">DESCONTO AUXILIO DISTÂNCIA</td>
        <td class="valor">R$ <?=formato_real($desconto_aux_distancia)?></td>
        <td class="descricao">R$ <?=formato_real($salario_limpo)?> x 6%</td>    
      </tr>
       <?php } if(!empty($vale_refeicao)) { ?>
      <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">DESCONTO VALE REFEI&Ccedil;&Atilde;O</td>
        <td class="valor">R$ <?=formato_real($vale_refeicao)?></td>
        <td class="descricao">R$ <?=formato_real($base_refeicao)?> x 20%</td>    
      </tr>
       <?php } if(!empty($desconto_ferias)) { ?>
      <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">VALOR PAGO NAS F&Eacute;RIAS</td>
        <td class="valor">R$ <?=formato_real($desconto_ferias)?></td>
        <td class="descricao"></td>    
      </tr>
      <?php } if(!empty($inss_ferias)) { ?>
      <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">INSS SOBRE F&Eacute;RIAS</td>
        <td class="valor">R$ <?=formato_real($inss_ferias)?></td>
        <td class="descricao"></td>    
      </tr>
      <?php } if(!empty($irrf_ferias) and $irrf_ferias != '0.00') { ?>
      <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">IRFF SOBRE F&Eacute;RIAS</td>
        <td class="valor">R$ <?=formato_real($irrf_ferias)?></td>
        <td class="descricao"></td>    
      </tr>
      <?php } if(!empty($desconto_rescisao)) { ?>
      <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">VALOR PAGO NA RESCIS&Atilde;O</td>
        <td class="valor">R$ <?=formato_real($desconto_rescisao)?></td>
        <td class="descricao"></td>    
      </tr>
      <?php } if(!empty($inss_rescisao)) { ?>
      <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">INSS SOBRE RESCIS&Atilde;O</td>
        <td class="valor">R$ <?=formato_real($inss_rescisao)?></td>
        <td class="descricao"></td>    
      </tr>
      <?php } if(!empty($irrf_rescisao)) { ?>
      <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">IRRF SOBRE RESCIS&Atilde;O</td>
        <td class="valor">R$ <?=formato_real($irrf_rescisao)?></td>
        <td class="descricao"></td>   
      </tr>
      <?php }
	   
	   		foreach($descontos_valor as $chave => $valor) {
			    	if(!empty($valor)) { ?>
          
          <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
            <td class="nome"><?=$descontos_nome[$chave]?></td>
            <td class="valor">R$ <?=formato_real($valor)?></td>
            <td class="descricao"></td>   
          </tr>
          
      <?php } } } else { ?>
      
      <tr id="descontos">
	    <td colspan="3" align="right"><a href="../../rh_movimentos_1.php?tela=2&clt=<?php echo $clt?>&enc=<?=$link_movimento?>#debito" target="_blank">Adicionar Descontos <img src="seta_transparente.png"></a></td>
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
	    <td class="valor">R$ <?=formato_real($descontos)?></td>
        <td class="descricao">&nbsp;</td>
      </tr>
      <tr class="linha_um">
      	<td class="nome">SAL&Aacute;RIO L&Iacute;QUIDO</td>
	    <td class="valor">R$ <?=formato_real(abs($liquido))?></td>
        <td class="descricao">R$ <?=formato_real($salario)?> + R$ <?=formato_real($rendimentos)?> - R$ <?=formato_real($descontos)?></td>
      </tr>
    </table>
</div>
</body>
</html>