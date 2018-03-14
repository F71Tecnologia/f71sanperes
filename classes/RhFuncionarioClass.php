<?php
/*
 * PHO-DOC - RhFuncionarioClass.php
 * 
 * Classe de manipulação dos registros de clt
 * 
 * 25-02-2016
 *
 * @name RhFuncionarioClass 
 * @package RhFuncionarioClass
 * @access public 
 *  
 * @version 
 *
 * Versão: 3.0.5055 - 25/02/2016 - Jacques - Versão Inicial
 * 
 * @author jacques@f71.com.br
 * 
 * @copyright www.f71.com.br 
 *  
 */ 

class RhFuncionarioClass {
    
    public function select(){
        
        $this->db->setQuery(SELECT," 
                            id_funcionario,
                            id_master,
                            tipo_usuario,
                            grupo_usuario,
                            horario_inicio,
                            horario_fim,
                            acesso_dias,
                            nome,
                            salario,
                            id_regiao,
                            regiao,
                            funcao,
                            locacao,
                            endereco,
                            bairro,
                            cidade,
                            uf,
                            cep,
                            tel_fixo,
                            tel_cel,
                            tel_rec,
                            data_nasci,
                            naturalidade,
                            nacionalidade,
                            civil,
                            ctps,
                            serie_ctps,
                            uf_ctps,
                            pis,
                            rg,
                            orgao,
                            data_rg,
                            cpf,
                            titulo,
                            zona,
                            secao,
                            pai,
                            mae,
                            estuda,
                            data_escola,
                            escolaridade,
                            instituicao,
                            curso,
                            foto,
                            banco,
                            agencia,
                            conta,
                            login,
                            senha,
                            alt_senha,
                            lisenca,
                            exclusao,
                            status_reg,
                            user_cad,
                            data_cad,
                            nome1,
                            botoes,
                            email_login,
                            email_senha,
                            master_origem
                            ");
        
        $this->db->setQuery(FROM," funcionario ");
            
        $id_funcionario = $this->getIdFuncionario();
        $id_master = $this->getIdMaster();

        $this->db->setQuery(WHERE, " status_reg ");

        if(!empty($id_funcionario)) {$this->db->setQuery(WHERE,"AND id_funcionario = {$id_funcionario}",ADD);}

        if(!empty($id_master)) {$this->db->setQuery(WHERE,"AND id_master = {$id_master}",ADD);}

        $this->db->setQuery(ORDER, " nome ASC ");
        
        if(empty($id_funcionario) && empty($id_master)) $this->error->set("Necessário a definição de alguma propriedade para execução do método select da classe RhFuncionarioClass",E_FRAMEWORK_ERROR);

        if(!$this->db->setRs()) $this->error->set("Houve um erro na query de consulta do método select da classe RhFuncionarioClass",E_FRAMEWORK_ERROR);

        return $this;
        
    }
   

}





