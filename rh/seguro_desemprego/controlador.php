<?php

/*
 * PHP-DOC  
 * 
 * Procedimentos para geração do arquivos SD (Seguro Desemprego)
 *  
 * ??/??/????
 * 
 * @version
 * 
 * Versão: 3.0.0000 - ??/??/???? - Não Def - Versão Inicial
 * Versão: 3.0.6666 - 18/02/2016 - Jacques - Acrescentado o nono dígito no campo do telefone a partir da coluna 120
 * 
 * @author não definido
 * 
 * @copyright www.f71.com.br
 * 
 */

include('../../conn.php');

include('../../wfunction.php');
include '../../classes/ArquivoTxt.class.php';
include 'classes/DaoSd.class.php';


$arr['mes'] = isset($_POST['mes']) ? $_POST['mes'] : FALSE;
$arr['ano'] = isset($_POST['ano']) ? $_POST['ano'] : FALSE;
$arr['id_clt'] = isset($_POST['id_clt']) ? $_POST['id_clt'] : FALSE;
$arr['tipo_cnpj'] = isset($_POST['cnpj_master']) ? $_POST['cnpj_master'] : FALSE;

$check_clts = isset($_POST['clts']) ? $_POST['clts'] : array();
if (!empty($_POST['id_clt'])) {
    $check_clts[] = $_POST['id_clt'];
}

$dao = new DaoSd();


$dados = $dao->getRelacao($arr);

if($_COOKIE['debug'] == 666){
    echo "## DADOS ##";
    print_array($dados);
}

$obj = new ArquivoTxt();

//echo 'foi 2';
//exit();
$quebra_linha = FALSE;
foreach ($dados as $key => $empresa) {

    if ($quebra_linha) {
        $obj->fechalinha();
    } else {
        $quebra_linha = TRUE;
    }

    $obj->dados('00'); // TIPO REGISTRO 
    $obj->dados('1'); // TIPO IDENTIFICADOR 1 - CNPJ 2 - CEI 
    $obj->dados($obj->completa($obj->limpar($key), 14)); // IDENTIFICADOR EMPRESA
    $obj->dados('001'); //versão do layout
    $obj->dados($obj->completa('', 280));
    $obj->fechalinha();
    
    $erros = array();

    foreach ($empresa['clts'] as $funcionario) {
        
        if($_COOKIE['debug'] == 666){
            echo "## FUNCIONARIO (#{$funcionario['id_clt']} - {$funcionario['nome']}) ##";
            print_array($dados);
        }

        if (in_array($funcionario['id_clt'], $check_clts)) {
            
            #NOME DA MÃE EM BRANCO
            if($funcionario['mae'] == ""){
                $erros['MAE'][$funcionario['id_clt']][$funcionario['nome']] = utf8_encode("Nome da mãe em branco - #{$funcionario['id_clt']} {$funcionario['nome']}");
            }
            
            #HORAS TRABALHADAS EM BRANCO
            if($funcionario['hora_semana'] == ""){
                $erros['HORAS_TRABALHADAS'][$funcionario['id_clt']][$funcionario['nome']] = utf8_encode("Horas trabalhadas em branco - #{$funcionario['id_clt']} {$funcionario['nome']}");
            }
            
            #HORAS TRABALHADAS MAIOR QUE 44
            if($funcionario['hora_semana'] > 44 || $funcionario['hora_semana'] < 1){
                $erros['HORA_SEMANA'][$funcionario['id_clt']][$funcionario['nome']] = utf8_encode("A quantidade de horas trabalhadas deve estar dentro do intervalo entre 01 e 44 - #{$funcionario['id_clt']} {$funcionario['nome']}");
            }
            
            $obj->dados('01'); // TIPO REQUERIMENTO
            $obj->dados($obj->completa($obj->limpar($funcionario['cpf']), 11));
            $obj->dados($obj->completa($obj->limpar($obj->nome($funcionario['nome'], 'UTF-8')), 40));
            $obj->dados($obj->completa($obj->limpar($obj->nome($funcionario['endereco'], 'UTF-8')), 40));
            $obj->dados($obj->completa($obj->limpar($obj->nome($funcionario['numero'] . ' ' . $funcionario['bairro'] . ' ' . $funcionario['complemento'], 'UTF-8')), 16));
            $obj->dados($obj->completa($obj->limpar($funcionario['cep']), 8));
            $obj->dados($obj->completa($obj->limpar($funcionario['uf']), 2));
            
            /*
             * gambi para pegar o telefone
             */
            $ddd = 0;
            $tel = 0;
            if(strlen($funcionario['tel_fixo']) >= 10){
                $ddd = substr($funcionario['tel_fixo'], 0,2);
                $tel = substr($funcionario['tel_fixo'], 2);
            }elseif(strlen($funcionario['tel_fixo']) >= 8){
                $ddd = 0;
                $tel = substr($funcionario['tel_fixo'], 8);
            }
            /*
             * gambi para pegar o telefone
             */
                     
            $obj->dados($obj->completa($ddd, 2, 0)); //ddd * não obrigatório
            $obj->dados($obj->completa($tel, 8, 0)); // telefone * não obrigatório
            $obj->dados($obj->completa($obj->limpar($obj->nome($funcionario['mae'], 'UTF-8')), 40));
            $obj->dados($obj->completa($obj->limpar($funcionario['pis']), 11));
            $obj->dados($obj->completa($obj->limpar($funcionario['numero_ctps']), 8, '0', 'antes'));
            $obj->dados($obj->completa($obj->limpar($funcionario['serie_ctps']), 5, '0', 'antes'));
            $obj->dados($obj->completa($obj->limpar($funcionario['uf_ctps']), 2));
            $obj->dados($obj->completa($obj->limpar($funcionario['cbo_codigo']), 6));
            $obj->dados($obj->completa($obj->limpar($funcionario['data_admissao']), 8));
            $obj->dados($obj->completa($obj->limpar($funcionario['data_dispensa']), 8));
            $obj->dados($obj->completa($obj->limpar($funcionario['sexo']), 1));
            $obj->dados($obj->completa($obj->limpar($funcionario['escolaridade']), 2, '0', 'antes'));
            $obj->dados($obj->completa($obj->limpar($funcionario['data_nascimento']), 8));
            $obj->dados($obj->completa($obj->limpar($funcionario['hora_semana']), 2));

            $completa = '0';

            $sal_2_mes_anterior = number_format($funcionario['sal_2_mes_anterior'], 2, '', '');
            $obj->dados($obj->completa($obj->limpar($sal_2_mes_anterior), 10, $completa, 'antes')); // ante penultimo salario *não obrigatório

            $sal_1_mes_anterior = number_format($funcionario['sal_1_mes_anterior'], 2, '', '');
            $obj->dados($obj->completa($obj->limpar($sal_1_mes_anterior), 10, $completa, 'antes')); // penúltimo salario *não obrigatório

            $sal_mes_rescisao = number_format($funcionario['sal_mes_rescisao'], 2, '', '');
            $obj->dados($obj->completa($obj->limpar($sal_mes_rescisao), 10, $completa, 'antes')); // ultimo salario 
            $obj->dados($obj->completa($obj->limpar($funcionario['meses_trabalhados']), 2, '0', 'antes'));
            $obj->dados($obj->completa($obj->limpar($funcionario['recebeu_6_meses']), 1, '0'));
            $obj->dados($obj->completa($obj->limpar($funcionario['aviso_codigo']), 1));

            $obj->dados($obj->completa($funcionario['id_banco'], 3, '0')); //CÓDIGO BANCO  $funcionario['id_banco']

            if (!empty($funcionario['agencia'])) {
                $num_agencia = substr($obj->limpar($funcionario['agencia']), 0, 4);
                $digito_verificador = substr($obj->limpar($funcionario['agencia']), 4);
            } else {
                $num_agencia = '0';
                $digito_verificador = '0';
            }


            $obj->dados($obj->completa($num_agencia, 4, '0')); //CÓDIGO AGÊNCIA $funcionario['agencia']
            $obj->dados($obj->completa($digito_verificador, 1, '0')); // DV DA AGÊNCIA
            $obj->dados($obj->completa(' ', 28));
            $obj->fechalinha();
        }
    }
    $obj->dados('99');
    $obj->dados($obj->completa(count($empresa['clts']), 5, '0', 'antes'));
    $obj->dados($obj->completa('', 293));
}



// Gera o arquivo
$diretorio = 'arquivos/';

if (!is_dir($diretorio)) {
    mkdir($diretorio);
}
$nome = date('YmdHis') . '_' . $_COOKIE['logado'] . '.SD';
//    $nome = 'teste.sd';
//    $nome = 'GRRF.re';
$caminho = $diretorio . $nome;
if (file_exists($caminho))
    unlink($caminho);
$fp = fopen($caminho, "a");
$escreve = fwrite($fp, $obj->arquivo);
fclose($fp);

echo json_encode(array('textarea' => utf8_encode($obj->arquivo), 'dados' => $funcionario, 'sql' => $sql, 'arquivo' => $nome, 'sql' => $sql_relaorio, 'erros' => $erros));
exit();
