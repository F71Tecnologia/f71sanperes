<?php

/**
 * Description of EstabilidadeClass
 *
 * pode servir como modelo para outros
 * 
 * @author Leonardo
 */
class Cnae {

    public $debug = FALSE;
    protected $tabela = 'cnae';
    protected $id_tabela = 'id_cnae';

    public function inserir($dados = array()) {
        $return = array('status' => FALSE, 'msg' => utf8_encode('Erro ao inserir'));
        if (empty($dados) || !is_array($dados)) {
            return $return;
        }

        array_filter($dados); //limpa campos vazios

        $cols = implode(',', array_keys($dados)) . ",data_cad";
        $rows = "'" . implode("','", array_values($dados)) . "',NOW()";

        $query = "INSERT INTO {$this->tabela} ($cols) VALUES ($rows)";

        if (!$this->showDebug($query)) {
            $result = mysql_query($query) or die("Erro ao inserir<br> Query: $query<br>" . mysql_error());
            $return = ($result) ? array('status' => TRUE, 'msg' => utf8_encode('Informação salva!')) : $return;
        }
        return $return;
    }

    public function editar($id_empresa, $dados = array()) {
        $return = array('status' => FALSE, 'msg' => utf8_encode('Erro ao editar'));
        if (empty($dados) || !is_array($dados)) {
            return $return;
        }
//        array_filter($dados); //limpa campos vazios
        $updates = array();
        foreach ($dados as $key => $value) {
            $updates[] = "$key = '$value'";
        }
        $up = implode(',', $updates);
        $query = "UPDATE {$this->tabela} SET $up WHERE {$this->id_tabela} = $id_empresa";

        if (!$this->showDebug($query)) {
            $result = mysql_query($query) or die("Erro ao editar<br> Query: $query<br>" . mysql_error());
            $return = ($result) ? array('status' => TRUE, 'msg' => utf8_encode('Informação alterada!')) : $return;
        } 
        return $return;
    }

    public function salvar($dados = array()) {
        $return = array('status' => FALSE, 'msg' => utf8_encode('Erro ao editar'));
        if (empty($dados) || !is_array($dados)) {
            return $return;
        }
        if (array_key_exists('id_empresa', $dados)) {
            $id_empresa = $dados['id_empresa'];
            unset($dados['id_empresa']);
            $return = $this->editar($id_empresa, $dados);
        } else {
            $return = $this->inserir($dados);
        } 
        return $return;
    }

    public function consultar($dados, $order_by = '', $limit = '') {
        $where = $this->prepara_where($dados);

        $query = "SELECT *, DATE_FORMAT(data_cad,'%d/%m/%Y %T') AS data_cad_br FROM {$this->tabela} AS a $where $order_by $limit";
        if (!$this->showDebug($query)) {
            $result = mysql_query($query) or die("Erro ao consultar<br> Query: $query<br>" . mysql_error());
            while ($row = mysql_fetch_assoc($result)) {
                $valores[$row[$this->id_tabela]] = $row;
            }
            $return = ($result) ? $valores : FALSE;
        }
        return $return;
    }

    protected function prepara_where($dados){
        if (is_array($dados)) {
            $dados = array_filter($dados); //limpa campos vazios
            if (empty($dados['status'])) {
                $cond[] = "status = 1";
            }
            foreach ($dados as $key => $value) {
                $cond[] = "$key = '$value'";
            }
            return (!empty($cond)) ? "WHERE " . implode(' AND ', $cond) : '';
        } else {
            return $dados;
        }
    }

    public function excluir($id_empresa) {
        $dados['status'] = '0';
        $status = $this->editar($id_empresa, $dados);
        return ($status['status']) ? array('status' => TRUE, 'msg' => utf8_encode('Exclusão realizada.')) : array('status' => FALSE);
    }
    
    protected function showDebug($dados){
        echo ($this->debug)?$dados:'';
        return $this->debug;
    }
    
    public function geraSelect(){

        $query = "SELECT mais_usados,{$this->id_tabela},codigo,descricao FROM {$this->tabela} AS a WHERE status = 1";
        if (!$this->showDebug($query)) {
            $result = mysql_query($query) or die("Erro ao consultar<br> Query: $query<br>" . mysql_error());
            while ($row = mysql_fetch_assoc($result)) {
                $valores[$row['mais_usados']][$row[$this->id_tabela]] = $row['codigo']." - ".$row['descricao'];
            }
            $return = ($result) ? $valores : FALSE;
        }
        return $return;
    }
    

}
