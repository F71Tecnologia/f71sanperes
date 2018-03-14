<?php

class c_classificacaoClass {

    public $lote;
    public $projeto;
//    private $api;

    /**
     * 
     */
//    public function __construct() {
//        $this->api = new ApiClass();
//    }

    public function matriz() {
        
        $sqlMatriz = "SELECT * FROM rhempresa WHERE cnpj LIKE '%/0001-%'";
        
        $result = mysql_query($sqlMatriz) or die('Erro: ' . mysql_error());
        
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
    }        
      
    public function lotesCriados($projeto) {

        if ($projeto == null) {
            $where = "";
        } else {
            $where = "AND A.id_projeto = '{$projeto}'";
        }

        $qry = "SELECT A.id_lote AS nrlote, A.lote_numero AS lote, A.data_criacao AS lote_data, 
            B.nome AS nome_projeto, B.id_projeto AS nrprojeto
                    FROM contabil_lote A
                    INNER JOIN rhempresa B ON(B.id_projeto = A.id_projeto)
                    WHERE A.status = 1 $where
                    ORDER BY lote_data DESC";

        $result = mysql_query($qry) or die('Erro ao consultar Lote: ' . mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
    }

    public function lotesAnalise($projeto) {

        if ($projeto == null) {
            $where = "";
        } else {
            $where = "AND A.id_projeto = '{$projeto}'";
        }

        $qry = "SELECT A.id_lote AS nrlote, A.lote_numero AS lote, A.data_criacao AS lote_data, 
            B.nome AS nome_projeto, B.id_projeto AS nrprojeto
                    FROM contabil_lote A
                    INNER JOIN rhempresa B ON(B.id_projeto = A.id_projeto)
                    WHERE A.status = 2 $where
                    ORDER BY lote_data DESC";

        $result = mysql_query($qry) or die('Erro ao consultar Lote: ' . mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
    }

    public function criarLote($projeto, $lote, $id_usuario, $exercicio) {

        if ($lote == null) {
            $sql_ultimo = "SELECT MAX(id_lote) FROM contabil_lote";
            $result = mysql_query($sql_ultimo) or die('Erro' . mysql_error());
            $dado = mysql_fetch_array($result);
            $lote = $dado[0] + 1;
        }

        $qry_lote = "INSERT INTO contabil_lote (id_projeto, lote_numero, data_criacao, usuario_criacao,exercicio)
                        VALUES ('{$projeto}','{$lote}', NOW(), '{$id_usuario}','$exercicio')";

        $result = mysql_query($qry_lote) or die('Erro ao salvar Lote Contabil' . mysql_error());

        return $result;
    }

    public function niveisContas() {
        $sql = "SELECT * FROM contabil_planodecontas
                ORDER BY classificador ASC";
        $result = mysql_query($sql) or die('Erro: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
    }

    public function salvaLancamentoSimples($lote, $projeto, $id_usuario, $dataLancamento) {
        $qry_lancamento = "INSERT INTO contabil_lancamento (id_lote, id_projeto, id_usuario, data_lancamento, status)
                    VALUES ('{$lote}','{$projeto}','{$id_usuario}','{$dataLancamento}','1')";

        $result = mysql_query($qry_lancamento);
        return ($result) ? array('status' => TRUE, 'id_lancamento' => mysql_insert_id()) : array('status' => FALSE, 'msg' => 'Erro ao salvar Classificação!');
    }

    public function salvaLancamentoMultiplos($lote, $projeto, $id_usuario, $dataLancamento) {
        $qry_lancamento = "INSERT INTO contabil_lancamento (id_lote, id_projeto, id_usuario, data_lancamento, status)
                    VALUES ('{$lote}','{$projeto}','{$id_usuario}','{$dataLancamento}','1')";

        $result = mysql_query($qry_lancamento);
        return ($result) ? array('status' => TRUE, 'id_lancamento' => mysql_insert_id()) : array('status' => FALSE, 'msg' => 'Erro ao salvar Classificação!');
    }

    public function preparaArrayItens($item_lancamento) {
        $qry_lancamentos = "INSERT INTO contabil_lancamento_itens (id_lancamento, id_conta, valor, tipo, status, documento, historico)
                    VALUES ('{$item_lancamento['id_lancamento']}','{$item_lancamento['id_conta']}','{$item_lancamento['valor']}','{$item_lancamento['tipo']}','1','{$item_lancamento['documento']}','{$item_lancamento['historico']}')";

        $result = mysql_query($qry_lancamentos);
        return ($result);
    }

    public function cancelarLote($projeto, $lote, $id_usuario) {
        $qry_cancelar = "UPDATE contabil_lote SET status = 0, data_cancelamento = NOW(), usuario_cancelamento = '{$id_usuario}'  WHERE id_projeto = '{$projeto}' AND id_lote = '{$lote}'";

        $result = mysql_query($qry_cancelar);

        return ($result) ? array('status' => TRUE, 'id_lote' => mysql_insert_id()) : array('status' => FALSE, 'msg' => 'Erro ao Excluir Lote!');
    }

    public function encerramento($id_projeto, $ano = null, $mes = null, $inicio = null, $final = null, $sped = true) {

        $periodoAnterior = new DateTime("$ano-" . sprintf('%02d', $mes) . "-01");
        $periodoAnterior->modify("-1 MONTH");
        $auxSped = (!$sped) ? " AND A.sped = 0 " : null;
        $exercicio = (!$ano) ? " AND (C.data_lancamento BETWEEN '{$inicio}' AND '{$final}')" : " AND YEAR(C.data_lancamento) = '{$ano}'";

        $sql_encerramento = "SELECT SUM(IF(A.tipo = 2, -A.valor, A.valor)) total
                            FROM contabil_lancamento_itens A 
                            INNER JOIN contabil_planodecontas B ON (B.id_conta = A.id_conta AND B.`status` = 1) 
                            INNER JOIN contabil_lancamento C ON (C.id_lancamento = A.id_lancamento AND C.`status` = 1) 
                            WHERE B.classificador >= 3 AND C.id_projeto = {$id_projeto} AND A.`status` != 0 $exercicio";
        $qry_encerramento = mysql_query($sql_encerramento) or die('Erro' . mysql_error());
        while ($row = mysql_fetch_assoc($qry_encerramento)) {
            $result = $row;
        }
        return $result;
    }

    public function dre($id_projeto, $ano = null, $mes = null, $inicio = null, $final = null, $sped = true) {

        $periodoAnterior = new DateTime("$ano-" . sprintf('%02d', $mes) . "-01");
        $periodoAnterior->modify("-1 MONTH");
        $auxSped = (!$sped) ? " AND A.sped = 0 " : null;
        $exercicio = (!$ano) ? " AND (C.data_lancamento BETWEEN '{$inicio}' AND '{$final}')" : " AND YEAR(C.data_lancamento) = '{$ano}'";

        $sql_dre = "SELECT A.id_conta, A.tipo, B.descricao, C.data_lancamento, B.classificador AS classificador, B.natureza AS natureza, B.nivel, B.cta_superior,
            SUM(CASE WHEN A.tipo = 2 THEN A.valor ELSE 0 END) AS credito, 
            SUM(CASE WHEN A.tipo = 1 THEN A.valor ELSE 0 END) AS debito, 
            SUM(CASE WHEN A.tipo = 1 THEN A.valor ELSE A.valor * -1 END) AS total, 
            C.id_projeto, D.descricao AS pai1, E.descricao AS pai2, F.descricao AS pai3, D.id_conta AS id_pai1, E.id_conta AS id_pai2, F.id_conta AS id_pai3 
            FROM contabil_lancamento_itens A 
            INNER JOIN contabil_planodecontas B ON (B.id_conta = A.id_conta AND B.`status` = 1) 
            INNER JOIN contabil_lancamento C ON (C.id_lancamento = A.id_lancamento) 
            INNER JOIN contabil_planodecontas D ON(D.id_conta = B.cta_superior) 
            INNER JOIN contabil_planodecontas E ON(D.cta_superior = E.id_conta) 
            INNER JOIN contabil_planodecontas F ON(E.cta_superior = F.id_conta) 
            WHERE B.classificador >= 3 AND C.id_projeto IN('{$id_projeto}',0) AND A.`status` != 0 $exercicio 
            GROUP BY A.id_conta
            ORDER BY B.classificador, B.cta_superior";

        $qry_dre = mysql_query($sql_dre) or die('Erro' . mysql_error());

        while ($row = mysql_fetch_assoc($qry_dre)) {
            $result[$row['id_conta']] = $row;
            $x[] = $row;
            if ($row['tipo'] == 1 && $row['total'] != 0) {
                $arr_receita[$row['id_pai3']]['descricao'] = $row['pai3'];
                $arr_receita[$row['id_pai3']]['array'][$row['id_pai2']]['descricao'] = $row['pai2'];
                $arr_receita[$row['id_pai3']]['array'][$row['id_pai2']]['array'][$row['id_pai1']]['descricao'] = $row['pai1'];
                $arr_receita[$row['id_pai3']]['array'][$row['id_pai2']]['array'][$row['id_pai1']]['array'][$row['id_conta']] = $result[$row['id_conta']];
            } elseif ($row['tipo'] == 2 && $row['total'] != 0) {
                $arr_despesa[$row['id_pai3']]['descricao'] = $row['pai3'];
                $arr_despesa[$row['id_pai3']]['array'][$row['id_pai2']]['descricao'] = $row['pai2'];
                $arr_despesa[$row['id_pai3']]['array'][$row['id_pai2']]['array'][$row['id_pai1']]['descricao'] = $row['pai1'];
                $arr_despesa[$row['id_pai3']]['array'][$row['id_pai2']]['array'][$row['id_pai1']]['array'][$row['id_conta']] = $result[$row['id_conta']];
            }
        }

        $result = array(2 => $arr_receita, 1 => $arr_despesa);
        ksort($result);
        return $result;
    }

    public function balanco($projeto, $ano, $sped = true) {
        
        $cnpj = substr($projeto, 0, 11);

        $auxSped = (!$sped) ? " AND A.sped = 0 " : null;

        $sqlLancamentos = "SELECT B.id_conta, B.id_lancamento_itens, B.tipo, C.contabil, B.valor 
                        FROM contabil_lancamento_itens B
                        INNER JOIN contabil_lancamento C ON(B.id_lancamento = C.id_lancamento)
                        INNER JOIN rhempresa D ON(D.id_projeto = C.id_projeto) 
                        WHERE B.status != 0 AND C.`status` != 0 AND D.cnpj LIKE '$cnpj%' AND YEAR(C.data_lancamento) = '$ano'";
        //    echo "<!--$sqlLancamentos-->";
        $sql = "
        SELECT RPAD(REPLACE(A.classificador,'.',''),'14','0') indice, A.id_conta, A.classificador, A.descricao, A.natureza, A.nivel, lancamentos.tipo, SUM(lancamentos.valor) total
        FROM contabil_planodecontas A
        LEFT JOIN ($sqlLancamentos) AS lancamentos ON(A.id_conta = lancamentos.id_conta)
        WHERE A.status = 1 $auxSped 
        GROUP BY A.id_conta, lancamentos.tipo
        ORDER BY RPAD(REPLACE(A.classificador,'.',''),'14','0') ASC, A.nivel ASC";
//        echo "<!--$sql-->";
        $qry = mysql_query($sql) or die('Erro' . mysql_error());
        while ($row = mysql_fetch_assoc($qry)) {
            $tipo_balance = false;
            $n = explode('.', $row['classificador']);
            $sql2 = "SELECT IF(D.classificador = '2.07.07.01.01', SUM(IF(A.tipo = 2, -A.valor, A.valor)), SUM(IF(A.tipo = 2, A.valor, -A.valor))) saldoAtual
                    FROM contabil_lancamento_itens A                    
                    INNER JOIN contabil_lancamento C ON(C.id_lancamento = A.id_lancamento)
                    INNER JOIN contabil_planodecontas D ON(D.id_conta = A.id_conta)
                    WHERE A.id_conta IN(SELECT id_conta FROM contabil_planodecontas WHERE status = 1 AND classificador LIKE '" . str_replace('00', '', $row['classificador']) . "%') AND C.data_lancamento <= LAST_DAY('$ano-12-01')AND A.`status` != 0";
//            echo $sql2."<br>";
            $row['saldoAtual'] = mysql_result(mysql_query($sql2), 0);
            $sql3 = "SELECT IF(D.classificador = '2.07.07.01.01', SUM(IF(A.tipo = 2, -A.valor, A.valor)), SUM(IF(A.tipo = 2, A.valor, -A.valor))) saldoAnterior
                    FROM contabil_lancamento_itens A
                    INNER JOIN contabil_lancamento C ON(A.id_lancamento = C.id_lancamento AND C.status != 0)
                    INNER JOIN contabil_planodecontas D ON(D.id_conta = A.id_conta)
                    WHERE A.id_conta IN(SELECT id_conta FROM contabil_planodecontas WHERE status = 1 AND classificador LIKE '" . str_replace('00', '', $row['classificador']) . "%') AND C.data_lancamento <= '$ano-12-01' AND A.`status` != 0";
//          echo $sql3."<br>";
            $row['saldoAnterior'] = mysql_result(mysql_query($sql2), 0);
            if (end($n) != '00') {
                $tipo_balanco = true;
            }
            if ($n[0] > 2)
                break;
            if (count($n) == 1)
                $result[$n[0]] = $row;
            if (count($n) == 2)
                $result[$n[0]]['array'][$row['classificador']] = $row;
            if (count($n) == 3)
                $result[$n[0]]['array']["$n[0].$n[1]"]['array'][$row['classificador']] = $row;
            if (count($n) == 4)
                $result[$n[0]]['array']["$n[0].$n[1]"]['array']["$n[0].$n[1].$n[2]"]['array'][$row['classificador']] = $row;
            if ($row['saldoAtual'] > 0 || $row['saldoAtual'] < 0) {
                if (count($n) == 5 && $tipo_balanco)
                    $result[$n[0]]['array']["$n[0].$n[1]"]['array']["$n[0].$n[1].$n[2]"]['array']["$n[0].$n[1].$n[2].$n[3]"]['array'][$row['classificador']] = $row;
            }
        }
        ksort($result);
        //print_array($result);
        return $result;
    }
    
     public function balancoSamperes($ano, $mes) {
        
        $competencia = "$ano-".str_pad($mes, 2, "0", STR_PAD_LEFT);
        $date = new DateTime($competencia);
        $date->sub(new DateInterval('P1M'));
        $competencia_ant = $date->format('Y-m') . "\n";
        
        $rs = "SELECT cnpj FROM master WHERE id_master = {$this->idMaster}";
        $rs = mysql_query($rs);
        while($row = mysql_fetch_assoc($rs)){
            $cnpj = $row['cnpj'];
        }

        $sqlLancamentos = "SELECT B.id_conta, B.id_lancamento_itens, B.tipo, C.contabil, B.valor 
                        FROM contabil_lancamento_itens B
                        INNER JOIN contabil_lancamento C ON(B.id_lancamento = C.id_lancamento)
                        INNER JOIN contabil_planodecontas L ON(L.id_conta = B.id_conta)
                        WHERE B.status != 0 AND C.`status` != 0 AND DATE_FORMAT(C.data_lancamento, '%Y') = '$ano'";
//      echo "<!--$sqlLancamentos-->";
        $sql = "SELECT RPAD(REPLACE(A.classificador,'.',''),'14','0') indice, A.id_conta, A.classificador, A.descricao, A.natureza, A.nivel, lancamentos.tipo, SUM(lancamentos.valor) total
                FROM contabil_planodecontas A
                LEFT JOIN ($sqlLancamentos) AS lancamentos ON(A.id_conta = lancamentos.id_conta)
                WHERE A.status = 1 
                GROUP BY A.id_conta, lancamentos.tipo
                ORDER BY RPAD(REPLACE(A.classificador,'.',''),'14','0') ASC, A.nivel ASC";
//      echo "<!--$sql-->";
        $qry = mysql_query($sql) or die('Erro' . mysql_error());
        while ($row = mysql_fetch_assoc($qry)) {
            
            $tipo_balance = false;
            $n = explode('.', $row['classificador']);
            $sql2 = "SELECT IF(D.classificador = '2.3.05.01', SUM(IF(A.tipo = 2, -A.valor, A.valor)), SUM(IF(A.tipo = 2, A.valor, -A.valor))) saldoAtual
                    FROM contabil_lancamento_itens A                    
                    INNER JOIN contabil_lancamento C ON(C.id_lancamento = A.id_lancamento)
                    INNER JOIN contabil_planodecontas D ON(D.id_conta = A.id_conta)
                    WHERE A.id_conta IN(SELECT id_conta FROM contabil_planodecontas WHERE status = 1 AND classificador LIKE '" .str_replace('00', '', $row['classificador']). "%') AND DATE_FORMAT(C.data_lancamento, '%Y-%m') = '$competencia' AND A.`status` != 0";
//          echo $sql2."<br>";
            $row['saldoAtual'] = mysql_result(mysql_query($sql2), 0);
            $sql3 = "SELECT IF(D.classificador = '2.3.05.01', SUM(IF(A.tipo = 2, -A.valor, A.valor)), SUM(IF(A.tipo = 2, A.valor, -A.valor))) saldoAnterior
                    FROM contabil_lancamento_itens A
                    INNER JOIN contabil_lancamento C ON(A.id_lancamento = C.id_lancamento AND C.status != 0)
                    INNER JOIN contabil_planodecontas D ON(D.id_conta = A.id_conta)
                    WHERE A.id_conta IN(SELECT id_conta FROM contabil_planodecontas WHERE status = 1 AND classificador LIKE '" .str_replace('00', '', $row['classificador']). "%') AND DATE_FORMAT(C.data_lancamento, '%Y-%m') = '$competencia_ant' AND A.`status` != 0";
//          echo $sql3.";<br>";
            $row['saldoAnterior'] = mysql_result(mysql_query($sql2), 0);
            if (end($n) != '00') {
                $tipo_balanco = true;
            }
            if ($n[0] > 2)
                break;
            if (count($n) == 1)
                $result[$n[0]] = $row;
            if (count($n) == 2)
                $result[$n[0]]['array'][$row['classificador']] = $row;
            if (count($n) == 3)
                $result[$n[0]]['array']["$n[0].$n[1]"]['array'][$row['classificador']] = $row;
            if (count($n) == 4)
                $result[$n[0]]['array']["$n[0].$n[1]"]['array']["$n[0].$n[1].$n[2]"]['array'][$row['classificador']] = $row;
            if ($row['saldoAtual'] > 0 || $row['saldoAtual'] < 0) {
                if (count($n) == 5 && $tipo_balanco)
                   $result[$n[0]]['array']["$n[0].$n[1]"]['array']["$n[0].$n[1].$n[2]"]['array']["$n[0].$n[1].$n[2].$n[3]"]['array'][$row['classificador']] = $row;
            }
        
        }
        ksort($result);
        
//        if (WEBSERVICE === true) {
//
//            $arrayRes = array();
//            $c = 0;
//            foreach ($result as $key => $value) {
//                $value['saldoAnterior'].' - '.$value[saldoAtual];
//                $arrayRes[$c]['cnpj'] = $cnpj;
//                $arrayRes[$c]['planocontabil'] = $value['classificador'];
//                $arrayRes[$c]['descricao'] = utf8_encode($value['descricao']);
//                $arrayRes[$c]['saldoAnterior'] = $value['saldoAnterior'];
//                $arrayRes[$c]['saldoAtual'] = $value['saldoAtual'];
//                    
//            }
//                
//        }
//        
        return $result;
    }

    public function getBalancoBrGaap($ano){
        $rs = "SELECT cnpj FROM master WHERE id_master = {$this->idMaster}";
        $rs = mysql_query($rs);
        while($row = mysql_fetch_assoc($rs)){
            $cnpj = $row['cnpj'];
        }
        if(!empty($cnpj)){
            $rs = $this->balancoSamperes($cnpj, $ano);
        }else{
            $rs = 'erro';
        }
        return $rs;
        
    }

    public function balancete($id_projeto, $inicio, $final, $sped = true) {
        $inicio_dt = implode('-', array_reverse(explode('/', $inicio)));
        $final_dt = implode('-', array_reverse(explode('/', $final)));
        if(WEBSERVICE === true){
            $altQeury1 = ",AB.cnpj";
            $altQeury2 = "LEFT JOIN rhempresa AS AB ON (AB.id_projeto = A.id_projeto)";
        }

        if($id_projeto == 0) { 
    
            $sql = "SELECT RPAD(REPLACE(A.classificador,'.',''),'10','0') indice, A.id_conta, A.classificador, A.descricao, A.acesso, A.classificacao analitica_sintetica, A.natureza,
                    (SELECT IF(SUBSTRING(A.classificador, 1, 1) = '1' OR SUBSTRING(A.classificador, 1, 1) = '3', SUM(IF(B.tipo = 2, B.valor, -B.valor)), SUM(IF(B.tipo = 1, B.valor, -B.valor))) 
                    FROM contabil_lancamento_itens B INNER JOIN contabil_lancamento C ON (C.id_lancamento = B.id_lancamento AND C.`status` != 0)
                    WHERE B.id_conta = A.id_conta AND B.`status` != 0 AND A.`status` != 0 AND IF(SUBSTRING(A.classificador, 1, 1) >= '3', C.data_lancamento >= CONCAT(YEAR('{$inicio_dt}'),'-01-01') AND C.data_lancamento < '{$inicio_dt}', C.data_lancamento < '{$inicio_dt}')) saldoAnterior, 
                    (SELECT SUM(IF(B.tipo = 2, B.valor, 0.00)) FROM contabil_lancamento_itens B INNER JOIN contabil_lancamento C ON (C.id_lancamento = B.id_lancamento AND C.`status` != 0) WHERE B.id_conta = A.id_conta AND B.`status` != 0 AND C.data_lancamento BETWEEN '{$inicio_dt}' AND '{$final_dt}') devedora, 
                    (SELECT SUM(IF(B.tipo = 1, B.valor, 0.00)) FROM contabil_lancamento_itens B INNER JOIN contabil_lancamento C ON (C.id_lancamento = B.id_lancamento AND C.`status` != 0) WHERE B.id_conta = A.id_conta AND B.`status` != 0 AND C.data_lancamento BETWEEN '{$inicio_dt}' AND '{$final_dt}') credora, 
                    (SELECT IF(SUBSTRING(A.classificador, 1, 1) = '1' OR SUBSTRING(A.classificador, 1, 1) = '3', SUM(IF(B.tipo = 2, B.valor, -B.valor)), SUM(IF(B.tipo = 1, B.valor, -B.valor))) FROM contabil_lancamento_itens B INNER JOIN contabil_lancamento C ON (C.id_lancamento = B.id_lancamento AND C.`status` != 0) WHERE B.id_conta = A.id_conta AND B.`status` != 0 AND A.`status` != 0 AND IF(SUBSTRING(A.classificador, 1, 1) >= '3', C.data_lancamento >= CONCAT(YEAR('{$inicio_dt}'),'-01-01') AND C.data_lancamento <= '{$final_dt}', C.data_lancamento <= '{$final_dt}')) saldoAtual
                    FROM contabil_planodecontas A 
                    WHERE A.status = 1 
                    GROUP BY A.id_conta, A.cta_superior 
                    ORDER BY indice";
                echo "<!--$sql-->";
        
        } else { 
    
            $sql = "SELECT RPAD(REPLACE(A.classificador,'.',''),'14','0') indice, A.id_conta, A.classificador, A.descricao, A.acesso, A.classificacao analitica_sintetica, A.natureza,
                (SELECT IF(SUBSTRING(A.classificador, 1, 1) = '1' OR SUBSTRING(A.classificador, 1, 1) = '3', SUM(IF(B.tipo = 2, B.valor, -B.valor)), SUM(IF(B.tipo = 1, B.valor, -B.valor)))
                FROM contabil_lancamento_itens B
                INNER JOIN contabil_lancamento C ON (C.id_lancamento = B.id_lancamento AND C.`status` != 0)
                WHERE B.id_conta = A.id_conta AND B.`status` != 0 AND A.`status` = 1 AND B.tipo != 0 AND
                IF(SUBSTRING(A.classificador, 1, 1) >= '3', C.data_lancamento >= CONCAT(YEAR('{$inicio_dt}'),'-01-01') AND C.data_lancamento < '{$inicio_dt}', C.data_lancamento < '{$inicio_dt}') AND C.id_projeto IN({$id_projeto})) saldoAnterior,
                (SELECT SUM(IF(B.tipo = 2, B.valor, 0.00))
                FROM contabil_lancamento_itens B
                INNER JOIN contabil_lancamento C ON (C.id_lancamento = B.id_lancamento AND C.`status` != 0)
                WHERE B.id_conta = A.id_conta AND B.`status` != 0 AND C.data_lancamento BETWEEN '{$inicio_dt}' AND '{$final_dt}' AND C.id_projeto = '{$id_projeto}') devedora, 
                (SELECT SUM(IF(B.tipo = 1, B.valor, 0.00))
                FROM contabil_lancamento_itens B
                INNER JOIN contabil_lancamento C ON (C.id_lancamento = B.id_lancamento AND C.`status` != 0)
                WHERE B.id_conta = A.id_conta AND B.`status` IN(1,2) AND C.data_lancamento BETWEEN '{$inicio_dt}' AND '{$final_dt}' AND C.id_projeto = '{$id_projeto}') credora,
                (SELECT IF(SUBSTRING(A.classificador, 1, 1) = '1' OR SUBSTRING(A.classificador, 1, 4) = '4.02', SUM(IF(B.tipo = 2, B.valor, -B.valor)), SUM(IF(B.tipo = 1, B.valor, -B.valor)))
                FROM contabil_lancamento_itens B
                INNER JOIN contabil_lancamento C ON (C.id_lancamento = B.id_lancamento AND C.`status` != 0)
                WHERE B.id_conta = A.id_conta AND B.`status` != 0 AND A.`status` = 1 AND B.tipo != 0 AND 
                IF(SUBSTRING(A.classificador, 1, 1) >= '3', C.data_lancamento >= CONCAT(YEAR('{$inicio_dt}'),'-01-01') AND C.data_lancamento <= '{$final_dt}', C.data_lancamento <= '{$final_dt}') AND C.id_projeto IN({$id_projeto})) saldoAtual, A.id_projeto $altQeury1
                FROM contabil_planodecontas A
                WHERE A.id_projeto = '{$id_projeto}' AND A.status = 1
                GROUP BY A.id_conta, A.cta_superior
                ORDER BY indice";
            echo "<!--$sql-->";
        }    
        $qry = mysql_query($sql) or die('Erro' . mysql_error());
        $result = $a = array();
        
        while ($row = mysql_fetch_assoc($qry)) {

            $n = explode('.', $row['classificador']);
            
            if(WEBSERVICE === true){
                $result[$row['indice']]['cnpj'] = $row['cnpj'];
            }
            $result[$row['indice']]['id_conta'] = $row['id_conta'];
            $result[$row['indice']]['classificador'] = $row['classificador'];
            $result[$row['indice']]['acesso'] = $row['acesso'];
            $result[$row['indice']]['descricao'] = $row['descricao'];
            $result[$row['indice']]['sped'] = $row['sped'];
            $result[$row['indice']]['analitica_sintetica'] = $row['analitica_sintetica'];
            $result[$row['indice']]['natureza'] = $row['natureza'];

            if (count($n) >= 7) {
                $result[str_pad($n[0] . $n[1] . $n[2] . $n[3] . $n[4] . $n[5] . $n[6], 14, 0)]['saldoAnterior'] += $row['saldoAnterior'];
                $result[str_pad($n[0] . $n[1] . $n[2] . $n[3] . $n[4] . $n[5] . $n[6], 14, 0)]['saldoAtual'] += $row['saldoAtual'];
                $result[str_pad($n[0] . $n[1] . $n[2] . $n[3] . $n[4] . $n[5] . $n[6], 14, 0)]['credora'] += $row['credora'];
                $result[str_pad($n[0] . $n[1] . $n[2] . $n[3] . $n[4] . $n[5] . $n[6], 14, 0)]['devedora'] += $row['devedora'];
            }
            if (count($n) >= 6) {
                $result[str_pad($n[0] . $n[1] . $n[2] . $n[3] . $n[4] . $n[5], 14, 0)]['saldoAnterior'] += $row['saldoAnterior'];
                $result[str_pad($n[0] . $n[1] . $n[2] . $n[3] . $n[4] . $n[5], 14, 0)]['saldoAtual'] += $row['saldoAtual'];
                $result[str_pad($n[0] . $n[1] . $n[2] . $n[3] . $n[4] . $n[5], 14, 0)]['credora'] += $row['credora'];
                $result[str_pad($n[0] . $n[1] . $n[2] . $n[3] . $n[4] . $n[5], 14, 0)]['devedora'] += $row['devedora'];
            }
            if (count($n) >= 5) {
                $result[str_pad($n[0] . $n[1] . $n[2] . $n[3] . $n[4], 14, 0)]['saldoAnterior'] += $row['saldoAnterior'];
                $result[str_pad($n[0] . $n[1] . $n[2] . $n[3] . $n[4], 14, 0)]['saldoAtual'] += $row['saldoAtual'];
                $result[str_pad($n[0] . $n[1] . $n[2] . $n[3] . $n[4], 14, 0)]['credora'] += $row['credora'];
                $result[str_pad($n[0] . $n[1] . $n[2] . $n[3] . $n[4], 14, 0)]['devedora'] += $row['devedora'];
            }
            if (count($n) >= 4) {
                $result[str_pad($n[0] . $n[1] . $n[2] . $n[3], 14, 0)]['saldoAnterior'] += $row['saldoAnterior'];
                $result[str_pad($n[0] . $n[1] . $n[2] . $n[3], 14, 0)]['saldoAtual'] += $row['saldoAtual'];
                $result[str_pad($n[0] . $n[1] . $n[2] . $n[3], 14, 0)]['credora'] += $row['credora'];
                $result[str_pad($n[0] . $n[1] . $n[2] . $n[3], 14, 0)]['devedora'] += $row['devedora'];
            }
            if (count($n) >= 3) {
                $result[str_pad($n[0] . $n[1] . $n[2], 14, 0)]['saldoAnterior'] += $row['saldoAnterior'];
                $result[str_pad($n[0] . $n[1] . $n[2], 14, 0)]['saldoAtual'] += $row['saldoAtual'];
                $result[str_pad($n[0] . $n[1] . $n[2], 14, 0)]['credora'] += $row['credora'];
                $result[str_pad($n[0] . $n[1] . $n[2], 14, 0)]['devedora'] += $row['devedora'];
            }
            if (count($n) >= 2) {

                $result[str_pad($n[0] . $n[1], 14, 0)]['saldoAnterior'] += $row['saldoAnterior'];
                $result[str_pad($n[0] . $n[1], 14, 0)]['saldoAtual'] += $row['saldoAtual'];
                $result[str_pad($n[0] . $n[1], 14, 0)]['credora'] += $row['credora'];
                $result[str_pad($n[0] . $n[1], 14, 0)]['devedora'] += $row['devedora'];
            
                if ($n[0] == 4 && $n[1] == 01) {
                    $result[str_pad($n[0], 14, 0)]['saldoAnterior'] -= $row['saldoAnterior'];
                    $result[str_pad($n[0], 14, 0)]['saldoAtual'] -= $row['saldoAtual'];
                } elseif ($n[0] == 4 && $n[1] == 02) {
                    $result[str_pad($n[0], 14, 0)]['saldoAnterior'] += $row['saldoAnterior'];
                    $result[str_pad($n[0], 14, 0)]['saldoAtual'] += $row['saldoAtual'];
                }
                else {
                    $result[str_pad($n[0], 14, 0)]['saldoAnterior'] += $row['saldoAnterior'];
                    $result[str_pad($n[0], 14, 0)]['saldoAtual'] += $row['saldoAtual'];
                }
            }

            $result[str_pad($n[0], 14, 0)]['credora'] += $row['credora'];
            $result[str_pad($n[0], 14, 0)]['devedora'] += $row['devedora'];

            if (count($n) >= 0) {
                

                $result[$row['indice']]['saldoAnterior'] = $row['saldoAnterior'];
                $result[$row['indice']]['saldoAtual'] = $row['saldoAtual'];
                $result[$row['indice']]['credora'] = $row['credora'];
                $result[$row['indice']]['devedora'] = $row['devedora'];
            }
        }
        ksort($result);
        
        /**
         * RETORNO DO WEBSERVECE
         */
        if(WEBSERVICE === true){
            
            $arrayRes = array();
            $c = 0;
            foreach ($result as $key => $value) {
                
                if ($value['saldoAtual'] > 0 || $value['saldoAnterior'] > 0 || $value['credora'] > 0 || $value['devedora'] > 0) {
                    $arrayRes[$c]['cnpj']           =   $value['cnpj'];
                    $arrayRes[$c]['planocontabil']  =   $value['classificador'];
                    $arrayRes[$c]['descricao']      =   utf8_encode($value['descricao']);
                    $arrayRes[$c]['saldoAnterior']  =   $value['saldoAnterior'];
                    $arrayRes[$c]['debito']         =   $value['devedora'];
                    $arrayRes[$c]['credito']        =   $value['credora'];
                    $arrayRes[$c]['saldoAtual']     =   $value['saldoAtual'];
                    
                    $arrayRes[$c]['estoque']        = "";
                    $arrayRes[$c]['ativomob']       = "";
                    $arrayRes[$c]['deprecia']       = "";
                    
                    $c += 1;
                }
            }
            return $arrayRes;
        }
        
//       print_array($result);
        return $result;
    }

    public function verificar_associacao($id_projeto) {
        $sql = "
        SELECT A.id_entradasaida, A.nome, COUNT(B.id) qtd
        FROM entradaesaida A
        LEFT JOIN (
                SELECT id_saida id, tipo FROM saida WHERE id_projeto = $id_projeto AND YEAR(data_proc) = YEAR(CURDATE())
                UNION
                SELECT id_entrada id, tipo FROM entrada WHERE id_projeto = $id_projeto AND YEAR(data_proc) = YEAR(CURDATE())) B ON (A.id_entradasaida = B.tipo)
        WHERE A.id_entradasaida NOT IN (
                SELECT B.id_entradasaida
                FROM contabil_contas_assoc B
                INNER JOIN contabil_planodecontas C ON (C.id_conta = B.id_conta AND C.id_projeto = $id_projeto)) AND A.grupo >= 5
        GROUP BY A.id_entradasaida ";
        //     echo "<!--$sql-->"; 
        $qry = mysql_query($sql);
        $result = mysql_error();
        while ($row = mysql_fetch_assoc($qry)) {
            $result[$row['id_entradasaida']]['nome'] = $row['nome'];
            $result[$row['id_entradasaida']]['qtd'] = $row['qtd'];
        }
        return $result;
    }

    public function conferencia_associacao($id_projeto, $tipo_assoc) {
        if ($tipo_assoc == 1) {
            $sql = "
            SELECT A.id_entradasaida id, C.nome, B.natureza
            FROM contabil_contas_assoc A
            INNER JOIN contabil_planodecontas B ON(A.id_conta = B.id_conta AND B.id_projeto IN($id_projeto))
            INNER JOIN entradaesaida C ON(A.id_entradasaida = C.id_entradasaida)
            WHERE A.status = 1
            ORDER BY A.id_entradasaida";
        } else if ($tipo_assoc == 2) {
            $sql = "
            SELECT A.id_cod id, B.natureza
            FROM contabil_contas_assoc_folha A
            INNER JOIN contabil_planodecontas B ON(A.id_conta = B.id_conta AND B.id_projeto IN($id_projeto))
            WHERE A.status = 1
            ORDER BY A.id_cod";
        } else if ($tipo_assoc == 3) {
            $sql = "
            SELECT A.id_banco id, C.nome, B.natureza
            FROM contabil_contas_assoc_banco A
            INNER JOIN contabil_planodecontas B ON(A.id_conta = B.id_conta AND B.id_projeto IN($id_projeto))
            INNER JOIN bancos C ON(A.id_banco = C.id_banco)
            WHERE A.status = 1
            ORDER BY A.id_banco";
        } else if ($tipo_assoc == 4) {
            $sql = "
            SELECT DISTINCT A.cod id, A.descicao nome, LEFT(categoria, 1) natureza
            FROM rh_movimentos A
            ORDER BY A.cod";
        }
//        echo "<!--". str_replace('  ','',$sql) ."\r-->";
        $qry = mysql_query($sql);
        $result = mysql_error();
        while ($row = mysql_fetch_assoc($qry)) {
            $result[$row['natureza']][] = array('id' => $row['id'], 'nome' => $row['nome']);
        }
        return $result;
    }

    public function carregarProjeto($id_projeto) {
        $sql = "SELECT * FROM rhempresa A WHERE A.id_projeto = '{$id_projeto}'";
        $qry = mysql_query($sql);
        $result = mysql_error();
        while ($row = mysql_fetch_assoc($qry)) {
//            $result[$row['id_projeto']][] = array('nome' => $row['nome']);
            $result['nome'] = $row['nome'];
            $result['endereco'] = $row['endereco'];
            $result['cnpj'] = $row['cnpj'];
        }
        return $result;
    }

    public function cnpjProjeto($projeto) {

        $cnpj = substr($projeto, 0, 11);

        $sql = "SELECT * FROM rhempresa A WHERE A.cnpj= '{$cnpj}'";
        $qry = mysql_query($sql);
        $result = mysql_error();
        while ($row = mysql_fetch_assoc($qry)) {
            $result['nome'] = $row['nome'];
            $result['endereco'] = $row['endereco'];
            $result['cnpj'] = $row['cnpj'];
        }
        return $result;
    }

}
