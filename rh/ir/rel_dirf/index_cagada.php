<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include("../../../conn.php");
include("../../../classes/funcionario.php");
include("../../../classes_permissoes/regioes.class.php");
include("../../../wfunction.php");

error_reporting(0);
ini_set('error_reporting', 0);
$usuario = carregaUsuario();
$relatorio = false;
$limitado = false; //"LIMIT 0,150";
$debug = false;
$debugSql = false;
$geraRelatorio = true;
$decimoAntigo = true;
$idsFaltaAtrazo = "62,232,236"; //IMPORTANTISSIMO NA LAGOS ESSES IDS SAO FALTA E ATRAZO

//FORMATAÇÃO NOJENTA, POIS O ARQUIVO DA DIRF NÃO PODE TER SEPARADO DECIMAL
//E QUANDO VOU MOSTRAR NA LISTAGEM TEM QUE TER PARA NÃO CONFUNDIR O USUÁRIO
//QUANDO O VALOR ESTÁ 200,00 VIRA 20000 E SE COLOCAR O numberformat() ELE VAI
//FAZER OS 200,00 VIRAR 20000,00...
function formatoNojo($val) {
    if (!empty($val)) {
        $valor = substr($val, 0, -2) . "," . substr($val, -2, 2);
    } else {
        $valor = "-";
    }
    return $valor;
}


//VALIDAÇÃO DE VALOR PARA NÃO ENTRAR UMA DETERMINADA LINHA CASO TODOS OS VALORES
//DAQUELA LINHA ESTEJÃO ZERADOS
function validaValor($arr) {
    $return = false;
    foreach ($arr as $valor) {
        $val = intval($valor);
        if ($val > 0) {
            $return = true;
        }
    }
    return $return;
}

function verificaDuplicidade($cpf) {
    $result = array();
    $result['rs'] = montaQuery("rh_clt", "id_clt", "REPLACE(REPLACE(cpf,'.',''),'-','')  = '{$cpf}'");
    $result['total'] = count($result['rs']);
    return $result;
}

function printaValor($arr){
    for($i=0;$i<=11;$i++){
        echo "<td>" . formatoNojo($arr[$i]) . "</td>";
    }
}

//SELECIONA OS MASTERS
$rowMaster = montaQueryFirst("master", "cnpj,razao,nome", "id_master = {$usuario['id_master']}");

//ANO
$optAnos = array();
for ($i = 2009; $i <= date('Y'); $i++) {
    $optAnos[$i] = $i;
}
//SÓ NO IDR O CAMPO (NOME) DENTRO DA TABELA RHEMPRESA NÃO É O NOME DO PROJETO, E SIM A SIGLA IDR
//SOMENTE AQUI NO IDR VOU TER QUE FAZER UM LEFTJOIN PARA TER OS NOMES DOS PROJETOS DE ACORDO COM 
//O ID DO MASTER DO USUARIO LOGADO (FALTA DE PADRÃO DESDE O CADASTRO ISSO Q FAZ O CÓDIGO SER CHEIO DE GAMBI)
//PEGANDO OS PROJETOS PRIMEIRA PARTE

$rsPro1 = montaQuery("projeto", "*", "id_master = {$usuario['id_master']}");
$pros[] = array();
foreach ($rsPro1 as $pro) {
    $pros[$pro['id_projeto']] = $pro['nome'];
}

//PEGANDO OS PROJETOS
$rsEmpresas = montaQuery("rhempresa", "*", "id_master = {$usuario['id_master']}");
$projetos = array();
$proNomes = array();
foreach ($rsEmpresas as $emp) {
    $projetos[] = $emp['id_projeto'];
    
    //MOSTRANDO A FOLHA DA ADM SOMENTE PARA O USUARIO 158 QUE SOU EU RAMON (NEM TODOS OS USUÁRIOS PODEM VER ESSA FOLHA)
    if($emp['id_projeto'] != "3309" || (($usuario['id_funcionario']==158 || $usuario['id_funcionario']==199)&& $emp['id_projeto'] == "3309")){
        $proNomes[$emp['id_projeto']] = $pros[$emp['id_projeto']];
    }
}

$tipoSelN = null;
$tipoSelS = null;

$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y') - 1;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;

if (isset($_REQUEST['tipo']) && $_REQUEST['tipo'] == "S") {
    $tipoSelS = "cheched='checked'";
} elseif (isset($_REQUEST['tipo']) && $_REQUEST['tipo'] == "N") {
    $tipoSelN = "cheched='checked'";
}

if (isset($_POST['historico'])) {
    $ano_calendario = $_POST['ano'];
    $master = $_POST['id_master'];
    $qr_historico = mysql_query("SELECT * FROM dirf WHERE id_master = '$master'  AND status = 1");
    $verifica_historico = mysql_num_rows($qr_historico);
}

if (isset($_REQUEST['filtrar']) && validate($_REQUEST['filtrar'])) {
    if ($limitado != false) {
        echo "Atenção, relatório limitado para testes<br/> Agarde o carregamento...";
    }
    $relatorio = true;

    $ano_referencia = date('Y');
    $ano_calendario = $_REQUEST['ano'];
    $ano_anterior = $ano_calendario - 1;
    $master = $usuario['id_master'];
    $tipoArq = (isset($_REQUEST['tipo'])) ? $_REQUEST['tipo'] : "N";
    
    //FEITA PARA A LAGOS POIS SÃO MUITOS PROJETOS E A VALIDAÇÃO DO RELATÓRIO VAI SER FEITA DE CADA PROJETO
    //QUANDO FOREM EXPORTAR AE SIM UTILIZO O ARRAY COM TODOS OS PROJETOS
    
    unset($projetos);
    $projetos[] = $_REQUEST['projeto'];
    
    //IMPORTANTE
    //O HENRIQUE PASSOU INFORAMÇÃO DE QUE ESSE RELATORIO NAO EH POR COMPETENCIA E SIM POR PAGAMENTO
    //ENTÃO A FOLHA DE DEZEMBRO NÃO ENTRA, POIS ELA SÓ É PG EM JANEIRO
    //CASO O PAGAMENTO DA FOLHA SEJE NO DIA 5 DE CADA MES
    //CADA EMPRESA FALA Q É DE UMA FORMA
    //A EMÍLIA DA ASCAGEL ENTROU EM CONTATO COM OCONTADOR E FALOU QUE TEM QUE ENTRAR SIM

    $folhaDezembro = true;

    if ($folhaDezembro) {
        $whereDezembro = " AND A.ano = {$ano_calendario} ";
    } else {
        $ano_ini = $ano_anterior;
        $ano_fim = $ano_calendario;
        $whereDezembro = "  AND ( (A.mes = 12 AND A.ano = {$ano_ini} AND A.terceiro = 2) OR (A.mes <> 12 AND A.ano = {$ano_fim}) OR (A.mes = 12 AND A.ano = {$ano_fim} AND A.terceiro = 1) )";
    }
    
    //AS FOLHAS ANTIGAS O DECIMO TERCEIRO ERA PAGO METADE DE TUDO EM CADA FOLHA
    //A PARTIR DE 2013 NOS NOVOS CLIENTES A PRIMEIRA PARCELA DO DECIMO VINHA O SALARIO BRUNO NORMAL
    //E NA SEGUNDA PARCELA ERA CALCULADO O SALARIO NORMAL MENOS O MOVIMENTO DE ADIANTAMENTO DO DECIMO TERCEIRO
    //ENTÃO NAS NOVAS FOLHAS É SÓ PEGAR A VALOR DA SEGUNDA PARCELA, LA JA TEM OS CALCULOS QUE VÃO NA DIRF
    //E NAS FOLHAS ANTIGAS VOCE PRECISA SOMAR TODOS OS RESULTADOS DAS 2 FOLHAS
    if($decimoAntigo){
        $whereDecimo = "1,2,3";
    }else{
        $whereDecimo = "2,3";
    }

    ///////////////////////////// //////////////////////////////////////////////////////
    ///////////REGISTROS DE VALORES DO MASTER PARA O HEADER DO ARQUIVO TXT /////////////
    ///////////////////////////// //////////////////////////////////////////////////////
    $qr_empresa = mysql_query("SELECT B.nome, REPLACE(REPLACE(B.cpf,'.',''),'-','') as cpf,
            REPLACE(REPLACE(REPLACE(B.cnpj,'.',''),'/',''),'-','') as cnpj,
            B.responsavel, B.razao,
            SUBSTR(REPLACE(REPLACE(B.tel, '(',''),')',''),1,2) as ddd,
            REPLACE(SUBSTR(B.tel,5),'-','') as telefone,
            B.contador_cpf, B.contador_nome, B.contador_tel_ddd,B.contador_tel_num, B.contador_fax, B.contador_email 
            FROM regioes as A
            INNER JOIN rhempresa as B ON (A.id_regiao = B.id_regiao)
            WHERE A.id_master = '{$master}' AND A.sigla = 'AD';") OR die("ln 95: " . mysql_error());
    $empresa = mysql_fetch_assoc($qr_empresa);

    $nomeFile = $empresa['cnpj'] . "_" . $ano_calendario . ".txt";
    $nome_arquivo = "arquivos/{$nomeFile}";
    $arquivo = fopen($nome_arquivo, 'w+');

    //////////////////////////////////////
    /////CABEÇALHO (IDENTIFICADOR DIRF /////
    ////////////////////////////////////////
    $IDENTIFICADOR_DIRF['ID_REGISTRO'] = 'DIRF';
    $IDENTIFICADOR_DIRF['ANO_REFERENCIA'] = $ano_referencia;
    $IDENTIFICADOR_DIRF['ANO_CALENDARIO'] = $ano_calendario;
    $IDENTIFICADOR_DIRF['IDENTIFICADOR_RETIFICADORA'] = $tipoArq;
    $IDENTIFICADOR_DIRF['NUMERO_RECIBO'] = NULL;
    $IDENTIFICADOR_DIRF['IDENTIFICADOR_ESTRUTURA_LEIAUTE'] = 'F8UCL6S';

    $LINHA_ID_DIRF = implode('|', $IDENTIFICADOR_DIRF) . '|';

    //////////////////////////////////////
    /////RESPONSÁVEL (IDENTIFICADOR RESPO /////
    ////////////////////////////////////////
    
    $IDENTIFICADOR_RESPO['ID_REGISTRO'] = 'RESPO';
    $IDENTIFICADOR_RESPO['CPF'] = sprintf("%011s", $empresa['contador_cpf']);
    $IDENTIFICADOR_RESPO['NOME'] = $empresa['contador_nome'];
    $IDENTIFICADOR_RESPO['DDD'] = $empresa['contador_tel_ddd'];
    $IDENTIFICADOR_RESPO['TELEFONE'] = $empresa['contador_tel_num'];
    $IDENTIFICADOR_RESPO['RAMAL'] = '';
    $IDENTIFICADOR_RESPO['FAX'] = $empresa['contador_fax'];
    $IDENTIFICADOR_RESPO['EMAIL'] = $empresa['contador_email'];
    
    $LINHA_ID_RESPO = implode('|', $IDENTIFICADOR_RESPO) . '|';
    
    //////////////////////////////////////
    /////DECLARAÇÃO DE PESSOA JURÍDICA (IDENTIFICADOR DECPJ) /////
    ////////////////////////////////////////
    $IDENTIFICADOR_DECPJ['ID_REGISTRO'] = 'DECPJ';
    $IDENTIFICADOR_DECPJ['CNPJ'] = sprintf("%014s", $empresa['cnpj']);
    $IDENTIFICADOR_DECPJ['NOME_EMPRESA'] = $empresa['razao'];
    $IDENTIFICADOR_DECPJ['NATUREZA_DECLARANTE'] = 0;
    $IDENTIFICADOR_DECPJ['CPF_RESPONSAVEL'] = sprintf("%011s", $empresa['cpf']);
    $IDENTIFICADOR_DECPJ['INDICADOR_SOCIO'] = 'N';
    $IDENTIFICADOR_DECPJ['INDICADOR_DECLARANTE_DEPOSITARIO'] = 'N';
    $IDENTIFICADOR_DECPJ['INDICADOR_DECLARANTE_INSTITUICAO'] = 'N';
    $IDENTIFICADOR_DECPJ['INDICADOR_DECLARANTE_RENDIMENTOS'] = 'N';
    $IDENTIFICADOR_DECPJ['INDICADOR_PLANO_PRIVADO'] = 'N';
    $IDENTIFICADOR_DECPJ['INDICADOR_PAGAMENTOS'] = 'N';
    $IDENTIFICADOR_DECPJ['INDICADOR_SITUACAO_ESPECIAL'] = 'N';
    $IDENTIFICADOR_DECPJ['DATA_EVENTO'] = '';

    $LINHA_DECPJ = implode('|', $IDENTIFICADOR_DECPJ) . '|';


    ///////////////////////////// //////////////////////////////////////
    /////IDENTIFICAÇÃO DE CÓDIGO DA RECEITA (IDENTIFICADOR IDREC) /////
    ///////////////////////////// //////////////////////////////////////
    $IDENTIFICADOR_IDREC['ID_REGISTRO'] = 'IDREC';
    $IDENTIFICADOR_IDREC['CODIGO_RECEITA'] = '0561';

    $LINHA_IDREC = implode('|', $IDENTIFICADOR_IDREC) . '|';

    $arquivo = fopen($nome_arquivo, 'w');
    
    fwrite($arquivo, $LINHA_ID_DIRF);
    fwrite($arquivo, "\n");
    fwrite($arquivo, $LINHA_ID_RESPO);
    fwrite($arquivo, "\n");
    fwrite($arquivo, $LINHA_DECPJ);
    fwrite($arquivo, "\n");
    fwrite($arquivo, $LINHA_IDREC);
    fwrite($arquivo, "\n");


    //////////////////////////////////////////////////////////   
    ///////////REGISTROS DE CLTS  ////////////////////////////
    ////////////////////////////////////////////////////////// 
    $qr_clts = "SELECT REPLACE(REPLACE(D.cpf,'-',''), '.','') as cpf, D.nome, D.id_clt, D.id_projeto
                                FROM rh_folha as A
                                INNER JOIN regioes as B ON (B.id_regiao = A.regiao)
                                INNER JOIN rh_folha_proc AS C ON (C.id_folha = A.id_folha)
                                INNER JOIN rh_clt as D ON (D.id_clt = C.id_clt)
                                WHERE A.projeto IN (" . implode(",", $projetos) . ") AND A.status = 3 AND C.status = 3 
                                $whereDezembro
                                GROUP BY D.cpf
                                ORDER BY D.cpf ASC {$limitado}"; //
    $rs_clts = mysql_query($qr_clts) or die("ln 180: " . mysql_error());

    //////////////////////////////////////////////////////////   
    /////////// FOLHAS ENVOLVIDAS NA DIRF  ///////////////////
    ////////////////////////////////////////////////////////// 
    $qr_folhas = "SELECT id_folha,ids_movimentos_estatisticas
                        FROM rh_folha as A 
                        WHERE A.projeto IN (" . implode(",", $projetos) . ") AND A.status = 3 
                          $whereDezembro"; //
    $rs_folhas = mysql_query($qr_folhas) or die("ln 186: " . mysql_error());
    $ids_movimentos_folhas = "";
    $idsEstatisticas[] = array();
    echo "<!-- ";
    while ($row_folha = mysql_fetch_assoc($rs_folhas)) {
        echo $row_folha['id_folha'] . ",";
        $ids_movimentos_folhas .= $row_folha['ids_movimentos_estatisticas'] . ",";
        $idsEstatisticas[$row_folha['id_folha']] = $row_folha['ids_movimentos_estatisticas'];
    }
    echo " -->";
    ///PUTZ CARA, ALEM DO TER Q SE BASEAR NUMA STRING DE IDS SEPARADOS POR VIRGULA, AINDA PRECISO TRATAR SE TEM 2 OU 3 VIRGULAS JUNTAS
    // 10,11,,,20,,13,14,15,,17
    // É MUITA NOJEIRA CARA.. 
    $ids_movimentos_folhas = str_replace(',,,', ',', $ids_movimentos_folhas);
    $ids_movimentos_folhas = str_replace(',,', ',', $ids_movimentos_folhas);

    if (substr($ids_movimentos_folhas, -1) == ",") {
        $ids_movimentos_folhas = substr($ids_movimentos_folhas, 0, -1);
    }
    if (substr($ids_movimentos_folhas, 0, 1) == ",") {
        $ids_movimentos_folhas = substr($ids_movimentos_folhas, 1);
    }
    
    //CÓDIGOS DE RESCISÃO PARA NÃO PEGAR A RESCISÃO DO CARA PELA FOLHA, E SIM PELA PARTE DE RESCISÃO
    $qr_codigos = "SELECT codigo FROM rhstatus WHERE tipo = 'recisao'";
    $rs_codigos = mysql_query($qr_codigos) or die("ln 290: " . mysql_error());
    $codigosRes = "";
    while($rowCodigos = mysql_fetch_assoc($rs_codigos)){
        $codigosRes .= $rowCodigos['codigo'].",";
    }
    $codigosRes = substr($codigosRes, 0, -1);
    
    $c = 0;
    $linhaRel = array();
    $folhaClt = array();
    
    while ($clt = mysql_fetch_assoc($rs_clts)) {
        //echo $c . " - {$clt['id_clt']} - {$clt['nome']}<br>";
        if ($debug)
            echo "ini({$c} - {$clt['id_clt']}) ";

        //DADOS DO CLT
        $IDENTIFICADOR_BPFDEC['ID_REGISTRO'] = 'BPFDEC';
        $IDENTIFICADOR_BPFDEC['CNPJ'] = trim($clt['cpf']);
        $IDENTIFICADOR_BPFDEC['NOME_EMPRESA'] = trim($clt['nome']);
        $IDENTIFICADOR_BPFDEC['DATA_ATRIBUIDA'] = '';
        $LINHA_BPFDEC = implode('|', $IDENTIFICADOR_BPFDEC) . '|';
        fwrite($arquivo, $LINHA_BPFDEC);
        fwrite($arquivo, "\n");
        
        $duplicidade = verificaDuplicidade($clt['cpf']);
        $totalCpfs = $duplicidade['total'];
        if ($totalCpfs > 1) {
            $ids_clts = "";
            foreach ($duplicidade['rs'] as $val) {
                $ids_clts .= $val['id_clt'] . ",";
            }
            $ids_clts = substr($ids_clts, 0, -1);
        } else {
            $ids_clts = $clt['id_clt'];
        }
        
        if ($debug) echo " inicio SALARIO NORMAL ";

        //----- SALARIO NORMAL ----------
        $sql_valores = "SELECT	A.id_folha,A.mes,A.ano,A.projeto,B.id_clt,B.status_clt,
                                SUM(sallimpo) AS sallimpo,
                                SUM(sallimpo_real) AS sallimpo_real,
                                SUM(salbase) AS salbase,
                                SUM(B.inss) AS inss,
                                SUM(B.a5049) AS a5049,
                                SUM(B.a5021) AS a5021,
                                B.ano, A.ids_movimentos_estatisticas
                                
                          FROM  rh_folha as A
                    INNER JOIN  rh_folha_proc as B ON (B.id_folha = A.id_folha)
                         WHERE  B.id_clt IN ({$ids_clts}) AND A.status = 3 AND A.ano = '{$ano_calendario}' 
                           AND  A.terceiro = 2 AND B.status = 3 AND B.status_clt NOT IN ({$codigosRes})
                      GROUP BY  A.mes";
        $qr_valores = mysql_query($sql_valores) or die("ln 139: " . mysql_error());
        
        while ($rowValorClt = mysql_fetch_assoc($qr_valores)) {
            $row_mov['total'] = 0;
            $mes = intval($rowValorClt['mes']) - 1;
            $folhaClt[$mes][] = $rowValorClt['id_clt'];
            /*
            $idsEstatisticasFolha = "";
            
            $qrEstatisticas = "SELECT       A.id_folha,A.ids_movimentos_estatisticas
                                    FROM    rh_folha AS A
                                    INNER   JOIN rh_folha_proc AS B ON (B.id_folha = A.id_folha)
                                    WHERE   B.id_clt IN ({$ids_clts}) AND A.status = 3 AND A.ano = '{$ano_calendario}' 
                                      AND   A.terceiro = 2 AND B.status = 3 AND B.status_clt NOT IN ({$codigosRes}) 
                                      AND   A.mes = {$rowValorClt['mes']}";
            $rsEstatisticas = mysql_query($qrEstatisticas) or die ("ERRO 357 ".  mysql_error());
            while($rowEstatisticas = mysql_fetch_assoc($rsEstatisticas)){
                $idsEstatisticasFolha .= $rowEstatisticas['ids_movimentos_estatisticas'].",";
            }
            $idsEstatisticasFolha = substr($idsEstatisticasFolha, 0,-1);
            
            if($idsEstatisticasFolha != ""){
                //MOVIMENTOS QUE INCIDEM 
                $slq_mov = "SELECT 
                                SUM( A.valor_movimento ) as total
                                FROM rh_movimentos_clt as A
                                INNER JOIN rh_clt as B
                                ON B.id_clt = A.id_clt
                                WHERE A.id_clt IN ({$ids_clts})
                                AND A.id_mov NOT IN(62)
                                AND A.id_movimento IN({$idsEstatisticasFolha}) 
                                AND A.incidencia like '%5021%' AND A.tipo_movimento != 'DEBITO'";
                $qr_mov = mysql_query($slq_mov) or die("ln 257: " . mysql_error());
                $row_mov = mysql_fetch_assoc($qr_mov);
                
                //NÃO POSSO MAIS ME BASEAR PELO CAMPO sallimpo_real
                //AGORA EXISTEM OS LANÇAMENTOS DE FALTA E ATRAZO
                //VOU BUSCALOS PARA SUBTRAIR DO SALARIO LIMPO REAL
                $slq_movDESC = "SELECT 
                                SUM( A.valor_movimento ) as total
                                FROM rh_movimentos_clt as A
                                INNER JOIN rh_clt as B
                                ON B.id_clt = A.id_clt
                                WHERE A.id_clt IN ({$ids_clts})
                                AND A.id_mov IN({$idsFaltaAtrazo}) 
                                AND A.id_movimento IN({$idsEstatisticasFolha})";
                $qr_movDESC = mysql_query($slq_movDESC) or die("ln 288: " . mysql_error());
                $row_movDESC = mysql_fetch_assoc($qr_movDESC);
                
            }
            $faltas = (!empty($row_movDESC['total'])) ? $row_movDESC['total'] : 0;
            
            $salario = ($rowValorClt['sallimpo_real'] + $row_mov['total']) - $faltas;*/
            
            $salario = $rowValorClt['salbase'];
            
            $LINHA_RTRT[$mes] = number_format($salario, 2, '', '');
            $LINHA_RTPO[$mes] = number_format($rowValorClt['inss'], 2, '', '');
            $LINHA_RTDP[$mes] = (!empty($rowValorClt['a5021'])) ? number_format($rowValorClt['a5049'], 2, '', '') : 0;
            $LINHA_RTIRF[$mes] = number_format($rowValorClt['a5021'], 2, '', '');
        }
        //---------- SALARIO NORMAL ----------
        
        
        //---------- FÉRIAS ----------
        $sql_ferias = "SELECT total_remuneracoes, inss, ir, mes, id_clt
                            FROM rh_ferias 
                            WHERE id_clt IN ({$ids_clts})
                            AND ano = '$ano_calendario' AND status = '1'";
        $qr_ferias = mysql_query($sql_ferias) or die("ln 332: " . mysql_error());

        if (mysql_num_rows($qr_ferias) != 0) {
            while ($row_ferias = mysql_fetch_assoc($qr_ferias)) {
                $mes = intval($row_ferias['mes']) - 1;
                
                //NA LAGOS ENCONTREI UMA PESSOA Q TEM FÉRIAS E SALARIO NO MESMO MES
                //NAO POSSO ZERAR O SALARIO DELA DA OUTRA UNIDADE, PRECISO SOMAR TUDO
                if ($LINHA_RTRT[$mes] > 0) {
                    
                    $LINHA_RTPO[$mes]   += number_format($row_ferias['inss'], 2, '', '');
                    $LINHA_RTIRF[$mes]  += number_format($row_ferias['ir'], 2, '', '');
                    $LINHA_RTRT[$mes]   += number_format($row_ferias['total_remuneracoes'], 2, '', '');
                    
                    /*$LINHA_RTRT[$mes] += number_format($RESC_rendimentos, 2, '', '');
                    $LINHA_RIIRP[$mes] = number_format($RESC_ferias, 2, '', '');
                    $LINHA_RTPO[$mes] += number_format($RESC_inss, 2, '', '');
                    $LINHA_RTIRF[$mes] += number_format($RESC_ir, 2, '', '');*/
                } else {

                    $LINHA_RTPO[$mes]   = number_format($row_ferias['inss'], 2, '', '');
                    $LINHA_RTIRF[$mes]  = number_format($row_ferias['ir'], 2, '', '');
                    $LINHA_RTRT[$mes]   = number_format($row_ferias['total_remuneracoes'], 2, '', '');
                    
                    $LINHA_RIIRP[$mes] = 0;
                    $LINHA_RTPP[$mes] = 0;
                    $LINHA_RTDP[$mes] = 0;
                    $LINHA_RIAP[$mes] = 0;
                    $LINHA_RTPA[$mes] = 0;
                    $LINHA_RIDAC[$mes] = 0;
                }
            }
        }
        //---------- FÉRIAS ----------
        
        if ($debug) echo " inicio 13º ";
        
        echo "<!-- DT ({$ids_clts}) ";
        //---------- DECIMO TERCEIRO ----------
        $qr_valores_dt = mysql_query("SELECT        B.id_clt,
                                                    B.valor_dt,
                                                    B.salliquido,
                                                    B.rend,
                                                    B.inss_dt,
                                                    B.a5049,
                                                    B.ir_dt,
                                                    B.ano,
                                                    A.ids_movimentos_estatisticas
                                                    FROM rh_folha as A
                                                    INNER JOIN rh_folha_proc as B
                                                    ON B.id_folha = A.id_folha
                                                    WHERE B.id_clt IN ({$ids_clts}) 
                                                    AND A.status = 3 AND A.ano = '$ano_calendario'  AND A.terceiro = 1 
                                                    AND B.status = 3 AND A.tipo_terceiro IN ({$whereDecimo})") or die("ln 345: " . mysql_error());
        while ($row_valor_dt = mysql_fetch_assoc($qr_valores_dt)) {
            $idDecimoTerceiro = array();
            //MOVIMENTOS QUE INCIDEM
            //ESTAVA ENTRANDO UM VALOR PEQUENO EM TODOS OS FUNCIONARIOS
            //QUE NÃO ESTAVA SENDO ESPECIFICADO NA FOLHA E NO BANCO
            //ESTA CADASTRADO SEM CÓDIGO, REMOVI DA DIRF ESSES MOVIMENTOS
            $row_mov['total'] = 0;
            if($row_valor_dt['ids_movimentos_estatisticas'] != ""){
                $sqlMov = "SELECT 
                                    SUM( A.valor_movimento ) as total
                                    FROM rh_movimentos_clt as A
                                    INNER JOIN rh_clt as B ON (B.id_clt = A.id_clt)
                                    WHERE A.id_clt = '{$row_valor_dt['id_clt']}' 
                                    AND A.id_mov NOT IN(62)
                                    AND A.id_movimento IN($row_valor_dt[ids_movimentos_estatisticas]) 
                                    AND A.incidencia like '%5021%' AND A.tipo_movimento != 'DEBITO' AND cod_movimento != ''";
                $qr_mov = mysql_query($sqlMov) or die("ln 362: " . mysql_error());
                $row_mov = mysql_fetch_assoc($qr_mov);
            }
            $idDecimoTerceiro[$row_valor_dt['id_clt']] = $row_valor_dt['id_clt'];
            
            echo $row_valor_dt['valor_dt']." - ".$row_mov['total']." | ";
//            exit;
            $valor_decimo = ($row_valor_dt['valor_dt'] + $row_mov['total']);
            $valor_RTRT += $valor_decimo;
            $valor_RTPO += $row_valor_dt['inss_dt'];
            $valor_RTDP += (!empty($row_valor_dt['ir_dt'])) ? $row_valor_dt['a5049'] : '';
            $valor_RTIRF += $row_valor_dt['ir_dt'];
        }
        
        //LINHAS DECIMO
        $DECIMO_RTRT = number_format($valor_RTRT, 2, "", "");
        $DECIMO_RTPO = number_format($valor_RTPO, 2, "", "");
        $DECIMO_RTPP = 0;
        $DECIMO_RTDP = number_format($valor_RTDP, 2, "", "");
        $DECIMO_RTIRF = number_format($valor_RTIRF, 2, "", "");
        $DECIMO_RTPA = 0;
        $DECIMO_RIDAC = 0;
        unset($valor_RTRT, $valor_RTPO, $valor_RTDP, $valor_RTIRF);
        
        //---------- DECIMO TERCEIRO ----------
        
        echo "-->";
        
        if ($debug) echo " recisao ";
        
        //---------- RIIRP - RESCISAO ----------
        $sql_rescisa = "SELECT total_liquido,mes_demissao,id_clt,ferias,dt_salario,
                                IF(total_liquido = 0 , 0, (total_rendimento - ferias) )AS rendimentos,
                                (inss_ferias + previdencia_ss + previdencia_dt) AS total_inss,
                                (inss_ferias + inss_ss + inss_dt) AS total_inss2,
                                (ir_ss + ir_dt + ir_ferias) AS total_ir
                                FROM (
                                        SELECT 
                                        total_liquido,saldo_salario,total_rendimento,dt_salario,
                                        IF(inss_ss IS NULL,0.00,inss_ss) AS inss_ss, 
                                        IF(inss_dt IS NULL,0.00,inss_dt) AS inss_dt,  
                                        IF(inss_ferias IS NULL,0.00,inss_ferias) AS inss_ferias,
                                        IF(previdencia_ss IS NULL,0.00,previdencia_ss) AS previdencia_ss, 
                                        IF(previdencia_dt IS NULL,0.00,previdencia_dt) AS previdencia_dt,
                                        IF(ir_ss IS NULL,0.00,ir_ss) AS ir_ss, 
                                        IF(ir_dt IS NULL,0.00,ir_dt) AS ir_dt,  
                                        IF(ir_ferias IS NULL,0.00,ir_ferias) AS ir_ferias,
                                        (ferias_vencidas+umterco_fv+ferias_pr+umterco_fp+um_terco_ferias_dobro+fv_dobro+ferias_aviso_indenizado) as ferias,
                                        MONTH(data_demi) AS mes_demissao, 
                                        data_demi, 
                                        id_clt
                                        FROM rh_recisao
                                        WHERE id_clt IN ({$ids_clts}) AND YEAR(data_demi) = '{$ano_calendario}' AND STATUS = 1
                                 ) AS temp";
        $qr_rescisao = mysql_query($sql_rescisa) or die("ln 296: " . mysql_error());

        if (mysql_num_rows($qr_rescisao) != 0) {
            
            while($row_rescisao = mysql_fetch_assoc($qr_rescisao)){
                
                $mes = intval($row_rescisao['mes_demissao']) - 1;

                //MESMO CASO QUE ACONTECEU NA TABELA CLT COM O CMAPO DE DATA DE DEMISSAO
                //TEM 2 CAMPOS DE INSS 13 E INSS SALDO DE SALARIO
                //NÃO SEI PQ MAS AS VEZES UM VEM E O OUTRO NÃO
                //E AS VEZES VEM VALOR NOS 2 CAMPOS
                if($row_rescisao['total_inss'] > 0){
                    $inssResci = $row_rescisao['total_inss'];
                }else{
                    $inssResci = $row_rescisao['total_inss2'];
                }

                $RESC_rendimentos    = $row_rescisao['rendimentos'];
                $RESC_ferias         = $row_rescisao['ferias'];
                $RESC_inss           = $inssResci;
                $RESC_ir             = $row_rescisao['total_ir'];
                if($RESC_rendimentos == 0){
                    $RESC_ferias = 0;
                    $RESC_inss = 0;
                    $RESC_ir = 0;
                }
                
                //NA LAGOS ENCONTREI UMA PESSOA Q TEM RESCISAO NO MES 7 EM UMA UNIDADE
                //E TEM SALARIO DE 30 DIAS NO MES 7 EM OUTRA UNIDADE
                //NAO POSSO ZERAR O SALARIO DELA DA OUTRA UNIDADE, PRECISO SOMAR TUDO
                #echo "<br><br><br>CLT: {$row_rescisao['id_clt']} ({$totalCpfs} > 1 E {$LINHA_RTRT[$mes]} > 0 E {$row_rescisao['id_clt']} nao esteja no array: ";
                #print_r($folhaClt[$mes]);
                #echo "<br>se tudo for verdade, ele soma os valores de salario com os valores de rescisao";
                if ($totalCpfs > 1 && $LINHA_RTRT[$mes] > 0 && !in_array($row_rescisao['id_clt'], $folhaClt[$mes])) {
                    #echo "<!-- <br><br><br>CLT: {$row_rescisao['id_clt']} - entro pra somar RTRT({$LINHA_RTRT[$mes]} + {$RESC_rendimentos}) -->";
                    $LINHA_RTRT[$mes] += number_format($RESC_rendimentos, 2, '', '');
                    $LINHA_RIIRP[$mes] = number_format($RESC_ferias, 2, '', '');
                    $LINHA_RTPO[$mes] += number_format($RESC_inss, 2, '', '');
                    $LINHA_RTIRF[$mes] += number_format($RESC_ir, 2, '', '');
                    
                }else{
                
                    $LINHA_RTRT[$mes]   = number_format($RESC_rendimentos, 2, '', '');
                    $LINHA_RIIRP[$mes]  = number_format($RESC_ferias, 2, '', '');
                    $LINHA_RTPO[$mes]   = number_format($RESC_inss, 2, '', '');
                    $LINHA_RTIRF[$mes]  = number_format($RESC_ir, 2, '', '');

                    $LINHA_RTPP[$mes] = 0;
                    $LINHA_RTDP[$mes] = 0;
                    $LINHA_RIAP[$mes] = 0;
                    $LINHA_RTPA[$mes] = 0;
                    $LINHA_RIDAC[$mes] = 0;
                    
                }
                //CASO TENHA VALOR NO 13 VAI ZERAR A COLUNA 13 TODA
                //POIS OS VALORES DE 13 FOI PROCESSADO NA RESCISAO
                //PRECISO VERIFICAR TAMBEM SE A RESCISÃO É DO MESMO ID DO CLT DO 13
                //POIS EXISTE CASO DA PESSOA SAIR DE UMA UPA E ENTRAR NA OUTRA
                //ASSIM ELA VAI TER DECIMO TERCEIRO SENDO DE OUTRO ID_CLT
                if($row_rescisao['dt_salario'] > 0 && in_array($row_rescisao['id_clt'],$idDecimoTerceiro)){
                    $DECIMO_RTRT = NULL;
                    $DECIMO_RIIRP = NULL;
                    $DECIMO_RTPO = NULL;
                    $DECIMO_RTPP = NULL;
                    $DECIMO_RTDP = NULL;
                    $DECIMO_RTIRF = NULL;
                    $DECIMO_RTPA = NULL;
                    $DECIMO_RIDAC = NULL;
                }
                
            }
        }
        //---------- RIIRP - RESCISAO ----------
        
        //---------- RIAP - ABONO PECUNIÁRIO ----------
        if ($debug)
            echo " inicio RIAP ";
        $sqlRiap = "SELECT mes, REPLACE(abono_pecuniario, '.','') as abono_pecuniario
                                                                    FROM rh_ferias 
                                                                    WHERE id_clt IN ({$ids_clts}) AND ano = '$ano_calendario'";
        $qr_ferias = mysql_query($sqlRiap) or die("ln 408: " . mysql_error());

        $row_ferias = mysql_fetch_assoc($qr_ferias);
        if (mysql_num_rows($qr_ferias) != 0) {
            $chave = $row_ferias['mes'] - 1;
            $LINHA_RIAP[$chave] = ($row_ferias['abono_pecuniario'] != '0.00' ) ? $row_ferias['abono_pecuniario'] : '';
            $DECIMO_RIAP = '';
        }
        
        
        if ($debug) echo " inicio RTPA ";
        
        //---------- RTPA RENDIMENTOS TRIB. DEDUCAO - PENSÃO ALIMENTICIA ----------
        $sql_pensao = "SELECT valor_movimento,mes_mov
                        FROM rh_movimentos_clt 
                        WHERE cod_movimento IN ('6004','7009') AND id_clt IN ({$ids_clts}) 
                        AND ano_mov = '{$ano_calendario}' 
                        AND id_movimento IN ($ids_movimentos_folhas)";
        $qr_pensao = mysql_query($sql_pensao) or die("ln 424: $sql_pensao <br>" . mysql_error());

        if (mysql_num_rows($qr_pensao) > 0) {
            $valor_pensao = null;
            $valor_pensaoD = null;
            while ($row_pensao = mysql_fetch_assoc($qr_pensao)) {

                if ($row_pensao['mes_mov'] < 13) {
                    $valor_pensao[$row_pensao['mes_mov'] - 1] += $row_pensao['valor_movimento'];
                } else {
                    $valor_pensaoD += $row_pensao['valor_movimento'];
                }
            }

            //NORMALIZAR O VALOR, TIRAR PONTO E VIRGULA PRA GAVAR NO TXT
            foreach ($valor_pensao as $mes => $val) {
                $LINHA_RTPA[$mes] = number_format($val, 2, "", "");
            }

            $DECIMO_RTPA = (!empty($valor_pensaoD)) ? number_format($valor_pensaoD, 2, "", "") : NULL;
        }

        if ($debug) echo " inicio RIDAC ";
        
        
        //---------- RIDAC Rendimentos Isentos - Diária e Ajuda de Custo (parcela única não incide) ----------
        $sql_ajuda = "SELECT valor_movimento,mes_mov
                        FROM rh_movimentos_clt 
                        WHERE cod_movimento = '50111' AND id_clt IN ({$ids_clts}) 
                        AND ano_mov = '{$ano_calendario}' 
                        AND id_movimento IN ($ids_movimentos_folhas)";
        $qr_ajuda = mysql_query($sql_ajuda) or die("ln 454: " . mysql_error());

        if (mysql_num_rows($qr_ajuda) > 0) {
            $valor_ajuda = null;
            $valor_ajudaD = null;
            while ($row_ajuda = mysql_fetch_assoc($qr_ajuda)) {
                if ($row_ajuda['mes_mov'] < 13) {
                    $valor_ajuda[$row_ajuda['mes_mov'] - 1] += $row_ajuda['valor_movimento'];
                } else {
                    $valor_ajudaD += $row_ajuda['valor_movimento'];
                }
            }

            foreach ($valor_ajuda as $mes => $val) {
                $LINHA_RIDAC[$mes] = number_format($val, 2, "", "");
            }

            $DECIMO_RIDAC = (!empty($valor_ajudaD)) ? number_format($valor_ajudaD, 2, "", "") : NULL;
        }

        if ($debug) echo " inicio GRAVANDO ";

        //MATANDO DEZEMBRO NA MÃO, POIS NÃO TEVE FOLHA ANO PASSADO (SOMENTE UTILIZADO NO ZICO)
        if (!$folhaDezembro) {
            $LINHA_RTRT[11] = "0";
            $LINHA_RTPO[11] = "0";
            $LINHA_RTPP[11] = "0";
            $LINHA_RTDP[11] = "0";
            $LINHA_RTIRF[11] = "0";
            $LINHA_RIIRP[11] = "0";
            $LINHA_RIAP[11] = "0";
            $LINHA_RTPA[11] = "0";
            $LINHA_RIDAC[11] = "0";
        }
        
        //NORMALIZA COLOCANDO ZERO NAS CHAVES QUE NÃO EXISTEM PARA ESCREVER NO TXT
        for ($i = 0; $i <= 11; $i++) {
            if (!array_key_exists($i, $LINHA_RTRT)) {
                $LINHA_RTRT[$i] = 0;
            }
            if (!array_key_exists($i, $LINHA_RTPO)) {
                $LINHA_RTPO[$i] = 0;
            }
            if (!array_key_exists($i, $LINHA_RTPP)) {
                $LINHA_RTPP[$i] = 0;
            }
            if (!array_key_exists($i, $LINHA_RTDP)) {
                $LINHA_RTDP[$i] = 0;
            }
            if (!array_key_exists($i, $LINHA_RTIRF)) {
                $LINHA_RTIRF[$i] = 0;
            }
            if (!array_key_exists($i, $LINHA_RIIRP)) {
                $LINHA_RIIRP[$i] = 0;
            }
            if (!array_key_exists($i, $LINHA_RIAP)) {
                $LINHA_RIAP[$i] = 0;
            }
            if (!array_key_exists($i, $LINHA_RTPA)) {
                $LINHA_RTPA[$i] = 0;
            }
            if (!array_key_exists($i, $LINHA_RIDAC)) {
                $LINHA_RIDAC[$i] = 0;
            }
        }
        
        //ORDENANDO O ARRAY PARA ESCREVER NO TXT
        ksort($LINHA_RTRT);
        ksort($LINHA_RTPO);
        ksort($LINHA_RTPP);
        ksort($LINHA_RTDP);
        ksort($LINHA_RTIRF);
        ksort($LINHA_RIIRP);
        ksort($LINHA_RIAP);
        ksort($LINHA_RTPA);
        ksort($LINHA_RIDAC);
        
        //VERIFICANDO QUAIS LINHAS TEM VALORES, PARA NÃO IMPRIMIR NO TXT AQUELAS Q NÃO TEM NADA
        $exibir_rtrt = validaValor($LINHA_RTRT);
        $exibir_rtpo = validaValor($LINHA_RTPO);
        $exibir_rtpp = validaValor($LINHA_RTPP);
        $exibir_rtdp = validaValor($LINHA_RTDP);
        $exibir_rtirf = validaValor($LINHA_RTIRF);
        $exibir_riirp = validaValor($LINHA_RIIRP);
        $exibir_riap = validaValor($LINHA_RIAP);
        $exibir_rtpa = validaValor($LINHA_RTPA);
        $exibir_ridac = validaValor($LINHA_RIDAC);
        
        //CRIANDO ARRAY PARA VISUALIZAÇÃO NA TELA
        if ($geraRelatorio) {
            $linhaRel[$clt['id_clt']]['tipo'] = 1;
            $linhaRel[$clt['id_clt']]['nome'] = $clt['nome'];
            $linhaRel[$clt['id_clt']]['id_projeto'] = $clt['id_projeto'];
            $linhaRel[$clt['id_clt']]['cpf'] = $clt['cpf'];
            $linhaRel[$clt['id_clt']]['RTRT'] = $LINHA_RTRT;
            $linhaRel[$clt['id_clt']]['RTRTD'] = $DECIMO_RTRT;

            $linhaRel[$clt['id_clt']]['RTPO'] = $LINHA_RTPO;
            $linhaRel[$clt['id_clt']]['RTPOD'] = $DECIMO_RTPO;

            $linhaRel[$clt['id_clt']]['RTPP'] = $LINHA_RTPP;
            $linhaRel[$clt['id_clt']]['RTPPD'] = $DECIMO_RTPP;

            $linhaRel[$clt['id_clt']]['RTDP'] = $LINHA_RTDP;
            $linhaRel[$clt['id_clt']]['RTDPD'] = $DECIMO_RTDP;

            $linhaRel[$clt['id_clt']]['RTIRF'] = $LINHA_RTIRF;
            $linhaRel[$clt['id_clt']]['RTIRFD'] = $DECIMO_RTIRF;

            $linhaRel[$clt['id_clt']]['RIIRP'] = $LINHA_RIIRP;
            $linhaRel[$clt['id_clt']]['RIIRPD'] = $DECIMO_RIIRP;

            $linhaRel[$clt['id_clt']]['RIAP'] = $LINHA_RIAP;
            $linhaRel[$clt['id_clt']]['RIAPD'] = $DECIMO_RIAP;

            $linhaRel[$clt['id_clt']]['RTPA'] = $LINHA_RTPA;
            $linhaRel[$clt['id_clt']]['RTPAD'] = $DECIMO_RTPA;

            $linhaRel[$clt['id_clt']]['RIDAC'] = $LINHA_RIDAC;
            $linhaRel[$clt['id_clt']]['RIDACD'] = $DECIMO_RIDAC;
        }
        
        //ESCREVENDO AS LINHAS DE CADA CPF NO TXT
        if ($exibir_rtrt or (!empty($DECIMO_RTRT) && $DECIMO_RTRT != "000")) {
            $LINHA_RTRT = "RTRT|".implode('|', $LINHA_RTRT) . '|' . $DECIMO_RTRT . '|';
            //echo "RTRT: ".$LINHA_RTRT."<br>";
            fwrite($arquivo, $LINHA_RTRT);
            fwrite($arquivo, "\n");
        }

        if ($exibir_rtpo or (!empty($DECIMO_RTPO) && $DECIMO_RTPO != "000")) {
            $LINHA_RTPO = "RTPO|".implode('|', $LINHA_RTPO) . '|' . $DECIMO_RTPO . '|';
            //echo "RTPO: ".$LINHA_RTPO."<br>";
            fwrite($arquivo, $LINHA_RTPO);
            fwrite($arquivo, "\n");
        }

        if ($exibir_rtpp or (!empty($DECIMO_RTPP) && $DECIMO_RTPP != "000")) {
            $LINHA_RTPP = "RTPP|".implode('|', $LINHA_RTPP) . '|' . $DECIMO_RTPP . '|';
            //echo "RTPP: ".$LINHA_RTPP."<br>";
            fwrite($arquivo, $LINHA_RTPP);
            fwrite($arquivo, "\n");
        }

        if ($exibir_rtdp or (!empty($DECIMO_RTDP) && $DECIMO_RTDP != "000")) {
            $LINHA_RTDP = "RTDP|".implode('|', $LINHA_RTDP) . '|' . $DECIMO_RTDP . '|';
            //echo "RTDP: ".$LINHA_RTDP."<br>";
            fwrite($arquivo, $LINHA_RTDP);
            fwrite($arquivo, "\n");
        }

        if ($exibir_rtirf or (!empty($DECIMO_RTIRF) && $DECIMO_RTIRF != "000")) {
            $LINHA_RTIRF = "RTIRF|".implode('|', $LINHA_RTIRF) . '|' . $DECIMO_RTIRF . '|';
            //echo "RTIRF: ".$LINHA_RTIRF."<br>";
            fwrite($arquivo, $LINHA_RTIRF);
            fwrite($arquivo, "\n");
        }

        if ($exibir_riirp or (!empty($DECIMO_RIIRP) && $DECIMO_RIIRP != "000")) {
            $LINHA_RIIRP = "RIIRP|".implode('|', $LINHA_RIIRP) . '|' . $DECIMO_RIIRP . '|';
            //echo "RIIRP: ".$LINHA_RIIRP."<br>";
            fwrite($arquivo, $LINHA_RIIRP);
            fwrite($arquivo, "\n");
        }

        if ($exibir_riap or (!empty($DECIMO_RIAP) && $DECIMO_RIAP != "000")) {
            $LINHA_RIAP = "RIAP|".implode('|', $LINHA_RIAP) . '|' . $DECIMO_RIAP . '|';
            //echo "RIAP: ".$LINHA_RIAP."<br>";
            fwrite($arquivo, $LINHA_RIAP);
            fwrite($arquivo, "\n");
        }

        if ($exibir_rtpa or (!empty($DECIMO_RTPA) && $DECIMO_RTPA != "000")) {
            $LINHA_RTPA = "RTPA|".implode('|', $LINHA_RTPA) . '|' . $DECIMO_RTPA . '|';
            //echo "RIAP: ".$LINHA_RIAP."<br>";
            fwrite($arquivo, $LINHA_RTPA);
            fwrite($arquivo, "\n");
        }

        if ($exibir_ridac or (!empty($DECIMO_RIDAC) && $DECIMO_RIDAC != "000")) {
            $LINHA_RIDAC = "RIDAC|".implode('|', $LINHA_RIDAC) . '|' . $DECIMO_RIDAC . '|';
            //echo "RIAP: ".$LINHA_RIAP."<br>";
            fwrite($arquivo, $LINHA_RIDAC);
            fwrite($arquivo, "\n");
        }

        //LIMPANDO AS VARIAVEIS PRINCIPALMENTE OS ARRAYS, PARA NÃO ESTOURAR A MEMÓRIA DO SERVIDOR
        unset($LINHA_RTRT, $DECIMO_RTRT, $LINHA_RTPO, $DECIMO_RTPO, $LINHA_RTPP, $DECIMO_RTPP, $LINHA_RTDP, $DECIMO_RTDP, $LINHA_RTIRF, $DECIMO_RTIRF, $exibir_rtrt, $exibir_rtpo, $exibir_rtpp, $exibir_rtdp, $exibir_rtirf, $LINHA_RIIRP, $DECIMO_RIIRP, $exibir_riirp, $chave);
        unset($LINHA_RIAP, $DECIMO_RIAP, $exibir_riap, $LINHA_RTPA, $DECIMO_RTPA, $LINHA_RIDAC, $DECIMO_RIDAC, $folhaClt);
        if ($debug)
            echo "fIM: ($c)<br/>";
        $c++;
    }

    
      //////////////////////////////////////////////////////////
      /////////// REGISTROS DE RPA/AUTONOMO  ///////////////////
      //////////////////////////////////////////////////////////

      if($debug) echo " inicio RPA ";
      $qr_autonomo = "SELECT
                            A.id_rpa,A.id_autonomo,A.data_geracao,A.valor,A.valor_inss,A.valor_ir,A.valor_liquido,DATE_FORMAT(A.data_geracao, '%m') as mes,
                            B.nome,B.cpf
                            FROM rpa_autonomo AS A
                            INNER JOIN autonomo AS B ON (A.id_autonomo = B.id_autonomo)
                            WHERE YEAR( A.data_geracao ) = {$ano_calendario} AND B.id_projeto IN (" . implode(",", $projetos) . ")
                            ORDER BY B.cpf ASC, A.data_geracao DESC";
      $rs_autonomo = mysql_query($qr_autonomo) or die(mysql_error());
      $linha_aut = array();
      $a = 0;
      //echo mysql_num_rows($rs_autonomo);
      while ($row_aut = mysql_fetch_assoc($rs_autonomo)) {

        $mes = $row_aut['mes'];
        $cpf = preg_replace('/[^[:digit:]]/', '', $row_aut['cpf']);
        //echo " autono ({$row_aut['id_autonomo']} - $a) <br/> ";
        $auto[$row_aut['id_autonomo']]['nome'] = $row_aut['nome'];
        $auto[$row_aut['id_autonomo']]['cpf'] = sprintf("%011s", $cpf);

        for ($i = 0; $i < 12; $i++) {
            $linha_aut[$row_aut['id_autonomo']]['RTRT'][$i] = NULL;
            $linha_aut[$row_aut['id_autonomo']]['RTPO'][$i] = NULL;
            $linha_aut[$row_aut['id_autonomo']]['RTIRF'][$i] = NULL;
        }

        $linha_aut[$row_aut['id_autonomo']]['RTRT'][$mes - 1] += $row_aut['valor'];
        $linha_aut[$row_aut['id_autonomo']]['RTPO'][$mes - 1] += $row_aut['valor_inss'];
        $linha_aut[$row_aut['id_autonomo']]['RTIRF'][$mes - 1] += $row_aut['valor_ir'];

        //NORMALIZANDO A PONTUAÇÃO
        for ($i = 0; $i < 12; $i++) {
            $linha_aut[$row_aut['id_autonomo']]['RTRT'][$i] = number_format($linha_aut[$row_aut['id_autonomo']]['RTRT'][$i], 2, "", "");
            $linha_aut[$row_aut['id_autonomo']]['RTPO'][$i] = number_format($linha_aut[$row_aut['id_autonomo']]['RTPO'][$i], 2, "", "");
            $linha_aut[$row_aut['id_autonomo']]['RTIRF'][$i] = number_format($linha_aut[$row_aut['id_autonomo']]['RTIRF'][$i], 2, "", "");
        }
        $a ++;
    }

    unset($exibir_rtrt, $exibir_rtpo, $exibir_rtirf);
    ///////////////////////////// //////////////////////////////////////
    /////IDENTIFICAÇÃO DE CÓDIGO DA RECEITA (IDENTIFICADOR IDREC) /////
    ///////////////////////////// //////////////////////////////////////
    $IDENTIFICADOR_IDREC['ID_REGISTRO'] = 'IDREC';
    $IDENTIFICADOR_IDREC['CODIGO_RECEITA'] = '0588';

    $LINHA_IDREC = implode('|', $IDENTIFICADOR_IDREC) . '|';
    fwrite($arquivo, $LINHA_IDREC);
    fwrite($arquivo, "\n");

    foreach ($auto as $id_autonomo => $autonomo) {
        if ($geraRelatorio) {
            $linhaRel[$id_autonomo]['tipo'] = 2;
            $linhaRel[$id_autonomo]['nome'] = $autonomo['nome'];
            $linhaRel[$id_autonomo]['RTRT'] = $linha_aut[$id_autonomo]['RTRT'];
            $linhaRel[$id_autonomo]['RTRTD'] = NULL;

            $linhaRel[$id_autonomo]['RTPO'] = $linha_aut[$id_autonomo]['RTPO'];
            $linhaRel[$id_autonomo]['RTPOD'] = NULL;

            $linhaRel[$id_autonomo]['RTPP'] = NULL;
            $linhaRel[$id_autonomo]['RTPPD'] = NULL;

            $linhaRel[$id_autonomo]['RTDP'] = NULL;
            $linhaRel[$id_autonomo]['RTDPD'] = NULL;

            $linhaRel[$id_autonomo]['RTIRF'] = $linha_aut[$id_autonomo]['RTIRF'];
            $linhaRel[$id_autonomo]['RTIRFD'] = NULL;

            $linhaRel[$id_autonomo]['RIIRP'] = NULL;
            $linhaRel[$id_autonomo]['RIIRPD'] = NULL;

            $linhaRel[$id_autonomo]['RIAP'] = NULL;
            $linhaRel[$id_autonomo]['RIAPD'] = NULL;

            $linhaRel[$id_autonomo]['RTPA'] = NULL;
            $linhaRel[$id_autonomo]['RIAPD'] = NULL;

            $linhaRel[$id_autonomo]['RIDAC'] = NULL;
            $linhaRel[$id_autonomo]['RIDACD'] = NULL;
        }

        //GRAVANDO NO TXT O AUTONOMO
        $cpf = trim($autonomo['cpf']);
        $IDENTIFICADOR_BPFDEC['ID_REGISTRO'] = 'BPFDEC';
        $IDENTIFICADOR_BPFDEC['CNPJ'] = sprintf("%011s", $cpf);
        $IDENTIFICADOR_BPFDEC['NOME_EMPRESA'] = trim($autonomo['nome']);
        $IDENTIFICADOR_BPFDEC['DATA_ATRIBUIDA'] = '';
        $LINHA_BPFDEC = implode('|', $IDENTIFICADOR_BPFDEC) . '|';
        fwrite($arquivo, $LINHA_BPFDEC);
        fwrite($arquivo, "\n");

        $LINHA_RTRT['IDENTIFICADOR'] = 'RTRT'; //RENDIMENTOS
        $LINHA_RTPO['IDENTIFICADOR'] = 'RTPO'; //PREVIDÊNCIA OFICIAL
        $LINHA_RTPP['IDENTIFICADOR'] = 'RTPP'; //PREVIDÊNCIA PRIVADA
        $LINHA_RTDP['IDENTIFICADOR'] = 'RTDP'; //DEPENDENTES
        $LINHA_RTIRF['IDENTIFICADOR'] = 'RTIRF'; //IRRF
        $LINHA_RIIRP['IDENTIFICADOR'] = 'RIIRP'; //RESCISAO
        $LINHA_RIAP['IDENTIFICADOR'] = 'RIAP'; //ABONO PECUNIÁRIO
        //$LINHA_RTRT[] = $linha_aut[$id_autonomo]['RTRT'];
        //////GRAVANDO VALORES MENSAIS NO TXT
        for ($i = 0; $i < 12; $i++) {
            $exibir_rtrt += ($linha_aut[$id_autonomo]['RTRT'][$i] != "000") ? 1 : 0;
            $exibir_rtpo += ($linha_aut[$id_autonomo]['RTPO'][$i] != "000") ? 1 : 0;
            $exibir_rtirf += ($linha_aut[$id_autonomo]['RTIRF'][$i] != "000") ? 1 : 0;
        }

        if (!empty($exibir_rtrt)) {
            $txRTRT = implode('|', $LINHA_RTRT) . "|";
            $txRTRT .= implode('|', $linha_aut[$id_autonomo]['RTRT']);
            $txRTRT .= "||";
            fwrite($arquivo, $txRTRT);
            fwrite($arquivo, "\n");
        }

        if (!empty($exibir_rtpo)) {
            $txRTPO = implode('|', $LINHA_RTPO) . "|";
            $txRTPO .= implode('|', $linha_aut[$id_autonomo]['RTPO']);
            $txRTPO .= "||";
            fwrite($arquivo, $txRTPO);
            fwrite($arquivo, "\n");
        }

        if (!empty($exibir_rtirf)) {
            $txRTIRF = implode('|', $LINHA_RTIRF) . "|";
            $txRTIRF .= implode('|', $linha_aut[$id_autonomo]['RTIRF']);
            $txRTIRF .= "||";
            fwrite($arquivo, $txRTIRF);
            fwrite($arquivo, "\n");
        }

        unset($exibir_rtrt, $exibir_rtpo, $exibir_rtirf, $txRTIRF, $txRTPO, $txRTRT, $LINHA_RTIRF, $LINHA_RTPO, $LINHA_RTRT);
    }

    fwrite($arquivo, "FIMDirf|");
    fclose($arquivo);
    if ($debug)
        echo " fim dirf ";
}
?>
<html>
    <head>
        <title>Gerar DIRF</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../../../net1.css" rel="stylesheet" type="text/css">
        <script src="../../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="../../../jquery/jquery.tools.min.js" type="text/javascript" ></script>
        <script src="../../../js/global.js" type="text/javascript" ></script>
    </head>
    <body class="novaintra">       
        <form  name="form" action="" method="post" id="form">
            <div id="content">
                <div id="head">
                    <img src="../../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
                    <div class="fleft">
                        <h2>DIRF</h2>
                        <p>Gerar arquivo de DIRF</p>
                    </div>
                </div>
                <br class="clear">

                <fieldset>
                    <legend>DIRF</legend>
                    <p><label class="first">Ano Base:</label> <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano')); ?></p>
                    <p><label class="first">CNPJ Principal:</label> <?php echo $rowMaster['cnpj'] . " - " . $rowMaster['razao'] ?></p>
                    <p>
                        <label class="first">Tipo Arquivo:</label>
                        <input type="radio" name="tipo" for="tipoN" value="N" <?php echo $tipoSelN ?> /><span id="tipoN">Arquivo Original</span>
                        <input type="radio" name="tipo" for="tipoS" value="S" <?php echo $tipoSelS ?> /><span id="tipoS">Arquivo Retificador</span>
                    </p>
                    <p><label class="first">Projeto:</label>  <?php echo montaSelect($proNomes, $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?></p>
                    
                    <p class="controls clear">
                        <input type="submit" name="filtrar" value="Filtrar" id="filtrar"/>
                    </p>
                </fieldset>
                <br/>
                <?php if ($relatorio && $geraRelatorio) { ?>
                    <?php if ($limitado != false) {
                        echo $limitado;
                    } ?>
                    <table cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">
                        <tbody>
                            <tr>
                                <td>RTRT</td>
                                <td>Rendimentos Tributáveis - Rendimento Tributável</td>
                                <td>RTIRF</td>
                                <td>Rendimentos Tributáveis - Imposto sobre a Renda Retido na Fonte</td>
                            </tr>
                            <tr>
                                <td>RTPO</td>
                                <td>Rendimentos Tributáveis - Dedução - Previdência Oficial</td>
                                <td>RIIRP</td>
                                <td>Rendimentos Isentos - Indenizações por Rescisão de Contrato de Trabalho e/ou PDV</td>
                            </tr>
                            <tr>
                                <td>RTPP</td>
                                <td>Rendimentos Tributáveis - Dedução - Previdência Privada</td>
                                <td>RIAP</td>
                                <td>Rendimentos Isentos - Abono Pecuniário</td>
                            </tr>
                            <tr>
                                <td>RTDP</td>
                                <td>Rendimentos Tributáveis - Dedução - Dependentes</td>
                                <td>RIDAC</td>
                                <td>Rendimentos Isentos - Diária e Ajuda de Custo (parcela única)</td>
                            </tr>
                            <tr>
                                <td>RTPA</td>
                                <td>RENDIMENTOS TRIB. - PENSÃO ALIMENTICIA</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                    <table cellpadding="0" cellspacing="0" border="0" class="grid" width="100%">
                        <thead>
                            <tr>
                                <th colspan="14">Relatório DIRF <?php echo $ano_calendario ?></th>
                            </tr>
                            <tr>
                                <th>Tipo</th>
                                <th>Jan</th>
                                <th>Fev</th>
                                <th>Mar</th>
                                <th>Abr</th>
                                <th>Mai</th>
                                <th>Jun</th>
                                <th>Jul</th>
                                <th>Ago</th>
                                <th>Set</th>
                                <th>Out</th>
                                <th>Nov</th>
                                <th>Dez</th>
                                <th>13</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $nomeAntes = "";
                            foreach ($linhaRel as $k => $clt) {
                                if ($nomeAntes != $clt['nome']) {
                                    echo "<tr class='titulo'><td colspan='14' class='txleft'>{$k} - {$clt['nome']} ({$proNomes[$clt['id_projeto']]}) {$clt['cpf']}</td></tr>";
                                }

                                echo "<tr><td>RTRT</td>";
                                printaValor($clt['RTRT']);
                                echo "<td>" . formatoNojo($clt['RTRTD']) . "</td></tr>";

                                echo "<tr><td>RTPO</td>";
                                printaValor($clt['RTPO']);
                                echo "<td>" . formatoNojo($clt['RTPOD']) . "</td></tr>";

                                if ($clt['tipo'] == 1) {
                                    echo "<tr><td>RTDP</td>";
                                    printaValor($clt['RTDP']);
                                    echo "<td>" . formatoNojo($clt['RTDPD']) . "</td></tr>";
                                }

                                echo "<tr><td>RTIRF</td>";
                                printaValor($clt['RTIRF']);
                                echo "<td>" . formatoNojo($clt['RTIRFD']) . "</td></tr>";

                                if ($clt['tipo'] == 1) {
                                    echo "<tr><td>RIIRP</td>";
                                    printaValor($clt['RIIRP']);
                                    echo "<td>" . formatoNojo($clt['RIIRPD']) . "</td></tr>";
                                }

                                if ($clt['tipo'] == 1) {
                                    echo "<tr><td>RIAP</td>";
                                    printaValor($clt['RIAP']);
                                    echo "<td>" . formatoNojo($clt['RIAPD']) . "</td></tr>";
                                }

                                if ($clt['tipo'] == 1) {
                                    echo "<tr><td>RTPA</td>";
                                    printaValor($clt['RTPA']);
                                    echo "<td>" . formatoNojo($clt['RTPAD']) . "</td></tr>";
                                }

                                if ($clt['tipo'] == 1) {
                                    echo "<tr><td>RIDAC</td>";
                                    printaValor($clt['RIDAC']);
                                    echo "<td>" . formatoNojo($clt['RIDACD']) . "</td></tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <p class="controls">
                        <a href="<?php echo $nome_arquivo ?>"> Download do arquivo<?php echo $nomeFile ?></a>
                    </p>
                    <?php }else if ($relatorio && !$geraRelatorio) { ?>
                    <p class="controls">
                        <a href="<?php echo $nome_arquivo ?>"> Download do arquivo<?php echo $nomeFile ?></a>
                    </p>
                <?php } ?>
            </div>
        </form>
    </body>
</html>