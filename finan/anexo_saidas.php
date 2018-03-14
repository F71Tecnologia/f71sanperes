<?php
session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

include("../conn.php");
include("../wfunction.php");
include("../classes/BotoesClass.php");
include("../classes/EntradaClass.php");
include("../classes/SaidaClass.php");
include("../classes/BancoClass.php");
include("../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$saidaStatusR = (!empty($_REQUEST['saida_status'])) ? $_REQUEST['saida_status'] : '';
$descricaoR = (!empty($_REQUEST['descricao'])) ? $_REQUEST['descricao'] : '';
$projetoR = (!empty($_REQUEST['id_projeto'])) ? $_REQUEST['id_projeto'] : '';
$projetoR = (!empty($_REQUEST['id_projeto'])) ? $_REQUEST['id_projeto'] : '';
$bancoR = (!empty($_REQUEST['id_banco'])) ? $_REQUEST['id_banco'] : '';
$mesR = (!empty($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$anoR = (!empty($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');

$entrada = new Entrada();
$saida = new Saida();
$global = new GlobalClass();
$banco = new Banco();

$optStauts = array('1'=>'Saídas Não Pagas', '2'=>'Saídas Pagas');

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Anexo de Saídas");
//$breadcrumb_pages = array("Principal" => "index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Anexo de Saídas</title>
        <link href="../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->        
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>

        <div class="container">
<!--            <input type="hidden" id="bancoSel" value="<?=$bancoR?>">-->
            <div class="col-md-12">
                <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Anexo de Saídas</small></h2></div>
                
                <!--resposta de algum metodo realizado-->
                <form action="" method="post" id="formAnexo" class="form-horizontal top-margin1" enctype="multipart/form-data" autocomplete="off">
                    <div class="panel panel-default">
                        <!--<div class="panel-heading text-bold">Anexo Saidas</div>-->
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="banco" class="col-md-2 control-label">Banco</label>
                                <div class="col-md-9">
                                    <?=montaSelect($global->carregaBancosByMaster($usuario['id_master']), $bancoR, 'id="id_banco" name="id_banco" class="validate[required,custom[select]] form-control"'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="banco" class="col-md-2 control-label">Status</label>
                                <div class="col-md-4">
                                    <?=montaSelect($optStauts, $saidaStatusR, 'id="saida_status" name="saida_status" class="validate[required,custom[select]] form-control"'); ?>
                                </div>
                                <label for="select" class="col-md-1 control-label text-sm">Competência</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <?=montaSelect(mesesArray(null,$key='-1',$opcao='« Selecione »'),$mesR, "id='mes' name='mes' class='validate[required,custom[select]] form-control'")?>
                                        <span class="input-group-addon">/</span>
                                        <?=montaSelect(AnosArray(2010,null),$anoR, "id='ano' name='ano' class='validate[required,custom[select]] form-control'")?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-md-2 control-label">Descrição</label>
                                <div class="col-md-9">
                                    <input type="text" id="descricao" name="descricao" class="form-control" value="<?=$descricaoR?>">
                                </div>
                            </div>
                            <?php if($_COOKIE['logado'] == 257){ ?>
                            <!--div class="form-group">
                                <label for="banco" class="col-md-2 control-label">Tipo Pagamento</label>
                                <div class="col-md-9">
                                    <?=montaSelect($optStauts, $saidaStatusR, 'id="saida_status" name="saida_status" class="validate[required,custom[select]] form-control"'); ?>
                                </div>
                            </div-->
                            <?php } ?>
                        </div>
                        <div class="panel-footer text-right">
                            <button type="submit" class="btn btn-warning" name="filtrar" value="filtrar"><i class="fa fa-filter"></i> Filtrar</button>
                        </div>
                    </div>
                    <?php if(isset($_REQUEST['filtrar'])){ ?>
                    <div class="panel panel-default anexo panelSaidas">
                        <div class="panel-body">
                            <?php
                            $qrySaida = $saida->getSaidasBanco($bancoR, $saidaStatusR, $mesR, $anoR, $descricaoR);
                            if(mysql_num_rows($qrySaida) > 0){ ?>
                            <table class="table table-bordered table-condensed table-striped valign-middle text-sm">
                                 <tr>
                                    <td class="text-center"><input type="checkbox" id="check_all"></td>
                                    <td class="text-center">Selecionar Todos</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                 </tr>
                                <?php while($rowSaida = mysql_fetch_assoc($qrySaida)){ ?>
                                <tr>
                                    <td class="text-center"><input type="checkbox" class="check" name="saidas[]" value="<?=$rowSaida['id_saida']?>"></td>
                                    <td class="text-center"><?=$rowSaida['id_saida']?></td>
                                    <td><?=$rowSaida['nome']?></td>
                                    <td class="text-center"><?=$rowSaida['n_documento']?></td>
                                    <td class="text-center"><?=number_format(str_replace(',','.',$rowSaida['valor'])+str_replace(',','.',$rowSaida['adicional']), 2, ',', '.')?></td>
                                    <td class="text-center"><?=$rowSaida['saida_vencimento']?></td>
                                </tr>
                                <?php } ?>
                            </table>
                            <?php } ?>
                        </div>
                        <div class="panel-footer text-right">
                            <button type="button" class="btn btn-primary next_back">Próximo <i class="fa fa-angle-double-right"></i></button>
                        </div>
                    </div>
                    <div class="panel panel-default anexo panelAnexo" style="display: none;">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12 margin_b20">
                                    <div class="col-sm-5 no-padding">
                                        <div class="input-group">
                                            <label class="input-group-addon pointer" for="radio_anexo"><input type="radio" id="radio_anexo" value="1" name="tipo_anexo" checked="true" /></label>
                                            <label class="form-control pointer" for="radio_anexo">Anexo</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-offset-2 col-sm-5 no-padding">
                                        <div class="input-group">
                                            <label class="input-group-addon pointer" for="radio_comprovante"><input type="radio" id="radio_comprovante" value="2" name="tipo_anexo" /></label>
                                            <label class="form-control pointer" for="radio_comprovante">Comprovante de Pagamento</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 margin_b20">
                                    <!--<div class="col-sm-5 no-padding">-->
                                        <input type="text" class="form-control" name="cod_barras" id="cod_barras" placeholder="Codigo de Barras">
                                    <!--</div>-->
                                </div>
                            </div>
                            <div id="dropzone" class="dropzone"></div>
                        </div>
                        <div class="panel-footer">
                            <div class="col-md-6 no-padding text-left">
                                <button type="button" class="btn btn-default next_back"><i class="fa fa-reply-all"></i> Voltar</button>
                            </div>
                            <div class="col-md-6 no-padding text-right">
                                <button type="button" class="btn btn-primary botaoAnexar"><i class="fa fa-paperclip"></i> Anexar</button>
                            </div>
                            <div class="clear" id="aaa"></div>
                        </div>
                    </div>
                    <?php } ?>
                </form>
            </div>
            <div class="clear"></div>
            <?php include("../template/footer.php"); ?>
        </div>
        
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/dropzone/dropzone.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.maskMoney.js"></script>
        <script src="../resources/js/financeiro/index.js"></script>
        <script>
            $(function() { 
                
                $('input').keypress(function (e) {
                    var code = null;
                    code = (e.keyCode ? e.keyCode : e.which);                
                    return (code == 13) ? false : true;
                });
   
                $("#cod_barras").mask("99999999999 9 99999999999 9 99999999999 9 99999999999 9");
                
                $("#check_all").on('click', function(){

                    if($(this).is(":checked") == true){
                        //marca tudo
                        $('.check').prop('checked', true);
                    }else{
                        //desmarca tudo
                        $('.check').prop('checked', false);
                    } ;                   

                });
                
                $('#formAnexo').validationEngine();
                
                $("body").on('click', '.next_back', function(){
                    $('.anexo').slideToggle();
                });
                
                Dropzone.autoDiscover = false;
                var myDropzone = new Dropzone("#dropzone",{
                    url: "actions/action.saida.php",
                    addRemoveLinks : true,
                    maxFilesize: 80,
                    
                    autoQueue: false,
                    
                    dictResponseError: "Erro no servidor!",
                    dictCancelUpload: "Cancelar",
                    dictFileTooBig: "Tamanho máximo: 10MB",
                    dictRemoveFile: "Remover Arquivo",
                    canceled: "Arquivo Cancelado",
                    acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF'
//                    , success: function(file, responseText){
//                        console.log(responseText);
//                        $("#aaa").append(responseText);
//                        $('.close').trigger('click');
//                    }
                });
                
                $("body").on('click', '.botaoAnexar', function(){
                    cria_carregando_modal();
                    var tipo_anexo;
                    if($("#radio_anexo").prop('checked')){
                        tipo_anexo = $("#radio_anexo").val();
                    } else if($("#radio_comprovante").prop('checked')){
                        tipo_anexo = $("#radio_comprovante").val();
                    }
                    var cod_barras = $('#cod_barras').val();
                    var dados = $('.check').serializeArray();
                    var array = new Array();
                    $.each(dados, function(i, field){
                        array.push(field.value);
                    });
                    
//                    cria_carregando_modal();
                    if(array.length > 0){
                        myDropzone.on('sending',function(file, xhr, formData) {
                            formData.append('id_saida', array); // Append all the additional input data of your form here!
                            formData.append('tipo_anexo', tipo_anexo); // Append all the additional input data of your form here!
                            formData.append('cod_barras', cod_barras); // Append all the additional input data of your form here!
                            formData.append("action", 'upload_anexo'); // Append all the additional input data of your form here!
                        });

                        myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));
                        
                        myDropzone.on("queuecomplete", function(progress) {
                            remove_carregando_modal();
                            bootDialog(
                                'Comprovante Anexado com Sucesso!', 
                                ' Anexar Comprovante!', 
                                [{
                                    label: 'Fechar',
                                    action: function(){
                                        window.location.href = "../finan";
                                    }
                                }], 
                                'success'
                            );
                        });

//                        remove_carregando_modal();
                    } else {
                        bootAlert('Selecione pelo menos 1 saída','Alerta',null,'warning');
                    }
                });
                
//                $('#projeto').trigger('change');
                
            });
        </script>
    </body>
</html>