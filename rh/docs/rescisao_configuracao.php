<?php
error_reporting(E_ALL);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include("../../conn.php");
include("../../classes/CltClass.php");
include("../../classes/RescisaoClass.php");
include("../../wfunction.php");
include "../../classes/LogClass.php";
$log = new Log();

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$objClt = new CltClass();
$objRescisao = new Rescisao();

// Verificando se existe dados de demissão pré-determinados pelo Portal
$clt = $_REQUEST['clt'] ?: $_REQUEST['id_clt'];
$qr_solicitacao = mysql_query("SELECT * FROM portal_rescisao_solicitacoes WHERE id_clt = '{$clt}' AND status_id = 1");
$row_solicitacao = mysql_fetch_assoc($qr_solicitacao);
$total_solicitacao = mysql_num_rows($qr_solicitacao);
if($total_solicitacao) {
	$qr_solicitacao_mensagem = mysql_query("SELECT * FROM portal_rescisao_mensagens WHERE solicitacao_id = '{$row_solicitacao[id]}' AND status_id = 1");
	$row_solicitacao_mensagem = mysql_fetch_assoc($qr_solicitacao_mensagem);
	$total_solicitacao_mensagem = mysql_num_rows($qr_solicitacao_mensagem);
}

// Envio de Formulário
if(validate($_REQUEST['gerar'])){
    $dados = $_REQUEST;

    //REMOVENDO VARIAVEIS Q NÃO VAI SALVAR
    unset($dados['instrucoes']);
    unset($dados['gerar']);
    unset($dados['clt']);
    //SALVANDO DADOS DO FORMULÁRIO
    $idRescisaoCltConf = $objRescisao->inserePreRescisao($dados);

    if ($dados['tipo'] == 60) {
        $dados['data_aviso'] = $dados['data_demi'];
    } 
    //PASSAR O CLT PARA STATUS 200
    $objClt->setCltAguardandoDemissao($dados['id_clt'],$dados['data_demi'],$dados['data_aviso']);
    
    //VERIFICANDO TIPO DE RESCISÃO PARA IMPRESSÃO DO DOC CORRETO
    switch ($dados['tipo']) {
        case 60:
            $formulario = "aviso_previo_trabalhado"; //ITALO VAI MODIFICAR
            break;
        case 61:
            $formulario = ($_REQUEST['aviso']==1) ? "aviso_previo_trabalhado" : "aviso_previo_indenizado"; //*
            break;
        case 62:
            $formulario = "aviso_previo_trabalhado"; //ESTA SEM AINDA
            break;
        case 63:
            $formulario = "pedido_demissao_interrupcao_contrato";//*
            break;
        case 64:
            $formulario = "interrupcao_contrato_experiencia"; //*
            break;
        case 65:
            $formulario = ($_REQUEST['aviso']==1) ? "pedido_demissao_aviso_trabalhado" : "pedido_demissao_descontando_aviso"; //*
            break;
        case 66:
            $formulario = "termino_contrato_experiencia";
            break;
        default:
            $formulario = "aviso_previo_trabalhado";
            break;
    }
    
    // Update do status na solicitação do portal
    $instrucoes = $_POST['instrucoes'];
    if($total_solicitacao) {
    	mysql_query("UPDATE portal_rescisao_solicitacoes SET status_id = 3 WHERE id_clt = '{$clt}' LIMIT 1");
    	if($instrucoes) {
    		mysql_query("INSERT INTO portal_rescisao_mensagens (solicitacao_id, status_id, texto, data) VALUES ({$row_solicitacao[id]}, 3, '{$instrucoes}', NOW())");
    	}
    }
    
    // Redirecionamento pro Documento
    $log->gravaLog('Rescisão', "Rescisão ID{$row_solicitacao[id]}, finalizada");
    header("Location: ../../relatorios/gerar_relatorio.php?documento={$formulario}&clt={$dados['id_clt']}");
    
    exit;
}

$clt = $objClt->carregaClt($_REQUEST['clt']);
$optionsFator = array("1"=>"Empregado", "2"=>"Empregador");
$optionsAviso = array("1"=>"Trabalhado", "2"=>"Indenizado", "3" =>"Ausencia/Dispensa");

/*echo "<pre>";
print_r($clt);
echo "</pre>";

echo "<br><pre>";
print_r($tpRescisao);
echo "</pre>";*/

?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Dados para a Rescisão</title>

        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <style>
            select[readonly] {
                pointer-events: none;
            }
            select[disabled] {
                pointer-events: none;
            }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Dados para a Rescisão</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">
                
                <div class="bs-callout bs-callout-danger" id="callout-helper-pull-navbar"> 
                    <input type="hidden" name="id_clt" value="<?php echo $clt->id_clt ?>" />
                    <h4><?php echo $clt->nome ?></h4> 
                    <p>Data de Admissão: <code><?php echo $clt->data_entrada ?></code></p>
                    <p>Fim do Período de Experiência: <code><?php echo date("d/m/Y", strtotime($clt->data_fim_experiencia)) ?></code> <?php if($clt->data_fim_experiencia > date('Y-m-d')){ ?><span class="label label-warning">Em experiência</span> <?php } ?></p>
                    <p>Função e Salário: <code><?php echo $clt->nome_curso." ".$clt->letranumero." - R$ ".$clt->salario ?></code></p>
                    <p>Unidade: <code><?php echo $clt->unidade ?></code></p>
                <?php if($total_solicitacao) { ?>
					<p>Data de Demissão Sugerida: <code><?php echo implode('/', array_reverse(explode('-', $row_solicitacao['data_demissao']))); ?></code></p>
                <?php } ?>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Dados para Rescisão</div>
                    <div class="panel-body">
                        <div class="form-group">

                            <label for="tipo" class="col-sm-2 control-label hidden-print" >Tipo</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($objRescisao->listTiposRescisao("array"), $row_solicitacao['tipo_demissao'], array('name' => "tipo", 'id' => 'tipo', 'class' => 'validate[required,custom[select]] form-control')); ?>
                            </div>
                            
                            <label for="fator" class="col-sm-1 control-label hidden-print" >Fator</label>
                            <div class="col-sm-4">
                                <?php echo montaSelect($optionsFator, null, array('name' => "fator", 'id' => 'fator', 'class' => 'validate[required,custom[select]] form-control')); ?>
                            </div>

                        </div>
                        
                        <div class="form-group">
                            <label for="aviso" class="col-sm-2 control-label hidden-print">Aviso prévio</label>
                            <div class="col-sm-4">    
                                <?php echo montaSelect($optionsAviso, $row_solicitacao['tipo_aviso'], array('name' => "aviso", 'id' => 'aviso', 'class' => 'validate[required,custom[select]] form-control')); ?>
                                <span class="naoseaplica-tipo hidden">Não se aplica</span>
                            </div>
                            
                            <label for="fator" class="col-sm-1 control-label hidden-print" >Dias Aviso</label>
                            <div class="col-sm-3">
                                <input type="text" class="col-sm-2 form-control" name="dias_aviso" id="dias_aviso" placeholder="Dias" />                                
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="data_demi" class="col-sm-2 control-label hidden-print">Data do Aviso</label>
                            <div class="col-lg-3">
                                <div class="input-daterange input-group" id="bs-datepicker">
                                    <input type="text" class="input form-control data validate[required]" name="data_aviso" id="data_aviso" placeholder="Data Aviso" value=""/>
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>                                    
                                </div>
                                <span class="naoseaplica-tipo hidden">Não se aplica</span>
                            </div>
                            
                            <label for="data_demi" class="col-sm-2 control-label hidden-print">Data de Demissão</label>
                            <div class="col-lg-4">
                                <input type="text" class="input form-control data validate[required]" name="data_demi" id="data_demi" placeholder="Ultimo dia Trabalhado" value="<?php echo implode('/', array_reverse(explode('-', $row_solicitacao['data_demissao']))); ?>"/>                                
                                <!--<input type="text" class="input form-control validate[required]" name="data_demi" id="data_demi" readonly="true" placeholder="Ultimo dia Trabalhado"/>-->
                            </div>
                        </div>
                        
                        <!--div class="form-group">
                            <label for="data_ini" class="col-sm-1 control-label hidden-print">Período</label>
                            <div class="col-lg-6">
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <input type="text" class="input form-control data" name="data_ini" id="data_ini" readonly="true" placeholder="Inicio" value="<?php echo $dt_ini ?>">
                                    <span class="input-group-addon ate">até</span>
                                    <input type="text" class="input form-control data" name="data_fim" id="data_fim" readonly="true" placeholder="Fim" value="<?php echo $dt_fim ?>">
                                    <span class="input-group-addon ate"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div-->
                        
                    <?php if($total_solicitacao) { ?>
                    
                        <div class="form-group">
                        	<label class="col-sm-2 control-label hidden-print">Motivo</label>
                            <div class="col-lg-3">
                                <em>
                                <?php
                                if(isset($total_solicitacao_mensagem) and $total_solicitacao_mensagem) {
                                	echo $row_solicitacao_mensagem['texto'];
                                } else {
                                	echo 'Nenhuma mensagem';
                                }
                                ?>
                                </em>
                            </div>
                        	<label for="instrucoes" class="col-sm-2 control-label hidden-print">Instruções</label>
                            <div class="col-lg-4">
                                <textarea id="instrucoes" name="instrucoes" class="form-control"></textarea>
                            </div>
                        </div>
                        
                   <?php } ?>
                        
                        <div class="panel-footer text-right hidden-print controls">
                            <button type="submit" name="gerar" id="gerar" value="gerar" class="btn btn-primary"><span class="fa fa-save"></span> Continuar</button>
                        </div>
                        
                    </div>
                </div>
            </form>

            <?php include('../../template/footer.php'); ?>
            <div class="clear"></div>
        </div>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function () {
               $("#form").validationEngine();
               
               $("#tipo").change(function () {
                   selecionadoTipo($(this).val());
               });
               
                $("body").on("change", "input[name='data_aviso'],input[name='dias_aviso']", function () {
                    calculaDataAviso();
                });
            });
            
            acoesObjetos = function(acao){
                resetaOpcoes();
                if(acao !== null){
                    for(var i = 0, len = acao.length; i < len; ++i) {
                        console.log(acao[i][1]);
                        if(acao[i][0] === "a"){
                            $(acao[i][1]).addClass("hidden");
                        }else if(acao[i][0] === "r"){
                            $(acao[i][1]).removeClass("hidden");
                        }else if(acao[i][0] === "s"){
                            $(acao[i][1]).val(acao[i][2]);
                        }else if(acao[i][0] === "b"){
                            $(acao[i][1]).attr("disabled","true");
                        }else if(acao[i][0] === "st"){
                            $(acao[i][1]).val(acao[i][2]);
                            $(acao[i][1]).attr("disabled","true");
                        }else if(acao[i][0] === "sb"){
                            $(acao[i][1]).val(acao[i][2]);
                            $(acao[i][1]).attr("readonly","true");
                        }
                    }
                }
            };
            
            selecionadoTipo = function(tipo){
                switch (tipo){
                    case "60":
                        acoesObjetos([["a","#aviso"],["r",".naoseaplica-tipo"],["s","#dias_aviso","30"]]);
                        $("#bs-datepicker").addClass("hidden");
                        $("#naoseaplica-tipo").removeClass("hidden");
                        break;
                    case "61":
                        acoesObjetos([["st","#fator","2"],["s","#dias_aviso","30"]]);
                        $("#bs-datepicker").removeClass("hidden");
                        break;
                    case "62":
                        acoesObjetos(null);
                        $("#bs-datepicker").removeClass("hidden");
                        break;
                    case "63":
                        acoesObjetos([["st","#fator","1"],["st","#aviso","2"],["a","#dias_aviso"]]);
                        $("#bs-datepicker").removeClass("hidden");
                        break;
                    case "64":
                        acoesObjetos([["st","#fator","2"],["st","#aviso","2"],["a","#dias_aviso"]]);
                        $("#bs-datepicker").removeClass("hidden");
                        break;
                    case "65":
                        acoesObjetos([["st","#fator","1"],["s","#dias_aviso","30"]]);
                        $("#bs-datepicker").removeClass("hidden");
                        break;
                    case "66":
                        //acoesObjetos([["a","#aviso"],["r",".naoseaplica-tipo"],["a","#dias_aviso"]]);
                        acoesObjetos([["sb","#aviso","3"],["a","#dias_aviso"]]);
                        $("#bs-datepicker").removeClass("hidden");
                        //PREENCHE A DATA QUE VEM DO CADASTRO DE CLT QUE DETERMINA O FIM DO CONTRATO
                        break;
                    case "81":
                        acoesObjetos([["sb","#fator","1"],["a","#aviso"],["r",".naoseaplica-tipo"],["a","#dias_aviso"]]);
                        break;
                    case "101":
                        acoesObjetos([["st","#fator","1"],["a","#aviso"],["r",".naoseaplica-tipo"],["a","#dias_aviso"]]);
                        break;
                }
            };
            
            resetaOpcoes = function(){
                $("select").removeClass("hidden").removeAttr("disabled");
                $(".naoseaplica-tipo").addClass("hidden");
                $("#dias_aviso").removeClass("hidden");
            };
            
            calculaDataAviso = function(){
                /**
                * RECUPERANDO DATA DE ENTRADA E DIAS DE AVISO
                */
                var dataEntrada = $("input[name='data_aviso']").val(); /**24/03/2016**/
                //VERIFICA SE ESTÁ VAZIO
                if(dataEntrada !== ""){
                    var dias = $("input[name='dias_aviso']").val();
                    if(dias===""){
                        dias = 0;
                    }

                    /**
                     * EXPLODE DE DATA DE ENTRADA
                     */
                    var explode = dataEntrada.split("/");

                    /**
                     * PREENCHENDO VARIAVEIS
                     */
                    var dia = parseInt(explode[0]);
                    var mes = parseInt(explode[1]);
                    //var mes = parseInt(explode[1]) - 1;
                    var ano = parseInt(explode[2]);

                    /**
                     * OBJETO
                     */
                    var data = new Date(ano, mes, dia);
                    data.setDate(data.getDate() + parseInt(dias));

                    var novaData = str_pad(data.getDate(), 2, '0', 'STR_PAD_LEFT') + "/" + str_pad(data.getMonth(), 2, '0', 'STR_PAD_LEFT') + "/" + data.getFullYear();

                    $("#data_demi").val(novaData);
                }
            };
        </script>

    </body>
</html>
