<?php

/**
 * Classe simplificada do Vale refeicao e do Vale Alimentacao
 *
 * @author F71sistemasweb
 */
class ValeAlimentacaoRefeicaoClass {

    private $table = 'rh_va_pedido';
    private $id_table = 'id_va_pedido';
    public $status_falta = false;
    public $status_ferias = false;
    public $status_evento = false;
    
    public function __construct() {

    }

    public function salvar($obj) {
        if (isset($obj['PHPSESSID'])) {
            unset($obj['PHPSESSID']);
        }

        if (isset($obj[$this->id_table]) && !empty($obj[$this->id_table])) {
            $id = $obj[$this->id_table];
            unset($obj[$this->id_table]);
            //print_r($obj);exit;
            $this->sqlUpdate($this->table, $obj, array($this->id_table => $id));
        } else {
//            $obj['data_cad'] = (!isset($obj['data_cad'])) ? date('Y-m-d H:i:s') : $obj['data_cad'];
            $campos = array_keys($obj);
            $this->sqlInsert($this->table, $campos, $obj);
            $id = mysql_insert_id();
        }

        return $id;
    }

    public function consultar($condicoes) {
        return $this->sqlQuery($this->table, '*', $condicoes, $this->id_table);
    }
    
    public function listar($categoria, $id_pedido = null){
        if(!is_null($id_pedido)){
            $and_pedido = "AND a.id_va_pedido = {$id_pedido}";
        }
        
        $sql = "SELECT *, b.regiao AS nome_regiao, c.nome AS nome_func, DATE_FORMAT(data,'%d/%m/%Y %H:%i:%s') AS data_proc, a.id_regiao
                FROM rh_va_pedido AS a
                INNER JOIN regioes AS b ON a.id_regiao = b.id_regiao
                INNER JOIN funcionario AS c ON a.user = c.id_funcionario
                WHERE a.status = 1 AND categoria_vale = '{$categoria}' {$and_pedido}
                ORDER BY data DESC";
                
        if(!is_null($id_pedido)){
            $qry = mysql_query($sql) or die(mysql_error());
            $res = mysql_fetch_assoc($qry);
            
            return $res;
        }else{
            return $this->sqlExecQuery($sql);
        }
    }
    
    public function getEmpregador($id_regiao) {
        $sql = "SELECT *
            FROM rhempresa AS A
            WHERE A.id_regiao = {$id_regiao}";
        $qry = mysql_query($sql) or die('ERRO getEmpregador');
        $res = mysql_fetch_assoc($qry);
        
        return $res;
    }
    
    public function getUnidade($id_pedido) {
        $sql = "SELECT A.id_va_pedido AS id_pedido, C.codigo_sodexo, C.unidade AS nome_unidade, C.id_unidade
            FROM rh_va_relatorio AS A
            LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
            LEFT JOIN unidade AS C ON(B.id_unidade = C.id_unidade)
            WHERE A.id_va_pedido = {$id_pedido} AND C.unidade != ''
            GROUP BY C.unidade
            ORDER BY C.unidade";
        $qry = mysql_query($sql) or die('ERRO getUnidade');
        
        return $qry;
    }
    
    public function getFeriados($mes, $ano, $lista = false, $count = false, $ini = "", $fim = "") {
        
        $and = "";
        
        if(($ini != "") && ($fim != "")){
            $and = "AND dt BETWEEN '{$ini}' AND '{$fim}'";
        }
        
        $query = "
            SELECT * FROM(
                SELECT A.*, WEEKDAY(DATE_FORMAT(A.data, '{$ano}-%m-%d')) AS num_dia,
                        IF(A.movel = 1, A.data, DATE_FORMAT(A.data, '{$ano}-%m-%d')) AS dt
                FROM rhferiados AS A
            ) AS tmp WHERE num_dia NOT IN(5,6) AND MONTH(dt) = {$mes} AND YEAR(dt) = {$ano} AND status = 1 {$and}
        ";
            
        if($_COOKIE['debug'] == 666){
            echo $query;
        }
        
        $sql = mysql_query($query) or die("ERRO getFeriados");
        
        if($lista){
            $result = mysql_fetch_assoc($sql);
            
            return $result;
        }elseif($count){
            $tot = mysql_num_rows($sql);
            
            return $tot;
        }else{
            return $sql;
        }
    }
    
    public function getSabadosNoMes($mes, $ano, $lista = false, $count = false) {
        $query = "SELECT *
            FROM ano AS A
            WHERE MONTH(A.data) = {$mes} AND YEAR(A.data) = {$ano} AND A.fds = 1 AND WEEKDAY(A.data) = 5";
        
        $sql = mysql_query($query) or die("ERRO getFeriados");
        
        if($lista){
            $result = mysql_fetch_assoc($sql);
            
            return $result;
        }elseif($count){
            $tot = mysql_num_rows($sql);
            
            return $tot;
        }else{
            return $sql;
        }
    }
    
    public function getListaClts($regiao = null, $id_clt = null, $sem_vt = false) {
        if(!is_null($id_clt)){
            $and_clt = "AND a.id_clt = {$id_clt}";
        }
        
        if(!is_null($regiao)){
            $and_regiao = "AND a.id_regiao = '{$regiao}'";
        }
        
        //CLTS ATIVOS SEM INFORMAÇÃO DE VT
        if($sem_vt){
            $and_vt = "AND (a.transporte = 0 OR a.transporte IS NULL OR a.transporte = '')";
        }else{
            $and_vt = "AND a.transporte = 1";
        }
        
        $query = "SELECT a.id_clt, a.nome, a.matricula, a.data_nasci, a.data_entrada, a.cpf, a.id_curso, b.nome AS nome_funcao, b.valor AS salario, a.id_unidade, a.status, a.transporte,
                c.unidade AS nome_unidade, e.horas_semanais, e.dias_semana, e.segunda_a_sabado, a.vale_refeicao, a.valor_refeicao, f.especifica AS nome_status
                FROM rh_clt AS a
                INNER JOIN curso AS b ON a.id_curso = b.id_curso
                INNER JOIN unidade AS c ON a.id_unidade = c.id_unidade
                INNER JOIN rhsindicato AS d ON a.rh_sindicato = d.id_sindicato
                INNER JOIN rh_horarios AS e ON a.rh_horario = e.id_horario
                INNER JOIN rhstatus AS f ON a.status = f.codigo
                WHERE (a.status < 60 OR a.status = 70) {$and_vt} {$and_clt} {$and_regiao}
                ORDER BY c.unidade, a.nome";
        $result = mysql_query($query) or die("ERRO getListaClts");
        
        if(!is_null($id_clt)){
            $return = mysql_fetch_assoc($result);
            
            return $return;
        }else{
            return $result;
        }
    }
    
    function diasUteis($mes, $ano, $feriado = true){
        $uteis = 0;
        
        // OBTEM O NUMERO DE DIAS DO MÊS
        $dias_no_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);                
        
        for($dia = 1; $dia <= $dias_no_mes; $dia++){
            
            // OBTEM O TIMESTAMP
            $timestamp = mktime(0, 0, 0, $mes, $dia, $ano);
            $semana = date("N", $timestamp);
            
            if($semana < 6){
                $uteis++;
            }
        }
        
        if($feriado){        
            $tot_feriados = $this->getFeriados($mes, $ano, false, true);
            $uteis -= $tot_feriados;
        }
        
        return $uteis;
    }
    
    // DIAS UTEIS ENTRE DUAS DATAS
    function diasUteisData($startDate, $endDate){
        $begin = strtotime($startDate);
        $end = strtotime($endDate);

        if ($begin > $end) {
            echo "startdate is in the future! <br />";

            return 0;
        } else {
            $no_days  = 0;
            $weekends = 0;
            while ($begin <= $end) {
                $no_days++; // no of days in the given interval
                $what_day = date("N", $begin);
                if ($what_day > 5) { // 6 and 7 are weekend days
                    $weekends++;
                };
                $begin += 86400; // +1 day
            };
            $working_days = $no_days - $weekends;

            return $working_days;
        }
    }

    public function getById($id) {
        return $this->sqlQueryFirst($this->table, '*', array($this->id_table => $id));
    }

    public function excluir($id) {
        return $this->sqlUpdate($this->table, '`status` = 0', "{$this->id_table} = {$id}");
    }

    public function sqlQuery($tabela, $campos = "*", $condicao = null, $order = null, $limit = null, $return = "array", $debug = FALSE, $groupBy = null) {
        $_where = "";
        $_order = "";
        $_limit = "";
        $_groupBy = "";
        $_joins = "";
        if (is_array($campos)) {
            $_camp = implode(",", $campos);
        } else {
            $_camp = $campos;
        }

        if ($condicao !== null) {
            $_where = " WHERE ";
            if (is_array($condicao)) {
                foreach ($condicao as $k => $val) {
                    $_where .= " " . $k . " = '" . $val . "' AND";
                }
                $_where = substr($_where, 0, -3);
            } else {
                $_where .= $condicao;
            }
        }

        if ($order !== null) {
            $_order = " ORDER BY ";
            if (is_array($order)) {
                foreach ($order as $k => $val) {
                    $_order .= $k . " " . $val . ",";
                }
                $_order = substr($_order, 0, -1);
            } else {
                $_order .= $order;
            }
        }

        if ($limit !== null) {
            $_limit = " LIMIT {$limit}";
        }

        if ($groupBy !== null) {
            if (is_array($groupBy)) {
                $_groupBy = " GROUP BY " . implode(",", $groupBy) . "";
            } else {
                $_groupBy = " GROUP BY {$groupBy}";
            }
        }


        if (count($this->joins) > 0) {
            $tabela .= " AS A ";
            foreach ($this->joins as $val) {
                //$_joins
                //"LEFT JOIN usuarios AS B ON (A.id_usuario = B.id_usuario)"
                $_joins .= $val['mod'] . " JOIN " . $val['tab'] . " AS " . $val['ali'] . " ON (" . $val['on'] . ") \r\n";
            }
        }

        $query = "SELECT {$_camp} FROM {$tabela}{$_joins}{$_where}{$_groupBy}{$_order}{$_limit}";

        $result = mysql_query($query);

//        echo (isset($_COOKIE['teste']))?$query." - ".$return." - ". $result ." - ".mysql_num_rows($result).";<br>":'';
        //TRATAMENTO DE ERRO MYSQL
        if (mysql_errno() > 0) {
            $errno = mysql_errno();
            $erro = "Erro referente ao Banco de Dados (consulta MySQL)";
            $msg = mysql_error() . "<br>$query";
            $url = $_GET['url'];
            echo "ERRO DE MYSQL: $errno - $erro - $msg - $url";
            exit;
        }

        if ($debug) {
            echo $query;
        }

        if ($return == "array") {
            $return = array();
            $count = 1;
            //Quando o mysql_query não retorna valor, ele seta a variavel $result=false
            //Se tentar rodar o mysql_num_rows(false), gera um WARNING
            //Por esse motivo fazemos primeiro o teste do $result !== false
            if ($result !== false && mysql_num_rows($result) > 0) {
                while ($row = mysql_fetch_assoc($result)) {
                    foreach ($row as $k => $val) {
                        $return[$count][$k] = $val;
                    }
                    $count++;
                }
            }
        } else {
            $return = $result;
        }

        return $return;
    }

    public function sqlQueryFirst($tabela, $campos = "*", $condicao = null, $order = null, $limit = null, $debug = false) {
        $rs = $this->sqlQuery($tabela, $campos, $condicao, $order, $limit, "array", $debug);
        return current($rs);
    }

    public function sqlInsert($tabela, $campos, $valores, $debug = false) {
        $palavrasReservadas = array("NOW()"); //depois montar uma forma de retirar as aspas simples das palavras reservados do mysql
        $_campos = "";
        $_valores = "";
        $qr = "INSERT INTO {$tabela} ";

        if (is_array($campos)) {
            $_campos = implode(",", $campos);
        } else {
            $_campos = $campos;
        }

        if (is_array($valores)) {
            //VERIFICANDO SE É UMA MATRIZ
            if (count($valores) == count($valores, COUNT_RECURSIVE)) {
                $_valores = "('" . implode("','", $valores) . "')";
            } else {
                foreach ($valores as $val) {
                    $_valores .= "('" . implode("','", $val) . "'),";
                }
                $_valores = substr($_valores, 0, -1);
            }
        } else {
            $_valores = "(" . $valores . ")";
        }

        $qr .= "($_campos) VALUES $_valores";
        if ($debug) {
            echo $qr . "<br/>";
        } else {
            mysql_query($qr);
            //TRATAMENTO DE ERRO MYSQL
            if (mysql_errno() > 0) {
                $errno = mysql_errno();
                $erro = "Erro referente ao Banco de Dados (consulta MySQL)";
                $msg = mysql_error();
                $url = $_GET['url'];
                echo "ERRO DE MYSQL: $errno - $erro - $msg - $url";
                exit;
            }
        }
        return mysql_insert_id();
    }

    public function sqlUpdate($tabela, $campos, $condicao, $debug = false) {
        $_campos = "";
        $_condicao = "";
        $qr = "UPDATE {$tabela} SET ";

        if (is_array($campos)) {
            foreach ($campos as $k => $val) {
                $_campos .= " {$k}='{$val}',";
            }
            $_campos = substr($_campos, 0, -1);
        } else {
            $_campos = $campos;
        }

        if (is_array($condicao)) {
            foreach ($condicao as $k => $val) {
                $_condicao .= " {$k}={$val} AND ";
            }
            $_condicao = substr($_condicao, 0, -4);
        } else {
            $_condicao = $condicao;
        }

        $qr .= $_campos . " WHERE " . $_condicao;

        if ($debug) {
            echo $qr . "<br/>";
            exit;
        } else {
            mysql_query($qr);

            if (mysql_errno() > 0) {
                echo $qr;
                $errno = mysql_errno();
                $erro = "Erro referente ao Banco de Dados (consulta MySQL)";
                $msg = mysql_error();
                $url = $_GET['url'];
                echo "ERRO DE MYSQL: $errno - $erro - $msg - $url";
                exit;
            }
        }
        return true;
    }

    public function sqlExecQuery($sql, $return = "array") {
        //echo $sql."<br>";
        $result = mysql_query($sql);

        if (mysql_errno() > 0) {
            $errno = mysql_errno();
            $erro = "Erro referente ao Banco de Dados (consulta MySQL)";
            $msg = mysql_error();
            $url = $_GET['url'];
            echo "ERRO DE MYSQL: $errno - $erro - $msg - $url";
            exit;
        }

        if ($return == "array") {
            $return = array();
            $count = 1;

            if (!empty($result) && mysql_num_rows($result) > 0) {
                while ($row = mysql_fetch_assoc($result)) {
                    foreach ($row as $k => $val) {
                        $return[$count][$k] = $val;
                    }
                    $count++;
                }
            }
        } else {
            $return = $result;
        }

        return $return;
    }

    public function addJoin($tabela, $alias, $on, $mode = "LEFT") {
        $jo = array('tab' => $tabela, 'ali' => $alias, 'on' => $on, 'mod' => $mode);
        $this->joins[] = $jo;
    }
    
    public function calculaVR($id_clt, $valor, $ano, $mes, $status_clt, $debug = false) {
        $id_clt_debug = $_COOKIE['clt'];
        $dias_vr = 30;
        $valorVR = $valor / $dias_vr;
        $mes = sprintf('%02d', $mes);
        
        if($id_clt == $id_clt_debug){
            echo "INICIAL: clt: {$id_clt} | valor/mes: {$valor} | valor/dia: {$valorVR} | dias_vr: {$dias_vr}<br><br>";                
        }
        
        $this->status_falta = false;
        $this->status_ferias = false;
        $this->status_evento = false;
        
        $dados_clt = $this->getListaClts(null, $id_clt);
        
        $horas_semanais = $dados_clt['horas_semanais'];
        $nome_funcao = $dados_clt['nome_funcao'];
        $dias_semana = $dados_clt['dias_semana'];
        
        //CONDIÇÃO PARA CLTS COM HORARIO SEGUNDA A SÁBADO
//        if($dados_clt['segunda_a_sabado']){
//            $sabados = $this->getSabadosNoMes($mes, $ano, false, true);
//            
//            $dias_vr += $sabados;
//        }
        
        /*
         * COMENTANDO POIS ESSA CONDIÇÃO É SÓ
         * PARA PESSOAL QUE RECEBE POR DIA
         */
//        $func36 = strripos($nome_funcao, "12x36");
        
        //CONDIÇÃO PARA HORARIO 12X36
//        if($func36){
//            $dias_no_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
//            
//            $dias_vr = round($dias_no_mes / 2);
//        }
        
        if($id_clt == $id_clt_debug){
            echo "<br>ANTES DAS FALTAS: {$dias_vr} <br>";
        }
        
        // FALTAS
        $sql_faltas = "SELECT A.qnt
            FROM rh_movimentos_clt AS A
            WHERE A.id_clt = {$id_clt} AND A.status IN(1,5) AND A.id_mov IN(62,232) AND A.mes_mov = MONTH(DATE_SUB('{$ano}-{$mes}-01', INTERVAL 1 MONTH)) AND ano_mov = '{$ano}'";
        $qr_faltas = mysql_query($sql_faltas) or die("ERRO NO CALCULO DE FALTAS");
        
        while ($faltas = mysql_fetch_assoc($qr_faltas)) {
            $dias_vr -= $faltas['qnt'];
            
            if($faltas['qnt'] > 0){
                $this->status_falta = true;
            }
        }
        
        if($id_clt == $id_clt_debug){
            echo "<br>DEPOIS DAS FALTAS: {$dias_vr} <br>";
        }
        
        if($id_clt == $id_clt_debug){
            echo "<br>ANTES DAS FERIAS: {$dias_vr} <br>";
        }
        
        // FERIAS
        $sql_ferias = "SELECT *, DATE_FORMAT(data_fim, '%d-%m-%Y') AS data_fim, DATE_FORMAT(data_ini, '%d-%m-%Y') AS data_ini, DATE_FORMAT(ADDDATE(LAST_DAY(SUBDATE(data_fim, INTERVAL 1 MONTH)), 1), '%d-%m-%Y') as data_ini2, DATE_FORMAT(LAST_DAY(data_ini), '%d-%m-%Y') as ultimo_dia
            FROM rh_ferias
            WHERE id_clt = {$id_clt} AND '{$ano}-{$mes}' BETWEEN DATE_FORMAT(data_ini, '%Y-%m') AND DATE_FORMAT(data_fim, '%Y-%m') AND STATUS = 1
            ORDER BY id_ferias DESC";
        $qr_ferias = mysql_query($sql_ferias) or die("ERRO NO CALCULO DE FERIAS");
        
        while($rs_ferias = mysql_fetch_assoc($qr_ferias)){
            if($rs_ferias['mes'] == $mes){
                $dtI = $rs_ferias['data_ini'];
                $dtF = $rs_ferias['ultimo_dia'];
            }else{
                $dtI = $rs_ferias['data_ini2'];
                $dtF = $rs_ferias['data_fim'];
            }
            
            $dias_ferias = diferencaDias($dtI, $dtF, '-');
            $dias_vr -= $dias_ferias;
            
            if($dias_ferias > 0){
                $this->status_ferias = true;
            }
        }
        
        if($id_clt == $id_clt_debug){
            echo "<br>DEPOIS DAS FERIAS: {$dias_vr} <br>";
        }
        
        if($id_clt == $id_clt_debug){
            echo "<br>ANTES DO EVENTO: {$dias_vr} <br>";
        }
        
        // EVENTOS
//        $sql_eventos = "
//            SELECT A.status,
//                IF(DATE_FORMAT(A.data, '%Y-%m') = '{$ano}-{$mes}',
//                    DATE_FORMAT(A.data, '%d-%m-%Y'),
//                    DATE_FORMAT(ADDDATE(LAST_DAY(SUBDATE(A.data_retorno, INTERVAL 1 MONTH)), 1), '%d-%m-%Y')
//                ) AS dataI_evento,
//                IF(DATE_FORMAT(A.data_retorno, '%Y-%m') = '{$ano}-{$mes}',
//                    DATE_FORMAT(A.data_retorno, '%d-%m-%Y'),
//                    DATE_FORMAT(LAST_DAY(A.data), '%d-%m-%Y')
//                ) AS dataF_evento
//            FROM rh_eventos AS A
//            WHERE (DATE_FORMAT(A.data, '%Y-%m') = '{$ano}-{$mes}' OR DATE_FORMAT(A.data_retorno, '%Y-%m') = '{$ano}-{$mes}') AND A.id_clt = {$id_clt} AND A.status = 1";
        
        $sql_eventos = "
            SELECT A.id_clt, A.cod_status, A.`data`, A.data_retorno,                
                IF(DATE_FORMAT(A.data_retorno, '%Y-%m') = '{$ano}-{$mes}', DATE_FORMAT('{$ano}-{$mes}-01', '%d-%m-%Y'), DATE_FORMAT(A.`data`, '%d-%m-%Y')) AS data_ini,
                DATE_FORMAT(A.data_retorno, '%d-%m-%Y') AS data_fim, 
                DATE_FORMAT(LAST_DAY('{$ano}-{$mes}-01'), '%d-%m-%Y') as ultimo_dia
            FROM rh_eventos AS A
            INNER JOIN(
                SELECT MAX(data) AS data, id_clt FROM rh_eventos WHERE status = 1 AND id_clt = {$id_clt} AND cod_status != 10 GROUP BY id_clt
            ) AS B ON(A.id_clt = B.id_clt AND A.data = B.data)
            WHERE A.status = 1 AND A.id_clt = {$id_clt} AND A.cod_status != 10 
            AND (('{$ano}-{$mes}' BETWEEN DATE_FORMAT(A.data, '%Y-%m') AND DATE_FORMAT(A.data_retorno, '%Y-%m')) OR (A.data_retorno = '0000-00-00'))
            AND A.`data` <= LAST_DAY('{$ano}-{$mes}-01')";
        
        $qr_eventos = mysql_query($sql_eventos) or die("ERRO NO CALCULO DE EVENTO");                
        
        while($rs_eventos = mysql_fetch_assoc($qr_eventos)){
//            $dtI_evento = $rs_eventos['dataI_evento'];
//            $dtF_evento = $rs_eventos['dataF_evento'];                        
            
            if($rs_eventos['data_retorno'] == '0000-00-00'){
                $dtI = "01-{$mes}-{$ano}";
                $dtF = $rs_eventos['ultimo_dia'];
            }else{
                $dtI = $rs_eventos['data_ini'];
                $dtF = $rs_eventos['data_fim'];
            }
            
            if($id_clt == $id_clt_debug){
                echo "<br><br><b>Query Eventos (sql_eventos):</b><br>{$sql_eventos}";
                echo "<br><br><b>Array Eventos (rs_eventos):</b>";
                print_array($rs_eventos);
            }
            
            $dias_evento = diferencaDias($dtI, $dtF, '-');
            $dias_vr -= $dias_evento;
            
            if($dias_evento > 0){
                $this->status_evento = true;
            }
        }
        
        if($id_clt == $id_clt_debug){
            echo "<br>DEPOIS DO EVENTO: {$dias_vr} <br>";
        }
        
//        if($debug){
//            if($_COOKIE['logado'] == 353){
//                if($id_clt == $id_clt_debug){
//                    echo "clt: {$id_clt} dias vr: {$dias_vr} valor vt: {$valorVR}<br>";
//                }
//            }
//        }
        
        $valor_final = $valorVR * $dias_vr;                
        
        // PESSOAS EM EVENTO NO MÊS TODO
//        if((mysql_num_rows($qr_eventos) == 0) && ($status_clt != 10) && ($status_clt != 40)){
//            if($id_clt == $id_clt_debug){
//                if($_COOKIE['logado'] == 353){
//                    echo "<br><br><strong>Mês todo em evento</strong> >> CLT: {$id_clt} | STATUS: {$status_clt}<br><br>";
//                }
//            }
//            
//            $valor_final = 0;
//        }
        
        if($valor_final < 0){
            $valor_final = 0;
        }
        
        if($valor_final == 0){
            //$feriasStatus = false;
            //$faltasStatus = false;
        }
        
        return $valor_final;
    }
    
    public function calculaVRDia($id_clt, $valor_dia, $ano, $mes, $status_clt, $debug = false) {
        $id_clt_debug = $_COOKIE['clt'];
        
        $dias_vr = $this->diasUteis($mes, $ano);                
        
        if($_COOKIE['logado'] == 353){
            if($id_clt == $id_clt_debug){
                echo "INICIAL: clt: {$id_clt} | valor_dia: {$valor_dia} | dias_vr: {$dias_vr}<br><br>";                
            }
        }
        
        $mes = sprintf('%02d', $mes);
        
        $this->status_falta = false;
        $this->status_ferias = false;
        $this->status_evento = false;
        
        $dados_clt = $this->getListaClts(null, $id_clt);
        
        $horas_semanais = $dados_clt['horas_semanais'];
        $nome_funcao = $dados_clt['nome_funcao'];
        $dias_semana = $dados_clt['dias_semana'];
        
        //CONDIÇÃO PARA CLTS COM HORARIO SEGUNDA A SÁBADO
        if($dados_clt['segunda_a_sabado']){
            if($id_clt == $id_clt_debug){
                echo "<br><strong>SEGUNDA A SABADO</strong><br>";
            }
            
            $sabados = $this->getSabadosNoMes($mes, $ano, false, true);
            
            $dias_vr += $sabados;
        }
        
        $func36 = strripos($nome_funcao, "12x36");
        
        //CONDIÇÃO PARA HORARIO 12X36
        if($func36){
            $dias_no_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
            
            $dias_vr = round($dias_no_mes / 2);
                        
            if($id_clt == $id_clt_debug){
                echo "<br><strong>12x36:</strong> CLT: {$id_clt} | DIAS NO MÊS: $dias_no_mes | DIAS VR: {$dias_vr} <br>";
            }
        }
        
        if($id_clt == $id_clt_debug){
            echo "<br>ANTES DAS FALTAS: {$dias_vr} <br>";
        }
        
        // FALTAS
        $sql_faltas = "SELECT A.qnt
            FROM rh_movimentos_clt AS A
            WHERE A.id_clt = {$id_clt} AND A.status IN(5, 1) AND A.id_mov IN(62,232) AND A.mes_mov = MONTH(DATE_SUB('{$ano}-{$mes}-01', INTERVAL 1 MONTH)) AND ano_mov = '{$ano}'";
        $qr_faltas = mysql_query($sql_faltas) or die("ERRO NO CALCULO DE FALTAS");
        
        while ($faltas = mysql_fetch_assoc($qr_faltas)) {
            $dias_vr -= $faltas['qnt'];
            
            if($faltas['qnt'] > 0){
                $this->status_falta = true;
            }
        }
        
        if($id_clt == $id_clt_debug){
            echo "DEPOIS DAS FALTAS: {$dias_vr} <br>";
        }
        
        if($id_clt == $id_clt_debug){
            echo "<br>ANTES DAS FERIAS: {$dias_vr} <br>";
        }
        
        // FERIAS
        $sql_ferias = "SELECT *, DATE_FORMAT(data_fim, '%d-%m-%Y') AS data_fim, DATE_FORMAT(data_ini, '%d-%m-%Y') AS data_ini, DATE_FORMAT(ADDDATE(LAST_DAY(SUBDATE(data_fim, INTERVAL 1 MONTH)), 1), '%d-%m-%Y') as data_ini2, DATE_FORMAT(LAST_DAY(data_ini), '%d-%m-%Y') as ultimo_dia
            FROM rh_ferias
            WHERE id_clt = {$id_clt} AND '{$ano}-{$mes}' BETWEEN DATE_FORMAT(data_ini, '%Y-%m') AND DATE_FORMAT(data_fim, '%Y-%m') AND STATUS = 1
            ORDER BY id_ferias DESC";
        $qr_ferias = mysql_query($sql_ferias) or die("ERRO NO CALCULO DE FERIAS");  
        
        if(mysql_num_rows($qr_ferias) > 0){
            if($func36){
                $dias_vr = $dias_vr;
            }else{
                $dias_vr = $this->diasUteis($mes, $ano, false);
            }
        }
        
        while($rs_ferias = mysql_fetch_assoc($qr_ferias)){
            if($rs_ferias['mes'] == $mes){
                $dtI = $rs_ferias['data_ini'];
                $dtF = $rs_ferias['ultimo_dia'];
                $dtI_ = date("Y-m-d", strtotime($dtI));
                $dtF_ = date("Y-m-d", strtotime($dtF));
            }else{
                $dtI = $rs_ferias['data_ini2'];
                $dtF = $rs_ferias['data_fim'];
                $dtI_ = date("Y-m-d", strtotime($dtI));
                $dtF_ = date("Y-m-d", strtotime($dtF));
            }
            
            $feriado_ferias = $this->getFeriados($mes, $ano, false, true, $dtI_, $dtF_);
            
            if($_COOKIE['logado'] == 353){
                if($id_clt == $id_clt_debug){
                    echo "<br><br>Inicio Férias: {$dtI_}<br>";
                    echo "Final Férias: {$dtF_}";
                    
                    echo "<br><br>dias_vt_antes_ferias:".$dias_vr;
                    echo "<br>feriados nas férias:".$feriado_ferias;
                }
            }
            
            $dias_ferias = $this->diasUteisData($dtI, $dtF);
            
            if($_COOKIE['logado'] == 353){
                if($id_clt == $id_clt_debug){
                    echo "<br>dias_ferias: $dias_ferias";
                }
            }
                       
            $totFeriados = $this->getFeriados($mes, $ano, false, true);
            
            $dias_vr += $feriado_ferias;           
            $dias_vr -= $totFeriados;           
            $dias_vr -= $dias_ferias;
            
            if($dias_ferias > 0){
                $this->status_ferias = true;
            }                        
        }
        
        if($id_clt == $id_clt_debug){
            echo "DEPOIS DAS FERIAS: {$dias_vr} <br>";
        }
        
        if($_COOKIE['logado'] == 353){
            if($id_clt == $id_clt_debug){
                echo "<br><br>clt: {$id_clt} dias ferias: {$dias_ferias}<br>";
            }
        }
        
        if($id_clt == $id_clt_debug){
            echo "<br>ANTES DO EVENTO: {$dias_vr} <br>";
        }
        
        // EVENTOS
//        $sql_eventos = "
//            SELECT A.status,
//                IF(DATE_FORMAT(A.data, '%Y-%m') = '{$ano}-{$mes}',
//                    DATE_FORMAT(A.data, '%d-%m-%Y'),
//                    DATE_FORMAT(ADDDATE(LAST_DAY(SUBDATE(A.data_retorno, INTERVAL 1 MONTH)), 1), '%d-%m-%Y')
//                ) AS dataI_evento,
//                IF(DATE_FORMAT(A.data_retorno, '%Y-%m') = '{$ano}-{$mes}',
//                    DATE_FORMAT(A.data_retorno, '%d-%m-%Y'),
//                    DATE_FORMAT(LAST_DAY(A.data), '%d-%m-%Y')
//                ) AS dataF_evento
//            FROM rh_eventos AS A
//            WHERE (DATE_FORMAT(A.data, '%Y-%m') = '{$ano}-{$mes}' OR DATE_FORMAT(A.data_retorno, '%Y-%m') = '{$ano}-{$mes}') AND A.id_clt = {$id_clt} AND A.status = 1";
        
        $sql_eventos = "
            SELECT A.id_clt, A.cod_status, A.`data`, A.data_retorno,                
                IF(DATE_FORMAT(A.data_retorno, '%Y-%m') = '{$ano}-{$mes}', DATE_FORMAT('{$ano}-{$mes}-01', '%d-%m-%Y'), DATE_FORMAT(A.`data`, '%d-%m-%Y')) AS data_ini,
                DATE_FORMAT(A.data_retorno, '%d-%m-%Y') AS data_fim, 
                DATE_FORMAT(LAST_DAY('{$ano}-{$mes}-01'), '%d-%m-%Y') as ultimo_dia
            FROM rh_eventos AS A
            INNER JOIN(
                SELECT MAX(data) AS data, id_clt FROM rh_eventos WHERE status = 1 AND id_clt = {$id_clt} AND cod_status != 10 GROUP BY id_clt
            ) AS B ON(A.id_clt = B.id_clt AND A.data = B.data)
            WHERE A.status = 1 AND A.id_clt = {$id_clt} AND A.cod_status != 10 
            AND (('{$ano}-{$mes}' BETWEEN DATE_FORMAT(A.data, '%Y-%m') AND DATE_FORMAT(A.data_retorno, '%Y-%m')) OR (A.data_retorno = '0000-00-00'))
            AND A.`data` <= LAST_DAY('{$ano}-{$mes}-01')";
        
        $qr_eventos = mysql_query($sql_eventos) or die("ERRO NO CALCULO DE EVENTO");
                
        if($id_clt == $id_clt_debug){
            echo "<br>".$sql_eventos."<br><br>";
        }
        
        while($rs_eventos = mysql_fetch_assoc($qr_eventos)){
//            $dtI_evento = $rs_eventos['dataI_evento'];
//            $dtF_evento = $rs_eventos['dataF_evento'];
            
            if($rs_eventos['data_retorno'] == '0000-00-00'){
                $dtI = "01-{$mes}-{$ano}";
                $dtF = $rs_eventos['ultimo_dia'];
            }else{
                $dtI = $rs_eventos['data_ini'];
                $dtF = $rs_eventos['data_fim'];
            }
            
            $dias_evento = $this->diasUteisData($dtI, $dtF);
            
            $dias_vr -= $dias_evento;
            
            if($dias_evento > 0){
                $this->status_evento = true;
            }
        }
        
        if($id_clt == $id_clt_debug){
            echo "DEPOIS DO EVENTO: {$dias_vr} <br>";
        }
        
        $valor_final = $valor_dia * $dias_vr;
        
        // PESSOAS EM EVENTO NO MÊS TODO
//        if((mysql_num_rows($qr_eventos) == 0) && ($status_clt != 10) && ($status_clt != 40)){
//            if($_COOKIE['logado'] == 353){
//                if($id_clt == $id_clt_debug){
//                    echo "<br><br><strong>VR por dia</strong> >> CLT: {$id_clt} | STATUS: {$status_clt}<br><br>";
//                }
//            }
//            
//            $valor_final = 0;
//        }
        
        if($valor_final < 0){
            $valor_final = 0;
        }
        
        if($valor_final == 0){
            //$feriasStatus = false;
            //$faltasStatus = false;
        }
        
        return $valor_final;
    }

}
