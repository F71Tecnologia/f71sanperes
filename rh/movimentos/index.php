<?php

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../funcoes.php');
include('../../classes/movimentosClass.php');

$usuario = carregaUsuario();
$tela = $_REQUEST['tela'];

switch ($tela) {
    case '1':

        if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) || (isset($_SESSION['voltarPrestador']))) {
            $filtro = true;
            if (isset($_SESSION['voltarPrestador'])) {
                $_REQUEST['regiao'] = $_SESSION['voltarPrestador']['id_regiao'];
                $_REQUEST['projeto'] = $_SESSION['voltarPrestador']['id_projeto'];
                unset($_SESSION['voltarPrestador']);
            }
        }

        /* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
        $projetoR = (!empty(($_REQUEST['projeto']))) ? $_REQUEST['projeto'] : $usuario[id_projeto];
        $regiaoR = (!empty(($_REQUEST['regiao']))) ? $_REQUEST['regiao'] : $usuario[id_regiao];

        if (isset($_REQUEST['projeto']) && !empty($_REQUEST['projeto'])) {
            $clt = listaCLT($_REQUEST['projeto']);
        }

        $regiaoArray = showRegiao($regiaoR);

        include_once 'relatorioMovimentos-listarParticipantes.php';

        break;

    case '2':
        $id_clt = decrypt(str_replace(' ', '+', $_REQUEST['id']));
        $id_projeto = decrypt(str_replace(' ', '+', $_REQUEST['projeto']));
        $id_regiao = decrypt(str_replace(' ', '+', $_REQUEST['regiao']));

        $regiaoArray = showRegiao($id_regiao);
        $projetoArray = showProjeto($id_projeto);

        $clt = listaCLT($id_projeto, $id_clt);

        include_once 'relatorioMovimentos.php';

        break;

    case '3':
        $id_clt = $_REQUEST['id_clt'];
        $id_projeto = $_REQUEST['id_projeto'];
        $id_regiao = $_REQUEST['id_regiao'];
        $mesIni = sprintf("%02d", $_REQUEST['mes_ini']);
        $ano = $_REQUEST['ano'];

        $regiaoArray = showRegiao($id_regiao);
        $projetoArray = showProjeto($id_projeto);

        $clt = listaCLT($id_projeto, $id_clt);
        $movimento = new movimentos($id_projeto, $id_clt, $mesIni, $ano);

        $movimentos = $movimento->gerarRelatorio($id_projeto, $id_clt, $mesIni, $ano);
        $mesFim = $movimento->mesFim;
        $anoFim = $movimento->anoFim;

//        echo '<pre>';
//        print_r($movimento);
//        echo '</pre>';


        include_once 'relatorioMovimentos.php';

        break;
}

// ------------------- FUNÇÕES -------------------------------------------------

/*
 * função para retornar os clts
 */

function listaCLT($id_projeto, $id_clt = NULL) {
    if (isset($id_clt)) {
        $cond_id = " AND id_clt = {$id_clt}";
    } else {
        $cond_id = '';
    }
    $query = "SELECT a.id_clt,a.matricula,a.nome,a.`status`,a.locacao,b.nome as `curso`
                FROM rh_clt as a
                INNER JOIN curso as b on (a.id_curso = b.id_curso)
                WHERE id_projeto = '{$id_projeto}' {$cond_id} AND (a.status < '60' OR a.status = '200') ORDER BY nome ASC";
    $result = mysql_query($query);
    $i = 1;
    while ($row = mysql_fetch_assoc($result)) {
        $clt[$i] = $row;
        $i++;
    }
    return $clt;
}

/*
 * função para exibir dados da Região
 */

function showRegiao($id_regiao) {
    $tabela = "regioes";
    $campos = "*";
    $condicao = "id_regiao = {$id_regiao}";
    return montaQuery($tabela, $campos, $condicao);
}

/*
 * função para exibir dados do Projeto
 */

function showProjeto($id_projeto) {
    $tabela = "projeto";
    $campos = "*";
    $condicao = "id_projeto = {$id_projeto}";
    return montaQuery($tabela, $campos, $condicao);
}

/*
 * função para exibir nome curto do mês
 */

function mesCurto($mes) {
    $array = array('01' => 'JAN', '02' => 'FEV', '03' => 'MAR', '04' => 'ABR', '05' => 'MAI', '06' => 'JUN', '07' => 'JUL', '08' => 'AGO', '09' => 'SET', '10' => 'OUT', '11' => 'NOV', '12' => 'DEZ',
        '1' => 'JAN', '2' => 'FEV', '3' => 'MAR', '4' => 'ABR', '5' => 'MAI', '6' => 'JUN', '7' => 'JUL', '8' => 'AGO', '9' => 'SET');
    return $array[$mes];
}
