<?php

class CalendarioClass {

    private static $data_inicial_calculada;
    private static $data_final_calculada;
    private static $data_inicial;
    private static $data_final;
    private static $dias_uteis;

    private function exec($sql) {
        $qr = mysql_query($sql);
        $arr = array();
        while ($row = mysql_fetch_array($qr)) {
            $arr[] = $row;
        }
        return $arr;
    }

    static function getDataInicial($dataCalculada = FALSE) {
        return ($dataCalculada) ? self::$data_inicial_calculada : self::$data_inicial;
    }

    static function getDataFinal($dataCalculada = FALSE) {
        return ($dataCalculada) ? self::$data_final_calculada : self::$data_final;
    }

    static function getDiasUties() {
        return self::$dias_uteis;
    }

    static function getTotalDiasUteis() {
        return count(self::$dias_uteis);
    }

    static function carrega($dataInicial, $dataFinal = FALSE, $calcularFeriados = FALSE, $diaFlag = array('6', '7')) {

        if ($dataFinal == FALSE) {
            $ultimo_dia = self::getUltimoDiaMes($dataInicial['mes'], $dataInicial['ano']);
            $dataFinal = array('dia' => $ultimo_dia, 'mes' => $dataInicial['mes'], 'ano' => $dataInicial['ano']);
        }

        $dias = self::getDias($dataInicial, $dataFinal, $calcularFeriados, $diaFlag);

        self::$dias_uteis = $dias;
        self::$data_inicial = $dataInicial['dia'] . '/' . $dataInicial['mes'] . '/' . $dataInicial['ano'];
        self::$data_final = $dataFinal['dia'] . '/' . $dataFinal['mes'] . '/' . $dataFinal['ano'];
        self::$data_inicial_calculada = $dias[0];
        self::$data_final_calculada = $dias[(count($dias) - 1)];
    }

    static function getDias($dataInicial, $dataFinal = FALSE, $calcularFeriados = FALSE, $diaFlag = array('6', '7')) {

        if ($dataFinal == FALSE) {
            $ultimo_dia = self::getUltimoDiaMes($dataInicial['mes'], $dataInicial['ano']);
            $dataFinal = array('dia' => $ultimo_dia, 'mes' => $dataInicial['mes'], 'ano' => $dataInicial['ano']);
        }

        $feriados = array();

        if ($calcularFeriados) {

            if (!is_null($dataInicial['ano']) && !empty($dataInicial['mes']) && !is_null($dataFinal['dia']) && !is_null($dataFinal['ano']) && !empty($dataFinal['mes']) && !is_null($dataFinal['dia'])) {

                $sql = 'SELECT nome, tipo, DAY(data) AS dia, MONTH(data) AS mes, YEAR(data) AS ano
                        FROM rhferiados
                        WHERE IF(movel=1, YEAR(`data`) BETWEEN ' . $dataInicial['ano'] . ' AND ' . $dataFinal['ano'] . ',1=1) 
                        AND MONTH(`data`) BETWEEN ' . $dataInicial['mes'] . ' AND ' . $dataFinal['mes'] . ' 
                        AND DAY(`data`) BETWEEN ' . $dataInicial['dia'] . ' AND ' . $dataFinal['dia'] . ' 
                        AND `status`=1 ORDER BY mes';
                $arr_feriados = self::exec($sql);
                for ($x = 0; $x < count($arr_feriados); $x++) {
                    $feriados[$x] = str_pad($arr_feriados[$x]['mes'], 2, "0", STR_PAD_LEFT) . '-' . str_pad($arr_feriados[$x]['dia'], 2, "0", STR_PAD_LEFT);
                }
            }
        }

        $dias = array();

        for ($a = $dataInicial['ano']; $a <= $dataFinal['ano']; $a++) {

            $mes_inicio = ($dataInicial['ano'] == $a) ? $dataInicial['mes'] : '01';
            $mes_final = ($dataInicial['ano'] == $a) ? $dataFinal['mes'] : '12';

            for ($m = $mes_inicio; $m <= $mes_final; $m++) {
                if (!is_null($m) && !empty($m) && !is_null($a) && !empty($a)) {
                    for ($d = $dataInicial['dia']; $d <= date("t", mktime(0, 0, 0, $m, '01', $a)); $d++) {

                        $ano = $a;
                        $mes = str_pad($m, 2, "0", STR_PAD_LEFT);
                        $dia = str_pad($d, 2, "0", STR_PAD_LEFT);

                        if ($ano . '-' . $mes . '-' . $dia <= $dataFinal['ano'] . '-' . $dataFinal['mes'] . '-' . $dataFinal['dia']) {
                            $diaCodSemana = date('N', strtotime($ano . '-' . $mes . '-' . $dia));
                            if (!in_array($diaCodSemana, $diaFlag) && !in_array($mes . '-' . $dia, $feriados)) { //!in_array($mesDia, $feriados) && !
                                $dias[] = str_pad($d, 2, "0", STR_PAD_LEFT) . '/' . str_pad($m, 2, "0", STR_PAD_LEFT) . '/' . $a . ' =>' . $diaCodSemana;
                            }
                        }
                    }
                }
            }
        }

        return $dias;
    }

    /**     *  retorna o ultimo dia do mes @param $mes = mes da consulta, @param completa = retorna data completa */
    static function getUltimoDiaMes($mes, $ano = FALSE, $completa = FALSE) {
        $ano = ($ano) ? $ano : date("Y"); // Ano atual
        $dia = date("t", mktime(0, 0, 0, $mes, '01', $ano));
        if ($completa) {
            $dia .= '/' . $mes . '/' . $ano;
        }
        return $dia;
    }

    /** pega todos os feriados * */
    static function getFeriados($data_inicial, $data_final) {

        $sql = 'SELECT nome, tipo, DAY(data) AS dia, MONTH(data) AS mes, YEAR(data) AS ano
                FROM rhferiados
                WHERE IF(movel=1, YEAR(`data`) BETWEEN ' . $data_inicial['ano'] . ' AND ' . $data_final['ano'] . ',1=1) 
                AND MONTH(`data`) BETWEEN ' . $data_inicial['mes'] . ' AND ' . $data_final['mes'] . ' 
                AND DAY(`data`) BETWEEN ' . $data_inicial['dia'] . ' AND ' . $data_final['dia'] . ' 
                AND `status`=1 ORDER BY mes';
        exec($sql);
    }

    static function checaDataFinalSemana($data, $timestamp = FALSE) {
        $data = ($timestamp) ? array_reverse(explode('-', $data)) : explode('/', $data);
        $dia_semana = date('N', strtotime($data[2] . '-' . $data[1] . '-' . $data[0]));
        return ($dia_semana == 6 || $dia_semana == 7) ? TRUE : FALSE;
    }

    static function diferencaDias($data_inicial, $data_final, $timestamp = FALSE) {
        $data_inicial = ($timestamp) ? array_reverse(explode('-', $data_inicial)) : explode('/', $data_inicial);
        $data_final = ($timestamp) ? array_reverse(explode('-', $data_final)) : explode('/', $data_final);
        $time_inicial = mktime(0, 0, 0, $data_inicial[1], $data_inicial[0], $data_inicial[2]);
        $time_final = mktime(0, 0, 0, $data_final[1], $data_final[0], $data_final[2]);
        $diferenca = ($time_final - $time_inicial);
        $diferenca_dias = (int) floor($diferenca / (60 * 60 * 24)); // 225 dias  
        $diferenca_dias = $diferenca_dias + 1;
        return $diferenca_dias;
    }

    static function somarDias($quantidade_dia = 0, $data = NULL, $delimiter = "-", $timestamp = TRUE) {
        $data = ($data != NULL) ? explode($delimiter, $data) : array(date('Y'), date('m'), date('d'));
        if (!$timestamp && !empty($data)) {
            $data = array_reverse($data);
        }
        return date('d/m/Y', mktime(0, 0, 0, $data[1], $data[2] + $quantidade_dia, $data[0]));
    }

}
