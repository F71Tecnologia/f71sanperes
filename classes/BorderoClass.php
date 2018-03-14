<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BorderoClass
 *
 * @author Renato
 */
class BorderoClass {
    
    public $debug = false;
    public $retorno = [];
    
    public function __construct() {
        
    }
    
    public function getBoredero($condicao = []) {
        
        $condicao[] = "A.status = 1";
        $condicao = array_filter($condicao);
        $condicao = implode(' AND ', $condicao);
        
        $sql = "
        SELECT A.*, B.id_saida, C.nome, REPLACE(C.valor, ',', '.') valor
        FROM bordero A 
        LEFT JOIN bordero_saidas B ON (A.id = B.id_bordero AND B.status = 1)
        LEFT JOIN saida C ON (B.id_saida = C.id_saida)
        WHERE {$condicao};";
        if($debug) {
            print_array($sql);
        }
        $qry = mysql_query($sql);
        while ($row = mysql_fetch_assoc($qry)) {
            if(!array_key_exists($row['id'], $array)) {
                $array[$row['id']] = $row;
                unset($array[$row['id']]['id_saida'],$array[$row['id']]['nome'],$array[$row['id']]['valor']);
            }
            if($row['id_saida']){
                $array[$row['id']]['saidas'][$row['id_saida']]['id_saida'] = $row['id_saida'];
                $array[$row['id']]['saidas'][$row['id_saida']]['nome'] = $row['nome'];
                $array[$row['id']]['saidas'][$row['id_saida']]['valor'] = $row['valor'];
            }
            
            
//            `id` INT(11) NOT NULL AUTO_INCREMENT,
//	`data_criacao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
//	`id_funcionario` INT(11) NOT NULL,
//	`numero_cheque` VARCHAR(20) NOT NULL,
//	`pago` TINYINT(1) NOT NULL DEFAULT '0',
//	`data_compensar` DATE NULL DEFAULT NULL,
//	`campo_livre` VARCHAR(50) NOT NULL COMMENT 'campo livre',
//	`descricao` VARCHAR(200) NOT NULL,
//	`status`
        }
//        print_array($array);exit;
        return $array;
    }
    
    public function deletaBordero($id){ 
        $usuario = carregaUsuario();
        
        $sql1 = "UPDATE bordero SET user_del = '{$usuario['id_funcionario']}', data_del = NOW(), status = 0 WHERE  id = '{$id}';";
        if(!mysql_query($sql1)){
            $error[] = 'Erro ao excluir o borderô ('.mysql_error().').';
        } else {
            $sql2 = "UPDATE saida SET status = 1 WHERE id_saida IN (SELECT id_saida FROM bordero_saidas WHERE id_bordero = '{$id}') AND status = 2;";
            if(!mysql_query($sql2)){
                $error[] = 'Erro ao voltar as saidas ('.mysql_error().').';
            }
        }
        
        if(count($error) > 0){
            $msg = utf8_encode(implode(' ', $error));
            $this->retorno = ['status' => 0, 'color' => 'danger', 'msg' => "$msg"];
        } else {
            $this->retorno = ['status' => 1, 'color' => 'success', 'msg' => utf8_encode("Borderô excluído com sucesso!")];
        }
    }
    
    public function removerSaidaDoBordero($id_bordero, $id_saida){ 
        $usuario = carregaUsuario();
        
        $sql1 = "UPDATE bordero_saidas SET user_del = '{$usuario['id_funcionario']}', data_del = NOW(), status = 0 WHERE id_bordero = '{$id_bordero}' AND id_saida = '{$id_saida}';";
        if(!mysql_query($sql1)){
            $error[] = 'Erro ao remover do borderô ('.mysql_error().').';
        } else {
            $sql2 = "UPDATE saida SET status = 1 WHERE id_saida IN ({$id_saida}) AND status = 2;";
            if(!mysql_query($sql2)){
                $error[] = 'Erro ao voltar as saidas ('.mysql_error().').';
            }
        }
        
        if(count($error) > 0){
            $msg = utf8_encode(implode(' ', $error));
            $this->retorno = ['status' => 0, 'color' => 'danger', 'msg' => "$msg"];
        } else {
            $this->retorno = ['status' => 1, 'color' => 'success', 'msg' => utf8_encode("Saida removida com sucesso!")];
        }
    }
}
