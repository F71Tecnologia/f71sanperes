<?php

if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="www.netsorrindo.com.br/intranet/login.php">Logar</a>';
    exit;
}

include('../../conn.php');
include('../../classes/funcionario.php');
require('../fpdf/fpdf.php');
require('../../wfunction.php');

error_reporting(1);
$usuario = carregaUsuario();
$Master = $usuario['id_master'];
$ano_base = date('Y') - 1;

//SELECIONANDO A REGI√O ADMINISTRATIVA PELO MASTER DO USUARIO LOGADO
$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_master = '$Master' AND sigla = 'AD' ") or die(mysql_error());
$row_regiao = mysql_fetch_assoc($qr_regiao);

//SELECIONANDO AS INFORMA«’ES DA EMPRESA, COMO ENDERE«O CNPJ ENTRE OUTROS
$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '{$row_regiao['id_regiao']}' ") or die(mysql_error());
$empresa = mysql_fetch_assoc($qr_empresa);

//SELECIONANDO TODOS OS PROJETOS ATIVOS
$qr_projetos_ativos = mysql_query("SELECT * FROM projeto WHERE status_reg = '1'");
while ($row_projetos_ativos = mysql_fetch_assoc($qr_projetos_ativos)) {
    $projetos_ativos[] = $row_projetos_ativos['id_projeto'];
}
$projetos_ativos = implode(',', $projetos_ativos);

//DESNECESSARIO
//$qr_empregado = mysql_query("SELECT DISTINCT(rh_clt .id_clt), rh_clt.nome, rh_folha_proc.ano 							
//							FROM rh_clt 
//							INNER JOIN rh_folha_proc ON rh_folha_proc.id_clt = rh_clt.id_clt	
//                                                        WHERE rh_folha_proc.ano = '$ano_base'  AND rh_clt.id_regiao NOT IN ('2','4','6','7','11','13','20','26','36') 
//                                                        AND rh_clt.id_projeto IN($projetos_ativos) ORDER BY `rh_clt`.`nome`  ASC") or die(mysql_error()); //retirado limit 0,100
//$empregado = mysql_fetch_assoc($qr_empregado);
//$num_empregado = mysql_num_rows($qr_empregado);

$sql_emp = "SELECT DISTINCT(rh_clt .id_clt), rh_clt.nome, rh_folha_proc.ano, rh_clt.id_curso, rh_clt.data_nasci, 
                                            rh_clt.nacionalidade, rh_clt.escolaridade, rh_clt.cpf, rh_clt.campo1, rh_clt.serie_ctps, 
                                            rh_clt.data_entrada,rh_clt.etnia, rh_clt.deficiencia, rh_clt.sexo, rh_clt.rh_sindicato,
                                            rh_clt.id_regiao, rh_clt.pis
					FROM rh_clt 
					INNER JOIN rh_folha_proc ON (rh_folha_proc.id_clt = rh_clt.id_clt)
					WHERE 
                                            rh_folha_proc.ano = '$ano_base'  AND 
                                            rh_clt.id_regiao NOT IN ('2','4','6','7','11','13','20','26','36') AND 
                                            rh_clt.id_projeto IN($projetos_ativos) 
                                        ORDER BY `rh_clt`.`nome`  ASC";
exit($sql_emp);
$qr_empregado = mysql_query($sql_emp);

$arquivo = fopen("arquivos/arquivo.txt", "wa");

// Linha 1
// MONTANDO O CABE«ALHO DO ARQUIVO
$sequencial = '1';
$sequencial = sprintf("%06s", $sequencial);
fwrite($arquivo, $sequencial, 6);

$cnpj = RemoveCaracteres($empresa['cnpj']);
$cnpj = substr($cnpj, 0, 14);
$cnpj = sprintf("%014s", $cnpj);
fwrite($arquivo, $cnpj, 14);

$prefixo = '00';
fwrite($arquivo, $prefixo, 2);

$registro = '0';
fwrite($arquivo, $registro, 1);

$constante = '1';
$constante = sprintf("%01s", $constante);
fwrite($arquivo, $constante, 1);

$cpf = RemoveCaracteres($empresa['cnpj']);
$cpf = substr($cpf, 0, 14);
$cpf = sprintf("%014s", $cpf);
fwrite($arquivo, $cpf, 14);

$tipo_inscricao = 1;
$tipo_inscricao = sprintf("%01s", $tipo_inscricao);
fwrite($arquivo, $tipo_inscricao, 1);

$nome = substr(RemoveAcentos(RemoveCaracteres($empresa['razao'])), 0, 40);
$nome = sprintf("%-40s", $nome);
fwrite($arquivo, $nome, 40);

$logradouro = explode(',', $empresa['endereco']);
$logradouro = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($logradouro[0]))), 0, 40);
$logradouro = sprintf("%-40s", $logradouro);
fwrite($arquivo, $logradouro, 40);

$numero = explode(',', $empresa['endereco']);
$numero = explode('-', $numero[1]);
$numero = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($numero[0]))), 0, 6);
$numero = sprintf("%06s", $numero);
fwrite($arquivo, $numero, 6);

$complemento = substr(NULL, 0, 21);
$complemento = sprintf("%-21s", $complemento);
fwrite($arquivo, $complemento, 21);

$bairro = explode('-', $empresa['endereco']);
$bairro = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($bairro[1]))), 0, 19);
$bairro = sprintf("%-19s", $bairro);
fwrite($arquivo, $bairro, 19);

$cep = substr(RemoveCaracteres(RemoveEspacos($empresa['cep'])), 0, 8);
;
$cep = sprintf("%08s", $cep);
fwrite($arquivo, $cep, 8);

$cod_municipio = substr(RemoveCaracteres($empresa['cod_municipio']), 0, 7);
$cod_municipio = sprintf("%07s", $cod_municipio);
fwrite($arquivo, $cod_municipio, 7);

$cidade = explode('-', $empresa['endereco']);
$cidade = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($cidade[2]))), 0, 30);
$cidade = sprintf("%-30s", $cidade);
fwrite($arquivo, $cidade, 30);

$uf = explode('-', $empresa['endereco']);
$uf = substr(RemoveAcentos(RemoveCaracteres(RemoveEspacos($uf[3]))), 0, 2);
$uf = sprintf("%02s", $uf);
fwrite($arquivo, $uf, 2);

$ddd_telefone = explode('(', $empresa['tel']);
$ddd_telefone = substr(RemoveCaracteres(RemoveEspacos($ddd_telefone[1])), 0, 2);
$ddd_telefone = sprintf("%02s", $ddd_telefone);
fwrite($arquivo, $ddd_telefone, 2);

$telefone = explode(')', $empresa['tel']);
$telefone = substr(RemoveCaracteres(RemoveEspacos($telefone[1])), 0, 8);
$telefone = sprintf("%08s", $telefone);
fwrite($arquivo, $telefone, 8);

$indicador_retificacao = '2';
fwrite($arquivo, $indicador_retificacao, 1);

$data_retificacao = substr(NULL, 0, 8);
$data_retificacao = sprintf("%08s", $data_retificacao);
fwrite($arquivo, $data_retificacao, 8);

$data = date('dmY');
fwrite($arquivo, $data, 8);

$email_responsavel = substr($empresa['email'], 0, 45);
$email_responsavel = sprintf("%-45s", $email_responsavel);
fwrite($arquivo, $email_responsavel, 45);

$nome_responsavel = substr(RemoveCaracteres(RemoveAcentos($empresa['responsavel'])), 0, 52);
$nome_responsavel = sprintf("%-52s", $nome_responsavel);
fwrite($arquivo, $nome_responsavel, 52);

$espacos1 = NULL;
$espacos1 = sprintf("%20s", $espacos1);
fwrite($arquivo, $espacos1, 20);

$tamanho_registro = '0551';
fwrite($arquivo, $tamanho_registro, 4);

$cpf_responsavel = RemoveCaracteres($empresa['cpf']);
$cpf_responsavel = substr($cpf_responsavel, 0, 11);
$cpf_responsavel = sprintf("%011s", $cpf_responsavel);
fwrite($arquivo, $cpf_responsavel, 11);

$crea = NULL;
$crea = substr($crea, 0, 12);
$crea = sprintf("%012s", $crea);
fwrite($arquivo, $crea, 12);

$data_nascimento_responsavel = implode('', array_reverse(explode('-', $empresa['data_nasc'])));
$data_nascimento_responsavel = substr($data_nascimento_responsavel, 0, 8);
$data_nascimento_responsavel = sprintf("%08s", $data_nascimento_responsavel);
fwrite($arquivo, $data_nascimento_responsavel, 8);

$espacos2 = NULL;
$espacos2 = sprintf("%160s", $espacos2);
fwrite($arquivo, $espacos2, 160);

fwrite($arquivo, "\r\n");

// Linha 2

$sequencial2 = '2';
$sequencial2 = sprintf("%06s", $sequencial2);
fwrite($arquivo, $sequencial2, 6);

$cnpj2 = $empresa['cnpj'];
$cnpj2 = RemoveCaracteres($cnpj2);
$cnpj2 = substr($cnpj2, 0, 14);
$cnpj2 = sprintf("%014s", $cnpj2);
fwrite($arquivo, $cnpj2, 14);

$prefixo2 = '00';
fwrite($arquivo, $prefixo2, 2);

$registro2 = '1';
fwrite($arquivo, $registro2, 1);

$nome2 = RemoveAcentos($empresa['nome']);
$nome2 = substr($nome, 0, 52);
$nome2 = sprintf("%-52s", $nome2);
fwrite($arquivo, $nome2, 52);

$logradouro2 = explode(',', RemoveAcentos($empresa['endereco']));
$logradouro2 = substr($logradouro2[0], 0, 40);
$logradouro2 = sprintf("%-40s", $logradouro2);
fwrite($arquivo, $logradouro2, 40);

$numero2 = explode(',', $empresa['endereco']);
$numero2 = explode('-', $numero2[1]);
$numero2 = str_replace(' ', '', $numero2[0]);
$numero2 = substr($numero2, 0, 6);
$numero2 = sprintf("%06s", $numero2);
fwrite($arquivo, $numero2, 6);

$complemento2 = '';
$complemento2 = substr($complemento2, 0, 21);
$complemento2 = sprintf("%-21s", $complemento2);
fwrite($arquivo, $complemento2, 21);

$bairro2 = explode(',', $empresa['endereco']);
$bairro2 = explode('-', $bairro2[1]);
$bairro2 = str_replace(' ', '', $bairro2[1]);
$bairro2 = substr($bairro2, 0, 19);
$bairro2 = sprintf("%-19s", $bairro2);
fwrite($arquivo, $bairro2, 19);

$cep2 = $empresa['cep'];
$cep2 = str_replace('-', '', $cep2);
$cep2 = substr($cep2, 0, 8);
$cep2 = sprintf("%08s", $cep2);
fwrite($arquivo, $cep2, 8);

$cod_municipio2 = $empresa['cod_municipio'];
$cod_municipio2 = str_replace('-', '', $cod_municipio2);
$cod_municipio2 = substr($cod_municipio2, 0, 7);
$cod_municipio2 = sprintf("%07s", $cod_municipio2);
fwrite($arquivo, $cod_municipio2, 7);

$cidade2 = explode(',', $empresa['endereco']);
$cidade2 = explode('-', $cidade2[1]);
$cidade2 = str_replace(' ', '', $cidade2[2]);
$cidade2 = substr($cidade2, 0, 30);
$cidade2 = sprintf("%-30s", $cidade2);
fwrite($arquivo, $cidade, 30);

$uf2 = explode(',', $empresa['endereco']);
$uf2 = explode('-', $uf2[1]);
$uf2 = str_replace(' ', '', $uf2[3]);
$uf2 = substr($uf2, 0, 2);
$uf2 = sprintf("%02s", $uf2);
fwrite($arquivo, $uf2, 2);

$ddd_telefone2 = explode('(', $empresa['tel']);
$ddd_telefone2 = substr($ddd_telefone2[1], 0, 2);
$ddd_telefone2 = sprintf("%02s", $ddd_telefone2);
fwrite($arquivo, $ddd_telefone2, 2);

$telefone2 = explode(')', $empresa['tel']);
$telefone2 = str_replace(' ', '', $telefone2[1]);
$telefone2 = str_replace('-', '', $telefone2);
$telefone2 = substr($telefone2, 0, 8);
$telefone2 = sprintf("%08s", $telefone2);
fwrite($arquivo, $telefone2, 8);

$email_responsavel2 = $empresa['email'];
$email_responsavel2 = substr($email_responsavel2, 0, 45);
$email_responsavel2 = sprintf("%-45s", $email_responsavel2);
fwrite($arquivo, $email_responsavel2, 45);

$cnae = $empresa['cnae'] . '00';
$cnae = sprintf("%07s", $cnae);
fwrite($arquivo, $cnae, 7);

$natureza = $empresa['natureza'];
$natureza = substr($natureza, 0, 4);
$natureza = sprintf("%04s", $natureza);
fwrite($arquivo, $natureza, 4);

$proprietarios = $empresa['proprietarios'];
$proprietarios = substr($proprietarios, 0, 2);
$proprietarios = sprintf("%02s", $proprietarios);
fwrite($arquivo, $proprietarios, 2);

$data_base = '04';
fwrite($arquivo, $data_base, 2);

$tipo_inscricao = '1';
fwrite($arquivo, $tipo_inscricao, 1);

$tipo_rais = '0';
fwrite($arquivo, $tipo_rais, 1);

$zeros = '';
$zeros = sprintf("%02s", $zeros);
fwrite($arquivo, $zeros, 2);

$matricula_cei = NULL;
$matricula_cei = sprintf("%012s", $matricula_cei);
fwrite($arquivo, $matricula_cei, 12);

$ano_base_rais = $ano_base;
fwrite($arquivo, $ano_base_rais, 4);

$porte_empresa = '3';
fwrite($arquivo, $porte_empresa, 1);

$participacao_simples = '2';
fwrite($arquivo, $participacao_simples, 1);

$participacao_pat = '2';
fwrite($arquivo, $participacao_pat, 1);

$f1 = '';
$f1 = sprintf("%030s", $f1);
fwrite($arquivo, $f1, 30);

$indicator_encerramento = '2';
fwrite($arquivo, $indicator_encerramento, 1);

$data_encerramento = NULL;
$data_encerramento = sprintf("%08s", $data_encerramento);
fwrite($arquivo, $data_encerramento, 8);

$contribuicao_associativa = NULL;
$contribuicao_associativa = sprintf("%014s", $contribuicao_associativa);
fwrite($arquivo, $contribuicao_associativa, 14);

$contribuicao_associativa_centavos = NULL;
$contribuicao_associativa_centavos = sprintf("%09s", $contribuicao_associativa_centavos);
fwrite($arquivo, $contribuicao_associativa_centavos, 9);

$contribuicao_sindical = NULL;
$contribuicao_sindical = sprintf("%014s", $contribuicao_sindical);
fwrite($arquivo, $contribuicao_sindical, 14);

$contribuicao_sindical_centavos = NULL;
$contribuicao_sindical_centavos = sprintf("%09s", $contribuicao_sindical_centavos);
fwrite($arquivo, $contribuicao_sindical_centavos, 9);

$contribuicao_assistencial = NULL;
$contribuicao_assistencial = sprintf("%014s", $contribuicao_assistencial);
fwrite($arquivo, $contribuicao_assistencial, 14);

$contribuicao_assistencial_centavos = NULL;
$contribuicao_assistencial_centavos = sprintf("%09s", $contribuicao_assistencial_centavos);
fwrite($arquivo, $contribuicao_assistencial_centavos, 9);

$contribuicao_confederativa = NULL;
$contribuicao_confederativa = sprintf("%014s", $contribuicao_confederativa);
fwrite($arquivo, $contribuicao_confederativa, 14);

$contribuicao_confederativa_centavos = NULL;
$contribuicao_confederativa_centavos = sprintf("%09s", $contribuicao_confederativa_centavos);
fwrite($arquivo, $contribuicao_confederativa_centavos, 9);

$atividade_ano_base = '1';
fwrite($arquivo, $atividade_ano_base, 1);

$indicador_centralizacao_pagamento = '2';
fwrite($arquivo, $indicador_centralizacao_pagamento, 1);

$cnpj_estabelecimento_centralizador = '';
$cnpj_estabelecimento_centralizador = sprintf("%014s", $cnpj_estabelecimento_centralizador);
fwrite($arquivo, $cnpj_estabelecimento_centralizador, 14);

$indicador_empresa_filiada_sindicato = '2';
fwrite($arquivo, $indicador_empresa_filiada_sindicato, 1);

$espacos3 = '';
$espacos3 = sprintf("%89s", $espacos3);
fwrite($arquivo, $espacos3, 89);

$exclusivo_empresa1 = '';
$exclusivo_empresa1 = sprintf("%12s", $exclusivo_empresa1);
fwrite($arquivo, $exclusivo_empresa1, 12);

fwrite($arquivo, "\r\n");

// Linha 3
$sequencial3 = '2';

while ($empregado = mysql_fetch_assoc($qr_empregado)) {


    $qr_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$empregado[id_curso]'");
    $curso = mysql_fetch_array($qr_curso);

    $qr_cbo = mysql_query("SELECT cod FROM rh_cbo WHERE id_cbo = '$curso[cbo_codigo]'");
    $row_cbo = mysql_fetch_assoc($qr_cbo);
    $num_cbo = mysql_num_rows($qr_cbo);

    if (empty($num_cbo)) {
        $cbo = $curso['cbo_codigo'];
    } else {
        $cbo = $row_cbo['cod'];
    }

    $sequencial3++;
    $sequencial3 = sprintf("%06s", $sequencial3);
    fwrite($arquivo, $sequencial3, 6);

    $cnpj3 = substr(RemoveCaracteres($empresa['cnpj']), 0, 14);
    $cnpj3 = sprintf("%014s", $cnpj3);
    fwrite($arquivo, $cnpj3, 14);

    $prefixo3 = '00';
    fwrite($arquivo, $prefixo3, 2);

    $registro3 = '2';
    fwrite($arquivo, $registro3, 1);

    $empregado_pis = RemoveCaracteres($empregado['pis']);
    $empregado_pis = sprintf("%011s", $empregado_pis);
    fwrite($arquivo, $empregado_pis, 11);

    $empregado_nome = RemoveAcentos(RemoveCaracteres($empregado['nome']));
    $empregado_nome = sprintf("%-52s", $empregado_nome);
    fwrite($arquivo, $empregado_nome, 52);

    $empregado_data_nasc = implode('', array_reverse(explode('-', $empregado['data_nasci'])));
    $empregado_data_nasc = sprintf("%08s", $empregado_data_nasc);
    fwrite($arquivo, $empregado_data_nasc, 8);

    if ($empregado['nacionalidade'] == 'BRASILEIRO' or $empregado['nacionalidade'] == 'Brasileiro' or $empregado['nacionalidade'] == 'brasileiro' or $empregado['nacionalidade'] == 'BRASILEIRA' or $empregado['nacionalidade'] == 'Brasileira' or $empregado['nacionalidade'] == 'brasileira') {
        $empregado_nacionalidade = '10';
        $empregado_ano_chegada = NULL;
    }

    $empregado_nacionalidade = sprintf("%02s", $empregado_nacionalidade);
    fwrite($arquivo, $empregado_nacionalidade, 2);

    $empregado_ano_chegada = sprintf("%04s", $empregado_ano_chegada);
    fwrite($arquivo, $empregado_ano_chegada, 4);

    if ($empregado['escolaridade'] < 12 and $empregado['escolaridade'] > 0) {
        $qr_cod_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE id = '$empregado[escolaridade]'");
        $cod_escolaridade = mysql_fetch_assoc($qr_cod_escolaridade);
    }

    $instrucao = number_format($cod_escolaridade['cod'], 0, '.', '.');
    $instrucao = sprintf("%02s", $instrucao);
    fwrite($arquivo, $instrucao, 2);

    $empregado_cpf = $empregado['cpf'];
    $empregado_cpf = str_replace('-', '', $empregado_cpf);
    $empregado_cpf = str_replace('.', '', $empregado_cpf);
    $empregado_cpf = sprintf("%011s", $empregado_cpf);
    fwrite($arquivo, $empregado_cpf, 11);

    if (strstr($empregado['campo1'], '/')) {
        $empregado_ctps = explode('/', $empregado['campo1']);
        $empregado_ctps = $empregado_ctps[0];
        $empregado_ctps_serie = $empregado_ctps[1];
    } else {
        $empregado_ctps = $empregado['campo1'];
        $empregado_ctps_serie = $empregado['serie_ctps'];
    }

    $empregado_ctps = sprintf("%08s", RemoveCaracteres(RemoveLetras(RemoveAcentos($empregado_ctps))));
    fwrite($arquivo, $empregado_ctps, 8);

    $empregado_ctps_serie = sprintf("%05s", RemoveCaracteres(RemoveLetras(RemoveAcentos($empregado_ctps_serie))));
    fwrite($arquivo, $empregado_ctps_serie, 5);

    $empregado_data_admissao = implode('', array_reverse(explode('-', $empregado['data_entrada'])));
    $empregado_data_admissao = sprintf("%08s", $empregado_data_admissao);
    fwrite($arquivo, $empregado_data_admissao, 8);

    $empregado_tipo_admissao = '2';
    $empregado_tipo_admissao = sprintf("%02s", $empregado_tipo_admissao);
    fwrite($arquivo, $empregado_tipo_admissao, 2);

    $empregado_salario_contratual = $curso['salario'];
    $empregado_salario_contratual = str_replace('.', '', $empregado_salario_contratual);
    $empregado_salario_contratual = sprintf("%09s", $empregado_salario_contratual);
    fwrite($arquivo, $empregado_salario_contratual, 9);

    $empregado_tipo_salario = '1';
    $empregado_tipo_salario = sprintf("%01s", $empregado_tipo_salario);
    fwrite($arquivo, $empregado_tipo_salario, 1);

    $horas_semanais = '44';
    $horas_semanais = sprintf("%02s", $horas_semanais);
    fwrite($arquivo, $horas_semanais, 2);

    $cbo = RemoveCaracteres($cbo);
    $cbo = sprintf("%06s", $cbo);
    fwrite($arquivo, $cbo, 6);

    $vinculo = '10';
    $vinculo = sprintf("%02s", $vinculo);
    fwrite($arquivo, $vinculo, 2);

    $qr_rescisao = mysql_query("SELECT * FROM rh_recisao WHERE id_clt = '$empregado[id_clt]' AND year(data_demi) = '$ano_base' AND motivo IN (60,61,62,80,81,100)");
    $rescisao = mysql_fetch_assoc($qr_rescisao);
    $verifica_rescisao = mysql_num_rows($qr_rescisao);

    if (!empty($verifica_rescisao)) {
        $dia_mes_desligamento = substr(implode('', array_reverse(explode('-', $rescisao['data_demi']))), 0, 4);
        $mes_desligamento = substr($dia_mes_desligamento, 2, 4);
        if ($rescisao['motivo'] == 60) {
            $causa = '10';
        } elseif ($rescisao['motivo'] == 61) {
            $causa = '11';
        } elseif ($rescisao['motivo'] == 62 or $rescisao['motivo'] == 100) {
            $causa = '12';
        } elseif ($rescisao['motivo'] == 80) {
            $causa = '76';
        } elseif ($rescisao['motivo'] == 81) {
            $causa = '60';
        }
    } else {
        $mes_desligamento = '13';
    }

//$causa = '11';
//$dia_mes_desligamento = '2702';
//$mes_desligamento = '02';

    $causa = sprintf("%02s", $causa);
    fwrite($arquivo, $causa, 2);

    $dia_mes_desligamento = sprintf("%04s", $dia_mes_desligamento);
    fwrite($arquivo, $dia_mes_desligamento, 4);

// remunera√ß√£o no ano base

    for ($f = 1; $f < $mes_desligamento; $f++) {

        $tubarao = sprintf('%02d', $f);
        $qr_folha = mysql_query("SELECT salliquido, a8006 FROM rh_folha_proc INNER JOIN rh_folha ON rh_folha_proc.id_folha = rh_folha.id_folha WHERE id_clt = '$empregado[id_clt]' AND rh_folha_proc.status = '3' AND rh_folha.status = '3' AND rh_folha_proc.mes = '$tubarao' AND rh_folha.ano = '$ano_base' AND rh_folha.terceiro = '2'");
        $row_folha = mysql_fetch_assoc($qr_folha);
        $total_folha = mysql_num_rows($qr_folha);

        if (!empty($total_folha)) {
            $meses[] = str_replace('.', '', ($row_folha['salliquido'] - $folha['a8006']));
        } else {
            $meses[] = NULL;
        }
    }

    if (!empty($verifica_rescisao)) {
        $meses[] = str_replace('.', '', $rescisao['total_liquido']);
    }

    $remuneracao_janeiro = $meses[0];
    $remuneracao_janeiro = sprintf("%09s", $remuneracao_janeiro);
    fwrite($arquivo, $remuneracao_janeiro, 9);

    $remuneracao_fevereiro = $meses[1];
    $remuneracao_fevereiro = sprintf("%09s", $remuneracao_fevereiro);
    fwrite($arquivo, $remuneracao_fevereiro, 9);

    $remuneracao_marco = $meses[2];
    $remuneracao_marco = sprintf("%09s", $remuneracao_marco);
    fwrite($arquivo, $remuneracao_marco, 9);

    $remuneracao_abril = $meses[3];
    $remuneracao_abril = sprintf("%09s", $remuneracao_abril);
    fwrite($arquivo, $remuneracao_abril, 9);

    $remuneracao_maio = $meses[4];
    $remuneracao_maio = sprintf("%09s", $remuneracao_maio);
    fwrite($arquivo, $remuneracao_maio, 9);

    $remuneracao_junho = $meses[5];
    $remuneracao_junho = sprintf("%09s", $remuneracao_junho);
    fwrite($arquivo, $remuneracao_junho, 9);

    $remuneracao_julho = $meses[6];
    $remuneracao_julho = sprintf("%09s", $remuneracao_julho);
    fwrite($arquivo, $remuneracao_julho, 9);

    $remuneracao_agosto = $meses[7];
    $remuneracao_agosto = sprintf("%09s", $remuneracao_agosto);
    fwrite($arquivo, $remuneracao_agosto, 9);

    $remuneracao_setembro = $meses[8];
    $remuneracao_setembro = sprintf("%09s", $remuneracao_setembro);
    fwrite($arquivo, $remuneracao_setembro, 9);

    $remuneracao_outubro = $meses[9];
    $remuneracao_outubro = sprintf("%09s", $remuneracao_outubro);
    fwrite($arquivo, $remuneracao_outubro, 9);

    $remuneracao_novembro = $meses[10];
    $remuneracao_novembro = sprintf("%09s", $remuneracao_novembro);
    fwrite($arquivo, $remuneracao_novembro, 9);

    $remuneracao_dezembro = $meses[11];
    $remuneracao_dezembro = sprintf("%09s", $remuneracao_dezembro);
    fwrite($arquivo, $remuneracao_dezembro, 9);

    unset($meses);

// remunera√ß√£o 13¬∫ sal√°rio

    $qr_salario13 = mysql_query("SELECT salliquido,rh_folha_proc.mes,tipo_terceiro,id_clt FROM rh_folha_proc INNER JOIN rh_folha ON rh_folha_proc.id_folha = rh_folha.id_folha WHERE id_clt = '$empregado[id_clt]' AND rh_folha_proc.status = '3' AND rh_folha.status = '3' AND rh_folha.ano = '$ano_base' AND rh_folha.terceiro = '1'");
    $numero_salario13 = mysql_num_rows($qr_salario13);
    if (!empty($numero_salario13)) {
        while ($salario13 = mysql_fetch_assoc($qr_salario13)) {
            if ($salario13['tipo_terceiro'] == 3) {
                $valor13_2 = str_replace('.', '', $salario13['salliquido']);
                $mes13_2 = $salario13['mes'];
            } elseif ($salario13['tipo_terceiro'] == 1) {
                $valor13 = str_replace('.', '', $salario13['salliquido']);
                $mes13 = $salario13['mes'];
            } elseif ($salario13['tipo_terceiro'] == 2) {
                $valor13_2 = str_replace('.', '', $salario13['salliquido']);
                $mes13_2 = $salario13['mes'];
            }
        }
    }

    $salario13_adiantamento_valor = $valor13;
    $salario13_adiantamento_valor = sprintf("%09s", $salario13_adiantamento_valor);
    fwrite($arquivo, $salario13_adiantamento_valor, 9);

    if (!empty($valor13)) {
        $salario13_adiantamento_mes = '11';
    }

//$salario13_adiantamento_mes = $mes13;
    $salario13_adiantamento_mes = sprintf("%02s", $salario13_adiantamento_mes);
    fwrite($arquivo, $salario13_adiantamento_mes, 2);

    $salario13_final_valor = $valor13_2;
    $salario13_final_valor = sprintf("%09s", $salario13_final_valor);
    fwrite($arquivo, $salario13_final_valor, 9);

    if (!empty($valor13_2)) {
        $salario13_final_mes = '12';
    }

//$salario13_final_mes = $mes13_2;
    $salario13_final_mes = sprintf("%02s", $salario13_final_mes);
    fwrite($arquivo, $salario13_final_mes, 2);

    $qr_etnia = mysql_query("SELECT * FROM etnias WHERE id = '$empregado[etnia]'");
    $etnia = mysql_fetch_assoc($qr_etnia);
    $etnia = number_format($etnia['cod'], 0, '.', '.');
    $etnia = sprintf("%01s", $etnia);
    fwrite($arquivo, $etnia, 1);

    $qr_deficiencia = mysql_query("SELECT * FROM deficiencias WHERE id = '$empregado[deficiencia]'");
    $deficiencia = mysql_fetch_assoc($qr_deficiencia);
    if (!empty($deficiencia['cod'])) {
        $indicador_deficiencia = '1';
        $tipo_deficiencia = number_format($deficiencia['cod'], 0, '.', '.');
    } else {
        $indicador_deficiencia = '2';
        $tipo_deficiencia = '0';
    }

    $indicador_deficiencia = sprintf("%01s", $indicador_deficiencia);
    fwrite($arquivo, $indicador_deficiencia, 1);

    $tipo_deficiencia = sprintf("%01s", $tipo_deficiencia);
    fwrite($arquivo, $tipo_deficiencia, 1);

    $indicador_alvara = 2;
    $indicador_alvara = sprintf("%01s", $indicador_alvara);
    fwrite($arquivo, $indicador_alvara, 1);

    $aviso_previo_indenizado = NULL;
    $aviso_previo_indenizado = sprintf("%09s", $aviso_previo_indenizado);
    fwrite($arquivo, $aviso_previo_indenizado, 9);

    if ($empregado['sexo'] == 'M') {
        $empregado_sexo = '1';
    } elseif ($empregado['sexo'] == "F") {
        $empregado_sexo = '2';
    }

    $empregado_sexo = sprintf("%01s", $empregado_sexo);
    fwrite($arquivo, $empregado_sexo, 1);

// Afastamentos

    $qr_afastamentos = mysql_query("SELECT * FROM rh_eventos WHERE cod_status IN (70,20,50,30,90) AND id_clt = '$empregado[id_clt]' AND year(data) = '$ano_base' ORDER BY id_evento DESC LIMIT 0,3");
    while ($afastamentos = mysql_fetch_assoc($qr_afastamentos)) {
        $afastamento_motivo[] = $afastamentos['cod_status'];
        $afastamento_inicio[] = $afastamentos['data'];
        $afastamento_final[] = $afastamentos['data_retorno'];
        $afastamento_dias[] = $afastamentos['dias'];
    }

    for ($z = 0; $z <= 2; $z++) {

        if ($afastamento_motivo[$z] == 70) {
            $afastamento_motivo_final[$z] = '10';
        } elseif ($afastamento_motivo[$z] == 20) {
            $afastamento_motivo_final[$z] = '40';
        } elseif ($afastamento_motivo[$z] == 50) {
            $afastamento_motivo_final[$z] = '50';
        } elseif ($afastamento_motivo[$z] == 30) {
            $afastamento_motivo_final[$z] = '60';
        } elseif ($afastamento_motivo[$z] == 90) {
            $afastamento_motivo_final[$z] = '70';
        }

        $afastamento_motivo_final[$z] = sprintf("%02s", $afastamento_motivo_final[$z]);
        fwrite($arquivo, $afastamento_motivo_final[$z], 2);

        $afastamento_inicio[$z] = substr(implode('', array_reverse(explode('-', $afastamento_inicio[$z]))), 0, 4);
        $afastamento_inicio[$z] = sprintf("%04s", $afastamento_inicio[$z]);
        fwrite($arquivo, $afastamento_inicio[$z], 4);

        $afastamento_final[$z] = substr(implode('', array_reverse(explode('-', $afastamento_final[$z]))), 0, 4);
        $afastamento_final[$z] = sprintf("%04s", $afastamento_final[$z]);
        fwrite($arquivo, $afastamento_final[$z], 4);
    }

    $quantidade_dias_afastamento = $afastamento_dias[0] + $afastamento_dias[1] + $afastamento_dias[2];
    $quantidade_dias_afastamento = sprintf("%03s", $quantidade_dias_afastamento);
    fwrite($arquivo, $quantidade_dias_afastamento, 3);

    unset($afastamento_motivo);
    unset($afastamento_motivo_final);
    unset($afastamento_inicio);
    unset($afastamento_final);
    unset($afastamento_dias);
    unset($quantidade_dias_afastamento);

//

    $qr_ferias_indenizadas = mysql_query("SELECT * FROM rh_recisao WHERE motivo IN (60,61,62,80,81,100) AND id_clt = '$empregado[id_clt]' AND year(data_demi) = '$ano_base'");
    $ferias_indenizadas = mysql_fetch_assoc($qr_ferias_indenizadas);
    $valor_ferias_indenizadas = $ferias_indenizadas['valor_total_ferias'];

    $valor_ferias_indenizadas = sprintf("%08s", $valor_ferias_indenizadas);
    fwrite($arquivo, $valor_ferias_indenizadas, 8);

    $valor_banco_horas = NULL;
    $valor_banco_horas = sprintf("%08s", $valor_banco_horas);
    fwrite($arquivo, $valor_banco_horas, 8);

    $quantidade_meses_banco_horas = NULL;
    $quantidade_meses_banco_horas = sprintf("%02s", $quantidade_meses_banco_horas);
    fwrite($arquivo, $quantidade_meses_banco_horas, 2);

    $valor_dissidio_coletivo = NULL;
    $valor_dissidio_coletivo = sprintf("%08s", $valor_dissidio_coletivo);
    fwrite($arquivo, $valor_dissidio_coletivo, 8);

    $quantidade_meses_dissidio_coletivo = NULL;
    $quantidade_meses_dissidio_coletivo = sprintf("%02s", $quantidade_meses_dissidio_coletivo);
    fwrite($arquivo, $quantidade_meses_dissidio_coletivo, 2);

    $valor_gratificacoes = NULL;
    $valor_gratificacoes = sprintf("%08s", $valor_gratificacoes);
    fwrite($arquivo, $valor_gratificacoes, 8);

    $quantidade_meses_gratificacoes = NULL;
    $quantidade_meses_gratificacoes = sprintf("%02s", $quantidade_meses_gratificacoes);
    fwrite($arquivo, $quantidade_meses_gratificacoes, 2);

    $valor_multa_rescisao = NULL;
    $valor_multa_rescisao = sprintf("%08s", $valor_multa_rescisao);
    fwrite($arquivo, $valor_multa_rescisao, 8);

    $cnpj_contribuicao_associativa1 = NULL;
    $cnpj_contribuicao_associativa1 = sprintf("%014s", $cnpj_contribuicao_associativa1);
    fwrite($arquivo, $cnpj_contribuicao_associativa1, 14);

    $valor_contribuicao_associativa1 = NULL;
    $valor_contribuicao_associativa1 = sprintf("%08s", $valor_contribuicao_associativa1);
    fwrite($arquivo, $valor_contribuicao_associativa1, 8);

    $cnpj_contribuicao_associativa2 = NULL;
    $cnpj_contribuicao_associativa2 = sprintf("%014s", $cnpj_contribuicao_associativa2);
    fwrite($arquivo, $cnpj_contribuicao_associativa2, 14);

    $valor_contribuicao_associativa2 = NULL;
    $valor_contribuicao_associativa2 = sprintf("%08s", $valor_contribuicao_associativa2);
    fwrite($arquivo, $valor_contribuicao_associativa2, 8);

// Contribui√ß√£o Sindical

    $qr_sindicato = mysql_query("SELECT * FROM rhsindicato WHERE id_sindicato = '$empregado[rh_sindicato]'");
    $row_sindicato = mysql_fetch_assoc($qr_sindicato);
    $total_sindicato = mysql_num_rows($qr_sindicato);

//$cnpj_contribuicao_sindical = $row_sindicato['cnpj'];
    $cnpj_contribuicao_sindical = '30.132.856/0001-81';
    $cnpj_contribuicao_sindical = str_replace('.', '', $cnpj_contribuicao_sindical);
    $cnpj_contribuicao_sindical = str_replace('-', '', $cnpj_contribuicao_sindical);
    $cnpj_contribuicao_sindical = str_replace('/', '', $cnpj_contribuicao_sindical);
    $cnpj_contribuicao_sindical = sprintf("%014s", $cnpj_contribuicao_sindical);
    fwrite($arquivo, $cnpj_contribuicao_sindical, 14);

//if(!empty($total_sindicato)) {
    $valor_sindicato = $curso['salario'] / 30;
//}

    $calculo_sindical = $valor_sindicato;
    $calculo_sindical = number_format($calculo_sindical, 2, ',', '.');
    $calculo_sindical = str_replace(',', '', $calculo_sindical);
    $calculo_sindical = str_replace('.', '', $calculo_sindical);

    $valor_contribuicao_sindical = $calculo_sindical;
    $valor_contribuicao_sindical = sprintf("%08s", $valor_contribuicao_sindical);
    fwrite($arquivo, $valor_contribuicao_sindical, 8);

//

    $cnpj_contribuicao_assistencial = NULL;
    $cnpj_contribuicao_assistencial = sprintf("%014s", $cnpj_contribuicao_assistencial);
    fwrite($arquivo, $cnpj_contribuicao_assistencial, 14);

    $valor_contribuicao_assistencial = NULL;
    $valor_contribuicao_assistencial = sprintf("%08s", $valor_contribuicao_assistencial);
    fwrite($arquivo, $valor_contribuicao_assistencial, 8);

    $cnpj_contribuicao_confederativa = NULL;
    $cnpj_contribuicao_confederativa = sprintf("%014s", $cnpj_contribuicao_confederativa);
    fwrite($arquivo, $cnpj_contribuicao_confederativa, 14);

    $valor_contribuicao_confederativa = NULL;
    $valor_contribuicao_confederativa = sprintf("%08s", $valor_contribuicao_confederativa);
    fwrite($arquivo, $valor_contribuicao_confederativa, 8);

    $empregado_cod_municipio = NULL;
    $empregado_cod_municipio = sprintf("%07s", $empregado_cod_municipio);
    fwrite($arquivo, $empregado_cod_municipio, 7);

// horas extras trabalhadas

    $horas_extras = NULL;
    $horas_extras = sprintf("%036s", $horas_extras);
    fwrite($arquivo, $horas_extras, 36);

//

    $empregado_indicador_filiado = '2';
    fwrite($arquivo, $empregado_indicador_filiado, 1);

    $exclusivo_empresa2 = '';
    $exclusivo_empresa2 = sprintf("%12s", $exclusivo_empresa2);
    fwrite($arquivo, $exclusivo_empresa2, 12);

    unset($empregado_pis);
    unset($empregado_nome);
    unset($empregado_data_nasc);
    unset($empregado_nacionalidade);
    unset($empregado_ano_chegada);
    unset($instrucao);
    unset($empregado_cpf);
    unset($empregado_ctps);
    unset($empregado_ctps_serie);
    unset($empregado_data_admissao);
    unset($empregado_salario_contratual);
    unset($cbo);
    unset($causa);
    unset($dia_mes_desligamento);
    unset($remuneracao_janeiro);
    unset($remuneracao_fevereiro);
    unset($remuneracao_marco);
    unset($remuneracao_abril);
    unset($remuneracao_maio);
    unset($remuneracao_junho);
    unset($remuneracao_julho);
    unset($remuneracao_agosto);
    unset($remuneracao_setembro);
    unset($remuneracao_outubro);
    unset($remuneracao_novembro);
    unset($remuneracao_dezembro);
    unset($salario13_adiantamento_valor);
    unset($salario13_adiantamento_mes);
    unset($salario13_final_valor);
    unset($salario13_final_mes);
    unset($valor13_2);
    unset($valor13);
    unset($mes13_2);
    unset($mes13);
    unset($etnia);
    unset($indicador_deficiencia);
    unset($tipo_deficiencia);
    unset($empregado_sexo);
    unset($quantidade_dias_afastamento);
    unset($cnpj_contribuicao_sindical);
    unset($valor_contribuicao_sindical);
    unset($valor_sindicato);

    fwrite($arquivo, "\r\n");
}

// Linha 4

$sequencial4 = $num_empregado + 3;
$sequencial4 = sprintf("%06s", $sequencial4);
fwrite($arquivo, $sequencial4, 6);

$cnpj4 = $empresa['cnpj'];
$cnpj4 = str_replace('.', '', $cnpj4);
$cnpj4 = str_replace('/', '', $cnpj4);
$cnpj4 = str_replace('-', '', $cnpj4);
$cnpj4 = substr($cnpj4, 0, 14);
$cnpj4 = sprintf("%014s", $cnpj4);
fwrite($arquivo, $cnpj4, 14);

$prefixo4 = '00';
fwrite($arquivo, $prefixo4, 2);

$registro4 = '9';
fwrite($arquivo, $registro4, 1);

$total_registros1 = '1';
$total_registros1 = sprintf("%06s", $total_registros1);
fwrite($arquivo, $total_registros1, 6);

$total_registros2 = $num_empregado;
$total_registros2 = sprintf("%06s", $total_registros2);
fwrite($arquivo, $total_registros2, 6);

$espacos4 = '';
$espacos4 = sprintf("%516s", $espacos4);
fwrite($arquivo, $espacos4, 516);

fwrite($arquivo, "\r\n");
fclose($arquivo);

print "<a href='arquivos/download.php?file=arquivo.txt'>Baixar arquivo txt </a>";
