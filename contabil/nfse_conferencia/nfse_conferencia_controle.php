<?php

header('Content-Type: text/html; charset=iso-8859-1');
include("../../conn.php");
include("../../wfunction.php");
include("../../classes/NFSeClass.php");
include("../../classes/NFSeSolicitacaoCorrecaoClass.php");
include("../../classes/global.php");
include("../../classes/ContabilLoteClass.php");
require_once ('../../classes/ContabilContasSaldoClass.php');
require_once ('../../classes/ContabilLancamentoClass.php');
require_once ('../../classes/ContabilLancamentoItemClass.php');

$objContas = new ContabilContasSaldoClass();
$objLancamento = new ContabilLancamentoClass();
$objLancamentoItens = new ContabilLancamentoItemClass();

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$nfse = new NFSe(); // instancia obj nfe 
$nfse_correcao = new NFSseSolicitacaoCorrecaoClass();

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'conferenciaImposto') {

    $nfse_arr['nfse'] = $nfse->exibirNota($_REQUEST['id']);

    $query = "SELECT ValorCofins,ValorCsll,ValorInss,ValorIr,ValorPis,ValorServicos,ValorLiquidoNfse,PrestadorServico FROM nfse WHERE id_nfse = {$_REQUEST['id']};";
    
    $return = mysql_query($query) or die($query." <br> erro ao consultar".mysql_error());

    $row = mysql_fetch_assoc($return);
    $nfse_arr['val']['nfse']['CSLL'] = $row['ValorCsll'];
    $nfse_arr['val']['nfse']['COFINS'] = $row['ValorCofins'];
    $nfse_arr['val']['nfse']['INSS'] = $row['ValorInss'];
    $nfse_arr['val']['nfse']['IRRF'] = $row['ValorIr'];
    $nfse_arr['val']['nfse']['PIS'] = $row['ValorPis'];

    $valor_servico = $row['ValorServicos'];
    $valor_liquido = $row['ValorLiquidoNfse'];
    $id_prestador = $row['PrestadorServico'];

    $inscricao_estadual = mysql_result(mysql_query("SELECT c_ie FROM prestadorservico WHERE id_prestador = '{$id_prestador}' limit 1"), 0);
    
    $retencao_query = " SELECT * FROM retencao as A 
                        LEFT JOIN retencao_tipo as B ON (A.id_retencao_tipo = B.id_retencao_tipo)
                        WHERE A.id_prestador = {$id_prestador}";
    $res_query = mysql_query($retencao_query);
    $num_rows_retencao = mysql_num_rows($res_query);
    
    while($row_retencao = mysql_fetch_assoc($res_query)){
        $nfse_arr["retencao"][$row_retencao["nome"]] = $row_retencao["valor"];
    }
    
//    $retencao_query_padrao = "SELECT * FROM retencao_tipo" ;
//    $res_query_padrao = mysql_query($retencao_query_padrao);
//    $num_rows_retencao_padrao = mysql_num_rows($res_query_padrao);
//    
//    while($row_retencao_padrao = mysql_fetch_assoc($res_query_padrao)){
//        $nfse_arr["retencao_padrao"][$row_retencao_padrao["nome"]] = $row_retencao_padrao['valor_padrao'];
//    }
    
//    $query = "SELECT * 
//                FROM contabil_impostos_assoc AS a
//                INNER JOIN contabil_impostos AS b ON a.id_imposto = b.id_imposto
//                WHERE id_contrato = $id_prestador";
//    $return = mysql_query($query);
//
//    while ($row = mysql_fetch_assoc($return)) {
//        $nfse_arr['val']['calculo'][$row['sigla']] = ($row['aliquota'] * $valor_servico) / 100.00;
//    }

    include 'nfse_visualizacao.php';
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'confirmaOk') {

//    print_array($_REQUEST);
    
    $array_atualiza = array(
        'ValorServicos' => addslashes(str_replace(',', '.', str_replace('.', '', $_REQUEST['ValorServicos']))),
        'ValorCofins' => addslashes(str_replace(',', '.', str_replace('.', '', $_REQUEST['COFINS']))),
        'ValorCsll' => addslashes(str_replace(',', '.', str_replace('.', '', $_REQUEST['CSLL']))),
        'ValorInss' => addslashes(str_replace(',', '.', str_replace('.', '', $_REQUEST['INSS']))),
        'ValorIr' => addslashes(str_replace(',', '.', str_replace('.', '', $_REQUEST['IRRF']))),
        'ValorPis' => addslashes(str_replace(',', '.', str_replace('.', '', $_REQUEST['PIS']))),
        'ValorDeducao' => addslashes(str_replace(',', '.', str_replace('.', '', $_REQUEST['ValorDeducao']))),
        'ValorDesconto' => addslashes(str_replace(',', '.', str_replace('.', '', $_REQUEST['ValorDesconto']))),
        'BaseCalculo' => addslashes(str_replace(',', '.', str_replace('.', '', $_REQUEST['BaseCalculo']))),
        'ValorIss' => addslashes(str_replace(',', '.', str_replace('.', '', $_REQUEST['ValorIss']))),
        'Aliquota' => addslashes(str_replace(',', '.', str_replace('.', '', $_REQUEST['Aliquota']))),
        'Credito' => addslashes(str_replace(',', '.', str_replace('.', '', $_REQUEST['Credito']))),
        'ValorLiquidoNfse' => addslashes(str_replace(',', '.', str_replace('.', '', $_REQUEST['ValorLiquidoNfse']))),
        'Discriminacao' => utf8_decode(addslashes($_REQUEST['Discriminacao'])),
        'OutrasInformacoes' => utf8_decode(addslashes($_REQUEST['OutrasInformacoes'])),
    );

    $nfse->atualizarRetencoes($_REQUEST['id_nfse'], $array_atualiza);

    $queryx = "SELECT nome FROM projeto WHERE id_projeto = {$_REQUEST['id_projeto']}";
    $proj_nome = mysql_fetch_assoc(mysql_query($queryx));

    $arr_competencia = explode('-', $_REQUEST['Competencia']);
    $mes_vencimento = $arr_competencia[1];
    $ano_vencimento = $arr_competencia[0];

    // ---- lancamentos contabeis (inicio)--------------------------------------
//    $objLote = new ContabilLoteClass();
//    $objLote->setIdProjeto($_REQUEST['id_projeto']);
//    $objLote->setMes($mes_vencimento);
//    $objLote->setAno($ano_vencimento);
//    $objLote->setStatus(1);
//    $objLote->setUsuarioCriacao($_COOKIE['logado']);
//    $objLote->setDataCriacao(date('Y-m-d H:i:s'));
//    $objLote->setLoteNumero($proj_nome['nome'] . " " . sprintf("%02d", $mes_vencimento) . "/{$ano_vencimento} - FINANCEIRO");
//    $objLote->setTipo(3);
//    $objLote->verificaLote();
    
    $rowTrava = mysql_fetch_array(mysql_query("SELECT * FROM contabil_trava WHERE id_projeto = $projeto_id AND mes_trava = MONTH('$pagamento') AND ano_trava = YEAR('$pagamento') LIMIT 1"));

    if(empty($rowTrava['id_trava'] && $rowTrava['mes_trava'] && $rowTrava['ano_trava'] && $rowTrava['id_projeto'])) {


    $query_imposto = "SELECT a.*,IF(b.sigla IS NULL, 'VALOR',b.sigla) sigla,b.id_imposto 
                        FROM contabil_contas_assoc_prestador AS a
                        LEFT JOIN contabil_impostos AS b ON a.id_imposto = b.id_imposto 
                        WHERE id_prestador = {$_REQUEST['PrestadorServico']};";
                        
    $result_imposto = mysql_query($query_imposto);
    $status = TRUE;
    
    $imp['VALOR'] = checkEmpty(strToNum($_REQUEST['ValorLiquidoNfse']));
    $imp['COFINS'] = checkEmpty(strToNum($_REQUEST['COFINS']));
    $imp['CSLL'] = checkEmpty(strToNum($_REQUEST['CSLL']));
    $imp['INSS'] = checkEmpty(strToNum($_REQUEST['INSS']));
    $imp['IRRF'] = checkEmpty(strToNum($_REQUEST['IRRF']));
    $imp['ISS'] = checkEmpty(strToNum($_REQUEST['ISS']));
    $imp['PIS'] = checkEmpty(strToNum($_REQUEST['PIS']));
    $imp['PIS / COFINS / CSLL'] = checkEmpty(strToNum($_REQUEST['COFINS'])) + checkEmpty(strToNum($_REQUEST['PIS'])) + checkEmpty(strToNum($_REQUEST['CSLL']));
        
    while ($row = mysql_fetch_assoc($result_imposto)) {
        if (!empty($imp[$row['sigla']])) {

            // preenche os campos da tabela lancamento
            $objLancamento->setIdProjeto($_REQUEST['id_projeto']);
            $objLancamento->setIdUsuario($usuario['id_funcionario']);
            $objLancamento->setContabil(1);
            $objLancamento->setHistorico($row['sigla'].' REFERENTE NFS-e '.$_REQUEST['Numero']);
            $objLancamento->setStatus(1);
            $objLancamento->setDataLancamento($_REQUEST['Competencia']);

            $objLancamento->salvar(); // salva
            $id = $objLancamento->getIdLancamento(); // substitui o idss
            $objLancamento->setDefault();
            
            $query = "INSERT INTO nfse_lancamentos_assoc (id_nfse,id_lancamento,id_imposto) VALUES ({$_REQUEST['id_nfse']},$id,{$row['id_imposto']});";
            mysql_query($query);
            
            // conta passivo
//            $objLancamentoItens->setIdLancamentoItens(checkEmpty($_POST['id_lancamento_item'][$val2][$i]));
            $objLancamentoItens->setIdLancamento($id);
            $objLancamentoItens->setIdConta(checkEmpty($row['id_conta_passivo']));
            $objLancamentoItens->setValor($imp[$row['sigla']]);
            $objLancamentoItens->setTipo(checkEmpty(1));
            $objLancamentoItens->setStatus(1);
            $objLancamentoItens->setHistorico('');
            $status = $status && $objLancamentoItens->salvar();
            $objContas->verificaSaldo2($objLancamentoItens->getIdLancamentoItens());

            $objLancamentoItens->setDefault(); // limpa campos

            // conta dre
//            $objLancamentoItens->setIdLancamentoItens(checkEmpty($_POST['id_lancamento_item'][$val2][$i]));
            $objLancamentoItens->setIdLancamento($id);
            $objLancamentoItens->setIdConta(checkEmpty($row['id_conta_dre']));
            $objLancamentoItens->setValor($imp[$row['sigla']]);
            $objLancamentoItens->setTipo(checkEmpty(2));
            $objLancamentoItens->setStatus(1);
            $objLancamentoItens->setHistorico('');
            $status = $status && $objLancamentoItens->salvar();
            $objContas->verificaSaldo2($objLancamentoItens->getIdLancamentoItens());

            $objLancamentoItens->setDefault(); // limpa campos
        }
    }
    
            }

    // ---- lancamentos contabeis (fim)-----------------------------------------
    
    
    if ($nfse->alteraStatus($_REQUEST['id_nfse'], 3)) {
        echo json_encode(array('status' => 'success', 'msg' => utf8_encode('Conferência realizada com sucesso.')));
    } else {
        echo json_encode(array('status' => 'danger', 'msg' => utf8_encode('Erro ao salvar Conferência.')));
    }
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'cancelarNFSe') {
    if ($nfse->alteraStatus($_REQUEST['id'], 0)) {
        echo json_encode(array('status' => 'success', 'msg' => utf8_encode('Cancelamento realizada com sucesso.')));
    } else {
        echo json_encode(array('status' => 'danger', 'msg' => utf8_encode('Erro ao Cancelar.')));
    }
}


if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'correcao'){
    $id = $_REQUEST['id'];
    $motivo = addslashes(utf8_decode($_REQUEST['motivo']));
    
    $nfse_correcao->setIdNfse($id);
    $nfse_correcao->setMotivo($motivo);
    $nfse_correcao->setDataCad(date('Y-m-d H:i:s'));
    $nfse_correcao->setIdRegiao($id_regiao);
    $nfse_correcao->setStatus(1);
    $nfse_correcao->insert();
        
    if($nfse->alteraStatus($id, 1)){
        echo json_encode(array('status'=>TRUE,'msg'=>utf8_encode('Enviado para Correção.')));
    }else{
        echo json_encode(array('status'=>TRUE,'msg'=>utf8_encode('Erro. Tente novamente.')));
    }
    exit();
}


function checkEmpty($var) {
    return (!empty($var)) ? $var : NULL;
}

function strToNum($string) {
    return str_replace(',', '.', str_replace('.', '', $string));
}
