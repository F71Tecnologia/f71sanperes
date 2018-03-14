<?php 
class SaidaTravarLancamentoClass { 

    protected $id_saida;
    protected $id_regiao;
    protected $id_projeto;
    protected $id_banco;
    protected $id_user;
    protected $nome;
    protected $id_nome;
    protected $especifica;
    protected $tipo;
    protected $adicional;
    protected $valor;
    protected $data_proc;
    protected $data_vencimento;
    protected $data_pg;
    protected $hora_pg;
    protected $comprovante;
    protected $tipo_arquivo;
    protected $id_userpg;
    protected $id_compra;
    protected $campo3;
    protected $status;
    protected $id_deletado;
    protected $data_deletado;
    protected $valor_bruto;
    protected $juridico;
    protected $id_referencia;
    protected $id_bens;
    protected $id_tipo_pag_saida;
    protected $id_categoria_pag_saida;
    protected $nosso_numero;
    protected $cod_barra_consumo;
    protected $cod_barra_gerais;
    protected $nota_impressa;
    protected $id_clt;
    protected $entradaesaida_subgrupo_id;
    protected $tipo_boleto;
    protected $tipo_empresa;
    protected $id_fornecedor;
    protected $nome_fornecedor;
    protected $cnpj_fornecedor;
    protected $id_prestador;
    protected $nome_prestador;
    protected $cnpj_prestador;
    protected $impresso;
    protected $user_impresso;
    protected $data_impresso;
    protected $id_coop;
    protected $link_nfe;
    protected $n_documento;
    protected $estorno;
    protected $estorno_obs;
    protected $valor_estorno_parcial;
    protected $id_saida_pai;
    protected $darf;
    protected $tipo_darf;
    protected $mes_competencia;
    protected $ano_competencia;
    protected $id_autonomo;
    protected $dt_emissao_nf;
    protected $tipo_nf;
    protected $rh_sindicato;
    protected $flag_remessa;
    protected $valor_multa;
    protected $valor_juros;
    protected $trava_contabil;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' saida ';
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
    function getIdSaida() {
        return $this->id_saida;
    }

    function getIdRegiao() {
        return $this->id_regiao;
    }

    function getIdProjeto() {
        return $this->id_projeto;
    }

    function getIdBanco() {
        return $this->id_banco;
    }

    function getIdUser() {
        return $this->id_user;
    }

    function getNome() {
        return $this->nome;
    }

    function getIdNome() {
        return $this->id_nome;
    }

    function getEspecifica() {
        return $this->especifica;
    }

    function getTipo() {
        return $this->tipo;
    }

    function getAdicional() {
        return $this->adicional;
    }

    function getValor() {
        return $this->valor;
    }

    function getDataProc() {
        return $this->data_proc;
    }

    function getDataVencimento($formato = null) {
        if (empty($this->data_vencimento) || $this->data_vencimento == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_vencimento), $formato) : $this->data_vencimento;
        }
    }

    function getDataPg($formato = null) {
        if (empty($this->data_pg) || $this->data_pg == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_pg), $formato) : $this->data_pg;
        }
    }

    function getHoraPg() {
        return $this->hora_pg;
    }

    function getComprovante() {
        return $this->comprovante;
    }

    function getTipoArquivo() {
        return $this->tipo_arquivo;
    }

    function getIdUserpg() {
        return $this->id_userpg;
    }

    function getIdCompra() {
        return $this->id_compra;
    }

    function getCampo3() {
        return $this->campo3;
    }

    function getStatus() {
        return $this->status;
    }

    function getIdDeletado() {
        return $this->id_deletado;
    }

    function getDataDeletado() {
        return $this->data_deletado;
    }

    function getValorBruto() {
        return $this->valor_bruto;
    }

    function getJuridico() {
        return $this->juridico;
    }

    function getIdReferencia() {
        return $this->id_referencia;
    }

    function getIdBens() {
        return $this->id_bens;
    }

    function getIdTipoPagSaida() {
        return $this->id_tipo_pag_saida;
    }

    function getIdCategoriaPagSaida() {
        return $this->id_categoria_pag_saida;
    }

    function getNossoNumero() {
        return $this->nosso_numero;
    }

    function getCodBarraConsumo() {
        return $this->cod_barra_consumo;
    }

    function getCodBarraGerais() {
        return $this->cod_barra_gerais;
    }

    function getNotaImpressa() {
        return $this->nota_impressa;
    }

    function getIdClt() {
        return $this->id_clt;
    }

    function getEntradaesaidaSubgrupoId() {
        return $this->entradaesaida_subgrupo_id;
    }

    function getTipoBoleto() {
        return $this->tipo_boleto;
    }

    function getTipoEmpresa() {
        return $this->tipo_empresa;
    }

    function getIdFornecedor() {
        return $this->id_fornecedor;
    }

    function getNomeFornecedor() {
        return $this->nome_fornecedor;
    }

    function getCnpjFornecedor($limpo = false) {
        return ($limpo) ? str_replace(array('.','-','/'), '' ,$this->cnpj_fornecedor) : $this->cnpj_fornecedor;
    }

    function getIdPrestador() {
        return $this->id_prestador;
    }

    function getNomePrestador() {
        return $this->nome_prestador;
    }

    function getCnpjPrestador($limpo = false) {
        return ($limpo) ? str_replace(array('.','-','/'), '' ,$this->cnpj_prestador) : $this->cnpj_prestador;
    }

    function getImpresso() {
        return $this->impresso;
    }

    function getUserImpresso() {
        return $this->user_impresso;
    }

    function getDataImpresso($formato = null) {
        if (empty($this->data_impresso) || $this->data_impresso == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_impresso), $formato) : $this->data_impresso;
        }
    }

    function getIdCoop() {
        return $this->id_coop;
    }

    function getLinkNfe() {
        return $this->link_nfe;
    }

    function getNDocumento() {
        return $this->n_documento;
    }

    function getEstorno() {
        return $this->estorno;
    }

    function getEstornoObs() {
        return $this->estorno_obs;
    }

    function getValorEstornoParcial() {
        return $this->valor_estorno_parcial;
    }

    function getIdSaidaPai() {
        return $this->id_saida_pai;
    }

    function getDarf() {
        return $this->darf;
    }

    function getTipoDarf() {
        return $this->tipo_darf;
    }

    function getMesCompetencia() {
        return $this->mes_competencia;
    }

    function getAnoCompetencia() {
        return $this->ano_competencia;
    }

    function getIdAutonomo() {
        return $this->id_autonomo;
    }

    function getDtEmissaoNf($formato = null) {
        if (empty($this->dt_emissao_nf) || $this->dt_emissao_nf == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->dt_emissao_nf), $formato) : $this->dt_emissao_nf;
        }
    }

    function getTipoNf() {
        return $this->tipo_nf;
    }

    function getRhSindicato() {
        return $this->rh_sindicato;
    }

    function getFlagRemessa() {
        return $this->flag_remessa;
    }

    function getValorMulta() {
        return $this->valor_multa;
    }

    function getValorJuros() {
        return $this->valor_juros;
    }

    function getTravaContabil() {
        return $this->trava_contabil;
    }

    //SET's DA CLASSE
    function setIdSaida($id_saida) {
        $this->id_saida = $id_saida;
    }

    function setIdRegiao($id_regiao) {
        $this->id_regiao = $id_regiao;
    }

    function setIdProjeto($id_projeto) {
        $this->id_projeto = $id_projeto;
    }

    function setIdBanco($id_banco) {
        $this->id_banco = $id_banco;
    }

    function setIdUser($id_user) {
        $this->id_user = $id_user;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

    function setIdNome($id_nome) {
        $this->id_nome = $id_nome;
    }

    function setEspecifica($especifica) {
        $this->especifica = $especifica;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    function setAdicional($adicional) {
        $this->adicional = $adicional;
    }

    function setValor($valor) {
        $this->valor = $valor;
    }

    function setDataProc($data_proc) {
        $this->data_proc = $data_proc;
    }

    function setDataVencimento($data_vencimento) {
        $this->data_vencimento = $data_vencimento;
    }

    function setDataPg($data_pg) {
        $this->data_pg = $data_pg;
    }

    function setHoraPg($hora_pg) {
        $this->hora_pg = $hora_pg;
    }

    function setComprovante($comprovante) {
        $this->comprovante = $comprovante;
    }

    function setTipoArquivo($tipo_arquivo) {
        $this->tipo_arquivo = $tipo_arquivo;
    }

    function setIdUserpg($id_userpg) {
        $this->id_userpg = $id_userpg;
    }

    function setIdCompra($id_compra) {
        $this->id_compra = $id_compra;
    }

    function setCampo3($campo3) {
        $this->campo3 = $campo3;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setIdDeletado($id_deletado) {
        $this->id_deletado = $id_deletado;
    }

    function setDataDeletado($data_deletado) {
        $this->data_deletado = $data_deletado;
    }

    function setValorBruto($valor_bruto) {
        $this->valor_bruto = $valor_bruto;
    }

    function setJuridico($juridico) {
        $this->juridico = $juridico;
    }

    function setIdReferencia($id_referencia) {
        $this->id_referencia = $id_referencia;
    }

    function setIdBens($id_bens) {
        $this->id_bens = $id_bens;
    }

    function setIdTipoPagSaida($id_tipo_pag_saida) {
        $this->id_tipo_pag_saida = $id_tipo_pag_saida;
    }

    function setIdCategoriaPagSaida($id_categoria_pag_saida) {
        $this->id_categoria_pag_saida = $id_categoria_pag_saida;
    }

    function setNossoNumero($nosso_numero) {
        $this->nosso_numero = $nosso_numero;
    }

    function setCodBarraConsumo($cod_barra_consumo) {
        $this->cod_barra_consumo = $cod_barra_consumo;
    }

    function setCodBarraGerais($cod_barra_gerais) {
        $this->cod_barra_gerais = $cod_barra_gerais;
    }

    function setNotaImpressa($nota_impressa) {
        $this->nota_impressa = $nota_impressa;
    }

    function setIdClt($id_clt) {
        $this->id_clt = $id_clt;
    }

    function setEntradaesaidaSubgrupoId($entradaesaida_subgrupo_id) {
        $this->entradaesaida_subgrupo_id = $entradaesaida_subgrupo_id;
    }

    function setTipoBoleto($tipo_boleto) {
        $this->tipo_boleto = $tipo_boleto;
    }

    function setTipoEmpresa($tipo_empresa) {
        $this->tipo_empresa = $tipo_empresa;
    }

    function setIdFornecedor($id_fornecedor) {
        $this->id_fornecedor = $id_fornecedor;
    }

    function setNomeFornecedor($nome_fornecedor) {
        $this->nome_fornecedor = $nome_fornecedor;
    }

    function setCnpjFornecedor($cnpj_fornecedor) {
        $this->cnpj_fornecedor = $cnpj_fornecedor;
    }

    function setIdPrestador($id_prestador) {
        $this->id_prestador = $id_prestador;
    }

    function setNomePrestador($nome_prestador) {
        $this->nome_prestador = $nome_prestador;
    }

    function setCnpjPrestador($cnpj_prestador) {
        $this->cnpj_prestador = $cnpj_prestador;
    }

    function setImpresso($impresso) {
        $this->impresso = $impresso;
    }

    function setUserImpresso($user_impresso) {
        $this->user_impresso = $user_impresso;
    }

    function setDataImpresso($data_impresso) {
        $this->data_impresso = $data_impresso;
    }

    function setIdCoop($id_coop) {
        $this->id_coop = $id_coop;
    }

    function setLinkNfe($link_nfe) {
        $this->link_nfe = $link_nfe;
    }

    function setNDocumento($n_documento) {
        $this->n_documento = $n_documento;
    }

    function setEstorno($estorno) {
        $this->estorno = $estorno;
    }

    function setEstornoObs($estorno_obs) {
        $this->estorno_obs = $estorno_obs;
    }

    function setValorEstornoParcial($valor_estorno_parcial) {
        $this->valor_estorno_parcial = $valor_estorno_parcial;
    }

    function setIdSaidaPai($id_saida_pai) {
        $this->id_saida_pai = $id_saida_pai;
    }

    function setDarf($darf) {
        $this->darf = $darf;
    }

    function setTipoDarf($tipo_darf) {
        $this->tipo_darf = $tipo_darf;
    }

    function setMesCompetencia($mes_competencia) {
        $this->mes_competencia = $mes_competencia;
    }

    function setAnoCompetencia($ano_competencia) {
        $this->ano_competencia = $ano_competencia;
    }

    function setIdAutonomo($id_autonomo) {
        $this->id_autonomo = $id_autonomo;
    }

    function setDtEmissaoNf($dt_emissao_nf) {
        $this->dt_emissao_nf = $dt_emissao_nf;
    }

    function setTipoNf($tipo_nf) {
        $this->tipo_nf = $tipo_nf;
    }

    function setRhSindicato($rh_sindicato) {
        $this->rh_sindicato = $rh_sindicato;
    }

    function setFlagRemessa($flag_remessa) {
        $this->flag_remessa = $flag_remessa;
    }

    function setValorMulta($valor_multa) {
        $this->valor_multa = $valor_multa;
    }

    function setValorJuros($valor_juros) {
        $this->valor_juros = $valor_juros;
    }

    function setTravaContabil($trava_contabil) {
        $this->trava_contabil = $trava_contabil;
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
        $this->id_saida = null;
        $this->id_regiao = null;
        $this->id_projeto = null;
        $this->id_banco = null;
        $this->id_user = null;
        $this->nome = null;
        $this->id_nome = null;
        $this->especifica = null;
        $this->tipo = null;
        $this->adicional = null;
        $this->valor = null;
        $this->data_proc = null;
        $this->data_vencimento = null;
        $this->data_pg = null;
        $this->hora_pg = null;
        $this->comprovante = null;
        $this->tipo_arquivo = null;
        $this->id_userpg = null;
        $this->id_compra = null;
        $this->campo3 = null;
        $this->status = null;
        $this->id_deletado = null;
        $this->data_deletado = null;
        $this->valor_bruto = null;
        $this->juridico = null;
        $this->id_referencia = null;
        $this->id_bens = null;
        $this->id_tipo_pag_saida = null;
        $this->id_categoria_pag_saida = null;
        $this->nosso_numero = null;
        $this->cod_barra_consumo = null;
        $this->cod_barra_gerais = null;
        $this->nota_impressa = null;
        $this->id_clt = null;
        $this->entradaesaida_subgrupo_id = null;
        $this->tipo_boleto = null;
        $this->tipo_empresa = null;
        $this->id_fornecedor = null;
        $this->nome_fornecedor = null;
        $this->cnpj_fornecedor = null;
        $this->id_prestador = null;
        $this->nome_prestador = null;
        $this->cnpj_prestador = null;
        $this->impresso = null;
        $this->user_impresso = null;
        $this->data_impresso = null;
        $this->id_coop = null;
        $this->link_nfe = null;
        $this->n_documento = null;
        $this->estorno = null;
        $this->estorno_obs = null;
        $this->valor_estorno_parcial = null;
        $this->id_saida_pai = null;
        $this->darf = null;
        $this->tipo_darf = null;
        $this->mes_competencia = null;
        $this->ano_competencia = null;
        $this->id_autonomo = null;
        $this->dt_emissao_nf = null;
        $this->tipo_nf = null;
        $this->rh_sindicato = null;
        $this->flag_remessa = null;
        $this->valor_multa = null;
        $this->valor_juros = null;
        $this->trava_contabil = null;
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
        $this->setFROM(' saida ');
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
            $this->setIdSaida($this->row['id_saida']);
            $this->setIdRegiao($this->row['id_regiao']);
            $this->setIdProjeto($this->row['id_projeto']);
            $this->setIdBanco($this->row['id_banco']);
            $this->setIdUser($this->row['id_user']);
            $this->setNome($this->row['nome']);
            $this->setIdNome($this->row['id_nome']);
            $this->setEspecifica($this->row['especifica']);
            $this->setTipo($this->row['tipo']);
            $this->setAdicional($this->row['adicional']);
            $this->setValor($this->row['valor']);
            $this->setDataProc($this->row['data_proc']);
            $this->setDataVencimento($this->row['data_vencimento']);
            $this->setDataPg($this->row['data_pg']);
            $this->setHoraPg($this->row['hora_pg']);
            $this->setComprovante($this->row['comprovante']);
            $this->setTipoArquivo($this->row['tipo_arquivo']);
            $this->setIdUserpg($this->row['id_userpg']);
            $this->setIdCompra($this->row['id_compra']);
            $this->setCampo3($this->row['campo3']);
            $this->setStatus($this->row['status']);
            $this->setIdDeletado($this->row['id_deletado']);
            $this->setDataDeletado($this->row['data_deletado']);
            $this->setValorBruto($this->row['valor_bruto']);
            $this->setJuridico($this->row['juridico']);
            $this->setIdReferencia($this->row['id_referencia']);
            $this->setIdBens($this->row['id_bens']);
            $this->setIdTipoPagSaida($this->row['id_tipo_pag_saida']);
            $this->setIdCategoriaPagSaida($this->row['id_categoria_pag_saida']);
            $this->setNossoNumero($this->row['nosso_numero']);
            $this->setCodBarraConsumo($this->row['cod_barra_consumo']);
            $this->setCodBarraGerais($this->row['cod_barra_gerais']);
            $this->setNotaImpressa($this->row['nota_impressa']);
            $this->setIdClt($this->row['id_clt']);
            $this->setEntradaesaidaSubgrupoId($this->row['entradaesaida_subgrupo_id']);
            $this->setTipoBoleto($this->row['tipo_boleto']);
            $this->setTipoEmpresa($this->row['tipo_empresa']);
            $this->setIdFornecedor($this->row['id_fornecedor']);
            $this->setNomeFornecedor($this->row['nome_fornecedor']);
            $this->setCnpjFornecedor($this->row['cnpj_fornecedor']);
            $this->setIdPrestador($this->row['id_prestador']);
            $this->setNomePrestador($this->row['nome_prestador']);
            $this->setCnpjPrestador($this->row['cnpj_prestador']);
            $this->setImpresso($this->row['impresso']);
            $this->setUserImpresso($this->row['user_impresso']);
            $this->setDataImpresso($this->row['data_impresso']);
            $this->setIdCoop($this->row['id_coop']);
            $this->setLinkNfe($this->row['link_nfe']);
            $this->setNDocumento($this->row['n_documento']);
            $this->setEstorno($this->row['estorno']);
            $this->setEstornoObs($this->row['estorno_obs']);
            $this->setValorEstornoParcial($this->row['valor_estorno_parcial']);
            $this->setIdSaidaPai($this->row['id_saida_pai']);
            $this->setDarf($this->row['darf']);
            $this->setTipoDarf($this->row['tipo_darf']);
            $this->setMesCompetencia($this->row['mes_competencia']);
            $this->setAnoCompetencia($this->row['ano_competencia']);
            $this->setIdAutonomo($this->row['id_autonomo']);
            $this->setDtEmissaoNf($this->row['dt_emissao_nf']);
            $this->setTipoNf($this->row['tipo_nf']);
            $this->setRhSindicato($this->row['rh_sindicato']);
            $this->setFlagRemessa($this->row['flag_remessa']);
            $this->setValorMulta($this->row['valor_multa']);
            $this->setValorJuros($this->row['valor_juros']);
            $this->setTravaContabil($this->row['trava_contabil']);
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
            'id_banco' => addslashes($this->getIdBanco()),
            'id_user' => addslashes($this->getIdUser()),
            'nome' => addslashes($this->getNome()),
            'id_nome' => addslashes($this->getIdNome()),
            'especifica' => addslashes($this->getEspecifica()),
            'tipo' => addslashes($this->getTipo()),
            'adicional' => addslashes($this->getAdicional()),
            'valor' => addslashes($this->getValor()),
            'data_proc' => addslashes($this->getDataProc()),
            'data_vencimento' => addslashes($this->getDataVencimento()),
            'data_pg' => addslashes($this->getDataPg()),
            'hora_pg' => addslashes($this->getHoraPg()),
            'comprovante' => addslashes($this->getComprovante()),
            'tipo_arquivo' => addslashes($this->getTipoArquivo()),
            'id_userpg' => addslashes($this->getIdUserpg()),
            'id_compra' => addslashes($this->getIdCompra()),
            'campo3' => addslashes($this->getCampo3()),
            'status' => addslashes($this->getStatus()),
            'id_deletado' => addslashes($this->getIdDeletado()),
            'data_deletado' => addslashes($this->getDataDeletado()),
            'valor_bruto' => addslashes($this->getValorBruto()),
            'juridico' => addslashes($this->getJuridico()),
            'id_referencia' => addslashes($this->getIdReferencia()),
            'id_bens' => addslashes($this->getIdBens()),
            'id_tipo_pag_saida' => addslashes($this->getIdTipoPagSaida()),
            'id_categoria_pag_saida' => addslashes($this->getIdCategoriaPagSaida()),
            'nosso_numero' => addslashes($this->getNossoNumero()),
            'cod_barra_consumo' => addslashes($this->getCodBarraConsumo()),
            'cod_barra_gerais' => addslashes($this->getCodBarraGerais()),
            'nota_impressa' => addslashes($this->getNotaImpressa()),
            'id_clt' => addslashes($this->getIdClt()),
            'entradaesaida_subgrupo_id' => addslashes($this->getEntradaesaidaSubgrupoId()),
            'tipo_boleto' => addslashes($this->getTipoBoleto()),
            'tipo_empresa' => addslashes($this->getTipoEmpresa()),
            'id_fornecedor' => addslashes($this->getIdFornecedor()),
            'nome_fornecedor' => addslashes($this->getNomeFornecedor()),
            'cnpj_fornecedor' => addslashes($this->getCnpjFornecedor()),
            'id_prestador' => addslashes($this->getIdPrestador()),
            'nome_prestador' => addslashes($this->getNomePrestador()),
            'cnpj_prestador' => addslashes($this->getCnpjPrestador()),
            'impresso' => addslashes($this->getImpresso()),
            'user_impresso' => addslashes($this->getUserImpresso()),
            'data_impresso' => addslashes($this->getDataImpresso()),
            'id_coop' => addslashes($this->getIdCoop()),
            'link_nfe' => addslashes($this->getLinkNfe()),
            'n_documento' => addslashes($this->getNDocumento()),
            'estorno' => addslashes($this->getEstorno()),
            'estorno_obs' => addslashes($this->getEstornoObs()),
            'valor_estorno_parcial' => addslashes($this->getValorEstornoParcial()),
            'id_saida_pai' => addslashes($this->getIdSaidaPai()),
            'darf' => addslashes($this->getDarf()),
            'tipo_darf' => addslashes($this->getTipoDarf()),
            'mes_competencia' => addslashes($this->getMesCompetencia()),
            'ano_competencia' => addslashes($this->getAnoCompetencia()),
            'id_autonomo' => addslashes($this->getIdAutonomo()),
            'dt_emissao_nf' => addslashes($this->getDtEmissaoNf()),
            'tipo_nf' => addslashes($this->getTipoNf()),
            'rh_sindicato' => addslashes($this->getRhSindicato()),
            'flag_remessa' => addslashes($this->getFlagRemessa()),
            'valor_multa' => addslashes($this->getValorMulta()),
            'valor_juros' => addslashes($this->getValorJuros()),
            'trava_contabil' => addslashes($this->getTravaContabil()),
        );

        return $array;
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE saida SET " . implode(", ", ($camposUpdate)) . " WHERE id_saida = {$this->getIdSaida()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO saida ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdSaida(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE saida SET status = 0 WHERE id_saida = {$this->getIdSaida()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM saida WHERE id_saida = {$this->getIdSaida()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function updateTrava() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE saida SET trava_contabil = 1 WHERE status = {$this->getStatus()} AND data_vencimento BETWEEN '{$this->getDataVencimento()}' AND LAST_DAY('{$this->getDataVencimento()}') AND id_projeto = '{$this->getIdProjeto()}'");
        if ($this->setRs()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

}