<?php
// Iniciamos o "contador"
list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;
 

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/CalculoFolhaClass.php');

$id_clt = 4808;
$mes = 11;
$ano = 2014;
$salario_contratual = '';

$qr_eventos = mysql_query("SELECT * FROM rh_eventos WHERE cod_status IN('20','30',50,'51','52','80','90','100','110')  AND status = '1' AND (YEAR(data) = '$ano' OR YEAR(data_retorno) >= '$ano') AND id_clt = $id_clt
ORDER BY id_evento DESC ");
while($row_evento = mysql_fetch_assoc($qr_eventos)){
    
    $meses = $row_evento['dias']/30;
    $dataIni = explode('-', $row_evento['data']);   
    
    
    $array_teste[$row_evento['cod_status']][$row_evento['id_evento']]['nome'] = $row_evento['nome_status'];
    $array_teste[$row_evento['cod_status']][$row_evento['id_evento']]['data_inicio'] = $row_evento['data'];
    $array_teste[$row_evento['cod_status']][$row_evento['id_evento']]['data_fim'] = $row_evento['data_retorno'];
    $array_teste[$row_evento['cod_status']][$row_evento['id_evento']]['dias'] = $row_evento['dias'];
    $array_teste[$row_evento['cod_status']][$row_evento['id_evento']]['meses'] = $meses;
    
 
    
    for($i=0; $i<=$meses;$i++ ){
      
       if($i==0){
             $dataIniSeg = mktime(0, 0, 0, $dataIni[1], $dataIni[2], $dataIni[0]); 
       } else {
             $dataIniSeg   = mktime(0, 0, 0, $dataIni[1]+$i, 1, $dataIni[0]); 
       }
       
       $dataIniCalc  = explode('-',date('Y-m-d', $dataIniSeg));       
       $ultimoDiaMes = cal_days_in_month(CAL_GREGORIAN, $dataIniCalc[1], $dataIniCalc[0]);       
       $dataFimSeg   = mktime(0,0,0,$dataIniCalc[1], $ultimoDiaMes,$dataIniCalc[0]);       
       $dias         = round(($dataFimSeg - $dataIniSeg)/86400) +1;
        
       
        echo date('d/m/Y', $dataIniSeg).' - '.date('d/m/Y', $dataFimSeg).'<br>';
       
        $periodos['inicio'] = date('d/m/Y', $dataIniSeg);
        $periodos['fim']    = date('d/m/Y', $dataFimSeg);
        $periodos['dias']   = $dias;
        $periodos['avo_dt']  = ($dias >= 15)?1:'';
        
        $array_teste[$row_evento['cod_status']][$row_evento['id_evento']]['periodos'][] = $periodos;
        $total_dias += $dias;
        $total_avos += $periodos['avo_dt'];
    }
    $array_teste[$row_evento['cod_status']][$row_evento['id_evento']]['total_dias'] = $total_dias;
    $array_teste[$row_evento['cod_status']][$row_evento['id_evento']]['total_avos'] = $total_avos;
    unset($total_dias,$total_avos);
    echo '<br>';
    
    
    
    
}

echo '<pre>';
    print_r($array_teste);
echo '</pre>';

?>