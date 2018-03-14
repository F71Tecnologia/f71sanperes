<?php

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../funcoes.php');
include('../../classes/abreviacao.php');
include('../../classes/ICalculosDatasClass.php');
include('../../classes/CalculosDatasClass.php');
include('../../classes/EventoClass.php');
include('../../classes/EventoViewClass.php');
include "../../classes_permissoes/acoes.class.php";


$ACOES = new Acoes(); // ACOES
// CARREGAR USUARIO
$usuario = carregaUsuario();

$eventos = new Eventos();
// CARREGAR REGIAO
$regiao = montaQuery("regioes", "*", array('id_regiao' => $usuario['id_regiao']));
$regiao = $regiao[1];


$usuarios_f71 = array(158,179,202,260,255,256,257,258,259);

// CARREGAR PROJETO
if (isset($_REQUEST['projeto'])) {
    $projeto = montaQuery("projeto", "id_projeto,nome", "id_projeto = {$_REQUEST['projeto']}");
    $projeto = $projeto[1];
}

// -----------------------------------------------------------------------------
// TELA PRINCIPAL --------------------------------------------------------------
if ($_REQUEST['tela'] == 'principal' || empty($_REQUEST['tela']) || !isset($_REQUEST['tela'])) {
// DESCRIPTOGRAFA REQUEST[ENC]
//    list($regiao, $evento) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
    // DEFININDO VARIAVEIS DO REQUEST
    $rhstatus = (isset($_REQUEST ['rhstatus']) && !empty($_REQUEST ['rhstatus'])) ? $_REQUEST ['rhstatus'] : 10; // define rhstatus, em caso de não estar setado default = 10
    $pagina = (isset($_REQUEST['paginacao']) && !empty($_REQUEST['paginacao'])) ? $_REQUEST['paginacao'] : 1; // define página, em caso de não estar setado defaut = 1
    // ABAS DE STATUS
    $cond_proj = (isset($_REQUEST['projeto']) && !empty($_REQUEST['projeto']) && $_REQUEST['projeto'] != '-1') ? "AND B.id_projeto = {$_REQUEST['projeto']}" : "";
    $qr_btn_status = "SELECT A.especifica, A.codigo, A.tipo, COUNT(A.codigo) AS total
                        FROM rhstatus AS A 
                        INNER JOIN rh_clt AS B ON(A.codigo = B.status)
                        WHERE B.id_regiao = ' {$regiao['id_regiao']}' $cond_proj AND A.status_reg = 1 AND A.codigo NOT IN(40,90,60,101,81,62,61,64,66,65,63)
                        GROUP BY A.codigo
                        ORDER BY A.especifica ";
    $sql_btn_status = mysql_query($qr_btn_status) or die("Erro ao selecionar clt com eventos");
    while ($row_status = mysql_fetch_assoc($sql_btn_status)) {
        $btn_status[] = array(
            'href' => '#' . $row_status['codigo'],
            'title' => 'Visualizar Participantes em ' . $row_status['especifica'],
            'html' => abreviacao($row_status['especifica'], 5) . ' (' . $row_status['total'] . ')',
            'rhstatus' => $row_status['codigo']
        );
        $total = ($rhstatus == $row_status['codigo']) ? $row_status['total'] : $total;
    }
    // FIM ABAS DE STATUS
    // 
    // SELECT PROJETO
    $projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;
    $projetosOp = array("-1" => "-- TODOS OS PROJETOS --");
    $query = "SELECT id_projeto,nome FROM projeto WHERE id_regiao = '{$regiao['id_regiao']}'";
    $result = mysql_query($query) or die(mysql_error());
    while ($row = mysql_fetch_assoc($result)) {
        $projetosOp[$row['id_projeto']] = $row['id_projeto'] . " - " . $row['nome'];
    }
    // FIM SELECT PROJETO


    $consultaByNome = (isset($_REQUEST['clt_nome']) && !empty($_REQUEST['clt_nome'])) ? true : false;

    // LISTANDO FUNCIONARIOS
    $participantes = $eventos->listarCltEmEvento($regiao['id_regiao'], $projeto['id_projeto'], $rhstatus, $_REQUEST['clt_nome'], $_REQUEST['inicial'], $pagina);
    // FIM LISTANDO FUNCIONARIOS
    // VIEW
    include_once('rh_eventos_principal.php');
    exit();
}

// -----------------------------------------------------------------------------
// TELA ACAO EVENTOS -----------------------------------------------------------
if (isset($_REQUEST['tela']) && $_REQUEST['tela'] == 'acao_evento') {
    session_start();

// Variáveis
    $clt = $_GET['clt'];
    $id_regiao = $_GET['regiao'];

// excluir evento
    if (isset($_POST['set_status']) && ($_POST['set_status'] == 'zerar')) {
        $id_evento = isset($_POST['id']) ? $_POST['id'] : NULL;
        $id_clt = isset($_POST['clt']) ? $_POST['clt'] : NULL;
        $retorno = $eventos->removeEvento($id_evento, $id_clt);

        //$retorno = false;
        if ($retorno) {
            $msg = utf8_encode('Exclusão realizada com sucesso!');
            $resp = array('status' => true, 'msg' => $msg);
            echo json_encode($resp);
        } else {
            $msg = utf8_encode('Não foi possível remover evento.');
            $resp = array('status' => false, 'msg' => $msg);
            echo json_encode($resp);
        }
        exit();
    }

// carrega dados da tabela rh_eventos no modal
    if (isset($_POST['carregaCampos'])) {
        $campos_eventos = $eventos->getEventoById($_POST['id']);
        $campos_eventos['obs'] = utf8_encode($campos_eventos['obs']);
        $campos_eventos['nome_status'] = utf8_encode($campos_eventos['nome_status']);
        echo json_encode($campos_eventos);
        exit();
    }

// Consulta do Participante
    $qr_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$clt' AND id_regiao = '$id_regiao'");
    $row_clt = mysql_fetch_assoc($qr_clt);

// Ação do Formulário
    if (isset($_POST['pronto'])) {

//        print_r($_POST);exit();
        
        $data = implode('-', array_reverse(explode('/', $_POST['data'])));

        $data_final = implode('-', array_reverse(explode('/', $_POST['data_final'])));
        $data_retorno = implode('-', array_reverse(explode('/', $_POST['data_retorno'])));

        if ($_POST['evento'] != 10 and ! empty($dias)) {
            $data_retorno = date('Y-m-d', strtotime("+" . $dias . " days", strtotime($data)));
        }

        if ($_POST['evento'] == 200) {
            $sql_update_clt = ", data_demi = '$data'";
        }

        $qr_status = mysql_query("SELECT * FROM rhstatus WHERE codigo = '$evento'");
        $row_status = mysql_fetch_assoc($qr_status);

        // verifica se clt está em evento
        $query_teste = "SELECT * FROM rh_eventos
                    WHERE id_clt = '{$_REQUEST['id_clt']}' 
                    AND '$data' <= data
                    AND status = 1;";
//        echo $query_teste;
        $resp = mysql_query($query_teste);
        if (mysql_num_rows($resp)) {
            $clt_em_evento = true;
            //header('location:form_eventos.php?enc=' . str_replace('+', '--', encrypt("$regiao&$id_clt&$ultimo_evento&$data")));
        } else {
            $dados_evento = array(
                'id_clt' => $_POST['id_clt'],
                'id_regiao' => $_POST['regiao'],
                'id_projeto' => $_POST['projeto'],
                'data' => $data,
                'data_retorno' => $data_retorno,
                'data_retorno_final' => $data_final,
                'dias' => $_POST['dias'],
                'obs' => $_POST['observacao']
            );

            $result = $eventos->cadEvento($_POST['evento'], $dados_evento);

            $ultimo_evento = $result['ultimo_id'];
            if ($result) {
                $sucesso = true;
            }
        }
    }

    // dados para o select de eventos
    $qr_status = mysql_query("SELECT * FROM rhstatus WHERE status_reg = '1' AND codigo NOT IN (10,90,40,200) AND tipo IS NULL ORDER BY tipo,especifica ASC");
    $options['-1'] = "-- Selecione uma Ocorrência --";
    while ($row_status = mysql_fetch_assoc($qr_status)) {

        if (($row_status['codigo'] == '50' and ( $row_clt['sexo'] == 'f' or $row_clt['sexo'] == 'F')) or ( $row_status['codigo'] == '51' and ( $row_clt['sexo'] == 'm' or $row_clt['sexo'] == 'M')) or ( $row_status['codigo'] == '54' and ( $row_clt['sexo'] == 'f' or $row_clt['sexo'] == 'F')) or ( $row_status['codigo'] != '50' and $row_status['codigo'] != '51' and $row_status['codigo'] != '54')) {
            $options[$row_status['codigo']] = abreviacao($row_status['especifica']);
        }
    }

    // HISTORICO
    $hist_eventos = $eventos->historico($clt, $id_regiao);
    include_once('acao_evento.php');
    exit();
}

// -----------------------------------------------------------------------------
// EDIT EVENTO -----------------------------------------------------------------
if (isset($_REQUEST['tela']) && $_REQUEST['tela'] == 'form_evento') {
    $eventos = new Eventos();

    $enc = str_replace('--', '+', $_REQUEST['enc']);
    $link = decrypt($enc);

    list($regiao, $clt, $id_evento, $data) = explode('&', $link);

    if (isset($_REQUEST['salvar'])) {

      
        $dados_evento['cod_status'] = isset($_REQUEST['evento']) ? $_REQUEST['evento'] : NULL;
        $dados_evento['dias'] = isset($_REQUEST['dias']) ? $_REQUEST['dias'] : NULL;
        $dados_evento['data'] = isset($_REQUEST['data']) ? implode('-', array_reverse(explode('/', $_REQUEST['data']))) : NULL;
        $dados_evento['data_retorno'] = isset($_REQUEST['data_retorno']) ? implode('-', array_reverse(explode('/', $_REQUEST['data_retorno']))) : NULL;
        $dados_evento['data_retorno_final'] = isset($_REQUEST['data_final']) ? implode('-', array_reverse(explode('/', $_REQUEST['data_final']))) : NULL;
        $dados_evento['obs'] = isset($_REQUEST['observacao']) ? $_REQUEST['observacao'] : NULL;
        $id_evento = isset($_REQUEST['id_evento']) ? $_REQUEST['id_evento'] : NULL;

        $pericia = $eventos->getPericia($dados_evento['cod_status']);
//        $data_calculo = ($pericia['pericia'] == 1 && $dados_evento['data_retorno_final'] == NULL) ? $dados_evento['data_retorno'] : $dados_evento['data_retorno_final']; //para saber qual data usar
        $data_calculo = $dados_evento['data_retorno'];
        $dif = $eventos->nova_qnt_dias($dados_evento['data'], $data_calculo);

        if ($dados_evento['dias'] != $dif) {
            $resp = array(
                'class' => 'back-red',
                'msg' => 'Favor verificar as data informadas'
            );
        } else {
            $update = $eventos->editarEvento($id_evento, $dados_evento);
            $resp = ($update['status']) ? array('class' => 'back-green', 'msg' => 'Dados gravados com sucesso.') : array('class' => 'back-red', 'msg' => 'Erro ao salvar dados.');
        }
    }

    $qr_clt = mysql_query("SELECT id_clt,nome,id_projeto,id_regiao,id_curso,
        (SELECT nome FROM projeto WHERE id_projeto = rh_clt.id_projeto) AS projeto,
        (SELECT regiao FROM regioes WHERE id_regiao = rh_clt.id_regiao) AS regiao,
        (SELECT nome FROM curso WHERE id_curso = rh_clt.id_curso) AS curso
        FROM rh_clt WHERE id_clt = '$clt'") or die("Erro ao consultar CLT: " . mysql_error());
    $row_clt = mysql_fetch_array($qr_clt);

    $row_evento = $eventos->getEventoById($id_evento);

    $tem_pericia = $eventos->getPericia($row_evento['cod_status']);
    $tem_pericia = $tem_pericia['pericia'];

    include_once('edit_evento.php');
    exit();
}