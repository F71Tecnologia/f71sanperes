<?php

if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br/><a href="../login.php">Logar</a>';
    exit;
}

error_reporting(E_ALL);
// Incluindo Arquivos

require('../../conn.php');
include('../../funcoes.php');
include('../../wfunction.php');
include('../../classes/ContabilEmpresaClass.php');

include("../../classes/CnaeClass.php");
include("../prestadores/MunicipiosClass.php");

include("../prestadores/PrestadorSocioClass.php");
include("../prestadores/PrestadorDependenteClass.php");

$usuario = carregaUsuario(); // carrega dados do usuário
// para layout nova intra
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "2", "area" => "ADMINISTRATIVO", "id_form" => "form1", "ativo" => "Empresas");
//$breadcrumb_pages = array(
//    "Visualizar Projeto"=>"../../ver.php",
//    'Visualizar CLT' => "../ver_clt.php?reg={$id_regiao}&pro={$id_projeto}&clt={$id_clt}"
//);
// fim para layout nova intra

$objSocio = new SocioClass();
$objDependente = new PrestadorDependenteClass();

$objEmpresa = new ContabilEmpresa();
$objMunicipio = new MunicipiosClass();
$objCNAE = new Cnae();

//$objEmpresa->debug = TRUE;
// variaveis gerais
//mascaras
$mask_cnpj = "##.###.###/####-##";
$mask_cep = "#####-###";

$method = (isset($_REQUEST['method'])) ? strtolower($_REQUEST['method']) : null;

switch ($method) {
    case 'form':
        $painel = $_REQUEST['panel'];

        $dados['status'] = 1;
        $listaEmpresas = $objEmpresa->consultar($dados);

        $optionsTipos = array('-1' => '-- Selecione --', 1 => 'Prestador', 2 => 'Empresa');

        $empresa = $objEmpresa->consultar(array('id_empresa' => $_REQUEST['id_empresa']));
        $empresa = $empresa[$_REQUEST['id_empresa']];

        $cnae = $objCNAE->geraSelect();

        $empresaSELECT = $objEmpresa->consultar(array(), 'ORDER BY razao');
        $arrayEmpresas['0'] = '-- Selecione --';
        foreach ($empresaSELECT as $key => $value) {
            $arrayEmpresas[$key] = mascara_string($mask_cnpj, $value['cnpj']) . ' - ' . $value['razao'];
        }
        include 'form_empresa.php';
        break;
    case 'salvar':
//        print_array($_REQUEST);exit;
//        $dados = array(
//            'razao' => utf8_decode(str_replace("'", '', $_REQUEST['razao'])),
//            'fantasia' => utf8_decode(str_replace("'", '', $_REQUEST['fantasia'])),
//            'cod_ibge' => utf8_decode(str_replace("'", '', $_REQUEST['cod_ibge'])),
//            'cep' => utf8_decode(str_replace("'", '', RemoveCaracteres($_REQUEST['cep']))),
//            'uf' => utf8_decode(str_replace("'", '', $_REQUEST['uf'])),
//            'mun' => utf8_decode(str_replace("'", '', $_REQUEST['mun'])),
//            'num' => utf8_decode(str_replace("'", '', $_REQUEST['numero'])),
//            'complemento' => utf8_decode(str_replace("'", '', $_REQUEST['complemento'])),
//            'endereco' => utf8_decode(str_replace("'", '', $_REQUEST['endereco'])),
//            'bairro' => utf8_decode(str_replace("'", '', $_REQUEST['bairro'])),
//            'cnpj' => utf8_decode(str_replace("'", '', RemoveCaracteres($_REQUEST['cnpj']))),
//            'ie' => utf8_decode(str_replace("'", '', $_REQUEST['ie'])),
//            'im' => utf8_decode(str_replace("'", '', $_REQUEST['im'])),
//            'tel' => utf8_decode(str_replace(" ", '', utf8_decode(str_replace("'", '', RemoveCaracteres($_REQUEST['tel']))))),
//            'tel2' => utf8_decode(str_replace(" ", '', str_replace("'", '', RemoveCaracteres($_REQUEST['tel2'])))),
//            'tel3' => utf8_decode(str_replace(" ", '', str_replace("'", '', RemoveCaracteres($_REQUEST['tel3'])))),
//            'email' => utf8_decode(str_replace(" ", '', $_REQUEST['email'])),
//            'site' => utf8_decode(str_replace("'", '', $_REQUEST['site'])),
//            'contato' => utf8_decode(str_replace("'", '', $_REQUEST['contato'])),
//            'obs' => utf8_decode(str_replace("'", '', $_REQUEST['obs'])),
//            'cnae' => utf8_decode(str_replace("'", '', $_REQUEST['cnae'])),
//        );
//        if (isset($_REQUEST['id_objEmpresa'])) {
//            $dados['id_objEmpresa'] = $_REQUEST['id_objEmpresa'];
//        }
//        $salvo = $objEmpresa->salvar($dados);
//        echo json_encode($salvo);
//        exit();
//        break;
        $array_empresa = array(
            "razao" => $_REQUEST['razao'],
            "fantasia" => $_REQUEST['fantasia'],
            "id_municipio" => $_REQUEST['cod_cidade'],
            "cep" => RemoveCaracteres($_REQUEST['cep']),
            "uf" => $_REQUEST['uf'],
            "bairro" => $_REQUEST['bairro'],
            "endereco" => $_REQUEST['endereco'],
            "num" => $_REQUEST['numero'],
            "mun" => $_REQUEST['cidade'],
            "complemento" => $_REQUEST['complemento'],
            "cnpj" => RemoveCaracteres($_REQUEST['cnpj']),
            "ie" => RemoveCaracteres($_REQUEST['ie']),
            "im" => RemoveCaracteres($_REQUEST['im']),
            "tel" => str_replace(' ', '', RemoveCaracteres($_REQUEST['tel'])),
            "tel2" => str_replace(' ', '', RemoveCaracteres($_REQUEST['tel2'])),
            "email" => $_REQUEST['email'],
            "site" => $_REQUEST['site'],
            "cnae" => $_REQUEST['cnae'],
            'id_matriz' => $_REQUEST['id_matriz']
        );
        
//    print_array($array_empresa);exit();

        if (isset($_REQUEST['id_empresa']) && !empty($_REQUEST['id_empresa'])) {
            $array_empresa['id_empresa'] = $_REQUEST['id_empresa'];
        }
        
//        print_array($array_empresa);
        $retorno = $objEmpresa->salvar($array_empresa);
        
        if(empty($array_empresa['id_empresa'])){
            $array_empresa['id_empresa'] = mysql_insert_id();
        }
        
        foreach ($_REQUEST['socio']['nome'] as $key => $value) {
            if (!empty($value)) {
                $objSocio->setNome($_REQUEST['socio']['nome'][$key]);
                $objSocio->setTel($_REQUEST['socio']['tel'][$key]);
                $objSocio->setCpf($_REQUEST['socio']['cpf'][$key]);
                $objSocio->setIdContabilEmpresa($array_empresa['id_empresa']);
                if (!empty($_REQUEST['socio']['id_socio'][$key])) {
                    $objSocio->setIdSocio($_REQUEST['socio']['id_socio'][$key]);
                    $objSocio->updateSocio();
                    //echo "UPATE socio SET nome = '{$_REQUEST['socio']['nome'][$key]}', tel = '{$_REQUEST['socio']['tel'][$key]}', cpf = '{$_REQUEST['socio']['cpf'][$key]}' WHERE id_socio = '{$_REQUEST['socio']['id_socio'][$key]}' LIMIT 1;<br>";
                } else {
                    $objSocio->insertSocio();
                    //echo "INSERT INTO socio (nome, tel, cpf) VALUES ('{$_REQUEST['socio']['nome'][$key]}','{$_REQUEST['socio']['tel'][$key]}','{$_REQUEST['socio']['cpf'][$key]}');<br>";
                }
            }
        }

        foreach ($_REQUEST['dependente']['nome'] as $key => $value) {
            if (!empty($value)) {
                $objDependente->setNome($_REQUEST['dependente']['nome'][$key]);
                $objDependente->setTel($_REQUEST['dependente']['tel'][$key]);
                $objDependente->setParentesco($_REQUEST['dependente']['parentesco'][$key]);
                $objDependente->setIdContabilEmpresa($array_empresa['id_empresa']);
                if (!empty($_REQUEST['dependente']['id_dependente'][$key])) {
                    $objDependente->setIdDependente($_REQUEST['dependente']['id_dependente'][$key]);
                    $objDependente->updatePrestadorDependente();
                    //echo "UPATE dependente SET nome = '{$_REQUEST['dependente']['nome'][$key]}', tel = '{$_REQUEST['dependente']['tel'][$key]}', parentesco = '{$_REQUEST['dependente']['parentesco'][$key]}' WHERE id_socio = '{$_REQUEST['dependente']['id_dependente'][$key]}' LIMIT 1;<br>";
                } else {
                    $objDependente->insertPrestadorDependente();
                    //echo "INSERT INTO dependente (nome, tel, parentesco) VALUES ('{$_REQUEST['dependente']['nome'][$key]}','{$_REQUEST['dependente']['tel'][$key]}','{$_REQUEST['dependente']['parentesco'][$key]}')<br>";
                }
            }
        }

        $selectOption['razao_empresa'] = $_REQUEST['cnpj'] . " - " . $_REQUEST['razao'];
        $selectOption['id_empresa'] = $retorno['id_empresa'];

        $value = array('status' => ($retorno['status'])?'success':'danger', 'msg' => $retorno['msg'], 'option' => $selectOption);
        echo json_encode($value);
        exit();
        break;

    case 'editar':
        $dados['id_empresa'] = $_REQUEST['id_empresa'];
        $cad_empresa = $objEmpresa->consultar($dados);
        $empresa = $cad_empresa[$dados['id_empresa']];
        $painel = true;
        $cnae = $objCNAE->geraSelect();
        
        $grauParentesco = montaQuery("grau_parentesco");
        $optParentesco = array('' => "« Selecione o Parentesco »");
        foreach ($grauParentesco as $value) {
            $optParentesco[$value['id_grau']] = $value['nome'];
        }
        
        require_once 'editar.php';
        exit();
        break;

    case 'excluir':
        $id_estabilidade = $_REQUEST['id'];
        $excluido = $objEmpresa->excluir($id_estabilidade);
        echo json_encode($excluido);
        exit();
        break;

    case 'print':
        include('../../empresa.php');
        $img = new empresa();
        $dados['id_objEmpresa'] = $_REQUEST['id_objEmpresa'];
        $cad_objEmpresa = $objEmpresa->consultar($dados, NULL, NULL, TRUE);
        $cad_objEmpresa = $cad_objEmpresa[$dados['id_objEmpresa']];
        require_once 'print.php';
        exit();
        break;

    case 'consultar':
        $dados['status'] = 1;
        $listaEmpresas = $objEmpresa->consultar($dados);
        foreach ($listaEmpresas as $key1 => $row) {
            foreach ($row as $key2 => $value) {
                $listaEmpresas[$key1][$key2] = utf8_encode($value);
            }
        }
        require_once 'table_empresa.php';
        exit();
        break;

    case 'visualizar':
        $dados['id_empresa'] = $_REQUEST['id_enoresa'];
        $cad_objEmpresa = $objEmpresa->consultar($dados, NULL, NULL, TRUE);
        $cad_objEmpresa = $cad_objEmpresa[$dados['id_objEmpresa']];
        require_once 'visualizar.php';
        exit();
        break;

    case 'inicio':
    default:
// consulta
        $dados['status'] = 1;
        $listaEmpresas = $objEmpresa->consultar($dados);

        $optionsTipos = array('-1' => '-- Selecione --', 1 => 'Prestador', 2 => 'Empresa');

        $empresa = $objEmpresa->consultar(array('id_empresa' => $_REQUEST['id_empresa']));
        $empresa = $empresa[$_REQUEST['id_empresa']];

        $cnae = $objCNAE->geraSelect();

        $empresaSELECT = $objEmpresa->consultar(array(), 'ORDER BY razao');
        $arrayEmpresas['0'] = '-- Selecione --';
        foreach ($empresaSELECT as $key => $value) {
            $arrayEmpresas[$key] = mascara_string($mask_cnpj, $value['cnpj']) . ' - ' . $value['razao'];
        }
        
        $grauParentesco = montaQuery("grau_parentesco");
        $optParentesco = array('' => "« Selecione o Parentesco »");
        foreach ($grauParentesco as $value) {
            $optParentesco[$value['id_grau']] = $value['nome'];
        }
        
        $painel = true;

        require_once 'inicio.php';
        break;
}
