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
//    echo "Deus � fiel ";
//}

$dados_recisao = array();
$programadores = array(179, 158, 260, 257, 258, 275,349);

// Recebendo a Vari�vel Criptografada
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

$regiao = $_REQUEST['regiao'];

if(!empty($_REQUEST['clt'])){
    $and = "WHERE A.id_clt = '{$_REQUEST['clt']}'";
}else{
    $and = "WHERE A.id_regiao = '{$regiao}'";
}

$qry_clt = "SELECT A.*, C.nome AS nome_projeto, B.id_clt, B.matricula, B.nome AS nome_clt, B.cpf, DATE_FORMAT(A.data_demi, '%m/%Y') AS referencia, TIMESTAMPDIFF(YEAR, B.data_entrada, A.data_demi) AS periodo_anos
    FROM rh_recisao AS A
    LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
    LEFT JOIN projeto AS C ON(B.id_projeto = C.id_projeto)
    {$and} AND A.status = 1
    ORDER BY A.data_demi";
$qr_rescisao = mysql_query($qry_clt) or die(mysql_error());

$status = "1";
if (isset($_REQUEST['reitegracao'])) {
    $status = "0";
}

// Consulta da Rescis�o
//$qr_rescisao = mysql_query("SELECT * FROM rh_recisao WHERE id_recisao = '$id' AND status = '{$status}'");
//$row_rescisao = mysql_fetch_array($qr_rescisao);

while($row_rescisao = mysql_fetch_array($qr_rescisao)){
    
    
    $clts[$row_rescisao['id_clt']] = $row_rescisao;

//if ($_COOKIE['logado'] == 353) {
//    print_array($row_rescisao);
//}

$complementar = ($row_rescisao['rescisao_complementar'] == 1) ? 'COMPLEMENTAR' : '';

if ($row_rescisao['aviso'] == 'trabalhado') {

    $tipo_aviso = 'Aviso Pr�vio trabalhado';
} else {
    $tipo_aviso = 'Aviso Pr�vio indenizado';
}


// Tipo da Rescis�o
$qr_motivo = mysql_query("SELECT * FROM rhstatus WHERE codigo = '{$row_rescisao['motivo']}'");
$row_motivo = mysql_fetch_array($qr_motivo);

// Informa��es do Participante
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
 * A pedido do italo por uma valida��o para deixar o campo da m�e de vermelho caso n�o venha preenchido
 */
//CALCULO DE DIAS PARA LEI 12506
//$qtd_ano_12506 = diferencaData($data_entrada, $data_demi, 'y');
$qtd_ano_12506 = $row_rescisao['periodo_anos'];

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
// Informa��es da Empresa
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

// Aviso Pr�vio
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

//function verificaQuantidade($tipoQnt, $qnt, $qntHoras) {
//    if (!empty($tipoQnt)) {
//        switch ($tipoQnt) {
//            case 1: $qntFinal = substr($qntHoras, 0, 6) . ' Horas';
//                break;
//            case 2: $qntFinal = $qnt . ' Dias';
//        }
//
//        return $qntFinal;
//    } else {
//        return '';
//    }
//}

/* * ***ARRAY DE CAMPOS DE MOVIMENTOS OBRIGAT�RIOS 22/05/2015******** */
/* * ****************MESMO COM VALOR ZERADO************************** */


//GRATIFICA��O
//    $dados_recisao[52] = array(
//        "movimento" => "Gratifica��o",
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
//SALDO DE SAL�RIO
$dados_recisao[50] = array(
    "movimento" => "Saldo de Sal�rio",
    "tipo" => "CREDITO",
    "qtd" => "{$row_rescisao['dias_saldo']} dia(s)",
    "valor" => $row_rescisao['saldo_salario']
);
if($id_clt == 4243){
    $dados_recisao[50]["movimento"] = "Saldo de Sal�rio - 1 plant�o";
}
if($id_clt == 4251){
    $dados_recisao[50]["movimento"] = "Saldo de Sal�rio";
}

//COMISS�O
if (!is_null($row_rescisao['comissao']) && $row_rescisao['comissao'] > 0) {
    $dados_recisao[51] = array(
        "movimento" => "Comiss�es",
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
    "movimento" => 'Reflexo do "DSR" sobre Sal�rio Vari�vel',
    "tipo" => "CREDITO",
    "valor" => 0.00
);

//MULTA 477
$dados_recisao[60] = array(
    "movimento" => 'Multa Art. 477, � 8�/CLT',
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['a477']
);

//SALARIO FAMILIA
$dados_recisao[62] = array(
    "movimento" => 'Sal�rio-Fam�lia',
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['sal_familia']
);

/*
 * 11/05/2016
 * by: MAX
 * SOLICITADO PELO �TALO(IABAS)
 * PQ ESTAVA TRAZENDO NUM CAMPO COM NUMERO 0(ZERO)
 * ENT�O ELE DISTRIBUI ESSES MOVIMENTOS
 * EM ALGUNS CAMPOS DA RESCIS�O
 */

/**
 * 21/06/2016
 * by: Ramon
 * ALANA SOLICITOU QUE TIRASSE A SOMA DA M�DIA DO 13 E DAS F�RIAS
 * POIS O VALOR CALCULADO DE 13 E DE F�RIAS J� EST� CONSIDERANDO A M�DIA LAN�ADA
 * COMENTANDO O C�DIGO QUE O ITALO PEDIU PARA ACRECENTAR
 */
//$arrayMovMediasFeriasEDt = array(384, 385, 386, 387, 388, 408, 409, 410);
//$arrayMovMediasFeriasEDt = array( 385, 386, 387, 388, 408, 409, 410); // comentado em 2017-03-14 - Leonardo
//$arrayMovMediasFeriasEDt = array(  386, 387, 388, 408, 409, 410);
$arrayMovMediasFeriasEDt = array(  386, 387, 388, 408, 409, 410, 466, 385, 508, 509);


/*
 * 04/10/2016
 * Renato
 * TIRANDO OS MOVIMENTOS DE MEDIA PARA AS RESCIS�ES COMPLEMENTARES
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
      print_array  ($sql_movTemp);
    }
}
/**
 * By Ramon 19/07/2016
 * A Alana lan�ou m�dias de ferias proporcionais, porem a pessoa teve 12 avos e na hora de GRAVAR a rescis�o 
 * os valores q antes apareciam como proporcionais, ter�o q aparecer como f�rias VENCIDAS por conta dos 12 avos...
 * Ent�o as m�dias q foram lan�adas para calculo como ferias proporcionais agora v�o pertencer as f�rias Vencidas...
 * Vamos a GAMBIARRA DA PROJE��O
 * (MEDIA FERIAS PROJE�AO AVISO PREVIO)
 */
while ($row_movimentosTemp = mysql_fetch_assoc($qr_movimentosTemp)) {
//    var_dump($row_movimentosTemp['id_mov']);
    if(in_array($_COOKIE['logado'],$programadores)){
        echo "**********************************************<br>";
        echo "<pre>";
            print_r($row_movimentosTemp);
        echo "</pre>";
    }

    /* -------------BLOCO 13 SALARIO------------- */
    //MEDIA 13� PROJE�AO AVISO PREVIO (junto com o campo 70)
    if ($row_movimentosTemp['id_mov'] == 410) {
        $row_rescisao['terceiro_ss'] = $row_rescisao['terceiro_ss'] + $row_movimentosTemp['valor'];
    }

    //MEDIA SOBRE 13� SALARIO
    if ($row_movimentosTemp['id_mov'] == 384) {
        $row_rescisao['dt_salario'] = $row_rescisao['dt_salario'] + $row_movimentosTemp['valor'];
    }
    
    //MEDIA SOBRE 13� SALARIO IDENIZADO
    if ($row_movimentosTemp['id_mov'] == 466) {
        $row_rescisao['terceiro_ss'] = $row_rescisao['terceiro_ss'] + $row_movimentosTemp['valor'];
    }

    /* -------------BLOCO F�RIAS VENCIDAS------------- */
    /*
     * 2017-03-14 - Leonardo
     * est� entrando como f�rias vencidas mas a pessoa nao tem ferias vencidas.
     */
    //MEDIA SOBRE FERIAS INDENIZADAS
//    if ($row_movimentosTemp['id_mov'] == 385) {
//        $row_rescisao['ferias_vencidas'] = $row_rescisao['ferias_vencidas'] + $row_movimentosTemp['valor'];
//    }
    
    //MEDIA FERIAS PROJE�AO AVISO PREVIO (junto com o campo 71)
    if ($row_movimentosTemp['id_mov'] == 408) {
        $row_rescisao['ferias_aviso_indenizado'] = $row_rescisao['ferias_aviso_indenizado'] + $row_movimentosTemp['valor'];
    }

    //1/3 MEDIA FERIAS PROJE��O AVISO PREVIO (junto com o campo 75)
    if ($row_movimentosTemp['id_mov'] == 409) {
        $row_rescisao['umterco_ferias_aviso_indenizado'] = $row_rescisao['umterco_ferias_aviso_indenizado'] + $row_movimentosTemp['valor'];
    }

    /* -------------BLOCO F�RIAS PROPORCIONAIS------------- */
    //MEDIA SOBRE FERIAS PROPORCIONAIS
    if ($row_movimentosTemp['id_mov'] == 387) {
        $row_rescisao['ferias_pr'] = $row_rescisao['ferias_pr'] + $row_movimentosTemp['valor'];
    }

    //1/3 DE MEDIA SOBRE FERIAS INDENIZADAS
//    if ($row_movimentosTemp['id_mov'] == 386) {
//        $row_rescisao['umterco_fp'] = $row_rescisao['umterco_fp'] + $row_movimentosTemp['valor'];
//    }
    if ($row_movimentosTemp['id_mov'] == 386) {
        $row_rescisao['umterco_ferias_aviso_indenizado'] = $row_rescisao['umterco_ferias_aviso_indenizado'] + $row_movimentosTemp['valor'];
    }

    //1/3 DE MEDIA SOBRE FERIAS PROPORCIONAIS
    if ($row_movimentosTemp['id_mov'] == 388) {
        $row_rescisao['umterco_fp'] = $row_rescisao['umterco_fp'] + $row_movimentosTemp['valor'];
    }
    
    //DE MEDIA SOBRE FERIAS IDENIZADAS
    if ($row_movimentosTemp['id_mov'] == 385) {
        $row_rescisao['ferias_aviso_indenizado'] = $row_rescisao['ferias_aviso_indenizado'] + $row_movimentosTemp['valor'];
    }
    
    // 1/3 DE MEDIA SOBRE FERIAS VENCIDAS
    if ($row_movimentosTemp['id_mov'] == 509) {
        $row_rescisao['umterco_fv'] = $row_rescisao['umterco_fv'] + $row_movimentosTemp['valor'];
    }
    
    // MEDIA SOBRE FERIAS VENCIDAS
    if ($row_movimentosTemp['id_mov'] == 508) {
        $row_rescisao['ferias_vencidas'] = $row_rescisao['ferias_vencidas'] + $row_movimentosTemp['valor'];
    }
   
}

//DECIMO TERCEIRO
$avos_dt = sprintf('%02d', $row_rescisao['avos_dt']);
$dados_recisao[63] = array(
    "movimento" => "13� Sal�rio Proporcional",
    "tipo" => "CREDITO",
    "qtd" => "{$avos_dt}/12 avos",
    "valor" => $row_rescisao['dt_salario']
);

//DECIMO TERCEIRO EXERCICIO
//$dados_recisao[64] = array(
//    "movimento" => '13� Sal�rio Exerc�cio 0/12 avos',
//    "tipo" => "CREDITO",
//    "valor" => 0.00
//);

if($_COOKIE['debug'] == 666){
    print_array($row_rescisao);
}

$clts_aviso_separado = array(4196);

/**
 * By Ramon 19/07/2016
 * A pedido da Alana juntando alguns movimentos no mesmo campo de AVISO PR�VIO
 * TEXTO ENVIADO SKYPE:
 * "a lei 12.506 e m�dias sobre aviso pr�vio deve aparecer junto com o aviso pr�vio indenizado no termo"
 */
// AVISO PREVIO
if ($row_rescisao['fator'] == 'empregado' && $row_rescisao['aviso'] == 'PAGO pelo ') {
    $aviso_previo_debito = $row_rescisao['aviso_valor'];
    $dados_recisao[103] = array(
        "movimento" => "Aviso-Pr�vio Indenizado",
        "tipo" => "DEBITO",
        "valor" => $row_rescisao['aviso_valor']
    );
} else {
    /*
     * 10/03/2017
     * by: Max
     * A PEDIDO DO ITALO, COLOCANDO SOMENTE QUANDO O AVISO FOR INDENIZADO
     * POIS TEVE UM CASO DE AVISO TRABALHADO QUE EST� APARECENDO
     */
    if($row_rescisao['aviso'] == "indenizado"){
        $aviso_previo_credito = $row_rescisao['aviso_valor'];                
        
        if(in_array($id_clt, $clts_aviso_separado)){
            $valor_aviso_ = $row_rescisao['aviso_valor'];
        }else{
            $valor_aviso_ = $row_rescisao['aviso_valor'] + $row_rescisao['lei_12_506'];
        }
        
        $dados_recisao[69] = array(
            "movimento" => "Aviso-Pr�vio Indenizado",
            "tipo" => "CREDITO",
            "valor" => $valor_aviso_
        );
    }
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

if($_COOKIE['debug'] == 666){
    echo "69: {$dados_recisao[69]["valor"]}";
}

/**
 * By Ramon 19/07/2016
 * A pedido da Alana juntando alguns movimentos no mesmo campo de AVISO PR�VIO
 * VERIFICANDO PRIMEIRO SE TEM AVISO PREVIO INDENIZADO
 * S� VAI CRIAR INFORMA��O NA MATRIZ DE $dados_recisao PARA A lei SE N�O TIVER VALOR NO AVISO PR�VIO
 * POIS SE TIVER O AVISO JA EST� SOMADO A LEI
 */
//LEI 12.506
if (!isset($dados_recisao[69]["valor"])) {
    $dados_recisao[95] = array(
        //"movimento" => "Lei 12.506 ({$row_rescisao['qnt_dias_lei_12_506']} dias)",
        "movimento" => "Lei 12.506",
        "tipo" => "CREDITO",
        "qtd" => "{$qtd_dias_12506} dia(s)",
        "valor" => $row_rescisao['lei_12_506']
    );
}

if(in_array($id_clt, $clts_aviso_separado)){
    $dados_recisao[95] = array(
        //"movimento" => "Lei 12.506 ({$row_rescisao['qnt_dias_lei_12_506']} dias)",
        "movimento" => "Lei 12.506",
        "tipo" => "CREDITO",
        "qtd" => "{$qtd_dias_12506} dia(s)",
        "valor" => $row_rescisao['lei_12_506']
    );
}

//PENS�O ALIMENT�CIA 
//$dados_recisao[100] = array(
//    "movimento" => "Pens�o Aliment�cia",
//    "tipo" => "DEBITO",
//    "valor" => 0.00,
//    "percentual" => 0.00,
//);




/* * ************************************************************** */
//Pegando os valores dos moviemntos e inserindo nos campos de acordo com o ANEXO VIII da rescis�o, o n�mero do campo encontra-se na tabela rh_movimento

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

//if ($_COOKIE['debug'] == 666) {
//    echo '/////////////////////////////$sql_mov///////////////////////';
//    print_array($sql_mov);
//}

$qr_movimentos = mysql_query($sql_mov) or die(mysql_error());
$qtd_movimentos = mysql_num_rows($qr_movimentos);

while ($row_movimentos = mysql_fetch_assoc($qr_movimentos)) {

    if($_COOKIE['logado'] == 179){
        echo "<pre>";
        print_r($row_movimentos);
        echo "</pre>";
    }


    $movimentos[$row_movimentos['campo_rescisao']] += $row_movimentos['valor'];
    $quantidade[$row_movimentos['campo_rescisao']] = $row_movimentos['qnt_horas']; //verificaQuantidade($row_movimentos['tipo_qnt'], $row_movimentos['qnt'], $row_movimentos['qnt_horas']);

    /*     * **************ARRAY DE MOVIMENTOS 20/05/2015******************** */

    $nome_movimento = "";

    /**
     * By Ramon 19/07/2016
     * A pedido da Alana juntando alguns movimentos no mesmo campo de AVISO PR�VIO
     * VERIFICANDO PRIMEIRO SE TEM AVISO PREVIO INDENIZADO
     * S� VAI CRIAR INFORMA��O NA MATRIZ DE $dados_recisao PARA O MOVIMENTO "96 - MEDIA SOBRE AVISO PREVIO" SE N�O TIVER VALOR NO AVISO PR�VIO
     * POIS SE TIVER O AVISO JA EST� SOMADO O MOVI "96 - MEDIA SOBRE AVISO PREVIO"
     */
    if (isset($dados_recisao[69]["valor"]) && $row_movimentos['campo_rescisao'] == 96) {
        //SOMAR O VALOR DO MOVIMENTO COM OS VALORES J� REGISTRADOS NA MATRIZ
        $dados_recisao[69]["valor"] += $row_movimentos['valor'];
        //PULA ESSE MOVIMENTO DO ARRAY, E CONTINUA O WHILE DO PROXIMO MOVIMENTO
        continue;
    }


    //TRATANDO NOME DO MOVIMENTOS JAJ LAN�ADOS
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
    } else if($row_movimentos['campo_rescisao'] == 63) {
        $nome_movimento = $dados_recisao[63]['movimento'];
    }else{
        $nome_movimento = $row_movimentos['descicao'];
    }

    if ($row_movimentos['valor'] > 0) {



        $dados_recisao[(String) $row_movimentos['campo_rescisao']]["movimento"] = $nome_movimento;
        $dados_recisao[(String) $row_movimentos['campo_rescisao']]["tipo"] = $row_movimentos['categoria'];
        $dados_recisao[(String) $row_movimentos['campo_rescisao']]["valor"] += $row_movimentos['valor'];
        $dados_recisao[(String) $row_movimentos['campo_rescisao']]["percentual"] = $row_movimentos['percentual'] * 100;
        $dados_recisao[(String) $row_movimentos['campo_rescisao']]["qtd"] = $row_movimentos['percentual'] * 100 . "%";

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

//F�RIAS PROPORCIONAIS
$dados_recisao[65] = array(
    "movimento" => "F�rias Proporcionais " . sprintf('%02d', $row_rescisao['avos_fp']) . "/12 avos<br>{$periodo_aquisitivo_fp}",
    "tipo" => "CREDITO",
    "qtd" => sprintf('%02d', $row_rescisao['avos_fp'])."/12 avos",
    "valor" => $row_rescisao['ferias_pr']
);


//F�RIAS VENCIDAS
$texto_fv = "F�rias Vencidas <br /> Per. Aquisitivo ";
//$texto_fv .= ' de ' . formato_brasileiro($row_rescisao['fv_data_ini']) . " � " . formato_brasileiro($row_rescisao['fv_data_fim']) . "<br>";

if ($row_rescisao['ferias_vencidas'] != '0.00') {
    $texto_fv .= '12/12 avos';
    $texto_fv .= 'de ' . formato_brasileiro($row_rescisao['fv_data_ini']) . " � " . formato_brasileiro($row_rescisao['fv_data_fim']) . "<br>";
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
 * A pedido do �talo, foi separado 
 * o campo 68(ter�o constitucional de f�rias) 
 * do campo 52(1/3 f�rias proporcionais)
 */

//TER�O CONSTITUCIONAL DE FERIAS

//if (isset($_COOKIE['debug']) == 'lucas') {
//    print_array($row_rescisao['umterco_fv']);
//}

$tercoConstitucionalFerias = $row_rescisao['umterco_fv'];
//if ($complementar == "COMPLEMENTAR") {
//    $tercoConstitucionalFerias = 0;
//}
$dados_recisao[68] = array(
    "movimento" => "Ter�o Constitucional de F�rias",
    "tipo" => "CREDITO",
//    "valor" => $row_rescisao['umterco_fv'] + $row_rescisao['umterco_fp']
    "valor" => $tercoConstitucionalFerias
);

$dados_recisao[52] = array(
    "movimento" => "1/3 f�rias proporcionais",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['umterco_fp']
);

if ($id_clt == 3931) {
    $dados_recisao[68] = array(
        "movimento" => "Ter�o Constitucional de F�rias",
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
        "movimento" => "Ter�o Constitucional de F�rias",
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

//13� SAL�RIO (AVISO PREVIO INDENIZADO)
$dados_recisao[70] = array(
    "movimento" => "13� Sal�rio (Aviso-Pr�vio Indenizado)",
    "tipo" => "CREDITO",
    "qtd" => "{$qntAvos13indenizado}/12 avos",
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

if ($id_clt == 843) {
    $qntAvosFeriasIndenizado = 1;
}

//13� SAL�RIO (AVISO PREVIO INDENIZADO)
$dados_recisao[71] = array(
    "movimento" => "F�rias (Aviso-Pr�vio Indenizado)",
    "tipo" => "CREDITO",
    "qtd" => "{$qntAvosFeriasIndenizado}/12 avos",
    "valor" => $row_rescisao['ferias_aviso_indenizado']
);

if ($id_clt == 2987) {
    $dados_recisao[71] = array(
        "movimento" => "F�rias (Aviso-Pr�vio Indenizado 2/12 avos)",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['ferias_aviso_indenizado']
    );
}

//F�RIAS EM DOBRO
$dados_recisao[72] = array(
    "movimento" => "F�rias em dobro",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['fv_dobro']
);

//1/3 F�RIAS EM DOBRO
$dados_recisao[73] = array(
    "movimento" => "1/3 f�rias em dobro",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['um_terco_ferias_dobro']
);

//1/3 F�RIAS EM DOBRO

$umterco_ferias_aviso_indenizado = $row_rescisao['umterco_ferias_aviso_indenizado'];
if ($complementar == "COMPLEMENTAR") {
    $umterco_ferias_aviso_indenizado = 0;
}
$dados_recisao[75] = array(
    "movimento" => "1/3 F�rias (Aviso Pr�vio Indenizado)",
    "tipo" => "CREDITO",
    "valor" => $umterco_ferias_aviso_indenizado
);

if ($id_clt == 3931) {
    $dados_recisao[75] = array(
        "movimento" => "1/3 F�rias (Aviso Pr�vio Indenizado)",
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
        "movimento" => "1/3 F�rias (Aviso Pr�vio Indenizado)",
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

//ADIANTAMENTO DE 13� SAL�RIO
$dados_recisao[101] = array(
    "movimento" => "Adiantamento Salarial",
    "tipo" => "DEBITO",
    "valor" => $row_rescisao['adiantamento']
);

//ADIANTAMENTO DE 13� SAL�RIO
$dados_recisao[102] = array(
    "movimento" => "Adiantamento de 13� Sal�rio",
    "tipo" => "DEBITO",
    "valor" => $row_rescisao['adiantamento_13']
);

//ADIANTAMENTO DE 13� SAL�RIO
$dados_recisao[105] = array(
    "movimento" => "Empr�stimo em Consigna��o",
    "tipo" => "DEBITO",
    "valor" => 0.00
);

//PREVID�NCIA SOCIAL
$dados_recisao["112.1"] = array(
    "movimento" => "Previd�ncia Social",
    "tipo" => "DEBITO",
    "valor" => $row_rescisao['inss_ss']
);

//PREVID�NCIA SOCIAL 13 SALARIO
$dados_recisao["112.2"] = array(
    "movimento" => "Previd�ncia Social - 13� Sal�rio",
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
    "movimento" => "IRRF sobre 13� Sal�rio",
    "tipo" => "DEBITO",
    "valor" => $row_rescisao['ir_dt']
);

//IRRF
$dados_recisao[116] = array(
    "movimento" => "IRRF F�rias",
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

//if (isset($_COOKIE['debug']) == 'lucas') {
//print_array([$row_rescisao['umterco_fv'], $umterco]);
//}

$umterco = (empty($umterco) || ($umterco == '0.00')) ? $movimentos[68] : $umterco; //$movimentos[50]

//if (isset($_COOKIE['debug']) == 'lucas') {
//    print_array([$row_rescisao['umterco_fv'], $umterco]);
//}

//    print_array($row_rescisao);

}

//print_array($dados_recisao);

}

//print_array($clts);
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Ficha Hist�rica - Rescis�o</title>        
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
    </head>
    <body>
        <div class="container">
            <div id="content">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td>Projeto</td>
                            <td>Matricula</td>
                            <td>Nome</td>
                            <td>CPF</td>
                            <td>M�s Ref.</td>
                            <td>C�d. da R�brica</td>
                            <td>Nome da R�brica</td>
                            <td>Folha Calculada</td>
                            <td>Qtd</td>
                            <td>Valor</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clts as $clt){
                            
                            foreach ($dados_recisao as $cod => $mov){
                                if($mov['valor'] > 0){
                        ?>
                        <tr>
                            <td><?php echo $clt['nome_projeto']; ?></td>
                            <td><?php echo $clt['matricula']; ?></td>
                            <td><?php echo $clt['nome_clt']; ?></td>
                            <td><?php echo $clt['cpf']; ?></td>
                            <td><?php echo $clt['referencia']; ?></td>                            
                            <td><?php echo $cod; ?></td>
                            <td><?php echo $mov['movimento']; ?></td>
                            <td>Rescis�o</td>
                            <td><?php echo ($mov['qtd'] <= 0) ? '' : $mov['qtd']; ?></td>
                            <td><?php echo formataMoeda($mov['valor'], 1); ?></td>
                        </tr>
                        <?php 
                                }
                            unset($mov);
                            }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>