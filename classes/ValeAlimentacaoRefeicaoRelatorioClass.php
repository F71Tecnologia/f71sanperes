<?php

/**
 * Classe simplificada do Vale refeicao e do Vale Alimentacao
 *
 * @author F71sistemasweb
 */
class ValeAlimentacaoRefeicaoRelatorioClass {

    private $table = 'rh_va_relatorio';
    private $id_table = 'id_va_relatorio';

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
    
    public function getValeVA ($idVale) {
        $sql = "SELECT *
                FROM rh_va_pedido
                WHERE id_va_pedido = '{$idVale}'";
        $query = mysql_query($sql);
        $result = mysql_fetch_object($query);
        
        return $result;
    }
            
    public function gerForRelatorio($id) {
        $q = "SELECT a.id_va_relatorio,a.id_clt,a.va_valor_mes, b.nome, b.`status`,b.cpf,b.data_entrada,b.matricula_sodexo,d.unidade nome_unidade,c.nome nome_curso,e.nome nome_sindicato
                                            FROM       rh_va_relatorio a
                                            LEFT JOIN rh_clt b ON a.id_clt = b.id_clt
                                            LEFT JOIN curso c ON b.id_curso = c.id_curso
                                            LEFT JOIN unidade d ON b.id_unidade = d.id_unidade
                                            LEFT JOIN rhsindicato e ON c.id_sindicato = e.id_sindicato
                                            WHERE a.id_va_pedido = $id";
        return $this->sqlExecQuery($q);
    }
    
    public function consultar($condicoes) {

        return $this->sqlExecQuery($this->table, '*', $condicoes, $this->id_table);
    }

    public function getById($id) {
        return $this->sqlQueryFirst($this->table, '*', array($this->id_table => $id));
    }
    
    public function getListaCltsVA($regiao, $id_tipo) {
        $query = "SELECT a.id_clt,a.nome,a.matricula,a.data_nasci,a.data_entrada,a.cpf,a.id_curso,b.nome AS nome_funcao, a.id_unidade, c.unidade AS nome_unidade, d.valor_alimentacao AS valor
                FROM rh_clt AS a
                INNER JOIN curso AS b ON a.id_curso = b.id_curso
                INNER JOIN unidade AS c ON a.id_unidade = c.id_unidade
                INNER JOIN rhsindicato AS d ON a.rh_sindicato = d.id_sindicato
                WHERE a.id_regiao = '{$regiao}' /*AND vale_alimentacao = '{$id_tipo}'*/ AND (a.status < 60 OR a.status = 70 OR a.status = 200)
                ORDER BY a.nome";
        $result = mysql_query($query);
        
        return $result;
    }
    
    public function getListaCltsVAInclusao($idVale, $notIn = 0) {
        
        $va = $this->getValeVA($idVale);
        $queryParticipantes = $this->gerForRelatorio($idVale);
        
        while ($rowParticipantes = mysql_fetch_object($queryParticipantes)) {
            $participantes[] = $rowParticipantes->id_clt;
        }
        
        $participantes = implode(',',$participantes);
        
        $query = "SELECT a.id_clt,a.nome,a.matricula,a.data_nasci,a.data_entrada,a.cpf,a.id_curso,b.nome AS nome_funcao, a.id_unidade, c.unidade AS nome_unidade, d.valor_alimentacao AS valor
                FROM rh_clt AS a
                INNER JOIN curso AS b ON a.id_curso = b.id_curso
                INNER JOIN unidade AS c ON a.id_unidade = c.id_unidade
                INNER JOIN rhsindicato AS d ON a.rh_sindicato = d.id_sindicato
                WHERE a.id_regiao = '$va->id_regiao' AND (a.status < 60 OR a.status = 70 OR a.status = 200) AND id_clt NOT IN ({$participantes})
                ORDER BY a.nome";
        $result = mysql_query($query);
        
        return $result;
    }
    
    public function getListaCltsVR($id_regiao, $id_tipo) {
        /*
        * CONDICAO PARA INSTITUCIONAL
        * PEGAR DIRETO DO SINDICATO
        * CONDIÇÃO 
        */
        if($id_regiao == 1){
            $val = "IF(d.valor_refeicao > 0, d.valor_refeicao, a.valor_refeicao)";            
        }else{
            //$val = "IF(b.nome LIKE '%medico%', b.valor_refeicao, IF(a.valor_refeicao > 0, a.valor_refeicao, d.valor_refeicao))";
            $val = "IF(a.valor_refeicao > 0, a.valor_refeicao, IF(b.valor_refeicao > 0, b.valor_refeicao, d.valor_refeicao))";            
        }                
        
        $query = "SELECT a.id_clt,a.nome,a.matricula,a.data_nasci,a.data_entrada,a.cpf,a.id_curso,b.nome AS nome_funcao, a.id_unidade, a.status,
                c.unidade AS nome_unidade, d.id_sindicato AS sindicato, e.horas_semanais, a.vale_refeicao, a.valor_refeicao, f.especifica AS nome_status,
                {$val} AS valor, IF(a.valor_vr_dia_util > 0, a.valor_vr_dia_util, IF(d.valor_vr_dia_util > 0, d.valor_vr_dia_util, 0)) AS val_dia, a.valor_vr_fixo
                FROM rh_clt AS a
                INNER JOIN curso AS b ON a.id_curso = b.id_curso
                INNER JOIN unidade AS c ON a.id_unidade = c.id_unidade
                INNER JOIN rhsindicato AS d ON a.rh_sindicato = d.id_sindicato
                INNER JOIN rh_horarios AS e ON a.rh_horario = e.id_horario
                INNER JOIN rhstatus AS f ON a.status = f.codigo
                WHERE a.id_regiao = '{$id_regiao}' /*AND a.vale_refeicao = '{$id_tipo}'*/ AND a.status < 60
                ORDER BY c.unidade, a.nome;";
        $result = mysql_query($query);
        
        if($_COOKIE['logado'] == 353){
//            echo $query;
        }
        
        return $result;
    }
    
    public function getSindicatos($regiao, $tipo_valor) {
        $queryS = "SELECT B.nome, B.id_sindicato
                FROM rh_clt AS A
                LEFT JOIN rhsindicato AS B ON(A.rh_sindicato = B.id_sindicato)
                WHERE A.id_regiao = '{$regiao}' AND (B.valor_{$tipo_valor} = 0 OR B.valor_$tipo_valor IS NULL) AND A.rh_sindicato > 0
                GROUP BY A.rh_sindicato";
        $resultS = mysql_query($queryS) or die(mysql_error());
        
        return $resultS;
    }
    
    public function getCltsSemFuncao($regiao) {
        $query = "SELECT A.nome, A.id_curso
            FROM rh_clt AS A
            WHERE (A.id_curso = '' OR A.id_curso IS NULL OR A.id_curso = 0 OR A.id_curso = '-1') AND (A.status < 60 OR A.status = 70 OR A.status = 200) AND A.id_regiao = '{$regiao}'";
        $result = mysql_query($query) or die(mysql_error());
                
        return $result;
    }
    
    public function getCltsSemUnidade($regiao) {
        $query = "SELECT A.nome, A.id_unidade
            FROM rh_clt AS A
            WHERE (A.id_unidade = '' OR A.id_unidade IS NULL OR A.id_unidade = 0 OR A.id_unidade = '-1') AND (A.status < 60 OR A.status = 70 OR A.status = 200) AND A.id_regiao = '{$regiao}'";
        $result = mysql_query($query) or die(mysql_error());
        
        return $result;
    }
    
    public function getCltsSemSindicato($regiao) {
        $query = "SELECT A.nome, A.rh_sindicato
            FROM rh_clt AS A
            WHERE (A.rh_sindicato = '' OR A.rh_sindicato IS NULL OR A.rh_sindicato = 0 OR A.rh_sindicato = '-1') AND (A.status < 60 OR A.status = 70 OR A.status = 200) AND A.id_regiao = '{$regiao}'";
        $result = mysql_query($query) or die(mysql_error());
                
        return $result;
    }
    
    public function getCltsSemHorario($regiao) {
        $query = "SELECT A.nome, A.rh_horario
            FROM rh_clt AS A
            WHERE (A.rh_horario = '' OR A.rh_horario IS NULL OR A.rh_horario = 0 OR A.rh_horario = '-1') AND (A.status < 60 OR A.status = 70 OR A.status = 200) AND A.id_regiao = '{$regiao}'";
        $result = mysql_query($query) or die(mysql_error());
                
        return $result;
    }
    
    public function getPedidoNoMes($mes, $ano, $projeto, $regiao, $categoria) {
        $query = "SELECT *
            FROM rh_va_pedido AS A
            WHERE A.mes = '{$mes}' AND A.ano = '{$ano}' AND A.status = 1 AND A.projeto = {$projeto} AND A.id_regiao = {$regiao} AND A.categoria_vale = {$categoria}";
        $result = mysql_query($query) or die(mysql_error());
        
        return $result;
    }
    
    public function getParticipantes($id_pedido, $id_unidade = null) {
        if(!is_null($id_unidade)){
            $and_unidade = "AND C.id_unidade = {$id_unidade}";
        }
        
        $queryS = "SELECT A.*, A.va_valor_mes AS valor, B.nome AS nome_empregado, B.data_nasci, B.cpf, B.sexo, C.codigo_sodexo, C.unidade AS nome_unidade, B.matricula_sodexo
            FROM rh_va_relatorio AS A
            LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
            LEFT JOIN unidade AS C ON(B.id_unidade = C.id_unidade)
            WHERE A.id_va_pedido = {$id_pedido} {$and_unidade}";
        $resultS = mysql_query($queryS) or die(mysql_error());
        
        return $resultS;
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

}
