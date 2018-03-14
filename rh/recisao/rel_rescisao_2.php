<?php
if(empty($_COOKIE['logado'])) {
	print "<script>location.href = '../login.php?entre=true';</script>";
	exit;
}

include('../../conn.php');
include('../../classes/funcionario.php');
include('../../classes/curso.php');
include('../../classes/clt.php');
include('../../classes/projeto.php');
include('../../classes/calculos.php');
include('../../classes/abreviacao.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../classes/valor_proporcional.php');
include('../../funcoes.php');

$Fun     = new funcionario();
$Fun    -> MostraUser(0);
$user	 = $Fun -> id_funcionario;
$regiao  = $_REQUEST['regiao'];

$Curso 	 = new tabcurso();
$Clt 	 = new clt();
$ClasPro = new projeto();
$Calc	 = new calculos();





?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet :: Rescis&atilde;o</title>
<link href="../../favicon.ico" rel="shortcut icon" />
<link href="../../net1.css" rel="stylesheet" type="text/css">
<link href="../../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
<link href="../../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../js/ramon.js"></script>
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.core.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker-pt-BR.js"></script>
<script type="text/javascript">
$(function() {
	$('#data_aviso').datepicker({
		changeMonth: true,
	    changeYear: true
	});
	
	$('.linha_1, .linha_2').click(function(){
		
		
		if($(this).attr('class') == 'linha_1' || $(this).attr('class') == 'linha_2' ) {
			
				$(this).addClass('linha_azul_1');	
		
		} else {
			
			$(this).removeClass('linha_azul_1');	
			
			
			}
			
			
		
		});

		
});

</script>
<style>

img {
margin:0;
padding:0;	
}
body {
	background-color:#FAFAFA; text-align:center; margin:0px;
}
p {
	margin:0px;
}
#corpo {
	width:4000px; background-color:#FFF; margin:0px auto; text-align:left; padding-top:20px; padding-bottom:10px;
}


.verticaltext {
writing-mode: tb-rl;
filter: flipv fliph;
}

.novo_tr td{
font-size:12px;	
	
}
.tabela{
background-color:#FFF;
 margin-top:5px;
   border:1px solid #FFF;
 line-height:22px; 
 font-size:11px;
}

.tabela tr td{
text-align:center;	
border:1px solid #FFF
}
.linha_1 {
	
	background-color:#FAFAFA;

}

.linha_2 {
	background-color: #F0F0F0;

}


.linha_1:hover, .linha_2:hover{
	
background-color:  #D7EBFF;
	
	
}

.linha_azul_1{
background-color: #8CC6FF;	
	}

</style>

<style media="print">
#corpo {
	 background-color:#FFF; margin:0px auto; text-align:left; padding-top:20px; padding-bottom:10px;
}
.novo_tr{
font-size:10px;	
	
}
.tabela{
background-color:#FFF;
 margin:0px auto;
 
 border:1px #FFF; 
 line-height:22px; 
 font-size:11px;
}
</style>
</head>
<body>
<div id="corpo">
<?php

$data_aviso = implode('-',array_reverse(explode('/', $_POST['data_aviso'])));
$regiao	    = $_POST['regiao'];
$projeto    = $_POST['projeto'];
$array_clts = implode(',' ,$_POST['id_clt']);

$array_clt_estabilidade = array();
if($projeto == 'todos') {

	$qr_clt_1 = mysql_query("SELECT * FROM rh_clt WHERE status IN(10,200) AND id_regiao = '$regiao' AND id_clt IN($array_clts) ORDER BY nome ASC");	
	
} else {
	$qr_clt_1 = mysql_query("SELECT * FROM rh_clt WHERE status IN(10,200) AND id_regiao = '$regiao' AND id_clt IN($array_clts) AND id_projeto = '$projeto'  ORDER BY nome ASC");

}


            
                    
               
      

///VERIFICAÇÃO DE TRABALHADOR EM PERÍODO DE ESTABILIDADE

while($row_clt_1 = mysql_fetch_assoc($qr_clt_1)):

	$qr_eventos = mysql_query("SELECT * FROM rh_eventos WHERE data_retorno <= '$data_aviso' AND cod_status IN(40,50,51,20,52) AND  id_clt = '$row_clt_1[id_clt]' ORDER BY data_retorno DESC") or die(mysql_error());
	if(mysql_num_rows($qr_eventos) != 0) {
		
		$row_eventos = mysql_fetch_assoc($qr_eventos);
	
			$dt_retorno   = explode('-', $row_eventos['data_retorno']);
			$data_retorno = mktime(0,0,0,$dt_retorno[1],$dt_retorno[2],  $dt_retorno[0]);
			
			$data_rescisao = explode('-',$data_aviso);
			$data_rescisao = mktime(0,0,0,$data_rescisao[1],$data_rescisao[2], $data_rescisao[0]);
			
			$diferenca = $data_rescisao -$data_retorno;
			$diferenca = $diferenca / 86400	;
				
			if($diferenca <30   and  $_COOKIE['logado'] ==87)	{
			
			////armazenar em um array
			$array_clt_estabilidade[] = $row_clt_1['id_clt'];
			$array_motivo[$row_clt_1['id_clt']] = $row_eventos['nome_status']; 		
			
				
			}
	}
endwhile;
$ids_clts = @implode(',',$array_clt_estabilidade);





$Clt 			   -> MostraClt($id_clt);
$nome 			   = $Clt -> nome;
$codigo 		   = $Clt -> campo3;
$data_demissao 	   = $Clt -> data_demi;
$contratacao 	   = $Clt -> tipo_contratacao;
$data_aviso_previo = $Clt -> data_aviso;
$data_demissaoF    = $Fun -> ConverteData($data_demissao);


if(sizeof($array_clt_estabilidade) != 0) {
	$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE status IN(10,200) AND id_regiao = '$regiao' AND id_clt NOT IN($ids_clts) AND id_clt IN($array_clts) ORDER BY nome ASC");
	
} else {
	$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE status IN(10,200) AND id_regiao = '$regiao' AND id_clt IN($array_clts) ORDER BY nome ASC");
}


?>


<fieldset  style="width:400px; background-color:#F4FAFF">
<legend style="margin-top: -20px;" ><h3>Relatório das recisões</h3></legend>
<table style="margin-left:100px;" >
	<tr>
    	<td align="right"> <strong>Data de demissão:</strong> </td>
        <td> <?=$_POST['data_aviso']?></td>      
    </tr>
    <tr>
    	  <td align="right"><strong>Motivo do Afastamento:</strong> </td>
          <td>Dispensa sem Justa Causa</td>
    </tr>
    <tr>
    	<td align="right"><strong>Fator:</strong></td>
        <td>Empregador</td>
    </tr>
    <tr>
    	<td align="right"><strong>Aviso prévio:</strong></td>
        <td>Indenizado</td>
    </tr>
</table>
</fieldset>
<?php
$cor_bloco_1 = "#CCCCCC";
$cor_bloco_2 = "#CCE6FF";
$cor_bloco_3 = "#E8D0D0";
$cor_bloco_4 = "#E7FFCE";
$cor_bloco_5 = "#FFF2EC";
$cor_bloco_6 = "#ECFFD9";
$cor_bloco_7 = "#FFE1D2";
?>


              <table cellpadding="0" cellspacing="0" class="tabela" border="1" >
              <tr>
              	<td colspan="7" bgcolor="<?=$cor_bloco_1; ?>"></td>
                <td colspan="5" bgcolor="<?=$cor_bloco_2; ?>" align="center"><strong>SALÁRIOS</strong></td>
                <td colspan="5" bgcolor="<?=$cor_bloco_3; ?>" align="center"><strong>DÉCIMO TERCEIRO</strong></td>
                <td colspan="7" bgcolor="<?=$cor_bloco_4; ?>" align="center"><strong>FÉRIAS</strong></td>
                <td colspan="14" bgcolor="<?=$cor_bloco_5; ?>" align="center"><strong>OUTROS VENCIMENTOS</strong></td>
                <td colspan="3" bgcolor="<?=$cor_bloco_6; ?>" align="center"><strong>OUTROS DESCONTOS</strong></td>
                <td  bgcolor="<?=$cor_bloco_7; ?>" align="center" colspan="3"><strong>FGTS</strong></td>
              
              </tr>
              
              
              
              
                <tr>
                  <td width="50" bgcolor="<?=$cor_bloco_1; ?>"><img src="imagens/cod.png"/></td>
                  <td width="350"  bgcolor="<?=$cor_bloco_1; ?>"><img src="imagens/nome.png"/></td>
                  <td width="70"  bgcolor="<?=$cor_bloco_1; ?>"><img src="imagens/dt_admissao.png"/></td>
                 <td  width="90" bgcolor="<?=$cor_bloco_1; ?>"><img src="imagens/sal_base_calc.png"/></td>
                  <td width="90"   bgcolor="<?=$cor_bloco_1; ?>"><img src="imagens/rendimentos.png"/></td>
                 <td  width="90" bgcolor="<?=$cor_bloco_1; ?>"><img src="imagens/descontos.png"/></td>
                 <td  width="90"bgcolor="#FF9D9D"><img src="imagens/total2.png"/></td>
                 
                 
                  <td  width="190" bgcolor="<?=$cor_bloco_2; ?>"><img src="imagens/saldo_salario.png"/></td>
                  <td   width="90" bgcolor="<?=$cor_bloco_2; ?>"><img src="imagens/inss_salarios.png"/></td>
                  <td  width="90"  bgcolor="<?=$cor_bloco_2; ?>"><img src="imagens/irrf_salarios.png"/></td>
                  <td  width="90" bgcolor="<?=$cor_bloco_2; ?>"><img src="imagens/previdencia.png"/></td>
                  <td  width="90" bgcolor="<?=$cor_bloco_2; ?>"><img src="imagens/total.png"/></td>
                  
                  
                 
                  <td  width="190"  bgcolor="<?=$cor_bloco_3; ?>"><img src="imagens/decimo_prop.png"/></td>
                  <td  width="90" bgcolor="<?=$cor_bloco_3; ?>"><img src="imagens/decimo_inss.png"/></td>
                  <td  width="90" bgcolor="<?=$cor_bloco_3; ?>"><img src="imagens/decimo_irrf.png"/></td>
                  <td  width="90" bgcolor="<?=$cor_bloco_3; ?>"><img src="imagens/decimo_prev.png"/></td>
                   <td  width="90" bgcolor="<?=$cor_bloco_3; ?>"><img src="imagens/total.png"/></td>
                   
               
                 
                    <td   align="center" width="90" bgcolor="<?=$cor_bloco_4; ?>"><img src="imagens/ferias_vencidas.png"/></td>
                    <td   align="center" width="90" bgcolor="<?=$cor_bloco_4; ?>"><img src="imagens/um_terco_ferias.png"/></td>
                   <td   align="center" width="190" bgcolor="<?=$cor_bloco_4; ?>"><img src="imagens/ferias_prop.png"/></td>
                   <td   align="center" width="90" bgcolor="<?=$cor_bloco_4; ?>"><img src="imagens/um_terco_ferias_prop.png"/></td>
                   <td   align="center" width="90" bgcolor="<?=$cor_bloco_4; ?>"><img src="imagens/inss_ferias.png"/></td>
                  <td   align="center" width="90" bgcolor="<?=$cor_bloco_4; ?>"><img src="imagens/irrf_ferias.png"/></td>
                  <td   align="center" width="90" bgcolor="<?=$cor_bloco_4; ?>"><img src="imagens/total.png"/></td>
                 
                 
                 
                 
                   
                   <td width="90" bgcolor="<?=$cor_bloco_5; ?>"><img src="imagens/sal_familia.png"/></td>
                    <td width="90" bgcolor="<?=$cor_bloco_5; ?>"><img src="imagens/adicional_noturno.png"/></td>
                    <td width="90" bgcolor="<?=$cor_bloco_5; ?>"><img src="imagens/comissoes.png"/></td>
                    <td width="90" bgcolor="<?=$cor_bloco_5; ?>"><img src="imagens/gratificacoes.png"/></td>
                    <td width="90" bgcolor="<?=$cor_bloco_5; ?>"><img src="imagens/h_extra.png"/></td>
                    <td width="90" bgcolor="<?=$cor_bloco_5; ?>"><img src="imagens/insalu_peri.png"/></td>
                    <td width="90" bgcolor="<?=$cor_bloco_5; ?>"><img src="imagens/atraso_resc.png"/></td>
                    <td width="90" bgcolor="<?=$cor_bloco_5; ?>"><img src="imagens/outros.png"/></td>
                    <td width="90" bgcolor="<?=$cor_bloco_5; ?>"><img src="imagens/aviso_previo.png"/></td>
                    <td width="190" bgcolor="<?=$cor_bloco_5; ?>"><img src="imagens/13_indenizado.png"/></td>
                    <td width="90" bgcolor="<?=$cor_bloco_5; ?>"><img src="imagens/ferias_dobro.png"/></td>
                   	<td  width="90" bgcolor="<?=$cor_bloco_5; ?>"><img src="imagens/vale_refeicao.png"/></td>
                     <td width="90"  bgcolor="<?=$cor_bloco_5; ?>"><img src="imagens/debito_vale_refeicao.png"/></td>
                     <td width="90" bgcolor="<?=$cor_bloco_5; ?>"><img src="imagens/total.png"/></td>
                  
                 
                  <td width="90" bgcolor="<?=$cor_bloco_6; ?>"><img src="imagens/aviso_pago_func.png"/></td>
                  <td width="90" bgcolor="<?=$cor_bloco_6; ?>"><img src="imagens/devolucao.png"/></td>    
                  <td width="90" bgcolor="<?=$cor_bloco_6; ?>"><img src="imagens/indenizacacao_479.png"/></td>   
                     
                  
                   <!--- <td>FGTS 8%:</td>
                 <td>FGTS 40%:</td>--->
                 <td  width="40" bgcolor="<?=$cor_bloco_7; ?>"><img src="imagens/codigo_saque.png"/></td>
                 <td  width="120" bgcolor="<?=$cor_bloco_7; ?>"><img src="imagens/saldo_fgts.png"/></td>
                 <td  width="120" bgcolor="<?=$cor_bloco_7; ?>"><img src="imagens/multa_50.png"/></td>
                
                </tr>
    <?php
$contador=0;
while($row_clt = mysql_fetch_assoc($qr_clt)):

$contador= $contador+1;


$id_clt		      = $row_clt['id_clt'];

// Faltas no Mês
list($ano_demissao, $mes_demissao, $dia_demissao) = explode('-', $data_aviso);

$qr_faltas = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND id_mov = '62' AND (status = '1' OR status = '5') AND mes_mov = '".$mes_demissao."' AND ano_mov = '".$ano_demissao."'");
$faltas    = @mysql_result($qr_faltas,0);

if($dia_demissao > 30) {
	$dias_trabalhados = 30;
} else {
	$dias_trabalhados = $dia_demissao;
}



// Calculando Saldo FGTS
$qr_liquido = mysql_query("SELECT SUM(salliquido) AS liquido FROM rh_folha_proc WHERE id_clt = '$id_clt' AND status = '3'");
$fgts = number_format(mysql_result($qr_liquido,0) * 0.08, 2, ',', '.'); 


$regiao		      = $regiao;
$fator		      = 'empregador';
$dispensa		  = '61';
$faltas			  = '';
$aviso			  = 'indenizado';
$previo			  = 30;
$fgts			  = $fgts;
$anterior		  = 0;
$valor			  = '0,00';
$data_aviso		  = implode('-', array_reverse(explode('/', $data_aviso)));
$devolucao  	  = '';//str_replace(',', '.', str_replace('.', '',  $_REQUEST['devolucao']));

$Clt 		   -> MostraClt($id_clt);
$nome 		    = $Clt -> nome;
$codigo 	    = $Clt -> campo3;
$data_demissao  = $data_aviso;
$data_entrada   = $Clt -> data_entrada;   
$idprojeto 	    = $Clt -> id_projeto;
$idcurso 	    = $Clt -> id_curso;
$idregiao 	    = $Clt -> id_regiao;
$data_demissaoF = $Fun -> ConverteData($data_demissao);
$data_entradaF  = $Fun -> ConverteData($data_entrada);


///////////////////////////////////////////////////////////////////////////////////// TODOS OS CÁLCULOS DA RESCISÃO
include('calculo_recisao.php');


// Formatando as Variáveis
$arredondamento_positivoF  = number_format($arredondamento_positivo,2,',','.'); 
$saldo_de_salarioF 		   = number_format($saldo_de_salario,2,',','.');
$inss_saldo_salarioF 	   = number_format($inss_saldo_salario,2,',','.');
$irrf_saldo_salarioF 	   = number_format($irrf_saldo_salario,2,',','.');
$terceiro_ssF              = number_format($terceiro_ss,2,',','.');
$base_irrf_saldo_salariosF = number_format($base_irrf_saldo_salarios,2,',','.');
$to_saldo_salarioF         = number_format($to_saldo_salario,2,',','.');

$valor_tdF                 = number_format($valor_td,2,',','.');
$valor_td_inssF            = number_format($valor_td_inss,2,',','.');
$valor_td_irrfF            = number_format($valor_td_irrf,2,',','.');
$base_irrf_tdF             = number_format($base_irrf_td,2,',','.');
$total_dtF                 = number_format($total_dt,2,',','.');

$fv_valor_baseF            = number_format($fv_valor_base,2,',','.');
$fv_um_tercoF              = number_format($fv_um_terco,2,',','.');
$fp_valor_totalF           = number_format($fp_valor_total,2,',','.');
$fp_um_tercoF              = number_format($fp_um_terco,2,',','.');
$ferias_totalF             = number_format($ferias_total,2,',','.');
$ferias_irrfF              = number_format($ferias_irrf,2,',','.');
$ferias_total_finalF       = number_format($ferias_total_final,2,',','.');

$valor_sal_familiaF        = number_format($valor_sal_familia,2,',','.');
$valor_adnoturnoF          = number_format($valor_adnoturno,2,',','.');
$valor_insalubridadeF      = number_format($valor_insalubridade,2,',','.');
$valor_atrasoF             = number_format($valor_atraso,2,',','.');
$valor_comissaoF           = number_format($valor_comissao,2,',','.');
$valor_grativicacaoF       = number_format($valor_grativicacao,2,',','.');
$hora_extraF               = number_format($hora_extra,2,',','.');
$valor_outroF              = number_format($valor_outro,2,',','.');

$aviso_previo_valor_dF     = number_format($aviso_previo_valor_d,2,',','.');
$outros_descontosF         = number_format($outros_descontos,2,',','.');
$aviso_previo_valor_aF     = number_format($aviso_previo_valor_a,2,',','.');

$fgts8_totalF              = number_format($fgts8_total,2,',','.');
$fgts4_totalF              = number_format($fgts4_total,2,',','.');

$total_outrosF             = number_format($total_outros,2,',','.');
$total_descontosF          = number_format($total_descontos,2,',','.');
$to_rendimentosF           = number_format($to_rendimentos,2,',','.');
$valor_rescisao_finalF     = number_format($valor_rescisao_final,2,',','.');
$devolucaoF                = number_format($devolucao,2,',','.');
$total_outros_descontosF   = number_format($total_outros_descontos,2,',','.');

// Formatando as Variáveis para Arquivo TXT
$saldo_de_salarioT 		   = number_format($saldo_de_salario,2,'.','');
$inss_saldo_salarioT 	   = number_format($inss_saldo_salario,2,'.','');
$irrf_saldo_salarioT 	   = number_format($irrf_saldo_salario,2,'.','');
$terceiro_ssT 			   = number_format($terceiro_ss,2,'.','');
$base_irrf_saldo_salariosT = number_format($base_irrf_saldo_salarios,2,'.','');
$to_saldo_salarioT 		   = number_format($to_saldo_salario,2,'.','');
$previ_ssT 				   = number_format($previ_ss,2,'.','');
$valor_sal_familia_totT    = number_format($valor_sal_familia + $sal_familia_anterior,2,'.','');

$valor_tdT 				   = number_format($valor_td,2,'.','');
$valor_td_inssT 		   = number_format($valor_td_inss,2,'.','');
$valor_td_irrfT 		   = number_format($valor_td_irrf,2,'.','');
$base_irrf_tdT             = number_format($base_irrf_td,2,'.','');
$total_dtT                 = number_format($total_dt,2,'.','');
$previ_dtT                 = number_format($previ_dt,2,'.','');

$fv_valor_baseT 		   = number_format($fv_valor_base,2,'.','');
$fv_um_tercoT 			   = number_format($fv_um_terco,2,'.','');
$fp_valor_totalT           = number_format($fp_valor_total,2,'.','');
$fp_um_tercoT              = number_format($fp_um_terco,2,'.','');
$ferias_totalT             = number_format($ferias_total,2,'.','');
$ferias_inssT              = number_format($ferias_inss,2,'.','');
$ferias_irrfT              = number_format($ferias_irrf,2,'.','');
$ferias_total_finalT       = number_format($ferias_total_final,2,'.','');

$valor_sal_familiaT 	   = number_format($valor_sal_familia,2,'.','');
$valor_adnoturnoT          = number_format($valor_adnoturno,2,'.','');
$valor_insalubridadeT      = number_format($valor_insalubridade,2,'.','');
$vale_refeicaoT            = number_format($vale_refeicao,2,'.','');
$debito_vale_refeicaoT     = number_format($debito_vale_refeicao,2,'.','');
$valor_atrasoT             = number_format($valor_atraso,2,'.','');
$valor_comissaoT           = number_format($valor_comissao,2,'.','');
$valor_grativicacaoT       = number_format($valor_grativicacao,2,'.','');
$hora_extraT               = number_format($hora_extra,2,'.','');
$valor_outroT              = number_format($valor_outro,2,'.','');

if($t_ap == '2') {
	$aviso_previo_valorT   = number_format($aviso_previo_valor_d,2,'.','');
} else {
	$aviso_previo_valorT   = number_format($aviso_previo_valor_a,2,'.','');
}

$fgts8_totalT 			   = number_format($fgts8_total,2,'.','');
$fgts4_totalT 			   = number_format($fgts4_total,2,'.','');
$total_outrosT             = number_format($total_outros,2,'.','');
$to_rendimentosT 		   = number_format($to_rendimentos,2,'.','');
$to_descontosT 			   = number_format($total_descontos,2,'.','');
$valor_rescisao_finalT 	   = number_format($valor_rescisao_final,2,'.','');
$UltSalF 				   = number_format($salario_base,2,',','.');
$UltSalT 				   = number_format($salario_base,2,'.','');
$devolucaoT 			   = number_format($devolucao,2,'.','');
//

if(!empty($array_codigos_rendimentos)) {
	foreach($array_codigos_rendimentos as $chave => $valor) {
		$a_rendimentos   .= $valor.',';
		$a_rendimentosva .= $array_valores_rendimentos[$chave].',';
	}
}

// Selecionando a Dispensa selecionada para gravar na tabela Eventos
$qr_eventos = mysql_query("SELECT * FROM rhstatus WHERE codigo = '$dispensa'");
$row_evento = mysql_fetch_array($qr_eventos);

$class = ($contador %2== 0)? 'class="linha_1"':'class="linha_2"';
 ?>
    <tr <?php echo $class;?>>
      <td><?=$id_clt?></td>
      <td><?php echo $nome;?></td>
      <td title="Data Admissão"><?=$data_entradaF?></td> 
      <td>R$
        <?=number_format(($salario_base - $total_rendi),2,',','.');
				$total_b_calculo += $salario_base - $total_rendi;
		?></td>
     
      
      <td>R$ <?php echo $to_rendimentosF;
				$total_rendimentos += $to_rendimentos;?></td>
      <td>R$ <?php echo number_format($total_descontos,2,',','.');
					$totalizador_descontos_ += $total_descontos;
		?></td>
      <td  align="right"><strong>R$
        <?=$valor_rescisao_finalF?>
        <br />
        <?php			
			 if(!empty($arredondamento_positivo)) {
                      echo 'Arredondamento Positivo: '.$arredondamento_positivoF.'';
                  } 
				  
				$totalizador_valor_final += $valor_rescisao_final; 
				  
				  
				  ?>
        </strong>
        <?php $total ?></td>
      
       
      <!--------salarios-->
      <td>(<?php echo $dias_trabalhados;?> /<?php echo $qnt_dias_mes; ?>) dias  R$
        <?=$saldo_de_salarioF?>
        <?php if(!empty($faltas)) { echo '('.$faltas.' faltas)'; } 
		 		$totalizador_saldo_salario +=$saldo_de_salario;
		 ?></td>
      <td>R$
        <?=$inss_saldo_salarioF;
			 		$totalizador_inns_salario += $inss_saldo_salario; 
			 	?></td>
      <td>R$
        <?=$irrf_saldo_salarioF;
			 		$totalizador_irrf_salario += $irrf_saldo_salario;
			 ?></td>
      <td>R$
        <?=number_format($previ_ss,2,',','.');
			 			$totalizador_previdencia += $previ_ss;
			 ?></td>
      <td  align="right"><strong>R$
        <?=number_format(($saldo_de_salario-$inss_saldo_salario-$irrf_saldo_salario-$previ_ss),2,',','.');
			 		
					$totalizador_salarios_ += $saldo_de_salario-$inss_saldo_salario -$irrf_saldo_salario-$previ_ss;
			
			 ?>
      </strong></td>
     
      <!--------décimo terceiro-->
      <td>(
        <?=$meses_ativo_dt?>
        /12) meses R$
        <?=$valor_tdF;
				$totalizador_decimo_ter_prop += $valor_td;
			?></td>
      <td>R$
        <?=$valor_td_inssF;
					$totalizador_inss_decimo += $valor_td_inss;
			?></td>
      <td>R$        
        <?=$valor_td_irrfF;
					$totalizador_irrf_decimo += $valor_td_irrf;
			?></td>
      <td>R$
        <?=number_format($previ_dt,2,',','.');
					$totalizador_previ_decimo += $previ_dt;
			?></td>
      <td  align="right"><strong>
        <?=number_format(($valor_td-$valor_td_inss-$valor_td_irrf-$previ_dt),2,',','.');
				$totalizador_decimo += $valor_td-$valor_td_inss-$valor_td_irrf-$previ_dt;
			
			?>
      </strong></td>
     
      <!--------Férias-->
      <td>R$
        <?=$fv_valor_baseF;
		 			$totalizador_ferias_vencidas += $fv_valor_base;
		 ?></td>
      <td>R$
        <?=$fv_um_tercoF;
		 			$totalizador_um_terco_ferias += $fv_um_terco;
		 ?></td>
      <td title="Férias Proporcionais">(
        <?=$meses_ativo_fp?>
        /12) meses R$
        <?=$fp_valor_totalF;
		 			$totalizador_proporcional += $fp_valor_total;
		 ?></td>
      <td>R$
        <?=$fp_um_tercoF;
		 			$totalizador_um_terco_prorporcional += 	$fp_um_terco;
		 ?></td>
      <td>R$ 0,00</td>
      <td>R$
        <?=$ferias_irrfF;
		 		$totalizador_irrf_ferias += $ferias_irrf;
		 ?></td>
      <td  align="right"><strong>R$
<?=number_format(($fv_valor_base+$fv_um_terco+$fp_valor_total+$fp_um_terco-$ferias_irrf),2,',','.');
			
			$totalizador_valor_ferias += $fv_valor_base+$fv_um_terco+$fp_valor_total+$fp_um_terco-$ferias_irrf;
			?>
      </strong></td>
      
      
      
  
      
      
      
      <!--------Outros vencimentos-->
    
      <td>R$
        <?=$valor_sal_familiaF;
	   		       $totalizador_sal_familia_ov += $valor_sal_familia;	   			
	   ?></td>
      <td>R$
        <?=$valor_adnoturnoF;
				  $totalizador_adnoturno_ov += $valor_salvalor_adnoturnoF_familia;	
		?></td>
      <td>R$
        <?=$valor_comissaoF;
				$totalizador_comissao_ov += $valor_comissao;
		?></td>
      <td>R$
        <?=$valor_grativicacaoF;
					$totalizador_gratificacao_oc += $valor_grativicacao;
		?></td>
      <td>R$
        <?=$hora_extraF;
					$totalizador_h_extra_ov += $hora_extra;
		?></td>
      <td>R$
        <?=$valor_insalubridadeF;
					$totalizador_insalubridade_ov += $valor_insalubridade;
		?></td>
      <td>R$
        <?=$valor_atrasoF; 
				$totalizador_atraso_ov += $valor_atraso;
				unset ($valor_atrasoF);?></td>
      <td>R$
        <?=$valor_outroF;
				$totalizador_outros_ov += $valor_outro;
		?></td>
      <td>R$
        <?=$aviso_previo_valor_aF;
					$totalizador_aviso_previo_ov += $aviso_previo_valor_a;
		?></td>
      <td>(
        <?=$num_ss?>
        /12) meses R$
        <?=$terceiro_ssF;
					$totalizador_saldo_indenizado_ov += $terceiro_ss;
		?></td>
      <td>R$
        <?=number_format($multa_fv,2,',','.');
					$totalizador_multa_ov += $multa_fv;
		?></td>
      <td>R$
        <?=number_format($vale_refeicao,2,',','.');
					$totalizador_vale_refeicao_ov += $vale_refeicao;
		?></td>
      <td>R$
        <?=number_format($debito_vale_refeicao,2,',','.');
					$totalizador_debito_vale_ov += $debito_vale_refeicao;
		?></td>
      <td  align="right"><strong> R$
        <?=number_format($valor_sal_familia+$valor_adnoturno+$valor_comissao+$valor_grativicacao+$hora_extra+$valor_atraso + $valor_outro+$aviso_previo_valor_a+$terceiro_ss+$re_tot_rendimento-$re_tot_desconto+$vale_refeicao-$debito_vale_refeicao + $valor_insalubridade,2,',','.');
		   
		   $totalizador_outros_vencimentos += $valor_sal_familia+$valor_adnoturno+$valor_comissao+$valor_grativicacao+$hora_extra+$valor_atraso + $valor_outro+$aviso_previo_valor_a+$terceiro_ss+$re_tot_rendimento-$re_tot_desconto+$vale_refeicao-$debito_vale_refeicao + $valor_insalubridade; 
		   
		   /* ANTES DA ALTERAÇÂO 
		    $totalizador_outros_vencimentos += $valor_sal_familia+$valor_adnoturno+$valor_comissao+$valor_grativicacao+$hora_extra-$valor_insalubridade+$valor_atraso + $valor_outro+$aviso_previo_valor_a+$terceiro_ss+$re_tot_rendimento-$re_tot_desconto+$vale_refeicao-$debito_vale_refeicao; */
		   ?>
      </strong></td>
     
      <!--------Outros descontos-->    
     
     <td> <?=$aviso_previo_valor_dF;
	 
	 $totalizador_aviso_previo += $aviso_previo_valor_d;
	 
	 ?></td>
      <td>R$ <?=$devolucaoF;
	  			$totalizador_devolucao += $devolucao;
	  
	  ?></td>
       <td>R$ <?=number_format($art_479,2,',','.');
				$totalizador_art_479 += $art_479;
				
		?></td>   
     
      <!--------FGTS-->             
     	 <td><?=sprintf('%02d',$cod_saque_fgts);?></td>
           <td>R$ <?php echo number_format($total_fgts,2,',','.')?> </td>
         <td>R$ <?php echo number_format($multa_50_fgts,2,',','.')?> </td>
         
    </tr>
 
         
    <?php 
			

	
		unset($salario_calc_inss, $salario_calc_IR, $salario_calc_FGTS,$total_rendi, $to_rendimentos,  $valor_salario_dia, $dias_trabalhados, $valor_faltas, $saldo_de_salario, $terceiro_ss, $aviso_previo_valor_a,$fv_valor_base, $fp_valor_total, $fp_um_terco, $fv_um_terco, $valor_insalubridade, $salario_base, $salario_base_limpo, $meses_ativo_fp, $t_fp, $ano_proporcional_inicio,$mes_proporcional_inicio,$dia_proporcional_inicio, $ano_proporcional_final,$mes_proporcional_final,$dia_proporcional_final, $periodo_proporcional_inicio,$periodo_proporcional_final,  $dia_quinze, $periodo_aquisitivo, $periodos_gozados, $aquisitivo_final, $data_demissao,  $aquisitivo_inicio, $data_entrada, $periodo_proporcional, $dias_trabalhados, $valor_atraso, $to_descontos,$aviso_previo_valor_d,$valor_td, $fv_um_terco, $total_outros,  $hora_extra, $total_descontos, $array_codigos_rendimentos,$array_valores_rendimentos, $valor_diario_3, $periodos_vencidos, $total_fgts);
endwhile;

/////////////////////////////totalizadores
?>
    <tr bgcolor="#B6B6B6">
      <td></td>
      <td colspan="2" align="right"><strong>Totalizadores:</strong></td>
      <td>R$ <?php echo number_format($total_b_calculo, 2,',','.');?></td>
     
      <td>R$ <?php echo number_format($total_rendimentos, 2,',','.');?></td>
      <td>R$ <?php echo number_format($totalizador_descontos_, 2,',','.');?></td>
      <td>R$ <?php echo number_format($totalizador_valor_final, 2,',','.');?></td>
          
      <td>R$ <?php echo number_format($totalizador_saldo_salario, 2,',','.');?></td>
      <td>R$ <?php echo number_format($totalizador_inns_salario, 2,',','.'); ?></td>
      <td>R$ <?php echo number_format($totalizador_irrf_salario, 2,',','.');?></td>
      <td>R$ <?php echo number_format($totalizador_previdencia, 2,',','.');?></td>      
      <td>R$ <?php echo number_format($totalizador_salarios_, 2,',','.');?></td>
      
     
      <td>R$ <?php echo number_format($totalizador_decimo_ter_prop, 2,',','.'); ?></td>
      <td>R$ <?php echo number_format($totalizador_inss_decimo, 2,',','.');?></td>
      <td>R$ <?php echo number_format($totalizador_irrf_decimo, 2,',','.');?></td>
      <td>R$ <?php echo number_format($totalizador_previ_decimo, 2,',','.');?></td>
      <td>R$ <?php echo number_format($totalizador_decimo, 2,',','.');?></td>
     
      <td>R$ <?php echo  number_format($totalizador_ferias_vencidas, 2,',','.'); ?></td>
      <td>R$ <?php echo  number_format($totalizador_um_terco_ferias , 2,',','.'); ?></td>
      <td>R$ <?php echo  number_format($totalizador_proporcional , 2,',','.'); ?></td>
      <td>R$  <?php  echo number_format($totalizador_um_terco_prorporcional, 2,',','.'); ?></td>
      <td>R$ 0,00</td>
      <td>R$ <?php echo  number_format($totalizador_irrf_ferias, 2,',','.'); ?></td>
      <td>R$ <?php echo  number_format($totalizador_valor_ferias, 2,',','.'); ?></td>
     
      <td>R$ <?php echo  number_format($totalizador_sal_familia_ov, 2,',','.');?></td>
      <td>R$ <?php echo  number_format($totalizador_adnoturno_ov, 2,',','.');?></td>
      <td>R$ <?php echo  number_format($totalizador_comissao_ov, 2,',','.'); ?></td>
      <td>R$ <?php echo  number_format($totalizador_gratificacao_oc, 2,',','.'); ?></td>
      <td>R$ <?php echo  number_format($totalizador_h_extra_ov, 2,',','.'); ?></td>
      <td>R$ <?php echo  number_format($totalizador_insalubridade_ov, 2,',','.'); ?></td>
      <td>R$ <?php echo  number_format($totalizador_atraso_ov, 2,',','.');?></td>
      <td>R$ <?php echo  number_format($totalizador_outros_ov, 2,',','.');?></td>
      <td>R$ <?php echo  number_format( $totalizador_aviso_previo_ov, 2,',','.');?></td>
      <td>R$ <?php echo  number_format($totalizador_saldo_indenizado_ov, 2,',','.');?></td>
      <td>R$ <?php echo  number_format($totalizador_multa_ov , 2,',','.');?></td>
      <td>R$ <?php echo  number_format($totalizador_vale_refeicao_ov, 2,',','.'); ?></td>
      <td> R$ <?php echo  number_format($totalizador_debito_vale_ov, 2,',','.'); ?></td>
      <td>R$ <?php echo  number_format($totalizador_outros_vencimentos, 2,',','.');?></td>
    
      <td>R$ <?php echo  number_format($totalizador_aviso_previo, 2,',','.'); ?></td>
      <td>R$ <?php echo  number_format($totalizador_devolucao, 2,',','.'); ?></td>     
      <td>R$ <?php echo  number_format( $totalizador_art_479, 2,',','.'); ?></td>
      <td></td>
      <td>R$ <?php echo  number_format($totalizador_saldo_fgts, 2,',','.'); ?></td>
      <td>R$ <?php echo  number_format($totalizador_multa_50_fgts, 2,',','.'); ?></td>
        
      <!--- <td>R$ <?php echo  number_format($totalizador_fgts8, 2,',','.'); ?></td>
      <td>R$ <?php echo  number_format($totalizador_fgts4, 2,',','.'); ?></td>-->
    </tr>
  </table>
  
  
  

  
<table border="0" width="1000">
<?php
 ///////////////////////////////////////Não podem ser demitdos 

if(sizeof($array_clt_estabilidade)) {
	
	
	
	
	$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE (status IN(20,30,40,50,51,52) AND id_regiao = '$regiao') OR  id_clt IN($ids_clts)  ORDER BY nome ASC")   or die(mysql_error());
} else {
	$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE (status IN(20,30,40,50,51,52) AND id_regiao = '$regiao')  ORDER BY nome ASC")   or die(mysql_error());
}



if(mysql_num_rows($qr_clt)  !=0 ){
 
?>

<tr>
 	<td>&nbsp;</td>
 </tr>
 <tr>
 	<td>&nbsp;</td>
 </tr>
 
 
 <tr>
 	<td class="show" colspan="5">NÃO PODEM SER DEMITIDOS</td>
 </tr>	
        
  <tr class="novo_tr">      
    <td>CÓD.</td>
    <td>NOME</td>
    <td>DATA DE ADMISSÃO</td>   
    <td>MOTIVO</td>
    <td>SALÁRIO</td>   
    </tr>
 
 <?php
while($row_clt = mysql_fetch_assoc($qr_clt)):

$id_clt		      = $row_clt['id_clt'];

// Faltas no Mês
list($ano_demissao, $mes_demissao, $dia_demissao) = explode('-', $data_aviso);

$qr_faltas = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND id_mov = '62' AND (status = '1' OR status = '5') AND mes_mov = '".$mes_demissao."' AND ano_mov = '".$ano_demissao."'");
$faltas    = @mysql_result($qr_faltas,0);

if($dia_demissao > 30) {
	$dias_trabalhados = 30;
} else {
	$dias_trabalhados = $dia_demissao;
}



// Calculando Saldo FGTS
$qr_liquido = mysql_query("SELECT SUM(salliquido) AS liquido FROM rh_folha_proc WHERE id_clt = '$id_clt' AND status = '3'");
$fgts = number_format(mysql_result($qr_liquido,0) * 0.08, 2, ',', '.'); 


$regiao		      = $regiao;
$fator		      = 'empregador';
$dispensa		  = '61';
$faltas			  = '';
$aviso			  = 'indenizado';
$previo			  = 30;
$fgts			  = $fgts;
$anterior		  = 0;
$valor			  = '0,00';
$data_aviso		  = implode('-', array_reverse(explode('/', $_REQUEST['data_aviso'])));
$devolucao  	  = '';//str_replace(',', '.', str_replace('.', '',  $_REQUEST['devolucao']));

$Clt 		   -> MostraClt($id_clt);
$nome 		    = $Clt -> nome;
$codigo 	    = $Clt -> campo3;
$data_demissao  = $data_aviso;
$data_entrada   = $Clt -> data_entrada;   
$idprojeto 	    = $Clt -> id_projeto;
$idcurso 	    = $Clt -> id_curso;
$idregiao 	    = $Clt -> id_regiao;
$data_demissaoF = $Fun -> ConverteData($data_demissao);
$data_entradaF  = $Fun -> ConverteData($data_entrada);
 
 
 
// include('calculo_recisao.php');
 
 $Curso -> MostraCurso($idcurso);
	$salario_base = $Curso -> salario;
 
 $totalizador_sal_base +=$salario_base;
 
 ///pegando nome do status
 $qr_status = mysql_query("SELECT * FROM rhstatus WHERE codigo = '$row_clt[status]'");
 $row_status = mysql_fetch_assoc($qr_status);
 
 
 
 $contador++;
 $class = ($contador % 2== 0)? 'class="linha_1"':'class="linha_2"';
 
?>

    <tr <?php echo $class;?>>
        <td><?php echo $row_clt['id_clt']?></td>
        <td><?php echo $row_clt['nome']?></td>
        <td><?php echo $data_entradaF; ?></td>
        <td><?php 
		if(in_array($row_clt['id_clt'],$array_clt_estabilidade)) {
			
				echo 'Estabilidade - '.$array_motivo[$row_clt['id_clt']];
			
			} else {
				
				echo $row_status['especifica'];
			}
		
		
		
		
		 ?></td>
        <td>R$ <?php echo number_format($salario_base, 2,',','.'); ?> </td>
    </tr>

<?php


	unset($salario_calc_inss, $salario_calc_IR, $salario_calc_FGTS,$total_rendi, $to_rendimentos,  $valor_salario_dia, $dias_trabalhados, $valor_faltas, $saldo_de_salario, $terceiro_ss, $aviso_previo_valor_a,$fv_valor_base, $fp_valor_total, $fp_um_terco, $fv_um_terco, $valor_insalubridade, $salario_base, $salario_base_limpo, $meses_ativo_fp, $t_fp, $ano_proporcional_inicio,$mes_proporcional_inicio,$dia_proporcional_inicio, $ano_proporcional_final,$mes_proporcional_final,$dia_proporcional_final, $periodo_proporcional_inicio,$periodo_proporcional_final,  $dia_quinze, $periodo_aquisitivo, $periodos_gozados, $aquisitivo_final, $data_demissao,  $aquisitivo_inicio, $data_entrada, $periodo_proporcional, $dias_trabalhados, $valor_atraso, $to_descontos,$aviso_previo_valor_d,$valor_td, $fv_um_terco, $total_outros,  $hora_extra, $total_descontos, $array_codigos_rendimentos,$array_valores_rendimentos, $valor_diario_3, $periodos_vencidos, $multa_50_fgts, $qnt_dias_mes, $re_tot_desconto, $re_tot_rendimento);

endwhile;
?>
<tr>
	<td colspan="4" align="right"><strong>CUSTO MENSAL TOTAL:</strong></td>
    <td><strong>R$ <?php echo number_format($totalizador_sal_base, 2,',','.'); ?></strong></td>
</tr>
</table>

<?php
}
?>

<?php



$diferenca_5 =  $totalizador_multa_50_fgts * 0.05;
?>

<!-------TOTALIZADORES FINAIS--------->
<?php

//$total_recolhimento_inss = $totalizador_previdencia + $totalizador_previ_decimo + $totalizador_inns_salario + $totalizador_inss_decimo;

$total_irrf = $totalizador_irrf_salario + $totalizador_irrf_decimo + $totalizador_irrf_ferias;
$totalizador_final = $totalizador_valor_final + $totalizador_sal_base + $totalizador_multa_50_fgts + $total_recolhimento_inss + $total_irrf;


$totalizador_pis = ($totalizador_aviso_previo_ov + $totalizador_insalubridade_ov + $totalizador_saldo_indenizado_ov) * 0.01;


////CALCULO DO INSS A RECOLHER/////
$soma      = $totalizador_saldo_salario + $totalizador_saldo_indenizado_ov;
$total_20  = $soma * 0.20;
$total_5_8 = $soma * 0.058;
$total_1   = $soma * 0.01;

$total_recolhimento_inss = ($total_20 + $total_5_8 + $total_1 + $totalizador_previdencia + $totalizador_previ_decimo) - $totalizador_sal_familia_ov;
/////////////////////
?>

<table border="0" style="margin-top:50px; font-weight:bold; margin-left:200px;" bgcolor="#FFFFFF" width="500">
        <tr height="35">
            <td class="show" colspan="4">TOTALIZADORES</td>
         </tr>	
         <tr class="linha_1" height="35">
         	<td>TOTAL A SER PAGO(RESCISÕES)</td>
            <td>R$ <?php echo number_format($totalizador_valor_final,2,',','.');?></td>
         </tr>
         
         <tr class="linha_2" height="35">
         	<td>NÃO PODEM SER DEMITIDOS</td>
            <td>R$ <?php echo number_format($totalizador_sal_base,2,',','.'); ?></td>
         </tr>
           <tr class="linha_1" height="35">
         	<td>PIS</td>
            <td>R$ <?php echo number_format($totalizador_pis,2,',','.'); ?></td>
         </tr>
         <tr class="linha_2" height="35">
         	<td>MULTA DE 50% DO FGTS</td>
            <td> R$ <?php echo number_format($totalizador_multa_50_fgts,2,',','.'); ?></td>
         </tr>
           <tr class="linha_1" height="35"> 
         	<td>INSS A RECOLHER:</td>
            <td> R$ <?php echo number_format($total_recolhimento_inss,2,',','.'); ?></td>
         </tr>
        <tr class="linha_2" height="35">
         	<td>TOTAL IRRF</td>
            <td> R$ <?php echo number_format($total_irrf,2,',','.'); ?></td>
         </tr>
         
        <tr height="35">
         	<td align="right">TOTALIZADOR FINAL: </td>
            <td>R$ <?php echo number_format($totalizador_final,2,',','.');  ?></td>
         </tr>
          <tr height="35">
        <td align="right"><strong>TOTALIZADOR C\ MARGEM DE ERRO DE 0,5%</strong> </td>
        <td><strong>R$ <?php echo number_format($totalizador_final/0.995,2,',','.'); ?></strong></td>
     </tr> 
</table>


</div>
</body>
</html>