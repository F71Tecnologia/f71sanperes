<?php

//dao

function getRegioesFuncionario() {
    $id_user = isset($_COOKIE['logado']) ? $_COOKIE['logado'] : FALSE;
    if ($id_user) {
        $sql = "SELECT A.id_regiao,B.regiao FROM funcionario_regiao_assoc AS A
                                LEFT JOIN regioes AS B ON (A.id_regiao = B.id_regiao)
                                WHERE   id_funcionario = " . $id_user . " AND 
                                        A.id_master = " . $id_user . " ORDER BY A.id_regiao;";
        $query = mysql_query($sql);
        $regiao = array();
        while ($row = mysql_fetch_array($query)) {
            $regiao[$row['id_regiao']] = $row['id_regiao'] . ' - ' . $row['regiao'];
        }
        return $regiao;
    }
}

//function getPrestador($id_prestador) {
//    $sql = "SELECT A.*, B.nome AS nome_projeto FROM prestadorservico AS A
//            LEFT JOIN projeto AS B ON(A.id_projeto=B.id_projeto)
//            WHERE A.id_prestador=$id_prestador";
//    $query = mysql_query($sql);
//    $prestador = array();
//    while ($row = mysql_fetch_array($query)) {
//        $prestador = $row;
//    }
//    echo $sql . "<br>";
//    return $prestador;
//}

function getPagamentos($id_prestador) {
    $sql = "SELECT A.id_saida, A.ano_competencia, A.mes_competencia, A.nome, REPLACE(A.valor, ',', '.') AS valor, A.data_vencimento, DATE_FORMAT(A.data_vencimento, '%d/%m/%Y') AS data_vencimento_f from saida AS A WHERE id_prestador = $id_prestador ORDER BY ano_competencia DESC, mes_competencia DESC;";
    $pagamentos = array();
    $query = mysql_query($sql);
    while ($row = mysql_fetch_array($query)) {
        $pagamentos[] = $row;
    }
    return $pagamentos;
}


$estados = array(
    "AC" => "Acre",
    "AL" => "Alagoas",
    "AM" => "Amazonas",
    "AP" => "Amapá",
    "BA" => "Bahia",
    "CE" => "Ceará",
    "DF" => "Distrito Federal",
    "ES" => "Espírito Santo",
    "GO" => "Goiás",
    "MA" => "Maranhão",
    "MT" => "Mato Grosso",
    "MS" => "Mato Grosso do Sul",
    "MG" => "Minas Gerais",
    "PA" => "Pará",
    "PB" => "Paraíba",
    "PR" => "Paraná",
    "PE" => "Pernambuco",
    "PI" => "Piauí",
    "RJ" => "Rio de Janeiro",
    "RN" => "Rio Grande do Norte",
    "RO" => "Rondônia",
    "RS" => "Rio Grande do Sul",
    "RR" => "Roraima",
    "SC" => "Santa Catarina",
    "SE" => "Sergipe",
    "SP" => "São Paulo",
    "TO" => "Tocantins");

function getPrestador($id_prestador) {
    $sql = "SELECT A.id_prestador, A.numero, A.id_regiao,  A.especialidade, D.nome AS nome_medico, D.crm,A.prestador_tipo, C.cep AS cep_contratante, C.logradouro AS logradouro_contratante, C.bairro AS bairro_contratante, C.municipio AS municipio_contratante, C.uf AS uf_contratante, 
            A.responsavel AS prestador_responsavel, A.rg AS prestador_rg, A.cpf AS prestador_cpf, A.contratante,A.imprimir, A.cnpj AS cnpj_contratante, A.endereco AS endereco_contratante, A.c_fantasia AS nome_fantasia, A.c_cnpj AS cnpj, A.c_endereco AS endereco, A.co_municipio AS municipio, A.contratado_em, A.valor_limite, A.nome_banco, A.agencia, A.conta,
             B.cidade, B.estado, B.nome AS nome_projeto
            FROM prestadorservico AS A
            LEFT JOIN projeto AS B ON(A.id_projeto=B.id_projeto)
            LEFT JOIN master AS C ON(B.id_master=C.id_master)
            LEFT JOIN prestador_medico AS D ON(D.id_prestador = A.id_prestador AND D.principal=1)
            WHERE A.id_prestador=$id_prestador";
    echo $sql."<br>";
    $query = mysql_query($sql);
    $prestador = array();
    while ($row = mysql_fetch_array($query)) {
        $prestador = $row;
    }
    return $prestador;
}