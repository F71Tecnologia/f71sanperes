<?php
include('../conn.php');


///CALCULO DE FÉRIAS
$qr_folha = mysql_query("select A.* FROM rh_folha as A
                                INNER JOIN rh_folha_proc as B
                                ON A.id_folha = B.id_folha
                                WHERE A.regiao = 45 AND A.status=3 
                                AND B.status = 3 AND A.terceiro != 1
                                 AND A.data_inicio BETWEEN DATE_SUB(NOW(), INTERVAL 7 MONTH) AND NOW()
                                AND B.id_clt = 4608;") or die(mysql_error());
while($row_folha = mysql_fetch_assoc($qr_folha)){      
    
    $ids_mov = $row_folha['ids_movimentos_estatisticas'];  

    $qr_movimento  = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = 4608 AND id_movimento IN($ids_mov) AND tipo_movimento = 'CREDITO'");
    while($row_mov = mysql_fetch_assoc($qr_movimento)){
 
        $movimentos_confere[$row_folha['mes']][$row_mov['nome_movimento']] += $row_mov['valor_movimento']; 
        $movimentos[$row_mov['nome_movimento']]         += $row_mov['valor_movimento']; 
}
    
}



echo '<pre>';
print_r($movimentos);
echo '</pre>';

echo '<pre>';
print_r($movimentos_confere);
echo '</pre>';


foreach($movimentos as $nome_mov => $valor){
    
    $total_media += ($valor/12)*6;
    
    echo 'média '.$nome_mov.' = '.($valor/12)*6;
    echo '<br>';
    
}
echo $total_media;