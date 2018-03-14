<?php 
include('../../conn.php');
include('../../funcoes.php');
include('../../classes/clt.php');
include('../../classes/abreviacao.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../classes/valor_proporcional.php');



// Recebendo a Variável Criptografada
list($regiao,$id_clt,$id) = explode('&',decrypt(str_replace('--','+',$_REQUEST['enc'])));

// Consulta da Rescisão
$qr_rescisao  = mysql_query("SELECT * FROM rh_recisao WHERE id_recisao = '$id' AND status = '1'");
$row_rescisao = mysql_fetch_array($qr_rescisao);

if($row_rescisao['aviso'] == 'trabalhado') {	
  $tipo_aviso = 'Aviso Prévio trabalhado';
} else {
  $tipo_aviso = 'Aviso Prévio indenizado';
	
}


// Tipo da Rescisão
$qr_motivo  = mysql_query("SELECT * FROM rhstatus WHERE codigo = '$row_rescisao[motivo]'");
$row_motivo = mysql_fetch_array($qr_motivo);

// Informações do Participante
$Clt           = new clt();
$Clt           -> MostraClt($id_clt);
$pis 		   = $Clt -> pis;
$nome 		   = $Clt -> nome;
$codigo 	   = $Clt -> campo3;
$endereco 	   = $Clt -> endereco;
$bairro	 	   = $Clt -> bairro;
$cidade 	   = $Clt -> cidade;
$uf		 	   = $Clt -> uf;
$cep	 	   = $Clt -> cep;
$cartrab 	   = $Clt -> campo1;
$serie_cartrab = $Clt -> serie_ctps;
$uf_cartrab    = $Clt -> uf_ctps;
$cpf	 	   = $Clt -> cpf;
$data_nasci	   = $Clt -> data_nasci;
$mae	 	   = $Clt -> mae;
$data_entrada  = $Clt -> data_entrada;
$data_demi	   = $Clt -> data_demi;
$rh_sindicato  = $Clt -> rh_sindicato;
$id_projeto_clt = $Clt -> id_projeto;

// Sindicato do Participante
$qr_sindicato  = mysql_query("SELECT * FROM rhsindicato WHERE id_sindicato = '$rh_sindicato'");
$row_sindicato = mysql_fetch_assoc($qr_sindicato);

$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]' ");
$row_master = mysql_fetch_assoc($qr_master);
/*
$cnpj 		= $row_master['cnpj'];
$razao 		= $row_master['razao'];
$logradouro = $row_master['logradouro'];
$municipio  = $row_master['municipio'];
$uf 		= $row_master['uf'];
$cep_ 		= $row_master['cep'];
$bairro 	= $row_master['bairro'];
*/


// Informações da Empresa


/////CONDIÇÂO LAGOS 
if($row_regiao['id_master'] == 6){


		$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$regiao' AND id_projeto = '$id_projeto_clt'");
		$row_empresa = mysql_fetch_assoc($qr_empresa);
		
		
		$cnpj_empresa 		= $row_empresa['cnpj'];
		$razao_empresa 		= $row_empresa['razao'];		
		$logradouro = explode('-',$row_empresa['endereco']);		
		$endereco_empresa   =  $logradouro[0];
		$municipio_empresa  = $logradouro[2];
		$uf_empresa		 = $logradouro[3];
		$cep_empresa = $row_empresa['cep'];
		$bairro_empresa 	 = $logradouro[1];
 
}else {

$Clt             -> EmpresadoCLT($id_clt);
$cnpj_empresa 	          = $Clt -> cnpj;
$razao_empresa 	          = $Clt -> razao;
$endereco_empresa = $Clt -> endereco;
$cep_empresa      = $Clt -> cep;

list($endereco_empresa,$bairro_empresa,$municipio_empresa,$uf_empresa) = explode(' - ',$endereco_empresa);


}

// Aviso Prévio
if($row_rescisao['fator'] == 'empregado' and $row_rescisao['aviso'] == 'indenizado') {
	$aviso_previo_debito  = $row_rescisao['aviso_valor'];
} else {
	$aviso_previo_credito = $row_rescisao['aviso_valor'];
}

/*
// Multa de Atraso
if( $row_rescisao['motivo'] != '64' and $row_rescisao['motivo'] != '65') {
	$multa_479 = $row_rescisao['a479'];
} else {
	$multa_480 = $row_rescisao['a479'];
}


*/

if( $row_rescisao['motivo'] == '64'){
	$multa_479 = $row_rescisao['a479'];
	$multa_480 = NULL;
} elseif($row_rescisao['motivo'] == '63') {
    
    $multa_479 = NULL;
    $multa_480 = $row_rescisao['a479'];
}




  
$qr_movimentos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$row_rescisao[id_clt]' AND mes_mov = 16 AND status = 1");
while($row_movimentos = mysql_fetch_assoc($qr_movimentos)){
    
    switch($row_movimentos['id_mov']){
        
        //REEMBOLSO VALE TRANSPORTE
        case 204:
        case 151: $reembolso_vale_refeicao = $row_movimentos['valor_movimento']; 
            break;
        
        //GRATIFICAÇÔES
        case 197:
        case 196:
        case 192: $gratificacao += $row_movimentos['valor_movimento']; 
            break;
        
        //DIFERENÇA SALARIAL
        case 14: $diferenca_salarial = $row_movimentos['valor_movimento']; 
            break;        
    }

    


}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Rescis&atilde;o de <?php echo $id_clt.' - '.$nome; ?></title>
<link href="rescisao.css" rel="stylesheet" type="text/css" />
</head>
<body>
<table id="rescisao" cellpadding="0" cellspacing="1">
  <tr>
    <td colspan="6" class="secao"><h1>TERMO DE RESCIS&Atilde;O DO CONTRATO DE TRABALHO</h1></td>
  </tr>
  <tr>
    <td colspan="6" class="secao">IDENTIFICA&Ccedil;&Atilde;O DO EMPREGADOR</td>
  </tr>
  <tr>
    <td colspan="2">
      <div class="campo"><span class="numero">01</span> CNPJ/CEI</div>
      <div class="valor"><?php echo $cnpj_empresa;?></div>
    </td>
    <td colspan="4">
      <div class="campo"><span class="numero">02</span> Raz&atilde;o Social/Nome</div>
      <div class="valor"><?php echo $razao_empresa; ?></div>
    </td>
  </tr>
  <tr>
    <td colspan="4">
      <div class="campo"><span class="numero">03</span> Endere&ccedil;o (logradouro, n&ordm;, andar, apartamento)</div>
      <div class="valor"><?php echo $endereco_empresa; ?></div>
    </td>
    <td colspan="2">
      <div class="campo"><span class="numero">04</span> Bairro</div>
      <div class="valor"><?php echo $bairro_empresa;?></div>
    </td>
  </tr>
  <tr>
    <td>
      <div class="campo"><span class="numero">05</span> Munic&iacute;pio</div>
      <div class="valor"><?php echo $municipio_empresa;?></div>
    </td>
    <td>
      <div class="campo"><span class="numero">06</span> UF</div>
      <div class="valor"><?php echo  $uf_empresa; ?></div>
    </td>
    <td>
      <div class="campo"><span class="numero">07</span> CEP</div>
      <div class="valor"><?php echo $cep_empresa;  ?></div>
    </td>
    <td>
      <div class="campo"><span class="numero">08</span> CNAE</div>
      <div class="valor">&nbsp;</div>
    </td>
    <td colspan="2">
      <div class="campo"><span class="numero">09</span> CNPJ/CEI Tomador/Obra</div>
      <div class="valor">&nbsp;</div>
    </td>
  </tr>
  <tr>
    <td colspan="6" class="secao">IDENTIFICA&Ccedil;&Atilde;O DO TRABALHADOR</td>
  </tr>
  <tr>
    <td colspan="2">
      <div class="campo"><span class="numero">10</span> PIS/PASEP</div>
      <div class="valor"><?php echo $pis; ?></div>
    </td>
    <td colspan="4">
      <div class="campo"><span class="numero">11</span> Nome</div>
      <div class="valor"><?php echo $nome; ?></div>
    </td>
  </tr>
  <tr>
    <td colspan="4">
      <div class="campo"><span class="numero">12</span> Endere&ccedil;o (logradouro, n&ordm;, andar, apartamento)</div>
      <div class="valor"><?php echo $endereco; ?></div>
    </td>
    <td colspan="2">
      <div class="campo"><span class="numero">13</span> Bairro</div>
      <div class="valor"><?php echo $bairro; ?></div>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <div class="campo"><span class="numero">14</span> Munic&iacute;pio</div>
      <div class="valor"><?php echo $cidade; ?></div>
    </td>
    <td>
      <div class="campo"><span class="numero">15</span> UF</div>
      <div class="valor"><?php echo $uf; ?></div>
    </td>
    <td>
      <div class="campo"><span class="numero">16</span> CEP</div>
      <div class="valor"><?php echo $cep; ?></div>
    </td>
    <td colspan="2">
      <div class="campo"><span class="numero">17</span> Carteira de Trabalho (n&ordm;, s&eacute;rie, UF)</div>
      <div class="valor"><?php echo $cartrab.' / '.$serie_cartrab.' / '.$uf_cartrab; ?></div>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <div class="campo"><span class="numero">18</span> CPF</div>
      <div class="valor"><?php echo $cpf; ?></div>
    </td>
    <td>
      <div class="campo"><span class="numero">19</span> Data de nascimento</div>
      <div class="valor"><?php echo formato_brasileiro($data_nasci); ?></div>
    </td>
    <td colspan="3">
      <div class="campo"><span class="numero">20</span> Nome da m&atilde;e</div>
      <div class="valor"><?php echo $mae; ?></div>
    </td>
  </tr>
  <tr>
    <td colspan="6" class="secao">DADOS DO CONTRATO</td>
  </tr>
  <tr>
    <td colspan="3">
      <div class="campo"><span class="numero">21</span> Tipo de Contrato</div>
      <div class="valor">1. Contrato de Trabalho por Prazo Indeterminado</div>
    </td>
    <td colspan="3">
      <div class="campo"><span class="numero">22</span> Causa do Afastamento</div>
      <div class="valor"><?php echo $row_motivo['especifica']; ?></div>
    </td>
  </tr>
  <tr>
    <td colspan="3">
      <div class="campo"><span class="numero">23</span> Remunera&ccedil;&atilde;o M&ecirc;s Anterior Afast.</div>
      <div class="valor">R$ <?php echo formato_real($row_rescisao['sal_base']); ?></div>
    </td>
    <td>
      <div class="campo"><span class="numero">24</span> Data de admiss&atilde;o</div>
      <div class="valor"><?php echo formato_brasileiro($data_entrada); ?></div>
    </td>
    <td>
      <div class="campo"><span class="numero">25</span> Data do Aviso Pr&eacute;vio</div>
      <div class="valor"><?php echo formato_brasileiro($row_rescisao['data_aviso']); ?></div>
    </td>
    <td>
      <div class="campo"><span class="numero">26</span> Data de afastamento</div>
      <div class="valor"><?php echo formato_brasileiro($data_demi); ?></div>
    </td>
  </tr>
  <tr>
    <td colspan="3">
      <div class="campo"><span class="numero">27</span> C&oacute;d. afastamento</div>
      <div class="valor"><?php echo sprintf('%02d',$row_rescisao['fgts_saque']); ?></div>
    </td>
    <td>
      <div class="campo"><span class="numero">28</span> Pens&atilde;o Aliment&iacute;cia (%) (TRCT)</div>
      <div class="valor">0,00%</div>
    </td>
    <td>
      <div class="campo"><span class="numero">29</span> Pens&atilde;o aliment&iacute;cia (%) (Saque FGTS)</div>
      <div class="valor">0,00%</div>
    </td>
    <td>
      <div class="campo"><span class="numero">30</span> Categoria do trabalhador</div>
      <div class="valor">01</div>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <div class="campo"><span class="numero">31</span> C&oacute;digo Sindical</div>
      <div class="valor"><?php echo $row_sindicato['codigo_sindical']; ?></div>
    </td>
    <td colspan="4">
      <div class="campo"><span class="numero">32</span> CNPJ e Nome da Entidade Sindical Laboral</div>
      <div class="valor"><?php echo $row_sindicato['cnpj'].' - '.substr($row_sindicato['nome'],0,52); ?></div>
    </td>
  </tr>
  <tr>
    <td colspan="6" class="secao">DISCRIMINA&Ccedil;&Atilde;O DAS VERBAS RESCIS&Oacute;RIAS</td>
  </tr>
  <tr>
    <td colspan="6" class="secao">VERBAS RESCIS&Oacute;RIAS</td>
  </tr>
  <tr>
    <td width="17%" class="secao_filho">Rubrica</td>
    <td width="16%" class="secao_filho">Valor</td>
    <td width="17%" class="secao_filho">Rubrica</td>
    <td width="16%" class="secao_filho">Valor</td>
    <td width="17%" class="secao_filho">Rubrica</td>
    <td width="16%" class="secao_filho">Valor</td>
  </tr>
  <tr>
    <td><span class="numero">50</span> Saldo de <?php echo sprintf('%02d',$row_rescisao['dias_saldo']); ?> dias Sal&aacute;rio (l&iacute;quido de <?php echo $row_rescisao['faltas']; ?> faltas acrescidas do DSR)</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['saldo_salario']); ?></div></td>
    <td><span class="numero">51</span> Comiss&otilde;es</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['comissao']); ?></div></td>
    <td><span class="numero">52</span> Gratifica&ccedil;&otilde;es</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['gratificacao']); ?></div></td>
  </tr>
  <tr>
    <td><span class="numero">53</span> Adicional de Insalubridade</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['insalubridade']); ?></div></td>
    <td><span class="numero">54</span> Adicional de Periculosidade</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['periculosidade']); ?></div></td>
    <td><span class="numero">55</span> Adicional Noturno <!--0 horas 20%--></td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['adicional_noturno']); ?></div></td>
  </tr>
  <tr>
    <td><span class="numero">56</span> Horas Extras 0 horas 0,00%</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['extra']); ?></div></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><span class="numero">57</span> Gorjetas</td>
    <td><div class="valor">R$ 0,00</div></td>
    <td><span class="numero">58</span> Descanso Semanal Remunerado (DSR)</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['dsr']); ?></div></td>
    <td><span class="numero">59</span> Reflexo do &quot;DSR&quot; sobre Sal&aacute;rio Vari&aacute;vel</td>
    <td><div class="valor">R$ 0,00</div></td>
  </tr>
  <tr>
    <td><span class="numero">60</span> Multa Art. 477, &sect; 8&ordm;/CLT</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['a477']); ?></div></td>
    <td><span class="numero">61</span> Multa Art. 479/CLT</td>
    <td><div class="valor">R$ <?php echo formato_real($multa_479); ?></div></td>
    <td><span class="numero">62</span> Sal&aacute;rio-Fam&iacute;lia</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['sal_familia']); ?></div></td>
  </tr>
  <tr>
    <td><span class="numero">63</span> 13&ordm; Sal&aacute;rio Proporcional <?php echo sprintf('%02d',$row_rescisao['avos_dt']); ?>/12 avos</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['dt_salario']); ?></div></td>
    <td><span class="numero">64</span> 13&ordm; Sal&aacute;rio Exerc&iacute;cio 0/12 avos</td>
    <td><div class="valor">R$ 0,00</div></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><span class="numero">65</span> F&eacute;rias Proporcionais <?php echo sprintf('%02d',$row_rescisao['avos_fp']); ?>/12 avos</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['ferias_pr']); ?></div></td>
    <td><span class="numero">66</span> F&eacute;rias Vencidas <br />
    <?php $qr_historico  = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id_clt' AND status = '1' ORDER BY id_ferias DESC LIMIT 1");
	      $row_historico = mysql_fetch_assoc($qr_historico); ?>
      Per. Aquisitivo de <?php echo formato_brasileiro($row_historico['data_aquisitivo_ini']); ?> <em>&agrave;</em> <?php echo formato_brasileiro($row_historico['data_aquisitivo_fim']); ?> <br />
    <?php if($row_rescisao['ferias_vencidas'] != '0.00') { echo '12'; } else { echo '0'; } ?>/12 avos</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['ferias_vencidas']); ?></div></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
    
    
  <tr>
    <td><span class="numero">68</span> Ter&ccedil;o Constitucional de F&eacute;rias</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['umterco_fv'] + $row_rescisao['umterco_fp']); ?></div></td>
    <td><span class="numero">69</span> <?php echo $tipo_aviso; ?></td>
    <td><div class="valor">R$  <?php echo formato_real($aviso_previo_credito); ?></div></td>
    <td><span class="numero">70</span> 13&ordm; Sal&aacute;rio (Aviso-Pr&eacute;vio Indenizado)</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['terceiro_ss']); ?></div></td>
  </tr>
  <tr>
    <td><span class="numero">71</span> F&eacute;rias (Aviso-Pr&eacute;vio Indenizado)</td>
    <td><div class="valor">R$ 0,00</div></td>
    <td><span class="numero">72</span> F&eacute;rias em dobro</td>
    <td><div class="valor"> R$ <?php echo  formato_real($row_rescisao['fv_dobro'])?></div></td>
    <td><span class="numero">73</span> 1/3 f&eacute;rias em dobro</td>
    <td><div class="valor">R$ <?php echo  formato_real($row_rescisao['um_terco_ferias_dobro'])?></div></td>
    
  </tr>
  
  <tr>  
    <td><span class="numero">80</span> Diferen&ccedil;a Salarial</td>
    <td>
   <?php 
   //  BACALHAU para mostrar os movimentos de DIFERENÇA SALARIAL
    ?>
    
    
        <div class="valor">R$ <?php    echo formato_real($diferenca_salarial);  ?></div>
    </td>
    <td><span class="numero">82</span> Ajuda de Custo Art. 470/CLT</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['ajuda_custo']); ?></div></td>
     <td><span class="numero">99</span> Ajuste do Saldo Devedor</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['arredondamento_positivo']); ?></div></td>
  </tr>
    
    <tr>
      <?php if(!empty($row_rescisao['aux_distancia'])){ ?>   
     <td><span class="numero">106 </span> <?php if($row_rescisao['id_regiao'] == 48) { echo 'Auxílio Distância'; } else  { echo 'Vale Transporte' ;} ?></td>
     <td ><div class="valor"> R$ <?php echo formato_real($row_rescisao['aux_distancia']); ?></div></td>
     <?php } else {
         echo '<td>&nbsp;</td>
    <td>&nbsp;</td>';
         
     }    
     if(!empty($reembolso_vale_refeicao)){ ?>   
     <td><span class="numero">109 </span> Vale Refeição</td>
     <td ><div class="valor"> R$ <?php echo formato_real($reembolso_vale_refeicao); ?></div></td>
     <?php } else {
         echo '<td>&nbsp;</td>
    <td>&nbsp;</td>';
         
     } ?>
    <td>&nbsp;</td>
    <td>&nbsp;</td> 
    
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><span class="numero">99</span> Ajuste do Saldo Devedor</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['arredondamento_positivo']); ?></div></td>
    <td class="secao">TOTAL RESCIS&Oacute;RIO BRUTO</td>
    <td class="secao"><div class="valor">R$ <?php echo formato_real($row_rescisao['total_rendimento']+ $pog); ?></div></td>
  </tr>
    
  <tr>
    <td colspan="6" class="secao">DEDU&Ccedil;&Otilde;ES</td>
  </tr>
  <tr>
    <td class="secao_filho">Desconto</td>
    <td class="secao_filho">Valor</td>
    <td class="secao_filho">Desconto</td>
    <td class="secao_filho">Valor</td>
    <td class="secao_filho">Desconto</td>
    <td class="secao_filho">Valor</td>
  </tr>
  <tr>
    <td><span class="numero">100</span> Pens&atilde;o Aliment&iacute;cia</td>
    <td><div class="valor">R$ 0,00</div></td>
    <td><span class="numero">101</span> Adiantamento Salarial</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['adiantamento']); ?></div></td>
    <td><span class="numero">102</span> Adiantamento de 13&ordm; Sal&aacute;rio</td>
    <td><div class="valor">R$ 0,00</div></td>
  </tr>
  <tr>
    <td><span class="numero">103</span> Aviso-Pr&eacute;vio Indenizado</td>
    <td><div class="valor">R$ <?php echo formato_real($aviso_previo_debito); ?></div></td>
    <td><span class="numero">104</span> Multa Art. 480/CLT</td>
    <td><div class="valor">R$ <?php echo formato_real($multa_480); ?></div></td>
    <td><span class="numero">105</span> Empr&eacute;stimo em Consigna&ccedil;&atilde;o</td>
    <td><div class="valor">R$ 0,00</div></td>
  </tr>
  <tr>
     <td><span class="numero">109</span> Vale Alimentação</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['desconto_vale_alimentacao']); ?></div></td>
    <td><span class="numero">112.1</span> Previd&ecirc;ncia Social</td>
    <td><div class="valor">R$ <?php if($row_rescisao['id_clt']  == 5046){ echo '329,43';} else { echo formato_real($row_rescisao['previdencia_ss']);} ?></div></td>
    <td><span class="numero">112.2</span> Previd&ecirc;ncia Social - 13&ordm; Sal&aacute;rio</td>
    <td><div class="valor">R$ <?php   echo formato_real($row_rescisao['previdencia_dt']); ?></div></td>
   
  </tr>
  <tr>
    <td><span class="numero">114.1</span> IRRF</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['ir_ss']); ?></div></td>
    <td><span class="numero">114.2</span> IRRF sobre 13&ordm; Sal&aacute;rio</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['ir_dt']); ?></div></td>     
    <td><span class="numero">115</span> Devolu&ccedil;&atilde;o de Cr&eacute;dito Indevido</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['devolucao']); ?></div></td>
  </tr>
     <tr>
        <td><span class="numero">116</span> IRRF F&eacute;rias</td>
   	<td><div class="valor">R$ <?php echo formato_real($row_rescisao['ir_ferias']); ?></div></td>       
     <td><span class="numero">117</span> Faltas</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['valor_faltas']); ?></div></td>   
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td class="secao">TOTAL DAS DEDU&Ccedil;&Otilde;ES</td>
    <td class="secao">R$ <?php if($id_clt == '3881') { echo formato_real($row_rescisao['total_deducao'] + '2168.06'); } 
                     else if($row_rescisao['id_clt']  == 5046){ echo '6.270,25';} else { echo formato_real($row_rescisao['total_deducao'] ); } ?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td class="secao">VALOR RESCIS&Oacute;RIO L&Iacute;QUIDO</td>
    <td class="secao">R$ <?php if($id_clt == '3881') { echo formato_real($row_rescisao['total_rendimento'] - ($row_rescisao['total_deducao'] + '2168.06')); } 
                                elseif($id_clt == 5046){  echo '7.598,00';} 
    else  { echo formato_real($row_rescisao['total_liquido']); } ?></td> 
  </tr>
  <tr>
    <td colspan="6" class="secao">FORMALIZA&Ccedil;&Atilde;O DA RESCIS&Atilde;O</td>
  </tr>
  <tr>
    <td colspan="3">
      <div class="campo"><span class="numero">150</span> Local e data do recebimento</div>
      <p>&nbsp;</p>
    </td>
    <td colspan="3">
      <div class="campo"><span class="numero">151</span> Carimbo e assinatura do empregador ou preposto</div>
      <p>&nbsp;</p>
    </td>
  </tr>
  <tr>
    <td colspan="3">
      <div class="campo"><span class="numero">152</span> Assinatura do trabalhador</div>
      <p>&nbsp;</p>
    </td>
    <td colspan="3">
      <div class="campo"><span class="numero">153</span> Assinatura do respons&aacute;vel legal do trabalhador</div>
      <p>&nbsp;</p>
    </td>
  </tr>
  <tr>
    <td colspan="4">
      <div class="campo">
        <p><span class="numero">154</span> HOMOLOGA&Ccedil;&Atilde;O<br />
          Foi prestada, gratuitamente, assist&ecirc;ncia ao trabalhador, nos termos do art. 477, &sect; 1&ordm;, da Consolida&ccedil;&atilde;o das Leis do Trabalho &ndash; CLT, sendo comprovado, neste ato, o efetivo pagamento das verbas rescis&oacute;rias acima especificadas.<br />
          </p>
        <p>&nbsp;</p>
        <p>________________________________________________<br />
          Local e data<br />
        </p>
        <p>&nbsp;</p>
        <p>________________________________________________<br />
          Carimbo e assinatura do assistente </p>
      </div>
    </td>
    <td valign="top">
      <div class="campo">
        <span class="numero">155</span> Digital do trabalhado
      </div>
    </td>
    <td valign="top">
      <div class="campo">
        <span class="numero">156</span> Digital do respons&aacute;vel legal
      </div>
    </td>
  </tr>
  <tr>
    <td colspan="4">
      <div class="campo">
          <span class="numero">157</span> Identifica&ccedil;&atilde;o do &oacute;rg&atilde;o homologador
          <p>&nbsp;</p>
      </div>
    </td>
    <td colspan="2">
      <div class="campo">
        <span class="numero">158</span> Recep&ccedil;&atilde;o pelo Banco (data e carimbo)
        <p>&nbsp;</p>
      </div>
    </td>
  </tr>
  <tr>
    <td colspan="6" class="secao">A ASSIST&Ecirc;NCIA NO ATO DE RESCIS&Atilde;O CONTRATUAL &Eacute; GRATUITA.<br />
      Pode o trabalhador iniciar a&ccedil;&atilde;o judicial quanto aos cr&eacute;ditos resultantes das rela&ccedil;&otilde;es de trabalho at&eacute; o limite de dois anos ap&oacute;s a extin&ccedil;&atilde;o do contrato de trabalho (Inc. XXIX, Art. 7&ordm; da Constitui&ccedil;&atilde;o Federal/1988).
    </td>
  </tr>
  <!-- ADICIONADO POR MAIKOM EM 16/09/2011. -->
  <tr>
  	<td align="center" colspan="6">
    	<?php 
			$qr_clt = mysql_query("SELECT agencia,conta,banco FROM rh_clt WHERE id_clt = '{$id_clt}' LIMIT 1");
			$row_clt = mysql_fetch_array($qr_clt);		
				
			$nome_banco = @mysql_result(mysql_query("SELECT nome FROM bancos WHERE id_banco = '$row_clt[banco]' "),0);
			
			echo "Banco :	";
			echo $nome_banco;
			echo '<br>';
			echo " Agência :	";
			echo $row_clt[0];
			echo " Conta : ";
			echo $row_clt[1];
		?>
    </td>
  </tr>
</table>
</body>
</html>