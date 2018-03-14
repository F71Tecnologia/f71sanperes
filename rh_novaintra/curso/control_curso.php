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
$id_curso = $_REQUEST['curso'];
$method = $_REQUEST['method'];
$cursoTable = "curso";
$form = (isset($_REQUEST['form'])) ? $_REQUEST['form'] : '';
$post = (isset($_REQUEST['post'])) ? $_REQUEST['post'] : '';
$form_post = (!empty($post)) ? $post : $form;

if ((empty($form_post) && empty($method)) || ($form_post == 'edicao' && empty($id_curso))) {
    $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Erro ao processar sua requisição. Se persistir contate um administrador.");
    header('Location: index.php');
    exit;
}
$data_cad = date('Y-m-d');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

if ($method == "alteraSalario") {
    /**
    * FUNÇÃO PARA FAZER UPDATE
    * @param type $id_curso
    */
    function onUpdateCltsByCurso($id_curso){
        /**
         * FAZENDO CONSULTA PARA SABER QUAIS 
         * SÃO OS CLTs QUE VÃO SOBRE O  UPDATE 
         * NA COLUNA DATA_ULTIMO_UPDATE
         * 67,68,69
         * RESCISAO INDIRETA COM AFASTAMENTO, RESCISAO INDIRETA SEM AFASTAMENTO, LICENÇA SEM VENCIMENTO 
         * (RESPECTIVAMENTE)
         */
        $queryVerificaClts = "SELECT * FROM rh_clt AS A WHERE A.id_curso = '{$id_curso}' AND ((A.status < 60 || A.status = 200) || A.status IN(67,68,69))";
        $sqlVerificaClts   = mysql_query($queryVerificaClts) or die("Erro ao selecionar clts");
        if(mysql_num_rows($sqlVerificaClts) > 0){
            while($rows = mysql_fetch_assoc($sqlVerificaClts)){
                /**
                * ATUALIZANDO A TABELA DE RH_CLT
                * COM A DATA ATUAL DA AÇÃO DE 
                * FINALIZAR A FOLHA
                */
                onUpdate($rows['id_clt']);
            }
        }
    }
    
    $salario_antigo = (!empty(trim($_REQUEST['salario_antigo']))) ? trim($_REQUEST['salario_antigo']) : '0.00';
    $salario_novo = (!empty(trim($_REQUEST['salario_new']))) ? trim($_REQUEST['salario_new']) : '0.00';
    $diferenca = $_REQUEST['difere'];
    $motivo = utf8_decode(addslashes(str_replace("\"", "'", mb_strtoupper(trim($_REQUEST['motivo'])))));
    
    if (empty($id_curso)) {
        $return = array('status'=>0);
        $return['erro'] = "ID de curso desconhecido. Contate um administrador.";
    }
    elseif (empty($salario_antigo) || empty($salario_novo) || empty($diferenca)) {
        $return = array('status'=>0);
        $return['erro'] = "Preencha todos os dados";
    }
    elseif (!preg_match('/[\d\.]+/', $salario_antigo) || !preg_match('/[\d\.\,]+/', $salario_novo) || !preg_match('/[\d\.]+/', $diferenca)) {
        $return = array('status'=>0);
        $return['erro'] = "Preencha todos os dados corretamente";
    }
    else {
        $keys = array('id_curso','data','salario_antigo','salario_novo','diferenca','user_cad','motivo','status');
        $values = array($id_curso,$data_cad,$salario_antigo,$salario_novo,$diferenca,$id_usuario,$motivo,'1');
        $salarioInsert = sqlInsert("rh_salario", $keys, $values, false);
        
        $salarioUpdate = array('salario' => $salario_novo, 'valor' => $salario_novo);
        $salarioQuery = sqlUpdate("curso", $salarioUpdate, "id_curso = '{$id_curso}' LIMIT 1", false);
        if (!empty($salarioQuery)) {
            $return = array('status'=>1);
            $return['valor'] = "R$ ".number_format($_REQUEST['salario_new'],2,",",".");
            
            onUpdateCltsByCurso($id_curso);
        }
        else {
            $return = array('status'=>0);
            $return['erro'] = "Erro ao atualizar salário no banco de dados";
        }
    }    
    echo json_encode($return);
    exit;
}

if ($form_post == 'clonagem') {
    // CARREGA FUNÇÃO A SER CLONADA
    $cursoQuery = montaQuery("curso", "*", "id_curso = {$id_curso}", null, null, '', false);
    $curso = mysql_fetch_assoc($cursoQuery);
    $newCurso = $curso;
    $newCurso['id_curso'] = 'null';
    $newCurso['nome'] = addslashes(str_replace("\"", "'", mb_strtoupper(trim($_REQUEST['nome']))));
    $newCurso['salario'] = $salario = str_replace(',', '.', str_replace(".", "", $_REQUEST['salario']));
    $newCurso['valor'] = $newCurso['salario'];
    $newCurso['data_cad'] = $data_cad;
    $newCurso['data_alter'] = 'null';
    if (!isset($curso)) {
        $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Falha ao carregar dados da função a ser duplicada.");
    }
    else {
        // CONSULTA AO BANCO PARA NÃO PERMITIR DADOS DUPLICADOS
        $result_cont = montaQuery("curso", "id_curso", "nome = '{$newCurso['nome']}' AND id_curso != '$id_curso' AND status=1", null, '1', "", false, null);
        $total_funcoes = mysql_num_rows($result_cont);
        if (empty($total_funcoes)) {
            $cursoClone_keys = array_keys($newCurso);
            $cursoClone_values = array_values($newCurso);
            $new_curso = sqlInsert($cursoTable, $cursoClone_keys, $cursoClone_values, false);
            if (empty($new_curso)) {
                $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Falha ao duplicar função <strong>{$curso['nome']}</strong>.");
            }
            else {
                $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Função duplicada com sucesso.");
            }
        }
        else {
            $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Falha ao duplicar função. Já existe uma função cadastrada com este nome.");
        }
    }
    header('Location: index.php');
    exit;
}
else if (!empty($post)) {
    $nome = addslashes(str_replace("\"", "'", mb_strtoupper(trim($_REQUEST['nome']))));
    $tipo = trim($_REQUEST['tipo']);
    $id_sindicato = (trim($_REQUEST['id_sindicato']) < 0 || empty(trim($_REQUEST['id_sindicato']))) ? 0 : trim($_REQUEST['id_sindicato']);
    $area = addslashes(str_replace("\"", "'", mb_strtoupper(trim($_REQUEST['area']))));
    $id_departamento = (trim($_REQUEST['id_departamento']) < 0 || empty(trim($_REQUEST['id_departamento']))) ? 0 : trim($_REQUEST['id_departamento']);
    if (strpos($_REQUEST['cbo'], "*") !== false) {
        list($curso['cbo_nome'],$cbo['cod']) = array_map('trim', explode("*", $_REQUEST['cbo']));
    }
    else {
        $curso['cbo_nome'] = trim($_REQUEST['cbo']);
    }
    $salario = str_replace(',', '.', str_replace(".", "", $_REQUEST['salario']));
    $salarioDia = number_format(str_replace(",", ".", $salario / 30), 2, ",", ".");
    $salarioHora = number_format(str_replace(",", ".", ($salario / 30) / 8), 2, ",", ".");
    $mes_abono = (is_numeric($_REQUEST['mes_abono']) && $_REQUEST['mes_abono'] > 0) ? $_REQUEST['mes_abono'] : 0;
    $undSalFixo = trim($_REQUEST['undSalFixo']);
    $dscSalVar = addslashes(str_replace("\"", "'", mb_strtoupper(trim($_REQUEST['dscSalVar']))));
    
    $parcelas = (!empty(trim($_REQUEST['parcelas']))) ? trim($_REQUEST['parcelas']) : 0;
    $quota = (!empty(trim($_REQUEST['quota']))) ? str_replace(',', '.', str_replace(".", "", trim($_REQUEST['quota']))) : '0.00';
    $num_quota = (!empty(trim($_REQUEST['num_quota']))) ? trim($_REQUEST['num_quota']) : 0;
    $qnt_maxima = trim($_REQUEST['qnt_maxima']);
    $hora_mes = (!empty(trim($_REQUEST['hora_mes']))) ? trim($_REQUEST['hora_mes']) : '';
    $hora_semana = (!empty(trim($_REQUEST['hora_semana']))) ? trim($_REQUEST['hora_semana']) : '';
    $valor_hora = (!empty($_REQUEST['valor_hora'])) ? str_replace('R$ ', "", str_replace(',', ".",  str_replace('.', "", $_REQUEST['valor_hora']))) : '0.00';
    $sobre_aviso = (is_numeric($_REQUEST['sobre_aviso'])) ? $_REQUEST['sobre_aviso'] : 0;
    $prontidao = (is_numeric($_REQUEST['prontidao'])) ? $_REQUEST['prontidao'] : 0;
    $gratificacao_funcao = (!empty(trim($_REQUEST['gratificacao_funcao']))) ? str_replace(',', '.', str_replace(".", "", trim($_REQUEST['gratificacao_funcao']))) : '0.00';
    $quebra_caixa = (!empty(trim($_REQUEST['quebra_caixa']))) ? str_replace(',', '.', str_replace(".", "", trim($_REQUEST['quebra_caixa']))) : '0.00';
    $periculosidade_30 = (is_numeric($_REQUEST['periculosidade_30'])) ? $_REQUEST['periculosidade_30'] : 0;
    $risco_vida = (is_numeric($_REQUEST['risco_vida'])) ? $_REQUEST['risco_vida'] : 0;
    $penosidade = ($_REQUEST['penosidade'] >= 1) ? $_REQUEST['penosidade'] : 0;
    if ($_REQUEST['tipo_insalubridade'] >= 1) {
        $tipo_insalubridade = $_REQUEST['tipo_insalubridade'];
        $qnt_salminimo_insalu = (is_numeric($_REQUEST['qnt_salminimo_insalu'])) ? $_REQUEST['qnt_salminimo_insalu'] : 0;
    }
    else {
        $tipo_insalubridade = 0;
        $qnt_salminimo_insalu = 0;
    }
    $horista_plantonista = (empty($_REQUEST['horista_plantonista'])) ? 0 : 1;
    $fracao_dsr_horista = $_REQUEST['fracao_dsr_horista'];
    $descricao = addslashes(str_replace("\"", "'", mb_strtoupper(trim($_REQUEST['descricao']))));
    $tipo_ad_cargo_confianca = $_REQUEST['tipo_ad_cargo_confianca'];
    if ($tipo_ad_cargo_confianca == 1) {
        $valor_ad_cargo_confianca = str_replace(",", ".", str_replace(".", "", $_REQUEST['valor_ad_cargo_confianca']));
        $percentual_ad_cargo_confianca = "0.00";
    }
    elseif ($tipo_ad_cargo_confianca == 2) {
        $percentual_ad_cargo_confianca = str_replace(",", ".", str_replace(".", "", $_REQUEST['percentual_ad_cargo_confianca'])) / 100;
        $valor_ad_cargo_confianca = "0.00";
    }
    else {
        $valor_ad_cargo_confianca = "0.00";
        $percentual_ad_cargo_confianca = "0.00";
    }
    
    // CHECA SE CBO EXISTE
    if (!empty($curso['cbo_nome'])) {
        $sql_cbo = FuncoesClass::getCBO($curso['cbo_nome']);
        $total_cbo = mysql_num_rows($sql_cbo);
    }
    if (empty($total_cbo)) {
        $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."CBO <strong>'{$curso['cbo_nome']}'</strong> não permitido, pois não existe em nosso banco de dados.");
        $cursoSucesso = 0;
    }
    else {
        $cbo = mysql_fetch_assoc($sql_cbo);
    }
    
    // TABELA 'curso'
    // CAMPOS E VALORES PARA UPDATE
    $cursoArray = array(
        "nome" => $nome,
        "area" => $area,
        "area_funcao" => 0,
        "id_regiao" => 1,
        "descricao" => $descricao,
        "valor" => $salario,
        "parcelas" => $parcelas,
        "campo1" => null, // Sem valor
        "campo2" => $nome, // Nome da função
        "campo3" => 1, // id_projeto
        "cbo_nome" => $cbo['nome'],
        "cbo_codigo" => $cbo['id_cbo'],
        "id_horario" => 0, // Sem valor
        "salario" => $salario,
        "undSalFixo" => $undSalFixo,
        "dscSalVar" => $dscSalVar,
        "ir" => 0, // Sem valor
        "mes_abono" => $mes_abono,
        "id_user" => $usuario['id_funcionario'],
        "data_cad" => $data_cad,
        "tipo" => $tipo,
        "hora_semana" => $hora_semana,
        "hora_mes" => $hora_mes,
        "quota" => $quota,
        "num_quota" => $num_quota,
        "data_alter" => $data_cad,
        "user_alter" => $usuario['id_funcionario'],
        "status" => 1,
        "status_reg" => 1,
        "qnt_maxima" => $qnt_maxima,
        "tipo_insalubridade" => $tipo_insalubridade,
        "qnt_salminimo_insalu" => $qnt_salminimo_insalu,
        "hora_folga" => 0, // Sem valor
        "periculosidade_30" => $periculosidade_30,
        "risco_vida" => $risco_vida,
        "penosidade" => $penosidade,
        "valor_hora" => $valor_hora,
        "id_departamento" => $id_departamento,
        "valor_base_insalubridade" => 0, // Sem valor
        "sobre_aviso" => $sobre_aviso,
        "prontidao" => $prontidao,
        "insalubridade" => $tipo_insalubridade,
        "id_sindicato" => $id_sindicato,
        "horista_plantonista" => $horista_plantonista,
        "fracao_dsr_horista" => $fracao_dsr_horista,
        "gratificacao_funcao" => $gratificacao_funcao,
        "quebra_caixa" => $quebra_caixa,
        "valor_ad_cargo_confianca" => $valor_ad_cargo_confianca,
        "percentual_ad_cargo_confianca" => $percentual_ad_cargo_confianca
    );
    // VALIDAÇÃO DOS CAMPOS
    // PULA CAMPOS NÃO-OBRIGATÓRIOS
    $buscaVazio = array_keys($cursoArray, '', true);
    // Formato: "campo_a_ser_removido" => "condição_para_remover",
    // Utilize o modelo "return <CONDIÇÃO>;" para remover campos caso a caso
    // Para obrigatoriamente remover basta setar a condição como '0'
    $cursoExclude = array(
        "tipo" => "return empty({$tipo});",
        "descricao" => 0,
        "salario" => "return {$form_post} == 'edicao';",
        "valor_hora" => 0,
        "valor" => 0,
        "parcelas" => 0,
        "quota" => 0,
        "num_quota" => 0,
        "mes_abono" => 0,
        "gratificacao_funcao" => 0,
        "quebra_caixa" => 0,
        "sobre_aviso" => "return {$tipo} == 1;",
        "prontidao" => "return {$tipo} == 1;",
        "valor_ad_cargo_confianca" => "return {$tipo_ad_cargo_confianca} != 1;",
        "percentual_ad_cargo_confianca" => "return {$tipo_ad_cargo_confianca} != 2;",
        "fracao_dsr_horista" => "return {$horista_plantonista} == 0;",
        "dscSalVar" => "return {$undSalFixo} != 6 && {$undSalFixo} != 7;"
    );
    // VERIFICA OBRIGATÓRIOS
    foreach ($cursoExclude as $remove => $condicao) {
        if (eval($condicao) OR empty($condicao)) {
            $search = array_search($remove, $buscaVazio);
            if ($search !== false) {
                unset($buscaVazio[$search]);
            }
        }
    }
    // INFORMA CAMPOS NÃO PREENCHIDOS
    foreach ($buscaVazio as $campoVazio) {
        $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."O campo \"<strong>{$campoVazio}</strong>\" é obrigatório e deve ser preenchido.");
        $cursoSucesso = 0;
    }
    // FORMATAÇÃO DOS CAMPOS
    $cursoPattern = array(
        "tipo" => '/\d+/',
        "id_sindicato" => '/\d+/',
        "id_departamento" => '/\d+/',
        "salario" => '/[\d\.\,]+/',
        "gratificacao_funcao" => '/[\d\.\,]+/',
        "quebra_caixa" => '/[\d\.\,]+/',
        "horista_plantonista" => '/\d+/',
        "undSalFixo" => '/\d+/',
        "qnt_maxima" => '/\d+/',
        "hora_semana" => '/\d+/',
        "hora_mes" => '/\d+/',
        "sobre_aviso" => '/[0-1]/',
        "prontidao" => '/[0-1]/',
        "valor_hora" => '/[\d\.\,]+/',
        "parcelas" => '/\d+/',
        "quota" => '/[\d\.\,]+/',
        "num_quota" => '/\d+/',
        "tipo_ad_cargo_confianca" => '/^[0-2]$/',
        "valor_ad_cargo_confianca" => '/[\d\.\,]+/',
        "percentual_ad_cargo_confianca" => '/[\d\.\,]+/',
        "fracao_dsr_horista" => '/\d+/'
    );
    foreach($cursoPattern as $campo => $pattern) {
        if (!preg_match($pattern, $cursoArray[$campo]) && !empty($pattern) && !empty($cursoArray[$campo])) {
            $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Erro ao gravar dados da função. O campo \"<strong>{$campo}</strong>\" deve ser preenchido corretamente.");
            $cursoSucesso = 0;
        }
    }
    if (!isset($cursoSucesso)) {
        // CAMPOS E VALORES PARA UPDATE
        $cursoUpdate = $cursoArray;
        unset($cursoUpdate['id_regiao'],$cursoUpdate['valor'],$cursoUpdate['campo3'],$cursoUpdate['salario'],$cursoUpdate['id_user'],$cursoUpdate['data_cad'],$cursoUpdate['tipo'],$cursoUpdate['status'],$cursoUpdate['status_reg']);
        // CAMPOS E VALORES PARA INSERT
        $cursoInsert = $cursoArray;
        unset($cursoInsert['data_alter'],$cursoInsert['user_alter']);
        $cursoInsert_keys = array_keys($cursoInsert);
        $cursoInsert_values = array_values($cursoInsert);
        $cursoUpdate_condicao = "id_curso = $id_curso";
        // SALVA NOVA ENTRADA
        if ($form_post == 'cadastro') {
            // CONSULTA AO BANCO PARA NÃO PERMITIR DADOS DUPLICADOS
            $result_cont = montaQuery("curso", "*", "nome = '$nome' AND campo3 = '1' AND tipo = '$tipo' AND status=1", null, null, "", false, null);
            $total_cursos = mysql_num_rows($result_cont);
            if ($total_cursos > 0) {
                $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Já existe uma função cadastrada com este nome nesse projeto.");
            }
            else {
                $id_curso = sqlInsert($cursoTable, $cursoInsert_keys, $cursoInsert_values, false);
                $cursoSucesso = (!empty($id_curso)) ? 1 : 0;
            }
        }
        // OU ATUALIZA DADOS EXISTENTES
        elseif ($form_post == 'edicao') {
            $cursoQuery = sqlUpdate($cursoTable, $cursoUpdate, $cursoUpdate_condicao, false);
            $cursoSucesso = (!empty($cursoQuery)) ? 1 : 0;
        }
    }
    if (!empty($cursoSucesso)) {
        $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Dados da função gravados com sucesso.");
        // INÍCIO LOG
        $log = new Log();
        $log->log(2, "Curso ID $id_curso ".($form_post == 'cadastro') ? "inserido" : "alterado", $cursoTable);
        // FIM LOG
        header('Location: index.php');
        exit;
    }
    else {
        $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "").'Erro ao gravar dados da função.');
    }
}

// APENAS MONTA HTML
if (!empty($form) || !empty($_SESSION['MESSAGE'])) {
    // CAMINHO DA PÁGINA
    $nome_pagina = "Gerenciamento de Função";
    //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
    $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
    $breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => $nome_pagina);
    $breadcrumb_pages = array("Gestão de Funções" => "index.php");
    
    if (!empty($id_curso)) {
        // CARREGA FUNÇÃO A SER EDITADA
        $cursoQuery = montaQuery("curso LEFT JOIN rh_cbo ON (curso.cbo_nome = rh_cbo.nome OR curso.cbo_codigo = rh_cbo.id_cbo)", "id_curso, curso.nome, area, area_funcao, id_regiao, descricao, valor, parcelas, campo1, campo2, campo3, salario, undSalFixo, dscSalVar, ir, mes_abono, id_user, data_cad, curso.tipo, hora_semana, hora_mes, quota, num_quota, data_alter, curso.status, status_reg, qnt_maxima, tipo_insalubridade, qnt_salminimo_insalu, hora_folga, periculosidade_30, risco_vida, penosidade, valor_hora, id_departamento, valor_base_insalubridade, sobre_aviso, prontidao, insalubridade, id_sindicato, horista_plantonista, fracao_dsr_horista, gratificacao_funcao, quebra_caixa, valor_ad_cargo_confianca, percentual_ad_cargo_confianca, rh_cbo.nome as cbo_nome, rh_cbo.id_cbo as id_cbo, rh_cbo.cod as cod", "id_curso = {$id_curso}", null, null, '', false);
        $curso = mysql_fetch_assoc($cursoQuery);
        if (!isset($curso)) {
            $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Falha ao carregar dados da função.");
        }
        
        // BLOQUEIA EDIÇÃO DO PRO-LABORE (DIRETOR)
        $diretor_readonly = 'readonlySelect';
        
        // VERIFICA CBO
        if (empty($curso['cbo_nome']) && empty($_REQUEST['cbo'])) {
            $_SESSION['MESSAGE'] .= (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Falha ao carregar CBO.");
        }
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
        $curso2 = recursivo($_REQUEST);
        $curso = array_replace($curso, $curso2);
        
        $curso['salario'] = str_replace(",", ".", str_replace(".", "", $curso['salario']));
        list($curso['cbo_nome'],$curso['cod']) = array_map('trim', explode("*", $curso['cbo']));
        $curso['valor_ad_cargo_confianca'] = str_replace(",", ".", str_replace(".", "",$_REQUEST['valor_ad_cargo_confianca']));
        $curso['valor_hora'] = str_replace(",", ".", str_replace(".", "",$_REQUEST['valor_hora']));
        $curso['gratificacao_funcao'] = str_replace(",", ".", str_replace(".", "",$_REQUEST['gratificacao_funcao']));
        $curso['quebra_caixa'] = str_replace(",", ".", str_replace(".", "",$_REQUEST['quebra_caixa']));
        $curso['percentual_ad_cargo_confianca'] = str_replace(",", ".", str_replace(".", "", $_REQUEST['percentual_ad_cargo_confianca'])) / 100;
        $_SESSION['MESSAGE'] .= !empty($_REQUEST) ? (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Dados carregados do cache. Corrija os erros e tente novamente.") : "";
    }
    
    // DESABILITA CAMPOS DE ACORDO COM TIPO DE CONTRATAÇÃO
    if ($curso['tipo'] == 1) {
        $tipoBlock1 = 'disabled="disabled"';
        $tipoHidden1 = 'hide';
    }
    elseif ($curso['tipo'] == 2) {
        $tipoBlock2 = 'disabled="disabled"';
        $tipoHidden2 = 'hide';
    }
    elseif ($curso['tipo'] == 3) {
        $tipoBlock3 = 'disabled="disabled"';
        $tipoHidden3 = 'hide';
    }
    
    // CARREGA SINDICATOS
    $arrSindicatos = GlobalClass::carregaSindicatos();
    $_SESSION['MESSAGE'] .= count($arrSindicatos) < 2 ? ((!empty($_SESSION['MESSAGE']) ? "<br />" : "") . "Falha ao carregar sindicados.") : "";
    
    // CARREGA JORNADAS
    $arrJornadas = GlobalClass::carregaJornadas();
    $_SESSION['MESSAGE'] .= empty($arrJornadas) ? ((!empty($_SESSION['MESSAGE']) ? "<br />" : "") . "Falha ao carregar tipos de jornada.") : "";
    
    // CARREGA TIPOS DE CONTRATO EM TEMPO PARCIAL
    $arrTmpParc = GlobalClass::carregaTempoParcial();
    $_SESSION['MESSAGE'] .= empty($arrTmpParc) ? ((!empty($_SESSION['MESSAGE']) ? "<br />" : "") . "Falha ao carregar tipos de contrato em tempo parcial.") : "";
    
    // CARREGA UNIDADES DE PAGAMENTO
    $arrPagamentos = GlobalClass::carregaPagamentos();
    $_SESSION['MESSAGE'] .= empty($arrPagamentos) ? ((!empty($_SESSION['MESSAGE']) ? "<br />" : "") . "Falha ao carregar unidades de pagamento.") : "";
    
    // CARREGA DEPARTAMENTOS
    $sql_departamento = montaQuery("setor", "*", null, 'nome', null, '', false);
    $arrDepartamentos[-1] = 'Selecione';
    while ($row_departamento = mysql_fetch_assoc($sql_departamento)) {
        $arrDepartamentos[$row_departamento['id_setor']] = $row_departamento['nome'];
    }
    $_SESSION['MESSAGE'] .= count($arrDepartamentos) <= 1 ? ((!empty($_SESSION['MESSAGE']) ? "<br />" : "")."Falha ao carregar departamentos.") : "";

    // CARREGA SALÁRIO MÍNIMO
    $valorSalMin = montaQuery("rh_movimentos", "*", "cod = 0001 AND anobase = YEAR(NOW())", null, null, 'array', false);
    $_SESSION['MESSAGE'] .= empty($valorSalMin[1]['v_fim']) ? (((!empty($_SESSION['MESSAGE'])) ? "<br />" : "")."Falha ao carregar salário mínimo.") : "";
}
?>