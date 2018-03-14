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

if($row_regiao['id_master'] == 6){
// Informações da Empresa
$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$regiao' AND id_projeto = '$id_projeto_clt'");
$row_empresa = mysql_fetch_assoc($qr_empresa);

$cnpj_empresa       = $row_empresa['cnpj'];
$razao_empresa 	    =  $row_empresa['razao'];		
$logradouro         = explode('-',$row_empresa['endereco']);		
$endereco_empresa   =  $logradouro[0];
$municipio_empresa  = $row_empresa['cidade'];
$uf_empresa         = $row_empresa['uf'];
$cep_empresa        = $row_empresa['cep'];
$bairro_empresa     = $row_empresa['bairro'];
$cnae               = $row_empresa['cnae'];

} else {

$Clt             -> EmpresadoCLT($id_clt);
$cnpj_empresa 	          = $Clt -> cnpj;
$razao_empresa 	          = $Clt -> razao;
$endereco_empresa = $Clt -> endereco;
$cep_empresa      = $Clt -> cep;

list($endereco_empresa,$bairro_empresa,$municipio_empresa,$uf_empresa) = explode(' - ',$endereco_empresa);

}

// Aviso Prévio
if( $row_rescisao['motivo']== 65) {
	$aviso_previo_debito  = $row_rescisao['aviso_valor'];
} else {
	$aviso_previo_credito = $row_rescisao['aviso_valor'];
}


$cod_sindicato = (empty($row_sindicato['codigo_sindical']))? "999.000.000.00000-3" : $row_sindicato['codigo_sindical'] ;



// Multa de Atraso

if( $row_rescisao['motivo'] == '64'){
	$multa_479 = $row_rescisao['a479'];
	$multa_480 = NULL;
} elseif($row_rescisao['motivo'] == '63') {    
    $multa_479 = NULL;
    $multa_480 = $row_rescisao['a480'];
}
  
  
//Pegando os valores dos moviemntos e inserindo nos campos de acordo com o ANEXO VIII da rescisão, o número do campo encontra-se na tabela rh_movimento
$qr_movimentos = mysql_query("SELECT B.descicao, B.id_mov, A.valor, B.campo_rescisao
                            FROM rh_movimentos_rescisao as A 
                            INNER JOIN
                            rh_movimentos as B
                            ON A.id_mov = B.id_mov
                            WHERE A.id_clt = '$row_rescisao[id_clt]' 
                            AND A.id_rescisao = '$row_rescisao[id_recisao]' 
                            AND A.status = 1") or die(mysql_error());


while($row_movimentos = mysql_fetch_assoc($qr_movimentos)){       
  
 
    $movimentos[$row_movimentos['campo_rescisao']] += $row_movimentos['valor'];   
    
    
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Rescis&atilde;o de <?php echo $id_clt.' - '.$nome; ?></title>
<link href="rescisao_1.css" rel="stylesheet" type="text/css" />
<style type="text/css" media="print">
table.rescisao td.secao {
	background-color:#C0C0C0;
	text-align:center;
	font-size:14px;
	height:20px;
}

</style>

</head>
<body>
<table class="rescisao" cellpadding="0" cellspacing="1">
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
      <div class="valor"><?php echo $cnae;   ?></div>
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
      <div class="valor"><?php echo $row_motivo['causa_afastamento'];?></div>
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
      <div class="valor"><?php echo $row_motivo['codigo_afastamento'];?></div>
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
      <div class="valor"><?php echo $cod_sindicato; ?></div>
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
    <td><span class="numero">50</span> Saldo de        <?php echo sprintf('%02d',$row_rescisao['dias_saldo']); ?> dias Sal&aacute;rio (l&iacute;quido de <?php echo $row_rescisao['faltas']; ?> faltas acrescidas do DSR)</td>
    <td><div class="valor">R$  <input name="saldo_salario" id="saldo_salario" type="text" value="<?php echo formato_real($row_rescisao['saldo_salario']); ?>"/></div></td>
    <td><span class="numero">51</span> Comiss&otilde;es</td>
    <td><div class="valor"> R$ <input name="comissao" id="comissao" type="text" value="<?php echo formato_real($row_rescisao['comissao']); ?>"/> </div></td>
    <td><span class="numero">52</span> Gratifica&ccedil;&otilde;es</td>
    <td><div class="valor">R$ <input name="gratificacao" id="gratificacao" type="text" value="<?php echo formato_real($row_rescisao['gratificacao']); ?>"/> </div></td>
  </tr>
  <tr>
    <td><span class="numero">53</span> Adicional de Insalubridade</td>
    <td><div class="valor">R$ <input name="insalubridade" id="insalubridade" type="text" value="<?php echo formato_real($row_rescisao['insalubridade']); ?>"/></div></td>
    <td><span class="numero">54</span> Adicional de Periculosidade</td>
    <td><div class="valor">R$ <input name="periculosidade" id="periculosidade" type="text" value="<?php echo formato_real($movimentos[54]); ?>"/></div></td>
    <td><span class="numero">55</span> Adicional Noturno <!--0 horas 20%--></td>
    <td><div class="valor">R$ <input name="adicional_noturno" id="adicional_noturno" type="text" value="<?php echo formato_real($movimentos[55]); ?>"/></div></td>
  </tr>
  <tr>
    <td><span class="numero">56</span> Horas Extras 0 horas 0,00%</td>
    <td><div class="valor">R$ <input name="hora_extra" id="hora_extra" type="text" value="<?php echo formato_real($movimentos[56]); ?>"/>  </div></td>
     <td><span class="numero">57</span> Gorjetas</td>
    <td><div class="valor">R$ 0,00</div></td>
    <td><span class="numero">58</span> Descanso Semanal Remunerado (DSR)</td>
    <td><div class="valor">R$ <input name="dsr" id="dsr" type="text" value="<?php echo formato_real($movimentos[58]); ?>"/>  </div></td>
  </tr>
  <tr>
  
    <td><span class="numero">59</span> Reflexo do &quot;DSR&quot; sobre Sal&aacute;rio Vari&aacute;vel</td>
    <td><div class="valor">R$ 0,00</div></td>
     <td><span class="numero">60</span> Multa Art. 477, &sect; 8&ordm;/CLT</td>
    <td><div class="valor">R$ <input name="a477" id="a477" type="text" value="<?php echo formato_real($row_rescisao['a477']); ?>"/></div></td>
    <td><span class="numero">61</span> Multa Art. 479/CLT</td>
    <td><div class="valor">    R$ <input name="a479" id="a479" type="text" value="<?php echo formato_real($multa_479); ?>"/>  </div></td>
  </tr>
  <tr>
   
    <td><span class="numero">62</span> Sal&aacute;rio-Fam&iacute;lia</td>
    <td><div class="valor">R$ <input name="familia" id="familia" type="text" value="<?php echo formato_real($row_rescisao['sal_familia']); ?>"/> </div></td>
    <td><span class="numero">63</span> 13&ordm; Sal&aacute;rio Proporcional <?php echo sprintf('%02d',$row_rescisao['avos_dt']); ?>/12 avos</td>
    <td><div class="valor">R$ <input name="dt_salario" id="dt_salario" type="text" value="<?php echo formato_real($row_rescisao['dt_salario']); ?>"/> </div></td>
    <td><span class="numero">64</span> 13&ordm; Sal&aacute;rio Exerc&iacute;cio 0/12 avos</td>
    <td><div class="valor">R$ 0,00</div></td>
  </tr>
  <tr>
     <td><span class="numero">65</span> F&eacute;rias Proporcionais <?php echo sprintf('%02d',$row_rescisao['avos_fp']); ?>/12 avos</td>
    <td><div class="valor">R$ <input name="ferias_proporcionais" id="ferias_proporcionais" type="text" value="<?php echo formato_real($row_rescisao['ferias_pr']); ?>"/></div></td>
    <td><span class="numero">66</span> F&eacute;rias Vencidas <br />
    <?php $qr_historico  = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id_clt' AND status = '1' ORDER BY id_ferias DESC LIMIT 1");
	      $row_historico = mysql_fetch_assoc($qr_historico); ?>
      Per. Aquisitivo de <?php echo formato_brasileiro($row_historico['data_aquisitivo_ini']); ?> <em>&agrave;</em> <?php echo formato_brasileiro($row_historico['data_aquisitivo_fim']); ?> <br />
    <?php if($row_rescisao['ferias_vencidas'] != '0.00') { echo '12'; } else { echo '0'; } ?>/12 avos</td>
    
    <td><div class="valor">R$ <input name="ferias_vencidas" id="ferias_vencidas" type="text" value="<?php echo formato_real($row_rescisao['ferias_vencidas']); ?>"/> </div></td>
    
     <td><span class="numero">68</span> Ter&ccedil;o Constitucional de F&eacute;rias</td>
    <td><div class="valor">R$ <input name="terco_constitucional" id="terco_constitucional" type="text" value="<?php echo formato_real($row_rescisao['umterco_fv'] + $row_rescisao['umterco_fp']); ?>"/> </div></td>
  </tr>
 
  <tr>
   
    <td><span class="numero">69</span> <?php echo $tipo_aviso; ?></td>
    <td><div class="valor">R$ <input name="aviso_previo_credito" id="aviso_previo_credito" type="text" value="<?php echo formato_real($aviso_previo_credito); ?>"/> </div></td>
    <td><span class="numero">70</span> 13&ordm; Sal&aacute;rio (Aviso-Pr&eacute;vio Indenizado)</td>
    <td><div class="valor">R$ <input name="13_aviso_indenizado" id="13_aviso_indenizado" type="text" value="<?php echo formato_real($row_rescisao['terceiro_ss']); ?>"/></div></td>
     <td><span class="numero">71</span> F&eacute;rias (Aviso-Pr&eacute;vio Indenizado)</td>
    <td><div class="valor"> R$ <input name="ferias_aviso_indenizado" id="ferias_aviso_indenizado" type="text" value="<?php echo formato_real($row_rescisao['ferias_aviso_indenizado']); ?>"/></div></td>
  </tr>
  <tr>
   
    <td><span class="numero">72</span> F&eacute;rias em dobro</td>
    <td><div class="valor"> R$ <input name="ferias_em_dobro" id="ferias_em_dobro" type="text" value="<?php echo formato_real($row_rescisao['fv_dobro']); ?>"/></div></td>
    <td><span class="numero">73</span> 1/3 f&eacute;rias em dobro</td>
    <td><div class="valor">R$ <input name="um_terco_ferias_dobro" id="um_terco_ferias_dobro" type="text" value="<?php echo formato_real($row_rescisao['um_terco_ferias_dobro']); ?>"/></div></td>
    <td><span class="numero">80</span> Diferen&ccedil;a Salarial</td>
    <td>      
    <div class="valor">R$ <input name="diferenca_salarial" id="diferenca_salarial" type="text" value="<?php echo formato_real($movimentos[80]); ?>"/></div></td>
 
  </tr>
  
  <tr>
    <td><span class="numero">82</span> Ajuda de Custo Art. 470/CLT</td>
    <td><div class="valor">R$ <input name="ajuda_custo" id="ajuda_custo" type="text" value="<?php echo formato_real($movimentos[82]); ?>"/></div></td>
    <td><span class="numero">99</span> Ajuste do Saldo Devedor</td>
    <td><div class="valor">R$ <input name="arredondamento_positivo" id="arredondamento_positivo" type="text" value="<?php echo formato_real($rescisao['arredondamento_positivo']); ?>"/></div></td>
     <td><span class="numero">95</span> Lei 12.506</td>
        <td><div class="valor">R$ <input name="lei_12_506" id="lei_12_506" type="text" value="<?php echo formato_real($rescisao['lei_12_506']); ?>"/><div></td>         
  
  </tr>
   
    
  <tr>
   
     <td><span class="numero">107  </span> <?php if($row_rescisao['id_regiao'] == 48) { echo 'Auxílio Distância'; } else  { echo 'Vale Transporte' ;} ?></td>
     <td ><div class="valor"> R$ <input name="vale_refeicao" id="vale_refeicao" type="text" value="<?php echo formato_real($movimentos['108']); ?>"/> </div></td>
     <td><span class="numero">108 </span> Vale Refeição</td>
     <td ><div class="valor"> R$ <input name="vale_refeicao" id="vale_refeicao" type="text" value="<?php echo formato_real($movimentos['108']); ?>"/></div></td>
    <td class="secao">TOTAL RESCIS&Oacute;RIO BRUTO</td>
    <td class="secao"><div class="valor">R$ <input name="tota_rendimento" id="tota_rendimento" type="text" value="<?php echo formato_real($row_rescisao['total_rendimento']); ?>"/> </div></td>
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
    <td><div class="valor">R$  <?php echo formato_real($movimentos[100]); ?></div></td>
    <td><span class="numero">101</span> Adiantamento Salarial</td>
    <td><div class="valor">R$ <?php echo formato_real($movimentos[101]); ?></div></td>
    <td><span class="numero">102</span> Adiantamento de 13&ordm; Sal&aacute;rio</td>
    <td><div class="valor">R$ 0,00</div></td>
  </tr>
  <tr>
    <td><span class="numero">103</span> Aviso-Pr&eacute;vio Indenizado</td>
    <td><div class="valor">R$ <?php echo formato_real($aviso_previo_debito); ?></div></td>
    <td><span class="numero">104</span> Multa Art. 480/CLT</td>
    <td><div class="valor">R$ <?php echo @formato_real($multa_480); ?></div></td>
    <td><span class="numero">105</span> Empr&eacute;stimo em Consigna&ccedil;&atilde;o</td>
    <td><div class="valor">R$ 0,00</div></td>
  </tr>
    
    <tr>
        <td><span class="numero">106</span> Vale Transporte</td>
        <td><div class="valor">R$ <?php echo formato_real($movimentos[106]);?></div></td>
        <td><span class="numero">109</span> Vale Alimentação</td>
        <td><div class="valor">R$ <?php echo formato_real($movimentos[109]); ?></div></td>
        
     <td><span class="numero">112.1</span> Previd&ecirc;ncia Social</td>
    <td><div class="valor">R$ <?php if($row_rescisao['id_clt']  == 5046){ echo '329,43';} else { echo formato_real($row_rescisao['inss_ss']);} ?></div></td>
   
    </tr>
        
    
  <tr>   
    <td><span class="numero">112.2</span> Previd&ecirc;ncia Social - 13&ordm; Sal&aacute;rio</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['inss_dt']); ?></div></td>
    <td><span class="numero">114.1</span> IRRF</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['ir_ss']); ?></div></td>
     <td><span class="numero">114.2</span> IRRF sobre 13&ordm; Sal&aacute;rio</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['ir_dt']); ?></div></td>  
  </tr>
  <tr>
        <?php if(!empty($row_rescisao['devolucao'])) { ?>
        <td><span class="numero">115<?php echo $i++; ?></span> Devolu&ccedil;&atilde;o de Cr&eacute;dito Indevido</td>
        <td><div class="valor">R$ <?php echo formato_real($row_rescisao['devolucao']); ?></div></td>        
        <?php } else {
            echo '   <td>&nbsp;</td>
                     <td>&nbsp;</td>';
        } ?>
        <td><span class="numero">115.<?php echo $i++; ?></span> Outros</td>
        <td><div class="valor">R$ <?php echo formato_real($movimentos[115]); ?></div></td>
        
        <?php if(!empty($row_rescisao['adiantamento_13'])) { ?>
        <td><span class="numero">115.<?php echo $i++; ?></span>Adiantamento de 13º Salário</td>
        <td><div class="valor">R$ <?php echo formato_real($row_rescisao['adiantamento_13']); ?></div></td>        
        <?php } else {
            echo '   <td>&nbsp;</td>
                     <td>&nbsp;</td>';
        } ?>
        
    
       
  </tr>
    <tr>
        <td><span class="numero">117</span> Faltas</td>
        <td><div class="valor">R$ <?php echo formato_real($row_rescisao['valor_faltas'] + $movimentos[117]); ?></div></td>
        <td><span class="numero">116</span> IRRF F&eacute;rias</td>
        <td><div class="valor">R$ <?php echo formato_real($row_rescisao['ir_ferias']); ?></div></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>

  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td class="secao">TOTAL DAS DEDU&Ccedil;&Otilde;ES</td>
    <td class="secao">R$ <?php if($id_clt == '3881') { echo formato_real($row_rescisao['total_deducao'] + '2168.06'); } else { echo formato_real($row_rescisao['total_deducao']); } ?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td class="secao">VALOR RESCIS&Oacute;RIO L&Iacute;QUIDO</td>
    <td class="secao">R$ <?php if($id_clt == '3881') { echo formato_real($row_rescisao['total_rendimento'] - ($row_rescisao['total_deducao'] + '2168.06')); } else { echo formato_real($row_rescisao['total_liquido']); } ?></td>
  </tr>
  
</table>
    
    
   <?php if($row_rescisao['um_ano'] >= 1 ) { ?>
    
 <table  class="rescisao" cellpadding="0" cellspacing="1" style="page-break-before: always; margin-top:20px; ">
  <tr>
    <td colspan="6" class="secao"><h1>TERMO DE HOMOLOGAÇÃO DE RESCISÃO DO CONTRATO DE TRABALHO</h1></td>
  </tr>     
     <tr>
    <td colspan="6" class="secao">EMPREGADOR</td>
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
     
    <td colspan="6" class="secao">TRABALHADOR</td>
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
      <td colspan="2">
      <div class="campo"><span class="numero">17</span> 17 CTPS (nº, série, UF)</div>
      <div class="valor"><?php echo $cartrab.' / '.$serie_cartrab.' / '.$uf_cartrab; ?></div>
    </td>
  
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
        <td colspan="6" class="secao">CONTRATO</td>
    </tr>

<tr>   
  <td colspan="6">
      <div class="campo"><span class="numero">22</span> Causa do Afastamento</div>
      <div class="valor"><?php echo $row_motivo['especifica']; ?></div>
    </td>
  </tr>
  <tr>    
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
      <td>
      <div class="campo"><span class="numero">27</span> C&oacute;d. afast.</div>
      <div class="valor"><?php echo $row_motivo['codigo_afastamento'];?></div>
    </td>  
      <td colspan="2">
      <div class="campo"><span class="numero">29</span>Pensão Alimentícia (%) (FGTS)</div>
      <div class="valor">0,00%</div>
    </td>  
  </tr>
  
  <tr>  
   <td colspan="6">
      <div class="campo"><span class="numero">30</span> Categoria do trabalhador</div>
      <div class="valor">01</div>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <div class="campo"><span class="numero">31</span> C&oacute;digo Sindical</div>
      <div class="valor"><?php echo $cod_sindicato; ?></div>
    </td>
    <td colspan="4">
      <div class="campo"><span class="numero">32</span> CNPJ e Nome da Entidade Sindical Laboral</div>
      <div class="valor"><?php echo $row_sindicato['cnpj'].' - '.substr($row_sindicato['nome'],0,52); ?></div>
    </td>
  </tr>
  
  <tr style="border: 0px;">
    <td colspan="6" style="border: 0px;">
      <div class="campo">
          Foi prestada, gratuitamente, assist&ecirc;ncia na rescisão do contrato de trabalho, nos termos do art. 477, &sect; 1&ordm;,
          da Consolida&ccedil;&atilde;o das Leis do Trabalho (CLT), sendo comprovado, neste ato, o efetivo pagamento das verbas rescis&oacute;rias
          acima especificadas no corpo do TRCT, no valor líquido de R$ <?php echo formato_real($row_rescisao['total_liquido']); ?>, o qual devidamente rubricado pelas partes, é parte integrante
          do presente Termo de Homologação. <br />
          </p>
        <p>As partes assistidas no presente ato de rescisão contratual foram identificadas como legitimas conforme previsto na Instrução Normativa/SRT nº 15/2010</p>
        <p>Fico ressalvado o direito de o trabalhador pleitear judicialmente os direitos informados no camppo 155, abaixo.</p>
        
        <p>____________________/___, ____ de _______________________ de _______. </p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>___________________________________________________________</br>
           150 Assinatura do Empregador ou Preposto
        </p>
      </div>
    </td>   
  </tr>
  
  <tr style="border: 0px;">
      <td colspan="3" style="border: 0px;">
        <p>&nbsp;</p>
        <p>&nbsp;</p>
           <p>___________________________________________________________</br>
            151 Assinatura do Trabalhador
        </p>
          
      </td>
      <td colspan="3" style="border: 0px;">
        <p>&nbsp;</p>
        <p>&nbsp;</p> 
          <p>___________________________________________________________</br>
           152 Assinatura do Responsável Legal do Trabalhador
        </p>
      </td>
  </tr>
  
   <tr style="border: 0px;">
      <td colspan="3"  style="border: 0px;">
            <p>&nbsp;</p>
        <p>&nbsp;</p>
           <p>___________________________________________________________</br>
           153 Carimbo e Assinatura do Assistente
        </p>
          
      </td>
      <td colspan="3"  style="border: 0px;">
            <p>&nbsp;</p>
        <p>&nbsp;</p>
           <p>___________________________________________________________</br>
           154 Nome do Órgão Homologador
        </p>
      </td>
  </tr>
  <tr>
      <td colspan="6" >   <div class="campo"><span class="numero">155</span> Ressalvas</div> 
       <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>      
      </td>      
  </tr>
  <tr>
      <td colspan="6">
           <div class="campo"><span class="numero">156</span> Informações à CAIXA</div> 
           <p>&nbsp;</p>
        
      </td>
  </tr>   
  <tr>
      <td colspan="6">
           <p style="text-align:center;">
            <strong> ASSISTÊNCIA NO ATO DE RESCISÃO CONTRATUAL É GRATUITA.</strong><bR>
           Pode o trabalhador iniciar ação judicial quanto aos créditos resultantes das relações de trabalho até o limite de dois anos após a extinção do contrato de trabalho (inciso XXIX, art. 7º da Constituição Federal/1988).
          </p>
      </td>
  </tr>
</table>
    
    <?php } else {  ?>
    
    <table  class="rescisao" cellpadding="0" cellspacing="1" style="page-break-before: always; margin-top:20px; ">
  <tr>
      <td colspan="6" class="secao"><h1>TERMO DE  QUITA&Ccedil;&Atilde;O DE RESCIS&Atilde;O DO CONTRATO DE TRABALHO</h1></td>
  </tr>     
     <tr>
    <td colspan="6" class="secao">EMPREGADOR</td>
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
    <td colspan="6" class="secao">TRABALHADOR</td>
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
      <td colspan="2">
      <div class="campo"><span class="numero">17</span> CTPS (nº, série, UF)</div>
      <div class="valor"><?php echo $cartrab.' / '.$serie_cartrab.' / '.$uf_cartrab; ?></div>
    </td>
  
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
        <td colspan="6" class="secao">CONTRATO</td>
    </tr>
    <tr>   
      <td colspan="6">
          <div class="campo"><span class="numero">22</span> Causa do Afastamento</div>
          <div class="valor"><?php echo $row_motivo['especifica']; ?></div>
        </td>
       </tr>
    <tr>    
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
        <td>
        <div class="campo"><span class="numero">27</span> C&oacute;d. Afast.</div>
        <div class="valor"><?php echo $row_motivo['codigo_afastamento'];?></div>
      </td>  
        <td colspan="2">
        <div class="campo"><span class="numero">29</span> Pensão Alimentícia (%) (FGTS)</div>
        <div class="valor">0,00%</div>
      </td>  
    </tr>
    <tr>  
    <td colspan="6">
       <div class="campo"><span class="numero">30</span> Categoria do trabalhador</div>
       <div class="valor">01</div>
     </td>
   </tr>
  <tr style="border: 0px;">
    <td colspan="6" style="border: 0px;">
      <div class="campo">
         <p> Foi realizada a rescisão do contrato de trabalho do trabalhador acima qualificado, nos termos do artigo nº 477 da 
        Consolidação das Leis do Trabalho (CLT). A assistência à rescisão prevista no §1º do art. nº 477 da CLT não é devida, 
        tendo em vista a duração do contrato de trabalho não ser superior a um ano de serviço e não existir previsão de 
        assistência à rescisão contratual em Acordo ou Convenção Coletiva de Trabalho da categoria a qual pertence o 
        trabalhador.</p>
       <p> No dia <?php echo implode('/',array_reverse(explode('-',$row_rescisao['data_demi'])))?> foi realizado, nos termos do art. 23 da Instrução Normativa/SRT nº 15/2010, o efetivo pagamento das 
           verbas rescisórias especificadas no corpo do TRCT, no valor líquido de R$ <?php echo number_format($row_rescisao['total_liquido'],2,',','.');?> ,o qual, devidamente rubricado pelas partes, é parte integrante do 
        presente Termo de Quitação.</p>
        <br />
        <p>____________________/___, ____ de _______________________ de _______. </p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>___________________________________________________________</br>
           150 Assinatura do Empregador ou Preposto
        </p>
      </div>
    </td>   
  </tr>
  <tr style="border: 0px;">
      <td colspan="3" style="border: 0px;">
        <p>&nbsp;</p>
        <p>&nbsp;</p>
           <p>___________________________________________________________</br>
            151 Assinatura do Trabalhador
       </p>
      </td>
      <td colspan="3" style="border: 0px;">
        <p>&nbsp;</p>
        <p>&nbsp;</p> 
          <p>___________________________________________________________</br>
           152 Assinatura do Responsável Legal do Trabalhador
        </p>
      </td>
  </tr> 
  <tr style="border: 0px; height: 300px;">
      <td colspan="6" style="border: 0px;">   
       
      </td>      
  </tr>
  <tr>
      <td colspan="6">
           <p style="text-align:center;">
            <strong> ASSISTÊNCIA NO ATO DE RESCISÃO CONTRATUAL É GRATUITA.</strong><bR>
           Pode o trabalhador iniciar ação judicial quanto aos créditos resultantes das relações de trabalho até o limite de dois anos após a extinção do contrato de trabalho (inciso XXIX, art. 7º da Constituição Federal/1988).
          </p>
      </td>
  </tr>
</table>
    
    
<?php  } ?>
</body>
</html>