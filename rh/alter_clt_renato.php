<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br/><a href="../login.php">Logar</a>';
    exit;
}

function removeAspas($str) {
    $str = str_replace("'", "", $str);
    return mysql_real_escape_string(trim(str_replace('"', '', $str)));
}

include('../conn.php');
include('../wfunction.php');
include('../classes/SetorClass.php');
include('../classes/PlanoSaudeClass.php');
include('../classes/InssOutrasEmpresasClass.php');

$usuario = carregaUsuario();
$objPlanoSaude = new PlanoSaudeClass();
$objInssOutrasEmpresas = new InssOutrasEmpresasClass();

$id_projeto = (!empty($_REQUEST['pro'])) ? $_REQUEST['pro'] : $_REQUEST['projeto'];
$id_regiao = $usuario['id_regiao'];

///GERANDO NÚMERO DE MATRICULA E O NÚMERO DO PROCESSO
$verifica_matricula = mysql_result(mysql_query("SELECT MAX(matricula) FROM rh_clt WHERE id_projeto = {$id_projeto}"), 0);
$matricula = $verifica_matricula + 1;

/**
 * *****************************************************************************
 * *******************INICIO EDIÇAO E CADASTRO**********************************
 * *****************************************************************************
 */

if(isset($_REQUEST['salvar']) || isset($_REQUEST['editar'])){
    
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
    $uf_nasc = removeAspas($_REQUEST['uf']);
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
    $pensao_alimenticia = ($_REQUEST['pensao_alimenticia']) ? 1 : 0;
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
    $vale1 = removeAspas($_REQUEST['vale1']);
    $vale2 = removeAspas($_REQUEST['vale2']);
    $vale3 = removeAspas($_REQUEST['vale3']);
    $vale4 = removeAspas($_REQUEST['vale4']);
    $vale5 = removeAspas($_REQUEST['vale5']);
    $vale6 = removeAspas($_REQUEST['vale6']);
    $cartao1 = removeAspas($_REQUEST['cartao1']);
    $cartao2 = removeAspas($_REQUEST['cartao2']);
    $banco = removeAspas($_REQUEST['banco']);
    $agencia = removeAspas($_REQUEST['agencia']);
    $conta = removeAspas($_REQUEST['conta']);
    $tipo_conta = removeAspas($_REQUEST['tipo_conta']);
    $nome_banco = removeAspas($_REQUEST['nome_banco']);
    $data_entrada = removeAspas(implode('-', array_reverse(explode('/', $_REQUEST['data_entrada']))));
    $data_exame = removeAspas(implode('-', array_reverse(explode('/', $_REQUEST['data_exame']))));
    $localpagamento = removeAspas($_REQUEST['localpagamento']);
    $tipo_pagamento = removeAspas($_REQUEST['tipo_pagamento']);
    $prazoexp = removeAspas($_REQUEST['prazoExp']);
    $tipo_contrato = removeAspas($_REQUEST['tipo_contrato']);
    $obs = removeAspas($_REQUEST['observacoes']);
    $tipo_pagamento = removeAspas($_REQUEST['tipo_pagamento']);
    
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
    conta,
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
    status
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
    '{$conta}',
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
    10)";
    mysql_query($insert) or die("ERRO CADASTRO DE CLT: " . mysql_error());;
    $id_clt = mysql_insert_id();    
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
    conta = '{$conta}',
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
    data_importacao = '{$data_importacao}'
    WHERE id_clt = '{$id_clt}' LIMIT 1";
//    echo $update;exit;
    mysql_query($update) or die("ERRO ALTERAÇÃO DE CLT: " . mysql_error());
}

if((isset($_REQUEST['salvar']) || isset($_REQUEST['editar'])) && !empty($id_clt)){
    
    foreach ($_REQUEST['unidade'] as $key => $value) {
        $principal = ($key == 1) ? 1 : 0;
        $value['id_projeto'] = ($key == 1) ? $id_projeto : $value['id_projeto'];
        if($value['id_assoc']){
            //UPDATE
            $updateUnidade = "UPDATE rh_clt_unidades_assoc SET id_projeto = '{$value['id_projeto']}', id_unidade = '{$value['id_unidade']}', porcentagem = '{$value['porcentagem']}' WHERE id_assoc = '{$value['id_assoc']}'";
            $updateUnidade = mysql_query($updateUnidade) or die("ERRO ALTERAÇÃO DE UNIDADE: " . mysql_error());
        } else {
            //INSERT
            if($value['id_unidade']){
                $insetUnidade = "INSERT INTO rh_clt_unidades_assoc (id_clt, id_projeto, id_unidade, porcentagem, principal, status) VALUE ({$id_clt}, '{$value['id_projeto']}', '{$value['id_unidade']}', '{$value['porcentagem']}', '$principal', '1')";
                $insetUnidade = mysql_query($insetUnidade) or die("ERRO INSERSÃO DE UNIDADE: " . mysql_error());
            }
        }
        ($insetUnidade) ? print_array($insetUnidade) : null;
        ($updateUnidade) ? print_array($updateUnidade) : null;
    }
    
    foreach ($_REQUEST['favorecidos_pensao'] as $key => $value) {
        if($value['id']){
            //UPDATE
            $updateFavorecido = "UPDATE favorecido_pensao_assoc SET cpf = '{$value['cpf']}', favorecido = '{$value['favorecido']}', id_lista_banco = '{$value['id_lista_banco']}', agencia = '{$value['agencia']}', conta = '{$value['conta']}', aliquota = '{$value['aliquota']}', oficio = '{$value['oficio']}' WHERE id = '{$value['id']}'";
            $updateFavorecido = mysql_query($updateFavorecido) or die("ERRO ALTERAÇÃO DE FAVORECIDO: " . mysql_error());
        } else {
            //INSERT
            $insetFavorecido = "INSERT INTO favorecido_pensao_assoc (id_clt, cpf, favorecido, id_lista_banco, agencia, conta, aliquota, oficio) VALUE ({$id_clt}, '{$value['cpf']}', '{$value['favorecido']}', '{$value['id_lista_banco']}', '{$value['agencia']}', '{$value['conta']}', '{$value['aliquota']}', '{$value['oficio']}')";
            $insetFavorecido = mysql_query($insetFavorecido) or die("ERRO INSERSÃO DE FAVORECIDO: " . mysql_error());
        }
//        ($insetFavorecido) ? print_array($insetFavorecido) : null;
//        ($updateFavorecido) ? print_array($updateFavorecido) : null;
    }
    
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
            $updateDependente = mysql_query($updateDependente) or die("ERRO ALTERAÇÃO DE DEPENDENTES: " . mysql_error());
        } else {
            //INSERT
            $insetDependente = "INSERT INTO dependentes ($keys) VALUES ('$values');";
            $insetDependente = mysql_query($insetDependente) or die("ERRO CADASTRO DE DEPENDENTES: " . mysql_error());
        }
//        ($insetDependente) ? print_array($insetDependente) : null;
//        ($updateDependente) ? print_array($updateDependente) : null;
//    }
    
    if ($transporte) {
        if($_REQUEST['id_vale']){
            $updateVale = "UPDATE rh_vale SET id_tarifa1 = '$vale1', id_tarifa2 = '$vale2', id_tarifa3 = '$vale3', id_tarifa4 = '$vale4', id_tarifa5 = '$vale5', id_tarifa6 = '$vale6', cartao1 = '$cartao1', cartao2 = '$cartao2',  status_reg = '$status_reg' WHERE id_clt = '$id_clt'";
                mysql_query($updateVale) or die("VT ATUALIZAÇÃO: " . mysql_error());
        } else {
            $insertVale = "INSERT INTO rh_vale(id_clt,id_regiao,id_projeto,id_tarifa1,id_tarifa2,id_tarifa3,id_tarifa4,id_tarifa5,id_tarifa6,cartao1,cartao2) VALUES 
            ('$id_clt','$id_regiao','$id_projeto','$vale1','$vale2','$vale3','$vale4','$vale5','$vale6','$cartao1','$cartao2')";
            mysql_query($insertVale) or die("VT CADASTRO: " . mysql_error());
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
//    header("Location: ver_clt.php?reg=$id_regiao&clt=$id_clt&ant=0&pro=$id_projeto&pagina=bol");
    header("Location: alter_clt_renato.php?clt=$id_clt&pro=$id_projeto");
    exit;
}

/**
 * *****************************************************************************
 * **********************FIM EDIÇAO E CADASTRO**********************************
 * *****************************************************************************
 */

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
if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'unidades'){
    $sqlUnidades = mysql_query("SELECT id_unidade, unidade FROM unidade WHERE campo1 = {$_REQUEST['id_projeto']} ORDER BY unidade");
    $arrayUnidades = array("" => "-- SELECIONE --");
    while ($rowUnidades = mysql_fetch_assoc($sqlUnidades)) {
        $arrayUnidades[$rowUnidades['id_unidade']] = $rowUnidades['id_unidade'] . " - " . utf8_encode($rowUnidades['unidade']);
    }
    echo montaSelect($arrayUnidades, $_REQUEST['id_unidade'], "class='form-control validate[required]' id='id_unidade' name='unidade[{$_REQUEST['ordem']}][id_unidade]'");
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
    $qryClt = mysql_query($sqlClt);
    $rowClt = mysql_fetch_assoc($qryClt);
    
    $sqlSindicato = "SELECT id_sindicato FROM rhsindicato WHERE id_sindicato = '{$rowClt['rh_sindicato']}' LIMIT 1";
    $qrySindicato = mysql_query($sqlSindicato);
    $rowSindicato = mysql_fetch_assoc($qrySindicato);
    
    /**
     * SELECIONA OS FAVORECIDOS
     */
    $sqlFavorecidos = mysql_query("SELECT * FROM favorecido_pensao_assoc WHERE id_clt = {$id_clt}") or die(mysql_error());
    while ($rowFavorecidos = mysql_fetch_assoc($sqlFavorecidos)) {
        $arrayFavorecidos[] = $rowFavorecidos;
    }
    
    /**
     * SELECIONA OS SALARIO E DESCONTOS EM OUTRAS EMPRESAS
     */
    $sqlInssOutrasEmpresas = mysql_query("SELECT * FROM rh_inss_outras_empresas WHERE id_clt = {$id_clt}") or die(mysql_error());
    while ($rowInssOutrasEmpresas = mysql_fetch_assoc($sqlInssOutrasEmpresas)) {
        $arrayInssOutrasEmpresas[] = $rowInssOutrasEmpresas;
    }
    
    /**
     * SELECIONA OS DEPENDENTES
     */
    $sqlDependentes = mysql_query("SELECT * FROM dependentes WHERE (id_bolsista = '$id_clt' OR id_clt = '$id_clt')  AND id_projeto = '{$rowClt['id_projeto']}' AND contratacao = '{$rowClt['tipo_contratacao']}' LIMIT 1") or die("ERRO DEPENDENTE 1: " . mysql_error());
    while ($rowDependentes = mysql_fetch_assoc($sqlDependentes)) {
//        print_array($rowDependentes);
        $arrayDependentes['id_dependentes'] = $rowDependentes['id_dependentes'];
        if($rowDependentes['nome1'])$arrayDependentes[1] = array('nome' => $rowDependentes['nome1'], 'data' => $rowDependentes['data1'], 'cpf' => $rowDependentes['cpf1'], 'deficiencia' => $rowDependentes['portador_def1'], 'fac_tec' => $rowDependentes['dep1_cur_fac_ou_tec'], 'nao_ir_filho' => $rowDependentes['nao_ir_filho1']);
        if($rowDependentes['nome2'])$arrayDependentes[2] = array('nome' => $rowDependentes['nome2'], 'data' => $rowDependentes['data2'], 'cpf' => $rowDependentes['cpf2'], 'deficiencia' => $rowDependentes['portador_def2'], 'fac_tec' => $rowDependentes['dep2_cur_fac_ou_tec'], 'nao_ir_filho' => $rowDependentes['nao_ir_filho2']);
        if($rowDependentes['nome3'])$arrayDependentes[3] = array('nome' => $rowDependentes['nome3'], 'data' => $rowDependentes['data3'], 'cpf' => $rowDependentes['cpf3'], 'deficiencia' => $rowDependentes['portador_def3'], 'fac_tec' => $rowDependentes['dep3_cur_fac_ou_tec'], 'nao_ir_filho' => $rowDependentes['nao_ir_filho3']);
        if($rowDependentes['nome4'])$arrayDependentes[4] = array('nome' => $rowDependentes['nome4'], 'data' => $rowDependentes['data4'], 'cpf' => $rowDependentes['cpf4'], 'deficiencia' => $rowDependentes['portador_def4'], 'fac_tec' => $rowDependentes['dep4_cur_fac_ou_tec'], 'nao_ir_filho' => $rowDependentes['nao_ir_filho4']);
        if($rowDependentes['nome5'])$arrayDependentes[5] = array('nome' => $rowDependentes['nome5'], 'data' => $rowDependentes['data5'], 'cpf' => $rowDependentes['cpf5'], 'deficiencia' => $rowDependentes['portador_def5'], 'fac_tec' => $rowDependentes['dep5_cur_fac_ou_tec'], 'nao_ir_filho' => $rowDependentes['nao_ir_filho5']);
        if($rowDependentes['nome6'])$arrayDependentes[6] = array('nome' => $rowDependentes['nome6'], 'data' => $rowDependentes['data6'], 'cpf' => $rowDependentes['cpf6'], 'deficiencia' => $rowDependentes['portador_def6'], 'fac_tec' => $rowDependentes['dep6_cur_fac_ou_tec'], 'nao_ir_filho' => $rowDependentes['nao_ir_filho6']);
        if($rowDependentes['ddir_pai'])$arrayDependentes['ddir_pai'] = $rowDependentes['ddir_pai'];
        if($rowDependentes['ddir_mae'])$arrayDependentes['ddir_mae'] = $rowDependentes['ddir_mae'];
        if($rowDependentes['ddir_conjuge'])$arrayDependentes['ddir_conjuge'] = $rowDependentes['ddir_conjuge'];
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
    $sqlValeTransporte = mysql_query("SELECT id_clt, id_regiao, id_projeto, id_tarifa1, id_tarifa2, id_tarifa3, id_tarifa4, id_tarifa5, id_tarifa6, cartao1, cartao2 FROM rh_vale WHERE id_clt = '{$id_clt}' AND status_reg = 1 LIMIT 1;") or die("ERRO VT CLT: " . mysql_error());
    $rowValeTransporte = mysql_fetch_assoc($sqlValeTransporte);
//    print_array($arrayUnidadesClt);
    
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
$sqlProjetos = mysql_query("SELECT id_projeto, nome FROM projeto WHERE status_reg = 1 ORDER BY nome") or die("ERRO AO SELECIONAR OS PROJETOS: " . mysql_error());
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
    $arrayNacionalidades[$rowNacionalidades['codigo']] = $rowNacionalidades['nome'];
}

/**
 * SELECIONA AS ESCOLARIDADES
 */
$sqlEscolaridades = mysql_query("SELECT cod, id, nome FROM escolaridade WHERE status = 'on' ORDER BY nome");
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
 * SELECIONA OS BANCOS
 */
$sqlBancos = mysql_query("SELECT id_lista, banco FROM listabancos WHERE status_reg = 1");
$arrayBancos[""] = "-- SELECIONE --";
while ($rowBancos = mysql_fetch_assoc($sqlBancos)) {
    $arrayBancos[$rowBancos['id_lista']] = $rowBancos['banco'];
}

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
$sqlVT = mysql_query("SELECT A.id_tarifa, A.valor, A.tipo, A.itinerario, B.nome FROM rh_tarifas A LEFT JOIN rh_concessionarias B ON (A.id_concessionaria = 'B.id_concessionaria) WHERE A.id_regiao = {$usuario['id_regiao']} AND A.status_reg = '1'");
$arrayVT[0] = "-- NÃO POSSUI --";
while ($rowVT = mysql_fetch_assoc($sqlVT)) {
    $arrayVT[$rowVT['id_tarifa']] = "{$rowVT['valor']} - {$rowVT['tipo']} [{$rowVT['itinerario']}] - {$rowVT['nome']}";
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
    <div id="carregando" class="modal fade in" style="display: block;" aria-hidden="false"><div class="modal-dialog text-center no-margin-t" style="width: 100%; height:100%; margin-top: 0!important; padding-top: 25%;"><img src="http://f71iabassp.com/intranet/imagens/loading2.gif" style="height: 100px;"></div></div>
    <div class="modal-backdrop fade in"></div>
    <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - <?= $nome_pagina ?></small></h2></div>
                </div>
            </div>
            <form action="" class="form-horizontal" method="post" id="form_clt">
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
                                    <input type="text" class='data form-control validate[required]' name='data_importacao' id='data_importacao' value="<?= implode('/', array_reverse( explode('-', $rowClt['data_importacao']))) ?>">
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
                                        <input type="text" class='form-control validate[required]' name='unidade[1][porcentagem]' id='unidade_porcentagem1' value="<?= $arrayUnidadesClt[0]['porcentagem'] ?>">
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
                                    <?= montaSelect($arrayProjetos, $arrayUnidadesClt[1]['id_projeto'], "class='unidade_projeto form-control validate[required]' name='unidade[2][id_projeto]' id='unidade_projeto2' data-ordem='2' data-unidade='" . $arrayUnidadesClt[1]['id_unidade'] . "' ") ?>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Unidade 2:</div>
                                <div class="" id="div_unidade_projeto2">
                                    <?= montaSelect(array('SELECIONE O PROJETO 2'), null, "class='form-control validate[required]' name='unidade[2][id_unidade]' id='id_unidade2'") ?>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">&nbsp;</div>
                                <div class="">
                                    <div class="input-group">
                                        <input type="text" class='form-control validate[required]' name='unidade[2][porcentagem]' id='unidade_porcentagem2' value="<?= $arrayUnidadesClt[1]['porcentagem'] ?>">
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
                                    <?= montaSelect($arrayProjetos, $arrayUnidadesClt[2]['id_projeto'], "class='unidade_projeto form-control validate[required]' name='unidade[3][id_projeto]' id='unidade_projeto3' data-ordem='3' data-unidade='" . $arrayUnidadesClt[2]['id_unidade'] . "' ") ?>
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
                                        <input type="text" class='form-control validate[required]' name='unidade[3][porcentagem]' id='unidade_porcentagem2' value="<?= $arrayUnidadesClt[2]['porcentagem'] ?>">
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
                                    <input type="text" class="form-control validate[required]" name="nome" id="nome" value="<?= $rowClt['nome'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Data de Nascimento:</div>
                                <div class="">
                                    <input type="text" class="data form-control validate[required]" name="data_nasci" id="data_nasci" value="<?= implode('/', array_reverse(explode('-', $rowClt['data_nasci']))) ?>">
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
                                    <?= montaSelect($arrayNacionalidades, $rowClt['nacionalidade'], "class='form-control validate[required]' name='nacionalidade' id='nacionalidade'") ?>
                                </div>
                            </div>
                            <div class="col-sm-2 div_nacionalidade">
                                <div class="text-bold text-sm" style="margin-bottom: 3px;">Data de chegada ao país:</div>
                                <div class="">
                                    <input type="text" class='data form-control' name='dtChegadaPais' id='dtChegadaPais' value="<?= implode('/', array_reverse(explode('-',$rowClt['dtChegadaPais']))) ?>" >
                                </div>
                            </div>
                            <div class="col-sm-4 div_nacionalidade">
                                <div class="text-bold">País de Nascimento:</div>
                                <div class="">
                                    <div class="input-group">
                                        <input type="text" class="pais form-control validate[required]" data-tipo="pais_nasc" name="pais_nasc" id="pais_nasc" value="<?= $rowClt['pais_nasc'] ?>">
                                        <div class="input-group-addon">Cod.</div>
                                        <input type="text" class="form-control validate[required]" name="id_pais_nasc" id="id_pais_nasc" value="<?= $rowClt['id_pais_nasc'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 div_nacionalidade">
                                <div class="text-bold">País de Nacionalidade:</div>
                                <div class="">
                                    <div class="input-group">
                                        <input type="text" class="pais form-control validate[required]" data-tipo="pais_nacionalidade" name="pais_nacionalidade" id="pais_nacionalidade" value="<?= $rowClt['pais_nacionalidade'] ?>">
                                        <div class="input-group-addon">Cod.</div>
                                        <input type="text" class="form-control validate[required]" name="id_pais_nacionalidade" id="id_pais_nacionalidade" value="<?= $rowClt['id_pais_nacionalidade'] ?>">
                                    </div>
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
                                <div class="text-bold">Endedreço:</div>
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
                                        <div class="input-group-addon"><input id="estuda_sim" name="estuda" type="radio" class="reset" value="sim" <?= (!empty($rowClt['estuda'])) ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="estuda_sim">SIM</label>
                                        <div class="input-group-addon"><input id="estuda_nao" name="estuda" type="radio" class="reset" value="nao" <?= (empty($rowClt['estuda'])) ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="estuda_nao">NÃO</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Término em:</div>
                                <div class="">
                                    <input type="text" class="data form-control" name="data_escola" id="data_escola" value="<?= ($rowClt['data_escola'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['data_escola']))) ?>">
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
                                    <input type="text" class='form-control' name='pai' id='pai' value="<?= $rowClt['pai'] ?>">
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
                                <div class="">
                                    <input type="text" class='form-control' name='nacionalidade_pai' id='nacionalidade_pai' value="<?= $rowClt['nacionalidade_pai'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Data de Nascimento:</div>
                                <div class="">
                                    <input type="text" class='data form-control' name='data_nasc_pai' id='data_nasc_pai' value="<?= ($rowClt['data_nasc_pai'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['data_nasc_pai']))) ?>">
                                </div>
                            </div>
                        </div>
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
                                    <input type="text" class='form-control' name='nacionalidade_mae' id='nacionalidade_mae' value="<?= $rowClt['nacionalidade_mae'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Data de Nascimento:</div>
                                <div class="">
                                    <input type="text" class='data form-control' name='data_nasc_mae' id='data_nasc_mae' value="<?= ($rowClt['data_nasc_mae'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['data_nasc_mae']))) ?>">
                                </div>
                            </div>
                        </div>
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
                                    <input type="text" class='data form-control' name='data_nasc_conjuge' id='data_nasc_conjuge' value="<?= ($rowClt['data_nasc_conjuge'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-', $rowClt['data_nasc_conjuge']))) ?>">
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">Filhos</div>
                            <div class="panel-body">
                                <input type="hidden" class='form-control' name='dependente[id_dependentes]' value="<?= $arrayDependentes['id_dependentes'] ?>">
                                <?php for($i = 1; $i <= 6; $i++) { ?>
                                    <div class="form-group">
                                        <div class="col-sm-5">
                                            <div class="text-bold">Nome:</div>
                                            <div class="">
                                                <input type="text" class='form-control' name='dependente[<?= $i ?>][nome]' id='nome_filho<?= $i ?>' value="<?= $arrayDependentes[$i]['nome'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="text-bold">CPF:</div>
                                            <div class="">
                                                <input type="text" class='cpf form-control validate[custom[cpf]]' name='dependente[<?= $i ?>][cpf]' id='cpf_filho<?= $i ?>' value="<?= $arrayDependentes[$i]['cpf'] ?>">
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
                                                <input type="text" class='data form-control' name='dependente[<?= $i ?>][data_nasc]' id='data_nasc_filho<?= $i ?>' value="<?= implode('/', array_reverse(explode('-', $arrayDependentes[$i]['data']))) ?>">
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
                                    </div>
                                    <hr>
                                <?php } ?>
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
                                    <input type="text" name="rg" id="rg" class="form-control validate[required]" value="<?= $rowClt['rg'] ?>">
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
                                    <?= montaSelect($arrayUfs, $rowClt['uf_rg'], 'class="form-control" id="uf_rg" name="uf_rg"') ?>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Data Expedição:</div>
                                <div class="">
                                    <input type="text" name="data_rg" id="data_rg" class="data form-control validate[required]" value="<?= ($rowClt['data_rg'] == '0000-00-00') ? null : implode('/',array_reverse(explode('-',$rowClt['data_rg']))) ?>">
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
                                    <input type="text" name="conselho" id="conselho" class="form-control validate[required]" value="<?= $rowClt['conselho'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Data de emissão:</div>
                                <div class="">
                                    <input type="text" name="data_emissao" id="data_emissao" class="data form-control validate[required]" value="<?= ($rowClt['data_emissao'] == '0000-00-00') ? null : implode('/',array_reverse(explode('-',$rowClt['data_emissao']))) ?>">
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
                                    <?= montaSelect($arrayUfs, $rowClt['uf_ctps'], 'class="form-control" id="uf_ctps" name="uf_ctps"') ?>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold">Data carteira de Trabalho:</div>
                                <div class="">
                                    <input type="text" name="data_ctps" id="data_ctps" class="data form-control validate[required]" value="<?= ($rowClt['data_ctps'] == '0000-00-00') ? null : implode('/',array_reverse(explode('-',$rowClt['data_ctps']))) ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">Nº Título de Eleitor:</div>
                                <div class="">
                                    <input type="text" name="titulo" id="titulo" class="form-control validate[required]" value="<?= $rowClt['titulo'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Zona:</div>
                                <div class="">
                                    <input type="text" name="zona" id="zona" class="form-control validate[required]" value="<?= $rowClt['zona'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Seção:</div>
                                <div class="">
                                    <input type="text" name="secao" id="secao" class="form-control validate[required]" value="<?= $rowClt['secao'] ?>">
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
                                    <input type="text" name="dada_pis" id="dada_pis" class="data form-control validate[required]" value="<?= ($rowClt['dada_pis'] == '0000-00-00') ? null : implode('/',array_reverse(explode('-',$rowClt['dada_pis']))) ?>">
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
                                        <label class="form-control" for="tipo_desconto_inss1">Suspenção de Recolhimento</label>
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
                                            <input type="text" name="outra_empresa[<?= $countInssOutrasEmpresas ?>][inicio]" class="data form-control" value='<?= $objInssOutrasEmpresas->getInicio('d/m/Y') ?>'>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Fim:</div>
                                        <div class="">
                                            <input type="text" name="outra_empresa[<?= $countInssOutrasEmpresas ?>][fim]" class="data form-control" value='<?= $objInssOutrasEmpresas->getFim('d/m/Y') ?>'>
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
                                            <input type="text" name="outra_empresa[0][inicio]" class="data form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="text-bold">Fim:</div>
                                        <div class="">
                                            <input type="text" name="outra_empresa[0][fim]" class="data form-control">
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
                                        <input type="hidden" class="form-control" id="cartao2" name="id_vale" value="<?= $rowValeTransporte['id_vale'] ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group div_transporte">
                            <div class="col-sm-4">
                                <div class="text-bold">Selecione 1:</div>
                                <div class="">
                                    <?= montaSelect($arrayVT, $rowValeTransporte['vale1'], 'class="form-control" id="vale1" name="vale1"') ?>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Selecione 2:</div>
                                <div class="">
                                    <?= montaSelect($arrayVT, $rowValeTransporte['vale2'], 'class="form-control" id="vale2" name="vale2"') ?>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Selecione 3:</div>
                                <div class="">
                                    <?= montaSelect($arrayVT, $rowValeTransporte['vale3'], 'class="form-control" id="vale3" name="vale3"') ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group div_transporte">
                            <div class="col-sm-4">
                                <div class="text-bold">Selecione 4:</div>
                                <div class="">
                                    <?= montaSelect($arrayVT, $rowValeTransporte['vale4'], 'class="form-control" id="vale4" name="vale4"') ?>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Selecione 5:</div>
                                <div class="">
                                    <?= montaSelect($arrayVT, $rowValeTransporte['vale5'], 'class="form-control" id="vale5" name="vale5"') ?>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Selecione 6:</div>
                                <div class="">
                                    <?= montaSelect($arrayVT, $rowValeTransporte['vale6'], 'class="form-control" id="vale6" name="vale6"') ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group div_transporte">
                            <div class="col-sm-4">
                                <div class="text-bold">Número Cartão 1:</div>
                                <div class="">
                                    <input type="text" class="form-control" id="cartao1" name="cartao1" value="<?= $rowValeTransporte['cartao1'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Numero Cartão 2:</div>
                                <div class="">
                                    <input type="text" class="form-control" id="cartao2" name="cartao2" value="<?= $rowValeTransporte['cartao2'] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold">&nbsp;</div>
                                <div class="">
                                    <div class="input-group">
                                        <div class="input-group-addon"><input type="checkbox" class="" name="pensao_alimenticia" id="pensao" value="1" <?= ($rowClt['pensao_alimenticia']) ? 'checked' : null ?> ></div>
                                        <label class="form-control text-default" for="pensao">Pensão Alimentícias:</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <legend class="div_pensao">Favorecidos Pensão Alimentícia<button type="button" class="div_pensao btn btn-success pull-right" id="add_favorecido"><i class="fa fa-plus"></i></button><div class="clear"></div></legend>
                        <div class="panel panel-default div_pensao" id="div_favorecidos">
                            <?php if(count($arrayFavorecidos) > 0) { ?>
                                <?php foreach ($arrayFavorecidos as $key => $value) { ?>
                                <div class="panel-footer">
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <div class="text-bold">Nome:</div>
                                            <div class="">
                                                <input type="hidden" name="favorecidos_pensao[<?= $key ?>][id]" class="form-control" value="<?= $value['id'] ?>">
                                                <input type="text" name="favorecidos_pensao[<?= $key ?>][favorecido]" class="form-control" value="<?= $value['favorecido'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="text-bold">CPF:</div>
                                            <div class="">
                                                <input type="text" name="favorecidos_pensao[<?= $key ?>][cpf]" class="cpf form-control validate[required,custom[cpf]]" value="<?= $value['cpf'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="text-bold">Aliquota:</div>
                                            <div class="">
                                                <input type="text" name="favorecidos_pensao[<?= $key ?>][aliquota]" class="aliquota form-control" placeholder="0.000" value="<?= $value['aliquota'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="text-bold">Oficio:</div>
                                            <div class="">
                                                <input type="text" name="favorecidos_pensao[<?= $key ?>][oficio]" class="form-control" value="<?= $value['oficio'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-3">
                                            <div class="text-bold">Banco:</div>
                                            <div class="">
                                                <?= montaSelect($arrayBancos, $value['id_lista_banco'], 'class="form-control" name="favorecidos_pensao[' . $key . '][id_lista_banco]"') ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="text-bold">Agência:</div>
                                            <div class="">
                                                <input type="text" name="favorecidos_pensao[<?= $key ?>][agencia]" class="form-control" value="<?= $value['agencia'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="text-bold">Conta:</div>
                                            <div class="">
                                                <input type="text" name="favorecidos_pensao[<?= $key ?>][conta]" class="form-control" value="<?= $value['conta'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="text-bold">&nbsp;</div>
                                            <div class="">
                                                <button type="button" class="btn btn-danger deletar_favorecido" data-id="<?= $value['id'] ?>"><i class="fa fa-trash-o"></i> Excluir Favorecido</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            <?php } else { ?>
                            <div class="panel-footer">
                                <div class="form-group">
                                    <div class="col-sm-3">
                                        <div class="text-bold">Nome:</div>
                                        <div class="">
                                            <input type="hidden" name="favorecidos_pensao[0][id]" class="form-control" value="">
                                            <input type="text" name="favorecidos_pensao[0][favorecido]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="text-bold">CPF:</div>
                                        <div class="">
                                            <input type="text" name="favorecidos_pensao[0][cpf]" class="cpf form-control validate[required,custom[cpf]]">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="text-bold">Aliquota:</div>
                                        <div class="">
                                            <input type="text" name="favorecidos_pensao[0][aliquota]" class="aliquota form-control" placeholder="0.000">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="text-bold">Oficio:</div>
                                        <div class="">
                                            <input type="text" name="favorecidos_pensao[0][oficio]" class="form-control" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-3">
                                        <div class="text-bold">Banco:</div>
                                        <div class="">
                                            <?= montaSelect($arrayBancos, null, 'class="form-control" name="favorecidos_pensao[0][id_lista_banco]"') ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="text-bold">Agência:</div>
                                        <div class="">
                                            <input type="text" name="favorecidos_pensao[0][agencia]" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="text-bold">Conta:</div>
                                        <div class="">
                                            <input type="text" name="favorecidos_pensao[0][conta]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="text-bold">&nbsp;</div>
                                        <div class="">
                                            <button type="button" class="btn btn-danger deletar_favorecido"><i class="fa fa-trash-o"></i> Excluir Favorecido</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="panel-heading border-t text-bold">DADOS BANCÁRIOS</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-sm-3">
                                <div class="text-bold">Banco:</div>
                                <div class="">
                                    <?= montaSelect($arrayBancos, $rowClt['banco'], 'class="form-control" id="banco" name="banco"') ?>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold">Agência:</div>
                                <div class="">
                                    <input type="text" name="agencia" id="agencia" class="form-control" value="<?= $rowClt['agencia'] ?>">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-bold">Conta:</div>
                                <div class="">
                                    <div class="input-group">
                                        <input type="text" name="conta" id="conta" class="form-control" value="<?= $rowClt['conta'] ?>">
                                        <div class="input-group-addon"><input id="conta_salario" name="tipo_conta" type="radio" class="reset" value="salario" <?= ($rowClt['tipo_conta'] == 'salario') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="conta_salario"><!--Conta -->Salário</label>
                                        <div class="input-group-addon"><input id="conta_corrente" name="tipo_conta" type="radio" class="reset" value="corrente" <?= ($rowClt['tipo_conta'] == 'corrente') ? 'checked="checked"' : null ?>></div>
                                        <label class="form-control" for="conta_corrente"><!--Conta -->Corrente</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="text-bold text-sm" style="margin-bottom: 3px;">Nome do Banco:<i class="text-danger"> (caso fora da lista acima)</i></div>
                                <div class="">
                                    <input type="text" name="nome_banco" id="nome_banco" class="form-control" value="<?= $rowClt['nome_banco'] ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading border-t text-bold">DADOS FINANCEIROS E DE CONTRATO</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-sm-2">
                                <div class="text-bold">Data de Entrada:</div>
                                <div class="">
                                    <input type="text" name="data_entrada" id="data_entrada" class="data form-control validate[required]" value="<?= ($rowClt['data_entrada'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-',$rowClt['data_entrada']))) ?>">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="text-bold text-sm" style="margin-bottom: 3px;">Data Exame Admissional:</div>
                                <div class="">
                                    <input type="text" name="data_exame" id="data_exame" class="data form-control validate[required]" value="<?= ($rowClt['data_exame'] == '0000-00-00') ? null : implode('/', array_reverse(explode('-',$rowClt['data_exame']))) ?>">
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
                        <div class="alert alert-warning">NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php if($id_clt){ ?><input name="id_clt" type="hidden" value="<?= $id_clt ?>"><?php } ?>
                        <button type="type" class="btn btn-primary" name="<?= $action ?>"><i class="fa fa-save"></i> SALVAR</button>
                    </div>
                </div>
            </form>
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
        <script>
            $(function() {
                $(".valor").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
                $(".aliquota").maskMoney({ precision: 3, allowNegative: true, thousands:'', decimal:'.' })
                
                $('.data').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '2005:c+1',
                    beforeShow: function () {
                        setTimeout(function () {
                            $('.ui-datepicker').css('z-index', 5010);
                        }, 0);
                    }
                });
                // máscaras
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
                        $(".div_desconto_inss, #div_dados_outra_empresa").hide();
                    }
                });
                $('#desconto_inss').trigger('change');
                
                $("body").on('change', '#transporte', function () {
                    if ($(this).prop('checked')) { 
                        $(".div_transporte").show();
                    } else {
                        $(".div_transporte").hide();
                    }
                });
                $('#transporte').trigger('change');
                
                $("body").on('change', '#pensao', function () {
                    if ($(this).prop('checked')) { 
                        $(".div_pensao").show();
                    } else {
                        $(".div_pensao").hide();
                    }
                });
                $('#pensao').trigger('change');
                
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
                                    $('<input>', { type: "text", name: "outra_empresa["+n+"][inicio]", class: "data form-control" })
                                )
                            ),
                            $('<div>', { class: "col-sm-2" }).append(
                                $('<div>', { class: "text-bold", html: "Fim:" }),
                                $('<div>', { class: "" }).append(
                                    $('<input>', { type: "text", name: "outra_empresa["+n+"][fim]", class: "data form-control" })
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
                        yearRange: '2005:c+1',
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
                                        $('<input>', { type: "text", name: "favorecidos_pensao[" + n + "][aliquota]", class: "aliquota form-control", placeholder: "0.000", value: "" })
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
                                            '<?php foreach ($arrayBancos as $key => $value) { echo "<option value=\"{$key}\">{$value}</option>"; } ?>'
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
                    $('#div_favorecidos').find('.aliquota').maskMoney({ precision: 3, allowNegative: true, thousands:'', decimal:'.' })
                    $('#div_favorecidos').find(".cpf").mask("999.999.999-99");
                });

                $('body').on('click', '.deletar_favorecido', function(){
                    if($(this).data('id')){
                        console.log("CONFIRMAÇÃO");
                    } else {
                        $(this).parent().parent().parent().parent().remove();
                    }
                });
                
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
                    if ($(this).val() != '10' && $(this).val() != '') {
                        $(".div_nacionalidade").show().find('input').addClass('validate[required]');
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
                        });
                    } else {
                        $(".div_nacionalidade").hide().find('input').removeClass('validate[required]');
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
                    console.log(uf);
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
                    $this.after('<img src="../img_menu_principal/loader_pequeno.gif" alt="buncando endereço..." style="position: absolute; margin-top: -7px;" id="img_load_cep" />');
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
                
                
            });
        </script>
    </body>
</html>