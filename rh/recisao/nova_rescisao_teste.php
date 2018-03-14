<?php 
include('../../conn.php');
include('../../funcoes.php');
include('../../classes/clt.php');
include('../../classes/abreviacao.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../classes/valor_proporcional.php');


// Recebendo a Vari�vel Criptografada
list($regiao,$id_clt,$id) = explode('&',decrypt(str_replace('--','+',$_REQUEST['enc'])));

if($_COOKIE['logado'] == 87){
    
    echo $id;
}
// Consulta da Rescis�o
$qr_rescisao  = mysql_query("SELECT * FROM rh_recisao WHERE id_recisao = '$id' AND status = '1'");
$row_rescisao = mysql_fetch_array($qr_rescisao);

$complementar = ($row_rescisao['rescisao_complementar'] == 1) ? 'COMPLEMENTAR':'' ;

if($row_rescisao['aviso'] == 'trabalhado') {
	
  $tipo_aviso = 'Aviso Pr�vio trabalhado';
} else {
  $tipo_aviso = 'Aviso Pr�vio indenizado';
	
}


// Tipo da Rescis�o
$qr_motivo  = mysql_query("SELECT * FROM rhstatus WHERE codigo = '{$row_rescisao['motivo']}'");
$row_motivo = mysql_fetch_array($qr_motivo);

// Informa��es do Participante
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
// Informa��es da Empresa
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

// Aviso Pr�vio
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
  
  
//Pegando os valores dos moviemntos e inserindo nos campos de acordo com o ANEXO VIII da rescis�o, o n�mero do campo encontra-se na tabela rh_movimento

$sql_mov = "SELECT B.descicao, B.id_mov, A.valor, B.campo_rescisao, B.percentual
                            FROM rh_movimentos_rescisao as A 
                            INNER JOIN
                            rh_movimentos as B
                            ON A.id_mov = B.id_mov
                            WHERE A.id_clt = '$row_rescisao[id_clt]' 
                            AND A.id_rescisao = '$row_rescisao[id_recisao]' 
                            AND A.status = 1";

$qr_movimentos = mysql_query($sql_mov) or die(mysql_error());


while($row_movimentos = mysql_fetch_assoc($qr_movimentos)){       
  
    $movimentos[$row_movimentos['campo_rescisao']] += $row_movimentos['valor'];   
    
    switch($row_movimentos['id_mov']){
        
        case 287:
        case 286:
        case 152:    $hora_extra = $row_movimentos['valor'];  
                     $percentExtra = (!empty($row_movimentos['percentual']) and $row_movimentos['percentual'] != '0.00')? ($row_movimentos['percentual']*100).'%':'';
                     
            break;
      
    }
    
}







echo '<!-- ';
print_r("SELECT * FROM rh_recisao WHERE id_recisao = '$id' AND status = '1'");
echo ' -->';
//echo '<pre>';
//echo '</pre>';
//
//echo '<br>';
//var_dump($row_rescisao['dt_salario']);
//echo '<br>';
//var_dump($row_rescisao['avos_fp']);
//echo '<br>';

      
$saldo_salario = (empty($row_rescisao['saldo_salario']) || ($row_rescisao['saldo_salario'] == '0.00')) ? $movimentos[50] : $row_rescisao['saldo_salario']; //$movimentos[50]
$dt_salario = (empty($row_rescisao['dt_salario']) || ($row_rescisao['dt_salario'] == '0.00')) ? $movimentos[63] : $row_rescisao['dt_salario']; //$movimentos[50]
$ferias_pr = (empty($row_rescisao['ferias_pr']) || ($row_rescisao['ferias_pr'] == '0.00')) ? $movimentos[65] : $row_rescisao['ferias_pr']; //$movimentos[50]
$ferias_vencidas = (empty($row_rescisao['ferias_vencidas']) || ($row_rescisao['ferias_vencidas'] == '0.00')) ? $movimentos[66] : $row_rescisao['ferias_vencidas']; //$movimentos[50]

$umterco = ($row_rescisao['umterco_fv'] + $row_rescisao['umterco_fp']);
$umterco = (empty($umterco) || ($umterco == '0.00')) ? $movimentos[68] : $umterco; //$movimentos[50]
echo '<!-- ';
var_dump($movimentos[107]);
echo ' -->';





$VALORES['CREDITO'][50]['descricao']    = 'Saldo de '.sprintf('%02d', $row_rescisao['dias_saldo']).' dias Sal&aacute;rio';
$VALORES['CREDITO'][50]['valor']        = $row_rescisao['saldo_salario'];

$VALORES['CREDITO'][51]['descricao']   = 'Comiss�es';
$VALORES['CREDITO'][51]['valor']       = $row_rescisao['comissoes'];

$VALORES['CREDITO'][53]['descricao']   = 'Adicional de Insalubridade';
$VALORES['CREDITO'][53]['valor']       = $row_rescisao['insalubridade'];

$VALORES['CREDITO'][57]['descricao']   = 'Gorjetas';
$VALORES['CREDITO'][57]['valor']       = $row_rescisao['gorjetas'];

$VALORES['CREDITO'][60]['descricao']   = 'Multa Art. 477, � 8�/CLT';
$VALORES['CREDITO'][60]['valor']       = $row_rescisao['a477'];

$VALORES['CREDITO'][61]['descricao']   = 'Multa Art. 479/CLT';
$VALORES['CREDITO'][61]['valor']       = $row_rescisao['a479'];

$VALORES['CREDITO'][62]['descricao']   = 'Sal�rio Familia';
$VALORES['CREDITO'][62]['valor']       = $row_rescisao['sal_familia'];

$avos_dt = sprintf('%02d', $row_rescisao['avos_dt']).'/12 avos';
$VALORES['CREDITO'][63]['descricao']   = '13&ordm; Sal&aacute;rio Proporcional '.$avos_dt;
$VALORES['CREDITO'][63]['valor']       = $row_rescisao['dt_salario'];

$VALORES['CREDITO'][64]['descricao']   = '13&ordm; Sal&aacute;rio Exerc&iacute;cio 0/12 avos';
$VALORES['CREDITO'][64]['valor']       = 0;     

if (!empty($row_rescisao['qnt_faltas_ferias'])) {      $faltas_ferias_fp =    "<span style='font-size:11px;'> ( Faltas: {$row_rescisao['qnt_faltas_ferias']})</span>";   } 
$VALORES['CREDITO'][65]['descricao']   = 'F&eacute;rias Proporcionais '.sprintf('%02d', $row_rescisao['avos_fp']).'/12 avos '.$periodo_aquisitivo_fp;
$VALORES['CREDITO'][65]['descricao']  .= $faltas_ferias_fp;
$VALORES['CREDITO'][65]['valor']       = $row_rescisao['ferias_pr'];


$avos_fv   = ($row_rescisao['ferias_vencidas'] != '0.00')? ' 12/12 avos' :'' ;
$faltas_fv = (!empty($row_rescisao['qnt_faltas_ferias_fv'])) ? "( <span style='font-size:11px;'> Faltas:{$row_rescisao['qnt_faltas_ferias_fv']} )</span>" : '' ;
$VALORES['CREDITO'][66]['descricao']   = "F&eacute;rias Vencidas ".$avos_fv.$periodo_aquisitivo_fv.$faltas_fv  ;
$VALORES['CREDITO'][66]['valor']       = $row_rescisao['ferias_vencidas'];


$VALORES['CREDITO'][68]['descricao']   = 'Ter&ccedil;o Constitucional de F&eacute;rias';
$VALORES['CREDITO'][68]['valor']       = $row_rescisao['umterco_fv'] + $row_rescisao['umterco_fp'];

$VALORES['CREDITO'][69]['descricao']   = $tipo_aviso;
$VALORES['CREDITO'][69]['valor']       = $aviso_previo_credito;

$VALORES['CREDITO'][70]['descricao']   = '13� Sal�rio (Aviso-Pr�vio Indenizado)';
$VALORES['CREDITO'][70]['valor']       = $row_rescisao['terceiro_ss'];

$VALORES['CREDITO'][71]['descricao']   = 'F&eacute;rias (Aviso-Pr&eacute;vio Indenizado)';
$VALORES['CREDITO'][71]['valor']       = $row_rescisao['um_avo_ferias_indenizadas'];

$VALORES['CREDITO'][72]['descricao']   = 'F&eacute;rias em dobro';
$VALORES['CREDITO'][72]['valor']       = $row_rescisao['fv_dobro'];

$VALORES['CREDITO'][73]['descricao']   = '1/3 f&eacute;rias em dobro';
$VALORES['CREDITO'][73]['valor']       = $row_rescisao['um_terco_ferias_dobro'];

$VALORES['CREDITO'][82]['descricao']   = 'Ajuda de Custo Art. 470/CLT';
$VALORES['CREDITO'][82]['valor']       = $row_rescisao['ajuda_custo'];

$VALORES['CREDITO'][95]['descricao']   = "Lei 12.506 ({$row_rescisao['qnt_dias_lei_12_506']} dias)";
$VALORES['CREDITO'][95]['valor']       = $row_rescisao['valor_lei_12_506'];

$VALORES['CREDITO'][99]['descricao']   = "Ajuste do Saldo Devedor";
$VALORES['CREDITO'][99]['valor']       = $row_rescisao['arredondamento_positivo'];


 $VALORES['DEBITO'][101]['descricao']   = "Adiantamento Salarial";
 $VALORES['DEBITO'][101]['valor']       = $row_rescisao['adiantamento'];

 $VALORES['DEBITO'][102]['descricao']   = "Adiantamento de 13� Sal�rio";
 $VALORES['DEBITO'][102]['valor']       =  0;

 $VALORES['DEBITO'][103]['descricao']   = "Aviso Pr�vio Indenizado";
 $VALORES['DEBITO'][103]['valor']       = $aviso_previo_debito;

 $VALORES['DEBITO'][104]['descricao']   = " Multa Art. 480/CLT";
 $VALORES['DEBITO'][104]['valor']       = $multa_479;

 $VALORES['DEBITO'][105]['descricao']   = "Empr�stimo em Consigna��o";
 $VALORES['DEBITO'][105]['valor']       = 0;

 $VALORES['DEBITO'][112.1]['descricao']   = "Previd�ncia Social";
 $VALORES['DEBITO'][112.1]['valor']       = $row_rescisao['inss_ss'];

 $VALORES['DEBITO'][112.2]['descricao']   = "Previd�ncia Social - 13� Sal�rio";
 $VALORES['DEBITO'][112.2]['valor']       = $row_rescisao['inss_dt'];

 $VALORES['DEBITO'][114.1]['descricao']   = "IRRF";
 $VALORES['DEBITO'][114.1]['valor']       = $row_rescisao['ir_ss'];
 $VALORES['DEBITO'][114.2]['descricao']   = "IRRF sobre 13� Sal�rio";
 $VALORES['DEBITO'][114.2]['valor']       = $row_rescisao['ir_dt'];





ksort($VALORES['CREDITO']);
ksort($VALORES['DEBITO']);

echo '<pre>';
        print_R($VALORES);
echo '</pre>';




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
    <td colspan="6" class="secao"><h1>TERMO DE RESCIS&Atilde;O <?php echo $complementar;?> DO CONTRATO DE TRABALHO</h1></td>
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
      <div class="valor"><?php echo $row_motivo['especifica'];?></div>
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
    <td><span class="numero">50</span> Saldo de <?php echo sprintf('%02d',$row_rescisao['dias_saldo']); ?> dias Sal&aacute;rio (l&iacute;quido de <?php echo $row_rescisao['faltas']; ?> faltas acrescidas do DSR)</td>
    <td><div class="valor">R$ <?php echo formato_real($saldo_salario); ?></div></td>
    <td><span class="numero">51</span> Comiss&otilde;es</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['comissao']); ?></div></td>
    <td><span class="numero">52</span> Gratifica&ccedil;&otilde;es</td>
    <td><div class="valor">R$ <?php  echo formato_real($movimentos[52])?></div></td>
  </tr>
  <tr>
    <td><span class="numero">53</span> Adicional de Insalubridade</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['insalubridade']); echo $movimentos[53]; ?></div></td>
    <td><span class="numero">54</span> Adicional de Periculosidade</td>
    <td><div class="valor">R$ <?php echo formato_real($movimentos[54] + $row_rescisao['periculosidade']); // periculosidade_30 ?> </div></td>
    <td><span class="numero">55</span> Adicional Noturno <!--0 horas 20%--></td>
    <td><div class="valor">R$ <?php echo formato_real($movimentos[55]); ?></div></td>
  </tr>
  <tr>
    <td><span class="numero">56</span> Horas Extras  <?php echo $percentExtra;?></td>
    <td><div class="valor">R$ <?php echo formato_real($hora_extra); ?>  </div></td>
     <td><span class="numero">57</span> Gorjetas</td>
    <td><div class="valor">R$ 0,00</div></td>
    <td><span class="numero">58</span> Descanso Semanal Remunerado (DSR)</td>
    <td><div class="valor">R$ <?php echo formato_real($movimentos[58]); ?></div></td>
  </tr>
  <tr>
  
    <td><span class="numero">59</span> Reflexo do &quot;DSR&quot; sobre Sal&aacute;rio Vari&aacute;vel</td>
    <td><div class="valor">R$ 0,00</div></td>
     <td><span class="numero">60</span> Multa Art. 477, &sect; 8&ordm;/CLT</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['a477']); ?></div></td>
    <td><span class="numero">61</span> Multa Art. 479/CLT</td>
    <td><div class="valor">R$ <?php echo formato_real($multa_479); ?></div></td>
  </tr>
  <tr>
   
    <td><span class="numero">62</span> Sal&aacute;rio-Fam&iacute;lia</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['sal_familia']); ?></div></td>
    <td><span class="numero">63</span> 13&ordm; Sal&aacute;rio Proporcional <?php echo sprintf('%02d',$row_rescisao['avos_dt']); ?>/12 avos</td>
    <td><div class="valor">R$ <?php echo formato_real($dt_salario); ?></div></td>
    <td><span class="numero">64</span> 13&ordm; Sal&aacute;rio Exerc&iacute;cio 0/12 avos</td>
    <td><div class="valor">R$ 0,00</div></td>
  </tr>
  <tr>
     <td><span class="numero">65</span> F&eacute;rias Proporcionais <?php echo sprintf('%02d',$row_rescisao['avos_fp']); ?>/12 avos</td>
    <td><div class="valor">R$ <?php echo formato_real($ferias_pr); ?></div></td>
    <td><span class="numero">66</span> F&eacute;rias Vencidas <br />
    <?php $qr_historico  = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id_clt' AND status = '1' ORDER BY id_ferias DESC LIMIT 1");
	      $row_historico = mysql_fetch_assoc($qr_historico); ?>
      Per. Aquisitivo de <?php echo formato_brasileiro($row_historico['data_aquisitivo_ini']); ?> <em>&agrave;</em> <?php echo formato_brasileiro($row_historico['data_aquisitivo_fim']); ?> <br />
    <?php if($row_rescisao['ferias_vencidas'] != '0.00') { echo '12'; } else { echo '0'; } ?>/12 avos</td>
    <td><div class="valor">R$ <?php echo formato_real($ferias_vencidas); ?></div></td>
    
     <td><span class="numero">68</span> Ter&ccedil;o Constitucional de F&eacute;rias</td>
    <td><div class="valor">R$ <?php echo formato_real($umterco); ?></div></td>
  </tr>
 
  <tr>
   
    <td><span class="numero">69</span> <?php echo $tipo_aviso; ?></td>
    <td><div class="valor">R$  <?php echo formato_real($aviso_previo_credito); ?></div></td>
    <td><span class="numero">70</span> 13&ordm; Sal&aacute;rio (Aviso-Pr&eacute;vio Indenizado)</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['terceiro_ss']); ?></div></td>
     <td><span class="numero">71</span> F&eacute;rias (Aviso-Pr&eacute;vio Indenizado)</td>
    <td><div class="valor"> R$ <?php echo formato_real($row_rescisao['ferias_aviso_indenizado']); ?></div></td>
  </tr>
  <tr>
   
    <td><span class="numero">72</span> F&eacute;rias em dobro</td>
    <td><div class="valor"> R$ <?php echo  formato_real($row_rescisao['fv_dobro'])?></div></td>
    <td><span class="numero">73</span> 1/3 f&eacute;rias em dobro</td>
    <td><div class="valor">R$ <?php echo  formato_real($row_rescisao['um_terco_ferias_dobro'])?></div></td>
     <td><span class="numero">80</span> Diferen&ccedil;a Salarial</td>
    <td>
       
    <div class="valor">R$ <?php echo formato_real($movimentos[80]); ?></div></td>
 
  </tr>
  
  <tr>
    <td><span class="numero">82</span> Ajuda de Custo Art. 470/CLT</td>
    <td><div class="valor">R$ <?php echo formato_real($movimentos[82]); ?></div></td>
    <td><span class="numero">99</span> Ajuste do Saldo Devedor</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['arredondamento_positivo']); ?></div></td>
    <td><span class="numero">82</span>1/3 F�rias (Aviso Pr�vio Indenizado)</td>
    <td><div class="valor">R$ <?php echo formato_real($row_rescisao['umterco_ferias_aviso_indenizado']); ?></div></td>
      
  </tr>
   
    
  <tr>     
    <?php if(!empty($row_rescisao['lei_12_506'])) { ?>
        <td><span class="numero">95</span> Lei 12.506</td>
        <td><div class="valor">R$ <?php echo formato_real($row_rescisao['lei_12_506']); ?></div></td>          
   <?php } else { ?>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    <?php } ?>   
        
     <?php if(!empty($movimentos[200])){ ?>   
     <td><span class="numero">95 </span>  Diferen�a de Diss�dio</td>
     <td ><div class="valor"> R$ <?php echo formato_real($movimentos[200]); ?></div></td>
     <?php } else {
         echo '<td>&nbsp;</td>
                <td>&nbsp;</td>';

                 } 
            ?>    
    <?php if(!empty($row_rescisao['aux_distancia'])){ ?>   
     <td><span class="numero">107  </span> <?php if($row_rescisao['id_regiao'] == 48) { echo 'Aux�lio Dist�ncia'; } else  { echo 'Vale Transporte' ;} ?></td>
     <td ><div class="valor"> R$ <?php echo formato_real($movimentos[107]); ?></div></td>
     <?php } else {
         echo '<td>&nbsp;</td>
    <td>&nbsp;</td>';
         
     }  ?>
  </tr>   
     <tr>
       <?php if(!empty($movimentos[108])){ ?>   
     <td><span class="numero">108 </span> Vale Refei��o</td>
     <td ><div class="valor"> R$ <?php echo formato_real($movimentos[108]); ?></div></td>
     <?php } else {
         echo '<td>&nbsp;</td>
                <td>&nbsp;</td>';
         
     } ?>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td class="secao">TOTAL RESCIS&Oacute;RIO BRUTO</td>
        <td class="secao"><div class="valor">R$ <?php echo formato_real($row_rescisao['total_rendimento']); ?></div></td>
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
        <td><span class="numero">109</span> Vale Alimenta��o</td>
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
        <td><span class="numero">115.<?php echo $i++; ?></span>Adiantamento de 13� Sal�rio</td>
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
    <?php if($_COOKIE['logado'] == 179){ echo $row_rescisao['um_ano']; }  ?>
    
   <?php if($row_rescisao['um_ano'] >= 1 ) { ?>
    
 <table  class="rescisao" cellpadding="0" cellspacing="1" style="page-break-before: always; margin-top:20px; ">
  <tr>
    <td colspan="6" class="secao"><h1>TERMO DE HOMOLOGA��O DE RESCIS�O DO CONTRATO DE TRABALHO</h1></td>
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
      <div class="campo"><span class="numero">17</span> 17 CTPS (n�, s�rie, UF)</div>
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
      <div class="campo"><span class="numero">29</span>Pens�o Aliment�cia (%) (FGTS)</div>
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
          Foi prestada, gratuitamente, assist&ecirc;ncia na rescis�o do contrato de trabalho, nos termos do art. 477, &sect; 1&ordm;,
          da Consolida&ccedil;&atilde;o das Leis do Trabalho (CLT), sendo comprovado, neste ato, o efetivo pagamento das verbas rescis&oacute;rias
          acima especificadas no corpo do TRCT, no valor l�quido de R$ <?php echo formato_real($row_rescisao['total_liquido']); ?>, o qual devidamente rubricado pelas partes, � parte integrante
          do presente Termo de Homologa��o. <br />
          </p>
        <p>As partes assistidas no presente ato de rescis�o contratual foram identificadas como legitimas conforme previsto na Instru��o Normativa/SRT n� 15/2010</p>
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
           152 Assinatura do Respons�vel Legal do Trabalhador
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
           154 Nome do �rg�o Homologador
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
           <div class="campo"><span class="numero">156</span> Informa��es � CAIXA</div> 
           <p>&nbsp;</p>
        
      </td>
  </tr>   
  <tr>
      <td colspan="6">
           <p style="text-align:center;">
            <strong> ASSIST�NCIA NO ATO DE RESCIS�O CONTRATUAL � GRATUITA.</strong><bR>
           Pode o trabalhador iniciar a��o judicial quanto aos cr�ditos resultantes das rela��es de trabalho at� o limite de dois anos ap�s a extin��o do contrato de trabalho (inciso XXIX, art. 7� da Constitui��o Federal/1988).
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
      <div class="campo"><span class="numero">17</span> CTPS (n�, s�rie, UF)</div>
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
        <div class="campo"><span class="numero">29</span> Pens�o Aliment�cia (%) (FGTS)</div>
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
         <p> Foi realizada a rescis�o do contrato de trabalho do trabalhador acima qualificado, nos termos do artigo n� 477 da 
        Consolida��o das Leis do Trabalho (CLT). A assist�ncia � rescis�o prevista no �1� do art. n� 477 da CLT n�o � devida, 
        tendo em vista a dura��o do contrato de trabalho n�o ser superior a um ano de servi�o e n�o existir previs�o de 
        assist�ncia � rescis�o contratual em Acordo ou Conven��o Coletiva de Trabalho da categoria a qual pertence o 
        trabalhador.</p>
       <p> No dia <?php echo implode('/',array_reverse(explode('-',$row_rescisao['data_demi'])))?> foi realizado, nos termos do art. 23 da Instru��o Normativa/SRT n� 15/2010, o efetivo pagamento das 
           verbas rescis�rias especificadas no corpo do TRCT, no valor l�quido de R$ <?php echo number_format($row_rescisao['total_liquido'],2,',','.');?> ,o qual, devidamente rubricado pelas partes, � parte integrante do 
        presente Termo de Quita��o.</p>
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
           152 Assinatura do Respons�vel Legal do Trabalhador
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
            <strong> ASSIST�NCIA NO ATO DE RESCIS�O CONTRATUAL � GRATUITA.</strong><bR>
           Pode o trabalhador iniciar a��o judicial quanto aos cr�ditos resultantes das rela��es de trabalho at� o limite de dois anos ap�s a extin��o do contrato de trabalho (inciso XXIX, art. 7� da Constitui��o Federal/1988).
          </p>
      </td>
  </tr>
</table>
    
    
<?php  } ?>
</body>
</html>