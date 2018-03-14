<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br/><a href="../login.php">Logar</a>';
    exit;
}

function removeAspas($str) {
    $str = str_replace("'", "", $str);
    return str_replace('"', '', $str);
}

include('../conn.php');
include('../classes/regiao.php');
include('../wfunction.php');
include('../classes/SetorClass.php');
include('../classes/PlanoSaudeClass.php');
require_once ("../classes/LogClass.php");
include(ROOT_CLASS . 'RhClass.php');


//$rh = new RhClass();
//$rh->AddClassExt('Clt');
#X 
$log = new Log();
$setorObj = new SetorClass();
$objPlanoSaude = new PlanoSaudeClass();

$usuario = carregaUsuario();


$objPlanoSaude->getPlanoSaude();
$arrayPlanoSaude[''] = '--Selecione o Plano de Saúde--';
while ($objPlanoSaude->getRowPlanoSaude()) {
    $arrayPlanoSaude[$objPlanoSaude->getIdPlanoSaude()] = $objPlanoSaude->getRazao();
}


$query_dados_tipo_de_dependente = mysql_query("SELECT * FROM tipo_dependente");
$select_dependentes = "";
while ($rows_tipos_dependentes = mysql_fetch_assoc($query_dados_tipo_de_dependente)) {
    $select_dependentes .= "<option value='{$rows_tipos_dependentes['id_tipo_dependente']}' > {$rows_tipos_dependentes['tipo_dependente']}</option>";
}


$sqlBancos = mysql_query("SELECT * FROM listabancos WHERE status_reg = 1");
$arrayBancos[''] = '--Selecione o Banco--';
while ($rowBancos = mysql_fetch_assoc($sqlBancos)) {
    $arrayBancos[$rowBancos['id_lista']] = $rowBancos['banco'];
}

if (isset($_REQUEST['change_unidade'])) {
    $qr_unidades = mysql_query("SELECT * FROM unidade WHERE id_regiao = {$_REQUEST['change_unidade']}");
    $verifica_unidade = mysql_num_rows($qr_unidades);
    while ($row_unidade_nova = mysql_fetch_assoc($qr_unidades)) {
        echo "<option value='{$row_unidade_nova['id_unidade']}'>" . utf8_encode($row_unidade_nova['unidade']) . "</option>";
    }exit();
}

//$qr_curso = mysql_query("SELECT * FROM curso WHERE id_regiao = '{$usuario['id_regiao']}' AND campo3 = '{$_REQUEST['pro']}' AND status = '1' AND status_reg = '1' ORDER BY nome ASC");
$qr_curso = mysql_query("SELECT * FROM curso WHERE id_regiao = '1' AND campo3 = '1' AND status = '1' AND status_reg = '1' ORDER BY nome ASC");
while ($row_curso = mysql_fetch_assoc($qr_curso)) {
//    print_array($row_curso);
//    $arrayCursosNovo[$row_curso['cod_websaass']]['nome'] = $row_curso['nome'];
//    $arrayCursosNovo[$row_curso['cod_websaass']][$row_curso['letra']][$row_curso['numero']] = $row_curso;
    $arrayCursosNovo[$row_curso['nome']][$row_curso['letra']][$row_curso['numero']] = $row_curso;
}

$cursoLetras = array("A", "B", "C", "D", "E");
if (count($arrayCursosNovo) > 0) {
    $tabelaFuncoesNova = "<table class='table table-bordered table-condensed text-sm valign-middle'><tr><td>Cargo</td><td class='text-center'>Letra</td><td class='text-center'>1</td><td class='text-center'>2</td><td class='text-center'>3</td><td class='text-center'>4</td><td class='text-center'>5</td></tr>";
    foreach ($arrayCursosNovo as $nome => $value) {
        $tabelaFuncoesNova .= "<tr><td rowspan='" . (count($value)) . "'>" . $nome . '</td>';
        if (!$value['']) {
            foreach ($cursoLetras as $letra) {
                if ($value[$letra]) {
                    //                $tabelaFuncoesNova .= (count($value) > 1) ? '<tr>' : '';
                    $tabelaFuncoesNova .= "<td class='text-center'>$letra</td>";
                    for ($i = 1; $i <= 5; $i++) {
                        switch ($i) {
                            case 1: $btn_cor = 'default';
                                break;
                            case 2: $btn_cor = 'warning';
                                break;
                            case 3: $btn_cor = 'primary';
                                break;
                            case 4: $btn_cor = 'info';
                                break;
                            case 5: $btn_cor = 'success';
                                break;
                        }
                        if ($value[$letra][$i]) {
                            $tabelaFuncoesNova .= "<td class='text-center'><button type='button' class='btn btn-{$btn_cor} nova_selecao_funcao' data-hora='{$value[$letra][$i]['hora_semana']}' data-id='{$value[$letra][$i]['id_curso']}' data-url='alter_clt.php?clt={$_REQUEST['clt']}&pro={$_REQUEST['pro']}&pagina=/intranet/rh/ver_clt.php&idcursos={$value[$letra][$i]['id_curso']}'>" . number_format($value[$letra][$i]['valor'], 2, ',', '.') . "</button></td>";
                        } else {
                            $tabelaFuncoesNova .= "<td></td>";
                        }
                    }
                    $tabelaFuncoesNova .= '</tr>';
                }
            }
        } else {
            $tabelaFuncoesNova .= "<td class='text-center'><button type='button' class='btn btn-default nova_selecao_funcao' data-hora='{$value['']['']['hora_semana']}' data-id='{$value['']['']['id_curso']}' data-url='alter_clt.php?clt={$_REQUEST['clt']}&pro={$_REQUEST['pro']}&pagina=/intranet/rh/ver_clt.php&idcursos={$value[$letra][$i]['id_curso']}'>" . number_format($value['']['']['valor'], 2, ',', '.') . "</button></td><td colspan='5'></td>";
        }

        $tabelaFuncoesNova .= '</tr>';
    }
    $tabelaFuncoesNova .= '<table>';
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'valLinha') {
    $linha = $_REQUEST['linha'];
    $sql = "SELECT valor_tarifa FROM rh_vt_linha WHERE id_vt_linha = '$linha'";
    $query = mysql_query($sql);
    $row = mysql_fetch_array($query);
    echo json_encode($row[0]);
    exit;
}

if (empty($_REQUEST['update'])) {

    $id_projeto = $_REQUEST['pro'];
    $id_clt = $_REQUEST['clt'];
    $result = mysql_query("SELECT A.*, date_format(A.data_nasci, '%d/%m/%Y') AS data_nascimento, 
                            date_format(A.data_rg, '%d/%m/%Y') AS data_rg2, 
                            date_format(A.data_escola, '%d/%m/%Y') AS data_escola2, 
                            date_format(A.data_entrada, '%d/%m/%Y') AS data_entrada2, 
                            date_format(A.data_exame, '%d/%m/%Y') AS data_exame, 
                            date_format(A.data_saida, '%d/%m/%Y') AS data_saida, 
                            date_format(A.data_ctps, '%d/%m/%Y') AS data_ctps2, 
                            date_format(A.data_nasc_pai, '%d/%m/%Y') AS data_nasc_pai, 
                            date_format(A.data_nasc_mae, '%d/%m/%Y') AS data_nasc_mae, 
                            date_format(A.data_nasc_conjuge, '%d/%m/%Y') AS data_nasc_conjuge, 
                            date_format(A.data_nasc_avo_h, '%d/%m/%Y') AS data_nasc_avo_h, 
                            date_format(A.data_nasc_avo_m, '%d/%m/%Y') AS data_nasc_avo_m, 
                            date_format(A.data_nasc_bisavo_h, '%d/%m/%Y') AS data_nasc_bisavo_h, 
                            date_format(A.data_nasc_bisavo_m, '%d/%m/%Y') AS data_nasc_bisavo_m, 
                            date_format(A.dada_pis, '%d/%m/%Y') AS dada_pis2,
                            date_format(A.data_emissao, '%d/%m/%Y') AS data_emissao, B.pais AS pais_nasc, C.pais AS pais_nacionalidade,
                            date_format(A.dtChegadaPais, '%d/%m/%Y') AS dtChegadaPais
                            FROM rh_clt AS A
                            LEFT JOIN paises AS B ON (A.id_pais_nasc = B.id_pais)
                            LEFT JOIN paises AS C ON (A.id_pais_nacionalidade = C.id_pais)
                            WHERE A.id_clt = '$id_clt'");
    $row = mysql_fetch_array($result);

    $query = "SELECT id_municipio, municipio FROM municipios WHERE sigla = '{$row['uf']}'";
    $result = mysql_query($query);
    $arr_municipios = array();
    while ($row_municipio = mysql_fetch_array($result)) {
        $arr_municipios[] = '(' . $row_municipio['id_municipio'] . ')- ' . utf8_encode($row_municipio['municipio']);
    }

    $qr_nacionalidade = mysql_query("select * from cod_pais_rais");

    // VALE TRANSPORTE
    if ($row['transporte'] == "1") {
        $result_vale = mysql_query("SELECT A.*, B.valor_tarifa AS val1, C.valor_tarifa AS val2, D.valor_tarifa AS val3, E.valor_tarifa AS val4, F.valor_tarifa AS val5
            FROM rh_vt_valores_assoc AS A
            LEFT JOIN rh_vt_linha AS B ON(A.id_linha1 = B.id_vt_linha)
            LEFT JOIN rh_vt_linha AS C ON(A.id_linha2 = C.id_vt_linha)
            LEFT JOIN rh_vt_linha AS D ON(A.id_linha3 = D.id_vt_linha)
            LEFT JOIN rh_vt_linha AS E ON(A.id_linha4 = E.id_vt_linha)
            LEFT JOIN rh_vt_linha AS F ON(A.id_linha5 = F.id_vt_linha)
            WHERE A.id_clt = '{$id_clt}' AND A.status_reg = 1");
        $row_vale = mysql_fetch_array($result_vale);
        echo '<!--';
        echo $row_vale['id_linha1'];
        echo '-->';
    } else {
        $result_vale = mysql_query("SELECT * FROM rh_vt_valores_assoc WHERE id_clt = '$id_clt' AND status_reg = 1");
        $row_vale_num = mysql_num_rows($result_vale);
        if (!empty($row_vale_num)) {
            mysql_query("UPDATE rh_vt_valores_assoc SET status_reg = 0 WHERE id_clt = '$id_clt'") or die("Erro em Zerando os Dados do Vale: " . mysql_error());
            $log->log('2', "Todos os VT's do CLT $id_clt foram removidos", 'rh_vt_valores_assoc');
        }
    }

    if ($_COOKIE['logado'] == 353) {
        print_array($row_vale);
    }

    if ($row['transporte'] == '1') {
        $chek2 = 'checked';
        $disable_vale = NULL;
    } else {
        $chek2 = NULL;
        $disable_vale = 'display:none;';
    }

    /**
     * PENSAO ALIMENTICIA 
     */
    if ($row['pensao_alimenticia'] == '1') {
        $chekPensao = 'checked';
        $disable_pensao = NULL;
    } else {
        $chekPensao = NULL;
        $disable_pensao = 'style="display:none;"';
    }

// DEPENDENTES
    if (!empty($row['id_antigo'])) {
        $referencia = $row['id_antigo'];
    } else {
        $referencia = $row['id_clt'];
    }
    $result_depe = mysql_query("SELECT *, date_format(data1, '%d/%m/%Y') AS datas1,
                                                                          date_format(data2, '%d/%m/%Y') AS datas2,
                                                                          date_format(data3, '%d/%m/%Y') AS datas3,
                                                                          date_format(data4, '%d/%m/%Y') AS datas4,
                                                                          date_format(data5, '%d/%m/%Y') AS datas5,
                                                                          date_format(data6, '%d/%m/%Y') AS datas6
                                                             FROM dependentes WHERE id_bolsista = '$referencia' AND id_projeto = '$id_projeto' AND contratacao = '$row[tipo_contratacao]'");

    if ($_COOKIE['logado'] == 354) {
        echo "SELECT *, date_format(data1, '%d/%m/%Y') AS datas1,
                                                                          date_format(data2, '%d/%m/%Y') AS datas2,
                                                                          date_format(data3, '%d/%m/%Y') AS datas3,
                                                                          date_format(data4, '%d/%m/%Y') AS datas4,
                                                                          date_format(data5, '%d/%m/%Y') AS datas5,
                                                                          date_format(data6, '%d/%m/%Y') AS datas6
                                                             FROM dependentes WHERE id_bolsista = '$referencia' AND id_projeto = '$id_projeto' AND contratacao = '$row[tipo_contratacao]'";
    }

    $row_depe = mysql_fetch_array($result_depe);

//    if($_COOKIE['debug'] == 666){
//        echo '////////////////Dependentes ($row_depe)//////////////';
//        print_array($row_depe);
//    }
    $checked_pai = ($row_depe['ddir_pai'] == 1) ? 'checked="checked"' : '';
    $checked_mae = ($row_depe['ddir_mae'] == 1) ? 'checked="checked"' : '';
    $checked_conjuge = ($row_depe['ddir_conjuge'] == 1) ? 'checked="checked"' : '';
    $checked_avo_h = ($row_depe['ddir_avo_h'] == 1) ? 'checked="checked"' : '';
    $checked_avo_m = ($row_depe['ddir_avo_m'] == 1) ? 'checked="checked"' : '';
    $checked_bisavo_h = ($row_depe['ddir_bisavo_h'] == 1) ? 'checked="checked"' : '';
    $checked_bisavo_m = ($row_depe['ddir_bisavo_m'] == 1) ? 'checked="checked"' : '';
    $checked_portador1 = ($row_depe['portador_def1'] == 1) ? 'checked="checked"' : '';
    $checked_portador2 = ($row_depe['portador_def2'] == 1) ? 'checked="checked"' : '';
    $checked_portador3 = ($row_depe['portador_def3'] == 1) ? 'checked="checked"' : '';
    $checked_portador4 = ($row_depe['portador_def4'] == 1) ? 'checked="checked"' : '';
    $checked_portador5 = ($row_depe['portador_def5'] == 1) ? 'checked="checked"' : '';
    $checked_portador6 = ($row_depe['portador_def6'] == 1) ? 'checked="checked"' : '';

    $nao_ir_filho1 = ($row_depe['nao_ir_filho1'] == 1) ? 'checked="checked"' : '';
    $nao_ir_filho2 = ($row_depe['nao_ir_filho2'] == 1) ? 'checked="checked"' : '';
    $nao_ir_filho3 = ($row_depe['nao_ir_filho3'] == 1) ? 'checked="checked"' : '';
    $nao_ir_filho4 = ($row_depe['nao_ir_filho4'] == 1) ? 'checked="checked"' : '';
    $nao_ir_filho5 = ($row_depe['nao_ir_filho5'] == 1) ? 'checked="checked"' : '';
    $nao_ir_filho6 = ($row_depe['nao_ir_filho6'] == 1) ? 'checked="checked"' : '';

    $possui_guarda_filho1 = ($row_depe['possui_guarda1'] == 1) ? 'checked="checked"' : '';
    $possui_guarda_filho2 = ($row_depe['possui_guarda2'] == 1) ? 'checked="checked"' : '';
    $possui_guarda_filho3 = ($row_depe['possui_guarda3'] == 1) ? 'checked="checked"' : '';
    $possui_guarda_filho4 = ($row_depe['possui_guarda4'] == 1) ? 'checked="checked"' : '';
    $possui_guarda_filho5 = ($row_depe['possui_guarda5'] == 1) ? 'checked="checked"' : '';
    $possui_guarda_filho6 = ($row_depe['possui_guarda6'] == 1) ? 'checked="checked"' : '';

    $checked_dep1_cur_fac_ou_tec = ($row_depe['dep1_cur_fac_ou_tec'] == 1) ? 'checked="checked"' : '';
    $checked_dep2_cur_fac_ou_tec = ($row_depe['dep2_cur_fac_ou_tec'] == 1) ? 'checked="checked"' : '';
    $checked_dep3_cur_fac_ou_tec = ($row_depe['dep3_cur_fac_ou_tec'] == 1) ? 'checked="checked"' : '';
    $checked_dep4_cur_fac_ou_tec = ($row_depe['dep4_cur_fac_ou_tec'] == 1) ? 'checked="checked"' : '';
    $checked_dep5_cur_fac_ou_tec = ($row_depe['dep5_cur_fac_ou_tec'] == 1) ? 'checked="checked"' : '';
    $checked_dep6_cur_fac_ou_tec = ($row_depe['dep6_cur_fac_ou_tec'] == 1) ? 'checked="checked"' : '';




    $result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = $id_projeto");
    $row_pro = mysql_fetch_array($result_pro);
    $result_reg = mysql_query("SELECT * FROM regioes WHERE id_regiao = $row[id_regiao]");
    $row_reg = mysql_fetch_array($result_reg);
    if ($row['insalubridade'] == "1") {
        $chek1 = "checked";
    } else {
        $chek1 = NULL;
    }
    if ($row['vr'] == "1") {
        $chek3 = "checked";
        $disable_vr = "style='display:'";
    } else {
        $chek3 = NULL;
        $disable_vr = "style='display:none'";
    }

    if ($row['assinatura'] == "1") {
        $selected_ass_sim = "checked";
        $selected_ass_nao = NULL;
    } elseif ($row['assinatura'] == "0") {
        $selected_ass_sim = NULL;
        $selected_ass_nao = "checked";
    } else {
        $selected_ass_sim = NULL;
        $selected_ass_nao = NULL;
        $mensagem_ass = "<font color=red size=1><b>Não marcado</b></font>";
    }

    if ($row['distrato'] == "1") {
        $selected_ass_sim2 = "checked";
        $selected_ass_nao2 = NULL;
    } elseif ($row['distrato'] == "0") {
        $selected_ass_sim2 = NULL;
        $selected_ass_nao2 = "checked";
    }

    if ($row['outros'] == "1") {
        $selected_ass_sim3 = "checked";
        $selected_ass_nao3 = NULL;
    } elseif ($row['outros'] == "0") {
        $selected_ass_sim3 = NULL;
        $selected_ass_nao3 = "checked";
    }

    if ($row['sexo'] == "M") {
        $chekH = "checked";
        $chekF = NULL;
        $mensagem_sexo = NULL;
    } elseif ($row['sexo'] == "F") {
        $chekH = NULL;
        $chekF = "checked";
        $mensagem_sexo = NULL;
    } else {
        $chekH = NULL;
        $chekF = NULL;
        $mensagem_sexo = "<font color=red size=1><b>Cadastrar Sexo</b></font>";
    }

    $displayPlanoSaude = ' style="display:none;" ';
    if ($row['medica'] == "0") {
        $chek_medi0 = "checked";
        $chek_medi1 = NULL;
        $mensagem_medi = NULL;
    } elseif ($row['medica'] == "1") {
        $chek_medi0 = NULL;
        $chek_medi1 = "checked";
        $mensagem_medi = NULL;
        $displayPlanoSaude = '';
    } else {
        $chek_medi0 = NULL;
        $chek_medi1 = NULL;
        $mensagem_medi = "<font color=red size=1><b>Selecione uma opção</b></font>";
    }

    if ($row['plano'] == "1") {
        $selected_planoF = "selected";
        $selected_planoI = NULL;
    } else {
        $selected_planoF = NULL;
        $selected_planoI = "selected";
    }

    if ($row['ad_noturno'] == "1") {
        $checkad_noturno1 = "checked";
        $checkad_noturno0 = NULL;
    } else {
        $checkad_noturno1 = NULL;
        $checkad_noturno0 = "checked";
    }

    if ($row['estuda'] == "sim") {
        $chekS = "checked";
        $chekN = NULL;
    } else {
        $chekS = NULL;
        $chekN = "checked";
    }

    if ($row['cipa'] == "1") {
        $checkedcipa1 = "checked";
        $checkedcipa0 = NULL;
    } else {
        $checkedcipa1 = NULL;
        $checkedcipa0 = "checked";
    }

    if ($row['status'] == "10" or $row['status'] == "1") {
        $AVISO = NULL;
        $status_ativado = "checked";
        $status_desativado = NULL;
        $data_desativacao = NULL;
    } else {
        $AVISO = "Este Funcionário Encontra-se DESATIVADO";
        $status_ativado = NULL;
        $status_desativado = "checked";
        $data_desativacao = "$row[data_saida]";
    }

    if ($row['foto'] == "1") {
        $foto = "Deseja remover a foto? <input name='foto' type='checkbox' id='foto' value='3'/> Sim";
    } else {
        $foto = "<input class='reset' name='foto' type='checkbox' id='foto' value='1' onClick=\"document.getElementById('tablearquivo').style.display = (document.getElementById('tablearquivo').style.display == 'none') ? '' : 'none' ;\">";
    }

    // Consulta para mostrar qual o sindicato atual do funcionário 
    $result_sindicatotb_tb_rh_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$id_clt'");
    $row_sindicato_tb_rh_clt = mysql_fetch_array($result_sindicatotb_tb_rh_clt);
// Vinculo da tabela rh_clt com a tabela rhsindicato
    $vinculo_tb_clt_com_rhsindicato = $row_sindicato_tb_rh_clt['rh_sindicato'];
    $result_sindicato = mysql_query("SELECT * FROM rhsindicato WHERE id_sindicato = '$vinculo_tb_clt_com_rhsindicato'");
    $row_sindicato = mysql_fetch_array($result_sindicato);
// Variárel com o "valor" da primeira opção do selet "Selecinar" sindicato
    $sindicato = $row_sindicato['nome'];
// Variárel com o "id" da primeira opção do selet "Selecinar" sindicato
    $sindicato_value = $row_sindicato_tb_rh_clt['rh_sindicato'];
// Este trecho de código marca automaticamente no fomulário "Possui sindicato" se o usuário possui sindicato Sim ou não.
    if (!empty($sindicato)) {
        $checked_sim = 'checked';
        $checked_nao = NULL;
        $statusBotao = NULL;
    } else {
        $checked_nao = 'checked';
        $statusBotao = 'none';
    }

    $checkbox_chefe_equipe = '';
    $select_escolha_gratificacao = '';
    $valor_chefe_equipe = $row_sindicato_tb_rh_clt['valor_chefe_equipe'];
    $valor_cargo_confianca = $row_sindicato_tb_rh_clt['valor_cargo_confianca'];
    $ajuda_custo = $row_sindicato_tb_rh_clt['ajuda_custo'];
    $produtividade_percent_fixo = $row_sindicato_tb_rh_clt['produtividade_percent_fixo'];
    $unidocencia = $row_sindicato_tb_rh_clt['unidocencia'];
    $escolha_gratificacao = $row_sindicato_tb_rh_clt['escolha_gratificacao'];

    if (!empty($valor_chefe_equipe) && $valor_chefe_equipe != 0) {

        $checkbox_chefe_equipe = 'checked';
    }

    if (!empty($valor_cargo_confianca) && $valor_cargo_confianca != 0) {

        $checkbox_cargo_confianca = 'checked';
    }

    if (!empty($ajuda_custo) && $ajuda_custo != 0) {

        $checkbox_ajuda_custo = 'checked';
    }

    if (!empty($produtividade_percent_fixo) && $produtividade_percent_fixo != 0) {

        $checkbox_percent_fixo = 'checked';
    }

    if (!empty($unidocencia) && $unidocencia != 0) {

        $checkbox_unidocencia = 'checked';
    }

    if (!empty($escolha_gratificacao) && $escolha_gratificacao != 0) {

        $select_escolha_gratificacao = 'selected';
    }

// Habilita ou desabilita o formulário "Selecionar" Sindicato
    if ($row_sindicato_tb_rh_clt['rh_sindicato'] == '0') {
        $visualizacao = "style=display:none";
    } else {
        $visualizacao = NULL;
    }

    $RE_pg_dep = mysql_query("SELECT id_tipopg FROM tipopg WHERE id_projeto = '$id_projeto' AND campo1 = '1'");
    $Row_pg_dep = mysql_fetch_array($RE_pg_dep);
    $RE_pg_che = mysql_query("SELECT id_tipopg FROM tipopg WHERE id_projeto = '$id_projeto' AND campo1 = '2'");
    $Row_pg_che = mysql_fetch_array($RE_pg_che);

// Log
//    $qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
//    $funcionario = mysql_fetch_array($qr_funcionario);
//    $ip = $_SERVER['REMOTE_ADDR'];
//    $local_banco = "Edição de CLT";
//    $acao_banco = "Editando o CLT ($row[campo3]) $row[nome]";
//
//    mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) 
//VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local_banco', NOW(), '$ip', '$acao_banco')") or die("Erro Inesperado<br/><br/>" . mysql_error());
// Fim do Log
    $pagina = $_REQUEST['pagina'];


///VERIFICANDO SE O CLT ESTA EM ALGUMA FOLHA
    $verifica_folha = mysql_num_rows(mysql_query("SELECT * FROM rh_folha as A 
                                    INNER JOIN rh_folha_proc as B
                                    ON B.id_folha = A.id_folha
                                    WHERE B.id_clt = $id_clt AND B.status IN(2, 3)"));


    //SELECIONA TODOS OS TIPOS DE ADMISSAO
    $tiposAdmi = montaQuery("rhstatus_admi", "*");
    $arrayTipoAdmi = array("" => "« Selecione o tipo de admissão »");
    foreach ($tiposAdmi as $tipoAdmi) {
        $arrayTipoAdmi[$tipoAdmi['id_status_admi']] = $tipoAdmi['codigo'] . " - " . $tipoAdmi['especifica'];
    }

    /**
     * VERIFICANDO FAVORECIDO CADASTRADO
     */
    $dadosFavorec = array();
    $ver_favorec = mysql_query("SELECT * FROM entradaesaida_nomes WHERE id_clt = '{$id_clt}'");
    while ($row_favorecido = mysql_fetch_assoc($ver_favorec)) {
        $dadosFavorec['nome'] = $row_favorecido['nome'];
        $dadosFavorec['cpf'] = $row_favorecido['cpfcnpj'];
    }


    /**
     * AÇÕES DE PENSAO ALIMENTICIA
     */
    if (isset($_REQUEST['method']) && !empty($_REQUEST['method'])) {
        if ($_REQUEST['method'] == "removerFavorecido") {
            $retorno = array("status" => 0);

            $query = mysql_query("UPDATE favorecido_pensao_assoc SET status_reg = 0 WHERE id = '{$_REQUEST['id']}'") or die('Erro ao remover favorecido');
            if ($query) {
                $retorno = array("status" => 1);
            }

            echo json_encode($retorno);
            exit();
        }

        if ($_REQUEST['method'] == "removendoFavorecidos") {
            $retorno = array("status" => 0);
            $query = mysql_query("UPDATE favorecido_pensao_assoc SET status_reg = 0 WHERE  id_clt = '{$_REQUEST['id_clt']}'") or die('Erro ao remover favorecido');
            if ($query) {
                $retorno = array("status" => 1);
            }

            echo json_encode($retorno);
            exit();
        }
    }

    // INSS outras empresas ----------------------------------------------------
    $query_inss = "SELECT * FROM rh_inss_outras_empresas WHERE id_clt= $id_clt AND status = 1";
    $result_inss = mysql_query($query_inss);
    while ($row_inss = mysql_fetch_array($result_inss)) {
        $arr_inss[] = $row_inss;
    }

    if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'excluir_inss') {
        $antigo = $log->getLinha('rh_inss_outras_empresas', $_REQUEST['id']);
        $query = "UPDATE rh_inss_outras_empresas SET status = 0 WHERE id_inss = {$_REQUEST['id']}";

        if (mysql_query($query)) {
            $novo = $log->getLinha('rh_inss_outras_empresas', $_REQUEST['id']);
            echo json_encode(array('status' => 1, 'msg' => 'Excluido com sucesso!'));
            $log->log('2', "INSS outra empresa ID {$_REQUEST['id']} do CLT $id_clt foi removido", 'rh_inss_outras_empresas', $antigo, $novo);
        } else {
            echo json_encode(array('status' => 0, 'msg' => 'Erro ao Excluir!'));
        }
        exit();
    }

    // INSS outras empresas ----------------------------------------------------
    // listando unidades -------------------------------------------------------

    $query_uni = "SELECT * FROM unidade WHERE id_regiao = {$row['id_regiao']}";

    $result_uni = mysql_query($query_uni);
    $arr_unidade[''] = 'Selecione';
    while ($row_uni = mysql_fetch_assoc($result_uni)) {
        $arr_unidade[$row_uni['id_unidade']] = $row_uni['unidade'];
    }

    $query_uni_assoc = "SELECT A.*, B.id_regiao FROM rh_clt_unidades_assoc AS A LEFT JOIN unidade AS B ON(B.id_unidade = A.id_unidade) WHERE id_clt = '$id_clt' AND status = 1;";
    $result_uni_assoc = mysql_query($query_uni_assoc);
    while ($row_uni_assoc = mysql_fetch_assoc($result_uni_assoc)) {
        $unidade_assoc[] = $row_uni_assoc;
    }
//    echo '<pre>';
//    print_r($unidade_assoc);
//    echo '</pre>';
    // listando unidades -------------------------------------------------------
    // listando regiao -------------------------------------------------------
    $query_regiao = "SELECT * FROM regioes";
    $result_regiao = mysql_query($query_regiao);
    // listando regiao -------------------------------------------------------

    $regUnid = $row['id_regiao'];

    if ($regUnid != '1' && count($unidade_assoc) == 2 && $unidade_assoc[1]['id_regiao'] != $regUnid) {

        if ($unidade_assoc[1]['id_regiao'] != $regUnid) {
            $unidade_assoc[2] = $unidade_assoc[1];
            $unidade_assoc[1] = array();
        }
    }
    $setorObj->getSetor();
    $arraySetor[''] = '--Selecione o Setor--';
    $sqlSetor = mysql_query("SELECT * FROM setor WHERE id_unidade = {$row['id_unidade']}");
    while ($rowSetor = mysql_fetch_assoc($sqlSetor)) {
        $arraySetor[$rowSetor['id_setor']] = $rowSetor['nome'];
    }
    ?>

    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <title>:: Intranet ::</title>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
            <link rel="shortcut icon" href="../favicon.ico"/>
            <link rel="stylesheet" href="css/estrutura_cadastro.css" type="text/css"/>
            <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">

                <script language="javascript" src="../js/ramon.js"></script>
                <script type="text/javascript" src="../js/valida_documento.js"></script>

                <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
                <script type="text/javascript" src="../js/jquery-1.8.3.min.js"></script>
                <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
                <!--<script type="text/javascript" src="../js/jquery.ui.datepicker-pt-BR.js"></script>-->
                <script type="text/javascript" src="../jquery/priceFormat.js"></script>
                <script type="text/javascript" src="../js/valida_documento.js"></script>
                <script type="text/javascript" src="../js/jquery.maskedinput.min.js"></script>
                <script type="text/javascript" src="../js/jquery.maskMoney_3.0.2.js"></script>
                <script src="../js/jquery.validationEngine-2.6.js"></script>
                <script src="../js/jquery.validationEngine-pt.js"></script>
                <script src="../js/global.js" type="text/javascript"></script>

                <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen"/>
                <!--<link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">-->
                <!--<link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">-->
                <!--<link href="../resources/css/add-ons.min.css" rel="stylesheet" media="screen">-->
                <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen"/>
                <link href="../resources/css/main.css" rel="stylesheet" media="screen"/>
                <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen"/>
                <!--<link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">-->
                <script src="../resources/js/bootstrap.min.js"></script>
                <script src="../resources/js/bootstrap-dialog.min.js"></script>
                <script src="../resources/js/main.js"></script>

                <script src="../resources/js/moment.js"></script>
                <link href="css/estrutura_cadastro.css" rel="stylesheet" type="text/css"/>
                <script type="text/javascript">
                    /**
                     * Função que converte para maiúscula
                     * 
                     * @access public
                     * @method up
                     * @param
                     * 
                     * @return void
                     */
                    function up(lstr) { // converte minusculas em maiusculas

                        var str = lstr.value; //obtem o valor 
                        lstr.value = str.toUpperCase(); //converte as strings e retorna ao campo

                    }

                    /**
                     * Função que converte para minúscula
                     * 
                     * @access public
                     * @method down
                     * @param
                     * 
                     * @return void
                     */

                    function down(lstr) { // converte maiusculas em minusculas 

                        var str = lstr.value; //obtem o valor 
                        lstr.value = str.toLowerCase(); //converte as strings e retorna ao campo

                    }
                    /*
                     * Função com mascara para telefone
                     * Autor: Leonardo
                     * data: 30/04/2014
                     * @returns {undefined}
                     */
                    jQuery.fn.brTelMask = function () {

                        return this.each(function () {
                            var el = this;
                            $(el).focus(function () {
                                $(el).mask("(99) 9999-9999?9", {placeholder: " "});
                            });

                            $(el).focusout(function () {
                                var phone, element;
                                element = $(el);
                                element.unmask();
                                phone = element.val().replace(/\D/g, '');
                                if (phone.length > 10) {
                                    element.mask("(99) 99999-999?9");
                                } else {
                                    element.mask("(99) 9999-9999?9");
                                }
                            });
                        });
                    };


                    /*
                     * Função para validar CPF
                     * Autor: Leonardo
                     * data: 30/04/2014
                     * @param {type} field
                     * @returns {String}
                     */
                    var verificaCPF = function (field) {

                        var value = field.val();

                        value = value.replace('.', '');
                        value = value.replace('.', '');
                        var cpf = value.replace('-', '');

                        if (!VerificaCPF(cpf)) {
                            return "CPF inválido";
                        }
                    };

                    /*
                     * Função para validar PIS
                     * Autor: Leonardo
                     * data: 30/04/2014
                     * @param {type} field
                     * @returns {String}
                     */
                    var verificaPIS = function (field) {
                        var value = field.val();

                        value = value.replace('.', '');
                        value = value.replace('.', '');
                        var pis = value.replace('-', '');
                        if (ChecaPIS(pis) == false) {
                            return 'PIS inválido';
                        }
                    };

                    $(document).ready(function () {

                        $('body').on('change', '#ad_transferencia_tipo', function () {

                            var t = $(this);
                            var ad_transferencia_tipo = t.val();

                            if (ad_transferencia_tipo == 1) {
                                $('.box_ad_transferencia_valor').removeClass('hide');
                            } else {
                                $('.box_ad_transferencia_valor').addClass('hide');
                                $('#ad_transferencia_valor').val(0);
                            }

                        });

                        $('body').on('click', '#btn-funcoes', function () {

                            var msg = "<?= $tabelaFuncoesNova ?>";

                            new BootstrapDialog({
                                nl2br: false,
                                type: 'type-primary',
                                title: 'PLANO DE CARGOS E SALÁRIOS COMPLETO',
                                message: msg,
                                size: BootstrapDialog.SIZE_WIDE,
                                closable: true
                                        //                                ,buttons: [{
                                        //                                        label: 'OK',
                                        //                                        action: function (dialog) {
                                        //                                            typeof dialog.getData('callback') === 'function' && dialog.getData('callback')(true);
                                        //                                            dialog.close();
                                        //                                        }
                                        //                                    }]
                            }).open();
                        });

                        $('body').on('click', '.nova_selecao_funcao', function () {
                            //                            console.log($(this).data('url'));
                            cria_carregando_modal();
                            $('#id_curso').val($(this).data('id')).trigger('change');
                        });


                        //                        $("#data_entrada").datepicker({minDate: new Date(2009, 1 - 1, 1)});
                        //                        $("#data_entrada").datepicker({showMonthAfterYear: true});
                        // máscaras
                        $("#cpf").mask("999.999.999-99", {placeholder: " "});
                        $("#cpf_1").mask("999.999.999-99", {placeholder: " "});
                        $("#cpf_2").mask("999.999.999-99", {placeholder: " "});
                        $("#cpf_3").mask("999.999.999-99", {placeholder: " "});
                        $("#cpf_4").mask("999.999.999-99", {placeholder: " "});
                        $("#cpf_5").mask("999.999.999-99", {placeholder: " "});
                        $("#cpf_6").mask("999.999.999-99", {placeholder: " "});
                        //                    $("#rg").mask("99.999.999-9", {placeholder: " "});
                        $("#cep").mask("99999-999", {placeholder: " "});
                        $(".tel").brTelMask();

                        $("#uf_nasc_text").hide();

                        $("#nacionalidade").change(function () {
                            if ($("#nacionalidade").val() != '10')
                            {
                                $("#uf_nasc_select").hide();
                                $("#uf_nasc_text").show();
                            } else
                            {
                                $("#uf_nasc_text").hide();
                                $("#uf_nasc_select").show();
                            }
                        });

                        /*
                         *    INÍCIO DAS ALTERAÇÕES FEITAS POR PV PARA  BUSCA CEP E E-SOCIAL
                         */

                        /* carrega municípios para o campo cidade */
                        $("#cidade").autocomplete({source: <?= json_encode($arr_municipios) ?>,
                            change: function (event, ui) {
                                if (event.type == 'autocompletechange') {
                                    var valor_municipio = ui.item.value.split(')-');
                                    $('#cod_cidade').val(valor_municipio[0].trim().substring(1, 5));
                                    $('#cidade').val(valor_municipio[1].trim());
                                }
                            }
                        });
                        /* carrega municípios para o campo cidade nascimento */
                        $("#municipio_nasc").autocomplete({source: <?= json_encode($arr_municipios) ?>,
                            change: function (event, ui) {
                                if (event.type == 'autocompletechange') {
                                    var valor_municipio = ui.item.value.split(')-');
                                    $('#cod_municipio_nasc').val(valor_municipio[0].trim().substring(1, 5));
                                    $('#municipio_nasc').val(valor_municipio[1].trim());
                                }
                            }
                        });
                        $('#uf').change(function () {
                            var uf = $('#uf').val();
                            $('#cidade').val('');
                            $('#cod_cidade').val('');
                            $.post('../busca_cep.php', {uf: uf, municipios: 1}, function (data) {

                                $("#cidade").autocomplete({source: data.municipios
                                            //                                    , 
                                            //                                    change: function( event, ui ) {
                                            //                                        if(event.type=='autocompletechange'){
                                            //                                            var valor_municipio = ui.item.value.split('-');
                                            //                                            $('#cod_cidade').val(valor_municipio[0].trim());
                                            //                                            $('#cidade').val(valor_municipio[1].trim());
                                            //                                        }
                                            //                                    } 
                                });

                            }, 'json');
                        });
                        /* carrega municípios para o campo município de nascimento */
                        $('#uf_nasc_select').change(function () {
                            var uf = $('#uf_nasc_select').val();
                            $('#municipio_nasc').val('');
                            $('#cod_municipio_nasc').val('');
                            $.post('../busca_cep.php', {uf: uf, municipios: 1}, function (data) {

                                $("#municipio_nasc").autocomplete({source: data.municipios,
                                    change: function (event, ui) {
                                        if (event.type == 'autocompletechange') {
                                            var valor_municipio = ui.item.value.split(')-');
                                            $('#cod_municipio_nasc').val(valor_municipio[0].trim().substring(1, 5));
                                            $('#municipio_nasc').val(valor_municipio[1].trim());
                                        }
                                    }
                                });

                            }, 'json');
                        });

                        /* busca dados pelo cep */
                        var cep_atual = $('#cep').val().replace("-", "").replace(".", "");
                        var numero_atual = $('#numero').val();
                        var complemento_atual = $('#complemento').val();
                        //INICO VIA CEP
                        //INICIO CEP

                        function limpa_formulario_cep() {
                            // Limpa valores do formulÃ¡rio de cep.
                            $("#endereco").val("");
                            $("#bairro").val("");
                            $("#cidade").val("");
                            $("#uf").val("");
                            //$("#ibge").val("");
                        }
                        //Quando o campo cep perde o foco.
                        $("#cep").blur(function () {
                            //alert('dentro');return false;

                            //Nova variÃ¡vel "cep" somente com dÃ­gitos.
                            var cep = $(this).val().replace(/\D/g, '');

                            //Verifica se campo cep possui valor informado.
                            if (cep != "") {

                                //ExpressÃ£o regular para validar o CEP.
                                var validacep = /^[0-9]{8}$/;

                                //Valida o formato do CEP.
                                if (validacep.test(cep)) {
                                    $('#cod_tp_logradouro').val("...");
                                    $('#endereco').val("...");
                                    $('#bairro').val("...");
                                    $('#uf').val("...");
                                    $('#cidade').val("...");
                                    $('#cod_cidade').val("...");
                                    //Preenche os campos com "..." enquanto consulta webservice.

                                    //Consulta o webservice viacep.com.br/

                                    $.getJSON("//viacep.com.br/ws/" + cep + "/json/?callback=?", function (data) {

                                        if (!("erro" in data)) {
                                            $('#cod_tp_logradouro').val(data.cod_tp_logradouro);
                                            $('#endereco').val(data.logradouro);
                                            $('#bairro').val(data.bairro);
                                            $('#uf').val(data.uf);
                                            $('#cidade').val(data.localidade);
                                            $('#cod_cidade').val(data.id_municipio);
                                        } //end if.
                                        else {
                                            //CEP pesquisado nÃ£o foi encontrado.
                                            limpa_formulario_cep();
                                            //alert("CEP nÃ£o encontrado.");
                                            bootAlert('CEP não encontrado!', 'Alerta', "Null", 'danger');
                                        }
                                    });
                                } //end if.
                                else {
                                    //cep Ã© invÃ¡lido.
                                    limpa_formulario_cep();
                                    bootAlert('Formato de CEP inválido!', 'Alerta', "Null", 'danger');
                                }
                            } //end if.
                            else {
                                //cep sem valor, limpa formulÃ¡rio.
                                limpa_formulario_cep();
                            }
                        });
                        //FIM CEP 
                        //FIM VIA CEP


                        /*
                         $('#cep').blur(function () {
                         
                         $this = $(this);
                         $this.after('<img src="../img_menu_principal/loader_pequeno.gif" alt="buscando endereço..." style="position: absolute; margin-top: -7px;" id="img_load_cep" />');
                         $('#cod_tp_logradouro').attr('disabled', 'disabled');
                         $('#endereco').attr('disabled', 'disabled');
                         $('#bairro').attr('disabled', 'disabled');
                         $('#uf').attr('disabled', 'disabled');
                         $('#cidade').attr('disabled', 'disabled');
                         
                         var cep = $this.val();
                         $.post('../busca_cep.php', {cep: cep, id_municipio: 1, municipios: 1}, function (data) {
                         
                         $('#cod_tp_logradouro').removeAttr('disabled');
                         $('#endereco').removeAttr('disabled');
                         $('#bairro').removeAttr('disabled');
                         $('#uf').removeAttr('disabled');
                         $('#cidade').removeAttr('disabled');
                         $('#img_load_cep').remove();
                         
                         if (data.cep == '') {
                         alert('Cep não encontrado!');
                         } else {
                         $("#cidade").autocomplete({source: data.municipios,
                         change: function (event, ui) {
                         if (event.type == 'autocompletechange') {
                         var valor_municipio = ui.item.value.split(')-');
                         $('#cod_cidade').val(valor_municipio[0].trim().substring(1, 5));
                         $('#cidade').val(valor_municipio[1].trim());
                         }
                         }
                         });
                         $('#cod_tp_logradouro').val(data.cod_tp_logradouro);
                         $('#endereco').val(data.logradouro);
                         $('#bairro').val(data.bairro);
                         $('#uf').val(data.uf);
                         $('#cidade').val(data.cidade);
                         $('#cod_cidade').val(data.id_municipio);
                         
                         if (data.cep == cep_atual) {
                         $('#numero').val(numero_atual);
                         $('#complemento').val(complemento_atual);
                         } else {
                         $('#numero').val('');
                         $('#complemento').val('');
                         }
                         }
                         
                         }, 'json');
                         
                         });*/
                        /*
                         *    FIM DAS ALTERAÇÕES FEITAS POR PV PARA  BUSCA CEP E E-SOCIAL
                         */

                        $('#horario').focusout(function () {
                            if ($('#horario').val() != '') {
                                $('#horas_semanais').val($('#horario option:selected').data('semana'));
                                $('#horas_mes').val($('#horario option:selected').data('mes'));
                            }
                        });


                        $('.formata_valor').priceFormat({
                            prefix: '',
                            centsSeparator: ',',
                            thousandsSeparator: '.'
                        });

                        var tipoVerifica = 0;
                        $("select[name=banco]").change(function () {
                            var valor = $(this).val();
                            if (valor == 0) {
                                desabilita();
                                tipoPgCheque();
                                tipoVerifica = 1;

                            } else if (valor == 9999) {
                                Ativa();
                                tipoPgConta();
                                tipoVerifica = 2;
                            } else if (valor == 111) {
                                Ativa();
                                tipoPgConta();
                                tipoVerifica = 2;
                                $('#id_banco_nacional').val('-1').attr('disabled', true);
                                $("input[name*='conta']").attr("disabled", false).val('');
                                $("input[name*='agencia']").attr("disabled", false).val('');
                            } else if (valor == 106) {
                                Ativa();
                                tipoPgConta();
                                tipoVerifica = 2;
                                $('#id_banco_nacional').val('-1').attr('disabled', true).css('background-color', '#eee');
                                $("input[name*='conta']").attr("disabled", false).val('');
                                $("input[name*='agencia']").attr("disabled", false).val('');
                            } else if (valor == 110) {
                                Ativa();
                                tipoPgConta();
                                tipoVerifica = 2;
                                $('#id_banco_nacional').val('-1').attr('disabled', true);
                                $("input[name*='conta']").attr("disabled", false).val('');
                                $("input[name*='agencia']").attr("disabled", false).val('');
                            } else {
                                Ativa();
                                tipoPgConta();
                                tipoVerifica = 3;
                                $("input[name='nome_banco']").attr("disabled", true);  // comentar quando a situaçao do banco estiver ok
                            }
                        });

                        function desabilita() {

                            $("input[name*='conta']").attr("disabled", true).val('');
                            $("input[type*='radio'][name*='radio_tipo_conta']").attr("disabled", true);
                            $("input[name*='agencia']").attr("disabled", true).val('');
                            //$("input[name='nome_banco']").attr("disabled", true).value('-1');   // comentar quando a situacao do banco estiver ok
                            $('#id_banco_nacional').val('-1').attr('disabled', true).css('background-color', '#eee');

                        }

                        function Ativa() {
                            $("input[name*='conta']").attr("disabled", false);
                            $("input[type*='radio'][name*='radio_tipo_conta']").attr("disabled", false);
                            $("input[name*='agencia']").attr("disabled", false);
                            $('#id_banco_nacional').attr("disabled", false).css('background-color', '#fff');
                        }

                        function tipoPgCheque() {
                            $("select[name='tipopg']").find('option').attr('disabled', false).attr('selected', false);
                            $("select[name='tipopg']").find('option').each(function () {
                                if ($(this).text() == "Cheque") {
                                    $(this).attr('selected', true);
                                } else {
                                    $(this).attr('disabled', true);
                                }

                            });
                        }

                        function tipoPgConta() {
                            $("select[name='tipopg']").find('option').attr('disabled', false).attr('selected', false);
                            $("select[name='tipopg']").find('option').each(function () {
                                if ($(this).text() == "Depósito em Conta Corrente") {
                                    $(this).attr('selected', true);
                                } else {
                                    $(this).attr('disabled', true);
                                }
                            });
                        }


    //                        if($("select[name=banco]").val() == 0) {
    //                            desabilita();
    //                            tipoPgCheque();
    //                            tipoVerifica = 1;
    //                        } else if ($("select[name=banco]").val() == 106) {
    //                            console.log($("select[name=banco]").val());
    //                            $('#id_banco_nacional').val('-1').attr('disabled', true).css('background-color', '#eee');
    //                        }



                        $("input[type*='button'][name*='Submit']").click(function () {
                            var indice = new Array();
                            if (tipoVerifica == 3) {
                                if ($("input[name*='conta']").val() == '') {
                                    indice.push("Conta");
                                }
                                if ($("input[name*='agencia']").val() == '') {
                                    indice.push("Agencia");
                                }
                                indiceRadio = 0;
                                $("input[name*='radio_tipo_conta']").each(function () {
                                    if ($(this).is(':checked')) {
                                        indiceRadio = 1;
                                    }
                                });

                                if (indiceRadio == 0) {
                                    indice.push("tipo de conta");
                                }


                            } else if (tipoVerifica == 2) {
                                if ($("input[name*='conta']").val() == '') {
                                    indice.push("Conta");
                                }
                                if ($("input[name*='agencia']").val() == '') {
                                    indice.push("Agencia");
                                }
                                indiceRadio = 0;
                                $("input[name*='radio_tipo_conta']").each(function () {
                                    if ($(this).is(':checked')) {
                                        indiceRadio = 1;
                                    }
                                });

                                if (indiceRadio == 0) {
                                    indice.push("tipo de conta");
                                }

                                if ($("input[name*='nome_banco']").val() == "") {
                                    indice.push("Nome do banco");
                                }
                            }

                            if (indice.length > 0) {
                                alert("Preencha o(s) dado(s) " + indice.join(', '));
                            } else {
                                $('#form1').submit();
                            }
                        });

                        // instancia o validation engine no formulário
                        $("#form1").validationEngine();
                        // add class do validation engine
                        $("#pis").change(function () {
                            // verifica se o campo não está vazio 
                            if ($("#pis").val() != '') {
                                $("#pis").addClass('validate[required,funcCall[verificaPIS]]'); // adiciona classe
                            } else {
                                $("#pis").removeClass('validate[required,funcCall[verificaPIS]]'); // remove classe
                            }
                        });

                        //Plano de saude
                        $("input[type='radio'][name='medica']").click(function () {
                            var valor = $(this).val();
                            if (valor == 1) { //Adiciona a classe validade
                                $("#planosaude").css('display', '');
                                $("#id_plano_saude").attr('class', "validate[required]");
                            } else {
                                $("#planosaude").css('display', 'none');
                                $("#id_plano_saude").removeAttr("class").val(''); // remove a classe
                            }
                        });

                        //Amanda
                        //Possui sindicato?
                        $("input[type='radio'][name='radio_sindicato']").click(function () {
                            var valor = $(this).val();
                            if (valor === 'sim') { //Adiciona a classe validade
                                $("#trsindicato").css('display', '');
                                $("#sindicato").attr('class', "validate[required]");
                            } else {
                                $("#trsindicato").css('display', 'none');
                                $("#sindicato").removeAttr("class").val(''); // remove a classe
                            }
                        });

                        //Isento de Contribuição?
                        $("input[type='radio'][name='radio_contribuicao']").click(function () {
                            var valor = $(this).val();
                            if (valor === 'sim') {//Adiciona a classe validade
                                $("#trcontribuicao").css('display', '');
                                $("#ano_contribuicao").attr('class', "validate[required]");
                            } else {
                                $("#trcontribuicao").css('display', 'none');
                                $("#ano_contribuicao").removeAttr("class").val('');// remove a classe

                            }
                        });
                        //FIM

                        //                        if (<?= $row['cod_pais_rais'] ?> == 10) {
                        //                            $('#ano-chegada').hide();
                        //                        } else {
                        //                            $('#ano-chegada').show();
                        //                        }

                        $('#nacionalidade').change(function () {
                            var valor = $(this).val();
                            if (valor == 10) {
                                $('#ano-chegada').hide();
                                $("#pais_nacionalidade").val('Brasil');
                                $("#cod_pais_nacionalidade").val('1');
                                $("#pais_nasc").val('Brasil');
                                $("#cod_pais_nasc").val('1');
                                //                                $('.pais').removeAttr('value');
                                //                                $( "input[name^='cod_pais_']").removeAttr('value');
                                $("#ano_chegada_pais").removeAttr('value');
                            } else {
                                //                                $('.pais').removeAttr('value');
                                //                                $("input[name^='cod_pais_']").removeAttr('value');
                                $('#ano-chegada').show();
                                $(".pais").focus(function () {
                                    var tipo = "#" + $(this).data('tipo');
                                    $.post('../methods.php', {method: 'carregaPais'}, function (data) {
                                        $(tipo).autocomplete({source: data.pais});
                                    }, 'json');
                                });
                                $(".pais").focusout(function () {
                                    var pais = $(this).val();
                                    var tipo = "#cod_" + $(this).data('tipo');
                                    if (pais !== '') {
                                        $.post('../methods.php', {method: 'carregaCodPais', pais: pais}, function (data) {
                                            $(tipo).val(data.id_pais);
                                        }, 'json');
                                    }
                                });
                            }
                        });

                        $("#nacionalidade").trigger("change");

                        /***
                         * FEITO POR SINESIO - 24/03/2016 - 656
                         */
                        $("body").on("change", "input[name='data_entrada']", function () {

                            /**
                             * RECUPERANDO DATA DE ENTRADA
                             */
                            var dataEntrada = $(this).val(); /**24/03/2016**/

                            /**
                             * EXPLODE DE DATA DE ENTRADA
                             */
                            var explode = dataEntrada.split("/");

                            /**
                             * PREENCHENDO VARIAVEIS
                             */
                            var dia = parseInt(explode[0]);
                            var mes = parseInt(explode[1]);
                            //var mes = parseInt(explode[1]) - 1;
                            var ano = parseInt(explode[2]);

                            /**
                             * OBJETO
                             */
                            var data = new Date(ano, mes, dia);
                            data.setDate(data.getDate() + 90);

                            var novaData = str_pad(data.getDate(), 2, '0', 'STR_PAD_LEFT') + "/" + str_pad(data.getMonth(), 2, '0', 'STR_PAD_LEFT') + "/" + data.getFullYear();

                            $("#dataFinalExperiencia").html("Termino da Experiência: " + novaData);

                        });

                        // ---------- jquery para municipio de nascimento ----------

                        //                        $(".uf_select").change(function() {
                        //                            var uf = $(this).val();
                        //                            var tipo = "#" + $(this).data('tipo');
                        //                            $.post('../methods.php', {method: 'carregaMunicipio', uf: uf}, function(data) {
                        //                                $(tipo).autocomplete({source: data.municipio});
                        //                            }, 'json');
                        //                        });

                        //                        $(".municipio").focusout(function() {
                        //                            var municipio = $(this).val();
                        //                            var cod_muni = "#cod_" + $(this).attr('id');
                        //                            if (municipio != '') {
                        //                                $.post('../methods.php', {method: 'carregaCodMunicipio', muni: municipio}, function(data) {
                        //                                    if (data) {
                        //                                        $(cod_muni).val(data.id_municipio);
                        //                                    } else {
                        //                                        alert("Município inválido. Favor verificar.");
                        //                                        $(cod_muni).val('');
                        //                                    }
                        //                                }, 'json');
                        //                                $(cod_muni).addClass('validate[required]');
                        //                            } else {
                        //                                $(cod_muni).removeClass('validate[required]');
                        //                            }
                        //                        });

                        //                        $(".uf_select").trigger('change');
                        // --------------- fim jquery para municipios --------------
                        // 

                        //SCRIPT DE VALIDAÇÃO DOS CAMPOS PORCENTAGEM

    //                        var porcMes = "A soma total das porcentagens deve ser igual a 100%.";
    //                        var porcTit = "PORCENTAGEM INCORRETA";
    //                        var porcCb = null;
    //                        var porcCol = 'danger';
    //
    //                        var porcUnidMes = "Selecione uma unidade antes de atribuir a porcentagem.";
    //                        var porcUnidTit = "SELECIONE UMA UNIDADE";
    //                        var porcUnidCb = null;
    //                        var porcUnidCol = 'danger';
    //
    //                        var porc1;
    //                        porc1 = $(".porc1").val();
    //                        if (porc1 == "") {
    //                            porc1 = 0;
    //                            $(".porc1").val('');
    //                        } else {
    //                            porc1 = porc1.replace('%', '');
    //                            porc1 = parseInt(porc1);
    //                        }
    //    //                        console.log(porc1);
    //
    //                        var porc2;
    //                        porc2 = $(".porc2").val();
    //                        if (porc2 == "") {
    //                            porc2 = 0;
    //                            $(".porc2").val('');
    //                        } else {
    //                            porc2 = porc2.replace('%', '');
    //                            porc2 = parseInt(porc2);
    //                        }
    //    //                        console.log(porc2);
    //
    //                        var porc3;
    //                        porc3 = $(".porc3").val();
    //                        if (porc3 == '' || porc3 == 0 || porc3 == undefined) {
    //                            porc3 = 0;
    //                            $(".porc3").val("");
    //                        } else {
    //                            porc3 = porc3.replace('%', '');
    //                            porc3 = parseInt(porc3);
    //                        }
    //    //                        console.log(porc3);
    //
    //                        var porcAnt = 0;
    //                        var porcFinal = porc1 + porc2 + porc3;
    //                        var porcUnid;
    //
    //    //                        console.log(porcFinal);                        
    //
    //                        $(".porc1").blur(function () {
    //                            porcUnid = $(".unid1").val();
    //
    //                            if (porcUnid == "") {
    //                                bootAlert(porcUnidMes, porcUnidTit, porcUnidCb, porcUnidCol);
    //                                $('.porc1').val('');
    //                                porcUnid = null;
    //                            } else {
    //                                porc1 = $(".porc1").val();
    //                                porc1 = porc1.replace('%', '');
    //                                porc1 = parseInt(porc1);
    //                                porcFinal = porc1 + porc2 + porc3;
    //                                if (porcFinal > 100) {
    //                                    bootAlert(porcMes, porcTit, porcCb, porcCol);
    //                                    $(".porc1").val('');
    //                                }
    //                            }
    //    //                            console.log(porcFinal);
    //                        });
    //
    //                        $(".porc2").blur(function () {
    //                            porcUnid = $(".unid2").val();
    //
    //                            if (porcUnid == "") {
    //                                bootAlert(porcUnidMes, porcUnidTit, porcUnidCb, porcUnidCol);
    //                                $('.porc2').val('');
    //                                porcUnid = null;
    //                            } else {
    //                                porc2 = $(".porc2").val();
    //                                porc2 = porc2.replace('%', '');
    //                                porc2 = parseInt(porc2);
    //                                porcFinal = porc1 + porc2 + porc3;
    //                                if (porcFinal > 100) {
    //                                    bootAlert(porcMes, porcTit, porcCb, porcCol);
    //                                    $(".porc2").val('');
    //                                }
    //                            }
    //    //                            console.log(porcFinal);
    //                        });
    //
    //                        $(".porc3").blur(function () {
    //                            porcUnid = $(".unid3").val();
    //
    //                            if (porcUnid == "") {
    //                                bootAlert(porcUnidMes, porcUnidTit, porcUnidCb, porcUnidCol);
    //                                $('.porc3').val('');
    //                                porcUnid = null;
    //                            } else {
    //                                porc3 = $(".porc3").val();
    //                                porc3 = porc3.replace('%', '');
    //                                porc3 = parseInt(porc3);
    //                                porcFinal = porc1 + porc2 + porc3;
    //                                if (porcFinal > 100) {
    //                                    bootAlert(porcMes, porcTit, porcCb, porcCol);
    //                                    $(".porc3").val('');
    //                                }
    //                                //                           console.log(porcFinal);    
    //                            }
    //
    //
    //                        });
    //
    //                        ////////////////////////////////////////////
    //
    //                        //SCRIPT DE VALIDAÇÃO DOS CAMPOS UNIDADE
    //
    //                        var unidMes = 'Essa unidade já foi selecionada.';
    //                        var unidTit = 'UNIDADE JÁ SELECIONADA';
    //                        var unidCb = null;
    //                        var unidCol = 'danger';
    //
    //                        var unid1;
    //    //                        unid1 = $(".unid1").val();
    //    //                        console.log(unid1);
    //
    //                        var unid2;
    //    //                        unid2 = $(".unid2").val();
    //    //                        console.log(unid2);
    //
    //                        var unid3;
    //    //                        unid3 = $(".unid3").val();
    //    //                        console.log(unid3);
    //
    //                        var unidCache;
    //
    //                        $(".unid1").change(function () {
    //                            unidCache = $(".unid1").val();
    //                            unid2 = $(".unid2").val();
    //                            unid3 = $(".unid3").val();
    //
    //                            if (unidCache == "") {
    //                                $('.porc1').val('');
    //                                porc1 = 0; //$('.porc1').val();
    //
    //                            }
    //
    //                            if (unidCache == unid2 || unidCache == unid3) {
    //                                if (unidCache != "") {
    //                                    $('.unid1').val("");
    //                                    $('.porc1').val('');
    //                                    bootAlert(unidMes, unidTit, unidCb, unidCol);
    //                                }
    //                            }
    //                        });
    //
    //                        $(".unid2").change(function () {
    //                            unidCache = $(".unid2").val();
    //                            unid1 = $(".unid1").val();
    //                            unid3 = $(".unid3").val();
    //
    //                            if (unidCache == "") {
    //                                $('.porc2').val('');
    //                                porc2 = 0; //$('.porc2').val();
    //                            }
    //
    //                            if (unidCache == unid1 || unidCache == unid3) {
    //                                if (unidCache != "") {
    //                                    $('.unid2').val("");
    //                                    $('.porc2').val('');
    //                                    bootAlert(unidMes, unidTit, unidCb, unidCol);
    //                                }
    //                            }
    //                        });
    //
    //                        $(".unid3").change(function () {
    //                            unidCache = $(".unid3").val();
    //                            unid1 = $(".unid1").val();
    //                            unid2 = $(".unid2").val();
    //
    //                            if (unidCache == "") {
    //                                $('.porc3').val('');
    //                                porc3 = 0; //$('.porc3').val();
    //                            }
    //
    //                            if (unidCache == unid1 || unidCache == unid2) {
    //                                if (unidCache != "") {
    //                                    $('.unid3').val("");
    //                                    $('.porc3').val('');
    //                                    bootAlert(unidMes, unidTit, unidCb, unidCol);
    //                                }
    //                            }
    //                        });
                        //////////////////////////////////////

                        if ($('#chefe_equipe').is(':checked')) {
                            $('#tbl_chefe_equipe').css('display', '');
                        }

                        $('#chefe_equipe').click(function () {
                            var checkbox = $(this);
                            if (checkbox.is(':checked')) {
                                $('#tbl_chefe_equipe').css('display', '');
                            } else {
                                $('#tbl_chefe_equipe').css('display', 'none');
                                $("#valor_chefe_equipe").val('');
                            }
                        });

                        if ($('#cargo_confianca').is(':checked')) {
                            $('#tbl_cargo_confianca').css('display', '');
                        }

                        $('#cargo_confianca').click(function () {
                            var checkbox = $(this);
                            if (checkbox.is(':checked')) {
                                $('#tbl_cargo_confianca').css('display', '');
                            } else {
                                $('#tbl_cargo_confianca').css('display', 'none');
                                $("#valor_cargo_confianca").val('');
                            }
                        });

                        if ($('#ajuda_custo').is(':checked')) {
                            $('#tbl_ajuda_custo').css('display', '');
                        }

                        $('#ajuda_custo').click(function () {
                            var checkbox = $(this);
                            if (checkbox.is(':checked')) {
                                $('#tbl_ajuda_custo').css('display', '');
                            } else {
                                $('#tbl_ajuda_custo').css('display', 'none');
                                $("#valor_ajuda_custo").val('');
                            }
                        });

                        if ($('#produtividade_percentual').is(':checked')) {
                            $('#tbl_produtividade_percentual').css('display', '');
                        }

                        $('#produtividade_percentual').click(function () {
                            var checkbox = $(this);
                            if (checkbox.is(':checked')) {
                                $('#tbl_produtividade_percentual').css('display', '');
                            } else {
                                $('#tbl_produtividade_percentual').css('display', 'none');
                                $("#valor_produtividade_percentual").val('');
                            }
                        });


                        if ($('#gratificacao').val() != '' && $('#gratificacao').val() != 0) {
                            $('.select_gratificacao').css('display', '');
                        }

                        $('#gratificacao').blur(function () {
                            if ($(this).val() != '0.00%') {
                                $('.select_gratificacao').css('display', '');
                                $('#select_gratificacao').addClass('validate[required]');
                                $('#select_gratificacao').prop('disabled', false);
                            } else {
                                $('.select_gratificacao').css('display', 'none');
                                $('#select_gratificacao').prop('disabled', 'disabled');
                                $('#select_gratificacao').removeClass('validate[required]');
                            }
                        });

                        $("#valor_chefe_equipe").maskMoney();
                        $("#valor_cargo_confianca").maskMoney();
                        $("#valor_ajuda_custo, .valor").maskMoney();
                    });

                    $(document).ready(function () {
                        $("#regiao_mod").hide();
                        $("body").on("change", "#regiao_mod", function () {
                            var valor = $("#regiao_mod").val();
                            $.post('#', {change_unidade: valor}, function (dados) {
                                $(".change_unidade").html(dados);
                            });
                        });

    //                    if($("#regiao_mod").is(":visible")){
    //                        $("#change_unidade").attr("class",".change_unidade");
    //                    }else{
    //                         $("#change_unidade").removeAttr("class",".change_unidade");
    //                    }
                    });

                    function OtherFunction() {
                        if ($('#outra_reg').is(":checked")) {
                            $("#regiao_mod").show();
                        } else {
                            $("#regiao_mod").hide();
                        }
                    }


                    $(function () {
    <?php if ($regUnid != '1' && $unidade_assoc[2]['id_regiao'] != $regUnid) { ?>
                            var outReg = '<?php echo $unidade_assoc[2]['id_regiao']; ?>';
                            var outAssoc = '<?php echo $unidade_assoc[2]['id_assoc']; ?>';
                            var outUnid = '<?php echo $unidade_assoc[2]['id_unidade']; ?>';
                            $('#outra_reg').click();
                            $("#regiao_mod").show();
                            $('#regiao_mod').val(outReg);
                            var valor = $("#regiao_mod").val();
                            //                            $.post('#', {change_unidade: valor}, function (dados) {
                            //                                $(".change_unidade").html(dados);
                            //                                $('#outAssoc').val(outAssoc);
                            //                                $('.unid3').val(outUnid);
                            //
                            //                            });
    <?php } ?>



                        $('body').css('display', 'block');
                    });


                </script>
                <style>

                    input:required:invalid, input:focus:invalid {
                        background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAeVJREFUeNqkU01oE1EQ/mazSTdRmqSxLVSJVKU9RYoHD8WfHr16kh5EFA8eSy6hXrwUPBSKZ6E9V1CU4tGf0DZWDEQrGkhprRDbCvlpavan3ezu+LLSUnADLZnHwHvzmJlvvpkhZkY7IqFNaTuAfPhhP/8Uo87SGSaDsP27hgYM/lUpy6lHdqsAtM+BPfvqKp3ufYKwcgmWCug6oKmrrG3PoaqngWjdd/922hOBs5C/jJA6x7AiUt8VYVUAVQXXShfIqCYRMZO8/N1N+B8H1sOUwivpSUSVCJ2MAjtVwBAIdv+AQkHQqbOgc+fBvorjyQENDcch16/BtkQdAlC4E6jrYHGgGU18Io3gmhzJuwub6/fQJYNi/YBpCifhbDaAPXFvCBVxXbvfbNGFeN8DkjogWAd8DljV3KRutcEAeHMN/HXZ4p9bhncJHCyhNx52R0Kv/XNuQvYBnM+CP7xddXL5KaJw0TMAF8qjnMvegeK/SLHubhpKDKIrJDlvXoMX3y9xcSMZyBQ+tpyk5hzsa2Ns7LGdfWdbL6fZvHn92d7dgROH/730YBLtiZmEdGPkFnhX4kxmjVe2xgPfCtrRd6GHRtEh9zsL8xVe+pwSzj+OtwvletZZ/wLeKD71L+ZeHHWZ/gowABkp7AwwnEjFAAAAAElFTkSuQmCC);
                        background-position: right top;
                        background-repeat: no-repeat;
                        -moz-box-shadow: none;
                    }

                    input:required:valid {
                        background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAepJREFUeNrEk79PFEEUx9/uDDd7v/AAQQnEQokmJCRGwc7/QeM/YGVxsZJQYI/EhCChICYmUJigNBSGzobQaI5SaYRw6imne0d2D/bYmZ3dGd+YQKEHYiyc5GUyb3Y+77vfeWNpreFfhvXfAWAAJtbKi7dff1rWK9vPHx3mThP2Iaipk5EzTg8Qmru38H7izmkFHAF4WH1R52654PR0Oamzj2dKxYt/Bbg1OPZuY3d9aU82VGem/5LtnJscLxWzfzRxaWNqWJP0XUadIbSzu5DuvUJpzq7sfYBKsP1GJeLB+PWpt8cCXm4+2+zLXx4guKiLXWA2Nc5ChOuacMEPv20FkT+dIawyenVi5VcAbcigWzXLeNiDRCdwId0LFm5IUMBIBgrp8wOEsFlfeCGm23/zoBZWn9a4C314A1nCoM1OAVccuGyCkPs/P+pIdVIOkG9pIh6YlyqCrwhRKD3GygK9PUBImIQQxRi4b2O+JcCLg8+e8NZiLVEygwCrWpYF0jQJziYU/ho2TUuCPTn8hHcQNuZy1/94sAMOzQHDeqaij7Cd8Dt8CatGhX3iWxgtFW/m29pnUjR7TSQcRCIAVW1FSr6KAVYdi+5Pj8yunviYHq7f72po3Y9dbi7CxzDO1+duzCXH9cEPAQYAhJELY/AqBtwAAAAASUVORK5CYII=);
                        background-position: right top;
                        background-repeat: no-repeat;
                    }                    

                    .none{ display: none;}
                    .quantSalMinimo{display: none;}

                    .vtQnt {
                        width: 40px;
                    }

                    .show {
                        display:block;
                    }

                    .hide {
                        display: none;
                    }
                    <?php if ($regUnid == 1) { ?> 
                        select[disabled] {
                            background: #eee; /*Simular campo inativo - Sugestão @GabrielRodrigues*/
                            pointer-events: none;
                            touch-action: none;
                        }

                        input.porc1[readonly] {
                            background: #eee; 
                            pointer-events: none;
                            touch-action: none;
                        }

                        input.porc2[readonly] {
                            background: #eee; 
                            pointer-events: none;
                            touch-action: none;
                        }
                    <?php } ?> 
                </style>
        </head>
        <body style="display: none">
            <div id="corpo">
                <table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
                    <tr>
                        <td>
                            <div style="border-bottom:2px solid #F3F3F3; margin-top:10px;">
                                <h2 style="float:left; font-size:18px;">EDITAR CADASTRO <span class="clt">CLT</span></h2>
                                <input type="hidden" name='id_clt_edt' id="id_clt_edt" value="<?php echo $id_clt; ?>" />
                                <p style="float:right;">

                                    <?php if (!isset($_GET['folha'])) { ?>
                                        <a href='ver_clt.php?reg=<?= $row['id_regiao'] ?>&clt=<?= $row[0] ?>&ant=<?= $row[1] ?>&pro=<?= $id_projeto ?>&pagina=bol'> &laquo; Voltar</a>
                                    <?php } else { ?>
                                        <a href="#"  onclick="window.close();"> Fechar (X)</a>
                                    <?php } ?>
                                </p>
                                <div class="clear"></div>
                            </div>
                            <p>&nbsp;</p>
                            <?php if ($_COOKIE['logado'] != 395) { ?>
                                <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" id="form1" name="form1" onSubmit="return validaForm()" enctype="multipart/form-data">
                                <?php } else { ?>
                                    <form action="../login.php" method="post" id="form1" name="form1" onSubmit="return validaForm()" enctype="multipart/form-data">
                                    <?php } ?>
                                    <table cellpadding="0" cellspacing="1" class="secao">
                                        <tr>
                                            <td class="secao_pai" colspan="6">DADOS ESPECIAS</td>
                                        </tr>
                                        <tr>
                                            <td class="secao">Matrícula:</td>
                                            <td colspan="6">
                                                <input name="matricula" type="text" id="matricula" size="20" value="<?php if ($row['matricula'] != 0) echo $row['matricula'] ?>"
                                                       disabled="disabled" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="25%" class="secao">Matrícula no Projeto:</td>
                                            <td width="75%" colspan="6">
                                                <input name="codigo" type="text" id="codigo" size="3" value="<?= $row['campo3'] ?>" class="validate[required]" disabled="disabled"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="secao">Número do processo:</td>
                                            <td colspan="6">
                                                <input name="n_processo" type="text" id="n_processo" size="20" value="<?php if ($row['n_processo'] != 0) echo $row['n_processo'] ?>" disabled="disabled">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="secao_pai" colspan="6" style="border-top:1px solid #777;">DADOS DO PROJETO</td>
                                        </tr>
                                        <tr style="display:none;">
                                            <td class="secao">Tipo de Contratação:</td>
                                            <td colspan="6">
                                                <select name="tipo_bol" id="tipo_bol">
                                                    <option value="2" selected>CLT</option>     
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="secao">Projeto:</td>
                                            <td colspan="6"><?= $row_pro['id_projeto'] . ' - ' . $row_pro['nome'] ?></td>
                                        </tr>

                                        <tr>
                                            <td class="secao">Curso:</td>
                                            <td colspan="6">
                                                <?php
                                                $result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = $row[id_curso]");
                                                $row_curso = mysql_fetch_assoc($result_curso);
                                                echo $row_curso['id_curso'] . ' - ' . "{$row_curso['nome']} {$row_curso['letra']}{$row_curso['numero']}" . ' / R$ ' . number_format($row_curso['valor'], 2, ',', '.');
                                                echo "<span style='font-style: italic; color:  #999999'>* Para trocar de função use a Transferência</span>";
                                                number_format
                                                ?>
                                                <?php if ($_COOKIE['logado'] == 158) { ?><button type="button" class="btn btn-default" id="btn-funcoes"><i class="fa fa-eye"></i></button><?php } ?>
                                            </td>
                                        </tr>
                                        <script>
                                            $(document).ready(function (e) {
                                                $(".novo_plantao").css("display", "none");
                                                var plantao = $("#plantao").val();
                                                //console.log(plantao);
                                                if (plantao == 6894) {
                                                    $(".novo_plantao").css("display", "block");
                                                    $("#valor_fixo_plantao").maskMoney({showSymbol: false, decimal: ",", thousands: "."});
                                                }
                                            });
                                        </script>
                                        <!--Medico Especialista Plantonista-->
                                        <tr class="plantao">
                                            <td width="20%" class="secao"></td>
                                            <td width="100%" class="novo_plantao" colspan="6">
                                                <input name="plantao" id="plantao" type="hidden" class="reset required" size="50px" value="<?php echo $row_curso['id_curso']; ?>"/>
                                                <label>Quantidade Plantão: <input name="quantidade_plantao" id="quantidade_plantao" type="text" class="reset required" size="5px" value="<?php echo $row['quantidade_plantao']; ?>"/></label>
                                                <label>Valor Fixo Plantão: <input name="valor_fixo_plantao" id="valor_fixo_plantao" type="text" class="reset required" size="10px" value="<?php echo formataMoeda($row['valor_fixo_plantao'], 1); ?>"/></label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%" class="secao">Possui Sindicato:</td>
                                            <td width="80%" colspan="6">
                                                <label><input name="radio_sindicato" type="radio" class="reset required" value="sim" <?= $checked_sim ?>/>Sim</label>
                                                <label><input name="radio_sindicato" type="radio" class="reset required" value="nao" <?= $checked_nao ?>/>Não</label>
                                            </td>
                                        </tr>
                                        <tr <?= $visualizacao ?> id="trsindicato">
                                            <td class="secao">Selecionar:</td>
                                            <td colspan="6">
                                                <label>
                                                    <select name="sindicato" id="sindicato">
                                                        <option value="<?= $sindicato_value ?>"><?php echo substr($sindicato, 0, 80); ?></option>
                                                        <?php
                                                        $result_todos_sindicato = mysql_query("SELECT * FROM rhsindicato WHERE status = '1' AND id_regiao = '$row[id_regiao]'");
                                                        while ($row_todos_sindicato = mysql_fetch_array($result_todos_sindicato)) {
                                                            echo "<option value='" . $row_todos_sindicato['id_sindicato'] . "'>" . substr($row_todos_sindicato['nome'], 0, 80) . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr class="trIsencao">
                                            <td width="20%" class="secao">Isento de Contribuição Sindical:</td>
                                            <td width="80%" colspan="6">
                                                <label><input <?= ($row['isento_sindical_confederativa']) ? 'checked' : '' ?> name="isento_confederativa" type="radio" class="isento_confederativa reset" value="1" />Sim</label>
                                                <label><input <?= (!$row['isento_sindical_confederativa']) ? 'checked' : '' ?> name='isento_confederativa' type='radio' class="isento_confederativa reset" value='0' />Não</label>
                                            </td>
                                        </tr>
                                        <tr class="trIsencao">
                                            <td width="20%" class="secao">Isento de Contribuição Assistencial:</td>
                                            <td width="80%" colspan="6">
                                                <label><input <?= ($row['isento_sindical_assistencial']) ? 'checked' : '' ?> name="isento_assistencial" type="radio" class="isento_assistencial reset" value="1" />Sim</label>
                                                <label><input <?= (!$row['isento_sindical_assistencial']) ? 'checked' : '' ?> name='isento_assistencial' type='radio' class="isento_assistencial reset" value='0' />Não</label>
                                            </td>
                                        </tr>
                                        <tr class="trIsencao">
                                            <td width="20%" class="secao">Isento de Contribuição Associativa:</td>
                                            <td width="80%" colspan="6">
                                                <label><input <?= ($row['isento_sindical_associativa']) ? 'checked' : '' ?> name="isento_associativa" type="radio" class="isento_associativa reset" value="1" />Sim</label>
                                                <label><input <?= (!$row['isento_sindical_associativa']) ? 'checked' : '' ?> name='isento_associativa' type='radio' class="isento_associativa reset" value='0' />Não</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="20%" class="secao">Isento de Contribuição:</td>
                                            <td width="80%" colspan="6">
                                                <label><input name="radio_contribuicao" type="radio" class="radio_contribuicao reset" value="sim" <?php
                                                    if (!empty($row['ano_contribuicao'])) {
                                                        echo 'checked="checked"';
                                                    }
                                                    ?>/>Sim</label>
                                                <label><input name='radio_contribuicao' type='radio' class="radio_contribuicao reset" value='nao' <?php
                                                    if (empty($row['ano_contribuicao'])) {
                                                        echo 'checked="checked"';
                                                    }
                                                    ?>/>Não</label>
                                            </td>
                                        </tr>
                                        <td width="19%" class="secao">Tipo de admiss&atilde;o</td>
                                        <td width="38%" colspan="6">
                                            <?php echo montaSelect($arrayTipoAdmi, $row['status_admi'], "name='tipo_admissao' id='tipo_admissao' class='validate[required]' style='width: 300px;'"); ?>     
                                        </td>
                                        <tr <?php if (empty($row['ano_contribuicao'])) echo 'style="display:none"'; ?> id="trcontribuicao">
                                            <td class="secao">Ano:</td>
                                            <td colspan="6">
                                                <select name="ano_contribuicao" id="ano_contribuicao" >
                                                    <option value="">Selecione</option>
                                                    <option value="1" <?= ($row['ano_contribuicao'] == 1) ? 'SELECTED' : '' ?> >TOTAL</option>
                                                    <?php
                                                    for ($ano = intval(date("Y")); $ano != 1999; $ano--) {
                                                        if ($row['ano_contribuicao'] != $ano) {
                                                            echo '<option value="' . $ano . '">' . $ano . '</option>';
                                                        } else {
                                                            echo '<option value="' . $ano . '" selected>' . $ano . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
    <!--                                        <tr>
                                            <td></td>
                                            <td colspan="6"><input type="checkbox" name="contrato_medico" value="1" <?php if ($row['contrato_medico'] == 1) echo 'checked="checked"'; ?>/> Necessita de contrato para médicos?</td>
                                        </tr>-->
                                        <tr>
                                            <td class="secao">Hor&aacute;rio:</td>
                                            <td colspan="6">
                                                <?php if (empty($verifica_folha)) { ?> 
                                                    <select name="horario" id="horario">
                                                        <?php
//                                                    $result_horarios = mysql_query("SELECT * FROM rh_horarios WHERE funcao = '$id_curso' AND id_regiao = '$row[id_regiao]'"); no lugar de  $id_curso {$_REQUEST['id']}
                                                        $result_horarios = mysql_query("SELECT * FROM rh_horarios WHERE horas_semanais = (SELECT hora_semana FROM curso WHERE id_curso = '$row[id_curso]')");


                                                        while ($row_horarios = mysql_fetch_array($result_horarios)) {
                                                            if ($row_horarios['0'] == "$row[rh_horario]") {
                                                                print "<option value='$row_horarios[0]' selected>$row_horarios[nome] ( $row_horarios[entrada_1] - $row_horarios[saida_1] - $row_horarios[entrada_2] - $row_horarios[saida_2] )</option>";
                                                                $hora_mes = $row_horarios['horas_mes'];
                                                                $hora_semana = $row_horarios['horas_semanais'];
                                                            } else {
                                                                print "<option data-mes= '{$row_horarios['horas_mes']}' data-semana ='{$row_horarios['horas_semanais']}' value='$row_horarios[0]'>$row_horarios[nome] ( $row_horarios[entrada_1] - $row_horarios[saida_1] - $row_horarios[entrada_2] - $row_horarios[saida_2] )</option>";
                                                            }
                                                        }
                                                        ?> 
                                                    </select> 
                                                    <?php
                                                } else {
                                                    $result_horarios = mysql_query("SELECT * FROM rh_horarios WHERE id_horario = $row[rh_horario]");
                                                    //  $result_horarios = mysql_query("SELECT * FROM rh_horarios WHERE funcao = $row[id_curso]");
                                                    $row_horarios = mysql_fetch_array($result_horarios);
                                                    echo $row_horarios['nome'] . '(' . $row_horarios['entrada_1'] . ' - ' . $row_horarios['saida_1'] . ' - ' . $row_horarios['entrada_2'] . ' - ' . $row_horarios['saida_2'] . ')';

                                                    echo "<input type='hidden' name='horario' value='{$row['rh_horario']}' />";
                                                }
                                                ?> 
                                            </td>
                                        </tr>
    <!--                                    <tr class="none"> 
                                            <td class="secao">Horas Semanais</td>
                                            <td colspan="6"><input type="text" id="horas_semanais" name ="horas_semanais" value="<?php echo $hora_semana ?>" disabled="disabled" size="15">&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <a href="<?php // echo 'rh_horarios_alterar.php?regiao=' . $row[id_regiao] . '&horario=' . $row_horarios[id_horario];                      ?>" target="_blank"> EDITAR</a>
                                                    Amanda
                                        <?php if ($_COOKIE['logado'] != 395) { ?>
                                                                    <a href="../adm/adm_curso/index.php" target="_blank"><label style=" cursor: default; cursor: pointer; ">EDITAR</label></a>   
                                        <?php } ?>
                                                    FIM
                                            </td>   
                                        </tr>-->

            <!--                                        <tr>
                <td class="secao">Valor da Hora:</td>
                <td colspan="6">
                    <input type="text" id="valor_hora" name ="valor_hora" value="<?php echo $row['valor_hora'] ?>"size="15">&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
            <tr>
                <td class="secao">Quantidade de Horas:</td>
                <td colspan="6">
                    <input type="number" id="quantidade_horas" name ="quantidade_horas" value="<?php echo $row['quantidade_horas'] ?>"size="15">&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>-->
            <!--                                    <tr> 
                <td class="secao">Horas Mensais</td>
                <td colspan="6"><input type="text" id="horas_mensais" name =" horas_mensais" value="<?php echo $hora_mes ?>" disabled="disabled" size="15">&nbsp;&nbsp;&nbsp;&nbsp;
                        <a href="<?php // echo 'rh_horarios_alterar.php?regiao=' . $row[id_regiao] . '&horario=' . $row_horarios[id_horario];                      ?>" target="_blank"> EDITAR</a>
                        Amanda
                                        <?php if ($_COOKIE['logado'] != 395) { ?>
                                        <a href="../adm/adm_curso/index.php" target="_blank"><label style=" cursor: default; cursor: pointer; ">EDITAR</label></a>   
                                        <?php } ?>
                        FIM
                </td>   
            </tr>-->
                                        <tr>
                                            <td class="secao">Setor</td>
                                            <td colspan="6"><?= montaSelect($arraySetor, $row['id_setor'], 'name="id_setor" id="id_setor" class=""') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="secao">Chefe de Equipe:</td>
                                            <td>
                                                <input type="checkbox" name="chefe_equipe" id="chefe_equipe" <?= $checkbox_chefe_equipe ?>>
                                            </td>
                                        </tr>

                                        <table cellpadding="0" cellspacing="1" id="tbl_chefe_equipe" class="secao" style="display: none">
                                            <tr>
                                                <td class="secao_pai" colspan="6">Chefe de Equipe</td>
                                            </tr>
                                            <tr>
                                                <td class="secao">Valor:</td>
                                                <td colspan="5">
                                                    <input name="valor_chefe_equipe" type="text" id="valor_chefe_equipe" value="<?= $valor_chefe_equipe ?>"/>
                                                </td>
                                            </tr>
                                        </table>
    <!--                                        <tr>
                                            <td class="secao" rowspan="3">Unidades</td>
                                            <td colspan="6">
                                                <input type="hidden" name="id_assoc_unidade[]" value="<?= $unidade_assoc[0]['id_assoc'] ?>"/>
                                        <?php if ($regUnid == 1) { ?> 
                                            <?php echo montaSelect($arr_unidade, $unidade_assoc[0]['id_unidade'], 'name="unidades_assoc[]" class="validate[required] unid1 validate[required]" disabled'); ?>
                                                                <input type="text" name="unidades_porcentagem[]" class="validate[required] porcentagem porc1" placeholder="00%" disabled value="<?php echo $unidade_assoc[0]['porcentagem'] . '%' ?>"/>
                                        <?php } else { ?>
                                            <?php echo montaSelect($arr_unidade, $unidade_assoc[0]['id_unidade'], 'name="unidades_assoc[]" class="validate[required] unid1 validate[required]" '); ?>
                                                                <input type="text" name="unidades_porcentagem[]" class="validate[required] porcentagem porc1" placeholder="00%" value="<?= $unidade_assoc[0]['porcentagem'] ?>"/>
                                        <?php } ?>
                                            </td>
                                        </tr>-->
    <!--                                        <tr>
                                            <td colspan="6">
                                                <input type="hidden" name="id_assoc_unidade[]" value="<?= $unidade_assoc[1]['id_assoc'] ?>"/>
                                        <?php if ($regUnid == 1) { ?> 
                                            <?php echo montaSelect($arr_unidade, $unidade_assoc[1]['id_unidade'], 'name="unidades_assoc[]" class="unid2" disabled'); ?>
                                                                <input type="text" name="unidades_porcentagem[]" class="porcentagem porc2" placeholder="00%" disabled value="<?= $unidade_assoc[1]['porcentagem'] . '%' ?>"/>
                                        <?php } else { ?>
                                            <?php echo montaSelect($arr_unidade, $unidade_assoc[1]['id_unidade'], 'name="unidades_assoc[]" class="unid2"'); ?>
                                                                <input type="text" name="unidades_porcentagem[]" class="porcentagem porc2" placeholder="00%" value="<?= $unidade_assoc[1]['porcentagem'] ?>"/>
                                        <?php } ?>    
                                            </td>
                                        </tr>-->
                                        <?php if ($regUnid == 1 && 1 == 2) { ?> 
                <!--                                            <tr>
                                                            <td colspan="6">

                                                                Selecionar outra Região: <input disabled type="checkbox" onclick="OtherFunction()" name="outra_reg" id="outra_reg"/><br><br>

                                                                        <select disabled name="regiao_mod" class="regiao_mod" id="regiao_mod">
                                                                            <option value="">Selecione</option>
                                            <?php
                                            while ($row_unidade_nova2 = mysql_fetch_assoc($result_regiao)) {
                                                echo "<option value='{$row_unidade_nova2['id_regiao']}'>{$row_unidade_nova2['regiao']}</option>";
                                            }
                                            ?>
                                                                        </select><br><br>

                                                                                <tr>
                                                                                    <td class="secao">Estado Civil:</td>
                                                                                    <td width="16%">
                                                                                        <select name="civil" id="civil">
                                            <?php
                                            $qr_estCivil = mysql_query("SELECT * FROM estado_civil");
                                            while ($row_estCivil = mysql_fetch_assoc($qr_estCivil)) {
                                                $selecionado = ($row_estCivil['id_estado_civil'] == $row['id_estado_civil']) ? 'selected="selected"' : '';
                                                echo '<option value="' . $row_estCivil['id_estado_civil'] . '|' . $row_estCivil['nome_estado_civil'] . '" ' . $selecionado . '>' . $row_estCivil['nome_estado_civil'] . '</option>';
                                            }
                                            ?>   
                                                                                        </select>
                                                                                    </td>
                                                                                    <td class="secao">Sexo:</td>
                                                                                    <td>
                                                                                        <label><input name="sexo" type="radio" class="reset" id="sexo" value="M" <?= $chekH ?>> Masculino</label><br/>
                                                                                        <label><input name="sexo" type="radio" class="reset" id="sexo" value="F" <?= $chekF ?>> Feminino</label>
                                                                                    </td>
                                                                                    <td class="secao">Nacionalidade:</td>
                                                                                    <td width="16%">
                                                                                        <input name="nacionalidade" type="text" id="nacionalidade" size="15" 
                                                                                                   onchange="this.value=this.value.toUpperCase()"/>
                                                                                        <select name="nacionalidade" id="nacionalidade">
                                            <?php
                                            while ($row_nacionalidade = mysql_fetch_assoc($qr_nacionalidade)) {
                                                $selecionado = ($row_nacionalidade['codigo'] == $row['cod_pais_rais']) ? 'selected="selected"' : '';
                                                echo '<option value="' . $row_nacionalidade['codigo'] . '" ' . $selecionado . '>' . $row_nacionalidade['nome'] . '</option>';
                                            }
                                            ?>
                                                                                        </select>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr id="ano-chegada">
                                                                                    <td class="secao">Data de chegada ao país:</td>
                                                                                    <td>
                                                                                        <input name="ano_chegada_pais" type="text" id="ano_chegada_pais" size="15" maxlength="10" value="<?php echo ( $row['dtChegadaPais'] == '00/00/0000' || empty($row['dtChegadaPais']) ? "" : $row['dtChegadaPais'] ); ?>" class="validate[required]"
                                                                                               onkeyup="mascara_data(this);"/>
                                                                                    </td>
                                                                                    <td class="secao">Pais de Nascimento</td>
                                                                                    <td>
                                                                                        <input name="pais_nasc" type="text" id="pais_nasc" data-tipo = "pais_nasc" size="15" class="pais" value="<?= $row['pais_nasc'] ?>" />
                                                                                        <input type="text" readonly="readonly" name="cod_pais_nasc" id="cod_pais_nasc" size="4" value="<?php echo ( $row['id_pais_nasc'] == 0 || empty($row['id_pais_nasc']) ? "" : $row['id_pais_nasc'] ); ?>"/>
                                                                                    </td>
                                                                                    <td class="secao">País de Nacionalidade</td>
                                                                                    <td>
                                                                                        <input name="pais_nacionalidade" type="text" id="pais_nacionalidade" data-tipo = "pais_nacionalidade" size="15" class="pais" value="<?= $row['pais_nacionalidade'] ?>" />
                                                                                        <input type="text" readonly="readonly" name="cod_pais_nacionalidade" id="cod_pais_nacionalidade" size="4" value="<?php echo( $row['id_pais_nacionalidade'] == 0 || empty($row['id_pais_nacionalidade']) ? "" : $row['id_pais_nacionalidade']); ?>"/>
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="secao">CEP:</td>
                                                                                    <td colspan="5"><input name="cep" type="text" id="cep" size="16" maxlength="9" value="<?= $row['cep'] ?>" />
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="secao">Endereço:</td>
                                                                                    <td><input name="cod_tp_logradouro" pattern="[a-z\s]+$" id="cod_tp_logradouro" type="hidden" value="<?= $row['tipo_endereco'] ?>"  /><input name="endereco" type="text" id="endereco" size="35" value="<?= $row['endereco'] ?>" class="validate[required]"
                                                                                                                                                                                                                onChange="this.value = this.value.toUpperCase()"/>
                                                                                    </td>

                                                                                    <td class="secao">Número</td>
                                                                                    <td colspan="6"><input name="numero"  id="numero" pattern="[0-9]+$" type="text"  value="<?= $row['numero'] ?>" /></td>

                                                                                    <select name="regiao_mod" class="regiao_mod" id="regiao_mod">
                                                                                        <option value="">Selecione</option>
                                            <?php
                                            while ($row_unidade_nova2 = mysql_fetch_assoc($result_regiao)) {
                                                echo "<option value='{$row_unidade_nova2['id_regiao']}'>{$row_unidade_nova2['regiao']}</option>";
                                            }
                                            ?>
                                                                                    </select><br><br>

                                                                                            <input type="hidden" id="outAssoc" class="change_unidade" name="id_assoc_unidade[]" value="<?= $unidade_assoc[2]['id_assoc'] ?>"/>
                                            <?php echo montaSelect($arr_unidade, $unidade_assoc[2]['id_unidade'], 'name="unidades_assoc[]" class="unid3 change_unidade"'); ?>
                                                                                            <input type="text" name="unidades_porcentagem[]" class="porcentagem porc3" placeholder="00%" value="<?= $unidade_assoc[2]['porcentagem'] ?>"/>
                                                                                            </td>
                                                                                            </tr>-->
                                        <?php } ?>
                                    </table>
                                    <table cellpadding="0" cellspacing="1" class="secao">
                                        <tr>
                                            <td class="secao_pai" colspan="6">DADOS PESSOAIS</td>
                                        </tr>
                                        <tr>
                                            <td class="secao">Nome:</td>
                                            <td colspan="5">
                                                <input name="nome" type="text" id="nome" size="75" onChange="this.value = this.value.toUpperCase();" onKeyPress="return(verificanome(this, event));"  value="<?= $row['nome'] ?>" class="validate[required]"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="secao">Data de Nascimento:</td>
                                            <td>
                                                <input name="data_nasc" type="text" id="data_nasc" size="15" maxlength="10" value="<?= $row['data_nascimento'] ?>" class="validate[required]"
                                                       onkeyup="mascara_data(this);"/>
                                            </td>

                                            <td class="secao">UF de Nascimento:</td>
                                            <td>
                                                <select name="uf_nasc_select" id="uf_nasc_select" data-tipo="municipio_nasc" class="uf_select">
                                                    <option value=""></option>
                                                    <?php
                                                    $qr_uf = mysql_query("SELECT * FROM uf");
                                                    while ($row_uf = mysql_fetch_assoc($qr_uf)) {
                                                        if ($row['cod_pais_rais'] == 10) {
                                                            $selected = ($row['uf_nasc'] == $row_uf['uf_sigla']) ? 'selected="selected"' : '';
                                                        }
                                                        echo '<option value="' . $row_uf['uf_sigla'] . '" ' . $selected . ' >' . $row_uf['uf_sigla'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                                <input name="uf_nasc_text" type="text" id="uf_nasc_text" size="16"  onchange="this.value = this.value.toUpperCase()" value="<?php echo $row['uf_nasc'] ?>" />
                                                <?php
                                                if ($row['cod_pais_rais'] == 10) {
                                                    ?>
                                                    <script>
                                                        $("#uf_nasc_select").show();
                                                        $("#uf_nasc_text").hide();
                                                    </script>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <script>
                                                        $("#uf_nasc_select").hide();
                                                        $("#uf_nasc_text").show();
                                                    </script>
                                                    <?php
                                                }
                                                ?>
                                            </td>

                                            <td class="secao">Município de Nascimento:</td>
                                            <td>
                                                <input name="municipio_nasc" type="text" id="municipio_nasc" size="15"  value="<?php echo $row['municipio_nasc'] ?>"
                                                       onChange="this.value = this.value.toUpperCase();"  class="municipio"  />
                                                <input type="text" readonly="readonly" name="cod_municipio_nasc" id="cod_municipio_nasc" size="4" value="<?= $row['id_municipio_nasc'] ?>" />
                                            </td>
                                        </tr>   

                                        <tr>
                                            <td class="secao">Estado Civil:</td>
                                            <td width="16%">
                                                <select name="civil" id="civil">
                                                    <?php
                                                    $qr_estCivil = mysql_query("SELECT * FROM estado_civil");
                                                    while ($row_estCivil = mysql_fetch_assoc($qr_estCivil)) {
                                                        $selecionado = ($row_estCivil['id_estado_civil'] == $row['id_estado_civil']) ? 'selected="selected"' : '';
                                                        echo '<option value="' . $row_estCivil['id_estado_civil'] . '|' . $row_estCivil['nome_estado_civil'] . '" ' . $selecionado . '>' . $row_estCivil['nome_estado_civil'] . '</option>';
                                                    }
                                                    ?>   
                                                </select>
                                            </td>
                                            <td class="secao">Sexo:</td>
                                            <td>
                                                <label><input name="sexo" type="radio" class="reset" id="sexo" value="M" <?= $chekH ?>> Masculino</label><br/>
                                                <label><input name="sexo" type="radio" class="reset" id="sexo" value="F" <?= $chekF ?>> Feminino</label>
                                            </td>
                                            <td class="secao">Nacionalidade:</td>
                                            <td width="16%">
                                                <!--<input name="nacionalidade" type="text" id="nacionalidade" size="15" 
                                                           onchange="this.value=this.value.toUpperCase()"/>-->
                                                <select name="nacionalidade" id="nacionalidade">
                                                    <?php
                                                    while ($row_nacionalidade = mysql_fetch_assoc($qr_nacionalidade)) {
                                                        $selecionado = ($row_nacionalidade['codigo'] == $row['cod_pais_rais']) ? 'selected="selected"' : '';
                                                        echo '<option value="' . $row_nacionalidade['codigo'] . '" ' . $selecionado . '>' . $row_nacionalidade['nome'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr id="ano-chegada">
                                            <td class="secao">Data de chegada ao país:</td>
                                            <td>
                                                <input name="ano_chegada_pais" type="text" id="ano_chegada_pais" size="15" maxlength="10" value="<?php echo ( $row['dtChegadaPais'] == '00/00/0000' || empty($row['dtChegadaPais']) ? "" : $row['dtChegadaPais'] ); ?>" class="validate[required]"
                                                       onkeyup="mascara_data(this);"/>
                                            </td>
                                            <td class="secao">Pais de Nascimento</td>
                                            <td>
                                                <input name="pais_nasc" type="text" id="pais_nasc" data-tipo = "pais_nasc" size="15" class="pais" value="<?= $row['pais_nasc'] ?>" />
                                                <input type="text" readonly="readonly" name="cod_pais_nasc" id="cod_pais_nasc" size="4" value="<?php echo ( $row['id_pais_nasc'] == 0 || empty($row['id_pais_nasc']) ? "" : $row['id_pais_nasc'] ); ?>"/>
                                            </td>
                                            <td class="secao">País de Nacionalidade</td>
                                            <td>
                                                <input name="pais_nacionalidade" type="text" id="pais_nacionalidade" data-tipo = "pais_nacionalidade" size="15" class="pais" value="<?= $row['pais_nacionalidade'] ?>" />
                                                <input type="text" readonly="readonly" name="cod_pais_nacionalidade" id="cod_pais_nacionalidade" size="4" value="<?php echo( $row['id_pais_nacionalidade'] == 0 || empty($row['id_pais_nacionalidade']) ? "" : $row['id_pais_nacionalidade']); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="secao">CEP:</td>
                                            <td colspan="5"><input name="cep" type="text" id="cep" size="16" maxlength="9" value="<?= $row['cep'] ?>" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="secao">Endereço:</td>
                                            <td><input name="cod_tp_logradouro" id="cod_tp_logradouro" type="hidden" value="<?= $row['tipo_endereco'] ?>"  /><input name="endereco" type="text" id="endereco" size="35" value="<?= $row['endereco'] ?>" class="validate[required]"
                                                                                                                                                                    onChange="this.value = this.value.toUpperCase()"/>
                                            </td>

                                            <td class="secao">Número</td>
                                            <td><input name="numero"  id="numero"  type="text"  value="<?= $row['numero'] ?>" /></td>

                                            <td class="secao">Complemento</td>
                                            <td><input name="complemento" id="complemento" type="text"  value="<?= $row['complemento'] ?>" /></td>
                                        </tr>
                                        <tr>
                                            <td class="secao">Bairro:</td>
                                            <td><input name="bairro" type="text" id="bairro" size="16" value="<?= $row['bairro'] ?>" class="validate[required]"
                                                       onChange="this.value = this.value.toUpperCase()"/></td>

                                            <td class="secao">UF:</td>
                                            <td>
                                                <select name="uf" id="uf" class="validate[required] uf_select" data-tipo="cidade">

                                                    <?php
                                                    $qr_uf = mysql_query("SELECT * FROM uf");
                                                    while ($row_uf = mysql_fetch_assoc($qr_uf)) {
                                                        if (isset($row['uf']) && $row['uf'] == $row_uf['uf_sigla']) {
                                                            echo '<option value="' . $row_uf['uf_sigla'] . '" selected>' . $row_uf['uf_sigla'] . '</option>';
                                                        } else {
                                                            echo '<option value="' . $row_uf['uf_sigla'] . '">' . $row_uf['uf_sigla'] . '</option>';
                                                        }
                                                    }
                                                    ?>    
                                                </select>
                                            </td>
                                            <td class="secao">Cidade:</td>
                                            <td><input name="cidade" type="text" id="cidade" size="35" value="<?= $row['cidade'] ?>" onChange="this.value = this.value.toUpperCase()" class="validate[required]" class="validate[required] municipio"  />
                                                <input type="text" readonly="readonly" name="cod_cidade" id="cod_cidade" size="4" value="<?= $row['id_municipio_end'] ?>"/>

                                            </td>
                                        </tr>
                                        <tr>

                                            <td class="secao">Estuda Atualmente?</td>
                                            <td>
                                                <label><input name="estuda" type="radio" class="reset" value="sim" <?= $chekS ?>> Sim </label>
                                                <label><input name="estuda" type="radio" class="reset" value="nao" <?= $chekN ?>> Não </label>
                                                <?= $mensagem_sexo ?>  
                                            </td>

                                            <td class="secao">Término em:</td>
                                            <td colspan="3">
                                                <input name="data_escola" type="text" id="data_escola" size="15" maxlength="10" value="<?= $row['data_escola2'] ?>"
                                                       onKeyUp="mascara_data(this);" /> 
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="secao">Escolaridade:</td>
                                            <td>
                                                <select name="escolaridade">
                                                    <option value="12">Não informado</option>
                                                    <?php
                                                    $qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE status = 'on' LIMIT 0,11");
                                                    while ($escolaridade = mysql_fetch_assoc($qr_escolaridade)) {
                                                        ?>
                                                        <option value="<?= $escolaridade['id'] ?>"<?php if ($row['escolaridade'] == $escolaridade['id']) { ?> selected="selected"<?php } ?>><?= $escolaridade['nome'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td class="secao">Curso:</td>
                                            <td><input name="curso" type="text" id="zona" size="16" value="<?= $row['curso'] ?>" 
                                                       onChange="this.value = this.value.toUpperCase()"/></td>

                                            <td class="secao">Institui&ccedil;&atilde;o:</td>
                                            <td><input name="instituicao" type="text" id="instituicao" size="15" value="<?= $row['instituicao'] ?>"
                                                       onChange="this.value = this.value.toUpperCase()"/></td>
                                        </tr>
                                        <tr>
                                            <td class="secao">Telefone Fixo:</td>
                                            <td><input name="tel_fixo" type="text" id="tel_fixo" size="16" value="<?= $row['tel_fixo'] ?>" class="tel"/></td>
                                            <td class="secao">Celular:</td>
                                            <td><input name="tel_cel" type="text" id="tel_cel" size="16" value="<?= $row['tel_cel'] ?>" class="tel" /></td>
                                            <td class="secao">Recado:</td>
                                            <td>
                                                <input name="tel_rec" type="text" id="tel_rec" size="15" value="<?= $row['tel_rec'] ?>" class="tel"/>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="secao">E-mail:</td>
                                            <td colspan="5">
                                                <input name="email" type="text" id="email" size="35" value='<?= $row['email'] ?>' />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="secao">Tipo Sanguíneo</td>
                                            <td colspan="5">
                                                <select name="tiposanguineo" id="tiposanguineo" >
                                                    <option value="">Selecione</option>
                                                    <?php
                                                    $qr_ts = mysql_query("SELECT * FROM tipo_sanguineo");
                                                    while ($row_ts = mysql_fetch_assoc($qr_ts)) {
                                                        $selected = ($row['tipo_sanguineo'] == $row_ts['nome']) ? 'selected="selected"' : '';
                                                        echo '<option value="' . $row_ts['nome'] . '" ' . $selected . ' >' . $row_ts['nome'] . '</option>';
                                                    }
                                                    ?>    
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                    <table cellpadding="0" cellspacing="1" class="secao">
                                        <tr>
                                            <td colspan="4" class="secao_pai">DADOS DA FAMÍLIA</td>
                                        </tr>

                                        <tr> 
                                            <td class="secao">Filiação - Pai:</td>
                                            <td colspan="3">
                                                <input name="pai" type="text" id="pai" size="45" value="<?= $row['pai'] ?>"
                                                       onChange="this.value = this.value.toUpperCase()"/>
                                                <input type="checkbox" name="ddir_pai" id="ddir_pai" value="1" <?php echo $checked_pai; ?>/> Dependente de IRRF
                                            </td>
                                        </tr>

                                        <tr> 
                                            <td class="secao"> Nacionalidade Pai:</td>
                                            <td>
                                                <input name="nacionalidade_pai" type="text" id="nacionalidade_pai" size="15" value="<?= $row['nacionalidade_pai'] ?>"
                                                       onChange="this.value = this.value.toUpperCase()"/>	
                                            </td>

                                            <td class="secao">Data de nascimento do Pai:</td>
                                            <td><input type="text" name="data_nasc_pai" id="data_nasc_pai" value="<?php echo $row['data_nasc_pai']; ?>" onkeyup="mascara_data(this);" /></td>
                                        </tr>

                                        <tr>
                                            <td class="secao">Filiação - Mãe:</td>
                                            <td colspan="3">
                                                <input name="mae" type="text" id="mae" size="45" value="<?= $row['mae'] ?>"
                                                       onChange="this.value = this.value.toUpperCase()"/>
                                                <input type="checkbox" name="ddir_mae" id="ddir_mae" value="1" <?php echo $checked_mae; ?> /> Dependente de IRRF
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="secao">
                                                Nacionalidade Mãe:
                                            </td>
                                            <td>
                                                <input name="nacionalidade_mae" type="text" id="nacionalidade_mae" size="15" value="<?= $row['nacionalidade_mae'] ?>"
                                                       onChange="this.value = this.value.toUpperCase()"/>	
                                            </td>

                                            <td class="secao">Data de nascimento da Mãe:</td>
                                            <td><input type="text" name="data_nasc_mae" id="data_nasc_mae" value="<?php echo $row['data_nasc_mae']; ?>" onkeyup="mascara_data(this);" /> </td>
                                        </tr>

                                        <tr>
                                            <td class="secao">Conjuge:</td>
                                            <td colspan="3">
                                                <input name="conjuge" type="text" id="conjuge" size="45" 
                                                       onchange="this.value = this.value.toUpperCase()" value="<?php echo $row['nome_conjuge'] ?>"/>
                                                <input type="checkbox" name="ddir_conjuge" id="ddir_conjuge" value="1" <?php echo $checked_conjuge; ?>/> Dependente de IRRF
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="secao">Data de nascimento do Conjuge:</td>
                                            <td colspan="3">
                                                <input name="data_nasc_conjuge" type="text" id="data_nasc_conjuge" size="15" 
                                                       onchange="this.value = this.value.toUpperCase()" value="<?php echo $row['data_nasc_conjuge']; ?>" onkeyup="mascara_data(this);" />	
                                            </td>
                                        </tr> 
                                        <?php
                                        if ($_COOKIE['logado'] == 87) {
                                            ?>
                                            <tr>
                                                <td class="secao">Avô:</td>
                                                <td colspan="4">
                                                    <input name="avo_h" type="text" id="avo_h" size="45" 
                                                           onchange="this.value = this.value.toUpperCase()"  value="<?php echo $row['nome_avo_h']; ?>"/>
                                                    <input type="checkbox" name="ddir_avo_h" id="ddir_avo_h" value="1" <?php echo $checked_avo_h; ?> /> Dependente de IRRF
                                                </td>
                                            </tr>
                                            <tr>            
                                                <td class="secao">Data de nascimento do Avô:</td>
                                                <td colspan="3"><input type="text" name="data_nasc_avo_h" id="data_nasc_avo_h" value="<?php echo $row['data_nasc_avo_h']; ?>" onkeyup="mascara_data(this);" /> </td>
                                            </tr>

                                            <tr>
                                                <td class="secao">Avó:</td>
                                                <td colspan="4">
                                                    <input name="avo_m" type="text" id="avo_m" size="45" 
                                                           onchange="this.value = this.value.toUpperCase()" value="<?php echo $row['nome_avo_m']; ?>"/>
                                                    <input type="checkbox" name="ddir_avo_m" id="ddir_avo_m" value="1" <?php echo $checked_avo_m; ?>/> Dependente de IRRF
                                                </td>
                                            </tr>
                                            <tr>             
                                                <td class="secao">Data de nascimento da Avó:</td>
                                                <td colspan="3"><input type="text" name="data_nasc_avo_m" id="data_nasc_avo_m" value="<?php echo $row['data_nasc_avo_m']; ?>" onkeyup="mascara_data(this);"/> </td>
                                            </tr>


                                            <tr>
                                                <td class="secao">Bisavô:</td>
                                                <td colspan="4">
                                                    <input name="bisavo_h" type="text" id="bisavo_h" size="45" 
                                                           onchange="this.value = this.value.toUpperCase()" value="<?php echo $row['nome_bisavo_h']; ?>"/>
                                                    <input type="checkbox" name="ddir_bisavo_h" id="ddir_bisavo_h" value="1" <?php echo $checked_bisavo_h; ?>/> Dependente de IRRF
                                                </td>
                                            </tr>
                                            <tr>             
                                                <td class="secao">Data de nascimento do Bisavô:</td>
                                                <td colspan="3"><input type="text" name="data_nasc_bisavo_h" id="data_nasc_bisavo_h" value="<?php echo $row['data_nasc_bisavo_h']; ?>" onkeyup="mascara_data(this);" /> </td>
                                            </tr>

                                            <tr>
                                                <td class="secao">Bisavó:</td>
                                                <td colspan="4">
                                                    <input name="bisavo_m" type="text" id="bisavo_m" size="45" 
                                                           onchange="this.value = this.value.toUpperCase()" value="<?php echo $row['nome_bisavo_m']; ?>"/>
                                                    <input type="checkbox" name="ddir_bisavo_m" id="ddir_bisavo_m" value="1" <?php echo $checked_bisavo_m; ?>/> Dependente de IRRF
                                                </td>
                                            </tr>
                                            <tr>           
                                                <td class="secao">Data de nascimento da Bisavó:</td>
                                                <td colspan="3"><input type="text" name="data_nasc_bisavo_m" id="data_nasc_bisavo_m" value="<?php echo $row['data_nasc_bisavo_m']; ?>" onkeyup="mascara_data(this);"/> </td>
                                            </tr>

                                            <?php
                                        }
                                        ?>
                                        <tr>
                                            <td class="secao">Número de Filhos:</td>
                                            <td colspan="3">
                                                <input name="filhos" type="text" id="filhos" size="2" value="<?= $row['num_filhos'] ?>" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="secao">Deduzir no imposto de renda:</td>
                                            <?php
                                            if ((is_null($row['imposto_renda'])) || ($row['imposto_renda'] == "")) {
                                                $imp_r = 'checked="checked"';
                                            }
                                            ?>
                                            <!-- Corrigindo falha ao mostrar coluna com colspan -->
                                            <td colspan="10">
                                                <label><input name="imposto_renda" type="radio" class="reset" value="sim" <?php echo $imp_r; ?> <?php echo (isset($row['imposto_renda']) && $row['imposto_renda'] == "sim") ? 'checked="checked"' : "" ?> /> SIM</label>
                                                <label><input name="imposto_renda" type="radio" class="reset" value="não" <?php echo (isset($row['imposto_renda']) && $row['imposto_renda'] == "não") ? 'checked="checked"' : "" ?> /> NÃO</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="secao">Nome:</td>
                                            <td>
                                                <input name="filho_1" type="text" id="filho_1" size="50" value="<?= $row_depe['nome1'] ?>"
                                                       onChange="this.value = this.value.toUpperCase()" class="nome_filho"/>
                                                <br><br>
                                                        <input name="cpf_1" type="text" id="cpf_1" size="50" value="<?= $row_depe['cpf1'] ?>" placeholder="CPF" class="validate[funcCall[verificaCPF]]" />
                                                        </td>
                                                        <td class="secao">Nascimento:</td>
                                                        <td>
                                                            <input name="data_filho_1" type="text" size="12" maxlength="10" id="data_filho_1" value="<?= ($row_depe['datas1'] != '00/00/0000') ? $row_depe['datas1'] : ''; ?>"
                                                                   onKeyUp="mascara_data(this);
                                                                           pula(10, this.id, filho_2.id)"
                                                                   onChange="this.value = this.value.toUpperCase()"  class="data_filho"/>
                                                            <br/>
                                                            <input name="portador1" id="portador1" value="1"  type="checkbox" <?php echo $checked_portador1; ?>/> Portador de deficiência</br>
                                                    <input name="dep1_cur_fac_ou_tec" id="dep1_cur_fac_ou_tec" value="1"  type="checkbox" <?php echo $checked_dep1_cur_fac_ou_tec; ?>/> Cursando escola técnica ou faculdade <br />
                                                    <input type="checkbox" name="ddir1" id="ddir1" value="1" <?php echo $nao_ir_filho1; ?>/> Não inside IRRF <br />
                                                    <input type="checkbox" name="guarda_filho1" id="guarda_filho1" value="1" <?php echo $possui_guarda_filho1; ?>/> Possui a guarda
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="secao">Nome:</td>
                                            <td>
                                                <input name="filho_2" type="text" id="filho_2" size="50" value="<?= $row_depe['nome2'] ?>"
                                                       onChange="this.value = this.value.toUpperCase()"  class="nome_filho"/>
                                                <br><br>
                                                        <input name="cpf_2" type="text" id="cpf_2" size="50" value="<?= $row_depe['cpf2'] ?>" placeholder="CPF" class="validate[funcCall[verificaCPF]]" />
                                                        </td>
                                                        <td class="secao">Nascimento:</td>
                                                        <td>
                                                            <input name="data_filho_2" type="text" size="12" maxlength="10" id="data_filho_2" value="<?= ($row_depe['datas2'] != '00/00/0000') ? $row_depe['datas2'] : ''; ?>"
                                                                   onKeyUp="mascara_data(this);
                                                                           pula(10, this.id, filho_3.id)"        
                                                                   onChange="this.value = this.value.toUpperCase()" class="data_filho"/>
                                                            <br/>
                                                            <input name="portador2" id="portador2" value="1"  type="checkbox" <?php echo $checked_portador2; ?>/> Portador de deficiência<br/>
                                                            <input name="dep2_cur_fac_ou_tec" id="dep2_cur_fac_ou_tec" value="1"  type="checkbox" <?php echo $checked_dep2_cur_fac_ou_tec; ?>/> Cursando escola técnica ou faculdade <br/>
                                                            <input type="checkbox" name="ddir2" id="ddir2" value="1" <?php echo $nao_ir_filho2; ?>/> Não inside IRRF <br />
                                                            <input type="checkbox" name="guarda_filho2" id="guarda_filho2" value="1" <?php echo $possui_guarda_filho2; ?>/> Possui a guarda
                                                        </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="secao">Nome:</td>
                                                            <td>
                                                                <input name="filho_3" type="text" id="filho_3" size="50" value="<?= $row_depe['nome3'] ?>"
                                                                       onChange="this.value = this.value.toUpperCase()"  class="nome_filho"/>
                                                                <br><br>
                                                                        <input name="cpf_3" type="text" id="cpf_3" size="50" value="<?= $row_depe['cpf3'] ?>" placeholder="CPF" class="validate[funcCall[verificaCPF]]" />
                                                                        </td>
                                                                        <td class="secao">Nascimento:</td>
                                                                        <td>
                                                                            <input name="data_filho_3" type="text" size="12" maxlength="10" id="data_filho_3" value="<?= ($row_depe['datas3'] != '00/00/0000') ? $row_depe['datas3'] : ''; ?>"
                                                                                   onKeyUp="mascara_data(this);
                                                                                           pula(10, this.id, filho_4.id)"
                                                                                   onChange="this.value = this.value.toUpperCase()" class="data_filho"/>
                                                                            <br/>
                                                                            <input name="portador3" id="portador3" value="1"  type="checkbox" <?php echo $checked_portador3; ?>/> Portador de deficiência</br>
                                                                    <input name="dep3_cur_fac_ou_tec" id="dep3_cur_fac_ou_tec" value="1"  type="checkbox" <?php echo $checked_dep3_cur_fac_ou_tec; ?>/> Cursando escola técnica ou faculdade<br/>
                                                                    <input type="checkbox" name="ddir3" id="ddir3" value="1" <?php echo $nao_ir_filho3; ?>/> Não inside IRRF <br />
                                                                    <input type="checkbox" name="guarda_filho3" id="guarda_filho3" value="1" <?php echo $possui_guarda_filho3; ?>/> Possui a guarda
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="secao">Nome:</td>
                                                            <td>
                                                                <input name="filho_4" type="text" id="filho_4" size="50" value="<?= $row_depe['nome4'] ?>"
                                                                       onChange="this.value = this.value.toUpperCase()"  class="nome_filho"/>
                                                                <br><br>
                                                                        <input name="cpf_4" type="text" id="cpf_4" size="50" value="<?= $row_depe['cpf4'] ?>" placeholder="CPF" class="validate[funcCall[verificaCPF]]" />
                                                                        </td>
                                                                        <td class="secao">Nascimento:</td>
                                                                        <td>
                                                                            <input name="data_filho_4" type="text" size="12" maxlength="10" id="data_filho_4" value="<?= ($row_depe['datas4'] != '00/00/0000') ? $row_depe['datas4'] : ''; ?>"
                                                                                   onKeyUp="mascara_data(this);
                                                                                           pula(10, this.id, filho_5.id)"
                                                                                   onChange="this.value = this.value.toUpperCase()" class="data_filho"/>
                                                                            <br/>
                                                                            <input name="portador4" id="portador4" value="1"  type="checkbox" <?php echo $checked_portador4; ?>/> Portador de deficiência</br>
                                                                    <input name="dep4_cur_fac_ou_tec" id="dep4_cur_fac_ou_tec" value="1"  type="checkbox" <?php echo $checked_dep4_cur_fac_ou_tec; ?>/> Cursando escola técnica ou faculdade <br />
                                                                    <input type="checkbox" name="ddir4" id="ddir4" value="1" <?php echo $nao_ir_filho4; ?>/> Não inside IRRF <br />
                                                                    <input type="checkbox" name="guarda_filho4" id="guarda_filho4" value="1" <?php echo $possui_guarda_filho4; ?>/> Possui a guarda
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="secao">Nome:</td>
                                                            <td>
                                                                <input name="filho_5" type="text" id="filho_5" size="50" value="<?= $row_depe['nome5'] ?>"
                                                                       onChange="this.value = this.value.toUpperCase()"  class="nome_filho"/>
                                                                <br><br>
                                                                        <input name="cpf_5" type="text" id="cpf_5" size="50" value="<?= $row_depe['cpf5'] ?>" placeholder="CPF" class="validate[funcCall[verificaCPF]]" />
                                                                        </td>
                                                                        <td class="secao">Nascimento:</td>
                                                                        <td>
                                                                            <input name="data_filho_5" type="text" size="12" maxlength="10" id="data_filho_5" value="<?= ($row_depe['datas5'] != '00/00/0000') ? $row_depe['datas5'] : ''; ?>"
                                                                                   onKeyUp="mascara_data(this);"
                                                                                   onChange="this.value = this.value.toUpperCase()" class="data_filho"/>
                                                                            <br/>
                                                                            <input name="portador5" id="portador5" value="1"  type="checkbox" <?php echo $checked_portador5; ?>/> Portador de deficiência</br>
                                                                    <input name="dep5_cur_fac_ou_tec" id="dep5_cur_fac_ou_tec" value="1"  type="checkbox" <?php echo $checked_dep5_cur_fac_ou_tec; ?>/> Cursando escola técnica ou faculdade <br />
                                                                    <input type="checkbox" name="ddir5" id="ddir5" value="1" <?php echo $nao_ir_filho5; ?>/> Não inside IRRF <br />
                                                                    <input type="checkbox" name="guarda_filho5" id="guarda_filho5" value="1" <?php echo $possui_guarda_filho5; ?>/> Possui a guarda
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="secao">Nome:</td>
                                                            <td>
                                                                <input name="filho_6" type="text" id="filho_6" size="50" value="<?= $row_depe['nome6'] ?>"
                                                                       onChange="this.value = this.value.toUpperCase()"  class="nome_filho"/>
                                                                <br><br>
                                                                        <input name="cpf_6" type="text" id="cpf_6" size="50" value="<?= $row_depe['cpf6'] ?>" placeholder="CPF" class="validate[funcCall[verificaCPF]]" />

                                                                        </td>
                                                                        <td class="secao">Nascimento:</td>
                                                                        <td>
                                                                            <input name="data_filho_6" type="text" size="12" maxlength="10" id="data_filho_6" value="<?= ($row_depe['datas6'] != '00/00/0000') ? $row_depe['datas6'] : ''; ?>"
                                                                                   onKeyUp="mascara_data(this);"
                                                                                   onChange="this.value = this.value.toUpperCase()" class="data_filho"/>
                                                                            <br/>
                                                                            <input name="portador6" id="portador6" value="1"  type="checkbox" <?php echo $checked_portador6; ?>/> Portador de deficiência</br>
                                                                    <input name="dep6_cur_fac_ou_tec" id="dep6_cur_fac_ou_tec" value="1"  type="checkbox" <?php echo $checked_dep6_cur_fac_ou_tec; ?>/> Cursando escola técnica ou faculdade<br />
                                                                    <input type="checkbox" name="ddir6" id="ddir6" value="1" <?php echo $nao_ir_filho6; ?>/> Não inside IRRF <br />
                                                                    <input type="checkbox" name="guarda_filho6" id="guarda_filho6" value="1" <?php echo $possui_guarda_filho6; ?>/> Possui a guarda
                                                            </td>
                                                        </tr>
                                                        </table>
                                                        <table cellpadding="0" cellspacing="1" class="secao">
                                                            <tr>
                                                                <td class="secao_pai" colspan="6">APARÊNCIA</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="secao">
                                                                    Cabelos:
                                                                </td>
                                                                <td>
                                                                    <select name="cabelos" id="cabelos">
                                                                        <option>Não informado</option>
                                                                        <?php
                                                                        $result_cabelos = mysql_query("SELECT * FROM tipos WHERE tipo = '1' AND status = '1'");
                                                                        while ($row_cabelos = mysql_fetch_array($result_cabelos)) {
                                                                            if ($row['cabelos'] == $row_cabelos['nome']) {
                                                                                print "<option selected>$row_cabelos[nome]</option>";
                                                                            } else {
                                                                                print "<option>$row_cabelos[nome]</option>";
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </td>
                                                                <td class="secao">Olhos:</td>
                                                                <td>
                                                                    <select name="olhos" id="olhos">
                                                                        <option>Não informado</option>
                                                                        <?php
                                                                        $result_olhos = mysql_query("SELECT * FROM tipos WHERE tipo = '2' AND status = '1'");
                                                                        while ($row_olhos = mysql_fetch_array($result_olhos)) {
                                                                            if ($row['olhos'] == $row_olhos['nome']) {
                                                                                print "<option selected>$row_olhos[nome]</option>";
                                                                            } else {
                                                                                print "<option>$row_olhos[nome]</option>";
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </td>
                                                                <td class="secao">Peso:</td>
                                                                <td>
                                                                    <input name="peso" type="text" id="peso" size="5" value="<?= $row['peso'] ?>" />
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="secao">Altura:</td>
                                                                <td>
                                                                    <input name="altura" type="text" id="altura" size="5" value="<?= $row['altura'] ?>" />
                                                                </td>
                                                                <td class="secao">Etnia:</td>
                                                                <td>
                                                                    <select name="etnia">
                                                                        <?php
                                                                        $qr_etnias = mysql_query("SELECT * FROM etnias WHERE status = 'on' ORDER BY id DESC");
                                                                        while ($etnia = mysql_fetch_assoc($qr_etnias)) {
                                                                            ?>
                                                                            <option value="<?= $etnia['id'] ?>"<?php if ($row['etnia'] == $etnia['id']) { ?> selected="selected"<?php } ?>><?= $etnia['nome'] ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </td>
                                                                <td class="secao">Marcas ou Cicatriz:</td>
                                                                <td>
                                                                    <input name="defeito" type="text" id="defeito" size="18" value="<?= $row['defeito'] ?>"
                                                                           onChange="this.value = this.value.toUpperCase()"/>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="secao">Deficiência:</td>
                                                                <td colspan="6">
                                                                    <select name="deficiencia">
                                                                        <option value="">Não é portador de deficiência</option>
                                                                        <?php
                                                                        $qr_deficiencias = mysql_query("SELECT * FROM deficiencias WHERE status = 'on'");
                                                                        while ($deficiencia = mysql_fetch_assoc($qr_deficiencias)) {
                                                                            ?>
                                                                            <option value="<?= $deficiencia['id'] ?>"<?php if ($row['deficiencia'] == $deficiencia['id']) { ?> selected="selected"<?php } ?>><?= $deficiencia['nome'] ?></option>
                                                                        <?php } ?>
                                                                    </select>    
                                                                </td>
                                                            </tr>
                                                            <tr id="ancora_foto">
                                                                <td class="secao">	
                                                                    Foto:
                                                                </td>
                                                                <td colspan="5"><?= $foto ?>
                                                                    <div id="tablearquivo" style="display:none;">ENVIAR FOTO: <input name="arquivo" type="file" id="arquivo" size="60" /></div>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <table cellpadding="0" cellspacing="1" class="secao">
                                                            <tr>
                                                                <td class="secao_pai" colspan="8">DOCUMENTAÇÃO</td>
                                                            </tr>
                                                            <tr>
                                                                <td width="16%" class="secao">
                                                                    Nº do RG:</td>
                                                                <td width="12%">
                                                                    <input name="rg" type="text" id="rg" size="13" maxlength="14" value="<?= $row['rg'] ?>" class="validate[required]"
                                                                           onkeyup="pula(14, this.id, orgao.id)"/>
                                                                </td>
                                                                <td width="15%" class="secao">Orgão Expedidor:</td>
                                                                <td width="9%">
                                                                    <input name="orgao" type="text" id="orgao" size="8" value="<?= $row['orgao'] ?>"
                                                                           onChange="this.value = this.value.toUpperCase()"/>
                                                                </td>
                                                                <td width="5%" class="secao">UF:</td>
                                                                <td width="7%">
                                                                    <select name="uf_rg" id="uf_rg" >
                                                                        <option value=""></option>
                                                                        <?php
                                                                        $qr_uf = mysql_query("SELECT * FROM uf");
                                                                        while ($row_uf = mysql_fetch_assoc($qr_uf)) {
                                                                            if (isset($row['uf_rg']) && $row['uf_rg'] == $row_uf['uf_sigla']) {
                                                                                echo '<option value="' . $row_uf['uf_sigla'] . '"selected>' . $row_uf['uf_sigla'] . '</option>';
                                                                            } else {
                                                                                echo '<option value="' . $row_uf['uf_sigla'] . '">' . $row_uf['uf_sigla'] . '</option>';
                                                                            }
                                                                        }
                                                                        ?>    
                                                                    </select>
                                                                </td>
                                                                <td width="18%" class="secao">Data Expedição:</td>
                                                                <td width="18%">
                                                                    <input name="data_rg" type="text" size="12" maxlength="10" value="<?= $row['data_rg2'] ?>" id="data_rg" 
                                                                           onkeyup="mascara_data(this);
                                                                                   pula(10, this.id, cpf.id)" />
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td class="secao">CPF:</td>
                                                                <td>
                                                                    <input name="cpf" type="text" id="cpf" size="17" maxlength="14" value="<?= $row['cpf'] ?>" class="validate[required,funcCall[verificaCPF]]" />
                                                                </td>
                                                                <td class="secao">&Oacute;rg&atilde;o Regulamentador:</td>
                                                                <td colspan="3">
                                                                    <input name="conselho" type="text" id="conselho" size="17" value="<?= $row['conselho'] ?>" />
                                                                    <br/><br/>
                                                                    <!--<input type="checkbox" name="verifica_orgao" value="1" <?php echo ($row['verifica_orgao'] == 1) ? 'checked="checked"' : ''; ?>/> Verificado?-->
                                                                </td>
                                                                <td class="secao">Data de emissão:</td>
                                                                <td>
                                                                    <input name="data_emissao" type="text" size="12"  id="data_emissao"
                                                                           onkeyup="mascara_data(this);
                                                                                   pula(10, this.id, reservista.id)" value="<?php echo $row['data_emissao'] ?>"/>    
                                                                </td> 
                                                            </tr>
                                                            <!-- NOVOS CAMPOS-->
                                                            <tr>
                                                                <td class="secao">Número CRT:</td>
                                                                <td>
                                                                    <input name="num_crt" type="text" id="num_crt" size="15"  value="<?= $row['num_crt'] ?>" />
                                                                </td>
                                                                <td class="secao">Emissão CRT:</td>
                                                                <td>
                                                                    <input name="emissao_crt" type="text" size="12" maxlength="10" id="emissao_crt"
                                                                           onkeyup="mascara_data(this);
                                                                                   pula(10, this.id, reservista.id)" value="<?php
                                                                           if ($row['emissao_crt'] && $row['emissao_crt'] != '0000-00-00') {
                                                                               echo date('d/m/Y', strtotime($row['emissao_crt']));
                                                                           }
                                                                           ?>"/>
                                                                </td>
                                                                <td class="secao">Validade CRT:</td>
                                                                <td>
                                                                    <input name="validade_crt" type="text" size="12" maxlength="10" id="validade_crt"
                                                                           onkeyup="mascara_data(this);
                                                                                   pula(10, this.id, reservista.id)" value="<?php
                                                                           if ($row['validade_crt'] && $row['validade_crt'] != '0000-00-00') {
                                                                               echo date('d/m/Y', strtotime($row['validade_crt']));
                                                                           }
                                                                           ?>"/>
                                                                </td>
                                                                <td colspan="2"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="secao">Nº Carteira de Trabalho:</td>
                                                                <td>
                                                                    <input name="trabalho" type="text" id="trabalho" size="15" value="<?= $row['campo1'] ?>" class="validate[required]" />
                                                                </td>
                                                                <td class="secao">Série:</td>
                                                                <td>
                                                                    <input name="serie_ctps" type="text" id="serie_ctps" size="10" value="<?= $row['serie_ctps'] ?>" class="validate[required]" />
                                                                </td>
                                                                <td class="secao">UF:</td>
                                                                <td>
                                                                    <select name="uf_ctps" class="validate[requeired]">
                                                                        <?php
                                                                        $qr_uf = mysql_query("SELECT * FROM uf");
                                                                        while ($row_uf = mysql_fetch_assoc($qr_uf)) {
                                                                            if (isset($row['uf_ctps']) && $row['uf_ctps'] == $row_uf['uf_sigla']) {
                                                                                echo '<option value="' . $row_uf['uf_sigla'] . '" selected>' . $row_uf['uf_sigla'] . '</option>';
                                                                            } else {
                                                                                echo '<option value="' . $row_uf['uf_sigla'] . '">' . $row_uf['uf_sigla'] . '</option>';
                                                                            }
                                                                        }
                                                                        ?> 
                                                                    </select>
                                                                </td>
                                                                <td class="secao">Data carteira de Trabalho:</td>
                                                                <td>  
                                                                    <input name="data_ctps" type="text" size="12" maxlength="10" id="data_ctps" value="<?= $row['data_ctps2'] ?>" 
                                                                           onkeyup="mascara_data(this);
                                                                                   pula(10, this.id, titulo2.id)" />     
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="secao">Nº Título de Eleitor:</td>
                                                                <td>
                                                                    <input name="titulo" type="text" id="titulo2" size="10" value="<?= $row['titulo'] ?>" />
                                                                </td>
                                                                <td class="secao"> Zona:</td>
                                                                <td colspan="3">
                                                                    <input name="zona" type="text" id="zona2" size="3" value="<?= $row['zona'] ?>" />
                                                                </td>
                                                                <td class="secao">Seção:</td>
                                                                <td>
                                                                    <input name="secao" type="text" id="secao" size="3" value="<?= $row['secao'] ?>" />
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="secao">PIS:</td>
                                                                <td>
                                                                    <input name="pis" type="text"  maxlength="11"  id="pis" size="12" value="<?= $row['pis'] ?>" />
                                                                </td>
                                                                <td class="secao">Data PIS:</td>
                                                                <td colspan="3">
                                                                    <input name="data_pis" type="text" size="12" maxlength="10" id="data_pis" value="<?= $row['dada_pis2'] ?>"
                                                                           onkeyup="mascara_data(this);
                                                                                   pula(10, this.id, fgts.id)" />
                                                                </td>
                                                                <td class="secao">FGTS:</td>
                                                                <td>
                                                                    <input name="fgts" type="text" id="fgts" size="10" value="<?= $row['fgts'] ?>" />
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="secao">Certificado de Reservista:</td>
                                                                <td colspan="3">
                                                                    <input name="reservista" type="text" id="reservista" size="18" value="<?= $row['reservista'] ?>" />
                                                                </td>
                                                                <td class="secao">Carteira do SUS:</td>
                                                                <td colspan="3">
                                                                    <input name="carteira_sus" type="text" id="carteira_sus" size="18" value="<?= $row['carteira_sus'] ?>" />
                                                                </td>   
                                                            </tr>

                                                        </table>
                                                        <table cellpadding="0" cellspacing="1" class="secao">
                                                            <tr>
                                                                <td class="secao_pai" colspan="6">BENEFÍCIOS</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="secao">
                                                                    Assistência Médica:</td>
                                                                <td>
                                                                    <label><input name="medica" type="radio" class="reset" value="1" <?= $chek_medi1 ?>/>Sim</label>
                                                                    <label><input name="medica" type="radio" class="reset" value="0" <?= $chek_medi0 ?>/>Não</label> <?= $mensagem_medi ?>
                                                                </td>
                                                                <td class="secao">Tipo de Plano:</td>
                                                                <td>
                                                                    <select name="plano_medico" id="plano_medico">
                                                                        <option value="1" <?= $selected_planoF ?>>Familiar</option>
                                                                        <option value="2" <?= $selected_planoI ?>>Individual</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr id="planosaude" <?= $displayPlanoSaude ?> >
                                                                <td class="secao">Plano de Saúde</td>
                                                                <td colspan="3"><?= montaSelect($arrayPlanoSaude, $row['id_plano_saude'], 'name="id_plano_saude" id="id_plano_saude"') ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="secao">Seguro, Apólice:</td>
                                                                <td>
                                                                    <select name="apolice" id="apolice">
                                                                        <option value="0">Não Possui</option>
                                                                        <?php
                                                                        $result_ap = mysql_query("SELECT * FROM apolice WHERE id_regiao = $row[regiao]", $conn);
                                                                        while ($row_ap = mysql_fetch_array($result_ap)) {
                                                                            if ($row_ap['id_apolice'] == $row['apolice']) {
                                                                                print "<option value = '$row_ap[id_apolice]' selected>$row_ap[razao]</option>";
                                                                            } else {
                                                                                print "<option value = '$row_ap[id_apolice]'>$row_ap[razao]</option>";
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </td>
                                                                <td class="secao">Dependente:</td>
                                                                <td>
                                                                    <input name="dependente" type="text" id="dependente" size="20" value="<?= $row['campo2'] ?>"
                                                                           onChange="this.value = this.value.toUpperCase()"/>
                                                                </td>
                                                            </tr>
                            <!--                                    <tr>
                                                                <td class="secao">Insalubridade:</td>
                                                                <td>
                                                                    <input name="insalubridade" type="checkbox" class="reset" id="insalubridade2" value="1" <?= $chek1 ?>/></td>    
                                                                <td class="secao">Adicional Noturno:</td>
                                                                <td>
                                                                    <label><input name="ad_noturno" type="radio" class="reset" value="1" <?= $checkad_noturno1 ?>/>Sim</label>
                                                                    <label><input name="ad_noturno" type="radio" class="reset" value="0" <?= $checkad_noturno0 ?>/>Não</label>
                                                                </td>
                                                            </tr>-->
                                                            <tr>
                                                                <td class="secao">Desconto de INSS:</td>
                                                                <td><label><input name="desconto_inss" type="checkbox" class="reset" value="1"
                                                                                  onClick="document.getElementById('desconto_inss').style.display = (document.getElementById('desconto_inss').style.display == 'none') ? '' : 'none';"
                                                                                  <?php
                                                                                  if (!empty($row['desconto_inss'])) {
                                                                                      echo 'checked';
                                                                                  }
                                                                                  ?> /></label>
                                                                </td>
                                                                <td class="secao">Integrante do CIPA:</td>
                                                                <td>
                                                                    <label><input name="cipa" type="radio" class="reset" value="1" <?= $checkedcipa1 ?>/>Sim</label>
                                                                    <label><input name="cipa" type="radio" class="reset" value="0" <?= $checkedcipa0 ?>/>Não</label>
                                                                </td>
                                                            </tr>
                                                            <tr>

                                                                                                                        <!--<td class="secao">Pensão Alimentícia:</td>
                                                                                                                        <td>
                                                                                                                            <label><input name="pensao_alimenticia" type="radio" class="reset" value="1" <?php echo ($row['pensao_alimenticia'] > 0) ? "checked='checked'" : ""; ?> />Sim</label>
                                                                                                                            <label><input name="pensao_alimenticia" type="radio" class="reset" value="0" <?php echo ($row['pensao_alimenticia'] == 0) ? "checked='checked'" : ""; ?> />Não</label>
                                                                                                                            <select name="pensao_percentual" style="">
                                                                                                                                <option value="">Selecione uma faixa</option>
                                                                                                                                <option value="0.15" <?php echo ($row['pensao_alimenticia'] == '0.15') ? "selected='selected'" : ""; ?>>15%</option>
                                                                                                                                <option value="0.20" <?php echo ($row['pensao_alimenticia'] == '0.20') ? "selected='selected'" : ""; ?>>20%</option>
                                                                                                                                <option value="0.30" <?php echo ($row['pensao_alimenticia'] == '0.30') ? "selected='selected'" : ""; ?>>30%</option>
                                                                                                                            </select>
                                                                                                                        </td>-->

                                                                <td class="secao">Recebendo Seguro Desemprego?</td>
                                                                <td>
                                                                    <label><input name="seguro_desemprego" type="checkbox" class="reset" value="1" <?= ($row['seguro_desemprego'] == 1) ? 'checked' : '' ?> /></label>
                                                                </td>
                                                                <td class="secao" width="296">Gratificação:</td>
                                                                <td><input name="gratificacao" type="text" id="gratificacao" placeholder="00%" class="porcentagem" value="<?php echo $row['gratificacao'] ?>" /></td>
                                                                <!--                                                                                                                    <td colspan="3" class="select_gratificacao" style="display: none">Escolha a Gratificação: </td>-->
                                                                <!--                                                                                                                    <td colspan="3" class="select_gratificacao" style="display: none">-->
                                                                <!--                                                                                                                        <select name="select_gratificacao" id="select_gratificacao">-->
                                                                <!-- <option value="80300" <?php if ($flag == 1) echo $selected; ?>>GRATIFICAÇÕES POR COMPLEXIDADE</option>
                                                                                                                            <option value="80301" <?php if ($flag == 2) echo $selected; ?>>GRATIFICAÇÕES POR PRODUTIVIDADE</option>
                                                                                                                        </select>
                                                                                                                    </td>-->

                                                            </tr>
                                                            <tr class="secao">
                                                                <td class="secao" width="197">Pensão Alimentícia:</td>
                                                                <td><input name="pensao" type="checkbox" data-key="<?php echo $id_clt; ?>" class="reset" id="pensao" onClick="document.getElementById('tablePensao').style.display = (document.getElementById('tablePensao').style.display == 'none') ? '' : 'none';" value="1" <?= $chekPensao ?> /></td>
                                                                <td class="secao" width="296">Ajuda de Custo:</td>
                                                                <td><input name="ajuda_custo" type="text" id="ajuda_custo" placeholder="Valo..." class="valor" value="<?php echo $row['ajuda_custo'] ?>" /></td>
                                                            </tr>
                                                            <tr class="secao">
                                                                <td class="secao" width="296">Adicional de Transferência:</td>
                                                                <td><?= montaSelect([0 => "« Selecione »", 1 => "Valor Fixo", 2 => "25% Sobre Salário Base", 3 => "35% Sobre Salário Base"], $row['ad_transferencia_tipo'], "id='ad_transferencia_tipo' name='ad_transferencia_tipo'"); ?></td>
                                                                <td class="secao" width="296"></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr class="box_ad_transferencia_valor secao <?= ($row['ad_transferencia_tipo'] != 1) ? 'hide' : null ?>">
                                                                <td class="secao" width="296">Valor Ad. de Transferência</td>
                                                                <td><input placeholder="Valor..." type="text" id="ad_transferencia_valor" name="ad_transferencia_valor" class="valor" value="<?= $row['ad_transferencia_valor'] ?>"/></td>
                                                                <td class="secao" width="296"></td>
                                                                <td></td>
                                                            </tr>


                                                                    <!--                                                                                                    <tr>
                                                                                                                                <td class="secao">Não recebe Vale Refeição</td>
                                                                                                                                <td>
                                                                                                                                    <label><input name="nao_recebe_vr" type="checkbox" class="reset" value="1" <?= ($row['nao_recebe_vr'] == 1) ? 'checked' : '' ?> /></label>
                                                                                                                                </td>
                                                                                                                                <td class="secao"></td>
                                                                                                                                <td></td>
                                                                                                                            </tr>-->
                                                            <?php
//                                    if($_COOKIE['logado']==202){ 
//
//                                                                                                    $sql_va = "SELECT A.*,B.*, A.`status` AS status_tipo, B.`status` AS status_categoria
//                                                    FROM rh_va_tipos AS A
//                                                    LEFT JOIN rh_va_categorias AS B ON(A.id_va_categoria=B.id_va_categoria)
//                                                    WHERE A.`status`=1 AND B.`status`=1";
//
//                                                                                                    $result_va = mysql_query($sql_va);
//
//                                                                                                    $arr_va = array();
//
//                                                                                                    while ($row_va = mysql_fetch_array($result_va)) {
//                                                                                                        $arr_va[$row_va['id_va_categoria']] = $row_va['nome_categoria'];
//                                                                                                        $arr_va_campo[$row_va['id_va_categoria']] = $row_va['campo_clt'];
//                                                                                                        $arr_va_tipos[$row_va['id_va_categoria']][$row_va['id_va_tipos']] = $row_va['nome_tipo'];
//                                                                                                    }
//                                                                                                    foreach ($arr_va as $k => $row_va) {
                                                            ?>
            <!--                                                                                                        <tr>
                                                                            <td class="secao">Vale <?= $row_va; ?>:</td>
                                                                            <td  <?= ($row_va !== 'Refeição') ? 'colspan="3"' : '' ?>>
                                                                                <select name="<?= $arr_va_campo[$k]; ?>">
                                                                                    <option value="0" selected="selected">NÃO POSSUI</option>
                                                            <?php
                                                            foreach ($arr_va_tipos[$k] as $i => $row_tipo) {
                                                                $selected = ($row[$arr_va_campo[$k]] == $i) ? ' selected="selected "' : ' ';
                                                                ?>
                                                                                                    <option value="<?= $i; ?>" <?= $selected; ?> ><?= $i . ' - ' . $row_tipo; ?></option>
    <?php } ?>
                                                                                </select>
                                                            <?php if ($row_va === 'Refeição') { ?>
                                                                                            </td>
                                                                                            <td class="secao">Valor:</td>
                                                                                            <td>
                                                                                                <input type="text" name="valor_refeicao" id="valor_refeicao" class="money" value="<?= $row['valor_refeicao'] ?>"/>
                                                            <?php } ?>
                                                                            </td>
                                                                        </tr>-->
    <?php
    //  } 
//                                                                                                    }
    ?>
            <!--                                                                                                    <tr>
                                                                        <td class="secao">Vale Transporte:</td>
                                                                        <td colspan="3"><input name="transporte" type="checkbox" class="reset" id="transporte2" onClick="document.getElementById('tablevale').style.display = (document.getElementById('tablevale').style.display == 'none') ? '' : 'none';" value="1" <?= $chek2 ?> /></td>
                                                                    </tr> -->
                                                            <?php
                                                            if (!empty($dadosFavorec)) {
                                                                $chek4 = " checked='checked'";
                                                            } else {
                                                                $chek4 = "";
                                                            }
                                                            ?>


                                                        </table>

                                                        <table cellpadding="0" cellspacing="1" class="secao" id="desconto_inss" <?php
                                                            if (empty($row['desconto_inss'])) {
                                                                echo 'style="display:none;"';
                                                            }
                                                            ?>>
                                                            <tbody>
                                                                <tr>
                                                                    <td colspan="10" class="secao_pai">PROPORCIONALIDADE DE INSS</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="secao">Tipo de Desconto:</td>
                                                                    <td colspan="2">
                                                                        <label><input name="tipo_desconto_inss" type="radio" class="reset" value="isento"
                                                                                      <?php
                                                                                      if ($row['tipo_desconto_inss'] == 'isento' or empty($row['tipo_desconto_inss'])) {
                                                                                          echo 'checked';
                                                                                      }
                                                                                      ?>/>
                                                                            Suspen&ccedil;&atilde;o de Recolhimento<br />
                                                                        </label>
                                                                        <label><input name="tipo_desconto_inss" type="radio" class="reset" value="parcial"
                                                                                      <?php
                                                                                      if ($row['tipo_desconto_inss'] == 'parcial') {
                                                                                          echo 'checked';
                                                                                      }
                                                                                      ?>/>Parcial</label>
                                                                    </td>

                                                                    <?php
                                                                    $sqlCnpj = "SELECT cnpj_outro_vinculo FROM rh_inss_outras_empresas WHERE id_clt= $id_clt AND status = 1";
                                                                    $queryCnpj = mysql_query($sqlCnpj);
                                                                    $arrCnpj = mysql_fetch_assoc($queryCnpj);
                                                                    ?>

                                                                    <td class="secao">CNPJ Outro Vinculo:</td>
                                                                    <td colspan="2">
                                                                        <input value="<?php echo $arrCnpj['cnpj_outro_vinculo'] ?>" name='cnpj' type='text' id='cnpj' size="19" maxlength='18' OnKeyPress="formatar('##.###.###/####-##', this)" onkeyup="pula(18, this.id, e_endereco.id)" />
                                                                    </td>
                                                                    <td class="secao">Trabalha em outra empresa?<br /></td>
                                                                    <td colspan="2">
                                                                        <label><input name="trabalha_outra_empresa" type="radio" class="reset" onClick="$('.outra_empresa').fadeIn();
                                                                                $('#novo_inss').fadeIn();" value="sim"
                                                                                      <?php
                                                                                      if ($row['trabalha_outra_empresa'] == 'sim') {
                                                                                          echo 'checked';
                                                                                      }
                                                                                      ?>/>Sim</label>
                                                                        <label><br />
                                                                            <input name="trabalha_outra_empresa" type="radio" class="reset" onClick="$('.outra_empresa').fadeOut();
                                                                                    $('#novo_inss').fadeOut();" value="nao"
                                                                                   <?php
                                                                                   if ($row['trabalha_outra_empresa'] == 'nao') {
                                                                                       echo 'checked';
                                                                                   }
                                                                                   ?>/>Não</label>
                                                                    </td>
                                                                    <td colspan="3" style="vertical-align:middle; text-align: center;">
                                                                        <button type="button" id="novo_inss" <?php echo ($row['trabalha_outra_empresa'] == 'nao' or empty($row['trabalha_outra_empresa'])) ? 'style="display:none;"' : '' ?>>+ Novo Período</button>&emsp;
                                                                    </td>
                                                                </tr>
    <?php foreach ($arr_inss as $inss_row) { ?>
                                                                    <tr class="outra_empresa" <?php echo ($row['trabalha_outra_empresa'] == 'nao' or empty($row['trabalha_outra_empresa'])) ? 'style="display:none;"' : '' ?>>
                                                                        <td class="secao">Salário da outra empresa:</td>
                                                                        <td>
                                                                            <input name="salario_outra_empresa[]" type="text" size="12" class="formata_valor" value="<?= $inss_row['salario'] ?>"/>
                                                                            <input name="id_inss[]" type="hidden" value="<?= $inss_row['id_inss'] ?>"/>
                                                                        </td>
                                                                        <td class="secao">Desconto da outra empresa:</td>
                                                                        <td>
                                                                            <input name="desconto_outra_empresa[]" type="text" size="12" class="formata_valor" value="<?= $inss_row['desconto'] ?>"/>
                                                                        </td>
                                                                        <td class="secao">Início:</td>
                                                                        <td>
                                                                            <input type="text" name="inicio_inss[]" class="data_inss" value="<?= converteData($inss_row['inicio'], 'd/m/Y') ?>"/>
                                                                        </td>
                                                                        <td class="secao">Fim:</td>
                                                                        <td>
                                                                            <input type="text" name="fim_inss[]" class="data_inss" value="<?= converteData($inss_row['fim'], 'd/m/Y') ?>"/>
                                                                        </td>
                                                                        <td colspan="2" style="vertical-align:middle; text-align: center;">
                                                                            <button type="button" class="excluir_inss" data-id="<?= $inss_row['id_inss'] ?>">Excluir</button>
                                                                        </td>
                                                                    </tr>
    <?php } ?>
                                                            </tbody>
                                                        </table>

                                                        <table cellpadding="0" cellspacing="1" style="background-color: white; <?= $disable_vale ?>" class="secao" id="tablevale" >
                                                            <tr>
                                                                <td class="secao_pai" colspan="8">VALE TRANSPORTE</td>
                                                            </tr>
                                                            <?php
                                                            for ($qtd_val = 1; $qtd_val <= 5; $qtd_val++) {
                                                                if ($qtd_val == 1 || $res_vt_linhas['id_vt_linha'] != 0 || $res_vt_linhas['id_vt_linha'] == 0 && $res_vt_valores['id_valor'] != 0) {
                                                                    $hideRow = 'class="show"';
                                                                } else {
                                                                    $hideRow = 'class="hide"';
                                                                }
                                                                ?>
                                                                <tr data-vtRow="<?php echo $qtd_val ?>" id="vtRow<?php echo $qtd_val ?>" <?php echo $hideRow; ?>>
                                                                    <td class="secao">Opção <?php echo $qtd_val; ?>:</td>
                                                                    <td>
                                                                        <select data-linha='<?php echo $qtd_val; ?>' class="opLinha" name="vt_linha<?php echo $qtd_val; ?>" id="vt_linha<?php echo $qtd_val; ?>">
                                                                            <option value="0">Linha não informada</option>

                                                                            <?php
                                                                            // TRAZ VALORES DE VT
                                                                            //                                                    $qry_vt_valores = "SELECT *
                                                                            //                                                                FROM rh_vt_valores AS A
                                                                            //                                                                WHERE A.status_reg = 1";
                                                                            //                                                    $sql_vt_valores = mysql_query($qry_vt_valores) or die("ERRO qry_vt_valores");

                                                                            $qry_vt_linhas = "SELECT * FROM rh_vt_linha AS A ORDER BY A.nome";
                                                                            $sql_vt_linhas = mysql_query($qry_vt_linhas) or die("ERRO qry_vt_valores");

                                                                            while ($res_vt_linhas = mysql_fetch_assoc($sql_vt_linhas)) {
                                                                                switch ($qtd_val) {
                                                                                    case 1: $linha = $row_vale['id_linha1'];
                                                                                        break;
                                                                                    case 2: $linha = $row_vale['id_linha2'];
                                                                                        break;
                                                                                    case 3: $linha = $row_vale['id_linha3'];
                                                                                        break;
                                                                                    case 4: $linha = $row_vale['id_linha4'];
                                                                                        break;
                                                                                    case 5: $linha = $row_vale['id_linha5'];
                                                                                        break;
                                                                                }
                                                                                $selLinha = '';
//                                                                                                                                    echo "<!--";
//                                                                                                                                    echo $row_vale['vt_linha1'];
//                                                                                                                                    echo "-->";
                                                                                if ($linha == $res_vt_linhas['id_vt_linha']) {
                                                                                    $selLinha = 'selected="selected"';
                                                                                }
                                                                                ?>
                                                                                <option value="<?php echo $res_vt_linhas['id_vt_linha']; ?>" <?php echo $selLinha; ?>><?php echo "{$res_vt_linhas['nome']} (Linha {$res_vt_linhas['codigo']}) - " . formataMoeda($res_vt_linhas['valor_tarifa']); ?></option>
                                                                        <?php } ?>
                                                                        </select>
                                                                    </td>
                                                                    <td class="secao">Valor:</td>
                                                                    <td>
                                                                        <?php
//                                                                                                                            if($row_vale["id_linha{$qtd_val}"] > 0){
//                                                                                                                                echo "<div id='valvt{$qtd_val}'>" . formataMoeda($row_vale["val{$qtd_val}"]) . "</div>";
//                                                                                                                            } else {
                                                                        if ($row_vale["id_linha{$qtd_val}"] > 0) {
                                                                            $classDiv = 'show';
                                                                            $classSel = 'hide';
                                                                        } else {
                                                                            $classDiv = 'hide';
                                                                            $classSel = 'show';
                                                                        }
//                                                                                                                            
                                                                        ?>
                                                                        <div class="<?php echo $classDiv ?>" style="width:79px;" id="<?php echo 'valvt' . $qtd_val; ?>"><?php echo formataMoeda($row_vale["val{$qtd_val}"]); ?></div>
                                                                        <select data-valor='<?php echo $qtd_val; ?>' class="opValor <?php echo $classSel ?>" name="vt_valor<?php echo $qtd_val; ?>" id="vt_valor<?php echo $qtd_val; ?>">
                                                                            <option value="0">Não Tem</option>

                                                                            <?php
                                                                            // TRAZ VALORES DE VT
                                                                            $qry_vt_valores = "SELECT * FROM rh_vt_valores AS A
                                                                                                                                                  WHERE A.status_reg = 1";
                                                                            $sql_vt_valores = mysql_query($qry_vt_valores) or die("ERRO qry_vt_valores");

                                                                            while ($res_vt_valores = mysql_fetch_assoc($sql_vt_valores)) {
                                                                                ?>
                                                                                <option value="<?php echo $res_vt_valores['id_valor']; ?>" <?php echo selected($res_vt_valores['id_valor'], $row_vale["id_valor{$qtd_val}"]); ?>>
                                                                            <?php echo $res_vt_valores['valor']; ?>
                                                                                </option>
        <?php } ?>
                                                                        </select>
        <?php //                                                                                                                            }    ?>
                                                                    </td>
                                                                    <td class="secao">Quantidade:</td>
                                                                    <td>
                                                                        <input data-qnt="<?php echo $qtd_val; ?>" class="vtQnt" name="vt_qtd<?php echo $qtd_val; ?>" type="number" id="vt_qtd<?php echo $qtd_val; ?>" value="<?php echo $row_vale["qtd{$qtd_val}"]; ?>" size="3" />
                                                                    </td>
                                                                    <td class="secao">Num. Cartão:</td>
                                                                    <td colspan="6">
                                                                <?php $vtCard = $row_vale["cartao{$qtd_val}"]; ?>
                                                                        <input data-card="<?php echo $qtd_val; ?>" class="vtCard" name="vt_card<?php echo $qtd_val; ?>" type="text" id="vt_card<?php echo $qtd_val; ?>" value="<?= $vtCard ?>"/>
                                                                    </td>
                                                                </tr>
    <?php } ?>
                                                            <tr>
                                                                <td class="secao_pai" colspan="8"></td>
                                                            </tr>
    <!--                                                        <tr>
                                                        <td class="secao">Numero Cartão 1:</td>
                                                        <td>
                                                            <input name="num_cartao" type="text" id="num_cartao" size="20" value="<?= $row_vale['cartao1'] ?>"
                                                                   onChange="this.value = this.value.toUpperCase()"/>
                                                        </td>
                                                        <td class="secao">Numero Cartão 2:</td>
                                                        <td>
                                                            <input name="num_cartao2" type="text" id="num_cartao2" size="20" value="<?= $row_vale['cartao2'] ?>"
                                                                   onChange="this.value = this.value.toUpperCase()"/>
                                                        </td>
                                                    </tr>-->
                                                        </table>


                                                        <script>
                                                            $(function () {

                                                                $("#cpf_favorecido").mask("999.999.999-99");
                                                                $(".cpf_favorecido").mask("999.999.999-99");
    //                                                                                                    $(".aliFav").mask("9.99");
                                                                $("#valor_hora").maskMoney();


                                                                $("body").on('blur', '.aliFav', function () {
                                                                    t = $(this);
                                                                    tVal = t.val();
                                                                    tVal = tVal.trim();

                                                                    if (tVal == '') {
                                                                        t.val(0);
                                                                    }
                                                                });

                                                                /**
                                                                 * ADICIONANDO FAVORECIDO NA PENSAO
                                                                 */
                                                                $("body").on("click", ".add_favorecido", function () {
                                                                    var ultRows = $("#tablePensao tbody > tr:last").data("key");
                                                                    var contador = parseInt($("input[name='contador']").val()) + 1;
                                                                    var seletor = "";
                                                                    if (typeof ultRows == "undefined") {
                                                                        seletor = ".rows_pensao";
                                                                    } else {
                                                                        seletor = ".rows_pensao_" + ultRows;
                                                                    }

                                                                    $('#tablePensao').append("<tbody class='rows_pensao_" + contador + "'>\n\
                                                                    <tr>\n\
                                                                        <td class='secao' width='235px'>Nome Favorecido:</td>\n\
                                                                        <td><input type='text' name='nome_favorecido[]' id='nome_favorecido_" + contador + "'  value='' /></td>\n\
                                                                        <td class='secao'>CPF Favorecido:</td>\n\
                                                                        <td><input type='text' name='cpf_favorecido[]' id='cpf_favorecido_" + contador + "' class='maskCpf'  value='' /></td>\n\
                                                                        <td class='secao'>Aliquota:</td>\n\
                                                                        <td><input type='text' name='aliquota_favorecido[]' placeholder='0.000' class='aliFav' id='aliquota_favorecido_" + contador + "'  value='' /></td>\n\\n\
                                                                        <td class='remover' rowspan='3'><a href='javascript:;' class='remover_favorecido' data-count='" + contador + "'>-</a></td>\n\
                                                                    </tr>\n\
                                                                    <tr>\n\
                                                                        <td class='secao'>Banco:</td>\n\
                                                                        <td colspan='1'><select name='id_lista_banco_favorecido[]' id='id_lista_banco_" + contador + "' class='validate[required]' >" + $('#id_lista_banco').html().replace('selected', ' ') + "</select></td>\n\
                                                                        <td class='secao'>Agência:</td>\n\
                                                                        <td colspan='1'>\n\
                                                                            <input type='text' name='agencia_favorecido[]' id='agencia_favorecido_" + contador + "'  value='' />\n\
                                                                        </td>\n\
                                                                        <td class='secao'>Conta:</td>\n\
                                                                        <td colspan='1'>\n\
                                                                            <input type='text' name='conta_favorecido[]' id='conta_favorecido_" + contador + "'  value='' />\n\n\
                                                                        </td>\n\
                                                                    </tr>\n\
                                                                    <tr>\n\
                                                                            <td class='secao'>Oficio:</td>\n\
                                                                        <td colspan='1'><input type='text' name='oficio[]' id='oficio' value=''></td>\n\
                                                                        <td class='secao'>Base de Calculo:</td>\n\
                                                                        <td colspan='1'>\n\
                                                                            <select name='base[]' id='base' class='mudaBasePensao'>\n\
                                                                                <option value=''>--Selecione--</option>\n\
                                                                                <option value='1'>Salário Líquido</option>\n\
                                                                                <option value='2'>Salário Bruto</option>\n\
                                                                                <option value='3'>Salário Mínimo</option>\n\
                                                                                <option value='4'>Valor Fixo</option>\n\
                                                                                <option value='5'>1/3 Sobre Líquido</option>\n\
                                                                                <option value='6'>Porcentagem Sobre Salário Mínimo</option>\n\
                                                                            </select>\n\
                                                                        </td> \n\\n\
                                                                        <td colspan='1' class='secao'>\n\
                                                                            <label class='quantSalMinimo'>Quantidade</label>\n\
                                                                            <label class='valorfixo'>Valor</label>\n\
                                                                        </td>\n\
                                                                        <td colspan='1'>\n\
                                                                            <select name='quant[]' id='quant' class='quantSalMinimo'>\n\
                                                                                <option value='1'>1</option>\n\
                                                                                <option value='2'>2</option>\n\
                                                                                <option value='3'>3</option>\n\
                                                                                <option value='4'>4</option>\n\
                                                                                <option value='5'>5</option>\n\
                                                                            </select>    \n\
                                                                            <input type='text' name='valorfixo[]' id='valorfixo' class='formata_valor valorfixo' value=''>\n\
                                                                        </td>\n\
                                                                    </tr> \n\
                                                                    <tr>\n\
                                                                        <td class='secao'>Incide Em:</td>\n\
                                                                        <td colspan='6'>\n\
                                                                            <span><input type='checkbox' name='incide_ferias[" + $(".incide_ferias").length + "] class='incide_ferias' /> Férias</span>\n\
                                                                            <span><input type='checkbox' name='incide_rescisao[" + $(".incide_ferias").length + "]' /> Rescisão</span>\n\
                                                                            <span><input type='checkbox' name='incide_13[" + $(".incide_ferias").length + "]' /> Décimo Terceiro</span>\n\
                                                                        </td>\n\
                                                                    </tr>\n\
                                                                    </tbody>");
                                                                    $('.rows_pensao_' + contador).find('.formata_valor').priceFormat({
                                                                        prefix: '',
                                                                        centsSeparator: ',',
                                                                        thousandsSeparator: '.'
                                                                    });
                                                                    $(".maskCpf").mask("999.999.999-99");

                                                                    $("#contador").val(contador);
                                                                });

                                                                /**
                                                                 * REMOVENDO FAVORECIDO DE PENSAO
                                                                 */
                                                                $("body").on("click", ".remover_favorecido", function () {
                                                                    var rows = $(this).data("count");
                                                                    $(".rows_pensao_" + rows).remove();
                                                                });

                                                                /**
                                                                 * REMOVENDO FAVORECIDO DO BANDO DE DADOS
                                                                 */

                                                                $('body').on('click', '.remover_favorecido_db', function () {
                                                                    var key = $(this).data("key");
                                                                    $.ajax({
                                                                        url: "",
                                                                        type: "POST",
                                                                        dataType: "json",
                                                                        data: {
                                                                            method: "removerFavorecido",
                                                                            id: key
                                                                        },
                                                                        success: function (data) {
                                                                            if (data.status) {
                                                                                $(".remover_" + key).remove();
                                                                            }
                                                                        }
                                                                    });
                                                                });

                                                                /**
                                                                 * LIMPANDO DADOS DE FAVORECIDO CASO O CHECKBOX DE PENSAO FOR DESMARCADO
                                                                 */
                                                                //                                        $("#pensao").click(function(){
                                                                //                                            var clt = $(this).data("key");
                                                                //                                            if($(this).is(":not(:checked)")){
                                                                //                                                $.ajax({
                                                                //                                                    url:"",
                                                                //                                                    type:"POST",
                                                                //                                                    dataType:"json",
                                                                //                                                    data:{
                                                                //                                                        method:"removendoFavorecidos",
                                                                //                                                        id_clt:clt
                                                                //                                                    },
                                                                //                                                    success: function(data){
                                                                //                                                        if(data.status){
                                                                //                                                            
                                                                //                                                        }                  
                                                                //                                                    }
                                                                //                                                });
                                                                //                                            } 
                                                                //                                        });

                                                            });
                                                        </script>
                                                        <table cellpadding="0" cellspacing="1" class="secao" id="tablePensao" <?= $disable_pensao ?>>
                                                            <tr>
                                                                <td class="secao_pai" colspan="7">PENSÃO ALIMENTÍCIA</td>
                                                            </tr>
                                                            <?php
                                                            $query_dados_pensao = mysql_query("SELECT *, IF(A.data_nasc_dependente != 0, DATE_FORMAT(A.data_nasc_dependente,'%d/%m/%Y'), '') data_nasc_dependente FROM favorecido_pensao_assoc A "
                                                                    . "left join tipo_dependente B ON (A.id_tipo_dependente = B.id_tipo_dependente) "
                                                                    . "WHERE id_clt  = '{$id_clt}' AND status_reg = 1");
                                                            ?>

                                                            <?php $rows = ""; ?>
                                                            <?php
                                                            if (mysql_num_rows($query_dados_pensao) > 0) {
                                                                $cont = 0;
                                                                //echo '<pre>'.json_encode($rows_pensao = mysql_fetch_assoc($query_dados_pensao), JSON_PRETTY_PRINT).'</pre>';
                                                                //var_dump($rows_pensao = mysql_fetch_assoc($query_dados_pensao));
                                                                //die();
                                                                ?>


                                                                <?php
                                                                $iDep = 0;
                                                                $gambiNomesPensao = '';
                                                                while ($rows_pensao = mysql_fetch_assoc($query_dados_pensao)) {
                                                                    $gambiNomesPensao .= ',' . $rows_pensao['nome_dependente'];
                                                                    ?>
                                                                    <tbody class="rows_pensao_<?php echo $rows_pensao['id']; ?> remover_<?php echo $rows_pensao['id']; ?>" data-key="<?php echo $rows_pensao['id']; ?>">
                                                                        <tr>
                                                                            <td class="secao">Nome Favorecido:</td>
                                                                            <td colspan="1">
                                                                                <input class="input_nome_fav" type="text" name="nome_favorecido[]" id="nome_favorecido"  value="<?php echo isset($rows_pensao['favorecido']) ? $rows_pensao['favorecido'] : ""; ?>" />
                                                                                <input type="hidden" name="id_favorecido[]" id="id_favorecido"  value="<?php echo isset($rows_pensao['id']) ? $rows_pensao['id'] : ""; ?>" /></td>
                                                                            </td>
                                                                            <td class="secao">CPF Favorecido:</td>
                                                                            <td colspan="1"><input type="text" name="cpf_favorecido[]" id="cpf_favorecido" class="cpf_favorecido" value="<?php echo isset($rows_pensao['cpf']) ? $rows_pensao['cpf'] : ""; ?>" /></td>
                                                                            <td class="secao">Aliquota:</td>
                                                                            <td colspan="1"><input type="text" name="aliquota_favorecido[]" placeholder='0.000' class="aliFav" id="aliquota_favorecido"  value="<?php echo isset($rows_pensao['aliquota']) ? $rows_pensao['aliquota'] : ""; ?>" /></td>
            <?php if ($rows != $rows_pensao['id_clt']) { ?>
                                                                                <?php $rows = $rows_pensao['id_clt']; ?>
                                                                                <td class="adicionar" rowspan="3">
                                                                                    <a href="javascript:;" class="add_favorecido">+</a>
                                                                                    <input type="hidden" name="contador" id="contador" value="0" />
                                                                                </td>
                                                                            <?php } else { ?>
                                                                                <td class="" rowspan="3">
                                                                                    <a href="javascript:;" class="remover_favorecido_db" data-key="<?php echo $rows_pensao['id']; ?>">-</a>
                                                                                </td>
            <?php } ?>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="secao">Banco:</td>
                                                                            <td colspan="1"><?php echo montaSelect($arrayBancos, $rows_pensao['id_lista_banco'], "name='id_lista_banco_favorecido[]' id='id_lista_banco' class='validate[required]'"); ?></td>
                                                                            <td class="secao">Agência:</td>
                                                                            <td colspan="1">
                                                                                <input type="text" name="agencia_favorecido[]" id="agencia_favorecido"  value="<?php echo isset($rows_pensao['agencia']) ? $rows_pensao['agencia'] : ""; ?>" />
                                                                            </td>
                                                                            <td class="secao">Conta:</td>
                                                                            <td colspan="1">
                                                                                <input type="text" name="conta_favorecido[]" id="conta_favorecido"  value="<?php echo isset($rows_pensao['conta']) ? $rows_pensao['conta'] : ""; ?>" />
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="secao">Oficio:</td>
                                                                            <td colspan="1"><input type="text" name="oficio[]" id="oficio" value="<?php echo isset($rows_pensao['oficio']) ? $rows_pensao['oficio'] : ""; ?>" /></td>
                                                                            <td class="secao">Base de Calculo:</td>
                                                                            <?php
//                                                                                                                $queryVerificaBasePensao = "SELECT * FROM favorecido_pensao_assoc AS A  
//                                                                                                                  WHERE A.id_clt = {$rows_pensao['id_clt']}";
//
//                                                                                                                $sqlVerificaBasePensao = mysql_query($queryVerificaBasePensao) or die("Erro ");
//                                                                                                                
//
//                                                                                                                while ($rowsVerificaBasePensao = mysql_fetch_assoc($sqlVerificaBasePensao)) {
                                                                            $base = 0;
                                                                            $valorFixo = 0;
                                                                            $incide_ferias = false;
                                                                            $incide_rescisao = false;
                                                                            $incide_13 = false;
                                                                            $quant = $rows_pensao['quantSalMinimo'];

                                                                            if ($rows_pensao['sobreSalLiquido'] == 1) {
                                                                                $base = 1;
                                                                            }
                                                                            if ($rows_pensao['sobreSalBruto'] == 1) {
                                                                                $base = 2;
                                                                            }
                                                                            if ($rows_pensao['sobreSalMinimo'] == 1) {
                                                                                $base = 3;
                                                                            }
                                                                            if ($rows_pensao['valorfixo'] > 0) {
                                                                                $base = 4;
                                                                                $valorFixo = $rows_pensao['valorfixo'];
                                                                            }
                                                                            if ($rows_pensao['umTercoSobreLiquido'] == 1) {
                                                                                $base = 5;
                                                                            }
                                                                            if ($rows_pensao['sobrePorcentagemSalMinimo'] == 1) {
                                                                                $base = 6;
                                                                            }

//                                                                                                                    $incide_ferias = $rowsVerificaBasePensao['incide_ferias'];
//                                                                                                                    $incide_rescisao = $rowsVerificaBasePensao['incide_rescisao'];
//                                                                                                                    $incide_13 = $rowsVerificaBasePensao['incide_13'];
//                                                                                                                }
                                                                            ?>
                                                                            <td colspan="1">
                                                                                <select name="base[]" id="base" class="mudaBasePensao">
                                                                                    <option value="">--Selecione-- <?php echo $base; ?></option>
                                                                                    <option value="1" <?php
                                                                        if ($base == 1) {
                                                                            echo " selected='selected' ";
                                                                        }
                                                                            ?>>Salário Liquido</option>
                                                                                    <option value="2" <?php
                                                                        if ($base == 2) {
                                                                            echo " selected='selected' ";
                                                                        }
                                                                            ?>>Salário Bruto</option>
                                                                                    <option value="3" <?php
                                                                        if ($base == 3) {
                                                                            echo " selected='selected' ";
                                                                        }
                                                                            ?>>Salário Mínimo</option>
                                                                                    <option value="4" <?php
                                                                        if ($base == 4) {
                                                                            echo " selected='selected' ";
                                                                        }
                                                                            ?>>Valor Fixo</option>
                                                                                    <option value="5" <?php
                                                                        if ($base == 5) {
                                                                            echo " selected='selected' ";
                                                                        }
                                                                            ?>>1/3 Sobre Líquido</option>
                                                                                    <option value="6" <?php
                                                                        if ($base == 6) {
                                                                            echo " selected='selected' ";
                                                                        }
                                                                        ?>>Porcentagem Sobre Salário Mínimo</option>
                                                                                </select>    
                                                                            </td>
                                                                            <td colspan="1" class="secao">
                                                                                <label class="quantSalMinimo">Quantidade</label>
                                                                                <label class="valorfixo">Valor</label>
                                                                            </td>
                                                                            <td colspan="1">
                                                                                <select name="quant[]" id="quant" class="quantSalMinimo">
                                                                                    <option value="1" <?php
                                                                        if ($quant == 1) {
                                                                            echo " selected='selected' ";
                                                                        }
                                                                            ?>>1</option>
                                                                                    <option value="2" <?php
                                                                        if ($quant == 2) {
                                                                            echo " selected='selected' ";
                                                                        }
                                                                            ?>>2</option>
                                                                                    <option value="3" <?php
                                                                        if ($quant == 3) {
                                                                            echo " selected='selected' ";
                                                                        }
                                                                            ?>>3</option>
                                                                                    <option value="4" <?php
                                                                        if ($quant == 4) {
                                                                            echo " selected='selected' ";
                                                                        }
                                                                            ?>>4</option>
                                                                                    <option value="5" <?php
                                                                        if ($quant == 5) {
                                                                            echo " selected='selected' ";
                                                                        }
                                                                        ?>>5</option>
                                                                                </select>    
                                                                                <input type="text" name="valorfixo[]" id="valorfixo" class="formata_valor valorfixo" value="<?= $valorFixo; ?>" />
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="secao">Incide Em:</td>
                                                                            <td colspan="6">

                                                                                <input type="checkbox" class='incide_ferias' name="incide_ferias[<?= $cont ?>]" value="1" <?= ($rows_pensao['incide_ferias'] == 1) ? "checked=checked" : "" ?>> Férias
                                                                                    <input type="checkbox" name="incide_rescisao[<?= $cont ?>]" value="1" <?= ($rows_pensao['incide_rescisao'] == 1) ? "checked=checked" : "" ?>> Rescisão
                                                                                        <input type="checkbox" name="incide_13[<?= $cont ?>]" value="1" <?= ($rows_pensao['incide_13'] == 1) ? "checked=checked" : "" ?>> Décimo Terceiro
                                                                                            </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td class="secao" width="235px">Dependente:</td>
                                                                                                <td colspan="1">
                                                                                                    <select id="selDepPensao<?= $iDep ?>" class="select_dependentes" name="select_dependentes[]">
            <!--                                                                                                                                                <option value="<?= !isset($rows_pensao['nome_dependente']) || ($rows_pensao['nome_dependente'] == "" ) ? 99 : 1 ?>"><?= isset($rows_pensao['nome_dependente']) ? ($rows_pensao['nome_dependente']) : "SELECIONE UM DEPENDENTE" ?></option>                                                                                                                                                -->
                                                                                                    </select> 
                                                                                                </td> 
                                                                                                <td class="secao">CPF Dependente:</td>
                                                                                                <td class="td_cpf" colspan="1"><input type="text" name="cpf_dependente_fav[]" value="<?= isset($rows_pensao['cpf_dependente']) ? ($rows_pensao['cpf_dependente']) : "" ?>" class="cpf_dependente"/></td>
                                                                                                <td class="secao">Data de Nascimento:</td>
                                                                                                <td class="td_dtnasc" colspan="2"><input type="text" name="dt_nasc_dependente[]" value="<?= isset($rows_pensao['data_nasc_dependente']) ? ($rows_pensao['data_nasc_dependente']) : "" ?>" class="dt_nasc_dependente"/></td>
                                                                                            </tr> 
                                                                                            <tr>
                                                                                                <td class="secao" width="235px">Tipo de Dependente:</td>
                                                                                                <td colspan="6">
                                                                                                    <select class="tipo_de_dependente" name="tipo_de_dependente[]">                                                                                                                                                
                                                                                                        <option value="<?= !isset($rows_pensao['id_tipo_dependente']) || ($rows_pensao['id_tipo_dependente'] == "" ) ? 99 : $rows_pensao['id_tipo_dependente'] ?>"><?= (!isset($rows_pensao['tipo_dependente']) || $rows_pensao['tipo_dependente'] == "" || $rows_pensao['tipo_dependente'] == 99 ) ? "-- Selecione --" : $rows_pensao['tipo_dependente']; ?> </option>                                                                                                                                                
            <?php echo $select_dependentes; ?>
                                                                                                    </select> 
                                                                                                </td> 
                                                                                            </tr>

                                                                                            </tbody>
                                                                                            <?php
                                                                                            $iDep++;
                                                                                            $cont++;
                                                                                            $gambiNomesPensaoArray = substr($gambiNomesPensao, 1);
                                                                                        }
                                                                                        ?>
    <?php } else { ?><tbody class="rows_pensao_<?php echo $rows_pensao['id']; ?> remover_<?php echo $rows_pensao['id']; ?>" data-key="<?php echo $rows_pensao['id']; ?>">
                                                                                            <tr>
                                                                                                <td class="secao">Nome Favorecido:</td>
                                                                                                <td colspan="1">
                                                                                                    <input class="input_nome_fav" type="text" name="nome_favorecido[]" id="nome_favorecido"  value="<?php echo isset($rows_pensao['favorecido']) ? $rows_pensao['favorecido'] : ""; ?>" />
                                                                                                    <input type="hidden" name="id_favorecido[]" id="id_favorecido"  value="<?php echo isset($rows_pensao['id']) ? $rows_pensao['id'] : ""; ?>" /></td>
                                                                                                </td>
                                                                                                <td class="secao">CPF Favorecido:</td>
                                                                                                <td colspan="1"><input type="text" name="cpf_favorecido[]" id="cpf_favorecido" class="cpf_favorecido" value="<?php echo isset($rows_pensao['cpf']) ? $rows_pensao['cpf'] : ""; ?>" /></td>
                                                                                                <td class="secao">Aliquota:</td>
                                                                                                <td colspan="1"><input type="text" name="aliquota_favorecido[]" placeholder='0.000' class="aliFav" id="aliquota_favorecido"  value="<?php echo isset($rows_pensao['aliquota']) ? $rows_pensao['aliquota'] : ""; ?>" /></td>
        <?php if ($rows != $rows_pensao['id_clt']) { ?>
                                                                                                    <?php $rows = $rows_pensao['id_clt']; ?>
                                                                                                    <td class="adicionar" rowspan="3">
                                                                                                        <a href="javascript:;" class="add_favorecido">+</a>
                                                                                                        <input type="hidden" name="contador" id="contador" value="0" />
                                                                                                    </td>
                                                                                                <?php } else { ?>
                                                                                                    <td class="" rowspan="3">
                                                                                                        <a href="javascript:;" class="add_favorecido" data-key="<?php echo $rows_pensao['id']; ?>">+</a>
                                                                                                    </td>
        <?php } ?>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td class="secao">Banco:</td>
                                                                                                <td colspan="1"><?php echo montaSelect($arrayBancos, $rows_pensao['id_lista_banco'], "name='id_lista_banco_favorecido[]' id='id_lista_banco' class='validate[required]'"); ?></td>
                                                                                                <td class="secao">Agência:</td>
                                                                                                <td colspan="1">
                                                                                                    <input type="text" name="agencia_favorecido[]" id="agencia_favorecido"  value="<?php echo isset($rows_pensao['agencia']) ? $rows_pensao['agencia'] : ""; ?>" />
                                                                                                </td>
                                                                                                <td class="secao">Conta:</td>
                                                                                                <td colspan="1">
                                                                                                    <input type="text" name="conta_favorecido[]" id="conta_favorecido"  value="<?php echo isset($rows_pensao['conta']) ? $rows_pensao['conta'] : ""; ?>" />
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td class="secao">Oficio:</td>
                                                                                                <td colspan="1"><input type="text" name="oficio[]" id="oficio" value="<?php echo isset($rows_pensao['oficio']) ? $rows_pensao['oficio'] : ""; ?>" /></td>
                                                                                                <td class="secao">Base de Calculo:</td>
                                                                                                <?php
//                                                                                                                $queryVerificaBasePensao = "SELECT * FROM favorecido_pensao_assoc AS A  
//                                                                                                                  WHERE A.id_clt = {$rows_pensao['id_clt']}";
//
//                                                                                                                $sqlVerificaBasePensao = mysql_query($queryVerificaBasePensao) or die("Erro ");
//                                                                                                                
//
//                                                                                                                while ($rowsVerificaBasePensao = mysql_fetch_assoc($sqlVerificaBasePensao)) {
                                                                                                $base = 0;
                                                                                                $valorFixo = 0;
                                                                                                $incide_ferias = false;
                                                                                                $incide_rescisao = false;
                                                                                                $incide_13 = false;
                                                                                                $quant = $rows_pensao['quantSalMinimo'];

                                                                                                if ($rows_pensao['sobreSalLiquido'] == 1) {
                                                                                                    $base = 1;
                                                                                                }
                                                                                                if ($rows_pensao['sobreSalBruto'] == 1) {
                                                                                                    $base = 2;
                                                                                                }
                                                                                                if ($rows_pensao['sobreSalMinimo'] == 1) {
                                                                                                    $base = 3;
                                                                                                }
                                                                                                if ($rows_pensao['valorfixo'] > 0) {
                                                                                                    $base = 4;
                                                                                                    $valorFixo = $rows_pensao['valorfixo'];
                                                                                                }
                                                                                                if ($rows_pensao['umTercoSobreLiquido'] == 1) {
                                                                                                    $base = 5;
                                                                                                }
                                                                                                if ($rows_pensao['sobrePorcentagemSalMinimo'] == 1) {
                                                                                                    $base = 6;
                                                                                                }

//                                                                                                                    $incide_ferias = $rowsVerificaBasePensao['incide_ferias'];
//                                                                                                                    $incide_rescisao = $rowsVerificaBasePensao['incide_rescisao'];
//                                                                                                                    $incide_13 = $rowsVerificaBasePensao['incide_13'];
//                                                                                                                }
                                                                                                ?>
                                                                                                <td colspan="1">
                                                                                                    <select name="base[]" id="base" class="mudaBasePensao">
                                                                                                        <option value="">--Selecione-- <?php echo $base; ?></option>
                                                                                                        <option value="1" <?php
                                                                                                if ($base == 1) {
                                                                                                    echo " selected='selected' ";
                                                                                                }
                                                                                                ?>>Salário Liquido</option>
                                                                                                        <option value="2" <?php
                                                                                                if ($base == 2) {
                                                                                                    echo " selected='selected' ";
                                                                                                }
                                                                                                ?>>Salário Bruto</option>
                                                                                                        <option value="3" <?php
                                                                                                if ($base == 3) {
                                                                                                    echo " selected='selected' ";
                                                                                                }
                                                                                                ?>>Salário Mínimo</option>
                                                                                                        <option value="4" <?php
                                                                                                if ($base == 4) {
                                                                                                    echo " selected='selected' ";
                                                                                                }
                                                                                                ?>>Valor Fixo</option>
                                                                                                        <option value="5" <?php
                                                                                                if ($base == 5) {
                                                                                                    echo " selected='selected' ";
                                                                                                }
                                                                                                ?>>1/3 Sobre Líquido</option>
                                                                                                        <option value="6" <?php
                                                                                                if ($base == 6) {
                                                                                                    echo " selected='selected' ";
                                                                                                }
                                                                                                ?>>Porcentagem Sobre Salário Mínimo</option>
                                                                                                    </select>    
                                                                                                </td>
                                                                                                <td colspan="1" class="secao">
                                                                                                    <label class="quantSalMinimo">Quantidade</label>
                                                                                                    <label class="valorfixo">Valor</label>
                                                                                                </td>
                                                                                                <td colspan="1">
                                                                                                    <select name="quant[]" id="quant" class="quantSalMinimo">
                                                                                                        <option value="1" <?php
                                                                                                if ($quant == 1) {
                                                                                                    echo " selected='selected' ";
                                                                                                }
                                                                                                ?>>1</option>
                                                                                                        <option value="2" <?php
                                                                                                if ($quant == 2) {
                                                                                                    echo " selected='selected' ";
                                                                                                }
                                                                                                ?>>2</option>
                                                                                                        <option value="3" <?php
                                                                                                if ($quant == 3) {
                                                                                                    echo " selected='selected' ";
                                                                                                }
                                                                                                ?>>3</option>
                                                                                                        <option value="4" <?php
                                                                                                if ($quant == 4) {
                                                                                                    echo " selected='selected' ";
                                                                                                }
                                                                                                ?>>4</option>
                                                                                                        <option value="5" <?php
                                                                                                if ($quant == 5) {
                                                                                                    echo " selected='selected' ";
                                                                                                }
                                                                                                ?>>5</option>
                                                                                                    </select>    
                                                                                                    <input type="text" name="valorfixo[]" id="valorfixo" class="formata_valor valorfixo" value="<?= $valorFixo; ?>" />
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td class="secao">Incide Em:</td>
                                                                                                <td colspan="6">

                                                                                                    <input type="checkbox" class='incide_ferias' name="incide_ferias[<?= $cont ?>]" value="1" <?= ($rows_pensao['incide_ferias'] == 1) ? "checked=checked" : "" ?>> Férias
                                                                                                        <input type="checkbox" name="incide_rescisao[<?= $cont ?>]" value="1" <?= ($rows_pensao['incide_rescisao'] == 1) ? "checked=checked" : "" ?>> Rescisão
                                                                                                            <input type="checkbox" name="incide_13[<?= $cont ?>]" value="1" <?= ($rows_pensao['incide_13'] == 1) ? "checked=checked" : "" ?>> Décimo Terceiro
                                                                                                                </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td class="secao" width="235px">Dependente:</td>
                                                                                                                    <td colspan="1">
                                                                                                                        <select id="selDepPensao<?= $iDep ?>" class="select_dependentes" name="select_dependentes[]">
        <!--                                                                                                                                                <option value="<?= !isset($rows_pensao['nome_dependente']) || ($rows_pensao['nome_dependente'] == "" ) ? 99 : 1 ?>"><?= isset($rows_pensao['nome_dependente']) ? ($rows_pensao['nome_dependente']) : "SELECIONE UM DEPENDENTE" ?></option>                                                                                                                                                -->
                                                                                                                        </select> 
                                                                                                                    </td> 
                                                                                                                    <td class="secao">CPF Dependente:</td>
                                                                                                                    <td class="td_cpf" colspan="1"><input type="text" name="cpf_dependente_fav[]" value="<?= isset($rows_pensao['cpf_dependente']) ? ($rows_pensao['cpf_dependente']) : "" ?>" class="cpf_dependente"/></td>
                                                                                                                    <td class="secao">Data de Nascimento:</td>
                                                                                                                    <td class="td_dtnasc" colspan="2"><input type="text" name="dt_nasc_dependente[]" value="<?= isset($rows_pensao['data_nasc_dependente']) ? ($rows_pensao['data_nasc_dependente']) : "" ?>" class="dt_nasc_dependente"/></td>
                                                                                                                </tr> 
                                                                                                                <tr>
                                                                                                                    <td class="secao" width="235px">Tipo de Dependente:</td>
                                                                                                                    <td colspan="6">
                                                                                                                        <select class="tipo_de_dependente" name="tipo_de_dependente[]">                                                                                                                                                
                                                                                                                            <option value="<?= !isset($rows_pensao['id_tipo_dependente']) || ($rows_pensao['id_tipo_dependente'] == "" ) ? 99 : $rows_pensao['id_tipo_dependente'] ?>"><?= (!isset($rows_pensao['tipo_dependente']) || $rows_pensao['tipo_dependente'] == "" || $rows_pensao['tipo_dependente'] == 99 ) ? "-- Selecione --" : $rows_pensao['tipo_dependente']; ?> </option>                                                                                                                                                
        <?php echo $select_dependentes; ?>
                                                                                                                        </select> 
                                                                                                                    </td> 
                                                                                                                </tr>

                                                                                                                </tbody>
        <!--                                                                                                                                <tbody class="rows_pensao_<?php echo $rows_pensao['id']; ?> remover_<?php echo $rows_pensao['id']; ?>" data-key="<?php echo $rows_pensao['id']; ?>">
                                                                                                                <tr>
                                                                                                                    <td class="secao" width="235px">Nome Favorecido:</td>
                                                                                                                    <td colspan="1"><input class="input_nome_fav" type="text" name="nome_favorecido[]" id="nome_favorecido"  value="<?php echo isset($rows_pensao['favorecido']) ? $rows_pensao['favorecido'] : ""; ?>" /></td>
                                                                                                                    <td class="secao">CPF Favorecido:</td>
                                                                                                                    <td colspan="1"><input type="text" name="cpf_favorecido[]" id="cpf_favorecido"  value="<?php echo isset($rows_pensao['cpf']) ? $rows_pensao['cpf'] : ""; ?>" /></td>
                                                                                                                    <td class="secao">Aliquota:</td>
                                                                                                                    <td colspan="1"><input type="text" name="aliquota_favorecido[]" placeholder='0.000' class="aliFav" id="aliquota_favorecido"  value="<?php echo isset($rows_pensao['aliquota']) ? $rows_pensao['aliquota'] : ""; ?>" /></td>
                                                                                                                    <td class="adicionar" rowspan="3">
                                                                                                                        <a href="javascript:;" class="add_favorecido">+</a>
                                                                                                                        <input type="hidden" name="contador" id="contador" value="0" />
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td class="secao">Banco:</td>
                                                                                                                    <td colspan="1"><?php echo montaSelect($arrayBancos, $rows_pensao['id_lista_banco'], "name='id_lista_banco_favorecido[]' id='id_lista_banco' class='validate[required]'"); ?></td>
                                                                                                                    <td class="secao">Agência:</td>
                                                                                                                    <td colspan="1">
                                                                                                                        <input type="text" name="agencia_favorecido[]" id="agencia_favorecido"  value="<?php echo isset($rows_pensao['agencia']) ? $rows_pensao['agencia'] : ""; ?>" />
                                                                                                                    </td>
                                                                                                                    <td class="secao">Conta:</td>
                                                                                                                    <td colspan="1">
                                                                                                                        <input type="text" name="conta_favorecido[]" id="conta_favorecido"  value="<?php echo isset($rows_pensao['conta']) ? $rows_pensao['conta'] : ""; ?>" />
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td class="secao">Oficio:</td>
                                                                                                                    <td><input type="text" name="oficio[]" id="oficio" value="<?php echo isset($rows_pensao['oficio']) ? $rows_pensao['oficio'] : ""; ?>" /></td>
                                                                                                                    <td class="secao">Base de Calculo:</td>
                                                                                                                    <td colspan="">
                                                                                                                        <select name="base[]" id="base" class="mudaBasePensao">
                                                                                                                            <option value="">--Selecione--</option>
                                                                                                                            <option value="1">Salário Liquido</option>
                                                                                                                            <option value="2">Salário Bruto</option>
                                                                                                                            <option value="3">Salário Mínimo</option>
                                                                                                                            <option value="4">Valor Fixo</option>
                                                                                                                            <option value="5">1/3 Sobre Líquido</option>
                                                                                                                        </select>    
                                                                                                                        <input type="text" name="valorfixo[]" id="valorfixo" class="formata_valor valorfixo" value="">
                                                                                                                    </td>
                                                                                                                    <td colspan="1" class="secao">
                                                                                                                        <label class="quantSalMinimo">Quantidade</label>
                                                                                                                        <label class="valorfixo">Valor</label>
                                                                                                                    </td>
                                                                                                                    <td colspan="1">
                                                                                                                        <select name="quant[]" id="quant" class="quantSalMinimo">
                                                                                                                            <option value="0">0</option>
                                                                                                                            <option value="1">1</option>
                                                                                                                            <option value="2">2</option>
                                                                                                                            <option value="3">3</option>
                                                                                                                            <option value="4">4</option>
                                                                                                                            <option value="5">5</option>
                                                                                                                        </select>    
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td class="secao">Incide Em:</td>
                                                                                                                    <td colspan="6">
                                                                                                                        <span><input type="checkbox" class='incide_ferias' name="incide_ferias[0]" /> Férias</span>
                                                                                                                        <span><input type="checkbox" name="incide_rescisao[0]" /> Rescisão</span>
                                                                                                                        <span><input type="checkbox" name="incide_13[0]" /> Décimo Terceiro</span>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td class="secao" width="235px">Nome Dependente:</td>
                                                                                                                    <td colspan="1"><input type="text" name="nome_favorecido_dependente[]" id="nome_favorecido_dependente" /></td>                                                                                                                                   
                                                                                                                </tr>   

                                                                                                            </tbody>-->
                                                                                                                <?php } ?>
                                                                                                            </table>
                                                                                                            <table cellpadding="0" cellspacing="1" class="secao">
                                                                                                                <?php
                                                                                                                $flag = '';
                                                                                                                if ($escolha_gratificacao == '80300') {
                                                                                                                    $selected = $select_escolha_gratificacao;
                                                                                                                    $flag = 1;
                                                                                                                } else if ($escolha_gratificacao == '80301') {
                                                                                                                    $selected = $select_escolha_gratificacao;
                                                                                                                    $flag = 2;
                                                                                                                } else {
                                                                                                                    $selected = '';
                                                                                                                }
                                                                                                                ?>

                                                                                                            </table>
                                                                                                            <!-- AQUI VIADO -->
    <?php ?>

                                                                                                            <script>
                                                                                                                $(document).ready(function (e) {


                                                                                                                    $("#ckb_agencia").click(function (e) {
                                                                                                                        if ($(this).is(':checked')) //Retornar true ou false
                                                                                                                            $("#agencia_dv").val("N").prop("readonly", true);
                                                                                                                        //$('#agencia_dv').attr("disabled", true);
                                                                                                                        else
                                                                                                                            $("#agencia_dv").val("").prop("readonly", false);
                                                                                                                    });

                                                                                                                });
                                                                                                            </script>

                                                                                                            <table cellpadding="0" cellspacing="1" class="secao">
                                                                                                                <tr>
                                                                                                                    <td class="secao_pai" colspan="2">DADOS BANCÁRIOS</td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td width="15%" class="secao">Banco:</td>
                                                                                                                    <td width="40%">
                                                                                                                        <select name="banco">
                                                                                                                            <option value="0">Nenhum Banco</option>
                                                                                                                            <?php
                                                                                                                            $sql_banco = "SELECT * FROM bancos WHERE id_regiao = '$row[id_regiao]' AND id_projeto = '$row[id_projeto]' AND status_reg = '1'";
                                                                                                                            $result_banco = mysql_query($sql_banco, $conn);
                                                                                                                            while ($row_banco = mysql_fetch_assoc($result_banco)) {
                                                                                                                                if ($row['banco'] == "{$row_banco['id_banco']}") {
                                                                                                                                    print "<option value={$row_banco['id_banco']} selected>{$row_banco['razao']} - {$row_banco['agencia']} - {$row_banco['conta']}</option>";
                                                                                                                                } else {
                                                                                                                                    print "<option value={$row_banco['id_banco']}>{$row_banco['razao']} - {$row_banco['agencia']} - {$row_banco['conta']}</option>";
                                                                                                                                }
                                                                                                                            }

                                                                                                                            if ($row['banco'] == "9999") {
                                                                                                                                print "<option value='9999' selected>Outro</option></select>";
                                                                                                                            } else {
                                                                                                                                print "<option value='9999'>Outro</option></select>";
                                                                                                                            }
                                                                                                                            ?>
                                                                                                                        </select>
                                                                                                                    </td>
                                                                                                                </tr>

                                                                                                                <tr>
                                                                                                                    <td width="25%" class="secao">Agência:</td>
                                                                                                                    <td width="40%">
                                                                                                                        <input name="agencia" type="text" id="agencia" size="5" maxlength="5" pattern="[0-9]+$" value="<?= $row['agencia'] ?>" />
                                                                                                                        DV <input name="agencia_dv" type="text" id="agencia_dv" size="1" maxlength="1"  value="<?= $row['agencia_dv'] ?>" />
                                                                                                                        <input type="checkbox" name="ckb_agencia" id="ckb_agencia" <?= ( $row['agencia_dv'] == 'N') ? 'checked' : '' ?>/> AGENCIA SEM DIGITO
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td class="secao">Conta:</td>
                                                                                                                    <td colspan="3">
                                                                                                                        <input name="conta" type="text" id="conta" size="12" maxlength="12" pattern="[0-9]+$" value="<?= $row['conta'] ?>"  />
                                                                                                                        DV <input name="conta_dv" type="text" id="conta_dv" size="2" maxlength="2" pattern="[A-Z0-9.-]+$" value="<?= $row['conta_dv'] ?>" onkeyup="up(this)"/>
                                                                                                                        <?php
                                                                                                                        $tipo = $row['tipo_conta'];
                                                                                                                        if ($tipo == 'salario') {
                                                                                                                            $checkedSalario = 'checked';
                                                                                                                        } elseif ($tipo == 'corrente') {
                                                                                                                            $checkedCorrente = 'checked';
                                                                                                                        } elseif ($tipo == 'poupanca') {
                                                                                                                            $checkedPolpanca = 'checked';
                                                                                                                        }
                                                                                                                        ?>

                                                                                                                        <label style="display:inline-block; padding: 0 10px 0 30px;"><input name="radio_tipo_conta" type="radio" class="reset" value="salario" <?= $checkedSalario ?>/> Conta Salário </label>
                                                                                                                        <label style="display:inline-block; padding: 0 10px;"><input name="radio_tipo_conta" type="radio" class="reset" value="corrente" <?= $checkedCorrente ?>/> Conta Corrente </label>
                                                                                                                        <label style="display:inline-block; padding: 0 10px;"><input name="radio_tipo_conta" type="radio" class="reset" value="poupanca" <?= $checkedPolpanca ?>/> Conta Poupança </label>
                                                                                                                    </td>

                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td class="secao">Nome do Banco: <!--<br /> (caso não esteja na lista acima)--></td>
                                                                                                                    <td>                                                                
                                                                                                                        <!--<input name="nome_banco" type="hidden" id="nome_banco" value="<?= $row['nome_banco'] ?>" />-->
                                                                                                                        <?php
                                                                                                                        $qb = "SELECT * FROM listabancos ";
                                                                                                                        $resultb = mysql_query($qb);
                                                                                                                        $banc[0] = '-- Selecione --';
                                                                                                                        $banc[-1] = '-- NENHUM BANCO --';
                                                                                                                        while ($rowb = mysql_fetch_assoc($resultb)) {
//                                                                    $banc[$rowb['id_nacional']] = $rowb['banco'];
                                                                                                                            $banc[$rowb['banco']] = $rowb['banco'];
                                                                                                                        }

//                                                                $nome_banco = ($row['banco'] == 106)?-1:$row['nome_banco']; // deixa marcado a opcao todoos os bancos ou marca o banco que esta salvo no bd
//                                                                $disabled = ($row['banco'] == 106)?'disabled style="background-color:#eee;"':'';
                                                                                                                        //echo montaSelect($banc, $row['id_banco_nacional'], 'name="id_banco_nacional" id="id_banco_nacional"');
                                                                                                                        echo montaSelect($banc, $row['nome_banco'], 'name="nome_banco" id="id_banco_nacional" ');
                                                                                                                        ?>
                                                                                                                    </td>

                                                                                                                </tr>
                                                                                                            </table>
                                                                                                            <table cellpadding="0" cellspacing="1" class="secao">
                                                                                                                <tr>
                                                                                                                    <td class="secao_pai" colspan="4">DADOS FINANCEIROS E DE CONTRATO</td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td  class="secao">Data de Entrada:</td>
                                                                                                                    <td colspan="3">
                                                                                                                        <input name="data_entrada" type="text" size="12" maxlength="10" id="data_entrada" readonly="readonly" class="validate[required]" value="<?= $row['data_entrada2'] ?>" onkeyup="mascara_data(this);
                                                                                                                                pula(10, this.id, data_exame.id)" />

                                                                                                                        <span id="dataFinalExperiencia" style="margin-left: 20px;"></span>
                                                                                                                    </td>
                                                                                                                </tr>

                                                                                                                <!-- FEITO POR SINESIO - 24/03/2016 - 2027 -->
                                                                                                                <tr>
                                                                                                                    <td class="secao">Data do Exame Admissional:</td>
                                                                                                                    <td colspan="3">
                                                                                                                        <input name="data_exame" type="text" size="12" maxlength="10" value="<?= $row['data_exame'] ?>" id="data_exame"
                                                                                                                               onkeyup="mascara_data(this);
                                                                                                                                       pula(10, this.id, localpagamento.id)" />
                                                                                                                    </td>                                       
                                                                                                                </tr>

                                                                                                                <tr>
                                                                                                                    <td width="23%" class="secao">Local de Pagamento:</td>
                                                                                                                    <td width="20%">
                                                                                                                        <input name="localpagamento" type="text" id="localpagamento" size="30" value="<?= $row['localpagamento'] ?>" class="validate[required]"
                                                                                                                               onChange="this.value = this.value.toUpperCase()"/>
                                                                                                                    </td>

                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td class="secao">Tipo de Pagamento:</td>
                                                                                                                    <td colspan="3">
                                                                                                                        <select name="tipopg" id="tipopg" class="">
                                                                                                                            <option value="">Selecione...</option>
                                                                                                                            <?php
                                                                                                                            $result_pg = mysql_query("SELECT * FROM tipopg WHERE id_projeto = '$id_projeto'", $conn);
                                                                                                                            echo "SELECT * FROM tipopg WHERE id_projeto = '$id_projeto'";
                                                                                                                            while ($row_pg = mysql_fetch_array($result_pg)) {
                                                                                                                                if ($row_pg['0'] == $row['tipo_pagamento']) {
                                                                                                                                    print "<option value='$row_pg[id_tipopg]' selected>$row_pg[tipopg]</option>";
                                                                                                                                } else {
                                                                                                                                    print "<option value='$row_pg[id_tipopg]'>$row_pg[tipopg]</option>";
                                                                                                                                }
                                                                                                                            }
                                                                                                                            ?>
                                                                                                                        </select>
                                                                                                                    </td>
                                                                                                                </tr>
    <!--                                                                                                                                <tr>
                                                                                                                    <td class="secao">Centro de custo</td>
                                                                                                                    <td colspan="3">
                                                                                                                        <select name="centrocusto" id="centrocusto" class="">
                                                                                                                            <option value="">« Selecione »</option>
                                                                                                                <?php
                                                                                                                $result_cc = mysql_query("SELECT * FROM centro_custo WHERE id_regiao = '{$row['id_regiao']}'", $conn);

                                                                                                                while ($row_cc = mysql_fetch_array($result_cc)) {
                                                                                                                    if ($row_cc['0'] == $row['id_centro_custo']) {
                                                                                                                        print "<option value='{$row_cc['id_centro_custo']}' selected>{$row_cc['nome']}</option>";
                                                                                                                    } else {
                                                                                                                        print "<option value='{$row_cc['id_centro_custo']}'>{$row_cc['nome']}</option>";
                                                                                                                    }
                                                                                                                }
                                                                                                                ?>
                                                                                                                        </select>
                                                                                                                    </td>
                                                                                                                </tr>-->
                                                                                                                <tr>                    
                                                                                                                    <td  class="secao">Prazo de Experiência:</td>
                                                                                                                    <td colspan="5" align="left">
                                                                                                                        <input type="radio" name="prazoExp" value="4" <?php if ($row['prazoexp'] == 4) echo 'checked=checked' ?> /> 30
                                                                                                                        <input type="radio" name="prazoExp" value="5" <?php if ($row['prazoexp'] == 5) echo 'checked=checked' ?> /> 45
                                                                                                                        <input type="radio" name="prazoExp" value="6" <?php if ($row['prazoexp'] == 6) echo 'checked=checked' ?> /> 60
                                                                                                                        <input type="radio" name="prazoExp" value="1" <?php if ($row['prazoexp'] == 1) echo 'checked=checked' ?> /> 30 + 60
                                                                                                                        <input type="radio" name="prazoExp" value="2" <?php if ($row['prazoexp'] == 2) echo 'checked=checked' ?> /> 45 + 45
                                                                                                                        <input type="radio" name="prazoExp" value="3" <?php if ($row['prazoexp'] == 3) echo 'checked=checked' ?> /> 60 + 30
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td width="15%" class="secao">Prazo Determinado:</td>
                                                                                                                    <td width="30%">
                                                                                                                        <input name="pde" type="checkbox" id="id_pde" size="12" value="1" <?= ($row['pde'] == 1) ? 'checked' : '' ?> />
                                                                                                                    </td>


                                                                                                                </tr>

                                                                                                                <tr style="display: none" id="tr-dias-pde">
                                                                                                                    <td class="secao">Dias Prazo Determinado: </td>
                                                                                                                    <td>
                                                                                                                        <input type="number" name="dias-pde" id="dias-pde">
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td width="15%" class="secao">Termino do Contrato:</td>
                                                                                                                    <td><input name="data_pde" type="text" id="data_pde" size="15" maxlength="10" class="" readonly="readonly" value="<?php
                                                                                                                if ($row['data_pde'] == '1969-12-31') {
                                                                                                                    echo $row['data_pde'] = '';
                                                                                                                } else {
                                                                                                                    echo converteData($row['data_pde'], 'd/m/Y');
                                                                                                                }
                                                                                                                ?>"
                                                                                                                               onkeyup="mascara_data(this);"/>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td class="secao">Tipo de Contrato:</td>
                                                                                                                    <td colspan="3">
                                                                                                                        <select name="tipo_contrato" id="tipo_contrato">
                                                                                                                            <?php
                                                                                                                            $qr_tpContrato = mysql_query("SELECT id_categoria_trab, descricao FROM categorias_trabalhadores WHERE grupo = 'Empregado e Trabalhador T';");
                                                                                                                            while ($row_tpContrato = mysql_fetch_assoc($qr_tpContrato)) {
                                                                                                                                $selecionado = ($row_tpContrato['id_categoria_trab'] == $row['tipo_contrato']) ? 'selected="selected"' : '';
                                                                                                                                print "<option value='{$row_tpContrato['id_categoria_trab']}' $selecionado>{$row_tpContrato['descricao']}</option>";
                                                                                                                            }
                                                                                                                            ?>
                                                                                                                        </select>
                                                                                                                    </td>
                                                                                                                </tr>
                                                                                                                <tr>
                                                                                                                    <td class="secao">Observações:</td>
                                                                                                                    <td colspan="3">
                                                                                                                        <textarea name="observacoes" id="observacoes" cols="55" rows="4"  
                                                                                                                                  onChange="this.value = this.value.toUpperCase()"><?= $row['observacao'] ?></textarea></td>
                                                                                                                </tr>

                                                                                                            </table>
                                                                                                            <!--                                <div id="finalizacao"> 
                                                                                                                                                O Contrato foi <strong>assinado</strong>?<br/>
                                                                                                                                                <label><input name="assinatura" type="radio" class="reset" id="assinatura" value="1" <?= $selected_ass_sim ?>/> 
                                                                                                                                                    SIM </label>
                                                                                                                                                <label><input name="assinatura" type="radio" class="reset" id="assinatura" value="0" <?= $selected_ass_nao ?>/> 
                                                                                                                                                    N&Atilde;O</label>
                                                                                                                                                <p>&nbsp;</p>
                                                                                                                                                O Distrato foi <strong>assinado</strong>?<br/>
                                                                                                                                                <label><input name="assinatura2" type="radio" class="reset" id="assinatura2" value="1" <?= $selected_ass_sim2 ?>/> 
                                                                                                                                                    SIM </label>
                                                                                                                                                <label><input name="assinatura2" type="radio" class="reset" id="assinatura2" value="0" <?= $selected_ass_nao2 ?>/> 
                                                                                                                                                    N&Atilde;O</label>
                                                                                                                                                <p>&nbsp;</p>
                                                                                                                                                Outros documentos foram <strong>assinados</strong>?<br/>
                                                                                                                                                <label><input name="assinatura3" type="radio" class="reset" id="assinatura3" value="1" <?= $selected_ass_sim3 ?>/> 
                                                                                                                                                    SIM </label>
                                                                                                                                                <label><input name="assinatura3" type="radio" class="reset" id="assinatura3" value="0" <?= $selected_ass_nao3 ?>/> 
                                                                                                                                                    N&Atilde;O</label>
    <?= $mensagem_ass ?>                 
                                                                                                                                            </div>-->
                                                                                                            <div id="observacao">NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</div>
    <?php if ($_COOKIE['logado'] != 395) { ?>
                                                                                                                <div align="center"><input type="submit" name="Submit" value="ATUALIZAR" class="botao" /></div> 
    <?php } ?>
                                                                                                            <input type="hidden" name="update" value="1"/>
                                                                                                            <input type="hidden" name="id_clt" value="<?= $row[0] ?>"/>
                                                                                                            <input type="hidden" name="regiao" value="<?= $row['id_regiao'] ?>"/>
                                                                                                            <input type="hidden" name="pro" value="<?= $id_projeto ?>"/>
                                                                                                            <input type="hidden" name="id_bolsista" value="<?= $row[1] ?>"/>
                                                                                                            <input type="hidden" name="pagina" value="<?= $pagina ?>"/>
                                                                                                            </form>
                                                                                                            </td>
                                                                                                            </tr>
                                                                                                            </table>
                                                                                                            </div>
                                                                                                            <script language="javascript" >
                                                                                                                function validaForm() {

                                                                                                                    d = document.form1;

                                                                                                                    deposito = "<?= $Row_pg_dep[0] ?>";
                                                                                                                    cheque = "<?= $Row_pg_che[0] ?>";

                                                                                                                    if ($("#pis").val() == '') {
                                                                                                                        alert('O campo de PIS foi deixado em branco, mas precisa ser preenchido no futuro.');
                                                                                                                    }


                                                                                                                    if (d.transporte2.checked == True && d.vale1.value == 0 && d.vale2.value == 0 && d.vale3.value == 0 && d.vale4.value == 0 && d.vale5.value == 0 && d.vale6value == 0) {
                                                                                                                        alert("Um dos Vales deve ser Selecionado\!");
                                                                                                                        d.vale1.focus();
                                                                                                                        return false;
                                                                                                                    }

                                                                                                                    if (document.getElementById("tipopg").value == deposito) {

                                                                                                                        if (document.getElementById("banco").value == 0) {
                                                                                                                            alert("Selecione um banco!");
                                                                                                                            return false;
                                                                                                                        }

                                                                                                                        if (d.agencia.value == "") {
                                                                                                                            alert("O campo Agencia deve ser preenchido!");
                                                                                                                            d.agencia.focus();
                                                                                                                            return false;
                                                                                                                        }

                                                                                                                        if (d.conta.value == "") {
                                                                                                                            alert("O campo Conta deve ser preenchido!");
                                                                                                                            d.conta.focus();
                                                                                                                            return false;
                                                                                                                        }
                                                                                                                    }
                                                                                                                    if (document.getElementById("tipopg").value == cheque) {
                                                                                                                        if (document.getElementById("banco").value != 0) {
                                                                                                                            alert("Para pagamentos em cheque deve selecionar SEM BANCO!");
                                                                                                                            return false;
                                                                                                                        }
                                                                                                                        d.agencia.value = "";
                                                                                                                        d.conta.value = "";
                                                                                                                    }
                                                                                                                    return true;
                                                                                                                }

                                                                                                                $(function () {

                                                                                                                    var carregaDependentes = [];

                                                                                                                    if ($("#filho_1").val() != "") {
                                                                                                                        carregaDependentes.push($("#filho_1").val());
                                                                                                                    }
                                                                                                                    if ($("#filho_2").val() != "") {
                                                                                                                        carregaDependentes.push($("#filho_2").val());
                                                                                                                    }
                                                                                                                    if ($("#filho_3").val() != "") {
                                                                                                                        carregaDependentes.push($("#filho_3").val());
                                                                                                                    }
                                                                                                                    if ($("#filho_4").val() != "") {
                                                                                                                        carregaDependentes.push($("#filho_4").val());
                                                                                                                    }
                                                                                                                    if ($("#filho_5").val() != "") {
                                                                                                                        carregaDependentes.push($("#filho_5").val());
                                                                                                                    }
                                                                                                                    if ($("#filho_6").val() != "") {
                                                                                                                        carregaDependentes.push($("#filho_6").val());
                                                                                                                    }

                                                                                                                    var gambiDepPensao = '<?= ($gambiNomesPensaoArray) ?>';
                                                                                                                    gambiDepPensao = gambiDepPensao.split(',');
                                                                                                                    console.log(gambiDepPensao.length);

                                                                                                                    if ($(".select_dependentes:eq(0)").children().val() == 99) {
                                                                                                                        $(".dt_nasc_dependente:eq(0)").val("");
                                                                                                                        $(".select_dependentes:eq(0)").empty();

                                                                                                                        $(".select_dependentes:eq(0)").append($('<option>',
                                                                                                                                {
                                                                                                                                    value: 99,
                                                                                                                                    text: "Dependentes"
                                                                                                                                }));

                                                                                                                        for (var i = 0; i < carregaDependentes.length; i++) {

                                                                                                                            if (gambiDepPensao[0] == carregaDependentes[i]) {
                                                                                                                                $(".select_dependentes:eq(0)").append($('<option>',
                                                                                                                                        {
                                                                                                                                            selected: 'selected',
                                                                                                                                            class: 'opt' + i,
                                                                                                                                            value: i,
                                                                                                                                            text: carregaDependentes[i]
                                                                                                                                        }));
                                                                                                                            } else {
                                                                                                                                $(".select_dependentes:eq(0)").append($('<option>',
                                                                                                                                        {
                                                                                                                                            class: 'opt' + i,
                                                                                                                                            value: i,
                                                                                                                                            text: carregaDependentes[i]
                                                                                                                                        }));
                                                                                                                            }
                                                                                                                        }
                                                                                                                    } else {


                                                                                                                        for (var i = 0; i < carregaDependentes.length; i++) {

                                                                                                                            if (gambiDepPensao[0] == carregaDependentes[i]) {
                                                                                                                                $(".select_dependentes:eq(0)").append($('<option>',
                                                                                                                                        {
                                                                                                                                            selected: 'selected',
                                                                                                                                            class: 'opt' + i,
                                                                                                                                            value: i,
                                                                                                                                            text: carregaDependentes[i]
                                                                                                                                        }));
                                                                                                                            } else {
                                                                                                                                $(".select_dependentes:eq(0)").append($('<option>',
                                                                                                                                        {
                                                                                                                                            class: 'opt' + i,
                                                                                                                                            value: i,
                                                                                                                                            text: carregaDependentes[i]
                                                                                                                                        }));
                                                                                                                            }


                                                                                                                        }
                                                                                                                        $(".select_dependentes:eq(0)").append($('<option>',
                                                                                                                                {
                                                                                                                                    value: 99,
                                                                                                                                    text: "Dependentes"
                                                                                                                                }));

                                                                                                                    }

                                                                                                                    if ($(".select_dependentes:eq(1)").children().val() == 99) {
                                                                                                                        $(".dt_nasc_dependente:eq(1)").val("");
                                                                                                                        $(".select_dependentes:eq(1)").empty();

                                                                                                                        $(".select_dependentes:eq(1)").append($('<option>',
                                                                                                                                {
                                                                                                                                    value: 99,
                                                                                                                                    text: "Dependentes"
                                                                                                                                }));

                                                                                                                        for (var i = 0; i < carregaDependentes.length; i++) {

                                                                                                                            if (gambiDepPensao[0] == carregaDependentes[i]) {
                                                                                                                                $(".select_dependentes:eq(1)").append($('<option>',
                                                                                                                                        {
                                                                                                                                            selected: 'selected',
                                                                                                                                            class: 'opt' + i,
                                                                                                                                            value: i,
                                                                                                                                            text: carregaDependentes[i]
                                                                                                                                        }));
                                                                                                                            } else {
                                                                                                                                $(".select_dependentes:eq(1)").append($('<option>',
                                                                                                                                        {
                                                                                                                                            class: 'opt' + i,
                                                                                                                                            value: i,
                                                                                                                                            text: carregaDependentes[i]
                                                                                                                                        }));
                                                                                                                            }
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        for (var i = 0; i < carregaDependentes.length; i++) {

                                                                                                                            if (gambiDepPensao[1] == carregaDependentes[i]) {
                                                                                                                                $(".select_dependentes:eq(1)").append($('<option>',
                                                                                                                                        {
                                                                                                                                            selected: 'selected',
                                                                                                                                            class: 'opt' + i,
                                                                                                                                            value: i,
                                                                                                                                            text: carregaDependentes[i]
                                                                                                                                        }));
                                                                                                                            } else {
                                                                                                                                $(".select_dependentes:eq(1)").append($('<option>',
                                                                                                                                        {
                                                                                                                                            class: 'opt' + i,
                                                                                                                                            value: i,
                                                                                                                                            text: carregaDependentes[i]
                                                                                                                                        }));
                                                                                                                            }
                                                                                                                        }
                                                                                                                        $(".select_dependentes:eq(1)").append($('<option>',
                                                                                                                                {
                                                                                                                                    value: 99,
                                                                                                                                    text: "Dependentes"
                                                                                                                                }));

                                                                                                                    }

                                                                                                                    if ($(".select_dependentes:eq(2)").children().val() == 99) {
                                                                                                                        $(".dt_nasc_dependente:eq(2)").val("");
                                                                                                                        $(".select_dependentes:eq(2)").empty();

                                                                                                                        $(".select_dependentes:eq(2)").append($('<option>',
                                                                                                                                {

                                                                                                                                    value: 99,
                                                                                                                                    text: "Dependentes"
                                                                                                                                }));

                                                                                                                        for (var i = 0; i < carregaDependentes.length; i++) {

                                                                                                                            if (gambiDepPensao[2] == carregaDependentes[i]) {
                                                                                                                                $(".select_dependentes:eq(2)").append($('<option>',
                                                                                                                                        {
                                                                                                                                            selected: 'selected',
                                                                                                                                            class: 'opt' + i,
                                                                                                                                            value: i,
                                                                                                                                            text: carregaDependentes[i]
                                                                                                                                        }));
                                                                                                                            } else {
                                                                                                                                $(".select_dependentes:eq(2)").append($('<option>',
                                                                                                                                        {
                                                                                                                                            class: 'opt' + i,
                                                                                                                                            value: i,
                                                                                                                                            text: carregaDependentes[i]
                                                                                                                                        }));
                                                                                                                            }
                                                                                                                        }
                                                                                                                    } else {


                                                                                                                        for (var i = 0; i < carregaDependentes.length; i++) {

                                                                                                                            if (gambiDepPensao[2] == carregaDependentes[i]) {
                                                                                                                                $(".select_dependentes:eq(2)").append($('<option>',
                                                                                                                                        {
                                                                                                                                            selected: 'selected',
                                                                                                                                            class: 'opt' + i,
                                                                                                                                            value: i,
                                                                                                                                            text: carregaDependentes[i]
                                                                                                                                        }));
                                                                                                                            } else {
                                                                                                                                $(".select_dependentes:eq(2)").append($('<option>',
                                                                                                                                        {
                                                                                                                                            class: 'opt' + i,
                                                                                                                                            value: i,
                                                                                                                                            text: carregaDependentes[i]
                                                                                                                                        }));
                                                                                                                            }
                                                                                                                        }
                                                                                                                        $(".select_dependentes:eq(2)").append($('<option>',
                                                                                                                                {
                                                                                                                                    value: 99,
                                                                                                                                    text: "Dependentes"
                                                                                                                                }));

                                                                                                                    }



                                                                                                                    $(".input_nome_fav").on("change", function () {
                                                                                                                        var dependentes = [];

                                                                                                                        if ($("#filho_1").val() != "") {
                                                                                                                            dependentes.push($("#filho_1").val());
                                                                                                                        }
                                                                                                                        if ($("#filho_2").val() != "") {
                                                                                                                            dependentes.push($("#filho_2").val());
                                                                                                                        }
                                                                                                                        if ($("#filho_3").val() != "") {
                                                                                                                            dependentes.push($("#filho_3").val());
                                                                                                                        }
                                                                                                                        if ($("#filho_4").val() != "") {
                                                                                                                            dependentes.push($("#filho_4").val());
                                                                                                                        }
                                                                                                                        if ($("#filho_5").val() != "") {
                                                                                                                            dependentes.push($("#filho_5").val());
                                                                                                                        }
                                                                                                                        if ($("#filho_6").val() != "") {
                                                                                                                            dependentes.push($("#filho_6").val());
                                                                                                                        }
                                                                                                                        $('.select_dependentes').empty();

                                                                                                                        $('.select_dependentes').append($('<option>',
                                                                                                                                {
                                                                                                                                    value: 99,
                                                                                                                                    text: "Dependentes"
                                                                                                                                }));

                                                                                                                        for (var i = 0; i < dependentes.length; i++) {

                                                                                                                            $('.select_dependentes').append($('<option>',
                                                                                                                                    {
                                                                                                                                        value: i,
                                                                                                                                        text: dependentes[i]
                                                                                                                                    }));
                                                                                                                        }
                                                                                                                    });

                                                                                                                    $('.select_dependentes').on("change", function () {


                                                                                                                        depentente_select_value2 = 0;
                                                                                                                        depentente_select_value2 = parseInt($(this).val());
                                                                                                                        depentente_select_value2 = depentente_select_value2 + 1;
                                                                                                                        selector = "#data_filho_" + depentente_select_value2;
                                                                                                                        selector2 = "#cpf_" + depentente_select_value2;

                                                                                                                        $(this).parent().siblings(".td_dtnasc").children(".dt_nasc_dependente").val($(selector).val());
                                                                                                                        $(this).parent().siblings(".td_cpf").children(".cpf_dependente").val($(selector2).val());

                                                                                                                    });





    <?php if ($row['pde'] != 1) { ?>


                                                                                                                        $("#data_pde").hide();
    <?php } ?>
                                                                                                                    $("#id_pde").click(function () {

                                                                                                                        if ($(this).is(":checked")) {
                                                                                                                            $("#data_pde").show();
                                                                                                                            $("#tr-dias-pde").show();
                                                                                                                        } else {
                                                                                                                            $("#data_pde").hide();
                                                                                                                            $("#tr-dias-pde").hide();
                                                                                                                        }
                                                                                                                    });
                                                                                                                    $('#dias-pde').blur(function () {
                                                                                                                        if ($('#data_entrada').val() != '') {



                                                                                                                            /**
                                                                                                                             * RECUPERANDO DATA DE ENTRADA
                                                                                                                             */
                                                                                                                            var dataEntrada = $('#data_entrada').val(); /**24/03/2016**/


                                                                                                                            /**
                                                                                                                             * EXPLODE DE DATA DE ENTRADA
                                                                                                                             */
                                                                                                                            var explode = dataEntrada.split("/");

                                                                                                                            /**
                                                                                                                             * PREENCHENDO VARIAVEIS
                                                                                                                             */
                                                                                                                            var dia = parseInt(explode[0]);
                                                                                                                            var mes = parseInt(explode[1]);
                                                                                                                            //var mes = parseInt(explode[1]) - 1;
                                                                                                                            var ano = parseInt(explode[2]);

                                                                                                                            /**
                                                                                                                             * OBJETO
                                                                                                                             * date.setTime( date.getTime() + days * 86400000 )
                                                                                                                             */


                                                                                                                            //Diminui 1 do valor total pois o dia inicial conta como dia trabalhado
                                                                                                                            var soma_dias = $('#dias-pde').val() - 1;

                                                                                                                            var data = new Date(ano, mes, dia);
                                                                                                                            data.setDate(data.getDate() + soma_dias);
                                                                                                                            var novaData = data.getDate() + '/' + data.getMonth() + '/' + data.getFullYear();

                                                                                                                            //var novaData = str_pad(data.getDate(), 2, '0', 'STR_PAD_LEFT') + "/" + str_pad(data.getMonth()+1 , 2, '0', 'STR_PAD_LEFT') + "/" + data.getFullYear();
                                                                                                                            $("#data_pde").val(novaData);

                                                                                                                        }
                                                                                                                    });


                                                                                                                    if ($("#id_pde").is(':checked')) {

                                                                                                                        $("#tr-dias-pde").show();
                                                                                                                    }
                                                                                                                    //#data_pde,
                                                                                                                    $('#data_nasc_conjuge, #data_nasc_pai, #data_nasc_mae, #data_escola, #data_filho_1,#data_filho_2, #data_filho_3,#data_filho_4,#data_filho_5, #data_filho_6,#data_rg,\n\
                                                                                                            #data_ctps, #data_pis, #data_entrada,#data_exame, #data_nasc_avo_h, #data_nasc_avo_m, #data_nasc_bisavo_h, #data_nasc_bisavo_m, #data_emissao,#ano_chegada_pais,#emissao_crt,#validade_crt').datepicker({
                                                                                                                        changeMonth: true,
                                                                                                                        changeYear: true,
                                                                                                                        yearRange: "1950:<?php echo date('Y') ?>",
                                                                                                                        dateFormat: "dd/mm/yy"
                                                                                                                    });



                                                                                                                    $('#data_nasc').datepicker({
                                                                                                                        changeMonth: true,
                                                                                                                        changeYear: true,
                                                                                                                        yearRange: "1950:<?php echo date('Y') - 16 ?>",
                                                                                                                        maxDate: '-16y',
                                                                                                                        dateFormat: 'dd/mm/yy'
                                                                                                                    });

                                                                                                                    $("#data_nasc").change(function () {

                                                                                                                        //formato do brasil 'pt-br'
                                                                                                                        moment.locale('pt-br');
                                                                                                                        //setando data1
                                                                                                                        var data1 = moment($(this).val(), 'DD/MM/YYYY');
                                                                                                                        //setando data2
                                                                                                                        var data2 = moment(moment().format('DD/MM/YYYY'), 'DD/MM/YYYY');
                                                                                                                        //tirando a diferenca da data2 - data1 em dias
                                                                                                                        var diff = data2.diff(data1, 'years');

                                                                                                                        if (diff < 16) {
                                                                                                                            alert("Data de nascimento inválida.");
                                                                                                                            $(this).val('');
                                                                                                                        }

                                                                                                                    });



                                                                                                                    $('#portador1,#portador2,#portador3, #portador4, #portador5, #portador6').change(function () {



                                                                                                                        var elemento = $(this);
                                                                                                                        var linha = elemento.parent().parent();
                                                                                                                        var nome = linha.find('.nome_filho').val();
                                                                                                                        var data = linha.find('.data_filho').val();


                                                                                                                        if (nome == '') {
                                                                                                                            alert("Preencha o nome do filho.");
                                                                                                                            linha.find('.nome_filho').focus();
                                                                                                                            elemento.attr('checked', false);
                                                                                                                            return false;
                                                                                                                        }
                                                                                                                        if (data == '') {
                                                                                                                            alert("Preencha a data de nascimento do filho.");
                                                                                                                            linha.find('.data_filho').focus();
                                                                                                                            elemento.attr('checked', false);
                                                                                                                            return false;
                                                                                                                        }


                                                                                                                    });

                                                                                                                    $('#ddir_pai').change(function () {

                                                                                                                        var linha = $(this).parent().parent();
                                                                                                                        var pai = linha.find('#pai')

                                                                                                                        if (pai.val() == '') {
                                                                                                                            alert('Preencha o nome do pai.');
                                                                                                                            pai.focus();
                                                                                                                            return false;
                                                                                                                        }


                                                                                                                    });


                                                                                                                    $('#ddir_mae').change(function () {

                                                                                                                        var linha = $(this).parent().parent();
                                                                                                                        var mae = linha.find('#mae')

                                                                                                                        if (mae.val() == '') {
                                                                                                                            alert('Preencha o nome da mãe.');
                                                                                                                            mae.focus();
                                                                                                                            return false;
                                                                                                                        }
                                                                                                                    });

                                                                                                                    $('#ddir_conjuge').change(function () {

                                                                                                                        var linha = $(this).parent().parent();
                                                                                                                        var conjuge = linha.find('#conjuge')

                                                                                                                        if (conjuge.val() == '') {
                                                                                                                            alert('Preencha o nome do conjuge.');
                                                                                                                            conjuge.focus();
                                                                                                                            return false;
                                                                                                                        }
                                                                                                                    });

                                                                                                                    $('#ddir_avo_h').change(function () {

                                                                                                                        var linha = $(this).parent().parent();
                                                                                                                        var conjuge = linha.find('#avo_h')

                                                                                                                        if (conjuge.val() == '') {
                                                                                                                            alert('Preencha o nome do Avô.');
                                                                                                                            conjuge.focus();
                                                                                                                            $(this).attr('checked', false);
                                                                                                                        }
                                                                                                                    });

                                                                                                                    $('#ddir_avo_m').change(function () {

                                                                                                                        var linha = $(this).parent().parent();
                                                                                                                        var conjuge = linha.find('#avo_m')

                                                                                                                        if (conjuge.val() == '') {
                                                                                                                            alert('Preencha o nome do Avó.');
                                                                                                                            conjuge.focus();
                                                                                                                            $(this).attr('checked', false);
                                                                                                                        }
                                                                                                                    });

                                                                                                                    $('#ddir_bisavo_h').change(function () {

                                                                                                                        var linha = $(this).parent().parent();
                                                                                                                        var conjuge = linha.find('#bisavo_h')

                                                                                                                        if (conjuge.val() == '') {
                                                                                                                            alert('Preencha o nome do Bisavô.');
                                                                                                                            conjuge.focus();
                                                                                                                            $(this).attr('checked', false);
                                                                                                                        }
                                                                                                                    });
                                                                                                                    $('#ddir_bisavo_m').change(function () {

                                                                                                                        var linha = $(this).parent().parent();
                                                                                                                        var conjuge = linha.find('#bisavo_m')

                                                                                                                        if (conjuge.val() == '') {
                                                                                                                            alert('Preencha o nome do Bisavó.');
                                                                                                                            conjuge.focus();
                                                                                                                            $(this).attr('checked', false);
                                                                                                                        }
                                                                                                                    });

                                                                                                                    /*******************PENSÃO ALIMENTICIA*******************/
                                                                                                                    var valor = "";
                                                                                                                    $("input:radio[name='pensao_alimenticia']").each(function () {
                                                                                                                        if ($(this).is(':checked')) {
                                                                                                                            valor = parseInt($(this).val());
                                                                                                                        }
                                                                                                                    });

                                                                                                                    if (valor == 0) {
                                                                                                                        $("select[name='pensao_percentual']").hide().val('').removeClass('validate[required]');
                                                                                                                    } else {
                                                                                                                        $("select[name='pensao_percentual']").show().addClass('validate[required]');

                                                                                                                    }

                                                                                                                    $("input:radio[name='pensao_alimenticia']").click(function () {
                                                                                                                        if ($(this).val() == 0) {
                                                                                                                            $("select[name='pensao_percentual']").hide().val('').removeClass('validate[required]');
                                                                                                                        } else {
                                                                                                                            $("select[name='pensao_percentual']").show().addClass('validate[required]');
                                                                                                                        }
                                                                                                                    });
                                                                                                                    /********************************************************/


                                                                                                                    $('body').on('click', "#novo_inss", function () {
                                                                                                                        console.log('teste');
                                                                                                                        $("#desconto_inss").append(
                                                                                                                                "<tr class='outra_empresa'>" +
                                                                                                                                "<td class='secao'>Salário da outra empresa:</td>" +
                                                                                                                                "<td>" +
                                                                                                                                "<input name='salario_outra_empresa[]' type='text' size='12' class='formata_valor'>" +
                                                                                                                                "<input name='id_inss[]' type='hidden' value='0'>" +
                                                                                                                                "</td>" +
                                                                                                                                "<td class='secao'>Desconto da outra empresa:</td>" +
                                                                                                                                "<td>" +
                                                                                                                                "<input name='desconto_outra_empresa[]' type='text' size='12' class='formata_valor'>" +
                                                                                                                                "</td>" +
                                                                                                                                "<td class='secao'>Início:</td>" +
                                                                                                                                "<td>" +
                                                                                                                                "<input type='text' name='inicio_inss[]' class='data_inss'><br>" +
                                                                                                                                "</td>" +
                                                                                                                                "<td class='secao'>Fim:</td>" +
                                                                                                                                "<td>" +
                                                                                                                                "<input type='text' name='fim_inss[]' class='data_inss'><br>" +
                                                                                                                                "</td>" +
                                                                                                                                "<td style='vertical-align:middle; text-align: center;'>" +
                                                                                                                                "<button type='button' class='excluir_inss'>Excluir</button>" +
                                                                                                                                "</td>" +
                                                                                                                                "</tr>"
                                                                                                                                );
                                                                                                                        $('.formata_valor').priceFormat({
                                                                                                                            prefix: '',
                                                                                                                            centsSeparator: ',',
                                                                                                                            thousandsSeparator: '.'
                                                                                                                        });
                                                                                                                        $('.data_inss').datepicker({
                                                                                                                            changeYear: true,
                                                                                                                            changeMonth: true
                                                                                                                        });
                                                                                                                    });

                                                                                                                    $('.data_inss').datepicker({
                                                                                                                        changeYear: true,
                                                                                                                        changeMonth: true
                                                                                                                    });

                                                                                                                    $('body').on('click', ".excluir_inss", function () {
                                                                                                                        var id = $(this).data('id');
                                                                                                                        var $this = $(this);
                                                                                                                        if (id > 0) {
                                                                                                                            if (confirm("Tem certeza que quer excluir?")) {
                                                                                                                                $.post(window.location.href, {method: 'excluir_inss', id: id}, function (data) {
                                                                                                                                    alert(data.msg);
                                                                                                                                    if (data.status) {
                                                                                                                                        $this.closest('tr').remove();
                                                                                                                                    }
                                                                                                                                }, 'json');
                                                                                                                            }
                                                                                                                        } else {
                                                                                                                            $(this).closest('tr').remove();
                                                                                                                        }

                                                                                                                    });

                                                                                                                    /**
                                                                                                                     * MUDANDO BASE DE PENSÃO
                                                                                                                     */
                                                                                                                    $("body").on("change", ".mudaBasePensao", function () {
                                                                                                                        var t = $(this);
                                                                                                                        var base = t.val();

                                                                                                                        t.parent().next().find(".quantSalMinimo, .valorfixo").hide();
                                                                                                                        t.parent().next().next().find(".quantSalMinimo, .valorfixo").hide();
                                                                                                                        //                                                                                                                                    $(".quantSalMinimo, .valorfixo").hide();
                                                                                                                        if (base == 1) {
                                                                                                                            t.parent().next().next().find(".valorfixo").val(0);
                                                                                                                            t.parent().next().next().find(".quantSalMinimo").val(0);
                                                                                                                        } else if (base == 2) {
                                                                                                                            t.parent().next().next().find(".valorfixo").val(0);
                                                                                                                            t.parent().next().next().find(".quantSalMinimo").val(0);
                                                                                                                        } else if (base == 3) {
                                                                                                                            t.parent().next().next().find(".valorfixo").val(0);
                                                                                                                            t.parent().next().find(".quantSalMinimo").show();
                                                                                                                            t.parent().next().next().find(".quantSalMinimo").show();
                                                                                                                            //                                                                                                                                        $(".quantSalMinimo").show();
                                                                                                                        } else if (base == 4) {
                                                                                                                            t.parent().next().next().find(".quantSalMinimo").val(0);
                                                                                                                            t.parent().next().find(".valorfixo").show();
                                                                                                                            t.parent().next().next().find(".valorfixo").show();
                                                                                                                            //                                                                                                                                        $(".valorfixo").show();
                                                                                                                        } else if (base == 5) {
                                                                                                                            t.parent().next().next().find(".valorfixo").val(0);
                                                                                                                            t.parent().next().next().find(".quantSalMinimo").val(0);
                                                                                                                        } else if (base == 6) {
                                                                                                                            t.parent().next().next().find(".valorfixo").val(0);
                                                                                                                            t.parent().next().next().find(".quantSalMinimo").val(0);
                                                                                                                        }

                                                                                                                    });
                                                                                                                    $('.mudaBasePensao').trigger('change');

                                                                                                                    $(".porcentagem").maskMoney({suffix: '%', thousands: '', precision: 2});
    //                                                                                                                    $(".valor").maskMoney({thousands: '', precision: 2});

                                                                                                                    $("#id_banco_nacional").change(function () {
                                                                                                                        var txt = $("#id_banco_nacional option:selected").text();
                                                                                                                        $('#nome_banco').val(txt);
                                                                                                                    });



                                                                                                                    /**
                                                                                                                     * CONDIÇÕES DE INSERÇÃO DE INFORMAÇÃO EM VALE TRANSPORTE
                                                                                                                     * @author Lucas Praxedes
                                                                                                                     */

                                                                                                                    var i, valLinha, valVt, r, vtRow, opValor, opLinha, vtQnt, vtCard, aux;
                                                                                                                    aux = 0;

                                                                                                                    for (i = 1; i <= 5; i++) {
                                                                                                                        valLinha = $("#vt_linha" + i).val();
                                                                                                                        valVt = $('#vt_valor' + i).val();
                                                                                                                        if (valLinha != 0 || valVt != 0) {
                                                                                                                            $("#vtRow" + i).removeClass('hide').addClass('show');
                                                                                                                            aux++;
                                                                                                                        }
                                                                                                                    }

                                                                                                                    if (aux > 0) {
                                                                                                                        aux++;
                                                                                                                        $("#vtRow" + aux).removeClass('hide').addClass('show');
                                                                                                                    }

                                                                                                                    $(".opLinha").change(function () {
                                                                                                                        valLinha = $(this).val();
                                                                                                                        opLinha = $(this).data('linha');
                                                                                                                        valVt = $('#vt_valor' + opLinha).val();
                                                                                                                        vtRow = opLinha + 1;

                                                                                                                        if (valLinha == 0 && valVt == 0) {
                                                                                                                            for (i = vtRow; i <= 5; i++) {
                                                                                                                                $("#valvt" + i).removeClass('show').addClass('hide');
                                                                                                                                $("#vt_valor" + i).removeClass('hide').addClass('show');
                                                                                                                                $("#vtRow" + i).removeClass('show').addClass('hide');
                                                                                                                                $("#vt_valor" + i).val('0');
                                                                                                                                $("#vt_linha" + i).val('0');
                                                                                                                                $("#vt_qtd" + i).val('0');
                                                                                                                                $("#vt_card" + i).val('0');
                                                                                                                            }
                                                                                                                        } else if (valLinha != 0 || valVt != 0) {
                                                                                                                            $("#vtRow" + vtRow).removeClass('hide').addClass('show');
                                                                                                                        }

                                                                                                                        if (valLinha == 0) {
                                                                                                                            $("#valvt" + opLinha).removeClass('show').addClass('hide');
                                                                                                                            $("#vt_valor" + opLinha).removeClass('hide').addClass('show');
                                                                                                                        } else {
                                                                                                                            $("#valvt" + opLinha).removeClass('hide').addClass('show');
                                                                                                                            $.ajax({
                                                                                                                                url: '',
                                                                                                                                method: 'POST',
                                                                                                                                dataType: 'json',
                                                                                                                                data: {method: 'valLinha', linha: valLinha},
                                                                                                                                success: function (data) {
                                                                                                                                    $("#valvt" + opLinha).html('R$ ' + data.replace('.', ' ').replace(',', '.').replace(' ', ','));


                                                                                                                                    console.log(data);
                                                                                                                                    $("#vt_valor" + opLinha).find("option").each(function (idx, obj) {
                                                                                                                                        //                                                                                                                                                    console.log(data,$(this).html().trim());
                                                                                                                                        if ($(this).html().trim() == data) {
                                                                                                                                            $(this).prop("selected", true);
                                                                                                                                        }
                                                                                                                                    });
                                                                                                                                }
                                                                                                                            });
                                                                                                                            $("#vt_valor" + opLinha).removeClass('show').addClass('hide');
                                                                                                                            $("#vt_valor" + opLinha).val('0');
                                                                                                                        }
                                                                                                                    });

                                                                                                                    $(".opValor").change(function () {
                                                                                                                        valLinha = $(this).val();
                                                                                                                        opValor = $(this).data('valor');
                                                                                                                        valVt = $('#vt_valor' + opValor).val();
                                                                                                                        vtRow = opValor + 1;

                                                                                                                        if (valLinha == 0 && valVt == 0) {
                                                                                                                            for (i = vtRow; i <= 5; i++) {
                                                                                                                                $("#valvt" + i).removeClass('show').addClass('hide');
                                                                                                                                $("#vt_valor" + i).removeClass('hide').addClass('show');
                                                                                                                                $("#vtRow" + i).removeClass('show').addClass('hide');

                                                                                                                                $("#vt_valor" + i).val()

                                                                                                                                $("#vt_valor" + i).val('0');
                                                                                                                                $("#vt_linha" + i).val('0');
                                                                                                                                $("#vt_qtd" + i).val('0');
                                                                                                                                $("#vt_card" + i).val('0');
                                                                                                                            }
                                                                                                                        } else if (valLinha != 0 || valVt != 0) {
                                                                                                                            $("#vtRow" + vtRow).removeClass('hide').addClass('show');
                                                                                                                        }
                                                                                                                    });

                                                                                                                    $(".vtQnt").change(function () {
                                                                                                                        vtQnt = $(this).data('qnt');
                                                                                                                        opValor = $("#vt_valor" + vtQnt).val();
                                                                                                                        opLinha = $("#vt_linha" + vtQnt).val();

                                                                                                                        if (opValor == 0 && opLinha == 0 && vtQnt != 0) {
                                                                                                                            $(this).val('0');
                                                                                                                            bootAlert("Selecione uma linha ou valor antes de inserir o número do cartão.", "Inserção Invalida", null, "danger");
                                                                                                                        }
                                                                                                                    });

                                                                                                                    $(".vtCard").change(function () {
                                                                                                                        vtCard = $(this).data('card');
                                                                                                                        opValor = $("#vt_valor" + vtCard).val();
                                                                                                                        opLinha = $("#vt_linha" + vtCard).val();

                                                                                                                        if (opValor == 0 && opLinha == 0 && vtCard != 0) {
                                                                                                                            $(this).val('0');
                                                                                                                            bootAlert("Selecione uma linha ou valor antes de inserir o número do cartão.", "Inserção Invalida", null, "danger");
                                                                                                                        }
                                                                                                                    });
                                                                                                                    /************************************FIM DAS CONDIÇÕES DE VT*******************************************/
                                                                                                                    /******************************************************************************************************/
                                                                                                                });</script>
                                                                                                            </body>
                                                                                                            </html>
                                                                                                            <?php
                                                                                                        } else {
                                                                                                            //echo '<pre>'.json_encode($_REQUEST, JSON_PRETTY_PRINT).'</pre>';
                                                                                                            // echo '<pre>'.json_encode($_REQUEST['nome_favorecido'][0], JSON_PRETTY_PRINT).'</pre>';
                                                                                                            /**
                                                                                                             * ATUALIZANDO A TABELA DE RH_CLT
                                                                                                             * COM A DATA ATUAL DA AÇÃO DE 
                                                                                                             * FINALIZAR A FOLHA
                                                                                                             */
//                                                    $rh->Clt->setDefault()->setIdClt($_REQUEST['id_clt'])->onUpdate();

                                                                                                            $dataEntrada = $_REQUEST['data_entrada'];
                                                                                                            $ano_entrada = date("Y", strtotime(str_replace("/", "-", $dataEntrada)));

                                                                                                            /* if ($ano_entrada < 2009) {

                                                                                                              print "<html>
                                                                                                              <head>
                                                                                                              <title>:: Intranet ::</title>
                                                                                                              </head>
                                                                                                              <body>
                                                                                                              <script type='text/javascript'>
                                                                                                              alert('Digite uma data de entrada Valida');
                                                                                                              history.back();
                                                                                                              </script>
                                                                                                              </body>
                                                                                                              </html>";

                                                                                                              exit;
                                                                                                              } */
                                                                                                            include('log_alter_clt.php');

                                                                                                            $id_clt = $_REQUEST['id_clt'];
                                                                                                            $result = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$id_clt'");
                                                                                                            $row = mysql_fetch_array($result);
                                                                                                            $data_hoje = date('Y-m-d');
                                                                                                            $id_user = $_COOKIE['logado'];
                                                                                                            $id_projeto = $_REQUEST['pro'];
                                                                                                            $regiao = $_REQUEST['regiao'];
                                                                                                            $horario = $_REQUEST['horario'];
                                                                                                            $id_bolsista = $_REQUEST['id_bolsista'];
                                                                                                            $nome = mysql_real_escape_string(trim($_REQUEST['nome']));
                                                                                                            $assinatura = $_REQUEST['assinatura'];
                                                                                                            $assinatura2 = $_REQUEST['assinatura2'];
                                                                                                            $assinatura3 = $_REQUEST['assinatura3'];
                                                                                                            $sexo = $_REQUEST['sexo'];
                                                                                                            $endereco = mysql_real_escape_string(trim($_REQUEST['endereco']));

                                                                                                            $bairro = mysql_real_escape_string(trim($_REQUEST['bairro']));
                                                                                                            $cidade = mysql_real_escape_string(trim($_REQUEST['cidade']));
                                                                                                            $uf = $_REQUEST['uf'];
                                                                                                            $cep = $_REQUEST['cep'];
                                                                                                            $tel_fixo = $_REQUEST['tel_fixo'];
                                                                                                            $tel_cel = $_REQUEST['tel_cel'];
                                                                                                            $tel_rec = $_REQUEST['tel_rec'];
                                                                                                            $data_nasci = $_REQUEST['data_nasc'];
                                                                                                            $municipio_nasc = addslashes($_REQUEST['municipio_nasc']);
                                                                                                            $uf_nasc = $_REQUEST['uf_nasc_select'];

                                                                                                            $complemento = $_REQUEST['complemento'];
                                                                                                            $unidocencia = ($_REQUEST['unidocencia'] == 'on') ? $unidocencia = 1 : $unidocencia = 0;
//    //trata unidade
//    $locacao = explode("//", $_REQUEST['locacao']);
//    $locacao_nome = $locacao[0];
//    $locacao_id = $locacao[1];
//Verifica se o uf_nasc foi digitado ou selecionado
                                                                                                            if (empty($uf_nasc)) {
                                                                                                                $uf_nasc = $_REQUEST['uf_nasc_text'];
                                                                                                            }

                                                                                                            $tipo_sanguineo = $_REQUEST['tiposanguineo'];

                                                                                                            $cod_nacionalidade = $_REQUEST['nacionalidade'];
//NOME DA NACIONALIDADE
                                                                                                            $qr_nome_nacionalidade = mysql_query("select nome from cod_pais_rais where codigo = $cod_nacionalidade");
                                                                                                            $row_nome_nacionalidade = mysql_fetch_row($qr_nome_nacionalidade);
                                                                                                            $nome_nacionalidade = $row_nome_nacionalidade[0];

                                                                                                            $ano_chegada_pais = $_REQUEST['ano_chegada_pais'];



                                                                                                            $civil = explode('|', $_REQUEST['civil']);
                                                                                                            $estCivilId = $civil[0];
                                                                                                            $estCivilNome = $civil[1];
                                                                                                            $rg = removeAspas($_REQUEST['rg']);
                                                                                                            $uf_rg = $_REQUEST['uf_rg'];
                                                                                                            $secao = removeAspas($_REQUEST['secao']);
                                                                                                            $data_rg = $_REQUEST['data_rg'];
                                                                                                            $cpf = $_REQUEST['cpf'];
                                                                                                            $conselho = removeAspas($_REQUEST['conselho']);
                                                                                                            $titulo = removeAspas($_REQUEST['titulo']);
                                                                                                            $zona = removeAspas($_REQUEST['zona']);
                                                                                                            $orgao = removeAspas($_REQUEST['orgao']);

                                                                                                            $pai = mysql_real_escape_string(trim($_REQUEST['pai']));
                                                                                                            $mae = mysql_real_escape_string(trim($_REQUEST['mae']));
                                                                                                            $avo_h = mysql_real_escape_string(trim($_REQUEST['avo_h']));
                                                                                                            $avo_m = mysql_real_escape_string(trim($_REQUEST['avo_m']));
                                                                                                            $bisavo_h = mysql_real_escape_string(trim($_REQUEST['bisavo_h']));
                                                                                                            $bisavo_m = mysql_real_escape_string(trim($_REQUEST['bisavo_m']));

                                                                                                            $conjuge = $_REQUEST['conjuge'];
                                                                                                            $nacionalidade_pai = $_REQUEST['nacionalidade_pai'];
                                                                                                            $nacionalidade_mae = $_REQUEST['nacionalidade_mae'];


                                                                                                            $data_nasc_pai = $_REQUEST['data_nasc_pai'];
                                                                                                            $data_nasc_mae = $_REQUEST['data_nasc_mae'];
                                                                                                            $data_nasc_conjuge = $_REQUEST['data_nasc_conjuge'];
                                                                                                            $data_nasc_avo_h = $_REQUEST['data_nasc_avo_h'];
                                                                                                            $data_nasc_avo_m = $_REQUEST['data_nasc_avo_m'];
                                                                                                            $data_nasc_bisavo_h = $_REQUEST['data_nasc_bisavo_h'];
                                                                                                            $data_nasc_bisavo_m = $_REQUEST['data_nasc_bisavo_m'];

                                                                                                            $ddir_pai = $_REQUEST['ddir_pai'];
                                                                                                            $ddir_mae = $_REQUEST['ddir_mae'];
                                                                                                            $ddir_conjuge = $_REQUEST['ddir_conjuge'];
                                                                                                            $ddir_avo_h = $_REQUEST['ddir_avo_h'];
                                                                                                            $ddir_avo_m = $_REQUEST['ddir_avo_m'];
                                                                                                            $ddir_bisavo_h = $_REQUEST['ddir_bisavo_h'];
                                                                                                            $ddir_bisavo_m = $_REQUEST['ddir_bisavo_m'];

                                                                                                            $numero = mysql_real_escape_string(trim($_REQUEST['numero']));
                                                                                                            $estuda = $_REQUEST['estuda'];
                                                                                                            $data_escola = $_REQUEST['data_escola'];
                                                                                                            $escolaridade = $_REQUEST['escolaridade'];
                                                                                                            $instituicao = $_REQUEST['instituicao'];
                                                                                                            $curso = mysql_real_escape_string(trim($_REQUEST['curso']));
                                                                                                            $banco = mysql_real_escape_string(trim($_REQUEST['banco']));
                                                                                                            $agencia = $_REQUEST['agencia'];
                                                                                                            $agencia_dv = $_REQUEST['agencia_dv'];
                                                                                                            $conta = $_REQUEST['conta'];
                                                                                                            $conta_dv = $_REQUEST['conta_dv'];
                                                                                                            $tipoDeConta = $_REQUEST['radio_tipo_conta'];
                                                                                                            $localpagamento = mysql_real_escape_string(trim($_REQUEST['localpagamento']));
                                                                                                            $apolice = $_REQUEST['apolice'];
                                                                                                            $tabela = $_REQUEST['tabela'];
                                                                                                            $data_entrada = $_REQUEST['data_entrada'];
                                                                                                            $codigo = $_REQUEST['codigo'];
                                                                                                            $tipo_contratacao = $_REQUEST['tipo_bol'];
                                                                                                            $id_curso = $_REQUEST['id_curso'];
                                                                                                            $trabalho = removeAspas($_REQUEST['trabalho']);
                                                                                                            $dependente = $_REQUEST['dependente'];

                                                                                                            // novo salvar do nome_banco
                                                                                                            $id_banco_nacional = $_REQUEST['id_banco_nacional'];
                                                                                                            $nome_banco = addslashes(trim($_REQUEST['nome_banco']));
                                                                                                            // novo salvar do nome_banco       

                                                                                                            $num_crt = $_REQUEST['num_crt'];
                                                                                                            $emissao_crt = explode("/", $_REQUEST['emissao_crt']);
                                                                                                            $emissao_crt = $emissao_crt[2] . "-" . $emissao_crt[1] . "-" . $emissao_crt[0];
                                                                                                            $validade_crt = explode("/", $_REQUEST['validade_crt']);
                                                                                                            $validade_crt = $validade_crt[2] . "-" . $validade_crt[1] . "-" . $validade_crt[0];

                                                                                                            $pis = str_replace('.', '', str_replace('-', '', $_REQUEST['pis']));
                                                                                                            $fgts = removeAspas($_REQUEST['fgts']);
                                                                                                            $tipopg = $_REQUEST['tipopg'];
                                                                                                            $filhos = $_REQUEST['filhos'];
                                                                                                            $imposto_renda = $_REQUEST['imposto_renda'];
                                                                                                            $observacao = $_REQUEST['observacoes'];
                                                                                                            $pde = $_REQUEST['pde'];
                                                                                                            $data_pde = $_REQUEST['data_pde'];
                                                                                                            $medica = $_REQUEST['medica'];
                                                                                                            $plano = $_REQUEST['plano_medico'];
                                                                                                            $serie_ctps = $_REQUEST['serie_ctps'];
                                                                                                            $uf_ctps = $_REQUEST['uf_ctps'];
                                                                                                            $data_ctps = $_REQUEST['data_ctps'];
                                                                                                            $data_pis = $_REQUEST['data_pis'];
                                                                                                            $ad_noturno = $_REQUEST['ad_noturno'];
                                                                                                            $data_exame = $_REQUEST['data_exame'];
                                                                                                            $reservista = removeAspas($_REQUEST['reservista']);
                                                                                                            $cabelos = $_REQUEST['cabelos'];
                                                                                                            $peso = $_REQUEST['peso'];
                                                                                                            $altura = $_REQUEST['altura'];
                                                                                                            $olhos = $_REQUEST['olhos'];
                                                                                                            $defeito = $_REQUEST['defeito'];
                                                                                                            $cipa = $_REQUEST['cipa'];
                                                                                                            $etnia = $_REQUEST['etnia'];
                                                                                                            $deficiencia = $_REQUEST['deficiencia'];
                                                                                                            $tipo_de_admissao = $_REQUEST['tipo_admissao'];
                                                                                                            $seguro_desemprego = $_REQUEST['seguro_desemprego'];
                                                                                                            $nao_recebe_vr = $_REQUEST['nao_recebe_vr'];
                                                                                                            $cnpj = $_REQUEST['cnpj'];
                                                                                                            $valor_hora = $_REQUEST['valor_hora'];
                                                                                                            $quantidade_horas = $_REQUEST['quantidade_horas'];
                                                                                                            $quantidade_plantao = $_REQUEST['quantidade_plantao'];
                                                                                                            $valor_fixo_plantao = $_REQUEST['valor_fixo_plantao'];
                                                                                                            $valor_fixo_plantao = str_replace(',', '.', str_replace('.', '', $valor_fixo_plantao));


                                                                                                            //GRAVA LOG CENTRO CUSTO
                                                                                                            $centrocusto = $_REQUEST['centrocusto'];
                                                                                                            $centrocusto_de = $row['id_centro_custo'];

                                                                                                            if ($centrocusto != '') {
                                                                                                                if ($centrocusto != $centrocusto_de) {
                                                                                                                    $qry_log_centrocusto = "INSERT INTO centro_custo_log (id_clt, id_cc_de, id_cc_para, data_proc, id_user)
                                    VALUES ({$id_clt}, {$centrocusto_de}, {$centrocusto}, NOW(), {$_COOKIE['logado']})";
                                                                                                                    $sql_log_centrocusto = mysql_query($qry_log_centrocusto); //or die('ERRO ao inserir LOG do CENTRO DE CUSTO');
                                                                                                                }
                                                                                                            }

                                                                                                            $contrato_medico = $_POST['contrato_medico'];
                                                                                                            $email = $_POST['email'];
                                                                                                            $data_emissao = $_REQUEST['data_emissao'];
                                                                                                            $verifica_orgao = $_REQUEST['verifica_orgao'];

                                                                                                            // tipo de contrato
                                                                                                            $tipo_contrato = $_REQUEST['tipo_contrato'];

                                                                                                            $prazoExp = $_REQUEST['prazoExp'];

                                                                                                            // NOVOS CAMPOS PARA O E-SOCIAL
                                                                                                            $cod_pais_nacionalidade = $_REQUEST['cod_pais_nacionalidade'];
                                                                                                            $cod_pais_nasc = $_REQUEST['cod_pais_nasc'];
                                                                                                            $tipo_endereco = $_REQUEST['cod_tp_logradouro'];
                                                                                                            $cod_muni_nasc = $_REQUEST['cod_municipio_nasc'];
                                                                                                            $cod_cidade = $_REQUEST['cod_cidade'];

                                                                                                            // VALES
                                                                                                            $vale_refeicao = isset($_REQUEST['vale_refeicao']) ? $_REQUEST['vale_refeicao'] : NULL;
                                                                                                            $vale_alimentacao = isset($_REQUEST['vale_alimentacao']) ? $_REQUEST['vale_alimentacao'] : NULL;

                                                                                                            $valor_refeicao = (isset($_REQUEST['valor_refeicao']) && $_REQUEST['vale_refeicao'] != 0) ? str_replace(',', '.', str_replace(array('R', '$', ' ', '.'), '', $_REQUEST['valor_refeicao'])) : 'NULL';

                                                                                                            //PENSAO ALIMENTICIA
                                                                                                            $pensao_alimenticia = (isset($_REQUEST['pensao'])) ? 1 : 0;
                                                                                                            //$pensao_percentual = $_REQUEST['pensao_percentual'];
                                                                                                            ///echo "Sinesio: " . $pensao_alimenticia . "<br>";
//Inicio Verificador CPF
                                                                                                            $qrCpf = mysql_query("SELECT COUNT(id_clt) AS total,id_clt, nome FROM rh_clt WHERE cpf = '$cpf' AND id_projeto = '$id_projeto' AND id_regiao = '$regiao' AND tipo_contratacao = 2 and (data_saida == '0000-00-00' and data_demi == '0000-00-00')");
                                                                                                            $rsCpf = mysql_fetch_assoc($qrCpf);
                                                                                                            $totalCpf = $rsCpf['total'];
                                                                                                            $idClt = $rsCpf['id_clt'];



                                                                                                            if ($totalCpf > 0 && $idClt != $id_clt) {
                                                                                                                $teste = "<input type='hidden' value='$rsCpf[nome]' name='nomeTeste' id='nomeTeste'/>";
                                                                                                                echo $teste;
                                                                                                                ?>

                                                                                                                <script type="text/javascript">
                                                                                                                    var nome = document.getElementById("nomeTeste").value;
                                                                                                                    alert("Esse CPF já existe para esse projeto " + nome);
                                                                                                                    window.history.back();

                                                                                                                </script>

                                                                                                                <?php
                                                                                                                exit();
                                                                                                            }


//Fim verificador CPF
// Desconto INSS
                                                                                                            if (empty($_REQUEST['desconto_inss'])) {
                                                                                                                $desconto_inss = 0;
                                                                                                                $tipo_desconto_inss = 0;
                                                                                                                $valor_desconto_inss = 0;
                                                                                                                $trabalha_outra_empresa = 0;
//        $salario_outra_empresa = 0;
//        $desconto_outra_empresa = 0;
                                                                                                            } else {
                                                                                                                $desconto_inss = 1;
                                                                                                                $tipo_desconto_inss = $_REQUEST['tipo_desconto_inss'];

                                                                                                                if ($tipo_desconto_inss == 'isento') {
                                                                                                                    $valor_desconto_inss = 0;
                                                                                                                } elseif ($tipo_desconto_inss == 'parcial') {
                                                                                                                    $valor_desconto_inss = str_replace(',', '.', str_replace('.', '', $_REQUEST['valor_desconto_inss']));
                                                                                                                }

                                                                                                                $trabalha_outra_empresa = $_REQUEST['trabalha_outra_empresa'];

                                                                                                                if ($trabalha_outra_empresa == 'sim') {
//            $salario_outra_empresa = str_replace(',', '.', str_replace('.', '', $_REQUEST['salario_outra_empresa']));
//            $desconto_outra_empresa = str_replace(',', '.', str_replace('.', '', $_REQUEST['desconto_outra_empresa']));
                                                                                                                } elseif ($trabalha_outra_empresa == 'nao') {
//            $salario_outra_empresa = 0;
//            $desconto_outra_empresa = 0;
                                                                                                                }
                                                                                                            }

//
// TRABALHANDO COM OS VALES
                                                                                                            if (empty($_REQUEST['transporte'])) {
                                                                                                                $transporte = '0';
                                                                                                            } else {
                                                                                                                $transporte = '1';
                                                                                                            }

                                                                                                            $vale1 = $_REQUEST['vale1'];
                                                                                                            $vale2 = $_REQUEST['vale2'];
                                                                                                            $vale3 = $_REQUEST['vale3'];
                                                                                                            $vale4 = $_REQUEST['vale4'];
                                                                                                            $vale5 = $_REQUEST['vale5'];
                                                                                                            $vale6 = $_REQUEST['vale6'];
                                                                                                            $num_cartao = $_REQUEST['num_cartao'];
                                                                                                            $num_cartao2 = $_REQUEST['num_cartao2'];
//
// TRABALHANDO COM OS DEPENDENTES
                                                                                                            $filho_1 = mysql_real_escape_string(trim($_REQUEST['filho_1']));
                                                                                                            $filho_2 = mysql_real_escape_string(trim($_REQUEST['filho_2']));
                                                                                                            $filho_3 = mysql_real_escape_string(trim($_REQUEST['filho_3']));
                                                                                                            $filho_4 = mysql_real_escape_string(trim($_REQUEST['filho_4']));
                                                                                                            $filho_5 = mysql_real_escape_string(trim($_REQUEST['filho_5']));
                                                                                                            $filho_6 = mysql_real_escape_string(trim($_REQUEST['filho_6']));

                                                                                                            $data_filho_1 = $_REQUEST['data_filho_1'];
                                                                                                            $data_filho_2 = $_REQUEST['data_filho_2'];
                                                                                                            $data_filho_3 = $_REQUEST['data_filho_3'];
                                                                                                            $data_filho_4 = $_REQUEST['data_filho_4'];
                                                                                                            $data_filho_5 = $_REQUEST['data_filho_5'];
                                                                                                            $data_filho_6 = $_REQUEST['data_filho_6'];

                                                                                                            $cpf_1 = $_REQUEST['cpf_1'];
                                                                                                            $cpf_2 = $_REQUEST['cpf_2'];
                                                                                                            $cpf_3 = $_REQUEST['cpf_3'];
                                                                                                            $cpf_4 = $_REQUEST['cpf_4'];
                                                                                                            $cpf_5 = $_REQUEST['cpf_5'];
                                                                                                            $cpf_6 = $_REQUEST['cpf_6'];

                                                                                                            $portador1 = $_REQUEST['portador1'];
                                                                                                            $portador2 = $_REQUEST['portador2'];
                                                                                                            $portador3 = $_REQUEST['portador3'];
                                                                                                            $portador4 = $_REQUEST['portador4'];
                                                                                                            $portador5 = $_REQUEST['portador5'];
                                                                                                            $portador6 = $_REQUEST['portador6'];


                                                                                                            $dep1_cur_fac_ou_tec = $_REQUEST['dep1_cur_fac_ou_tec'];
                                                                                                            $dep2_cur_fac_ou_tec = $_REQUEST['dep2_cur_fac_ou_tec'];
                                                                                                            $dep3_cur_fac_ou_tec = $_REQUEST['dep3_cur_fac_ou_tec'];
                                                                                                            $dep4_cur_fac_ou_tec = $_REQUEST['dep4_cur_fac_ou_tec'];
                                                                                                            $dep5_cur_fac_ou_tec = $_REQUEST['dep5_cur_fac_ou_tec'];
                                                                                                            $dep6_cur_fac_ou_tec = $_REQUEST['dep6_cur_fac_ou_tec'];


                                                                                                            $ddir1 = ($_REQUEST['ddir1'] > 0) ? $_REQUEST['ddir1'] : '0';
                                                                                                            $ddir2 = ($_REQUEST['ddir2'] > 0) ? $_REQUEST['ddir2'] : '0';
                                                                                                            $ddir3 = ($_REQUEST['ddir3'] > 0) ? $_REQUEST['ddir3'] : '0';
                                                                                                            $ddir4 = ($_REQUEST['ddir4'] > 0) ? $_REQUEST['ddir4'] : '0';
                                                                                                            $ddir5 = ($_REQUEST['ddir5'] > 0) ? $_REQUEST['ddir5'] : '0';
                                                                                                            $ddir6 = ($_REQUEST['ddir6'] > 0) ? $_REQUEST['ddir6'] : '0';


                                                                                                            $guarda_filho1 = ($_REQUEST['guarda_filho1'] > 0) ? $_REQUEST['guarda_filho1'] : '0';
                                                                                                            $guarda_filho2 = ($_REQUEST['guarda_filho2'] > 0) ? $_REQUEST['guarda_filho2'] : '0';
                                                                                                            $guarda_filho3 = ($_REQUEST['guarda_filho3'] > 0) ? $_REQUEST['guarda_filho3'] : '0';
                                                                                                            $guarda_filho4 = ($_REQUEST['guarda_filho4'] > 0) ? $_REQUEST['guarda_filho4'] : '0';
                                                                                                            $guarda_filho5 = ($_REQUEST['guarda_filho5'] > 0) ? $_REQUEST['guarda_filho5'] : '0';
                                                                                                            $guarda_filho6 = ($_REQUEST['guarda_filho6'] > 0) ? $_REQUEST['guarda_filho6'] : '0';

                                                                                                            $id_setor = $_REQUEST['id_setor'];
                                                                                                            $id_plano_saude = $_REQUEST['id_plano_saude'];

//
// SINDICATO
                                                                                                            $sindicato = $_REQUEST['sindicato'];
                                                                                                            $ano_contribuicao = $_REQUEST['ano_contribuicao'];
                                                                                                            $radio_sindicato = $_REQUEST['radio_sindicato'];
                                                                                                            if ($radio_sindicato == 'nao') {
                                                                                                                $sindicato = NULL;
                                                                                                            }
//
// FOTO
                                                                                                            if (empty($_REQUEST['foto'])) {
                                                                                                                $foto = "0";
                                                                                                            } else {
                                                                                                                $foto = $_REQUEST['foto'];
                                                                                                            }
                                                                                                            if ($foto == "3") {
                                                                                                                $foto_banco = "0";
                                                                                                                $foto_up = "0";
                                                                                                            } elseif ($foto == "1") {
                                                                                                                $foto_banco = "1";
                                                                                                                $foto_up = "1";
                                                                                                            } else {
                                                                                                                $vendo_foto = mysql_query("SELECT foto FROM rh_clt WHERE id_clt = '$id_clt'");
                                                                                                                $row_vendo_foto = mysql_fetch_array($vendo_foto);
                                                                                                                $foto_banco = "$row_vendo_foto[foto]";
                                                                                                                $foto_up = "0";
                                                                                                            }
//
// INSALUBRIDADE
                                                                                                            if (empty($_REQUEST['insalubridade'])) {
                                                                                                                $insalubridade = "0";
                                                                                                            } else {
                                                                                                                $insalubridade = $_REQUEST['insalubridade'];
                                                                                                            }
//
// DATA DE DESATIVAÇÃO
                                                                                                            if ($status == '62') {
                                                                                                                $desativacao = $_REQUEST['data_desativacao'];
                                                                                                            } else {
                                                                                                                $desativacao = NULL;
                                                                                                            }



                                                                                                            $data_entrada2 = (!empty($data_entrada)) ? "'" . ConverteData($data_entrada) . "'" : 'null';
                                                                                                            $data_rg2 = (!empty($data_rg)) ? "'" . ConverteData($data_rg) . "'" : 'null';
                                                                                                            $data_nasci2 = (!empty($data_nasci)) ? "'" . ConverteData($data_nasci) . "'" : 'null';
                                                                                                            $data_ctps = (!empty($data_ctps)) ? "'" . ConverteData($data_ctps) . "'" : 'null';
                                                                                                            $data_pis1 = (!empty($data_pis)) ? "'" . ConverteData($data_pis) . "'" : 'null';
                                                                                                            $data_exame = (!empty($data_exame)) ? "'" . ConverteData($data_exame) . "'" : 'null';
                                                                                                            $desativacao = (!empty($desativacao)) ? "'" . ConverteData($desativacao) . "'" : 'null';
                                                                                                            $data_escola = (!empty($data_escola)) ? "'" . ConverteData($data_escola) . "'" : 'null';
                                                                                                            $data_filho_1 = (!empty($data_filho_1)) ? "'" . ConverteData($data_filho_1) . "'" : 'null';
                                                                                                            $data_filho_2 = (!empty($data_filho_2)) ? "'" . ConverteData($data_filho_2) . "'" : 'null';
                                                                                                            $data_filho_3 = (!empty($data_filho_3)) ? "'" . ConverteData($data_filho_3) . "'" : 'null';
                                                                                                            $data_filho_4 = (!empty($data_filho_4)) ? "'" . ConverteData($data_filho_4) . "'" : 'null';
                                                                                                            $data_filho_5 = (!empty($data_filho_5)) ? "'" . ConverteData($data_filho_5) . "'" : 'null';
                                                                                                            $data_filho_6 = (!empty($data_filho_6)) ? "'" . ConverteData($data_filho_6) . "'" : 'null';
                                                                                                            $data_nasc_pai = (!empty($data_nasc_pai)) ? "'" . ConverteData($data_nasc_pai) . "'" : 'null';
                                                                                                            $data_nasc_mae = (!empty($data_nasc_mae)) ? "'" . ConverteData($data_nasc_mae) . "'" : 'null';
                                                                                                            $data_nasc_conjuge = (!empty($data_nasc_conjuge)) ? "'" . ConverteData($data_nasc_conjuge) . "'" : 'null';
                                                                                                            $data_nasc_avo_h = (!empty($data_nasc_avo_h)) ? "'" . ConverteData($data_nasc_avo_h) . "'" : 'null';
                                                                                                            $data_nasc_avo_m = (!empty($data_nasc_avo_m)) ? "'" . ConverteData($data_nasc_avo_m) . "'" : 'null';
                                                                                                            $data_nasc_bisavo_h = (!empty($data_nasc_bisavo_h)) ? "'" . ConverteData($data_nasc_bisavo_h) . "'" : 'null';
                                                                                                            $data_nasc_bisavo_m = (!empty($data_nasc_bisavo_m)) ? "'" . ConverteData($data_nasc_bisavo_m) . "'" : 'null';
                                                                                                            $data_emissao = (!empty($data_emissao)) ? "'" . ConverteData($data_emissao) . "'" : 'null';
                                                                                                            $ano_chegada_pais = (!empty($ano_chegada_pais)) ? "'" . ConverteData($ano_chegada_pais) . "'" : 'null';
                                                                                                            $data_pde = (!empty($data_pde)) ? "'" . ConverteData($data_pde) . "'" : 'null';
                                                                                                            $gratificacao = $_REQUEST['gratificacao'];
// VERIFICAÇÃO SE JÁ EXISTE O CLT CADASTRADO            
                                                                                                            if ($codigo == 'INSERIR') {
                                                                                                                $resultado_teste2 = NULL;
                                                                                                            } else {
                                                                                                                $result_teste2 = mysql_query("SELECT COUNT(*) FROM rh_clt WHERE campo3 = '$codigo' AND id_antigo != '$id_bolsista' AND id_projeto = '$id_projeto'");
                                                                                                                $row_teste2 = mysql_fetch_row($result_teste2);
                                                                                                                $resultado_teste2 = $row_teste2['0'];
                                                                                                            }
// SE EXISTE AVISA E PÁRA
                                                                                                            if (!empty($resultado_teste2)) {
                                                                                                                print "<center>
                  <p>JÁ EXISTE UM FUNCIONÁRIO CADASTRADO COM ESTE CÓDIGO: <b>$codigo</b></p>
           </center>";
                                                                                                                exit;
// SENÃO PROSSEGUE
                                                                                                            } else {


///VERIFICANDO SE O CLT ESTA EM ALGUMA FOLHA
                                                                                                                $verifica_folha = mysql_num_rows(mysql_query("SELECT * FROM rh_folha as A 
                                    INNER JOIN rh_folha_proc as B
                                    ON B.id_folha = A.id_folha
                                    WHERE B.id_clt = $id_clt AND B.status IN(2, 3)"));

                                                                                                                if (empty($verifica_folha)) {
//            $sql_curso = "id_curso = '$id_curso', "; 
                                                                                                                }

                                                                                                                $favs = count($_REQUEST['id_favorecido']);
                                                                                                                $maxFavs = 6;

//                                                                                                                echo '<pre>';
//                                                                                                                print_r([$_REQUEST['select_dependentes'],
//                                                                                                                    $_REQUEST['cpf_dependente_fav'],
//                                                                                                                    $_REQUEST['tipo_de_dependente'],
//                                                                                                                    $_REQUEST['dt_nasc_dependente']]);
//                                                                                                                echo '</pre>';

                                                                                                                for ($i = 0; $i < $maxFavs; $i++) {
                                                                                                                    $sobreSalLiquido = ($_REQUEST['base'][$i] == 1) ? 1 : 0;
                                                                                                                    $sobreSalBruto = ($_REQUEST['base'][$i] == 2) ? 1 : 0;
                                                                                                                    $sobreSalMinimo = ($_REQUEST['base'][$i] == 3) ? 1 : 0;
                                                                                                                    $umTercoSobreLiquido = ($_REQUEST['base'][$i] == 5) ? 1 : 0;
                                                                                                                    $sobrePorcentagemSalMinimo = ($_REQUEST['base'][$i] == 6) ? 1 : 0;
                                                                                                                    $valorFixo = ($_REQUEST['valorfixo'][$i] != '') ? str_replace(",", ".", str_replace(".", "", $_REQUEST['valorfixo'][$i])) : 0;
                                                                                                                    //$valorFixo = str_replace(".", "", $_REQUEST['valorfixo'][$i]);
                                                                                                                    //$valorFixo = str_replace(",", ".", $valorFixo);
                                                                                                                    $incide_ferias = (isset($_REQUEST['incide_ferias'][$i])) ? 1 : 0;
                                                                                                                    $incide_rescisao = (isset($_REQUEST['incide_rescisao'][$i])) ? 1 : 0;
                                                                                                                    $incide_13 = (isset($_REQUEST['incide_13'][$i])) ? 1 : 0;
//                                                                                                                                $quant = ($_REQUEST['quant'][$i] == 5) ? 1 : 0;
                                                                                                                    $quant = ($_REQUEST['base'][$i] == 3) ? $_REQUEST['quant'][$i] : 0;
                                                                                                                    $cod_dependente = $_REQUEST['select_dependentes'][$i];



                                                                                                                    if ($cod_dependente == 0) {
                                                                                                                        $nome_dep = $_REQUEST['filho_1'];
                                                                                                                    }
                                                                                                                    if ($cod_dependente == 1) {
                                                                                                                        $nome_dep = $_REQUEST['filho_2'];
                                                                                                                    }
                                                                                                                    if ($cod_dependente == 2) {
                                                                                                                        $nome_dep = $_REQUEST['filho_3'];
                                                                                                                    }
                                                                                                                    if ($cod_dependente == 3) {
                                                                                                                        $nome_dep = $_REQUEST['filho_4'];
                                                                                                                    }
                                                                                                                    if ($cod_dependente == 4) {
                                                                                                                        $nome_dep = $_REQUEST['filho_5'];
                                                                                                                    }
                                                                                                                    if ($cod_dependente == 5) {
                                                                                                                        $nome_dep = $_REQUEST['filho_6'];
                                                                                                                    }



                                                                                                                    if (isset($_REQUEST['id_favorecido'][$i]) && $_REQUEST['id_favorecido'][$i] !== '') {

//                if ($_REQUEST['dt_nasc_dependente'][$i] != '') {                                                      
                                                                                                                        $data_nasc_dep = $_REQUEST['dt_nasc_dependente'][$i];
                                                                                                                        $data_nasc_dep = implode('-', array_reverse(explode('/', $data_nasc_dep)));
//                } else {
//                    $data_nasc_dep = '';
//                }



                                                                                                                        $upd = "UPDATE favorecido_pensao_assoc
                        SET id_clt = {$_REQUEST['id_clt']},
                        favorecido = '{$_REQUEST['nome_favorecido'][$i]}',
                        cpf = '{$_REQUEST['cpf_favorecido'][$i]}',
                        aliquota = {$_REQUEST['aliquota_favorecido'][$i]},
                        id_lista_banco = {$_REQUEST['id_lista_banco_favorecido'][$i]},
                        agencia = '{$_REQUEST['agencia_favorecido'][$i]}',
                        conta = '{$_REQUEST['conta_favorecido'][$i]}',
                        oficio = '{$_REQUEST['oficio'][$i]}',
                        sobreSalLiquido = {$sobreSalLiquido},
                        sobreSalBruto = {$sobreSalBruto},
                        sobreSalMinimo = {$sobreSalMinimo},
                        sobrePorcentagemSalMinimo = {$sobrePorcentagemSalMinimo},
                        quantSalMinimo = {$quant},
                        valorfixo = {$valorFixo},
                        umTercoSobreLiquido = {$umTercoSobreLiquido},
                        incide_ferias = {$incide_ferias},
                        incide_rescisao = {$incide_rescisao},
                        incide_13 = {$incide_13},
                        nome_dependente = '{$nome_dep}',
                        cpf_dependente = '{$_REQUEST['cpf_dependente_fav'][$i]}',
                        data_nasc_dependente = '$data_nasc_dep' ,
                        id_tipo_dependente = {$_REQUEST['tipo_de_dependente'][$i]}
                        WHERE id = {$_REQUEST['id_favorecido'][$i]}";

                                                                                                                        $nome_dep = null;

//                if () {
//                    
//                }
                                                                                                                        $antigo = $log->getLinha('favorecido_pensao_assoc', $_REQUEST['id_favorecido'][$i]);
//                echo '<pre>';
//                print_r($antigo);
//                echo '</pre>';
                                                                                                                        mysql_query($upd); // or print_r(mysql_error());
                                                                                                                        $novo = $log->getLinha('favorecido_pensao_assoc', $_REQUEST['id_favorecido'][$i]);
//                echo '<pre>';
//                print_r($novo);
//                echo '</pre>';

                                                                                                                        $log->log('2', "Favorecido ID {$_REQUEST['id_favorecido'][$i]} do CLT $id_clt atualizado", 'favorecido_pensao_assoc', $antigo, $novo);
                                                                                                                    } else {
                                                                                                                        if ($_REQUEST['nome_favorecido'][$i] != '') {
                                                                                                                            $data_nasc_dep = $_REQUEST['dt_nasc_dependente'][$i];
                                                                                                                            $data_nasc_dep = implode('-', array_reverse(explode('/', $data_nasc_dep)));

                                                                                                                            $query_pensao = "INSERT INTO favorecido_pensao_assoc (id_clt,favorecido,cpf,aliquota,id_lista_banco,agencia,conta,oficio,sobreSalLiquido,sobreSalBruto,sobreSalMinimo,quantSalMinimo,valorfixo,umTercoSobreLiquido, incide_ferias, incide_rescisao, incide_13,nome_dependente,cpf_dependente,data_nasc_dependente,id_tipo_dependente) VALUES ";
                                                                                                                            $query_pensao .= "('{$id_clt}','{$_REQUEST['nome_favorecido'][$i]}','{$_REQUEST['cpf_favorecido'][$i]}','{$_REQUEST['aliquota_favorecido'][$i]}','{$_REQUEST['id_lista_banco_favorecido'][$i]}','{$_REQUEST['agencia_favorecido'][$i]}','{$_REQUEST['conta_favorecido'][$i]}','{$_REQUEST['oficio'][$i]}','{$sobreSalLiquido}','{$sobreSalBruto}','{$sobreSalMinimo}','{$_REQUEST['quant'][$i]}','{$valorFixo}','{$umTercoSobreLiquido}', {$incide_ferias}, {$incide_rescisao}, {$incide_13},'{$nome_dep}','{$_REQUEST['cpf_dependente_fav'][$i]}','$data_nasc_dep','{$_REQUEST['tipo_de_dependente'][$i]}')";
                                                                                                                            //echo $query_pensao;
                                                                                                                            mysql_query($query_pensao); // or print_r(mysql_error());
                                                                                                                            $idFav = mysql_insert_id();
                                                                                                                            $log->log('2', "Favorecido ID $idFav criado para o CLT $id_clt", 'favorecido_pensao_assoc');
                                                                                                                        }
                                                                                                                    }
                                                                                                                }
                                                                                                                # die();
                                                                                                                /**
                                                                                                                 * MONTANDO QUERY
                                                                                                                 */
                                                                                                                //Removido por não fazer sentido - Rafael - 24/10/2016
//                                                                                                $query_remove_pensao = "DELETE FROM favorecido_pensao_assoc WHERE id_clt = '{$id_clt}'";
//                                                                                                if (mysql_query($query_remove_pensao)) {
//                                                                                                    /**
//                                                                                                     * MONTANDO QUERY DE INSERT
//                                                                                                     */
//                                                                                                    $query_pensao = "INSERT INTO favorecido_pensao_assoc (id_clt,favorecido,cpf,aliquota,id_lista_banco,agencia,conta,oficio,sobreSalLiquido,sobreSalBruto,sobreSalMinimo,quantSalMinimo,valorfixo,umTercoSobreLiquido) VALUES ";
//                                                                                                    for ($i = 0; $i < count($_REQUEST['nome_favorecido']); $i++) {
//                                                                                                        $sobreSalLiquido = ($_REQUEST['base'][$i] == 1) ? 1 : null;
//                                                                                                        $sobreSalBruto = ($_REQUEST['base'][$i] == 2) ? 1 : null;
//                                                                                                        $sobreSalMinimo = ($_REQUEST['base'][$i] == 3) ? 1 : null;
//                                                                                                        $umTercoSobreLiquido = ($_REQUEST['base'][$i] == 5) ? 1 : null;
//                                                                                                        $valorFixo = str_replace(".", "", $_REQUEST['valorfixo'][$i]);
//                                                                                                        $valorFixo = str_replace(",", ".", $valorFixo);
//                                                                                                        $query_pensao .= "('{$id_clt}','{$_REQUEST['nome_favorecido'][$i]}','{$_REQUEST['cpf_favorecido'][$i]}','{$_REQUEST['aliquota_favorecido'][$i]}','{$_REQUEST['id_lista_banco_favorecido'][$i]}','{$_REQUEST['agencia_favorecido'][$i]}','{$_REQUEST['conta_favorecido'][$i]}','{$_REQUEST['oficio'][$i]}','{$sobreSalLiquido}','{$sobreSalBruto}','{$sobreSalMinimo}','{$_REQUEST['quant'][$i]}','{$valorFixo}','{$umTercoSobreLiquido}'),";
//                                                                                                    }
//
//                                                                                                    $query_pensao = substr($query_pensao, 0, -1);
//
//                                                                                                    //echo $query_pensao;exit();
//
//                                                                                                    mysql_query($query_pensao) or die("Erro ao cadastrar favorecidos");
//                                                                                                } else {
//                                                                                                    die("Erro ao remover favorecidos");
//                                                                                                }

                                                                                                                /**
                                                                                                                 * FAVORECIDOS DE PENSÃO ALIMENTICIA
                                                                                                                 */
                                                                                                                if ($pensao_alimenticia == 0) {
                                                                                                                    // $query = mysql_query("DELETE FROM favorecido_pensao_assoc WHERE id_clt = '{$id_clt}'") or die('Erro ao remover favorecido');
                                                                                                                    $query = mysql_query("UPDATE favorecido_pensao_assoc SET status_reg = 0 WHERE id_clt = {$id_clt}");
                                                                                                                    $log->log('2', "Todos os favorecidos do CLT $id_clt foram removidos", 'favorecido_pensao_assoc');
                                                                                                                }

                                                                                                                $isento_confederativa = $_REQUEST['isento_confederativa'];
                                                                                                                $isento_associativa = $_REQUEST['isento_associativa'];
                                                                                                                $isento_assistencial = $_REQUEST['isento_assistencial'];

                                                                                                                if ($_REQUEST['radio_sindicato'] == 'nao') {
                                                                                                                    $isento_confederativa = 0;
                                                                                                                    $isento_associativa = 0;
                                                                                                                    $isento_assistencial = 0;
                                                                                                                }

                                                                                                                $ad_transferencia_tipo = $_REQUEST['ad_transferencia_tipo'];
                                                                                                                $ad_transferencia_valor = $_REQUEST['ad_transferencia_valor'];

                                                                                                                $carteira_sus = $_REQUEST['carteira_sus'];
                                                                                                                $escolha_grat = $_REQUEST['select_gratificacao'];
                                                                                                                $percent_fixo = $_REQUEST['valor_produtividade_percentual'];
//                                                                                                                $ajd_custo = $_REQUEST['valor_ajuda_custo'];
                                                                                                                $vl_cargo_conf = $_REQUEST['valor_cargo_confianca'];
                                                                                                                $vl_chefe_eq = $_REQUEST['valor_chefe_equipe'];

                                                                                                                $ajuda_custo = $_REQUEST['ajuda_custo'];

                                                                                                                $query = "UPDATE rh_clt SET  $sql_curso ad_transferencia_valor = '$ad_transferencia_valor', ad_transferencia_tipo = '$ad_transferencia_tipo', localpagamento = '$localpagamento',nome = '$nome',id_setor = '$id_setor', id_plano_saude='$id_plano_saude', sexo = '$sexo', endereco = '$endereco', tipo_endereco='$tipo_endereco', numero = '$numero', bairro = '$bairro', cidade = '$cidade', uf = '$uf', cep = '$cep', tel_fixo = '$tel_fixo', complemento = '$complemento',
                                tel_cel = '$tel_cel', tel_rec = '$tel_rec', data_nasci = $data_nasci2, naturalidade = '$municipio_nasc, $uf_nasc', nacionalidade = '$nome_nacionalidade', civil = '$estCivilNome', rg = '$rg', orgao = '$orgao', data_rg = $data_rg2, cpf = '$cpf', conselho = '$conselho', titulo = '$titulo', zona = '$zona', secao = '$secao', pai = '$pai', nacionalidade_pai = '$nacionalidade_pai', mae = '$mae', nacionalidade_mae = '$nacionalidade_mae', estuda = '$estuda', data_escola = $data_escola, escolaridade = '$escolaridade', instituicao = '$instituicao', curso = '$curso', banco = '$banco', agencia ='$agencia', agencia_dv = '{$agencia_dv}', conta = '$conta', conta_dv = '{$conta_dv}',tipo_conta = '$tipoDeConta', data_saida = $desativacao, tipo_contratacao = '$tipo_contratacao',  apolice = '$apolice', data_entrada = $data_entrada2, campo2 = '$dependente', campo1 = '$trabalho',
                                data_exame = $data_exame, reservista = '$reservista', etnia = '$etnia', deficiencia = '$deficiencia', cabelos = '$cabelos', peso = '$peso', altura = '$altura', olhos = '$olhos', defeito = '$defeito', cipa = '$cipa', ad_noturno = '$ad_noturno', plano = '$plano', assinatura = '$assinatura', distrato = '$assinatura2', outros = '$assinatura3', pis = '$pis', dada_pis = $data_pis1, data_ctps = $data_ctps, serie_ctps = '$serie_ctps', uf_ctps = '$uf_ctps', uf_rg = '$uf_rg', fgts = '$fgts', insalubridade = '$insalubridade', transporte = '$transporte', medica = '$medica', tipo_pagamento = '$tipopg', id_centro_custo = '$centrocusto', nome_banco = '$nome_banco', num_filhos = '$filhos', observacao = '$observacao', foto = '$foto_banco', dataalter = '$data_hoje', useralter = '$id_user', rh_horario = '$horario', rh_sindicato = '$sindicato', status_admi = '$tipo_de_admissao', desconto_inss = '$desconto_inss', tipo_desconto_inss = '$tipo_desconto_inss', trabalha_outra_empresa = '$trabalha_outra_empresa', salario_outra_empresa = '$salario_outra_empresa', desconto_outra_empresa = '$desconto_outra_empresa', contrato_medico = '$contrato_medico', email= '$email',
                                data_nasc_pai = $data_nasc_pai, data_nasc_mae = $data_nasc_mae, data_nasc_conjuge = $data_nasc_conjuge, nome_conjuge = '$conjuge', nome_avo_h = '$avo_h', 
                                data_nasc_avo_h = $data_nasc_avo_h, nome_avo_m = '$avo_m', data_nasc_avo_m = $data_nasc_avo_m, nome_bisavo_h = '$bisavo_h', data_nasc_bisavo_h = $data_nasc_bisavo_h,
                                nome_bisavo_m = '$bisavo_m', data_nasc_bisavo_m = $data_nasc_bisavo_m, municipio_nasc = '$municipio_nasc', uf_nasc = '$uf_nasc', data_emissao = $data_emissao, verifica_orgao = '$verifica_orgao', tipo_sanguineo = '$tipo_sanguineo', ano_contribuicao = '$ano_contribuicao', dtChegadaPais = $ano_chegada_pais, cod_pais_rais = '$cod_nacionalidade', tipo_contrato = '$tipo_contrato', prazoexp = '$prazoExp', id_estado_civil = '$estCivilId', id_municipio_nasc='$cod_muni_nasc', id_municipio_end='$cod_cidade', id_pais_nasc = '$cod_pais_nasc', id_pais_nacionalidade = '$cod_pais_nacionalidade', vale_refeicao = '$vale_refeicao', vale_alimentacao = '$vale_alimentacao', 
                                pensao_alimenticia = '$pensao_alimenticia', carteira_sus = '$carteira_sus', data_ultima_atualizacao = NOW(),seguro_desemprego = '$seguro_desemprego', imposto_renda = '$imposto_renda' , pde = '$pde', data_pde = $data_pde, valor_refeicao = '$valor_refeicao', id_banco_nacional='$id_banco_nacional',num_crt = '$num_crt' , emissao_crt = '$emissao_crt' , validade_crt = '$validade_crt', valor_hora = '$valor_hora', quantidade_horas = '$quantidade_horas', gratificacao = '$gratificacao', quantidade_plantao = '$quantidade_plantao' , valor_fixo_plantao = '$valor_fixo_plantao',
                                isento_sindical_confederativa = '$isento_confederativa', isento_sindical_assistencial = '$isento_assistencial', isento_sindical_associativa = '$isento_associativa', nao_recebe_vr = '$nao_recebe_vr',
                                valor_chefe_equipe = '$vl_chefe_eq', valor_cargo_confianca = '$vl_cargo_conf', ajuda_custo = '$ajuda_custo', produtividade_percent_fixo = '$percent_fixo',
                                unidocencia = '$unidocencia', escolha_gratificacao = '$escolha_grat' 
                    WHERE id_clt = '$id_clt' LIMIT 1";                                                                                                                //COLOCAR PARA CARREGAR OS FAVORECIDOS PARA O CLT
                                                                                                                //REMOVER FAVORECIDOS CASO A FLAG PENSAO ALIMENTÍCIA FOR DESMARCADA
                                                                                                                $antigo = $log->getLinha('rh_clt', $id_clt);
                                                                                                                mysql_query($query) or die("Erro no UPDATE:<br/>" . $query . "<br/><font color=red> " . mysql_error());
                                                                                                                $novo = $log->getLinha('rh_clt', $id_clt);

                                                                                                                $log->log('2', "CLT ID $id_clt atualizado", 'rh_clt', $antigo, $novo);
                                                                                                                // VALORES/QTD DE VT
                                                                                                                //Referente ao VALOR
                                                                                                                $vt_valor1 = $_REQUEST['vt_valor1'];
                                                                                                                $vt_valor2 = $_REQUEST['vt_valor2'];
                                                                                                                $vt_valor3 = $_REQUEST['vt_valor3'];
                                                                                                                $vt_valor4 = $_REQUEST['vt_valor4'];
                                                                                                                $vt_valor5 = $_REQUEST['vt_valor5'];
                                                                                                                //Referente a QUANTIDADE
                                                                                                                $vt_qtd1 = $_REQUEST['vt_qtd1'];
                                                                                                                $vt_qtd2 = $_REQUEST['vt_qtd2'];
                                                                                                                $vt_qtd3 = $_REQUEST['vt_qtd3'];
                                                                                                                $vt_qtd4 = $_REQUEST['vt_qtd4'];
                                                                                                                $vt_qtd5 = $_REQUEST['vt_qtd5'];
                                                                                                                //Referente a OPÇÃO
                                                                                                                $vt_linha1 = $_REQUEST['vt_linha1'];
                                                                                                                $vt_linha2 = $_REQUEST['vt_linha2'];
                                                                                                                $vt_linha3 = $_REQUEST['vt_linha3'];
                                                                                                                $vt_linha4 = $_REQUEST['vt_linha4'];
                                                                                                                $vt_linha5 = $_REQUEST['vt_linha5'];
                                                                                                                //Referente ao NÚMERO DO CARTÃO
                                                                                                                $vt_card1 = $_REQUEST['vt_card1'];
                                                                                                                $vt_card2 = $_REQUEST['vt_card2'];
                                                                                                                $vt_card3 = $_REQUEST['vt_card3'];
                                                                                                                $vt_card4 = $_REQUEST['vt_card4'];
                                                                                                                $vt_card5 = $_REQUEST['vt_card5'];

                                                                                                                // VALE TRANSPORTE
                                                                                                                $result_cont_vale = mysql_query("SELECT * FROM rh_vt_valores_assoc WHERE id_clt = '$id_clt' AND status_reg = 1");
                                                                                                                $array_cont_vale = mysql_fetch_array($result_cont_vale);
                                                                                                                $row_cont_vale = mysql_num_rows($result_cont_vale);

                                                                                                                if (empty($row_cont_vale)) {
                                                                                                                    mysql_query("INSERT INTO rh_vt_valores_assoc(id_clt, id_valor1, id_valor2, id_valor3, id_valor4, id_valor5, qtd1, qtd2, qtd3, qtd4, qtd5,
                                                                                                                                id_linha1, id_linha2, id_linha3, id_linha4, id_linha5, cartao1, cartao2, cartao3, cartao4, cartao5, status_reg) VALUES
                                                                                                                                ('$id_clt','$vt_valor1','$vt_valor2','$vt_valor3', '$vt_valor4', '$vt_valor5', '$vt_qtd1','$vt_qtd2','$vt_qtd3', '$vt_qtd4', '$vt_qtd5',
                                                                                                                                 '$vt_linha1', '$vt_linha2', '$vt_linha3', '$vt_linha4', '$vt_linha5', '$vt_card1', '$vt_card2', '$vt_card3', '$vt_card4', '$vt_card5', 1)") or die("Erro de digitação no INSERT dos vales query: " . mysql_error());
                                                                                                                    $vtId = mysql_insert_id();
                                                                                                                    $log->log('2', "VT ID $vtId inserido para o CLT $id_clt", 'rh_vt_valores_assoc');
                                                                                                                } else {

                                                                                                                    if (($vt_valor1 == '0' && $vt_linha1 == '0') or $transporte == '0') {
                                                                                                                        $status_reg = '0';
                                                                                                                    } else {
                                                                                                                        $status_reg = '1';
                                                                                                                    }
                                                                                                                    $antigo = $log->getLinha('rh_vt_valores_assoc', $array_cont_vale['id_valor_assoc']);
                                                                                                                    mysql_query("UPDATE rh_vt_valores_assoc SET id_clt = '$id_clt', id_valor1 = '$vt_valor1', id_valor2 = '$vt_valor2', id_valor3 = '$vt_valor3', id_valor4 = '$vt_valor4', id_valor5 = '$vt_valor5', 
                                                                                                                                            qtd1 = '$vt_qtd1', qtd2 = '$vt_qtd2', qtd3 = '$vt_qtd3', qtd4 = '$vt_qtd4', qtd5 = '$vt_qtd5', 
                                                                                                                                            id_linha1 = '$vt_linha1', id_linha2 = '$vt_linha2', id_linha3 = '$vt_linha3', id_linha4 = '$vt_linha4', id_linha5 = '$vt_linha5',
                                                                                                                                            cartao1 = '$vt_card1', cartao2 = '$vt_card2', cartao3 = '$vt_card3', cartao4 = '$vt_card4', cartao5 = '$vt_card5',
                                                                                                                                            status_reg = '$status_reg' WHERE id_clt = '$id_clt' AND id_valor_assoc = {$array_cont_vale['id_valor_assoc']}") or die("Erro de digitação no UPDATE dos vales query: " . mysql_error());
                                                                                                                    $novo = $log->getLinha('rh_vt_valores_assoc', $array_cont_vale['id_valor_assoc']);

                                                                                                                    $log->log('2', "VT ID {$array_cont_vale['id_valor_assoc']} atualiado. CLT $id_clt", 'rh_vt_valores_assoc', $antigo, $novo);
                                                                                                                }
//
                                                                                                                // DEPENDENTES
                                                                                                                if (!empty($row['id_antigo'])) {
                                                                                                                    $referencia = $row['id_antigo'];
                                                                                                                } else {
                                                                                                                    $referencia = $row['id_clt'];
                                                                                                                }


                                                                                                                if ($_COOKIE['debug'] == 666) {
                                                                                                                    echo '/////////////////////////VERIFICAÇÃO DEPENDENTES//////////////////////////';
                                                                                                                    print_array("SELECT id_bolsista FROM dependentes WHERE   id_bolsista = '$referencia' AND id_projeto = '$id_projeto' AND contratacao = '$tipo_contratacao'");
                                                                                                                }
                                                                                                                $result_cont1 = mysql_query("SELECT id_bolsista FROM dependentes WHERE   id_bolsista = '$referencia' AND id_projeto = '$id_projeto' AND contratacao = '$tipo_contratacao'");
                                                                                                                $row_cont1 = mysql_num_rows($result_cont1);
                                                                                                                if ($row_cont1 == 0) {

                                                                                                                    mysql_query("INSERT INTO dependentes (id_regiao, id_projeto, id_bolsista, contratacao, nome, data1, nome1, cpf1, data2, nome2, cpf2, data3, nome3, cpf3, data4, nome4, cpf4, data5, nome5, cpf5, data6, nome6, cpf6, ddir_pai, ddir_mae, ddir_conjuge, portador_def1,portador_def2, portador_def3, portador_def4, portador_def5, portador_def6, dep1_cur_fac_ou_tec, dep2_cur_fac_ou_tec, dep3_cur_fac_ou_tec, dep4_cur_fac_ou_tec, dep5_cur_fac_ou_tec, dep6_cur_fac_ou_tec,ddir_avo_h, ddir_avo_m, ddir_bisavo_h, ddir_bisavo_m, nao_ir_filho1, nao_ir_filho2, nao_ir_filho3, nao_ir_filho4, nao_ir_filho5, nao_ir_filho6, possui_guarda1, possui_guarda2, possui_guarda3, possui_guarda4, possui_guarda5, possui_guarda6)
        VALUES
        ('$regiao', '$id_projeto', '$referencia', '2', '$nome', $data_filho_1, '$filho_1', '$cpf_1', $data_filho_2, '$filho_2', '$cpf_2', $data_filho_3, '$filho_3', '$cpf_3', $data_filho_4, '$filho_4', '$cpf_4', $data_filho_5, '$filho_5', '$cpf_5', $data_filho_6, '$filho_6', '$cpf_6', '$ddir_pai', '$ddir_mae', '$ddir_conjuge', '$portador1', '$portador2', '$portador3', '$portador4', '$portador5', '$portador6', '$dep1_cur_fac_ou_tec', '$dep2_cur_fac_ou_tec', '$dep3_cur_fac_ou_tec', '$dep4_cur_fac_ou_tec', '$dep5_cur_fac_ou_tec', '$dep6_cur_fac_ou_tec', '$ddir_avo_h', '$ddir_avo_m', '$ddir_bisavo_h', '$ddir_bisavo_m', $ddir1, $ddir2, $ddir3, $ddir4, $ddir5, $ddir6, $guarda_filho1, $guarda_filho2, $guarda_filho3, $guarda_filho4, $guarda_filho5, $guarda_filho6)") or die("Insert de Dependentes: " . mysql_error() . "<!-- INSERT INTO dependentes (id_regiao, id_projeto, id_bolsista, contratacao, nome, data1, nome1, cpf1, data2, nome2, cpf2, data3, nome3, cpf3, data4, nome4, cpf4, data5, nome5, cpf5, data6, nome6, cpf6, ddir_pai, ddir_mae, ddir_conjuge, portador_def1,portador_def2, portador_def3, portador_def4, portador_def5, portador_def6, dep1_cur_fac_ou_tec, dep2_cur_fac_ou_tec, dep3_cur_fac_ou_tec, dep4_cur_fac_ou_tec, dep5_cur_fac_ou_tec, dep6_cur_fac_ou_tec,ddir_avo_h, ddir_avo_m, ddir_bisavo_h, ddir_bisavo_m, nao_ir_filho1, nao_ir_filho2, nao_ir_filho3, nao_ir_filho4, nao_ir_filho5, nao_ir_filho6)
        VALUES
        ('$regiao', '$id_projeto', '$referencia', '2', '$nome', $data_filho_1, '$filho_1', '$cpf_1', $data_filho_2, '$filho_2', '$cpf_2', $data_filho_3, '$filho_3', '$cpf_3', $data_filho_4, '$filho_4', '$cpf_4', $data_filho_5, '$filho_5', '$cpf_5', $data_filho_6, '$filho_6', '$cpf_6', '$ddir_pai', '$ddir_mae', '$ddir_conjuge', '$portador1', '$portador2', '$portador3', '$portador4', '$portador5', '$portador6', '$dep1_cur_fac_ou_tec', '$dep2_cur_fac_ou_tec', '$dep3_cur_fac_ou_tec', '$dep4_cur_fac_ou_tec', '$dep5_cur_fac_ou_tec', '$dep6_cur_fac_ou_tec', '$ddir_avo_h', '$ddir_avo_m', '$ddir_bisavo_h', '$ddir_bisavo_m', $ddir1, $ddir2, $ddir3, $ddir4, $ddir5, $ddir6, $guarda_filho1, $guarda_filho2, $guarda_filho3, $guarda_filho4, $guarda_filho5, $guarda_filho6) -->");
                                                                                                                    $idDep = mysql_insert_id();
                                                                                                                    $log->log('2', "Dependente ID $idDep inserido para o CLT $id_clt", 'dependentes');
                                                                                                                } else {
                                                                                                                    $sqlSelDependente = "SELECT id_dependentes FROM dependentes WHERE id_projeto = '$id_projeto' AND id_bolsista = '$referencia' AND contratacao = '$tipo_contratacao' LIMIT 1";
                                                                                                                    $querySelDependente = mysql_query($sqlSelDependente);
                                                                                                                    $rowDependentes = mysql_fetch_assoc($querySelDependente);
                                                                                                                    $idDependentes = $rowDependentes['id_dependentes'];

                                                                                                                    $query = "UPDATE dependentes SET contratacao = '2', data1 =$data_filho_1, nome1 = '$filho_1', cpf1 = '$cpf_1', data2 = $data_filho_2, nome2 = '$filho_2', cpf2 = '$cpf_2', data3 = $data_filho_3, nome3 = '$filho_3', cpf3 = '$cpf_3', data4 = $data_filho_4, nome4 = '$filho_4', cpf4 = '$cpf_4', data5 = $data_filho_5, nome5 = '$filho_5', cpf5 = '$cpf_5', data6 = $data_filho_6, nome6 = '$filho_6', cpf6 = '$cpf_6', ddir_pai = '$ddir_pai', ddir_mae = '$ddir_mae', ddir_conjuge = '$ddir_conjuge', portador_def1 = '$portador1',portador_def2 = '$portador2', portador_def3 = '$portador3', portador_def4 = '$portador4', portador_def5 = '$portador5', portador_def6 = '$portador6', dep1_cur_fac_ou_tec = '$dep1_cur_fac_ou_tec', dep2_cur_fac_ou_tec = '$dep2_cur_fac_ou_tec', dep3_cur_fac_ou_tec = '$dep3_cur_fac_ou_tec', dep4_cur_fac_ou_tec = '$dep4_cur_fac_ou_tec', dep5_cur_fac_ou_tec = '$dep5_cur_fac_ou_tec', dep6_cur_fac_ou_tec = '$dep6_cur_fac_ou_tec', ddir_avo_h = '$ddir_avo_h', ddir_avo_m = '$ddir_avo_m', ddir_bisavo_h = '$ddir_bisavo_h', ddir_bisavo_m = '$ddir_bisavo_m', nao_ir_filho1 = $ddir1, nao_ir_filho2 = $ddir2, nao_ir_filho3 = $ddir3, nao_ir_filho4 = $ddir4, nao_ir_filho5 = $ddir5, nao_ir_filho6 = $ddir6, possui_guarda1 = $guarda_filho1, possui_guarda2 = $guarda_filho2, possui_guarda3 = $guarda_filho3, possui_guarda4 = $guarda_filho4, possui_guarda5 = $guarda_filho5, possui_guarda6 = $guarda_filho6
                                                                                                                                            WHERE id_dependentes = $idDependentes LIMIT 1 ";

                                                                                                                    $antigo = $log->getLinha('dependentes', $idDependentes);
                                                                                                                    mysql_query($query) or die("Update de Dependentes: " . mysql_error());
                                                                                                                    $novo = $log->getLinha('dependentes', $idDependentes);
                                                                                                                    $log->log('2', "Dependente ID $idDependentes atualizado para o CLT $id_clt", 'dependentes', $antigo, $novo);
                                                                                                                }
//
// FOTO
                                                                                                                $arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;
                                                                                                                if ($foto_up == "1") {
                                                                                                                    if (!$arquivo) {
                                                                                                                        $mensagem = "Não acesse esse arquivo diretamente!";
                                                                                                                    } else {
                                                                                                                        $tipo_arquivo = ".gif";
                                                                                                                        $diretorio = "../fotosclt/";
                                                                                                                        $nome_tmp = $regiao . "_" . $id_projeto . "_" . $id_clt . $tipo_arquivo;
                                                                                                                        $nome_arquivo = "$diretorio$nome_tmp";
                                                                                                                        move_uploaded_file($arquivo['tmp_name'], $nome_arquivo) or die("Erro ao enviar o Arquivo: $nome_arquivo");
                                                                                                                    }
                                                                                                                }


                                                                                                                /**
                                                                                                                 * ALTERANDO VINCULO DO CLT COM FAVORECIDO DE PENSÃO ALIMENTICIA
                                                                                                                 */
                                                                                                                $ver_favorecido = mysql_query("SELECT * FROM entradaesaida_nomes WHERE id_clt = '{$id_clt}'");
                                                                                                                $rows = mysql_num_rows($ver_favorecido);
                                                                                                                if ($rows > 0) {
                                                                                                                    while ($rowEnt = mysql_fetch_assoc($ver_favorecido)) {
                                                                                                                        $antigo = $log->getLinha('entradaesaida_nomes', $rowEnt['id_nome']);
                                                                                                                        $upFavorecido = mysql_query("UPDATE entradaesaida_nomes SET 
                                            nome = '{$_REQUEST['nome_favorecido']}', 
                                            cpfcnpj = '{$_REQUEST['cpf_favorecido']}' 
                                            WHERE id_nome = '{$rowEnt['id_nome']}'");
                                                                                                                        $novo = $log->getLinha('entradaesaida_nomes', $rowEnt['id_nome']);

                                                                                                                        $log->log('2', "Vinculo do CLT $id_clt com favorecido de pensão alimentícia ID {$rowEnt['id_nome']} atualizado", 'entradaesaida_nomes', $antigo, $novo);
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    $insFavorecido = mysql_query("INSERT INTO entradaesaida_nomes (id_projeto, id_clt, nome,cpfcnpj,id_entradasaida) VALUES ('$id_clt', '$id_projeto', '{$_REQUEST['nome_favorecido']}','{$_REQUEST['cpf_favorecido']}','154')");

                                                                                                                    $idEntSai = mysql_insert_id();
                                                                                                                    $log->log('2', "Novo vinculo Id $idEntSai inserido para o CLT $id_clt", 'entradaesaida_nomes');
                                                                                                                }

                                                                                                                // INSS de outras Empresas -------------------------------------------------
                                                                                                                foreach ($_REQUEST['salario_outra_empresa'] as $key => $sal_outra_empresa) {
                                                                                                                    $sal_outra_empresa = str_replace(',', '.', str_replace('.', '', $sal_outra_empresa));
                                                                                                                    $desconto_inss = str_replace(',', '.', str_replace('.', '', $_REQUEST['desconto_outra_empresa'][$key]));
                                                                                                                    $inicio_inss = converteData($_REQUEST['inicio_inss'][$key]);
                                                                                                                    $fim_inss = converteData($_REQUEST['fim_inss'][$key]);
                                                                                                                    if ($_REQUEST['id_inss'][$key] > 0) {
                                                                                                                        $antigo = $log->getLinha('rh_inss_outras_empresas', $_REQUEST['id_inss'][$key]);
                                                                                                                        $query = "UPDATE rh_inss_outras_empresas SET salario = '$sal_outra_empresa',desconto = '$desconto_inss',inicio = '$inicio_inss',fim = '$fim_inss', cnpj_outro_vinculo = '$cnpj' WHERE id_inss = {$_REQUEST['id_inss'][$key]}";
                                                                                                                        mysql_query($query);
                                                                                                                        $novo = $log->getLinha('rh_inss_outras_empresas', $_REQUEST['id_inss'][$key]);

                                                                                                                        $log->log('2', "INSS outra empresa ID {$_REQUEST['id_inss'][$key]} atualizado para o CLT ID $id_clt", 'rh_inss_outras_empresas');
                                                                                                                    } else {
                                                                                                                        $query = "INSERT INTO rh_inss_outras_empresas (id_clt,salario,desconto,inicio,fim,status,data_cad,cnpj_outro_vinculo, user_cad) VALUES ('$id_clt','$sal_outra_empresa','{$desconto_inss}','{$inicio_inss}','{$fim_inss}',1,NOW(),'$cnpj', {$_COOKIE['logado']});";
                                                                                                                        mysql_query($query);
                                                                                                                        $inssId = mysql_insert_id();
                                                                                                                        $log->log('2', "INSS outra empresa ID $inssId cadastrado para o CLT ID $id_clt", 'rh_inss_outras_empresas');
                                                                                                                    }
                                                                                                                }
                                                                                                                // INSS de outras Empresas -------------------------------------------------
                                                                                                                // salvando unidades ---------------------------------------------------
                                                                                                                foreach ($_REQUEST['unidades_assoc'] as $key => $id_unidade) {
                                                                                                                    if ($id_unidade > 0 && $id_unidade != '') {
                                                                                                                        $porcentagem = str_replace('%', '', $_REQUEST['unidades_porcentagem'][$key]);
                                                                                                                        if (empty($_REQUEST['id_assoc_unidade'][$key])) {
                                                                                                                            $query_uni = "INSERT INTO rh_clt_unidades_assoc (id_clt,id_unidade,porcentagem) VALUES('$id_clt','$id_unidade','{$porcentagem}')";
                                                                                                                            mysql_query($query_uni);
                                                                                                                            $unidadeId = mysql_insert_id();
                                                                                                                            $log->log('2', "Unidade assoc ID $unidadeId cadastrada para o CLT ID $id_clt", 'rh_clt_unidades_assoc');
                                                                                                                        } else {
                                                                                                                            $antigo = $log->getLinha('rh_clt_unidades_assoc', $_REQUEST['id_assoc_unidade'][$key]);
                                                                                                                            $query_uni = "UPDATE rh_clt_unidades_assoc SET id_unidade = '$id_unidade', porcentagem = '{$porcentagem}' WHERE id_assoc = '{$_REQUEST['id_assoc_unidade'][$key]}' AND id_clt = '$id_clt'";
                                                                                                                            mysql_query($query_uni);
                                                                                                                            $novo = $log->getLinha('rh_clt_unidades_assoc', $_REQUEST['id_assoc_unidade'][$key]);

                                                                                                                            $log->log('2', "Unidade assoc ID {$_REQUEST['id_assoc_unidade'][$key]} atualizada para o CLT ID $id_clt", 'rh_clt_unidades_assoc', $antigo, $novo);
                                                                                                                        }
                                                                                                                    } else if ($id_unidade != '') {
                                                                                                                        $antigo = $log->getLinha('rh_clt_unidades_assoc', $_REQUEST['id_assoc_unidade'][$key]);
                                                                                                                        $query_uni = "UPDATE rh_clt_unidades_assoc SET status = 0 WHERE id_assoc = '{$_REQUEST['id_assoc_unidade'][$key]}' AND id_clt = '$id_clt'";
                                                                                                                        mysql_query($query_uni);
                                                                                                                        $novo = $log->getLinha('rh_clt_unidades_assoc', $_REQUEST['id_assoc_unidade'][$key]);

                                                                                                                        $log->log('2', "Unidade assoc ID {$_REQUEST['id_assoc_unidade'][$key]} atualizada para o CLT ID $id_clt", 'rh_clt_unidades_assoc', $antigo, $novo);
                                                                                                                    }
                                                                                                                }
//        exit();
                                                                                                                // salvando unidades ---------------------------------------------------
//
// REDIRECIONAMENTO
                                                                                                                $pagina = $_REQUEST['pagina'];
                                                                                                                if ($pagina == "clt") {
                                                                                                                    header("Location: clt.php?regiao=$regiao&sucesso=edicao");
                                                                                                                    exit;
                                                                                                                } else {
                                                                                                                    header("Location: /intranet/rh_novaintra/bolsista.php?projeto=$id_projeto&regiao=$regiao&sucesso=edicao");
                                                                                                                    exit;
                                                                                                                }
//
                                                                                                            } // FIM DA VERIFICAÇÃO
                                                                                                        } // FIM DO UPDATE
                                                                                                        ?>



