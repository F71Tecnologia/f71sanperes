<?php
class PainelAdmClass {
    private $array;
    private $meses;
    private $indices;
    private $totaisMes;
    
    function getArray() {
        return $this->array;
    }
    
    function getMeses() {
        return $this->meses;
    }
    
    function getIndices() {
        return $this->indices;
    }
    
    function getTotaisMes($mes) {
        return $this->totais[$mes];
    }
    
    function getMesesRange($mes_atras = 0, $mes_afrente = 0) {
        $sql = "SELECT * FROM ano_meses WHERE CONCAT(YEAR(CURDATE()),'-', num_mes, '-01') BETWEEN ADDDATE(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL -$mes_atras MONTH) AND LAST_DAY(ADDDATE(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL $mes_afrente MONTH))";
        $qry = mysql_query($sql) or die('ERRO getUltimosTresMeses: ' . mysql_error() . '<br>' . print_array($sql));
        while($row = mysql_fetch_assoc($qry)) {
            $this->meses[(int)$row['num_mes']] = $row['nome_mes'];
        }
        
        /**
         * DEBUG
         */
        if($_COOKIE['debug'] == 666){
            print_array("getMeses()");
            print_array($sql);
            print_array($this->meses);
        }
    }
    
    function getPainelFinanceiro($id_regiao, $id_projeto = null) {
        $this->getMesesRange(3,-1);
        $auxProjeto = ($id_projeto) ? " AND C.id_projeto = {$id_projeto}" : null;
        
        /**
        * ARRAY SAIDAS
        */
        $sql = "
        SELECT id, nome, CONCAT(id, ' - ', nome) AS indice, id_projeto, MONTH(data_vencimento) AS mes, SUM(valor) AS valor FROM (
            SELECT A.id_grupo AS id, A.nome_grupo AS nome, C.id_projeto, C.data_vencimento,
            CAST(CONCAT(SUBSTR(REPLACE(REPLACE(C.valor, ',', ''), '.', ''),1,LENGTH(C.valor)-3), '.', SUBSTR(REPLACE(REPLACE(C.valor, ',', ''), '.', ''),-2)) AS DECIMAL(15,2)) AS valor
            FROM entradaesaida_grupo A
            LEFT JOIN entradaesaida B ON (A.id_grupo = B.grupo)
            LEFT JOIN saida C ON (C.tipo = B.id_entradasaida)
            WHERE 
            C.id_projeto IN (SELECT id_projeto FROM projeto WHERE id_regiao = '{$id_regiao}' AND status_reg = 1) AND 
            C.id_regiao = '{$id_regiao}' AND 
            A.id_grupo >= 10 AND 
            C.status = 2 AND 
            C.data_vencimento BETWEEN ADDDATE(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL -3 MONTH) AND LAST_DAY(ADDDATE(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL -1 MONTH))
            $auxProjeto
            ORDER BY A.id_grupo, C.data_vencimento
        ) AS A1
        GROUP BY id, MONTH(data_vencimento), id_projeto
        -- LIMIT 1";
        $qry = mysql_query($sql) or die('ERRO getPainelFinanceiro: ' . mysql_error() . '<br>' . print_array($sql));
        while($row = mysql_fetch_assoc($qry)) {
            $array[$row['id_projeto']][$row['id']][$row['mes']] = $row['valor'];
            $this->indices[$row['id']] = $row['indice'];
            $this->totais[$row['mes']]++;
        }
        
        /**
         * PREENCHENDO OS CAMPOS VAZIOS DO ARRAY
         */
        foreach ($array as $k1 => $ar) {
            foreach ($this->indices as $k2 => $value) {
                if(!array_key_exists($k2, $ar)){
                    foreach ($this->meses as $key => $value) {
                        $array[$k1][$k2][$key] = 0.00;
                    }
                }
            }
            
            foreach ($ar as $k2 => $ar2) {
                foreach ($this->meses as $key => $value) {
                    if(!array_key_exists($key, $ar2)){
                        $array[$k1][$k2][$key] = 0.00;
                    }
                }
            }
        }
        $this->array = $array;
        
        /**
         * DEBUG
         */
        if($_COOKIE['debug'] == 666){
            print_array("getPainelFinanceiro()");
            print_array($sql);
            print_array($this->indices);
            print_array($this->array);
        }
    }
    
    private function getNomeAdvogados($id_advogado = 0) {
        $id_advogado = ($id_advogado > 0) ? $id_advogado : 0;
        $sql = "SELECT adv_nome nome FROM advogados WHERE adv_id IN ($id_advogado)";
        $qry = mysql_query($sql) or die('ERRO getNomeAdvogados: ' . mysql_error() . '<br>' . print_array($sql));
        while($row = mysql_fetch_assoc($qry)) {
            $array[] = $row['nome'];
        }
        return implode(', ', $array).'<br>';
    }
    
    function getPainelJuridico($id_regiao, $id_projeto = null) {
        $this->getMesesRange(1,1);
        $auxProjeto = ($id_projeto) ? " AND A.id_projeto = {$id_projeto}" : null;
        
        /**
        * ARRAY PROCESSOS
        */
        $sql = "
        SELECT A.proc_id AS id, G.nome AS projeto, A.proc_nome AS nome, C.andamento_data_movi AS data, C.andamento_horario AS hora, MONTH(C.andamento_data_movi) AS mes, E.proc_status_nome AS status, A.adv_id, D.prep_nome AS preposto, B.n_processo_numero AS nprocesso, F.proc_tipo_nome AS tipo
        FROM processos_juridicos A 
        LEFT JOIN n_processos B ON (A.proc_id = B.proc_id AND B.status = 1)
        LEFT JOIN proc_trab_andamento C ON (A.proc_id = C.proc_id AND C.andamento_status = 1 AND C.proc_status_id > 1)
        LEFT JOIN prepostos D ON (A.preposto_id = D.prep_id AND D.prep_status = 1)
        LEFT JOIN processo_status E ON (C.proc_status_id = E.proc_status_id)
        LEFT JOIN processo_tipo F ON (A.proc_tipo_id = F.proc_tipo_id)
        LEFT JOIN projeto G ON (A.id_projeto = G.id_projeto)
        WHERE 
        C.andamento_data_movi BETWEEN ADDDATE(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL -1 MONTH) AND LAST_DAY(ADDDATE(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH))
        AND A.status = 1 AND A.id_regiao = {$id_regiao}
        $auxProjeto
        ORDER BY C.andamento_data_movi ASC, C.andamento_horario ASC";
        $qry = mysql_query($sql) or die('ERRO getPainelJuridico: ' . mysql_error() . '<br>' . print_array($sql));
        while($row = mysql_fetch_assoc($qry)) {
            $row['advogados'] = $this->getNomeAdvogados($row['adv_id']);
//            $this->array[$row['id_projeto']][$row['mes']][$row['data']][$row['id']] = $row;
//            $this->array[$row['mes']][$row['data']][$row['id']] = $row;
//            $this->array[$row['mes']][$row['tipo']][] = $row;
            $this->array[$row['mes']][$row['projeto']][$row['tipo']][] = $row;
            $this->totais[$row['mes']]++;
            $this->totais[$row['projeto']][$row['mes']]++;
        }
        
        /**
         * DEBUG
         */
        if($_COOKIE['debug'] == 666){
            print_array("getPainelJuridico()");
            print_array($sql);
            print_array($this->array);
        }
    }
}