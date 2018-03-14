<?php

class calculos {

    public $meses_ativos;
    public $meses_ativos_dt;
    public $valor_deducao_ir_total;
    public $valor_deducao_ir_fixo;
    public $total_filhos_menor_21;
    public $valor;
    public $percentual; 
    public $valor_fixo_ir;
    public $base_calculo_ir;
    public $recolhimento_ir;
    public $filhos_menores;
    public $fixo;
    public $programadores = array(179,158,260,275,257,256);
    public $residuo_inss; // base + ferias - inss_ferias
    public $rend_com_ferias; // base + ferias
    public $id_mov;
    public $cod_mov;
    
    /**
     * MÉTODO CONSTRUTOR
     */
    public function __construct() {
	
	$id_user     = $_COOKIE['logado'];
	$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
	$row_user    = mysql_fetch_array($result_user);
	
	$this->id_userlocado   = $row_user['id_master'];
	$this->regiaologado    = $row_user['regiao'];
	$this->id_regiaologado = $row_user['id_regiao'];
	
    }


    function MostraINSS($base,$data,$aliquota=null,$base_ferias=null,$clt=null) {
	
//        if(in_array($_COOKIE['logado'], $this->programadores)){
//            echo "<pre>";
////                print_r($base . "," . $data);
//            echo "</pre>";
//        }
        
	if(strstr($data, '/')) {
		$d = explode ('/', $data);
		$dia = $d[0];
		$mes = $d[1];
		$ano = $d[2];
		$data_f = implode('-', array_reverse($d));
	} elseif(strstr($data, '-')) {
		$d = explode ('-', $data);
		$dia = $d[2];
		$mes = $d[1];
		$ano = $d[0];
		$data_f = $data;
	}

        $sql = "SELECT faixa, fixo, percentual, piso, teto FROM rh_movimentos 
								WHERE cod = '5020' 
								AND v_ini <= '$base' AND v_fim >= '$base' 
								AND '$data_f' BETWEEN data_ini AND data_fim";
        
	$result_inss = mysql_query($sql);
        
	$row_inss = mysql_fetch_array($result_inss);
        
        if(isset($aliquota) && $aliquota !== null){
            $perc = $aliquota / 100;             
            $aliquota_ferias = $aliquota / 100;
            
//            echo "ALIQ. FERIAS: {$aliquota_ferias} | ALIQ. FOLHA: {$row_inss['percentual']}<br>";
//            echo "BASE: {$base} | BASE FERIAS: {$base_ferias}<br>";
//            echo "BASE + BASE FERIAS = ".$calc_base_ferias = $base + $base_ferias."<br>";
            $calc_base_ferias = $base + $base_ferias;
            
            $sql_base_ferias = "SELECT faixa, fixo, percentual, piso, teto FROM rh_movimentos 
                                                                WHERE cod = '5020' 
                                                                AND v_ini <= '$calc_base_ferias' AND v_fim >= '$calc_base_ferias' 
                                                                AND '$data_f' BETWEEN data_ini AND data_fim";
            $result_inss_ferias = mysql_query($sql_base_ferias);
            $row_inss_ferias = mysql_fetch_array($result_inss_ferias);

//            echo "ALIQUOTA(BASE + BASE FERIAS): {$row_inss_ferias['percentual']}";
            
            $perc = $row_inss_ferias['percentual'];
//            echo "<br>INSS(BASE + BASE FERIAS): ".$valor_inss_ferias = $calc_base_ferias * $perc;
//            echo "<br>INSS FERIAS: " . $inss_ferias_res = $base_ferias * $aliquota / 100;
//            echo "<br>INSS FINAL: ".$this->residuo_inss = $valor_inss_ferias - $inss_ferias_res;
            $valor_inss_ferias = $calc_base_ferias * $perc;
            $inss_ferias_res = $base_ferias * $aliquota / 100;                        
            
//            if($valor_inss_ferias > 513.01){
//                $calc_resid = $valor_inss_ferias - 513.01;
//                $this->residuo_inss = $valor_inss_ferias - $inss_ferias_res - $calc_resid;
//            }else{
            $this->residuo_inss = $valor_inss_ferias - $inss_ferias_res;
//            }
            
            if(($clt == 7107) || ($clt == 5299)){
//                if($valor_inss_ferias > 513.01){
//                    $calc_resid = $valor_inss_ferias - 513.01;
//                    $this->residuo_inss = $valor_inss_ferias - $inss_ferias_res - $calc_resid;
//                }
                if($valor_inss_ferias > $row_inss_ferias['teto']){
                    $calc_resid = $valor_inss_ferias - $row_inss_ferias['teto'];
                    $this->residuo_inss = $valor_inss_ferias - $inss_ferias_res - $calc_resid;
                }
            }
            
            $this->rend_com_ferias = $calc_base_ferias;
        }else{
            $perc = $row_inss['percentual'];
        }
        
        if($_COOKIE['logado']==258)
        {
            echo "result_inss = [{$sql}]<br/>\n";
        }        
        
	$inss_saldo_salario = $base * $perc;
        
	if($inss_saldo_salario > $row_inss['teto']) {
		$inss_saldo_salario = $row_inss['teto'];
	}
	
//	$inss_saldo_salario = number_format($inss_saldo_salario, 3, '.', '');
//	$inss_saldo_salario = explode('.', $inss_saldo_salario);
//	$decimal            = substr($inss_saldo_salario[1], 0, 2);
//	
//	$valor_final = $inss_saldo_salario[0].'.'.$decimal;
        
	$valor_final = round($inss_saldo_salario,2);
	
	$this->valor        = $valor_final;
	$this->percentual   = $perc;
	$this->teto         = $row_inss['teto'];

        
    }


    /**
     * MÉTODO PARA CALCULAR IRRF 
     * @param type $base
     * @param type $idclt
     * @param type $idprojeto
     * @param type $data
     * @param type $tipo
     */
    function MostraIRRF($base,$idclt,$idprojeto,$data,$tipo='clt') {
        
        
        //echo $base . "<br>";
	$total_filhos_menor_21 = 0;
	
	if(strstr($data, '/')) {
		$d = explode('/', $data);
		$dia = $d[0];
		$mes = $d[1];
		$ano = $d[2];
	} elseif(strstr($data, '-')) {
		$d = explode('-', $data);
		$dia = $d[2];
		$mes = $d[1];
		$ano = $d[0];
	}
	
	$data_menor21 =  mktime(0,0,0, $mes, $dia, $ano - 21);	
	$qr_dependentes = mysql_query("SELECT COUNT(*) FROM prestador_dependente WHERE id_prestador = '$id_prestador'");
        //echo "SELECT COUNT(*) FROM prestador_dependente WHERE id_prestador = '$id_prestador'";
	$total_filhos_menor_21 = (int) @mysql_result($qr_dependentes, 0);
	
        if ($tipo == 'clt') {
             $wheretipo = "AND contratacao != 3";
        }
        
        $qr_menor21 = mysql_query("SELECT  
                                    IF(data1 > DATE_SUB(CURDATE(), INTERVAL 21 YEAR), 1,0) as filho1,
                                    IF(data2 > DATE_SUB(CURDATE(), INTERVAL 21 YEAR), 1,0) as filho2,
                                    IF(data3 > DATE_SUB(CURDATE(), INTERVAL 21 YEAR), 1,0) as filho3,
                                    IF(data4 > DATE_SUB(CURDATE(), INTERVAL 21 YEAR), 1,0) as filho4,
                                    IF(data5 > DATE_SUB(CURDATE(), INTERVAL 21 YEAR), 1,0) as filho5,
                                    IF(data6 > DATE_SUB(CURDATE(), INTERVAL 21 YEAR), 1,0) as filho6,
                                    ddir_pai, ddir_mae, ddir_conjuge, portador_def1, portador_def2, portador_def3, portador_def4,  portador_def5,  portador_def6,
                                    ddir_avo_h, ddir_avo_m, ddir_bisavo_h, ddir_bisavo_m 
                                    FROM dependentes 
                                    WHERE id_bolsista = '$idclt'							
                                    AND id_projeto = '$idprojeto' $wheretipo;") or die(mysql_error()); 

        if(mysql_num_rows($qr_menor21) != 0){
             $row_menor = mysql_fetch_assoc($qr_menor21);           
             if($row_menor['filho1'] == 1 or $row_menor['portador_def1'] == 1 ){ $total_filhos_menor_21++; }           
             if($row_menor['filho2'] == 1 or $row_menor['portador_def2'] == 1){ $total_filhos_menor_21++; }
             if($row_menor['filho3'] == 1 or $row_menor['portador_def3'] == 1){ $total_filhos_menor_21++; }
             if($row_menor['filho4'] == 1 or $row_menor['portador_def4'] == 1){ $total_filhos_menor_21++; }
             if($row_menor['filho5'] == 1 or $row_menor['portador_def5'] == 1){ $total_filhos_menor_21++; }
             if($row_menor['filho6'] == 1 or $row_menor['portador_def6'] == 1){ $total_filhos_menor_21++; }  			
             if($row_menor['ddir_pai'] == 1 ){ $total_filhos_menor_21++; }  			
             if($row_menor['ddir_mae'] == 1 ){ $total_filhos_menor_21++; }  			
             if($row_menor['ddir_conjuge'] == 1 ){ $total_filhos_menor_21++; }  			
             if($row_menor['ddir_avo_h'] == 1 ){ $total_filhos_menor_21++; }  			
             if($row_menor['ddir_avo_m'] == 1 ){ $total_filhos_menor_21++; }  			
             if($row_menor['ddir_bisavo_h'] == 1 ){ $total_filhos_menor_21++; }  			
             if($row_menor['ddir_bisavo_m'] == 1 ){ $total_filhos_menor_21++; }  			

        }
        
        
	if(!empty($total_filhos_menor_21)) {
	    //echo "SELECT * FROM rh_movimentos WHERE cod = '5049' AND anobase = '$ano'";
            $result_deducao_ir = mysql_query("SELECT * FROM rh_movimentos WHERE cod = '5049' AND anobase = '$ano' AND '{$data}' BETWEEN data_ini AND data_fim");
            $row_deducao_ir = mysql_fetch_array($result_deducao_ir);	
            $valor_deducao_ir = $total_filhos_menor_21 * $row_deducao_ir['fixo'];                 
            $base -= $valor_deducao_ir;               

            $this->valor_deducao_ir_total = $valor_deducao_ir;
            $this->valor_deducao_ir_fixo  = $row_deducao_ir['fixo'];
            $this->total_filhos_menor_21  = $total_filhos_menor_21;
		
	} else {
		
            $this->valor_deducao_ir_total = 0;
            $this->valor_deducao_ir_fixo  = 0;
            $this->total_filhos_menor_21  = 0;
		
	}
        
        $data_atual = $data;
        
	$result_IR = mysql_query("SELECT * FROM rh_movimentos 
                                        WHERE cod = '5021' 
                                        AND v_ini <= '$base' AND v_fim >= '$base' 
                                        AND anobase = '$ano'
                                        AND '{$data_atual}' BETWEEN data_ini AND data_fim");
                                        
       
                                        
	$row_IR = mysql_fetch_array($result_IR);	
	$valor_IR = ($base * $row_IR['percentual']) - $row_IR['fixo']; 

        if(in_array($_COOKIE['logado'], [256])){
            $bruto = $base+$valor_deducao_ir;
            echo "<pre>CALCULO IRRF<br>
                BASE BRUTO: ".$bruto." 
                PERCENTUAL:{$row_IR['percentual']}
                DEDUCAO:{$row_IR['fixo']}
                TOTAL FILHOS:$total_filhos_menor_21
                valor_deducao_ir = $valor_deducao_ir
                BASE LIQUIDO (-DEDUCAO FILHOS): ".$base." 
                valor IR = (BASE LIQUIDO * PERCENTUAL) - DEDUCAO;
                $valor_IR = ($base * {$row_IR['percentual']}) - {$row_IR['fixo']};</pre>"; 
        }
        
	if($tipo == 'clt') {
		
            $result_recolhimentoIR = mysql_query("SELECT recolhimento_ir FROM rh_clt WHERE id_clt = '$idclt'");
            $row_recolhimentoIR    = mysql_fetch_assoc($result_recolhimentoIR);
            $recolhimento          = $row_recolhimentoIR['recolhimento_ir'];

            // Se o recolhimento não estiver vazio, soma o valor do IR mais o recolhimento
//            if(!empty($recolhimento)) {
            $valor_IR = $valor_IR + $recolhimento;
//            }

            // Se ainda assim o valor do IR mais o recolhimento for menor que 10 reais, atualiza o recolhimento 
            // e o valor do IR fica nulo
//            if($valor_IR < 10) {
//                $update_recolhimentoIR = "UPDATE rh_clt SET recolhimento_ir = '$valor_IR' WHERE id_clt = '$idclt'";
//                $valor_IR = 0;
//
//            // Se o valor do IR mais o recolhimento for maior que 10 reais e o recolhimento não estiver vazio, 
//            // o recolhimento fica nulo e o valor do IR permanece
//            } elseif((!empty($recolhimento)) and ($valor_IR > 10)) {
            $update_recolhimentoIR = "UPDATE rh_clt SET recolhimento_ir = 0 WHERE id_clt = '$idclt'";
//            }
	}
        
	
	$this->valor            = $valor_IR;
	$this->percentual	= $row_IR['percentual'];
	$this->valor_fixo_ir    = $row_IR['fixo'];
	$this->base_calculo_ir  = $base;
	$this->recolhimento_ir  = $update_recolhimentoIR;	
    }


    /**
     * MÉTODO PARA CALCULO DE SALÁRIO FAMÍLIA
     * @param type $base
     * @param type $idclt
     * @param type $idprojeto
     * @param type $data
     * @param type $contratacao
     */
    function Salariofamilia($base,$idclt,$idprojeto,$data,$contratacao) {

        $retorno = array();
        
	if(strstr($data, '/')) {
		$d   = explode('/', $data);
		$dia = $d[0];
		$mes = $d[1];
		$ano = $d[2];
	} elseif(strstr($data, '-')) {
		$d   = explode('-', $data);
		$dia = $d[2];
		$mes = $d[1];
		$ano = $d[0];
	}
        
        $qr_menor = mysql_query("SELECT  
                                IF(data1 >= DATE_SUB(CURDATE(), INTERVAL 14 YEAR), 1,0) as filho1,
                                IF(data2 >= DATE_SUB(CURDATE(), INTERVAL 14 YEAR), 1,0) as filho2,
                                IF(data3 >= DATE_SUB(CURDATE(), INTERVAL 14 YEAR), 1,0) as filho3,
                                IF(data4 >= DATE_SUB(CURDATE(), INTERVAL 14 YEAR), 1,0) as filho4,
                                IF(data5 >= DATE_SUB(CURDATE(), INTERVAL 14 YEAR), 1,0) as filho5,
                                IF(data6 >= DATE_SUB(CURDATE(), INTERVAL 14 YEAR), 1,0) as filho6
                                FROM dependentes 
                                WHERE id_bolsista = '$idclt'							
                                AND id_projeto = '$idprojeto'; ") or die(mysql_error()); 
         
        if(mysql_num_rows($qr_menor) != 0){
            
            $row_menor = mysql_fetch_assoc($qr_menor);           
            if($row_menor['filho1'] == 1){ $total_menor++; }
            if($row_menor['filho2'] == 1){ $total_menor++; }
            if($row_menor['filho3'] == 1){ $total_menor++; }
            if($row_menor['filho4'] == 1){ $total_menor++; }
            if($row_menor['filho5'] == 1){ $total_menor++; }
            if($row_menor['filho6'] == 1){ $total_menor++; }  
            
            //echo "SELECT * FROM rh_movimentos WHERE cod = '5022' AND v_ini <= '$base' AND v_fim >= '$base' AND anobase = '$ano' AND '$data' BETWEEN data_ini AND data_fim";
            $result_familia = mysql_query("SELECT * FROM rh_movimentos WHERE cod = '5022' AND v_ini <= '$base' AND v_fim >= '$base' AND anobase = '$ano' AND '$data' BETWEEN data_ini AND data_fim");
            $row_familia = mysql_fetch_array($result_familia);	  
            
            if($_COOKIE['logado'] == 179){
                echo "<pre>";
                    echo "total de filhos: " . $total_menor;
                echo "</pre>";
            }
            
            $valor_familia = $total_menor * $row_familia['fixo'];	
             
            $this->valor          = $valor_familia;
            $this->filhos_menores = $total_menor;
            $this->fixo           = $row_familia['fixo'];
            $this->id_mov         = $row_familia['id_mov'];
            $this->cod_mov        = $row_familia['cod'];
            
            $retorno = array(
                'valor' => $valor_familia,
                'filhos_menomes' => $total_menor,
                'fixo' => $row_familia['fixo'],
                'id_mov' => $row_familia['id_mov'],
                'cod_mov' => $row_familia['cod']
            );
                
        } else {
            
            $this->valor          = NULL;
            $this->filhos_menores = NULL;
            $this->fixo           = NULL;
            
            $retorno = array(
                'valor' => NULL,
                'filhos_menomes' => NULL,
                'fixo' => NULL,
            );
        }
        
        return $retorno;
    }


    /**
    * MÉTODO DE ADICIONAL NOTURNO
    * @param type $idclt
    * @param type $data
    */
    function adnoturno($idclt,$data) {

        if(empty($data)) {
                $dataexp = explode('/', date('d/m/Y'));
        } else {
                $dataexp = explode('/', $data);
        }

        $re_adnoturno = mysql_query("SELECT * FROM rh_movimentos_clt 
                                        WHERE cod_movimento = '9000' 
                                        AND id_clt = '$idclt' 
                                        AND status = '5' 
                                        AND (lancamento = '1' AND mes_mov = '".$dataexp[1]."' AND ano_mov = '".$dataexp[2]."' OR lancamento = '2')");
        
        $row_adnoturno = mysql_fetch_array($re_adnoturno);
        $this->valor = $row_adnoturno['valor_movimento'];

    }
    
    /**
     * MÉTODO CALCULO DE INSALUBRIDADE
     * @param type $idclt
     * @param type $data
     */
    function insalubridade($idclt,$data) {

        if(empty($data)) {
                $dataexp = explode('/', date('d/m/Y'));
        } else {
                $dataexp = explode('/', $data);
        }

        $re_insalubridade = mysql_query("SELECT * FROM rh_movimentos_clt 
                                        WHERE (cod_movimento = '6006' OR cod_movimento = '6007')
                                        AND id_clt = '$idclt' 
                                        AND status != '0' AND (lancamento = '1' AND mes_mov = '".$dataexp[1]."' AND ano_mov = '".$dataexp[2]."' OR lancamento = '2') ORDER BY data_movimento DESC");

        $row_insalubridade = mysql_fetch_array($re_insalubridade);
        $this->valor = $row_insalubridade['valor_movimento'];

    }


    /**
     * MÉTODO CALCULA VALOR E MESES TRABALHADOS PARA DÉCIMO TERCEIRO
     * @param type $parcela
     * @param type $data_entrada
     * @param type $ano_folha
     * @param type $mes_folha
     * @param type $salario_base
     * @param type $clt
     * @param type $meses_evento
     */
    function dt_data($parcela,$data_entrada,$ano_folha,$mes_folha,$salario_base,$clt, $meses_evento = NULL) {

	list($ano_entrada,$mes_entrada,$dia_entrada) = explode('-', $data_entrada);
        
        $ultimo_dia = cal_days_in_month(CAL_GREGORIAN, $mes_entrada, $ano_entrada);
	$dia_13 = ($ultimo_dia ==30) ? 15:16;
        
	// 2010 == 2010
	if($ano_entrada == $ano_folha) {
		
            // 12 != 12
            if($mes_entrada != $mes_folha) {
                $meses_trab = 12 - $mes_entrada;

                if($dia_entrada <= $dia_13) {
                $meses_trab += 1;
                }
            } else {
               $meses_trab += 1;
            }
		
        } else {
	    $meses_trab = 12;
	}
        
        /**
         * FEITO POR SINESIO
         * 05/12/2016
         */
        if($mes_entrada == 11 && $dia_entrada <= 15){
            $meses_trab++;
        }        
        
        if(($mes_entrada == 11 && $dia_entrada >= 1) && ($mes_entrada == 11 && $dia_entrada <= 14) ){
            $meses_trab--;
        }
		
	// Valor Décimo Terceiro		  
	$pre_valor_dt = ($salario_base / 12) * ($meses_trab - $meses_evento);
    
	// Primeira Parcela ou Segunda Parcela
	if($parcela == 1 or $parcela == 2) {
            $valor_dt = $pre_valor_dt / 2;
	
	// Integral
	} else {
            $valor_dt = $pre_valor_dt;
	}
	
	// Valores Finais de Décimo Terceiro
	$this->valor	  = $valor_dt;
	$this->meses_trab = $meses_trab;
	
    }
    
    /**
     * 
     * @param type $id_clt
     * @param type $mes
     * @param type $ano
     * @return int
     */
    function QntFaltas($id_clt, $mes, $ano){

        $qr_faltas = mysql_query("SELECT SUM(qnt) as total_falta FROM rh_movimentos_clt WHERE id_clt = '$id_clt'  AND id_mov = 62 AND mes_mov = $mes AND ano_mov = $ano AND status IN(1,5) AND nome_movimento != 'FALTA (Mês anterior)';");
        $row_falta = mysql_fetch_assoc( $qr_faltas);       
        if(mysql_num_rows($qr_faltas) == 0) { return 0; } else { return $row_falta['total_falta']; }    

    }

    /**
     * CALCULA OS MESES PARA 13, FÉRIAS E RESCISÃO
     * @param type $dt_inicial
     * @param type $dt_final
     * @param type $id_clt
     */
    function Calc_qnt_meses_13_ferias_rescisao($dt_inicial, $dt_final, $id_clt = NULL) {
        
        $begin = new DateTime($dt_inicial);
        $end = new DateTime($dt_final);
        $end = $end->modify( '+1 day' ); 

        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval ,$end);

        $mes_atual = 0;
        $count = 1;
        $m = 0;
        
        //DEBUG
        if(in_array($_COOKIE['logado'], $this->programadores)){
            echo "<br>===================================DATAS PARA CALCULO DE AVOS=========================================<br>"; 
        }
        
        foreach($daterange as $date){
            $count++;

            if($mes_atual != $date->format("m")){
                $mes_atual = $date->format("m");
                $count = 0;
            }
            
            //DEBUG
            if(in_array($_COOKIE['logado'], $this->programadores)){
               echo $date->format("d-m-Y") . "<br>";
            }
            
            if($count == 14){
                //DEBUG
                if(in_array($_COOKIE['logado'], $this->programadores)){
                    echo "+ 1 mês...<br>";
                }
                $m++;
            }

        }
        
        //DEBUG
        if(in_array($_COOKIE['logado'], $this->programadores)){
            echo "<br> Meses: " . $m  . "<br><br>";
        }
        
            
        // Calcula a diferença em segundos entre as datas
        $diferenca = strtotime($dt_final) - strtotime($dt_inicial);

        //Calcula a diferença em dias
        $dias = (floor($diferenca / 86400)) + 1;
            
        
        //debug periodos
        if(in_array($_COOKIE['logado'], $this->programadores)){
           echo $dt_inicial . ' - ' . $dt_final . ' = ' . round($dias) . ' dias. (Faltas: ' . $faltas . ') <br>';
           echo "Avos de Décimo Terceiro: " . round($m) . "<br>";
           echo "<br>=======================================================================================================<br>"; 
        }
        
        $this->meses_ativos_dt = round($m);
        
    }
    
    /**
     * CALCULA OS MESES PARA 13, FÉRIAS E RESCISÃO
     * @param type $dt_inicial
     * @param type $dt_final
     * @param type $id_clt
     * //Apenas quando nao tiver 1 periodo aquisitivo
     * @param type $dataEntrada Apenas quando $dt_inicial estiver vazia
     * @param type $dataDemissao Apenas quando $dt_final estiver vazia
     */
    function Calc_qnt_meses_13_ferias($dt_inicial, $dt_final, $id_clt = NULL, $dataEntrada, $dataDemissao) {
        
        $begin = (!empty($dt_inicial)) ? new DateTime($dt_inicial) : new DateTime($dataEntrada);
        $end = (!empty($dt_final)) ? new DateTime($dt_final) : new DateTime($dataDemissao);
        $end = $end->modify( '+1 day' ); 

        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval ,$end);

        $mes_atual = 0;
        $count = 1;
        $dias = 1;
        $m = 0;
        
        //DEBUG
        if(in_array($_COOKIE['logado'], $this->programadores)){
            echo "<br>===================================DATAS PARA CALCULO DE AVOS FERIAS=========================================<br>"; 
        }
        
        foreach($daterange as $date){
            //DEBUG
            if(in_array($_COOKIE['logado'], $this->programadores)){
               echo $date->format("d-m-Y") . "<br>";
            }
            
            if($date->format("d") == 31){
                continue;
            } 
            
            $count++;
            $dias++;
 
            
            if($count == 30){
                //DEBUG
                if(in_array($_COOKIE['logado'], $this->programadores)){
                    echo "+ 1 mêsss...<br>";
                }
                $m++;
                $count = 0;
            }

        }
        
        $dias_contabilizados = $m * 30;
         
        
        //DEBUG
        if(in_array($_COOKIE['logado'], $this->programadores)){
            echo "<br> Meses: " . $m . "<br />";
        }
        
            
        // Calcula a diferença em segundos entre as datas
        $diferenca = strtotime($dt_final) - strtotime($dt_inicial);
 
        
        //debug periodos
        if(in_array($_COOKIE['logado'], $this->programadores)){
           echo $dt_inicial . ' - ' . $dt_final . ' = ' . round($dias) . ' dias. (Faltas: ' . $faltas . ') <br>';
           echo "Avos de Férias: " . round($m) . "<br>";
           echo "<br>=======================================================================================================<br>"; 
        }

        if($m > 12) {
            
            $m -=12;
                    
        }
        
        $this->meses_ativos = round($m);
        
    }
    
    
    /**
     * MÉTODO IDENTICO DO DE CIMA... NÃO SERVE PARA PORRA NENHUMA
     * @param type $dt_inicial
     * @param type $dt_final
     * @param type $id_clt
     */
    function Calc_qnt_meses_ferias_rescisao($dt_inicial, $dt_final,$id_clt = NULL){
    
    
        list($ano_inicio,$mes_inicio,$dia_inicio) = explode('-',$dt_inicial);
        list($ano_final,$mes_final,$dia_final)    = explode('-',$dt_final);    

        $dt_inicio_seg     = mktime(0,0,0,$mes_inicio,$dia_inicio, $ano_inicio );
        $dt_fim_seg        = mktime(0,0,0,$mes_final,$dia_final, $ano_final );   
        $diferenca_meses   =  round(($dt_fim_seg - $dt_inicio_seg)/2592000);    

        for($i=0;$i<=$diferenca_meses;$i++){   

            if($i == 0){
                $data_1 =  mktime(0,0,0,($mes_inicio + $i),$dia_inicio, $ano_inicio );
            } else {
                $data_1 =  mktime(0,0,0,($mes_inicio + $i),$dia_inicio, $ano_inicio );
            }

            $data_2 =  mktime(0,0,0,($mes_inicio + ($i+1)), $dia_inicio -1, $ano_inicio );

            if($data_2 >= $dt_fim_seg){   
                $data_2 = $dt_fim_seg; 
            }

            $dias_trab = (($data_2 - $data_1)/86400) - $faltas;            

            if($_COOKIE['logado'] == 1){                      
              //   echo date('d/m/Y',$data_1).' - '.date('d/m/Y',$data_2).' = '.round($dias_trab).' dias. ('.$faltas.') <br>';                     
            }   

            if($dias_trab >=14){
                $meses_ativos +=1;          
             }   
            //debug periodos

        }

        $this->meses_ativos = $meses_ativos;
    }
    
    public function hourToSec ($qntHoras) {
        
        list($h, $m, $s) = explode(':', $qntHoras); 
        $segundos = ($h * 3600) + ($m * 60) + $s;
        
        return $segundos;
        
    }
    
    public function getDiasDsr($mes, $ano, $lista = false, $count = false){
        $query = "SELECT *
            FROM ano AS A
            WHERE MONTH(A.data) = {$mes} AND YEAR(A.data) = {$ano} AND A.fds = 1 AND WEEKDAY(A.data) = 6";
//            
        $sql = mysql_query($query) or die("ERRO getDiasDsr");
        
        if($lista){
            $result = mysql_fetch_assoc($sql);
            
            return $result;
        }elseif($count){
            $tot = mysql_num_rows($sql);
            
            if ($_COOKIE['debug'] == 1) {
                print_array("QNT DE DOMINGOS: $tot");
            }
            
            return $tot;
        }else{
            return $sql;
        }
    }
    
    public function getDiasFeriados($mes, $ano, $projeto, $lista = false, $count = false, $ini = "", $fim = "") {
        
        $and = "";
        
        if(($ini != "") && ($fim != "")){
            $and = "AND dt BETWEEN '{$ini}' AND '{$fim}'";
        }
        $query2 = mysql_query("SELECT cod_municipio FROM rhempresa");
        $row = mysql_fetch_assoc($query2);
        $arrayEmpresa = explode("-", $row['cod_municipio']);



        $query = "
            SELECT *
            FROM(
            SELECT A.*, WEEKDAY(DATE_FORMAT(A.data, '2017-%m-%d')) AS num_dia, IF(A.movel = 1, A.data, DATE_FORMAT(A.data, '2017-%m-%d')) AS dt
            FROM rhferiados AS A) AS tmp
            LEFT JOIN projeto pro ON (pro.id_projeto = $projeto)
            LEFT JOIN rhempresa rhf ON (rhf.id_projeto = pro.id_projeto)
            LEFT JOIN municipios m ON tmp.cod_municipio = m.id_municipio
            WHERE num_dia NOT IN(5,6) AND MONTH(tmp.dt) = $mes AND YEAR(tmp.dt) = $ano AND tmp.STATUS = 1 AND 
            ((tmp.tipo = 'Estadual' AND tmp.uf = rhf.uf) OR (tmp.tipo = 'Municipal' AND tmp.cod_municipio = SUBSTRING(rhf.cod_municipio, 4))
             OR (tmp.tipo = 'Nacional'));
        ";

//        if ($_COOKIE['logado'] == 299) {
//
//            echo '<pre>';
//            print_r($query);
//            echo '</pre>';
//
//        }
        $sql = mysql_query($query) or die("ERRO getFeriados");
        
        if($lista){
            $result = mysql_fetch_assoc($sql);
            
            return $result;
        }elseif($count){
            $tot = mysql_num_rows($sql);
            
            if ($_COOKIE['debug'] == 1) {
                print_array("QNT DE FERIADOS: $tot");
            }
            
            return $tot;
        }else{
            return $sql;
        }
    }
    
    public function getDsr($baseCalc, $diasTrab = 30, $mesFolha = '', $anoFolha = '', $projeto){

        $mes = $mesFolha;
        $ano = $anoFolha;

        $domingos = $this->getDiasDsr($mes, $ano, $lista = false, $count = true);

        $feriados = $this->getDiasFeriados($mes, $ano, $projeto, $lista = false, $count = true, $ini = "", $fim = "");
        
        $diasMes = cal_days_in_month ( CAL_GREGORIAN , $mes , $ano );
        
        if ($_COOKIE['debug'] == 666) {
            echo '<pre>';
            print_r("DIAS NO MÊS: $diasMes");
            echo '</pre>';
        }
        $diasDsr = $domingos + $feriados;
        $diasUteis = $diasMes - $diasDsr;
        $valor_dsr = ($baseCalc/$diasUteis) * $diasDsr;
        
//        if ($_COOKIE['logado'] == 299) {
//            echo '<pre>';
//            print_r([$diasDsr,$diasUteis,$domingos,$feriados]);
//            echo '</pre>';
//        }
        
        $resultado['valor_integral'] = number_format($valor_dsr,2,'.','') ;
        
//        print_array(["Domingos: $domingos", 
//                     "Feriados: $feriados", 
//                     "Dias no Mês: $diasMes", 
//                     "Dias Úteis: $diasUteis = $diasMes - ($domingos + $feriados)",
//                     "Valor do DSR: $valor_dsr = ($baseCalc/$diasUteis) * $diasDsr",
//                     "Valor Proporcional: $valorProporcional = ($valor_dsr/$diasMes) * $diasTrab"]);
        
        return $resultado; 
    }
    
    public function getHorasParaCalculo($qntHoras) {

//        echo $qntHoras . '<br>';
        list($qnt_hora, $qnt_minuto) = explode(':', $qntHoras);
        $totalQnt = $qnt_hora + ($qnt_minuto / 60);
        $valorCalc = $valor_hora;

        return $totalQnt;
    }
    
    /**
     * Converte o valor no formato HH:MM:SS para decimal que pode ser aplicado em calculo.
     */
    public function getHorasParaCalculoMedia ($qntHoras) {
        
        $segundos = $this->hourToSec($qntHoras);
        $mediaDeSegundos = $segundos / 12;
        $mediaDeHoras = gmdate("H:i", $mediaDeSegundos);
        list($qnt_hora, $qnt_minuto) = explode(':', $mediaDeHoras);
        $totalQnt = $qnt_hora + ($qnt_minuto / 60);
        
        return $totalQnt;
    }

    public function getMediaHoraExtra($valorHora, $qntHoras, $percentual) {
//        echo "-- MEDIA DE HORA EXTRA --";
//        print_array(["valor da hora: $valorHora","Qnt de Meses: 12","Qnt de Horas: $qntHoras","Percentual: $percentual"]);
        
        $totalQnt = $this->getHorasParaCalculoMedia($qntHoras);
//        echo "-- HORAS PARA CALCULO --";
//        print_array($totalQnt);
        
        $valorHoraExtra = ($valorHora * $percentual) + $valorHora;
//        echo "-- VALOR DA HORA EXTRA --";
//        print_array($valorHoraExtra);
        
        $valorMedia = $valorHoraExtra * $totalQnt;
//        echo "-- VALOR DA MÉDIA --";
//        print_array($valorMedia);
      
        return $valorMedia;
    }

    public function getMediaAdicionalNoturno($valorHora, $qntHoras, $percentual) {
//        echo "-- MEDIA DE ADICIONAL NOTURNO --";
//        print_array(["valor da hora: $valorHora","Qnt de Meses: 12","Qnt de Horas: $qntHoras","Percentual: $percentual"]);
        
        $totalQnt = $this->getHorasParaCalculoMedia($qntHoras);
//        echo "-- HORAS PARA CALCULO --";
//        print_array($totalQnt);
        
        $valorAdicionalNoturno = $valorHora * $percentual;
//        echo "-- VALOR DO ADICIONAL NOTURNO --";
//        print_array($valorAdicionalNoturno);
        
        $valorMedia = $valorAdicionalNoturno * $totalQnt;
//        echo "-- VALOR DA MÉDIA --";
//        print_array($valorMedia);
      
        return $valorMedia;
    }
    
    public function getMediaAdicionalProntidao($valorHora, $qnt) {
        
        $totalQnt = $this->getHorasParaCalculoMedia($qnt);
//        echo "-- HORAS PARA CALCULO --";
//        print_array($totalQnt);
        
        $valorAdicionalProntidao = ($valorHora * 2) / 3;
//        echo "-- VALOR DO ADICIONAL DE PRONTIDÃO --";
//        print_array($valorAdicionalProntidao);
        
        $valorMedia = $valorAdicionalProntidao * $totalQnt;
//        echo "-- VALOR DA MÉDIA --";
//        print_array($valorMedia);
      
        return $valorMedia;
    }
    
    public function getMediaSobreaviso($valorHora, $qnt) {
        
        $totalQnt = $this->getHorasParaCalculoMedia($qnt);
//        echo "-- HORAS PARA CALCULO --";
//        print_array($totalQnt);
        
        $valorAdicionalProntidao = $valorHora / 3;
//        echo "-- VALOR DO ADICIONAL DE PRONTIDÃO --";
//        print_array($valorAdicionalProntidao);
        
        $valorMedia = $valorAdicionalProntidao * $totalQnt;
//        echo "-- VALOR DA MÉDIA --";
//        print_array($valorMedia);
      
        return $valorMedia;
    }
    
    public function getMediaHoraExtraAdNoturno ($valorHora, $qntHoras, $percHoraExtra, $percAdNoturno) {
//        echo "-- MEDIA DE ADICIONAL NOTURNO --";
//        print_array(["valor da hora: $valorHora","Qnt de Meses: 12","Qnt de Horas: $qntHoras","Percentual Hora Extra: $percHoraExtra", "Percentual Ad Noturno: $percAdNoturno"]);
        
        $totalQnt = $this->getHorasParaCalculoMedia($qntHoras);
//        echo "-- HORAS PARA CALCULO --";
//        print_array($totalQnt);

        $valorHoraNoturna = $valorHora * $percAdNoturno;
//        echo "-- VALOR DA HORA NOTURNA --";
//        print_array($valorHoraNoturna);
        
        $valorHoraExtra = ($valorHoraNoturna + $valorHora) + (($valorHoraNoturna + $valorHora) * $percHoraExtra);
//        echo "-- VALOR DA HORA EXTRA NOTURNA --";
//        print_array($valorHoraExtra);
        
        $valorMedia = $valorHoraExtra * $totalQnt;
//        echo "-- VALOR DA MÉDIA --";
//        print_array($valorMedia);
        
        return $valorMedia;
        
    }
    
    public function getMedia ($valorTotal) {
        
        $valorMedia = $valorTotal / 12;
        
        return $valorMedia;
        
    }
    
    

}

?>