<?php
include('../../conn.php');
include('../../funcoes.php');
include('../../wfunction.php');
include('../../classes/clt.php');
include_once "../../classes/LogClass.php";
include('../../classes/abreviacao.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../classes/valor_proporcional.php');

//if($_COOKIE['logado'] == 179){
//    echo "Deus é fiel ";
//}

$dados_recisao = array();
$programadores = array(179, 158, 260, 257, 258, 275);

// Recebendo a Variável Criptografada
if(!empty($flagFichaParaRescisao)){
    $regiao = $flagRegiao;
    $id_clt = $flagId_clt;
    $id = $flagIdRescisao;
}else{
    list($regiao, $id_clt, $id) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
}

//if($_COOKIE['logado'] == 179){
//    
//    echo $id;
//}

$status = "1";
if (isset($_REQUEST['reitegracao'])) {
    $status = "0";
}

// Consulta da Rescisão
$qr_rescisao = mysql_query("SELECT * FROM rh_recisao WHERE id_recisao = '$id' AND status = '{$status}'");
$row_rescisao = mysql_fetch_array($qr_rescisao);

if ($_COOKIE['logado'] == 353) {
    print_array($row_rescisao);
}

$complementar = ($row_rescisao['rescisao_complementar'] == 1) ? 'COMPLEMENTAR' : '';

if ($row_rescisao['aviso'] == 'trabalhado') {

    $tipo_aviso = 'Aviso Prévio trabalhado';
} else {
    $tipo_aviso = 'Aviso Prévio indenizado';
}


// Tipo da Rescisão
$qr_motivo = mysql_query("SELECT * FROM rhstatus WHERE codigo = '{$row_rescisao['motivo']}'");
$row_motivo = mysql_fetch_array($qr_motivo);

// Informações do Participante
$Clt = new clt();
$Clt->MostraClt($id_clt);
$pis = $Clt->pis;
$nome = $Clt->nome;
$codigo = $Clt->campo3;
$bairro = $Clt->bairro;
$cidade = $Clt->cidade;
$uf = $Clt->uf;
$cep = $Clt->cep;
$cartrab = $Clt->campo1;
$serie_cartrab = $Clt->serie_ctps;
$uf_cartrab = $Clt->uf_ctps;
$cpf = $Clt->cpf;
$data_nasci = $Clt->data_nasci;
$mae = $Clt->mae;
$data_entrada = $Clt->data_entrada;
$data_demi = $Clt->data_demi;
$rh_sindicato = $Clt->rh_sindicato;
$id_projeto_clt = $Clt->id_projeto;

/**
 * By Ramon 10/01/2017
 * A pedido do italo por uma validação para deixar o campo da mãe de vermelho caso não venha preenchido
 */
//CALCULO DE DIAS PARA LEI 12506
$qtd_ano_12506 = diferencaData($data_entrada, $data_demi, 'y');

$qtd_dias_12506 = 0;

for ($i = 0; $i < $qtd_ano_12506; $i++) {
    $qtd_dias_12506 += 3;
}

if (!empty($Clt->endereco)) {
    $dadosEndereco[] = $Clt->endereco;
}
if (!empty($Clt->numero)) {
    $dadosEndereco[] = $Clt->numero;
}
if (!empty($Clt->complemento)) {
    $dadosEndereco[] = $Clt->complemento;
}
$endereco = implode(', ', $dadosEndereco);

// Sindicato do Participante
$qr_sindicato = mysql_query("SELECT * FROM rhsindicato WHERE id_sindicato = '$rh_sindicato'");
$row_sindicato = mysql_fetch_assoc($qr_sindicato);

$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]' ");
$row_master = mysql_fetch_assoc($qr_master);

if ($row_regiao['id_master'] == 1) {
// Informações da Empresa
    $qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$regiao' AND id_projeto = '$id_projeto_clt'");
    $row_empresa = mysql_fetch_assoc($qr_empresa);

    $cnpj_empresa = $row_empresa['cnpj'];
    $razao_empresa = $row_empresa['razao'];
    $logradouro = explode('-', $row_empresa['endereco']);
    $endereco_empresa = $logradouro[0];
    $municipio_empresa = $row_empresa['cidade'];
    $uf_empresa = $row_empresa['uf'];
    $cep_empresa = $row_empresa['cep'];
    $bairro_empresa = $row_empresa['bairro'];
    $cnae = $row_empresa['cnae'];
} else {

    $Clt->EmpresadoCLT($id_clt);
    $cnpj_empresa = $Clt->cnpj;
    $razao_empresa = $Clt->razao;
    $endereco_empresa = $Clt->endereco;
    $cep_empresa = $Clt->cep;

    list($endereco_empresa, $bairro_empresa, $municipio_empresa, $uf_empresa) = explode(' - ', $endereco_empresa);
}

// Aviso Prévio
if ($row_rescisao['motivo'] == 65) {
    $aviso_previo_debito = $row_rescisao['aviso_valor'];
} else {
    $aviso_previo_credito = $row_rescisao['aviso_valor'];
}

$cod_sindicato = (empty($row_sindicato['codigo_sindical'])) ? "999.000.000.00000-3" : $row_sindicato['codigo_sindical'];



// Multa de Atraso

if ($row_rescisao['motivo'] == '64') {
    $multa_479 = $row_rescisao['a479'];
    $multa_480 = NULL;
} elseif ($row_rescisao['motivo'] == '63') {
    $multa_479 = NULL;
    $multa_480 = $row_rescisao['a480'];
}

function verificaQuantidade($tipoQnt, $qnt, $qntHoras) {
    if (!empty($tipoQnt)) {
        switch ($tipoQnt) {
            case 1: $qntFinal = substr($qntHoras, 0, 6) . ' Horas';
                break;
            case 2: $qntFinal = $qnt . ' Dias';
        }

        return $qntFinal;
    } else {
        return '';
    }
}

/* * ***ARRAY DE CAMPOS DE MOVIMENTOS OBRIGATÓRIOS 22/05/2015******** */
/* * ****************MESMO COM VALOR ZERADO************************** */


//GRATIFICAÇÃO
//    $dados_recisao[52] = array(
//        "movimento" => "Gratificação",
//        "tipo" => "CREDITO",
//        "valor" => 0.00
//    );
//ADICIONAL NOTURNO
//    $dados_recisao[55] = array(
//        "movimento" => "Adicional Noturno",
//        "tipo" => "CREDITO",
//        "valor" => 0.00
//    );
//    
//    //HORA EXTRA    
//    $dados_recisao[56] = array(
//        "movimento" => "Horas Extras",
//        "tipo" => "CREDITO",
//        "valor" => 0.00
//    );
//DSR
$dados_recisao[58] = array(
    "movimento" => "Descanso Semanal Remunerado (DSR)",
    "tipo" => "CREDITO",
    "valor" => 0.00
);


/* * **************************************************************** */

/* * **************ARRAY DE MOVIMENTOS 20/05/2015******************** */
//SALDO DE SALÁRIO
$dados_recisao[50] = array(
    "movimento" => "Saldo de {$row_rescisao['dias_saldo']} dias Salário",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['saldo_salario']
);

//COMISSÃO
if (!is_null($row_rescisao['comissao']) && $row_rescisao['comissao'] > 0) {
    $dados_recisao[51] = array(
        "movimento" => "Comissões",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['comissao']
    );
}

//INSALUBRIDADE
if (!is_null($row_rescisao['insalubridade']) && $row_rescisao['insalubridade'] > 0) {
    $dados_recisao[53] = array(
        "movimento" => "Adicional de Insalubridade",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['insalubridade']
    );
}

//PERICULOSIDADE
//$dados_recisao[54] = array(
//    "movimento" => "Adicional de Periculosidade",
//    "tipo" => "CREDITO",
//    "valor" => 0
//);
//GORJETA
//$dados_recisao[57] = array(
//    "movimento" => "Gorjeta",
//    "tipo" => "CREDITO",
//    "valor" => 0.00
//);
//REFLEXO DO DSR 
$dados_recisao[59] = array(
    "movimento" => 'Reflexo do "DSR" sobre Salário Variável',
    "tipo" => "CREDITO",
    "valor" => 0.00
);

//MULTA 477
$dados_recisao[60] = array(
    "movimento" => 'Multa Art. 477, § 8º/CLT',
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['a477']
);

//SALARIO FAMILIA
$dados_recisao[62] = array(
    "movimento" => 'Salário-Família',
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['sal_familia']
);

/*
 * 11/05/2016
 * by: MAX
 * SOLICITADO PELO ÍTALO(IABAS)
 * PQ ESTAVA TRAZENDO NUM CAMPO COM NUMERO 0(ZERO)
 * ENTÃO ELE DISTRIBUI ESSES MOVIMENTOS
 * EM ALGUNS CAMPOS DA RESCISÃO
 */

/**
 * 21/06/2016
 * by: Ramon
 * ALANA SOLICITOU QUE TIRASSE A SOMA DA MÉDIA DO 13 E DAS FÉRIAS
 * POIS O VALOR CALCULADO DE 13 E DE FÉRIAS JÁ ESTÁ CONSIDERANDO A MÉDIA LANÇADA
 * COMENTANDO O CÓDIGO QUE O ITALO PEDIU PARA ACRECENTAR
 */
$arrayMovMediasFeriasEDt = array(384, 385, 386, 387, 388, 408, 409, 410);


/*
 * 04/10/2016
 * Renato
 * TIRANDO OS MOVIMENTOS DE MEDIA PARA AS RESCISÕES COMPLEMENTARES
 */
if ($row_rescisao['rescisao_complementar'] == 0) {

    $sql_movTemp = "SELECT B.descicao, B.id_mov, A.valor,B.categoria, C.tipo_movimento, C.valor_movimento, B.campo_rescisao, B.percentual, A.tipo_qnt, A.qnt, C.qnt_horas
                    FROM rh_movimentos_rescisao AS A
                    LEFT JOIN rh_movimentos AS B ON(A.id_mov = B.id_mov)
                    LEFT JOIN (SELECT * FROM rh_movimentos_clt AS A WHERE A.id_clt = '{$row_rescisao[id_clt]}' AND A.status = 1) AS C ON(B.id_mov = C.id_mov)
                    WHERE A.id_clt = '{$row_rescisao['id_clt']}' 
                    AND A.id_mov IN(" . implode(',', $arrayMovMediasFeriasEDt) . ")
                    AND A.status = 1 GROUP BY A.id_mov";
    $qr_movimentosTemp = mysql_query($sql_movTemp) or die(mysql_error());

    if ($_COOKIE['debug'] == 666) {
        echo '/////////////////////////////$sql_movTemp///////////////////////';
        print_array($sql_movTemp);
    }
}
/**
 * By Ramon 19/07/2016
 * A Alana lançou médias de ferias proporcionais, porem a pessoa teve 12 avos e na hora de GRAVAR a rescisão 
 * os valores q antes apareciam como proporcionais, terão q aparecer como férias VENCIDAS por conta dos 12 avos...
 * Então as médias q foram lançadas para calculo como ferias proporcionais agora vão pertencer as férias Vencidas...
 * Vamos a GAMBIARRA DA PROJEÇÃO
 * (MEDIA FERIAS PROJEÇAO AVISO PREVIO)
 */
while ($row_movimentosTemp = mysql_fetch_assoc($qr_movimentosTemp)) {

//    if(in_array($_COOKIE['logado'],$programadores)){
//        echo "**********************************************<br>";
//        echo "<pre>";
//            print_r($row_movimentosTemp);
//        echo "</pre>";
//    }

    /* -------------BLOCO 13 SALARIO------------- */
    //MEDIA 13º PROJEÇAO AVISO PREVIO (junto com o campo 70)
    if ($row_movimentosTemp['id_mov'] == 410) {
        $row_rescisao['terceiro_ss'] = $row_rescisao['terceiro_ss'] + $row_movimentosTemp['valor'];
    }

    //MEDIA SOBRE 13º SALARIO
    if ($row_movimentosTemp['id_mov'] == 384) {
        $row_rescisao['dt_salario'] = $row_rescisao['dt_salario'] + $row_movimentosTemp['valor'];
    }

    /* -------------BLOCO FÉRIAS VENCIDAS------------- */
    //MEDIA SOBRE FERIAS INDENIZADAS
    if ($row_movimentosTemp['id_mov'] == 385) {
        $row_rescisao['ferias_vencidas'] = $row_rescisao['ferias_vencidas'] + $row_movimentosTemp['valor'];
    }

    //MEDIA FERIAS PROJEÇAO AVISO PREVIO (junto com o campo 71)
    if ($row_movimentosTemp['id_mov'] == 408) {
        $row_rescisao['ferias_aviso_indenizado'] = $row_rescisao['ferias_aviso_indenizado'] + $row_movimentosTemp['valor'];
    }

    //1/3 MEDIA FERIAS PROJEÇãO AVISO PREVIO (junto com o campo 75)
    if ($row_movimentosTemp['id_mov'] == 409) {
        $row_rescisao['umterco_ferias_aviso_indenizado'] = $row_rescisao['umterco_ferias_aviso_indenizado'] + $row_movimentosTemp['valor'];
    }

    /* -------------BLOCO FÉRIAS PROPORCIONAIS------------- */
    //MEDIA SOBRE FERIAS PROPORCIONAIS
    if ($row_movimentosTemp['id_mov'] == 387) {
        $row_rescisao['ferias_pr'] = $row_rescisao['ferias_pr'] + $row_movimentosTemp['valor'];
    }

    //1/3 DE MEDIA SOBRE FERIAS INDENIZADAS
    if ($row_movimentosTemp['id_mov'] == 386) {
        $row_rescisao['umterco_fp'] = $row_rescisao['umterco_fp'] + $row_movimentosTemp['valor'];
    }

    //1/3 DE MEDIA SOBRE FERIAS PROPORCIONAIS
    if ($row_movimentosTemp['id_mov'] == 388) {
        $row_rescisao['umterco_fp'] = $row_rescisao['umterco_fp'] + $row_movimentosTemp['valor'];
    }
}

//DECIMO TERCEIRO
$avos_dt = sprintf('%02d', $row_rescisao['avos_dt']);
$dados_recisao[63] = array(
    "movimento" => "13º Salário Proporcional {$avos_dt}/12 avos",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['dt_salario']
);

//DECIMO TERCEIRO EXERCICIO
$dados_recisao[64] = array(
    "movimento" => '13º Salário Exercício 0/12 avos',
    "tipo" => "CREDITO",
    "valor" => 0.00
);

/**
 * By Ramon 19/07/2016
 * A pedido da Alana juntando alguns movimentos no mesmo campo de AVISO PRÉVIO
 * TEXTO ENVIADO SKYPE:
 * "a lei 12.506 e médias sobre aviso prévio deve aparecer junto com o aviso prévio indenizado no termo"
 */
// AVISO PREVIO
if ($row_rescisao['fator'] == 'empregado' && $row_rescisao['aviso'] == 'PAGO pelo ') {
    $aviso_previo_debito = $row_rescisao['aviso_valor'];
    $dados_recisao[103] = array(
        "movimento" => "Aviso-Prévio Indenizado",
        "tipo" => "DEBITO",
        "valor" => $row_rescisao['aviso_valor']
    );
} else {
    $aviso_previo_credito = $row_rescisao['aviso_valor'];
    $dados_recisao[69] = array(
        "movimento" => "Aviso-Prévio Indenizado",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['aviso_valor'] + $row_rescisao['lei_12_506']
    );
}

///DISPENSA ANTES DO TERMINO DE CONTRATO PELA EMPRESA
if ($row_rescisao['motivo'] != '63' and $row_rescisao['motivo'] != '65') {
    $multa_479 = $row_rescisao['a479'];
    $dados_recisao[61] = array(
        "movimento" => "Multa Art. 479/CLT",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['a479']
    );
} else {
    //INVERSO
    $multa_480 = $row_rescisao['a479'];
    $dados_recisao[104] = array(
        "movimento" => "Multa Art. 480/CLT",
        "tipo" => "DEBITO",
        "valor" => $row_rescisao['a480']
    );
}

/**
 * By Ramon 19/07/2016
 * A pedido da Alana juntando alguns movimentos no mesmo campo de AVISO PRÉVIO
 * VERIFICANDO PRIMEIRO SE TEM AVISO PREVIO INDENIZADO
 * SÓ VAI CRIAR INFORMAÇÃO NA MATRIZ DE $dados_recisao PARA A lei SE NÃO TIVER VALOR NO AVISO PRÉVIO
 * POIS SE TIVER O AVISO JA ESTÁ SOMADO A LEI
 */
//LEI 12.506
if (!isset($dados_recisao[69]["valor"])) {
    $dados_recisao[95] = array(
        //"movimento" => "Lei 12.506 ({$row_rescisao['qnt_dias_lei_12_506']} dias)",
        "movimento" => "Lei 12.506 ({$qtd_dias_12506} dias)",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['lei_12_506']
    );
}

//PENSÃO ALIMENTÍCIA 
//$dados_recisao[100] = array(
//    "movimento" => "Pensão Alimentícia",
//    "tipo" => "DEBITO",
//    "valor" => 0.00,
//    "percentual" => 0.00,
//);




/* * ************************************************************** */
//Pegando os valores dos moviemntos e inserindo nos campos de acordo com o ANEXO VIII da rescisão, o número do campo encontra-se na tabela rh_movimento

if ($row_rescisao['rescisao_complementar'] == 1) {
    $and_complementar = "AND A.complementar = 1";
}

//by ramon 05/07/2016
//SOMANDO MOVIMENTOS Q TEM O MESMO id_mov
$sql_mov = "SELECT  A.id_mov_rescisao,B.id_mov,C.id_movimento,
                    B.descicao, A.id_mov, B.categoria, C.tipo_movimento, SUM(A.valor) AS  valor , B.campo_rescisao, B.percentual, A.tipo_qnt, A.qnt, C.qnt_horas
                        FROM rh_movimentos_rescisao AS A
                        LEFT JOIN rh_movimentos AS B ON(A.id_mov = B.id_mov)
                        LEFT JOIN (SELECT * FROM rh_movimentos_clt AS A WHERE A.id_clt = '{$row_rescisao[id_clt]}' AND A.status = 1 GROUP BY A.id_mov) AS C ON(B.id_mov = C.id_mov)
                        WHERE A.id_clt = '{$row_rescisao[id_clt]}' 
                        AND A.id_mov NOT IN(" . implode(',', $arrayMovMediasFeriasEDt) . ") 
                        AND A.status = 1 {$and_complementar} AND A.id_rescisao = {$row_rescisao['id_recisao']} GROUP BY A.id_mov"; //AND C.id_clt = '{$row_rescisao[id_clt]}' AND C.status = '1'";

if ($_COOKIE['debug'] == 666) {
    echo '/////////////////////////////$sql_mov///////////////////////';
    print_array($sql_mov);
}

$qr_movimentos = mysql_query($sql_mov) or die(mysql_error());
$qtd_movimentos = mysql_num_rows($qr_movimentos);

while ($row_movimentos = mysql_fetch_assoc($qr_movimentos)) {

//    if(in_array($_COOKIE['logado'],$programadores)){
//        echo "<pre>";
//        print_r($row_movimentos);
//        echo "</pre>";
//    }


    $movimentos[$row_movimentos['campo_rescisao']] += $row_movimentos['valor'];
    $quantidade[$row_movimentos['campo_rescisao']] = $row_movimentos['qnt_horas']; //verificaQuantidade($row_movimentos['tipo_qnt'], $row_movimentos['qnt'], $row_movimentos['qnt_horas']);

    /*     * **************ARRAY DE MOVIMENTOS 20/05/2015******************** */

    $nome_movimento = "";

    /**
     * By Ramon 19/07/2016
     * A pedido da Alana juntando alguns movimentos no mesmo campo de AVISO PRÉVIO
     * VERIFICANDO PRIMEIRO SE TEM AVISO PREVIO INDENIZADO
     * SÓ VAI CRIAR INFORMAÇÃO NA MATRIZ DE $dados_recisao PARA O MOVIMENTO "96 - MEDIA SOBRE AVISO PREVIO" SE NÃO TIVER VALOR NO AVISO PRÉVIO
     * POIS SE TIVER O AVISO JA ESTÁ SOMADO O MOVI "96 - MEDIA SOBRE AVISO PREVIO"
     */
    if (isset($dados_recisao[69]["valor"]) && $row_movimentos['campo_rescisao'] == 96) {
        //SOMAR O VALOR DO MOVIMENTO COM OS VALORES JÁ REGISTRADOS NA MATRIZ
        $dados_recisao[69]["valor"] += $row_movimentos['valor'];
        //PULA ESSE MOVIMENTO DO ARRAY, E CONTINUA O WHILE DO PROXIMO MOVIMENTO
        continue;
    }


    //TRATANDO NOME DO MOVIMENTOS JAJ LANÇADOS
    if ($row_movimentos['campo_rescisao'] == 117) {
        $quant_faltas += $row_movimentos['qnt'];
        $quant_horas += $row_movimentos['qnt_horas'];

        if ($quant_faltas > 0) {

            if ($id_clt == 144) {
                $quant_faltas = 30;
            }
            $nome_movimento = "Faltas ({$quant_faltas} dias)";
        } else {
            $nome_movimento = "Faltas ({$quant_horas} horas)";
        }
    } else if ($row_movimentos['campo_rescisao'] == 58) {
        $nome_movimento = "Descanso Semanal Remunerado (DSR)";
    } else {
        $nome_movimento = $row_movimentos['descicao'];
    }

    if ($row_movimentos['valor'] > 0) {



        $dados_recisao[(String) $row_movimentos['campo_rescisao']]["movimento"] = $nome_movimento;
        $dados_recisao[(String) $row_movimentos['campo_rescisao']]["tipo"] = $row_movimentos['categoria'];
        $dados_recisao[(String) $row_movimentos['campo_rescisao']]["valor"] += $row_movimentos['valor'];
        $dados_recisao[(String) $row_movimentos['campo_rescisao']]["percentual"] = $row_movimentos['percentual'] * 100;

        if ($id_clt == 144) {
            $dados_recisao[(String) $row_movimentos['campo_rescisao']]["valor"] = 1642.37;
        }
    }
    /*     * *************************************************************** */

    if ($row_movimentos['id_mov'] == 292) {
        $adiantamento_13 = $row_movimentos['valor'];
    }
}



/* * **************ARRAY DE MOVIMENTOS 20/05/2015******************** */

//FÉRIAS PROPORCIONAIS
$dados_recisao[65] = array(
    "movimento" => "Férias Proporcionais " . sprintf('%02d', $row_rescisao['avos_fp']) . "/12 avos<br>{$periodo_aquisitivo_fp}",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['ferias_pr']
);


//FÉRIAS VENCIDAS
$texto_fv = "Férias Vencidas <br /> Per. Aquisitivo ";
//$texto_fv .= ' de ' . formato_brasileiro($row_rescisao['fv_data_ini']) . " à " . formato_brasileiro($row_rescisao['fv_data_fim']) . "<br>";

if ($row_rescisao['ferias_vencidas'] != '0.00') {
    $texto_fv .= '12/12 avos';
    $texto_fv .= 'de ' . formato_brasileiro($row_rescisao['fv_data_ini']) . " à " . formato_brasileiro($row_rescisao['fv_data_fim']) . "<br>";
} else {
    $texto_fv .= '0/12 avos';
}

if (!empty($row_rescisao['qnt_faltas_ferias_fv'])) {
    $texto_fv .= "<span>( Faltas: " . $row_rescisao['qnt_faltas_ferias_fv'] . ")</span>";
}


$dados_recisao[66] = array(
    "movimento" => $texto_fv,
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['ferias_vencidas']
);

/*
 * 08/11/16
 * by: Max
 * A pedido do Ítalo, foi separado 
 * o campo 68(terço constitucional de férias) 
 * do campo 52(1/3 férias proporcionais)
 */

//TERÇO CONSTITUCIONAL DE FERIAS

$tercoConstitucionalFerias = $row_rescisao['umterco_fv'];
if ($complementar == "COMPLEMENTAR") {
    $tercoConstitucionalFerias = 0;
}
$dados_recisao[68] = array(
    "movimento" => "Terço Constitucional de Férias",
    "tipo" => "CREDITO",
//    "valor" => $row_rescisao['umterco_fv'] + $row_rescisao['umterco_fp']
    "valor" => $tercoConstitucionalFerias
);

$dados_recisao[52] = array(
    "movimento" => "1/3 férias proporcionais",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['umterco_fp']
);

if ($id_clt == 3931) {
    $dados_recisao[68] = array(
        "movimento" => "Terço Constitucional de Férias",
        "tipo" => "CREDITO",
        "valor" => 1020.36
    );
}
if ($id_clt == 3926) {
    $valorFdp2 = 1470.95;
    if ($complementar == "COMPLEMENTAR") {
        $valorFdp2 = 0;
    }
    $dados_recisao[68] = array(
        "movimento" => "Terço Constitucional de Férias",
        "tipo" => "CREDITO",
        "valor" => $valorFdp2
    );
}

$avos_projetado = $row_rescisao['avos_projetado'];

/**
 * By Ramon 17/10/16
 */
$qntAvos13indenizado = 1;

if ($avos_projetado > 0) {
    $qntAvos13indenizado = $avos_projetado;
}

if ($id_clt == 1952) {
    $qntAvos13indenizado = 3;
}
if ($id_clt == 1967 || $id_clt == 2023 || $id_clt == 4176 || $id_clt == 3111) {
    $qntAvos13indenizado = 2;
}

//13° SALÁRIO (AVISO PREVIO INDENIZADO)
$dados_recisao[70] = array(
    "movimento" => "13º Salário (Aviso-Prévio Indenizado {$qntAvos13indenizado}/12 avos)",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['terceiro_ss']
);

/**
 * By Ramon 17/10/16
 */
$qntAvosFeriasIndenizado = 1;

if ($avos_projetado > 0) {
    $qntAvosFeriasIndenizado = $avos_projetado;
}

if ($id_clt == 1952 || $id_clt == 1967 || $id_clt == 2023 || $id_clt == 3111 || $id_clt == 4021) {
    $qntAvosFeriasIndenizado = 2;
}

//13° SALÁRIO (AVISO PREVIO INDENIZADO)
$dados_recisao[71] = array(
    "movimento" => "Férias (Aviso-Prévio Indenizado {$qntAvosFeriasIndenizado}/12 avos)",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['ferias_aviso_indenizado']
);

if ($id_clt == 2987) {
    $dados_recisao[71] = array(
        "movimento" => "Férias (Aviso-Prévio Indenizado 2/12 avos)",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['ferias_aviso_indenizado']
    );
}

//FÉRIAS EM DOBRO
$dados_recisao[72] = array(
    "movimento" => "Férias em dobro",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['fv_dobro']
);

//1/3 FÉRIAS EM DOBRO
$dados_recisao[73] = array(
    "movimento" => "1/3 férias em dobro",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['um_terco_ferias_dobro']
);

//1/3 FÉRIAS EM DOBRO

$umterco_ferias_aviso_indenizado = $row_rescisao['umterco_ferias_aviso_indenizado'];
if ($complementar == "COMPLEMENTAR") {
    $umterco_ferias_aviso_indenizado = 0;
}
$dados_recisao[75] = array(
    "movimento" => "1/3 Férias (Aviso Prévio Indenizado)",
    "tipo" => "CREDITO",
    "valor" => $umterco_ferias_aviso_indenizado
);

if ($id_clt == 3931) {
    $dados_recisao[75] = array(
        "movimento" => "1/3 Férias (Aviso Prévio Indenizado)",
        "tipo" => "CREDITO",
        "valor" => 48.57
    );
}

if ($id_clt == 3926) {
    $valorFdp1 = 90.89;
    if ($complementar == "COMPLEMENTAR") {
        $valorFdp1 = 0;
    }
    $dados_recisao[75] = array(
        "movimento" => "1/3 Férias (Aviso Prévio Indenizado)",
        "tipo" => "CREDITO",
        "valor" => $valorFdp1
    );
}

//AJUSTE DE SALDO DEVEDOR
$dados_recisao[99] = array(
    "movimento" => "Ajuste do Saldo Devedor",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['arredondamento_positivo']
);

//ADIANTAMENTO DE 13° SALÁRIO
$dados_recisao[101] = array(
    "movimento" => "Adiantamento Salarial",
    "tipo" => "DEBITO",
    "valor" => $row_rescisao['adiantamento']
);

//ADIANTAMENTO DE 13° SALÁRIO
$dados_recisao[102] = array(
    "movimento" => "Adiantamento de 13º Salário",
    "tipo" => "DEBITO",
    "valor" => $row_rescisao['adiantamento_13']
);

//ADIANTAMENTO DE 13° SALÁRIO
$dados_recisao[105] = array(
    "movimento" => "Empréstimo em Consignação",
    "tipo" => "DEBITO",
    "valor" => 0.00
);

//PREVIDÊNCIA SOCIAL
$dados_recisao["112.1"] = array(
    "movimento" => "Previdência Social",
    "tipo" => "DEBITO",
    "valor" => $row_rescisao['inss_ss']
);

//PREVIDÊNCIA SOCIAL 13 SALARIO
$dados_recisao["112.2"] = array(
    "movimento" => "Previdência Social - 13º Salário",
    "tipo" => "DEBITO",
    "valor" => $row_rescisao['inss_dt']
);

//IRRF
$dados_recisao["114.1"] = array(
    "movimento" => "IRRF",
    "tipo" => "DEBITO",
    "valor" => $row_rescisao['ir_ss']
);

//IRRF
$dados_recisao["114.2"] = array(
    "movimento" => "IRRF sobre 13º Salário",
    "tipo" => "DEBITO",
    "valor" => $row_rescisao['ir_dt']
);

//IRRF
$dados_recisao[116] = array(
    "movimento" => "IRRF Férias",
    "tipo" => "DEBITO",
    "valor" => $row_rescisao['ir_ferias']
);

//ORDERNANDO O ARRAY
ksort($dados_recisao);

//if ($_COOKIE['logado'] == 179) {
//    echo "<pre>";
//        print_r($dados_recisao);
//    echo "</pre>";
//} 

if(!empty($flagFichaParaRescisao)){
    return $dados_recisao;    
}else{

$saldo_salario = (empty($row_rescisao['saldo_salario']) || ($row_rescisao['saldo_salario'] == '0.00')) ? $movimentos[50] : $row_rescisao['saldo_salario']; //$movimentos[50]
$dt_salario = (empty($row_rescisao['dt_salario']) || ($row_rescisao['dt_salario'] == '0.00')) ? $movimentos[63] : $row_rescisao['dt_salario']; //$movimentos[50]
$ferias_pr = (empty($row_rescisao['ferias_pr']) || ($row_rescisao['ferias_pr'] == '0.00')) ? $movimentos[65] : $row_rescisao['ferias_pr']; //$movimentos[50]
$ferias_vencidas = (empty($row_rescisao['ferias_vencidas']) || ($row_rescisao['ferias_vencidas'] == '0.00')) ? $movimentos[66] : $row_rescisao['ferias_vencidas']; //$movimentos[50]

$umterco = ($row_rescisao['umterco_fv'] + $row_rescisao['umterco_fp']);
$umterco = (empty($umterco) || ($umterco == '0.00')) ? $movimentos[68] : $umterco; //$movimentos[50]
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Rescis&atilde;o de <?php echo $id_clt . ' - ' . $nome; ?></title>
        <link href="rescisao_1.css" rel="stylesheet" type="text/css" />
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <style type="text/css">
            .font13{
                font-size: 12px;
                font-style: normal;
                font-weight: 100;
            }
        </style>
        <style type="text/css" media="print">
            table.rescisao td.secao {
                background-color:#C0C0C0;
                text-align:center;
                font-size:14px;
                height:20px;
            }

        </style>
        <style>
            /* The Modal (background) */
            .modal {
                display: none; /* Hidden by default */
                position: fixed; /* Stay in place */
                z-index: 1; /* Sit on top */
                padding-top: 100px; /* Location of the box */
                left: 0;
                top: 0;
                width: 100%; /* Full width */
                height: 100%; /* Full height */
                overflow: auto; /* Enable scroll if needed */
                background-color: rgb(0,0,0); /* Fallback color */
                background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            }

            /* Modal Content */
            .modal-content {
                position: relative;
                background-color: #fefefe;
                margin: auto;
                padding: 0;
                border: 1px solid #888;
                width: 40%;
                box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
                -webkit-animation-name: animatetop;
                -webkit-animation-duration: 0.4s;
                animation-name: animatetop;
                animation-duration: 0.4s
            }

            /* Add Animation */
            @-webkit-keyframes animatetop {
                from {top:-300px; opacity:0} 
                to {top:0; opacity:1}
            }

            @keyframes animatetop {
                from {top:-300px; opacity:0}
                to {top:0; opacity:1}
            }

            /* The Close Button */
            .close {
                color: white;
                float: right;
                font-size: 28px;
                font-weight: bold;
            }

            .close:hover,
            .close:focus {
                color: #000;
                text-decoration: none;
                cursor: pointer;
            }

            .modal-header {
                padding: 2px 16px;
                background-color: #e22626;
                color: white;
            }

            .modal-body {padding: 2px 16px;}

            .modal-footer {
                padding: 2px 16px;
                background-color: #e22626;
                color: white;
            }

            @media print {
                .noPrint {
                    display:none;
                }
            }

        </style>
        <?php if ($qtd_movimentos > 8) { ?>
            <style>
                table.rescisao td.secao {
                    font-size:12px;
                    height:15px;
                }
            </style>
        <?php } ?>


    </head>

    <body>
        <!--<?php echo $id ?>-->
        <table class="rescisao" cellpadding="0" cellspacing="1">
            <?php if ($complementar) { ?>
                <tr class="noPrint" ><td colspan="6" style="text-align:center">
                        <a href='javascript:;' id="myBtn" class="btn_remove_recisao_complementar " style="padding:15px;background-color:#FF0000; color:#fff;text-decoration: none;font-size: 10px;" >Desprocessar Rescisão Complementar?</a>
                    </td>
                </tr>   <?php } ?>
            <tr>
                <td colspan="6" class="secao"><h1>TERMO DE RESCIS&Atilde;O <?php echo $complementar; ?> DO CONTRATO DE TRABALHO</h1></td>
            </tr>
            <tr>
                <td colspan="6" class="secao">IDENTIFICA&Ccedil;&Atilde;O DO EMPREGADOR</td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="campo font13"><span class="numero">01</span> CNPJ/CEI</div>
                    <div class="valor font13"><?php echo $cnpj_empresa; ?></div>
                </td>
                <td colspan="4">
                    <div class="campo font13"><span class="numero">02</span> Raz&atilde;o Social/Nome</div>
                    <div class="valor font13"><?php echo $razao_empresa; ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <div class="campo font13"><span class="numero">03</span> Endere&ccedil;o (logradouro, n&ordm;, andar, apartamento)</div>
                    <div class="valor font13"><?php echo $endereco_empresa; ?></div>
                </td>
                <td colspan="2">
                    <div class="campo font13"><span class="numero">04</span> Bairro</div>
                    <div class="valor font13"><?php echo $bairro_empresa; ?></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="campo font13"><span class="numero">05</span> Munic&iacute;pio</div>
                    <div class="valor font13"><?php echo $municipio_empresa; ?></div>
                </td>
                <td>
                    <div class="campo font13"><span class="numero">06</span> UF</div>
                    <div class="valor font13"><?php echo $uf_empresa; ?></div>
                </td>
                <td>
                    <div class="campo font13"><span class="numero">07</span> CEP</div>
                    <div class="valor font13"><?php echo $cep_empresa; ?></div>
                </td>
                <td>
                    <div class="campo font13"><span class="numero">08</span> CNAE</div>
                    <div class="valor font13"><?php echo $cnae; ?></div>
                </td>
                <td colspan="2">
                    <div class="campo font13"><span class="numero">09</span> CNPJ/CEI Tomador/Obra</div>
                    <div class="valor font13">&nbsp;</div>
                </td>
            </tr>
            <tr>
                <td colspan="6" class="secao">IDENTIFICA&Ccedil;&Atilde;O DO TRABALHADOR</td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="campo font13"><span class="numero">10</span> PIS/PASEP</div>
                    <div class="valor font13"><?php echo $pis; ?></div>
                </td>
                <td colspan="4">
                    <div class="campo font13"><span class="numero">11</span> Nome</div>
                    <div class="valor font13"><?php echo $nome; ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <div class="campo font13"><span class="numero">12</span> Endere&ccedil;o (logradouro, n&ordm;, andar, apartamento)</div>
                    <div class="valor font13"><?php echo $endereco; ?></div>
                </td>
                <td colspan="2">
                    <div class="campo font13"><span class="numero">13</span> Bairro</div>
                    <div class="valor font13"><?php echo $bairro; ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="campo font13"><span class="numero">14</span> Munic&iacute;pio</div>
                    <div class="valor font13"><?php echo $cidade; ?></div>
                </td>
                <td>
                    <div class="campo font13"><span class="numero">15</span> UF</div>
                    <div class="valor font13"><?php echo $uf; ?></div>
                </td>
                <td>
                    <div class="campo font13"><span class="numero">16</span> CEP</div>
                    <div class="valor font13"><?php echo $cep; ?></div>
                </td>
                <td colspan="2">
                    <div class="campo font13"><span class="numero">17</span> Carteira de Trabalho (n&ordm;, s&eacute;rie, UF)</div>
                    <div class="valor font13"><?php echo $cartrab . ' / ' . $serie_cartrab . ' / ' . $uf_cartrab; ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="campo font13"><span class="numero">18</span> CPF</div>
                    <div class="valor font13"><?php echo $cpf; ?></div>
                </td>
                <td>
                    <div class="campo font13"><span class="numero">19</span> Data de nascimento</div>
                    <div class="valor font13"><?php echo formato_brasileiro($data_nasci); ?></div>
                </td>
                <td colspan="3">
                    <div class="campo font13"><span class="numero">20</span> Nome da m&atilde;e</div>
                    <div class="valor font13"><?php echo $mae; ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="6" class="secao">DADOS DO CONTRATO</td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="campo font13"><span class="numero">21</span> Tipo de Contrato</div>
                    <div class="valor font13">
                        <?php
                        /*
                         * Leonardo
                         * A pedido do Italo em 2017-01-06
                         */
                        if ($row_motivo['especifica'] == 'Termino de Contrato') {
                            echo '2. Contrato por prazo Determinado';
                        } else {
                            echo '1. Contrato de Trabalho por Prazo Indeterminado';
                        }
                        ?>
                    </div>
                </td>
                <td colspan="3">
                    <div class="campo font13"><span class="numero">22</span> Causa do Afastamento</div>
                    <div class="valor font13"><?php echo $row_motivo['especifica']; ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="campo font13"><span class="numero">23</span> Remunera&ccedil;&atilde;o M&ecirc;s Anterior Afast.</div>
                    <?php if ($id_clt == 4305) { ?>
                        <?php $row_rescisao['sal_base'] = 5807.01; ?>
                    <?php } ?>
                    <div class="valor font13">R$ <?php echo formato_real($row_rescisao['sal_base']); ?></div>
                </td>
                <td>
                    <div class="campo font13"><span class="numero">24</span> Data de admiss&atilde;o</div>
                    <div class="valor font13"><?php echo formato_brasileiro($data_entrada); ?></div>
                </td>
                <td>
                    <div class="campo font13"><span class="numero">25</span> Data do Aviso Pr&eacute;vio</div>
                    <div class="valor font13"><?php echo formato_brasileiro($row_rescisao['data_aviso']); ?></div>
                </td>
                <td>
                    <div class="campo font13"><span class="numero">26</span> Data de afastamento</div>
                    <div class="valor font13"><?php echo formato_brasileiro($data_demi); ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="campo font13"><span class="numero">27</span> C&oacute;d. afastamento</div>
                    <div class="valor font13"><?php echo $row_motivo['codigo_afastamento']; ?></div>
                </td>
                <td>
                    <div class="campo font13"><span class="numero">28</span> Pens&atilde;o Aliment&iacute;cia (%) (TRCT)</div>
                    <div class="valor font13"><?php echo $dados_recisao[100]["percentual"]; ?>%</div>
                </td>
                <td>
                    <div class="campo font13"><span class="numero">29</span> Pens&atilde;o aliment&iacute;cia (%) (Saque FGTS)</div>
                    <div class="valor font13"><?php echo $dados_recisao[100]["percentual"]; ?>%</div>
                </td>
                <td>
                    <div class="campo font13"><span class="numero">30</span> Categoria do trabalhador</div>
                    <div class="valor font13">01</div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="campo font13"><span class="numero">31</span> C&oacute;digo Sindical</div>
                    <div class="valor font13"><?php echo $cod_sindicato; ?></div>
                </td>
                <td colspan="4">
                    <div class="campo font13"><span class="numero">32</span> CNPJ e Nome da Entidade Sindical Laboral</div>
                    <?php if ($id_clt == 4038) { ?>
                        <div class="valor font13"><?php echo "52034840000179 - Sindicato Dos Odontologistas"; ?></div>
                    <?php } else { ?>
                        <div class="valor font13"><?php echo $row_sindicato['cnpj'] . ' - ' . substr($row_sindicato['nome'], 0, 52); ?></div>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td colspan="6" class="secao">DISCRIMINA&Ccedil;&Atilde;O DAS VERBAS RESCIS&Oacute;RIAS</td>
            </tr>
            <tr>
                <td colspan="6" class="secao">VERBAS RESCIS&Oacute;RIAS</td>
            </tr>
            <tr>
                <td width="17%" class="secao_filho">Rubrica</td>
                <td width="16%" class="secao_filho">Valor</td>
                <td width="17%" class="secao_filho">Rubrica</td>
                <td width="16%" class="secao_filho">Valor</td>
                <td width="17%" class="secao_filho">Rubrica</td>
                <td width="16%" class="secao_filho">Valor</td>
            </tr>
            <!-- MOVIMENTOS DE CREDITO -->
            <?php $count = 1;
            $totalCredito = 0;
            $resto = 0;
            ?>
            <?php foreach ($dados_recisao as $key => $valores) { ?>
                <?php if ($valores['tipo'] == "CREDITO") {
                    $totalCredito += $valores['valor']; ?>
                        <?php if ($count == 1) { ?>
                        <tr>
        <?php } ?>    

                        <td class="font13"><span class="numero"><?php echo $key; ?></span> <?php echo $valores['movimento']; ?></td>
                        <td class="font13"><?php echo "R$ " . number_format($valores['valor'], 2, ',', '.'); ?></td>

                    <?php if ($count == 3) { ?>
                        </tr>
                    <?php } ?>    
                    <?php
                    if ($count == 3) {
                        $count = 0;
                    } $count++;
                    ?>
                <?php } ?>     
            <?php } ?>
            <?php
            if ($count <= 3) {
                $resto = 3 - $count;
                for ($i = 0; $i <= $resto; $i++) {
                    echo "<td></td><td></td>";
                }
            }

            $totRendFloat = floatval($row_rescisao['total_rendimento']);
            $corAviso = "";
            ($totalCredito == $totRendFloat) ? "" : " style=\"background-color: red;\"";
            ?>   

            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="secao"<?php echo $corAviso; ?>>TOTAL RESCISÓRIO BRUTO</td>
                <td class="secao"<?php echo $corAviso; ?>><div class="valor">R$ <?php echo formato_real($row_rescisao['total_rendimento']); ?></div></td>
            </tr>
            <!--  -->  



            <tr>
                <td colspan="6" class="secao">DEDU&Ccedil;&Otilde;ES</td>
            </tr>
            <tr>
                <td class="secao_filho">Desconto</td>
                <td class="secao_filho">Valor</td>
                <td class="secao_filho">Desconto</td>
                <td class="secao_filho">Valor</td>
                <td class="secao_filho">Desconto</td>
                <td class="secao_filho">Valor</td>
            </tr>
            <!-- MOVIMENTOS DE DEBITO -->
                <?php $count = 1;
                $resto = 0;
                ?>
                <?php foreach ($dados_recisao as $key => $valores) { ?>
                    <?php if ($valores['tipo'] == "DEBITO") { ?>
                    <?php if ($count == 1) { ?>
                        <tr>
                    <?php } ?>    
                        <td class="font13"><span class="numero"><?php echo $key; ?></span> <?php echo $valores['movimento']; ?></td>
                        <td class="font13"><?php echo "R$ " . number_format($valores['valor'], 2, ',', '.'); ?></td>
                    <?php if ($count == 3) { ?>
                        </tr>
                    <?php } ?>    
                    <?php
                    if ($count == 3) {
                        $count = 0;
                    } $count++;
                    ?>
                <?php } ?>     
            <?php } ?>
<?php
if ($count <= 3) {
    $resto = 3 - $count;
    for ($i = 0; $i <= $resto; $i++) {
        echo "<td></td><td></td>";
    }
}
?>   
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="secao">TOTAL DAS DEDUÇÕES</td>
                <td class="secao">R$ <?php echo formato_real($row_rescisao['total_deducao']); ?> </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="secao">VALOR RESCISÓRIO LÍQUIDO</td>
                <td class="secao">R$ <?php echo formato_real($row_rescisao['total_liquido']); ?></td>
            </tr>
            <!--  -->

        </table>
<?php if ($row_rescisao['um_ano'] >= 1 && $row_rescisao['motivo'] != 63) { ?>
            <table  class="rescisao" cellpadding="0" cellspacing="1" style="page-break-before: always; margin-top:20px; ">
                <tr>
    <?php
    if ($row_rescisao['id_clt'] == 138 || $row_rescisao['um_ano'] < 1) {
        $nome_termo = 'QUITAÇÃO';
    } else {
        $nome_termo = 'HOMOLOGAÇÃO';
    }
    ?>
                    <td colspan="6" class="secao"><h1>TERMO DE <?= $nome_termo ?> DE RESCISÃO DO CONTRATO DE TRABALHO</h1></td>
                </tr>     
                <tr>
                    <td colspan="6" class="secao">EMPREGADOR</td>
                </tr>

                <tr>
                    <td colspan="2">
                        <div class="campo font13"><span class="numero">01</span> CNPJ/CEI</div>
                        <div class="valor font13"><?php echo $cnpj_empresa; ?></div>
                    </td>
                    <td colspan="4">
                        <div class="campo font13"><span class="numero">02</span> Raz&atilde;o Social/Nome</div>
                        <div class="valor font13"><?php echo $razao_empresa; ?></div>
                    </td>
                </tr>

                <td colspan="6" class="secao">TRABALHADOR</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="campo font13"><span class="numero">10</span> PIS/PASEP</div>
                        <div class="valor font13"><?php echo $pis; ?></div>
                    </td>
                    <td colspan="4">
                        <div class="campo font13"><span class="numero">11</span> Nome</div>
                        <div class="valor font13"><?php echo $nome; ?></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="campo font13"><span class="numero">17</span> 17 CTPS (nº, série, UF)</div>
                        <div class="valor font13"><?php echo $cartrab . ' / ' . $serie_cartrab . ' / ' . $uf_cartrab; ?></div>
                    </td>

                    <td>
                        <div class="campo font13"><span class="numero">18</span> CPF</div>
                        <div class="valor font13"><?php echo $cpf; ?></div>
                    </td>
                    <td>
                        <div class="campo font13"><span class="numero">19</span> Data de nascimento</div>
                        <div class="valor font13"><?php echo formato_brasileiro($data_nasci); ?></div>
                    </td>
                    <td colspan="2">
                        <div class="campo font13"><span class="numero">20</span> Nome da m&atilde;e</div>
                        <div class="valor font13"><?php echo $mae; ?></div>
                    </td>
                </tr>

                <tr>
                    <td colspan="6" class="secao">CONTRATO</td>
                </tr>

                <tr>   
                    <td colspan="6">
                        <div class="campo font13"><span class="numero">22</span> Causa do Afastamento</div>
                        <div class="valor font13"><?php echo $row_motivo['especifica']; ?></div>
                    </td>
                </tr>
                <tr>    
                    <td>
                        <div class="campo font13"><span class="numero">24</span> Data de admiss&atilde;o</div>
                        <div class="valor font13"><?php echo formato_brasileiro($data_entrada); ?></div>
                    </td>
                    <td>
                        <div class="campo font13"><span class="numero">25</span> Data do Aviso Pr&eacute;vio</div>
                        <div class="valor font13"><?php echo formato_brasileiro($row_rescisao['data_aviso']); ?></div>
                    </td>
                    <td>
                        <div class="campo font13"><span class="numero">26</span> Data de afastamento</div>
                        <div class="valor font13"><?php echo formato_brasileiro($data_demi); ?></div>
                    </td>
                    <td>
                        <div class="campo font13"><span class="numero">27</span> C&oacute;d. afast.</div>
                        <div class="valor font13"><?php echo $row_motivo['codigo_afastamento']; ?></div>
                    </td>  
                    <td colspan="2">
                        <div class="campo font13"><span class="numero">29</span>Pensão Alimentícia (%) (FGTS)</div>
                        <div class="valor font13">0,00%</div>
                    </td>  
                </tr>

                <tr>  
                    <td colspan="6">
                        <div class="campo font13"><span class="numero">30</span> Categoria do trabalhador</div>
                        <div class="valor font13">01</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="campo font13"><span class="numero">31</span> C&oacute;digo Sindical</div>
                        <div class="valor font13"><?php echo $cod_sindicato; ?></div>
                    </td>
                    <td colspan="4">
                        <div class="campo font13"><span class="numero">32</span> CNPJ e Nome da Entidade Sindical Laboral</div>
    <?php if ($id_clt == 4038) { ?>
                            <div class="valor font13"><?php echo "52034840000179 - Sindicato Dos Odontologistas"; ?></div>
    <?php } else { ?>
                            <div class="valor font13"><?php echo $row_sindicato['cnpj'] . ' - ' . substr($row_sindicato['nome'], 0, 52); ?></div>
    <?php } ?>
                    </td>
                </tr>

                <tr style="border: 0px;">
                    <td colspan="6" style="border: 0px;">
                        <div class="campo font13">
                            Foi prestada, gratuitamente, assist&ecirc;ncia na rescisão do contrato de trabalho, nos termos do art. 477, &sect; 1&ordm;,
                            da Consolida&ccedil;&atilde;o das Leis do Trabalho (CLT), sendo comprovado, neste ato, o efetivo pagamento das verbas rescis&oacute;rias
                            acima especificadas no corpo do TRCT, no valor líquido de R$ <?php echo formato_real($row_rescisao['total_liquido']); ?>, o qual devidamente rubricado pelas partes, é parte integrante
                            do presente Termo de Homologação. <br />
                            </p>
                            <p>As partes assistidas no presente ato de rescisão contratual foram identificadas como legitimas conforme previsto na Instrução Normativa/SRT nº 15/2010</p>
                            <p>Fico ressalvado o direito de o trabalhador pleitear judicialmente os direitos informados no camppo 155, abaixo.</p>

                            <p>____________________/___, ____ de _______________________ de _______. </p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>___________________________________________________________</br>
                                150 Assinatura do Empregador ou Preposto
                            </p>
                        </div>
                    </td>   
                </tr>

                <tr style="border: 0px;">
                    <td colspan="3" style="border: 0px;" class="font13">
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>___________________________________________________________</br>
                            151 Assinatura do Trabalhador
                        </p>

                    </td>
                    <td colspan="3" style="border: 0px;" class="font13">
                        <p>&nbsp;</p>
                        <p>&nbsp;</p> 
                        <p>___________________________________________________________</br>
                            152 Assinatura do Responsável Legal do Trabalhador
                        </p>
                    </td>
                </tr>

                <tr style="border: 0px;">
                    <td colspan="3"  style="border: 0px;" class="font13">
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>___________________________________________________________</br>
                            153 Carimbo e Assinatura do Assistente
                        </p>

                    </td>
                    <td colspan="3"  style="border: 0px;" class="font13">
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>___________________________________________________________</br>
                            154 Nome do Órgão Homologador
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" class="font13" >   <div class="campo"><span class="numero ">155</span> Ressalvas</div> 
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>      
                    </td>      
                </tr>
                <tr>
                    <td colspan="6">
                        <div class="campo font13"><span class="numero">156</span> Informações à CAIXA</div> 
                        <p>&nbsp;</p>

                    </td>
                </tr>   
                <tr>
                    <td colspan="6" class="font13">
                        <p style="text-align:center;">
                            <strong> ASSISTÊNCIA NO ATO DE RESCISÃO CONTRATUAL É GRATUITA.</strong><bR>
                                Pode o trabalhador iniciar ação judicial quanto aos créditos resultantes das relações de trabalho até o limite de dois anos após a extinção do contrato de trabalho (inciso XXIX, art. 7º da Constituição Federal/1988).
                        </p>
                    </td>
                </tr>
            </table>
            <div id="myModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="close">&times;</span>
                        <h2>Desprocessando Rescisão Complementar</h2>
                    </div>
                    <div class="modal-body">
                        <p style="font-size: 15px;">Deseja desprocessar a rescisão complementar?</p>
                        <a href='javascript:;' class="remove_recisao_complementar" style="margin:15px;background-color:#e22626;font-size:20px;text-decoration: none;padding:2px 10px;border-radius:5px;color:#fff" data-rescisao="<?= $row_rescisao['id_recisao'] ?>">Sim</a>
                        <a href='javascript:;' class="nao_remove_recisao_complementar" style="margin:15px;background-color:#45e226;font-size:20px;text-decoration: none;padding:2px 10px;border-radius:5px;color:#fff">Não</a>

                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>

<?php } else { ?>

            <table  class="rescisao" cellpadding="0" cellspacing="1" style="page-break-before: always; margin-top:20px; ">
                <tr>
                    <td colspan="6" class="secao"><h1>TERMO DE  QUITA&Ccedil;&Atilde;O DE RESCIS&Atilde;O DO CONTRATO DE TRABALHO</h1></td>
                </tr>     
                <tr>
                    <td colspan="6" class="secao">EMPREGADOR</td>
                </tr>     
                <tr>
                    <td colspan="2">
                        <div class="campo font13"><span class="numero">01</span> CNPJ/CEI</div>
                        <div class="valor font13"><?php echo $cnpj_empresa; ?></div>
                    </td>
                    <td colspan="4">
                        <div class="campo font13"><span class="numero">02</span> Raz&atilde;o Social/Nome</div>
                        <div class="valor font13"><?php echo $razao_empresa; ?></div>
                    </td>
                </tr>     
                <td colspan="6" class="secao">TRABALHADOR</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="campo font13"><span class="numero">10</span> PIS/PASEP</div>
                        <div class="valor font13"><?php echo $pis; ?></div>
                    </td>
                    <td colspan="4">
                        <div class="campo font13"><span class="numero">11</span> Nome</div>
                        <div class="valor font13"><?php echo $nome; ?></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="campo font13"><span class="numero">17</span> CTPS (nº, série, UF)</div>
                        <div class="valor font13"><?php echo $cartrab . ' / ' . $serie_cartrab . ' / ' . $uf_cartrab; ?></div>
                    </td>

                    <td colspan="2">
                        <div class="campo font13"><span class="numero">18</span> CPF</div>
                        <div class="valor font13"><?php echo $cpf; ?></div>
                    </td>
                    <td>
                        <div class="campo font13"><span class="numero">19</span> Data de nascimento</div>
                        <div class="valor font13"><?php echo formato_brasileiro($data_nasci); ?></div>
                    </td>
                    <td colspan="3">
                        <div class="campo font13"><span class="numero">20</span> Nome da m&atilde;e</div>
                        <div class="valor font13"><?php echo $mae; ?></div>
                    </td>
                </tr>     
                <tr>
                    <td colspan="6" class="secao">CONTRATO</td>
                </tr>
                <tr>   
                    <td colspan="6">
                        <div class="campo font13"><span class="numero">22</span> Causa do Afastamento</div>
                        <div class="valor font13"><?php echo $row_motivo['especifica']; ?></div>
                    </td>
                </tr>
                <tr>    
                    <td>
                        <div class="campo font13"><span class="numero">24</span> Data de admiss&atilde;o</div>
                        <div class="valor font13"><?php echo formato_brasileiro($data_entrada); ?></div>
                    </td>
                    <td>
                        <div class="campo font13"><span class="numero">25</span> Data do Aviso Pr&eacute;vio</div>
                        <div class="valor font13"><?php echo formato_brasileiro($row_rescisao['data_aviso']); ?></div>
                    </td>
                    <td>
                        <div class="campo font13"><span class="numero">26</span> Data de afastamento</div>
                        <div class="valor font13"><?php echo formato_brasileiro($data_demi); ?></div>
                    </td>
                    <td>
                        <div class="campo font13"><span class="numero">27</span> C&oacute;d. Afast.</div>
                        <div class="valor font13"><?php echo $row_motivo['codigo_afastamento']; ?></div>
                    </td>  
                    <td colspan="2">
                        <div class="campo font13"><span class="numero">29</span> Pensão Alimentícia (%) (FGTS)</div>
                        <div class="valor font13">0,00%</div>
                    </td>  
                </tr>
                <tr>  
                    <td colspan="6">
                        <div class="campo font13"><span class="numero">30</span> Categoria do trabalhador</div>
                        <div class="valor font13">01</div>
                    </td>
                </tr>
                <tr style="border: 0px;">
                    <td colspan="6" style="border: 0px;">
                        <div class="campo font13">
                            <p> Foi realizada a rescisão do contrato de trabalho do trabalhador acima qualificado, nos termos do artigo nº 477 da 
                                Consolidação das Leis do Trabalho (CLT). A assistência à rescisão prevista no §1º do art. nº 477 da CLT não é devida, 
                                tendo em vista a duração do contrato de trabalho não ser superior a um ano de serviço e não existir previsão de 
                                assistência à rescisão contratual em Acordo ou Convenção Coletiva de Trabalho da categoria a qual pertence o 
                                trabalhador.</p>
                            <p> No dia <?php echo implode('/', array_reverse(explode('-', $row_rescisao['data_demi']))) ?> foi realizado, nos termos do art. 23 da Instrução Normativa/SRT nº 15/2010, o efetivo pagamento das 
                                verbas rescisórias especificadas no corpo do TRCT, no valor líquido de R$ <?php echo number_format($row_rescisao['total_liquido'], 2, ',', '.'); ?> ,o qual, devidamente rubricado pelas partes, é parte integrante do 
                                presente Termo de Quitação.</p>
                            <br />
                            <p>____________________/___, ____ de _______________________ de _______. </p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p>___________________________________________________________</br>
                                150 Assinatura do Empregador ou Preposto
                            </p>
                        </div>
                    </td>   
                </tr>
                <tr style="border: 0px;">
                    <td colspan="3" style="border: 0px;" class="font13">
                        <p>&nbsp;</p>
                        <p>&nbsp;</p>
                        <p>___________________________________________________________</br>
                            151 Assinatura do Trabalhador
                        </p>
                    </td>
                    <td colspan="3" style="border: 0px;" class="font13">
                        <p>&nbsp;</p>
                        <p>&nbsp;</p> 
                        <p>___________________________________________________________</br>
                            152 Assinatura do Responsável Legal do Trabalhador
                        </p>
                    </td>
                </tr> 
                <tr style="border: 0px; height: 300px;">
                    <td colspan="6" style="border: 0px;" class="font13">   

                    </td>      
                </tr>
                <tr>
                    <td colspan="6" class="font13">
                        <p style="text-align:center;">
                            <strong> ASSISTÊNCIA NO ATO DE RESCISÃO CONTRATUAL É GRATUITA.</strong><bR>
                                Pode o trabalhador iniciar ação judicial quanto aos créditos resultantes das relações de trabalho até o limite de dois anos após a extinção do contrato de trabalho (inciso XXIX, art. 7º da Constituição Federal/1988).
                        </p>
                    </td>
                </tr>

            </table>
            <!-- The Modal -->
            <div id="myModal" class="modal">

                <!-- Modal content -->
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="close">&times;</span>
                        <h2>Desprocessando Rescisão Complementar</h2>
                    </div>
                    <div class="modal-body">
                        <p style="font-size: 15px;">Deseja desprocessar a rescisão complementar?</p>
                        <a href='javascript:;' class="remove_recisao_complementar" style="margin:15px;background-color:#e22626;font-size:20px;text-decoration: none;padding:2px 10px;border-radius:5px;color:#fff" data-rescisao="<?= $row_rescisao['id_recisao'] ?>">Sim</a>
                        <a href='javascript:;' class="nao_remove_recisao_complementar" style="margin:15px;background-color:#45e226;font-size:20px;text-decoration: none;padding:2px 10px;border-radius:5px;color:#fff">Não</a>

                    </div>
                    <div class="modal-footer">
                    </div>
                </div>

            </div>
<?php } ?>
         <!--<a href="" style="padding:15px;background-color:#FF0000; border-radius: 15px;color:#fff;text-decoration: none" data-rescisao="<?= $row_rescisao['id_recisao'] ?>">Remover Rescisão Complementar</a>-->
    </body>
    <script>
        $(document).ready(function () {

            // Get the modal
            var modal = document.getElementById('myModal');

// Get the button that opens the modal
            var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
            btn.onclick = function () {
                modal.style.display = "block";
            }

// When the user clicks on <span> (x), close the modal
            span.onclick = function () {
                modal.style.display = "none";
            }

// When the user clicks anywhere outside of the modal, close it
            window.onclick = function (event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }

            $(".nao_remove_recisao_complementar").click(function () {
                modal.style.display = "none";
            });

            $("body").on("click", ".remove_recisao_complementar", function () {
                var id_complementar = $(this).data("rescisao");
                alert(id_complementar);
                $.post('../../rh_novaintra/recisao/remover_rescisao_complementar.php', {id: id_complementar}, function (data) {
                    //mostrando o retorno do post
                    //alert(data)
                })
                modal.style.display = "none";
                window.location.href = "../../rh_novaintra/recisao/nova_rescisao.php";
            });

        });


    </script>
</html>
<?php } ?>