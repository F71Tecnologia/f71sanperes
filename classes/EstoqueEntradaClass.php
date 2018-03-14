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
class EstoqueEntrada {

    public $debug = FALSE;
    
    protected $estoque;
    
    public function __construct() {
        $this->estoque = new Estoque();
    }

    public function inserir($dados = array()) {
        $return = array('status' => FALSE, 'msg' => utf8_encode('Erro ao inserir'));
        if (empty($dados) || !is_array($dados)) {
            return $return;
        }

        array_filter($dados); //limpa campos vazios

        $cols = implode(',', array_keys($dados));
        $rows = "'" . implode("','", array_values($dados)) . "'";

        $query = "INSERT INTO estoque_produto_entrada ($cols, data_entrada) VALUES ($rows,NOW())";

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
        $query = "UPDATE estoque_produto_entrada SET $up WHERE id = $id";

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

    public function excluir($id) {
        $dados['status'] = '0';
        $status = $this->editar($id, $dados);
        return ($status['status']) ? array('status' => TRUE, 'msg' => utf8_encode('Exclusão realizada.')) : array('status' => FALSE);
    }
    
    

}
