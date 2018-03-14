<?php

if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br/><a href="../login.php">Logar</a>';
    exit;
}

//error_reporting(E_ALL);
// Incluindo Arquivos
require('../../conn.php');
include('../../funcoes.php');
include('../../wfunction.php');
include('../../classes/FornecedorClass.php');

$usuario = carregaUsuario(); // carrega dados do usuário
// para layout nova intra
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "35", "area" => "Compras e Contratos", "id_form" => "form1", "ativo" => "Fornecedores");
//$breadcrumb_pages = array(
//    "Visualizar Projeto"=>"../../ver.php",
//    'Visualizar CLT' => "../ver_clt.php?reg={$id_regiao}&pro={$id_projeto}&clt={$id_clt}"
//);
// fim para layout nova intra

$fornecedor = new Fornecedor();
//$fornecedor->debug = TRUE;
// variaveis gerais
//mascaras
$mask_cnpj = "##.###.###/####-##";
$mask_cep = "#####-###";

$method = (isset($_REQUEST['method'])) ? strtolower($_REQUEST['method']) : null;

switch ($method) {
    case 'salvar':
        $dados = array(
            'razao' => utf8_decode(str_replace("'", '', $_REQUEST['razao'])),
            'fantasia' => utf8_decode(str_replace("'", '', $_REQUEST['fantasia'])),
            'cod_ibge' => utf8_decode(str_replace("'", '', $_REQUEST['cod_ibge'])),
            'cep' => utf8_decode(str_replace("'", '', RemoveCaracteres($_REQUEST['cep']))),
            'uf' => utf8_decode(str_replace("'", '', $_REQUEST['uf'])),
            'mun' => utf8_decode(str_replace("'", '', $_REQUEST['mun'])),
            'num' => utf8_decode(str_replace("'", '', $_REQUEST['numero'])),
            'complemento' => utf8_decode(str_replace("'", '', $_REQUEST['complemento'])),
            'endereco' => utf8_decode(str_replace("'", '', $_REQUEST['endereco'])),
            'bairro' => utf8_decode(str_replace("'", '', $_REQUEST['bairro'])),
            'cnpj' => utf8_decode(str_replace("'", '', RemoveCaracteres($_REQUEST['cnpj']))),
            'ie' => utf8_decode(str_replace("'", '', $_REQUEST['ie'])),
            'im' => utf8_decode(str_replace("'", '', $_REQUEST['im'])),
            'tel' => utf8_decode(str_replace(" ", '', utf8_decode(str_replace("'", '', RemoveCaracteres($_REQUEST['tel']))))),
            'tel2' => utf8_decode(str_replace(" ", '', str_replace("'", '', RemoveCaracteres($_REQUEST['tel2'])))),
            'tel3' => utf8_decode(str_replace(" ", '', str_replace("'", '', RemoveCaracteres($_REQUEST['tel3'])))),
            'email' => utf8_decode(str_replace(" ", '', $_REQUEST['email'])),
            'site' => utf8_decode(str_replace("'", '', $_REQUEST['site'])),
            'contato' => utf8_decode(str_replace("'", '', $_REQUEST['contato'])),
            'obs' => utf8_decode(str_replace("'", '', $_REQUEST['obs'])),
            'cnae' => utf8_decode(str_replace("'", '', $_REQUEST['cnae'])),
        );
        if (isset($_REQUEST['id_fornecedor'])) {
            $dados['id_fornecedor'] = $_REQUEST['id_fornecedor'];
        }
        $salvo = $fornecedor->salvar($dados);
        echo json_encode($salvo);
        exit();
        break;

    case 'editar':
        $dados['id_fornecedor'] = $_REQUEST['id_fornecedor'];
        $cad_fornecedor = $fornecedor->consultar($dados);
        $cad_fornecedor = $cad_fornecedor[$dados['id_fornecedor']];
        $listaCNAE = getListaCNAE();
        require_once 'editar.php';
        exit();
        break;

    case 'excluir':
        $id_estabilidade = $_REQUEST['id'];
        $excluido = $fornecedor->excluir($id_estabilidade);
        echo json_encode($excluido);
        exit();
        break;

    case 'print':
        include('../../empresa.php');
        $img = new empresa();
        $dados['id_fornecedor'] = $_REQUEST['id_fornecedor'];
        $cad_fornecedor = $fornecedor->consultar($dados, NULL, NULL, TRUE);
        $cad_fornecedor = $cad_fornecedor[$dados['id_fornecedor']];
        require_once 'print.php';
        exit();
        break;

    case 'consultar':
        $dados['status'] = 1;
        $listaFornecedores = $fornecedor->consultar($dados);
        foreach ($listaFornecedores as $key1 => $row) {
            foreach ($row as $key2 => $value) {
                $listaFornecedores[$key1][$key2] = utf8_encode($value);
            }
        }
        require_once 'table_fornecedor.php';
        exit();
        break;

    case 'visualizar':
        $dados['id_fornecedor'] = $_REQUEST['id_fornecedor'];
        $cad_fornecedor = $fornecedor->consultar($dados, NULL, NULL, TRUE);
        $cad_fornecedor = $cad_fornecedor[$dados['id_fornecedor']];
        require_once 'visualizar.php';
        exit();
        break;

    case 'inicio':
    default:
        // consulta
        $dados['status'] = 1;
        $listaFornecedores = $fornecedor->consultar($dados);

        // form cadastro        
        $listaCNAE = getListaCNAE();

        require_once 'inicio.php';
        break;
}

// - funcoes -------------------------------------------------------------------
function getListaCNAE() {
    $query = "SELECT * FROM cnae ORDER BY codigo";
    $result = mysql_query($query);
    $return['-1'] = "-- Selecione --";
    while ($row = mysql_fetch_assoc($result)) {
        $return[$row['id_cnae']] = "{$row['codigo']} - {$row['descricao']}";
    }
    return $return;
}
