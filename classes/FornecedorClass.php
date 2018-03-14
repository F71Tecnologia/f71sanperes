<?php

/*** Description of EstabilidadeClass ** pode servir como modelo para outros ** @author Leonardo ***/
 
class Fornecedor {

    public $debug = FALSE;
    protected $tabela = 'cad_fornecedor';

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

    public function editar($id_fornecedor, $dados = array()) {
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
        $query = "UPDATE {$this->tabela} SET $up WHERE id_fornecedor = $id_fornecedor";

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
        if (array_key_exists('id_fornecedor', $dados)) {
            $id_fornecedor = $dados['id_fornecedor'];
            unset($dados['id_fornecedor']);
            $return = $this->editar($id_fornecedor, $dados);
        } else {
            $return = $this->inserir($dados);
        } 
        return $return; 
    }

    public function consultar($dados, $order_by = '', $limit = '', $completa = FALSE) {
        $where = $this->prepara_where($dados);
        $join = "";
        if($completa){
            $join = "LEFT JOIN (SELECT id_cnae,CONCAT(codigo,' - ',descricao) AS cnae_descricao FROM cnae) AS b ON (a.cnae = b.id_cnae) 
                        LEFT JOIN (select uf_sigla,uf_nome from uf) AS c ON (a.uf = c.uf_sigla) ";
            }

        $query = "SELECT *, DATE_FORMAT(data_cad,'%d/%m/%Y %T') AS data_cad_br FROM {$this->tabela} AS a $join $where $order_by $limit";

        if (!$this->showDebug($query)) {
            $result = mysql_query($query) or die("Erro ao consultar<br> Query: $query<br>" . mysql_error());
            while ($row = mysql_fetch_assoc($result)) {
                $valores[$row['id_fornecedor']] = $row;
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

    public function excluir($id_fornecedor) {
        $dados['status'] = '0';
        $status = $this->editar($id_fornecedor, $dados);
        return ($status['status']) ? array('status' => TRUE, 'msg' => utf8_encode('Exclusão realizada.')) : array('status' => FALSE);
    }
    
    protected function showDebug($dados){
        echo ($this->debug)?$dados:'';
        return $this->debug;
    }

}
