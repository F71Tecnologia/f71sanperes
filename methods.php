<?php
include("conn.php");
include("wfunction.php");
include("classes/global.php");
include("classes/FolhaClass.php");
include("classes/EventoClass.php");
include("classes/RelatorioClass.php");
include("classes/UnidadeClass.php");
include("classes/MovimentoClass.php");
include("classes/calculos.php");

$evento     = new Eventos();
$movimentos = new Movimentos();
$relatorio  = new Relatorio();
$folha      = new Folha();
$calc_folha = new Calculo_Folha();
$calc       = new calculos();

$defaults = array(
    "1" => array("-1" => "« Selecione »"),
    "2" => array("0" => "« Todos »")
);

$arrFaltas  = array(232, 293);
$arrAtrasos = array(236);

if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaProjetoById") {
    
    $id_projeto = $_REQUEST['id_projeto'];
    $ps = GlobalClass::getProjetobyId($_REQUEST['id_projeto']);
    
    foreach ($ps as $k => $val) {
        $projetos .= "<option value=\"{$k}\">" . utf8_encode($val) . "</option>";
    }
    
    echo $projetos;
    exit();
}

//METODO PARA RETORNAR UM SJON COM AS REGIÕES DE UM DETERMINADO PROJETO
//RECEBE APENAS COMO PARAMETRO O MASTER
//ORDENA PELO NOME
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaRegioes") {
    $opt = (!empty($_REQUEST['default'])) ? $_REQUEST['default'] : 1;
    $rs = GlobalClass::carregaRegioes($_REQUEST['master'], $defaults[$opt]);
    foreach ($rs as $k => $val) {
        $regioes .= "<option value=\"{$k}\">" . utf8_encode($val) . "</option>";
    }
    echo $regioes;
    exit; 
}

if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaClts") {
    $rs = GlobalClass::getCltsByRegiao($_REQUEST['projeto']);
    foreach ($rs as $k => $val) {
        $regioes .= "<option value=\"{$k}\">" . utf8_encode($val) . "</option>";
    }
    echo $regioes;
    exit;
}

//METODO PARA RETORNAR UM SJON COM OS PROJETOS DE UMA DETERMINADA REGIAO
//RECEBE APENAS COMO PARAMETRO A REGIAO
//ORDENA PELO NOME
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaProjetos") {
    $opt = (!empty($_REQUEST['default'])) ? $_REQUEST['default'] : 1;
    $request = $_REQUEST['request'];
    $select = (!empty($request)) ? $_REQUEST[$request] : $_REQUEST['regiao'];    
    $rs = GlobalClass::carregaProjetosByRegiao($select, $defaults[$opt]);
        $opt = (!empty($_REQUEST['default'])) ? $_REQUEST['default'] : 1;

    
    $projeto = "";
    foreach ($rs as $k => $val) {
        $SELECTED = ($_REQUEST['default'] == $k) ? 'SELECTED' : '';
        $projeto .= "<option value=\"{$k}\" {$SELECTED} >" . utf8_encode($val) . "</option>";
    }
    echo $projeto;
    exit;
}

//METODO PARA RETORNAR UM SJON COM OS SINDICATOS DE UMA DETERMINADA REGIAO
//RECEBE APENAS COMO PARAMETRO A REGIAO
//ORDENA PELO NOME
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaSindicatos") {
    $opt = (!empty($_REQUEST['default'])) ? $_REQUEST['default'] : 1;
    $request = $_REQUEST['request'];
    $select = (!empty($request)) ? $_REQUEST[$request] : $_REQUEST['regiao'];    
    $rs = GlobalClass::carregaSindicatosByRegiao($select, $defaults[$opt]);
    $sindicato = "";
    foreach ($rs as $k => $val) {
        $sindicato .= "<option value=\"{$k}\">" . utf8_encode($val) . "</option>";
    }
    echo $sindicato;
    exit;
}

//METODO PARA RETORNAR UM SJON COM OS PRESTADORES DE UM DETERMINADO PROJETO
//RECEBE APENAS COMO PARAMETRO O PROJETO
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaPrestadores") {
    $opt = (!empty($_REQUEST['default'])) ? $_REQUEST['default'] : 1;
    $request = $_REQUEST['request'];    
    $projeto = (!empty($request)) ? $_REQUEST[$request] : $_REQUEST['projeto'];
    $rs = GlobalClass::carregaPrestadorByProjeto($projeto, $defaults[$opt], $_REQUEST['nomeTipo']);
    $prestador = "";
    foreach ($rs as $k => $val) {
        $prestador .= "<option value=\"{$k}\">" . utf8_encode($val) . "</option>";
    }
    echo $prestador;
    exit;
}
//METODO PARA RETORNAR UM SJON COM OS FORNECEDORES 
//CADASTRO DOS PRODUTOS E MEDICAMENTOS FARMACEUTICOS
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaFornecedor") {
    $opt = (!empty($_REQUEST['default'])) ? $_REQUEST['default'] : 1;
    $request = $_REQUEST['request'];    
//    $projeto = (!empty($request)) ? $_REQUEST[$request] : $_REQUEST['projeto'];
    $rs = GlobalClass::carregaFornecedorByProjeto($defaults[$opt]);
    $fornecedor = "";
    foreach ($rs as $k => $val) {
        $fornecedor .= "<option value=\"{$k}\">" . utf8_encode($val) . "</option>";
    }
    echo $fornecedor;
    exit;
}

if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaPrestadoresInativos") {
    $opt = (!empty($_REQUEST['default'])) ? $_REQUEST['default'] : 1;
    $request = $_REQUEST['request'];
    $projeto = (!empty($request)) ? $_REQUEST[$request] : $_REQUEST['projeto'];
    $rs = GlobalClass::carregaPrestadorInativoByProjeto($projeto, $defaults[$opt], $_REQUEST['nomeTipo']);
    $prestador = "";
    foreach ($rs as $k => $val) {
        $prestador .= "<option value=\"{$k}\">" . utf8_encode($val) . "</option>";
    }
    echo $prestador;
    exit;
}

if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaPrestadoresOutros") {
    $opt = (!empty($_REQUEST['default'])) ? $_REQUEST['default'] : 1;
    $request = $_REQUEST['request'];
    $projeto = (!empty($request)) ? $_REQUEST[$request] : $_REQUEST['projeto'];
    $rs = GlobalClass::carregaPrestadorOutrosByProjeto($projeto, $defaults[$opt], $_REQUEST['nomeTipo']);
    $prestador = "";
    foreach ($rs as $k => $val) {
        $prestador .= "<option value=\"{$k}\">" . utf8_encode($val) . "</option>";
    }
    echo $prestador;
    exit;
}

//METODO PARA RETORNAR UM SJON COM OS FORNECEDORES DE UMA DETERMINADA REGIÃO
//RECEBE APENAS COMO PARAMETRO A REGIÃO
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaFornecedores") {
    $opt = (!empty($_REQUEST['default'])) ? $_REQUEST['default'] : 1;
    $request = $_REQUEST['request'];
    $select = (!empty($request)) ? $_REQUEST[$request] : $_REQUEST['regiao'];
    $rs = GlobalClass::carregaFornecedorByRegiao($select, $defaults[$opt]);
    $fornecedor = "";
    foreach ($rs as $k => $val) {
        $fornecedor .= "<option value=\"{$k}\">" . utf8_encode($val) . "</option>";
    }
    echo $fornecedor;
    exit;
}

//METODO PARA RETORNAR UM SJON COM OS NOMES(CONTABILIDADE) DE UM DETERMINADO TIPO DE ENTRADA/SAIDA
//RECEBE APENAS COMO PARAMETRO O ID DO TIPO
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaNomes") {
    $opt = (!empty($_REQUEST['default'])) ? $_REQUEST['default'] : 1;
    $request = $_REQUEST['request'];
    $select = (!empty($request)) ? $_REQUEST[$request] : $_REQUEST['tipo'];
    $rs = GlobalClass::carregaNomesByTipo($select, $defaults[$opt]);
    $nome = "";
    foreach ($rs as $k => $val) {
        $nome .= "<option value=\"{$k}\">" . utf8_encode($val) . "</option>";
    }
    echo $nome;
    exit;
}

//METODO PARA RETORNAR UM SJON COM OS PARCEIROS DE UMA DETERMINADA REGIAO
//RECEBE APENAS COMO PARAMETRO A REGIAO
//ORDENA PELO NOME
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaParceiros") {
    $opt = (!empty($_REQUEST['default'])) ? $_REQUEST['default'] : 1;
    $rs = GlobalClass::carregaParceirosByRegiao($_REQUEST['regiao'], $defaults[$opt]);
    foreach ($rs as $k => $val) {
        $parceiro .= "<option value=\"{$k}\">" . utf8_encode($val) . "</option>";
    }

    echo $parceiro;
    exit;
}

/*
 * METODO PARA RETORNAR UM JSON COM AS UNIDADES DE UM DETERMINADO PROJETO
 * RECEBE COMO PARAMETRO O PROJETO E A REGIAO
 */
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaUnidades") {
    $opt = (!empty($_REQUEST['default'])) ? $_REQUEST['default'] : 1;
    $rs = getUnidade($_REQUEST['regiao'], $_REQUEST['projeto']);
    while ($row1 = mysql_fetch_assoc($rs)) {
        $selected = ($row1['id_unidade'] == $_REQUEST['unidade']) ? ' SELECTED ' : null;
        $unidade .= "<option value=\"{$row1['id_unidade']}\" $selected >" . utf8_encode($row1['id_unidade'] . " - " . $row1['unidade']) . "</option>";
    }
    echo $unidade;
    exit;
}

//METODO PARA RETORNAR UM SJON COM OS BANCOS DE UMA DETERMINADO PROJETO
//RECEBE APENAS COMO PARAMETRO O PROJETO                                                 
//ORDENA PELO NOME
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaBancos") {
    $rs = mysql_query("SELECT id_banco,nome FROM bancos WHERE id_projeto={$_REQUEST['projeto']} AND status_reg = 1 ORDER BY nome");
    $opt = (!empty($_REQUEST['default'])) ? $_REQUEST['default'] : 1;
    $key = current(array_keys($defaults[$opt]));
    $val = current(array_values($defaults[$opt]));
    $projeto = "<option value=\"$key\">" . utf8_encode($val) . "</option>";
    $selected = (mysql_num_rows($rs) == 1) ? ' SELECTED ' : null;
    while ($row = mysql_fetch_assoc($rs)) {
        $projeto .= "<option value=\"{$row['id_banco']}\" {$selected} >{$row['id_banco']} - " . utf8_encode($row['nome']) . "</option>";
    }
    echo $projeto;
    exit;
}

//MÉTODO PARA RETORNAR UM JSON COM OS CARGOS DE UM DETERMINADO PROJETO
//RECEBE COMO PARAMETRO O PROJETO
//ORDENA PELO NOME
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaCargos") {

    if(isset($_REQUEST['tipo'])){
        $auxTipo = "AND tipo = {$_REQUEST['tipo']}";
    }
    
    $projSelect = $_REQUEST['proj'];
    $pro = "";
    foreach ($projSelect as $key => $value) {
        $pro .= $value . ",";
    }
    $pro = substr($pro, 0, -1);

    $rs = mysql_query("SELECT * FROM curso WHERE campo3 IN({$pro}) AND status = '1' AND status_reg = '1' $auxTipo ORDER BY nome");
    $cargo = utf8_encode("<option value=\"-1\">« Todos »</option>");
    while ($row = mysql_fetch_assoc($rs)) {
        $cargo .= "<option value=\"{$row['id_curso']}\">{$row['id_curso']} - " . utf8_encode($row['nome']) . "</option>";
    }

    echo $cargo;
    exit;
}

//METODO PARA RETORNAR UM SJON COM AS FUNÇÕES DE UMA DETERMINADO PROJETO
//RECEBE APENAS COMO PARAMETRO O PROJETO
//ORDENA PELO NOME
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaFuncoes") {
    $rs = mysql_query("SELECT id_curso,nome FROM curso WHERE campo3 = {$_REQUEST['projeto']} ORDER BY nome;");
    $opt = (!empty($_REQUEST['default'])) ? $_REQUEST['default'] : 1;

    $key = current(array_keys($defaults[$opt]));
    $val = current(array_values($defaults[$opt]));
    $projeto = "<option value=\"$key\">" . utf8_encode($val) . "</option>";
    while ($row = mysql_fetch_assoc($rs)) {
        $projeto .= "<option value=\"{$row['id_curso']}\">{$row['id_curso']} - " . utf8_encode($row['nome']) . "</option>";
    }
    echo $projeto;
    exit;
}

//METODO PARA MUDAR UMA PESSOA DE REGIAO, NOJÃO MAS NÃO TENHO TEMPO DE MELHORAR :(
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "trocaRegiao") {
    $usuario = carregaUsuario();
    $user = $usuario['id_funcionario'];
    $regiao = $_REQUEST['regiao'];
    $regiao_de = $_REQUEST['regiao_de'];
    //GRAVANDO SESSÃO
    $_SESSION['id_regiao'] = $regiao;
    //GRAVANDO SESSÃO
    $result_regiao_1 = mysql_query("SELECT regiao FROM regioes where id_regiao = '$regiao_de'");
    $row_regiao_1 = mysql_fetch_array($result_regiao_1);
    $result_regiao_2 = mysql_query("SELECT regiao FROM regioes where id_regiao = '$regiao'");
    $row_regiao_2 = mysql_fetch_array($result_regiao_2);
    mysql_query("UPDATE funcionario set id_regiao = '$regiao' where id_funcionario = '$user'");

    //----- INI -- GRAVANDO AS INFORMAÇÕES DO LOGIN NA TABELA LOG
    $result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$user'");
    $row_user = mysql_fetch_array($result_user);
    $ip = $_SERVER['REMOTE_ADDR'];  //PEGANDO O IP
    $local = "TROCA DE REGIÃO";
    $horario = date('Y-m-d H:i:s');
    $acao = "SAIU DE: $row_regiao_1[0] PARA: $row_regiao_2[0]";
    mysql_query("INSERT INTO log (id_user,id_regiao,tipo_user,grupo_user,local,horario,ip,acao)
                        VALUES ('$user','$regiao_de','$row_user[tipo_usuario]',
                        '$row_user[grupo_usuario]','$local','$horario','$ip','$acao')") or die("Erro Inesperado<br><br>" . mysql_error());
    //----- FIM -- GRAVANDO AS INFORMAÇÕES DO LOGIN NA TABELA LOG

    echo json_encode(array("status" => "1"));
    exit;
}

//METODO PARA MUDAR UMA PESSOA DE REGIAO, NOJÃO MAS NÃO TENHO TEMPO DE MELHORAR :(
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "trocaMaster") {
    $usuario = carregaUsuario();
    $user = $usuario['id_funcionario'];
    $master = $_REQUEST['master'];
    $master_de = $_REQUEST['master_de'];

    $result_master_1 = mysql_query("SELECT * FROM master where id_master = '$master_de'");
    $row_master_1 = mysql_fetch_array($result_master_1);
    $result_master_2 = mysql_query("SELECT * FROM master where id_master = '$master'");
    $row_master_2 = mysql_fetch_array($result_master_2);
    $result_regiao_master = mysql_query("SELECT regioes.id_regiao  FROM regioes
                                            INNER JOIN  funcionario_regiao_assoc
                                            ON funcionario_regiao_assoc.id_regiao = regioes.id_regiao
                                            WHERE regioes.id_master = '$master'
                                            AND funcionario_regiao_assoc.id_funcionario = '$user'
                                            ORDER BY regioes.regiao LIMIT 1 ");
    $row_regiao_master = mysql_fetch_array($result_regiao_master);
    mysql_query("UPDATE funcionario set id_master = '$master' , id_regiao = '$row_regiao_master[id_regiao]' where id_funcionario = '$user'");

    $_SESSION['id_regiao'] = $row_regiao_master[id_regiao];

    //----- INI -- GRAVANDO AS INFORMAÇÕES DO LOGIN NA TABELA LOG
    $result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$user'");
    $row_user = mysql_fetch_array($result_user);
    $ip = $_SERVER['REMOTE_ADDR'];  //PEGANDO O IP
    $local = "TROCA DE MASTER";
    $horario = date('Y-m-d H:i:s');
    $acao = "SAIU DE: $row_master_1[nome] PARA: $row_master_2[nome]";
    mysql_query("INSERT INTO log (id_user,id_regiao,tipo_user,grupo_user,local,horario,ip,acao)
      VALUES ('$user','$master_de','$row_user[tipo_usuario]',
        '$row_user[grupo_usuario]','$local','$horario','$ip','$acao')") or die("Erro Inesperado<br><br>" . mysql_error());
    //----- FIM -- GRAVANDO AS INFORMAÇÕES DO LOGIN NA TABELA LOG

    echo json_encode(array("status" => "1"));
    exit;
}

//MÉTODO PARA GRAVAR LOG DE RETIRADA DE CLT DA FOLHA DE PAGAMENTO
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "logRemoveDaFolha") {

    $folha = new Folha();
    if ($_REQUEST['tipo'] == 1) {
        //MÉTODO PARA CRIAR LOG DE EXCLUSÃO
        $folha->logExcluirDaFolha($_REQUEST['clt'], $_REQUEST['folha'], $_REQUEST['usuario']);
        $removeCltFolha = mysql_query("UPDATE rh_folha_proc SET status = 0 WHERE id_clt = '{$_REQUEST['clt']}' AND id_folha = '{$_REQUEST['folha']}'");
        
    } else {
        //REMOVER DA TABELA LOG
        $folha->logDesfazerExcluirFolha($_REQUEST['clt'], $_REQUEST['folha'], $_REQUEST['usuario']);
        $removeCltFolha = mysql_query("UPDATE rh_folha_proc SET status = 2 WHERE id_clt = '{$_REQUEST['clt']}' AND id_folha = '{$_REQUEST['folha']}'");
        
    }
}

//MÉTODO PARA PRORROGAR EVENTOS
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "prorroga_evento") {

    //VARIÁVEIS 
    $retorno = array("status" => false);
    $id_evento = $_REQUEST['id_evento'];
    $data_retorno = date("Y-m-d", strtotime(str_replace("/", "-", $_REQUEST['data_retorno'])));
    $data_prorrogada = date("Y-m-d", strtotime(str_replace("/", "-", $_REQUEST['data_prorrogada'])));
    $id_user = $_REQUEST['id_user'];
    $prorrogado_em = date("Y-m-d H:i:s");
    $motivo = $_REQUEST['mensagem'];
    //RETORNA A DATA DE RETORNO
    $data_retorno = $evento->getDataRetorno($id_evento);

    if (!empty($data_prorrogada) && $data_prorrogada > $data_retorno['data_retorno']) {
        if (!empty($motivo)) {

            //ATUALIZA A DATA PARA UMA NOVA DATA
            $retorno = $evento->prorrogaEvento($id_evento, $data_retorno, $data_prorrogada, $id_user, $prorrogado_em, $motivo);
        } else {
            $texto[] = utf8_encode("Mensagem obrigatória");
            $retorno = array("status" => false, "erro" => $texto);
        }
    } else {
        $texto[] = utf8_encode("A data é vazia ou menor que a data atual de retorno");
        $retorno = array("status" => false, "erro" => $texto);
    }

    echo json_encode($retorno);
    exit();
}

//MÉTODO PARA VOLTAR PARA ATIVIDADE NORMAL
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "cadEvento") {

    $retorno = array("status" => 0);

    //RECUPERA DADOS DO EVENTO CLICADO PELO ID
    $evento_selecionado = $evento->getEventoById($_REQUEST['id_evento']);
    $evento_selecionado["data"] = date("Y-m-d");
    $evento_selecionado["data_retorno"] = "0000-00-00";
    $evento_selecionado["dias"] = 0;


    //CADASTRANDO EVENTOS
    $retorno = $evento->cadEvento(10, $evento_selecionado);

    if ($retorno) {
        $retorno = array("status" => 1);
    }

    echo json_encode($retorno);
    exit();
}

// REMOVER FUNCIONÁRIO DA FOLHA DE PAGAMENTO
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "removeCltFolha") {
    $folha = new Folha();
    
    $folha->desativaParticipanteFolhaProc($_REQUEST['folha'], $_REQUEST['clt'], $_REQUEST['diretor']);
}

// REMOVER FUNCIONÁRIO DA FOLHA DE PAGAMENTO
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "adicionaCltFolha") {
    $folha = new Folha();
    
    $folha->ativaParticipanteFolhaProc($_REQUEST['folha'], $_REQUEST['clt'], $_REQUEST['diretor']);
}

// MÉTODO PARA CALCULCAR A DATA PRORROGADA DE UM EVENTO A PARTIR DA QTD DE DIAS
if (isset($_POST['calcData'])) {
    $data = $evento->calculaNovoRetorno($_POST['id'], $_POST['qtdDias']);
    echo json_encode(array('data' => $data));
    exit();
}

// MÉTODO PARA GRAVAR LOG DE RELATÓRIOS
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'logRelatorio') {
    $retorno = $relatorio->criaLog($_REQUEST['url'], $_REQUEST['id_relatorio'], $_REQUEST['id_usuario']);
    echo json_encode(array('status' => $retorno));
    exit();
}

// MÉTODO PARA RETORNAR UM JSON COM OS MUNICÍPIOS
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'carregaMunicipio') {
    $query = "select id_municipio, cod_2, municipio from municipios where sigla = '{$_REQUEST['uf']}'";
    $result = mysql_query($query);
    // Verificação para página de rh ferias
    if(isset($_REQUEST['feriado']) && $_REQUEST['feriado'] == "feriado"){
        header('Content-Type: text/html; charset=ISO-8859-1');
        while ($row = mysql_fetch_array($result)) {
            $array.= "<option value='{$row["cod_2"]}'>".$row['municipio'];
        }
        echo $array;
    }else{
        while ($row = mysql_fetch_array($result)) {
            $array['municipio'][] = utf8_encode($row['municipio']);
        }
        echo json_encode($array);
    }

    exit();
}

// MÉTODO PARA RETORNAR UM JSON COM OS MUNICÍPIOS
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'carregaCodMunicipio') {
    $muni = utf8_decode($_REQUEST['muni']);
    $query = "select id_municipio from municipios where municipio = '{$muni}'";
    //echo $query;
    $result = mysql_query($query);
    $row = mysql_fetch_array($result);
    echo json_encode($row);
    exit();
}

// MÉTODO PARA RETORNAR UM JSON COM OS PAÍSES
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'carregaPais') {
    $query = "SELECT pais FROM paises;";
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result)) {
        $array['pais'][] = utf8_encode($row['pais']);
    }
    echo json_encode($array);
    exit();
}

// MÉTODO PARA RETORNAR UM JSON COM OS CODIGO DO PAÍS
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'carregaCodPais') {
    $pais = utf8_decode($_REQUEST['pais']);
    $query = "SELECT id_pais FROM paises WHERE pais = '$pais';";
    $result = mysql_query($query);
    $row = mysql_fetch_assoc($result);
    echo json_encode($row);
    exit();
}

// MÉTODO PARA RETORNAR O ÚLTIMO EVENTO
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'verificaRetorno') {
    $retorno = $evento->lastEvento($_REQUEST['id_clt']);
    foreach($retorno['dados'] as $key => $value){
        $retorno['dados'][$key] = utf8_encode($value);
    }
    echo json_encode($retorno);
    exit();
}

// MÉTODO PARA CALCULAR A QUANTIDADE DE DIAS A PARTIR DA DATA DE INICIO E FIM
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'calcDias') {
    $data = converteData($_REQUEST['data']);
    $data_retorno = converteData($_REQUEST['data_retorno']);
    $dias = $evento->nova_qnt_dias($data, $data_retorno);
    echo json_encode(array('dias' => $dias));
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == "novo_retorno") {
    $data = converteData($_REQUEST['data']);
    $data_retorno = $evento->nova_data_retorno($data, $_REQUEST['dias']);
    echo json_encode(array('data_retorno' => $data_retorno));
    exit();
}

// METODO PARA PEGAR A FLAG PERICIA NA TABELA RHSTATUS
if(isset($_REQUEST['method']) && $_REQUEST['method'] == "getPericia") {
    $retrun = $evento->getPericia($_POST['evento']);
    echo json_encode($retrun);
}

//método para listar os funcionários ativos da tabela rh_clt.
if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'carregaClt'){
    $query = "SELECT id_clt, nome, cpf FROM rh_clt WHERE id_projeto = {$_REQUEST['projeto']} AND (status < 60) ORDER BY nome";
    $result =mysql_query($query);
    while ($row = mysql_fetch_assoc($result)) {
       $clts .= "<option value=\"{$row['id_clt']}\">" . utf8_encode($row['cpf']." - ". $row['nome']) . "</option>";
    }
    echo $clts;
    exit();
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'addDias'){
    
    $data = $_REQUEST['data'];
    $dias = $_REQUEST['dias'];
    
    $data = implode('-', array_reverse(explode('/', $data)));
    $data = date('d/m/Y', strtotime($dias.' day', strtotime($data)));
    echo $data;
}

// Usado na tela de lançamento de movimentos para listar as datas/horas dos atrasos e faltas.
if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'getDatasHorasFaltasAtrasos'){
    
    $id_movimento = $_REQUEST['id_movimento'];
    
    $data_horas = Movimentos::getDatasHorasFaltasAtrasos($id_movimento);
    
    $data = '';
    foreach ($data_horas as $mov) {
        
        if (in_array($mov['id_mov'], $arrAtrasos)) {
            $data .= "  <div class='col-sm-12' style='margin-top:5px'>
                            <div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-calendar'></i></span>
                                <input disabled type='text' title='{$mov['data_ref']}' value='{$mov['data_ref']}' placeholder='__/__/____' class='form-control data_atraso_falta'>
                                <span class='input-group-addon'><i class='fa fa-clock-o'></i></span>
                                <input disabled type='text' title='{$mov['horas_ref']}' value='{$mov['horas_ref']}' placeholder='___:__' class='form-control qnt_atraso_falta'>
                                <span data-id='{$mov['id']}' class='pointer input-group-addon rem_data_hora'><i class='danger fa fa-minus'></i></span>
                            </div>
                        </div>";
        } else if (in_array($mov['id_mov'], $arrFaltas)) {
            $data .= "  <div class='col-sm-6' style='margin-top:5px'>
                            <div class='input-group'>
                                <span class='input-group-addon'><i class='fa fa-calendar'></i></span>
                                <input disabled type='text' title='{$mov['data_ref']}' value='{$mov['data_ref']}' placeholder='__/__/____' class='form-control data_atraso_falta'>
                                <span data-id='{$mov['id']}' class='pointer input-group-addon rem_data_hora'><i class='danger fa fa-minus'></i></span>
                            </div>
                        </div>";
        }
    }
    
    echo $data;
    
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'updateDatasHorasFaltasAtrasos'){
    
    extract($_POST);
    
    if (!empty($_REQUEST['remove'])) {
        $removidos = Movimentos::removeDatasHorasFaltasAtrasos($remove);
    }
    
    if (!empty($_REQUEST['add'])) {
        
        $adicionados = Movimentos::addDatasHorasFaltasAtrasos(
                $id_clt, 
                $id_mov, 
                $id_movimento, 
                $mes, 
                $ano, 
                $add[0], 
                $add[1]);
    }
    
    if (!empty($_REQUEST['remove']) || !empty($_REQUEST['add'])) {
    
        $datas_horas = Movimentos::getDatasHorasFaltasAtrasos($id_movimento);
        
        $segundos = 0;
        $dias = 0;
        $tipo_quantidade = 2;
        foreach ($datas_horas as $data_hora) {
            $dias++;
            $segundos += $data_hora['total_seconds'];
        }
        $horas_minutos = gmdate("H:i", $segundos);
        
        if ($segundos > 0) {
            $tipo_quantidade = 1;
        }
            
        if ($tipo_quantidade == 1) {
            list($qnt_hora, $qnt_minuto) = explode(':', $horas_minutos);
            $totalQnt = $qnt_hora + ($qnt_minuto / 60);
        } elseif ($tipo_quantidade == 2) {
            $totalQnt = $dias;
            $horas_minutos = $dias;
        }
        
//        print_array([$totalQnt,$tipo_quantidade]);
        $valor = $calc_folha->getFaltasAtrasos($id_clt, $totalQnt, $tipo_quantidade);
//        print_array($valor);
        Movimentos::updateDatasHorasFaltasAtrasos($id_movimento, $valor, $horas_minutos, $tipo_quantidade);
        
    }
    
    echo 1;
    
}

?>
