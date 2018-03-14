<?php
include('include/restricoes.php');
include('../conn.php');
include('../funcoes.php');
include "../include/criptografia.php";
include("../classes_permissoes/regioes.class.php");
include("../wfunction.php");
header('Content-Type: text/html; charset=UTF-8');
$uf = "RJ";
$estaduais = file_get_contents("http://dadosbr.github.io/feriados/estaduais/{$uf}.json");
$nacionais = file_get_contents("http://dadosbr.github.io/feriados/nacionais.json");
$f = json_decode($nacionais);
$qr = mysql_query("select * from rhferiados");
$flag = 0;

foreach ($f as $chave => $feriado) {
    $dataJson = ($f[$chave]->date) ? $f[$chave]->date . "/2017" : "";

    if($flag == 0){
        while($row = mysql_fetch_assoc($qr)){
            $lista[] = $row['data'];
            $flag = 1;
        }
    }
    $data = implode('-', array_reverse(explode('/', $dataJson)));;

    if (!in_array($data, $lista)) {
        if (!empty($f[$chave]->date)) {
            echo $sql = "INSERT INTO rhferiados (tipo, nome, data, status) VALUES ('Nacional', '{$feriado->title}', '{$data}', '1');";
            echo "<br>";
            mysql_query($sql);
        }
        else {
            foreach ($feriado->variableDates as $key => $feriadoDataDiff) {
                $data = $feriadoDataDiff . "/" . $key;
                $data = implode('-', array_reverse(explode('/', $data)));
                if ($key >= 2017 and !in_array($data, $dataDIff)) {
                    echo $sql = "INSERT INTO rhferiados (tipo, nome, data, status) VALUES ('Nacional', '{$feriado->title}', '{$data}', '1');";
                    echo "<br>";
                    mysql_query($sql);
                }
            }
        }

    }
}

?>
