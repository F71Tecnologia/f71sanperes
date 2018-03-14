<?php
if(!isset($_COOKIE['logado'])) {
    header('Location: http://www.netsorrindo.com/intranet/login.php?entre=true');
    exit;
}

include('../../conn.php');
include('../../funcoes.php');
include('../../wfunction.php');
include('../../classes/clt.php');
include('../../classes/abreviacao.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../classes/valor_proporcional.php');

$dados_recisao = array();

// Recebendo a Vari�vel Criptografada
list($regiao, $id_clt, $id) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));

$status = '1';
if(isset($_REQUEST['reitegracao'])) $status = '0';

// Consulta da Rescis�o
$qr_rescisao = mysql_query("SELECT * FROM rh_recisao WHERE id_recisao = '$id' AND status = '{$status}'");
$row_rescisao = mysql_fetch_array($qr_rescisao);

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

// CALCULO DE DIAS PARA LEI 12506
$qtd_ano_12506 = diferencaData($data_entrada, $data_demi, 'y');

$qtd_dias_12506 = 0;

for($i=0; $i<$qtd_ano_12506; $i++){
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

/*****ARRAY DE CAMPOS DE MOVIMENTOS OBRIGAT�RIOS 22/05/2015*********/
/******************MESMO COM VALOR ZERADO***************************/

    
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
        "valor" => 0.00,
    	"campo" => ''
    );

    
/*******************************************************************/

/****************ARRAY DE MOVIMENTOS 20/05/2015*********************/
//SALDO DE SAL�RIO
$dados_recisao[50] = array(
    "movimento" => 'Saldo de <span contenteditable="true" data-id="'.$id.'" data-coluna="dias_saldo" title="Editar valor" data-tipo="individual">'.$row_rescisao['dias_saldo'].'</span> dias Sal�rio',
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['saldo_salario'],
    "campo" => 'saldo_salario'
);

//COMISS�O
if(!is_null($row_rescisao['comissao']) && $row_rescisao['comissao'] > 0){
    $dados_recisao[51] = array(
        "movimento" => "Comiss�es",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['comissao'],
        "campo" => 'comissao'
    );
}

//INSALUBRIDADE
if(!is_null($row_rescisao['insalubridade']) && $row_rescisao['insalubridade'] > 0){
    $dados_recisao[53] = array(
        "movimento" => "Adicional de Insalubridade",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['insalubridade'],
   		"campo" => 'insalubridade'
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
    "valor" => $row_rescisao['a477'],
   	"campo" => 'a477'
);

//SALARIO FAMILIA
$dados_recisao[62] = array(
    "movimento" => 'Sal�rio-Fam�lia',
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['sal_familia'],
   	"campo" => 'sal_familia'
);

/*
 * 11/05/2016
 * by: MAX
 * SOLICITADO PELO �TALO(IABAS)
 * PQ ESTAVA TRAZENDO NUM CAMPO COM NUMERO 0(ZERO)
 * ENT�O ELE DISTRIBUI ESSES MOVIMENTOS
 * EM ALGUNS CAMPOS DA RESCIS�O
 */

$sql_movTemp = "SELECT B.descicao, B.id_mov, A.valor,B.categoria, C.tipo_movimento, C.valor_movimento, B.campo_rescisao, B.percentual, A.tipo_qnt, A.qnt, C.qnt_horas
                FROM rh_movimentos_rescisao AS A
                LEFT JOIN rh_movimentos AS B ON(A.id_mov = B.id_mov)
                LEFT JOIN (SELECT * FROM rh_movimentos_clt AS A WHERE A.id_clt = '{$row_rescisao[id_clt]}' AND A.status = 1) AS C ON(B.id_mov = C.id_mov)
                WHERE A.id_clt = '{$row_rescisao['id_clt']}' 
                AND A.id_mov IN(384,385,386,387,388)
                AND A.status = 1 GROUP BY A.id_mov";
$qr_movimentosTemp = mysql_query($sql_movTemp) or die(mysql_error());

while ($row_movimentosTemp = mysql_fetch_assoc($qr_movimentosTemp)) {
    if($row_movimentosTemp['id_mov'] == 384){
        $row_rescisao['dt_salario'] = $row_rescisao['dt_salario'] + $row_movimentosTemp['valor'];
    }
    
    if($row_movimentosTemp['id_mov'] == 385){
        $row_rescisao['ferias_vencidas'] = $row_rescisao['ferias_vencidas'] + $row_movimentosTemp['valor'];
    }
    
    if($row_movimentosTemp['id_mov'] == 387){
        $row_rescisao['ferias_pr'] = $row_rescisao['ferias_pr'] + $row_movimentosTemp['valor'];
    }
    
    if($row_movimentosTemp['id_mov'] == 386){
        $row_rescisao['umterco_fp'] = $row_rescisao['umterco_fp'] + $row_movimentosTemp['valor'];
    }
    
    if($row_movimentosTemp['id_mov'] == 388){
        $row_rescisao['umterco_fp'] = $row_rescisao['umterco_fp'] + $row_movimentosTemp['valor'];
    }
}

//DECIMO TERCEIRO
$avos_dt = sprintf('%02d', $row_rescisao['avos_dt']);
$dados_recisao[63] = array(
    "movimento" => '13� Sal�rio Proporcional <span contenteditable="true" data-id="'.$id.'" data-coluna="avos_dt" title="Editar valor" data-tipo="individual">'.$avos_dt.'</span>/12 avos',
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['dt_salario'],
   	"campo" => 'dt_salario'
);

//DECIMO TERCEIRO EXERCICIO
$dados_recisao[64] = array(
    "movimento" => '13� Sal�rio Exerc�cio 0/12 avos',
    "tipo" => "CREDITO",
    "valor" => 0.00,
   	"campo" => ''
);

// AVISO PREVIO
if ($row_rescisao['fator'] == 'empregado' && $row_rescisao['aviso'] == 'PAGO pelo ') {
    $aviso_previo_debito = $row_rescisao['aviso_valor'];
    $dados_recisao[103] = array(
        "movimento" => "Aviso-Pr�vio Indenizado",
        "tipo" => "DEBITO",
        "valor" => $row_rescisao['aviso_valor'],
   		"campo" => 'aviso_valor'
    );
} else {
    $aviso_previo_credito = $row_rescisao['aviso_valor'];
    $dados_recisao[69] = array(
        "movimento" => "Aviso-Pr�vio Indenizado",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['aviso_valor'],
   		"campo" => 'aviso_valor'
    );
}

///DISPENSA ANTES DO TERMINO DE CONTRATO PELA EMPRESA
if ($row_rescisao['motivo'] != '63' and $row_rescisao['motivo'] != '65') {
    $multa_479 = $row_rescisao['a479'];
    $dados_recisao[61] = array(
        "movimento" => "Multa Art. 479/CLT",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['a479'],
   		"campo" => 'a479'
    );
} else {
    //INVERSO
    $multa_480 = $row_rescisao['a479'];
    $dados_recisao[104] = array(
        "movimento" => "Multa Art. 480/CLT",
        "tipo" => "DEBITO",
        "valor" => $row_rescisao['a480'],
   		"campo" => 'a480'
    );
}

//LEI 12.506
$dados_recisao[95] = array(
    //"movimento" => "Lei 12.506 ({$row_rescisao['qnt_dias_lei_12_506']} dias)",
    "movimento" => "Lei 12.506 ({$qtd_dias_12506} dias)",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['lei_12_506'],
   	"campo" => 'lei_12_506'
);
    
//PENS�O ALIMENT�CIA 
//$dados_recisao[100] = array(
//    "movimento" => "Pens�o Aliment�cia",
//    "tipo" => "DEBITO",
//    "valor" => 0.00,
//    "percentual" => 0.00,
//);

 
/**************************************************************** */
//Pegando os valores dos moviemntos e inserindo nos campos de acordo com o ANEXO VIII da rescis�o, o n�mero do campo encontra-se na tabela rh_movimento

if($row_rescisao['rescisao_complementar'] == 1){
    $and_complementar = "AND A.complementar = 1";
}

$sql_mov = "SELECT B.descicao, B.id_mov, A.valor,B.categoria, C.tipo_movimento, C.valor_movimento, B.campo_rescisao, B.percentual, A.tipo_qnt, A.qnt, C.qnt_horas
                            FROM rh_movimentos_rescisao AS A
                            LEFT JOIN rh_movimentos AS B ON(A.id_mov = B.id_mov)
                            LEFT JOIN (SELECT * FROM rh_movimentos_clt AS A WHERE A.id_clt = '{$row_rescisao[id_clt]}' AND A.status = 1) AS C ON(B.id_mov = C.id_mov)
                            WHERE A.id_clt = '{$row_rescisao[id_clt]}' 
                            AND A.id_mov NOT IN(384,385,386,387,388)
                            AND A.status = 1 {$and_complementar} GROUP BY A.id_mov"; //AND C.id_clt = '{$row_rescisao[id_clt]}' AND C.status = '1'
//echo $sql_mov;
$qr_movimentos = mysql_query($sql_mov) or die(mysql_error());

while ($row_movimentos = mysql_fetch_assoc($qr_movimentos)) {

    if ($_COOKIE['logado'] == 179) {
        echo "<pre>";
        print_r($row_movimentos);
        echo "</pre>";
    }


    $movimentos[$row_movimentos['campo_rescisao']] += $row_movimentos['valor'];
    $quantidade[$row_movimentos['campo_rescisao']] = $row_movimentos['qnt_horas']; //verificaQuantidade($row_movimentos['tipo_qnt'], $row_movimentos['qnt'], $row_movimentos['qnt_horas']);

    /*     * **************ARRAY DE MOVIMENTOS 20/05/2015******************** */

    $nome_movimento = "";

    //TRATANDO NOME DO MOVIMENTOS JAJ LAN�ADOS
    if ($row_movimentos['campo_rescisao'] == 117) {                
        $quant_faltas += $row_movimentos['qnt'];
        $quant_horas += $row_movimentos['qnt_horas'];
        
        if($quant_faltas > 0){
            $nome_movimento = "Faltas ({$quant_faltas} dias)";
        }else{
            $nome_movimento = "Faltas ({$quant_horas} horas)";
        }
        
    }else if($row_movimentos['campo_rescisao'] == 58){
        $nome_movimento = "Descanso Semanal Remunerado (DSR)";
    }else {
        $nome_movimento = $row_movimentos['descicao'];
    }

    if($row_movimentos['valor'] > 0) { 
        $dados_recisao[(String) $row_movimentos['campo_rescisao']]["movimento"] = $nome_movimento;
        $dados_recisao[(String) $row_movimentos['campo_rescisao']]["tipo"] = $row_movimentos['categoria'];
        $dados_recisao[(String) $row_movimentos['campo_rescisao']]["valor"] += $row_movimentos['valor'];
        $dados_recisao[(String) $row_movimentos['campo_rescisao']]["percentual"] = $row_movimentos['percentual'] * 100;
    }
    /***************************************************************** */
    
    if($row_movimentos['id_mov'] == 292){
        $adiantamento_13 = $row_movimentos['valor'];
    }
}

/* * **************ARRAY DE MOVIMENTOS 20/05/2015******************** */

//F�RIAS PROPORCIONAIS
$dados_recisao[65] = array(
    "movimento" => 'F�rias Proporcionais <span contenteditable="true" data-id="'.$id.'" data-coluna="avos_fp" title="Editar valor" data-tipo="individual">'.sprintf('%02d', $row_rescisao['avos_fp']).'</span>/12 avos<br>'.$periodo_aquisitivo_fp,
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['ferias_pr'],
    "campo" => "ferias_pr"
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
    "valor" => $row_rescisao['ferias_vencidas'],
    "campo" => 'ferias_vencidas'
);

//TER�O CONSTITUCIONAL DE FERIAS
$dados_recisao[68] = array(
    "movimento" => "Ter�o Constitucional de F�rias Vencidas",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['umterco_fv'],
    "campo" => 'umterco_fv'
);
$dados_recisao[68.5] = array(
    "movimento" => "Ter�o Constitucional de F�rias Proporcionais",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['umterco_fp'],
    "campo" => 'umterco_fp'
);

//13� SAL�RIO (AVISO PREVIO INDENIZADO)
$dados_recisao[70] = array(
    "movimento" => "13� Sal�rio (Aviso-Pr�vio Indenizado 1/12 avos)",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['terceiro_ss'],
    "campo" => 'terceiro_ss'
);

//13� SAL�RIO (AVISO PREVIO INDENIZADO)
$dados_recisao[71] = array(
    "movimento" => "F�rias (Aviso-Pr�vio Indenizado 1/12 avos)",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['ferias_aviso_indenizado'],
    "campo" => 'ferias_aviso_indenizado'
);

//F�RIAS EM DOBRO
$dados_recisao[72] = array(
    "movimento" => "F�rias em dobro",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['fv_dobro'],
    "campo" => 'fv_dobro'
);

//1/3 F�RIAS EM DOBRO
$dados_recisao[73] = array(
    "movimento" => "1/3 f�rias em dobro",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['um_terco_ferias_dobro'],
    "campo" => 'um_terco_ferias_dobro'
);

//1/3 F�RIAS EM DOBRO
$dados_recisao[75] = array(
    "movimento" => "1/3 F�rias (Aviso Pr�vio Indenizado)",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['umterco_ferias_aviso_indenizado'],
    "campo" => 'umterco_ferias_aviso_indenizado'
);

//AJUSTE DE SALDO DEVEDOR
$dados_recisao[99] = array(
    "movimento" => "Ajuste do Saldo Devedor",
    "tipo" => "CREDITO",
    "valor" => $row_rescisao['arredondamento_positivo'],
    "campo" => 'arredondamento_positivo'
);

//ADIANTAMENTO DE 13� SAL�RIO
$dados_recisao[101] = array(
    "movimento" => "Adiantamento Salarial",
    "tipo" => "DEBITO",
    "valor" => $row_rescisao['adiantamento'],
    "campo" => 'adiantamento'
);

//ADIANTAMENTO DE 13� SAL�RIO
$dados_recisao[102] = array(
    "movimento" => "Adiantamento de 13� Sal�rio",
    "tipo" => "DEBITO",
    "valor" => $adiantamento_13,
    "campo" => ''
);

//ADIANTAMENTO DE 13� SAL�RIO
$dados_recisao[105] = array(
    "movimento" => "Empr�stimo em Consigna��o",
    "tipo" => "DEBITO",
    "valor" => 0.00,
    "campo" => ''
);

//PREVID�NCIA SOCIAL
$dados_recisao["112.1"] = array(
    "movimento" => "Previd�ncia Social",
    "tipo" => "DEBITO",
    "valor" => $row_rescisao['inss_ss'],
    "campo" => 'inss_ss'
);

//PREVID�NCIA SOCIAL 13 SALARIO
$dados_recisao["112.2"] = array(
    "movimento" => "Previd�ncia Social - 13� Sal�rio",
    "tipo" => "DEBITO",
    "valor" => $row_rescisao['inss_dt'],
    "campo" => 'inss_dt'
);

//IRRF
$dados_recisao["114.1"] = array(
    "movimento" => "IRRF",
    "tipo" => "DEBITO",
    "valor" => $row_rescisao['ir_ss'],
    "campo" => 'ir_ss'
);

//IRRF
$dados_recisao["114.2"] = array(
    "movimento" => "IRRF sobre 13� Sal�rio",
    "tipo" => "DEBITO",
    "valor" => $row_rescisao['ir_dt'],
    "campo" => 'ir_dt'
);

//IRRF
$dados_recisao[116] = array(
    "movimento" => "IRRF F�rias",
    "tipo" => "DEBITO",
    "valor" => $row_rescisao['ir_ferias'],
    "campo" => 'ir_ferias'
);

//ORDERNANDO O ARRAY
ksort($dados_recisao);

//print_r($dados_recisao);

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
        <style type="text/css">
            .font13{
                font-size: 12px;
                font-style: normal;
                font-weight: 100;
            }
            span[contenteditable=true] {
            	background-color:#5A5A5A;
            	color:#FFF;
            	padding:4px;
            	border-radius:4px;
            }
            span[contenteditable=true]:hover, span[contenteditable=true]:focus {
            	background-color:#00A779;
            	cursor:pointer;
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <script>
			$(document).ready(function(){
        	
		    	$('span[contenteditable=true]').blur(function(){

					var tipo = $(this).data('tipo');
		    		var $tipos = [];
		    		$tipos['credito'] = $('#total_credito');
		    		$tipos['debito'] = $('#total_debito');
		    		$tipos['liquido'] = $('#total_liquido');

					var tipos = ['credito', 'debito'];
		    		for(i in tipos) {
		    			$tipos[tipos[i]]['valor'] = 0;
						$('span[data-tipo='+tipos[i]+']').each(function(){
							$tipos[tipos[i]]['valor'] += +trataValorBackend($(this).text());
						});
						$tipos[tipos[i]].text(number_format($tipos[tipos[i]]['valor'], 2, ',', '.'));
					}
					$tipos['liquido']['valor'] = $tipos['credito']['valor'] - $tipos['debito']['valor'];
					$tipos['liquido'].text(number_format($tipos['liquido']['valor'], 2, ',', '.'));
					
					var data = {
								id: $(this).data('id'),
								coluna: $(this).data('coluna'),
								valor: $(this).text(),
								tipo: tipo
								};
					if(tipo == 'credito' || tipo == 'debito') {
						data.total_tipo = $tipos[tipo]['valor'];
						data.total_liquido = $tipos['liquido']['valor'];
					}
					
		    		$.ajax({
						method: 'POST',
						url: 'rescisao_edicao_ajax.php',
						data: data
					});
					
		    	});
		    	
		    	function trataValorBackend(valor) {
		    		return valor.replace('.', '').replace(',', '.');
		    	}
		    	
		    	function number_format(number, decimals, dec_point, thousands_sep) {
					number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
					var n = !isFinite(+number) ? 0 : +number,
					prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
					sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
					dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
					s = '',
					toFixedFix = function(n, prec) {
						var k = Math.pow(10, prec);
						return '' + (Math.round(n * k) / k).toFixed(prec);
					};
					s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
					if(s[0].length > 3) {
						s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
					}
					if((s[1] || '').length < prec) {
						s[1] = s[1] || '';
						s[1] += new Array(prec - s[1].length + 1).join('0');
					}
					return s.join(dec);
				}
		    	
		    });
        </script>
    </head>
    <body>
        <table class="rescisao" cellpadding="0" cellspacing="1">
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
                    <div class="valor font13">1. Contrato de Trabalho por Prazo Indeterminado</div>
                </td>
                <td colspan="3">
                    <div class="campo font13"><span class="numero">22</span> Causa do Afastamento</div>
                    <div class="valor font13"><?php echo $row_motivo['especifica']; ?></div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="campo font13"><span class="numero">23</span> Remunera&ccedil;&atilde;o M&ecirc;s Anterior Afast.</div>
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
                    <div class="valor font13"><?php echo $dados_recisao[100]["percentual"];?>%</div>
                </td>
                <td>
                    <div class="campo font13"><span class="numero">29</span> Pens&atilde;o aliment&iacute;cia (%) (Saque FGTS)</div>
                    <div class="valor font13"><?php echo $dados_recisao[100]["percentual"];?>%</div>
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
                    <div class="valor font13"><?php echo $row_sindicato['cnpj'] . ' - ' . substr($row_sindicato['nome'], 0, 52); ?></div>
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
            <?php
            $movimentos_nao_editaveis = [58,59,64];
            $count = 1;
            $resto = 0; ?>
            <?php foreach ($dados_recisao as $key => $valores) { ?>
                <?php if ($valores['tipo'] == "CREDITO") { ?>
                    <?php if ($count == 1) { ?>
                        <tr>
                    <?php } ?>    

                        <td class="font13"><span class="numero"><?php echo $key; ?></span> <?php echo $valores['movimento']; ?></td>
                        <td class="font13">R$ <span <?php if(!in_array($key, $movimentos_nao_editaveis)) echo 'contenteditable="true"'; ?> data-id="<?php echo $id; ?>" data-coluna="<?php echo $valores['campo']; ?>" title="Editar valor" data-tipo="credito"><?php echo number_format($valores['valor'], 2, ',', '.'); ?><span></td>
                    <?php if ($count == 3) { ?>
                        </tr>
                        <?php } ?>    
                        <?php if ($count == 3) {
                            $count = 0;
                        } $count++; ?>
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
                <td class="secao">TOTAL RESCIS�RIO BRUTO</td>
                <td class="secao">R$ <span id="total_credito"><?php echo formato_real($row_rescisao['total_rendimento']); ?></span></td>
            </tr>
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
            <?php
            $movimentos_nao_editaveis = [102,105,106,109,117,119];
            $count = 1;
            $resto = 0; ?>
            <?php foreach ($dados_recisao as $key => $valores) { ?>
                <?php if ($valores['tipo'] == "DEBITO") { ?>
                    <?php if ($count == 1) { ?>
                        <tr>
                        <?php } ?>    
                        <td class="font13"><span class="numero"><?php echo $key; ?></span> <?php echo $valores['movimento']; ?></td>
                        <td class="font13">R$ <span <?php if(!in_array($key, $movimentos_nao_editaveis)) echo 'contenteditable="true"'; ?> data-id="<?php echo $id; ?>" data-coluna="<?php echo $valores['campo']; ?>" title="Editar valor" data-tipo="debito"><?php echo number_format($valores['valor'], 2, ',', '.'); ?><span></td>
                        <?php if ($count == 3) { ?>
                        </tr>
                    <?php } ?>    
                    <?php if ($count == 3) {
                        $count = 0;
                    } $count++; ?>
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
                <td class="secao">TOTAL DAS DEDU��ES</td>
                <td class="secao">R$ <span id="total_debito"><?php echo formato_real($row_rescisao['total_deducao']); ?></span></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="secao">VALOR RESCIS�RIO L�QUIDO</td>
                <td class="secao">R$ <span id="total_liquido"><?php echo formato_real($row_rescisao['total_liquido']); ?></span></td>
            </tr>
        </table>
        <?php if ($row_rescisao['um_ano'] >= 1 && $row_rescisao['motivo'] != 63) { ?>
            <table  class="rescisao" cellpadding="0" cellspacing="1" style="page-break-before: always; margin-top:20px; ">
                <tr>
                    <td colspan="6" class="secao"><h1>TERMO DE HOMOLOGA��O DE RESCIS�O DO CONTRATO DE TRABALHO</h1></td>
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
                        <div class="campo font13"><span class="numero">17</span> 17 CTPS (n�, s�rie, UF)</div>
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
                        <div class="campo font13"><span class="numero">27</span> C&oacute;d. afast.</div>
                        <div class="valor font13"><?php echo $row_motivo['codigo_afastamento']; ?></div>
                    </td>  
                    <td colspan="2">
                        <div class="campo font13"><span class="numero">29</span>Pens�o Aliment�cia (%) (FGTS)</div>
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
                        <div class="valor font13"><?php echo $row_sindicato['cnpj'] . ' - ' . substr($row_sindicato['nome'], 0, 52); ?></div>
                    </td>
                </tr>

                <tr style="border: 0px;">
                    <td colspan="6" style="border: 0px;">
                        <div class="campo font13">
                            Foi prestada, gratuitamente, assist&ecirc;ncia na rescis�o do contrato de trabalho, nos termos do art. 477, &sect; 1&ordm;,
                            da Consolida&ccedil;&atilde;o das Leis do Trabalho (CLT), sendo comprovado, neste ato, o efetivo pagamento das verbas rescis&oacute;rias
                            acima especificadas no corpo do TRCT, no valor l�quido de R$ <?php echo formato_real($row_rescisao['total_liquido']); ?>, o qual devidamente rubricado pelas partes, � parte integrante
                            do presente Termo de Homologa��o. <br />
                            </p>
                            <p>As partes assistidas no presente ato de rescis�o contratual foram identificadas como legitimas conforme previsto na Instru��o Normativa/SRT n� 15/2010</p>
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
                            152 Assinatura do Respons�vel Legal do Trabalhador
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
                            154 Nome do �rg�o Homologador
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
                        <div class="campo font13"><span class="numero">156</span> Informa��es � CAIXA</div> 
                        <p>&nbsp;</p>

                    </td>
                </tr>   
                <tr>
                    <td colspan="6" class="font13">
                        <p style="text-align:center;">
                            <strong> ASSIST�NCIA NO ATO DE RESCIS�O CONTRATUAL � GRATUITA.</strong><bR>
                                Pode o trabalhador iniciar a��o judicial quanto aos cr�ditos resultantes das rela��es de trabalho at� o limite de dois anos ap�s a extin��o do contrato de trabalho (inciso XXIX, art. 7� da Constitui��o Federal/1988).
                        </p>
                    </td>
                </tr>
            </table>

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
                        <div class="campo font13"><span class="numero">17</span> CTPS (n�, s�rie, UF)</div>
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
                        <div class="campo font13"><span class="numero">29</span> Pens�o Aliment�cia (%) (FGTS)</div>
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
                            <p> Foi realizada a rescis�o do contrato de trabalho do trabalhador acima qualificado, nos termos do artigo n� 477 da 
                                Consolida��o das Leis do Trabalho (CLT). A assist�ncia � rescis�o prevista no �1� do art. n� 477 da CLT n�o � devida, 
                                tendo em vista a dura��o do contrato de trabalho n�o ser superior a um ano de servi�o e n�o existir previs�o de 
                                assist�ncia � rescis�o contratual em Acordo ou Conven��o Coletiva de Trabalho da categoria a qual pertence o 
                                trabalhador.</p>
                            <p> No dia <?php echo implode('/', array_reverse(explode('-', $row_rescisao['data_demi']))) ?> foi realizado, nos termos do art. 23 da Instru��o Normativa/SRT n� 15/2010, o efetivo pagamento das 
                                verbas rescis�rias especificadas no corpo do TRCT, no valor l�quido de R$ <?php echo number_format($row_rescisao['total_liquido'], 2, ',', '.'); ?> ,o qual, devidamente rubricado pelas partes, � parte integrante do 
                                presente Termo de Quita��o.</p>
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
                            152 Assinatura do Respons�vel Legal do Trabalhador
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
                            <strong> ASSIST�NCIA NO ATO DE RESCIS�O CONTRATUAL � GRATUITA.</strong><bR>
                                Pode o trabalhador iniciar a��o judicial quanto aos cr�ditos resultantes das rela��es de trabalho at� o limite de dois anos ap�s a extin��o do contrato de trabalho (inciso XXIX, art. 7� da Constitui��o Federal/1988).
                        </p>
                    </td>
                </tr>
            </table>
        <?php } ?>
    </body>
</html>
