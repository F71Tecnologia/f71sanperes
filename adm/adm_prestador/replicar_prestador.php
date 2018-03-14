<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}
error_reporting(E_ALL);
include "../../conn.php";
include "../../funcoes.php";
include "../../wfunction.php";

$usuario = carregaUsuario();
$master = $usuario['id_master'];

$id_prestador = 31999;

$sql = "SELECT * FROM prestadorservico WHERE id_prestador = {$id_prestador}";
$qry = mysql_query($sql);
$row = mysql_fetch_assoc($qry);


$sqlP = "SELECT * FROM prestadorservico WHERE c_cnpj = '{$row['c_cnpj']}'";
$qryP = mysql_query($sqlP);
while($rowP = mysql_fetch_assoc($qryP)){
    $arrayProjeto[$rowP['id_projeto']] = $rowP['id_projeto'];
}
$array = $row;
echo $sqlProjeto = "SELECT * FROM projeto WHERE id_projeto NOT IN (" . implode(', ', $arrayProjeto) . ")";
$qryProjeto = mysql_query($sqlProjeto);
while($rowProjeto = mysql_fetch_assoc($qryProjeto)){
    unset($array['id_prestador']);
    
    $array['id_projeto'] = $rowProjeto['id_projeto'];
    $array['id_regiao'] = $rowProjeto['id_regiao'];
    
    $keys = implode(',', array_keys($array));
    $values = implode("' , '", $array);

    print_array("INSERT INTO prestadorservico ($keys) VALUES ('$values');");
    
}



