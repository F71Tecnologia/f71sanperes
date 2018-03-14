<?php
/*
 * PHP-DOC  
 * 
 * Procedimentos para listagem de rescis�es
 * 
 * Obs.: Sequ�ncia de par�metros para defini��o da chave de calculo (Tipo de Desligamento / Fator / Aviso Pr�vio / Tempo)
 * 
 * 29-02-2016
 * 
 * @package RhResicsaoProcessar.php
 * @access public  
 * 
 * @version
 * 
 * Vers�o: 3.0.0000 - 10/03/2015 - Jacques - Vers�o Inicial
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
 * PHP-DOC - Classe para controle da v�rias tela de lan�amento de f�rias
 */

class webRescisaoProcessarClass extends webClass {
    
    private $rh;
    private $date;
    private $user;
    private $funcionario;
    
    protected function setUser($value) {
        
        $this->user = $value;
        
    }
    
    protected function setBreadCrumb(){
        
        $usuario = carregaUsuario();
        
        $this->setTitle(':: Intranet :: Rescis�o');
                        
        $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); 
        
        $breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Rescis�o");

        $breadcrumb_pages = array("Gest�o de RH"=>"/intranet/rh/principalrh.php");
        
        include("../../template/navbar_default.php"); 
        
        
    }
    /*
     * PHP-DOC - Carrega os Css respons�veis pelo layout da p�gina
     */
    protected function setCssExt(){
        ?>
                
                <style>
                </style>
        <?php
    }
    /*
     * PHP-DOC - Carrega os JavaScripts da utilizados na p�gina
     */    
    protected function setJavaScriptExtFooter(){
        
        $datetime = new DateTime();
        
        ?>
                <script>
                    $(function(){            

                        $("#form").validationEngine();
                        
                        $('#data_demi').datepicker({
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
                        
                        $('#btn_calcular_rescisao').click(function () {
                            
                            var id_clt = 0;
                            var motivo = 0;
                            var tipo_aviso = 0;
                            var data_demi = '';
                            var chk = 0;
                        
//                            var div_message_erro = $("#message_erro");
//                            var data_demi_fmt = $("#data_demi_fmt").val();
//                            var data = new String(data_demi_fmt);
//                            
//                            data = data.replace("/", "").replace("/", "");
//                            
//                            var dia = data.substr(0, 2);
//                            var mes = data.substr(2, 2);
//                            var ano = data.substr(4, 4);
//                            
//                            var data_demi = ano+'-'+mes+'-'+dia;
                            
//                            abono_pecuniario = $("#chk_abono_pecuniario").is(':checked') ? 1 : 0;
//                            ignorar_faltas = $("#chk_ignorar_faltas").is(':checked') ? 1 : 0;
                            
                            BootstrapDialog.show({
                            type: BootstrapDialog.TYPE_INFO,                             
                            title: "C�lculo de Rescis�o",
                            message: $("<div id='modal'></div>").load("rescisao_processar.php?method=telaModalCalculaRescisao&id_clt="+id_clt+"&motivo="+motivo+"tipo_aviso="+tipo_aviso+"&data_demi="+data_demi+"&chk="+chk),
                            closable: false,
                            buttons: [
                                {
                                    label: "Lan�ar Rescis�o",
                                    cssClass: "btn-primary",
                                    hotkey: 13,
                                    autospin: true,                                        
                                    action: function (dialog) {

                                        dialog.enableButtons(false);
                                        dialog.setClosable(false);
                                        dialog.getModalBody().html("Lan�ando Rescis�o ... ");

                                        console.log(data_ini);

                                        $.ajax({
                                            url: "rescisao_processar.php",
                                            type: "POST",
                                            dataType: "json",
                                            data: {
                                                method: "insertRescisao",
                                                id_clt: id_clt,
                                                data_aquisitivo_ini: data_aquisitivo_ini,
                                                data_aquisitivo_fim: data_aquisitivo_fim,
                                                data_ini: data_ini,
                                                chk_abono_pecuniario: abono_pecuniario,
                                                chk_ignorar_faltas: ignorar_faltas
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
                                        dialog.getModalBody().html("Executando Lan�amento ... ");

                                        BootstrapDialog.show({
                                            type: BootstrapDialog.TYPE_INFO,                           
                                            title: "Lan�amento de F�rias",
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
                                                            dialog.getModalBody().html('Gerando Visualiza��o ...');

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
                        
                        
                        /**
                         * Excluir rescis�o
                         */
                        $("body").on("click",".remove_recisao",function() {
                            
                            $("#CancelAviso").show();
                            thickBoxModal("Desprocessar Recis�o", "#CancelAviso", 500, 600);
                            $("#idCanRescisao").val($(this).attr("data-recisao"));
                            $("#idCanRegiao").val($(this).attr("data-regiao"));
                            $("#idCanClt").val($(this).attr("data-clt"));

                        });

                        $(".btn").click(function (){
                            if ($(this).val() == 'Sim') {
                                var id_rescisao = $("#idCanRescisao").val();
                                var id_regiao = $("#idCanRegiao").val();
                                var id_clt = $("#idCanClt").val();
                                var tpCanAvisoPr = $("#tpCancelAvisoPre").val();
                                var obs = $("#obsCancel").val();
                                $.ajax({
                                    url:"",
                                    type:"POST",
                                    dataType:"json",
                                    data: {
                                        tpCanAvisoPr: tpCanAvisoPr,
                                        obs: obs,
                                        id_rescisao: id_rescisao,
                                        id_regiao: id_regiao,
                                        id_clt: id_clt,
                                        method: "desprocessar_recisao"
                                    },
                                    success: function(data) {
                                        if(!data.status){
                                            $(data.dados).each(function(k, v) {
                                                $(".data_demissao").html(v.data_demissao);
                                                $(".data_pagamento").html(v.data_pg);
                                                $(".nome").html(v.nome_clt);
                                                $(".status").html(v.status_saida);
                                                $(".valor").html(v.valor);
                                            });
                                            $("#mensagens").show();
                                            thickBoxModal("Desprocessar Recis�o", "#mensagens", "500", "600");
                                        }else{
                                            history.go(0);
                                        }
                                    }
                                });
                            }

                        });

                        /*
                         * Consulta participantes aguardando rescis�o no projeto
                         */
                        $("body").on("click", "input[value='Consultar']", function () {

                            var regiao  = $("#regiao").val();
                            var projeto = $("#projeto_lista").val();
                            var mes = $("#txt_mes").val();
                            var ano = $("#txt_ano").val();
                            var pesquisa = $("#pesquisa").val();

                            $.ajax({
                                url: "",
                                type: "post",
                                dataType: "json",
                                data: {
                                    regiao:regiao,
                                    projeto: projeto,
                                    txt_mes: mes,
                                    txt_ano: ano,
                                    pesquisa: pesquisa,
                                    method:"listaParticipantesAguardando"
                                },
                                success: function (data) {

                                    if(data != null && data !== undefined){
                                        var html = "";

                                        html += "<h4 class='valign-middle'>";
                                        html += "<i class='fa fa-chevron-right'></i>";
                                        html += " PARTICIPANTES AGUARDANDO DEMISS�O";
                                        html += "</h4>";

                                        html += "<table class='table table-striped table-hover table-condensed table-bordered' id='tbRelatorio'>";
                                        html += "<thead>";

                                        html += "<tr class='bg-primary valign-middle'>";
                                        html += "<th class='text-center' style='width:4%;'>COD</th>";
                                        html += "<th style='width:25%;'>NOME</th>";
                                        html += "<th class='text-center' style='width:15%;'>PROJETO</th>";
                                        html += "<th class='text-center' style='width:10%;'>UNIDADE</th>";
                                        html += "<th class='text-center' style='width:15%;'>CARGO</th>";
                                        html += "<th class='text-center' style='width:5%;'>A��O</th>";
                                        html += "</tr>";

                                        html += "</thead>";                        
                                        html += "<tbody>";

                                        $.each(data,function(key,dados){
                                            html += "<tr class='valign-middle' data-key='" + key + "'>";
                                            html += "<td>" + key + "</td>";
                                            html += "<td><a href='" + dados.link + "'>" + dados.nome + "</a></td>";
                                            html += "<td>" + dados.projeto + "</td>";
                                            html += "<td>" + dados.unidade + "</td>";
                                            html += "<td>" + dados.funcao + "</td>";
                                            html += "<td class='text-center'><a class='btn btn-danger btn-xs  excluir_aguardando' href='javascript:;' data-url='" + dados.link2 + "' data-key='" + key + "'><i class='bt-image fa fa-trash-o tooo' data-original-title='Desprocessar Aguardando Rescis�o' data-toggle='tooltip' data-placement='top'></i></a></td>";
                                            html += "</tr>";
                                        });

                                        html += "</tbody>";
                                        html += "</table>";

                                        $("#retorno-lista-aguardando-demissao").html(html);
                                    }               
                                }
                            });

                            /**
                            * LISTA DE PARTICIPANTES DESATIVADOS
                            **/
                            $.ajax({
                                url: "",
                                type: "post",
                                dataType: "json",
                                data: {
                                    regiao:regiao,
                                    projeto: projeto,
                                    txt_mes: mes,
                                    txt_ano: ano,
                                    pesquisa: pesquisa,
                                    method:"listaParticipantes"
                                },
                                success: function (data) {

                                    if(data != null && data !== undefined){

                                        var html = "";

                                        html += "<h4 class='valign-middle'>";
                                        html += "<i class='fa fa-chevron-right'></i>";
                                        html += " PARTICIPANTES DESATIVADOS";
                                        html += "</h4>";

                                        html += "<table class='table table-striped table-hover table-condensed table-bordered' id='tbRelatorio'>";
                                        html += "<thead>";

                                        html += "<tr class='bg-primary valign-middle'>";
                                        html += "<th class='text-center' style='width:5%;'>COD</th>";
                                        html += "<th style='width:35%;'>NOME</th>";
                                        html += "<th class='text-center' style='width:15%;'>PROJETO</th>";
                                        html += "<th class='text-center' style='width:10%;'>DATA</th>";
                                        html += "<th class='text-center' style='width:10%;'>RESCIS�O</th>";
                                        html += "<th class='text-center' style='width:10%;'>COMPLEMENTAR</th>";
                                        html += "<th class='text-center' style='width:5%;'>ADD</th>";
                                        html += "<th class='text-center' style='width:20%;'>VALOR</th>";
                                        html += "<th class='text-center' style='width:5%;'>A��O</th>";
                                        html += "</tr>";

                                        html += "</thead>";                        
                                        html += "<tbody>";

                                        $.each(data,function(key,dados){
                                            html += "<tr class='valign-middle'>";
                                            html += "<td>" + key + "</td>";
                                            html += "<td>" + dados.nome + "</td>";
                                            html += "<td>" + dados.projeto + "</td>";
                                            html += "<td>" + dados.data + "</td>";
                                            html += "<td class='text-center'><a href='" + dados.link + "' class='btn btn-default btn-xs'><i class='text-danger fa fa-file-pdf-o' alt='Ver PDF'></i></a></td>";
                                            html += "<td class='text-center'>";
                                                $.each(dados.complementa, function(k,linkComplementar){
                                                    html += "<a href='" + linkComplementar + " 'class='btn btn-default btn-xs'><i class='text-danger fa fa-file-pdf-o' alt='Ver PDF'></i></a>";
                                                })
                                            html += "</td>";
                                            html += "<td class='text-center'><a class='btn btn-default btn-xs' href='" + dados.add_complementa + "' title='Adicionar Complementar'><i class='fa fa-plus'></i></a></td>";
                                            html += "<td>" + dados.liquido + "</td>";
                                            html += "<td class='text-center'><a class='btn btn-danger btn-xs remove_recisao' href='javascript:;' title='Desprocessar Rescis�o' data-clt='" + key + "' data-recisao='" + dados.rescisao + "'><i class='bt-image fa fa-trash-o tooo' data-original-title='Desprocessar Rescis�o' data-toggle='tooltip' data-placement='top'></i></a></td>";
                                            html += "</tr>";
                                        });

                                        html += "</tbody>";
                                        html += "</table>";

                                        $("#retorno-lista").html(html);
                                    }
                                }
                            });

                        });

                        /**
                        * EXCLUIR AGUARDANDO DEMISS�O
                         */
                        $("body").on("click",".excluir_aguardando",function(){
                            var url = $(this).data("url");
                            var key = $(this).data("key");

                            BootstrapDialog.confirm('Remover Participante de Aguardando Demiss�o?', 'Confirma��o', function(result) {
                                if (result) {                    
                                    $.ajax({
                                        url:url,
                                        type:'get',
                                        dataType:'json',
                                        success:function(data){
                                            if(data.status){
                                                $("tr[data-key='"+key+"']").hide();
                                            }
                                        }
                                    });
                                }
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
     * PHP-DOC 
     * 
     * @name action
     * 
     * @internal - A��o a ser executada pela classe
     * 
     * Obs.: O Campo matr�cula foi adicionado com o objetivo de agrupar v�rias cadastros de Clt para uma mesma
     *       rescis�o, pois o Clt pode trabalhar em regi�es ou projetos diferentes atrav�z de uma �nica contrata��o
     * 
     */
    public function action(){

        try {

        
            header ('Content-type: text/html; charset=ISO-8859-1');            

            $this->setBuild('7831');
            $this->setPageTitle('<h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Rescis�o</small></h2>');
            $this->funcionario = new funcionario();
            $this->date = new DateClass();
            $this->rh = new RhClass();

            /* 
             * carrega vari�veis passadas por POST ou GET
             */
            $id_rescisao =  isset($_REQUEST['id_rescisao']) ? $_REQUEST['id_rescisao'] : 0; 
            $id_regiao =  isset($_REQUEST['regiao']) && $_REQUEST['regiao'] > 0 ? $_REQUEST['regiao'] : $this->funcionario->id_regiao; 
            $id_projeto =  isset($_REQUEST['projeto']) && $_REQUEST['projeto'] > 0 ? $_REQUEST['projeto'] : 0;
            $id_clt = isset($_REQUEST['id_clt']) ? $_REQUEST['id_clt'] : 0;
            $id_matricula = isset($_REQUEST['id_matricula']) ? $_REQUEST['id_matricula'] : 0;
            $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : ''; 
            
            $this->funcionario->MostraUser(0);
            $funcionario = $this->funcionario->id_funcionario;      

            /*
             * Inst�ncia as classes do framework
             * 
             * Obs: � importante utilizar o inst�ncimanto na ordem em que v�o acontecer 
             *      a sele��o de dados para se poder fazer uso da Macro $this->rh->select() e $this->rh->getRow()
             */

            $this->rh->AddClassExt('Clt'); 
            $this->rh->AddClassExt('Curso');
            $this->rh->AddClassExt('Projeto');
            $this->rh->AddClassExt('Rescisao');
            $this->rh->AddClassExt('Eventos');
            $this->rh->AddClassExt('Status');
            
            $this->rh->setDefault(); 

            $this->rh->Clt->setIdRegiao($id_regiao);
            $this->rh->Clt->setIdProjeto($id_projeto);
            $this->rh->Clt->setIdClt($id_clt);

            $this->rh->Clt->select()->getRow();
            $this->rh->Curso->select()->getRow();
            $this->rh->Projeto->select()->getRow();
            $this->rh->Rescisao->select()->getRow();
            $this->rh->Eventos->select()->getRow();

            $this->setMethodExt($method); 
            
            switch ($this->getMethodExt()) {
                case 'telaForm':
                    $this->showPage($this->getMethodExt());
                    break;
                case 'telaRecisao':
                    $this->showPage($this->getMethodExt());
                    break;
                default:
                    $this->exeMethodExt($this->getMethodExt());
                    break;
            }

            
        } catch (Exception $ex) {
            
            $this->error->set("Uma exe��o em webRescisaoProcessarClass->action() impediu a finaliza��o do processo",E_FRAMEWORK_ERROR,$ex);
            
        }        
        
    }
 
    /*
     * PHP-DOC Tela 1 - Tela de filtragem e exibi��o de funcion�rios desativados e aguardando demiss�o
     */
    protected function telaForm(){
        
        $dominio = $this->getDominio()
        
        ?>
            <div class="row">
                <div class="col-lg-12">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
                        <li role="presentation" class="active"><a href="#lista" role="tab" data-toggle="tab">Lista de Funcion�rios</a></li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">

                        <div role="tabpanel" class="tab-pane active" id="lista">

                            <form class="form-horizontal" role="form" id="form-lista" method="post">
                                <div class="panel panel-default hidden-print">
                                    <div class="panel-body">

                                        <div class="form-group">
                                            <label for="projeto_lista" class="col-lg-2 control-label">Projeto:</label>
                                            <div class="col-lg-9">
                                                <select name="projeto" id="projeto_lista" class="form-control">
                                                    <option value="00">� Todos �</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="projeto_lista" class="col-lg-2 control-label">Compet�ncia:</label>
                                            <div class="col-lg-4">
                                                <div class="input-group">
                                                    <select name="txt_mes" id="txt_mes" class="form-control">
                                                        <option value="00">� Todos �</option>
                                                        <option value="01">Janeiro</option>
                                                        <option value="02">Fevereiro</option>
                                                        <option value="03">Mar�o</option>
                                                        <option value="04">Abril</option>
                                                        <option value="05">Maio</option>
                                                        <option value="06">Junho</option>
                                                        <option value="07">Julho</option>
                                                        <option value="08">Agosto</option>
                                                        <option value="09">Setembro</option>
                                                        <option value="10">Outubro</option>
                                                        <option value="11">Novembro</option>
                                                        <option value="12">Dezembro</option>
                                                    </select>
                                                    <div class="input-group-addon"></div>
                                                    <select name="txt_ano" id="txt_ano" class="form-control">
                                                        <option>� Todos �</option>
                                                        <?=$this->rh->lib->getMakeHtmlOption((int)$this->rh->date->now()->minusYear(5)->val('Y'),(int)$this->rh->date->now()->val('Y'))?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="nome_clt" class="col-lg-2 control-label">Filtro:</label>
                                            <div class="col-lg-9"><input type="text" name="pesquisa" id="pesquisa" class="form-control" placeholder="Nome do CLT, CPF, Matr�cula"></div>
                                        </div>

                                    </div><!-- /.panel-body -->

                                    <div class="panel-footer text-right">                                       
                                        <input type="button" value="Consultar" class="btn btn-primary">                                        
                                        <a href="rescisao_processar.php" target="_blank" class="btn btn-warning">Rescis�o em Lote</a>
                                    </div>

                                </div><!-- /.panel -->
                                <div id="retorno-lista-aguardando-demissao">
                                    
                                </div>
                                <div id="retorno-lista">
                                    
                                </div>
                            </form>

                        </div><!-- /#lista -->
                    </div>
                </div><!-- /.col-lg-12 -->
                <div id="CancelAviso" style="display: none;">
                    <p>
                        <input type="hidden" id="idCanRescisao"/>
                        <input type="hidden" id="idCanRegiao"/>
                        <input type="hidden" id="idCanClt"/>
                    </p>
                    <p>Motivo do Cancelamento do Aviso Previo:</p>
                    <p><select id="tpCancelAvisoPre" name="tpCancelAvisoPre" class="validate[required]">
                            <option value="">Selecione...</option>
                        <?php
                             $qr_canAvisoPre = mysql_query("SELECT id_tipoCanAvisoPre, descricao FROM tipo_cancelamento_aviso_previo;");
                             while ($rowAvisoPre = mysql_fetch_assoc($qr_canAvisoPre)) {
                        ?>
                                <option value="<?= $rowAvisoPre['id_tipoCanAvisoPre'] ?>"><?= $rowAvisoPre['descricao'] ?></option>
                        <?php } ?>
                        </select>
                    </p>
                    <p>Observa��o:</p>
                    <p><textarea id="obsCancel" name="obsCancel" cols="30" rows="5"></textarea></p>
                    <p class="controls">
                        <input type="button"  class="btn" value="Sim"/>
                    </p>
                </div>
            </div><!-- /.row -->
         <?php   
         
    }
    
    /*
     * PHP-DOC 
     * 
     * @name telaRecisao
     * 
     * @internal - Esse m�todo exibe a tela para defini��o das condi��es rescis�rias
     * 
     */
    protected function telaRecisao(){
        
        $this->rh->Clt->setDefault()->setIdClt(9189)->onUpdate();
        
        $dominio = $_SERVER['HTTP_HOST'];
        
        $collection_status = $this->rh->Status->setDefault()->setMagneticKey(0)->setTipo('recisao')->setMotivo('desligamento')->select()->db->getCollection('codigo_fmt');
        
        $html = $this->getAlertHtml($this->rh->getAllMsgCode(),"danger");
        
        ?>
        <div class="note note-warning text-center">
             <h4><?=!empty($this->rh->Clt->GetIdClt()) ? $this->rh->Clt->GetIdClt().' - '.$this->rh->Clt->GetNome() : ''?></h4>
        </div>                                        
            
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
            <li role="presentation" class="active"><a href="#rescisao" role="tab" data-toggle="tab">Rescis�o</a></li>
            <li role="presentation"><a href="#tabela_legislacao" role="tab" data-toggle="tab">Tabelas de Legisla��o</a></li>
            <li role="presentation"><a href="#memoria_de_calculo" role="tab" data-toggle="tab">Mem�ria de C�lculo</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="rescisao">
                <div class="panel-body">
                    <form class="form-horizontal" action="" enctype="multipart/form-data" method="post" name="form" id="form">
                        <div class="form-group" id="div_lancamento">
                            <label for="nome_clt" class="col-lg-2 control-label">Data de Admiss�o:</label>
                            <div class="col-lg-2">
                                <input type="text" name="pesquisa" id="pesquisa" class="form-control" placeholder="teste" value="<?=$this->rh->Clt->getDataEntrada('d/m/Y');?>" disabled>
                            </div>
                            <div class="col-lg-8">
                            </div>
                        </div>     
                        <div class="form-group">
                            <label for="motivo" class="col-lg-2 control-label">Motivo:</label>
                            <div class="col-lg-10">
                                <select name="motivo" id="tipo_desligamento" class="form-control">
                                    <option>� Todos �</option>
                                    <?=$this->rh->lib->getMakeHtmlOption($collection_status['dados'],'especifica,codigo_fmt')?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tipo_aviso_previo" class="col-lg-2 control-label">Tipo de Aviso:</label>
                            <div class="col-lg-10">
                                <select name="tipo_aviso_previo" id="tipo_aviso_previo" class="form-control">
                                    <option>Trabalhado</option>
                                    <option>Indenizado</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="div_lancamento">
                            <div class="panel-body">
                                <div class="col-lg-2">
                                    <label class="control-label">Data de Demiss�o:</label>   
                                </div>
                                <div class="col-lg-2">
                                    <div id="data_demi" class='input-group date'>
                                        <input type='text' id="data_demi_fmt" name="data_demi_fmt" class="form-control span2" onKeyUp="mascara_data(this)" onChange="$('#btn_calcular_ferias').prop('disabled', false);" readonly="true" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar">
                                            </span>
                                        </span>
                                    </div>                                    
                                    <span class="add-on"><i class="icon-th"></i></span>
                                </div>
                                <div>
                                    <label for="nome_clt" class="control-label col-lg-2">In�cio do Aviso:</label>
                                    <div class="col-lg-2">
                                        <input type="text" name="pesquisa" id="pesquisa" class="form-control col-lg-3" placeholder="teste">
                                    </div>
                                </div>     
                                <div class="col-lg-4" id="div_label_dias_ferias">
                                </div>
                                <div class="col-lg-2" id="div_dias_ferias">
                                </div>
                            </div>
                        </div>   
                        <div class="form-group">
                            <label for="tempo_de_servico" class="col-lg-2 control-label">Tempo de Servi�o:</label>
                            <div class="col-lg-2">
                                <input type="text" name="pesquisa" id="pesquisa" class="form-control" placeholder="teste">
                            </div>
                            <div class="col-lg-8">
                            </div>
                        </div>     
                        <div class="form-group">
                            <div class="col-lg-12 panel-footer text-center">
                                <input type="button" class="btn btn-success" name="btn_calcular_rescisao" id="btn_calcular_rescisao" value="CALCULAR RESCIS�O">
                            </div>
                        </div>
                    </form>
                </div>    
            </div>            
            <div role="tabpanel" class="tab-pane active" id="tabela_legislacao">
                <div class="panel-body">
                    <form class="form-horizontal" action="" enctype="multipart/form-data" method="post" name="form" id="form">
                        <div id="div_form">
                        </div>
                    </form>     
                </div>
            </div>
         </div>
            
        <div id="message_erro">
            <?=$html;?>
        </div>
            
         <?php   
         
    }

    /*
     * PHP-DOC - Method - Verifica a situa��o do Clt e faz o lan�amento das f�rias caso esteja em atividade normal 
     */
    protected function insertRescisao(){
        
        $return = array(
                        "status" => 0,
                        "mensagem" => ""
                        );
        
        $this->rh->Clt->select();
        $this->rh->Clt->getRow();
        
        if($this->rh->Clt->getStatus()!=10){
            
            /*
             * Necess�rio verificar se o status do clt n�o est� compat�vel com o �ltimo lan�amento
             */
            $return["mensagem"] = $this->getAlertHtml($this->rh->error->setError('"Funcion�rio n�o est� em atividade normal"')->getError(),"danger");
            
            echo json_encode($return);   
            
            return 0;
            
        }
        
        /*
         * Obtem os dados do Clt para inclus�o
         */
        $id_clt = $this->rh->Clt->getIdClt();
        $nome = $this->rh->Clt->getNome();
        $id_regiao = $this->rh->Clt->getIdRegiao();
        $id_projeto = $this->rh->Clt->getIdProjeto(); 
        
        /*
         * Executa o calculo das f�rias a partir do Clt
         */
        $this->rh->Ferias->setCalcFerias();


        /*
         * Obtem os dados de Calculo de F�rias referente ao Clt que ainda n�o foram carregados na classe f�rias
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
         * Inicia o controle de uma transa��o distribui�da no Framework
         */
        $this->rh->db->setTransaction();     
        
        if($this->rh->Ferias->insert()){
            
            /* 
             * Caso tenha sucesso na inclus�o do registro, atualiza seu status
             */
            $this->rh->Clt->setDefault();
            $this->rh->Clt->setIdClt($id_clt);
            $this->rh->Clt->setStatus(40);
            $this->rh->Clt->update();
            
            
            /*
             * temporariamente desfaz a inclus�o para evitar incluir na base real
             */
//            $this->rh->db->RollBack();
            
            
            /*
             * Caso a opera��o de inser��o do cabe�ario das f�rias tenha sido bem sucedido ent�o retorna a chave gerada
             */
            $this->rh->db->commit();
            
            $id = $this->rh->Ferias->getIdFerias();
            
            $return["status"] = $id;
            
            $return["mensagem"] = iconv("iso-8859-1","utf-8","F�rias de {$nome} lan�adas com Sucesso na chave ({$id})"); //$this->getAlertHtml("F�rias de {$nome} lan�adas com Sucesso na chave ({$id})","success");

            echo json_encode($return);        
            
            
        }
        else {
            
            /*
             * Caso tenha acontecido algum erro na opera��o de inclus�o de F�rias desfaz a opera��o
             */
            $this->rh->db->RollBack();
            
            $return["mensagem"] = $this->getAlertHtml($this->rh->error->setError('N�o foi poss�vel realizar a inclus�o das f�rias')->getError(),"danger");
            
            echo json_encode($return);        
            
            
        }

               
        
    }
    
    /*
     * Modo de compatibilidade com URI que contenha o par�metro enc (encrypt)
     */
    protected function getEnc(){
        
        return str_replace("+", "--", encrypt("{$this->rh->Clt->getIdRegiao()}&{$this->rh->Clt->getIdClt()}"));
        
    }
    

} // Final da Class webNovaRescisao


/*
 * PHP-DOC - Main - M�dulo principal de execu��o da classe webFerias
 */

$webRescisaoProcessar = new webRescisaoProcessarClass();

$webRescisaoProcessar->action();


