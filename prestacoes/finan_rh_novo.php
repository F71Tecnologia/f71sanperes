<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');

$usuario = carregaUsuario();

$regiao = $usuario['id_regiao'];
$master = $usuario['id_master'];

$result = null;
$continue = true;
$btexportar = true;
$btfinalizar = true;

if (isset($_REQUEST['mes']) && isset($_REQUEST['ano'])) {
    
    $percent = $_REQUEST['percent'];
    $retifica = $_REQUEST['reti'];
    $id_projeto = $_REQUEST['projeto'];
    $terceiro = null;
    $mes2d = sprintf("%02d",$_REQUEST['mes']); //mes com 2 digitos
    if ($_REQUEST['mes'] == '12' && $_REQUEST['terceiro'] == 1) {
        $terceiro = 1;
    } else {
        $terceiro = 2;
    }

    /* FOLHAS DOS PROJETOS */
    if (isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) {
        
        /*RECUPERANDO OS PROJETOS JA FINALIZADOS*/
        //VERIFICA SE OUTRO PROJETO PRECISA PRESTAR CONTAS NO MES SELECIONADO
        $dataMesIni = "{$_REQUEST['ano']}-{$mes2d}-31";
        $dataMesRef = "{$_REQUEST['ano']}-{$mes2d}-01";
        $qr_verifica = "SELECT A.id_projeto,A.nome as projeto,DATE_FORMAT(B.gerado_em, '%d/%m/%Y') as gerado_embr, C.nome as funcionario
                        FROM projeto AS A
                        LEFT JOIN prestacoes_contas AS B ON (A.id_projeto=B.id_projeto AND tipo = 'rh' AND status = 1 AND data_referencia = '{$dataMesRef}')
                        LEFT JOIN funcionario AS C ON (B.gerado_por=C.id_funcionario)
                        WHERE A.inicio < '{$dataMesIni}' AND A.prestacontas = 1";

        
        $rs_verifica = mysql_query($qr_verifica);
        $arProjetos = array();
        while($row_veri = mysql_fetch_assoc($rs_verifica)){
            $arProjetos[] = $row_veri['id_projeto'];
        }
        
        $arProjetos = implode(",", $arProjetos);
        
        $qrFolhas = "SELECT A.id_folha,A.projeto,B.nome
                        FROM rh_folha AS A
                        LEFT JOIN projeto AS B ON(A.projeto=B.id_projeto)
                        WHERE A.regiao = {$regiao} AND A.status = 3 AND A.mes = {$_REQUEST['mes']} AND A.ano = {$_REQUEST['ano']} AND A.terceiro = {$terceiro} AND id_projeto IN ({$arProjetos})";
        
    }else{
        $qrFolhas = "SELECT A.id_folha,A.projeto,B.nome
                        FROM rh_folha AS A
                        LEFT JOIN projeto AS B ON(A.projeto=B.id_projeto)
                        WHERE A.regiao = {$regiao} AND A.status = 3 AND A.mes = {$_REQUEST['mes']} AND A.ano = {$_REQUEST['ano']} AND A.terceiro = {$terceiro} AND id_projeto = '{$_REQUEST['projeto']}'";
        
    }
    
    $reFolhas = mysql_query($qrFolhas);
    $arFolhas = array();
    $arFolhasProjetos = array();
    $arFolhasPro = "";
    $pro = "";
    while ($row = mysql_fetch_assoc($reFolhas)) {
        $arFolhas[] = $row['id_folha'];
        $arFolhasProjetos[] = $row['projeto'];
        if ($pro != $row['nome']) {
            $arFolhasPro .= $row['nome'] . ",";
            $pro = $row['nome'];
        }
    }
    
    $arFolhasPro = substr($arFolhasPro, 0, -1);

    if (mysql_num_rows($reFolhas) == 0) {
        $continue = false;
    }

    $dtreferencia = $_REQUEST['ano'] . "-" . $mes2d . "-01";
    $nterceiro = ($terceiro == 1) ? "Sim" : "Nao";
    $resValida = mysql_query("SELECT id_prestacao,status FROM prestacoes_contas  WHERE id_projeto = {$_REQUEST['projeto']} AND data_referencia = '{$dtreferencia}' AND terceiro = '{$nterceiro}'");
    if (mysql_num_rows($resValida)) {
        #$continue = false;
    }

    /* QUERY DOS PROJETOS */
    $qr = "
         SELECT *,
            CAST(salprofissional * qtde as decimal(13,2)) AS totalCat
            FROM
                (SELECT *,
                    CAST(((rendimentos / qtde + salario)) as decimal(13,2)) + CAST(((salario+(rendimentos / qtde)) * $percent) as decimal(13,2)) as salprofissional,
                    CAST(((salario+(rendimentos / qtde)) * $percent) as decimal(13,2)) AS percent
                          FROM 
				(SELECT temp.*,
				C.id_curso AS tcur,
				IF(temp.saldorescisao IS NOT NULL && temp.saldorescisao!=0,CONCAT(C.nome,' - RESCISAO'),C.nome) AS cargo,
                                IF(E.horas_semanais IS NULL,'-',E.horas_semanais) AS horas_mes,
				D.cod,
				COUNT(id_clt) AS qtde, 
 	 			SUM(rend) AS rendimentos
                                    FROM 
                                        (						 
                                        SELECT 
                                            A.id_clt,A.id_folha_proc,A.id_folha,A.id_projeto,B.id_curso,B.rh_horario,
                                            F.cod_sesrj,F.cod_contrato,REPLACE(REPLACE(EMP.cnae,'.',''),'-','') AS cnae,
                                            'CLT' AS tipocontrato,
                                            B.nome as nomeclt,B.cpf,B.data_entrada,

                                            IF(B.`status` != 10, (SELECT data FROM rh_eventos WHERE id_clt=A.id_clt AND MONTH(data) = '{$mes2d}' AND YEAR(data) = '{$_REQUEST['ano']}' ORDER BY id_evento ASC LIMIT 1),'') as dt_evento,

                                            (SELECT SUM(valor_movimento) as valor FROM rh_movimentos_clt WHERE id_movimento IN (33501,33502,35315,35316,33503,34915,34916,33504,34303,34304,33505,35601,35602,33506,35295,35296,33507,36661,36662,33508) AND id_clt = A.id_clt AND cod_movimento IN (6006,9996)) as insalubridade,
                                            (SELECT SUM(valor_movimento) as valor FROM rh_movimentos_clt WHERE id_movimento IN (33501,33502,35315,35316,33503,34915,34916,33504,34303,34304,33505,35601,35602,33506,35295,35296,33507,36661,36662,33508) AND id_clt = A.id_clt AND cod_movimento IN (9000,8005)) as add_noturno,
                                            (SELECT SUM(valor_movimento) as valor FROM rh_movimentos_clt WHERE id_movimento IN (33501,33502,35315,35316,33503,34915,34916,33504,34303,34304,33505,35601,35602,33506,35295,35296,33507,36661,36662,33508) AND id_clt = A.id_clt AND cod_movimento IN (5912)) as grat_even,
                                            (SELECT SUM(valor_movimento) as valor FROM rh_movimentos_clt WHERE id_movimento IN (33501,33502,35315,35316,33503,34915,34916,33504,34303,34304,33505,35601,35602,33506,35295,35296,33507,36661,36662,33508) AND id_clt = A.id_clt AND cod_movimento IN (5061,5060)) as grat_mens,


                                            A.sallimpo AS salario,
                                            (A.sallimpo + CAST(A.rend AS DECIMAL(13,2))) AS remun_bruta,
                                            G.saldo_salario AS remun_rescisao,
                                            CAST(A.rend AS DECIMAL(13,2)) as rend,
                                            A.inss,
                                            A.fgts,

                                             G.saldo_salario AS saldorescisao,
                                            (SELECT id_curso_de FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '{$dtreferencia}' ORDER BY id_transferencia ASC LIMIT 1) AS de,
                                            (SELECT id_curso_para FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '{$dtreferencia}' ORDER BY id_transferencia DESC LIMIT 1) AS para,
                                            (SELECT id_horario_de FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '{$dtreferencia}' ORDER BY id_transferencia ASC LIMIT 1) AS horario_de,
                                            (SELECT id_horario_para FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '{$dtreferencia}' ORDER BY id_transferencia DESC LIMIT 1) AS horario_para  

                                            FROM rh_folha_proc AS A
                                            LEFT JOIN rh_folha AS FO ON (A.id_folha = FO.id_folha)
                                                 LEFT JOIN rh_clt AS B ON (B.id_clt=A.id_clt)
                                            LEFT JOIN projeto AS F ON (A.id_projeto=F.id_projeto)
                                            LEFT JOIN rh_recisao AS G ON (G.id_clt=A.id_clt AND G.`status` = 1 AND MONTH(G.data_demi) = {$mes2d} AND YEAR(G.data_demi) = {$_REQUEST['ano']})
                                            LEFT JOIN rhempresa AS EMP ON (EMP.id_projeto = A.id_projeto)

                                            WHERE A.id_folha IN (" . implode(",", $arFolhas) . ") AND A.status = 3 GROUP BY id_folha_proc
                                 ) as temp

				LEFT JOIN curso AS C ON (IF(temp.para IS NOT NULL,C.id_curso=temp.para, IF(temp.de IS NOT NULL,C.id_curso=temp.de,C.id_curso=temp.id_curso)))
                                LEFT JOIN rh_horarios AS E ON (IF(temp.horario_para IS NOT NULL,E.id_horario=temp.horario_para, IF(temp.de IS NOT NULL,E.id_horario=temp.horario_de,E.id_horario=temp.rh_horario)))
				LEFT JOIN rh_cbo AS D ON (D.id_cbo=C.cbo_codigo)
				
				GROUP BY id_folha,cargo,rend
				ORDER BY id_folha,cargo,rend
				
                        ) as tb) as tm ";

    $qrMaster = "SELECT nome,cod_os FROM master WHERE id_master = {$master}";
    $reMaster = mysql_query($qrMaster);
    $roMaster = mysql_fetch_assoc($reMaster);

    $mesShow = mesesArray($_REQUEST['mes']) . "/" . $_REQUEST['ano'];
    if ($terceiro == 1)
        $mesShow .= " 13º salário";
}

/* FINALIZA A PRESTAÇÃO, GUARDA TODOS OS VALORES EM UMA TABELA PARA CONSULTAS POSTERIOES */
if (isset($_REQUEST['finalizar']) && !empty($_REQUEST['finalizar'])) {
    //PRIMEIRA PARTE DO RELATORIO
    $result = mysql_query($qr);
    $linhasArquivo = mysql_num_rows($result);
    //TOTALIZADOR
    $qr_to = "SELECT SUM(salprofissional) as total, SUM(totalCat) as total2 FROM (" . $qr . ") AS b";
    $result_to = mysql_query($qr_to);
    $row_total = mysql_fetch_assoc($result_to);
    $total = $row_total['total'];
    $total2 = $row_total['total2'];


    //PREPARANDO VARIAVIES PARA GUARDAR NA TABELA 'prestacoes_contas'
    $id_folha = (count($arFolhas) == 1) ? current($arFolhas) : "0";
    $gerado = date("Y-m-d H:i:s");

    $campos = array(
        "id_regiao",
        "id_projeto",
        "id_folha",
        "terceiro",
        "tipo",
        "data_referencia",
        "gerado_em",
        "gerado_por",
        "linhas",
        "valor_total",
        "valor_total_profissional"
    );

    $valores = array(
        $regiao,
        $_REQUEST['projeto'],
        $id_folha,
        $nterceiro,
        "rh",
        $dtreferencia,
        $gerado,
        $usuario['id_funcionario'],
        $linhasArquivo,
        $total,
        $total2
    );

    $id = sqlInsert("prestacoes_contas", $campos, $valores);

    $matriz = array();
    $count = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $matriz[$count][] = $id;
        $matriz[$count][] = $row['id_projeto'];
        $matriz[$count][] = $row['cargo'];
        $matriz[$count][] = $row['cod'];
        $matriz[$count][] = $row['qtde'];
        $matriz[$count][] = $row['tipocontrato'];
        $matriz[$count][] = (empty($row['horas_mes']))?"null":$row['horas_mes'];
        $matriz[$count][] = (empty($row['salario']))?"null":$row['salario'];
        $matriz[$count][] = (empty($row['percent']))?"null":$row['percent'];
        $matriz[$count][] = (empty($row['rend']))?"null":$row['rendimentos'];
        $matriz[$count][] = (empty($row['salprofissional']))?"null":$row['salprofissional'];
        $matriz[$count][] = (empty($row['totalCat']))?"null":$row['totalCat'];
        $count++;
    }

    $campos = array(
        "id_prestacao",
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
    fwrite($handle, "RESERVA_DEST_PROVI;BAIXA_PROV_FERIAS;BAIXA_PROV_13_SAL;BAIXA_PROV_RESCISAO\r\n");*/
    
    fwrite($handle, "D;COD_OS;COD_UNIDADE;COD_CONTRATO;ANO_MES_REF;CATEGORIA;QUANTIDADE;FORMA_CONTRATACAO;CARGA_HORARIA;SALARIO_BASE;");
    fwrite($handle, "BENEFICIOS;OUTROS_ENCARGOS;SALARIO_TOTAL\r\n");


    //ESCREVENDO AS LINHAS NO ARQUIVO (PARTE 1 - FOLHA DA UNIDADE)
    while ($row = mysql_fetch_assoc($result)) {
        $salario = str_replace(".", ",", $row['salario']);
        $rendimentos = str_replace(".", ",", $row['rend']);
        $descontos = str_replace(".", ",", $row['percent']);
        $totalCat = str_replace(".", ",", $row['totalCat']);

        fwrite($handle, "D;{$roMaster['cod_os']};{$row['cod_sesrj']};{$row['cod_contrato']};{$anoMesReferencia};{$row['cargo']};{$row['qtde']};");
        fwrite($handle, "{$row['tipocontrato']};{$row['horas_mes']};{$salario};{$rendimentos};");
        fwrite($handle, "{$descontos};{$totalCat}\r\n");
    }
    unset($row);
    $qr_to = "SELECT SUM(salario) as base, SUM(rendimentos) AS rendimentos,  SUM(percent) AS percent, SUM(totalCat) as saltotal
                     FROM (" . $qr . ") AS b";
    $result_to = mysql_query($qr_to);
    $row_total = mysql_fetch_assoc($result_to);

    $baseTo = $row_total['base'];
    $rendimentoTo = $row_total['rendimentos'];
    $encargosTo = $row_total['percent'];
    $saltotalTo = $row_total['saltotal'];

    $baseTo = str_replace(".", ",", $baseTo);
    $rendimentoTo = str_replace(".", ",", $rendimentoTo);
    $encargosTo = str_replace(".", ",", $encargosTo);
    $saltotalToF = str_replace(".", ",", $saltotalTo);
    
    /*RODAPE*/
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
if (isset($_REQUEST['relatorio']) && !empty($_REQUEST['relatorio'])) {
    echo "<!-- $qr -->\r\n";
    
    if ($continue) {
        $result = mysql_query($qr);

        $qr_to = "SELECT 
                        SUM(salprofissional) as total, 
                        SUM(totalCat) as total2,
                        SUM(rendimentos) as torendi,
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
            $btfinalizar = false;*/
        
    }
    
    //LÓGICA DE VERIFICAÇÃO DE OUTROS PROJETOS EM ABERTOS
    //VERIFICA SE OUTRO PROJETO PRECISA PRESTAR CONTAS NO MES SELECIONADO
    $dataMesIni = "{$_REQUEST['ano']}-{$mes2d}-31";
    $dataMesRef = "{$_REQUEST['ano']}-{$mes2d}-01";
    $qr_verifica = "SELECT A.id_projeto,A.nome as projeto,DATE_FORMAT(B.gerado_em, '%d/%m/%Y') as gerado_embr, C.nome as funcionario
                    FROM projeto AS A
                    LEFT JOIN prestacoes_contas AS B ON (A.id_projeto=B.id_projeto AND tipo = 'rh' AND status = 1 AND data_referencia = '{$dataMesRef}')
                    LEFT JOIN funcionario AS C ON (B.gerado_por=C.id_funcionario)
                    WHERE A.inicio < '{$dataMesIni}' AND A.prestacontas = 1";
                    
    echo "<!-- $qr_verifica -->\r\n";
    $rs_verifica = mysql_query($qr_verifica);
    $total_verifica = mysql_num_rows($rs_verifica);
    $projetosFaltante = array();
    $contErro = 0;
    
    while($rowVeri = mysql_fetch_assoc($rs_verifica)){
        //VERIFICA SE OS OUTROS NÃO ESTÃO FINALIZADOS
        if($rowVeri['gerado_embr'] == null && $rowVeri['id_projeto'] != $id_projeto){
            $btexportar = false;
            $projetosFaltante[] = $rowVeri['projeto'];
            $contErro ++;
        }elseif($rowVeri['gerado_embr'] != null && $rowVeri['id_projeto'] == $id_projeto){  //VERIFICA SE O ATUAL ESTÁ FINALIZADO
            $btfinalizar = false;
        }
        
        //VERIFICA SE SÓ TEM 1 E SE JA FOI FINALIZADO
        if($total_verifica == 1 && $rowVeri['id_projeto'] == $id_projeto && $rowVeri['gerado_embr'] != null){
            $btexportar=false;
        }
    }
    
    if($btfinalizar)
        $btexportar = false;
    
    $proj_faltantes = count($projetosFaltante);
    
}

$meses = mesesArray(null);
$anos = anosArray(null, null, array("-1" => "« Selecione o ano »"));

/* SELECIONA TODOS OS PROJETOS EXCETO ADMINISTRAÇÃO */
$rsProj = montaQuery("projeto", "*", "id_master = {$master} AND id_regiao = {$regiao}", "nome");
$projetos = array("-1" => "« Selecione »");
foreach ($rsProj as $pro) {
    $projetos[$pro['id_projeto']] = $pro['id_projeto'] . " - " . $pro['nome'];
}


/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : null;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : null;
$rterceiro = (isset($_REQUEST['terceiro'])) ? $_REQUEST['terceiro'] : 0;
$rcterceiro = ($mesR==12) ? "":"class=\"hidden\"";

$retiR = (isset($_REQUEST['reti'])) ? $_REQUEST['reti'] : "0";
$percentR = (isset($_REQUEST['percent'])) ? $_REQUEST['percent'] : "0.7561";

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
            }
            @media screen
            {
                #headerPrint{display: none;}
            }
        </style>

        <script>
            $(function(){
                $("#form1").validationEngine();
                
                $("#bt-ver").click(function(){
                    thickBoxIframe("Histórico de Arquivos", "historico.php", {tipo:"rh"}, 850, 500);
                });
                
                $("#mes").change(function(){
                    if($("#mes").val() == "12"){
                        $("#pterceiro").removeClass('hidden');
                    }else{
                        $("#pterceiro").addClass('hidden');
                    }
                    
                    if($("#projeto").val() != "-1" && $("#mes").val() != "-1" && $("#ano").val() != "-1"){
                        checaFolha();
                    }
                });
                
                $("#ano").change(function(){
                    if($("#projeto").val() != "-1" && $("#mes").val() != "-1" && $("#ano").val() != "-1")
                        checaFolha();
                });
                
                $("#projeto").change(function(){
                    if($("#projeto").val() != "-1" && $("#mes").val() != "-1" && $("#ano").val() != "-1")
                        checaFolha();
                });
                
            });
            
            var checaFolha = function(){
                var ter = $("input[name=terceiro]:checked").val();
                $.post('verifica.php', {projeto: $("#projeto").val(), mes: $("#mes").val(), ano: $("#ano").val(), terceiro:ter , method: "checafolha"}, function(data) {
                    $("#reti").val(0);
                    $("#reti").parent().addClass('hidden');
                    if(data.status==1){
                        $("#mes").val("-1");
                        thickBoxAlert("Atenção", "A Prestação de Contas ja foi gerada com as opções selecionadas. Não é possivel gerar outra para o mesmo mês<br><a href='historico_rh.php?id="+data.prestacao+"'>Clique aqui para visualizar.</a>", 400, 180);
                    }
                },"json");
            }
        </script>
    </head>
    <body id="page-fin-rh" class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1">
                <div id="headerPrint">
                    <div id="head">
                        <img src="../imagens/logomaster<?php echo $master; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                        <div class="fleft">
                            <h2><?php echo $roMaster['razao'] ?></h2>
                            <h2>RECURSOS HUMANOS</h2>
                        </div>
                    </div>
                    <br class="clear"/>
                </div>

                <input type="hidden" name="folhaR" id="folhaR" value="<?php echo $folhaR ?>" />
                <input type="hidden" name="proadmR" id="proadmR" value="<?php echo $proadmR ?>" />
                <fieldset>
                    <legend>Dados</legend>

                    <p><label class="first">Projeto:</label> <?php echo montaSelect($projetos, $projetoR, "id='projeto' name='projeto' class='validate[custom[select]]'") ?></p>
                    <p><label class="first">Mês:</label> <?php echo montaSelect($meses, $mesR, "id='mes' name='mes' class='validate[custom[select]]'") ?>  <?php echo montaSelect($anos, $anoR, "id='ano' name='ano' class='validate[custom[select]]'") ?> (mês da folha de pagamento)</p>
                    <p id="pterceiro" <?php echo $rcterceiro?>><label class="first">Décimo Terceiro?</label> <label><input type="radio" name="terceiro" id="terceiroS" value="1" <?php echo ($rterceiro==1)?"checked='checked'":""; ?>/> Sim </label> <label><input type="radio" name="terceiro" id="terceiroN" value="0" <?php echo ($rterceiro==0)?"checked='checked'":""; ?>/> Não </label> </p>
                    <p class="hidden"><label class="first">Retificadora:</label> <input type="hidden" name="reti" id="reti" value="<?php echo $retiR ?>" /> Sim </p>
                    <p><label class="first">Percentual de Encargos:</label> <input type="hidden" name="percent" id="percent" value="<? echo $percentR ?>" size="4" /> <? echo $percentR ?> </p>

                    <p class="controls"> 
                        <input type="submit" class="button" value="Relatório" name="relatorio" /> 
                        <!-- <input type="button" class="button" id="bt-ver" value="Prestações Finalizadas" name="gerados" /> -->
                    </p>
                </fieldset>

                <?php if (!empty($result) && mysql_num_rows($result) > 0) { ?>
                    <br/><br/>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="grid">
                        <thead>
                            <tr>
                                <th colspan="9">UNIDADE GERENCIADA: <?php echo $arFolhasPro ?></th>
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
                                if ($_REQUEST['projeto'] == "-1" && $row['id_projeto'] != $proAnt) {
                                    $terceiro = ($row['terceiro'] == 1) ? " - 13º Salário" : "";
                                    echo "<tr class=\"titulo\"><td colspan=\"10\">{$row['projeto']}{$terceiro}</td></tr>";
                                    $proAnt = $row['id_projeto'];
                                }
                                echo "<tr>";
                                echo "<td>{$row['cargo']}</td>";
                                echo "<td>{$row['cod']}</td>";
                                echo "<td>{$row['qtde']}</td>";
                                echo "<td>{$row['tipocontrato']}</td>";
                                echo "<td>{$row['horas_mes']}</td>";
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
                        <?php if ($btexportar) { ?>
                            <p class="controls">
                                <input type="submit" class="button" value="Exportar" name="exportar" />
                            </p>
                        <?php } ?>
                        
                        <br/>
                        <?php if ($btfinalizar) { ?>
                            <p class="controls"> 
                                <input type="submit" class="button" value="Finalizar Prestação" name="finalizar" />
                            </p>
                        <?php }else{ ?>
                            <div id='message-box' class='message-yellow'>
                                <p>Prestação finalizada.</p>
                            </div>
                        <?php } ?>
                            
                            
                        <?php if($proj_faltantes > 0){ ?>
                        <div id='message-box' class='message-blue'>
                            <p>Foi verificado a existencia de <?php echo $contErro?> projeto(s) para finalizar neste mês antes de gerar o arquivo de prestação de contas.</p>
                            <ul>
                            <?php foreach($projetosFaltante as $val){
                                echo "<li>".$val."</li>";
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