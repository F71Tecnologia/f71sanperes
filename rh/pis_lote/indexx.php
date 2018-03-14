<?php

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include "../../classes/FormataDadosClass.php";
include "../../wfunction.php";
include('../../funcoes.php');
include('../../classes/abreviacao.php');
include "../../classes_permissoes/acoes.class.php";
include "dao/PisLoteClass.php";

//error_reporting(E_ALL);

$usuario = carregaUsuario(); // CARREGAR USUARIO
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "PIS em lote");
$breadcrumb_pages = array("Gestão de RH" => "../");
// CARREGAR REGIAO
$regiao = montaQuery("regioes", "*", array('id_regiao' => $usuario['id_regiao']));
$regiao = $regiao[1];


$pis = new PisLoteClass();

$method = (isset($_REQUEST['method'])) ? $_REQUEST['method'] : NULL;

switch ($method) {
    case 'atualizar':

        for ($i = 0; $i < count($_REQUEST['id_clt']); $i++) {
            $id_clt = $_REQUEST['id_clt'][$i];
            $novo_pis = $_REQUEST['novo_pis'][$i];
            $cpf = $_REQUEST['cpf'][$i];
            $nome = $_REQUEST['nome'][$i];
            $result = $pis->atualizaPIS($id_clt, $novo_pis);
            if (!$result) {
                $arr_erro[] = $nome;
            }
        }
        if (!empty($arr_erro)) {
            echo '<div class="alert alert-dismissable alert-danger">
                    <button type="button" class="close" data-dismiss="alert">x</button>
                    <h4>Erro ao salvar Participantes</h4>';
            foreach ($arr_erro as $value) {
                echo "<p>$value</p>";
            }
            echo '<p>Demais Participantes tiveram PIS editado com sucesso.</p></div>';
        } else {
            echo '<div class="alert alert-dismissable alert-success">
                    <button type="button" class="close" data-dismiss="alert">x</button>
                    <strong>Todos os PIS foram alterados!</strong>
                    </div>';
        }

        exit();
        break;

    case 'Visualizar':
        $file_name = date('YmdHis') . "_" . $_FILES['arquivo']['name'];
        $file_tmp_name = $_FILES['arquivo']['tmp_name'];
        $pis->moverArquivo($file_name, $file_tmp_name);
        $array_arquivo = $pis->passFileToArrayClt($file_name);
//        print_array($array_arquivo);
//        array_filter($array_arquivo);
        include 'visualizar_retorno.php';
        break;

    case 'carrega_erros':
        $dados = "id IN ({$_REQUEST['lista_erros']})";
        $erros = $pis->consultaErro($dados);
        foreach($erros as $key => $value){
            foreach ($erros[$key]  as $key2 => $value2) {
                 $erros[$key][$key2] = utf8_encode($value2);
            }
           
        }
//        print_array($erros);
        echo json_encode(array('erros'=>$erros));
        break;
    
    case 'Debug':
        $file_name = date('YmdHis') . "_" . $_FILES['arquivo']['name'];
        $file_tmp_name = $_FILES['arquivo']['tmp_name'];
        $pis->moverArquivo($file_name, $file_tmp_name);
        $array_arquivo = $pis->passFileToArray($file_name);
        array_filter($array_arquivo);
        include 'visualizando_campos_debug.php';
        exit();
        break;

    default:
        include 'inicio.php';
        exit();
        break;
}