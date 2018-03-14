<?php

// Log - Edi√ß√£o de CLT
$qr_colunas = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$_POST[id_clt]'");
$coluna = mysql_fetch_assoc($qr_colunas);

$qr_dependentes = mysql_query("SELECT * FROM dependentes WHERE id_bolsista = '$_POST[id_clt]' AND contratacao = $coluna[tipo_contratacao]");
$dependentes = mysql_fetch_assoc($qr_dependentes);

echo "SELECT * FROM rh_vale WHERE id_clt = '{$_POST['id_clt']}'";

$qr_vales = mysql_query("SELECT * FROM rh_vale WHERE id_clt = '{$_POST['id_clt']}'");
$vale = mysql_fetch_assoc($qr_vales);

function formata($post_data) {
    $formatado = implode("-", array_reverse(explode("/", $post_data)));
    return $formatado;
}

function formata2($post_vazio) {
    if (empty($post_vazio)) {
        $formatado = "0";
    } else {
        $formatado = $post_vazio;
    }
    return $formatado;
}

function formata3($post_vazio) {
    if ($post_vazio == '0') {
        $formatado = NULL;
    } else {
        $formatado = $post_vazio;
    }
    return $formatado;
}

function formata4($civil) {
    $return = explode('|', $civil);
    return $return[1];
}

$colunas = array(
    $coluna['id_clt'], $coluna['nome'], $coluna['data_nasci'], $coluna['uf_nasc'], $coluna['municipio_nasc'], $coluna['civil'], $coluna['sexo'], $coluna['cod_pais_rais'],
    $coluna['cep'], $coluna['endereco'], $coluna['numero'], $coluna['complemento'], $coluna['bairro'], $coluna['uf'], $coluna['cidade'], $coluna['estuda'],
    $coluna['data_escola'], $coluna['escolaridade'], $coluna['curso'], $coluna['instituicao'], $coluna['tel_fixo'], $coluna['tel_cel'], $coluna['tel_rec'], $coluna['email'],
    $coluna['tipo_sanguineo'], $coluna['pai'], $coluna['mae'], $coluna['nome_conjuge'], $coluna['nome_avo_h'], $coluna['nome_avo_m'], $coluna['nome_bisavo_h'], $coluna['nome_bisavo_m'],
    $coluna['data_nasc_pai'], $coluna['data_nasc_mae'], $coluna['data_nasc_conjuge'], $coluna['data_nasc_avo_h'], $coluna['data_nasc_avo_m'], $coluna['data_nasc_bisavo_h'], $coluna['data_nasc_bisavo_m'], $dependentes['ddir_pai'],
    $dependentes['ddir_mae'], $dependentes['ddir_conjuge'], $dependentes['ddir_avo_h'], $dependentes['ddir_avo_m'], $dependentes['ddir_bisavo_h'], $dependentes['ddir_bisavo_m'], $coluna['nacionalidade_pai'], $coluna['nacionalidade_mae'],
    $coluna['num_filhos'], $dependentes['nome1'], $dependentes['nome2'], $dependentes['nome3'], $dependentes['nome4'], $dependentes['nome5'], $dependentes['data1'], $dependentes['data2'],
    $dependentes['data3'], $dependentes['data4'], $dependentes['data5'], $dependentes['portador_def1'], $dependentes['portador_def2'], $dependentes['portador_def3'], $dependentes['portador_def4'], $dependentes['portador_def5'],
    $coluna['cabelos'], $coluna['olhos'], $coluna['peso'], $coluna['altura'], $coluna['etinia'], $coluna['defeito'], $coluna['deficiencia'], $coluna['rg'],
    $coluna['orgao'], $coluna['uf_rg'], $coluna['data_rg'], $coluna['cpf'], $coluna['conselho'], $coluna['data_emissao'], $coluna['campo1'], $coluna['serie_ctps'],
    $coluna['uf_ctps'], $coluna['data_ctps'], $coluna['titulo'], $coluna['zona'], $coluna['secao'], $coluna['pis'], $coluna['dada_pis'], $coluna['fgts'],
    $coluna['reservista'], $coluna['medica'], $coluna['plano'], $coluna['apolice'], $coluna['campo2'], $coluna['insalubridade'], $coluna['ad_noturno'],
    $coluna['desconto_inss'], $coluna['cipa'], $coluna['transporte'], $vale['id_tarifa1'], $vale['id_tarifa2'], $vale['id_tarifa3'], $vale['id_tarifa4'], $vale['id_tarifa5'],
    $vale['id_tarifa6'], $vale['cartao1'], $vale['cartao2'], $coluna['rh_sindicato'], $coluna['ano_contribuicao'], $coluna['banco'], $coluna['agencia'], $coluna['tipo_conta'],
    $coluna['data_entrada'], $coluna['data_exame'], $coluna['localpagamento'], $coluna['status_admi'], $coluna['tipo_pagamento'], $coluna['prazoexp'], $coluna['tipo_contrato'], $coluna['observacao'],
);

$posts = array(
    $_POST['id_clt'], $_POST['nome'], formata($_POST['data_nasc']), $_POST['uf_nasc_select'], $_POST['municipio_nasc'], formata4($_POST['civil']), $_POST['sexo'], $_POST['nacionalidade'],
    $_POST['cep'], $_POST['endereco'], $_POST['numero'], $_POST['complemento'], $_POST['bairro'], $_POST['uf'], $_POST['cidade'], $_POST['estuda'],
    formata($_POST['data_escola']), $_POST['escolaridade'], $_POST['curso'], $_POST['instituicao'], $_POST['tel_fixo'], $_POST['tel_cel'], $_POST['tel_rec'], $_POST['email'],
    $_POST['tiposanguineo'], $_POST['pai'], $_POST['mae'], $_POST['conjuge'], $_POST['avo_h'], $_POST['avo_m'], $_POST['bisavo_h'], $_POST['bisavo_m'],
    formata($_POST['data_nasc_pai']), formata($_POST['data_nasc_mae']), formata($_POST['data_nasc_conjuge']), formata($_POST['data_nasc_avo_h']), formata($_POST['data_nasc_avo_m']), formata($_POST['data_nasc_bisavo_h']), formata($_POST['data_nasc_bisavo_m']), formata2($_POST['ddir_pai']),
    formata2($_POST['ddir_mae']), formata2($_POST['ddir_conjuge']), formata2($_POST['ddir_avo_h']), formata2($_POST['ddir_avo_m']), formata2($_POST['ddir_bisavo_h']), formata2($_POST['ddir_avo_m']), $_POST['nacionalidade_pai'], $_POST['nacionalidade_mae'],
    $_POST['filhos'], $_POST['filho_1'], $_POST['filho_2'], $_POST['filho_3'], $_POST['filho_4'], $_POST['filho_5'], formata($_POST['data_filho_1']), formata($_POST['data_filho_2']),
    formata($_POST['data_filho_3']), formata($_POST['data_filho_4']), formata($_POST['data_filho_5']), formata2($_POST['portador1']), formata2($_POST['portador2']), formata2($_POST['portador3']), formata2($_POST['portador4']), formata2($_POST['portador5']),
    $_POST['cabelos'], $_POST['olhos'], $_POST['peso'], $_POST['altura'], $_POST['etinia'], $_POST['defeito'], $_POST['deficiencia'], $_POST['rg'],
    $_POST['orgao'], $_POST['uf_rg'], formata($_POST['data_rg']), $_POST['cpf'], $_POST['conselho'], formata($_POST['data_emissao']), $_POST['trabalho'], $_POST['serie_ctps'],
    $_POST['uf_ctps'], formata($_POST['data_ctps']), $_POST['titulo'], $_POST['zona'], $_POST['secao'], $_POST['pis'], formata($_POST['data_pis']), $_POST['fgts'],
    $_POST['reservista'], $_POST['medica'], $_POST['plano_medico'], $_POST['apolice'], $_POST['dependente'], formata2($_POST['insalubridade']), $_POST['ad_noturno'],
    formata2($_POST['desconto_inss']), $_POST['cipa'], formata2($_POST['transporte']), formata2($_POST['vale1']), formata2($_POST['vale2']), formata2($_POST['vale3']), formata2($_POST['vale4']), formata2($_POST['vale5']),
    formata2($_POST['vale6']), $_POST['num_cartao'], $_POST['num_cartao2'], formata3($_POST['sindicato']), formata2($_POST['ano_contribuicao']), $_POST['banco'], $_POST['agencia'], $_POST['radio_tipo_conta'],
    formata($_POST['data_entrada']), formata($_POST['data_exame']), $_POST['localpagamento'], $_POST['tipo_admissao'], $_POST['tipopg'], $_POST['prazoExp'], $_POST['tipo_contrato'], $_POST['observacoes']
);



$campos = array(
    "o cÛdigo", "o nome", "a data de nascimento", "a UF de nascimento", "o municÌpio de nascimento", "o estado civil", "o sexo", "a nacionalidade",
    "o cep", "o endereco", "o n˙mero da residÍncia", "o complemento da residÍncia", "o bairro da residÍncia", "a UF da residÍncia", "a cidade da residÍncia", "o campo estuda atualmente",
    "a data de termono dos estudos", "a escolaridade", "o curso", "a instituiÁ„o de ensino", "o telefone fixo", "o telefone celular", "o telefone de recado", "o e-mail",
    "o tipo sanguineo", "o nome do pai", "o nome da m„e", "o nome do conjuge", "o nome do avÙ", "o nome da avÙ", "o nome do bisavÙ", "o nome da bisavÛ",
    "a data de nascimento do pai", "a data de nascimento da m„e", "a data de nascimento do conjuge", "a data de nascimento do avÙ", "a data de nascimento da avÛ", "a data de nascimento do bisavÙ", "a data de nascimento da bisavÛ", "se pai È dependente de IRRF",
    "se m„e È dependente de IRRF", "se conjuge È dependente de IRRF", "se avÙ È dependente de IRRF", "se avÛ È dependente de IRRF", "se bisavÙ È dependente de IRRF", "se bisavÛ È depende de IRRF", "a nacionalidade do pai", "a nacionalidade da m„e",
    "a quantidade de filhos", "o nome do primeiro filho", "o nome do segundo filho", "o nome do terceiro filho", "o nome do quarto filho", "o nome do quinto filho", "a data de nascimento do primeiro filho", "a data de nascimento do segundo filho",
    "a data de nascimento do terceiro filho", "a data de nascimento do quarto filho", "a data de nascimento do quinto filho", "se primeiro filho È portador de necessidades", "se segundo filho È portador de necessidades", "se terceiro filho È portador de necessidades", "se quarto filho È portador de necessidades", "se quinto filho È portador de necessidades",
    "a cor do cabelo", "a cor dos olhos", "o peso", "a altura", "a etinia", "os defeitos", "a deficiÍncia", "o RG", "o org„o do RG", "a UF do RG", "a data do RG", "o CPF", "o Conselho/Org„o Regulador", "a data de emiss„o", "a CTPS", "a sÈrie da CTPS",
    "a UF da CTPS", "a data da CTPS", "o titulo de eleitor", "a zona eleitoral", "a seÁ„o eleitoral", "o pis", "a data do pis", "o fgts",
    "o certificado de reservista", "se tem assistÍncia mÈdica", "o plano mÈdico", "a apÛlice", "o dependente", "se recebe insalubridade", "se recebe ad. noturno",
    "se possui desconto de INSS", "se È integrante do CIPA", "se recebe vale transporte", "o vale 1", "o vale 2", "o vale 3", "o vale 4", "o vale 5",
    "o vale 6", "o n˙m. do cart„o", "o n˙m. do cart„o 2", "o sindicato", "o ano de contribuiÁ„o", "o banco", "a agÍncia", "o tipo de conta",
    "a data de entrada", "a data do exame", "o local de pagamento", "o tipo de admiss„o", "o tipo de pagamento", "o prazo de experiÍncia", "o tipo de contrato", "as observaÁıes"
);

$n = 0;
$edicao = "";

for ($a = 0; $a < 119; $a++) {
//    $teste .= "{$campos[$a]} - BD: {$colunas[$a]} - POST: {$posts[$a]}<br>"; // teste teste teste teste teste teste teste teste teste teste
    if (($colunas[$a] != $posts[$a]) and ( empty($posts[$a]))) {
        $n++;
        $edicao .= " <b>$n)</b> removeu <b>$campos[$a] ($colunas[$a])</b>";
    } elseif (($colunas[$a] != $posts[$a]) and ( empty($colunas[$a]))) {
        $n++;
        $edicao .= " <b>$n)</b> inseriu <b>$campos[$a] ($posts[$a])</b>";
    } elseif ($colunas[$a] != $posts[$a]) {
        $n++;
        $edicao .= " <b>$n)</b> editou <b>$campos[$a]</b> de <b>$colunas[$a]</b> para <b>$posts[$a]</b>";
    }
}

//if ($_COOKIE['logado'] == '256') { // teste teste teste teste teste teste teste teste teste
//    exit($teste);
//}

$qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$funcionario = mysql_fetch_array($qr_funcionario);
$ip = $_SERVER['REMOTE_ADDR'];
$data = date("d/m/Y H:i");
$cabecalho = "($funcionario[0]) $funcionario[nome] √†s " . $data . "h (ip: $ip)";
$local = "Edi√ß√£o de CLT - ($coluna[campo3]) $coluna[nome]";
$local_banco = "Edi√ß√£o de CLT";
$acao_banco = "Editou o CLT ($coluna[campo3]) $coluna[nome]";

mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) 
VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local_banco', NOW(), '$ip', '$acao_banco')") or die("Erro Inesperado<br><br>" . mysql_error());

$nome_arquivo = "../log/" . $funcionario[0] . ".txt";

$arquivo = fopen("$nome_arquivo", "a");
fwrite($arquivo, "$cabecalho");
fwrite($arquivo, "\r\n");
fwrite($arquivo, "$local");
fwrite($arquivo, "\r\n");
fwrite($arquivo, "$edicao");
fwrite($arquivo, "\r\n");
fwrite($arquivo, "\r\n");
fwrite($arquivo, "---------------------------------------------------------------");
fwrite($arquivo, "\r\n");
fwrite($arquivo, "\r\n");
fclose($arquivo);
// Fim do Log
// Log de Sefip (Altera√ß√£o de Cadastro)
$colunas_sefip = array($coluna['campo3'], $coluna['nome'], $coluna['data_nasci'], $coluna['campo1'],
    $coluna['serie_ctps'], $coluna['pis'], $coluna['data_entrada']);

$posts_sefip = array($_POST['codigo'], $_POST['nome'], formata($_POST['data_nasc']), $_POST['trabalho'],
    $_POST['serie_ctps'], $_POST['pis'], formata($_POST['data_entrada'], $_POST['endereco'], $_POST['bairro'], $_POST['cep'], $_POST['cidade'], $_POST['estado']));

$codigos_sefip = array('406', '404', '428', '403', '403', '405', '408');

$ano_sefip = date('Y');
$mes_sefip = date('m');

for ($b = 0; $b <= 8; $b++) {
    if ($colunas_sefip[$b] != $posts_sefip[$b]) {
        mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, sefip, sefip_ano, sefip_mes, sefip_id, sefip_tipo, sefip_codigo, sefip_valor) VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local_banco', NOW(), '$ip', '1', '$ano_sefip', '$mes_sefip', '$_REQUEST[id_clt]', '2', '$codigos_sefip[$b]', '$posts_sefip[$b]')") or die("Erro Inesperado<br><br>" . mysql_error());
    }
}
// Fim do Log
// Log de Sefip (Altera√ß√£o de Endere√ßo)
$colunas_endereco_sefip = array($coluna['endereco'], $coluna['bairro'], $coluna['cep'], $coluna['cidade'], $coluna['estado']);

$posts_endereco_sefip = array($_POST['endereco'], $_POST['bairro'], $_POST['cep'], $_POST['cidade'], $_POST['estado']);

$alteracao_endereco = NULL;

for ($c = 0; $c <= 5; $c++) {
    if ($colunas_endereco_sefip[$c] != $posts_endereco_sefip[$c]) {
        $alteracao_endereco = 1;
    }
}

if ($alteracao_endereco == 1) {
    mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, sefip, sefip_ano, sefip_mes, sefip_id, sefip_tipo) VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local_banco', NOW(), '$ip', '1', '$ano_sefip', '$mes_sefip', '$_REQUEST[id_clt]', '2')") or die("Erro Inesperado<br><br>" . mysql_error());
}
// Fim do Log
?>