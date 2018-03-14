<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Parceiros {

    public function edita($var) {
        
        $update_logo = (!empty($var['nome_logo'])) ? ", parceiro_logo = '$var[nome_logo]'" : "";
        
        
        $qr_insert = mysql_query("UPDATE parceiros SET 
						id_regiao 			= '$var[regiao]', 
						parceiro_nome 		= '$var[nome]'
						$update_logo,
						parceiro_endereco 	= '$var[endereco]', 
						parceiro_cnpj 		= '$var[cnpj]', 
						parceiro_ccm 		= '$var[ccm]', 
						parceiro_ie 		= '$var[ie]', 
						parceiro_im 		= '$var[im]', 
						parceiro_bairro 	= '$var[bairro]', 
						parceiro_cidade 	= '$var[cidade]', 
						parceiro_estado 	= '$var[estado]', 
						parceiro_telefone 	= '$var[telefone]', 
						parceiro_celular 	= '$var[celular]', 
						parceiro_email 		= '$var[email]', 
						parceiro_contato 	= '$var[contato]', 
						parceiro_cpf 		= '$var[cpf]', 
						parceiro_banco 		= '$var[banco]', 
						parceiro_agencia 	= '$var[agencia]', 
						parceiro_conta 		= '$var[conta]',
						parceiro_atualizacao = NOW(),
						parceiro_id_atualizacao = '$_COOKIE[logado]'
							
						WHERE parceiro_id = '$var[parceiro]' 
						LIMIT 1") or die(mysql_error());

        if ($qr_insert) {

            $nome_funcionario = mysql_result(mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'"), 0);
            registrar_log('ADMINISTRAวรO - EDIวรO DE PARCEIROS', $nome_funcionario . ' editou o parceiro: ' . '(' . $var['parceiro'] . ') - ' . $var['nome']);

            header("Location: index.php?sucesso=curso&m=$var[link_master]&curso=$id");
        }
    }
    
    public function getItem($parceiro){
        $qr_parceiro = mysql_query("SELECT * FROM parceiros WHERE parceiro_id = '$parceiro'");
        return mysql_fetch_assoc($qr_parceiro);
        
    }

}
