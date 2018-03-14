<?php

class FeriasProgramadasClass {
    
    protected $id_ferias_programadas;
    protected $id_clt;
    protected $inicio;
    protected $dias_ferias;
    protected $fim;
    protected $data_cad;
    protected $id_funcionario;
    protected $status;
    protected $aquisitivo_inicial;
    protected $aquisitivo_final;
    protected $abono_pecuniario;
    protected $ignorar_faltas;
    protected $ignorar_Ferias_dobradas;
     
    
    protected $QUERY;
    protected $SELECT = " * ";
    protected $FROM = " rh_ferias_programadas ";
    protected $WHERE;
    protected $GROUP;
    protected $HAVING;
    protected $ORDER;
    protected $LIMIT;
    
    protected $rsFeriasProgramadas;
    protected $rowFeriasProgramadas;
    protected $numRowsFeriasProgramadas;
        
    function __construct() {
        
    }
    
    function getIdFeriasProgramadas() {
        return $this->id_ferias_programadas;
    }

    function getIdClt() {
        return $this->id_clt;
    }

    function getInicio() {
        return $this->inicio;
    }

    function getDataCad() {
        return $this->data_cad;
    }

    function getIdFuncionario() {
        return $this->id_funcionario;
    }

    function getStatus() {
        return $this->status;
    }
    function setIdFeriasProgramadas($id_ferias_programadas) {
        $this->id_ferias_programadas = $id_ferias_programadas;
    }

    function setIdClt($id_clt) {
        $this->id_clt = $id_clt;
    } 

    function setInicio($inicio) {
        $this->inicio = $inicio;
    }
    
    function setDiasFerias($value) {
        $this->dias_ferias = $value;
    }
    
    function setAbonoPecuniario($abono_pecuniario) {
        $this->abono_pecuniario = $abono_pecuniario;
    }
    
    function setIgnorarFaltas($ignorar_faltas) {
        $this->ignorar_faltas = $ignorar_faltas;
    }

    function setIgnorarFeriasDobradas($ignorar_Ferias_dobradas) {
        $this->ignorar_Ferias_dobradas = $ignorar_Ferias_dobradas;
    }    

    function setDataCad($data_cad) {
        $this->data_cad = $data_cad;
    }

    function setIdFuncionario($id_funcionario) {
        $this->id_funcionario = $id_funcionario;
    }

    function setFim($fim) {
        $this->fim = $fim;
    }

    function setStatus($status) {
        $this->status = $status;
    }
    
    public function setPeriodoInicio($aquisitivo_ini) {
        $this->aquisitivo_inicial = $aquisitivo_ini;
    }
    public function setPeriodoFim($aquisitivo_fim) {
        $this->aquisitivo_final = $aquisitivo_fim;
    }
    
    protected function setQUERY($QUERY) {
        $this->QUERY = $QUERY;
    }

    protected function setSELECT($SELECT) {
        $this->SELECT = $SELECT;
    }

    protected function setFROM($FROM) {
        $this->FROM = $FROM;
    }

    protected function setWHERE($WHERE) {
        $this->WHERE = $WHERE;
    }

    protected function setGROUP($GROUP) {
        $this->GROUP = $GROUP;
    }

    protected function setORDER($ORDER) {
        $this->ORDER = $ORDER;
    }

    protected function setLIMIT($LIMIT) {
        $this->LIMIT = $LIMIT;
    }

    protected function setHAVING($HAVING) { 
        $this->HAVING = $HAVING;
    }

    protected function setRsFeriasProgramadas($valor){ 
        if(!empty($this->QUERY)){
            $sql = $this->QUERY;
        } else {
            $auxWhere  = (!empty($this->WHERE))  ? " WHERE $this->WHERE "    : null ;
            $auxGroup  = (!empty($this->GROUP))  ? " GROUP BY $this->GROUP " : null ;
            $auxHaving = (!empty($this->HAVING)) ? " HAVING $this->HAVING "  : null ;
            $auxOrder  = (!empty($this->ORDER))  ? " ORDER BY $this->ORDER " : null ;
            $auxLimit  = (!empty($this->LIMIT))  ? " LIMIT $this->LIMIT "    : null ;
            
            $sql = "SELECT $this->SELECT FROM $this->FROM $auxWhere $auxGroup $auxHaving $auxOrder $auxLimit";
        }
        
        $this->rsFeriasProgramadas = mysql_query($sql);
        $this->numRowsFeriasProgramadas = mysql_num_rows($this->rsFeriasProgramadas);
        return $this->rsFeriasProgramadas;
    }
    
    protected function limpaQuery() {
        $this->setQUERY("");
        $this->setSELECT(" * ");
        $this->setFROM(" rh_ferias_programada ");
        $this->setWHERE("");
        $this->setGROUP("");
        $this->setHAVING("");
        $this->setORDER("");
        $this->setLIMIT("");
    }
    
    public function getPeriodoInicio() {
        return $this->aquisitivo_inicial;
    }
    
    function getDiasFerias() {
        return $this->dias_ferias;
    }
    
    function getAbonoPecuniario() {
        return $this->abono_pecuniario;
    }
    
    function getIgnorarFaltas() {
        return $this->ignorar_faltas;
    }
    
    function getIgnorarFeriasDobradas() {
        return $this->ignorar_Ferias_dobradas;
    }
    
    function getFim() {
        return $this->fim;
    }
    
    public function getPeriodoFim() {
        return $this->aquisitivo_final;
    }
    
    public function getNumRowFeriasProgramadas(){
        return $this->numRowsFeriasProgramadas;
    }

    protected function setRowFeriasProgramadas($valor){
        return $this->rowFeriasProgramadas = mysql_fetch_assoc($valor);
    }
    
    public function getRowFeriasProgramadas(){

        if($this->setRowFeriasProgramadas($this->rsFeriasProgramadas)){
            
            $this->setIdFeriasProgramada($this->rowFeriasProgramadas['id_ferias_programadas']);
            $this->setIdClt($this->rowFeriasProgramadas['id_clt']);
            $this->setInicio($this->rowFeriasProgramadas['inicio']);
            $this->setFim($this->rowFeriasProgramadas['fim']);
            $this->setDataCad($this->rowFeriasProgramadas['data_cad']);
            $this->setIdFuncionario($this->rowFeriasProgramadas['id_funcionario']);
            $this->setStatus($this->rowFeriasProgramadas['status']);
            
            return 1;
        } else{
            return 0;
        }
    }
    
    public function getFeriasProgramadas($mes, $ano, $id_regiao, $auxProjeto = null){
        $sql = "
        SELECT A.*,B.*, B.nome, B.id_regiao,E.unidade ,C.nome AS funcao, DATE_FORMAT(A.inicio, '%d/%m/%Y') inicioBR, DATE_FORMAT(A.fim, '%d/%m/%Y') fimBR,DATE_FORMAT(A.aquisitivo_ini, '%d/%m/%Y') aq_inicioBR,DATE_FORMAT(A.aquisitivo_fim, '%d/%m/%Y') aq_fimBR, D.nome AS nome_projeto
        FROM rh_ferias_programadas A 
        INNER JOIN rh_clt B ON(A.id_clt = B.id_clt AND B.id_regiao = $id_regiao $auxProjeto)
        LEFT JOIN curso AS C ON(B.id_curso = C.id_curso)
        LEFT JOIN projeto AS D ON(B.id_projeto = D.id_projeto)
        LEFT JOIN unidade AS E ON(E.id_unidade = B.id_unidade)
        WHERE 
            (MONTH(A.inicio) = $mes AND YEAR(A.inicio) = $ano)
            AND A.status = 1
            ORDER BY B.nome";
        
        $query = mysql_query($sql) or die(mysql_error());
        
        while($row = mysql_fetch_assoc($query)){
            $programacao[] = $row;
        }
        return $programacao;
    }
    
    public function getFeriasProgramadasById($id_ferias_prog){
        $sql = "
        SELECT A.*,B.*, B.nome,C.nome AS funcao,D.nome AS nome_projeto,E.razao,E.cnpj, DATE_FORMAT(A.inicio, '%d/%m/%Y') inicioBR,DATE_FORMAT(A.aquisitivo_ini, '%d/%m/%Y') aq_inicioBR,DATE_FORMAT(A.aquisitivo_fim, '%d/%m/%Y') aq_fimBR,DATE_FORMAT(B.data_entrada, '%d/%m/%Y') data_entradaBR, DATE_FORMAT(A.fim, '%d/%m/%Y') fimBR,
        DATE_FORMAT(DATE_ADD(A.fim, INTERVAL 1 DAY), '%d/%m/%Y') retornoBR
        FROM rh_ferias_programadas A
        INNER JOIN rh_clt B ON(A.id_clt = B.id_clt)
        LEFT JOIN curso AS C ON(B.id_curso = C.id_curso)
        LEFT JOIN projeto as D ON(B.id_projeto = D.id_projeto)
        LEFT JOIN rhempresa as E ON(D.id_projeto = E.id_projeto)
        WHERE A.id_ferias_programadas = $id_ferias_prog
        AND A.status = 1
            ORDER BY B.nome";
        $query = mysql_query($sql) or die(mysql_error());
        while($row = mysql_fetch_assoc($query)){
            $programacao = $row;
        }
        return $programacao;
    }
    
    public function getVerAgendamentosDia($data, $id_regiao, $auxProjeto = null){
        $sql = "
        SELECT A.*, B.nome, DATE_FORMAT(A.inicio, '%d/%m/%Y') inicioBR, DATE_FORMAT(A.fim, '%d/%m/%Y') fimBR
        FROM rh_ferias_programadas A INNER JOIN rh_clt B ON(A.id_clt = B.id_clt AND B.id_regiao = {$id_regiao} $auxProjeto)
        WHERE 
            A.inicio <= '$data' AND A.fim >= '$data'
            AND A.status = 1
        ORDER BY B.nome";
        $query = mysql_query($sql) or die(mysql_error());
        while($row = mysql_fetch_assoc($query)){
            $array[] = $row;
        }
        return $array;
    }
    
    public function insertFeriasProgramadas(){
        $this->limpaQuery();
        
        $array['id_clt'] = $this->getIdClt();
        $array['inicio'] = $this->getInicio();
        $array['fim'] = $this->getFim();
        $array['data_cad'] = date('Y-m-d H:i:s');
        $array['id_funcionario'] = $this->getIdFuncionario();
        $array['status'] = 1;
        $array['aquisitivo_ini'] = $this->getPeriodoInicio();
        $array['aquisitivo_fim'] = $this->getPeriodoFim();
        $array['dias_ferias'] = $this->getDiasFerias();
        $array['abono_pecuniario'] = $this->getAbonoPecuniario();
        $array['ignorar_faltas'] = $this->getIgnorarFaltas();
        $array['ignorar_ferias_dobradas'] = $this->getIgnorarFeriasDobradas();
        
        $keys = implode(', ', array_keys($array));
        $values = implode("' , '", $array);
        
        $this->setQUERY("INSERT INTO rh_ferias_programadas ($keys) VALUES ('$values');"); 

       
        //echo $this->QUERY."<br>";
        if($this->setRsFeriasProgramadas()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
    
//    public function updateFeriasProgramadas(){
//        $this->limpaQuery();
//        
//        $array['prestador_dep_nome'] = $this->getNome();
//        $array['prestador_dep_tel'] = $this->getTel();
//        $array['prestador_dep_data_nasc'] = $this->getDataNasc();
//        $array['prestador_id'] = $this->getIdPrestador();
//        $array['prestador_dep_parentesco'] = $this->getParentesco();
//        $array['prestador_dep_status'] = $this->getStatus();
//        
//        foreach ($array as $key => $value) {
//            $camposUpdate[] = "$key = '$value'";
//        }
//        
//        $this->setQUERY("UPDATE rh_ferias_programada SET " . implode(", ",($camposUpdate)) ." WHERE id_ferias_programada = {$this->getIdFeriasProgramadas()} LIMIT 1;");
//        echo $this->QUERY."<br>";
////        if($this->setRsFeriasProgramadas()){
////            return 1;
////        } else {
////            return 0;//$this->setError(mysql_error());
////        }
//    }
    
    public function deletarFeriasProgramadas(){
        $this->limpaQuery();
        $this->setQUERY("UPDATE rh_ferias_programadas SET status = 0 WHERE id_ferias_programadas = {$this->getIdFeriasProgramadas()} LIMIT 1;");

//        echo $this->QUERY."<br>";
        if($this->setRsFeriasProgramadas()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
}