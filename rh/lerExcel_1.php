<?php
    
        
// Exemplo de scrip para exibir os nomes obtidos no arquivo CSV de exemplo
 
$delimitador = ';';
$cerca = '"';
 
// Abrir arquivo para leitura
$f = fopen('../excel/Informações Funcionários Nasf 2.csv', 'r');
if ($f) {
    //$query = "INSERT INTO rh_clt (cpf,serie_ctps) VALUES ";
    // Enquanto nao terminar o arquivo
    $count = 0;
    while ($dados = fgetcsv($f, 0, $delimitador, $cerca)) {
        
        if($count > 0){
            /**
             * NOME
             */
            $nome  = utf8_encode($dados[0]);
            
            /**
             * DATA ENTRADA
             */
            $dataEntrada = explode("/",$dados[1]);
            $dataEntrada = implode("-",array_reverse($dataEntrada));
            
            /**
             * DATA NASCIMENTO
             */
            $dataNascimento = explode("/",$dados[6]);
            $dataNascimento = implode("-",array_reverse($dataNascimento));
            
            /**
             * ESCOLARIDADE
             */
            $escolaridade = utf8_encode($dados[12]);
                            
            /**
             * ENDEREÇO
             */
            $endereco = utf8_encode($dados[13]);
            
            /**
             * NUMERO 
             */
            $numero = utf8_encode($dados[14]);
            
            /**
             * COMPLEMENTO 
             */
            $complemento = utf8_encode($dados[15]);
            
            /**
             * BAIRRO
             */
            $bairro = utf8_encode($dados[16]);
            
            /**
             * CIDADE 
             */
            $cidade = utf8_encode($dados[17]);
            
            /**
             * CEP
             */
            $cep = mask($dados[18], '#####-###');
            
            /**
             * TELEFONE
             */
            $telefone = mask($dados[19], '(##) ####-####');
            
            /**
             * CELULAR
             */
            $celular = mask($dados[20], '(##) ####-#####');
            
            /**
             * CPF
             */
            //$cpf =  $dados[22]; //mask($dados[22], '###.###.###-##');
            $cpf = str_replace(".", "", $dados[22]);
            $cpf = str_replace("-", "", $cpf);
            
            /**
             * RG
             */
            $rg =  $dados[23]; //mask($dados[23], '##.###.###-#');
            
            /**
             * ORGÃO EMISSOR 
             */
            $emissor = $dados[24];
            
            /**
             * DATA EMISSÃO
             */
            $dataEmissao = explode("/",$dados[25]);
            $dataEmissao = implode("-",array_reverse($dataEmissao));
            
            /**
             * UF RG
             */
            $uf_rg = $dados[26];
            
            /**
             * N° CARTEIRA DE TRABALHO
             */
            $numCarteira = $dados[27];
            
            /**
             * N° CARTEIRA DE TRABALHO
             */
            $ctps = $dados[28];
            
            /**
             * TITULO
             */
            $titulo = $dados[31];
            
            /**
             * ZONA
             */
            $zona = $dados[32];
            
            /**
             * SEÇÃO
             */
            $sessao = $dados[33];
            
            /**
             * PIS
             */
            $pis = mask($dados[34], '###.#####.##-#');
            
            /**
             * DATA PIS
             */
            $dataPis = explode("/",$dados[35]);
            $dataPis = implode("-",array_reverse($dataPis));
            
            /**
             * 
             */
            $reservista = $dados[36];
            
            /**
             * NACIONALIDADE
             */
            $nacionalidade = $dados[38];          
            
            /**
             * EMAIL
             */
            $email = $dados[39];       
            
            /**
             * QUERY
             */
            
            $query .= "UPDATE rh_clt SET status_admi = '70' WHERE REPLACE(REPLACE(cpf,'.',''),'-','') = '{$cpf}';";
            //$query .= "('{$cpf}','{$ctps}'),";
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