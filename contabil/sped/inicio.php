<?php 
    require_once('MontaArquivo2.class.php');
    include_once("../../classes/LogClass.php");
    
    $log = new Log();
    
    //Get contadores
    $sqlContadores = "SELECT * FROM contabil_contador";
    $res = mysql_query($sqlContadores);
    
    while ($row = mysql_fetch_assoc($res)) {
        $array_contadores[] = $row;
    }
    
    if (isset($_REQUEST['gerar_ECD']) || isset($_REQUEST['gerar_ECF'])){ 
        $tipo="";
        
        if(isset($_REQUEST['gerar_ECD'])){
            
            $log->log('2', "Geração do Arquivo ECD", '');
            
            $dados['dta_ini']                           = $_REQUEST['dta_ini'];
            $dados['dta_fin']                           = $_REQUEST['dta_fin'];
            $dados['empresa']                           = $_REQUEST['empresa'];
            $dados['tipo_escrituracao']                 = "G";
            $dados['empresa_de']                        = $_REQUEST['empresa_de'];
            $dados['indicador_situacao_especial']       = $_REQUEST['indicador_situacao_especial'];
            $dados['indicador_situacao_inicio_periodo'] = $_REQUEST['indicador_situacao_inicio_periodo'];
            $dados['J935']                              = $_REQUEST['J935'];
            $dados['cod_scp']                           = $_REQUEST['cod_scp'];
            $dados['desc']                              = $_REQUEST['desc'];
            $dados['ind_desc']                          = $_REQUEST['ind_desc'];            
            $dados['nome_auditor']                      = $_REQUEST['nome_auditor'];
            $dados['abr_demonstracao']                  = $_REQUEST['abr_demonstracao'];
            $dados['id_scp_ecd']                        = $_REQUEST['id_scp_ecd'];
            $dados['nome_scp']                          = $_REQUEST['nome_scp'];
            $dados['nat_livro']                         = $_REQUEST['nat_livro'];
            $dados['est_dem_result']                    = $_REQUEST['est_dem_result'];
            $dados['ident_demonstracoes']               = $_REQUEST['ident_demonstracoes'];
            $dados['cab_dem']                           = $_REQUEST['cab_dem'];
            $dados['dt_arq_atos']                       = $_REQUEST['dt_arq_atos'];
            $dados['dt_ato_conv']                       = $_REQUEST['dt_ato_conv'];
            $dados['qual_cont']                         = $_REQUEST['qual_cont'];
            $dados['qual_responsavel']                  = $_REQUEST['qual_responsavel'];
            $dados['reg_auditor']                       = $_REQUEST['reg_auditor'];
            $dados['indicador_finalidade']              = $_REQUEST['indicador_finalidade'];
            $dados['ger_dem']                           = $_REQUEST['ger_dem'];
            $dados['hash']                              = $_REQUEST['hash'];
            $dados['nire']                              = $_REQUEST['nire'];
            $dados['id_moeda_funcional']                = $_REQUEST['id_moeda_funcional'];
            $dados['esc_con_cons']                      = $_REQUEST['esc_con_cons'];
            $dados['existe_nire']                       = $_REQUEST['existe_nire'];
            $dados['cod_ent_ref']                       = $_REQUEST['cod_ent_ref'];
            $dados['cod_inscr']                         = $_REQUEST['cod_inscr'];
            $dados['num_ord']                           = $_REQUEST['num_ord'];            
            $dados['registro_auditor']                  = $_REQUEST['registro_auditor'];
            $dados['id_scp_ecd2']                       = $_REQUEST['id_scp_ecd2'];
            $dados['reg_cod']                           = $_REQUEST['reg_cod'];
            $dados['nome_campo']                        = $_REQUEST['nome_campo'];
            $dados['desc_campo']                        = $_REQUEST['desc_campo'];
            $dados['ind_tipo_dado']                     = $_REQUEST['ind_tipo_dado'];
            $dados['sig_esc']                           = $_REQUEST['sig_esc'];  
            $dados['projetos_teste']                    = $_REQUEST['projetos_teste']; 
            
            
            
            $nomeFile ="Sped_ECD.rtf";
            $tipo = "ECD";
        }

        if(isset($_REQUEST['gerar_ECF'])){

            $log->log('2', "Geração do Arquivo ECF", '');
            
            $dados['dta_ini_ecf']                           = $_REQUEST['dta_ini_ecf'];
            $dados['dta_fin_ecf']                           = $_REQUEST['dta_fin_ecf'];
            $dados['cnpj_ecf']                              = $_REQUEST['cnpj_ecf'];
            $dados['nome_empresarial_ecf']                  = $_REQUEST['nome_empresarial_ecf'];
            $dados['indicador_situacao_inicio_periodo_ecf'] = $_REQUEST['indicador_situacao_inicio_periodo_ecf'];
            $dados['indicador_situacao_especial_ecf']       = $_REQUEST['indicador_situacao_especial_ecf'];
            $dados['patr_remanesc_cisao_ecf']               = $_REQUEST['patr_remanesc_cisao_ecf'];
            $dados['dta_esp']                               = $_REQUEST['dta_esp'];
            $dados['esc_retificadora_ecf']                  = $_REQUEST['esc_retificadora_ecf'];
            $dados['num_recibo_anterior_ecf']               = $_REQUEST['num_recibo_anterior_ecf'];
            $dados['tipo_ecf']                              = $_REQUEST['tipo_ecf'];
            $dados['id_scp']                                = $_REQUEST['id_scp'];
            $dados['opt_refis']                             = $_REQUEST['opt_refis'];
            $dados['opt_paes']                              = $_REQUEST['opt_paes'];
            $dados['forma_trib_lucro']                      = $_REQUEST['forma_trib_lucro'];
            $dados['per_apuracao_irpj']                     = $_REQUEST['per_apuracao_irpj'];
            $dados['qual_pessoa_juridica']                  = $_REQUEST['qual_pessoa_juridica'];
            $dados['1o_trim']                               = $_REQUEST['1o_trim'];
            $dados['2o_trim']                               = $_REQUEST['2o_trim'];
            $dados['3o_trim']                               = $_REQUEST['3o_trim'];
            $dados['4o_trim']                               = $_REQUEST['4o_trim'];
            $dados['tipo_escrituracao_ecf']                 = $_REQUEST['tipo_escrituracao_ecf'];
            $dados['tipo_ent_imune_isenta']                 = $_REQUEST['tipo_ent_imune_isenta'];
            $dados['exist_ativ_trib']                       = $_REQUEST['exist_ativ_trib'];
            $dados['apuracao_csll']                         = $_REQUEST['apuracao_csll'];
            $dados['opt_lei']                               = $_REQUEST['opt_lei'];
            $dados['dif_cont_fcont']                        = $_REQUEST['dif_cont_fcont'];
            $dados['est_janeiro']                           = $_REQUEST['est_janeiro'];
            $dados['est_fevereiro']                         = $_REQUEST['est_fevereiro'];
            $dados['est_marco']                             = $_REQUEST['est_marco'];
            $dados['est_abril']                             = $_REQUEST['est_abril'];
            $dados['est_maio']                              = $_REQUEST['est_maio'];
            $dados['est_junho']                             = $_REQUEST['est_junho'];
            $dados['est_julho']                             = $_REQUEST['est_julho'];
            $dados['est_agosto']                            = $_REQUEST['est_agosto'];
            $dados['est_setembro']                          = $_REQUEST['est_setembro'];
            $dados['est_outubro']                           = $_REQUEST['est_outubro'];
            $dados['est_novembro']                          = $_REQUEST['est_novembro'];
            $dados['est_dezembro']                          = $_REQUEST['est_dezembro'];
            $dados['aliq_csll']                             = $_REQUEST['aliq_csll'];
            $dados['qtd_scp_pj']                            = $_REQUEST['qtd_scp_pj'];
            $dados['adm_clubes']                            = $_REQUEST['adm_clubes'];
            $dados['part_consorcios']                       = $_REQUEST['part_consorcios'];
            $dados['operacoes_exterior']                    = $_REQUEST['operacoes_exterior'];
            $dados['doacoes_eleitorais']                    = $_REQUEST['doacoes_eleitorais'];
            $dados['operacoes_vinculada']                   = $_REQUEST['operacoes_vinculada'];
            $dados['pj_artigos48_49']                       = $_REQUEST['pj_artigos48_49'];
            $dados['participacoes_exterior']                = $_REQUEST['participacoes_exterior'];
            $dados['ativ_rural']                            = $_REQUEST['ativ_rural'];
            $dados['lucro_exploracao']                      = $_REQUEST['lucro_exploracao'];
            $dados['isencao_red_lucro_presumido']           = $_REQUEST['isencao_red_lucro_presumido'];
            $dados['finor_finam']                           = $_REQUEST['finor_finam'];
            $dados['part_permanente_coligadas_controladas'] = $_REQUEST['part_permanente_coligadas_controladas'];
            $dados['vendas_exp']                            = $_REQUEST['vendas_exp'];
            $dados['rec_exterior']                          = $_REQUEST['rec_exterior'];
            $dados['ativos_exterior']                       = $_REQUEST['ativos_exterior'];
            $dados['pj_comercial_exportadora']              = $_REQUEST['pj_comercial_exportadora'];
            $dados['pags_ao_exterior']                      = $_REQUEST['pags_ao_exterior'];
            $dados['comercio_eletronico']                   = $_REQUEST['comercio_eletronico'];
            $dados['royalties_brasil_exterior']             = $_REQUEST['royalties_brasil_exterior'];
            $dados['royalties_pagos']                       = $_REQUEST['royalties_pagos'];
            $dados['rendimentos_sjd']                       = $_REQUEST['rendimentos_sjd'];
            $dados['pagamentos_remessas']                   = $_REQUEST['pagamentos_remessas'];
            $dados['inovacao_tec']                          = $_REQUEST['inovacao_tec'];
            $dados['cap_info']                              = $_REQUEST['cap_info'];
            $dados['repes_recap_etc']                       = $_REQUEST['repes_recap_etc'];
            $dados['polo_manaus']                           = $_REQUEST['polo_manaus'];
            $dados['zon_processamento_exp']                 = $_REQUEST['zon_processamento_exp'];
            $dados['areas_livre_comercio']                  = $_REQUEST['areas_livre_comercio'];
            $dados['cnpj_da_scp']                           = $_REQUEST['cnpj_da_scp'];
            $dados['nome_da_scp']                           = $_REQUEST['nome_da_scp'];
            $dados['ind_rec_receita']                       = $_REQUEST['ind_rec_receita'];
            $dados['ind_pais_a_pais']                       = $_REQUEST['ind_pais_a_pais'];
            $dados['sig_esc_ecf']                           = $_REQUEST['sig_esc_ecf'];
            $dados['qualif_assinante']                      = $_REQUEST['qualif_assinante'];
            
            $dados['repes']            = $_REQUEST['repes'];
            $dados['recap']            = $_REQUEST['recap'];
            $dados['padis']            = $_REQUEST['padis'];
            $dados['patvd']            = $_REQUEST['patvd'];
            $dados['reidi']            = $_REQUEST['reidi'];
            $dados['repenec']          = $_REQUEST['repenec'];
            $dados['recine']           = $_REQUEST['recine'];
            $dados['res_solidos']      = $_REQUEST['res_solidos'];
            $dados['recopa']           = $_REQUEST['recopa'];
            $dados['copa_do_mundo']    = $_REQUEST['copa_do_mundo'];
            $dados['retid']            = $_REQUEST['retid'];
            $dados['repnbl']           = $_REQUEST['repnbl'];
            $dados['reif']             = $_REQUEST['reif'];
            $dados['olimpiadas']       = $_REQUEST['olimpiadas'];
            $dados['reicomp']          = $_REQUEST['reicomp'];
            $dados['retaero']          = $_REQUEST['retaero'];

        $nomeFile ="Sped_ECF.rtf";
            $tipo = "ECF";
        }        

        $arquivo = fopen("arquivos/" . $nomeFile, "w+");

        /* 
        _____
        montar conteudo do arquivo aqui
        -----
        */
        $sped = new MontaArquivo2($arquivo, $tipo, $dados);
        /* 
        _____
        fim conteudo do arquivo
        -----
        */
        fclose($arquivo);

        //modal de download
        $html = '<div class="modal fade" id="modalDownload" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Fazer Download do Arquivo </h4>
                            </div>
                            <div class="modal-body"> 
                                 O arquivo foi gerado e os dados podem ser modificados através do próprio software <i>Sped Contábil</i> após a importação.                  
                            </div>
                            <div class="modal-footer"> 
                                <a href="arquivos/download.php?file='.$nomeFile.'" target="_blank"><button type="button" class="btn btn-primary"> <i class="fa fa-download" aria-hidden="true"></i> Download</button></a>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->';
    }
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" conent="width=device-width, initial-scale=1">

        <title>:: Intranet :: Administrativo</title>

        <link rel="shortcut icon" href="../../favicon.png">

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
    </head>    
    <body>
        <?php echo $html; ?>        
          
        <style>
            .panel {
                min-height: 130px;
            }
            
            .row-checks{
                padding-left: 20px;
            }
            
            .tipos_de_programa{
                display:none;
            }
        </style>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row" style="padding-bottom:15px;">
                <div class="col-lg-12">
                    <div class="page-header box-contabil-header"><h2><?php echo $icon['38'] ?> - CONTABILIDADE <small>- Escrituração Contábil / Escrituração Fiscal</small></h2></div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#contabil" aria-controls="contabil" role="tab" data-toggle="tab">ECD</a></li>
                        <li role="presentation"><a href="#fiscal" aria-controls="fiscal" role="tab" data-toggle="tab">ECF</a></li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="contabil">
                            <form method="post" class="form-horizontal" id="form-sped-contabil-ecd">
                                
<!--                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Campo teste para projetos individuais</label>
                                        <input type="text" id="projetos_teste" name="projetos_teste" class="form-control">
                                    </div>
                                </div>-->
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="row">                                            
                                            <div class="col-md-5"> 
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div>
                                                            <div class="checkbox ttip_existe_nire">
                                                            <label><input type="checkbox" id="existe_nire" name="existe_nire">Indicador de existência de NIRE</label>
                                                            </div>                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>    
                                            <div class="col-md-7"> 
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div>
                                                            <label>NIRE</label>
                                                            <input type="text" id="nire" name="nire" class="form-control validate[required]" maxlength="11" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>   
                                        </div>
                                        
                                        
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-12 col-lg-offset-1">
                                                            <label for="" class="control-label">Exercício</label>
                                                            <div class="input-group">
                                                                <div class="input-group-addon control-label">Início</div>
                                                                <input type="text" id='dta_ini' name='dta_ini' class='text-sm text-center data  form-control' value="<?= $data_ini ?>" required>
                                                                <div class="input-group-addon control-label">Fim</div>
                                                                <input type="text" id='dta_fin' name='dta_fin' class='text-sm text-center data  form-control' value="<?= $data_fim ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-7"> 
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div>
                                                            <label>Razão Social</label>
                                                            <input type="text" id="empresa_id" name="empresa" class="form-control validate[required]" value="INSTITUTO DOS LAGOS RIO" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                            
                                        </div>
<!--                                        <div class="row"> 
                                            <div class="col-md-12">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-12 col-lg-offset-1">
                                                            <label for="tipo_escrituracao" class="control-label">Forma de Escrituração</label>
                                                            <div class="radio">
                                                                <label><input class="tipo_escrituracao" name="tipo_escrituracao" type="radio" value="G" checked="checked"><strong>G</strong> - Livro Diário (Completo sem escrituração auxiliar) ou Livro Auxiliar da SCP</label>
                                                            </div>
                                                            <div class="radio">    
                                                                <label><input class="tipo_escrituracao" name="tipo_escrituracao" type="radio" value="R"><strong>R</strong> - Livro Diário com Escrituração Resumida (com escrituração auxiliar)</label>
                                                            </div>
                                                            <div class="radio">
                                                                <label><input class="tipo_escrituracao" name="tipo_escrituracao" type="radio" value="A"><strong>A</strong> - Livro Diário Auxiliar ao Diário com Escrituração Resumida</label>
                                                            </div>
                                                            <div class="radio">
                                                                <label><input class="tipo_escrituracao" name="tipo_escrituracao" type="radio" value="B"><strong>B</strong> - Livro Balancetes Diários e Balanços</label>
                                                            </div>
                                                            <div class="radio">
                                                                <label><input class="tipo_escrituracao" name="tipo_escrituracao" type="radio" value="Z"><strong>Z</strong> - Razão Auxiliar <i class="text text-sm text-danger">(Livro Contábil Auxiliar conforme leiaute definido nos registros I500 a I555)</i></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>   
                                            </div>
                                        </div>-->
                                        <div class="row">    
                                            <div class="col-md-4 ">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-10 col-md-offset-1">
                                                            <label for="fe" class="control-label">Indicador de Situação Especial</label> 
                                                        <!--<div class="radio">
                                                                <label><input type="radio" id="indicador_situacao_especial" name="indicador_situacao_especial"><strong>0</strong> - Abertura </label>
                                                            </div>-->
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_especial" name="indicador_situacao_especial" value="1"><strong>1</strong> - Cisão</label>
                                                            </div>   
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_especial" name="indicador_situacao_especial" value="2"><strong>2</strong> - Fusão</label>
                                                            </div> 
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_especial" name="indicador_situacao_especial" value="3"><strong>3</strong> - Incorporação</label>
                                                            </div> 
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_especial" name="indicador_situacao_especial" value="4"><strong>4</strong> - Extinção</label>
                                                            </div> 
                                                           
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-8 "> 
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-10 col-md-offset-1">
                                                            <label for="fe" class="control-label">Indicador de Situação no Início do Período</label> 
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_inicio_periodo" name="indicador_situacao_inicio_periodo" value="0" checked="">Normal <i class="text text-danger text-sm">(Início no primeiro dia do ano)</i></label>
                                                            </div>
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_inicio_periodo" name="indicador_situacao_inicio_periodo" value="1">Abertura</label>
                                                            </div>   
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_inicio_periodo" name="indicador_situacao_inicio_periodo" value="2">Resultante de Cisão/Fusão ou remanescente de Cisão, ou realizou Incorporação</label>
                                                            </div> 
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_inicio_periodo" name="indicador_situacao_inicio_periodo" value="3">Início de obrigatoriedade na entrega da ECD no curso do ano calendário</label>
                                                            </div> 
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>   
                                        <div class="row">
                                            <div class="col-md-6 "> 
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-12 col-md-offset-1">
                                                            <label for="fe" class="control-label">Indicador de Entidade Sujeita a Auditoria Independente</label> 
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_auditoria" name="J935" value="0" checked="">Empresa não é entidade sujeita a auditoria independente </label>
                                                            </div>
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_auditoria" name="J935" value="1">Empresa é entidade sujeita a auditoria independente<i class="text text-sm text-danger"> (Ativo total superior a R$ 240.000.000,00 ou Receita Bruta Anual superior a R$300.000.000,00)</i></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 "> 
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-12 col-md-offset-1">
                                                            <label for="fe" class="control-label">Indicador do Tipo de ECD</label> 
                                                            <div class="radio">
                                                                <label><input type="radio" id="tipo_ecd" name="cod_scp" value="0" checked>ECD de empresa não participante de SCP como sócio ostensivo</label>
                                                            </div>
                                                            <div class="radio">
                                                                <label><input type="radio" id="tipo_ecd" name="cod_scp" value="1">ECD de empresa participante de SCP como sócio ostensivo </label>
                                                            </div>
                                                            <div class="radio">
                                                                <label><input type="radio" id="tipo_ecd" name="cod_scp" value="2" >ECD da SCP </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row camposJ935 text text-danger">
                                            <div class="col-md-8"> 
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div>
                                                            <label>Nome do Auditor</label>
                                                            <input type="text" id="nome_auditor" name="nome_auditor" class="form-control validate[required]" placeholder="CAMPO OBRIGATÓRIO">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4"> 
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div>
                                                            <label>Registro do Auditor na CVM</label>
                                                            <input type="text" id="registro_auditor" name="registro_auditor" class="form-control validate[required]" placeholder="CAMPO OBRIGATÓRIO">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row camposSCP text text-danger">
                                            <div class="col-md-5">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-12 col-md-offset-1">
                                                            <label>Identificação da SCP</label>
                                                            <input type="text" name="id_scp_ecd" id="id_scp_ecd" class="form-control text-center" maxlength="18" onkeypress="mascaraMutuario(this,cpfCnpj)" onblur="clearTimeout()" placeholder="(CNPJ) CAMPO OBRIGATÓRIO"> 
                                                            <i class="text text-sm text-danger">(Art 52 da Instrução Normativa RFB Nº 1.470, de 30 de maio de 2014)</i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-12 col-md-offset-1"> 
                                                            <label>Nome da SCP</label>
                                                            <input type="text" id="nome_scp" name="nome_scp" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row_scp2 row text text-danger">
                                            <div class="col-md-5">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-12 col-md-offset-1"> 
                                                            <label>Identificação da SCP</label>
                                                            <input type="text" name="id_scp_ecd2" id="id_scp_ecd2" class="validate[required] form-control text-center" maxlength="18" onkeypress="mascaraMutuario(this,cpfCnpj)" onblur="clearTimeout()" placeholder="(CNPJ) CAMPO OBRIGATÓRIO">
                                                         </div>
                                                    </div>
                                                </div>
                                            </div>                                            
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-12 col-md-offset-1">
                                                            <label for="num_ord" class="control-label">Número de Ordem do Instrumento de Escrituração</label>                                                            
                                                            <input type="number" class="form-control" id="num_ord" name="num_ord" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-12 col-md-offset-1">
                                                            <label for="fe" class="control-label">Natureza do Livro</label>                                                            
                                                            <input type="text" pattern="[a-zA-Z\s]+" class="form-control" id="nat_livro" name="nat_livro" placeholder="finalidade à que se destina o instrumento" title="Apenas Letras" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                                                              
                                        <div class="row">                                            
                                            <div class="col-md-4">
                                                <div class="panel panel-default">
                                                    <div class="panel-body dt_arq_atos_ttip">
                                                           <label for="dt_arq_atos">Data de Arquivamentos dos Atos Constitutivos :</label>
                                                          <input type="text" id='dt_arq_atos' name='dt_arq_atos' class='text-sm text-center data form-control'>                                                      
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="panel panel-default">
                                                    <div class="panel-body dt_ato_conv_ttip">
                                                           <label for="dt_ato_conv">Data de Arquivamento do Ato de Conversão de Sociedade Simples em Sociedade Empresária</label>
                                                           <input type="text" id='dt_ato_conv' name='dt_ato_conv' class='text-sm text-center data form-control'>                                                      
                                                    </div>
                                                </div>
                                            </div>
                                            
                                             <div class="col-md-4">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="cod_inscr">Código Cadastral Da Pessoa Jurídica na instituição do campo anterior</label>
                                                        <input type="text" class="form-control" name="cod_inscr" id="cod_inscr">                                                          
                                                      </div>
                                                    </div>
                                                </div>
                                            </div>
<!--                                            <div class="col-md-4">
                                                <div class="panel panel-default">
                                                    <div class="panel-body ">
                                                        <div>
                                                            <div class="checkbox id_moeda_funcional_ttarea">
                                                            <label><input type="checkbox" id="id_moeda_funcional" name="id_moeda_funcional">Identificação de Moeda Funcional</label>
                                                            </div>                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>-->
                                        </div>
                                        <div class="row camposI020">                                            
                                            <div class="col-md-3">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                           <label for="reg_cod">Código do Registro</label>
                                                          <input type="text" id='reg_cod' name='reg_cod' class='text-sm text-center validate[required] form-control'>                                                      
                                                    </div>                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                           <label for="nome_campo">Nome do Campo Adicional</label>
                                                           <input type="text" id='nome_campo' name='nome_campo' class='text-sm validate[required] text-center form-control'>                                                      
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                           <label for="desc_campo">Descrição do Campo Adicional</label>
                                                           <input type="text" id='desc_campo' name='desc_campo' class='text-sm text-center form-control'>                                                      
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="ind_tipo_dado">Indicação do Tipo de Dado</label>
                                                        <select class="form-control" name="ind_tipo_dado" id="ind_tipo_dado">
                                                          <option value="N">Numérico</option>    
                                                          <option value="C">Caractere</option>
                                                        </select>
                                                      </div>                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="row">                                            
                                            <div class="col-md-6">
                                                <div class="panel panel-default">
                                                    <div class="panel-body cod_ent_ref_ttip">
                                                        <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="cod_ent_ref">Instituição Responsável pela Administração do Cadastro</label>
                                                        <select class="form-control" name="cod_ent_ref" id="cod_ent_ref">
                                                          <option value="00">Nenhuma inscrição em outras entidades</option>    
                                                          <option value="01">Banco Central do Brasil</option>  
                                                          <option value="02">Superintendência de Seguros Privados (Susep)</option>  
                                                          <option value="03">Comissão de Valores Mobiliários (CVM)</option>  
                                                          <option value="04">Agência Nacional de Transportes Terrestres (ANTT)</option>  
                                                          <option value="05">Tribunal Superior Eleitoral (TSE)</option>  
                                                          <option value="RJ">Secretaria da Fazenda do Rio de Janeiro, ou equivalente</option>  
                                                        </select>
                                                      </div>                                                        
                                                    </div>
                                                </div>
                                            </div>  
                                            
                                            <div class="col-md-6">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="sig_esc">Signatário da Escrituração</label>
                                                        <select class="form-control" name="sig_esc" id="sig_esc">
                                                            <?php 
                                                                foreach($array_contadores as $value){
                                                                    echo "<option value='{$value['id_contador']}'>{$value['nome']}</option>" ;
                                                                }
                                                            ?>
                                                          
                                                        </select>
                                                      </div>                                                        
                                                    </div>
                                                </div>
                                            </div>  
                                            
                                        </div>     
                                    </div><!-- /.panel-body -->
                                    <div class="panel-footer text-right">
                                        <input type="hidden" name="home" id="home" value="">
                                        <input type="hidden" name="method" id="method" value="gerar-contabil">
                                        <button name="gerar_ECD" type="submit" class="btn btn-primary"><i class="fa fa-refresh"></i> Gerar</button>
                                    </div>
                                </div><!-- /.panel -->                                
                            </form>
                        </div>
                        
                        <div role="tabpanel" class="tab-pane" id="fiscal">
                            <form method="post" class="form-horizontal" id="form_ecf">                                
                                <div class="panel panel-default">
                                    <div class="panel-heading">IDENTIFICAÇÃO</div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <label for="cnpj_ecf">CNPJ</label>
                                                        <input type="text" class="form-control validate[required]" name="cnpj_ecf" value ='07.813.739/0001-61' id="cnpj_ecf" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <label for="nome_empresarial_ecf">Nome Empresarial</label>
                                                        <input type="text" class="form-control validate[required]" name="nome_empresarial_ecf" value="INSTITUTO DOS LAGOS RIO" id="nome_empresarial_ecf" readonly >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 "> 
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-10 col-md-offset-1">
                                                            <label for="fe" class="control-label">Indicador de Situação no Início do Período</label> 
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_inicio_periodo_ecf" name="indicador_situacao_inicio_periodo_ecf" value="0" checked="">Normal <i class="text text-danger text-sm">(Início no primeiro dia do ano)</i></label>
                                                            </div>
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_inicio_periodo_ecf" name="indicador_situacao_inicio_periodo_ecf" value="1">Abertura</label>
                                                            </div>   
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_inicio_periodo_ecf" name="indicador_situacao_inicio_periodo_ecf" value="2">Resultante de Cisão/Fusão ou remanescente de Cisão, ou realizou Incorporação</label>
                                                            </div> 
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_inicio_periodo_ecf" name="indicador_situacao_inicio_periodo_ecf" value="4">Início de obrigatoriedade na entrega da ECD no curso do ano calendário</label>
                                                            </div> <br><br><br><br><br><br>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6 ">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-10 col-md-offset-1">
                                                            <label for="fe" class="control-label">Indicador de Situação Especial</label> 
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_especial_ecf" name="indicador_situacao_especial_ecf" value="0" checked><strong>0</strong> - Normal (Sem ocorrência de situação especial ou evento)</label>
                                                            </div>
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_especial_ecf" name="indicador_situacao_especial_ecf" value="1"><strong>1</strong> - Extinção</label>
                                                            </div>
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_especial_ecf" name="indicador_situacao_especial_ecf" value="2"><strong>2</strong> - Fusão</label>
                                                            </div>
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_especial_ecf" name="indicador_situacao_especial_ecf" value="3"><strong>3</strong> - Incorporação/ Incorporadora</label>
                                                            </div>
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_especial_ecf" name="indicador_situacao_especial_ecf" value="4"><strong>4</strong> - Incorporação/ Incorporada</label>
                                                            </div>
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_especial_ecf" name="indicador_situacao_especial_ecf" value="5"><strong>5</strong> - Cisão Total</label>
                                                            </div>
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_especial_ecf" name="indicador_situacao_especial_ecf" value="6"><strong>6</strong> - Cisão Parcial</label>
                                                            </div>                                                                
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_especial_ecf" name="indicador_situacao_especial_ecf" value="8"><strong>8</strong> - Desenquadramento de Imune/ Isenta</label>
                                                            </div> 
                                                            <div class="radio">
                                                                <label><input type="radio" class="indicador_situacao_especial_ecf" name="indicador_situacao_especial_ecf" value="9"><strong>9</strong> - Inclusão no Simples Nacional</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 "> 
                                                <div class="panel panel-default patr_remanesc_cisao_ecf-panel">
                                                    <div class="panel-body">
                                                        <label for="patr_remanesc_cisao_ecf">Patrimônio Remanescente em Caso de Cisão (%)</label>
                                                        <input type="text" class="form-control validate[required]" name="patr_remanesc_cisao_ecf" id="patr_remanesc_cisao_ecf">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-12 col-lg-offset-1">
                                                            <label for="dta_esp" class="control-label">Data da Situação Especial/ Evento</label> 
                                                            <input type="text" id='dta_esp' name='dta_esp' class='text-sm text-center data validate[required] form-control' disabled>                                                                                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-12 col-lg-offset-1">
                                                            <label for="" class="control-label">Exercício</label>
                                                            <div class="input-group">
                                                                <div class="input-group-addon control-label">Início</div>
                                                                <input type="text" value="01/01/2016" id='dta_ini_ecf' name='dta_ini_ecf' class='text-sm text-center data validate[required] form-control' readonly>
                                                                <div class="input-group-addon control-label">Fim</div>
                                                                <input type="text" id='dta_fin_ecf' name='dta_fin_ecf' class='text-sm text-center data validate[required] form-control'>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-10 col-md-offset-1">
                                                            <label for="esc_retificadora_ecf">Escrituração Retificadora ?</label>
                                                            <select class="form-control" name="esc_retificadora_ecf" id="esc_retificadora_ecf">
                                                                <option value="F">ECF original com mudança de forma de tributação (art. 5o da IN no 166/1999)</option> 
                                                                <option value="N">ECF original</option> 
                                                                <option value="S">ECF retificadora</option> 
                                                            </select>
                                                        </div>  
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="panel panel-default num_recibo_anterior_ecf_body">
                                                    <div class="panel-body">
                                                        <label for="num_recibo_anterior_ecf">Número do Recibo Anterior</label>
                                                        <input type="text" class="form-control validate[required]" name="num_recibo_anterior_ecf" id="num_recibo_anterior_ecf" maxlength="41">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-10 col-md-offset-1">
                                                            <label for="tipo_ecf">Tipo da ECF</label>
                                                            <select class="form-control" name="tipo_ecf" id="tipo_ecf">
                                                                <option value="0">ECF de empresa não participante de SCP como sócio ostensivo</option> 
                                                                <option value="1">ECF de empresa participante de SCP como sócio ostensivo</option> 
                                                                <option value="2">ECF da SCP</option>                                                                  
                                                            </select>
                                                        </div>  
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="panel panel-default id_scp_panel">
                                                    <div class="panel-body" >
                                                        <label for="id_scp">Identificação da SCP</label>
                                                        <input type="text" class="form-control validate[required]" name="id_scp" id="id_scp">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row" id="sig_esc_ecf1">                                            
                                            <div class="col-md-12">
                                                <div class="panel panel-default">
                                                    <div class="panel-body"> 
                                                      
                                                        <div class="col-md-4">
                                                          <label for="sig_esc_ecf">Signatário da Escrituração</label>
                                                          <select class="form-control" name="sig_esc_ecf[]" id="sig_esc_ecf">
                                                              <option value='-1'>Selecione um Contador</option>
                                                              <?php 
                                                                  foreach($array_contadores as $value){
                                                                      echo "<option value='{$value['id_contador']}'>{$value['nome']}</option>" ;
                                                                  }
                                                              ?>

                                                          </select>
                                                        </div> 

                                                          <div class="col-md-4 col-md-offset-1">
                                                          <label for="qualif_assinante">Qualificação do Assinante</label>
                                                          <select class="form-control" name="qualif_assinante[]" id="qualif_assinante">
                                                              <option value='-1'>Selecione uma Qualificação</option>                                                                  
                                                              <option value='204'>Conselheiro de Administração</option>
                                                              <option value='205'>Administrador</option>
                                                              <option value='206'>Administrador do Grupo</option>
                                                              <option value='207'>Administrador de Sociedade Filiada</option>
                                                              <option value='220'>Administrador Judicial - Pessoa Física</option>
                                                              <option value='222'>Administrador Judicial - Profissional Responsável</option>
                                                              <option value='223'>Administrador Judicial/ Gestor</option>
                                                              <option value='226'>Gestor Judicial</option>
                                                              <option value='309'>Procurador</option>
                                                              <option value='312'>Inventariante</option>
                                                              <option value='313'>Liquidante</option>
                                                              <option value='315'>Interventor</option>
                                                              <option value='401'>Titular Pessoa Física - EIRELI</option>
                                                              <option value='801'>Empresário</option>
                                                              <option value='900'>Contador/ Contabilista</option>
                                                              <option value='999'>Outros</option>
                                                          </select>
                                                        </div> 
                                                        <div class="col-md-2">
                                                            <button id="add_signatario" type="button" class="btn btn-success" style="margin-top: 25px">+</button>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>  
                                         </div>
                                        
                                        <div class="row" id="sig_esc_ecf2" style="display:none">                                            
                                            <div class="col-md-12">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                      
                                                        <div class="col-md-4">
                                                          <label for="sig_esc_ecf">Signatário da Escrituração</label>
                                                          <select class="form-control" name="sig_esc_ecf[]" id="sig_esc_ecf">
                                                              <option value='-1'>Selecione um Contador</option>
                                                              <?php 
                                                                  foreach($array_contadores as $value){
                                                                      echo "<option value='{$value['id_contador']}'>{$value['nome']}</option>" ;
                                                                  }
                                                              ?>

                                                          </select>
                                                        </div> 

                                                          <div class="col-md-4 col-md-offset-1">
                                                          <label for="qualif_assinante">Qualificação do Assinante</label>
                                                          <select class="form-control" name="qualif_assinante[]" id="qualif_assinante">
                                                              <option value='-1'>Selecione uma Qualificação</option>                                                                  
                                                              <option value='204'>Conselheiro de Administração</option>
                                                              <option value='205'>Administrador</option>
                                                              <option value='206'>Administrador do Grupo</option>
                                                              <option value='207'>Administrador de Sociedade Filiada</option>
                                                              <option value='220'>Administrador Judicial - Pessoa Física</option>
                                                              <option value='222'>Administrador Judicial - Profissional Responsável</option>
                                                              <option value='223'>Administrador Judicial/ Gestor</option>
                                                              <option value='226'>Gestor Judicial</option>
                                                              <option value='309'>Procurador</option>
                                                              <option value='312'>Inventariante</option>
                                                              <option value='313'>Liquidante</option>
                                                              <option value='315'>Interventor</option>
                                                              <option value='401'>Titular Pessoa Física - EIRELI</option>
                                                              <option value='801'>Empresário</option>
                                                              <option value='900'>Contador/ Contabilista</option>
                                                              <option value='999'>Outros</option>
                                                          </select>
                                                        </div> 
                                                        <div class="col-md-2">
                                                            <button id="add_signatario2" type="button" class="btn btn-success" style="margin-top: 25px">+</button>
                                                        </div>
                                                        
                                                        
                                                    </div>
                                                </div>
                                            </div>  
                                         </div>
                                        
                                        <div class="row" id="sig_esc_ecf3" style="display:none">                                            
                                            <div class="col-md-12">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                      
                                                        <div class="col-md-4">
                                                          <label for="sig_esc_ecf">Signatário da Escrituração</label>
                                                          <select class="form-control" name="sig_esc_ecf[]" id="sig_esc_ecf">
                                                              <option value='-1'>Selecione um Contador</option>
                                                              <?php 
                                                                  foreach($array_contadores as $value){
                                                                      echo "<option value='{$value['id_contador']}'>{$value['nome']}</option>" ;
                                                                  }
                                                              ?>

                                                          </select>
                                                        </div> 

                                                          <div class="col-md-4 col-md-offset-1">
                                                          <label for="qualif_assinante">Qualificação do Assinante</label>
                                                          <select class="form-control" name="qualif_assinante[]" id="qualif_assinante">
                                                              <option value='-1'>Selecione uma Qualificação</option>                                                                  
                                                              <option value='204'>Conselheiro de Administração</option>
                                                              <option value='205'>Administrador</option>
                                                              <option value='206'>Administrador do Grupo</option>
                                                              <option value='207'>Administrador de Sociedade Filiada</option>
                                                              <option value='220'>Administrador Judicial - Pessoa Física</option>
                                                              <option value='222'>Administrador Judicial - Profissional Responsável</option>
                                                              <option value='223'>Administrador Judicial/ Gestor</option>
                                                              <option value='226'>Gestor Judicial</option>
                                                              <option value='309'>Procurador</option>
                                                              <option value='312'>Inventariante</option>
                                                              <option value='313'>Liquidante</option>
                                                              <option value='315'>Interventor</option>
                                                              <option value='401'>Titular Pessoa Física - EIRELI</option>
                                                              <option value='801'>Empresário</option>
                                                              <option value='900'>Contador/ Contabilista</option>
                                                              <option value='999'>Outros</option>
                                                          </select>
                                                        </div> 
                                                    </div>
                                                </div>
                                            </div>  
                                         </div>
                                        
                                        
                                    </div> <!-- panel-body -->
                                </div>  <!-- panel-default -->   
                                <div class="panel panel-default">
                                    <div class="panel-heading">PARÂMETROS DE TRIBUTAÇÃO</div>                                    
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-10 col-md-offset-1">
                                                            <div class="checkbox">
                                                            <label><input type="checkbox" id="opt_refis" name="opt_refis">Indicador de Optante pelo Refis</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-10 col-md-offset-1">
                                                            <div class="checkbox">
                                                            <label><input type="checkbox" id="opt_paes" name="opt_paes">Indicador de Optante pelo Paes</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                        
                                    <div class="row">
                                        
                                        <div class="col-md-4">
                                            <div class="panel panel-default" >
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                            <label for="per_apuracao_irpj">Período de apuração do IRPJ e CSLL</label>
                                                            <select class="form-control validate[required]" name="per_apuracao_irpj" id="per_apuracao_irpj">
                                                                <option value="">-- Selecione --</option> 
                                                                <option value="A">Anual</option> 
                                                                <option value="T">Trimestral</option>                                                              
                                                            </select>                                                       
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="forma_trib_lucro">Forma de Tributação do Lucro</label>
                                                        <select class="form-control validate[required]" name="forma_trib_lucro" id="forma_trib_lucro">
                                                            
                                                            <option value="1">Lucro Real</option> 
                                                            <option value="2">Lucro Real/ Arbitrado</option> 
                                                            <option value="3">Lucro Presumido/ Real</option> 
                                                            <option value="4">Lucro Presumido/ Real/ Arbitrado</option>   
                                                            <option value="5">Lucro Presumido</option>  
                                                            <option value="6">Lucro Arbitrado</option>  
                                                            <option value="7">Lucro Presumido/ Arbitrado</option>  
                                                            <option value="8">Imune do IRPJ</option>  
                                                            <option value="9">Isento de IRPJ</option>  
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="qual_pessoa_juridica">Qualificação da Pessoa Jurídica</label>
                                                        <select class="form-control validate[required]" name="qual_pessoa_juridica" id="qual_pessoa_juridica">
                                                            <option value="01">PJ em geral</option> 
                                                            <option value="02">PJ Componente do Sistema Financeiro</option>     
                                                            <option value="03">Sociedades Seguradoras, de Capitalização ou Entidade Aberta de Previdência Complementar</option> 
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                        
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="1o_trim">1º Trimestre</label>
                                                        <select class="trimestres form-control" name="1o_trim" id="1o_trim">
                                                            <option value="0">-- Selecione --</option>
                                                            <option value="P">Presumido</option>
                                                            <option value="R">Real</option>
                                                            <option value="A">Arbitrado</option>
                                                            <option value="E">Real Estimativa</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="2o_trim">2º Trimestre</label>
                                                        <select class="trimestres form-control" name="2o_trim" id="2o_trim">
                                                            <option value="0">-- Selecione --</option>
                                                            <option value="P">Presumido</option>
                                                            <option value="R">Real</option>
                                                            <option value="A">Arbitrado</option>
                                                            <option value="E">Real Estimativa</option> 
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="3o_trim">3º Trimestre</label>
                                                        <select class="trimestres form-control" name="3o_trim" id="3o_trim">
                                                            <option value="0">-- Selecione --</option>
                                                            <option value="P">Presumido</option>
                                                            <option value="R">Real</option>
                                                            <option value="A">Arbitrado</option>
                                                            <option value="E">Real Estimativa</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="4o_trim">4º Trimestre</label>
                                                        <select class="trimestres form-control" name="4o_trim" id="4o_trim">
                                                            <option value="0">-- Selecione --</option>
                                                            <option value="P">Presumido</option>
                                                            <option value="R">Real</option>
                                                            <option value="A">Arbitrado</option>
                                                            <option value="E">Real Estimativa</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                                        
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="tipo_escrituracao_ecf">Tipo da Escrituração</label>
                                                        <select class="form-control validate[required]" name="tipo_escrituracao_ecf" id="tipo_escrituracao_ecf" disabled>
                                                            <option value="C">Obrigadas a entregar a ECD ou entrega facultativa da ECD com recuperação de dados</option> 
                                                            <option value="L">Livro Caixa ou não obrigadas a entregar a ECD ou entrega facultativa da ECD sem recuperação de dados.</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="tipo_ent_imune_isenta">Tipo de Entidade Imune ou Isenta</label>
                                                        <select class="form-control validate[required]" name="tipo_ent_imune_isenta" id="tipo_ent_imune_isenta" disabled>
                                                            <option value="01">Assistência Social</option> 
                                                            <option value="02">Educacional</option>        
                                                            <option value="03">Sindicato de Trabalhadores</option>      
                                                            <option value="04">Associação Civil</option>  
                                                            <option value="05">Cultural</option>      
                                                            <option value="06">Entidade Fechada de Previdência Complementar</option>   
                                                            <option value="07">Filantrópica </option>  
                                                            <option value="08">Sindicato</option>  
                                                            <option value="09">Recreativa</option>  
                                                            <option value="10">Científica</option> 
                                                            <option value="11">Associação de Poupança e Empréstimo</option> 
                                                            <option value="12">Entidade Aberta de Previdência Complementar(Sem Fins Lucrativos)</option> 
                                                            <option value="13">Fifa e Entidades Relacionadas</option> 
                                                            <option value="14">CIO e Entidades Relacionadas</option> 
                                                            <option value="15">Partidos Políticos</option> 
                                                            <option value="99">Outras</option> 
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="exist_ativ_trib">Existência de Atividade Tributada pelo IRPJ para Imune ou Isenta </label>
                                                        <select class="form-control" name="exist_ativ_trib" id="exist_ativ_trib" disabled>
                                                            <option value="A">Anual</option> 
                                                            <option value="D">Desobrigada</option>
                                                            <option value="T">Trimestral</option> 
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                        
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="apuracao_csll">Apuração da CSLL</label>
                                                        <select class="form-control" name="apuracao_csll" id="apuracao_csll" disabled>
                                                            <option value="A">Anual</option> 
                                                            <option value="D">Desobrigada</option>
                                                            <option value="T">Trimestral</option>  
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
<!--                                        <div class="col-md-4">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="opt_lei">Optante pela Aplicação das disposições da Lei nº 12.973/2014 para o ano calendário de 2014</label>
                                                        <select class="form-control" name="opt_lei" id="opt_lei">
                                                            <option value="S">Sim</option> 
                                                            <option value="N">Não</option>                                                              
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>-->
                                        <div class="col-md-4">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="ind_rec_receita">Critério de reconhecimento de receitas para empresas tributadas pelo Lucro Presumido</label>
                                                        <select class="form-control" name="ind_rec_receita" id="ind_rec_receita" disabled>
                                                            <option value="1">Regime de Caixa</option> 
                                                            <option value="2">Regime de Competência</option> 
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                        <h4>Forma de Determinação das Estimativas Mensais</h4>
                                    <div class="row">                                        
                                       <div class="col-md-3">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="est_janeiro">Janeiro </label>
                                                        <select class="form-control" name="est_janeiro" id="est_janeiro">
                                                            <option value="0">-- Selecione --</option> 
                                                            <option value="E">Receita Bruta e Acréscimos</option> 
                                                            <option value="B">Balanço/ Balancete de Suspensão/ Redução</option> 
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="est_fevereiro">Fevereiro </label>
                                                        <select class="form-control" name="est_fevereiro" id="est_fevereiro">
                                                            <option value="0">-- Selecione --</option> 
                                                            <option value="E">Receita Bruta e Acréscimos</option> 
                                                            <option value="B">Balanço/ Balancete de Suspensão/ Redução</option> 
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="est_marco">Março </label>
                                                        <select class="form-control" name="est_marco" id="est_marco">
                                                            <option value="0">-- Selecione --</option> 
                                                            <option value="E">Receita Bruta e Acréscimos</option> 
                                                            <option value="B">Balanço/ Balancete de Suspensão/ Redução</option> 
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="est_abril">Abril </label>
                                                        <select class="form-control" name="est_abril" id="est_abril">
                                                            <option value="0">-- Selecione --</option> 
                                                            <option value="E">Receita Bruta e Acréscimos</option> 
                                                            <option value="B">Balanço/ Balancete de Suspensão/ Redução</option> 
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">                                        
                                       <div class="col-md-3">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="est_maio">Maio </label>
                                                        <select class="form-control" name="est_maio" id="est_maio">
                                                            <option value="0">-- Selecione --</option> 
                                                            <option value="E">Receita Bruta e Acréscimos</option> 
                                                            <option value="B">Balanço/ Balancete de Suspensão/ Redução</option> 
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="est_junho">Junho </label>
                                                        <select class="form-control" name="est_junho" id="est_junho">
                                                            <option value="0">-- Selecione --</option> 
                                                            <option value="E">Receita Bruta e Acréscimos</option> 
                                                            <option value="B">Balanço/ Balancete de Suspensão/ Redução</option> 
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="est_julho">Julho </label>
                                                        <select class="form-control" name="est_julho" id="est_julho">
                                                            <option value="0">-- Selecione --</option> 
                                                            <option value="E">Receita Bruta e Acréscimos</option> 
                                                            <option value="B">Balanço/ Balancete de Suspensão/ Redução</option> 
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="est_agosto">Agosto </label>
                                                        <select class="form-control" name="est_agosto" id="est_agosto">
                                                            <option value="0">-- Selecione --</option> 
                                                            <option value="E">Receita Bruta e Acréscimos</option> 
                                                            <option value="B">Balanço/ Balancete de Suspensão/ Redução</option> 
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">                                        
                                       <div class="col-md-3">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="est_setembro">Setembro </label>
                                                        <select class="form-control" name="est_setembro" id="est_setembro">
                                                           <option value="0">-- Selecione --</option> 
                                                            <option value="E">Receita Bruta e Acréscimos</option> 
                                                            <option value="B">Balanço/ Balancete de Suspensão/ Redução</option> 
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="est_outubro">Outubro </label>
                                                        <select class="form-control" name="est_outubro" id="est_outubro">
                                                            <option value="0">-- Selecione --</option> 
                                                            <option value="E">Receita Bruta e Acréscimos</option> 
                                                            <option value="B">Balanço/ Balancete de Suspensão/ Redução</option> 
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="est_novembro">Novembro </label>
                                                        <select class="form-control" name="est_novembro" id="est_novembro">
                                                            <option value="0">-- Selecione --</option> 
                                                            <option value="E">Receita Bruta e Acréscimos</option> 
                                                            <option value="B">Balanço/ Balancete de Suspensão/ Redução</option> 
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="form-group col-md-10 col-md-offset-1">
                                                        <label for="est_dezembro">Dezembro </label>
                                                        <select class="form-control" name="est_dezembro" id="est_dezembro">
                                                           <option value="0">-- Selecione --</option> 
                                                            <option value="E">Receita Bruta e Acréscimos</option> 
                                                            <option value="B">Balanço/ Balancete de Suspensão/ Redução</option>  
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                                       
                                        
                                </div>
                                    
                                
                                    
                                

                            
                        </div>
                                <div class="panel panel-default">
                                    <div class="panel-heading">PARÂMETROS COMPLEMENTARES</div>                                    
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group col-md-10 col-md-offset-1">
                                                            <label for="aliq_csll">PJ Sujeita à Alíquota da CSLL de 9%
                                                            ou 17% ou 20% em 31/12/2015</label>
                                                            <select class="form-control" name="aliq_csll" id="aliq_csll">
                                                                <option value="1">9%</option> 
                                                                <option value="2">17%</option> 
                                                                <option value="3">20%</option> 
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div>
                                                            <label for="qtd_scp_pj">Quantidade de SCP da PJ</label>
                                                            <input type="text" id="qtd_scp_pj" name="qtd_scp_pj" class="form-control validate[required]" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="row row-checks">
                                                    <div class="form-group col-md-4 ">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="adm_clubes" name="adm_clubes">Admnistradora de Fundos e Clubes de Investimento</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="part_consorcios" name="part_consorcios">Participações em Consórcios de Empresas</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="operacoes_exterior" name="operacoes_exterior">Operações com o Exterior</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row row-checks">
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="doacoes_eleitorais" name="doacoes_eleitorais">Doações a Campanhas Eleitorais</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="operacoes_vinculada" name="operacoes_vinculada">Operações com Pessoa Vinculada / Interposta Pessoa / País com Tributação Favorecida</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="pj_artigos48_49" name="pj_artigos48_49">PJ Enquadrada nos artigos 48 ou 49 da IN RFB no. 1312/2012</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row row-checks">
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="participacoes_exterior" name="participacoes_exterior">Participações no Exterior</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="ativ_rural" name="ativ_rural">Atividade Rural</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="lucro_exploracao" name="lucro_exploracao">Lucro da Exploração</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row row-checks">
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="isencao_red_lucro_presumido" name="isencao_red_lucro_presumido">Isenção e Redução do Imposto para Lucro Presumido</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="finor_finam" name="finor_finam">FINOR/ FINAM</label>
                                                        </div>
                                                    </div> 
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="equiv_patr" name="equiv_patr">Participação Avaliada Pelo Método de Equivalência Patrimonial</label>
                                                        </div>
                                                    </div> 
                                                </div>
                                                <hr>
                                                <div class="row row-checks">
                                                    <div class="form-group col-md-4 ">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="part_permanente_coligadas_controladas" name="part_permanente_coligadas_controladas">Participação Permanente em Coligadas ou Controladas</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="vendas_exp" name="vendas_exp">PJ Efetuou Vendas a Empresa Comercial Exportadora 
                                                            com Fim Específico de Exportação</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="rec_exterior" name="rec_exterior">Recebimentos do Exterior ou de Não Residentes</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row row-checks">
                                                    <div class="form-group col-md-4 ">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="ativos_exterior" name="ativos_exterior">Ativos no Exterior</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="pj_comercial_exportadora" name="pj_comercial_exportadora">PJ Comercial Exportadora</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="pags_ao_exterior" name="pags_ao_exterior">Pagamentos ao Exterior ou a Não Residentes</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <h4>Informações Econômicas</h4>
                                                 <div class="row row-checks">
                                                    <div class="form-group col-md-4 ">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="comercio_eletronico" name="comercio_eletronico">Comércio Eletrônico e Tecnologia da Informação</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="royalties_brasil_exterior" name="royalties_brasil_exterior">Royalties Recebidos do Brasil e do Exterior</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="royalties_pagos" name="royalties_pagos">Royalties Pagos a Beneficiários do Brasil e do Exterior</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row row-checks">
                                                    <div class="form-group col-md-4 ">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="rendimentos_sjd" name="rendimentos_sjd">Rendimentos Relativos a Serviços, Juros e Dividendos Recebidos do Brasil e Do Exterior</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="pagamentos_remessas" name="pagamentos_remessas">Pagamentos ou Remessas a Título de Serviços
                                                        , Juros e Dividendos a Beneficiários do Brasil e do Exterior</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="inovacao_tec" name="inovacao_tec">Inovação Tecnológica e Desenvolvimento Tecnológico</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row row-checks">
                                                    <div class="form-group col-md-4 ">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="cap_info" name="cap_info">Capacitação de Informática e Inclusão Digital</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="repes_recap_etc" name="repes_recap_etc">Repes, Recap, Padis, PATVD, Reidi, Repenec, 
                                                        Reicomp, Retaero, Recine, Resíduos Sólidos, Recopa, Copa do Mundo, Retid, REPNBL-Redes, Reif e Olimpíadas</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="polo_manaus" name="polo_manaus">Pólo Industrial de Manaus e Amazônia Ocidental</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row row-checks">
                                                    <div class="form-group col-md-4 ">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="zon_processamento_exp" name="zon_processamento_exp">Zonas de Processamento de Exportação</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="areas_livre_comercio" name="areas_livre_comercio">Áreas de Livre Comércio</label>
                                                        </div>
                                                    </div>  
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="ind_pais_a_pais" name="ind_pais_a_pais">Entidade integrante de grupo multinacional, nos termos da Instrução Normativa RFB nº 1.681/2016</label>
                                                        </div>
                                                    </div> 
                                                </div>
                                                <hr>
                                                <h4 class="tipos_de_programa">Parâmetros de Identificação dos Tipos de Programa </h4> 
                                                <div class="row row-checks tipos_de_programa">
                                                    <div class="form-group col-md-4 ">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="repes" name="repes">Repes</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="recap" name="recap">Recap</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="padis" name="padis">Padis</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row row-checks tipos_de_programa">
                                                    <div class="form-group col-md-4 ">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="patvd" name="patvd">PATVD</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="reidi" name="reidi">Reidi</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="repenec" name="repenec">Repenec</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row row-checks tipos_de_programa">
                                                    <div class="form-group col-md-4 ">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="recine" name="recine">Recine</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="res_solidos" name="res_solidos">Indicador de Resíduos Sólidos</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="recopa" name="recopa">Recopa</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row row-checks tipos_de_programa">
                                                    <div class="form-group col-md-4 ">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="copa_do_mundo" name="copa_do_mundo">Copa do Mundo</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="retid" name="retid">Retid</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="repnbl" name="repnbl">REPNBL-Redes</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row row-checks tipos_de_programa">
                                                    <div class="form-group col-md-4 ">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="reif" name="reif">REIF</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="olimpiadas" name="olimpiadas">Jogos Olímpicos</label>
                                                        </div>
                                                    </div>   
                                                    <div class="form-group col-md-4">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="reicomp" name="reicomp">Reicomp</label>
                                                        </div>
                                                    </div> 
                                                </div>
                                                
                                                <div class="row row-checks tipos_de_programa">
                                                    <div class="form-group col-md-4 ">
                                                        <div class="checkbox">
                                                        <label><input type="checkbox" id="retaero" name="retaero">Retaero</label>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                                
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="panel panel-default panel_scps">
                                    <div class="panel-heading">IDENTIFICAÇÃO DAS SCP</div>                                    
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div>
                                                            <label for="cnpj_da_scp">CNPJ da SCP</label>
                                                            <input type="text" id="cnpj_da_scp" name="cnpj_da_scp" class="form-control validate[required]">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <div>
                                                            <label for="nome_da_scp">Nome da SCP</label>
                                                            <input type="text" id="nome_da_scp" name="nome_da_scp" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <input type="hidden" name="home" id="home" value="">
                                    <input type="hidden" name="method" id="method" value="gerar-contabil">
                                    <button name="gerar_ECF" type="submit" class="btn btn-primary"><i class="fa fa-refresh"></i> Gerar</button>
                            </form>
                    </div>
                </div>
            </div>
            <?php include("../../template/footer.php"); ?>
        </div><!-- /.container -->

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            
            $(document).ready(function(){
                
                
                $("#cnpj").mask("99.999.999/9999-99");
                $("#cnpj_da_scp").mask("99.999.999/9999-99");                
                $('.camposSCP').hide();
                $('.camposJ935').hide();
                $('#modalDownload').modal('toggle');
                $("#form-sped-contabil-ecd").validationEngine();
                $("#form_ecf").validationEngine();
                $('#patr_remanesc_cisao_ecf').prop('disabled', 'disabled');
                $('#id_scp').prop('disabled', 'disabled');
                $('.panel_scps').hide();
                $('.row_scp2').hide();
                $('.camposI020').hide();
                $('#id_scp').mask("99.999.999/9999-99"); 
                
                
                //default ecf
                $('#dta_fin_ecf').val("31/12/2016"); 
                
                
                
                $('.id_moeda_funcional_ttarea').tooltip({title: "Indica que a escrituração abrange valores \n\
                com base na moeda funcional (art. 156 da Instrução Normativa RFB no 1.515, de 24 de novembro de 2014).\n\
                Observações: Nessa situação, deverá ser utilizado o registro I020 para informação de campos adicionais." ,
                placement: "bottom"});
            
                $('.ttip_existe_nire').tooltip({title: "Indica se a empresa possui registro na Junta Comercial" ,
                placement: "bottom"});
            
                $('.dt_arq_atos_ttip').tooltip({title: "É a data de arquivamento do ato de constituição da empresa. As datas de alterações contratuais devem ser desconsideradas. Em termos práticos, é a data do NIRE. Para empresas que não possuem NIRE, colocar a data de abertura da empresa." ,
                placement: "bottom"});
            
                $('.dt_ato_conv_ttip').tooltip({title: "É a data em que a Junta Comercial arquivou o documento que formaliza a conversão. Em termos práticos, é a data do NIRE. Com o novo Código Civil, parte das antigas sociedades civis passou a ser classificada como sociedade empresária. Com isto, deixaram de ter registro em cartório e passaram para as juntas comerciais." ,
                placement: "bottom"});
            
                $('.cod_ent_ref_ttip').tooltip({title: "Neste registro, devem ser incluídas as inscrições cadastrais da pessoa jurídica que, legalmente, tenha direito de acesso ao livro contábil digital." ,
                placement: "bottom"});
            
            
            
                
                $("#add_signatario").on('click', function(){
                    $("#sig_esc_ecf2").show();
                    $(this).hide();
                   
                });      
                
                $("#add_signatario2").on('click', function(){
                    $("#sig_esc_ecf3").show();
                    $(this).hide();
                   
                });  
                   
                $('input[name="J935"]').change(function () {
                    if ($('input[name="J935"]:checked').val() === "1") {
                        $('.camposJ935').show();
                    } else {
                        $('.camposJ935').hide();
                    }
                });
                
                $("#exist_ativ_trib").on('change', function(){
                    if($("#exist_ativ_trib").val() == "D"){
                        $('#aliq_csll').prop('disabled', 'disabled');
                    } else{
                        $('#aliq_csll').removeAttr('disabled');
                    }
                }) 
                
                $("#id_moeda_funcional").on("click", function(){
                    
                    $('.camposI020').toggle();
                });
                
                
                $('.indicador_situacao_inicio_periodo_ecf').on('change', function(){
                    var valor_ini_periodo = $('.indicador_situacao_inicio_periodo_ecf:checked').val();
                    if(valor_ini_periodo == 0){
                        $('#dta_ini_ecf').val('01/01/2016');
                        $('#dta_ini_ecf').prop('readonly', 'readonly');
                       
                    }else{
                        $('#dta_ini_ecf').val('');
                        $('#dta_ini_ecf').removeAttr('readonly');
                    }
                });

                $('input[name="cod_scp"]').change(function () {
                    if ($('input[name="cod_scp"]:checked').val() === "1") {
                        $('.camposSCP').show();
                        $('#id_scp_ecd').addClass('validate[required]');
                    } else {
                        $('.camposSCP').hide();
                    }
                });
                
                $('input[name="cod_scp"]').change(function () {
                    if ($('input[name="cod_scp"]:checked').val() === "2") {
                        $('.row_scp2').show();
                    } else {
                        $('.row_scp2').hide();
                    }
                });

                $("#cnpj_ecf").mask("99.999.999/9999-99");
                
//                Alterações em 'Indicador de Situação Especial'
                $('.indicador_situacao_especial_ecf').on('change', function(){
                    var valor = $(this).val();
                    if(valor != 0 ){
                        
                        $('#dta_esp').removeAttr('disabled');
                    }else{                
                        $('#dta_fin_ecf').val("31/12/2016"); 
                        $('#dta_esp').prop('disabled', 'disabled');
                    }
                    
                    if(valor == 6 ){
                        $('#patr_remanesc_cisao_ecf').removeAttr('disabled');
                    }else{
                        $('#patr_remanesc_cisao_ecf').prop('disabled', 'disabled');
                    }
                });
                
                //Datas inicio/fim
                $('#dta_ini_ecf').on('change', function(){                    
                    var data = $('#dta_ini_ecf').val();
                    var ano = data.substring(6);
                    $('#dta_fin_ecf').val('31/12/'+ano);
                    
                    var ind_sit_special = $('.indicador_situacao_especial_ecf:checked').val();
                    if(ind_sit_special == 0){
                        $('#dta_fin_ecf').val('31/12/'+ano);
                        $('#dta_fin_ecf').prop('readonly', 'readonly');
                    }
                    
                });
                
                //Escrituração retificadora
                $('#esc_retificadora_ecf').on('change', function(){
                    var ret = $('#esc_retificadora_ecf').val();
                    if(ret == 'S' || ret == 'F'){
                        $('.num_recibo_anterior_ecf_body').show();
                    }else{
                        $('.num_recibo_anterior_ecf_body').hide();
                    }
                });
                
                //Tipo ECF
                $('#tipo_ecf').on('change', function(){
                    var ret = $('#tipo_ecf').val();
                    if(ret == 2){
                        
                        $('#id_scp').removeAttr('disabled');
                    }else{
                        
                        $('#id_scp').prop('disabled', 'disabled');
                    }
                    
                    if(ret == 1){
                        $('.panel_scps').show();
                        $('#qtd_scp_pj').removeAttr('disabled');
                        
                    }else{
                        $('.panel_scps').hide();
                        $('#qtd_scp_pj').prop('disabled', 'disabled');                        
                    }
                    
                });
                
                               
                $('#apuracao_csll').on('change', function(){
                   var valor_check = $('#forma_trib_lucro').val();
                   ap_csll = $('#apuracao_csll').val();
                   if((valor_check == 8 || valor_check == 9) && (ap_csll == 'D')){
                       $('#aliq_csll').prop('disabled', 'disabled');
                   }else{
                       $('#aliq_csll').removeAttr('disabled');
                   }                   
                });
                
                
                $("#per_apuracao_irpj").on('change', function(){ 
                    
                    //SELECT forma de trib do lucro
                    if($("#per_apuracao_irpj").val() == 'A'){
                        
                        //REGRA_FORMA_APUR_VALIDA
                        $("#opt_refis").prop("checked", true);
                     
                        $("#forma_trib_lucro option").remove();
                         
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "",
                            text: '-- Selecione --'
                        }));
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "1",
                            text: 'Lucro Real'
                        }));
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "2",
                            text: 'Lucro Real/ Arbitrado'
                        }));
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "3",
                            text: 'Lucro Presumido/ Real'
                        }));
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "4",
                            text: 'Lucro Presumido/ Real/ Arbitrado'
                        }));
                    }else if($("#per_apuracao_irpj").val() == 'T'){
                        
                        //REGRA_FORMA_APUR_VALIDA
                        $("#opt_refis").prop("checked", false);
                       
                        $("#forma_trib_lucro option").remove();
                         
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "",
                            text: '-- Selecione --'
                        }));
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "1",
                            text: 'Lucro Real'
                        }));
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "2",
                            text: 'Lucro Real/ Arbitrado'
                        }));
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "3",
                            text: 'Lucro Presumido/ Real'
                        }));
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "4",
                            text: 'Lucro Presumido/ Real/ Arbitrado'
                        }));
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "5",
                            text: 'Lucro Presumido'
                        }));
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "6",
                            text: 'Lucro Arbitrado'
                        }));
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "7",
                            text: 'Lucro Presumido/ Arbitrado'
                        }));
                    }else{
                        $("#forma_trib_lucro option").remove();
                         
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "",
                            text: '-- Selecione --'
                        }));
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "1",
                            text: 'Lucro Real'
                        }));
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "2",
                            text: 'Lucro Real/ Arbitrado'
                        }));
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "3",
                            text: 'Lucro Presumido/ Real'
                        }));
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "4",
                            text: 'Lucro Presumido/ Real/ Arbitrado'
                        }));
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "5",
                            text: 'Lucro Presumido'
                        }));
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "6",
                            text: 'Lucro Arbitrado'
                        }));
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "7",
                            text: 'Lucro Presumido/ Arbitrado'
                        }));
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "8",
                            text: 'Imune do IRPJ'
                        }));
                        $('#forma_trib_lucro').append($('<option>', {
                            value: "9",
                            text: 'Isento de IRPJ'
                        }));
                    }
                   
                   if($("#per_apuracao_irpj").val() == 'A'){
                       
                       $("#est_janeiro").removeAttr('disabled');
                       $("#est_fevereiro").removeAttr('disabled');
                       $("#est_marco").removeAttr('disabled');
                       $("#est_abril").removeAttr('disabled');
                       $("#est_maio").removeAttr('disabled');
                       $("#est_junho").removeAttr('disabled');
                       $("#est_julho").removeAttr('disabled');
                       $("#est_agosto").removeAttr('disabled');
                       $("#est_setembro").removeAttr('disabled');
                       $("#est_outubro").removeAttr('disabled');
                       $("#est_novembro").removeAttr('disabled');
                       $("#est_dezembro").removeAttr('disabled');
                   }else{
                       $("#est_janeiro").prop('disabled', 'disabled');
                       $("#est_fevereiro").prop('disabled', 'disabled');
                       $("#est_marco").prop('disabled', 'disabled');
                       $("#est_abril").prop('disabled', 'disabled');
                       $("#est_maio").prop('disabled', 'disabled');
                       $("#est_junho").prop('disabled', 'disabled');
                       $("#est_julho").prop('disabled', 'disabled');
                       $("#est_agosto").prop('disabled', 'disabled');
                       $("#est_setembro").prop('disabled', 'disabled');
                       $("#est_outubro").prop('disabled', 'disabled');
                       $("#est_novembro").prop('disabled', 'disabled');
                       $("#est_dezembro").prop('disabled', 'disabled');
                   }
                });
                
                
                //REGRA_MES_BAL_RED_INVALIDO
                    $('#1o_trim').on('change', function(){
                        prim_trim                = $('#1o_trim').val();
                        
                        if(prim_trim != "R" && prim_trim != "E"){
                            $("#est_janeiro option").remove();

                            $('#est_janeiro').append($('<option>', {
                                value: "0",
                                text: 'Não Aplicável'
                            }));

                            $("#est_fevereiro option").remove();

                            $('#est_fevereiro').append($('<option>', {
                                value: "0",
                                text: 'Não Aplicável'
                            }));

                            $("#est_marco option").remove();

                            $('#est_marco').append($('<option>', {
                                value: "0",
                                text: 'Não Aplicável'
                            }));
                        }else{
                            $("#est_janeiro option").remove();

                            $('#est_janeiro').append($('<option>', {
                                value: "0",
                                text: '-- Selecione --'
                            }));
                            $('#est_janeiro').append($('<option>', {
                                value: "E",
                                text: 'Receita Bruta e Acréscimos'
                            }));
                            $('#est_janeiro').append($('<option>', {
                                value: "B",
                                text: 'Balanço/ Balancete de Suspensão/ Redução'
                            }));
                            
                            $("#est_fevereiro option").remove();

                            $('#est_fevereiro').append($('<option>', {
                                value: "0",
                                text: '-- Selecione --'
                            }));
                            $('#est_fevereiro').append($('<option>', {
                                value: "E",
                                text: 'Receita Bruta e Acréscimos'
                            }));
                            $('#est_fevereiro').append($('<option>', {
                                value: "B",
                                text: 'Balanço/ Balancete de Suspensão/ Redução'
                            }));
                            
                            $("#est_marco option").remove();

                            $('#est_marco').append($('<option>', {
                                value: "0",
                                text: '-- Selecione --'
                            }));
                            $('#est_marco').append($('<option>', {
                                value: "E",
                                text: 'Receita Bruta e Acréscimos'
                            }));
                            $('#est_marco').append($('<option>', {
                                value: "B",
                                text: 'Balanço/ Balancete de Suspensão/ Redução'
                            }));
                        } 
                        
                    });
                    
                    $('#2o_trim').on('change', function(){
                        seg_trim                 = $('#2o_trim').val();
                        
                        if(seg_trim != "R" && seg_trim != "E"){
                            $("#est_abril option").remove();

                            $('#est_abril').append($('<option>', {
                                value: "0",
                                text: 'Não Aplicável'
                            }));

                            $("#est_maio option").remove();

                            $('#est_maio').append($('<option>', {
                                value: "0",
                                text: 'Não Aplicável'
                            }));

                            $("#est_junho option").remove();

                            $('#est_junho').append($('<option>', {
                                value: "0",
                                text: 'Não Aplicável'
                            }));
                        }else{
                            $("#est_abril option").remove();

                            $('#est_abril').append($('<option>', {
                                value: "0",
                                text: '-- Selecione --'
                            }));
                            $('#est_abril').append($('<option>', {
                                value: "E",
                                text: 'Receita Bruta e Acréscimos'
                            }));
                            $('#est_abril').append($('<option>', {
                                value: "B",
                                text: 'Balanço/ Balancete de Suspensão/ Redução'
                            }));
                            
                            $("#est_maio option").remove();

                            $('#est_maio').append($('<option>', {
                                value: "0",
                                text: '-- Selecione --'
                            }));
                            $('#est_maio').append($('<option>', {
                                value: "E",
                                text: 'Receita Bruta e Acréscimos'
                            }));
                            $('#est_maio').append($('<option>', {
                                value: "B",
                                text: 'Balanço/ Balancete de Suspensão/ Redução'
                            }));
                            
                            $("#est_junho option").remove();

                            $('#est_junho').append($('<option>', {
                                value: "0",
                                text: '-- Selecione --'
                            }));
                            $('#est_junho').append($('<option>', {
                                value: "E",
                                text: 'Receita Bruta e Acréscimos'
                            }));
                            $('#est_junho').append($('<option>', {
                                value: "B",
                                text: 'Balanço/ Balancete de Suspensão/ Redução'
                            }));
                        } 
                        
                    });
                    
                    
                    $('#3o_trim').on('change', function(){
                        terc_trim                = $('#3o_trim').val();
                        
                        if(terc_trim != "R" && terc_trim != "E"){
                            $("#est_julho option").remove();

                            $('#est_julho').append($('<option>', {
                                value: "0",
                                text: 'Não Aplicável'
                            }));

                            $("#est_agosto option").remove();

                            $('#est_agosto').append($('<option>', {
                                value: "0",
                                text: 'Não Aplicável'
                            }));

                            $("#est_setembro option").remove();

                            $('#est_setembro').append($('<option>', {
                                value: "0",
                                text: 'Não Aplicável'
                            }));
                        }else{
                            $("#est_julho option").remove();

                            $('#est_julho').append($('<option>', {
                                value: "0",
                                text: '-- Selecione --'
                            }));
                            $('#est_julho').append($('<option>', {
                                value: "E",
                                text: 'Receita Bruta e Acréscimos'
                            }));
                            $('#est_julho').append($('<option>', {
                                value: "B",
                                text: 'Balanço/ Balancete de Suspensão/ Redução'
                            }));
                            
                            $("#est_agosto option").remove();

                            $('#est_agosto').append($('<option>', {
                                value: "0",
                                text: '-- Selecione --'
                            }));
                            $('#est_agosto').append($('<option>', {
                                value: "E",
                                text: 'Receita Bruta e Acréscimos'
                            }));
                            $('#est_agosto').append($('<option>', {
                                value: "B",
                                text: 'Balanço/ Balancete de Suspensão/ Redução'
                            }));
                            
                            $("#est_setembro option").remove();

                            $('#est_setembro').append($('<option>', {
                                value: "0",
                                text: '-- Selecione --'
                            }));
                            $('#est_setembro').append($('<option>', {
                                value: "E",
                                text: 'Receita Bruta e Acréscimos'
                            }));
                            $('#est_setembro').append($('<option>', {
                                value: "B",
                                text: 'Balanço/ Balancete de Suspensão/ Redução'
                            }));
                        } 
                        
                    });
                    
                    
                    $('#4o_trim').on('change', function(){
                        quarto_trim                = $('#4o_trim').val();
                        
                        if(quarto_trim != "R" && quarto_trim != "E"){
                            $("#est_outubro option").remove();

                            $('#est_outubro').append($('<option>', {
                                value: "0",
                                text: 'Não Aplicável'
                            }));

                            $("#est_novembro option").remove();

                            $('#est_novembro').append($('<option>', {
                                value: "0",
                                text: 'Não Aplicável'
                            }));

                            $("#est_dezembro option").remove();

                            $('#est_dezembro').append($('<option>', {
                                value: "0",
                                text: 'Não Aplicável'
                            }));
                        }else{
                            $("#est_outubro option").remove();

                            $('#est_outubro').append($('<option>', {
                                value: "0",
                                text: '-- Selecione --'
                            }));
                            $('#est_outubro').append($('<option>', {
                                value: "E",
                                text: 'Receita Bruta e Acréscimos'
                            }));
                            $('#est_outubro').append($('<option>', {
                                value: "B",
                                text: 'Balanço/ Balancete de Suspensão/ Redução'
                            }));
                            
                            $("#est_novembro option").remove();

                            $('#est_novembro').append($('<option>', {
                                value: "0",
                                text: '-- Selecione --'
                            }));
                            $('#est_novembro').append($('<option>', {
                                value: "E",
                                text: 'Receita Bruta e Acréscimos'
                            }));
                            $('#est_novembro').append($('<option>', {
                                value: "B",
                                text: 'Balanço/ Balancete de Suspensão/ Redução'
                            }));
                            
                            $("#est_dezembro option").remove();

                            $('#est_dezembro').append($('<option>', {
                                value: "0",
                                text: '-- Selecione --'
                            }));
                            $('#est_dezembro').append($('<option>', {
                                value: "E",
                                text: 'Receita Bruta e Acréscimos'
                            }));
                            $('#est_dezembro').append($('<option>', {
                                value: "B",
                                text: 'Balanço/ Balancete de Suspensão/ Redução'
                            }));
                        } 
                        
                    });
                    
                               
                
                // Período de apuração do IRPJ e CSLL
                $('#forma_trib_lucro').on('change', function(){
                    var valor_check = $('#forma_trib_lucro').val();
                    
                     if(valor_check == 1 || valor_check == 2 ||valor_check == 6 ||valor_check == 8 ||valor_check == 9 ){
                         $('#ind_rec_receita').prop('disabled', 'disabled');
                     }else{
                         $('#ind_rec_receita').removeAttr('disabled');
                     }
                    
                                     
                     if(valor_check == 8 || valor_check == 9){
                         
                         
                         //REGRA_ NAO_PREENCHER_IMUNE   
                        $('#qual_pessoa_juridica').prop('disabled', 'disabled');
                        
                        $('#tipo_ent_imune_isenta').removeAttr('disabled');
                        $('#exist_ativ_trib').removeAttr('disabled');
                        $('#apuracao_csll').removeAttr('disabled');
                        $('#per_apuracao_irpj').prop('disabled', 'disabled');                         
                        $('#1o_trim').prop('disabled', 'disabled');
                        $('#2o_trim').prop('disabled', 'disabled');
                        $('#3o_trim').prop('disabled', 'disabled');
                        $('#4o_trim').prop('disabled', 'disabled');
                        
                       $("#est_janeiro").prop('disabled', 'disabled');
                       $("#est_fevereiro").prop('disabled', 'disabled');
                       $("#est_marco").prop('disabled', 'disabled');
                       $("#est_abril").prop('disabled', 'disabled');
                       $("#est_maio").prop('disabled', 'disabled');
                       $("#est_junho").prop('disabled', 'disabled');
                       $("#est_julho").prop('disabled', 'disabled');
                       $("#est_agosto").prop('disabled', 'disabled');
                       $("#est_setembro").prop('disabled', 'disabled');
                       $("#est_outubro").prop('disabled', 'disabled');
                       $("#est_novembro").prop('disabled', 'disabled');
                       $("#est_dezembro").prop('disabled', 'disabled');
                         
                     }else{
                         //REGRA_ NAO_PREENCHER_IMUNE   
                        $('#qual_pessoa_juridica').removeAttr('disabled');
                        
                        $('#tipo_ent_imune_isenta').prop('disabled', 'disabled');
                        $('#exist_ativ_trib').prop('disabled', 'disabled');
                        $('#apuracao_csll').prop('disabled', 'disabled');
                        $('#per_apuracao_irpj').removeAttr('disabled');                         
                        $('#1o_trim').removeAttr('disabled');
                        $('#2o_trim').removeAttr('disabled');
                        $('#3o_trim').removeAttr('disabled');
                        $('#4o_trim').removeAttr('disabled');
                         
                        if( $("#per_apuracao_irpj") == "A" ){
                            $("#est_janeiro").removeAttr('disabled');
                            $("#est_fevereiro").removeAttr('disabled');
                            $("#est_marco").removeAttr('disabled');
                            $("#est_abril").removeAttr('disabled');
                            $("#est_maio").removeAttr('disabled');
                            $("#est_junho").removeAttr('disabled');
                            $("#est_julho").removeAttr('disabled');
                            $("#est_agosto").removeAttr('disabled');
                            $("#est_setembro").removeAttr('disabled');
                            $("#est_outubro").removeAttr('disabled');
                            $("#est_novembro").removeAttr('disabled');
                            $("#est_dezembro").removeAttr('disabled');
                        }
                     }
                     
                     if(valor_check == 3 || valor_check == 4 || valor_check == 5 || valor_check == 7 || valor_check == 8 || valor_check == 9){
                         
                        $('#tipo_escrituracao_ecf').removeAttr('disabled');
                     }else{
                        
                        $('#tipo_escrituracao_ecf').prop('disabled', 'disabled');
                     }
                     
                     
                     if(valor_check == 1 || valor_check == 2 || valor_check == 6){
                         $("#qual_pessoa_juridica option").remove();
                         
                        $('#qual_pessoa_juridica').append($('<option>', {
                            value: "01",
                            text: 'PJ em Geral'
                        }));
                        
                        $('#qual_pessoa_juridica').append($('<option>', {
                            value: "02",
                            text: 'PJ Componente do Sistema Financeiro'
                        }));
                        $('#qual_pessoa_juridica').append($('<option>', {
                            value: "03",
                            text: 'Sociedades Seguradoras, de Capitalização ou Entidade Aberta de Previdência Complementar'
                        }));                 
                         
                     //REGRA_COD_QUALIF_PJ
                     }else if(valor_check == 3 || valor_check == 4 || valor_check == 5 || valor_check == 7){
                        $("#qual_pessoa_juridica option").remove();
                         
                        $('#qual_pessoa_juridica').append($('<option>', {
                            value: "01",
                            text: 'PJ em Geral'
                        }));
                         
                         
                         $('#qual_pessoa_juridica').val('01');
                                                
                     }else{
                        $("#qual_pessoa_juridica option").remove();
                         
                        $('#qual_pessoa_juridica').append($('<option>', {
                            value: "",
                            text: '--- Selecione ---- '
                        }));
                         
                        $('#qual_pessoa_juridica').append($('<option>', {
                            value: "01",
                            text: 'PJ em Geral'
                        }));
                        
                        $('#qual_pessoa_juridica').append($('<option>', {
                            value: "02",
                            text: 'PJ Componente do Sistema Financeiro'
                        }));
                        $('#qual_pessoa_juridica').append($('<option>', {
                            value: "03",
                            text: 'Sociedades Seguradoras, de Capitalização ou Entidade Aberta de Previdência Complementar'
                        }));
                     }
                     
                                         
                     
                     //SELECTS DOS TRIMESTRES
                                      
                     //Lucro Real
                     if(valor_check == 1){
                        $(".trimestres option").remove();
                         
                        $('.trimestres').append($('<option>', {
                            value: "0",
                            text: 'Não Informado'
                        }));
                        $('.trimestres').append($('<option>', {
                            value: "R",
                            text: 'Real'
                        }));
                     } 
                     
                     //Lucro Real/ Arbitrado 
                     if(valor_check == 2){
                        $(".trimestres option").remove();
                         
                        $('.trimestres').append($('<option>', {
                            value: "0",
                            text: 'Não Informado'
                        }));
                        $('.trimestres').append($('<option>', {
                            value: "R",
                            text: 'Real'
                        }));
                        $('.trimestres').append($('<option>', {
                            value: "A",
                            text: 'Arbitrado'
                        }));
                     }
                     
                     // Real/ Presumido com opt_refis desativado
                     if(valor_check == 3 && $('#opt_refis').is(":checked") == false){                         
                         $(".trimestres option").remove();
                         
                        $('.trimestres').append($('<option>', {
                            value: "0",
                            text: 'Não Informado'
                        }));
                        $('.trimestres').append($('<option>', {
                            value: "R",
                            text: 'Real'
                        }));
                        $('.trimestres').append($('<option>', {
                            value: "P",
                            text: 'Presumido'
                        }));
                     }
                     
                     // Real/ Presumido com opt_refis desativado e apuração = Anual
                     if((valor_check == 3) && ($('#opt_refis').is(":checked") == true) && ($("#per_apuracao_irpj").val() == "A")){                         
                         $(".trimestres option").remove();
                         
                        $('.trimestres').append($('<option>', {
                            value: "0",
                            text: 'Não Informado'
                        }));
                        $('.trimestres').append($('<option>', {
                            value: "E",
                            text: 'Estimado'
                        }));
                        $('.trimestres').append($('<option>', {
                            value: "P",
                            text: 'Presumido'
                        }));
                     }
                     
                     // Real/ Presumido com opt_refis desativado e apuração =Trimestral
                     if((valor_check == 3) && ($('#opt_refis').is(":checked") == true) && ($("#per_apuracao_irpj").val() == "T")){                         
                         $(".trimestres option").remove();
                         
                        $('.trimestres').append($('<option>', {
                            value: "0",
                            text: 'Não Informado'
                        }));
                        $('.trimestres').append($('<option>', {
                            value: "R",
                            text: 'Real'
                        }));
                        $('.trimestres').append($('<option>', {
                            value: "P",
                            text: 'Presumido'
                        }));
                     }
                     
                     // Real/ Presumido/ arbitrado com opt_refis desativado 
                     if((valor_check == 4) && ($('#opt_refis').is(":checked") == false)){                         
                         $(".trimestres option").remove();
                         
                        $('.trimestres').append($('<option>', {
                            value: "0",
                            text: 'Não Informado'
                        }));
                        $('.trimestres').append($('<option>', {
                            value: "R",
                            text: 'Real'
                        }));
                        $('.trimestres').append($('<option>', {
                            value: "P",
                            text: 'Presumido'
                        }));
                        $('.trimestres').append($('<option>', {
                            value: "A",
                            text: 'Arbitrado'
                        }));
                     }
                     
                     
                     // Real/ Presumido/ arbitrado com opt_refis ativado e apuração anual 
                     if((valor_check == 4) && ($('#opt_refis').is(":checked") == true) && ($("#per_apuracao_irpj").val() == "A") ){                         
                         $(".trimestres option").remove();
                         
                        $('.trimestres').append($('<option>', {
                            value: "0",
                            text: 'Não Informado'
                        }));
                        $('.trimestres').append($('<option>', {
                            value: "E",
                            text: 'Estimado'
                        }));
                        $('.trimestres').append($('<option>', {
                            value: "P",
                            text: 'Presumido'
                        }));
                        $('.trimestres').append($('<option>', {
                            value: "A",
                            text: 'Arbitrado'
                        }));
                     }
                     
                     // Real/ Presumido/ arbitrado com opt_refis ativado e apuração trimestral 
                     if((valor_check == 4) && ($('#opt_refis').is(":checked") == true) && ($("#per_apuracao_irpj").val() == "T") ){                         
                         $(".trimestres option").remove();
                         
                        $('.trimestres').append($('<option>', {
                            value: "0",
                            text: 'Não Informado'
                        }));
                        $('.trimestres').append($('<option>', {
                            value: "R",
                            text: 'Real'
                        }));
                        $('.trimestres').append($('<option>', {
                            value: "P",
                            text: 'Presumido'
                        }));
                        $('.trimestres').append($('<option>', {
                            value: "A",
                            text: 'Arbitrado'
                        }));
                     }
                     
                     // Presumido 
                     if(valor_check == 5){                         
                         $(".trimestres option").remove();                         
                        
                        $('.trimestres').append($('<option>', {
                            value: "0",
                            text: 'Não Informado'
                        }));
                        
                        $('.trimestres').append($('<option>', {
                            value: "P",
                            text: 'Presumido'
                        }));                        
                     }
                     
                     // Arbitrado 
                     if(valor_check == 6){                         
                         $(".trimestres option").remove();                         
                        
                        $('.trimestres').append($('<option>', {
                            value: "0",
                            text: 'Não Informado'
                        }));
                        
                        $('.trimestres').append($('<option>', {
                            value: "A",
                            text: 'Arbitrado'
                        }));                        
                     }
                     
                     // Arbitrado 
                     if(valor_check == 7){                         
                         $(".trimestres option").remove();                         
                        
                        $('.trimestres').append($('<option>', {
                            value: "0",
                            text: 'Não Informado'
                        }));
                        
                        $('.trimestres').append($('<option>', {
                            value: "A",
                            text: 'Arbitrado'
                        }));          
                        $('.trimestres').append($('<option>', {
                            value: "P",
                            text: 'Presumido'
                        }));  
                     }
                     
                                          
                });
                
                $('#repes_recap_etc').on('click', function(){
                    $('.tipos_de_programa').toggle();
                });
                
                
                 $('#existe_nire').on('click', function(){
                     var valor_check = $('#existe_nire').prop('checked');
                     if(valor_check == true){
                         $('#nire').removeAttr('disabled');
                     }else{
                         $('#nire').prop('disabled', 'disabled');
                     }
                });
                
                $('.indicador_finalidade').on('click', function(){
                     var valor_check = $(this).val();                     
                     if(valor_check == 1){
                         $('#hash').removeAttr('disabled');
                         $('#nire_subst').removeAttr('disabled');
                     }else{
                         $('#nire_subst').prop('disabled', 'disabled');
                         $('#hash').prop('disabled', 'disabled');
                     }
                });
                
                
                // Regras de validação
                $("#form-sped-contabil-ecd").submit(function( event ) {  
                    
                    data_inicial          = $("#dta_ini").val().split('/').reverse().join('-');
                    data_final            = $("#dta_fin").val().split('/').reverse().join('-');
                    data_arq_atos         = $("#dt_arq_atos").val().split('/').reverse().join('-');
                    dt_ato_conv           = $("#dt_ato_conv").val().split('/').reverse().join('-');
                    
                    data_ini              = new Date(data_inicial);
                    data_fin              = new Date(data_final);  
                    data_arq_atos         = new Date(data_arq_atos); 
                    dt_ato_conv           = new Date(dt_ato_conv); 
                                       
                    data_arq_atos         = data_arq_atos.getTime();
                    data_ini              = data_ini.getTime();
                    data_fin              = data_fin.getTime();
                    dt_ato_conv           = dt_ato_conv.getTime();
                    
                    dia_ini               = $("#dta_ini").val().substr(0,2);
                    ano_ini               = $("#dta_ini").val().substr(6,4);
                    dia_fin               = $("#dta_fin").val().substr(0,2);
                    ano_fin               = $("#dta_fin").val().substr(6,4);
                    mes_fin               = $("#dta_fin").val().substr(3,2);
                    lastDay               = new Date(ano_fin, mes_fin, 0);
                    ulimo_dia_mes         = lastDay.getDate();
                    
                    //REGRA_DATA_INI_MAIOR
                    if ( data_ini > data_fin){
                        alert('Data Inicial do Exercício não pode ser maior que a Data Final');
                        event.preventDefault();                        
                    }
                    
                    //REGRA_INICIO_PERIODO
                    if (($('.indicador_situacao_inicio_periodo:checked').val() == 0) && dia_ini != '01' ){                  
                        alert('O campo Data Inicial do Exercício deve corresponder ao primeiro dia do mês se o Indicador de Situação no Início do Período for igual a NORMAL');
                        event.preventDefault();
                    }
                    
                    //REGRA_FIM_PERIODO
                    if((typeof $('.indicador_situacao_especial:checked').val() == 'undefined') && (dia_fin !=  ulimo_dia_mes)){ 
                        alert('Sem Indicador de Situação Especial definido, a Data Final do Exercício deve ser o último dia do mês');
                        event.preventDefault();
                    }
                    
                    //REGRA_PERIODO_MAXIMO_ESCRITURACAO
                    if(ano_fin != ano_ini){
                        alert('As datas de Início e Fim de Exercício precisam estar no mesmo ano');
                        event.preventDefault();
                    }
                    
                    //REGRA_DATA_INI_MAIOR_ADV
                    if(data_arq_atos > data_fin){
                        alert('A Data de Arquivamentos dos Atos Constitutivos deve ser menor ou igual à Data Final do Exercício');
                        event.preventDefault();
                    }
                    
                    //REGRA_DATA_INI_MAIOR
                    if(dt_ato_conv > data_fin){
                        alert('A Data de Arquivamento do Ato de Conversão de Sociedade Simples em Sociedade Empresária deve ser menor ou igual à Data Final do Exercício');
                        event.preventDefault();
                    }
                    
                    if($("#dt_arq_atos").val() == "" && $("#dt_ato_conv").val() == ""){
                        alert("Data de Arquivamentos dos Atos Constitutivos OU Data de Arquivamento do Ato de Conversão de Sociedade Simples em Sociedade Empresária deverá ser preenchido");
                        event.preventDefault();
                    }
                    
                });
                
                $("#form_ecf").submit(function(event){ 
                    
                    data_inicial_ecf         = $("#dta_ini_ecf").val().split('/').reverse().join('-');                    
                    data_final_ecf           = $("#dta_fin_ecf").val().split('/').reverse().join('-');
                    data_ini_ecf             = new Date(data_inicial_ecf );
                    data_fin_ecf             = new Date(data_final_ecf );  
                    data_ini_ecf             = data_ini_ecf.getTime();
                    data_fin_ecf             = data_fin_ecf.getTime();
                    dia_ini_ecf              = $("#dta_ini_ecf").val().substr(0,2);  
                    inicio_periodo           = $('.indicador_situacao_inicio_periodo_ecf:checked').val();
                    ind_sit_special          = $('.indicador_situacao_especial_ecf:checked').val();
                                        
//                    REGRA_INICIO_PERIODO
                    if ((inicio_periodo == 0) && (dia_ini_ecf != '01') ){
                        alert('O campo Data Inicial do Exercício deve corresponder ao primeiro dia do mês se o Indicador de Situação no Início do Período for igual a NORMAL');
                        event.preventDefault();
                    }
                    
                    //REGRA_DATA_INI_MAIOR
                    if ( data_ini_ecf  > data_fin_ecf ){
                        alert('Data Inicial do Exercício não pode ser maior que a Data Final');
                        event.preventDefault();
                    }
                    
                    //Regra DT_SITUACAO_ESPECIAL = FINAL_DA_ESCRITURACAO
                    if(($('.indicador_situacao_especial_ecf:checked').val() == 1 || $('.indicador_situacao_especial_ecf:checked').val() == 2 || $('.indicador_situacao_especial_ecf:checked').val() == 3 || $('.indicador_situacao_especial_ecf:checked').val() == 4 || $('.indicador_situacao_especial_ecf:checked').val() == 5 || $('.indicador_situacao_especial_ecf:checked').val() == 6) && ($("#dta_esp").val() != $("#dta_fin_ecf").val())){
                        alert('Data Final do Exercício Deve ser igual à Data da Situação Especial');
                        event.preventDefault();
                    }                    
                    
                    //REGRA_FORMA_APUR_VALIDA
                    verifica_refis = $("#opt_refis").is(":checked");                    
                    if($("#per_apuracao_irpj").val() == 'A' && verifica_refis == false){     
                        alert('Indicador de optante pelo Refis deve ser marcado caso Período de Apuração seja Anual.');
                        event.preventDefault();                        
                    }
                    
                    //REGRA_FORM_TRIB_FORA_PERIODO 
                    //primeiro trimestre
                    primeiro_trimestre = new Date('2016-04-01');
                    primeiro_trimestre = primeiro_trimestre.getTime();                     
                    if(data_ini_ecf > primeiro_trimestre && $('#1o_trim').val() != '0'){
                        alert('O 1º trimestre não deverá ser preenchido, pois não está contido no período do exercício informado');
                        event.preventDefault();  
                    } 
                    
                    //segundo trimestre
                    segundo_trimestre = new Date('2016-07-01');
                    segundo_trimestre = segundo_trimestre.getTime();
                    if(data_ini_ecf > segundo_trimestre && $('#2o_trim').val() != '0'){
                        alert('O 2º trimestre não deverá ser preenchido, pois não está contido no período do exercício informado');
                        event.preventDefault();  
                    } 
                    
                    //terceiro trimestre
                    terceiro_trimestre = new Date('2016-10-01');
                    terceiro_trimestre = terceiro_trimestre.getTime();
                    if(data_ini_ecf > terceiro_trimestre && $('#3o_trim').val() != '0'){
                        alert('O 3º trimestre não deverá ser preenchido, pois não está contido no período do exercício informado');
                        event.preventDefault();  
                    } 
                    
                    //REGRA_MES_BAL_RED _FORA_PERIODO
                    janeiro = new Date('2016-02-01');
                    janeiro = janeiro.getTime();
                    fevereiro = new Date('2016-03-01');
                    fevereiro = fevereiro.getTime();
                    marco = new Date('2016-04-01');
                    marco = marco.getTime();
                    abril = new Date('2016-05-01');
                    abril = abril.getTime();
                    maio = new Date('2016-06-01');
                    maio = maio.getTime();
                    junho = new Date('2016-07-01');
                    junho = junho.getTime();
                    julho = new Date('2016-08-01');
                    julho = julho.getTime();
                    agosto = new Date('2016-09-01');
                    agosto = agosto.getTime();
                    setembro = new Date('2016-10-01');
                    setembro = setembro.getTime();
                    outubro = new Date('2016-11-01');
                    outubro = outubro.getTime();
                    novembro = new Date('2016-12-01');
                    novembro = novembro.getTime();
                    
                    //janeiro
                    if(data_ini_ecf > janeiro && $("#est_janeiro").val() != 0 ){
                        alert('O mês de Janeiro está fora do período da escrituração, nao deve ser informado');
                        event.preventDefault();  
                    }
                    
                    //fevereiro
                    if(data_ini_ecf > fevereiro && $("#est_fevereiro").val() != 0 ){
                        alert('O mês de Fevereiro está fora do período da escrituração, nao deve ser informado');
                        event.preventDefault();  
                    }
                    
                    //marco
                    if(data_ini_ecf > marco && $("#est_marco").val() != 0 ){
                        alert('O mês de Março está fora do período da escrituração, nao deve ser informado');
                        event.preventDefault();  
                    }
                    
                    //abril
                    if(data_ini_ecf > abril && $("#est_abril").val() != 0 ){
                        alert('O mês de Abril está fora do período da escrituração, nao deve ser informado');
                        event.preventDefault();  
                    }
                    
                    //maio
                    if(data_ini_ecf > maio && $("#est_maio").val() != 0 ){
                        alert('O mês de Maio está fora do período da escrituração, nao deve ser informado');
                        event.preventDefault();  
                    }
                    
                    //junho
                    if(data_ini_ecf > junho && $("#est_junho").val() != 0 ){
                        alert('O mês de Junho está fora do período da escrituração, nao deve ser informado');
                        event.preventDefault();  
                    }
                    
                    //julho
                    if(data_ini_ecf > julho && $("#est_julho").val() != 0 ){
                        alert('O mês de Julho está fora do período da escrituração, nao deve ser informado');
                        event.preventDefault();  
                    }
                    
                    //agosto
                    if(data_ini_ecf > agosto && $("#est_agosto").val() != 0 ){
                        alert('O mês de Agosto está fora do período da escrituração, nao deve ser informado');
                        event.preventDefault();  
                    }
                    
                    //setembro
                    if(data_ini_ecf > setembro && $("#est_setembro").val() != 0 ){
                        alert('O mês de Setembro está fora do período da escrituração, nao deve ser informado');
                        event.preventDefault();  
                    }
                    
                    //outubro
                    if(data_ini_ecf > outubro && $("#est_outubro").val() != 0 ){
                        alert('O mês de Outubro está fora do período da escrituração, nao deve ser informado');
                        event.preventDefault();  
                    }
                    
                    //novembro
                    if(data_ini_ecf > novembro && $("#est_novembro").val() != 0 ){
                        alert('O mês de Novembro está fora do período da escrituração, nao deve ser informado');
                        event.preventDefault();  
                    }
                    
//                    //REGRA_APUR_IGUAL
//                    if( $("#apuracao_csll").val() != "D" && $("#apuracao_csll").val() != $("#exist_ativ_trib").val() ){
//                        alert('Quando valor de Apuração da CSLL for diferente de "Desobrigado", este deverá ter valor igual a Apuração do IRPJ para Imunes ou Isentas. ');
//                        event.preventDefault();
//                    }
                });
            });
            
            function mascaraMutuario(o,f){
                    n_obj=o
                    n_fun=f
                    setTimeout('execmascara()',1)
                }
                
                function execmascara(){
                    n_obj.value=n_fun(n_obj.value)
                }
 
                function cpfCnpj(n){
                    //Remove tudo o que não é dígito
                    n=n.replace(/\D/g,"")
                    if (n.length <= 11) { //CPF
                        //Coloca um ponto entre o terceiro e o quarto dígitos
                        n=n.replace(/(\d{3})(\d)/,"$1.$2")
                        //Coloca um ponto entre o terceiro e o quarto dígitos
                        ////de novo (para o segundo bloco de números)
                        n=n.replace(/(\d{3})(\d)/,"$1.$2")
                        //Coloca um hífen entre o terceiro e o quarto dígitos
                        n=n.replace(/(\d{3})(\d{1,2})$/,"$1-$2")
                    } else { //CNPJ
                        //Coloca ponto entre o segundo e o terceiro dígitos
                        n=n.replace(/^(\d{2})(\d)/,"$1.$2")
                        //Coloca ponto entre o quinto e o sexto dígitos
                        n=n.replace(/^(\d{2})\.(\d{3})(\d)/,"$1.$2.$3")
                        //Coloca uma barra entre o oitavo e o nono dígitos
                        n=n.replace(/\.(\d{3})(\d)/,".$1/$2")
                        //Coloca um hífen depois do bloco de quatro dígitos
                        n=n.replace(/(\d{4})(\d)/,"$1-$2")
                    }
                    return n
                }

        </script>
    </body>
</html>
