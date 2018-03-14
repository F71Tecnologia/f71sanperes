<?php
/*
 * relatorio_rescisao_2.php
 * 
 * 24-11-2015
 * 
 * Relatório de rescisões por período
 * 
 * Obs: Relatório desenvolvido para a contabilidade (Milton)
 * 
 * Versão: 3.0.1829 - 28/08/2015 - Jacques - Versão Inicial
 * Versão: 3.0.5313 - 05/01/2016 - Jacques - Adição de carga de variável com id_folha e execução de método específico 
 * 
 * @author jacques@f71.com.br
 * 
 * Calculo que deve ser gerado para o Milton
 * C:\projetos\lagos\public_html\intranet\prestacoes\plano_contas\PlanoContas.class.php -> prosoftfolhaPagamento
 * 
 * http://f71lagos.com/intranet/rh/folha/relatorio_rescisao.php
 */

include('../../conn.php');  
include('../../funcoes.php');
include('../../classes/global.php');
include('../../classes/DateClass.php');
include('../../wfunction.php');
include('../../classes_permissoes/regioes.class.php');
include('../../classes_permissoes/acoes.class.php');
include('../../classes/funcionario.php');
include('../../classes/webClass.php');
include("../../classes/RhClass.php");

/*
 * PHP-DOC - Classe para controle da geração de relatório de rescisão
 */

class webRelatorioRescisaoClass extends webClass {
    
    private $rh;
    private $date;
    private $user;
    private $funcionario;

    protected function setUser($value) {
        
        $this->user = $value;
        
    }
    
    protected function setBreadCrumb(){
        
        $usuario = carregaUsuario();
        
        $this->setTitle(':: Intranet :: Férias');
                        
        $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); 
        
        $breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Relatório de Rescisões");

        $breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php");
        
        include("../../template/navbar_default.php"); 

        
    }
    /*
     * PHP-DOC - Carrega os Css responsáveis pelo layout da página
     */
    protected function setCssExt(){
        ?>
                
                <style>
                    .data{
                        cursor: default;
                    }

                    .credito {
                        color: blue;
                    }

                    .debito {
                        color: red;
                    }
                    
                    .sub-total {
                        color: green;
                    }
                    
                    .panel-ferias {
                        width: 80%;
                    }
                    
                    .table-ferias {
                        margin: 20px 20px 40px 20px;
                        width: 95%;
                    }
                    
                    .table-salario-variavel {
                        margin: 2px 2px 2px 6px;
                    }
                    
                    .space-padding-form-group {
                        padding: 15px 0px 0px 0px;
                    }
                    
                    .diagonal {
                        z-index: 999;
                        -webkit-transform: rotate(330deg);
                        -moz-transform: rotate(330deg);
                        -o-transform: rotate(330deg);
                        writing-mode: lr-tb;
                      }   
                </style>
        <?php
    }
    /*
     * PHP-DOC - Carrega os JavaScripts já utilizados na página
     */    
    protected function setJavaScriptExtFooter(){
        ?>      <script>
                    $(function () {
                        
                        $('.date').datepicker({
                            format: "mm/yyyy",
                            startView: "months", 
                            minViewMode: "months",                            
                            viewMode: 'years',
                            today: "Today",
                            clear: "Clear",
                            titleFormat: "MM yyyy", 
                            language: "pt-BR",
                            todayBtn: "linked",
                            todayHighlight: true,
                            calendarWeeks: false,
                            weekStart: 0,
                            autoclose: true,
                            yearRange: '2005:c+1',
                            startDate: "",
                            endDate: "",                            
                            changeMonth: true,
                            changeYear: true
                        }); 
                        
                        $("#data_ini_fmt").change(function () {
                            
                            $('#data_fim_fmt').val($("#data_ini_fmt").val());
                            
                        });                        
                        

                        var id_destination = "projeto";

                        $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function(data) {
                            
                            removeLoading();
                            
                            $("#" + id_destination).html(data);
                            
                            var selected = $("input[name=hide_" + id_destination + "]").val();
                            
                            if (selected !== undefined) {
                                
                                $("#" + id_destination).val(selected);
                                
                            }
                            
                            $('#projeto').trigger('change');
                            
                        }, "projeto");
                        
                        
                        $('#projeto_rel').change(function () {
                            
                            $.post("/intranet/methods.php", {proj: [$("#projeto_rel").val()], tipo: 2, method: 'carregaCargos'}, function (resultado) {
                                
                                $("#funcao").html(resultado);
                                
                            });
                            
                        });       
                        
                        $('#btn_consultar').click(function () {
                        
                            var projeto = $("#projeto").val();
                            var data_ini_fmt = $("#data_ini_fmt").val();
                            var data_fim_fmt = $("#data_fim_fmt").val();
                            
                            var data_ini_obj_str = new String(data_ini_fmt);
                            var data_fim_obj_str = new String(data_fim_fmt);
                            
                            var data_ini_str = data_ini_obj_str.replace("/","").replace("/","");
                            var data_fim_str = data_fim_obj_str.replace("/","").replace("/","");
                            
                            var mes_ini = data_ini_str.substr(0, 2);
                            var ano_ini = data_ini_str.substr(2, 4);
                            
                            var mes_fim = data_fim_str.substr(0, 2);
                            var ano_fim = data_fim_str.substr(2, 4);
                            
                            var data_ini = ano_ini+'-'+mes_ini;
                            var data_fim = ano_fim+'-'+mes_fim;
                            
                            $("#projeto:empty").css( "background", "rgb(255,220,200)" );        
                      
                            if($("#projeto").val()=='' || $("#data_ini_fmt").val()=='' || $("#data_fim_fmt").val()=='') {
                                
                                alert('Necessário selecionar um projeto com intervalo de data');
                                
                                return;
                                
                            }
                            
                            console.log(projeto);
                            console.log(data_ini);
                            console.log(data_fim);
                            
                            var url = 'relatorio_rescisao_2.php';
                            
                            $("#relatorio").html("<p class=\"text-center\"><img src=\"/intranet/imagens/loading2.gif\" style=\"width:50px; height: 50px;\"></p>");

                            $.post(
                                    url,
                                    {
                                      method: 'telaRelatorio',
                                      projeto: projeto,
                                      data_ini: data_ini,
                                      data_fim: data_fim
                                    },
                                    function(data){

                                        $("#relatorio").html(data);

                            });
                        });                        
                        
                
                    });
                </script>
        <?php
        
    }
    
    protected function getUser($valor){
        
        return $this->user[$valor];
        
    }

    /*
     * PHP-DOC - Ação a ser executada pela classe
     */
    public function action(){

        header ('Content-type: text/html; charset=ISO-8859-1');            
        
        $this->setUser(carregaUsuario()); 
        $this->setBuild('7849');
        $this->setPageTitle('<h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Relatório de Rescisões</small></h2>');

        $this->funcionario = new funcionario();
        $this->date = new DateClass();
        $this->rh = new RhClass();
        
        /* 
         * carrega variáveis passadas por POST ou GET$usuario['id_regiao']
         */
        $id_regiao =  isset($_REQUEST['regiao']) ? $_REQUEST['regiao'] : isset($_REQUEST['id_folha']) ? 0 : $this->funcionario->id_regiao; 
        $id_projeto =  isset($_REQUEST['projeto']) && $_REQUEST['projeto'] > 0 ? $_REQUEST['projeto'] : 0;
        $id_folha =  isset($_REQUEST['id_folha']) ? $_REQUEST['id_folha'] : 0;
        $data_ini = isset($_REQUEST['data_ini']) ? $_REQUEST['data_ini'] : '';
        $data_fim = isset($_REQUEST['data_fim']) ? $_REQUEST['data_fim'] : '';
        $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : ''; 
        
        $this->funcionario->MostraUser(0);
        $funcionario = $this->funcionario->id_funcionario;      
        
        /*
         * Instância as classes do framework
         * 
         * Obs: É importante utilizar o instâncimanto na ordem em que vão acontecer 
         *      a seleção de dados para se poder fazer uso da Macro $this->rh->select() e $this->rh->getRow()
         */
        $this->rh->AddClassExt('Folha');
        $this->rh->AddClassExt('FolhaProc');
        $this->rh->AddClassExt('Rescisao');
        $this->rh->AddClassExt('Movimentos');
        $this->rh->AddClassExt('MovimentosRescisao');
        $this->rh->AddClassExt('Projeto');
        
        /*
         * Macro do Framework para setar valores padrões em todas as classes instânciadas 
         */
        $this->rh->setDefault(); 
        
        $this->rh->Folha->setIdFolha($id_folha);
        $this->rh->Folha->setRegiao($id_regiao);
        $this->rh->Folha->setProjeto($id_projeto);
        $this->rh->Folha->setDateRangeIni($data_ini);
        $this->rh->Folha->setDateRangeFim($data_fim);
        $this->rh->Folha->setDateRangeField('CONCAT(ano,mes)'); 
        $this->rh->Folha->setDateRangeFmt('Ym');

        $this->setMethodExt($method); 
        
        if(empty($this->getMethodExt())){
            
            if(empty($id_folha)) {

                $this->showPage('telaFiltro');
                
            }
            else {
                
                $this->showPage('telaRelatorio');
                
            }
            
        }
        else {
            
            $this->exeMethodExt();
            
        }
        
        
    }
    
    /*
     * PHP-DOC - Tela 0 - Tela NavSearch (Parámetros de Busca)
     */
    protected function telaFiltro(){
        
        ?>
                
        <div class="container">
            <div class="panel panel-default hidden-print">
                <div class="panel-heading text-bold hidden-print">Filtro</div>
                    <form id="form" method="post" class="form-horizontal">      
                        <fieldset>                        
                            <div class="panel-body">
                                <div class="form-group space-padding-form-group">
                                    <label for="select" class="col-lg-2 control-label">Projeto</label>
                                    <div class="col-lg-9">
                                        <?=montaSelect(getProjetos($this->rh->Folha->getRegiao()), $this->rh->Folha->getProjeto(), array('name' => "projeto", 'id' => 'projeto', 'class' => 'validate[required] form-control'));?>
                                    </div>
                                </div>
                                <div class="form-group space-padding-form-group datas">
                                    <label for="data_ini" class="col-lg-2 control-label">Período</label>
                                    <div class="col-lg-4">
                                        <div class='input-group date' id='data_ini'>
                                            <input type='text' id="data_ini_fmt" class="form-control data" name="data_ini_fmt" readonly="true" data-fv-notempty data-fv-notempty-message="Uma data de início é necessária"/>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>    
                                    <div class="col-lg-1 control-label">
                                        <span>Até</span>
                                    </div>    
                                    <div class="col-lg-4">
                                        <div class='input-group date' id='data_fim'>
                                            <input type='text' id="data_fim_fmt" class="form-control data" name="data_fim_fmt" readonly="true"/>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>    
                    </form>                    
                <div class="panel-footer text-right hidden-print">
                    <input type="button" class="btn btn-success" name="btn_consultar" id="btn_consultar" value="Consultar">
                </div>
            </div>
            <!--<div class="diagonal"><h1>EM PROCESSO DE HOMOLOGAÇÃO - BUILD 7849 BETA</h1></div>-->            
            <div id="relatorio" class="table-responsive">
            </div>
        </div>
                
        <?php
        
        
    }    
    
    /*
     * PHP-DOC - Tela 1 - Tela inicial para geração do relatório de rescisões
     */
    protected function telaRelatorio(){
        
        $collection = $this->rh->Folha->getCollectionRescisoes();

//        print_array($collection['rescisoes']);     
        
//        print_array($collection['movimentos']);
        
        $dominio = $_SERVER['HTTP_HOST'];
         
        ?>
            <p style="text-align: right; margin-top: 20px">
                <button type="button" onclick="tableToExcel('table_relatorio_sintetico', 'Relatório de Rescisões')" class="btn btn-success">
                <span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
            </p>        
                
<!--        <table id="table_relatorio_analitico" class="table table-striped table-hover table-condensed table-bordered text-sm valign-middle"> 
            <thead>
                <tr class="head bg-primary valign-middle">
                    <td align="center">ID.</td>
                    <td align="left">Nome</td>
                    <td align="right">Sal. Base</td>
                    <td align="right">Saldo Sal.</td>
                    <td align="right">INSS SS</td>
                    <td align="right">IR SS</td>
                    <td align="right">TERCEIRO SS</td>
                    <td align="right">13 SAL.</td>
                    <td align="right">INSS 13</td>
                    <td align="right">Tot.Mov.D.</td>
                    <td align="right">Tot.Mov.C.</td>
                </tr>
            </thead>
            <tbody>
                <?php
                
                foreach ($collection['rescisoes']['dados'] as $collection_clt => $clt_itens) {  
                    
                    $this->rh->Rescisao->getRow($clt_itens);

                ?>        
                <tr>
                    <td align="center"><?=$this->rh->Rescisao->getIdClt()?></td>
                    <td align="left"><?=$this->rh->Rescisao->getNome()?></td>
                    <td align="right"><?=$this->rh->Rescisao->getSalBase('R$  |.|,|2')?></td>
                    
                    <td align="right"><?=$this->rh->Rescisao->getSaldoSalario('R$  |.|,|2')?></td>
                    <td align="right"><?=$this->rh->Rescisao->getInssSS('R$  |.|,|2')?></td>
                    <td align="right"><?=$this->rh->Rescisao->getIrSS('R$  |.|,|2')?></td>
                    <td align="right"><?=$this->rh->Rescisao->getTerceiroSs('R$  |.|,|2')?></td>
                    <td align="right"><?=$this->rh->Rescisao->getDtSalario('R$  |.|,|2')?></td>
                    <td align="right"><?=$this->rh->Rescisao->getInssDt('R$  |.|,|2')?></td>
                    <td align="right">R$ <?=number_format($clt_itens['total_movimento_dedito'], 2, ',','.');?></td>
                    <td align="right">R$ <?=number_format($clt_itens['total_movimento_credito'], 2, ',','.');?></td>
                </tr>
                <?php
                }
                ?>        
            </tbody>
        </table>        -->
        <table id="table_relatorio_sintetico" class="table table-striped table-hover table-condensed table-bordered text-sm valign-middle"> 
            <thead>
                <tr class="head bg-primary valign-middle">
                    <td align="center" colspan="3">TOTALIZADORES</td>
                </tr>
                <tr class="head bg-primary valign-middle">
                    <td align="center">NOME</td>
                    <td align="center">PROVENTOS</td>
                    <td align="center">DESCONTOS</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="left">SALDO DE SALÁRIO</td>
                    <td align="right">R$ <?=number_format($collection['rescisoes']['sum']['general']['saldo_salario'], 2, ',','.');?></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td align="left">FÉRIAS PROPORCIONAIS</td>
                    <td align="right">R$ <?=number_format($collection['rescisoes']['sum']['general']['ferias_pr'], 2, ',','.');?></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td align="left">FÉRIAS VENCIDAS</td>
                    <td align="right">R$ <?=number_format($collection['rescisoes']['sum']['general']['ferias_vencidas'], 2, ',','.');?></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td align="left">1/3 CONSTITUCIONAL DE FÉRIAS</td>
                    <td align="right">R$ <?=number_format($collection['rescisoes']['sum']['general']['terco_constitucional'], 2, ',','.');?></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td align="left">13º SALÁRIO</td>
                    <td align="right">R$ <?=number_format($collection['rescisoes']['sum']['general']['dt_salario'], 2, ',','.');?></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td align="left">13º SALÁRIO (AVISO-PRÉVIO INDENIZADO)</td>
                    <td align="right">R$ <?=number_format($collection['rescisoes']['sum']['general']['terceiro_ss'], 2, ',','.');?></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td align="left">AVISO PRÉVIO (EMPRESA)</td>
                    <td align="right">R$ <?=number_format($collection['rescisoes']['sum']['general']['valor_aviso_emp'], 2, ',','.');?></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td align="left">INSALUBRIDADE</td>
                    <td align="right">R$ <?=number_format($collection['rescisoes']['sum']['general']['insalubridade'], 2, ',','.');?></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td align="left">MULTA ART. 477</td>
                    <td align="right">R$ <?=number_format($collection['rescisoes']['sum']['general']['a477'], 2, ',','.');?></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td align="left">MULTA ART. 479/CLT</td>
                    <td align="right">R$ <?=number_format($collection['rescisoes']['sum']['general']['a479'], 2, ',','.');?></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td align="left">AJUSTE DE SALDO DEVEDOR</td>
                    <td align="right">R$ <?=number_format($collection['rescisoes']['sum']['general']['arredondamento_positivo'], 2, ',','.');?></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td align="left">LEI 12.506 (AVISO PRÉVIO)</td>
                    <td align="right">R$ <?=number_format($collection['rescisoes']['sum']['general']['lei_12_506'], 2, ',','.');?></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td align="left">AVISO PRÉVIO (FUNCIONÁRIO)</td>
                    <td align="center"></td>
                    <td align="right">R$ <?=number_format($collection['rescisoes']['sum']['general']['valor_aviso_fun'], 2, ',','.');?></td>
                </tr>
                <tr>
                    <td align="left">PREVIDÊNCIA SOCIAL (INSS SALDO DE SALÁRIO)</td>
                    <td align="center"></td>
                    <td align="right">R$ <?=number_format($collection['rescisoes']['sum']['general']['inss_ss'], 2, ',','.');?></td>
                </tr>
                <tr>
                    <td align="left">PREVIDÊNCIA SOCIAL 13º SALÁRIO (INSS 13º SALÁRIO)</td>
                    <td align="center"></td>
                    <td align="right">R$ <?=number_format($collection['rescisoes']['sum']['general']['inss_dt'], 2, ',','.');?></td>
                </tr>
                <tr>
                    <td align="left">IRRF (SALDO DE SALÁRIO)</td>
                    <td align="center"></td>
                    <td align="right">R$ <?=number_format($collection['rescisoes']['sum']['general']['ir_ss'], 2, ',','.');?></td>
                </tr>
                <tr>
                    <td align="left">IRRF 13º SALÁRIO</td>
                    <td align="center"></td>
                    <td align="right">R$ <?=number_format($collection['rescisoes']['sum']['general']['ir_dt'], 2, ',','.');?></td>
                </tr>
                <tr>
                    <td align="center" colspan="3"><strong>MOVIMENTOS</strong></td>
                </tr>
                <?php
                foreach ($collection['movimentos']['dados']['CREDITO'] as $key => $value) {  
                ?>
                <tr>
                    <td align="left"><?=$value['descicao']?></td>
                    <td align="right">R$ <?=number_format($value['valor'], 2, ',','.');?></td>
                    <td align="right"></td>
                </tr>
                <?php
                }
                ?>
                <?php
                foreach ($collection['movimentos']['dados']['DEBITO'] as $key => $value) {  
                ?>
                <tr>
                    <td align="left"><?=$value['descicao']?></td>
                    <td align="right"></td>
                    <td align="right">R$ <?=number_format($value['valor'], 2, ',','.');?></td>
                </tr>
                <?php
                }
                ?>
            <tbody>    
            <footer>
                <tr class="footer valign-middle">
                    <td align="right">TOTAL</td>
                    <td align="right">R$ <?=number_format($collection['rescisoes']['sum']['general']['total_proventos'], 2, ',','.');?></td>
                    <td align="right">R$ <?=number_format($collection['rescisoes']['sum']['general']['total_descontos'], 2, ',','.');?></td>
                </tr>
            </footer>
        </table>        
        <?php   
        
         
    }    

} // Final da Class webRelatorioRescisao


/*
 * PHP-DOC - Main - Módulo principal de execução da classe webRelatorioRescisao
 */

$webRelatorioRescisao = new webRelatorioRescisaoClass();

$webRelatorioRescisao->action();


