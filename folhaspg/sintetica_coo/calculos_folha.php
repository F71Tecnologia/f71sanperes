<?php

// Consulta de Dados do Participante
$qr_cooperado = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$cooperado'");
$row_cooperado = mysql_fetch_array($qr_cooperado);

// Buscando a Atividade do Participante e o Salário Limpo
$qr_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_cooperado[id_curso]'");
$row_curso = mysql_fetch_array($qr_curso);

// Selecione a Cooperativa de cada Participante 
$qr_cooperativa = mysql_query("SELECT * FROM cooperativas WHERE id_coop = '$row_cooperado[id_cooperativa]'");
$row_cooperativa = mysql_fetch_array($qr_cooperativa);

// Variáveis da Data de Entrada
list($ano_entrada, $mes_entrada, $dia_entrada) = explode('-', $row_cooperado['data_entrada']);

// Meses Trabalhados para Décimo Terceiro
if ($row_folha['terceiro'] == 1) {
    if ($ano_entrada == $ano) {
        if ($mes_entrada != $mes) {
            $meses_trabalhados = 12 - $mes_entrada;
        } else {
            $meses_trabalhados = 0;
        }
        if ($dia_entrada >= 15) {
            $meses_trabalhados += 1;
        }
    } else {
        $meses_trabalhados = 12;
    }
}

// Horas Trabalhadas
if (!empty($row_participante['faltas'])) {
    $horas_trabalhadas = $row_participante['faltas'];
} else {
    $horas_trabalhadas = $row_curso['hora_mes'];
}

if($row_participante['valor_hora'] == "0.00") {
    // Valor por Hora
    if ($row_curso['hora_mes'] > 0) {
        $valor_hora = $row_curso['salario'] / $row_curso['hora_mes'];
        $valor_hora = number_format($valor_hora,4,'.','');
    } else {
        $valor_hora = $row_curso['salario'];
    }
} else {
    $valor_hora = $row_participante['valor_hora'];
}

// Salário Base
if ($row_folha['terceiro'] == 1) {
    $salario_base = ((($horas_trabalhadas * $valor_hora)) / 12) * $meses_trabalhados;
} else {
    $salario_base = $horas_trabalhadas * $valor_hora;
}



//if($cooperado == 9495){
    
    //echo number_format($valor_hora,2,'.','').'<br>';
//}
//echo $salario_base." = ".$horas_trabalhadas." * ".$valor_hora;exit;

// Base de INSS
$base_inss = $salario_base;

// Calculando Rendimentos
$rendimentos = $row_participante['adicional'] + $bonificacao;
$base_inss += $rendimentos;

// Calculando Descontos
$descontos = $row_participante['desconto'];
$base_inss -= $descontos;


if ($row_folha['terceiro'] != 1) {

    // Calculando INSS
    $qr_inss = mysql_query("SELECT faixa, fixo, percentual, piso, teto
                                                           FROM rh_movimentos
                                                          WHERE cod = '5024'
                                                                AND '$row_folha[data_inicio]' BETWEEN data_ini AND data_fim");
    $row_inss = mysql_fetch_array($qr_inss);
    
    $minimo_inss = $row_inss['piso'];
    $maximo_inss = $row_inss['teto'];


    // Verifica se o INSS é Definido ou Percentual
    if ($row_cooperado['tipo_inss'] == 1) { // Valor Definido
        $inss = $row_cooperado['inss'];
    } else { // Valor Percentual
        if ($row_cooperado['tipo_contratacao'] == 3) {
            //$taxa_inss = $row_cooperado['inss'] / 100;
            $taxa_inss = $row_cooperado['inss'] / 100;
        } else {
            $taxa_inss = 0.2;
        }

        $inss = $base_inss * $taxa_inss;
        
        if(isset($maximo_inss) && !empty($maximo_inss)){
            if ($inss > $maximo_inss) {
                $inss = $maximo_inss;
            }
        }
    }
    
    if($_COOKIE['logado'] == 179 ){
        echo "<pre>";
//            print_r("Teto Inss: " . $maximo_inss . "<br />");
//            print_r("Tipo Inss: " . $row_cooperado['tipo_inss'] . "<br />");
//            print_r("INSS: " . $row_cooperado['inss']. "<br />");
//            print_r("Taxa INSS: " . $row_cooperado['inss'] / 100 . "<br />");
//            print_r("Tipo Contrata��o: " . $row_cooperado['tipo_contratacao'] . "<br />");
//            print_r("Base INSS: " . $base_inss . "<br />");
//            print_r("Valor INSS Final: " . $inss . "<br />");
//            print_r($row_participante);
            
        echo "</pre>";
    }
    
    // Ajuda de Custo
    $ajuda_custo = $row_participante['ajuda_custo'];
}

// IRRF
$base_irrf = $base_inss - $inss;
$minimo_irrf = 10.00;

// Vestígio de IRRF do mês anterior
$qr_vestigio = mysql_query("SELECT irrf FROM folha_cooperado WHERE mes = '$mes_anterior' AND id_autonomo = '$cooperado' 
AND status = '3' AND irrf <= '$minimo_irrf'");
$row_vestigio = mysql_fetch_array($qr_vestigio);
$total_vestigio = mysql_num_rows($qr_vestigio);
$valor_vestigio = $row_vestigio['irrf'];

// Calculando IRRF
$Calc->MostraIRRF($base_irrf, $cooperado, $row_folha['projeto'], $data_inicio,'coop');
$irrf = $Calc->valor + $valor_vestigio;

// Esta variável será utilizada somente para gravar na base de dados
$gravar_irrf = $irrf;

if ($irrf < $minimo_irrf) {
    $irrf = 0;
}

$taxa_irrf = $row_IR['percentual'] * 100;

// Quota
// Comentado a pedido da SHIRLEY no dia 27/06/2016
//$qr_valor_quota_paga = mysql_query("SELECT SUM(a.quota) FROM folha_cooperado a INNER JOIN folhas b ON a.id_folha = b.id_folha WHERE a.id_autonomo = '$cooperado' AND a.quota != '0.00' AND a.status = '3' AND b.status = '3'");
//$valor_quota_paga = @mysql_result($qr_valor_quota_paga, 0);
//
//$valor_quota_limite = $row_cooperado['cota'];
//$numero_parcelas = $row_cooperado['parcelas'];
//@$valor_parcela = $valor_quota_limite / $numero_parcelas;
//
//if ($valor_quota_paga < $valor_quota_limite) {
//
//    if (($valor_quota_paga + $valor_parcela) > $valor_quota_limite) {
//
//        $valor_quota = $valor_quota_limite - $valor_quota_paga;
//    } else {
//
//        $valor_quota = $valor_parcela;
//
//        if ($row_folha['terceiro'] == 1) {
//            $valor_quota = 0;
//        }
//    }
//
//    $parcela_quota = mysql_num_rows(mysql_query("SELECT * FROM folha_cooperado a INNER JOIN folhas b ON a.id_folha = b.id_folha WHERE a.id_autonomo = '$cooperado' AND a.quota != '0.00' AND a.status = '3' AND b.status = '3'")) + 1;
//} else {

    $valor_quota = 0;
    $parcela_quota = 0;
//}

// Liquido
$liquido = $salario_base + $rendimentos - $descontos - $inss - $irrf + $valor_quota + $ajuda_custo;


// Cálculos da Cooperativa
$taxa_cooperativa = $row_cooperativa['taxa'];
$taxa_operacional = $base_inss * $taxa_cooperativa;
$nota_fiscal = $liquido + $taxa_operacional + $inss + $irrf + $valor_quota;
//$nota_fiscal = $salario_base + $rendimentos + $quota - $descontos - $inss - $irrf;
?>
