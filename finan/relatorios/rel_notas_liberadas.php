<?php
// session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/ContabilLancamentoClass.php");
include("../../classes/BotoesClass.php");
include("../../classes/NFSeClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$objNFSe = new NFSe();
$global = new GlobalClass();

$id_projeto = $_REQUEST['projeto'];
$id_regiao = $usuario['id_regiao'];
$id_prestador = $_REQUEST['prestador'];

function apropriacao($id_nfse) {

    $lan = new ContabilLancamentoClass();
    $query = "SELECT a.*,b.c_razao, b.c_cnpj, b.id_prestador 
    FROM nfse a
    INNER JOIN prestadorservico AS b ON a.PrestadorServico = b.id_prestador
    WHERE id_nfse = '$id_nfse';";

    $result = mysql_query($query) or die($query . ' - ' . mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        // cria lancamento contabil caso o periodo não esteja finalizsado

        $query2 = "SELECT * FROM contabil_contas_assoc_prestador a
                WHERE id_prestador = $row[id_prestador]";
        $result2 = mysql_query($query2) or die($query2 . ' - ' . mysql_error());

        /*
         * 1; COFINS
         * 2; CSLL
         * 3; INSS
         * 4; IRRFPJ
         * 5; ISS
         * 6; PIS
         * 7; PIS/COFINS/CSLL
         */

        while ($row2 = mysql_fetch_array($result2)) {
            $retencao[$row2['id_imposto']] = $row2;
            $dre = $row2['id_conta_dre'];
        }

        $periodo = substr($data_vencimento, 0, 4) . '' . substr($data_vencimento, 5, 2);
        $rowTrava = mysql_fetch_array(mysql_query("SELECT * FROM contabil_trava WHERE id_projeto = '{$arrayDados['id_projeto']}' AND periodo = '$periodo' LIMIT 1"));


        if ($row['ValorServicos'] > 0 &&
                (
                $row['ValorPis'] > 0 ||
                $row['ValorCofins'] > 0 ||
                $row['ValorCsll'] > 0 ||
                $row['ValorIr'] > 0 ||
                $row['ValorInss'] > 0 ||
                $row['ValorIss'] > 0 ||
                $row['ValorPisCofinsCsll'] > 0 ||
                $row['ValorLiquidoNfse'] > 0
                )
        ) {

            $dados_lancamento = [
                'id_projeto' => $row['id_projeto'],
                'data_lancamento' => $row['DataEmissao'],
                'historico' => 'FORNECEDOR / RETENÇÃO À PAGAR  - ' . $row['c_razao'] . ' - ' . $row['c_cnpj'] . ' - NFSe ' . $row['Numero'],
                'contabil' => '1'
            ];


            $id_lancamento = $lan->inserirLancamento($dados_lancamento);
            
            $lan->gera_lancamento_nfse_assoc($id_nfse, $id_lancamento);

            if ($row['ValorServicos'] > 0 && $dre > 0) {
                $itens[] = [
                    'id_lancamento' => $id_lancamento,
                    'id_conta' => $dre,
                    'valor' => $row['ValorServicos'],
                    'documento' => $row['Numero'],
                    'tipo' => 2,
                    'historico' => $row['c_razao'] . ' - ' . $row['c_cnpj'] . ' - NFSe ' . $row['Numero'],
                    'fornecedor' => $row['id_prestador'],
                    'status' => 1
                ];
            }

            if ($row['ValorPis'] > 0 && $retencao[6]['id_conta_passivo'] > 0) {
                $itens[] = [
                    'id_lancamento' => $id_lancamento,
                    'id_conta' => $retencao[6]['id_conta_passivo'],
                    'valor' => $row['ValorPis'],
                    'documento' => $row['Numero'],
                    'tipo' => 1,
                    'historico' => 'RETENÇÃO PIS - ' . $row['c_razao'] . ' - ' . $row['c_cnpj'] . ' - NFSe ' . $row['Numero'],
                    'fornecedor' => $row['id_prestador'],
                    'status' => 1
                ];
            }

            if ($row['ValorCofins'] > 0 && $retencao[1]['id_conta_passivo'] > 0) {
                $itens[] = [
                    'id_lancamento' => $id_lancamento,
                    'id_conta' => $retencao[1]['id_conta_passivo'],
                    'valor' => $row['ValorCofins'],
                    'documento' => $row['Numero'],
                    'tipo' => 1,
                    'historico' => 'RETENÇÃO COFINS - ' . $row['c_razao'] . ' - ' . $row['c_cnpj'] . ' - NFSe ' . $row['Numero'],
                    'fornecedor' => $row['id_prestador'],
                    'status' => 1
                ];
            }

            if ($row['ValorCsll'] > 0 && $retencao[2]['id_conta_passivo'] > 0) {
                $itens[] = [
                    'id_lancamento' => $id_lancamento,
                    'id_conta' => $retencao[2]['id_conta_passivo'],
                    'valor' => $row['ValorCsll'],
                    'documento' => $row['Numero'],
                    'tipo' => 1,
                    'historico' => 'RETENÇÃO CSLL - ' . $row['c_razao'] . ' - ' . $row['c_cnpj'] . ' - NFSe ' . $row['Numero'],
                    'fornecedor' => $row['id_prestador'],
                    'status' => 1
                ];
            }

            if ($row['ValorIr'] > 0 && $retencao[4]['id_conta_passivo'] > 0) {
                $itens[] = [
                    'id_lancamento' => $id_lancamento,
                    'id_conta' => $retencao[4]['id_conta_passivo'],
                    'valor' => $row['ValorIr'],
                    'documento' => $row['Numero'],
                    'tipo' => 1,
                    'historico' => 'RETENÇÃO IRPJ - ' . $row['c_razao'] . ' - ' . $row['c_cnpj'] . ' - NFSe ' . $row['Numero'],
                    'fornecedor' => $row['id_prestador'],
                    'status' => 1
                ];
            }

            if ($row['ValorInss'] > 0 && $retencao[3]['id_conta_passivo'] > 0) {
                $itens[] = [
                    'id_lancamento' => $id_lancamento,
                    'id_conta' => $retencao[3]['id_conta_passivo'],
                    'valor' => $row['ValorInss'],
                    'documento' => $row['Numero'],
                    'tipo' => 1,
                    'historico' => 'RETENÇÃO INSS - ' . $row['c_razao'] . ' - ' . $row['c_cnpj'] . ' - NFSe ' . $row['Numero'],
                    'fornecedor' => $row['id_prestador'],
                    'status' => 1
                ];
            }

            if ($row['ValorIss'] > 0 && $retencao[5]['id_conta_passivo'] > 0) {
                $itens[] = [
                    'id_lancamento' => $id_lancamento,
                    'id_conta' => $retencao[5]['id_conta_passivo'],
                    'valor' => $row['ValorIss'],
                    'documento' => $row['Numero'],
                    'tipo' => 1,
                    'historico' => 'RETENÇÃO ISS - ' . $row['c_razao'] . ' - ' . $row['c_cnpj'] . ' - NFSe ' . $row['Numero'],
                    'fornecedor' => $row['id_prestador'],
                    'status' => 1
                ];
            }

            if ($row['ValorPisCofinsCsll'] > 0 && $retencao[7]['id_conta_passivo']) {
                $itens[] = [
                    'id_lancamento' => $id_lancamento,
                    'id_conta' => $retencao[7]['id_conta_passivo'],
                    'valor' => $row['ValorPisCofinsCsll'],
                    'documento' => $row['Numero'],
                    'tipo' => 1,
                    'historico' => 'RETENÇÃO PIS/COFINS/CSLL - ' . $row['c_razao'] . ' - ' . $row['c_cnpj'] . ' - NFSe ' . $row['Numero'],
                    'fornecedor' => $row['id_prestador'],
                    'status' => 1
                ];
            }

            if ($row['ValorLiquidoNfse'] > 0 && $retencao[0]['id_conta_passivo'] > 0) {
                $itens[] = [
                    'id_lancamento' => $id_lancamento,
                    'id_conta' => $retencao[0]['id_conta_passivo'],
                    'valor' => $row['ValorLiquidoNfse'],
                    'documento' => $row['Numero'],
                    'tipo' => 1,
                    'historico' => 'FORNECEDOR A PAGAR - ' . $row['c_razao'] . ' - ' . $row['c_cnpj'] . ' - NFSe ' . $row['Numero'],
                    'fornecedor' => $row['id_prestador'],
                    'status' => 1
                ];
            }

            $lan->inserirItensLancamento($itens);

            unset($itens);
        }
    }
}

if (isset($_REQUEST['filtrar'])) {
    $filtro = true;
    if ($id_projeto !== "-1") {
        $x[] = "a.id_projeto = '{$id_projeto}'";
    }
    if ($id_prestador !== "-1") {
        $x[] = "REPLACE(REPLACE(REPLACE(b.c_cnpj, '-', ''), '/', ''), '.', '') = '$id_prestador'";
    }
    $x[] = "a.status = 3";
    $x[] = "a.id_regiao = $id_regiao";

//    $dados = array('id_projeto' => $id_projeto, 'status' => 3, 'PrestadorServico' => $id_prestador);
    $dados = implode(' AND ', $x);
    $arrayNFSe = $objNFSe->exibeNFSe($dados);

    $arr_where[] = ($id_projeto > 0) ? "id_projeto = $id_projeto" : '';
    $arr_where[] = ($id_regiao > 0) ? "id_regiao = $id_regiao" : '';

//    $where = implode(' AND ', array_filter($arr_where));

    $sqlB = "SELECT * FROM bancos";
    $qryB = mysql_query($sqlB);
    while ($rowB = mysql_fetch_assoc($qryB)) {
        $arrayBancos[$rowB['id_banco']] = $rowB['id_banco'] . ' - ' . $rowB['nome'];
        //$arrIdBanco[$rowB['id_projeto']] = $rowB['id_banco']; // tava dando merda, melhor comentar
    }
//    print_array($arrayNFSe);
}

// preencher select dos prestadores
$query = "SELECT c_razao AS razao, REPLACE(REPLACE(REPLACE(c_cnpj, '-', ''), '/', ''), '.', '') AS cnpj #,encerrado_em
            FROM prestadorservico 
            -- WHERE encerrado_em > CURDATE()
            GROUP BY REPLACE(REPLACE(REPLACE(c_cnpj, '-', ''), '/', ''), '.', '')
            ORDER BY c_razao";
$result = mysql_query($query);
$prestadores['-1'] = "« TODOS »";
while ($row = mysql_fetch_array($result)) {
    $prestadores[$row['cnpj']] = mascara_string('##.###.###/####-##', $row['cnpj']) . " - {$row['razao']}";
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
if (isset($_REQUEST['gerar_saida']) && $_REQUEST['gerar_saida'] == 'gerar_saida') {

    $query_imp = "SELECT * FROM contabil_impostos WHERE status = 1";
    $result_imp = mysql_query($query_imp);
    while ($row_imp = mysql_fetch_assoc($result_imp)) {
        $imposto[$row_imp['id_imposto']] = $row_imp;
        $tipos_impostos[] = $row_imp['id_tipo'];
    }

    $periodo = substr($data_vencimento, 0, 4) . '' . substr($data_vencimento, 6, 2);
    foreach ($_REQUEST['nfse'] as $key_id => $id_nfse) {
        $arrayValores = [];
        $dados = array('id_nfse' => $id_nfse, 'status' => 3);
        $arrayDados = $objNFSe->exibeNFSe($dados);
        $arrayDados = $arrayDados[$id_nfse];
        
        $proj = projetosId($arrayDados['id_projeto']);

        // verifica se eh parcelado
        if (isset($_REQUEST['valor_p'][$key_id])) {
            // foreach das parcelas
            foreach ($_REQUEST['valor_p'][$key_id] as $id_valor => $valor) {
                $d = converteData($_REQUEST['data_p'][$key_id][$id_valor]);
                $v = addslashes(str_replace(',', '.', str_replace('.', '', $valor)));
                $p = addslashes($_REQUEST['porcentagem_p'][$key_id][$id_valor] / 100);
                $parcela = $id_valor + 1;
                $arrayValores[] = [
                    'nome' => addslashes($arrayDados['c_razao'] . ' - Projeto: ' . $proj['nome'] . ' - NF: ' . $arrayDados['Numero'] .  " - Parcela: " . $parcela . '/' . count($_REQUEST['valor_p'][$key_id])),
                    'especifica' => addslashes($arrayDados['c_razao'] . ' ' . $arrayDados['Discriminacao']),
                    'tipo' => addslashes($arrayDados['id_tipo_entradasaida']),
                    'valor' => $v,
                    'entradaesaida_subgrupo_id' => addslashes($arrayDados['id_subgrupo_entradasaida']),
                    'valor_bruto' => addslashes(number_format($arrayDados['ValorServicos'], 2, '.', '')),
                    'data_vencimento' => $d
                ];
                $query_parcela = "INSERT INTO nfse_parcelas (id_nfse,data,valor,porcentagem,parcela) VALUES ('{$id_nfse}', '{$d}', '{$v}', '{$p}', '{$parcela}')";
                mysql_query($query_parcela) or die(mysql_error());
            }
        } else {
            // parcela única

            $data_vencimento = (isset($_REQUEST['data_vencimento_individual'][$key_id]) && !empty($_REQUEST['data_vencimento_individual'][$key_id])) ?
                    $_REQUEST['data_vencimento_individual'][$key_id] :
                    $_REQUEST['data_vencimento'];
            $data_vencimento = converteData($data_vencimento,'Y-m-d');
            $arrayValores[] = [
                'nome' => addslashes($arrayDados['c_razao']. ' - Projeto: ' . $proj['nome'] . ' - NF: ' . $arrayDados['Numero']),
                'especifica' => addslashes($arrayDados['c_razao'] . ' - ' . $arrayDados['Discriminacao'] ),
                'tipo' => addslashes($arrayDados['id_tipo_entradasaida']),
                'valor' => addslashes(number_format($arrayDados['ValorLiquidoNfse'], 2, '.', '')),
                'entradaesaida_subgrupo_id' => addslashes($arrayDados['id_subgrupo_entradasaida']),
                'valor_bruto' => addslashes(number_format($arrayDados['ValorServicos'], 2, '.', '')),
                'data_vencimento' => $data_vencimento
            ];
        }

        // montando os valores de cada saída da da nota decompondo os impostos
        // data do mes subsequenste
        $data = new DateTime($arrayDados['Competencia']);
        $data->add(new DateInterval('P1M'));

        $data_ir_formatada = $data->format('Y-m-20');
        $arrayValores[] = [
            'nome' => addslashes("PIS/COFINS/CSLL - " . $arrayDados['c_razao'] . ' - Projeto: ' . $proj['nome'] . ' - NF: ' . $arrayDados['Numero']),
            'especifica' => addslashes("PIS/COFINS/CSLL " . $arrayDados['c_razao'] . ' ' . $arrayDados['Discriminacao']),
            'tipo' => $imposto[7]['id_tipo'],
            'valor' => addslashes(number_format(($arrayDados['ValorPis'] + $arrayDados['ValorCofins'] + $arrayDados['ValorCsll']), 2, '.', '')),
            'entradaesaida_subgrupo_id' => $imposto[7]['id_subgrupo'],
            'id_imposto' => $imposto[7]['id_imposto'],
            'data_vencimento' => $data_ir_formatada
        ];
        $data_inss_formatada = $data->format('Y-m-20');
        $arrayValores[] = [
            'nome' => addslashes("INSS - " . $arrayDados['c_razao'] . ' - Projeto: ' . $proj['nome'] . ' - NF: ' . $arrayDados['Numero']),
            'especifica' => addslashes("INSS: " . $arrayDados['c_razao'] . ' ' . $arrayDados['Discriminacao']),
            'tipo' => $imposto[5]['id_tipo'],
            'valor' => addslashes(number_format($arrayDados['ValorInss'], 2, '.', '')),
            'entradaesaida_subgrupo_id' => $imposto[5]['id_subgrupo'],
            'id_imposto' => $imposto[5]['id_imposto'],
            'data_vencimento' => $data_inss_formatada
        ];
        $data_ir_formatada = $data->format('Y-m-20');
        $arrayValores[] = [
            'nome' => addslashes("IR - " . $arrayDados['c_razao'] . ' - Projeto: ' . $proj['nome'] . ' - NF: ' . $arrayDados['Numero']),
            'especifica' => addslashes("IR: " . $arrayDados['c_razao'] . ' ' . $arrayDados['Discriminacao']),
            'tipo' => $imposto[4]['id_tipo'],
            'valor' => addslashes(number_format($arrayDados['ValorIr'], 2, '.', '')),
            'entradaesaida_subgrupo_id' => $imposto[4]['id_subgrupo'],
            'id_imposto' => $imposto[4]['id_imposto'],
            'data_vencimento' => $data_ir_formatada
        ];
        $data_iss_formatada = $data->format('Y-m-10');
        $arrayValores[] = [
            'nome' => addslashes("ISS - " . $arrayDados['c_razao'] . ' - Projeto: ' . $proj['nome'] . ' - NF: ' . $arrayDados['Numero']),
            'especifica' => addslashes("ISS: " . $arrayDados['c_razao'] . ' ' . $arrayDados['Discriminacao']),
            'tipo' => $imposto[6]['id_tipo'],
            'valor' => addslashes(number_format($arrayDados['ValorIss'], 2, '.', '')),
            'entradaesaida_subgrupo_id' => $imposto[6]['id_subgrupo'],
            'id_imposto' => $imposto[6]['id_imposto'],
            'data_vencimento' => $data_iss_formatada
        ];
        
        foreach ($arrayValores as $key => $valores) {

            // montando array da saida
            if ($valores['valor'] > 0.00) {
                $array = array(
                    'id_regiao' => addslashes($arrayDados['id_regiao']),
                    'id_projeto' => addslashes($arrayDados['id_projeto']),
                    'id_banco' => addslashes($_REQUEST['banco'][$key_id]),
                    'id_user' => addslashes($usuario['id_funcionario']),
                    'nome' => $valores['nome'],
                    'especifica' => $valores['especifica'],
                    'tipo' => $valores['tipo'], // assim seria o jeiro mais correto, mas o financeiro não quer assim
//                    'tipo' => addslashes($arrayDados['id_tipo_entradasaida']),
                    'valor' => $valores['valor'],
                    'valor_bruto' => $valores['valor_bruto'],
                    'data_proc' => addslashes(date('Y-m-d')),
//                    'data_vencimento' => addslashes($arrayDados['DataEmissao']),
                    'data_vencimento' => addslashes($valores['data_vencimento']),
                    'comprovante' => addslashes(2),
                    'status' => addslashes(1),
                    'id_prestador' => addslashes($arrayDados['id_prestador']),
                    'nome_prestador' => addslashes($arrayDados['c_razao']),
                    'cnpj_prestador' => addslashes($arrayDados['c_cnpj']),
                    'n_documento' => addslashes($arrayDados['Numero']),
                    'mes_competencia' => addslashes(substr($arrayDados['Competencia'], 5, 2)),
                    'ano_competencia' => addslashes(substr($arrayDados['Competencia'], 0, 4)),
                    'dt_emissao_nf' => addslashes($arrayDados['DataEmissao']),
                    'entradaesaida_subgrupo_id' => $valores['entradaesaida_subgrupo_id'], // assim seria o jeito mais correto, mas o financeiro não quer assim
//                    'entradaesaida_subgrupo_id' => addslashes($arrayDados['id_subgrupo_entradasaida']),
                    'tipo_empresa' => 1
                );

                $keys = implode(',', array_keys($array));
                $values = implode("' , '", $array);

                $insert = "INSERT INTO saida ($keys) VALUES ('$values');";

                // insert da saida
                mysql_query($insert);
                if (mysql_errno())
                    $erro[mysql_errno()] = mysql_errno();
                $id_saida = mysql_insert_id();

                // insert da nfse
                $insert_assoc = "INSERT INTO nfse_saidas (`id_nfse`, `id_saida`) VALUES ('$id_nfse', '$id_saida');";
                mysql_query($insert_assoc);
                if (mysql_errno())
                    $erro[mysql_errno()] = mysql_errno();

                // update do status na nfse
                $update = "UPDATE nfse SET status = 4 WHERE id_nfse = '$id_nfse';";
                mysql_query($update);
                if (mysql_errno())
                    $erro[mysql_errno()] = mysql_errno();
                
                // cria lancamento contabil caso o periodo não esteja finalizsado

                $periodo = substr($data_vencimento, 0, 4) . '' . substr($data_vencimento, 5, 2);
                $rowTrava = mysql_fetch_array(mysql_query("SELECT * FROM contabil_trava WHERE id_projeto = '{$arrayDados['id_projeto']}' AND periodo = '$periodo' LIMIT 1"));
                if (empty($rowTrava['periodo'] && $rowTrava['id_projeto'])) {

                    $query_lanc = "INSERT INTO contabil_lancamento (id_projeto, id_usuario, id_saida ,data_lancamento, historico, contabil)
                                   VALUES ('{$arrayDados['id_projeto']}','{$usuario['id_funcionario']}','{$id_saida}','{$data_vencimento}','{$valores['nome']}',1);";
                    $lancamento_assoc = mysql_query($query_lanc) or die(mysql_error());
                    //echo "<-- $lancamento_assoc -->";
                    if (mysql_errno())
                        $erro[mysql_errno()] = mysql_errno();
                }

                // copiando anexo ----------------------------------------------
                if (!in_array($valores['tipo'], $tipos_impostos)) {
                    $query = "SELECT 
                                    a.id_nfse,
                                    a.id_projeto,
                                    MAX(e.arquivo_pdf) AS arquivo_pdf
                             FROM nfse AS a
                             LEFT JOIN nfse_anexos AS e ON a.id_nfse = e.id_nfse
                             WHERE a.`status` > 1 AND a.id_nfse = $id_nfse
                             GROUP BY a.id_nfse
                             ORDER BY a.id_nfse;";
                    $arquivo = mysql_fetch_assoc(mysql_query($query));
                    $arr_arquivo = explode('.', $arquivo['arquivo_pdf']);
                    $extencao = end($arr_arquivo);
                    $query_file = "INSERT INTO saida_files (id_saida, tipo_saida_file) VALUES ('{$id_saida}','.{$extencao}');";
                    if (mysql_query($query_file)) {
                        $id_files = mysql_insert_id();
                        $arquivo_1 = "../../compras/notas_fiscais/nfse_anexos/{$arquivo['id_projeto']}/{$arquivo['arquivo_pdf']}";
                        $arquivo_2 = "../../comprovantes/{$id_files}.{$id_saida}.pdf";
                        $confirmacao = copy($arquivo_1, $arquivo_2);
                        if (!$confirmacao) {
                            $erro[] = "erro ao copiar arquivo (id_nfse = {$arquivo['id_nfse']} - id_saida = {$id_saida})";
//                            exit("erro ao copiar arquivo (id_nfse = {$arquivo['id_nfse']} - id_saida = {$id_saida})");
                        }
                    } else {
                        $erro[mysql_errno()] = mysql_errno();
                    }
                }
                // copiando anexo ----------------------------------------------
            }
        }
        unset($arrayValores);
    }

    $erro = implode(', ', $erro);
    if ($erro) {
        $msg = "e=$erro";
    } else {
        $msg = "s";
    }
    $query = "INSERT INTO nfse_log (id_nfse, status, id_user, data_cad) VALUES ('{$id_nfse}', '4', '{$_COOKIE['logado']}', NOW());";
    mysql_query($query) or die($query . ' - ' . mysql_error());
    header("Location: rel_notas_liberadas.php?$msg");
    exit;
}

$nome_pagina = 'NFSe Liberadas';
$breadcrumb_config = array("nivel" => "../", "key_btn" => "4", "area" => "Financeiro", "id_form" => "form1", "ativo" => $nome_pagina);
$breadcrumb_pages = array("Principal" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
<?php include("../../template/navbar_default.php"); ?>

        <!--<div class="container-full over-x">-->
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - <?= $nome_pagina ?></small></h2></div>
            <?php if (isset($_GET['s'])) { ?><div class="alert alert-dismissable alert-success text-bold"><button type="button" class="close" data-dismiss="alert">×</button>Saídas Geradas com Sucesso!</div><?php } ?>
            <?php if (isset($_GET['e'])) { ?><div class="alert alert-dismissable alert-danger text-bold"><button type="button" class="close" data-dismiss="alert">×</button>Erro: <?= $_GET['e'] ?>. Entre em contato com o suporte.</div><?php } ?>
            <form action="rel_notas_liberadas.php" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Projeto</label>
                            <div class="col-lg-4">
                                <?php echo montaSelect($global->carregaProjetosByRegiao($usuario['id_regiao'], array("-1" => "« TODOS »")), $id_projeto, "id='projeto' name='projeto' class='form-control'"); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Prestador</label>
                            <div class="col-lg-9">
                                <?php echo montaSelect($prestadores, $id_prestador, "id='prestador' name='prestador' class='form-control'"); ?>
                            </div>
                            <!--<label for="select" class="col-lg-2 control-label text-sm">Visualizar Saídas de</label>
                            <div class="col-lg-4">
                                <div class="input-group">
                                    <input type="text" class="input form-control data validate[required]" name="data_ini" id="data_ini" placeholder="Data Inicial" value="<?php echo $dataIni; ?>">
                                    <span class="input-group-addon">até</span>
                                    <input type="text" class="input form-control data validate[required]" name="data_fim" id="data_fim" placeholder="Data Final" value="<?php echo $dataFim; ?>">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>-->
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <!--<input type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary" />-->
                        <button type="submit" name="filtrar"  id="filt" value="Filtrar" class="btn btn-primary"><i class="fa fa-filter"></i> Filtrar</button>
                    </div>
                </div>

                <?php
                if ($filtro) {
                    if (count($arrayNFSe) > 0) {
                        ?> 
                        <p><span style="background-color: rgb(252, 248, 227); border: 1px solid rgb(244, 176, 79);">&emsp;</span> Falta associação de Subgrupo e Tipo de Saída</p>
                        <table class='table table-bordered table-hover table-striped table-condensed text-sm valign-middle'>
                            <thead>
                                <tr class="bg-primary">
                                    <th class="text-center">
                                        <input type="checkbox" id="checkAll">
                                    </th>
                                    <th>Projeto</th>
                                    <th style="width: 210px;">
                                        Banco
                                        <?= montaSelect($arrayBancos, 2, 'name="banco_all" title="Caso queira alterar todos os bancos, selecione aqui." id="banco_all" class="validate[required,custom[select]] form-control input-sm"') ?>
                                    </th>
                                    <th style="width: 17%">Fronecedor</th>
                                    <th>N&ordm; NFSe</th>
                                    <!--<th style="width: 17%">Discriminação</th>-->
                                    <th class="text-center">PIS / COFINS / CSLL</th>
                                    <th class="text-center">IR</th>
                                    <th class="text-center">INSS</th>
                                    <th class="text-center">ISS</th>
                                    <th class="text-center">Valor Líquido</th>
                                    <th class="text-center"><i class="fa fa-file-pdf-o"></i></th>
                                    <!--<th class="text-center"><i class="fa fa-print"></i></th>-->
                                    <th colspan="2">&emsp;</th>
                                </tr>
                            </thead>
                            <tbody>
        <?php foreach ($arrayNFSe as $id_nfse => $value) { ?>
                                    <tr class="<?= (!empty($value['id_tipo_entradasaida']) && !empty($value['id_subgrupo_entradasaida'])) ? "" : "warning" ?>">
                                        <td class="text-center">
            <?php $tem_tipo = (!empty($value['id_tipo_entradasaida']) && !empty($value['id_subgrupo_entradasaida'])) ? "1" : "0" ?>
                                            <?php $tem_tipo_class = (!empty($value['id_tipo_entradasaida']) && !empty($value['id_subgrupo_entradasaida'])) ? "tem_tipo_class" : "" ?>
                                            <input type="checkbox" name="nfse[<?= $value['id_nfse']; ?>]" value="<?= $value['id_nfse']; ?>" class="check_nfse <?= $tem_tipo_class ?>" data-validacao="<?= $tem_tipo ?>" data-cod-serv="<?= $value['CodigoTributacaoMunicipio'] ?>" data-prestador="<?= $value['id_prestador'] ?>">
                                        </td>
                                        <td><?= $value['nome_projeto']; ?></td>
                                        <td><?= montaSelect($arrayBancos, 2, 'name="banco[' . $value['id_nfse'] . ']" class="validate[required,custom[select]] banco form-control input-sm"') ?></td>
                                        <td><?= $value['c_razao']; ?></td>
                                        <td><?= $value['Numero']; ?></td>
                                        <!--<td><?= $value['Discriminacao']; ?></td>-->
                                        <td class="text-right"><?= number_format(($value['ValorPis'] + $value['ValorCofins'] + $value['ValorCsll']), 2, ',', '.'); ?></td>
                                        <td class="text-right"><?= number_format($value['ValorIr'], 2, ',', '.'); ?></td>
                                        <td class="text-right"><?= number_format($value['ValorInss'], 2, ',', '.'); ?></td>
                                        <td class="text-right"><?= number_format($value['ValorIss'], 2, ',', '.'); ?></td>
                                        <td class="text-right"><?= number_format($value['ValorLiquidoNfse'], 2, ',', '.'); ?></td>
                                        <td class="text-right"><a class="btn btn-xs btn-default" href="../../compras/notas_fiscais/nfse_anexos/<?= $value['id_projeto'] ?>/<?= $value['arquivo_pdf'] ?>" target="_blank"><i class="fa fa-file-pdf-o text-danger"></i></a></td>
                                        <!--<td class="text-right">-->
                                        <!--link so funciona se for nfse do municipio do rj-->
                                        <!--<a class="btn btn-xs btn-success" href="https://notacarioca.rio.gov.br/contribuinte/notaprint.aspx?nf=<?= $value['Numero'] ?>&cod=<?= str_replace(array('-'), '', $value['CodigoVerificacao']) ?>&inscricao=<?= $value['inscricao_municipal'] ?>" target="_blank"><i class="fa fa-print"></i></a>-->
                                        <!--</td>-->
                                        <td>
                                            <input type="text" name="data_vencimento_individual[<?= $value['id_nfse']; ?>]" class="form-control input-sm data data_vencimento_individual validate[required]" disabled placeholder="Data Vencimento">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-info btn_parcelar btn-sm" disabled data-valor-liquido="<?= $value['ValorLiquidoNfse'] ?>" data-id="<?= $value['id_nfse']; ?>">Parcelar</button> 
                                            <div id="parcelas_<?= $value['id_nfse']; ?>"> </div>
                                        </td>
                                    </tr>
        <?php } ?>
                            </tbody>
                        </table>

                        <div class="panel panel-default">
                            <div class="panel-footer text-right">
                                <div class=" col-xs-2 col-xs-offset-6">
                                    <label class="control-label">Data de Vencimento</label>
                                </div>
                                <div class=" col-xs-2">
                                    <input class="form-control data validate[required]" name="data_vencimento" id="data_vencimento" disabled>
                                </div>
                                <!--<div class=" col-xs-2">
                                <!--<label class="control-label">Banco</label>
                            </div>
                            <div class=" col-xs-4">
                                <!--<?= montaSelect($arrayBancos, null, 'name="banco" class="validate[required,custom[select]] form-control"') ?>
                            </div>-->
                                <div class="col-xs-2">
                                    <button type="submit" class="btn btn-pa-purple btn-block" name="gerar_saida" value="gerar_saida"><i class="fa fa-gears"></i> Gerar Saída</button>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>

                        <div class="clear"></div>

    <?php } else { ?>
                        <div class="alert alert-danger top30">
                            Nenhum registro encontrado
                        </div>
        <?php
    }
}
?>
            </form>
                <?php include('../../template/footer.php'); ?>
        </div>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../js/jquery.form.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="rel_notas_liberadas.js" type="text/javascript"></script>

    </body>
</html>
