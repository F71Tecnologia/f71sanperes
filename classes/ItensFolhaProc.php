<?php

include_once('../conn.php');

class ItensFolhaProc{
    
    private $id;
    private $id_clt;
    private $id_folha;
    private $base;
    private $dias;
    private $aliquota;
    private $valor_dependente;
    private $numero_dependente;
    private $parcela_deduzir;
    private $idade_filho;
    private $cod_movimentacao;
    private $nome_movimentacao;
    private $hora_mensal;
    private $horas_noturnas;
    private $horas_noturnas_sindicato;
    private $porcentagem_adnoturno_sind;
    private $tipo_movimento;
    private $valor;
    private $cadastrado_em;
    private $cadastrado_por;
    private $lancamento_automotico;
    
    public function getId() {
        return $this->id;
    }

    public function getId_clt() {
        return $this->id_clt;
    }

    public function getId_folha() {
        return $this->id_folha;
    }

    public function getBase() {
        return $this->base;
    }

    public function getDias() {
        return $this->dias;
    }

    public function getAliquota() {
        return $this->aliquota;
    }

    public function getValor_dependente() {
        return $this->valor_dependente;
    }

    public function getNumero_dependente() {
        return $this->numero_dependente;
    }

    public function getParcela_deduzir() {
        return $this->parcela_deduzir;
    }

    public function getIdade_filho() {
        return $this->idade_filho;
    }

    public function getCod_movimentacao() {
        return $this->cod_movimentacao;
    }

    public function getNome_movimentacao() {
        return $this->nome_movimentacao;
    }

    public function getHora_mensal() {
        return $this->hora_mensal;
    }

    public function getHoras_noturnas() {
        return $this->horas_noturnas;
    }

    public function getHoras_noturnas_sindicato() {
        return $this->horas_noturnas_sindicato;
    }

    public function getPorcentagem_adnoturno_sind() {
        return $this->porcentagem_adnoturno_sind;
    }

    public function getTipo_movimento() {
        return $this->tipo_movimento;
    }

    public function getValor() {
        return $this->valor;
    }

    public function getCadastrado_em() {
        return $this->cadastrado_em;
    }

    public function getCadastrado_por() {
        return $this->cadastrado_por;
    }

    public function getLancamento_automotico() {
        return $this->lancamento_automotico;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setId_clt($id_clt) {
        $this->id_clt = $id_clt;
        return $this;
    }

    public function setId_folha($id_folha) {
        $this->id_folha = $id_folha;
        return $this;
    }

    public function setBase($base) {
        $this->base = $base;
        return $this;
    }

    public function setDias($dias) {
        $this->dias = $dias;
        return $this;
    }

    public function setAliquota($aliquota) {
        $this->aliquota = $aliquota;
        return $this;
    }

    public function setValor_dependente($valor_dependente) {
        $this->valor_dependente = $valor_dependente;
        return $this;
    }

    public function setNumero_dependente($numero_dependente) {
        $this->numero_dependente = $numero_dependente;
        return $this;
    }

    public function setParcela_deduzir($parcela_deduzir) {
        $this->parcela_deduzir = $parcela_deduzir;
        return $this;
    }

    public function setIdade_filho($idade_filho) {
        $this->idade_filho = $idade_filho;
        return $this;
    }

    public function setCod_movimentacao($cod_movimentacao) {
        $this->cod_movimentacao = $cod_movimentacao;
        return $this;
    }

    public function setNome_movimentacao($nome_movimentacao) {
        $this->nome_movimentacao = $nome_movimentacao;
        return $this;
    }

    public function setHora_mensal($hora_mensal) {
        $this->hora_mensal = $hora_mensal;
        return $this;
    }

    public function setHoras_noturnas($horas_noturnas) {
        $this->horas_noturnas = $horas_noturnas;
        return $this;
    }

    public function setHoras_noturnas_sindicato($horas_noturnas_sindicato) {
        $this->horas_noturnas_sindicato = $horas_noturnas_sindicato;
        return $this;
    }

    public function setPorcentagem_adnoturno_sind($porcentagem_adnoturno_sind) {
        $this->porcentagem_adnoturno_sind = $porcentagem_adnoturno_sind;
        return $this;
    }

    public function setTipo_movimento($tipo_movimento) {
        $this->tipo_movimento = $tipo_movimento;
        return $this;
    }

    public function setValor($valor) {
        $this->valor = $valor;
        return $this;
    }

    public function setCadastrado_em($cadastrado_em) {
        $this->cadastrado_em = $cadastrado_em;
        return $this;
    }

    public function setCadastrado_por($cadastrado_por) {
        $this->cadastrado_por = $cadastrado_por;
        return $this;
    }

    public function setLancamento_automotico($lancamento_automotico) {
        $this->lancamento_automotico = $lancamento_automotico;
        return $this;
    }

    public function addItem(){
        $campos = array('id_clt','id_folha','base','dias','aliquota','valor_dependente','numero_dependente','parcela_deduzir','idade_filho','cod_movimentacao','nome_movimentacao','hora_mensal','horas_noturnas','horas_noturnas_sindicato','porcentagem_adnoturno_sind','tipo_movimento','valor','cadastrado_por','lancamento_automatico');
        $qry = "INSERT INTO itens_folha_proc ( " . implode(',', $campos) . " ) VALUES (
                    '{$this->getId_clt()}',
                    '{$this->getId_folha()}',
                    '{$this->getBase()}',
                    '{$this->getDias()}',
                    '{$this->getAliquota()}',
                    '{$this->getValor_dependente()}',
                    '{$this->getNumero_dependente()}',
                    '{$this->getParcela_deduzir()}',
                    '{$this->getIdade_filho()}',
                    '{$this->getCod_movimentacao()}',
                    '{$this->getNome_movimentacao()}',
                    '{$this->getHora_mensal()}',
                    '{$this->getHoras_noturnas()}',
                    '{$this->getHoras_noturnas_sindicato()}',
                    '{$this->getPorcentagem_adnoturno_sind()}',
                    '{$this->getTipo_movimento()}',
                    '{$this->getValor()}',
                    '{$this->getCadastrado_por()}',
                    '{$this->getLancamento_automotico()}'    
                )";
                    
        mysql_query($qry) or die("erro ao inserir itens na tabela itens_folha_proc"); 
        $this->clearItem();
        
    }
    
    /*
     * 
     */
    public function clearItem(){
        $this->setId_clt('');
        $this->setId_folha('');
        $this->setBase('');
        $this->setDias('');
        $this->setAliquota('');
        $this->setValor_dependente('');
        $this->setNumero_dependente('');
        $this->setParcela_deduzir('');
        $this->setIdade_filho('');
        $this->setCod_movimentacao('');
        $this->setNome_movimentacao('');
        $this->setHora_mensal('');
        $this->setHoras_noturnas('');
        $this->setHoras_noturnas_sindicato('');
        $this->setPorcentagem_adnoturno_sind('');
        $this->setTipo_movimento('');
        $this->setValor('');
        $this->setCadastrado_por('');
        $this->setLancamento_automotico('');  
    }
    
    /**
     * 
     * @param type $id_clt
     * @param type $id_folha
     */
    public function removeItens($id_folha){
        $return = false;
        $qry = "DELETE FROM itens_folha_proc WHERE id_folha = '{$id_falha}'";
        
        if($sql = mysql_query($qry) or die('Erro ao remover itens')){
            $return = true;
        }
        
        return $return;
    }
    
}

$itensFolha = new ItensFolhaProc();
$itensFolha->setId_clt(1)->setBase(2000)->setValor(220); 
$itensFolha->addItem(); 

$itensFolha->setId_clt(1)->setBase(3000)->setLancamento_automotico(1); 
$itensFolha->addItem(); 