<?php

class SefipClass {

    public $brancos = NULL;
    public $fimLinha = "*";
    public $caracteres = array(".", "º", ",", "-", "_", "{", "}", "[", "]", "(", ")", ";", ":", "\"", "\'", "/", "*", "&", "\\", "@", "$", "&");
    public $mes;
    public $ano;
    public $prazoFGTS;
    public $prazoINSS;
    public $dtHoje;
    public $id_master;
    public $id_regiao;
    public $id_projeto;
    public $terceiro;
    public $ano_ini;
    public $ano_fim;
    public $a;
    public $cbo_autonomo = "2251";
    public $permissao_folha_aberta = array(353,354);
    
    public function __construct($mes, $ano, $id_master, $terceiro) {
        $this->mes = $mes;
        $this->ano = $ano;
        $this->prazoFGTS = date('Y-m-d', mktime("0", "0", "0", $mes + 1, 07, $ano)); // PRAZO 07/MES+1/ANO 
        $this->prazoINSS = date('Y-m-d', mktime('0','0','0', $mes + 1, 10, $ano)); // PRAZO 10/MES+1/ANO 
        $this->dtHoje = date("Y-m-d", time());        
        $this->id_master = $id_master;
        $this->terceiro = $terceiro;
    }
    
    function setId_regiao($id_regiao) {
        $this->id_regiao = $id_regiao;
    }
    
    function setId_projeto($id_projeto) {
        $this->id_projeto = $id_projeto;
    }
        
    public function getAnosSefip($id_master) {
        $qry = "SELECT MIN(A.ano) AS primeiro_ano, MAX(A.ano) AS ultimo_ano
            FROM rh_folha AS A
            LEFT JOIN regioes AS B ON(A.regiao = B.id_regiao)
            LEFT JOIN master AS C ON(B.id_master = C.id_master)
            WHERE C.id_master = {$id_master}";
        $sql = mysql_query($qry) or die(mysql_error());
        $res = mysql_fetch_assoc($sql);
        
        $this->ano_ini = $res['primeiro_ano'];
        $this->ano_fim = $res['ultimo_ano'];
    }
    
    public static function getFPAS($id) {
        $qry = "SELECT *
            FROM fpas AS A
            WHERE A.id = {$id}";
        $sql = mysql_query($qry) or die(mysql_error());
        $res = mysql_fetch_assoc($sql);
        
        return $res;
    }
    
    public function getRegioesMaster($id_master) {
        $qry = "SELECT *
            FROM regioes
            WHERE id_master = {$id_master}";
        $sql = mysql_query($qry) or die(mysql_error());

        return $sql;
    }
    
    public function getListaSefip($ano, $master, $terceiro = null) {
//        if(!empty($terceiro)){
//            $where = "AND B.terceiro = 1 AND (B.mes = '12' OR (B.mes >= '11' AND B.tipo_terceiro = 3))";
//        }else{
//            $where = "AND B.terceiro <> 1";
//        }
        if($_COOKIE['aberta'] != 1){
            $where .= " AND B.status = 3 AND A.status = 3";
        }
        
        $qry = "SELECT A.*, COUNT(A.id_clt) AS tot_participantes, C.id_master AS regiao_master, B.tipo_terceiro, B.terceiro, D.cnpj
            FROM rh_folha_proc AS A
            LEFT JOIN rh_folha AS B ON(A.id_folha = B.id_folha)
            LEFT JOIN regioes AS C ON(C.id_regiao = A.id_regiao)
            LEFT JOIN master AS D ON(D.id_master = C.id_master)
            WHERE 
            /*(B.terceiro = 2 OR (B.terceiro = 1 AND A.mes = 12))*/
            B.terceiro = 2
            AND B.ano = '{$ano}' AND C.id_master = '{$master}' {$where}
            GROUP BY A.mes, B.terceiro";
        $sql = mysql_query($qry) or die(mysql_error());
        
        if($_COOKIE['debug'] == 666){
            echo "////////////////////////////////";
            echo "QUERY LISTA ANOS";
            echo "////////////////////////////////";
            print_array($qry);
        }
        
        return $sql;
    }
    
    public function getListaSefipByCnpj($ano, $master, $mes, $terceiro = null) {        
        if($_COOKIE['aberta'] != 1){
            $where .= " AND B.status = 3 ";
        }
        
        if($terceiro == 1){
            $where .= " AND D.terceiro = 1 AND tipo_terceiro IN (2,3)";
        } else {
            $where .= " AND D.terceiro = 2 ";
        }
        
        $qry = "SELECT A.*,B.mes, GROUP_CONCAT(DISTINCT C.nome) projetos, B.ano, COUNT(B.id_clt) AS tot_participantes
        FROM rh_folha_proc B
        LEFT JOIN rhempresa A ON (A.id_projeto = B.id_projeto)
        LEFT JOIN projeto C ON (A.id_projeto = C.id_projeto)
        LEFT JOIN rh_folha D ON (B.id_folha = D.id_folha)
        WHERE B.ano = {$ano} AND B.mes IN ({$mes}) AND B.status != 0 {$where}
        GROUP BY A.cnpj, B.mes;";
        $sql = mysql_query($qry) or die(mysql_error());
        
        if($_COOKIE['debug'] == 666){
            echo "////////////////////////////////";
            echo "QUERY LISTA CNPJ";
            echo "////////////////////////////////";
            print_array($qry);
        }
        
        return $sql;
    }
    
    public function getListaSefipIndividual($ano, $master, $mes, $terceiro = null) {
        if(!empty($terceiro)){
            $where = "AND B.terceiro = 1 AND (B.mes = '12' OR (B.mes >= '11' AND B.tipo_terceiro = 3))";
        }else{
            $where = "AND B.terceiro <> 1";
        }
        
        $qry = "SELECT A.*, D.nome AS projeto_nome, COUNT(A.id_clt) AS qtd_participantes, B.terceiro AS folha_terceiro
            FROM rh_folha_proc AS A
            LEFT JOIN rh_folha AS B ON(A.id_folha = B.id_folha)
            LEFT JOIN regioes AS C ON(C.id_regiao = A.id_regiao)
            LEFT JOIN projeto AS D ON(D.id_projeto = A.id_projeto)
            WHERE B.ano = {$ano} AND C.id_master = {$master} AND B.status = 3 AND A.status = 3 AND B.mes IN({$mes}) {$where}
            GROUP BY A.id_projeto
            ORDER BY D.nome";
        $sql = mysql_query($qry) or die(mysql_error());
        
        return $sql;
    }
    
    public function getEmpregador($regiao, $projeto = null, $cnpj) { // FALTA DEF. O CAMPO B.ADMINISTRACAO DOS OUTROS PROJETOS 
        if($this->id_master == 6){
            $empresa = 28;
        }elseif($this->id_master != 6 && $this->id_master != 8){
            $empresa = 1;
        }else{
            $empresa = 38;
        }
        
//        if(!empty($projeto)){
//            $qry = "SELECT A.id_empresa, IF (A.cnpj IS NOT NULL, '1', '2') AS tpInscResp, IF (A.cnpj IS NOT NULL, A.cnpj, '') AS inscResp, A.razao, A.responsavel, 
//                CONCAT(A.logradouro,' ',A.numero,' ', A.complemento) AS endereco, B.endereco AS endereco2, A.bairro, A.cep, A.cidade, A.uf, A.tel, A.email, A.cnae
//                FROM rhempresa AS A
//                LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
//                WHERE A.id_projeto = {$projeto};";
//        }else{
//            $qry = "SELECT A.id_empresa, IF (A.cnpj IS NOT NULL, '1', '2') AS tpInscResp, IF (A.cnpj IS NOT NULL, A.cnpj, '') AS inscResp, A.razao, A.responsavel, 
//                CONCAT(A.logradouro,' ',A.numero,' ', A.complemento) AS endereco, B.endereco AS endereco2, A.bairro, A.cep, A.cidade, A.uf, A.tel, A.email, A.cnae
//                FROM rhempresa AS A
//                LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
//                WHERE A.id_empresa = {$empresa};";
//        }
        $qry = "SELECT A.id_empresa, IF (A.cnpj IS NOT NULL, '1', '2') AS tpInscResp, IF (A.cnpj IS NOT NULL, A.cnpj, '') AS inscResp, A.razao, A.responsavel, 
                CONCAT(A.logradouro,' ',A.numero,' ', A.complemento) AS endereco, B.endereco AS endereco2, A.bairro, A.cep, A.cidade, A.uf, A.tel, A.email, A.cnae, A.terceiros, A.fpas
                FROM rhempresa AS A
                LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
                WHERE A.cnpj = '$cnpj' GROUP BY A.cnpj;";
        
        $sql = mysql_query($qry)or die ("ERRO getEmpresa");
        return $sql;
    }
    
    public function getTomador($regiao, $projeto) {
        $qry = "SELECT A.id_empresa, IF (A.cnpj IS NOT NULL, '1', '2') AS tpInscResp, IF (A.cnpj IS NOT NULL, A.cnpj, '') AS inscResp, A.razao, A.responsavel, 
            CONCAT(A.logradouro,' ',A.numero,' ', A.complemento) AS endereco, B.endereco AS endereco2, A.bairro, A.cep, A.cidade, A.uf, A.tel, A.email, A.cnae
            FROM rhempresa AS A
            LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
            WHERE A.id_regiao = '{$regiao}' AND A.id_projeto = '{$projeto}'";
        
        if($_COOKIE['debug'] == "sefip"){
            echo "<br><br><strong>SQL getTomador</strong><br><br>";
            echo $qry;
        }
        
        $sql = mysql_query($qry) or die ("ERRO getTomador");
        return $sql;
    }
    
    public function getSalMaternidade_Familia($id_folha) {
        //$id_folha = 82;
//        $qry = "SELECT IF( A.data_proc > '2010-06-30', SUM(B.a6005), SUM(B.salbase)) AS salMaternidade, IF( A.data_proc > '2010-06-30', SUM(B.a5022), SUM(B.sallimpo_real))  AS salFamilia
//                FROM rh_folha AS A
//                LEFT JOIN rh_folha_proc AS B ON (A.id_folha = B.id_folha)
//                WHERE A.id_folha IN ({$id_folha}) AND A.`status` = 3;";
        $qry = "SELECT IF( A.data_proc > '2010-06-30', SUM(B.a6005 + IFNULL(SMMA.valor_movimento,0)), SUM(B.salbase)) AS salMaternidade, IF( A.data_proc > '2010-06-30', SUM(B.a5022), SUM(B.sallimpo_real))  AS salFamilia
                FROM rh_folha AS A
                LEFT JOIN rh_folha_proc AS B ON (A.id_folha = B.id_folha)
                LEFT JOIN (SELECT * FROM rh_movimentos_clt WHERE cod_movimento IN (6009) AND status > 0) AS SMMA ON (B.id_clt = SMMA.id_clt AND A.mes = SMMA.mes_mov AND A.ano = SMMA.ano_mov)
                WHERE A.id_folha IN ({$id_folha}) AND A.`status` = 3;";
        $sql = mysql_query($qry)or die ("ERRO getSalMaternidade_Familia");
        return $sql;     
    }
    
    public function getIdFolhas($cnpj = null, $terceiro = 2) {
        if($_COOKIE['aberta'] != 1){
            $where .= " AND A.status = 3";
        }
        
        if($terceiro == 1){
            $where .= " AND A.terceiro = 1 AND tipo_terceiro IN (2,3)";
        } else {
            $where .= " AND A.terceiro = 2 ";
        }
        
        $where .= ($this->mes != 13) ? " AND A.mes = '$this->mes' " : null;
        
        $auxCnpj = (!empty($cnpj)) ? " AND projeto IN (SELECT id_projeto FROM rhempresa B WHERE B.cnpj = '{$cnpj}') " : null;
        $qry = "SELECT GROUP_CONCAT(A.id_folha SEPARATOR ',') AS id_folha FROM rh_folha AS A WHERE A.ano = '$this->ano' {$where} $auxCnpj;";
        $sql = mysql_query($qry)or die ("ERRO getIdFolhas");
        if($_COOKIE['debug'] == 666){
            echo "////////////////////////////////";
            echo "QUERY getIdFolhas";
            echo "////////////////////////////////";
            print_array($qry);
        }
        return $sql;
    }
    
    public function getEmpregado($id_folha) {
        if($_COOKIE['aberta'] != 1){
            $aux = "AND A.status = 3 AND B.status = 3";
        }
        //$id_folha = 82;
        $sql_movimentos_estatistica = "SELECT ids_movimentos_estatisticas, projeto FROM rh_folha WHERE id_folha IN ({$id_folha})";
        $qry_movimentos_estatistica = mysql_query($sql_movimentos_estatistica);
        while($row_movimentos_estatistica = mysql_fetch_assoc($qry_movimentos_estatistica)){
            $array_ids_movimentos_estatisticas[] = $row_movimentos_estatistica['ids_movimentos_estatisticas'];
            $arrayIdsProjeto[] = $row_movimentos_estatistica['projeto'];
        }
        
        if(in_array(null, $array_ids_movimentos_estatisticas)){
            $array_ids_movimentos_estatisticas = "''";
        }else{
            $array_ids_movimentos_estatisticas = implode(',',$array_ids_movimentos_estatisticas);
        }
        
        $sql_13 = "SELECT GROUP_CONCAT(id_folha SEPARATOR ',') AS id_folha FROM rh_folha AS A WHERE A.ano = '2016'  AND A.status = 3 AND A.terceiro = 1 AND tipo_terceiro > 1 AND projeto IN (".implode(',',$arrayIdsProjeto).");";
        $qry_13 = mysql_query($sql_13);
        $row_13 = mysql_fetch_assoc($qry_13);
        if(!empty($row_13['id_folha'])){
//            $aux13_FROM = "LEFT JOIN (SELECT id_clt, base_inss FROM rh_folha_proc WHERE id_folha IN ({$row_13['id_folha']}) AND status = 3) AS DT ON (B.id_clt = DT.id_clt)";
//            $aux13_SELECT = " - IFNULL(DT.base_inss,0)";
        }
        
        $qry = "
            SELECT '' AS parte, REPLACE(REPLACE(H.pis,'.',''),'-','') AS pislimpo, '' AS data_entrada, H.id_autonomo AS id_trab, '' AS campo1, '' AS serie_ctps, H.nome, '' AS sefip_codigo, '' AS sefip_valor,
            CONCAT(H.endereco, ' ', H.numero, ' ', H.complemento) AS endereco, H.bairro, H.cep, H.cidade, H.uf, '' AS data_nasci, '{$this->cbo_autonomo}' AS cod, G.mes_competencia AS mes_folha_proc, G.ano_competencia AS ano_folha_proc,
            H.inss AS desconto_inss, SUM(G.valor_inss) AS valor_desconto_inss, H.tipo_inss AS tipo_desconto_inss, SUM(G.valor) AS base_inss, '' AS base_fgts, IF(G.valor_inss < (G.valor*0.11), '05', NULL) AS ocorrencia, IF(G.valor_inss < (G.valor*0.11), G.valor_inss, '') AS valDescSegurado,
            '' AS base_inss_13_rescisao, '' AS data_inicio, '' AS data_fim,  '' AS mes, '' AS ano, '' AS data_demi, '' AS status_clt, '' AS valor_ferias, '' AS soma, H.tipo_contratacao, '13' AS categoria, H.id_projeto, H.id_regiao, '' AS data_importacao,
            '' AS inss_rescisao
            FROM rpa_autonomo AS G
            LEFT JOIN autonomo AS H ON(G.id_autonomo = H.id_autonomo)
            WHERE /*H.id_regiao = '{$this->id_regiao}' AND*/ 
            G.id_projeto_pag IN (SELECT projeto FROM rh_folha WHERE id_folha IN ({$id_folha}))
            AND H.status_reg = 1
            AND G.mes_competencia = '{$this->mes}' AND G.ano_competencia = '{$this->ano}'
            AND REPLACE(REPLACE(H.pis,'.',''),'-','') NOT IN (SELECT REPLACE(REPLACE(pis,'.',''),'-','') FROM rh_folha_proc rfp LEFT JOIN rh_clt rc ON (rfp.id_clt = rc.id_clt) WHERE rfp.id_folha IN ({$id_folha}) AND rfp.status = 3)
            GROUP BY H.nome
            
            UNION
            
            SELECT A.parte, REPLACE(REPLACE(C.pis,'.',''),'-','') AS pislimpo, C.data_entrada, C.id_clt AS id_trab, C.campo1, C.serie_ctps, C.nome, D.sefip_codigo, D.sefip_valor, 
            CONCAT(C.endereco,' ',C.numero,' ', C.complemento) AS endereco, C.bairro, C.cep, C.cidade, C.uf, C.data_nasci, F.cod, B.mes AS mes_folha_proc, B.ano AS ano_folha_proc,
            C.desconto_inss, C.valor_desconto_inss, C.tipo_desconto_inss, (B.base_inss - IF(A.terceiro = 1, IFNULL(ADT.valor_movimento,0), 0)) AS base_inss, (B.fgts / 0.08) AS base_fgts, 
            IF(I.id_inss > 0, '05', NULL) AS ocorrencia, 
            IF(I.id_inss > 0,IF(A.terceiro = 2,B.a5020,B.a5031), NULL) AS valDescSegurado,
            IF((B.base_inss_13_rescisao $aux13_SELECT) > 0,(B.base_inss_13_rescisao $aux13_SELECT), 0.01) AS base_inss_13_rescisao, A.data_inicio, A.data_fim, A.mes, A.ano, C.data_demi, B.status_clt, B.valor_ferias, (B.valor_ferias+B.base_inss) as soma, C.tipo_contratacao, '01' AS categoria, C.id_projeto, C.id_regiao, C.data_importacao,
            B.inss_rescisao
            FROM rh_folha AS A
            LEFT JOIN rh_folha_proc AS B ON (A.id_folha = B.id_folha)
            LEFT JOIN rh_clt AS C ON (B.id_clt = C.id_clt)
            LEFT JOIN (SELECT sefip_id, sefip_codigo, sefip_valor FROM log WHERE sefip = '1' AND sefip_ano = '{$this->ano}' AND sefip_mes = '{$this->mes}' AND sefip_codigo != '' ORDER BY id_log DESC LIMIT 0,1 ) AS D ON (C.id_clt = sefip_id)
            LEFT JOIN curso AS E ON (E.id_curso = C.id_curso)
            LEFT JOIN rh_cbo AS F ON (F.id_cbo = E.cbo_codigo)
            LEFT JOIN rh_inss_outras_empresas AS I ON (I.id_clt = C.id_clt AND I.status = 1 AND A.data_inicio BETWEEN I.inicio AND I.fim)
            LEFT JOIN (SELECT id_clt, valor_movimento FROM rh_movimentos_clt WHERE cod_movimento = 80031 AND mes_mov = 14 AND ano_mov = '{$this->ano}' AND id_movimento IN (".$array_ids_movimentos_estatisticas.")) AS ADT ON (B.id_clt = ADT.id_clt)
            $aux13_FROM
            WHERE A.id_folha IN ({$id_folha}) $aux 
            ORDER BY pislimpo, categoria";
            
            if($_COOKIE['debug'] == 666){
                echo "////////////////////////////////";
                echo "QUERY getEmpregado";
                echo "////////////////////////////////";
                print_array($qry);
            }
            
//        $qry = "SELECT * FROM (
//                    SELECT A.parte, REPLACE(REPLACE(C.pis,'.',''),'-','') AS pislimpo, C.data_entrada, C.id_clt AS id_trab, C.campo1, C.serie_ctps, C.nome, D.sefip_codigo, D.sefip_valor, 
//                    CONCAT(C.endereco,' ',C.numero,' ', C.complemento) AS endereco, C.bairro, C.cep, C.cidade, C.uf, C.data_nasci, F.cod, B.mes AS mes_folha_proc, B.ano AS ano_folha_proc,
//                    C.desconto_inss, C.valor_desconto_inss, C.tipo_desconto_inss, B.base_inss, IF(C.desconto_inss = 1, '05', NULL) AS ocorrencia, (B.a5020+B.a5035+B.inss_dt+B.inss_rescisao) AS valDescSegurado,
//                    B.base_inss_13_rescisao, A.data_inicio, A.data_fim, A.mes, A.ano, C.data_demi, B.status_clt, B.valor_ferias, (B.valor_ferias+B.base_inss) as soma, C.tipo_contratacao, '01' AS categoria
//                    FROM rh_folha AS A
//                    LEFT JOIN rh_folha_proc AS B ON (A.id_folha = B.id_folha)
//                    LEFT JOIN rh_clt AS C ON (B.id_clt = C.id_clt)
//                    LEFT JOIN (
//                        SELECT sefip_id, sefip_codigo, sefip_valor
//                        FROM log
//                        WHERE sefip = '1' AND sefip_ano = '{$this->ano}' AND sefip_mes = '{$this->mes}' AND sefip_codigo != ''
//                        ORDER BY id_log DESC
//                        LIMIT 0,1
//                    ) AS D ON (C.id_clt = sefip_id)
//                    LEFT JOIN curso AS E ON (E.id_curso = C.id_curso)
//                    LEFT JOIN rh_cbo AS F ON (F.id_cbo = E.cbo_codigo)
//                    WHERE A.id_folha IN ({$id_folha}) AND A.status = 3 AND B.status = 3
//                    UNION 
//                    SELECT A.parte, REPLACE(REPLACE(C.pis,'.',''),'-','') AS pislimpo, C.data_entrada, C.id_clt AS id_trab, C.campo1, C.serie_ctps, C.nome, D.sefip_codigo, D.sefip_valor, 
//                    CONCAT(C.endereco,' ',C.numero,' ', C.complemento) AS endereco, C.bairro, C.cep, C.cidade, C.uf, C.data_nasci, F.cod, B.mes AS mes_folha_proc, B.ano AS ano_folha_proc,
//                    '' AS desconto_inss, '' AS valor_desconto_inss, '' AS tipo_desconto_inss, '' AS base_inss, '' AS ocorrencia, '' AS valDescSegurado,
//                    '' AS base_inss_13_rescisao, A.data_inicio, A.data_fim, A.mes, A.ano, C.data_demi, B.status_clt, '' AS valor_ferias, '' as soma, C.tipo_contratacao, '01' AS categoria
//                    FROM rh_folha AS A
//                    LEFT JOIN rh_folha_proc AS B ON (A.id_folha = B.id_folha)
//                    LEFT JOIN rh_clt AS C ON (B.id_clt = C.id_clt)
//                    LEFT JOIN (
//                        SELECT sefip_id, sefip_codigo, sefip_valor
//                        FROM log
//                        WHERE sefip = '1' AND sefip_ano = '{$this->ano}' AND sefip_mes = '{$this->mes}' AND sefip_codigo != ''
//                        ORDER BY id_log DESC
//                        LIMIT 0,1
//                    ) AS D ON (C.id_clt = sefip_id)
//                    LEFT JOIN curso AS E ON (E.id_curso = C.id_curso)
//                    LEFT JOIN rh_cbo AS F ON (F.id_cbo = E.cbo_codigo)
//                    WHERE A.mes = '{$this->mes}' AND A.ano = '{$this->ano}' AND A.terceiro = 1 AND A.status = 3 AND B.status = 3 /*AND A.projeto = {$this->id_projeto}*/
//                    AND B.id_clt NOT IN (SELECT id_clt FROM rh_folha_proc WHERE id_folha IN ({$id_folha}) AND status = 3)
//                    ) transf_meio_13
//                    GROUP BY id_trab
//            
//                
//            
//            UNION
//            
//            SELECT '' AS parte, REPLACE(REPLACE(H.pis,'.',''),'-','') AS pislimpo, '' AS data_entrada, H.id_autonomo AS id_trab, '' AS campo1, '' AS serie_ctps, H.nome, '' AS sefip_codigo, '' AS sefip_valor,
//            CONCAT(H.endereco, ' ', H.numero, ' ', H.complemento) AS endereco, H.bairro, H.cep, H.cidade, H.uf, '' AS data_nasci, '{$this->cbo_autonomo}' AS cod, G.mes_competencia AS mes_folha_proc, G.ano_competencia AS ano_folha_proc,
//            H.inss AS desconto_inss, SUM(G.valor_inss) AS valor_desconto_inss, H.tipo_inss AS tipo_desconto_inss, SUM(G.valor) AS base_inss, IF(G.valor_inss = '0,00', '05', NULL) AS ocorrencia, '' AS valDescSegurado,
//            '' AS base_inss_13_rescisao, '' AS data_inicio, '' AS data_fim,  '' AS mes, '' AS ano, '' AS data_demi, '' AS status_clt, '' AS valor_ferias, '' AS soma, H.tipo_contratacao, '13' AS categoria
//            FROM rpa_autonomo AS G
//            LEFT JOIN autonomo AS H ON(G.id_autonomo = H.id_autonomo)
//            WHERE /*H.id_regiao = '{$this->id_regiao}' AND H.id_projeto = '{$this->id_projeto}' AND*/ H.status_reg = 1
//            AND G.mes_competencia = '{$this->mes}' AND G.ano_competencia = '{$this->ano}'
//            GROUP BY G.id_autonomo
//            
//            ORDER BY pislimpo";
//        if($_COOKIE['logado'] == 353){
//            echo $qry."<br>";exit;
//        }    
        $sql = mysql_query($qry)or die (mysql_error());
        return $sql;
    }
    
    public function getSefip($id_folha) {
        $qry = "SELECT *
            FROM sefip
            WHERE folha = '{$id_folha}'";
        $sql = mysql_query($qry)or die ("ERRO getSefip");
        return $sql;
    }
    
    public function getDiasTrabalhadosByAno($id_trab, $id_projeto) {
//        if($_COOKIE['logado'] == 353){ $aux = ',2'; }
        $qry = "SELECT SUM(dias_trab) AS dias_trab
            FROM rh_folha_proc AS A
            LEFT JOIN rh_folha AS B ON (A.id_folha = B.id_folha)
            WHERE A.id_clt = '{$id_trab}'  AND A.status IN (3) AND A.ano = '{$this->ano}' AND B.status IN (3) AND B.terceiro = 2 AND B.projeto = '{$id_projeto}';";
//        echo $qry.'<br>';
        $sql = mysql_query($qry)or die ("ERRO getDiasTrabalhadosByAno");
        return $sql;
    }
    
    public function getDecimoTerceiroMes($id_trab, $id_projeto, $terceiro) {
        if($_COOKIE['aberta'] != 1){
            $aux1 = "AND A.status = 3";
            $aux2 = "AND A.status = 3 AND B.status = 3";
        }
        $mes = ($terceiro == 2) ? $this->mes : 12;
        $ano = $this->ano;
        $qry_folha = "SELECT *
                FROM rh_folha AS A
                WHERE A.mes = '{$mes}' AND A.ano = '{$ano}' AND A.status = 3 AND A.terceiro = 1 AND A.tipo_terceiro IN (1,2,3) AND A.projeto = '{$id_projeto}'";
        $sql_folha = mysql_query($qry_folha) or die(mysql_error());
        $res_folha = mysql_fetch_assoc($sql_folha);
        
        $tipo_decimo = $res_folha['tipo_terceiro'];
        
        $mesMov = 13;
        
        if($terceiro == 2){
//            $calc = "A.salliquido";
            $calc = ($tipo_decimo == 1) ? "A.salliquido" : "(A.base_inss - IFNULL(E.valor_movimento,0))";
        }else{
//            $calc = "A.salbase + A.rend/* - A.desco*/";
            $calc = ($tipo_decimo == 1) ? "A.base_inss" : "(A.salbase - IFNULL(E.valor_movimento,0))";
        }
        
        if($tipo_decimo == 1){ 
            $qry = "SELECT IF({$calc} > 0, {$calc}, IFNULL(C.valor_movimento,0)) decimo_terceiro
                    FROM rh_folha_proc AS A
                    LEFT JOIN rh_folha AS B ON (A.id_folha = B.id_folha)
                    LEFT JOIN (SELECT id_clt, valor_movimento FROM rh_movimentos_clt WHERE cod_movimento = '80030' AND mes_mov = '{$mesMov}' AND ano_mov = {$ano} AND id_movimento IN ({$res_folha['ids_movimentos_estatisticas']})) AS C ON (C.id_clt = A.id_clt)
                    LEFT JOIN (SELECT id_clt, valor_movimento FROM rh_movimentos_clt WHERE cod_movimento IN (80031,5050) AND mes_mov = '14' AND ano_mov = {$ano} AND id_movimento IN ({$res_folha['ids_movimentos_estatisticas']})) AS D ON (C.id_clt = A.id_clt)
                    LEFT JOIN (SELECT id_clt, valor_movimento FROM rh_movimentos_clt WHERE cod_movimento IN (5050) AND mes_mov = '14' AND ano_mov = {$ano} AND id_movimento IN ({$res_folha['ids_movimentos_estatisticas']})) AS E ON (E.id_clt = A.id_clt)
                    WHERE A.id_clt = '{$id_trab}' AND A.mes = '{$mes}' AND A.ano = '{$ano}' {$aux2} AND B.terceiro = 1 AND B.tipo_terceiro IN (1,2,3) AND B.projeto = '{$id_projeto}';";
        } else {
            $qry = "SELECT A1.id_clt, (A1.base_inss - IFNULL(C1.valor_movimento,0) - IFNULL(E.valor_movimento,0)) decimo_terceiro, A1.base_inss
                FROM rh_folha_proc A1
                LEFT JOIN rh_folha AS B1 ON (A1.id_folha = B1.id_folha)
                LEFT JOIN (SELECT id_clt, valor_movimento FROM rh_movimentos_clt WHERE cod_movimento = '80031' AND mes_mov = '14' AND id_movimento IN ({$res_folha['ids_movimentos_estatisticas']})) AS C1 ON (C1.id_clt = A1.id_clt)
                LEFT JOIN (SELECT id_clt, SUM(valor_movimento) valor_movimento FROM rh_movimentos_clt WHERE cod_movimento IN (5050,80031) AND mes_mov = '14' AND ano_mov = {$ano} AND id_movimento IN ({$res_folha['ids_movimentos_estatisticas']}) GROUP BY id_clt) AS E ON (E.id_clt = A1.id_clt)
                WHERE A1.id_clt = '{$id_trab}' AND A1.status = 3 AND B1.status = 3 AND B1.terceiro = 1 AND B1.tipo_terceiro IN (2,3) AND B1.projeto = '{$id_projeto}'";
        }
        if($_COOKIE['debug'] == 666){
            echo "////////////////////////////////";
            echo 'QUERY getDecimoTerceiroMes';
            echo "////////////////////////////////";
            print_array($qry);
        }
        $sql = mysql_query($qry)or die ("ERRO getDecimoTerceiroMes");
        
        return $sql;
    }
    
    public function getMedia13($id_clt) {
        $sql = "SELECT A.valor_movimento
            FROM rh_movimentos_clt AS A
            WHERE A.id_clt = {$id_clt} AND A.mes_mov = 16 AND A.ano_mov = {$this->ano} AND A.cod_movimento = 90031 AND A.`status` > 0";
        $qry = mysql_query($sql) or die(mysql_error());
        
        while($res = mysql_fetch_assoc($qry)){
            $media += $res['valor_movimento'];
        }
        
        return $media;
    }
    
    public function montaArrayStatusCodMovimento() {
        $qry = "SELECT codigo, cod_movimentacao FROM rhstatus WHERE status_Reg = 1 AND cod_movimentacao != '';";
        $sql = mysql_query($qry);
        while ($row = mysql_fetch_assoc($sql)) {
            $array[$row['codigo']] = $row['cod_movimentacao'];
        }
        return $array;
    }
    
    public function montaArrayRescisao() {
        $qry = "SELECT * FROM rhstatus WHERE status_Reg = 1 AND tipo = 'recisao' AND codigo NOT IN (60, 101);"; //sefipe mandou tirar o 81//  por motivo rescisão (exceto rescisão com justa causa), aposentadoria com quebra de vínculo ou falecimento
        $sql = mysql_query($qry);
        while ($row = mysql_fetch_assoc($sql)) {
            $array[] = $row['codigo'];
        }
        return $array;
    }
    
    public function getIdsCltAnteriores($id_folha) {
        $qry = "SELECT B.id_clt AS  id_cltAnteriores
            FROM sefip AS A
            INNER JOIN rh_folha_proc AS B ON (A.folha = B.id_folha)
            WHERE B.status = 3 AND B.id_folha IN ($id_folha)
            GROUP BY B.id_clt;";
        
        $sql = mysql_query($qry);
        return $sql;
    }
    
    public function getMovCartaInss() {
        $qry = "SELECT SUM(A.valor_movimento) AS total_carta
            FROM rh_movimentos_clt AS A
            WHERE A.id_mov = 398 AND A.status IN(1,5) AND A.mes_mov = {$this->mes} AND A.ano_mov = {$this->ano}";
        
        $sql = mysql_query($qry) or die(mysql_error());
        $res = mysql_fetch_assoc($sql);
        
        $valor = $res['total_carta'];
        
        return $valor;
    }
    
    public function gravaLog($id_regiao,$id_projeto,$id_folha,$usuario, $parte_folha) {
        $qry = "INSERT INTO sefip (mes, ano, regiao, projeto, folha, tipo_sefip, data, autor, parte_folha) VALUES ('{$this->mes}', '{$this->ano}', '{$id_regiao}', '{$id_projeto}', '{$id_folha}', '2', NOW(), '{$usuario}', '{$parte_folha}');";
        $sql = mysql_query($qry)or die("ERRO AO GRAVAR O LOG.");        
    }
    
    public function delSefip($folha){
        $qry = "DELETE FROM sefip WHERE folha = $folha";
        $sql = mysql_query($qry) or die("ERRO metodo delSefip");
        return $sql;
    }
    
    //REGISTRO TIPO 00 - INFORMAÇÕES DO RESPONSÁVEL
    public function montaReg00($arquivo, $responsavel) {        
        //01 TIPO DE REGISTRO
        $tpReg = "00";
        $tpReg = sprintf("%2s", $tpReg);
        fwrite($arquivo, $tpReg, 2);
        
        //02 BRANCOS
        $brancos = sprintf("%-51s", $this->brancos);
        fwrite($arquivo, $brancos, 51);
        
        //03 TIPO DE REMESSA
        $tpRemessa = "1";
        $tpRemessa = sprintf("%1s", $tpRemessa);
        fwrite($arquivo, $tpRemessa, 1);
        
        //04 TIPO DE INSCRIÇÃO - RESPONSÁVEL
        $tpInscResp = $responsavel['tpInscResp']; // CAMPO OBRIGATÓRIO 
        $tpInscResp = sprintf("%1s", $tpInscResp); // 1 - (CNPJ), 2 - (CEI), 3 - (CPF) OBS: SÓ PODE SER 3 PARA COD DE RECOLHIMENTO 418
        fwrite($arquivo, $tpInscResp, 1);
        
        //05 INSCRIÇÃO DO RESPONSÁVEL
        $inscResp = $responsavel['inscResp'];
        $inscResp = sprintf("%14s", RemoveCaracteres($inscResp));
        fwrite($arquivo, $inscResp, 14);

        //06 NOME RESPONSÁVEL (RAZÃO SOCIAL)
        $nomeResp = $responsavel['razao'];
        $nomeResp = sprintf("%-30s", RemoveAcentos(exPersonalizada($nomeResp, $this->caracteres)));
        fwrite($arquivo, $nomeResp, 30);

        //07 NOME PESSOA CONTATO
        $nomeContato = $responsavel['responsavel'];
        $nomeContato = sprintf("%-20s", RemoveAcentos(expersonalizada($nomeContato, $this->caracteres,array('[[:digit:]]'))));
        fwrite($arquivo, $nomeContato, 20);

        //08 LOGRADOURO, RUA, Nº, ANDAR, APARTAMENTO
        $endereco = $responsavel['endereco'];
        $endereco = sprintf("%-50s", RemoveAcentos(RemoveCaracteres(expersonalizada($endereco, $this->caracteres))));
        fwrite($arquivo, $endereco, 50);

        //09 BAIRRO
        $bairro = $responsavel['bairro'];
        $bairro = sprintf("%-20s", RemoveAcentos(expersonalizada($bairro, $this->caracteres)));
        fwrite($arquivo, $bairro, 20);

        //10 CEP
        $cep = $responsavel['cep'];
        $cep = sprintf("%8s", RemoveCaracteres($cep));
        fwrite($arquivo, $cep, 8);

        //11 CIDADE
        $cidade = RemoveAcentos($responsavel['cidade']);
        $cidade = sprintf("%-20s", expersonalizada($cidade, $this->caracteres));
        fwrite($arquivo, $cidade, 20);

        //12 UNIDADE DA FEDERAÇÃO
        $uf = $responsavel['uf'];
        $uf = sprintf("%-2s", $uf);
        fwrite($arquivo, $uf, 2);

        //13 TELEFONE CONTATO
        $telContato = $responsavel['tel'];
        $telContato = sprintf("%12s", RemoveCaracteres($telContato));
        fwrite($arquivo, $telContato, 12);

        //14 ENDEREÇO INTERNET CONTATO
        $endInternetContato = $responsavel['email'];
        $endInternetContato = sprintf("%-60s", $endInternetContato);
        fwrite($arquivo, $endInternetContato, 60);
                
        if($this->terceiro == 1){
            $mesCompetencia = 13;
        }else{
            $mesCompetencia = $this->mes;
        }
        
        //15 COMPETÊNCIA
        $competencia = $this->ano . $mesCompetencia;
        $competencia = sprintf("%6s", $competencia);
        fwrite($arquivo, $competencia, 6);
        
        //16 CÓDIGO DE RECOLHIMENTO
        $codRecolhimento = "115"; // TABELA DE CÓDIGO DE RECOLHIMENTO 
        $codRecolhimento = sprintf("%3s", $codRecolhimento);
        fwrite($arquivo, $codRecolhimento, 3);
        
        if ($this->terceiro == 1) {
            $indRecFGTS = ''; // NÃO PODE SER INFORMADO NA COMPETÊNCIA 13
            $modRecolhimento = 1; // DECLARAÇÃO AO FGTS E À PREVICÊNCIA
            $indRecINSS = 1; // (Previdência social no prazo)
            $dtRecolhimentoINSS = NULL;
        } else {
            if ($this->dtHoje > $this->prazoFGTS) {
                $indRecFGTS = 2; // (GRF em atraso)
                $dtRecolhimentoFGTS = $this->dtHoje; // INDICAR A DATA EFETIVA DO RECOLHIMENTO DO FGTS SEMPRE QUE FOR FEITO EM ATRASO                
            } else {
                $indRecFGTS = 1; // (GRF no prazo)
                $dtRecolhimentoFGTS = NULL; // NÃO PODE SER INFORMADO QUANDO O INDICADOR DE FGTS FOIR IGUAL A 1
            }
            if ($this->dtHoje > $this->prazoINSS) {
                $indRecINSS = 2; // (Previdência social em atraso)
                $dtRecolhimentoINSS = $this->dtHoje;
            } else {
                $indRecINSS = 1; // (Previdência social no prazo)
                $dtRecolhimentoINSS = NULL;
            }
            $modRecolhimento = NULL; // RECOLHIMENTO AO FGTS E DECLARAÇÃO À PREVIDÊNCIA
        }
        
        //17 INDICADOR DE RECOLHIMENTO FGTS
        $indRecFGTS = sprintf("%1s", $indRecFGTS);
        fwrite($arquivo, $indRecFGTS, 1);

        //18 MODALIDADE DO ARQUIVO
        $modRecolhimento = sprintf("%1s", $modRecolhimento);
        fwrite($arquivo, $modRecolhimento, 1);

        //19 DATA DE RECOLHIMENTO DO FGTS
        $dtRecolhimentoFGTS = sprintf("%8s", implode("", array_reverse(explode("-", $dtRecolhimentoFGTS)))); // DDMMAAAA
        fwrite($arquivo, $dtRecolhimentoFGTS, 8);

        //20 INDICADOR DE RECOLHIMENTO DA PREVIDÊNCIA SOCIAL
        $indRecINSS = sprintf("%1s", $indRecINSS);
        fwrite($arquivo, $indRecINSS, 1);

        //21 DATA DE RECOLHIMENTO DA PREVIDÊNCIA SOCIAL
        $dtRecolhimentoINSS = sprintf("%8s", implode("", array_reverse(explode("-", $dtRecolhimentoINSS)))); // DDMMAAAA
        fwrite($arquivo, $dtRecolhimentoINSS, 8);
        
        //22 INDICE DE RECOLHIMENTO EM ATRASO DA PREVIDÊNCIA SOCIAL
        $indiceRecolhimento = NULL;
        $indiceRecolhimento = sprintf("%7s", $indiceRecolhimento);
        fwrite($arquivo, $indiceRecolhimento, 7);
        
        //23 TIPO DE INSCRIÇÃO - FORNECEDOR FOLHA DE PAGAMENTO
        $tpInscFornecedor = 1; // CNPJ
        $tpInscFornecedor = sprintf("%1s", $tpInscFornecedor);
        fwrite($arquivo, $tpInscFornecedor, 1);
        
        //24 INSCRIÇÃO DO FORNECEDOR FOLHA DE PAGAMENTO
//        $inscFornecedor = "09652823000338"; // CNPJ F71
        $inscFornecedor = "015689180000138"; // CNPJ F71
        $inscFornecedor = sprintf("%014s", $inscFornecedor);
        fwrite($arquivo, $inscFornecedor, 14);
        
        //25 BRANCOS
        $brancos = sprintf("%-18s", $this->brancos);
        fwrite($arquivo, $brancos, 18);
        
        //26 FINAL DE LINHA
        $fimLinha = sprintf("%-1s", $this->fimLinha);
        fwrite($arquivo, $fimLinha, 1);
        
        fwrite($arquivo, "\r\n");
    }
    
    //REGISTRO TIPO 10 - INFORMAÇÕES DO EMPRESA
    public function montaReg10($arquivo, $responsavel, $salario = NULL) {
        //01 TIPO DE REGISTRO
        $tpReg = "10";
        $tpReg = sprintf("%2s", $tpReg);
        fwrite($arquivo, $tpReg, 2);
        
        //02 TIPO DE INSCRIÇÃO - EMPRESA
        $tpInscResp = $responsavel['tpInscResp']; // CAMPO OBRIGATÓRIO 
        $tpInscResp = sprintf("%1s", $tpInscResp); // 1 - (CNPJ), 2 - (CEI), 3 - (CPF) OBS: SÓ PODE SER 3 PARA COD DE RECOLHIMENTO 418
        fwrite($arquivo, $tpInscResp, 1);
        
        //03 INSCRIÇÃO DA EMPRESA
        $inscResp = $responsavel['inscResp'];
        $inscResp = sprintf("%14s", RemoveCaracteres($inscResp));
        fwrite($arquivo, $inscResp, 14);
        
        //04 ZEROS
        $zeros = sprintf("%036s", $this->brancos);
        fwrite($arquivo, $zeros, 36);
        
        //05 NOME EMPRESA/RAZÃO SOCIAL
        $nomeEmpresa = $responsavel['razao'];
        $nomeEmpresa = sprintf("%-40s", RemoveAcentos(RemoveAcentos(expersonalizada($nomeEmpresa, $this->caracteres))));
        fwrite($arquivo, $nomeEmpresa, 40);

        //06 LOGRADOURO, RUA, Nº, ANDAR, APT
        $endereco = (!empty($responsavel['endereco'])) ? $responsavel['endereco'] : $responsavel['endereco2'];
        $endereco = sprintf("%-50s", RemoveAcentos(RemoveAcentos(expersonalizada($endereco, $this->caracteres))));
        fwrite($arquivo, $endereco, 50);

        //07 BAIRRO
        $bairro = $responsavel['bairro'];
        $bairro = sprintf("%-20s", RemoveAcentos(expersonalizada($bairro, $this->caracteres)));
        fwrite($arquivo, $bairro, 20);

        //08 CEP
        $cep = $responsavel['cep'];
        $cep = sprintf("%8s", RemoveCaracteres($cep));
        fwrite($arquivo, $cep, 8);
        
        //09 CIDADE
        $cidade = $responsavel['cidade'];
        $cidade = sprintf("%-20s", RemoveAcentos(expersonalizada($cidade, $this->caracteres)));
        fwrite($arquivo, $cidade, 20);

        //10 UNIDADE DE FEDERAÇÃO
        $uf = $responsavel['uf'];
        $uf = sprintf("%-2s", $uf);
        fwrite($arquivo, $uf, 2);

        //11 TELEFONE
        $tel = $responsavel['tel'];
        $tel = sprintf("%12s", RemoveCaracteres($tel));
        fwrite($arquivo, $tel, 12);
        
        //12 INDICADOR DE ALTERAÇÃO DE ENDEREÇO
        $indAltEndereco = "N";
        $indAltEndereco = sprintf("%-1s", $indAltEndereco);
        fwrite($arquivo, $indAltEndereco, 1);
        
        //13 CNAE
        $cnae = $responsavel['cnae'];
        $cnae = sprintf("%7s", RemoveCaracteres($cnae));
        fwrite($arquivo, $cnae, 7);
        
        if ($this->terceiro == 1) {
            $indAltCnae = "N";
            $salMaternidade = NULL;
            $salFamilia = NULL;
        }else{
            $indAltCnae = "P";
            $salFamilia = $salario['salFamilia']; // TOTAL PAGO PELA EMPRESA
            $salMaternidade = $salario['salMaternidade']; // TOTAL PAGO PELA EMPRESA
        }
        
        //14 INDICADOR DE ALTERAÇÃO CNAE
        $indAltCnae = sprintf("%-1s",$indAltCnae);
        fwrite($arquivo, $indAltCnae, 1);
        
        //15 ALÍQUOTA RAT
        $alqRat = "10";
        $alqRat = sprintf("%2s", $alqRat);
        fwrite($arquivo, $alqRat, 2);
        
        //16 CÓDIGO DE CENTRALIZAÇÃO
        $codCentral = "0"; // INDICA EMPRESAS QUE CENTRALIZAM O RECOLHIMENTO DO FGTS
        $codCentral = sprintf("%1s", $codCentral); // 0 - NÃO CENTRALIZA; 1 - CENTRALIZADORA; 2 - CENTRALIZADA;
        fwrite($arquivo, $codCentral, 1);
        
        //17 SIMPLES
        $simples = '1';
        $simples = sprintf("%1s", $simples);
        fwrite($arquivo, $simples, 1);
        
        $fpas_res = SefipClass::getFPAS($responsavel['fpas']);

        //18 FPAS
        //$fpas = '515';
        $fpas = $fpas_res['codigo'];
        $fpas = sprintf("%3s", $fpas);
        fwrite($arquivo, $fpas, 3);

        //19 CÓDIGO DE OUTRAS ENTIDADES
        $codOutrasEntidades = $responsavel['terceiros'];
        $codOutrasEntidades = sprintf("%4s", $codOutrasEntidades);
        fwrite($arquivo, $codOutrasEntidades, 4);
        
        //20 CÓDIGO DE PAGAMENTO GPS
        $codPagGps = '2100';
        $codPagGps = sprintf("%4s", $codPagGps);
        fwrite($arquivo, $codPagGps, 4);

        //21 PERCENTUAL DE ISENÇÃO DE FILANTROPIA
        $percentualIsenFilantropia = NULL;
        $percentualIsenFilantropia = sprintf("%5s", $percentualIsenFilantropia);
        fwrite($arquivo, $percentualIsenFilantropia, 5);
        
        //22 SALÁRIO FAMÍLIA (TOTAL PAGO PELA EMPRESA)
        $salFamilia = sprintf("%015s", RemoveCaracteres($salFamilia));
        fwrite($arquivo, $salFamilia, 15);
        
        //23 SALÁRIO MATERNIDADE (TOTAL PAGO PELA EMPRESA)
        $salMaternidade = sprintf("%015s", RemoveCaracteres($salMaternidade));
        fwrite($arquivo, $salMaternidade, 15);
        
        //24 CONTRIBUIÇÃO DESCONTADA EMPREGADO REFERENTE À COMPETÊNCIA 13
        $contribDescEmpregado = NULL;
        $contribDescEmpregado = sprintf("%015s", $contribDescEmpregado);
        fwrite($arquivo, $contribDescEmpregado, 15);
        
        //25 INDICADOR DE VALOR NEGATIVO OU POSITIVO
        $indValNegOuPositivo = NULL;
        $indValNegOuPositivo = sprintf("%01s", $indValNegOuPositivo);
        fwrite($arquivo, $indValNegOuPositivo, 1);
        
        //26 VALOR DEVIDO À PREVIDÊNCIA SOCIAL REFERENTE À COMPETÊNCIA 13
        $valDevINSS = NULL;
        $valDevINSS = sprintf("%014s", $valDevINSS);
        fwrite($arquivo, $valDevINSS, 14);
        
        //27 BANCO
        $banco = NULL;
        $banco = sprintf("%3s", $banco);
        fwrite($arquivo, $banco, 3);

        //28 AGÊNCIA
        $agencia = NULL;
        $agencia = sprintf("%4s", $agencia);
        fwrite($arquivo, $agencia, 4);

        //29 CONTA CORRENTE
        $contaCorrente = NULL;
        $contaCorrente = sprintf("%9s", $contaCorrente);
        fwrite($arquivo, $contaCorrente, 9);

        //30 ZEROS
        $zeros = sprintf("%045s", $this->brancos);
        fwrite($arquivo, $zeros, 45);
        
        //31 BRANCOS
        $brancos = sprintf("%-4s", $this->brancos);
        fwrite($arquivo, $brancos, 18);
        
        //32 FINAL DE LINHA
        $fimLinha = sprintf("%-1s", $this->fimLinha);
        fwrite($arquivo, $fimLinha, 1);
        
        fwrite($arquivo, "\r\n");
    }
    
    //REGISTRO TIPO 12 - INFORMAÇÕES ADICIONAIS DO RECOLHIMENTO DA EMPRESA    
    public function montaReg12($arquivo, $responsavel) {
        //01 TIPO DE REGISTRO
        $tpReg = "12";
        $tpReg = sprintf("%2s", $tpReg);
        fwrite($arquivo, $tpReg, 2);
        
        //02 TIPO DE INSCRIÇÃO - EMPRESA
        $tpInscResp = $responsavel['tpInscResp']; // CAMPO OBRIGATÓRIO
        $tpInscResp = sprintf("%1s", $tpInscResp); // 1 - (CNPJ), 2 - (CEI), 3 - (CPF) OBS: SÓ PODE SER 3 PARA COD DE RECOLHIMENTO 418
        fwrite($arquivo, $tpInscResp, 1);
        
        //03 INSCRIÇÃO DA EMPRESA
        $inscResp = $responsavel['inscResp'];
        $inscResp = sprintf("%14s", RemoveCaracteres($inscResp));
        fwrite($arquivo, $inscResp, 14);
        
        //04 ZEROS
        $zeros = sprintf("%036s", $this->brancos);
        fwrite($arquivo, $zeros, 36);
        
        //05 DEDUÇÃO 13º SALÁRIO LICENÇA MATERNIDADE
        $deducao13LicMaternidade = NULL;
        $deducao13LicMaternidade = sprintf("%015s", $deducao13LicMaternidade);
        fwrite($arquivo, $deducao13LicMaternidade, 15);
        
        //06 RECEITA EVENTO DESPORTIVO/PATROCÍNIO
        $eventoDesportivo = NULL;
        $eventoDesportivo = sprintf("%015s", $eventoDesportivo);
        fwrite($arquivo, $eventoDesportivo, 15);
        
        if (!empty($eventoDesportivo) && $this->terceiro == 2){
            $indOrigemReceita = ""; // E - RECEITA REF. A ARRECADAÇÃO DE EVENTOS; P - RECEITA REF A PATROCÍNIO; A - RECEITA REF À ARRECADAÇÃO DE EVENTOS E PATROCÍNIOS
        }else{
            $indOrigemReceita = NULL;
        }
        
        //07 INDICATIVO ORIGEM DA RECEITA
        $indOrigemReceita = sprintf("%-1s", $indOrigemReceita);
        fwrite($arquivo, $indOrigemReceita, 1);
        
        //08 COMERCIALIZAÇÃO DA PRODUÇÃO - PESSO FÍSICA
        $comerciProducaoPF = NULL;
        $comerciProducaoPF = sprintf("%015s", $comerciProducaoPF);
        fwrite($arquivo, $comerciProducaoPF, 15);
        
        //09 COMERCIALIZAÇÃO DA PRODUÇÃO - PESSO JURÍDICA
        $comerciProducaoPJ = NULL;
        $comerciProducaoPJ = sprintf("%015s", $comerciProducaoPJ);
        fwrite($arquivo, $comerciProducaoPJ, 15);
        
        //10 OUTRAS INFORMAÇÕES PROCESSO
        $outrasInfProcesso = NULL;
        $outrasInfProcesso = sprintf("%11s", $outrasInfProcesso);
        fwrite($arquivo, $outrasInfProcesso, 11);
        
        //11 OUTRAS INFORMAÇÕES PROCESSO - ANO
        $outrasInfProcessoAno = NULL;
        $outrasInfProcessoAno = sprintf("%4s", $outrasInfProcessoAno);
        fwrite($arquivo, $outrasInfProcessoAno, 4);
        
        //12 OUTRAS INFORMAÇÕES VARA/JCJ
        $outrasInfVara = NULL;
        $outrasInfVara = sprintf("%5s", $outrasInfVara);
        fwrite($arquivo, $outrasInfVara, 5);
        
        //13 OUTRAS INFORMAÇÕES PERÍODO INÍCIO
        $outrasInfIni = NULL;
        $outrasInfIni = sprintf("%-6s", $outrasInfIni);
        fwrite($arquivo, $outrasInfIni, 6);
        
        //14 OUTRAS INFORMAÇÕES PERÍODO FIM
        $outrasInfFim = NULL;
        $outrasInfFim = sprintf("%-6s", $outrasInfFim);
        fwrite($arquivo, $outrasInfFim, 6);                
        
        /*
        if ($this->dtHoje < $this->prazoFGTS) {
            $compensacaoVal = ""; // PREENCHER COM UM VALOR 
        }else{
            $compensacaoVal = NULL;
        }
        */
        
        //15 COMPENSAÇÃO - VALOR CORRIGIDO
//        $compensacaoVal = $this->getMovCartaInss(); // CAMPO OPCIONAL
        $compensacaoVal = null; // CAMPO OPCIONAL
        $compensacaoVal = sprintf("%015s", RemoveCaracteres($compensacaoVal));
        fwrite($arquivo, $compensacaoVal, 15);
        
        if (!empty($compensacaoVal) && $compensacaoVal > 0){
            $compensacaoIni = $this->ano.$this->mes; // AAAAMM
            $compensacaoFim = $this->ano.$this->mes; // AAAAMM
        }else{
            $compensacaoIni = NULL; 
            $compensacaoFim = NULL; 
        }
        
        //16 COMPENSAÇÃO - PERÍODO INÍCIO
        $compensacaoIni = sprintf("%-6s", $compensacaoIni);
        fwrite($arquivo, $compensacaoIni, 6);
        
        //17 COMPENSAÇÃO - PERÍODO FIM
        $compensacaoFim = sprintf("%-6s", $compensacaoFim);
        fwrite($arquivo, $compensacaoFim, 6);
        
        //18 RECOLHIMENTO DE COMPETÊNCIAS ANTERIORES - VALOR DO INSS SOBRE FOLHA DE PAGAMENTO
        $recCompAntInss = NULL;
        $recCompAntInss = sprintf("%015s", $recCompAntInss);
        fwrite($arquivo, $recCompAntInss, 15);
        
        //19 RECOLHIMENTO DE COMPETÊNCIAS ANTERIORES - OUTRAS ENTIDADES SOBRE FOLHA DE PAGAMENTO
        $recCompAntTerceiros = NULL;
        $recCompAntTerceiros = sprintf("%015s", $recCompAntTerceiros);
        fwrite($arquivo, $recCompAntTerceiros, 15);
        
        //20 RECOLHIMENTO DE COMPETÊNCIAS ANTERIORES - COMERCIALIZAÇÃO DE PRODUÇÃO - VALOR DE INSS
        $recCompAntProducaoInss = NULL;
        $recCompAntProducaoInss = sprintf("%015s", $recCompAntProducaoInss);
        fwrite($arquivo, $recCompAntProducaoInss, 15);
        
        //21 RECOLHIMENTO DE COMPETÊNCIAS ANTERIORES - COMERCIALIZAÇÃO DE PRODUÇÃO - OUTRAS ENTIDADES
        $recCompAntProducaoTerceiros = NULL;
        $recCompAntProducaoTerceiros = sprintf("%015s", $recCompAntProducaoTerceiros);
        fwrite($arquivo, $recCompAntProducaoTerceiros, 15);
        
        //22 RECOLHIMENTO DE COMPETÊNCIAS ANTERIORES - RECEITA DE EVENTO DESPORTIVO/PATROCÍNIO - VALOR DE INSS
        $recCompAntEventoInss = NULL;
        $recCompAntEventoInss = sprintf("%015s", $recCompAntEventoInss);
        fwrite($arquivo, $recCompAntEventoInss, 15);
        
        //23 PARCELAMENTO DO FGTS - SOMATÓRIO REMUNERAÇÕES DAS CATEGORIAS 01,02,03,05,06 (IMPLEMENTAÇÃO FUTURA)
        $parcelFgts = NULL;
        $parcelFgts = sprintf("%015s", $parcelFgts);
        fwrite($arquivo, $parcelFgts, 15);
        
        //24 PARCELAMENTO DO FGTS - SOMATÓRIO REMUNERAÇÕES DAS CATEGORIAS 04,07 (IMPLEMENTAÇÃO FUTURA)
        $parcelFgts = NULL;
        $parcelFgts = sprintf("%015s", $parcelFgts);
        fwrite($arquivo, $parcelFgts, 15);
        
        //25 PARCELAMENTO DO FGTS - VALOR RECOLHIDO
        $parcelFgts = NULL;
        $parcelFgts = sprintf("%015s", $parcelFgts);
        fwrite($arquivo, $parcelFgts, 15);
        
        //26 VALORES PAGOS À COOPERATIVAS DE TRABALHO - SERVIÇOS PRESTADOS
        $valPagCoopTrabalho = NULL;
        $valPagCoopTrabalho = sprintf("%015s", $valPagCoopTrabalho);
        fwrite($arquivo, $valPagCoopTrabalho, 15);
        
        //27 IMPLEMENTAÇÃO FUTURA
        $implementacaoFutura = NULL;
        $implementacaoFutura = sprintf("%045s", $implementacaoFutura);
        fwrite($arquivo, $implementacaoFutura, 45);
        
        //28 BRANCOS
        $brancos = sprintf("%-6s", $this->brancos);
        fwrite($arquivo, $brancos, 6);
        
        //29 FIM DE LINHA
        $fimLinha = sprintf("%-1s", $this->fimLinha);
        fwrite($arquivo, $fimLinha, 1);
        
        fwrite($arquivo, "\r\n");
    }
    
    //REGISTRO TIPO 13 - ALTERAÇÃO CADASTRAL TRABALHADOR (OPCIONAL)
    public function montaReg13($arquivo, $responsavel, $dadosBasicos, $altCadTrab) {
        //01 TIPO DE REGISTRO
        $tpReg = "13";
        $tpReg = sprintf("%2s", $tpReg);
        fwrite($arquivo, $tpReg, 2);
        
        //02 TIPO DE INSCRIÇÃO
        $tpInscResp = $responsavel['tpInscResp']; // CAMPO OBRIGATÓRIO 
        $tpInscResp = sprintf("%1s", $tpInscResp); // 1 - (CNPJ), 2 - (CEI), 3 - (CPF) OBS: SÓ PODE SER 3 PARA COD DE RECOLHIMENTO 418
        fwrite($arquivo, $tpInscResp, 1);

        //03 INSCRIÇÃO DA EMPRESA
        $inscResp = $responsavel['inscResp'];
        $inscResp = sprintf("%14s", RemoveCaracteres($inscResp));
        fwrite($arquivo, $inscResp, 14);
        
        //04 ZEROS
        $zeros = sprintf("%036s", $this->brancos);
        fwrite($arquivo, $zeros, 36);
        
        //05 PIS/PASEP/CI
        $pis = $dadosBasicos['pis'];
        $pis = sprintf("%11s", RemoveCaracteres($pis));
        fwrite($arquivo, $pis, 11);
        
        //06 DATA DE ADMISSÃO
        $dtAdmissao = $dadosBasicos['data_entrada'];
        $dtAdmissao = sprintf("%-8s", implode('', array_reverse(explode('-', $dtAdmissao)))); //DDMMAAAA
        fwrite($arquivo, $dtAdmissao, 8);
        
        //07 CATEGORIA TRABALHADOR
        $categoriaTrab = "01";
        $categoriaTrab = sprintf("%2s", $categoriaTrab);
        fwrite($arquivo, $categoriaTrab, 2);
        
        //08 MATRÍCULA DO TRABALHADOR
        $matriculaTrab = $dadosBasicos['id_trab'];
        $matriculaTrab = sprintf("%11s", $matriculaTrab);
        fwrite($arquivo, $matriculaTrab, 11);
        
        //09 NÚMERO CTPS
        $nrCtps = $dadosBasicos['campo1'];
        $nrCtps = sprintf("%07s", RemoveCaracteres(trim($nrCtps)));
        fwrite($arquivo, $nrCtps, 7);
        
        //10 SÉRIE CTPS
        $serieCtps = $dadosBasicos['serie_ctps'];
        $serieCtps = sprintf("%05s", preg_replace("/[[:alpha:]]/", "0",RemoveCaracteres(trim($serieCtps)))); // SEMPRE QUE SÉRIE DA CTPS FOR ALFANUMÉRICO DEVE-SE SUBSTITUIR AS LETRAS POR ZEROS 
        fwrite($arquivo, $serieCtps, 5);
        
        //11 NOME TRABALHADOR
        $nomeTrab = $dadosBasicos['nome'];
        $nomeTrab = sprintf("%-70s", RemoveAcentos(expersonalizada($nomeTrab, $this->caracteres, array('[[:digit:]]'))));
        fwrite($arquivo, $nomeTrab, 70);
        
        //12 CÓDIGO EMPRESA CAIXA
        $codEmpresaCaixa = NULL;
        $codEmpresaCaixa = sprintf("%14s", $codEmpresaCaixa);
        fwrite($arquivo, $codEmpresaCaixa, 14);
        
        //13 CÓDIGO TRABALHADOR CAIXA
        $codTrabCaixa = NULL;
        $codTrabCaixa = sprintf("%11s", $codTrabCaixa);
        fwrite($arquivo, $codTrabCaixa, 11);
        
        //14 CÓDIGO ALTERAÇÃO CADASTRAL
        $codAltCadastral = $altCadTrab['sefip_codigo'];
        $codAltCadastral = sprintf("%3s", $codAltCadastral);
        fwrite($arquivo, $codAltCadastral, 3);
        
        //15 NOVO CONTEÚDO DO CAMPO
        $novoConteudo = $altCadTrab['sefip_valor'];
        $novoConteudo = sprintf("%-70s", implode('', array_reverse(explode('-', $novoConteudo))));
        fwrite($arquivo, $novoConteudo, 70);
        
        //16 BRANCOS
        $brancos = sprintf("%-94s", $this->brancos);
        fwrite($arquivo, $brancos, 94);
        
        //17 FINAL DA LINHA
        $fimLinha = sprintf("%-1s", $this->fimLinha);
        fwrite($arquivo, $fimLinha, 1);
        
        fwrite($arquivo, "\r\n");
    }
        
    //REGISTRO TIPO 14 - INCLUSAO/ALTERACAO DE ENDEREÇO DO TRABALHADOR
    public function montaReg14($arquivo, $responsavel,$dadosBasicos, $incAltEndTrab) { 
        //01 TIPO DE REGISTRO
        $tpReg = "14";
        $tpReg = sprintf("%2s", $tpReg);
        fwrite($arquivo, $tpReg, 2);
        
        //02 TIPO DE INSCRIÇÃO - EMPRESA
        $tpInscResp = $responsavel['tpInscResp']; // CAMPO OBRIGATÓRIO 
        $tpInscResp = sprintf("%1s", $tpInscResp); // 1 - (CNPJ), 2 - (CEI), 3 - (CPF) OBS: SÓ PODE SER 3 PARA COD DE RECOLHIMENTO 418
        fwrite($arquivo, $tpInscResp, 1);
        
        //03 INSCRIÇÃO DA EMPRESA
        $inscResp = $responsavel['inscResp'];
        $inscResp = sprintf("%14s", RemoveCaracteres($inscResp));
        fwrite($arquivo, $inscResp, 14);
        
        //04 ZEROS
        $zeros = sprintf("%036s", $this->brancos);
        fwrite($arquivo, $zeros, 36);
        
        //05 PIS/PASEP/CI
        $pis = $dadosBasicos['pis'];
        $pis = sprintf("%11s", RemoveCaracteres($pis));
        fwrite($arquivo, $pis, 11);
        
        //06 DATA ADMISSÃO
        $dtAdmissao = $dadosBasicos['data_entrada'];
        $dtAdmissao = sprintf("%-8s", implode('', array_reverse(explode('-', $dtAdmissao)))); //DDMMAAAA
        fwrite($arquivo, $dtAdmissao, 8);
        
        //07 CATEGORIA TRABALHADOR
        $categoriaTrab = "01";
        $categoriaTrab = sprintf("%2s", $categoriaTrab);
        fwrite($arquivo, $categoriaTrab, 2);
        
        //08 NOME TRABALHADOR
        $nomeTrab = $dadosBasicos['nome'];
        $nomeTrab = sprintf("%-70s", RemoveAcentos(expersonalizada($nomeTrab, $this->caracteres, array('[[:digit:]]'))));
        fwrite($arquivo, $nomeTrab, 70);
        
        //09 NÚMERO CTPS
        $nrCtps = $dadosBasicos['campo1'];
        $nrCtps = sprintf("%07s", RemoveCaracteres(trim($nrCtps)));
        fwrite($arquivo, $nrCtps, 7);
        
        //10 SÉRIE CTPS
        $serieCtps = $dadosBasicos['serie_ctps'];
        $serieCtps = sprintf("%05s", preg_replace("/[[:alpha:]]/", "0",RemoveCaracteres(trim($serieCtps)))); // SEMPRE QUE SÉRIE DA CTPS FOR ALFANUMÉRICO DEVE-SE SUBSTITUIR AS LETRAS POR ZEROS 
        fwrite($arquivo, $serieCtps, 5);
        
        //11 LOGRADOURO, RUA, Nº, ANDAR, APARTAMENTO
        $endereco = $incAltEndTrab['endereco'];
        $endereco = sprintf("%-50s", RemoveAcentos(expersonalizada(RemoveCaracteresGeral(trim($endereco)), $this->caracteres)));
        fwrite($arquivo, $endereco, 50);
        
        //12 BAIRRO
        $bairro = $incAltEndTrab['bairro'];
        $bairro = sprintf("%-20s", RemoveAcentos(expersonalizada(RemoveCaracteresGeral($bairro), $this->caracteres)));
        fwrite($arquivo, $bairro, 20);

        //13 CEP
        $cep = $incAltEndTrab['cep'];
        $cep = sprintf("%8s", RemoveCaracteres($cep));
        fwrite($arquivo, $cep, 8);
        
        //14 CIDADE
        $cidade = $incAltEndTrab['cidade'];
        $cidade = sprintf("%-20s", RemoveAcentos(expersonalizada($cidade, $this->caracteres)));
        fwrite($arquivo, $cidade, 20);

        //15 UNIDADE DA FEDERAÇÃO
        $uf = $incAltEndTrab['uf'];
        $uf = sprintf("%-2s", $uf);
        fwrite($arquivo, $uf, 2);
        
        //16 BRANCOS
        $brancos = sprintf("%-103s", $this->brancos);
        fwrite($arquivo, $brancos, 103);
        
        //17 FINAL DE LINHA
        $fimLinha = sprintf("%-1s", $this->fimLinha);
        fwrite($arquivo, $fimLinha, 1);
        
        fwrite($arquivo, "\r\n");
    }
        
    //REGISTRO TIPO 20 - REGISTRO DO TOMADOR DE SERVIÇO/OBRA DE CONSTRUÇÃO CIVIL
    public function montaReg20($arquivo, $tomadorServ, $responsavel) {
        if($_COOKIE['debug'] == "sefip"){
            echo "<br><br><strong>ARRAY tomadorServ</strong>";
            print_array($tomadorServ);

            echo "<strong>ARRAY responsavel</strong>";
            print_array($responsavel);
        }                
        
        //01 TIPO DE REGISTRO
        $tpReg = "20";
        $tpReg = sprintf("%2s", $tpReg);
        fwrite($arquivo, $tpReg, 2);
        
        //02 TIPO DE INSCRIÇÃO - EMPRESA
        $tpInscResp = $responsavel['tpInscResp']; // CAMPO OBRIGATÓRIO 
        $tpInscResp = sprintf("%1s", $tpInscResp); // 1 - (CNPJ), 2 - (CEI), 3 - (CPF) OBS: SÓ PODE SER 3 PARA COD DE RECOLHIMENTO 418
        fwrite($arquivo, $tpInscResp, 1);

        //03 INSCRIÇÃO DA EMPRESA
        $inscResp = $responsavel['inscResp'];
        $inscResp = sprintf("%14s", RemoveCaracteres($inscResp));
        fwrite($arquivo, $inscResp, 14);
        
        //04 TIPO DE INSCRIÇÃO - TOMADOR/OBRA CONST. CIVIL
        $tpInscTomador = $tomadorServ['tpInscTomador']; // CAMPO OBRIGATÓRIO 
        $tpInscTomador = sprintf("%1s", $tpInscTomador); // 1 - (CNPJ), 2 - (CEI)
        fwrite($arquivo, $tpInscTomador, 1);

        //05 INSCRIÇÃO TOMADOR/OBRA CONST. CIVIL
        $inscTomador = $tomadorServ['inscTomador'];
        $inscTomador = sprintf("%14s", RemoveCaracteres($inscTomador));
        fwrite($arquivo, $inscTomador, 14);
        
        //06 ZEROS
        $zeros = sprintf("%021s", $this->brancos);
        fwrite($arquivo, $zeros, 21);
        
        //07 NOME DO TOMADOR/OBRA DE CONST. CIVIL
        $nomeTomador = $tomadorServ['razao'];
        $nomeTomador = sprintf("%-40s", RemoveAcentos(expersonalizada($nomeTomador, $this->caracteres)));
        fwrite($arquivo, $nomeTomador, 40);
        
        //08 LOGRADOURO, RUA, Nº, ANDAR, APARTAMENTO
        $endereco = $tomadorServ['endereco'];
        $endereco = sprintf("%-50s", RemoveAcentos(expersonalizada($endereco, $this->caracteres)));
        fwrite($arquivo, $endereco, 50);

        //09 BAIRRO
        $bairro = $tomadorServ['bairro'];
        $bairro = sprintf("%-20s", RemoveAcentos(expersonalizada($bairro, $this->caracteres)));
        fwrite($arquivo, $bairro, 20);

        //10 CEP
        $cep = $tomadorServ['cep'];
        $cep = sprintf("%8s", RemoveCaracteres($cep));
        fwrite($arquivo, $cep, 8);
        
        //11 CIDADE
        $cidade = $tomadorServ['cidade'];
        $cidade = sprintf("%-20s", RemoveAcentos(expersonalizada($cidade, $this->caracteres)));
        fwrite($arquivo, $cidade, 20);

        //12 UNIDADE DA FEDERAÇÃO
        $uf = $tomadorServ['uf'];
        $uf = sprintf("%-2s", $uf);
        fwrite($arquivo, $uf, 2);
        
        //13 CÓDIGO DE PAGAMENTO GPS
        $codPagGps = $tomadorServ['codPagGps'];
        $codPagGps = sprintf("%4s", $codPagGps);
        fwrite($arquivo, $codPagGps, 4);
        
        //14 SALÁRIO FAMÍLIA
        $salFamilia = $tomadorServ['salFamilia'];
        $salFamilia = sprintf("%015s", $salFamilia);
        fwrite($arquivo, $salFamilia, 15);
        
        //15 CONTRIBUIÇÃO DESCONTADA EMPREGADO REFERENTE À COMPETÊNCIA 13
        $contribDescEmp = NULL;
        $contribDescEmp = sprintf("%015s", $contribDescEmp);
        fwrite($arquivo, $contribDescEmp, 15);
        
        //16 INDICADOR DE VALOR NEGATIVO OU POSITIVO
        $indValNegPositivo = NULL;
        $indValNegPositivo = sprintf("%01s", $indValNegPositivo);
        fwrite($arquivo, $indValNegPositivo, 1);
        
        //17 VALOR DEVIDO À PREVIDÊNCIA SOCIAL, REFERENTA A COMPETÊNCIA 13
        $valDevInss = NULL;
        $valDevInss = sprintf("%014s", $valDevInss);
        fwrite($arquivo, $valDevInss, 14);
        
        //18 VALOR DE RETENÇÃO(LEI 9.711/98)
        $valRetencao = NULL;
        $valRetencao = sprintf("%015s", $valRetencao);
        fwrite($arquivo, $valRetencao, 15);
        
        //19 VALOR DAS FATURAS EMITIDAS PARA O TOMADOR
        $valFatEmitidasparaTomador = NULL;
        $valFatEmitidasparaTomador = sprintf("%015s", $valFatEmitidasparaTomador);
        fwrite($arquivo, $valFatEmitidasparaTomador, 15);

        //20 ZEROS
        $zeros = sprintf("%045s", $this->brancos);
        fwrite($arquivo, $brancos, 45);
        
        //21 BRANCOS
        $brancos = sprintf("%-42s", $this->brancos);
        fwrite($arquivo, $brancos, 42);
        
        //22 FINAL DE LINHA
        $fimLinha = sprintf("%-1s", $this->fimLinha);
        fwrite($arquivo, $fimLinha, 1);
        
        fwrite($arquivo, "\r\n");        
    }
        
    //REGISTRO TIPO 21 - REGISTRO DE INFORMAÇÕES ADICIONAIS DO TOMADOR DE SERVICO/OBRA DE CONST. CIVIL
    public function montaReg21($arquivo, $tomadorServ) {
        //01 TIPO DE REGISTRO
        $tpReg = "21";
        $tpReg = sprintf("%2s", $tpReg);
        fwrite($arquivo, $tpReg, 2);
        
        //02 TIPO DE INSCRIÇÃO - EMPRESA
        $tpInscResp = $responsavel['tpInscResp']; // CAMPO OBRIGATÓRIO 
        $tpInscResp = sprintf("%1s", $tpInscResp); // 1 - (CNPJ), 2 - (CEI), 3 - (CPF) OBS: SÓ PODE SER 3 PARA COD DE RECOLHIMENTO 418
        fwrite($arquivo, $tpInscResp, 1);

        //03 INSCRIÇÃO DA EMPRESA
        $inscResp = $responsavel['inscResp'];
        $inscResp = sprintf("%14s", RemoveCaracteres($inscResp));
        fwrite($arquivo, $inscResp, 14);
        
        //04 TIPO INSCRIÇÃO - TOMADOR/OBRA CONST. CIVIL
        $tpInscTomador = $tomadorServ['tpInscTomador']; // CAMPO OBRIGATÓRIO 
        $tpInscTomador = sprintf("%1s", $tpInscTomador); // 1 - (CNPJ), 2 - (CEI)
        fwrite($arquivo, $tpInscTomador, 1);
        
        //05 INSCRIÇÃO TOMADOR/OBRA CONST. CIVIL
        $inscTomador = $tomadorServ['inscTomador'];
        $inscTomador = sprintf("%14s", RemoveCaracteres($inscTomador));
        fwrite($arquivo, $inscTomador, 14);
        
        //06 ZEROS
        $zeros = sprintf("%021s", $this->brancos);
        fwrite($arquivo, $zeros, 21);
                
        $compensacaoVal = NULL; // CAMPO OPCIONAL
        
        /*
        if ($this->dtHoje < $this->prazoFGTS && $this->mes != 13) {
            $compensacaoVal = ""; // PREENCHER COM UM VALOR 
        }else{
            $compensacaoVal = NULL;
        }
        */
        
        //07 COMPENSAÇÃO - VALOR CORRIGIDO
        $compensacaoVal = sprintf("%015s", $compensacaoVal);
        fwrite($arquivo, $compensacaoVal, 15);
                
        if (!empty($compensacaoVal) && $this->mes != 13){
            $compensacaoIni = ""; // AAAAMM
            $compensacaoFim = ""; // AAAAMM
        }else{
            $compensacaoIni = NULL; 
            $compensacaoFim = NULL; 
        }
        
        //08 COMPENSAÇÃO - PERÍODO INÍCIO
        $compensacaoIni = sprintf("%-6s", $compensacaoIni);
        fwrite($arquivo, $compensacaoIni, 6);
        
        //09 COMPENSAÇÃO - PERÍODO FIM
        $compensacaoFim = sprintf("%-6s", $compensacaoFim);
        fwrite($arquivo, $compensacaoFim, 6);
        
        //10 RECOLHIMENTO DE COMPETÊNCIAS ANTERIORES - VALOS DO INSS SOBRE FOLHA DE PGT
        $recCompAntInss = NULL;
        $recCompAntInss = sprintf("%015s", $recCompAntInss);
        fwrite($arquivo, $recCompAntInss, 15);
        
        //11 RECOLHIMENTO DE COMPETÊNCIAS ANTERIORES - OUTRAS ENTIDADES SOBRE FOLHA DE PGT
        $recCompAntTerceiros = NULL;
        $recCompAntTerceiros = sprintf("%015s", $recCompAntTerceiros);
        fwrite($arquivo, $recCompAntTerceiros, 15);
        
        //12 PARCELAMENTO DO FGTS - SOMATÓRIO DAS REM. DAS CATEGORIAS 01,02,03,05,06
        $parcelFgts = NULL; // IMPLEMENTAÇÃO FUTURA
        $parcelFgts = sprintf("%015s", $parcelFgts);
        fwrite($arquivo, $parcelFgts, 15);
        
        //13 PARCELAMENTO DO FGTS - SOMATÓRIO DAS REM. DAS CATEGORIAS 04,07 
        $parcelFgts = NULL; // IMPLEMENTAÇÃO FUTURA
        $parcelFgts = sprintf("%015s", $parcelFgts);
        fwrite($arquivo, $parcelFgts, 15);
        
        //14 PARCELAMENTO DO FGTS - VALOR RECOLHIDO AO FGTS (DEPÓSITO + JAM + MULTA) 
        $parcelFgts = NULL; // IMPLEMENTAÇÃO FUTURA
        $parcelFgts = sprintf("%015s", $parcelFgts);
        fwrite($arquivo, $parcelFgts, 15);
        
        //15 BRANCOS
        $brancos = sprintf("%-204s", $this->brancos);
        fwrite($arquivo, $brancos, 204);
        
        //16 FIM DE LINHA
        $fimLinha = sprintf("%-1s", $this->fimLinha);
        fwrite($arquivo, $fimLinha, 1);
        
        fwrite($arquivo, "\r\n");          
    }
    
    //REGISTRO TIPO 30 - REGISTRO DO TRABALHADOR
    public function montaReg30($arquivo, $responsavel, $dadosBasicos, $regTrab, $arrayRescisao, $diasTrab, $decimoTerceiro) {
        
        if($_COOKIE['debug'] == 666){
//            if($dadosBasicos['id_trab'] == 554){
                echo "////////////////////////////////";
                echo 'ARRAY $dadosBasicos';
                echo "////////////////////////////////";
                print_array($dadosBasicos);
                echo "////////////////////////////////";
                echo 'ARRAY $regTrab';
                echo "////////////////////////////////";
                print_array($regTrab);
//            }
        }
        
//        echo "{$regTrab['base_inss']}<br>";
        
        //01 TIPO DE REGISTRO
        $tpReg = "30";
        $tpReg = sprintf("%2s", $tpReg);
        fwrite($arquivo, $tpReg, 2);
        
        //02 TIPO DE INSCRIÇÃO EMPRESA
        $tpInscResp = $responsavel['tpInscResp']; // CAMPO OBRIGATÓRIO
        $tpInscResp = sprintf("%1s", $tpInscResp); // 1 - (CNPJ), 2 - (CEI), 3 - (CPF) OBS: SÓ PODE SER 3 PARA COD DE RECOLHIMENTO 418
        fwrite($arquivo, $tpInscResp, 1);
        
        //03 INSCRIÇÃO DA EMPRESA
        $inscResp = $responsavel['inscResp'];
        $inscResp = sprintf("%14s", RemoveCaracteres($inscResp));
        fwrite($arquivo, $inscResp, 14);
        
        //04 TIPO DE INSCRIÇÃO - TOMADOR/OBRA DE CONST. CIVIL
        $tpInscTomador = $responsavel['tpInscTomador']; // CAMPO OBRIGATÓRIO 
        $tpInscTomador = sprintf("%1s", $tpInscTomador); // 1 - (CNPJ), 2 - (CEI)
        fwrite($arquivo, $tpInscTomador, 1);
        
        //05 INSCRIÇÃO - TOMADOR/OBRA DE CONST. CIVIL
        $inscTomador = $responsavel['inscTomador'];
        $inscTomador = sprintf("%14s", RemoveCaracteres($inscTomador));
        fwrite($arquivo, $inscTomador, 14);
        
        //06 PIS/PASEP/CI
        $pis = $dadosBasicos['pis'];
        $pis = sprintf("%011s", RemoveCaracteres($pis));
        fwrite($arquivo, $pis, 11);
//        if($_COOKIE['logado'] == 354 AND $dadosBasicos['pis'] == 13636182233){
//            print_array($dadosBasicos);                
//        }
        
        //07 DATA ADMISSÃO
        $dtAdmissao = $dadosBasicos['data_entrada'];
        $dtAdmissao = sprintf("%-8s", implode('', array_reverse(explode('-', $dtAdmissao)))); //DDMMAAAA
        fwrite($arquivo, $dtAdmissao, 8);                
        
        //08 CATEGORIA TRABALHADOR
        $categoriaTrab = $regTrab['categoria'];
        $categoriaTrab = sprintf("%2s", $categoriaTrab);
        fwrite($arquivo, $categoriaTrab, 2);
        
        //09 NOME TRABALHADOR
        $nomeTrab = $dadosBasicos['nome'];
        $nomeTrab = sprintf("%-70s", RemoveAcentos(expersonalizada(RemoveCaracteres($nomeTrab), $this->caracteres, array('[[:digit:]]'))));
//        $nomeTrab = sprintf("%-70s", RemoveCaracteres(RemoveAcentos(expersonalizada($nomeTrab, $this->caracteres, array('[[:digit:]]')))));
        fwrite($arquivo, $nomeTrab, 70);
        
        //10 MATRÍCULA DO EMPREGADO
        $matriculaTrab = $dadosBasicos['id_trab'];
        $matriculaTrab = sprintf("%11s", $matriculaTrab);
        fwrite($arquivo, $matriculaTrab, 11);                
        
        //11 NÚMERO CTPS
        //12 SÉRIE CTPS
        if($regTrab['categoria'] == "13"){
            $nrCtps = $dadosBasicos['campo1'];
            $nrCtps = str_pad($input1, 7, " ", STR_PAD_RIGHT);
            fwrite($arquivo, $nrCtps, 7);
            
            $serieCtps = $dadosBasicos['serie_ctps'];
            $serieCtps = str_pad($serieCtps, 5, " ", STR_PAD_RIGHT);
            fwrite($arquivo, $serieCtps, 5);
        }else{
            $nrCtps = $dadosBasicos['campo1'];
            $nrCtps = sprintf("%07s", RemoveCaracteres(trim($nrCtps)));
            fwrite($arquivo, $nrCtps, 7);
            
            $serieCtps = $dadosBasicos['serie_ctps'];
            $serieCtps = sprintf("%05s", preg_replace("/[[:alpha:]]/", "0",RemoveCaracteres(trim($serieCtps)))); // SEMPRE QUE SÉRIE DA CTPS FOR ALFANUMÉRICO DEVE-SE SUBSTITUIR AS LETRAS POR ZEROS 
            fwrite($arquivo, $serieCtps, 5);
        }
        
        //13 DATA DE OPÇÃO
        $dtOpcao = $dtAdmissao;
        fwrite($arquivo, $dtOpcao, 8);
        
        //14 DATA DE NASCIMENTO
        $dtNasc = $regTrab['data_nasci']; // DDMMAAAA
        $dtNasc = sprintf("%-8s", implode('', array_reverse(explode('-', $dtNasc)))); //DDMMAAAA
        fwrite($arquivo, $dtNasc, 8);
        
        //15 CBO - CÓDIGO BRASILEIRO DE OCUPAÇÃO
        $cbo = RemoveCaracteres($regTrab['cod']); // 0 + XXXX ONDE XXXX É DO CÓDIGO DA FAMÍLIA DE NOVO CBO A QUAL PERTENCE O TRABALHADOR
        $cbo = '0'.substr($cbo,0,4);
        $cbo = sprintf("%05s", $cbo);
        fwrite($arquivo, $cbo, 5);
        
//        $verifica_ferias = "SELECT *, DATE_FORMAT(data_fim, '%d-%m-%Y') AS data_fim, DATE_FORMAT(data_ini, '%d-%m-%Y') AS data_ini, DATE_FORMAT(ADDDATE(LAST_DAY(SUBDATE(data_fim, INTERVAL 1 MONTH)), 1), '%d-%m-%Y') AS data_ini2, DATE_FORMAT(LAST_DAY(data_ini), '%d-%m-%Y') AS ultimo_dia
//            FROM rh_ferias
//            WHERE id_clt = '{$dadosBasicos['id_trab']}' AND '{$this->ano}-{$this->mes}' BETWEEN DATE_FORMAT(data_ini, '%Y-%m') AND DATE_FORMAT(data_fim, '%Y-%m') AND STATUS = 1 AND MONTH(data_ini) = {$this->mes}
//            ORDER BY id_ferias DESC";
//        $query_ferias = mysql_query($verifica_ferias) or die(mysql_error());
//        $resul_ferias = mysql_fetch_assoc($query_ferias);
//        $total_ferias = mysql_num_rows($query_ferias);
        
//        if($_COOKIE['logado'] == 353){
//            if($dadosBasicos['id_trab'] == 554){
//                echo round($regTrab['base_inss'] + $resul_ferias['base_inss'], 2).'<br>';
//                echo "{$regTrab['base_inss']} + {$resul_ferias['base_inss']}";
//            }
//        }
        
        if($this->terceiro == 1){
            $remuneracaoSem13 = NULL;
            $remuneracao13 = NULL;
//            $baseCalc13RefCompetencia = $decimoTerceiro['decimo_terceiro'];  // ????
            $baseCalc13RefCompetencia = $regTrab['base_inss'];  // ????
            
            if($baseCalc13RefCompetencia == 0){
                $baseCalc13RefCompetencia = 1;
            }
        }  else {
            $remuneracaoSem13 = $regTrab['base_inss'];                        
            
            //condição para FGTS de clts em SERVIÇO MILITAR
            if($regTrab['status_clt'] == 30){
                $remuneracaoSem13 = number_format(($regTrab['base_inss'] > 0) ? $regTrab['base_inss'] : $regTrab['base_fgts'], 2,'.','');
            }
            
//            if($total_ferias > 0){
//                $remuneracaoSem13 = number_format($regTrab['base_inss'] + $resul_ferias['base_inss'], 2,'.','');
//            }
            
            if($remuneracaoSem13 == '0.00'){
                $remuneracaoSem13 = '1';
            }
            
            $remuneracao13 = $decimoTerceiro['decimo_terceiro'];      
            
            if($_COOKIE['debug'] == 666){
                echo "////////////////////////////////";
                echo 'Condição if dias trabalhados ano';
                echo "////////////////////////////////";
                print_array("({$diasTrab} >= 15 && {$regTrab['base_inss_13_rescisao']} > 0) || ({$regTrab['data_importacao']} != '' && {$regTrab['data_importacao']} != '0000-00-00' && $this->ano == 2016)");
                print_array(($diasTrab >= 15 && $regTrab['base_inss_13_rescisao'] > 0) || ($regTrab['data_importacao'] != '' && $regTrab['data_importacao'] != '0000-00-00' && $this->ano == 2016));
            }
            
            if(in_array($regTrab['status_clt'], $arrayRescisao) && date("mY", str_replace("/", "-", strtotime($regTrab['data_demi']))) < sprintf("%06s", ($this->mes+1).$this->ano)){ // OBRIGATÓRIO NO MÊS DE RESCISÃO PARA QUEM TRABALHOU MAIS DE 15 DIAS NO ANO
//                if($_COOKIE['logado'] == 354 && $dadosBasicos['id_trab'] == 4382){ echo "A{$diasTrab}";  }
                if(($diasTrab >= 15 && $regTrab['base_inss_13_rescisao'] > 0) || ($regTrab['data_importacao'] != '' && $regTrab['data_importacao'] != '0000-00-00' && $this->ano == 2016)){
//                    if($_COOKIE['logado'] == 354 && $dadosBasicos['id_trab'] == 3393){ echo "B";  }
                    $baseCalc13RefCompetencia = $regTrab['base_inss_13_rescisao'];        // E POSSUI COD DE MOV POR MOTIVO DE RESCISÃO (EXCETO JUSTA CAUSA), APOSENTADORIA COM QUEBRA DE VÍNCULO OU FALECIMENTO.
//            if($_COOKIE['logado'] == 354 && $dadosBasicos['id_trab'] == 3393){ echo $baseCalc13RefCompetencia; exit; }        
//                    $media_13 = $this->getMedia13($dadosBasicos['id_trab']);
//                    
//                    if($media_13 > 0){
//                        $baseCalc13RefCompetencia += $media_13;
//                    }
                    
                    if(($regTrab['status_clt'] == 65) || ($regTrab['status_clt'] == 63)){
                        $remuneracao13 = $baseCalc13RefCompetencia;
                    }
                    
                    if($baseCalc13RefCompetencia == '0.00'){
                        $baseCalc13RefCompetencia = '1';
                    }
                } else {
                    $baseCalc13RefCompetencia = NULL;
                }
                
                if($diasTrab < 15){
                    $baseCalc13RefCompetencia = '0.00';
                }
            }else{
                $baseCalc13RefCompetencia = NULL;
            }
        }   
//        if($regTrab['status_clt'] == 65){
//            $baseCalc13RefCompetencia = '1';
//        }
        
//        if($_COOKIE['logado'] == 353 && $regTrab['categoria'] != "13"){
//            $this->a += $remuneracaoSem13; 
//            echo $dadosBasicos['id_trab'] . ' - ' . $remuneracaoSem13. ' - ' .$this->a.'<br>';
//        }
        $data_demi = date("m/Y", str_replace("/", "-", strtotime($regTrab['data_demi'])));
        $data_evento = $regTrab['mes']."/".$regTrab['ano'];        
        
        if($data_demi != '' && $data_demi != '0000-00-00'){
            if($data_demi != $data_evento){
                //$baseCalc13RefCompetencia = '0.00';
            }
        }          
        
        // EXCESSÃO PARA AFASTADO POR ACIDENTE DE TRABALHO
        if($regTrab['status_clt'] == 70){
//            $remuneracaoSem13 = round($regTrab['base_fgts'], 2);
//            $remuneracaoSem13 = number_format($regTrab['base_fgts'], 2,'.','');
            $remuneracaoSem13 = number_format(($regTrab['base_inss'] > 0) ? $regTrab['base_inss'] : $regTrab['base_fgts'], 2,'.','');
            
            if($remuneracaoSem13 == '0.00'){
                $remuneracaoSem13 = '1';
            }
        }
        
        if($_COOKIE['debug'] == 666){
            echo "////////////////////////////////";
            echo '16 REMUNERAÇÃO SEM 13º';
            echo "////////////////////////////////";
            print_array($remuneracaoSem13);
            echo "////////////////////////////////";
            echo '17 REMUNERAÇÃO 13º';
            echo "////////////////////////////////";
            print_array($remuneracao13);
            echo "////////////////////////////////";
            echo '22 BASE DE CÁLCULO 13º SALÁRIO PREVIDÊNCIA SOCIAL - REFERENTE À COMPETÊNCIA DO MOVIMENTO';
            echo "////////////////////////////////";
            print_array($baseCalc13RefCompetencia);
        }
        
        //16 REMUNERAÇÃO SEM 13º
        $remuneracaoSem13 = sprintf("%015s", RemoveCaracteres($remuneracaoSem13));
        fwrite($arquivo, $remuneracaoSem13, 15);
        
        //17 REMUNERAÇÃO 13º
        $remuneracao13 = sprintf("%015s", RemoveCaracteres($remuneracao13));
        fwrite($arquivo, $remuneracao13, 15);
        
        //18 CLASSE DE CONTRIBUIÇÃO
        $classeContrib = NULL;
        $classeContrib = sprintf("%2s", $classeContrib);
        fwrite($arquivo, $classeContrib, 2);                
        
        if($total_ferias > 0){
            $ocorrenciaSegurado = '05';
        }else{
            $ocorrenciaSegurado = $regTrab['ocorrencia'];
        }
        
        /*
         *  Feito por Renato a pedido de Ramon 03/10/2016
         * 
         * 2964 CLEIDE SANTOS DA SILVA MONTEIRO (NÃO DESCONTOU INSS NA FOLHA CENTRO)
         * 2615 RICARDO BEZERRA SILVA (INSS DE FERIAS DESCONTANDO O TETO FUNCIONARIO COM TETO NA CARTA POREM CARTA CADASTRADA APOS CADSTRO DAS FERIAS NORTE)
         * 
         */
        
        if(($dadosBasicos['id_trab'] == 2964 && $regTrab['mes']."/".$regTrab['ano'] == '09/2016') || ($dadosBasicos['id_trab'] == 3083 || $dadosBasicos['id_trab'] == 4294 && $regTrab['mes']."/".$regTrab['ano'] == '10/2016')) {
            $ocorrenciaSegurado = '05';
            if($_COOKIE['debug'] == 666){
                echo "AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA$ocorrenciaSegurado AAAAAAAAAAAAAAAAAAAAAAAA";
            }
        } else if($dadosBasicos['id_trab'] == 2615 && $regTrab['mes']."/".$regTrab['ano'] == '10/2016') {
            $ocorrenciaSegurado = null;
        } else if($regTrab['mes']."/".$regTrab['ano'] == '12/2016') {
            if($dadosBasicos['id_trab'] == 2451 || $dadosBasicos['id_trab'] == 4180 || $dadosBasicos['id_trab'] == 4162 || $dadosBasicos['id_trab'] == 4169 || $dadosBasicos['id_trab'] == 2300) {
                $ocorrenciaSegurado = '05';
            }
        }
        
        //19 OCORRÊNCIA
        $ocorrencia = $ocorrenciaSegurado;
        $ocorrencia = sprintf("%2s", $ocorrencia);
        fwrite($arquivo, $ocorrencia, 2);                
        
//        if ($regTrab['desconto_inss'] == 1 && $regTrab['tipo_desconto_inss'] != 'isento'){
        if ($regTrab['desconto_inss'] == 1){
            $valDescSegurado = $regTrab['valDescSegurado'];
        }else{
            if($regTrab['categoria'] == 13 && $regTrab['ocorrencia'] == '05'){
                $valDescSegurado = $regTrab['valDescSegurado'];
            } else {
            
//            if($total_ferias > 0){                
//                $valDescSegurado = $regTrab['valDescSegurado'];
//                
//                if($valDescSegurado > 570.88){
//                    $valDescSegurado = 570.88;
//                }
//                
//                if($valDescSegurado == 0){
//                    $valDescSegurado = $resul_ferias['inss'];
//                }
//                
//            }else{
                $valDescSegurado = NULL;
            }
        }
        
        if(($dadosBasicos['id_trab'] == 3083 || $dadosBasicos['id_trab'] == 4294)  && $regTrab['mes']."/".$regTrab['ano'] == '10/2016') {
            $valDescSegurado = '570.88';
            if($_COOKIE['debug'] == 666){
                echo "AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA$valDescSegurado AAAAAAAAAAAAAAAAAAAAAAAA";
            }
        } else if($regTrab['mes']."/".$regTrab['ano'] == '11/2016') {
            if($dadosBasicos['id_trab'] == 4236){
                $valDescSegurado = '570.88';
            }elseif($dadosBasicos['id_trab'] == 4251){
                $valDescSegurado = '409.08';
            }
        } 
        if($regTrab['mes']."/".$regTrab['ano'] == '12/2016') {
            if($dadosBasicos['id_trab'] == 2451){
                $valDescSegurado = 570.88;
            } else if($dadosBasicos['id_trab'] == 4180){
                $valDescSegurado = 422.71;
            } else if($dadosBasicos['id_trab'] == 4162){
                $valDescSegurado = 537.58;
            } else if($dadosBasicos['id_trab'] == 4169) {
                $valDescSegurado = 570.88;
            } else if($dadosBasicos['id_trab'] == 2300) {
                $valDescSegurado = 570.88;
            }
        }
        if($regTrab['mes']."/".$regTrab['ano'] == '01/2017') {
            if($dadosBasicos['id_trab'] == 3152 || $dadosBasicos['id_trab'] == 4226 || $dadosBasicos['id_trab'] == 4239){
                $valDescSegurado = 570.88;
            }
        }
        
        if($regTrab['mes']."/".$regTrab['ano'] == '03/2017') {           
            if($dadosBasicos['id_trab'] == 4218 || $dadosBasicos['id_trab'] == 4311 || $dadosBasicos['id_trab'] == 2458 || $dadosBasicos['id_trab'] == 2540 || $dadosBasicos['id_trab'] == 2541){
                $valDescSegurado = 608.44;
            }
            
            if($dadosBasicos['id_trab'] == 4129) {
                $valDescSegurado = 37.56;
            }
        }
        
        if(in_array($regTrab['status_clt'], $arrayRescisao) && $regTrab['ocorrencia'] == '05'){
            $valDescSegurado = $regTrab['inss_rescisao'];
        }
        
        //20 VALOR DESCONTADO DO SEGURADO
        $valDescSegurado = sprintf("%015s", RemoveCaracteres($valDescSegurado));
        fwrite($arquivo, $valDescSegurado, 15);
        
        //21 REMUNERAÇÃO BASE DE CÁLCULO DA CONTRIBUIÇÃO PREVIDENCIÁRIA
        $baseCalcFgts = NUll;                
        
        $baseCalcFgts = sprintf("%015s", RemoveCaracteres($baseCalcFgts));
        fwrite($arquivo, $baseCalcFgts, 15);
        
        //if($_COOKIE['logado'] == 257 AND $dadosBasicos['id_trab'] == 8893){echo $baseCalc13RefCompetencia.'<br>';}
        
        //22 BASE DE CÁLCULO 13º SALÁRIO PREVIDÊNCIA SOCIAL - REFERENTE À COMPETÊNCIA DO MOVIMENTO
        $baseCalc13RefCompetencia = sprintf("%015s", RemoveCaracteres($baseCalc13RefCompetencia));
        fwrite($arquivo, $baseCalc13RefCompetencia, 15);
        
        //23 BASE DE CÁLCULO 13º SALÁRIO PREVIDÊNCIA - REFERENTE À GPS DA COMPETÊNCIA 13
        $baseCalc13RefGps = NULL;
        $baseCalc13RefGps = sprintf("%015s", $baseCalc13RefGps);
        fwrite($arquivo, $baseCalc13RefGps, 15);
        
        //24 BRANCOS
        $brancos = sprintf("%-98s", $this->brancos);
        fwrite($arquivo, $brancos, 98);
        
        //25 FINAL DE LINHA
        $fimLinha = sprintf("%-1s", $this->fimLinha);
        fwrite($arquivo, $fimLinha, 1);
        
        fwrite($arquivo, "\r\n");    
    }
        
    //REGISTRO TIPO 32 - MOVIMENTACAO DO TRABALHADOR
    public function montaReg32($arquivo, $responsavel, $dadosBasicos, $codMovimento, $dataMovimento) {
        //01 TIPO DE REGISTRO
        $tpReg = "32";
        $tpReg = sprintf("%2s", $tpReg);
        fwrite($arquivo, $tpReg, 2);
        
        //02 TIPO DE INSCRIÇÃO EMPRESA
        $tpInscResp = $responsavel['tpInscResp']; // CAMPO OBRIGATÓRIO 
        $tpInscResp = sprintf("%1s", $tpInscResp); // 1 - (CNPJ), 2 - (CEI), 3 - (CPF) OBS: SÓ PODE SER 3 PARA COD DE RECOLHIMENTO 418
        fwrite($arquivo, $tpInscResp, 1);
        
        //03 INSCRIÇÃO DA EMPRESA
        $inscResp = $responsavel['inscResp'];
        $inscResp = sprintf("%14s", RemoveCaracteres($inscResp));
        fwrite($arquivo, $inscResp, 14);
        
        //04 TIPO DE INSCRIÇÃO - TOMADOR/OBRA CONST. CIVIL
        $tpInscTomador = NULL;
        $tpInscTomador = sprintf("%1s", $tpInscTomador);
        fwrite($arquivo, $tpInscTomador, 1);

        //05 INSCRIÇÃO - TOMADOR/OBRA CONST. CIVIL
        $inscTomador = NULL;
        $inscTomador = sprintf("%14s", RemoveCaracteres($inscTomador));
        fwrite($arquivo, $inscTomador, 14);
        
        //06 PIS/PASEP/CI
        $pis = $dadosBasicos['pis'];
        $pis = sprintf("%11s", RemoveCaracteres($pis));
        fwrite($arquivo, $pis, 11);
        
        //07 DATA ADMISSÃO
        $dtAdmissao = $dadosBasicos['data_entrada'];
        $dtAdmissao = sprintf("%-8s", implode('', array_reverse(explode('-', $dtAdmissao)))); //DDMMAAAA
        fwrite($arquivo, $dtAdmissao, 8);
        
        //08 CATEGORIA TRABALHADOR
        $categoriaTrab = "01";
        $categoriaTrab = sprintf("%2s", $categoriaTrab);
        fwrite($arquivo, $categoriaTrab, 2);
        
        //09 NOME TRABALHADOR
        $nomeTrab = $dadosBasicos['nome'];
        $nomeTrab = sprintf("%-70s", RemoveAcentos(expersonalizada($nomeTrab, $this->caracteres, array('[[:digit:]]'))));
        fwrite($arquivo, $nomeTrab, 70);
        
        //10 CÓDIGO DE MOVIMENTAÇÃO
        $codMov = $codMovimento;
        $codMov = sprintf("%-2s", $codMov);
        fwrite($arquivo, $codMov, 2);
        
        //11 DATA DE MOVIMENTAÇÃO
        $dtMov = $dataMovimento;
        $dtMov = sprintf("%-8s", implode('', array_reverse(explode('-', $dtMov)))); //DDMMAAAA
        fwrite($arquivo, $dtMov, 8);
                                                //  indicar  se o empregador já efetuou arrecadação FGTS na Guia de Recolhimento Rescisório para trabalhadores com estes codigos de movimentação
        $arrayMovS = array( "I1", "I3", "I4"); // Rescisões sem justa causa "S"
        $arrayMovN = array( "I2", "L"); // Rescisões com justa causa "N"
        
        if (in_array(trim($codMov), $arrayMovN)){
            $indRecFGTS = "N";
        }elseif(in_array(trim($codMov), $arrayMovS)){
            $indRecFGTS = "S";
        }else{
            $indRecFGTS = NULL; // Demais casos "branco"
        }
        
        //12 INDICATIVO DE RECOLHIMENTO DO FGTS
        $indRecFGTS = sprintf("%-1s", $indRecFGTS);
        fwrite($arquivo, $indRecFGTS, 1);
        
        //13 BRANCOS
        $brancos = sprintf("%-225s", $this->brancos);
        fwrite($arquivo, $brancos, 225);
        
        //14 FINAL DE LINHA
        $fimLinha = sprintf("%-1s", $this->fimLinha);
        fwrite($arquivo, $fimLinha, 1);
        
        fwrite($arquivo, "\r\n");                  
    }
    
    // IMPLEMENTAÇÃO FUTURA
    public function montaReg50() {     
    }
    
    // IMPLEMENTAÇÃO FUTURA
    public function montaReg51() {        
    }
    
    //REGISTRO TIPO 90 - MOVIMENTACAO DO TRABALHADOR
    public function montaReg90($arquivo) {
        //01 TIPO DE REGISTRO
        $tpReg = "90";
        $tpReg = sprintf("%2s", $tpReg);
        fwrite($arquivo, $tpReg, 2);
        
        $marcaFimReg = '9';
        
        for($i=0; $i<51; $i++) {
            $marcaFimReg .= 9;
        }
        
        //02 MARCA FINAL DE REGISTRO
        $marcaFimReg = sprintf("%-51s",$marcaFimReg);
        fwrite($arquivo, $marcaFimReg, 51);
        
        //03 BRANCOS
        $brancos = sprintf("%-306s", $this->brancos);
        fwrite($arquivo, $brancos, 306);
        
        //04 FINAL DE LINHA
        $fimLinha = sprintf("%-1s", $this->fimLinha);
        fwrite($arquivo, $fimLinha, 1);
    }
    
} ?>