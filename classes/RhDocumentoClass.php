<?php


include('/home/ispv/public_html/intranet/classes/CltBancoClass.php');


class CltDocumentoClass extends CltBancoClassClass {
    

    private $documento_clt_anexo = array(
                        'anexo_id' => 0,
                        'id_upload'=> 0,
                        'anexo_nome' => '',
                        'anexo_diretorio' => '',
                        'anexo_extensao' => '',
                        'ordem' => 0,
                        'data_cad' => '',
                        'user_cad' => 0,
                        'anexo_status' => 0
                        );
    
    private $upload = array(
                        'anexo_id' => 0,
                        'arquivo' => '',
                        'descricao' => '',
                        'status_reg' => 0,
                        'ordem' => 0
                        );


    private $config = array(
                        'diretorio_padrao' => '',
                        'diretorio_internet' => '',
                        'del_diretorio_internet' => ''
                        );
    
    protected $query_bancos = array(QUERY => '',
                             SELECT => '',
                             UPDATE => '',
                             INSERT => '',
                             WHERE => '',
                             GROUP => '',
                             HAVING => '',
                             ORDER => '',
                             LIMIT => '');
    
    protected $rsDocumento;
    protected $row;    
    
   
    private function setDefaultBanco() {
        
    }
    
   
    public function setBancoId($valor){
        
        return $this->bancos['id_banco'] = $valor;
        
    } 
    
    
    private function setBancoQuery($valor,$index = QUERY){

        return $this->query_tipo_pg[$index] = $valor;
    
    }    
    
    private function setBancoRs($valor){
        
        return $this->rsBanco = mysql_query($valor);
       
    }
    
    private function setBancoRow($valor){
        
        return $this->row = mysql_fetch_array($valor);
       
    }  
    
    public function getBancoId(){
        
        return $this->bancos['id_banco'];
        
    } 
    
    
    public function getBancoQuery($index){
        
        $query = '';
        
        if(empty($index)){
            for ($i = 0; $i < 9; $i++) {

                $query .= " {$this->query_banco[$i]} ";

            }
        }
        else {
            
            $query = $this->query_banco[$index];
            
        }
        
            
        return $query;
        
    }    
    
    
    public function getBancoRow(){
        
        if($this->setBancoRow($this->rsBanco)){
            
            $this->setBancoId($this->row["id_banco"]);
            
            return 1;
            
        }
        else{
            
            parent::setError(mysql_error());
            
            return 0;
        }
        
    }
    
    public function selectBanco(){
        
                
        $this->setBancoQuery("SELECT 
                                id_banco,
                             FROM bancos ",SELECT);
        
        $this->setBancoQuery(parent::getBanco() ? "WHERE id_banco = ".parent::getBanco()." AND id_regiao=".parent::getIdRegiao(): "",WHERE);
        
        
        if($this->setBancoRs($this->getBancoQuery())){
            
            return 1;
            
        }
        else {

            parent::setError(mysql_error());            
            return 0;
            
        }        
        
    }     
    
    
    
}
