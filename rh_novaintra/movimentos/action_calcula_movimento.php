<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}
include "../../conn.php";
include "../../funcoes.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include '../../classes/CalculoFolhaClass.php';
include '../../classes/MovimentoClass.php';



$id_clt = $_POST['id_clt'];
$id_mov = $_POST['id_mov'];
$tipo_quantidade =$_POST['tipo_qnt'];
$quantidade = $_POST['qnt'];


$objCalcFolha = new Calculo_Folha();
$objCalcFolha->CarregaTabelas(date('Y'));

$configIdMovimento = array(232 => 'FALTAS',  236 => 'ATRASO', 293 => 'FALTA MÊS ANTERIOR');


$qr_clt = mysql_query("SELECT A.*,B.nome as funcao, B.id_curso, B.salario ,B.cbo_codigo, F.horas_mes, D.regiao as nome_regiao,E.nome as nome_projeto,A.id_projeto, B.tipo_insalubridade, B.qnt_salminimo_insalu,
                       B.periculosidade_30, F.adicional_noturno, F.horas_noturnas, F.nome as nome_horario, F.id_horario
                        FROM rh_clt as A 
                       LEFT JOIN curso as B ON A.id_curso = B.id_curso
                       LEFT JOIN rhstatus as C ON C.codigo = A.status
                       LEFT JOIN regioes as D ON D.id_regiao = A.id_regiao
                       LEFT JOIN projeto as E ON E.id_projeto = A.id_projeto
                       LEFT JOIN rh_horarios as F ON F.id_horario = A.rh_horario
                       WHERE A.id_clt = $id_clt");
$row_clt = mysql_fetch_assoc($qr_clt);



if($row_clt['periculosidade_30']){
    $periculosidade = $objCalcFolha->getPericulosidade($row_clt['salario']);
}
$insalubridade = $objCalcFolha->getInsalubridade(30, $row_clt['tipo_insalubridade'], $row_clt['qnt_salminimo_insalu'], date('Y'));



if($row_clt['adicional_noturno']){
 $baseCalcAdiconal = $row_clt['salario']+ $insalubridade['valor_integral'] + $periculosidade['valor_integral'];   
$adicional_noturno = $objCalcFolha->getAdicionalNoturno($baseCalcAdiconal, $row_clt['horas_mes'], $row_clt['horas_noturnas']);
$dsr = $objCalcFolha->getDsr($adicional_noturno['valor_integral']);
        
}


$baseCalc = $row_clt['salario']+ $insalubridade['valor_integral'] + $periculosidade['valor_integral'] + $adicional_noturno['valor_integral'] + $dsr['valor_integral'];
$valor_diario  = ($baseCalc) / 30;
$valor_hora    = ($baseCalc)/$row_clt['horas_mes'];


$baseCalcAtraso =  $row_clt['salario']+ $insalubridade['valor_integral'] + $periculosidade['valor_integral'] ;
$valor_diarioAtraso  = ($baseCalcAtraso) / 30;
$valor_horaAtraso    = ($baseCalcAtraso)/$row_clt['horas_mes'];


//Verificando o tipo de quantidade
if($tipo_quantidade == 1){
    
    list($qnt_hora ,$qnt_minuto) = explode(':', $quantidade);
    $totalQnt = $qnt_hora + ($qnt_minuto/60);
    $valorCalc = $valor_hora ;
    
    
}elseif($tipo_quantidade == 2){
    
    $valorCalc = $valor_diario;
    $totalQnt = $quantidade;
}


//verifica o movimento e faz os 
switch($configIdMovimento[$id_mov]){
    
    case 'FALTAS':  $valorMov = $valorCalc * $totalQnt;
        break;
    case 'ATRASO':  if($tipo_quantidade == 1){
                        $valorMov = $valor_horaAtraso * $totalQnt;
                    }else if($tipo_quantidade == 2){
                        $valorMov = $valor_diarioAtraso * $totalQnt;
                    }
        break;
    case 'FALTA MÊS ANTERIOR':  $valorMov = $valorCalc * $totalQnt;
    break;
    
    default : $valorMov = $valorCalc * $totalQnt;
}
echo number_format($valorMov,2,'.','');
?>