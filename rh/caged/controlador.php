<?php

include ("../../conn.php");
include ("../../wfunction.php");
include ("../../classes/construcaoTXT.php");
include ("../../classes/construcaoTXTCaged.php");
include("./actions/montaCaged.class.php");
include("dao/ICagedClass.php");
include("dao/CagedClass.php");



/*
 * VALIDA��ES ///////////
 * uf_ctps 
 * cpf
 * pis/pasep
 * cbo
 * 
 * 
 */


$dev = TRUE;

//echo '<script src="http://www.netsorrindo.com/intranet/js/jquery-1.8.3.min.js" type="text/javascript"></script>';

if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}



$usuario = carregaUsuario();
$id_master = $usuario['id_master'];



$id_master = isset($_REQUEST['master']) ? $_REQUEST['master'] : $id_master;
$ano_competencia = isset($_REQUEST['ano']) ? $_REQUEST['ano'] : date('Y');
$mes_competencia = str_pad(isset($_REQUEST['mes']) ? $_REQUEST['mes'] : date('m'), 2, "0", STR_PAD_LEFT);
$tipo_transmicao_mov = (isset($_REQUEST['acerto']) && $_REQUEST['acerto'] == 'on') ? 'X' : 'C'; // C - Movimentacao, X - Acerto
$competencia = $ano_competencia . '-' . $mes_competencia;



//$tipo_transmicao_mov = 'X'; // C - Movimentacao, X - Acerto

$competencia_arquivo = ($tipo_transmicao_mov == 'C') ? $mes_competencia . $ano_competencia : date('mY');

$dao = new CagedClass();

$master = $dao->getMaster($id_master);

$reprocessar_totalizadores = FALSE;
$filtro_ano = FALSE;
$filtro_mes = FALSE;
if(isset($_REQUEST['reprocessar_totalizadores']) && $_REQUEST['reprocessar_totalizadores'] == 'on'){
    $reprocessar_totalizadores = TRUE;
    $filtro_ano = $ano_competencia;
    $filtro_mes = $mes_competencia;
}
$relacao = $dao->carregaRelacao($id_master, $filtro_ano, $filtro_mes)->getRelacaoPorCompetencia($competencia);

if($reprocessar_totalizadores){
    $totais_dados_competencias = $dao->gravarTotalizadorClt($id_master, $filtro_ano,$filtro_mes);
}else{
    $totais_dados_competencias = $dao->getTotalizadoresClt($filtro_ano,$filtro_mes);
}
echo '<pre>';
print_r($totais_dados_competencias);
echo '</pre>';
exit('ok');


$alteracao = 1; // 1 = nada a alterar, 2 = alterar dados
$sequencia = 1;
$tipo_identificador = 1; // 1 CNPJ, 2 CEI
$cnpj_autorizado = $master['cnpj_limpo'];
$nome_autorizado = $master['razao'];
$endereco_autorizado = $master['endereco'];
$cep_autorizado = $master['cep'];
$uf_autorizado = $master['uf'];
$telefone_completo = $master['telefone_limpo'];
$ramal_autorizado = '     ';
$total_estabelecimentos = count($relacao);
$total_movimentos = $dao->getTotalMovimentos($competencia);



//echo '<pre>';
//print_r($total_movimentos);
//echo '</pre>';
//exit();

$txt = new txtCaged();



//REGISTRO A (AUTORIZADO)
$txt->dados($txt->completa($txt->limpar('A'), 1)); // Tipo de reg
$txt->dados($txt->completa($txt->limpar('L2009'), 5)); // Tipo de layout
$txt->dados($txt->completa(' ', 3)); // 2 Filler + 1 Mag
$txt->dados($txt->completa($txt->limpar($competencia_arquivo), 6)); // Compet�ncia mmaaaa
$txt->dados($txt->completa($txt->limpar($alteracao), 1)); // Altera��o
$txt->dados($txt->completa($txt->limpar($sequencia), 5, '0', 'antes')); // Sequ�ncia
$txt->dados($txt->completa($txt->limpar($tipo_identificador), 1)); // Tipo Identidade
$txt->dados($txt->completa($txt->limpar($master['cnpj_limpo']), 14)); // N�mero Identificador do Autorizado
$txt->dados($txt->completa($txt->nome($master['razao']), 35)); // Nome do Autorizado
$txt->dados($txt->completa($txt->nome($master['endereco']), 40)); // Endere�o do Autorizado
$txt->dados($txt->completa($txt->limpar($master['cep']), 8)); // CEP
$txt->dados($txt->completa($txt->limpar($master['uf']), 2)); // UF
$txt->dados($txt->completa($txt->limpar($master['telefone_limpo']), 12, ' ', 'antes')); // Telefone completo 4 - DDD + 8 TEL
$txt->dados($txt->completa($txt->limpar(''), 5)); // RAMAL
$txt->dados($txt->completa($txt->limpar(count($relacao)), 5, ' ', 'antes')); // Total de Estabelecimentos Informados
$txt->dados($txt->completa($txt->limpar($total_movimentos), 5, ' ', 'antes')); // Total de Movimenta��es Informadas
$txt->dados($txt->completa(' ', 90)); // Filler 50 + 40
$txt->fechalinha();







$codigos_desligamento = $dao->getCodigosDesligamento();

$array_uf = $estados = $dao->getArrayUF(); 

$array_etnias = $dao->getCodigosEtnias();

$erros = array();

foreach ($relacao as $cnpj => $arr_clt) {
    $sequencia++;
    $tipo_identidade = 1; // 1 = CNPJ, 2 CEI

    $primeira_declaracao = 2; // 1 = Sim, 2 = N�o
    $alteracao = 1; //1 = Nada a Atualizar, 2 = Alterar dados estabelecimento (Raz�o, CEP,...), 3 = Encerramento (Fechar Estabelecimento)
    $porte_estabelecimento = 1; //?????
    //REGISTRO B (ESTABELECIMENTO)
    $txt->dados($txt->completa($txt->limpar('B'), 1)); // Tipo de reg
    $txt->dados($txt->completa($txt->limpar($tipo_identidade), 1)); // Tipo Identidade
    $txt->dados($txt->completa($txt->limpar($cnpj), 14)); // N�mero Identificador do Estabelecimento
    $txt->dados($txt->completa($txt->limpar($sequencia), 5, '0', 'antes')); // Sequ�ncia
    $txt->dados($txt->completa($txt->limpar($primeira_declaracao), 1)); // Declara��o
    $txt->dados($txt->completa($txt->limpar($alteracao), 1)); // Altera��o
    $txt->dados($txt->completa($txt->limpar($dao->empresas[$cnpj]['cep_empresa']), 8)); // CEP
    $txt->dados($txt->completa(' ', 5)); // Filler
    $txt->dados($txt->completa($txt->nome($dao->empresas[$cnpj]['razao_empresa']), 40)); // Nome    
    $txt->dados($txt->completa($txt->nome($dao->empresas[$cnpj]['endereco_empresa']), 40)); // Bairro
    $txt->dados($txt->completa($txt->nome($dao->empresas[$cnpj]['bairro_empresa']), 20)); // Bairro
    $txt->dados($txt->completa($txt->limpar($dao->empresas[$cnpj]['uf_empresa']), 2)); // UF
    $txt->dados($txt->completa($txt->limpar($totais_dados_competencias[$cnpj][$competencia]['primeiro_dia']), 5, ' ', 'antes')); // Total de Empregados Existentes no Primeiro Dia
    $txt->dados($txt->completa($txt->limpar($porte_estabelecimento), 1)); // Porte Estabelecimento
    $txt->dados($txt->completa($txt->limpar($dao->empresas[$cnpj]['cnae']), 7)); // CNAE 2.0 com Subclasse
    $txt->dados($txt->completa($txt->limpar($dao->empresas[$cnpj]['tel_empresa']), 12, ' ', 'antes')); // (4)DDD/(8)TEL
    $txt->dados($txt->completa($dao->empresas[$cnpj]['email_empresa'], 50)); // Email
    $txt->dados($txt->completa(' ', 27)); // Filler
    $txt->fechalinha();

//    header('Content-type: text/plain');
//    echo $txt->arquivo;
//    exit();

    foreach ($arr_clt as $clt) {
        
        $aprendiz = 2; // 1 SIM, 2 N�O
        $tipo_identificador = 1;
        $sequencia++;
        $tipo_deficiencia = ' ';
        //ADMISS�O 10 - Primeiro emprego 20 - Reemprego 25 - Contrato por prazo determinado 35 - Reintegra��o 70 - 
        //Transfer�ncia de entrada DESLIGAMENTO 31 - Dispensa sem justa causa 32 - Dispensa por justa causa 40 -
        //A pedido (espont�neo) 43 - T�rmino de contrato por prazo determinado 45 - T�rmino de contrato 50 - Aposentado 60 - Morte 80 - Transfer�ncia de sa�da
        $tipo_movimentacao = '';
        
        
        if (empty($clt['id_curso']) || $clt['id_curso']<=0) {
            $erros['CPF'][$clt['id_clt']]['nome'] =  utf8_encode('LINHA '.$sequencia.' - Curso em branco ou zerado #' . $clt['id_clt'] . ' ' . $clt['nome_funcionario']);
        }
        if (!validaCPF($clt['cpf_limpo'])) {
            $erros['CPF'][$clt['id_clt']]['nome'] =  utf8_encode('LINHA '.$sequencia.' - CPF: "' . $clt['cpf_limpo'] . '" #' . $clt['id_clt'] . ' ' . $clt['nome_funcionario']);
        }
        if (!validaPIS($clt['pis_limpo'])) {
            $erros['PIS'][$clt['id_clt']]['nome'] =  utf8_encode('LINHA '.$sequencia.' - PIS: "' . $clt['pis_limpo'] . '" invalido. #' . $clt['id_clt'] . ' ' . $clt['nome_funcionario']);
        }
        if(empty($clt['numero_ctps']) || $clt['numero_ctps']<=0 || !is_numeric($clt['numero_ctps']) ){
            $erros['CTPS'][$clt['id_clt']]['nome'] = utf8_encode('LINHA '.$sequencia.' - N�mero da carteira de trabalho em branco, zerado ou n�o num�rico. #' . $clt['id_clt'] . ' ' . $clt['nome_funcionario']);
        }
        
        //OBS: UF CTPS PODE ESTAR EM BRANCO
        if(!empty($clt['uf_ctps']) && !in_array($clt['uf_ctps'],$array_uf)){
            $erros['UF_CTPF'][$clt['id_clt']]['nome'] = utf8_encode('LINHA '.$sequencia.' - UF da carteira de trabalho inv�lido. #' . $clt['id_clt'] . ' ' . $clt['nome_funcionario']);
        }
        if(empty($clt['escolaridade']) || $clt['escolaridade']<=0 || $clt['escolaridade']>11 ){
            $erros['ESCOLARIDADE'][$clt['id_clt']]['nome'] = utf8_encode('LINHA '.$sequencia.' - Grau de Escolaridade inv�lida. #' . $clt['id_clt'] . ' ' . $clt['nome_funcionario']);
        }
        if(empty($clt['hora_semana']) || $clt['hora_semana']<=0 || $clt['hora_semana']>44 ){
            $res_curso = $dao->getCurso($clt['id_curso']);
            $erros['HORA_SEMANA'][$clt['id_clt']]['nome'] =  utf8_encode('LINHA '.$sequencia.' - Horas contratuais menores que 1 ou maiores que 44. CURSO: #'.$clt['id_curso'].' '.$res_curso['nome_curso'].'. PROJETO:'.$res_curso['nome_projeto'].'. REGI�O:'.$res_curso['nome_regiao'].'. FUNCION�RIO: #' . $clt['id_clt'] . ' ' . $clt['nome_funcionario']);
        }
        
        if($clt['tipo'] == 'demissao' && $clt['status']<60){
           $erros['STATUS'][$clt['id_clt']]['nome'] = utf8_encode('LINHA '.$sequencia.' - Status Clt inv�lido para Status Demissional tipo 1. #' . $clt['id_clt'] . ' ' . $clt['nome_funcionario']); 
        }
        
//        $cep_trabalhador = substr(str_replace('.','',str_replace('-','',$clt['cep_trabalhador'])),5, 4);
//        if($cep_trabalhador<=0){
//            $erros['CEP'][$clt['id_clt']]['nome'] = utf8_encode('LINHA '.$sequencia.' - Cep "'.$clt['cep_trabalhador'].'" pode estar incorreto. #' . $clt['id_clt'] . ' ' . $clt['nome_funcionario']);
//        }
        
        $dia_entrada = substr($clt['data_entrada_f'],0, 2);
        $mes_entrada = substr($clt['data_entrada_f'],2, 2);
        $ano_entrada = substr($clt['data_entrada_f'],4, 4);
        
        $dia_proc = substr($clt['data_proc_f'],0, 2);
        $mes_proc = substr($clt['data_proc_f'],2, 2);
        $ano_proc = substr($clt['data_proc_f'],4, 4);
        
        
        if($ano_entrada.$mes_entrada.$dia_entrada > $ano_proc.$mes_proc.$ano_proc){
           $erros['DATA_ADMISSAO'][$clt['id_clt']]['nome'] = utf8_encode('LINHA '.$sequencia.' => '.$clt['data_entrada_f'].' > '.$clt['data_proc_f'].' - Data de admiss�o maior que a data de transfer�ncia. #' . $clt['id_clt'] . ' ' . $clt['nome_funcionario']); 
        }
        if(empty($clt['cbo']) || $clt['cbo']<=0){
           $res_curso = $dao->getCurso($clt['id_curso']);
           $erros['CBO'][$clt['id_clt']]['nome'] = utf8_encode('LINHA '.$sequencia.' - CBO branco ou zerado. CURSO: '.$res_curso['nome_curso'].' na Regi�o '.$res_curso['nome_regiao'].' (#' . $clt['id_clt'] . ' ' . $clt['nome_funcionario'].')'); 
        }
        
        $data1 = new DateTime( $competencia.'-01');
        $data2 = new DateTime($clt['data_nasci']);
        $intervalo = $data1->diff($data2);
        
        if ($intervalo->y<=9) {
            $erros['IDADE'][$clt['id_clt']]['nome'] = utf8_encode('LINHA '.$sequencia.' - A IDADE N�O PODE SER MENOR QUE 10 ANOS.  #' . $clt['id_clt'] . ' ' . $clt['nome_funcionario']);
        }
        //REGISTRO C (MOVIMENTA��O)
        $txt->dados($txt->completa($tipo_transmicao_mov, 1)); // Tipo de reg
        $txt->dados($txt->completa($txt->limpar($tipo_identificador), 1)); // Tipo Identidade
        $txt->dados($txt->completa($txt->limpar($clt['cnpj_limpo']), 14)); // N�mero Identificador do Estabelecimento
        $txt->dados($txt->completa($txt->limpar($sequencia), 5, '0', 'antes')); // Sequ�ncia
        $txt->dados($txt->completa($txt->limpar($clt['pis_limpo']), 11)); // PIS/PASEP
        $txt->dados($txt->completa($txt->limpar($clt['sexo']), 1)); // Sexo
        $txt->dados($txt->completa($txt->limpar($clt['data_nasci_f']), 8)); // Nascimento ddmmaaaa
        $txt->dados($txt->completa($txt->limpar($clt['escolaridade']), 2, '0', 'antes')); // Instru��o
        $txt->dados($txt->completa(' ', 4)); // Filler

//        if (isset($salarios_alterados[$clt['id_curso']]) && $clt['data_proc'] <= $salarios_alterados['data']) {
//            $salario = $salarios_alterados['salario_novo'];
//        } else {
//            $salario = $clt['salario_competencia'];
//        }
        $salario = $clt['salario_competencia'];

        $txt->dados($txt->completa($txt->limpar($salario), 8, '0', 'antes')); // Sal Mensal
        $txt->dados($txt->completa($txt->limpar($clt['hora_semana']), 2)); // HS Trabal
//            $txt->dados($txt->completa($txt->limpar($clt['status_admi']), 2,'0','antes')); // Tipo Movimenta��o
        
        $tipo_mov = '';
        $data_desligamento = '';
        $data_entrada = $clt['data_entrada_f'];
        if ($clt['tipo'] == 'demissao') {
            $tipo_mov = $codigos_desligamento[$clt['status']];
            $data_desligamento = $clt['data_proc_f'];
        } elseif ($clt['tipo'] == 'admissao') {
            $tipo_mov = $clt['status_admi'];
        } elseif ($clt['tipo'] == 'entrada') {
            $tipo_mov = '70';
            $data_entrada = $clt['data_proc_f'];
        } elseif ($clt['tipo'] == 'saida') {
            $tipo_mov = '80';
            $data_desligamento = $clt['data_proc_f'];
        }
        
        $txt->dados($txt->completa($txt->limpar($data_entrada), 8)); // Admiss�o ddmmaaaaa
        $txt->dados($txt->completa($txt->limpar($clt['status_admi']), 2, '0', 'antes')); // Tipo Movimenta��o
        //checar se � entrada ou saida pra informar a data ou n�o
        $txt->dados($txt->completa($txt->limpar($data_desligamento), 2, ' ', 'antes')); // Dia Deslig

        $txt->dados($txt->completa($txt->nome($clt['nome_funcionario']), 40)); // Nome do Empregado
        $txt->dados($txt->completa($txt->limpar($clt['numero_ctps']), 8)); // N�mero Carteira de Trabalho 
        $txt->dados($txt->completa($txt->limpar($clt['serie_ctps']), 4)); // S�rie Carteira de Trabalho 

        if ($tipo_transmicao_mov == 'X') {
            $txt->dados(1); // 1 - exclusao, 2 -inclusao
            $txt->dados($txt->completa($mes_competencia . $ano_competencia, '6'));
        } else {
            $txt->dados($txt->completa(' ', 7)); // Filler 
        }

        $clt['etnia'] = in_array($clt['etnia'], $array_etnias) ? $clt['etnia'] : '9';

        $txt->dados($txt->completa($clt['etnia'], 1)); // Ra�a Cor
        $txt->dados($txt->completa($clt['deficiencia'], 1)); // Def F�sico
        $txt->dados($txt->completa($txt->limpar($clt['cbo']), 6)); // CBO 20000
        $txt->dados($txt->completa($txt->limpar($aprendiz), 1)); // Aprendiz

        if ($dev) {
            $clt['uf_ctps'] = empty($clt['uf_ctps']) ? 'RJ' : $clt['uf_ctps'];
        }

        $txt->dados($txt->completa($txt->limpar($clt['uf_ctps']), 2)); // UF CTPS
        $txt->dados($txt->completa($txt->limpar($tipo_deficiencia), 1)); // TP DEF FISI
        $txt->dados($txt->completa($txt->limpar($clt['cpf_limpo']), 11)); // CPF
        $txt->dados($txt->completa($txt->limpar($clt['cep_trabalhador']), 8)); // CEP Resid�ncia Trabalhador
        $txt->dados($txt->completa(' ', 81)); // Filler

        $txt->fechalinha();
    }
}



if (isset($_GET['print'])) {
    header('Content-type: text/plain');
    
    echo '<pre>';
    print_r($totais_dados_competencias);
    echo '</pre>';
    
    echo "\n\n\n";
    
    echo 'ADMITIDOS =>'.$dao->sql_admitidos;
    echo "\n\n\n";
    echo 'DEMITIDOS =>'.$dao->sql_demitidos;
    echo "\n\n\n";
    echo 'TRANSFERIDOS =>'.$dao->sql_transferidos;
    echo "\n\n\n";
    echo($txt->arquivo);
} else {
    $folder = 'arquivos_caged/';
    $name = date('Y-m-d') . '.txt';
    $cont = 1;
    if (!is_dir($folder)) {
        mkdir($folder);
    }
    while (is_file($folder . $name)) {
        $name = date('Y-m-d') . '_' . $cont . '.txt';
        $cont++;
    }
    $file = fopen($folder . $name, 'a');
    fwrite($file, $txt->arquivo);
    fclose($file);
    echo json_encode(array('name_file' => $name, 'erros' => $erros));
}
exit();






//echo $txt->arquivo;
exit();


