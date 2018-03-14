<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}
else {
    $id_usuario = $_COOKIE['logado'];
}
include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/FuncoesClass.php');
include("../../classes_permissoes/acoes.class.php");
include('../../classes/LogClass.php');

$acoes = new Acoes();
$usuario = carregaUsuario();
$master = $usuario['id_master'];
$id_regiao = $usuario['id_regiao'];
$id_horario = $_REQUEST['horario'];

$method = $_REQUEST['method'];
$rhHorariosTable = "rh_horarios";
$cursoCLTHorarioTable = "curso_clt_horario";
$form = (isset($_REQUEST['form'])) ? $_REQUEST['form'] : '';
$post = (isset($_REQUEST['post'])) ? $_REQUEST['post'] : '';
$form_post = (!empty($post)) ? $post : $form;

if ((empty($form_post) && empty($method)) || ($form_post == 'edicao' && empty($id_horario))) {
    echo "(empty($form_post) && empty($method)) || ($form_post == 'edicao' && empty($id_horario))";
    $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Erro ao processar sua requisição. Se persistir contate um administrador.");
    header('Location: index.php');
    exit;
}
$data_cad = date('Y-m-d');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

if ($form_post == 'clonagem') {
    // CARREGA HORÁRIO A SER CLONADO
    $horarioQuery = montaQuery("rh_horarios", "*", "id_horario = {$id_horario} AND status_reg = 1", 'id_horario ASC', null, '', false);
    $oldHorario = mysql_fetch_assoc($horarioQuery);
    $newHorario = $oldHorario;
    $newHorario['id_horario'] = 'null';
    $newHorario['nome'] = addslashes(str_replace("\"", "'", mb_strtoupper(trim($_REQUEST['nome']))));
    if (!isset($oldHorario)) {
        $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Falha ao carregar dados do horário a ser duplicado.");
    }
    else {
        $rhHorarioClone_keys = array_keys($newHorario);
        $rhHorarioClone_values = array_values($newHorario);
        $new_horario = sqlInsert($rhHorariosTable, $rhHorarioClone_keys, $rhHorarioClone_values, false);
        if (empty($new_horario)) {
            $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Falha ao duplicar horário. Contate um administrador.");
        }
        else {
            $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Horário duplicado com sucesso.");
            // CARREGA HORÁRIOS DIÁRIOS
            $cltHorarioQuery = montaQuery("$cursoCLTHorarioTable", "*", "id_horario = '{$oldHorario['id_horario']}'", "dia_semana ASC", null, '', false);
            while ($cltHorarioWhile = mysql_fetch_assoc($cltHorarioQuery)) {
                $cltHorarioWhile['id_horario'] = $new_horario;
                $cltHorarioWhile['data_cadastro'] = $data_cad;
                $cursoCLThorarioClone_keys = array_keys($cltHorarioWhile);
                $cursoCLThorarioClone_values = array_values($cltHorarioWhile);
                $new_cltHorario = sqlInsert($cursoCLTHorarioTable, $cursoCLThorarioClone_keys, $cursoCLThorarioClone_values, false);
                $result = mysql_query('SELECT IF(ROW_COUNT() = 1,  "1", "0") as status');
                $status = mysql_fetch_assoc($result);
                if (empty($status['status'])) {
                    $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Falha ao duplicar semana #<strong>{$cltHorarioWhile['dia_semana']}</strong> do horário.");
                }
            }
        }
    }
    header('Location: index.php');
    exit;
}
else if (!empty($post)) {
    // TABELA 'rh_horarios'
    $rhHorarioUpdate_condicao = "id_horario = ".$id_horario;
    // VALIDA SE OS HORÁRIOS ESTÃO PREENCHIDOS CORRETAMENTE E DE FORMA CRESCENTE
    if (($_REQUEST['saida_1'] == '00:00' || empty($_REQUEST['saida_1'])) && ($_REQUEST['entrada_2'] == '00:00' || empty($_REQUEST['entrada_2'])) && (strtotime($_REQUEST['entrada_1']) >= strtotime($_REQUEST['saida_2']) && !empty($_REQUEST['entrada_1']) && $_REQUEST['saida_2'] != '00:00' && !empty($_REQUEST['saida_2']))) {
        $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Erro ao gravar horário de trabalho do horário <strong>{$_REQUEST['nome']}</strong>. O horário de saída deve ser maior que o de entrada.");
        $rhHorariosSucesso = 0;
    }
    else if ((!empty($_REQUEST['entrada_1']) || (!empty($_REQUEST['saida_1']) && $_REQUEST['saida_1'] != '00:00') || (!empty($_REQUEST['entrada_2']) && $_REQUEST['entrada_2'] != '00:00') || (!empty($_REQUEST['saida_2']) && $_REQUEST['saida_2'] != '00:00')) && (empty($_REQUEST['entrada_1']) || (empty($_REQUEST['saida_1']) || $_REQUEST['saida_1'] == '00:00') || (empty($_REQUEST['entrada_2']) || $_REQUEST['entrada_2'] == '00:00') || (empty($_REQUEST['saida_2']) || $_REQUEST['saida_2'] == '00:00')) && ((!empty($_REQUEST['saida_1']) && $_REQUEST['saida_1'] != '00:00') || (!empty($_REQUEST['entrada_2']) && $_REQUEST['entrada_2'] != '00:00') || empty($_REQUEST['entrada_1']))) {
        $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Erro ao gravar horário de trabalho do horário <strong>{$_REQUEST['nome']}</strong>. Preencha todos os horários ou apenas o de <strong>Entrada</strong> e <strong>Saída</strong>.");
        $rhHorariosSucesso = 0;
    }
    else if ((!empty($_REQUEST['entrada_1']) && (!empty($_REQUEST['saida_1']) && $_REQUEST['saida_1'] != '00:00') && (!empty($_REQUEST['entrada_2']) && $_REQUEST['entrada_2'] != '00:00') && (!empty($_REQUEST['saida_2']) && $_REQUEST['saida_2'] != '00:00')) && (strtotime($_REQUEST['entrada_1']) >= strtotime($_REQUEST['saida_1']) || strtotime($_REQUEST['saida_1']) >= strtotime($_REQUEST['entrada_2']) || strtotime($_REQUEST['entrada_2']) >= strtotime($_REQUEST['saida_2']))) {
        $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Erro ao gravar horário de trabalho do horário <strong>{$_REQUEST['nome']}</strong>. Preencha todos os horários corretamente.");
        $rhHorariosSucesso = 0;
    }
    // CAMPOS E VALORES PARA UPDATE
    $rhHorarioArray = array(
        "id_regiao" => 1,
        "nome" => addslashes(str_replace("\"", "'", mb_strtoupper(trim($_REQUEST['nome'])))),
        "tpJornada" => $_REQUEST['tpJornada'],
        "dscTpJorn" => addslashes(str_replace("\"", "'", mb_strtoupper(trim($_REQUEST['dscTpJorn'])))),
        "tmpParc" => $_REQUEST['tmpParc'],
        "perHorFlexivel" => $_REQUEST['perHorFlexivel'],
        "obs" => addslashes(str_replace("\"", "'", mb_strtoupper(trim($_REQUEST['obs'])))),
        "entrada_1" => (!empty($_REQUEST['entrada_1'])) ? $_REQUEST['entrada_1'].":00" : "00:00:00",
        "saida_1" => (!empty($_REQUEST['saida_1'])) ? $_REQUEST['saida_1'].":00" : "00:00:00",
        "entrada_2" => (!empty($_REQUEST['entrada_2'])) ? $_REQUEST['entrada_2'].":00" : "00:00:00",
        "saida_2" => (!empty($_REQUEST['saida_2'])) ? $_REQUEST['saida_2'].":00" : "00:00:00",
        "horas_semanais" => $_REQUEST['horas_semanais'],
        "horas_mes" => $_REQUEST['horas_mes'],
        "dias_semana" => $_REQUEST['dias_semana'],
        "dias_mes" => $_REQUEST['dias_mes'],
        "horas_trabalho" => 0, // Sem valor
        "horas_folga" => 0, // Sem valor
        "salario" => $salario,
        "funcao" => 0,
        "valor_dia" => $salarioDia,
        "valor_hora" => $salarioHora,
        "folga" => MAX(0, $_REQUEST['folgaSabado'], $_REQUEST['folgaDomingo'], ($_REQUEST['folgaSabado']+$_REQUEST['folgaDomingo']), $_REQUEST['plantonista']),
        "status_reg" => 1,
        "adicional_noturno" => (!empty($_REQUEST['horas_noturnas'])) ? 1 : 0,
        "horas_noturnas" => (is_numeric($_REQUEST['horas_noturnas'])) ? $_REQUEST['horas_noturnas'] : 0,
        "porcentagem_adicional" => (!empty($_REQUEST['horas_noturnas']) && !empty($_REQUEST['porcentagem_adicional'])) ? $_REQUEST['porcentagem_adicional'] : '0.00',
        "plantonista" => (!empty($_REQUEST['plantonista'])) ? 1 : 0
    );
    // VALIDAÇÃO DOS CAMPOS
    // PULA CAMPOS NÃO-OBRIGATÓRIOS
    $buscaVazio = array_keys($rhHorarioArray, '', true);
    // Formato: "campo_a_ser_removido" => "condição_para_remover",
    // Utilize o modelo "return <CONDIÇÃO>;" para remover campos caso a caso
    // Para obrigatoriamente remover basta setar a condição como '0'
    $rhHorarioExclude = array(
        "obs" => 0,
        "salario" => "return {$form_post} == 'edicao';",
        "valor_dia" => "return {$form_post} == 'edicao';",
        "valor_hora" => "return {$form_post} == 'edicao';",
        "dscTpJorn" => "return {$rhHorarioArray['tpJornada']} != 9;"
    );
    foreach ($rhHorarioExclude as $remove => $condicao) {
        if (eval($condicao) OR empty($condicao)) {
            $search = array_search($remove, $buscaVazio);
            if ($search !== false) {
                unset($buscaVazio[$search]);
            }
        }
    }
    // VERIFICA OBRIGATÓRIOS
    foreach ($buscaVazio as $campoVazio) {
        $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Erro ao gravar dados do horário. O campo \"<strong>{$campoVazio}</strong>\" é obrigatório e deve ser preenchido.");
        $rhHorariosSucesso = 0;
    }
    // FORMATAÇÃO DOS CAMPOS
    $rhHorarioPattern = array(
        "tpJornada" => '/[1-9]/',
        "tmpParc" => '/[1-9]/',
        "perHorFlexivel" => '/[0-1]/i',
        "horas_semanais" => '/\d+/',
        "horas_mes" => '/\d+/',
        "dias_semana" => '/\d+/',
        "dias_mes" => '/\d+/',
        "horas_trabalho" => '/\d+/',
        "horas_folga" => '/\d+/',
        "salario" => '/[\d\.\,]+/',
        "funcao" => '/\d+/',
        "valor_dia" => '/[\d\.\,]+/',
        "valor_hora" => '/[\d\.\,]+/',
        "folga" => '/\d+/',
        "horas_noturnas" => '/\d+/',
        "porcentagem_adicional" => '/[\d\.]+/'
    );
    foreach($rhHorarioPattern as $campo => $pattern) {
        if (!preg_match($pattern, $rhHorarioArray[$campo]) && !empty($pattern) && !empty($rhHorarioArray[$campo])) {
            $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Erro ao gravar dados do horário. O campo \"<strong>{$campo}</strong>\" deve ser preenchido corretamente.");
            $rhHorariosSucesso = 0;
        }
    }
    $rhHorarioHorarios = array(
        "entrada_1",
        "saida_1",
        "entrada_2",
        "saida_2"
    );
    foreach ($rhHorarioHorarios as $campo) {
        $split = split(':', $rhHorarioArray[$campo]);
        if (($split[0] < 0 OR $split[0] > 24) OR ($split[1] < 0 OR $split[1] > 59)) {
            $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Erro ao gravar dados do horário. O campo \"<strong>{$campo}</strong>\" deve ser preenchido corretamente.");
            $rhHorariosSucesso = 0;
        }
    }
    if (!isset($rhHorariosSucesso)) {
        // CAMPOS E VALORES PARA UPDATE
        $rhHorarioUpdate = $rhHorarioArray;
        unset($rhHorarioUpdate['id_horario'],$rhHorarioUpdate['id_regiao'],$rhHorarioUpdate['salario'],$rhHorarioUpdate['funcao'],$rhHorarioUpdate['valor_dia'],$rhHorarioUpdate['valor_hora'],$rhHorarioUpdate['status_reg']);
        // CAMPOS E VALORES PARA INSERT
        $rhHorarioInsert = $rhHorarioArray;
        $rhHorarioInsert_keys = array_keys($rhHorarioInsert);
        $rhHorarioInsert_values = array_values($rhHorarioInsert);
        // CONSULTA AO BANCO PARA NÃO PERMITIR DADOS DUPLICADOS
        $result_cont = montaQuery("rh_horarios", "id_horario", "nome = '{$rhHorarioArray['nome']}' AND id_horario != '$id_horario' AND status_reg=1", null, '1', "", false, null);
        $total_horarios = mysql_num_rows($result_cont);
        if (empty($total_horarios)) {
            // SALVA NOVA ENTRADA
            if (empty($id_horario)) {
                $id_horario = sqlInsert($rhHorariosTable, $rhHorarioInsert_keys, $rhHorarioInsert_values, false);
                $_REQUEST['id_horario'] = $id_horario;
                $rhHorariosSucesso = (!empty($id_horario)) ? 1 : 0;
            }
            // ATUALIZA DADOS EXISTENTES
            elseif (!empty($id_horario)) {
                $rhHorariosQuery = sqlUpdate($rhHorariosTable, $rhHorarioUpdate, $rhHorarioUpdate_condicao, false);
                $rhHorariosSucesso = (!empty($rhHorariosQuery)) ? 1 : 0;
            }
            if (empty($rhHorariosSucesso)) {
                $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Erro ao gravar dados do horário. Contate um administrador.");
            }
        }
        else {
            $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Erro ao gravar dados do horário. Já existe um horário cadastrado com este nome.");
            $rhHorariosSucesso = 0;
        }
    }
    // TABELA 'curso_clt_horario'
    if (!empty($rhHorariosSucesso)) {
        $registroDia = 7;
        for($dias = 1; $dias <= 7; $dias++) {
            // VALIDA SE OS HORÁRIOS ESTÃO PREENCHIDOS CORRETAMENTE E DE FORMA CRESCENTE
            if (($_REQUEST['horarios_alt'][$dias][saida1] == '00:00' || empty($_REQUEST['horarios_alt'][$dias][saida1])) && ($_REQUEST['horarios_alt'][$dias][entrada2] == '00:00' || empty($_REQUEST['horarios_alt'][$dias][entrada2])) && (strtotime($_REQUEST['horarios_alt'][$dias][entrada1]) >= strtotime($_REQUEST['horarios_alt'][$dias][saida2]) && !empty($_REQUEST['horarios_alt'][$dias][entrada1]) && $_REQUEST['horarios_alt'][$dias][saida2] != '00:00' && !empty($_REQUEST['horarios_alt'][$dias][saida2]))) {
                $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Erro ao gravar horário de trabalho de \"<strong>".ucfirst(strftime('%A', strtotime('Sunday +'.$dias.' day')))."</strong>\". O horário de saída deve ser maior que o de entrada.");
                $cursoCLThorarioSucesso["$i+$dias"] = 0;
            }
            else if ((!empty($_REQUEST['horarios_alt'][$dias][entrada1]) || (!empty($_REQUEST['horarios_alt'][$dias][saida1]) && $_REQUEST['horarios_alt'][$dias][saida1] != '00:00') || (!empty($_REQUEST['horarios_alt'][$dias][entrada2]) && $_REQUEST['horarios_alt'][$dias][entrada2] != '00:00') || (!empty($_REQUEST['horarios_alt'][$dias][saida2]) && $_REQUEST['horarios_alt'][$dias][saida2] != '00:00')) && (empty($_REQUEST['horarios_alt'][$dias][entrada1]) || (empty($_REQUEST['horarios_alt'][$dias][saida1]) || $_REQUEST['horarios_alt'][$dias][saida1] == '00:00') || (empty($_REQUEST['horarios_alt'][$dias][entrada2]) || $_REQUEST['horarios_alt'][$dias][entrada2] == '00:00') || (empty($_REQUEST['horarios_alt'][$dias][saida2]) || $_REQUEST['horarios_alt'][$dias][saida2] == '00:00')) && ((!empty($_REQUEST['horarios_alt'][$dias][saida1]) && $_REQUEST['horarios_alt'][$dias][saida1] != '00:00') || (!empty($_REQUEST['horarios_alt'][$dias][entrada2]) && $_REQUEST['horarios_alt'][$dias][entrada2] != '00:00') || empty($_REQUEST['horarios_alt'][$dias][entrada1]))) {
                $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Erro ao gravar horário de trabalho de \"<strong>".ucfirst(strftime('%A', strtotime('Sunday +'.$dias.' day')))."</strong>\". Preencha todos os horários ou apenas o de <strong>Entrada</strong> e <strong>Saída</strong>.");
                $cursoCLThorarioSucesso["$i+$dias"] = 0;
            }
            else if ((!empty($_REQUEST['horarios_alt'][$dias][entrada1]) && (!empty($_REQUEST['horarios_alt'][$dias][saida1]) && $_REQUEST['horarios_alt'][$dias][saida1] != '00:00') && (!empty($_REQUEST['horarios_alt'][$dias][entrada2]) && $_REQUEST['horarios_alt'][$dias][entrada2] != '00:00') && (!empty($_REQUEST['horarios_alt'][$dias][saida2]) && $_REQUEST['horarios_alt'][$dias][saida2] != '00:00')) && (strtotime($_REQUEST['horarios_alt'][$dias][entrada1]) >= strtotime($_REQUEST['horarios_alt'][$dias][saida1]) || strtotime($_REQUEST['horarios_alt'][$dias][saida1]) >= strtotime($_REQUEST['horarios_alt'][$dias][entrada2]) || strtotime($_REQUEST['horarios_alt'][$dias][entrada2]) >= strtotime($_REQUEST['horarios_alt'][$dias][saida2]))) {
                $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Erro ao gravar horário de trabalho de \"<strong>".ucfirst(strftime('%A', strtotime('Sunday +'.$dias.' day')))."</strong>\". Preencha todos os horários corretamente.");
                $cursoCLThorarioSucesso["$i+$dias"] = 0;
            }
            // CAMPOS E VALORES PARA UPDATE
            $cursoCLThorarioArray[$dias] = array(
                "id_horario" => $id_horario,
                "dia_semana" => $dias,
                "variavel" => (!empty($_REQUEST['horarios_alt'][$dias][variavel])) ? 1 : 0,
                "tpInterv" => ($_REQUEST['horarios_alt'][$dias][tpInterv] == 2) ? 2 : 1,
                "entrada1" => (!empty($_REQUEST['horarios_alt'][$dias][entrada1])) ? "2017-05-".($registroDia+$dias)." ".$_REQUEST['horarios_alt'][$dias][entrada1].":00" : "2017-05-".($registroDia+$dias)." 00:00:00",
                "saida1" => (!empty($_REQUEST['horarios_alt'][$dias][saida1])) ? "2017-05-".($registroDia+$dias)." ".$_REQUEST['horarios_alt'][$dias][saida1].":00" : "2017-05-".($registroDia+$dias)." 00:00:00",
                "entrada2" => (!empty($_REQUEST['horarios_alt'][$dias][entrada2])) ? "2017-05-".($registroDia+$dias)." ".$_REQUEST['horarios_alt'][$dias][entrada2].":00" : "2017-05-".($registroDia+$dias)." 00:00:00",
                "saida2" => (!empty($_REQUEST['horarios_alt'][$dias][saida2])) ? "2017-05-".($registroDia+$dias)." ".$_REQUEST['horarios_alt'][$dias][saida2].":00" : "2017-05-".($registroDia+$dias)." 00:00:00",
                "folga" => (!empty($_REQUEST['horarios_alt'][$dias][folga])) ? 1 : 0,
                "data_cadastro" => $data_cad
            );
            $cursoCLThorarioHorarios = array(
                "entrada1",
                "saida1",
                "entrada2",
                "saida2"
            );
            foreach ($cursoCLThorarioHorarios as $campo) {
                $split = split(':', split(' ', $cursoCLThorarioArray[$dias][$campo])[1]);
                if (($split[0] < 0 OR $split[0] > 24) OR ($split[1] < 0 OR $split[1] > 59)) {
                    $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Erro ao gravar horário de trabalho de \"<strong>".ucfirst(strftime('%A', strtotime('Sunday +'.$dias.' day')))."</strong>\". O campo \"<strong>{$campo}</strong>\" não foi preenchido corretamente.");
                    $cursoCLThorarioSucesso["$i+$dias"] = 0;
                }
            }
            // INSERE DADOS NO BANCO
            if (!isset($cursoCLThorarioSucesso["$i+$dias"]) && !empty($id_horario) && !empty($dias)) {
                // CAMPOS E VALORES PARA UPDATE
                $cursoCLThorarioUpdate[$dias] = $cursoCLThorarioArray[$dias];
                unset($cursoCLThorarioUpdate[$dias]['id_horario'],$cursoCLThorarioUpdate[$dias]['dia_semana'],$cursoCLThorarioUpdate[$dias]['data_cadastro']);
                foreach ($cursoCLThorarioUpdate[$dias] as $k => $val) {
                    $cursoCLThorarioUpdateSet[$dias] .= " {$k}='{$val}',";
                }
                $cursoCLThorarioUpdateSet[$dias] = substr($cursoCLThorarioUpdateSet[$dias], 0, -1);
                // CAMPOS E VALORES PARA INSERT
                $cursoCLThorarioInsert[$dias] = $cursoCLThorarioArray[$dias];
                foreach ($cursoCLThorarioInsert[$dias] as $k => $val) {
                    $cursoCLThorarioInsertSet[$dias] .= " {$k}='{$val}',";
                }
                $cursoCLThorarioInsertSet[$dias] = substr($cursoCLThorarioInsertSet[$dias], 0, -1);
                $cursoCLThorarioQuery = "INSERT INTO $cursoCLTHorarioTable SET {$cursoCLThorarioInsertSet[$dias]} ON DUPLICATE KEY UPDATE {$cursoCLThorarioUpdateSet[$dias]}";
                mysql_query("$cursoCLThorarioQuery") or die("Erro na query:<br/>" . $cursoCLThorarioQuery . "<br/><br/>Descrição:<br/>" . mysql_error());
                $cursoCLThorarioSucesso["$i+$dias"] = 1;
            }
            else {
                $cursoCLThorarioSucesso["$i+$dias"] = 0;
            }
        }
    }
    if (isset($rhHorariosSucesso) && !in_array(0, $rhHorariosSucesso) && isset($cursoCLThorarioSucesso) && !in_array(0, $cursoCLThorarioSucesso)) {
    // INÍCIO LOG
    $log = new Log();
    $log->log(2, "Horário ID $id_horario ".($form_post == 'cadastro') ? "inserido" : "alterado", $rhHorariosTable);
    // FIM LOG
    header('Location: index.php');
    exit;
    }
}

// APENAS MONTA HTML
if (!empty($form) || !empty($_SESSION['MESSAGE'])) {
    // CAMINHO DA PÁGINA
    $nome_pagina = "Gerenciamento de Horário";
    //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
    $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
    $breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => $nome_pagina);
    $breadcrumb_pages = array("Gestão de Horários" => "index.php");
    
    if (!empty($id_horario)) {
        // CARREGA HORÁRIO A SER EDITADO
        $horarioQuery = montaQuery("rh_horarios", "id_horario, nome, tpJornada, dscTpJorn, tmpParc, perHorFlexivel, obs, entrada_1, saida_1, entrada_2, saida_2, horas_semanais, horas_mes, dias_semana, dias_mes, folga, adicional_noturno, horas_noturnas, porcentagem_adicional", "id_horario = {$id_horario} AND status_reg = 1", 'id_horario ASC', null, '', false);
        $horario = mysql_fetch_assoc($horarioQuery);
        $cltHorarioQuery = montaQuery("$cursoCLTHorarioTable", "entrada1, saida1, entrada2, saida2, folga, dia_semana, variavel, tpInterv", "id_horario = '{$id_horario}'", "dia_semana ASC", null, '', false);
        while ($cltHorarioWhile = mysql_fetch_assoc($cltHorarioQuery)) {
            foreach ($cltHorarioWhile as $key => $value) {
                $horario['horarios_alt'][$cltHorarioWhile['dia_semana']][$key] = $value;
            }
        }
        $_SESSION['MESSAGE'] .= empty($horarioQuery) ? (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Falha ao carregar dados do horário.") : "";
    }
        
    // CARREGA DADOS DO CACHE
    if (!empty($post)) {
        function recursivo(array $array) {
            $result = array();
            foreach($array as $key => $value) {
                if(is_array($item)) {
                    $result[$key] = recursivo($value);
                }
                else {
                    $result[$key] = $value;
                }
            }
            return $result;
        }
        $horario2 = recursivo($_REQUEST);
        $horario = array_replace($horario, $horario2);
        $_SESSION['MESSAGE'] .= !empty($_REQUEST) ? (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Dados carregados do cache. Corrija os erros e tente novamente.") : "";
    }
    
    // CARREGA JORNADAS
    $arrJornadas = GlobalClass::carregaJornadas();
    $_SESSION['MESSAGE'] .= empty($arrJornadas) ? ((!empty($_SESSION['MESSAGE']) ? "<br />" : "") . "Falha ao carregar tipos de jornada.") : "";
    
    // CARREGA TIPOS DE CONTRATO EM TEMPO PARCIAL
    $arrTmpParc = GlobalClass::carregaTempoParcial();
    $_SESSION['MESSAGE'] .= empty($arrTmpParc) ? ((!empty($_SESSION['MESSAGE']) ? "<br />" : "") . "Falha ao carregar tipos de contrato em tempo parcial.") : "";
}
?>