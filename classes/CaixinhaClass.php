<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CaixinhaClass
 *
 * @author Renato
 */

if(file_exists('../classes/LogClass.php')){
    require_once('../classes/LogClass.php');
} else {
    require_once('../../classes/LogClass.php');
}



class CaixinhaClass { 

    protected $id_caixinha;
    protected $id_projeto;
    protected $id_regiao;
    protected $id_unidade;
    protected $id_saida;
    protected $data;
    protected $tipo;
    protected $descricao;
    protected $saldo;
    protected $data_cad;
    protected $user_cad;
    protected $id_grupo;
    protected $id_subgrupo;
    protected $id_tipo;
    protected $status;
    protected $especifica;
    protected $id_item;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' caixinha ';
    protected $WHERE;
    protected $GROUP;
    protected $HAVING;
    protected $ORDER;
    protected $LIMIT;
    protected $rs;
    protected $row;
    protected $numRows;

    function __construct() { 
        
    }

    //GET's DA CLASSE
    function getIdCaixinha() {
        return $this->id_caixinha;
    }

    function getIdProjeto() {
        return $this->id_projeto;
    }

    function getIdRegiao() {
        return $this->id_regiao;
    }

    function getIdUnidade() {
        return $this->id_unidade;
    }

    function getIdSaida() {
        return $this->id_saida;
    }

    function getData($formato = null) {
        if (empty($this->data) || $this->data == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data), $formato) : $this->data;
        }
    }

    function getTipo() {
        return $this->tipo;
    }

    function getDescricao() {
        return $this->descricao;
    }

    function getSaldo() {
        return $this->saldo;
    }

    function getDataCad($formato = null) {
        if (empty($this->data_cad) || $this->data_cad == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_cad), $formato) : $this->data_cad;
        }
    }

    function getUserCad() {
        return $this->user_cad;
    }

    function getIdGrupo() {
        return $this->id_grupo;
    }

    function getIdSubgrupo() {
        return $this->id_subgrupo;
    }

    function getIdTipo() {
        return $this->id_tipo;
    }

    function getStatus() {
        return $this->status;
    }
    
    function getEspecifica() {
        return $this->especifica;
    }
    
    function getIdItem() {
        return $this->id_item;
    }

    //SET's DA CLASSE
    function setIdCaixinha($id_caixinha) {
        $this->id_caixinha = $id_caixinha;
    }

    function setIdProjeto($id_projeto) {
        $this->id_projeto = $id_projeto;
    }

    function setIdRegiao($id_regiao) {
        $this->id_regiao = $id_regiao;
    }

    function setIdUnidade($id_unidade) {
        $this->id_unidade = $id_unidade;
    }

    function setIdSaida($id_saida) {
        $this->id_saida = $id_saida;
    }

    function setData($data) {
        $this->data = $data;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    function setSaldo($saldo) {
        $this->saldo = $saldo;
    }

    function setDataCad($data_cad) {
        $this->data_cad = $data_cad;
    }

    function setUserCad($user_cad) {
        $this->user_cad = $user_cad;
    }

    function setIdGrupo($id_grupo) {
        $this->id_grupo = $id_grupo;
    }

    function setIdSubgrupo($id_subgrupo) {
        $this->id_subgrupo = $id_subgrupo;
    }

    function setIdTipo($id_tipo) {
        $this->id_tipo = $id_tipo;
    }

    function setStatus($status) {
        $this->status = $status;
    }
    
    function setEspecifica($especifica) {
        $esp = explode(" - Reg",$especifica);
        $this->especifica = current($esp);
    }
    
    function setIdItem($id_item) {
        $this->id_item = $id_item;
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

    //SET DEFAULT
    function setDefault() {
        $this->id_caixinha = null;
        $this->id_projeto = null;
        $this->id_regiao = null;
        $this->id_unidade = null;
        $this->id_saida = null;
        $this->data = null;
        $this->tipo = null;
        $this->descricao = null;
        $this->saldo = null;
        $this->data_cad = null;
        $this->user_cad = null;
        $this->id_grupo = null;
        $this->id_subgrupo = null;
        $this->id_tipo = null;
        $this->status = null;
        $this->id_item = null;
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

            $sql = "SELECT $this->SELECT FROM $this->FROM $auxWhere $auxGroup $auxHaving $auxOrder $auxLimit";
        }

        $this->rs = mysql_query($sql);
        $this->numRows = mysql_num_rows($this->rs);
        return $this->rs;
    }

    protected function limpaQuery() {
        $this->setQUERY('');
        $this->setSELECT(' * ');
        $this->setFROM(' caixinha ');
        $this->setWHERE('');
        $this->setGROUP('');
        $this->setHAVING('');
        $this->setORDER('');
        $this->setLIMIT('');
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
            $this->setIdCaixinha($this->row['id_caixinha']);
            $this->setIdProjeto($this->row['id_projeto']);
            $this->setIdRegiao($this->row['id_regiao']);
            $this->setIdUnidade($this->row['id_unidade']);
            $this->setIdSaida($this->row['id_saida']);
            $this->setData($this->row['data']);
            $this->setTipo($this->row['tipo']);
            $this->setDescricao($this->row['descricao']);
            $this->setSaldo($this->row['saldo']);
            $this->setDataCad($this->row['data_cad']);
            $this->setUserCad($this->row['user_cad']);
            $this->setIdGrupo($this->row['id_grupo']);
            $this->setIdSubgrupo($this->row['id_subgrupo']);
            $this->setIdTipo($this->row['id_tipo']);
            $this->setStatus($this->row['status']);
            $this->setEspecifica($this->row['especifica']);
            $this->setIdItem($this->row['id_item']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_projeto' => addslashes($this->getIdProjeto()),
            'id_regiao' => addslashes($this->getIdRegiao()),
            'id_unidade' => addslashes($this->getIdUnidade()),
            'id_saida' => addslashes($this->getIdSaida()),
            'data' => addslashes($this->getData()),
            'tipo' => addslashes($this->getTipo()),
            'descricao' => addslashes($this->getDescricao()),
            'saldo' => addslashes($this->getSaldo()),
            'data_cad' => addslashes($this->getDataCad()),
            'user_cad' => addslashes($this->getUserCad()),
            'id_grupo' => addslashes($this->getIdGrupo()),
            'id_subgrupo' => addslashes($this->getIdSubgrupo()),
            'id_tipo' => addslashes($this->getIdTipo()),
            'status' => addslashes($this->getStatus()),
            'id_item' => addslashes($this->getIdItem()),
        );

        return $array;
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        
        $this->setQUERY("UPDATE caixinha SET " . implode(", ", ($camposUpdate)) . " WHERE id_caixinha = {$this->getIdCaixinha()} LIMIT 1;");

        if ($this->setRs()) {
            $log = new Log();
            $log->gravaLog('Movimentação de Caixa', "Movimentação de Caixa Editada: ID{$this->getIdCaixinha()}");
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

        $this->setQUERY("INSERT INTO caixinha ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdCaixinha(mysql_insert_id());
            $log = new Log();
            $log->gravaLog('Movimentação de Caixa', "Nova Movimentaçao de Caixa: ID".  mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE caixinha SET status = 0 WHERE id_caixinha = {$this->getIdCaixinha()} LIMIT 1;");
        if ($this->setRs()) {
            $log = new Log();
            $log->gravaLog('Movimentação de Caixa', "Movimentação Excluida: ID{$this->getIdCaixinha()}");
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM caixinha WHERE id_caixinha = {$this->getIdCaixinha()} LIMIT 1;");
        if ($this->setRs()) {
            $log = new Log();
            $log->gravaLog('Movimentação de Caixa', "Movimentação Excluida Definitivamente: ID{$this->getIdCaixinha()}");
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function getAllCaixinhas($competencia = null, $id_projeto = null) {
        $this->limpaQuery();
        
//        $auxCompetencia = ($competencia) ? " AND DATE_FORMAT(A.data, '%Y%m') = DATE_FORMAT('{$competencia}', '%Y%m')" : null;
        $auxCompetencia = ($competencia) ? " AND DATE_FORMAT(A.data, '%Y%m%d') = DATE_FORMAT('{$competencia}', '%Y%m%d')" : null;
        $auxProjeto = ($id_projeto > 0) ? " AND A.id_projeto = '{$id_projeto}'" : null;
        
//        $auxProjeto = ($this->getIdProjeto()) ? " AND A.id_projeto = {$this->getIdProjeto()} " : null;
//        $auxUnidade = ($this->getIdUnidade()) ? " AND A.id_unidade = {$this->getIdUnidade()} " : null;
        $auxData = ($this->getData()) ? " AND A.data = '{$this->getData()}' " : null;
        $sql = "SELECT A.*, B.especifica
                FROM caixinha A
                LEFT JOIN saida B ON (A.id_saida = B.id_saida AND A.id_projeto = B.id_projeto AND A.id_tipo = B.tipo AND B.status > 0 AND B.caixinha = 1)
                WHERE A.status = 1 AND A.saldo != '0,00' AND IF(A.id_saida > 0,A.id_saida = B.id_saida,1) $auxProjeto $auxData $auxCompetencia
                ORDER BY A.data, A.id_caixinha";
        $this->setQUERY($sql);
        
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function getSaldoCaixinhasByIdUnidade() {
        $this->limpaQuery();
        $auxSql = "SELECT C.id_caixinha
                FROM caixinha C
                LEFT JOIN (
                    SELECT A.id_saida, B.id_unidade, B.id_tipo, A.especifica
                    FROM saida A
                    INNER JOIN saida_unidade B ON (A.id_saida = B.id_saida AND B.`status` = 1 AND B.valor > 0)
                    WHERE A.status > 0 AND A.caixinha = 1) D ON (C.id_saida = D.id_saida AND C.id_unidade = D.id_unidade AND C.id_tipo = D.id_tipo)
                WHERE C.status = 1 AND C.saldo != '0,00' AND IF(C.tipo = 2,C.id_saida = D.id_saida,1)";
        
        $auxIdCaixinha = ($this->getIdCaixinha()) ? " AND id_caixinha NOT IN ({$this->getIdCaixinha()})" : null;
        $sql = "SELECT SUM(IF(tipo = 2, saldo, -saldo)) saldoUnidade FROM caixinha WHERE id_unidade = '{$this->getIdUnidade()}' AND id_tipo = '{$this->getIdTipo()}' AND status = 1 AND id_caixinha IN ($auxSql) $auxIdCaixinha ORDER BY data;";
        $qry = mysql_query($sql);
        if ($qry) {
            $row = mysql_fetch_assoc($qry);
            return $row['saldoUnidade'];
        } else {
            die(mysql_error());
        }
    }

    public function getSaldoCaixinhasByIdProjeto() {
        $this->limpaQuery();
        $auxSql = "SELECT A.id_caixinha
                FROM caixinha A
                LEFT JOIN saida B ON (A.id_saida = B.id_saida AND A.id_projeto = A.id_projeto AND A.id_tipo = B.tipo AND B.status > 0 AND B.caixinha = 1)
                WHERE A.status = 1 AND A.saldo != '0,00' AND IF(A.tipo = 2,A.id_saida = B.id_saida,1)";
        
        $auxIdCaixinha = ($this->getIdCaixinha()) ? " AND id_caixinha NOT IN ({$this->getIdCaixinha()})" : null;
        $sql = "SELECT SUM(IF(tipo = 2, saldo, -saldo)) saldoUnidade FROM caixinha WHERE id_projeto = '{$this->getIdProjeto()}' AND id_tipo = '{$this->getIdTipo()}' AND status = 1 AND id_caixinha IN ($auxSql) $auxIdCaixinha ORDER BY data;";
        $qry = mysql_query($sql);
        if ($qry) {
            $row = mysql_fetch_assoc($qry);
            return $row['saldoUnidade'];
        } else {
            die(mysql_error());
        }
    }

    public function getSaldoCaixinhasByMes($competencia = null, $anterior = false) {
        $this->limpaQuery();
        if(!$anterior) {
//            $auxCompetencia = ($competencia) ? " AND DATE_FORMAT(A.data, '%Y%m') <= DATE_FORMAT('{$competencia}', '%Y%m')" : null;
            $auxCompetencia = ($competencia) ? " AND DATE_FORMAT(A.data, '%Y%m%d') <= DATE_FORMAT('{$competencia}', '%Y%m%d')" : null;
        } else {
//            $auxCompetencia = ($competencia) ? " AND DATE_FORMAT(A.data, '%Y%m') <= DATE_FORMAT(ADDDATE('{$competencia}', INTERVAL -1 MONTH), '%Y%m')" : null;
            $auxCompetencia = ($competencia) ? " AND DATE_FORMAT(A.data, '%Y%m%d') <= DATE_FORMAT(ADDDATE('{$competencia}', INTERVAL -1 DAY), '%Y%m%d')" : null;
        }
        $auxProjeto = ($this->getIdProjeto() > 0) ? " AND A.id_projeto = '{$this->getIdProjeto()}'" : null;
        
        $auxSql = "SELECT A.id_caixinha
                FROM caixinha A
                LEFT JOIN saida B ON (A.id_saida = B.id_saida AND B.status > 0 AND B.caixinha = 1)
                WHERE A.status = 1 AND A.saldo != '0,00' AND IF(A.id_saida > 0,A.id_saida = B.id_saida,1) $auxProjeto $auxCompetencia";
        
        $auxIdCaixinha = ($this->getIdCaixinha()) ? " AND id_caixinha NOT IN ({$this->getIdCaixinha()})" : null;
        $sql = "SELECT SUM(IF(tipo = 2, saldo, -saldo)) saldoUnidade FROM caixinha WHERE status = 1 AND id_caixinha IN ($auxSql) $auxIdCaixinha ORDER BY data;";
        $qry = mysql_query($sql);
        if ($qry) {
            $row = mysql_fetch_assoc($qry);
            return $row['saldoUnidade'];
        } else {
            die(mysql_error());
        }
    }

    public function getById() {
        $this->limpaQuery();
        $this->setWHERE("id_caixinha = {$this->getIdCaixinha()}");
        $this->setLIMIT(1);
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function getTipoSaida() {
        $sql = "SELECT CONCAT(cod, ' ', nome) nome FROM entradaesaida A WHERE A.id_entradasaida = {$this->getIdTipo()}";
        $sql = mysql_query($sql);
        return mysql_result($sql, 0);
    }

    public function getGrupoSaida() {
        $sql = "SELECT nome FROM entradaesaida_grupo A WHERE A.id_grupo = {$this->getIdGrupo()}";
        $sql = mysql_query($sql);
        return mysql_result($sql, 0);
    }

    public function getSubGrupoSaida() {
        $sql = "SELECT CONCAT(id_subgrupo, ' ', nome) nome FROM entradaesaida_subgrupo A WHERE A.id = {$this->getIdSubgrupo()}";
        $sql = mysql_query($sql);
        return mysql_result($sql, 0);
    }

    public function getAvisoSaidaCaixinhaSemUnidade() {
        $sql = "SELECT A.id_saida, A.nome FROM saida A LEFT JOIN saida_unidade B ON(A.id_saida = B.id_saida) WHERE A.status > 0 AND A.caixinha = 1 GROUP BY A.id_saida HAVING COUNT(B.id_assoc) = 0 ORDER BY A.id_saida;";
        $qry = mysql_query($sql);
        if (mysql_num_rows($qry) > 0) {
            echo "<div class='alert alert-warning'>";
            echo "<legend>Saídas caixinha sem unidade cadastrada!</legend>";
            while($row = mysql_fetch_assoc($qry)){
                echo "<p><a href='../form_saida.php?id_saida={$row['id_saida']}' target='_blank'><b>{$row['id_saida']} <i class='fa fa-arrow-right'></i> {$row['nome']}</b></a></p>";
            }
            echo '</div>';
        } else {
            die(mysql_error());
        }
    }

    public function getAvisoSaidaCaixinhaSemProjeto() {
        $sql = "SELECT A.id_saida, A.nome FROM saida A WHERE A.status > 0 AND A.caixinha = 1 AND id_projeto = 0 ORDER BY A.id_saida;";
        $qry = mysql_query($sql);
        if (mysql_num_rows($qry) > 0) {
            echo "<div class='alert alert-warning'>";
            echo "<legend>Saídas caixinha sem unidade cadastrada!</legend>";
            while($row = mysql_fetch_assoc($qry)){
                echo "<p><a href='../form_saida.php?id_saida={$row['id_saida']}' target='_blank'><b>{$row['id_saida']} <i class='fa fa-arrow-right'></i> {$row['nome']}</b></a></p>";
            }
            echo '</div>';
        } else {
//            die(mysql_error());
        }
    }
    
    public function getItensDespesas() {
        $sql = "SELECT * FROM itens_despesas WHERE status = 1 ORDER BY nome";
        $qry = mysql_query($sql) or die(mysql_error());
        $dados[''] = 'SELECIONE';
        while($row = mysql_fetch_assoc($qry)){
            $dados[$row['id']] = $row['nome'];
        }
        return $dados;
    }
    
    public function getItensDespesasNome() {
        $sql = "SELECT nome FROM itens_despesas WHERE status = 1 AND id = '{$this->getIdItem()}' ORDER BY nome";
        $qry = mysql_query($sql) or die(mysql_error());
        $row = mysql_fetch_assoc($qry);
        return $row['nome'];
    }
}