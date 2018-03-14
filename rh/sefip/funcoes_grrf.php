<?php

function Data($data) {
    return implode('', array_reverse(explode('-', $data)));
}

function get_cod_data_aviso($row_rescisao) {
    return ($row_rescisao['aviso_codigo'] == 2) ? '00000000' : $row_rescisao['data_aviso'];
}

function get_cod_genero($row_clt) {
    return ($row_clt['sexo'] == 'M' or $row_clt['sexo'] == 'm') ? 1 : 2;
}

//$array_incidencia_fgts = array(
//    'adicional_insalubridade' => 'Adicional de Insalubridade',
//    'adicional_periculosidade' => 'Adicional de Periculosidade',
//    'adicional_transferencia' => 'Adicional de Transferência',
//    'adicional_noturno' => 'Adicional Noturno',
//    'alimentacao' => 'Alimentação',
//    'auxilio_enfermidade' => 'Auxilio Enfermidade',
//    'aviso_previo_indenizado' => 'Aviso Prévio Indenizado',
//    'aviso_previo_trabalhado' => 'Aviso Previo Trabalhado',
//    'comissoes' => 'Comissões',
//    'decimo_terceiro_1_parcela' => 'Décimo Terceiro Salário - 1 Parcela',
//    'decimo_terceiro_2_parcela' => 'Décimo Terceiro Salário - 2 Parcela',
//    'decimo_terceiro_salario_rescisao' => 'Décimo Terceiro Salário na Rescisão',
//    'decimo_terceiro_salario_parcela_aviso_previo' => 'Décimo Terceiro Salário - Parcela Referente ao Aviso-Prévio Indenizado',
//    'ferias_normais_com_mais_um_terco' => 'Férias Normais com mais 1/3',
//    'gorgetas' => 'Gorjetas',
//    'gratificações' => 'Gratificações',
//    'habilitacao' => 'Habilitação',
//    'horas_extras' => 'Horas Extras ou Extraordinárias',
//    'premios' => 'Prêmios',
//    'quebra_de_caixa' => 'Quebra de Caixa',
//    'reembolso_quilometragem' => 'Reembolso de Quilometragem',
//    'salarios' => 'Salários',
//    'salario_maternidade' => 'Salario Maternidade'
//);
/** * Tudo na rescisão que tenha incidencia no fgts */
function get_remuneracao_mes($row_rescisao) {
    
    $array_incidencias = array('saldo_salario' => 'Saldo de Salario',
        'dt_salario' => '13° Salario',
//        'terceiro_ss' => '13 indenizado', //não entra! 28/11/2016 -> italo falou que entra - 09/01/2017 -> comentado para atender o italo
        'insalubridade' => 'Insalubridade',
        'adicional_noturno' => 'Adicional Noturno',
        'dsr'=>'Descanso Semanal Remunerado',
//        'gratificacao'=>'Gratificacao', NAO SE PEGA NA rh_recisao E SIM NA rh_movimentos
        'valor_lei_12_506'=>'Lei 12.506'
        
        );
    $array_incidencias_debito = array('adiantamento_13' => 'Adiantamento de 13');
    $base_fgts = 0;
    $calculo = '';
    $incidencias = array();
    foreach ($array_incidencias as $incide => $descricao) {
        $valor_incidencia = $row_rescisao[$incide];
        if ($valor_incidencia > 0) {
            $base_fgts = $base_fgts + $valor_incidencia;

            $incidencias[] = array('tipo' => 'CREDITO', 'descricao' => $descricao, 'tabela' => 'rh_recisao', 'campo' => $incide, 'id' => $row_rescisao['id_recisao'], 'valor' => $row_rescisao[$incide]);
        }
    }
    foreach ($array_incidencias_debito as $incide => $descricao) {
        $valor_incidencia = $row_rescisao[$incide];
        if ($valor_incidencia > 0) {
            $base_fgts = $base_fgts - $valor_incidencia;

            $incidencias[] = array('tipo' => 'DEBITO', 'descricao' => $descricao, 'tabela' => 'rh_recisao', 'campo' => $incide, 'id' => $row_rescisao['id_recisao'], 'valor' => $row_rescisao[$incide]);
        }
    }
   
    if($_COOKIE['debug'] == 666){
        print_r('teste');
        var_dump($row_rescisao['rescisao_complementar']);
        var_dump($row_rescisao['plantonista']);
    }
    

    //PEGANDO OS MOVIMENTOS QUE INCIDEM NA RESCISÃO
    if($row_rescisao['rescisao_complementar'] == 0 && $row_rescisao['plantonista'] == 0){
        
        $sql_movi = "SELECT * FROM rh_movimentos_clt 
                                WHERE id_clt = '{$row_rescisao['id_clt']}'
                                AND mes_mov = 16 AND ano_mov = '{$row_rescisao['ano_rescisao']}' /*AND status = 1*/ AND ((incidencia LIKE '%5023%')/* OR (id_mov = 62) OR (id_mov = 424)*/) /*AND id_mov not in(410,56) */";
        //$sql_movi = "SELECT A.nome_movimento, A.valor AS valor_movimento, A.incidencia, B.categoria AS tipo_movimento FROM rh_movimentos_rescisao A LEFT JOIN rh_movimentos B ON (A.id_mov = B.id_mov) WHERE A.id_rescisao = '{$row_rescisao['id_recisao']}' AND A.status = 1 $aux";
    } else if($row_rescisao['rescisao_complementar'] == 0 && $row_rescisao['plantonista'] == 1) {
        $sql_movi = "SELECT * FROM rh_movimentos_clt 
                                WHERE id_clt = '{$row_rescisao['id_clt']}'
                                AND mes_mov = 16 AND ano_mov = '{$row_rescisao['ano_rescisao']}' AND status = 1 AND ((incidencia LIKE '%5023%')/* OR (id_mov = 62) OR (id_mov = 424)*/) /*AND id_mov not in(410,56,466) */";
    } else {
        if ($row_rescisao['id_recisao'] == 435) { 
            $aux = "AND A.id_mov_rescisao = 1976";
        }
        $sql_movi = "SELECT A.nome_movimento, A.valor AS valor_movimento, A.incidencia, B.categoria AS tipo_movimento FROM rh_movimentos_rescisao A LEFT JOIN rh_movimentos B ON (A.id_mov = B.id_mov) WHERE A.id_rescisao = '{$row_rescisao['id_recisao']}' AND A.id_mov NOT IN (410, 383) AND A.status = 1 $aux";
    }

    if($_COOKIE['debug'] == 666){
        echo "<pre>";
            print_r($sql_movi);
        echo "</pre>";
    }
    $qr_movi = mysql_query($sql_movi) or die(mysql_error());

    while ($row_mov = mysql_fetch_assoc($qr_movi)) {

        $incidencia = explode(',', $row_mov['incidencia']);
        // FGTS
        if (in_array(5023, $incidencia)) {
            
//            var_dump($row_mov);
            
            if ($row_mov['tipo_movimento'] == 'CREDITO') {
                
//                $calculo .= $base_fgts.' + '.str_replace('.', '', $row_mov['valor_movimento']).' ('.$row_mov['nome_movimento'].')';
                
//                $base_fgts += str_replace('.', '', $row_mov['valor_movimento']);
                $base_fgts += $row_mov['valor_movimento'];
                
//                echo '<pre>';
//                var_dump($row_mov);
//                echo '</pre>';
//                
//                exit();
                
//                $calculo .=  '='.$base_fgts.' <br>';
                
                $incidencias[] = array('tipo' => $row_mov['tipo_movimento'], 'descricao' => $row_mov['nome_movimento'], 'tabela' => 'rh_movimentos_clt', 'campo' => 'valor_movimento', 'id' => $row_mov['id_movimento'], 'valor' => $row_mov['valor_movimento']);
            } elseif ($row_mov['tipo_movimento'] == 'DEBITO') {
                
//                $calculo .= $base_fgts.' - '.str_replace('.', '', $row_mov['valor_movimento']).' ('.$row_mov['nome_movimento'].')';
                
//                $base_fgts -= str_replace('.', '', $row_mov['valor_movimento']);
                $base_fgts -= $row_mov['valor_movimento'];
                
//                 $calculo .= '='.$base_fgts.' <br>';
                
                $incidencias[] = array('tipo' => $row_mov['tipo_movimento'], 'descricao' => $row_mov['nome_movimento'], 'tabela' => 'rh_movimentos_clt', 'campo' => 'valor_movimento', 'id' => $row_mov['id_movimento'], 'valor' => $row_mov['valor_movimento']);
            }
        }
    }
    
if($_COOKIE['debug'] == 666){
        print_r('get_remuneracao_mes');
        echo "<pre>";
            print_r($incidencias);
        echo "</pre>";
}
    
    if($base_fgts < 0){$base_fgts = 0;}
    
    return array('base_fgts' => number_format($base_fgts,2, '.',''), 'calculo' => $calculo, 'incidencias' => $incidencias);
}

/** * Não deve ser preenchido se campo Data da Homologação do Dissídio estiver preenchido. 
  · Não deve ser preenchido se a Data de Admissão e a Data de Movimentação estiverem no
  mesmo mês. */
function get_remuneracao_mes_anterior($row_rescisao) {
    return $row_rescisao['sal_base'];
//    return (str_replace('.', '', $row_rescisao['sal_base']));
}

/** * aviso prévio indenizado = aviso indenizado + 13° indenizado */
function get_aviso_previo_indenizado($row_rescisao) {
    $aviso_previo_indenizado = 0;

    if ($row_rescisao['aviso_codigo'] == 2) {

        //+ number_format($row_rescisao['ferias_aviso_indenizado'], 2, '', '')
        if($row_rescisao['plantonista'] == 0){
            $aviso_previo_indenizado = (
                    number_format($row_rescisao['terceiro_ss'], 2, '', '') + 
                    number_format($row_rescisao['aviso_valor'], 2, '', '') + 
                    number_format($row_rescisao['lei_12_506'], 2, '', '') 
            );
        }else if($row_rescisao['plantonista'] == 1){
                $aviso_previo_indenizado = (
                    number_format($row_rescisao['aviso_valor'], 2, '', '') + 
                    number_format($row_rescisao['lei_12_506'], 2, '', '') 
            );
        }
//        $aviso_previo_indenizado = $valor_movimento + $aviso_previo_indenizado;
//        var_dump($aviso_previo_indenizado);
//        exit('');
        
        $incidencias['terceiro_ss'] = $row_rescisao['terceiro_ss']; //  
        $incidencias['terceiro_ss'] = $row_rescisao['terceiro_ss']; //  
        $incidencias['aviso_valor'] = $row_rescisao['aviso_valor'];
        $incidencias['lei_12_506'] = $row_rescisao['lei_12_506'];
        //PEGANDO OS MOVIMENTOS QUE INCIDEM NA RESCISÃO
//        $sql_movi = "SELECT * FROM rh_movimentos_clt 
//        WHERE id_clt = '{$row_rescisao['id_clt']}'
//        AND mes_mov = 16 AND ano_mov = '{$row_rescisao['ano_rescisao']}' AND status = 1 AND id_mov IN (410, 383)";
        if($row_rescisao['plantonista'] == 0){
            $sql_movi = "SELECT * FROM rh_movimentos_rescisao 
            WHERE id_rescisao = {$row_rescisao['id_recisao']} AND status = 1 AND id_mov IN (410, 383)";
        }else if($row_rescisao['plantonista'] == 1){
            $sql_movi = "SELECT * FROM rh_movimentos_rescisao 
            WHERE id_rescisao = {$row_rescisao['id_recisao']} AND status = 1 AND id_mov IN (410, 383, 466)";
        }
        $qr_movi = mysql_query($sql_movi) or die(mysql_error());
        while ($row_mov = mysql_fetch_assoc($qr_movi)) {
            
//            if ($row_mov['tipo_movimento'] == 'CREDITO') {
                $incidencias[$row_mov['nome_movimento']] = $row_mov['valor'];
                $aviso_previo_indenizado += number_format($row_mov['valor'], 2, '', '');
//            } 
//            elseif ($row_mov['tipo_movimento'] == 'DEBITO') {
//                $incidencias[$row_mov['nome_movimento']] = ($row_mov['valor_movimento'] * -1);
//                $aviso_previo_indenizado -= number_format($row_mov['valor_movimento'], 2, '', '');
//            }
//            if($_COOKIE['debug'] == 666){
//                echo "<pre>";
//                echo number_format($row_mov['valor_movimento'], 2, '', '');
//                echo "</pre>";
//            }
        }
        
    }
    if($_COOKIE['debug'] == 666){
        echo "<pre>";
            print_r('get_aviso_previo_indenizado');
            print_r($incidencias);
            print_r(array_sum($incidencias));
        echo "</pre>";
    }
    return $aviso_previo_indenizado;
}

function get_pensao_alimenticia($row_clt, $row_rescisao) {
    if ($row_clt['pensao_alimenticia'] == 1) {
        $arr['percentual'] = $row_clt['pensao_percentual'];
        $arr['valor'] = $row_rescisao['valor_pensao'];
        $arr['flag'] = 'S';
    } else {
        $arr['percentual'] = '';
        $arr['valor'] = '';
        $arr['flag'] = 'N';
    }
    return $arr;
}

function print_helper($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}
