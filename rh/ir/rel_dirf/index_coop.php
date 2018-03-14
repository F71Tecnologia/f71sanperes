<?php
$exec = false;
//CHAMADA PELO CRON, NAO TEM NINGUEM ON-LINE PASSO UM PARAMETRO
//SETO COOKIE NO MEU ID PARA RODAR SEM ESTAR LOGADO NO SISTEMA
if(isset($_REQUEST['of'])){
    $_COOKIE['logado'] = 179;
    $_REQUEST['filtrar'] = "filtrar";
    $_REQUEST['ano'] = $_REQUEST['of'];
    $_REQUEST['coop'] = '15.096.930/0001-68';
    $exec = true;
}

///print_r($_REQUEST);exit();

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

set_time_limit(0);

include("../../../conn.php");
include("../../../classes/funcionario.php");
include("../../../classes_permissoes/regioes.class.php");
include("../../../wfunction.php");

error_reporting(0);
ini_set('error_reporting', 0);
$usuario = carregaUsuario();

//VARIAVEIS DE CONTROLE
$relatorio = true;     //VARIAVEL PARA CONTROLAR TABELA QUE MOSTRA OS RESULTADOS DA CONSULTA
$limitado = false;      //LIMITA A QNT DE LINHAS "LIMIT 0,150"; //Registros encontrados: 2.794
$debug = false;         //IMPRIME LINHAS DE DEBUGS EM VARIOS LUGARES DO CODIGO
$geraRelatorio = true;  //SE FOR VERDADEIRO CRIA O ARRAY PARA VISUALIZAÇÃO NA TELA
$decimoAntigo = true;  //DECIMO ANTIGO, SE FOR TRUE SELECIONA TODOS OS 13 (1,2,3) E SOMA ENTRE ELES, SE FOR FALSO PEGA SO (2,3) E NAO SOMA
$arrayPensaoAlimenticia = array('6004','7009','50222');

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

//SELECIONA OS MASTERS
$rowMaster = montaQueryFirst("master", "cnpj,razao,nome", "id_master = {$usuario['id_master']}");

//SELECIONA AS REGIÕES DO MASTER
$rsRegioes = montaQuery("regioes", "id_regiao", "id_master = {$usuario['id_master']}");

foreach($rsRegioes as $rowRegiao){
    $ids_Regioes[] = $rowRegiao['id_regiao'];
}

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
    $proNomes[$emp['id_projeto']] = $pros[$emp['id_projeto']];
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

if (isset($_REQUEST['filtrar'])) {
    $CnpjCoop  = $_REQUEST['coop'];
    $ano_calendario = $_REQUEST['ano'];
    $ano_referencia = $ano_calendario + 1;
    $ano_anterior = $ano_calendario - 1;
    $tipo_arquivo = 1; //COOPERADO SOMENTE
    
    //$master = mysql_result(mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$id_regiao'"), 0) or die(mysql_error());
    $master = $_POST['id_master'];
    $n_arquivo = $CnpjCoop . '_' . $ano_referencia . '.txt';
    $nome_arquivo = 'arquivos_coop/' . $n_arquivo;
    $nome_arquivo = normalizaNometoFile($nome_arquivo);
    
    // AND id_regiao IN (" . implode(",", $ids_Regioes) . ")
    $rsCoops = montaQuery("cooperativas", "id_coop,gera_dirf", "cnpj='{$CnpjCoop}'"); /**,null,null,"array",true**/
    $idsCoops = array();
    $idCoopMaster = null;

    foreach ($rsCoops as $coop) {
        $idsCoops[] = $coop['id_coop'];
        if ($coop['gera_dirf'] == 1) {
            $idCoopMaster = $coop['id_coop'];
        }
    }
    
    $qr_empresa = mysql_query("SELECT   REPLACE(REPLACE(REPLACE(cnpj,'/',''),'.',''),'-','')as cnpj, 
                                        SUBSTR(REPLACE(REPLACE(nome,'?',''),',',''),1,150)as nome,
                                        cpfd,contador_cpf, contador_nome, contador_tel_ddd, contador_tel_num, contador_fax,contador_email
                                  FROM  cooperativas WHERE id_regiao = '{$usuario['id_regiao']}' AND cnpj = '{$CnpjCoop}'") OR die(mysql_error());
    $empresa = mysql_fetch_assoc($qr_empresa);
    
    $ano_ini = $ano_anterior;
    $ano_fim = $ano_calendario;
    
    //exit($ano_anterior . " , " . $ano_calendario);
    
    $whereDezembro = "  AND ( (B.mes = 12 AND B.ano = {$ano_ini} AND B.terceiro = 0) OR ((B.mes <> 12 AND B.ano = {$ano_fim}) OR (B.mes = 12 AND B.ano = {$ano_fim} AND B.terceiro = 1)))";
    
    $ok = true;
    //if (!empty($tipo_arquivo)) {
    if ($ok) {

        //////////////////////////////////////                   
        /////CABEÇALHO (IDENTIFICADOR DIRF /////
        ////////////////////////////////////////
        
        $identificadorLeiaute = array("2013" => "7C2DE7J", "2014" => "F8UCL6S", "2015" => "M1LB5V2", "2016" => "L35QJS2");
        
        $IDENTIFICADOR_DIRF['ID_REGISTRO'] = 'DIRF';
        $IDENTIFICADOR_DIRF['ANO_REFERENCIA'] = $ano_referencia;
        $IDENTIFICADOR_DIRF['ANO_CALENDARIO'] = $ano_calendario;
        $IDENTIFICADOR_DIRF['IDENTIFICADOR_RETIFICADORA'] = 'N';
        $IDENTIFICADOR_DIRF['NUMERO_RECIBO'] = NULL;
        $IDENTIFICADOR_DIRF['IDENTIFICADOR_ESTRUTURA_LEIAUTE'] = $identificadorLeiaute[$ano_referencia];

        $LINHA_ID_DIRF = implode('|', $IDENTIFICADOR_DIRF) . '|';

        //////////////////////////////////////                   
        /////RESPONSÁVEL (IDENTIFICADOR RESPO /////
        ////////////////////////////////////////
        $IDENTIFICADOR_RESPO['ID_REGISTRO'] = 'RESPO';
        $IDENTIFICADOR_RESPO['CPF'] = $empresa['contador_cpf'];
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
        $IDENTIFICADOR_DECPJ['CNPJ'] = $empresa['cnpj'];
        $IDENTIFICADOR_DECPJ['NOME_EMPRESA'] = str_replace('-', '', $empresa['nome']);
        $IDENTIFICADOR_DECPJ['NATUREZA_DECLARANTE'] = 0;
        $IDENTIFICADOR_DECPJ['CPF_RESPONSAVEL'] = $empresa['cpfd'];
        $IDENTIFICADOR_DECPJ['INDICADOR_SOCIO'] = 'N';
        $IDENTIFICADOR_DECPJ['INDICADOR_DECLARANTE_DEPOSITARIO'] = 'N';
        $IDENTIFICADOR_DECPJ['INDICADOR_DECLARANTE_INSTITUICAO'] = 'N';
        $IDENTIFICADOR_DECPJ['INDICADOR_DECLARANTE_RENDIMENTOS'] = 'N';
        $IDENTIFICADOR_DECPJ['INDICADOR_PLANO_PRIVADO'] = 'N';
        $IDENTIFICADOR_DECPJ['INDICADOR_PAGAMENTOS'] = 'N';
        $IDENTIFICADOR_DECPJ['INDICADOR_PAGAMENTOS_OLIMPIADAS'] = 'N';
        $IDENTIFICADOR_DECPJ['INDICADOR_SITUACAO_ESPECIAL'] = 'N';
        $IDENTIFICADOR_DECPJ['DATA_EVENTO'] = '';

        $LINHA_DECPJ = implode('|', $IDENTIFICADOR_DECPJ) . '|';

        ///////////////////////////// //////////////////////////////////////                   
        /////IDENTIFICAÇÃO DE CÓDIGO DA RECEITA (IDENTIFICADOR IDREC) /////
        ///////////////////////////// //////////////////////////////////////  
        $IDENTIFICADOR_IDREC['ID_REGISTRO'] = 'IDREC';
        $IDENTIFICADOR_IDREC['CODIGO_RECEITA'] = '0588';

        $LINHA_IDREC = implode('|', $IDENTIFICADOR_IDREC) . '|';

        $arquivo = fopen("arquivos_coop/".$nome_arquivo, 'w');

        fwrite($arquivo, $LINHA_ID_DIRF);
        fwrite($arquivo, "\n");
        fwrite($arquivo, $LINHA_ID_RESPO);
        fwrite($arquivo, "\n");
        fwrite($arquivo, $LINHA_DECPJ);
        fwrite($arquivo, "\n");
        fwrite($arquivo, $LINHA_IDREC);
        fwrite($arquivo, "\n");

        if ($tipo_arquivo == 1) {
            ///////////////////////////// //////////////////////////////////////                   
            /////////// SELECIONANDO OS ID DOS COOPERADOS///////////////////////
            ///////////////////////////// //////////////////////////////////////  
            $qrAut = "SELECT C.id_autonomo, C.nome, REPLACE(REPLACE(C.cpf,'-',''), '.','') as cpf
                                   FROM folhas as A
                                   INNER JOIN folha_cooperado as B
                                   ON A.id_folha = B.id_folha
                                   INNER JOIN  autonomo as C
                                   ON B.id_autonomo = C.id_autonomo
                                   WHERE A.ano = '{$ano_calendario}' AND A.coop IN(" . implode(",", $idsCoops) . ")
                                   AND A.status = 3 AND A.contratacao = 3 AND B.status = 3
                                   GROUP BY C.cpf
                                   ORDER BY cpf ASC";
                                   //REMOVIDO 
                                   //AND A.regiao IN (" . implode(",", $ids_Regioes) . ")
            
            //if($aut['id_autonomo'] == 11394){
               // exit($qrAut);
            //} 
            
            //exit($qrAut);
            $qr_aut = mysql_query($qrAut);
            while ($aut = mysql_fetch_assoc($qr_aut)) {
                
                $sqlDuplicado = "SELECT id_autonomo,cpf FROM autonomo WHERE REPLACE(REPLACE(cpf,'-',''), '.','') = {$aut['cpf']}";
                $rsDuplicado = mysql_query($sqlDuplicado);
                $idsCoop = array();
                if(mysql_num_rows($rsDuplicado) > 1){
                    while($rowDuplicado = mysql_fetch_assoc($rsDuplicado)){
                        $idsCoop[] = $rowDuplicado['id_autonomo'];
                    }
                }else{
                    $idsCoop[] = $aut['id_autonomo'];
                }
                
                //DADOS DO CLT 
                $IDENTIFICADOR_BPFDEC['ID_REGISTRO'] = 'BPFDEC';
                $IDENTIFICADOR_BPFDEC['CPF'] = trim($aut['cpf']);
                $IDENTIFICADOR_BPFDEC['NOME_TRAB'] = trim(RemoveCaracteres($aut['nome']));
                $IDENTIFICADOR_BPFDEC['DATA_ATRIBUIDA'] = '';
                $LINHA_BPFDEC = implode('|', $IDENTIFICADOR_BPFDEC) . '|';
                fwrite($arquivo, $LINHA_BPFDEC);
                fwrite($arquivo, "\n");

                
                //SELECIONANDO OS VALORES MENSAIS
//                $qr_valores = "SELECT A.id_autonomo, A.nome, A.cpf ,  B.projeto, B.terceiro, B.tipo_terceiro, A.mes, A.ano,
//                                        IF(A.ano=2014,0,CAST(A.mes AS signed)) as mesEdit, A.salario, A.adicional, 
//                                        IF(adicional != '', A.salario + CAST(REPLACE(A.adicional, ',', '.') AS DECIMAL(13,2)), A.salario) AS total_rend, 
//                                        A.inss, A.irrf, A.ajuda_custo
//                                        FROM folha_cooperado AS A
//                                        INNER JOIN folhas AS B ON A.id_folha = B.id_folha
//                                        WHERE A.id_autonomo IN(".  implode(",", $idsCoop).") AND B.coop IN (" . implode(",", $idsCoops) . ")
//                                              AND B.`status` = 3 
//                                              $whereDezembro ORDER BY A.nome, B.mes, B.ano";
                $qr_valores = "SELECT *,
                                SUM(salario) AS salario,
                                SUM(adicional) AS adicional, 
                                SUM(total_rend) AS total_rend,
                                SUM(inss) AS inss, 
                                SUM(irrf) AS irrf, 
                                SUM(ajuda_custo) AS ajuda_custo

                         FROM (
                                SELECT A.id_autonomo, A.nome, A.cpf, B.projeto, B.terceiro, B.tipo_terceiro, A.mes, A.ano, IF(A.ano=2014,0, CAST(A.mes AS signed)) AS mesEdit, 

                                A.salario, 
                                A.adicional, 
                                IF(adicional != '', A.salario + CAST(REPLACE(A.adicional, ',', '.') AS DECIMAL(13,2)), A.salario) AS total_rend, 
                                A.inss, 
                                A.irrf, 
                                A.ajuda_custo

                                FROM folha_cooperado AS A
                                INNER JOIN folhas AS B ON A.id_folha = B.id_folha
                                WHERE A.id_autonomo IN(".  implode(",", $idsCoop).") AND B.coop IN (" . implode(',', $idsCoops) . ") AND B.`status` = 3  $whereDezembro ORDER BY A.nome, B.mes, B.ano
                        ) AS temp

                        GROUP BY mes,ano";
                
//                if(in_array("11393", $idsCoop)){
//                    exit($qr_valores);
//                }  
                
                $rs_valores = mysql_query($qr_valores) or die(mysql_error());
                
                while($row_coop = mysql_fetch_assoc($rs_valores)){
                    
//                    echo "<pre>";
//                        print_r($row_coop);
//                    echo "</pre>";
                    
                    if($row_coop['terceiro']==0){
                        
                        $mes = intval($row_coop['mesEdit']);
                        $LINHA_RTRT[$mes]   += number_format($row_coop['total_rend'],2,'','');
                        $LINHA_RTPO[$mes]   += number_format($row_coop['inss'],2,'','');
                        $LINHA_RTPP[$mes]   = NULL;
                        $LINHA_RTDP[$mes]   = NULL;
                        $LINHA_RTIRF[$mes]  += number_format($row_coop['irrf'],2,'','');
                        $LINHA_RIIRP[$mes]  = NULL;
                        $LINHA_RIDAC[$mes]  += number_format($row_coop['ajuda_custo'],2,'','');
                    }else{
                        $DECIMO_RTRT    += number_format($row_coop['total_rend'],2,'','');
                        $DECIMO_RTPO    += number_format($row_coop['inss'],2,'','');
                        $DECIMO_RTPP    = NULL;
                        $DECIMO_RTDP    = NULL;
                        $DECIMO_RTIRF   += number_format($row_coop['irrf'],2,'','');
                        $DECIMO_RIDAC   = NULL;
                    }
                }
                
                /*
                for ($i = 1; $i < 13; $i++) {
                    
                    
                    $qr_valores = mysql_query("select A.salario , A.adicional, 
                                                IF( adicional != '',
                                                REPLACE(REPLACE(format(salario+adicional,2),',',''),'.','') ,
                                                REPLACE(REPLACE(format(salario,2),',',''),'.',''))  as total_rend,
                                                REPLACE(REPLACE(inss,',',''),'.','') as inss,
                                                REPLACE(REPLACE(irrf,',',''),'.','') as irrf,
                                                REPLACE(REPLACE(ajuda_custo,',',''),'.','') as ajuda_custo,
                                                A.mes
                                                from folha_cooperado as A
                                                INNER JOIN folhas as B                                                    
                                                ON A.id_folha = B.id_folha 
                                                WHERE A.id_autonomo = {$aut['id_autonomo']} AND A.mes = '" . sprintf('%02d', $i) . "' 
                                                AND A.ano = '$ano_calendario' AND B.terceiro = 0 AND A.status=3 AND B.coop IN (" . implode(",", $idsCoops) . ");") or die(mysql_error());

                    $row_valor = mysql_fetch_assoc($qr_valores);

                    $LINHA_RTRT[] = $row_valor['total_rend'];
                    $LINHA_RTPO[] = $row_valor['inss'];
                    $LINHA_RTPP[] = NULL;
                    $LINHA_RTDP[] = '';
                    $LINHA_RTIRF[] = $row_valor['irrf'];
                    $LINHA_RIIRP[] = NULL;
                    $LINHA_RIAP[] = NULL;
                    $LINHA_RIDAC[] = $row_valor['ajuda_custo'];
                    
                }*/
                /*
                ////DÉCIMO TERCEIRO
                $qr_valores_dt = mysql_query("select IF( adicional != '',
                                                REPLACE(REPLACE(format(salario+adicional,2),',',''),'.','') ,
                                                REPLACE(REPLACE(format(salario,2),',',''),'.',''))  as total_rend,
                                                REPLACE(REPLACE(inss,',',''),'.','') as inss,
                                                REPLACE(REPLACE(irrf,',',''),'.','') as irrf,
                                                REPLACE(REPLACE(ajuda_custo,',',''),'.','') as ajuda_custo
                                                FROM folha_cooperado as A
                                                INNER JOIN folhas as B
                                                ON A.id_folha = B.id_folha 
                                                WHERE A.id_autonomo = $aut[id_autonomo]  AND A.ano = '$ano_calendario' AND B.terceiro = 1;") or die(mysql_error());
                while ($row_valor_dt = mysql_fetch_assoc($qr_valores_dt)) {

                    $DECIMO_RTRT += $row_valor_dt['total_rend'];
                    $DECIMO_RTPO += $row_valor_dt['inss'];
                    $DECIMO_RTPP += NULL;
                    $DECIMO_RTDP += '';
                    $DECIMO_RTIRF += $row_valor_dt['irrf'];
                    $DECIMO_RIDAC += '';
                }

                */
                
                
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
                    /*if (!array_key_exists($i, $LINHA_RTPA)) {
                        $LINHA_RTPA[$i] = 0;
                    }*/
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
                //ksort($LINHA_RTPA);
                ksort($LINHA_RIDAC);
                
                //VERIFICANDO QUAIS LINHAS TEM VALORES, PARA NÃO IMPRIMIR NO TXT AQUELAS Q NÃO TEM NADA
                $exibir_rtrt = validaValor($LINHA_RTRT);
                $exibir_rtpo = validaValor($LINHA_RTPO);
                $exibir_rtpp = validaValor($LINHA_RTPP);
                $exibir_rtdp = validaValor($LINHA_RTDP);
                $exibir_rtirf = validaValor($LINHA_RTIRF);
                $exibir_riirp = validaValor($LINHA_RIIRP);
                $exibir_riap = validaValor($LINHA_RIAP);
                //$exibir_rtpa = validaValor($LINHA_RTPA);
                $exibir_ridac = validaValor($LINHA_RIDAC);

                if (!empty($exibir_rtrt) or !empty($DECIMO_RTRT)) {
                    $LINHA_RTRT = "RTRT|".implode('|', $LINHA_RTRT) . '|' . $DECIMO_RTRT . '|';
                    fwrite($arquivo, $LINHA_RTRT);
                    fwrite($arquivo, "\n");
                }

                if (!empty($exibir_rtpo) or !empty($DECIMO_RTPO)) {
                    $LINHA_RTPO = "RTPO|".implode('|', $LINHA_RTPO) . '|' . $DECIMO_RTPO . '|';
                    fwrite($arquivo, $LINHA_RTPO);
                    fwrite($arquivo, "\n");
                }

                if (!empty($exibir_rtpp) or !empty($DECIMO_RTPP)) {
                    $LINHA_RTPP = "RTPP|".implode('|', $LINHA_RTPP) . '|' . $DECIMO_RTPP . '|';
                    fwrite($arquivo, $LINHA_RTPP);
                    fwrite($arquivo, "\n");
                }

                if (!empty($exibir_rtdp) or !empty($DECIMO_RTDP)) {
                    $LINHA_RTDP = "RTDP|".implode('|', $LINHA_RTDP) . '|' . $DECIMO_RTDP . '|';
                    fwrite($arquivo, $LINHA_RTDP);
                    fwrite($arquivo, "\n");
                }

                if (!empty($exibir_rtirf) or !empty($DECIMO_RTIRF)) {
                    $LINHA_RTIRF = "RTIRF|".implode('|', $LINHA_RTIRF) . '|' . $DECIMO_RTIRF . '|';
                    fwrite($arquivo, $LINHA_RTIRF);
                    fwrite($arquivo, "\n");
                }

                if (!empty($exibir_riirp) or !empty($DECIMO_RIIRP)) {
                    $LINHA_RIIRP = "RIIRP|".implode('|', $LINHA_RIIRP) . '|' . $DECIMO_RIIRP . '|';
                    fwrite($arquivo, $LINHA_RIIRP);
                    fwrite($arquivo, "\n");
                }

                if (!empty($exibir_riap) or !empty($DECIMO_RIAP)) {
                    $LINHA_RIAP = "RIAP|".implode('|', $LINHA_RIAP) . '|' . $DECIMO_RIAP . '|';
                    fwrite($arquivo, $LINHA_RIAP);
                    fwrite($arquivo, "\n");
                }

                if (!empty($exibir_ridac)) {
                    $LINHA_RIDAC = "RIDAC|".implode('|', $LINHA_RIDAC) . '||';
                    fwrite($arquivo, $LINHA_RIDAC);
                    fwrite($arquivo, "\n");
                }
                
                unset($DECIMO_RIDAC, $LINHA_RIDAC, $exibir_ridac, $LINHA_RTRT, $DECIMO_RTRT, $LINHA_RTPO, $DECIMO_RTPO, $LINHA_RTPP, $DECIMO_RTPP, $LINHA_RTDP, $DECIMO_RTDP, $LINHA_RTIRF, $DECIMO_RTIRF, $exibir_rtrt, $exibir_rtpo, $exibir_rtpp, $exibir_rtdp, $exibir_rtirf, $LINHA_RIIRP, $DECIMO_RIIRP, $exibir_riirp, $chave, $LINHA_RIAP, $DECIMO_RIAP, $exibir_riap);
            
            }
        }//FIM LINHAS CLT  
        fwrite($arquivo, "FIMDirf|");
        fclose($arquivo);

        /*    mysql_query("INSERT INTO dirf (id_master, ano_calendario, data_geracao, gerado_por, arquivo_clt,arquivo_autonomo, arquivo_prestador)
          VALUES
          ('$master', '$ano_calendario', NOW(), '$_COOKIE[logado]', '$checked_clt', '$checked_autonomo', '$checked_prestador')") or die(mysql_error());
         */

//        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
//        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
//        header("Cache-Control: no-store, no-cache, must-revalidate");
//        header("Cache-Control: post-check=0, pre-check=0", false);
//        header("Pragma: no-cache");
//        header("Content-type: application/x-msdownload");
//        header("Content-Length: " . filesize($nome_arquivo));
//        header("Content-Disposition: attachment; filename={$n_arquivo}");
//        flush();

//        readfile($nome_arquivo);
//        exit;
        
    }
}

//CARREGANDO DADOS PARA OS SELECTS
//ANO
$optAnos = anosArray(2009, date('Y'));
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');

//COOPERATIVAS VINCULADAS AS REGIÕES DO MASTER E QUE TEM A FLAG GERA_DARF
$rsCoops = montaQuery("cooperativas", "cnpj,nome", "id_regiao IN (" . implode(",", $ids_Regioes) . ") AND gera_dirf = 1", null, null, "array", false, "cnpj");
$optCoops = array("-1" => "« Selecione »");

foreach ($rsCoops as $valor) {
    $optCoops[$valor['cnpj']] = $valor['cnpj'] . " - " . $valor['nome'];
}
$coopSel = (isset($_REQUEST['coop'])) ? $_REQUEST['coop'] : null;
?>
<html>
    <head>
        <title>Gerar DIRF Cooperado</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../../../net1.css" rel="stylesheet" type="text/css">
        <script src="../../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="../../../jquery/jquery.tools.min.js" type="text/javascript" ></script>
        <script src="../../../js/global.js" type="text/javascript" ></script>
        <script>
            $(function() {
                /*$('#form').submit(function(){
                 
                 // var checkbox = $('input[name=tipo_arquivo]:checked');
                 alert(checkbox);
                 return false;
                 });*/


            });

        </script>


    </head>
    <body class="novaintra">       
        <div id="content">
            <div id="head">
                <img src="../../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
                <div class="fleft">
                    <h2>DIRF</h2>
                    <p>Gerar arquivo de DIRF</p>
                </div>
            </div>
            <br class="clear">
            <form  name="form" action="" method="post" id="form">
                <fieldset>
                    <legend>DIRF</legend>
                    <p><label class="first">Ano Base:</label> <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano')); ?></p>
                    <p><label class="first">Cooperativa:</label> <?php echo montaSelect($optCoops, $coopSel, array('name' => "coop", 'id' => 'coop', 'style' => 'width:350px')); ?></p>
                    <p class="controls clear">
                        <input type="hidden" name="id_master" value="<?php echo $id_master; ?>"/>
                        <input type="submit" name="filtrar" value="Filtrar" id="filtrar"/>
                    </p>
                </fieldset>
            </form>
            
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
            
        </div>
    </body>
</html>