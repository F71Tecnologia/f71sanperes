<?php 
require("../../conn.php");
$charset = mysql_set_charset('utf8');




$query_fornecedores = mysql_query("SELECT * 
                                    FROM  fornecedores 
                                    WHERE id_regiao = '$_REQUEST[regiao]' AND
                                    id_projeto = '$_REQUEST[projeto]' 
                                        AND status = 1
                                    ORDER BY nome
                                    ");

while($row_forn = mysql_fetch_assoc($query_fornecedores)){
    
    $json[] = $row_forn;
	
    
} 
echo json_encode($json);

?>