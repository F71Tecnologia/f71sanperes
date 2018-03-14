<?php
/*
 * Módulo de classe de CTPS (Carteira de Trabalho por Tempo de Serviço)
 * Data Criação: 27/04/2015
 * Adaptado e incorporado ao sistema de classes da CltClassObj em 18/05/2015
 * Desenvolvimento: Jacques de Azevedo Nunes
 * e-mail: jacques@f71.com.br
 * Versão: 0.2 (Build 00002)* 
 */
include('/home/ispv/public_html/intranet/classes/CltTipoPgClass.php');


class CltCtpsClass extends CltTipoPgClass {
    
    protected $versao = '0.2.00002';              // Versão/build da classe
    protected $rs = '';
    protected $rsRecebidoPor = '';
    protected $rsEntreguePor = '';
    protected $rsFuncionario = '';
    protected $row = '';
    protected $querySelect = '';
    protected $queryInsert = '';
    protected $queryUpdate = '';
    protected $querySelectRecebidoPor = '';
    protected $querySelectEntreguePor = '';
    protected $querySelectFuncionario = '';
    protected $error = '';
    
    private $ctps = array(
                        'id_controle' => 0,
                        'id_regiao' => 0,
                        'id_user_cad' => 0,
                        'id_user_ent' => 0,
                        'nome' => '',
                        'numero' => '',
                        'serie' => '',
                        'uf' => '',
                        'obs' => '',
                        'preenchimento' => '',
                        'obs_preenchimento' => '',
                        'data_cad' => '',
                        'data_ent' => '',
                        'acompanhamento' => 0,
                        'status_reg' => 0
                        );
    
    protected $query_ctps = array(QUERY => '',
                             SELECT => '',
                             UPDATE => '',
                             INSERT => '',
                             WHERE => '',
                             GROUP => '',
                             HAVING => '',
                             ORDER => '',
                             LIMIT => '');
    
    protected $rsCtps;
    protected $row;    
    
    function __construct() {
        

    }
    
    public function setCtpsDefault(){
        
        $this->setIdControle(0);
        $this->setIdRegiao(0);
        $this->setIdUserCad(0);
        $this->setIdUserEnt(0);
        $this->setNome('');
        $this->setNumero('');
        $this->setSerie('');
        $this->setUf('');
        $this->setObs('');
        $this->setPreenchimento('');
        $this->setObsPreenchimento('');
        $this->setDataCad('');
        $this->setDataEnt('');
        $this->setAcompanhamento(0);
        $this->setStatusReg(0);
        
        
    }
    
    public function setIdControle($valor){
        
        $this->ctps['id_controle'] = $valor;
        
    }
    
    public function setIdRegiao($valor){
        
        $this->ctps['id_regiao'] = $valor;
        
    }
    
    public function setIdUserCad($valor){
        
        $this->ctps['id_user_cad'] = $valor;
        
    }
    
    public function setIdUserEnt($valor){
        
        $this->ctps['id_user_ent'] = $valor;
        
    }
    
    public function setNome($valor){
        
        $this->ctps['nome'] = $valor;
        
    }

    public function setNumero($valor){
        
        $this->ctps['numero'] = $valor;
    }    

    public function setSerie($valor){
        
        $this->ctps['serie'] = $valor;
        
    }    

    public function setUf($valor){
        
        $this->ctps['uf'] = $valor;
        
    }    

    public function setObs($valor){
        
        $this->ctps['obs'] = $valor;
        
    }    

    public function setPreenchimento($valor){
        
        $this->ctps['preenchimento'] = $valor;
        
    }    
    
    
    public function setObsPreenchimento($valor){
        
        $this->ctps['obs_preenchimento'] = $valor;
        
    }    
    
    public function setDataCad($valor){
        
        $this->ctps['data_cad'] = $valor;
        
    }    
    
    public function setDataEnt($valor){

        $this->ctps['data_ent'] = $valor;
        
    }    
    
    public function setAcompanhamento($valor){
        
        $this->ctps['acompanhamento'] = $valor;
        
    }    
    
    public function setStatusReg($valor){
        
        $this->ctps['status_reg'] = $valor;
        
    }    


    private function setCtpsRs($valor){
        
        $this->rsCtps = $valor;
        
    }    
    
    private function setCtpsQuery($valor,$index = QUERY){

        return $this->query[$index] = $valor;
    
    }    
    
    private function setTipoPgRs($valor){
        
        return $this->rsTipoPg = mysql_query($valor);
       
    }
    
    private function setTipoPgRow($valor){
        
        return $this->row = mysql_fetch_array($valor);
       
    }  
    
    public function getTipoPgQuery($index){
        
        $query = '';
        
        if(empty($index)){
            for ($i = 0; $i < 9; $i++) {

                $query .= " {$this->query_tipo_pg[$i]} ";

            }
        }
        else {
            
            $query = $this->query_tipo_pg[$index];
            
        }
        
            
        return $query;
        
    }    
    
    
    public function getIdControle(){
        
        return $this->ctps['id_controle'];
        
    }
    
    public function getIdRegiao(){
        
        return $this->ctps['id_regiao'];
    }
    
    public function getIdUserCad(){
        
        return $this->ctps['id_user_cad'];
        
    }
    
    public function getIdUserEnt(){
        
        return $this->ctps['id_user_ent'];
        
    }
    
    public function getNome(){
        
        return $this->ctps['nome'];
        
    }

    public function getNumero(){
        
        return $this->ctps['numero'];
    }    

    public function getSerie(){
        
        return $this->ctps['serie'];
        
    }    

    public function getUf(){
        
        return $this->ctps['uf'];
        
    }    

    public function getObs($valor){
        
        return $this->ctps['obs'];
        
    }    
    
    
    public function getObsPreenchimento(){
        
        return $this->ctps['obs_preenchimento'];
        
    }    
    
    public function getDataCad(){
        
        return $this->ctps['data_cad'];
        
    }    
    
    public function getDataEnt(){

        return $this->ctps['data_ent'];
        
    }    
    
    public function getAcompanhamento(){
        
        return $this->ctps['acompanhamento'];
        
    }    
    
    public function getStatusReg(){
        
        return $this->ctps['status_reg'];
        
    }    
    
    /*
     * Retorna o total de linhas do recordset
     */
    public function getCountRow(){

        return mysql_num_rows($this->rsCtps);
        
    }
    
   
    /*
     * Function para verificar se existe alguém elemento do array com valor null
     */
    private function isNullArray($arry) {
        
       
        foreach ($arry as $key => $value) {
            
         
           if(is_null($value) && !is_numeric($key)){
               return $key;
           }
           
        }
        
        return 0;

    }
    
   private function setUpdate(){
       
       $this->queryUpdate = ""
               . "UPDATE controlectps "
               . "SET "
                    . "acompanhamento = $this->acompanhamento, "
                    . "data_ent = '$this->data_ent', "
                    . "id_user_ent = '$this->id_user_ent' "
               . "WHERE id_controle = '$this->id_controle'";
       
   }
   
   private function errorInsert(){
        
       
       if(strlen($this->id_regiao)==0 || is_null($this->id_regiao)){
           $this->setError('ID_REGIAO não definido');
           return 1;
       }

       if(strlen($this->id_user_cad)==0 || is_null($this->id_user_cad)){
           $this->setError('ID_USER_CAD não definido');
           return 1;
       }
       
       if(strlen($this->nome)==0 || is_null($this->nome) || strlen($this->nome) > 250){
           $this->setError('Erro no campo NOME: zerado, nulo o maior que 250');
           return 1;
       }

       if(strlen($this->numero)==0 || is_null($this->numero) || strlen($this->numero) > 150){
           $this->setError('Erro no campo NÚMERO: zerado, nulo ou maior que 150');
           return 1;
       }
       
       if(strlen($this->serie)==0 || is_null($this->serie) || strlen($this->uf) > 150){
           $this->setError('Erro no campo SÉRIE: zerado, nulo ou maior que 150');
           return 1;
       }
       
       if(strlen($this->uf)==0 || is_null($this->uf) || strlen($this->uf)!=2 ){
           $this->setError('Erro no campo UF: zerado, nulo ou diferente de 2');
           return 1;
       }
       
       if(strlen($this->obs) > 250){
           $this->setError('Erro no campo OBS: maior que 250');
           return 1;
       }
       
      
       if(strlen($this->data_cad)==0 or is_null($this->data_cad)){
           $this->setError('DATA DO CADASTRAMENTO não definida');
           return 1;
       }
       
       return 0;
        
   }

    
    private function setInsert(){
        
        
        $this->queryInsert = "
            INSERT INTO controlectps 
                (
                id_regiao,
                id_user_cad,
                nome,numero,
                serie,
                uf,
                obs,
                obs_preenchimento,
                preenchimento,
                data_cad
                ) 
            VALUES 
                ("
                . "$this->id_regiao,"
                . "$this->id_user_cad,"
                . "'$this->nome',"
                . "'$this->numero',"
                . "'$this->serie',"
                . "'$this->uf',"
                . "'$this->obs',"
                . "'$this->obs_preenchimento',"
                . "'$this->preenchimento',"
                . "'$this->data_cad'"
                . ")";
            
            
        return 1;
        
        
    }
    
    private function setSelect(){
        
        $this->querySelect = ""
                . "SELECT "
                    . "id_controle,"
                    . "id_regiao,"
                    . "id_user_cad,"
                    . "id_user_ent,"
                    . "nome,"
                    . "numero,"
                    . "serie,"
                    . "uf,"
                    . "obs,"
                    . "preenchimento,"
                    . "obs_preenchimento,"
                    . "DATE_FORMAT(data_cad, '%d/%m/%Y') AS data_cad,"
                    . "DATE_FORMAT(data_ent, '%d/%m/%Y') AS data_ent,"
                    . "acompanhamento,"
                    . "status_reg "
                . "FROM controlectps ";
        
        if($this->id_controle > 0 || $this->id_regiao > 0 || $this->acompanhamento > 0) {
            $this->querySelect .= "WHERE 1=1 ";
         
            if($this->id_controle > 0) {
                $this->querySelect .= "and id_controle = $this->id_controle ";
            }

            if($this->id_regiao > 0) {
                $this->querySelect .= "and id_regiao = $this->id_regiao ";
            }

            if($this->acompanhamento > 0) {
                $this->querySelect .= "and acompanhamento = $this->acompanhamento ";
            }
        
        }
        
        return 1;
        
    }
    
    
    private function setRecebidoPor(){
        
        $this->querySelectRecebidoPor = "SELECT nome FROM funcionario WHERE id_funcionario = $this->id_user_cad ";
        
    }   
    
    private function setEntreguePor(){
        
        $this->querySelectEntreguePor = "SELECT nome FROM funcionario WHERE id_funcionario = $this->id_user_ent ";        
        
    }
    
  
       
    public function insert(){
        
        $this->setInsert();
        
        if($this->errorInsert()){
            return 0;
        }
        
        if(mysql_query($this->getQueryInsert())) {
            $this->setIdControle(mysql_insert_id());
            return 1; // Define o ID gerado no INSERT
        }
        else {
            $this->setError(mysql_error());
            return 0;
        }
    
    }
    
    public function update(){
        
        $this->setUpdate();
        
        if(mysql_query($this->getQueryUpdate())) {
            $this->setIdControle();            
            return 1; 
        }
        else {
            $this->setError(mysql_error());
            return 0;
        }
    
    }    
    
    
    public function select() {

        $this->setSelect();
       
        if($this->rs = mysql_query($this->querySelect)){
            return 1;
        }
        else{
            $this->setError(mysql_error());
            return 0;
        }        
        
    }
    
    
    private function getQuerySelect(){
        
        return $this->querySelect;
        
    }

    private function getQueryUpdate(){
        
        return $this->queryUpdate;
        
    }
    
    private function getQueryInsert(){
        
        return $this->queryInsert;
        
    }
    
    public function selectRecebidoPor(){
        
        return ($this->rsRecebidoPor = mysql_query($this->querySelectRecebidoPor));        
        
    }
    
    public function selectEntreguePor(){
        
        return ($this->rsEntreguePor = mysql_query($this->querySelectEntreguePor));        
        
    }
    
    public function selectFuncionario(){
        
        return ($this->rsFuncionario = mysql_query($this->querySelectFuncionario));        
        
    }
    
    /*
     * Obtem a linha do recorset e define as variáveis da classe
     */
    public function getRow(){
        
        if($this->row = mysql_fetch_array($this->rs)){
            
            $this->setIdControle($this->row['id_controle']);
            $this->setIdRegiao($this->row['id_regiao']);
            $this->setIdUserCad($this->row['id_user_cad']);
            $this->setIdUserEnt($this->row['id_user_ent']);
            $this->setNome($this->row['nome']);
            $this->setNumero($this->row['numero']);
            $this->setSerie($this->row['serie']);
            $this->setUf($this->row['uf']);
            $this->setObs($this->row['obs']);
            $this->setPreenchimento($this->row['preenchimento']);
            $this->setObsPreenchimento($this->row['obs_preenchimento']);
            $this->setDataCad($this->row['data_cad']);
            $this->setDataEnt($this->row['data_ent']);
            $this->setAcompanhamento($this->row['acompanhamento']);
            $this->setStatusReg($this->row['status_reg']);
            
            return 1;
            
        }
        else {
    
            $this->setError(mysql_error());
            return 0;
            
        }
        
    }
    
    public function getRowRecebidoPor(){
        
        $this->setRecebidoPor();
        $this->selectRecebidoPor();
       
        if($this->row = mysql_fetch_array($this->rsRecebidoPor)){
            
            return $this->row['nome'];
            
        }
        else {
            $this->setError(mysql_error());    
            return '';
        }
        
    }
    
    public function getRowEntreguePor(){
        
        $this->setEntreguePor();
        $this->selectEntreguePor();
        
        if($this->row = mysql_fetch_array($this->rsEntreguePor)){
            
            return $this->row['nome'];
            
        }
        else {
            $this->setError(mysql_error());            
            return '';
        }
        
    }

    
    public function getLabelPreenchimento(){

        switch($this->preenchimento){
            case 1: return "Assinar"; 
                    break;
            case 2: return "Dar Baixa"; 
                    break;
            case 3: return "Férias"; 
                    break;
            case 4: return "13º Salário"; 
                    break;
            case 5: return "Licança"; 
                    break;
            case 6: return "Outros"; 
                    break;
        }        
        
    }
            
            
    public function getError(){
        
        return $this->error;
        
    }    
    
}

/* Arquivos utilizando essa classe
 
*/

?>
