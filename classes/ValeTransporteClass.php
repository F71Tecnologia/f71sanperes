<?php

class ValeTransporteClass{
    
    private $table = 'rh_vt_pedido';
    private $id_table = 'id_vt_pedido';
    public $status_falta = false;
    public $status_ferias = false;
    public $status_evento = false;
    public $diasvt = 0;
    
    public function consultar($condicoes) {
        return $this->sqlQuery($this->table, '*', $condicoes, $this->id_table);
    }
    
    public function listar($id_pedido = null){
        if(!is_null($id_pedido)){
            $and_pedido = "AND a.id_vt_pedido = {$id_pedido}";
        }
        
        $sql = "SELECT *, b.regiao AS nome_regiao, c.nome AS nome_func, DATE_FORMAT(data,'%d/%m/%Y %H:%i:%s') AS data_proc, a.id_regiao
            FROM $this->table AS a
            INNER JOIN regioes AS b ON a.id_regiao = b.id_regiao
            INNER JOIN funcionario AS c ON a.user = c.id_funcionario
            WHERE a.status = 1 {$and_pedido}
            ORDER BY a.data DESC";
        
        if(!is_null($id_pedido)){
            $qry = mysql_query($sql) or die(mysql_error());
            $res = mysql_fetch_assoc($qry);

            return $res;
        }else{
            return $this->sqlExecQuery($sql);
        }
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
        
        if($regiao == 1){
            $query = "SELECT a.id_clt, a.nome, a.matricula, a.data_nasci, a.data_entrada, a.cpf, a.id_curso, b.nome AS nome_funcao, b.valor AS salario, a.id_unidade, a.status, a.transporte,
                c.unidade AS nome_unidade, e.horas_semanais, e.dias_semana, e.segunda_a_sabado, a.vale_refeicao, a.valor_refeicao, f.especifica AS nome_status
                FROM rh_clt AS a
                INNER JOIN curso AS b ON a.id_curso = b.id_curso
                INNER JOIN unidade AS c ON a.id_unidade = c.id_unidade
                INNER JOIN rhsindicato AS d ON a.rh_sindicato = d.id_sindicato
                INNER JOIN rh_horarios AS e ON a.rh_horario = e.id_horario
                INNER JOIN rhstatus AS f ON a.status = f.codigo
                WHERE (a.status < 60 OR a.status = 70) {$and_vt} {$and_clt} {$and_regiao}
                    
                UNION

                SELECT G.id_estagiario AS id_clt, G.nome, G.matricula, G.data_nasci, G.data_entrada, G.cpf, G.id_curso, 'Estagiário' AS nome_funcao, 
                G.salario AS salario, G.id_unidade, G.`status`, G.transporte, I.unidade AS nome_unidade, '' AS horas_semanais, '' AS dias_semana,
                '' AS segunda_a_sabado, '' AS vale_refeicao, '' AS valor_refeicao, 'Estagiário' AS nome_status
                FROM estagiario AS G
                INNER JOIN unidade AS I ON G.id_unidade = I.id_unidade
                WHERE G.status = 1 AND G.id_regiao = 1
                
                ORDER BY nome";
        }else{
        
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
        }
                
        if($_COOKIE['logado'] == 353){
//            echo $query;
        }
                
        $result = mysql_query($query) or die("ERRO getListaClts");
        
        if(!is_null($id_clt)){
            $return = mysql_fetch_assoc($result);
            
            return $return;
        }else{
            return $result;
        }
    }
    
    public function getValorDia($id_clt) {
        $query = "SELECT 
            IF(A.id_linha1 > 0, (G.valor_tarifa * A.qtd1), (B.valor * A.qtd1)) AS valor1,
            IF(A.id_linha2 > 0, (H.valor_tarifa * A.qtd2), (C.valor * A.qtd2)) AS valor2,
            IF(A.id_linha3 > 0, (I.valor_tarifa * A.qtd3), (D.valor * A.qtd3)) AS valor3,
            IF(A.id_linha4 > 0, (J.valor_tarifa * A.qtd4), (E.valor * A.qtd4)) AS valor4,
            IF(A.id_linha5 > 0, (K.valor_tarifa * A.qtd5), (F.valor * A.qtd5)) AS valor5
            FROM rh_vt_valores_assoc AS A
            LEFT JOIN rh_vt_valores AS B ON(A.id_valor1 = B.id_valor)
            LEFT JOIN rh_vt_valores AS C ON(A.id_valor2 = C.id_valor)
            LEFT JOIN rh_vt_valores AS D ON(A.id_valor3 = D.id_valor)
            LEFT JOIN rh_vt_valores AS E ON(A.id_valor4 = E.id_valor)
            LEFT JOIN rh_vt_valores AS F ON(A.id_valor5 = F.id_valor)
            LEFT JOIN rh_vt_linha AS G ON(A.id_linha1 = G.id_vt_linha)
            LEFT JOIN rh_vt_linha AS H ON(A.id_linha2 = H.id_vt_linha)
            LEFT JOIN rh_vt_linha AS I ON(A.id_linha3 = I.id_vt_linha)
            LEFT JOIN rh_vt_linha AS J ON(A.id_linha4 = J.id_vt_linha)
            LEFT JOIN rh_vt_linha AS K ON(A.id_linha5 = K.id_vt_linha)
            WHERE A.id_clt = '{$id_clt}' AND A.status_reg = 1";
        
//        if($_COOKIE['logado'] == 158){
//            if($id_clt == 2465){
//                echo $query;
//            }
//        }
        
        $sql = mysql_query($query) or die("ERRO getValorDia");
        $result = mysql_fetch_assoc($sql);
        
        $valor = $result['valor1'] + $result['valor2'] + $result['valor3'] + $result['valor4'] + $result['valor5'];
        
        return $valor;
    }
    
    public function getValorDiaEstagiario($id_estagiario) {
        $query = "SELECT A.valor_vt
            FROM estagiario AS A
            WHERE A.id_estagiario = {$id_estagiario}";
              
        $sql = mysql_query($query) or die("ERRO getValorDiaEstagiario");
        $result = mysql_fetch_assoc($sql);
        
        $valor = $result['valor_vt'];
        
        return $valor;
    }
    
    public function getInfoVt($id_clt) {
        $query = "
                SELECT 
                    IF(A.id_linha1 > 0, (G.valor_tarifa * A.qtd1), (B.valor * A.qtd1)) AS valor1, 
                    IF(A.id_linha2 > 0, (H.valor_tarifa * A.qtd2), (C.valor * A.qtd2)) AS valor2, 
                    IF(A.id_linha3 > 0, (I.valor_tarifa * A.qtd3), (D.valor * A.qtd3)) AS valor3, 
                    IF(A.id_linha4 > 0, (J.valor_tarifa * A.qtd4), (E.valor * A.qtd4)) AS valor4, 
                    IF(A.id_linha5 > 0, (K.valor_tarifa * A.qtd5), (F.valor * A.qtd5)) AS valor5,
                    IF(A.id_linha1 > 0, G.nome, '') AS linha1,
                    IF(A.id_linha2 > 0, H.nome, '') AS linha2,
                    IF(A.id_linha3 > 0, I.nome, '') AS linha3,
                    IF(A.id_linha4 > 0, J.nome, '') AS linha4,
                    IF(A.id_linha5 > 0, K.nome, '') AS linha5
                FROM rh_vt_valores_assoc AS A
                LEFT JOIN rh_vt_valores AS B ON(A.id_valor1 = B.id_valor)
                LEFT JOIN rh_vt_valores AS C ON(A.id_valor2 = C.id_valor)
                LEFT JOIN rh_vt_valores AS D ON(A.id_valor3 = D.id_valor)
                LEFT JOIN rh_vt_valores AS E ON(A.id_valor4 = E.id_valor)
                LEFT JOIN rh_vt_valores AS F ON(A.id_valor5 = F.id_valor)
                LEFT JOIN rh_vt_linha AS G ON(A.id_linha1 = G.id_vt_linha)
                LEFT JOIN rh_vt_linha AS H ON(A.id_linha2 = H.id_vt_linha)
                LEFT JOIN rh_vt_linha AS I ON(A.id_linha3 = I.id_vt_linha)
                LEFT JOIN rh_vt_linha AS J ON(A.id_linha4 = J.id_vt_linha)
                LEFT JOIN rh_vt_linha AS K ON(A.id_linha5 = K.id_vt_linha)
                WHERE A.id_clt = '{$id_clt}' AND A.status_reg = 1";
        
//        if($_COOKIE['logado'] == 353){
//            if($id_clt == 3216){
//                echo $query;
//            }
//        }
        
        $sql = mysql_query($query) or die("ERRO getInfoVt");
        $result = mysql_fetch_assoc($sql);
        
        return $result;
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
    
    public function getEmpregador($id_regiao) {
        $sql = "SELECT *
            FROM rhempresa AS A
            WHERE A.id_regiao = {$id_regiao}";
        $qry = mysql_query($sql) or die('ERRO getEmpregador');
        $res = mysql_fetch_assoc($qry);
        
        return $res;
    }
    
    public function getUnidade($id_pedido) {
        $sql = "SELECT A.id_vt_pedido AS id_pedido, C.codigo_sodexo, C.unidade AS nome_unidade, C.id_unidade
            FROM rh_vt_relatorio AS A
            LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
            LEFT JOIN unidade AS C ON(B.id_unidade = C.id_unidade)
            WHERE A.id_vt_pedido = {$id_pedido} AND C.unidade != ''
            GROUP BY C.unidade
            ORDER BY C.unidade";
        $qry = mysql_query($sql) or die('ERRO getUnidade');
        
        return $qry;
    }
    
    public function getParticipantes($id_pedido, $id_unidade = null) {
        if(!is_null($id_unidade)){
            $and_unidade = "AND C.id_unidade = {$id_unidade}";
        }
        
        $queryS = "SELECT A.*, A.vt_valor_diario AS valor, B.nome AS nome_empregado, B.data_nasci, B.cpf, B.sexo, B.matricula_sodexo, B.rg, B.uf_rg, B.data_rg, B.orgao, B.tipo_endereco, B.endereco, B.numero, B.complemento, B.bairro, B.cidade, B.uf, B.cep, B.mae, B.civil, B.email, B.tel_cel, C.codigo_sodexo, C.unidade AS nome_unidade, D.nome AS cargo
            FROM rh_vt_relatorio AS A
            LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
            LEFT JOIN unidade AS C ON(B.id_unidade = C.id_unidade)
            LEFT JOIN curso AS D ON(B.id_curso = D.id_curso)
            WHERE A.id_vt_pedido = {$id_pedido} {$and_unidade}";
        $resultS = mysql_query($queryS) or die(mysql_error());
        
        return $resultS;
    }
    
    public function getPedidoNoMes($mes, $ano, $regiao) {
        $query = "SELECT *
            FROM rh_vt_pedido AS A
            WHERE A.mes = '{$mes}' AND A.ano = '{$ano}' AND A.status = 1 AND A.id_regiao = {$regiao}";
        $result = mysql_query($query) or die(mysql_error());
        
        return $result;
    }
    
    public function calculaVTEstagiario($id_estagiario, $ano, $mes, $debug) {
        $valor_dia = $this->getValorDiaEstagiario($id_estagiario);
        $this->diasvt = $this->diasUteis($mes, $ano);
        
        $valor_final = $valor_dia * $this->diasvt;
        
        return $valor_final;
    }
    
    public function calculaVT($id_clt, $ano, $mes, $debug = false) {
        $id_clt_debug = "";
        
        $dados_clt = $this->getListaClts(null, $id_clt);
        
        $horas_semanais = $dados_clt['horas_semanais'];
        $nome_funcao = $dados_clt['nome_funcao'];
        $dias_semana = $dados_clt['dias_semana'];
        $status_clt = $dados_clt['status'];
        
        $valor_dia = $this->getValorDia($id_clt);
        $this->diasvt = $this->diasUteis($mes, $ano);
        
        //CONDIÇÃO PARA CLTS COM HORARIO SEGUNDA A SÁBADO
        if($dados_clt['segunda_a_sabado']){
            $sabados = $this->getSabadosNoMes($mes, $ano, false, true);
            
            $this->diasvt += $sabados;
        }
        
        if($_COOKIE['logado'] == 353){
            if($id_clt == $id_clt_debug){
                echo "INICIAL: clt: {$id_clt} | valor_dia: {$valor_dia} | dias_vt: {$this->diasvt}<br><br>";
            }
        }
        
        $func36 = strripos($nome_funcao, "12x36");
        
        //CONDIÇÃO PARA HORARIO 12X36
        if($func36){
            $dias_no_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
            
            $this->diasvt = round($dias_no_mes / 2);
        }
        
        if($_COOKIE['logado'] == 353){
            if($id_clt == $id_clt_debug){
                print_array($dados_clt);
            }                        
        }
        
//        if($horas_semanais == 36){
//            $dias_no_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
//            
//            $this->diasvt = round($dias_no_mes / 2);
//        }
        
        //CONDIÇÃO PARA CARGA HORÁRIO <= 20
//        if($horas_semanais <= 20){
//            $this->diasvt = $dias_semana * 4;
//            
//            if($dados_clt['transporte'] == 1){
//                echo $dados_clt['nome']."<br>";
//            }
//        }                
        
        $mes = sprintf('%02d', $mes);
        
        $this->status_falta = false;
        $this->status_ferias = false;
        $this->status_evento = false;
        
        // FALTAS
        $sql_faltas = "SELECT A.qnt
            FROM rh_movimentos_clt AS A
            WHERE A.id_clt = {$id_clt} AND A.status IN(5, 1) AND A.id_mov IN(62,232) AND A.mes_mov = MONTH(DATE_SUB('{$ano}-{$mes}-01', INTERVAL 1 MONTH)) AND ano_mov = '{$ano}'";
        $qr_faltas = mysql_query($sql_faltas) or die("ERRO NO CALCULO DE FALTAS");
        
        while ($faltas = mysql_fetch_assoc($qr_faltas)) {
            $this->diasvt -= $faltas['qnt'];
            
            if($faltas['qnt'] > 0){
                $this->status_falta = true;
            }
        }
        
        // FERIAS
        $sql_ferias = "SELECT *, DATE_FORMAT(data_fim, '%d-%m-%Y') AS data_fim, DATE_FORMAT(data_ini, '%d-%m-%Y') AS data_ini, DATE_FORMAT(ADDDATE(LAST_DAY(SUBDATE(data_fim, INTERVAL 1 MONTH)), 1), '%d-%m-%Y') as data_ini2, DATE_FORMAT(LAST_DAY(data_ini), '%d-%m-%Y') as ultimo_dia
            FROM rh_ferias
            WHERE id_clt = {$id_clt} AND '{$ano}-{$mes}' BETWEEN DATE_FORMAT(data_ini, '%Y-%m') AND DATE_FORMAT(data_fim, '%Y-%m') AND STATUS = 1
            ORDER BY id_ferias DESC";
        $qr_ferias = mysql_query($sql_ferias) or die("ERRO NO CALCULO DE FERIAS");  
        
        if(mysql_num_rows($qr_ferias) > 0){
            $this->diasvt = $this->diasUteis($mes, $ano, false);
        }
        
        if($_COOKIE['logado'] == 353){
            if($id_clt == $id_clt_debug){
                echo "@total_feriados: " . $tot_feriados;
                echo "<br>@dias_vt: " . $this->diasvt;
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
                    
                    echo "<br><br>dias_vt_antes_ferias:".$this->diasvt;
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
            
            $this->diasvt += $feriado_ferias;           
            $this->diasvt -= $totFeriados;           
            $this->diasvt -= $dias_ferias;
            
            if($dias_ferias > 0){
                $this->status_ferias = true;
            }                        
        }
        
//        if($debug){
            if($_COOKIE['logado'] == 353){
                if($id_clt == $id_clt_debug){
                    echo "<br><br>clt: {$id_clt} dias ferias: {$dias_ferias}<br>";
                }
            }
//        }
        
        // EVENTOS
        $sql_eventos = "
            SELECT A.status,
                IF(DATE_FORMAT(A.data, '%Y-%m') = '{$ano}-{$mes}',
                    DATE_FORMAT(A.data, '%d-%m-%Y'),
                    DATE_FORMAT(ADDDATE(LAST_DAY(SUBDATE(A.data_retorno, INTERVAL 1 MONTH)), 1), '%d-%m-%Y')
                ) AS dataI_evento,
                IF(DATE_FORMAT(A.data_retorno, '%Y-%m') = '{$ano}-{$mes}',
                    DATE_FORMAT(A.data_retorno, '%d-%m-%Y'),
                    DATE_FORMAT(LAST_DAY(A.data), '%d-%m-%Y')
                ) AS dataF_evento
            FROM rh_eventos AS A
            WHERE (DATE_FORMAT(A.data, '%Y-%m') = '{$ano}-{$mes}' OR DATE_FORMAT(A.data_retorno, '%Y-%m') = '{$ano}-{$mes}') AND A.id_clt = {$id_clt} AND A.status = 1";
        $qr_eventos = mysql_query($sql_eventos) or die("ERRO NO CALCULO DE EVENTO");
        
        while($rs_eventos = mysql_fetch_assoc($qr_eventos)){
            $dtI_evento = $rs_eventos['dataI_evento'];
            $dtF_evento = $rs_eventos['dataF_evento'];
            
            $dias_evento = $this->diasUteisData($dtI_evento, $dtF_evento);
            
            $this->diasvt -= $dias_evento;
            
            if($dias_evento > 0){
                $this->status_evento = true;
            }
        }
        
//        if($debug){
            if($_COOKIE['logado'] == 353){
                if($id_clt == $id_clt_debug){
                    echo "clt: {$id_clt} dias vt: {$this->diasvt}<br>";
                }
            }
//        }
        
        $valor_final = $valor_dia * $this->diasvt;
        
        // PESSOAS EM EVENTO NO MÊS TODO
        if((mysql_num_rows($qr_eventos) == 0) && ($status_clt != 10)){
            $valor_final = 0;
        }
        
        if($valor_final < 0){
            $valor_final = 0;
        }
        
        if($valor_final == 0){
            //$feriasStatus = false;
            //$faltasStatus = false;
        }
        
        return $valor_final;
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
    
    public function gerForRelatorio($id, $gerar = null) {
        
        if($gerar){
            $q = "SELECT a.id_vt_relatorio, a.id_clt, a.vt_valor_diario, a.dias_uteis, b.nome, b.`status`, b.cpf, b.data_entrada, b.matricula_sodexo, d.unidade nome_unidade, c.nome nome_curso, e.nome nome_sindicato, f.mes, f.ano
                FROM rh_vt_relatorio a
                LEFT JOIN rh_clt b ON a.id_clt = b.id_clt
                LEFT JOIN curso c ON b.id_curso = c.id_curso
                LEFT JOIN unidade d ON b.id_unidade = d.id_unidade
                LEFT JOIN rhsindicato e ON c.id_sindicato = e.id_sindicato
                LEFT JOIN rh_vt_pedido f ON a.id_vt_pedido = f.id_vt_pedido
                WHERE a.id_vt_pedido = {$id} AND a.estagiario IS NULL";
        }else{
            $q = "SELECT a.id_vt_relatorio, a.id_clt, a.vt_valor_diario, a.dias_uteis, b.nome, b.`status`, b.cpf, b.data_entrada, b.matricula_sodexo, d.unidade nome_unidade, c.nome nome_curso, e.nome nome_sindicato, f.mes, f.ano
                FROM rh_vt_relatorio a
                LEFT JOIN rh_clt b ON a.id_clt = b.id_clt
                LEFT JOIN curso c ON b.id_curso = c.id_curso
                LEFT JOIN unidade d ON b.id_unidade = d.id_unidade
                LEFT JOIN rhsindicato e ON c.id_sindicato = e.id_sindicato
                LEFT JOIN rh_vt_pedido f ON a.id_vt_pedido = f.id_vt_pedido
                WHERE a.id_vt_pedido = {$id} AND a.estagiario IS NULL

                UNION

                SELECT G.id_vt_relatorio, G.id_clt, G.vt_valor_diario, G.dias_uteis, H.nome, H.status, H.cpf, H.data_entrada, '' AS matricula_sodexo, 
                I.unidade AS nome_unidade, 'Estagiário' AS nome_curso, '' AS nome_sindicato, J.mes, J.ano
                FROM rh_vt_relatorio AS G
                LEFT JOIN estagiario AS H ON (G.id_clt = H.id_estagiario)
                LEFT JOIN unidade I ON H.id_unidade = I.id_unidade
                LEFT JOIN rh_vt_pedido J ON G.id_vt_pedido = J.id_vt_pedido
                WHERE G.id_vt_pedido = {$id} AND G.estagiario = 1";
        }        
        return $this->sqlExecQuery($q);
    }
    
    public function getMovimentosVtEmAberto($id_pedido) {
        $q = "SELECT COUNT(id_movimento) AS tot_mov
            FROM rh_movimentos_clt AS A
            WHERE A.status = 1 AND A.id_pedido = {$id_pedido}";
        return $this->sqlExecQuery($q);
    }
    
    public function getMovimentosVtPagos($id_pedido) {
        $q = "SELECT COUNT(id_movimento) AS tot_mov
            FROM rh_movimentos_clt AS A
            WHERE A.status = 5 AND A.id_pedido = {$id_pedido}";
        return $this->sqlExecQuery($q);
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
    
    public function getTable() {
        return $this->table;
    }
    
    public function setTable($table) {
        $this->table = $table;
    }
    
    public function getIdTable() {
        return $this->id_table;
    }
    
    public function setIdTable($id_table) {
        $this->id_table = $id_table;
    }
    
}

?>