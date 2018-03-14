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

include('../../classes/EstabilidadeClass.php');
include('../../classes/ICalculosDatasClass.php');
include('../../classes/CalculosDatasClass.php');

$usuario = carregaUsuario(); // carrega dados do usuário

$id_projeto = (isset($_REQUEST['pro']))?$_REQUEST['pro']:$_REQUEST['id_projeto'];
$id_clt = $_REQUEST['id_clt'];
$id_regiao = (isset($_REQUEST['id_reg']))?$_REQUEST['id_reg']:$usuario['id_regiao'];

// para layout nova intra
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Estabilidade Provisória");
$breadcrumb_pages = array(
    "Visualizar Projeto"=>"../../ver.php",
    'Visualizar CLT' => "../ver_clt.php?reg={$id_regiao}&pro={$id_projeto}&clt={$id_clt}"
);
// fim para layout nova intra
    
$calcDatas = new CalculosDatas();
$estabilidade = new Estabilidade();
//$estabilidade->debug = TRUE;

$method = (isset($_REQUEST['method'])) ? strtolower($_REQUEST['method']) : null;

switch ($method) {
    case 'salvar':
        $dados = array(
            'id_projeto' => $_REQUEST['id_projeto'],
            'id_clt' => $_REQUEST['id_clt'],
            'nome' => $_REQUEST['nome'],
            'data_ini' => (!empty($_REQUEST['data_ini'])) ? ConverteData($_REQUEST['data_ini']) : '',
            'data_fim' => (!empty($_REQUEST['data_fim'])) ? ConverteData($_REQUEST['data_fim']) : '',
            'id_func' => $_COOKIE['logado'],
            'id_tipo' => $_REQUEST['tipo'],
            'obs' => utf8_decode($_REQUEST['obs'])
        );
        if(isset($_REQUEST['id_estabilidade'])){
            $dados['id_estabilidade'] = $_REQUEST['id_estabilidade'];
        }
        $salvo = $estabilidade->salvar($dados);
        echo json_encode($salvo);
        exit();
        break;
    
    case 'editar':
        $dados['id_estabilidade'] = $_REQUEST['id_estabilidade'];
        $dados_est = $estabilidade->consultar($dados);
        $dados_est = $dados_est[$dados['id_estabilidade']];
        require_once 'editar.php';
        exit();
        break;
    
    case 'excluir':
        $id_estabilidade = $_REQUEST['id'];
        $excluido = $estabilidade->excluir($id_estabilidade);
        echo json_encode($excluido);
        exit();
        break;
    
    case 'refresh_table':
        $dados['id_clt'] = $_REQUEST['id_clt'];;
        $list_estabilidades = $estabilidade->consultar($dados);
        require_once 'tabela_estabilidade.php';
        exit();
        break;
    case 'calcdata':
        $data_ini = ConverteData($_REQUEST['data_ini']);
        $tipo = $_REQUEST['tipo'];
        $dias = $estabilidade->getDiasTipo($tipo);
        $data_fim = $calcDatas->somarDias($dias, $data_ini);
        echo json_encode(array('data_fim'=>$data_fim));
        exit();
        break;
    case 'inicio':
    default:
        $id_clt = $_REQUEST['id_clt'];
        $clt = montaQuery('rh_clt', 'id_clt,id_projeto,nome', array('id_clt' => $id_clt));
        $clt = $clt[1];
        $dados['id_clt'] = $id_clt;
        $tipos = $estabilidade->getTipos();
        $selectTipos['-1'] = "-- Selecione --";
        $html = '';
        foreach ($tipos as $key => $value) {
            $selectTipos[$key] = $value['descricao'];
            
            $html_info .= "<h4>{$value['descricao']}</h4>";
            $html_info .= "<p class='text-justify'>{$value['detalhamento']}</p>";
        }
        $list_estabilidades = $estabilidade->consultar($dados);
        require_once 'inicio.php';
        break;
}

