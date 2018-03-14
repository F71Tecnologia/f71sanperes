<?php
 
include "../conn.php";
        
// Exemplo de scrip para exibir os nomes obtidos no arquivo CSV de exemplo
 
$delimitador = ';';
$cerca = '"';
$dataAtual = date("Y-m-d"); 

// Abrir arquivo para leitura
$f = fopen('../excel/Institucional_5_2016.csv', 'r');
if ($f) {
    $query = "INSERT INTO rh_movimentos_clt (id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,data_movimento,valor_movimento,lancamento,status,status_reg) VALUES ";
    // Enquanto nao terminar o arquivo
    $count = 0;
    while ($dados = fgetcsv($f, 0, $delimitador, $cerca)) {
        
//        echo "<pre>";
//            print_r($dados);
//        echo "</pre>";
        
        if($count >= 0){
            
            /**
             * ID
             */
            $id  = $dados[0] ;
            
             
            /**
             * BUSCANDO DADOS DO CLT 
             */
            $queryBuscaClt = "SELECT * FROM (
                                SELECT *, REPLACE(REPLACE(A.cpf,'.',''),'-','') as cpf_formatado
                                FROM rh_clt AS A
                            ) as tmp WHERE id_clt = '{$id}'";
            $sqlBuscaClt = mysql_query($queryBuscaClt) or die("Erro ao selecionar participante");
            $id_clt = ""; $id_regiao = ""; $id_projeto = "";
            if(mysql_num_rows($sqlBuscaClt) > 0){
                while($rowsClt = mysql_fetch_assoc($sqlBuscaClt)){
                    $id_clt = $rowsClt['id_clt'];
                    $id_regiao = $rowsClt['id_regiao'];
                    $id_projeto = $rowsClt['id_projeto'];
                }
            }                     
            
            /**
             * CODIGO MOVIMENTO
             */
            $cod  = $dados[6];           
            
            /**
             * BUSCANDO INFORMAÇÃO DO MOVIMENTO
             */
            $queryBuscaMov = "SELECT * FROM rh_movimentos AS A WHERE A.cod = '{$cod}'";
            $sqlBuscaMov = mysql_query($queryBuscaMov) or die("Erro ao selecionar participante");
            $id_mov = ""; $cod_mov = ""; $tipo_mov = ""; $nome_mov = "";
            if(mysql_num_rows($sqlBuscaMov) > 0){
                while($rowsMov = mysql_fetch_assoc($sqlBuscaMov)){
                     $id_mov   = $rowsMov['id_mov'];
                     $cod_mov  = $rowsMov['cod'];
                     $tipo_mov = $rowsMov['categoria'];
                     $nome_mov = $rowsMov['descicao'];
                }
            }  
            
            
            /**
             * VALOR
             */
            $valor  = str_replace(".", "", $dados[9]);                     
            $valor  = str_replace(",", ".", $valor);                     
            $valor  = str_replace("R$ ", "", $valor);                     
            
            /**
             * QUERY
             */
            $nome_mov .= ""; 
            $query .= "('{$id_clt}','{$id_regiao}','{$id_projeto}','05','2016','{$id_mov}','{$cod_mov}','{$tipo_mov}','{$nome_mov}','{$dataAtual}','{$valor}',1,1,1),";
        }
        $count++;
    }
    $query = substr($query,0,-1);
    echo $query;
    
    fclose($f);
}

/**
 * 
 * @param type $val
 * @param type $mask
 * @return type
 */
function mask($val, $mask) {

    $maskared = '';
    $k = 0;
    for ($i = 0; $i <= strlen($mask) - 1; $i++) {

        if ($mask[$i] == '#') {
            if (isset($val[$k]))
                $maskared .= $val[$k++];
        }else {
            if (isset($mask[$i]))
                $maskared .= $mask[$i];
        }
    }

    return $maskared;
}