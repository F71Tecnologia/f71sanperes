<?php
/* 
 * Módulo Objeto da classe ParceirosClass2 orientado ao FrameWork do sistema da F71
 * Data Criação: 26/05/2015
 * Desenvolvimento: Renato
 * Versão: 0.1 (Build 00001)
 * 
 * Obs sobre a versão: 
 * 
 */

class PrestadorDocumentosClass {
    
    protected $prestador_documento_id;
    protected $id_prestador;
    protected $prestador_tipo_doc_id;
    protected $data_vencimento;
    protected $nome_arquivo;
    protected $extensao_arquivo;
    protected $status;
    
    protected $QUERY;
    protected $SELECT = " * ";
    protected $FROM = " prestador_documentos ";
    protected $WHERE;
    protected $GROUP;
    protected $HAVING;
    protected $ORDER;
    protected $LIMIT;
    
    protected $rsPrestadorDocumentos;
    protected $rowPrestadorDocumentos;
    protected $numRowsPrestadorDocumentos;
        
    function __construct() {
        
    }
    
    function getPrestador_documento_id() {
        return $this->prestador_documento_id;
    }

    function getId_prestador() {
        return $this->id_prestador;
    }

    function getPrestador_tipo_doc_id() {
        return $this->prestador_tipo_doc_id;
    }

    function getData_vencimento($formato) {
        if(empty($this->data_vencimento) || $this->data_vencimento == '0000-00-00') { return ''; } 
        else {
            return (!empty($formato)) ? date_format(date_create($this->data_vencimento), $formato) : $this->data_vencimento ;
        }
    }

    function getNome_arquivo() {
        return $this->nome_arquivo;
    }

    function getExtensao_arquivo() {
        return $this->extensao_arquivo;
    }

    function getStatus() {
        return $this->status;
    }

    function setPrestador_documento_id($prestador_documento_id) {
        $this->prestador_documento_id = $prestador_documento_id;
    }

    function setId_prestador($id_prestador) {
        $this->id_prestador = $id_prestador;
    }

    function setPrestador_tipo_doc_id($prestador_tipo_doc_id) {
        $this->prestador_tipo_doc_id = $prestador_tipo_doc_id;
    }

    function setData_vencimento($data_vencimento) {
        $this->data_vencimento = $data_vencimento;
    }

    function setNome_arquivo($nome_arquivo) {
        $this->nome_arquivo = $nome_arquivo;
    }

    function setExtensao_arquivo($extensao_arquivo) {
        $this->extensao_arquivo = $extensao_arquivo;
    }

    function setStatus($documento_status) {
        $this->status = $documento_status;
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
    
    protected function setRsPrestadorDocumentos($valor){ 
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
        //echo $sql;
        $this->rsPrestadorDocumentos = mysql_query($sql) or die(mysql_error());
        $this->numRowsPrestadorDocumentos = mysql_num_rows($this->rsPrestadorDocumentos);
        return $this->rsPrestadorDocumentos;
    }
    
    protected function limpaQuery() {
        $this->setQUERY("");
        $this->setSELECT(" * ");
        $this->setFROM(" prestador_tipo_doc ");
        $this->setWHERE("");
        $this->setGROUP("");
        $this->setHAVING("");
        $this->setORDER("");
        $this->setLIMIT("");
    }
    
    public function getNumRowPrestadorDocumentos(){
        return $this->numRowsPrestadorDocumentos;
    }

    protected function setRowPrestadorDocumentos($valor){
        return $this->rowPrestadorDocumentos = mysql_fetch_assoc($valor);
    }
    
    public function getRowPrestadorDocumentos(){

        if($this->setRowPrestadorDocumentos($this->rsPrestadorDocumentos)){
            
            $this->setPrestador_documento_id($this->rowPrestadorDocumentos["prestador_documento_id"]);
            $this->setId_prestador($this->rowPrestadorDocumentos["id_prestador"]);
            $this->setPrestador_tipo_doc_id($this->rowPrestadorDocumentos["prestador_tipo_doc_id"]);
            $this->setData_vencimento($this->rowPrestadorDocumentos["data_vencimento"]);
            $this->setNome_arquivo($this->rowPrestadorDocumentos["nome_arquivo"]);
            $this->setExtensao_arquivo($this->rowPrestadorDocumentos["extensao_arquivo"]);
            $this->setStatus($this->rowPrestadorDocumentos["status"]);
            
            return 1;
        } else{
            //$this->setError(mysql_error());
            return 0;
        }
    }
    
    public function getDocumentoPrestador(){
        $this->limpaQuery();
        $auxPrestador = (!empty($this->getId_prestador())) ? " AND id_prestador = {$this->getId_prestador()} " : null ;
        $auxTipoDoc = (!empty($this->getPrestador_tipo_doc_id())) ? " AND prestador_tipo_doc_id = {$this->getPrestador_tipo_doc_id()} " : null ;
        
        //$this->setSELECT("prestador_documento_id");
        $this->setFROM("prestador_documentos");
        $this->setWHERE("status = 1 $auxPrestador $auxTipoDoc");
        
        if($this->setRsPrestadorDocumentos()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
        
    public function getStatusList(){
        $this->limpaQuery();
        
        $this->setSELECT("prestador_documento_id");
        $this->setFROM("prestador_tipo_doc AS A LEFT JOIN prestador_documentos AS B ON (A.prestador_tipo_doc_id = B.prestador_tipo_doc_id AND B.id_prestador = {$this->getId_prestador()})");
        $this->setWHERE("id_prestador = {$this->getId_prestador()} AND status = 1");
        
        if($this->setRsPrestadorDocumentos()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
    
//    public static function getDocsVencidos($id_prestador){
//        $qr = "SELECT *,IF(qntDocs IS NOT NULL AND qntDocsVen IS NULL,1,0) as resultado FROM (
//                 SELECT A.prestador_tipo_doc_id,A.prestador_tipo_doc_nome,A.ordem,B.*,C.* FROM prestador_tipo_doc AS A
//                 LEFT JOIN 
//                  (
//                   SELECT prestador_tipo_doc_id as qntDocsVen FROM prestador_documentos
//                    WHERE id_prestador = {$id_prestador} AND data_vencimento > CURDATE()
//                    ORDER BY data_vencimento
//                  ) 
//                  AS B ON (A.prestador_tipo_doc_id = B.qntDocsVen)
//                 LEFT JOIN 
//                  (
//                   SELECT prestador_tipo_doc_id AS qntDocs FROM prestador_documentos
//                    WHERE id_prestador = {$id_prestador}
//                    ORDER BY data_vencimento
//                  ) 
//                 AS C ON (A.prestador_tipo_doc_id = C.qntDocs)
//                 ORDER BY A.ordem
//                ) AS temp
//                HAVING resultado = 1";
//        
//        $rs = mysql_query($qr);
//        $rowPrestadorDocumentos = mysql_num_rowPrestadorDocumentoss($rs);
//        return $rowPrestadorDocumentos;
//    }
    
    public function updatePrestadorDocumentos(){
        $this->limpaQuery();
        
        if(!empty($this->getId_prestador())){ $array['id_prestador'] = $this->getId_prestador(); }
        if(!empty($this->getPrestador_tipo_doc_id())){ $array['prestador_tipo_doc_id'] = $this->getPrestador_tipo_doc_id(); }
        if(!empty($this->getData_vencimento())){ $array['data_vencimento'] = $this->getData_vencimento(); }
        if(!empty($this->getNome_arquivo())){ $array['nome_arquivo'] = $this->getNome_arquivo(); }
        if(!empty($this->getExtensao_arquivo())){ $array['extensao_arquivo'] = $this->getExtensao_arquivo(); }
        if(!empty($this->getStatus()) || strlen($this->getStatus()) > 0){ $array['status'] = $this->getStatus(); }
        
        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        
//        echo "UPDATE prestador_documentos SET " . implode(", ",($camposUpdate)) ." WHERE prestador_documento_id = {$this->getPrestador_documento_id()} LIMIT 1;";
        $this->setQUERY("UPDATE prestador_documentos SET " . implode(", ",($camposUpdate)) ." WHERE prestador_documento_id = {$this->getPrestador_documento_id()} LIMIT 1;");
        
        if($this->setRsPrestadorDocumentos()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
    
    public function insertPrestadorDocumentos(){
        $this->limpaQuery();
        
        if(!empty($this->getId_prestador())){ $array['id_prestador'] = $this->getId_prestador(); }
        if(!empty($this->getPrestador_tipo_doc_id())){ $array['prestador_tipo_doc_id'] = $this->getPrestador_tipo_doc_id(); }
        if(!empty($this->getData_vencimento())){ $array['data_vencimento'] = $this->getData_vencimento(); }
        if(!empty($this->getNome_arquivo())){ $array['nome_arquivo'] = $this->getNome_arquivo(); }
        if(!empty($this->getExtensao_arquivo())){ $array['extensao_arquivo'] = $this->getExtensao_arquivo(); }
        $array['status'] = 1;
        
        $keys = implode(',', array_keys($array));
        $values = implode("' , '", $array);
        
        $this->setQUERY("INSERT INTO prestador_documentos ($keys) VALUES ('$values');");
        
//        echo $sql = "$this->QUERY";
        
        if($this->setRsPrestadorDocumentos()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }

}