<?php              
include('../conn.php');

//ALTERAวยO DE SALRIO
$id_regiao      = 45;
$salario_novo   =  '2517.92';
$ids_curso      = '1312,1330,1348,1411,1957,1949,1917,1909,1877,1869,1837,1829,1797,1789,1757,1989,2006,2231,2233';

$qr_curso = mysql_query("SELECT * FROM curso WHERE id_regiao = 45 AND id_curso IN($ids_curso)");
while($row_curso = mysql_fetch_assoc($qr_curso)){
    
    
        $diferenca = $salario_novo - $row_curso['salario'];
        
      /* $update = "INSERT INTO rh_salario (id_curso, data, salario_antigo, salario_novo, diferenca, user_cad, status)
                                            VALUES
                                            ({$row_curso['id_curso']}, NOW(), '{$row_curso['salario']}', '{$salario_novo}',{$diferenca}, '{$_COOKIE['logado']}','1' )"; ;  
       if(mysql_query($update)){
      
        //mysql_query("UPDATE curso SET salario = '$salario_novo' , valor = '$salario_novo' WHERE id_curso = {$row_curso['id_curso']} LIMIT 1");
        echo 'ok';
    }
      
     */ 
    
    
}
?>