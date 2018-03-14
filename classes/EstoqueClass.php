<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of estoqueClass
 *
 * @author Leonardo
 */
class Estoque {

    public $debug = FALSE;

    public function inserir($dados = array()) {
        $return = array('status' => FALSE, 'msg' => utf8_encode('Erro ao inserir'));
        if (empty($dados) || !is_array($dados)) {
            return $return;
        }

        array_filter($dados); //limpa campos vazios

        $cols = implode(',', array_keys($dados)) . ",data_proc";
        $rows = "'" . implode("','", array_values($dados)) . "',NOW()";

        $query = "INSERT INTO estoque_produto ($cols) VALUES ($rows)";

        if (!$this->debug) {
            $result = mysql_query($query) or die("Erro ao inserir<br> Query: $query<br>" . mysql_error());
            $return = ($result) ? array('status' => TRUE, 'msg' => utf8_encode('Informação salva!')) : $return;
        } else {
            echo $query;
        }
        return $return;
    }

    public function editar($id, $dados = array()) {
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
        $query = "UPDATE estoque_produto SET $up WHERE id = $id";

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
        if (array_key_exists('id', $dados)) {
            $id = $dados['id'];
            unset($dados['id']);
            $return = $this->editar($id, $dados);
        } else {
            $return = $this->inserir($dados);
        }
        return $return;
    }

//    public function excluir($id) {
//        $dados['status'] = '0';
//        $status = $this->editar($id, $dados);
//        return ($status['status']) ? array('status' => TRUE, 'msg' => utf8_encode('Exclusão realizada.')) : array('status' => FALSE);
//    }

    public function consultar($dados = array(), $order_by = '', $limit = '') {
        if (empty($dados) || !is_array($dados)) {
            return FALSE;
        }
        array_filter($dados); //limpa campos vazios

        foreach ($dados as $key => $value) {
            $cond[] = "$key = '$value'";
        }

//        array_filter($cond); //limpa campos vazios

        $where = (!empty($cond)) ? "WHERE " . implode(' AND ', $cond) : '';

        $query = "SELECT * FROM estoque_produto AS a $where $order_by $limit";

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

    public function saldoAtual($id) {
        $query = "SELECT * FROM estoque_produto WHERE id_prod = $id ORDER BY data_proc DESC, id DESC LIMIT 1";
        return mysql_fetch_assoc(mysql_query($query));
    }

    public function incrementa($dados = array()) {
        if (empty($dados) || !is_array($dados)) {
            return FALSE;
        }
        $saldo_atual = $this->saldoAtual($dados['id_prod']);
        $saldo_novo['saldo_qtd'] = $saldo_atual['saldo_qtd'] + $dados['qtd'];
        $saldo_novo['id_prod'] = $dados['id_prod'];
        return $this->inserir($saldo_novo);
    }

    public function decrementa($dados = array()) {
        if (empty($dados) || !is_array($dados)) {
            return FALSE;
        }
        $saldo_atual = $this->saldoAtual($dados['id_prod']);
        $saldo_novo['saldo_qtd'] = $saldo_atual['saldo_qtd'] - $dados['qtd'];
        $saldo_novo['id_prod'] = $dados['id_prod'];
        return $this->inserir($saldo_novo);
    }
    
    public function estoqueConsulta (){
        $query = "SELECT A. * , B.xProd
            FROM estoque_produto A
            INNER JOIN nfe_produtos B ON (B.id_prod = A.id_prod)
            ORDER BY A.id";
        
        $result = mysql_query($query) or die("Erro ao consultar<br> Query: $query<br>". mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            $return[$row['id_prod']] = $row;
        }
        return $return;
    }

}
