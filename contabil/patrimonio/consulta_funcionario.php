<?php

$id_projeto = $_REQUEST["id_projeto"];

//echo $nome;

include_once '../../conn.php';


 
$sql = "SELECT B.nome, C.id_projeto, B.id_clt, A.id_responsavel_setor FROM responsavel_setor AS A
        LEFT JOIN rh_clt AS B ON B.id_clt = A.id_clt
        LEFT JOIN projeto AS C ON B.id_projeto = C.id_projeto
        WHERE B.id_projeto = '$id_projeto'";

$resultado_nome = mysql_query($sql);

    if(mysql_num_rows($resultado_nome) > 0){
        while($option = mysql_fetch_array($resultado_nome)){
                echo "<option value='{$option['id_responsavel_setor']}'>{$option['nome']}</option>";
                
              }       
                                            
        }
    

?>