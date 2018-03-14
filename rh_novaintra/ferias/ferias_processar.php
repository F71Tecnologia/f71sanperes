<?php
/*
 * PHP-DOC  
 * 
 * Procedimentos para lançamento de férias
 *  
 * 15-09-2015 
 * 
 * @package RhMovimentosCltClass
 * @access public  
 * 
 * @version
 * 
 * Versão: 3.0.0000 - 15/09/2015 - Jacques - Versão Inicial 
 * Versão: 3.0.4394 - 15/09/2015 - Jacques - Alterado na verificação de parámetro $_REQUEST['regiao'] para quando vazio e maior que zero define $this->funcionario->id_regiao
 * Versão: 3.0.4440 - 15/09/2015 - Jacques - Adicionado método encadeado em data.replace("/", "").replace("/", "");
 * Versão: 3.0.5102 - 18/12/2015 - Jacques - Acertado a impressão do nome da empresa na classe $this->rh->Empresa->getNome() para getRazao(), 
 *                                           Suprimido o $this->rh->date->set()->get('d/m/Y')
 *                                           Acertado o path relativo na geração de $pdf ->Output("arquivos/{$nomearquivo}");    
 * Versão: 3.0.5102 - 04/01/2016 - Jacques - Acrescentado uso de parámetro para o método telaGerarPDF com retorno json ou pdf
 * Versão: 3.0.5475 - 11/01/2016 - Jacques - Definido parámetro de retorno default como JSON em sua geração no método telaGerarPDF
 * Versão: 3.0.5648 - 19/01/2016 - Jacques - Adicionado o método telaAvisoFerias para impressão antes do recibo de férias
 * Versão: 3.0.5648 - 22/01/2016 - Jacques - Liberado e alterado a forma de bloqueio do botão para processamento quando houve ERROR_FRAMEWORK_NOTICE
 * Versão: 3.0.6313 - 11/02/2016 - Jacques - Adicionado ao método gerarPdf a opção de impressão em lote ou individual de acordo com o valor do método setIdFerias
 * Versão: 3.0.6313 - 16/02/2016 - Jacques - Atualização de status do Clt desativada para ser feita pela CRON como combinado com Sinésio já que o fato de se gerar férias 
 *                                           não necessárimante significa que o mesmo encontra-se em período de gozo.
 * Versão: 3.0.0000 - 11/03/2016 - Jacques - Adicionado o parâmetro now para o calendário de início das férias limitando emissão retroativa
 *                                           Inclusão do gerador de evento rh->Clt->onUpdate() que determina alteração nos registros relacionados
 * Versão: 3.0.8708 - 29/03/2016 - Jacques - Adicionado try e catch para o método action
 * Versão: 3.0.8997 - 20/04/2016 - Jacques - Adicionado o chkbox chk_ignorar_ferias_dobradas 
 * Versão: 3.0.9069 - 20/04/2016 - Jacques - Adicionado o chkbox chk_metade_ferias para processamento com férias pela metada e exibição personalizavel 
 * 
 * Obs: Sugestões do Rogério; exibir o número de dependentes para cálculo do IR e listagem do salário variável
 * 
 * @author jacques
 * 
 * @copyright www.f71.com.br
 * 
 */

include('../../conn.php');  
include('../../funcoes.php');
include('../../classes/global.php');
include('../../classes/DateClass.php');
include('../../wfunction.php');
include('../../classes_permissoes/acoes.class.php');
include('../../classes/funcionario.php');
include('../../classes/webClass.php');
include("../../classes/RhClass.php");

/*
 * PHP-DOC - Classe para controle da várias tela de lançamento de férias
 */

class webFeriasClass extends webClass { 
    
    private $rh;
    private $user;
    private $funcionario;
    
    protected function setUser($value) {
        
        $this->user = $value;
        
    }
    
    protected function setBreadCrumb(){
        
        $usuario = carregaUsuario();
        
        $this->setTitle(':: Intranet :: Férias');
                        
        $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); 
        
        $breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Férias");

        $breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php");
        
        include("../../template/navbar_default.php"); 
        
        
    }
    /*
     * PHP-DOC - Carrega os Css responsáveis pelo layout da página
     */
    protected function setCssExt(){
        ?>
                
                <style>
                    #div_form{
                        display: none;
                    }

                    #div_historico{
                        display: none;
                    }
                    
                    #div_lancamento{
                        display: none;
                    }
                    
                    #id_ferias{
                        display: none;
                    }
                    
                    
                    #resumo_ferias {
                        padding: 25px;
                    }
                    
                    #data_ini {
                        cursor: hand;                        
                    }
                    
                    .modal-dialog  {
                        position: relative;
                        overflow-y: auto;
                        width: 80%;
                        padding: 15px;
                    }  
                    
                    .modal_label {
                        width: 75%;
                    }
                    
                    .modal_value {
                        width: 10%;
                    }
                    
                    .modal_status {
                        width: 15%;
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
                    
                    .table-aviso {
                        font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
                        font-size: 12px;
                    }
                    
                    .borda-top-lef-right tr {
                        border:2px solid #000;
                        padding: 5px; 
                        border-collapse: collapse;                            
                        border-right:1px solid #000;
                        border-left:1px solid #000;
                        border-top: 4px solid #000;
                        border-bottom: none; 
                    }                    
                    
                    .borda-simples tr {
                        border:2px solid #000;
                        padding: 5px; 
                        border-collapse: collapse;   
                    }                    
                </style>
        <?php
    }
    /*
     * PHP-DOC - Carrega os JavaScripts da utilizados na página
     */    
    protected function setJavaScriptExtFooter(){
        ?>
                <script>
                    
                    function QuantidaDiasComOuSemAbono(){

                        var data_ini_fmt = $("#data_ini_fmt");
                        var qnt_dias_ferias = $("#qnt_dias_ferias");
                        var qnt_dias_com_abono_pecuniario = $("#qnt_dias_com_abono_pecuniario");
                        var qnt_dias_sem_abono_pecuniario = $("#qnt_dias_sem_abono_pecuniario");
                        var chk_abono_pecuniario = $("#chk_abono_pecuniario");
                        
                        if(chk_abono_pecuniario.is(':checked')) {

                            qnt_dias_ferias.val(qnt_dias_com_abono_pecuniario.val());

                        }
                        else {

                            qnt_dias_ferias.val(qnt_dias_sem_abono_pecuniario.val());

                        }

                    };
                    
                    function chkMetadeFerias(){

                        var chk_metade_ferias = $("#chk_metade_ferias");
                        var qnt_dias_ferias = $("#qnt_dias_ferias");
                        
                        if(chk_metade_ferias.is(':checked')) {

                            qnt_dias_ferias.val(15);

                        }
                        else {
                            
                            qnt_dias_ferias.val(qnt_dias_ferias.val());
                            
                        }

                    };
                    
                    function chkFaltasAbonoPecuniario(){
                        
                        var div_message_erro = $("#message_erro");
                        var div_label_dias_ferias = $("#div_label_dias_ferias");
                        var div_dias_ferias = $("#div_dias_ferias");
                        var div_lancamento = $("#div_lancamento");
                        var btn_calcular_ferias = $("#btn_calcular_ferias");
                        
                        ignorar_faltas = $("#chk_ignorar_faltas").is(':checked') ? 1 : 0;
                        
                        $('.periodos_pendentes').each(function () {
                        
                            if ($(this).is(":checked")) {
                        
                                id_clt = $(this).data("clt");
                                data_aquisitivo_ini = $(this).data("ini");
                                data_aquisitivo_fim = $(this).data("fim");
                                
                            }    
                        
                        });
                        
                        $.ajax({
                            url: "ferias_processar.php",
                            type: "POST",
                            dataType: "json",
                            data: {
                                method: "chkFaltasAbonoPecuniario",
                                id_clt: id_clt,
                                data_aquisitivo_ini: data_aquisitivo_ini,
                                data_aquisitivo_fim: data_aquisitivo_fim,
                                chk_ignorar_faltas: ignorar_faltas
                            },
                            success: function (data) {

                                console.log(data);

                                if (data.status == 1) {

                                    html =  "<label class='control-label'>Quantidade de Dias:</label>";   
                                    div_label_dias_ferias.html(html);

                                    html = "<input type='text' class='form-control' id='qnt_dias_ferias' name='qnt_dias_ferias' value='0' disabled>";
                                    div_dias_ferias.html(html);

                                    html = "<input type='hidden' class='form-control' id='qnt_dias_sem_abono_pecuniario' name='qnt_dias_sem_abono_pecuniario' value='"+data.qnt_dias_sem_abono_pecuniario+"' disabled>";
                                    div_dias_ferias.append(html);

                                    html = "<input type='hidden' class='form-control' id='qnt_dias_com_abono_pecuniario' name='qnt_dias_com_abono_pecuniario' value='"+data.qnt_dias_com_abono_pecuniario+"' disabled>";
                                    div_dias_ferias.append(html);

                                    QuantidaDiasComOuSemAbono();

                                    div_lancamento.show();

//                                    btn_calcular_ferias.prop( "disabled", true);

//                                    data_ini_fmt.val('');

                                }

                            },
                            error: function(data){

                                console.log(data.responseText);
                                
                                html = "<div class='bs-example'><div class='alert alert-danger fade in'><a href='#' class='close' data-dismiss='alert'>&times;</a><strong>Error</strong> em webFerias->chkFaltasAbonoPecuniario in "+data.responseText+"</div></div>";
                                
                                div_message_erro.append(html);
                                
                                div_message_erro.show();
                                
                            }
                        });

                    }
                    
                    $(function () {

                        id_clt = 0;
                        data_aquisitivo_ini = '';
                        data_aquisitivo_fim = '';
                        abono_pecuniario = 0;
                        ignorar_faltas = 0;
                        
                        $("#form").validationEngine();
                        
                        $('#data_ini').datepicker({
                            today: "Today",
                            clear: "Clear",
                            titleFormat: "MM yyyy", 
                            language: "pt-BR",
                            format: "dd/mm/yyyy",
                            todayBtn: "linked",
                            todayHighlight: true,
                            calendarWeeks: false,
                            weekStart: 0,
                            autoclose: true,
                            yearRange: '2005:c+1',
                            startDate: "now",
                            endDate: "",                            
                            changeMonth: true,
                            changeYear: true                            
                        });
                        
                        $("#data_ini").mouseover(function() {
                            
                            $("#data_ini").css("cursor","hand");
                            
                        });                        
                        
                        $("#data_ini").click(function () {
                            
                            $('#data_ini').datepicker('show');
                            
                        });
                        
                        $("#btn_ver_historico").click(function () {
                            
                            var div_historico = $("#div_historico");
                            var btn_ver_historico = $("#btn_ver_historico");
                            
                            if(div_historico.is(':visible')){
                                
                                btn_ver_historico.val('VER HISTÓRICO DE FÉRIAS');
                                div_historico.hide();
                                
                            }
                            else {
                                
                                btn_ver_historico.val('OCULTAR HISTÓRICO DE FÉRIAS');
                                div_historico.show();
                                
                            }
                            
                        });
                        
                        $("#btn_nao_processou").click(function () {
                            
                              <?php
                              $url = "/intranet/rh/rh_movimentos_3.php?tela=2&pg=0&clt={$this->rh->Clt->getIdClt()}&regiao={$this->rh->Clt->getIdRegiao()}&projeto={$this->rh->Clt->getIdProjeto()}"
                              ?>
                                          
                            window.open("<?=$url?>");             
                            
                        });

                        $("#chk_abono_pecuniario").click(function () {
                            
                            QuantidaDiasComOuSemAbono();

                        });
                        
                        $("#chk_metade_ferias").click(function () {
                            
                            QuantidaDiasComOuSemAbono();
                            chkMetadeFerias();

                        });
                        
                        
                        $("#btn_sim_processou").click(function () {
                            
                            $("#div_form").show();
                            
                        });
                        
                        
                        
                        $("#chk_ignorar_faltas").click(function () {
                            
                            chkFaltasAbonoPecuniario();
                            
                        });
                        
                        $(".periodos_pendentes").click(function () {
                            
                            chkFaltasAbonoPecuniario();
                            
                        });
                        
                        $('#btn_calcular_ferias').click(function () {
                        
                            var div_message_erro = $("#message_erro");
                            var data_ini_fmt = $("#data_ini_fmt").val();
                            var data = new String(data_ini_fmt);
                            
                            data = data.replace("/", "").replace("/", "");
                            
                            var dia = data.substr(0, 2);
                            var mes = data.substr(2, 2);
                            var ano = data.substr(4, 4);
                            
                            var data_ini = ano+'-'+mes+'-'+dia;
                            
                            var chk_abono_pecuniario = $("#chk_abono_pecuniario").is(':checked') ? 1 : 0;
                            var chk_ignorar_faltas = $("#chk_ignorar_faltas").is(':checked') ? 1 : 0;
                            var chk_ignorar_ferias_dobradas = $("#chk_ignorar_ferias_dobradas").is(':checked') ? 1 : 0;
                            var chk_metade_ferias = $("#chk_metade_ferias").is(':checked') ? 1 : 0;
                            
                            $('.periodos_pendentes').each(function () {
                                
                                if ($(this).is(":checked")) {

                                    id_clt = $(this).data("clt");
                                    data_aquisitivo_ini = $(this).data("ini");
                                    data_aquisitivo_fim = $(this).data("fim");
                                    
                                }    

                            });
                            
                            BootstrapDialog.show({
                                type: BootstrapDialog.TYPE_INFO,                             
                                title: "Calculo de Férias",
                                message: $("<div id='modal'></div>").load("ferias_processar.php?method=telaModalCalculaFerias&id_clt="+id_clt+"&data_aquisitivo_ini="+data_aquisitivo_ini+"&data_aquisitivo_fim="+data_aquisitivo_fim+"&data_ini="+data_ini+"&chk_abono_pecuniario="+chk_abono_pecuniario+"&chk_ignorar_faltas="+chk_ignorar_faltas+"&chk_ignorar_ferias_dobradas="+chk_ignorar_ferias_dobradas+"&chk_metade_ferias="+chk_metade_ferias),
                                closable: false,
                                buttons: [
                                    {
                                        label: "Lançar Férias",
                                        cssClass: "btn-primary",
                                        hotkey: 13,
                                        autospin: true,                                        
                                        action: function (dialog) {
                                            
                                            dialog.enableButtons(false);
                                            dialog.setClosable(false);
                                            dialog.getModalBody().html("Lançando Férias ... ");
                                            
                                            console.log(data_ini);
                                            
                                            $.ajax({
                                                url: "ferias_processar.php",
                                                type: "POST",
                                                dataType: "json",
                                                data: {
                                                    method: "insertFerias",
                                                    id_clt: id_clt,
                                                    data_aquisitivo_ini: data_aquisitivo_ini,
                                                    data_aquisitivo_fim: data_aquisitivo_fim,
                                                    data_ini: data_ini,
                                                    chk_abono_pecuniario: chk_abono_pecuniario,
                                                    chk_ignorar_faltas: chk_ignorar_faltas,
                                                    chk_ignorar_ferias_dobradas: chk_ignorar_ferias_dobradas,
                                                    chk_metade_ferias: chk_metade_ferias
                                                },
                                                success: function (data) {

                                                    $('#id_ferias').val(data.status);
                                                    $('#mensagem').text(data.mensagem);

                                                    if(data.status){

                                                        $('#modal').addClass( "alert-sucess" );                                                                                

                                                    }
                                                    else {

                                                        $('#modal').addClass( "alert-danger" );                                                                                

                                                    }    

                                                },
                                                error: function(data){

                                                    html = "<div class='bs-example'><div class='alert alert-danger fade in'><a href='#' class='close' data-dismiss='alert'>&times;</a><strong>Error</strong> em webFerias->insertFerias in "+data.responseText+"</div></div>";

                                                    div_message_erro.append(html);

                                                    div_message_erro.show();

                                                }
                                            });
                                            
                                            dialog.enableButtons(false);
                                            dialog.setClosable(false);
                                            dialog.getModalBody().html("Executando Lançamento ... ");
                                            
                                            BootstrapDialog.show({
                                                type: BootstrapDialog.TYPE_INFO,                           
                                                title: "Lançamento de Férias",
                                                message: $('<div id="modal"></div>').html("<div id='mensagem'></div>"),
                                                closable: false,
                                                buttons: [{
                                                            label: "Visualizar PDF",
                                                            cssClass: "btn-primary",
                                                            hotkey: 13,
                                                            autospin: true,                                        
                                                            action: function (dialog) {
                                                                
                                                                dialog.enableButtons(false);
                                                                dialog.setClosable(false);
                                                                dialog.getModalBody().html('Gerando Visualização ...');

                                                                id_ferias = $("#id_ferias").val()

                                                                setTimeout(function(){

                                                                    $.ajax({
                                                                        url: "ferias_processar.php",
                                                                        type: "POST",
                                                                        dataType: "json",
                                                                        data: {
                                                                            method: "gerarPdf",
                                                                            id_ferias: id_ferias,
                                                                        },
                                                                        success: function (data) {

                                                                            console.log(data);
                                                                            
                                                                            window.open(data.url);

                                                                            event.preventDefault();         
                                                                            
                                                                            dialog.close();
                                                                

                                                                        },
                                                                        error: function(data){

                                                                            console.log(data);

                                                                            html = "<div class='bs-example'><div class='alert alert-danger fade in'><a href='#' class='close' data-dismiss='alert'>&times;</a><strong>Error</strong> em webFerias->insertFerias in "+data.responseText+"</div></div>";

                                                                            div_message_erro.append(html);

                                                                            div_message_erro.show();
                                                                            
                                                                            dialog.close();

                                                                        }
                                                                    });

                                                                }, 1000);                                        
                                                            }
                                                        },                                    
                                                        {
                                                            label: "Fechar",
                                                            cssClass: "btn-primary",
                                                            action: function (dialog) {
                                                                dialog.close();
                                                            }
                                                        }]
                                            });                                            
                                            
                                            dialog.close();
                                            
                                        }
                                    },
                                    {
                                        label: "Cancelar",
                                        action: function (dialog) {
                                            dialog.close();
                                        }
                                    }]
                            });


                        });                        
                        
                        $('body').on('click',".historico-ferias-salario-variavel",function () {
                            
                            var url = 'ferias_processar.php';
                            
                            $('.periodos_pendentes').each(function () {

                                if ($(this).is(":checked")) {

                                    var id_clt = $(this).data("clt");
                                    var data_aquisitivo_ini = $(this).data("ini");
                                    var data_aquisitivo_fim = $(this).data("fim");

                                    $.post(
                                            url,
                                            {
                                              method: 'telaHistoricoSalarioVariavel',
                                              id_clt: id_clt,
                                              data_aquisitivo_ini: data_aquisitivo_ini,
                                              data_aquisitivo_fim: data_aquisitivo_fim
                                            },function(data){

                                            bootDialog(data,'Histórico de Salário Variável');

                                            $("[data-toggle='tooltip']").tooltip(); 

                                     });

                                }    
                            
                            });
                            
                        });                        
                        
                    });

                    function mascara_data(d) {
                    
                        var mydata = '';
                        var dia = '';
                        var mes = '';
                        var ano = '';
                        
                        data = d.value;
                        
                        mydata = mydata + data;
                        
                        if (mydata.length == 2) {
                            mydata = mydata + '/';
                            d.value = mydata;
                        }
                        
                        if (mydata.length == 5) {
                            mydata = mydata + '/';
                            d.value = mydata;
                        }
                        
                        if (mydata.length == 10) {

                            verifica_data(d);
                            
                        }
                        
                    }
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
        
        try {
             
            header("location: /intranet/?class=ferias/processar&id_clt={$_REQUEST['id_clt']}");             

            exit('esse módulo foi migrado, necessita de nova referência para execução');
            
            //exit('esse módulo foi migrado, necessita de nova referência para execução');

            header ('Content-type: text/html; charset=ISO-8859-1');         

            $this->setBuild('2681');
            $this->setPageTitle('<h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Processamento de Férias</small></h2>');
            $this->funcionario = new funcionario();
            $this->date = new DateClass();
            $this->rh = new RhClass();

            /* 
             * carrega variáveis passadas por POST ou GET
             */
            $id_ferias =  isset($_REQUEST['id_ferias']) ? $_REQUEST['id_ferias'] : 0; 
            $id_regiao =  isset($_REQUEST['regiao']) && $_REQUEST['regiao'] > 0 ? $_REQUEST['regiao'] : $this->funcionario->id_regiao; 
            $id_projeto =  isset($_REQUEST['projeto']) && $_REQUEST['projeto'] > 0 ? $_REQUEST['projeto'] : 0;
            $id_clt = isset($_REQUEST['id_clt']) ? $_REQUEST['id_clt'] : 0;
            $data_aquisitivo_ini = isset($_REQUEST['data_aquisitivo_ini']) ? $_REQUEST['data_aquisitivo_ini'] : '';
            $data_aquisitivo_fim = isset($_REQUEST['data_aquisitivo_fim']) ? $_REQUEST['data_aquisitivo_fim'] : '';
            $data_ini = isset($_REQUEST['data_ini']) ? $_REQUEST['data_ini'] : '';
            $chk_abono_pecuniario = isset($_REQUEST['chk_abono_pecuniario']) ? $_REQUEST['chk_abono_pecuniario'] : 0;
            $chk_ignorar_faltas = isset($_REQUEST['chk_ignorar_faltas']) ? $_REQUEST['chk_ignorar_faltas'] : 0;
            $chk_ignorar_ferias_dobradas = isset($_REQUEST['chk_ignorar_ferias_dobradas']) ? $_REQUEST['chk_ignorar_ferias_dobradas'] : 0;
            $chk_metade_ferias = isset($_REQUEST['chk_metade_ferias']) ? $_REQUEST['chk_metade_ferias'] : 0;
            $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : ''; 
            $value = isset($_REQUEST['value']) ? $_REQUEST['value'] : ''; 

            $this->funcionario->MostraUser(0);
            $funcionario = $this->funcionario->id_funcionario;   
            
            /*
             * Instância as classes do framework
             * 
             * Obs: É importante utilizar o instâncimanto na ordem em que vão acontecer 
             *      a seleção de dados para se poder fazer uso da Macro $this->rh->select() e $this->rh->getRow()
             */

            $this->rh->AddClassExt('Clt'); 
            $this->rh->AddClassExt('Status');
            $this->rh->AddClassExt('Curso');
            $this->rh->AddClassExt('Projeto');
            $this->rh->AddClassExt('Empresa');
            $this->rh->AddClassExt('Bancos');
            $this->rh->AddClassExt('Eventos');
            $this->rh->AddClassExt('Folha');
            $this->rh->AddClassExt('FolhaProc');
            $this->rh->AddClassExt('Movimentos');
            $this->rh->AddClassExt('MovimentosClt');
            $this->rh->AddClassExt('Ferias');
            $this->rh->AddClassExt('FeriasItens');

            $this->rh->setDefault();

//            $this->rh->Clt->setIdRegiao($id_regiao);
//            $this->rh->Clt->setIdProjeto($id_projeto);
            $this->rh->Clt->setIdClt($id_clt);

            if(!empty($data_ini)) $this->rh->Clt->setStatusDateFuture($data_ini);

            $this->rh->Ferias->setDataAquisitivoIni($data_aquisitivo_ini);
            $this->rh->Ferias->setDataAquisitivoFim($data_aquisitivo_fim);
            $this->rh->Ferias->setDataIni($data_ini);
            $this->rh->Ferias->setUser($funcionario);

            /*
             * Carrega registros em todas as instâncias de classes
             */
            if($method=='gerarPdf' || $method=='telaAvisoFerias' || $method=='correcao'){

                $this->rh->Ferias->setDefault()->setIdFerias($id_ferias);

            }
            else {

                if(!$this->rh->Clt->select()->getRow()->isOk()) $this->rh->Clt->error->set('Nenhum registro carregado para Clt! Verifique região e projeto',E_FRAMEWORK_ERROR);
                if(!$this->rh->Projeto->select()->getRow()->isOk()) $this->rh->Projeto->error->set('Nenhum registro carregado para Projeto deste Clt! Verifique região e projeto',E_FRAMEWORK_ERROR);
                if(!$this->rh->Empresa->select()->getRow()->isOk()) $this->rh->Empresa->error->set('Nenhum registro carregado para Empresa deste Clt! Verifique região e projeto',E_FRAMEWORK_ERROR);
                
                $this->rh->Bancos->select()->getRow()->isOk();               
//                if(!$this->rh->Bancos->select()->getRow()->isOk()) $this->rh->Bancos->error->set('Nenhum registro carregado para Banco deste Clt! Verifique região e projeto',E_FRAMEWORK_ERROR);
                

            }

            $this->rh->Ferias->setVendido($chk_abono_pecuniario);
            $this->rh->Ferias->setIgnorarFaltas($chk_ignorar_faltas);
            $this->rh->Ferias->setIgnorarFeriasDobradas($chk_ignorar_ferias_dobradas);
            $this->rh->Ferias->setMetadeFerias($chk_metade_ferias);
            

            $this->setMethodExt($method); 

            if(empty($this->getMethodExt())){

                $this->showPage('telaForm');

            }
            else {

                $this->exeMethodExt($value);

            }
            
        } catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            echo $this->rh->getAllMsgCode();
            
        }
        
    }
 
    /*
     * PHP-DOC Tela 1 - Tela de definição das condições de lançamento das férias
     */
    protected function telaForm(){
        
        $periodos_gozados = $this->rh->Ferias->getPeriodosGozados();
        $periodos_pendentes = $this->rh->Ferias->getPeriodoAquisitivoPendente();
        
        $dominio = $_SERVER['HTTP_HOST'];
        
        $html = $this->getAlertHtml($this->rh->getAllMsgCode(),"danger");
        
        ?>
        <input type="hidden" id="id_ferias" value="">                
        <div class="note note-warning text-center">
             <h4><?=!empty($this->rh->Clt->GetIdClt()) ? $this->rh->Clt->GetIdClt().' - '.$this->rh->Clt->GetNome() : ''?></h4>
        </div>                                        
        <div class="panel-body">
            <form class="form-horizontal" action="" enctype="multipart/form-data" method="post" name="form" id="form">
                <div class="form-group">
                    <div class="col-lg-12">
                        <label class="control-label">Já lançou os movimentos do candidato neste mês?:</label>       
                        <input type="button" class="btn btn-success" name="sim_processou" id="btn_sim_processou" value="SIM">                    
                        <input type="button" class="btn btn-danger" name="nao_processou" id="btn_nao_processou" value="NÃO">  
                        <input type="button" class="btn btn-success" name="ver_historico" id="btn_ver_historico" value="VER HISTÓRICO DE FÉRIAS">                    
                    </div>
                </div>
                <div id="div_historico" class="form-group">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="col-lg-12">
                                <label class="control-label">Férias Gozadas:</label>   
                            </div>
                            <div class="col-lg-12">
                                <label class="control-label"></label>   
                            </div>
                            <div class="col-lg-12">
                                <?php
                                if(count($periodos_gozados)){
                                    /*
                                     * Lista as férias já gozadas pelo clt
                                     */
                                    foreach ($periodos_gozados as $key => $value) {

                                    ?>
                                    <div class="col-lg-12">
                                        <label class="label_periodo_gozados" name="label_periodo_gozados[]" data-clt="<?=$this->rh->Clt->GetIdClt()?>" data-ini="<?=$this->date->getDate('Y-m-d',$value['data_aquisitivo_ini'])?>" data-fim="<?=$this->date->getDate('Y-m-d',$value['data_aquisitivo_fim'])?>">
                                            <?=$this->date->getDate('d/m/Y',$value['data_aquisitivo_ini'])?> - <?=$this->date->getDate('d/m/Y',$value['data_aquisitivo_fim'])?>
                                        </label>
                                    </div>
                                    <?php
                                    }
                                }
                                else {
                                    ?>
                                    <div class="col-lg-12">
                                        <label>
                                            Não existem períodos gozados para esse Clt
                                        </label>
                                    </div>
                                    <?php
                                }
                                ?>  
                            </div>
                        </div>
                    </div>
                </div>
                <div id="div_form">
                    <div class="form-group">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="col-lg-12">
                                    <label class="control-label">Selecione um Período Aquisitivo:</label>   
                                </div>
                                <div class="col-lg-12">
                                    <label class="control-label"></label>   
                                </div>
                                <?php
                                if(count($periodos_pendentes)){
                                    /*
                                     * Necessário ferificar faltas no período para concessão das férias
                                     */
                                    foreach ($periodos_pendentes as $key => $value) {

                                    ?>
                                    <div class="col-lg-12">
                                        <label class="control-label label_periodos_pendentes" name="label_periodos_pendentes[]">
                                            <input type="radio" name="periodos_pendentes[]" id="periodos_pendentes<?=$key?>" class="periodos_pendentes" value="1" data-clt="<?=$this->rh->Clt->GetIdClt()?>" data-ini="<?=$this->date->getDate('Y-m-d',$value['data_aquisitivo_ini'])?>" data-fim="<?=$this->date->getDate('Y-m-d',$value['data_aquisitivo_fim'])?>" >
                                            <?=$this->date->getDate('d/m/Y',$value['data_aquisitivo_ini'])?> - <?=$this->date->getDate('d/m/Y',$value['data_aquisitivo_fim'])?> 
                                            <?=$value['faltas'] ? "<label class='control-label has-error has-feedback' for='inputError2'>({$value['faltas']} falta(s) no período)</label>" : ""?>
                                            <?=$value['soma_eventos_mais_180'] ? "<label class='control-label' for='inputError2'>(Data aquisitivo alterada pois Clt possui evento(s) com 180 dias ou mais no período)" : ""?>
                                        </label>
                                    </div>
                                    <?php
                                    }
                                }
                                else {
                                    ?>
                                    <div class="col-lg-12">
                                        <label>
                                            Não existem períodos pendentes para esse Clt a serem gozados
                                        </label>
                                    </div>
                                    <?php
                                }
                                ?>  
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="div_lancamento">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="col-lg-3">
                                    <label class="control-label">Data de Início das Férias:</label>   
                                </div>
                                <div class="col-lg-2">
                                    <div id="data_ini" class='input-group date'>
                                        <input type='text' id="data_ini_fmt" name="data_ini_fmt" class="form-control span2" onKeyUp="mascara_data(this)" onChange="$('#btn_calcular_ferias').prop('disabled', false);" readonly="true" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar">
                                            </span>
                                        </span>
                                    </div>                                    
                                    <span class="add-on"><i class="icon-th"></i></span>
                                </div>
                                <div class="col-lg-3" id="div_label_dias_ferias">
                                </div>
                                <div class="col-lg-1" id="div_dias_ferias">
                                </div>
                                <div class="col-lg-3 alert alert-danger" role="alert" id="div_abono_pecuniario">
                                    <?php
                                    if($this->rh->config()->title('ferias')->key('chk_abono_pecuniario')->val()){
                                    ?>
                                    <label><input type='checkbox' value='1' id='chk_abono_pecuniario' name='chk_abono_pecuniario'>&nbsp;Abono Pecuniário</label>
                                    <?php
                                    }
                                    if($this->rh->config()->title('ferias')->key('chk_ignorar_faltas')->val()){
                                    ?>
                                    <label><input type='checkbox' value='1' id='chk_ignorar_faltas' name='chk_ignorar_faltas'>&nbsp;Ignorar Faltas</label>
                                    <?php
                                    }
                                    if($this->rh->config()->title('ferias')->key('chk_ignorar_ferias_dobradas')->val()){
                                    ?>
                                    <label><input type='checkbox' value='1' id='chk_ignorar_ferias_dobradas' name='chk_ignorar_ferias_dobradas'>&nbsp;Ignorar Férias Dobradas</label>
                                    <?php
                                    }
                                    if($this->rh->config()->title('ferias')->key('chk_metade_ferias')->val()){
                                    ?>
                                    <label><input type='checkbox' value='1' id='chk_metade_ferias' name='chk_metade_ferias'>&nbsp;Lançar apenas 15 dias de férias</label>
                                    <?php
                                    }
                                    if($this->rh->config()->title('ferias')->key('chk_definir_dias_ferias')->val()){
                                    ?>
                                    <label><input type='checkbox' value='1' id='chk_definir_dias_ferias' name='chk_definir_dias_ferias'>&nbsp;Definir número de dias de férias</label>
                                    <?php
                                    }
                                    ?>
                                    
                                </div>
                            </div>
                        </div>
                    </div>     
                    <div class="form-group">
                        <div class="col-lg-12 panel-footer text-center">
                            <input type="button" class="btn btn-success" name="btn_calcular_ferias" id="btn_calcular_ferias" value="CALCULAR FÉRIAS" disabled>
                        </div>
                    </div>
                </div>
                <div id="message_erro">
                    <?=$html;?>
                </div>
            </form>             
         </div>
         <?php    
         
    }

    /*
     * PHP-DOC
     * 
     * @name telaModalCalculaFerias
     * 
     * @internal - Exibe Uma memória de calculo para processamento das férias.
     */      
    protected function telaModalCalculaFerias(){
        
        try {
            
            $this->rh->Ferias->setCalcFerias();

            $collection_movimentos_clt = $this->rh->Ferias->getCollectionMovimentosClt();
            
            ?>


            <!-- Modal para exibição da memória de calculo -->
            <div id="div_modal_calc_ferias" name="div_modal_calc_ferias">
                <form name="" id="" action="">
                <div id="modal_motivo" class="">
                    <div role="tabpanel" class="tab-pane active" id="avisos">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-danger">
                                    <div class="panel-heading"><?=$this->rh->Clt->GetIdClt()?> - <?=$this->rh->Clt->GetNome()?></div>
                                    <div class="panel-body overflow">
                                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                            <div>
                                                <div class="panel-heading pointer" role="tab" id="heading1" data-toggle="collapse" data-parent="#resumo_ferias" href="#resumo_ferias">
                                                    <h3 class="panel-title">Resumo do Período de Férias</h3>
                                                </div>
                                                <div id="resumo_ferias">
                                                    <table id="table_resumo_ferias" class="table">
                                                        <tbody>
                                                            <tr>
                                                                <td>Período Aquisitivo:</td>
                                                                <td class="text-center" id="td_periodo_aquisitivo"><strong><?=$this->rh->Ferias->GetDataAquisitivoIni('d/m/Y')->val()?> à <?=$this->rh->Ferias->GetDataAquisitivoFim('d/m/Y')->val()?></strong></td>
                                                                <td>Período de Férias:</td>
                                                                <td class="text-center" id="td_periodo_ferias"><strong><?=$this->rh->Ferias->GetDataIni('d/m/Y')->val()?> à <?=$this->rh->Ferias->GetDataFim('d/m/Y')->val()?></strong></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Quantidade de Dias:</td>
                                                                <td class="text-center" id="td_qnt_dias_total"><strong><?=$this->rh->Ferias->GetDiasFerias()?></strong></td>
                                                                <td>Data de Retorno:</td>
                                                                <td class="text-center" id="td_data_retorno"><strong><?=$this->rh->Ferias->GetDataRetorno('d/m/Y')->val()?></strong></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="panel panel-default">
                                                <div class="panel-heading pointer" role="tab" id="heading2" data-toggle="collapse" data-parent="#resumo_pagto_ferias" href="#resumo_pagto_ferias">
                                                    <script>
                                                        $("#heading2").on("click", function () {

                                                            if($("#panel2").hasClass("glyphicon-plus")) {

                                                                $("#panel2").removeClass('glyphicon-plus');
                                                                $("#panel2").addClass('glyphicon-minus');

                                                            }
                                                            else {

                                                                $("#panel2").removeClass('glyphicon-minus');
                                                                $("#panel2").addClass('glyphicon-plus');

                                                            }

                                                        }); 

                                                    </script> 

                                                    <span class="pull-right clickable"><i id="panel2" class="glyphicon glyphicon-plus"></i></span>                                                
                                                    <h3 class="panel-title">Resumo do Pagamento de Férias</h3>
                                                </div>
                                                <div id="resumo_pagto_ferias" class="panel-collapse collapse" role="tabpanel">
                                                    <table class="table table-hover table-striped table-ferias text-sm">
                                                        <tbody>
                                                            <tr>
                                                                <td class="modal_label">Último Salário:</td>
                                                                <td class="modal_value text-right"><?=$this->rh->Curso->getSalario('R$  |.|,|2')?><?php if($this->rh->Ferias->getSalarioExtra() > 0) echo $this->rh->Ferias->getSalarioExtra('+ R$  |.|,|2');?></td>
                                                                <td class="modal_status text-right">&nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="modal_label">Média Salário Variável:</td>
                                                                <td class="modal_value text-right"><?=$this->rh->Ferias->getSalarioVariavel('R$  |.|,|2')?></td>
                                                                <td class="modal_status text-right">
                                                                    <a href="javascript:void(0);" data-id-clt="<?=$this->rh->Clt->GetIdClt()?> " class="historico-ferias-salario-variavel">
                                                                        <i data-type="visualizar" class="tooo btn btn-xs btn-primary fa fa-search <?php if(!$this->rh->Ferias->getSalarioVariavel()){ ?>xdisabled<?php } ?>" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ver Histórico de Salário Variável"></i>
                                                                    </a>                                                                
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="modal_label">Insalubridade/Periculosidade:</td>
                                                                <td class="modal_value text-right"><?=$this->rh->Ferias->getInsalubridadePericulosidade('R$  |.|,|2')?></td>
                                                                <td class="modal_status text-right">
                                                                    <a href="javascript:void(0);" data-id-clt="<?=$this->rh->Clt->GetIdClt()?> " class="historico-ferias-salario-variavel">
                                                                    </a>                                                                
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>                                                        
                                                    <table class="table table-hover table-striped table-ferias text-sm">
                                                        <tbody>
                                                            <tr>
                                                                <td class="modal_label">A (+) Valor das Férias: (<?=$this->rh->Ferias->getDiasFerias()?> dias de férias)</td>
                                                                <td class="modal_value text-right"><span class="credito"><?=$this->rh->Ferias->getValorTotalFerias('R$  |.|,|2')?></span></td>
                                                                <td class="modal_status text-right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="modal_label">A (+) 1/3 Constitucional Férias: (<?=$this->rh->Ferias->getDiasFerias()?> dias de férias)</td>
                                                                <td class="modal_value text-right"><span class="credito"><?=$this->rh->Ferias->getUmTerco('R$  |.|,|2')?></span></td>
                                                                <td class="modal_status text-right"></td>
                                                            </tr>
                                                            <?php
                                                            if($this->rh->Ferias->chkFeriasDobradas()->isOk()){
                                                            ?>
                                                            <tr>
                                                                <td class="modal_label">A (+)<strong> Último Salário de Férias Dobradas:</strong></td>
                                                                <td class="modal_value text-right"><span class="credito"><?=$this->rh->Curso->getSalario('R$  |.|,|2')?></span></td>
                                                                <td class="modal_status text-right">(INSS,IR,FGTS)</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="modal_label">A (+)<strong> 1/3 Constitucional Férias Dobradas:</strong></td>
                                                                <td class="modal_value text-right"><span class="credito"><?=$this->rh->Ferias->getUmTercoFeriasDobradas('R$  |.|,|2')?></span></td>
                                                                <td class="modal_status text-right">(INSS,IR,FGTS)</td>
                                                            </tr>
                                                            <?php
                                                            } 
                                                            /*
                                                             * Exibe movimentos de crédito que não fazem parte das médias
                                                             */
                                                            if(is_array($collection_movimentos_clt)) {
                                                                foreach ($collection_movimentos_clt['dados']['CREDITO'] as $key => $value) {

                                                                    if($value['cod_movimento']!=$this->rh->getKeyMaster(12)) {
                                                                    ?>
                                                            <tr>
                                                                <td class="modal_label">A (+)<strong>&nbsp;<?=$value['nome_movimento']?></strong></td>
                                                                <td class="modal_value text-right"><span class="credito"><?=$value['valor_movimento']?></span></td>
                                                                <td class="modal_status text-right"></td>
                                                            </tr>
                                                                    <?php
                                                                    }

                                                                }
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>    
                                                    <table class="table table-hover table-striped table-ferias text-sm">
                                                        <tbody>
                                                            <tr class="info" role="alert">
                                                                <td class="modal_label">A (=) Total de Remunerações:</td>
                                                                <td class="modal_value text-right"><span class="sub-total"><?=$this->rh->Ferias->getTotalRemuneracoes('R$  |.|,|2')?></span></td>
                                                                <td class="modal_status text-right"></td>
                                                            </tr>
                                                            <tr class="info" role="alert">
                                                                <td class="modal_label">A (=) Salário Base de Calculo INSS:</td>
                                                                <td class="modal_value text-right"><span class="sub-total"><?=$this->rh->Ferias->getInssBase('R$  |.|,|2')?></span></td>
                                                                <td class="modal_status text-right"></td>
                                                            </tr>
                                                            <tr  class="info" role="alert">
                                                                <td class="modal_label">A - INSS <?=$this->rh->Ferias->getIrrfQntDependentes() ? ' - <strong>'.$this->rh->Ferias->getIrrfQntDependentes().'</strong> dependentes' : '' ?> (=) Salário Base de Calculo IRRF:</td>
                                                                <td class="modal_value text-right"><span class="sub-total"><?=$this->rh->Ferias->getIrrfBase('R$  |.|,|2')?></span></td>
                                                                <td class="modal_status text-right"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>                                                        
                                                    <?php
                                                    if($this->rh->Ferias->getVendido()){
                                                    ?>
                                                    <table class="table table-hover table-striped table-ferias text-sm">
                                                        <tbody>
                                                            <tr>
                                                                <td class="modal_label">A (+) Valor do Abono: (<?=$this->rh->Ferias->getCalcDiasAbonoPecuniario()?> dias de abono)</td>
                                                                <td class="modal_value text-right"><span class="credito"><?=$this->rh->Ferias->getAbonoPecuniario('R$  |.|,|2')?></span></td>
                                                                <td class="modal_status text-right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="modal_label">A (+) 1/3 Constitucional Abono Pecuniário: (<?=$this->rh->Ferias->getCalcDiasAbonoPecuniario()?> dias de abono)</td>
                                                                <td class="modal_value text-right"><span class="credito"><?=$this->rh->Ferias->getUmTercoAbonoPecuniario('R$  |.|,|2')?></span></td>
                                                                <td class="modal_status text-right"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>                                                        
                                                    <table class="table table-hover table-striped table-ferias text-sm">
                                                        <tbody>
                                                            <tr class="info" role="alert">
                                                                <td class="modal_label">A (=) Total</td>
                                                                <td class="modal_value text-right"><span class="sub-total"><?=$this->rh->Ferias->getTotalRemuneracoes('R$  |.|,|2')?></span></td>
                                                                <td class="modal_status text-right"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>                                                        
                                                    <?php
                                                    }
                                                    ?>
                                                    <table class="table table-hover table-striped table-ferias text-sm">
                                                        <tbody>
                                                            <tr>
                                                                <td class="modal_label">B (-) INSS</td>
                                                                <td class="modal_value text-right"><span class="debito"><?=$this->rh->Ferias->getInss('R$  |.|,|2')?></span></td>
                                                                <td class="modal_status text-right"></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="modal_label">B (-) IRRF </td>
                                                                <td class="modal_value text-right"><span class="debito"><?=$this->rh->Ferias->getIrrfValor('R$  |.|,|2')?></span></td>
                                                                <td class="modal_status text-right"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>                                                        
                                                    <table class="table table-hover table-striped table-ferias text-sm">
                                                        <tbody>
                                                            <tr class="info">
                                                                <td class="modal_label">B (=) Total dos Descontos:</td>
                                                                <td class="modal_value text-right"><span class="debito"><?=$this->rh->Ferias->getTotalDescontos('R$  |.|,|2')?></span></td>
                                                                <td class="modal_status text-right"></td>
                                                            </tr>
                                                            <tr class="info">
                                                                <td class="modal_label">A - B (=) Sub Total Líquido:</td>
                                                                <td class="modal_value text-right"><span class="sub-total"><?=$this->rh->Ferias->getCalcSubTotalLiquido('R$  |.|,|2')?></span></td>
                                                                <td class="modal_status text-right"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>                                                        
                                                    <table class="table table-hover table-striped table-ferias text-sm">
                                                        <tbody>
                                                            <tr>
                                                                <td class="modal_label">C (-) Pensão Alimentícia:</td>
                                                                <td class="modal_value text-right"><span class="debito"><?=$this->rh->Ferias->getPensaoAlimenticia('R$  |.|,|2')?></span></td>
                                                                <td class="modal_status text-right"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <table class="table table-hover table-striped table-ferias text-sm">
                                                        <tbody>
                                                            <tr>
                                                                <td class="modal_label">A - B - C (=) Total à Receber:</td>
                                                                <td class="modal_value text-right"><?=$this->rh->Ferias->getTotalLiquido('R$  |.|,|2')?></td>
                                                                <td class="modal_status text-right"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- /.panel-body -->
                                </div><!-- /.panel-primary -->
                            </div><!-- /.col-lg-6 -->
                        </div><!-- /.row -->
                    </div>                
                    <div id="message_erro">
                        <?php
                        /*
                         * Caso haja algum retorno de erro desabilita o botão de processamento
                         */
                        if($this->rh->chkInCode(E_FRAMEWORK_ERROR)){
                        ?>
                        <script>
                        $('.btn-primary').prop( "disabled", true);
                        </script>
                        <?php
                        }

                        /*
                         * Verifica se houve algum erro na operação
                         */
                        echo $this->getAlertHtml($this->rh->getAllMsgCode(),"danger"); 

                        ?>
                    </div>
                </div>
                </form>
            <?php
            
        } 
        catch (Exception $ex) {
            
            //echo $ex->getTraceAsString();
            
        } 



        
    }
    
    /*
     * PHP-DOC
     * 
     * @name telaHistoricoSalarioVariavel
     * 
     * @internal - Exibe um histórico de salário variável dentro do período aquisitivo.
     */      
    protected function telaHistoricoSalarioVariavel(){
        
        $this->rh->Folha->setDefault();
        $this->rh->Folha->setDateRangeIni($this->rh->Ferias->getDataAquisitivoIni()->val());
        $this->rh->Folha->setDateRangeFim($this->rh->Ferias->getDataAquisitivoFim()->val());
        
        $collection_salario_variavel = $this->rh->Folha->getCollectionMovimentosFixosVariaveis();
        
        /*
         * Verifica se houve algum erro na operação
         */
        $html = $this->getAlertHtml($this->rh->Ferias->error->getAll(E_FRAMEWORK_NOTICE),"danger");
        
        ?>

        
        <!-- Modal para exibição da memória de calculo -->
        <div id="div_modal_calc_ferias" name="div_modal_calc_ferias">
            <form name="" id="" action="">
            <input type="hidden" name="home" id="home" value="" />
            <div id="modal_motivo" class="">
                <div role="tabpanel" class="tab-pane active" id="avisos">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-danger">
                                <div class="panel-heading"><?=!empty($this->rh->Clt->GetIdClt()) ? $this->rh->Clt->GetIdClt().' - '.$this->rh->Clt->GetNome() : ''?></div>
                                <div class="panel-body overflow">
                                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                        <div>
                                            <div class="panel-heading pointer" role="tab" id="heading1" data-toggle="collapse" data-parent="#salario_variavel" href="#salario_variavel">
                                                <h3 class="panel-title">Lançamentos de Crédito de Salário Variável de <?=$this->rh->Ferias->getDataAquisitivoIni('d/m/Y')?> à <?=$this->rh->Ferias->getDataAquisitivoFim('d/m/Y')?></h3>
                                            </div>
                                            <div id="salario_variavel">
                                                <table class="table table-salario-variavel">
                                                    <thead>
                                                    </thead>    
                                                    <tbody>
                                                        <?php 
                                                        foreach ($collection_salario_variavel['collection'] as $collection_ano => $ano_itens) {    

                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <table class="table table-salario-variavel">
                                                                    <thead>
                                                                        <tr class="novo_tr">
                                                                            <td>
                                                                                <strong><?=$collection_ano?></strong>
                                                                            </td>
                                                                        </tr>
                                                                    </thead>    
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                <?php
                                                                                foreach ($ano_itens as $collection_mes => $mes_itens) {
                                                                                ?>
                                                                                <table class="table table-salario-variavel">
                                                                                    <thead>
                                                                                        <tr class="novo_tr">
                                                                                            <td>
                                                                                                <strong><?=$this->date->stringMonth((int)$collection_mes)?></strong>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </thead>    
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <table class="table table-salario-variavel">
                                                                                                    <thead>
                                                                                                        <tr class="bg-primary valign-middle">
                                                                                                            <th class="text-left" style="width:70%;">DESCRIÇÃO</th>
                                                                                                            <th class="text-right" style="width:20%;">VALOR</th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                        <?php

                                                                                                        foreach ($mes_itens as $collection_itens => $itens) {


                                                                                                            foreach ($itens as $collection_itens => $item) {

                                                                                                            ?>

                                                                                                            <tr>
                                                                                                                <td class="text-left"><?=$item['nome_movimento']?></td>
                                                                                                                <td class="text-right">R$ <?=number_format($item['valor_movimento'], 2, ',', '.')?></td>
                                                                                                            </tr>

                                                                                                            <?php
                                                                                                            } 
                                                                                                        }
                                                                                                        ?> 
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>     
                                                                                    </tbody>
                                                                                </table>    
                                                                                <?php
                                                                                }
                                                                                ?> 
                                                                            </td>        
                                                                        </tr>    
                                                                    </tbody>
                                                                </table>        
                                                            </td>
                                                        </tr>
                                                        <?php
                                                        }
                                                        ?> 
                                                    </tbody>   
                                                    <tfoot>
                                                        <tr>
                                                            <td class="text-right">
                                                                <h3>
                                                                    R$ <?=number_format($collection_salario_variavel['total_geral'], 2, ',', '.')?>
                                                                </h3>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-right">
                                                                <h3>
                                                                    <?=number_format($collection_salario_variavel['total_geral'], 2, ',', '.')?>/12 = R$ <?=number_format(round($collection_salario_variavel['total_geral']/12,2), 2, ',', '.')?>
                                                                </h3>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- /.panel-body -->
                            </div><!-- /.panel-primary -->
                        </div><!-- /.col-lg-6 -->
                    </div><!-- /.row -->
                </div>                
                <div id="message_erro">
                    <?php
                    /*
                     * Caso haja algum retorno de erro desabilita o botão de processamento
                     */
                    if(!empty($this->rh->Ferias->error->getAll(E_FRAMEWORK_WARNING,'=='))){
                    ?>
                    <script>
                    $('.btn-primary').prop( "disabled", true);
                    </script>
                    
                    <?php
                    echo $html;
                    }
                    ?>
                </div>
            </div>
            </form>
        <?php
        
    }
    
    /*
     * PHP-DOC
     * 
     * @name telaAvisoFerias
     * 
     * @internal - Gera tela de formulário para aviso de férias
     */   
    protected function telaAvisoFerias(){
        
        $this->rh->Ferias->select();
        $this->rh->Ferias->getRow();
        
        $this->rh->Ferias->setCalcValoresDistribuidosEmDoisMeses();
        
        
        $this->rh->Clt->setDefault();
        $this->rh->Clt->setIdClt($this->rh->Ferias->getIdClt());
        $this->rh->Clt->select();
        $this->rh->Clt->getRow();

        $this->rh->Projeto->setDefault();
        $this->rh->Projeto->select();
        $this->rh->Projeto->getRow();
        
        $this->rh->Curso->setDefault();
        $this->rh->Curso->select();
        $this->rh->Curso->getRow();
        
        $this->rh->Bancos->setDefault();
        $this->rh->Bancos->select();
        $this->rh->Bancos->getRow();

        $this->rh->Empresa->setDefault();
        $this->rh->Empresa->select();
        $this->rh->Empresa->getRow();
        
        $this->setCssExt();
        ?>
        <!DOCTYPE html>
        <html lang="en">
            <head>
              <title>Bootstrap Example</title>
              <meta charset="utf-8">
              <meta name="viewport" content="width=device-width, initial-scale=1">
              <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" media='screen,print'>
              <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
              <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
            </head>
            <body>
                <div class="container">
                    <center><h2>Aviso de Férias</h2></center>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td colspan="4">
                                    <?=$this->rh->Projeto->getNome();?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    Sr(a): <?=$this->rh->Clt->getIdClt();?> - <?=$this->rh->Clt->getNome();?>
                                </td>
                                <td>
                                    Admissão: <?=$this->rh->Clt->getDataEntrada('d/m/Y');?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    CTPS: <?=$this->rh->Clt->getCampo1();?>
                                </td>
                                <td colspan="2">
                                    Depto: <?=$this->rh->Bancos->getNome();?>
                                </td>
                                <td>
                                    Cargo: <?=$this->rh->Curso->getNome();?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="small">
                                    Nos termos das disposições vigentes, suas férias serão concedidas conforme demostrativo abaixo:
                                </td>
                            </tr>
                            <tr>
                                <td class="small">
                                    Período aquisito:<br>
                                    <?=$this->rh->Ferias->getDataAquisitivoIni('d/m/Y');?> a
                                    <?=$this->rh->Ferias->getDataAquisitivoFim('d/m/Y');?>                                    
                                </td>
                                <td class="small">
                                    Período de gozo<br>
                                    <?=$this->rh->Ferias->getDataIni('d/m/Y');?> a
                                    <?=$this->rh->Ferias->getDataFim('d/m/Y');?>                                    
                                </td>
                                <td class="small">
                                    Retorno<br>
                                    <?=$this->rh->Ferias->getDataRetorno('d/m/Y');?>                                    
                                </td>
                                <td>
                                    Período de Abono<br>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <br>
                                    _____________________________________<br>
                                     <?=$this->rh->Empresa->getRazao();?> 
                                </td>
                                <td colspan="2" class="small">
                                    <br>
                                    <table class="table-condensed" border="0">
                                        <tbody>
                                            <tr>
                                                <td>______________________</td>
                                                <td>____/____/____</td>
                                                <td>____________________________</td>
                                            </tr>        
                                            <tr>
                                                <td>Local</td>
                                                <td>Data</td>
                                                <td>Empregado</td>
                                            </tr>  
                                        </tbody>    
                                    </table>
                                </td>
                            </tr>
                                <td colspan="4" class="small">
                                    NOTA: O Aviso de Férias será participado por escrito pela empresa, com antecedência de 30 dias
                                </td>
                        </tbody>
                    </table>
                </div>
            </body>    
        </html>    
        <?php
    }

    protected function gerarPdfOld($return = 'json'){
        
        try {

            require_once('../fpdf/fpdf.php');

            define('FPDF_FONTPATH','../fpdf/font/');

            $pdf  = new FPDF("P","cm","A4");
            $pdf->SetAutoPageBreak(true,0.0); // Reduz a tolerância da margem inferior
            $pdf->Open();

            $arr_id_ferias = explode(',',$this->rh->Ferias->getIdFerias());
            
            /*
             * Caso o setIdFerias() esteja setado com vários IDs de férias então imprime em lote
             */
            foreach ($arr_id_ferias as $key => $id_ferias) {

                $this->rh->setDefault();        

                $this->rh->Ferias->setIdFerias($id_ferias)->select()->getRow();    
                
                $this->rh->Clt->setIdClt($this->rh->Ferias->getIdClt())->select()->getRow();

                $this->rh->Ferias->setCalcMovimentosClt();            
                
                $this->rh->Ferias->setCalcValoresDistribuidosEmDoisMeses();
                
                $collection_movimentos_clt = $this->rh->Ferias->getCollectionMovimentosClt();
                
                $this->rh->Projeto->setDefault()->select()->getRow();

                $this->rh->Curso->setDefault()->select()->getRow();

                $this->rh->Bancos->setDefault()->select()->getRow();

                $this->rh->Empresa->setDefault()->select()->getRow();

                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(5, 30, " ");

                $pdf->Image('../images/fundo_ferias.jpg', 0,0.9,20.4075,27.1771,'jpg');


                $pdf->SetXY(1.8,7.2);
                $pdf->Cell(0,0,$this->rh->Empresa->getRazao(),0,0,'L');

                $pdf->SetXY(1.8,3);
                $pdf->Cell(0,0,$this->rh->Projeto->getNome(),0,0,'L');

                $pdf->SetXY(2.8,3.54);
                $pdf->Cell(0,0,$this->rh->Clt->getIdClt()." - ".$this->rh->Clt->getNome(),0,0,'L');

                $pdf->SetXY(15.82,3.55);
                $pdf->Cell(0,0,$this->rh->Clt->getDataEntrada('d/m/Y'),0,0,'L');

                $pdf->SetXY(2.8,4.05);
                $pdf->MultiCell(0,0,$this->rh->Clt->getCampo1(),0,'L');

                $pdf->SetXY(6.9,4.05);
                $pdf->Cell(0,0,$this->rh->Clt->getLocacao(),0,0,'L');

                $pdf->SetXY(14.2,4.10);
                $pdf->Cell(0,0,$this->rh->Curso->getNome(),0,0,'L');

                $pdf->SetXY(1.8,5.7);
                $pdf->Cell(0,0,$this->rh->Ferias->getDataAquisitivoIni('d/m/Y')->val(),0,0,'L');

                $pdf->SetXY(3.4,5.7);
                $pdf->Cell(0,0,"a",0,0,'L');

                $pdf->SetXY(3.9,5.7);
                $pdf->Cell(0,0,$this->rh->Ferias->getDataAquisitivoFim('d/m/Y')->val(),0,0,'L');

                $pdf->SetXY(6.5,5.7);
                $pdf->Cell(0,0,$this->rh->Ferias->getDataIni('d/m/Y')->val(),0,0,'L');

                $pdf->SetXY(8.1,5.7);
                $pdf->Cell(0,0,"a",0,0,'L');

                $pdf->SetXY(8.4,5.7);
                $pdf->Cell(0,0,$this->rh->Ferias->getDataFim('d/m/Y')->val(),0,0,'L');

                $pdf->SetXY(11.36,5.7);
                $pdf->Cell(0,0,$this->rh->Ferias->getDataRetorno('d/m/Y')->val(),0,0,'L');

                $pdf->SetXY(14.2,5.7);
                $pdf->Cell(0,0,"",0,0,'L');

                $pdf->SetXY(15.8,5.7);
                $pdf->Cell(0,0,"",0,0,'L');

                $pdf->SetXY(16.3,5.7);
                $pdf->Cell(0,0,"",0,0,'L');

                $pdf->SetXY(5,9.2);
                $pdf->Cell(0,0,$this->rh->Clt->getNome(),0,0,'L');

                $pdf->SetXY(2.7,9.64);
                $pdf->Cell(0,0,$this->rh->Clt->getCampo1(),0,0,'L');

                $pdf->SetXY(6.8,9.64);
                $pdf->Cell(0,0,$this->rh->Clt->getLocacao(),0,0,'L');

                $pdf->SetXY(14.1,9.61);
                $pdf->Cell(0,0,$this->rh->Curso->getCampo2(),0,0,'L');

                $pdf->SetXY(2.8,10);
                $pdf->Cell(0,0,$this->rh->Bancos->getNome(),0,0,'L');

                $pdf->SetXY(10.7,10);
                $pdf->Cell(0,0,$this->rh->Clt->getAgencia(),0,0,'L');

                $pdf->SetXY(14.1,10);
                $pdf->Cell(0,0,$this->rh->Clt->getConta(),0,0,'L');

                $pdf->SetXY(1.8,10.95);
                $pdf->Cell(0,0,$this->rh->Ferias->getDataAquisitivoIni('d/m/Y')->val(),0,0,'L');

                $pdf->SetXY(3.4,10.95);
                $pdf->Cell(0,0,"a",0,0,'L');

                $pdf->SetXY(3.9,10.95);
                $pdf->Cell(0,0,$this->rh->Ferias->getDataAquisitivoFim('d/m/Y')->val(),0,0,'L');

                $pdf->SetXY(7.45,10.95);
                $pdf->Cell(0,0,$this->rh->Ferias->getDataIni('d/m/Y')->val(),0,0,'L');

                $pdf->SetXY(9.05,10.95);
                $pdf->Cell(0,0,"a",0,0,'L');

                $pdf->SetXY(9.45,10.95);
                $pdf->Cell(0,0,$this->rh->Ferias->getDataFim('d/m/Y')->val(),0,0,'L');

                $pdf->SetXY(13.4,10.95);
                $pdf->Cell(0,0,"",0,0,'L');

                $pdf->SetXY(15.2,10.95);
                $pdf->Cell(0,0,"",0,0,'L');

                $pdf->SetXY(15.7,10.95);
                $pdf->Cell(0,0,"",0,0,'L');

                $pdf->SetXY(1.8,12.4);
                $pdf->Cell(0,0,"-",0,0,'L');

                $pdf->SetXY(2,12.4);
                $pdf->Cell(0.40,0,$this->rh->Ferias->getFaltas(),0,0,'C');

                $pdf->SetXY(2.32,12.4);
                $pdf->Cell(0,0,"-",0,0,'L');

                $pdf->SetXY(5.3,12.4);
                $pdf->Cell(0,0,$this->rh->Ferias->getSalario('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(8.9,12.4);
                $pdf->Cell(0,0,$this->rh->Ferias->getSalarioVariavel('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(12.4,12.4);
                $pdf->Cell(0,0,$this->rh->Ferias->getValorTotalFerias('R$  |.|,|2'),0,0,'L');
                
                $pdf->SetXY(6.150,13.38);
                $pdf->Cell(0,0,$this->rh->Ferias->getDataIni('m/Y')->val(),0,0,'L');
                
                /*
                 * Aqui começa a discriminação dos itens de férias
                 */
                
                $linha = 14.4;
                $fator = 0.5;

                // Valores do 1º Mês

                $pdf->SetXY(1.8,$linha);
                $pdf->Cell(0,0,$this->rh->Ferias->getDiasFerias1(),0,0,'L');

                $pdf->SetXY(2.2,$linha);
                $pdf->Cell(0,0,"dias",0,0,'L');
                
                $pdf->SetXY(8.5,$linha);
                $pdf->Cell(0,0,$this->rh->Ferias->getValorTotalFerias1('R$  |.|,|2'),0,0,'L');

                $linha += $fator;
                
                $pdf->SetXY(1.8,$linha);
                $pdf->Cell(0,0,"Acréscimo constitucional 1/3",0,0,'L');

                $pdf->SetXY(8.5,$linha);
                $pdf->Cell(0,0,$this->rh->Ferias->getAcrescimoConstitucional1('R$  |.|,|2'),0,0,'L');
                
                if($this->rh->Ferias->getValorTotalFeriasDobradas()) {

                    $linha += $fator;
                    $pdf->SetXY(1.8,$linha);
                    $pdf->Cell(0,0,"*Art. 137 CLT - FÉRIAS EM DOBRO",0,0,'L');
                    $pdf->SetXY(8.5,$linha);
                    $pdf->Cell(0,0,$this->rh->Ferias->getValorTotalFeriasDobradas('R$  |.|,|2'),0,0,'L');

                }
                
                if($this->rh->Ferias->getVendido()) {

                    $linha += $fator;
                    $pdf->SetXY(1.8,$linha);
                    $pdf->Cell(0,0,"Abono Pecuniário",0,0,'L');

                    $pdf->SetXY(8.5,$linha);
                    $pdf->Cell(0,0,$this->rh->Ferias->getAbonoPecuniario('R$  |.|,|2'),0,0,'L');

                    $linha += $fator;
                    $pdf->SetXY(1.8,$linha);
                    $pdf->Cell(0,0,"1/3 sobre Abono Pecuniário",0,0,'L');

                    $pdf->SetXY(8.5,$linha);
                    $pdf->Cell(0,0,$this->rh->Ferias->getUmTercoAbonoPecuniario('R$  |.|,|2'),0,0,'L');

                }
                
                foreach ($collection_movimentos_clt['dados']['CREDITO'] as $key => $value) {
                    
                    $linha += $fator;
                    
                    $pdf->SetXY(1.8,$linha);
                    $pdf->Cell(0,0,$value['nome_movimento'],0,0,'L');

                    $pdf->SetXY(8.5,$linha);
                    $pdf->Cell(0,0,'R$  '.number_format($value['valor_movimento'],2,',','.'),0,0,'L');
                    
                }
                
                /*
                 * Aqui finaliza a discriminação dos itens de férias
                 */

                $pdf->SetXY(8.5,17.86);
                $pdf->Cell(0,0,$this->rh->Ferias->getTotalRemuneracoes1('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(1.8,18.9);
                $pdf->Cell(0,0,"Pensão Alimentícia",0,0,'L');

                $pdf->SetXY(1.8,19.5);
                $pdf->Cell(0,0,"INSS",0,0,'L');

                $pdf->SetXY(2.8,19.5);
                $pdf->Cell(0,0,$this->rh->Ferias->getInssPorcentagem(). "%",0,0,'L');

                $pdf->SetXY(1.8,20.1);
                $pdf->Cell(0,0,"IRRF",0,0,'L');

                $pdf->SetXY(8.5,18.9);
                $pdf->Cell(0,0,$this->rh->Ferias->getPensaoAlimenticia('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(8.5,19.5);
                $pdf->Cell(0,0,$this->rh->Ferias->getInss('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(8.5,20.1);
                $pdf->Cell(0,0,$this->rh->Ferias->getIr('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(8.5,21);
                $pdf->Cell(0,0,$this->rh->Ferias->getTotalDescontos('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(8.5,21.55);
                $pdf->Cell(0,0,$this->rh->Ferias->getTotalLiquido('R$  |.|,|2'),0,0,'L');

                // Valores do 2º Mês
                if($this->rh->Ferias->getDataIni('m/Y')->val() != $this->rh->Ferias->getDataFim('m/Y')->val()) {

                    $linha = 14.4;
                    $fator = 0.5;

                    $pdf->SetXY(14.70,13.38);
                    $pdf->Cell(0,0,$this->rh->Ferias->getDataFim('m/Y'),0,0,'L');

                    $pdf->SetXY(10.4,$linha);
                    $pdf->Cell(0,0,$this->rh->Ferias->getDiasFerias2(),0,0,'L');

                    $pdf->SetXY(10.8,$linha);
                    $pdf->Cell(0,0,"dias",0,0,'L');

                    $pdf->SetXY(17.26,$linha);
                    $pdf->Cell(0,0,$this->rh->Ferias->getValorTotalFerias2('R$  |.|,|2'),0,0,'L');

                    $linha += $fator;

                    $pdf->SetXY(10.4,$linha);
                    $pdf->Cell(0,0,"Acréscimo constitucional 1/3",0,0,'L');

                    $pdf->SetXY(17.26,$linha);
                    $pdf->Cell(0,0,$this->rh->Ferias->getAcrescimoConstitucional2('R$  |.|,|2'),0,0,'L');

                    $pdf->SetXY(17.3,17.86);
                    $pdf->Cell(0,0,$this->rh->Ferias->getTotalRemuneracoes2('R$  |.|,|2'),0,0,'L');

                    $pdf->SetXY(10.4,18.9);
                    $pdf->Cell(0,0,"Base de INSS",0,0,'L');

                    $pdf->SetXY(10.4,19.5);
                    $pdf->Cell(0,0,"IRRF",0,0,'L');

                    $pdf->SetXY(17.3,18.9);
                    $pdf->Cell(0,0,$this->rh->Ferias->getBaseInss('R$  |.|,|2'),0,0,'L');

                    $pdf->SetXY(15.0,19.5);
                    $pdf->Cell(0,0,"(Apurado em {$this->rh->Ferias->getDataIni('m/Y')->val()})",0,0,'L');

                    $pdf->SetXY(17.3,21);
                    $pdf->Cell(0,0,"R$ 0,00",0,0,'L');

                    $pdf->SetXY(17.3,21.55);
                    $pdf->Cell(0,0,$this->rh->Ferias->getTotalLiquido2('R$  |.|,|2'),0,0,'L');

                }

                // Valores Finais

                $pdf->SetXY(7.5,22.1);
                $pdf->Cell(0,0,$this->rh->Ferias->getCalcSubTotalLiquido('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(11.3,22.1);
                $pdf->Cell(0,0,$this->rh->Ferias->getTotalDescontos('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(17.3,22.1);
                $pdf->Cell(0,0,$this->rh->Ferias->getTotalLiquido('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(3.4,22.71);
                $pdf->Cell(0,0,$this->rh->Empresa->getRazao() . " ( CNPJ: " . $this->rh->Empresa->getCnpj() . " )",0,0,'L');

                $pdf->SetXY(1.9,23.6);
                $pdf->Cell(0,0,"*** " . $this->valorPorExtenso($this->rh->Ferias->getTotalLiquido()) . " ***",0,0,'L');

                $pdf->SetXY(12.8,25.6);
                $pdf->Cell(0,0,$this->rh->Clt->getNome(),0,0,'L');

                $pdf->SetXY(13.5,25.9);
                $pdf->Cell(0,0,'('.$this->rh->Clt->getCpf().')',0,0,'L');

            }

            /* 
             * Salva o arquivo PDF 
             */
            if(count($arr_id_ferias)==1){

                $nomearquivo = 'ferias_'.$this->rh->Clt->getIdClt().'_'.$this->rh->Ferias->getIdFerias().'.pdf';

            }
            else {

                $date = date("YmdHis");

                $nomearquivo = "ferias_lote_{$date}.pdf";

            }

            $pdf ->Output("arquivos/{$nomearquivo}");

            $dominio = $_SERVER['HTTP_HOST'];

            $url = "http://{$dominio}/intranet/rh_novaintra/ferias/arquivos/{$nomearquivo}";

            $pdf->Close();

            switch ($return) {
                case 'pdf':

                    header("Location: $url");

                    break;

                default:

                    $return = array(
                                    "status" => 1,
                                    "url" => $url
                                    );

                    echo json_encode($return);        

                    break;

            }
            
            
        } catch (Exception $ex) {
            
            $this->error->set("A exceção no método webFeriasClass->gerarPdf impediu a finalização do processo",E_FRAMEWORK_WARNING,$ex);            

        }
        
            

    }
    
    /*
     * PHP-DOC
     * 
     * @name gerarPdf
     * 
     * @internal - Gera um arquivo de PDF para recibo de férias ou um conjunto de recibos de acordo com a definição de rh->Ferias->setIdFerias()
     */     
    protected function gerarPdf($return = 'json'){ 
        
        try {
            
            require_once('../fpdf/fpdf.php');

            define('FPDF_FONTPATH','../fpdf/font/');

            $pdf  = new FPDF("P","cm","A4");
            $pdf->SetAutoPageBreak(true,0.0); // Reduz a tolerância da margem inferior
            $pdf->Open();

            $arr_id_ferias = explode(',',$this->rh->Ferias->getIdFerias());

            /*
             * Caso o setIdFerias() esteja setado com vários IDs de férias então imprime em lote
             */
            foreach ($arr_id_ferias as $key => $id_ferias) {

                $this->rh->setDefault();        

                $this->rh->Ferias->setIdFerias($id_ferias)->select()->getRow();
                
                $this->rh->Clt->setIdClt($this->rh->Ferias->getIdClt())->select()->getRow();

                $this->rh->Ferias->setCalcMovimentosClt();            

                $collection_movimentos_clt = $this->rh->Ferias->getCollectionMovimentosClt();
                
                $this->rh->Projeto->setDefault()->select()->getRow();

                $this->rh->Curso->setDefault()->select()->getRow();

                $this->rh->Bancos->setDefault()->select()->getRow();

                $this->rh->Empresa->setDefault()->select()->getRow();
                
                $this->rh->Ferias->setCalcDiasEmDoisMeses();

                $pdf->SetFont('Arial','B',8);
                $pdf->Cell(5, 30, " ");
                
                $pdf->Image('../images/fundo_ferias.jpg', 0,0.9,20.4075,27.1771,'jpg');

                $pdf->SetXY(1.8,7.2);
                $pdf->Cell(0,0,$this->rh->Empresa->getRazao(),0,0,'L');

                $pdf->SetXY(1.8,3);
                $pdf->Cell(0,0,$this->rh->Projeto->getNome(),0,0,'L');

                $pdf->SetXY(2.8,3.54);
                $pdf->Cell(0,0,$this->rh->Clt->getIdClt()." - ".$this->rh->Clt->getNome(),0,0,'L');

                $pdf->SetXY(15.82,3.55);
                $pdf->Cell(0,0,$this->rh->Clt->getDataEntrada('d/m/Y'),0,0,'L');

                $pdf->SetXY(2.8,4.05);
                $pdf->MultiCell(0,0,$this->rh->Clt->getCampo1(),0,'L');

                $pdf->SetXY(6.9,4.05);
                $pdf->Cell(0,0,$this->rh->Clt->getLocacao(),0,0,'L');

                $pdf->SetXY(14.2,4.10);
                $pdf->Cell(0,0,$this->rh->Curso->getNome(),0,0,'L');

                $pdf->SetXY(1.8,5.7);
                $pdf->Cell(0,0,$this->rh->Ferias->getDataAquisitivoIni('d/m/Y')->val(),0,0,'L');

                $pdf->SetXY(3.4,5.7);
                $pdf->Cell(0,0,"a",0,0,'L');

                $pdf->SetXY(3.9,5.7);
                $pdf->Cell(0,0,$this->rh->Ferias->getDataAquisitivoFim('d/m/Y')->val(),0,0,'L');

                $pdf->SetXY(6.5,5.7);
                $pdf->Cell(0,0,$this->rh->Ferias->getDataIni('d/m/Y')->val(),0,0,'L');

                $pdf->SetXY(8.1,5.7);
                $pdf->Cell(0,0,"a",0,0,'L');

                $pdf->SetXY(8.4,5.7);
                $pdf->Cell(0,0,$this->rh->Ferias->getDataFim('d/m/Y')->val(),0,0,'L');

                $pdf->SetXY(11.36,5.7);
                $pdf->Cell(0,0,$this->rh->Ferias->getDataRetorno('d/m/Y')->val(),0,0,'L');

                $pdf->SetXY(14.2,5.7);
                $pdf->Cell(0,0,"",0,0,'L');

                $pdf->SetXY(15.8,5.7);
                $pdf->Cell(0,0,"",0,0,'L');

                $pdf->SetXY(16.3,5.7);
                $pdf->Cell(0,0,"",0,0,'L');

                $pdf->SetXY(5,9.2);
                $pdf->Cell(0,0,$this->rh->Clt->getNome(),0,0,'L');

                $pdf->SetXY(2.7,9.64);
                $pdf->Cell(0,0,$this->rh->Clt->getCampo1(),0,0,'L');

                $pdf->SetXY(6.8,9.64);
                $pdf->Cell(0,0,$this->rh->Clt->getLocacao(),0,0,'L');

                $pdf->SetXY(14.1,9.61);
                $pdf->Cell(0,0,$this->rh->Curso->getCampo2(),0,0,'L');

                $pdf->SetXY(2.8,10);
                $pdf->Cell(0,0,$this->rh->Bancos->getNome(),0,0,'L');

                $pdf->SetXY(10.7,10);
                $pdf->Cell(0,0,$this->rh->Clt->getAgencia(),0,0,'L');

                $pdf->SetXY(14.1,10);
                $pdf->Cell(0,0,$this->rh->Clt->getConta(),0,0,'L');

                $pdf->SetXY(1.8,10.95);
                $pdf->Cell(0,0,$this->rh->Ferias->getDataAquisitivoIni('d/m/Y')->val(),0,0,'L');

                $pdf->SetXY(3.4,10.95);
                $pdf->Cell(0,0,"a",0,0,'L');

                $pdf->SetXY(3.9,10.95);
                $pdf->Cell(0,0,$this->rh->Ferias->getDataAquisitivoFim('d/m/Y')->val(),0,0,'L');

                $pdf->SetXY(7.45,10.95);
                $pdf->Cell(0,0,$this->rh->Ferias->getDataIni('d/m/Y')->val(),0,0,'L');

                $pdf->SetXY(9.05,10.95);
                $pdf->Cell(0,0,"a",0,0,'L');

                $pdf->SetXY(9.45,10.95);
                $pdf->Cell(0,0,$this->rh->Ferias->getDataFim('d/m/Y')->val(),0,0,'L');

                $pdf->SetXY(13.4,10.95);
                $pdf->Cell(0,0,"",0,0,'L');

                $pdf->SetXY(15.2,10.95);
                $pdf->Cell(0,0,"",0,0,'L');

                $pdf->SetXY(15.7,10.95);
                $pdf->Cell(0,0,"",0,0,'L');

                $pdf->SetXY(1.8,12.4);
                $pdf->Cell(0,0,"-",0,0,'L');

                $pdf->SetXY(2,12.4);
                $pdf->Cell(0.40,0,$this->rh->Ferias->getFaltas(),0,0,'C');

                $pdf->SetXY(2.32,12.4);
                $pdf->Cell(0,0,"-",0,0,'L');

                $pdf->SetXY(5.3,12.4);
                $pdf->Cell(0,0,$this->rh->Ferias->getSalario('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(8.9,12.4);
                $pdf->Cell(0,0,$this->rh->Ferias->getSalarioVariavel('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(12.4,12.4);
                $pdf->Cell(0,0,$this->rh->Ferias->getValorTotalFerias('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(6.150,13.38);
                $pdf->Cell(0,0,$this->rh->Ferias->getDataIni('m/Y')->val(),0,0,'L');

                /*
                 * Aqui começa a discriminação dos itens de férias
                 */

                $linha = 14.4;
                $fator = 0.5;

                // Valores do 1º Mês

                $pdf->SetXY(1.8,$linha);
                $pdf->Cell(0,0,$this->rh->Ferias->getDiasFerias1(),0,0,'L');

                $pdf->SetXY(2.2,$linha);
                $pdf->Cell(0,0,"dias",0,0,'L');

                $pdf->SetXY(8.5,$linha);
                $pdf->Cell(0,0,$this->rh->Ferias->getValorTotalFerias1('R$  |.|,|2'),0,0,'L');

                $linha += $fator;

                $pdf->SetXY(1.8,$linha);
                $pdf->Cell(0,0,"Acréscimo constitucional 1/3",0,0,'L');

                $pdf->SetXY(8.5,$linha);
                $pdf->Cell(0,0,$this->rh->Ferias->getAcrescimoConstitucional1('R$  |.|,|2'),0,0,'L');

                if($this->rh->Ferias->getValorTotalFeriasDobradas() > 0) {

                    $linha += $fator;
                    $pdf->SetXY(1.8,$linha);
                    $pdf->Cell(0,0,"*Art. 137 CLT - FÉRIAS EM DOBRO",0,0,'L');
                    $pdf->SetXY(8.5,$linha);
                    $pdf->Cell(0,0,$this->rh->Ferias->getValorTotalFeriasDobradas('R$  |.|,|2'),0,0,'L');

                    $linha += $fator;
                    $pdf->SetXY(1.8,$linha);
                    $pdf->Cell(0,0,"*1/3 FÉRIAS DOBRADAS",0,0,'L');
                    $pdf->SetXY(8.5,$linha);
                    $pdf->Cell(0,0,$this->rh->Ferias->getUmTercoFeriasDobradas('R$  |.|,|2'),0,0,'L');


                }

                if($this->rh->Ferias->getVendido()) {

                    $linha += $fator;
                    $pdf->SetXY(1.8,$linha);
                    $pdf->Cell(0,0,"Abono Pecuniário",0,0,'L');

                    $pdf->SetXY(8.5,$linha);
                    $pdf->Cell(0,0,$this->rh->Ferias->getAbonoPecuniario('R$  |.|,|2'),0,0,'L');

                    $linha += $fator;
                    $pdf->SetXY(1.8,$linha);
                    $pdf->Cell(0,0,"1/3 sobre Abono Pecuniário",0,0,'L');

                    $pdf->SetXY(8.5,$linha);
                    $pdf->Cell(0,0,$this->rh->Ferias->getUmTercoAbonoPecuniario('R$  |.|,|2'),0,0,'L');

                }

                foreach ($collection_movimentos_clt['dados']['CREDITO'] as $key => $value) {

                    $linha += $fator;

                    $pdf->SetXY(1.8,$linha);
                    $pdf->Cell(0,0,$value['nome_movimento'],0,0,'L');

                    $pdf->SetXY(8.5,$linha);
                    $pdf->Cell(0,0,'R$  '.number_format($value['valor_movimento'],2,',','.'),0,0,'L');

                }

                /*
                 * Aqui finaliza a discriminação dos itens de férias
                 */

                $pdf->SetXY(8.5,17.86);
                $pdf->Cell(0,0,$this->rh->Ferias->getTotalRemuneracoes1('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(1.8,18.9);
                $pdf->Cell(0,0,"Pensão Alimentícia",0,0,'L');

                $pdf->SetXY(1.8,19.5);
                $pdf->Cell(0,0,"INSS",0,0,'L');

                $pdf->SetXY(2.8,19.5);
                $pdf->Cell(0,0,$this->rh->Ferias->getInssPorcentagem(). "%",0,0,'L');

                $pdf->SetXY(1.8,20.1);
                $pdf->Cell(0,0,"IRRF",0,0,'L');

                $pdf->SetXY(8.5,18.9);
                $pdf->Cell(0,0,$this->rh->Ferias->getPensaoAlimenticia('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(8.5,19.5);
                $pdf->Cell(0,0,$this->rh->Ferias->getInss('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(8.5,20.1);
                $pdf->Cell(0,0,$this->rh->Ferias->getIr('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(8.5,21);
                $pdf->Cell(0,0,$this->rh->Ferias->getTotalDescontos('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(8.5,21.55);
                $pdf->Cell(0,0,$this->rh->Ferias->getTotalLiquido('R$  |.|,|2'),0,0,'L');

                // Valores do 2º Mês
                if($this->rh->Ferias->getDataIni('m/Y')->val() != $this->rh->Ferias->getDataFim('m/Y')->val()) {

                    $linha = 14.4;
                    $fator = 0.5;

                    $pdf->SetXY(14.70,13.38);
                    $pdf->Cell(0,0,$this->rh->Ferias->getDataFim('m/Y'),0,0,'L');

                    $pdf->SetXY(10.4,$linha);
                    $pdf->Cell(0,0,$this->rh->Ferias->getDiasFerias2(),0,0,'L');

                    $pdf->SetXY(10.8,$linha);
                    $pdf->Cell(0,0,"dias",0,0,'L');

                    $pdf->SetXY(17.26,$linha);
                    $pdf->Cell(0,0,$this->rh->Ferias->getValorTotalFerias2('R$  |.|,|2'),0,0,'L');

                    $linha += $fator;

                    $pdf->SetXY(10.4,$linha);
                    $pdf->Cell(0,0,"Acréscimo constitucional 1/3",0,0,'L');

                    $pdf->SetXY(17.26,$linha);
                    $pdf->Cell(0,0,$this->rh->Ferias->getAcrescimoConstitucional2('R$  |.|,|2'),0,0,'L');

                    $pdf->SetXY(17.3,17.86);
                    $pdf->Cell(0,0,$this->rh->Ferias->getTotalRemuneracoes2('R$  |.|,|2'),0,0,'L');

                    $pdf->SetXY(10.4,18.9);
                    $pdf->Cell(0,0,"Base de INSS",0,0,'L');

                    $pdf->SetXY(10.4,19.5);
                    $pdf->Cell(0,0,"IRRF",0,0,'L');

                    $pdf->SetXY(17.3,18.9);
                    $pdf->Cell(0,0,$this->rh->Ferias->getBaseInss('R$  |.|,|2'),0,0,'L');

                    $pdf->SetXY(15.0,19.5);
                    $pdf->Cell(0,0,"(Apurado em {$this->rh->Ferias->getDataIni('m/Y')->val()})",0,0,'L');

                    $pdf->SetXY(17.3,21);
                    $pdf->Cell(0,0,"R$ 0,00",0,0,'L');

                    $pdf->SetXY(17.3,21.55);
                    $pdf->Cell(0,0,$this->rh->Ferias->getTotalLiquido2('R$  |.|,|2'),0,0,'L');

                }

                // Valores Finais

                $pdf->SetXY(7.5,22.1);
                $pdf->Cell(0,0,$this->rh->Ferias->getTotalRemuneracoes('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(11.3,22.1);
                $pdf->Cell(0,0,$this->rh->Ferias->getTotalDescontos('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(17.3,22.1);
                $pdf->Cell(0,0,$this->rh->Ferias->getTotalLiquido('R$  |.|,|2'),0,0,'L');

                $pdf->SetXY(3.4,22.71);
                $pdf->Cell(0,0,$this->rh->Empresa->getRazao() . " ( CNPJ: " . $this->rh->Empresa->getCnpj() . " )",0,0,'L');

                $pdf->SetXY(1.9,23.6);
                $pdf->Cell(0,0,"*** " . $this->valorPorExtenso($this->rh->Ferias->getTotalLiquido()) . " ***",0,0,'L');

                $pdf->SetXY(12.8,25.6);
                $pdf->Cell(0,0,$this->rh->Clt->getNome(),0,0,'L');

                $pdf->SetXY(13.5,25.9);
                $pdf->Cell(0,0,'('.$this->rh->Clt->getCpf().')',0,0,'L');

            }

            /* 
             * Salva o arquivo PDF 
             */
            if(count($arr_id_ferias)==1){

                $nomearquivo = 'ferias_'.$this->rh->Clt->getIdClt().'_'.$this->rh->Ferias->getIdFerias().'.pdf';

            }
            else {

                $date = date("YmdHis");

                $nomearquivo = "ferias_lote_{$date}.pdf";

            }

            $pdf ->Output("arquivos/{$nomearquivo}");

            $dominio = $_SERVER['HTTP_HOST'];

            $url = "http://{$dominio}/intranet/rh_novaintra/ferias/arquivos/{$nomearquivo}";

            $pdf->Close();

            switch ($return) {
                case 'pdf':

                    header("Location: $url");

                    break;

                default:

                    $return = array(
                                    "status" => 1,
                                    "url" => $url
                                    );

                    echo json_encode($return);        

                    break;

            }
            
            
        } catch (Exception $ex) {
            
            $this->error->set("A exceção no método webFeriasClass->gerarPdf impediu a finalização do processo",E_FRAMEWORK_WARNING,$ex);            

        }
        
            

    }
    
    
    /*
     * PHP-DOC
     * 
     * @name mergePDF
     * 
     * @internal - Executa um merge de PDF via classe para impressão em lote (EM FASE DE IMPLEMENTAÇÃO)
     */    
    protected function mergePDFa(){
        
        require_once('../fpdf/fpdf.php');

        define('FPDF_FONTPATH','../fpdf/font/');
        
        $pdf  = new FPDF("P","cm","A4");
        
        
        $pdf = new PDFMerger;

        $pdf->addPDF('../tools/PDFMerger/samplepdfs/three.pdf', 'all')
        ->addPDF('../tools/PDFMerger/samplepdfs/three.pdf', 'all')
        ->addPDF('../tools/PDFMerger/samplepdfs/three.pdf', 'all')
        ->merge('file', '../tools/PDFMerger/samplepdfs/TEST22.pdf');      
        
    }
    
    /*
     * PHP-DOC
     * 
     * @name mergePDF
     * 
     * @internal - Executa um merge de PDF via prompt de comando linux para impressão em lote (EM FASE DE IMPLEMENTAÇÃO)
     */    
    protected function mergePDFb(){
        
        $fileArray= $filer;

        $datadir = "../dir/";
        
        $outputName = $datadir."/lote_ferias.pdf";


        $cmd = "gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=$outputName ";
        
        
        foreach($filer as $file) {
            
            $cmd .= $file." ";

        }
        
        $result = shell_exec($cmd);        
        
    }

    protected function telaNaoFerias(){
        
        /*
         * Verifica se houve algum erro na operação
         */
        $html = $this->getAlertHtml($this->rh->Ferias->error->getAll(),"danger");
        
        
        ?>
        <div class="note note-warning text-center">
             <h4><?=!empty($this->rh->Clt->GetIdClt()) ? $this->rh->Clt->GetIdClt().' - '.$this->rh->Clt->GetNome() : ''?></h4>
        </div>                                        

         <div class="panel-body" style="max-height: 250px;">
            <form class="form-horizontal" action="" enctype="multipart/form-data" method="post" name="form" id="form">
                <div class="form-group col-lg-12" id="message_erro">
                    <?php
                    /*
                     * Caso haja algum retorno de erro desabilita o botão de processamento
                     */
                    echo $html;
                    ?>
                </div>
                
            </form>             
         </div>
         <?php   
         
    }
    
    /*
     * PHP-DOC 
     * 
     * @name correcao
     * 
     * @internal - Método para correção de lançamentos na tabela rh_ferias_itens. A correção pode tanto ser feita por atualização quanto por inclusão,
     *             bastanto para isso a inexistência dos registros na tabela supra citada.
     * 
     */      
    protected function correcao(){
        
        try {

            $arr_id_ferias = explode(',',$this->rh->Ferias->getIdFerias());

            $id_ferias = $arr_id_ferias[++$arr_id_ferias[0]];

            if(!empty($id_ferias)){

                $this->rh->Ferias->setIdFerias($id_ferias);
                
                if($this->rh->Ferias->select()->getRow()){
                    
                    /*
                     * Fazer verificação da existência dos itens para atualizaçã ou exclusão.
                     */

                    $this->update();

                    $list_id_ferias = implode(',',$arr_id_ferias);

                    echo "Id de férias {$id_ferias} atualizado...";
                    echo "<script>window.location = 'ferias_processar.php?method=correcao&id_ferias={$list_id_ferias}';</script>";
                    
                }

            }
            else {

                echo "Finalizado";

            }
            
        } catch (FrameWorkException $obj) {
            
            $this->rh->error->set('Uma excessão no método correcao da classe webFerias impediu a finalização do processo',E_FRAMEWORK_ERROR,$obj);
            
        }         

    }
    
    /*
     * PHP-DOC 
     * 
     * @name update
     * 
     * @internal - Método para atualização apenas dos registros distribuídos em dois meses na tabela rh_ferias e rh_ferias_itens.
     *             Caso não exista um item ou todos em férias itens o método Ferias->update() irá incluir.
     * 
     */     
    protected function update(){
        
        try {
            
            $this->rh->Ferias->setCalcValoresDistribuidosEmDoisMeses();
            
            if($this->rh->Ferias->getValorTotalFerias1() == 0 || $this->rh->Ferias->getTotalRemuneracoes1() == 0 || $this->rh->Ferias->getTotalLiquido1() == 0) echo print_array ($this->rh->Ferias); //$this->rh->error->set('Existem valores vazios no método update da classe webFerias que impediram a finalização do processo',E_FRAMEWORK_ERROR);

            $this->rh->Ferias->update();     
            
            /*
             * Gera o evento para a classe Clt a fim de informar que houve alteração nos registros vinculados a classe
             */
            $this->rh->Clt->onUpdate();
            
        
        } catch (FrameWorkException $obj) {
            
            $this->rh->error->set('Uma excessão no método update da classe webFerias impediu a finalização do processo',E_FRAMEWORK_ERROR,$obj);
            
        }         
            
    }    

    /*
     * PHP-DOC - Method 
     * Verifica faltas e eventos para concessão de férias do período aquisitivo selecionado.
     * Verifica os eventos de licença médica e acidente de trabalho com mais de 180 dias.
     */
    protected function chkFaltasAbonoPecuniario(){
        
        /*
         * Caso não tenha sido instânciado a classe eventos, cria-se a instância para execução do método
         */
        if(is_object($this->rh->Ferias) && is_object($this->rh->MovimentosClt)){
            
            $chk_ignorar_faltas = $this->rh->Ferias->getIgnorarFaltas();

            $this->rh->Ferias->setCalcDataFim();

            $qnt_dias_limite_ferias = $this->rh->Ferias->getCalcLimiteDiasFeriasPorFalta();

            $qnt_dias_abono_pecuniario = $this->rh->Ferias->getCalcDiasAbonoPecuniario();

            $qnt_dias_sem_abono_pecuniario = $qnt_dias_limite_ferias;

            $qnt_dias_com_abono_pecuniario = $qnt_dias_limite_ferias - $qnt_dias_abono_pecuniario;

            $qnt_faltas = $this->rh->Ferias->getFaltas();

            $return = array(
                            "status" => 1,
                            "qnt_dias_sem_abono_pecuniario" => $qnt_dias_sem_abono_pecuniario,
                            "qnt_dias_com_abono_pecuniario" => $qnt_dias_com_abono_pecuniario,
                            "qnt_dias_limite_ferias" => $qnt_dias_limite_ferias,
                            "chk_ignorar_faltas" => $chk_ignorar_faltas,
                            "qnt_faltas" => $qnt_faltas
                            );
            
        }
        else {

            $return = array(
                            "status" => 0,
                            "texto" => $this->error->set("Método webFerias->chkFaltasAbonoPecuniario() não pode ser executado porque a classe rh->Ferias ou rh->MovimentosClt não está instanciada",E_FRAMEWORK_ERROR)->getAll()
                            );
            
        }

        echo json_encode($return);        
        
    }
    
    /*
     * PHP-DOC - Method - Faz o levantamento de das férias já gozadas
     */
    protected function chkFeriasGozadas(){
        
        $this->rh->Ferias->setCalcDataFim();
        
        $gozadas = $this->rh->Ferias->select('ano,mes');
        
        $return = array(
                        "status" => 1,
                        "ferias_gozadas" => $gozadas
                        );

        echo json_encode($return);        
        
    }  
    
    /*
     * PHP-DOC - Method - Verifica a situação do Clt e faz o lançamento das férias caso esteja em atividade normal 
     */
    protected function insertFerias(){
        
        $return = array(
                        "status" => 0,
                        "mensagem" => ""
                        );
        
        $this->rh->Clt->select()->getRow(); 
        
//        if($this->rh->Clt->getStatus()!=10){
//            
//            /*
//             * Necessário verificar se o status do clt não está compatível com o último lançamento
//             */
//            
//            $this->error->set("Funcionário não está em atividade normal",E_FRAMEWORK_NOTICE);
//            
//            $return["mensagem"] = iconv("iso-8859-1","utf-8",$this->rh->Ferias->error->getAllMsgCode()); 
//            
//            exit(json_encode($return));   
//            
//        }
        
        
        /*
         * Obtem os dados do Clt para inclusão
         */
        $id_clt = $this->rh->Clt->getIdClt();
        $nome = $this->rh->Clt->getNome();
        $id_regiao = $this->rh->Clt->getIdRegiao();
        $id_projeto = $this->rh->Clt->getIdProjeto(); 
        
        /*
         * Executa o calculo das férias a partir do Clt
         */
        $this->rh->Ferias->setCalcFerias();

        /*
         * Obtem os dados de Calculo de Férias referente ao Clt que ainda não foram carregados na classe férias
         */
        $this->rh->Ferias->setRegiao($id_regiao);
        $this->rh->Ferias->setProjeto($id_projeto);
        $this->rh->Ferias->setIdClt($id_clt);
        $this->rh->Ferias->setNome($nome);
        $this->rh->Ferias->setFaltasAno('');
        $this->rh->Ferias->setMesDt('');
        
        $this->rh->Ferias->setDataProc(date("YmdHis", mktime()));
        $this->rh->Ferias->setStatus(1);
        
        /*
         * Inicia o controle de uma transação distribuiída no Framework
         */
        $this->rh->db->setTransaction();     
        
        if($this->rh->Ferias->insert()){

            /*
             * Gera o evento para a classe Clt a fim de informar que houve alteração nos registros vinculados a classe
             */
            $this->rh->Clt->onUpdate();
            
            /*
             * Caso a operação de inserção do cabeçario das férias tenha sido bem sucedido então retorna a chave gerada
             */
            $this->rh->db->commit();
            
            $id = $this->rh->Ferias->getIdFerias();
            
            $return["status"] = $id;
            
            $return["mensagem"] = iconv("iso-8859-1","utf-8","Férias de {$nome} lançadas com Sucesso na chave ({$id})"); 

            
        }
        else {
            
            /*
             * Caso tenha acontecido algum erro na operação de inclusão de Férias desfaz a operação
             */
            $this->rh->db->RollBack();
            
            $return["mensagem"] = iconv("iso-8859-1","utf-8",$this->rh->Ferias->error->getAllMsgCode()); 
            
        }

        echo json_encode($return);        
        
        
    }
    
    /*
     * Modo de compatibilidade com URI que contenha o parámetro enc (encrypt)
     */
    protected function getEnc(){
        
        return str_replace("+", "--", encrypt("{$this->rh->Clt->getIdRegiao()}&{$this->rh->Clt->getIdClt()}"));
        
    }
    
    function valorPorExtenso($valor=0) {
        
	$singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
	$plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
	$c = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
	$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
	$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove");
	$u = array("", "um", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
	$z=0;

	$valor = number_format($valor, 2, ".", ".");

	$inteiro = explode(".", $valor);

	for($i=0;$i<count($inteiro);$i++)
		for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
			$inteiro[$i] = "0".$inteiro[$i];

	// $Fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
	$fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);

	for ($i=0;$i<count($inteiro);$i++) {
		$valor = $inteiro[$i];
		$rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
		$rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
		$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

		$r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd && $ru) ? " e " : "").$ru;
		$t = count($inteiro)-1-$i;
		$r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
		
		if ($valor == "000")$z++; elseif ($z > 0) $z--;

		if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t]; 

		if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;

	}
        
	return($rt ? $rt : "zero");
        
}
    

} // Final da Class webFerias


/*
 * PHP-DOC - Main - Módulo principal de execução da classe webFerias
 */
$webFerias = new webFeriasClass();

$webFerias->action();


