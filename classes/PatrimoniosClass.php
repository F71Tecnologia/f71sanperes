<?php
class PatrimoniosClass {

    protected $id_patrimonio;
    protected $id_projeto;
    protected $data;
    protected $user;
    protected $numero;
    protected $numero_serie;
    protected $nome;
    protected $descricao;
    protected $data_aquisicao;
    protected $data_acerto;
    protected $data_cadastro;
    protected $data_contabilizacao;
    protected $data_vistoria;
    protected $data_marcacao;
    protected $data_baixa;
    protected $vencimento_garantia;
    protected $n_nf;
    protected $chave_nfs;
    protected $valor_original;
    protected $valor_compra;
    protected $valor_atualizado;
    protected $valor_baixa;
    protected $status;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' patrimonios ';
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
    function getIdPatrimonio() {
        return $this->id_patrimonio;
    }

    function getIdProjeto() {
        return $this->id_projeto;
    }

    function getData($formato = null) {
        if (empty($this->data) || $this->data == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data), $formato) : $this->data;
        }
    }

    function getUser() {
        return $this->user;
    }

    function getNumero() {
        return $this->numero;
    }

    function getNumeroSerie() {
        return $this->numero_serie;
    }

    function getNome() {
        return $this->nome;
    }

    function getDescricao() {
        return $this->descricao;
    }

    function getDataAquisicao($formato = null) {
        if (empty($this->data_aquisicao) || $this->data_aquisicao == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_aquisicao), $formato) : $this->data_aquisicao;
        }
    }

    function getDataAcerto($formato = null) {
        if (empty($this->data_acerto) || $this->data_acerto == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_acerto), $formato) : $this->data_acerto;
        }
    }

    function getDataCadastro($formato = null) {
        if (empty($this->data_cadastro) || $this->data_cadastro == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_cadastro), $formato) : $this->data_cadastro;
        }
    }

    function getDataContabilizacao($formato = null) {
        if (empty($this->data_contabilizacao) || $this->data_contabilizacao == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_contabilizacao), $formato) : $this->data_contabilizacao;
        }
    }

    function getDataVistoria($formato = null) {
        if (empty($this->data_vistoria) || $this->data_vistoria == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_vistoria), $formato) : $this->data_vistoria;
        }
    }

    function getDataMarcacao($formato = null) {
        if (empty($this->data_marcacao) || $this->data_marcacao == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_marcacao), $formato) : $this->data_marcacao;
        }
    }

    function getDataBaixa($formato = null) {
        if (empty($this->data_baixa) || $this->data_baixa == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_baixa), $formato) : $this->data_baixa;
        }
    }

    function getVencimentoGarantia($formato = null) {
        if (empty($this->vencimento_garantia) || $this->vencimento_garantia == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->vencimento_garantia), $formato) : $this->vencimento_garantia;
        }
    }

    function getNNf() {
        return $this->n_nf;
    }

    function getChaveNfs() {
        return $this->chave_nfs;
    }

    function getValorOriginal() {
        return $this->valor_original;
    }

    function getValorCompra() {
        return $this->valor_compra;
    }

    function getValorAtualizado() {
        return $this->valor_atualizado;
    }

    function getValorBaixa() {
        return $this->valor_baixa;
    }

    function getStatus() {
        return $this->status;
    }

    //SET's DA CLASSE
    function setIdPatrimonio($id_patrimonio) {
        $this->id_patrimonio = $id_patrimonio;
    }

    function setIdProjeto($id_projeto) {
        $this->id_projeto = $id_projeto;
    }

    function setData($data) {
        $this->data = $data;
    }

    function setUser($user) {
        $this->user = $user;
    }

    function setNumero($numero) {
        $this->numero = $numero;
    }

    function setNumeroSerie($numero_serie) {
        $this->numero_serie = $numero_serie;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

    function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    function setDataAquisicao($data_aquisicao) {
        $this->data_aquisicao = $data_aquisicao;
    }

    function setDataAcerto($data_acerto) {
        $this->data_acerto = $data_acerto;
    }

    function setDataCadastro($data_cadastro) {
        $this->data_cadastro = $data_cadastro;
    }

    function setDataContabilizacao($data_contabilizacao) {
        $this->data_contabilizacao = $data_contabilizacao;
    }

    function setDataVistoria($data_vistoria) {
        $this->data_vistoria = $data_vistoria;
    }

    function setDataMarcacao($data_marcacao) {
        $this->data_marcacao = $data_marcacao;
    }

    function setDataBaixa($data_baixa) {
        $this->data_baixa = $data_baixa;
    }

    function setVencimentoGarantia($vencimento_garantia) {
        $this->vencimento_garantia = $vencimento_garantia;
    }

    function setNNf($n_nf) {
        $this->n_nf = $n_nf;
    }

    function setChaveNfs($chave_nfs) {
        $this->chave_nfs = $chave_nfs;
    }

    function setValorOriginal($valor_original) {
        $this->valor_original = $valor_original;
    }

    function setValorCompra($valor_compra) {
        $this->valor_compra = $valor_compra;
    }

    function setValorAtualizado($valor_atualizado) {
        $this->valor_atualizado = $valor_atualizado;
    }

    function setValorBaixa($valor_baixa) {
        $this->valor_baixa = $valor_baixa;
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
        $this->id_patrimonio = null;
        $this->id_projeto = null;
        $this->data = null;
        $this->user = null;
        $this->numero = null;
        $this->numero_serie = null;
        $this->nome = null;
        $this->descricao = null;
        $this->data_aquisicao = null;
        $this->data_acerto = null;
        $this->data_cadastro = null;
        $this->data_contabilizacao = null;
        $this->data_vistoria = null;
        $this->data_marcacao = null;
        $this->data_baixa = null;
        $this->vencimento_garantia = null;
        $this->n_nf = null;
        $this->chave_nfs = null;
        $this->valor_original = null;
        $this->valor_compra = null;
        $this->valor_atualizado = null;
        $this->valor_baixa = null;
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

            $sql = "SELECT $this->SELECT FROM $this->FROM $auxWhere $auxGroup $auxHaving $auxOrder $auxLimit";
        }

        $this->rs = mysql_query($sql);
        $this->numRows = mysql_num_rows($this->rs);
        return $this->rs;
    }

    protected function limpaQuery() {
        $this->setQUERY('');
        $this->setSELECT(' * ');
        $this->setFROM(' patrimonios ');
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
            $this->setIdPatrimonio($this->row['id_patrimonio']);
            $this->setIdProjeto($this->row['id_projeto']);
            $this->setData($this->row['data']);
            $this->setUser($this->row['user']);
            $this->setNumero($this->row['numero']);
            $this->setNumeroSerie($this->row['numero_serie']);
            $this->setNome($this->row['nome']);
            $this->setDescricao($this->row['descricao']);
            $this->setDataAquisicao($this->row['data_aquisicao']);
            $this->setDataAcerto($this->row['data_acerto']);
            $this->setDataCadastro($this->row['data_cadastro']);
            $this->setDataContabilizacao($this->row['data_contabilizacao']);
            $this->setDataVistoria($this->row['data_vistoria']);
            $this->setDataMarcacao($this->row['data_marcacao']);
            $this->setDataBaixa($this->row['data_baixa']);
            $this->setVencimentoGarantia($this->row['vencimento_garantia']);
            $this->setNNf($this->row['n_nf']);
            $this->setChaveNfs($this->row['chave_nfs']);
            $this->setValorOriginal($this->row['valor_original']);
            $this->setValorCompra($this->row['valor_compra']);
            $this->setValorAtualizado($this->row['valor_atualizado']);
            $this->setValorBaixa($this->row['valor_baixa']);
            $this->setStatus($this->row['status']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_projeto' => addslashes($this->getIdProjeto()),
            'data' => addslashes($this->getData()),
            'user' => addslashes($this->getUser()),
            'numero' => addslashes($this->getNumero()),
            'numero_serie' => addslashes($this->getNumeroSerie()),
            'nome' => addslashes($this->getNome()),
            'descricao' => addslashes($this->getDescricao()),
            'data_aquisicao' => addslashes($this->getDataAquisicao()),
            'data_acerto' => addslashes($this->getDataAcerto()),
            'data_cadastro' => addslashes($this->getDataCadastro()),
            'data_contabilizacao' => addslashes($this->getDataContabilizacao()),
            'data_vistoria' => addslashes($this->getDataVistoria()),
            'data_marcacao' => addslashes($this->getDataMarcacao()),
            'data_baixa' => addslashes($this->getDataBaixa()),
            'vencimento_garantia' => addslashes($this->getVencimentoGarantia()),
            'n_nf' => addslashes($this->getNNf()),
            'chave_nfs' => addslashes($this->getChaveNfs()),
            'valor_original' => addslashes($this->getValorOriginal()),
            'valor_compra' => addslashes($this->getValorCompra()),
            'valor_atualizado' => addslashes($this->getValorAtualizado()),
            'valor_baixa' => addslashes($this->getValorBaixa()),
            'status' => addslashes($this->getStatus()),
        );

        return $array;
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE patrimonios SET " . implode(", ", ($camposUpdate)) . " WHERE id_patrimonio = {$this->getIdPatrimonio()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO patrimonios ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdPatrimonio(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE patrimonios SET status = 0 WHERE id_patrimonio = {$this->getIdPatrimonio()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM patrimonios WHERE id_patrimonio = {$this->getIdPatrimonio()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function getById() {
        $this->limpaQuery();

        $this->setQUERY("SELECT * FROM patrimonios WHERE id_patrimonio = {$this->getIdPatrimonio()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function getPatrimoniosByProjeto() {
        $this->limpaQuery();

        $this->setWHERE("id_projeto = {$this->getIdProjeto()} AND status = 1");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }
}