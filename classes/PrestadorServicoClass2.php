<?php
/* 
 * Módulo Objeto da classe ParceirosClass2 orientado ao FrameWork do sistema da F71
 * Data Criação: 21/05/2015
 * Desenvolvimento: Renato
 * Versão: 0.1 (Build 00001)
 * 
 * Obs sobre a versão: 
 * 
 */
class PrestadorServicoClass {
    
    private $id_prestador;
    private $id_regiao;
    private $id_projeto;
    private $id_medida;
    private $aberto_por;
    private $aberto_em;
    private $contratado_por;
    private $contratado_em;
    private $encerrado_por;
    private $encerrado_em;
    private $contratante;
    private $numero;
    private $endereco;
    private $cnpj;
    private $responsavel;
    private $civil;
    private $nacionalidade;
    private $formacao;
    private $rg;
    private $cpf;
    private $c_fantasia;
    private $c_razao;
    private $c_endereco;
    private $c_cnpj;
    private $c_ie;
    private $c_im;
    private $c_tel;
    private $c_fax;
    private $c_email;
    private $c_responsavel;
    private $c_civil;
    private $c_nacionalidade;
    private $c_formacao;
    private $c_rg;
    private $c_cpf;
    private $c_email2;
    private $c_site;
    private $co_responsavel;
    private $co_tel;
    private $co_fax;
    private $co_civil;
    private $co_nacionalidade;
    private $co_email;
    private $co_municipio;
    private $assunto;
    private $objeto;
    private $especificacao;
    private $valor;
    private $data;
    private $data_proc;
    private $acompanhamento;
    private $imprimir;
    private $status;
    private $prestador_tipo;
    private $c_data_nascimento;
    private $co_responsavel_socio1;
    private $co_tel_socio1;
    private $co_fax_socio1;
    private $co_civil_socio1;
    private $co_nacionalidade_socio1;
    private $co_email_socio1;
    private $co_municipio_socio1;
    private $data_nasc_socio1;
    private $co_responsavel_socio2;
    private $co_tel_socio2;
    private $co_fax_socio2;
    private $co_civil_socio2;
    private $co_nacionalidade_socio2;
    private $co_email_socio2;
    private $co_municipio_socio2;
    private $data_nasc_socio2;
    private $nome_banco;
    private $agencia;
    private $conta;
    private $valor_limite;
    private $id_compra;
    private $prestacao_contas;
    private $especialidade;
    private $c_cep;
    private $c_id_tp_logradouro;
    private $c_numero;
    private $c_complemento;
    private $c_bairro;
    private $c_uf;
    private $c_cod_cidade;
    
    private $QUERY;
    private $SELECT = " * ";
    private $FROM = " prestadorservico ";
    private $WHERE;
    private $GROUP;
    private $HAVING;
    private $ORDER;
    private $LIMIT;
    
    private $rs;
    private $row;
    private $num_rows;
        
    function __construct() {
        
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
        if(empty($this->aberto_em) || $this->aberto_em == '0000-00-00') { return '-'; } 
        else {
            return (!empty($formato)) ? date_format(date_create($this->aberto_em), $formato) : $this->aberto_em ;
        }
    }

    function getContratado_por() {
        return $this->contratado_por;
    }

    function getContratado_em($formato) {
        if(empty($this->contratado_em) || $this->contratado_em == '0000-00-00') { return '-'; } 
        else {
            return (!empty($formato)) ? date_format(date_create($this->contratado_em), $formato) : $this->contratado_em ;
        }
    }

    function getEncerrado_por() {
        return $this->encerrado_por;
    }

    function getEncerrado_em($formato) {
        if(empty($this->encerrado_em) || $this->encerrado_em == '0000-00-00') { return '-'; } 
        else {
            return (!empty($formato)) ? date_format(date_create($this->encerrado_em), $formato) : $this->encerrado_em ;
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
        return ($formatoBr) ? number_format(str_replace(",", ".", $this->valor), 2, ',', '.') : $this->valor;
    }

    function getData($formato) {
        if(empty($this->data) || $this->data == '0000-00-00') { return '-'; } 
        else {
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
        if(empty($this->c_data_nascimento) || $this->c_data_nascimento == '0000-00-00') { return '-'; } 
        else {
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
        if(empty($this->data_nasc_socio1) || $this->data_nasc_socio1 == '0000-00-00') { return '-'; } 
        else {
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
        if(empty($this->data_nasc_socio2) || $this->data_nasc_socio2 == '0000-00-00') { return '-'; } 
        else {
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

    private function setQUERY($QUERY) {
        $this->QUERY = $QUERY;
    }

    private function setSELECT($SELECT) {
        $this->SELECT = $SELECT;
    }

    private function setFROM($FROM) {
        $this->FROM = $FROM;
    }

    private function setWHERE($WHERE) {
        $this->WHERE = $WHERE;
    }

    private function setGROUP($GROUP) {
        $this->GROUP = $GROUP;
    }

    private function setORDER($ORDER) {
        $this->ORDER = $ORDER;
    }

    private function setLIMIT($LIMIT) {
        $this->LIMIT = $LIMIT;
    }

    private function setHAVING($HAVING) {
        $this->HAVING = $HAVING;
    }

    
    private function setRs($valor){ 
        if(!empty($this->QUERY)){
            $sql = $this->QUERY;
        } else {
            $auxWhere = (!empty($this->WHERE)) ? " WHERE $this->WHERE " : null ;
            $auxGroup = (!empty($this->GROUP)) ? " GROUP BY $this->GROUP " : null ;
            $auxHaving = (!empty($this->HAVING)) ? " HAVING $this->HAVING " : null ;
            $auxOrder = (!empty($this->ORDER)) ? " ORDER BY $this->ORDER " : null ;
            $auxLimit = (!empty($this->LIMIT)) ? " LIMIT $this->LIMIT " : null ;
            
            $sql = "SELECT $this->SELECT FROM $this->FROM $auxWhere $auxGroup $auxHaving $auxOrder $auxLimit";
        }
        
        $this->rs = mysql_query($sql);
        $this->num_rows = mysql_num_rows($this->rs);
        return $this->rs;
    }
    
    private function limpaQuery() {
        $this->setQUERY("");
        $this->setSELECT(" * ");
        $this->setFROM(" prestadorservico ");
        $this->setWHERE("");
        $this->setGROUP("");
        $this->setHAVING("");
        $this->setORDER("");
        $this->setLIMIT("");
    }
    
    public function getNumRow(){
        return $this->num_rows;
    }

    private function setRow($valor){
        return $this->row = mysql_fetch_array($valor);
    }
    
    public function getRow(){

        if($this->setRow($this->rs)){
            
            $this->setId_prestador($this->row["id_prestador"]);
            $this->setId_regiao($this->row["id_regiao"]);
            $this->setId_projeto($this->row["id_projeto"]);
            $this->setId_medida($this->row["id_medida"]);
            $this->setAberto_por($this->row["aberto_por"]);
            $this->setAberto_em($this->row["aberto_em"]);
            $this->setContratado_por($this->row["contratado_por"]);
            $this->setContratado_em($this->row["contratado_em"]);
            $this->setEncerrado_por($this->row["encerrado_por"]);
            $this->setEncerrado_em($this->row["encerrado_em"]);
            $this->setContratante($this->row["contratante"]);
            $this->setNumero($this->row["numero"]);
            $this->setEndereco($this->row["endereco"]);
            $this->setCnpj($this->row["cnpj"]);
            $this->setResponsavel($this->row["responsavel"]);
            $this->setCivil($this->row["civil"]);
            $this->setNacionalidade($this->row["nacionalidade"]);
            $this->setFormacao($this->row["formacao"]);
            $this->setRg($this->row["rg"]);
            $this->setCpf($this->row["cpf"]);
            $this->setC_fantasia($this->row["c_fantasia"]);
            $this->setC_razao($this->row["c_razao"]);
            $this->setC_endereco($this->row["c_endereco"]);
            $this->setC_cnpj($this->row["c_cnpj"]);
            $this->setC_ie($this->row["c_ie"]);
            $this->setC_im($this->row["c_im"]);
            $this->setC_tel($this->row["c_tel"]);
            $this->setC_fax($this->row["c_fax"]);
            $this->setC_email($this->row["c_email"]);
            $this->setC_responsavel($this->row["c_responsavel"]);
            $this->setC_civil($this->row["c_civil"]);
            $this->setC_nacionalidade($this->row["c_nacionalidade"]);
            $this->setC_formacao($this->row["c_formacao"]);
            $this->setC_rg($this->row["c_rg"]);
            $this->setC_cpf($this->row["c_cpf"]);
            $this->setC_email2($this->row["c_email2"]);
            $this->setC_site($this->row["c_site"]);
            $this->setCo_responsavel($this->row["co_responsavel"]);
            $this->setCo_tel($this->row["co_tel"]);
            $this->setCo_fax($this->row["co_fax"]);
            $this->setCo_civil($this->row["co_civil"]);
            $this->setCo_nacionalidade($this->row["co_nacionalidade"]);
            $this->setCo_email($this->row["co_email"]);
            $this->setCo_municipio($this->row["co_municipio"]);
            $this->setAssunto($this->row["assunto"]);
            $this->setObjeto($this->row["objeto"]);
            $this->setEspecificacao($this->row["especificacao"]);
            $this->setValor($this->row["valor"]);
            $this->setData($this->row["data"]);
            $this->setData_proc($this->row["data_proc"]);
            $this->setAcompanhamento($this->row["acompanhamento"]);
            $this->setImprimir($this->row["imprimir"]);
            $this->setStatus($this->row["status"]);
            $this->setPrestador_tipo($this->row["prestador_tipo"]);
            $this->setC_data_nascimento($this->row["c_data_nascimento"]);
            $this->setCo_responsavel_socio1($this->row["co_responsavel_socio1"]);
            $this->setCo_tel_socio1($this->row["co_tel_socio1"]);
            $this->setCo_fax_socio1($this->row["co_fax_socio1"]);
            $this->setCo_civil_socio1($this->row["co_civil_socio1"]);
            $this->setCo_nacionalidade_socio1($this->row["co_nacionalidade_socio1"]);
            $this->setCo_email_socio1($this->row["co_email_socio1"]);
            $this->setCo_municipio_socio1($this->row["co_municipio_socio1"]);
            $this->setData_nasc_socio1($this->row["data_nasc_socio1"]);
            $this->setCo_responsavel_socio2($this->row["co_responsavel_socio2"]);
            $this->setCo_tel_socio2($this->row["co_tel_socio2"]);
            $this->setCo_fax_socio2($this->row["co_fax_socio2"]);
            $this->setCo_civil_socio2($this->row["co_civil_socio2"]);
            $this->setCo_nacionalidade_socio2($this->row["co_nacionalidade_socio2"]);
            $this->setCo_email_socio2($this->row["co_email_socio2"]);
            $this->setCo_municipio_socio2($this->row["co_municipio_socio2"]);
            $this->setData_nasc_socio2($this->row["data_nasc_socio2"]);
            $this->setNome_banco($this->row["nome_banco"]);
            $this->setAgencia($this->row["agencia"]);
            $this->setConta($this->row["conta"]);
            $this->setValor_limite($this->row["valor_limite"]);
            $this->setId_compra($this->row["id_compra"]);
            $this->setPrestacao_contas($this->row["prestacao_contas"]);
            $this->setEspecialidade($this->row["especialidade"]);
            $this->setC_cep($this->row["c_cep"]);
            $this->setC_id_tp_logradouro($this->row["c_id_tp_logradouro"]);
            $this->setC_numero($this->row["c_numero"]);
            $this->setC_complemento($this->row["c_complemento"]);
            $this->setC_bairro($this->row["c_bairro"]);
            $this->setC_uf($this->row["c_uf"]);
            $this->setC_cod_cidade($this->row["c_cod_cidade"]);
            
            return 1;
        } else{
            //$this->setError(mysql_error());
            return 0;
        }
    }
    
    public function getAllPrestador($id_regiao = null, $id_projeto = null){
        $this->limpaQuery();
        $auxRegiao = (!empty($id_regiao)) ? " AND id_regiao = $id_regiao " : null ;
        $auxProjeto = (!empty($id_projeto)) ? " AND id_projeto = $id_projeto " : null ;
        
        $this->setWHERE("status = 1 $auxRegiao $auxProjeto");
        $this->setORDER("prestador_tipo, c_fantasia");
        
        if($this->setRs()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
    
    public function getPrestadorAtivo($id_regiao = null, $id_projeto = null){
        $this->limpaQuery();
        $auxRegiao = (!empty($id_regiao)) ? " AND id_regiao = $id_regiao " : null ;
        $auxProjeto = (!empty($id_projeto)) ? " AND id_projeto = $id_projeto " : null ;
        
        $this->setWHERE("status = 1 AND encerrado_em >= CURRENT_DATE() $auxRegiao $auxProjeto");
        $this->setORDER("prestador_tipo, c_fantasia");
        
        if($this->setRs()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
    
    public function getPrestadorEncerrado($id_regiao = null, $id_projeto = null){
        $this->limpaQuery();
        $auxRegiao = (!empty($id_regiao)) ? " AND id_regiao = $id_regiao " : null ;
        $auxProjeto = (!empty($id_projeto)) ? " AND id_projeto = $id_projeto " : null ;
        
        $this->setWHERE("status = 1 AND encerrado_em < CURRENT_DATE() $auxRegiao $auxProjeto");
        $this->setORDER("c_fantasia");
        
        if($this->setRs()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
    
    
    public function getStatusList(){
        $this->limpaQuery();
        
        $this->setSELECT("COUNT(B.prestador_documento_id) as qnt");
        $this->setFROM("prestador_tipo_doc AS A LEFT JOIN prestador_documentos AS B ON (A.prestador_tipo_doc_id = B.prestador_tipo_doc_id AND B.id_prestador = {$this->getId_prestador()})");
        $this->setWHERE("B.id_prestador IS NOT NULL");
        $this->setGROUP("B.`status`");
        $this->setORDER("A.ordem");
        
        if($this->setRs()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
    
    public static function getDocsVencidos($id_prestador){
        $qr = "SELECT *,IF(qntDocs IS NOT NULL AND qntDocsVen IS NULL,1,0) as resultado FROM (
                 SELECT A.prestador_tipo_doc_id,A.prestador_tipo_doc_nome,A.ordem,B.*,C.* FROM prestador_tipo_doc AS A
                 LEFT JOIN 
                  (
                   SELECT prestador_tipo_doc_id as qntDocsVen FROM prestador_documentos
                    WHERE id_prestador = {$id_prestador} AND data_vencimento > CURDATE()
                    ORDER BY data_vencimento
                  ) 
                  AS B ON (A.prestador_tipo_doc_id = B.qntDocsVen)
                 LEFT JOIN 
                  (
                   SELECT prestador_tipo_doc_id AS qntDocs FROM prestador_documentos
                    WHERE id_prestador = {$id_prestador}
                    ORDER BY data_vencimento
                  ) 
                 AS C ON (A.prestador_tipo_doc_id = C.qntDocs)
                 ORDER BY A.ordem
                ) AS temp
                HAVING resultado = 1";
        
        $rs = mysql_query($qr);
        $row = mysql_num_rows($rs);
        return $row;
    }
    
    public function update(){
        $this->limpaQuery();
//        if(!$this->getId_prestador()){
//            return 0;
//        }
//        $sql = "UPDATE * FROM prestadorservico WHERE status = 1";
//        return 1;
    }
    
    public function insert(){
        $this->limpaQuery();
//        $keys = implode(',', array_keys($this->parceiro_save));
//        $values = implode(", ",($this->parceiro_save));
//        $sql = "INSERT INTO parceiros ($keys) VALUES ($values);";
//        if($this->setRs($this->getQuery())){
//            $this->setIdParceiro(mysql_insert_id());
//            return 1;
//        } else {
//            $this->setError(mysql_error());            
//            return 0;
//        }        
//        return 1;
    }

}