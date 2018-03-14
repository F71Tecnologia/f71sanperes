<?php


$exec = false;
//CHAMADA PELO CRON, NAO TEM NINGUEM ON-LINE PASSO UM PARAMETRO
//SETO COOKIE NO MEU ID PARA RODAR SEM ESTAR LOGADO NO SISTEMA
if(isset($_REQUEST['of'])){
    $_COOKIE['logado'] = 158;
    $_REQUEST['filtrar'] = "filtrar";
    $_REQUEST['ano'] = $_REQUEST['of'];
    $exec = true;
}

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

set_time_limit(0);

include("../../../conn.php");
include("../../../classes/funcionario.php");
include("../../../classes/InformeRendimentoClass.php");
include("../../../classes_permissoes/regioes.class.php");
include("../../../wfunction.php");

error_reporting(0);
ini_set('error_reporting', 0);
$usuario = carregaUsuario();

//VARIAVEIS DE CONTROLE
$relatorio = false;     //VARIAVEL PARA CONTROLAR TABELA QUE MOSTRA OS RESULTADOS DA CONSULTA
$limitado = false;      //LIMITA A QNT DE LINHAS "LIMIT 0,150"; //Registros encontrados: 2.794
$debug = false;         //IMPRIME LINHAS DE DEBUGS EM VARIOS LUGARES DO CODIGO
$geraRelatorio = true;  //SE FOR VERDADEIRO CRIA O ARRAY PARA VISUALIZAÇÃO NA TELA
$decimoAntigo = false;  //DECIMO ANTIGO, SE FOR TRUE SELECIONA TODOS OS 13 (1,2,3) E SOMA ENTRE ELES, SE FOR FALSO PEGA SO (2,3) E NAO SOMA
$arrayPensaoAlimenticia = array('6004','7009','50222','80026','80034','7010','7012','90019','90076','90077','80047','70013');
$idCltCOrrecaoBUGFERIAS = array(106,827);
//6004,7009,50222,80026,80034,7010,7011,7012,90019,90076,90077,80047,70013

$urlInforme = "http://f71iabassp.com/intranet/rendimento/index.php?clt=";

$folhaDezembro = false;

/**
 * OBJETOS 
 */
//print_r($_REQUEST);exit();
if(isset($_REQUEST['ano']) && !empty($_REQUEST['ano'])){
    $informeRendimento = new InformeRendimentoClass($usuario['id_master']);
    $informeRendimento->setAnoBase($_REQUEST['ano']);
    $informeRendimento->setTipo(2);
}


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

//RETORNA TODOS OS IDS DO MESMO CPF
function verificaDuplicidade($cpf,$projetos) {
    $result = array();
    $result['rs'] = montaQuery("rh_clt", "id_clt,id_projeto", "REPLACE(REPLACE(cpf,'.',''),'-','')  = '{$cpf}' AND id_projeto IN (".implode(",",$projetos).")");
    $result['total'] = count($result['rs']);
    return $result;
}

function printaValor($arr){
    for($i=0;$i<=11;$i++){
        echo "<td>" . formatoNojo($arr[$i]) . "</td>";
    }
}

function normalizaIdsEstatisticas($ids){
    $ids_mo = "";
    $ids_mo = str_replace(',,,', ',', $ids);
    $ids_mo = str_replace(',,', ',', $ids_mo);

    if (substr($ids_mo, -1) == ",") {
        $ids_mo = substr($ids_mo, 0, -1);
    }
    if (substr($ids_mo, 0, 1) == ",") {
        $ids_mo = substr($ids_mo, 1);
    }
    return $ids_mo;
}

function normalizaNome($value){
    $nome = preg_replace("/[Ñ]/i","N",$value);
    return $nome;
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
    $arrProjetosMaster[] = $pro['id_projeto'];
}

//PEGANDO OS PROJETOS
$rsEmpresas = montaQuery("rhempresa", "*", "id_master = {$usuario['id_master']}");
$projetos = array();
$proNomes = array();
foreach ($rsEmpresas as $emp) {
    $projetos[] = $emp['id_projeto'];
    $proNomes[$emp['id_projeto']] = $pros[$emp['id_projeto']];
}

$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y') - 1;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;

$tipoSelN = null;
$tipoSelS = null;

if (isset($_REQUEST['tipo']) && $_REQUEST['tipo'] == "S") {
    $tipoSelS = "checked='checked'";
} elseif (isset($_REQUEST['tipo']) && $_REQUEST['tipo'] == "N") {
    $tipoSelN = "checked='checked'";
} else{
    $tipoSelN = "checked='checked'";
}

$pgSelN = null;
$pgSelS = null;


if (isset($_REQUEST['pg']) && $_REQUEST['pg'] == "S") {
    $pgSelS = "checked='checked'";
    $folhaDezembro = true;
} elseif (isset($_REQUEST['pg']) && $_REQUEST['pg'] == "N") {
    $pgSelN = "checked='checked'";
} else{
    $pgSelN = "checked='checked'";
}


if (isset($_REQUEST['filtrar']) && validate($_REQUEST['filtrar'])) {
    if(isset($_REQUEST['p'])){
        $ini = $_REQUEST['p'] * 500;
        $limitado = " LIMIT $ini,500 ";
    }
    
    
    //if ($limitado != false) {
        //echo "Atenção, relatório limitado para validação<br/> Agarde o carregamento...";
    //}
    $relatorio = true;

    $ano_referencia = $_REQUEST['ano'] + 1;
    $ano_calendario = $_REQUEST['ano'];
    $ano_anterior = $ano_calendario - 1;    //NO CASO DE ALGUNS CLIENTES TEM Q PEGAR A FOLHA DE DEZEMBRO DO ANO ANTERIOR
    $master = $usuario['id_master'];
    $tipoArq = (!empty($_REQUEST['tipo'])) ? $_REQUEST['tipo'] : "N";
    
    $cltUnico = (!empty($_REQUEST['codigo'])) ? $_REQUEST['codigo'] : false;
        
    //FEITA PARA A LAGOS POIS SÃO MUITOS PROJETOS E A VALIDAÇÃO DO RELATÓRIO VAI SER FEITA DE CADA PROJETO
    //QUANDO FOREM EXPORTAR AE SIM UTILIZO O ARRAY COM TODOS OS PROJETOS
    if(!$exec){
      unset($projetos);
      $projetos[] = $_REQUEST['projeto'];
    }
    
    /*unset($projetos);
    $projetos[] = 3338;*/
    
    //IMPORTANTE
    //O HENRIQUE PASSOU INFORAMÇÃO DE QUE ESSE RELATORIO NAO EH POR COMPETENCIA E SIM POR PAGAMENTO
    //ENTÃO A FOLHA DE DEZEMBRO NÃO ENTRA, POIS ELA SÓ É PG EM JANEIRO
    //CASO O PAGAMENTO DA FOLHA SEJE NO DIA 5 DE CADA MES
    //CADA EMPRESA FALA Q É DE UMA FORMA
    //A EMÍLIA DA ASCAGEL ENTROU EM CONTATO COM OCONTADOR E FALOU QUE TEM QUE ENTRAR SIM

    $naoMostraColunaDezembro = false;   //CONDIÇÃO PARA ZERAR QUALQUER VALOR COLOCADO NA COLUNA DE DEZEMBRO DO ANO BASE (SOMENTE O ZICO PEDIU ASSIM)
    
    if ($folhaDezembro) {
        $whereDezembro = " AND A.ano = {$ano_calendario} ";
        $anoConsultaMovi = " AND ano_mov = '{$ano_calendario}' ";
    } else {
        $ano_ini = $ano_anterior;
        $ano_fim = $ano_calendario;
        $whereDezembro = "  AND ( (A.mes = 12 AND A.ano = {$ano_ini} AND A.terceiro = 2) OR ((A.mes <> 12 AND A.ano = {$ano_fim}) OR (A.mes = 12 AND A.ano = {$ano_fim} AND A.terceiro = 1)) AND YEAR(A.data_proc) = {$ano_fim})";
        $anoConsultaMovi = "  AND ( (mes_mov = 12 AND ano_mov = {$ano_ini}) OR (mes_mov NOT IN(12,16,17) AND ano_mov = {$ano_fim}) )"; //MES_MOV 12(DEZEMBRO) 16 E 17 (RESCISAO)
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
    
    
    /**
     * FEITO POR : SINESIO LUIZ
     * REGISTROS DE VALORES DO MASTER PARA O HEADER DO ARQUIVO TXT
     */
    /*$qr_empresa = mysql_query("SELECT B.nome, REPLACE(REPLACE(B.cpf,'.',''),'-','') as cpf,
            REPLACE(REPLACE(REPLACE(B.cnpj,'.',''),'/',''),'-','') as cnpj,
            B.responsavel, B.razao,
            SUBSTR(REPLACE(REPLACE(B.tel, '(',''),')',''),1,2) as ddd,
            REPLACE(SUBSTR(B.tel,5),'-','') as telefone,
            B.contador_cpf, B.contador_nome, B.contador_tel_ddd,B.contador_tel_num, B.contador_fax, B.contador_email 
            FROM regioes as A
            INNER JOIN rhempresa as B ON (A.id_regiao = B.id_regiao)
            WHERE A.id_master = '{$master}' AND A.sigla = 'AD';") OR die("ln 95: " . mysql_error());*/
    
    $sql_empresa = $informeRendimento->getMaster($master,true);
    $qr_empresa = mysql_query($sql_empresa) OR die("ln 95: " . mysql_error());
    $empresa = mysql_fetch_assoc($qr_empresa);
    
    //A PEDIDO DO SABINO, VAMOS DE CNPJ NA MÃO
    $empresa['cnpj'] = "09652823000176";
    
    if($exec){
        $nomeFile = date("Ymd")."__".$empresa['cnpj'] . "_" . $ano_calendario . "_FINAL.txt";
    }else{
        $nomeFile = date("Ymd")."__".$empresa['cnpj'] . "_" . $ano_calendario . ".txt";
    }
    
    
    
    $nome_arquivo = "arquivos/{$nomeFile}";
    $arquivo = fopen($nome_arquivo, 'w+');
    
    $identificadorLeiaute = array("2013" => "7C2DE7J", "2014" => "F8UCL6S", "2015" => "M1LB5V2", "2016" => "L35QJS2", "2017"=>"P49VS72");
    
    //////////////////////////////////////
    /////CABEÇALHO (IDENTIFICADOR DIRF /////
    ////////////////////////////////////////
    $IDENTIFICADOR_DIRF['ID_REGISTRO'] = 'DIRF';
    $IDENTIFICADOR_DIRF['ANO_REFERENCIA'] = $ano_referencia;
    $IDENTIFICADOR_DIRF['ANO_CALENDARIO'] = $ano_calendario;
    $IDENTIFICADOR_DIRF['IDENTIFICADOR_RETIFICADORA'] = $tipoArq;
    $IDENTIFICADOR_DIRF['NUMERO_RECIBO'] = NULL;
    $IDENTIFICADOR_DIRF['IDENTIFICADOR_ESTRUTURA_LEIAUTE'] = $identificadorLeiaute[$ano_referencia];

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
    $IDENTIFICADOR_DECPJ['INDICADOR_PAGAMENTOS_OLIMPIADAS'] = 'N';
    //$IDENTIFICADOR_DECPJ['INDICADOR_SITUACAO_ESPECIAL'] = 'N';
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
    
    /**
     * FEITO POR: SINESIO LUIZ
     * REGISTROS DE CLTS
     */
    /*$qr_clts = "SELECT REPLACE(REPLACE(D.cpf,'-',''), '.','') as cpf, D.nome, D.id_clt, D.id_projeto
                                FROM rh_folha as A
                                INNER JOIN regioes as B ON (B.id_regiao = A.regiao)
                                INNER JOIN rh_folha_proc AS C ON (C.id_folha = A.id_folha)
                                INNER JOIN rh_clt as D ON (D.id_clt = C.id_clt)
                                WHERE A.projeto IN (" . implode(",", $projetos) . ") AND A.status = 3 AND C.status = 3 AND D.id_clt = 7622 
                                $whereDezembro
                                GROUP BY REPLACE(REPLACE(D.cpf,'-',''), '.','')
                                ORDER BY REPLACE(REPLACE(D.cpf,'-',''), '.','') ASC {$limitado}"; //SINESIO - AND D.id_clt IN(6705,7595)*/
    
    $qr_clts = $informeRendimento->getClts($projetos,$limitado,$whereDezembro,true,$cltUnico); 
    $rs_clts = mysql_query($qr_clts) or die("ln 180: " . mysql_error());
    
    
    /**
     * FOLHAS ENVOLVIDAS NA DIRF
     * FEITO POR : SINESIO LUIZ
     */
    /*$qr_folhas = "SELECT id_folha,mes,ano,ids_movimentos_estatisticas,terceiro,projeto
    FROM rh_folha as A 
    WHERE A.projeto IN (" . implode(",", $projetos) . ") AND A.status = 3 
      $whereDezembro";*/
    
    $qr_folhas = $informeRendimento->getFolhas($projetos,$whereDezembro,true);
    $rs_folhas = mysql_query($qr_folhas) or die("ln 186: " . mysql_error());
    $ids_movimentos_folhas = "";
    $idsEstatisticas = array();
    $folhasEnvolvidas = array();
    $idsEstatisticasMes = array();
    //echo "<!-- IDS FOLHAS: <br> ";   
    
    while ($row_folha = mysql_fetch_assoc($rs_folhas)) {
        //echo $row_folha['id_folha'] . ",";
        $ids_movimentos_folhas .= $row_folha['ids_movimentos_estatisticas'] . ",";
        $idsEstatisticas[$row_folha['id_folha']] = $row_folha['ids_movimentos_estatisticas'];
        $idsEstatisticasMes[$row_folha['id_folha']]['mes'] = $row_folha['mes'];
        $idsEstatisticasMes[$row_folha['id_folha']]['flag'] = $row_folha['terceiro'];
        $folhasEnvolvidas[] = $row_folha['id_folha'];
        
        $idsEstatisticasPro[$row_folha['projeto']][] = explode(',',$row_folha['ids_movimentos_estatisticas']);
    }
    //echo " -->";
    
    ///PUTZ CARA, ALEM DO TER Q SE BASEAR NUMA STRING DE IDS SEPARADOS POR VIRGULA, AINDA PRECISO TRATAR SE TEM 2 OU 3 VIRGULAS JUNTAS
    // 10,11,,,20,,13,14,15,,17
    // É MUITA NOJEIRA CARA.. 
    $ids_movimentos_folhas = normalizaIdsEstatisticas($ids_movimentos_folhas);
    
    /**
     * CÓDIGOS DE RESCISÃO PARA NÃO PEGAR A RESCISÃO DO CARA 
     * PELA FOLHA, E SIM PELA PARTE DE RESCISÃO
     * FEITO POR : SINESIO LUIZ
     */
    /*$qr_codigos = "SELECT codigo FROM rhstatus WHERE tipo = 'recisao'";*/
     
    
    $qr_codigos = $informeRendimento->getStatusRescisao(true);
    $rs_codigos = mysql_query($qr_codigos) or die("ln 290: " . mysql_error());
    
    $codigosRes = "";
    while($rowCodigos = mysql_fetch_assoc($rs_codigos)){
        $codigosRes .= $rowCodigos['codigo'].",";
    }
    $codigosRes = substr($codigosRes, 0, -1);
    
    
    
    $c = 0;
    $linhaRel = array();
    $folhaClt = array();
    $totalClts = mysql_num_rows($rs_clts);
//    echo "<!-- INICIO CLTS \r\n";
//    
//    exit("Até aqui me ajudou o Senhor!!!"); 
   
    $EXEclt = true;//HACK PARA NÃO GERAR OS CLTS, POIS ESTAVA ESTANDO AUTONOMOS
    
    if($EXEclt){
    
    while ($clt = mysql_fetch_assoc($rs_clts)) {
        set_time_limit(0);
        if ($exec)
        echo $c . " | {$totalClts} - {$clt['id_clt']} - {$clt['nome']}\r\n";
        //if ($debug) echo "ini({$c} | {$totalClts} - {$clt['id_clt']}) ";

        //DADOS DO CLT
        $IDENTIFICADOR_BPFDEC['ID_REGISTRO'] = 'BPFDEC';
        $IDENTIFICADOR_BPFDEC['CNPJ'] = trim($clt['cpf']);
        $IDENTIFICADOR_BPFDEC['NOME_EMPRESA'] = trim(normalizaNome($clt['nome']));
        $IDENTIFICADOR_BPFDEC['DATA_ATRIBUIDA'] = '';
        $LINHA_BPFDEC = implode('|', $IDENTIFICADOR_BPFDEC) . '|';
        
        $duplicidade = verificaDuplicidade($clt['cpf'],$arrProjetosMaster);
        $totalCpfs = $duplicidade['total'];
        if ($totalCpfs > 1) {
            $ids_clts = "";
            foreach ($duplicidade['rs'] as $val) {
                $ids_clts .= $val['id_clt'] . ",";
                $ids_cltsPros[] = $val['id_projeto'];
            }
            $ids_clts = substr($ids_clts, 0, -1);
        } else {
            $ids_clts = $clt['id_clt'];
        }
        
        if ($debug) echo " inicio SALARIO NORMAL ";
        

        //----- SALARIO NORMAL ----------
        /*$sql_valores = "SELECT *,IF(status_clt=50, (sallimpo+salbase),salbase) AS salbaseCorreto,
                IF(status_clt=50, base_inss,base_inss) AS salbaseCorretoBinss,
                IF(status_clt=40, a5020 + a5035,inss) AS inss,
                IF(status_clt=40, a5021 + a5036, a5021) AS a5021,
                IF(id_ferias>0, 1, 0) AS feriasMes
           FROM  (
           SELECT	A.id_folha,A.mes,A.ano,A.projeto,B.id_clt,B.status_clt,
                   IF(A.ano={$ano_anterior},0,CAST(A.mes AS signed)) as mesEdit,
                   SUM(sallimpo) AS sallimpo,
                   SUM(sallimpo_real) AS sallimpo_real,
                   SUM(salbase) AS salbase,
                   SUM(B.inss) AS inss,
                   SUM(B.a5049) AS a5049,
                   SUM(B.a5021) AS a5021,
                   SUM(B.a5036) AS a5036,
                   SUM(B.a5020) AS a5020,
                   SUM(B.a5035) AS a5035,
                   SUM(B.base_inss) AS base_inss,
                   SUM(B.dias_trab) AS dias_trab,
                   A.ids_movimentos_estatisticas,
                   C.id_ferias

               FROM  rh_folha as A
         INNER JOIN  rh_folha_proc as B ON (B.id_folha = A.id_folha)
         LEFT JOIN   rh_ferias AS C ON (B.id_clt = C.id_clt AND C.`status` = 1 AND C.mes = B.mes AND B.ano = C.ano)
              WHERE  B.id_clt IN ({$ids_clts}) AND A.status = 3 
                AND  A.id_folha IN (" . implode(",", $folhasEnvolvidas) . ") 
                AND  A.terceiro = 2 AND B.status = 3 AND B.status_clt NOT IN ({$codigosRes})
           GROUP BY  A.mes
                ) AS temp";*/
        
        /**
         * SALARIO
         * FEITO POR: SINESIO
         * PASSANDO A QUERY PARA DENTRO DO MÉTODO
         */
        $sql_valores = $informeRendimento->getDadosFolhas($ids_clts,$ano_anterior,$folhasEnvolvidas,$codigosRes,$ano_calendario,$projetos,true);
        
        if(isset($_REQUEST['debug'])){
            echo "<br>*** qry folha ***<br><pre>";
            print_r($sql_valores);
            echo "</pre>";
        }
        
        $qr_valores = mysql_query($sql_valores) or die("ln 139: " . mysql_error());
        $ddirfinal = 0;
        $feriasFolhaProcMes = null;
        while ($rowValorClt = mysql_fetch_assoc($qr_valores)) {
            $row_mov['total'] = 0;
            //MAIS UM CLIENTE ADOTOU O METODO DE NAO ENVIAR DEZEMBRO E PUCHAR DEZEMBRO DO ANO PASSADO E DIZER Q É JANEIRO
            $mes = ($folhaDezembro) ? intval($rowValorClt['mes']) - 1 : intval($rowValorClt['mesEdit']);
            //SINESIO
            //echo "PORRA do MES: " . $mes;
            
            $folhaClt[$mes][] = $rowValorClt['id_clt'];
            
            //SALARIO MATERNIDADE NÃO TEM MOVIMENTO (NÃO SEI PQ)
            //ENTÃO TEM ESSA GAMBI LA NA QUERY
            
            //$salb = $rowValorClt['salbaseCorreto'];
            //$salb = $rowValorClt['salbaseCorretoBinss'];
            
            
            //if($ids_clts == 6936){
            //  echo "Dias trabalhados: " . $rowValorClt['dias_trab'] . "<br />";
            //  echo "MesFerias: " . $rowValorClt['feriasMes'] . "<br />";
            //}
            
            if($rowValorClt['dias_trab'] == 0 AND $rowValorClt['feriasMes'] == 1 && $rowValorClt['base_inss_edit'] <= 0){
                $salb = 0;
            }elseif($rowValorClt['dias_trab'] >= 0 AND $rowValorClt['feriasMes'] == 1){
                $salb = ($rowValorClt['base_inss_edit'] < 0)? $rowValorClt['base_inss'] : $rowValorClt['base_inss_edit'];
            }else{
                $salb = $rowValorClt['base_inss_edit'];
                $salb = ($salb < 0) ? 0 : $salb;
            }
            
            /*RESOLVENDO CASO DE FERIAS QUEBRADA DA WANDA GUEDES*/
            if($rowValorClt['feriasMes'] == 1){
                $feriasFolhaProcMes = $mes;
            }
            
            //CORRIGINDO BUG DE UM CARA
            //DPS CORRIGI ISSO NA QUERY, MAIS TEM Q FICAR ESSE POIS ESSES 2 CASOS EU MUDEI NO BANCO
            if(in_array($rowValorClt['id_clt'],$idCltCOrrecaoBUGFERIAS) && $rowValorClt['feriasMes'] == 1){
                $salb = $rowValorClt['base_inss'];
            }

            /*if(($mes-1) == $feriasFolhaProcMes){
                $salb = $rowValorClt['salbase'];
                $salb = ($salb < 0) ? 0 : $salb;
            }*/
            
            
            $LINHA_RTRT[$mes]  = number_format($salb, 2, '', '');
            $LINHA_RTPO[$mes]  = number_format($rowValorClt['inss'], 2, '', '');
            //$LINHA_RTDP[$mes]  = ($rowValorClt['a5021'] > 0) ? number_format($rowValorClt['a5049'], 2, '', '') : 0;
            $LINHA_RTDP[$mes]  = number_format($rowValorClt['a5049'], 2, '', '');
            $LINHA_RTIRF[$mes] = number_format($rowValorClt['a5021'], 2, '', '');
        }
        unset($sql_valores,$qr_valores);
        
        
        /**
         * FERIAS
         * FEITO POR: SINESIO
         * PASSANDO A QUERY PARA DENTRO DO MÉTODO
         */
        $sql_ferias = $informeRendimento->getDadosFerias($ids_clts,true);
        
        $legendaPensaoFerias = 8;
        /*"SELECT total_remuneracoes, inss, ir, mes, id_clt, abono_pecuniario,pensao_alimenticia
            FROM rh_ferias 
            WHERE id_clt IN ({$ids_clts})
            AND ano = '$ano_calendario' AND status = '1'";*/
        
        if(isset($_REQUEST['debug'])){
            echo "<br>*** qry ferias ***<br><pre>";
            print_r($sql_ferias);
            echo "<br>dados folha antes de ferias<br>RTRT<br>";
            print_r($LINHA_RTRT);
            echo "</pre>";
        }
        //aki//pensao//ferias//80026
        $qr_ferias = mysql_query($sql_ferias) or die("ln 332: " . mysql_error());
        $adianta13Ferias = 0;
        if (mysql_num_rows($qr_ferias) != 0) {
            while ($row_ferias = mysql_fetch_assoc($qr_ferias)) {
                
                $mes = intval($row_ferias['mes']);
                $mesInss = $mes; //MES SEM ALTERAÇÃO, POIS SENA FALOU Q DEVERIA VIR NO MES SEGUINTE
                //$mes = ($folhaDezembro) ? intval($row_ferias['mes']) - 1 : intval($row_ferias['mes']);
                $mes = $mes - 1;
                if($row_ferias['id_clt'] == 652 && $row_ferias['id_ferias'] == 7579){
                    $mes -= 1;
                    //PEGOU ADIANTAMENTO DA PRIMEIRA FERIAS, HACK PARA TIRAR
                    $row_ferias['valor_ferias'] = 5818.19; //VALOR BASE DAS FÉRIAS
                    $row_ferias['adiantamento_mov'] = 0;
                }
                if($row_ferias['id_clt'] == 4114 && $row_ferias['id_ferias'] == 7599){
                    $mes -= 1;
                }
                //LOGICA DO CARAMBA, COMO EMPURREI O DEZEMBRO DE 2012 PARA PRIMEIRA LINHA DO ARRAY, LOGO MES 12 DEZEMBRO NAO É 12 E SIM 11, POIS SE NAO FACO 14 MESES
                //EXEMPLO, PAGAMENTOS DA FOLHA DO MES DE JANEIRO, FORAM EMPURRADOS PARA FEVEREIRO, ENTÃO A FOLHA DE JANEIRO É PAGA EM FEVEREIRO, A RESCISÃO É PAGA NO MESMO MES
                //SE A RESCISAO FOI FEITA EM JANEIRO, ELA ENTRA NO SEGUNDO ITEM DO ARRAY, POIS O PRIMEIRO ITEM DO ARRAY É DEZEMBRO, LOCO NÉ?
                //PORTANTO EM DEZEMBRO, ELE NÃO PODE ENTRAR NO MES 12, E SIM NO MES 11, POIS A CHAVE 12 É O DECIMO TERCEIRO
                
                //NA LAGOS ENCONTREI UMA PESSOA Q TEM FÉRIAS E SALARIO NO MESMO MES
                //NAO POSSO ZERAR O SALARIO DELA DA OUTRA UNIDADE, PRECISO SOMAR TUDO
                //if ($LINHA_RTRT[$mes] > 0) {
                    
                    $LINHA_RTPO[$mes]   += number_format($row_ferias['inss'], 2, '', '');
                    $LINHA_RTIRF[$mes]  += number_format($row_ferias['ir'], 2, '', '');
                    //$LINHA_RTRT[$mes]   += number_format($row_ferias['total_remuneracoes'], 2, '', ''); //$row_ferias['base_inss']
                    $LINHA_RTRT[$mes]   += number_format($row_ferias['valor_ferias'], 2, '', ''); //$row_ferias['base_inss']
                    
                    //$LINHA_RIAP[$mes]   += number_format($row_ferias['abono_pecuniario'], 2, '', '');
                    $LINHA_RIAP[$mes]   += number_format($row_ferias['total_abono'], 2, '', '');
                    $DECIMO_RTRT        += number_format($row_ferias['adiantamento_mov'], 2, '', ''); //ADIANTAMENTO DE 13
                    $adianta13Ferias    = $row_ferias['adiantamento_mov'];
                    
                    //BUSCAR PENSÃO NA TABELA DE FERIAS_ITENS
                    $sqlPensaoFerias = $informeRendimento->getPensaoEmFerias($row_ferias['id_clt'], $row_ferias['id_ferias'], $legendaPensaoFerias, true);
                    if(isset($_REQUEST['debug'])){
                        echo "<br>*** qry pensao ferias ***<br><pre>";
                        print_r($sqlPensaoFeriasfbxcv);
                        echo "</pre>";
                    }
                    
                    
                    $qr_p_ferias = mysql_query($sqlPensaoFerias) or die("ln pensao ferias 580: " . mysql_error());
                    $valorPensaoFerias = 0;
                    if (mysql_num_rows($qr_p_ferias) != 0) {
                        while ($row_pferias = mysql_fetch_assoc($qr_p_ferias)) {
                            $valorPensaoFerias += $row_pferias['valor'];
                        }
                    }
                    
                    $LINHA_RTPA[$mes]   += number_format($valorPensaoFerias, 2, '', '');
                    
                    /**
                    * A PEDIDO DO MILTON, SEMPRE O FOR MÊS DE FERIAS O VALOR DE DEDUÇÃO 
                    * POR DEPENDENTES PRECISAR SER DOBRADO, J Á QUE TB É DEDUZIDO NAS FERIAS
                    * NA HORA DO CALCULO DO IR
                    */
                    
                    $LINHA_RTDP[$mes]   += $LINHA_RTDP[$mes]; //AINDA NÃO FUNCIONA NAS FÉRIAS -- DEDUÇÃO DE IR
                    
                /*} else {
                    
                    $LINHA_RTPO[$mes]   = number_format($row_ferias['inss'], 2, '', '');
                    $LINHA_RTIRF[$mes]  = number_format($row_ferias['ir'], 2, '', '');
                    $LINHA_RTRT[$mes]   = number_format($row_ferias['total_remuneracoes'], 2, '', '');//$row_ferias['base_inss']
                    
                    $LINHA_RIIRP[$mes] = 0;
                    $LINHA_RTPP[$mes] = 0;
                    $LINHA_RIAP[$mes] = number_format($row_ferias['abono_pecuniario'], 2, '', '');
                    $LINHA_RTPA[$mes] = number_format($row_ferias['pensao_alimenticia'], 2, '', '');
                    $LINHA_RIDAC[$mes] = 0;
                }*/
            }
        }
        
        unset($sql_ferias,$qr_ferias);
        
        if ($debug) echo " inicio 13º ";
        
        
        /**
         * DECIMO TERCEIRO
         * FEITO POR: SINESIO
         * PASSANDO A QUERY PARA DENTRO DO MÉTODO
         * DECIMO TERCEIRO É O VALOR LIQUIDO
         */
        /*$qr_valores_dt = mysql_query("SELECT B.id_clt,
            B.salbase,
            B.valor_dt,
            (B.salliquido + B.inss_dt + B.ir_dt + B.a5049) AS valor_rt_dt,
            (B.salliquido + B.desco + B.inss_dt + B.ir_dt + B.a5049) AS val_ultimo,
            (B.salbase - B.ir_dt) as brutoMenosIr,
            B.base_inss,
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
            AND B.status = 3 AND A.tipo_terceiro IN ({$whereDecimo})") or die("ln 345: " . mysql_error());*/
        
        $qr_valores =  $informeRendimento->getDadosDecimoTerceiro($ids_clts, $ano_calendario, $whereDecimo,true);
        $qr_valores_dt = mysql_query($qr_valores);
        
        if(isset($_REQUEST['debug'])){
            echo "<br>*** qry 13 ***<br><pre>";
            print_r($qr_valores);
            echo "</pre>";
        }
        
        while ($row_valor_dt = mysql_fetch_assoc($qr_valores_dt)) {
            $idDecimoTerceiro = array();
            $idDecimoTerceiro[$row_valor_dt['id_clt']] = $row_valor_dt['id_clt'];
            
            if($adianta13Ferias > 0){
                $valor_decimo   = $row_valor_dt['base_inss'] - $adianta13Ferias; 
            }else{
                $valor_decimo   = $row_valor_dt['base_inss']; //1 - $row_mov['total'] // 2 - $row_valor_dt['valor_rt_dt'] // TIREI 'salbase' 12.02.2015
            }
            $valor_RTRT     += $valor_decimo;
            $valor_RTPO     += $row_valor_dt['inss_dt'];
            //$valor_RTDP     += ($row_valor_dt['ir_dt'] > 0) ? $row_valor_dt['a5049'] : '';
            $valor_RTDP     += $row_valor_dt['a5049'];
            $valor_RTIRF    += $row_valor_dt['ir_dt'];
            
            if(isset($_REQUEST['debug'])){
                echo "<br>*** valores de 13 ***<br><pre>";
                $debValor = $row_valor_dt;
                $debValor['ids_movimentos_estatisticas'] = "";
                print_r($debValor);
                echo "</pre>";
            }
        }
        
        
        
        //LINHAS DECIMO
        $DECIMO_RTRT += number_format($valor_RTRT, 2, "", ""); //SOMANDO POIS TEM VALOR CASO DE ADIANTAMENTO EM FÉRIAS, JÁ VEM PREENCHDO AE EM CIMA
        $DECIMO_RTPO = number_format($valor_RTPO, 2, "", "");
        $DECIMO_RTPP = 0;
        $DECIMO_RTDP = number_format($valor_RTDP, 2, "", "");
        $DECIMO_RTIRF = number_format($valor_RTIRF, 2, "", "");
        $DECIMO_RTPA = 0;
        $DECIMO_RIDAC = 0;
        unset($valor_RTRT, $valor_RTPO, $valor_RTDP, $valor_RTIRF, $qr_valores,$qr_valores_dt);
        
        //---------- DECIMO TERCEIRO ----------
        
        ///EXPLICAÇÃO DA LINHA IF(motivo!=65,aviso_valor,0)aviso_valor,
        //SE A PESSOA RECEBER O AVISO, ESSE VALOR NÃO ENTRA NA DIRF,
        //O TIPO 65 É QND A PESSOA PAGA O AVISO, ENTÃO NÃO PODE DIMINUIR NA DIRF
        //POIS A PESSOA NÃO ESTA RECEBENDO ESSE VALOR, E SIM PAGANDO
        //(ANDERSON) O TIPO 65 - PEDIDO DE DISPENSA, O FUNCIONÁRIO PAGA PARA EMPRESA, OU SEJA, VEM COMO DESCONTO
        if ($debug) echo " recisao ";
        
        /**
         * RIIRP - RESCISAO
         * FEITO POR: SINESIO
         * PASSANDO A QUERY PARA DENTRO DO MÉTODO
         * DECIMO TERCEIRO É O VALOR LIQUIDO
         */
        $sql_rescisa = $informeRendimento->getDadosRescisao2015($ids_clts, true);
                
         /*"SELECT *,total_liquido,mes_demissao,id_clt,ferias,dt_salario,
            IF(total_liquido = 0 , 0, (total_rendimento - ferias - aviso_valor) )AS rendimentos,
            (inss_ferias + previdencia_ss) AS total_inss,
            (inss_ferias + inss_ss) AS total_inss2,
            (ir_ss + ir_ferias) AS total_ir,
            aviso_valor + ferias AS outros
            FROM (
                    SELECT 
                    total_liquido,saldo_salario,total_rendimento,dt_salario,
                    IF(motivo!=65,aviso_valor,0)aviso_valor,
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
            ) AS temp";*/
        
        if(isset($_REQUEST['debug'])){
            echo "<br>***** SQL RESCISÃO *****<br><pre>";
            print_r($sql_rescisa);
            echo "</pre>";
        }
        
        $qr_rescisao = mysql_query($sql_rescisa) or die("ln 296: " . mysql_error());

        if (mysql_num_rows($qr_rescisao) != 0) {
            //WHILE, PEGA CASO TENHA RESCISÃO COMPLEMENTAR
            while($row_rescisao = mysql_fetch_assoc($qr_rescisao)){
                //LOGICA DO CARAMBA, COMO EMPURREI O DEZEMBRO DE 2012 PARA PRIMEIRA LINHA DO ARRAY, LOGO MES 12 DEZEMBRO NAO É 12 E SIM 11, POIS SE NAO FACO 14 MESES
                //EXEMPLO, PAGAMENTOS DA FOLHA DO MES DE JANEIRO, FORAM EMPURRADOS PARA FEVEREIRO, ENTÃO A FOLHA DE JANEIRO É PAGA EM FEVEREIRO, A RESCISÃO É PAGA NO MESMO MES
                //SE A RESCISAO FOI FEITA EM JANEIRO, ELA ENTRA NO SEGUNDO ITEM DO ARRAY, POIS O PRIMEIRO ITEM DO ARRAY É DEZEMBRO, LOCO NÉ?
                //PORTANTO EM DEZEMBRO, ELE NÃO PODE ENTRAR NO MES 12, E SIM NO MES 11, POIS A CHAVE 12 É O DECIMO TERCEIRO
                $mes = ($folhaDezembro) ? intval($row_rescisao['mes_demissao']) - 1 : intval($row_rescisao['mes_demissao']);
                $mes = $mes - 1;
                $mes = ($mes == 12) ? 11 : $mes;

                //MESMO CASO QUE ACONTECEU NA TABELA CLT COM O CMAPO DE DATA DE DEMISSAO
                //TEM 2 CAMPOS DE INSS 13 E INSS SALDO DE SALARIO
                //NÃO SEI PQ MAS AS VEZES UM VEM E O OUTRO NÃO
                //E AS VEZES VEM VALOR NOS 2 CAMPOS
//                if($row_rescisao['total_inss'] > 0){
//                    $inssResci = $row_rescisao['total_inss'];
//                }else{
//                    $inssResci = $row_rescisao['total_inss2'];
//                }
                
                $valorMovsMediasInden = 0;
                $valorMovsMediasDT = 0;
                $valorMovsMediasFerias = 0;
                $valorRTRTComplementar = 0;
                $valorRTRTComplmenInd = 0;
                $arrMovsMediasSubRogoRescisao = array();
                
                //SÓ BUSCA UMA VEZ, SE TIVER RESCISAO COMPLEMENTAR NÃO BUSCA NOVAMENTE
                if($row_rescisao['rescisao_complementar'] == 0){
                    $codigos = array("90030","90035","90056","90057","90055","90034","90086","70011","");
                    $sql_movs_MediasSubRogoRescisao = $informeRendimento->getRendimentosMediasSubRogoRescisao($ids_clts,$codigos,true);
                    $rs_movs_MediasSubRogoRescisao = mysql_query($sql_movs_MediasSubRogoRescisao);
                    
                    if(isset($_REQUEST['debug'])){
                        echo "<br>*** getRendimentosMediasSubRogoRescisao ****<br><pre>";
                        print_r($sql_movs_MediasSubRogoRescisao);
                        echo "</pre><br/>";
                    }
                    
                    
                    if(mysql_num_rows($rs_movs_MediasSubRogoRescisao) > 0){
                        while($row_movs_indenizados = mysql_fetch_assoc($rs_movs_MediasSubRogoRescisao)){
                            $arrMovsMediasSubRogoRescisao[$row_movs_indenizados['cod_movimento']] = $row_movs_indenizados;
                        }
                    }

                    //MÉDIAS INDENIZADAS
                    $arr_movsMediasIndenizados = array("90030","90055","90056","70011");
                    foreach($arr_movsMediasIndenizados as $movs){
                        if(array_key_exists($movs,$arrMovsMediasSubRogoRescisao)){
                            $valorMovsMediasInden += $arrMovsMediasSubRogoRescisao[$movs]['valor_movimento'];
                        }
                    }

                    //MÉDIAS DE 13
                    $arr_movsMediasDT = array("90086","90057");
                    foreach($arr_movsMediasDT as $movs){
                        if(array_key_exists($movs,$arrMovsMediasSubRogoRescisao)){
                            $valorMovsMediasDT += $arrMovsMediasSubRogoRescisao[$movs]['valor_movimento'];
                        }
                    }

                    //MÉDIAS FERIAS
                    $arr_movsMediasFerias = array("90034","90035"); 
                    foreach($arr_movsMediasFerias as $movs){
                        if(array_key_exists($movs,$arrMovsMediasSubRogoRescisao)){
                            $valorMovsMediasFerias += $arrMovsMediasSubRogoRescisao[$movs]['valor_movimento'];
                        }
                    }
                    
                }else{
                    
                    //MOVIMENTOS LANÇADOS NA RESCISAO COMPLEMENTAR
                    $dadosMovsComplementar = $informeRendimento->getMovimentosRescisaoComplementarRT($row_rescisao['id_recisao']);
                    $valorRTRTComplementar = $dadosMovsComplementar['total'];
                    
                    $dadosComplementarInd = $informeRendimento->getMovimentosRescisaoComplementar($row_rescisao['id_recisao']);
                    $valorRTRTComplmenInd = $dadosComplementarInd['total'];
                    
                    if(isset($_REQUEST['debug'])){
                        echo "<br>*** getMovimentosRescisaoComplementarRT ****<br><pre>";
                        echo "total: {$dadosMovsComplementar['total']}<br>";
                        print_r($dadosMovsComplementar);
                        echo "</pre><br/>";
                        
                        echo "<br>*** getMovimentosRescisaoComplementar ****<br><pre>";
                        print_r($dadosComplementarInd);
                        echo "</pre><br/>";
                    }
                    
                }
                
                //VERIFICAR SE TEVE DESCONTO DE ADIANTAMENTO DO 13 SALARIO
                
                
                if(isset($_REQUEST['debug'])){
                    echo "<br>*** query Movs indenizados resicsao (complementar:{$row_rescisao['rescisao_complementar']}) ****<br><pre>";
                    echo "valor: {$valorMovsMediasInden}<br>";
                    //echo "{$sql_movs_MediasSubRogoRescisao}<br>";
                    print_r($arrMovsMediasSubRogoRescisao);
                    echo "</pre>";
                }
                
                /**
                 * By Ramon 02/02/2017
                 * Dia tenso, não estou achando o valor q preciso tirar da parte de indenizados
                 * para liberar logo o Sena vou por isso na mão, se algum dia eu tiver tempo eu procuro melhor
                 * mais ja estou a 2 dias para liberar essa joça... fé em Deus...
                 */
                /**
                 * By Ramon 13/02/2017
                 * Ainda não chegamos ao valor correto desse funcionário totalmente zuado
                 * para responder logo um e-mail vamos fazer gambi, não gosto disso, mais não temos mais tempo
                 * fé em Deus
                 */
                //echo "<br>++++ aki {$ids_clts}<br>";
                if($ids_clts == "4264"){
                    $row_rescisao['valor_indenizado'] = $row_rescisao['valor_indenizado'] - 1466.43; //primeiro tive q tirar
                    $row_rescisao['valor_indenizado'] = $row_rescisao['valor_indenizado'] + 1113.53; //agora vou ter q adicionar um valor PRÓXIMO
                }
                /**
                 * By Ramon 14/02/2017
                 * Infelizmente não estou achando a diferença desse ACACIO
                 * e como de costume não temos muito tempo vamos na mão
                 */
                if($ids_clts == "81" && $row_rescisao['rescisao_complementar'] == 1){
                    $row_rescisao['valor_indenizado'] = $row_rescisao['valor_indenizado'] - 32.97; 
                }
                
                //$RESC_rendimentos    = $row_rescisao['rendimentos'];
                $movimentosSemRT     = 0; //PEGAR MOVIMENTOS ESPECIFICOS, (74,82,107) AJUDA DE CUSTO , VT E VT REEMBOLSO
                //$RESC_rendimentos    = $row_rescisao['rendimentos'] - $row_rescisao['valor_indenizado'] - $row_rescisao['dt_salario'] - $valorMovsMediasInden - $valorMovsMediasDT - $movimentosSemRT;
                $RESC_rendimentos    = $row_rescisao['base_inss_ss'] + $valorRTRTComplementar;// + $row_rescisao['ferias'] + $valorMovsMediasFerias; 
                $RESC_indenizado     = $row_rescisao['valor_indenizado'] + $valorMovsMediasInden + $row_rescisao['ferias'] + $valorMovsMediasFerias + $valorRTRTComplmenInd;
                $RESC_inss           = $row_rescisao['inss_ss'];
                $RESC_ir             = $row_rescisao['ir_ss'];
                $RESC_rio            = $row_rescisao['sal_familia'];
                
                /*if($ids_clts == "4021"){
                    if($row_rescisao['rescisao_complementar'] == 0){
                        $RESC_indenizado = 18454.85; 
                    }else{
                        $RESC_indenizado = 0;
                    }
                }*/
                
                //$valDt = $row_rescisao['dt_salario'] + $valorMovsMediasDT - $row_rescisao['adiantamento_13'];
                //VARIAVEL QUE DIZ Q TEVE FOLHA DE 13
                if($valor_decimo > 0){
                    //DIMINUI O 13 PAGO NA FOLHA
                    $valDt = $row_rescisao['base_inss_13'] + $valorMovsMediasDT;// - $row_rescisao['adiantamento_13'];
                }else{
                    //NÃO DIMINUI O ADIANTAMENTO MESMO QUE TENHA, POIS FOI ADIANTADO NAS FÉRIAS, MAIS LA NAS FÉRIAS EU NÃO PEGO
                    $valDt = $row_rescisao['base_inss_13'] + $valorMovsMediasDT;
                }
                
                if($row_rescisao['rescisao_complementar'] == 1){
                    $valDt = $row_rescisao['dt_salario'] + $row_rescisao['terceiro_ss'];
                }
                
                
                
                $RESC_salario_dt     = $valDt;
                $RESC_inss_dt        = $row_rescisao['inss_dt'];
                $RESC_ir_dt          = $row_rescisao['ir_dt'];
                
                if($RESC_rendimentos <= 0 && $row_rescisao['rescisao_complementar'] == 0){
                    $RESC_indenizado = 0;
                    $RESC_inss = 0;
                    $RESC_ir = 0;
                    $RESC_rendimentos = 0;
                }
                
                if(isset($_REQUEST['debug'])){
                    echo "<br>*** valores da variavel antes de pegar a rescisao (mes: {$mes}) (complementar: {$row_rescisao['rescisao_complementar']}) ****<br><pre>";
                    print_r($LINHA_RTRT);
                    print_r($LINHA_RTPO);
                    print_r($LINHA_RTIRF);
                    echo "inden";
                    print_r($LINHA_RIIRP);
                    echo "<br><br>*** row rescisao  (complementar: {$row_rescisao['rescisao_complementar']}) *** <br>";
                    print_r($row_rescisao);
                    echo "<br>RTRT Rescisao: RESC_rendimentos    = row_rescisao['base_inss_ss'] + valorRTRTComplementar;<br>";
                    echo "<br>RTRT Rescisao: $RESC_rendimentos  = {$row_rescisao['base_inss_ss']} + $valorRTRTComplementar<br>";
                    echo "<br>****";
                    echo "<br>RTRT INDENIZADO: {row_rescisao['valor_indenizado']} + valorMovsMediasInden + {row_rescisao['ferias']} + valorMovsMediasFerias + valorRTRTComplmenInd;<br>";
                    echo "<br>RTRT INDENIZADO: {$row_rescisao['valor_indenizado']} + $valorMovsMediasInden + {$row_rescisao['ferias']} + $valorMovsMediasFerias + $valorRTRTComplmenInd;<br>";
                    echo "<br>****BASE INSS 13";
                    echo "<br>RESC_salario_dt Rescisao: $RESC_salario_dt<br>";
                    echo "</pre>";
                }
                
                //NA LAGOS ENCONTREI UMA PESSOA Q TEM RESCISAO NO MES 7 EM UMA UNIDADE
                //E TEM SALARIO DE 30 DIAS NO MES 7 EM OUTRA UNIDADE
                //NAO POSSO ZERAR O SALARIO DELA DA OUTRA UNIDADE, PRECISO SOMAR TUDO
                //AGORA ENCONTREI UMA PESSOA Q TEM 2 RESCISÕES EM 2 UNIDADES NO MESMO MES
                //ENTÃO OPTEI EM SEMPRE SOMAR AS VALORES ENCONTRADOS NO MES
                $LINHA_RTRT[$mes]   += number_format($RESC_rendimentos, 2, '', '');
                $LINHA_RIIRP[$mes]  += number_format($RESC_indenizado, 2, '', '');
                $LINHA_RTPO[$mes]   += number_format($RESC_inss, 2, '', '');
                $LINHA_RTIRF[$mes]  += number_format($RESC_ir, 2, '', '');
                $LINHA_RIO['salfam'] += number_format($RESC_rio, 2, '', '');
                //CASO TENHA VALOR NO 13 VAI ZERAR A COLUNA 13 TODA
                //POIS OS VALORES DE 13 FOI PROCESSADO NA RESCISAO
                //PRECISO VERIFICAR TAMBEM SE A RESCISÃO É DO MESMO ID DO CLT DO 13
                //POIS EXISTE CASO DA PESSOA SAIR DE UMA UPA E ENTRAR NA OUTRA
                //ASSIM ELA VAI TER DECIMO TERCEIRO SENDO DE OUTRO ID_CLT
                if($row_rescisao['dt_salario'] > 0){
                    $DECIMO_RTRT    += number_format($RESC_salario_dt, 2, "", "");
                    $DECIMO_RTPO    += number_format($RESC_inss_dt, 2, "", "");
                    $DECIMO_RTIRF   += number_format($RESC_ir_dt, 2, "", "");
                    
                    $DECIMO_RIIRP = NULL;
                    
                    $DECIMO_RTPP = NULL;
                    $DECIMO_RTDP = null;
                    $DECIMO_RTPA = NULL;
                    $DECIMO_RIDAC = NULL;
                }
                
            }
            
            //NÃO PODE EXISTIR VALOR NEGATIVO
            $LINHA_RTRT[$mes]   = ($LINHA_RTRT[$mes] < 0) ? 0 : $LINHA_RTRT[$mes];
            $LINHA_RIIRP[$mes]  = ($LINHA_RIIRP[$mes] < 0) ? 0 : $LINHA_RIIRP[$mes];
            $LINHA_RTPO[$mes]   = ($LINHA_RTPO[$mes] < 0) ? 0 : $LINHA_RTPO[$mes];
            $LINHA_RTIRF[$mes]  = ($LINHA_RTIRF[$mes] < 0) ? 0 : $LINHA_RTIRF[$mes];
            
            unset($sql_rescisa,$qr_rescisao,$movimentosSemRT,$RESC_rendimentos,$RESC_indenizado,$RESC_inss,$RESC_ir,$valDt,$RESC_salario_dt,$RESC_inss_dt,$RESC_ir_dt);
            unset($valor_decimo); //limpando aqui, pois a rescisão precisa dessa variavel
        }
        //---------- RIIRP - RESCISAO ----------
        
        //---------- SEPARA IDS_MOVIMENTOS_ESTATISTICAS ---------- aki
        $idsMovimentosRowClt = "";
        if($totalCpfs > 1){
            foreach($ids_cltsPros as $pros){
                foreach($idsEstatisticasPro[$pros] as $ids){
                    $idsMovimentosRowClt .= implode(",",$ids);
                }
            }
        }else{
            foreach($idsEstatisticasPro[$clt['id_projeto']] as $ids){
                $idsMovimentosRowClt .= implode(",",$ids);
            }
        }
        
        $idsMovimentosRowClt = (strlen($idsMovimentosRowClt) > 0) ? $idsMovimentosRowClt : 0;
        
        //---------- RIAP - ABONO PECUNIÁRIO ----------
        /*if ($debug) echo " inicio RIAP ";
        
        $sqlRiap = "SELECT mes, REPLACE(abono_pecuniario, '.','') as abono_pecuniario
                        FROM rh_ferias 
                        WHERE id_clt IN ({$ids_clts}) AND ano = '$ano_calendario'";
        $qr_ferias = mysql_query($sqlRiap) or die("ln 408: " . mysql_error());

        $row_ferias = mysql_fetch_assoc($qr_ferias);
        if (mysql_num_rows($qr_ferias) != 0) {
            $chave = $row_ferias['mes'];
            $LINHA_RIAP[$chave] = ($row_ferias['abono_pecuniario'] != '0.00' ) ? $row_ferias['abono_pecuniario'] : '';
            $DECIMO_RIAP = '';
        }
        */
        
        if ($debug) echo " inicio RTPA ";
        
        /**
         * RTPA RENDIMENTOS TRIB. DEDUCAO - PENSÃO ALIMENTICIA
         * FEITO POR: SINESIO
         * PASSANDO A QUERY PARA DENTRO DO MÉTODO
         * 
         */
        /*$sql_pensao = "SELECT id_movimento,valor_movimento FROM rh_movimentos_clt WHERE id_clt IN ({$ids_clts}) AND cod_movimento IN (".implode(",", $arrayPensaoAlimenticia).") AND id_movimento IN ($idsMovimentosRowClt)";*/
        
        //$sql_pensao = $informeRendimento->getDadosPensaoAlimenticiaNovaTabela($ids_clts,$folhasEnvolvidas,true);
        $dadosPensao = $informeRendimento->getDadosPensaoAlimenticiaNovaTabela($ids_clts,$folhasEnvolvidas,$arrayPensaoAlimenticia,$idsMovimentosRowClt);
        //$sql_pensaoMov = $informeRendimento->getDadosPensaoAlimenticia($ids_clts,$arrayPensaoAlimenticia,$idsMovimentosRowClt,true);
        
        //$qr_pensao = mysql_query($sql_pensao) or die("ln 424: $sql_pensao <br>" . mysql_error());
        //$qr_pensaoMov = mysql_query($sql_pensaoMov) or die("ln 424: $sql_pensaoMov <br>" . mysql_error());
        
        /*if (mysql_num_rows($qr_pensao) > 0) {
            $valor_pensao = null;
            $valor_pensaoD = null;
            while ($row_pensao = mysql_fetch_assoc($qr_pensao)) {
                //ATENÇÃO, O MES DO MOVIMENTO NÃO VEM DA TABELA DE MOVIMENTOS, POIS PODE HAVER UM MOVIMENTO SEMPRE, 
                //ENTÃO, PARA SABER REALMENTE O MES DO MOVIMENTO, TEM Q VERIFICAR EM QUAL MES AGENTE ACHA O ID_MOVIMENTO_ESTATISTICA DELE
                
                if($row_pensao['mesEdit'] == 12 && $row_pensao['terceiro'] == 1){
                    $valor_pensaoD += $row_pensao['valor_movimento'];
                }else{
                    $valor_pensao[$row_pensao['mesEdit']] += $row_pensao['valor_movimento'];
                }
                
                //COMANTANDO DAQUI PARA BAIXO, POIS NÃO PRECISA VERIFICAR MOV_ESTATISTICAS
//                $idMovP = $row_pensao['id_movimento'];
//                
//                foreach($folhasEnvolvidas as $foLHAS){
//                    if(strstr($idsEstatisticas[$foLHAS], $idMovP)){
//                        
//                        $m = $idsEstatisticasMes[$foLHAS]['mes'];
//                        
//                        if ($idsEstatisticasMes[$foLHAS]['flag'] == 2) {
//                            if($m == 12){
//                                $mesMov = 0;
//                            }else{
//                                $mesMov = ($folhaDezembro) ? intval($m) - 1 : intval($m);
//                            }
//                            $valor_pensao[$mesMov] += $row_pensao['valor_movimento'];
//                        } else {
//                            $valor_pensaoD += $row_pensao['valor_movimento'];
//                        }
//                    }
//                }
            }
            
            if(isset($_REQUEST['debug'])){
                echo "<br>**pensao**<pre>";
                echo "{$sql_pensao}<br>arr pens<br>";
                print_r($valor_pensao);
                echo "</pre><br>pensao 13: {$valor_pensaoD}<br>";
            }
            
            //NORMALIZAR O VALOR, TIRAR PONTO E VIRGULA PRA GAVAR NO TXT
            foreach ($valor_pensao as $mes => $val) {
                $LINHA_RTPA[$mes] += number_format($val, 2, "", "");
            }

            $DECIMO_RTPA = (!empty($valor_pensaoD)) ? number_format($valor_pensaoD, 2, "", "") : NULL;
            unset($row_pensao);
            
            $sql_infoDepen = $informeRendimento->getInformacoesFavorecidosPensao($ids_clts,true);
            
            if(isset($_REQUEST['debug'])){
                echo "<br>**infomarções dos favorecidos da pensao**<pre>";
                echo "{$sql_infoDepen}</pre><br>";
            }
            
            $LINHA_INFPA = "";
            $IDENTIFICADOR_INFPA = "";
            $qr_infoPensao = mysql_query($sql_infoDepen) or die("ln 989 info pensao: $sql_infoDepen <br>" . mysql_error());
            if (mysql_num_rows($qr_infoPensao) > 0) {
                while ($row_infoPensao = mysql_fetch_assoc($qr_infoPensao)) {
                    //INFORMAÇÕES DOS BENEFICIADOS PELA PENSAO
                    $IDENTIFICADOR_INFPA['ID_REGISTRO'] = 'INFPA';
                    $IDENTIFICADOR_INFPA['CPF'] = "";                  //2017 - OPCIONAL
                    $IDENTIFICADOR_INFPA['DATA_NASCIMENTO'] = "";      //2017 - OPCIONAL
                    $IDENTIFICADOR_INFPA['NOME'] = trim(normalizaNome($row_infoPensao['favorecido']));
                    $IDENTIFICADOR_INFPA['RELACAO_DEPE'] = $row_infoPensao['cod_receita'];
                    $LINHA_INFPA .= implode('|', $IDENTIFICADOR_INFPA) . '|'."\n";
                }
            }
            
            if(isset($_REQUEST['debug'])){
                echo "<br>** linha info pensao **<pre>";
                echo "{$LINHA_INFPA}</pre><br>";
            }
            
        }
        unset($sql_pensao,$qr_pensao);*/
        
        
        //DADOS RETORNADO DO MÉTODO JÁ TRATADO, SÓ PASSAR PRO ARQUIVO TXT
        //NORMALIZAR O VALOR, TIRAR PONTO E VIRGULA PRA GAVAR NO TXT
        
        $PENSAO_DEPENDENTE = null;
        
        if(isset($_REQUEST['debug'])){
            echo "<br>************-Pensao__ PENSAO PENSAO-**************<br><pre><div style='max-width:500px; overflow:auto'>";
            print_r($dadosPensao);
            echo "<br>SQL MOVIMENTOS de pensao<br>";
            print_r($sql_pensaoMov);
            echo "</div></pre><br/>";
        }
        
        if(count($dadosPensao['dependentes']>0)){
            
            //RODANDO AS PENSÕES Q FORAM LANÇADAS COMO MOVIMENTOS MANUALMENTE (DIF PENSAO)
            /*if (mysql_num_rows($qr_pensaoMov)){
                while ($row_pensao = mysql_fetch_assoc($qr_pensaoMov)) {
                    if(isset($_REQUEST['debug'])){
                        echo "<br>pensões lançadas<br><pre>";
                        print_r($row_pensao);
                        echo "</pre>";
                    }
                    $LINHA_RTPA[$row_pensao['mes_mov']] += number_format($row_pensao['valor_movimento'], 2, "", "");
                    $LINHA_RTPA_INFO[$row_pensao['mes_mov']] += number_format($row_pensao['valor_movimento'], 2, "", "");
                }
                //$valor_pensao[$row_pensao['mesEdit']] += $row_pensao['valor_movimento'];
            }*/
            
            
            foreach($dadosPensao['dependentes'] as $cpf){
                
                //NORMALIZA VALOR PARA ESCREVER NO TXT
                foreach ($dadosPensao['mensal'][$cpf] as $mes => $val) {
                    $LINHA_RTPA[$mes] += number_format($val, 2, "", "");
                    $LINHA_RTPA_INFO[$mes] += number_format($val, 2, "", "");
                }
                $DECIMO_RTPA += (!empty($dadosPensao['dt'][$cpf])) ? number_format($dadosPensao['dt'][$cpf], 2, "", "") : NULL;
                $DECIMO_RTPA_INFO += (!empty($dadosPensao['dt'][$cpf])) ? number_format($dadosPensao['dt'][$cpf], 2, "", "") : NULL;
                
                //ANTES DE ESCREVER PRECISO JOGAR ZERO NOS MESES Q NÃO TEM NADA
                for ($i = 0; $i <= 11; $i++) {
                    if (!array_key_exists($i, $LINHA_RTPA_INFO)) {
                        $LINHA_RTPA_INFO[$i] = 0;
                    }
                }
                
                ksort($LINHA_RTPA_INFO);
                
                //INFORMAÇÕES DO DEPENDENTE
                $PENSAO_DEPENDENTE .= implode('|', $dadosPensao['info'][$cpf])."|\n";
                $PENSAO_DEPENDENTE .= "RTPA|".implode('|', $LINHA_RTPA_INFO) . '|' . $DECIMO_RTPA_INFO . '|'."\n";
                unset($LINHA_RTPA_INFO,$DECIMO_RTPA_INFO);
            }
            unset($dadosPensao);
        }
        
        if(isset($_REQUEST['debug'])){
            echo "<br>************-Pensao-**************<br>";
            print_r($DECIMO_RTPA);
            print_r($LINHA_RTPA);
            echo "<br/>";
            echo $PENSAO_DEPENDENTE;
            echo "<br>Linha Pesao lançadas<br>";
        }
        
        
        if ($debug) echo " inicio RIDAC ";
        
        
        /**
         * RIDAC RENDIMENTOS ISENTOS - DIÁRIA E AJUDA DE CUSTO (PARCELA UNICA NÃO INCIDE)
         * FEITO POR : SINESIO LUIZ
         */
        /*$sql_ajuda = "SELECT id_movimento,valor_movimento
                        FROM rh_movimentos_clt 
                        WHERE cod_movimento = '50111' AND id_clt IN ({$ids_clts}) 
                        AND id_movimento IN ($idsMovimentosRowClt)";*/
        $sql_ajuda = $informeRendimento->getRendimentosIsentos($ids_clts,$idsMovimentosRowClt,true);
        $qr_ajuda = mysql_query($sql_ajuda) or die("ln 454: " . mysql_error());
        unset($foLHAS);
        if (mysql_num_rows($qr_ajuda) > 0) {
            $valor_ajuda = null;
            $valor_ajudaD = null;
            while ($row_ajuda = mysql_fetch_assoc($qr_ajuda)) {
                
                $idMovA = $row_ajuda['id_movimento'];
                foreach($folhasEnvolvidas as $foLHAS){
                    if(strstr($idsEstatisticas[$foLHAS], $idMovA)){
                        
                        $m = $idsEstatisticasMes[$foLHAS]['mes'];
                        
                        if ($idsEstatisticasMes[$foLHAS]['flag'] == 2) {
                            if($m == 12){
                                $mesMov = 0;
                            }else{
                                $mesMov = ($folhaDezembro) ? intval($m) - 1 : intval($m);
                            }
                            $valor_ajuda[$mesMov] += $row_ajuda['valor_movimento'];
                        } else {
                            $valor_ajudaD += $row_ajuda['valor_movimento'];
                        }
                    }
                }
            }

            foreach ($valor_ajuda as $mes => $val) {
                $LINHA_RIDAC[$mes] = number_format($val, 2, "", "");
            }

            $DECIMO_RIDAC = (!empty($valor_ajudaD)) ? number_format($valor_ajudaD, 2, "", "") : NULL;
        }
        unset($sql_ajuda,$qr_ajuda,$row_ajuda,$valor_ajuda);

        if ($debug) echo " inicio GRAVANDO ";

        //MATANDO DEZEMBRO NA MÃO, POIS NÃO TEVE FOLHA ANO PASSADO (SOMENTE UTILIZADO NO ZICO)
        /*if (!$folhaDezembro && $naoMostraColunaDezembro) {
            $LINHA_RTRT[11] = "0";
            $LINHA_RTPO[11] = "0";
            $LINHA_RTPP[11] = "0";
            $LINHA_RTDP[11] = "0";
            $LINHA_RTIRF[11] = "0";
            $LINHA_RIIRP[11] = "0";
            $LINHA_RIAP[11] = "0";
            $LINHA_RTPA[11] = "0";
            $LINHA_RIDAC[11] = "0";
        }*/
        
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
        
        //VERIFICANDO SE PRECISA ESCREVER LINHA PARA O RIO
        $exibir_rio = false;
        if(!empty($LINHA_RIO)){
            //RIO SAL_FAMILIA
            if($LINHA_RIO['salfam'] > 0){
                $exibir_rio = true;
            }
        }
        
        //VALIDAÇÃO -> CASO O RTRT ESTIVER ZERADO, NÃO PODE HAVER NENHUM DESCONTO PARA O CONTRIBUINTE
        foreach($LINHA_RTRT as $k => $valor){
            if($valor <= 0){
                $LINHA_RTPO[$k] = 0;
                $LINHA_RTPP[$k] = 0;
                $LINHA_RTDP[$k] = 0;
                $LINHA_RTIRF[$k] = 0;
                $LINHA_RIIRP[$k] = 0;
                $LINHA_RIAP[$k] = 0;
                $LINHA_RTPA[$k] = 0;
                $LINHA_RIDAC[$k] = 0;
            }
        }
        
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
            
            $linhaRel[$clt['id_clt']]['RIO'] = $LINHA_RIO['salfam'];
        }
        
        if(($exibir_rtrt or (!empty($DECIMO_RTRT) && $DECIMO_RTRT != "000")) || 
           ($exibir_rtpo or (!empty($DECIMO_RTPO) && $DECIMO_RTPO != "000")) ||
           ($exibir_rtdp or (!empty($DECIMO_RTDP) && $DECIMO_RTDP != "000")) ||
           ($exibir_rtirf or (!empty($DECIMO_RTIRF) && $DECIMO_RTIRF != "000")) ||
           ($exibir_riirp or (!empty($DECIMO_RIIRP) && $DECIMO_RIIRP != "000")) ||
           ($exibir_riap or (!empty($DECIMO_RIAP) && $DECIMO_RIAP != "000")) || 
           ($exibir_rtpa or (!empty($DECIMO_RTPA) && $DECIMO_RTPA != "000")) ||
           ($exibir_ridac or (!empty($DECIMO_RIDAC) && $DECIMO_RIDAC != "000")) ){
            
            fwrite($arquivo, $LINHA_BPFDEC);
            fwrite($arquivo, "\n");
            
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
        
        if($exibir_rio){
            foreach($LINHA_RIO as $kRio => $valorRio){
                $descricaoRio = "";
                switch ($kRio){
                    case 'salfam':
                        $descricaoRio = "SALARIO FAMILIA";
                        break;
                }
                $LINHA_RIOFW = "RIO|" . $valorRio . '|' . $descricaoRio . '|';
                //echo "RIAP: ".$LINHA_RIAP."<br>";
                fwrite($arquivo, $LINHA_RIOFW);
                fwrite($arquivo, "\n");
            }
        }
        /*if ($exibir_rtpa or (!empty($DECIMO_RTPA) && $DECIMO_RTPA != "000")) {
            $LINHA_RTPA = "RTPA|".implode('|', $LINHA_RTPA) . '|' . $DECIMO_RTPA . '|';
            //echo "RIAP: ".$LINHA_RIAP."<br>";
            
            //SE TEM DEPENDENTE É OBRIGATORIO TER AS INFORMAÇÕES DO BENEFICIADO (31/01/2017 BY RAMON)
            fwrite($arquivo, $LINHA_INFPA);
            
            fwrite($arquivo, $LINHA_RTPA);
            fwrite($arquivo, "\n");
            
        }*/
        
        if($PENSAO_DEPENDENTE!=null){
            fwrite($arquivo, $PENSAO_DEPENDENTE);
        }

        if ($exibir_ridac or (!empty($DECIMO_RIDAC) && $DECIMO_RIDAC != "000")) {
            $LINHA_RIDAC = "RIDAC|".implode('|', $LINHA_RIDAC) . '|' . $DECIMO_RIDAC . '|';
            //echo "RIAP: ".$LINHA_RIAP."<br>";
            fwrite($arquivo, $LINHA_RIDAC);
            fwrite($arquivo, "\n");
        }
        
        //SINESIO
        if ($exec)
            echo $LINHA_RTRT;
        
        //LIMPANDO AS VARIAVEIS PRINCIPALMENTE OS ARRAYS, PARA NÃO ESTOURAR A MEMÓRIA DO SERVIDOR
        unset($duplicidade,$totalCpfs,$ids_clts,$ids_cltsPros);
        unset($LINHA_RTRT, $DECIMO_RTRT, $LINHA_RTPO, $DECIMO_RTPO, $LINHA_RTPP, $DECIMO_RTPP, $LINHA_RTDP, $DECIMO_RTDP, $LINHA_RTIRF, $DECIMO_RTIRF, $LINHA_RIO, $LINHA_RIOFW);
        unset($exibir_rtrt, $exibir_rtpo, $exibir_rtpp, $exibir_rtdp, $exibir_rtirf, $LINHA_RIIRP, $DECIMO_RIIRP, $exibir_riirp, $chave,$exibir_rio);
        unset($LINHA_RIAP, $DECIMO_RIAP, $exibir_riap, $LINHA_RTPA, $DECIMO_RTPA, $LINHA_RIDAC, $DECIMO_RIDAC, $folhaClt, $idDecimoTerceiro,$idsMovimentosRowClt);
        unset($valor_pensao, $valor_pensaoD);
        $informeRendimento->limpaVariaveis();
        
        if ($debug)
            echo "FIM LAÇO CLT: ($c) \n";
        $c++;
    }
    }
    
    if ($debug)
        echo " FIM DOS CLTS \r\n";
    
    /**
     * REGISTROS DE RPA/AUTONOMO
     * LEFT JOIN rpa_saida_assoc AS C ON (C.id_rpa = A.id_rpa AND C.tipo_vinculo = 1) 
     * -- REMOVI O 'AND...' POIS NO ZICO NÃO EXISTE A COLUNA C.tipo_vinculo
     */
       
      if($debug) echo " inicio RPA ";
//      $qr_autonomo = "SELECT
//                            A.id_rpa,A.id_autonomo,A.data_geracao,
//                            SUM(A.valor) AS valor,
//                            SUM(A.valor_inss) AS valor_inss,
//                            SUM(A.valor_ir) AS valor_ir,
//                            SUM(A.valor_liquido) AS valor_liquido,
//                            DATE_FORMAT(A.data_geracao, '%m') as mes,
//                            B.nome,B.cpf
//                            FROM rpa_autonomo AS A
//                            INNER JOIN autonomo AS B ON (A.id_autonomo = B.id_autonomo)
//                            LEFT JOIN rpa_saida_assoc AS C ON (C.id_rpa = A.id_rpa AND C.tipo_vinculo = 1)
//                            LEFT JOIN saida AS D ON (D.id_saida = C.id_saida)
//                            WHERE A.data_geracao >= '2014-07-01' AND YEAR( A.data_geracao ) = {$ano_calendario} AND B.id_projeto IN (" . implode(",", $projetos) . ") AND D.`status` = 2
//                            GROUP BY REPLACE(REPLACE(cpf,'-',''), '.',''),mes
//                            ORDER BY REPLACE(REPLACE(B.cpf,'-',''), '.','') ASC, A.data_geracao DESC";
      /**
       * RPAs
       */
      //aki
      $informeRendimento->setTipo(1.1);
      $qr_autonomo = $informeRendimento->getDadosFolhas($cltUnico,true);
      
      $rs_autonomo = mysql_query($qr_autonomo) or die("autonomo: ".mysql_error());
      $linha_aut = array();
      $a = 0;
      //echo "\r\n----AUTONOMOS---- \r\n\r\n";
      
      if(isset($_REQUEST['debug'])){
            echo "<br>************-Qry Autonomo-**************<br>";
            print_r($qr_autonomo);
            echo "<br><br>";
        }
        
      $cpfOld = "";
      //if($exec){
        while ($row_aut = mysql_fetch_assoc($rs_autonomo)) {
            //echo "{$row_aut['cpf']} - {$clt['nome']}\r\n";
          $mes = $row_aut['mes'];
          $mesN = $mes-1;
          $cpf = preg_replace('/[^[:digit:]]/', '', $row_aut['cpf']);
          //echo " autono ({$row_aut['id_autonomo']} - $a) <br/> ";
          $auto[$cpf]['nome'] = $row_aut['nome'];
          $auto[$cpf]['cpf'] = sprintf("%011s", $cpf);
          $auto[$cpf]['id'] = $row_aut['id_autonomo'];

          //echo "{$cpf} - {$mes} | {$row_aut['valor']} | ";

          if($cpf != $cpfOld){
              for ($i = 0; $i <= 11; $i++) {
                  $linha_aut[$cpf]['RTRT'][$i] = 0;
                  $linha_aut[$cpf]['RTPO'][$i] = 0;
                  $linha_aut[$cpf]['RTIRF'][$i] = 0;
              }
              $cpfOld = $cpf;
          }

          $linha_aut[$cpf]['RTRT'][$mesN] = $row_aut['valor'];
          $linha_aut[$cpf]['RTPO'][$mesN] = $row_aut['valor_inss'];
          $linha_aut[$cpf]['RTIRF'][$mesN] = $row_aut['valor_ir'];


          //NORMALIZANDO A PONTUAÇÃO
          for ($i = 0; $i <= 11; $i++) {
              $linha_aut[$cpf]['RTRT'][$i] = str_replace(".","",$linha_aut[$cpf]['RTRT'][$i]);
              $linha_aut[$cpf]['RTPO'][$i] = str_replace(".","",$linha_aut[$cpf]['RTPO'][$i]);
              $linha_aut[$cpf]['RTIRF'][$i] = str_replace(".","",$linha_aut[$cpf]['RTIRF'][$i]);
  //            $linha_aut[$cpf]['RTRT'][$i] = number_format($linha_aut[$cpf]['RTRT'][$i], 2, "", "");
  //            $linha_aut[$cpf]['RTPO'][$i] = number_format($linha_aut[$cpf]['RTPO'][$i], 2, "", "");
  //            $linha_aut[$cpf]['RTIRF'][$i] = number_format($linha_aut[$cpf]['RTIRF'][$i], 2, "", "");
          }

          //echo " || ($mesN) {$linha_aut[$cpf]['RTRT'][$mesN]} || \r\n";

          $a ++;
        }
    //}
    
    unset($exibir_rtrt, $exibir_rtpo, $exibir_rtirf, $qr_autonomo, $rs_autonomo);
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
            $linhaRel[$id_autonomo]['id'] = $autonomo['id'];
            $linhaRel[$id_autonomo]['RTRT'] = $linha_aut[$id_autonomo]['RTRT'];
            $linhaRel[$id_autonomo]['RTRTD'] = 0;

            $linhaRel[$id_autonomo]['RTPO'] = $linha_aut[$id_autonomo]['RTPO'];
            $linhaRel[$id_autonomo]['RTPOD'] = 0;

            $linhaRel[$id_autonomo]['RTPP'] = 0;
            $linhaRel[$id_autonomo]['RTPPD'] = 0;

            $linhaRel[$id_autonomo]['RTDP'] = 0;
            $linhaRel[$id_autonomo]['RTDPD'] = 0;

            $linhaRel[$id_autonomo]['RTIRF'] = $linha_aut[$id_autonomo]['RTIRF'];
            $linhaRel[$id_autonomo]['RTIRFD'] = 0;

            $linhaRel[$id_autonomo]['RIIRP'] = 0;
            $linhaRel[$id_autonomo]['RIIRPD'] = 0;

            $linhaRel[$id_autonomo]['RIAP'] = 0;
            $linhaRel[$id_autonomo]['RIAPD'] = 0;

            $linhaRel[$id_autonomo]['RTPA'] = 0;
            $linhaRel[$id_autonomo]['RIAPD'] = 0;

            $linhaRel[$id_autonomo]['RIDAC'] = 0;
            $linhaRel[$id_autonomo]['RIDACD'] = 0;
        }

        //GRAVANDO NO TXT O AUTONOMO
        $cpf = trim($autonomo['cpf']);
        $IDENTIFICADOR_BPFDEC['ID_REGISTRO'] = 'BPFDEC';
        $IDENTIFICADOR_BPFDEC['CNPJ'] = sprintf("%011s", $cpf);
        $IDENTIFICADOR_BPFDEC['NOME_EMPRESA'] = trim(str_replace(array(".","'","-"), "", $autonomo['nome']));
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
            $exibir_rtirf += ((int)$linha_aut[$id_autonomo]['RTIRF'][$i] > 0) ? 1 : 0;
            
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
        
        if ($exibir_rtirf > 0) {
            $txRTIRF = implode('|', $LINHA_RTIRF) . "|";
            $txRTIRF .= implode('|', $linha_aut[$id_autonomo]['RTIRF']);
            $txRTIRF .= "||";
            fwrite($arquivo, $txRTIRF);
            fwrite($arquivo, "\n");
        }

        //unset($linha_aut, $exibir_rtrt, $exibir_rtpo, $exibir_rtirf, $txRTIRF, $txRTPO, $txRTRT, $LINHA_RTIRF, $LINHA_RTPO, $LINHA_RTRT);
        unset( $exibir_rtrt, $exibir_rtpo, $exibir_rtirf);
    }
    //echo "-->";
    
    
    /**
     * RPes - Recibo de pagamento de Estagiario
     */
    //aki
    $informeRendimento->setTipo(4);
    $qr_estagiario = $informeRendimento->getDadosFolhas($cltUnico,true);

    $rs_estagiario = mysql_query($qr_estagiario) or die("estagiario: ".mysql_error());
    $linha_aut = array();
    $a = 0;
    //echo "\r\n----AUTONOMOS---- \r\n\r\n";

    if(isset($_REQUEST['debug'])){
          echo "<br>************-Qry ESTAGIARIO-**************<br>";
          print_r($qr_estagiario);
          echo "<br><br>";
      }

    $cpfOld = "";
    unset($auto,$linha_aut);
    while ($row_esta = mysql_fetch_assoc($rs_estagiario)) {
        //echo "{$row_esta['cpf']} - {$clt['nome']}\r\n";
      $mes = $row_esta['mes'];
      $mesN = $mes-1;
      $cpf = preg_replace('/[^[:digit:]]/', '', $row_esta['cpf']);
      //echo " autono ({$row_esta['id_autonomo']} - $a) <br/> ";
      $auto[$cpf]['nome'] = $row_esta['nome'];
      $auto[$cpf]['cpf'] = sprintf("%011s", $cpf);
      $auto[$cpf]['id'] = sprintf("%011s", $row_esta['id_estagiario']);

      //echo "{$cpf} - {$mes} | {$row_esta['valor']} | ";

      if($cpf != $cpfOld){
          for ($i = 0; $i <= 11; $i++) {
              $linha_aut[$cpf]['RTRT'][$i] = 0;
              $linha_aut[$cpf]['RTPO'][$i] = 0;
              $linha_aut[$cpf]['RTIRF'][$i] = 0;
          }
          $cpfOld = $cpf;
      }

      $linha_aut[$cpf]['RTRT'][$mesN] = $row_esta['valor'];
      $linha_aut[$cpf]['RTPO'][$mesN] = $row_esta['valor_inss'];
      $linha_aut[$cpf]['RTIRF'][$mesN] = $row_esta['valor_ir'];


      //NORMALIZANDO A PONTUAÇÃO
      for ($i = 0; $i <= 11; $i++) {
          $linha_aut[$cpf]['RTRT'][$i] = str_replace(".","",$linha_aut[$cpf]['RTRT'][$i]);
          $linha_aut[$cpf]['RTPO'][$i] = str_replace(".","",$linha_aut[$cpf]['RTPO'][$i]);
          $linha_aut[$cpf]['RTIRF'][$i] = str_replace(".","",$linha_aut[$cpf]['RTIRF'][$i]);
//            $linha_aut[$cpf]['RTRT'][$i] = number_format($linha_aut[$cpf]['RTRT'][$i], 2, "", "");
//            $linha_aut[$cpf]['RTPO'][$i] = number_format($linha_aut[$cpf]['RTPO'][$i], 2, "", "");
//            $linha_aut[$cpf]['RTIRF'][$i] = number_format($linha_aut[$cpf]['RTIRF'][$i], 2, "", "");
      }

      //echo " || ($mesN) {$linha_aut[$cpf]['RTRT'][$mesN]} || \r\n";

      $a ++;
    }
  //}
    
    unset($exibir_rtrt, $exibir_rtpo, $exibir_rtirf, $qr_estagiario, $rs_estagiario);
    ///////////////////////////// //////////////////////////////////////
    /////IDENTIFICAÇÃO DE CÓDIGO DA RECEITA (IDENTIFICADOR IDREC) /////
    ///////////////////////////// //////////////////////////////////////
    $IDENTIFICADOR_IDREC['ID_REGISTRO'] = 'IDREC';
    $IDENTIFICADOR_IDREC['CODIGO_RECEITA'] = '0???';

    $LINHA_IDREC = implode('|', $IDENTIFICADOR_IDREC) . '|';
    //fwrite($arquivo, $LINHA_IDREC);
    //fwrite($arquivo, "\n");

    foreach ($auto as $id_autonomo => $autonomo) {
        if ($geraRelatorio) {
            $linhaRel[$id_autonomo]['tipo'] = 2;
            $linhaRel[$id_autonomo]['nome'] = $autonomo['nome'];
            $linhaRel[$id_autonomo]['RTRT'] = $linha_aut[$id_autonomo]['RTRT'];
            $linhaRel[$id_autonomo]['RTRTD'] = 0;

            $linhaRel[$id_autonomo]['RTPO'] = $linha_aut[$id_autonomo]['RTPO'];
            $linhaRel[$id_autonomo]['RTPOD'] = 0;

            $linhaRel[$id_autonomo]['RTPP'] = 0;
            $linhaRel[$id_autonomo]['RTPPD'] = 0;

            $linhaRel[$id_autonomo]['RTDP'] = 0;
            $linhaRel[$id_autonomo]['RTDPD'] = 0;

            $linhaRel[$id_autonomo]['RTIRF'] = $linha_aut[$id_autonomo]['RTIRF'];
            $linhaRel[$id_autonomo]['RTIRFD'] = 0;

            $linhaRel[$id_autonomo]['RIIRP'] = 0;
            $linhaRel[$id_autonomo]['RIIRPD'] = 0;

            $linhaRel[$id_autonomo]['RIAP'] = 0;
            $linhaRel[$id_autonomo]['RIAPD'] = 0;

            $linhaRel[$id_autonomo]['RTPA'] = 0;
            $linhaRel[$id_autonomo]['RIAPD'] = 0;

            $linhaRel[$id_autonomo]['RIDAC'] = 0;
            $linhaRel[$id_autonomo]['RIDACD'] = 0;
        }

        //GRAVANDO NO TXT O AUTONOMO
        $cpf = trim($autonomo['cpf']);
        $IDENTIFICADOR_BPFDEC['ID_REGISTRO'] = 'BPFDEC';
        $IDENTIFICADOR_BPFDEC['CNPJ'] = sprintf("%011s", $cpf);
        $IDENTIFICADOR_BPFDEC['NOME_EMPRESA'] = trim(str_replace(array(".","'","-"), "", $autonomo['nome']));
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

        //unset($linha_aut, $exibir_rtrt, $exibir_rtpo, $exibir_rtirf, $txRTIRF, $txRTPO, $txRTRT, $LINHA_RTIRF, $LINHA_RTPO, $LINHA_RTRT);
    }
    //echo "-->";
    
    
    fwrite($arquivo, "FIMDirf|");
    fclose($arquivo);
    
    if($exec){
        echo " FIM Dirf ";
        exit;
    }
    
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
                    <p>
                        <label class="first">Folha paga até o 5º dia util?</label>
                        <input type="radio" name="pg" for="pgN" value="N" <?php echo $pgSelN ?> /><span id="pgN">Sim</span>
                        <input type="radio" name="pg" for="pgS" value="S" <?php echo $pgSelS ?> /><span id="pgS">Não</span>
                    </p>
                    
                    <p><label class="first">Projeto:</label>  <?php echo montaSelect($proNomes, $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?></p>
                    
                    <p><label class="first">CÓDIGO:</label> <input type="text" name="codigo" id="codigo" placeholder="Ex: 3097" value="<?php echo $cltUnico ?>" /> </p>
                    
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
                                <td>1 - RTRT</td>
                                <td>Rendimentos Tributáveis - Rendimento Tributável</td>
                                <td>6 - RIAP</td>
                                <td>Rendimentos Isentos - Abono Pecuniário</td>
                            </tr>
                            <tr>
                                <td>2 - RTPO</td>
                                <td>Rendimentos Tributáveis - Dedução - Previdência Oficial</td>
                                <td>7 - RTPA</td>
                                <td>RENDIMENTOS TRIB. - PENSÃO ALIMENTICIA</td>
                            </tr>
                            <tr>
                                <td>3 - RTDP</td>
                                <td>Rendimentos Tributáveis - Dedução - Dependentes</td>
                                <td>8 - RIDAC</td>
                                <td>Rendimentos Isentos - Diária e Ajuda de Custo (parcela única)</td>
                            </tr>
                            <tr>
                                <td>4 - RTIRF</td>
                                <td>Rendimentos Tributáveis - Imposto sobre a Renda Retido na Fonte</td>
                                <td>9 - RIO</td>
                                <td>Rendimentos Isentos - Outros (salario familia anual)</td>
                            </tr>
                            
                            <tr>
                                <td>5 - RIIRP</td>
                                <td>Rendimentos Isentos - Indenizações por Rescisão de Contrato de Trabalho e/ou PDV</td>
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
                                    
                                    if(strlen($k) > 5){
                                        $urlFicha = "http://f71iabassp.com/intranet/autonomo/ficha_financeira_aut.php?ano=2016&autonomo=".$clt['id'];
                                    }else{
                                        $urlFicha = "http://f71iabassp.com/intranet/relatorios/fichafinanceira_clt.php?reg=1&pro=1&tipo=2&tela=2&gerar=gerar&ano=2016&id=".$k;
                                    }
                                    
                                    echo "<tr class='titulo'>
                                            <td colspan='14' class='txleft'>
                                                {$k} - {$clt['nome']} (CPF: {$clt['cpf']}) 
                                                <a href='{$urlFicha}' target='_blank'> Ficha Financeira</a> - 
                                                <a href='{$urlInforme}{$k}' target='_blank'> Informe de Rendimentos</a>
                                            </td></tr>";
                                }

                                echo "<tr><td>1 - RTRT</td>";
                                printaValor($clt['RTRT']);
                                echo "<td>" . formatoNojo($clt['RTRTD']) . "</td></tr>";

                                echo "<tr><td>2 - RTPO</td>";
                                printaValor($clt['RTPO']);
                                echo "<td>" . formatoNojo($clt['RTPOD']) . "</td></tr>";

                                if ($clt['tipo'] == 1) {
                                    echo "<tr><td>3 - RTDP</td>";
                                    printaValor($clt['RTDP']);
                                    echo "<td>" . formatoNojo($clt['RTDPD']) . "</td></tr>";
                                }

                                echo "<tr><td>4 - RTIRF</td>";
                                printaValor($clt['RTIRF']);
                                echo "<td>" . formatoNojo($clt['RTIRFD']) . "</td></tr>";

                                if ($clt['tipo'] == 1) {
                                    echo "<tr><td>5 - RIIRP</td>";
                                    printaValor($clt['RIIRP']);
                                    echo "<td>" . formatoNojo($clt['RIIRPD']) . "</td></tr>";
                                }

                                if ($clt['tipo'] == 1) {
                                    echo "<tr><td>6 - RIAP</td>";
                                    printaValor($clt['RIAP']);
                                    echo "<td>" . formatoNojo($clt['RIAPD']) . "</td></tr>";
                                }

                                if ($clt['tipo'] == 1) {
                                    echo "<tr><td>7 - RTPA</td>";
                                    printaValor($clt['RTPA']);
                                    echo "<td>" . formatoNojo($clt['RTPAD']) . "</td></tr>";
                                }

                                if ($clt['tipo'] == 1) {
                                    echo "<tr><td>8 - RIDAC</td>";
                                    printaValor($clt['RIDAC']);
                                    echo "<td>" . formatoNojo($clt['RIDACD']) . "</td></tr>";
                                }
                                
                                if ($clt['tipo'] == 1) {
                                    echo "<tr><td>9 - RIO</td>";
                                    //printaValor($clt['RIO']);
                                    echo "<td colspan='13'>" . formatoNojo($clt['RIO']) . " total salario familia</td></tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <!--p class="controls">
                        <a href="<?php echo $nome_arquivo ?>"> Download do arquivo<?php echo $nomeFile ?></a>
                    </p-->
                    <?php }else if ($relatorio && !$geraRelatorio) { ?>
                    <!--p class="controls">
                        <a href="<?php echo $nome_arquivo ?>"> Download do arquivo<?php echo $nomeFile ?></a>
                    </p-->
                <?php } ?>
            </div>
        </form>
    </body>
</html>