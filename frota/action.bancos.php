<?php

include "../conn.php";
mysql_query ('SET character_set_client=utf8');
mysql_query ('SET character_set_connection=utf8');
mysql_query ('SET character_set_results=utf8');

	//EXECUTANDO O AJAX CARREGANDO OS BANCO DE ACORDO COM A REGIÃO SELECIONADA
	
	
	$projeto = $_REQUEST['projeto'];
        
        $qr_projeto = mysql_query("SELECT  master.id_master
                                    FROM projeto
                                   INNER JOIN regioes
                                   on regioes.id_regiao = projeto.id_regiao
                                   INNER JOIN master
                                   ON master.id_master = regioes.id_master
                                   WHERE projeto.id_projeto = '$projeto'");

        $row_projeto = mysql_fetch_assoc($qr_projeto);
	
        if($row_projeto['id_master'] == 1){ 
            
            $projetos2 = array();
            $qr_proj_fahjel = mysql_query("SELECT * FROM projeto WHERE id_master = 4");
            while($row_fahjel     = mysql_fetch_assoc($qr_proj_fahjel)):
                
                $projetos2[] = $row_fahjel['id_projeto'];
                
            endwhile;
            $projetos2 = implode(',',$projetos2);
            
            $sql = "id_projeto IN ($projeto,$projetos2) ";
           
        } else {
            
             $sql = "id_projeto IN($projeto)";
        }
        
      
	$result_banco = mysql_query("SELECT * FROM bancos WHERE $sql  AND status_reg = 1");
	
	while($row_banco = mysql_fetch_array($result_banco)){
		print "<option value='$row_banco[0]'>$row_banco[id_banco] - $row_banco[nome] - $row_banco[agencia] / $row_banco[conta]</option>";
	}
?>