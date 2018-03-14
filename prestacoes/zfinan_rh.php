<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');
include('PrestacaoContas.class.php');

$usuario = carregaUsuario();

$regiao = $usuario['id_regiao'];
$master = $usuario['id_master'];

$result = null;
$btexportar = true;
$btfinalizar = true;
$historico = false;
$dataMesIni = date("Y-m") . "-31";
$erros = 0;
$idsErros = array();

//----- CARREGA OS BANCOS VIA AJAX, RETORNA UM JSON 
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "loadbancos") {
    $return['status'] = 1;
    $qr_proj = mysql_query("SELECT administracao FROM projeto WHERE id_projeto = '{$_REQUEST['projeto']}' AND status_reg=1");
    $row_proj = mysql_fetch_assoc($qr_proj);

    $qr_bancos = mysql_query("SELECT * FROM bancos WHERE id_projeto = '{$_REQUEST['projeto']}' AND administracao = {$row_proj['administracao']} AND status_reg=1");
    $num_rows = mysql_num_rows($qr_bancos);
    $bancos = array();
    if ($num_rows > 0) {
        while ($row = mysql_fetch_assoc($qr_bancos)) {
            $bancos[$row['id_banco']] = $row['id_banco'] . " - " . utf8_encode($row['nome']);
        }
    } else {
        $bancos["-1"] = "Banco não encontrado";
    }
    $return['options'] = $bancos;
    echo json_encode($return);
    exit;
}

//UPDATE CURSO HORAS SEMANA
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "cad_carga_horaria") {

    $return = array("status" => 0);
    $qr = "UPDATE curso SET hora_semana = '{$_REQUEST['valor']}' WHERE id_curso = '{$_REQUEST['curso']}'";
    if (mysql_query($qr)) {
        $return = array("status" => 1, "curso" => $_REQUEST['curso'], "hora" => $_REQUEST['valor']);
    }
    echo json_encode($return);
    exit;
}

//UPDATE CURSO SALARIO
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "cad_salario_rpa") {

    $return = array("status" => 0);
    $qr = "UPDATE curso SET salario = '{$_REQUEST['valor']}' WHERE id_curso = '{$_REQUEST['curso']}'";
    if (mysql_query($qr)) {
        $return = array("status" => 1, "curso" => $_REQUEST['curso'], "salario" => $_REQUEST['valor']);
    }
    echo json_encode($return);
    exit;
}


// CASO TENHA PROJETO (EM TODOS OS CASOS DPS DO POST)
if (isset($_REQUEST['projeto'])) {

    $percent = $_REQUEST['percent'];
    $retifica = $_REQUEST['reti'];
    $id_projeto = $_REQUEST['projeto'];
    $id_banco = $_REQUEST['banco'];
    $terceiro = null;
    $mes2d = sprintf("%02d", $_REQUEST['mes']); //mes com 2 digitos
    $mesShow = mesesArray($_REQUEST['mes']) . "/" . $_REQUEST['ano'];
    $dataMesRef = "{$_REQUEST['ano']}-{$mes2d}-01";
    $anoMesReferencia = $_REQUEST['ano'] . "-" . $mes2d;
    $historico = false;

    if ($_REQUEST['mes'] == '12' && $_REQUEST['terceiro'] == 1) {
        $terceiro = 1;
    } else {
        $terceiro = 2;
    }

    /* FOLHAS DOS PROJETOS */
    if ((isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) || (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar']))) {

        /* RECUPERANDO OS PROJETOS JA FINALIZADOS */
        //VERIFICA SE OUTRO PROJETO PRECISA PRESTAR CONTAS NO MES SELECIONADO
        $dataMesIni = "{$_REQUEST['ano']}-{$mes2d}-31";
        $qr_verifica = PrestacaoContas::getQueryVerifica("rh", $dataMesRef, $dataMesIni);
        
        $rs_verifica = mysql_query($qr_verifica);
        $total_verifica = mysql_num_rows($rs_verifica);
        $projetosFaltante = array();
        $contErro = 0;
        $finalizados = array();

        while ($rowVeri = mysql_fetch_assoc($rs_verifica)) {
            //VERIFICA SE OS OUTROS NÃO ESTÃO FINALIZADOS
            if ($rowVeri['gerado_embr'] == null && $rowVeri['id_banco'] != $id_banco) {
                $btexportar = false;
                $projetosFaltante[$contErro]['nome'] = $rowVeri['projeto'];
                $projetosFaltante[$contErro]['banco'] = " Banco: " . $rowVeri['id_banco'] . " AG: " . $rowVeri['agencia'] . " CC: " . $rowVeri['conta'];
                $contErro++;
            } elseif ($rowVeri['gerado_embr'] != null && $rowVeri['id_projeto'] == $id_projeto && $rowVeri['id_banco'] == $id_banco) {  //VERIFICA SE O ATUAL ESTÁ FINALIZADO
                $btfinalizar = false;
            }

            //VERIFICA SE SÓ TEM 1 E SE JA FOI FINALIZADO
            if ($total_verifica == 1 && $rowVeri['id_projeto'] == $id_projeto && $rowVeri['gerado_embr'] != null) {
                $btfinalizar = false;
            }

            //PRESTAÇÕES FINALIZADAS PARA A EXPORTAÇÃO
            if ($rowVeri['gerado_embr'] != null && $rowVeri['administracao'] == "0") {
                $finalizados[] = $rowVeri['id_prestacao'];
            }

            //CASO A PESQUISADA ESTIVER FINALIZADA, PEGA DO HISTÓRICO
            if ($rowVeri['id_projeto'] == $id_projeto && $rowVeri['gerado_embr'] != null && $rowVeri['id_banco'] == $id_banco) {
                $historico = $rowVeri['id_prestacao'];
               
            }
        
        }
        if ($btfinalizar)
            $btexportar = false;

        $proj_faltantes = count($projetosFaltante);
    }

    $qrFolhas = "SELECT A.id_folha,A.projeto,B.nome
                    FROM rh_folha AS A
                    LEFT JOIN projeto AS B ON(A.projeto=B.id_projeto)
                    WHERE A.status = 3 AND A.mes = {$_REQUEST['mes']} AND A.ano = {$_REQUEST['ano']} AND A.terceiro = {$terceiro} AND id_projeto = '{$_REQUEST['projeto']}'";

    $reFolhas = mysql_query($qrFolhas);
    $arFolhas = array();
    while ($row = mysql_fetch_assoc($reFolhas)) {
        $arFolhas[] = $row['id_folha'];
    }

    if (mysql_num_rows($reFolhas) == 0) {
        $erros = 1;
        $msgErro = "Não foi encontrado a folha de pagamento referente aos dados informados";
    }

    $dtreferencia = $_REQUEST['ano'] . "-" . $mes2d . "-01";
//    
    $qr_rpa = "SELECT *, CAST(salario_profissional * qnt AS decimal(13,2)) AS salario_categoria
            	FROM(
              	SELECT 
                        A.*,COUNT(B.id_autonomo) AS qnt, B.nome, B.cpf, TRIM(C.nome) AS nome_projeto, C.id_projeto, B.agencia,B.conta,E.id_saida, G.id_saida_file, G.tipo_saida_file, CS.cbo_codigo, CS.hora_semana, A.valor AS salario_profissional, CAST(A.valor_ir AS DECIMAL(13,2)) + CAST(valor_inss AS DECIMAL(13,2)) AS encargo, IF(B.banco != '9999', (
                        SELECT razao
                        FROM bancos
                        WHERE id_banco = B.banco), B.nome_banco) AS banco
                        FROM rpa_autonomo AS A
                        INNER JOIN autonomo AS B ON (A.id_autonomo = B.id_autonomo)
                        INNER JOIN projeto AS C ON (C.id_projeto = B.id_projeto)
                        INNER JOIN curso AS CS ON (B.id_curso = CS.id_curso)
                        INNER JOIN regioes AS D ON (D.id_regiao = B.id_regiao)
                        INNER JOIN rpa_saida_assoc AS E ON (E.id_rpa = A.id_rpa)
                        INNER JOIN saida AS F ON (F.id_saida = E.id_saida)
                        INNER JOIN saida_files AS G ON (F.id_saida = G.id_saida)
                        WHERE  MONTH(data_geracao) = '{$mes2d}' AND YEAR(data_geracao) = '{$_REQUEST['ano']}' AND F.status IN(2) AND B.id_projeto = '{$id_projeto}' AND E.tipo_vinculo = '1'
                        GROUP BY A.valor
                        ORDER BY nome
	) AS tmp";	

//    SELECT *, CAST(valor * qnt AS decimal(13,2)) AS salario_categoria
//	FROM( 
//			SELECT D.id_curso,A.valor, D.nome AS categoria, A.id_autonomo, C.nome AS nome_projeto, B.nome AS nome_autonomo, D.cbo_codigo,  B.tipo_contratacao, D.hora_semana, A.valor AS salario_profissional,
//			COUNT(B.id_autonomo) AS qnt, A.valor_ir, A.valor_inss, CAST(valor_ir AS decimal(13,2)) + CAST(valor_inss AS decimal(13,2)) AS encargo
//			FROM rpa_autonomo AS A 
//			LEFT JOIN autonomo AS B ON(A.id_autonomo = B.id_autonomo)
//			LEFT JOIN projeto AS C ON(B.id_projeto = C.id_projeto)
//			LEFT JOIN curso AS D ON(B.id_curso = D.id_curso)
//			LEFT JOIN rh_horarios AS E ON(D.id_curso = E.funcao)
//			WHERE B.id_regiao = '{$regiao}' AND B.id_projeto = '{$id_projeto}' AND MONTH(A.data_geracao) = '$mes2d' AND YEAR(A.data_geracao) = '{$_REQUEST['ano']}'
//			GROUP BY D.id_curso, A.valor
//	) AS tmp                    


    /*     * RELATÓRIO COM RPA* */
    $result_rpa = mysql_query($qr_rpa);
    $qr_totais_rpa = mysql_query("SELECT SUM(salario_profissional) as total_rpa, SUM(salario_categoria) as total_rpa2 FROM (" . $qr_rpa . ") AS b");
    $rows_rpa = mysql_fetch_assoc($qr_totais_rpa);

    $total_rpa = $rows_rpa['total_rpa'];
    $total_rpa2 = $rows_rpa['total_rpa2'];
     
    if ($historico === false) {

        $qr = "SELECT *,
                CAST(salprofissional * qtde as decimal(13,2)) AS totalCat,
                salprofissional AS totalCaterr
                FROM
                    (SELECT *,
                        CAST(((rendimentos / qtde + salario)) as decimal(13,2)) + CAST(((salario+(rendimentos / qtde)) * $percent) as decimal(13,2)) as salprofissional,
                        CAST(((salario+(rendimentos / qtde)) * $percent) as decimal(13,2)) AS percent
                              FROM 
                                    (SELECT temp.*,
                                    C.id_curso AS tcur,
                                    IF((temp.saldorescisao IS NOT NULL && temp.saldorescisao!=0) || (temp.status_clt = '64') ,CONCAT(C.nome,' - RESCISAO'),C.nome) AS cargo,
                                    IF(E.horas_semanais IS NULL,'-',E.horas_semanais) AS horas_mes,
                                    D.cod,
                                    COUNT(id_clt) AS qtde, 
                                    SUM(rend) AS rendimentos
                                        FROM 
                                            (SELECT 
                                             A.id_clt,A.id_folha_proc,A.id_folha,A.id_projeto,B.id_curso,B.rh_horario,A.status_clt,
                                             F.cod_sesrj,F.cod_contrato,
                                             'CLT' AS tipocontrato,
                                             A.sallimpo AS salario,
                                             G.saldo_salario AS saldorescisao,
                                             (A.a7001+A.a8003) as rend,
                                             (SELECT id_curso_de FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '{$dtreferencia}' ORDER BY id_transferencia ASC LIMIT 1) AS de,
                                             (SELECT id_curso_para FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '{$dtreferencia}' ORDER BY id_transferencia DESC LIMIT 1) AS para,
                                             (SELECT id_horario_de FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '{$dtreferencia}' ORDER BY id_transferencia ASC LIMIT 1) AS horario_de,
                                             (SELECT id_horario_para FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '{$dtreferencia}' ORDER BY id_transferencia DESC LIMIT 1) AS horario_para  
                                            FROM rh_folha_proc AS A
                                            LEFT JOIN rh_clt AS B ON (B.id_clt=A.id_clt)
                                            LEFT JOIN projeto AS F ON (A.id_projeto=F.id_projeto)
                                            LEFT JOIN rh_recisao AS G ON (G.id_clt=A.id_clt AND G.`status` = 1 AND MONTH(G.data_demi) = {$mes2d} AND YEAR(G.data_demi) = {$_REQUEST['ano']})
                                            WHERE A.id_folha IN (" . implode(",", $arFolhas) . ") AND A.status = 3 GROUP BY id_folha_proc) as temp

                                    LEFT JOIN curso AS C ON (IF(temp.para IS NOT NULL,C.id_curso=temp.para, IF(temp.de IS NOT NULL,C.id_curso=temp.de,C.id_curso=temp.id_curso)))
                                    LEFT JOIN rh_horarios AS E ON (IF(temp.horario_para IS NOT NULL,E.id_horario=temp.horario_para, IF(temp.de IS NOT NULL,E.id_horario=temp.horario_de,E.id_horario=temp.rh_horario)))
                                    LEFT JOIN rh_cbo AS D ON (D.id_cbo=C.cbo_codigo)

                                    GROUP BY id_folha,cargo,rend
                                    ORDER BY id_folha,cargo,rend

                            ) as tb) as tm";
                                            
    } else {

        //RENOMEANDO OS CMAPOS, PARA APARECEREM NO RELATÓRIO SEM MODIFICAR O HTML
        $qr = "SELECT 
                        categoria as cargo,
                        cbo as cod,
                        qtde,
                        contratacao as tipocontrato,
                        carga_horaria as horas_mes,
                        salario_base as salario,
                        encargos as percent,
                        beneficios as rend,
                        salario_profissional as salprofissional,
                        salario_total as totalCat
                FROM prestacoes_contas_rh WHERE id_prestacao = {$historico}";
                
    }


    //QUERY EXPORTAÇÃO PEGANDO TODOS DO HISTÓRICO
    if (isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) {
        $qr = "SELECT   cod_os,cod_unidade,cod_contrato,
                        categoria as cargo,
                        cbo as cod,
                        qtde,
                        contratacao as tipocontrato,
                        carga_horaria as horas_mes,
                        salario_base as salario,
                        encargos as percent,
                        beneficios as rend,
                        salario_profissional as salprofissional,
                        salario_total as totalCat
                FROM prestacoes_contas_rh WHERE id_prestacao IN (" . implode(",", $finalizados) . ")";
    }

    $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = {$_REQUEST['projeto']}");
    $projeto = mysql_fetch_assoc($qr_projeto);

    $qrMaster = "SELECT nome,cod_os FROM master WHERE id_master = {$master}";
    $reMaster = mysql_query($qrMaster);
    $roMaster = mysql_fetch_assoc($reMaster);

    $mesShow = mesesArray($_REQUEST['mes']) . "/" . $_REQUEST['ano'];
    if ($terceiro == 1)
        $mesShow .= " 13º salário";
}

/* FINALIZA A PRESTAÇÃO, GUARDA TODOS OS VALORES EM UMA TABELA PARA CONSULTAS POSTERIOES */
if (isset($_REQUEST['finalizar']) && !empty($_REQUEST['finalizar'])) {
    /*     * RELATÓRIO COM CLT* */
    $result = mysql_query($qr);
    $linhas = mysql_num_rows($result);
    $qr_to = "SELECT SUM(salprofissional) as total, SUM(totalCat) as total2 FROM (" . $qr . ") AS b";
    $result_to = mysql_query($qr_to);
    $row_total = mysql_fetch_assoc($result_to);
    $total = $row_total['total'];
    $total2 = $row_total['total2'];


    //PREPARANDO VARIAVIES PARA GUARDAR NA TABELA 'prestacoes_contas'
    $id_folha = (count($arFolhas) == 1) ? current($arFolhas) : "0";
    $referencia = "{$_REQUEST['ano']}-{$mes2d}-01";

    $campos = "id_projeto, id_regiao, id_banco, id_folha, terceiro , tipo, data_referencia, gerado_em, gerado_por, linhas, valor_total, valor_total_profissional ,status";
    $valores = array(
        $_REQUEST['projeto'],
        $regiao,
        $id_banco,
        $id_folha,
        $terceiro,
        "rh",
        $referencia,
        date("Y-m-d H:i:s"),
        $usuario['id_funcionario'],
        $linhas,
        $total,
        $total2,
        "1");

    $id = sqlInsert("prestacoes_contas", $campos, $valores);

    $matriz = array();
    $count = 0;

    while ($row = mysql_fetch_assoc($result)) {
        $matriz[$count][] = $id;
        $matriz[$count][] = $roMaster['cod_os'];
        $matriz[$count][] = $row['cod_sesrj'];
        $matriz[$count][] = $row['cod_contrato'];
        $matriz[$count][] = $referencia;
        $matriz[$count][] = $row['id_projeto'];
        $matriz[$count][] = $row['cargo'];
        $matriz[$count][] = $row['cod'];
        $matriz[$count][] = $row['qtde'];
        $matriz[$count][] = $row['tipocontrato'];
        $matriz[$count][] = (empty($row['horas_mes'])) ? "null" : $row['horas_mes'];
        $matriz[$count][] = (empty($row['salario'])) ? "0" : $row['salario'];
        $matriz[$count][] = (empty($row['percent'])) ? "0" : $row['percent'];
        $matriz[$count][] = (empty($row['rend'])) ? "0" : $row['rend'];
        $matriz[$count][] = (empty($row['salprofissional'])) ? "0" : $row['salprofissional'];
        $matriz[$count][] = (empty($row['totalCat'])) ? "0" : $row['totalCat'];
        $count++;
    }

    $campos = array(
        "id_prestacao",
        "cod_os",
        "cod_unidade",
        "cod_contrato",
        "ano_mes_ref",
        "id_projeto",
        "categoria",
        "cbo",
        "qtde",
        "contratacao",
        "carga_horaria",
        "salario_base",
        "encargos",
        "beneficios",
        "salario_profissional",
        "salario_total"
    );

    sqlInsert("prestacoes_contas_rh", $campos, $matriz);
    header('Location: finan_rh.php');
    exit;
}

/* MONTA O ARQUIVO PARA BAIXAR */
if (isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) {
    error_reporting(E_ERROR);

    $tipoExport = $_REQUEST['tpexport'];

    //PRIMEIRA PARTE DO RELATORIO
    $result = mysql_query($qr);
    $linhasArquivo = mysql_num_rows($result);

    $linhas = $linhasArquivo + 5;

    $anoMesReferencia = $_REQUEST['ano'] . "-" . $mes2d;

    $folder = dirname(__FILE__) . "/arquivos/";
    $fname = "OS_{$roMaster['cod_os']}_RERH_" . date("Ymd") . "_{$mes2d}{$_REQUEST['ano']}.CSV";
    $filename = $folder . $fname;

    if ($tipoExport == 1)
        $retificacao = ($retifica == 1) ? "R" : "N";
    else
        $retificacao = "N";

    $handle = fopen($filename, "w+");
    /* ESCREVENDO NO ARQUIVO */
    /* HEADER */
    fwrite($handle, "H;COD_OS;DATA_GERACAO;LINHAS;TIPO;ANO_MES_REF;TIPO_ARQUIVO;VER_DOC;SECRETARIA\r\n");
    fwrite($handle, "H;{$roMaster['cod_os']};" . date("Y-m-d") . ";{$linhas};{$retificacao};{$anoMesReferencia};RERH;3.1;01.01.01.01\r\n");

    /* DETAIL */
    //NOVO
    /*
      fwrite($handle, "D;COD_OS;COD_UNIDADE;COD_CONTRATO;ANO_MES_REF;COD_CNES;NOME;CPF;COD_CONSELHO;UF_CONSELHO;COD_CBO;DESC_CARGO_CBO;");
      fwrite($handle, "COD_CATEGORIA;COD_ESPECIALIDADE;FORMA_CONTRATACAO;CARGA_HORARIA;DATA_ADMISSAO;DATA_DEMISSÃO;DATA_LICENCA;REMUNERACAO_BRUTA;");
      fwrite($handle, "REMUNERAÇÃO_BASICA;INSALUBRIDADE;GRAT_EVENTUAL;GRAT_MENSAL;ADIC_NOTURNO;LICENC_MATERN;VALOR_INSS;VALOR_FGTS;VALOR_PIS;ENCARGOS_PATRON;");
      fwrite($handle, "SALARIO_TOTAL;PROV_13_SAL;PROV_FERIAS;PROV_FERIAS_ABONO;PROV_AV_PREV_INDE;PROV_AVISO_PREVIO;PROV_MULTA_FGTS;PROV_ENC_PATRONAL;");
      fwrite($handle, "RESERVA_DEST_PROVI;BAIXA_PROV_FERIAS;BAIXA_PROV_13_SAL;BAIXA_PROV_RESCISAO\r\n"); */

    fwrite($handle, "D;COD_OS;COD_UNIDADE;COD_CONTRATO;ANO_MES_REF;CATEGORIA;QUANTIDADE;FORMA_CONTRATACAO;CARGA_HORARIA;SALARIO_BASE;");
    fwrite($handle, "BENEFICIOS;OUTROS_ENCARGOS;SALARIO_TOTAL\r\n");

    //ESCREVENDO AS LINHAS NO ARQUIVO (PARTE 1 - FOLHA DA UNIDADE)
    while ($row = mysql_fetch_assoc($result)) {
        $salario = str_replace(".", ",", $row['salario']);
        $rendimentos = str_replace(".", ",", $row['rend']);
        $descontos = str_replace(".", ",", $row['percent']);
        $totalCat = str_replace(".", ",", $row['salario'] + $row['rend'] + $row['percent']);

        fwrite($handle, "D;{$row['cod_os']};{$row['cod_unidade']};{$row['cod_contrato']};{$anoMesReferencia};{$row['cargo']};{$row['qtde']};");
        fwrite($handle, "{$row['tipocontrato']};{$row['horas_mes']};{$salario};{$rendimentos};");
        fwrite($handle, "{$descontos};{$totalCat}\r\n");
    }
 
    unset($row);
    $qr_to = "SELECT SUM(salario) as base, SUM(rend) AS rendimentos,  SUM(percent) AS percent, SUM(salprofissional) as saltotal
                     FROM (" . $qr . ") AS b";


    $result_to = mysql_query($qr_to);
    $row_total = mysql_fetch_assoc($result_to);

    $baseTo = $row_total['base'];
    $rendimentoTo = $row_total['rendimentos'];
    $encargosTo = $row_total['percent'];
    $saltotalTo = $row_total['base'] + $row_total['rendimentos'] + $row_total['percent'];

    $baseTo = str_replace(".", ",", $baseTo);
    $rendimentoTo = str_replace(".", ",", $rendimentoTo);
    $encargosTo = str_replace(".", ",", $encargosTo);
    $saltotalToF = str_replace(".", ",", $saltotalTo);

    /* RODAPE */
    //NOVO
    //fwrite($handle, "T;QUANTIDADE_REGISTROS;TOTAL_SALARIO;TOTAL_INSALUBRIDADE;TOTAL_PROV_13_SAL;TOTAL_PROV_ENC_PATRONAL\r\n");

    fwrite($handle, "T;QUANTIDADE_REGISTROS;TOTAL_SALARIO;TOTAL_BENEFICIOS;TOTAL_ENCARGOS;TOTAL_GERAL_SALARIO\r\n");
    fwrite($handle, "T;{$linhasArquivo};{$baseTo};{$rendimentoTo};{$encargosTo};{$saltotalToF}");

    /* ------------- */
    fclose($handle);

    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Content-type: application/x-msdownload");
    header("Content-Length: " . filesize($filename));
    header("Content-Disposition: attachment; filename={$fname}");
    flush();

    readfile($filename);
    exit;
}

/* FILTRO PARA MOSTRAR O RELATÓRIO */
/* RECEBE AS INFORMÇÕES PRA MONTAR O SELECT */
if (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) {

    $result = mysql_query($qr);
    $qr_to = "SELECT 
                    SUM(salprofissional) as total, 
                    SUM(totalCat) as total2,
                    SUM(rend) as torendi,
                    SUM(percent) as topercent,
                    SUM(salario) as tosal
                FROM (" . $qr . ") AS b";

    $result_to = mysql_query($qr_to);
    $row_total = mysql_fetch_assoc($result_to);
    $total = $row_total['total'];
    $total2 = $row_total['total2'];
    $torendi = $row_total['torendi'];
    $topercent = $row_total['topercent'];
    $tosal = $row_total['tosal'];
    $id_folha = (count($arFolhas) == 1) ? current($arFolhas) : "0";

    /*
      $where = array("id_regiao" => $regiao, "id_folha" => $id_folha, "MONTH(data_referencia)" => $_REQUEST['mes'], "YEAR(data_referencia)" => $_REQUEST['ano']);
      $resultHistory = montaQuery("prestacoes_contas", "COUNT(*) AS total", $where);
      $resultHistory = current($resultHistory);
      if ($resultHistory['total'] == 1)
      $btfinalizar = false; */

    echo "<!-- " . $qr . " -->";
    echo "<!-- " . $qr_verifica . " -->";
    echo "<!-- " . $qr_to . " -->";
}

$meses = mesesArray(null);
$anos = anosArray(null, null, array("-1" => "« Selecione o ano »"));

/* SELECIONA TODOS OS PROJETOS EXCETO ADMINISTRAÇÃO */
$rsProj = montaQuery("projeto", "*", "id_regiao = {$regiao}", "nome");
$projetos = array("-1" => "« Selecione »");
foreach ($rsProj as $pro) {
    $projetos[$pro['id_projeto']] = $pro['id_projeto'] . " - " . $pro['nome'];
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$bancoR = (isset($_REQUEST['banco'])) ? $_REQUEST['banco'] : null;
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m') - 1;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$rterceiro = (isset($_REQUEST['terceiro'])) ? $_REQUEST['terceiro'] : 0;
$rcterceiro = ($mesR == 12) ? "" : "class=\"hidden\"";

$retiR = (isset($_REQUEST['reti'])) ? $_REQUEST['reti'] : "0";
$percentR = (isset($_REQUEST['percent'])) ? $_REQUEST['percent'] : "0.7651";
?>
<html>
    <head>
        <title>:: Intranet :: FINANCEIRO - RECURSOS HUMANOS</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>

        <script src="../js/global.js" type="text/javascript"></script>

        <style>
            @media print
            {
                fieldset{display: none;}
                .h2page{display: none;}
                .grAdm{display: none;}
                #message-box{display: none;}
                input{display: none;}
            }
            @media screen
            {
                /*#headerPrint{display: none;}*/
            }
        </style>

        <script>
            $(function(){
                $("#form1").validationEngine(); 
                
                $("#projeto").change(function() {
                    var $this = $(this);
                    if ($this.val() != "-1") {
                        showLoading($this, "../");
                        $.post('finan_rh.php', {projeto: $this.val(), method: "loadbancos"}, function(data) {
                            removeLoading();
                            if (data.status == 1) {
                                var opcao = "";
                                var selected = "";
                                for (var i in data.options) {
                                    selected = "";
                                    if (i == $("#bancSel").val()) {
                                        selected = "selected=\"selected\" ";
                                    }
                                    opcao += "<option value='" + i + "' " + selected + ">" + data.options[i] + "</option>";
                                }
                                $("#banco").html(opcao);
                            }
                        }, "json");
                    }
                }).trigger("change");
                
                $(".j_cadastrar").click(function(){
                    var valor = $(".j_horario").val();
                    var curso = $(".j_curso").val();
                    $.ajax({
                        type:"POST",
                        dataType:"json",
                        data:{
                            valor:valor,
                            curso:curso,
                            method:"cad_carga_horaria"
                        },
                        success: function(data){
                            if(data.status){
                                $(".ocultar_campo[data-key=" + data.curso + "]").html(data.hora);
                            }
                        }
                    });
                });
                
                $(".j_cadastrar_salario").click(function(){
                    var valor = $(".j_salario_rpa").val();
                    var curso = $(".j_curso_rpa").val();
                    $.ajax({
                        type:"POST",
                        dataType:"json",
                        data:{
                            valor:valor,
                            curso:curso,
                            method:"cad_salario_rpa"
                        },
                        success: function(data){
                            if(data.status){
                                $(".ocultar_campo_rpa[data-key=" + data.curso + "]").html(data.salario);
                            }
                        }
                    });
                });
                    
            });
        </script>
    </head>
    <body id="page-fin-rh" class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1">
                <div id="headerPrint">
                    <div id="head">
                        <img src="../imagens/logomaster<?php echo $master; ?>.gif" class="fleft" style="margin-right: 25px;" />
                        <div class="fleft">
                            <h2><?php echo $roMaster['razao'] ?></h2>
                            <h2>RECURSOS HUMANOS</h2>
                        </div>
                    </div>
                    <br class="clear"/>
                </div>

                <input type="hidden" name="folhaR" id="folhaR" value="<?php echo $folhaR ?>" />
                <input type="hidden" name="proadmR" id="proadmR" value="<?php echo $proadmR ?>" />
                <input type="hidden" name="bancSel" id="bancSel" value="<?php echo $bancoR ?>" />

                <fieldset>
                    <legend>Dados</legend>

                    <p><label class="first">Projeto:</label> <?php echo montaSelect(PrestacaoContas::carregaProjetos($master), $projetoR, "id='projeto' name='projeto' class='validate[custom[select]]'") ?></p> 
                    <p><label class="first">Banco:</label> <?php echo montaSelect(array("-1" => "« Selecione »"), null, "id='banco' name='banco'") ?></p>
                    <p><label class="first">Mês:</label> <?php echo montaSelect($meses, $mesR, "id='mes' name='mes' class='validate[custom[select]]'") ?>  <?php echo montaSelect($anos, $anoR, "id='ano' name='ano' class='validate[custom[select]]'") ?> (mês da folha de pagamento)</p>
                    <p id="pterceiro" <?php echo $rcterceiro ?>><label class="first">Décimo Terceiro?</label> <label><input type="radio" name="terceiro" id="terceiroS" value="1" <?php echo ($rterceiro == 1) ? "checked='checked'" : ""; ?>/> Sim </label> <label><input type="radio" name="terceiro" id="terceiroN" value="0" <?php echo ($rterceiro == 0) ? "checked='checked'" : ""; ?>/> Não </label> </p>
                    <p class="hidden"><label class="first">Retificadora:</label> <input type="hidden" name="reti" id="reti" value="<?php echo $retiR ?>" /> Sim </p>
                    <p><label class="first">Percentual de Encargos:</label> <input type="hidden" name="percent" id="percent" value="<? echo $percentR ?>" size="4" /> <? echo $percentR ?> </p>

                    <p class="controls"> 
                        <input type="submit" class="button" value="Filtrar" name="filtrar" /> 
                    </p>
                </fieldset>

                <?php if (!empty($result) && mysql_num_rows($result) > 0) { ?>
                    <br/><br/>
                    <p style="text-align: right;"><input type="button" onclick="tableToExcel('rhTableGeral', 'Folha')" value="Exportar para Excel" class="exportarExcel"></p>

                    <table id="rhTableGeral"  border="0" width="100%">
                        <tr><td>
                                <table id="rhTable" border="0" cellpadding="0" cellspacing="0" width="100%" class="grid">
                                    <thead>
                                        <tr>
                                            <th colspan="9">UNIDADE GERENCIADA: <?php echo $projeto['nome'] ?></th>
                                            <th><?php echo $mesShow ?></th>
                                        </tr>
                                        <tr>
                                            <th colspan="10">RH CONTRATADO</th>
                                        </tr>
                                        <tr>
                                            <th rowspan="2">CATEGORIA PROFISSIONAL</th>
                                            <th rowspan="2">CBO</th>
                                            <th rowspan="2">QTDE.</th>
                                            <th rowspan="2">FORMA DE CONTRATAÇÃO</th>
                                            <th rowspan="2">CARGA HORÁRIA SEMANAL</th>
                                            <th colspan="5">R$</th>
                                        </tr>
                                        <tr>
                                            <th>SALÁRIO BASE</th>
                                            <th>ENCARGOS</th>
                                            <th>BENEFÍCIOS</th>
                                            <th>SALÁRIO TOTAL POR PROFISSIONAL</th>
                                            <th>SALÁRIO TOTAL<br> DA CATEGORIA <br></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($row = mysql_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>{$row['cargo']}</td>";
                                            echo "<td>{$row['cod']}</td>";
                                            echo "<td>{$row['qtde']}</td>";
                                            echo "<td>{$row['tipocontrato']}</td>";
                                            echo "<td>{$row['horas_mes']}</td>"; //{$row['horas_mes']};
                                            echo "<td class=\"txright\">" . number_format($row['salario'], 2, ",", ".") . "</td>";
                                            echo "<td class=\"txright\">" . number_format($row['percent'], 2, ",", ".") . "</td>";
                                            echo "<td class=\"txright\">" . number_format($row['rend'], 2, ",", ".") . "</td>";
                                            echo "<td class=\"txright\">" . number_format($row['salprofissional'], 2, ",", ".") . "</td>";
                                            echo "<td class=\"txright\">" . number_format($row['totalCat'], 2, ",", ".") . "</td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="8" class="txright">Totais:</td>

                                            <td class="txright"><?php echo number_format($total, 2, ",", ".") ?></td>
                                            <td class="txright"><?php echo number_format($total2, 2, ",", ".") ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </td></tr>  

                        <tr><td>
                                <?php if (!empty($result_rpa) && mysql_num_rows($result_rpa) > 0) { ?>
                                    <table id="rhTable_rpa" border="0" cellpadding="0" cellspacing="0" width="100%" class="grid">
                                        <thead>
                                            <tr>
                                                <th colspan="9">UNIDADE GERENCIADA: <?php echo $projeto['nome'] ?></th>
                                                <th><?php echo $mesShow ?></th>
                                            </tr>
                                            <tr>
                                                <th colspan="10">RH RPA</th>
                                            </tr>
                                            <tr>
                                                <th rowspan="2" style="width:366px">CATEGORIA PROFISSIONAL</th>
                                                <th rowspan="2">CBO</th>
                                                <th rowspan="2">QTDE.</th>
                                                <th rowspan="2">FORMA DE CONTRATAÇÃO</th>
                                                <th rowspan="2">CARGA HORÁRIA SEMANAL</th>
                                                <th colspan="5">R$</th>
                                            </tr>
                                            <tr>
                                                <th>SALÁRIO PAGO</th>
                                                <th>ENCARGOS</th>
                                                <th>BENEFÍCIOS</th>
                                                <th>SALÁRIO TOTAL POR PROFISSIONAL</th>
                                                <th>SALÁRIO TOTAL<br> DA CATEGORIA <br></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $totalProfissional = "";
                                            $totalCategoria = "";
                                            while ($row_rpa = mysql_fetch_assoc($result_rpa)) {

                                                $totalProfissional += $row_rpa['salario_profissional'];
                                                $totalCategoria += $row_rpa['salario_categoria'];

                                                echo "<td style='width:366px'>";
                                                if (empty($row_rpa['categoria'])) {
                                                    echo "MÉDICO";
                                                } else {
                                                    echo $row_rpa['categoria'];
                                                }
                                                echo "</td>";
                                                echo "<td>{$row_rpa['cbo_codigo']}</td>";
                                                echo "<td>{$row_rpa['qnt']}</td>";
                                                echo "<td>RPA</td>";
                                                echo "<td>";

                                                if (empty($row_rpa['hora_semana'])) {
                                                    echo "
                                            <div class='ocultar_campo' data-key='" . $row_rpa['id_curso'] . "'><input type='text' name='hora_semanal' class='j_horario' style='border: 1px solid #ccc; padding: 5px 13px; ' />
                                            <input type='button' name='enviar_horario' value='ok' class='j_cadastrar' />
                                            <input type='hidden' name='idcurso' value='" . $row_rpa['id_curso'] . "' class='j_curso' /></div> 
                                        ";
                                                } else {
                                                    echo $row_rpa['hora_semana'];
                                                }

                                                echo"</td>";
                                                echo "<td class=\"txright\">" . number_format($row_rpa['valor'], 2, ",", ".") . "</td>";
                                                echo "<td class=\"txright\">" .number_format($row_rpa['encargo'], 2, ",", "."). "</td>";
                                                echo "<td class=\"txright\"> 0,00 </td>";
                                                echo "<td class=\"txright\">" . number_format($row_rpa['valor'], 2, ",", ".") . "</td>";
                                                echo "<td class=\"txright\">" . number_format($row_rpa['salario_categoria'], 2, ",", ".") . "</td>";
                                                echo "</tr>";
                                            }
                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="8" class="txright">Totais:</td>
                                                <td class="txright"><?php echo number_format($totalProfissional, 2, ",", ".") ?></td>
                                                <td class="txright"><?php echo number_format($totalCategoria, 2, ",", ".") ?></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </td></tr>        
                        </table>    
                    <?php } ?>


                <?php } else { ?>
                    <?php if ($projetoR !== null) { ?>
                        <br/>
                        <div id='message-box' class='message-green'>
                            <p>Nenhum resultado</p>
                        </div>
                    <?php } ?>
                <?php } ?>

                <?php if ($projetoR !== null) { ?>
                    <?php if ($btexportar) { ?>
                        <p class="controls">
                            <input type="submit" class="button" value="Exportar" name="exportar" />
                        </p>
                    <?php } ?>

                    <br/>
                    <?php if ($btfinalizar) { ?>
                        <?php if ($erros == 0) { ?>
                            <p class="controls"> 
                                <input type="submit" class="button" value="Finalizar Prestação" name="finalizar" />
                            </p>
                        <?php } else { ?>
                            <div id='message-box' class='message-yellow'>
                                <p><?php echo $msgErro . " ";
                echo (count($idsErros) > 0) ? implode(", ", $idsErros) : "";
                            ?></p>
                            </div>
                        <?php } ?>
    <?php } else { ?>
                        <div id='message-box' class='message-yellow'>
                            <p>Prestação finalizada.</p>
                        </div>
    <?php } ?>


    <?php if ($proj_faltantes > 0) { ?>
                        <div id='message-box' class='message-blue'>
                            <p><?php echo $percent; ?>Foi verificado a existencia de <?php echo $contErro ?> projeto(s) para finalizar neste mês antes de gerar o arquivo de prestação de contas.</p>
                            <ul>
                                <?php
                                foreach ($projetosFaltante as $val) {
                                    echo "<li>" . $val['nome'] . $val['banco'] . "</li>";
                                }
                                ?>
                            </ul>
                        </div>
                    <?php } ?>
<?php } ?>
            </form>

        </div>
    </body>
</html>