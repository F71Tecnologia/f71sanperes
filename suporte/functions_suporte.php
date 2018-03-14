<?php

include_once '../conn.php';


function print_helper($var){
    echo '<pre>'; print_r($var); echo '</pre>';
}
function get_funcionario($id_user=NULL){
    $id_user = is_null($id_user) ? ($_COOKIE['logado']) : $id_user;
    $sql = "SELECT * FROM funcionario A
            LEFT JOIN grupo B ON A.grupo_usuario = B.id_grupo
            LEFT JOIN funcionario_tipo C ON A.tipo_usuario = C.id_funcionario_tipo
            WHERE id_funcionario = '$id_user'  LIMIT 1";
    $row_funcionario = mysql_query($sql);
    return mysql_fetch_assoc($row_funcionario);
}
function get_chamados($status){
    $sql = '';
    $array_permissao = get_funcionario();
    $tipo_usuario = isset($array_permissao['tipo_usuario']) ? $array_permissao['tipo_usuario'] : NULL;
    $grupo_usuario = isset($array_permissao['grupo_usuario']) ? $array_permissao['grupo_usuario'] : 1;
    switch ($tipo_usuario){
        case 5: //adm
        $sql = "SELECT *, date_format(suporte.data_cad, '%d/%m/%Y') AS data_cad, date_format(suporte.ultima_alteracao, '%d/%m/%Y') AS ultima_alteracao FROM suporte, funcionario, suporte_status WHERE status != '$status' AND suporte.user_cad = funcionario.id_funcionario AND suporte.`status` = suporte_status.id_suporte_status ORDER BY id_suporte, id_suporte_status DESC";            
//        $sql = "SELECT *, date_format(data_cad, '%d/%m/%Y') AS data_cad, date_format(ultima_alteracao, '%d/%m/%Y') AS ultima_alteracao FROM suporte WHERE status != '4' ORDER BY id_suporte DESC";            
        break;
        case 4: //diretor
        $sql = "SELECT *, date_format(suporte.data_cad, '%d/%m/%Y') AS data_cad, date_format(suporte.ultima_alteracao, '%d/%m/%Y') AS ultima_alteracao FROM suporte, funcionario, suporte_status WHERE status != '$status' AND suporte.user_cad = funcionario.id_funcionario AND suporte.`status` = suporte_status.id_suporte_status ORDER BY id_suporte DESC";            
        break;
        case 2: //financeiro
        $sql = "SELECT *, date_format(data_cad, '%d/%m/%Y') AS data_cad, date_format(ultima_alteracao, '%d/%m/%Y') AS ultima_alteracao FROM suporte WHERE status != '$status' ORDER BY id_suporte DESC";            
        break;
        case 1: //financeiro
        $sql = "SELECT *, date_format(suporte.data_cad, '%d/%m/%Y') AS data_cad, date_format(suporte.ultima_alteracao, '%d/%m/%Y') AS ultima_alteracao FROM suporte, funcionario, suporte_status WHERE status != '$status' AND suporte.user_cad = funcionario.id_funcionario AND suporte.`status` = suporte_status.id_suporte_status AND grupo_usuario='$grupo_usuario'  ORDER BY id_suporte, id_suporte_status DESC";            
        break;
    }
    return mysql_query($sql);
//    return mysql_fetch_assoc($query);
}
function get_chamados_status(){
    $sql = "SELECT * FROM suporte_status;";
    return mysql_query($sql);
}
