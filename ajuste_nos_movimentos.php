<?php

//
//include_once $_SERVER['DOCUMENT_ROOT']."/intranet/conf.php";
//
//$servidor   = 'f71lagos.com';
//$usuario    = 'ispv_netsorr';
//$senha      = 'L4G0S#321h1_msql';
//$banco      = 'ispv_netsorrindo';
////$banco = 'f71itgm';
//
////date_default_timezone_set ("Etc/GMT+2");
//
//error_reporting(E_ERROR);
//
//// Criando ConexÃ£o
//$conn = mysql_connect($servidor, $usuario, $senha) or die('Não pude conectar ao banco de dados');
//
//// Selecionando o Banco de Dados
//mysql_select_db("$banco") or die('Não pude selecionar o banco de dados'); 
//$domainName = 'http://'.$_SERVER['HTTP_HOST'].'/intranet/';
//
//define('_SITENAME', ':: Intranet ::');
//define('_URL', $domainName);
//
//$meta_title = sprintf($lang['homepage_title'], _SITENAME);
//
//// Bloqueio AdministraÃ§Ã£o
//function bloqueio_administracao($id_regiao_bloqueio) {
//    $ids_usuarios_liberados = array('5', '9', '77', '68', '82', '87', '89', '71', '22', '80','40');
//    if (!in_array($_COOKIE['logado'], $ids_usuarios_liberados) and $id_regiao_bloqueio == 15) {
//        echo '<p>&nbsp;</p>Acesso somente para pessoas autorizadas.';
//        exit();
//    }
//}
//
//session_cache_expire(1440); //24 horas
//session_start();
//
//if (isset($_SESSION['ultima_acao'])) {
//    $_SESSION['ultima_acao'] = date("Y-m-d H:i:s");
//}

include ("conn.php");
include ("wfunction.php");

$sql = "SELECT * FROM rh_movimentos A WHERE campo_rescisao = 0 ORDER BY cod, id_mov";
$query = mysql_query($sql);

while ($row = mysql_fetch_assoc($query)) {

    $arr[$row['cod']][] = $row;
}

//print_array($arr);

$_qry_max = mysql_query("SELECT MAX(A.campo_rescisao) AS max_campo_resc FROM rh_movimentos AS A") or die(mysql_error());
$_res_max = mysql_fetch_assoc($_qry_max);
$i = $_res_max['max_campo_resc'] + 1;

$count = 0;

foreach ($arr as $cod => $mov) {
    foreach ($mov as $value) {
        $sql_update = "UPDATE rh_movimentos SET campo_rescisao = '$i' WHERE id_mov = {$value['id_mov']};<br>";
//        $update = mysql_query($sql_update);
        
        echo $sql_update;
    }
    
    $i++;
    $count++;
}

echo "<br>TOTAL: {$count}";