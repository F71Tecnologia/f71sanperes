<?php

//$qrEvento = " SELECT *, MONTH(data_retorno) as mes_retorno, MONTH(data_retorno) as mes_inicio,
//                                 DATE_ADD(data, INTERVAL 15 DAY) AS 15_dias,
//                                 MONTH(data) as mes_inicio_evento,
//                                 MONTH(DATE_ADD(data, INTERVAL 15 DAY)) as mes_15_dias,
//                                 DATE_FORMAT(data,'%d/%m/%Y') as data_inicioBR,
//                                 DATE_FORMAT(data_retorno,'%d/%m/%Y') as data_retornoBR,
//                                 DATE_FORMAT(DATE_ADD(data, INTERVAL 15 DAY),'%d/%m/%Y') as data_15_diasBR                                 
//                            FROM rh_eventos 
//                            WHERE id_clt = '$clt' 
//                            AND status = '1'                           
//                            ORDER BY id_evento DESC";
//


$mes_referente = $ano . "-" . $mes;

$verifica_status = mysql_query("SELECT * FROM rh_eventos AS A
    WHERE A.status = 1 AND A.id_clt = '{$clt}'
    ORDER BY data DESC LIMIT 1");
$dados_evento = mysql_fetch_assoc($verifica_status);


        

if (mysql_num_rows($verifica_status) != 0) {

    
    
    if ($dados_evento['cod_status'] != "10") {
    
        $qr_eventos = mysql_query("SELECT *,MONTH(data_retorno) as mes_retorno, MONTH(data_retorno) as mes_inicio,
            DATE_ADD(data, INTERVAL 15 DAY) AS 15_dias,
            MONTH(data) as mes_inicio_evento,
            MONTH(DATE_ADD(data, INTERVAL 15 DAY)) as mes_15_dias,
            DATE_FORMAT(data,'%d/%m/%Y') as data_inicioBR,
            DATE_FORMAT(data_retorno,'%d/%m/%Y') as data_retornoBR,
            DATE_FORMAT(DATE_ADD(data, INTERVAL 15 DAY),'%d/%m/%Y') as data_15_diasBR 
            FROM rh_eventos AS A WHERE A.id_clt = '{$clt}' AND '{$mes_referente}' 
            BETWEEN DATE_FORMAT(A.data, '%Y-%m') AND DATE_FORMAT(A.data_retorno, '%Y-%m') AND A.status = '1'");

        $row_evento = mysql_fetch_array($qr_eventos);
        $num_eventos = mysql_num_rows($qr_eventos);

    

// Sinalizando Evento

        /*
          if(!empty($num_eventos)) {
          $sinaliza_evento = true;
          } */




// Eventos que Desconta
        $codigos_licenca = array(20, 30, '80', '90', '100');
        $codigos_eventos = array(20, 30, '50', '80', '90', '100');
        $codigos_15_dias = array(20, 90);
        
        $verifica_desconto_licenca = (in_array($row_evento['cod_status'], $codigos_licenca))? 1: 0;
        
        /*
          if(in_array($row_evento['cod_status'], $cod_licenca)){
          $sem_mov_sempre = true;

          echo $sem_mov_sempre.' - '.$clt.'<br>';
          }
         */


        if (!empty($num_eventos) and in_array($row_evento['cod_status'], $codigos_eventos)) {



// InÃ­cio do Evento entre o InÃ­cio e Fim da Folha
            if ($row_evento['data'] >= $row_folha['data_inicio'] and $row_evento['data'] <= $row_folha['data_fim']) {

                $inicio = $row_evento['data'];

                // Se o Fim do Evento for antes do Fim da Folha	
                $fim = ($row_evento['data_retorno'] < $row_folha['data_fim']) ? $row_evento['data_retorno'] : $row_folha['data_fim'];

                //Condição para os 15 primeiros dias de licença médica
                if (in_array($row_evento['cod_status'], $codigos_15_dias)) {
                    //Se os primeiros 15 dias não ultrapassarem a data de término da folha
                    if ($row_evento['15_dias'] <= $row_folha['data_fim']) {
                        $inicio = $row_evento['15_dias'];
                    } else {
                        $dias_evento = 0;
                    }
                    $msg_15_dias = ' <BR>PAGANDO OS PRIMEIROS 15 DIAS DE LICENÇA  ATÉ ' . $row_evento['data_15_diasBR'];
                }

                $msg_evento = $row_evento['data_inicioBR'] . ' a ' . $row_evento['data_retornoBR'] . $msg_15_dias;
                $evento = true;
                $sinaliza_evento = true;
// Fim do Evento entre o InÃ­cio e Fim da Folha
            } elseif ($row_evento['data_retorno'] >= $row_folha['data_inicio'] and $row_evento['data_retorno'] <= $row_folha['data_fim']) {



                ////Condição para os 15 primeiros dias de licença médica
                if (in_array($row_evento['cod_status'], $codigos_15_dias)) {

                      if ($row_evento['15_dias'] > $row_folha['data_inicio']) {
                        $inicio = $row_evento['15_dias'];
                        $msg_15_dias = ' <BR>PAGANDO OS PRIMEIROS 15 DIAS DE LICENÇA  ATÉ ' . $row_evento['data_15_diasBR'];
                    } else {
                        $inicio = $row_folha['data_inicio'];
                    }                    
                } else {

                    // Se o InÃ­cio do Evento for depois do InÃ­cio da Folha     
                    $inicio = ($row_evento['data'] > $row_folha['data_inicio']) ? $row_evento['data'] : $row_folha['data_inicio'];
                }




                $fim = $row_evento['data_retorno'];
                $msg_evento = $row_evento['data_inicioBR'] . ' a ' . $row_evento['data_retornoBR'] . $msg_15_dias;
                $evento = true;
                $sinaliza_evento = true;
            } elseif ($row_evento['data'] <= $row_folha['data_inicio'] and $row_evento['data_retorno'] >= $row_folha['data_fim']) {

                ////Condição para os 15 primeiros dias de licença médica
                if (in_array($row_evento['cod_status'], $codigos_15_dias)) {

                    if ($row_evento['15_dias'] > $row_folha['data_inicio']) {
                        $inicio = $row_evento['15_dias'];
                        $msg_15_dias = ' <BR>PAGANDO OS PRIMEIROS 15 DIAS DE LICENÇA  ATÉ ' . $row_evento['data_15_diasBR'];
                    } else {
                        $dias_evento = 30;
                    }


                    $fim = $row_folha['data_fim'];
                } else {

                    $dias_evento = 30;
                }


                $msg_evento = $row_evento['data_inicioBR'] . ' a ' . $row_evento['data_retornoBR'] . $msg_15_dias;
                $evento = true;
                $sinaliza_evento = true;
            }



// Calculando Dias do Evento
            if (isset($evento)) {
                if (!isset($dias_evento))
                    $dias_evento = abs((int) floor((strtotime($inicio) - strtotime($fim)) / 86400));
                $dias_evento = ($dias_evento > 30) ? 30 : $dias_evento;
                unset($evento);
            }
        } else
        /// verificando se a pessoa foi colocada para Atividade Normal no mês da folha
        if (!empty($num_eventos) and $row_evento['cod_status'] == 10) {



            if ($row_evento['data'] > $row_folha['data_inicio'] and $row_evento['data'] < $row_folha['data_fim']) {

                $inicio = $row_folha['data_inicio'];
                $fim = $row_evento['data'];

                $dias_evento = abs((int) floor((strtotime($inicio) - strtotime($fim)) / 86400));
                if ($dias_evento != 0) {
                    $sinaliza_evento = true;
                }

                $msg_evento = 'RETORNOU DIA ' . $row_evento['data_inicioBR'];
            }
        }
    }
}
?>