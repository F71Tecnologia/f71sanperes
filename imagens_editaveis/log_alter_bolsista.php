<?php
// Log - Edição de Bolsista
$qr_colunas = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$_POST[id_bolsista]'");
$coluna = mysql_fetch_assoc($qr_colunas);

$qr_dependentes = mysql_query("SELECT * FROM dependentes WHERE id_bolsista = '$_POST[id_bolsista]'");
$dependentes = mysql_fetch_assoc($qr_dependentes);

function formata($post_data) {
	  $formatado = implode("-", array_reverse(explode("/", $post_data)));
	  return $formatado;
}

function formata2($post_vazio) {
	  if(empty($post_vazio)) {
		  $formatado = "0";
	  } else {
		  $formatado = $post_vazio;
	  }
	  return $formatado;
}

$colunas = array($coluna['campo3'], $coluna['tipo_contratacao'], $coluna['id_curso'], $coluna['locacao'], $coluna['nome'], $coluna['endereco'], $coluna['bairro'], $coluna['cidade'], $coluna['uf'], $coluna['cep'], $coluna['tel_fixo'], $coluna['tel_cel'], $coluna['tel_rec'], $coluna['data_nasci'], $coluna['naturalidade'], $coluna['nacionalidade'], $coluna['civil'], $coluna['sexo'], $coluna['pai'], $coluna['nacionalidade_pai'], $coluna['mae'], $coluna['nacionalidade_mae'], $coluna['estuda'], $coluna['data_escola'], $coluna['escolaridade'], $coluna['instituicao'], $coluna['curso'], $coluna['num_filhos'], $dependentes['nome1'], $dependentes['data1'], $dependentes['nome2'], $dependentes['data2'], $dependentes['nome3'], $dependentes['data3'], $dependentes['nome4'], $dependentes['data4'], $dependentes['nome5'], $dependentes['data5'], $coluna['cabelos'], $coluna['olhos'], $coluna['peso'], $coluna['altura'], $coluna['etnia'], $coluna['defeito'], $coluna['deficiencia'], $coluna['foto'], $coluna['rg'], $coluna['orgao'], $coluna['uf_rg'], $coluna['data_rg'], $coluna['cpf'], $coluna['reservista'], $coluna['campo1'], $coluna['serie_ctps'], $coluna['uf_ctps'], $coluna['data_ctps'], $coluna['titulo'], $coluna['zona'], $coluna['secao'], $coluna['pis'], $coluna['dada_pis'], $coluna['fgts'], $coluna['medica'], $coluna['plano'], $coluna['apolice'], $coluna['campo2'], $coluna['insalubridade'], $coluna['ad_noturno'], $coluna['transporte'], $coluna['cipa'], $coluna['banco'], $coluna['agencia'], $coluna['conta'], $coluna['tipo_conta'], $coluna['nome_banco'], $coluna['data_entrada'], $coluna['data_exame'], $coluna['localpagamento'], $coluna['tipo_pagamento'], $coluna['observacao'], $coluna['assinatura'], $coluna['distrato'], $coluna['outros']);

$posts = array($_POST['codigo'], $_POST['tipo_bol'], $_POST['id_curso'], $_POST['lotacao'], $_POST['nome'], $_POST['endereco'], $_POST['bairro'], $_POST['cidade'], $_POST['uf'], $_POST['cep'], $_POST['tel_fixo'], $_POST['tel_cel'], $_POST['tel_rec'], formata($_POST['data_nasc']), $_POST['naturalidade'], $_POST['nacionalidade'], $_POST['civil'], $_POST['sexo'], $_POST['pai'], $_POST['nacionalidade_pai'], $_POST['mae'], $_POST['nacionalidade_mae'], $_POST['estuda'], formata($_POST['data_escola']), $_POST['escolaridade'], $_POST['instituicao'], $_POST['curso'], $_POST['filhos'], $_POST['filho_1'], formata($_POST['data_filho_1']), $_POST['filho_2'], formata($_POST['data_filho_2']), $_POST['filho_3'], formata($_POST['data_filho_3']), $_POST['filho_4'], formata($_POST['data_filho_4']), $_POST['filho_5'], formata($_POST['data_filho_5']), $_POST['cabelos'], $_POST['olhos'], $_POST['peso'], $_POST['altura'], $_POST['etnia'], $_POST['defeito'], $_POST['deficiencia'], formata2($_POST['foto']), $_POST['rg'], $_POST['orgao'], $_POST['uf_rg'], formata($_POST['data_rg']), $_POST['cpf'], $_POST['reservista'], $_POST['trabalho'], $_POST['serie_ctps'], $_POST['uf_ctps'], formata($_POST['data_ctps']), $_POST['titulo'], $_POST['zona'], $_POST['secao'], $_POST['pis'], formata($_POST['data_pis']), $_POST['fgts'], $_POST['medica'], $_POST['plano_medico'], $_POST['apolice'], $_POST['dependente'], formata2($_POST['insalubridade']), $_POST['ad_noturno'], formata2($_POST['transporte']), $_POST['cipa'], $_POST['banco'], $_POST['agencia'], $_POST['conta'], $_POST['radio_tipo_conta'], $_POST['nome_banco'], formata($_POST['data_entrada']), formata($_POST['exame_data']), $_POST['localpagamento'], $_POST['tipopg'], $_POST['observacoes'], $_POST['assinatura'], $_POST['assinatura2'], $_POST['assinatura3']);

$campos = array("o código", "o tipo de contratação", "o curso", "a unidade", "o nome", "o endereço", "o bairro", "a cidade", "o estado", "o CEP", "o telefone fixo", "o telefone celular", "o telefone de recado", "a data de nascimento", "a naturalidade", "a nacionalidade", "o estado civil", "o sexo", "o nome do pai", "a nacionalidade do pai", "o nome da mãe", "a nacionalidade da mãe", "o estudo", "o término do estudo", "a escolaridade", "a instituição escolar", "o curso", "o número de filhos", "o nome do 1º filho", "a data de nascimento do 1º filho", "o nome do 2º filho", "a data de nascimento do 2º filho", "o nome do 3º filho", "a data de nascimento do 3º filho", "o nome do 4º filho", "a data de nascimento do 4º filho", "o nome do 5º filho", "a data de nascimento do 5º filho", "a cor do cabelo", "a cor dos olhos", "o peso", "a altura", "a etnia", "a marca", "a deficiência", "a foto", "o RG", "o órgão do RG", "o estado do RG", "a data do RG", "o CPF", "o certificado de reservista", "a carteira de trabalho", "a série do CTPS", "o estado do CTPS", "a data do CTPS", "o Título de Eleitor", "a zona do Título", "a secão do Título", "o PIS", "a data do PIS", "o FGTS", "a assistência médica", "o tipo de plano", "a apólice", "o dependente", "a insalubridade", "o adicional noturno", "o vale transporte", "o integrante do CIPA", "o banco", "a agência", "a conta", "o tipo de conta", "o nome do banco", "a data de entrada", "a data de exame", "o local de pagamento", "o tipo de pagamento", "as observações", "a assinatura", "o distrato", "os outros documentos");

$n = 0;
$edicao = "";

for ($a=0; $a<=82; $a++) {
	if(($colunas[$a] != $posts[$a]) and (empty($posts[$a]))) {
		$n++;
		$edicao .= " <b>$n)</b> removeu <b>$campos[$a] ($colunas[$a])</b>";
	} elseif(($colunas[$a] != $posts[$a]) and (empty($colunas[$a]))) {
		$n++;
		$edicao .= " <b>$n)</b> inseriu <b>$campos[$a] ($posts[$a])</b>";
	} elseif($colunas[$a] != $posts[$a]) {
		$n++;
		$edicao .= " <b>$n)</b> editou <b>$campos[$a]</b> de <b>$colunas[$a]</b> para <b>$posts[$a]</b>";
	}
}

$qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$funcionario = mysql_fetch_array($qr_funcionario);
$ip = $_SERVER['REMOTE_ADDR'];
$data = date("d/m/Y H:i");
$cabecalho = "($funcionario[0]) $funcionario[nome] às ".$data."h (ip: $ip)";
$local = "Edição de Bolsista - ($coluna[campo3]) $coluna[nome]";
$local_banco = "Edição de Bolsista";
$acao_banco = "Editou o Bolsista ($coluna[campo3]) $coluna[nome]";

mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) 
VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local_banco', NOW(), '$ip', '$acao_banco')") or die ("Erro Inesperado<br><br>".mysql_error());

$nome_arquivo = "log/".$funcionario[0].".txt";

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
//
?>