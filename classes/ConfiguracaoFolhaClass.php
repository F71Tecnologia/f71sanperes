<?php

class ConfiguracaoFolhaClass{
    
    private $projeto;
    private $regiao;
    private $master;


    public function getProjeto() {
        return $this->projeto;
    }

    public function setProjeto($projeto) {
        $this->projeto = $projeto;
    } 
    
    public function getRegiao() {
        return $this->regiao;
    }

    public function setRegiao($regiao) {
        $this->regiao = $regiao;
    } 
    
    public function getMaster(){
        return $this->master;
    }
    
    public function setMaster($master){
        $this->master = $master;
    }
        
    public function __construct($regiao) {
        
        $this->setRegiao($regiao);
        
    }

    public function getSequenciaInclude(){
        
        $dados = array();
        
        try{
            $qry = "SELECT 
                        A.id,
                        A.caminho,
                        A.sequencia,
                        A.cod_mov,
                        A.id_mov,
                        A.id_regiao, 
                        A.id_projeto, 
                        B.nome as nome_projeto,
                        A.status
                        FROM includes_projeto_assoc AS A
                                LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto )
                        WHERE A.`status` >= 0 AND A.id_regiao = '{$this->getRegiao()}'
                        ORDER BY A.id_regiao, A.id_projeto, A.sequencia ASC";
            $sql = mysql_query($qry);

            if(mysql_num_rows($sql) > 0){
                while($row = mysql_fetch_assoc($sql)){

                    $dados[$row['id_projeto']]['nome'] = $row['nome_projeto'];
                    $dados[$row['id_projeto']][$row['id']] = array(
                        "caminho" => $row['caminho'],
                        "sequencia" => $row['sequencia'],
                        "cod_mov" => $row['cod_mov'],
                        "id_mov" => $row['id_mov'],
                        "status" => $row['status'],
                        "id_regiao" => $row['id_regiao'],
                        "id_projeto" => $row['id_projeto'] 
                    );
                }
            }
        }catch(Exception $e){
            echo $e->getMessage(); 
        }   
        
        return $dados;
        
    }
    
    /**
     * 
     * @param type $idInclude
     * @param type $file
     * @return boolean
     */
    public function removeInclude($idInclude, $file, $idRegiao, $idProjeto){
        
        $retorno = false; 
        
        try{
            $qry_assoc = "DELETE FROM includes_projeto_assoc WHERE id = '{$idInclude}' AND id_regiao = '{$idRegiao}' AND id_projeto = '{$idProjeto}'";
            $sql_assoc = mysql_query($qry_assoc) or die('Erro ao remover associações');

            if($sql_assoc){
                $retorno = true; 
            }
        }catch(Exception $e){
            echo $e->getMessage(); 
        }
          
        return $retorno;
        
    }
    
    /**
     * 
     * @param type $idInclude
     * @param type $value
     */
    public function editInclude($idRegiao, $idProjeto, $idInclude, $campo, $value){
        $retorno = true; 
        
        try{
            $qry =  "UPDATE includes_projeto_assoc SET $campo = '{$value}' WHERE id_regiao = '{$idRegiao}' AND id_projeto = '{$idProjeto}' AND id = '{$idInclude}'";
            $sql = mysql_query($qry) or die('Erro ao editar include');

            if($sql){
                $retorno = true; 
            }
        }catch(Exception $e){
            echo $e->getMessage(); 
        }
        
        return $retorno;
        
    }    
    
    /**
     * 
     */
    public function listaArquivoDeInclude(){
        $array = array();
         
        $pasta = scandir('../../../classes/classIncludesFolha/');
        
        try{
            if (!empty($pasta)) {
                foreach ($pasta as $key => $arquivo) {  
                    if($arquivo != '.' && $arquivo != '..'){
                        $array[] = $arquivo;
                    }
                }
            } else {
                echo 'A pasta não existe.';
            }
        }catch(Exception $e){
            echo $e->getMessage(); 
        }
            
        return $array;
    }
    
    /**
     * 
     * @param type $master
     * @return type
     */
    public function getDadosMaster($master){
        $dados = array();
        
        try{
            $qry = "SELECT * FROM master WHERE id_master = '{$master}'";
            $sql = mysql_query($qry);

            if(mysql_num_rows($sql) > 0){
                while($row = mysql_fetch_assoc($sql)){
                    $dados[] = $row;
                }
            }
        }catch(Exception $e){
            echo $e->getMessage(); 
        }
        
        return $dados;
    }
    
    /**
     * 
     * @param type $master
     */
    public function dividirEncargos($valor){
        $retorno = false;
        $master  = $this->getMaster();
        
        try{
            
            $qry = "UPDATE master SET dividir_encargos_de_ferias = '{$valor}' WHERE id_master = '{$master}'";
            $sql = mysql_query($qry) or die("Erro ao atualizar informações de divisão de encargos");
            
            if($sql){
                $retorno = true;
            }
            
        }catch(Exception $e){
            echo $e->getMessage(); 
        }
        
        return $retorno;
        
    }
}
 