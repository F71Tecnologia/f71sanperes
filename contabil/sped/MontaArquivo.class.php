<?php
include("../../classes/c_classificacaoClass.php");
include("../../classes/ContabilLancamentoItemClass.php");
include("../../classes/ContabilLancamentoClass.php");
//require 'C:\xampp\htdocs\f71lagos-2.0\vendor\autoload.php';
Class MontaArquivo {
    
    public $arquivo;    
    //A ordem dos projetos determina a ordem em que as contas serão puxadas para o plano

    public $projetos = "3309,3317,3320,3318,3303,3315,3302,3338,3316,3304,3319,3331";

    public $total_registros_bloco_0_ecf = 0;
    public $total_registros_bloco_c_ecf = 0;
    public $total_registros_bloco_e_ecf = 0;
    public $total_registros_bloco_j_ecf = 0;
    public $total_registros_bloco_k_ecf = 0;
    public $total_registros_bloco_l_ecf = 0;
    public $total_registros_bloco_m_ecf = 0;
    public $total_registros_bloco_n_ecf = 0;
    public $total_registros_bloco_p_ecf = 0;
    public $total_registros_bloco_q_ecf = 0;
    public $total_registros_bloco_t_ecf = 0;
    public $total_registros_bloco_u_ecf = 0;
    public $total_registros_bloco_w_ecf = 0;
    public $total_registros_bloco_x_ecf = 0;
    public $total_registros_bloco_y_ecf = 0;
    public $total_registros_bloco_9_ecf = 0;
    
    public $total_registros_bloco_0_ecd = 0;
    public $total_registros_bloco_i_ecd = 0;
    public $total_registros_bloco_j_ecd = 0;
    public $total_registros_bloco_k_ecd = 0;
    public $total_registros_bloco_9_ecd = 0;
    
    public $total_credora   = 0;
    public $total_devedora  = 0;
    
    public $datas_saldos_periodicos;    

    public $array_totais_registros_ecd = array();
    public $array_totais_registros_ecf = array();
    public $write                      = array();    
    public $array_encerramento         = array();

public function __construct($arquivo, $tipo, $dados){
    $this->arquivo = $arquivo;
    
    if($dados['projetos_teste'] != ""){
        $this->projetos = $dados['projetos_teste'];
    }    
    
    /*---------------
     Query dados cadastrais
     ----------------
    */
    $query_dados_cadastrais     = "SELECT * FROM rhempresa WHERE cnpj = '07.813.739/0001-61'";
    $res                        = mysql_query($query_dados_cadastrais);
    $data_empresa               = mysql_fetch_assoc($res);
    $dados_plano_de_contas      = $this->query_plano_de_contas();
    
    
    if($tipo == "ECD") {
        
//        $this->geraRelatorio($dados);
        $this->gera_array_encerramento();
        $this->bloco_0_ecd($dados ,$data_empresa);
        $this->bloco_i_ecd($dados, $data_empresa, $dados_plano_de_contas);
        $this->bloco_j_ecd($dados, $data_empresa);
//        (Facultativo para o ano-calendário 2016)
//        if($dados['esc_con_cons'] == 'on'){            
//            $this->bloco_k_ecd($dados ,$data_empresa);
//        }
        $this->bloco_9_ecd();
        $this->write_ecd();
        
    } else if($tipo == "ECF") {        
        $this->bloco_0_ecf($dados, $data_empresa);
        
//       O bloco C não é preenchido pela empresa. O sistema preencherá o bloco C no momento da recuperação
//       das Escriturações Contábeis Digitais (ECD).
       $this->bloco_c_ecf();   
        
//       O bloco E não é preenchido pela empresa. O sistema preencherá o 
//       bloco E no momento da recuperação da ECF no período imediatamente 
//       anterior e efetuará os cálculos fiscais relativos aos dados recuperados da ECD.
       $this->bloco_e_ecf();
        
//       Caso a ECD recuperada possua o mapeamento para o plano de contas referencial 
//       válido na ECF, o bloco J pode ser construído automaticamente e é permitida a sua edição.
       $this->bloco_j_ecf($dados);
       
       
//      Caso haja recuperação da ECD, o bloco K pode ser construído 
//      automaticamente e é permitida a sua edição.
      $this->bloco_k_ecf($dados);
      
        // acho que o bloco L também é importado do ECD, mas ainda não há programa validador, então não tenho como verificar isso no momento
        $this->bloco_l_ecf($dados);
        
        
        $this->bloco_m_ecf($dados);
        $this->bloco_n_ecf($dados);
        $this->bloco_p_ecf($dados);
        $this->bloco_q_ecf($dados);
        $this->bloco_t_ecf($dados);
        $this->bloco_u_ecf($dados);
        
        $this->bloco_x_ecf($dados);
        
        $this->bloco_y_ecf($dados);
        $this->bloco_w_ecf($dados);
        $this->bloco_9_ecf();
        $this->write_ecf();
    }   
}
/* 
_____
INICIO DO SPED - ECD
-----
*/

/* 
_____
INICIO BLOCOS PRINCIPAIS (AGRUPAMENTO DE INFORMAÇÕES)
-----
*/

//Gerar a array de encerramento das contas de DRE
public function gera_array_encerramento(){
    $objClassificador   = new c_classificacaoClass();   
    $prj_arr = explode(",", $this->projetos);
    unset($arrayClassificacao);
    foreach($prj_arr as $id_projeto){
        $arrayProjeto = $objClassificador->balancete($id_projeto, "31/12/2016", "31/12/2016" , true);
        foreach($arrayProjeto as $indice => $value){
            
            $arrayClassificacao[$indice]['id_conta'] = $value['id_conta'];
            $arrayClassificacao[$indice]['classificador'] = $value['classificador'];
            $arrayClassificacao[$indice]['acesso'] = $value['acesso'];
            $arrayClassificacao[$indice]['descricao'] = $value['descricao'];
            $arrayClassificacao[$indice]['analitica_sintetica'] = $value['analitica_sintetica'];
            $arrayClassificacao[$indice]['natureza'] = $value['natureza'];
            $arrayClassificacao[$indice]['credora'] += $value['credora'];
            $arrayClassificacao[$indice]['devedora'] += $value['devedora'];
            $arrayClassificacao[$indice]['saldoAnterior'] += $value['saldoAnterior'];
            $arrayClassificacao[$indice]['saldoAtual'] += $value['saldoAtual'];                        
        }
    }
//    joga os valores para a array de encerramento
    foreach($arrayClassificacao as $key => $value){        
        $prim = substr($key, 0, 3);
        
        if(($prim == "401" || $prim == "402") && $value['analitica_sintetica'] == "A"){
    
            $this->array_encerramento[$value['classificador']] += $value['saldoAtual'] ;
        }
    }
}


public function gera_balancete($data_inicio, $data_fim){
    $objClassificador   = new c_classificacaoClass();   
    $prj_arr = explode(",", $this->projetos);
    
    foreach($prj_arr as $id_projeto){        
        
        $arrayProjeto = $objClassificador->balancete($id_projeto, $data_inicio, $data_fim , true);
        foreach($arrayProjeto as $indice => $value){
            
            $arrayClassificacao[$indice]['id_conta']            = $value['id_conta'];
            $arrayClassificacao[$indice]['classificador']       = $value['classificador'];
            $arrayClassificacao[$indice]['acesso']              = $value['acesso'];
            $arrayClassificacao[$indice]['descricao']           = $value['descricao'];
            $arrayClassificacao[$indice]['analitica_sintetica'] = $value['analitica_sintetica'];
            $arrayClassificacao[$indice]['natureza']            = $value['natureza'];
            $arrayClassificacao[$indice]['credora']             += $value['credora'];
            $arrayClassificacao[$indice]['devedora']            += $value['devedora'];
            $arrayClassificacao[$indice]['saldoAnterior']       += $value['saldoAnterior'];
            $arrayClassificacao[$indice]['saldoAtual']          += $value['saldoAtual'];
            
            }
    }
    
    return $arrayClassificacao;
}
    
//Abertura, Identificação e Referências
//BLOCO 0
public function bloco_0_ecd($dados, $data_empresa){
    $this->abertura_arquivo_id_emp($dados, $data_empresa); 
    $this->abertura_bloco_0();
    $this->outras_inscricoes($dados);
    //$this->esc_contabil_desc();
    if($dados['cod_scp'] == 1){
        $this->id_scp($dados);
    }
    
//    $this->tab_cad_participante();
//    $this->id_relacionamento_part();
    $this->enc_bloco_0();
}


//Lançamentos Contábeis
//BLOCO I
public function bloco_i_ecd($dados, $data_empresa, $dados_plano_de_contas){
    $this->ab_bloco_i();
    $this->id_esc_cont($dados);
//    $this->livros_aux_diario();
//    $this->id_contas_res_esc_aux();
    if($dados['id_moeda_funcional'] == 'on'){
        $this->campos_adicionais($dados);
    }     
    $this->termo_abertura($dados, $data_empresa);
    $this->plano_de_contas($dados_plano_de_contas);
//    $this->plan_contas_ref();
//    $this->subcontas_correlatas();
//    $this->tab_historico_padronizado();
//    $this->centro_custos();
    $this->saldos_periodicos_id_periodo($dados);
//    $this->ass_digital_arq_periodo();
//    $this->transf_saldos_cont_ant();

    //livro diário
    if($dados['tipo_escrituracao'] == 'G' || $dados['tipo_escrituracao'] == 'R' || $dados['tipo_escrituracao'] == 'A') {
        $this->lancamento_contabil($dados);
        $this->lancamento_do_encerramento();
    }   
    
    if($dados['tipo_escrituracao'] == 'B'){
        $this->balancetes_diarios_id_data($dados);
    }

//    $this->detalhes_balancete_diario();
    $this->saldos_contas_result_ant_enc($dados);
//    $this->det_saldos_contas_result_ant_enc();
    if($dados['tipo_escrituracao'] == 'Z'){
        $this->par_impr_liv_raz_aux();
        $this->def_campos_livro();
        $this->det_liv_raz_aux($dados);
    }
    
//    $this->tot_livro_raz_aux();
    $this->encerramento_bloco_i() ;
    
}
//Demonstrações Contábeis
//BLOCO J
public function bloco_j_ecd($dados, $data_empresa){
    $this->abertura_bloco_j();
    if($dados['tipo_escrituracao'] != "Z"){
        $this->demonstracoes_contabeis($dados);
        $this->balanco_patrimonial($dados);
        $this->demonstracao_resultados($dados);
    }    
    
//    $this->tab_hist_fatos_cont();
//    $this->dem_lucros_preju();
//    $this->fato_contabil_alt();
//    $this->outras_infos();
//    $this->termo_de_verif();
    $this->termo_de_encerramento($dados, $data_empresa);
    $this->id_signatarios($dados['sig_esc'], $data_empresa);
    if($dados['J935'] ==1){
        $this->id_auditores($dados);
    }
    $this->encerramento_bloco_j();
}

//Conglomerados Econômicos (Facultativo para o ano-calendário 2016)
//BLOCO K
public function bloco_k_ecd($dados, $data_empresa){
    $this->abertura_bloco_k();
    $this->periodo_esc_contabil($dados);
//    $this->relacao_emp_consolidadas();
//    $this->relacao_eventos_societarios();
//    $this->empresas_part_ev_societario();
//    $this->plano_contas_consolidado();
//    $this->mapeamento_para_plano_de_contas();
//    $this->saldo_contas_cons();
//    $this->emp_detentoras_parc();
//    $this->emp_contrapartes();
    $this->encerramento_bloco_k();
}

//Controle e Encerramento do Arquivo Digital
//BLOCO 9
public function bloco_9_ecd(){
    $this->abertura_bloco_9();
    $this->registros_arq();
    $this->encerramento_bloco_9();
    $this->encerramento_arquivo_digital();
}

public function write_ecd(){   
        
    //Adicionando o total de linhas do arquivo nos registros
    $total_linhas_ecd = count($this->write);
    foreach($this->write as $key => $line){
        if(strpos($line, "|SUBSTUTUIR_TOTAL_LINHAS|")){            
            $this->write[$key] = str_replace("SUBSTUTUIR_TOTAL_LINHAS", $total_linhas_ecd, $line);            
        }
    }
    
    //ESCREVER NO ARQUIVO
    foreach($this->write as $line){ 
        fwrite($this->arquivo, $line);
    }
}

/* 
_____
FIM BLOCOS PRINCIPAIS
-----
*/

/* 
_____
INÍCIO DOS REGISTROS
-----
*/

//ABERTURA DO ARQUIVO DIGITAL E IDENTIFICAÇÃO DO EMPRESÁRIO OU DA SOCIEDADE EMPRESÁRIA
public function abertura_arquivo_id_emp($dados, $data_empresa){
    $dados['dta_ini']               = RemoveCaracteres($dados['dta_ini']);
    $dados['dta_fin']               = RemoveCaracteres($dados['dta_fin']);
    $dados['id_scp_ecd']            = RemoveCaracteres($dados['id_scp_ecd']);
    $data_empresa['cnpj']           = RemoveCaracteres($data_empresa['cnpj']);
    $data_empresa['cod_municipio']  = RemoveCaracteres($data_empresa['cod_municipio']);
    $dados['existe_nire']           = ($dados['existe_nire'] == 'on') ? '1' : '0' ;
    $dados['id_moeda_funcional']    = ($dados['id_moeda_funcional'] == 'on') ? 'S' : 'N' ;
    $dados['esc_con_cons']          = ($dados['esc_con_cons'] == 'on') ? 'S' : 'N' ; 
    
    if(is_null($dados['indicador_situacao_especial'])){
        $dados['indicador_situacao_especial'] = "";
    }else{
        $dados['indicador_situacao_especial'] = 1;
    }
    
    $dados['id_scp_ecd2'] = RemoveCaracteres($dados['id_scp_ecd2']);
    $this->array_totais_registros_ecd['0000'] = 1;
    $this->total_registros_bloco_0_ecd +=1;
    $this->write[] = "|0000|LECD|{$dados['dta_ini']}|{$dados['dta_fin']}|{$data_empresa['razao']}|{$data_empresa['cnpj']}|{$data_empresa['uf']}||{$data_empresa['cod_municipio']}||{$dados['indicador_situacao_especial']}|{$dados['indicador_situacao_inicio_periodo']}|{$dados['existe_nire']}|0||{$dados['J935']}|{$dados['cod_scp']}|{$dados['id_scp_ecd2']}|{$dados['id_moeda_funcional']}|N|\n";
}

//ABERTURA DO BLOCO 0
public function abertura_bloco_0(){
    $this->total_registros_bloco_0_ecd +=1;   
    $this->array_totais_registros_ecd['0001'] = 1;
    $this->write[] =  "|0001|0|\n";
}

//OUTRAS INSCRIÇÕES CADASTRAIS DA PESSOA JURÍDICA
public function outras_inscricoes($dados){
    $this->total_registros_bloco_0_ecd +=1;
     $this->array_totais_registros_ecd['0007'] = 1;
    $this->write[] =  "|0007|{$dados['cod_ent_ref']}|{$dados['cod_inscr']}|\n";
}

//ESCRITURAÇÃO CONTÁBIL DESCENTRALIZADA
public function esc_contabil_desc(){
    $this->total_registros_bloco_0_ecd +=1;
     $this->array_totais_registros_ecd['0020'] = 1;
    $this->write[] = "|0020|1|11111111000191|DF|123456|3434401||11111111|\n";    
}

//IDENTIFICAÇÃO DAS SCP
// Obrigatório se o campo cod_scp do registro 0000 for igual a 1 (ECD participante de SCP como sócio ostensivo).
public function id_scp($dados){     
    
    $dados['id_scp_ecd'] = RemoveCaracteres($dados['id_scp_ecd']);
    $this->total_registros_bloco_0_ecd +=1;
     $this->array_totais_registros_ecd['0035'] = 1;
    $this->write[] = "|0035|{$dados['id_scp_ecd']}|{$dados['nome_scp']}|\n";
}

//TABELA DE CADASTRO DO PARTICIPANTE
public function tab_cad_participante(){
    $this->total_registros_bloco_0_ecd +=1;
     $this->array_totais_registros_ecd['0150'] = 1;
    $this->write[] = "|0150|03|COLIGADA TESTE S.A.|01058|99999999000191|||35|999999||3550508|||\n";    
}

//IDENTIFICAÇÃO DO RELACIONAMENTO COM O PARTICIPANTE
public function id_relacionamento_part(){
    $this->total_registros_bloco_0_ecd +=1;
     $this->array_totais_registros_ecd['0180'] = 1;
    $this->write[] = "|0180|03|23032011||\n";     
}

//ENCERRAMENTO DO BLOCO 0
public function enc_bloco_0(){
    $this->total_registros_bloco_0_ecd +=1;
     $this->array_totais_registros_ecd['0990'] = 1;
    $this->write[] = "|0990|{$this->total_registros_bloco_0_ecd}|\n";
    
}

//ABERTURA DO BLOCO I
public function ab_bloco_i(){
    
    $this->write[] = "|I001|0|\n";   
    $this->array_totais_registros_ecd['I001'] = 1;
    $this->total_registros_bloco_i_ecd +=1;
}

//IDENTIFICAÇÃO DA ESCRITURAÇÃO CONTÁBIL
public function id_esc_cont($dados){
    $this->array_totais_registros_ecd['I010'] = 1;
    $this->write[] = "|I010|{$dados['tipo_escrituracao']}|5.00|\n";   
    $this->total_registros_bloco_i_ecd +=1;
}

//LIVROS AUXILIARES AO DIÁRIO
public function livros_aux_diario(){
    $this->array_totais_registros_ecd['I012'] = 1;
    $this->write[] =  "|I012|1|DIARIO COM RESCRITURAÇÃO RESUMIDA|0||";
    $this->total_registros_bloco_i_ecd +=1;
    
}

//IDENTIFICAÇÃO DAS CONTAS DA ESCRITURAÇÃO RESUMIDA A QUE SE REFERE A ESCRITURAÇÃO AUXILIAR
public function id_contas_res_esc_aux(){
    $this->array_totais_registros_ecd['I015'] = 1;
    $this->write[] =  "|I015|2328.1.0001|\n";
    $this->total_registros_bloco_i_ecd +=1;
    
}

//CAMPOS ADICIONAIS
public function campos_adicionais($dados){
     
    $this->write[] = "|I020|{$dados['reg_cod']}|01|{$dados['nome_campo']}|{$dados['desc_campo']}|{$dados['ind_tipo_dado']}|\n";
    $this->array_totais_registros_ecd['I020'] = 1;
    $this->total_registros_bloco_i_ecd +=1;
}

//TERMO DE ABERTURA - Nível Hierárquico 3 
public function termo_abertura($dados, $data_empresa){ 
    
    $data_empresa['cnpj']  = RemoveCaracteres($data_empresa['cnpj']);
    $dados['dt_arq_atos']  = RemoveCaracteres($dados['dt_arq_atos']);
    $dados['dt_ato_conv']  = RemoveCaracteres($dados['dt_ato_conv']);
    $dados['dta_fin']      = RemoveCaracteres($dados['dta_fin']);
    
    $this->write[] = "|I030|TERMO DE ABERTURA|{$dados['num_ord']}|{$dados['nat_livro']}|SUBSTUTUIR_TOTAL_LINHAS|{$data_empresa['razao']}|{$dados['nire']}|{$data_empresa['cnpj']}|{$dados['dt_arq_atos']}|{$dados['dt_ato_conv']}|{$data_empresa['cidade']}|{$dados['dta_fin']}|\n";
    $this->total_registros_bloco_i_ecd +=1;
    $this->array_totais_registros_ecd['I030'] = 1;
}

////PLANO DE CONTAS
public function plano_de_contas($dados_plano_de_contas){
    
    foreach($dados_plano_de_contas as $dado){
        
        $contador_de_pontos = substr_count($dado['classificador'], ".") ;
        $nivel = $contador_de_pontos+1;
        
        if($nivel > 1){            
            $conta_superior = substr($dado['classificador'], 0 , -3);
        }else{
            $conta_superior = "";
        }        

        $dado['descricao'] = trim($dado['descricao']); 
        
        $prim_num = substr($dado['classificador'], 0, 1);
        if($prim_num >= 3){
            $dado['natureza'] = "4";
        }
        
        $this->write[] =  "|I050|01012016|0{$dado['natureza']}|{$dado['classificacao']}|{$nivel}|{$dado['classificador']}|{$conta_superior}|{$dado['descricao']}|\n";  
        
        //contadores
        $this->total_registros_bloco_i_ecd +=1;
        $this->array_totais_registros_ecd['I050'] = $this->array_totais_registros_ecd['I050']+  1;
        
        //inserir o registro com o código de aglutinação somente contas analíticas
        if($dado['classificacao'] == "A"){
            $classificador =$dado['classificador'];
            $class_array = explode(".", $classificador);
            $numero_de_pontos = count($class_array) - 1;

            if($numero_de_pontos > 0){
                //executa o método de escrita
                unset($class_array[$numero_de_pontos]);
                $class_array = implode(".", $class_array);
                $this->ind_cods_aglutinacao($class_array);
            }
        }        
    }
}
//PLANO DE CONTAS REFERENCIAL
public function plan_contas_ref(){
    $this->write[] = "|I051|8||11100009|\n";    
    $this->total_registros_bloco_i_ecd +=1;
    $this->array_totais_registros_ecd['I051'] = 1;
}

//INDICAÇÃO DOS CÓDIGOS DE AGLUTINAÇÃO
public function ind_cods_aglutinacao($conta){
    $this->write[] =  "|I052||{$conta}|\n";
    $this->total_registros_bloco_i_ecd +=1;
    $this->array_totais_registros_ecd['I052'] += 1;
    
}

//SUBCONTAS CORRELATAS
public function subcontas_correlatas(){
     $this->write[] =  "|I053|FT1234|1.05.01.10|02|\n";  
     $this->total_registros_bloco_i_ecd +=1;
     $this->array_totais_registros_ecd['I053'] = 1;
}

//TABELA DE HISTÓRICO PADRONIZADO
public function tab_historico_padronizado(){
     $this->write[] = "|I075|12345|PAGAMENTO A FORNECEDORES|\n";  
     $this->total_registros_bloco_i_ecd +=1;
     $this->array_totais_registros_ecd['I075'] = 1;
        
}

//CENTRO DE CUSTOS
public function centro_custos(){
    $this->write[] = "|I100|01012005|CC2328-001|DIVISÃO A|\n";     
    $this->total_registros_bloco_i_ecd +=1;
    $this->array_totais_registros_ecd['I100'] = 1;
}

//SALDOS PERIÓDICOS - IDENTIFICAÇÃO DO PERÍODO
public function saldos_periodicos_id_periodo($dados){        
    
    $data_inicio                              = implode("-", array_reverse(explode("/", $dados['dta_ini'])));
    $data_final_sd_per                        = implode("-", array_reverse(explode("/", $dados['dta_fin'])));
    $data_final_1_mes                         = date("t/m/Y", strtotime($data_inicio));
    $data_inicio_com_slashes                  = $data_inicio;
    $data_final_1_mes_com_slashes             = $data_final_1_mes;
    $data_final_1_mes                         = RemoveCaracteres($data_final_1_mes);
    $dados['dta_ini']                         = RemoveCaracteres($dados['dta_ini']);
    $this->write[]                            = "|I150|{$dados['dta_ini']}|{$data_final_1_mes}|\n";
    $this->total_registros_bloco_i_ecd        += 1;
    $this->array_totais_registros_ecd['I150'] = $this->array_totais_registros_ecd['I150'] + 1;

    //escreve detalhes do primeiro saldo
    $this->det_saldos_periodicos($data_inicio_com_slashes, $data_final_1_mes_com_slashes);
    
    //escreve os outros meses
    $primeiro_mes = date("m", strtotime($data_inicio));
    $ultimo_mes   = date("m", strtotime($data_final_sd_per)); 
    
    
    for($i = (int)$primeiro_mes+1 ; $i< ($ultimo_mes+1) ; $i++){
        $data_inicio                              = date("d/m/Y", strtotime("2016-" . $i . "-01"));
        $data_fim                                 = date("t/m/Y", strtotime("2016-" . $i . "-01"));
        $data_inicio_com_slashes                  = $data_inicio;
        $data_fim_com_slashes                     = $data_fim;
        $data_inicio                              = RemoveCaracteres($data_inicio);
        $data_fim                                 = RemoveCaracteres($data_fim);
        $this->write[]                            = "|I150|{$data_inicio}|{$data_fim}|\n";
        $this->total_registros_bloco_i_ecd        += 1;
        $this->array_totais_registros_ecd['I150'] = $this->array_totais_registros_ecd['I150'] + 1;

            //detalhes dos outros saldos
        $this->det_saldos_periodicos($data_inicio_com_slashes, $data_fim_com_slashes);
        $mes_dt_fim = substr($data_fim, 3, 2) ;
    }
}

//ASSINATURA DIGITAL DOS ARQUIVOS QUE CONTÉM AS FICHAS DE LANÇAMENTO UTILIZADAS NO PERÍODO
public function ass_digital_arq_periodo(){
    $this->write[] =  "|I151|123456789012345|\n";    
    $this->total_registros_bloco_i_ecd +=1;
    $this->array_totais_registros_ecd['I151'] = 1;
}

//DETALHES DOS SALDOS PERIÓDICOS
public function det_saldos_periodicos($data_inicio, $data_fim){
    
    $arrayClassificacao = $this->gera_balancete($data_inicio, $data_fim);
        
    $mes_dt_fim = substr($data_fim, 3, 2) ;
    foreach($arrayClassificacao as $key => $value){   
        
        //zerando os valores null
        if(is_null($value['devedora'])){
            $value['devedora'] = 0.00;
        }
        if(is_null($value['credora'])){
            $value['credora'] = 0.00;
        }
        if(is_null($value['saldoAnterior'])){
            $value['saldoAnterior'] = 0.00;
        }
        if(is_null($value['saldoAtual'])){
            $value['saldoAtual'] = 0.00;
        }
        
        //somente Analíticas        
        if($value['analitica_sintetica'] == "A"){

            //verifica a natureza e atribui credor/devedor
            if($value['natureza'] == 1){
                
                $value['ind_dc_ini'] = "D";
                $value['ind_dc_fim'] = "D";
                
            }else{
                $value['ind_dc_ini'] = "C";
                $value['ind_dc_fim'] = "C";                
            }
                        
//            verifica se valor é negativo e inverte C/D
            if($value['saldoAnterior'] < 0){
                $value['saldoAnterior'] = $value['saldoAnterior'] * (-1);
                $value['ind_dc_ini'] = ($value['ind_dc_ini'] == "D") ? "C" : "D"; 
            }
            
            if($value['saldoAtual'] < 0){
                $value['saldoAtual'] = $value['saldoAtual'] * (-1);
                $value['ind_dc_fim'] = ($value['ind_dc_fim'] == "D") ? "C" : "D";
            }
            
            $tres_prim = substr($value['classificador'], 0, 4);
            if($mes_dt_fim == "12" && ($tres_prim == "4.01" || $tres_prim == "4.02")){
                $value['saldoAtual'] = 0;
            }
            
            if($tres_prim == "4.01" && $mes_dt_fim == "12"){
                $value['devedora'] = $value['saldoAnterior'] + $value['credora'] ;
            }
            
             if($tres_prim == "4.02" && $mes_dt_fim == "12"){
                $value['credora'] = $value['saldoAnterior'] + $value['devedora'] ;
            }
            
            $value['saldoAnterior'] = number_format($value['saldoAnterior'], 2, ",", "");
            $value['saldoAtual']    = number_format($value['saldoAtual'], 2, ",", "");
            $value['credora']       = number_format($value['credora'], 2, ",", "");
            $value['devedora']      = number_format($value['devedora'], 2, ",", "");
            
            if($value['saldoAnterior'] != '0,00' || $value['saldoAtual'] != '0,00' || $value['credora'] != '0,00' || $value['devedora'] != '0,00'){
               
                $this->write[] =  "|I155|{$value['classificador']}||{$value['saldoAnterior']}|{$value['ind_dc_ini']}|{$value['devedora']}|{$value['credora']}|{$value['saldoAtual']}|{$value['ind_dc_fim']}|\n";    
                $this->total_registros_bloco_i_ecd +=1;
                $this->array_totais_registros_ecd['I155'] = $this->array_totais_registros_ecd['I155'] + 1;
            }
        }//endif
    }//endforeach
    
    //incluir conta 8.01.01.01
    if($mes_dt_fim == "12"){
        foreach($this->array_encerramento as $conta => $valor){
            $prim = substr($conta, 0, 4);
            if($prim == "4.01"){
                $totalAtual += $valor;
            }
        }
    $totalAtual  = number_format($totalAtual, 2, ",", "");
    $this->write[]                            = "|I155|8.01.01.01||0,00|C|{$totalAtual}|{$totalAtual}|0,00|C|\n";
    $this->total_registros_bloco_i_ecd        += 1;
    $this->array_totais_registros_ecd['I155'] = $this->array_totais_registros_ecd['I155'] + 1;
    
    }
}// endfunction

//TRANSFERÊNCIA DE SALDOS DO PLANO DE CONTAS ANTERIOR
public function transf_saldos_cont_ant(){
    $this->write[] =  "|I157|2328.1.0001||1000,00|D|\n";
    $this->total_registros_bloco_i_ecd +=1;
    $this->array_totais_registros_ecd['I157'] = 1;
}

//LANÇAMENTO CONTÁBIL

public function lancamento_contabil($dados){
    
    $objLancamentoItens = new ContabilLancamentoItemClass();
    $data_inicio        = implode("-", array_reverse(explode("/", $dados['dta_ini'])));
    $data_final         = implode("-",array_reverse(explode("/", $dados['dta_fin'])));
    
    $arrayLancamentos = $objLancamentoItens->getLivroDiario($this->projetos, $data_inicio, $data_final);
    $arrayRepeatLançamentos = array();
    foreach($arrayLancamentos as $key => $value){
        foreach($value as $key2 => $value2){
           foreach($value2 as $key3 =>$value3){               
               foreach($value3 as $lanc_key => $lanc ){ 
                   if($lanc['tipo'] == 1){
                       $array_valores_totais_dos_lancamentos[$lanc['lancamento']] +=  $lanc['valor']; 
                   }                                     
               }
               
               foreach($value3 as $key4 => $value4){
                   if(!in_array($value4['lancamento'], $arrayRepeatLançamentos)){

                        $array_valores_totais_dos_lancamentos[$value4['lancamento']] = number_format($array_valores_totais_dos_lancamentos[$value4['lancamento']], 2, ",", "");
                        $data_lancamento                                             = RemoveCaracteres($key);
                        $tipolanc                                                    = "N";
                        $this->write[]                                               = "|I200|{$value4['lancamento']}|{$data_lancamento}|{$array_valores_totais_dos_lancamentos[$value4['lancamento']]}|{$tipolanc}|\n";
                        $this->total_registros_bloco_i_ecd                           += 1;
                        $this->array_totais_registros_ecd['I200']                    += 1;
                        $arrayRepeatLançamentos[]                                    = $value4['lancamento'];
                            $this->partidas_lanc_contabil($value4, $data_lancamento);
                   }else{
                        $this->partidas_lanc_contabil($value4, $data_lancamento);
                   }
               }
           }            
        }         
    }
}

//PARTIDAS DO LANÇAMENTO CONTÁBIL
public function partidas_lanc_contabil($value4, $data_lancamento){
    
    $value4['historico_l']                  = str_replace('"', "", $value4['historico_l']);
    $deb_cred                               = ($value4['tipo'] == 2) ? 'D' : 'C';
    $valor_lancamento                       = number_format($value4['valor'], 2, ",", "");
    $value4['historico_l']                  = trim($value4['historico_l']);  
    $value4['historico_l']                  = str_replace("	", " ", $value4['historico_l']);
    
    if($value4['historico_l'] == ""){
        $value4['historico_l'] = "HISTÓRICO NÃO DEFINIDO";
    }
        
    $this->write[]   = "|I250|{$value4['classificador']}||{$valor_lancamento}|{$deb_cred}|||{$value4['historico_l']}||\n";
    
    $this->total_registros_bloco_i_ecd        += 1;
    $this->array_totais_registros_ecd['I250'] += 1;    
}

public function lancamento_do_encerramento(){
    
//    Pegar o saldo atual das contas 4.01 e 4.02 do balancete dia 31/12/2016
    //calcula o valor total do saldoAtual das 4.01
    foreach($this->array_encerramento as $conta => $valor){
        $prim = substr($conta, 0, 4);
        if($prim == "4.01"){
            $totalAtual += $valor;
        }
    }
    $totalAtual  = number_format($totalAtual, 2, ",", "");
    
//   fazer o encerramento das 4.01
    $this->write[]  = "|I200|99998|31122016|{$totalAtual}|N|\n";
    $this->total_registros_bloco_i_ecd        += 1;
    $this->array_totais_registros_ecd['I200']   += 1;
    
//    insere as partidas da 4.01 
    foreach($this->array_encerramento as $conta => $valor){
        $prim = substr($conta, 0, 4);
        if($prim == "4.01"){
            $valor                                    = number_format($valor, 2, ",", "");
            $this->write[]                            = "|I250|{$conta}||{$valor}|D|||ENCERRAMENTO DO EXERCÍCIO||\n";
            $this->total_registros_bloco_i_ecd        += 1;
            $this->array_totais_registros_ecd['I250'] += 1; 
        }
    }
//    lança a contrapartida das 4.01   
    $this->write[]                            = "|I250|8.01.01.01||{$totalAtual}|C|||ENCERRAMENTO DO EXERCÍCIO||\n";
    $this->total_registros_bloco_i_ecd        += 1;
    $this->array_totais_registros_ecd['I250'] += 1; 
    
    //   fazer o encerramento das 4.02
    $this->write[]  = "|I200|99999|31122016|{$totalAtual}|N|\n";
    $this->total_registros_bloco_i_ecd        += 1;
    $this->array_totais_registros_ecd['I200']   += 1;
    
//    insere as partidas da 4.02
    foreach($this->array_encerramento as $conta => $valor){
        $prim = substr($conta, 0, 4);
        if($prim == "4.02"){
            $valor  = number_format($valor, 2, ",", "");
            $this->write[]                            = "|I250|{$conta}||{$valor}|C|||ENCERRAMENTO DO EXERCÍCIO||\n";
            $this->total_registros_bloco_i_ecd        += 1;
            $this->array_totais_registros_ecd['I250'] += 1; 
        }
    }
//    lança a contrapartida das 4.02
    $this->write[]                            = "|I250|8.01.01.01||{$totalAtual}|D|||ENCERRAMENTO DO EXERCÍCIO||\n";
    $this->total_registros_bloco_i_ecd        += 1;
    $this->array_totais_registros_ecd['I250'] += 1;     
}

//BALANCETES DIÁRIOS ? IDENTIFICAÇÃO DA DATA
public function balancetes_diarios_id_data($dados){
    
    $data_inicio       = implode("-", array_reverse(explode("/", $dados['dta_ini'])));
    $data_final_sd_per = implode("-", array_reverse(explode("/", $dados['dta_fin'])));
    $primeiro_mes      = date("m", strtotime($data_inicio));
    $ultimo_mes        = date("m", strtotime($data_final_sd_per));
    
    //loop mes
    for($i=$primeiro_mes ; $i <= $ultimo_mes ; $i++){
        $data_mes = "2016-".$i."-01";
        $ultimo_dia_mes = date("t", strtotime($data_mes));
        
        //loop dia
        for($j = 1; $j <= $ultimo_dia_mes; $j++){
            $dia                       = ($j < 10) ? "0" . $j : $j;
            $data_balancete            = $dia . "/" . $i . "/2016";
            $data_balancete_sem_barras = RemoveCaracteres($data_balancete);
            
            $arrayClassificacaoDiario = $this->gera_balancete($data_balancete, $data_balancete);
            
            $this->total_credora             = 0;
            $this->total_devedora            = 0;
            
            //soma todos credores e devedores
            foreach($arrayClassificacaoDiario as $key2 => $value2){
                $this->total_credora  += $value2['credora'];
                $this->total_devedora += $value2['devedora'];
            }

            //insere somente as datas que têm alguma movimentação em credores ou devedores
            if($this->total_credora != 0.00 || $this->total_devedora != 0.00){
                $this->write[] =  "|I300|{$data_balancete_sem_barras}|\n";
                $this->total_registros_bloco_i_ecd +=1;
                $this->array_totais_registros_ecd['I300'] += 1;   
            }
              
            //loop conta
            foreach($arrayClassificacaoDiario as $key => $value){
                $this->detalhes_balancete_diario($value);
            }
        }
    }  
}

//DETALHES DO BALANCETE DIÁRIO
public function detalhes_balancete_diario($value){
    //pega somente as analíticas diferentes de 0
    if($value['analitica_sintetica'] == "A" && ($value['credora'] != 0.00 || $value['devedora'] != 0.00)){
        $value['credora']                         = number_format($value['credora'], 2, ",", "");
        $value['devedora']                        = number_format($value['devedora'], 2, ",", "");
        $this->write[]                            = "|I310|{$value['classificador']}||{$value['devedora']}|{$value['credora']}|\n";
        $this->total_registros_bloco_i_ecd        += 1;
        $this->array_totais_registros_ecd['I310'] += 1;
    }
}

//SALDOS DAS CONTAS DE RESULTADO ANTES DO ENCERRAMENTO ? IDENTIFICAÇÃO DA DATA
public function saldos_contas_result_ant_enc($dados){
    
    $data_inicio       = implode("-", array_reverse(explode("/", $dados['dta_ini'])));
    $data_final_sd_per = implode("-", array_reverse(explode("/", $dados['dta_fin'])));
    
    $this->write[] = "|I350|31122016|\n";   
    $this->total_registros_bloco_i_ecd +=1;
    $this->array_totais_registros_ecd['I350'] = 1;
    $this->det_saldos_contas_result_ant_enc();
}

//DETALHES DOS SALDOS DAS CONTAS DE RESULTADO ANTES DO ENCERRAMENTO
public function det_saldos_contas_result_ant_enc(){

    foreach($this->array_encerramento as $conta => $valor ){
        $prim = substr($conta, 0, 4);
        $tipo_conta = "C";
        
        if($prim == "8.01"){
            $valor = "0,00";
        }
        
        $valor = number_format($valor, 2, ",", "");
        $this->write[] =  "|I355|{$conta}||{$valor}|{$tipo_conta}|\n";   
        $this->total_registros_bloco_i_ecd +=1;
        $this->array_totais_registros_ecd['I355'] += 1;
    }     
}

//PARÂMETROS DE IMPRESSÃO/VISUALIZAÇÃO DO LIVRO RAZÃO AUXILIAR COM LEIAUTE PARAMETRIZÁVEL
public function par_impr_liv_raz_aux(){
    $this->write[] = "|I500|10|\n";  
    $this->total_registros_bloco_i_ecd +=1;
    $this->array_totais_registros_ecd['I500'] = 1;
    
}

//DEFINIÇÃO DOS CAMPOS DO LIVRO RAZÃO AUXILIAR COM LEIAUTE PARAMETRIZÁVEL
public function def_campos_livro(){
    
    $this->write[] =  "|I510|RAZ_CONTA|RAZÃO_DA_CONTA|C|100||100|\n";  
    $this->total_registros_bloco_i_ecd +=1;
    $this->array_totais_registros_ecd['I510'] += 1;
    
    $this->write[] =  "|I510|DATA|DATA|C|10||10|\n";  
    $this->total_registros_bloco_i_ecd +=1;
    $this->array_totais_registros_ecd['I510'] += 1;
    
    $this->write[] =  "|I510|HISTORICO|HISTORICO|C|150||150|\n";
    $this->total_registros_bloco_i_ecd +=1;
    $this->array_totais_registros_ecd['I510'] += 1;
    
    $this->write[] =  "|I510|DEBITO|DEBITO|N|12|2|12|\n";
    $this->total_registros_bloco_i_ecd +=1;
    $this->array_totais_registros_ecd['I510'] += 1;
    
    $this->write[] =  "|I510|CREDITO|CREDITO|N|12|2|12|\n";
    $this->total_registros_bloco_i_ecd +=1;
    $this->array_totais_registros_ecd['I510'] += 1;
}

//DETALHES DO LIVRO RAZÃO AUXILIAR COM LEIAUTE PARAMETRIZÁVEL
public function det_liv_raz_aux($dados){
    
        $objLancamentoItens = new ContabilLancamentoItemClass();
        $data_inicio        = implode("-", array_reverse(explode("/", $dados['dta_ini'])));
        $data_final         = implode("-", array_reverse(explode("/", $dados['dta_fin'])));
        $arrayLancamentos   = $objLancamentoItens->getLivroDiario($this->projetos, $data_inicio, $data_final);

    foreach($arrayLancamentos as $key => $value){
        foreach($value as $key2 => $value2){
           foreach($value2 as $key3 =>$value3){   
               foreach($value3 as $key4 =>$value4){  
                    $debito                            = $value4['tipo'] == 2 ? $value4['valor'] : "";
                    $credito                           = $value4['tipo'] == 1 ? $value4['valor'] : "";
                    $debito                            = number_format($debito, 2, ",", "");
                    $credito                           = number_format($credito, 2, ",", "");
                    $value4['historico_l']             = str_replace('"', "", $value4['historico_l']);
                    $historico                         = trim($value4['historico_l']);
                    $data                              = $value4['data_lancamento'];
                    $razao_conta                       = $value4['classificador'] . " - " . $value4['descricao'];
                    $this->write[]                     = "|I550|{$razao_conta}|{$data}|{$historico}|{$debito}|{$credito}|\n";
                    $this->total_registros_bloco_i_ecd += 1;
                    $this->array_totais_registros_ecd['I550'] += 1;
                }
           }
        }
    } 
}

//TOTAIS NO LIVRO RAZÃO AUXILIAR COM LEIAUTE PARAMETRIZÁVEL
public function tot_livro_raz_aux(){
    
    $this->write[] = "|I550|2002|PRODUTO2|20,20|100|2020|\n";
    $this->total_registros_bloco_i_ecd +=1;
}

//ENCERRAMENTO DO BLOCO I
public function encerramento_bloco_i(){
    $this->total_registros_bloco_i_ecd +=1;
    $this->array_totais_registros_ecd['I990'] = 1;
    $this->write[] = "|I990|{$this->total_registros_bloco_i_ecd}|\n";
    
    
}

//ABERTURA DO BLOCO J
public function abertura_bloco_j(){
    $this->write[] = "|J001|0|\n"; 
    $this->total_registros_bloco_j_ecd +=1;
    $this->array_totais_registros_ecd['J001'] = 1;
}

//DEMONSTRAÇÕES CONTÁBEIS
public function demonstracoes_contabeis($dados){
    $data_inicio = RemoveCaracteres($dados['dta_ini']);
    $data_fim = RemoveCaracteres($dados['dta_fin']);
    $this->write[] = "|J005|{$data_inicio}|{$data_fim}|1||\n";   
    $this->total_registros_bloco_j_ecd +=1;
    $this->array_totais_registros_ecd['J005'] += 1;
}

//BALANÇO PATRIMONIAL
public function balanco_patrimonial($dados){
    
    //pega somente as sinteticas ativo e passivo, sem dre
    $data_inicio  = $dados['dta_ini'];
    $data_fim     = $dados['dta_fin'];
    
    $arrayClassificacao  = $this->gera_balancete($data_inicio, $data_fim);
    ksort($arrayClassificacao);
    
    foreach($arrayClassificacao as $value){
        $prim_num = substr($value['classificador'], 0, 1);
        //lançar somente as sintéticas ATIVO OU PASSIVO
        if($value['analitica_sintetica'] == "S" && ($prim_num == "1" || $prim_num == "2")){
            
            if($prim_num =="1"){
                $grupo_cta = "1";
            }else{
                $grupo_cta = "2";
            }
            
            //verifica a natureza e atribui credor/devedor
            if($value['natureza'] == 1){
                
                $value['ind_dc_ini'] = "D";
                $value['ind_dc_fim'] = "D";
                
            }else{
                $value['ind_dc_ini'] = "C";
                $value['ind_dc_fim'] = "C";                
            }
            
            //verifica se valor é negativo e inverte C/D
            if($value['saldoAnterior'] < 0){
                $value['saldoAnterior'] = $value['saldoAnterior'] * (-1);
                $value['ind_dc_ini'] = ($value['ind_dc_ini'] == "D") ? "C" : "D"; 
            }
            
            if($value['saldoAtual'] < 0){
                $value['saldoAtual'] = $value['saldoAtual'] * (-1);
                $value['ind_dc_fim'] = ($value['ind_dc_fim'] == "D") ? "C" : "D";
            }
            
            $value['saldoAtual']    = number_format($value['saldoAtual'], 2, ",", "");
            $value['saldoAnterior']    = number_format($value['saldoAnterior'], 2, ",", "");
            
            if(is_null($value['saldoAtual'])){
                $value['saldoAtual'] = "0.00";
            }
            
            if(is_null($value['saldoAnterior'])){
                $value['saldoAnterior'] = "0.00";
            }
            
            $contador = substr_count($value['classificador'], ".");
            $nivel = $contador+1;
            
            if($value['saldoAnterior'] != "0,00"  || $value['saldoAtual'] != "0,00" ){
                $this->write[] = "|J100|{$value['classificador']}|{$nivel}|{$grupo_cta}|{$value['descricao']}|{$value['saldoAtual']}|{$value['ind_dc_ini']}|{$value['saldoAnterior']}|{$value['ind_dc_fim']}|\n";  
                $this->total_registros_bloco_j_ecd              += 1;
                $this->array_totais_registros_ecd['J100']       += 1;
            }
        }
    }   
}

//DEMONSTRAÇÃO DOS RESULTADOS
public function demonstracao_resultados($dados){
    $data_inicio     = $dados['dta_ini'];
    $data_fim        = $dados['dta_fin'];
    
    $arrayClassificacao = $this->gera_balancete($data_inicio, $data_fim);    
    
    foreach($arrayClassificacao as $value){
        
        $prim_num = substr($value['classificador'], 0, 1);
        //lançar somente as sintéticas ATIVO OU PASSIVO
        if($value['analitica_sintetica'] == "S" && $prim_num >2){
            //verifica a natureza e atribui credor/devedor
            if($value['natureza'] == 1){

                $value['ind_dc_ini'] = "N";
                $value['ind_dc_fim'] = "N";

            }else{
                $value['ind_dc_ini'] = "P";
                $value['ind_dc_fim'] = "P";                
            }

            //verifica se valor é negativo e inverte C/D
            if($value['saldoAnterior'] < 0){
                $value['saldoAnterior'] = $value['saldoAnterior'] * (-1);
                $value['ind_dc_ini'] = ($value['ind_dc_ini'] == "D") ? "R" : "D"; 
            }

            if($value['saldoAtual'] < 0){
                $value['saldoAtual'] = $value['saldoAtual'] * (-1);
                $value['ind_dc_fim'] = ($value['ind_dc_fim'] == "D") ? "R" : "D";
            }

            $contador = substr_count($value['classificador'], ".");
            $nivel = $contador+1;

            $value['saldoAtual']    = number_format($value['saldoAtual'], 2, ",", "");
            $value['saldoAnterior']    = number_format($value['saldoAnterior'], 2, ",", "");

            if(is_null($value['saldoAtual'])){
                $value['saldoAtual'] = "0,00";
            }

            if(is_null($value['saldoAnterior'])){
                $value['saldoAnterior'] = "0,00";
            }
            
            if($value['saldoAnterior'] != "0,00" || $value['saldoAtual'] != "0,00"){
                $this->write[] = "|J150|{$value['classificador']}|{$nivel}|{$value['descricao']}|{$value['saldoAtual']}|{$value['ind_dc_ini']}|{$value['saldoAnterior']}|{$value['ind_dc_fim']}|\n";
                $this->total_registros_bloco_j_ecd +=1;
                $this->array_totais_registros_ecd['J150'] += 1;
            }
        }
    }
}

//TABELA DE HISTÓRICO DE FATOS CONTÁBEIS QUE MODIFICAM A CONTA LUCROS ACUMULADOS OU A CONTA PREJUÍZOS ACUMULADOS OU TODO O PATRIMÔNIO LÍQUIDO
public function tab_hist_fatos_cont(){
    $this->write[] = "|J200|10|REVERSÃO DE RESERVA LEGAL|\n"; 
    $this->total_registros_bloco_j_ecd +=1;    
    $this->array_totais_registros_ecd['J200'] = 1;
}

//DEMONSTRAÇÃO DE LUCROS OU PREJUÍZOS ACUMULADOS (DLPA)/DEMONSTRAÇÃO DE MUTAÇÕES DO PATRIMÔNIO LÍQUIDO (DMPL)
public function dem_lucros_preju(){
    $this->write[] = "|J210|0|1.1|LUCROS ACUMULADOS|0,00|C|0,00|C|\n";  
    $this->total_registros_bloco_j_ecd +=1;
    $this->array_totais_registros_ecd['J210'] = 1;
}

//FATO CONTÁBIL QUE ALTERA A CONTA LUCROS ACUMULADOS OU A CONTA PREJUÍZOS ACUMULADOS OU O PATRIMÔNIO LÍQUIDO
public function fato_contabil_alt(){
    $this->write[] = "|J215|10|1000,00|C|\n";  
    $this->total_registros_bloco_j_ecd +=1;
    $this->array_totais_registros_ecd['J215'] = 1;
}

//OUTRAS INFORMAÇÕES
public function outras_infos(){
     $this->write[] = "|J800|001|Notas\n";    
     $this->total_registros_bloco_j_ecd +=1;
     $this->array_totais_registros_ecd['J800'] = 1;
}

//TERMO DE VERIFICAÇÃO PARA FINS DE SUBSTITUIÇÃO DA ECD
public function termo_de_verif(){
    $this->write[] = "|J801|001|Notas\n";    
    $this->total_registros_bloco_j_ecd +=1;
    $this->array_totais_registros_ecd['J801'] = 1;
}

//TERMO DE ENCERRAMENTO - Nível Hierárquico 2
public function termo_de_encerramento($dados, $data_empresa) {
    
    $dados['dta_ini'] = RemoveCaracteres($dados['dta_ini']);
    $dados['dta_fin'] = RemoveCaracteres($dados['dta_fin']);
    
    $this->write[] = "|J900|TERMO DE ENCERRAMENTO|{$dados['num_ord']}|{$dados['nat_livro']}|{$data_empresa['razao']}|SUBSTUTUIR_TOTAL_LINHAS|{$dados['dta_ini']}|{$dados['dta_fin']}|\n";
    $this->total_registros_bloco_j_ecd +=1;
    $this->array_totais_registros_ecd['J900'] = 1;
}

//IDENTIFICAÇÃO DOS SIGNATÁRIOS DA ESCRITURAÇÃO E DO TERMO DE VERIFICAÇÃO PARA FINS DE SUBSTITUIÇÃO DA ECD J930 
public function id_signatarios($id_contador, $data_empresa){
    
    $sqlContador = "SELECT * FROM contabil_contador WHERE id_contador = {$id_contador}";
    $res = mysql_query($sqlContador);
    $contador = mysql_fetch_assoc($res);
    
    $cpf = RemoveCaracteres($contador['cpf']) ;
    $crc = RemoveCaracteres($contador['crc']) ;
    $tel = $contador['tel_comercial'];

    $cpf_responsavel = RemoveCaracteres($contador['cpf']);
    $cpf_responsavel = trim($cpf_responsavel);
    
    $this->write[] = "|J930|{$contador['nome']}|{$cpf}|CONTADOR|900|{$crc}|{$contador['email']}|{$tel}|{$contador['crc_uf']}|RJ/2012/001||N|\n"; 
    $this->total_registros_bloco_j_ecd +=1;
    $this->array_totais_registros_ecd['J930'] += 1;
    
    $this->write[] = "|J930|INSTITUTO LAGOS RIO|07813739000161|Signatário da ECD com e-CNPJ ou e-PJ|001|||||||S|\n"; 
    $this->total_registros_bloco_j_ecd +=1;
    $this->array_totais_registros_ecd['J930'] += 1;

    $this->write[] = "|J930|{$data_empresa['responsavel']}|{$cpf_responsavel}|Diretor|203|||||||N|\n"; 
    $this->total_registros_bloco_j_ecd +=1;
    $this->array_totais_registros_ecd['J930'] += 1;
}

//IDENTIFICAÇÃO DOS AUDITORES INDEPENDENTES
public function id_auditores($dados){
    
    $this->write[] = "|J935|{$dados['nome_auditor']}|{$dados['registro_auditor']}|\n";  
    $this->total_registros_bloco_j_ecd +=1;
    $this->array_totais_registros_ecd['J935'] = 1;
}

//ENCERRAMENTO DO BLOCO J
public function encerramento_bloco_j(){
    $this->total_registros_bloco_j_ecd +=1;
    $this->array_totais_registros_ecd['J990'] = 1;
    $this->write[] = "|J990|{$this->total_registros_bloco_j_ecd}|\n";        
}


//ABERTURA DO BLOCO K
public function abertura_bloco_k(){
    $this->write[] = "|K001|1|\n";    
    $this->total_registros_bloco_k_ecd +=1;
    $this->array_totais_registros_ecd['K001'] = 1;
}

//PERÍODO DA ESCRITURAÇÃO CONTÁBIL CONSOLIDADA
public function periodo_esc_contabil($dados){
    $dados['dta_ini'] = RemoveCaracteres($dados['dta_ini']);
    $dados['dta_fin'] = RemoveCaracteres($dados['dta_fin']);
    $this->write[] = "|K030|{$dados['dta_ini']}|{$dados['dta_fin']}|\n";
    $this->total_registros_bloco_k_ecd +=1;
    $this->array_totais_registros_ecd['K030'] = 1;
}

//RELAÇÃO DAS EMPRESAS CONSOLIDADAS
public function relacao_emp_consolidadas(){
    $this->write[] = "|K100|105|1234| 11111111|EMPRESA PARTICIPANTE Z|30,00|S|100,00|01012016|31122016|\n";
    $this->total_registros_bloco_k_ecd +=1;
    $this->array_totais_registros_ecd['K100'] = 1;
}

//RELAÇÃO DOS EVENTOS SOCIETÁRIOS
public function relacao_eventos_societarios(){
    $this->write[] = "|K110|1|30032016|\n";
    $this->total_registros_bloco_k_ecd +=1;
    $this->array_totais_registros_ecd['K110'] = 1;
}

//EMPRESAS PARTICIPANTES DO EVENTO SOCIETÁRIO 
public function empresas_part_ev_societario(){
    $this->write[] = "|K115|1234|1|50,00|\n";
    $this->total_registros_bloco_k_ecd +=1;
    $this->array_totais_registros_ecd['K115'] = 1;
}

//PLANO DE CONTAS CONSOLIDADO
public function plano_contas_consolidado(){
    $this->write[] = "|K200|01|S|1|1||ATIVO|\n";
    $this->total_registros_bloco_k_ecd +=1;
    $this->array_totais_registros_ecd['K200'] = 1;
}

//MAPEAMENTO PARA O PLANO DE CONTAS DAS EMPRESAS CONSOLIDADAS
public function mapeamento_para_plano_de_contas(){
    $this->write[] = "|K210|1234|1.01.01.01|\n";
    $this->total_registros_bloco_k_ecd +=1;
    $this->array_totais_registros_ecd['K210'] = 1;
}

//SALDOS DAS CONTAS CONSOLIDADAS
public function saldo_contas_cons(){
    $this->write[] = "|K300|1.01.01.01.01|1000,00|D|300,00|D|700,00|D|\n";
    $this->total_registros_bloco_k_ecd +=1;
    $this->array_totais_registros_ecd['K300'] = 1;
}

//EMPRESAS DETENTORAS DAS PARCELAS DO VALOR ELIMINADO TOTAL
public function emp_detentoras_parc(){
    $this->write[] = "|K310|1234|100,00|D|\n";
    $this->total_registros_bloco_k_ecd +=1;
    $this->array_totais_registros_ecd['K310'] = 1;
}

//EMPRESAS CONTRAPARTES DAS PARCELAS DO VALOR ELIMINDADO TOTAL
public function emp_contrapartes(){
    $this->write[] = "|K315|5678|2.01.02.01.02|100,00|D|\n";
    $this->total_registros_bloco_k_ecd +=1;
    $this->array_totais_registros_ecd['K315'] = 1;
}
//ENCERRAMENTO DO BLOCO K
public function encerramento_bloco_k(){
    $this->total_registros_bloco_k_ecd +=1;
    $this->array_totais_registros_ecd['K990'] = 1;
    $this->write[] = "|K990|{$this->total_registros_bloco_k_ecd}|\n";    
}


//ABERTURA DO BLOCO 9
public function abertura_bloco_9(){
    $this->total_registros_bloco_9_ecd +=1;
    $this->write[] = "|9001|0|\n";
    $this->array_totais_registros_ecd['9001'] = 1;
}

//REGISTROS DO ARQUIVO
public function registros_arq(){
    $total9900 = 0 ;
    $this->array_totais_registros_ecd['9990'] = 1;
    $this->array_totais_registros_ecd['9999'] = 1;
    
    foreach($this->array_totais_registros_ecd as $key => $value){
        $this->total_registros_bloco_9_ecd +=1;
        $this->write[] = "|9900|{$key}|{$value}|\n";  
        $total9900 +=1 ;
    }
    
    $this->total_registros_bloco_9_ecd +=1;
    $total9900 +=1 ;
    $this->write[] = "|9900|9900|{$total9900}|\n";
}

//ENCERRAMENTO DO BLOCO 9
public function encerramento_bloco_9(){
    $this->total_registros_bloco_9_ecd +=2;
    $this->write[] = "|9990|{$this->total_registros_bloco_9_ecd}|\n";
}

//ENCERRAMENTO DO ARQUIVO DIGITAL
public function encerramento_arquivo_digital(){
        
    $this->write[] = "|9999|SUBSTUTUIR_TOTAL_LINHAS|\n";    
}

/* 
_____
FIM DOS REGISTROS
-----
*/


/* 
_____
FIM ECD
-----
*/

/* 
_____
INICIO DO SPED - ECF
-----
*/

/* 
_____
BLOCOS PRINCIPAIS ECF
-----
*/

//ABERTURA E IDENTIFICAÇÃO
//BLOCO 0
public function bloco_0_ecf($dados, $data_empresa){
    $this->abertura_arquivo_id_pj($dados);
    $this->abertura_bloco_0_ecf($dados);
    $this->parametros_tributacao($dados);
    $this->parametros_complementares($dados);
    
    // se IND_PJ_HAB estiver marcado, incluir os parametros tipo de programa 
    if($dados['repes_recap_etc'] == 'on'){
        $this->parametros_id_tipos_programa($dados);
    }       
    $this->dados_cadastrais($data_empresa);
    
    //se tipo da ecf = 1, incluir registro 0035
    if($dados['tipo_ecf'] == 1){
        $this->id_scp_ecf($dados);
    }
         
    
    $this->id_sig_ecf($dados['sig_esc_ecf'], $dados['qualif_assinante']);
    $this->enc_bloco_0_ecf();
}

//Informações Recuperadas das ECD (Bloco recuperado pelo sistema. Não é importado e não é editado no programa)
//BLOCO C
public function bloco_c_ecf(){
//    $this->abertura_bloco_C();
//    $this->id_ecd();
//    $this->plano_contas_ecd();
//    $this->plano_contas_referencial_ecd();
//    $this->subcontas_correlatas_ecf();
//    $this->centro_de_custos();
//    $this->id_periodo_saldos_periodicos();
//    $this->detalhes_saldos_contabeis_ct_patr();
//    $this->trsfr_saldos_ct_anterior();
//    $this->id_data_saldos_ct_res_ant_enc();
//    $this->det_sal_cont_res_ant_enc();
//    $this->encerramento_bloco_c();
}

//Informações Recuperadas da ECF Anterior e Cálculo Fiscal dos Dados Recuperados da ECD (Bloco recuperado pelo sistema ? Não é
//importado e não é editado no programa)
//BLOCO E
public function bloco_e_ecf(){
//    $this->abertura_bloco_e();
//    $this->saldos_finais_ecf_anterior();
//    $this->contas_contabeis_mapeadas();
//    $this->saldos_fin_cont_pb_e_lalur();
//    $this->id_periodo();
//    $this->det_saldos_cont_calc_base_ecd() ;
//    $this->det_saldos_cont_res_ant_enc();
//    $this->enc_bloco_e();
}

//Plano de Contas e Mapeamento
//importados do ecd
//BLOCO J
public function bloco_j_ecf($dados){
//    $this->abertura_bloco_j_ecf();
    
//     if(($dados['forma_trib_lucro'] == 1 || $dados['forma_trib_lucro'] == 2 || $dados['forma_trib_lucro'] == 3 || $dados['forma_trib_lucro'] == 4) || (($dados['forma_trib_lucro'] == 5 || $dados['forma_trib_lucro'] == 7 || $dados['forma_trib_lucro'] == 8 || $dados['forma_trib_lucro'] == 9) && $dados['tipo_escrituracao_ecf'] == "C" )){
//        $this->pln_contas_cont();
//        $this->pln_contas_ref();
//        $this->subcontas_correlatas_ecf2();
//     }    
//     
//     if(($dados['forma_trib_lucro'] == 1 || $dados['forma_trib_lucro'] == 2 || $dados['forma_trib_lucro'] == 3 || $dados['forma_trib_lucro'] == 4) || (($dados['forma_trib_lucro'] == 5 || $dados['forma_trib_lucro'] == 7 || $dados['forma_trib_lucro'] == 8 || $dados['forma_trib_lucro'] == 9) && $dados['tipo_escrituracao_ecf'] == "C" )){
//         $this->centro_de_custos_ecf();
//     }

    
//    $this->enc_bloco_j();
}

//Saldos das Contas Contábeis e Referenciais
//BLOCO K
public function bloco_k_ecf($dados){
    
//    if(($dados['forma_trib_lucro'] == 1 || $dados['forma_trib_lucro'] == 2 || $dados['forma_trib_lucro'] == 3 || $dados['forma_trib_lucro'] == 4) || (($dados['forma_trib_lucro'] == 7 || $dados['forma_trib_lucro'] == 8 || $dados['forma_trib_lucro'] == 9) && ($dados['tipo_escrituracao_ecf'] == "C"))){
//        $obr = 0;
//    }    else{
//        $obr = 1;
//    }
//    
//    $this->abertura_bloco_k_ecf($obr);   
//    
//    if(($dados['forma_trib_lucro'] == 1 || $dados['forma_trib_lucro'] == 2 || $dados['forma_trib_lucro'] == 3 || $dados['forma_trib_lucro'] == 4) || (($dados['forma_trib_lucro'] == 7 || $dados['forma_trib_lucro'] == 8 || $dados['forma_trib_lucro'] == 9) && ($dados['tipo_escrituracao_ecf'] == "C"))){
//        $this->id_pr_for_ap_ifpj_csll($dados);
//        //    $this->det_sald_cont();
//        //    $this->map_ref_sald_fin();
//    }
    

//    $this->saldos_fin_contas_cont_res_antes_enc();
//    $this->map_refer_sald_fin_ct_res_ant_enc();
//    $this->enc_bloco_k();
}    

//Lucro Líquido ? Lucro Real
//BLOCO L 
public function bloco_l_ecf($dados){
    
    if($dados['forma_trib_lucro'] == 1 || $dados['forma_trib_lucro'] == 2 || $dados['forma_trib_lucro'] == 3 || $dados['forma_trib_lucro'] == 4){
        $obr = 0;
    }else{
        $obr = 1;
    }
    
    $this->abertura_bloco_l($obr);
    
    if($dados['forma_trib_lucro'] == 1 || $dados['forma_trib_lucro'] == 2 || $dados['forma_trib_lucro'] == 3 || $dados['forma_trib_lucro'] == 4){
        $this->id_per_for_ap_irpj_csll($dados);
        
//        $this->balanco_patr();
//        $this->met_av_est_fin();
//        $this->inf_com_custos();
//        $this->dem_res_liq_per_fiscal();
    }

    $this->enc_bloco_l();
}  

//e-LALUR e e-LACS ? Lucro Real
// BLOCO M
public function bloco_m_ecf($dados){
    $this->abertura_bloco_m();
    if($dados['forma_trib_lucro'] == 1 || $dados['forma_trib_lucro'] == 2 || $dados['forma_trib_lucro'] == 3 || $dados['forma_trib_lucro'] == 4){
//    $this->id_cont_pb_elalur_elacs();
//    $this->id_per_for_ap_irpj_csll_lr();
//    $this->dem_lr();
//    $this->cont_partb_rel_lan_pa_lalur();
//    $this->cont_cont_rel_lan_pa_elalur();
//    $this->n_lan_rel_cont_cont();
//    $this->id_proc_jud_adm();
//    $this->dem_base_calc();
//    $this->cont_b_rel_lanc_pa_elacs();
//    $this->contas_cont_rel_lanc_pa_elacs();
//    $this->n_lanc_rel_cont_cont();
//    $this->id_pja_ref_lanc();
//    $this->lanc_cont_b_lalur_elacs_sem_refl_a();
//    $this->id_proc_ja_ref_lanc();
//    $this->contr_sald_cont_pb_elalur_elacs();
    }
    
    $this->enc_bloco_m();
}  

//Cálculo do IRPJ e da CSLL ? Lucro Real
//BLOCO N
public function bloco_n_ecf($dados){
    $this->abertura_bloco_n();
    
    if($dados['forma_trib_lucro'] == 1 || $dados['forma_trib_lucro'] == 2 || $dados['forma_trib_lucro'] == 3 || $dados['forma_trib_lucro'] == 4){
        $this->id_per_form_irpj_csll_emp_trib_luc_real();
        $this->base_calc_irpj_luc_real();
//    $this->dem_luc_exploracao();
//    $this->calc_isenc_red_imp_lr();
//    $this->inf_base_calc_inc_fisc();
//    $this->ap_irpj_mens_est();
//    $this->ap_irpj_luc_real();
        $this->base_calc_csll_apos_comp();
//    $this->ap_csll_estimativa();
//    $this->ap_csll_base_lr();
    }
    
    $this->enc_bloco_n();
} 

//Lucro Presumido
//BLOCO P
public function bloco_p_ecf($dados){
    $this->abertura_bloco_p();
//    if($dados['forma_trib_lucro'] == 3 || $dados['forma_trib_lucro'] == 4 || $dados['forma_trib_lucro'] == 5 || $dados['forma_trib_lucro'] == 7) {  
//        $this->id_per_forma_irpj_csll_empt_trib_lp();
//        if($dados['tipo_escrituracao_ecf'] == "C"){
//        $this->balanco_pat();
//        }
//
//        if($dados['isencao_red_lucro_presumido'] == "on" && $dados['opt_refis'] == "on"){
//            $this->dem_rec();
//        }
//
//        if($dados['tipo_escrituracao_ecf'] == "C"){
//            $this->dem_result();
//        }
//
//        $this->ap_base_calc_lp();
//
//        if($dados['isencao_red_lucro_presumido'] == "on" && $dados['opt_refis'] == "on"){
//            $this->calc_isenc_lp();
//        }
//
//        $this->calc_irpj_lp();
//        $this->ap_bs_calc_lp();
//        $this->csll_ll();
//    }
    
    $this->enc_bloco_p();
} 

//Demonstrativo do Livro Caixa
//BLOCO Q
public function bloco_q_ecf($dados){
    $this->abertura_bloco_q();
    
//    if($dados['tipo_escrituracao_ecf'] == "L"){
//        $this->demonstrativo_livro_caixa();
//    }
    
    $this->encerramento_bloco_q();
} 

//Lucro Arbitrado
//BLOCO T
public function bloco_t_ecf($dados){
    $this->abertura_bloco_t();
    
//    if($dados['forma_trib_lucro'] == 2 || $dados['forma_trib_lucro'] == 4 || $dados['forma_trib_lucro'] == 6 || $dados['forma_trib_lucro'] == 7) { 
//        $this->id_per_irpj_csll_la();
//        $this->bas_calc_la();
//        $this->ir_la();
//        $this->base_calc_csll_la();
//        $this->calc_csll_la();
//    }
    
    $this->encerramento_bloco_t();
}

//Imunes e Isentas
//BLOCO U
public function bloco_u_ecf($dados){
    $this->abertura_bloco_u();
    
    if($dados['forma_trib_lucro'] == 8 || $dados['forma_trib_lucro'] == 9 ) { 
       $this->id_per_iprj_csll_im_is($dados);
       
       if(($dados["exist_ativ_trib"] == "A" || $dados["exist_ativ_trib"] == "T") || ($dados["apuracao_csll"] == "A" || $dados["apuracao_csll"] == "T")){
           
           
            $this->bal_patrimon();
            
            
            
            $this->dem_results($dados);
            
            
            
       }
       
       if($dados["exist_ativ_trib"] == "A" || $dados["exist_ativ_trib"] == "T"){
           $this->calc_irpj_im_is();
       }
        
       if(($dados["exist_ativ_trib"] == "A" || $dados["exist_ativ_trib"] == "T") || ($dados["apuracao_csll"] == "A" || $dados["apuracao_csll"] == "T")){
           $this->csll_im_is();
       }
    }

    $this->enc_bloco_u();
} 


//Relatório País-a-País
//BLOCO W
public function bloco_w_ecf($dados){
    $this->abertura_bloco_w();
    
    if($dados["ind_pais_a_pais"] == "on"){
        $this->inf_grupo_mult();
        $this->dec_pais_pais();
        $this->dec_p_p_ent_int();
    }
    $this->obs_adicionais();
    $this->enc_bloco_w();
} 

//Informações Econômicas
//BLOCO X
public function bloco_x_ecf($dados){
    
    $this->abertura_bloco_x();
    
    if($dados['lucro_exploracao'] == "on" || $dados['isencao_red_lucro_presumido'] == "on"){
        $this->ativ_incent();        
    }
    
    if($dados['operacoes_exterior'] == 'on' && $dados['operacoes_vinculada'] == "on"){
        $this->op_ext_pessoa();
    }
    
    if($dados['operacoes_exterior'] == 'on' && $dados['operacoes_vinculada'] != "on") {
        $this->op_ext_p_nao_vinc();
    }
     //     $this->vinc_int_pais_tf();
    
    
    if($dados['operacoes_exterior'] == 'on' && $dados['operacoes_vinculada'] == "on"){
        $this->op_ext_exports();
        $this->op_ext_contrat_exp();
        
        $this->op_ext_import();
        $this->op_ext_contrat_import();
    }
    
    if($dados['participacoes_exterior'] == 'on'){
        $this->id_part_ext();
        $this->parts_ext_res_per_ap();
        $this->demons_res_imp_pag_ext();
        $this->dem_res_ext_auf_int_col_reg_caixa();
        $this->dem_consolida();
        $this->dem_preju_acumulados();
        $this->dem_rendas_at_pas();
        $this->dem_estr_societ();
    }
    
    
    if($dados['forma_trib_lucro'] == 8 || $dados['forma_trib_lucro'] == 9 ) {
        $this->origem_aplcs_recursos_im_is();
    }
    
    
    if($dados['comercio_eletronico'] == 'on' ){
        $this->com_elet_tec_inf_vend();
        $this->com_elet_inf_server();
    }
    
    if($dados['royalties_brasil_exterior'] == 'on' || $dados['rendimentos_sjd'] == 'on'){
        $this->royalties_rec_pag_ext();
    }
    
    if($dados['rendimentos_sjd'] == 'on'){
        $this->rend_rel_serv_juros_div();
    }
    
    if($dados['pagamentos_remessas'] == 'on'){
        $this->pag_rem_rel_serv_juros();
    }
    
    if($dados['inovacao_tec'] == 'on'){
        $this->inov_tec_des_tec();
    }
    
    if($dados['cap_info'] == 'on'){
        $this->cap_info_incl_dig();
    }
    
    if($dados['repes_recap_etc'] == 'on'){
        $this->repes_recap_padis();
    }
    
    if($dados['polo_manaus'] == 'on'){
        $this->polo_ind_man_am();
    }
    
    if($dados['zon_processamento_exp'] == 'on'){
        $this->zon_process_exp();
    }
    
    if($dados['areas_livre_comercio'] == 'on'){
        $this->areas_liv_com();
    }
    
    $this->encerramento_bloco_x_ecf();
} 

//Informações Gerais
//BLOCO Y
public function bloco_y_ecf($dados){
    $this->abertura_bloco_y();
    
    if($dados['rec_exterior'] == 'on' || $dados['pags_ao_exterior'] == 'on' ){
        $this->pags_recbs_ext_nao_res();
    }
    
    if($dados['tipo_ent_imune_isenta'] != 6 && $dados['tipo_ent_imune_isenta'] != 13 && $dados['tipo_ent_imune_isenta'] != 14 ){
        $this->disc_receita_vend_est_at_ec();
    }
    
    if($dados['vendas_exp'] == 'on'){
        $this->vendas_com_exp_fim_esp();
    }
     
    if($dados['pj_comercial_exportadora'] == 'on'){
        $this->det_exp_com_exp();
    }
    
    if($dados['exist_ativ_trib'] != 'D' && $dados['apuracao_csll'] != 'D'){
        $this->demons_ir_csll_rf();
    }
   
    if($dados['doacoes_eleitorais'] == 'on'){
        $this->doacoes_camp_el();
    }
    
    if($dados['ativos_exterior'] == 'on'){
        $this->ativ_exterior();
    }
    
    if($dados['forma_trib_lucro'] == 1 || $dados['forma_trib_lucro'] == 2 || $dados['forma_trib_lucro'] == 3 || $dados['forma_trib_lucro'] == 4 || $dados['forma_trib_lucro'] == 5 || $dados['forma_trib_lucro'] == 6 || $dados['forma_trib_lucro'] == 7 ){
        $this->id_rem_socios_tit();
    }
    
    if($dados['forma_trib_lucro'] == 8 || $dados['forma_trib_lucro'] == 9){
        $this->id_rend_dir_cons();
    }
    
    if($dados['equiv_patr'] == 'on'){
        $this->part_av_met_equiv();
    }
    
    if($dados['adm_clubes'] == 'on'){
        $this->fund_inv();
    }
    
    if($dados['part_consorcios'] == 'on'){
        $this->parts_cons_empre();
    }
    
    $this->parts_cons();
    
    if($dados['indicador_situacao_especial_ecf'] == '2' || $dados['indicador_situacao_especial_ecf'] == '3' || $dados['indicador_situacao_especial_ecf'] == '5' || $dados['indicador_situacao_especial_ecf'] == '6' ){
        $this->dados_sucess();
    }
    
    if($dados['forma_trib_lucro'] == '1' || $dados['forma_trib_lucro'] == '2' || $dados['forma_trib_lucro'] == '3' || $dados['forma_trib_lucro'] == '4' ){
        $this->outras_info();
    }
    
    if($dados['forma_trib_lucro'] == '5' || $dados['forma_trib_lucro'] == '6' || $dados['forma_trib_lucro'] == '7'){
        $this->outras_info_lpla();
    }
    
    if($dados['forma_trib_lucro'] != '8' && $dados['forma_trib_lucro'] != '9' && $dados['opt_refis'] != 'on'){
        $this->mes_info_opt_refis();
        $this->info_opt_refis_lr_pa();
        $this->info_opt_refis_im_is();
    }
    
    
    if($dados['opt_paes'] == "on"){
        $this->info_opt_paes();
    }
    
        
//    $this->info_perio_ante();
//    $this->outras_infors();
    $this->encerramento_bloco_y_ecf();
} 

//Encerramento do Arquivo Digital
//BLOCO 9
public function bloco_9_ecf(){
    $this->abertura_bloco_9_ecf();
//    $this->avisos_escr();  // --> INSERIDO PELO SISTEMA
    $this->regs_do_arq();
    $this->enc_bloco_9_ecf();
    $this->enc_arquivo_digital_ecf();
} 

public function write_ecf(){
    
    //Adicionando o total de linhas do arquivo nos registros
    $total_linhas_ecf = count($this->write);
    
    foreach($this->write as $key => $line){
        if(strpos($line, "|SUBSTUTUIR_TOTAL_LINHAS|")){
            $this->write[$key] = str_replace("SUBSTUTUIR_TOTAL_LINHAS", $total_linhas_ecf, $line);            
        }
    }
    
    //ESCREVER NO ARQUIVO
    foreach($this->write as $line){
        fwrite($this->arquivo, $line);
    }
}



/* 
_____
FIM BLOCOS PRINCIPAIS ECF
-----
*/

/* 
_____
INICIO REGISTROS ECF
-----
*/

//Abertura do Arquivo Digital e Identificação da Pessoa Jurídica
public function abertura_arquivo_id_pj($dados){
    
    // CNPJ
    $dados['cnpj_ecf'] = RemoveCaracteres($dados['cnpj_ecf']);    
    $cnpj_ecf = sprintf("%014s", $dados['cnpj_ecf']);
    
    // Data evento especial
    $dta_esp = RemoveCaracteres($dados['dta_esp']);
    
    //Data início
    $dta_ini_ecf = RemoveCaracteres($dados['dta_ini_ecf']);
    
    //Data Fim
    $dta_fin_ecf = RemoveCaracteres($dados['dta_fin_ecf']);
    
    //Recibo anterior
    $num_recibo_anterior_ecf = RemoveCaracteres($dados['num_recibo_anterior_ecf']);
    
    //Cod ISCP
    $id_scp = RemoveCaracteres($dados['id_scp']);
    
    $this->write[] =  "|0000|LECF|0003|{$cnpj_ecf}|{$dados['nome_empresarial_ecf']}|{$dados['indicador_situacao_inicio_periodo_ecf']}|{$dados['indicador_situacao_especial_ecf']}|{$dados['patr_remanesc_cisao_ecf']}|{$dta_esp}|{$dta_ini_ecf}|{$dta_fin_ecf}|{$dados['esc_retificadora_ecf']}|{$num_recibo_anterior_ecf}|{$dados['tipo_ecf']}|{$id_scp}|\n";
    $this->total_registros_bloco_0_ecf +=1;    
    $this->array_totais_registros_ecf['0000'] =1;
} 

//Abertura do Bloco 0
public function abertura_bloco_0_ecf($arquivo, $dados){
    $this->write[] =  "|0001|0|\n";
    $this->total_registros_bloco_0_ecf +=1;   
    $this->array_totais_registros_ecf['0001'] =1;
} 

//Parâmetros de Tributação
public function parametros_tributacao($dados){
    // O segundo parâmetro  --- HASH ---  é preenchido automaticamente pelo sistema
    
    $opt_refis = ($dados['opt_refis'] == 'on') ? 'S' : 'N' ;
    $opt_paes  = ($dados['opt_paes']  == 'on') ? 'S' : 'N' ;
    
    $this->write[] =  "|0010||{$opt_refis}|{$opt_paes}|{$dados['forma_trib_lucro']}|{$dados['per_apuracao_irpj']}|{$dados['qual_pessoa_juridica']}|{$dados['1o_trim']}{$dados['2o_trim']}{$dados['3o_trim']}{$dados['4o_trim']}|{$dados['est_janeiro']}{$dados['est_fevereiro']}{$dados['est_marco']}{$dados['est_abril']}{$dados['est_maio']}{$dados['est_junho']}{$dados['est_julho']}{$dados['est_agosto']}{$dados['est_setembro']}{$dados['est_outubro']}{$dados['est_novembro']}{$dados['est_dezembro']}|{$dados['tipo_escrituracao_ecf']}|{$dados['tipo_ent_imune_isenta']}|{$dados['exist_ativ_trib']}|{$dados['apuracao_csll']}|{$dados['ind_rec_receita']}|\n";
    $this->total_registros_bloco_0_ecf +=1;
    $this->array_totais_registros_ecf['0010'] =1;
} 

//Parâmetros Complementares
public function parametros_complementares($dados){
    
    //Quantidade de SCP da PJ
    $qtd_scp_pj = $dados['qtd_scp_pj'];
    if(is_null($dados['qtd_scp_pj'])){
        $qtd_scp_pj = 0;
    }
    
    //TRATAMENTO DOS CHECKBOXES
    $adm_clubes = ($dados['adm_clubes'] == 'on') ? 'S' : 'N' ;
    $part_consorcios = ($dados['part_consorcios'] == 'on') ? 'S' : 'N' ;
    $operacoes_exterior = ($dados['operacoes_exterior'] == 'on') ? 'S' : 'N' ;
    $operacoes_vinculada = ($dados['operacoes_vinculada'] == 'on') ? 'S' : 'N' ;
    $pj_artigos48_49 = ($dados['pj_artigos48_49'] == 'on') ? 'S' : 'N' ;
    $participacoes_exterior = ($dados['participacoes_exterior'] == 'on') ? 'S' : 'N' ;
    $ativ_rural = ($dados['ativ_rural'] == 'on') ? 'S' : 'N' ;
    $lucro_exploracao = ($dados['lucro_exploracao'] == 'on') ? 'S' : 'N' ;
    $isencao_red_lucro_presumido = ($dados['isencao_red_lucro_presumido'] == 'on') ? 'S' : 'N' ;
    $finor_finam = ($dados['finor_finam'] == 'on') ? 'S' : 'N' ;
    $doacoes_eleitorais = ($dados['doacoes_eleitorais'] == 'on') ? 'S' : 'N' ;
    $equiv_patr = ($dados['equiv_patr'] == 'on') ? 'S' : 'N' ;
    $vendas_exp = ($dados['vendas_exp'] == 'on') ? 'S' : 'N' ;
    $rec_exterior = ($dados['rec_exterior'] == 'on') ? 'S' : 'N' ;
    $ativos_exterior = ($dados['ativos_exterior'] == 'on') ? 'S' : 'N' ;
    $pj_comercial_exportadora = ($dados['pj_comercial_exportadora'] == 'on') ? 'S' : 'N' ;
    $pags_ao_exterior = ($dados['pags_ao_exterior'] == 'on') ? 'S' : 'N' ;
    $comercio_eletronico = ($dados['comercio_eletronico'] == 'on') ? 'S' : 'N' ;
    $royalties_brasil_exterior = ($dados['royalties_brasil_exterior'] == 'on') ? 'S' : 'N' ;
    $royalties_pagos = ($dados['royalties_pagos'] == 'on') ? 'S' : 'N' ;
    $rendimentos_sjd = ($dados['rendimentos_sjd'] == 'on') ? 'S' : 'N' ;
    $pagamentos_remessas = ($dados['pagamentos_remessas'] == 'on') ? 'S' : 'N' ;
    $inovacao_tec = ($dados['inovacao_tec'] == 'on') ? 'S' : 'N' ;
    $cap_info = ($dados['cap_info'] == 'on') ? 'S' : 'N' ;
    $repes_recap_etc = ($dados['repes_recap_etc'] == 'on') ? 'S' : 'N' ;
    $polo_manaus = ($dados['polo_manaus'] == 'on') ? 'S' : 'N' ;
    $zon_processamento_exp = ($dados['zon_processamento_exp'] == 'on') ? 'S' : 'N' ;    
    $areas_livre_comercio = ($dados['areas_livre_comercio'] == 'on') ? 'S' : 'N' ;
    $ind_pais_a_pais = ($dados['ind_pais_a_pais'] == 'on') ? 'S' : 'N';
    
    
    
    $this->write[] =  "|0020|{$dados['aliq_csll']}|{$qtd_scp_pj}|{$adm_clubes}|{$part_consorcios}|{$operacoes_exterior}|{$operacoes_vinculada}|{$pj_artigos48_49}|{$participacoes_exterior}|{$ativ_rural}|{$lucro_exploracao}|{$isencao_red_lucro_presumido}|{$finor_finam}|{$doacoes_eleitorais}|{$equiv_patr}|{$vendas_exp}|{$rec_exterior}|{$ativos_exterior}|{$pj_comercial_exportadora}|{$pags_ao_exterior}|{$comercio_eletronico}|{$royalties_brasil_exterior}|{$royalties_pagos}|{$rendimentos_sjd}|{$pagamentos_remessas}|{$inovacao_tec}|{$cap_info}|{$repes_recap_etc}|{$polo_manaus}|{$zon_processamento_exp}|{$areas_livre_comercio}|{$ind_pais_a_pais}|\n";
    $this->total_registros_bloco_0_ecf += 1;    
    $this->array_totais_registros_ecf['0020'] += 1;
    
} 

//Parâmetros de Identificação dos Tipos de Programa

public function parametros_id_tipos_programa($dados){
        
    $repes = ($dados['repes'] == 'on') ? 'S' : 'N' ;
    $recap = ($dados['recap'] == 'on') ? 'S' : 'N' ;
    $padis = ($dados['padis'] == 'on') ? 'S' : 'N' ;
    $patvd = ($dados['patvd'] == 'on') ? 'S' : 'N' ;
    $reidi = ($dados['reidi'] == 'on') ? 'S' : 'N' ;
    $repenec = ($dados['repenec'] == 'on') ? 'S' : 'N' ;
    $recine = ($dados['recine'] == 'on') ? 'S' : 'N' ;
    $res_solidos = ($dados['res_solidos'] == 'on') ? 'S' : 'N' ;
    $recopa = ($dados['recopa'] == 'on') ? 'S' : 'N' ;
    $copa_do_mundo = ($dados['copa_do_mundo'] == 'on') ? 'S' : 'N' ;
    $retid = ($dados['retid'] == 'on') ? 'S' : 'N' ;
    $repnbl = ($dados['repnbl'] == 'on') ? 'S' : 'N' ;
    $reif = ($dados['reif'] == 'on') ? 'S' : 'N' ;
    $olimpiadas = ($dados['olimpiadas'] == 'on') ? 'S' : 'N' ;
    $reicomp = ($dados['reicomp'] == 'on') ? 'S' : 'N' ;
    $retaero = ($dados['retaero'] == 'on') ? 'S' : 'N' ;
    
    $this->write[] =  "|0021|{$repes}|{$recap}|{$padis}|{$patvd}|{$reidi}|{$repenec}|{$reicomp}|{$retaero}|{$recine}|{$res_solidos}|{$recopa}|{$copa_do_mundo}|{$retid}|{$repnbl}|{$reif}|{$olimpiadas}|\n";
    $this->total_registros_bloco_0_ecf +=1;     
    $this->array_totais_registros_ecf['0021'] =1;
    
} 

//Dados Cadastrais
public function dados_cadastrais($data_empresa){
    
    $data_empresa['cnae'] = RemoveCaracteres($data_empresa['cnae']);
    $data_empresa['cod_municipio'] = RemoveCaracteres($data_empresa['cod_municipio']);
    $data_empresa['cep'] = RemoveCaracteres($data_empresa['cep']);
    $data_empresa['tel'] = RemoveCaracteres($data_empresa['tel']);
    
    $this->write[] =  "|0030|{$data_empresa['natureza']}|{$data_empresa['cnae']}|{$data_empresa['logradouro']}|{$data_empresa['numero']}|{$data_empresa['complemento']}|{$data_empresa['bairro']}|{$data_empresa['uf']}|{$data_empresa['cod_municipio']}|{$data_empresa['cep']}|{$data_empresa['tel']}|{$data_empresa['email']}|\n";
    $this->total_registros_bloco_0_ecf +=1;    
    $this->array_totais_registros_ecf['0030'] =1;
   
} 

//Identificação das SCP
public function id_scp_ecf($dados){
    
    $dados['cnpj_da_scp'] = RemoveCaracteres($dados['cnpj_da_scp']);
    
    $this->write[] =  "|0035|{$dados['cnpj_da_scp']}|{$dados['nome_da_scp']}|\n";
    $this->total_registros_bloco_0_ecf +=1; 
    $this->array_totais_registros_ecf['0035'] =1;
} 

//Identificação dos Signatários da ECF
public function id_sig_ecf($id_contador, $id_qualif){    
   
    $id_contador = implode(", ",$id_contador);
    
    $sqlContador = "SELECT * FROM contabil_contador WHERE id_contador IN ({$id_contador})";
    $res      = mysql_query($sqlContador);
    
    while($contador_res = mysql_fetch_assoc($res)){
        $contador[] = $contador_res;
    }    
    
    $id_qualif_count = 0;
   foreach($contador as $value){
       
        $cpf = RemoveCaracteres($value['cpf']) ;
        $crc = RemoveCaracteres($value['crc']) ;
        $tel = $value['tel_comercial'];

        $this->write[] =  "|0930|{$value['nome']}|{$cpf}|{$id_qualif[$id_qualif_count]}|{$crc}|{$value['email']}|{$tel}|\n";
        $this->total_registros_bloco_0_ecf +=1;
        $this->array_totais_registros_ecf['0930'] += 1;
        
        $id_qualif_count++;
   }
} 

//Encerramento do Bloco 0
public function enc_bloco_0_ecf(){
    
    $this->total_registros_bloco_0_ecf +=1;
    $this->array_totais_registros_ecf['0990'] +=1;
    $this->write[] =  "|0990|{$this->total_registros_bloco_0_ecf}|\n";    
} 

//Abertura do Bloco C - Informações Recuperadas da ECD
public function abertura_bloco_C(){
    $this->write[] =  "|C001|1|\n";
    $this->total_registros_bloco_c_ecf +=1;   
    $this->array_totais_registros_ecf['C001'] += 1;
} 

//Identificador da ECD
public function id_ecd(){
    
}

//Plano de Contas da ECD
public function plano_contas_ecd(){
    
}

//Plano de Contas Referencial Preenchido na ECD
public function plano_contas_referencial_ecd(){
    
}

//Subcontas Correlatas
public function subcontas_correlatas_ecf(){
    
}

//Centro de Custos
public function centro_de_custos(){
    
}

//Identificação do Período dos Saldos Periódicos das Contas Patrimoniais
public function id_periodo_saldos_periodicos(){
    
}

//Detalhes dos Saldos Contábeis das Contas Patrimoniais
public function detalhes_saldos_contabeis_ct_patr(){
    
}

//Transferência de Saldos do Plano de Contas Anterior
public function trsfr_saldos_ct_anterior(){
    
}

//Identificação da Data dos Saldos das Contas de Resultado Antes do Encerramento
public function id_data_saldos_ct_res_ant_enc(){
    
}

//Detalhes dos Saldos das Contas de Resultado Antes do Encerramento
public function det_sal_cont_res_ant_enc(){
    
}

//Encerramento do Bloco C
public function encerramento_bloco_c(){
    $this->total_registros_bloco_c_ecf +=1;
    $this->array_totais_registros_ecf['C990'] +=1;
    $this->write[] =  "|C990|{$this->total_registros_bloco_c_ecf}|\n";  
}

//Abertura do Bloco E ? Informações Recuperadas da ECF Anterior e Cálculo Fiscal dos Dados Recuperados da ECD
public function abertura_bloco_e(){
    $this->write[] =  "|E001|1|\n";
    $this->total_registros_bloco_e_ecf +=1;   
    $this->array_totais_registros_ecf['E001'] += 1;
}

//Saldos Finais Recuperados da ECF Anterior
public function saldos_finais_ecf_anterior(){
    
}

//Contas Contábeis Mapeadas
public function contas_contabeis_mapeadas(){
    
}

//Saldos Finais das Contas da Parte B do e-Lalur da ECF
//Imediatamente Anterior
public function saldos_fin_cont_pb_e_lalur(){
    
}

//Identificação do Período
public function id_periodo(){
    
}

//Detalhes dos Saldos Contábeis Calculados com Base nas ECD
public function det_saldos_cont_calc_base_ecd(){
    
}

//Detalhes dos Saldos das Contas de Resultado Antes do Encerramento
public function det_saldos_cont_res_ant_enc(){
    
}

//Encerramento do Bloco E
public function enc_bloco_e(){
    $this->total_registros_bloco_e_ecf +=1;
    $this->array_totais_registros_ecf['E990'] +=1;
    $this->write[] =  "|E990|{$this->total_registros_bloco_e_ecf}|\n"; 
}

//Abertura do Bloco J - Plano de Contas e Mapeamento
public function abertura_bloco_j_ecf(){
    $this->write[] =  "|J001|0|\n";
    $this->total_registros_bloco_j_ecf +=1 ;
    $this->array_totais_registros_ecf['J001'] =1;    
}

//Plano de Contas do Contribuinte
public function pln_contas_cont(){
    $this->total_registros_bloco_j_ecf +=1 ;
    $this->write[] =  "|J050|01012014|01|S|1|1||Ativo Sintética 1|\n";
    $this->array_totais_registros_ecf['J050'] =1; 
}

//Plano de Contas Referencial
public function pln_contas_ref(){
    $this->total_registros_bloco_j_ecf +=1 ;
    $this->write[] =  "|J051||1.01.01.01.01|\n";
    $this->array_totais_registros_ecf['J051'] =1; 
}

//Subcontas Correlatas
public function subcontas_correlatas_ecf2(){
    $this->total_registros_bloco_j_ecf +=1 ;
    $this->write[] =  "|J053|FT1234|1.05.01.10|02|\n";
    $this->array_totais_registros_ecf['J053'] =1;
}

//Centro de Custos
public function centro_de_custos_ecf(){
    $this->total_registros_bloco_j_ecf +=1;
    $this->write[] =  "|J100|01012014|1234|CENTRO DE CUSTOS 1234|\n";
    $this->array_totais_registros_ecf['J100'] =1;
}

//Encerramento do Bloco J
public function enc_bloco_j(){
    $this->total_registros_bloco_j_ecf +=1 ;
    $this->array_totais_registros_ecf['J990'] +=1; 
    $this->write[] =  "|J990|{$this->total_registros_bloco_j_ecf}|\n";
    
}

//Abertura do Bloco K ? Saldos das Contas Contábeis e Referenciais
public function abertura_bloco_k_ecf($obr){
    $this->total_registros_bloco_k_ecf += 1;
    $this->write[] =  "|K001|{$obr}|\n";
    $this->array_totais_registros_ecf['K001'] += 1; 
}

//Identificação dos Períodos e Formas de Apuração do IRPJ e da CSLL no Ano-Calendário
public function id_pr_for_ap_ifpj_csll($dados){
    
    $data_inicio =  $dados['dta_ini_ecf'];
    $data_final  =  $dados['dta_fin_ecf'];
    $mes_dt_ini = (int)substr($data_inicio, 3, 2);
    $mes_dt_fim = (int)substr($data_final, 3, 2);   
    
    $data_inicio_dash = str_replace("/", "-", $data_inicio);
    $data_final_dash = str_replace("/", "-", $data_final);
    $timestamp_ini = strtotime($data_inicio_dash);
    $timestamp_fim = strtotime($data_final_dash);
        
    //Período de Apuração [para 0010.FORMA_APUR = ?A? ou (0010.FORMA_APUR_I = ?A? OU 0010.APUR_CSLL = ?A? E 0010.TIP_ESC_PRE = ?C?)]     
    if(($dados['per_apuracao_irpj'] == "A") || ($dados['apuracao_csll'] == "A" || $dados['exist_ativ_trib'] == "A" && $dados['tipo_escrituracao_ecf'] == "C" )){
        
        for($i = $mes_dt_ini ; $i <= $mes_dt_fim ; $i++){
            
            if($i < 10){
                $i = "0".$i;
            }
            
            $data_ini_reg = "01-{$i}-2016";
            $data_fim_reg = date("t-m-Y", strtotime($data_ini_reg));
            
            $data_ini_reg_no_dash = str_replace("-", "", $data_ini_reg);
            $data_fim_reg_no_dash = str_replace("-", "", $data_fim_reg);
            
            $this->total_registros_bloco_k_ecf +=1;
            $this->write[] =  "|K030|{$data_ini_reg_no_dash}|{$data_fim_reg_no_dash}|A{$i}|\n";
            $this->array_totais_registros_ecf['K030'] +=1;
            
        }
        
    //Indicador do período de referência [para 0010.FORMA_APUR = ?T? OU (0010.FORMA_APUR = ?A? E 0010.FORMA_TRIB = ?2?) ou (0010.FORMA_APUR_I = ?T? OU 0010.APUR_CSLL = ?T? E 0010.TIP_ESC_PRE = ?C?)]
    }else if(($dados['per_apuracao_irpj'] == "T") || ($dados['per_apuracao_irpj'] == "A" && $dados['forma_trib_lucro'] == "2") || (($dados['exist_ativ_trib'] == "T" || $dados['apuracao_csll'] == "T") && $dados['tipo_escrituracao_ecf'] == "C" )){
        
        // datas dos trimestres
        $timestamp_ini_1 = strtotime("01-01-2016");
        $timestamp_ini_2 = strtotime("01-04-2016");
        $timestamp_ini_3 = strtotime("01-07-2016");
        $timestamp_ini_4 = strtotime("01-10-2016");
        
        //1o trimestre
        if($timestamp_fim >= $timestamp_ini_1){
            $this->total_registros_bloco_k_ecf +=1;
            $this->write[] =  "|K030|01012016|31032016|T01|\n";
            $this->array_totais_registros_ecf['K030'] +=1;
        }
            
        //2o trimestre
        if($timestamp_fim >= $timestamp_ini_2){
            $this->total_registros_bloco_k_ecf +=1;
            $this->write[] =  "|K030|01042016|30062016|T02|\n";
            $this->array_totais_registros_ecf['K030'] +=1;
        }
            
        //3o trimestre
        if($timestamp_fim >= $timestamp_ini_3){
            $this->total_registros_bloco_k_ecf +=1;
            $this->write[] =  "|K030|01072016|30092016|T03|\n";
            $this->array_totais_registros_ecf['K030'] +=1;
        }
            
        //4o trimestre
        if($timestamp_fim >= $timestamp_ini_4)   {
            $this->total_registros_bloco_k_ecf +=1;
            $this->write[] =  "|K030|01102016|31122016|T04|\n";
            $this->array_totais_registros_ecf['K030'] +=1;
        } 
    }
}

//Detalhes dos Saldos Contábeis (Depois do Encerramento do Resultado do Período)
public function det_sald_cont(){
    $this->total_registros_bloco_k_ecf +=1;
    $this->write[] =  "|K155|2328.2.0001||0,00|D|7500,00|5000,00|2500,00|D|\n";
    $this->array_totais_registros_ecf['K155'] +=1;
}

//Mapeamento Referencial do Saldo Final
public function map_ref_sald_fin(){
    $this->total_registros_bloco_k_ecf +=1;
    $this->write[] =  "|K156|1.01.01.01.01|5000,00|D|\n";
    $this->array_totais_registros_ecf['K156'] +=1;
}

//Saldos Finais das Contas Contábeis de Resultado Antes do Encerramento
public function saldos_fin_contas_cont_res_antes_enc(){
    $this->total_registros_bloco_k_ecf += 1;
    $this->write[] =  "|K355|3.01.1234||5000,00|C|\n";
    $this->array_totais_registros_ecf['K355'] += 1;
}

//Mapeamento Referencial dos Saldos Finais das Contas de Resultado Antes do Encerramento
public function map_refer_sald_fin_ct_res_ant_enc(){
    $this->total_registros_bloco_k_ecf +=1;
    $this->write[] =  "|K356|3.01.01.01.01.01|5000,00|C|\n";
    $this->array_totais_registros_ecf['K356'] =1;
}

//Encerramento do Bloco K
public function enc_bloco_k(){
    $this->total_registros_bloco_k_ecf += 1;
    $this->array_totais_registros_ecf['K990'] += 1;
    $this->write[] =  "|K990|{$this->total_registros_bloco_k_ecf}|\n";
    
}

//Abertura do Bloco L - Lucro Real
public function abertura_bloco_l($obr){
    $this->total_registros_bloco_l_ecf +=1;
    $this->write[] =  "|L001|{$obr}|\n";
    $this->array_totais_registros_ecf['L001'] =1;
}

//Identificação dos Períodos e Formas de Apuração do IRPJ e da CSLL no Ano-Calendário
public function id_per_for_ap_irpj_csll($dados){
    
    $data_inicio =  $dados['dta_ini_ecf'];
    $data_final  =  $dados['dta_fin_ecf'];
    $mes_dt_ini = (int)substr($data_inicio, 3, 2);
    $mes_dt_fim = (int)substr($data_final, 3, 2);   
    
    $data_inicio_dash = str_replace("/", "-", $data_inicio);
    $data_final_dash = str_replace("/", "-", $data_final);
    $timestamp_ini = strtotime($data_inicio_dash);
    $timestamp_fim = strtotime($data_final_dash);
    
    //Período de Apuração [para 0010.FORMA_APUR = ?A? ou (0010.FORMA_APUR_I = ?A? OU 0010.APUR_CSLL = ?A? E 0010.TIP_ESC_PRE = ?C?)]     
    if(($dados['per_apuracao_irpj'] == "A") || ($dados['apuracao_csll'] == "A" || $dados['exist_ativ_trib'] == "A" && $dados['tipo_escrituracao_ecf'] == "C" )){
        
        for($i = $mes_dt_ini ; $i <= $mes_dt_fim ; $i++){
            
            if($i < 10){
                $i = "0".$i;
            }
            
            $data_ini_reg = "01-{$i}-2016";
            $data_fim_reg = date("t-m-Y", strtotime($data_ini_reg));
            
            $data_ini_reg_no_dash = str_replace("-", "", $data_ini_reg);
            $data_fim_reg_no_dash = str_replace("-", "", $data_fim_reg);
            
            $this->total_registros_bloco_l_ecf +=1;
            $this->write[] =  "|L030|{$data_ini_reg_no_dash}|{$data_fim_reg_no_dash}|A{$i}|\n";
            $this->array_totais_registros_ecf['L030'] +=1;
            
        }
        
    //Indicador do período de referência [para 0010.FORMA_APUR = ?T? OU (0010.FORMA_APUR = ?A? E 0010.FORMA_TRIB = ?2?) ou (0010.FORMA_APUR_I = ?T? OU 0010.APUR_CSLL = ?T? E 0010.TIP_ESC_PRE = ?C?)]
    }else if(($dados['per_apuracao_irpj'] == "T") || ($dados['per_apuracao_irpj'] == "A" && $dados['forma_trib_lucro'] == "2") || (($dados['exist_ativ_trib'] == "T" || $dados['apuracao_csll'] == "T") && $dados['tipo_escrituracao_ecf'] == "C" )){
        
        // datas dos trimestres
        $timestamp_ini_1 = strtotime("01-01-2016");
        $timestamp_ini_2 = strtotime("01-04-2016");
        $timestamp_ini_3 = strtotime("01-07-2016");
        $timestamp_ini_4 = strtotime("01-10-2016");
        
        //1o trimestre
        if($timestamp_fim >= $timestamp_ini_1){
            $this->total_registros_bloco_l_ecf +=1;
            $this->write[] =  "|L030|01012016|31032016|T01|\n";
            $this->array_totais_registros_ecf['L030'] +=1;
        }
            
        //2o trimestre
        if($timestamp_fim >= $timestamp_ini_2){
            $this->total_registros_bloco_l_ecf +=1;
            $this->write[] =  "|L030|01042016|30062016|T02|\n";
            $this->array_totais_registros_ecf['L030'] +=1;
        }
            
        //3o trimestre
        if($timestamp_fim >= $timestamp_ini_3){
            $this->total_registros_bloco_l_ecf +=1;
            $this->write[] =  "|L030|01072016|30092016|T03|\n";
            $this->array_totais_registros_ecf['L030'] +=1;
        }
            
        //4o trimestre
        if($timestamp_fim >= $timestamp_ini_4)   {
            $this->total_registros_bloco_l_ecf +=1;
            $this->write[] =  "|L030|01102016|31122016|T04|\n";
            $this->array_totais_registros_ecf['L030'] +=1;
        }
    }
}

//Balanço Patrimonial
public function balanco_patr(){
    $this->total_registros_bloco_l_ecf +=1;
    $this->write[] =  "|L100|2.03.04.01.99|Contas de Patrimônio Líquido Não Classificadas|A|5|03|2.03.04.01|10000,00|C|20000,00|C|\n";
    $this->array_totais_registros_ecf['L100'] =1;
    
}

//Método de Avaliação do Estoque Final
public function met_av_est_fin(){
    $this->total_registros_bloco_l_ecf +=1;
    $this->write[] =  "|L200|2|\n";
    $this->array_totais_registros_ecf['L200'] =1;
}

//Informativo da Composição de Custos
public function inf_com_custos(){
    $this->total_registros_bloco_l_ecf +=1;
    $this->write[] =  "|L210|92|Constituição de Provisões|1000,00|\n";
    $this->array_totais_registros_ecf['L210'] =1;
}

//Demonstração do Resultado Líquido no Período Fiscal
public function dem_res_liq_per_fiscal(){
    $this->total_registros_bloco_l_ecf +=1;
    fwrite($this->arquivo, "|L300|3.11.05.01.03.03|Outras Participações|A|6|04|3.11.05.01.03|10000,00|D|\n");
    $this->array_totais_registros_ecf['L300'] =1;
    
}

//Encerramento do Bloco L
public function enc_bloco_l(){
    $this->total_registros_bloco_l_ecf += 1;
    $this->write[] =  "|L990|{$this->total_registros_bloco_l_ecf}|\n";
    $this->array_totais_registros_ecf['L990'] += 1;
}

//Abertura do Bloco M ? Livro Eletrônico de Apuração do
//Lucro Real (e-Lalur) e Livro Eletrônico de Apuração da Base
//de Cálculo da CSLL (e-Lacs)
public function abertura_bloco_m(){
    $this->total_registros_bloco_m_ecf +=1;
    $this->write[] =  "|M001|1|\n";
    $this->array_totais_registros_ecf['M001'] +=1;
}

//Identificação da Conta na Parte B e-Lalur e do e-Lacs
public function id_cont_pb_elalur_elacs(){
    $this->total_registros_bloco_m_ecf +=1;
    $this->write[] =  "|M010|101|CONTA DA PARTE B|01012013|103|Depreciação Acelerada Incentivada - (Lei nº 11.196/2005, art. 31)|31122016|I|1000,00|D||\n";
    $this->array_totais_registros_ecf['M010'] =1;
    
}

//Identificação do Período e Forma de Apuração do IRPJ e da
//CSLL das Empresas Tributadas pelo Lucro Real
public function id_per_for_ap_irpj_csll_lr(){
    $this->total_registros_bloco_m_ecf +=1;
    $this->write[] =  "|M030|01012014|31032014|T01|\n";
    $this->array_totais_registros_ecf['M030'] =1;
}

//Demonstração do Lucro Real
public function dem_lr(){
    $this->total_registros_bloco_m_ecf +=1;
    $this->write[] =  "|M300|138|(-) Outras Exclusões|E|1|1000,00|LANÇAMENTO DE EXCLUSÃO XXXX|\n";
    $this->array_totais_registros_ecf['M300'] =1;
}

//Contas da Parte B Relacionadas ao Lançamento da Parte A
//do e-Lalur
public function cont_partb_rel_lan_pa_lalur(){
    $this->total_registros_bloco_m_ecf +=1;
    $this->write[] =  "|M305|101|2000,00|D|\n";
    $this->array_totais_registros_ecf['M305'] =1;
}

//Contas Contábeis Relacionadas ao Lançamento da Parte A
//do e-Lalur
public function cont_cont_rel_lan_pa_elalur(){
    $this->total_registros_bloco_m_ecf +=1;
    $this->write[] =  "|M310|1.01.01.01||1000,00|D|\n";
    $this->array_totais_registros_ecf['M310'] =1;
}

//Números dos Lançamentos Relacionados à Conta Contábil
public function n_lan_rel_cont_cont(){
    $this->total_registros_bloco_m_ecf +=1;
    $this->write[] =  "|M312|12345|\n";
    $this->array_totais_registros_ecf['M312'] =1;
}

//Identificação de Processos Judiciais e Administrativos
//Referentes ao Lançamento
public function id_proc_jud_adm(){
    $this->total_registros_bloco_m_ecf +=1;
    $this->write[] =  "|M315|1|123456789|\n";
    $this->array_totais_registros_ecf['M315'] =1;
}

//Demonstração da Base de Cálculo da CSLL
public function dem_base_calc(){
    $this->total_registros_bloco_m_ecf +=1;
    $this->write[] =  "|M350|138|(-) Outras Exclusões|E|1|1000,00|LANÇAMENTO DE EXCLUSÃO XXXX|\n";
    $this->array_totais_registros_ecf['M350'] =1;
}

//Contas da Parte B Relacionadas ao ao Lançamento da Parte
//A do e-Lacs
public function cont_b_rel_lanc_pa_elacs(){
    $this->total_registros_bloco_m_ecf +=1;
    $this->write[] =  "|M355|101|2000,00|D|\n";
    $this->array_totais_registros_ecf['M355'] =1;
}

//Contas Contábeis Relacionadas ao Lançamento da Parte A
//do e-Lacs
public function contas_cont_rel_lanc_pa_elacs(){
    $this->total_registros_bloco_m_ecf +=1;
    $this->write[] =  "|M360|1.01.01.01||1000,00|D|\n";
    $this->array_totais_registros_ecf['M360'] =1;
}

//Números dos Lançamentos Relacionados à Conta Contábil
public function n_lanc_rel_cont_cont(){
    $this->total_registros_bloco_m_ecf +=1;
    $this->write[] =  "|M362|12345|\n";
    $this->array_totais_registros_ecf['M362'] =1;
}

//Identificação de Processos Judiciais e Administrativos
//Referentes ao Lançamento
public function id_pja_ref_lanc(){
    $this->total_registros_bloco_m_ecf +=1;
    $this->write[] =  "|M365|1|123456789|\n";
    $this->array_totais_registros_ecf['M365'] =1;
}

//Lançamentos na Conta da Parte B do e-Lalur e do e-Lacs
//Sem Reflexo na Parte A
public function lanc_cont_b_lalur_elacs_sem_refl_a(){
    $this->total_registros_bloco_m_ecf +=1;
    $this->write[] =  "|M410|101|I|1000,00|CR|202|LANÇAMENTO DE CRÉDITO EM VIRTUDE DA OCORRÊNCIA XXXX|\n";
    $this->array_totais_registros_ecf['M410'] =1;
}

//Identificação de Processos Judiciais e Administrativos
//Referentes ao Lançamento
public function id_proc_ja_ref_lanc(){
    $this->total_registros_bloco_m_ecf +=1;
    $this->write[] =  "|M415|1|123456789|\n";
    $this->array_totais_registros_ecf['M415'] =1;
}

//Controle de Saldos das Contas da Parte B do e-Lalur e do e-
//Lacs
public function contr_sald_cont_pb_elalur_elacs(){
    $this->total_registros_bloco_m_ecf +=1;
    $this->write[] =  "|M500|101|1000,00|C|500,00|D|100,00|D|400,00|C|\n";
    $this->array_totais_registros_ecf['M500'] =1;
}

//Encerramento do Bloco M
public function enc_bloco_m(){
    $this->total_registros_bloco_m_ecf +=1;
    $this->array_totais_registros_ecf['M990'] +=1;
    $this->write[] =  "|M990|{$this->total_registros_bloco_m_ecf}|\n";
}

//Abertura do bloco N ? Cálculo do IRPJ e da CSLL
public function abertura_bloco_n(){
    $this->total_registros_bloco_n_ecf +=1;
    $this->write[] =  "|N001|1|\n";
    $this->array_totais_registros_ecf['N001'] =1;
}

//Identificação do Período e Forma de Apuração do IRPJ e da
//CSLL das Empresas Tributadas pelo Lucro Real
public function id_per_form_irpj_csll_emp_trib_luc_real(){
    $this->write[] =  "|N030|01012014|31032014|T01|\n";
    $this->array_totais_registros_ecf['N030'] =1;
}

//Base de Cálculo do IRPJ Sobre o Lucro Real Após as
//Compensações de Prejuízos
public function base_calc_irpj_luc_real(){
    $this->write[] =  "|N500|1|Valor da base de cálculo do IRPJ|100000,00|\n";
    $this->array_totais_registros_ecf['N500'] =1;
}

///Demonstração do Lucro da Exploração
public function dem_luc_exploracao(){
    $this->write[] =  "|N600|66|Parcela Correspondente às Demais Atividades|10000,00|\n";
    $this->array_totais_registros_ecf['N600'] =1;
}

//Cálculo da Isenção e Redução do Imposto sobre Lucro Real
public function calc_isenc_red_imp_lr(){
    $this->write[] =  "|N610|77|REDUÇÃO POR REINVESTIMENTO|10000,00|\n";
    $this->array_totais_registros_ecf['N610'] =1;
}

//Informações da Base de Cálculo de Incentivos Fiscais
public function inf_base_calc_inc_fisc(){
    $this->write[] =  "|N615|2000,00|3,00|60,00|3,00|60,00|120,00|\n";
    $this->array_totais_registros_ecf['N615'] =1;
}

//Apuração do IRPJ Mensal por Estimativa
public function ap_irpj_mens_est(){
    $this->write[] =  "|N620|7|(-) Operações de Caráter Cultural e Artístico|10000,00|\n";
    $this->array_totais_registros_ecf['N620'] =1;
}

//Apuração do IRPJ Com Base no Lucro Real
public function ap_irpj_luc_real(){
    $this->write[] =  "|N630|21|(-) Imposto de Renda Mensal Pago por Estimativa|10000,00|\n";
    $this->array_totais_registros_ecf['N630'] =1;
}

//Base de Cálculo da CSLL Após Compensações das Bases de
//Cálculo Negativa
public function base_calc_csll_apos_comp(){
    $this->write[] =  "|N650|1|Valor da base de cálculo da CSLL|100000,00|\n";
    $this->array_totais_registros_ecf['N650'] =1;
}

//Apuração da CSLL Mensal por Estimativa
public function ap_csll_estimativa(){
    $this->write[] =  "|N660|19|CSLL A PAGAR|10000,00|\n";
    $this->array_totais_registros_ecf['N660'] =1;
}

//Apuração da CSLL Com Base no Lucro Real
public function ap_csll_base_lr(){
    $this->write[] =  "|N670|23|CSLL POSTERGADA DE PERÍODOS DE APURAÇÃO ANTERIORES|10000,00|\n";
    $this->array_totais_registros_ecf['N670'] =1;
}

//Encerramento do Bloco N
public function enc_bloco_n(){
    $this->total_registros_bloco_n_ecf +=1;
    $this->array_totais_registros_ecf['N990'] =1;
    $this->write[] =  "|N990|{$this->total_registros_bloco_n_ecf}|\n";
    
}

//Abertura do Bloco P ? Lucro Presumido
public function abertura_bloco_p(){
    $this->total_registros_bloco_p_ecf +=1;
    $this->write[] =  "|P001|1|\n";
    $this->array_totais_registros_ecf['P001'] =1;
}

//Identificação dos Período e Forma de Apuração do IRPJ e da
//CSLL das Empresas Tributadas pelo Lucro Presumido
public function id_per_forma_irpj_csll_empt_trib_lp(){
    $this->total_registros_bloco_p_ecf +=1;
    $this->write[] =  "|P030|01092014|31122014|T04|\n";
    $this->array_totais_registros_ecf['P030'] =1;
}

///Balanço Patrimonial
public function balanco_pat(){
    $this->total_registros_bloco_p_ecf +=1;
    fwrite($this->arquivo, "|P100|2.03.04.01.99|Contas de Patrimônio Líquido Não Classificadas|A|5|03|2.03.04.01|10000,00|C|20000,00|C|\n");
    $this->array_totais_registros_ecf['P100'] =1;
    
}

//Demonstração das Receitas Incentivadas do Lucro
//Presumido
public function dem_rec(){
    $this->total_registros_bloco_p_ecf +=1;
    $this->write[] =  "|P130|82|TOTAL DO LUCRO PRESUMIDO AJUSTADO|100000,00|\n";
    $this->array_totais_registros_ecf['P130'] =1;
}

///Demonstração do Resultado
public function dem_result(){
    $this->total_registros_bloco_p_ecf +=1;
    $this->write[] =  "|P150|3.11.05.01.03.03|Outras Participações|A|6|04|3.11.05.01.03|10000,00|D|\n";
    $this->array_totais_registros_ecf['P150'] =1;
}

//Apuração da Base de Cálculo do Lucro Presumido
public function ap_base_calc_lp(){
    $this->total_registros_bloco_p_ecf +=1;
    $this->write[] =  "|P200|25|(-)Divulgação Eleitoral e Partidária Gratuita|10000,00|\n";
    $this->array_totais_registros_ecf['P200'] =1;
}

//Cálculo da Isenção e Redução do Lucro Presumido
public function calc_isenc_lp(){
    $this->total_registros_bloco_p_ecf +=1;
    $this->write[] =  "|P230|37|TOTAL DA ISENÇÃO E REDUÇÃO|10000,00|\n";
    $this->array_totais_registros_ecf['P230'] =1;
}

//Cálculo do IRPJ com Base no Lucro Presumido
public function calc_irpj_lp(){
    $this->total_registros_bloco_p_ecf +=1;
    $this->write[] =  "|P300|17|IMPOSTO DE RENDA POSTERGADO DE PERÍODOS DE APURAÇÃO ANTERIORES|10000,00|\n";
    $this->array_totais_registros_ecf['P300'] =1;
}

//Apuração da Base de Cálculo da CSLL com Base no Lucro
//Presumido
public function ap_bs_calc_lp(){
    $this->total_registros_bloco_p_ecf +=1;
    $this->write[] =  "|P400|18|(-)Excedente de Variação Cambial (MP nº 1.858-10/1999, art. 31)|10000,00|\n";
    $this->array_totais_registros_ecf['P400'] =1;
}

//Cálculo da CSLL com Base no Lucro Líquido
public function csll_ll(){
    $this->total_registros_bloco_p_ecf +=1;
    $this->write[] =  "|P500|15|CSLL POSTERGADA DE PERÍODOS DE APURAÇÃO ANTERIORES|10000,00|\n";
    $this->array_totais_registros_ecf['P500'] =1;
}

//Encerramento do Bloco P
public function enc_bloco_p(){
    $this->total_registros_bloco_p_ecf +=1;
    $this->array_totais_registros_ecf['P990'] =1;
    $this->write[] =  "|P990|{$this->total_registros_bloco_p_ecf}|\n";
    
}

//Abertura do Bloco Q - Livro Caixa
public function abertura_bloco_q(){
    $this->total_registros_bloco_q_ecf +=1;
    $this->write[] =  "|Q001|1|\n";
    $this->array_totais_registros_ecf['Q001'] =1;
}

//Demonstrativo do Livro Caixa
public function demonstrativo_livro_caixa(){
    $this->total_registros_bloco_q_ecf +=1;
    $this->write[] =  "|Q100|01092015|123|HISTORICO|1000,00|0,00|1000,00|\n";
    $this->array_totais_registros_ecf['Q100'] =1;
}

//Encerramento do Bloco Q
public function encerramento_bloco_q(){
    $this->total_registros_bloco_q_ecf +=1;
    $this->array_totais_registros_ecf['Q990'] =1;
    $this->write[] =  "|Q990|{$this->total_registros_bloco_q_ecf}|\n";
    
}

//Abertura do Bloco T - Lucro Arbitrado
public function abertura_bloco_t(){
    $this->total_registros_bloco_t_ecf +=1;
    $this->write[] =  "|T001|1|\n";
    $this->array_totais_registros_ecf['T001'] =1;
}

//Identificação dos Período e Forma de Apuração do IRPJ e
//CSLL das Empresas Tributadas pelo Lucro Arbitrado
public function id_per_irpj_csll_la(){
    $this->total_registros_bloco_t_ecf +=1;
    $this->write[] =  "|T030|01042014|30062014|T02|\n";
    $this->array_totais_registros_ecf['T030'] =1;
}

//Apuração da Base de Cálculo do IRPJ com Base no Lucro
//Arbitrado
public function bas_calc_la(){
    $this->total_registros_bloco_t_ecf +=1;
    $this->write[] =  "|T120|26|BASE DE CÁLCULO|1000000,00|\n";
    $this->array_totais_registros_ecf['T120'] =1;
}

//Cálculo do Imposto de Renda com Base no Lucro Arbitrado
public function ir_la(){
    $this->total_registros_bloco_t_ecf +=1;
    $this->write[] =  "|T150|16|IMPOSTO DE RENDA A PAGAR|100000,00|\n";
    $this->array_totais_registros_ecf['T150'] =1;
}

//Apuração da Base de Cálculo da CSLL com Base no Lucro
//Arbitrado
public function base_calc_csll_la(){
    $this->total_registros_bloco_t_ecf +=1;
    $this->write[] =  "|T170|13|Lucros Disponibilizados no Exterior|100000,00|\n";
    $this->array_totais_registros_ecf['T170'] =1;
}

///Cálculo da CSLL com Base no Lucro Arbitrado
public function calc_csll_la(){
    $this->total_registros_bloco_t_ecf +=1;
    $this->write[] =  "|T181|17|CSLL POSTERGADA DE PERÍODOS DE APURAÇÃO ANTERIORES|10000,00|\n";
    $this->array_totais_registros_ecf['T181'] =1;
}
//Encerramento do Bloco T
public function encerramento_bloco_t(){
    $this->total_registros_bloco_t_ecf +=1;
    $this->array_totais_registros_ecf['T990'] =1;
    $this->write[] =  "|T990|{$this->total_registros_bloco_t_ecf}|\n";
   
}

//Abertura do Bloco U ? Imunes e Isentas
public function abertura_bloco_u(){
    $this->total_registros_bloco_u_ecf +=1;
    $this->write[] =  "|U001|1|\n";
    $this->array_totais_registros_ecf['U001'] =1;
}

//Identificação dos Períodos e Formas de Apuração do IPRJ e
//da CSLL das Empresas Imunes e Isentas
public function id_per_iprj_csll_im_is($dados){
    
    $data_inicio =  $dados['dta_ini_ecf'];
    $data_final  =  $dados['dta_fin_ecf'];
    $mes_dt_ini = (int)substr($data_inicio, 3, 2);
    $mes_dt_fim = (int)substr($data_final, 3, 2);   
    
    $data_inicio_dash = str_replace("/", "-", $data_inicio);
    $data_final_dash = str_replace("/", "-", $data_final);
    $timestamp_ini = strtotime($data_inicio_dash);
    $timestamp_fim = strtotime($data_final_dash);
        
    //Período de Apuração [para 0010.FORMA_APUR = ?A? ou (0010.FORMA_APUR_I = ?A? OU 0010.APUR_CSLL = ?A? E 0010.TIP_ESC_PRE = ?C?)]     
    if(($dados['per_apuracao_irpj'] == "A") || ($dados['apuracao_csll'] == "A" || $dados['exist_ativ_trib'] == "A" && $dados['tipo_escrituracao_ecf'] == "C" )){
        
        for($i = $mes_dt_ini ; $i <= $mes_dt_fim ; $i++){
            
            if($i < 10){
                $i = "0".$i;
            }
            
            $data_ini_reg = "01-{$i}-2016";
            $data_fim_reg = date("t-m-Y", strtotime($data_ini_reg));
            
            $data_ini_reg_no_dash = str_replace("-", "", $data_ini_reg);
            $data_fim_reg_no_dash = str_replace("-", "", $data_fim_reg);
            
            $this->total_registros_bloco_u_ecf +=1;
            $this->write[] =  "|U030|{$data_ini_reg_no_dash}|{$data_fim_reg_no_dash}|A{$i}|\n";
            $this->array_totais_registros_ecf['U030'] +=1;
            
        }
        
    //Indicador do período de referência [para 0010.FORMA_APUR = ?T? OU (0010.FORMA_APUR = ?A? E 0010.FORMA_TRIB = ?2?) ou (0010.FORMA_APUR_I = ?T? OU 0010.APUR_CSLL = ?T? E 0010.TIP_ESC_PRE = ?C?)]
    }else if(($dados['per_apuracao_irpj'] == "T") || ($dados['per_apuracao_irpj'] == "A" && $dados['forma_trib_lucro'] == "2") || (($dados['exist_ativ_trib'] == "T" || $dados['apuracao_csll'] == "T") && $dados['tipo_escrituracao_ecf'] == "C" )){
        
        // datas dos trimestres
        $timestamp_ini_1 = strtotime("01-01-2016");
        $timestamp_ini_2 = strtotime("01-04-2016");
        $timestamp_ini_3 = strtotime("01-07-2016");
        $timestamp_ini_4 = strtotime("01-10-2016");
        
        //1o trimestre
        if($timestamp_fim >= $timestamp_ini_1){
            $this->total_registros_bloco_u_ecf +=1;
            $this->write[] =  "|U030|01012016|31032016|T01|\n";
            $this->array_totais_registros_ecf['U030'] +=1;
        }
            
        //2o trimestre
        if($timestamp_fim >= $timestamp_ini_2){
            $this->total_registros_bloco_u_ecf +=1;
            $this->write[] =  "|U030|01042016|30062016|T02|\n";
            $this->array_totais_registros_ecf['U030'] +=1;
        }
            
        //3o trimestre
        if($timestamp_fim >= $timestamp_ini_3){
            $this->total_registros_bloco_u_ecf +=1;
            $this->write[] =  "|U030|01072016|30092016|T03|\n";
            $this->array_totais_registros_ecf['U030'] +=1;
        }
            
        //4o trimestre
        if($timestamp_fim >= $timestamp_ini_4)   {
            $this->total_registros_bloco_u_ecf +=1;
            $this->write[] =  "|U030|01102016|31122016|T04|\n";
            $this->array_totais_registros_ecf['U030'] +=1;
        } 
    }
    
}

//Balanço Patrimonial
public function bal_patrimon(){
    
    $this->total_registros_bloco_u_ecf +=1;
    $this->write[] =  "|U100|2.03.02.04.01|SUPERÁVIT/DÉFICIT ACUMULADO|A|5|03|2.03.02.04|10000,00|C|20000,00|C|\n";
    $this->array_totais_registros_ecf['U100'] =1;
}

//Demonstração do Resultado
public function dem_results($dados){
    
    
    $data_inicio  = $dados['dta_ini_ecf'];
    $data_fim  = $dados['dta_fin_ecf'];
    $objClassificador   = new c_classificacaoClass();   
    $prj_arr = explode(",", $this->projetos);
    unset($arrayClassificacao);
    
    foreach($prj_arr as $id_projeto){
        $arrayProjeto = $objClassificador->balancete($id_projeto, $data_inicio, $data_fim , true);
        foreach($arrayProjeto as $indice => $value){
            
            $arrayClassificacao[$indice]['id_conta'] = $value['id_conta'];
            $arrayClassificacao[$indice]['classificador'] = $value['classificador'];
            $arrayClassificacao[$indice]['acesso'] = $value['acesso'];
            $arrayClassificacao[$indice]['descricao'] = $value['descricao'];
            $arrayClassificacao[$indice]['analitica_sintetica'] = $value['analitica_sintetica'];
            $arrayClassificacao[$indice]['natureza'] = $value['natureza'];
            $arrayClassificacao[$indice]['credora'] += $value['credora'];
            $arrayClassificacao[$indice]['devedora'] += $value['devedora'];
            $arrayClassificacao[$indice]['saldoAnterior'] += $value['saldoAnterior'];
            $arrayClassificacao[$indice]['saldoAtual'] += $value['saldoAtual'];                        
        }
    }
    
    
    foreach($arrayClassificacao as $value){
        
        $prim_num = substr($value['classificador'], 0, 1);
        //lançar somente as sintéticas ATIVO OU PASSIVO
        if($prim_num >2){
            //verifica a natureza e atribui credor/devedor
            if($value['natureza'] == 1){

                $value['ind_dc_ini'] = "D";
                $value['ind_dc_fim'] = "D";

            }else{
                $value['ind_dc_ini'] = "R";
                $value['ind_dc_fim'] = "R";                
            }

            //verifica se valor é negativo e inverte C/D
            if($value['saldoAnterior'] < 0){
                $value['saldoAnterior'] = $value['saldoAnterior'] * (-1);
                $value['ind_dc_ini'] = ($value['ind_dc_ini'] == "D") ? "R" : "D"; 
            }

            if($value['saldoAtual'] < 0){
                $value['saldoAtual'] = $value['saldoAtual'] * (-1);
                $value['ind_dc_fim'] = ($value['ind_dc_fim'] == "D") ? "R" : "D";
            }

            
            $contador = substr_count($value['classificador'], ".");
            $nivel = $contador+1;

            if($nivel > 1){            
                $conta_superior = substr($value['classificador'], 0 , -3);
            }else{
                $conta_superior = "";
            }

            $value['saldoAtual']    = number_format($value['saldoAtual'], 2, ",", "");
            $value['saldoAnterior']    = number_format($value['saldoAnterior'], 2, ",", "");

            if(is_null($value['saldoAtual'])){
                $value['saldoAtual'] = "0,00";
            }

            if(is_null($value['saldoAnterior'])){
                $value['saldoAnterior'] = "0,00";
            }
            
            if($value['saldoAnterior'] != "0,00" || $value['saldoAtual'] != "0,00"){
               
                // REGISTRO ECF
                $this->total_registros_bloco_u_ecf +=1;
                $this->write[] =  "|U150|{$value['classificador']}|{$value['descricao']}|{$value['analitica_sintetica']}|{$nivel}|04|{$conta_superior}|{$value['saldoAtual']}|{$value['ind_dc_fim']}|\n";
                $this->array_totais_registros_ecf['U150'] =1;
            }
        }
    }
    
    
    
    
    
}

//Cálculo do IRPJ das Empresas Imunes ou Isentas
public function calc_irpj_im_is(){
    $this->total_registros_bloco_u_ecf +=1;
    $this->write[] =  "|U180|12|IMPOSTO DE RENDA A PAGAR|10000,00|\n";
    $this->array_totais_registros_ecf['U180'] =1;
}

//Cálculo da CSLL das Empresas Imunes ou Isentas
public function csll_im_is(){
    $this->total_registros_bloco_u_ecf +=1;
    $this->write[] =  "|U182|1|Base de Cálculo da CSLL|10000,00|\n";
    $this->array_totais_registros_ecf['U182'] =1;
}

//Encerramento do Bloco U
public function enc_bloco_u(){
    $this->total_registros_bloco_u_ecf +=1;
    $this->write[] =  "|U990|{$this->total_registros_bloco_u_ecf }|\n";
    $this->array_totais_registros_ecf['U990'] =1;
}

//Abertura do Bloco W - Declaração País a País
public function abertura_bloco_w(){
    $this->total_registros_bloco_w_ecf +=1;
    $this->write[] =  "|W001|1|\n";
    $this->array_totais_registros_ecf['W001'] =1;
}

//Informações Sobre o Grupo Multinacional e a Entidade
//Declarante
public function inf_grupo_mult(){
    $this->total_registros_bloco_w_ecf +=1;
    $this->write[] =  "|W100|MULTINACIONAL A|S|CONTROLADORA FINAL|105|11111111000191|2|||||01012016|31122016|11|PT|\n";
    $this->array_totais_registros_ecf['W100'] =1;
    
}

//Declaração País a País
public function dec_pais_pais(){
    $this->total_registros_bloco_w_ecf +=1;
    $this->write[] =  "|W200|21||100000000||200000000||300000000||30000000||4500000||3000000||100000000||25000000||50000000|2000|\n";
    $this->array_totais_registros_ecf['W200'] =1;
    
}

//Declaração País a País - Entidades Integrantes
public function dec_p_p_ent_int(){
    $this->total_registros_bloco_w_ecf +=1;
    fwrite($this->arquivo, "|W250|23|ENTIDADE INTEGRANTE 1|12345678|25||||OECD303|RUA ALFA 121  PERDIZES  SÃO PAULO/SP  CEP: 20.000-000|551133334444|EMAIL@EMAIL.COM|S|N|N|N|N|N|N|N|N|N|N|N|N|||\n");
    $this->array_totais_registros_ecf['W250'] =1;
    
}

//Observações Adicionais
public function obs_adicionais(){
    $this->total_registros_bloco_w_ecf +=1;
    $this->write[] =  "|W300|105|S|N|N|N|N|N|N|N|N|N|OBSERVAÇÕES|W300FIM|\n";
    $this->array_totais_registros_ecf['W300'] =1;
}

//Encerramento do Bloco W
public function enc_bloco_w(){
    $this->total_registros_bloco_w_ecf +=1;
    $this->array_totais_registros_ecf['W990'] =1;
    $this->write[] =  "|W990|{$this->total_registros_bloco_w_ecf}|\n";
    
}

//Abertura do Bloco X ? Informações Econômicas
public function abertura_bloco_x(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X001|1|\n";
    $this->array_totais_registros_ecf['X001'] =1;
}

//Atividades Incentivadas - PJ em Geral
public function ativ_incent(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X280|01|08|11111111112014|01012014|31122016|\n";
    $this->array_totais_registros_ecf['X280'] =1;
}

//Operações com o Exterior - Pessoa
public function op_ext_pessoa(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X291|11|OUTRAS INFORMAÇÕES||\n";
    $this->array_totais_registros_ecf['X291'] =1;
}

//Vinculada/Interposta/País com Tributação Favorecida
public function vinc_int_pais_tf(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X292|11|OUTRAS INFORMAÇÕES||\n";
    $this->array_totais_registros_ecf['X292'] =1;
}

//Operações com o Exterior - Pessoa Não Vinculada/ Não
//Interposta/País sem Tributação Favorecida
public function op_ext_p_nao_vinc(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X292|11|OUTRAS INFORMAÇÕES||\n";
    $this->array_totais_registros_ecf['X292'] =1;
}

//Operações com o Exterior - Exportações (Entradas de
//Divisas)
public function op_ext_exports(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X300|01|01|BEM DO IMOBILIZADO|100000,00|11111111|100|15|N|PRL20|90000,00|90000,00|1000,00|0,00|0,00|0,00|||\n";
    $this->array_totais_registros_ecf['X300'] =1;
    
}

//Operações com o Exterior - Contratantes das Exportações
public function op_ext_contrat_exp(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X310|PESSOA JURIDICA CONTRATANTE|249|1000000,00|01|\n";
    $this->array_totais_registros_ecf['X310'] =1;
}

//Operações com o Exterior - Importações (Saídas de Divisas)
public function op_ext_import(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X320|01|01|BEM DO IMOBILIZADO|100000,00|11111111|100|15|PRL20|90000,00|90000,00|1000,00|0,00|0,00|0,00|||\n";
    $this->array_totais_registros_ecf['X320'] =1;
    
}

//Operações com o Exterior - Contratantes das Importações
public function op_ext_contrat_import(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X330|PESSOA JURIDICA CONTRATANTE|249|1000000,00|01|\n";
    $this->array_totais_registros_ecf['X330'] =1;
}

//Identificação da Participação no Exterior
public function id_part_ext(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X340|EMPRESA CONTROLADA|1111111111|1|249|N|S||\n";
    $this->array_totais_registros_ecf['X340'] =1;
}

//Participações no Exterior - Resultado do Período de
//Apuração
public function parts_ext_res_per_ap(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X350|1000000,00|600000,00|400000,00|100000,00|0,00|40000,00|10000,00|450000,00|50000,00|0,00|0,00|500000,00|100000,00|400000,00|\n";
    $this->array_totais_registros_ecf['X350'] =1;
    
}

//Demonstrativo de Resultados e de Imposto a Pagar no
//Exterior
public function demons_res_imp_pag_ext(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X351|100000,00|250000,00|10000,00|90000,00|235000,00|50000,00|125000,00|20000,00|50000,00|\n";
    $this->array_totais_registros_ecf['X351'] =1;
    
}

//Demonstrativo de Resultados no Exterior Auferidos por
//Intermédio de Coligadas em Regime de Caixa
public function dem_res_ext_auf_int_col_reg_caixa(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X352|-100000,00|-250000,00|0,00|0,00|\n";
    $this->array_totais_registros_ecf['X352'] =1;
}

//Demonstrativo de Consolidação
public function dem_consolida(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X353|100000,00|250000,00|50000,00|125000,00|\n";
    $this->array_totais_registros_ecf['X353'] =1;
}

//Demonstrativo de Prejuízos Acumulados
public function dem_preju_acumulados(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X354|100000,00|250000,00|50000,00|\n";
    $this->array_totais_registros_ecf['X354'] =1;
}

//Demonstrativo de Rendas Ativas e Passivas
public function dem_rendas_at_pas(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X355|100000,00|250000,00|1000000,00|2500000,00|900000,00|2250000,00|90,00|\n";
    $this->array_totais_registros_ecf['X355'] =1;
}

//Demonstrativo de Estrutura Societária
public function dem_estr_societ(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X356|50,00|1000000,00|200000,00|\n";
    $this->array_totais_registros_ecf['X356'] =1;
}

//Origem e Aplicação de Recursos - Imunes ou Isentas
public function origem_aplcs_recursos_im_is(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X390|1|ORIGEM DE RECURSOS||\n";
    $this->array_totais_registros_ecf['X390'] =1;
}

//Comércio Eletrônico e Tecnologia da Informação ?
//Informações das Vendas
public function com_elet_tec_inf_vend(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X400|23|TRANSAÇÕES COM ÓRGÃOS DA ADMINISTRAÇÃO PÚBLICA||\n";
    $this->array_totais_registros_ecf['X400'] =1;
}

//Comércio Eletrônico ? Informação de Homepage/Servidor
public function com_elet_inf_server(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X410|105|S|N|\n";
    $this->array_totais_registros_ecf['X410'] =1;
}

//Royalties Recebidos ou Pagos a Beneficiários do Brasil e do
//Exterior
public function royalties_rec_pag_ext(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X420|R|105|50000,00|10000,00|5000,00|6000,00|7000,00|8000,00|9000,00|\n";
    $this->array_totais_registros_ecf['X420'] =1;
}

//Rendimentos Relativos a Serviços, Juros e Dividendos
//Recebidos do Brasil e do Exterior
public function rend_rel_serv_juros_div(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X430|105|50000,00|10000,00|5000,00|6000,00|7000,00|8000,00|\n";
    $this->array_totais_registros_ecf['X430'] =1;
}

//Pagamentos/Remessas Relativos a Serviços, Juros e
//Dividendos Recebidos do Brasil e do Exterior
public function pag_rem_rel_serv_juros(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X450|105|50000,00|10000,00|5000,00|6000,00|7000,00|8000,00|9000,00|10000,00|\n";
    $this->array_totais_registros_ecf['X450'] =1;
}

//Inovação Tecnológica e Desenvolvimento Tecnológico
public function inov_tec_des_tec(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X460|17|PROGRAMA DE DESENVOLVIMENTO TECNOLÓGICO INDUSTRIAL E AGROPECUÁRIO (PDTI / PDTA - LEI Nº 8.661/1993)||\n";
    $this->array_totais_registros_ecf['X460'] =1;
}
//Capacitação de Informática e Inclusão Digital
public function cap_info_incl_dig(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X470|12|PROGRAMA DE INCLUSÃO DIGITAL - LEI Nº 11.196/2005||\n";
    $this->array_totais_registros_ecf['X470'] =1;
}

//Repes, Recap, Padis, PATVD, Reidi, Repenec, Reicomp,
//Retaero, Recine, Resíduos Sólidos, Recopa, Copa do
//Mundo, Retid, REPNBL-Redes, Reif e Olimpíadas
public function repes_recap_padis(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X480|161|RECEITAS||\n";
    $this->array_totais_registros_ecf['X480'] =1;
}

//Pólo Industrial de Manaus e Amazônia Ocidental
public function polo_ind_man_am(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X490|1|PÓLO INDUSTRIAL DE MANAUS (ZFM)||\n";
    $this->array_totais_registros_ecf['X490'] =1;
}

//Zonas de Processamento de Exportação (ZPE)
public function zon_process_exp(){
    $this->total_registros_bloco_x_ecf +=1;    
    $this->write[] =  "|X500|1|ZPE (LEI Nº 11.508/2007, ALTERADA PELA LEI Nº 11.732/2008)||\n";
    $this->array_totais_registros_ecf['X500'] =1;
}

//Áreas de Livre Comércio (ALC)
public function areas_liv_com(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->write[] =  "|X510|1|ALC (Lei nº 7.965/1989, Lei nº 8.210/1991, Lei nº 8.256/1991, alterada pela Lei nº 11.732/2008, Lei nº 8.387/1991, 8.857/1994 e Decreto nº 517/1992)||\n";
    $this->array_totais_registros_ecf['X510'] =1;
    
}

//Encerramento do Bloco X
public function encerramento_bloco_x_ecf(){
    $this->total_registros_bloco_x_ecf +=1;
    $this->array_totais_registros_ecf['X990'] =1;
    $this->write[] =  "|X990|{$this->total_registros_bloco_x_ecf}|\n";
    
}

//Abertura do Bloco Y - Informações Gerais
public function abertura_bloco_y(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y001|1|\n";
    $this->array_totais_registros_ecf['Y001'] =1;
}

//Pagamentos/Recebimentos do Exterior ou de Não
//Residentes
public function pags_recbs_ext_nao_res(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y520|R|1|1|10500|100000,00|\n";
    $this->array_totais_registros_ecf['Y520'] =1;
}

//Discriminação da Receita de Vendas dos Estabelecimentos
//por Atividade Econômica
public function disc_receita_vend_est_at_ec(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y540|22222222222222||100000,00|4399101|\n";
    $this->array_totais_registros_ecf['Y540'] =1;
}

//Vendas a Comercial Exportadora com Fim Específico de Expor
public function vendas_com_exp_fim_esp(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y550|22222222222222|11111111|100000,00|\n";
    $this->array_totais_registros_ecf['Y550'] =1;
}

//Detalhamento das Exportações da Comercial Exportadora
public function det_exp_com_exp(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y560|22222222222222|11111111|100000,00|85000,00|\n";
    $this->array_totais_registros_ecf['Y560'] =1;
}

///Demonstrativo do Imposto de Renda e CSLL Retidos na Fonte
public function demons_ir_csll_rf(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y570|11111111000191|FONTE PAGADORA|S|5928|100000,00|1500,00|500,00|\n";
    $this->array_totais_registros_ecf['Y570'] =1;
}

//Doações a Campanhas Eleitorais
public function doacoes_camp_el(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y580|1111111100191|03|02|100000,00|\n";
    $this->array_totais_registros_ecf['Y580'] =1;
}

//Ativos no Exterior
public function ativ_exterior(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y590|0331|249|10.000 AÇÕES DA COMPANHIA ABC ADQUIRIDAS EM 20/02/2014 POR MEIO DA BOLSA DE VALORES DE NOVA YORK POR U$ 100.000,00|0|300000,00|\n";
    $this->array_totais_registros_ecf['Y590'] =1;
    
}

//Identificação e Remuneração de Sócios, Titulares, Dirigentes
//e Conselheiro
public function id_rem_socios_tit(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y600|01012012|105|PF|00000000000|FULANO SÓCIO|01|60,00|60,00|||100000,00|10000,00|5000,00|3000,00|9000,00|\n";
    $this->array_totais_registros_ecf['Y600'] =1;
    
}

//Identificação e Rendimentos de Dirigentes e Conselheiros -
//Imunes ou Isentas
public function id_rend_dir_cons(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y612|00000000000|FULANO DIRIGENTE|12|50000,00|10000,00|8000,00|\n";
    $this->array_totais_registros_ecf['Y612'] =1;
}

//Participações Avaliadas pelo Método de Equivalência
//Patrimonial
public function part_av_met_equiv(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y620|01012014|1|105|44444444000191|EMPRESA COLIGADA NO BRASIL|1000000,00|300000,00|25,00|30,00|-100000,00|31102013|N|||N||\n";
    $this->array_totais_registros_ecf['Y620'] =1;
    
}

//Fundos/Clubes de Investimento
public function fund_inv(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y630|44444444000191|100|5000000|100000000,00|10012010||\n";
    $this->array_totais_registros_ecf['Y630'] =1;
}

//Participações em Consórcios de Empresas
public function parts_cons_empre(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y640|44444444000191|1|500000,00|22222222000191|400000,00|\n";
    $this->array_totais_registros_ecf['Y640'] =1;
}
//Participantes do Consórcio
public function parts_cons(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y650|11111111000191|100000,00|\n";
    $this->array_totais_registros_ecf['Y650'] =1;
}

//Dados de Sucessoras
public function dados_sucess(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y660|11111111000191|EMPRESA SUCESSORA 1 LTDA|40,00|\n";
    $this->array_totais_registros_ecf['Y660'] =1;
}

//Outras Informações
public function outras_info(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y671|10000,00|20000,00|10000,00|200000,00|5000,00|6000,00|6000,00|20000,00|25000,00|10000,00|10,00|2|2|\n";
    $this->array_totais_registros_ecf['Y671'] =1;
    
}

//Outras Informações (Lucro Presumido ou Lucro Arbitrado)
public function outras_info_lpla(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y672|10000,00|20000,00|1000,00|2000,00|5000,00|6000,00|1000,00|2000,00|1000,00|2000,00|1000,00|2000,00|1000,00|2000,00|10000,00|100000,00|10000,00|10,00|2|2|\n";
    $this->array_totais_registros_ecf['Y672'] =1;
    
}

//Mês das Informações de Optantes pelo Refis (Lucro Real,
//Presumido e Arbitrado)
public function mes_info_opt_refis(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y680|01|\n";
    $this->array_totais_registros_ecf['Y680'] =1;
}
//nformações de Optantes pelo Refis (Lucro Real, Presumido
//e Arbitrado)
public function info_opt_refis_lr_pa(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y681|1| Receita da Venda de Produtos de Fabricação Própria|100000,00|\n";
    $this->array_totais_registros_ecf['Y681'] =1;
}

//Informações de Optantes pelo Refis - Imunes ou Isentas
public function info_opt_refis_im_is(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y682|01|100000,00|\n";
    $this->array_totais_registros_ecf['Y682'] =1;
}

//Informações de Optantes pelo Paes
public function info_opt_paes(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y690|01|100000,00|\n";
    $this->array_totais_registros_ecf['Y690'] =1;
}

//Informações de Períodos Anteriores
public function info_perio_ante(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y720|100000,00|31122013|100000000,00|\n";
    $this->array_totais_registros_ecf['Y720'] =1;
}
//Outras Informações
public function outras_infors(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->write[] =  "|Y800|001|Memória de Cálculo ? Incoportação|1234567890ABCDEFABCDEFABCDEFAB1234567890|{\rtf1\ansi\ansicpg1252\uc1...|Y800FIM|\n";
    $this->array_totais_registros_ecf['Y800'] =1;
    
}
//Encerramento do Bloco Y
public function encerramento_bloco_y_ecf(){
    $this->total_registros_bloco_y_ecf +=1;
    $this->array_totais_registros_ecf['Y990'] =1;
    $this->write[] =  "|Y990|{$this->total_registros_bloco_y_ecf}|\n";
    
}

//Abertura do Bloco 9
public function abertura_bloco_9_ecf(){
    $this->total_registros_bloco_9_ecf +=1;
    $this->write[] =  "|9001|1|\n";
    $this->array_totais_registros_ecf['9001'] =1;
}

//Avisos da Escrituração
public function avisos_escr(){
    
}

//Registros do Arquivo
public function regs_do_arq(){
    
    $this->array_totais_registros_ecf['9900'] = 1;
    $this->array_totais_registros_ecf['9990'] = 1;
    $this->array_totais_registros_ecf['9999'] = 1;
   
    
    foreach($this->array_totais_registros_ecf as $key => $value){
        $this->total_registros_bloco_9_ecf +=1;
        $this->write[] = "|9900|{$key}|{$value}|||\n";      
    }
    
    $this->total_registros_bloco_9_ecf +=1;    
}

//Encerramento do Bloco 9
public function enc_bloco_9_ecf(){
    $this->total_registros_bloco_9_ecf +=1;
    $this->write[] =  "|9990|{$this->total_registros_bloco_9_ecf}|\n";    
}

//Encerramento do Arquivo Digital
public function enc_arquivo_digital_ecf(){
    
    $this->write[] =  "|9999|SUBSTUTUIR_TOTAL_LINHAS|\n";  
}

public function query_plano_de_contas(){
    
    $query_pl_contas = "SELECT * FROM contabil_planodecontas where id_projeto IN({$this->projetos}, 0) and status= 1 order by classificador";
    $res = mysql_query($query_pl_contas);
    
    while($row = mysql_fetch_assoc($res)){
        $dados_plano_de_contas[$row['classificador']] = $row;        
    }
    return $dados_plano_de_contas;
}


public function geraRelatorio($dados){
    $data_inicio  = $dados['dta_ini'];
    $data_fim  = $dados['dta_fin'];
    $objClassificador   = new c_classificacaoClass();   
    $prj_arr = explode(",", $this->projetos);
    unset($arrayClassificacao);
    
    foreach($prj_arr as $id_projeto){        
        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo "<br>";
        
        echo " -------- Projeto:  $id_projeto ----------";
        echo "<br>";
        $arrayProjeto = $objClassificador->balancete($id_projeto, $data_inicio, $data_fim , true);

        foreach($arrayProjeto as $indice => $value){
            
            if($value['saldoAnterior'] != "0,00"  || $value['saldoAtual'] != "0,00" ){
           
                echo "Classificador: {$value['id_conta']}";
                echo "<br>";
                echo "Analitica Sintetica: {$value['analitica_sintetica']}";
                echo "<br>";
                echo "Credora: {$value['credora']}";
                echo "<br>";
                echo "Devedora: {$value['devedora']}";
                echo "<br>";
                echo "Saldo Anterior: {$value['saldoAnterior']}";
                echo "<br>";
                echo "Saldo Atual: {$value['saldoAtual']}";
                echo "<br>";
                echo "...........";       
                echo "<br>";
                echo "<br>";
            }
            
            $arrayClassificacao[$indice]['id_conta'] = $value['id_conta'];
            $arrayClassificacao[$indice]['classificador'] = $value['classificador'];
            $arrayClassificacao[$indice]['acesso'] = $value['acesso'];
            $arrayClassificacao[$indice]['descricao'] = $value['descricao'];
            $arrayClassificacao[$indice]['analitica_sintetica'] = $value['analitica_sintetica'];
            $arrayClassificacao[$indice]['natureza'] = $value['natureza'];
            $arrayClassificacao[$indice]['credora'] += $value['credora'];
            $arrayClassificacao[$indice]['devedora'] += $value['devedora'];
            $arrayClassificacao[$indice]['saldoAnterior'] += $value['saldoAnterior'];
            $arrayClassificacao[$indice]['saldoAtual'] += $value['saldoAtual'];                        
        }
        
    }
    echo ":::::::::::::::::::::::::: TOTAIS ::::::::::::::::::::::::::::: ";
    echo '<pre>' . var_export($arrayClassificacao, true) . '</pre>';
     
}


function RemoveCaracteres($variavel) {	
	$variavel = str_replace("  ", " ", $variavel);
	$variavel = str_replace("(", "", $variavel);
	$variavel = str_replace(")", "", $variavel);
	$variavel = str_replace("-", "", $variavel);
	$variavel = str_replace("/", "", $variavel);
	$variavel = str_replace(":", "", $variavel);
	$variavel = str_replace(",", " ", $variavel);
	$variavel = str_replace(".", "", $variavel);
	$variavel = str_replace(";", "", $variavel);
	$variavel = str_replace("\"", "", $variavel);
	$variavel = str_replace("\'", "", $variavel);
        $variavel = str_replace(".", "", $variavel);
	return $variavel;
    }
} //fim class
?>


