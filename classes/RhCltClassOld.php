<?php
/* 
 * Módulo Objeto da classe Mãe CltClass2 CLT orientado ao FrameWork do sistema da F71
 * Data Criação: 04/05/2015
 * Desenvolvimento: Jacques de Azevedo Nunes
 * e-mail: jacques@f71.com.br
 * Versão: 0.1 
 * 
 * Obs sobre a versão: 
 * 
 * Arquivos que Fazem parte do framework RhCltClass
 * 
 * RhBancoClass.php
 * RhCltClass.php
 * RhCursoClass.php
 * RhDocumentoClass.php
 * RhEventoClass.php
 * RhFeriasClass.php
 * RhGestaoClass.php
 * RhMetodosClass.php
 * RhProcessosInternoClass.php
 * RhProjetosClass.php
 * RhRecisaoClass.php
 * RhStatusClass.php
 * RhTipoPgClass.php
 * RhAutonomoClass.php
 * 
 * MySqlClass.php
 * ErrorClass.php
 * DateClass.php
 */

const PATH_CLASS = "/intranet/classes/"; 

const DIA = 0;
const MES = 1;
const ANO = 2; 

const DATA_CONTRATACAO = 0;
const DATA_CONTRATACAO_FMT = 1;
const DATA_CONTRATACAO_DIA = 2;
const DATA_CONTRATACAO_MES = 3;
const DATA_CONTRATACAO_ANO = 4;
const DATA_CONTRATACAO_STATUS = 5;

const VENCIDOS = 0;
const NA_DATA = 1;
const A_VENCER = 2;

function array_find($string, $array)
{
   foreach ($array as $key => $value)
   {
      if (strpos($value, $string) !== FALSE)
      {
         return $key;
         break;
      }
   }
}

class CltClass {
    
    protected $error;
    private   $db;
    private   $date;
    
    
    private   $clt_default = array(
                                'id_clt' => 0,            
                                'id_antigo' => 0,
                                'id_projeto' => 0,
                                'id_regiao' => 0,
                                'id_unidade' => 0,
                                'atividade' => '',
                                'salario' => '',
                                'localpagamento' => '',
                                'locacao' => '',
                                'unidade' => '',
                                'nome' => '',
                                'sexo' => '',
                                'endereco' => '',
                                'tipo_endereco' => '',
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
                                'pai' => '',
                                'nacionalidade_pai' => '',
                                'mae' => '',
                                'nacionalidade_mae' => '',
                                'estuda' => '',
                                'data_escola' => '',
                                'escolaridade' => '',
                                'instituicao' => '',
                                'curso'	=> '',
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
                                'status' => '',
                                'status_reg' => 0,
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
                                'data_pis' => '',
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
                                'dataalter' => '',
                                'useralter' => 0,
                                'vale' => 0,
                                'documento' => '',
                                'rh_vale' => 0,
                                'rh_vinculo' => 0,
                                'rh_status' => 0,
                                'rh_horario' => 0,
                                'rh_sindicato' => 0,
                                'rh_cbo' => 0,
                                'recolhimento_ir' => '',
                                'desconto_inss' => '',
                                'tipo_desconto_inss' => '',
                                'valor_desconto_inss' => '',
                                'trabalha_outra_empresa' => '',
                                'salario_outra_empresa' => '',
                                'desconto_outra_empresa' => '',
                                'vr' => 0,
                                'valor_vr' => 0.00,
                                'data_aviso' => '',
                                'data_demi' => '',
                                'status_admi' => '',
                                'status_demi' => 0,
                                'status_reg' => 0,
                                'matricula' => 0,
                                'n_processo' => 0,
                                'contrato_medico' => 0,
                                'email' => '',
                                'data_nasc_pai' => '',
                                'data_nasc_mae' => '',
                                'data_nasc_conjuge' => '',
                                'nome_conjuge' => '',
                                'nome_avo_h' => '',
                                'data_nasc_avo_h' => '',
                                'nome_avo_m' => '',
                                'data_nasc_avo_m' => '',
                                'nome_bisavo_h' => '',
                                'data_nasc_bisavo_h' => '',
                                'municipio_nasc' => '',
                                'uf_nasc' => '',
                                'data_emissao' => '',
                                'verifica_orgao' => 0,
                                'tipo_sanguineo' => '',
                                'ano_contribuicao' => 0,
                                'dtchegadapais' => '',
                                'cod_pais_rais' => 0,
                                'tipo_contrato' => 0,
                                'prazoexp' => '',
                                'id_estado_civil' => 0,
                                'id_municipio_nasc' => 0,
                                'id_municipio_end' => 0,
                                'id_pais_nasc' => 0,
                                'id_pais_nacionalidade' => 0,
                                'curriculo' => 0,
                                'vale_refeicao' => 0,
                                'vale_alimentacao' => 0,
                                'pensao_alimenticia' => '',
                                'status_contratacao' => '');
    
    
    private $clt = array();
   
    private $clt_save = array();
    
    private $select_tipo = 0;
    
    private $search = '';
    
    
    public function setSearch($value, $key, $operand, $inline, $add){
        
        $this->createCoreClass();

        $this->db->setSearch($value, $key, $operand, $inline, $add);
        
    }    
    
    public function setSelectTipo($value){
        
        $this->select_tipo = $value;

        
    }
   

    public function __construct() {
        
        try {

            $this->setDefault();
           
        } catch (Exception $ex) {
            
            print_array($ex);
            exit('Não foi possível atribuir valor default ao objeto clt');            

        }


    }
    
    /*
     * Sets da classe
     */    
      
    
    public function setDefault(){
        
        $this->createCoreClass();

        $this->clt_save = array();
        $this->clt = $this->clt_default;
        
        $this->search = '';
        
    }
    
    public function setIdClt($value){
        
        $this->clt_save['id_clt'] = ($this->clt["id_clt"] = $value);
        
    }
    
    public function setIdAntigo($value){
        
        $this->clt_save['id_antigo'] =($this->clt["id_antigo"] = $value);
        
    }

    public function setIdRegiao($value){
        
        $this->clt_save['id_regiao'] = ($this->clt["id_regiao"] = $value);
        
    } 

    public function setIdProjeto($value){
        
        $this->clt_save['id_projeto'] = ($this->clt["id_projeto"] = $value);
        
    }
    
    public function setIdUnidade($value){
        
        $this->clt_save['id_unidade'] = ($this->clt["id_unidade"] = $value);
        
    } 

    
    public function setSalario($value){
        
        $this->clt_save['salario'] = "'".($this->clt["salario"] = $value)."'";
        
    }

    public function setAtividade($value){
        
        $this->clt_save['atividade'] = "'".($this->clt["atividade"] = $value)."'";
        
    }

    public function setLocalpagamento($value){
        
        $this->clt_save['localpagamento'] = "'".($this->clt["localpagamento"] = $value)."'";
        
    }
    
    public function setLocacao($value){
        
        $this->clt_save['locacao'] = "'".($this->clt["locacao"] = $value)."'";
        
    }

    public function setUnidade($value){
        
        $this->clt_save['unidade'] = "'".($this->clt["unidade"] = $value)."'";
        
    }
   
    public function setNome($value){
        
        $this->clt_save['nome'] = "'".($this->clt["nome"] = $value)."'";
        
    }

    public function setSexo($value){
        
        $this->clt_save['sexo'] = "'".($this->clt["sexo"] = $value)."'";
        
    }
    
    public function setEndereco($value){
        
        $this->clt_save['endereco'] = "'".($this->clt["endereco"] = $value)."'";
        
    }
    
    public function setNumero($value){
        
        $this->clt_save['numero'] = "'".($this->clt["numero"] = $value)."'";
        
    }
    
    public function setComplemento($value){
        
        $this->clt_save['complemento'] = "'".($this->clt["complemento"] = $value)."'";
        
    }
    
    public function setTipoEndereco($value){
        
        $this->clt_save['tipo_endereco'] = "'".($this->clt["tipo_endereco"] = $value)."'";
        
    }

    public function setTipoNumero($value){
        
        $this->clt_save['tipo_numero'] = "'".($this->clt["tipo_numero"] = $value)."'";
        
    }
    
    public function setBairro($value){
        
        $this->clt_save['bairro'] = "'".($this->clt["bairro"] = $value)."'";
        
    }
    
    public function setCidade($value){
        
        $this->clt_save['cidade'] = "'".($this->clt["cidade"] = $value)."'";
        
    }

    public function setUF($value){
        
        $this->clt_save['uf'] = "'".($this->clt["uf"] = $value)."'";
        
    }
    
    public function setCEP($value){
        
        $this->clt_save['cep'] = "'".($this->clt["cep"] = $value)."'";
        
    }

    public function setTelFixo($value){
        
        $this->clt_save['tel_fixo'] = "'".($this->clt["tel_fixo"] = $value)."'";
        
    }

    public function setTelCel($value){
        
        $this->clt_save['tel_cel'] = "'".($this->clt["tel_cel"] = $value)."'";
        
    }
    
    public function setTelRec($value){
        
        $this->clt_save['tel_rec'] = "'".($this->clt["tel_rec"] = $value)."'";
        
    }

    public function setDataNasci($value){
        
        $this->date->setDate($this->clt["data_nasci"],$value);

        $this->clt_save['data_nasci'] = "'".($this->clt["data_nasci"])."'";
        
    }

    public function setNaturalidade($value){
        
        $this->clt_save['naturalidade'] = "'".($this->clt["naturalidade"] = $value)."'";
        
    }
    
    public function setNacionalidade($value){
        
        $this->clt_save['nacionalidade'] = "'".($this->clt["nacionalidade"] = $value)."'";
        
    }
    
    public function setCivil($value){
        
        $this->clt_save['civil'] = "'".($this->clt["civil"] = $value)."'";
        
    }
    
    public function setRg($value){
        
        $this->clt_save['rg'] = "'".($this->clt["rg"] = $value)."'";
        
    }
    
    public function setOrgao($value){
        
        $this->clt_save['orgao'] = "'".($this->clt["orgao"] = $value)."'";
        
    }

    public function setDataRg($value){
        
        $this->date->setDate($this->clt["data_rg"],$value);

        $this->clt_save['data_rg'] = "'".($this->clt["data_rg"])."'";
        
    }
    
    public function setCpf($value){
        
        $this->clt_save['cpf'] = "'".($this->clt["cpf"] = $value)."'";
        
    }
    
    public function setConselho($value){
        
        $this->clt_save['conselho'] = "'".($this->clt["conselho"] = $value)."'";
        
    }
    
    public function setTitulo($value){
        
        $this->clt_save['titulo'] = "'".($this->clt["titulo"] = $value)."'";
        
    }
    
    public function setZona($value){
        
        $this->clt_save['zona'] = "'".($this->clt["zona"] = $value)."'";
        
    }

    public function setSecao($value){
        
        $this->clt_save['secao'] = "'".($this->clt["secao"] = $value)."'";
        
    }
    
    public function setPai($value){
        
        $this->clt_save['pai'] = "'".($this->clt["pai"] = $value)."'";
        
    }
    
    public function setNacionalidadePai($value){
        
        $this->clt_save['nacionalidade_pai'] = "'".($this->clt["nacionalidade_pai"] = $value)."'";
        
    }
    
    
    public function setMae($value){
        
        $this->clt_save['mae'] = "'".($this->clt["mae"] = $value)."'";
        
    }

    public function setNacionalidadeMae($value){
        
        $this->clt_save['nacionalidade_mae'] = "'".($this->clt["nacionalidade_mae"] = $value)."'";
        
    }
    
    public function setEstuda($value){
        
        $this->clt_save['estuda'] = "'".($this->clt["estuda"] = $value)."'";
        
    }
    
    public function setDataEscola($value){
        
        
        $this->date->setDate($this->clt["data_escola"],$value);

        $this->clt_save['data_escola'] = "'".($this->clt["data_escola"])."'";
        
        
    }
  
    public function setEscolaridade($value){
        
        $this->clt_save['escolaridade'] = "'".($this->clt["escolaridade"] = $value)."'";
        
    }

    public function setInstituicao($value){
        
        $this->clt_save['instituicao'] = "'".($this->clt["instituicao"] = $value)."'";
        
    }
    
    public function setCurso($value){
        
        $this->clt_save['curso'] = "'".($this->clt["curso"] = $value)."'";
        
    }
    
    public function setTipoContratacao($value){
        
        $this->clt_save['tipo_contratacao'] = "'".($this->clt["tipo_contratacao"] = $value)."'";
        
    }
    
    public function setTvSorrindo($value){
        
        $this->clt_save['tvsorrindo'] = "'".($this->clt["tvsorrindo"] = $value)."'";
        
    }

    public function setBanco($value){
        
        $this->clt_save['banco'] = "'".($this->clt["banco"] = $value)."'";
        
    }
    
    public function setAgencia($value){
        
        $this->clt_save['agencia'] = "'".($this->clt["agencia"] = $value)."'";
        
    }
    
    public function setConta($value){
        
        $this->clt_save['conta'] = "'".($this->clt["conta"] = $value)."'";
        
    }
    
    public function setTipoConta($value){
        
        $this->clt_save['tipo_conta'] = "'".($this->clt["tipo_conta"] = $value)."'";
        
    }

    public function setIdCurso($value){
        
        $this->clt_save['id_curso'] = "'".($this->clt["id_curso"] = $value)."'";
        
    }
    
    public function setIdPsicologia($value){
        
        $this->clt_save['id_psicologia'] = "'".($this->clt["id_psicologia"] = $value)."'";
        
    }
    
    public function setPsicologia($value){
        
        $this->clt_save['psicologia'] = "'".($this->clt["psicologia"] = $value)."'";
        
    }
    
    public function setObs($value){
        
        $this->clt_save['obs'] = "'".($this->clt["obs"] = $value)."'";
        
    }
    
    public function setApolice($value){
        
        $this->clt_save['apolice'] = "'".($this->clt["apolice"] = $value)."'";
        
    }
    
    public function setStatus($value){
        
        $this->clt_save['status'] = "'".($this->clt["status"] = $value)."'";
        
    }
    
    public function setDataEntrada($value){
        
        $this->date->setDate($this->clt["data_entrada"],$value);

        $this->clt_save['data_entrada'] = "'".($this->clt["data_entrada"])."'";

    }
    
    public function setDataSaida($value){
        
        $this->date->setDate($this->clt["data_saida"],$value);

        $this->clt_save['data_saida'] = "'".($this->clt["data_saida"])."'";

    }
    
    public function setNumeroCtps($value){
        
        $this->setCampo1($value);
        
    }
    
    public function setCampo1($value){
        
        $this->clt_save['campo1'] = "'".($this->clt["campo1"] = $value)."'";
        
    }
    
    public function setCampo2($value){
        
        $this->clt_save['campo2'] = "'".($this->clt["campo2"] = $value)."'";
        
    }
    
    public function setMatriculaPorProjeto($value){
        
        $this->clt_save['campo3'] = "'".($this->clt["campo3"] = $value)."'";
        
    }
    
    public function setCampo3($value){
        
        $this->setMatriculaPorProjeto($value);
        
    }
    
    public function setDataExame($value){

        $this->date->setDate($this->clt["data_exame"],$value);

        $this->clt_save['data_exame'] = "'".($this->clt["data_exame"])."'";
        
    }

    public function setDataExame2($value){
        
        $this->date->setDate($this->clt["data_exame2"],$value);

        $this->clt_save['data_exame2'] = "'".($this->clt["data_exame2"])."'";

    }
   
    public function setReservista($value){
        
        $this->clt_save['reservista'] = "'".($this->clt["reservista"] = $value)."'";
        
    }
    
    public function setEscola($value){
        
        $this->clt_save['escola'] = "'".($this->clt["escola"] = $value)."'";
        
    }
    
    public function setEtnia($value){
        
        $this->clt_save['etnia'] = "'".($this->clt["etnia"] = $value)."'";
        
    }
    
    public function setDeficiencia($value){
        
        $this->clt_save['deficiencia'] = "'".($this->clt["deficiencia"] = $value)."'";
        
    }
    
    public function setCabelos($value){
        
        $this->clt_save['cabelos'] = "'".($this->clt["cabelos"] = $value)."'";
        
    }
    
    public function setAltura($value){
        
        $this->clt_save['altura'] = "'".($this->clt["altura"] = $value)."'";
        
    }

    public function setOlhos($value){
        
        $this->clt_save['olhos'] = "'".($this->clt["olhos"] = $value)."'";
        
    }
    
    public function setPeso($value){
        
        $this->clt_save['peso'] = "'".($this->clt["peso"] = $value)."'";
        
    }

    public function setDefeito($value){
        
        $this->clt_save['defeito'] = "'".($this->clt["defeito"] = $value)."'";
        
    }

    public function setCipa($value){
        
        $this->clt_save['cipa'] = "'".($this->clt["cipa"] = $value)."'";
        
    }
    
    public function setAdNoturno($value){
        
        $this->clt_save['ad_noturno'] = "'".($this->clt["ad_noturno"] = $value)."'";
        
    }

    public function setPlano($value){
        
        $this->clt_save['plano'] = ($this->clt["plano"] = $value);
        
    }
    
    public function setAssinatura($value){
        
        $this->clt_save['assinatura'] = ($this->clt["assinatura"] = $value);
        
    }
    
    public function setDistrato($value){
        
        $this->clt_save['distrato'] = ($this->clt["distrato"] = $value);
        
    }
    
    public function setOutros($value){
        
        $this->clt_save['outros'] = ($this->clt["outros"] = $value);
        
    }
    
    public function setPis($value){
        
        $this->clt_save['pis'] = "'".($this->clt["pis"] = $value)."'";
        
    }   
    
    public function setDataPis($value){
        
        $this->date->setDate($this->clt["data_pis"],$value);

        $this->clt_save['data_pis'] = "'".($this->clt["data_pis"] = $value)."'";
        
    }
    
    public function setDataCtps($value){
        
        $this->date->setDate($this->clt["data_ctps"],$value);

        $this->clt_save['data_ctps'] = "'".($this->clt["data_ctps"] = $value)."'";
        
    }
    
    
    public function setSerieCtps($value){
        
        $this->clt_save['serie_ctps'] = "'".($this->clt["serie_ctps"] = $value)."'";
        
    }    
    
    public function setUfCtps($value){
        
        $this->clt_save['uf_ctps'] = "'".($this->clt["uf_ctps"] = $value)."'";
        
    }    
    
    public function setUfRg($value){
        
        $this->clt_save['uf_rg'] = "'".($this->clt["uf_rg"] = $value)."'";
        
    } 
    
    public function setFgts($value){
        
        $this->clt_save['fgts'] = "'".($this->clt["fgts"] = $value)."'";
        
    }    
    
    public function setInsalubridade($value){
        
        $this->clt_save['insalubridade'] = ($this->clt["insalubridade"] = $value);
        
    }    
    
    public function setTransporte($value){
        
        $this->clt_save['transporte'] = "'".($this->clt["transporte"] = $value)."'";
        
    }    
    
    public function setAdicional($value){
        
        $this->clt_save['adicional'] = "'".($this->clt["adicional"] = $value)."'";
        
    }   
    
    public function setTerceiro($value){
        
        $this->clt_save['terceiro'] = "'".($this->clt["terceiro"] = $value)."'";
        
    }  
    
    public function setNumPar($value){
        
        $this->clt_save['num_par'] = "'".($this->clt["num_par"] = $value)."'";
        
    }    
    
    public function setDataIni($value){
        
        $this->date->setDate($this->clt["dataini"],$value);

        $this->clt_save['dataini'] = "'".($this->clt["dataini"])."'";
        
    }
    
    public function setMedica($value){
        
        $this->clt_save['medica'] = "'".($this->clt["medica"] = $value)."'";
        
    } 
    
    public function setTipoPagamento($value){
        
        $this->clt_save['tipo_pagamento'] = "'".($this->clt["tipo_pagamento"] = $value)."'";
        
    }  
    
    public function setNomeBanco($value){
        
        $this->clt_save['nome_banco'] = "'".($this->clt["nome_banco"] = $value)."'";
        
    }    
    
    public function setNumFilhos($value){
        
        $this->clt_save['num_filhos'] = "'".($this->clt["num_filhos"] = $value)."'";
        
    }   
    
    public function setNomeFilhos($value){
        
        $this->clt_save['nome_filhos'] = "'".($this->clt["nome_filhos"] = $value)."'";
        
    }  
    
    public function setObservacao($value){
        
        $this->clt_save['observacao'] = "'".($this->clt["observacao"] = $value)."'";
        
    }  
    
    public function setImpressos($value){
        
        $this->clt_save['impressos'] = "'".($this->clt["impressos"] = $value)."'";
        
    }    
    
    public function setCampo4($value){
        
        $this->clt_save['campo4'] = "'".($this->clt["campo4"] = $value)."'";
        
    }    
    
    public function setSisUser($value){
        
        $this->clt_save['sis_user'] = "'".($this->clt["sis_user"] = $value)."'";
        
    }  
    
    
    public function setDataCad($value){
        
        $this->date->setDate($this->clt["data_cad"],$value);

        $this->clt_save['data_cad'] = "'".($this->clt["data_cad"])."'";
        
    }    
    
    public function setFoto($value){
        
        $this->clt_save['foto'] = "'".($this->clt["foto"] = $value)."'";
        
    }    

    public function setDataAlter($value){

        $this->date->setDate($this->clt["dataalter"],$value);

        $this->clt_save['dataalter'] = "'".($this->clt["dataalter"])."'";
        
    }  
    
    public function setUserAlter($value){
        
        $this->clt_save['useralter'] = "'".($this->clt["useralter"] = $value)."'";
        
    }    
    
    public function setVale($value){
        
        $this->clt_save['vale'] = "'".($this->clt["vale"] = $value)."'";
        
    }    
    
    public function setDocumento($value){
        
        $this->clt_save['documento'] = "'".($this->clt["documento"] = $value)."'";
        
    } 
    
    public function setRhVale($value){
        
        $this->clt_save['rh_vale'] = "'".($this->clt["rh_vale"] = $value)."'";
        
    }  
    
    public function setRhVinculo($value){
        
        $this->clt_save['rh_vinculo'] = "'".($this->clt["rh_vinculo"] = $value)."'";
        
    }   
    
    public function setRhStatus($value){
        
        $this->clt_save['rh_status'] = "'".($this->clt["rh_status"] = $value)."'";
        
    }    
    
    public function setRhHorario($value){
        
        $this->clt_save['rh_horario'] = "'".($this->clt["rh_horario"] = $value)."'";
        
    }   
    
    public function setRhSindicato($value){
        
        $this->clt_save['rh_sindicato'] = "'".($this->clt["rh_sindicato"] = $value)."'";
        
    }    
    
    public function setRhCbo($value){
        
        $this->clt_save['rh_cbo'] = "'".($this->clt["rh_cbo"] = $value)."'";
        
    }    
    
    public function setRecolhimentoIr($value){
        
        $this->clt_save['recolhimento_ir'] = "'".($this->clt["recolhimento_ir"] = $value)."'";
        
    }    
    
    public function setDescontoInss($value){
        
        $this->clt_save['desconto_inss'] = "'".($this->clt["desconto_inss"] = $value)."'";
        
    }    
    
    public function setTipoDescontoInss($value){
        
        $this->clt_save['tipo_desconto_inss'] = "'".($this->clt["tipo_desconto_inss"] = $value)."'";
        
    }    
    
    public function setValorDescontoInss($value){
        
        $this->clt_save['valor_desconto_inss'] = "'".($this->clt["valor_desconto_inss"] = $value)."'";
        
    }    
    
    public function setTrabalhaOutraEmpresa($value){
        
        $this->clt_save['trabalha_outra_empresa'] = "'".($this->clt["trabalha_outra_empresa"] = $value)."'";
        
    }    
    
    public function setSalarioOutraEmpresa($value){
        
        $this->clt_save['salario_outra_empresa'] = "'".($this->clt["salario_outra_empresa"] = $value)."'";
        
    }    
    
    public function setDescontoOutraEmpresa($value){
        
        $this->clt_save['desconto_outra_empresa'] = "'".($this->clt["desconto_outra_empresa"] = $value)."'";
        
    }    
    
    public function setVr($value){
        
        $this->clt_save['vr'] = "'".($this->clt["vr"] = $value)."'";
        
    }
    
    public function setValorVr($value){
        
        $this->clt_save['valor_vr'] = "'".($this->clt["valor_vr"] = $value)."'";
        
    }    
    
    public function setDataAviso($value){
        
        $this->date->setDate($this->clt["data_aviso"],$value);

        $this->clt_save['data_aviso'] = "'".($this->clt["data_aviso"])."'";
        
    }      

    
   public function setDataDemi($value){
       
        $this->date->setDate($this->clt["data_demi"],$value);

        $this->clt_save['data_demi'] = "'".($this->clt["data_demi"])."'";
        
    }  
    
    public function setStatusAdmi($value){
        
        $this->clt_save['status_admi'] = "'".($this->clt["status_admi"] = $value)."'";
        
    }    
    
    public function setStatusDemi($value){
        
        $this->clt_save['status_demi'] = "'".($this->clt["status_demi"] = $value)."'";
        
    }    
    
   
    public function setMatricula($value){
        
        $this->clt_save['matricula'] = "'".($this->clt["matricula"] = $value)."'";
        
    }    
    
    public function setNProcesso($value){
        
        $this->clt_save['n_processo'] = "'".($this->clt["n_processo"] = $value)."'";
        
    }    
    
    public function setContratoMedico($value){
        
        $this->clt_save['contrato_medico'] = "'".($this->clt["contrato_medico"] = $value)."'";
        
    }    
    
    public function setEmail($value){
        
        $this->clt_save['email'] = "'".($this->clt["email"] = $value)."'";
        
    }    
    
    public function setDataNascPai($value){
        
        $this->date->setDate($this->clt["data_nasc_pai"],$value);

        $this->clt_save['data_nasc_pai'] = "'".($this->clt["data_nasc_pai"])."'";
        
    }    
    
    public function setDataNascMae($value){
        
        $this->date->setDate($this->clt["data_nasc_mae"],$value);

        $this->clt_save['data_nasc_mae'] = "'".($this->clt["data_nasc_mae"])."'";
        
    }    
    
    public function setDataNascConjuge($value){
        
        $this->date->setDate($this->clt["data_nasc_conjuge"],$value);

        $this->clt_save['data_nasc_conjuge'] = "'".($this->clt["data_nasc_conjuge"])."'";
        
    }    
    
    public function setNomeConjuge($value){
        
        $this->clt_save['nome_conjuge'] = "'".($this->clt["nome_conjuge"] = $value)."'";
        
    }    
    
    public function setNomeAvoH($value){
        
        $this->clt_save['nome_avo_h'] = "'".($this->clt["nome_avo_h"] = $value)."'";
        
    }    
    
    public function setDataNascAvoH($value){
        
        $this->date->setDate($this->clt["data_nasc_avo_m"],$value);

        $this->clt_save['data_nasc_avo_m'] = "'".($this->clt["data_nasc_avo_m"])."'";
        
    }    
    
    public function setNomeAvoM($value){
        
        $this->clt_save['nome_avo_m'] = "'".($this->clt["nome_avo_m"] = $value)."'";
        
    }    
    
    
    public function setDataNascAvoM($value){
        
        $this->date->setDate($this->clt["data_nasc_avo_m"],$value);

        $this->clt_save['data_nasc_avo_m'] = "'".($this->clt["data_nasc_avo_m"])."'";
        
    }    
    
    public function setNomeBisavoH($value){
        
        $this->date->setDate($this->clt["nome_bisavo_h"],$value);

        $this->clt_save['nome_bisavo_h'] = "'".($this->clt["nome_bisavo_h"])."'";
        
    }    
    
    public function setDataNascBisavoH($value){
        
        $this->date->setDate($this->clt["data_nasc_bisavo_h"],$value);

        $this->clt_save['data_nasc_bisavo_h'] = "'".($this->clt["data_nasc_bisavo_h"])."'";
        
    }    
    
    public function setNomeBisavoM($value){
        
        $this->clt_save['nome_bisavo_m'] = "'".($this->clt["nome_bisavo_m"] = $value)."'";
        
    }    
    
    public function setDataNascBisavoM($value){
        
        $this->date->setDate($this->clt["data_nasc_bisavo_m"],$value);

        $this->clt_save['data_nasc_bisavo_m'] = "'".($this->clt["data_nasc_bisavo_m"])."'";
        
    }    
    
    
    public function setMunicipioNasc($value){
        
        $this->clt_save['municipionasc'] = "'".($this->clt["municipionasc"] = $value)."'";
        
    }    

   
    public function setUfNasc($value){
        
        $this->clt_save['uf_nasc'] = "'".($this->clt["uf_nasc"] = $value)."'";
        
    }    
    
    public function setDataEmissao($value){
        
        $this->date->setDate($this->clt["data_emissao"],$value);

        $this->clt_save['data_emissao'] = "'".($this->clt["data_emissao"])."'";
        
    }    
    
    public function setVerificaOrgao($value){
        
        $this->clt_save['verifica_orgao'] = "'".($this->clt["verifica_orgao"] = $value)."'";
        
    }    
    
    public function setTipoSanguineo($value){
        
        $this->clt_save['tipo_sanquineo'] = "'".($this->clt["tipo_sanquineo"] = $value)."'";
        
    }    
    
    public function setAnoContribuicao($value){
        
        $this->clt_save['ano_contribuicao'] = "'".($this->clt["ano_contribuicao"] = $value)."'";
        
    }    
    
    public function setDtChegadaPais($value){
        
        $this->date->setDate($this->clt["dtchegadapais"],$value);

        $this->clt_save['dtchegadapais'] = "'".($this->clt["dtchegadapais"])."'";
        
    }    
    
    public function setCodPaisRais($value){
        
        $this->clt_save['cod_pais_rais'] = "'".($this->clt["cod_pais_rais"] = $value)."'";
        
    }    
    
    public function setTipoContrato($value){
        
        $this->clt_save['tipo_contrato'] = "'".($this->clt["tipo_contrato"] = $value)."'";
        
    }    
    
    public function setPrazoExp($value){
        
        $this->clt_save['prazoexp'] = "'".($this->clt["prazoexp"] = $value)."'";
        
    }    
    
    public function setIdEstadoCicil($value){
        
        $this->clt_save['id_estado_civil'] = "'".($this->clt["id_estado_civil"] = $value)."'";
        
    }    
    
    public function setIdMunicipioNasc($value){
        
        $this->clt_save['id_municipio_nasc'] = "'".($this->clt["id_municipio_nasc"] = $value)."'";
        
    }    
    
    public function setIdMunicipioEnd($value){
        
        $this->clt_save['id_municipio_end'] = "'".($this->clt["id_municipio_end"] = $value)."'";
        
    }    
    
    public function setIdPaisNasc($value){
        
        $this->clt_save['id_pais_nasc'] = "'".($this->clt["id_pais_nasc"] = $value)."'";
        
    }    
    
    public function setIdPaisNacionalidade($value){
        
        $this->clt_save['id_pais_nacionalidade'] = "'".($this->clt["id_pais_nacionalidade"] = $value)."'";
        
    }    
    
    public function setCurriculo($value){
        
        $this->clt_save['curriculo'] = "'".($this->clt["curriculo"] = $value)."'";
        
    }    
    
    public function setValeRefeicao($value){
        
        $this->clt_save['vale_refeicao'] = "'".($this->clt["vale_refeicao"] = $value)."'";
        
    }    
    
    public function setValeAlimentacao($value){
        
        $this->clt_save['vale_alimentacao'] = "'".($this->clt["vale_alimentacao"] = $value)."'";
        
    }    
    
    public function setPensaoAlimenticia($value){
        
        $this->clt_save['pensao_alimenticia'] = "'".($this->clt["pensao_alimenticia"] = $value)."'";
        
    } 
    
    public function setStatusContratacao($value){
        
        $this->clt_save['status_contratacao'] = "'".($this->clt["status_contratacao"] = $value)."'";
        
    }
    
    public function setOrder($value) {
        
        $this->createCoreClass();
        $this->db->setQuery("ORDER BY $value",ORDER);
        
    }
    
    public function setAddValueExt($method,$value){

        foreach ($class_vars as $name => $value) {
            
            //echo "$name : $value</br>";
            
        }        
        
            
        foreach ($this as $key => $obj) {
            
            //$this->$key->$method($value);
            // echo "$key</br>";
             
                    
        }
        
    }
    
    
    /*
     * Gets da classe
     */    
    
    public function getError(){
        
        return $this->error->getError();
        
    }
    
    public function getSearch(){
        
        return $this->db->getSearch();
        
    }

   
    public function getIdClt(){
        
       return $this->clt["id_clt"];
        
    }
    
    public function getIdAntigo(){
        
       return $this->clt["id_antigo"];
        
    }

    public function getIdProjeto(){
        
       return $this->clt["id_projeto"];
        
    }
    
    public function getIdRegiao(){
        
       return $this->clt["id_regiao"];
        
    } 
         
    public function getIdUnidade(){
        
       return $this->clt["id_unidade"];
        
    } 

    
    public function getSalario(){
        
       return $this->clt["salario"];
        
    }

    public function getAtividade(){
        
       return $this->clt["atividade"];
        
    }

    public function getLocalpagamento(){
        
       return $this->clt["localpagamento"];
        
    }
    
    public function getLocacao(){
        
       return $this->clt["locacao"];
        
    }
    
    
    public function getUnidade(){
        
       return $this->clt["unidade"];
        
    }
   
    public function getNome(){
        
       return $this->clt["nome"];
        
    }

    public function getSexo(){
        
       return $this->clt["sexo"];
        
    }
    
    public function getEndereco(){
        
       return $this->clt["Endereco"];
        
    }
    
    public function getNumero(){
        
       return $this->clt["numero"];
        
    }
    
    public function getComplemento(){
        
       return $this->clt["complemento"];
        
    }
    
    public function getTipoEndereco(){
        
       return $this->clt["tipo_endereco"];
        
    }

    public function getTipoNumero(){
        
       return $this->clt["tipo_numero"];
        
    }
    
    public function getBairro(){
        
       return $this->clt["bairro"];
        
    }
    
    public function getCidade(){
        
       return $this->clt["cidade"];
        
    }

    public function getUF(){
        
       return $this->clt["uf"];
        
    }
    
    public function getCEP(){
        
       return $this->clt["cep"];
        
    }

    public function getTelFixo(){
        
       return $this->clt["tel_fixo"];
        
    }

    public function getTelCel(){
        
       return $this->clt["tel_cel"];
        
    }
    
    public function getTelRec(){
        
       return $this->clt["tel_rec"];
        
    }

    public function getDataNasci(){
        
       return $this->clt["data_nasci"];
        
    }

    public function getNaturalidade(){
        
       return $this->clt["naturalidade"];
        
    }
    
    public function getNacionalidade(){
        
       return $this->clt["nacionalidade"];
        
    }
    
    public function getCivil(){
        
       return $this->clt["civil"];
        
    }
    
    public function getRg(){
        
       return $this->clt["rg"];
        
    }
    
    public function getOrgao(){
        
       return $this->clt["orgao"];
        
    }

    public function getDataRg(){
        
       return $this->clt["data_rg"];
        
    }
    
    public function getCpf(){
        
       return $this->clt["cpf"];
        
    }
    
    public function getConselho(){
        
       return $this->clt["conselho"];
        
    }
    
    public function getTitulo(){
        
       return $this->clt["titulo"];
        
    }
    
    public function getZona(){
        
       return $this->clt["zona"];
        
    }

    public function getSecao(){
        
       return $this->clt["secao"];
        
    }
    
    public function getPai(){
        
       return $this->clt["pai"];
        
    }
    
    public function getNacionalidadePai(){
        
       return $this->clt["nacionalidade_pai"];
        
    }
    
    
    public function getMae(){
        
       return $this->clt["mae"];
        
    }
    
    public function getNacionalidadeMae(){
        
       return $this->clt["nacionalidade_mae"];
        
    }
    
    
    public function getEstuda(){
        
       return $this->clt["estuda"];
        
    }
    
    public function getDataEscola(){
        
       return $this->clt["data_escola"];
        
    }    
    
  
    public function getEscolaridade(){
        
       return $this->clt["escolaridade"];
        
    }

    public function getInstituicao(){
        
       return $this->clt["instituicao"];
        
    }
    
    public function getCurso(){
        
       return $this->clt["curso"];
        
    }
    
    public function getTipoContratacao(){
        
       return $this->clt["tipo_contratacao"];
        
    }
    
    public function getTvSorrindo(){
        
       return $this->clt["tvsorrindo"];
        
    }

    public function getBanco(){
        
       return $this->clt["banco"];
        
    }
    
    public function getAgencia(){
        
       return $this->clt["agencia"];
        
    }
    
    public function getConta(){
        
       return $this->clt["conta"];
        
    }
    
    public function getTipoConta(){
        
       return $this->clt["tipo_conta"];
        
    }

    public function getIdCurso(){
        
       return $this->clt["id_curso"];
        
    }
    
    public function getIdPsicologia(){
        
       return $this->clt["id_psicologia"];
        
    }
    
    public function getPsicologia(){
        
       return $this->clt["psicologia"];
        
    }
    
    public function getObs(){
        
       return $this->clt["obs"];
        
    }
    
    public function getApolice(){
        
       return $this->clt["apolice"];
        
    }
    
    public function getStatus(){
        
       return $this->clt["status"];
        
    }

    public function getTipo(){
        
       return $this->clt["tipo"];
        
    }
    
    public function getDataEntrada($format){
        
        return $this->date->getDate($this->clt["data_entrada"],$format);
        
    }
    
   
    public function getDataSaida($format){
        
        return $this->date->getDate($this->clt["data_saida"],$format);
        
    }
    
    
    public function getNumeroCtps(){
        
       return $this->getCampo1();
        
    }
    
    public function getMatriculaPorProjeto(){
        
       return $this->getCampo3();
        
    }

    public function getCampo1(){
        
        
       return $this->clt["campo1"];
        
    }
    
    
    public function getCampo2(){
        
       return $this->clt["campo2"];
        
    }
    
    
    public function getCampo3(){
        
       return $this->clt["campo3"];
        
    }
    
    public function getDataExame($format){
        
        return $this->date->getDate($this->clt["data_exame"],$format);
        
    }

    public function getDataExame2(){
        
        return $this->date->getDate($this->clt["data_exame2"],$format);
        
    }
    
   
    public function getReservista(){
        
       return $this->clt["reservista"];
        
    }
    
    public function getEscola(){
        
       return $this->clt["escola"];
        
    }
    
    public function getEtnia(){
        
       return $this->clt["Etnia"];
        
    }
    
    public function getDeficiencia(){
        
       return $this->clt["deficiencia"];
        
    }
    
    public function getCabelos(){
        
       return $this->clt["cabelos"];
        
    }
    
    public function getAltura(){
        
       return $this->clt["altura"];
        
    }

    public function getOlhos(){
        
       return $this->clt["olhos"];
        
    }
    
    public function getPeso(){
        
       return $this->clt["peso"];
        
    }

    public function getDefeito(){
        
       return $this->clt["defeito"];
        
    }

    public function getCipa(){
        
       return $this->clt["cipa"];
        
    }
    
    public function getAdNoturno(){
        
       return $this->clt["ad_noturno"];
        
    }

    public function getPlano(){
        
       return $this->clt["plano"];
        
    }
    
    public function getAssinatura(){
        
       return $this->clt["assinatura"];
        
    }
    
    public function getDistrato(){
        
       return $this->clt["distrato"];
        
    }
    
    public function getOutros(){
        
       return $this->clt["outros"];
        
    }
    
    public function getPis(){
        
       return $this->clt["pis"];
        
    }   
    
    public function getDataPis(){
        
       return $this->date->getDate($this->clt["data_pis"],$format);
        
    }    
    
    public function getDataCtps(){

       return $this->date->getDate($this->clt["data_ctps"],$format);
        
    }   
    
    public function getSerieCtps(){
        
       return $this->clt["serie_ctps"];
        
    }    
    
    public function getUfCtps(){
        
       return $this->clt["uf_ctps"];
        
    }    
    
    public function getUfRg(){
        
       return $this->clt["uf_rg"];
        
    } 
    
    public function getFgts(){
        
       return $this->clt["fgts"];
        
    }    
    
    public function getInsalubridade(){
        
       return $this->clt["insalubridade"];
        
    }    
    
    public function getTransporte(){
        
       return $this->clt["transporte"];
        
    }    
    
    public function getAdicional(){
        
       return $this->clt["adicional"];
        
    }   
    
    public function getTerceiro(){
        
       return $this->clt["terceiro"];
        
    }  
    
    public function getNumPar(){
        
       return $this->clt["num_par"];
        
    }    
    
    public function getDataIni(){
        
       return $this->date->getDate($this->clt["DataIni"],$format);
        
    }    
    
    public function getMedica(){
        
       return $this->clt["medica"];
        
    } 
    
    public function getTipoPagamento(){
        
       return $this->clt["tipo_pagamento"];
        
    }  
    
    public function getNomeBanco(){
        
       return $this->clt["nome_banco"];
        
    }    
    
    public function getNumFilhos(){
        
       return $this->clt["num_filhos"];
        
    }   
    
    public function getNomeFilhos(){
        
       return $this->clt["nome_filhos"];
        
    }  
    
    public function getObservacao(){
        
       return $this->clt["observacao"];
        
    }  
    
    public function getImpressos(){
        
       return $this->clt["impressos"];
        
    }    
    
    public function getCampo4(){
        
       return $this->clt["campo4"];
        
    }    
    
    public function getSisUser(){
        
       return $this->clt["sis_user"];
        
    }  
    
    public function getDataCad(){
        
       return $this->date->getDate($this->clt["data_cad"],$format);
        
    }  
    
    public function getFoto(){
        
       return $this->clt["foto"];
        
    }    
    
    public function getDataAlter($format){
        
       return $this->date->getDate($this->clt["dataalter"],$format);

    }  
    
    public function getUserAlter(){
        
       return $this->date->getDate($this->clt["useralter"],$format);
        
    }    
    
    public function getVale(){
        
       return $this->clt["vale"];
        
    }    
    
    public function getDocumento(){
        
       return $this->clt["documento"];
        
    } 
    
    public function getRhVale(){
        
       return $this->clt["rh_vale"];
        
    }  
    
    public function getRhVinculo(){
        
       return $this->clt["rh_vinculo"];
        
    }   
    
    public function getRhStatus(){
        
       return $this->clt["rh_status"];
        
    }    
    
    public function getRhHorario(){
        
       return $this->clt["rh_horario"];
        
    }   
    
    public function getRhSindicato(){
        
       return $this->clt["rh_sindicato"];
        
    }    
    
    public function getRhCbo(){
        
       return $this->clt["rh_cbo"];
        
    }    
    
    public function getRecolhimentoIr(){
        
       return $this->clt["recolhimento_ir"];
        
    }    
    
    public function getDescontoInss(){
        
       return $this->clt["desconto_inss"];
        
    }    
    
    public function getTipoDescontoInss(){
        
       return $this->clt["tipo_desconto_inss"];
        
    }    
    
    public function getValorDescontoInss(){
        
       return $this->clt["valor_desconto_inss"];
        
    }    
    
    public function getTrabalhaOutraEmpresa(){
        
       return $this->clt["trabalha_outra_empresa"];
        
    }    
    
    public function getSalarioOutraEmpresa(){
        
       return $this->clt["salario_outra_empresa"];
        
    }    
    
    public function getDescontoOutraEmpresa(){
        
       return $this->clt["desconto_outra_empresa"];
        
    }    
    
    public function getVr(){
        
       return $this->clt["vr"];
        
    }
    
    public function getValorVr(){
        
       return $this->clt["valor_vr"];
        
    }    

    public function getDataAviso(){
        
       return $this->date->getDate($this->clt["data_aviso"],$format);
        
    }     
    
    public function getDataDemi(){
        
       return $this->date->getDate($this->clt["data_demi"],$format);
        
    }    
    
    public function getStatusAdmi(){
        
       return $this->clt["status_admi"];
        
    }    
    
    
    public function getStatusDemi(){
        
       return $this->clt["status_demi"];
        
    }    
    
   
    public function getMatricula($format){
        
      return isset($format) ? vsprintf($format, $this->clt["matricula"]) : $this->clt["matricula"];
        
    }    
    
    public function getNProcesso($format){
        
      return isset($format) ? vsprintf($format, $this->clt["n_processo"]) : $this->clt["n_processo"];
        
    }    
    
    public function getContratoMedico(){
        
       return $this->clt["contrato_medico"];
        
    }    
    
    public function getEmail(){
        
       return $this->clt["email"];
        
    }    
    
    public function getDataNascPai(){
        
       return $this->date->getDate($this->clt["data_nasc_pai"],$format);
        
    }    
    
    public function getDataNascMae(){

       return $this->date->getDate($this->clt["data_nasc_mae"],$format);
        
    }    
    
    public function getDataNascConjuge(){

       return $this->date->getDate($this->clt["data_nasc_conjuge"],$format);
        
    }    
    
    public function getNomeConjuge(){
        
       return $this->clt["nome_conjuge"];
        
    }    
    
    public function getNomeAvoH(){
        
       return $this->clt["nome_avo_h"];
        
    }    
    
    public function getDataNascAvoH(){
        
       return $this->date->getDate($this->clt["data_nasc_avo_m"],$format);
        
    }    
    
    public function getNomeAvoM(){
        
       return $this->clt["nome_avo_m"];
        
    }    
    
    
    public function getDataNascAvoM(){
        
       return $this->date->getDate($this->clt["data_nasc_avo_m"],$format);
        
    }    
    
    public function getNomeBisavoH(){
        
       return $this->clt["nome_bisavo_h"];
        
    }    
    
    public function getDataNascBisavoH(){
        
       return $this->date->getDate($this->clt["data_nasc_bisavo_h"],$format);
        
    }    
    
    public function getNomeBisavoM(){
        
        return $this->clt["nome_bisavo_m"];
        
    }    
    
    public function getDataNascBisavoM(){
        
       return $this->date->getDate($this->clt["data_nasc_bisavo_m"],$format);
        
    }    
    
    
    public function getMunicipioNasc(){
        
       return $this->clt["MunicipioNasc"];
        
    }    
    
    public function getUfNasc(){
        
       return $this->clt["uf_nasc"];
        
    }    
    
    public function getDataEmissao(){
        
       return $this->date->getDate($this->clt["data_emissao"],$format);
        
    }    
    
    public function getVerificaOrgao(){
        
       return $this->clt["verifica_orgao"];
        
    }    
    
    public function getTipoSanguineo(){
        
       return $this->clt["tipo_sanquineo"];
        
    }    
    
    public function getAnoContribuicao(){
        
       return $this->clt["ano_contribuicao"];
        
    }    
    
    public function getDtChegadaPais(){
        
       return $this->date->getDate($this->clt["dtchegadapais"],$format);
        
    }    
    
    public function getCodPaisRais(){
        
       return $this->clt["cod_pais_rais"];
        
    }    
    
    public function getTipoContrato(){
        
       return $this->clt["tipo_contrato"];
        
    }    
    
    public function getPrazoExp(){
        
       return $this->clt["prazoexp"];
        
    }    
    
    public function getIdEstadoCicil(){
        
       return $this->clt["id_estado_civil"];
        
    }    
    
    public function getIdMunicipioNasc(){
        
       return $this->clt["id_municipio_nasc"];
        
    }    
    
    public function getIdMunicipioEnd(){
        
       return $this->clt["id_municipio_end"];
        
    }    
    
    public function getIdPaisNasc(){
        
       return $this->clt["id_pais_nasc"];
        
    }    
    
    public function getIdPaisNacionalidade(){
        
       return $this->clt["id_pais_nacionalidade"];
        
    }    
    
    public function getCurriculo(){
        
       return $this->clt["curriculo"];
        
    }    
    
    public function getValeRefeicao(){
        
       return $this->clt["vale_refeicao"];
        
    }    
    
    public function getValeAlimentacao(){
        
       return $this->clt["vale_alimentacao"];
        
    }    
    
    public function getPensaoAlimenticia(){
        
       return $this->clt["pensao_alimenticia"];
        
    }    
    
    public function getStatusContratacao(){
        
       return $this->clt["status_contratacao"];
        
    }    
    
    public function getTot(){
        
       return $this->db->getNumRows();
        
    }
    
    public function getCollection($value){
        
        return $this->db->getCollection($value);
        
    }
    
    public function eRecisao(){

            return ($this->getStatus() == "recisao") ? false : true;        
            
    }   
    
        
    /*
     * Set de consultas
     * 
     * Retorna um vetor com informações da contratação do funcionário
     */
    
    
     private function selectCheck(){
        
        foreach ($this->clt_save as $key => $value) {
            
            switch ($key) {

                case 'id_regiao':
                    
                    if(empty($this->clt_save["{$key}"])){
                        
                       
                        $this->error->setError("Campo {$key} obrigatório para essa inclusão");
                        
                        return 0;
                        
                    }
                    
                    break;

                default:
                    break;
            }
            
        }
        
        return 1;
        
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
    
    public function select(){

        $this->createCoreClass();
        
        $this->db->setQuery("SELECT "
                . "     id_clt,"
                . "     id_antigo,"
                . "     id_projeto,"
                . "     id_regiao,"
                . "     id_unidade,"
                . "     atividade,"
                . "     salario,"
                . "     localpagamento,"
                . "     locacao,"
                . "     unidade,"
                . "     nome,"
                . "     sexo,"
                . "     endereco,"
                . "     tipo_endereco,"
                . "     numero,"
                . "     complemento,"
                . "     bairro,"
                . "     cidade,"
                . "     uf,"
                . "     cep,"
                . "     tel_fixo,"
                . "     tel_cel,"
                . "     tel_rec,"
                . "     data_nasci,"
                . "     naturalidade,"
                . "     nacionalidade,"
                . "     civil,"
                . "     rg,"
                . "     orgao,"
                . "     data_rg,"
                . "     cpf,"
                . "     conselho,"
                . "     titulo,"
                . "     zona,"
                . "     secao,"
                . "     pai,"
                . "     nacionalidade_pai,"
                . "     mae,"
                . "     nacionalidade_mae,"
                . "     estuda,"
                . "     data_escola,"
                . "     escolaridade,"
                . "     instituicao,"
                . "     curso,"
                . "     tipo_contratacao,"
                . "     tvsorrindo,"
                . "     banco,"
                . "     agencia,"
                . "     conta,"
                . "     tipo_conta,"
                . "     id_curso,"
                . "     id_psicologia,"
                . "     psicologia,"
                . "     obs,"
                . "     apolice,"
                . "     status,"
                . "     status_reg,"
                . "     data_entrada,"
                . "     data_saida,"
                . "     campo1,"
                . "     campo2,"
                . "     campo3,"
                . "     data_exame,"
                . "     data_exame2,"
                . "     reservista,"
                . "     etnia,"
                . "     deficiencia,"
                . "     cabelos,"
                . "     altura,"
                . "     olhos,"
                . "     peso,"
                . "     defeito,"
                . "     cipa,"
                . "     ad_noturno,"
                . "     plano,"
                . "     assinatura,"
                . "     distrato,"
                . "     outros,"
                . "     pis,"
                . "     dada_pis,"
                . "     data_ctps,"
                . "     serie_ctps,"
                . "     uf_ctps,"
                . "     uf_rg,"
                . "     fgts,"
                . "     insalubridade,"
                . "     transporte,"
                . "     adicional,"
                . "     terceiro,"
                . "     num_par,"
                . "     data_ini,"
                . "     medica,"
                . "     tipo_pagamento,"
                . "     nome_banco,"
                . "     num_filhos,"
                . "     nome_filhos,"
                . "     observacao,"
                . "     impressos,"
                . "     campo4,"
                . "     sis_user,"
                . "     data_cad,"
                . "     foto,"
                . "     dataalter,"
                . "     useralter,"
                . "     vale,"
                . "     documento,"
                . "     rh_vale,"
                . "     rh_vinculo,"
                . "     rh_status,"
                . "     rh_horario,"
                . "     rh_sindicato,"
                . "     rh_cbo,"
                . "     recolhimento_ir,"
                . "     desconto_inss,"
                . "     valor_desconto_inss,"
                . "     trabalha_outra_empresa,"
                . "     salario_outra_empresa,"
                . "     desconto_outra_empresa,"
                . "     vr,"
                . "     valor_vr,"
                . "     data_aviso,"
                . "     data_demi,"
                . "     status_admi,"
                . "     status_demi,"
                . "     matricula,"
                . "     n_processo,"
                . "     contrato_medico,"
                . "     email,"
                . "     data_nasc_pai,"
                . "     data_nasc_mae,"
                . "     data_nasc_conjuge,"
                . "     nome_conjuge,"
                . "     nome_avo_h,"
                . "     data_nasc_avo_m,"
                . "     nome_bisavo_h,"
                . "     data_nasc_bisavo_h,"
                . "     nome_bisavo_m,"
                . "     data_nasc_bisavo_m,"
                . "     municipio_nasc,"
                . "     uf_nasc,"
                . "     data_emissao,"
                . "     verifica_orgao,"
                . "     tipo_sanguineo,"
                . "     ano_contribuicao,"
                . "     dtchegadapais,"
                . "     cod_pais_rais,"
                . "     tipo_contrato,"
                . "     prazoexp,"
                . "     id_estado_civil,"
                . "     id_municipio_nasc,"
                . "     id_municipio_end,"
                . "     id_pais_nasc,"
                . "     id_pais_nacionalidade,"
                . "     curriculo,"
                . "     vale_refeicao,"
                . "     vale_alimentacao,"
                . "     pensao_alimenticia,"
                . "     DATE_FORMAT(data_saida, '%d/%m/%Y') AS data_saida_fmt, "
                . "     DATE_FORMAT(data_entrada, '%d/%m/%Y') AS data_entrada_fmt, "
                . "     DATE_FORMAT(dataalter, '%d/%m/%Y') AS dataalter_fmt, "
                . "     DATE_ADD(data_entrada, INTERVAL '90' DAY) AS data_contratacao, "
                . "     CASE WHEN data_entrada < DATE_SUB(CURDATE(), INTERVAL '90' DAY) THEN 'Contratado' "
                . "          WHEN data_entrada > DATE_SUB(CURDATE(), INTERVAL '90' DAY) AND data_entrada <= CURDATE() THEN 'Em experiência até ' ELSE 'Aguardando' "
                . "     END AS status_contratacao "
                ,SELECT);
        
        $this->db->setQuery("FROM rh_clt ",FROM,true);      

        $this->db->setQuery((!empty($this->getIdClt()) || !empty($this->getIdRegiao()) ||  !empty($this->getIdProjeto() || !empty($this->getSearch()) || !empty($this->getStatus()) || class_exists('RhGestaoCltClass')) ? "WHERE 1=1" : ""),WHERE,true);

        $this->db->setQuery((!empty($this->getIdClt())? "AND id_clt = {$this->getIdClt()}" : ""),WHERE,true);
        $this->db->setQuery((!empty($this->getIdRegiao())? "AND id_regiao = {$this->getIdRegiao()}" : ""),WHERE,true);
        $this->db->setQuery((!empty($this->getIdProjeto())? "AND id_projeto = {$this->getIdProjeto()}" : ""),WHERE,true);
        $this->db->setQuery((!empty($this->getStatus())? "AND status = {$this->getStatus()}" : ""),WHERE,true);
        $this->db->setQuery((!empty($this->getSearch())? "AND ({$this->getSearch()}) AND status !='' AND status !='0' " : ""),WHERE,true);
      
        $this->db->setQuery((empty($this->getIdClt()) && !empty($this->getIdRegiao()) ? "AND status IN(200,20,30,40,50,51,52,70,80,90,100,110,10,60,61,62,63,64,65,81,101)" : ""),WHERE,true); 

        $this->db->setQuery("ORDER BY id_projeto, nome ASC " ,ORDER);
        
        if($this->selectCheck()){
            
            return $this->db->setRs();
            
        }
        else {
            
            return 0;
            
        }

        
        //print_array($this->db->query_string);

        
        
    }
    
    
    public function update(){
        
        $campos = $this->db->makeCamposUpdate($this->clt_save);
        
        if(empty($this->clt_save)){

            return 0;
            
        }
        
        if(!$this->db->setQuery(""
                ."UPDATE rh_clt SET"
                . " $campos "
                . "WHERE id_clt=".$this->getIdClt())){

            return 0;
            
        }
        
//        if($this->db->setRs($this->db->query)){
//            
//            return 1;
//
//        }
//        else {
//
//            return 0;
//            
//        }        
       
        return 1;
        
    }
    
    public function getRow(){
            

        if($this->db->setRow()){

            $this->setIdClt($this->db->getRow('id_clt'));
            $this->setNome($this->db->getRow('nome'));
            $this->setStatus($this->db->getRow('status')); 
            $this->setIdProjeto($this->db->getRow('id_projeto'));
            $this->setIdAntigo($this->db->getRow('id_antigo'));
            $this->setIdRegiao($this->db->getRow('id_regiao'));
            $this->setIdUnidade($this->db->getRow('id_unidade'));
            $this->setAtividade($this->db->getRow('atividade'));
            $this->setSalario($this->db->getRow('salario'));
            $this->setLocalpagamento($this->db->getRow('localpagamento'));
            $this->setLocacao($this->db->getRow('locacao'));
            $this->setUnidade($this->db->getRow('unidade')); 
            $this->setSexo($this->db->getRow('sexo'));
            $this->setEndereco($this->db->getRow('endereco')); 
            $this->setTipoEndereco($this->db->getRow('tipo_endereco'));
            $this->setNumero($this->db->getRow('numero'));
            $this->setComplemento($this->db->getRow('complemento'));
            $this->setBairro($this->db->getRow('bairro')); 
            $this->setCidade($this->db->getRow('cidade'));
            $this->setUF($this->db->getRow('uf'));
            $this->setCEP($this->db->getRow('cep'));
            $this->setTelFixo($this->db->getRow('tel_fixo'));
            $this->setTelCel($this->db->getRow('tel_cel'));
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
            $this->setPai($this->db->getRow('pai'));            
            $this->setNacionalidadePai($this->db->getRow('nacionalidade_pai'));            
            $this->setMae($this->db->getRow('mae'));            
            $this->setNacionalidadeMae($this->db->getRow('mae'));            
            $this->setEstuda($this->db->getRow('estuda'));            
            $this->setDataEscola($this->db->getRow('data_escola'));            
            $this->setEscolaridade($this->db->getRow('escolaridade'));  
            $this->setInstituicao($this->db->getRow('instituicao'));  
            $this->setCurso($this->db->getRow('curso'));  
            $this->setEscolaridade($this->db->getRow('escolaridade'));  
            $this->setInstituicao($this->db->getRow('instituicao'));  
            $this->setCurso($this->db->getRow('curso'));            
            $this->setTipoContratacao($this->db->getRow('tipo_contratacao'));              
            $this->setTvSorrindo($this->db->getRow('tvsorrindo'));              
            $this->setBanco($this->db->getRow('banco'));              
            $this->setAgencia($this->db->getRow('agencia'));              
            $this->setConta($this->db->getRow('conta'));              
            $this->setTipoConta($this->db->getRow('tipo_conta'));  
            $this->setIdCurso($this->db->getRow('id_curso'));              
            $this->setIdPsicologia($this->db->getRow('id_psicologia'));              
            $this->setPsicologia($this->db->getRow('psilocogia'));              
            $this->setObs($this->db->getRow('obs'));              
            $this->setApolice($this->db->getRow('apolice'));              
            $this->setDataEntrada($this->db->getRow('data_entrada'));              
            $this->setDataSaida($this->db->getRow('data_saida'));              
            $this->setCampo1($this->db->getRow('campo1'));  
            $this->setCampo2($this->db->getRow('campo2'));              
            $this->setCampo3($this->db->getRow('campo3'));    
            $this->setCampo4($this->db->getRow('campo4'));              
            $this->setDataExame($this->db->getRow('data_exame'));              
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
            $this->setDataPis($this->db->getRow('data_pis'));              
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
            $this->setSisUser($this->db->getRow('sis_user'));              
            $this->setDataCad($this->db->getRow('data_cad'));              
            $this->setDataAlter($this->db->getRow('dataalter'));              
            $this->setUserAlter($this->db->getRow('useralter'));              
            $this->setVale($this->db->getRow('vale'));              
            $this->setDocumento($this->db->getRow('documento'));              
            $this->setRhVale($this->db->getRow('rh_vale'));              
            $this->setRhVinculo($this->db->getRow('rh_vinculo'));              
            $this->setRhStatus($this->db->getRow('rh_status'));              
            $this->setRhHorario($this->db->getRow('rh_horario'));              
            $this->setRhSindicato($this->db->getRow('rh_sindicato'));              
            $this->setRhCbo($this->db->getRow('rh_cbo'));              
            $this->setRecolhimentoIr($this->db->getRow('recolhimento_ir'));              
            $this->setDescontoInss($this->db->getRow('desconto_inss'));              
            $this->setValorDescontoInss($this->db->getRow('valor_desconto_inss'));              
            $this->setTrabalhaOutraEmpresa($this->db->getRow('trabalha_outra_empresa'));   
            $this->setSalarioOutraEmpresa($this->db->getRow('salario_outra_empresa'));   
            $this->setDescontoOutraEmpresa($this->db->getRow('desconto_outra_empresa'));               
            $this->setVr($this->db->getRow('vr'));               
            $this->setValorVr($this->db->getRow('valor_vr'));               
            $this->setDataAviso($this->db->getRow('data_aviso'));               
            $this->setDataDemi($this->db->getRow('data_demi'));               
            $this->setStatusAdmi($this->db->getRow('status_admi'));               
            $this->setStatusDemi($this->db->getRow('status_demi'));               
            $this->setMatricula($this->db->getRow('matricula'));               
            $this->setNProcesso($this->db->getRow('n_processo'));               
            $this->setContratoMedico($this->db->getRow('contrato_medico'));               
            $this->setEmail($this->db->getRow('email'));               
            $this->setDataNascPai($this->db->getRow('data_nasc_pai'));               
            $this->setDataNascMae($this->db->getRow('data_nasc_mae'));               
            $this->setDataNascConjuge($this->db->getRow('data_nasc_conjuge'));               
            $this->setNomeConjuge($this->db->getRow('nome_conjuge'));               
            $this->setNomeAvoH($this->db->getRow('nome_avo_h'));               
            $this->setDataNascAvoH($this->db->getRow('data_nasc_avo_h'));               
            $this->setNomeAvoM($this->db->getRow('nome_avo_m'));               
            $this->setDataNascAvoM($this->db->getRow('data_nasc_avo_m'));               
            $this->setNomeBisavoH($this->db->getRow('nome_bisavo_h'));               
            $this->setDataNascBisavoH($this->db->getRow('data_nasc_bisavo_h'));               
            $this->setNomeBisavoM($this->db->getRow('nome_bisavo_m'));               
            $this->setDataNascBisavoM($this->db->getRow('data_nasc_bisavo_m'));               
            $this->setMunicipioNasc($this->db->getRow('municipio_nasc'));                           
            $this->setUfNasc($this->db->getRow('uf_nasc'));                           
            $this->setDataEmissao($this->db->getRow('data_emissao'));                           
            $this->setVerificaOrgao($this->db->getRow('verifica_orgao'));               
            $this->setTipoSanguineo($this->db->getRow('tipo_sanguineo'));               
            $this->setAnoContribuicao($this->db->getRow('ano_contribuicao'));                           
            $this->setDtChegadaPais($this->db->getRow('dtchegadapais'));               
            $this->setCodPaisRais($this->db->getRow('cod_pais_rais'));                           
            $this->setTipoContrato($this->db->getRow('tipo_contrato'));                           
            $this->setPrazoExp($this->db->getRow('prazoexp'));                           
            $this->setIdEstadoCicil($this->db->getRow('id_estado_civil'));                           
            $this->setIdMunicipioNasc($this->db->getRow('id_municipio_nasc'));               
            $this->setIdPaisNasc($this->db->getRow('id_pais_nasc'));                           
            $this->setIdPaisNacionalidade($this->db->getRow('id_pais_nacionalidade'));                           
            $this->setCurriculo($this->db->getRow('curriculo'));                           
            $this->setValeRefeicao($this->db->getRow('vale_refeicao'));                           
            $this->setValeAlimentacao($this->db->getRow('vale_alimentacao'));                           
            $this->setPensaoAlimenticia($this->db->getRow('pensao_alimenticia'));  
            $this->setStatusContratacao($this->db->getRow('status_contratacao'));


            return 1;
        }
        else{

            $this->error->setError($this->db->error->getError());

            return 0;
        }
        
    }
    
    
    
    public function strFormateDate($dt,$data_formato = array(ANO,MES,DIA)){
        
        return $dt;
        
        $data_array = explode(' ',$dt);
        
        $data = $data_array[0];
        $hora = $data_array[1];

        $data_replace = str_replace('/','-',$data);         
        $data_explode = explode('-',$data_replace); 

        $y = $data_explode[$data_formato[ANO]];
        $m = $data_explode[$data_formato[MES]];
        $d = $data_explode[$data_formato[DIA]];

        if (checkdate($d,$m,$y)){

            return "{$y}-{$m}-{$d}";

        }
        else {

            $this->error->setError("Data Inválida!");
            return 0;
            
        }
        
        
       
    }

}

class RhCltClass extends CltClass {
    
//    public function __call($method,$args)
//    {
//
//        echo '__call';
//        
//        foreach($this as $ext)
//        {
//            if(property_exists($ext,$varname))
//            return $ext->$varname;
//        }
//
//    }    
//    
//    public function __get($varname)
//    {
//        
//        echo '__get';
//        
//        foreach($this as $ext)
//        {
//            if(method_exists($ext,$method))
//            return call_user_method_array($method,$ext,$args);
//        }
//        throw new Exception("Este Metodo {$method} nao existe!");
//
//    }    
    
    // array contem as classes-extensões
    public function addExt($class)
    {
        
        $this->$class = $this->addClassChildren($class);
        
        $this->$class->superclasse = $this;
        
        return $this->$class;
        
    }
    

    
    /*
     * Adiciona classes filhas a super classe rh_clt
     */
    public function addClassChildren($classe) {
        
        
        try {
            
            $conteudo = file($_SERVER['DOCUMENT_ROOT'].PATH_CLASS."Rh{$classe}Class.php");
            
            $total_linhas = count($conteudo); 
            
            $linha_offset = array_find("class",$conteudo);
               
            
            for ($i = 0; $i <= $linha_offset; $i++) {
                
                unset($conteudo[$i]);                 // exclui as linhas que precedem a declaração da classe e a própria declaração
                
            }
            
            unset($conteudo[$total_linhas-1]);       // exclui última linha

            $conteudo = implode("",$conteudo);       // adiciona conteúdo do arquivo para criação da classe
            
            return $this->extend($classe,$conteudo);

        } catch (Exception $ex) {
            
            //trigger_error($ex,E_USER_ERROR);
            //print_array($ex);
            //$this->db->error->setError($ex);
            
            return 0;

        }        
        
    }


    private function extend($class,$codigo) {
       
        try {

            $nome_classe = get_class($this);
            
            $nome_nova_classe = "{$nome_classe}x";

            eval("class {$nome_nova_classe} extends {$nome_classe} { {$codigo} }");
            
            //eval("class {$class} extends {$nome_classe} { {$codigo} }");

            //eval("class {$class} extends CltClass { {$codigo} }");
            
            $this->$class = new $nome_nova_classe();
            
            return $this->$class;

            //return new $nome_nova_classe();

        } catch (Exception $ex) {
            
            $this->error->setError($ex);
            
            return 0;

        }        
       
    }    
    
    
    
}

