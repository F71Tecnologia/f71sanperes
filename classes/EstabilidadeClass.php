<?php

/**
 * Description of EstabilidadeClass
 *
 * pode servir como modelo para outros
 * 
 * @author Leonardo
 */
class Estabilidade {

    public $debug = FALSE;

    public function inserir($dados = array()) {
        $return = array('status' => FALSE, 'msg' => utf8_encode('Erro ao inserir'));
        if (empty($dados) || !is_array($dados)) {
            return $return;
        }

        array_filter($dados); //limpa campos vazios

        $cols = implode(',', array_keys($dados)) . ",data_proc";
        $rows = "'" . implode("','", array_values($dados)) . "',NOW()";

        $query = "INSERT INTO rh_estabilidade_provisoria ($cols) VALUES ($rows)";

        if (!$this->debug) {
            $result = mysql_query($query) or die("Erro ao inserir<br> Query: $query<br>" . mysql_error());
            $return = ($result) ? array('status' => TRUE, 'msg' => utf8_encode('Informação salva!')) : $return;
        } else {
            echo $query;
        }
        return $return;
    }

    public function editar($id_estabilidade, $dados = array()) {
        $return = array('status' => FALSE, 'msg' => utf8_encode('Erro ao editar'));
        if (empty($dados) || !is_array($dados)) {
            return $return;
        }
//        array_filter($dados); //limpa campos vazios
        $updates = array();
        foreach ($dados as $key => $value) {
            $updates[] ="$key = '$value'";
        }
        $up = implode(',', $updates);
        $query = "UPDATE rh_estabilidade_provisoria SET $up WHERE id_estabilidade = $id_estabilidade";

        if (!$this->debug) {
            $result = mysql_query($query) or die("Erro ao editar<br> Query: $query<br>" . mysql_error());
            $return = ($result) ? array('status' => TRUE, 'msg' => utf8_encode('Informação alterada!')) : $return;
        } else {
            echo $query;
        }
        return $return;
    }

    public function salvar($dados = array()) {
        $return = array('status' => FALSE, 'msg' => utf8_encode('Erro ao editar'));
        if (empty($dados) || !is_array($dados)) {
            return $return;
        }
        if (array_key_exists('id_estabilidade', $dados)) {
            $id_estabilidade = $dados['id_estabilidade'];
            unset($dados['id_estabilidade']);
            $return = $this->editar($id_estabilidade, $dados);
        } else {
            $return = $this->inserir($dados);
        }
        return $return;
    }

    public function consultar($dados = array(), $order_by = '', $limit = '') {
        if (empty($dados) || !is_array($dados)) {
            return FALSE;
        }
        array_filter($dados); //limpa campos vazios

        $cond[] = "status = 1";
        foreach ($dados as $key => $value) {
            $cond[] = "$key = '$value'";
        }

//        array_filter($cond); //limpa campos vazios

        $where = (!empty($cond)) ? "WHERE " . implode(' AND ', $cond) : '';

        $query = "SELECT *, "
                . "DATE_FORMAT(data_ini,'%d/%m/%Y') AS data_ini_br, "
                . "DATE_FORMAT(data_fim,'%d/%m/%Y') AS data_fim_br, "
                . "DATE_FORMAT(data_ref,'%d/%m/%Y') AS data_ref_br, "
                . "DATE_FORMAT(data_proc,'%d/%m/%Y %T') AS data_proc_br, "
                . "(SELECT nome FROM projeto WHERE id_projeto = a.id_projeto) AS nome_projeto, "
                . "(SELECT nome FROM funcionario WHERE id_funcionario = a.id_func) AS nome_usuario, "
                . "(SELECT descricao FROM rh_tipos_estabilidade_provisoria WHERE id_tipo = a.id_tipo) AS tipo "
                . "FROM rh_estabilidade_provisoria AS a $where $order_by $limit";

        if (!$this->debug) {
            $result = mysql_query($query) or die("Erro ao consultar<br> Query: $query<br>" . mysql_error());
            while ($row = mysql_fetch_assoc($result)) {
                $valores[$row['id_estabilidade']] = $row;
            }
            $return = ($result) ? $valores : FALSE;
        } else {
            echo $query;
        }
        return $return;
    }

    public function excluir($id_estabilidade) {
        $dados['status'] = '0';
        $status = $this->editar($id_estabilidade, $dados);
        return ($status['status']) ? array('status' => TRUE, 'msg' => utf8_encode('Exclusão realizada.')) : array('status' => FALSE);
    }

    public function testeCLT($id_clt) {
        $consulta = $this->consultar(array('id_clt' => $id_clt));
        $keys = array_keys($consulta);
        $consulta = $consulta[$keys[0]];
        if (is_array($consulta)) {
            $return = "Funcionário em Estabilidade Provisória";
            if (!empty($consulta['data_ini']) && !empty($consulta['data_fim'])) {
                $return .= " de {$consulta['data_ini_br']} até {$consulta['data_fim_br']}";
            } else if (!empty($consulta['data_ini']) && empty($consulta['data_fim'])) {
                $return .= " desde {$consulta['data_ini_br']}";
}
        }
        return (!empty($return))?$return . '.':'';
    }

    public function getTipos($dados = array()) {
        if (!isset($dados['status'])) {
            $dados['status'] = 1;
        }
        foreach ($dados as $key => $value) {
            $cond[] = "$key = '$value'";
        }
        $where = "WHERE " . implode(' AND ', $cond);
        $query = "SELECT *,data_ref AS bool_ref FROM rh_tipos_estabilidade_provisoria $where ORDER BY descricao";
        $result = mysql_query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $return[$row['id_tipo']] = $row;
        }
        return $return;
    }
    
    public function getDiasTipo($id_tipo){
        $tipos = $this->getTipos(array('id_tipo'=>$id_tipo));
        return $tipos[$id_tipo]['qtd_dias_retorno'];
    }
    

}
