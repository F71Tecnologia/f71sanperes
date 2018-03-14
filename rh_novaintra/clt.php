<?php
/*
 * clt.php
 * 
 * 16-12-2015
 * 
 * Tela de listagem de Clt por Status
 * 
 * Versão: 3.0.0000 - 16/12/2015 - Jacques - Versão Inicial
 * 
 * @author jacques@f71.com.br
 * 
 */

include('../conn.php');  
include('../funcoes.php');
include('../classes/global.php');
include('../classes/DateClass.php');
include('../wfunction.php');
include('../classes_permissoes/regioes.class.php');
include('../classes_permissoes/acoes.class.php');
include('../classes/funcionario.php');
include('../classes/webClass.php');
include('../classes/RhClass.php');
include('../classes/abreviacao.php');
include('../classes/formato_data.php');


/*
 * PHP-DOC - Classe para exibição e edição de clts
 */

class webCltClass extends webClass {
    
    private $rh;
    private $date;
    private $user;
    private $funcionario;

    protected function setBreadCrumb(){
        
        $usuario = carregaUsuario();
        
        $this->setTitle(':: Intranet :: Edição de Participantes');
                        
        $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); 
        
        $breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Relatório de Rescisões");

        $breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php");
        
        include("../template/navbar_default.php"); 

        
    }
    /*
     * PHP-DOC - Carrega os Css responsáveis pelo layout da página
     */
    protected function setCssExt(){
        ?>
                
                <style>

                </style>
        <?php
    }
    /*
     * PHP-DOC - Carrega os JavaScripts já utilizados na página
     */    
    protected function setJavaScriptExtFooter(){
        ?>      
                <script src="../js/jquery-1.10.2.min.js"></script>
                <script src="../resources/js/bootstrap.min.js"></script>
                <script src="../resources/js/bootstrap-dialog.min.js"></script>
                <script src="../js/jquery.validationEngine-2.6.js"></script>
                <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
                <script src="../resources/js/main.js"></script>
                <script src="../js/global.js"></script>
                <script type="text/javascript">
                $().ready(function(){
                    $('.show').click(function() {
                        $('.panel-body').hide();
                        var div = $(this).data('key');
                        console.log(div);
                        $(div).show();
                        $(this).addClass('ativo');
                        $('.show').not(this).removeClass('ativo');
                    });
                    /*$('#botao_localizacao').click(function() {
                        $('#localizacao').show();
                        $('#botao_localizacao').hide();
                    });
                    $('#fecha_localizacao').click(function() {
                        $('#localizacao').hide();
                        $('#botao_localizacao').show();
                    });*/
                    $('.participante').click(function(){
                        console.log($(this).attr("href"));
                    });
                });

                function ajaxFunction(){
                    
                    var xmlHttp;
                    try {
                        xmlHttp=new XMLHttpRequest();
                    } catch (e) {
                        try {
                            xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
                        } catch (e) {
                            try {
                                xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
                            } catch (e) {
                                alert("Your browser does not support AJAX!");
                                return false;
                            }
                        }
                    }
                    xmlHttp.onreadystatechange=function() {
                        if(document.getElementById('username').value == ''){
                            document.all.ttdiv.style.display="none";
                        }else{
                            document.all.ttdiv.style.display="";
                            if(xmlHttp.readyState==3){
                                document.all.tbdiv.innerHTML="<div align='center' style='background-color:#5C7E59'><img src='../imagens/carregando/CIRCLE_BALL.gif' align='absmiddle'>Aguarde</div>";
                            }else if(xmlHttp.readyState==4){
                                document.all.tbdiv.innerHTML=xmlHttp.responseText;
                            }
                        }
                    }

                    var enviando = escape(document.getElementById('username').value);
                    
                    xmlHttp.open("GET",'clt.php?procura=' + enviando + '&id=1&id_regiao=<?=$id_regiao?>',true);
                    
                    xmlHttp.send(null);

                }
                </script>
        <?php
        
    }

    /*
     * PHP-DOC - Ação a ser executada pela classe
     */
    public function action(){
        
        header ('Content-type: text/html; charset=ISO-8859-1');     
        
        $this->setUser(carregaUsuario()); 
        $this->setBuild('5167');
        $this->setPageTitle('<h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Edição de Participantes</small></h2>');

        $this->funcionario = new funcionario();
        $this->date = new DateClass();
        $this->rh = new RhClass();
        
        /* 
         * carrega variáveis passadas por POST ou GET$usuario['id_regiao']
         */
        $id_regiao =  isset($_REQUEST['regiao']) ? $_REQUEST['regiao'] : $this->getUser('id_regiao'); 
        $id_projeto =  isset($_REQUEST['projeto']) && $_REQUEST['projeto'] > 0 ? $_REQUEST['projeto'] : 0;
        $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : ''; 
        $busca  = isset($_REQUEST['procura']) ? $_REQUEST['procura'] : ''; 
        
        
        $this->funcionario->MostraUser(0);
        $funcionario = $this->funcionario->id_funcionario;      
        
        /*
         * Instância as classes do framework
         * 
         * Obs: É importante utilizar o instâncimanto na ordem em que vão acontecer 
         *      a seleção de dados para se poder fazer uso da Macro $this->rh->select() e $this->rh->getRow()
         */
        
        $this->rh->AddClassExt('Clt');
        $this->rh->AddClassExt('Projeto');
        $this->rh->AddClassExt('Status');
        $this->rh->AddClassExt('Curso');

        /*
         * Macro do Framework para setar valores padrões em todas as classes instânciadas 
         */
        $this->rh->setDefault(); 
        
        $this->rh->Clt->setIdRegiao($id_regiao);
        $this->rh->Clt->setIdProjeto($id_projeto);
        $this->rh->Clt->setSearch($busca,'nome','and');

        $this->setMethodExt($method); 
        
        if(empty($this->getMethodExt())){
            
            $this->showPage('telaFiltro');
            
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
            <div class="row">
                <div class="col-xs-10">
                    <form id="form1" method="post">
                        <input type="hidden" name="home" id="home" value="" />
                        <input type="text" name="username" placeholder="Insira o nome do participante" onKeyUp="ajaxFunction();" id="username" class="form-control pull-left hidden-xs" />
                    </form>
                </div>
                <div class="col-xs-2">
                    <a class="btn btn-success pull-right" href="cadastroclt.php?id_regiaoiao=<?=$id_regiao?>&pagina=clt"><i class="fa fa-plus"></i> Novo Cadastro</a>
                </div>
                <div class="col-xs-5" style="z-index: 10;">
                    <table class="table table-bordered" id="ttdiv" style="position: absolute; display:none; background-color: #FFF;">
                        <tbody id="tbdiv">
                            <!--tr>
                                <td><span style="font-size:13px;" id="spantt"></span></td>
                            </tr-->
                        </tbody>
                    </table>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-xs-12">
                    <?php 
                    $count_eventos = 0;
                    $count_projetos  = 0;
                    $count_clts = 0;

                    $collection_status = $this->rh->Status->select()->getCollection('codigo');

                    $collection_projeto = $this->rh->Projeto->select()->getCollection('id_projeto');
                    
                    $collection_curso = $this->rh->Curso->select()->getCollection('id_curso');

                    $collection_clt_status = $this->rh->Clt->select()->getCollection('status,id_projeto,id_clt','','status');
                    
                    foreach ($collection_clt_status['dados'] as $id_status => $itens_status) {   

                        $count_eventos++;

                        ?>
                        <div class="panel panel-primary">
                    
                            <div class="panel-heading pointer show" data-key=".<?=$id_status?>">
                                <h3 class="panel-title"><?=$collection_status['dados'][$id_status]['especifica']?> (<?=$collection_clt_status['count']['group'][$id_status]['status']?>)</h3>
                            </div>
                            <div class="panel-body table-responsive <?=$id_status?>" <?php if(1 == 1) { echo 'style="display:none;"'; } ?>>
                                <?php
                                foreach ($itens_status as $id_projetos => $itens_clt) {

                                    $count_projeto++;
                                    ?>
                                    <table class="table table-striped table-hover <?=$id_status?>">
                                        <h4 class="td_show <?=$id_status?>">
                                            <i class="fa fa-chevron-right"></i> <?=$collection_projeto['dados'][$id_projetos]['nome']?> <!--span class="pull-right"><a class="btn btn-success" href="javascript:;" onclick="tableToExcel('tbRelatorio3315', 'Relatório')"><i class="fa fa-file-excel-o"></i> Exportar para Excel</a></span-->
                                        </h4>

                                        <thead>
                                            <tr class="novo_tr">
                                                <th>&nbsp;</th>
                                                <th width="5%" align="center">COD</th>
                                                <th width="30%">&nbsp;&nbsp;NOME</th>
                                                <th width="25%">&nbsp;&nbsp;CARGO</th>
                                                <th width="20%" align="center"><?php if($id_status== 10) { echo 'ENTRADA'; } else { echo 'DURA&Ccedil;&Atilde;O'; } ?></th>
                                                <th width="10%" align="center">PONTO</th>
                                                <th width="10%" align="center">DOCUMENTOS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($itens_clt as $id_clt => $clt) {


                                                $count_clts++;


                                                $this->rh->Clt->getRow($clt);

                                                if($this->rh->Clt->getAssinatura() == 1) {
                                                        $botao1 = '<a href="ver_tudo.php?id=18&id_projeto='.$this->rh->Clt->getIdProjeto().'&id_regiao='.$this->rh->Clt->getIdRegiao().'&ass=0&bolsista='.$this->rh->Clt->getIdClt().'&tipo=1&tab=rh_clt" title="Remover ASSINATURA do Contrato de '.$this->rh->Clt->getNome().'">
                                                                                  <img src="../imagens/assinado.gif" alt="Contrato">
                                                                           </a>';
                                                } else {
                                                        $botao1 = '<a href="ver_tudo.php?id=18&id_projeto='.$this->rh->Clt->getIdProjeto().'&id_regiao='.$this->rh->Clt->getIdRegiao().'&ass=1&bolsista='.$this->rh->Clt->getIdClt().'&tipo=1&tab=rh_clt" title="Alterar o Contrato para ASSINADO de '.$this->rh->Clt->getNome().'">
                                                                                  <img src="../imagens/naoassinado.gif" alt="Contrato">
                                                                           </a>';
                                                }

                                                if($this->rh->Clt->getDistrato() == 1) {
                                                        $botao2 = '<a href="ver_tudo.php?id=18&id_projeto='.$this->rh->Clt->getIdProjeto().'&id_regiao='.$this->rh->Clt->getIdRegiao().'&ass=0&bolsista='.$this->rh->Clt->getIdClt().'&tipo=2&tab=rh_clt" title="Remover ASSINATURA do Distrato de '.$this->rh->Clt->getNome().'">
                                                                        <img src="../imagens/assinado.gif" alt="Distrato">
                                                                    </a>';
                                                } else {
                                                        $botao2 = '<a href="ver_tudo.php?id=18&id_projeto='.$this->rh->Clt->getIdProjeto().'&id_regiao='.$this->rh->Clt->getIdRegiao().'&ass=1&bolsista='.$this->rh->Clt->getIdClt().'&tipo=2&tab=rh_clt" title="Alterar o Distrato para ASSINADO de '.$this->rh->Clt->getNome().'">
                                                                                  <img src="../imagens/naoassinado.gif" alt="Distrato">
                                                                           </a>';
                                                }

                                                if($this->rh->Clt->getOutros() == 1) {
                                                        $botao3 = '<a href="ver_tudo.php?id=18&id_projeto='.$this->rh->Clt->getIdProjeto().'&id_regiao='.$this->rh->Clt->getIdRegiao().'&ass=0&bolsista='.$this->rh->Clt->getIdClt().'&tipo=3&tab=rh_clt" title="Remover ASSINATURA de Outros Documentos de '.$this->rh->Clt->getNome().'">
                                                                                  <img src="../imagens/assinado.gif" alt="Outros Documentos">
                                                                           </a>';
                                                } else {
                                                        $botao3 = '<a href="ver_tudo.php?id=18&id_projeto='.$this->rh->Clt->getIdProjeto().'&id_regiao='.$this->rh->Clt->getIdRegiao().'&ass=1&bolsista='.$this->rh->Clt->getIdClt().'&tipo=3&tab=rh_clt" title="Alterar Outros Documentos para ASSINADO de '.$this->rh->Clt->getNome().'">
                                                                                  <img src="../imagens/naoassinado.gif" alt="Outros Documentos">
                                                                           </a>';
                                                }

                                                if(strstr($this->rh->Clt->getCampo3(),'INSERIR')) { 
                                                    $classe = 'amarelo'; $classe = 'warning'; 
                                                } elseif(strstr($this->rh->Clt->getLocacao(),'A CONFIRMAR')) { 
                                                    $classe = 'vermelho'; $classe = 'danger'; 
                                                } elseif($this->rh->Clt->getFoto() == '1') {
                                                    $classe = 'verde_foto'; $classe = 'success'; 
                                                } elseif(!empty($this->rh->Clt->getObservacao())) {
                                                    $classe = 'amarelo'; $classe = 'warning'; 
                                                    $observacao = 'title="Observações: '.$this->rh->Clt->getObservacao().'"';
                                                } else {
                                                    $classe = 'verde';$classe = 'info'; 
                                                }
                                                ?>
                                                <tr>    
                                                    <td class="<?=$classe?>">&nbsp;</td>
                                                    <td><?=$this->rh->Clt->getIdClt()?></td>
                                                    <td align="left">
                                                        <a href="ver_clt.php?id_regiao=<?=$this->rh->Clt->getIdRegiao()?>&id_clt=<?=$this->rh->Clt->getIdClt()?>&id_projeto=<?=$this->rh->Clt->getIdProjeto()?>&pagina=clt&caminho=1" class="participante" title="Editar cadastro de <?=$this->rh->Clt->getNome()?>">
                                                            <?=abreviacao($this->rh->Clt->getNome(), 4, 1)?>
                                                        </a>
                                                    </td>
                                                    <td align="left">
                                                        &nbsp;&nbsp;<?=str_replace('CAPACITANDO EM', '',$collection_curso['dados'][$this->rh->Clt->getIdCurso()]['nome'])?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        if($this->rh->Clt->getStatus() == 40) {
//                                                            echo $this->rh->Clt->getFeriasDif();
                                                        } 
                                                        elseif(in_array($this->rh->Clt->getStatus(),$eventos_rescisao)) {
//                                                            echo $this->rh->Clt->getRecisaoDif();
                                                        } 
                                                        elseif($this->rh->Clt->getStatus() != 10) {
//                                                            echo $this->rh->Clt->getEventoDif();
                                                        } 
                                                        else {
//                                                            echo $this->rh->Clt->getEventoData('d/m/Y');
                                                        } ?>
                                                    </td>
                                                    <td><a href="../folha_ponto.php?id=2&unidade=&id_regiao=<?=$this->rh->Clt->getIdRegiao()?>&id_projeto=<?=$this->rh->Clt->getIdProjeto()?>&id_bol=<?=$this->rh->Clt->getIdClt()?>&tipo=clt&caminho=0" title="Gerar folha de ponto para <?=$this->rh->Clt->getNome()?>">Gerar</a></td>
                                                    <td><?=$botao1.' '.$botao2.' '.$botao3?></td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                            <?php
                            }
                            ?>
                            </div><!--table-responsive-->
                        </div><!--panel-primary-->                   
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
                
        <?php
        
        
    }    
    
    /*
     * PHP-DOC - Tela 1 - Tela inicial para geração do relatório de rescisões
     */
    protected function telaSearch(){
        
        $collection = $this->rh->Folha->getCollectionRescisoes();

        $dominio = $_SERVER['HTTP_HOST'];

        $this->rh->Clt->setDefault();
        $this->rh->Clt->setIdRegiao($id_regiao);
        $this->rh->Clt->setNome($busca);
        $this->rh->Clt->setSearch();

        if(empty($this->rh->Clt->getTot()) or empty($busca) or strlen($busca) == 1 or strlen($busca) == 2) {
            
            echo '<tr><td><a href="#" style="color:#C30; text-decoration:none; display:block; padding:3px; padding-left:5px;">Sua busca n&atilde;o retornou resultado</a></td></tr>';
            
        } 
        else {
            
            while($this->rh->Clt->getRow()) {
                echo '
                <tr><td><a class="busca" href="ver_clt.php?id_regiao='.$this->rh->Clt->getIdRegiao().'&id_clt='.$this->rh->Clt->getIdClt().'&id_projeto='.$this->rh->Clt->getIdProjeto().'&pagina=clt"
                          onclick="document.all.ttdiv.style.display=none; 
                          document.all.username.value='.$this->rh->Clt->getNome().'">'.$this->rh->Clt->getNome().'</a></td></tr>';
            }
            
        }
        
    }    
                    
} // Final da Class webCltClass


/*
 * PHP-DOC - Main - Módulo principal de execução da classe webRelatorioRescisao
 */

$webCltRescisao = new webCltClass();

$webCltRescisao->action();


