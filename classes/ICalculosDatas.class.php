<?php

abstract class ICalculosDatas {
   /** *  retorna o ultimo dia do mes @param $mes = mes da consulta, @param completa = retorna data completa */
    function getUltimoDiaMes( $mes , $ano=FALSE, $completa=FALSE) {
        $ano = ($ano) ? $ano : date("Y"); // Ano atual
        $dia = date("t", mktime(0, 0, 0, $mes, '01', $ano));
        if($completa){
          $dia .= '/'.$mes.'/'.$ano;  
        }
        return $dia;
    }
//    /** pega todos os feriados **/
    function getFeriados($data_inicial, $data_final, $retorno_completo = FALSE, $timestamp = FALSE) {

        $data_inicial = ($timestamp) ? array_reverse(explode('-', $data_inicial)) : explode('/', $data_inicial);
        $data_final = ($timestamp) ? array_reverse(explode('-', $data_final)) : explode('/', $data_final);

        $this->sql = 'SELECT nome, tipo, DAY(data) AS dia, MONTH(data) AS mes, YEAR(data) AS ano
                FROM rhferiados
                WHERE IF(movel=1, YEAR(`data`) BETWEEN ' . $data_inicial[2] . ' AND ' . $data_final[2] . ',1=1) 
                AND MONTH(`data`) BETWEEN ' . $data_inicial[1] . ' AND ' . $data_final[1] . ' 
                AND DAY(`data`) BETWEEN ' . $data_inicial[0] . ' AND ' . $data_final[0] . ' 
                AND `status`=1 ORDER BY mes';
        $qr = mysql_query($this->sql);
        $arr = array();
        while ($row = mysql_fetch_array($qr)) {
            if ($retorno_completo) {
                $arr[] = array('nome' => utf8_encode($row['nome']), 'tipo' => $row['tipo'], 'dia' => str_pad($row['dia'], 2, "0", STR_PAD_LEFT), 'mes' => str_pad($row['mes'], 2, "0", STR_PAD_LEFT), 'ano' => $data_inicial[2]);
            }else{
                $arr[] = str_pad($row['dia'], 2, "0", STR_PAD_LEFT) . '/' . str_pad($row['mes'], 2, "0", STR_PAD_LEFT) . '/'.$data_inicial[2];
            }
        }
        return $arr;
    }
    function getDiasUteis($data_inicial, $data_final, $array_feriados = array(), $timestamp = FALSE) {
        $data_inicial = ($timestamp) ? array_reverse(explode('-', $data_inicial)) : explode('/', $data_inicial);
        $data_final = ($timestamp) ? array_reverse(explode('-', $data_final)) : explode('/', $data_final);
        $feriados = array();
        if ($timestamp) {
            foreach ($array_feriados as $feriado) {
                $feriados[] = implode('/', array_reverse(explode('-', $data_inicial)));
            }
            $array_feriados = $feriados;
        }
        $arr_fim_semana = array('6', '7');
        $uteis = array();
        for ($a = $data_inicial[2]; $a <= $data_final[2]; $a++) {
            for ($m = $data_inicial[1]; $m <= $data_final[1]; $m++) {
                for ($d = $data_inicial[0]; $d <= $data_final[0]; $d++) {
                    $dia_semana = date('N', strtotime($a . '-' . $m . '-' . $d));
                    if (!in_array($dia_semana, $arr_fim_semana) && !in_array(str_pad($d, 2, "0", STR_PAD_LEFT) . '/' . str_pad($m, 2, "0", STR_PAD_LEFT) . '/' . $a, $array_feriados)) {
                        $uteis[] = str_pad($d, 2, "0", STR_PAD_LEFT) . '/' . str_pad($m, 2, "0", STR_PAD_LEFT) . '/' . $a;
                    }else{
//                        $uteis[] = 'final de semana';
                    }
                }
            }
        }
        return $uteis;
    }
    function checaDataFinalSemana($data, $timestamp = FALSE) {
        $data = ($timestamp) ? array_reverse(explode('-', $data)) : explode('/', $data);
        $dia_semana = date('N', strtotime($data[2] . '-' . $data[1] . '-' . $data[0]));
        return ($dia_semana == 6 || $dia_semana == 7) ? TRUE : FALSE;
    }
    function diferencaDias($data_inicial, $data_final, $timestamp = FALSE) {
        $data_inicial = ($timestamp) ? array_reverse(explode('-', $data_inicial)) : explode('/', $data_inicial);
        $data_final = ($timestamp) ? array_reverse(explode('-', $data_final)) : explode('/', $data_final);
        $time_inicial = mktime(0, 0, 0, $data_inicial[1], $data_inicial[0], $data_inicial[2]);
        $time_final = mktime(0, 0, 0, $data_final[1], $data_final[0], $data_final[2]);
        $diferenca = ($time_final - $time_inicial);
        $diferenca_dias = (int) floor($diferenca / (60 * 60 * 24)); // 225 dias  
        $diferenca_dias = $diferenca_dias + 1;
        return $diferenca_dias;
    }
    function somarDias($quantidade_dia = 0, $data = NULL, $delimiter = "-", $timestamp = TRUE) {
        $data = ($data != NULL) ? explode($delimiter, $data) : array(date('Y'), date('m'), date('d'));
        if (!$timestamp && !empty($data)) {
            $data = array_reverse($data);
        }
        return date('d/m/Y', mktime(0, 0, 0, $data[1], $data[2] + $quantidade_dia, $data[0]));
    }
}