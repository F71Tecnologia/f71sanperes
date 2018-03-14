<?php
require_once('ApiClass.php');

class ProtocolosEntregasClass {

    protected $id_protocolos_entregas;
    protected $id_regiao;
    protected $id_projeto;
    protected $mes_competencia;
    protected $ano_competencia;
    protected $identificador;
    protected $descricao;
    protected $id_tipo_protocolo;
    protected $data_cad;
    protected $status;
    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' protocolos_entregas ';
    protected $JOIN;
    protected $WHERE;
    protected $GROUP;
    protected $HAVING;
    protected $ORDER;
    protected $LIMIT;
    protected $rs;
    protected $row;
    protected $numRows;
    private $api;
    
    public function __construct() {    
        $this->api = new ApiClass();
    }
    

    //GET's DA CLASSE
    function getIdProtocolosEntregas() {
        return $this->id_protocolos_entregas;
    }

    function getIdRegiao() {
        return $this->id_regiao;
    }

    function getIdProjeto() {
        return $this->id_projeto;
    }

    function getMesCompetencia() {
        return $this->mes_competencia;
    }

    function getAnoCompetencia() {
        return $this->ano_competencia;
    }

    function getIdentificador() {
        return $this->identificador;
    }

    function getDescricao() {
        return $this->descricao;
    }

    function getIdTipoProtocolo() {
        return $this->id_tipo_protocolo;
    }

    function getDataCad() {
        return $this->data_cad;
    }

    function getStatus() {
        return $this->status;
    }

    //SET's DA CLASSE
    function setIdProtocolosEntregas($id_protocolos_entregas) {
        $this->id_protocolos_entregas = $id_protocolos_entregas;
    }

    function setIdRegiao($id_regiao) {
        $this->id_regiao = $id_regiao;
    }

    function setIdProjeto($id_projeto) {
        $this->id_projeto = $id_projeto;
    }

    function setMesCompetencia($mes_competencia) {
        $this->mes_competencia = $mes_competencia;
    }

    function setAnoCompetencia($ano_competencia) {
        $this->ano_competencia = $ano_competencia;
    }

    function setIdentificador($identificador) {
        $this->identificador = $identificador;
    }

    function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    function setIdTipoProtocolo($id_tipo_protocolo) {
        $this->id_tipo_protocolo = $id_tipo_protocolo;
    }

    function setDataCad($data_cad) {
        $this->data_cad = $data_cad;
    }

    function setStatus($status) {
        $this->status = $status;
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
    
    protected function setJOIN($table,$alias="B",$on,$type="LEFT"){
        $this->JOIN[] = array('table'=>$table,'alias'=>$alias,'on'=>$on,'type'=>$type);
    }
    
    protected function linpaJOIN(){
        unset($this->JOIN);
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

    //SET DEFAULT
    function setDefault() {
        $this->id_protocolos_entregas = null;
        $this->id_regiao = null;
        $this->id_projeto = null;
        $this->mes_competencia = null;
        $this->ano_competencia = null;
        $this->identificador = null;
        $this->descricao = null;
        $this->id_tipo_protocolo = null;
        $this->data_cad = null;
        $this->status = null;
    }

    protected function setRs() {
        if (!empty($this->QUERY)) {
            $sql = $this->QUERY;
        } else {
            $auxWhere = (!empty($this->WHERE)) ? " WHERE $this->WHERE " : null;
            $auxGroup = (!empty($this->GROUP)) ? " GROUP BY $this->GROUP " : null;
            $auxHaving = (!empty($this->HAVING)) ? " HAVING $this->HAVING " : null;
            $auxOrder = (!empty($this->ORDER)) ? " ORDER BY $this->ORDER " : null;
            $auxLimit = (!empty($this->LIMIT)) ? " LIMIT $this->LIMIT " : null;
            $auxFromAlias = null;
            $auxJoin = null;
            if(count($this->JOIN) > 0){
                $auxFromAlias = "AS A";
                foreach($this->JOIN as $join){
                    $auxJoin .= $join['type']." JOIN ".$join['table']." AS ".$join['alias']." ON ({$join['on']}) ";
                }
            }

            $sql = "SELECT $this->SELECT FROM $this->FROM $auxFromAlias $auxJoin $auxWhere $auxGroup $auxHaving $auxOrder $auxLimit";
        }

        $this->rs = mysql_query($sql);
        $this->numRows = mysql_num_rows($this->rs);
        return $this->rs;
    }

    protected function limpaQuery() {
        $this->setQUERY('');
        $this->setSELECT(' * ');
        $this->setFROM(' protocolos_entregas ');
        $this->setWHERE('');
        $this->setGROUP('');
        $this->setHAVING('');
        $this->setORDER('');
        $this->setLIMIT('');
        $this->linpaJOIN();
    }

    public function getNumRows() {
        return $this->numRows;
    }

    protected function setRow($valor) {
        return $this->row = mysql_fetch_assoc($valor);
    }

    //RECUPERANDO INFO DO BANCO
    public function getRow() {

        if ($this->setRow($this->rs)) {
            $this->setIdProtocolosEntregas($this->row['id_protocolos_entregas']);
            $this->setIdRegiao($this->row['id_regiao']);
            $this->setIdProjeto($this->row['id_projeto']);
            $this->setMesCompetencia($this->row['mes_competencia']);
            $this->setAnoCompetencia($this->row['ano_competencia']);
            $this->setIdentificador($this->row['identificador']);
            $this->setDescricao($this->row['descricao']);
            $this->setIdTipoProtocolo($this->row['id_tipo_protocolo']);
            $this->setDataCad($this->row['data_cad']);
            $this->setStatus($this->row['status']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_regiao' => addslashes($this->getIdRegiao()),
            'id_projeto' => addslashes($this->getIdProjeto()),
            'mes_competencia' => addslashes($this->getMesCompetencia()),
            'ano_competencia' => addslashes($this->getAnoCompetencia()),
            'identificador' => addslashes($this->getIdentificador()),
            'descricao' => addslashes($this->getDescricao()),
            'id_tipo_protocolo' => addslashes($this->getIdTipoProtocolo()),
            'data_cad' => addslashes($this->getDataCad()),
            'status' => addslashes($this->getStatus()),
        );

        return array_filter($array);
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE protocolos_entregas SET " . implode(", ", ($camposUpdate)) . " WHERE id_protocolos_entregas = {$this->getIdProtocolosEntregas()} LIMIT 1;");

        if ($this->setRs()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

    public function insert() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        $keys = implode(',', array_keys($array));
        $values = implode("' , '", $array);

        $this->setQUERY("INSERT INTO protocolos_entregas ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdProtocolosEntregas(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE protocolos_entregas SET status = 0 WHERE id_protocolos_entregas = {$this->getIdProtocolosEntregas()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM protocolos_entregas WHERE id_protocolos_entregas = {$this->getIdProtocolosEntregas()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function getProtocolosEntregas($toArray = false) {
        $array = $this->makeCampos();

        $array = array_filter($array);

        foreach ($array as $key => $value) {
            $condicoes[] = "$key = '$value'";
        }

        $this->limpaQuery();
        $this->setWHERE(implode(' AND ', $condicoes));


        if ($this->setRs()) {
            if ($toArray) {
                while ($row = mysql_fetch_assoc($this->rs)) {
                    $arrayX[] = $row;
                }
                return $arrayX;
            } else {
                return 1;
            }
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

    public function salvar() {
        if (empty($this->id_protocolos_entregas)) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }

    public function getProtocoloEntregaById() {
        $this->setWHERE("id_protocolos_entregas = " . $this->getIdProtocolosEntregas());
        $this->setRs();
        $this->getRow();
    }

    public function listaByPeriodo($mes, $ano, $id_tipo) {
        if (!empty($mes) && !empty($ano)) {
            $w = (empty($id_tipo)) ? '' : "AND id_tipo_protocolo = '$id_tipo'";
            $this->setWHERE("mes_competencia= '$mes' AND ano_competencia = '$ano' $w");
            $this->setRs();
            $arr = NULL;
            while ($this->getRow()) {
                $arr[] = [
                    'id_protocolos_entregas' => $this->getIdProtocolosEntregas(),
                    'identificador' => $this->getIdentificador(),
                    'descricao' => $this->getDescricao(),
                    'id_tipo_protocolo' => $this->getIdTipoProtocolo(),
                    'status' => $this->getStatus(),
                    'data_cad' => $this->getDataCad(),
                    'mes_competencia' => $this->getMesCompetencia(),
                    'ano_competencia' => $this->getAnoCompetencia(),
                ];
            }
            return empty($arr) ? FALSE : $arr;
        } else {
            return FALSE;
        }
    }
    
    public function getProtocolosByCompetencia($mes,$ano){
        $protocolos = FALSE;
        if(!empty($mes) && !empty($ano)){
            $this->setSELECT("B.cnpj,C.descricao,A.identificador as protocolo,
                                DATE_FORMAT(A.data_cad, '%Y-%m-%d') as data,
                                DATE_FORMAT(A.data_cad, '%H-%i') as hora,
                                A.mes_competencia AS competencia");
            $this->setJOIN("rhempresa", "B", "A.id_regiao = B.id_regiao");
            $this->setJOIN("protocolos_tipos", "C", "A.id_tipo_protocolo = C.id_protocolos_tipo");
            $this->setWHERE("A.mes_competencia= '$mes' AND A.ano_competencia = '$ano'");
            
            $rs = $this->setRs();
            $protocolos = $this->api->montaRetorno($rs);
        }
        return $protocolos;
    }

}
