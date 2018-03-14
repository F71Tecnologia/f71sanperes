<?php

if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../../../login.php'>Logar</a>";
    exit;
}
include("../../../conn.php");
include("../../../classes/regiao.php");
include("../../../classes/projeto.php");
include("../../../classes/funcionario.php");
include("../../../classes_permissoes/regioes.class.php");
include("../../../classes_permissoes/acoes.class.php");
include("../../../wfunction.php");
include("../../../classes/global.php");

$usuario = carregaUsuario();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
//$bradcrumb_config = array("nivel" => "../../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form-lista", "ativo" => "Gerenciar Movimentos");
//$breadcrumb_pages = array("Gestão de RH" => "/intranet/rh/principalrh.php", "Movimentos" => "rh_movimentos1.php");


$id_estagiario = $_REQUEST['id_estagiario'];
$query = "SELECT * FROM estagiario WHERE id_estagiario = '$id_estagiario'";
$result = mysql_query($query);
$row_estagiario = mysql_fetch_assoc($result);

$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : 'inicio';

switch ($method) {
// realiza os calculos e gera uma visualizacao previa para o usuario
    case 'memoria_calculo':
        $data_fim = $_REQUEST['data_fim'];
        $motivo = $_REQUEST['motivo'];

        // salario proporcional
        $arr_date = explode('/', $data_fim);
        $dias = $arr_date[0] <= 30 ? $arr_date[0] : 30;
        $saldo_salario = $row_estagiario['salario'] / 30 * $dias;

        // ferias proporcionais
        // verificar idade +45
        $data_fim_convertida = ConverteData($data_fim, 'Y-m-d');
        $data1 = new DateTime($data_fim_convertida);
        $data2 = new DateTime($row_estagiario['inicio_estagio']);
        $ddd = $data1->diff($data2);
        $meses = $ddd->m;
        $meses = $ddd->d >= 15 ? $meses + 1 : $meses;
        $saldo_recesso = $row_estagiario['salario'] / 12 * $meses;
        
        // texto motivo
        $query = "SELECT * FROM rh_rescisao_estagiario_motivo WHERE id_motivo = {$_REQUEST['id_motivo']} LIMIT 1";
        $result = mysql_query($query);
        $arr_motivo = mysql_fetch_assoc($result);
        
        include_once 'memoria_calculo.php';
        break;
    
// processa a rescisao de estagio salvando no bd
    case 'processar':
        $arr['id_user'] = (int) $usuario['id_funcionario'];
        $arr['id_estagiario'] = (int) $_REQUEST['id_estagiario'];
        $arr['nome'] = addslashes($_REQUEST['nome']);
        $arr['id_motivo'] = (int) $_REQUEST['id_motivo'];
        $arr['obs_motivo'] = addslashes($_REQUEST['obs_motivo']);
        $arr['data_fim'] = converteData($_REQUEST['data_fim'], 'Y-m-d');
        $arr['valor_bolsa'] = str_replace(',', '.', str_replace('.', '', $_REQUEST['valor_bolsa']));
        $arr['valor_recesso'] = str_replace(',', '.', str_replace('.', '', $_REQUEST['valor_recesso']));
        $arr['total_liquido'] = str_replace(',', '.', str_replace('.', '', $_REQUEST['total_liquido']));
        $arr['data_proc'] = date('Y-m-d H:i:s');
        $col = implode('`,`', array_keys($arr));
        $val = implode("','", array_values($arr));
        $insert_rescisao = "INSERT INTO rh_rescisao_estagiario (`$col`) VALUES ('$val')";
        $x = mysql_query($insert_rescisao) or die($insert_rescisao.'<br>'.mysql_error());
        $update_estagiario = "UPDATE estagiario SET status = 2 WHERE id_estagiario = {$id_estagiario}";
        $y = mysql_query($update_estagiario);
        if ($x && $y) {
            header("location: /intranet/rh/recisao/estagiario/tre.php?id_estagiario=$id_estagiario");
        } else {
            echo 'erro ao gravar.';
        }
        break;
        
// tela onde o usuario coloca informacoes sobre a rescisao
    case 'inicio':
    default:
        $query = "SELECT * FROM rh_rescisao_estagiario_motivo WHERE status = 1";
        $result = mysql_query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $motivos[$row['fator']][] = $row;
        }
        include_once 'inicio.php';
        break;
}


if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'confirmar') {

    exit();
}



