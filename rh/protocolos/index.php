<?php

if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}
define('MODELS', "../../classes/");
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/intranet/rh/protocolos/uploads/');

function loadClass($name) {
    $model = ucfirst($name) . 'Class';
    require_once(MODELS . $model . ".php");
    return new $model();
}

include "../../conn.php";
include "../../wfunction.php";

$usuario = carregaUsuario();
$optMeses = mesesArray();
$optAnos = anosArray();
$optRegiao = getRegioes();

$objProtocoloEntrega = loadClass('ProtocolosEntregas');
$objProtocoloArquivos = loadClass('ProtocolosArquivos');
$objProtocoloTipos = loadClass('ProtocolosTipos');

function mensagem_session($status, $mensagem) {
    $_SESSION['status'] = $status;
    $_SESSION['mensagem'] = $mensagem;
}

$method = $_REQUEST['m'];

$method = is_null($method) ? 'index' : $method;

switch ($method) {
    case 'novo':
        $optTiposProtocolos = $objProtocoloTipos->listaTiposToSelect();
        require_once 'form_protocolo.php';
        break;

    case 'editar':
        $objProtocoloEntrega->setIdProtocolosEntregas($_REQUEST['id']);
        $objProtocoloEntrega->getProtocoloEntregaById();
        $optTiposProtocolos = $objProtocoloTipos->listaTiposToSelect();

        $objProtocoloArquivos->setIdProtocolosEntregas($_REQUEST['id']);
        $listaArquivos = $objProtocoloArquivos->listaByIdProtocolosEntregas();

        require_once 'form_protocolo.php';
        break;

    case 'salvar':
        $objProtocoloEntrega->setIdProjeto($_REQUEST['id_projeto']);
        $objProtocoloEntrega->setIdRegiao($_REQUEST['id_regiao']);
        $objProtocoloEntrega->setMesCompetencia($_REQUEST['mes_competencia']);
        $objProtocoloEntrega->setAnoCompetencia($_REQUEST['ano_competencia']);
        $objProtocoloEntrega->setIdentificador(addslashes($_REQUEST['identificador']));
        $objProtocoloEntrega->setIdTipoProtocolo(addslashes($_REQUEST['id_tipo_protocolo']));
        $objProtocoloEntrega->setDescricao(addslashes($_REQUEST['descricao']));
        $objProtocoloEntrega->setStatus(1);
        $objProtocoloEntrega->setDataCad(date('Y-m-d H:i:s'));

        if (isset($_REQUEST['id_protocolos_entregas'])) {
            $objProtocoloEntrega->setIdProtocolosEntregas(addslashes($_REQUEST['id_protocolos_entregas']));
        }

        if ($objProtocoloEntrega->salvar()) {

            // editando arquivos
            foreach ($_REQUEST['id_protocolo_arquivo'] as $value) {
                $objProtocoloArquivos->setIdProtocolosArquivos($value);
                $objProtocoloArquivos->setIdProtocolosEntregas($objProtocoloEntrega->getIdProtocolosEntregas());
                $objProtocoloArquivos->salvar();
            }

            mensagem_session('success', 'Protocolo de entrada salvo com sucesso.');
        } else {
            mensagem_session('danger', 'Erro ao salvar protocolo de entrada.');
        }
        header('location: ?m=index');
        break;

    case 'dropzone_up':
        $nome = explode('.', $_FILES['file']['name']);
        $uploadfile = time() . '.' . $nome[1];
        if (move_uploaded_file($_FILES['file']['tmp_name'], UPLOAD_DIR . $uploadfile)) {
            $objProtocoloArquivos->setNomeArquivo($uploadfile);
            $objProtocoloArquivos->setDataCad(date('Y-m-d H:i:s'));
            $objProtocoloArquivos->setStatus(1);
            $objProtocoloArquivos->salvar();
            echo json_encode(['id' => $objProtocoloArquivos->getIdProtocolosArquivos()]);
        } else {
            echo json_encode(['id' => 0]);
        }
        break;

    case 'excluir_arquivo':
        $objProtocoloArquivos->setIdProtocolosArquivos($_REQUEST['id']);
        $objProtocoloArquivos->getProtocoloArquivoById();
        if ($objProtocoloArquivos->inativa()) {
            unlink(UPLOAD_DIR . $objProtocoloArquivos->getNomeArquivo());
            echo json_encode(['msg' => 'Excluido com sucesso.', 'status' => 'success']);
        } else {
            echo json_encode(['msg' => 'Erro ao excluir.', 'status' => 'danger']);
        }
        break;

    case 'excluir':
        $objProtocoloEntrega->setIdProtocolosEntregas($_REQUEST['id']);
        if($objProtocoloEntrega->inativa()){
            echo json_encode(['msg' => 'Excluido com sucesso.', 'status' => 'success']);
        } else {
            echo json_encode(['msg' => 'Erro ao excluir.', 'status' => 'danger']);
        }
        break;

    case 'consultar':
        $mes = filter_input(INPUT_POST, 'mes_competencia');
        $ano = filter_input(INPUT_POST, 'ano_competencia');
        $tipo_protocolo = filter_input(INPUT_POST, 'tipo_protocolo');

        $listaProtocolos = $objProtocoloEntrega->listaByPeriodo($mes, $ano, $tipo_protocolo);

    case 'index':
    default:
        $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

        $breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Seguro Desemprego");
        $breadcrumb_pages = array("Gestão de RH" => "../../rh/principalrh.php");

        $optTiposProtocolos = $objProtocoloTipos->listaTiposToSelect(TRUE);


        require_once 'form_consulta.php';
        break;
}