<?php
//$qr_eventos  = mysql_query("SELECT * FROM rh_eventos WHERE cod_status IN('20','30','51','52','80','90','100','110') 
//    AND id_clt = '$clt' AND status = '1' AND (YEAR(data) = '$ano' OR YEAR(data_retorno) = '$ano') ORDER BY id_evento DESC LIMIT 1");

//VARI�VEIS GLOBAIS


$regra_quinze_dias = true;

$qr_eventos  = mysql_query("SELECT *,
        DATE_ADD(data, INTERVAL 15 DAY) AS dataInss
        FROM rh_eventos
        WHERE cod_status NOT IN('10','40','50') AND id_clt = {$clt} AND STATUS = '1' AND 
        ((YEAR(data) = '".date('Y')."' OR YEAR(data_retorno) = '".date('Y')."') || '{$ano}-{$mes}' BETWEEN DATE_FORMAT(data, '%Y-%m') AND DATE_FORMAT(data_retorno, '%Y-%m')) 
        ORDER BY data_retorno DESC");
 
$num_eventos = mysql_num_rows($qr_eventos);

// Eventos que Desconta
$codigos_eventos = array("20","21", "30", "50", "54","67","68","69","80","90","70","100"); 
$codigos_15_dias = array("20","21", "90");

$inicio_folha = $ano.'-01-01';
$fim_folha    = $ano.'-12-31';

if(!empty($num_eventos)) { 
    
        // Sinalizando Evento
	$sinaliza_evento = true;
	
	while($row_evento = mysql_fetch_array($qr_eventos)) {
            
            
            if($_COOKIE['logado'] == 179){
                echo "<pre>";
                    print_r($row_evento);
                echo "</pre>";
            }            
            
            if(in_array($row_evento['cod_status'],$codigos_eventos)) {
	
                // Início do Evento entre o Início e Fim da Folha
                if($row_evento['data'] >= $inicio_folha and $row_evento['data'] <= $fim_folha) {

                        $inicio = $row_evento['data'];

                        // Se o Fim do Evento for antes do Fim da Folha	
                        if($row_evento['data_retorno'] < $fim_folha) {
                                $fim = $row_evento['data_retorno'];
                        // Fim do Evento depois do Fim da Folha
                        } else {
                                $fim = $fim_folha;
                        }

                        $dias_evento += abs((int)floor((strtotime($inicio) - strtotime($fim)) / 86400));




                // Fim do Evento entre o Início e Fim da Folha
                } elseif($row_evento['data_retorno'] >= $inicio_folha and $row_evento['data_retorno'] <= $fim_folha ) {

                        // Se o Início do Evento for depois do Início da Folha
                        if($row_evento['data'] > $inicio_folha) {
                                $inicio = $row_evento['data'];
                        // Início do Evento antes do Início da Folha
                        } else {
                                $inicio = $inicio_folha;
                        }

                        $fim = $row_evento['data_retorno'];

                        $dias_evento += abs((int)floor((strtotime($inicio) - strtotime($fim)) / 86400));

                }else{
                    $dias_evento += $row_evento['dias'];
                }
            }
            
            if($_COOKIE['logado'] == 179){
//                echo "<br />";
//                echo ($dias_evento - 15)  ." - ". floor($dias_evento/30) ;
//                echo "<br />";
//                echo $date1 = $row_evento['data'];
//                echo "<br />";
//                echo $date1 = $row_evento['dataInss'];
//                echo "<br />";
//                echo $date2 = $row_evento['data_retorno'];
//                echo "<br />";
//                $ts1 = strtotime($date1);
//                $ts2 = strtotime($date2);
//                $seconds_diff = $ts2 - $ts1;
//                echo "<br />";
//                echo 12 - (12 - (floor($seconds_diff/3600/24/30)));
//                echo "<br />";
            }
            
            
            
            
            if(($dias_evento/30) < 1){
                $meses_evento = 0;
                unset($sinaliza_evento);
            }else{
                
                
                $ultimo_dia_ano = "31/12/" . date("Y");
                $primeiro_dia_ano = "01/01/" . date("Y");
                
                if(date("Y",strtotime(str_replace("/", "-", $row_evento['dataInss']))) < date("Y")){
                    $data1 =  date("d/m/Y",strtotime(str_replace("/", "-", $primeiro_dia_ano)));
                }else{
                    $data1 =  date("d/m/Y",strtotime(str_replace("/", "-", $row_evento['dataInss'])));
                }
                
                if(in_array($row_evento['cod_status'], $codigos_15_dias)){
                    $data1 =  date("d/m/Y",strtotime(str_replace("/", "-", $row_evento['dataInss'])));
                }else{
                    $data1 =  date("d/m/Y",strtotime(str_replace("/", "-", $row_evento['data'])));
                }
                
                if(date("Y",strtotime(str_replace("/", "-", $row_evento['data_retorno']))) > date("Y")){
                    $data2 =  date("d/m/Y",strtotime(str_replace("/", "-", $ultimo_dia_ano)));
                }else{
                    $data2 =  date("d/m/Y",strtotime(str_replace("/", "-", $row_evento['data_retorno'])));
                }
                
                if($row_evento['data_retorno'] == '' || $row_evento['data_retorno'] == '0000-00-00'){
                    $data2 =  date("d/m/Y",strtotime(str_replace("/", "-", $ultimo_dia_ano)));
                }
                
                $meses = mesesdiferenca($data1,$data2);
                
                if($_COOKIE['logado'] == 179){
                    echo "<pre>";
                    print_r($row_evento);
                    echo "</pre>";
                    
                    echo "<br />";
                    echo $data1;
                    echo "<br />";
                    echo $data2;
                    echo "<br>";
                    echo $meses;
                }
                
                
                //VARI�VEL CONFIGURADA NO COME�O DO ARQUIVO
                if($regra_quinze_dias){
                    
                    if(in_array($row_evento['cod_status'], $codigos_15_dias)){
                        $dias_i = (int) date("d",strtotime(str_replace("/", "-", $row_evento['dataInss'])));
                        $ano_i = (int) date("Y",strtotime(str_replace("/", "-", $row_evento['dataInss'])));
                    }else{
                        $dias_i = (int) date("d",strtotime(str_replace("/", "-", $row_evento['data'])));
                        $ano_i = (int) date("Y",strtotime(str_replace("/", "-", $row_evento['data'])));
                    }
                    if($dias_i > 15 and $ano_i == $ano) { 
                        $meses--;
                    }
                    
                    $dias_f = (int) date("d",strtotime(str_replace("/", "-", $row_evento['data_retorno'])));
                    $ano_f = (int) date("Y",strtotime(str_replace("/", "-", $row_evento['data_retorno'])));
                     
                    if($dias_f <= 15 and $ano_f == $ano){
                        $meses--;                     
                    }
                } 

                $meses_evento += $meses;
                
                
                
                
            }
            
            if($_COOKIE['logado'] == 179){
                echo "<br>MESES EV:" . $meses_evento;
            }
            
            $anoDtInicio = date("Y",strtotime($row_evento['data']));
            if($anoDtInicio < date('Y') && $row_evento['data_retorno'] == '0000-00-00'){
                $meses_evento = 12;
            }
            
	}
        
        if($meses_evento > 12){
            $meses_evento = 12;
        }
           
}


?>