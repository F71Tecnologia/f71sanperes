<?php

include ("../../conn.php");
include ("../../wfunction.php");
include("./actions/montaCaged.class.php");

if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

$usuario = carregaUsuario();


//$id_master = $usuario['id_master'];
$id_master = 6;

$sql_master = "SELECT * FROM master WHERE id_master =$id_master";
$result = mysql_query($sql_master);
$master = mysql_fetch_array($result);


echo '<h2>' . $master['id_master'] . '# ' . $master['nome'] . ' ' . $master['cnpj'] . ' </h2>';

//TIPO DE MOVIMENTO, numérico, 2 posições. Define o tipo de movimento.
//        
//ADMISSÃO 
//10 - Primeiro emprego 
//20 - Reemprego 
//25 - Contrato por prazo determinado 
//35 - Reintegração 
//70 - Transferência de entrada
//DESLIGAMENTO
//31 - Dispensa sem justa causa 
//32 - Dispensa por justa causa 
//40 - A pedido (espontâneo) 
//43 - Término de contrato por prazo determinado 
//45 - Término de contrato 
//50 - Aposentado 
//60 - Morte 
//80 - Transferência de saída
//1 - PEGAR TODOS OS ATIVOS DO MÊS CORRENTE
//3 - PEGAR TODOS OS ATIVOS DO MÊS PASSADO 
//4 - PEGAR QUEM ENTROU NO MÊS CORRENTE
//5 - PEGAR QUEM SAIU POR DEMISSÃO (PEGANDO O TIPO DE DEMISSÃO) NO MÊS CORRENTE  
//6 - PEGAR QUEM SAIU POR TRANSFERÊNCIA NO MÊS CORRENTE


function getEntradas($ano, $mes, $id_empresa, $tipo_entrada = FALSE) {
    $sql_tipo_entrada = ($tipo_entrada) ? "HAVING status_transf = '$tipo_entrada' " : '';
    $sql = "SELECT *
            FROM (
                    SELECT B.id_master, B.id_projeto, IF(temp.total > 0,'transferencia','admissao') AS status_transf, A.id_clt,C.cbo_codigo, C.salario, D.horas_semanais, A.data_entrada, A.status_admi, A.data_demi, A.`status`, F.id_regiao, F.regiao, E.cnpj, B.nome AS projeto_atual, A.nome,
                    REPLACE(
                    REPLACE(A.pis,'.',''), '-', '') AS pis_limpo,
                    REPLACE(
                    REPLACE(A.cpf, '.', ''), '-', '') AS cpf_limpo, IF(A.sexo = 'M', 1, 2) AS sexo, A.data_nasci, A.escolaridade, A.campo1, A.serie_ctps, A.uf_ctps, G.cod AS etnia, A.deficiencia,
                    REPLACE(A.cep, '-', '') AS cep_limpo
                    FROM rh_clt AS A                            
                            LEFT JOIN (SELECT COUNT(T.id_clt) AS total, T.id_clt	FROM rh_transferencias AS T GROUP BY T.id_clt) AS temp ON (A.id_clt = temp.id_clt)
                            LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
                            LEFT JOIN regioes AS F ON(F.id_regiao = B.id_regiao)
                            LEFT JOIN curso AS C ON (A.id_curso = C.id_curso)
                            LEFT JOIN rh_horarios AS D ON (C.id_horario = D.id_horario)
                            LEFT JOIN rhempresa AS E ON (E.id_projeto = B.id_projeto)
                            LEFT JOIN etnias AS G ON (A.etnia = G.id)
                    WHERE MONTH(A.data_entrada) = '$mes' AND YEAR(A.data_entrada) = '$ano' AND E.id_empresa = '$id_empresa'
                    GROUP BY A.id_clt
                    $sql_tipo_entrada) AS ADM
            ORDER BY ADM.id_projeto;";
    $entradas = array();
    $result = mysql_query($sql);
    while ($resp = mysql_fetch_array($result)) {
        $entradas[] = $resp;
    }
//    echo '<h4>SQL Entradas: '.$tipo_entrada.' '.count($entradas).'</h4> '.$sql.'<br>';
    return $entradas;
}

function getSaidas($ano, $mes, $id_master) {
    $sql = "SELECT B.id_master, B.id_projeto, A.id_clt,C.cbo_codigo, C.salario, D.horas_semanais, A.data_entrada, A.status_admi, A.data_demi, A.`status`, F.id_regiao, F.regiao, E.cnpj, B.nome AS projeto_atual, A.nome,
            REPLACE(
            REPLACE(A.pis,'.',''), '-', '') AS pis_limpo,
            REPLACE(
            REPLACE(A.cpf, '.', ''), '-', '') AS cpf_limpo, IF(A.sexo = 'M', 1, 2) AS sexo, A.data_nasci, A.escolaridade, A.campo1, A.serie_ctps, A.uf_ctps, G.cod AS etnia, A.deficiencia,
            REPLACE(A.cep, '-', '') AS cep_limpo
            FROM rh_clt AS A
            LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
            LEFT JOIN regioes AS F ON(F.id_regiao = B.id_regiao)
            LEFT JOIN curso AS C ON (A.id_curso = C.id_curso)
            LEFT JOIN rh_horarios AS D ON (C.id_horario = D.id_horario)
            LEFT JOIN rhempresa AS E ON (E.id_projeto = B.id_projeto)
            LEFT JOIN etnias AS G ON (A.etnia = G.id)
            WHERE DATE_FORMAT(A.data_demi,'%Y-%m') = '$ano-$mes' AND E.id_empresa = '$id_empresa' AND (A.data_entrada <= '$ano-$mes-01' OR (A.data_entrada > '$ano-$mes-01' AND A.data_entrada < DATE_ADD('$ano-$mes-01', INTERVAL 1 MONTH)))
            GROUP BY A.id_clt";
    $saidas = array();
    $result = mysql_query($sql);
    while ($resp = mysql_fetch_array($result)) {
        $saidas[] = $resp;
    }
//    echo '<br> <h4>SQL Saídas: '.count($saidas).'</h4> '.$sql."<br>";
    return $saidas;
}

function getDemitidos($ano, $mes) {
    $sql = "SELECT * FROM (SELECT IF(temp.total > 0,'tem','nao tem') AS status_transf, "
            . "A.id_clt,C.cbo_codigo, C.salario, D.horas_semanais, A.data_entrada, "
            . "A.status_admi, A.data_demi, A.`status`, F.id_regiao, F.regiao, B.id_projeto, "
            . "E.cnpj, B.nome AS projeto_atual, A.nome, REPLACE(REPLACE(A.pis,'.',''), '-', '') AS pis_limpo, "
            . "REPLACE(REPLACE(A.cpf, '.', ''), '-', '') AS cpf_limpo, IF(A.sexo = 'M', 1, 2) AS sexo, "
            . "A.data_nasci, A.escolaridade, A.campo1, A.serie_ctps, A.uf_ctps, G.cod AS etnia, A.deficiencia, "
            . "REPLACE(A.cep, '-', '') as cep_limpo FROM rh_clt AS A LEFT JOIN (SELECT COUNT(T.id_clt) AS total, "
            . "T.id_clt FROM rh_transferencias AS T GROUP BY T.id_clt) AS temp ON (A.id_clt = temp.id_clt) "
            . "LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto) LEFT JOIN regioes AS F ON(F.id_regiao = B.id_regiao) "
            . "LEFT JOIN curso AS C ON (A.id_curso = C.id_curso) LEFT JOIN rh_horarios AS D ON (C.id_horario = D.id_horario) "
            . "LEFT JOIN rhempresa AS E ON (E.id_projeto = B.id_projeto) LEFT JOIN etnias AS G ON (A.etnia = G.id) "
            . "WHERE MONTH(A.data_demi) = '$mes' AND YEAR(A.data_demi) = '$ano' AND B.id_master = F.id_master GROUP BY A.id_clt HAVING status_transf = 'nao tem') AS DEMI ORDER BY DEMI.id_projeto;";
    $demitidos = array();
    $result = mysql_query($sql);
    while ($resp = mysql_fetch_array($result)) {
        $demitidos[] = $resp;
    }
//    echo '<br> <h4>SQL Demitidos: '.count($demitidos).'</h4> '.$sql."<br>";
    return $demitidos;
}

function getEmpresaByIdMaster($id_master) {
    $sql = "SELECT * FROM rhempresa WHERE `status`=1 AND `id_master`=$id_master";
    $result = mysql_query($sql);
    $empresas = array();
    while ($resp = mysql_fetch_array($result)) {
        $empresas[] = $resp;
    }
    return $empresas;
}

$ano_inicial = isset($_GET['ano_inicial']) ? $_GET['ano_inicial'] : 10;
$ano_final = isset($_GET['ano_final']) ? $_GET['ano_final'] : 14;

$empresas = getEmpresaByIdMaster($id_master);
foreach ($empresas as $empresa) {

    echo '<h1>'.$empresa['nome'].'</h1>';
    $clt = array();
    $final_ano = 0;

    for ($y = $ano_inicial; $y <= $ano_final; $y++) {

        $ano = '20' . str_pad($y, 2, '0', STR_PAD_LEFT);

        for ($x = 1; $x <= 12; $x++) {
            //    $ano = '2012';
            $mes = str_pad($x, 2, '0', STR_PAD_LEFT);
            $mes_anterior = str_pad(($x - 1), 2, '0', STR_PAD_LEFT);
            $clt[$ano][$mes]['entradas'] = getEntradas($ano, $mes, $empresa['id_empresa']); // pega todas as entradas
            $clt[$ano][$mes]['transferencia'] = getEntradas($ano, $mes, $empresa['id_empresa'], 'transferencia'); // pega todas as entradas de transferência
            $clt[$ano][$mes]['admissao'] = getEntradas($ano, $mes, $empresa['id_empresa'], 'admissao'); // pega todas as entradas de admissão (que não tiveram transfêrencia)
            $clt[$ano][$mes]['saida'] = getSaidas($ano, $mes, $empresa['id_empresa']);


            if ($x == 1) {
                $clt[$ano][$mes]['primeiro_dia'] = $final_ano;
            } else {
                $clt[$ano][$mes]['primeiro_dia'] = (( count($clt[$ano][$mes_anterior]['entradas']) - count($clt[$ano][$mes_anterior]['saida'])) + $clt[$ano][$mes_anterior]['primeiro_dia']);
            }

            $clt[$ano][$mes]['ultimo_dia'] = ( count($clt[$ano][$mes]['entradas']) - count($clt[$ano][$mes]['saida']) + $clt[$ano][$mes]['primeiro_dia']);
//        $soma_total += $clt[$mes]['ultimo_dia'];
            echo '<h3>01/' . $mes . '/' . $ano . "</h3>";

            echo '<ul> '
            . '<li> Primeiro dia: ' . $clt[$ano][$mes]['primeiro_dia'] . '</li>'
            . '<li> Entradas: ' . count($clt[$ano][$mes]['entradas']) . '</li>'
            . '<li> Transferências: ' . count($clt[$ano][$mes]['transferencia']) . '</li>'
            . '<li> Admissões: ' . count($clt[$ano][$mes]['admissao']) . '</li>'
            . '<li> Saídas: ' . count($clt[$ano][$mes]['saida']) . '</li>'
            . '<li> Último dia: ' . $clt[$ano][$mes]['ultimo_dia'] . '</li></ul>';
            //    $clt['demitidos'] = getDemitidos($ano, $mes);
            echo '<hr>';
            $final_ano = $clt[$ano][$mes]['ultimo_dia'];
        }
        echo 'FINAL ANO ' . $ano . ' = ' . $final_ano;
    }
}