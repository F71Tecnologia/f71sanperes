<?php

if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br/><a href="../login.php">Logar</a>';
    exit;    
}



/**
 * Cadastro e alteracao de CLT
 * _____________________________________________________________________________
 * PHP                                  |   JS         
 * Queries                              | Metodos
 * Linha - Tipo                         |
 * 302 - Insert CLT                     |
 * 557 - Update CLT                     |
 * 684 - Update rh_clt_unidades_assoc   |
 * 689 - Insert rh_clt_unidades_assoc   |
 * 705 - Insert favorecido_pensao_assoc |
 * 722 - Update favorecido_pensao_assoc |
 * 769 - Update dependentes             |
 * 773 - Insert dependentes             |    
 * 784 - Update rh_vt_valores_assoc     |
 * 798 - Insert rh_vt_valores_assoc     |
 * 823 - Update rh_inss_outras_empresas |
 * 830 - Insert rh_inss_outras_empresas |
 * Consultas                            |
 * 870 - Verificação de CPF             |
 * 896 - Montagem de horarios           |
 * 913 - Seleciona Unidades             |
 * 924 - Valores das linhas de onibus   |
 * 915 - País de origem por CLT         |
 * 942 - Sindicatos                     |
 * 959 - Seleciona favorecidos de PA    |
 * 970 - Sal. e Desc. Outras empresas   |
 * 978 - Dependentes                    |
 * 996 - Unidades do CLT                |
 * 1004 - VT do CLT                     |
 * 1018 - Descontos outra empresa       |
 * 1028 - Sindicatos da regiao          |
 * 1037 - Funcoes do projeto            |
 * 1094 - Tipos de admissao             |
 * 1103 - Todos projetos                |
 * 1119 - Unidades projetos             |
 * 1121 - Todas UF                      |
 * 1130 - Paises                        |
 * 1137 - Estados Civis                 |
 * 1148 - Nacionalidades                |
 * 1157 - Escolaridades                 |
 * 1166 - Tipo Sanguineo                |
 * 1179 - Olhos/Cabelos                 |
 * 1183 - Etnias                        |
 * 1193 - Deficiencia                   |
 * 1199 - Bancos (Nome)                 |
 * 1209 - Bancos (Fav. Pensao)          |
 * 1223 - Bancos (Contas/Projeto)       |
 * 1233 - Tipos de Pagamentos           |
 * 1243 - Tipos de Contratação          |
 * 1253 - VA/VR                         |
 * 1263 - Apolices                      |
 * 1273 - VT                            |
 * 1283 - LInhas                        |
 * 1293 - Tarifas                       |
 * 1303 - Anos Contribuicao             |
 */

function removeAspas($str) {
    $str = str_replace("'", "", $str);
    return mysql_real_escape_string(trim(str_replace('"', '', $str)));
}

include('../conn.php');
include('../wfunction.php');
include('../classes/SetorClass.php');
include('../classes/PlanoSaudeClass.php');
include('../classes/InssOutrasEmpresasClass.php');
include_once("../classes/LogClass.php");
$log = new Log();


//include_once("../classes/LogClass.php");
//$log = new Log();
/*
foreach ($_REQUEST['favorecidos_pensao'] as $key => $value) {
        //if($value['id']){
           
            //INSERT
            echo $insetFavorecido = "INSERT INTO favorecido_pensao_assoc (id_clt, cpf, favorecido, id_lista_banco, agencia, conta, aliquota, oficio)
            VALUES ({$id_clt}, '{$value['cpf']}', '{$value['favorecido']}', '{$value['id_lista_banco']}', '{$value['agencia']}', '{$value['conta']}', '{$value['aliquota']}', '{$value['oficio']}')<br/>";
            
       // }
//        ($insetFavorecido) ? print_array($insetFavorecido) : null;
//        ($updateFavorecido) ? print_array($updateFavorecido) : null;
    }

(var_dump($_REQUEST['favorecidos_pensao']));

$insertFavorecidos = "INSERT INTO favorecido_pensao_assoc (id_clt,favorecido,cpf,aliquota,id_lista_banco,agencia,conta,oficio) VALUES ";
    for ($i = 1; $i <= count($_REQUEST['favorecidos_pensao']); $i++) {
        
        $favorecido = $_REQUEST['favorecidos_pensao'][$i];
        
        if(!empty($favorecido['id'])){
            
        }else{
            if(isset($_REQUEST['pensao_alimenticia'.$i])){
                $insertFavorecidos .= "('{$id_clt}','{$favorecido['favorecido']}','{$favorecido['cpf']}','{$favorecido['aliquota'][$i]}','{$favorecido['id_lista_banco']}','{$favorecido['agencia']}','{$favorecido['conta']}','{$favorecido['oficio']}'),"; 
                //'{$value['favorecido']}', '{$value['id_lista_banco']}', '{$value['agencia']}', '{$value['conta']}', '{$value['aliquota']}', '{$value['oficio'
            }
           
        }  
    }
    echo $insertFavorecidos;
var_dump($_REQUEST);
die();
*/
/*
var_dump($_REQUEST['favorecidos_pensao']['1']);
echo "ID:".$_REQUEST['favorecidos_pensao']['1']['id'];
die();*/

$usuario = carregaUsuario();

if($_REQUEST['regiao'] != $usuario['id_regiao']){
    header("location: ver.php");
   // var_dump($_REQUEST['regiao'],$usuario['id_regiao']);
    //die();
}
//var_dump($usuario,$_SESSION);

$objPlanoSaude = new PlanoSaudeClass();
$objInssOutrasEmpresas = new InssOutrasEmpresasClass();

$id_projeto = (!empty($_REQUEST['pro'])) ? $_REQUEST['pro'] : $_REQUEST['projeto'];
$id_regiao = $usuario['id_regiao'];

$id_clt = (!empty($_REQUEST['clt'])) ? $_REQUEST['clt'] : null;

if(!$id_clt){  
///GERANDO NÚMERO DE MATRICULA E O NÚMERO DO PROCESSO
$verifica_matricula = mysql_result(mysql_query("SELECT MAX(matricula) FROM rh_clt WHERE id_projeto = {$id_projeto}"), 0);
$matricula = $verifica_matricula + 1;
}
else {
    $verifica_matricula = mysql_result(mysql_query("SELECT matricula FROM rh_clt WHERE id_projeto = {$id_projeto} AND id_clt={$id_clt}"), 0);
    $matricula = $verifica_matricula;    
    }

/**
 * *****************************************************************************
 * *******************INICIO EDIÇAO E CADASTRO**********************************
 * *****************************************************************************
 */
//var_dump($usuario);
if(isset($_REQUEST['salvar']) || isset($_REQUEST['editar'])){
    $cpf_pai= removeAspas($_REQUEST['cpf_pai']);
    $cpf_mae= removeAspas($_REQUEST['cpf_mae']);
    $cpf_conjuge= removeAspas($_REQUEST['cpf_conjuge']);     
    $id_setor = removeAspas($_REQUEST['id_setor']); 
    $id_regiao = removeAspas($_REQUEST['regiao']);
    $id_projeto = removeAspas($_REQUEST['id_projeto']);
    $rh_sindicato = removeAspas($_REQUEST['rh_sindicato']);
    $status_admi = removeAspas($_REQUEST['status_admi']);
    $data_importacao = removeAspas(implode('-', array_reverse(explode('/', $_REQUEST['data_importacao']))));
    $radio_contribuicao = removeAspas($_REQUEST['radio_contribuicao']);
    $ano_contribuicao = removeAspas($_REQUEST['ano_contribuicao']);
    $id_curso = removeAspas($_REQUEST['id_curso']);
    $rh_horario = removeAspas($_REQUEST['rh_horario']);
    $nome = removeAspas($_REQUEST['nome']);
    $data_nasci = removeAspas(implode('-', array_reverse(explode('/', $_REQUEST['data_nasci']))));
    $uf_nasc = removeAspas($_REQUEST['uf_nasc']);
    $municipio_nasc = removeAspas($_REQUEST['municipio_nasc']);
    $id_municipio_nasc = removeAspas($_REQUEST['id_municipio_nasc']);
    $civil = explode('|',removeAspas($_REQUEST['civil']));
    $id_estado_civil = $civil[0];
    $civil = $civil[1];
    $sexo = removeAspas($_REQUEST['sexo']);
    $nacionalidade = removeAspas($_REQUEST['nacionalidade']);
    $dtChegadaPais = removeAspas(implode('-', array_reverse(explode('/', $_REQUEST['dtChegadaPais']))));
    $id_pais_nasc = removeAspas($_REQUEST['id_pais_nasc']);
    $id_pais_nacionalidade = removeAspas($_REQUEST['id_pais_nacionalidade']);
    $cep = removeAspas($_REQUEST['cep']);
    $endereco = removeAspas($_REQUEST['endereco']);
    $numero = removeAspas($_REQUEST['numero']);
    $bairro = removeAspas($_REQUEST['bairro']);
    $uf = removeAspas($_REQUEST['uf']);
    $cidade = removeAspas($_REQUEST['cidade']);
    $id_municipio_end = removeAspas($_REQUEST['id_municipio_end']);
    $complemento = removeAspas($_REQUEST['complemento']);
    $estuda = removeAspas($_REQUEST['estuda']);
    $data_escola = removeAspas(implode('-', array_reverse(explode('/', $_REQUEST['data_escola']))));
    $escolaridade = removeAspas($_REQUEST['escolaridade']);
    $curso = removeAspas($_REQUEST['curso']);
    $instituicao = removeAspas($_REQUEST['instituicao']);
    $tel_fixo = removeAspas($_REQUEST['tel_fixo']);
    $tel_cel = removeAspas($_REQUEST['tel_cel']);
    $tel_rec = removeAspas($_REQUEST['tel_rec']);
    $tipo_sanguineo = removeAspas($_REQUEST['tipo_sanguineo']);
    $email = removeAspas($_REQUEST['email']);
    $pai = removeAspas($_REQUEST['pai']);
    $nacionalidade_pai = removeAspas($_REQUEST['nacionalidade_pai']);
    $data_nasc_pai = removeAspas(implode('-', array_reverse(explode('/', $_REQUEST['data_nasc_pai']))));
    $mae = removeAspas($_REQUEST['mae']);
    $nacionalidade_mae = removeAspas($_REQUEST['nacionalidade_mae']);
    $data_nasc_mae = removeAspas(implode('-', array_reverse(explode('/', $_REQUEST['data_nasc_mae']))));
    $nome_conjuge = removeAspas($_REQUEST['nome_conjuge']);
    $data_nasc_conjuge = removeAspas(implode('-', array_reverse(explode('/', $_REQUEST['data_nasc_conjuge']))));
    $cabelos = removeAspas($_REQUEST['cabelos']);
    $transporte = ($_REQUEST['transporte']) ? 1 : 0;    
    $desconto_inss = ($_REQUEST['desconto_inss']) ? 1 : 0;
    $contrato_medico = ($_REQUEST['contrato_medico']) ? 1 : 0;
    $pensao_alimenticia = ($_REQUEST['pensao_alimenticia1'] || $_REQUEST['pensao_alimenticia2'] || 
                            $_REQUEST['pensao_alimenticia3'] || $_REQUEST['pensao_alimenticia4'] ||
                            $_REQUEST['pensao_alimenticia5']) ? 1 : 0;
            
    $seguro_desemprego = ($_REQUEST['seguro_desemprego']) ? 1 : 0;
    $ddir_pai = ($_REQUEST['ddir_pai']) ? 1 : 0;
    $ddir_mae = ($_REQUEST['ddir_mae']) ? 1 : 0;
    
    $ddir_conjuge = ($_REQUEST['ddir_conjuge']) ? 1 : 0;
    $tipo_desconto_inss = removeAspas($_REQUEST['tipo_desconto_inss']);
    $olhos = removeAspas($_REQUEST['olhos']);
    $peso = removeAspas($_REQUEST['peso']);
    $altura = removeAspas($_REQUEST['altura']);
    $defeito = removeAspas($_REQUEST['defeito']);
    $etnia = removeAspas($_REQUEST['etnia']);
    $deficiencia = removeAspas($_REQUEST['deficiencia']);
    $arquivo = removeAspas($_REQUEST['arquivo']);
    $rg = removeAspas($_REQUEST['rg']);
    $orgao = removeAspas($_REQUEST['orgao']);
    $uf_rg = removeAspas($_REQUEST['uf_rg']);
    $data_rg = removeAspas(implode('-', array_reverse(explode('/', $_REQUEST['data_rg']))));
    $cpf = removeAspas($_REQUEST['cpf']);
    $conselho = removeAspas($_REQUEST['conselho']);
    $data_emissao = removeAspas(implode('-', array_reverse(explode('/', $_REQUEST['data_emissao']))));
    $campo1 = removeAspas($_REQUEST['campo1']);
    $campo3 = $n_processo = $matricula;
    $serie_ctps = removeAspas($_REQUEST['serie_ctps']);
    $uf_ctps = removeAspas($_REQUEST['uf_ctps']);
    $data_ctps = removeAspas(implode('-', array_reverse(explode('/', $_REQUEST['data_ctps']))));
    $titulo = removeAspas($_REQUEST['titulo']);
    $zona = removeAspas($_REQUEST['zona']);
    $secao = removeAspas($_REQUEST['secao']);
    $pis = removeAspas($_REQUEST['pis']);
    $dada_pis = removeAspas(implode('-', array_reverse(explode('/', $_REQUEST['dada_pis']))));
    $fgts = removeAspas($_REQUEST['fgts']);
    $reservista = removeAspas($_REQUEST['reservista']);
    $carteira_sus = removeAspas($_REQUEST['carteira_sus']);
    $trabalha_outra_empresa = removeAspas($_REQUEST['trabalha_outra_empresa']);
    $cipa = removeAspas($_REQUEST['cipa']);
    $vale_refeicao = removeAspas($_REQUEST['vale_refeicao']);
    $vale_alimentacao = removeAspas($_REQUEST['vale_alimentacao']);
    $medica = removeAspas($_REQUEST['medica']);
    $id_plano_saude = removeAspas($_REQUEST['id_plano_saude']);
    $plano = removeAspas($_REQUEST['plano']);
    /* Linha vale*/
    $vale1 = removeAspas($_REQUEST['vale1']);
    $vale2 = removeAspas($_REQUEST['vale2']);
    $vale3 = removeAspas($_REQUEST['vale3']);
    $vale4 = removeAspas($_REQUEST['vale4']);
    $vale5 = removeAspas($_REQUEST['vale5']);
    //$vale6 = removeAspas($_REQUEST['vale6']);
    /* Tarifa vale */
    $valorvale1 = removeAspas($_REQUEST['vt_valor1']);
    $valorvale2 = removeAspas($_REQUEST['vt_valor2']);
    $valorvale3 = removeAspas($_REQUEST['vt_valor3']);
    $valorvale4 = removeAspas($_REQUEST['vt_valor4']);
    $valorvale5 = removeAspas($_REQUEST['vt_valor5']);
    //$valorvale6 = removeAspas($_REQUEST['valorvale6']);
    /* Quantidade viagens vale */
    $qtdvale1 = removeAspas($_REQUEST['vt_qtd1']);
    $qtdvale2 = removeAspas($_REQUEST['vt_qtd2']);
    $qtdvale3 = removeAspas($_REQUEST['vt_qtd3']);
    $qtdvale4 = removeAspas($_REQUEST['vt_qtd4']);
    $qtdvale5 = removeAspas($_REQUEST['vt_qtd5']);
    //$qtdvale6 = removeAspas($_REQUEST['vt_qtd6']);
    /* Número Cartão */
    $cartao1 = removeAspas($_REQUEST['vt_card1']);
    $cartao2 = removeAspas($_REQUEST['vt_card2']);
    $cartao3 = removeAspas($_REQUEST['vt_card3']);
    $cartao4 = removeAspas($_REQUEST['vt_card4']);
    $cartao5 = removeAspas($_REQUEST['vt_card5']);
    //$cartao6 = removeAspas($_REQUEST['vt_card6']);
    /*
    $cartao1 = removeAspas($_REQUEST['cartao1']);
    $cartao2 = removeAspas($_REQUEST['cartao2']);
     * 
     */
    $banco = removeAspas($_REQUEST['banco']);
    $agencia = removeAspas($_REQUEST['agencia']);
    $agencia_dv = removeAspas($_REQUEST['agencia_dv']);
    $conta = removeAspas($_REQUEST['conta']);
    $conta_dv = removeAspas($_REQUEST['conta_dv']);
    $tipo_conta = removeAspas($_REQUEST['tipo_conta']);
    $nome_banco = removeAspas($_REQUEST['nome_banco']);
    $data_entrada = removeAspas(implode('-', array_reverse(explode('/', $_REQUEST['data_entrada']))));
    $data_exame = removeAspas(implode('-', array_reverse(explode('/', $_REQUEST['data_exame']))));
    $localpagamento = removeAspas($_REQUEST['localpagamento']);
    //$localpagamento = '';
    $tipo_pagamento = removeAspas($_REQUEST['tipo_pagamento']);
    $prazoexp = removeAspas($_REQUEST['prazoExp']);
    $tipo_contrato = removeAspas($_REQUEST['tipo_contrato']);
    $obs = removeAspas($_REQUEST['observacoes']);
    $tipo_pagamento = removeAspas($_REQUEST['tipo_pagamento']);
    
    $validade_crt= removeAspas(implode('-', array_reverse(explode('/', $_REQUEST['validade_crt']))));
    $emissao_crt = removeAspas(implode('-', array_reverse(explode('/', $_REQUEST['emissao_crt']))));
    $num_crt = removeAspas($_REQUEST['num_crt']);
    $id_pde = isset($_REQUEST['id_pde']) ? 1 : 0;    
    $data_pde = removeAspas(implode('-', array_reverse(explode('/', $_REQUEST['data_pde']))));
    
    
//    print_array($_REQUEST);
}

if(isset($_REQUEST['salvar'])){    
    
    $insert = "INSERT INTO rh_clt (
    id_projeto,
    id_regiao,
    localpagamento,
    id_unidade,
    nome,
    sexo,
    endereco,
    numero,
    complemento,
    bairro,
    cidade,
    uf,
    cep,
    tel_fixo,
    tel_cel,
    tel_rec,
    data_nasci,
    nacionalidade,
    civil,
    rg,
    orgao,
    data_rg,
    cpf,
    conselho,
    titulo,
    zona,
    secao,
    pai,
    nacionalidade_pai,
    mae,
    nacionalidade_mae,
    estuda,
    data_escola,
    escolaridade,
    instituicao,
    curso,
    tipo_contratacao,
    banco,
    agencia,
    agencia_dv,
    conta,
    conta_dv,
    tipo_conta,
    id_curso,
    obs,
    data_entrada,
    campo1,
    campo2,
    campo3,
    data_exame,
    reservista,
    etnia,
    deficiencia,
    cabelos,
    altura,
    olhos,
    peso,
    defeito,
    cipa,
    plano,
    pis,
    dada_pis,
    data_ctps,
    serie_ctps,
    uf_ctps,
    uf_rg,
    fgts,
    transporte,
    medica,
    tipo_pagamento,
    nome_banco,
    observacao,
    data_cad,
    foto,
    rh_vinculo,
    rh_status,
    rh_horario,
    rh_sindicato,
    desconto_inss,
    tipo_desconto_inss,
    trabalha_outra_empresa,
    status_admi,
    status_reg,
    matricula,
    n_processo,
    contrato_medico,
    email,
    data_nasc_pai,
    data_nasc_mae,
    data_nasc_conjuge,
    nome_conjuge,
    municipio_nasc,
    uf_nasc,
    data_emissao,
    tipo_sanguineo,
    ano_contribuicao,
    dtChegadaPais,
    cod_pais_rais,
    tipo_contrato,
    prazoexp,
    id_estado_civil,
    id_municipio_nasc,
    id_municipio_end,
    id_pais_nasc,
    id_pais_nacionalidade,
    vale_refeicao,
    vale_alimentacao,
    pensao_alimenticia,
    id_setor,
    id_plano_saude,
    id_centro_custo,
    reintegracao,
    computar,
    carteira_sus,
    seguro_desemprego,
    imposto_renda,
    data_importacao,
    status,
    num_crt,
    emissao_crt,
    validade_crt,
    pde,
    data_pde
    ) VALUES (
    '{$id_projeto}',
    '{$id_regiao}',
    '{$localpagamento}',
    '{$_REQUEST['unidade'][1]['id_unidade']}',
    '{$nome}',
    '{$sexo}',
    '{$endereco}',
    '{$numero}',
    '{$complemento}',
    '{$bairro}',
    '{$cidade}',
    '{$uf}',
    '{$cep}',
    '{$tel_fixo}',
    '{$tel_cel}',
    '{$tel_rec}',
    '{$data_nasci}',
    '{$nacionalidade}',
    '{$civil}',
    '{$rg}',
    '{$orgao}',
    '{$data_rg}',
    '{$cpf}',
    '{$conselho}',
    '{$titulo}',
    '{$zona}',
    '{$secao}',
    '{$pai}',
    '{$nacionalidade_pai}',
    '{$mae}',
    '{$nacionalidade_mae}',
    '{$estuda}',
    '{$data_escola}',
    '{$escolaridade}',
    '{$instituicao}',
    '{$curso}',
    2,
    '{$banco}',
    '{$agencia}',
    '{$agencia_dv}',
    '{$conta}',
    '{$conta_dv}',
    '{$tipo_conta}',
    '{$id_curso}',
    '{$obs}',
    '{$data_entrada}',
    '{$campo1}',
    '{$campo2}',
    '{$campo3}',
    '{$data_exame}',
    '{$reservista}',
    '{$etnia}',
    '{$deficiencia}',
    '{$cabelos}',
    '{$altura}',
    '{$olhos}',
    '{$peso}',
    '{$defeito}',
    '{$cipa}',
    '{$plano}',
    '{$pis}',
    '{$dada_pis}',
    '{$data_ctps}',
    '{$serie_ctps}',
    '{$uf_ctps}',
    '{$uf_rg}',
    '{$fgts}',
    '{$transporte}',
    '{$medica}',
    '{$tipo_pagamento}',
    '{$nome_banco}',
    '{$observacao}',
    NOW(),
    '{$foto}',
    '{$rh_vinculo}',
    10,
    '{$rh_horario}',
    '{$rh_sindicato}',
    '{$desconto_inss}',
    '{$tipo_desconto_inss}',
    '{$trabalha_outra_empresa}',
    '{$status_admi}',
    1,
    '{$matricula}',
    '{$n_processo}',
    '{$contrato_medico}',
    '{$email}',
    '{$data_nasc_pai}',
    '{$data_nasc_mae}',
    '{$data_nasc_conjuge}',
    '{$nome_conjuge}',
    '{$municipio_nasc}',
    '{$uf_nasc}',
    '{$data_emissao}',
    '{$tipo_sanguineo}',
    '{$ano_contribuicao}',
    '{$dtChegadaPais}',
    '{$nacionalidade}',
    '{$tipo_contrato}',
    '{$prazoexp}',
    '{$id_estado_civil}',
    '{$id_municipio_nasc}',
    '{$id_municipio_end}',
    '{$id_pais_nasc}',
    '{$id_pais_nacionalidade}',
    '{$vale_refeicao}',
    '{$vale_alimentacao}',
    '{$pensao_alimenticia}',
    '{$id_setor}',
    '{$id_plano_saude}',
    '{$id_centro_custo}',
    '{$reintegracao}',
    '{$computar}',
    '{$carteira_sus}',
    '{$seguro_desemprego}',
    '{$imposto_renda}',
    '{$data_importacao}',
    10,
    '{$num_crt}',
    '{$emissao_crt}',
    '{$validade_crt}',
    '{$id_pde}',
    '{$data_pde}')";
    
    
    mysql_query($insert) or die("ERRO CADASTRO DE CLT: " . mysql_error());       
    $id_clt = mysql_insert_id();
    $log->log('2', "Cadastro do CLT: ID{$id_clt} - $nome",'rh_clt');
    
}else if(isset($_REQUEST['editar'])){
    
    $id_clt = $_REQUEST['id_clt'];
    
    $update = "UPDATE rh_clt SET
    id_projeto = '{$id_projeto}',
    id_regiao = '{$id_regiao}',
    localpagamento = '{$localpagamento}',
    id_unidade = '{$_REQUEST['unidade'][1]['id_unidade']}',
    nome = '{$nome}',
    sexo = '{$sexo}',
    endereco = '{$endereco}',
    numero = '{$numero}',
    complemento = '{$complemento}',
    bairro = '{$bairro}',
    cidade = '{$cidade}',
    uf = '{$uf}',
    cep = '{$cep}',
    tel_fixo = '{$tel_fixo}',
    tel_cel = '{$tel_cel}',
    tel_rec = '{$tel_rec}',
    data_nasci = '{$data_nasci}',
    nacionalidade = '{$nacionalidade}',
    civil = '{$civil}',
    rg = '{$rg}',
    orgao = '{$orgao}',
    data_rg = '{$data_rg}',
    cpf = '{$cpf}',
    conselho = '{$conselho}',
    titulo = '{$titulo}',
    zona = '{$zona}',
    secao = '{$secao}',
    pai = '{$pai}',
    nacionalidade_pai = '{$nacionalidade_pai}',
    mae = '{$mae}',
    nacionalidade_mae = '{$nacionalidade_mae}',
    estuda = '{$estuda}',
    data_escola = '{$data_escola}',
    escolaridade = '{$escolaridade}',
    instituicao = '{$instituicao}',
    curso = '{$curso}',
    tipo_contratacao = '2',
    banco = '{$banco}',
    agencia = '{$agencia}',
    agencia_dv = '{$agencia_dv}',
    conta = '{$conta}',
    conta_dv = '{$conta_dv}',
    tipo_conta = '{$tipo_conta}',
    id_curso = '{$id_curso}',
    obs = '{$obs}',
    data_entrada = '{$data_entrada}',
    campo1 = '{$campo1}',
    campo2 = '{$campo2}',
    data_exame = '{$data_exame}',
    reservista = '{$reservista}',
    etnia = '{$etnia}',
    deficiencia = '{$deficiencia}',
    cabelos = '{$cabelos}',
    altura = '{$altura}',
    olhos = '{$olhos}',
    peso = '{$peso}',
    defeito = '{$defeito}',
    cipa = '{$cipa}',
    plano = '{$plano}',
    pis = '{$pis}',
    dada_pis = '{$dada_pis}',
    data_ctps = '{$data_ctps}',
    serie_ctps = '{$serie_ctps}',
    uf_ctps = '{$uf_ctps}',
    uf_rg = '{$uf_rg}',
    fgts = '{$fgts}',
    transporte = '{$transporte}',
    medica = '{$medica}',
    tipo_pagamento = '{$tipo_pagamento}',
    nome_banco = '{$nome_banco}',
    observacao = '{$observacao}',
    foto = '{$foto}',
    rh_vinculo = '{$rh_vinculo}',
    rh_horario = '{$rh_horario}',
    rh_sindicato = '{$rh_sindicato}',
    desconto_inss = '{$desconto_inss}',
    tipo_desconto_inss = '{$tipo_desconto_inss}',
    trabalha_outra_empresa = '{$trabalha_outra_empresa}',
    status_admi = '{$status_admi}',
    contrato_medico = '{$contrato_medico}',
    email = '{$email}',
    data_nasc_pai = '{$data_nasc_pai}',
    data_nasc_mae = '{$data_nasc_mae}',
    data_nasc_conjuge = '{$data_nasc_conjuge}',
    nome_conjuge = '{$nome_conjuge}',
    municipio_nasc = '{$municipio_nasc}',
    uf_nasc = '{$uf_nasc}',
    data_emissao = '{$data_emissao}',
    tipo_sanguineo = '{$tipo_sanguineo}',
    ano_contribuicao = '{$ano_contribuicao}',
    dtChegadaPais = '{$dtChegadaPais}',
    cod_pais_rais = '{$nacionalidade}',
    tipo_contrato = '{$tipo_contrato}',
    prazoexp = '{$prazoexp}',
    id_estado_civil = '{$id_estado_civil}',
    id_municipio_nasc = '{$id_municipio_nasc}',
    id_municipio_end = '{$id_municipio_end}',
    id_pais_nasc = '{$id_pais_nasc}',
    id_pais_nacionalidade = '{$id_pais_nacionalidade}',
    vale_refeicao = '{$vale_refeicao}',
    vale_alimentacao = '{$vale_alimentacao}',
    pensao_alimenticia = '{$pensao_alimenticia}',
    id_setor = '{$id_setor}',
    id_plano_saude = '{$id_plano_saude}',
    id_centro_custo = '{$id_centro_custo}',
    reintegracao = '{$reintegracao}',
    computar = '{$computar}',
    carteira_sus = '{$carteira_sus}',
    seguro_desemprego = '{$seguro_desemprego}',
    imposto_renda = '{$imposto_renda}',
    data_importacao = '{$data_importacao}',
    num_crt = '{$num_crt}',
    emissao_crt = '{$emissao_crt}',
    validade_crt = '{$validade_crt}',
    pde = '{$id_pde}',
    data_pde = '{$data_pde}'
    WHERE id_clt = '{$id_clt}' LIMIT 1";
   // echo $update;exit;
    
    $antigo = $log->getLinha('rh_clt', $id_clt);
    mysql_query($update) or die("ERRO ALTERAÇÃO DE CLT: " . mysql_error());
    $novo = $log->getLinha('rh_clt', $id_clt);
    
    $log->log('2', "Editando o CLT: ID{$id_clt} - $nome",'rh_clt',$antigo, $novo);
}

if((isset($_REQUEST['salvar']) || isset($_REQUEST['editar'])) && !empty($id_clt)){    
    foreach ($_REQUEST['unidade'] as $key => $value) {
        $principal = ($key == 1) ? 1 : 0;
        $value['id_projeto'] = ($key == 1) ? $id_projeto : $value['id_projeto'];
        if($value['id_assoc']){
            //UPDATE
            $updateUnidade = "UPDATE rh_clt_unidades_assoc SET id_projeto = '{$value['id_projeto']}', id_unidade = '{$value['id_unidade']}', porcentagem = '{$value['porcentagem']}' WHERE id_assoc = '{$value['id_assoc']}'";
            $antigo = $log->getLinha('rh_clt_unidades_assoc', $value['id_assoc']);            
            $updateUnidade = mysql_query($updateUnidade) or die("ERRO ALTERAÇÃO DE UNIDADE: " . mysql_error());
            $novo = $log->getLinha('rh_clt_unidades_assoc', $value['id_assoc']);   
            $log->log('2', "Atualização unidade: ID{$value['id_unidade']} associada ao CLT: {$id_clt}",'rh_clt_unidades_assoc',$antigo,$novo);
        } else {
            //INSERT
            if($value['id_unidade']){
                $insetUnidade = "INSERT INTO rh_clt_unidades_assoc (id_clt, id_projeto, id_unidade, porcentagem, principal, status) VALUES ({$id_clt}, '{$value['id_projeto']}', '{$value['id_unidade']}', '{$value['porcentagem']}', '$principal', '1')";                
                $insetUnidade = mysql_query($insetUnidade) or die("ERRO INSERSÃO DE UNIDADE: " . mysql_error());                    
                $log->log('2', "Associação Unidade: ID{$value['id_unidade']} ao CLT: {$id_clt}",'rh_clt_unidades_assoc');
            }
        }
        ($insetUnidade) ? print_array($insetUnidade) : null;
        ($updateUnidade) ? print_array($updateUnidade) : null;
    }
    
   // var_dump($_REQUEST['favorecidos_pensao']);
   // exit;
    
    // *** NEW FAVORECIDOS ***
    
    //array com favs da base
    $sqlFavorecidos = mysql_query("SELECT * FROM favorecido_pensao_assoc WHERE id_clt = {$id_clt}") or die(mysql_error());
    // Correcao pra indices
    $fav = 1;
    while ($rowFavorecidos = mysql_fetch_assoc($sqlFavorecidos)) {
        //ATENCAO indice pos incrementado
        $arrayFavorecidos[$fav++] = $rowFavorecidos;
    }    
    
    $favorecidos = $_REQUEST['favorecidos_pensao'];
    $dependentefav = $_REQUEST['dependente'];
    
    //echo '<pre>' . var_export($arrayFavorecidos, true) . '</pre>';
    //echo count($arrayFavorecidos);
         
    for($i=1; $i<=6; $i++){        
        $favbase= 0;        
        if($favorecidos[$i]['favorecido'] != ""){            
            //conferir se o favorecido ja está na base pelo nome
            //loop pelos favorecidos da base
            for($j=1; $j<=count($arrayFavorecidos); $j++){
                if ($favorecidos[$i]['favorecido'] == $arrayFavorecidos[$j]['favorecido']){
                    //está na base
                    $favbase = 1;
                    $posFav = $j;
                }
            }            
            
            if($favbase ==1){
                //***** update****
                echo $dependentefav[$i]['cpf']."<br>";
                $sqlUpdateFav = "UPDATE favorecido_pensao_assoc SET cpf = '{$dependentefav[$i]['cpf']}', favorecido = '{$favorecidos[$i]['favorecido']}', id_lista_banco = '{$favorecidos[$i]['id_lista_banco']}', agencia = '{$favorecidos[$i]['agencia']}', conta = '{$favorecidos[$i]['conta']}', aliquota = '{$favorecidos[$i]['aliquota']}', oficio = '{$favorecidos[$i]['oficio']}' WHERE id = '{$arrayFavorecidos[$posFav]['id']}'";
                //echo "Update Favorecido {$favorecidos[$i]['favorecido']} <br>";                
                mysql_query($sqlUpdateFav) or die("Erro ao atualizar favorecido");                
            }
            else{
                //**** insert ****  
                $sqlInsertFav = "INSERT INTO favorecido_pensao_assoc (id_clt,favorecido,cpf,aliquota,id_lista_banco,agencia,conta,oficio) VALUES ('{$id_clt}','{$favorecidos[$i]['favorecido']}','{$dependentefav[$i]['cpf']}','{$favorecidos[$i]['aliquota']}','{$favorecidos[$i]['id_lista_banco']}','{$favorecidos[$i]['agencia']}','{$favorecidos[$i]['conta']}','{$favorecidos[$i]['oficio']}')";
                //echo "Insert Favorecido {$favorecidos[$i]['favorecido']} <br>";         
                mysql_query($sqlInsertFav) or die("Erro ao inserir favorecido");
            }
        }
    }
    
    
                // *** OLD INSERT FAVORECIDOS **** 
                
//    $insertFavorecidos = "INSERT INTO favorecido_pensao_assoc (id_clt,favorecido,cpf,aliquota,id_lista_banco,agencia,conta,oficio) VALUES ";
//    
//    for ($i = 1; $i <= count($_REQUEST['favorecidos_pensao']); $i++) { 
//        //echo '<pre>' . var_export($_REQUEST['favorecidos_pensao'][$i]['favorecido'], true) . '</pre>';
//        if(!empty($_REQUEST['favorecidos_pensao'][$i]['favorecido'])){ 
//            
//            $favorecido = $_REQUEST['favorecidos_pensao'][$i];            
//            if(!empty($favorecido['id'])){
//                 
//                 $updateFavorecido = "UPDATE favorecido_pensao_assoc SET cpf = '{$favorecido['cpf']}', favorecido = '{$favorecido['favorecido']}', id_lista_banco = '{$favorecido['id_lista_banco']}', agencia = '{$favorecido['agencia']}', conta = '{$favorecido['conta']}', aliquota = '{$favorecido['aliquota']}', oficio = '{$favorecido['oficio']}' WHERE id = '{$favorecido['id']}'";
//                 
//                 $antigo = $log->getLinha('favorecido_pensao_assoc', $favorecido['id']); 
//                 $updateFavorecido = mysql_query($updateFavorecido) or die("ERRO ALTERAÇÃO DE FAVORECIDO: " . mysql_error());
//                 $novo = $log->getLinha('favorecido_pensao_assoc', $favorecido['id']); 
//                 $log->log('2', "Atualização do favorecido: ID{$favorecido['id']} associado ao CLT: {$id_clt}",'favorecido_pensao_assoc',$antigo,$novo);
//                 // Evitando que o insert seja executado mais abaixo
//                // $insertFavorecidos = isset($insertFavorecidos) ? null : null;                 
//            }else{
//                if(isset($_REQUEST['pensao_alimenticia'.$i])){
//                    $insertFavorecidosValues .= "('{$id_clt}','{$favorecido['favorecido']}','{$favorecido['cpf']}','{$favorecido['aliquota']}','{$favorecido['id_lista_banco']}','{$favorecido['agencia']}','{$favorecido['conta']}','{$favorecido['oficio']}'),"; 
//                    //'{$value['favorecido']}', '{$value['id_lista_banco']}', '{$value['agencia']}', '{$value['conta']}', '{$value['aliquota']}', '{$value['oficio'
//                }                
//            }
//        }
//        
//    }
//  //  die($insertFavorecidos);
//    if(isset($insertFavorecidosValues)){
//            $insertFavorecidos .= substr($insertFavorecidosValues, 0, -1);
//      //     die($insertFavorecidos); 
//            mysql_query($insertFavorecidos) or die("ERRO INSERÇÃO DE FAVORECIDO: " . mysql_error());            
//            $log->log('2', "Inclusão do favorecido: {$favorecido['favorecido']}, associado ao CLT: {$id_clt}",'favorecido_pensao_assoc');
//    }
    /*
    foreach ($_REQUEST['favorecidos_pensao'] as $key => $value) {
        if($value['id']){
            //UPDATE
            $updateFavorecido = "UPDATE favorecido_pensao_assoc SET cpf = '{$value['cpf']}', favorecido = '{$value['favorecido']}', id_lista_banco = '{$value['id_lista_banco']}', agencia = '{$value['agencia']}', conta = '{$value['conta']}', aliquota = '{$value['aliquota']}', oficio = '{$value['oficio']}' WHERE id = '{$value['id']}'";
            $updateFavorecido = mysql_query($updateFavorecido) or die("ERRO ALTERAÇÃO DE FAVORECIDO: " . mysql_error());
        } else {
            //INSERT
            $insetFavorecido = "INSERT INTO favorecido_pensao_assoc (id_clt, cpf, favorecido, id_lista_banco, agencia, conta, aliquota, oficio) VALUES ({$id_clt}, '{$value['cpf']}', '{$value['favorecido']}', '{$value['id_lista_banco']}', '{$value['agencia']}', '{$value['conta']}', '{$value['aliquota']}', '{$value['oficio']}')";
            $insetFavorecido = mysql_query($insetFavorecido) or die("ERRO INSERÇÃO DE FAVORECIDO: " . mysql_error());
        }
//        ($insetFavorecido) ? print_array($insetFavorecido) : null;
//        ($updateFavorecido) ? print_array($updateFavorecido) : null;
    }*/
    
         

    
//    if($_REQUEST['dependente']){
        
        $d = $_REQUEST['dependente'];        
        unset($d[0]);        
        
        $d['nome'] = $nome;
        $d['contratacao'] = 2;
        $d['id_regiao'] = $id_regiao;
        $d['id_projeto'] = $id_projeto;
        $d['id_clt'] = $id_clt;
        $d['ddir_pai'] = $ddir_pai;
        $d['ddir_mae'] = $ddir_mae;
        $d['ddir_conjuge'] = $ddir_conjuge;
        for ($i = 1; $i <= 6; $i++) {
            $d['nome'.$i] = removeAspas($d[$i]['nome']);
            $d['cpf'.$i] = $d[$i]['cpf'];
            $d['nao_ir_filho'.$i] = $d[$i]['nao_ir_filho'];
            $d['data'.$i] = implode('-', array_reverse(explode('/', $d[$i]['data_nasc'])));
            $d['portador_def'.$i] = $d[$i]['deficiencia'];
            $d["dep{$i}_cur_fac_ou_tec"] = $d[$i]['fac_tec'];
            
            unset($d[$i]);
        }        
//        print_array($d);exit;
        $keys = implode(', ', array_keys($d));
        $values = implode("' , '", $d);        
        if(!empty($d['id_dependentes'])){
            //UPDATE
            foreach ($d as $key => $value) {
                $camposUpdate[] = "$key = '$value'";
            }
            $updateDependente = "UPDATE dependentes SET " . implode(", ",($camposUpdate)) ." WHERE id_dependentes = {$_REQUEST['dependente']['id_dependentes']} LIMIT 1;";
            $antigo = $log->getLinha('dependentes', $_REQUEST['dependente']['id_dependentes']);
            $updateDependente = mysql_query($updateDependente) or die("ERRO ALTERAÇÃO DE DEPENDENTES: " . mysql_error());
            $novo = $log->getLinha('dependentes', $_REQUEST['dependente']['id_dependentes']);
            $log->log('2', "Atualização dos dependendes: ID{$_REQUEST['dependente']['id_dependentes']} associado ao CLT: {$id_clt}",'dependentes',$antigo,$novo);
        } else {
            //INSERT
            $insetDependente = "INSERT INTO dependentes ($keys) VALUES ('$values');"; 
            $insetDependente = mysql_query($insetDependente) or die("ERRO CADASTRO DE DEPENDENTES: " . mysql_error());            
            $log->log('2', "Inclusão de dependendes associados ao CLT: {$id_clt}",'dependentes');
        }
//        ($insetDependente) ? print_array($insetDependente) : null;
//        ($updateDependente) ? print_array($
//        updateDependente) : null;
//    }
        
        // ***
        //  NEW INSERT DEPENDENTES
        // ***        
        //  
        //puxar uma array com todos os dependentes da base com indices de 1 a 6
        $valuesDependentes ="";
        $insertFlag = 0;
        $sql = "select * from dependente where id_clt={$id_clt} and parentesco= 3";
        $result = mysql_query($sql);
        $dep =1;
        //gera a array
        while ($dependentesbd = mysql_fetch_array($result)){
            //ARRAY COM OS FILHOS DEPENDENTES DA BASE
            $arraynewdep[$dep++] = $dependentesbd;
        } 
        $countarray = count($arraynewdep);         
        //recupera valores digitados no form
        $newdep = $_REQUEST['dependente'];  
        
        for ($i = 1; $i <= 6; $i++) {
            $base = 0;
            $nomedep = $newdep[$i]['nome'];            
            if($nomedep != ""){
                for($j=1; $j<=$countarray; $j++){
                    //se nome do input for diferente do nome na base
                    if($nomedep == $arraynewdep[$j]['nome']){                      
                        $base = 1;
                    }            
                }                
                    $ddirform = 0;
                    $deficienciaform = 0;
                    $fac_tecform = 0;
                    $ddirform = $newdep[$i]['nao_ir_filho'];                                       
                    if ($ddirform == 1) {
                        $ddirfilho = 0;
                    }
                    else{
                        $ddirfilho = 1;
                    }
                    if($newdep[$i]['deficiencia'] == 1){
                        $deficienciaform = 1;
                    }
                    if($newdep[$i]['fac_tec'] == 1){
                        $fac_tecform = 1;
                    }
                    $data_filhoz = removeAspas(implode('-', array_reverse(explode('/', $newdep[$i]['data_nasc']))));
                //ja se encontra na base ? 0= nao 1= sim                
                if($base == 0 && $i>$countarray){
                    //  *** INSERIR  **    
                    $insertFlag = 1;
                    $valuesDependentes .= "(3, {$id_clt}, '{$newdep[$i]['nome']}', '{$newdep[$i]['cpf']}', '{$data_filhoz}', {$deficienciaform}, {$ddirfilho}, {$fac_tecform}),";
                    
                } else{
                    //  *** UPDATE  ** 
                    $sqlUpdate = "UPDATE dependente set nome = '{$newdep[$i]['nome']}' , cpf = '{$newdep[$i]['cpf']}' , data_nasc = '{$data_filhoz}', deficiencia = {$deficienciaform} , ddir = {$ddirfilho} , fac_tec = {$fac_tecform} where id_dependente = {$arraynewdep[$i]['id_dependente']}";                    
                    mysql_query($sqlUpdate) or die('Erro no update de dependentes');
                }
            }
            else{
                //nome nao declarado no form
            }
        } 
              
        $incluirDependentes = "INSERT INTO dependente(parentesco, id_clt, nome, cpf, data_nasc, deficiencia, ddir, fac_tec) VALUES ";
        //incluir PAI, MÃE, CONJUGE, FILHOS. INDEPENDENTE DE SER DEPENTENDE OU NÃO
        $paiBD = 0;
        $maeBD = 0;
        $conjugeBD=0;
        //verificar no bd se já tem pai setado
        $sqlPai = "SELECT * FROM dependente WHERE id_clt={$id_clt} and parentesco = 1";
        $resultPai = mysql_query($sqlPai);
        if (mysql_num_rows($resultPai) == 1) {
            $rowPai = mysql_fetch_array($resultPai);
            $idPai = $rowPai['id_dependente'];            
            $paiBD = 1;
        }
        //verificar no bd se já tem mea setado
        $sqlMae = "SELECT * FROM dependente WHERE id_clt={$id_clt} and parentesco = 2";
        $resultMae = mysql_query($sqlMae);
        if (mysql_num_rows($resultMae) == 1) {
            $rowMae = mysql_fetch_array($resultMae);
            $idMae = $rowMae['id_dependente'];            
            $maeBD = 1;
        }        
        //verificar no bd se já tem conjuge setado
        $sqlConjuge = "SELECT * FROM dependente WHERE id_clt={$id_clt} and parentesco = 4";
        $resultConjuge = mysql_query($sqlConjuge);
        if (mysql_num_rows($resultConjuge) == 1) {
            $rowConjuge = mysql_fetch_array($resultConjuge);
            $idConjuge = $rowConjuge['id_dependente']; 
            $conjugeBD = 1;
        }        
        
        //pai
        if ($pai != "" && $paiBD == 0){                        
            $valuesDependentes .= "(1, {$id_clt}, '{$pai}', '{$cpf_pai}', '{$data_nasc_pai}', null, {$ddir_pai}, null),";  
            $insertFlag = 1;
        } else{
            $sqlUpdatePai = "UPDATE dependente SET nome = '{$pai}' , cpf ='{$cpf_pai}', data_nasc = '{$data_nasc_pai}', ddir = {$ddir_pai} where id_dependente = {$idPai}";
            mysql_query($sqlUpdatePai) or die("Falha update Pai");
        }
        
        if($mae != "" && $maeBD == 0){
            $insertFlag = 1;
            $valuesDependentes .= "(2, {$id_clt}, '{$mae}', '{$cpf_mae}', '{$data_nasc_mae}', null, {$ddir_mae}, null),";
        } else{
            $sqlUpdateMae = "UPDATE dependente SET nome = '{$mae}' , cpf ='{$cpf_mae}', data_nasc = '{$data_nasc_mae}', ddir = {$ddir_mae} where id_dependente = {$idMae}";
            mysql_query($sqlUpdateMae) or die("Falha UPDATE mãe");            
        }
        
        if($nome_conjuge != "" && $conjugeBD == 0){
            $insertFlag = 1;
            $valuesDependentes .= "(4, {$id_clt}, '{$nome_conjuge}', '{$cpf_conjuge}', '{$data_nasc_conjuge}', null, {$ddir_conjuge}, null),";
        }else{
            $sqlUpdateMae = "UPDATE dependente SET nome = '{$nome_conjuge}' , cpf ='{$cpf_conjuge}', data_nasc = '{$data_nasc_conjuge}', ddir = {$ddir_conjuge} where id_dependente = {$idConjuge}";
            mysql_query($sqlUpdateMae) or die("Falha UPDATE mãe");            
        }
       if ($insertFlag == 1){
            $finalSQL = $incluirDependentes.$valuesDependentes;
            $finalSQL = substr($finalSQL, 0, -1);
            mysql_query($finalSQL) or die('falha ao incluir dependentes :'.mysql_error()); 
       }
       
       
       
       //  ****** 
       //  TRANSPORTE
       //   *******
    
    if ($transporte) { 
        if(isset($_REQUEST['editar'])){            
            //$updateVale = "UPDATE rh_vale SET id_tarifa1 = '$vale1', id_tarifa2 = '$vale2', id_tarifa3 = '$vale3', id_tarifa4 = '$vale4', id_tarifa5 = '$vale5', id_tarifa6 = '$vale6', cartao1 = '$cartao1', cartao2 = '$cartao2',  status_reg = '$status_reg' WHERE id_clt = '$id_clt'";
              $updateVale = "UPDATE rh_vt_valores_assoc SET 
                      id_valor1 = '$valorvale1', id_valor2 = '$valorvale2', id_valor3 = '$valorvale3', 
                      id_valor4 = '$valorvale4', id_valor5 = '$valorvale5',
                      qtd1 = '$qtdvale1', qtd2 = '$qtdvale2',qtd3 = '$qtdvale3',
                      qtd4 = '$qtdvale4', qtd5 = '$qtdvale5',
                      id_linha1 = '$vale1', id_linha2 = '$vale2', id_linha3 = '$vale3',
                      id_linha4 = '$vale4', id_linha5 = '$vale5',
                      cartao1 = '$cartao1', cartao2 = '$cartao2', cartao3 = '$cartao3',
                      cartao4 = '$cartao4', cartao5 = '$cartao5',
                      status_reg = 1 WHERE id_clt = '$id_clt'";              
                
                mysql_query($updateVale) or die("VT ATUALIZAÇÃO: " . mysql_error());               
        } else {
            $sqlvt = "INSERT INTO rh_vt_valores_assoc (id_clt, id_valor1, id_valor2, id_valor3, id_valor4, id_valor5,
                      qtd1, qtd2, qtd3, qtd4, qtd5,id_linha1,id_linha2,id_linha3,id_linha4,id_linha5,cartao1,cartao2,cartao3,cartao4,cartao5) VALUES
                ('$id_clt','$valorvale1','$valorvale2','$valorvale3','$valorvale4', '$valorvale5', 
                '$qtdvale1','$qtdvale2','$qtdvale3', '$qtdvale4', '$qtdvale5','$vale1','$vale2','$vale3','$vale4','$vale5',
                '$cartao1','$cartao2','$cartao3','$cartao4','$cartao5')";
            mysql_query($sqlvt) or die("$mensagem_erro - 2.3<br><br>" . mysql_error());
            //mysql_query($insertVale) or die("VT CADASTRO: " . mysql_error());
            $log->log('2', "Inclusão vales-transporte associados ao CLT: {$id_clt}",'rh_vt_valores_assoc');
        }
    }
    /**
     * DESCONTO OUTRA EMPRESA
     */
    foreach ($_REQUEST['outra_empresa'] as $key => $value) { 
        $objInssOutrasEmpresas->setDefault();
        if($value['id_inss']){
            $objInssOutrasEmpresas->setIdInss($value['id_inss']);
            $objInssOutrasEmpresas->getByIdInss();
            $objInssOutrasEmpresas->getRow();
        }

        $objInssOutrasEmpresas->setSalario(str_replace(',', '.', str_replace('.', '', $value['salario'])));
        $objInssOutrasEmpresas->setDesconto(str_replace(',', '.', str_replace('.', '', $value['desconto'])));
        $objInssOutrasEmpresas->setInicio(implode('-', array_reverse(explode('/',$value['inicio']))));
        $objInssOutrasEmpresas->setFim(implode('-', array_reverse(explode('/',$value['fim']))));

        if($value['id_inss']){
            if(!$objInssOutrasEmpresas->update()){
                 die("ERRO ALTERAÇÃO DE INSS OUTRA EMPRESA: " . mysql_error());
            }
        } else {
            $objInssOutrasEmpresas->setIdClt($id_clt);
            $objInssOutrasEmpresas->setStatus(1);
            $objInssOutrasEmpresas->setDataCad(date('Y-m-d'));
            if(!$objInssOutrasEmpresas->insert()){
                die("ERRO CADASTRO DE INSS OUTRA EMPRESA: " . mysql_error());
            }
        }
    }
    
    //FAZENDO O UPLOAD DA FOTO
    //die(var_dump($_FILES['arquivo']));
    $arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;
    //if ($arquivo == '1') {
        if (!$arquivo) {
            $mensagem = "Não acesse esse arquivo diretamente!";
        }else {
            $nome_arq = str_replace(" ", "_", $nome);
            $tipo_arquivo = ".gif";
            // Resolvendo o nome e para onde o arquivo será movido
            $diretorio = "../fotosclt/";
            $nome_tmp = $id_regiao . "_" . $id_projeto . "_" . $id_clt . $tipo_arquivo;
            $nome_arquivo = "$diretorio$nome_tmp";

           $isUploaded =  move_uploaded_file($arquivo['tmp_name'], $nome_arquivo); // or die("Erro ao enviar o Arquivo: $nome_arquivo");
           if(!$isUploaded){
               echo "Falha ao enviar arquivo";
           }
        }
    //}
    
//    header("Location: ver_clt.php?reg=$id_regiao&clt=$id_clt&ant=0&pro=$id_projeto&pagina=bol");
    header("Location: alter_clt_final.php?clt=$id_clt&pro=$id_projeto&regiao={$id_regiao}");
    exit;
}

/**
 * *****************************************************************************
 * **********************FIM EDIÇAO E CADASTRO**********************************
 * *****************************************************************************
 */

/* Verificação de CPF */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'verificaCpf') {
    $query = "SELECT A.id_clt,A.nome,B.nome AS projeto, B.id_projeto, C.especifica
                FROM rh_clt AS A
                LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
                LEFT JOIN rhstatus AS C ON(A.`status` = C.codigo)
                WHERE A.cpf = '{$_REQUEST['cpf']}' AND (A.`status` != 10 || A.status != 200 || A.status != 40) AND B.id_master = {$usuario['id_master']};";
    $sql = mysql_query($query) or die('Erro ao selecionar ex funcionario');
    $dados = array("status" => 0);
    $d = array();
    if (mysql_num_rows($sql)) {
        while ($linha = mysql_fetch_assoc($sql)) {
            $d['id'] = $linha['id_clt'];
            $d['nome'] = $linha['nome'];
            $d['projeto'] = $linha['projeto'];
            $d['idprojeto'] = $linha['id_projeto'];
            $d['status'] = $linha['especifica'];
        }
        $dados = array("status" => 1, "dados" => $d);
    }
    echo json_encode($dados);
    exit();
}

/* Monta horários */
if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'horarios'){
    $qr_horarios = mysql_query("SELECT id_horario, nome, entrada_1, saida_1, entrada_2, saida_2 FROM rh_horarios WHERE horas_semanais = (SELECT hora_semana FROM curso WHERE id_curso = '{$_REQUEST['id']}')");
    $verifica_horario = mysql_num_rows($qr_horarios);
    if (!empty($verifica_horario)) {
        $array[$row_horarios['id_horario']] = "-- Selecione --";
        while ($row_horarios = mysql_fetch_array($qr_horarios)) {
//            $auxHorario = ($idHorario == $row_horarios['id_horario']) ? ", selected: 'selected' " : null;
              $array[$row_horarios['id_horario']] = "{$row_horarios['id_horario']} - {$row_horarios['nome']} ( {$row_horarios['entrada_1']} - {$row_horarios['saida_1']} - {$row_horarios['entrada_2']} - {$row_horarios['saida_2']} )";
        }

        $html = montaSelect($array, $_REQUEST['rh_horario'], "class='form-control validate[required]' id='rh_horario' name='rh_horario'");
    } else {
        $html = '<a href="../rh_novaintra/curso" target="_blank"><label style=" cursor: default; cursor: pointer; ">Clique aqui para cadastrar um hor&aacute;rio</label></a>';
    }
    echo $html;
    exit;
}

/* Seleciona Unidades  */
if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'unidades'){
    $sqlUnidades = mysql_query("SELECT id_unidade, unidade FROM unidade WHERE campo1 = {$_REQUEST['id_projeto']} ORDER BY unidade");
    $arrayUnidades = array("" => "-- SELECIONE --");
    while ($rowUnidades = mysql_fetch_assoc($sqlUnidades)) {
        $arrayUnidades[$rowUnidades['id_unidade']] = $rowUnidades['id_unidade'] . " - " . utf8_encode($rowUnidades['unidade']);
    }
    echo montaSelect($arrayUnidades, $_REQUEST['id_unidade'], "class='form-control validate[required]' id='id_unidade' name='unidade[{$_REQUEST['ordem']}][id_unidade]'");
    exit;
}

//criação da array de setores
$setorObj = new SetorClass();
$setorObj->getSetor();
$arraySetor[''] = '--Selecione o Setor--';
while ($setorObj->getRowSetor()) {
    $arraySetor[$setorObj->getIdSetor()] = $setorObj->getNome();
}

/* Seleciona valores para linhas de onibus */
if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'valLinha'){
    $linha = $_REQUEST['linha'];
    $sql = "SELECT valor_tarifa FROM rh_vt_linha WHERE id_vt_linha = '$linha'";
    $query = mysql_query($sql);
    $row = mysql_fetch_array($query);
    echo json_encode($row[0]);
    exit;
}

$id_clt = (!empty($_REQUEST['clt'])) ? $_REQUEST['clt'] : null;

$action = 'salvar';

if($id_clt){    
    $action = 'editar';            
    $sqlClt = "
    SELECT A.*, B.pais AS pais_nasc, C.pais AS pais_nacionalidade 
    FROM rh_clt A 
    LEFT JOIN paises AS B ON (A.id_pais_nasc = B.id_pais)
    LEFT JOIN paises AS C ON (A.id_pais_nacionalidade = C.id_pais)
    WHERE A.id_clt = '{$id_clt}'
    LIMIT 1";
    //select CLT
    $qryClt = mysql_query($sqlClt);
    $rowClt = mysql_fetch_assoc($qryClt);
   // var_dump($rowClt);
   // die();   
    
    //select do sindicato
    $sqlSindicato = "SELECT id_sindicato FROM rhsindicato WHERE id_sindicato = '{$rowClt['rh_sindicato']}' LIMIT 1";
    $qrySindicato = mysql_query($sqlSindicato);
    $rowSindicato = mysql_fetch_assoc($qrySindicato);    
    /**
     * SELECIONA OS FAVORECIDOS
     */
//    $sqlFavorecidos = mysql_query("SELECT * FROM favorecido_pensao_assoc WHERE id_clt = {$id_clt}") or die(mysql_error());
//    // Correcao pra indices
//    $fav = 1;
//    while ($rowFavorecidos = mysql_fetch_assoc($sqlFavorecidos)) {
//        //ATENCAO indice pos incrementado
//        $arrayFavorecidos[$fav++] = $rowFavorecidos;
//    }
    
//    echo '<pre>' . var_export($arrayFavorecidos, true) . '</pre>';
//    exit;
    //(var_dump($arrayFavorecidos))
    /**
     * SELECIONA OS SALARIO E DESCONTOS EM OUTRAS EMPRESAS
     */
    $sqlInssOutrasEmpresas = mysql_query("SELECT * FROM rh_inss_outras_empresas WHERE id_clt = {$id_clt}") or die(mysql_error());
    while ($rowInssOutrasEmpresas = mysql_fetch_assoc($sqlInssOutrasEmpresas)) {
        $arrayInssOutrasEmpresas[] = $rowInssOutrasEmpresas;
    }
    // ** SELEÇÃO DA NOVA 
    // TABELA DE DEPENDENTES ***
        //mae
        $sqlMae = "SELECT * FROM dependente WHERE id_clt={$id_clt} and parentesco = 2";
        $resultMae = mysql_query($sqlMae);
        $rowMae = mysql_fetch_array($resultMae);
        $cpfMae = $rowMae['cpf'];        
        
        //pai
        $sqlPai = "SELECT * FROM dependente WHERE id_clt={$id_clt} and parentesco = 1";
        $resultPai = mysql_query($sqlPai);       
        $rowPai = mysql_fetch_array($resultPai);
        $cpfPai = $rowPai['cpf'];  
        
        //conjuge
        $sqlConjuge = "SELECT * FROM dependente WHERE id_clt={$id_clt} and parentesco = 4";
        $resultConjuge = mysql_query($sqlConjuge);       
        $rowConjuge = mysql_fetch_array($resultConjuge);
        $cpfConjuge = $rowConjuge['cpf'];  
    
    /**
     * SELECIONA OS DEPENDENTES
     */
    $sqlDependentes = mysql_query("SELECT * FROM dependentes WHERE (id_bolsista = '$id_clt' OR id_clt = '$id_clt')  AND id_projeto = '{$rowClt['id_projeto']}' AND contratacao = '{$rowClt['tipo_contratacao']}' LIMIT 1") or die("ERRO DEPENDENTE 1: " . mysql_error());
    while ($rowDependentes = mysql_fetch_assoc($sqlDependentes)) {        
//        print_array($rowDependentes);
        $arrayDependentes['id_dependentes'] = $rowDependentes['id_dependentes'];
        if ($rowDependentes['nome1']) {
            $arrayDependentes[1] = array('nome' => $rowDependentes['nome1'], 'data' => $rowDependentes['data1'], 'cpf' => $rowDependentes['cpf1'], 'deficiencia' => $rowDependentes['portador_def1'], 'fac_tec' => $rowDependentes['dep1_cur_fac_ou_tec'], 'nao_ir_filho' => $rowDependentes['nao_ir_filho1']);
        }
        if ($rowDependentes['nome2']) {
            $arrayDependentes[2] = array('nome' => $rowDependentes['nome2'], 'data' => $rowDependentes['data2'], 'cpf' => $rowDependentes['cpf2'], 'deficiencia' => $rowDependentes['portador_def2'], 'fac_tec' => $rowDependentes['dep2_cur_fac_ou_tec'], 'nao_ir_filho' => $rowDependentes['nao_ir_filho2']);
        }
        if ($rowDependentes['nome3']) {
            $arrayDependentes[3] = array('nome' => $rowDependentes['nome3'], 'data' => $rowDependentes['data3'], 'cpf' => $rowDependentes['cpf3'], 'deficiencia' => $rowDependentes['portador_def3'], 'fac_tec' => $rowDependentes['dep3_cur_fac_ou_tec'], 'nao_ir_filho' => $rowDependentes['nao_ir_filho3']);
        }
        if ($rowDependentes['nome4']) {
            $arrayDependentes[4] = array('nome' => $rowDependentes['nome4'], 'data' => $rowDependentes['data4'], 'cpf' => $rowDependentes['cpf4'], 'deficiencia' => $rowDependentes['portador_def4'], 'fac_tec' => $rowDependentes['dep4_cur_fac_ou_tec'], 'nao_ir_filho' => $rowDependentes['nao_ir_filho4']);
        }
        if ($rowDependentes['nome5']) {
            $arrayDependentes[5] = array('nome' => $rowDependentes['nome5'], 'data' => $rowDependentes['data5'], 'cpf' => $rowDependentes['cpf5'], 'deficiencia' => $rowDependentes['portador_def5'], 'fac_tec' => $rowDependentes['dep5_cur_fac_ou_tec'], 'nao_ir_filho' => $rowDependentes['nao_ir_filho5']);
        }
        if ($rowDependentes['nome6']) {
            $arrayDependentes[6] = array('nome' => $rowDependentes['nome6'], 'data' => $rowDependentes['data6'], 'cpf' => $rowDependentes['cpf6'], 'deficiencia' => $rowDependentes['portador_def6'], 'fac_tec' => $rowDependentes['dep6_cur_fac_ou_tec'], 'nao_ir_filho' => $rowDependentes['nao_ir_filho6']);
        }
        if ($rowDependentes['ddir_pai']) {
            $arrayDependentes['ddir_pai'] = $rowDependentes['ddir_pai'];
        }
        if ($rowDependentes['ddir_mae']) {
            $arrayDependentes['ddir_mae'] = $rowDependentes['ddir_mae'];
        }
        if ($rowDependentes['ddir_conjuge']) {
            $arrayDependentes['ddir_conjuge'] = $rowDependentes['ddir_conjuge'];
        }
    }
//    print_array($arrayDependentes);
    /**
     * SELECIONA OS UNIDADES DO CLT
     */
    $sqlUnidadesClt = mysql_query("SELECT * FROM rh_clt_unidades_assoc WHERE id_clt = '{$id_clt}' ORDER BY principal DESC, id_assoc ASC") or die("ERRO UNIDADES CLT: " . mysql_error());
    while ($rowUnidadesClt = mysql_fetch_assoc($sqlUnidadesClt)) {
        $arrayUnidadesClt[] = $rowUnidadesClt;
    }   
    
    /**
     * SELECIONA OS VALES TRANSPORTES DO CLT
     */
    $sqlValeTransporte = mysql_query("SELECT id_valor_assoc,id_clt, id_valor1, id_valor2, id_valor3, id_valor4, id_valor5,
                        qtd1, qtd2, qtd3, qtd4, qtd5,id_linha1,id_linha2,id_linha3,id_linha4,id_linha5,cartao1,cartao2,cartao3,cartao4,cartao5 FROM rh_vt_valores_assoc WHERE id_clt = '{$id_clt}' AND status_reg = 1 LIMIT 1;") or die("ERRO VT CLT: " . mysql_error());
    //rh_vt_valores_assoc()
    $arrayValeTransporte = array();
    $dep = 1;
    while($rowValeTransporte = mysql_fetch_assoc($sqlValeTransporte)){
        //ATENCAO indice pos incrementado
        $arrayValeTransporte[$dep++] = $rowValeTransporte;
    }
   
    
    /**
     * SELECIONA OS DESCONTOS OUTRA EMPRESA
     */
    $objInssOutrasEmpresas->setIdClt($id_clt);
    $objInssOutrasEmpresas->getByIdClt();
    $countInssOutrasEmpresas = 0;
}

$row_projeto = mysql_fetch_assoc(mysql_query("SELECT * FROM projeto WHERE id_projeto = {$id_projeto} LIMIT 1;"));
/**
 * SELECIONA TODOS OS SINDICATOS DA REGIÃO
 */
$sqlArraySindicatos = mysql_query("SELECT id_sindicato, nome FROM rhsindicato WHERE id_regiao = {$id_regiao} ORDER BY nome");
$arraySindicatos[''] = "-- SELECIONE --";
while($rowArraySindicatos = mysql_fetch_assoc($sqlArraySindicatos)){
    $arraySindicatos[$rowArraySindicatos['id_sindicato']] = $rowArraySindicatos['nome'];       
}


/**
 * SELECIONA TODAS AS FUNÇÕES DO PROJETO
 */
$sqlCurso = mysql_query("SELECT id_curso, nome, letra, numero, valor, salario FROM curso WHERE campo3 = '{$id_projeto}' AND tipo IN(0,2) AND status = '1' AND status_reg = '1' ORDER BY nome ASC");
$verifica_curso = mysql_num_rows($sqlCurso);
if (!empty($verifica_curso)) {
    $arrayFuncoes[''] = "-- SELECIONE --";
    while ($row_curso = mysql_fetch_assoc($sqlCurso)) {
        
        $salario = number_format((!empty($row_curso['valor'])) ? $row_curso['valor'] : $row_curso['salario'], 2, ',', '.');
        $nomeNovo = "{$row_curso['nome']} {$row_curso['letra']}{$row_curso['numero']}";
        $arrayFuncoes[$row_curso['id_curso']] = "{$row_curso['id_curso']} - {$nomeNovo} (Valor: $salario)";        
        /**
         * seleção de curso nova
         */
        $arrayCursosNovo[$row_curso['nome']][$row_curso['letra']][$row_curso['numero']] = $row_curso;
    }
    $cursoLetras = array("A","B","C","D","E");
    if(count($arrayCursosNovo) > 0){
        $tabelaFuncoesNova = "<table class='table table-bordered table-condensed text-sm valign-middle'><tr><td>Cargo</td><td class='text-center'>Letra</td><td class='text-center'>1</td><td class='text-center'>2</td><td class='text-center'>3</td><td class='text-center'>4</td><td class='text-center'>5</td></tr>";
        foreach ($arrayCursosNovo as $nome => $value) {
            $tabelaFuncoesNova .= "<tr><td rowspan='".(count($value))."'>".$nome.'</td>';
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
    }
} else {
    $arrayFuncoes[''] = "Nenhum Curso Cadastrado para o Projeto";
}
/**
 * SELECIONA TODOS OS TIPOS DE ADMISSAO
 */
$sqlTipoAdm = mysql_query("SELECT id_status_admi, codigo, especifica FROM rhstatus_admi");
$arrayTipoAdm = array("" => "-- SELECIONE --");
while ($rowTipoAdm = mysql_fetch_assoc($sqlTipoAdm)) {
    $arrayTipoAdm[$rowTipoAdm['id_status_admi']] = $rowTipoAdm['codigo'] . " - " . $rowTipoAdm['especifica'];
}

/**
 * SELECIONA TODOS OS PROJETOS
 */
$sqlProjetos = mysql_query("SELECT id_projeto, nome FROM projeto WHERE status_reg = 1 AND id_master = ".$usuario['id_master']." ORDER BY nome") or die("ERRO AO SELECIONAR OS PROJETOS: " . mysql_error());
$arrayProjetos = array("" => "-- SELECIONE --");
while ($rowProjetos = mysql_fetch_assoc($sqlProjetos)) {
    $arrayProjetos[$rowProjetos['id_projeto']] = $rowProjetos['nome'];
}

/**
 * SELECIONA TODAS AS UNIDADES DO PROJETO
 */
$sqlUnidades = mysql_query("SELECT id_unidade, unidade FROM unidade WHERE campo1 = {$id_projeto} ORDER BY unidade");
$arrayUnidades = array("" => "-- SELECIONE --");
while ($rowUnidades = mysql_fetch_assoc($sqlUnidades)) {
    $arrayUnidades[$rowUnidades['id_unidade']] = $rowUnidades['id_unidade'] . " - " . $rowUnidades['unidade'];
}

/**
 * SELECIONA TODAS AS UF
 */
$sqlUfs = mysql_query("SELECT uf_sigla FROM uf ORDER BY uf_sigla");
$arrayUfs[""] = "-- SELECIONE --";
while ($rowUfs = mysql_fetch_assoc($sqlUfs)) {
    $arrayUfs[$rowUfs['uf_sigla']] = $rowUfs['uf_sigla'];
}

/*
 * Seleciona os paises
 */
$sqlPaises = mysql_query("SELECT * FROM paises");
$arrayPaises[0] = "Selecione";

while ($row = mysql_fetch_assoc($sqlPaises)) {
    $arrayPaises[$row['id_pais']] = utf8_encode($row['pais']);
}
/**
 * SELECIONA OS ESTADOS CIVIS
 */
$sqlEstadoCivil = mysql_query("SELECT id_estado_civil, nome_estado_civil FROM estado_civil ORDER BY nome_estado_civil");
$arrayEstadoCivil[""] = "-- SELECIONE --";
while ($rowEstadoCivil = mysql_fetch_assoc($sqlEstadoCivil)) {
    $arrayEstadoCivil[$rowEstadoCivil['id_estado_civil'] . '|' . $rowEstadoCivil['nome_estado_civil']] = $rowEstadoCivil['nome_estado_civil'];
}

/**
 * SELECIONA AS NACIONALIDADES
 */
$sqlNacionalidades = mysql_query("SELECT codigo, nome FROM cod_pais_rais ORDER BY nome");
$arrayNacionalidades[""] = "-- SELECIONE --";
while ($rowNacionalidades = mysql_fetch_assoc($sqlNacionalidades)) {
    $arrayNacionalidades[$rowNacionalidades['nome']] = $rowNacionalidades['nome'];
}

/**
 * SELECIONA AS ESCOLARIDADES
 */
$sqlEscolaridades = mysql_query("SELECT cod, id, nome FROM escolaridade WHERE status = 'on' ORDER BY 2");
$arrayEscolaridades[""] = "-- SELECIONE --";
while ($rowEscolaridades = mysql_fetch_assoc($sqlEscolaridades)) {
    $arrayEscolaridades[$rowEscolaridades['id']] = $rowEscolaridades['cod'] . ' - ' . $rowEscolaridades['nome'];
}

/**
 * SELECIONA OS TIPOS SANGUINEOS
 */
$sqlTipoSangue = mysql_query("SELECT nome FROM tipo_sanguineo ORDER BY nome");
$arrayTipoSangue[""] = "-- SELECIONE --";
while ($rowTipoSangue = mysql_fetch_assoc($sqlTipoSangue)) {
    $arrayTipoSangue[$rowTipoSangue['nome']] = $rowTipoSangue['nome'];
}

/**
 * SELECIONA OS OLHOS/CABELOS
 */
$sqlOlhosCabelos = mysql_query("SELECT nome, tipo FROM tipos WHERE status = '1' ORDER BY nome");
$arrayOlhosCabelos[1][""] = $arrayOlhosCabelos[2][""] = "-- SELECIONE --";
while ($rowOlhosCabelos = mysql_fetch_assoc($sqlOlhosCabelos)) {
    $arrayOlhosCabelos[$rowOlhosCabelos['tipo']][$rowOlhosCabelos['nome']] = $rowOlhosCabelos['nome'];
}

/**
 * SELECIONA AS ETINIAS
 */
$sqlEtinia = mysql_query("SELECT id, nome FROM etnias WHERE status = 'on' ORDER BY id DESC");
$arrayEtinia[""] = "-- SELECIONE --";
while ($rowEtinia = mysql_fetch_assoc($sqlEtinia)) {
    $arrayEtinia[$rowEtinia['id']] = $rowEtinia['nome'];
}

/**
 * SELECIONA AS DEFICIÊNCIA
 */
$sqlDeficiencias = mysql_query("SELECT id, nome FROM deficiencias WHERE status = 'on'");
$arrayDeficiencias[""] = "-- SELECIONE --";
while ($rowDeficiencias = mysql_fetch_assoc($sqlDeficiencias)) {
    $arrayDeficiencias[$rowDeficiencias['id']] = $rowDeficiencias['nome'];
}

/**
 * LISTA OS BANCOS (Nome)
 */
$sqlBancos = mysql_query("SELECT id_lista, banco FROM listabancos WHERE status_reg = 1");
$arrayBancos[""] = "-- SELECIONE --";
while ($rowBancos = mysql_fetch_assoc($sqlBancos)) {
    $arrayBancos[$rowBancos['banco']] = $rowBancos['banco'];
}
$arrayBancos["000"] = "Outro";

/**
 * LISTA OS BANCOS P/ Favorecidos de pensao (ID)
 */
$sqlBancosFav = mysql_query("SELECT id_lista, banco FROM listabancos WHERE status_reg = 1");
$arrayBancosFav[""] = "-- SELECIONE --";
while ($rowBancos = mysql_fetch_assoc($sqlBancosFav)) {
    $arrayBancosFav[$rowBancos['id_lista']] = $rowBancos['banco'];
}
$arrayBancosFav["000"] = "Outro";


/*
 * Seleciona contas de bancos por projeto
 */
$sqlBancosProjeto = mysql_query("SELECT * FROM bancos WHERE id_projeto = '$id_projeto' AND status_reg = '1'");
$arrayBancosProjeto[0] = "Sem Banco";
while($rowBancos = mysql_fetch_assoc($sqlBancosProjeto)){
    $arrayBancosProjeto[$rowBancos['id_banco']] = "{$rowBancos['razao']} - {$rowBancos['agencia']} - {$rowBancos['conta']}";
}
$arrayBancosProjeto[9999] = "Outro Banco";

/**
 * SELECIONA OS TIPO PAGAMENTO
 */
$sqlTipoPagamento = mysql_query("SELECT id_tipopg, tipopg FROM tipopg WHERE id_projeto = '$id_projeto' ORDER BY tipopg");
$arrayTipoPagamento[""] = "-- SELECIONE --";
while ($rowTipoPagamento = mysql_fetch_assoc($sqlTipoPagamento)) {
    $arrayTipoPagamento[$rowTipoPagamento['id_tipopg']] = $rowTipoPagamento['tipopg'];
}

/**
 * SELECIONA OS TIPO CONTRATAÇÃO
 */
$sqlTipoContratacao = mysql_query("SELECT id_categoria_trab, descricao FROM categorias_trabalhadores WHERE grupo = 'Empregado' ORDER BY descricao;");
$arrayTipoContratacao[""] = "-- SELECIONE --";
while ($rowTipoContratacao = mysql_fetch_assoc($sqlTipoContratacao)) {
    $arrayTipoContratacao[$rowTipoContratacao['id_categoria_trab']] = $rowTipoContratacao['descricao'];
}

/**
 * SELECIONA OS VALES ALIMENTAÇÃO E REFEIÇÃO
 */
$sqlRefAli = mysql_query("SELECT B.nome_categoria, B.campo_clt, A.nome_tipo, A.id_va_tipos FROM rh_va_tipos AS A LEFT JOIN rh_va_categorias AS B ON(A.id_va_categoria=B.id_va_categoria) WHERE A.`status`=1 AND B.`status`=1");
while ($rowRefAli = mysql_fetch_assoc($sqlRefAli)) {
    $arrayRefAliNome[$rowRefAli['campo_clt']] = $rowRefAli['nome_categoria'];
    $arrayRefAli[$rowRefAli['campo_clt']][""] = "-- SELECIONE --";
    $arrayRefAli[$rowRefAli['campo_clt']][$rowRefAli['id_va_tipos']] = $rowRefAli['nome_tipo'];
}

/**
 * SELECIONA OS VALES ALIMENTAÇÃO E REFEIÇÃO
 */
$objPlanoSaude->getPlanoSaude();
$arrayPlanoSaude[''] = "-- SELECIONE --";
while($objPlanoSaude->getRowPlanoSaude()){
    $arrayPlanoSaude[$objPlanoSaude->getIdPlanoSaude()] = $objPlanoSaude->getRazao();
}

/**
 * SELECIONA AS APOLICES
 */
$sqlApolices = mysql_query("SELECT id_apolice, razao FROM apolice WHERE id_regiao = {$usuario['id_regiao']}");
$arrayApolices[0] = "-- NÃO POSSUI --";
while ($rowApolices = mysql_fetch_assoc($sqlApolices)) {
    $arrayApolices[$rowApolices['id_apolice']] = $rowApolices['razao'];
}

/**
 * SELECIONA OS VALES TRANSPORTES
 */
$sqlVT = mysql_query("SELECT A.id_tarifas, A.valor, A.tipo, A.itinerario, B.nome FROM rh_tarifas A LEFT JOIN rh_concessionarias B ON (A.id_concessionaria = B.id_concessionaria) WHERE A.id_regiao = {$usuario['id_regiao']} AND A.status_reg = '1'");
$arrayVT[0] = "-- NÃO POSSUI --";
while ($rowVT = mysql_fetch_assoc($sqlVT)) {
    $arrayVT[$rowVT['id_tarifas']] = "{$rowVT['valor']} - {$rowVT['tipo']} [{$rowVT['itinerario']}] - {$rowVT['nome']}";
}

/**
 * Seleciona as linhas de transporte
 */
$sqlLinha = mysql_query("SELECT * FROM rh_vt_linha AS A ORDER BY A.nome");
$arrayLinha[] = '-- SELECIONE --';
while($rowLinha = mysql_fetch_assoc($sqlLinha)){
    $arrayLinha[$rowLinha['id_vt_linha']] = $rowLinha['nome']."Linha (".$rowLinha['codigo'].")" ;
}

/*
 * Seleciona os valores das tarifas
 */
$sqlVTValores = mysql_query("SELECT *FROM rh_vt_valores AS A WHERE A.status_reg = 1");
$arrayVTVal[] = "-- SELECIONE --";
while ($rowVTVal = mysql_fetch_assoc($sqlVTValores)) {
    $arrayVTVal[$rowVTVal['id_valor']] = "{$rowVTVal['valor']}";
}

/**
 * Anos Contribuição
 */
$arrayAnosContrib = anosArray(2000, date('Y'));
$arrayAnosContrib[''] = "-- SELECIONE --";
$arrayAnosContrib[1] = "-- TOTAL --";
ksort($arrayAnosContrib);

$nome_pagina = "Gerenciamento de CLT";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>$nome_pagina);
//$breadcrumb_pages = $breadcrumb_caminhos[$caminho];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <!--<link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" >-->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <!--<link href="../resources/css/bootstrap-note.css.css" rel="stylesheet" type="text/css">-->
        <style>
            .ui-autocomplete {
                position: absolute;
                top: 100%;
                left: 0;
                z-index: 1000;
                float: left;
                display: none;
                min-width: 160px;
                _width: 160px;
                padding: 4px 0;
                margin: 2px 0 0 0;
                list-style: none;
                background-color: #ffffff;
                border-color: #ccc;
                border-color: rgba(0, 0, 0, 0.2);
                border-style: solid;
                border-width: 1px;
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
                -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
                -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
                -webkit-background-clip: padding-box;
                -moz-background-clip: padding;
                background-clip: padding-box;
                *border-right-width: 2px;
                *border-bottom-width: 2px;

                .ui-menu-item > a.ui-corner-all {
                  display: block;
                  padding: 3px 15px;
                  clear: both;
                  font-weight: normal;
                  line-height: 18px;
                  color: #555555;
                  white-space: nowrap;
                  cursor: pointer;

                  &.ui-state-hover, &.ui-state-active {
                    color: #ffffff;
                    text-decoration: none;
                    background-color: #0088cc;
                    border-radius: 0px;
                    -webkit-border-radius: 0px;
                    -moz-border-radius: 0px;
                    background-image: none;
                  }
                }
              }
        </style>
    </head>
    <body>
<!--v2.0-->
    <div id="carregando" class="modal fade in" style="display: block;" aria-hidden="false"><div class="modal-dialog text-center no-margin-t" style="width: 100%; height:100%; margin-top: 0!important; padding-top: 25%;"><img src="http://f71iabassp.com/intranet/imagens/loading2.gif" style="height: 100px;"></div></div>
    <div class="modal-backdrop fade in"></div>
    <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - <?= $nome_pagina ?></small></h2></div>
                </div>
            </div>
            <form action="" class="form-horizontal" method="post" id="form_clt" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class="panel-heading text-bold">DADOS DO PROJETO</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-sm-6">
                                <div class="text-bold">Matrícula:</div>
                                <div class=""><?= $matricula ?></div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-bold">Projeto:</div>
                                <div class=""><?= $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome'] ?></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <!--<div class="col-sm-6">
                                <div class="text-bold">Possui Sindicato:</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input id="sind_sim" name="radio_sindicato" type="radio" class="reset" value="sim" <?= (empty($id_clt) || !empty($rowSindicato)) ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="sind_sim">SIM</label>
                                        <div class="input-group-addon"><input id="sind_nao" name="radio_sindicato" type="radio" class="reset" value="nao" <?= (!empty($id_clt) && empty($rowSindicato)) ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="sind_nao">NÃO</label>
                                    </div>
                                </div>
                            </div>-->
                            <div id="div_sindicato" class="col-sm-6">
                                <div class="text-bold">Sindicato:</div>
                                <div class=""><?= montaSelect($arraySindicatos, $rowClt['rh_sindicato'], "class='form-control validate[required]' name='rh_sindicato' id='rh_sindicato'") ?></div>                                
                            </div> 
                            <div class="col-sm-4">
                                <div class="text-bold">Tipo Admissão:</div>
                                <div class="">
                                    <?= montaSelect($arrayTipoAdm, $rowClt['status_admi'], "class='form-control validate[required]' name='status_admi' id='status_admi'") ?>
                                </div>                                
                            </div>
                            <div class="col-sm-2" id="div_data_importacao">
                                <div class="text-bold">Data Importação:</div>
                                <div class="">
                                    <input type="text" class='dtformat form-control validate[required] datemask' name='data_importacao' id='data_importacao' value="<?= implode('/', array_reverse( explode('-', $rowClt['data_importacao']))) ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-6">
                                <div class="text-bold">Isento de Contribuição:</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input id="ano_contribuicao_sim" name="radio_contribuicao" type="radio" value="sim" <?= ($rowClt['ano_contribuicao'] > 0) ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="ano_contribuicao_sim">SIM</label>
                                        <div class="input-group-addon"><input id="ano_contribuicao_nao" name="radio_contribuicao" type="radio" value="nao" <?= (empty($rowClt['ano_contribuicao'])) ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="ano_contribuicao_nao">NÃO</label>
                                    </div>
                                </div>
                            </div>
                            <div id="div_ano_contribuicao" class="col-sm-2" <?= ($rowClt['ano_contribuicao'] > 0) ? null : 'style="display: none;"' ?>>
                                <div class="text-bold">Ano:</div>
                                <div class=""><?= montaSelect($arrayAnosContrib, $rowClt['ano_contribuicao'], "class='form-control' name='ano_contribuicao' id='ano_contribuicao'") ?></div>
                            </div>                            
                            <div class="col-sm-4">
                                <div class="text-bold">&nbsp;</div>
                                <div class="input-group">
                                    <div class="input-group-addon"><input type="checkbox" class="" name="contrato_medico" id="contrato_medico" value="1" <?= ($rowClt['contrato_medico']) ? 'checked="checked"' : null ?>></div>
                                    <label class="form-control text-default" for="contrato_medico">Contrato para Médicos</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-6">
                                <div class="text-bold">Função:</div>
                                <div class="">
                                    <div class="input-group">
                                        <?= montaSelect($arrayFuncoes, $rowClt['id_curso'], "class='form-control validate[required]' name='id_curso' id='id_curso'") ?>                                        
                                        <div class="input-group-addon pointer" id="btn-funcoes"><i class="fa fa-eye"></i></div>                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-bold">Horário:</div>
                                <div id="div_horario" class="">Selecione uma função!</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-6">
                                        <div class="text-bold">Setor: </div>
                                        <?= montaSelect($arraySetor, $rowClt['id_setor'], "name='id_setor' id='id_setor' class=' form-control validate[required]'") ?>                        
                            </div>
                        </div>                             
                        <legend>Unidades do CLT</legend>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <div class="text-bold">Unidade 1:</div>
                                <div class="">
                                    <input type="hidden" name="id_projeto" value="<?= $id_projeto ?>">
                                    <input type="hidden" name="unidade[1][id_assoc]" value="<?= $arrayUnidadesClt[0]['id_assoc'] ?>">
                                    <?= montaSelect($arrayUnidades, $arrayUnidadesClt[0]['id_unidade'], "class='form-control validate[required]' name='unidade[1][id_unidade]' id='id_unidade1'") ?>                                    
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">&nbsp;</div>
                                <div class="">
                                    <div class="input-group">
                                        <input type="text" class='form-control validate]' name='unidade[1][porcentagem]' id='unidade_porcentagem1' value="<?= isset($arrayUnidadesClt[0]['porcentagem']) ? $arrayUnidadesClt[0]['porcentagem'] : 0 ?>">
                                        <div class="input-group-addon">%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <div class="text-bold">Projeto 2:</div>
                                <div class="">
                                    <input type="hidden" name="unidade[2][id_assoc]" value="<?= $arrayUnidadesClt[1]['id_assoc'] ?>">
                                    <?= montaSelect($arrayProjetos, $arrayUnidadesClt[1]['id_projeto'], "class='unidade_projeto form-control' name='unidade[2][id_projeto]' id='unidade_projeto2' data-ordem='2' data-unidade='" . $arrayUnidadesClt[1]['id_unidade'] . "' ") ?>
                                    
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Unidade 2:</div>
                                <div class="" id="div_unidade_projeto2">
                                    <?= montaSelect(array('SELECIONE O PROJETO 2'), null, "class='form-control' name='unidade[2][id_unidade]' id='id_unidade2'") ?>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">&nbsp;</div>
                                <div class="">
                                    <div class="input-group">
                                        <input type="text" class='form-control validate[required]' name='unidade[2][porcentagem]' id='unidade_porcentagem2' value="<?= isset($arrayUnidadesClt[1]['porcentagem']) ? $arrayUnidadesClt[1]['porcentagem'] : 0 ?>">
                                        <div class="input-group-addon">%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <div class="text-bold">Projeto 3:</div>
                                <div class="">
                                    <input type="hidden" name="unidade[3][id_assoc]" value="<?= $arrayUnidadesClt[2]['id_assoc'] ?>">
                                    <?= montaSelect($arrayProjetos, $arrayUnidadesClt[2]['id_projeto'], "class='unidade_projeto form-control' name='unidade[3][id_projeto]' id='unidade_projeto3' data-ordem='3' data-unidade='" . $arrayUnidadesClt[2]['id_unidade'] . "' ") ?>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Unidade 3:</div>
                                <div class="" id="div_unidade_projeto3">
                                    <?= montaSelect(array('SELECIONE O PROJETO 3'), null, "class='form-control validate[required]' name='unidade[3][id_unidade]' id='id_unidade3'") ?>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">&nbsp;</div>
                                <div class="">
                                    <div class="input-group">
                                        <input type="text" class='form-control validate[required]' name='unidade[3][porcentagem]' id='unidade_porcentagem3' value="<?= isset($arrayUnidadesClt[2]['porcentagem']) ? $arrayUnidadesClt[2]['porcentagem'] : 0 ?>">
                                        <div class="input-group-addon">%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading border-t text-bold">DADOS PESSOAIS</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-sm-8">
                                <div class="text-bold">Nome:</div>
                                <div class="">
                                    <input type="text" class="form-control validate[required, custom[onlyLetterSp]]" name="nome" id="nome" value="<?= $rowClt['nome'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Data de Nascimento:</div>
                                <div class="">
                                    <input type="text" class="dtformat form-control validate[required] datemask" name="data_nasci" id="data_nasci" value="<?= implode('/', array_reverse(explode('-', $rowClt['data_nasci']))) ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">UF de Nascimento:</div>
                                <div class="">
                                    <?= montaSelect($arrayUfs, $rowClt['uf_nasc'], "class='form-control validate[required]' name='uf_nasc' id='uf_nasc'") ?>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Municipio de Nascimento:</div>
                                <div class="">
                                    <div class="input-group">
                                        <input type="text" class="form-control validate[required]" name="municipio_nasc" id="municipio_nasc" value="<?= $rowClt['municipio_nasc'] ?>">
                                        <div class="input-group-addon">Cod.</div>
                                        <input type="text" class="form-control validate[required]" name="id_municipio_nasc" id="id_municipio_nasc" value="<?= $rowClt['id_municipio_nasc'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Estado Civil:</div>
                                <div class="">
                                    <?= montaSelect($arrayEstadoCivil, $rowClt['id_estado_civil'] . '|' . $rowClt['civil'], "class='form-control validate[required]' name='civil' id='civil'") ?>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Sexo:</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input id="sexo_m" name="sexo" type="radio" value="M" <?= ($rowClt['sexo'] == 'M') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="sexo_m">Masculino</label>
                                        <div class="input-group-addon"><input id="sexo_f" name="sexo" type="radio" value="F" <?= ($rowClt['sexo'] == 'F') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="sexo_f">Feminino</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">Nacionalidade:</div>
                                <div class="">
                                    <?=  montaSelect($arrayNacionalidades, isset($rowClt['nacionalidade']) ? $rowClt['nacionalidade'] : 'Brasileiro' , "class='form-control validate[required]' name='nacionalidade' id='nacionalidade'") ?>
                                </div>
                            </div>
                            <div class="col-sm-2 div_nacionalidade">
                                <div class="text-bold text-sm" style="margin-bottom: 3px;">Data de chegada ao país:</div>
                                <div class="">
                                    <input type="text" class='dtformat form-control datemask' name='dtChegadaPais' id='dtChegadaPais' value="<?= implode('/', array_reverse(explode('-',$rowClt['dtChegadaPais']))) ?>" >
                                </div>
                            </div>
                            <div class="col-sm-4 div_nacionalidade">
                                <div class="text-bold">País de Nascimento:</div>
                                <div class="">
                                    <!--
                                    <div class="input-group">
                                        <input type="text" class="pais form-control validate[required]" data-tipo="pais_nasc" name="pais_nasc" id="pais_nasc" value="<?= $rowClt['pais_nasc'] ?>">
                                        <div class="input-group-addon">Cod.</div>
                                        <input type="text" class="form-control validate[required]" name="id_pais_nasc" id="id_pais_nasc" value="<?= $rowClt['id_pais_nasc'] ?>">
                                    </div>
                                    -->
                                     <div class="">
                                        <?=  montaSelect($arrayPaises, isset($rowClt['id_pais_nasc']) ? $rowClt['id_pais_nasc'] : 1 , "class='form-control validate[required]' name='id_pais_nasc' id='id_pais_nasc'") ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 div_nacionalidade">
                                <div class="text-bold">País de Nacionalidade:</div>
                                <!-- <div class="">
                                    <div class="input-group">
                                        <input type="text" class="pais form-control validate[required]" data-tipo="pais_nacionalidade" name="pais_nacionalidade" id="pais_nacionalidade" value="<?= $rowClt['pais_nacionalidade'] ?>">
                                        <div class="input-group-addon">Cod.</div>
                                        <input type="text" class="form-control validate[required]" name="id_pais_nacionalidade" id="id_pais_nacionalidade" value="<?= $rowClt['id_pais_nacionalidade'] ?>">
                                    </div>
                                </div> -->
                                <div class="">
                                        <?=  montaSelect($arrayPaises, isset($rowClt['id_pais_nacionalidade']) ? $rowClt['id_pais_nacionalidade'] : 1 , "class='form-control validate[required]' name='id_pais_nacionalidade' id='id_pais_nacionalidade'") ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">CEP:</div>
                                <div class="">
                                    <input type="text" id="cep" name="cep" maxlength="9" class="cep form-control validate[required]" value="<?= $rowClt['cep'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-bold">Endereço:</div>
                                <div class="">
                                    <div class="input-group">
                                        <input type="text" class='form-control validate[required]' name='endereco' id='endereco' value="<?= $rowClt['endereco'] ?>" >
                                        <div class="input-group-addon">Nº.</div>
                                        <input type="text" name="numero" id="numero" class="form-control validate[required]" style="width: 70px;" value="<?= $rowClt['numero'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Bairro:</div>
                                <div class="">
                                    <input type="text" class="form-control validate[required]" name="bairro" id="bairro" value="<?= $rowClt['bairro'] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">UF:</div>
                                <div class="">
                                    <?= montaSelect($arrayUfs, $rowClt['uf'], "class='form-control validate[required]' name='uf' id='uf' data-tipo='cidade'") ?>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Cidade:</div>
                                <div class="">
                                    <div class="input-group">
                                        <input type="text" class="form-control validate[required]" name="cidade" id="cidade" value="<?= $rowClt['cidade'] ?>">
                                        <div class="input-group-addon">Cod.</div>
                                        <input type="text" class="form-control validate[required]" name="id_municipio_end" id="id_municipio_end" value="<?= $rowClt['id_municipio_end'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-bold">Complemento:</div>
                                <div class="">
                                    <input type="text" class="form-control" name="complemento" id="complemento" value="<?= $rowClt['complemento'] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">Estuda Atualmente?</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input id="estuda_sim" name="estuda" type="radio" class="reset" value="sim" <?= (($rowClt['estuda'])=='sim') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="estuda_sim">SIM</label>
                                        <div class="input-group-addon"><input id="estuda_nao" name="estuda" type="radio" class="reset" value="nao" <?= (($rowClt['estuda'])=='nao') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="estuda_nao">NÃO</label>
                                    </div>
                                </div>
                            </div>
                            <div id="termino_em" class="col-sm-2" <?= ($rowClt['data_escola'] == '0000-00-00') ? 'style="display: none;"'  : null ?>>
                                <div class="text-bold">Término em:</div>   
                                <div class="">
                                    <input id="termino_em_input" type="text" class="dtformat form-control datemask" name="data_escola" id="data_escola" value="<?= ($rowClt['data_escola'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['data_escola']))); ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Escolaridade:</div>
                                <div class="">
                                    <?= montaSelect($arrayEscolaridades, $rowClt['escolaridade'], 'class="form-control" id="escolaridade" name="escolaridade"') ?>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Curso:</div>
                                <div class="">
                                    <input type="text" class="form-control" name="curso" id="curso" value="<?= $rowClt['curso'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Instituição:</div>
                                <div class="">
                                    <input type="text" class="form-control" name="instituicao" id="instituicao" value="<?= $rowClt['instituicao'] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">Telefone Fixo:</div>
                                <div class="">
                                    <input type="text" class="tel form-control" name="tel_fixo" id="tel_fixo" value="<?= $rowClt['tel_fixo'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Telefone Celular:</div>
                                <div class="">
                                    <input type="text" class="tel form-control" name="tel_cel" id="tel_cel" value="<?= $rowClt['tel_cel'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Recado:</div>
                                <div class="">
                                    <input type="text" class="tel form-control" name="tel_rec" id="tel_rec" value="<?= $rowClt['tel_rec'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Tipo Sanguíneo:</div>
                                <div class="">
                                    <?= montaSelect($arrayTipoSangue, $rowClt['tipo_sanguineo'], 'class="form-control" id="tipo_sanguineo" name="tipo_sanguineo"') ?>
                                 
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">E-mail:</div>
                                <div class="">
                                    <input type="text" class="form-control" name="email" id="email" value="<?= $rowClt['email'] ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading border-t text-bold">DADOS DA FAMÍLIA</div>
                    <div class="panel-body">
                        <legend>Filiação Pai</legend>
                        <div class="form-group">
                            <div class="col-sm-5">
                                <div class="text-bold">Nome:</div>
                                <div class="">
                                    <input type="text" class='form-control validate[custom[onlyLetterSp]]'  name='pai' id='pai' value="<?= $rowClt['pai'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">&nbsp;</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="ddir_pai" id="ddir_pai" <?= ($arrayDependentes['ddir_pai']) ? 'checked' : null ?> value="1"></div>
                                        <label class="form-control text-default" for="ddir_pai">Dependente de IRRF</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Nacionalidade:</div>
                                <!-- <div class="">
                                    <input type="text" class='form-control' name='nacionalidade_pai' id='nacionalidade_pai' value="<?= $rowClt['nacionalidade_pai'] ?>">
                                </div> -->
                                <div class="">
                                    <?=  montaSelect($arrayNacionalidades, $rowClt['nacionalidade_pai'], "class='form-control' name='nacionalidade_pai' id='nacionalidade_pai'") ?>                                   
                                </div>
                            </div>
                            </div> <!-- form group -->
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <div class="text-bold">Data de Nascimento:</div>
                                    <div class="">
                                        <input type="text" class='dtformat form-control datemask' name='data_nasc_pai' id='data_nasc_pai' value="<?= ($rowClt['data_nasc_pai'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['data_nasc_pai']))) ?>">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="text-bold">CPF:</div>
                                    <div class="">                                        
                                        <input type="text" name="cpf_pai" value ="<?=$cpfPai ?> " class='cpf form-control validate[custom[cpf]]'?>
                                    </div>
                                </div>
                        </div> <!-- form group -->
                        <legend>Filiação Mãe</legend>
                        <div class="form-group">
                            <div class="col-sm-5">
                                <div class="text-bold">Nome:</div>
                                <div class="">
                                    <input type="text" class='form-control' name='mae' id='mae' value="<?= $rowClt['mae'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">&nbsp;</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="ddir_mae" id="ddir_mae" <?= ($arrayDependentes['ddir_mae']) ? 'checked' : null ?> value="1"></div>
                                        <label class="form-control text-default" for="ddir_mae">Dependente de IRRF</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Nacionalidade:</div>
                                
                                <div class="">
                                    <?=  montaSelect($arrayNacionalidades, $rowClt['nacionalidade_mae'] , "class='form-control' name='nacionalidade_mae' id='nacionalidade_mae'") ?>
                                </div>                                
                            </div>
                            </div> <!--form-group -->
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <div class="text-bold">Data de Nascimento:</div>
                                    <div class="">
                                        <input type="text" class='dtformat form-control datemask' name='data_nasc_mae' id='data_nasc_mae' value="<?= ($rowClt['data_nasc_mae'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['data_nasc_mae']))) ?>">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="text-bold">CPF:</div>
                                    <div class="">
                                        <input type="text" value ="<?=$cpfMae ?> " name="cpf_mae" class='cpf form-control validate[custom[cpf]]'?>
                                    </div>
                                </div>
                        </div> <!--form-group -->
                        <legend>Cônjuge</legend>
                        <div class="form-group">
                            <div class="col-sm-5">
                                <div class="text-bold">Nome:</div>
                                <div class="">
                                    <input type="text" class='form-control' name='nome_conjuge' id='nome_conjuge' value="<?= $rowClt['nome_conjuge'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">&nbsp;</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="ddir_conjuge" id="ddir_conjuge" <?= ($arrayDependentes['ddir_conjuge']) ? 'checked' : null ?> value="1"></div>
                                        <label class="form-control text-default" for="ddir_conjuge">Dependente de IRRF</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Data de Nascimento:</div>
                                <div class="">
                                    <input type="text" class='dtformat form-control datemask' name='data_nasc_conjuge' id='data_nasc_conjuge' value="<?= ($rowClt['data_nasc_conjuge'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['data_nasc_conjuge']))) ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                    <div class="text-bold">CPF:</div>
                                    <div class="">
                                        <input type="text" value ="<?=$cpfConjuge ?> " name="cpf_conjuge" class='cpf form-control validate[custom[cpf]]'?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">Filhos</div>
                            <div class="panel-body">
                                <input type="hidden" class='form-control' name='dependente[id_dependentes]' value="<?= $arrayDependentes['id_dependentes'] ?>">
                                <?php for($i = 1; $i <= 6; $i++) { ?>
                                <div id='painel-filhos<?= $i ?>'>
                                    <div class="form-group" >                                        
                                        <div class="col-sm-5">
                                            <div class="text-bold">Nome:</div>
                                            <div class="">
                                                <input type="text" class='form-control nomeFavorecido' name='dependente[<?= $i ?>][nome]' id='nome_filho<?= $i ?>' value="<?= $arrayDependentes[$i]['nome'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="text-bold">CPF:</div>
                                            <div class="">
                                                <input type="text" class='cpf form-control validate[custom[cpf]] cpfFavorecido' name='dependente[<?= $i ?>][cpf]' id='cpf_filho<?= $i ?>' value="<?= $arrayDependentes[$i]['cpf'] ?>">
                                                    
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="text-bold">&nbsp;</div>
                                            <div class="">
                                                <div class="input-group">
                                                    <div class="input-group-addon"><input type="checkbox" class="" name="dependente[<?= $i ?>][nao_ir_filho]" id="nao_ir_filho<?= $i ?>" <?= ($arrayDependentes[$i]['nao_ir_filho']) ? 'checked' : null ?> value="1"></div>
                                                    <label class="form-control text-default" for="nao_ir_filho<?= $i ?>">Não deduzir no IR</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="text-bold">Data de Nascimento:</div>
                                            <div class="">
                                                <input type="text" class='dtformat form-control datemask' name='dependente[<?= $i ?>][data_nasc]' id='data_nasc_filho<?= $i ?>' value="<?= implode('/', array_reverse(explode('-', $arrayDependentes[$i]['data']))) ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <div class="">
                                                <div class="input-group">
                                                    <div class="input-group-addon"><input type="checkbox" class="" name="dependente[<?= $i ?>][deficiencia]" id="deficiencia_filho<?= $i ?>" <?= ($arrayDependentes[$i]['deficiencia']) ? 'checked' : null ?> value="1"></div>
                                                    <label class="form-control text-default" for="deficiencia_filho<?= $i ?>">Portador de deficiência</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="">
                                                <div class="input-group">
                                                    <div class="input-group-addon"><input type="checkbox" class="" name="dependente[<?= $i ?>][fac_tec]" id="fac_tec_filho<?= $i ?>" <?= ($arrayDependentes[$i]['fac_tec']) ? 'checked' : null ?> value="1"></div>
                                                    <label class="form-control text-default" for="fac_tec_filho<?= $i ?>">Cursando escola técnica ou faculdade</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="">
                                                <div class="input-group">   
                                                    <div class="input-group-addon"><input type="checkbox" class="pensao" name="pensao_alimenticia<?= $i ?>" data-target="filho<?= $i ?>" id="pensao<?= $i ?>"  value="1" <?= ($i <= count($arrayFavorecidos)) ? 'checked' : null ?> ></div>
                                                    <label class="form-control text-default" for="pensao<?= $i ?>">Pensão Alimentícia</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                   
                                    <div class="div_favorecido" id="pensao_alimenticia<?= $i ?>">
                                        <?php if(count($arrayFavorecidos) > 0) { ?>                                                
                                            <div class="form-group">                                                        
                                                 <input type="hidden" name="favorecidos_pensao[<?= $i ?>][id]" class="form-control" value="<?= $arrayFavorecidos[$i]['id'] ?>">
                                                 <input type="hidden" name="favorecidos_pensao[<?= $i ?>][favorecido]" data-parent="pensao<?= $i ?>" id="favorecidos_nome_filho<?= $i ?>" class="form-control" value="<?= $arrayFavorecidos[$i]['favorecido'] ?>">   
                                                 <input type="hidden" name="favorecidos_pensao[<?= $i ?>][cpf]" data-parent="pensao<?= $i ?>" id="favorecidos_cpf_filho<?= $i ?>" class="form-control" value="<?= $arrayFavorecidos[$i]['cpf'] ?>">
        
                                                 <div class="col-sm-3">
                                                    <div class="text-bold">Aliquota:</div>
                                                    <div class="">
                                                        <input type="text" name="favorecidos_pensao[<?= $i ?>][aliquota]" class="aliquota form-control" placeholder="0.00" value="<?= $arrayFavorecidos[$i][aliquota] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="text-bold">Oficio:</div>
                                                        <div class="">
                                                           <input type="text" name="favorecidos_pensao[<?= $i ?>][oficio]" class="form-control" value="<?= $arrayFavorecidos[$i][oficio] ?>">
                                                        </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="text-bold">Banco:</div>
                                                        <div class="">
                                                            <?= montaSelect($arrayBancosFav, $arrayFavorecidos[$i][id_lista_banco], 'class="form-control" name="favorecidos_pensao['.$i.'][id_lista_banco]"') ?>
                                                        </div>
                                                </div>
                                                <div class="col-sm-3">
                                                      <div class="text-bold">Agência:</div>
                                                           <div class="">
                                                               <input type="text" name="favorecidos_pensao[<?= $i ?>][agencia]" class="form-control" value="<?= $arrayFavorecidos[$i][agencia] ?>">
                                                           </div>
                                                      </div>
                                                <div class="col-sm-3">
                                                    <div class="text-bold">Conta:</div>
                                                        <div class="">
                                                            <input type="text" name="favorecidos_pensao[<?= $i ?>][conta]" class="form-control" value="<?= $arrayFavorecidos[$i][conta] ?>">
                                                        </div>
                                                </div>
                                            </div>
                                        <?php }else{ ?>
                                            <div class="form-group">
                                                     <input type="hidden" name="favorecidos_pensao[<?= $i ?>][id]" class="form-control" value="<?= $value['id'] ?>">
                                                     <input type="hidden" name="favorecidos_pensao[<?= $i ?>][favorecido]" data-parent="pensao<?= $i ?>" id="favorecidos_nome_filho<?= $i ?>" class="form-control" value="<?= $value['favorecido'] ?>">   
                                                     <input type="hidden" name="favorecidos_pensao[<?= $i ?>][cpf]" data-parent="pensao<?= $i ?>" id="favorecidos_cpf_filho<?= $i ?>" class="form-control" value="<?= $value['cpf'] ?>">

                                                     <div class="col-sm-3">
                                                        <div class="text-bold">Aliquota:</div>
                                                        <div class="">
                                                            <input type="text" name="favorecidos_pensao[<?= $i ?>][aliquota]" class="aliquota form-control" placeholder="0.00">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="text-bold">Oficio:</div>
                                                            <div class="">
                                                               <input type="text" name="favorecidos_pensao[<?= $i ?>][oficio]" class="form-control" value="">
                                                            </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="text-bold">Banco:</div>
                                                            <div class="">
                                                                <?= montaSelect($arrayBancosFav, null, 'class="form-control" name="favorecidos_pensao['.$i.'][id_lista_banco]"') ?>
                                                            </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                          <div class="text-bold">Agência:</div>
                                                               <div class="">
                                                                   <input type="text" name="favorecidos_pensao[<?= $i ?>][agencia]" class="form-control" >
                                                               </div>
                                                          </div>
                                                    <div class="col-sm-3">
                                                        <div class="text-bold">Conta:</div>
                                                            <div class="">
                                                                <input type="text" name="favorecidos_pensao[<?= $i ?>][conta]" class="form-control">
                                                            </div>
                                                    </div>
                                            </div>
                                        <?php } ?>
                                    </div>    
                                    <hr>
                            </div>
                                <?php } ?>
                                <button id='add_filho' type='button' class="btn btn-success"> Adicionar Filho </button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading border-t text-bold">APARÊNCIA</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">Cabelos:</div>
                                <div class="">
                                    <?= montaSelect($arrayOlhosCabelos[1], $rowClt['cabelos'], 'class="form-control" id="cabelos" name="cabelos"') ?>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Olhos:</div>
                                <div class="">
                                    <?= montaSelect($arrayOlhosCabelos[2], $rowClt['olhos'], 'class="form-control" id="olhos" name="olhos"') ?>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Peso:</div>
                                <div class="">
                                    <input type="text" class="form-control" name="peso" id="peso" value="<?= $rowClt['peso'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Altura:</div>
                                <div class="">
                                    <input type="text" class="form-control" name="altura" id="altura" value="<?= $rowClt['altura'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Marcas ou Cicatriz:</div>
                                <div class="">
                                    <input type="text" class="form-control" name="defeito" id="defeito" value="<?= $rowClt['defeito'] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">Etnia:</div>
                                <div class="">
                                    <?= montaSelect($arrayEtinia, $rowClt['etnia'], 'class="form-control" id="etnia" name="etnia"') ?>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Deficiência:</div>
                                <div class="">
                                    <?= montaSelect($arrayDeficiencias, $rowClt['deficiencia'], 'class="form-control" id="deficiencia" name="deficiencia"') ?>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="text-bold">Enviar Foto:</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <input type="checkbox" class="" name="foto" id="foto">
                                        </div>
                                        <div class="form-control no-border">
                                            <input name="arquivo" type="file" id="arquivo">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading border-t text-bold">DOCUMENTAÇÃO</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">Nº do RG:</div>
                                <div class="">
                                    <input type="text" name="rg" id="rg" class="form-control validate[required, custom[onlyNumberSp]]" value="<?= $rowClt['rg'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Orgão Expedidor:</div>
                                <div class="">
                                    <input type="text" name="orgao" id="orgao" class="form-control validate[required]" value="<?= $rowClt['orgao'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">UF:</div>
                                <div class="">
                                    <?= montaSelect($arrayUfs, $rowClt['uf_rg'], 'class="form-control validate[required]" id="uf_rg" name="uf_rg"') ?>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Data Expedição:</div>
                                <div class="">
                                    <input type="text" name="data_rg" id="data_rg" class="dtformat form-control validate[required] datemask" value="<?= ($rowClt['data_rg'] == '0000-00-00') ? null : implode('/',array_reverse(explode('-',$rowClt['data_rg']))) ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">CPF:</div>
                                <div class="">
                                    <input type="text" name="cpf" id="cpf" class="cpf form-control validate[required,custom[cpf]] verificaCpfFunDemitidos" value="<?= $rowClt['cpf'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Órgão Regulamentador:</div>
                                <div class="">
                                    <input type="text" name="conselho" id="conselho" class="form-control" value="<?= $rowClt['conselho'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Data de emissão:</div>
                                <div class="">
                                    <input type="text" name="data_emissao" id="data_emissao" class="dtformat form-control validate[required] datemask" value="<?= ($rowClt['data_emissao'] == '0000-00-00') ? null : implode('/',array_reverse(explode('-',$rowClt['data_emissao']))) ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3">
                              <div class="text-bold">Número CRT</div>
                                <div class="">
                                    <input type="text" name="num_crt" id="num_crt" class="form-control validate[required, custom[onlyNumberSp]]" value="<?= $rowClt['num_crt'] ?>">
                                </div>  
                            </div>
                            <div class="col-sm-2">
                              <div class="text-bold">Emissão CRT</div>
                                <div class="">
                                    <!-- <input type="text" name="emissao_crt" id="emissao_crt" class="dtformat form-control validate[required] hasDatepicker" value="<?= $rowClt['emissao_crt'] ?>"> -->
                                    <input type="text" name="emissao_crt" id="emissao_crt" class="dtformat  form-control validate[required] datemask" value="<?= ($rowClt['emissao_crt'] == '0000-00-00') ? null : implode('/',array_reverse(explode('-',$rowClt['emissao_crt']))) ?>">
                                </div>  
                            </div>
                            <div class="col-sm-2">
                              <div class="text-bold">Validade CRT</div>
                                <div class="">
                                    <!-- <input type="text" name="validade_crt" id="validade_crt" class="dtformat form-control validate[required] hasDatepicker" value="<?= $rowClt['validade_crt'] ?>"> -->
                                    <input type="text" name="validade_crt" id="validade_crt" class="dtformat form-control validate[required] datemask" value="<?= ($rowClt['validade_crt'] == '0000-00-00') ? null : implode('/',array_reverse(explode('-',$rowClt['validade_crt']))) ?>">
                                </div>  
                            </div>
                        </div>    
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold">Nº Carteira de Trabalho:</div>
                                <div class="">
                                    <input type="text" name="campo1" id="campo1" class="form-control validate[required]" value="<?= $rowClt['campo1'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Série:</div>
                                <div class="">
                                    <input type="text" name="serie_ctps" id="serie_ctps" class="form-control validate[required]" value="<?= $rowClt['serie_ctps'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">UF:</div>
                                <div class="">
                                    <?= montaSelect($arrayUfs, $rowClt['uf_ctps'], 'class="form-control validate[required]" id="uf_ctps" name="uf_ctps"') ?>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Data carteira de Trabalho:</div>
                                <div class="">
                                    <input type="text" name="data_ctps" id="data_ctps" class="dtformat form-control datemask" value="<?= ($rowClt['data_ctps'] == '0000-00-00') ? null : implode('/',array_reverse(explode('-',$rowClt['data_ctps']))) ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">Nº Título de Eleitor:</div>
                                <div class="">
                                    <input type="text" name="titulo" id="titulo" class="form-control validate[required, custom[onlyNumberSp]]" value="<?= $rowClt['titulo'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Zona:</div>
                                <div class="">
                                    <input type="text" name="zona" id="zona" class="form-control validate[required, custom[onlyNumberSp]]" value="<?= $rowClt['zona'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Seção:</div>
                                <div class="">
                                    <input type="text" name="secao" id="secao" class="form-control validate[required,custom[onlyNumberSp]]" value="<?= $rowClt['secao'] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">PIS:</div>
                                <div class="">
                                    <input type="text" name="pis" id="pis" class="form-control <?= ($rowClt['pis'] != '') ? 'validate[required,custom[pis]]' : null ?>" value="<?= $rowClt['pis'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Data Pis:</div>
                                <div class="">
                                    <input type="text" name="dada_pis" id="dada_pis" class="dtformat form-control validate[condRequired[pis]] datemask" value="<?= ($rowClt['dada_pis'] == '0000-00-00') ? null : implode('/',array_reverse(explode('-',$rowClt['dada_pis']))) ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">FGTS:</div>
                                <div class="">
                                    <input type="text" name="fgts" id="fgts" class="form-control validate[required]" value="<?= $rowClt['fgts'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Certificado de Reservista:</div>
                                <div class="">
                                    <input type="text" name="reservista" id="reservista" class="form-control validate[required]" value="<?= $rowClt['reservista'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Carteira do SUS:</div>
                                <div class="">
                                    <input type="text" name="carteira_sus" id="carteira_sus" class="form-control validate[required]" value="<?= $rowClt['carteira_sus'] ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading border-t text-bold">BENEFÍCIOS</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold">&nbsp;</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="desconto_inss" id="desconto_inss" value="1" <?= ($rowClt['desconto_inss'] == 1) ? 'checked' : null ?> ></div>
                                        <label class="form-control text-default" for="desconto_inss">Proporcionalidade INSS</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-5 div_desconto_inss">
                                <div class="text-bold">Tipo de Desconto:</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input id="tipo_desconto_inss1" name="tipo_desconto_inss" type="radio" class="reset" value="isento" <?= ($rowClt['tipo_desconto_inss'] == 'isento') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="tipo_desconto_inss1">Suspensão de Recolhimento</label>
                                        <div class="input-group-addon"><input id="tipo_desconto_inss2" name="tipo_desconto_inss" type="radio" class="reset" value="parcial" <?= ($rowClt['tipo_desconto_inss'] == 'parcial') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="tipo_desconto_inss2">Parcial</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 div_desconto_inss">
                                <div class="text-bold">Trabalha em outra empresa?</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input id="trabalha_outra_empresa_sim" name="trabalha_outra_empresa" type="radio" class="reset" value="sim" <?= ($rowClt['trabalha_outra_empresa'] == 'sim') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="trabalha_outra_empresa_sim">SIM</label>
                                        <div class="input-group-addon"><input id="trabalha_outra_empresa_nao" name="trabalha_outra_empresa" type="radio" class="reset" value="nao" <?= ($rowClt['trabalha_outra_empresa'] == 'nao' || empty($rowClt['trabalha_outra_empresa'])) ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="trabalha_outra_empresa_nao">NÃO</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-1 div_trabalha_outra_empresa" <?= ($rowClt['trabalha_outra_empresa'] != 'sim') ? 'style="display: none;"' : null ?>>
                                <div class="text-bold">&nbsp;</div>
                                <button type="button" class="btn btn-success" id="add_dados_outra_empresa"><i class="fa fa-plus"></i></button>
                            </div>
                            <!--div_trabalha_outra_empresa-->
                        </div>
                        <div id="div_dados_outra_empresa" class="div_trabalha_outra_empresa">
                            <?php if($objInssOutrasEmpresas->getNumRows() > 0) { ?>
                                <?php while($objInssOutrasEmpresas->getRow()) { ?>
                                <div class="form-group dados_outra_empresa">
                                    <div class="col-sm-3">
                                        <div class="text-bold">Salário da outra empresa:</div>
                                        <div class="">
                                            <input type="hidden" name="outra_empresa[<?= $countInssOutrasEmpresas ?>][id_inss]" value='<?= $objInssOutrasEmpresas->getIdInss() ?>'>
                                            <input type="text" name="outra_empresa[<?= $countInssOutrasEmpresas ?>][salario]" class="valor form-control" value='<?= number_format($objInssOutrasEmpresas->getSalario(), 2, ',', '.') ?>'>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="text-bold">Desconto da outra empresa:</div>
                                        <div class="">
                                            <input type="text" name="outra_empresa[<?= $countInssOutrasEmpresas ?>][desconto]" class="valor form-control" value='<?= number_format($objInssOutrasEmpresas->getDesconto(), 2, ',', '.') ?>'>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="text-bold">Início:</div>
                                        <div class="">
                                            <input type="text" name="outra_empresa[<?= $countInssOutrasEmpresas ?>][inicio]" class="dtformatinicio data form-control datemask" value='<?= $objInssOutrasEmpresas->getInicio('d/m/Y') ?>'>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Fim:</div>
                                        <div class="">
                                            <input type="text" name="outra_empresa[<?= $countInssOutrasEmpresas ?>][fim]" class="dtformatfim data form-control datemask" value='<?= $objInssOutrasEmpresas->getFim('d/m/Y') ?>'>
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <div class="text-bold">&nbsp;</div>
                                        <button type="button" class="del_dados_empresa btn btn-danger" data-id="<?= $objInssOutrasEmpresas->getIdInss() ?>"><i class="fa fa-trash-o"></i></button>
                                    </div>
                                </div>
                                <?php $countInssOutrasEmpresas++; } ?>
                            <?php } else { ?>
                                <div class="form-group dados_outra_empresa">
                                    <div class="col-sm-3">
                                        <div class="text-bold">Salário da outra empresa:</div>
                                        <div class="">
                                            <input type="text" name="outra_empresa[0][salario]" class="valor form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="text-bold">Desconto da outra empresa:</div>
                                        <div class="">
                                            <input type="text" name="outra_empresa[0][desconto]" class="valor form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="text-bold">Início:</div>
                                        <div class="">
                                            <input type="text" name="outra_empresa[0][inicio]" class="dtformatinicio data form-control datemask">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Fim:</div>
                                        <div class="">
                                            <input type="text" name="outra_empresa[0][fim]" class="dtformatfim data form-control datemask">
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <div class="text-bold">&nbsp;</div>
                                        <button type="button" class="del_dados_empresa btn btn-danger" data-id=""><i class="fa fa-trash-o"></i></button>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold">Integrante do CIPA:</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input id="cipa_sim" name="cipa" type="radio" value="1" <?= ($rowClt['cipa'] == 1) ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="cipa_sim">SIM</label>
                                        <div class="input-group-addon"><input id="cipa_nao" name="cipa" type="radio" value="0" <?= (empty($rowClt['cipa'])) ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="cipa_nao">NÃO</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">&nbsp;</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="seguro_desemprego" id="seguro_desemprego" value="1" <?= ($rowClt['seguro_desemprego'] == 1) ? 'checked' : null ?> ></div>
                                        <label class="form-control text-default" for="seguro_desemprego">Recebendo Seguro Desemprego?</label>
                                    </div>
                                </div>
                            </div>
                            <?php foreach ($arrayRefAli as $nomeCampo => $value) { ?>
                            <div class="col-sm-2">
                                <div class="text-bold">Vale <?= $arrayRefAliNome[$nomeCampo] ?>:</div>
                                <div class="">
                                    <?= montaSelect($value, $rowClt[$nomeCampo], 'class="form-control" id="'.$nomeCampo.'" name="'.$nomeCampo.'"') ?>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold">Assistência Médica:</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input id="medica_sim" name="medica" type="radio" value="1" <?= ($rowClt['medica'] == 1) ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="medica_sim">SIM</label>
                                        <div class="input-group-addon"><input id="medica_nao" name="medica" type="radio" value="0" <?= (empty($rowClt['medica'])) ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="medica_nao">NÃO</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 div_medica">
                                <div class="text-bold">Plano de Saúde:</div>
                                <div class="">
                                    <?= montaSelect($arrayPlanoSaude, $rowClt['id_plano_saude'], 'class="form-control" id="id_plano_saude" name="id_plano_saude"') ?>                                    
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Tipo de Plano:</div>
                                <div class="">
                                    <?= montaSelect(array(1 => 'Familiar', 2 => 'Individual'), $rowClt['plano'], 'class="form-control" id="plano" name="plano"') ?>
                                </div>
                            </div>
                        </div>
                        <!--<div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold">Seguro, Apólice:</div>
                                <div class="">
                                    <?= montaSelect($arrayApolices, $rowClt['apolice'], 'class="form-control" id="apolice" name="apolice"') ?>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Dependente:</div>
                                <div class="">
                                    <input type="text" name="campo2" id="campo2" class="form-control" value="<?= $rowClt['campo2'] ?>">
                                </div>
                            </div>
                        </div>-->
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold">&nbsp;</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="transporte" id="transporte" value="1" <?= ($rowClt['transporte']) ? 'checked' : null ?> ></div>
                                        <label class="form-control text-default" for="transporte">Vale Transporte</label>
                                        <input type="hidden" class="form-control" id="id_vale" name="id_vale" value="<?= $arrayValeTransporte[1]['id_vale'] ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                        <?php if(isset($arrayValeTransporte)){ 
                            //se houver algum vale cadastrado
                                $pHide = '';
                                $aux = 0; $count = 0;
                                for ($i = 1; $i <= 5; $i++){ 
                                    $hide = 'hide';
                                    if(isset($arrayValeTransporte[1]['id_linha'.$i]) && !empty($arrayValeTransporte[1]['id_linha'.$i])){
                                        //se id_linha estiver setada e não vazia then não vai dar o hide
                                        $hide = '';
                                    }
                                    $pHide = $hide;
                                    $aux = $i;
                                    if($pHide == 'hide' && empty($arrayValeTransporte[1]['id_linha'.$aux++]) && $count == 0){                                        
                                        $hide = '';
                                        $count++;
                                    }
                                    
                                    
                                //    echo "<script> console.log('".$i." - ".$hide."-".'id_linha'.$i."')</script>";
                                
                        ?>
                        <div class="form-group div_transporte <?= $hide ?>">
                            <div class="col-sm-3">
                                <div class="text-bold">Opção <?php echo $i; ?>:</div>
                                <div >
                                    <?= montaSelect($arrayLinha, $arrayValeTransporte[1]['id_linha'.$i], 'class="form-control linha-vale validate[required]" id="vale'.$i.'" data-index="'.$i.'"  name="vale'.$i.'"') ?>
                                </div>
                            </div>
                            <div class="col-sm-3">
                             <div class="text-bold">Valor</div>
                                <div >
                                   <?= montaSelect($arrayVTVal, $arrayValeTransporte[1]['id_valor'.$i], 'class="form-control vt_valor" id="vt_valor'.$i.'" name="vt_valor'.$i.'"') ?>
                                </div> 
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Quantidade</div>
                                <div >
                                    <input name="vt_qtd<?php echo $i; ?>" class="form-control qtd_class" type="text" id="vt_qtd<?php echo $i; ?>" size="3" value="<?= $arrayValeTransporte[1]["qtd".$i] ?>"/>                                           
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Num. Cartão</div>
                                <div >
                                    <input name="vt_card<?php echo $i; ?>" class="form-control card_class" type="text" id="vt_card<?php echo $i; ?>" value="<?= $arrayValeTransporte[1]['cartao'.$i] ?>" />
                                </div>
                            </div>
                            <div class="col-sm-1" style="margin-top: 15px;">
                                <button type="button" class="del_tp btn btn-danger"><i class="fa fa-trash-o"></i></button>
                            </div>
                        </div>
                        <?php 
                                }
                            }else{
                                for ($i = 1; $i <= 5; $i++){
                                    if($i > 1){
                                        $hide = 'hide';
                                    }
                                    
                        ?>
                        <div class="form-group div_transporte <?= $hide ?>">
                            <div class="col-sm-3">
                                <div class="text-bold">Opção <?php echo $i; ?>:</div>
                                <div >
                                    <?= montaSelect($arrayLinha, $arrayValeTransporte[1]['id_linha'.$i], "class='form-control linha-vale validate[required]' id='vale".$i."' data-index='".$i."' name='vale".$i."'") ?>
                                </div>
                            </div>
                            <div class="col-sm-3">
                             <div class="text-bold">Valor</div>
                                <div >
                                   <?= montaSelect($arrayVTVal, $arrayValeTransporte[1]['id_valor'.$i], 'class="form-control vt_valor" id="vt_valor'.$i.'" name="vt_valor'.$i.'"') ?>
                                </div> 
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Quantidade</div>
                                <div >
                                    <input name="vt_qtd<?php echo $i; ?>" class="form-control qtd_class" type="text" id="vt_qtd<?php echo $i; ?>" size="3" value="<?= $arrayValeTransporte[1]["qtd".$i] ?>"/>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Num. Cartão</div>
                                <div >
                                    <input name="vt_card<?php echo $i; ?>" class="form-control card_class" type="text" id="vt_card<?php echo $i; ?>" value="<?= $arrayValeTransporte[1]['cartao'.$i] ?>" />
                                </div>
                            </div>
                        </div>
                        <?php
                        
                                }
                            }
                        ?>
                    </div>
                    <div class="panel-heading border-t text-bold">DADOS BANCÁRIOS</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold">Banco:</div>
                                <div class="">
                                    <?= montaSelect($arrayBancosProjeto, $rowClt['banco'], 'class="form-control" id="banco" name="banco"') ?>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Agência:</div>
                                <div class="">
                                    <input type="text" name="agencia" id="agencia" maxlength = "5" size="5" class="form-control" value="<?= $rowClt['agencia']; ?>">
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="text-bold">DV:</div>
                                <div class="">
                                    <input type="text" name="agencia_dv" id="agencia_dv" maxlength="1" size="1" class="form-control" value="<?= $rowClt['agencia_dv'] ?>" />
                                </div>
                            </div>
                            
                            <div class="">
                                    <div class="text-bold">&nbsp;</div>
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="ckb_agencia" id="ckb_agencia"  /></div>
                                        <label class="form-control text-default" for="ckb_agencia">Agência sem digito</label>
                                    </div>
                            </div>
                            <!--
                            <div class="col-sm-2">
                                <div class="text-bold">Agência sem digito:</div>
                                <div class="">
                                    <input type="checkbox" name="ckb_agencia" id="ckb_agencia" value="">
                                    
                                </div>
                            </div>  
                            -->
                        </div>
                        <div class="form-group">
                             <div class="col-sm-2">
                                <div class="text-bold">Conta:</div>
                                <div class="">
                                        <input type="text" name="conta" id="conta" class="form-control" value="<?= $rowClt['conta'] ?>">
                                </div>
                            </div>
                           <div class="col-sm-6">
                                <div class="text-bold">DV:</div>
                                <div>
                                    <div class="input-group">
                                        <input type="text" name="conta_dv" id="conta_dv" maxlength="1" size="2" class="form-control" <?= (($rowClt['conta_dv'])) ? 'disabled' : null ?> value="<?= $rowClt['conta_dv'] ?>" />
                                        <div class="input-group-addon"><input id="conta_salario" name="tipo_conta" type="radio" class="reset" value="salario" <?= ($rowClt['tipo_conta'] == 'salario') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="conta_salario"><!--Conta -->Salário</label>
                                        <div class="input-group-addon"><input id="conta_corrente" name="tipo_conta" type="radio" class="reset" value="corrente" <?= ($rowClt['tipo_conta'] == 'corrente') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="conta_corrente"><!--Conta -->Corrente</label>
                                        <div class="input-group-addon"><input id="conta_poupanca" name="tipo_conta" type="radio" class="reset" value="poupanca" <?= ($rowClt['tipo_conta'] == 'poupanca') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="conta_poupanca"><!--Conta -->Poupança</label>
                                    </div>
                                </div>    
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold text-sm" style="margin-bottom: 3px;">Nome do Banco:<i class="text-danger"> (caso fora da lista acima)</i></div>
                                <div class="">
                                    <?= montaSelect($arrayBancos, $rowClt['nome_banco'], 'class="form-control" id="nome_banco" name="nome_banco"') ?>
                                </div>
                                
                                <!-- <div class="">
                                    <input type="text" name="nome_banco" id="nome_banco" class="form-control" value="<?= $rowClt['nome_banco'] ?>">
                                </div>
                                 -->
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading border-t text-bold">DADOS FINANCEIROS E DE CONTRATO</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">Data de Entrada:</div>
                                <div class="">
                                    <input type="text" name="data_entrada" id="data_entrada" class="dtformat form-control validate[required] datemask" value="<?= ($rowClt['data_entrada'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-',$rowClt['data_entrada']))) ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold text-sm" style="margin-bottom: 3px;">Data Exame Admissional:</div>
                                <div class="">
                                    <input type="text" name="data_exame" id="data_exame" class="dtformat form-control datemask" value="<?= ($rowClt['data_exame'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-',$rowClt['data_exame']))) ?>">
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="text-bold">Local de Pagamento:</div>
                                <div class="">
                                    <input type="text" name="localpagamento" id="localpagamento" class="form-control validate[required]" value="<?= $rowClt['localpagamento'] ?>">
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="text-bold">Tipo de Pagamento:</div>
                                <div class="">
                                    <?= montaSelect($arrayTipoPagamento, $rowClt['tipo_pagamento'], 'class="form-control validate[required]" id="tipo_pagamento" name="tipo_pagamento"') ?>                                    
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <div class="text-bold">Prazo de Experiência:</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input id="3060" name="prazoExp" type="radio" class="reset" value="1" <?= ($rowClt['prazoexp'] == '1') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="3060">30 + 60</label>
                                        <div class="input-group-addon"><input id="4545" name="prazoExp" type="radio" class="reset" value="2" <?= ($rowClt['prazoexp'] == '2' || $rowClt['prazoexp'] == '') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="4545">45 + 45</label>
                                        <div class="input-group-addon"><input id="6030" name="prazoExp" type="radio" class="reset" value="3" <?= ($rowClt['prazoexp'] == '3') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="6030">60 + 30</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-bold">Tipo de Contrato:</div>
                                <div class="">
                                    <?= montaSelect($arrayTipoContratacao, $rowClt['tipo_contrato'], 'class="form-control validate[required]" id="tipo_contrato" name="tipo_contrato"') ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="text-bold">Observações:</div>
                                <div class="">
                                    <textarea class="form-control" cols="55" rows="4" name="observacoes" id="observacoes"><?= $rowClt['obs'] ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-sm-6">
                                <div class="text-bold">&nbsp;</div>
                                <div class="input-group">
                                   
                                     <div class="input-group-addon"><input type="checkbox" class="" name="id_pde" id="id_pde" <?= $rowClt['pde']=='1' ? 'checked' : null ?>></div>
                                        <label class="form-control text-default" for="id_pde">PDE</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-bold">Término de contrato:</div>
                                    <div class="">
                                        <input type="text" name="data_pde" id="data_pde" class="dtformat form-control datemask" value="<?= ($rowClt['data_pde'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-',$rowClt['data_pde']))) ?>">
                                    </div>
                            </div>
                        </div>
                        <div class="alert alert-warning">NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php if($id_clt){ ?><input name="id_clt" type="hidden" value="<?= $id_clt ?>"><?php } ?>
                        <button type="type" class="btn btn-primary" name="<?= $action ?>"><i class="fa fa-save"></i> SALVAR</button>
                    </div>
                </div>
            </form>
        
            <div class="modal fade bs-example-modal-sm" tabindex="-1" id="alertaUnidades" role="dialog" aria-labelledby="alertaUnidades">
                <div class="modal-dialog modal-sm" role="document">
                  <div class="modal-content">
                      <div class="modal-header"> 
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">×</span>
                          </button> 
                          <h4 class="modal-title" id="mySmallModalLabel">Atençao!</h4> 
                      </div>
                      <div class="modal-body"> A soma dos valores nao deve ultrapassar 100% </div>                  
                  </div>
                </div>
            </div>

                
                
        <?php include_once '../template/footer.php'; ?>
        </div><!-- /.content -->
        
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../js/jquery.maskMoney.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.steps.min.js"></script>
        <script>
            
            
            $(function() {
                
                //desabilita envio do form pelo ENTER
                $('input').keypress(function (e) {
                    console.log("chamou a function");
                    var code = null;
                    code = (e.keyCode ? e.keyCode : e.which);                
                    return (code == 13) ? false : true;
               });
                
                /**
                 * Validacao para os valores das unidades do CLT
                 * Unidades nao podem ter valor < 0 ou > 100
                 * @author Rafael
                 * @date 2016-09-02
                 */               
//                $("#form_clt").steps({
//                    headerTag: "ht",
//                    bodyTag: "section",
//                    transitionEffect: "slideLeft",
//                    autoFocus: true
//                });

                
                
                $("#unidade_porcentagem1,#unidade_porcentagem2,#unidade_porcentagem3").on("blur",function(){
                        if($("#unidade_porcentagem1").val() > 100) {
                               $("#unidade_porcentagem1").val(100);
                        }
                        else{
                            if($("#unidade_porcentagem1").val() < 0){
                               $("#unidade_porcentagem1").val(0);
                            }
                        }

                        if($("#unidade_porcentagem2").val() > 100) {
                               $("#unidade_porcentagem2").val(100);
                        }
                        else{
                            if($("#unidade_porcentagem2").val() < 0){
                               $("#unidade_porcentagem2").val(0);
                            }
                        }
                        if($("#unidade_porcentagem3").val() > 100) {
                               $("#unidade_porcentagem3").val(100);
                        }
                        else{
                            if($("#unidade_porcentagem3").val() < 0){
                               $("#unidade_porcentagem3").val(0);
                            }
                        }
                });
                
                
                //hide forms de dependentes
                if ($('#nome_filho2').val() === ''){
                    $("#painel-filhos2").hide();
                }
                if ($('#nome_filho3').val() === ''){
                    $("#painel-filhos3").hide();
                }
                if ($('#nome_filho4').val() === ''){
                    $("#painel-filhos4").hide();
                }
                if ($('#nome_filho5').val() === ''){
                    $("#painel-filhos5").hide();
                }
                if ($('#nome_filho6').val() === ''){
                    $("#painel-filhos6").hide();
                }
                
                //action do botao ADICIONAR FILHO
                $('#add_filho').click(function(){
                     if (($('#painel-filhos2').css('display') === 'none') && ($('#nome_filho1').val() !== '')){
                        $("#painel-filhos2").show(); 
                        return;
                    }
                    if ($('#painel-filhos3').css('display') === 'none' && ($('#nome_filho2').val() !== '')){
                        $("#painel-filhos3").show();                         
                        return;
                    }
                    if ($('#painel-filhos4').css('display') === 'none' && ($('#nome_filho3').val() !== '')){
                        $("#painel-filhos4").show(); 
                        return;
                    }
                    if ($('#painel-filhos5').css('display') === 'none' && ($('#nome_filho4').val() !== '')){
                        $("#painel-filhos5").show(); 
                        return;
                    }
                    if ($('#painel-filhos6').css('display') === 'none' && ($('#nome_filho5').val() !== '')){
                        $("#painel-filhos6").show(); 
                        return;
                    }
                    if ($('#painel-filhos6').css('display') === 'block'){
                    alert('Não é possível adicionar mais filhos');
                    }
                });
                
                           


                /*
                 * Validacao para ano de contribuicao
                 */
                $("#ano_contribuicao_nao").on("change",function(){ 
                    if($("#ano_contribuicao_nao").prop("checked")){ 
                        $("#ano_contribuicao").val(""); 
                    }
                });

                /*
                 * Validacao para data de admissao/importacao
                 */
                $("#status_admi").on("change",function(){ 
                    if($("#status_admi").val() != 70){ 
                        $("#data_importacao").val(""); 
                    }
                });

                /*
                 * Validacao para plano de saude
                 */
                $("#medica_nao").on("change",function(){ 
                    if($("#medica_nao").prop("checked")){
                        $("#id_plano_saude").val(""); 
                    }
                });

                /*
                 * Controle para PDE e data
                 */
                $("#data_pde").hide();
                if ($(this).is(":checked")) {
                        $("#data_pde").show();
                } else {
                        $("#data_pde").hide();
                        $("#data_pde").val("");
                }
                $("#id_pde").on("change",function () {
                });

                $('#id_pde').trigger('change');
                /*
                 * Efeitos para os campos de porcentagem
                 * Impede que a validacao nao seja realizada por erro na soma dos campos
                 */
                $("#unidade_porcentagem1,#unidade_porcentagem2,#unidade_porcentagem3").focus(function(){ if($(this).val() == 0){$(this).val("")}});
                $("#unidade_porcentagem1,#unidade_porcentagem2,#unidade_porcentagem3").blur(function(){ if($(this).val() == "") {$(this).val("0")}});

                /*
                 * Validação para impedir que o valor agregado 
                 * das porcenttagens das unidades ultrapasse 100
                 */
                $("#unidade_porcentagem1,#unidade_porcentagem2,#unidade_porcentagem3").on("change",function(){
                    if(parseInt($("#unidade_porcentagem1").val())+parseInt($("#unidade_porcentagem2").val())+parseInt($("#unidade_porcentagem3").val()) > 100){

                          $("#unidade_porcentagem1").val("0");
                          $("#unidade_porcentagem2").val("0");
                          $("#unidade_porcentagem3").val("0");
                          $('#alertaUnidades').modal('show');
                    }
               });


                /*
                 * Validacao para nome de banco
                 */
              /*  $("#banco").on("change",function(){
                  //  console.log($(this).children(":selected").text(),$(this).val());
                    if($(this).val() != '' && $(this).val() != 0){
                       $("#nome_banco").val($(this).children(":selected").val());
                    }else{
                      $("#nome_banco").val('');
                    }
                });
                */
                /*
                 * Bloqueio de checkbox
                 */
                $("#ckb_agencia").on("click",function(){

                if($("#ckb_agencia").is(":checked")){
                         $("#agencia_dv").prop("disabled",true);
                         $("#agencia_dv").val("N");
                   }else{
                         $("#agencia_dv").prop("disabled",false);
                         $("#agencia_dv").val("");
                   }
                });
            
                
                $(".valor").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
                $(".aliquota").maskMoney({ precision: 2, allowNegative: true, thousands:'', decimal:'.' });
                /* Não funciona!
                $('.data').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true,
                    //yearRange: '1930:c+1',
                    yearRange: "nnnn:nnnn",
                    beforeShow: function () {
                        setTimeout(function () {
                            $('.ui-datepicker').css('z-index', 5010);
                        }, 0);
                    }
                });
                */
                $(".dtformat").datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '1910:c+1',
                    //yearRange: "nnnn:nnnn",
                    beforeShow: function () {
                        setTimeout(function () {
                            $('.ui-datepicker').css('z-index', 5010);
                        }, 0);
                    }
                });
                
                /*
                 * Gatilho para mostrar/esconder os dados de favorecidos
                 * por pensao alimenticia.
                 * A associacao e feita pelo atributo name do checkbox que
                 * deve ser igual ao id da div
                 */
                $("body").on('change', '.pensao', function () {
                    //console.log($(this));
                    //pensao_alimenticia
                    var div = "#"+ $(this).attr("name");
                    if ($(this).prop('checked')) { 
                        //$(".div_favorecido").show();
                        $(div).show();                                              
                        /*
                         * 
                         * Correcao para nome e cpf de dependentes
                         * copiando os nomes para os inputs hidden
                         */
                        var target = $(this).attr("data-target");
                        var nome= "#nome_"+target;
                        var fnome = "#favorecidos_nome_"+target;
                        var cpf = "#cpf_"+target;
                        var fcpf = "favorecidos_cpf_"+target;
                        
                        /*
                         * Atribuicao de valores
                         */
                        $(fnome).val($(nome).val());
                        $(fcpf).val($(cpf).val());
                        
                    } else {
                        //$(".div_favorecido").hide();
                        $(div).hide();
                        
                        /**
                         * Itera os elementos filhos da div ativada e reseta seus valores
                         * @var index = o indice do elemento [0..n] <nao utilizado, apenas para referencia>
                         * @var element = o elemento html
                         */
                        $(div+" .form-control").each(function(index,element){

                            if($(element).attr("type") == "text" || $(element).attr("type") == "hidden"){
                               $(element).val("");
                            }else{
                              $(element).val(0);
                            }

                         });
                    }
                });
                $('.pensao').trigger('change');
                
                
                
                $(".nomeFavorecido,.cpfFavorecido").on("blur",function(){
 
                            var nome = "#favorecidos_"+$(this).attr("id");
                            var parent = "#"+$(nome).attr("data-parent");
                            if($(parent).prop("checked")){
                                $(nome).val($(this).val());
                            }
                });
                
                
                
                // máscaras
                $(".datemask").mask("99/99/9999");
                $(".cpf").mask("999.999.999-99");               
                $("#cep").mask("99999-999", {placeholder: " "});
                $(".tel").brTelMask();
                   
                /*
                 * Função para validar PIS
                 * Autor: Leonardo
                 * data: 30/04/2014
                 * @param {type} field
                 * @returns {String}
                 */
//                var verificaPIS = function (field) {
//                    var value = field.val();
//
//                    value = value.replace('.', '');
//                    value = value.replace('.', '');
//                    var pis = value.replace('-', '');
//                    if (ChecaPIS(pis) == false) {
//                        return 'PIS inválido';
//                    }
//                };
                   
                $('#form_clt').validationEngine();
                
//                $("input[type='radio'][name='radio_sindicato']").click(function () {
//                    var valor = $(this).val();
//                    if (valor === 'sim') { //Adiciona a classe validade
//                        $("#div_sindicato").show();
//                        $("#rh_sindicato").addClass("validate[required]");
//                    } else {
//                        $("#div_sindicato").hide();
//                        $("#rh_sindicato").removeClass("validate[required]").val(''); // remove a classe
//                    }
//                });
//                
                // add class do validation engine
                $("#pis").change(function () {
                    // verifica se o campo não está vazio 
                    if ($("#pis").val() != '') {
                        $("#pis").addClass('validate[required,custom[pis]]');    // adiciona classe
                    }
                    else {
                        $("#pis").removeClass('validate[required,custom[pis]]'); // remove classe
                    }
                });
                    
                $("input[type='radio'][name='radio_contribuicao']").click(function () {
                    var valor = $(this).val();
                    if (valor === 'sim') { 
                        $("#div_ano_contribuicao").show();
                        $("#radio_contribuicao").addClass("validate[required]").removeClass("disabled");
                    } else {
                        $("#div_ano_contribuicao").hide();
                        $("#radio_contribuicao").removeClass("validate[required]").addClass("disable").val(''); // remove a classe
                    }
                });
                var valorRadioEstuda = $("input[type='radio'][name='estuda']:checked").val();
                    if (valorRadioEstuda === 'sim') { 
                        $("#termino_em").show();
                        $("#termino_em_input").addClass("validate[required]");                        
                    } else {
                        $("#termino_em").hide();                        
                    }
                
                $("input[type='radio'][name='estuda']").click(function () {
                    var valor = $(this).val();
                    if (valor === 'sim') { 
                        $("#termino_em").show();
                        $("#termino_em_input").addClass("validate[required]");
                    } else {
                        $("#termino_em").hide();
                        $("#termino_em_input").removeClass("validate[required]").val(''); // remove a classe
                    }
                });
                
                $("body").on('change', '#status_admi', function () {
                    var valor = $(this).val();
                    if (valor === '70') { 
                        $("#div_data_importacao").show();
                    } else {
                        $("#div_data_importacao").hide().val('');
                    }
                });
                $("#status_admi").trigger('change');
                
                $("input[type='radio'][name='trabalha_outra_empresa']").change(function () {
                    var valor = $('#trabalha_outra_empresa_sim').prop('checked');
                    if (valor) { 
                        $(".div_trabalha_outra_empresa").show();
                    } else {
                        $(".div_trabalha_outra_empresa").hide();
                        for(var i = 0; i <= $('#div_dados_outra_empresa > .form-group').length;i++){
                                $("input[name='outra_empresa["+i+"][salario]'").val("");
                                $("input[name='outra_empresa["+i+"][desconto]'").val("");
                                $("input[name='outra_empresa["+i+"][inicio]'").val("");
                                $("input[name='outra_empresa["+i+"][fim]'").val("");
                        }
                    }
                });
                $("#trabalha_outra_empresa_sim").trigger('change');
                
                $("input[type='radio'][name='medica']").change(function () {
                    var valor = $('#medica_sim').prop('checked');
                    if (valor) { 
                        $(".div_medica").show();
                    } else {
                        $(".div_medica").hide();
                        $('#id_plano_saude').val('');
                    }
                });
                $("#medica_sim").trigger('change');
                
                $("body").on('change', '#desconto_inss', function () {
                    if ($(this).prop('checked')) { 
                        $(".div_desconto_inss").show();
                    } else {
                        $(".div_desconto_inss, #div_dados_outra_empresa, .div_trabalha_outra_empresa").hide();
                        $("#trabalha_outra_empresa_sim").prop('checked',false);
                        $("#tipo_desconto_inss1").prop('checked',false);
                        $("#tipo_desconto_inss2").prop('checked',false);
                        for(var i = 0; i <= $('#div_dados_outra_empresa > .form-group').length;i++){
                                $("input[name='outra_empresa["+i+"][salario]'").val("");
                                $("input[name='outra_empresa["+i+"][desconto]'").val("");
                                $("input[name='outra_empresa["+i+"][inicio]'").val("");
                                $("input[name='outra_empresa["+i+"][fim]'").val("");
                        }
                    }
                });
                $('#desconto_inss').trigger('change');
                
                $(".del_tp").click(function(){
                    $(this).parents(".div_transporte").find(".qtd_class").val("0"); 
                    $(this).parents(".div_transporte").find(".card_class").val("0"); 
                    $(this).parents(".div_transporte").find(".vt_valor").val("0");
                    $(this).parents(".div_transporte").find(".linha-vale").val("0");
                    
                    var linha1 = $("#vale1").val();
                    if(linha1 === "0") {
                        //uncheck no vale transporte
                        $("#transporte").removeAttr('checked');
                        $(".qtd_class").val("0");
                        $(".card_class").val("0");
                        $(".vt_valor").val("0");
                        $(".linha-vale").val("0");
                        $(".div_transporte").hide();
                    }                    
                    //$(this).parents(".div_transporte").hide();
                });  
                
                $("body").on('change', '#transporte', function () {
                    
                    if ($(this).prop('checked')) { 
                        $(".div_transporte").show();                        
                    } else {
                        $(".div_transporte").hide();                        
                        console.log("xpto");
                        for(var i =1; i <= 5; i++){
                                  $("#vale"+i).val(0);
                                  $("#vt_valor"+i).find('option').removeAttr('disabled').removeAttr('selected');
                                  $("#vt_valor"+i).val(0);
                                  $("#vt_qtd"+i).val("");
                                  $("#vt_card"+i).val("");
                            }
                    }
                });
                
                $('#transporte').trigger('change');
                
                
                $(".linha-vale").on("change",function(){
                    if($(this).val() != 0){
                        var linha = this;        
                        //linha = select com todas as linhas
                        var linhaVal = $(this).val();
                        //linhaVal = numero da linha
                        $(this).closest(".div_transporte").next().removeClass("hide");
//                         $.ajax({
//                               url:'',
//                               method:'POST',
//                               dataType:'json',
//                               data: {method:'valLinha', linha:linhaVal},
//                               success:function(data){                                  
//                                  var vtVal = $(linha).closest(".div_transporte").find("[id^=vt_valor]");
//                                  //console.log(vtVal);
//                                  vtVal.find('option').removeAttr('disabled').removeAttr('selected');
//                                  vtVal.find('option').each(function () {
//                                
//                                        if ($(this).text() == data) {
//                                         /*   console.log($(this).text(), data);
//                                            console.log(vtVal.val(),$(this).val());
//                                            console.log(vtVal);
//                                            console.log(this);*/
//                                            vtVal.val($(this).val());
//                                           // console.log(vtVal.val($(this).val()));
//                                        } else {
//                                            $(this).attr('disabled', true);
//                                        }
//                                  });
//                               }
//                           });

                    }else{
                     var index =  $(this).attr("data-index");

                     $('.div_transporte').each(function(idx,obj){
                        var objIndex = $(obj).find('.linha-vale').attr("data-index");
                        console.log(objIndex);
                        if(objIndex >= index){
                            
                          if(objIndex > index)  {
                             $(obj).addClass("hide"); 
                          }
                          $("#vt_valor"+objIndex).find('option').removeAttr('disabled').removeAttr('selected');
                          $("#vt_valor"+objIndex).val(0);  
                          $("#vale"+objIndex).val(0);
                          $("#vt_qtd"+objIndex).val("");
                          $("#vt_card"+objIndex).val("");
                       }
                     });
                    }
                    var linha1 = $("#vale1").val();
                    if(linha1 === "0") {
                        //uncheck no vale transporte
                        $("#transporte").removeAttr('checked');
                        $(".qtd_class").val("0");
                        $(".card_class").val("0");
                        $(".vt_valor").val("0");
                        $(".linha-vale").val("0");
                        $(".div_transporte").hide();
                    }   
               });
                
                
                /*
                $("body").on('change', '#pensao', function () {
                    if ($(this).prop('checked')) { 
                        $(".div_pensao").show();
                    } else {
                        $(".div_pensao").hide();

                        for(var i = 0; i <= $('#div_favorecidos .panel-footer').length;i++){
                                $("input[name='favorecidos_pensao["+i+"][favorecido]'").val("");
                                $("input[name='favorecidos_pensao["+i+"][cpf]'").val("");
                                $("input[name='favorecidos_pensao["+i+"][aliquota]'").val("");
                                $("input[name='favorecidos_pensao["+i+"][oficio]'").val("");
                                $("select[name='favorecidos_pensao["+i+"][id_lista_banco]'").val(0);
                                $("input[name='favorecidos_pensao["+i+"][agencia]'").val("");
                                $("input[name='favorecidos_pensao["+i+"][conta]'").val("");
                        }

                    }
                });
                $('#pensao').trigger('change');
                */
                $('body').on('click', '#btn-funcoes', function(){
                            
                    var msg = "<?= $tabelaFuncoesNova ?>";
                    new BootstrapDialog({
                        nl2br: false,
                        type: 'type-primary',
                        title: 'PLANO DE CARGOS E SALÁRIOS COMPLETO',
                        message: msg,
                        size: BootstrapDialog.SIZE_WIDE,
                        closable: true
                    }).open();
                });
                
                $('body').on('click', '#add_dados_outra_empresa', function(){
                    var n = $('#div_dados_outra_empresa .dados_outra_empresa').length;
                    $('#div_dados_outra_empresa').append(
                        $('<div>', { class: "form-group dados_outra_empresa" }).append(
                            $('<div>', { class: "col-sm-3" }).append(
                                $('<div>', { class: "text-bold", html: "Salário da outra empresa:" }),
                                $('<div>', { class: "" }).append(
                                    $('<input>', { type: "text", name: "outra_empresa["+n+"][salario]", class: "valor form-control" })
                                )
                            ),
                            $('<div>', { class: "col-sm-3" }).append(
                                $('<div>', { class: "text-bold", html: "Desconto da outra empresa:" }),
                                $('<div>', { class: "" }).append(
                                    $('<input>', { type: "text", name: "outra_empresa["+n+"][desconto]", class: "valor form-control" })
                                )
                            ),
                            $('<div>', { class: "col-sm-3" }).append(
                                $('<div>', { class: "text-bold", html: "Início:" }),
                                $('<div>', { class: "" }).append(
                                    $('<input>', { type: "text", name: "outra_empresa["+n+"][inicio]", class: "datainicio data form-control" })
                                )
                            ),
                            $('<div>', { class: "col-sm-2" }).append(
                                $('<div>', { class: "text-bold", html: "Fim:" }),
                                $('<div>', { class: "" }).append(
                                    $('<input>', { type: "text", name: "outra_empresa["+n+"][fim]", class: "datafim data form-control" })
                                )
                            ),
                            $('<div>', { class: "col-sm-1" }).append(
                                $('<div>', { class: "text-bold", html: "&nbsp;" }),
                                $('<button>', { type: "button", class: "del_dados_empresa btn btn-danger" }).append(
                                    $('<i>', { class: "fa fa-trash-o" })
                                )
                            )
                        )
                    );
                    $('#div_dados_outra_empresa').find('.data').datepicker({
                        dateFormat: 'dd/mm/yy',
                        changeMonth: true,
                        changeYear: true,
                       yearRange: '1930:c+1',
                        beforeShow: function () {
                            setTimeout(function () {
                                $('.ui-datepicker').css('z-index', 5010);
                            }, 0);
                        }
                    });
                    $('#div_dados_outra_empresa').find('.valor').maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
                });

                $('body').on('click', '.del_dados_empresa', function(){
                    if($(this).data('id') > 0){
                        $(this).parent().parent().remove();
                    } else {
                        $(this).parent().parent().remove();
                    }
                });

                /*
                $('body').on('click', '#add_favorecido', function(){
                    var n = $('#div_favorecidos .panel-footer').length;
                    
                    $('#div_favorecidos').append(
                        $('<div>', { class: "panel-footer" }).append(
                            $('<div>', { class: "form-group" }).append(
                                $('<div>', { class: "col-sm-3" }).append(
                                    $('<div>', { class: "text-bold", html: "Nome:" }),
                                    $('<div>', { class: "" }).append(
                                        $('<input>', { type: "hidden", name: "favorecidos_pensao[" + n + "][id]", class: "form-control", value: "" }),
                                        $('<input>', { type: "text", name: "favorecidos_pensao[" + n + "][favorecido]", class: "form-control", value: "" })
                                    )
                                ),
                                $('<div>', { class: "col-sm-3" }).append(
                                    $('<div>', { class: "text-bold", html: "CPF:" }),
                                    $('<div>', { class: "" }).append(
                                        $('<input>', { type: "text", name: "favorecidos_pensao[" + n + "][cpf]", class: "cpf form-control validate[required,custom[cpf]]", value: "" })
                                    )
                                ),
                                $('<div>', { class: "col-sm-3" }).append(
                                    $('<div>', { class: "text-bold", html: "Aliquota:" }),
                                    $('<div>', { class: "" }).append(
                                        $('<input>', { type: "text", name: "favorecidos_pensao[" + n + "][aliquota]", class: "aliquota form-control", placeholder: "0.00", value: "" })
                                    )
                                ),
                                $('<div>', { class: "col-sm-3" }).append(
                                    $('<div>', { class: "text-bold", html: "Oficio:" }),
                                    $('<div>', { class: "" }).append(
                                        $('<input>', { type: "text", name: "favorecidos_pensao[" + n + "][oficio]", class: "form-control", value: "" })
                                    )
                                )
                            ),
                            $('<div>', { class: "form-group" }).append(
                                $('<div>', { class: "col-sm-3" }).append(
                                    $('<div>', { class: "text-bold", html: "Banco:" }),
                                    $('<div>', { class: "" }).append(
                                        $('<select>', { name: "favorecidos_pensao[" + n + "][id_lista_banco]", class: "form-control" }).html(
                                            '<?php //foreach ($arrayBancos as $key => $value) { echo "<option value=\"{$key}\">{$value}</option>"; } ?>'
                                        )
                                    )
                                ),
                                $('<div>', { class: "col-sm-3" }).append(
                                    $('<div>', { class: "text-bold", html: "Agência:" }),
                                    $('<div>', { class: "" }).append(
                                        $('<input>', { type: "text", name: "favorecidos_pensao[" + n + "][agencia]", class: "form-control", value: "" })
                                    )
                                ),
                                $('<div>', { class: "col-sm-3" }).append(
                                    $('<div>', { class: "text-bold", html: "Conta:" }),
                                    $('<div>', { class: "" }).append(
                                        $('<input>', { type: "text", name: "favorecidos_pensao[" + n + "][conta]", class: "form-control", value: "" })
                                    )
                                ),
                                $('<div>', { class: "col-sm-3" }).append(
                                    $('<div>', { class: "text-bold", html: "&nbsp;" }),
                                    $('<div>', { class: "" }).append(
                                        $('<button>', { type: "button", class: "btn btn-danger deletar_favorecido" }).append(
                                            $('<i>', { class: "fa fa-trash-o", html: " Excluir Favorecido" })
                                        )
                                    )
                                )
                            )
                        )
                    );
                    $('#\n\
                ').find('.aliquota').maskMoney({ precision: 3, allowNegative: true, thousands:'', decimal:'.' })
                    $('#div_favorecidos').find(".cpf").mask("999.999.999-99");
                });
                        
                $('body').on('click', '.deletar_favorecido', function(){
                    if($(this).data('id')){
                        console.log("CONFIRMAÇÃO");
                    } else {
                        $(this).parent().parent().parent().parent().remove();
                    }
                });
                */
                $('body').on('click', '.nova_selecao_funcao', function(){
                    $('#id_curso').val($(this).data('id')).trigger('change');
                    $('.modal, .modal-backdrop').remove();
                });

                $('body').on('change', '#id_curso', function(){
                    if($(this).val() > 0) { 
                        $.post("", {bugger:Math.random(), method:'horarios', id:$(this).val(), rh_horario: '<?= $rowClt['rh_horario'] ?>'}, function(result){
                            $('#div_horario').html(result);
                        });
                    }
                });
                $('#id_curso').trigger('change');

                $('body').on('change', '.unidade_projeto', function(){
                    var $this = $(this);
                    if($this.val() > 0) { 
                        $.post("", {bugger:Math.random(), method:'unidades', id_unidade:$this.data('unidade'), id_projeto:$this.val(), ordem:$this.data('ordem')}, function(result){
                            $('#div_unidade_projeto' + $this.data('ordem')).html(result);
                        });
                    }
                });
                $('.unidade_projeto').trigger('change');
                
                
                /**
                 * NACIONALIDADE
                 */
                $("body").on('change', '#nacionalidade', function () {
                    if ($(this).val() != 'Brasileiro' && $(this).val() != '') {
                        $(".div_nacionalidade").show().find('input').addClass('validate[required]');
                        /*
                        $(".pais").focus(function () {
                            var tipo = "#" + $(this).data('tipo');
                            $.post('../methods.php', {method: 'carregaPais'}, function (data) {
                                $(tipo).autocomplete({source: data.pais});
                            }, 'json');
                        });
                        $(".pais").focusout(function () {
                            var pais = $(this).val();
                            var tipo = "#id_" + $(this).data('tipo');
                            if (pais !== '') {
                                $.post('../methods.php', {method: 'carregaCodPais', pais: pais}, function (data) {
                                    $(tipo).val(data.id_pais);
                                }, 'json');
                            }
                        });*/
                    } else {
                        $(".div_nacionalidade").hide().find('input').removeClass('validate[required]'); 
                        //$("#nacionalidade").val("");
                        $("#dtChegadaPais").val("");
                        $("#pais_nasc").val("");
                        $("#id_pais_nasc").val("");
                        $("#pais_nacionalidade").val("0");
                        $("#id_pais_nacionalidade").val("0");
                    }
                });
                $('#nacionalidade').trigger('change');
                
                /**
                 * carrega municípios para o campo município de nascimento
                 */
                 $('#uf').change(function () {
                    var uf = $('#uf').val();
                    $('#cidade').val('');
                    $('#id_municipio_end').val('');
                    $.post('../busca_cep.php', {uf: uf, municipios: 1}, function (data) {

                        $("#cidade").autocomplete({source: data.municipios,
                            change: function (event, ui) {
                                if (event.type == 'autocompletechange') {
                                    var valor_municipio = ui.item.value.split(')-');
                                    $('#id_municipio_end').val(valor_municipio[0].trim().substring(1, 5));
                                    $('#cidade').val(valor_municipio[1].trim());
                                }
                            }
                        });

                    }, 'json');
                });
                $('#uf_nasc').change(function () {
                    var uf = $('#uf_nasc').val();
                    //console.log(uf);
                    $('#municipio_nasc, #id_municipio_nasc').val('');
                    $.post('../busca_cep.php', {uf: uf, municipios: 1}, function (data) {
                        $("#municipio_nasc").autocomplete({source: data.municipios,
                            change: function (event, ui) {
                                if (event.type == 'autocompletechange') {
                                    var valor_municipio = ui.item.value.split(')-');
                                    $('#id_municipio_nasc').val(valor_municipio[0].trim().substring(1, 5));
                                    $('#municipio_nasc').val(valor_municipio[1].trim());
                                }
                            }
                        });
                    }, 'json');
                });
                
                /**
                 * BUSCA CEP
                 */
                var cep_atual = $('#cep').val().replace("-", "").replace(".", "");
                var numero_atual = $('#numero').val();
                var complemento_atual = $('#complemento').val();

                $('#cep').blur(function () {

                    $this = $(this);
                    $('<img src="../images-box/loading.gif" alt="Buscando endereço ..." style="margin-top: -30px; z-index: 999; float: right; width: 25px;" id="img_load_cep" />').insertAfter('#cep');
                    //images-box/loading.gif
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
                                        $('#id_municipio_end').val(valor_municipio[0].trim().substring(1, 5));
                                        $('#cidade').val(valor_municipio[1].trim());
                                    }
                                }
                            });
                            $('#cod_tp_logradouro').val(data.cod_tp_logradouro);
                            $('#endereco').val(data.logradouro);
                            $('#bairro').val(data.bairro);
                            $('#uf').val(data.uf);
                            $('#cidade').val(data.cidade);
                            $('#id_municipio_end').val(data.id_municipio);

                            if (data.cep == cep_atual) {
                                $('#numero').val(numero_atual);
                                $('#complemento').val(complemento_atual);
                            } else {
                                $('#numero').val('');
                                $('#complemento').val('');
                            }
                        }

                    }, 'json');
                });
                
                $('body').on('change', '#foto', function(){
                    if($(this).prop('checked') == true){
                        $('#arquivo').show();
                    } else {
                        $('#arquivo').val('').hide();
                    }
                });
                $('#foto').trigger('change');
                
                $(".verificaCpfFunDemitidos").change(function () {
                    var cpf = $(this).val();
                    $.ajax({
                        url: "",
                        type: "POST",
                        dataType: "json",
                        data: {
                            method: "verificaCpf",
                            cpf: cpf
                        },
                        success: function (data) {
                            if (data.status) {
                                $("#participanteDesativado").css({display: "block"});
                                $(data.dados).each(function (d, i) {
                                    $("#part").html(i.nome + " | <b>PROJETO:</b> " + i.projeto + " | <b>MOTIVO:</b> " + i.status + "<br><a href='http://f71lagos.com/intranet/registrodeempregado.php?bol=0&pro=" + i.idprojeto + "&clt=" + i.id + "'>Visualizar Ficha</a>");
                                });

                                $("input[name='Submit']").css({display: "none"});
                                $('html, body').animate({
                                    scrollTop: $("#part").offset().top
                                }, 2000).trigger('click');
                            }
                        }
                    });
                });
                
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

                /***
                 * FEITO POR SINESIO - 24/03/2016 - 656
                 */
                $("body").on("change","input[name='data_entrada']",function(){

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
                    var data = new Date(ano,mes,dia);
                        data.setDate(data.getDate() + 90);

                    var novaData = str_pad(data.getDate(),2,'0','STR_PAD_LEFT') + "/" + str_pad(data.getMonth(),2,'0','STR_PAD_LEFT') + "/" + data.getFullYear();

                    $("#dataFinalExperiencia").html("Termino da Experiência: " + novaData);

                });
                
                //FECHAR TELA DE CARREGANDO AO TERMINAR DE CARREGAR A PAGINA
                $(window).load(function(){
                    $('#carregando').remove();
                    $('.modal-backdrop').remove();
                });
                
                
                var tipoVerifica = 0;
//                    $("#banco").on("change",function () {
//                                               
//                        function tipoPgCheque() {
//                            
//                            $("#tipo_pagamento").find('option').removeAttr('disabled').removeAttr('selected');
//                            $("#tipo_pagamento").find('option').each(function () {
//                                
//                                if ($(this).val() == 2) {
//                                    //console.log($(this).attr('selected', true));
//                                    $("#tipo_pagamento").val($(this).val());
//                                } else {
//                                    $(this).attr('disabled', true);
//                                }
//
//                            });
//                        }
//
//                        function tipoPgConta() {
//                            
//                            $("#tipo_pagamento").find('option').removeAttr('disabled').removeAttr('selected');
//                            $("#tipo_pagamento").find('option').each(function () {
//                                if ($(this).val() == 1) {
//                                   
//                                   $("#tipo_pagamento").val($(this).val());
//                                } else {
//                                    $(this).attr('disabled', true);
//                                }
//
//                            });
//                        }
//
//                        var valor = $(this).val();
//                        
//                        var bancos = [106,110,111];
//                        if (valor == 0) {
//                            
//                            desabilita();
//                            tipoPgCheque();
//                            tipoVerifica = 1;
//
//                        } else if (valor == 9999) {
//                                Ativa();
//                                tipoPgConta();
//                                tipoVerifica = 2;
//                            $('#nome_banco').attr('disabled',false);
//                            } else if(bancos.indexOf(valor) != -1){
//                                    Ativa();
//                                    tipoPgConta();
//                                    tipoVerifica = 2;
//                                    $('#nome_banco').val("").attr('disabled',true);
//                                } else {
//                                     habilita();
//                                     tipoPgConta();
//                                     tipoVerifica = 3;
//                                     //$("input[name='nomebanco']").prop('disabled', 'disabled'); // comentar quando resolver o problema do nome banco
//                                 }
//
//                    });
                        /*
                         * Desabilita campos, deposito por cheque
                         * @returns void
                         */
                        function desabilita() {
                           
                            $("#conta").attr("disabled", true).val('');
                            $("#conta_dv").attr("disabled", true).val('');
                            $("input[name='tipo_conta'").attr("disabled", true).val('');
                            $("#agencia").attr("disabled", true).val('');
                            $("#agencia_dv").attr("disabled", true).val('');
                            $("#ckb_agencia").attr("disabled", true).val('');
                            $('#nome_banco').val('').attr('disabled',true);
                           
                        }
                        
                        /*
                         * Ativa os campos para selecao de banco
                         * @return void
                         */
                        function Ativa() {
                            
                            $("#conta").attr("disabled", false);
                            $("#conta_dv").attr("disabled", false);
                            $("input[name='tipo_conta'").attr("disabled", false);
                            $("#agencia").attr("disabled", false);
                            $('#nome_banco').attr('disabled',false);
                            $('#nome_banco').addClass('validate[required]').validationEngine("validate");
                            $("#agencia_dv").attr("disabled", false);
                            $("#ckb_agencia").attr("disabled", false);
                           
                        }
                        /*
                         * Habilita os campos para banco pre-carregado
                         * @returns void
                         */
                        function habilita(){
                            $("#conta").attr("disabled", false);
                            $("#conta_dv").attr("disabled", false);
                            $("input[name='tipo_conta'").attr("disabled", false);
                            $("#agencia").attr("disabled", false);
                            $('#nome_banco').attr('disabled',true);
                            $("#agencia_dv").attr("disabled", false);
                            $("#ckb_agencia").attr("disabled", false);
                        }
                            
                        
                 $("#banco").trigger("change");
            });
            
          
            
            /*
            function checaDatas(dataini,datafim,indice){
                    console.log("checando");
                    $(".datainicio").each(function(i,o){

                       if($("input[name='outra_empresa[indice][inicio]'").val() >= dataini && $("input[name='outra_empresa[indice][fim]).val() <= datafim'")){
                         console.log("No Intervalo");
                       }
                    });
            }
            
            $(".datafim").on("blur",function(i,o){
                console.log("blr");
                if($(this).length > 0){
                    
                     $(".datainicio").each(function(i,o){

                        checaDatas($("input[name='outra_empresa[i][inicio]'").val(),$("input[name='outra_empresa[i][fim]").val(),i);

                     });
                    
                }    
                
            });
            */
           
        </script>
    </body>
</html>