<?php
include_once('../../conn.php');
include_once('../../wfunction.php');

$id_clt = $_REQUEST['id_clt'];
$data_ocorrencia = $_REQUEST['data_ocorrencia'];
$data_ocorrencia = str_replace('/', '-', $data_ocorrencia);
$data_ocorrencia = explode('-', $data_ocorrencia);
$data_ocorrencia = $data_ocorrencia[2].'-'.$data_ocorrencia[1].'-'.$data_ocorrencia[0];

$data_ocorrencia = implode('-', array_reverse(explode('/', $_REQUEST['data_ocorrencia'])));

if($a){
    $b = 'A';
} else {
    $b = 'B';
}

$b = ($a) ? 'A' : 'B';

//echo $id_clt;
//echo $data_ocorrencia;
//echo '<br>';
//print_array($data_ocorrencia);

$sql ="SELECT * FROM rh_ferias_programadas WHERE id_clt  = {$id_clt} AND '{$data_ocorrencia}' BETWEEN inicio AND fim AND status = 1 AND 1=2;";
$result = mysql_query($sql);
//echo $sql;

//while ($row = mysql_fetch_assoc($result)) {
//    print_array($row);
//}

echo mysql_num_rows($result);





?>
