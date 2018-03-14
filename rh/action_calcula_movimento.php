<?php

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}
include "../conn.php";
include "../wfunction.php";
include "../funcoes.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include '../classes/CalculoFolhaClass.php';
include '../classes/MovimentoClass.php';
include '../classes/calculos.php';

$id_clt = $_POST['id_clt'];
$id_mov = $_POST['id_mov'];
$tipo_quantidade = $_POST['tipo_qnt'];
$quantidade = $_POST['qnt'];
$just_info = $_REQUEST['just_info'];

$objCalcFolha = new Calculo_Folha();
$objCalc = new calculos();
$objCalcFolha->CarregaTabelas(date('Y'));

//$configIdMovimento = array(232 => 'FALTAS', 236 => 'ATRASO', 293 => 'FALTA MÊS ANTERIOR', 286 => "HORA EXTRA 50%", 287 => "HORA EXTRA 100%", 368 => "HORA EXTRA 75%");
//$configIdMovimento = array(630 => 'FALTAS OU ATRASOS', 232 => 'FALTAS', 236 => 'ATRASO', 293 => 'FALTA MÊS ANTERIOR');

$movHoraExtra           = array(287, 401, 402, 400, 368, 286, 461, 462, 463, 464, 465, 639, 644, 649, 655, 502, 614, 503);
$movAdicionalNoturno    = array(406, 403, 404, 405, 454, 493, 494, 495, 496, 497);
$movHoraExtraAdNoturno  = array(407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 418, 419, 420, 421, 422, 423, 424, 425, 426, 455, 456, 457, 458, 459, 460, 466, 467, 468, 469, 470, 471, 472, 473, 474, 475, 476, 477, 478, 479, 480, 481, 482, 483, 484, 485, 486, 487, 488, 489, 490, 491, 640, 641, 642, 643, 645, 646, 647, 648, 650, 651, 652, 653, 654, 656, 657, 658, 659, 660);
$movAdicionalProntidao  = array(623);
$movSobreaviso          = array(624);
$movFaltasAtrasos       = array(232,236,293,321,573,630);

//$qr_clt = mysql_query("SELECT A.*,B.nome as funcao, B.id_curso, B.salario ,B.cbo_codigo, F.horas_mes, D.regiao as nome_regiao,E.nome as nome_projeto,A.id_projeto, B.tipo_insalubridade, B.qnt_salminimo_insalu,
//                       B.periculosidade_30, F.adicional_noturno, F.horas_noturnas, F.nome as nome_horario, F.id_horario
//                        FROM rh_clt as A 
//                       LEFT JOIN curso as B ON A.id_curso = B.id_curso
//                       LEFT JOIN rhstatus as C ON C.codigo = A.status
//                       LEFT JOIN regioes as D ON D.id_regiao = A.id_regiao
//                       LEFT JOIN projeto as E ON E.id_projeto = A.id_projeto
//                       LEFT JOIN rh_horarios as F ON F.id_horario = A.rh_horario
//                       WHERE A.id_clt = $id_clt");
//$row_clt = mysql_fetch_assoc($qr_clt);
//
//
//if ($row_clt['periculosidade_30']) {
//    $periculosidade = $objCalcFolha->getPericulosidade($row_clt['salario'], 30, 12);
//}
//$insalubridade = $objCalcFolha->getInsalubridade(30, $row_clt['tipo_insalubridade'], $row_clt['qnt_salminimo_insalu'], date('Y'));
//
//if ($row_clt['adicional_noturno']) {
//    $baseCalcAdiconal = $row_clt['salario'] + $insalubridade['valor_integral'] + $periculosidade['valor_integral'];
//    $adicional_noturno = $objCalcFolha->getAdicionalNoturno($baseCalcAdiconal, $row_clt['horas_mes'], $row_clt['horas_noturnas'], 30, $row_clt['id_curso']);
//    $dsr = $objCalcFolha->getDsr($adicional_noturno['valor_integral']);
//}
//
//$baseCalc = $row_clt['salario'] + $insalubridade['valor_integral'] + $periculosidade['valor_integral'] + $adicional_noturno['valor_integral'] + $dsr['valor_integral'];
//$valor_diario = ($baseCalc) / 30;
//$valor_hora = ($baseCalc) / $row_clt['horas_mes'];
//
//$baseCalcAtraso = $row_clt['salario'] + $insalubridade['valor_integral'] + $periculosidade['valor_integral'];
//$valor_diarioAtraso = ($baseCalcAtraso) / 30;
//$valor_horaAtraso = ($baseCalcAtraso) / $row_clt['horas_mes'];

//Verificando o tipo de quantidade

if ($tipo_quantidade == 1) {

    list($qnt_hora, $qnt_minuto) = explode(':', $quantidade);
    $totalQnt = $qnt_hora + ($qnt_minuto / 60);
    $valorCalc = $valor_hora;

} elseif ($tipo_quantidade == 2) {

    $valorCalc = $valor_diario;
    $totalQnt = $quantidade;
}

if (in_array($id_mov, $movHoraExtraAdNoturno)) {

    $valorMov = $objCalcFolha->getMovHoraExtraAdNoturno($id_clt, $id_mov, $totalQnt, $tipo_quantidade, $just_info);

}

if (in_array($id_mov, $movFaltasAtrasos)) {

    $valorMov = $objCalcFolha->getFaltasAtrasos($id_clt, $totalQnt, $tipo_quantidade, $just_info);

}

if (in_array($id_mov, $movAdicionalNoturno)) {

    $valorMov = $objCalcFolha->getMovAdNoturno($id_clt, $id_mov, $totalQnt, $tipo_quantidade, $just_info);

}

if (in_array($id_mov, $movHoraExtra)) {
    $valorMov = $objCalcFolha->getHoraExtra($id_clt, $id_mov, $totalQnt, $tipo_quantidade, $just_info);
}

if (in_array($id_mov, $movAdicionalProntidao)) {
    $valorMov = $objCalcFolha->getMovAdProntidao($id_clt, $totalQnt, $tipo_quantidade, $just_info);
}

if (in_array($id_mov, $movSobreaviso)) {
    $valorMov = $objCalcFolha->getSobreAvisoEmHoras($id_clt, $totalQnt, $tipo_quantidade, $just_info);
}

if ($_POST['method'] == 'calculaDSR') {

    $valorMov = $objCalc->getDsr($_POST['mov_valor'], 30, $_POST['mes'], $_POST['ano']);
    $valorMov = $valorMov['valor_integral'];
}

//verifica o movimento e faz os 
//switch ($configIdMovimento[$id_mov]) {
//
//    case 'FALTAS':
//        $valorMov = $valorCalc * $totalQnt;
//        break;
//    case 'FALTAS OU ATRASOS':
//        if ($tipo_quantidade == 1) {
//            $valorMov = $valor_horaAtraso * $totalQnt;
//        } else if ($tipo_quantidade == 2) {
//            $valorMov = $valor_diarioAtraso * $totalQnt;
//        }
//        break;
//    case 'ATRASO':
//        if ($tipo_quantidade == 1) {
//            $valorMov = $valor_horaAtraso * $totalQnt;
//        } else if ($tipo_quantidade == 2) {
//            $valorMov = $valor_diarioAtraso * $totalQnt;
//        }
//        break;
//    case 'FALTA MÊS ANTERIOR':
//        $valorMov = $valorCalc * $totalQnt;
//}

if (isset($_REQUEST['just_info']) && $_REQUEST['just_info'] == TRUE) {
    echo json_encode($valorMov);
} else {
    echo number_format($valorMov, 2, '.', '');
}
?>