<?php
//CLASSE regiao 30.07.2009
class calculos{

public function __construct() {
	$id_user = $_COOKIE['logado'];
	
	$r = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
	$row_user = mysql_fetch_array($r);
	
	$this->id_userlocado= $row_user['id_master'];
	$this->regiaologado= $row_user['regiao'];
	$this->id_regiaologado= $row_user['id_regiao'];
	
}

function MostraINSS($base,$data){
	
	if(strstr($data, "/")){
		$d = explode ("/", $data);
		$dia = $d[0];
		$mes = $d[1];
		$ano = $d[2];
		$data_f = implode('-', array_reverse($d));
	}elseif(strstr($data, "-")){
		$d = explode ("-", $data);
		$dia = $d[2];
		$mes = $d[1];
		$ano = $d[0];
		$data_f = $data;
	}
		
	$result_inss = mysql_query("SELECT faixa,fixo,percentual,piso,teto FROM rh_movimentos where cod = '5020' and v_ini 
	<= '$base' and v_fim >= '$base' AND '$data_f' BETWEEN data_ini AND data_fim");
	$row_inss = mysql_fetch_array($result_inss);
			  
	$inss_saldo_salario = $base * $row_inss['percentual'];
	
	if($inss_saldo_salario > $row_inss['teto']) {
		$inss_saldo_salario = $row_inss['teto'];
	}
	
	
	$inss_saldo_salario = number_format($inss_saldo_salario,3,".","");
	$inss_saldo_salarioex = explode(".",$inss_saldo_salario);
	$decimal = substr($inss_saldo_salarioex[1], 0, 2); 
		
	$valor_final = $inss_saldo_salarioex[0].".".$decimal;
	
	$this->valor		= $valor_final;
	$this->percentual	= $row_inss['percentual'];
	//return $inss_saldo_salario;

}

function MostraIRRF($base,$idclt,$idprojeto,$data,$tipo='clt'){
	
	//----------- CALCULANDO DEDUÇÃO DO IMPOSTO DE RENDA -------------------
	if(strstr($data, "/")){
		$d = explode ("/", $data);
		$dia = $d[0];
		$mes = $d[1];
		$ano = $d[2];
	}elseif(strstr($data, "-")){
		$d = explode ("-", $data);
		$dia = $d[2];
		$mes = $d[1];
		$ano = $d[0];
	}
	$data_menor21 = date("Y-m-d", mktime(0,0,0, $mes,$dia,$ano - 21));
	// Será preciso enviar a data que o arquivo está sendo processado
	
	$menor21 = mysql_query("SELECT count(data1)as cont FROM dependentes where id_bolsista = '$idclt' and data1 > '$data_menor21' 
	and data1 != '0000-00-00' and id_projeto = '$idprojeto'");
	$row_menor21 = mysql_fetch_array($menor21);

	$menor22 = mysql_query("SELECT count(data1)as cont FROM dependentes where id_bolsista = '$idclt' and data2 > '$data_menor21' 
	and data2 != '0000-00-00' and id_projeto = '$idprojeto'");
	$row_menor22 = mysql_fetch_array($menor22);
		  
	$menor23 = mysql_query("SELECT count(data1)as cont FROM dependentes where id_bolsista = '$idclt' and data3 > '$data_menor21' 
	and data3 != '0000-00-00' and id_projeto = '$idprojeto'");
	$row_menor23 = mysql_fetch_array($menor23);

	$menor24 = mysql_query("SELECT count(data1)as cont FROM dependentes where id_bolsista = '$idclt' and data4 > '$data_menor21' 
	and data4 != '0000-00-00' and id_projeto = '$idprojeto'");
	$row_menor24 = mysql_fetch_array($menor24);

	$menor25 = mysql_query("SELECT count(data1)as cont FROM dependentes where id_bolsista = '$idclt' and data5 > '$data_menor21' 
	and data5 != '0000-00-00' and id_projeto = '$idprojeto'");
	$row_menor25 = mysql_fetch_array($menor25);
	   
	$total_filhos_menor_21 =  $row_menor21['0'] + $row_menor22['0'] + $row_menor23['0'] + $row_menor24['0'] + $row_menor25['0'];
	
	if($total_filhos_menor_21 != 0){ // REMOVIDO and $base > "1200.00" 22/01/2010
		$filhos_deducao = $total_filhos_menor_21;
		$result_deducao_ir = mysql_query("SELECT * FROM rh_movimentos where cod = '5049' AND anobase = '$ano'");
		$row_deducao_ir = mysql_fetch_array($result_deducao_ir);
		$valor_deducao_ir = $total_filhos_menor_21 * $row_deducao_ir['fixo'];
		
		$this->valor_deducao_ir_total	= $valor_deducao_ir;
		$this->valor_deducao_ir_fixo	= $row_deducao_ir['fixo'];
		$this->total_filhos_menor_21	= $total_filhos_menor_21;
		
		$base = $base - $valor_deducao_ir;
	}else{
		$this->valor_deducao_ir_total	= 0;
		$this->valor_deducao_ir_fixo	= 0;
		$this->total_filhos_menor_21	= 0;
	}
		  
	// --------------- CALCULANDO IMPOSTO DE RENDA -------------
	$result_IR = mysql_query("SELECT * FROM rh_movimentos where cod = '5021' and v_ini <= '$base' and v_fim >= '$base' AND anobase = '$ano'");
	$row_IR = mysql_fetch_array($result_IR);
	
	$valor_IR = $base * $row_IR['percentual'] - $row_IR['fixo'];
	
	if($tipo == "clt"){
		$result_recolhimentoIR = mysql_query("SELECT recolhimento_ir FROM rh_clt WHERE id_clt = '$idclt'");
		$row_recolhimentoIR = mysql_fetch_assoc($result_recolhimentoIR);
		$recolhimento = $row_recolhimentoIR['recolhimento_ir'];
		
		// Se o recolhimento não estiver vazio, soma o valor do IR mais o recolhimento
		if(!empty($recolhimento)) {
			$valor_IR = $valor_IR + $recolhimento;
		}
		
		// Se ainda assim o valor do IR mais o recolhimento for menor que 10 reais, atualiza o recolhimento e o valor do IR fica nulo
		if($valor_IR < 10) {
			$update_recolhimentoIR = "UPDATE rh_clt SET recolhimento_ir = '$valor_IR' WHERE id_clt = '$idclt'";
			$valor_IR = 0;
		// Se o valor do IR mais o recolhimento for maior que 10 reais e o recolhimento não estiver vazio, o recolhimento fica 
		// nulo e o valor do IR permanece
		} elseif((!empty($recolhimento)) and ($valor_IR > 10)) {
			$update_recolhimentoIR = "UPDATE rh_clt SET recolhimento_ir = 0 WHERE id_clt = '$idclt'";
		}
		//-------------------------
	}
	
	$this->valor				= $valor_IR;
	$this->percentual			= $row_IR['percentual'];
	$this->valor_fixo_ir		= $row_IR['fixo'];
	$this->base_calculo_ir		= $base;
	$this->recolhimento_ir 		= $update_recolhimentoIR;
	
}


function Salariofamilia($base,$idclt,$idprojeto,$data,$contratacao){
	
	if(strstr($data, "/")){
		$d = explode ("/", $data);
		$dia = $d[0];
		$mes = $d[1];
		$ano = $d[2];
	}elseif(strstr($data, "-")){
		$d = explode ("-", $data);
		$dia = $d[2];
		$mes = $d[1];
		$ano = $d[0];
	}
	
	$data_menor14 = date("Y-m-d", mktime(0,0,0, $mes,$dia,$ano - 14));
	
	$menor1 = mysql_query("SELECT count(data1)as cont FROM dependentes WHERE id_bolsista = '$idclt' and data1 > '$data_menor14' 
	and data1 != '0000-00-00' and id_projeto = '$idprojeto' AND contratacao = '$contratacao'");
	$row_menor1 = mysql_fetch_array($menor1);

	$menor2 = mysql_query("SELECT count(data1)as cont FROM dependentes WHERE id_bolsista = '$idclt' and data2 > '$data_menor14' 
	and data2 != '0000-00-00' and id_projeto = '$idprojeto' AND contratacao = '$contratacao'");
  	$row_menor2 = mysql_fetch_array($menor2);
		  
	$menor3 = mysql_query("SELECT count(data1)as cont FROM dependentes WHERE id_bolsista = '$idclt' and data3 > '$data_menor14' 
	and data3 != '0000-00-00' and id_projeto = '$idprojeto' AND contratacao = '$contratacao'");
	$row_menor3 = mysql_fetch_array($menor3);

	$menor4 = mysql_query("SELECT count(data1)as cont FROM dependentes WHERE id_bolsista = '$idclt' and data4 > '$data_menor14' 
	and data4 != '0000-00-00' and id_projeto = '$idprojeto' AND contratacao = '$contratacao'");
	$row_menor4 = mysql_fetch_array($menor4);

	$menor5 = mysql_query("SELECT count(data1)as cont FROM dependentes WHERE id_bolsista = '$idclt' and data5 > '$data_menor14' 
	and data5 != '0000-00-00' and id_projeto = '$idprojeto' AND contratacao = '$contratacao'");
	$row_menor5 = mysql_fetch_array($menor5);
	  
	$total_menor = $row_menor1['0'] + $row_menor2['0'] + $row_menor3['0'] + $row_menor4['0'] + $row_menor5['0'];
	
	if($mes == 01 and $ano == 2009){
		$ano = $ano - 1;
	}
	
	$result_familia = mysql_query("SELECT * FROM rh_movimentos where cod = '5022' and v_ini <= '$base' and v_fim >= '$base' AND anobase = '$ano'");
	$row_familia = mysql_fetch_array($result_familia);
	  
	$valor_familia = $total_menor * $row_familia['fixo'];
	
	$this->valor				= $valor_familia;
	$this->filhos_menores		= $total_menor;
	$this->fixo					= $row_familia['fixo'];
	
}

function adnoturno($idclt,$data){
	
	if(empty($data)){
		$dataexp = explode("/",date('d/m/Y'));
	}else{
		$dataexp = explode("/",$data);
	}
	
	$re_adnoturno = mysql_query("SELECT * FROM rh_movimentos_clt WHERE cod_movimento = '9000' AND id_clt = '$idclt' AND status = '5' AND 
	(lancamento = '1' AND mes_mov = '".$dataexp[1]."' AND ano_mov = '".$dataexp[2]."' OR lancamento = '2') ");
	$row_adnoturno = mysql_fetch_array($re_adnoturno);
	
	$this->valor			= $row_adnoturno['valor_movimento'];
	
}

function insalubridade($idclt,$data){
	
	if(empty($data)){
		$dataexp = explode("/",date('d/m/Y'));
	}else{
		$dataexp = explode("/",$data);
	}
	
	$re_insalubridade = mysql_query("SELECT * FROM rh_movimentos_clt WHERE (cod_movimento = '6006' OR cod_movimento = '6007') AND id_clt = '$idclt' 
	AND status != '0' AND (lancamento = '1' AND mes_mov = '".$dataexp[1]."' AND ano_mov = '".$dataexp[2]."' OR lancamento = '2') ");
	$row_insalubridade = mysql_fetch_array($re_insalubridade);
	
	$this->valor			= $row_insalubridade['valor_movimento'];
	
	
}
#(3,2009-12-01,2009,12,736.88,4109,0 )
function dt_data($parcela,$data,$ano_folha,$mes_folha,$salario_base,$clt,$valor_dt_rend){

	$exp_entrada = explode("-",$data);
	#2009 == 2009
	if($exp_entrada[0] == $ano_folha){
		#12 == 12
		if($exp_entrada[1] == $mes_folha){
			
			$meses_trab = 0;
			if($exp_entrada[2] <= 15){
				$meses_trab ++;
			}
		}else{
		
			$meses_trab = 11 - $exp_entrada[1];
			if($exp_entrada[2] <= 15){
				$meses_trab ++;
			}
		}
	}else{
	  $meses_trab = 12;
	}
				  
	$valor_dt1 = $salario_base / 12;				//EX: 1200.00 / 12 = 100.00
	$valor_dt = $meses_trab * $valor_dt1;			//EX: 11 * 100 = 1100.00
	
	
	if($parcela == 1){	// PRIMEIRA PARCELA
		$valor_dt = $valor_dt / 2 + $valor_dt_rend;					//EX: 1100.00 / 2 = 550.00
	}elseif($parcela == 2){
		$valor_desconto = $valor_dt / 2;
		
		if($exp_entrada[0] == $ano_folha){
			$valor_dt = $valor_dt + $valor_dt_rend + $valor_dt1;		//EX: 1100.00 / 2 + 100 = 650.0
		}else{
			$valor_dt = $valor_dt + $valor_dt_rend;						//EX: 1100.00 / 2 + 100 = 650.00   (REMOVI = $valor_dt1 +)
		}
		
		
	}else{
		$valor_dt = $valor_dt + $valor_dt_rend;						//EX: 1100.00 
	}
	
	#-- VALOR DO DT DO CLT
	$this->valor			= $valor_dt;
	$this->desconto_dt		= $valor_desconto;	
	$this->meses_trab		= $meses_trab;
	
}

}

/* ANTIGO INSS
function MostraINSS($base,$data){
	
	if(strstr($data, "/")){
		$d = explode ("/", $data);
		$dia = $d[0];
		$mes = $d[1];
		$ano = $d[2];
	}elseif(strstr($data, "-")){
		$d = explode ("-", $data);
		$dia = $d[2];
		$mes = $d[1];
		$ano = $d[0];
	}
		
	$result_inss = mysql_query("SELECT faixa,fixo,percentual,piso,teto FROM rh_movimentos where cod = '5020' and v_ini 
	<= '$base' and v_fim >= '$base' AND anobase = '$ano'");
	$row_inss = mysql_fetch_array($result_inss);
			  
	$inss_saldo_salario = $base * $row_inss['percentual'];
	
	if($inss_saldo_salario > $row_inss['teto']) {
		$inss_saldo_salario = $row_inss['teto'];
	}
	
	$this->valor		= $inss_saldo_salario;
	$this->percentual	= $row_inss['percentual'];
	//return $inss_saldo_salario;

}*/



/* ARQUIVOS EXECUTANDO ESTA ROTINA
- ESCALA.PHP
- COOPERATIVAS/TVSORRINO.PHP
- COOPERATIVAS/CONTRATO.PHP
- COOPERATIVAS/QUOTA.PHP
- ESCALA/AJAX.PHP
*/
?>