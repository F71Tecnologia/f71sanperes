<?php
// Incluindo Arquivos
require('../../../conn.php');
require_once ('../../../classes/LogClass.php');
include('../../../classes/calculos.php');
include('../../../classes/calculos_new.php');
include('../../../classes/formato_valor.php');
include('../../../classes/formato_data.php');
include('../../../classes/EventoClass.php');
include('../../../classes/FeriasClass.php');
include('../../../classes/RescisaoClass.php');
include('../../../classes/MovimentoClass.php');
include('../../../classes/CalculoFolhaClass.php');
include('../../../classes/valor_proporcional.php');
include('../../../funcoes.php');

// Definindo Classe Cálculos
$Calc = new calculos();
$Trab = new proporcional();
$objFerias = new Ferias();
$objEvento = new Eventos();
$objRescisao = new Rescisao();



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

///movimentos
$objMovimento = new Movimentos();
$objMovimento->carregaMovimentos($ano);


//clase de calculos da folha
$objCalcFolha = new Calculo_Folha();
$objCalcFolha->CarregaTabelas($ano);

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
$qr_participante  = mysql_query("SELECT * FROM rh_folha_proc WHERE id_clt = '$clt' AND id_folha = '$folha' AND status = '2'");
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
 //BUSCANDO INFORMAÇÃO DO MOVIMENTOS  
$qr_movimentos = mysql_query("SELECT * FROM rh_movimentos WHERE mov_lancavel = 1 OR id_mov IN(259,258)");
while($row_movimento = mysql_fetch_assoc($qr_movimentos)){
 
    $INF_MOVIMENTOS[$row_movimento['id_mov']]['cod'] = $row_movimento['cod'];
    $INF_MOVIMENTOS[$row_movimento['id_mov']]['categoria'] = $row_movimento['categoria'];
    $INF_MOVIMENTOS[$row_movimento['id_mov']]['descicao'] = $row_movimento['descicao'];

}       

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
                            $rendimentos_listados[]     = $movimento['id_movimento'];
                            $rendimentos_nome[]         = $movimento['nome_movimento'];
                            $rendimentos_id_mov[]       = $movimento['id_mov'];
                            $rendimentos_qnt[]          = $movimento['qnt'];
                            $rendimentos_tipo_qnt[]     = $movimento['tipo_qnt'];
                            $rendimentos_qntHoras[]     = $movimento['qnt_horas'];
                            $rendimentos_valor[]        = $movimento['valor_movimento'];
                    }
                    
                    
			
		} elseif($movimento['tipo_movimento'] == 'DEBITO' or $movimento['tipo_movimento'] == 'DESCONTO') {
			
                    if(!in_array($movimento['id_mov'], $descontos_listados)) {
                            $descontos_listados[] = $movimento['id_movimento'];				
                            $descontos_nome[]     = $movimento['nome_movimento'];
                            $descontos_id_mov[]   = $movimento['id_mov'];
                            $descontos_qnt[]      = $movimento['qnt'];
                            $descontos_tipo_qnt[] = $movimento['tipo_qnt'];
                            $descontos_qntHoras[] = $movimento['qnt_horas'];
                            $descontos_valor[]    = $movimento['valor_movimento'];
                    }
	
		}
	
	}
        
        

}
	   
// Organizado as Arrays pelo Nome
array_multisort($rendimentos_nome, $rendimentos_qnt, $rendimentos_tipo_qnt, $rendimentos_valor, $descontos_qntHoras);
if ($_COOKIE['debug'] == 666) {
            echo '<pre>';
            print_r([$rendimentos_id_mov,$rendimentos_qntHoras,$rendimentos_valor]);
            echo '</pre>';

        }
//array_multisort($descontos_nome, $descontos_qnt, $descontos_tipo_qnt, $descontos_valor);


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
     
    <table cellpadding="0" cellspacing="0" id="relatorio" style="margin-bottom: 10px;">
      <tr id="salario" style="border:0px;">
	    <td colspan="2">SAL&Aacute;RIO</td>
	    <td><a href="../../alter_clt.php?clt=<?=$clt?>&pro=<?=$projeto?>&folha" target="_blank">Editar Cadastro <img src="seta_transparente.png"></a></td>
	  </tr>
      <tr class="linha_um">
        <td class="nome">SAL&Aacute;RIO CONTRATUAL</td>
        <td class="valor">R$ <?=formato_real($salario_limpo)?></td>
        <td class="descricao"><?=@mysql_result($qr_curso, 0, 1)?></td>    
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
        <?php 
            $mensagem = "";
            if(!empty($dias_entrada)) {
                  $mensagem .= "FOI CONTRATADO EM ".formato_brasileiro($row_clt['data_entrada'])." </br>";
            } 
            
            if(!empty($sinaliza_evento)) {
                  $mensagem .= "FICOU $dias_evento DIAS DE LICENÇA "."<br>PERÍODO: ".$msg_evento . "</br>";
            } 
            
            if(!empty($dias_ferias)) {
                  $mensagem .= "TEVE $dias_ferias DIAS DE FÉRIAS </br>";
            } 
            
            if(!empty($dias_rescisao)) {
                  $mensagem .= "FOI RESCINDIDO EM ".formato_brasileiro($row_clt['data_saida'])."</br>";
            } 
            
            echo $mensagem;
        ?>
        </td>
      </tr>
      <tr class="linha_dois">
        <td class="nome">SAL&Aacute;RIO</td>
        <td class="valor">R$ <?=formato_real($salario)?></td>
        <td class="descricao">
            <?php 
            /*29-08-16 -> Ramon = add if para exibir qnt_horas se tiver */
            if(!empty($row_clt['quantidade_plantao']) && $row_clt['quantidade_plantao'] != 0){ 
                echo "R$ " . number_format($row_clt['valor_fixo_plantao'],2,',','.') . " x " . $row_clt['quantidade_plantao'] . " Plantões "; 
            }elseif(!empty($row_clt['quantidade_horas']) && $row_clt['quantidade_horas'] != 0){ 
                echo "R$ " . number_format($row_clt['valor_hora'],2,',','.') . " x " . $row_clt['quantidade_horas'] . " Horas "; 
            }else{
                echo "R$ " .formato_real($valor_dia)." x ". $dias . "dias";  
            } ?></td>   
      </tr>
      
      <?php $rendimentos += $familia;
	  		if(!empty($rendimentos)) { ?>
            
	  <tr id="rendimentos">
	    <td colspan="2">RENDIMENTOS</td>
            <td><?php if ($_COOKIE['logado'] != 395) { ?><a href="../../rh_movimentos_3.php?tela=2&clt=<?php echo $clt?>&regiao=<?=$regiao?>&projeto=<?=$regiao?>&folha#credito" target="_blank">Gerenciar Rendimentos <img src="seta_transparente.png"></a><?php } ?></td>
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
        <td class="valor" colspan="2">R$ <?=formato_real($valor_ferias)?><?php echo " " .$legenda_ferias; ?></td>
             
      </tr>
      <?php } if(!empty($valor_rescisao)) { ?>
      <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">RESCIS&Atilde;O</td>
        <td class="valor">R$ <?=formato_real($valor_rescisao)?></td>
        <td class="descricao"></td>    
      </tr>
      <?php } 

	 foreach($rendimentos_valor as $chave => $valor) {
		if(!empty($valor)) { 
                    if ($rendimentos_id_mov[$chave] == 66 && $rendimentos_qntHoras[$chave] > 0) {
                        
                        $qntHoras = explode(":",$rendimentos_qntHoras[$chave]);
                        $qntHoras = "{$qntHoras[0]}:{$qntHoras[1]}";
//                        $nome = $rendimentos_nome[$chave];
                        $nome = "{$rendimentos_nome[$chave]} ({$qntHoras} Horas)";
                        
                    } else {
                        $nome = $rendimentos_nome[$chave];
                    }
                    ?>
          
          <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
            <td class="nome"><?=$nome?></td>
            <td class="valor">R$ <?=formato_real($valor)?></td>
            <td class="descricao"></td>   
          </tr>
          
      <?php } } } else { ?>
      
      <tr id="rendimentos">
            <td colspan="3"><?php if ($_COOKIE['logado'] != 395) { ?><a href="../../rh_movimentos_3.php?tela=2&clt=<?php echo $clt?>&regiao=<?=$regiao?>&projeto=<?=$regiao?>&folha#credito" target="_blank">Adicionar Rendimentos <img src="seta_transparente.png"></a><?php } ?></td>
	  </tr>
      
     <?php }
           
            if($_COOKIE['logado'] == 179){
               echo "Valores dentro de Relatorio: <br>";
               echo "INSS Completo: " . $inss_completo . "<br>";
               echo "IR Completo: " . $irrf_completo . "<br>";
           }          
           
           $descontos += $inss_completo + $irrf_completo;
           if(!empty($descontos) or !empty($row_clt['desconto_inss'])) { ?>
	  
	  <tr id="descontos">
	    <td colspan="2">DESCONTOS*</td>
           <td><?php if ($_COOKIE['logado'] != 395) { ?><a href="../../rh_movimentos_1.php?tela=2&clt=<?php echo $clt?>&regiao=<?=$regiao?>&projeto=<?=$regiao?>&folha#debito" target="_blank">Gerenciar Descontos <img src="seta_transparente.png"></a><?php } ?></td>
      </tr>
       <?php if(!empty($sindicato)) { ?>
      <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">CONTRIBUI&Ccedil;&Atilde;O SINDICAL</td>
        <td class="valor">R$ <?=formato_real($sindicato)?></td>
        <td class="descricao"></td>    
      </tr>
       <?php } if((!empty($inss) and $inss != '0.00') or !empty($row_clt['desconto_inss'])) { ?>
      <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">INSS</td>
        <td class="valor">R$ <?=formato_real($inss)?></td>
        <td class="descricao">
        <?php if($row_clt['tipo_desconto_inss'] == 'isento') { ?>
        	INSS recolhido em outra empresa
        <?php } elseif($row_clt['tipo_desconto_inss'] == 'parcial') { ?>
                INSS parcialmente recolhido em outra empresa (R$ <?php if($vInssParcial > 0){echo $vInssParcial; } ?>)
        <?php } else { ?>
        	R$ <?=formato_real($base_inss)?> x <?=$percentual_inss?>% <?php echo $legendaSinistra; ?>
        <?php } ?> 
        <?php if(!empty($legendaDescontaOutraEmpresa)){ echo $legendaDescontaOutraEmpresa; } ?>        
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
        <td class="valor" colspan="2">R$ <?=formato_real($desconto_ferias)?><?php echo " " .$legenda_desconto_ferias; ?></td>
          
      </tr>
      <?php } if(!empty($inss_ferias) && $InfoFerias['mes'] == $InfoFerias['mes_ferias'] && $InfoFerias['ano'] == $InfoFerias['ano_ferias']) { ?>
        <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
          <td class="nome">INSS SOBRE F&Eacute;RIAS</td>
          <td class="valor" colspan="2">R$ <?=formato_real($inss_ferias)?><?php echo " ". $legenda_inss_ferias; ?></td>
            
        </tr>
      <?php } if(!empty($irrf_ferias) and $irrf_ferias != '0.00' && $InfoFerias['mes'] == $InfoFerias['mes_ferias'] && $InfoFerias['ano'] == $InfoFerias['ano_ferias']) { ?>
      <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">IRFF SOBRE F&Eacute;RIAS</td>
        <td class="valor" colspan="2">R$ <?=formato_real($irrf_ferias)?><?php echo " " . $legenda_ir_ferias; ?></td>
         
      </tr>
      <?php } if(!empty($desconto_rescisao)) { ?>
      <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td class="nome">VALOR PAGO NA RESCIS&Atilde;O</td>
        <td class="valor">R$ <?=formato_real($liquido_rescisao)?></td>
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
      
      if($clt == 7168){ ?>
          <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
            <td class="nome">INSS DE FÉRIAS</td>
            <td class="valor">R$ 513,01</td>
            <td class="descricao"></td>   
          </tr>
      <?php  }
	      
        foreach($descontos_valor as $chave => $valor) {
			    	if(!empty($valor)) { ?>
          
          <tr class="linha_<?php if($linha2++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
            <td class="nome"><?=$descontos_nome[$chave]?></td>
            <td class="valor">R$ <?=formato_real($valor)?></td>
            
            <?php 
            
                if(!empty($descontos_qnt[$chave]) or !empty($descontos_qntHoras[$chave])){
                    if($descontos_tipo_qnt[$chave] ==1){
                        $descricao =  substr($descontos_qntHoras[$chave],0, 5).' Horas';
                    }elseif($descontos_tipo_qnt[$chave] == 2){
                          $descricao= $descontos_qnt[$chave].' dias';
                    }
                    
                }
            ?>
            
            <td class="descricao"><?php if($descricao != "0 dias"){ echo $descricao; } ?></td>   
         
          
          </tr>
          
      <?php } } } else { ?>
      
      <tr id="descontos">
            <td colspan="3"><?php if ($_COOKIE['logado'] != 395) { ?><a href="../../rh_movimentos_1.php?tela=2&clt=<?php echo $clt?>&regiao=<?=$regiao?>&projeto=<?=$regiao?>&folha#debito" target="_blank">Adicionar Descontos <img src="seta_transparente.png"></a><?php } ?></td>
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
      
      <tr class="linha_dois">
      	<td class="nome">SAL&Aacute;RIO L&Iacute;QUIDO</td>
	    <td class="valor">R$ <?=formato_real($liquido)?></td>
        <td class="descricao">R$ <?=formato_real($salario)?> + R$ <?=formato_real($rendimentos)?> - R$ <?=formato_real($descontos - $valorPensaoExibirPopUp) ?> 
            <?php if(isset($arrayPensoes) && !empty($arrayPensoes)){ ?>
                    <?php foreach ($arrayPensoes as $key => $values){ ?>
                        <?php echo " - R$ " . number_format($values['valor'],2,',','.'); ?>
                    <?php } ?>
            <?php } ?>
        </td>
      </tr>
      <tr class="linha_um">
          <td colspan="3" style="line-height: 13px; color: #fe0000; font-weight: bold; font-size: 12px; text-transform: uppercase; text-align: center;"><?php echo (!empty($msgErrorPensao))?$msgErrorPensao:""; ?></td>
      </tr>
    </table>
</div>
</body>
</html>