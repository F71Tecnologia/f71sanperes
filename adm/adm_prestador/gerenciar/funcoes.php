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
    $sql = "SELECT A.id_saida, A.ano_competencia, A.mes_competencia, A.nome, REPLACE(A.valor, ',', '.') AS valor, A.data_vencimento, DATE_FORMAT(A.data_vencimento, '%d/%m/%Y') AS data_vencimento_f, status from saida AS A WHERE id_prestador = $id_prestador ORDER BY ano_competencia DESC, mes_competencia DESC;";
    $pagamentos = array();
    $query = mysql_query($sql);
    while ($row = mysql_fetch_array($query)) {
        $pagamentos[] = $row;
    }
    return $pagamentos;
}

function getImpostoRetido($id_prestador){
    $sql = "SELECT id_saida,ano_competencia,mes_competencia,especifica, CAST(REPLACE(valor, ',', '.') as decimal(13,2))AS valor FROM saida WHERE tipo_nf = 1 AND id_prestador = $id_prestador AND status = 2 ORDER BY ano_competencia DESC, mes_competencia DESC;";
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
            A.c_responsavel AS prestador_responsavel, A.c_rg AS prestador_rg, A.c_cpf AS prestador_cpf, A.contratante,A.imprimir, A.cnpj AS cnpj_contratante, A.endereco AS endereco_contratante, A.c_fantasia AS nome_fantasia, A.c_cnpj AS cnpj, A.c_endereco AS endereco, A.co_municipio AS municipio, A.contratado_em, A.valor_limite, A.valor, A.nome_banco, A.agencia, A.conta,
            B.cidade, B.estado, B.nome AS nome_projeto, DAY(A.contratado_em) AS dia_contratado, MONTH(A.contratado_em) AS mes_contratado, YEAR(A.contratado_em) AS ano_contratado, A.id_cnae,
            E.razao AS empresa_razao, E.cnpj AS empresa_cnpj, E.endereco AS empresa_endereco, E.bairro AS empresa_bairro, E.cidade AS empresa_cidade, E.uf AS empresa_uf
            FROM prestadorservico AS A
            LEFT JOIN projeto AS B ON(A.id_projeto=B.id_projeto)
            LEFT JOIN master AS C ON(B.id_master=C.id_master)
            LEFT JOIN prestador_medico AS D ON(D.id_prestador = A.id_prestador AND D.principal=1)
            LEFT JOIN rhempresa AS E ON(E.id_regiao = A.id_regiao)
            WHERE A.id_prestador=$id_prestador";
    $query = mysql_query($sql);
    $prestador = array();
    while ($row = mysql_fetch_array($query)) {
        $prestador = $row;
    }
    return $prestador;
}
    function getMedicosPj($id_prestador){
//    $sql = "SELECT A.*, B.nome AS nome_especialidade, B.valor AS valor_especialidade, B.tipo AS tipo_especialidade FROM prestador_medico AS A
//            LEFT JOIN medico_especialidade AS B ON (A.especialidade=B.id_medico_especialidade)
//            WHERE A.id_prestador=$id_prestador AND A.status=1";
    $sql = "SELECT A.*, B.id_curso, B.nome AS nome_curso, B.valor_hora, B.salario salario  FROM terceirizado AS A LEFT JOIN curso AS B ON(A.id_curso=B.id_curso)WHERE A.id_prestador = '$id_prestador' ";    //AND A.contrato_medico=1
    $query = mysql_query($sql);
    $medicos = array();
    while ($row = mysql_fetch_array($query)) {
        
        $medicos[$row['id_terceirizado']]['id_terceirizado'] = $row['id_terceirizado'];
        $medicos[$row['id_terceirizado']]['nome'] = $row['nome'];
        $medicos[$row['id_terceirizado']]['tel'] = $row['tel_fixo'];
        $medicos[$row['id_terceirizado']]['cpf'] = $row['cpf'];
        $medicos[$row['id_terceirizado']]['crm'] = $row['carteira_conselho'];
        $medicos[$row['id_terceirizado']]['id_curso'] = $row['id_curso'];
        $medicos[$row['id_terceirizado']]['valor_hora'] = $row['valor_hora'];
        $medicos[$row['id_terceirizado']]['salario'] = $row['salario'];
        $medicos[$row['id_terceirizado']]['nome_curso'] = $row['nome_curso'];
//        $medicos[$row['id_medico']]['especialidade'] = $row['especialidade'];
//        $medicos[$row['id_medico']]['principal'] = $row['principal'];
//        $medicos[$row['id_medico']]['nome_especialidade'] = $row['nome_especialidade'];
//        $medicos[$row['id_medico']]['valor_especialidade'] = $row['valor_especialidade'];
//        $medicos[$row['id_medico']]['status'] = $row['status'];
    }
    return $medicos;
}


function getPeriodoContrato($id_prestador){
    $query = "SELECT id_prestador,contratado_em,encerrado_em,
                MONTH(contratado_em) AS mes_competencia_ini,
                YEAR(contratado_em) AS ano_competencia_ini, 
                MONTH(encerrado_em) AS mes_competencia_fim,
                YEAR(encerrado_em) AS ano_competencia_fim,
                valor
                FROM prestadorservico 
                WHERE id_prestador = $id_prestador;";
    $x = mysql_query($query) or die(mysql_error()."<br><br>".$query);
    return mysql_fetch_assoc($x);
}

function getPagametosByCompetencia($id_prestador,$mes,$ano){
    $query = "SELECT 
                ROUND(SUM(valor), 2) AS total,
                ROUND(SUM(IF(`status` = 2, valor, 0)), 2) AS total_pago,  
                ROUND(SUM(IF(`status` = 1, valor, 0)), 2) AS total_a_pagar,
                COUNT(id_saida) AS qtd_saidas
                FROM saida 
                WHERE id_prestador = $id_prestador AND status > 0
                AND mes_competencia = $mes 
                AND ano_competencia = $ano;";
    $x = mysql_query($query);
    return mysql_fetch_assoc($x);
}


function createDateRangeArray($strDateFrom,$strDateTo)
{
    // takes two dates formatted as YYYY-MM-DD and creates an
    // inclusive array of the dates between the from and to dates.

    // could test validity of dates here but I'm already doing
    // that in the main script

    $aryRange=array();

    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

    if ($iDateTo>=$iDateFrom)
    {
        array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
        while ($iDateFrom<$iDateTo)
        {
            $dt = end($aryRange);
            $arr_dt = explode('-', $dt);
//            $iDateFrom+=86400; // add 24 hours
            $iDateFrom+=86400*cal_days_in_month(CAL_GREGORIAN,$arr_dt[1],$arr_dt[0]); // add 24 hours
            array_push($aryRange,date('Y-m-d',$iDateFrom));
        }
    }
    return $aryRange;
}