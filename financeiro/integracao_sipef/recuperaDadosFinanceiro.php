<?php

/**
 * 
 */
include_once('../../conn.php');
include_once('webServiceSipef.php');

/**
 * OBJETO
 */
$webservice = new webServiceSipef();
 
/**
 * URL
 */
$url = "http://40.117.155.6:8055/api/LancamentoDiarioF71/docfinanceirof71";

/**
 * CONSULTA SAIDAS
*/
$qrySaidas = "SELECT 
	A.id_saida as Id,
	B.cnpj as PrestacaoContaCNPJ,
	'' as DataFimExercicio,
	'' as DataInicioExercicio,
	A.nome as RazaoSocialOS,
	'' as ContaFinanceiraId,
	'' as ContaContabil,
	A.especifica as ContaFinanceiraDescricao,
	'' as ContaFinanceiraContaContabil,
	A.id_banco as BancoId,
	'' as BancoAgencia,
	'' as BancoAgenciaDig,
	'' as BancoAgenciaConta,
	'' as BancoAgenciaContaDig,
	'' as BancoNome,
	'' as BancoAgenciaContaSaldoAnterior,
	'' as OperacaoId,
	'' as OperacaoDescricao,
	'' as Classificacao,
	'' as DocTipoId,
	'' as NOperacao,
	'' as NDocFin,
	A.data_vencimento as Data,
	'' as SerieNF,
	A.valor as Valor,
	'' as NFDoc,
	'' as DataNfDoc,
	'' as NContrato,
	'' as ContratoObjeto,
	A.cnpj_prestador as CnpjContrato,
	'' as ContratoDescricao,
	'' as ContratoInicioVigencia,
	'' as ContratoFimVigencia,
	'' as ContratoValor,
	'' as ContratoValorParcela,
	'' as ContratoNSerieParcela,
	'' as PossuiContrato,
	'' as VencimentoNota,
	'' as NCertidao,
	A.cnpj_fornecedor as CNPJCPFParticipante,
	A.nome_fornecedor as NomeParticipante,
	'' as SaldoAnterior,
	'' as ValorDoc,
	'' as MultaJuros,
	'' as Desconto,
	'' as ICMS,
	'' as ISS,
	'' as IR,
	'' as PIS,
	'' as COFINS,
	'' as CSLL,
	'' as Conciliado,
	'' as Historico,
	'' as Complemento,
	'' as TipoFornecedorId	
    FROM saida AS A 
        LEFT JOIN rhempresa as B ON(A.id_projeto = B.id_projeto)
    LIMIT 1";
$sqlSaidas = mysql_query($qrySaidas) or die('Nenhuma Saida Encontrada' . mysql_error());
$params = array();
while($rowsSaidas =  mysql_fetch_assoc($sqlSaidas)){
     
    /*
    * PARAMETROS
    */
    $params = array(
           "Id" => $rowsSaidas['Id'],  
           "PrestacaoContaCNPJ" => $rowsSaidas['PrestacaoContaCNPJ'],
           "DataFimExercicio" => $rowsSaidas['DataFimExercicio'],
           "DataInicioExercicio" => $rowsSaidas['DataInicioExercicio'],
           "RazaoSocialOS" => utf8_encode($rowsSaidas['RazaoSocialOS']),
           "ContaFinanceiraId" => $rowsSaidas['ContaFinanceiraId'],
           "ContaContabil" => $rowsSaidas['ContaContabil'],
           "ContaFinanceiraDescricao" => utf8_encode($rowsSaidas['ContaFinanceiraDescricao']),
           "ContaFinanceiraContaContabil" => $rowsSaidas['ContaFinanceiraContaContabil'],
           "BancoId" => $rowsSaidas['BancoId'],
           "BancoAgencia" => $rowsSaidas['BancoAgencia'],
           "BancoAgenciaDig" => $rowsSaidas['BancoAgenciaDig'],
           "BancoAgenciaConta" => $rowsSaidas['BancoAgenciaConta'],
           "BancoAgenciaContaDig" => $rowsSaidas['BancoAgenciaContaDig'],
           "BancoNome" => $rowsSaidas['BancoNome'],
           "BancoAgenciaContaSaldoAnterior" => $rowsSaidas['BancoAgenciaContaSaldoAnterior'],
           "OperacaoId" => $rowsSaidas['OperacaoId'],
           "OperacaoDescricao" => $rowsSaidas['OperacaoDescricao'],
           "Classificacao" => $rowsSaidas['Classificacao'],
           "DocTipoId" => $rowsSaidas['DocTipoId'],
           "NOperacao" => $rowsSaidas['NOperacao'],
           "NDocFin" => $rowsSaidas['NDocFin'],
           "Data" => $rowsSaidas['Data'],
           "SerieNF" => $rowsSaidas['SerieNF'],
           "Valor" => $rowsSaidas['Valor'],
           "NFDoc" => $rowsSaidas['NFDoc'],
           "DataNfDoc" => $rowsSaidas['DataNfDoc'],
           "NContrato" => $rowsSaidas['NContrato'],
           "ContratoObjeto" => $rowsSaidas['ContratoObjeto'],
           "CnpjContrato" => $rowsSaidas['CnpjContrato'],
           "ContratoDescricao" => $rowsSaidas['ContratoDescricao'],
           "ContratoInicioVigencia" => $rowsSaidas['ContratoInicioVigencia'],
           "ContratoFimVigencia" => $rowsSaidas['ContratoFimVigencia'],
           "ContratoValor" => $rowsSaidas['ContratoValor'],
           "ContratoValorParcela" => $rowsSaidas['ContratoValorParcela'],
           "ContratoNSerieParcela" => $rowsSaidas['ContratoNSerieParcela'],
           "PossuiContrato" => $rowsSaidas['PossuiContrato'],
           "VencimentoNota" => $rowsSaidas['VencimentoNota'],
           "NCertidao" => $rowsSaidas['NCertidao'],
           "CNPJCPFParticipante" => $rowsSaidas['CNPJCPFParticipante'], 
           "NomeParticipante" => $rowsSaidas['NomeParticipante'],
           "SaldoAnterior" => $rowsSaidas['SaldoAnterior'],
           "ValorDoc" => $rowsSaidas['ValorDoc'],
           "MultaJuros" => $rowsSaidas['MultaJuros'],
           "Desconto" => $rowsSaidas['Desconto'],
           "ICMS" => $rowsSaidas['ICMS'],
           "ISS" => $rowsSaidas['ISS'],
           "IR" => $rowsSaidas['IR'],
           "PIS" => $rowsSaidas['PIS'],
           "COFINS" => $rowsSaidas['COFINS'],
           "CSLL" => $rowsSaidas['CSLL'],
           "Conciliado" => $rowsSaidas['Conciliado'],
           "Historico" => $rowsSaidas['Historico'],
           "Complemento" => $rowsSaidas['Complemento'],
           "TipoFornecedorId" => $rowsSaidas['TipoFornecedorId']
       );
    
    /**
     * POST 
     */
    $retorno = $webservice->httpPost($url, $params);
    echo "<pre>";
        print_r($rowsSaidas);
        print_r($retorno);
    echo "</pre>";
    
} 