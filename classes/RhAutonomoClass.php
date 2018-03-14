<?php

        
class RhAutonomoClass {

    private     $super_class;    
    protected   $error;
    private     $db;
    private     $date;
        
    private     $autonomo_default = array(
                                        'id_autonomo' => 0,
                                        'id_bolsista' => 0,
                                        'id_projeto' => 0,
                                        'id_regiao' => 0,
                                        'atividade' => '',
                                        'salario' => '',
                                        'localpagamento' => '',
                                        'locacao' => '',
                                        'id_unidade' => 0,
                                        'unidade' => '',
                                        'nome' => '',
                                        'sexo' => '',
                                        'endereco' => '',
                                        'numero' => '',
                                        'complemento' => '',
                                        'bairro' => '',
                                        'cidade' => '',
                                        'uf' => '',
                                        'cep' => '',
                                        'tel_fixo' => '',
                                        'tel_cel' => '',
                                        'tel_rec' => '',
                                        'data_nasci' => '',
                                        'naturalidade' => '',
                                        'nacionalidade' => '',
                                        'civil' => '',
                                        'rg' => '',
                                        'orgao' => '',
                                        'data_rg' => '',
                                        'cpf' => '',
                                        'conselho' => '',
                                        'titulo' => '',
                                        'zona' => '',
                                        'secao' => '',
                                        'inss' => '',
                                        'tipo_inss' => '',
                                        'pai' => '',
                                        'nacionalidade_pai' => '',
                                        'mae' => '',
                                        'nacionalidade_mae' => '',
                                        'estuda' => '',
                                        'data_escola' => '',
                                        'escolaridade' => '',
                                        'instituicao' => '',
                                        'curso' => '',
                                        'tipo_contratacao' => '',
                                        'tvsorrindo' => '',
                                        'banco' => '',
                                        'agencia' => '',
                                        'conta' => '',
                                        'tipo_conta' => '',
                                        'id_curso' => '',
                                        'id_psicologia' => '',
                                        'psicologia' => '',
                                        'obs' => '',
                                        'apolice' => '',
                                        'status' => 0,
                                        'data_entrada' => '',
                                        'data_saida' => '',
                                        'campo1' => '',
                                        'campo2' => '',
                                        'campo3' => '',
                                        'data_exame' => '',
                                        'data_exame2' => '',
                                        'reservista' => '',
                                        'etnia' => '',
                                        'deficiencia' => '',
                                        'cabelos' => '',
                                        'altura' => '',
                                        'olhos' => '',
                                        'peso' => '',
                                        'defeito' => '',
                                        'cipa' => '',
                                        'ad_noturno' => '',
                                        'plano' => 0,
                                        'assinatura' => 0,
                                        'distrato' => 0,
                                        'outros' => 0,
                                        'pis' => '',
                                        'dada_pis' => '',
                                        'data_ctps' => '',
                                        'serie_ctps' => '',
                                        'uf_ctps' => '',
                                        'uf_rg' => '',
                                        'fgts' => '',
                                        'insalubridade' => 0,
                                        'transporte' => '',
                                        'adicional' => '',
                                        'terceiro' => '',
                                        'num_par' => '',
                                        'data_ini' => '',
                                        'medica' => '',
                                        'tipo_pagamento' => '',
                                        'nome_banco' => '',
                                        'num_filhos' => '',
                                        'nome_filhos' => '',
                                        'observacao' => '',
                                        'impressos' => 0,
                                        'campo4' => '',
                                        'sis_user' => 0,
                                        'data_cad' => '',
                                        'foto' => '',
                                        'id_cooperativa' => 0,
                                        'c_nome' => '',
                                        'c_cpf' => '',
                                        'c_nascimento' => '',
                                        'c_profissao' => '',
                                        'e_empresa' => '',
                                        'e_cnpj' => '',
                                        'e_ramo' => '',
                                        'e_endereco' => '',
                                        'e_bairro' => '',
                                        'e_cidade' => '',
                                        'e_estado' => '',
                                        'e_cep' => '',
                                        'e_tel' => '',
                                        'e_ramal' => '',
                                        'e_fax' => '',
                                        'e_email' => '',
                                        'e_tempo' => '',
                                        'e_profissao' => '',
                                        'e_cargo' => '',
                                        'e_renda' => 0,
                                        'e_dataemissao' => '',
                                        'e_referencia' => '',
                                        'r_nome' => '',
                                        'r_endereco' => '',
                                        'r_bairro' => '',
                                        'r_cidade' => '',
                                        'r_estado' => '',
                                        'r_cep' => '',
                                        'r_tel' => '',
                                        'r_ramal' => '',
                                        'r_fax' => '',
                                        'r_email' => '',
                                        'dataalter' => '',
                                        'useralter' => 0,
                                        'vale' => 0,
                                        'senhatv' => '',
                                        'documento' => '',
                                        'rh_vale' => 0,
                                        'rh_vinculo' => 0,
                                        'rh_status' => 0,
                                        'rh_horario' => 0,
                                        'rh_sindicato' => 0,
                                        'rh_cbo' => 0,
                                        'ajuda_custo' => '',
                                        'cota' => 0,
                                        'parcelas' => 0,
                                        'status_reg' => 0,
                                        'contrato_medico' => 0,
                                        'matricula' => '',
                                        'n_processo' => '',
                                        'email' => '',
                                        'data_emissao' => '',
                                        'tipo_sanguineo' => '',
                                        'in_ponto' => '',
                                        'hora_retirada' => '',
                                        'hora_almoco' => '',
                                        'hora_retorno' => '',
                                        'hora_saida' => '',
                                        'id_categoria_trab' => 0,
                                        'id_estado_civil' => 0,
                                        'curriculo' => 0,
                                    );

    private     $autonomo = array();

    private     $autonomo_save = array();
    
    private     $search = '';
    
    public function setSuperClass($value) {
        
        $this->super_class = $value;
        
    }    

    public function setSearch($value, $key, $operand, $inline, $add){
        
        $this->createCoreClass();

        $this->db->setSearch($value, $key, $operand, $inline, $add);
        
    }

    public function setDefault() {

        $this->autonomo = $this->autonomo_default;
        

    }

    public function setId($value) {

        $this->autonomo_save['id_autonomo'] = ($this->autonomo['id_autonomo'] = $value);

    }

    public function setIdBolsista($value) {

        $this->autonomo_save['id_bolsista'] = ($this->autonomo['id_bolsista'] = $value);

    }

    public function setIdProjeto($value) {

        $this->autonomo_save['id_projeto'] = ($this->autonomo['id_projeto'] = $value);

    }

    public function setIdRegiao($value) {

        $this->autonomo_save['id_regiao'] = ($this->autonomo['id_regiao'] = $value);

    }

    public function setAtividade($value) {

        $this->autonomo_save['atividade'] = ($this->autonomo['atividade'] = $value);

    }

    public function setSalario($value) {

        $this->autonomo_save['salario'] = ($this->autonomo['salario'] = $value);

    }

    public function setLocalpagamento($value) {

        $this->autonomo_save['localpagamento'] = ($this->autonomo['localpagamento'] = $value);

    }

    public function setLocacao($value) {

        $this->autonomo_save['locacao'] = ($this->autonomo['locacao'] = $value);

    }

    public function setIdUnidade($value) {

        $this->autonomo_save['id_unidade'] = ($this->autonomo['id_unidade'] = $value);

    }

    public function setUnidade($value) {

        $this->autonomo_save['unidade'] = ($this->autonomo['unidade'] = $value);

    }

    public function setNome($value) {

        $this->autonomo_save['nome'] = ($this->autonomo['nome'] = $value);

    }

    public function setSexo($value) {

        $this->autonomo_save['sexo'] = ($this->autonomo['sexo'] = $value);

    }

    public function setEndereco($value) {

        $this->autonomo_save['endereco'] = ($this->autonomo['endereco'] = $value);

    }

    public function setNumero($value) {

        $this->autonomo_save['numero'] = ($this->autonomo['numero'] = $value);

    }

    public function setComplemento($value) {

        $this->autonomo_save['complemento'] = ($this->autonomo['complemento'] = $value);

    }

    public function setBairro($value) {

        $this->autonomo_save['bairro'] = ($this->autonomo['bairro'] = $value);

    }

    public function setCidade($value) {

        $this->autonomo_save['cidade'] = ($this->autonomo['cidade'] = $value);

    }

    public function setUf($value) {

        $this->autonomo_save['uf'] = ($this->autonomo['uf'] = $value);

    }

    public function setCep($value) {

        $this->autonomo_save['cep'] = ($this->autonomo['cep'] = $value);

    }

    public function setTelFixo($value) {

        $this->autonomo_save['tel_fixo'] = ($this->autonomo['tel_fixo'] = $value);

    }

    public function setTelCel($value) {

        $this->autonomo_save['tel_cel'] = ($this->autonomo['tel_cel'] = $value);

    }

    public function setTelRec($value) {

        $this->autonomo_save['tel_rec'] = ($this->autonomo['tel_rec'] = $value);

    }

    public function setDataNasci($value) {


        $this->date->setDate($this->clt["data_nasci"],$value);

        $this->autonomo_save['data_nasci'] = ($this->autonomo["data_nasci"]);


    }

    public function setNaturalidade($value) {

        $this->autonomo_save['naturalidade'] = ($this->autonomo['naturalidade'] = $value);

    }

    public function setNacionalidade($value) {

        $this->autonomo_save['nacionalidade'] = ($this->autonomo['nacionalidade'] = $value);

    }

    public function setCivil($value) {

        $this->autonomo_save['civil'] = ($this->autonomo['civil'] = $value);

    }

    public function setRg($value) {

        $this->autonomo_save['rg'] = ($this->autonomo['rg'] = $value);

    }

    public function setOrgao($value) {

        $this->autonomo_save['orgao'] = ($this->autonomo['orgao'] = $value);

    }

    public function setDataRg($value) {


        $this->date->setDate($this->clt["data_nasci"],$value);

        $this->autonomo_save['data_rg'] = ($this->autonomo["data_rg"]);


    }

    public function setCpf($value) {

        $this->autonomo_save['cpf'] = ($this->autonomo['cpf'] = $value);

    }

    public function setConselho($value) {

        $this->autonomo_save['conselho'] = ($this->autonomo['conselho'] = $value);

    }

    public function setTitulo($value) {

        $this->autonomo_save['titulo'] = ($this->autonomo['titulo'] = $value);

    }

    public function setZona($value) {

        $this->autonomo_save['zona'] = ($this->autonomo['zona'] = $value);

    }

    public function setSecao($value) {

        $this->autonomo_save['secao'] = ($this->autonomo['secao'] = $value);

    }

    public function setInss($value) {

        $this->autonomo_save['inss'] = ($this->autonomo['inss'] = $value);

    }

    public function setTipoInss($value) {

        $this->autonomo_save['tipo_inss'] = ($this->autonomo['tipo_inss'] = $value);

    }

    public function setPai($value) {

        $this->autonomo_save['pai'] = ($this->autonomo['pai'] = $value);

    }

    public function setNacionalidadePai($value) {

        $this->autonomo_save['nacionalidade_pai'] = ($this->autonomo['nacionalidade_pai'] = $value);

    }

    public function setMae($value) {

        $this->autonomo_save['mae'] = ($this->autonomo['mae'] = $value);

    }

    public function setNacionalidadeMae($value) {

        $this->autonomo_save['nacionalidade_mae'] = ($this->autonomo['nacionalidade_mae'] = $value);

    }

    public function setEstuda($value) {

        $this->autonomo_save['estuda'] = ($this->autonomo['estuda'] = $value);

    }

    public function setDataEscola($value) {


        $this->date->setDate($this->clt["data_nasci"],$value);

        $this->autonomo_save['data_escola'] = ($this->autonomo["data_escola"]);


    }

    public function setEscolaridade($value) {

        $this->autonomo_save['escolaridade'] = ($this->autonomo['escolaridade'] = $value);

    }

    public function setInstituicao($value) {

        $this->autonomo_save['instituicao'] = ($this->autonomo['instituicao'] = $value);

    }

    public function setCurso($value) {

        $this->autonomo_save['curso'] = ($this->autonomo['curso'] = $value);

    }

    public function setTipoContratacao($value) {

        $this->autonomo_save['tipo_contratacao'] = ($this->autonomo['tipo_contratacao'] = $value);

    }

    public function setTvsorrindo($value) {

        $this->autonomo_save['tvsorrindo'] = ($this->autonomo['tvsorrindo'] = $value);

    }

    public function setBanco($value) {

        $this->autonomo_save['banco'] = ($this->autonomo['banco'] = $value);

    }

    public function setAgencia($value) {

        $this->autonomo_save['agencia'] = ($this->autonomo['agencia'] = $value);

    }

    public function setConta($value) {

        $this->autonomo_save['conta'] = ($this->autonomo['conta'] = $value);

    }

    public function setTipoConta($value) {

        $this->autonomo_save['tipo_conta'] = ($this->autonomo['tipo_conta'] = $value);

    }

    public function setIdCurso($value) {

        $this->autonomo_save['id_curso'] = ($this->autonomo['id_curso'] = $value);

    }

    public function setIdPsicologia($value) {

        $this->autonomo_save['id_psicologia'] = ($this->autonomo['id_psicologia'] = $value);

    }

    public function setPsicologia($value) {

        $this->autonomo_save['psicologia'] = ($this->autonomo['psicologia'] = $value);

    }

    public function setObs($value) {

        $this->autonomo_save['obs'] = ($this->autonomo['obs'] = $value);

    }

    public function setApolice($value) {

        $this->autonomo_save['apolice'] = ($this->autonomo['apolice'] = $value);

    }

    public function setStatus($value) {

        $this->autonomo_save['status'] = ($this->autonomo['status'] = $value);

    }

    public function setDataEntrada($value) {


        $this->date->setDate($this->clt["data_nasci"],$value);

        $this->autonomo_save['data_entrada'] = ($this->autonomo["data_entrada"]);


    }

    public function setDataSaida($value) {


        $this->date->setDate($this->clt["data_nasci"],$value);

        $this->autonomo_save['data_saida'] = ($this->autonomo["data_saida"]);


    }

    public function setCampo1($value) {

        $this->autonomo_save['campo1'] = ($this->autonomo['campo1'] = $value);

    }

    public function setCampo2($value) {

        $this->autonomo_save['campo2'] = ($this->autonomo['campo2'] = $value);

    }

    public function setCampo3($value) {

        $this->autonomo_save['campo3'] = ($this->autonomo['campo3'] = $value);

    }

    public function setDataExame($value) {


        $this->date->setDate($this->clt["data_nasci"],$value);

        $this->autonomo_save['data_exame'] = ($this->autonomo["data_exame"]);


    }

    public function setDataExame2($value) {


        $this->date->setDate($this->clt["data_nasci"],$value);

        $this->autonomo_save['data_exame2'] = ($this->autonomo["data_exame2"]);


    }

    public function setReservista($value) {

        $this->autonomo_save['reservista'] = ($this->autonomo['reservista'] = $value);

    }

    public function setEtnia($value) {

        $this->autonomo_save['etnia'] = ($this->autonomo['etnia'] = $value);

    }

    public function setDeficiencia($value) {

        $this->autonomo_save['deficiencia'] = ($this->autonomo['deficiencia'] = $value);

    }

    public function setCabelos($value) {

        $this->autonomo_save['cabelos'] = ($this->autonomo['cabelos'] = $value);

    }

    public function setAltura($value) {

        $this->autonomo_save['altura'] = ($this->autonomo['altura'] = $value);

    }

    public function setOlhos($value) {

        $this->autonomo_save['olhos'] = ($this->autonomo['olhos'] = $value);

    }

    public function setPeso($value) {

        $this->autonomo_save['peso'] = ($this->autonomo['peso'] = $value);

    }

    public function setDefeito($value) {

        $this->autonomo_save['defeito'] = ($this->autonomo['defeito'] = $value);

    }

    public function setCipa($value) {

        $this->autonomo_save['cipa'] = ($this->autonomo['cipa'] = $value);

    }

    public function setAdNoturno($value) {

        $this->autonomo_save['ad_noturno'] = ($this->autonomo['ad_noturno'] = $value);

    }

    public function setPlano($value) {

        $this->autonomo_save['plano'] = ($this->autonomo['plano'] = $value);

    }

    public function setAssinatura($value) {

        $this->autonomo_save['assinatura'] = ($this->autonomo['assinatura'] = $value);

    }

    public function setDistrato($value) {

        $this->autonomo_save['distrato'] = ($this->autonomo['distrato'] = $value);

    }

    public function setOutros($value) {

        $this->autonomo_save['outros'] = ($this->autonomo['outros'] = $value);

    }

    public function setPis($value) {

        $this->autonomo_save['pis'] = ($this->autonomo['pis'] = $value);

    }

    public function setDadaPis($value) {


        $this->date->setDate($this->clt["data_nasci"],$value);

        $this->autonomo_save['dada_pis'] = ($this->autonomo["dada_pis"]);


    }

    public function setDataCtps($value) {


        $this->date->setDate($this->clt["data_nasci"],$value);

        $this->autonomo_save['data_ctps'] = ($this->autonomo["data_ctps"]);


    }

    public function setSerieCtps($value) {

        $this->autonomo_save['serie_ctps'] = ($this->autonomo['serie_ctps'] = $value);

    }

    public function setUfCtps($value) {

        $this->autonomo_save['uf_ctps'] = ($this->autonomo['uf_ctps'] = $value);

    }

    public function setUfRg($value) {

        $this->autonomo_save['uf_rg'] = ($this->autonomo['uf_rg'] = $value);

    }

    public function setFgts($value) {

        $this->autonomo_save['fgts'] = ($this->autonomo['fgts'] = $value);

    }

    public function setInsalubridade($value) {

        $this->autonomo_save['insalubridade'] = ($this->autonomo['insalubridade'] = $value);

    }

    public function setTransporte($value) {

        $this->autonomo_save['transporte'] = ($this->autonomo['transporte'] = $value);

    }

    public function setAdicional($value) {

        $this->autonomo_save['adicional'] = ($this->autonomo['adicional'] = $value);

    }

    public function setTerceiro($value) {

        $this->autonomo_save['terceiro'] = ($this->autonomo['terceiro'] = $value);

    }

    public function setNumPar($value) {

        $this->autonomo_save['num_par'] = ($this->autonomo['num_par'] = $value);

    }

    public function setDataIni($value) {


        $this->date->setDate($this->clt["data_nasci"],$value);

        $this->autonomo_save['data_ini'] = ($this->autonomo["data_ini"]);


    }

    public function setMedica($value) {

        $this->autonomo_save['medica'] = ($this->autonomo['medica'] = $value);

    }

    public function setTipoPagamento($value) {

        $this->autonomo_save['tipo_pagamento'] = ($this->autonomo['tipo_pagamento'] = $value);

    }

    public function setNomeBanco($value) {

        $this->autonomo_save['nome_banco'] = ($this->autonomo['nome_banco'] = $value);

    }

    public function setNumFilhos($value) {

        $this->autonomo_save['num_filhos'] = ($this->autonomo['num_filhos'] = $value);

    }

    public function setNomeFilhos($value) {

        $this->autonomo_save['nome_filhos'] = ($this->autonomo['nome_filhos'] = $value);

    }

    public function setObservacao($value) {

        $this->autonomo_save['observacao'] = ($this->autonomo['observacao'] = $value);

    }

    public function setImpressos($value) {

        $this->autonomo_save['impressos'] = ($this->autonomo['impressos'] = $value);

    }

    public function setCampo4($value) {

        $this->autonomo_save['campo4'] = ($this->autonomo['campo4'] = $value);

    }

    public function setSisUser($value) {

        $this->autonomo_save['sis_user'] = ($this->autonomo['sis_user'] = $value);

    }

    public function setDataCad($value) {


        $this->date->setDate($this->clt["data_nasci"],$value);

        $this->autonomo_save['data_cad'] = ($this->autonomo["data_cad"]);


    }

    public function setFoto($value) {

        $this->autonomo_save['foto'] = ($this->autonomo['foto'] = $value);

    }

    public function setIdCooperativa($value) {

        $this->autonomo_save['id_cooperativa'] = ($this->autonomo['id_cooperativa'] = $value);

    }

    public function setCNome($value) {

        $this->autonomo_save['c_nome'] = ($this->autonomo['c_nome'] = $value);

    }

    public function setCCpf($value) {

        $this->autonomo_save['c_cpf'] = ($this->autonomo['c_cpf'] = $value);

    }

    public function setCNascimento($value) {


        $this->date->setDate($this->clt["data_nasci"],$value);

        $this->autonomo_save['c_nascimento'] = ($this->autonomo["c_nascimento"]);


    }

    public function setCProfissao($value) {

        $this->autonomo_save['c_profissao'] = ($this->autonomo['c_profissao'] = $value);

    }

    public function setEEmpresa($value) {

        $this->autonomo_save['e_empresa'] = ($this->autonomo['e_empresa'] = $value);

    }

    public function setECnpj($value) {

        $this->autonomo_save['e_cnpj'] = ($this->autonomo['e_cnpj'] = $value);

    }

    public function setERamo($value) {

        $this->autonomo_save['e_ramo'] = ($this->autonomo['e_ramo'] = $value);

    }

    public function setEEndereco($value) {

        $this->autonomo_save['e_endereco'] = ($this->autonomo['e_endereco'] = $value);

    }

    public function setEBairro($value) {

        $this->autonomo_save['e_bairro'] = ($this->autonomo['e_bairro'] = $value);

    }

    public function setECidade($value) {

        $this->autonomo_save['e_cidade'] = ($this->autonomo['e_cidade'] = $value);

    }

    public function setEEstado($value) {

        $this->autonomo_save['e_estado'] = ($this->autonomo['e_estado'] = $value);

    }

    public function setECep($value) {

        $this->autonomo_save['e_cep'] = ($this->autonomo['e_cep'] = $value);

    }

    public function setETel($value) {

        $this->autonomo_save['e_tel'] = ($this->autonomo['e_tel'] = $value);

    }

    public function setERamal($value) {

        $this->autonomo_save['e_ramal'] = ($this->autonomo['e_ramal'] = $value);

    }

    public function setEFax($value) {

        $this->autonomo_save['e_fax'] = ($this->autonomo['e_fax'] = $value);

    }

    public function setEEmail($value) {

        $this->autonomo_save['e_email'] = ($this->autonomo['e_email'] = $value);

    }

    public function setETempo($value) {

        $this->autonomo_save['e_tempo'] = ($this->autonomo['e_tempo'] = $value);

    }

    public function setEProfissao($value) {

        $this->autonomo_save['e_profissao'] = ($this->autonomo['e_profissao'] = $value);

    }

    public function setECargo($value) {

        $this->autonomo_save['e_cargo'] = ($this->autonomo['e_cargo'] = $value);

    }

    public function setERenda($value) {

        $this->autonomo_save['e_renda'] = ($this->autonomo['e_renda'] = $value);

    }

    public function setEDataemissao($value) {


        $this->date->setDate($this->clt["data_nasci"],$value);

        $this->autonomo_save['e_dataemissao'] = ($this->autonomo["e_dataemissao"]);


    }

    public function setEReferencia($value) {

        $this->autonomo_save['e_referencia'] = ($this->autonomo['e_referencia'] = $value);

    }

    public function setRNome($value) {

        $this->autonomo_save['r_nome'] = ($this->autonomo['r_nome'] = $value);

    }

    public function setREndereco($value) {

        $this->autonomo_save['r_endereco'] = ($this->autonomo['r_endereco'] = $value);

    }

    public function setRBairro($value) {

        $this->autonomo_save['r_bairro'] = ($this->autonomo['r_bairro'] = $value);

    }

    public function setRCidade($value) {

        $this->autonomo_save['r_cidade'] = ($this->autonomo['r_cidade'] = $value);

    }

    public function setREstado($value) {

        $this->autonomo_save['r_estado'] = ($this->autonomo['r_estado'] = $value);

    }

    public function setRCep($value) {

        $this->autonomo_save['r_cep'] = ($this->autonomo['r_cep'] = $value);

    }

    public function setRTel($value) {

        $this->autonomo_save['r_tel'] = ($this->autonomo['r_tel'] = $value);

    }

    public function setRRamal($value) {

        $this->autonomo_save['r_ramal'] = ($this->autonomo['r_ramal'] = $value);

    }

    public function setRFax($value) {

        $this->autonomo_save['r_fax'] = ($this->autonomo['r_fax'] = $value);

    }

    public function setREmail($value) {

        $this->autonomo_save['r_email'] = ($this->autonomo['r_email'] = $value);

    }

    public function setDataalter($value) {


        $this->date->setDate($this->clt["data_nasci"],$value);

        $this->autonomo_save['dataalter'] = ($this->autonomo["dataalter"]);


    }

    public function setUseralter($value) {

        $this->autonomo_save['useralter'] = ($this->autonomo['useralter'] = $value);

    }

    public function setVale($value) {

        $this->autonomo_save['vale'] = ($this->autonomo['vale'] = $value);

    }

    public function setSenhatv($value) {

        $this->autonomo_save['senhatv'] = ($this->autonomo['senhatv'] = $value);

    }

    public function setDocumento($value) {

        $this->autonomo_save['documento'] = ($this->autonomo['documento'] = $value);

    }

    public function setRhVale($value) {

        $this->autonomo_save['rh_vale'] = ($this->autonomo['rh_vale'] = $value);

    }

    public function setRhVinculo($value) {

        $this->autonomo_save['rh_vinculo'] = ($this->autonomo['rh_vinculo'] = $value);

    }

    public function setRhStatus($value) {

        $this->autonomo_save['rh_status'] = ($this->autonomo['rh_status'] = $value);

    }

    public function setRhHorario($value) {

        $this->autonomo_save['rh_horario'] = ($this->autonomo['rh_horario'] = $value);

    }

    public function setRhSindicato($value) {

        $this->autonomo_save['rh_sindicato'] = ($this->autonomo['rh_sindicato'] = $value);

    }

    public function setRhCbo($value) {

        $this->autonomo_save['rh_cbo'] = ($this->autonomo['rh_cbo'] = $value);

    }

    public function setAjudaCusto($value) {

        $this->autonomo_save['ajuda_custo'] = ($this->autonomo['ajuda_custo'] = $value);

    }

    public function setCota($value) {

        $this->autonomo_save['cota'] = ($this->autonomo['cota'] = $value);

    }

    public function setParcelas($value) {

        $this->autonomo_save['parcelas'] = ($this->autonomo['parcelas'] = $value);

    }

    public function setStatusReg($value) {

        $this->autonomo_save['status_reg'] = ($this->autonomo['status_reg'] = $value);

    }

    public function setContratoMedico($value) {

        $this->autonomo_save['contrato_medico'] = ($this->autonomo['contrato_medico'] = $value);

    }

    public function setMatricula($value) {

        $this->autonomo_save['matricula'] = ($this->autonomo['matricula'] = $value);

    }

    public function setNProcesso($value) {

        $this->autonomo_save['n_processo'] = ($this->autonomo['n_processo'] = $value);

    }

    public function setEmail($value) {

        $this->autonomo_save['email'] = ($this->autonomo['email'] = $value);

    }

    public function setDataEmissao($value) {


        $this->date->setDate($this->clt["data_nasci"],$value);

        $this->autonomo_save['data_emissao'] = ($this->autonomo["data_emissao"]);


    }

    public function setTipoSanguineo($value) {

        $this->autonomo_save['tipo_sanguineo'] = ($this->autonomo['tipo_sanguineo'] = $value);

    }

    public function setInPonto($value) {

        $this->autonomo_save['in_ponto'] = ($this->autonomo['in_ponto'] = $value);

    }

    public function setHoraRetirada($value) {


        $this->date->setDate($this->clt["data_nasci"],$value);

        $this->autonomo_save['hora_retirada'] = ($this->autonomo["hora_retirada"]);


    }

    public function setHoraAlmoco($value) {


        $this->date->setDate($this->clt["data_nasci"],$value);

        $this->autonomo_save['hora_almoco'] = ($this->autonomo["hora_almoco"]);


    }

    public function setHoraRetorno($value) {


        $this->date->setDate($this->clt["data_nasci"],$value);

        $this->autonomo_save['hora_retorno'] = ($this->autonomo["hora_retorno"]);


    }

    public function setHoraSaida($value) {


        $this->date->setDate($this->clt["data_nasci"],$value);

        $this->autonomo_save['hora_saida'] = ($this->autonomo["hora_saida"]);


    }

    public function setIdCategoriaTrab($value) {

        $this->autonomo_save['id_categoria_trab'] = ($this->autonomo['id_categoria_trab'] = $value);

    }

    public function setIdEstadoCivil($value) {

        $this->autonomo_save['id_estado_civil'] = ($this->autonomo['id_estado_civil'] = $value);

    }

    public function setCurriculo($value) {

        $this->autonomo_save['curriculo'] = ($this->autonomo['curriculo'] = $value);

    }
    
    public function getSuperClass() {
        
        return $this->super_class;
        
    }     
    
    public function getSearch(){
        
        return $this->db->getSearch();
        
    }

    public function getId($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['id_autonomo']) : $this->autonomo['id_autonomo'];

    }    

    public function getIdBolsista($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['id_bolsista']) : $this->autonomo['id_bolsista'];

    }    

    public function getIdProjeto($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['id_projeto']) : $this->autonomo['id_projeto'];

    }    

    public function getIdRegiao($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['id_regiao']) : $this->autonomo['id_regiao'];

    }    

    public function getAtividade() {

        return $this->autonomo['atividade'];

    }    

    public function getSalario() {

        return $this->autonomo['salario'];

    }    

    public function getLocalpagamento() {

        return $this->autonomo['localpagamento'];

    }    

    public function getLocacao() {

        return $this->autonomo['locacao'];

    }    

    public function getIdUnidade($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['id_unidade']) : $this->autonomo['id_unidade'];

    }    

    public function getUnidade() {

        return $this->autonomo['unidade'];

    }    

    public function getNome() {

        return $this->autonomo['nome'];

    }    

    public function getSexo() {

        return $this->autonomo['sexo'];

    }    

    public function getEndereco() {

        return $this->autonomo['endereco'];

    }    

    public function getNumero() {

        return $this->autonomo['numero'];

    }    

    public function getComplemento() {

        return $this->autonomo['complemento'];

    }    

    public function getBairro() {

        return $this->autonomo['bairro'];

    }    

    public function getCidade() {

        return $this->autonomo['cidade'];

    }    

    public function getUf() {

        return $this->autonomo['uf'];

    }    

    public function getCep() {

        return $this->autonomo['cep'];

    }    

    public function getTelFixo() {

        return $this->autonomo['tel_fixo'];

    }    

    public function getTelCel() {

        return $this->autonomo['tel_cel'];

    }    

    public function getTelRec() {

        return $this->autonomo['tel_rec'];

    }    

    public function getDataNasci($format) {

        $this->date->setDate($this->clt['data_nasci'],$format);

        return $this->autonomo['data_nasci'];

    } 

    public function getNaturalidade() {

        return $this->autonomo['naturalidade'];

    }    

    public function getNacionalidade() {

        return $this->autonomo['nacionalidade'];

    }    

    public function getCivil() {

        return $this->autonomo['civil'];

    }    

    public function getRg() {

        return $this->autonomo['rg'];

    }    

    public function getOrgao() {

        return $this->autonomo['orgao'];

    }    

    public function getDataRg($format) {

        $this->date->setDate($this->clt['data_rg'],$format);

        return $this->autonomo['data_rg'];

    } 

    public function getCpf() {

        return $this->autonomo['cpf'];

    }    

    public function getConselho() {

        return $this->autonomo['conselho'];

    }    

    public function getTitulo() {

        return $this->autonomo['titulo'];

    }    

    public function getZona() {

        return $this->autonomo['zona'];

    }    

    public function getSecao() {

        return $this->autonomo['secao'];

    }    

    public function getInss() {

        return $this->autonomo['inss'];

    }    

    public function getTipoInss() {

        return $this->autonomo['tipo_inss'];

    }    

    public function getPai() {

        return $this->autonomo['pai'];

    }    

    public function getNacionalidadePai() {

        return $this->autonomo['nacionalidade_pai'];

    }    

    public function getMae() {

        return $this->autonomo['mae'];

    }    

    public function getNacionalidadeMae() {

        return $this->autonomo['nacionalidade_mae'];

    }    

    public function getEstuda() {

        return $this->autonomo['estuda'];

    }    

    public function getDataEscola($format) {

        $this->date->setDate($this->clt['data_escola'],$format);

        return $this->autonomo['data_escola'];

    } 

    public function getEscolaridade() {

        return $this->autonomo['escolaridade'];

    }    

    public function getInstituicao() {

        return $this->autonomo['instituicao'];

    }    

    public function getCurso() {

        return $this->autonomo['curso'];

    }    

    public function getTipoContratacao() {

        return $this->autonomo['tipo_contratacao'];

    }    

    public function getTvsorrindo() {

        return $this->autonomo['tvsorrindo'];

    }    

    public function getBanco() {

        return $this->autonomo['banco'];

    }    

    public function getAgencia() {

        return $this->autonomo['agencia'];

    }    

    public function getConta() {

        return $this->autonomo['conta'];

    }    

    public function getTipoConta() {

        return $this->autonomo['tipo_conta'];

    }    

    public function getIdCurso() {

        return $this->autonomo['id_curso'];

    }    

    public function getIdPsicologia() {

        return $this->autonomo['id_psicologia'];

    }    

    public function getPsicologia() {

        return $this->autonomo['psicologia'];

    }    

    public function getObs() {

        return $this->autonomo['obs'];

    }    

    public function getApolice() {

        return $this->autonomo['apolice'];

    }    

    public function getStatus($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['status']) : $this->autonomo['status'];

    }    

    public function getDataEntrada($format) {

        $this->date->setDate($this->clt['data_entrada'],$format);

        return $this->autonomo['data_entrada'];

    } 

    public function getDataSaida($format) {

        $this->date->setDate($this->clt['data_saida'],$format);

        return $this->autonomo['data_saida'];

    } 

    public function getCampo1() {

        return $this->autonomo['campo1'];

    }    

    public function getCampo2() {

        return $this->autonomo['campo2'];

    }    

    public function getCampo3() {

        return $this->autonomo['campo3'];

    }    

    public function getDataExame($format) {

        $this->date->setDate($this->clt['data_exame'],$format);

        return $this->autonomo['data_exame'];

    } 

    public function getDataExame2($format) {

        $this->date->setDate($this->clt['data_exame2'],$format);

        return $this->autonomo['data_exame2'];

    } 

    public function getReservista() {

        return $this->autonomo['reservista'];

    }    

    public function getEtnia() {

        return $this->autonomo['etnia'];

    }    

    public function getDeficiencia() {

        return $this->autonomo['deficiencia'];

    }    

    public function getCabelos() {

        return $this->autonomo['cabelos'];

    }    

    public function getAltura() {

        return $this->autonomo['altura'];

    }    

    public function getOlhos() {

        return $this->autonomo['olhos'];

    }    

    public function getPeso() {

        return $this->autonomo['peso'];

    }    

    public function getDefeito() {

        return $this->autonomo['defeito'];

    }    

    public function getCipa() {

        return $this->autonomo['cipa'];

    }    

    public function getAdNoturno() {

        return $this->autonomo['ad_noturno'];

    }    

    public function getPlano($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['plano']) : $this->autonomo['plano'];

    }    

    public function getAssinatura($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['assinatura']) : $this->autonomo['assinatura'];

    }    

    public function getDistrato($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['distrato']) : $this->autonomo['distrato'];

    }    

    public function getOutros($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['outros']) : $this->autonomo['outros'];

    }    

    public function getPis() {

        return $this->autonomo['pis'];

    }    

    public function getDadaPis($format) {

        $this->date->setDate($this->clt['dada_pis'],$format);

        return $this->autonomo['dada_pis'];

    } 

    public function getDataCtps($format) {

        $this->date->setDate($this->clt['data_ctps'],$format);

        return $this->autonomo['data_ctps'];

    } 

    public function getSerieCtps() {

        return $this->autonomo['serie_ctps'];

    }    

    public function getUfCtps() {

        return $this->autonomo['uf_ctps'];

    }    

    public function getUfRg() {

        return $this->autonomo['uf_rg'];

    }    

    public function getFgts() {

        return $this->autonomo['fgts'];

    }    

    public function getInsalubridade($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['insalubridade']) : $this->autonomo['insalubridade'];

    }    

    public function getTransporte() {

        return $this->autonomo['transporte'];

    }    

    public function getAdicional() {

        return $this->autonomo['adicional'];

    }    

    public function getTerceiro() {

        return $this->autonomo['terceiro'];

    }    

    public function getNumPar() {

        return $this->autonomo['num_par'];

    }    

    public function getDataIni($format) {

        $this->date->setDate($this->clt['data_ini'],$format);

        return $this->autonomo['data_ini'];

    } 

    public function getMedica() {

        return $this->autonomo['medica'];

    }    

    public function getTipoPagamento() {

        return $this->autonomo['tipo_pagamento'];

    }    

    public function getNomeBanco() {

        return $this->autonomo['nome_banco'];

    }    

    public function getNumFilhos() {

        return $this->autonomo['num_filhos'];

    }    

    public function getNomeFilhos() {

        return $this->autonomo['nome_filhos'];

    }    

    public function getObservacao() {

        return $this->autonomo['observacao'];

    }    

    public function getImpressos($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['impressos']) : $this->autonomo['impressos'];

    }    

    public function getCampo4() {

        return $this->autonomo['campo4'];

    }    

    public function getSisUser($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['sis_user']) : $this->autonomo['sis_user'];

    }    

    public function getDataCad($format) {

        $this->date->setDate($this->clt['data_cad'],$format);

        return $this->autonomo['data_cad'];

    } 

    public function getFoto() {

        return $this->autonomo['foto'];

    }    

    public function getIdCooperativa($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['id_cooperativa']) : $this->autonomo['id_cooperativa'];

    }    

    public function getCNome() {

        return $this->autonomo['c_nome'];

    }    

    public function getCCpf() {

        return $this->autonomo['c_cpf'];

    }    

    public function getCNascimento($format) {

        $this->date->setDate($this->clt['c_nascimento'],$format);

        return $this->autonomo['c_nascimento'];

    } 

    public function getCProfissao() {

        return $this->autonomo['c_profissao'];

    }    

    public function getEEmpresa() {

        return $this->autonomo['e_empresa'];

    }    

    public function getECnpj() {

        return $this->autonomo['e_cnpj'];

    }    

    public function getERamo() {

        return $this->autonomo['e_ramo'];

    }    

    public function getEEndereco() {

        return $this->autonomo['e_endereco'];

    }    

    public function getEBairro() {

        return $this->autonomo['e_bairro'];

    }    

    public function getECidade() {

        return $this->autonomo['e_cidade'];

    }    

    public function getEEstado() {

        return $this->autonomo['e_estado'];

    }    

    public function getECep() {

        return $this->autonomo['e_cep'];

    }    

    public function getETel() {

        return $this->autonomo['e_tel'];

    }    

    public function getERamal() {

        return $this->autonomo['e_ramal'];

    }    

    public function getEFax() {

        return $this->autonomo['e_fax'];

    }    

    public function getEEmail() {

        return $this->autonomo['e_email'];

    }    

    public function getETempo() {

        return $this->autonomo['e_tempo'];

    }    

    public function getEProfissao() {

        return $this->autonomo['e_profissao'];

    }    

    public function getECargo() {

        return $this->autonomo['e_cargo'];

    }    

    public function getERenda($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['e_renda']) : $this->autonomo['e_renda'];

    }    

    public function getEDataemissao($format) {

        $this->date->setDate($this->clt['e_dataemissao'],$format);

        return $this->autonomo['e_dataemissao'];

    } 

    public function getEReferencia() {

        return $this->autonomo['e_referencia'];

    }    

    public function getRNome() {

        return $this->autonomo['r_nome'];

    }    

    public function getREndereco() {

        return $this->autonomo['r_endereco'];

    }    

    public function getRBairro() {

        return $this->autonomo['r_bairro'];

    }    

    public function getRCidade() {

        return $this->autonomo['r_cidade'];

    }    

    public function getREstado() {

        return $this->autonomo['r_estado'];

    }    

    public function getRCep() {

        return $this->autonomo['r_cep'];

    }    

    public function getRTel() {

        return $this->autonomo['r_tel'];

    }    

    public function getRRamal() {

        return $this->autonomo['r_ramal'];

    }    

    public function getRFax() {

        return $this->autonomo['r_fax'];

    }    

    public function getREmail() {

        return $this->autonomo['r_email'];

    }    

    public function getDataalter($format) {

        $this->date->setDate($this->clt['dataalter'],$format);

        return $this->autonomo['dataalter'];

    } 

    public function getUseralter($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['useralter']) : $this->autonomo['useralter'];

    }    

    public function getVale($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['vale']) : $this->autonomo['vale'];

    }    

    public function getSenhatv() {

        return $this->autonomo['senhatv'];

    }    

    public function getDocumento() {

        return $this->autonomo['documento'];

    }    

    public function getRhVale($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['rh_vale']) : $this->autonomo['rh_vale'];

    }    

    public function getRhVinculo($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['rh_vinculo']) : $this->autonomo['rh_vinculo'];

    }    

    public function getRhStatus($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['rh_status']) : $this->autonomo['rh_status'];

    }    

    public function getRhHorario($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['rh_horario']) : $this->autonomo['rh_horario'];

    }    

    public function getRhSindicato($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['rh_sindicato']) : $this->autonomo['rh_sindicato'];

    }    

    public function getRhCbo($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['rh_cbo']) : $this->autonomo['rh_cbo'];

    }    

    public function getAjudaCusto() {

        return $this->autonomo['ajuda_custo'];

    }    

    public function getCota($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['cota']) : $this->autonomo['cota'];

    }    

    public function getParcelas($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['parcelas']) : $this->autonomo['parcelas'];

    }    

    public function getStatusReg($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['status_reg']) : $this->autonomo['status_reg'];

    }    

    public function getContratoMedico($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['contrato_medico']) : $this->autonomo['contrato_medico'];

    }    

    public function getMatricula() {

        return $this->autonomo['matricula'];

    }    

    public function getNProcesso() {

        return $this->autonomo['n_processo'];

    }    

    public function getEmail() {

        return $this->autonomo['email'];

    }    

    public function getDataEmissao($format) {

        $this->date->setDate($this->clt['data_emissao'],$format);

        return $this->autonomo['data_emissao'];

    } 

    public function getTipoSanguineo() {

        return $this->autonomo['tipo_sanguineo'];

    }    

    public function getInPonto() {

        return $this->autonomo['in_ponto'];

    }    

    public function getHoraRetirada($format) {

        $this->date->setDate($this->clt['hora_retirada'],$format);

        return $this->autonomo['hora_retirada'];

    } 

    public function getHoraAlmoco($format) {

        $this->date->setDate($this->clt['hora_almoco'],$format);

        return $this->autonomo['hora_almoco'];

    } 

    public function getHoraRetorno($format) {

        $this->date->setDate($this->clt['hora_retorno'],$format);

        return $this->autonomo['hora_retorno'];

    } 

    public function getHoraSaida($format) {

        $this->date->setDate($this->clt['hora_saida'],$format);

        return $this->autonomo['hora_saida'];

    } 

    public function getIdCategoriaTrab($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['id_categoria_trab']) : $this->autonomo['id_categoria_trab'];

    }    

    public function getIdEstadoCivil($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['id_estado_civil']) : $this->autonomo['id_estado_civil'];

    }    

    public function getCurriculo($format) {

        return isset($format) ? vsprintf($format, $this->autonomo['curriculo']) : $this->autonomo['curriculo'];

    }    
    
    public function getRow($collection){

        if($this->db->setRow($collection)){

            $this->setId($this->db->getRow('id_autonomo'));
            $this->setIdBolsista($this->db->getRow('id_bolsista'));
            $this->setIdProjeto($this->db->getRow('id_projeto'));
            $this->setIdRegiao($this->db->getRow('id_regiao'));
            $this->setAtividade($this->db->getRow('atividade'));
            $this->setSalario($this->db->getRow('salario'));
            $this->setLocalpagamento($this->db->getRow('localpagamento'));
            $this->setLocacao($this->db->getRow('locacao'));
            $this->setIdUnidade($this->db->getRow('id_unidade'));
            $this->setUnidade($this->db->getRow('unidade'));
            $this->setNome($this->db->getRow('nome'));
            $this->setSexo($this->db->getRow('sexo'));
            $this->setEndereco($this->db->getRow('endereco'));
            $this->setNumero($this->db->getRow('numero'));
            $this->setComplemento($this->db->getRow('complemento'));
            $this->setBairro($this->db->getRow('bairro'));
            $this->setCidade($this->db->getRow('cidade'));
            $this->setUf($this->db->getRow('uf'));
            $this->setCep($this->db->getRow('cep'));
            $this->setTelFixo($this->db->getRow('tel_fixo'));
            $this->setTelCel($this->db->getRow('tel_cel'));
            $this->setTelRec($this->db->getRow('tel_rec'));
            $this->setDataNasci($this->db->getRow('data_nasci'));
            $this->setNaturalidade($this->db->getRow('naturalidade'));
            $this->setNacionalidade($this->db->getRow('nacionalidade'));
            $this->setCivil($this->db->getRow('civil'));
            $this->setRg($this->db->getRow('rg'));
            $this->setOrgao($this->db->getRow('orgao'));
            $this->setDataRg($this->db->getRow('data_rg'));
            $this->setCpf($this->db->getRow('cpf'));
            $this->setConselho($this->db->getRow('conselho'));
            $this->setTitulo($this->db->getRow('titulo'));
            $this->setZona($this->db->getRow('zona'));
            $this->setSecao($this->db->getRow('secao'));
            $this->setInss($this->db->getRow('inss'));
            $this->setTipoInss($this->db->getRow('tipo_inss'));
            $this->setPai($this->db->getRow('pai'));
            $this->setNacionalidadePai($this->db->getRow('nacionalidade_pai'));
            $this->setMae($this->db->getRow('mae'));
            $this->setNacionalidadeMae($this->db->getRow('nacionalidade_mae'));
            $this->setEstuda($this->db->getRow('estuda'));
            $this->setDataEscola($this->db->getRow('data_escola'));
            $this->setEscolaridade($this->db->getRow('escolaridade'));
            $this->setInstituicao($this->db->getRow('instituicao'));
            $this->setCurso($this->db->getRow('curso'));
            $this->setTipoContratacao($this->db->getRow('tipo_contratacao'));
            $this->setTvsorrindo($this->db->getRow('tvsorrindo'));
            $this->setBanco($this->db->getRow('banco'));
            $this->setAgencia($this->db->getRow('agencia'));
            $this->setConta($this->db->getRow('conta'));
            $this->setTipoConta($this->db->getRow('tipo_conta'));
            $this->setIdCurso($this->db->getRow('id_curso'));
            $this->setIdPsicologia($this->db->getRow('id_psicologia'));
            $this->setPsicologia($this->db->getRow('psicologia'));
            $this->setObs($this->db->getRow('obs'));
            $this->setApolice($this->db->getRow('apolice'));
            $this->setStatus($this->db->getRow('status'));
            $this->setDataEntrada($this->db->getRow('data_entrada'));
            $this->setDataSaida($this->db->getRow('data_saida'));
            $this->setCampo1($this->db->getRow('campo1'));
            $this->setCampo2($this->db->getRow('campo2'));
            $this->setCampo3($this->db->getRow('campo3'));
            $this->setDataExame($this->db->getRow('data_exame'));
            $this->setDataExame2($this->db->getRow('data_exame2'));
            $this->setReservista($this->db->getRow('reservista'));
            $this->setEtnia($this->db->getRow('etnia'));
            $this->setDeficiencia($this->db->getRow('deficiencia'));
            $this->setCabelos($this->db->getRow('cabelos'));
            $this->setAltura($this->db->getRow('altura'));
            $this->setOlhos($this->db->getRow('olhos'));
            $this->setPeso($this->db->getRow('peso'));
            $this->setDefeito($this->db->getRow('defeito'));
            $this->setCipa($this->db->getRow('cipa'));
            $this->setAdNoturno($this->db->getRow('ad_noturno'));
            $this->setPlano($this->db->getRow('plano'));
            $this->setAssinatura($this->db->getRow('assinatura'));
            $this->setDistrato($this->db->getRow('distrato'));
            $this->setOutros($this->db->getRow('outros'));
            $this->setPis($this->db->getRow('pis'));
            $this->setDadaPis($this->db->getRow('dada_pis'));
            $this->setDataCtps($this->db->getRow('data_ctps'));
            $this->setSerieCtps($this->db->getRow('serie_ctps'));
            $this->setUfCtps($this->db->getRow('uf_ctps'));
            $this->setUfRg($this->db->getRow('uf_rg'));
            $this->setFgts($this->db->getRow('fgts'));
            $this->setInsalubridade($this->db->getRow('insalubridade'));
            $this->setTransporte($this->db->getRow('transporte'));
            $this->setAdicional($this->db->getRow('adicional'));
            $this->setTerceiro($this->db->getRow('terceiro'));
            $this->setNumPar($this->db->getRow('num_par'));
            $this->setDataIni($this->db->getRow('data_ini'));
            $this->setMedica($this->db->getRow('medica'));
            $this->setTipoPagamento($this->db->getRow('tipo_pagamento'));
            $this->setNomeBanco($this->db->getRow('nome_banco'));
            $this->setNumFilhos($this->db->getRow('num_filhos'));
            $this->setNomeFilhos($this->db->getRow('nome_filhos'));
            $this->setObservacao($this->db->getRow('observacao'));
            $this->setImpressos($this->db->getRow('impressos'));
            $this->setCampo4($this->db->getRow('campo4'));
            $this->setSisUser($this->db->getRow('sis_user'));
            $this->setDataCad($this->db->getRow('data_cad'));
            $this->setFoto($this->db->getRow('foto'));
            $this->setIdCooperativa($this->db->getRow('id_cooperativa'));
            $this->setCNome($this->db->getRow('c_nome'));
            $this->setCCpf($this->db->getRow('c_cpf'));
            $this->setCNascimento($this->db->getRow('c_nascimento'));
            $this->setCProfissao($this->db->getRow('c_profissao'));
            $this->setEEmpresa($this->db->getRow('e_empresa'));
            $this->setECnpj($this->db->getRow('e_cnpj'));
            $this->setERamo($this->db->getRow('e_ramo'));
            $this->setEEndereco($this->db->getRow('e_endereco'));
            $this->setEBairro($this->db->getRow('e_bairro'));
            $this->setECidade($this->db->getRow('e_cidade'));
            $this->setEEstado($this->db->getRow('e_estado'));
            $this->setECep($this->db->getRow('e_cep'));
            $this->setETel($this->db->getRow('e_tel'));
            $this->setERamal($this->db->getRow('e_ramal'));
            $this->setEFax($this->db->getRow('e_fax'));
            $this->setEEmail($this->db->getRow('e_email'));
            $this->setETempo($this->db->getRow('e_tempo'));
            $this->setEProfissao($this->db->getRow('e_profissao'));
            $this->setECargo($this->db->getRow('e_cargo'));
            $this->setERenda($this->db->getRow('e_renda'));
            $this->setEDataemissao($this->db->getRow('e_dataemissao'));
            $this->setEReferencia($this->db->getRow('e_referencia'));
            $this->setRNome($this->db->getRow('r_nome'));
            $this->setREndereco($this->db->getRow('r_endereco'));
            $this->setRBairro($this->db->getRow('r_bairro'));
            $this->setRCidade($this->db->getRow('r_cidade'));
            $this->setREstado($this->db->getRow('r_estado'));
            $this->setRCep($this->db->getRow('r_cep'));
            $this->setRTel($this->db->getRow('r_tel'));
            $this->setRRamal($this->db->getRow('r_ramal'));
            $this->setRFax($this->db->getRow('r_fax'));
            $this->setREmail($this->db->getRow('r_email'));
            $this->setDataalter($this->db->getRow('dataalter'));
            $this->setUseralter($this->db->getRow('useralter'));
            $this->setVale($this->db->getRow('vale'));
            $this->setSenhatv($this->db->getRow('senhatv'));
            $this->setDocumento($this->db->getRow('documento'));
            $this->setRhVale($this->db->getRow('rh_vale'));
            $this->setRhVinculo($this->db->getRow('rh_vinculo'));
            $this->setRhStatus($this->db->getRow('rh_status'));
            $this->setRhHorario($this->db->getRow('rh_horario'));
            $this->setRhSindicato($this->db->getRow('rh_sindicato'));
            $this->setRhCbo($this->db->getRow('rh_cbo'));
            $this->setAjudaCusto($this->db->getRow('ajuda_custo'));
            $this->setCota($this->db->getRow('cota'));
            $this->setParcelas($this->db->getRow('parcelas'));
            $this->setStatusReg($this->db->getRow('status_reg'));
            $this->setContratoMedico($this->db->getRow('contrato_medico'));
            $this->setMatricula($this->db->getRow('matricula'));
            $this->setNProcesso($this->db->getRow('n_processo'));
            $this->setEmail($this->db->getRow('email'));
            $this->setDataEmissao($this->db->getRow('data_emissao'));
            $this->setTipoSanguineo($this->db->getRow('tipo_sanguineo'));
            $this->setInPonto($this->db->getRow('in_ponto'));
            $this->setHoraRetirada($this->db->getRow('hora_retirada'));
            $this->setHoraAlmoco($this->db->getRow('hora_almoco'));
            $this->setHoraRetorno($this->db->getRow('hora_retorno'));
            $this->setHoraSaida($this->db->getRow('hora_saida'));
            $this->setIdCategoriaTrab($this->db->getRow('id_categoria_trab'));
            $this->setIdEstadoCivil($this->db->getRow('id_estado_civil'));
            $this->setCurriculo($this->db->getRow('curriculo'));

            return 1;

        }
        else{

            $this->error->setError($this->db->error->getError());            

            return 0;
        }

    }
    

    public function select(){

        //$this->createCoreClass();

        $this->db->setQuery("SELECT 
                                id_autonomo,
                                id_bolsista,
                                id_projeto,
                                id_regiao,
                                atividade,
                                salario,
                                localpagamento,
                                locacao,
                                id_unidade,
                                unidade,
                                nome,
                                sexo,
                                endereco,
                                numero,
                                complemento,
                                bairro,
                                cidade,
                                uf,
                                cep,
                                tel_fixo,
                                tel_cel,
                                tel_rec,
                                data_nasci,
                                naturalidade,
                                nacionalidade,
                                civil,
                                rg,
                                orgao,
                                data_rg,
                                cpf,
                                conselho,
                                titulo,
                                zona,
                                secao,
                                inss,
                                tipo_inss,
                                pai,
                                nacionalidade_pai,
                                mae,
                                nacionalidade_mae,
                                estuda,
                                data_escola,
                                escolaridade,
                                instituicao,
                                curso,
                                tipo_contratacao,
                                tvsorrindo,
                                banco,
                                agencia,
                                conta,
                                tipo_conta,
                                id_curso,
                                id_psicologia,
                                psicologia,
                                obs,
                                apolice,
                                status,
                                data_entrada,
                                data_saida,
                                campo1,
                                campo2,
                                campo3,
                                data_exame,
                                data_exame2,
                                reservista,
                                etnia,
                                deficiencia,
                                cabelos,
                                altura,
                                olhos,
                                peso,
                                defeito,
                                cipa,
                                ad_noturno,
                                plano,
                                assinatura,
                                distrato,
                                outros,
                                pis,
                                dada_pis,
                                data_ctps,
                                serie_ctps,
                                uf_ctps,
                                uf_rg,
                                fgts,
                                insalubridade,
                                transporte,
                                adicional,
                                terceiro,
                                num_par,
                                data_ini,
                                medica,
                                tipo_pagamento,
                                nome_banco,
                                num_filhos,
                                nome_filhos,
                                observacao,
                                impressos,
                                campo4,
                                sis_user,
                                data_cad,
                                foto,
                                id_cooperativa,
                                c_nome,
                                c_cpf,
                                c_nascimento,
                                c_profissao,
                                e_empresa,
                                e_cnpj,
                                e_ramo,
                                e_endereco,
                                e_bairro,
                                e_cidade,
                                e_estado,
                                e_cep,
                                e_tel,
                                e_ramal,
                                e_fax,
                                e_email,
                                e_tempo,
                                e_profissao,
                                e_cargo,
                                e_renda,
                                e_dataemissao,
                                e_referencia,
                                r_nome,
                                r_endereco,
                                r_bairro,
                                r_cidade,
                                r_estado,
                                r_cep,
                                r_tel,
                                r_ramal,
                                r_fax,
                                r_email,
                                dataalter,
                                useralter,
                                vale,
                                senhatv,
                                documento,
                                rh_vale,
                                rh_vinculo,
                                rh_status,
                                rh_horario,
                                rh_sindicato,
                                rh_cbo,
                                ajuda_custo,
                                cota,
                                parcelas,
                                status_reg,
                                contrato_medico,
                                matricula,
                                n_processo,
                                email,
                                data_emissao,
                                tipo_sanguineo,
                                in_ponto,
                                hora_retirada,
                                hora_almoco,
                                hora_retorno,
                                hora_saida,
                                id_categoria_trab,
                                id_estado_civil,
                                curriculo
                            FROM autonomo ",SELECT);

        if(class_exists('RhCltClass')){

            $id_regiao  = parent::getIdRegiao();
            $id_projeto = parent::getIdProjeto();
            $cpf = parent::getCpf();
            $nome = parent::getNome();

        }        
        else {

            $id_regiao  = $this->getIdRegiao();
            $id_projeto = $this->getIdProjeto();
            $cpf = $this->getCCpf();
            $nome = $this->getNome();

        }

        $id_autonomo = $this->getId();
        $id_bolsista = $this->getIdBolsista();
        
        $search = $this->getSearch();

        

        if(!empty($id_regiao) || !empty($id_projeto) || !empty($id_autonomo) || !empty($id_bolsista) || !empty($cpf) || !empty($search)){

            $this->db->setQuery("WHERE 1=1",WHERE,false);

            $this->db->setQuery((!empty($id_regiao) ? "AND id_regiao = {$id_regiao}" : ""),WHERE,true);        
            $this->db->setQuery((!empty($id_projeto) ? "AND id_projeto = {$id_projeto}" : ""),WHERE,true);        
            $this->db->setQuery((!empty($id_autonomo) ? "AND id_autonomo = {$id_autonomo}" : ""),WHERE,true);        
            $this->db->setQuery((!empty($id_bolsista) ? "AND id_bolsista = {$id_bolsista}" : ""),WHERE,true);        
            $this->db->setQuery((!empty($cpf) ? "OR REPLACE(REPLACE(cpf, '.', ''), '-', '') = '{$cpf}'" : ""),WHERE,true);   
            $this->db->setQuery((!empty($search) ? " $search " : ""),WHERE,true);   

        }

        if($this->db->setRs()) {
        
            if(!empty($collection)) {

                return $this->db->getCollection($collection);

            }
            else {
                
                return 1;
                
            }
            
        }
        else {
            
            return 0;
            
        }        

    }
    
    private function createCoreClass() {
        
        if(!isset($this->error)){
            
            include_once($_SERVER['DOCUMENT_ROOT'].PATH_CLASS.'ErrorClass.php');
            $this->error = new ErrorClass();        
            
        }
        
        if(!isset($this->db)){
            
            include_once($_SERVER['DOCUMENT_ROOT'].PATH_CLASS.'MySqlClass.php');

            $this->db = new MySqlClass();
            
        }
        
        if(!isset($this->date)){
            
            include_once($_SERVER['DOCUMENT_ROOT'].PATH_CLASS.'DateClass.php');

            $this->date = new DateClass();
            
        }
        
        
    }    
    
}