<?php
include "../conn.php";
include "../wfunction.php";
include '../classes/CalculoFolhaClass.php';

$objCalcFolha = new Calculo_Folha();
$objCalcFolha->CarregaTabelas(date('Y'));

if (isset($_REQUEST['method']) && !empty($_REQUEST['method'])) {
    if($_REQUEST['method'] == "consulta_clt"){
        $retorno = array("status" => "0");
        
        $id_clt = $_REQUEST['clt'];
        $faltas = $_REQUEST['faltas'];
        $atraso = $_REQUEST['atraso'];
        
        $qry = "SELECT A.*, B.salario AS sal_curso, B.tipo_insalubridade, B.qnt_salminimo_insalu, B.periculosidade_30, 
            C.adicional_noturno, C.horas_mes, C.horas_noturnas
            FROM rh_clt AS A
            LEFT JOIN curso AS B ON(A.id_curso = B.id_curso)
            LEFT JOIN rh_horarios AS C ON(A.rh_horario = C.id_horario)
            WHERE A.id_clt = {$id_clt}";
        $sql = mysql_query($qry) or die("ERRO method consulta_clt");
        $res = mysql_fetch_assoc($sql);
        $tot = mysql_num_rows($sql);
        
        $salario = $res['sal_curso'];
        
        //INSALUBRIDADE
        $insalubridade = $objCalcFolha->getInsalubridade(30, $res['tipo_insalubridade'], $res['qnt_salminimo_insalu'], date('Y'));
        
        //PERICULOSIDADE
        if($res['periculosidade_30']){
            $periculosidade = $objCalcFolha->getPericulosidade($salario);
        }
        
        //ADICIONAL NOTURNO
        if($res['adicional_noturno']){
            $baseCalcAdiconal = $salario + $insalubridade['valor_integral'] + $periculosidade['valor_integral'];
            $adicional_noturno = $objCalcFolha->getAdicionalNoturno($baseCalcAdiconal, $res['horas_mes'], $res['horas_noturnas']);
            $dsr = $objCalcFolha->getDsr($adicional_noturno['valor_integral']);
        }
        
        //FALTAS
        $baseCalc = $salario + $insalubridade['valor_integral'] + $periculosidade['valor_integral'] + $adicional_noturno['valor_integral'] + $dsr['valor_integral'];
        $valor_hora = ($baseCalc) / $res['horas_mes'];
        
        //ATRASO
        $baseCalcAtraso =  $salario + $insalubridade['valor_integral'] + $periculosidade['valor_integral'] ;
        $valor_diarioAtraso  = ($baseCalcAtraso) / 30;
        $valor_horaAtraso    = ($baseCalcAtraso) / $res['horas_mes'];
        
        //CALCULO DE VALOR DE FALTAS
        list($qnt_hora, $qnt_minuto) = explode(':', $faltas);   
        $totalQnt = $qnt_hora + ($qnt_minuto / 60);
        $valorMov = $valor_hora * $totalQnt;
        $valorMovF = formataMoeda($valorMov, 1);
        
        //CALCULO DE VALOR DE ATRASO
        $valorMovAtraso = $valor_horaAtraso * $atraso;
        $valorMovAtrasoF = formataMoeda($valorMovAtraso, 1);
        
        if($tot >= 1){
            $retorno = array(
                "status" => "1", 
                "id_clt" => $id_clt, 
                "nome" => $res['nome'], 
                "valor_mov" => round($valorMov,2), 
                "valor_movF" => $valorMovF, 
                "valor_mov_atraso" => round($valorMovAtraso,2), 
                "valor_mov_atrasoF" => $valorMovAtrasoF
            );
        }
        
        echo json_encode($retorno);
        exit;
    }
}
?>