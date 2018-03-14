<?php

/*
 * Data Criação: 21/05/2015
 * Desenvolvimento: Renato
 * Versão: 0.1 (Build 00001)
 * 
 * Obs sobre a versão: 
 * 
 */

class PrestadorServicoClass {

//class PrestadorServicoClass {

    protected $id_prestador;
    protected $id_regiao;
    protected $id_projeto;
    protected $id_medida;
    protected $aberto_por;
    protected $aberto_em;
    protected $contratado_por;
    protected $contratado_em;
    protected $encerrado_por;
    protected $encerrado_em;
    protected $contratante;
    protected $numero;
    protected $endereco;
    protected $cnpj;
    protected $responsavel;
    protected $civil;
    protected $nacionalidade;
    protected $formacao;
    protected $rg;
    protected $cpf;
    protected $c_fantasia;
    protected $c_razao;
    protected $c_endereco;
    protected $c_cnpj;
    protected $c_ie;
    protected $c_im;
    protected $c_tel;
    protected $c_fax;
    protected $c_email;
    protected $c_responsavel;
    protected $c_civil;
    protected $c_nacionalidade;
    protected $c_formacao;
    protected $c_rg;
    protected $c_cpf;
    protected $c_email2;
    protected $c_site;
    protected $co_responsavel;
    protected $co_tel;
    protected $co_fax;
    protected $co_civil;
    protected $co_nacionalidade;
    protected $co_email;
    protected $co_municipio;
    protected $assunto;
    protected $objeto;
    protected $especificacao;
    protected $valor;
    protected $data;
    protected $data_proc;
    protected $acompanhamento;
    protected $imprimir;
    protected $status;
    protected $prestador_tipo;
    protected $c_data_nascimento;
    protected $co_responsavel_socio1;
    protected $co_tel_socio1;
    protected $co_fax_socio1;
    protected $co_civil_socio1;
    protected $co_nacionalidade_socio1;
    protected $co_email_socio1;
    protected $co_municipio_socio1;
    protected $data_nasc_socio1;
    protected $co_responsavel_socio2;
    protected $co_tel_socio2;
    protected $co_fax_socio2;
    protected $co_civil_socio2;
    protected $co_nacionalidade_socio2;
    protected $co_email_socio2;
    protected $co_municipio_socio2;
    protected $data_nasc_socio2;
    protected $nome_banco;
    protected $agencia;
    protected $conta;
    protected $valor_limite;
    protected $id_compra;
    protected $prestacao_contas;
    protected $especialidade;
    protected $c_cep;
    protected $c_id_tp_logradouro;
    protected $c_numero;
    protected $c_complemento;
    protected $c_bairro;
    protected $c_uf;
    protected $c_cod_cidade;
    protected $QUERY;
    protected $SELECT = " * ";
    protected $FROM = " prestadorservico ";
    protected $WHERE;
    protected $GROUP;
    protected $HAVING;
    protected $ORDER;
    protected $LIMIT;
    protected $rsPrestador;
    protected $rowPrestador;
    protected $numRowsPrestador;
    protected $id_contabil_empresa;
    protected $id_cnae;
    
    function __construct() {
        
    }

    function getIdContabilEmpresa() {
        return $this->id_contabil_empresa;
    }

    function getId_prestador() {
        return $this->id_prestador;
    }

    function getId_regiao() {
        return $this->id_regiao;
    }

    function getId_projeto() {
        return $this->id_projeto;
    }

    function getId_medida() {
        return $this->id_medida;
    }

    function getAberto_por() {
        return $this->aberto_por;
    }

    function getAberto_em($formato) {
        if (empty($this->aberto_em) || $this->aberto_em == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->aberto_em), $formato) : $this->aberto_em;
        }
    }

    function getContratado_por() {
        return $this->contratado_por;
    }

    function getContratado_em($formato) {
        if (empty($this->contratado_em) || $this->contratado_em == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->contratado_em), $formato) : $this->contratado_em;
        }
    }

    function getEncerrado_por() {
        return $this->encerrado_por;
    }

    function getEncerrado_em($formato) {
        if (empty($this->encerrado_em) || $this->encerrado_em == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->encerrado_em), $formato) : $this->encerrado_em;
        }
    }

    function getContratante() {
        return $this->contratante;
    }

    function getNumero() {
        return $this->numero;
    }

    function getEndereco() {
        return $this->endereco;
    }

    function getCnpj() {
        return $this->cnpj;
    }

    function getResponsavel() {
        return $this->responsavel;
    }

    function getCivil() {
        return $this->civil;
    }

    function getNacionalidade() {
        return $this->nacionalidade;
    }

    function getFormacao() {
        return $this->formacao;
    }

    function getRg() {
        return $this->rg;
    }

    function getCpf() {
        return $this->cpf;
    }

    function getC_fantasia() {
        return $this->c_fantasia;
    }

    function getC_razao() {
        return $this->c_razao;
    }

    function getC_endereco() {
        return $this->c_endereco;
    }

    function getC_cnpj() {
        return $this->c_cnpj;
    }

    function getC_ie() {
        return $this->c_ie;
    }

    function getC_im() {
        return $this->c_im;
    }

    function getC_tel() {
        return $this->c_tel;
    }

    function getC_fax() {
        return $this->c_fax;
    }

    function getC_email() {
        return $this->c_email;
    }

    function getC_responsavel() {
        return $this->c_responsavel;
    }

    function getC_civil() {
        return $this->c_civil;
    }

    function getC_nacionalidade() {
        return $this->c_nacionalidade;
    }

    function getC_formacao() {
        return $this->c_formacao;
    }

    function getC_rg() {
        return $this->c_rg;
    }

    function getC_cpf() {
        return $this->c_cpf;
    }

    function getC_email2() {
        return $this->c_email2;
    }

    function getC_site() {
        return $this->c_site;
    }

    function getCo_responsavel() {
        return $this->co_responsavel;
    }

    function getCo_tel() {
        return $this->co_tel;
    }

    function getCo_fax() {
        return $this->co_fax;
    }

    function getCo_civil() {
        return $this->co_civil;
    }

    function getCo_nacionalidade() {
        return $this->co_nacionalidade;
    }

    function getCo_email() {
        return $this->co_email;
    }

    function getCo_municipio() {
        return $this->co_municipio;
    }

    function getAssunto() {
        return $this->assunto;
    }

    function getObjeto() {
        return $this->objeto;
    }

    function getEspecificacao() {
        return $this->especificacao;
    }

    function getValor($formatoBr = FALSE) {
        return ($formatoBr) ? number_format(str_replace(",", ".", str_replace(".", "", $this->valor)), 2, ',', '.') : $this->valor;
    }

    function getData($formato) {
        if (empty($this->data) || $this->data == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data), $formato) : $this->data;
        }
    }

    function getData_proc() {
        return $this->data_proc;
    }

    function getAcompanhamento() {
        return $this->acompanhamento;
    }

    function getImprimir() {
        return $this->imprimir;
    }

    function getStatus() {
        return $this->status;
    }

    function getPrestador_tipo() {
        return $this->prestador_tipo;
    }

    function getC_data_nascimento($formato) {
        if (empty($this->c_data_nascimento) || $this->c_data_nascimento == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->c_data_nascimento), $formato) : $this->c_data_nascimento;
        }
    }

    function getCo_responsavel_socio1() {
        return $this->co_responsavel_socio1;
    }

    function getCo_tel_socio1() {
        return $this->co_tel_socio1;
    }

    function getCo_fax_socio1() {
        return $this->co_fax_socio1;
    }

    function getCo_civil_socio1() {
        return $this->co_civil_socio1;
    }

    function getCo_nacionalidade_socio1() {
        return $this->co_nacionalidade_socio1;
    }

    function getCo_email_socio1() {
        return $this->co_email_socio1;
    }

    function getCo_municipio_socio1() {
        return $this->co_municipio_socio1;
    }

    function getData_nasc_socio1($formato) {
        if (empty($this->data_nasc_socio1) || $this->data_nasc_socio1 == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_nasc_socio1), $formato) : $this->data_nasc_socio1;
        }
    }

    function getCo_responsavel_socio2() {
        return $this->co_responsavel_socio2;
    }

    function getCo_tel_socio2() {
        return $this->co_tel_socio2;
    }

    function getCo_fax_socio2() {
        return $this->co_fax_socio2;
    }

    function getCo_civil_socio2() {
        return $this->co_civil_socio2;
    }

    function getCo_nacionalidade_socio2() {
        return $this->co_nacionalidade_socio2;
    }

    function getCo_email_socio2() {
        return $this->co_email_socio2;
    }

    function getCo_municipio_socio2() {
        return $this->co_municipio_socio2;
    }

    function getData_nasc_socio2($formato) {
        if (empty($this->data_nasc_socio2) || $this->data_nasc_socio2 == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_nasc_socio2), $formato) : $this->data_nasc_socio2;
        }
    }

    function getNome_banco() {
        return $this->nome_banco;
    }

    function getAgencia() {
        return $this->agencia;
    }

    function getConta() {
        return $this->conta;
    }

    function getValor_limite($formatoBr = FALSE) {
        return ($formatoBr) ? number_format(str_replace(",", ".", $this->valor_limite), 2, ',', '.') : $this->valor_limite;
    }

    function getId_compra() {
        return $this->id_compra;
    }

    function getPrestacao_contas() {
        return $this->prestacao_contas;
    }

    function getEspecialidade() {
        return $this->especialidade;
    }

    function getC_cep() {
        return $this->c_cep;
    }

    function getC_id_tp_logradouro() {
        return $this->c_id_tp_logradouro;
    }

    function getC_numero() {
        return $this->c_numero;
    }

    function getC_complemento() {
        return $this->c_complemento;
    }

    function getC_bairro() {
        return $this->c_bairro;
    }

    function getC_uf() {
        return $this->c_uf;
    }

    function getC_cod_cidade() {
        return $this->c_cod_cidade;
    }
    
    function getId_cnae() {
        return $this->id_cnae;
    }

    function setId_cnae($id_cnae) {
        $this->id_cnae = $id_cnae;
    }
    
    function setId_prestador($id_prestador) {
        $this->id_prestador = $id_prestador;
    }

    function setId_regiao($id_regiao) {
        $this->id_regiao = $id_regiao;
    }

    function setId_projeto($id_projeto) {
        $this->id_projeto = $id_projeto;
    }

    function setId_medida($id_medida) {
        $this->id_medida = $id_medida;
    }

    function setAberto_por($aberto_por) {
        $this->aberto_por = $aberto_por;
    }

    function setAberto_em($aberto_em) {
        $this->aberto_em = $aberto_em;
    }

    function setContratado_por($contratado_por) {
        $this->contratado_por = $contratado_por;
    }

    function setContratado_em($contratado_em) {
        $this->contratado_em = $contratado_em;
    }

    function setEncerrado_por($encerrado_por) {
        $this->encerrado_por = $encerrado_por;
    }

    function setEncerrado_em($encerrado_em) {
        $this->encerrado_em = $encerrado_em;
    }

    function setContratante($contratante) {
        $this->contratante = $contratante;
    }

    function setNumero($numero) {
        $this->numero = $numero;
    }

    function setEndereco($endereco) {
        $this->endereco = $endereco;
    }

    function setCnpj($cnpj) {
        $this->cnpj = $cnpj;
    }

    function setResponsavel($responsavel) {
        $this->responsavel = $responsavel;
    }

    function setCivil($civil) {
        $this->civil = $civil;
    }

    function setNacionalidade($nacionalidade) {
        $this->nacionalidade = $nacionalidade;
    }

    function setFormacao($formacao) {
        $this->formacao = $formacao;
    }

    function setRg($rg) {
        $this->rg = $rg;
    }

    function setCpf($cpf) {
        $this->cpf = $cpf;
    }

    function setC_fantasia($c_fantasia) {
        $this->c_fantasia = $c_fantasia;
    }

    function setC_razao($c_razao) {
        $this->c_razao = $c_razao;
    }

    function setC_endereco($c_endereco) {
        $this->c_endereco = $c_endereco;
    }

    function setC_cnpj($c_cnpj) {
        $this->c_cnpj = $c_cnpj;
    }

    function setC_ie($c_ie) {
        $this->c_ie = $c_ie;
    }

    function setC_im($c_im) {
        $this->c_im = $c_im;
    }

    function setC_tel($c_tel) {
        $this->c_tel = $c_tel;
    }

    function setC_fax($c_fax) {
        $this->c_fax = $c_fax;
    }

    function setC_email($c_email) {
        $this->c_email = $c_email;
    }

    function setC_responsavel($c_responsavel) {
        $this->c_responsavel = $c_responsavel;
    }

    function setC_civil($c_civil) {
        $this->c_civil = $c_civil;
    }

    function setC_nacionalidade($c_nacionalidade) {
        $this->c_nacionalidade = $c_nacionalidade;
    }

    function setC_formacao($c_formacao) {
        $this->c_formacao = $c_formacao;
    }

    function setC_rg($c_rg) {
        $this->c_rg = $c_rg;
    }

    function setC_cpf($c_cpf) {
        $this->c_cpf = $c_cpf;
    }

    function setC_email2($c_email2) {
        $this->c_email2 = $c_email2;
    }

    function setC_site($c_site) {
        $this->c_site = $c_site;
    }

    function setCo_responsavel($co_responsavel) {
        $this->co_responsavel = $co_responsavel;
    }

    function setCo_tel($co_tel) {
        $this->co_tel = $co_tel;
    }

    function setCo_fax($co_fax) {
        $this->co_fax = $co_fax;
    }

    function setCo_civil($co_civil) {
        $this->co_civil = $co_civil;
    }

    function setCo_nacionalidade($co_nacionalidade) {
        $this->co_nacionalidade = $co_nacionalidade;
    }

    function setCo_email($co_email) {
        $this->co_email = $co_email;
    }

    function setCo_municipio($co_municipio) {
        $this->co_municipio = $co_municipio;
    }

    function setAssunto($assunto) {
        $this->assunto = $assunto;
    }

    function setObjeto($objeto) {
        $this->objeto = $objeto;
    }

    function setEspecificacao($especificacao) {
        $this->especificacao = $especificacao;
    }

    function setValor($valor) {
        $this->valor = $valor;
    }

    function setData($data) {
        $this->data = $data;
    }

    function setData_proc($data_proc) {
        $this->data_proc = $data_proc;
    }

    function setAcompanhamento($acompanhamento) {
        $this->acompanhamento = $acompanhamento;
    }

    function setImprimir($imprimir) {
        $this->imprimir = $imprimir;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setPrestador_tipo($prestador_tipo) {
        $this->prestador_tipo = $prestador_tipo;
    }

    function setC_data_nascimento($c_data_nascimento) {
        $this->c_data_nascimento = $c_data_nascimento;
    }

    function setCo_responsavel_socio1($co_responsavel_socio1) {
        $this->co_responsavel_socio1 = $co_responsavel_socio1;
    }

    function setCo_tel_socio1($co_tel_socio1) {
        $this->co_tel_socio1 = $co_tel_socio1;
    }

    function setCo_fax_socio1($co_fax_socio1) {
        $this->co_fax_socio1 = $co_fax_socio1;
    }

    function setCo_civil_socio1($co_civil_socio1) {
        $this->co_civil_socio1 = $co_civil_socio1;
    }

    function setCo_nacionalidade_socio1($co_nacionalidade_socio1) {
        $this->co_nacionalidade_socio1 = $co_nacionalidade_socio1;
    }

    function setCo_email_socio1($co_email_socio1) {
        $this->co_email_socio1 = $co_email_socio1;
    }

    function setCo_municipio_socio1($co_municipio_socio1) {
        $this->co_municipio_socio1 = $co_municipio_socio1;
    }

    function setData_nasc_socio1($data_nasc_socio1) {
        $this->data_nasc_socio1 = $data_nasc_socio1;
    }

    function setCo_responsavel_socio2($co_responsavel_socio2) {
        $this->co_responsavel_socio2 = $co_responsavel_socio2;
    }

    function setCo_tel_socio2($co_tel_socio2) {
        $this->co_tel_socio2 = $co_tel_socio2;
    }

    function setCo_fax_socio2($co_fax_socio2) {
        $this->co_fax_socio2 = $co_fax_socio2;
    }

    function setCo_civil_socio2($co_civil_socio2) {
        $this->co_civil_socio2 = $co_civil_socio2;
    }

    function setCo_nacionalidade_socio2($co_nacionalidade_socio2) {
        $this->co_nacionalidade_socio2 = $co_nacionalidade_socio2;
    }

    function setCo_email_socio2($co_email_socio2) {
        $this->co_email_socio2 = $co_email_socio2;
    }

    function setCo_municipio_socio2($co_municipio_socio2) {
        $this->co_municipio_socio2 = $co_municipio_socio2;
    }

    function setData_nasc_socio2($data_nasc_socio2) {
        $this->data_nasc_socio2 = $data_nasc_socio2;
    }

    function setNome_banco($nome_banco) {
        $this->nome_banco = $nome_banco;
    }

    function setAgencia($agencia) {
        $this->agencia = $agencia;
    }

    function setConta($conta) {
        $this->conta = $conta;
    }

    function setValor_limite($valor_limite) {
        $this->valor_limite = $valor_limite;
    }

    function setId_compra($id_compra) {
        $this->id_compra = $id_compra;
    }

    function setPrestacao_contas($prestacao_contas) {
        $this->prestacao_contas = $prestacao_contas;
    }

    function setEspecialidade($especialidade) {
        $this->especialidade = $especialidade;
    }

    function setC_cep($c_cep) {
        $this->c_cep = $c_cep;
    }

    function setC_id_tp_logradouro($c_id_tp_logradouro) {
        $this->c_id_tp_logradouro = $c_id_tp_logradouro;
    }

    function setC_numero($c_numero) {
        $this->c_numero = $c_numero;
    }

    function setC_complemento($c_complemento) {
        $this->c_complemento = $c_complemento;
    }

    function setC_bairro($c_bairro) {
        $this->c_bairro = $c_bairro;
    }

    function setC_uf($c_uf) {
        $this->c_uf = $c_uf;
    }

    function setC_cod_cidade($c_cod_cidade) {
        $this->c_cod_cidade = $c_cod_cidade;
    }

    public function setIdContabilEmpresa($id_contabil_empresa) {
        $this->id_contabil_empresa = $id_contabil_empresa;
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

    protected function setRsPrestador($valor) {
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
//        echo $sql;
        $this->rsPrestador = mysql_query($sql);
        $this->numRowsPrestador = mysql_num_rows($this->rsPrestador);
        return $this->rsPrestador;
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

    public function getNumRowsPrestador() {
        return $this->numRowsPrestador;
    }

    protected function setRowPrestador($valor) {
        return $this->rowPrestador = mysql_fetch_assoc($valor);
    }

    public function getRowPrestador() {

        if ($this->setRowPrestador($this->rsPrestador)) {
            $this->setId_prestador($this->rowPrestador["id_prestador"]);
            $this->setId_regiao($this->rowPrestador["id_regiao"]);
            $this->setId_projeto($this->rowPrestador["id_projeto"]);
            $this->setId_medida($this->rowPrestador["id_medida"]);
            $this->setAberto_por($this->rowPrestador["aberto_por"]);
            $this->setAberto_em($this->rowPrestador["aberto_em"]);
            $this->setContratado_por($this->rowPrestador["contratado_por"]);
            $this->setContratado_em($this->rowPrestador["contratado_em"]);
            $this->setEncerrado_por($this->rowPrestador["encerrado_por"]);
            $this->setEncerrado_em($this->rowPrestador["encerrado_em"]);
            $this->setContratante($this->rowPrestador["contratante"]);
            $this->setNumero($this->rowPrestador["numero"]);
            $this->setEndereco($this->rowPrestador["endereco"]);
            $this->setCnpj($this->rowPrestador["cnpj"]);
            $this->setResponsavel($this->rowPrestador["responsavel"]);
            $this->setCivil($this->rowPrestador["civil"]);
            $this->setNacionalidade($this->rowPrestador["nacionalidade"]);
            $this->setFormacao($this->rowPrestador["formacao"]);
            $this->setRg($this->rowPrestador["rg"]);
            $this->setCpf($this->rowPrestador["cpf"]);
            $this->setC_fantasia($this->rowPrestador["c_fantasia"]);
            $this->setC_razao($this->rowPrestador["c_razao"]);
            $this->setC_endereco($this->rowPrestador["c_endereco"]);
            $this->setC_cnpj($this->rowPrestador["c_cnpj"]);
            $this->setC_ie($this->rowPrestador["c_ie"]);
            $this->setC_im($this->rowPrestador["c_im"]);
            $this->setC_tel($this->rowPrestador["c_tel"]);
            $this->setC_fax($this->rowPrestador["c_fax"]);
            $this->setC_email($this->rowPrestador["c_email"]);
            $this->setC_responsavel($this->rowPrestador["c_responsavel"]);
            $this->setC_civil($this->rowPrestador["c_civil"]);
            $this->setC_nacionalidade($this->rowPrestador["c_nacionalidade"]);
            $this->setC_formacao($this->rowPrestador["c_formacao"]);
            $this->setC_rg($this->rowPrestador["c_rg"]);
            $this->setC_cpf($this->rowPrestador["c_cpf"]);
            $this->setC_email2($this->rowPrestador["c_email2"]);
            $this->setC_site($this->rowPrestador["c_site"]);
            $this->setCo_responsavel($this->rowPrestador["co_responsavel"]);
            $this->setCo_tel($this->rowPrestador["co_tel"]);
            $this->setCo_fax($this->rowPrestador["co_fax"]);
            $this->setCo_civil($this->rowPrestador["co_civil"]);
            $this->setCo_nacionalidade($this->rowPrestador["co_nacionalidade"]);
            $this->setCo_email($this->rowPrestador["co_email"]);
            $this->setCo_municipio($this->rowPrestador["co_municipio"]);
            $this->setAssunto($this->rowPrestador["assunto"]);
            $this->setObjeto($this->rowPrestador["objeto"]);
            $this->setEspecificacao($this->rowPrestador["especificacao"]);
            $this->setValor($this->rowPrestador["valor"]);
            $this->setData($this->rowPrestador["data"]);
            $this->setData_proc($this->rowPrestador["data_proc"]);
            $this->setAcompanhamento($this->rowPrestador["acompanhamento"]);
            $this->setImprimir($this->rowPrestador["imprimir"]);
            $this->setStatus($this->rowPrestador["status"]);
            $this->setPrestador_tipo($this->rowPrestador["prestador_tipo"]);
            $this->setC_data_nascimento($this->rowPrestador["c_data_nascimento"]);
            $this->setCo_responsavel_socio1($this->rowPrestador["co_responsavel_socio1"]);
            $this->setCo_tel_socio1($this->rowPrestador["co_tel_socio1"]);
            $this->setCo_fax_socio1($this->rowPrestador["co_fax_socio1"]);
            $this->setCo_civil_socio1($this->rowPrestador["co_civil_socio1"]);
            $this->setCo_nacionalidade_socio1($this->rowPrestador["co_nacionalidade_socio1"]);
            $this->setCo_email_socio1($this->rowPrestador["co_email_socio1"]);
            $this->setCo_municipio_socio1($this->rowPrestador["co_municipio_socio1"]);
            $this->setData_nasc_socio1($this->rowPrestador["data_nasc_socio1"]);
            $this->setCo_responsavel_socio2($this->rowPrestador["co_responsavel_socio2"]);
            $this->setCo_tel_socio2($this->rowPrestador["co_tel_socio2"]);
            $this->setCo_fax_socio2($this->rowPrestador["co_fax_socio2"]);
            $this->setCo_civil_socio2($this->rowPrestador["co_civil_socio2"]);
            $this->setCo_nacionalidade_socio2($this->rowPrestador["co_nacionalidade_socio2"]);
            $this->setCo_email_socio2($this->rowPrestador["co_email_socio2"]);
            $this->setCo_municipio_socio2($this->rowPrestador["co_municipio_socio2"]);
            $this->setData_nasc_socio2($this->rowPrestador["data_nasc_socio2"]);
            $this->setNome_banco($this->rowPrestador["nome_banco"]);
            $this->setAgencia($this->rowPrestador["agencia"]);
            $this->setConta($this->rowPrestador["conta"]);
            $this->setValor_limite($this->rowPrestador["valor_limite"]);
            $this->setId_compra($this->rowPrestador["id_compra"]);
            $this->setPrestacao_contas($this->rowPrestador["prestacao_contas"]);
            $this->setEspecialidade($this->rowPrestador["especialidade"]);
            $this->setC_cep($this->rowPrestador["c_cep"]);
            $this->setC_id_tp_logradouro($this->rowPrestador["c_id_tp_logradouro"]);
            $this->setC_numero($this->rowPrestador["c_numero"]);
            $this->setC_complemento($this->rowPrestador["c_complemento"]);
            $this->setC_bairro($this->rowPrestador["c_bairro"]);
            $this->setC_uf($this->rowPrestador["c_uf"]);
            $this->setC_cod_cidade($this->rowPrestador["c_cod_cidade"]);
            $this->setIdContabilEmpresa($this->rowPrestador['id_contabil_empresa']);
            $this->setId_cnae($this->rowPrestador['id_cnae']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    public function getPrestador() {
        $this->limpaQuery();
        $auxPrestador = (!empty($this->getId_prestador())) ? " AND id_prestador = {$this->getId_prestador()} " : null;
        $auxRegiao = (!empty($this->getId_regiao())) ? " AND id_regiao = {$this->getId_regiao()} " : null;
        $auxProjeto = (!empty($this->getId_projeto()) && $this->getId_projeto() > 0) ? " AND id_projeto = {$this->getId_projeto()} " : null;

        $this->setFROM("prestadorservico");
        $this->setWHERE("status = 1 $auxPrestador $auxRegiao $auxProjeto");
        $this->setORDER("prestador_tipo, c_fantasia");

        if ($this->setRsPrestador()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

    public function getPrestadorAtivo($id_regiao = null, $id_projeto = null) {
        $this->limpaQuery();
        $auxRegiao = (!empty($this->getId_regiao())) ? " AND b.id_regiao = {$this->getId_regiao()} " : null;
        $auxProjeto = (!empty($this->getId_projeto())) ? " AND b.id_projeto = {$this->getId_projeto()} " : null;

        $this->setSELECT("a.id_empresa,
 IF(a.razao IS NULL,b.c_razao,a.razao) AS c_razao,
 IF(a.cnpj IS NULL, REPLACE(REPLACE(REPLACE(b.c_cnpj,'-',''),'/',''),'.',''), a.cnpj) AS c_cnpj, 
 b.id_prestador, 
 b.contratado_em, 
 b.encerrado_em, 
 b.valor, 
 b.prestador_tipo ");
        $this->setFROM("prestadorservico AS b LEFT JOIN contabil_empresa AS a ON (a.id_empresa = b.id_contabil_empresa)");
        $this->setWHERE("b.status = 1 AND (b.encerrado_em >= CURRENT_DATE() OR b.encerrado_em = '0000-00-00') $auxRegiao $auxProjeto");
        $this->setORDER("b.prestador_tipo, b.c_fantasia");

        if ($this->setRsPrestador()) {
            return 1;
        } else {
            return mysql_error();
        }
    }

    // backup da versao antiga
//    public function getPrestadorAtivo($id_regiao = null, $id_projeto = null) {
//        $this->limpaQuery();
//        $auxRegiao = (!empty($this->getId_regiao())) ? " AND id_regiao = {$this->getId_regiao()} " : null;
//        $auxProjeto = (!empty($this->getId_projeto())) ? " AND id_projeto = {$this->getId_projeto()} " : null;
//
//        $this->setFROM("prestadorservico");
//        $this->setWHERE("status = 1 AND encerrado_em >= CURRENT_DATE() $auxRegiao $auxProjeto");
//        $this->setORDER("prestador_tipo, c_fantasia");
//
//        if ($this->setRsPrestador()) {
//            return 1;
//        } else {
//            return 0; //$this->setError(mysql_error());
//        }
//    }

    public function getPrestadorEncerrado($id_regiao = null, $id_projeto = null) {
        $this->limpaQuery();
        $auxRegiao = (!empty($this->getId_regiao())) ? " AND id_regiao = {$this->getId_regiao()} " : null;
        $auxProjeto = (!empty($this->getId_projeto())) ? " AND id_projeto = {$this->getId_projeto()} " : null;

        $this->setSELECT("a.id_empresa,
 IF(a.razao IS NULL,b.c_razao,a.razao) AS c_razao,
 IF(a.cnpj IS NULL, REPLACE(REPLACE(REPLACE(b.c_cnpj,'-',''),'/',''),'.',''), a.cnpj) AS c_cnpj, 
 b.id_prestador, 
 b.contratado_em, 
 b.encerrado_em, 
 b.valor, 
 b.prestador_tipo ");
        $this->setFROM("prestadorservico AS b LEFT JOIN contabil_empresa AS a ON (a.id_empresa = b.id_contabil_empresa)");
        $this->setWHERE("b.status = 1 AND b.encerrado_em < CURRENT_DATE() $auxRegiao $auxProjeto");
        $this->setORDER("c_fantasia");

        if ($this->setRsPrestador()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

    private function makeCamposPrestador() {

        $array = array(
            'id_regiao' => $this->getId_regiao(),
            'id_projeto' => $this->getId_projeto(),
            'id_medida' => $this->getId_medida(),
            'aberto_por' => $this->getAberto_por(),
            'aberto_em' => $this->getAberto_em(),
            'contratado_por' => $this->getContratado_por(),
            'contratado_em' => $this->getContratado_em(),
            'encerrado_por' => $this->getEncerrado_por(),
            'encerrado_em' => $this->getEncerrado_em(),
            'contratante' => $this->getContratante(),
            'numero' => $this->getNumero(),
            'endereco' => $this->getEndereco(),
            'cnpj' => $this->getCnpj(),
            'responsavel' => $this->getResponsavel(),
            'civil' => $this->getCivil(),
            'nacionalidade' => $this->getNacionalidade(),
            'formacao' => $this->getFormacao(),
            'rg' => $this->getRg(),
            'cpf' => $this->getCpf(),
            'c_fantasia' => $this->getC_fantasia(),
            'c_razao' => $this->getC_razao(),
            'c_endereco' => $this->getC_endereco(),
            'c_cnpj' => $this->getC_cnpj(),
            'c_ie' => $this->getC_ie(),
            'c_im' => $this->getC_im(),
            'c_tel' => $this->getC_tel(),
            'c_fax' => $this->getC_fax(),
            'c_email' => $this->getC_email(),
            'c_responsavel' => $this->getC_responsavel(),
            'c_civil' => $this->getC_civil(),
            'c_nacionalidade' => $this->getC_nacionalidade(),
            'c_formacao' => $this->getC_formacao(),
            'c_rg' => $this->getC_rg(),
            'c_cpf' => $this->getC_cpf(),
            'c_email2' => $this->getC_email2(),
            'c_site' => $this->getC_site(),
            'co_responsavel' => $this->getCo_responsavel(),
            'co_tel' => $this->getCo_tel(),
            'co_fax' => $this->getCo_fax(),
            'co_civil' => $this->getCo_civil(),
            'co_nacionalidade' => $this->getCo_nacionalidade(),
            'co_email' => $this->getCo_email(),
            'co_municipio' => $this->getCo_municipio(),
            'assunto' => $this->getAssunto(),
            'objeto' => $this->getObjeto(),
            'especificacao' => $this->getEspecificacao(),
            'valor' => $this->getValor(),
            'data' => $this->getData(),
            'data_proc' => $this->getData_proc(),
            'acompanhamento' => $this->getAcompanhamento(),
            'imprimir' => $this->getImprimir(),
            'status' => $this->getStatus(),
            'prestador_tipo' => $this->getPrestador_tipo(),
            'c_data_nascimento' => $this->getC_data_nascimento(),
            'co_responsavel_socio1' => $this->getCo_responsavel_socio1(),
            'co_tel_socio1' => $this->getCo_tel_socio1(),
            'co_fax_socio1' => $this->getCo_fax_socio1(),
            'co_civil_socio1' => $this->getCo_civil_socio1(),
            'co_nacionalidade_socio1' => $this->getCo_nacionalidade_socio1(),
            'co_email_socio1' => $this->getCo_email_socio1(),
            'co_municipio_socio1' => $this->getCo_municipio_socio1(),
            'data_nasc_socio1' => $this->getData_nasc_socio1(),
            'co_responsavel_socio2' => $this->getCo_responsavel_socio2(),
            'co_tel_socio2' => $this->getCo_tel_socio2(),
            'co_fax_socio2' => $this->getCo_fax_socio2(),
            'co_civil_socio2' => $this->getCo_civil_socio2(),
            'co_nacionalidade_socio2' => $this->getCo_nacionalidade_socio2(),
            'co_email_socio2' => $this->getCo_email_socio2(),
            'co_municipio_socio2' => $this->getCo_municipio_socio2(),
            'data_nasc_socio2' => $this->getData_nasc_socio2(),
            'nome_banco' => $this->getNome_banco(),
            'agencia' => $this->getAgencia(),
            'conta' => $this->getConta(),
            'valor_limite' => $this->getValor_limite(),
            'id_compra' => $this->getId_compra(),
            'prestacao_contas' => $this->getPrestacao_contas(),
            'especialidade' => $this->getEspecialidade(),
            'c_cep' => $this->getC_cep(),
            'c_id_tp_logradouro' => $this->getC_id_tp_logradouro(),
            'c_numero' => $this->getC_numero(),
            'c_complemento' => $this->getC_complemento(),
            'c_bairro' => $this->getC_bairro(),
            'c_uf' => $this->getC_uf(),
            'c_cod_cidade' => $this->getC_cod_cidade(),
            'id_contabil_empresa' => $this->getIdContabilEmpresa(),
            'id_cnae' => $this->getId_cnae()
        );

        return $array;
    }

    public function updatePrestador() {
        $this->limpaQuery();

        $array = $this->makeCamposPrestador();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }

        //echo "UPDATE prestador_documentos SET " . implode(", ",($camposUpdate)) ." WHERE prestador_documento_id = {$this->getPrestador_documento_id()} LIMIT 1;";
        $this->setQUERY("UPDATE prestadorservico SET " . implode(", ", ($camposUpdate)) . " WHERE id_prestador = {$this->getId_prestador()} LIMIT 1;");

        if ($this->setRsPrestador()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

    public function insertPrestador() {
        $this->limpaQuery();

        $array = $this->makeCamposPrestador();

        $keys = implode(',', array_keys($array));
        $values = implode("' , '", $array);

        $this->setQUERY("INSERT INTO prestadorservico ($keys) VALUES ('$values');");

//        echo $sql = "$this->QUERY";exit();

        if ($this->setRsPrestador()) {
            $this->setId_prestador(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function getPrestadorContratoVencendo() {
        $this->limpaQuery();
        $auxRegiao = (!empty($this->getId_regiao())) ? " AND id_regiao = {$this->getId_regiao()} " : null;
        $auxProjeto = (!empty($this->getId_projeto())) ? " AND id_projeto = {$this->getId_projeto()} " : null;
        $this->setSELECT("a.id_empresa,
 IF(a.razao IS NULL,b.c_razao,a.razao) AS c_razao,
 IF(a.cnpj IS NULL, REPLACE(REPLACE(REPLACE(b.c_cnpj,'-',''),'/',''),'.',''), a.cnpj) AS c_cnpj, 
 b.id_prestador, 
 b.contratado_em, 
 b.encerrado_em, 
 b.valor, 
 b.prestador_tipo ");
        $this->setFROM("contabil_empresa AS a INNER JOIN prestadorservico AS b ON (a.id_empresa = b.id_contabil_empresa)");
        $this->setWHERE("DATEDIFF(encerrado_em,CURDATE()) <= 60 AND DATEDIFF(encerrado_em,CURDATE()) > 0 AND b.status = 1 $auxRegiao $auxProjeto");
        $this->setORDER("encerrado_em,prestador_tipo, c_fantasia");

        if ($this->setRsPrestador()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

}
