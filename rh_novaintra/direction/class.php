<?php
function getGeral($id_clt){
    $sql = mysql_query("SELECT A.nome AS nome_clt, A.endereco AS endereco_clt, A.numero AS num_clt, A.complemento AS complem_clt, A.foto AS foto_clt,
                        A.bairro AS bairro_clt, A.cidade AS cidade_clt, A.uf AS uf_clt, A.cep AS cep_clt, A.tel_cel AS cel_clt, A.id_clt AS id_clt,
                        A.email AS email_clt, B.unidade AS nome_uni, B.tel AS tel_uni, B.endereco AS endereco_uni, B.bairro AS bairro_uni, 
                        B.cidade AS cidade_uni, B.uf AS uf_uni, B.cep AS cep_uni, B.ponto_referencia AS ref_uni, B.id_regiao AS id_regiao, B.id_unidade AS id_uni,
                        B.campo1 AS id_projeto, C.regiao AS nome_regiao, D.nome AS nome_projeto, E.razao AS razao_empresa, E.cnpj AS cnpj_empresa 
                        FROM rh_clt AS A
                        LEFT JOIN unidade AS B ON(A.id_unidade = B.id_unidade)
                        LEFT JOIN regioes AS C ON(B.id_regiao = C.id_regiao)
                        LEFT JOIN projeto AS D ON(B.campo1 = D.id_projeto)
                        LEFT JOIN rhempresa AS E ON(B.campo1 = E.id_projeto)
                        WHERE A.id_clt = '{$id_clt}'") or die(mysql_error());
    $row = mysql_fetch_array($sql);
    return $row;
}

function removeGeral($variavel){
    $variavel = strtolower($variavel);
    $variavel = str_replace('(', "", $variavel);
    $variavel = str_replace(')', "", $variavel);
    $variavel = str_replace('-', '', $variavel);
    $variavel = str_replace('/', '', $variavel);
    $variavel = str_replace(":", "", $variavel);
    $variavel = str_replace(",", " ", $variavel);
    $variavel = str_replace('.', '', $variavel);
    $variavel = str_replace(";", "", $variavel);
    $variavel = str_replace("\"", "", $variavel);
    $variavel = str_replace("\'", "", $variavel);
    $variavel = str_replace("  ", "", $variavel);
    $variavel = str_replace("º", "", $variavel);
    $variavel = str_replace("ª", "", $variavel);
    $variavel = str_replace("andar", "", $variavel);
    $variavel = str_replace("sala", "", $variavel);
    $variavel = str_replace("sl", "", $variavel);
    $variavel = str_replace("apartamento", "", $variavel);
    $variavel = str_replace("apto", "", $variavel);
    $variavel = str_replace("apt", "", $variavel);
    $variavel = str_replace("ap", "", $variavel);    
    $variavel = str_replace("lote", "", $variavel);
    $variavel = str_replace("lt", "", $variavel);
    $variavel = str_replace("quadra", "", $variavel);    
    $variavel = str_replace("qd", "", $variavel);
    $variavel = str_replace("casa", "", $variavel);    
    $variavel = str_replace("cs", "", $variavel);
    $variavel = str_replace("bloco", "", $variavel);    
    //$variavel = str_replace("bl", "", $variavel);
    $variavel = str_replace("fds", "", $variavel);
    return trim(preg_replace('/([0-9]{1,})/i', "", $variavel));
}
?>