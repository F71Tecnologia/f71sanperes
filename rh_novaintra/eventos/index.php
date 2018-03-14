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
$usuario = carregaUsuario(); // CARREGAR USUARIO
$eventos = new Eventos(); // classe Eventos
$eventoView = new EventoView(); // classe Evento View - com alguns componentes de visualização
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Eventos");
$breadcrumb_pages = array("Gestão de RH" => "../");
// CARREGAR REGIAO
$regiao = montaQuery("regioes", "*", array('id_regiao' => $usuario['id_regiao']));
$regiao = $regiao[1];


$usuarios_f71 = array(158, 179, 202, 260, 255, 256, 257, 258, 259);


// CARREGAR PROJETO
if (isset($_REQUEST['projeto'])) {
    $projeto = montaQuery("projeto", "id_projeto,nome", "id_projeto = {$_REQUEST['projeto']}");
    $projeto = $projeto[1];
}

// -----------------------------------------------------------------------------
// TELA PRINCIPAL --------------------------------------------------------------
if ($_REQUEST['tela'] == 'principal' || empty($_REQUEST['tela']) || !isset($_REQUEST['tela'])) {

    // DEFININDO VARIAVEIS DO REQUEST
    $rhstatus = (isset($_REQUEST ['rhstatus']) && !empty($_REQUEST ['rhstatus'])) ? $_REQUEST ['rhstatus'] : 10; // define rhstatus, em caso de não estar setado default = 10
    $pagina = (isset($_REQUEST['paginacao']) && !empty($_REQUEST['paginacao'])) ? $_REQUEST['paginacao'] : 1; // define página, em caso de não estar setado defaut = 1
    // define projeto e regiao na classe view evento
    $eventoView->projeto = $projeto['id_projeto'];
    $eventoView->regiao = $regiao['id_regiao'];

    // SELECT PROJETO
    $projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;
    $projetosOp = array("-1" => "-- TODOS OS PROJETOS --");
    $query = "SELECT id_projeto,nome FROM projeto WHERE id_regiao = '{$regiao['id_regiao']}'";
    $result = mysql_query($query) or die(mysql_error());
    while ($row = mysql_fetch_assoc($result)) {
        $projetosOp[$row['id_projeto']] = $row['id_projeto'] . " - " . $row['nome'];
    }
    // FIM SELECT PROJETO
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
    $clt = $_REQUEST['clt'];
    $id_regiao = $_REQUEST['regiao'];

    // dados para o select de eventos
    $qr_status = mysql_query("SELECT * FROM rhstatus WHERE status_reg = '1' AND codigo NOT IN (10,90,40,200) AND tipo IS NULL ORDER BY tipo,especifica ASC");
    $options['-1'] = "-- Selecione uma Ocorrência --";
    while ($row_status = mysql_fetch_assoc($qr_status)) {
        if (($row_status['codigo'] == '50' and ( $row_clt['sexo'] == 'f' or $row_clt['sexo'] == 'F')) or ( $row_status['codigo'] == '51' and ( $row_clt['sexo'] == 'm' or $row_clt['sexo'] == 'M')) or ( $row_status['codigo'] == '54' and ( $row_clt['sexo'] == 'f' or $row_clt['sexo'] == 'F')) or ( $row_status['codigo'] != '50' and $row_status['codigo'] != '51' and $row_status['codigo'] != '54')) {
            $options[$row_status['codigo']] = abreviacao($row_status['especifica']);
        }
    }

    // monta select e envia via ajax
    if (isset($_REQUEST['montaSelectEventos'])) {
        echo utf8_encode(montaSelect($options, null, 'name="evento" id="evento" class="form-control validate[required,custom[select]]"'));
        exit();
    }

    // excluir evento
    if (isset($_POST['set_status']) && ($_POST['set_status'] == 'zerar')) {
        $id_evento = isset($_POST['id']) ? $_POST['id'] : NULL;
        $id_clt = isset($_POST['clt']) ? $_POST['clt'] : NULL;
        $retorno = $eventos->removeEvento($id_evento, $id_clt);

        //$retorno = false;
        if ($retorno) {
            $msg = utf8_encode('Exclusão realizada com sucesso!');
            echo json_encode(array('status' => true, 'msg' => $msg));
        } else {
            $msg = utf8_encode('Não foi possível remover evento.');
            echo json_encode(array('status' => false, 'msg' => $msg));
        }
        exit();
    }

    // Consulta do Participante
    $qr_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$clt' AND id_regiao = '$id_regiao'");
    $row_clt = mysql_fetch_assoc($qr_clt);

    // Ação do Formulário
    if (isset($_POST['pronto'])) {
//        print_r($_REQUEST);exit();
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

//        $teste_evento = $eventos->verificaCltStatus($_POST['id_clt'], $data);
//        if ($teste_evento['num_rows'] > 0) {
//            $clt_em_evento = true;
//        } else {
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
        if ($result['status']) {
            $sucesso = true;
        }
//        }
    }

    // define classes que habilitaram ou desabilitarao formularios
    $class = ($row_clt['status'] != 10) ? 'class="show"' : '';
    $required = ($row_clt['status'] == 10) ? 'class="validate[required]"' : '';

    // HISTORICO
    $hist_eventos = $eventos->historico($clt, $id_regiao);

    // view
    include_once('acao_evento.php');

    exit();
}

// -----------------------------------------------------------------------------
// TELA EDIT EVENTO ------------------------------------------------------------
if (isset($_REQUEST['tela']) && $_REQUEST['tela'] == 'form_evento') {
//    $enc = str_replace('--', '+', $_REQUEST['enc']);
//    $link = decrypt($enc);
//
//    list($regiao, $clt, $id_evento, $data) = explode('&', $link);

    $clt = $_REQUEST['id_clt'];
    $id_evento = $_REQUEST['id_evento'];
    
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

        if ($dados_evento['dias'] != $dif && $dados_evento['dias'] != 0 && $dados_evento['dados_retorno'] != '0000-00-00') {
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


// -----------------------------------------------------------------------------
// TELA PRORROGAR EVENTO -------------------------------------------------------
if (isset($_REQUEST['tela']) && $_REQUEST['tela'] == "modal_prorrogar") {
    $campos_eventos = $eventos->getEventoById($_REQUEST['id']);
    $valor_campo = ($campos_eventos['pericia'] == 1) ? $campos_eventos['data_retorno_br'] : $campos_eventos['data_retorno_final_br'];
    include_once 'prorroga_evento.php';
    exit();
}


// -----------------------------------------------------------------------------
// TELA ANEXOS EVENTOS LIST ----------------------------------------------------
if (isset($_REQUEST['tela']) && $_REQUEST['tela'] == 'lista_anexo') {
    $id_evento = $_REQUEST['id'];
    if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'excluir_anexo_evento') {
        $id_anexo = $_REQUEST['id_anexo'];
        $arquivo = mysql_fetch_assoc(mysql_query("SELECT * FROM anexo_eventos WHERE id_anexo = $id_anexo"));
        $retorn = unlink("../anexo_atestado/{$arquivo['nome']}");
        $return2 = mysql_query("DELETE FROM anexo_eventos WHERE id_anexo=$id_anexo");
        echo json_encode(array('status' => ($retorn && $return2)));
        exit();
    }

    $query = "SELECT *,DATE_FORMAT(data,'%d/%m/%Y %T') AS data FROM anexo_eventos WHERE id_evento = '{$id_evento}' ORDER BY id_anexo";
    $result = mysql_query($query);
    list($id_clt, $id_regiao, $id_projeto, $clt_nome) = mysql_fetch_row(mysql_query("SELECT id_clt,id_regiao,id_projeto,nome FROM rh_clt WHERE id_clt = (SELECT id_clt FROM rh_eventos WHERE id_evento = $id_evento)"));

    switch ($_REQUEST['voltar']) {
        case 1:
            $link = "index.php?tela=acao_evento&clt=$id_clt&regiao=$id_regiao";
            break;

        default:
            $link = "/intranet/rh/ver_clt.php?reg=$id_regiao&clt=$id_clt&ant=0&pro=$id_projeto&pagina=bol";
            break;
    }
}
