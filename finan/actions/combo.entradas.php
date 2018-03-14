<?php
include "../../conn.php";

function get_anexo ($id_nota){
    return 'http://'.$_SERVER['HTTP_HOST'].'/intranet/finan/ver_notas.php';
}

$array_response = array();

if(isset($_REQUEST['notas'])){
    
    $parceiro = $_REQUEST['id_parceiro'];
    $id_nota_edit = $_REQUEST['id_notas'];
    $id_entrada = $_REQUEST['id_entrada'];
    $array_response['nao_associada'] = array();
    $array_response['associada'] = array();
    
    // CONSULTANDO NOTAS NAO ASSOCIADAS
    $qr_notas = mysql_query("SELECT notas.id_notas
                    FROM notas
                    INNER JOIN notas_assoc ON notas_assoc.id_notas = notas.id_notas");    
    
    while($row_notas = mysql_fetch_assoc($qr_notas)){
        $intercessao[] = $row_notas['id_notas'];
    }
    
    unset($row_notas);
    
    if(!empty($intercessao)){
        $not_in = "id_notas NOT IN (".implode(',',$intercessao).") AND ";
    }
    
    $qr_notas = mysql_query("SELECT * FROM notas WHERE $not_in id_parceiro = '$parceiro' AND STATUS = '1'");
    $num_notas = mysql_num_rows($qr_notas);
    
    // CONSULTANDO NOTAS ASSOCIADAS
    $qr_notas_associadas = mysql_query("SELECT *
                                FROM notas
                                INNER JOIN notas_assoc ON notas_assoc.id_notas = notas.id_notas
                                WHERE id_parceiro = '$parceiro' AND notas.status = '1'
                                GROUP BY (notas.id_notas)");
    $num_notas_associadas = mysql_num_rows($qr_notas_associadas);        
    
    if(empty($num_notas) and (empty($num_notas_associadas))) $array_response['erro'] = '1';
    
    // LOOP DAS NOTAS NAO ASSOCIADAS
    while($row_notas = mysql_fetch_assoc($qr_notas)){
        $row_notas['anexo'] = get_anexo ($row_notas['id_notas']);
        $row_notas['data_emissao'] = implode('/',array_reverse(explode('-',$row_notas['data_emissao'])));
        $row_notas['valor'] = number_format($row_notas['valor'],2,',','.');
        $row_notas['numero'] = utf8_encode($row_notas['numero']);
        unset($row_notas['descricao']);
        
        $array_response['nao_associada'][] = $row_notas;
    }
    
    // CONSULTANDO AS NOTAS ASSOCIADAS
    $qr_notas_assoc = mysql_query("SELECT * FROM notas_assoc WHERE id_entrada = '$id_entrada'");
    $array_de_entradas = array();
    
    while($rw_notas_assoc = mysql_fetch_assoc($qr_notas_assoc)){
        $array_de_entradas[] = $rw_notas_assoc['id_notas'];
    }
    
    // LOOP DAS NOTAS ASSOCIADAS
    while($row_notas_associadas = mysql_fetch_assoc($qr_notas_associadas)){
        $row_notas_associadas['anexo'] = get_anexo($row_notas_associadas['id_notas']);
        $row_notas_associadas['data_emissao'] = implode('/',array_reverse(explode('-',$row_notas_associadas['data_emissao'])));
        $row_notas_associadas['valor'] = number_format($row_notas_associadas['valor'],2,',','.');
        $row_notas_associadas['link_entrada'] = 'http://'.$_SERVER['HTTP_HOST'].'/intranet/finan/ver_entradas.php';
        $row_notas_associadas['checked'] = (in_array($row_notas_associadas['id_notas'],$array_de_entradas)) ? '1' : '0';
        
        // PEGANDO O TOTAL DE ENTRADAS
        $qr_total = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM entrada INNER JOIN notas_assoc ON entrada.id_entrada = notas_assoc.id_entrada WHERE notas_assoc.id_notas = '$row_notas_associadas[id_notas]' AND entrada.status = '2'");
        $row_notas_associadas['total_entrada'] = (float) @mysql_result($qr_total,0);
        $row_notas_associadas['total_entrada'] = number_format($row_notas_associadas['total_entrada'],2,',','.');
        $row_notas_associadas['numero'] = utf8_encode($row_notas_associadas['numero']);
        unset($row_notas_associadas['descricao']);
        
        $array_response['associada'][] = $row_notas_associadas;
    }
    
    $array_response['tot_assoc'][] = $num_notas_associadas;
    $array_response['tot_n_assoc'][] = $num_notas;
    
}

echo json_encode($array_response);

?>