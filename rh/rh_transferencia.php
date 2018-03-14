<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";
include "../classes/LogClass.php";

//include("../classes/RhClass.php");

$usuario = carregaUsuario();
$log = new Log();
$user = $usuario['id_funcionario'];
$regiao = $usuario['id_regiao'];
$master = $usuario['id_master'];
$id_clt = $_REQUEST['clt'];
$projeto = $_REQUEST['projeto'];
//$id_clt = 4213;
$tela = 1;
$bloqueio = false;

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

//$opt = array("2"=>"CLT","1"=>"Autônomo","3"=>"Cooperado","4"=>"Autônomo/PJ");

/**
 * By Ramon 29-07
 * VARIAVEL DE EMERGENCIA
 * POR PADRÃO SEMPRE TEM Q ESTAR FALSE
 * SE PRECISAR LIBERAR A TRANSFERENCIA MESMO COM FOLHA ABERTA COLOQUE TRUE
 */
$desconsideraBloq = true; 
$arRegFunc = array(1 => 1, 2 => 2, 3 => 2);
/**
 * OBJETOS 
 */
//$rh = new RhClass();
//$rh->AddClassExt('Clt'); 

/**
 * NOVA SELEÇÃO DE FUNÇÃO E HORARIOS
 */
if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'horarios'){
    $qr_horarios = mysql_query("SELECT id_horario, nome, entrada_1, saida_1, entrada_2, saida_2 FROM rh_horarios WHERE horas_semanais = (SELECT hora_semana FROM curso WHERE id_curso = '{$_REQUEST['id']}')");
    $verifica_horario = mysql_num_rows($qr_horarios);
    if (!empty($verifica_horario)) {
        $array[$row_horarios['id_horario']] = "-- Selecione --";
        while ($row_horarios = mysql_fetch_array($qr_horarios)) {
//            $auxHorario = ($idHorario == $row_horarios['id_horario']) ? ", selected: 'selected' " : null;
            $array[$row_horarios['id_horario']] = "{$row_horarios['id_horario']} - {$row_horarios['nome']} ( {$row_horarios['entrada_1']} - {$row_horarios['saida_1']} - {$row_horarios['entrada_2']} - {$row_horarios['saida_2']} )";
        }

        $html = montaSelect($array, $_REQUEST['rh_horario'], "class='validate[required] form-control' id='horario' name='horario'");
    }
    echo $html;
    exit;
}

/* CARREGA OS HORARIOS VIA AJAX, RETORNA UM JSON */
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregahorario") {

    $return['status'] = 1;
    
    
    $sql_horarios = "SELECT id_horario,nome,
                                date_format(entrada_1, '%H:%i:%s') as entrada1, 
                                date_format(saida_1, '%H:%i:%s') as saida_1, 
                                date_format(entrada_2, '%H:%i:%s') as entrada_2, 
                                date_format(saida_2, '%H:%i:%s') as saida_2
                                FROM rh_horarios WHERE funcao = {$_REQUEST['funcao']} AND status_reg=1 ORDER BY nome";
                                exit('Ok');
    $qrhorario = mysql_query($sql_horarios);
    $num_rowsU = mysql_num_rows($qrhorario);
    $unidades = array();
    if ($num_rowsU > 0) {
        while ($row = mysql_fetch_assoc($qrhorario)) {
            $unidades[$row['id_horario']] = $row['id_horario'] . " - " . utf8_encode($row['nome']) . " ({$row['entrada1']} - {$row['saida_1']} - {$row['entrada_2']} - {$row['saida_2']})";
        }
    } else {
        $unidades["-1"] = utf8_encode("nenhum horário encontrado");
    }

    $return['options'] = $unidades;

    echo json_encode($return);
    exit;
}

/* CARREGA OS PROJETOS VIA AJAX, RETORNA UM JSON */
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregaprojetos") {
    $return['status'] = 1;
    $reProjetos = montaQuery("projeto", "id_projeto,nome", "id_regiao={$_REQUEST['regiao']}", "nome", null);
    if (count($reProjetos) > 0) {
        foreach ($reProjetos as $pro) {
            $projetos[$pro["id_projeto"]] = $pro["id_projeto"] . " - " . utf8_encode($pro["nome"]);
        }
    } else {
        $projetos["-1"] = utf8_encode("nenhum projeto encontrado");
    }

    $return['options'] = $projetos;

    echo json_encode($return);
    exit;
}

/* CARREGA AS FUNÇÕES E UNIDADES VIA AJAX, RETORNA UM JSON */
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "carregafuncao") {
    //FUNÇÃO
    $qr_bancos = mysql_query("SELECT id_curso,nome,salario FROM curso WHERE campo3 = {$arRegFunc[$_REQUEST['projeto']]} AND tipo = 2 AND status_reg = 1 AND status = 1 ORDER BY nome");
    $num_rows = mysql_num_rows($qr_bancos);
    $cursos = array();
    if ($num_rows > 0) {
        $return['stfun'] = 1;
        while ($row = mysql_fetch_assoc($qr_bancos)) {
            $cursos[$row['id_curso']] = $row['id_curso'] . " - " . utf8_encode($row['nome']) . " - R$ " . number_format($row['salario'], 2, ",", ".");
        }
    } else {
        $return['stfun'] = 0;
        $cursos["-1"] = "nenhum curso encontrado";
    }

    //UNIDADE
    $qrUnidade = mysql_query("SELECT id_unidade,unidade FROM unidade WHERE campo1 = '{$_REQUEST['projeto']}' ORDER BY unidade");
    $num_rowsU = mysql_num_rows($qrUnidade);
    $unidades = array();
    if ($num_rowsU > 0) {
        $return['stunid'] = 1;
        while ($row = mysql_fetch_assoc($qrUnidade)) {
            $unidades[utf8_encode($row['id_unidade'] . "//" . $row['unidade'])] = utf8_encode($row['id_unidade'] . " - " . $row['unidade']);
        }
    } else {
        $return['stunid'] = 0;
        $unidades["-1"] = "nenhum curso encontrado";
    }

    //BANCOS
    $qrBancos = mysql_query("SELECT id_banco,nome,agencia,conta FROM bancos WHERE id_projeto = '{$_REQUEST['projeto']}'");
    $bancos = array();
    if (mysql_num_rows($qrBancos) > 0) {
        $return['stbanc'] = 1;
        while ($row = mysql_fetch_assoc($qrBancos)) {
            $bancos[$row['id_banco']] = $row['id_banco'] . " - " . utf8_encode($row['nome']) . " (AG: {$row['agencia']}, CC: {$row['conta']})";
        }
    } else {
        $return['stbanc'] = 0;
        $bancos["-1"] = "nenhum banco encontrado";
    }

    //TIPO PG
    $qrTipoPg = mysql_query("SELECT id_tipopg,tipopg FROM tipopg WHERE id_projeto = '{$_REQUEST['projeto']}'");
    $pagamentos = array();
    if (mysql_num_rows($qrTipoPg) > 0) {
        $return['sttppg'] = 1;
        while ($row = mysql_fetch_assoc($qrTipoPg)) {
            $pagamentos[$row['id_tipopg']] = utf8_encode($row['tipopg']);
        }
    } else {
        $return['sttppg'] = 0;
        $pagamentos["-1"] = "nenhum tipo de pagamento encontrado";
    }


    //SINDICATOS
    $qrSindicato = mysql_query("SELECT id_sindicato, nome FROM rhsindicato WHERE id_regiao = '{$_REQUEST['id_regiao']}'");
    $sindicatos = array();
    if (mysql_num_rows($qrSindicato) > 0) {
        $return['stsindicato'] = 1;
        while ($row = mysql_fetch_assoc($qrSindicato)) {
            $sindicatos[$row['id_sindicato']] = $row['id_sindicato'] . " - " . utf8_encode($row['nome']);
        }
    } else {
        $return['stsindicato'] = 0;
        $sindicatos["-1"] = "nenhum banco encontrado";
    }

    $return['funcao'] = $cursos;
    $return['unidade'] = $unidades;
    $return['bancos'] = $bancos;
    $return['pagamentos'] = $pagamentos;
    $return['sindicatos'] = $sindicatos;

    echo json_encode($return);
    exit;
}

/*DADOS DO CLT*/
$qr = "SELECT 
        A.nome,A.cpf,A.id_regiao,A.id_projeto,A.id_curso,A.id_regiao,A.foto,A.locacao,A.id_unidade,A.tipo_pagamento,A.banco,A.rh_sindicato,A.tipo_contratacao,A.rh_horario,A.nome_banco,
        B.nome as projeto,
        C.nome as funcao, C.salario,
        D.id_horario,D.nome as horario, G.tipopg as tipo_pg,
        date_format(D.entrada_1, '%H:%i:%s') as entrada1, 
        date_format(D.saida_1, '%H:%i:%s') as saida_1, 
        date_format(D.entrada_2, '%H:%i:%s') as entrada_2, 
        date_format(D.saida_2, '%H:%i:%s') as saida_2,
        E.regiao,
        F.nome as nome_sindicato
        FROM rh_clt AS A
        LEFT JOIN projeto AS B ON (A.id_projeto=B.id_projeto)
        LEFT JOIN curso AS C ON (A.id_curso=C.id_curso)
        LEFT JOIN rh_horarios AS D ON (D.id_horario = A.rh_horario)
        LEFT JOIN regioes AS E ON (E.id_regiao = A.id_regiao)
        LEFT JOIN rhsindicato as F ON (F.id_sindicato = A.rh_sindicato)
        LEFT JOIN tipopg as G ON (G.id_tipopg = A.tipo_pagamento)
        LEFT JOIN bancos as H ON (H.id_banco = A.banco)
        WHERE id_clt = {$id_clt}";
        
$result = mysql_query($qr) or die(mysql_error());
if (mysql_num_rows($result) == 0) {
    echo "Erro! Funcionário não encontrado.";
    exit;
}
$clt = mysql_fetch_assoc($result);


/**
 * SELECIONA TODAS AS FUNÇÕES DO PROJETO
 */
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == funcoesProjeto){
    
if($projeto == 3){
    $projeto_curso = 2;
}else{
    $projeto_curso = $projeto;
}
    
$sqlCurso = mysql_query("SELECT id_curso, nome, letra, numero, valor, salario FROM curso WHERE campo3 = '{$projeto_curso}' AND tipo IN(0,2) AND status = '1' AND status_reg = '1' ORDER BY nome ASC");
$verifica_curso = mysql_num_rows($sqlCurso);
if (!empty($verifica_curso)) {
    $arrayFuncoes[''] = "-- SELECIONE --";
    while ($row_curso = mysql_fetch_assoc($sqlCurso)) {
        
        $salario = number_format((!empty($row_curso['valor'])) ? $row_curso['valor'] : $row_curso['salario'], 2, ',', '.');
        $nomeNovo = "{$row_curso['nome']} {$row_curso['letra']}{$row_curso['numero']}";
        $arrayFuncoes[$row_curso['id_curso']] = utf8_encode("{$row_curso['id_curso']} - {$nomeNovo} (Valor: $salario)");
        
        /**
         * seleção de curso nova
         */
        $arrayCursosNovo[$row_curso['nome']][$row_curso['letra']][$row_curso['numero']] = $row_curso;
    }
    $cursoLetras = array("A","B","C","D","E","F","G");
        if(count($arrayCursosNovo) > 0){
            $tabelaFuncoesNova = "<table class='table table-bordered table-condensed text-sm valign-middle'><tr><td>Cargo</td><td class='text-center'>Letra</td><td class='text-center'>1</td><td class='text-center'>2</td><td class='text-center'>3</td><td class='text-center'>4</td><td class='text-center'>5</td></tr>";
            foreach ($arrayCursosNovo as $nome => $value) {
                $tabelaFuncoesNova .= "<tr><td rowspan='".(count($value))."'>".utf8_encode($nome).'</td>';
                if(!$value['']){
                    foreach ($cursoLetras as $letra) {
                        if($value[$letra]){
            //                $tabelaFuncoesNova .= (count($value) > 1) ? '<tr>' : '';
                            $tabelaFuncoesNova .= "<td class='text-center'>$letra</td>";
                            for ($i = 1; $i <= 5; $i++) {
                                switch ($i) {
                                    case 1: $btn_cor = 'default'; break;
                                    case 2: $btn_cor = 'warning'; break;
                                    case 3: $btn_cor = 'primary'; break;
                                    case 4: $btn_cor = 'info'; break;
                                    case 5: $btn_cor = 'success'; break;
                                }
                                if($value[$letra][$i]){
                                    $tabelaFuncoesNova .= "<td class='text-center'><button type='button' class='btn btn-{$btn_cor} nova_selecao_funcao' data-id='{$value[$letra][$i]['id_curso']}'>".number_format($value[$letra][$i]['valor'],2,',','.')."</button></td>";
                                } else {
                                    $tabelaFuncoesNova .= "<td></td>";
                                }
                            }
                            $tabelaFuncoesNova .= '</tr>';
                        }
                    }
                } else {
                    $tabelaFuncoesNova .= "<td class='text-center'><button type='button' class='btn btn-default nova_selecao_funcao' data-id='{$value['']['']['id_curso']}'>".number_format($value['']['']['valor'],2,',','.')."</button></td><td colspan='5'></td>";
                }

                $tabelaFuncoesNova .= '</tr>';
            }
            $tabelaFuncoesNova .= '<table>';
            echo $tabelaFuncoesNova;
        }
    } else {
        $arrayFuncoes[''] = "Nenhum Curso Cadastrado para o Projeto";
    }
    exit;
}

//Regiões
//$reRegioes = montaQuery("regioes", "id_regiao,regiao", "id_master={$master}", "regiao");
$sqlReg = "SELECT * FROM regioes";
$queryReg = mysql_query($sqlReg);

$reRegioes = array("-1" => "« Selecione »");

while($row = mysql_fetch_assoc($queryReg)) {    
    $reRegioes[$row['id_regiao']] =  $row['id_regiao'] . ' - ' .$row['regiao'];   
}

//print_r($reRegioes);


//foreach ($reRegioes as $reg) {
//    $regioes[$reg["id_regiao"]] = $reg["id_regiao"] . " - " . $reg["regiao"];
//}

//Projeto
$reProjetos = montaQuery("projeto", "id_projeto,nome", "id_regiao={$clt['id_regiao']}", "id_projeto");
$projeto = array("-1" => "« Selecione »");
foreach ($reProjetos as $pro) {
    $projetosOpt[$pro["id_projeto"]] = $pro["id_projeto"] . " - " . $pro["nome"];
}

//CURSO
$reCursos = montaQuery("curso", "id_curso,nome", array("campo3 = {$clt['id_projeto']} AND tipo IN (0,{$clt['tipo_contratacao']})"), "id_curso");
$cursosOpt = array("-1" => "« Selecione »");
foreach ($reCursos as $funcao) {
    $cursosOpt[$funcao["id_curso"]] = $funcao["id_curso"] . " - " . $funcao["nome"];
}

//SINDICATO
$reSindicato = montaQuery("rhsindicato", "*", "id_regiao={$clt['id_regiao']}", 'id_sindicato');
$sindicatoOpt = array("-1" => "« Selecione »");
foreach ($reSindicato as $sind) {
    $sindicatoOpt[$sind["id_sindicato"]] = $sind["id_sindicato"] . " - " . $sind["nome"];
}

//horarios
$reHorarios = montaQuery("rh_horarios", "*", "funcao={$clt['id_curso']}", 'id_horario');
$horarioOpt = array("-1" => "« Selecione »");
foreach ($reHorarios as $hor) {
    $horarioOpt[$hor["id_horario"]] = $hor["id_horario"] . " - " . $hor["nome"] . ' (' . $hor['entrada_1'] . ' - ' . $hor['saida_1'] . ' - ' . $hor['entrada_2'] . ' - ' . $hor['saida_2'] . ')';
}
//
////horarios
//$reHorarios = montaQuery("rh_horarios","*","funcao={$clt['id_curso']}",'id_horario');
//$horarioOpt = array("-1"=>"« Selecione »");
//foreach ($reHorarios as $hor) {
//    $horarioOpt[$hor["id_horario"]] = $hor["id_horario"] . " - " . $hor["nome"].' ('.$hor['entrada_1'].' - '.$hor['saida_1'].' - '.$hor['entrada_2'].' - '.$hor['saida_2'].')';
//}
//UnidADE
$reUnidade = montaQuery("unidade", "*", "campo1={$clt['id_projeto']}", 'id_unidade');
$unidadeOpt = array("-1" => "« Selecione »");
foreach ($reUnidade as $uni) {
    $unidadeOpt[$uni["id_unidade"]] = $uni["id_unidade"] . " - " . $uni['unidade'];
}

//Banco
$reBanco = montaQuery("bancos", "*", "id_projeto={$clt['id_projeto']}", 'id_banco');
$bancoOpt = array("-1" => "« Selecione »");
foreach ($reBanco as $banc) {
    $bancoOpt[$banc["id_banco"]] = $banc['id_banco'] . ' - ' . $banc['nome'];
}

//Tipo de pagamento
$reTipo = montaQuery("tipopg", "*", "id_projeto={$clt['id_projeto']} AND id_regiao = '$clt[id_regiao]'");
$tipoOpt = array("-1" => "« Selecione »");
foreach ($reTipo as $tipo) {
    $tipoOpt[$tipo["id_tipopg"]] = $tipo['id_tipopg'] . ' - ' . $tipo['tipopg'];
}

$id_unidade = $_REQUEST['unidade'];
//Nome Unidade
$reNomeUnidade = montaQuery("unidade", "unidade", "id_unidade = $id_unidade", 'id_unidade');
foreach ($reNomeUnidade as $nomeUni) {
    $nome_unidade = $nomeUni['unidade'];
}

/* APRONTANDO TODOS AS QUERYS DE TRANSFERENCIA */
if (isset($_REQUEST['transferir']) && $_REQUEST['transferir'] == "Transferir") {
    
    /**
     * ATUALIZANDO O CAMPO data_ultima_atualizacao
     * EM rh_clt SEMPRE QUE ALGUMA ATUALIZAÇÃO FOR 
     * FEITA NO CLT
     */
//    $rh->Clt->setDefault()->setIdClt($id_clt)->onUpdate();
    $antigo = $log->getLinha('rh_clt',$id_clt);
    onUpdate($id_clt);
    $novo = $log->getLinha('rh_clt',$id_clt);
    $log->log('2',"Funcionário ID $id_clt alterado",'rh_clt',$antigo,$novo);
    
    //if($_COOKIE[logado] == 257){
    $mudarApenasUnidade = false;
    if  (
        $clt['id_regiao'] == $_REQUEST['regiao'] &&
        $clt['id_projeto'] == $_REQUEST['projeto'] &&
        $clt['id_curso'] == $_REQUEST['curso'] &&
        $clt['id_horario'] == $_REQUEST['horario'] &&
        $clt['tipo_pagamento'] == $_REQUEST['tipopg'] &&
        $clt['banco'] == $_REQUEST['banco'] &&
        $clt['locacao'] != $nome_unidade &&
        $clt['id_unidade'] != $id_unidade &&
        $clt['rh_sindicato'] == $_REQUEST['sindicato']
    ){
        $mudarApenasUnidade = true;
    }

    $sqlFolhaProcFechada = "SELECT id_folha_proc FROM rh_folha_proc A INNER JOIN rh_folha B ON(A.id_folha = B.id_folha) WHERE A.status = 3 AND A.ano = {$_REQUEST['ano']} AND A.mes = {$_REQUEST['mes']} AND A.id_clt = $id_clt AND B.terceiro = 2 LIMIT 1;";
    $sqlFolhaProcFechada = mysql_query($sqlFolhaProcFechada);
    if(mysql_num_rows($sqlFolhaProcFechada) > 0){
        if ($mudarApenasUnidade == false){
            header("Location: rh_transferencia.php?clt=$id_clt&erro=2");
            exit;
        }
    }
    $sqlFolhaFechada = "SELECT id_folha FROM rh_folha WHERE status = 3 AND ano = {$_REQUEST['ano']} AND mes = {$_REQUEST['mes']} AND terceiro = 2 AND projeto IN ({$clt['id_projeto']},{$_REQUEST['projeto']}) LIMIT 1;";
    $sqlFolhaFechada = mysql_query($sqlFolhaFechada);
    if(mysql_num_rows($sqlFolhaFechada) > 0){
        if ($mudarApenasUnidade == false){
            header("Location: rh_transferencia.php?clt=$id_clt&erro=3");
            exit;
        }
    }
    //}
    $tela = 2;                                                                                          
    //VÃO SELECIONAR O MES QUE VAI ENTRAR O NOVO SALARIO
    //POREM VOU GRAVAR O MES ANTERIOR, POIS O SISTEMA JA TRABALHA COM ESSE CALCULO
    $mes2d = sprintf("%02d", $_REQUEST['mes']);
    $d = "{$_REQUEST['ano']}-{$mes2d}-28";
    $data_mov = date("Y-m-d", strtotime($d . " -1 month"));
    $campos = array(
        "id_clt",
        "id_regiao_de",
        "id_projeto_de",
        "id_curso_de",
        "id_horario_de",
        "id_tipo_pagamento_de",
        "id_banco_de",
        "unidade_de",
        "id_unidade_de",
        "id_regiao_para",
        "id_projeto_para",
        "id_curso_para",
        "id_horario_para",
        "id_tipo_pagamento_para",
        "id_banco_para",
        "unidade_para",
        "id_unidade_para",
        "motivo",
        "data_proc",
        "criado_em",
        "id_usuario",
        "id_sindicato_de",
        "id_sindicato_para",
        "data_transferencia"
    );
    
    $data_transferencia = implode("-",array_reverse(explode("/",$_REQUEST['data_transferencia'])));

    $valores = array(
        $id_clt,
        $clt['id_regiao'],
        $clt['id_projeto'],
        $clt['id_curso'],
        $clt['id_horario'],
        $clt['tipo_pagamento'],
        $clt['banco'],
        $clt['locacao'],
        $clt['id_unidade'],
        $_REQUEST['regiao'],
        $_REQUEST['projeto'],
        $_REQUEST['curso'],
        $_REQUEST['horario'],
        $_REQUEST['tipopg'],
        $_REQUEST['banco'],
        $nome_unidade,
        $id_unidade,
        $_REQUEST['motivo'],
        $data_mov,
        date("Y-m-d H:i:s"),
        $user,
        $clt['rh_sindicato'],
        $_REQUEST['sindicato'],
        $data_transferencia
    );
    
    
    $selDependentes = "SELECT id_dependentes FROM dependentes WHERE id_bolsista = $id_clt";
    $querySelDependentes = mysql_query($selDependentes);
    while($rowSelDependentes = mysql_fetch_assoc($querySelDependentes)) {
        $idDependentes = $rowSelDependentes['id_dependentes'];
        
        $antigo = $log->getLinha('dependentes',$idDependentes);
        $sql = "UPDATE dependentes SET id_projeto = {$_REQUEST['projeto']}, id_regiao = {$_REQUEST['regiao']} WHERE id_dependentes = $idDependentes";
        $query = mysql_query($sql);
        $novo = $log->getLinha('dependentes',$idDependentes);
        
        $log->log('2',"Dependente ID $idDependentes atualizado",'dependentes',$antigo,$novo);
    }
    
    $condicao = array(
        "regiao" => $clt['id_regiao'],
        "projeto" => $clt['id_projeto'],
        "mes" => date('m'),
        "ano" => date('Y'),
        "status" => 2
    );

    $rsFolha = montaQuery("rh_folha", "*", $condicao);

    //Se a folha estiver aberta, atualiza o campo id_curso e id_horario na folha
    $qr_verifica_folha = mysql_query("SELECT B.* FROM rh_folha as A
                                            INNER JOIN rh_folha_proc as B
                                            ON A.id_folha = B.id_folha
                                            WHERE A.mes = " . date('m') . " AND A.ano = '" . date('Y') . "' AND A.status = 2 AND B.status = 2 AND
                                            B.id_clt = $id_clt;");

    if (mysql_num_rows($qr_verifica_folha) != 0) {
        $row_folha = mysql_fetch_assoc($qr_verifica_folha);
        $idFolhaProc = $row_folha['id_folha_proc'];
        
        $antigo = $log->getLinha('rh_folha_proc',$idFolhaProc);
        
        mysql_query("UPDATE rh_folha_proc SET id_curso = '$_REQUEST[curso]', id_horario = '$_REQUEST[horario]'  
                        WHERE id_folha_proc = $idFolhaProc
                        LIMIT 1");
        $novo = $log->getLinha('rh_folha_proc',$idFolhaProc);
        
        $log->log('2',"Folha proc. ID {$row_folha['id_folha_proc']} alterada",$antigo,$novo);
    }
    /////////////

    if (count($rsFolha) > 0) {
        $ids = array();
        foreach ($rsFolha as $val) {
            $ids[] = $val['id_folha'];
        }

        $rsFolhaProc = montaQuery("rh_folha_proc", "*", "id_folha IN (" . implode(",", $ids) . ") AND id_clt = '{$id_clt}' AND status = 2");
    }

    if (($clt['id_regiao'] != $_REQUEST['regiao'] or $clt['id_projeto'] != $_REQUEST['projeto']) and count($rsFolhaProc) > 0) {
        echo '<p>O funcionário selecionado encontra-se em uma folha em aberto, para trocar de unidade é necessário removê-lo da folha para realizar a transferência!</p>';
        echo '<a href="rh_transferencia.php?clt=' . $id_clt . '"> VOLTAR </a>';
        exit;
    }

    $rhTransfId = sqlInsert("rh_transferencias", $campos, $valores);
    $log->log('2',"Nova transferência efetuada ID $rhTransfId",'rh_transferencias');
    
    $updates = array(
        "id_regiao" => $_REQUEST['regiao'],
        "id_projeto" => $_REQUEST['projeto'],
        "id_curso" => $_REQUEST['curso'],
        "rh_horario" => $_REQUEST['horario'],
        "tipo_pagamento" => $_REQUEST['tipopg'],
        "banco" => $_REQUEST['banco'],
        "locacao" => $nome_unidade,
        "id_unidade" => $id_unidade,
        "rh_sindicato" => $_REQUEST['sindicato']
    );
    
    $antigo = $log->getLinha('rh_clt',$id_clt);
    sqlUpdate("rh_clt", $updates, array("id_clt" => $id_clt));
    $novo = $log->getLinha('rh_clt',$id_clt);
    
    $log->log('2',"Funcionário ID $id_clt transferido com sucesso",'rh_clt',$antigo,$novo);

    /* MUDAR O NOME DA FOTO */
    if ($clt['foto'] == '1') {
        $dir = dirname(dirname(__FILE__)) . "/fotosclt/";
        $nomeOld = $clt['id_regiao'] . "_" . $clt['id_projeto'] . "_" . $id_clt . '.gif';
        $nomeNovo = $_REQUEST['regiao'] . "_" . $_REQUEST['projeto'] . "_" . $id_clt . '.gif';
        rename($dir . $nomeOld, $dir . $nomeNovo);
        $log->log('2',"Foto do funcionário ID $id_clt renomeada para $nomeNovo",'');
    }
}


/* VERIFICANDO SE O FUNCIONÁRIO ESTÁ NA FOLHA DE PAGAMENTO */
//SELECT * FROM rh_folha WHERE regiao = 45 AND projeto = 3302 AND status = 2
$condicao = array(
    "regiao" => $regiao,
    "projeto" => $clt['id_projeto'],
    "mes" => date('m'),
    "ano" => date('Y'),
    "status" => 2
);

$rsFolha = montaQuery("rh_folha", "*", $condicao);

if (count($rsFolha) > 0) {
    $ids = array();
    foreach ($rsFolha as $val) {
        $ids[] = $val['id_folha'];
    }

    $rsFolhaProc = montaQuery("rh_folha_proc", "*", "id_folha IN (" . implode(",", $ids) . ") AND id_clt = '{$id_clt}' AND status = 2");

    if (count($rsFolhaProc) > 0) {
        $bloqueio = true;
    }
}

$dtnow = date("Y-m-d");
//TRAVANDO PARA RODAR TRANSFERENCIA SOMENTE PARA A COMPETENCIA ATUAL
//$meses = @mesesArray();
//$anos = anosArray(null, null, array("-1" => "« Selecione »"));

$m = date('m');
$a = date('Y');
$meses = array($m=>mesesArray($m));
$anos = array($a);
$mesSelected = date("m", strtotime($dtnow . " -1 month"));
$anoSelected = date("Y", strtotime($dtnow . " -1 month"));

/*LOGICA: SE FOR INICIO DO MES, A FOLHA DO MES ANTERIOR PODE SER Q ESTEJA ABERTA AINDA, ENTÃO A TRANSFERENCIA SERÁ DO MES ANTERIOU (MES DA FOLHA EM ABERTO)*/
$condicaoFolhaAnterior = array(
    "regiao" => $regiao,
    "projeto" => $clt['id_projeto'],
    "mes" => $mesSelected,
    "ano" => $anoSelected,
    "status" => 2
);

$rsFolhaAnterior = montaQuery("rh_folha", "id_folha", $condicaoFolhaAnterior);
if(count($rsFolhaAnterior)>0){
    /*NESSE CASO TEM FOLHA DO MES ANTERIOR ABERTA, VAMOS SOBRESCREVER A VARIAVEL $m E $a*/
    $m = $mesSelected;
    $a = $anoSelected;
}

/**
 * By Ramon 29-07-2016
 * Variavel de emergencia que ignora a verificação de folha em aberto
 * e deixa transferir mesmo assim
 */
if($desconsideraBloq){
    $bloqueio = false;
}


//$sql = "SELECT * FROM dependentes  where id_bolsista=" . $id_clt;
//echo "<br>".$sql."<br>";
//while ($res = mysql_fetch_array($row_dependentes)) {
//    echo '<pre>';
//    print_r($res);
//    echo '</pre>';
//}
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Transferência de Funcionário</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- CSS -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <!--<link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">-->
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <!--<link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">-->
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        
        <style>
            .input-group-addon {
                background-color: #fff;
                border: none;
            }
        </style>
        
    </head>
    <body>
    <?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Transferência de Funcionário</h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <?php if ($tela == 1) {?>
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Informações Atuais do Funcionário</div>
                    
                    <?php if ($_REQUEST['erro'] == 2){ ?>
                    <div class="alert alert-danger" role="alert">
                    O funcionário selecionado encontra-se em uma folha fechada na competência selecianada!
                    </div>
                    <?php } else if($_REQUEST['erro'] == 3){ ?>
                    <div class="alert alert-danger" role="alert">
                    O funcionário selecionado não pode sair nem entrar em um projeto com folha fechada na competência selecianada!
                    </div>    
                    <?php }
                    if ($bloqueio) { ?>
                    <div class="alert alert-warning" role="alert">
                        O funcionário selecionado encontra-se em uma folha em aberto, não poderá alterar região e projeto!
                    </div>
                    <?php } ?>
                    <div class="panel-body">
                        <div class="form-group" >

                            <label  class="col-sm-2 control-label hidden-print" >Nome:</label>
                            <div class="col-sm-6 valign"><input type="text" value="<?php echo $clt['nome'] ?>" class="form-control" disabled><span class="loader"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label  class="col-sm-2 control-label hidden-print" >CPF:</label>
                            <div class="col-sm-6 valign"><input type="text" value="<?php echo $clt['cpf'] ?>" class="form-control" disabled><span class="loader"></span>
                            </div>
                        </div>
                        <div class="form-group">
                           <label  class="col-sm-2 control-label hidden-print" >Região:</label>
                            <div class="col-sm-6 valign"><input type="text" value="<?php echo $clt['regiao'] ?>" class="form-control" disabled><span class="loader"></span>
                            </div>
                        </div>
                        <div class="form-group">
                           <label  class="col-sm-2 control-label hidden-print" >Projeto:</label>
                            <div class="col-sm-6 valign"><input type="text" value="<?php echo $clt['projeto'] ?>" class="form-control" disabled><span class="loader"></span>
                            </div>
                        </div>
                        <div class="form-group">
                           <label  class="col-sm-2 control-label hidden-print" >Função:</label>
                            <div class="col-sm-6 valign"><input type="text" value="<?php echo $clt['id_curso'] . $clt['funcao'] . " - R$ " . number_format($clt['salario'], 2, ",", ".") ?>" class="form-control" disabled><span class="loader"></span>
                            </div>
                        </div>
                        <div class="form-group">
                           <label  class="col-sm-2 control-label hidden-print" >Sindicato:</label>
                            <div class="col-sm-6 valign"><input type="text" value="<?php echo $clt['rh_sindicato'] . ' - ' . $clt['nome_sindicato']; ?>" class="form-control" disabled><span class="loader"></span>
                            </div>
                        </div>
                        <div class="form-group">
                           <label  class="col-sm-2 control-label hidden-print" >Horário:</label>
                            <div class="col-sm-6 valign"><input type="text" value="<?php echo $clt['id_horario'] . " - " . $clt['horario'] . " ({$row['entrada1']} - {$row['saida_1']} - {$row['entrada_2']} - {$row['saida_2']})" ?>" class="form-control" disabled><span class="loader"></span>
                            </div>
                        </div>
                        <div class="form-group">
                           <label  class="col-sm-2 control-label hidden-print" >Unidade:</label>
                            <div class="col-sm-6 valign"><input type="text" value="<?php echo $clt['id_unidade'] . " - " . $clt['locacao'] ?>" class="form-control" disabled><span class="loader"></span>
                            </div>
                        </div>
                        <div class="form-group">
                           <label  class="col-sm-2 control-label hidden-print" >Banco:</label>
                            <div class="col-sm-6 valign"><input type="text" value="<?php echo $clt['nome_banco'] ?>" class="form-control" disabled><span class="loader"></span>
                            </div>
                        </div>
                        <div class="form-group">
                           <label  class="col-sm-2 control-label hidden-print" >Tipo de Pagamento:</label>
                            <div class="col-sm-6 valign"><input type="text" value="<?php echo $clt['tipo_pg'] ?>" class="form-control" disabled><span class="loader"></span>
                            </div>
                        </div> <!--fim form group-->
                    </div> <!--fim panel body-->
                        
                    <div class="panel-heading text-bold hidden-print">Informações Atuais do Funcionário</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-sm-12 alert alert-warning text-center" role="alert">
                                As Transferencias futuras foram desabilitadas, agora só podem acontecer para a folha que estiver aberta.
                            </div>
                        </div>
                        <div class="form-group">
                            <label  class="col-sm-2 control-label hidden-print"> Competência:</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect($meses, $mesSelected, "id='mes' name='mes' class='validate[required] form-control'") ?>
                            </div>
                            <div class="col-sm-2">
                                <?php echo montaSelect($anos, $anoSelected, "id='ano' name='ano' class='validate[required] form-control'") ?> 
                            </div>
                            <!--div class="col-sm-6">
                                <span>Competência da folha que entrará a diferença salarial</span>
                                <span class="loader"></span>
                            </div>
                            <br><br-->
                        </div>
                        <div class="form-group">
                            <label  class="col-sm-2 control-label hidden-print"> Data Transferência:</label>
                            <div class="col-sm-2">
                                <input type="text" size="8" maxlength="10" id="data_transferencia"  name="data_transferencia" class="form-control"> <span class="loader"></span>
                            </div>
                        </div>
                                
                        <?php if ($bloqueio) { ?>
                        <div class="form-group">
                           <label  class="col-sm-2 control-label hidden-print" >Região:</label>
                            <div class="col-sm-6 valign"><?php echo $regioes[$clt['id_regiao']]; ?><input type="hidden" id='regiao' name='regiao' value="<?php echo $clt['id_regiao']; ?>" ><span class="loader"></span>
                            </div>
                        </div>
                        <div class="form-group">
                           <label  class="col-sm-2 control-label hidden-print" >Projeto:</label>
                            <div class="col-sm-6 valign"><?php echo $projetosOpt[$clt['id_projeto']]; ?><input type="hidden" id='projeto' name='projeto' value="<?php echo $clt['id_projeto']; ?>" ><span class="loader"></span>
                            </div>
                        </div>
                            <?php }else{ ?>
                        <div class="form-group">
                           <label  class="col-sm-2 control-label hidden-print" >Região:</label>
                            <div class="col-sm-6 valign"><?php echo montaSelect($reRegioes, $clt['id_regiao'], "id='regiao' name='regiao' class='validate[required] form-control'") ?><span class="loader"></span>
                            </div>
                        </div>
                        <div class="form-group">
                           <label  class="col-sm-2 control-label hidden-print" >Projeto:</label>
                            <div class="col-sm-6 valign"><?php echo montaSelect($projetosOpt, $clt['id_projeto'], "id='projeto' name='projeto' class='validate[required] form-control'") ?><span class="loader"></span>
                            </div>
                        </div>              
                        <?php } ?>
                        
                        <div class="form-group">
                           <label  class="col-sm-2 control-label hidden-print" >Função:</label>
                            <div class="col-sm-6 valign">
                                <div class="input-group">
                                    <?php echo montaSelect($cursosOpt, $clt['id_curso'], "id='curso' name='curso' class='validate[required] form-control' style='margin-top: 6px;'") ?>
<!--                                    <span class="input-group-addon">
                                        <button type="button" class="btn btn-default" id="btn-funcoes"><i class="fa fa-eye"></i></button>
                                    </span>    -->
                                </div>
                                <!--span class="loader"></span-->
                            </div>
                        </div>
                           
                        <div class="form-group">   
                           <label  class="col-sm-2 control-label hidden-print" >Sindicato:</label>
                            <div class="col-sm-6 valign">
                                <?php echo montaSelect($sindicatoOpt, $clt['rh_sindicato'], "id='sindicato' name='sindicato' class='form-control' ") ?><span class="loader"></span>
                            </div>
                        </div>
                        <div class="form-group">   
                           <label  class="col-sm-2 control-label hidden-print" >Horário:</label>
                            <div class="col-sm-6 valign">
                                <div id="div_horario"><?php echo montaSelect($horarioOpt, $clt['id_horario'], "id='horario' name='horario' class='form-control validate[required] ' ") ?></div><span class="loader"></span>
                            </div>
                        </div>
                        <div class="form-group">
                           <label  class="col-sm-2 control-label hidden-print" >Unidade:</label>
                            <div class="col-sm-6 valign">
                                <?php echo montaSelect($unidadeOpt, $clt['id_unidade'], "id='unidade' name='unidade' class='validate[required] form-control'") ?><span class="loader"></span>
                            </div>
                        </div>
                        <div class="form-group">
                           <label  class="col-sm-2 control-label hidden-print" >Banco:</label>
                            <div class="col-sm-6 valign">
                                <?php echo montaSelect($bancoOpt, $clt['banco'], "id='banco' name='banco' class='form-control' ") ?><span class="loader"></span>
                            </div>
                        </div>
                        <div class="form-group">
                           <label  class="col-sm-2 control-label hidden-print" >Tipo de Pagamento:</label>
                            <div class="col-sm-6 valign">
                                <?php echo montaSelect($tipoOpt, $clt['tipo_pagamento'], "id='tipopg' name='tipopg' class='form-control' ") ?><span class="loader"></span>
                            </div>
                        </div>
                        <div class="form-group">
                           <label  class="col-sm-2 control-label hidden-print" >Motivo:</label>
                           <div class="col-sm-6 valign">
                               <textarea id="motivo" name="motivo" cols="25" rows="5" class="form-control"></textarea><span class="loader"></span>
                            </div>
                        </div>
                           
                        </div> <!--fim form group-->
                        

                        <div class="panel-footer text-right hidden-print controls">
                            <input type="hidden" name="clt" value="<?= $id_clt ?>" />
                            <button type="submit" name="transferir" id="transferir" value="Transferir" class="btn btn-primary"><span class="fa fa-exchange"></span>Transferir</button>
                        </div>
                    </div> <!-- fim panel default -->
               
               <?php } else { ?>
                    <h2>Funcionário transferido com sucesso!</h2>
                    <br/><br/>
                    <p>feche a tela para continuar navegando</p>
                     
                <?php } ?>
            <?php include('../template/footer.php'); ?>
        </form>
        </div>
            
        <div class="clear"></div>
        
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="consulta.js"></script>
        <script src="../js/ramon.js" type="text/javascript" language="javascript"></script>
        <script type="text/javascript" src="../js/jquery-1.8.3.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../js/jquery.ui.datepicker-pt-BR.js"></script>
        <script type="text/javascript" src="../jquery/priceFormat.js"></script>
        <script type="text/javascript" src="../js/valida_documento.js"></script>
        <script type="text/javascript" src="../js/jquery.maskedinput.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt.js"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/main.js"></script>
         <script>
            $(function() {
                
                /**
                 * NOVA SELEÇÂO DE FUNÇÃO E HORÁRIO
                 */
                
                /**
                 * NOVA SELEÇÂO DE FUNÇÃO E HORÁRIO
                 */
                
                $('body').on('click', '#btn-funcoes', function(){
                    var projeto = $('#projeto').val();
//                    console.log(projeto);
                    $.post("", {method:'funcoesProjeto', projeto: projeto}, function(tabelaFuncoesNova){
                            new BootstrapDialog({
                                nl2br: false,
                                type: 'type-primary',
                                title: 'PLANO DE CARGOS E SALÁRIOS COMPLETO',
                                message: tabelaFuncoesNova,
                                size: BootstrapDialog.SIZE_WIDE,
                                closable: true
                            }).open();                        
                    });

                });
                
                $('body').on('click', '.nova_selecao_funcao', function(){
                    $('#curso').val($(this).data('id')).trigger('change');
                    $('.modal, .modal-backdrop').remove();
                });

                $('body').on('change', '#curso', function(){
                    if($(this).val() > 0) { 
                        $.post("", {bugger:Math.random(), method:'horarios', id:$(this).val(), rh_horario: '<?= $clt['rh_horario'] ?>'}, function(result){
                            $('#div_horario').html(result);
                            $('#horario').html(result);
                        });
                    }
                });
                $('#curso').trigger('change');
                /*****************************************************************************/
                
                
                $("#form1").validationEngine();

                $("#regiao").change(function() {
                    var $this = $(this);
                    if ($this.val() != "-1") {
                        $.post('rh_transferencia.php', {regiao: $this.val(), method: "carregaprojetos"}, function(data) {
                            if (data.status == 1) {
                                var opcao = "<option value='-1'>« Selecione »</option>\n";

                                $("#curso").html("<option value='-1'>« Selecione o Projeto »</option>");
                                $("#horario").html("<option value='-1'>« Selecione o Função »</option>");
                                $("#unidade").html("<option value='-1'>« Selecione o Projeto »</option>");

                                for (var i in data.options) {
                                    opcao += "<option value='" + i + "'>" + data.options[i] + "</option>\n";
                                }
                                $("#projeto").html(opcao);
                            }
                        }, "json");
                    }
                });

                $("#projeto").change(function() {
                    var $this = $(this);
                    if ($this.val() != "-1") {
                        $.post('rh_transferencia.php', {projeto: $this.val(), id_regiao: $('#regiao').val(), method: "carregafuncao"}, function(data) {
                            if (data.stfun == 1) {
                                var opcao = "<option value='-1'>« Selecione »</option>\n";
                                var selected = "";
                                for (var i in data.funcao) {
                                    selected = "";
                                    if (i == $("#cursoSel").val()) {
                                        selected = "selected=\"selected\" ";
                                    }
                                    opcao += "<option value='" + i + "' " + selected + ">" + data.funcao[i] + "</option>\n";
                                }
                                $("#curso").html(opcao);
                            }
                            if (data.stunid == 1) {
                                var unid = "<option value='-1'>« Selecione »</option>\n";
                                for (var i in data.unidade) {
                                    unid += "<option value='" + i + "' " + selected + ">" + data.unidade[i] + "</option>\n";
                                }
                                $("#unidade").html(unid);
                            }
                            if (data.stbanc == 1) {
                                var unid = "<option value='-1'>« Selecione »</option>\n";
                                for (var i in data.bancos) {
                                    unid += "<option value='" + i + "' " + selected + ">" + data.bancos[i] + "</option>\n";
                                }
                                $("#banco").html(unid);
                            }
                            if (data.sttppg == 1) {
                                var unid = "<option value='-1'>« Selecione »</option>\n";
                                for (var i in data.pagamentos) {
                                    unid += "<option value='" + i + "' " + selected + ">" + data.pagamentos[i] + "</option>\n";
                                }
                                $("#tipopg").html(unid);
                            }


                            if (data.stsindicato == 1) {
                                var unid = "<option value='-1'>« Selecione »</option>\n";
                                for (var i in data.sindicatos) {
                                    unid += "<option value='" + i + "' " + selected + ">" + data.sindicatos[i] + "</option>\n";
                                }
                                $("#sindicato").html(unid);
                            }

                        }, "json");
                    }
                });

                $("#curso").change(function() {
                    var $this = $(this);
                    if ($this.val() != "-1") {
                        $.post('rh_transferencia.php', {funcao: $this.val(), method: "carregahorario"}, function(data) {
                            if (data.status == 1) {
                                var hora = "<option value='-1'>« Selecione »</option>\n";
                                for (var i in data.options) {
                                    hora += "<option value='" + i + "'>" + data.options[i] + "</option>\n";
                                }
                                $("#horario").html(hora);
                            }
                        }, "json");
                    }
                });
                
                    //datepicker para o data_transferencia
                       $("#data_transferencia").datepicker();
                
                    //maskedInput para data_transferencia
                        $('#data_transferencia').mask("99/99/9999", {placeholder: ""});
                
            });
        </script>

    </body>
</html>
<!-- A -->
