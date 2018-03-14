<?php

const DATA_CONTRATACAO = 0;
const DATA_CONTRATACAO_FMT = 1;
const DATA_CONTRATACAO_DIA = 2;
const DATA_CONTRATACAO_MES = 3;
const DATA_CONTRATACAO_ANO = 4;
const DATA_CONTRATACAO_STATUS = 5;

class RhMetodosClass {

    protected   $projeto = array( 'id_projeto' => 0,
                                    'nome' => ''
                                );
    
    protected $query_metodos = array(QUERY => '',
                             SELECT => '',
                             UPDATE => '',
                             INSERT => '',
                             WHERE => '',
                             GROUP => '',
                             HAVING => '',
                             ORDER => '',
                             LIMIT => '');
    protected   $rs;
    protected   $row;
    
    
    public function __construct() {
        
        try {
            
            
            
        } catch (Exception $ex) {
            
            print_array($ex);

        }


    }
    
    
    public function setProjetoId($valor){
        
        $this->projeto['id_projeto'] = $valor;
        
    }
    
    public function setProjetoNome($valor){
        
        $this->projeto['nome'] = $valor;
        
    }
    
    private function setMetodosQuery($valor,$index = QUERY,$add = FALSE){

        ($add) ? $this->query_metodos[$index] .= " {$valor} " : $this->query_metodos[$index] = $valor;
    
    }       
    
    private function setMetodosRs($valor){
        
        return $this->rs = mysql_query($valor);
       
    }
    
    private function setMetodosRow($valor){
        
        return $this->row = mysql_fetch_array($valor);
       
    }
    
    private function getMetodosQuery($index){
        
        $query = '';
        
        if(empty($index)){
            for ($i = 0; $i < 9; $i++) {

                $query .= " {$this->query_metodos[$i]} ";
                $this->query_metodos[$i] = '';

            }
        }
        else {
            
            $query = $this->query_metodos[$index];
            $this->query_metodos[$index] = '';
            
        }
        
            
        return $query;
        
    }     
    
    public function selectListaUnidadesTrabalhadas(){
    
        $this->setMetodosQuery("SELECT "
                            . "  p.id_projeto,"
                            . "  p.nome AS projeto_nome "
                             ,SELECT);
        
        $this->db->setQuery("FROM rh_clt AS clt  LEFT JOIN projeto AS p ON (clt.id_projeto = p.id_projeto)",FROM);
        
        if(!empty(get_parent_class($this))){
    
            $this->setMetodosQuery("WHERE clt.id_clt=".parent::getIdClt(), WHERE);
            
        }

        
        if($this->setMetodosRs($this->getMetodosQuery())){
            
            return 1;
            
        }
        else {

            $this->setError(mysql_error());            
            return 0;
            
        }        
        
    }
    
    
    public function getUnidadesTrabalhadasRow(){
        
        if($this->setMetodoRow($this->rs)){
            
            $this->setProjetoId($this->row['id_projeto']);
            $this->setProjetoNome($this->row['projeto_nome']);
            
            return 1;
            
        }
        else {
            
            return 0;
            
        }
        
    }
    
    public function getUnidadesTrabalhadasTot(){
        
        return mysql_num_rows($this->rs);
        
    }   
    
   
    public function getDadosContratacao($index){
        
           
        $data_contratacao = implode('/', array_reverse(explode('-', parent::getDataEntrada('d/m/Y'))));

        $data_contratacao_array = array(
                "data" => parent::getDataEntrada(),
                "data_fmt" => parent::getDataEntrada('d/m/Y'), 
                "dia" => $data_contratacao[0],
                "mes" => $data_contratacao[1],
                "ano" => $data_contratacao[2],
                "status_contratacao" => parent::getStatusContratacao()             
                );
        
        if(isset($index)){
            
            return $data_contratacao_array[0];
            
        }
        else {
            
            return $data_contratacao_array;
            
        }
            
        
    }    
        
}